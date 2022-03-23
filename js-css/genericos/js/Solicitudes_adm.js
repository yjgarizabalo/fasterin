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
var id_tipo3_sele = 0;
var id_responsable_sele_tipo3 = 0;
var no_borrar_personas = 0;
var borrador_solicitud = [];
var destinos_gu = 1;
var mas_destino = 0;
var limite_dias = 3;
var tipo_evento_gen = "";
var id_modificando_t4 = 0;
var con_requ_modi = 0;
var asignando_t4 = 0;
var asignando_t3 = 0;
var modificando_tipo3 = 0;
var tipo_asignacion = -1;
var iadjunto = 1;
var modificando = 0;
var IsInte = false;
var nombre_evento = "";
var solicitante = "";
var correo_solicitante = "";
var id_sol_bus_sele = 0;
var desde_responsables = 0;
var id_responsable_sele = 0;
var id_tiquete_pesona_sele = 0;
var tipo_solicitud_sele = "";
var mensaje = "";
var personas_sele = [];
var codigo_sap_persona_sele = [];
var personas_seleccionadas = [];
var idpersona_seleccionada_re = 0;
var nombre_completo = "";
var proceso = "inicial";
var id_solicitud = 0;
var server = "localhost";
var tipo_gen = "";
var datos_solicitud = 0;
var id_solicitud_borrador = 0;
var datos_reserva = 0;
var datos_evento = 0;
var tipo_sol_modi = "";
var id_general_tipo3 = "";
var tipo_reserva_Adm_modi = "";
var con_requ = 0;
var modificando_tipo4 = 0;
var id_persona_solici_tabla = 0;
var ruta_personas = "archivos_adjuntos/solicitudesADM/archivos_personas/";
var ruta_reembolso = "archivos_adjuntos/solicitudesADM/reembolso/";
var ruta_polizas = "archivos_adjuntos/solicitudesADM/polizas/";
var ruta_matriculas = "archivos_adjuntos/solicitudesADM/matriculas/";
var ruta_logistica = "archivos_adjuntos/solicitudesADM/logistica/";
let url = `${Traer_Server()}index.php/comunicaciones_control/`;
let id_solicitud_com = null;

$(document).ready(function () {

	$("#ver_adjuntos_lista").click(function () {
		listar_archivos_adjuntos(id_solicitud_com, 2);
		$("#modal_listar_archivos_adjuntos").modal("show");
	});

	server = Traer_Server();
	Configurar_form_Reserva_Adm("");
	$("#detalle_persona_solicita").click(function () {

		obtener_datos_persona_id_completo(id_persona_solici_tabla, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});
	
	$("#Guardar_solicitud_general select[name='tipo_calificacion']").click(function () {
		let clasificacion=$("#Guardar_solicitud_general select[name='tipo_calificacion']").val();
			let so = `${Traer_Server()}index.php/solicitudes_adm_control/`;
		consulta_ajax(`${so}cargar_select`,{clasificacion}, (data) => {
			$('.cbxtipo').html('<option value="">Seleccione Tipo de Viaje</option>');
			data.forEach(({ nombre_tipo, id_tipo }) =>
				$('.cbxtipo').append(`<option value="${id_tipo}">${nombre_tipo}</option>`)
			);
		});
	});

	$(".thumbnail").hover(
		function () {
			$(this).find("p:last").fadeIn(500);
		},
		function () {
			$(this).find("p:last").fadeOut(0);
		}
	);
	$("#limpiar_filtros_solicitud").click(function () {

		sin_filtros();
	});
	$("#asignar_mas_responsables").click(function () {

		vaidar_estado_Actual_solicitud(id_solicitud, 4, id_sol_bus_sele, 0)
	});
	$("#asignar_mas_responsables_capa").click(function () {

		vaidar_estado_Actual_solicitud(id_solicitud, 11, id_tipo3_sele, 0)
	});
	$("#filtrar_solicitud_fecha").change(function () {
		if ($(this).is(':checked')) {
			$("#div_fecha_inicio_filtro").show("slow");
			$("#div_fecha_inicio_filtro input").val("");
		} else {
			$("#div_fecha_inicio_filtro").hide("slow");
			$("#div_fecha_inicio_filtro input").val("");
		}
	});
	$("#generar_reporte").click(function () {
		Listar_solciitudes();
	})
	$("#filtrar_datos_solicitud").click(function () {

		$("#Modal_filtrar_solcicitud").modal("show");
	});
	$("#solt1").click(function () {
		var titulo = $("#solt1 span").html();
		configurar_form_solicitud("SolT1", titulo);
	});
	$("#solt2").click(function () {
		var titulo = $("#solt2 span").html();
		configurar_form_solicitud("SolT2", titulo);
	});
	$("#solt3").click(function () {
		var titulo = $("#solt3 span").html();
		configurar_form_solicitud("SolT3", titulo);
	});
	$("#solt4").click(function () {
		var titulo = $("#solt4 span").html();
		configurar_form_solicitud("SolT4", titulo);
	});
	$("#listado").click(function () {
		$("#menu_principal").css("display", "none");
		$(".listado_solciitudes").fadeIn(1000);


	});

	$(".cerrar-reque").click(function () {
		$(".fixed").hide("fast");
	});
	$(".mostrar-reque").click(function () {
		$(".fixed").show("fast");
	});

	$(".mostrar-reque-modi").click(function () {
		$(".fixed_eventos_modi").show("fast");
	});

	$(".mostrar-reque-viaticos").click(function () {
		$(".fixed_viaticos").show("fast");
	});

	$("#re_manteles").click(function () {
		if ($("#re_manteles").is(':checked')) {
			$(".inp_manteles").show("fast");
			$(".inp_manteles").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_manteles").css("display", "none");
			$(".inp_manteles").val('');
			$(".inp_manteles").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_flores").click(function () {
		if ($("#re_flores").is(':checked')) {
			$(".inp_flores").show("fast");
			$(".inp_flores").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_flores").css("display", "none");
			$(".inp_flores").val('');
			$(".inp_flores").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_sillas").click(function () {
		if ($("#re_sillas").is(':checked')) {
			$(".inp_sillas").show("fast");
			$(".inp_sillas").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_sillas").css("display", "none");
			$(".inp_sillas").val('');
			$(".inp_sillas").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_carpas").click(function () {
		if ($("#re_carpas").is(':checked')) {
			$(".inp_carpas").show("fast");
			$(".inp_carpas").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_carpas").css("display", "none");
			$(".inp_carpas").val('');
			$(".inp_carpas").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_vasos").click(function () {
		if ($("#re_vasos").is(':checked')) {
			$(".inp_vasos").show("fast");
			$(".inp_vasos").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_vasos").css("display", "none");
			$(".inp_vasos").val('');
			$(".inp_vasos").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_refri").change(function () {
		if ($(this).is(':checked')) {
			$(".inp_refrigerios").show("fast");
			$(".inp_refrigerios").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_refrigerios").css("display", "none");
			$(".inp_refrigerios").val('');
			$(".inp_refrigerios").removeAttr("required", "true");
			con_requ--;
		}
	});

	$("#re_agua").change(function () {
		if ($(this).is(':checked')) {
			$(".inp_cafe_agua").show("fast");
			$(".inp_cafe_agua").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_cafe_agua").css("display", "none");
			$(".inp_cafe_agua").val('');
			$(".inp_cafe_agua").removeAttr("required", "true");
			con_requ--;
		}
	});

	$("#cbx_categorias_tipo4").change(function () {
		var tipo = $(this).val();
		Config_fort4_tipo(tipo);
		$("#cbx_categorias_tipo4").val(tipo);
	});
	$("#cbx_categorias_tipo4_modi").change(function () {
		var tipo = $(this).val();
		Config_fort4_tipo_modi(tipo);
		Configurando_modificar_bodega();
		$("#cbx_categorias_tipo4_modi").val(tipo);
	});
	$("#re_tenedores").click(function () {
		if ($("#re_tenedores").is(':checked')) {
			$(".inp_tenedores").show("fast");
			$(".inp_tenedores").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_tenedores").css("display", "none");
			$(".inp_tenedores").val('');
			$(".inp_tenedores").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_mesas").click(function () {
		if ($("#re_mesas").is(':checked')) {
			$(".inp_mesas").show("fast");
			$(".inp_mesas").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_mesas").css("display", "none");
			$(".inp_mesas").val('');
			$(".inp_mesas").removeAttr("required", "true");
			con_requ--;
		}
	});

	$("#re_cucharas").click(function () {
		if ($("#re_cucharas").is(':checked')) {
			$(".inp_cucharas").show("fast");
			$(".inp_cucharas").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_cucharas").css("display", "none");
			$(".inp_cucharas").val('');
			$(".inp_cucharas").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_platos").click(function () {
		if ($("#re_platos").is(':checked')) {
			$(".inp_platos").show("fast");
			$(".inp_platos").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_platos").css("display", "none");
			$(".inp_platos").val('');
			$(".inp_platos").removeAttr("required", "true");
			con_requ--;
		}
	});
	$("#re_cuchillos").click(function () {
		if ($("#re_cuchillos").is(':checked')) {
			$(".inp_cuchillos").show("fast");
			$(".inp_cuchillos").attr("required", "true");
			con_requ++;
		} else {
			$(".inp_cuchillos").css("display", "none");
			$(".inp_cuchillos").val('');
			$(".inp_cuchillos").removeAttr("required", "true");
			con_requ--;
		}
	});
	//-----------------------------------------------------------
	$("#re_manteles_modi").click(function () {
		if ($("#re_manteles_modi").is(':checked')) {
			$(".inp_manteles_modi").show("fast");
			$(".inp_manteles_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_manteles_modi").css("display", "none");
			$(".inp_manteles_modi").val('');
			$(".inp_manteles_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_sillas_modi").click(function () {
		if ($("#re_sillas_modi").is(':checked')) {
			$(".inp_sillas_modi").show("fast");
			$(".inp_sillas_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_sillas_modi").css("display", "none");
			$(".inp_sillas_modi").val('');
			$(".inp_sillas_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_carpas_modi").click(function () {
		if ($("#re_carpas_modi").is(':checked')) {
			$(".inp_carpas_modi").show("fast");
			$(".inp_carpas_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_carpas_modi").css("display", "none");
			$(".inp_carpas_modi").val('');
			$(".inp_carpas_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_vasos_modi").click(function () {
		if ($("#re_vasos_modi").is(':checked')) {
			$(".inp_vasos_modi").show("fast");
			$(".inp_vasos_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_vasos_modi").css("display", "none");
			$(".inp_vasos_modi").val('');
			$(".inp_vasos_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});

	$("#re_tenedores_modi").click(function () {
		if ($("#re_tenedores_modi").is(':checked')) {
			$(".inp_tenedores_modi").show("fast");
			$(".inp_tenedores_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_tenedores_modi").css("display", "none");
			$(".inp_tenedores_modi").val('');
			$(".inp_tenedores_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_mesas_modi").click(function () {
		if ($("#re_mesas_modi").is(':checked')) {
			$(".inp_mesas_modi").show("fast");
			$(".inp_mesas_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_mesas_modi").css("display", "none");
			$(".inp_mesas_modi").val('');
			$(".inp_mesas_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});

	$("#re_cucharas_modi").click(function () {
		if ($("#re_cucharas_modi").is(':checked')) {
			$(".inp_cucharas_modi").show("fast");
			$(".inp_cucharas_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_cucharas_modi").css("display", "none");
			$(".inp_cucharas_modi").val('');
			$(".inp_cucharas_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_platos_modi").click(function () {
		if ($("#re_platos_modi").is(':checked')) {
			$(".inp_platos_modi").show("fast");
			$(".inp_platos_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_platos_modi").css("display", "none");
			$(".inp_platos_modi").val('');
			$(".inp_platos_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	$("#re_cuchillos_modi").click(function () {
		if ($("#re_cuchillos_modi").is(':checked')) {
			$(".inp_cuchillos_modi").show("fast");
			$(".inp_cuchillos_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_cuchillos_modi").css("display", "none");
			$(".inp_cuchillos_modi").val('');
			$(".inp_cuchillos_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});

	$("#re_flores_modi").click(function () {
		if ($("#re_flores_modi").is(':checked')) {
			$(".inp_flores_modi").show("fast");
			$(".inp_flores_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_flores_modi").css("display", "none");
			$(".inp_flores_modi").val('');
			$(".inp_flores_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});

	$("#re_refri_modi").change(function () {
		if ($("#re_refri_modi").is(':checked')) {
			$(".inp_refrigerios_modi").show("fast");
			$(".inp_refrigerios_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_refrigerios_modi").css("display", "none");
			$(".inp_refrigerios_modi").val('');
			$(".inp_refrigerios_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});

	$("#re_agua_modi").change(function () {
		if ($("#re_agua_modi").is(':checked')) {
			$(".inp_cafe_agua_modi").show("fast");
			$(".inp_cafe_agua_modi").attr("required", "true");
			con_requ_modi++;
		} else {
			$(".inp_cafe_agua_modi").css("display", "none");
			$(".inp_cafe_agua_modi").val('');
			$(".inp_cafe_agua_modi").removeAttr("required", "true");
			con_requ_modi--;
		}
	});
	//------------------------------------------------------------
	$("#tipo-polizas").change(function () {
		var valor = $(this).val();
		if (valor == "Poli_add") {
			var titulo = ["Adjuntar Polizas Anteriores", "Adjuntar Contratos"];
			CrearAdjuntar(2, titulo);
		} else {
			var titulo = ["Adjuntar Contratos"];
			CrearAdjuntar(1, titulo);
		}
	});
	$("#tipo-polizas_modi").change(function () {
		var valor = $(this).val();
		if (valor == "Poli_add") {
			var titulo = ["Adjuntar Polizas Anteriores", "Adjuntar Contratos"];
			CrearAdjuntar_modi(2, titulo);
		} else {
			var titulo = ["Adjuntar Contratos"];
			CrearAdjuntar_modi(1, titulo);
		}
	});
	$("#btnmodificar_tipo3").click(function () {
		vaidar_estado_Actual_solicitud(id_solicitud, 8, 0, 0);

	});
	$("#btnmodificar_tipo4").click(function () {
		vaidar_estado_Actual_solicitud(id_solicitud, 9, 0, 0);

	});
	$("#minimizar-alerta-mensaje").click(function () {
		$(".div_mensaje_sol").hide("slow");
	});
	$(".minimizar-alerta-mensaje-tiqu").click(function () {
		$(".div_mensaje_tiquetes").hide("slow");
	});
	$(".minimizar-alerta-mensaje-reserva").click(function () {

		$(".div_mensaje_reserv").hide("slow");
	});
	$("#re_viaticos_resereva").change(function () {
		var valor = $(this).val();
		if (valor == "Tiquetes" || valor == "Viaticos y tiquetes") {
			$("#requiere_tiquetes_Reserva").show("slow");
			$("#requiere_tiquetes_Reserva input").attr("required", "true");
		} else {
			$("#requiere_tiquetes_Reserva").hide("slow");
			$("#requiere_tiquetes_Reserva input").removeAttr("required", "true");
		}
	});
	$("#re_viaticos_resereva_modi").change(function () {
		var valor = $(this).val();
		if (valor == "Tiquetes" || valor == "Viaticos y tiquetes") {
			$("#requiere_tiquetes_Reserva_modi").show("slow");
			$("#requiere_tiquetes_Reserva_modi input").attr("required", "true");
		} else {
			$("#requiere_tiquetes_Reserva_modi").hide("slow");
			$("#requiere_tiquetes_Reserva_modi input").removeAttr("required", "true");
		}
	});
	$("#cbx_tipo_evento").change(function () {
		tipo_evento_gen = $(this).val();

	});

	$(".cerrar_mensaje_sol").click(function () {
		$(".div_mensaje_sol").hide("slow");
	});
	$("#buscar_persona_sele").click(function () {
		tipo_asignacion = 2;
		$("#Modal-selec-personas-gen").modal("show");

	});
	$("#buscar_persona_sele_capa").click(function () {
		tipo_asignacion = 5;
		$("#Modal-selec-personas-gen").modal("show");

	});
	$("#sele_perso_tiquetes").click(function () {
		tipo_asignacion = 1;
		$("#Modal-selec-personas-gen").modal("show");


	});
	$("#persona_solicita_tiquetes").click(function () {
		tipo_asignacion = 1;
		$("#Modal-selec-personas-gen").modal("show");


	});
	$(".sele_perso_bodega").click(function () {
		Reiniciar_Tabla();
		tipo_asignacion = 4;
		modificando_tipo4 = 0;
		$("#Modal-selec-personas-gen").modal("show");


	});
	$(".sele_perso_bodega_modi").click(function () {
		Reiniciar_Tabla();
		tipo_asignacion = 4;
		modificando_tipo4 = 1;
		$("#Modal-selec-personas-gen").modal("show");


	});

	$("#continuar-iti").click(function () {
		$("#panel-requerimientos").hide("slow");
		$("#panel-itinerario-datos").show("slow");
	});
	$("#continuar-iti-modi").click(function () {
		$("#panel-requerimientos-modi").hide("slow");
		$("#panel-itinerario-datos-modi").show("slow");
	});

	$("#continuar-info-bodega").click(function () {
		if (con_requ == 0) {
			MensajeConClase("Para continuar seleccione de la tabla requerimientos lo que necesita.!!", "info", "Oops");
		} else {
			$("#panel-requerimientos-bodega").hide("slow");
			$("#info-form-bodega").show("slow");
		}
	});
	$("#regresar-info-bodega").click(function () {

		$("#panel-requerimientos-bodega").show("slow");
		$("#info-form-bodega").hide("slow");

	});

	$("#continuar-info-bodega_modi").click(function () {
		if (con_requ_modi == 0) {
			MensajeConClase("Para continuar seleccione de la tabla requerimientos lo que necesita.!!", "info", "Oops");
		} else {
			$("#panel-requerimientos-bodega-modi").hide("slow");
			$("#info-form-bodega_modi").show("slow");
		}
	});
	$("#regresar-info-bodega_modi").click(function () {

		$("#panel-requerimientos-bodega-modi").show("slow");
		$("#info-form-bodega_modi").hide("slow");

	});
	$("#regresar-itinerario-requ").click(function () {
		$("#panel-requerimientos").show("slow");
		$("#panel-itinerario-datos").hide("slow");
	});
	$("#regresar-itinerario-requ-modi").click(function () {
		$("#panel-requerimientos-modi").show("slow");
		$("#panel-itinerario-datos-modi").hide("slow");
	});
	$("#cargar_archivo").submit(function () {
		holaaaa();
		return false;
	});
	$("#Guardar_solicitud_tipo3").submit(function () {
		var tipo = $("#tipo_reserva_Adm").val();
		if (tipo == "SolT3_remb" || tipo == "SolT3_matr") {
			var value_input = $("#Guardar_solicitud_tipo3 input[name*='archivo0']").val();
			if (value_input.length == 0) {
				MensajeConClase("Antes de Continuar debe Adjuntar los archivos", "info", "Oops...");
				return false;
			}
			if (tipo == "SolT3_matr") {
				var value_input = $("#Guardar_solicitud_tipo3 input[name*='archivo1']").val();
				if (value_input.length == 0) {
					MensajeConClase("Antes de Continuar debe Adjuntar los archivos", "info", "Oops...");
					return false;
				}
			}

		}
		if (tipo == "SolT3_poli") {
			if ($("#tipo-polizas").val() == "Poli_nue") {
				let value_input = $("#Guardar_solicitud_tipo3 input[name*='archivo0']").val();
				if (value_input.length == 0) {
					MensajeConClase("Antes de Continuar debe Adjuntar los contratos.", "info", "Oops...");
					return false;
				}
			} else {
				let value_input = $("#Guardar_solicitud_tipo3 input[name*='archivo0']").val();
				let value_input2 = $("#Guardar_solicitud_tipo3 input[name*='archivo1']").val();
				if (value_input.length == 0 || value_input2.length == 0) {
					MensajeConClase("Antes de Continuar debe Adjuntar las polizas anteriores y los contratos.", "info", "Oops...");
					return false;
				}
			}
		}
		Guardar_Solicicitudes_tipo3();
		return false;
	});
	$("#Guardar_bodega").submit(function () {
		Guardar_Solicicitudes_tipo4();
		return false;
	});
	$("#Modificar_bodega").submit(function () {
		Modificar_Solicicitudes_tipo4();
		return false;
	});
	iniciar_tabla_persona();

	$("#tipo_reserva_Adm").change(function () {
		var valor = $(this).val();
		Configurar_form_Reserva_Adm(valor);
	})
	$("#check_inscr").click(function () {
		if ($("#check_inscr").is(':checked')) {


			$("#requiere_inscrip").show("slow");
			$("#requiere_inscrip .requerido").attr("required", "true");
		} else {
			$("#requiere_inscrip").hide("slow");
			$("#requiere_inscrip .CampoGeneral").val('');
			$("#requiere_inscrip .requerido").removeAttr("required", "true");


		}
	});
	$("#check_inscr_modi").click(function () {
		if ($("#check_inscr_modi").is(':checked')) {


			$("#requiere_inscrip_modi").show("slow");
			$("#requiere_inscrip_modi .requerido").attr("required", "true");
		} else {
			$("#requiere_inscrip_modi").hide("slow");
			$("#requiere_inscrip_modi .CampoGeneral").val('');
			$("#requiere_inscrip_modi .requerido").removeAttr("required", "true");


		}
	});

	$("#btnmodificar_solciitud").click(function () {
		if (id_solicitud == 0) MensajeConClase("Antes de Continuar debe seleccionar la solicitud", "info", "Oops...")
		else if (tipo_gen == 'Even_Com') MensajeConClase('Este tipo de solicitud solo puede ser modificada por el modulo de comunicaciones', 'info', 'Oops.!');
		else vaidar_estado_Actual_solicitud(id_solicitud, 5, 0, 0)
	});

	$("#buscar_sele_perso").click(function () {
		var datos = $("#input_persona_reserva").val().trim();
		if (datos.length == 0) {
			MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...")
		} else {
			listar_Personas_solicitudes(datos);
		}
	});
	$("#input_persona_reserva").keypress(function (e) {
		if (e.which == 13) {
			var datos = $("#input_persona_reserva").val().trim();
			if (datos.length == 0) {
				MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...")
			} else {
				listar_Personas_solicitudes(datos);
			}
		}
	});


	$("#Retirar_persona_sele").click(function () {
		var s = $("#personal_asignado-combo").val();
		if (s.length == 0) {
			MensajeConClase("Seleccione Persona a Retirar..!", "info", "Oops...")
		} else {
			Retirar_Persona_Listado_selecc(s);
		}

	});
	$("#Retirar_persona_sele_tique").click(function () {
		var s = $("#personal_asignado-combo-par").val();
		if (s.length == 0) {
			MensajeConClase("Seleccione Persona a Retirar..!", "info", "Oops...")
		} else {
			Retirar_Persona_Listado_selecc(s);
		}

	});

	$("#Retirar_persona_sele_capa").click(function () {
		var s = $("#persona_responsable_capa").val();
		if (s.length == 0) {
			MensajeConClase("Seleccione Persona a Retirar..!", "info", "Oops...")
		} else {
			Retirar_Persona_Listado_selecc(s);
		}

	});

	$("#agregar_nueva_persona").click(function () {

		$("#Registrar-persona").modal("show");

	});
	$("#Guardar_Itinerario").submit(function () {
		Guardar_Solicicitudes_tipo1();
		return false;
	});
	$("#Guardar_trasnporte").submit(function () {
		Guardar_Solicicitudes_tipo2("Guardar_trasnporte");
		return false;
	});
	$("#modificar_trasnporte").submit(function () {
		confirmar_modificar(2, "Esta seguro que desea modificar los datos del transporte, si desea actualizar los datos debe presionar la opcion 'Si, Modificar'.!!");

		return false;
	});
	$("#modificar_itinerario").submit(function () {
		confirmar_modificar(3, "Esta seguro que desea modificar los datos del Itinerario, si desea actualizar los datos debe presionar la opcion 'Si, Modificar'.!!");
		return false;
	});
	$("#form-ingresar-persona-identidades").submit(function () {
		registrarPersona_identidades(1);
		return false;
	});

	$("#departamento_sele_guardar").change(function () {
		var valory = $(this).val().trim();
		Listar_cargos_departamento_combo(".cbxcargos", "Seleccione Cargo", valory, 0);
	});
	$("#btnAgregar_solicitud").click(function () {
		modificando = 0;
		$(".listado_solciitudes").css("display", "none");
		$("#menu_principal").fadeIn(1000);


	});

	$(".btn_asignar_detalle").click(function () {

		if (id_solicitud == 0) {
			MensajeConClase("Antes de Continuar debe seleccionar la solicitud", "info", "Oops...")
		} else {
			MostrarInformacionevento(tipo_gen);
			if (tipo_gen == "SolT1" || tipo_gen == "SolT2" || tipo_gen == "SolT4" || tipo_gen == "SolT3") {
				vaidar_estado_Actual_solicitud(id_solicitud, 1, 0);
				return;
			} else {

				MensajeConClase("Para el Tipo de solicitud seleccionada no esta disponible esta opcion.", "info", "Oops...")
				return;
			}


		}
	});


	$("#Recargar").click(function () {

		location.reload();
	});

	$("#re_tiquete").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_tiquetes").show("slow");
			$("#requiere_tiquetes input").attr("required", "true");
			if (tipo_evento_gen == "Even_Nac") {
				$(".ocultar_adjunto").hide("fast");
			} else {
				$(".ocultar_adjunto").show("fast");
			}
		} else {
			$("#requiere_tiquetes").hide("slow");
			$("#fecha_retorno_tiqu").val('');
			$("#fecha_salida_tiqu").val('');
			$("#requiere_tiquetes input").removeAttr("required", "true");
			$(".ocultar_adjunto").hide("fast");
		}
	});


	$("#re_hotel").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_hoteles").show("slow");
			$("#requiere_hoteles input").attr("required", "true");
			$("#re_viaticos").prop("checked", false);
		} else {
			$("#requiere_hoteles").hide("slow");
			$("#fecha_salida_hotel").val('');
			$("#fecha_ingreso_hotel").val('');
			$("#requiere_hoteles input").removeAttr("required", "true");
		}
	});


	$("#re_mul_des").change(function () {
		if ($(this).is(':checked')) {
			multiples_destinos(1);
		} else {
			multiples_destinos(2);


		}
	});
	$("#regresar_multip").click(function () {
		$("#destinos").hide("slow");
		$("#datos_destino").show("slow");
		$("#guardar_fin_datos").hide("slow");
		$("#guardar_mas_datos").show("slow");
	});

	$("#guardar_mas_datos").click(function () {
		if (mas_destino == 1) {
			$("#nombre_itine p").html("Datos del Itinerario No." + destinos_gu);
			$("#guardar_fin_datos").show("slow");
			$("#guardar_mas_datos").hide("slow");
			$("#regresar_multip").show("slow");
			$("#datos_destino").hide("slow");
			$("#destinos").show("slow");
		} else {
			MensajeConClase("Debe seleccionar la opcion de multiples destino para continuar.", "info", "Oops...")
		}
	});
	$("#re_viaticos").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_hoteles").hide("slow");
			$("#fecha_salida_hotel").val('');
			$("#fecha_ingreso_hotel").val('');
			$("#requiere_hoteles input").removeAttr("required", "true");
			$("#re_hotel").prop("checked", false);
		}
	});
	$("#re_viaticos_modifica").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_hoteles_modifica").hide("slow");
			$("#fecha_salida_hotel_modifica").val('');
			$("#fecha_ingreso_hotel_modifica").val('');
			$("#requiere_hoteles_modifica input").removeAttr("required", "true");
			$("#re_hotel_modifica").prop("checked", false);
		}
	});
	$("#re_tiquete_modifica").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_tiquetes_modifica").show("slow");
			$("#fecha_retorno_tiqu_modifica").val('');
			$("#fecha_salida_tiqu_modifica").val('');
			$("#requiere_tiquetes_modifica input").attr("required", "true");
			if (tipo_evento_gen == "Even_Nac") {
				$(".ocultar_adjunto_modi").hide("fast");
			} else {
				$(".ocultar_adjunto_modi").show("fast");
			}
		} else {
			$("#requiere_tiquetes_modifica").hide("slow");
			$("#fecha_retorno_tiqu_modifica").val('');
			$("#fecha_salida_tiqu_modifica").val('');
			$("#requiere_tiquetes_modifica input").removeAttr("required", "true");
			$(".ocultar_adjunto_modi").hide("fast");

		}
	});
	$("#re_hotel_modifica").change(function () {
		if ($(this).is(':checked')) {
			$("#requiere_hoteles_modifica").show("slow");
			$("#fecha_ingreso_hotel_modifica").val('');
			$("#fecha_salida_hotel_modifica").val('');
			$("#requiere_hoteles_modifica input").attr("required", "true");
			$("#re_viaticos_modifica").prop("checked", false);
		} else {
			$("#requiere_hoteles_modifica").hide("slow");
			$("#fecha_ingreso_hotel_modifica").val('');
			$("#fecha_salida_hotel_modifica").val('');
			$("#requiere_hoteles_modifica input").removeAttr("required", "true");
		}
	});
	$("#cbx_tipo_solicitud").change(function () {
		tipo_gen = $(this).val();

		$("#fecha_inicio_evento").attr("required", "true");
		$("#fecha_final_evento").attr("required", "true");
		if (tipo_gen == "SolT1" || tipo_gen == "SolT2" || tipo_gen == "SolT4") {
			//$("#tit-sol").html("Datos Evento");
			$("#panel-add-reserva input").removeAttr("required", "true");
			$("#panel-add-reserva select").removeAttr("required", "true");
			$("#panel-add-reserva").hide("slow");
			$("#div_req_inscripcion").show("slow");
			$("#fecha_inicio_evento_div").show("slow");
			$("#fecha_final_evento_div").show("slow");
			$("#fecha_inicio_evento").val("");
			$("#fecha_final_evento").val("");
			if (tipo_gen == "SolT1") {
				$("#cbx_tipo_evento").val("");
				$("#cbx_tipo_evento").show("slow");
			} else {
				$("#cbx_tipo_evento").val("Even_Nac");
				tipo_evento_gen = "Even_Nac";
				$("#cbx_tipo_evento").hide("slow");
			}
		} else if (tipo_gen == "SolT3") {
			$("#tit-sol").html("Datos Solicitud");
			$("#panel-add-reserva .requerido").attr("required", "true");

			$("#cbx_tipo_evento").val("Even_Nac");
			tipo_evento_gen = "Even_Nac";
			$("#cbx_tipo_evento").hide("slow");
			$("#fecha_inicio_evento_div").hide("slow");
			$("#fecha_final_evento_div").hide("slow");
			$("#fecha_inicio_evento").val("");
			$("#fecha_final_evento").val("");
			$("#fecha_inicio_evento").removeAttr("required", "true");
			$("#fecha_final_evento").removeAttr("required", "true");
			$("#div_req_inscripcion").hide("slow");

			$("#check_inscr").prop("checked", false);
			$("#requiere_inscrip").hide("slow");
			$("#requiere_inscrip .CampoGeneral").val('');
			$("#requiere_inscrip .requerido").removeAttr("required", "true");
			$("#panel-add-reserva").show("slow");
			$("#columnas input").hide("fast");
			Configurar_form_Reserva_Adm("dff");
		}
		MostrarInformacionsolicitud(tipo_gen);

	});
	$("#Modificar_solicitud_tipo3").submit(function () {
		confirmar_modificar(4, "Esta seguro que desea modificar los datos de la solicitud, si desea actualizar los datos debe presionar la opcion 'Si, Modificar'.!!")
		return false;
	});
	$("#Guardar_solicitud_general").submit(function () {
		Guardar_Solicitud(0);
		return false;

	});
	$("#Modificar_solicitud_general").submit(function () {
		var xxx = "de la solicitud";
		//        if (tipo_sol_modi != "SolT3") {
		//            var ini = $("#fecha_inicio_evento_modi").val().trim();
		//            var fin = $("#fecha_final_evento_modi").val().trim();
		//
		//
		//            if (ini.length == 0 || fin.length == 0) {
		//                MensajeConClase("Seleccione Fecha de Inicio y final del Evento", "info", "Oops...")
		//                return false;
		//            }
		//        } else {
		//            xxx = "de la solicitud";
		//        }
		//
		confirmar_modificar(1, "Esta seguro que desea modificar los datos " + xxx + ", si desea actualizar los datos debe presionar la opcion 'Si, Modificar'.!!");
		return false;

	});

});

function MostrarInformacionsolicitud(tipo_gen) {
	for (var i = 0; i <= datos_solicitud.length - 1; i++) {
		if (datos_solicitud[i].id_aux == tipo_gen) {
			$(".mensaje_solic").html(datos_solicitud[i].valorx);
			$(".titulo-sol").html(datos_solicitud[i].valor + "!");
			$(".div_mensaje_sol").show("slow");

			return;
		}
	}
	$(".mensaje_solic").html("Para Iniciar Con el proceso de una Nueva solicitud debe seleccionar el tipo de solicitud a Guardar.");
	$(".div_mensaje_sol").hide("slow");
}

function MostrarInformacionsolicitud_Datos(tipo_gen) {
	for (var i = 0; i <= datos_solicitud.length - 1; i++) {
		if (datos_solicitud[i].id_aux == tipo_gen) {
			return datos_solicitud[i];
		}
	}
	return null;
}

function MostrarInformacionreserva(tipo_gen) {

	for (var i = 0; i <= datos_reserva.length - 1; i++) {
		if (datos_reserva[i].id_aux == tipo_gen) {
			$(".mensaje_reserv").html(datos_reserva[i].valorx);
			$(".titulo_reserv").html(datos_reserva[i].valor + "!");
			$(".div_mensaje_reserv").show("slow");

			return;
		}
	}
	$(".div_mensaje_reserv").hide("slow");
}

function MostrarInformacionevento(tipo_gen) {

	$(".nombre_evento_guardar").html(nombre_evento);
	return;
	for (var i = 0; i <= datos_evento.length - 1; i++) {
		if (datos_evento[i].id_aux == tipo_gen) {
			$(".mensaje_con_tiq").html(datos_evento[i].valorx);
			$(".div_mensaje_tiquetes").show("slow");
			return;
		}
	}
	$(".div_mensaje_tiquetes").hide("slow");
}

function Guardar_Solicitud(continuar) {
	//MensajeConClase("validando info", "add_inv", "Oops...");
	var formData = new FormData(document.getElementById("Guardar_solicitud_general"));
	formData.append("tipo_solicitud", tipo_gen);
	formData.append("cont", continuar);
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Guardar",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		console.log(datos);
		if (datos[0] == -1) {
			MensajeConClase("Seleccione Tipo Solicitud", "info", "Oops...");
			return true;
		} else if (datos[0] == -2) {
			MensajeConClase("Ingrese Nombre de la solicitud", "info", "Oops...");
			return true;
		} else if (datos[0] == -3) {
			MensajeConClase("Seleccione Fecha Inicio de la solicitud", "info", "Oops...");
			return true;
		} else if (datos[0] == -4) {
			MensajeConClase("Seleccione Fecha Final de la solicitud", "info", "Oops...");
			return true;
		} else if (datos[0] == -5) {
			MensajeConClase("Ingrese Lugar de Origen", "info", "Oops...");
			return true;
		} else if (datos[0] == -6) {
			MensajeConClase("Ingrese Lugar de destino", "info", "Oops...");
			return true;
		} else if (datos[0] == -7) {
			MensajeConClase("Seleccione si Necesita Inscripcion", "info", "Oops...");
			return true;
		} else if (datos[0] == -8) {
			MensajeConClase("Seleccione si Necesita Tiquetes", "info", "Oops...");
			return true;
		} else if (datos[0] == -9) {
			MensajeConClase("Ingrese Valor de la Inscripcion", "info", "Oops...");
			return true;
		} else if (datos[0] == -10) {
			MensajeConClase("Ingrese Contacto", "info", "Oops...");
			return true;
		} else if (datos[0] == -11) {
			MensajeConClase("Ingrese Celular Contacto", "info", "Oops...");
			return true;
		} else if (datos[0] == -12) {
			MensajeConClase("Ingrese Correo Contacto", "info", "Oops...");
			return true;
		} else if (datos[0] == -13) {
			MensajeConClase("La Fecha de Inicio de la solicitud No puede Ser inferior a la Fecha Actual.", "info", "Oops...");
			return true;
		} else if (datos[0] == -14) {

			MensajeConClase("La Fecha Final de la solicitud no puede Ser inferior a la fecha Inicial.", "info", "Oops...");
			return true;
		} else if (datos[0] == -15) {
			MensajeFueraFechaeWarning("Tener en Cuenta...", "La solicitud ingresada se encuentra fuera de los tiempos establecidos, debe tener en cuenta que el departamento encargado del proceso cuenta con " + limite_dias + " días hábiles para gestionar la solicitud.", 1);
			return true;
		} else if (datos[0] == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...");
			return true;
		} else if (datos[0] == 0) {
			borrador_solicitud = datos[1];
			nombre_evento = borrador_solicitud[1];
			tipo_evento_gen = borrador_solicitud[4];
			MostrarInformacionevento(tipo_evento_gen);
			Reiniciar_Tabla();
			if (tipo_gen == "SolT2") {
				//$("#Guardar_trasnporte").get(0).reset();
				//Reiniciar_Tabla();
				$("#Modal-add-transporte").modal("show");
			} else
				if (tipo_gen == "SolT1") {
					//$("#Guardar_Itinerario").get(0).reset();
					//Reiniciar_Tabla();
					documentos();
					//aqui
					$("#Modal-add-itinerario").modal("show");
					if (tipo_evento_gen == 'Even_Nac') {
						$('#viaticos').removeClass('oculto');	
						$('#tiquete').removeClass('oculto');	
						$('#hotel').addClass('oculto');
						$('#seguro').addClass('oculto');
						$('#mul_des').addClass('oculto');
					}else if(tipo_evento_gen == 'Even_Int'){
						$('#viaticos').removeClass('oculto');	
						$('#tiquete').removeClass('oculto');	
						$('#hotel').removeClass('oculto');
						$('#seguro').removeClass('oculto');
						$('#mul_des').removeClass('oculto');
					}else{
						$('#viaticos').addClass('oculto');	
						$('#tiquete').addClass('oculto');	
						$('#hotel').addClass('oculto');
						$('#seguro').addClass('oculto');
						$('#mul_des').addClass('oculto');
					}

				} else if (tipo_gen == "SolT3") {
					//Configurar_form_Reserva_Adm("");
					//$("#Guardar_solicitud_tipo3").get(0).reset();
					$("#Modal-guardar-tipo3").modal("show");
				} else
					if (tipo_gen == "SolT4") {
						//Reiniciar_Tabla();
						//$("#Guardar_bodega").get(0).reset();
						//reiniciar_form_tipo4();
						//con_requ = 0;
						$("#Modal-add-bodega").modal("show");
					}
			//$("#Guardar_solicitud_general").get(0).reset();
			//MensajeConClase("Paso 1 terminado, ya puede Continuar con el detalle de la solicitud para finalizar.", "success", "Proceso Exitoso!")
			$("#Modal-add-via").modal("hide");
			return true;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Guardar la solicitud: " + datos, "error", "Oops...")
		}

	});

}

function Modificar_Solicitud(continuar) {

	MensajeConClase("validando info", "add_inv", "Oops...");
	var formData = new FormData(document.getElementById("Modificar_solicitud_general"));
	formData.append("id", id_solicitud);
	formData.append("tipo_solicitud", tipo_sol_modi);
	formData.append("cont", continuar);
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Modificar",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1) {

			MensajeConClase("Error al cargar el tipo de Solicitud", "info", "Oops...");
			return true;

		} else if (datos == -2) {
			MensajeConClase("Ingrese Nombre de la solicitud", "info", "Oops...");
			return true;

		} else if (datos == -3) {
			MensajeConClase("Seleccione Fecha Inicio de la solicitud", "info", "Oops...");
			return true;
		} else if (datos == -4) {
			MensajeConClase("Seleccione Fecha Final de la solicitud", "info", "Oops...");
			return true;
		} else if (datos == -9) {
			MensajeConClase("Ingrese Valor de la Inscripcion", "info", "Oops...");
			return true;
		} else if (datos == -10) {
			MensajeConClase("Ingrese Contacto", "info", "Oops...");
			return true;
		} else if (datos == -11) {
			MensajeConClase("Ingrese Celular Contacto", "info", "Oops...");
			return true;
		} else if (datos == -12) {
			MensajeConClase("Ingrese Correo Contacto", "info", "Oops...");
			return true;
		} else if (datos == -13) {
			MensajeConClase("La Fecha de Inicio de la solicitud No puede Ser inferior a la Fecha Actual.", "info", "Oops...")
			return true;
		} else if (datos == -14) {
			MensajeConClase("La Fecha Final de la solicitud no puede Ser inferior a la fecha Inicial.", "info", "Oops...")
			return true;
		} else if (datos == -15) {
			MensajeConClase("Error al cargar el id de la solcitud, contacte con el administrador.", "error", "Oops...")
			return true;
		} else if (datos == -16) {
			MensajeFueraFechaeWarning("Tener en Cuenta...", "La solicitud ingresada se encuentra fuera de los tiempos establecidos, debe tener en cuenta que el departamento encargado del proceso cuenta con " + limite_dias + " días hábiles para gestionar la solicitud.", 2);
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		} else if (datos == 0) {
			modificando = 0;
			tipo_sol_modi = "";
			swal.close();
			//MensajeConClase("Solicitud Modificada con Exito.", "success", "Proceso Exitoso!");
			$("#Modificar_solicitud_general").get(0).reset();
			$("#Modal-modificar-solicitud").modal("hide");
			Listar_solciitudes();
			return true;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Modificar la solicitud: " + datos, "error", "Oops...")
		}

	});

}

function Modificar_tipo3() {

	var formData = new FormData(document.getElementById("Modificar_solicitud_tipo3"));
	formData.append("id", id_general_tipo3);
	formData.append("tipo_reserva_Adm", tipo_reserva_Adm_modi);

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Modificar_tipo3",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == -14) {
			MensajeConClase("Error al cargar el id de la solicitud, contacte con el administrador.!!.", "error", "Oops...");
			return true;
		} else
			if (datos == -15) {
				MensajeConClase("Seleccione Categoria.", "info", "Oops...");
				return true;
			} else if (datos == -16) {
				MensajeConClase("Ingrese Codigo SAP.", "info", "Oops...");
				return true;
			} else if (datos == -17) {

				confirmar_sin_codigo_sap(7);
				return true;
			} else if (datos == -18) {
				MensajeConClase("Seleccione la Fecha de Entregar-Reserva de la solicitud.", "info", "Oops...");
				return true;
			} else if (datos == -19) {
				MensajeConClase("Seleccione la Fecha de Entregar-Reserva no puede ser inferior a la fecha Actual.", "info", "Oops...");
				return true;
			} else if (datos == -20) {
				MensajeConClase("Seleccione la Fecha de salida del tiquete.", "info", "Oops...");
				return true;
			} else if (datos == -21) {
				MensajeConClase("la Fecha de retorno del tiquete.", "info", "Oops...");
				return true;
			} else if (datos == -22) {
				MensajeConClase("la Fecha de salida del tiquete no puede ser inferior a la fecha Actual.", "info", "Oops...");
				return true;
			} else if (datos == -23) {
				MensajeConClase("la Fecha de salida del tiquete no puede ser inferior que la fecha de retorno.", "info", "Oops...");
				return true;
			} else if (datos == -24) {
				MensajeConClase("No se encontraron los archivos adjuntos para esta solicitud, si desea continuar con el proceso debe adjuntar los archivos", "info", "Oops...");
				return true;
			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
				return true;
			} else if (datos == 0) {
				swal.close();
				//MensajeConClase("Solicitud Modificada con Exito.", "success", "Proceso Exitoso!")
				$("#Modificar_solicitud_tipo3").get(0).reset();
				$("#Modal-modificar-tipo3").modal("hide");
				modificando_tipo3 = 0;
				id_general_tipo3 = "";
				listar_info_solicitud_tipo3(id_solicitud);

				return true;

			} else {
				// en dado caso que ocurra un error

				MensajeConClase("Error al Modificar la solicitud: " + datos, "error", "Oops...")
			}

	});

}

function Guardar_Solicicitudes_tipo3() {
	MensajeConClase("validando info", "add_inv", "Oops...");
	var formData = new FormData(document.getElementById("Guardar_solicitud_tipo3"));
	formData.append("solicitudADD", borrador_solicitud);
	formData.append("personas", personas_sele);
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Guardar_Solicicitudes_tipo3",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1) {
			MensajeConClase("Error al cargar el ID de la solicitud, contacte con el administrador.", "errro", "Oops...");
			return true;
		} else if (datos == -15) {
			MensajeConClase("Seleccione Categoria.", "info", "Oops...");
			return true;
		} else if (datos == -16) {
			MensajeConClase("Ingrese Codigo SAP.", "info", "Oops...");
			return true;
		} else if (datos == -17) {
			confirmar_sin_codigo_sap(5);
			return true;
		} else if (datos == -18) {
			MensajeConClase("Seleccione la Fecha de Entregar-Reserva de la solicitud.", "info", "Oops...");
			return true;
		} else if (datos == -19) {
			MensajeConClase("Seleccione la Fecha de Entregar-Reserva no puede ser inferior a la fecha Actual.", "info", "Oops...");
			return true;
		} else if (datos == -20) {
			MensajeConClase("Seleccione la Fecha de salida del tiquete.", "info", "Oops...");
			return true;
		} else if (datos == -21) {
			MensajeConClase("la Fecha de retorno del tiquete.", "info", "Oops...");
			return true;
		} else if (datos == -22) {
			MensajeConClase("la Fecha de salida del tiquete no puede ser inferior a la fecha Actual.", "info", "Oops...");
			return true;
		} else if (datos == -23) {
			MensajeConClase("la Fecha de salida del tiquete no puede ser inferior que la fecha de retorno.", "info", "Oops...");
			return true;
		} else if (datos == -24) {
			MensajeConClase("Seleccione Responsables.", "info", "Oops...");
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...");
			return true;
		} else if (datos == 0) {
			Listar_solciitudes();
			if (borrador_solicitud.length != 0) {
				var ser = '<a href="' + server + 'index.php/solicitudesADM"><b>AQUI</b></a>'
				mensaje = "Su solicitud " + nombre_evento + " fue ingresada con exito puede validar la informaci&oacuten " + ser;
				enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "Solicitud Administrativa", "ParCodAdm", -1);
			} else {
				listar_info_solicitud_tipo3(id_solicitud);
			}
			borrador_solicitud = [];
			$("#Guardar_solicitud_tipo3").get(0).reset();
			$("#Guardar_solicitud_general").get(0).reset();
			Configurar_form_Reserva_Adm("");
			$("#Modal-guardar-tipo3").modal("hide");
			MensajeConClase("Los datos para su solicitud " + nombre_evento + " fueron registrados con exito.", "success", "Proceso Exitoso!");
			asignando_t3 = 0;
			return true;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Guardar la solicitud: " + datos, "error", "Oops...")
		}

	});

}

function sin_filtros() {
	$("#tipos_solicitud_filtro").val("");
	$("#estados_solicitud_filtro").val("");
	$("#inicial_fecha_filtro").val("");
	Listar_solciitudes();
}

function Listar_solciitudes() {
	id_persona_solici_tabla = 0;
	id_solicitud_com = null;
	tipo_filtro = $("#tipos_solicitud_filtro").val();
	estado_filtro = $("#estados_solicitud_filtro").val();
	fecha_filtro = $("#inicial_fecha_filtro").val();
	if (estado_filtro.length != 0 || fecha_filtro.length != 0 || tipo_filtro.length != 0) {
		$(".mensaje-filtro").show("fast");
	} else {
		$(".mensaje-filtro").css("display", "none");
	}

	IsInte = false;
	id_solicitud = 0;
	$('#tabla_solicitudes_principal tbody').off('dblclick', 'tr');
	$('#tabla_solicitudes_principal tbody').off('click', 'tr');
	$('#tabla_solicitudes_principal tbody').off('click', 'tr td:nth-of-type(1)');


	var myTable = $("#tabla_solicitudes_principal").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_solciitudes",
			dataType: "json",
			type: "post",
			data: {
				tipo_filtro,
				estado_filtro,
				fecha_filtro
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
			"data": "nombre_evento"
		},
		{
			"data": "tipo"
		},
		{
			"data": "solicitante"
		},
		{
			"data": "fecha_registro"
		},
		{
			"data": "estado"
		},
		{
			"data": "gestion"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [{
			// genera boton para exportar Excel
			extend: 'excelHtml5',
			text: '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			className: 'btn btn-success',
		},
		{
			// genera boton para exportar csv
			extend: 'csvHtml5',
			text: '<i class="fa fa-file-text-o"></i>',
			titleAttr: 'CSV',
			className: 'btn btn-default',
		},
		{
			// genera boton para exportar pdf
			extend: 'pdfHtml5',
			text: '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF',
			className: 'btn btn-danger2',
		}
		],
	});

	$('#tabla_solicitudes_principal tbody').on('click', 'tr', function () {
		var data = myTable.row(this).data();
		nombre_evento = data.nombre_evento;
		id_solicitud = data.id;
		tipo_gen = data.tipo_gen;
		solicitante = data.solicitante;
		correo_solicitante = data.correo_persona_solciita;
		modificando = 0;
		modificando_tipo3 = 0;
		asignando_t4 = 0;
		asignando_t3 = 0;
		tipo_evento_gen = data.tipo_evento_gen;
		id_persona_solici_tabla = data.id_persona_solicita;

		$("#tabla_solicitudes_principal tbody tr").removeClass("warning");
		$(this).attr("class", "warning");


	});
	$('#tabla_solicitudes_principal tbody').on('dblclick', 'tr', function () {
		var data = myTable.row(this).data();
		obtener_info_solicitud_tabla_id(data);

	});

	$('#tabla_solicitudes_principal tbody').on('click', 'tr td:nth-of-type(1)', function () {
		var data = myTable.row($(this).parent()).data();
		obtener_info_solicitud_tabla_id(data);
	});

}

function holaaaa() {
	alert("llama")

	var formData = new FormData(document.getElementById("cargar_archivo"));
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/cargar_archivo",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		alert(datos)


	});

}

function vaidar_estado_Actual_solicitud(id, x, id_alterno, id_tarsporte) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/vaidar_estado_Actual_solicitud",
		type: "post",
		data: {
			id: id,
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == "Sol_soli") {
			if (x == 1) {
				id_solicitud_borrador = 0;
				borrador_solicitud = [];
				Reiniciar_Tabla();
				if (tipo_gen == "SolT1") {

					$("#Guardar_Itinerario").get(0).reset();
					$("#requiere_tiquetes").hide("fast");
					$("#requiere_hoteles").hide("fast");
					$("#requiere_hoteles input").removeAttr("required", "true");
					$("#requiere_tiquetes input").removeAttr("required", "true");
					$("#regresar_multip").hide("fast");
					$("#datos_destino").show("fast");
					$("#destinos").show("fast");
					$("#guardar_fin_datos").show("fast");
					$("#guardar_mas_datos").hide("fast");
					$(".ocultar_adjunto").hide("fast");
					$("#persona_solicita_tiquetes").html("Seleccione Participante");
					mas_destino = 0;
					$("#nombre_itine p").html("Datos del Itinerario");
					$("#Modal-add-itinerario").modal("show");
					//MostrarInformacionevento(tipo_evento_gen);
					return;
				} else if (tipo_gen == "SolT2") {
					$("#Guardar_trasnporte").get(0).reset();
					$("#Modal-add-transporte").modal("show");
					return;
				} else if (tipo_gen == "SolT4") {
					asignando_t4 = 1;
					reiniciar_form_tipo4();
					if (asignando_t4 == 1) {
						MostrarInformacionevento(tipo_evento_gen);
						$("#Modal-add-bodega").modal("show");
						return;
					}
					return;
				} else if (tipo_gen == "SolT3") {
					$("#Guardar_solicitud_tipo3").get(0).reset();
					asignando_t3 = 1;
					listar_info_solicitud_tipo3(id_solicitud);
					return;
				} else {
					MensajeConClase("Para el Tipo de solicitud seleccionada no esta disponible esta opcion.", "info", "Oops...")
					return;
				}

			} else if (x == 2) {
				retirar_Detalle_tiquete_seleccionado(id_alterno);
			} else if (x == 3 || x == 12) {
				retirar_responsable_transporte_seleccionado(id_alterno, id_tarsporte, x)

			} else if (x == 4) {
				Agregar_mas_responsable_sol(id_alterno, 3);
			} else if (x == 5) {
				modificando = 1;
				obtener_info_solicitud_id(id_solicitud);

			} else if (x == 6) {
				Buscar_transporte_id(id_alterno);
			} else if (x == 7) {
				Listar_detalle_tiquetes_id_persona(id_alterno)
			} else if (x == 8) {
				modificando_tipo3 = 1;
				listar_info_solicitud_tipo3(id);

			} else if (x == 9) {
				listar_info_solicitud_tipo4(id_alterno);

			} else if (x == 10) {
				retirar_transporte_seleccionado(id_alterno);

			} else if (x == 11) {
				Agregar_mas_responsable_sol(id_alterno, 6);
			} else if (x == 13) {
				retirar_pedido_seleccionado(id_alterno);

			}
		} else {
			MensajeConClase("La Solicitud  esta en proceso o terminada por lo cual no es posible continuar con esta operacion.", "info", "Oops...")
		}


	});
	return;
}

function Gestionar_solicitud(tipo, id) {
	var mensaje = "Solicitud .?"
	if (tipo == 1) {
		mensaje = "Tramitar " + mensaje;
	} else if (tipo == 3 || tipo == 2) {
		mensaje = "Denegar " + mensaje;
		swal({
			title: `${tipo == 3 ? 'Denegar Solicitud .?' : 'Aprobar Solicitud .?'}`,
			text: "Si desea continuar con el cambio de estado, por favor presionar la opción de 'Continuar'",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: `${tipo == 3 ? '#D9534F' : '#5cb85c'}`,
			confirmButtonText: "Si, Continuar!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Ingrese ${tipo == 3 ? 'Motivo' : 'Información'}`,
		}, function (motivo) {

			if (motivo === false)
				return false;
			if (motivo === "") {
				swal.showInputError(`Debe ${tipo == 3 ? 'ingresar el motivo por el cual no fue aprobada la solicitud' : 'informarle al usuario donde continua el proceso de su solicitud'}..!`);
				return false;
			} else {
				Cambiar_Estado(tipo, id, motivo);
				return false;
			}
		});
		return;
	} else if (tipo == 4) {
		mensaje = "Reiniciar " + mensaje;
	}
	swal({
		title: mensaje,
		text: "Si desea continuar con el cambio de estado, por favor presionar la opción de 'Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				if (tipo == 2) {
					//MensajeConClase(correo_solicitante, "add_adm", "Proceso Exitoso!");
				}
				Cambiar_Estado(tipo, id, "");
			}
		});

}

function Cambiar_Estado(tipo, id, mensaje = -1) {
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Gestionar_solicitud",
		type: "post",
		data: {
			tipo: tipo,
			id: id,
			mensaje
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		swal.close();
		//MensajeConClase("Estado de la solicitud modificado con exito.", "success", "Proceso Exitoso.!!");
		Listar_solciitudes();
		var ser = '<a href="' + server + 'index.php/solicitudesADM"><b>AQUI</b></a>'
		if (tipo == 3) {
			mensaje = "Su solicitud " + nombre_evento + " fue denegada puede validar la informaci&oacuten " + ser + "<br><br>Motivo: " + mensaje
			//	MensajeConClase("Estado de la solicitud modificado con exito.", "success", "Proceso Exitoso.!!");
			enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "Solicitud Administrativa", "ParCodAdm", 1);
		} else if (tipo == 2) {
			mensaje = "Su solicitud " + nombre_evento + " fue aprobada puede validar la informaci&oacuten " + ser + ".<br><br><b>Nota: </b>" + mensaje;
			enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "Solicitud Administrativa", "ParCodAdm", 1);

		}



	});
}

function iniciar_tabla_persona() {
	idpersona_seleccionada_re = 0;
	correo_soli = "";
	$("#input_sele_re").val("");
	$("#persona_solicita_seleccionada").html("Persona Solicita");
	var table = $("#tablapersonas_general_sele").DataTable({
		"destroy": true,
		searching: false,
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []

	});
}

function listar_Personas_solicitudes(dato) {
	idpersona_seleccionada_re = 0;
	$("#input_sele_re").val("");
	$('#tablapersonas_general_sele tbody').off('click', 'tr');
	$('#tablapersonas_general_sele tbody').off('dblclick', 'tr');

	var table = $("#tablapersonas_general_sele").DataTable({
		"destroy": true,
		searching: false,
		"ajax": {
			url: server + "index.php/personas_control/Cargar_personas_Dato",
			dataType: "json",
			type: "post",
			data: {
				dato: dato,
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
		"pageLength": 10,
		"columns": [{
			"data": "nombre"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "correo"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tablapersonas_general_sele tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		$("#tablapersonas_general_sele tbody tr").removeClass("success");
		$(this).attr("class", "success");
		idpersona_seleccionada_re = data.id;
		nombre_completo = data.nombre;

		if (idpersona_seleccionada_re == 0) {
			MensajeConClase("Seleccione la persona que desea asignar..!", "info", "Oops...");
		} else {
			if (tipo_asignacion == 1) {
				persona_Tiene_Tiquetes(idpersona_seleccionada_re, id_solicitud);
			} else if (tipo_asignacion == 2) {
				persona_Tiene_es_responsable_bus(idpersona_seleccionada_re, -1);
			} else if (tipo_asignacion == 3) {
				persona_Tiene_es_responsable_bus(idpersona_seleccionada_re, id_sol_bus_sele);
			} else if (tipo_asignacion == 4) {
				var persona = [nombre_completo, idpersona_seleccionada_re];
				Asignar_persona_sele(persona);
			} else if (tipo_asignacion == 5) {
				var persona = [nombre_completo, idpersona_seleccionada_re];
				Asignar_persona_sele(persona);
			} else if (tipo_asignacion == 6) {
				persona_Tiene_es_responsable_tipo3(idpersona_seleccionada_re, id_tipo3_sele);
			}
		}

	});
}

function Retirar_Persona_Listado_selecc(persona) {

	for (var i = 0; i < personas_seleccionadas.length; i++) {
		if (personas_seleccionadas[i][1] == persona) {
			personas_seleccionadas.splice(i, 1);
			MensajeConClase("", "success", "Persona Retirada..!");
			Pintar_Persona_combo(tipo_gen);
			return true;
		}

	}
	MensajeConClase("", "error", "Error Al Retirar..!")
	return false;
}

function Existe_Persona(persona) {
	for (var i = 0; i < personas_seleccionadas.length; i++) {
		if (personas_seleccionadas[i][1] == persona) {
			return true;
		}

	}
	return false;
}

function Solo_ide_personas() {

	personas_sele = [];
	for (var i = 0; i < personas_seleccionadas.length; i++) {
		personas_sele[i] = personas_seleccionadas[i][1];

	}

	return personas_sele;
}

function Asignar_persona_sele(persona) {
	if (tipo_asignacion == 1) {
		personas_seleccionadas.push(persona);
		Pintar_Persona_combo();
		Solo_ide_personas();

	} else if (tipo_asignacion == 2) {
		personas_seleccionadas.push(persona);

		Pintar_Persona_combo();
		Solo_ide_personas();
	} else if (tipo_asignacion == 3 || tipo_asignacion == 6) {
		swal({
			title: "Asignar Responsable.?",
			text: "Si desea continuar con la asignación del responsable presione la opción continuar.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Continuar!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
			function (isConfirm) {
				if (isConfirm) {
					personas_seleccionadas = [];
					Asignar_nuevo_responsable(id_sol_bus_sele, persona[1], tipo_asignacion);

					listar_Personas_solicitudes("**$dd4");
				}
			});
		return true;
	} else if (tipo_asignacion == 4) {
		if (modificando_tipo4 == 1) {
			$("#input_sele_responsable_bodega_modi").val(persona[1]);
			$("#persona_responsable_bodega_modi").html(persona[0]);
			$("#Modal-selec-personas-gen").modal("hide");
		} else {
			$("#input_sele_responsable_bodega").val(persona[1]);
			$("#persona_responsable_bodega").html(persona[0]);
			$("#Modal-selec-personas-gen").modal("hide");
		}
		personas_seleccionadas = [];
	} else if (tipo_asignacion == 5) {
		personas_seleccionadas.push(persona);
		Pintar_Persona_combo();
		Solo_ide_personas();
	}
	$("#input_persona_reserva").val("");
	$("#input_persona_reserva").focus();
	listar_Personas_solicitudes("**$dd4");
	MensajeConClase("", "success", "Persona Asignada.!");
}

function Pintar_Persona_combo(mensaje) {
	$(".cbx_personal_Asignado").html("");
	$(".cbx_personal_Asignado").append("<option   value=''>Personas Seleccionadas.!</option>");
	for (var i = 0; i < personas_seleccionadas.length; i++) {
		$(".cbx_personal_Asignado").append("<option   value= " + personas_seleccionadas[i][1] + ">" + personas_seleccionadas[i][0] + "</option>");

	}
}

function Guardar_Solicicitudes_tipo2() {
	var id_solicitud_n = "";
	MensajeConClase("validando info", "add_inv", "Oops...");
	var formData = new FormData(document.getElementById("Guardar_trasnporte"));
	if (id_solicitud_borrador != 0) {
		id_solicitud_n = id_solicitud_borrador;
	} else {
		id_solicitud_n = id_solicitud;
	}

	formData.append("personas", personas_sele);
	formData.append("id", id_solicitud_n);
	formData.append("solicitudADD", borrador_solicitud);

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Guardar_Solicicitudes_tipo2",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos[0] == "sin_session") {
			close();
			return;
		}

		if (datos[0] == 5) {
			MensajeConClase("Ingrese Lugar de Origen", "info", "Oops...");
			return true;
		} else if (datos[0] == 6) {
			MensajeConClase("Ingrese Lugar de destino", "info", "Oops...");
			return true;
		} else if (datos[0] == 9) {
			MensajeConClase("No Se Encontraron Persona Seleccionadas", "info", "Oops...");
			return true;
		} else if (datos[0] == 10) {
			MensajeConClase("El id de la Solicitud No fue encontrada, Informe al Administrador", "error", "Oops...");
			return true;
		} else if (datos[0] == 11) {
			borrador_solicitud = [];
			id_solicitud_borrador = datos[1];
			MensajeConClase("No fue posible Asignar Todos Los participantes..!", "error", "Atencion..!");
			return true;
		} else if (datos[0] == 12) {
			MensajeConClase("Ingrese Fecha de salida del Transporte", "info", "Oops...");
			return true;
		} else if (datos[0] == 13) {
			MensajeConClase("Ingrese Fecha de Retorno del Transporte", "info", "Oops...");
			return true;
		} else if (datos[0] == 14) {
			MensajeConClase("Debe Ingresar el Numero de Personas", "info", "Oops...");
			return true;
		} else if (datos[0] == 15) {
			MensajeConClase("Debe Ingresar el Codigo SAP", "info", "Oops...");
			return true;
		} else if (datos[0] == -15) {
			MensajeConClase("La Fecha de Salida del Transporte No puede Ser inferior a la Fecha Actual.", "info", "Oops...");
			return true;
		} else if (datos[0] == -16) {
			MensajeConClase("La Fecha de Retorno del Transporte no puede Ser inferior a la fecha de salida.", "info", "Oops...");
			return true;
		} else if (datos[0] == -17) {
			confirmar_sin_codigo_sap(1);
			return true;
		} else if (datos[0] == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...");
			return true;
		} else if (datos[0] == 0) {
			if (borrador_solicitud.length != 0) {
				Listar_solciitudes();
				var ser = '<a href="' + server + 'index.php/solicitudesADM"><b>AQUI</b></a>'
				mensaje = "Su solicitud " + nombre_evento + " fue ingresada con exito puede validar la informaci&oacuten " + ser;
				enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "Solicitud Administrativa", "ParCodAdm", -1);
			} else {
				Listar_detalle_bus_id(id_solicitud);
			}
			borrador_solicitud = [];
			id_solicitud_borrador = datos[1];
			MensajeConClase("Los datos para su solicitud " + nombre_evento + " fueron registrados con exito.", "success", "Proceso Exitoso!");
			$("#Guardar_trasnporte").get(0).reset();
			$("#Guardar_solicitud_general").get(0).reset();
			Reiniciar_Tabla();
			return true;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Guardar la solicitud", "error", "Oops...")
		}

	});

}

function Guardar_Solicicitudes_tipo1() {
	MensajeConClase("validando info", "add_inv", "Oops...");
	var id_solicitud_n = "";
	var formData = new FormData(document.getElementById("Guardar_Itinerario"));
	if (id_solicitud_borrador != 0) {
		id_solicitud_n = id_solicitud_borrador;
	} else {
		id_solicitud_n = id_solicitud;
	}
	formData.append("id", id_solicitud_n);
	formData.append("solicitudADD", borrador_solicitud);
	formData.append("personas", personas_sele);

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Guardar_Solicicitudes_tipo1",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos[0] == "sin_session") {
			close();
			return;
		}

		if (datos[0] == 5) {
			MensajeConClase("Ingrese Lugar de Origen", "info", "Oops...");
			return true;
		} else if (datos[0] == 6) {
			MensajeConClase("Ingrese Lugar de destino", "info", "Oops...");
			return true;
		} else if (datos[0] == 7) {
			MensajeConClase("Ingrese Fecha de salida del Tiquete", "info", "Oops...");
			return true;
		} else if (datos[0] == 8) {
			MensajeConClase("Ingrese Fecha de Retorno del Tiquete", "info", "Oops...");
			return true;
		} else if (datos[0] == 9) {
			MensajeConClase("Debe Seleccionar Una persona", "info", "Oops...");
			return true;
		} else if (datos[0] == 10) {
			MensajeConClase("El id de la Solicitud No fue encontrada, Informe al Administrador", "error", "Oops...");
			return true;
		} else if (datos[0] == 15) {
			MensajeConClase("Debe Ingresar el Codigo SAP", "info", "Oops...");
			return true;
		} else if (datos[0] == 16) {
			MensajeConClase("Seleccione fecha Ingreso al Hotel", "info", "Oops...");
			return true;
		} else if (datos[0] == 17) {
			MensajeConClase("Seleccione fecha de salida del Hotel", "info", "Oops...");
			return true;
		} else if (datos[0] == 18) {
			MensajeConClase("La fecha de Ingreso al hotel no puede ser menor que la fecha actual", "info", "Oops...");
			return true;
		} else if (datos[0] == 19) {
			MensajeConClase("La fecha de salida del Hotel no puede ser menor que la fecha de ingreso al hotel", "info", "Oops...");
			return true;
		} else if (datos[0] == -13) {
			MensajeConClase("La Fecha de Salida del Tiquete No puede Ser inferior a la Fecha Actual.", "info", "Oops...");
			return true;
		} else if (datos[0] == -14) {
			MensajeConClase("La Fecha de Retorno del Tiquete no puede Ser inferior a la fecha de salida del tiquete.", "info", "Oops...");
			return true;
		} else if (datos[0] == -17) {
			confirmar_sin_codigo_sap(6);
			return true;
		} else if (datos[0] == -18) {
			MensajeConClase("Para los Eventos Internacionales si requiere los tiquetes debe adjuntar el pasaporte y la Visa(si es necesaria) , ademas si requiere los viáticos debe adjuntar la Agenda del evento(si cuenta con una).", "info", "Oops...");
			return true;
		} else if (datos[0] == -19) {
			MensajeConClase("Error al cargar el pasaporte :\n" + datos[1], "info", "Oops...");
			return true;
		} else if (datos[0] == -20) {
			MensajeConClase("Error al cargar la VISA :\n" + datos[1], "info", "Oops...");
			return true;
		} else if (datos[0] == -21) {
			MensajeConClase("Error al cargar la Agenda o información del Evento :\n" + datos[1], "info", "Oops...");
			return true;
		} else if (datos[0] == -22) {
			borrador_solicitud = [];
			id_solicitud_borrador = datos[1];
			MensajeConClase("No fue posible Asignar Todos Los participantes..!", "error", "Atencion..!");
			return true;
		} else if (datos[0] == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...");
			return true;
		} else if (datos[0] == 0) {
			if (borrador_solicitud.length != 0) {
				Listar_solciitudes();
				var ser = '<a href="' + server + 'index.php/solicitudesADM"><b>AQUI</b></a>'
				mensaje = "Su solicitud " + nombre_evento + " fue ingresada con exito puede validar la informaci&oacuten " + ser;
				enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "Solicitud Administrativa", "ParCodAdm", -1);

			} else {
				Listar_detalle_tiquetes_id(id_solicitud);
			}

			borrador_solicitud = [];
			id_solicitud_borrador = datos[1];
			if (mas_destino == 1) {
				MensajeConClase("Datos Del Itinerario No." + destinos_gu + " Guardado", "success", "Proceso Exitoso!");
				$("#destinos input").val("");
				$("#destinos textarea").val("");
				$("#nombre_itine p").html("Datos del Itinerario No." + destinos_gu);
				destinos_gu++;
				return;
			}

			MensajeConClase("Los datos para su solicitud " + nombre_evento + " fueron registrados con exito.", "success", "Proceso Exitoso!");

			Reiniciar_Tabla();
			$("#Guardar_Itinerario").get(0).reset();
			$("#Guardar_solicitud_general").get(0).reset();
			$("#persona_solicita_tiquetes").html("Seleccione Participante");
			$("#requiere_tiquetes").hide("fast");
			$("#requiere_hoteles").hide("fast");
			$("#panel-requerimientos").show("slow");
			$("#panel-itinerario-datos").hide("fast");
			$("#requiere_hoteles input").removeAttr("required", "true");
			$("#requiere_tiquetes input").removeAttr("required", "true");
			$(".ocultar_adjunto").hide("fast");
			return true;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Guardar la solicitud: " + datos, "error", "Oops...");
		}

	});

}

function persona_Tiene_Tiquetes(id, idsoli) {
	if (Existe_Persona(idpersona_seleccionada_re)) {
		MensajeConClase("la persona seleccionada ya se encuestra Asignada..!", "info", "Oops...");
	} else {
		var persona = [nombre_completo, idpersona_seleccionada_re];
		Asignar_persona_sele(persona);
	}
	return;
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/persona_Tiene_Tiquetes",
		data: {
			id: id,
			idsoli: idsoli,
		},
		type: "post",
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}


		if (datos == 2) {
			MensajeConClase("la persona seleccionada Fue Asignada Anteriormente..!", "info", "Oops...");
			return;
		} else {
			var persona = [nombre_completo, idpersona_seleccionada_re];
			Asignar_persona_sele(persona);


		}
	});

}

function persona_Tiene_es_responsable_bus(id, idsoli) {
	if (tipo_asignacion == 2) {
		var persona = [nombre_completo, idpersona_seleccionada_re];

		if (Existe_Persona(idpersona_seleccionada_re)) {
			MensajeConClase("la persona seleccionada ya se encuestra Asignada..!", "info", "Oops...")
		} else {
			var s = 1;
			Asignar_persona_sele(persona);
		}
		return;
	}
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/persona_Tiene_es_responsable_bus",
		data: {
			id: id,
			idsoli: idsoli,
		},
		type: "post",
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}


		if (datos == 2) {
			MensajeConClase("la persona seleccionada Fue Asignada Anteriormente como responsable..!", "info", "Oops...");
			return;
		} else {
			var persona = [nombre_completo, idpersona_seleccionada_re];

			if (Existe_Persona(idpersona_seleccionada_re)) {
				MensajeConClase("la persona seleccionada ya se encuestra Asignada..!", "info", "Oops...")
			} else {

				Asignar_persona_sele(persona);
			}
			return;
		}
	});

}

function persona_Tiene_es_responsable_tipo3(id, idsoli) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/persona_Tiene_es_responsable_tipo3",
		data: {
			id: id,
			idsoli: idsoli,
		},
		type: "post",
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}


		if (datos == 2) {
			MensajeConClase("la persona seleccionada Fue Asignada Anteriormente como responsable..!", "info", "Oops...");
			return;
		} else {
			var persona = [nombre_completo, idpersona_seleccionada_re];

			if (Existe_Persona(idpersona_seleccionada_re)) {
				MensajeConClase("la persona seleccionada ya se encuestra Asignada..!", "info", "Oops...")
			} else {

				Asignar_persona_sele(persona);
			}
			return;
		}
	});

}

function Reiniciar_Tabla() {
	personas_sele = [];
	personas_seleccionadas = [];
	Pintar_Persona_combo();
	listar_Personas_solicitudes("-1");
	$("#input_persona_reserva").val("");
}

function obtener_info_solicitud_id(id) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Buscar_Solicitud_id",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return false;
		}

		$("#Modificar_solicitud_general").get(0).reset();
		$("#cbx_tipo_solicitud_modi").html(MostrarInformacionsolicitud_Datos(datos[0].tipo_gen).valor);
		$("#nombre_evento_modi").val(datos[0].nombre_evento);
		$("#cbx_tipo_evento_modi").val(datos[0].tipo_evento_gen);
		$("#fecha_inicio_evento_modi").val(datos[0].fecha_inicio_evento);

		tipo_sol_modi = datos[0].tipo_gen;

		if (tipo_sol_modi == "SolT1") {
			$("#cbx_tipo_evento_modi").show("fast");
		} else {
			$("#cbx_tipo_evento_modi").hide("fast");
		}
		$("#fecha_final_evento_modi").val(datos[0].fecha_fin_evento);
		$("#fecha_final_evento_div_modi").show("fast");
		if (datos[0].requiere_inscripcion == 1) {
			$("#check_inscr_modi").prop("checked", true);
			$("#txtvalor_inscripcion_modi").val(datos[0].valor_inscripcion);

			if (datos[0].descuento_inscripcion == 1) {
				$("#check_descuento_modi").prop("checked", true);
			}
			$("#txtContacto_modi").val(datos[0].contacto);

			$("#txtTelefono_contacto_modi").val(datos[0].telefono_contacto);
			$("#txtCelular_contacto_modi").val(datos[0].celular_contacto);
			$("#txtCorreo_contacto_modi").val(datos[0].correo_contacto);
			$("#txtWeb_contacto_modi").val(datos[0].web_contacto);
			$("#requiere_inscrip_modi").show("fast");
			$("#requiere_inscrip_modi .requerido").attr("required", "true");

		} else {
			$("#requiere_inscrip_modi").hide("fast");
			$("#requiere_inscrip_modi .requerido").removeAttr("required", "true");
		}
		$("#Modal-modificar-solicitud").modal("show");
		//MostrarInformacionsolicitud(tipo_sol_modi);
		return;


	});
}


async function obtener_info_solicitud_tabla_id(datos) {
	tipo_solicitud_sele = datos.tipo_gen;
	let data_comu = null;
	if (tipo_solicitud_sele == 'Even_Com') {
		id_solicitud_com = datos.id_evento_com;
		data_comu = await consulta_solicitud_comunicaciones_id(datos.id_evento_com);
		datos.fecha_inicio_evento = data_comu.fecha_inicio_evento;
		datos.fecha_fin_evento = data_comu.fecha_fin_evento;
	}

	$("#datos_detalle_tipo4").css("display", "none");
	$("#datos_detalle_tipo1").css("display", "none");
	$("#datos_detalle_tipo2").css("display", "none");
	$("#datos_servicios_com").css("display", "none");
	$("#datos-tipo-4").css("display", "none");
	$("#datos-tipo-3").css("display", "none");
	$(".tipo_viaje").css("display", "none");
	$(".tipo_clasificacion").css("display", "none");
	$(".tr_valor_motivo").css("display", "none");
	$("#tr_codigo_sap_com").css("display", "none");
	$("#tr_lugar_evento_com").css("display", "none");
	$("#tr_direccion_com").css("display", "none");
	$("#adjunto_comunicaciones").css("display", "none");
	$(".valor_solicitud").html(datos.tipo);
	$(".valor_evento").html(datos.nombre_evento);
	$(".valor_clasificacion").html(datos.valor_clasificacion);
	$(".valor_tipo").html(datos.tipo_evento);
	$(".valor_fecha_inicio").html(datos.fecha_inicio_evento);
	$(".valor_fecha_fin").html(datos.fecha_fin_evento);
	$(".valor_motivo").html(datos.motivo_den);
	if (datos.requiere_inscripcion == "SI") {
		$(".valor_con_inscripcion").html("SI, <span id = 'ver_detalle_inscripcion' class = 'pointer ttitulo'>Ver Detalle</span>");

		$(".valor_inscripcion").html(datos.valor_inscripcion);

		if (datos.descuento_inscripcion == 1) {
			$(".valor_con_descuento").html("SI");
		} else {
			$(".valor_con_descuento").html("NO");
		}
		$(".valor_contacto").html(datos.contacto);

		$(".valor_telefono_contacto").html(datos.telefono_contacto);
		$(".valor_celular_contacto").html(datos.celular_contacto);
		$(".valor_correo_contacto").html(datos.correo_contacto);
		$(".valor_pagina").html(datos.web_contacto);
		$("#ver_detalle_inscripcion").click(function () {

			$("#Modal-info-inscripcion").modal("show");
		});

	} else {
		$(".valor_con_inscripcion").html("NO");
	}

	$(".valor_solicitante").html(datos.solicitante);



	$(".valor_fecha_solicitud").html(datos.fecha_registro);
	$(".valor_estado").html(datos.estado);

	if (datos.estado_gen == "Sol_Den") {
		$(".tr_valor_motivo").show("fast");
	} else if (datos.estado_gen == "Sol_Apro") {
		$(".tr_valor_motivo").show("fast");
	}


	if (tipo_solicitud_sele == "SolT1") {
		$("#datos_detalle_tipo1").css("display", "block");
		$(".tipo_viaje").show("fast");
		$(".tipo_clasificacion").show("fast");
		$("#btn_asignar_detalle_info").show("fast");
		Listar_detalle_tiquetes_id(datos.id);
	} else if (tipo_solicitud_sele == "SolT2") {

		$("#datos_detalle_tipo2").css("display", "block");
		$("#btn_asignar_detalle_info").show("fast");
		Listar_detalle_bus_id(datos.id);
	} else if (tipo_solicitud_sele == "SolT3") {
		$("#btn_asignar_detalle_info").css("display", "none");
		$("#datos-tipo-3").css("display", "block");
		modificando_tipo3 = 0;
		listar_info_solicitud_tipo3(datos.id);
	} else if (tipo_solicitud_sele == "SolT4") {
		$("#btn_asignar_detalle_info").show("fast");
		$("#datos_detalle_tipo4").css("display", "block");
		Listar_detalle_pedidos_id(datos.id);
		//listar_info_solicitud_tipo4(datos.id);
	} else if (tipo_solicitud_sele == "Even_Com") {
		$("#datos_servicios_com").show("fast");
		$("#codigo_sap_com").html(data_comu.id_codigo_sap);
		$("#lugar_evento_com").html(data_comu.nombre_lugar);
		$("#direccion_com").html(data_comu.direccion);
		$("#tr_codigo_sap_com").show();
		$("#tr_lugar_evento_com").show();
		$("#tr_direccion_com").show();
		$("#adjunto_comunicaciones").show();
		listar_servicios_solicitud_com(data_comu.id);
	}

	$("#Modal-info-solicitud").modal("show");

}

const listar_servicios_solicitud_com = id => {
	$('#tabla_servicios_solicitud tbody').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
	let url = `${Traer_Server()}index.php/solicitudes_adm_control/listar_servicios_solicitud`;
	consulta_ajax(url, { id }, (resp) => {
		let i = 0;
		const table = $("#tabla_servicios_solicitud").DataTable({
			"destroy": true,
			"processing": true,
			'data': resp,
			"columns": [
				{
					"render": function (data, type, full, meta) {
						let { id_tipo_solicitud, tipo_ser, id_aux } = full;
						if (id_tipo_solicitud == 'Com_Env') {
							if (tipo_ser == 'Com' || id_aux == null) return `<span style="width: 100%;" class="pointer form-control" disabled='disabled'><span>Ver</span></span>`;
							else return `<span style=" background-color:white;color: black;width: 100%;" class="pointer form-control info_comp"><span>Ver</span></span>`;
						} else {
							i++;
							return i;
						}
					}
				},
				{
					"data": "nombre"
				},
				{
					"data": "fecha"
				},
				{
					"data": "solicitante"
				},

			],
			"language": idioma,
			dom: 'Bfrtip',
			"buttons": [get_botones()]

		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tabla_servicios_solicitud tbody').on('click', 'tr', function () {
			$("#tabla_servicios_solicitud tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_servicios_solicitud tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = table.row($(this).parent()).data();
			ver_detalle_servicio(data);

		});

		const ver_detalle_servicio = data => {
			let { cantidad, tipo, tipo_entrega, observaciones, id_aux } = data;
			$(".cantidad_servicio").html(cantidad);
			$(".tipo_entrega_servicio").html(tipo_entrega);
			$(".tipo_servicio").html(tipo);
			$(".observaciones_servicio").html(observaciones);
			if (cantidad == null) $("#tr_cantidad_ser").css('display', 'none');
			else $("#tr_cantidad_ser").show();
			if (tipo == null) $("#tr_tipo_ser").css('display', 'none');
			else $("#tr_tipo_ser").show();
			if (tipo_entrega == null) $("#tr_tipo_entrega_ser").css('display', 'none');
			else $("#tr_tipo_entrega_ser").show();
			if (observaciones == null) $("#tr_observaciones_ser").css('display', 'none');
			else $("#tr_observaciones_ser").show();
			id_aux != null ? $("#modal_detalle_servicio").modal() : $("#modal_detalle_servicio").hide()
		}
	});

}

function listar_info_solicitud_tipo3(id) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/listar_info_solicitud_tipo3",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return false;
		}
		if (asignando_t3 == 1) {
			if (datos.length == 0) {
				Configurar_form_Reserva_Adm("");
				$("#Modal-guardar-tipo3").modal("show");
			} else {
				MensajeConClase("La solicitud seleccionada ya le fue asignado el detalle anteriormente, por lo cual no esta disponible esta opcion.!", "info", "Oops.!");
			}
			return;
		}

		if (modificando_tipo3 == 1) {
			Configurar_form_Reserva_Adm_modi(datos.tipo_reserva_gen, datos);
			return;
		} else {

			if (datos.length == 0) {

				$("#btn_asignar_detalle_info").show("fast");
				$("#datos-tipo-3").css("display", "none");
				return;
			} else {
				$(".datos-tipo-3").show("fast");
				$("#btn_asignar_detalle_info").hide("slow");
			}
			$("#responsables_tipo3").css("display", "none");
			$(".columna").css("display", "none");
			$(".tipo_refrigerios").css("display", "none");
			$(".tipo_proveedor").css("display", "none");
			$(".tipo_polizas").css("display", "none");
			$("#fecha_entrega_reserva_info").show("fast");
			$(".valor_codigo_sap_tipo3").html(datos.codigo_sap);
			$(".valor_tipo_proveedor").html(datos.proveedor);
			$(".valor_tipo_poliza").html(datos.tipo_poliza);
			$(".valor_tipo_refrigerios").html(datos.tipo_refrigerios);
			$(".valor_categoria_tipo3").html(datos.categoria);
			$(".valor_fecha_reserva").html(datos.fecha_entrega_reserva);
			$(".valor_observaciones_reserva").html(datos.observaciones);
			$(".columna1 .valor_Columna1").html(datos.columna1);
			$(".columna2 .valor_Columna2").html(datos.columna2);
			$(".columna3 .valor_Columna3").html(datos.columna3);
			$(".columna4 .valor_Columna4").html(datos.columna4);
			$(".columna5 .valor_Columna5").html(datos.columna5);
			$("#observaciones_tipo3").html("Observaciones");
			$("#fecha_entrega_tipo3").html("Fecha Entrega");
			$(".valor_codigo_sap_tipo3_tr").show("fast");
			if (datos.tipo_reserva_gen == "SolT3_flor") {
				id_tipo3_sele = datos.id;
				$(".columna1 .valor_Columna1").html("<a href='" + server + ruta_personas + datos.columna1 + "' target='_blank'>Ver Adjuntos. </a>");
				$(".columna1 .ttitulo").html("Facturas");
				$(".columna1").show("fast");
				$("#ver_responsables_tipo3").html(` < span onclick = "Listar_responsables_tipo3_id(${datos.id})" > < span class = "fa fa-eye red" > < /span> Ver</span > `);
				$("#responsables_tipo3").show("fast");
				$("#observaciones_tipo3").html("Observaciones");

			} else if (datos.tipo_reserva_gen == "SolT3_refr") {
				$(".columna1 .ttitulo").html("#Personas");
				$(".columna1").show("fast");
				$(".columna2 .ttitulo").html("Cantidad x Persona");
				$(".columna2").show("fast");
				$(".columna3 .ttitulo").html("Lugar Entrega");
				$(".columna3").show("fast");
				$(".columna4 .ttitulo").html("Nombre Contacto");
				$(".columna4").show("fast");
				$(".columna5 .ttitulo").html("Celular Contacto");
				$(".columna5").show("fast");
				$(".tipo_refrigerios").show("fast");
				$(".tipo_proveedor").show("fast");


			} else if (datos.tipo_reserva_gen == "SolT3_rest") {

				$(".columna1 .ttitulo").html("#Personas");
				$(".columna1").show("fast");
				$("#fecha_entrega_tipo3").html("Fecha Reserva");

			} else if (datos.tipo_reserva_gen == "SolT3_even") {

				$(".columna1 .ttitulo").html("Hotel");
				$(".columna1").show("fast");
				$(".columna2 .ttitulo").html("#Personas");
				$(".columna2").show("fast");
				$("#fecha_entrega_tipo3").html("Fecha Reserva");
				$("#observaciones_tipo3").html("Servicios Requeridos");
			} else if (datos.tipo_reserva_gen == "SolT3_reno") {

				$(".columna1 .ttitulo").html("Nombre");
				$(".columna1").show("fast");
				//                $(".columna2 .ttitulo").html("Cotizacion");
				//                $(".columna2").show("fast");
				$(".columna3 .ttitulo").html("Valor");
				$(".columna3").show("fast");
				$("#fecha_entrega_reserva_info").css("display", "none");
			} else if (datos.tipo_reserva_gen == "SolT3_remb") {

				//                $(".columna1 .ttitulo").html("Nombre Evento");
				//                $(".columna1").show("fast");
				if (datos.columna2.length != 0) {
					$(".columna2 .valor_Columna2").html("<a href='" + server + ruta_reembolso + datos.columna2 + "' target='_blank'>Ver Adjuntos. </a>");

				} else {
					$(".columna2 .valor_Columna2").html("No hay Adjuntos");
				}
				$(".columna2 .ttitulo").html("Facturas");
				$(".columna2").show("fast");
				$("#fecha_entrega_tipo3").html("Fecha Reembolso");

			} else if (datos.tipo_reserva_gen == "SolT3_poli") {
				if (datos.tipo_poliza_general == "Poli_add") {
					if (datos.columna1.length != 0) {
						$(".columna1 .valor_Columna1").html("<a href='" + server + ruta_polizas + datos.columna1 + "' target='_blank'>Ver Adjuntos. </a>");

					} else {
						$(".columna1 .valor_Columna1").html("No hay Adjuntos");
					}
					if (datos.columna2.length != 0) {
						$(".columna2 .valor_Columna2").html("<a href='" + server + ruta_polizas + datos.columna2 + "' target='_blank'>Ver Adjuntos. </a>");

					} else {
						$(".columna2 .valor_Columna2").html("No hay Adjuntos");
					}
					$(".columna1 .ttitulo").html("Polizas Anteriores");
					$(".columna1").show("fast");
					$(".columna2 .ttitulo").html("Contratos");
					$(".columna2").show("fast");
				} else if (datos.tipo_poliza_general == "Poli_nue") {
					if (datos.columna1.length != 0) {
						$(".columna1 .valor_Columna1").html("<a href='" + server + ruta_polizas + datos.columna1 + "' target='_blank'>Ver Adjuntos. </a>");
					} else {
						$(".columna1 .valor_Columna1").html("No hay Adjuntos");
					}
					$(".columna1 .ttitulo").html("Contratos");
					$(".columna1").show("fast");
				}

				$(".tipo_polizas").show("fast");
				$("#fecha_entrega_reserva_info").css("display", "none");

			} else if (datos.tipo_reserva_gen == "SolT3_matr") {

				if (datos.columna4.length != 0) {
					$(".columna4 .valor_Columna4").html("<a href='" + server + ruta_matriculas + datos.columna4 + "' target='_blank'>Ver Adjuntos. </a>");

				} else {
					$(".columna4 .valor_Columna4").html("No hay Adjuntos");


				}
				if (datos.columna5.length != 0) {
					$(".columna5 .valor_Columna5").html("<a href='" + server + ruta_matriculas + datos.columna5 + "' target='_blank'>Ver Adjuntos. </a>");

				} else {
					$(".columna5 .valor_Columna5").html("No hay Adjuntos");


				}
				$(".columna5 .ttitulo").html("Recibo Pago adjunto");
				$(".columna5").show("fast");
				$(".columna4 .ttitulo").html("Acuerdo adjunto");
				$(".columna4").show("fast");
				$(".columna1 .ttitulo").html("Requiere");
				$(".columna1").show("fast");
				$(".columna2 .ttitulo").html("Salida Tiquetes");
				$(".columna2").show("fast");
				$(".columna3 .ttitulo").html("Retorno Tiquetes");
				$(".columna3").show("fast");
				$("#fecha_entrega_reserva_info").css("display", "none");
				$(".valor_codigo_sap_tipo3_tr").css("display", "none");
			} else if (datos.tipo_reserva_gen == "SolT3_memb") {
				$(".columna1 .ttitulo").html("Nombre Membrecia");
				$(".columna1").show("fast");
				$(".columna2 .ttitulo").html("Link Pago");
				$(".columna2").show("fast");
				$(".columna3 .ttitulo").html("Usuario");
				$(".columna3").show("fast");
				$(".columna4 .ttitulo").html("Contraseña");
				$(".columna4").show("fast");
				$(".columna5 .ttitulo").html("Valor");
				$(".columna5").show("fast");
				$("#fecha_entrega_reserva_info").css("display", "none");

			} else if (datos.tipo_reserva_gen == "SolT3_otra") {
				$(".columna1 .ttitulo").html("Archivos Adjuntos");
				$(".columna1").show("fast");
				if (datos.columna1.length != 0) {
					$(".columna1 .valor_Columna1").html("<a href='" + server + ruta_personas + datos.columna1 + "' target='_blank'>Ver Adjuntos. </a>");
				} else {
					$(".columna1 .valor_Columna1").html("No hay Adjuntos");
				}
			}
		}
	});
}

function listar_info_solicitud_tipo4(id) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/listar_info_solicitud_tipo4",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return false;
		}
		Config_fort4_tipo_modi(datos.id_categoria_gen);
		con_requ_modi = 0;
		MostrarInformacionevento(tipo_gen);
		$("#panel-requerimientos-bodega-modi").show("fast");
		$("#info-form-bodega_modi").hide("fast");
		if (datos.manteles != null) {
			$("#re_manteles_modi").prop("checked", true);
			$("#inp_manteles_modi").val(datos.manteles);
		}
		if (datos.sillas != null) {
			$("#re_sillas_modi").prop("checked", true);
			$("#inp_sillas_modi").val(datos.sillas);
		}
		if (datos.coctel != null) {
			$("#re_coctel_modi").prop("checked", true);
		}
		if (datos.carpas != null) {
			$("#re_carpas_modi").prop("checked", true);
			$("#inp_carpas_modi").val(datos.carpas);
		}
		if (datos.vasos != null) {
			$("#re_vasos_modi").prop("checked", true);
			$("#inp_vasos_modi").val(datos.vasos);
		}
		if (datos.tenedores != null) {
			$("#re_tenedores_modi").prop("checked", true);
			$("#inp_tenedores_modi").val(datos.tenedores);
		}
		if (datos.mesas != null) {
			$("#re_mesas_modi").prop("checked", true);
			$("#cbx_tipo_mesas_modi").val(datos.id_tipo_mesa);
			$("#inp_mesas_modi").val(datos.mesas);
		}
		if (datos.cuchillos != null) {
			$("#re_cuchillos_modi").prop("checked", true);
			$("#inp_cuchillos_modi").val(datos.cuchillos);
		}
		if (datos.platos != null) {
			$("#re_platos_modi").prop("checked", true);
			$("#cbx_tipo_platos_modi").val(datos.id_tipo_plato);
			$("#inp_platos_modi").val(datos.platos);
		}
		if (datos.cucharas != null) {
			$("#re_cucharas_modi").prop("checked", true);
			$("#cbx_tipo_cucharas_modi").val(datos.id_tipo_cuchara);
			$("#inp_cucharas_modi").val(datos.cucharas);
		}
		if (datos.tipo_refrigerios_gen != null) {
			$("#re_refri_modi").prop("checked", true);
			$("#cbx_refrigerios_modi").val(datos.tipo_refrigerios_gen);
			$("#canxpersona_modi").val(datos.cantidad_refrigerios);
			$("#tipo_entre_ref_modi").val(datos.tipo_entrega_refri);
		}
		if (datos.tipo_entrega_cafe != null) {
			$("#re_agua_modi").prop("checked", true);
			$("#tipo_entr_ca_modi").val(datos.tipo_entrega_cafe);

		}
		if (datos.con_portatil != null) {
			$("#re_port_modi").prop("checked", true);
		}
		if (datos.con_sonido != null) {
			$("#re_soni_modi").prop("checked", true);
		}

		if (datos.con_video_beam != null) {
			$("#re_vb_modi").prop("checked", true);
		}
		if (datos.con_almuerzo != null) {
			$("#re_almuerzo_modi").prop("checked", true);
		}
		if (datos.valor_flores != null) {
			$("#re_flores_modi").prop("checked", true);
			$("#valor_flores_modi").val(datos.valor_flores);
		}
		$("#codigo_sap_input_bodega_modi").val(datos.codigosap);
		$("#inp_lugar_entrega_modi").val(datos.lugar_entrega);
		$("#input_sele_responsable_bodega_modi").val(datos.id_responsable);
		$("#fecha_entrega_bo_modi").val(datos.fecha_entrega);
		$("#fecha_retiro_bode_modi").val(datos.fecha_retiro);
		$("#observaciobes_bode_modi").val(datos.observaciones);
		$("#persona_responsable_bodega_modi").html(datos.responsable);
		$("#inp_num_per_modi").val(datos.num_personas);
		$("#cbx_categorias_tipo4_modi").val(datos.id_categoria_gen);
		$("#fecha_entrega_modi_t4").val(datos.fecha_entrega);

		Configurando_modificar_bodega();
		id_modificando_t4 = datos.id;
		$("#Modal-modificarbodega").modal("show");
		return;

	});
}

function Detalle_tiquete_seleccionado(lugar_origen, lugar_destino, req_tiquete, fecha_salida, fecha_retorno, req_viaticos, req_seguro, observaciones, req_hotel, fecha_ingreso_hotel, fecha_salida_hotel, archivos, cod_sap, fecha_registro, persona, identificacion, visa, agenda) {
	$(".fechas_tiquetes").hide("fast");
	$(".fechas_hoteles").hide("fast");
	$(".datos_adjuntos").hide("fast");
	$(".datos_visa").hide("fast");
	$(".datos_otro").hide("fast");
	$(".valor_fecha_registro_ti").html(fecha_registro);
	$(".valor_persona_ti").html(persona);
	$(".valor_codigo_sap_ti").html(cod_sap);
	$(".valor_identificacion_ti").html(identificacion);
	$(".valor_lugar_origen").html(lugar_origen);
	$(".valor_lugar_destino").html(lugar_destino);
	if (req_tiquete == 1) {
		$(".valor_req_tiquete").html("SI");
		$(".fechas_tiquetes").show("fast");

	} else {
		$(".valor_req_tiquete").html("NO");
		$(".fechas_tiquetes").hide("fast");

	}
	if (req_viaticos == 1) {
		$(".valor_req_viaticos").html("SI");
	} else {
		$(".valor_req_viaticos").html("NO");
	}
	if (req_seguro == 1) {
		$(".valor_req_seguro").html("SI");
	} else {
		$(".valor_req_seguro").html("NO");
	}
	if (req_hotel == 1) {
		$(".valor_req_hotel").html("SI");

		$(".fechas_hoteles").show("fast");
	} else {
		$(".valor_req_hotel").html("NO");
		$(".fechas_hoteles").hide("fast");
	}
	$(".valor_fecha_salida").html(fecha_salida);
	$(".valor_observaciones_tique").html(observaciones);
	$(".valor_fecha_retorno").html(fecha_retorno);
	$(".valor_fecha_ingreso_hotel").html(fecha_ingreso_hotel);
	$(".valor_fecha_salida_hotel").html(fecha_salida_hotel);
	if (archivos != null) {
		$(".valor_datos_Adjuntos").html("<a href='" + server + ruta_personas + archivos + "' target='_blank'>Ver Adjuntos. </a>");
		$(".datos_adjuntos").show("fast");
	}
	if (visa != null) {
		$(".valor_datos_visa").html("<a href='" + server + ruta_personas + visa + "' target='_blank'>Ver Adjuntos. </a>");
		$(".datos_visa").show("fast");
	}
	if (agenda != null) {
		$(".valor_datos_otro").html("<a href='" + server + ruta_personas + agenda + "' target='_blank'>Ver Adjuntos. </a>");
		$(".datos_otro").show("fast");
	}
	$("#Modal-info-tiquete-seleccionado").modal("show");
}

function Listar_detalle_tiquetes_id(dato) {

	id_tiquete_pesona_sele = 0;
	$('#tabla_info_tipo1 tbody').off('click', 'tr');
	$('#tabla_info_tipo1 tbody').off('dblclick', 'tr');
	$('#tabla_info_tipo1 tbody').off('click', 'tr td:nth-of-type(1)');

	var table = $("#tabla_info_tipo1").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_detalle_tiquetes_id",
			dataType: "json",
			type: "post",
			data: {
				id: dato,
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
		"pageLength": 5,
		"columns": [{
			"data": "codigo"
		},
		{
			"data": "persona"
		},
		{
			"data": "cod_sap"
		},
		{
			"data": "lugar_destino"
		},
		{
			"data": "detalle"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [{
			// genera boton para exportar Excel
			extend: 'excelHtml5',
			text: '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			className: 'btn btn-success',
		},
		{
			// genera boton para exportar csv
			extend: 'csvHtml5',
			text: '<i class="fa fa-file-text-o"></i>',
			titleAttr: 'CSV',
			className: 'btn btn-default',
		},
		{
			// genera boton para exportar pdf
			extend: 'pdfHtml5',
			text: '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF',
			className: 'btn btn-danger2',
		}
		],

	});


	$('#tabla_info_tipo1 tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_info_tipo1 tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_tiquete_pesona_sele = data.id;
	});
	$('#tabla_info_tipo1 tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		Detalle_tiquete_seleccionado(data.lugar_origen, data.lugar_destino, data.req_tiquete, data.fecha_salida, data.fecha_retorno, data.req_viaticos, data.req_seguro, data.observaciones, data.req_hotel, data.fecha_ingreso_hotel, data.fecha_salida_hotel, data.archivo_adjunto, data.cod_sap, data.fecha_registro, data.persona, data.identificacion, data.archivo_visa, data.archivo_agenda);
	});
	$('#tabla_info_tipo1 tbody').on('click', 'tr td:nth-of-type(1)', function () {
		var data = table.row($(this).parent()).data();
		Detalle_tiquete_seleccionado(data.lugar_origen, data.lugar_destino, data.req_tiquete, data.fecha_salida, data.fecha_retorno, data.req_viaticos, data.req_seguro, data.observaciones, data.req_hotel, data.fecha_ingreso_hotel, data.fecha_salida_hotel, data.archivo_adjunto, data.cod_sap, data.fecha_registro, data.persona, data.identificacion, data.archivo_visa, data.archivo_agenda);

	});

}

function Listar_detalle_bus_id(dato) {

	id_sol_bus_sele = 0;
	$('#tabla_info_tipo2 tbody').off('click', 'tr');
	$('#tabla_info_tipo2 tbody').off('dblclick', 'tr');
	$('#tabla_info_tipo2 tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_info_tipo2").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_detalle_bus_id",
			dataType: "json",
			type: "post",
			data: {
				id: dato,
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
		"pageLength": 5,
		"columns": [{
			"data": "codigo"
		},
		{
			"data": "codigo_sap"
		},
		{
			"data": "num_personas"
		},
		{
			"data": "direccion_destino"
		},
		{
			"data": "responsables"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [{
			// genera boton para exportar Excel
			extend: 'excelHtml5',
			text: '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			className: 'btn btn-success',
		},
		{
			// genera boton para exportar csv
			extend: 'csvHtml5',
			text: '<i class="fa fa-file-text-o"></i>',
			titleAttr: 'CSV',
			className: 'btn btn-default',
		},
		{
			// genera boton para exportar pdf
			extend: 'pdfHtml5',
			text: '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF',
			className: 'btn btn-danger2',
		}
		],

	});

	$('#tabla_info_tipo2 tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_info_tipo2 tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_sol_bus_sele = data.id;
	});
	$('#tabla_info_tipo2 tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		Detalle_transporte_solicitado(data);
	});
	$('#tabla_info_tipo2 tbody').on('click', 'tr td:nth-of-type(1)', function () {
		var data = table.row($(this).parent()).data();
		Detalle_transporte_solicitado(data);
	});
}

function Listar_detalle_pedidos_id(dato) {
	$('#tabla_info_tipo4 tbody').off('click', 'tr');
	$('#tabla_info_tipo4 tbody').off('dblclick', 'tr');
	$('#tabla_info_tipo4 tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_info_tipo4").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_detalle_pedidos_id",
			dataType: "json",
			type: "post",
			data: {
				id: dato,
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
		"pageLength": 5,
		"columns": [{
			"data": "codigo"
		},
		{
			"data": "sap"
		},
		{
			"data": "fecha_entrega"
		},
		{
			"data": "categoria"
		},
		{
			"data": "gestion"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [{
			// genera boton para exportar Excel
			extend: 'excelHtml5',
			text: '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			className: 'btn btn-success',
		},
		{
			// genera boton para exportar csv
			extend: 'csvHtml5',
			text: '<i class="fa fa-file-text-o"></i>',
			titleAttr: 'CSV',
			className: 'btn btn-default',
		},
		{
			// genera boton para exportar pdf
			extend: 'pdfHtml5',
			text: '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF',
			className: 'btn btn-danger2',
		}
		],

	});

	$('#tabla_info_tipo4 tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_info_tipo4 tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$('#tabla_info_tipo4 tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		Detalle_pedido_solicitado(data);
	});
	$('#tabla_info_tipo4 tbody').on('click', 'tr td:nth-of-type(1)', function () {
		var data = table.row($(this).parent()).data();
		Detalle_pedido_solicitado(data);

	});
}

function Detalle_transporte_solicitado(datos) {
	$(".valor_codigosap_trans").html(datos.codigo_sap);
	$(".valor_personas_transp").html(datos.num_personas);
	$(".valor_lugar_origen_transp").html(datos.direccion_salida);
	$(".valor_lugar_destino_transp").html(datos.direccion_destino);
	$(".valor_fecha_salida_transp").html(datos.hora_salida);
	$(".valor_fecha_retorno_transp").html(datos.hora_regreso);
	$(".valor_observaciones_trans").html(datos.observaciones);
	$(".valor_fecha_registro_trans").html(datos.fecha_registro);
	Listar_responsables_buses_id(datos.id)
	$("#Modal-info-transporte-seleccionado").modal("show");
}

function Detalle_pedido_solicitado(datos) {
	$(".t4_oculto").css("display", "none");
	$(".valor_codigo_sap_tipo4").html(datos.sap);
	$(".valor_manteles_tipo4").html(datos.manteles);
	$(".valor_sillas_tipo4").html(datos.sillas);
	$(".valor_carpas_tipo4").html(datos.carpas);
	$(".valor_vasos_tipo4").html(datos.vasos);
	$(".valor_tenedores_tipo4").html(datos.tenedores);
	$(".valor_tipo_mesa_tipo4").html(datos.tipo_mesa);
	$(".valor_mesa_tipo4").html(datos.mesas);
	$(".valor_cuchillos_tipo4").html(datos.cuchillos);
	$(".valor_tipo_plato_tipo4").html(datos.tipo_plato);
	$(".valor_platos_tipo4").html(datos.platos);
	$(".valor_tipo_cucharas_tipo4").html(datos.tipo_cuchara);
	$(".valor_cucharas_tipo4").html(datos.cucharas);
	$(".valor_lugar_entrega_tipo4").html(datos.lugar_entrega);
	$(".valor_responsable_tipo4").html(datos.responsable);
	$(".valor_personas_tipo4").html(datos.num_personas);
	$(".valor_flores_tipo4").html(datos.valor_flores);
	$(".valor_celular_tipo4").html(datos.telefono);
	$(".valor_fecha_entrega_tipo4").html(datos.fecha_entrega);
	$(".valor_fecha_retiro_tipo4").html(datos.fecha_retiro);
	$(".valor_observaciones_tipo4").html(datos.observaciones);

	$(".valor_tipo_refri_tipo4").html(datos.tipo_refrigerios);
	$(".valor_cantidad_tipo4").html(datos.cantidad_refrigerios);
	$(".valor_entrega_re_tipo4").html(datos.tipo_entrega_refri);
	$(".valor_entr_caf_tipo4").html(datos.tipo_entrega_cafe);
	$(".valor_categoria_tipo4").html(datos.categoria);

	if (datos.manteles != null) {
		$(".tr_manteles").show("fast");
	}
	if (datos.valor_flores != null) {
		$(".tr_flores").show("fast");
	}
	if (datos.sillas != null) {
		$(".tr_sillas").show("fast");
	}
	if (datos.carpas != null) {
		$(".tr_carpas").show("fast");
	}
	if (datos.vasos != null) {
		$(".tr_vasos").show("fast");
	}
	if (datos.tenedores != null) {
		$(".tr_tenedores").show("fast");
	}
	if (datos.mesas != null) {
		$(".tr_mesas").show("fast");
	}
	if (datos.cuchillos != null) {
		$(".tr_cuchillos").show("fast");
	}
	if (datos.platos != null) {
		$(".tr_platos").show("fast");
	}
	if (datos.cucharas != null) {
		$(".tr_cucharas").show("fast");
	}


	if (datos.tipo_refrigerios != null) {
		$(".tr_refri").show("fast");
	}
	if (datos.tipo_entrega_cafe != null) {
		$(".tr_cafe").show("fast");
	}
	if (datos.con_almuerzo == 1) {
		$(".tr_almu").show("fast");
		$(".valor_alm_tipo4").html("SI");

	}

	if (datos.con_video_beam == 1) {
		$(".tr_recur").show("fast");
		$(".valor_recu_tipo4").html("SI");
	}
	if (datos.con_portatil == 1) {
		$(".tr_por").show("fast");
		$(".valor_port_tipo4").html("SI");
	}
	if (datos.coctel == 1) {
		$(".tr_coctel").show("fast");
		$(".valor_coctel_tipo4").html("SI");
	}
	if (datos.con_sonido == 1) {
		$(".tr_son").show("fast");
		$(".valor_sonido_tipo4").html("SI");

	}

	if (datos.adjunto != null) {
		$(".valor_adjuntos_tipo4").html("<a href='" + server + ruta_logistica + datos.adjunto + "' target='_blank'>Ver Adjuntos. </a>");

	} else {
		$(".valor_adjuntos_tipo4").html("No hay Adjuntos");


	}
	$("#Modal-info-solicitud-tipo4").modal("show");
}

function Buscar_transporte_id(id) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Buscar_transporte_id",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return false;
		}
		if (datos.length != 0) {
			$("#modificar_trasnporte").get(0).reset();
			$("#num_personas_modi").val(datos.num_personas);
			$("#dir_origen_modi").val(datos.direccion_salida);
			$("#dir_destino_modi").val(datos.direccion_destino);
			$("#hora_salida_modi").val(datos.hora_salida);
			$("#hora_retono_modi").val(datos.hora_regreso);
			//            if (datos.codigo_sap != "-----") {
			$("#codigo_sap_input_modi").val(datos.codigo_sap);
			$("#observaciones_input_modi").val(datos.observaciones);
			//                $("#codigo_sap_input_modi").attr("required", "true");
			//                $("#codigo_sap_input_modi").show("fast");
			//                $("#concodigotipo2_modi").prop("checked", true);
			//
			//            } else {
			//
			//                $("#codigo_sap_input_modi").val("");
			//                $("#codigo_sap_input_modi").removeAttr("required");
			//                $("#codigo_sap_input_modi").hide("fast");
			//            }
			id_sol_bus_sele = id;
			MostrarInformacionevento(tipo_evento_gen);
			$("#Modal-modificar-transporte").modal("show");
		} else {
			MensajeConClase("Error al Cargar la informacion del transporte, contacte con el administrador", "error", "Oops...")
		}

	});
}

function Listar_detalle_tiquetes_id_persona(id) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Listar_detalle_tiquetes_id_persona",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return false;
		}
		if (datos.length != 0) {
			$("#panel-requerimientos-modi").show("fast");
			$("#panel-itinerario-datos-modi").hide("fast");
			$("#modificar_itinerario").get(0).reset();
			$("#lugar_origen_modifica").val(datos.lugar_origen);
			$("#lugar_destino_modifica").val(datos.lugar_destino);


			if (datos.req_tiquete == 1) {
				$("#fecha_retorno_tiqu_modifica").val(datos.fecha_retorno);
				$("#fecha_salida_tiqu_modifica").val(datos.fecha_salida);
				$("#re_tiquete_modifica").prop("checked", true);
				$("#requiere_tiquetes_modifica").show("fast");
				$("#requiere_tiquetes_modifica input").attr("required", "true");
				if (tipo_evento_gen == "Even_Nac") {
					$(".ocultar_adjunto_modi").hide("fast");
				} else {
					$(".ocultar_adjunto_modi").show("fast");
				}
			} else {
				$("#requiere_tiquetes_modifica").hide("fast");
				$("#requiere_tiquetes_modifica input").removeAttr("required", "true");
				$(".ocultar_adjunto_modi").hide("fast");
			}
			if (datos.req_viaticos == 1) {
				$("#re_viaticos_modifica").prop("checked", true);
			}
			if (datos.req_hotel == 1) {
				$("#re_hotel_modifica").prop("checked", true);
				$("#fecha_ingreso_hotel_modifica").val(datos.fecha_ingreso_hotel);
				$("#fecha_salida_hotel_modifica").val(datos.fecha_salida_hotel);
				$("#requiere_hoteles_modifica").show("fast");
				$("#requiere_hoteles_modifica input").attr("required", "true");
			} else {
				$("#requiere_hoteles_modifica").hide("fast");
				$("#requiere_hoteles_modifica input").removeAttr("required", "true");
			}
			if (datos.req_seguro == 1) {
				$("#re_seguro_modifica").prop("checked", true);
			}
			$("#codigo_sap_input_modifica").val(datos.cod_sap);
			$("#observaciones_input_modifica").val(datos.observaciones);


			id_tiquete_pesona_sele = id;
			MostrarInformacionevento(tipo_evento_gen);
			$("#Modal-modificar-itinerario").modal("show");
		} else {
			MensajeConClase("Error al Cargar la informacion del Itinerario, contacte con el administrador", "error", "Oops...")
		}

	});
}

function retirar_transporte_seleccionado(id) {
	swal({
		title: "Retirar Transporte..?",
		text: "Si desea continuar con el retiro del transporte debe presionar la opción de 'Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Retirar_solicitud_bus(id)
			}
		});
	return;
}

function retirar_pedido_seleccionado(id) {
	swal({
		title: "Retirar Detalle..?",
		text: "Si desea continuar con el retiro del detalle seleccionado debe presionar la opción de 'Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Retirar_solicitud_pedido(id)
			}
		});
	return;
}


function retirar_Detalle_tiquete_seleccionado(id) {
	swal({
		title: "Retirar Persona..?",
		text: "Si desea continuar con el retiro de la persona en la solicitud seleccionada, por favor presionar la opción de 'Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Retirar_persona_tiquete(id)
			}
		});
}

function retirar_responsable_transporte_seleccionado(id, idtras, x) {
	swal({
		title: "Retirar Responsable..?",
		text: "Si desea continuar con el retiro del responsable en la solicitud seleccionada, por favor presionar la opción de 'Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Retirar_persona_responsable(id, idtras, x)
			}
		});
}

function Listar_responsables_buses_id(id) {

	id_responsable_sele = 0;
	$('#tablaresponsables_buses tbody').off('click', 'tr');
	$('#tablaresponsables_buses tbody').off('dblclick', 'tr');

	var table = $("#tablaresponsables_buses").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_responsables_buses_id",
			dataType: "json",
			type: "post",
			data: {
				id: id,
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
		"pageLength": 5,
		"columns": [{
			"data": "nombre_completo"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "telefono"
		},
		{
			"data": "correo"
		},
		{
			"data": "op"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tablaresponsables_buses tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tablaresponsables_buses tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_responsable_sele = data.id_res;
	});
	$('#tablaresponsables_buses tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
	});


}

function Listar_responsables_tipo3_id(id) {

	id_responsable_sele_tipo3 = 0;
	$('#tablaresponsables_tipo3 tbody').off('click', 'tr');
	$('#tablaresponsables_tipo3 tbody').off('dblclick', 'tr');

	var table = $("#tablaresponsables_tipo3").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/solicitudes_adm_control/Listar_responsables_tipo3_id",
			dataType: "json",
			type: "post",
			data: {
				id: id,
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
		"pageLength": 5,
		"columns": [{
			"data": "nombre_completo"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "telefono"
		},
		{
			"data": "correo"
		},
		{
			"data": "op"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tablaresponsables_tipo3 tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tablaresponsables_tipo3 tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_responsable_sele_tipo3 = data.id_res;
	});
	$('#tablaresponsables_tipo3 tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
	});
	$("#Modal-info-responsables-tipo3").modal();

}

function Retirar_persona_tiquete(id) {
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Retirar_persona_tiquete",
		type: "post",
		data: {
			id: id,
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")

		} else {
			swal.close();
			//MensajeConClase("la persona seleccionada fue retirada de la solicitud ..!", "success", "Proceso Exitoso.!!")
			Listar_detalle_tiquetes_id(id_solicitud);

		}
	});
}

function Retirar_solicitud_bus(id) {
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Retirar_solicitud_bus",
		type: "post",
		data: {
			id: id,
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")

		} else {
			swal.close();
			//MensajeConClase("El transporte seleccionado fue retirado de la solicitud ..!", "success", "Proceso Exitoso.!!")
			Listar_detalle_bus_id(id_solicitud);

		}
	});
}

function Retirar_solicitud_pedido(id) {
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Retirar_solicitud_pedido",
		type: "post",
		data: {
			id: id,
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")

		} else {
			swal.close();
			//MensajeConClase("El Detalle seleccionado fue retirado de la solicitud ..!", "success", "Proceso Exitoso.!!")
			Listar_detalle_pedidos_id(id_solicitud);

		}
	});
}

function Retirar_persona_responsable(id, idtra, x) {
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Retirar_persona_responsable",
		type: "post",
		data: {
			id: id,
			tipo: x
		},
		dataType: "json",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == -1302) {

			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")

		} else {
			swal.close();
			//MensajeConClase("la persona responsable fue retirada de la solicitud ..!", "success", "Proceso Exitoso.!!");
			if (x == 3) {
				Listar_responsables_buses_id(idtra);
			} else {
				Listar_responsables_tipo3_id(id_tipo3_sele);
			}

		}

	});
}

function Agregar_mas_responsable_sol(id, tipo) {

	tipo_asignacion = tipo;
	$("#Modal-selec-personas-gen").modal("show");
	id_sol_bus_sele = id;
	id_tipo3_sele = id;
	Reiniciar_Tabla();

}

function Asignar_nuevo_responsable(id, personas, tipo) {

	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Asignar_nuevo_responsable",
		type: "post",
		data: {
			id: id,
			personas: personas,
			tipo: tipo
		},
		dataType: "json",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == 1) {
			MensajeConClase("No Se Encontraron Persona Seleccionadas", "info", "Oops...")
			return true;
		} else if (datos == 2) {
			MensajeConClase("Error con la llave de la solicitud, contacte al administrador", "error", "Oops...")
			return true;
		} else if (datos == 3) {
			MensajeConClase("Ocurrio un error al asignar un responsable, valide la informacion en el listado de responsables.!!", "info", "Oops...")
			return true;
		} else if (datos == 0) {
			swal.close();
			//MensajeConClase("Responsable Asignado Con Exito..!", "success", "Proceso Exitoso..!");
			Reiniciar_Tabla();
			if (tipo == 3) {
				Listar_responsables_buses_id(id_sol_bus_sele);
			} else {
				Listar_responsables_tipo3_id(id_tipo3_sele);
			}

			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		} else {
			MensajeConClase("Error con Asignar Responsables, contacte al administrador", "error", "Oops...")
			return true;
		}
	});

}

function modificar_transporte() {

	var formData = new FormData(document.getElementById("modificar_trasnporte"));
	formData.append("id", id_sol_bus_sele);


	$.ajax({
		url: server + "index.php/solicitudes_adm_control/modificar_transporte",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == 5) {
			MensajeConClase("Ingrese Lugar de Origen", "info", "Oops...")
			return true;
		} else if (datos == 6) {
			MensajeConClase("Ingrese Lugar de destino", "info", "Oops...")
			return true;
		} else if (datos == 10) {
			MensajeConClase("El id del Transporte No fue encontrado, Informe al Administrador", "error", "Oops...")
			return true;
		} else if (datos == 12) {
			MensajeConClase("Ingrese Fecha de salida del Transporte", "info", "Oops...")
			return true;
		} else if (datos == 13) {
			MensajeConClase("Ingrese Fecha de Retorno del Transporte", "info", "Oops...")
			return true;
		} else if (datos == 14) {
			MensajeConClase("Debe Ingresar el Numero de Personas", "info", "Oops...")
			return true;
		} else if (datos == 15) {
			MensajeConClase("Debe Ingresar el Codigo SAP", "info", "Oops...")
			return true;
		} else if (datos == -15) {
			MensajeConClase("La Fecha de Salida del Transporte No puede Ser inferior a la Fecha Actual.", "info", "Oops...")
			return true;
		} else if (datos == -16) {
			MensajeConClase("La Fecha de Retorno del Transporte no puede Ser inferior a la fecha de salida.", "info", "Oops...")
			return true;
		} else if (datos == -17) {
			confirmar_sin_codigo_sap(2);
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		} else if (datos == 0) {
			swal.close();
			//MensajeConClase("Datos del Transporte Modificado con Exito.", "success", "Proceso Exitoso!");
			Listar_detalle_bus_id(id_solicitud);
			$("#Modal-modificar-transporte").modal("hide")

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Modificar el Transporte, contacte con el administrador", "error", "Oops...")
		}

	});

}

function modificar_tiquetes_viaticos() {

	var formData = new FormData(document.getElementById("modificar_itinerario"));
	formData.append("id", id_tiquete_pesona_sele);
	formData.append("id_solicitud", id_solicitud);


	$.ajax({
		url: server + "index.php/solicitudes_adm_control/modificar_tiquetes_viaticos",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == 5) {
			MensajeConClase("Ingrese Lugar de Origen", "info", "Oops...")
			return true;
		} else if (datos == 6) {
			MensajeConClase("Ingrese Lugar de destino", "info", "Oops...")
			return true;
		} else if (datos == 7) {
			MensajeConClase("Ingrese Fecha de salida del Tiquete", "info", "Oops...")
			return true;
		} else if (datos == 8) {
			MensajeConClase("Ingrese Fecha de Retorno del Tiquete", "info", "Oops...")
			return true;
		} else if (datos == 10) {
			MensajeConClase("El id de la Solicitud No fue encontrada, Informe al Administrador", "error", "Oops...")
			return true;
		} else if (datos == 11) {
			MensajeConClase("Ingrese Codigo SAP", "info", "Oops...");
			return true;
		} else if (datos == 16) {
			MensajeConClase("Seleccione fecha Ingreso al Hotel", "info", "Oops...")
			return true;
		} else if (datos == 17) {
			MensajeConClase("Seleccione fecha de salida del Hotel", "info", "Oops...")
			return true;
		} else if (datos == 18) {
			MensajeConClase("La fecha de Ingreso al hotel no puede ser menor que la fecha actual", "info", "Oops...")
			return true;
		} else if (datos == 19) {
			MensajeConClase("La fecha de salida del Hotel no puede ser menor que la fecha de ingreso al hotel", "info", "Oops...")
			return true;
		} else if (datos == -13) {
			MensajeConClase("La Fecha de Salida del Tiquete No puede Ser inferior a la Fecha Actual.", "info", "Oops...")
			return true;
		} else if (datos == -14) {
			MensajeConClase("La Fecha de Retorno del Tiquete no puede Ser inferior a la fecha de salida.", "info", "Oops...")
			return true;
		} else if (datos == -17) {
			confirmar_sin_codigo_sap(3);
			return true;
		} else if (datos == -18) {
			MensajeConClase("Para los Eventos Internacionales si requiere los tiquetes debe adjuntar el pasaporte y la Visa(si es necesaria) , ademas si requiere los viáticos debe adjuntar la Agenda del evento(si cuenta con una).", "info", "Oops...");
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		} else if (datos == 0) {
			swal.close();
			$("#requiere_tiquetes_modifica input").removeAttr("required", "true");
			$("#requiere_hoteles_modifica input").removeAttr("required", "true");
			//MensajeConClase("Datos del Itinerario Modificado con Exito...", "success", "Proceso Exitoso!");
			$("#Modal-modificar-itinerario").modal("hide");
			Listar_detalle_tiquetes_id(id_solicitud);
			return;

		} else {
			// en dado caso que ocurra un error

			MensajeConClase("Error al Modificar la solicitud:" + datos, "error", "Oops...")
		}

	});

}

function Existe_codigo_sap(valor, persona) {

	$.ajax({
		url: server + "index.php/genericas_control/obtener_valores_parametro_valox",
		type: "post",
		data: {
			valor: valor,
			idparametro: 25,
		},
		dataType: "json",
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}


	});
}

function confirmar_sin_codigo_sap(tipo, persona) {

	if (tipo != 6 && tipo != 3) {

		MensajeConClase("Ingrese un codigo SAP valido.!", "info", "Oops...");
		return true;
	}
	swal({
		title: "Codigo SAP Invalido",
		text: "El código SAP ingresado no se encuentra registrado en el sistema, si desea que el encargado del proceso le asigne un código valido debe presionar la opción de 'Si, Continuar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	}, function (isConfirm) {
		if (isConfirm) {
			if (tipo == 1) {
				$("#codigo_sap_input").val("-----");
				Guardar_Solicicitudes_tipo2();
			} else if (tipo == 2) {
				$("#codigo_sap_input_modi").val("-----");
				modificar_transporte();
			} else if (tipo == 3) {
				$("#codigo_sap_input_modifica").val("-----");
				modificar_tiquetes_viaticos();
			} else if (tipo == 4) {
				Existe_codigo_sap("-----", persona)
			} else if (tipo == 5) {
				$("#codigo_sap_input_Reserva").val("-----");
				// Guardar_Solicitud();
			} else if (tipo == 6) {
				$("#codigo_sap_tipo1").val("-----");
				Guardar_Solicicitudes_tipo1();
			} else if (tipo == 7) {
				$("#codigo_sap_input_Reserva_modi").val("-----");
				Modificar_tipo3();
			} else if (tipo == 8) {
				$("#codigo_sap_input_bodega").val("-----");
				Guardar_Solicicitudes_tipo4();
			} else if (tipo == 9) {
				$("#codigo_sap_input_bodega_modi").val("-----");
				Modificar_Solicicitudes_tipo4();
			}
		}
	});
}

function PasarArray(datos) {

	datos_solicitud = datos;
	Cargar_informacion_menu();
}

function PasarArray2(datos) {
	datos_reserva = datos;
}

function PasarArray3(datos) {
	datos_evento = datos;
}

function Configurar_form_Reserva_Adm_modi(tipo, datos) {
	id_general_tipo3 = datos.id;
	tipo_reserva_Adm_modi = tipo;
	$("#tipo_reserva_Adm_modi").html(datos.categoria);
	$(".div_mensaje_reserv").css("display", "none");
	$("#adjuntos_reserva_modi").html("");
	$("#tipo-polizas_modi").css("display", "none");
	$("#columnas_modi input").val("");
	$("#columnas_modi input").css("display", "none");
	$("#columnas_modi input").removeAttr("required", "true");
	$("#columnas_modi input").attr("type", "text");
	$("#observaciones_reserva_modi").attr("placeholder", "Observaciones");
	$("#codigo_sap_input_Reserva_modi").attr("required", "true");
	$("#codigo_sap_input_Reserva_modi").show("fast");
	$("#tipo_refrigerios_modi").css("display", "none");
	$("#proveedor_modi").css("display", "none");
	$("#re_viaticos_resereva_modi").css("display", "none");
	$("#tipo_refrigerios_modi").removeAttr("required", "true");
	$("#tipo-polizas_modi").removeAttr("required", "true");
	$("#proveedor_modi").removeAttr("required", "true");
	$("#re_viaticos_resereva_modi").removeAttr("required", "true");
	$("#requiere_tiquetes_Reserva_modi").css("display", "none");
	$("#codigo_sap_input_Reserva_modi").val(datos.codigo_sap);
	$("#proveedor_modi").val(datos.proveedor_gen);
	$("#tipo-polizas_modi").val(datos.tipo_poliza_general);
	$("#tipo_refrigerios_modi").val(datos.tipo_refrigerios_gen);
	$("#fecha_Reserva_modi").val(datos.fecha_entrega_reserva);
	$("#observaciones_reserva_modi").val(datos.observaciones);
	$("#columna1_modi").val(datos.columna1);
	$("#re_viaticos_resereva_modi").val(datos.columna1);
	$("#columna2_modi").val(datos.columna2);
	$("#columna3_modi").val(datos.columna3);
	$("#columna4_modi").val(datos.columna4);
	$("#columna5_modi").val(datos.columna5);
	$("#requiere_tiquetes_Reserva_modi input").removeAttr("required", "true");
	$("#fecha_Reserva_modi").attr("required", "true");
	if (tipo == "SolT3_flor") {
		// si es de categoria flor
		// columna1 -> Valor_Flores
		// $("#columna1_modi").attr("required", "true");
		// $("#columna1_modi").attr("placeholder", "Valor Flores");
		// $("#columna1_modi").show("fast");
		// $("#columna1_modi").attr("type", "number");
		// $("#observaciones_reserva_modi").attr("placeholder", "Tipo de Arreglo Solicitado");
		// $("#fecha_Reserva_modi").attr("placeholder", "Fecha Entrega");
		$("#observaciones_reserva_modi").attr("placeholder", "Observaciones");
		var titulos = ["Adjuntar Facturas"];
		CrearAdjuntar_modi(1, titulos);

	} else if (tipo == "SolT3_even") {
		//si es de categoria eventos
		// columna1 -> hotel
		// columna2 -> Numero de personas
		$("#columna1_modi").attr("required", "true");
		$("#columna1_modi").attr("placeholder", "Hotel");
		$("#columna1_modi").show("fast");
		$("#columna2_modi").attr("required", "true");
		$("#columna2_modi").attr("placeholder", "# Personas");
		$("#columna2_modi").show("fast");
		$("#columna2_modi").attr("type", "number");
		$("#observaciones_reserva_modi").attr("placeholder", "Servicios Requeridos");

	} else if (tipo == "SolT3_refr") {
		//Si es de categoria refrigerios
		// columna1 -> Numero Persona
		// columna2 -> Cantidad Persona
		// columna3 -> Lugar Entrega
		// columna4 -> Nombre Contacto
		// columna5 -> Celular Contacto
		$("#columna1_modi").attr("required", "true");
		$("#columna1_modi").attr("placeholder", "#Personas");
		$("#columna1_modi").show("fast");
		$("#columna1_modi").attr("type", "number");
		$("#columna2_modi").attr("required", "true");
		$("#columna2_modi").attr("placeholder", "Cantidad x persona");
		$("#columna2_modi").show("fast");
		$("#columna2_modi").attr("type", "number");
		$("#columna3_modi").attr("required", "true");
		$("#columna3_modi").attr("placeholder", "Lugar Entrega");
		$("#columna3_modi").show("fast");
		$("#columna4_modi").attr("required", "true");
		$("#columna4_modi").attr("placeholder", "Nombre Contacto");
		$("#columna4_modi").show("fast");
		$("#columna5_modi").attr("required", "true");
		$("#columna5_modi").attr("placeholder", "Celular Contacto");
		$("#columna5_modi").show("fast");
		$("#columna5_modi").attr("type", "number");
		$("#tipo_refrigerios_modi").show("fast");
		$("#proveedor_modi").show("fast");
		$("#tipo_refrigerios_modi").attr("required", "true");
		$("#proveedor_modi").attr("required", "true");
		$("#fecha_Reserva_modi").attr("placeholder", "Fecha Entrega");
	} else if (tipo == "SolT3_rest") {
		// Si es de categoria Restaurante
		// columna1 -> Numero Personas
		$("#columna1_modi").attr("required", "true");
		$("#columna1_modi").attr("placeholder", "#Personas");
		$("#columna1_modi").show("fast");
		$("#columna1_modi").attr("type", "number");

	} else if (tipo == "SolT3_memb") {
		// Si es de categoria Membrecia
		// columna1 -> Nombre Menbrecia
		// columna2 -> Link de pago
		// columna3 -> Usuario
		// columna4 -> contraseña
		// columna5 -> valor
		$("#columna1_modi").attr("required", "true");
		$("#columna1_modi").attr("placeholder", "Nombre Membrecia");
		$("#columna1_modi").show("fast");

		$("#columna2_modi").attr("required", "true");
		$("#columna2_modi").attr("placeholder", "Link de pago");
		$("#columna2_modi").show("fast");
		$("#columna2_modi").attr("type", "url");

		$("#columna3_modi").attr("required", "true");
		$("#columna3_modi").attr("placeholder", "Usuario");
		$("#columna3_modi").show("fast");

		$("#columna4_modi").attr("required", "true");
		$("#columna4_modi").attr("placeholder", "Contraseña");
		$("#columna4_modi").show("fast");

		$("#columna5_modi").attr("required", "true");
		$("#columna5_modi").attr("placeholder", "Valor");
		$("#columna5_modi").show("fast");
		$("#columna5_modi").attr("type", "number");
		$("#fecha_reserva_div_modi").css("display", "none");
		$("#fecha_Reserva_modi").removeAttr("required", "true");
	} else if (tipo == "SolT3_reno") {
		// Si es de categoria renovacion
		// columna1 -> Nombre
		// columna2 -> Cotizacion
		// columna3 -> Valor

		$("#columna1_modi").attr("required", "true");
		$("#columna1_modi").attr("placeholder", "Nombre Renovacion");
		$("#columna1_modi").show("fast");

		//        $("#columna2_modi").attr("required", "true");
		//        $("#columna2_modi").attr("placeholder", "Cotizacion");
		//        $("#columna2_modi").show("fast");


		$("#columna3_modi").attr("required", "true");
		$("#columna3_modi").attr("placeholder", "Valor");
		$("#columna3_modi").show("fast");
		$("#columna3_modi").attr("type", "number");

	} else if (tipo == "SolT3_remb") {
		// Si es de categoria Membrecia
		// columna1 -> Nombre
		// columna2 -> link facturas cargadas
		var titulos = ["Adjuntar Facturas"];
		CrearAdjuntar_modi(1, titulos);
	} else if (tipo == "SolT3_poli") {
		// Si es de categoria poliza
		// columna1 -> link archivos

		$("#fecha_reserva_div_modi").css("display", "none");
		$("#fecha_Reserva_modi").removeAttr("required", "true");
		$("#tipo-polizas_modi").show("fast");
		$("#tipo-polizas_modi").attr("required", "true");

		if (datos.tipo_poliza_general == "Poli_add") {
			var titulo = ["Adjuntar Polizas Anteriores", "Adjuntar Contratos"];
			CrearAdjuntar_modi(2, titulo);
		} else {
			var titulo = ["Adjuntar Contratos"];
			CrearAdjuntar_modi(1, titulo);
		}
	} else if (tipo == "SolT3_matr") {
		// Si es de categoria matriculas
		// columna1 -> link archivos
		$("#codigo_sap_input_Reserva_modi").removeAttr("required", "true");
		$("#codigo_sap_input_Reserva_modi").css("display", "none");
		$("#re_viaticos_resereva_modi").show("fast");
		$("#re_viaticos_resereva_modi").attr("required", "true");
		if (datos.columna1 == "Tiquetes" || datos.columna1 == "Viaticos y tiquetes") {
			$("#requiere_tiquetes_Reserva_modi").show("fast");
			$("#fecha_salida_tiqu_Reserva_modi").val(datos.columna2);
			$("#fecha_retorno_tiqu_reserva_modi").val(datos.columna3);
			$("#requiere_tiquetes_Reserva_modi input").attr("required", "true");
		}
		var titulos = ["Adjuntar Acuerdo", "Adjuntar Recibo Pago"];
		CrearAdjuntar_modi(2, titulos);
	} else if (tipo == "SolT3_otra") {
		var titulos = ["Adjuntar Archivo"];
		CrearAdjuntar_modi(1, titulos);
	}
	$("#Modal-modificar-tipo3").modal("show");
	return;
}

function Configurar_form_Reserva_Adm(tipo) {

	$("#div_responsable_capa").css("display", "none");
	//La configuracion del form de solicitudes tipo 3 o de reservas y mas, los datos se configuran por categorias y los datos se almacenas en las columnas enumeradas columna1 a columna6
	$(".div_mensaje_reserv").css("display", "none");
	$("#adjuntos").html("");
	$("#tipo-polizas").css("display", "none");
	$("#columnas input").val("");
	$("#columnas input").css("display", "none");
	$("#columnas input").removeAttr("required", "true");
	$("#columnas input").attr("type", "text");
	$("#observaciones_reserva").attr("placeholder", "Observaciones");
	$("#codigo_sap_input_Reserva").attr("required", "true");
	$("#codigo_sap_input_Reserva").show("fast");
	$("#tipo_refrigerios").css("display", "none");
	$("#proveedor").css("display", "none");
	$("#re_viaticos_resereva").css("display", "none");
	$("#requiere_tiquetes_Reserva").css("display", "none");

	$("#tipo_refrigerios").removeAttr("required", "true");
	$("#tipo-polizas").removeAttr("required", "true");
	$("#proveedor").removeAttr("required", "true");
	$("#re_viaticos_resereva").removeAttr("required", "true");
	$("#requiere_tiquetes_Reserva input").removeAttr("required", "true");
	if (tipo == "SolT3_flor") {
		// si es de categoria flor
		// columna1 -> Valor_Flores
		//$("#columna1").attr("required", "true");
		//$("#columna1").attr("placeholder", "Valor Flores");
		//$("#columna1").show("fast");
		//	$("#columna1").attr("type", "number");
		$("#div_responsable_capa").show("fast");
		$("#observaciones_reserva").attr("placeholder", "Observaciones");
		//$("#fecha_Reserva").attr("placeholder", "Fecha Entrega");
		var titulos = ["Adjuntar Facturas"];
		CrearAdjuntar(1, titulos);
	} else if (tipo == "SolT3_even") {
		//si es de categoria eventos
		// columna1 -> hotel
		// columna2 -> Numero de personas
		$("#columna1").attr("required", "true");
		$("#columna1").attr("placeholder", "Hotel");
		$("#columna1").show("fast");
		$("#columna2").attr("required", "true");
		$("#columna2").attr("placeholder", "# Personas");
		$("#columna2").show("fast");
		$("#columna2").attr("type", "number");
		$("#observaciones_reserva").attr("placeholder", "Servicios Requeridos");

	} else if (tipo == "SolT3_refr") {
		//Si es de categoria refrigerios
		// columna1 -> Numero Persona
		// columna2 -> Cantidad Persona
		// columna3 -> Lugar Entrega
		// columna4 -> Nombre Contacto
		// columna5 -> Celular Contacto
		$("#columna1").attr("required", "true");
		$("#columna1").attr("placeholder", "#Personas");
		$("#columna1").show("fast");
		$("#columna1").attr("type", "number");
		$("#columna2").attr("required", "true");
		$("#columna2").attr("placeholder", "Cantidad x persona");
		$("#columna2").show("fast");
		$("#columna2").attr("type", "number");
		$("#columna3").attr("required", "true");
		$("#columna3").attr("placeholder", "Lugar Entrega");
		$("#columna3").show("fast");
		$("#columna4").attr("required", "true");
		$("#columna4").attr("placeholder", "Nombre Contacto");
		$("#columna4").show("fast");
		$("#columna5").attr("required", "true");
		$("#columna5").attr("placeholder", "Celular Contacto");
		$("#columna5").show("fast");
		$("#columna5").attr("type", "number");
		$("#tipo_refrigerios").show("fast");
		$("#proveedor").show("fast");
		$("#tipo_refrigerios").attr("required", "true");
		$("#proveedor").attr("required", "true");
		$("#fecha_Reserva").attr("placeholder", "Fecha Entrega");
	} else if (tipo == "SolT3_rest") {
		// Si es de categoria Restaurante
		// columna1 -> Numero Personas
		$("#columna1").attr("required", "true");
		$("#columna1").attr("placeholder", "#Personas");
		$("#columna1").show("fast");
		$("#columna1").attr("type", "number");

	} else if (tipo == "SolT3_memb") {
		// Si es de categoria Membrecia
		// columna1 -> Nombre Menbrecia
		// columna2 -> Link de pago
		// columna3 -> Usuario
		// columna4 -> contraseña
		// columna5 -> valor
		$("#columna1").attr("required", "true");
		$("#columna1").attr("placeholder", "Nombre Membrecia");
		$("#columna1").show("fast");

		$("#columna2").attr("required", "true");
		$("#columna2").attr("placeholder", "Link de pago");
		$("#columna2").show("fast");
		$("#columna2").attr("type", "url");

		$("#columna3").attr("required", "true");
		$("#columna3").attr("placeholder", "Usuario");
		$("#columna3").show("fast");

		$("#columna4").attr("required", "true");
		$("#columna4").attr("placeholder", "Contraseña");
		$("#columna4").show("fast");

		$("#columna5").attr("required", "true");
		$("#columna5").attr("placeholder", "Valor");
		$("#columna5").show("fast");
		$("#columna5").attr("type", "number");
		$("#fecha_reserva_div").css("display", "none");
		$("#fecha_Reserva").removeAttr("required", "true");
	} else if (tipo == "SolT3_reno") {
		// Si es de categoria renovacion
		// columna1 -> Nombre
		// columna2 -> Cotizacion
		// columna3 -> Valor

		$("#columna1").attr("required", "true");
		$("#columna1").attr("placeholder", "Nombre Renovacion");
		$("#columna1").show("fast");

		//        $("#columna2").attr("required", "true");
		//        $("#columna2").attr("placeholder", "Cotizacion");
		//        $("#columna2").show("fast");


		$("#columna3").attr("required", "true");
		$("#columna3").attr("placeholder", "Valor");
		$("#columna3").show("fast");
		$("#columna3").attr("type", "number");

	} else if (tipo == "SolT3_remb") {
		// Si es de categoria Membrecia
		// columna1 -> Nombre
		// columna2 -> link facturas cargadas
		var titulos = ["Adjuntar Facturas"];
		CrearAdjuntar(1, titulos);
	} else if (tipo == "SolT3_poli") {
		// Si es de categoria poliza
		// columna1 -> link archivos

		//  MostrarInformacionreserva(tipo);
		$("#fecha_reserva_div").css("display", "none");
		$("#fecha_Reserva").removeAttr("required", "true");
		$("#tipo-polizas").show("fast");
		$("#tipo-polizas").attr("required", "true");
	} else if (tipo == "SolT3_matr") {
		// Si es de categoria matriculas
		// columna1 -> link acuerdo
		// columna2 -> link recibo
		$("#codigo_sap_input_Reserva").removeAttr("required", "true");
		$("#codigo_sap_input_Reserva").css("display", "none");
		$("#re_viaticos_resereva").show("fast");
		$("#re_viaticos_resereva").attr("required", "true");
		var titulos = ["Adjuntar Acuerdo", "Adjuntar Recibo Pago"];
		CrearAdjuntar(2, titulos);
	} else if (tipo == "SolT3_otra") {
		var titulos = ["Adjuntar Archivo"];
		CrearAdjuntar(1, titulos);
	}

}

function CrearAdjuntar(n, titulos) {
	$("#adjuntos").html("");
	for (var iadjunto = 0; iadjunto < n; iadjunto++) {
		$("#adjuntos").append('<div class="agrupado"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span> Buscar <input name="archivo' + iadjunto + '" type="file" style="display: none;"></span></label><input type="text" class="form-control" readonly></div></div><div><p class = "">' + titulos[iadjunto] + '</p></div>');
	}
	//$("#adjuntos").append("<p class='text-left' ><b class= 'ttitulo'>Nota 3:</b> Solo se puede adjuntar un solo archivo(PDF, DOC, EXCEL, PNG etc), ademas es posible adjuntar carpetas comprimidas(7Z, RAR etc) que contengan varios archivos.</p>");

	activarfile();
}

function CrearAdjuntar_modi(n, titulos) {
	$("#adjuntos_reserva_modi").html("");
	for (var iadjunto = 0; iadjunto < n; iadjunto++) {
		$("#adjuntos_reserva_modi").append('<div class="agrupado"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span> Buscar  <input name="archivo' + iadjunto + '" type="file" style="display: none;"></span></label><input type="text" class="form-control" readonly></div></div><div><p class = "">' + titulos[iadjunto] + '</p></div>');
	}
	// $("#adjuntos_reserva_modi").append("<p class='text-left' ><b class= 'ttitulo'>Nota 3:</b> Solo se puede adjuntar un solo archivo(PDF, DOC, EXCEL, PNG etc), ademas es posible adjuntar carpetas comprimidas(7Z, RAR etc) que contengan varios archivos.</p>");

	activarfile();
}

function confirmar_modificar(tipo, mensaje) {
	swal({
		title: "Modificar Datos..?",
		text: mensaje,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Modificar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	}, function (isConfirm) {
		if (isConfirm) {
			if (tipo == 1) {

				Modificar_Solicitud(0);
			} else if (tipo == 2) {

				modificar_transporte();
			} else if (tipo == 3) {

				modificar_tiquetes_viaticos();
			} else if (tipo == 4) {
				Modificar_tipo3();
			}
		}
	});
}

function Guardar_Solicicitudes_tipo4() {
	var id_solicitud_n = "";
	MensajeConClase("validando info", "add_inv", "Oops...");
	var formData = new FormData(document.getElementById("Guardar_bodega"));
	if (id_solicitud_borrador != 0) {
		id_solicitud_n = id_solicitud_borrador;
	} else {
		id_solicitud_n = id_solicitud;
	}

	formData.append("id", id_solicitud_n);
	formData.append("solicitudADD", borrador_solicitud);
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Guardar_Solicicitudes_tipo4",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (data) {
		let datos = data[0];
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		}

		if (datos == 1) {
			MensajeConClase("Error al cargar el id de la solicitud, contacte con el administrador.!", "error", "Oops...");
			return true;
		} else if (datos == 2) {
			MensajeConClase("Error al cargar el tipo de solicitud, contacte con el administrador.!", "error", "Oops...");
			return true;
		} else if (datos == 3) {
			MensajeConClase("Ingrese Codigo SAP.!", "info", "Oops...");

			return true;
		} else if (datos == 4) {
			MensajeConClase("Seleccione Responsable.!", "info", "Oops...");
			return true;
		} else if (datos == 5) {
			MensajeConClase("Ingrese lugar de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 6) {
			MensajeConClase("Ingrese la fecha de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 7) {
			MensajeConClase("Ingrese la fecha de retiro.!", "info", "Oops...");
			return true;
		} else if (datos == 8) {

			confirmar_sin_codigo_sap(8);
			return true;
		} else if (datos == 9) {
			MensajeConClase("La fecha de entrega no puede ser menor que la fecha actual.!", "info", "Oops...");
			return true;
		} else if (datos == 10) {
			MensajeConClase("La fecha de retiro no puede ser inferior a la fecha de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 11) {
			MensajeConClase("Ingrese Numero de manteles.!", "info", "Oops...");
			return true;
		} else if (datos == 12) {
			MensajeConClase("El numero de manteles debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 13) {
			MensajeConClase("Ingrese Numero de sillas.!", "info", "Oops...");
			return true;
		} else if (datos == 14) {
			MensajeConClase("El numero de sillas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 15) {
			MensajeConClase("Ingrese Numero de carpas.!", "info", "Oops...");
			return true;
		} else if (datos == 16) {
			MensajeConClase("El numero de carpas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 17) {
			MensajeConClase("Ingrese Numero de vasos.!", "info", "Oops...");
			return true;
		} else if (datos == 18) {
			MensajeConClase("El numero de vasos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 19) {
			MensajeConClase("Ingrese Numero de tenedores.!", "info", "Oops...");
			return true;
		} else if (datos == 20) {
			MensajeConClase("El numero de tenedores debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 21) {
			MensajeConClase("Ingrese Numero de cuchillos.!", "info", "Oops...");
			return true;
		} else if (datos == 22) {
			MensajeConClase("El numero de cuchillos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 23) {
			MensajeConClase("Seleccione tipo de mesas.!", "info", "Oops...");
			return true;
		} else if (datos == 24) {
			MensajeConClase("Ingrese Numero de mesas.!", "info", "Oops...");
			return true;
		} else if (datos == 25) {
			MensajeConClase("El numero de mesas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 26) {
			MensajeConClase("Seleccione tipo de Cucharas.!", "info", "Oops...");
			return true;
		} else if (datos == 27) {
			MensajeConClase("Ingrese Numero de Cucharas.!", "info", "Oops...");
			return true;
		} else if (datos == 28) {
			MensajeConClase("El numero de Cucharas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 29) {
			MensajeConClase("Seleccione tipo de Platos.!", "info", "Oops...");
			return true;
		} else if (datos == 30) {
			MensajeConClase("Ingrese Numero de Platos.!", "info", "Oops...");
			return true;
		} else if (datos == 31) {
			MensajeConClase("El numero de Platos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 32) {
			MensajeConClase("Seleccione Numero de personas!", "info", "Oops...");
			return true;
		} else if (datos == 33) {
			MensajeConClase("Ingrese el valor de las Flores!", "info", "Oops...");
			return true;
		} else if (datos == 38) {
			MensajeConClase("Seleccione Categoria!", "info", "Oops...");
			return true;
		} else if (datos == 33) {
			MensajeConClase("Ingrese el valor de las Flores!", "info", "Oops...");
			return true;
		} else if (datos == 34) {
			MensajeConClase("Seleccione tipo de refrigerios!", "info", "Oops...");
			return true;
		} else if (datos == 35) {
			MensajeConClase("Ingrese cantidad de refrigerios por persona!", "info", "Oops...");
			return true;
		} else if (datos == 36) {
			MensajeConClase("Seleccione el tipo de entrega de los refrigerios!", "info", "Oops...");
			return true;
		} else if (datos == 37) {
			MensajeConClase("Seleccione el tipo de entrega del cafe y el agua!", "info", "Oops...");
			return true;
		} else if (datos == 0) {

			if (borrador_solicitud.length != 0) {
				Listar_solciitudes();
				var ser = '<a href="' + server + 'index.php/solicitudesADM"><b>AQUI</b></a>'
				mensaje = "Su solicitud " + nombre_evento + " fue ingresada con exito puede validar la informaci&oacuten " + ser;
				enviar_correo_personalizado("adm", mensaje, correo_solicitante, solicitante, "Solicitud Administrativa CUC", "SSolicitud Administrativa", "ParCodAdm", -1);
			} else {
				Listar_detalle_pedidos_id(id_solicitud);
			}
			borrador_solicitud = [];
			id_solicitud_borrador = data[1];
			MensajeConClase("Los datos para su solicitud " + nombre_evento + " fueron registrados con exito.", "success", "Proceso Exitoso!");
			reiniciar_form_tipo4();
			$("#Guardar_solicitud_general").get(0).reset();
			//$("#Modal-add-bodega").modal("hide");
			$(".datos-tipo-4").show("fast");
			asignando_t4 = 0;
			con_requ = 0;
			return true;
		}

	});

}

function Modificar_Solicicitudes_tipo4() {

	var formData = new FormData(document.getElementById("Modificar_bodega"));
	formData.append("id", id_modificando_t4);
	$.ajax({
		url: server + "index.php/solicitudes_adm_control/Modificar_Solicicitudes_tipo4",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "info", "Oops...")
			return true;
		}

		if (datos == 1) {
			MensajeConClase("Error al cargar el id de la solicitud, contacte con el administrador.!", "error", "Oops...");
			return true;
		} else if (datos == 3) {
			MensajeConClase("Ingrese Codigo SAP.!", "info", "Oops...");
			return true;
		} else if (datos == 4) {
			MensajeConClase("Seleccione Responsable.!", "info", "Oops...");
			return true;
		} else if (datos == 5) {
			MensajeConClase("Ingrese lugar de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 6) {
			MensajeConClase("Ingrese la fecha de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 7) {
			MensajeConClase("Ingrese la fecha de retiro.!", "info", "Oops...");
			return true;
		} else if (datos == 8) {
			confirmar_sin_codigo_sap(9);
			return true;
		} else if (datos == 9) {
			MensajeConClase("La fecha de entrega no puede ser menor que la fecha actual.!", "info", "Oops...");
			return true;
		} else if (datos == 10) {
			MensajeConClase("La fecha de retiro no puede ser inferior a la fecha de entrega.!", "info", "Oops...");
			return true;
		} else if (datos == 11) {
			MensajeConClase("Ingrese Numero de manteles.!", "info", "Oops...");
			return true;
		} else if (datos == 12) {
			MensajeConClase("El numero de manteles debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 13) {
			MensajeConClase("Ingrese Numero de sillas.!", "info", "Oops...");
			return true;
		} else if (datos == 14) {
			MensajeConClase("El numero de sillas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 15) {
			MensajeConClase("Ingrese Numero de carpas.!", "info", "Oops...");
			return true;
		} else if (datos == 16) {
			MensajeConClase("El numero de carpas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 17) {
			MensajeConClase("Ingrese Numero de vasos.!", "info", "Oops...");
			return true;
		} else if (datos == 18) {
			MensajeConClase("El numero de vasos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 19) {
			MensajeConClase("Ingrese Numero de tenedores.!", "info", "Oops...");
			return true;
		} else if (datos == 20) {
			MensajeConClase("El numero de tenedores debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 21) {
			MensajeConClase("Ingrese Numero de cuchillos.!", "info", "Oops...");
			return true;
		} else if (datos == 22) {
			MensajeConClase("El numero de cuchillos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 23) {
			MensajeConClase("Seleccione tipo de mesas.!", "info", "Oops...");
			return true;
		} else if (datos == 24) {
			MensajeConClase("Ingrese Numero de mesas.!", "info", "Oops...");
			return true;
		} else if (datos == 25) {
			MensajeConClase("El numero de mesas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 26) {
			MensajeConClase("Seleccione tipo de Cucharas.!", "info", "Oops...");
			return true;
		} else if (datos == 27) {
			MensajeConClase("Ingrese Numero de Cucharas.!", "info", "Oops...");
			return true;
		} else if (datos == 28) {
			MensajeConClase("El numero de Cucharas debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 29) {
			MensajeConClase("Seleccione tipo de Platos.!", "info", "Oops...");
			return true;
		} else if (datos == 30) {
			MensajeConClase("Ingrese Numero de Platos.!", "info", "Oops...");
			return true;
		} else if (datos == 31) {
			MensajeConClase("El numero de Platos debe ser mayor o igual a 1.!", "info", "Oops...");
			return true;
		} else if (datos == 32) {
			MensajeConClase("Seleccione Numero de personas!", "info", "Oops...");
			return true;
		} else if (datos == 33) {
			MensajeConClase("Ingrese el valor de las Flores!", "info", "Oops...");
			return true;
		} else if (datos == 34) {
			MensajeConClase("Seleccione tipo de refrigerios!", "info", "Oops...");
			return true;
		} else if (datos == 35) {
			MensajeConClase("Ingrese cantidad de refrigerios por persona!", "info", "Oops...");
			return true;
		} else if (datos == 36) {
			MensajeConClase("Seleccione el tipo de entrega de los refrigerios!", "info", "Oops...");
			return true;
		} else if (datos == 37) {
			MensajeConClase("Seleccione el tipo de entrega del cafe y el agua!", "info", "Oops...");
			return true;
		} else if (datos == 38) {
			MensajeConClase("Seleccione Categoria!", "info", "Oops...");
			return true;
		} else if (datos == 0) {
			MensajeConClase("El detalle del arriendo y bodega fue  Modificado con exito.!", "success", "Proceso Exitoso.!");
			$("#Modificar_bodega").get(0).reset();
			asignando_t4 = 0;
			id_modificando_t4 = 0;
			$("#Modal-modificarbodega").modal("hide");
			Listar_detalle_pedidos_id(id_solicitud);
			return true;
		}

	});

}


function Configurando_modificar_bodega() {

	con_requ_modi = 0;
	if ($("#re_manteles_modi").is(':checked')) {
		$(".inp_manteles_modi").show("slow");
		$(".inp_manteles_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_manteles_modi").hide("slow");
		$(".inp_manteles_modi").val('');
		$(".inp_manteles_modi").removeAttr("required", "true");

	}


	if ($("#re_sillas_modi").is(':checked')) {
		$(".inp_sillas_modi").show("slow");
		$(".inp_sillas_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_sillas_modi").hide("slow");
		$(".inp_sillas_modi").val('');
		$(".inp_sillas_modi").removeAttr("required", "true");

	}


	if ($("#re_carpas_modi").is(':checked')) {
		$(".inp_carpas_modi").show("slow");
		$(".inp_carpas_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_carpas_modi").hide("slow");
		$(".inp_carpas_modi").val('');
		$(".inp_carpas_modi").removeAttr("required", "true");

	}

	if ($("#re_vasos_modi").is(':checked')) {
		$(".inp_vasos_modi").show("slow");
		$(".inp_vasos_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_vasos_modi").hide("slow");
		$(".inp_vasos_modi").val('');
		$(".inp_vasos_modi").removeAttr("required", "true");

	}



	if ($("#re_tenedores_modi").is(':checked')) {
		$(".inp_tenedores_modi").show("slow");
		$(".inp_tenedores_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_tenedores_modi").hide("slow");
		$(".inp_tenedores_modi").val('');
		$(".inp_tenedores_modi").removeAttr("required", "true");

	}


	if ($("#re_mesas_modi").is(':checked')) {
		$(".inp_mesas_modi").show("slow");
		$(".inp_mesas_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_mesas_modi").hide("slow");
		$(".inp_mesas_modi").val('');
		$(".inp_mesas_modi").removeAttr("required", "true");

	}



	if ($("#re_cucharas_modi").is(':checked')) {
		$(".inp_cucharas_modi").show("slow");
		$(".inp_cucharas_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_cucharas_modi").hide("slow");
		$(".inp_cucharas_modi").val('');
		$(".inp_cucharas_modi").removeAttr("required", "true");

	}


	if ($("#re_platos_modi").is(':checked')) {
		$(".inp_platos_modi").show("slow");
		$(".inp_platos_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_platos_modi").hide("slow");
		$(".inp_platos_modi").val('');
		$(".inp_platos_modi").removeAttr("required", "true");

	}


	if ($("#re_cuchillos_modi").is(':checked')) {
		$(".inp_cuchillos_modi").show("slow");
		$(".inp_cuchillos_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_cuchillos_modi").hide("slow");
		$(".inp_cuchillos_modi").val('');
		$(".inp_cuchillos_modi").removeAttr("required", "true");

	}

	if ($("#re_flores_modi").is(':checked')) {
		$(".inp_flores_modi").show("slow");
		$(".inp_flores_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_flores_modi").hide("slow");
		$(".inp_flores_modi").val('');
		$(".inp_flores_modi").removeAttr("required", "true");
		con_requ_modi--;
	}

	if ($("#re_refri_modi").is(':checked')) {
		$(".inp_refrigerios_modi").show("slow");
		$(".inp_refrigerios_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_refrigerios_modi").hide("slow");
		$(".inp_refrigerios_modi").val('');
		$(".inp_refrigerios_modi").removeAttr("required", "true");
		con_requ_modi--;
	}

	if ($("#re_agua_modi").is(':checked')) {
		$(".inp_cafe_agua_modi").show("slow");
		$(".inp_cafe_agua_modi").attr("required", "true");
		con_requ_modi++;
	} else {
		$(".inp_cafe_agua_modi").hide("slow");
		$(".inp_cafe_agua_modi").val('');
		$(".inp_cafe_agua_modi").removeAttr("required", "true");
		con_requ_modi--;
	}




}

function Cargar_informacion_menu() {

	for (var i = 0; i <= datos_solicitud.length - 1; i++) {

		if (datos_solicitud[i].id_aux == "SolT1") {

			$("#titulo_viajes").html(datos_solicitud[i].valor);
			$("#solt1 span").attr("data-content", datos_solicitud[i].valorx);

		} else if (datos_solicitud[i].id_aux == "SolT2") {
			$("#titulo_transporte").html(datos_solicitud[i].valor);
			$("#solt2 span").attr("data-content", datos_solicitud[i].valorx);

		} else if (datos_solicitud[i].id_aux == "SolT3") {
			$("#titulo_otras").html(datos_solicitud[i].valor);
			$("#solt3 span").attr("data-content", datos_solicitud[i].valorx);

		} else if (datos_solicitud[i].id_aux == "SolT4") {
			$("#titulo_logistica").html(datos_solicitud[i].valor);
			$("#solt4 span").attr("data-content", datos_solicitud[i].valorx);

		}



	}

}

function configurar_form_solicitud(tipo, titulo) {
	//iniciando_solicitud();
	borrador_solicitud = [];
	tipo_gen = tipo;
	$("#div_req_inscripcion").show("fast");
	$("#fecha_inicio_evento_div").show("fast");
	$("#fecha_final_evento_div").show("fast");

	//	$("#fecha_inicio_evento").val("");
	//	$("#fecha_final_evento").val("");
	if (tipo_gen == "SolT1") {
		//$("#cbx_tipo_evento").val("");
		$("#cbx_tipo_evento").show("fast");
	} else {
		$("#cbx_tipo_evento").val("Even_Nac");
		tipo_evento_gen = "Even_Nac";
		$("#cbx_tipo_evento").hide("fast");
	}
	/*if (tipo_gen == "SolT4" || tipo_gen == "SolT2") {
		$("#fecha_final_evento_div").show("fast");
		$("#fecha_final_evento_div input").attr("required", "true");
	} else {
		$("#fecha_final_evento_div").hide("fast");
		$("#fecha_final_evento_div input").removeAttr("required", "true");
	}*/

	$("#nombre_solicitud").html(titulo);
	$("#Modal-add-via").modal("show");
}

function iniciando_solicitud() {

	$("#panel_info_evento").show("fast");

	//$("#requiere_inscrip").hide("fast");
	//$("#Guardar_solicitud_general").get(0).reset();
	//$("#cbx_tipo_solicitud").removeAttr("disabled");
	//$("#cbx_tipo_evento").removeAttr("disabled");
	//$("#cbx_tipo_evento").val("Even_Nac");
	//	tipo_evento_gen = "Even_Nac";
	$("#cbx_tipo_evento").hide("fast");
	//$("#panel-add-reserva .requerido").removeAttr("required", "true");
	//$("#panel-add-reserva").hide("fast");
	//$("#div_req_inscripcion").show("fast");
	$("#fecha_inicio_evento_div").show("fast");
	$("#fecha_final_evento_div").show("fast");
	$("#Modal-add-via").modal("show");
}

function reiniciar_form_tipo4() {
	$(".requerimientos_tipo4").hide("fast");
	$("#Guardar_bodega").get(0).reset();
	$("#persona_responsable_bodega").html("Seleccione Responsable");
	$("#campos_for_t4 .oculto").hide("slow");
	$(".inp_manteles").removeAttr("required", "true");
	$(".inp_sillas").removeAttr("required", "true");
	$(".inp_carpas").removeAttr("required", "true");
	$(".inp_vasos").removeAttr("required", "true");
	$(".inp_tenedores").removeAttr("required", "true");
	$(".inp_mesas").removeAttr("required", "true");
	$(".inp_cucharas").removeAttr("required", "true");
	$(".inp_platos").removeAttr("required", "true");
	$(".inp_cuchillos").removeAttr("required", "true");
	$(".inp_flores").removeAttr("required", "true");
	$(".inp_refrigerios").removeAttr("required", "true");
	$(".inp_cafe_agua").removeAttr("required", "true");
}

function reiniciar_form_tipo4_modi() {
	$("#Modificar_bodega").get(0).reset();
	$("#persona_responsable_bodega_modi").html("Seleccione Responsable");
	$("#campos_for_t4_modi .oculto").hide("slow");
	$(".inp_manteles").removeAttr("required", "true");
	$(".inp_sillas").removeAttr("required", "true");
	$(".inp_carpas").removeAttr("required", "true");
	$(".inp_vasos").removeAttr("required", "true");
	$(".inp_tenedores").removeAttr("required", "true");
	$(".inp_mesas").removeAttr("required", "true");
	$(".inp_cucharas").removeAttr("required", "true");
	$(".inp_platos").removeAttr("required", "true");
	$(".inp_cuchillos").removeAttr("required", "true");
	$(".inp_flores").removeAttr("required", "true");
	$(".inp_refrigerios").removeAttr("required", "true");
	$(".inp_cafe_agua").removeAttr("required", "true");
}

function MensajeFueraFechaeWarning(titulo, mensaje, tipo) {
	swal({
		title: titulo,
		text: mensaje,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				if (tipo == 1) {
					Guardar_Solicitud(1);
					return;
				} else if (tipo == 2) {
					Modificar_Solicitud(1);
				}
			}
		});
}

function Pasar_valor_limite_dias(valor) {
	limite_dias = valor;
}

function Config_fort4_tipo(tipo) {
	reiniciar_form_tipo4();
	if (tipo == "Log_Hot") {
		$("#to_eventos").hide("fast");
		$(".requerimientos_tipo4").show("fast");
	} else if (tipo == "Log_Res") {
		$(".requerimientos_tipo4").hide("fast");

	} else if (tipo == "Log_Eve") {
		$("#to_eventos").show("fast");
		$(".requerimientos_tipo4").show("fast");
	} else {
		$(".requerimientos_tipo4").hide("fast");
	}
}

function Config_fort4_tipo_modi(tipo) {
	reiniciar_form_tipo4_modi();
	if (tipo == "Log_Hot") {
		$("#to_eventos_modi").hide("fast");
		$(".requerimientos_tipo4_modi").show("fast");
	} else if (tipo == "Log_Res") {
		$(".requerimientos_tipo4_modi").hide("fast");

	} else if (tipo == "Log_Eve") {
		$("#to_eventos_modi").show("fast");
		$(".requerimientos_tipo4_modi").show("fast");
	} else {
		$(".requerimientos_tipo4_modi").hide("fast");
	}
}

function multiples_destinos(con) {
	if (con == 1) {
		$("#guardar_mas_datos").show("slow");
		$("#guardar_fin_datos").hide("slow");
		$("#destinos").hide("slow");
		$("#datos_destino").show("slow");
		mas_destino = 1;
	} else {
		Reiniciar_Tabla();
		//$("#Guardar_Itinerario").get(0).reset();
		$(".ocultar_adjunto").hide("fast");
		$("#persona_solicita_tiquetes").html("Seleccione Participante");
		$("#requiere_tiquetes").hide("fast");
		$("#requiere_hoteles").hide("fast");
		$("#requiere_hoteles input").removeAttr("required", "true");
		$("#requiere_tiquetes input").removeAttr("required", "true");
		$("#regresar_multip").hide("slow");
		$("#datos_destino").show("slow");
		$("#destinos").show("slow");
		$("#guardar_fin_datos").show("slow");
		$("#guardar_mas_datos").hide("slow");
		mas_destino = 0;
		$("#nombre_itine p").html("Datos del Itinerario");

	}
}


const listar_archivos_adjuntos = (id, tipo = 2) => {
	$('#tabla_adjuntos_comunicaciones tbody').off('click', 'tr .eliminar').off('click', 'tr .remover').off('click', 'tr .seleccionar').off('click', 'tr');
	consulta_ajax(`${url}listar_archivos_adjuntos`, { id }, (resp) => {
		const table = $("#tabla_adjuntos_comunicaciones").DataTable({
			"destroy": true,
			"processing": true,
			'data': resp,
			"columns": [
				{
					"render": function (data, type, full, meta) {
						return "<a class='sin-decoration ' href='" + Traer_Server() + 'archivos_adjuntos/comunicaciones/solicitudes/' + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
					}
				},
				{
					"data": "nombre_real"
				},
				{
					"data": "fecha_registro"
				},
				{
					"data": "solicitante"
				},
				{

					"render": function (data, type, full, meta) {
						let { estado_solicitud, id } = full;
						let sw = true;
						let resp = '<span class="fa fa-toggle-off btn "></span>';
						if (tipo == 1) {
							if (enviar_adjuntos.length != 0) {
								let add = enviar_adjuntos.find((key) => { return key.id == id; });
								if (add) resp = '<span class="fa fa-check-square-o red btn btn-default pointer" style="background-color:#eee" disabled="disabled"></span> <span class="fa fa-remove btn btn-default remover" style="color:#cc0000"></span>';
								else resp = '<span class="fa fa-check-square-o red btn btn-default pointer seleccionar"></span> <span class="fa fa-remove btn btn-default" style="color:#cc0000; background-color:#eee" disabled="disabled"></span>';
							} else {
								resp = '<span class="fa fa-check-square-o red btn btn-default pointer seleccionar"></span> <span class="fa fa-remove btn btn-default" style="color:#cc0000; background-color:#eee" disabled="disabled"></span>';
							}

						} else if (estado_solicitud == 'Com_Sol_E' && tipo == 2) resp = '<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
						return resp;
					}
				},
			],
			"language": idioma,
			dom: 'Bfrtip',
			"buttons": []

		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tabla_adjuntos_comunicaciones tbody').on('click', 'tr', function () {
			$("#tabla_adjuntos_comunicaciones tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_adjuntos_comunicaciones tbody').on('click', 'tr .eliminar', function () {
			let { id } = table.row($(this).parent().parent()).data();
			eliminar_adjunto_solicitud(id);
		});
		$('#tabla_adjuntos_comunicaciones tbody').on('click', 'tr .seleccionar', function () {
			let data = table.row($(this).parent().parent()).data();
			enviar_adjuntos.push(data);
			let pintar = '<span class="fa fa-check-square-o red btn btn-default pointer" style="background-color:#eee" disabled="disabled"></span> <span class="fa fa-remove btn btn-default remover" style="color:#cc0000"></span>';
			$(this).parent().html(pintar);
		});
		$('#tabla_adjuntos_comunicaciones tbody').on('click', 'tr .remover', function () {
			let data = table.row($(this).parent().parent()).data();
			let pintar = '<span class="fa fa-check-square-o red btn btn-default pointer seleccionar"></span> <span class="fa fa-remove btn btn-default" style="color:#cc0000; background-color:#eee" disabled="disabled"></span>';
			$(this).parent().html(pintar);
			enviar_adjuntos.forEach((key, indice) => {
				if (key.id == data.id) {
					enviar_adjuntos.splice(indice, 1);
					return;
				}
			});

		});

	});

}


function documentos(){
	let clasificacion=$("#Guardar_solicitud_general select[name='tipo_calificacion']").val();
	let so = `${Traer_Server()}index.php/solicitudes_adm_control/`;
	consulta_ajax(`${so}documentos_adm`,{clasificacion}, (data) => {
		$("#adjuntar_ev_adm").html("");
		if(data['nombre_tipo']){
			$("#adjuntar_ev_adm").append(`
				<div class="agrupado">
				<div class="input-group ">
				<label class="input-group-btn">
				<span class="btn btn-primary">
				<span class="fa fa-folder-open"> </span>Buscar
				<input name="archivootro" type="file" style="display: none;" id="archivootro">
				</span> 
				</label>
				<input type="text" class="form-control" readonly placeholder="Adjuntar ${data['nombre_tipo']}" >
				</div
				</div>

			`);
		}else{
			$("#adjuntar_ev_adm").html("");
		}
	});
}




