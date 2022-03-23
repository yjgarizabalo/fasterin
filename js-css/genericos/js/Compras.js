let en_notificacion = 0;
let data_noti = [];
let data_comite = { };
let id_coment = 0;
let id_comite_dire = 0;
let id_articulo_add_soli = 0;
let id_ver_comentarios = 0;
let tipo_cargue = 0;
let directivos_correos = [];
let datos_gestion = { };
let id_archivos = 0;
let num_archivos = 0;
let myDropzone = 0;
let modificando_ini = 0;
let id_articulo_sele_ini = -1;
let soli_usu_sele = 0;
let estados_faltantes_usu = [];
let estados_exceptuadas = [];
let solicitudes_exceptuadas = [];
let solicitudes_faltantes_usu = [];
let id_sol_comi = 0;
let idproveedor_sele_sol = 0;
let idsolicitud_alt = 0;
let conceptos = 0;
let conceptos_modi = 0;
let id_articulo_sele = "";
let id_com_modi = "s";
let idproveedor = 0;
let idpregunta = 0;
let usuario_selecc = 0;
let ruta_adjunto_solicitudes = "archivos_adjuntos/compras/solicitudes/";
let ruta_adjunto_proveedores = "archivos_adjuntos/compras/proveedores/";
let modificando = 0;
let estados_disponibles = [];
let estado_actual = "";
let guardar;
let articulo;
let correo_soli;
let articulos_sele = [];
let idsolicitud = 0;
let idarticulo = 0;
var server = "localhost";
let modificando_articulo = 0;
let responsable_sele = "";
let tipo_sol_sele = "";
let tipo_estado_sele = "";
let actual_pre = 1;
let errores = [];
let cargados = 0;
let id_usuario_sol = 0;
let id_usuario_sol_noti = 0;
let modulo_noti = 1;
let con_tarjeta = 0;
let causal = 0;
let id_pjefe = 0;
let correo_jefe = null;
let ruta_generica = "";
let ruta_mod_parametros = "";
let clasificacion_area_selected = "";
let ruta_interna = "";
let lugar_activo = "";
let tipo_enc_selected = "";
let idCurrentSoli = "";
let int_id_user = "";
let persona_busc = "";
let id_persona_selected = "";
let id_criterio = "";
let form_activo = "";
let idps = "";
let idSolicitud = "";
let criterio_selected = "";
let massives = [];
let tipo_orden_sel = '';
let encuesta_activa = '';
let massive_idsols = '';
let datos_selected = [];

$(document).ready(function () {
	server = Traer_Server();
	ruta_interna = `${Traer_Server()}index.php/compras_control/`;
	ruta_generica = `${Traer_Server()}index.php/genericas_control/Cargar_valor_Parametros/true`;
	ruta_mod_parametros = `${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`;
	$("#GuardarProveedor").submit(function () {
		registrar_proveedor();
		return false;
	});

	$("#listado").click(function () {
		Listar_solicitudes();
	});

	/* Eventos para el agregar los inputs */
	$("#incremento").click(function () {
		let num = $("#nums_inputs").val();
		num++;
		$("#nums_inputs").val(num);
		$("#preguntas_container").append(`
		<input type="text" class="form-control" name="pregunta[]" value="" data-id="preg" data-item="${num}" placeholder="Escriba la pregunta aqui!">
		<hr data-item="${num}">
		`);
	});

	let last_num = "";
	$("#nums_inputs").blur(function () {
		let num = $("#nums_inputs").val();
		if (num > 0) {
			last_num = num;
		}

		let conteo = 0;
		$(`input[data-id="preg"]`).each(function () {
			conteo++;
		});

		if (num == 0) {
			conteo = 0;
			$("#nums_inputs").val(last_num);
			return false;
		} else if (num > conteo) {

			for (let x = conteo; x < num; x++) {
				$("#preguntas_container").append(`
					<input type="text" class="form-control" name="pregunta[]" value="" data-item="${x + 1}" data-id="preg" placeholder="Escriba la pregunta aqui!">
					<hr data-item="${x + 1}">
				`);
			}
			conteo = 0;
			$(`input[data-id="preg"]`).each(function () {
				conteo++;
			});
		} else if (num < conteo) {

			for (let x = conteo; x > num; x--) {
				$(`input[data-item="${x}"]`).remove();
				$(`hr[data-item="${x}"]`).remove();
			}
			conteo = 0;
			$(`input[data-id="preg"]`).each(function () {
				conteo++;
			});
		}

		conteo = 0;
		$(`input[data-id="preg"]`).each(function () {
			conteo++;
		});

	});

	$("#decremento").click(function () {
		let num = $("#nums_inputs").val();
		if (num < 1) {
			return false;
		} else {
			if (num == 1) {
				return false;
			} else {
				$(`input[data-item='${num}']`).remove();
				$(`select[data-item='${num}']`).remove();
				$(`hr[data-item='${num}']`).remove();
			}
			num--;
			$("#nums_inputs").val(num);
		}
	});
	/* Fin de enevento de numeros de inputs */

	/* Evento para filtrar promedios totales de proveedores segun fecha */
	$("#form_promedios_provs").submit(function () {
		let datos = $(this).serializeJSON();
		promediar_proveedores(datos);
		if (datos != "" || datos != undefined) {
			$("#tabla_promedios_proveedores .mensaje-filtro").show("fast");
		} else {
			$("#tabla_promedios_proveedores .mensaje-filtro").css("display", "none");
		}
		return false;
	});

	$("#clear_results").click(function () {
		$("#tabla_promedios_proveedores .mensaje-filtro").css("display", "none");
		$("#form_promedios_provs").trigger('reset');
		$("#alerta_faltantes #provs_restantes_conatiner").html(``);
		$("#alerta_faltantes").addClass('oculto');
		promediar_proveedores();
	});

	/* Eventos para form de modificar o agregar valor_parametro */
	$("#form_valor_parametro").submit(function () {
		let datos = $(this).serializeJSON();

		if (form_activo == "upd") {
			let crit_id = $(`#modal_valor_parametro #valor`).attr("data-id");
			upd_valorp(datos, crit_id);
		} else if (form_activo == "add") {
			add_valorp(datos);
		}

		return false;
	});

	/* Evento para formulario de actualizacion de porcentajes - Apartado de poderaciones */
	$("#form_valor_porcentaje").submit(function () {
		let id_procentaje_selcted = $("#form_valor_porcentaje #valor_ini").attr("data-id");
		let datos = $(this).serializeJSON();
		if (form_activo == "upd") {
			upd_porcentajes(datos, id_procentaje_selcted);
		} else if (form_activo == "add") {
			create_porcentajes(datos);
		}
		return false;
	});

	/* Evento para buscar los usuarios en apartado de permisos */
	$("#buscar_usuario").click(function () {
		let persona_buscada = $("#txtusuario_buscar").val();
		listar_personas_compras(persona_buscada);
	});

	$("#txtusuario_buscar").on("keydown", function (e) {
		if (e.keyCode == 13) {
			let persona_buscada = $("#txtusuario_buscar").val();
			listar_personas_compras(persona_buscada);
		}
	});

	/* Evento para buscar los usuarios que se les va a asignar una encuesta */
	$("#buscar_user").click(function () {
		persona_busc = "";
		let persona_buscada = $("#txtsearch_user").val();
		persona_busc = persona_buscada;
		listar_personas_rp(persona_buscada);
	});

	$("#txtsearch_user").on("keydown", function (e) {
		if (e.keyCode == 13) {
			let persona_buscada = $("#txtsearch_user").val();
			persona_busc = persona_buscada;
			listar_personas_rp(persona_buscada);
		}
	});

	/* Filtro de proveedores segun fecha */
	$("#form_prov_filter").submit(function () {
		let fd = $(`#form_prov_filter input[name="fecha_desde"]`).val();
		let fh = $(`#form_prov_filter input[name="fecha_hasta"]`).val();
		$("#modal_filtrar_proveedores").modal('hide');
		$("#modal_prov_list").modal();
		listar_proveedores_filtrados(fd, fh);
		return false;
	});

	/* Eventos cuando se seleccione la clasificacion de pro y la seleccion de area */
	$("#clasi_proveedor").change(async function () {
		let clasi = await find_idParametro('critico_alto');
		let valor = $(this).val();
		clasificacion_area_selected = valor;
		if (valor != clasi.id) {
			$("#seleccion_area").addClass('oculto').attr('required', false).attr('disabled', true);
		} else {
			$("#seleccion_area").removeClass('oculto').attr('required', true).attr('disabled', false);
		}
	});

	/* Evento para cuando se vayan a relizar los masivos */
	$("#do_massives").click(function () {
		let current_val = '';
		let proveedor = '';
		$("#modal_masivos_compras .filtro_msg").addClass('oculto');
		listar_catego_rp(massive_idsols);
		massives_rp();
		$("#modal_masivos_compras").modal();
		$("#modal_masivos_compras #massives_encs").off('change');
		$("#modal_masivos_compras #massives_encs").change(async function () {
			current_val = $(this).val();
			encuesta_activa = current_val;
			current_val != '' ? $("#modal_masivos_compras .filtro_msg").removeClass('oculto') : $("#modal_masivos_compras .filtro_msg").addClass('oculto');
			massives_rp(current_val)
		});

		$("#modal_masivos_compras #massives_prov").change(async function () {
			proveedor = $(this).val();
			massives_rp(current_val, proveedor);
		});
	});

	$("#form_masivos").submit(function () {
		let ids_array = [];
		massive_idsols = '';

		if (massives.length != 0) {
			massives.forEach(element => {
				ids_array.push(element.id);
			});

			massive_idsols = massives[0].id;
			idCurrentSoli = ids_array;
			preguntas_rp(massives[0].enc_type);
		} else {
			swal({
				title: "¡Sin encuestas!",
				text: "Actualmente, no posee encuestas pendientes por realizar.",
				type: "info",
				confirmButtonColor: "#7DA8F0",
				confirmButtonText: "Si, comprendo",
				allowOutsideClick: true,
				closeOnConfirm: false,
			},
				function (isConfirm) {
					if (isConfirm) {
						swal.close();
					}
				});
		}

		return false;
	});

	/* Fin de eventos de seleccion */
	$("#detalle_persona_solicita").click(function () {
		obtener_datos_persona_id_completo(id_usuario_sol, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});
	$("#detalle_persona_solicita_noti").click(function () {
		obtener_datos_persona_id_completo(id_usuario_sol_noti, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});

	$("#listado").click(function () {
		$("#menu_principal").css("display", "none");
		$(".listado_solicitudes").fadeIn(1000);
	});
	$("#btn_responder_comentario").click(function () {
		let id = idsolicitud == 0 ? id_sol_comi : idsolicitud;
		if (en_notificacion != 0) {
			id_coment = data_noti[1];
			id = data_noti[0];
		}
		responder_preguntas(id_coment, id);
	});

	$("#btn_imprimir_acta").click(() => {
		crear_acta_compra(data_comite);
	});


	$("#ver_adjuntos_lista").click(function () {
		listar_archivo_compra(idsolicitud);
		$("#Modal_listar_archivos_adjuntos").modal("show");
	});

	$("#ver_cronogramas_lista").click(function () {
		compra_crono(true);
	});

	$("#ver_adjuntos_lista_noti").click(function () {
		listar_archivo_compra(data_noti[0], '#tabla_adjuntos_compras_noti');
		$("#Modal_listar_archivos_adjuntos_noti").modal("show");
	});
	$("#editar_cod_orden").click(function () {
		swal({
			title: "Modificar #Orden .?",
			text: "Si desea continuar con el proceso de modificación, por favor ingrese el numero de orden y presionar la opción de 'Continuar'",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Continuar!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: "Ingrese #Orden"
		}, function (codigo) {

			if (codigo === false)
				return false;
			if (codigo === "") {
				swal.showInputError("Debe Ingresar el #Orden..!");
				return false;
			} else {
				modificar_cod_orden_solicitud(idsolicitud, codigo);
				return false;
			}
		});
	});
	$("#editar_tiempo_entrega").click(function () {
		swal({
			title: "Modificar días de Entrega .?",
			text: "Si desea continuar con el proceso de modificación, por favor ingrese el numero de días y presionar la opción de 'Continuar'",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Continuar!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: "Ingrese #Días"
		}, function (dias) {

			if (dias === false)
				return false;
			if (dias === "") {
				swal.showInputError("Debe Ingresar el #Días..!");
				return false;
			} else {
				modificar_fecha_entrega_est(idsolicitud, dias);
				return false;
			}
		});
	});
	$(".cerrar_comentarios").click(function () {
		$(".fixed-comentarios").hide("slow");
	});
	$("#ver_notificaciones").click(function () {
		mostrar_notificaciones(modulo_noti);
		$("#modal_notificaciones_compras").modal("show");
	});
	$(".ver_comentarios").click(function () {
		$(".fixed-comentarios").show("slow");
	});

	$("#ver_adjuntos_lista_comite").click(function () {
		listar_archivo_compra(id_sol_comi);
	});
	$(".ver_compra_comite").on("click", () => {
		traer_solicitud(id_sol_comi, -1, 1);
		$("#modal_detalle_solicitud_noti").modal("show");
	});

	$("#nueva_solicitud").click(function () {
		/*let mensaje = `Se informa que hasta el día 27 de noviembre se recibieron solicitudes para adquisición de bienes y servicios.<br><br>
		Se reciben solo solicitudes de convenio, mantenimientos institucionales y actividades programadas para fin de año.`;
		swal({
			title: "INFORMACIÓN IMPORTANTE:",
			text: mensaje,
			html: mensaje,
			type: "info",
			confirmButtonText: "Si, Continuar!",
			closeOnConfirm: true,
		}, function (isConfirm) {
			if (isConfirm) {
				setTimeout(solicitudes_por_encuestar_persona, 750);
			}
		});*/	
		solicitudes_por_encuestar_persona();
	});
	$("#regresar_add").click(function () {
		$(".listado_solicitudes").css("display", "none");
		$("#menu_principal").fadeIn(1000);
	});


	$("#comentar").click(function () {
		let comentario = $("#comentario").val().trim();
		guardar_comentario(id_sol_comi, comentario);
	});
	$("#comentar_noti").click(function () {
		let comentario = $("#comentario_noti").val().trim();
		guardar_comentario(id_sol_comi, comentario);
	});
	$("#listar_comentarios").click(function () {
		listar_comentario(id_sol_comi);
	});
	$("#listar_comentarios_detalle").click(function () {
		listar_comentario(id_ver_comentarios);
	});
	$("#comentar_directo_1").click(function () {
		var comentario = $("#comentario_directo_1").val().trim();
		guardar_comentario(idsolicitud, comentario);
	});
	$("#comentar_directo_2").click(function () {
		var comentario = $("#comentario_directo_2").val().trim();
		guardar_comentario(idsolicitud, comentario);
	});

	$(".listar_comentarios_directos").click(function () {
		listar_comentario(idsolicitud);
	});

	$("#calcular_tiempos").click(function () {
		calcular_tiempo_gestion(idsolicitud);
	});

	$("#moneda").change(function () {
		let moneda = $(this).val();
		if (moneda == "usd") {
			$("#precio_dolar").show("slow");
			$("#precio_dolar").attr("required", "true");
		} else {
			$("#precio_dolar").hide("slow");
			$("#precio_dolar").removeAttr("required", "true");

		}
	});


	$("#solicitudes_no_asignadas").change(function () {
		let solicitud_no = $(this).val();
		if (solicitud_no == "excepto") {
			$(".exceptuando").show("slow");
			$(`#lista_excluida li`).removeClass("oculto");
			solicitudes_exceptuadas = solicitudes_faltantes_usu;
			//$("#precio_dolar_modi").attr("required", "true");
		} else {
			$(".exceptuando").hide("slow");

		}
	});

	$("#estados_no_asignados").change(function () {
		let estado = $(this).val();
		if (estado == "excepto") {
			$(".exceptuando_estados").show("slow");
			$(`#lista_excluida_estados li`).removeClass("oculto");
			estados_exceptuadas = estados_faltantes_usu;
			//$("#precio_dolar_modi").attr("required", "true");
		} else {
			$(".exceptuando_estados").hide("slow");

		}
	});

	$("#moneda_modi").change(function () {
		let moneda = $(this).val();
		if (moneda == "usd") {
			$("#precio_dolar_modi").show("slow");
			$("#precio_dolar_modi").attr("required", "true");
		} else {
			$("#precio_dolar_modi").hide("slow");
			$("#precio_dolar_modi").removeAttr("required", "true");

		}
	});

	/* Guardar encuestas rp */
	$("#encuesta_rp").submit(function () {
		guardar_encuestas_rp(idps);
		return false;
	});

	$("#asignar_proveedor_solicitud").submit(function () {
		guardar_proveedor_solicitud();
		return false;
	});

	/* Agregar nuevas preguntas a encuestas EVENTO del formu */
	$("#agregar_preguntas_encuestas").submit(function () {
		guardar_pregunta_encuesta();
		return false;
	});

	$("#form_modificar_proveedor_solicitud").submit(function () {
		modificar_proveedor_solicitud();
		return false;
	});

	$("#modificar_sol_comite").submit(function () {
		Modificar_solicitud_comite()
		return false;
	});

	$("#terminar_comite").click(function () {
		validar_aprobados_comite(idsolicitud)
		return false;
	});

	$("#btnmodificar_sol_comit").click(function () {
		$("#Modal_modificar_sol_comite select").val('');
		$("#Modal_modificar_sol_comite textarea").val('');
		traer_solicitud(idsolicitud);
		$("#Modal_modificar_sol_comite").modal("show");
		return false;
	});

	$("#asignar_solicitud_comite").submit(function () {
		swal({
			title: "Estas Seguro ?",
			text: "Tener en cuenta que, al modificar el estado de la solicitud no podra regresar al estado anterior.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Continuar!",
			cancelButtonText: "No, Regresar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				asignar_solicitud_comite();
			}
		});

		return false;
	});

	$("#form_modificar_comite").submit(function () {
		modifcar_comite();
		return false;
	});

	$("#form_guardar_comite").submit(function () {
		registrar_comite();
		return false;
	});

	$("#ModificarItem").submit(function () {
		if (idpregunta == 0) {
			Modificar_Proveedor();
		} else if (idproveedor == 0) {
			$("#modal_modpara_titulo").html(`<span class="fa fa-check-square-o"></span> Modificar Pregunta`);
			Modificar_Encuestas();
		}
		return false;
	});

	$('#mostar_otras_cargas').click(function () {
		if (conceptos == 0) {
			$("#otras_cargas").show("slow");
			$("#mostar_otras_cargas span").removeClass("fa fa-plus");
			$("#mostar_otras_cargas span").addClass("fa fa-minus");
			$("#otras_cargas input").attr("required", "true");
			conceptos = 1;
		} else {
			$("#otras_cargas").hide("slow");
			$("#mostar_otras_cargas span").removeClass("fa fa-minus");
			$("#mostar_otras_cargas span").addClass("fa fa-plus");
			$("#otras_cargas input").removeAttr("required", "true");
			conceptos = 0;
		}

	});

	$('#mostar_otras_cargas_modi').click(function () {
		if (conceptos_modi == 0) {
			$("#otras_cargas_modi").show("slow");
			$("#mostar_otras_cargas_modi span").removeClass("fa fa-plus");
			$("#mostar_otras_cargas_modi span").addClass("fa fa-minus");
			$("#otras_cargas_modi input").attr("required", "true");
			conceptos_modi = 1;
		} else {
			$("#otras_cargas_modi").hide("slow");
			$("#mostar_otras_cargas_modi span").removeClass("fa fa-minus");
			$("#mostar_otras_cargas_modi span").addClass("fa fa-plus");
			$("#otras_cargas_modi input").removeAttr("required", "true");
			conceptos_modi = 0;
		}

	});

	$('#admin_responsables').click(function () {
		mostrar_solicitudes(this);
		$("#container_admin_respo").fadeIn(1000);
		$("#container_mostrar_areas").css("display", "none");

	});

	$('#admin_proveedor').click(function () {
		idpregunta = 0;
		lugar_activo = "proveedor";
		$("#txtDescripcion_modificar").removeClass("oculto");
		mostrar_proveedor(this);
		$("#container_admin_provedor").fadeIn(1000);
		$("#container_mostrar_areas").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_ponderados").css("display", "none");
		$("#container_mostrar_criterios").css("display", "none");
		$("#container_admin_permisos").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_encuestas_pendientes").css("display", "none");
	});

	$('#admin_comite').click(function () {
		mostrar_comite(this);
		$("#container_admin_comite").fadeIn(1000);
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_ponderados").css("display", "none");
		$("#container_mostrar_criterios").css("display", "none");
		$("#container_admin_permisos").css("display", "none");
		$("#container_admin_provedor").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_encuestas_pendientes").css("display", "none");
	});

	/* Administrar permisos */
	$('#admin_permisos').click(function () {
		listar_personas_compras("undefined");
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		$("#container_admin_permisos").fadeIn(1000);
		$("#container_admin_ponderados").css("display", "none");
		$("#container_mostrar_criterios").css("display", "none");
		$("#container_admin_provedor").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_admin_comite").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_encuestas_pendientes").css("display", "none");
	});

	/* Administrar ponderados */
	$('#admin_ponderados').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		$("#container_admin_ponderados").fadeIn(1000);
		$("#container_mostrar_criterios").css("display", "none");
		$("#container_admin_permisos").css("display", "none");
		$("#container_admin_provedor").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_admin_comite").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_encuestas_pendientes").css("display", "none");
		listar_ponderados_rp();
	});

	/* Reevaluacion de proveedores - fj */
	$('#admin_encuestas_pendientes').click(function () {
		idproveedor = 0;
		lugar_activo = "encuesta";
		$("#txtDescripcion_modificar").addClass("oculto");
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		$("#container_admin_encuestas_pendientes").fadeIn(1000);
		$("#container_admin_ponderados").css("display", "none");
		$("#container_admin_permisos").css("display", "none");
		$("#container_admin_provedor").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_admin_comite").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		listarPersConEncuestas();
	});

	$('#admin_criterios').click(function () {
		$("#txtDescripcion_modificar").addClass("oculto");
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		$("#container_mostrar_criterios").fadeIn(1000);
		$("#container_admin_ponderados").css("display", "none");
		$("#container_admin_permisos").css("display", "none");
		$("#container_admin_provedor").css("display", "none");
		$("#container_admin_respo").css("display", "none");
		$("#container_admin_comite").css("display", "none");
		$("#container_mostrar_areas").css("display", "none");
		$("#container_admin_encuestas_pendientes").css("display", "none");
		//funcion para traer los criterios ya asignarlos
		listar_criterios_rp();
	});

	$("#conadjuntos").click(function () {
		$("#modal_enviar_archivos").modal("show");
		$("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
	});
	$(".conadjuntos_gestion").click(function () {
		$("#modal_enviar_archivos").modal("show");
		$("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-danger active btnAgregar" id="enviar_adjuntos"><span class="glyphicon glyphicon-ok"></span> Temrinar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');

		$('#enviar_adjuntos').click(function () {
			if (num_archivos != 0) {
				tipo_cargue = 0;
				$("#id_archivo").val(idsolicitud);
				myDropzone.processQueue();
			} else {
				MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
			}
		});
	});

	/* Evento para btn de generar los promedio de los proveedores */
	$("#generar_promedios").click(function () {
		//function para llenar el datatable de los promedios
		$("#modal_promedios_proveedores").modal();
		promediar_proveedores();
	});

	$('#auditarsolo').click(function () {
		$("#modal_tiempos_gestion").modal("show");
		$("#modal_tiempos_gestion li").removeClass("active");
		$("#modal_tiempos_gestion #show_satis_enc").addClass("active");
		$("#div_encs_rp").addClass("oculto");
		$("#tabla_satis_enc").removeClass("oculto");

		/* Eventos para los btns del nav de encuestas a mostrar */
		$("#show_satis_enc").click(function () {
			$("#modal_tiempos_gestion li").removeClass("active");
			$(this).addClass("active");
			$("#tabla_satis_enc").removeClass("oculto");
			$("#div_encs_rp").addClass("oculto");
		});

		/* Btn ver encuestas RP */
		$("#show_rp_encs").off('click');
		$("#show_rp_encs").click(function () {
			$("#modal_tiempos_gestion li").removeClass("active");
			$(this).addClass("active");
			$("#tabla_satis_enc").addClass("oculto");
			$("#div_encs_rp").removeClass("oculto");
			list_finished_rp_encs(tipo_orden_sel);
		});
	});
	$('#agregar_soliciutd_usuario').click(function () {
		asignar_solicitud_usuario(usuario_selecc);
	});
	$('#agregar_estado_usuario').click(function () {
		asignar_estado_usuario(soli_usu_sele);
	});

	$("#limpiar_filtros_compras").click(function () {
		sin_filtros();
	});
	$("#limpiar_filtros_comite").click(function () {
		listar_comites_directivos(-1);
		Con_filtros(false);
	});
	$("#conadjuntos_modi").change(function () {
		if ($(this).is(':checked')) {
			$("#adjuntos_modi").show("slow");

		} else {
			$("#adjuntos_modi").hide("slow");

		}
	});
	$("#gestionar_entregas_par").click(function () {
		$("#Modal_Entregas_parciales").modal();
	});

	$("#estados_siguientes").off('change');
	$("#estados_siguientes").on('change', async function () {
		$("#campo_par").css("display", "none");
		$("#campo_or_comp").css("display", "none");
		$("#tipo_compra_asi").css("display", "none");
		$("#campo_or_comp input").removeAttr("required", "true");
		$("#campo_or_comp select").removeAttr("required", "true");
		$("#tipo_compra_asi select").removeAttr("required", "true");

		$("#campo_comi").css("display", "none");
		$("#comites_compras").removeAttr("required", "true");
		$("#descripcion_cmt").removeAttr("required", "true");

		$("#causal_dev").css("display", "none");
		$("#causal_dev select").removeAttr("required", "true");

		let estado = $(this).val();
		if (estado == 'Soli_Oco') {
			$("#campo_or_comp input").attr("required", "true");
			$("#campo_or_comp select").attr("required", "true");
			$(".checks").attr("required", false);
			$("#campo_or_comp").show("fast");
		} else if (estado == 'Soli_Com') {
			listar_comites_combo()
			$("#comites_compras").attr("required", "true");
			$("#descripcion_cmt").attr("required", "true");
			$("#campo_comi").show("fast");
		} else if (estado == 'Soli_Par') {
			$("#campo_par").show("fast");
			Listar_articulos_parciales(idsolicitud);
		} else if (estado == 'Soli_Rec') {
			$("#tipo_compra_asi").show("fast");
			$("#tipo_compra_asi select").attr("required", "true");
		} else if (estado == 'Soli_Dev') {
			$("#causal_dev").show("fast");
			$("#causal_dev select").attr("required", "true");
		} /*else if (estado == 'Soli_Pen') { //Para reactivar el cronograma, descomentar es elseif
			compra_crono(true);
		}*/
		return false;
	});

	$("#btnreporte").click(function () {
		Listar_solicitudes();
	});
	$("#admin_solicitudes").click(function () {
		let li = $("#nav_admin_compras li:first");
		let primer_item = $("#nav_admin_compras li:first")[0].id;

		if (primer_item == 'admin_responsables') {
			mostrar_solicitudes(li);
			$("#container_admin_respo").show("fast");
		} else if (primer_item == 'admin_comite') {
			mostrar_comite(li)
			$("#container_admin_comite").show("fast");
		} else if (primer_item == 'admin_proveedor') {
			mostrar_proveedor(li)
			$("#container_admin_provedor").show("fast");
		} else if (primer_item == 'admin_permisos') {
			$("#container_admin_permisos").show("fast");
			$("#container_admin_ponderados").css("display", "none");
			$("#container_mostrar_criterios").css("display", "none");
			$("#container_admin_provedor").css("display", "none");
			$("#container_admin_respo").css("display", "none");
			$("#container_admin_comite").css("display", "none");
			$("#container_mostrar_areas").css("display", "none");
			$("#container_admin_encuestas_pendientes").css("display", "none");
			$("#nav_admin_compras li").removeClass("active");
			$("#admin_permisos").addClass("active");
			listar_personas_compras("undefined");
		}
		$("#Modal_administrar_solicitudes").modal("show");
	});
	$("#ver_historial_estado").click(function () {
		Listar_historial_estado(idsolicitud);
	});
	$("#cbx_articulos_historial").change(function () {
		let art = $(this).val();
		historial_articulos_entregas_parciales(idsolicitud, art);
	});
	$("#ver_historial_entregas").click(function () {
		$("#modal_historial_entregas").modal();
		cargar_articulos(idsolicitud, "#cbx_articulos_historial", "Seleccione Articulo");
		historial_articulos_entregas_parciales(-1, -1);
	});

	$("#gestionar_solicitud").submit(function () {
		let estado = $("#estados_siguientes").val();
		causal = $("#causal_compra").val();
		if (estado == "Soli_Dev") {
			swal({
				title: "Denegar Solicitud .?",
				text: "Si desea continuar con el proceso de denegación, por favor ingrese un motivo y presionar la opción de 'Continuar'",
				type: "input",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Continuar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
				inputPlaceholder: `Ingrese un motivo ${(causal == 'Co_CausOtr' ? '' : '(Opcional)')}`
			}, function (motivo) {
				if (motivo === false)
					return false;
				if (motivo === "" && causal == 'Co_CausOtr') {
					swal.showInputError("Debe Ingresar el Motivo por el cual no fue aprobada la solicitud..!");
					return false;
				} else {
					Gestionar_solicitud(idsolicitud, motivo, estado);
					return false;
				}
			});
			return false;

		}
		if (estado == undefined) {
			estado = 'Ser_Rec'
		}

		swal({
			title: "Estas Seguro ?",
			text: "Tener en cuenta que, al modificar el estado de la solicitud no podra regresar al estado anterior.!",
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
					Gestionar_solicitud(idsolicitud, '', estado);
				}
			});

		return false;
	});


	$(".sele_jefe_area").click(function () {
		listar_Personas_jefe("--1");
		$("#Modal_jefe").modal("show");
		$("#input_persona_reserva").val("");
	});

	$("#buscar_sele_perso").click(function () {
		var datos = $("#input_persona_reserva").val().trim();
		(datos.length == 0) ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") : listar_Personas_jefe(datos);
	});

	$("#input_persona_reserva").keypress(function (e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			var datos = $("#input_persona_reserva").val().trim();
			(datos.length == 0) ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") : listar_Personas_jefe(datos);
		}
	});

	$("#Agregar_Solicitud").submit(function () {
		if (articulos_sele.length > 0) {
			guardar_solicitud();

		} else {
			MensajeConClase("Por favor agregar al menos un artículo.", "info", "");
		}

		return false;
	});

	$("#Modificar_Solicitud").submit(function () {
		swal({
			title: "Modificar Solicitud..?",
			text: "Si desea continuar por favor presionar la opción de 'Modificar'",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Modificar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
			function (isConfirm) {
				if (isConfirm) {
					modificar_solicitud(idsolicitud);
				}
			});

		return false;
	})
	$("#guardar_encuesta").submit(function () {
		guardar_encuesta();
		return false;
	})
	$("#Agregar_Articulos").submit(function () {
		articulo = $(this).serializeJSON();
		guardar_articulo(articulo);

		return false;
	});

	$("#Editar_Articulos").submit(function () {
		modificar_articulo(idarticulo);
		return false;
	});

	$(".sel_cod").click(function () {
		buscar_codigo("&-1%");
		$("#Buscar_Codigo").modal();
		$("#txtcodigo_sap").val("");
	});

	$(".sel_cod_modi").click(function () {
		buscar_codigo("&-1%");
		$("#Buscar_Codigo").modal();
		$("#txtcodigo_sap").val("");
	});

	$("#modificar_solicitud_ini").click(() => {
		if (idsolicitud != 0) {
			$("#modalModificarSolicitud").modal("show");
			$("#Modificar_Solicitud").get(0).reset();
		} else {
			MensajeConClase("Seleccione Solicitud a modificar.", "info", "Oops");
		}
	});

	$("#buscar_cod_sap").click(function () {
		buscar_codigo($("#txtcodigo_sap").val());
		return false;
	});

	$("#txtcodigo_sap").keypress(function (e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			buscar_codigo($("#txtcodigo_sap").val());
			return false;
		}
	});

	$("#btsagregar").click(function () {
		guardar = 1;
		$("#text_add_arts").html("Agregar Artículos y/o Servicios");
		if (modificando_ini == 1) {
			modificando_ini = 0;
			$("#Agregar_Articulos").get(0).reset();
			$("#cod_orden_sele").html("Seleccione Código SAP");
		}
		$("#modalArticulos").modal();
	});
	$("#con_tarjeta").click(function () {
		if ($(this).is(':checked')) {
			$("#con_tarjeta").removeClass('funkyradio-success');
			$("#container_con_tarjeta").show('slow');
			$("#Agregar_Articulos input[name='fecha_compra_tarjeta']").attr("required", "true");
		} else {
			$("#Agregar_Articulos input[name='fecha_compra_tarjeta']").removeAttr("required", "true");
			$("#container_con_tarjeta").hide('slow');
			$("#con_tarjeta").addClass("funkyradio-success")
		}
	});
	$("#con_tarjeta_modi").click(function () {
		if ($(this).is(':checked')) {
			$("#con_tarjeta_modi").removeClass('funkyradio-success');
			$("#container_con_tarjeta_modi").show('slow');
			$("#Editar_Articulo input[name='fecha_compra_tarjeta']").attr("required", "true");
		} else {
			$("#Editar_Articulo input[name='fecha_compra_tarjeta']").removeAttr("required", "true");
			$("#container_con_tarjeta_modi").hide('slow');
			$("#con_tarjeta_modi").addClass("funkyradio-success")
		}
	});

	$("#cambiar_proveedor").click(function () {
		$("#modal_cambiar_proveedor").modal("show");
	});

	$("#form_cambiar_proveedor").submit((e) => {
		e.preventDefault();
		let nuevo_prov = $("#select_cambiar_apoyo").val();
		cambiar_proveedor(idsolicitud, nuevo_prov);
	});

	/* Eventos formulario upd crono */
	$('#form_upd_crono').submit(function () {
		return false;
	});

	$('#modal_encuesta_usuario input[type=radio]').click(function () {
		let res1 = $('#modal_encuesta_usuario input[name="respuesta1"]:checked').val()
		let res2 = $('#modal_encuesta_usuario input[name="respuesta2"]:checked').val()
		let res3 = $('#modal_encuesta_usuario input[name="respuesta3"]:checked').val()
		if (res1 == 1 || res1 == 2 || res2 == 1 || res2 == 2 || res3 == 1 || res3 == 2) $("#pregunta4").show("fast");
		else $("#pregunta4").hide("fast");
	});

	$('.buscar_jefe').click(() => {
		$('#txt_dato_buscar').val('');
		buscar_jefe();
		$('#modal_buscar_jefe').modal();
	});

	$('#form_buscar_persona').submit((e) => {
		e.preventDefault();
		let dato = $('#txt_dato_buscar').val();
		buscar_jefe(dato);
	});

	validacion_cargo().then((data) => {
		if (data.length > 0) {
			if (data[0].valory == "1") {
				$("#div_jefe").hide()
				$('#txt_nombre_jefe').attr('required', false)
			}
		}
	});

});

/* FUERA DEL READY */

function cargar_combo_articulos(arr) {


	for (let index = 0; index < arr.length; index++) {
		const element = arr[index];

	}

	Listar_articulos_solicitados(articulos_sele);
}

/* Funcion para traer encuestas masivas segun usuario */
const massives_rp = async (tipo_encuesta = '', proveedor) => {
	if(typeof proveedor == 'undefined' || proveedor == '') listarProveedoresEnc(tipo_encuesta);
	$("#tabla_rp_massives").off('click', '.ver_detalles');
	consulta_ajax(`${ruta_interna}massives_rp`, { tipo_encuesta, proveedor }, res => {
		massives = res;
		const massivess = $("#tabla_rp_massives").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: 'ver' },
				{ data: 'solicitante' },
				{ data: 'proveedor_name' },
				{ data: 'name_order_type' },
				{ data: 'fecha_registra' },
				{ data: 'no_orden' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		/* Eventos de btns activado */
		$("#tabla_rp_massives").on('click', '.ver_detalles', function () {
			let datos = massivess.row($(this).parent().parent()).data();
			$("#modal_detalles_masivos").modal();
			detalles_articulos_masivos(datos.id);
		});
	});
}

const listarProveedoresEnc = (tipo_encuesta) => {
	$("#modal_masivos_compras #massives_prov").html(`<option value="">Filtrar por proveedor...</option>`);
	consulta_ajax(`${ruta_interna}listarProveedoresEnc`, {tipo_encuesta}, res => {
		if (res) {
			res.forEach(element => {
				if (element.id != 'N/A') {
					$("#modal_masivos_compras #massives_prov").append(
						`<option value="${element.id}">${element.valor}</option>`
					);
				}
			});
		}
	});
}

/* Funcion para promediar proveedores segun datos del listar soliciudes */
const promediar_proveedores = (datos = "") => {
	let { fecha_ini, fecha_fin } = datos;
	if (datos.length != 0) {
		encuestas_rp_faltantes(fecha_ini, fecha_fin);
	}
	$("#tabla_promedios_proveedores").off('click', '#verSolicitudesPP');
	consulta_ajax(`${ruta_interna}promediar_proveedores`, { fecha_ini, fecha_fin }, res => {
		const PromediosProveedores = $("#tabla_promedios_proveedores").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: 'prov_name' },
				{ "render": function (data, type, full, meta) {
					return '<span id="verSolicitudesPP" title="Ver solicitudes" data-toggle="popover" data-trigger="hover" class="btn btn-default"><span class="badge btn btn-danger">'+full.canti_sol+'</span> Ver</span>';
				} },
				{ data: 'res_del' },
				{ data: 'res_sga' },
				{ data: 'res_tipmat' },
				{ data: 'res_tipserv' },
				{ data: 'res_sst' },
				{ data: 'puntaje' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_promedios_proveedores').on('click', '#verSolicitudesPP', function(){
			let datos = PromediosProveedores.row($(this).parent().parent()).data();
			$('#modal_solicitudes_promedios').modal();
			$('#tabla_solicitudes_promedios .nombre_tabla .persona').html(datos.prov_name);
			consulta_ajax(`${ruta_interna}solicitudes_promedo_proveedores`, { idproveedor: datos.idproveedor }, res => {
				const SolicitudesProveedores = $("#tabla_solicitudes_promedios").DataTable({
					destroy: true,
					processing: true,
					searching: true,
					data: res,
					columns: [
						{
							"render": function (data, type, full, meta) {
								return '<span id="verDetalleSolicitud" title="Ver solicitudes" data-toggle="popover" data-trigger="hover" class="btn btn-default">Ver</span>';
							} 
						},
						{ data: 'solicitante' },
						{ data: 'tipo_orden' },
						{ data: 'fecha_solicitud' },
						{ data: 'num_orden' }
					],
					language: get_idioma(),
					dom: 'Bfrtip',
					buttons: get_botones(),
				});
				
				$('#tabla_solicitudes_promedios').on('click', '#verDetalleSolicitud', function(){
					let datos = SolicitudesProveedores.row($(this).parent().parent()).data();
					llenar_tabla_detalles(datos);
				});
			});
		});
	});
}

/* Traer solicitudes que tengan una encuesta pendiente por realizar */
const encuestas_rp_faltantes = async (fecha_ini, fecha_fin) => {
	$("#alerta_faltantes #provs_restantes_conatiner").html("");
	$("#alerta_faltantes #provs_restantes_conatiner .see_details").off('click');
	consulta_ajax(`${ruta_interna}encuestas_rp_faltantes`, { fecha_ini, fecha_fin }, res => {
		if (res.length != 0) {
			$("#alerta_faltantes").removeClass('oculto');
			$("#alerta_faltantes #provs_restantes_conatiner").append(`<i><span class="see_details pointer"><u>Click aquí para más detalles...</u></span></i>`);
		} else {
			$("#alerta_faltantes").addClass('oculto');
		}

		$("#alerta_faltantes #provs_restantes_conatiner .see_details").on('click', function () {
			$("#modal_rp_faltante").modal();
			$("#tabla_rp_faltantes").DataTable({
				destroy: true,
				processing: true,
				searching: true,
				data: res,
				columns: [
					{ data: 'no_s' },
					{ data: 'solicitanteName' },
					{ data: 'tipoCompra' },
					{ data: 'fecha_sol' },
					{ data: 'tipo_orden' },
					{ data: 'sga' },
					{ data: 'sst' },
					{ data: 'mat_ser' },
				],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: get_botones(),
			});
		});
	});
}

/* Adicionar criterios de valorp */
const add_valorp = (datos) => {
	let { valor, valorx } = datos;
	consulta_ajax(`${ruta_interna}add_valorp`, { valor, valorx }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			$("#modal_valor_parametro").modal("hide");
			$("#form_valor_parametro").trigger("reset");
			listar_criterios_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Eliminar criterios de valorp */
const del_valorp = (cri_id) => {
	consulta_ajax(`${ruta_interna}del_valorp`, { cri_id }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			listar_criterios_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Actualizar valor_parametro */
const upd_valorp = (datos, critId) => {
	let { valor, valorx } = datos;
	consulta_ajax(`${ruta_interna}upd_valorp`, { valor, valorx, critId }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			$("#modal_valor_parametro").modal("hide");
			$("#form_valor_parametro").trigger("reset");
			listar_criterios_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Function para agregar porcentajes */
const create_porcentajes = (datos) => {
	let { valor_ini, valor_fin, porcentaje } = datos;
	consulta_ajax(`${ruta_interna}create_porcentajes`, { valor_ini, valor_fin, porcentaje }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			$("#form_valor_porcentaje").trigger("reset");
			$("#modal_valor_porcentaje").modal("hide");
			listar_ponderados_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Function para actualizar los porcentajes */
const upd_porcentajes = (datos, id_ps) => {
	let { valor_ini, valor_fin, porcentaje } = datos;
	consulta_ajax(`${ruta_interna}upd_porcentajes`, { valor_ini, valor_fin, porcentaje, id_ps }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			$("#form_valor_porcentaje").trigger("reset");
			$("#modal_valor_porcentaje").modal("hide");
			listar_ponderados_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Function para eliminar los porcentajes */
const del_porcentajes = (id_porcen) => {
	consulta_ajax(`${ruta_interna}del_porcentajes`, { id_porcen }, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			listar_ponderados_rp();
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

/* Guardar preguntas nuevas */
const guardar_pregunta_encuesta = () => {
	let datos = new FormData(document.getElementById('agregar_preguntas_encuestas'));
	datos.append("preg_catego", tipo_enc_selected);
	enviar_formulario(`${ruta_interna}guardar_pregunta_encuesta`, datos, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			$("#preguntas_container").html(`
				<input type="text" class="form-control" name="pregunta[]" data-item="1" value="" placeholder="Escriba la pregunta aqui!">
				<hr>
			`);
			$("#agregar_preguntas_encuestas").trigger('reset');
			$("#modal_agregar_pregunta_encuesta").modal('hide');
			listar_encuestas_compras();
			setTimeout(cerrar_swals, 1300);
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
		}
	});
}

function guardar_solicitud() {
	MensajeConClase("validando info", "add_inv", "Oops...");
	let jsonString = JSON.stringify(articulos_sele);
	let soli_desc = "";

	for (let n = 0; n < articulos_sele.length; n++) {
		if ((n - (articulos_sele.length - 1)) == -1) {
			soli_desc += `${articulos_sele[n].nombre_art} y `;
		} else if (n == (articulos_sele.length - 1)) {
			soli_desc += `${articulos_sele[n].nombre_art}. `;
		} else {
			soli_desc += `${articulos_sele[n].nombre_art}, `;
		}
	}

	let formData = new FormData(document.getElementById("Agregar_Solicitud"));
	formData.append("data", jsonString);
	formData.append("jefe", id_pjefe);
	$.ajax({
		url: server + "index.php/compras_control/guardar_solicitud",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {
		let dato = datos[0];
		if (dato == "sin_session") {
			close();
			return;
		}
		if (dato == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operaci&oacuten", "error", "Oops...");
			return;
		}
		if (dato == -1) {
			MensajeConClase("Digite un nombre para la solicitud.!", "info", "Oops...");
		} else if (dato == -2) {
			MensajeConClase("Seleccione un tipo de compra.!", "info", "Oops...");
		} else if (dato == -3) {
			MensajeConClase("Seleccione jefe encargado.!", "info", "Oops...");
		} else if (dato == -4) {
			MensajeConClase("Seleccione departamento donde sera utlizado.!", "info", "Oops...");
		} else if (dato == -5) {
			MensajeConClase(datos[1], "info", "Oops...");
		} else if (dato == -6) {
			MensajeConClase("Seleccione archivo adjunto.!", "info", "Oops...");
		} else if (dato == 0) {

			$("#Agregar_Solicitud").get(0).reset();
			$("#spanjefe_area").html('Seleccione Jefe encargado');
			$('#cbxarticulos_agregados').empty().append('<option value="">0 Articulos solicitados</option> ');
			$("#adjuntos").hide("slow");
			$("#myModal").modal("hide");
			let filas_tabla_art = ''
			articulos_sele.map((articulo) => {
				filas_tabla_art = filas_tabla_art + 
				`<tr>
					<td>${articulo.nombre_art}</td>
					<td>${articulo.cantidad_art}</td>
					<td>${articulo.observaciones}</td>
				</tr> `;
			})
			articulos_sele = [];
			id_articulo_add_soli = 0;
			if (num_archivos != 0) {
				tipo_cargue = 1;
				id_archivos = datos[1];
				$("#id_archivo").val(id_archivos);
				myDropzone.processQueue();

			}
			if (num_archivos == 0) {
				MensajeConClase("Solicitud Guardada con exito.!", "success", "Proceso Exitoso!");
			}
			Listar_solicitudes();
			let correos = [{ "correo": datos[2].correo }];
			if (correo_jefe != null) correos.push({ "correo": correo_jefe })
			let ser = '<a href="' + server + 'index.php/compras/' + datos[1] + '"><b>agil.cuc.edu.co</b></a>'
			let tabla_articulos = `<table>
			<thead style="font-weight: bold;">
				<tr>
				<td>Artículo</td>
				<td>Cantidad</td>
				<td>Descripción</td>
				</tr>
			</thead>
			<tbody>${filas_tabla_art}</tbody>
			</table>`
			let mensaje = `Se le notifica que la solicitud realizada por ${datos[2].fullname},  fue recibida y se encuentra en proceso. A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra la solicitud.
			<br><br>A continuación se relacionan los artículos solicitados :
			<br><br>${tabla_articulos}
			<br>Mas información en : ${ser}`;
			enviar_correo_personalizado("comp", mensaje, correos, datos[2].fullname, "Compras CUC", "Solicitud de Compra", "ParCodAdm", 3);
			return;
		} else {
			MensajeConClase("Error al guardar la solicitud. Por favor contacte al administrador", "error", "Error!");
		}
	});
}

/* Funcion para promediar proveedores segun datos del listar soliciudes */
const detalles_articulos_masivos = (idSol) => {
	consulta_ajax(`${ruta_interna}detalles_articulos_masivos`, { idSol }, res => {
		$("#tabla_articulos_masivos").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: 'art_o_ser' },
				{ data: 'cantidad' },
				{ data: 'codSap' },
				{ data: 'fecha_registra' },
				{ data: 'obs' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});
	});
}

function modificar_solicitud(id_solicitud) {
	let formData = new FormData(document.getElementById("Modificar_Solicitud"));
	formData.append('id_solicitud', id_solicitud);
	$.ajax({
		url: server + "index.php/compras_control/modificar_solicitud",
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
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion.", "error", "Oops.!");
			return;
		} else if (datos == -1) {
			MensajeConClase("Seleccione tipo de compra nuevo.", "info", "Oops.!");
			return;
		} else if (datos == -2) {
			MensajeConClase("No se encontraron cambios al modificar, verifique que el nuevo tipo de compra sea diferente al actual.", "info", "Oops.!");
			return;
		} else if (datos == -3) {
			MensajeConClase("Ingrese motivo del cambio de tipo de compra.", "info", "Oops.!");
			return;
		} else if (datos == -4) {
			MensajeConClase("El cambio al tipo seleccionado no esta disponible, ya que afecta el proceso de compra. ", "info", "Oops.!");
			return;
		} else if (datos == 1) {
			//MensajeConClase("El tipo de compra fue modificado con exito.", "success", "Proceso Exitoso!");
			swal.close();
			Listar_solicitudes();
			$("#modalModificarSolicitud").modal("hide");
			return;
		} else {
			MensajeConClase("Error al modificar la solicitud, contacte con el administrador.", "error", "Oops.!");
		}
	});
}

function Listar_articulos(id, tipo = -1, render = '#tabla_articulos') {
	$(`${render} tbody`).off('dblclick', 'tr');
	$(`${render} tbody`).off('click', 'tr');
	$(`${render} tbody`).off('click', 'tr td:nth-of-type(1)');

	const myTable = $(render).DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/Listar_articulos/",
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
		},
		"processing": true,
		"columns": [{

			"data": "codigo"
		}, {

			"data": "valor"
		},
		{
			"data": "nombre_articulo"
		},
		{
			"data": "cantidad"
		},
		{
			"data": "con_tarjeta"
		},
		{
			"data": "gestion"
		},

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$(`${render} tbody`).on('click', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_articulos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		modificando_articulo = 0;
	});

	$(`${render} tbody`).on('dblclick', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_articulos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	$(`${render} tbody`).on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		llenar_tabla_detalles_articulo(data);
	});

}

function llenar_tabla_detalles_articulo(datos) {
	let { valor, nombre_articulo, cantidad, marca, referencia, fecha_creacion, observaciones, fecha_compra_tarjeta } = datos;
	$(".valor_codigo_art").html(valor);
	$(".valor_nombre_art").html(nombre_articulo);
	$(".valor_cantidad_art").html(cantidad);
	$(".valor_marca_art").html(marca);
	$(".valor_referencia_art").html(referencia);
	$(".valor_fecha_cr_art").html(fecha_creacion);
	$(".valor_observaciones_art").html(observaciones);
	$(".valor_fecha_compra_tarjeta_art").html(fecha_compra_tarjeta);
	fecha_compra_tarjeta ? $(".tr_fecha_compra_art").show('fast') : $(".tr_fecha_compra_art").hide('fast');
	$("#modal_detalle_articulo").modal();

}

function Listar_articulos_parciales(id) {

	$('#tabla_articulos_parciales tbody').off('dblclick', 'tr');
	$('#tabla_articulos_parciales tbody').off('click', 'tr');
	const myTable = $("#tabla_articulos_parciales").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/Listar_articulos_parciales/1",
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
		},
		"processing": true,
		"order": [
			[4, "asc"]
		],
		"columns": [{
			"data": "nombre_articulo"
		},
		{
			"data": "marca"
		},
		{
			"data": "cantidad"
		},
		{
			"data": "entregada"
		},

		{
			"data": "pendiente"
		},



		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_articulos_parciales tbody').on('click', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_articulos_parciales tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});



	$("#Modal_Entregas_parciales").modal();

}

function Listar_historial_estado(id, tiempo_habil = null, tiempo_gestion = null) {
	if (tiempo_habil != null) {
		$(".valor_gestion_habiles").html(tiempo_habil);
		$(".valor_gestion_enges").html(tiempo_gestion);
	}
	$('#tabla_historial tbody').off('dblclick', 'tr');
	$('#tabla_historial tbody').off('click', 'tr');
	const myTable = $("#tabla_historial").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/Listar_historial_estado",
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
		},
		"processing": true,
		"columns": [{

			"data": "indice"
		},
		{
			"data": "estado"
		},
		{
			"data": "usuario"
		},
		{
			"data": "fecha_cambio"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_historial tbody').on('click', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_historial tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$("#modal_historial_estados").modal();

}

const mostrar_solicitudes = li => {
	listar_responsables_procesos();
	$("#nav_admin_compras li").removeClass("active");
	$(li).addClass("active");
	$("#container_admin_ponderados").css("display", "none");
	$("#container_admin_provedor").css("display", "none");
	$("#container_admin_comite").css("display", "none");
	$("#container_admin_permisos").css("display", "none");
}

const mostrar_comite = li => {
	listar_comites();
	$("#nav_admin_compras li").removeClass("active");
	$(li).addClass("active");
	$("#container_admin_ponderados").css("display", "none");
	$("#container_admin_provedor").css("display", "none");
	$("#container_admin_respo").css("display", "none");
	$("#container_admin_permisos").css("display", "none");
}

const mostrar_proveedor = li => {
	listar_proveedores();
	$("#nav_admin_compras li").removeClass("active");
	$(li).addClass("active");
	$("#container_admin_ponderados").css("display", "none");
	$("#container_admin_respo").css("display", "none");
	$("#container_admin_comite").css("display", "none");
	$("#container_admin_permisos").css("display", "none");
}

const Listar_solicitudes = async (idpas = 0, consulta = -1, sinencusta = -1) => {
	idps = "";
	datos_gestion = { };
	let datos;
	//idsolicitud = 0;
	idsolicitud_alt = idpas;
	modificando = 0;
	let tipo = '%%';
	let estado = '%%';
	let departamento = '%%';
	let fecha = '';
	let fecha2 = '';
	let proveedor = '';
	let sw = false;

	/* Filtrado por proveedor */
	if ($("#cbxproveedores_ordenn").val().trim().length != 0) {
		sw = true;
		proveedor = $("#cbxproveedores_ordenn").val();
	}
	if ($("#tipo_compra_filtro").val().trim().length != 0) {
		sw = true;
		tipo = $("#tipo_compra_filtro").val();
	}
	if ($("#estado_filtro").val().trim().length != 0) {
		sw = true;
		estado = $("#estado_filtro").val();
	}
	if ($("#fecha_filtro").val().trim().length != 0 && $("#fecha_filtro_2").val().trim().length != 0) {
		sw = true;
		fecha = $("#fecha_filtro").val();
		fecha2 = $("#fecha_filtro_2").val();
	}
	if (consulta > 0 || sinencusta > 0) {
		sw = true;
	}
	$('#tabla_solicitudes tbody').off('dblclick', 'tr');
	$('#tabla_solicitudes tbody').off('click', 'tr');
	$('#tabla_solicitudes tbody').off('click', 'tr td:nth-of-type(1)');
	$('#tabla_solicitudes tbody span .btnMod').off("click");
	$('#tabla_solicitudes tbody').off('click', 'tr span');
	$('#tabla_solicitudes').off('click', 'tbody tr .do_sga_enc');
	$('#tabla_solicitudes').off('click', 'tbody tr .do_sst_enc');
	$('#tabla_solicitudes').off('click', 'tbody tr .do_tip_mat_enc');
	$('#tabla_solicitudes').off('click', 'tbody tr .do_tip_ser_enc');
	$('#tabla_solicitudes').off('click', 'tbody tr .do_satis_enc');
	$('#tabla_solicitudes').off('click', 'tbody tr .fechas_check');
	$('#tabla_solicitudes').off('click', 'tbody tr .admin_crono_check');
	$('#tabla_solicitudes').off('click', 'tbody tr .pasar_pendiente');
	const myTable = $("#tabla_solicitudes").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/Listar_solicitudes",
			dataType: "json",
			data: {
				tipo,
				estado,
				departamento,
				fecha,
				fecha2,
				consulta,
				proveedor,
				sinencusta
			},
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
			"data": "codigo"
		},
		{
			"data": "tipo_compra"
		},
		{
			"data": "solicitante"
		},
		{
			"data": "fecha_registra"
		},
		{
			"data": "indice_fecha"
		},
		{
			"data": "num_orden"
		},
		{
			"data": "estimada_real"
		},
		{
			"data": "estado_solicitud"
		},
		{
			"data": "gestion"
		},
		{
			"data": "res1_encuesta"
		},
		{
			"data": "res2_encuesta"
		},
		{
			"data": "res3_encuesta"
		},
		{
			"data": "obs_encuesta"
		},
		{ "data": "proveedor" },
		{ "data": "resultado_final_rp" },
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(''),
	});


	myTable.column(9).visible(false);
	myTable.column(10).visible(false);
	myTable.column(11).visible(false);
	myTable.column(12).visible(false);
	myTable.column(13).visible(false);
	myTable.column(14).visible(false);

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_solicitudes tbody').on('click', 'tr', function () {
		en_notificacion = 0;
		const data = myTable.row(this).data();
		idsolicitud = data.id;
		estado_actual = data.estado_general;
		modificando = 0;
		id_usuario_sol = data.id_solicitante;
		datos_gestion = {
			"persona": data.solicitante,
			"correo": data.correo,
			"id_tipo_orden": data.id_tipo_orden,
			"tipo_sol": data.id_tipo_compra,
		};
		$(".valor_comite").html(data.nombre_comite);
		$(".valor_descripcion_cmt").html(data.descripcion_cmt);
		$(".valor_observaciones_cmt").html(data.observaciones_cmt);
		$(".valor_fecha_cierre").html(data.fecha_cierre_comite);
		$("#tabla_solicitudes tbody tr").removeClass("warning");
		$(this).addClass("warning");

		if (data.estado_general == "Soli_Par") {
			$("#Modal_Entregas_parciales #footermodal").html('<button type="button" class="btn btn-danger active btnAgregar" id="enviar_entregas_parciales"><span class="fa fa-history"></span> Temrinar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
			$("#enviar_entregas_parciales").click(function () {
				entregas_parciales();
				return false;
			});
		} else if (data.estado_general == "Soli_Pen") {
			$("#Modal_Entregas_parciales #footermodal").html('<button type="submit" class="btn btn-danger active btnAgregar"><span class="fa fa-history"></span> Temrinar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
		} else {
			$("#Modal_Entregas_parciales #footermodal").html();
		}

		if (data.estado_general == "Soli_Rev" && data.sw_add == 1) {
			$("#modalArticulos_Solicitud #footermodal").html(' <button   type="button" class="btn btn-danger active btnAgregar mas_articulos"><span class="btn-Efecto-men  fa fa-plus"></span> Articulos</button><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
			$(".mas_articulos").click(function () {
				if (idsolicitud != 0) {
					guardar = 2;
					$("#modalArticulos").modal();
				} else {
					MensajeConClase("Seleccione una Solicitud.", "info", "Lo que sea!");
				}
			});
		} else {
			$("#modalArticulos_Solicitud #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');

		}

	});

	/* Eventos de los btns de encuestas RP */
	$('#tabla_solicitudes').on('click', 'tbody tr .do_sst_enc', async function () {
		idps = "";
		let enc_type = $(this).attr("data-enctype");
		encuesta_activa = enc_type;
		const data = myTable.row($(this).parent().parent()).data();
		idps = data.id;
		preguntas_rp(enc_type, data.id);
	});

	$('#tabla_solicitudes').on('click', 'tbody tr .do_sga_enc', async function () {
		idps = "";
		let enc_type = $(this).attr("data-enctype");
		encuesta_activa = enc_type;
		const data = myTable.row($(this).parent().parent()).data();
		idps = data.id;
		preguntas_rp(enc_type, data.id);
	});

	$('#tabla_solicitudes').on('click', 'tbody tr .do_tip_mat_enc', async function () {
		idps = "";
		let enc_type = $(this).attr("data-enctype");
		encuesta_activa = enc_type;
		const data = myTable.row($(this).parent().parent()).data();
		idps = data.id;
		preguntas_rp(enc_type, data.id);
	});

	$('#tabla_solicitudes').on('click', 'tbody tr .do_tip_ser_enc', async function () {
		idps = "";
		let enc_type = $(this).attr("data-enctype");
		encuesta_activa = enc_type;
		const data = myTable.row($(this).parent().parent()).data();
		idps = data.id;
		preguntas_rp(enc_type, data.id);
	});

	$('#tabla_solicitudes').on('click', 'tbody tr .do_satis_enc', async function () {
		idps = "";
		const data = myTable.row($(this).parent().parent()).data();
		let encList = await lista_encuestas(data.id);
		idps = data.id;
		mostrar_encuesta(data.id, encList);
	});

	/* Dar check a fechas establecidas por personal de compras */
	$('#tabla_solicitudes').on('click', 'tbody tr .fechas_check', async function () {
		const data = myTable.row($(this).parent().parent()).data();
		/*let chk = await check_estado_entregables('', data.id);
		if (chk == 1) {
			titulo = `¡Atención!`;
			mensaje = `Usted ha aprobado todas las entregas de servicio satisfactoriamente; no obstante ¿Desea revisar su cronograma antes de continuar?`;
			tipo = `warning`;
			sibtn = `Si, revisar`;
			nobtn = `No, continuar`;
			let choice = await confirm_action(titulo, mensaje, tipo, sibtn, nobtn);
			if (choice == 1) {
				swal.close();
				compra_crono(true);
			} else {
				Mostrar_estados_siguientes(14);
			}
		} else {*/
			compra_crono(true);
		//}
	});

	/* Vista de checks dados por el cliente */
	$('#tabla_solicitudes').on('click', 'tbody tr .admin_crono_check', async function () {
		const data = myTable.row($(this).parent().parent()).data();
		/*let chk = await check_estado_entregables('', data.id);
		if (chk == 1) {
			titulo = `¡Atención!`;
			mensaje = `El <u>Solicitante</u> ha aprobado todas las entregas de servicio satisfactoriamente; no obstante ¿Desea revisar el cronograma antes de continuar?`;
			tipo = `warning`;
			sibtn = `Si, revisar`;
			nobtn = `No, continuar`;
			let choice = await confirm_action(titulo, mensaje, tipo, sibtn, nobtn);
			if (choice == 1) {
				swal.close();
				compra_crono(true);
			} else {
				Mostrar_estados_siguientes(14);
			}
		} else {*/
			compra_crono(true);
		//}
	});

	$('#tabla_solicitudes').on('click', 'tbody tr .pasar_pendiente', function () {
		Mostrar_estados_siguientes(8);
	});

	/* Fin de eventos de btns de encuestas RP */

	$('#tabla_solicitudes').on('dblclick', 'tr', function () {
		idps = "";
		const data = myTable.row(this).data();
		llenar_tabla_detalles(data);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr', function () {
		idps = "";
		const data = myTable.row(this).data();
		idsolicitud = data.id;
		idps = data.id_solicitante;
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span', function () {
		idps = "";
		tipo_enc_selected = "";
		idCurrentSoli = "";
		const data = myTable.row($(this).parent().parent()).data();
		tipo_enc_selected = data.enc_type;
		idCurrentSoli = data.id;
		idsolicitud = data.id;
	});

	$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
		idSolicitud = "";
		const data = myTable.row($(this).parent()).data();
		idSolicitud = data.id;
		tipo_orden_sel = data.id_tipo_orden;
		llenar_tabla_detalles(data);
	});
	Con_filtros(sw);
}

function Con_filtros(sw) {
	if (sw) {
		$(".mensaje-filtro").show("fast");
	} else {
		$(".mensaje-filtro").css("display", "none");
	}
}

function llenar_tabla_detalles(datos) {
	idSolicitud = datos.id;
	id_ver_comentarios = datos.id;
	let { nombre_comite, id_estado_solicitud } = datos;
	if (datos.num_orden != null) {
		if (id_estado_solicitud == "Soli_Fin") $("#editar_cod_orden").css("display", "none");
		else $("#editar_cod_orden").show("fast");

		if (id_estado_solicitud == "Soli_Fin" || id_estado_solicitud == "Soli_Pen" || id_estado_solicitud == "Ser_Rec" || id_estado_solicitud == "Soli_Dev" || id_estado_solicitud == "Soli_Par") {
			$("#editar_tiempo_entrega").css("display", "none");
			$("#cambiar_proveedor").css("display", "none");
		} else {
			$("#editar_tiempo_entrega").show("fast");
			$("#cambiar_proveedor").show("fast");
		}

		$(".valor_orden_cod").html(datos.num_orden);
		$(".valor_proveedor").html(datos.proveedor);
		$(".valor_tipo_orden").html(datos.tipo_orden);
		$('#select_cambiar_apoyo').val(datos.id_proveedor)
		$(".sin_info").show("fast");
	} else {
		$(".sin_info").css("display", "none");
	}
	$(".tr_valor_obs_devolucion").css("display", "none");
	if (datos.obs_devolucion) {
		$(".valor_obs_devolucion").html(datos.obs_devolucion);
		$(".tr_valor_obs_devolucion").show("fast");
	}

	$(".tr_valor_causal_dev").css("display", "none");
	if (datos.causal_dev != null) {
		$(".valor_causal_dev").html(datos.causal_dev);
		$(".tr_valor_causal_dev").show("fast");
	}

	$(".valor_nombre").html(datos.nombre_solicitud);
	$(".valor_tipo_sol").html(datos.tipo_compra);
	$(".valor_cargo_sap").html(datos.cargo_sap);
	$(".valor_solicitante").html(datos.solicitante);
	$(".valor_jefe").html(datos.jefe2);
	$(".valor_fecha_registro").html(datos.fecha_registra);
	$(".valor_fecha_solicitud").html(datos.fecha_solicitud);
	$(".valor_estado_sol").html(datos.estado_solicitud);
	$(".valor_observaciones").html(datos.observaciones);
	$(".valor_nombre_comite").html(datos.nombre_comite);

	if (nombre_comite) $(".tr_nombre_comite").show("fast");
	else $(".tr_nombre_comite").hide("fast");

	/*if(datos.id_tipo_orden == 'Tip_Ser' && datos.numero_entregables != null && datos.numero_entregables != '') $(".tr_cronogramas").show("fast");
	else */$(".tr_cronogramas").hide("fast");

	$(".valor_gestion_habiles").html("----");
	$(".valor_gestion_enges").html("----");
	$(".valor_fe_real").html("----");
	$(".valor_fe_estimada_real").html("----");
	$(".valor_fe_estimada").html("----");
	$(".valor_enc_pre_1").html("-----");
	$(".valor_enc_pre_2").html("----");
	$(".valor_enc_pre_3").html("----");
	$(".valor_enc_pre_4").html("----");
	$(".resultado_final_rp").html("----");
	if (datos.tiempo_gestion != null) {
		$(".valor_gestion_habiles").html(datos.tiempo_habil);
		$(".valor_gestion_enges").html(datos.tiempo_gestion);
	}
	if (datos.fecha_entrega_real != null) {
		$(".valor_fe_real").html(datos.fecha_entrega_real);
	}
	if (datos.fecha_entrega_est != null) {
		$(".valor_fe_estimada").html(datos.fecha_entrega_est + " Días");
	}
	if (datos.estimada_real != null) {
		$(".valor_fe_estimada_real").html(datos.estimada_real);
	}
	if (datos.resultado_final_rp != null) {
		$(".resultado_final_rp").html(datos.resultado_final_rp);
	}
	if (datos.fecha_fin_encuesta != null) {
		$(".valor_enc_pre_1").html(datos.res1_encuesta);
		$(".valor_enc_pre_2").html(datos.res2_encuesta);
		$(".valor_enc_pre_3").html(datos.res3_encuesta);
		$(".valor_enc_pre_4").html(datos.obs_encuesta);
	}
	$("#modalArticulos_Solicitud").modal();
	Listar_articulos(datos.id);
}

function guardar_articulo(articulo) {
	let formData = new FormData(document.getElementById("Agregar_Articulos"));
	formData.append("id_solicitud", idsolicitud);
	formData.append("tipo", guardar);
	$.ajax({
		url: server + "index.php/compras_control/guardar_articulo",
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
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		}
		if (datos == 0) {
			MensajeConClase("Articulo asignado con exito.", "success", "Proceso Exitoso!");
			$("#Agregar_Articulos").get(0).reset();
			$("#cod_orden_sele").html("Seleccione Código SAP");
			Listar_articulos(idsolicitud);
			return;
		} else if (datos == 2) {
			let {
				nombre_art,
				cantidad_art,
				marca_art,
				referencia_art,
				codigo_orden,
				observaciones,
				con_tarjeta,
				fecha_compra_tarjeta
			} = articulo;
			articulos_sele.push({
				"nombre_art": nombre_art,
				"cantidad_art": cantidad_art,
				"marca_art": marca_art,
				"referencia_art": referencia_art,
				"codigo_orden": codigo_orden,
				"id": id_articulo_add_soli,
				"observaciones": observaciones,
				'fecha_compra_tarjeta': con_tarjeta ? fecha_compra_tarjeta : '',
				'con_tarjeta': con_tarjeta ? 1 : 0,
				"texto_code": $("#cod_orden_sele").html(),

			});
			id_articulo_add_soli++;
			$("#Agregar_Articulos").get(0).reset();
			$("#Agregar_Articulos input[name='fecha_compra_tarjeta']").removeAttr("required", "true");
			$("#container_con_tarjeta").hide('slow');
			$("#con_tarjeta").addClass("funkyradio-success")
			$("#cod_orden_sele").html("Seleccione Código SAP");
			if (modificando_ini == 1) {
				MensajeConClase("", "success", "Artículo Modificado");
				modificar_articulo_solicitud();
				Listar_articulos_solicitados(articulos_sele);

				return;
			}
			Listar_articulos_solicitados(articulos_sele);
			MensajeConClase("", "success", "Artículo Guardado");
			$("#input_codigo_orden").val("");
			return;
		} else if (datos == -1) {
			MensajeConClase("Seleccione solicitud a la cual se le asignara el nuevo articulo", "info", "Oops...");
			return;
		} else if (datos == -2) {
			MensajeConClase("Seleccione Codigo SAP", "info", "Oops...");
			return;
		} else if (datos == -3) {
			MensajeConClase("Ingrese cantidad", "info", "Oops...");
			return;
		} else if (datos == -4) {
			MensajeConClase("Ingrese Referencia", "info", "Oops...");
			return;
		} else if (datos == -5) {
			MensajeConClase("Ingrese solo numeros en la cantidad", "info", "Oops...");
			return;
		} else if (datos == -6) {
			MensajeConClase("Ingrese solo numeros mayor a 1 en la cantidad", "info", "Oops...");
			return;
		} else if (datos == -7) {
			MensajeConClase("La solicitud ya se encuentra en proceso o no esta autorizado para realizar esta operación.", "info", "Oops...");
			return;
		} else if (datos == -8) {
			MensajeConClase("Ingrese nombre del articulo", "info", "Oops...");
			return;
		} else if (datos == -9) {
			MensajeConClase("Ingrese Marca del articulo", "info", "Oops...");
			return;
		} else if (datos == -10) {
			MensajeConClase("Ingrese Descripción", "info", "Oops...");
			return;
		} else if (datos == -11) {
			MensajeConClase("Ingrese una fecha de compra valida y superior a la fecha actual.", "info", "Oops...");
			return;
		}
	});
}

function eliminar_articulo(id) {
	swal({
		title: "Retirar Articulo..?",
		text: "Si desea continuar con el retiro del artículo en la solicitud seleccionada, por favor presionar la opción de 'Continuar'",
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
				articulo_eliminar(id);
			}
		});
}

function articulo_eliminar(id) {
	$.ajax({
		url: server + "index.php/compras_control/eliminar_articulo",
		type: "post",
		data: {
			id,
			idsolicitud
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
			return;
		}

		if (datos == 1) {
			//MensajeConClase("El articulo fue retirado con exito.", "success", "Proceso Exitoso!");
			swal.close();
			Listar_articulos(idsolicitud);
		} else if (datos == -6) {
			MensajeConClase("La solicitud no puede quedar sin articulos, Tener en cuenta que puede modificar el articulo seleccionado.", "info", "Oops...");
			return;
		} else if (datos == -7) {
			MensajeConClase("La solicitud ya se encuentra en proceso o no esta autorizado para realizar esta operación.", "info", "Oops...");
			return;
		}
	});
	return;
}

function traer_articulo(id) {
	$.ajax({
		url: server + "index.php/compras_control/traer_articulo",
		type: "post",
		data: {
			id
		},
		dataType: "json",
	}).done(function (datos) {
		$("#Editar_Articulo").get(0).reset();
		modificando_articulo = 1;
		idarticulo = datos[0].id;
		$("#txtinput_codigo_orden").val(datos[0].vpid);
		$("#cod_sap").html(datos[0].valor);
		$("#txtnom_art").val(datos[0].nombre_articulo);
		$("#txtcant").val(datos[0].cantidad);
		$("#txtmarca").val(datos[0].marca);
		$("#txtref").val(datos[0].referencia);
		$("#txtobservaciones_art").val(datos[0].observaciones);
		$("#fecha_compra_tarjeta_modi").val(datos[0].fecha_compra_tarjeta);
		if (datos[0].fecha_compra_tarjeta != null) {
			$("#con_tarjeta_modi").prop("checked", true);
			$("#container_con_tarjeta_modi").show('fast');
			$("#Editar_Articulo input[name='fecha_compra_tarjeta']").attr("required", "true");
		} else {
			$("#con_tarjeta_modi").prop("checked", false);
			$("#Editar_Articulo input[name='fecha_compra_tarjeta']").removeAttr("required", "true");
			$("#container_con_tarjeta_modi").hide('fast');
		}
		$("#Editar_Articulos").modal();
	});
}

function modificar_articulo(id_articulo) {
	let formData = new FormData(document.getElementById("Editar_Articulo"));
	formData.append('id_articulo', id_articulo);
	formData.append('id_solicitud', idsolicitud);
	$.ajax({
		url: server + "index.php/compras_control/modificar_articulo",
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
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		}
		if (datos == 0) {
			modificando_articulo = 0;
			MensajeConClase("Los datos del articulo fueron modificados con exito.", "success", "Proceso Exitoso!");
			Listar_articulos(idsolicitud);
			$("#Editar_Articulos").modal("hide");
			$("#Agregar_Articulos").get(0).reset();
			$("#cod_orden_sele").html("Seleccione Código SAP");
		} else if (datos == -1) {
			MensajeConClase("Seleccione solicitud a la cual se le asignara el nuevo articulo", "info", "Oops...");
			return;
		} else if (datos == -2) {
			MensajeConClase("Seleccione Codigo SAP ", "info", "Oops...");
			return;
		} else if (datos == -3) {
			MensajeConClase("Ingrese cantidad", "info", "Oops...");
			return;
		} else if (datos == -4) {
			MensajeConClase("Ingrese Referencia", "info", "Oops...");
			return;
		} else if (datos == -5) {
			MensajeConClase("Ingrese solo numeros en la cantidad", "info", "Oops...");
			return;
		} else if (datos == -6) {
			MensajeConClase("Ingrese solo numeros mayor a 1 en la cantidad", "info", "Oops...");
			return;
		} else if (datos == -7) {
			MensajeConClase("La solicitud ya se encuentra en proceso o no esta autorizado para realizar esta operación.", "info", "Oops...");
			return;
		} else if (datos == -8) {
			MensajeConClase("Ingrese nombre del articulo", "info", "Oops...");
			return;
		} else if (datos == -9) {
			MensajeConClase("Ingrese Marca del articulo", "info", "Oops...");
			return;
		} else if (datos == -10) {
			MensajeConClase("Ingrese Descripción", "info", "Oops...");
			return;
		} else if (datos == -11) {
			MensajeConClase("Ingrese una fecha de compra valida y superior a la fecha actual.", "info", "Oops...");
			return;
		}

	});
}

function buscar_codigo(nom_cod) {
	if (nom_cod.length == 0) {
		MensajeConClase("Ingrese código a buscar", "info", "Ups!");
		return;
	}
	idcodigo = 0;
	$('#tabla_codigos tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .seleccionar');
	let myTable = $("#tabla_codigos").DataTable({
		"destroy": true,
		"searching": false,
		"ajax": {
			url: server + "index.php/compras_control/buscar_codigo_sap",
			data: {
				nom_cod
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
			dataType: "json",
			type: "post",
		},
		"processing": true,
		"columns": [{
			"data": "codigo"
		},
		{
			"data": "descripcion"
		},
		{ 'defaultContent': '<span style="color: #39B23B;" title="Seleccionar Codigo" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>' },
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_codigos tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		seleccionar_codigo(data);
	});

	$('#tabla_codigos tbody').on('click', 'tr td .seleccionar', function () {
		let data = myTable.row($(this).parent().parent()).data();
		seleccionar_codigo(data);
	});
	const seleccionar_codigo = data => {
		let id = data.id;
		let idcodigo = data.codigo;
		$("#input_codigo_orden").val(id);
		$("#txtinput_codigo_orden").val(id);
		$("#cod_orden_sele").html(idcodigo);
		$(".cod_sel").html(idcodigo);
		$("#texto_code").html(idcodigo);
		$("#Buscar_Codigo").modal('hide');
	}
}

function listar_Personas_jefe(dato) {
	idpersona_seleccionada_re = 0;
	$('#tabla_peronas_jefe tbody').off('click', 'tr');
	$('#tabla_peronas_jefe tbody').off('dblclick', 'tr');
	var table = $("#tabla_peronas_jefe").DataTable({
		"destroy": true,
		searching: false,
		"ajax": {
			url: server + "index.php/personas_control/Cargar_personas_Dato",
			dataType: "json",
			type: "post",
			data: {
				dato
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
			"data": "nombre"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "correo"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_peronas_jefe tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_peronas_jefe tbody tr").removeClass("success");
		$(this).attr("class", "success");
		idpersona_seleccionada_re = data.id;
		correo_soli = data.correo;
		if (modificando == 1) {
			$("#mod_id_jefe").html(data.nombre);
			$("#mod_jefe_area").val(data.id);
		} else {
			$("#spanjefe_area").html(data.nombre);
			$("#input_sele_jefe").val(data.id);
		}
		$("#Modal_jefe").modal("hide");
	});
}

function cargar_articulos(idsolicitud, combo, mensaje) {
	$.ajax({
		url: server + "index.php/compras_control/cargar_articulos/1",
		dataType: "json",
		data: {
			idsolicitud
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			$(combo).html("");
			$(combo).append("<option value=''>" + mensaje + "</option>");
			for (var i = 0; i <= datos.length - 1; i++) {
				$(combo).append("<option   value= " + datos[i].id + ">" + datos[i].cantidad + " " + datos[i].nombre_articulo + " " + datos[i].marca + "</option>");
			};
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});
}

function traer_solicitud(id, data = -1, tipo = -1) {
	$.ajax({
		url: server + "index.php/compras_control/traer_solicitud",
		dataType: "json",
		data: {
			id,
			tipo
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (tipo == 1) {
				llenar_tabla_detalles_notificacion(datos);
				return;
			}
			if (data == 1) {
				$("#cbxtipo_compra").val(datos[0].id_tipo_compra);
				$("#input_sele_jefe").val(datos[0].id_jefe_area);
				$("#txt_observaciones").val(datos[0].observaciones);
				$("#cbxdepartamento").val(datos[0].id_departamento);
				$("#spanjefe_area").html(datos[0].jefe);
				$("#myModal").modal();
				MensajeConClase("Tener en cuenta que es necesario adjuntar los soportes para su solicitud nuevamente.!", "success", "Copia Creada!");
			}
			$("#cbxmod_tipo_compra").val(datos[0].id_tipo_compra);
			$("#mod_id_jefe").val(datos[0].id_jefe_area);
			$("#mod_nombre_solicitud").val(datos[0].nombre_solicitud);
			$("#mod_txtobservaciones").val(datos[0].observaciones);
			$("#cbxmod_departamento").val(datos[0].id_departamento);
			$("#mod_id_jefe").val(datos[0].id_jefe_area);
			$("#mod_jefe_area").html(datos[0].jefe);
			$("#comites_compras_modi").val(datos[0].id_comite);
			$("#descripcion_cmt_modi").val(datos[0].descripcion_cmt);
			$("#observaciones_cmt_modi").html(datos[0].observaciones_cmt);
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function traer_articulos_copia(id) {
	articulos_sele = [];
	$.ajax({
		url: server + "index.php/compras_control/traer_articulos_copia",
		type: "post",
		data: {
			id
		},
		dataType: "json",
	}).done(function (datos) {
		id_articulo_add_soli = 0;
		for (let index = 0; index < datos.length; index++) {
			let {
				nombre_articulo,
				cantidad,
				marca,
				referencia,
				cod_sap,
				observaciones,
				valor,
				fecha_compra_tarjeta
			} = datos[index];
			articulos_sele.push({
				"nombre_art": nombre_articulo,
				"cantidad_art": cantidad,
				"marca_art": marca,
				"referencia_art": referencia,
				"codigo_orden": cod_sap,
				"id": id_articulo_add_soli,
				"observaciones": observaciones,
				"texto_code": valor,
				"fecha_compra_tarjeta": fecha_compra_tarjeta

			});
			id_articulo_add_soli++;
		}
		Listar_articulos_solicitados(articulos_sele);
		$("#myModal").modal();
		MensajeConClase("Tener en cuenta que es necesario adjuntar nuevamente los soportes para su solicitud.!", "success", "Copia Creada!");
	});
}

function traer_proveedor_solicitud(id) {
	$.ajax({
		url: server + "index.php/compras_control/traer_proveedor_solicitud",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}

			$("#form_modificar_proveedor_solicitud input[name='nombre']").val(datos[0].nombre);
			$("#form_modificar_proveedor_solicitud input[name='iva']").val(datos[0].iva);

			if (datos[0].precio_dolar != null) {
				$("#form_modificar_proveedor_solicitud input[name='valor_total']").val(datos[0].valor_dolar);
				$("#form_modificar_proveedor_solicitud input[name='precio_dolar']").val(datos[0].precio_dolar);
				$("#moneda_modi").val('usd');
				$("#precio_dolar_modi").show("fast");
				$("#precio_dolar_modi").attr("required", "true");

			} else {
				$("#precio_dolar_modi").hide("fast");
				$("#precio_dolar_modi").removeAttr("required", "true");
				$("#moneda_modi").val('cop');
				$("#form_modificar_proveedor_solicitud input[name='valor_total']").val(datos[0].valor_total);
			}


			if (datos[0].administracion != null || datos[0].utilidad != null || datos[0].imprevistos != null) {
				$("#otras_cargas_modi").show("fast");
				$("#mostar_otras_cargas_modi span").removeClass("fa fa-plus");
				$("#mostar_otras_cargas_modi span").addClass("fa fa-minus");
				$("#otras_cargas_modi input").attr("required", "true");
				$("#form_modificar_proveedor_solicitud input[name='administracion']").val(datos[0].administracion);
				$("#form_modificar_proveedor_solicitud input[name='imprevistos']").val(datos[0].imprevistos);
				$("#form_modificar_proveedor_solicitud input[name='utilidad']").val(datos[0].utilidad);
				conceptos_modi = 1;

			} else {
				$("#otras_cargas_modi").hide("slow");
				$("#mostar_otras_cargas_modi span").removeClass("fa fa-minus");
				$("#mostar_otras_cargas_modi span").addClass("fa fa-plus");
				$("#otras_cargas_modi input").removeAttr("required", "true");
				conceptos_modi = 0;
			}

		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function Mostrar_estados_siguientes(estado_act) {
	$("#campo_or_comp").css("display", "none");
	$("#campo_comi").css("display", "none");
	$("#campo_par").css("display", "none");
	$("#tipo_compra_asi").css("display", "none");
	$("#causal_dev").css("display", "none");

	$("#gestionar_solicitud").get(0).reset();
	const estados_sig = estados_siguientes(estado_act);
	$("#estados_siguientes").html("<option value=''>Seleccione Estado</option>");
	for (var j = 0; j < estados_sig.length; j++) {
		for (var i = 0; i < estados_disponibles.length; i++) {
			if (estados_sig[j] == estados_disponibles[i].id_aux) {
				$("#estados_siguientes").append("<option value='" + estados_disponibles[i].id_aux + "'>" + estados_disponibles[i].valor + "</option>");
				i = i + estados_disponibles.length;
			}
		}
	}
	$("#Modal_gestionar_solicitud").modal("show");
}

/* Check si la solicitud en cuestion ya realizo las encuestas pertinentes. */
const respuestas_rp = () => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}listar_respuestas_rp`, "", async resp => {
			resolve(resp);
		})
	})
}

const preguntas_rp = async (tipo_encuesta = "", idsolicitud = "") => {
	let rptas = await respuestas_rp();
	let iDsol = '';
	idsolicitud == '' ? iDsol = massive_idsols : iDsol = idsolicitud;
	let encList = await lista_encuestas(iDsol);

	consulta_ajax(`${ruta_interna}preguntas_rp`, { tipo_encuesta, idsolicitud }, res => {

		if (res.tipo == "info" || res.tipo == "error") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		} else {

			$("#modal_encuestas_rp").modal();
			$("#tabla_enc_rp").html("");

			for (let x = 0; x < res.length; x++) {
				$("#tabla_enc_rp").append(`
				<div id="preguntas${x + 1}" class="preguntass">
					<p class="text-center" data-id="question" data-id_enc_type="${res[x].id_tipo_encuesta}" data-tipo="${res[x].tipo}" id="${res[x].id}" style="font-size:large;">${x + 1}. ${res[x].pregunta}</p>
					<div class="respuestass text-center">
					</div>
					<div style="width: 50%; margin: auto;">
						<textarea class="form-control obs oculto" disabled value="NULL" data-preg="${x + 1}" data-resp="" required name="obs_${x + 1}" id="obs_${x + 1}" placeholder="Ingrese observación..."></textarea>
					</div>
					<hr>
				</div>
				`);

				for (let r = 0; r < rptas.length; r++) {
					$(`#preguntas${x + 1} .respuestass`).append(
						`
						<span style="font-size:large; margin: auto; padding:2%;">
							<input class="form-check-input" data-id="ress_selected" style="margin-right:2.5%;" data-num_pre="${x + 1}" data-res="${rptas[r].respuesta}" id="preg${x + 1}_res_${rptas[r].respuesta}" type="radio" name="respuestaa${x + 1}" value="${rptas[r].id}" required>
							<label for="preg${x + 1}_res_${rptas[r].respuesta}">${rptas[r].respuesta}</label>
						</span>
						`
					);
				}
			}
		}

		/* Eventos para las preguntas con menos de 3 */
		$("#modal_encuestas_rp input").click(function () {
			let preg_num = $(this).attr("data-num_pre");
			let n = $(this).attr("data-res");
			let res_selected = $(this).attr("data-res");
			if (n <= 3) {
				$(`#obs_${preg_num}`).removeClass('oculto').attr("disabled", false).attr("data-resp", res_selected);
			} else {
				$(`#obs_${preg_num}`).addClass('oculto').attr("disabled", true).attr("data-resp", res_selected).val("");
			}
		});

		/* Renderiza las encuesta asiganadas que tiene el usuario y la cual esta gestinando en el momento. */
		$(`#modal_encuestas_rp .encs_nav`).html('');
		let complete = '<span class="fa fa-check red"></span>';
		let incomplete = '<span class="fa fa-hourglass-2 red"></span>';
		let status_icon = '';
		let targets = [`#modal_encuestas_rp .encs_nav`, `#modal_encuesta_usuario .encs_nav`];

		encList.forEach(element => {
			element.estado == 'incomplete' ? status_icon = incomplete : status_icon = complete;
			$(`#modal_encuestas_rp .encs_nav`).append(`<li class="pointer" data-id="${element.idaux}"><a>${status_icon} ${element.area}</a></li>`);
		});
		nav_encs(targets, encuesta_activa);
	});
}

const Gestionar_solicitud = async (id, motivo = "", estadopasa) => {
	await realizar_gestion(id, motivo, estadopasa);
	async function realizar_gestion(id, motivo, estadopasa) {
		MensajeConClase("validando info", "add_inv", "Oops...");
		let formData = new FormData(document.getElementById("gestionar_solicitud"));
		formData.append("id", id);
		formData.append("motivo", motivo);
		formData.append("id_solicitante", idps);
		$.ajax({
			url: server + "index.php/compras_control/Gestionar_solicitud",
			type: "post",
			dataType: "json",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (datos) {
				if (datos == "sin_session") {
					close();
					return;
				}
				if (datos == -1302) {
					MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
					return;
				} else if (datos.tipo == "error") {
					MensajeConClase(datos.mensaje, datos.tipo, datos.titulo);
					return;
				} else if (datos == -1) {
					MensajeConClase("Error al gestionar la solicitud, contacte con el administrador", "error", "Oops...");
					return;
				} else if (datos == -2) {
					MensajeConClase("Seleccione Estado Nuevo", "info", "Oops...");
					return;
				} else if (datos == -3) {
					MensajeConClase("Seleccione Proveedor", "info", "Oops...");
					return;
				} else if (datos == -4) {
					MensajeConClase("Ingrese codigo de orden", "info", "Oops...");
					return;
				} else if (datos == -5) {
					MensajeConClase("Ingrese dias de entrega estimados", "info", "Oops...");
					return;
				} else if (datos == -6) {
					MensajeConClase("Ingrese solo numeros en los dias de entrega estimados", "info", "Oops...");
					return;
				} else if (datos == -7) {
					MensajeConClase("Ingrese Descripcion", "info", "Oops...");
					return true;
				} else if (datos == -8) {
					MensajeConClase("Ingrese todas las cantidades", "info", "Oops...");
					return true;
				} else if (datos == -9) {
					MensajeConClase("Ingrese solo datos numericos en las cantidades", "info", "Oops...");
					return true;
				} else if (datos == -10) {
					MensajeConClase("La cantidad ingresada no puede ser mayor que la solicitada", "info", "Oops...");
					return true;
				} else if (datos == -11) {
					MensajeConClase("No fue posible cambiar el estado ya que  otro usuario gestiono la solicitud y se encuentra en otro proceso, por favor refresque los datos y valide la información.!", "info", "Oops...");
					return;
				} else if (datos == -12) {
					MensajeConClase("Se esta haciendo entrega de todos los artículos, para este caso debe pasar a estado finalizado directamente", "info", "Oops...");
					return true;
				} else if (datos == -13) {
					MensajeConClase("Seleccione tipo de orden de compra", "info", "Oops...");
					return true;
				} else if (datos == -14) {
					MensajeConClase("Seleccione causal de devolucion", "info", "Oops...");
					return true;
				} else if (datos == -122) {
					MensajeConClase("La solicitud ya paso por el estado seleccionado por tal motivo no es posible continuar.", "info", "Oops...");
					return true;
				} else if (datos == -123) {
					MensajeConClase("El servicio no ha sido recibido por el solicitante, por tal motivo no es posible continuar.", "info", "Oops...");
					return true;
				} else if (datos == 1) {
					//MensajeConClase("", "success", "Estado Modificado..!");
					swal.close();
					let enviar = datos_gestion;
					Listar_solicitudes(id);
					$("#Modal_gestionar_solicitud").modal("hide");
					$("#Modal_listar_proveedores_articulo").modal("hide");
					$("#Modal_Entregas_parciales").modal("hide");
					if (estadopasa == "Soli_Dev" || estadopasa == "Soli_Fin" || (estadopasa == 'Soli_Pen' && enviar.id_tipo_orden == 'Tip_Ser')) {
						envio_correo(enviar, estadopasa, id, motivo);
					} else if (estadopasa == "Soli_Com") {
						traer_correos_comite_compras_tipo2(id);
					}
					return;
				}
			},
			error: function (error) {
				console.log('Something went wrong', status, error);
			}
		});
	}
}

const envio_correo = async (datos, estadopasa, id_solicitud, motivo) => {
	let mensaje = "";
	let adj = "";
	let ser = '<a href="' + server + 'index.php/compras/' + id_solicitud + '"><b>agil.cuc.edu.co</b></a>'
	let {correo : correos_persona, persona : nombre_persona, id_tipo_orden} = datos
	let tipo_correo = 1;
	adj = "Solicitud de Compra";

	if (estadopasa == "Soli_Dev") {
		info_causal = await buscar_datos_valor_parametro(causal, 2);
		mensaje = `Se informa que su solicitud ha sido devuelta por el siguiente causal : ${info_causal.valor} ${(info_causal.valorx != 'Ninguna' ? info_causal.valorx : '')} <br>${motivo}<br><br>Mas informaci&oacuten en ${ser}`;
	} else if (estadopasa == "Soli_Pen") {
		mensaje = `Su solicitud se encuentra pendiente por entrega, en cuanto el proveedor haya entregado el servicio solicitado agradecemos que envi&eacute su recibido a satisfacci&oacuten para finalizar este proceso.<br><br>Mas informaci&oacuten en ${ser}`;
	}
	/* else if (estadopasa == "Ser_Rec") {
		adj = "Solicitud Gestionada Compras";
		nombre_persona = 'Funcionario Compras';
		correos_persona = await obtener_correos_permiso(datos.tipo_sol, estadopasa);
		tipo_correo = 3;
		mensaje = `Se informa que la solicitud realizada por ${datos.persona}, ha recibido el servicio solicitado. Puede verificar la informacion ingresando al aplicativo AGIL ${ser} .`;
	} */
	else {
		mensaje = `Se informa que su solicitud ha finalizado${id_tipo_orden === 'Tip_Mat' ? ' y la mercancía se encuentra en el departamento de almacén' : ''}. A partir de este momento tiene 24 horas para dar respuesta a la encuesta, de lo contrario se asumirá que recibió todo a conformidad.<br><br>Mas informaci&oacuten en ${ser}`;
	}
	enviar_correo_personalizado("comp", mensaje, correos_persona, nombre_persona, "Compras CUC", adj, "ParCodAdm", tipo_correo);
}


function asignar_solicitud_comite() {
	var formData = new FormData(document.getElementById("asignar_solicitud_comite"));
	formData.append("id_solicitud", idsolicitud);
	$.ajax({
		url: server + "index.php/compras_control/asignar_solicitud_comite",
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
		} else if (datos == 0) {
			$("#asignar_solicitud_comite").get(0).reset();
			//MensajeConClase("Articulo Asignado", "success", "Proceso Exitoso!");
			swal.close();
			$("#Modal_asignar_solcitud_comite").modal("hide");
			$("#Modal_gestionar_solicitud").modal("hide");
			asignar_proveedor(idsolicitud)
			//Listar_articulos(i, 1);

			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Asignar el articulo al Comité", "error", "Oops...");
		}
	});
}


const Obtener_estados_compras = (datos) => estados_disponibles = datos;
const estados_siguientes = (estadoACT) => {
	const estados = [
		//REVICION
		["Soli_Rec", "Soli_Dev"],
		//RECIBIDO
		["Soli_Cot", "Soli_Cac", "Soli_Pre", "Soli_Pro", "Soli_Com", "Soli_Oco", "Soli_Dev"],
		//COTIZACION
		["Soli_Pro", "Soli_Pre", "Soli_Com", "Soli_Cac", "Soli_Oco", "Soli_Dev"],
		//CREACION DE ACTIVO
		["Soli_Oco", "Soli_Dev"],
		//PRESUPUESTO
		["Soli_Cot", "Soli_Com", "Soli_Pro", "Soli_Cac", "Soli_Oco", "Soli_Dev"],
		//CREACION DE PROVEEDORES
		["Soli_Pre", "Soli_Com", "Soli_Cac", "Soli_Oco", "Soli_Dev"],
		//COMITE COMPRAS
		["Soli_Pro", "Soli_Pre", "Soli_Cac", "Soli_Oco", "Soli_Dev"],
		//ORDEN COMPRAS
		["Soli_Mon", "Soli_Lib", "Soli_Dev"],
		//LIBERACION
		["Soli_Ord", "Soli_Pen", "Soli_Pdoc", "Soli_Dev"],
		//PENDIENTE ANTICIPO
		["Soli_Pen", "Soli_Dev"],
		//PENDIENTE ENTREGA
		["Soli_Dev", "Soli_Par", "Soli_Fin"],
		// ENTREGA PARCIAL
		["Soli_Fin"],
		//PENDIENTE DOCUMENTO
		["Soli_Ord", "Soli_Dev"],

		["Soli_Dev"],

		["Ser_Rec"], //["Ser_Rec", "Soli_Par"] 
		//PAGO MONEDA EXTRANJERA
		["Soli_Lib", "Soli_Dev"],

	];
	return estados[estadoACT];
};

function sin_filtros() {
	$("#tipo_compra_filtro").val('');
	$("#estado_filtro").val('');
	$("#fecha_filtro").val('');
	$("#fecha_filtro_2").val('');
	$("#cbxproveedores_ordenn").val('');
	Listar_solicitudes();
}

function listar_responsables_procesos() {
	responsable_sele = "";
	$('#tabla_responsables_proc tbody').off('click', 'tr');
	$('#tabla_responsables_proc tbody').off('dblclick', 'tr');
	var table = $("#tabla_responsables_proc").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_responsables_procesos",
			dataType: "json",
			type: "post",
			data: {
				"persona_selected": id_persona_selected
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
		}, {
			"data": "nombre"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "correo"
		},

		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_responsables_proc tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		responsable_sele = data.nombre;
		$("#tabla_responsables_proc tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

}

function solicitudes_usuario(id) {
	tipo_sol_sele = "";
	usuario_selecc = id;
	$('#tabla_solicitudes_usuario tbody').off('click', 'tr');
	$('#tabla_solicitudes_usuario tbody').off('dblclick', 'tr');
	var table = $("#tabla_solicitudes_usuario").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/solicitudes_usuario",
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
		},
		"processing": true,
		"columns": [{
			"data": "nombre"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_solicitudes_usuario tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		tipo_sol_sele = data.nombre;
		$("#tabla_solicitudes_usuario tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});


	listar_solicitudes_sin_asignar(id);
	$("#Modal_solicitudes_usuario").modal("show");
}

function estados_solicitudes_usuario(id) {
	tipo_estado_sele = "";
	soli_usu_sele = id;
	$('#tabla_estados_solicitudes tbody').off('click', 'tr');
	$('#tabla_estados_solicitudes tbody').off('dblclick', 'tr');
	var table = $("#tabla_estados_solicitudes").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/estados_solicitudes_usuario",
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
		},
		"processing": true,
		"columns": [{
			"data": "nombre"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_estados_solicitudes tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		tipo_estado_sele = data.nombre;
		$("#tabla_estados_solicitudes tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	listar_estados_sin_asignar(id);
	$("#Modal_estados_solicitudes").modal("show");
}

function listar_solicitudes_sin_asignar(idusuario) {
	$.ajax({
		url: server + "index.php/compras_control/listar_solicitudes_sin_asignar",
		dataType: "json",
		data: {
			idusuario
		},
		type: "post",
		success: function (datos) {
			$(".exceptuando").hide("slow");
			if (datos.length == 0) {
				$("#lista_excluida").html("");
				$("#solicitudes_no_asignadas").html("<option value=''>Todas Asignadas</option>");
				return;
			}
			solicitudes_faltantes_usu = [];
			$("#lista_excluida").html("");
			$("#solicitudes_no_asignadas").html("<option value=''>Seleccione Solicitud</option>");
			$("#solicitudes_no_asignadas").append("<option value='excepto'>Todas excepto</option>");
			for (var i = 0; i <= datos.length - 1; i++) {
				solicitudes_faltantes_usu.push(datos[i].id_aux);
				$("#solicitudes_no_asignadas").append("<option value=" + datos[i].id_aux + ">" + datos[i].valor + "</option>");
				$("#lista_excluida").append(`<li class="${datos[i].id_aux}"><span  >${datos[i].valor}</span><span onclick="solicitudes_exceptuadas_lista('${datos[i].id_aux}');" class="fa fa-remove red pointer"></span></li>`);

			}

			;
		},
		error: function () {

			console.log('Something went wrong', status, error);

		}
	});
}

function listar_estados_sin_asignar(id) {
	$.ajax({
		url: server + "index.php/compras_control/listar_estados_sin_asignar",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			$(".exceptuando_estados").hide("slow");
			if (datos.length == 0) {
				$("#lista_excluida_estados").html("");
				$("#estados_no_asignados").html("<option value=''>Todas asignados</option>");
				return;
			}
			estados_faltantes_usu = [];
			$("#lista_excluida_estados").html("");
			$("#estados_no_asignados").html("<option value=''>Seleccione Solicitud</option>");
			$("#estados_no_asignados").append("<option value='excepto'>Todas excepto</option>");
			for (var i = 0; i <= datos.length - 1; i++) {
				estados_faltantes_usu.push(datos[i].id_aux);
				$("#estados_no_asignados").append("<option value=" + datos[i].id_aux + ">" + datos[i].valor + "</option>");
				$("#lista_excluida_estados").append(`<li class="${datos[i].id_aux}"><span  >${datos[i].valor}</span><span onclick="estados_exceptuadas_lista('${datos[i].id_aux}');" class="fa fa-remove red pointer"></span></li>`);

			}

			;
		},
		error: function () {

			console.log('Something went wrong', status, error);

		}
	});
}

function asignar_solicitud_usuario(id_usuario) {

	MensajeConClase("validando info", "add_inv", "Oops...");
	const id_tipo_solicitud = $("#solicitudes_no_asignadas").val();
	$.ajax({
		url: server + "index.php/compras_control/asignar_solicitud_usuario",
		dataType: "json",
		data: {
			id_usuario,
			id_tipo_solicitud,
			solicitudes_exceptuadas
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
				return;
			} else if (datos == -1) {
				MensajeConClase("Error al Asignar la solicitud, contacte con el administrador", "error", "Oops...");
				return;
			} else if (datos == -2) {
				MensajeConClase("Seleccione solicitud", "info", "Oops...");
				return;
			} else if (datos == 1) {
				MensajeConClase("", "success", "Solicitud(es) Asignada(s)..!");
				solicitudes_usuario(id_usuario);
				listar_solicitudes_sin_asignar(id_usuario);
				solicitudes_exceptuadas = [];
				solicitudes_faltantes_usu = [];

				$(".exceptuando").hide("slow");
				return;
			}


		},
		error: function () {

			console.log('Something went wrong', status, error);

		}
	});
}

function asignar_estado_usuario(id) {
	MensajeConClase("validando info", "add_inv", "Oops...");
	const estado = $("#estados_no_asignados").val();
	$.ajax({
		url: server + "index.php/compras_control/asignar_estado_usuario",
		dataType: "json",
		data: {
			id,
			estado,
			estados_exceptuadas
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
				return;
			} else if (datos == -1) {
				MensajeConClase("Error al Asignar la solicitud, contacte con el administrador", "error", "Oops...");
				return;
			} else if (datos == -2) {
				MensajeConClase("Seleccione Estado", "info", "Oops...");
				return;
			} else if (datos == 1) {
				MensajeConClase("", "success", "Estado(s) Asignado(s)..!");
				estados_solicitudes_usuario(id);
				listar_estados_sin_asignar(id);
				estados_exceptuadas = [];
				estados_faltantes_usu = [];
				$(".exceptuando_estados").hide("slow");
				return;
			}


		},
		error: function () {

			console.log('Something went wrong', status, error);

		}
	});
}

function confirmar_cambiar_estado(tipo, id, estado, id_alterno) {



	let mensaje = "Tener en cuenta que,";
	if (tipo == 1) {
		mensaje = mensaje + " al eliminar este tipo de solicitud el usuario seleccionado no podra gestionarlas.";
	} else {
		mensaje = mensaje + " al eliminar este estado el usuario seleccionado no podra gestionarlo.";

	}

	swal({
		title: "Estas Seguro ?",
		text: mensaje,
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
				cambiar_permisos_usuarios_solicitudes(id, estado, tipo, id_alterno);
			}
		});

	return false;
}

function cambiar_permisos_usuarios_solicitudes(id, estado, tipo, id_alterno) {

	$.ajax({
		url: server + "index.php/compras_control/cambiar_permisos_usuarios_solicitudes",
		type: "post",
		dataType: "json",
		data: {
			id,
			estado,
			tipo
		},

		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
				return;
			} else if (datos == -1) {
				MensajeConClase("Error al cambiar los permisos, contacte con el administrador", "error", "Oops...");
				return;
			} else if (datos == -2) {
				MensajeConClase("Seleccione Estado Nuevo", "info", "Oops...");
				return;
			} else if (datos == 1) {

				if (tipo == 1) {
					//MensajeConClase("", "success", "Solicitud Eliminada..!");
					swal.close();
					solicitudes_usuario(id_alterno);
				} else {
					MensajeConClase("", "success", "Estado Eliminado..!");
					estados_solicitudes_usuario(id_alterno);
				}
				return;
			}



		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function mostrar_encuesta(id, lista_encuestas = []) {
	idsolicitud = id;
	actual_pre = 1;

	$("#pregunta4").hide("fast");
	$("#guardar_encuesta").get(0).reset();
	$("#modal_encuesta_usuario").modal("show");

	encuesta_activa = "satis_enc";

	$(`#modal_encuestas_rp .encs_nav`).html('');
	$(`#modal_encuesta_usuario .encs_nav`).html('');

	let complete = '<span class="fa fa-check red"></span>';
	let incomplete = '<span class="fa fa-hourglass-2 red"></span>';
	let status_icon = '';
	let targets = [`#modal_encuestas_rp .encs_nav`, `#modal_encuesta_usuario .encs_nav`];
	let status = '';

	lista_encuestas.forEach(element => {
		element.estado == 'incomplete' ? status_icon = incomplete : status_icon = complete;
		$(`#modal_encuesta_usuario .encs_nav`).append(`<li class="pointer" data-id="${element.idaux}"><a>${status_icon} ${element.area}</a></li>`);
		status = element.estado;
	});

	if (status = 'complete') {
		idCurrentSoli = '';
	}
	nav_encs(targets, encuesta_activa);

	return false;
}

function guardar_encuesta() {

	let formData = new FormData(document.getElementById("guardar_encuesta"));
	formData.append("id", idsolicitud);
	$.ajax({
		url: server + "index.php/compras_control/guardar_encuesta",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (dato) {

		if (dato == "sin_session") {
			close();
			return;
		}
		if (dato == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operaci&oacuten", "error", "Oops...");
			return;
		}
		if (dato == -1) {
			MensajeConClase("Seleccione calificacion a la pregunta No 1.!", "info", "Oops...");
		} else if (dato == -2) {
			MensajeConClase("Seleccione calificacion a la pregunta No 2.!", "info", "Oops...");
		} else if (dato == -3) {
			MensajeConClase("Seleccione calificacion a la pregunta No 3.!", "info", "Oops...");
		} else if (dato == -4) {
			MensajeConClase("Justifique su respuesta!", "info", "Oops...");
		} else if (dato == 0) {
			MensajeConClase("Encuesta guardada con exito.", "success", "Proceso Exitoso");
			Listar_solicitudes(0, -1, 1);
			$("#guardar_encuesta").get(0).reset();
			$("#modal_encuesta_usuario").modal("hide");
		} else {
			MensajeConClase("Error al enviar la encuesta. Por favor contacte al administrador", "error", "Error!");
		}
	});
}

/* Guardar encuestas rp */
const guardar_encuestas_rp = async (idsol) => {

	let preguntas_array = [];
	let respuestas_array = [];
	let observaciones = [];

	$("p[data-id='question']").each(function () {
		preguntas_array.push($(this).attr("id"));
	});

	$("#tabla_enc_rp input[type='radio']:checked").each(function () {
		respuestas_array.push($(this).val());
	});

	$("#tabla_enc_rp textarea").each(function () {
		let obs = $(this).val();
		if (obs == "") {
			obs = null;
		}
		let num_preg = $(this).attr("data-preg");
		let resp = $(this).attr("data-resp");
		observaciones.push({ "pregunta": num_preg, "respuesta": resp, "obs": obs });
	});

	let tipo = $("#tabla_enc_rp .preguntass p").attr("data-tipo");
	let id_enc_type = $("#tabla_enc_rp .preguntass p").attr("data-id_enc_type");

	let dataToSend = {
		preguntas_array,
		respuestas_array,
		"ids": idCurrentSoli,
		"enc_type": tipo_enc_selected,
		"tipo": tipo,
		"id_enc_type": id_enc_type,
		"obs": observaciones
	}

	consulta_ajax(`${ruta_interna}guardar_encuestas_rp`, dataToSend, async res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			setTimeout(cerrar_swals, 1300);
			$("#encuesta_rp").trigger("reset");
			$("#modal_encuestas_rp").modal("hide");
			let check = Array.isArray(idCurrentSoli);
			check == false ? next_enc(idCurrentSoli) : false;
			tipo_enc_selected = "";
			Listar_solicitudes();
			let valor_buscado = $("#modal_masivos_compras #massives_encs").val();
			massives_rp(valor_buscado);
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});
}

function listar_proveedores() {
	idproveedor = 0;
	$("#modal_modpara_titulo").html(`<span class="fa fa-truck"></span> Modificar Proveedor`);
	$('#tabla_proveedores tbody').off('click', 'tr');
	$('#tabla_proveedores tbody').off('dblclick', 'tr');
	var table = $("#tabla_proveedores").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/genericas_control/Cargar_valor_Parametros/true",
			dataType: "json",
			type: "post",
			data: {
				idparametro: 37
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
			"data": "valor"
		},
		{
			"data": "valorx"
		},
		{
			"data": "op"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones()
	});

	$('#tabla_proveedores tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_proveedores tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		idproveedor = data.id;
	});

}

/* Listar clasificacion de proveedores */
const listar_clasificacion_proveedores = async () => {
	let clasif = await find_idParametro('critico_alto'); //codigo corresponde al idaux el cual me sirve para traer en idparametro
	$("#clasi_proveedor").html(`<option value="">Clasificación de proveedores</option>`);
	consulta_ajax(`${ruta_generica}`, { idparametro: clasif.idpa }, res => {
		res.data.forEach(element => {
			$("#clasi_proveedor").append(`<option value="${element.id}">${element.valor}</option>`);
		});
	});
}

/* Listar seleccion de area */
const listar_seleccion_area = async () => {
	let area = await find_idParametro('no_aplica'); //codigo corresponde al idaux el cual me sirve para traer en idparametro
	$("select[data-id='preg_catego']").html(`<option value="">Seleccione Área</option>`);
	$("#seleccion_area").html(`<option value="">Seleccione Área</option>`);
	consulta_ajax(`${ruta_generica}`, { idparametro: area.idpa }, res => {
		res.data.forEach((element, index) => {
			$("#seleccion_area").append(`
			<option value="${element.id}" data-Tipo_Enc="${element.id_aux}">${element.valor}</option>
			`);
			$("select[data-id='preg_catego']").append(`<option value="${element.id_aux}">${element.valor}</option>`);
		});
	});
	$(".checks").attr("required", false);
}

/* Listar tipo de preguntas RP */
const listar_tipos_preguntasRP = () => {
	tipo_enc_selected = "";
	consulta_ajax(`${ruta_interna}listar_tipos_preguntasRP`, { "idps": id_persona_selected }, res => {
		$('#tabla_mostrar_areas tbody').off('click', 'tr .ver_preguntas');
		$('#tabla_mostrar_areas tbody').off('click', 'tr');
		$('#tabla_mostrar_areas tbody').off('click', 'tr .admin_enc');
		$('#tabla_mostrar_areas tbody').off("click", "tr td .retirar_enc")
		const myTable = $("#tabla_mostrar_areas").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: 'area' },
				{ data: 'accion' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos de la tabla activados */
		$('#tabla_mostrar_areas tbody').on('click', 'tr', function () {
			$("#tabla_mostrar_areas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		/* btn de ver preguntas */
		$('#tabla_mostrar_areas tbody').on('click', 'tr .ver_preguntas', function () {
			let data = myTable.row($(this).parent().parent()).data();
			tipo_enc_selected = data.idaux;
			listar_encuestas_compras();
			$("#Modal_Mostrar_Preguntas").modal();
		});

		/* btn para asignar encuestas a usuarios */
		$('#tabla_mostrar_areas tbody').on('click', 'tr .admin_enc', function () {
			tipo_enc_selected = "";
			let data = myTable.row($(this).parent().parent()).data();
			tipo_enc_selected = data.idaux;
			asignar_encuesta_rp(id_persona_selected, tipo_enc_selected);
		});

		/* Retirar encuestas RP */
		$('#tabla_mostrar_areas tbody').on("click", "tr td .retirar_enc", function () {
			tipo_enc_selected = "";
			let data = myTable.row($(this).parent().parent()).data();
			tipo_enc_selected = data.idaux;

			swal({
				title: "¿Atención?",
				text: "¿De verdad desea retirar la encuesta asignada?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						retirar_encuesta_rp(id_persona_selected, tipo_enc_selected);
						int_id_user = data.id;
					}
				});
		});
	});
}

/* Listar tipos de preguntas para select de masivos */
const listar_catego_rp = () => {
	$("#modal_masivos_compras #massives_encs").html(`<option value="">Filtrar por tipo de encuesta...</option>`);
	consulta_ajax(`${ruta_interna}listar_categos_rp`, { }, res => {
		if (res) {
			res.forEach(element => {
				if (element.id != 'N/A') {
					$("#modal_masivos_compras #massives_encs").append(
						`<option value="${element.idaux}">${element.area}</option>`
					);
				}
			});
		}
	});
}

function registrar_proveedor() {
	var formData = new FormData(document.getElementById("GuardarProveedor"));
	formData.append("idparametro", 37);
	$.ajax({
		url: server + "index.php/genericas_control/guardar_valor_Parametro",
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
		if (datos == 1) {

			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
			return true;

		} else if (datos == 2) {
			$("#GuardarProveedor").get(0).reset();
			MensajeConClase("Proveedor Guardado", "success", "Proceso Exitoso!");
			$("#ValorParmetro").modal("hide")
			listar_proveedores();
			Cargar_parametro_buscado(37, ".cbxproveedores", "Seleccione proveedor");
			return true;

		} else if (datos == 3) {

			MensajeConClase("El Nombre del Proveedor ya esta en el sistema", "info", "Oops...");
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {


			MensajeConClase("Error al Guardar el Proveedor", "error", "Oops...");
		}
	});
}

function Modificar_Proveedor() {
	var formData = new FormData(document.getElementById("ModificarItem"));
	formData.append("idparametro", idproveedor);
	$.ajax({
		url: server + "index.php/genericas_control/Modificar_valor_Parametro",
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
		if (datos == 1) {

			MensajeConClase("Proveedor Modificado con exito", "success", "Proceso Exitoso!");
			$("#ModificarItem").get(0).reset();
			$("#ModalModificarParametro").modal("hide");
			listar_proveedores();
			return false;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else if (datos == 2) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "error", "Oops...");
			return true;
		} else {
			MensajeConClase("Error al Modificar el proveedor", "error", "Oops...");
		}
	});
}

/* Modificar encuestas */
function Modificar_Encuestas() {
	var formData = new FormData(document.getElementById("ModificarItem"));
	formData.append("idparametro", idpregunta);
	$.ajax({
		url: server + "index.php/genericas_control/Modificar_valor_Parametro",
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
		if (datos == 1) {
			MensajeConClase("Encuesta modificada con exito", "success", "Proceso Exitoso!");
			$("#ModificarItem").trigger("reset");
			$("#ModalModificarParametro").modal("hide");
			listar_encuestas_compras();
			return false;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else if (datos == 2) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "error", "Oops...");
			return true;
		} else {
			MensajeConClase("Error al Modificar el encuesta!", "error", "Oops...");
		}
	});
}

function registrar_comite() {
	var formData = new FormData(document.getElementById("form_guardar_comite"));
	$.ajax({
		url: server + "index.php/compras_control/guardar_comite",
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
		if (datos == 1) {
			MensajeConClase("Ingrese Nombre del comité", "info", "Oops...");
			return true;
		} else if (datos == 2) {
			MensajeConClase("Ingrese fecha de cierre del comité", "info", "Oops...");
			return true;
		} else if (datos == 3) {
			MensajeConClase("Ingrese una fecha de cierre del comité valida", "info", "Oops...");
			return true;
		} else if (datos == 4) {
			MensajeConClase("El nombre del comité ya se encuentra registrado", "info", "Oops...");
			return true;
		} else if (datos == 0) {
			$("#form_guardar_comite").get(0).reset();
			$("#comiteadd").modal("hide")
			MensajeConClase("Comité Guardado", "success", "Proceso Exitoso!");
			listar_comites();
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Guardar el Comité", "error", "Oops...");
		}
	});
}

function listar_comites() {
	$('#tabla_comite tbody').off('click', 'tr');
	$('#tabla_comite tbody').off('dblclick', 'tr');
	$('#tabla_comite tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_comite").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_comites",
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
			"data": "codigo"
		},
		{
			"data": "nombre"
		},
		{
			"data": "descripcion"
		},
		{
			"data": "solicitudes"
		},
		{
			"data": "estado_alt"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_comite tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		data_comite = data;
		$("#tabla_comite tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla_comite tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		Listar_solicitudes_en_comite(data.id);
	});

	$('#tabla_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
		const data = table.row($(this).parent()).data();
		Listar_solicitudes_en_comite(data.id);
	});

}

function eliminar_comite(id) {
	$.ajax({
		url: server + "index.php/compras_control/modificar_comite",
		type: "post",
		data: {
			id,
			delete: 1
		},
		dataType: "json",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == 0) {
			//MensajeConClase("Estado Modificado", "success", "Proceso Exitoso!");
			swal.close();
			listar_comites();
			let correos = "";
			let ser = '<a href="' + server + 'index.php/comite/' + id + '"><b>agil.cuc.edu.co</b></a>'
			let mensaje = `Se informa que un nuevo comit&eacute; se encuentra en curso, puede validar la informaci&oacute;n en ${ser}`;
			enviar_correo_personalizado("comp", mensaje, directivos_correos, "COMITÉ COMPRAS", "Solicitud de compras enviada", "Nuevo comite de compras", "ParCodAdm", 3);
			return;
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Eliminar el Comité", "error", "Oops...");
		}
	});
}

function confirm_eliminar_comite(id) {
	swal({
		title: "Estas Seguro ?",
		text: "Tener en cuenta que, al pasar el comité a en curso no podra asignarle solicitudes de compras para su aprobacion.!",
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
				eliminar_comite(id);

			}
		});

	return true;

}

function traer_comite(id) {
	$.ajax({
		url: server + "index.php/compras_control/traer_comite",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			id_com_modi = id;
			$("#nombre_comi_modi").val(datos[0].nombre);
			$("#descripcion_comi_modi").val(datos[0].descripcion);
			$("#fecha_comi_modi").val(datos[0].fecha_cierre);
			$("#comitemodi").modal("show");
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function modifcar_comite() {
	var formData = new FormData(document.getElementById("form_modificar_comite"));
	formData.append("id", id_com_modi);
	$.ajax({
		url: server + "index.php/compras_control/modificar_comite",
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
		if (datos == 1) {
			MensajeConClase("Ingrese Nombre del comité", "info", "Oops...");
			return true;
		} else if (datos == 2) {
			MensajeConClase("Ingrese fecha de cierre del comité", "info", "Oops...");
			return true;
		} else if (datos == 3) {
			MensajeConClase("Ingrese una fecha de cierre del comité valida", "info", "Oops...");
			return true;
		} else if (datos == 0) {
			$("#form_guardar_comite").get(0).reset();
			MensajeConClase("Comité Modificado", "success", "Proceso Exitoso!");
			$("#comitemodi").modal("hide");
			listar_comites();
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Modificar el Comité", "error", "Oops...");
		}
	});
}

function confirmar_eliminar_parametro(id, estado) {
	let msg = "";
	if (lugar_activo == "proveedor") {
		msg = `Tener en cuenta que al Eliminar el proveedor no estara disponible en las solicitudes de compra!`;
	} else if (lugar_activo == "encuesta") {
		msg = `Tenga en cuenta que el aliminar esta pregunta, dejará de estar disponible en las encuestas!`;
	}
	swal({
		title: "¿Está Seguro?",
		text: msg,
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
				eliminar_parametro(id, estado, lugar_activo);
			}
		});
}

function eliminar_parametro(parametro, estado, lugar_activo) {
	$.ajax({
		url: server + "index.php/genericas_control/cambio_estado_parametro",
		dataType: "json",
		data: {
			idparametro: parametro,
			estado: estado
		},
		type: "post",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		if (datos == 1) {
			//MensajeConClase("Proveedor Eliminado con exito", "success", "Proceso Exitoso!");
			swal.close();
			if (lugar_activo == "proveedor") {
				listar_proveedores();
			} else if (lugar_activo == "encuesta") {
				listar_encuestas_compras();
			}
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Eliminar el Proveedor", "error", "Oops...");
		}
	});
}

function mostrar_form_asignar_solcitud_comite(id) {
	idsolicitud = id;
	listar_comites_combo();
	$("#Modal_asignar_solcitud_comite").modal();
}


function listar_comites_combo() {
	$.ajax({
		url: server + "index.php/compras_control/listar_comites/1",
		dataType: "json",
		type: "post",
		success: function (datos) {
			$(".comites_compras").html("<option value=''>Seleccione Comité</option>");
			for (var i = 0; i <= datos.length - 1; i++) {
				$(".comites_compras").append("<option value=" + datos[i].id + ">" + datos[i].nombre + "</option>");
			}

		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});
}


function guardar_proveedor_solicitud() {
	var formData = new FormData(document.getElementById("asignar_proveedor_solicitud"));
	formData.append("id_solicitud", idsolicitud);
	formData.append("coceptos", conceptos);
	$.ajax({
		url: server + "index.php/compras_control/guardar_proveedor_solicitud",
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
		if (datos == 1) {
			MensajeConClase("Ingrese Nombre del proveedor", "info", "Oops...");
			return true;
		}
		if (datos == 2) {
			MensajeConClase("Ingrese Valor total", "info", "Oops...");
			return true;
		}
		if (datos == 3) {
			MensajeConClase("Ingrese Valor del IVA", "info", "Oops...");
			return true;
		}
		if (datos == 4) {
			MensajeConClase("Ingrese datos numericos en el  Valor total", "info", "Oops...");
			return true;
		}
		if (datos == 5) {
			MensajeConClase("Ingrese datos numericos en el  IVA", "info", "Oops...");
			return true;
		}
		if (datos == 6) {
			MensajeConClase("Ingrese datos numericos en el  Precio Dolar", "info", "Oops...");
			return true;
		}
		if (datos == 7) {
			MensajeConClase("Ingrese datos numericos en %Administración", "info", "Oops...");
			return true;
		}
		if (datos == 8) {
			MensajeConClase("Ingrese datos numericos en %Imprevistos", "info", "Oops...");
			return true;
		}
		if (datos == 9) {
			MensajeConClase("Ingrese datos numericos en %Utilidad", "info", "Oops...");
			return true;
		}
		if (datos == 10) {
			MensajeConClase("Ingrese datos numericos en el precio del Dolar", "info", "Oops...");
			return true;
		}
		if (datos == 11) {
			MensajeConClase("Ingrese precio del USD HOY", "info", "Oops...");
			return true;
		}
		if (datos == 12) {
			MensajeConClase("Ingrese %Administracion", "info", "Oops...");
			return true;
		}
		if (datos == 13) {
			MensajeConClase("Ingrese $Imprevistos", "info", "Oops...");
			return true;
		}
		if (datos == 14) {
			MensajeConClase("Ingrese $Utilidad", "info", "Oops...");
			return true;
		}
		if (datos == 15) {
			MensajeConClase("No es posible continuar, la solicitud ya cuenta con proveedores aprobados por los directivos.", "info", "Oops...");
			return true;
		}
		if (datos == 0) {
			$("#asignar_proveedor_solicitud").get(0).reset();
			MensajeConClase("Proveedor Asignado", "success", "Proceso Exitoso!");
			listar_proveedores_solicitud(idsolicitud);
			$("#precio_dolar").hide("slow");
			$("#precio_dolar").removeAttr("required", "true");
			$("#otras_cargas").hide("slow");
			$("#mostar_otras_cargas span").removeClass("fa fa-minus");
			$("#mostar_otras_cargas span").addClass("fa fa-plus");
			$("#otras_cargas input").removeAttr("required", "true");
			conceptos = 0;
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		} else {
			MensajeConClase("Error al Asignar el proveedor: " + datos, "error", "Oops...");
			return;
		}
	});
}

function modificar_proveedor_solicitud() {
	var formData = new FormData(document.getElementById("form_modificar_proveedor_solicitud"));
	formData.append("id", idproveedor_sele_sol);
	formData.append("idsolicitud", idsolicitud);
	formData.append("coceptos", conceptos_modi);
	$.ajax({
		url: server + "index.php/compras_control/modificar_proveedor_solicitud",
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
		if (datos == 1) {
			MensajeConClase("Ingrese Nombre del proveedor", "info", "Oops...");
			return true;
		}
		if (datos == 2) {
			MensajeConClase("Ingrese Valor total", "info", "Oops...");
			return true;
		}
		if (datos == 3) {
			MensajeConClase("Ingrese Valor del IVA", "info", "Oops...");
			return true;
		}
		if (datos == 4) {
			MensajeConClase("Ingrese datos numericos en el  Valor total", "info", "Oops...");
			return true;
		}
		if (datos == 5) {
			MensajeConClase("Ingrese datos numericos en el  IVA", "info", "Oops...");
			return true;
		}
		if (datos == 6) {
			MensajeConClase("Ingrese datos numericos en el  Precio Dolar", "info", "Oops...");
			return true;
		}
		if (datos == 7) {
			MensajeConClase("Ingrese datos numericos en %Administración", "info", "Oops...");
			return true;
		}
		if (datos == 8) {
			MensajeConClase("Ingrese datos numericos en %Imprevistos", "info", "Oops...");
			return true;
		}
		if (datos == 9) {
			MensajeConClase("Ingrese datos numericos en %Utilidad", "info", "Oops...");
			return true;
		}
		if (datos == 10) {
			MensajeConClase("Ingrese datos numericos en el precio del Dolar", "info", "Oops...");
			return true;
		}
		if (datos == 11) {
			MensajeConClase("Ingrese precio del USD HOY", "info", "Oops...");
			return true;
		}
		if (datos == 12) {
			MensajeConClase("Ingrese %Administracion", "info", "Oops...");
			return true;
		}
		if (datos == 13) {
			MensajeConClase("Ingrese $Imprevistos", "info", "Oops...");
			return true;
		}
		if (datos == 14) {
			MensajeConClase("Ingrese $Utilidad", "info", "Oops...");
			return true;
		}
		if (datos == 15) {
			MensajeConClase("No es posible continuar, la solicitud ya cuenta con proveedores aprobados por los directivos.", "info", "Oops...");
			return true;
		}
		if (datos == 0) {
			$("#Modal_modificar_proveedor_solicitud").modal("hide");
			$("#form_modificar_proveedor_solicitud").get(0).reset();
			MensajeConClase("Proveedor Modificado con exito", "success", "Proceso Exitoso!");
			listar_proveedores_solicitud(idsolicitud);
			$("#precio_dolar_modi").hide("slow");
			$("#precio_dolar_modi").removeAttr("required", "true");
			$("#otras_cargas_modi").hide("slow");
			$("#mostar_otras_cargas_modi span").removeClass("fa fa-minus");
			$("#mostar_otras_cargas_modi span").addClass("fa fa-plus");
			$("#otras_cargas_modi input").removeAttr("required", "true");
			conceptos_modi = 0;
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		} else {
			MensajeConClase("Error al modificar el proveedor: " + datos, "error", "Oops...");
			return;
		}
	});
}

function mostrar_datos_proveedor_modi(id) {
	traer_proveedor_solicitud(id);
	idproveedor_sele_sol = id;
	$("#Modal_modificar_proveedor_solicitud").modal();
}

function asignar_proveedor(id) {
	idsolicitud = id;
	$("#Modal_asignar_proveedor_articulo").modal();
}

function listar_proveedores_solicitud(id) {
	listar_comentario_tipo2(id);
	esta_negada_usuario(id);
	$('#tabla_proveedores_articulo tbody').off('click', 'tr');
	$('#tabla_proveedores_articulo tbody').off('dblclick', 'tr');
	$('#tabla_proveedores_articulo tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_proveedores_articulo").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_proveedores_solicitud",
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
		},
		"processing": true,
		"columns": [{
			"data": "indice"
		},
		{
			"data": "nombre"
		},
		{
			"data": "total_compra"
		},
		{
			"data": "total_compra_dolar"
		},
		{
			"data": "vb"
		},
		{
			"data": "vbs"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_proveedores_articulo tbody').on('click', 'tr', function () {
		$("#tabla_proveedores_articulo tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$('#tabla_proveedores_articulo tbody').on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		llenar_tabla_detalles_proveedor(data);
	});

	$('#tabla_proveedores_articulo tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		llenar_tabla_detalles_proveedor(data);
	});

	idsolicitud = id;
	$("#Modal_listar_proveedores_articulo").modal();
	listar_comites_combo();
}

function llenar_tabla_detalles_proveedor(datos) {
	if (datos.administracion != null || datos.imprevistos != null || datos.utilidad != null) {
		$(".sin_info_proveedor").show("fast");
		$(".pesos_administracion").html("$ " + datos.cal_adm);
		$(".pesos_imprevisto").html("$ " + datos.cal_imp);
		$(".pesos_utilidad").html("$ " + datos.cal_utl);
		$(".dolar_administracion").html("$ " + datos.cal_adm_dolar);
		$(".dolar_imprevisto").html("$ " + datos.cal_imp_dolar);
		$(".dolar_utilidad").html("$ " + datos.cal_utl_dolar);
	} else {
		$(".sin_info_proveedor").css("display", "none");
	}
	if (datos.precio_dolar != null) {
		$(".sin_dolar").show("fast");
	} else {
		$(".sin_dolar").css("display", "none");
	}
	$(".valor_nombre_proveedor").html(datos.nombre);
	$(".valor_pesos").html("$ " + datos.valor_total_alt);
	$(".valor_dolares").html("$ " + datos.valor_dolar_alt);
	$(".valor_precio_dolar").html("$ " + datos.precio_dolar);
	$(".valor_iva").html(datos.iva);
	$(".valor_administracion").html(datos.administracion);
	$(".valor_imprevistos").html(datos.imprevistos);
	$(".valor_utilidad").html(datos.utilidad);
	$(".valor_fecha_registro_prove").html(datos.fecha_registra);
	$(".pesos_iva").html("$ " + datos.cal_iva);
	$(".total_compra").html("$ " + datos.total_compra);
	$(".dolar_iva").html("$ " + datos.cal_iva_dolar);
	$(".dolar_total_compra").html("$ " + datos.total_compra_dolar);



	if (datos.adjunto != null) {
		$(".valor_propuesta").html("<a href='" + server + ruta_adjunto_proveedores + datos.adjunto + "' target='_blank'>Ver Adjuntos. </a>");
		$(".tr-adjunto").show("fast");
	} else {
		$(".tr-adjunto").css("display", "none");
	}
	traer_proveedor_aprobados(datos.id);
	$("#modal_detalle_pro_articulo").modal();

}


function entregas_parciales() {
	var formData = new FormData(document.getElementById("gestionar_solicitud"));
	formData.append("id", idsolicitud);
	$.ajax({
		url: server + "index.php/compras_control/guardar_entregas_parciales",
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

		if (datos == -8) {
			MensajeConClase("Ingrese todas las cantidades", "info", "Oops...");
			return true;
		} else if (datos == -9) {
			MensajeConClase("Ingrese solo datos numericos en las cantidades", "info", "Oops...");
			return true;
		} else if (datos == -10) {
			MensajeConClase("La cantidad ingresada no puede ser mayor que la solicitada", "info", "Oops...");
			return true;
		} else if (datos == -11) {
			MensajeConClase("No fue posible cambiar el estado ya que  otro usuario gestiono la solicitud y esta se encuentra en otro proceso, por favor refresque los datos y valide la información.!", "info", "Oops...");
			return;
		} else if (datos == -12) {
			MensajeConClase("Todos los artículos fueron entregados ya puede continuar con el proceso de la solicitud.", "success", "Proceso Exitoso!");
			Mostrar_estados_siguientes(11);
			$("#Modal_Entregas_parciales").modal("hide");
			return true;
		} else if (datos == 1) {
			MensajeConClase("Guardado con entregas parciales", "success", "Proceso Exitoso!");
			Listar_articulos_parciales(idsolicitud);
			$("#Modal_Entregas_parciales").modal("hide");
			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		} else {
			MensajeConClase("Error al asignar la cantidad entregada", "error", "Oops...");
			return;
		}
	});
}

function historial_articulos_entregas_parciales(id, id_articulo) {
	$('#tabla_historial_entregas tbody').off('dblclick', 'tr');
	$('#tabla_historial_entregas tbody').off('click', 'tr');
	const myTable = $("#tabla_historial_entregas").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/historial_articulos_entregas_parciales",
			dataType: "json",
			type: "post",
			data: {
				id,
				id_articulo
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
			"data": "entregada"
		},
		{
			"data": "fecha_registro"
		},
		{
			"data": "persona"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_historial_entregas tbody').on('click', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_historial_entregas tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});


}

function Modificar_solicitud_comite() {
	var formData = new FormData(document.getElementById("modificar_sol_comite"));
	formData.append("id", idsolicitud);
	$.ajax({
		url: server + "index.php/compras_control/Modificar_solicitud_comite",
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
		if (datos == -1) {
			MensajeConClase("Seleccione Comité", "info", "Oops...");
			return true;
		} else if (datos == -2) {
			MensajeConClase("Ingrese descripcion", "info", "Oops...");
			return true;
		} else if (datos == 15) {
			MensajeConClase("No es posible continuar, la solicitud ya cuenta con proveedores aprobados por los directivos.", "info", "Oops...");
			return true;
		} else if (datos == 1) {
			$(".valor_comite").html($("#comites_compras_modi option:selected").text());
			$(".valor_descripcion_cmt").html($("#descripcion_cmt_modi").val());
			$(".valor_observaciones_cmt").html($("#observaciones_cmt_modi").val());
			$("#modificar_sol_comite").get(0).reset();
			MensajeConClase("Datos de la Solicitud en Comité Modificados", "success", "Proceso Exitoso!");
			$("#Modal_modificar_sol_comite").modal("hide");

			return true;
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Modificar los datos de la solicitud Comité", "error", "Oops...");
		}
	});
}

function eliminar_proveedor_solicitud(id) {
	swal({
		title: "Estas Seguro ?",
		text: "El proveedor será eliminado del listado en esta solicitud.",
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

				$.ajax({
					url: server + "index.php/compras_control/eliminar_proveedor_solicitud",
					type: "post",
					data: {
						id,
						idsolicitud
					},
					dataType: "json",
				}).done(function (datos) {
					if (datos == "sin_session") {
						close();
						return;
					}
					if (datos == -1302) {
						MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
						return;
					} else if (datos == 15) {
						MensajeConClase("No es posible continuar, la solicitud ya cuenta con proveedores aprobados por los directivos.", "info", "Oops...");
						return true;
					}

					if (datos == 0) {
						//MensajeConClase("El proveedor fue retirado con exito.", "success", "Proceso Exitoso!");
						swal.close();
						listar_proveedores_solicitud(idsolicitud);
					} else {
						MensajeConClase("error al retirar el proveedor.", "error", "Oops!");
					}
				});
				return;

			}
		});
}


function Listar_solicitudes_en_comite(id, directivos = -1) {
	$('#tabla_solicitudes_comite tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .retirar').off('click', 'tr td .notificar_dir');
	const myTable = $("#tabla_solicitudes_comite").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/Listar_solicitudes_en_comite/" + directivos,
			dataType: "json",
			data: {
				id,

			},
			type: "post",
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"pageLength": 50,
		"processing": true,
		"columns": [{
			"data": "codigo"
		},

		{
			"data": "no"
		},
		{
			"data": "solicitante"
		},
		{
			"data": "descripcion_cmt"
		},
		{
			"data": "observaciones_cmt"
		},
		{
			"data": "vb"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_solicitudes_comite tbody').on('click', 'tr', function () {
		const data = myTable.row(this).data();
		id_sol_comi = data.id;
		$(".valor_comite").html(data.nombre_comite);
		$(".valor_descripcion_cmt").html(data.descripcion_cmt);
		$(".valor_observaciones_cmt").html(data.observaciones_cmt);
		$(".valor_fecha_cierre").html(data.fecha_cierre_comite);
		$("#tabla_solicitudes_comite tbody tr").removeClass("warning");
		$(this).addClass("warning");

	});
	$('#tabla_solicitudes_comite tbody').on('click', 'tr td .retirar', function () {
		const { id, id_comite } = myTable.row($(this).parent().parent()).data();
		confir_retirar_solicitud_comite(id, id_comite);
	});


	$('#tabla_solicitudes_comite tbody').on('click', 'tr td .notificar_dir', function () {
		const { id, id_comite } = myTable.row($(this).parent().parent()).data();
		recordatorio_directivos(id, id_comite);
	});
	// if (directivos == 1) myTable.column(4).visible(false);

	$("#Modal_solicitudes_por_comite").modal();

}

function listar_proveedores_solicitud_comite(id) {
	$('#tabla_proveedores_solicitud_comite tbody').off('click', 'tr');
	$('#tabla_proveedores_solicitud_comite tbody').off('dblclick', 'tr');
	$('#tabla_proveedores_solicitud_comite tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_proveedores_solicitud_comite").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_proveedores_solicitud/1",
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
		},
		"processing": true,
		"columns": [{
			"data": "indice"
		},
		{
			"data": "nombre"
		},
		{
			"data": "total_compra"
		},
		{
			"data": "total_compra_dolar"
		},
		{
			"data": "vb"
		},
		{
			"data": "vbs"
		},

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla_proveedores_solicitud_comite tbody').on('click', 'tr', function () {
		$("#tabla_proveedores_solicitud_comite tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$('#tabla_proveedores_solicitud_comite tbody').on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		llenar_tabla_detalles_proveedor(data);
	});

	$('#tabla_proveedores_solicitud_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		llenar_tabla_detalles_proveedor(data);
	});


	$("#Modal_listar_proveedores_solicitud_comite").modal();

}

function listar_comites_directivos(id) {
	$('#tabla_comite_directivos tbody').off('click', 'tr');
	$('#tabla_comite_directivos tbody').off('dblclick', 'tr');
	$('#tabla_comite_directivos tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_comite_directivos").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_comites_directivos",
			data: {
				id
			},
			dataType: "json",
			type: "post",
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"pageLength": 50,
		"processing": true,
		"columns": [{
			"data": "gestion"
		}, {
			"data": "nombre"
		},

		{
			"data": "descripcion"
		},
		{
			"data": "solicitudes"
		},

		{
			"data": "estado_alt"
		},


		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla_comite_directivos tbody').on('click', 'tr', function () {
		var data = table.row(this).data();
		$("#tabla_comite_directivos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_comite_dire = data.id;
		en_notificacion = 0;

	});

	$('#tabla_comite_directivos tbody').on('dblclick', 'tr', function () {
		var data = table.row(this).data();
		Listar_solicitudes_en_comite(data.id, 1);
	});

	$('#tabla_comite_directivos tbody').on('click', 'tr td:nth-of-type(1)', function () {
		const data = table.row($(this).parent()).data();
		Listar_solicitudes_en_comite(data.id, 1);
	});
	if (id > 0) {
		Con_filtros(true);
	}


}

function listar_proveedores_solicitud_comite_directivos(id, render = '#tabla_proveedores_solicitud_comite_dir') {
	listar_comentario_tipo2(id);
	esta_negada_usuario(id);
	$(`${render} tbody`).off('click', 'tr');
	$(`${render} tbody`).off('dblclick', 'tr');
	$(`${render} tbody`).off('click', 'tr td:nth-of-type(1)');
	var table = $(render).DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_proveedores_solicitud/2",
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
		},
		"processing": true,
		"columns": [{
			"data": "indice"
		},
		{
			"data": "nombre"
		},
		{
			"data": "total_compra"
		},
		{
			"data": "total_compra_dolar"
		},
		{
			"data": "vb"
		},
		{
			"data": "vbs"
		},
		{
			"data": "gestion"
		},

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$(`${render} tbody`).on('click', 'tr', function () {
		$(`${render} tbody tr`).removeClass("warning");
		$(this).attr("class", "warning");
	});

	$(`${render} tbody`).on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		llenar_tabla_detalles_proveedor(data);
	});

	$(`${render} tbody`).on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		llenar_tabla_detalles_proveedor(data);
	});

	if (render == '#tabla_proveedores_solicitud_comite_dir_noti') {
		$("#Modal_listar_proveedores_solicitud_comite_noti").modal();
	} else {
		$("#Modal_listar_proveedores_solicitud_comite").modal();
	}

}

function aprobar_proveedor(id) {
	swal({
		title: "Aprobar Proveedor..?",
		text: "Tener en cuenta que, podrá realizar modificaciones en los vistos buenos mientras no se hayan completado el numero avales mínimo.",
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
				$.ajax({
					url: server + "index.php/compras_control/aprobar_proveedor",
					type: "post",
					data: {
						id,
						id_sol_comi,
						id_comite_dire
					},
					dataType: "json",
				}).done(function (data) {
					datos = data[0];
					if (datos == "sin_session") {
						close();
						return;
					}
					if (datos == -1302) {
						MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
						return;
					}

					if (datos == 1 && data[1] == 0) {
						//MensajeConClase("El proveedor fue aprobado con exito.", "success", "Proceso Exitoso!");
						swal.close();
						if (en_notificacion == 0) {
							listar_proveedores_solicitud_comite_directivos(id_sol_comi);
							Listar_solicitudes_en_comite(id_comite_dire, 1);
						} else {
							listar_proveedores_solicitud_comite_directivos(id_sol_comi, '#tabla_proveedores_solicitud_comite_dir_noti');
						}
						if (data[2] == 2) {
							listar_comites_directivos(-1);
						}
					} else if (datos == -1) {
						MensajeConClase("El proveedor seleccionado ya cuenta con el visto bueno por parte de usted.", "info", "Oops!");
					} else if (datos == -7) {
						MensajeConClase("La solicitud ya fue procesada por el departamento de compras.", "info", "Oops...");
					} else {
						MensajeConClase("error al aprobar el proveedor.", "error", "Oops!");
					}
				});
				return;
			}
		});
}

function traer_proveedor_aprobados(id) {
	$('#tabla_vb_personas tbody').off('click', 'tr');
	var table = $("#tabla_vb_personas").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/traer_proveedor_aprobados",
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
		},
		"processing": true,
		"columns": [{
			"data": "tipo"
		}, {
			"data": "persona"
		},
		{
			"data": "correo"
		},
		{
			"data": "fecha_registra"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla_vb_personas tbody').on('click', 'tr', function () {
		$("#tabla_vb_personas tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
}


function validar_aprobados_comite(id) {
	$.ajax({
		url: server + "index.php/compras_control/validar_aprobados_comite",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			if (datos == 1) {
				Mostrar_estados_siguientes(6)
			} else if (datos == "no") {
				swal({
					title: "Estas Seguro ?",
					text: "La solicitud no cuenta con el numero de aprobaciones necesarias para continuar con el proceso.!",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Entiendo!",
					cancelButtonText: "No, Regresar!",
					allowOutsideClick: true,
					closeOnConfirm: true,
					closeOnCancel: true
				},
					function (isConfirm) {
						if (isConfirm) {
							Mostrar_estados_siguientes(6)
						}
					});
			} else {
				MensajeConClase("La solicitud no cuenta con el numero de aprobaciones necesarias para continuar con el proceso", "info", "Oops...");
			}
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});
}

function guardar_comentario(id, comentario, id_pregunta = -1, tipo = 1) {
	$.ajax({
		url: server + "index.php/compras_control/guardar_comentario",
		dataType: "json",
		data: {
			id,
			comentario,
			id_pregunta
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				if (id_pregunta != -1) {
					if (tipo == 1) listar_comentario_tipo2(id);
					else mostrar_notificaciones(modulo_noti);
				}
				MensajeConClase("", "success", "Comentario Enviado!");
				if (modulo_noti == 2) listar_comentario_tipo2(id);
				$("textarea").val("");
			} else if (datos == -2) {
				MensajeConClase("Antes de enviar debe Ingresar debe redactar el comentario.", "info", "Oops...");
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
	id_coment = 0;
	$('#tabla_comentarios tbody').off('click', 'tr');
	$('#tabla_comentarios tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_comentarios").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_comentario",
			dataType: "json",
			type: "post",
			data: {
				id,
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
		"ordering": false,
		"columns": [{
			"data": "indice"
		}, {
			"data": "comentario"
		},
		{
			"data": "persona"
		},
		{
			"data": "fecha_registro"
		},
		{
			"data": "codigo"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});
	$('#tabla_comentarios tbody').on('click', 'tr', function () {
		$("#tabla_comentarios tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$('#tabla_comentarios tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		id_coment = data.id;
		$(".pregunta_info").html(data.comentario);
		$(".usuario_pre_info").html(`${data.persona} dice:`);
		listar_comentarios_respuestas(idsolicitud, data.id);
		$("#Modal_comentarios_pregunta_compra").modal("show");
	});
	$("#Modal_comentarios_compras").modal("show");
}

const mostrar_repsuestas_comentarios_t2 = (id_compra, id, comentario, persona) => {
	id_coment = id;
	$(".pregunta_info").html(comentario);
	$(".usuario_pre_info").html(`${persona} dice:`);
	listar_comentarios_respuestas(id_compra, id);
	$("#Modal_comentarios_pregunta_compra").modal("show");
}

function listar_comentario_tipo2(id) {
	$.ajax({
		url: server + "index.php/compras_control/listar_comentario_tipo2",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: (datos) => { dibujar_comentario(datos, '.panel_comentarios_formato_2', 1) },
		error: function (status, error) {
			console.log('Something went wrong', status, error);
		}
	});

}

function esta_negada_usuario(id) {
	$.ajax({
		url: server + "index.php/compras_control/esta_negada_usuario",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			let procc = datos[0];
			let data = datos[1];
			let negados = datos[2];
			if (procc == "sin_session") {
				close();
				return;
			} else if (procc == -1) {
				MensajeConClase("Error al cargar el ID de la solicitud, contacte con el administrador.", "error", "Oops.!");
			} else if (procc == 1) {
				if (data.length == 0) {
					$(".negar_compra").html("Negar Compra").removeClass('btn-success').addClass('btn-danger').on("click", () => {
						confirm_negar_solicitud()
					});
				} else {
					$(".negar_compra").html("Aprobar Compra").removeClass('btn-danger').addClass('btn-success').on("click", () => {
						confirm_cancelar_negado(data.id)
					});
				}
				if (negados.length == 0) {
					$(".div_negados").html(``);
				} else {
					let terminar_compra_den = modulo_noti == 1 ? '<span class="badge pointer btn-danger" onclick="Mostrar_estados_siguientes(13)" >Fin</span>' : '';
					let text_negados = `<div class="alert alert-warning" role="alert"><p><span class="fa fa-warning"></span> Tener en cuenta que, algunos usuarios del comité han negado esta compra.	<span class="badge pointer" onclick="listar_personas_compra_negados(${id})" >Ver</span> ${terminar_compra_den}</p></div>`;
					$(".div_negados").html(`${text_negados}`);
				}
			} else {
				MensajeConClase("Error al verificar la solicitud, contacte con el administrador.", "error", "Oops.!");
			}
			return false;
		},
		error: function (status, error) {
			console.log('Something went wrong', status, error);
		}
	});

}

const confirm_negar_solicitud = () => {
	swal({
		title: "Negar Compra ?",
		text: "Si desea continuar con el proceso, por favor presione la opción de 'Si, Negar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Negar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				negar_compra(id_sol_comi);
			}
		});

	return false;
}
const confirm_cancelar_negado = (id) => {
	swal({
		title: "Aprobar Compra ?",
		text: "Tener en cuenta que, al aprobar la compra se continuara con el proceso de esta.!",
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
				cancelar_negado_solicitud(id);
			}
		});

	return false;
}


function listar_comentarios_respuestas(id, id_coment, modal = -1, comentario = "", persona = "") {
	$('#tabla_comentarios_respuestas tbody').off('click', 'tr');
	var table = $("#tabla_comentarios_respuestas").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_comentario",
			dataType: "json",
			type: "post",
			data: {
				id,
				id_coment
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
		"ordering": false,
		"columns": [{
			"data": "comentario"
		},
		{
			"data": "usuario"
		},
		{
			"data": "fecha_registro"
		}
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});
	$('#tabla_comentarios_respuestas tbody').on('click', 'tr', function () {
		$("#tabla_comentarios_respuestas tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	if (modal == 1) {
		$('#ver_compra').off('click');
		$('#btn_terminar_comentario').off('click');
		data_noti = [id, id_coment];
		$("#Modal_comentarios_pregunta_compra").modal("show");
		$(".pregunta_info").html(comentario);
		$(".usuario_pre_info").html(`${persona} dice:`);
		$("#ver_compra").on("click", () => {
			if (modulo_noti == 1) {
				traer_solicitud(id, -1, 1);
				$("#modal_detalle_solicitud_noti").modal("show");
			} else {
				id_sol_comi = id;
				id_comite_dire = 0;
				listar_proveedores_solicitud_comite_directivos(id, '#tabla_proveedores_solicitud_comite_dir_noti');
			}
		});
		$("#btn_terminar_comentario").on("click", () => {
			swal({
				title: "Terminar Comentario.?",
				text: "Tener en cuenta que al terminar un comentario no se enviaran mas notificaciones referente a este tema.",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Ok, Terminar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: true,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						terminar_comentario(id_coment);
						return;
					}
				});

		});
	}
	if (en_notificacion == 0) {
		$("#ver_compra").hide("fast");
		$('#btn_terminar_comentario').hide('fast');
	} else {
		$("#ver_compra").show("fast");
		$('#btn_terminar_comentario').show('fast');
	}
}


let crear_copia = async (id) => {
	MensajeConClase("validando info", "add_inv", "Oops...");
	//traer_solicitud(id, 1);
	traer_articulos_copia(id);

}

function calcular_tiempo_gestion(id) {

	$.ajax({
		url: server + "index.php/compras_control/calcular_tiempo_gestion",
		dataType: "json",
		data: {
			id

		},
		type: "post",
		success: function (datos) {
			let resul = datos[0];
			if (resul == 1) {
				MensajeConClase("El tiempo de gestion para esta solicitud fue de: " + datos[1] + "Diás", "success", "Proceso Exitoso!");
			} else if (resul == "sin_session") {
				close();
			} else if (resul == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			} else {
				MensajeConClase("Error al calcular el tiempo de gestion", "error", "Oops...");
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function solicitudes_exceptuadas_lista(params) {
	for (let index = 0; index < solicitudes_exceptuadas.length; index++) {
		if (solicitudes_exceptuadas[index] == params) {
			solicitudes_exceptuadas.splice(index, 1);
			$(`#lista_excluida .${params}`).addClass("oculto");
			return;
		}

	}


}


function estados_exceptuadas_lista(params) {
	for (let index = 0; index < estados_exceptuadas.length; index++) {
		if (estados_exceptuadas[index] == params) {
			estados_exceptuadas.splice(index, 1);
			$(`#lista_excluida_estados .${params}`).addClass("oculto");
			return;
		}

	}


}

function Listar_articulos_solicitados(datos) {
	modificando_ini = 0;
	id_articulo_sele_ini = 0;
	$('#tabla_articulos_solicitados tbody').off('dblclick', 'tr');
	$('#tabla_articulos_solicitados tbody').off('click', 'tr');
	const myTable = $("#tabla_articulos_solicitados").DataTable({
		"destroy": true,
		"processing": true,
		data: datos,
		columns: [{
			data: 'texto_code'
		}, {
			data: 'nombre_art'
		},
		{
			data: 'cantidad_art'
		},
		{
			data: 'marca_art'
		},
		{
			data: 'referencia_art'
		},
		{
			data: 'fecha_compra_tarjeta'
		},
		{
			data: 'observaciones'
		},

		{

			"render": function (data, type, full, meta) {

				return '<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default" onclick="eliminar_articulo_solicitud(' + full.id + ')"></span> <span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default" onclick="mostrar_modificar_articulo_solicitud(' + full.id + ')"></span>';
			}
		}

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_articulos_solicitados tbody').on('click', 'tr', function () {
		var data = myTable.row(this).data();
		id_articulo_sele_ini = data.id;
		$("#tabla_articulos_solicitados tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla_articulos_solicitados tbody').on('dblclick', 'tr', function () {
		//var data = myTable.row(this).data();
		$("#tabla_articulos_solicitados tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});


	//config_modal_sol(tipo);

}

function eliminar_articulo_solicitud(articulo) {
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

function mostrar_modificar_articulo_solicitud(articulo) {
	$("#Agregar_Articulos").get(0).reset();
	id_articulo_sele_ini = articulo;
	for (let i = 0; i < articulos_sele.length; i++) {
		let { id, nombre_art, cantidad_art, marca_art, referencia_art, codigo_orden, texto_code, observaciones, fecha_compra_tarjeta, con_tarjeta } = articulos_sele[i];
		if (id == articulo) {
			// console.log(articulos_sele[i]);
			$("#cod_orden_sele").html(texto_code);
			$("#txtnombre_articulo").val(nombre_art)
			$("#txtcantidad").val(cantidad_art)
			$("#txtmarca_articulo").val(marca_art)
			$("#txtreferencia").val(referencia_art)
			$("#input_codigo_orden").val(codigo_orden);
			$("#txt_observaciones_articulo").val(observaciones);
			$("#fecha_compra_tarjeta").val(fecha_compra_tarjeta);
			if (con_tarjeta) {
				$("#con_tarjeta").prop("checked", true);
				$("#container_con_tarjeta").show('slow');
				$("#Agregar_Articulos input[name='fecha_compra_tarjeta']").attr("required", "true");
			} else {
				$("#Agregar_Articulos input[name='fecha_compra_tarjeta']").removeAttr("required", "true");
				$("#container_con_tarjeta").hide('slow');
			}
			modificando_ini = 1;
			$("#text_add_arts").html("Modificar Artículos");
			$("#modalArticulos").modal();
			return false;
		}

	}


}


function modificar_articulo_solicitud() {

	for (let i = 0; i < articulos_sele.length; i++) {
		const id = articulos_sele[i].id;
		if (id == id_articulo_sele_ini) {
			articulos_sele.splice(i, 1);
			$("#modalArticulos").modal('hide');
			modificando_ini = 0;
			return false;
		}

	}


}

let guardar_solicitud_2 = () => {


	Dropzone.options.Subir = {
		url: server + "index.php/compras_control/cargar_archivo_2", //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
		method: "post", //por defecto es post se puede poner get, put, etc.....
		withCredentials: false,
		parallelUploads: 20, //Cuanto archivos subir al mismo tiempo
		uploadMultiple: false,
		maxFilesize: 1000, //Maximo Tama�o del archivo expresado en mg
		paramName: "file", //Nombre con el que se envia el archivo a nivel de parametro
		createImageThumbnails: true,
		maxThumbnailFilesize: 1000, //Limite para generar imagenes (Previsualizacion)
		thumbnailWidth: 154, //Medida de largo de la Previsualizacion
		thumbnailHeight: 154, //Medida alto Previsualizacion
		filesizeBase: 1000,
		maxFiles: 20, //si no es nulo, define cu�ntos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
		params: { }, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
		clickable: true,
		ignoreHiddenFiles: true,
		acceptedFiles: "image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv,.gz,.gzip,.rar,.zip", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
		acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
		autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas
		error: function (response) {

			if (!response.xhr) {
				MensajeConClase("Solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");

			} else {
				errores.push(response.xhr.responseText);

				if (cargados == num_archivos) {
					if (tipo_cargue == 1) {
						MensajeConClase("La solicitud fue guardada con exito, pero ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
					} else {
						MensajeConClase("Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
					}

				}

			}

		},
		success: function (file, response) {

			let errorlist = "No ingresa";
			if (errores.length > 0) {
				errorlist = "";
				for (let index = 0; index < errores.length; index++) {
					errorlist = errorlist + errores[index] + ",";
				}
				if (tipo_cargue == 1) {
					MensajeConClase("La solicitud fue guardada con exito, pero algunos Archivos No fueron cargados:\n\n" + errorlist + "\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
				} else {
					listar_archivo_compra(idsolicitud);
					MensajeConClase("Algunos Archivos No fueron cargados:\n\n" + errorlist + "\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
				}

			} else {
				if (tipo_cargue == 1) {
					MensajeConClase("La solicitud fue Guardada con exito y Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
				} else {
					listar_archivo_compra(idsolicitud);
					MensajeConClase("Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
				}
			}
		},

		init: function () {
			num_archivos = 0;
			myDropzone = this;
			this.on("addedfile", function (file) {
				num_archivos++;
			});
			this.on("removedfile", function (file) {
				num_archivos--;
			});;

			myDropzone.on("complete", function (file) {
				myDropzone.removeFile(file);
				cargados++;
			});
		},
		autoQueue: true,
		addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacci�n.
		previewsContainer: null, //define d�nde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
		capture: null,
		dictDefaultMessage: "Arrastra los archivos aqui para subirlos",
		dictFallbackMessage: "Su navegador no soporta arrastrar y soltar para subir archivos.",
		dictFallbackText: "Por favor utilize el formuario de reserva de abajo como en los viejos timepos.",
		dictFileTooBig: "La imagen revasa el tama�o permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.",
		dictInvalidFileType: "No se puede subir este tipo de archivos.",
		dictResponseError: "Server responded with {{statusCode}} code.",
		dictCancelUpload: "Cancel subida",
		dictCancelUploadConfirmation: "�Seguro que desea cancelar esta subida?",
		dictRemoveFile: "Eliminar archivo",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Se ha excedido el numero de archivos permitidos.",
	};

}

function listar_archivo_compra(id, render = "#tabla_adjuntos_compras") {



	var table = $(render).DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_archivo_compra",
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
			"data": "nombre_real"
		},
		{
			"data": "fecha_registro"
		},
		{

			"render": function (data, type, full, meta) {
				return "<a class='sin-decoration ' href='" + server + ruta_adjunto_solicitudes + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
			}
		}
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});

}

function Listar_personas_por_perfil(perfil) {
	$.ajax({
		url: server + "index.php/compras_control/Listar_personas_por_perfil",
		dataType: "json",
		data: {
			perfil
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			directivos_correos = datos;
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function modificar_cod_orden_solicitud(id, codigo) {
	$.ajax({
		url: server + "index.php/compras_control/modificar_cod_orden_solicitud",
		dataType: "json",
		data: {
			id,
			codigo
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			} else if (datos == 0) {
				//MensajeConClase("La #Orden fue Modificado con exito.!", "success", "Proceso Exitoso!");
				swal.close();
				Listar_solicitudes();
				$("#modalArticulos_Solicitud").modal("hide");
				return;
			} else if (datos == -1) {
				MensajeConClase("Error al Modificar el #Orden, contacte al Administrador", "error", "Oops...");
				return;
			} else if (datos == -2) {
				MensajeConClase("La solicitud ya se encuentra Terminada por tal motivo no es posible continuar", "info", "Oops...");
				return;
			} else {
				MensajeConClase("Error al Modificar el #Orden, contacte al Administrador", "error", "Oops...");
			}

		},
		error: function () {
			MensajeConClase("Error al Modificar el #Orden, contacte al Administrador", "error", "Oops...");
		}
	});

};

function solicitudes_por_encuestar_persona() {
	$.ajax({
		url: server + "index.php/compras_control/solicitudes_por_encuestar_persona",
		dataType: "json",
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == 1) {
				$("#myModal").modal("show");
				$("#txt_nombre_jefe").val('');
				id_pjefe = 0;
				correo_jefe = null;
				Listar_articulos_solicitados(articulos_sele)
			} else {
				swal({
					title: "Encuestas Pendientes.!",
					text: "Si desea agregar una nueva solicitud de compra debe realizar las encuestas que tiene pendiente.",
					type: "warning",
					showCancelButton: false,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Ok, Realizar!",
					cancelButtonText: "No, Regresar!",
					allowOutsideClick: true,
					closeOnConfirm: true,
					closeOnCancel: true
				},
					function (isConfirm) {
						if (isConfirm) {
							$("#tipo_compra_filtro").val('');
							$("#estado_filtro").val('');
							$("#fecha_filtro").val('');
							$("#fecha_filtro_2").val('');
							Listar_solicitudes(0, -1, 1);
							$("#menu_principal").css("display", "none");
							$(".listado_solicitudes").fadeIn(1000);
						}
					});

			}

			return false;
		},
		error: function () {
			console.log('Something went wrong', status, error);
		}
	});

};

function responder_preguntas(id, idsolicitud, tipo = 1) {
	swal({
		title: "Responder Comentario.!",
		text: "",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Responder!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: "Ingrese Respuesta"
	}, function (respuesta) {

		if (respuesta === false)
			return false;
		if (respuesta === "") {
			swal.showInputError("Debe Ingresar la respuesta.!");
			return false;
		} else {
			guardar_comentario(idsolicitud, respuesta, id, tipo);

			return false;
		}
	});
}


const mostrar_notificaciones = (tipo = 1) => {
	en_notificacion = 1;
	modulo_noti = tipo;
	let url = Traer_Server() + "index.php/compras_control/mostrar_notificaciones_comentario";
	consulta_ajax(url, { tipo }, async (datos) => {
		dibujar_comentario(datos, "#panel_notificaciones", 2);
		$(".n_notificaciones").html(datos.length);
		if (tipo == 1) {
			let rango_dias = await obtener_intevalo_fecha_entrega();
			let total_comite = await pintar_notificaciones_solicitudes({ 'filtro': "sc.id_estado_solicitud = 'Soli_Com'", 'having': 'vb > 2', 'container': '#panel_notificaciones_solicitudes', 'titulo': 'Solicitudes en comité para gestionar' }, datos.length);
			let total_fecha = await pintar_notificaciones_solicitudes({ 'filtro': `sc.id_estado_solicitud = 'Soli_Pen' AND  (dias_no_habiles BETWEEN CURDATE() AND date_add(CURDATE(), INTERVAL +${rango_dias} DAY))`, 'having': '', 'container': '#panel_notificaciones_solicitudes_proxima', 'titulo': 'Solicitudes con fecha de entrega próxima' }, total_comite, 2);
			let total_servicio_rec = await pintar_noti_ser_rec({ 'filtro': "", 'having': '', 'container': '#panel_notificaciones_solicitudes_serviciorec', 'titulo': 'Solicitudes con Servicio Recibido' }, total_comite);
		}
		if (datos.length > 0) $("#modal_notificaciones_compras").modal("show");
	});
}

function terminar_comentario(id) {

	$.ajax({
		url: server + "index.php/compras_control/terminar_comentario",
		data: {
			id
		},
		dataType: "json",
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Operaci&oacuten", "error", "Oops...");
				return;
			} else if (datos == 0) {
				//MensajeConClase("", "success", "Comentario Cerrado!");
				swal.close();
				$("#Modal_comentarios_pregunta_compra").modal('hide');
				mostrar_notificaciones(modulo_noti);
			} else {
				MensajeConClase("Error al temrinar el comentario.", "error", "Oops...");
			}
			return false;
		},
		error: function (status, error) {
			console.log('Something went wrong', status, error);
		}
	});
}

function llenar_tabla_detalles_notificacion(datos) {
	if (datos.num_orden != null) {
		$(".valor_orden_cod_noti").html(datos.num_orden);
		$(".valor_proveedor_noti").html(datos.proveedor);
		$(".sin_info_noti").show("fast");
	} else {
		$(".sin_info_noti").css("display", "none");
	}
	$(".tr_valor_obs_devolucion_noti").css("display", "none");
	if (datos.obs_devolucion != null) {
		$(".valor_obs_devolucion_noti").html(datos.obs_devolucion);
		$(".tr_valor_obs_devolucion_noti").show("fast");
	}
	if (datos.descripcion_cmt != null) {
		$(".valor_observaciones_comite").html(datos.observaciones_cmt);
		$(".valor_descripcion_comite").html(datos.descripcion_cmt);
		$(".tr_comite").show("fast");
	} else {
		$(".tr_comite").css("display", "none");
	}
	$(".valor_nombre_noti").html(datos.nombre_solicitud);
	$(".valor_tipo_sol_noti").html(datos.tipo_compra);
	$(".valor_departamento_noti").html(datos.departamento);
	$(".valor_solicitante_noti").html(datos.solicitante);
	$(".valor_jefe_noti").html(datos.jefe_encargado);
	$(".valor_fecha_registro_noti").html(datos.fecha_registra);
	$(".valor_fecha_solicitud_noti").html(datos.fecha_solicitud);
	$(".valor_estado_sol_noti").html(datos.estado_solicitud);
	$(".valor_observaciones_noti").html(datos.observaciones);

	$(".valor_fe_estimada_noti").html("----");
	if (datos.fecha_entrega_est != null) {
		$(".valor_fe_estimada_noti").html(datos.fecha_entrega_est + " Días");
	}
	$("#modal_detalle_solicitud_noti").modal("show");
	id_usuario_sol_noti = datos.id_solicitante;
	Listar_articulos(datos.id, -1, '#tabla_articulos_notificacion');
}

function traer_correos_comite_compras_tipo2(id) {
	$.ajax({
		url: server + "index.php/compras_control/traer_correos_comite_compras_tipo2",
		dataType: "json",
		data: {
			id
		},
		type: "post",
		success: function (datos) {
			if (datos.length != 0) {
				let ser = '<a href="' + server + 'index.php/comite/' + 1 + '"><b>agil.cuc.edu.co</b></a>'
				let mensaje = `Se informa que hay solicitudes de compras que requieren el visto bueno por parte de usted, puede validar la informaci&oacute;n en ${ser}`;
				enviar_correo_personalizado("comp", mensaje, datos, "Comit&eacute; Compras", "Compras CUC", "Nueva solicitud de compras en comite", "ParCodAdm", 3);
				return;
			}
		},
		error: function () {

			console.log('Something went wrong', status, error);

		}
	});
}

function negar_compra(id, comentario = '') {
	$.ajax({
		url: server + "index.php/compras_control/negar_compra",
		dataType: "json",
		data: {
			id,
			comentario,
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				//MensajeConClase("", "success", "Compra Negada!");
				swal.close();
				listar_comentario_tipo2(id);
				esta_negada_usuario(id);
				if (en_notificacion == 0) {
					listar_proveedores_solicitud_comite_directivos(id_sol_comi);
				} else {
					listar_proveedores_solicitud_comite_directivos(id_sol_comi, '#tabla_proveedores_solicitud_comite_dir_noti');
				}
			} else if (datos == -2) {
				MensajeConClase("Antes de enviar debe Ingresar debe redactar el comentario.", "info", "Oops...");
			} else if (datos == "sin_session") {
				close();
			} else if (datos == -6) {
				MensajeConClase("La solicitud ya se encuentra negada.", "info", "Oops...");
			} else if (datos == -7) {
				MensajeConClase("La solicitud ya fue procesada por el departamento de compras.", "info", "Oops...");
			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			} else {
				MensajeConClase("Error al enviar al negar la solicitud", "error", "Oops...");
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function cancelar_negado_solicitud(id) {
	$.ajax({
		url: server + "index.php/compras_control/cancelar_negado_solicitud",
		dataType: "json",
		data: {
			id,
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				//MensajeConClase("", "success", "Compra Aprobada!");
				swal.close();
				listar_comentario_tipo2(id_sol_comi);
				esta_negada_usuario(id_sol_comi);
				if (en_notificacion == 0) {
					listar_proveedores_solicitud_comite_directivos(id_sol_comi);
				} else {
					listar_proveedores_solicitud_comite_directivos(id_sol_comi, '#tabla_proveedores_solicitud_comite_dir_noti');
				}
			} else if (datos == "sin_session") {
				close();
			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			} else {
				MensajeConClase("Error al enviar al aprobar la solicitud", "error", "Oops...");
			}
		},
		error: function () {
			console.log('Something went wrong', status, err);
		}
	});

}

function listar_personas_compra_negados(id) {
	$('#tabla_negados_compra tbody').off('click', 'tr');
	$('#tabla_negados_compra tbody').off('click', 'tr td:nth-of-type(1)');
	var table = $("#tabla_negados_compra").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/compras_control/listar_personas_compra_negados",
			dataType: "json",
			type: "post",
			data: {
				id,
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
		"ordering": false,
		"columns": [{
			"data": "persona"
		},
		{
			"data": "fecha"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});
	$('#tabla_negados_compra tbody').on('click', 'tr', function () {
		$("#tabla_negados_compra tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$("#Modal_compra_negada").modal("show");
}

const listar_respuestas_comentario = (id, id_coment) => {
	return new Promise(resolve => {
		$.ajax({
			url: server + "index.php/compras_control/listar_respuestas_comentario",
			dataType: "json",
			data: {
				id,
				id_coment
			},
			type: "post",
			success: function (datos) {
				let respuestas = '';
				datos.map((elemento) => {
					let { persona, comentario } = elemento;
					respuestas = respuestas + `<p><b>${persona}: </b>${comentario}</p>`;
				})
				resolve(respuestas);
			},
			error: function (status, error) {
				console.log('Something went wrong', status, error);
			}
		});
	});

}

const dibujar_comentario = async (datos, panel = '.panel_comentarios_formato_2', tipo) => {
	if (datos == "sin_session") {
		close();
		return;
	}

	let negados = 0;
	let normales = 0;
	let notificaciones = "";
	for (let index = 0; index < datos.length; index++) {
		const element = datos[index];
		if (element.id_negada != null) {
			negados++;
		}
		let respuestas = await listar_respuestas_comentario(element.id_compra, element.id);
		notificaciones += `<a href="#" class="list-group-item">
		${tipo == 2 ? `	<span class="badge" onclick='ver_compra_comite(${element.id_compra},${element.id})'>Abrir</span>` : ''}
		${tipo == 2 ? `	<span class="badge btn-danger" onclick='confir_terminar_comentario(${element.id})'>Cerrar</span>` : ''}
				<span class="badge" onclick='responder_preguntas(${element.id},${element.id_compra},${tipo})'>Responder</span>
					<h4 class="list-group-item-heading">${element.persona} </h4>
					<p class="list-group-item-text">${element.comentario}</p>
					<br>
					<p>Respuestas:</p>
					${respuestas}
				</a>
				`;
		normales++;

	}

	$(panel).html(`
		<ul class="list-group">
			<li class="list-group-item active">
			<span class="badge">${normales}</span>
			Comentarios Compra
		</li>
		${notificaciones}
		</ul>
		`);
}

const ver_compra_comite = (id, id_coment) => {
	data_noti = [id, id_coment];
	if (modulo_noti == 1) {
		traer_solicitud(id, -1, 1);
		$("#modal_detalle_solicitud_noti").modal("show");
	} else {
		id_sol_comi = id;
		id_comite_dire = 0;
		listar_proveedores_solicitud_comite_directivos(id, '#tabla_proveedores_solicitud_comite_dir_noti');
	}

}

const confir_terminar_comentario = (id) => {
	swal({
		title: "Terminar Comentario.?",
		text: "Tener en cuenta que al terminar un comentario no se enviaran mas notificaciones referente a este tema.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Ok, Terminar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				terminar_comentario(id);
				return;
			}
		});

}


const confir_retirar_solicitud_comite = (id, id_comite) => {
	swal({
		title: "Retirar del Comité.?",
		text: "Tener en cuenta que al retirar la solicitud del comité no podrá ser gestionada por los directivos.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Retirar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${Traer_Server()}index.php/compras_control/retirar_solicitud_comite`, { id, id_comite }, ({ mensaje, tipo, titulo, }) => {
					if (tipo == 'success') {
						swal.close();
						Listar_solicitudes_en_comite(id_comite);
						Listar_solicitudes();
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				})
			}
		});

}

const notificaciones_solicitudes = (filtro, having) => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/notificaciones_solicitudes`;
		consulta_ajax(url, { filtro, having }, (resp) => {
			resolve(resp);
		});
	});
}

const notificaciones_servicio_recibido = () => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/notificaciones_servicio_recibido`;
		consulta_ajax(url, { }, (resp) => {
			resolve(resp);
		});
	});
}

const pintar_notificaciones_solicitudes = (data, n, tipo = 1) => {
	return new Promise(async resolve => {
		let { filtro, having, container, titulo } = data;
		let solicitudes = await notificaciones_solicitudes(filtro, having);
		let resultado = ``;
		for (let index = 0; index < solicitudes.length; index++) {
			let { id, descripcion_cmt, indice_fecha, dias_no_habiles } = solicitudes[index];
			let detalle = '';
			if (tipo == 1) detalle = descripcion_cmt;
			else if (tipo == 2) detalle = `Entrega estimada para el día ${dias_no_habiles}`;
			resultado = `${resultado}<a href="#" class="list-group-item"><span class="badge" onclick='Listar_solicitudes(0,${id})'>Ver</span><p class="list-group-item-text">Solicitud No. ${indice_fecha}</p><h4 class="list-group-item-heading">${detalle}</h4></a>`;
		}
		$(container).html(`<ul class="list-group"><li class="list-group-item active">	<span class="badge">${solicitudes.length}</span>${titulo}</li>${resultado}</ul>`);
		$('.n_notificaciones').html(n + solicitudes.length);
		if (solicitudes.length > 0) $('#modal_notificaciones_compras').modal();
		resolve(solicitudes.length + n);
	});
};

const pintar_noti_ser_rec = (data, n) => {
	return new Promise(async resolve => {
		let { container, titulo } = data;
		let solicitudes = await notificaciones_servicio_recibido();
		let resultado = ``;
		for (let index = 0; index < solicitudes.length; index++) {
			let { id, indice_fecha, solicitante } = solicitudes[index];
			let detalle = '';
			detalle = `Solicitante: ${solicitante}`;
			resultado = `${resultado}<a href="#" class="list-group-item"><span class="badge" onclick='Listar_solicitudes(0,${id})'>Ver</span><p class="list-group-item-text">Solicitud No. ${indice_fecha}</p><h4 class="list-group-item-heading">${detalle}</h4></a>`;
		}
		$(container).html(`<ul class="list-group"><li class="list-group-item active">	<span class="badge">${solicitudes.length}</span>${titulo}</li>${resultado}</ul>`);
		$('.n_notificaciones').html(n + solicitudes.length);
		if (solicitudes.length > 0) $('#modal_notificaciones_compras').modal();
		resolve(solicitudes.length + n);
	});
};

const modificar_fecha_entrega_est = (id_solicitud, dia) => {
	let url = `${Traer_Server()}index.php/compras_control/modificar_fecha_entrega_est`;
	consulta_ajax(url, { id_solicitud, dia }, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			swal.close();
			Listar_solicitudes();
			$("#modalArticulos_Solicitud").modal("hide");
		} else MensajeConClase(mensaje, tipo, titulo)
	});

}
const obtener_intevalo_fecha_entrega = () => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/obtener_intevalo_fecha_entrega`;
		consulta_ajax(url, { }, (resp) => {
			resolve(resp);
		});
	});
}

const crear_acta_compra = async (data) => {
	let { id, nombre, fecha_inicio, fecha_fin, ano } = data;
	let imprimir = document.querySelector("#acta_compra");
	let personas = await obtener_correos_comite();
	let solicitudes = await obtener_solicitudes_comite_acta(id);


	$("#nombre_pri").html(nombre.toUpperCase());
	$("#ano_pri").html(ano);
	$("#fecha_inicio_pri").html(fecha_inicio);
	$("#fecha_fin_pri").html(fecha_fin);

	$("#tabla_miembros tbody").html('').append(`<tr><td rowspan='${personas.length + 1}'>MIEMBROS: </td></tr> `);
	personas.map((elemento) => {
		let { persona, departamento } = elemento;
		$("#tabla_miembros tbody").append(`<tr><td>${persona} - ${departamento}</td></tr> `);
	})

	$("#tabla_solicitudes_acta tbody").html('');
	let html = '';
	for (let i = 0; i <= solicitudes.length - 1; i++) {
		let { indice_fecha, solicitante, descripcion_cmt, observaciones_cmt, tipo, id } = solicitudes[i];
		let { aprobados, total_compra, proveedor } = await listar_aprobados_proveedor(id);
		let miembros = '';
		aprobados.map(({ aprueba }) => {
			miembros = `${miembros} ${aprueba}, `
		})
		html = html + `<tr><td>${indice_fecha}</td><td>${solicitante}</td><td>${tipo}</td><td>${descripcion_cmt}</td><td>${observaciones_cmt}</td><td>${proveedor}</td><td>${total_compra}</td><td>${aprobados.length} aprobados :  ${miembros}</td></tr> `;
	}
	$("#tabla_solicitudes_acta tbody").append(html)

	imprimirDIV(imprimir);
}

const obtener_solicitudes_comite_acta = (id_comite) => {
	return new Promise(resolve => {
		consulta_ajax(`${Traer_Server()}index.php/compras_control/obtener_solicitudes_comite_acta`, { id_comite }, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_correos_comite = () => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/obtener_correos_comite`;
		consulta_ajax(url, '', (resp) => {
			resolve(resp);
		});
	});
}

const listar_aprobados_proveedor = (id_solicitud) => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/listar_aprobados_proveedor`;
		consulta_ajax(url, { id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_correos_permiso = (tipo_solicitud, estado_nuevo) => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/obtener_correos_permiso`;
		consulta_ajax(url, { tipo_solicitud, estado_nuevo }, (resp) => {
			resolve(resp);
		});
	});
}

const validacion_cargo = () => {
	return new Promise(resolve => {
		let url = `${Traer_Server()}index.php/compras_control/validacion_cargo`;
		consulta_ajax(url, '', (resp) => {
			resolve(resp);
		});
	});
}

const cambiar_proveedor = (id_solicitud, id_nuevo_prov) => {
	let url = `${Traer_Server()}index.php/compras_control/cambiar_proveedor`;
	consulta_ajax(url, { id_solicitud, id_nuevo_prov }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			Listar_solicitudes();
			$("#modal_cambiar_proveedor").modal("hide");
			$("#modalArticulos_Solicitud").modal("hide");
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_personas_compras = (persona_buscada) => {
	if (persona_buscada == undefined || persona_buscada == "undefined") {
		$("#tabla_personas_compras").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: { },
			columns: [
				{ data: 'nombre' },
				{ data: 'identificacion' },
				{ data: 'correo' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});
		return false;
	}
	
	consulta_ajax(`${server}index.php/compras_control/listar_personas_compras`, { "persona_buscada": persona_buscada }, data => {
		$(`#tabla_estados tbody`).off('click', 'tr');
		$(`#tabla_estados tbody`).off('click', 'tr td .adm_permisos_com');
		$(`#tabla_personas_compras tbody`).off("click", "tr td .adm_permisos_com");
		$(`#tabla_personas_compras tbody`).off("click", "tr td .adm_encuestas_rp");
		$(`#tabla_personas_compras tbody`).off("click", "tr td .adm_solicitudes_com");
		$(`#tabla_personas_compras tbody`).off("click", "tr td .adm_cronogramas_com");
		const myTable = $("#tabla_personas_compras").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'nombre' },
				{ data: 'identificacion' },
				{ data: 'correo' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos de la tabla activados */

		//Permisos
		$(`#tabla_personas_compras tbody`).on("click", "tr td .adm_permisos_com", function () {
			lugar_activo = "proveedor";
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_administrar_permisos").modal();
			listar_tipos_permiso(data.id);
		});

		//Preguntas RP
		$(`#tabla_personas_compras tbody`).on("click", "tr td .adm_encuestas_rp", function () {
			id_persona_selected = "";
			let data = myTable.row($(this).parent().parent()).data();
			id_persona_selected = data.id;
			lugar_activo = "encuesta";
			$("#modal_mostrar_areas").modal();
			listar_tipos_preguntasRP();
		});

		//Solicitudes
		$(`#tabla_personas_compras tbody`).on("click", "tr td .adm_solicitudes_com", function () {
			id_persona_selected = "";
			let data = myTable.row($(this).parent().parent()).data();
			id_persona_selected = data.id;
			solicitudes_usuario(id_persona_selected);
		});

		//Cronogramas
		$(`#tabla_personas_compras tbody`).on("click", "tr td .adm_cronogramas_com", function () {
			let data = myTable.row($(this).parent().parent()).data();
			permisos_cronogramas(data.id);
		});
	});
}

const permisos_cronogramas = (persona_buscada) => {	
	if (persona_buscada == undefined || persona_buscada == "undefined") {
		$("#tabla_permisos_cronogramas").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: { },
			columns: [
				{ data: 'nombre' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});
		return false;
	}

	consulta_ajax(`${ruta_interna}/listar_permisos_cronogramas`, {"persona_buscada": persona_buscada}, data => {
		$("#tabla_permisos_cronogramas tbody").html('');
		const myTable = $("#tabla_permisos_cronogramas").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: data,
			columns: [
				{ data: 'nombre' },
				{ data: 'perm' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos de la tabla activados */
		$(`#tabla_permisos_cronogramas tbody`).on("click", "tr td .asignar_permiso_cronograma", function () {
			let data = myTable.row($(this).parent().parent()).data();
			swal({
				title: "Atención!",
				text: "¿De verdad desea asignar este permiso?, La persona podra gestionar los cronogramas en este estado.",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Asignar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					asignar_encuesta_cronogramas(persona_buscada, data.id);
				}
			});
		});

		$(`#tabla_permisos_cronogramas tbody`).on("click", "tr td .desasignar_permiso_cronograma", function () {
			let data = myTable.row($(this).parent().parent()).data();
			swal({
				title: "Atención!",
				text: "¿De verdad desea retirar el permiso asignado?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					desasignar_encuesta_cronogramas(persona_buscada, data.id);
				}
			});
		});
		$("#modal_permisos_cronogramas").modal("show");
	});	
}

/* Listar personas para asignar encuesta RP */
const listar_personas_rp = (persona_buscada) => {
	int_id_user = "";
	if (persona_buscada == undefined || persona_buscada == "undefined") {
		$("#tabla_personas_RP").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: { },
			columns: [
				{ data: 'nombre' },
				{ data: 'identificacion' },
				{ data: 'correo' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});
		return false;
	}
	consulta_ajax(`${ruta_interna}/listar_personas_general`, { "persona_buscada": persona_buscada, "tipo_enc": tipo_enc_selected }, data => {
		$(`#tabla_estados tbody`)
			.off('click', 'tr')
			.off('click', 'tr td .adm_permisos_com');
		$(`#tabla_personas_RP tbody`).off("click", "tr td .asignar_enc");
		$(`#tabla_personas_RP tbody`).off("click", "tr td .retirar_enc");
		const myTable = $("#tabla_personas_RP").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'nombre' },
				{ data: 'identificacion' },
				{ data: 'correo' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos de la tabla activados */
		$(`#tabla_personas_RP tbody`).on("click", "tr td .asignar_enc", function () {
			let data = myTable.row($(this).parent().parent()).data();
			asignar_encuesta_rp(data.id, tipo_enc_selected);
			int_id_user = data.id;
		});

		$(`#tabla_personas_RP tbody`).on("click", "tr td .retirar_enc", function () {
			let data = myTable.row($(this).parent().parent()).data();
			swal({
				title: "¿Atención?",
				text: "¿De verdad desea retirar la encuesta asignada?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						retirar_encuesta_rp(data.id, tipo_enc_selected);
						int_id_user = data.id;
					}
				});
		});
	});
}

/* Listar encuestas de compras */
const listar_encuestas_compras = () => {
	idpregunta = 0;
	$("#modal_modpara_titulo").html(`<span class="fa fa-check-square-o"></span> Modificar Pregunta`);
	$('#tabla_mostrar_preguntas tbody').off('click', 'tr .del_pregunta_enc');
	$('#tabla_mostrar_preguntas tbody').off('click', 'tr .upd_pregunta_enc');
	$('#tabla_mostrar_preguntas tbody').off('click', 'tr');
	consulta_ajax(`${ruta_interna}listar_preguntas_encuestas`, { "tipo_encuesta": tipo_enc_selected }, res => {
		const myTable = $("#tabla_mostrar_preguntas").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: 'pregunta' },
				{ data: 'accion' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos activados */
		$('#tabla_mostrar_preguntas tbody').on('click', 'tr', function () {
			$("#tabla_mostrar_preguntas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_mostrar_preguntas tbody').on('click', 'tr .del_pregunta_enc', function () {
			let data = myTable.row($(this).parent().parent()).data();
			confirmar_eliminar_parametro(data.id);
		});

		$('#tabla_mostrar_preguntas tbody').on('click', 'tr .upd_pregunta_enc', function () {
			let data = myTable.row($(this).parent().parent()).data();
			idpregunta = data.id;
			Mostrar_modal_modificar(data.id);
		});
	});
}

const listar_tipos_permiso = (id_persona) => {
	$(`#tabla_permisos_com tbody`)
		.off("click", "tr")
		.off("click", "tr .asignar_per_com")
		.off("click", "tr .retirar_per_com");
	consulta_ajax(`${server}index.php/compras_control/listar_tipos_permisos`, { id_persona }, data => {
		const myTable = $("#tabla_permisos_com").DataTable({
			destroy: true,
			processing: true,
			"autoWidth": false,
			data,
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

		$(`#tabla_permisos_com tbody`).on("click", "tr .asignar_per_com", function () {
			let { persona, tipo } = myTable.row($(this).parent().parent()).data();
			asignar_permiso_com(persona, tipo);
		});
		$(`#tabla_permisos_com tbody`).on("click", "tr .retirar_per_com", function () {
			let { persona, tipo } = myTable.row($(this).parent().parent()).data();
			retirar_permiso_com(persona, tipo);
		});
	});
}

const asignar_permiso_com = (id_persona, tipo_per) => {
	consulta_ajax(`${server}index.php/compras_control/asignar_permiso_com`, { id_persona, tipo_per }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			listar_tipos_permiso(id_persona);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

/* Asignar encuesta a personas - RP */
const asignar_encuesta_rp = (id_persona, tipo_enc_selected) => {
	consulta_ajax(`${ruta_interna}asignar_encuesta_rp`, { id_persona, tipo_enc_selected }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			listar_tipos_preguntasRP();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

/* Asignar encuesta a personas - RP */
const retirar_encuesta_rp = (id_persona, tipo_enc_selected) => {
	consulta_ajax(`${ruta_interna}retirar_encuesta_rp`, { id_persona, tipo_enc_selected }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			listar_tipos_preguntasRP();
			swal.close();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const retirar_permiso_com = (id_persona, tipo_per) => {
	consulta_ajax(`${server}index.php/compras_control/retirar_permiso_com`, { id_persona, tipo_per }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			listar_tipos_permiso(id_persona);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const recordatorio_directivos = (id, id_comite) => {
	swal({
		title: "Enviar recordatorio al Comité.?",
		text: "Tener en cuenta que se enviara un correo a los miembros del comité que aún no han gestionado la solicitud.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Enviar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${Traer_Server()}index.php/compras_control/recordatorio_directivos`, { id }, resp => {
					correos_notificar = resp[0]
					info_solic = resp[1]
					let ser = '<a href="' + server + 'index.php/comite/' + id_comite + '"><b>agil.cuc.edu.co</b></a>'
					let mensaje = `Se le informa que actualmente "${info_solic.nombre_comite}" se encuentra en curso y la solicitud del funcionario ${info_solic.solicitante} requiere de su gestión, puede validar la informaci&oacute;n en ${ser}`;
					enviar_correo_personalizado("comp", mensaje, correos_notificar, "COMITÉ COMPRAS", "Solicitud de compras enviada", "Recordatorio de Comite en Curso", "ParCodAdm", 3);
					MensajeConClase('Recordatorio enviado exitosamente.', 'success', 'Proceso Exitoso.!');
				})
			}
		});

}

/* Listar criterios de evaluacion RP */
const listar_criterios_rp = () => {
	id_criterio = "";
	$("#tabla_mostrar_criterios .asig_cri").off("click");
	$("#tabla_mostrar_criterios .del_cri").off("click");
	$("#tabla_mostrar_criterios .upd_cri").off("click");
	$("#tabla_mostrar_criterios .add_criterio").off("click");
	$("#tabla_mostrar_criterios .see_details").off("click");
	$("#tabla_mostrar_criterios tr").off("click");
	consulta_ajax(`${ruta_interna}listar_criterios_rp`, { }, res => {
		const tabla_criterios = $("#tabla_mostrar_criterios").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: "ver" },
				{ data: "nombre_criterio" },
				{ data: "porcentaje" },
				{ data: "accion" },
			],
			language: get_idioma(),
			buttons: [],
			dom: 'Bfrtip',
		});

		/* Eventos de accion activados */
		$("#tabla_mostrar_criterios .asig_cri").on("click", function () {
			let datos = tabla_criterios.row($(this).parent().parent()).data();
			$("#ModalPermiso").modal();
			criterio_selected = datos.idaux;
			listar_tipos_encuestas_RP(datos.id);
			id_criterio = datos.id;
		});

		/* Actualizar criterio */
		$("#tabla_mostrar_criterios .upd_cri").on("click", function () {
			let datos = tabla_criterios.row($(this).parent().parent()).data();
			form_activo = "upd";
			$("#modal_valor_parametro").modal();
			$(`#modal_valor_parametro #valor`).val(datos.nombre_criterio);
			$(`#modal_valor_parametro #valor`).attr("data-id", datos.id);
			$(`#modal_valor_parametro #valorx`).val(datos.descrip);
		});

		/* Elimininar criterio */
		$("#tabla_mostrar_criterios .del_cri").on("click", function () {
			let datos = tabla_criterios.row($(this).parent().parent()).data();
			swal({
				title: `¿Desea eliminar el Criterio: ${datos.nombre_criterio}?`,
				text: ``,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Eliminar!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (confirm) {
					if (confirm) {
						del_valorp(datos.id);
					}
				});
		});

		/* Adicionar criterios */
		$("#tabla_mostrar_criterios .add_criterio").on("click", function () {
			$("#modal_valor_parametro").modal();
			$("#form_valor_parametro").trigger("reset");
			form_activo = "add";
		});

		/* Ver detalles del criterio */
		$("#tabla_mostrar_criterios .see_details").on("click", function () {
			let datos = tabla_criterios.row($(this).parent().parent()).data();
			$("#modal_detalle_permiso").modal();
			ver_detalles_criterio(datos);
		});

		$("#tabla_mostrar_criterios tr").on("click", function () {
			$('#tabla_mostrar_criterios tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
	});
}

/* Listar tipos de encuesta RP */
const listar_tipos_encuestas_RP = (criterio_id) => {
	sw = false;
	$("#tablapermisoparametro .enable").off('click');
	$("#tablapermisoparametro .disable").off('click');
	$("#tablapermisoparametro tr").off('click');
	consulta_ajax(`${ruta_interna}listar_tipos_encuestas_RP`, { criterio_id, criterio_selected }, res => {
		$("#ModalPermiso .alertpeso_c").html(`<b>Peso Porcentual acumulado: <span class="detalle_peso">${res[0]['total_porcent']}%</span></b>`);
		const tabla_encs = $("#tablapermisoparametro").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: "id" },
				{ data: "area" },
				{ data: "porcent" },
				{ data: "accion" },
			],
			language: get_idioma(),
			buttons: [],
			dom: 'Bfrtip',
		});

		/* Eventos de btns activados */

		/* Habilitar criterio y asignarle un porcentaje */
		$("#tablapermisoparametro .enable").on('click', function () {
			let data = tabla_encs.row($(this).parent().parent()).data();
			swal({
				title: "Habilitar permiso!",
				text: "",
				type: "input",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Asignar",
				cancelButtonText: "Cancelar",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
				inputPlaceholder: "Ingrese procentaje..."
			}, function (porcent) {

				if (porcent === false) {
					return false;
				}

				results();
				async function results() {
					let aviso = await enable_permission(criterio_id, data.id, porcent);
					if (aviso.tipo != "success") {
						swal.showInputError(aviso.mensaje);
						return false;
					} else {
						MensajeConClase(aviso.mensaje, aviso.tipo, aviso.titulo);
						setTimeout(cerrar_swals, 1300);
						listar_tipos_encuestas_RP(criterio_id);
						listar_criterios_rp();
					}
				}
			});
		});

		/* Deshabilitar criterio y retirar el porcentaje asignado */
		$("#tablapermisoparametro .disable").on('click', function () {
			let data = tabla_encs.row($(this).parent().parent()).data();
			swal({
				title: "¿Desea seguir?",
				text: "Tenga en cuenta que los valores establecidos serán reiniciados.",
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
						/* Promesa para desactivar */
						results();
						async function results() {
							let aviso = await disable_permission(criterio_id, data.id);
							if (aviso.tipo != "success") {
								MensajeConClase(aviso.mensaje, aviso.tipo, aviso.titulo);
								return false;
							} else {
								MensajeConClase(aviso.mensaje, aviso.tipo, aviso.titulo);
								setTimeout(cerrar_swals, 1300);
								listar_tipos_encuestas_RP(criterio_id);
								listar_criterios_rp();
							}
						}
						/* Fin de la promesa */
					}
				});
		});

		$("#tablapermisoparametro tr").on('click', function () {
			$("#tablapermisoparametro tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
}

/* Enable permission function */
const enable_permission = (id_cri, id_encuesta, porcentaje) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}enable_permission`, { id_cri, id_encuesta, porcentaje }, res => {
			resolve(res);
		});
	});
}

/* Disable permission function */
const disable_permission = async (id_cri, id_encuesta) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}disable_permission`, { id_cri, id_encuesta }, res => {
			resolve(res);
		});
	});
}

/* Ver detalles del criterio */
const ver_detalles_criterio = (data) => {
	let { nombre_criterio, descrip, porcentaje } = data;

	$(".valor").html(nombre_criterio);
	$(".valorx").html(descrip);
	$(".porcentaje").html(porcentaje);
}

/* Listar poderados RP */
const listar_ponderados_rp = () => {
	$("#tabla_mostrar_ponderados .upd_porcentaje").off('click');
	$("#tabla_mostrar_ponderados .del_porcentaje").off('click');
	$("#tabla_mostrar_ponderados .create_porcentaje").off('click');
	$("#tabla_mostrar_ponderados tbody tr").off('click');
	consulta_ajax(`${ruta_interna}listar_ponderados_rp`, { }, res => {
		const mitabla = $("#tabla_mostrar_ponderados").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: "valor_inicial" },
				{ data: "valor_final" },
				{ data: "porcentaje" },
				{ data: "accion" }
			],
			language: get_idioma(),
			buttons: get_botones(),
			dom: 'Bfrtip',
		});

		/* Eventos de btns activados */

		$("#tabla_mostrar_ponderados tbody tr").on('click', function () {
			$("#tabla_mostrar_ponderados tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		/* Btn para agregar porcentajes */
		$("#tabla_mostrar_ponderados .create_porcentaje").on('click', function () {
			form_activo = "";
			$("#modal_valor_porcentaje").modal();
			$("#form_valor_porcentaje .modal-title").html(`<span class="fa fa-plus fa-lg"></span> Agregar Porcentajes`);
			$("#form_valor_porcentaje").trigger("reset");
			form_activo = "add";
		});

		/* Btn de actualizar porcentaje */
		$("#tabla_mostrar_ponderados .upd_porcentaje").on('click', function () {
			form_activo = "";
			let datos = mitabla.row($(this).parent().parent()).data();
			$("#modal_valor_porcentaje").modal();
			$("#form_valor_porcentaje .modal-title").html(`<span class="fa fa-edit fa-lg"></span> Actualizar Porcentajes`);
			$("#modal_valor_porcentaje #valor_ini").val(datos.valor_inicial).attr("data-id", datos.id);
			$("#modal_valor_porcentaje #valor_fin").val(datos.valor_final);
			$("#modal_valor_porcentaje #porcentaje").val(datos.porcentaje);
			form_activo = "upd";
		});

		/* Btn de eliminar porcentaje */
		$("#tabla_mostrar_ponderados .del_porcentaje").on('click', function () {
			let datos = mitabla.row($(this).parent().parent()).data();
			swal({
				title: "¡Aviso!",
				text: "Tenga en cuenta que, esta acción no se puede revertir.!",
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
						del_porcentajes(datos.id);
						listar_ponderados_rp();
						return false;
					}
				});
		});
	});
}

/* Listar proveedores filtrados */
const listar_proveedores_filtrados = (fecha_desde, fecha_hasta) => {
	consulta_ajax(`${ruta_interna}listar_proveedores_filtrados`, { "fecha_desde": fecha_desde, "fecha_hasta": fecha_hasta }, res => {
		const myTable = $("#tabla_proveedores_f").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{ data: "tipo_compra" },
				{ data: "num_orden" },
				{ data: "proveedor" },
				{ data: "fecha_registra" },
				{ data: "dias_estimados" },
				{ data: "entrega_real" },
				{ data: "entrega_ideal" },
				{ data: "estado" },
			],
			language: get_idioma(),
			buttons: get_botones(),
			dom: 'Bfrtip',
		});
	});
}

/* Listar encuestas RP ya realizadas para la opcion VER */
const list_finished_rp_encs = (tipo_orden) => {
	$("#tabla_encs_rp .ver_preguntas").off("click");
	$("#tabla_encs_rp tbody tr").off("click");
	consulta_ajax(`${ruta_interna}list_finished_rp_encs`, { tipo_orden }, res => {
		let x = 1;
		const mitabla = $("#tabla_encs_rp").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{
					render: function () {
						return `<span>${x++}</span>`;
					}
				},
				{ data: 'area' },
				{ data: 'accion' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		/* Eventos de la tabla pata btns, activados */
		$("#tabla_encs_rp .ver_preguntas").on("click", function () {
			let datos = mitabla.row($(this).parent().parent()).data();
			if (datos.id == 30701) {
				$("#modal_tiempo_entrega").modal();
				consulta_ajax(`${ruta_interna}obtenerDatosSolicitud`, { idSolicitud }, res => {
					const mitabla = $("#tabla_tiempo_entrega").DataTable({
						destroy: true,
						processing: true,
						searching: true,
						data: res,
						columns: [
							{ data: 'entrega_estimada' },
							{ data: 'entrega_real' },
							{ data: 'valoracion' },
						],
						language: get_idioma(),
						dom: 'Bfrtip',
						buttons: [],
					});
				});
			} else {
				$("#modal_finished_rp").modal();
				$("#modal_finished_rp .modal-title h3 span").html(datos.area);
				traer_encuestas_resueltas_rp(idSolicitud, datos.id);
			}
		});

		/* Activar la clase que indica que diste clic sobre una fila */
		$("#tabla_encs_rp tbody tr").on("click", function () {
			$("#tabla_encs_rp tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});
	});
}

/* Traer las encuestas y su respuestas RP */
const traer_encuestas_resueltas_rp = (idsol, id_tipo_enc) => {
	consulta_ajax(`${ruta_interna}traer_encuestas_resueltas_rp`, { idsol, id_tipo_enc }, res => {
		let x = 1;
		const mitabla = $("#tabla_finised_rp").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{
					render: function () {
						return `<span>${x++}</span>`;
					}
				},
				{ data: 'pregunta' },
				{ data: 'respuesta' },
				{ data: 'obs' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});
	});
}

const buscar_jefe = (persona = '') => {
	consulta_ajax(`${Traer_Server()}index.php/compras_control/buscar_jefe`, { persona }, (data) => {
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

		$('#tabla_jefes tbody').on('dblclick', 'tr', function () {
			let { id, fullname, correo } = myTable.row($(this)).data()
			id_pjefe = id;
			correo_jefe = correo;
			$("#txt_nombre_jefe").val(fullname);
			$("#modal_buscar_jefe").modal('hide');
		});
		$('#tabla_jefes tbody').on('click', 'tr span.selec_persona', function () {
			let { id, fullname, correo } = myTable.row($(this).parent().parent()).data()
			id_pjefe = id;
			correo_jefe = correo;
			$("#txt_nombre_jefe").val(fullname);
			$("#modal_buscar_jefe").modal('hide');
		});
	});
};

const cerrar_swals = () => {
	return swal.close();
}

/* Lista las encuestas que cada persona tiene asignada en los modales de encuestas RP y satisfaccion */
const lista_encuestas = async (ids = "") => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}listar_catego_rp`, { ids }, res => {
			resolve(res);
		});
	});
}

/* Traer encuesta siguiente */
const next_enc = async (idsol) => {
	let encList = await lista_encuestas(idsol);

	for (let x = 0; x < encList.length; x++) {
		if (encList[x].idaux != 'satis_enc') {
			if (encList[x].estado == 'incomplete') {
				final_status = encList[x].estado;
				encuesta_activa = encList[x].idaux;
				preguntas_rp(encuesta_activa, idsol);
				break;
			}
		} else if (encList[x].idaux == 'satis_enc') {
			if (encList[x].estado == 'incomplete') {
				final_status = encList[x].estado;
				encuesta_activa = encList[x].idaux;
				mostrar_encuesta(idsol, encList);
				break;
			}
		}
	}

}

/* Function para comenzar a trabajar con los cronogramas */
const compra_crono = async (active = false) => {
	let check = await check_entregable(idsolicitud);
	if (check.numero_entregables == null || check.numero_entregables == '' || check.tiempo_entregables == null || check.tiempo_entregables == '') {
		$('.detalle-entregable').addClass('alert alert-info');
		$('.detalle-entregable').html(`
			<h4 class="text-center"><span class="fa fa-exclamation-triangle"></span> ¡Aviso!</h4>
			<p class="aviso_alert">
				Necesita ingresar los siguientes datos:
			</p>
			<input type="text" class="form-control input-number" placeholder="Número de entregables" id="numero_entregables">
			<input type="text" class="form-control input-number" placeholder="Tiempo de entregables" id="tiempo_entregables">
			<button class="btn btn-danger save_data_crono">Guardar</button>
		`);
		$(`#modal_cronograma`).modal();
		traer_cronograma(idsolicitud);
		$('.save_data_crono').on('click', function(){
			let numero_entregables = $('#numero_entregables').val();
			let tiempo_entregables = $('#tiempo_entregables').val();
			guardar_entregable(numero_entregables, tiempo_entregables, idsolicitud);
			return false;
		});
	} else {
		$('.detalle-entregable').removeClass('alert alert-info');
		$('.detalle-entregable').html(`
			<p><b>Número de entregables:</b> ${check.numero_entregables}</p>
			<p><b>Tiempo de entregables:</b> Cada ${check.tiempo_entregables} Días</p>
		`);
		$(`#modal_cronograma`).modal();
		traer_cronograma(idsolicitud);
	}
}

/* Check si el entregable esta seteado en BD */
const check_entregable = async (id_Solicitud) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}check_entregable`, { id_Solicitud }, res => {
			resolve(res);
		});
	});
}

/* Verifico que el input este correctamente diligenciado para poder guardar el entregable en BD */
const checkit = () => {
	return new Promise(resolve => {
		let entregable_selecto = $('#tipo_entregable');

		entregable_selecto.change(function () {
			if ($(this).val() == 0) {
				$('#error_alert').fadeIn('fast');
				resolve(-1);
			} else {
				$('#error_alert').fadeOut('fast');
			}
		});

		if (entregable_selecto.val() == 0 || entregable_selecto.val() == '') {
			$('#error_alert').fadeIn('fast');
			resolve(-1);
		} else {
			$('#error_alert').fadeOut('fast');
			resolve(entregable_selecto.val());
		}
	});
}

/* Guardar entregables - funcion */
const guardar_entregable = (numero_entregables, tiempo_entregables, id_Solicitud) => {
	consulta_ajax(`${ruta_interna}guardar_entregable`, { numero_entregables, tiempo_entregables, id_Solicitud }, res => {
		if (res) {
			if (res.tipo == "success") {
				$(`#Modal_gestionar_solicitud`).modal('hide');
				swal.close();
				$(`#modal_cronograma`).modal();
				compra_crono(true);
			} else {
				//MensajeConClase(res.mensaje, res.tipo, res.titulo);
				swal({
					title: res.titulo,
					text: res.mensaje,
					type: res.tipo,
					html: res.mensaje,
					showConfirmButton: true,
					allowOutsideClick: true,
					closeOnConfirm: true,
				});
				$('[data-toggle="popover"]').popover();
				return false;
			}
		}
	});
}

/* Listar o traer cronograma */
const traer_cronograma = (id_Solicitud) => {
	datosCrono  = [];
	$(`#tabla_cronograma`).off('click', '.btn_especificar');
	$(`#tabla_cronograma`).off('click', '.upd_crono');
	$(`#tabla_cronograma`).off('click', '.btn_dene');
	$(`#tabla_cronograma`).off('click', 'tr');
	$(`#tabla_cronograma`).off('click', '.fecha_parcial');
	$(`#tabla_cronograma`).off('click', '.btn_ver_detalles_cronograma');
	$(`#form_tipo_entregable`).off('submit');
	consulta_ajax(`${ruta_interna}traer_cronograma`, { id_Solicitud }, res => {
		let show = [];
		let true_false = false;
		let true_false_adj = true;
		if (res.length > 0 && res[0].vista == 1) {
			true_false = true;
			show = [
				{ 'data': 'ver' },
				{ 'data': 'item' },
				{ 
					"render": function (data, type, full, meta) {
						return formatDate(full.fecha, 'full');
					}
				},
				{ 'data': 'detalles_estado' },
				{ 'data': 'acciones' },
			]
		} else {
			true_false = false;
			show = [
				{ 'data': 'ver' },
				{ 'data': 'item' },
				{ 
					"render": function (data, type, full, meta) {
						return formatDate(full.fecha, 'full');
					}
				},
				{ 'data': 'cliente_coment' },
				{ 'data': 'detalles_estado' },
				{ 'data': 'acciones' },
			];
		}
		if(res.length > 0 && res[0].alert == true) $('#form_tipo_entregable .alert').removeClass('oculto');
		

		$(`#modal_cronograma .showdiv`).attr('hidden', true_false);
		$(`#modal_cronograma .showdivadj`).attr('hidden', true_false_adj);
		$(`#tabla_cronograma`).DataTable().clear();
		const MyTable = $(`#tabla_cronograma`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: res,
			columns: show,
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});


		/* Eventos btns ver adjuntos */
		$(`#tabla_cronograma`).on('click', '.btn_ver_detalles_cronograma', function () {			
			datosCrono = MyTable.row($(this).parent().parent()).data();
			listar_adjuntos_cronogramas(datosCrono);
			$('#modal_ver_archvos_cronograma').modal();
			$('#show_estados_crono').removeClass('active');
			$('#show_adj_crono').addClass('active');
			$('#container_adjuntos_cronograma').removeClass('oculto');
			$('#container_estados_cronograma').addClass('oculto');
		});

		// eventos para cambiar la vista
		$('#show_estados_crono').on('click', function(){
			listarEstadosConograma(datosCrono);
			$('#show_estados_crono').addClass('active');
			$('#show_adj_crono').removeClass('active');
			$('#container_adjuntos_cronograma').addClass('oculto');
			$('#container_estados_cronograma').removeClass('oculto');				
		});

		$('#show_adj_crono').on('click', function(){
			listar_adjuntos_cronogramas(datosCrono);
			$('#show_estados_crono').removeClass('active');
			$('#show_adj_crono').addClass('active');
			$('#container_adjuntos_cronograma').removeClass('oculto');
			$('#container_estados_cronograma').addClass('oculto');				
		});

		/* Eventos btns adjuntar cronograma */
		$(`#tabla_adjuntos_cronograma`).on('click', '.agregar_archvos_cronograma', function () {
			$("#modal_enviar_archivos").modal("show");			
			$("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-danger active btnAgregar" id="enviar_adjuntos_cronograma"><span class="glyphicon glyphicon-ok"></span> Temrinar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');

			$('#enviar_adjuntos_cronograma').click(function () {
				if (num_archivos != 0) {
					tipo_cargue = 0;
					$("#id_archivo").val(id_Solicitud);
					$("#id_cronograma").val(datosCrono.id);					
					myDropzone.processQueue();
					listar_adjuntos_cronogramas(datosCrono);						
				} else {
					MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
				}
			});				
		});

		/* Eventos btns activados */
		$(`#tabla_cronograma`).on('click', '.btn_especificar', function () {
			$('#modal_crono_checks').modal();
			let datos = MyTable.row($(this).parent().parent()).data();
			$('#form_crono_checks').off('submit');
			$('#form_crono_checks').submit(function () {
				MensajeConClase("validando info", "add_inv", "Oops...");
				let comentario = $('#especify').val();
				siguienteEstadoCronograma(idsolicitud, comentario, datos.id);
				return false;
			});
		});

		/* Update cronograma, item selecto */
		$(`#tabla_cronograma`).on('click', '.upd_crono', function () {
			let datos = MyTable.row($(this).parent().parent()).data();
			$(`#modal_upd_crono`).modal();
			$(`#fecha_upd`).val(datos.especificaciones);
			$(`#form_upd_crono`).off('submit');
			$(`#form_upd_crono`).submit(function () {
				let espe = $(`#fecha_upd`).val();
				upd_cronograma(idsolicitud, datos.id_entregable, datos.codigo_item, espe, datos.id);
				return false;
			});
		});

		/* Indexa en un array los datos que voy a mandar al array el cual sera enviado por el formulario. */
		$(`#tabla_cronograma`).on('click', '.fecha_parcial', function () {
			let datos = MyTable.row($(this).parent().parent()).data();
			if (datos_selected.length > 0) {
				let doit = true;
				datos_selected.forEach(element => {
					if (element.codigo_item == datos.codigo_item) {
						doit = false;
						return false;
					}
				});
				if (doit) {
					datos_selected.push({ 'id': datos.id, 'codigo_item': datos.codigo_item, 'id_entregable': datos.id_entregable });
				}
			} else {
				datos_selected.push({ 'id': datos.id, 'codigo_item': datos.codigo_item, 'id_entregable': datos.id_entregable });
			}
		});

		/* Denegar un cronograma */
		$(`#tabla_cronograma`).on('click', '.btn_dene', async function () {
			let datos = MyTable.row($(this).parent().parent()).data();
			$('#modal_crono_checks').modal();
			$('#form_crono_checks').off('submit');
			$('#form_crono_checks').submit(function () {
				MensajeConClase("validando info", "add_inv", "Oops...");
				let coment = $('#especify').val();
				denegar_cronograma(datos.id_entregable, id_Solicitud, datos.id, coment);
				return false;
			});
		});
	});
}

const listar_adjuntos_cronogramas = (datos) => {
	consulta_ajax(`${ruta_interna}listar_adjuntos_cronograma`, { id: datos.id }, res => {
		$(`#tabla_adjuntos_cronograma`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: res,
			columns: [
				{ 'data': 'ver' },
				{ 'data': 'nombre_real' },
				{ 'data': 'nombre_guardado' },
				{ 'data': 'fecha_registro' }
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});
	});
}

const listarEstadosConograma = (datos) => {
	consulta_ajax(`${ruta_interna}listarEstadosConograma`, { idCronograma: datos.id }, res => {
		let x = 1;
		$(`#tabla_estados_cronograma`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: res,
			columns: [
				{ render: () => { return x++ } },
				{ 'data': 'valor' },
				{ 'data': 'observacion' },
				{ 'data': 'fecha_registra' }
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});
	});
}

/* Guardar cronogramas */
const siguienteEstadoCronograma = (id_Solicitud, comentario = "", idCrono) => {
	consulta_ajax(`${ruta_interna}guardar_cronograma`, { id_Solicitud, comentario, idCrono }, res => {
		if (res.tipo == "success") {
			if (res.next_step != -1) {
				$('#modal_crono_checks, #Modal_gestionar_solicitud').modal('hide');
				$('form').trigger('reset');
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
				setTimeout(cerrar_swals, 1300);				
				compra_crono(true);
			} else {

				$('#modal_crono_checks').modal('hide');
				$('form').trigger('reset');
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
				setTimeout(cerrar_swals, 1300);
				traer_cronograma(id_Solicitud);
			}

			if (res.next_step === 3) {
				$(`#modal_cronograma`).modal('hide');
				Listar_solicitudes();
			}
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			return false;
		}
	});

}

/* Actualizar cronograma */
const upd_cronograma = (id_Solicitud, entregable, codigo_item, especi, idCrono) => {
	consulta_ajax(`${ruta_interna}upd_cronograma`, { id_Solicitud, entregable, codigo_item, especi, idCrono }, res => {
		if (res.tipo == 'success') {
			datos_selected = [];
			$(`#modal_upd_crono`).modal('hide');
			swal.close();
			traer_cronograma(entregable, id_Solicitud);
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			$(`.confirm`).off('click');
			$(`.confirm`).on('click', function () {
				cerrar_swals();
			});
			return false;
		}
	});
}

/* Denegar un cronograma */
const denegar_cronograma = async (idSol, idCron, coment) => {
	consulta_ajax(`${ruta_interna}denegar_cronograma`, { idSol, idCron, coment }, res => {
		if (res.tipo == "success") {
			$('#modal_crono_checks').modal('hide');
			$('form').trigger('reset');
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			traer_cronograma(idSol);
			setTimeout(cerrar_swals, 1300);
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
		}
	});
}

/* Check de entregables completos */
const check_estado_entregables = async (idEnt = '', idSol = '', idCron = '') => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}check_estado_entregables`, { idEnt, idSol, idCron }, res => {
			resolve(res);
		});
	});
}

/* Funcion que solicita confirmacion del usuario */
const confirm_action = (titulo, msg, tipo, si_btn_text, no_btn_text) => {
	return new Promise(resolve => {
		let btn_color = "";
		if (tipo == "warning") {
			btn_color = "#D9534F";
		} /*else if (btn_color == info) {
			btn_color = "#7DA8F0";
		}*/
		swal({
			title: titulo,
			text: msg,
			type: tipo,
			html: msg,
			showCancelButton: true,
			confirmButtonColor: btn_color,
			confirmButtonText: si_btn_text,
			cancelButtonText: no_btn_text,
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				resolve(1);
			} else {
				resolve(0);
			}
		});
	});
}

const traerDatosValorP = (id = '', idaux = '', metodoEntrega = true) => { //true para que regrese row
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}traerDatosValorP`, { id, idaux, metodoEntrega }, res => {
			resolve(res);
		});
	});
}

const nav_encs = async (modal_apunta, encType) => {
	modal_apunta.forEach(element => {
		$(`${element} li[data-id="${encType}"]`).removeClass('active').addClass('active');
	});
}

/* Traer datos valorp */
const find_idParametro = (codigo) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_interna}find_idParametro`, { codigo }, res => {
			resolve(res);
		});
	});
}

const asignar_encuesta_cronogramas = (persona, estado) => {
	consulta_ajax(`${ruta_interna}asignar_encuesta_cronogramas`, { persona, estado }, res => {
		let { titulo, mensaje, tipo } = res;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			permisos_cronogramas(persona);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const desasignar_encuesta_cronogramas = (persona, estado) => {
	consulta_ajax(`${ruta_interna}desasignar_encuesta_cronogramas`, { persona, estado }, res => {
		let { titulo, mensaje, tipo } = res;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			permisos_cronogramas(persona);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const listarPersConEncuestas = () => {
	$('#tabla_pers_encuestas_pendientes .verSoliConEncuesta').off('click');
	$('#tabla_pers_encuestas_pendientes .notificarPers').off('click');
	consulta_ajax(`${ruta_interna}listarPersConEncuestas`, {}, res => {
			const tablaPersEncPen = $(`#tabla_pers_encuestas_pendientes`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: res,
			columns: [
				{  render: () => { return `<span title="Ver solicitudes" data-toggle="popover" data-trigger="hover" class="btn btn-default verSoliConEncuesta">Ver</span>` } },
				{ 'data': 'solicitante' },
				{ 'data': 'num_soli' },
				{  render: () => { return `<span title="Notificar" data-toggle="popover" data-trigger="hover" class="fa fa-bell btn btn-default notificarPers" style="color:#3874A8"></span>` } }
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});

		$('#tabla_pers_encuestas_pendientes').on('click', '.verSoliConEncuesta', function () {
			const data = tablaPersEncPen.row($(this).parent().parent()).data();
			verSoliConEncuesta(data.idper);
		});

		$('#tabla_pers_encuestas_pendientes').on('click', '.notificarPers', async function () {
			MensajeConClase("validando info", "add_inv", "Oops...");
			const data = tablaPersEncPen.row($(this).parent().parent()).data();
			await notificarPers(data);
		});
	});
}

const verSoliConEncuesta = (idper) => {
	consulta_ajax(`${ruta_interna}listarSoliConEncuestas`, { idper }, res => {
		$(`#tabla_encuestas_pendientes`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: res,
			columns: [
				{ 'data': 'tipo_compra' },
				{ 'data': 'solicitante' },
				{ 'data': 'fecha_registra' },
				{ 'data': 'indice_fecha' },
				{ 'data': 'num_orden' }
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});
		$('#modal_encuestas_pendientes').modal();
	});
}

const notificarPers = async ({idper, correo, solicitante}) => {
	await consulta_ajax(`${ruta_interna}listarSoliConEncuestas`, { idper }, async res => {
		let ser = '<a href="' + server + 'index.php/compras"><b>agil.cuc.edu.co</b></a>';
		let dataBody = '';
		res.map((solicitud) => {
			dataBody += `<tr>
				<td>${solicitud.indice_fecha}</td>
				<td>${solicitud.num_orden}</td>
				<td>${solicitud.tipo_compra}</td>
				<td>${solicitud.fecha_registra}</td>
			</tr> `;
		})
		let mensaje = `
			Hola, Cordialmente informamos que tiene (s) encuesta (s) pendientes de satisfacción al cliente, las cuales debes realizar para que tengas acceso a nuevas solicitudes
			<br>Mas información en : ${ser}
			<br>
			<br>Solicitudes con encuestas pendientes:
			<table>
				<thead style="font-weight: bold;">
					<tr>
						<td>No.</td>
						<td>#Orden</td>
						<td>Tipo</td>
						<td>Fecha Solicitud</td>
					</tr>
				</thead>
				<tbody>${dataBody}</tbody>
			</table>
		`;
		enviar_correo_personalizado("comp", mensaje, correo, solicitante, "Compras CUC", "Encuestas - Modulo de Compras", "ParCodAdm", 1);
		MensajeConClase("Notificacion enviada correctamente.", "success", "Exito!");
	});
}

$(document).on('input', '.input-number', function () {
	this.value = this.value.replace(/[^0-9]/g, '');
})

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