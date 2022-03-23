// DECLARACION DE VARIABLES
let perifericos_sele_guardar = []; //guardo los perifericos seleccioandos de la tabla inventario para guardarlos
let id_inventario = 0; //Capturo el id del recurso seleccionado de la tabla inventario
let id_mantenimiento = 0; //capturo el id del mantenimeinto seleccionado de la tabla mantenimiento
let estado_mante = ""; // capturo el estado actual del mantenimiento de la tabla mantenimiento
let estado_x = ""; // capturo el estado actual del mantenimiento de la tabla inventario
let en_sele_peri = 0;
let tipo = 0;
let lista_perifericos = Array();
let tipo_recurso = 0;
let tipo_modulo = "";
let datos_vista = {};
let ruta = null;
let ruta_ = `${Traer_Server()}index.php/inventario_control/`;
const ruta_documentos = `${Traer_Server()}archivos_adjuntos/laboratorios/`;
let responsables = [];
let id_proveedor = 0;
let nombre_completo_cont = null;
let num_archivos = 0;
let myDropzone = 0;
let num_archivos_cargados = 0;
let codigo_interno_ = null;
let serial_ = null;
let tipo_articulo = null;
let id_detalle_inventario = null;
let id_codigo_sap = null;
let tipo_periferico = null;
let estado_recurso_ = null;
let en_fecha = "";
let id_articulo = "";
let dependencia_id = "";
let adm_activo = null;
let sw_filtro = true;
let tipo_listar_dep = "ubi";
let tipo_documento_id = null;
//Variables de busqueda
let id_ubicacion = "";
let sw_buscado = "";
let estado_buscado = "";
//Variables Para acciones masivas
let acciones_masivas = false;
let articulos = [];
let tipo_modificar = "";
let callbak_activo = (resp) => {};
let callback_activo = (resp) => {};
let callbak_activo_alt = (resp) => {};
let callbak_activo_acciones = (resp) => {
	MensajeConClase("Seleccione la acción que desea realizar", "info", "Oops.!");
};
let datos_activos = {};
let requerimiento = [];
let datos = [];

$(document).ready(function () {
	gestionar_ruta(window.location.pathname);
	if (ruta == "laboratorios") {
		$("#inicio-user").hide();
	}

	$("#btn_agregar_perifericos").click(() => {
		$("#Modal-Perifericos").modal("show");
		Listar_perifericos();
	});

	$("#form_agregar_mantenimiento").submit((e) => {
		agregar_mantenimiento_lab(e.target);
		e.preventDefault();
	});

	$("#btn_agregar_mantenimiento_lab").click(() => {
		$("#modal_agregar_mantenimiento").modal();
	});

	$("#btn_mantenimiento").click(() => {
		$("#modal_mantenimientos_lab").modal();
		get_mantenimientos_lab(id_inventario);
	});

	$("#btndocumentos_recurso").click(() => {
		$("#modal_documentos").modal();
		cargar_documentos_disponibles(id_inventario);
	});

	$("#btn_documentos").click(() => {
		cargar_documentos_disponibles(id_inventario);
		$("#modal_documentos").modal();
	});

	$("#form_datos_tecnicos").submit((e) => {
		e.preventDefault();
		guardar_datos_tecnicos(e.target);
	});

	$("#modal_adjuntar_documento").on("hidden.bs.modal", () => {
		cargar_documentos_disponibles(id_inventario);
	});

	$("#btn_estado_solicitudes").click(() => {
		$("#inicio-user").css("display", "block");
		$("#menu_principal").css("display", "none");
	});

	if (tipo_modulo === "Inv_Lab") {
		$("#btn_regresar_menu").click(() => {
			$("#inicio-user").css("display", "none");
			$("#menu_principal").fadeIn(1000);
		});
	}

	$("#btn_informacion_tecnica").click(() => {
		$("#modal_requerimientos_tecnicos").modal();
		cargar_requerimientos_tecnicos();
	});

	$("#btn_datos_tecnicos").click(() => {
		$("#modal_datos_tecnicos").modal();
	});

	$("#modal_detalle_laboratorios").on("hidden.bs.modal", () => {
		id_inventario = 0;
	});

	$("#cambiar_vista").click(() => {
		if (tipo_listar_dep === "dep") {
			tipo_listar_dep = "ubi";
			$("#txtnombre_tabla").html("UBICACIONES");
		} else {
			tipo_listar_dep = "dep";
			$("#txtnombre_tabla").html("DEPENDENCIAS");
		}
		listar_dependencias(tipo_listar_dep);
	});

	$("#form_search_person").submit((e) => {
		const texto = $("#txt_search_person").val();
		listar_personas(texto, (data) => {
			let num = 1;
			$("#tabla_personas_permisos tbody")
				.off("click", "tr")
				.off("click", "tr .permisos");
			const myTable = $("#tabla_personas_permisos").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ render: (data, type, full, meta) => num++ },
					{ data: "nombre" },
					{
						render: (data, type, full, meta) =>
							`<span class="btn btn-default permisos" title="Administrar Permisos"><span class="fa fa-cog" style="color:#777;"></span></span>`,
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			$("#tabla_personas_permisos tbody").on("click", "tr", function () {
				const data = myTable.row(this).data();
				$("#tabla_personas_permisos tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_personas_permisos tbody").on(
				"click",
				"tr .permisos",
				function () {
					const { id } = myTable.row($(this).parent().parent()).data();
					get_permisos_persona(id);
					$("#Modal_administrar_permisos").modal();
					$("#nav_admin_permisos li").removeClass("active");
					$("#per_equipos_asignados").addClass("active");
					$("#container_asignados").fadeIn(1000);
				}
			);
		});
		e.preventDefault();
	});

	$("#agregar_responsable").click(() => {
		callbak_activo = (data) => seleccionar_persona(data);
		$("#txt_dato_buscar").val("");
		buscar_persona("", callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$(".permisos input[type='checkbox']").click((e) => {
		const accion = e.target.name;
		const sw = e.target.checked;
		const permiso = "asignados";
		const data = $("#tabla_personas_permisos")
			.DataTable()
			.row(".warning")
			.data();
		const { id, nombre } = data;

		swal(
			{
				title: `¿ ${sw ? "Asignar" : "Desasignar"} Permiso ?`,
				text: `<strong>${nombre}</strong> tendrá permisos para ${accion}`,
				type: "warning",
				html: true,
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
					gestionar_permiso_persona(id, permiso, sw, accion);
				} else e.target.checked = !sw;
			}
		);
	});

	$("#buscar_responsables").click(() => {
		callbak_activo = ({ id }) => {
			id_ubicacion = "";
			tipo = id;
			sw_buscado = "responsable";
			limpiar_filtros();
			$("#dispositivos_modal").modal();
			$("#modal_buscar_persona").modal("hide");
		};
		$("#txt_dato_buscar").val("");
		buscar_persona("", callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$("#seleccionar_todo").click(() => {
		Listar_articulos(
			(id, thiss) => seleccionar_articulos_masi(id, thiss),
			(recursos) => seleccionar_todo_articulos(recursos)
		);
	});

	$("#limpiar_filtros_masivos").click(() => {
		Listar_articulos(
			(id, thiss) => seleccionar_articulos_masi(id, thiss),
			() => (articulos = [])
		);
		id_inventario = "";
	});

	$("#accion_masiva input[name='accion']").change(function () {
		let accion = $(this).val();
		let estado_ant = estado_buscado;
		estado_buscado = "";
		switch (accion) {
			case "ubi":
				callbak_activo_acciones = () => {
					$("#form_agregar_lugar_nuevo").get(0).reset();
					$("#modal_agregar_lugar_nuevo").modal();
				};
				break;
			case "res":
				callbak_activo_acciones = () => {
					callbak_activo = (data) => seleccionar_responsable_new(data);
					$("#txt_dato_buscar").val("");
					buscar_persona("", callbak_activo);
					$("#modal_buscar_persona").modal();
				};
				break;
			case "man":
				estado_buscado = "RecAct";
				callbak_activo_acciones = () => {
					$("#form_asignar_mantenimiento").get(0).reset();
					$("#modal_asignar_mantenimiento").modal();
				};
				break;
			case "rep":
				estado_buscado = "RecMat";
				callbak_activo_acciones = () => {
					confirmarAccion(
						() => terminar_mantenimiento_masivo(articulos),
						"¿ Equipos Reparados ?",
						`Todos los mantenimientos asignados serán terminados, si desea continuar presione la opción de 'Si, Entiendo!'`
					);
				};
				break;
			default:
				callbak_activo_acciones = (resp) => {
					MensajeConClase(
						"Seleccione la acción que desea realizar",
						"info",
						"Oops.!"
					);
				};
				break;
		}
		if (estado_ant != estado_buscado)
			Listar_articulos(
				(id, thiss) => seleccionar_articulos_masi(id, thiss),
				() => (articulos = [])
			);
	});

	$("#buscar_serial").click(() => buscar_serial_mensaje());

	$("#btn_admin_solicitudes").click(() => {
		variables = () => 2;
		$("#nav_admin_inventario li").removeClass("active");
		administrar_modulo("tipo_recurso", 6);
		$("#admin_tipo_recursos").addClass("active");
		$("#Modal_administrar_solicitudes").modal("show");
	});

	$("#btnacciones").click(() => {
		$(".mensaje_notificacion").fadeIn("fast");
		$(".acciones_tabla").fadeOut("fast");
		$("#accion_masiva").get(0).reset();
		articulos = [];
		acciones_masivas = true;
		Listar_articulos((id, thiss) => seleccionar_articulos_masi(id, thiss));
	});

	$("#btncancelar").click(() => $("#limpiar_filtros").trigger("click"));

	$("#btnaceptar").click(() => {
		if (articulos.length > 0) callbak_activo_acciones(articulos);
		else
			MensajeConClase(
				"Debe seleccionar al menos un recurso para gestionar",
				"info",
				"Oops.!"
			);
	});

	$("#Buscar_Codigo_Orden").submit((e) => {
		const codigo = $("#txtcodigo_sap").val();
		buscar_valor_parametro(codigo, idparametro_activo);
		e.preventDefault();
	});

	$("#buscar_cod_sap").click(() => {
		const codigo = $("#txtcodigo_sap").val();
		buscar_valor_parametro(codigo, idparametro_activo);
		e.preventDefault();
	});

	$("#sap_input").click(() => buscar_codigo());
	$("#sap_search").click(() => buscar_codigo());

	$("#btn_modificar_cod").click(() => {
		idparametro_activo = 25;
		callbak_activo = (resp) => {
			let { id, nombre } = resp;
			id_codigo_sap = id;
			$("#btn_modificar_cod").html(nombre);
			$("#Buscar_Codigo").modal("hide");
			$("#txtcodigo_sap").val("");
		};
		$("#Buscar_Codigo .modal-title").html(
			'<span class="fa fa-search"></span> Buscar Codigo SAP'
		);
		$("#Buscar_Codigo").modal();
		buscar_valor_parametro("$$$$++1");
	});

	$("#btn_buscar_mod").click(() => {
		idparametro_activo = 25;
		callbak_activo = (resp) => {
			let { id, nombre } = resp;
			id_codigo_sap = id;
			$("#btn_modificar_cod").html(nombre);
			$("#Buscar_Codigo").modal("hide");
			$("#txtcodigo_sap").val("");
		};
		$("#Buscar_Codigo .modal-title").html(
			'<span class="fa fa-search"></span> Buscar Codigo SAP'
		);
		$("#Buscar_Codigo").modal();
		buscar_valor_parametro("$$$$++1");
	});

	$("#form_modificar_periferico").submit(() => {
		tipo_periferico = $("#tipo_agregar_mod option:selected").text();
		// marca_periferico = $("#marca_agregar_mod option:selected").text();
		// modelo_periferico = $("#modelo_agregar_mod option:selected").text();
		modificar_periferico(serial_);
		return false;
	});

	$("#form_buscar_persona").submit(() => {
		let dato = $("#txt_dato_buscar").val();
		buscar_persona(dato, callbak_activo);
		return false;
	});

	$("#inventario_lugares").click(() => {
		if (id_inventario == 0) {
			MensajeConClase(
				"Antes de continuar seleccione el dispositivo ..!",
				"info",
				"Oops..."
			);
		} else {
			listar_lugares(id_inventario);
			$("#modal_tb_lugares").modal("show");
		}
	});

	$("#retirar_responsable").click(() => eliminar_responsable());

	$("#agregar_periferico").click(() => {
		const recurso = $(
			`#dataRecursos option[value='${$("#lista_recursos").val()}']`
		).attr("class");
		if (recurso) {
			listar_tipos_recursos_asignados(recurso);
			$("#modal_agregar_periferico").modal();
		} else
			MensajeConClase(
				"Por favor seleccione un tipo de recurso",
				"info",
				"Ooops!"
			);
	});

	$("#nuevo_lugar").click(() => {
		$("#form_agregar_lugar_nuevo").get(0).reset();
		$("#modal_agregar_lugar_nuevo").modal();
	});

	$("#inventario_perifericos").click(() => {
		$("#Modal-Perifericos").modal("show");
		Listar_perifericos();
	});

	$("#nuevo-periferico").click(() => {
		if (estado_x == "RecBaja") {
			MensajeConClase(
				"El recurso Fue dado de Baja, por tal motivo no puede asignarle Periféricos ..!",
				"info",
				"Oops..."
			);
		} else {
			en_sele_peri = 1;
			$("#Modal_asignar_periferico").modal();
			traer_perifericos("", tipo);
			$("#txt_periferico").val("");
		}
	});

	$("#frm_agregar_periferico").submit((e) => {
		tipo_periferico = $("#tipo_agregar option:selected").text();
		asignar_perifericos();
		e.preventDefault();
	});
	$("#form_agregar_lugar_nuevo").submit((e) => {
		guardar_lugar_nuevo(id_inventario);
		e.preventDefault();
	});

	$("#btn_buscar_periferico").click(() =>
		traer_perifericos($("#txt_periferico").val(), tipo)
	);

	$("#Guardar_inventario select[name='id_lugar']").change(function () {
		const id = $(this).val().trim();
		listar_ubicaciones(id, '#Guardar_inventario select[name="id_ubicacion"]');
	});

	$("#Guardar_inventario select[name='marca']").change(function () {
		const id = $(this).val().trim();
		listar_modelos(id, '#Guardar_inventario select[name="modelo"]');
	});

	$("#Modificar_inventario select[name='marca']").change(function () {
		const id = $(this).val().trim();
		$("#Modificar_inventario select[name='modelo_mod']").val("");
		listar_modelos(id, '#Modificar_inventario select[name="modelo_mod"]');
	});

	$("#form_agregar_lugar_nuevo select[name='id_lugar']").change(function () {
		const id = $(this).val().trim();
		listar_ubicaciones(
			id,
			'#form_agregar_lugar_nuevo select[name="id_ubicacion"]'
		);
	});

	$("#departamento_sele_guardar").change(function () {
		const valory = $(this).val().trim();
		Listar_cargos_departamento_combo(
			"#cbxcargo",
			"Seleccione Cargo Responsable",
			valory,
			0
		);
	});

	$("#modal_filtros .cbxlugar").change(function () {
		const id = $(this).val().trim();
		listar_ubicaciones(id, "#modal_filtros .cbxubicacion");
	});

	$("#form_agregar_responsable").submit((e) => {
		agregar_responsable();
		$("#form_agregar_responsable").get(0).reset();
		e.preventDefault();
	});

	$("#btn_no_retirar").click(() => {
		callbak_activo_alt("");
	});

	$("#btn_retirar").click(() => {
		callbak_activo_alt(1);
	});

	$("#departamento_sele_traslado").change(function () {
		const valory = $(this).val().trim();
		Listar_cargos_departamento_combo(
			"#cargo_traslado",
			"Seleccione Cargo Responsable",
			valory,
			0
		);
	});

	$("#cbxrecursos").change(function () {
		if ($(this).val().length != 0) {
			obtener_valor_parametro_id_recurso($(this).val());
		}
	});

	$(".btnAgregar").click(() => {
		$("#cbxrecursos").val("");
		$(".CampoGeneral").attr("required", "true");
		$(".CampoComputador").removeAttr("required");
		$(".CampoPortatil").removeAttr("required");
		$("#Info-Portatil").hide("fast");
	});

	$("#Recargar").click(() => location.reload());

	$("#nuevo-responsable").click(() => {
		callbak_activo = (data) => seleccionar_responsable_new(data);
		$("#txt_dato_buscar").val("");
		buscar_persona("", callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$("#btnBuscarpersona").click(() => {
		const identificacion = $("#txtIdentificacionpersona").val();
		const tipo_identificacion = $("#tipo_ide_persona").val();
		if (tipo_identificacion.length == 0) {
			MensajeConClase("Seleccione Tipo de identificacion", "info", "Oops...");
		} else if (identificacion.trim().length == 0) {
			MensajeConClase("Ingrese Numero de identificacion", "info", "Oops...");
		} else {
			obtener_datos_persona_identificacion(
				identificacion,
				tipo_identificacion,
				".nombre_perso",
				".apellido_perso",
				".identi_perso",
				".tipo_id_perso",
				".foto_perso",
				".ubica_perso",
				".depar_perso",
				".cargo_perso",
				"#datos_perso",
				"#id_persona"
			);
		}
	});

	$("#btn-guardar-responsable").click(() => {
		const cargo = $("#cargo_traslado").val().trim();
		if (cargo.length == 0) {
			MensajeConClase(
				"Antes de Guardar debe Seleccionar el nuevo Responsable",
				"info",
				"Oops..."
			);
		} else if (estado_x == "RecBaja") {
			MensajeConClase(
				"El recurso Fue dado de Baja, por tal motivo no esta disponible el Panel de  Mantenimiento ..!",
				"info",
				"Oops..."
			);
		} else {
			swal(
				{
					title: "Estas Seguro ?",
					text:
						"Tener en cuenta que al asignar un nuevo responsable, el sistema automaticamente retirara al actual y lo pasara a la historia de responsables!",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Trasladar!",
					cancelButtonText: "No, cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						Guardar_responsable();
					}
				}
			);
		}
	});

	$("#Modificar_inventario").submit((e) => {
		e.preventDefault();
		modificar_inventario(id_inventario);
	});

	$("#inventario_responsables").click(() => {
		if (id_inventario == 0) {
			MensajeConClase(
				"Antes de continuar seleccione el recurso ..!",
				"info",
				"Oops..."
			);
		} else {
			$("#Modal-responsables").modal();
			Listar_responsables(id_inventario);
		}
	});
	$("#inventario_mantenimiento").click(() => {
		if (tipo_modulo === "Inv_Lab") {
			$("#modal_mantenimientos_lab").modal();
			get_mantenimientos_lab(id_inventario);
		} else {
			id_mantenimiento = 0;
			Cargar_parametro_buscado(
				11,
				".cbx_tipo_mantenimiento",
				"Seleccione Tipo Mantenimiento"
			);
			if (id_inventario == 0) {
				MensajeConClase(
					"Antes de continuar seleccione el recurso ..!",
					"info",
					"Oops..."
				);
			} else {
				Listar_mantenimientos(id_inventario);
				$("#ModalMantenimiento").modal();
			}
		}
	});
	$("#btnBitacora_mantenimiento").click(() => {
		if (tipo_modulo === "Inv_Lab") {
			//$("#modal_asignar_mantenimiento").modal();
			Listar_mantenimientos(id_inventario);
			$("#ModalMantenimiento").modal();
			//asignar_mantenimiento(id);
		} else {
			tipo_mantenimiento = 0;
			Cargar_parametro_buscado(
				11,
				".cbx_tipo_mantenimiento",
				"Seleccione Tipo Mantenimiento"
			);
			if (id == 0) {
				MensajeConClase(
					"Antes de continuar seleccione el recurso ..!",
					"info",
					"Oops..."
				);
			} else {
				Listar_mantenimientos(id);
				$("#ModalMantenimiento").modal();
			}
		}
	});

	$("#btnmodificar_inventario").click(() => {
		$("#Modificar_inventario").get(0).reset();
		obtener_valor_inventario_id(id_inventario, tipo_articulo);
		obtener_detalle_inventario_mod(id_inventario, tipo_articulo);
		if (id_inventario == 0) {
			MensajeConClase(
				"Antes de continuar seleccione el recurso a Modificar..!",
				"info",
				"Oops..."
			);
		} else {
			const { estado_aux } = $("#tbl_articulos")
				.DataTable()
				.row(".warning")
				.data();
			estado_aux === "RecBaja"
				? MensajeConClase(
						"No se permite modificar este recurso porque se encuentra inactivo",
						"info",
						"Oops..."
				  )
				: $("#ModalModificarInventario").modal();
		}
		if (tipo_modulo === "Inv_Lab") {
			$(".accordion_investigacion").css("display", "none");
		} else $(".accordion_investigacion").show("fast");
	});

	$("#Guardar_inventario").submit((e) => {
		registrar_inventario();
		e.preventDefault();
	});

	$("#btneliminar_inventario").click(() => {
		if (id_inventario == 0) {
			MensajeConClase(
				"Antes de continuar seleccione el recurso a dar de baja..!",
				"info",
				"Oops..."
			);
		} else if (estado_x == "RecBaja") {
			MensajeConClase(
				"El recurso ya  Fue dado de Baja Anteriormente..!",
				"info",
				"Oops..."
			);
		} else {
			swal(
				{
					title: "Estas Seguro ?",
					text:
						"Tener en cuenta que al dar de BAJA  el recurso se Desactivara y No se tendra en cuenta para sus diferentes usos..!",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Terminar!",
					cancelButtonText: "No, cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: true,
					closeOnCancel: true,
					inputPlaceholder: "Ingrese Motivo",
				},
				function (mensaje) {
					if (mensaje === false) return false;
					if (mensaje === "") {
						swal.showInputError("Debe Ingresar el motivo.!");
					} else {
						dar_baja(id_inventario, mensaje);
						return false;
					}
				}
			);
		}
	});

	$("#txt_periferico").keypress((e) => {
		const text = $("#txt_periferico").val();
		const code = e.keyCode ? e.keyCode : e.which;
		if (code == 13) {
			if (tipo_modulo === "Inv_Lab") get_accesorios(text);
			else traer_perifericos(text, tipo);
		}
	});

	$("#add-especial").click(() => {
		if (id_inventario == 0) {
			MensajeConClase(
				"Antes de continuar seleccione el recurso que desea pasar a estado especial..!",
				"info",
				"Oops..."
			);
			return;
		}
		if (estado_x != "RecEsp" && estado_x != "RecAct") {
			MensajeConClase(
				"No es posible cambiar el estado, esta opción solo esta disponible para pasar de estado activo a especial y viceversa..!",
				"info",
				"Oops..."
			);
		} else {
			if (estado_x == "RecEsp") {
				estado =
					"Tener en cuenta que al pasar a estado activo el recurso estará disponible para sus diferentes usos.!";
			} else if (estado_x == "RecAct") {
				estado =
					"Tener en cuenta que al pasar a estado especial el recurso se Desactivara para la reserva  y solo estará disponibles para los administradores del modulo de audiovisuales..!";
			}
			swal(
				{
					title: "Estas Seguro ?",
					text: estado,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Cambiar!",
					cancelButtonText: "No, cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						pasar_especial(id_inventario, estado_x);
					}
				}
			);
		}
	});

	$("#form_asignar_mantenimiento").submit((e) => {
		e.preventDefault();
		if (tipo_modulo === "Inv_Lab") {
			asignar_mantenimiento(id_inventario);
		} else asignar_mantenimiento(id_inventario);
	});

	$("#nuevo_mantenimiento").click(() => {
		$("#form_asignar_mantenimiento").get(0).reset();
		estado_recurso_ == "Inactivo"
			? MensajeConClase(
					"El recurso fue dado de baja, no puede agregar un mantenimiento.",
					"info",
					"Oops..."
			  )
			: $("#modal_asignar_mantenimiento").modal();
	});

	$("#limpiar_filtros").click(() => {
		limpiar_filtros();
	});

	$("#ver_en_fecha").click(() => {
		en_fecha = "proyectos";
		id_ubicacion = "";
		tipo = "";
		sw_buscado = "";
		$("#limpiar_filtros").trigger("click");
		$("#dispositivos_modal").modal();
	});

	$("#ver_en_garantia").click(() => {
		en_fecha = "garantia";
		id_ubicacion = "";
		tipo = "";
		sw_buscado = "";
		$("#limpiar_filtros").trigger("click");
		$("#dispositivos_modal").modal();
	});
	$("#ver_mantenimientos_a_vencer").click(() => {
		mantenimiento_a_vencer = "mantenimiento";
		id_ubicacion = "";
		tipo = "";
		sw_buscado = "";
		$("#limpiar_filtros").trigger("click");
		$("#dispositivos_modal").modal();
	});

	$("#ver_modificaciones").click(() => {
		listar_modificaciones(id_inventario);
		$("#modal_modificaciones_solicitud").modal();
	});

	$("#admin_permisos").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		mostrar_personas_permisos();
	});
	$("#admin_marcas").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("marcas", 4);
	});
	$("#admin_tipo_recursos").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("tipo_recurso", 6);
	});
	$("#admin_modelos").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("modelos", 5);
	});
	$("#admin_proveedores").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("proveedores", 37);
	});
	$("#admin_procesadores").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("procesador", 9);
	});
	$("#admin_so").click(function () {
		$("#nav_admin_inventario li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo("so", 10);
	});
	$("#form_guardar_valor_parametro").submit((e) => {
		if (adm_activo.parametro === 6) {
			guardar_tipo_recurso();
		} else {
			guardar_valor_parametro();
		}
		e.preventDefault();
	});

	$("#form_modificar_valor_parametro").submit((e) => {
		modificar_valor_parametro();
		e.preventDefault();
	});

	$("#lista_recursos").focusout((e) => {
		const option = $(`#dataRecursos option[value='${e.target.value}']`);
		const id_tipo = option.attr("id");
		const clase = option.attr("class");
		if (id_tipo) {
			tipo = id_tipo;
			if (clase == "Torre" || clase == "PortMini" || clase == "Port") {
				pintar_informacion_general();
				Cargar_parametro_buscado(9, ".cbxprocesador", "Seleccione Procesador");
				Cargar_parametro_buscado(
					10,
					".cbxSistemaOperativo",
					"Seleccione Sistema Operativo"
				);
			} else $("#computadores").html("");
		} else {
			MensajeConClase(
				"Este tipo de recurso no existe. Por favor seleccione uno de la lista.",
				"info",
				"Ooops!"
			);
			e.target.value = "";
		}
	});
	$("#form_buscar_proveedor").submit(() => {
		let dato = $("#txt_proveedores").val();
		buscar_proveedor(dato, callback_activo);
		return false;
	});
	$("#btnBuscar_Proveedor").click(() => {
		callback_activo = (dato) => seleccionar_proveedor(dato);
		$("#txt_proveedores").val("");
		buscar_proveedor("", callback_activo);
		$("#modal_buscar_proveedor").modal();
	});
	$("#btnBuscar_Proveedor_mod").click(() => {
		callback_activo = (dato) => seleccionar_proveedor(dato);
		$("#txt_proveedores").val("");
		buscar_proveedor("", callback_activo);
		$("#modal_buscar_proveedor").modal();
	});
	mostrar_notificaciones();
	$("#modal_notificaciones").modal("show");
	$("#btn_notificaciones").click(() => {
		mostrar_notificaciones();
		$("#modal_notificaciones").modal("show");
	});
});

const seleccionar_proveedor = (dato) => {
	let { id, valor } = dato;
	let nombre_proveedor = valor;
	id_proveedor = id;
	MensajeConClase("Proveedor asignado con exito", "success", "Proceso Exitoso");
	$("#form_buscar_proveedor").get(0).reset();
	buscar_proveedor("", callback_activo);
	$("#txt_Buscar_proveedor").val(nombre_proveedor);
	$("#txt_Buscar_proveedor_mod").val(nombre_proveedor);
	$("#modal_buscar_proveedor").modal("hide");
};

const cargar_tipo_recursos = () => {
	consulta_ajax(`${ruta_}cargar_tipo_recursos`, { tipo_modulo }, (data) => {
		$("#dataRecursos").empty();
		data.forEach(({ id, recurso, aux }) => {
			$("#dataRecursos").append(
				`<option value="${recurso}" id="${id}" class="${aux}">`
			);
		});
	});
};

const cargar_tipo_activo = () => {
	consulta_ajax(`${ruta_}cargar_tipo_recursos`, { tipo_modulo }, (data) => {
		$("#tipo_activo").empty();

		data.forEach(({ id, recurso, aux }) => {
			$('#Modificar_inventario select[name="tipo_activo"]').append(
				`<option value = '${id}'>${recurso}</option>`
			);
		});
	});
};

//BLOQUE DE FUNCIONES DEL MODULO
const mostrar_dispositivos = (id) => {
	tipo = id;
	$("#dispositivos_modal").modal();
	Listar_articulos();
	id_inventario = "";
};

const traer_perifericos = (text) => {
	$("#tbl_perifericos tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr .seleccionar");
	const myTable = $("#tbl_perifericos").DataTable({
		destroy: true,
		ajax: {
			url: `${Traer_Server()}index.php/inventario_control/Traer_perifericos`,
			dataType: "json",
			data: {
				id_inventario,
				text,
			},
			dataSrc: ({ data }) => (data ? data : Array()),
			type: "post",
		},
		processing: true,
		searching: false,
		columns: [
			{ data: "recurso" },
			{ data: "serial" },
			{ data: "codigo_interno" },
			{ data: "marca" },
			{ data: "modelo" },
			{ data: "estado" },
			{ data: "accion" },
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tbl_perifericos tbody").on("click", "tr", function () {
		$("#tbl_perifericos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$("#tbl_perifericos tbody").on("dblclick", "tr", function () {
		const { id } = myTable.row(this).data();
		confirmarAccion(() => relacionar_periferico(id));
	});
	$("#tbl_perifericos tbody").on("click", "tr .seleccionar", function () {
		const { id } = myTable.row($(this).parent().parent()).data();
		confirmarAccion(() => relacionar_periferico(id));
	});
};

const relacionar_periferico = (id) => {
	consulta_ajax(
		`${ruta_}relacionar_periferico`,
		{ recurso: id_inventario, id },
		(resp) => {
			const { mensaje, titulo, tipo: t } = resp;
			if (t == "success") {
				swal.close();
				traer_perifericos("", tipo);
				Listar_perifericos();
			} else MensajeConClase(mensaje, t, titulo);
		}
	);
};

const Listar_inventario = (ubicacion = "", aux = "", lugar = "") => {
	id_ubicacion = ubicacion;
	id_inventario = 0;
	$("#tablainventario tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr td:nth-last-child(1)");
	consulta_ajax(
		`${ruta_}Cargar_inventario`,
		{
			tipo_modulo,
			ubicacion,
			aux,
			lugar,
		},
		(data) => {
			const myTable_inventario = $("#tablainventario").DataTable({
				destroy: true,
				processing: true,
				data,
				columns: [
					{
						defaultContent:
							"<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >ver</span></span>",
					},
					{ data: "nombre" },
					{ data: "cantidad" },
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			//EVENTOS DE LA TABLA ACTIVADOS
			$("#tablainventario tbody").on("click", "tr", function () {
				const { id, nombre } = myTable_inventario.row(this).data();
				tipo = id;
				$("#tablainventario tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
				$("#tipo_recurso").html(nombre);
			});

			$("#tablainventario tbody").on("dblclick", "tr", function () {
				const { id } = myTable_inventario.row(this).data();
				$(".mensaje_notificacion").hide("fast");
				$(".acciones_tabla").show("fast");
				acciones_masivas = false;
				en_fecha = "";
				estado_buscado = "";
				mostrar_dispositivos(id);
				articulos = [];
			});

			$("#tablainventario tbody").on(
				"click",
				"tr td:nth-of-type(1)",
				function () {
					const { id } = myTable_inventario.row(this).data();
					$(".mensaje_notificacion").hide("fast");
					$(".acciones_tabla").show("fast");
					acciones_masivas = false;
					en_fecha = "";
					estado_buscado = "";
					mostrar_dispositivos(id);
					articulos = [];
				}
			);
		}
	);
};

// Buscar por tipo de recurso si aux = false
// Buscar por serial si aux = true

const Listar_articulos = (
	callback = (id, thiss) => seleccionar_articulos_indi(id, thiss),
	todo = ""
) => {
	consulta_ajax(
		`${ruta_}Cargar_articulos`,
		{
			tipo_modulo,
			ubicacion: id_ubicacion,
			buscar: tipo,
			aux: sw_buscado,
			estado: estado_buscado,
			en_fecha,
			lugar: tipo_listar_dep === "ubi" ? dependencia_id : "",
		},
		({ recursos, ad, per }) => {
			$("#tbl_articulos tbody")
				.off("dblclick", "tr")
				.off("click", "tr td:nth-of-type(1)")
				.off("click", "tr .perifericos")
				.off("click", "tr .responsables")
				.off("click", "tr .lugares")
				.off("click", "tr .dar_baja")
				.off("click", "tr .mantenimiento")
				.off("click", "tr .documentos")
				.off("click", "tr .edit_act")
				.off("click", "tr");

			const myTable = $("#tbl_articulos").DataTable({
				destroy: true,
				processing: true,
				data: recursos,
				columns: [
					{ data: "codigo" },
					{ data: "recurso" },
					{ data: "serial" },
					{ data: "nombre_activo" },
					{ data: "codigo_interno" },
					{ data: "marca" },
					{ data: "modelo" },
					{ data: "valor" },
					{ data: "lugar" },
					{ data: "ubicacion" },
					{ data: "estado_recurso" },
					{ data: "accion" },
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: get_botones(""),
			});
			myTable.column(7).visible(false);
			if (tipo_modulo != "Inv_Lab") myTable.column(3).visible(false);

			if (acciones_masivas) {
				if (todo) todo(recursos);
				myTable.column(10).visible(false);
				myTable.column(0).visible(false);
				$("#tbl_articulos")
					.DataTable()
					.rows()
					.every(function (rowIdx, tableLoop, rowLoop) {
						const { id } = this.data();
						let existe = articulos.find((i) => i == id);
						if (existe) this.nodes().to$().addClass("warning");
					});
			}

			$("#tbl_articulos tbody").on("click", "tr", function () {
				const { id, estado_aux, estado_recurso, tipo } = myTable
					.row(this)
					.data();
				id_inventario = id;
				estado_x = estado_aux;
				estado_recurso_ = estado_recurso;
				tipo_articulo = tipo;
				callback(id, this);
			});

			$("#tbl_articulos tbody").on("dblclick", "tr", function () {
				if (!acciones_masivas) {
					const data = myTable.row(this).data();
					id_inventario = data.id;
					$("lista_requerimientos").val("");
					obtener_info_inventario_tabla_id(data);
					tipo_articulo = data.tipo;
					obtener_detalle_inventario(data.id, data.tipo);
				}
			});

			$("#tbl_articulos tbody").on(
				"click",
				"tr td:nth-of-type(1)",
				function () {
					if (!acciones_masivas) {
						const data = myTable.row($(this).parent()).data();
						id_inventario = data.id;
						$("lista_requerimientos").val("");
						obtener_info_inventario_tabla_id(data);
						tipo_articulo = data.tipo;
						obtener_detalle_inventario(data.id, data.tipo);
					}
				}
			);

			$("#tbl_articulos tbody").on("click", "tr .perifericos", function () {
				$("#Modal_asignar_periferico").modal();
				traer_perifericos("", tipo);
				$("#txt_periferico").val("");
			});

			$("#tbl_articulos tbody").on("click", "tr .accesorios", function () {
				$("#Modal_asignar_periferico").modal();
				get_accesorios();
				$("#txt_periferico").val("");
			});

			// $("#tbl_articulos tbody").on("click", "tr .documentos", function () {
			// 	const { id } = myTable.row($(this).parent()).data();
			// 	$("#modal_documentos").modal();
			// 	cargar_documentos_disponibles(id);
			// });

			$("#tbl_articulos tbody").on("click", "tr .dar_baja", function () {
				confirDarBajaRecurso();
			});

			$("#tbl_articulos tbody").on("click", "tr .responsables", function () {
				callbak_activo = (data) => seleccionar_responsable_new(data);
				$("#txt_dato_buscar").val("");
				buscar_persona("", callbak_activo);
				$("#modal_buscar_persona").modal();
			});

			$("#tbl_articulos tbody").on("click", "tr .lugares", function () {
				const { id } = myTable.row($(this).parent()).data();
				listar_lugares(id);
				$("#modal_agregar_lugar_nuevo").modal("show");
			});

			$("#tbl_articulos tbody").on("click", "tr .mantenimiento", function () {
				const { id } = myTable.row($(this).parent()).data();
				if (tipo_modulo === "Inv_Lab") {
					$("#modal_asignar_mantenimiento").modal();
					get_mantenimientos_lab(id);
				} else $("#modal_asignar_mantenimiento").modal();
			});

			$("#tbl_articulos tbody").on("click", "tr .edit_act", function () {
				const { id } = myTable.row($(this).parent()).data();
				$(".alert-guardar_inv").css("display", "none");
				$("#ModalModificarInventario").modal();
				obtener_valor_inventario_id(id);
				cargar_tipo_activo();
			});
		}
	);
};

const get_accesorios = (text) => {
	consulta_ajax(`${ruta_}get_accesorios`, { text, tipo_modulo }, (data) => {
		$("#tbl_perifericos tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr .asignar");

		const myTable = $("#tbl_perifericos").DataTable({
			destroy: true,
			data: text ? data : [],
			processing: true,
			searching: false,
			columns: [
				{ data: "recurso" },
				{ data: "serial" },
				{ data: "codigo_interno" },
				{ data: "marca" },
				{ data: "modelo" },
				{ data: "estado" },
				{
					render: (data, type, full, meta) => {
						return '<span title="Asignar Periférico" data-toggle="popover" data-trigger="hover" style="color: #5cb85c;margin-left: 5px" class="pointer fa fa-check btn btn-default asignar"></span>';
					},
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tbl_perifericos tbody").on("click", "tr", function () {
			$("#tbl_perifericos tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tbl_perifericos tbody").on("click", "tr .asignar", function () {
			const { id } = myTable.row($(this).parent().parent()).data();
			confirmarAccion(() => relacionar_periferico(id));
		});
	});
};

//La funcion Terminar_Mantenimiento se encarga de activar un recurso que se encontraba en mantenimiento
const Terminar_mantenimiento = () => {
	//Llamada AJAX que ejecuta la accion, se pasa el id del tipo de mantenimiento y el id del recurso del inventario a terminar
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/Terminar_Mantenimiento`,
		dataType: "json",
		data: {
			id: id_mantenimiento,
			id_inventario: id_inventario,
		},
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se le informa al usuario
		switch (datos) {
			case "sin_session":
				close();
				return false;
			case 1:
				swal.close();
				Listar_mantenimientos(id_inventario);
				mostrar_dispositivos(tipo);
				id_mantenimiento = 0;
				return true;
			default:
				MensajeConClase(
					"Error al Terminar El Mantenimiento",
					"error",
					"Oops..."
				);
				break;
		}
	});
};

//La funcion Listar_responsables muestras el historial de los responsables del recurso en especifico
const Listar_responsables = (id) => {
	$("#tabla-responsables tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr td .eliminar");
	const myTable = $("#tabla-responsables").DataTable({
		destroy: true,
		ajax: {
			url: `${Traer_Server()}index.php/inventario_control/Cargar_responsables`,
			dataType: "json",
			data: { id },
			dataSrc: ({ data }) => (data ? data : Array()),
			type: "post",
		},
		processing: true,
		columns: [
			{ data: "ver" },
			{ data: "persona" },
			{ data: "fecha_asigna" },
			{
				render: function (data, type, full, meta) {
					let { estado } = full;
					let estado_ = "";
					estado == 1 ? (estado_ = "Asignado") : (estado_ = "Retirado");
					return estado_;
				},
			},
			{ data: "accion" },
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: get_botones(),
	});
	//Activo los eventos de la tabla
	$("#tabla-responsables tbody").on("click", "tr", function () {
		$("#tabla-responsables tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	$("#tabla-responsables tbody").on("dblclick", "tr", function () {
		let data = myTable.row(this).data();
		ver_detalle_responsable(data);
	});
	$("#tabla-responsables tbody").on(
		"click",
		"tr td:nth-of-type(1)",
		function () {
			let data = myTable.row(this).data();
			ver_detalle_responsable(data);
		}
	);
	$("#tabla-responsables tbody").on("click", "tr td .eliminar", function () {
		let { id, id_inventario } = myTable.row($(this).parent().parent()).data();
		eliminar_responsable_asignado(id, id_inventario);
	});
};

// La funcion Listar_mantenimientos carga el historial de los mantenimientos de un recurso en especifico
const Listar_mantenimientos = (id) => {
	let permisos = [];
	let admin = false;
	id_mantenimiento = 0;
	$("#tabla-mantenimiento tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr .terminar")
		.off("click", "tr td:nth-of-type(1)");
	const myTable = $("#tabla-mantenimiento").DataTable({
		destroy: true,
		ajax: {
			url: `${Traer_Server()}index.php/inventario_control/Cargar_mantenimiento`,
			dataType: "json",
			data: { id },
			dataSrc: ({ data, ad, per }) => {
				if (per) permisos = per[0];
				admin = ad;
				return data ? data : Array();
			},
			type: "post",
		},
		processing: true,
		columns: [
			{
				render: function (info, type, full, meta) {
					const { estado } = full;
					const en_curso =
						'<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control "><span>ver</span></span>';
					const terminado =
						'<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #39B23B;color: white;width: 100%;" class="pointer form-control "><span>ver</span></span>';
					return estado == "En curso" ? en_curso : terminado;
				},
			},
			{ data: "tipo" },
			{ data: "fecha" },
			{ data: "usuario" },
			{ data: "estado" },
			{
				render: function (info, type, { estado, estado_valor }, meta) {
					const en_curso =
						'<span style="color: #39B23B;" title="Terminar mantenimiento" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default terminar" ></span>';
					const terminado =
						'<span style="color: #a0a0a0;" title="Mantenimiento Terminado" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off" ></span>';
					if (tipo_modulo === "Inv_Tec") {
						if (estado_valor === "Mat_Pend") {
							return en_curso;
						} else if (estado_valor === "Mat_Curs") {
							return en_curso;
						}
						return terminado;
					} else if (tipo_modulo === "Inv_Lab") {
						if (estado_valor === "Mat_Pend") {
							return en_curso;
						} else if (estado_valor === "Mat_Curs") {
							return en_curso;
						}
						return terminado;
					} else {
						if (estado_valor === "Mat_Pend") {
							return en_curso;
						} else if (estado_valor === "Mat_Curs") {
							return en_curso;
						}
						return terminado;
					}
				},
			},
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: get_botones(),
	});
	//Activo los eventos de la tabla
	$("#tabla-mantenimiento tbody").on("click", "tr", function () {
		const { id, estado_valor } = myTable.row(this).data();
		$("#tabla-mantenimiento tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		id_mantenimiento = id;
		estado_mante = estado_valor;
	});
	$("#tabla-mantenimiento tbody").on("click", "tr .terminar", function () {
		let { id } = myTable.row($(this).parent().parent()).data();
		id_mantenimiento = id;
		Terminar_Mat();
	});
	$("#tabla-mantenimiento tbody").on(
		"click",
		"tr td:nth-of-type(1)",
		function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_mantenimiento(data);
		}
	);
};
const registrar_inventario = () => {
	//obtengo los datos del formulario Guardar_inventario para guardarlo
	let data = new FormData(document.getElementById("Guardar_inventario"));
	data.append("recurso", tipo);
	data.append("tipo_modulo", tipo_modulo);
	data.append("id_codigo_sap", id_codigo_sap);
	data.append("tipo_modulo", tipo_modulo);
	if (tipo && responsables.length)
		data.append("responsables", JSON.stringify(responsables));
	data.append("id_proveedor", id_proveedor);
	enviar_formulario(
		`${ruta_}guardar_inventario`,
		data,
		({ titulo, mensaje, tipo, errores, id }) => {
			if (tipo == "success") {
				id_inventario = id;
				listar_dependencias(tipo_listar_dep);
				lista_perifericos = [];
				responsables = [];
				responsables_agregados();
				$("#Guardar_inventario").get(0).reset();
				$("#myModal").modal("hide");
				error = isArrayEmpty(errores);
				if (!error) {
					let error = "";
					errores.forEach((element) => (error += `${element}\n`));
					MensajeConClase(
						`Su registro fue guardado con los siguientes errores: \n\n ${error} \n Por favor modifique el registro.`,
						tipo,
						titulo
					);
					$("#modal_detalle_laboratorios").modal();
				}
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
	function isArrayEmpty(errores) {
		return !Array.isArray(errores) || !errores.length;
	}
};

const dar_baja = (id, mensaje) => {
	consulta_ajax(`${ruta_}dar_baja`, { id, mensaje }, (resp) => {
		let { titulo, mensaje, tipo: t } = resp;
		if (t == "success") {
			Listar_articulos();
			swal.close();
		} else MensajeConClase(mensaje, t, titulo);
	});
};

//la funcion pasar_especial pasa a estado especial un recurso en especifico
const pasar_especial = (id, estado) => {
	//Llamada AJAX que ejecuta la accion se le pasa el id del recursos a dar de baja
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/pasar_especial`,
		dataType: "json",
		data: { id, estado },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se le informa al usuario
		switch (datos) {
			case "sin_session":
				close();
				break;
			case 1:
				swal.close();
				Listar_articulos();
				break;
			case -1302:
				MensajeConClase(
					"No tiene Permisos Para Realizar Esta Opereacion",
					"error",
					"Oops..."
				);
				break;
			case -2:
				MensajeConClase(
					"No es posible cambiar el estado, esta opción solo esta disponible para pasar de estado activo a especial y viceversa..!",
					"info",
					"Oops..."
				);
			default:
				MensajeConClase(
					"Error al dar de baja a el recurso",
					"error",
					"Oops..."
				);
				break;
		}
	});
};

const modificar_inventario = (id) => {
	let formdata = new FormData(document.getElementById("Modificar_inventario"));
	let data = formDataToJson(formdata);
	data.id = id;
	data.id_detalle_inventario = id_detalle_inventario;
	data.id_codigo_sap = id_codigo_sap;
	data.id_inventario = id_inventario;
	data.recurso = tipo;
	data.tipo_modulo = tipo_modulo;
	tipo_modulo == "Inv_Lab" ? (data.id_proveedor = id_proveedor) : [];
	consulta_ajax(`${ruta_}modificar_inventario`, data, (resp) => {
		let { mensaje, tipo: tipo_res, titulo } = resp;
		if (tipo_res == "success") {
			$("#Modificar_inventario").get(0).reset();
			Listar_articulos();
			$("#ModalModificarInventario").modal("hide");
			if (tipo_modulo == "Inv_Lab") {
				mostrar__datos__tecnicos(id);
				$("#modal_detalle_laboratorios").modal();
			}
			$("#btn_modificar_cod").text("Seleccione Código SAP");
		}
		MensajeConClase(mensaje, tipo_res, titulo);
	});
};

const obtener_valores_inventario = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta_}obtener_valores_inventario`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_valor_inventario_id = async (id) => {
	let {
		descripcion,
		nombre_activo,
		fecha_inicio_proyecto,
		fecha_fin_proyecto,
		codigo_sap,
		id_codigo_sap: codigo_sap_id,
		serial,
		codigo_interno,
		uso_del_activo,
		referencia,
		valor,
		lugar_origen,
		observaciones,
		id_marca,
		id_modelo,
		fecha_ingreso,
		fecha_garantia,
		tipo,
	} = await obtener_valores_inventario(id);

	let modelos = await obtener_permisos_parametros(id_marca);
	if (tipo_modulo == "Inv_Lab") {
		pintar_datos_combo(
			modelos,
			"#Modificar_inventario select[name='modelo_mod']",
			"Seleccione Modelo"
		);
		$("#Modificar_inventario select[name='uso_equipo']").val(uso_del_activo);
		$("#Modificar_inventario input[name='nombre_activo_modi']").val(
			nombre_activo
		);
		$("#Modificar_inventario input[name='referencia']").val(referencia);
		$("#Modificar_inventario input[name='valor']").val(valor ? valor : 0);
		$("#Modificar_inventario input[name='lugar_origen']").val(lugar_origen);
		$("#observaciones_modi").val(observaciones);
		$("#Modificar_inventario select[name='marca']").val(id_marca);
		$("#Modificar_inventario input[name='fecha_ingreso']").val(fecha_ingreso);
		$("#Modificar_inventario input[name='fecha_garantia']").val(fecha_garantia);
		$("#Modificar_inventario select[name='tipo_activo']").val(tipo);
		$("#Modificar_inventario select[name='modelo_mod']").val(id_modelo);
	}

	$("#descripcion_modi").val(descripcion);
	$("#Modificar_inventario input[name='fecha_inicio_proyecto']").val(
		fecha_inicio_proyecto
	);
	$("#Modificar_inventario input[name='fecha_fin_proyecto']").val(
		fecha_fin_proyecto
	);
	$("#Modificar_inventario input[name='serial']").val(serial);
	$("#Modificar_inventario input[name='codigo_interno']").val(codigo_interno);
	id_codigo_sap = codigo_sap_id;
	$("#btn_modificar_cod").text(codigo_sap);
};

//La funcion obtener_info_inventario_tabla_id muestra la informacion de un recurso en especifico obteniendo la informacion de la tabla
const obtener_info_inventario_tabla_id = ({
	recurso,
	fecha_garantia,
	codigo_interno,
	fecha_ingreso,
	marca,
	modelo,

	serial,
	valor,
	descripcion,
	fecha_inicio_proyecto,
	fecha_fin_proyecto,
	codigo_sap,
	motivo_baja,
	estado_aux,
}) => {
	$(".valor_recurso").html(recurso);
	$(".valor_garantia").html(fecha_garantia);
	$(".valor_cod_in").html(codigo_interno);
	$(".valor_ingreso").html(fecha_ingreso);
	$(".valor_marca").html(marca);
	$(".valor_modelo").html(modelo);
	$(".valor_serial").html(serial);
	$(".valor_valor").html(valor);
	$(".valor_descripcion").html(descripcion);
	$(".fecha_inicio_proyecto").html(fecha_inicio_proyecto);
	$(".fecha_fin_proyecto").html(fecha_fin_proyecto);
	$(".id_codigo_sap").html(codigo_sap);
	if (estado_aux === "RecBaja") {
		$("#msj_motivo_baja").fadeIn("fast");
		$(".valor_baja").html(
			`<strong style="color:#d9534f;">${motivo_baja}</strong>`
		);
	} else {
		$("#msj_motivo_baja").fadeOut("fast");
		$(".valor_baja").html("");
	}
	if (
		fecha_fin_proyecto == null &&
		fecha_inicio_proyecto == null &&
		codigo_sap == null
	)
		$("#tabla_detalle_proyecto").hide();
	else $("#tabla_detalle_proyecto").show();
	$("#Modal-info-dispositivo").modal("show");
};
//La funcion obtener_detalle_inventario muestra el detalle de un recurso en especifico, se le pasa por parametro el recursos y el tipo de recurso
const obtener_detalle_inventario = (id, tipo) => {
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/obtener_detalle_inventario_info`,
		dataType: "json",
		data: { id, tipo, tipo_modulo },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se muestra la informacion
		if (datos == "sin_session") {
			close();
			return false;
		}
		if (tipo_modulo === "Inv_Lab") {
			if (datos.datos_tecnicos) {
				$("#tabla_datos_tecnicos .valor_tecnologia").html(datos.tecnologia);
				$("#tabla_datos_tecnicos .valor_fase").html(datos.fase);
				$("#tabla_datos_tecnicos .valor_estado").html(datos.estado);
				$("#tabla_datos_tecnicos .valor_vida_util").html(
					`${datos.vida_util} años`
				);
				$("#tabla_datos_tecnicos .valor_peso").html(`${datos.peso}Kg`);
				$("#tabla_datos_tecnicos .valor_potencia").html(
					`${datos.potencia}${datos.unidades}`
				);
				$("#tabla_datos_tecnicos .valor_voltaje").html(`${datos.voltaje}V`);
			} else $("#tabla_datos_tecnicos").css("display", "none");
			$(".tabla_info_inventario .valor_referencia").html(datos.referencia);
			$(".tabla_info_inventario .valor_nombre_activo").html(
				datos.nombre_activo
			);
			$(".tabla_info_inventario .valor_uso").html(datos.uso_equipo);
			$(".tabla_info_inventario .valor_lugar").html(`${datos.lugar_origen}`);
			$(".tabla_info_inventario .valor_proveedor").html(`${datos.proveedor}`);
			$(".tabla_info_inventario .valor_observaciones").html(
				`${datos.observaciones}`
			);
			$(".tabla_info_inventario .valor_valor").html(
				`${valor_peso(datos.valor)}`
			);
			if (!datos.requerimientos.length) {
				$("#div_requerimientos").css("display", "none");
			} else if (datos.requerimientos.length) {
				$("#lista_requerimientos").html("");
				$("#lista_requerimientos").css("display", "block");
				datos.requerimientos.forEach(({ requerimiento }) =>
					$("#lista_requerimientos").append(`<li>${requerimiento}</li>`)
				);
			}
		} else {
			if (datos == "") {
				$("#tabla_detalle_recurso").hide("fast");
				$("#msj_periferico").fadeOut("fast");
				$(".valor_periferico").html("");
			} else {
				if (datos.periferico) {
					$("#msj_periferico").fadeIn("fast");
					$(".valor_periferico").html(datos.data[0].serial);
					$("#tabla_detalle_recurso").hide("fast");
				} else {
					if (tipo == "Torre" || tipo == "Port" || tipo == "PortMini") {
						$("#msj_periferico").fadeOut("fast");
						$(".valor_periferico").html("");
						const {
							procesador,
							disco_duro,
							memoria,
							sistema_operativo,
						} = datos[0];
						$(".valor_procesador").html(procesador);
						$(".valor_discoduro").html(disco_duro);
						$(".valor_memoria").html(memoria);
						$(".valor_sistemaope").html(sistema_operativo);
						$("#tabla_detalle_recurso").show("fast");
					}
				}
			}
		}
	});
};

obtener_info_sesion = (perfil, url, persona) =>
	(datos_vista = { perfil, url, persona });

const obtener_detalle_inventario_mod = (id, tipo) => {
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/obtener_detalle_inventario_info`,
		dataType: "json",
		data: { id, tipo },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se muestra la informacion
		if (datos == "sin_session") {
			close();
			return false;
		}
		if (datos.periferico) {
			$("#tipo_modificar").hide("fast");
			$(".valor_sistemaope_mod").removeAttr("required");
			$(".valor_procesador_mod").removeAttr("required");
			$(".valor_discoduro_mod").removeAttr("required");
			$(".valor_memoria_mod").removeAttr("required");
		} else {
			if (datos[0]) {
				const {
					id_procesador,
					disco_duro,
					memoria,
					id_sistema_operativo,
					id,
					codigo_interno,
					serial,
				} = datos[0];
				$("#Modificar_inventario input[name='codigo_interno']").val(
					codigo_interno
				);
				$("#Modificar_inventario input[name='serial']").val(serial);
				$(".valor_sistemaope_mod").val(id_sistema_operativo);
				$(".valor_procesador_mod").val(id_procesador);
				$(".valor_discoduro_mod").val(disco_duro);
				$(".valor_memoria_mod").val(memoria);
				id_detalle_inventario = id;
			}

			$("#tipo_modificar").show("fast");
			$(".valor_sistemaope_mod").attr("required", "true");
			$(".valor_procesador_mod").attr("required", "true");
			$(".valor_discoduro_mod").attr("required", "true");
			$(".valor_memoria_mod").attr("required", "true");
		}
	});
};

//Funcion que envia un mensaje de confirmacion al terminar un mantenimiento
const Terminar_Mat = () => {
	if (id_mantenimiento == 0) {
		MensajeConClase("Seleccione Mantenimiento a terminar", "info", "Oops...");
	} else if (estado_mante === "Mat_Term") {
		MensajeConClase(
			"El Mantenimiento ya se encuentra Terminado",
			"info",
			"Oops..."
		);
	} else {
		swal(
			{
				title: "Estas Seguro ?",
				text:
					"Tener en cuenta que al Terminar el Mantenimiento el recurso se Activara y se tendra en cuenta para sus diferentes usos..!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Terminar!",
				cancelButtonText: "No, cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			function (isConfirm) {
				if (isConfirm) {
					//Si confirma la accion se ejecuta la funcion Terminar_mantenimiento
					Terminar_mantenimiento();
				}
			}
		);
	}
};

//La funcion obtener_valor_parametro_id_recurso se encarga de mostrar u ocultar la informacion del formulario Guardar_inventario dependiento del tipo de recurso
function obtener_valor_parametro_id_recurso(idparametro) {
	$.ajax({
		//Llamada AJAX que ejecuta la accion
		url: `${Traer_Server()}index.php/genericas_control/obtener_valor_parametro_id`,
		dataType: "json",
		data: { idparametro },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se muesta u oculta informacion
		if (datos == "sin_session") {
			close();
			return;
		}
		const { id_aux, idparametro } = datos[0];
		if (idparametro == 6) {
			if (id_aux == "Comp") {
				$(".CampoGeneral").removeAttr("required");
				$(".CampoComputador").attr("required", "true");
				$("#tipoGeneral").hide("slow");
				return 2;
			} else {
				if (id_aux == "Port" || id_aux == "PortMini") {
					$(".CampoPortatil").attr("required", "true");
					$("#Info-Portatil").show("slow");
				} else {
					$(".CampoPortatil").removeAttr("required");
					$("#Info-Portatil").hide("slow");
				}
				$(".CampoGeneral").attr("required", "true");
				$(".CampoComputador").removeAttr("required");
				$("#tipoGeneral").show("slow");
				return 2;
			}
		}
		return -1;
	});
}

//Esta funcion le asigna un codigo interno aquellos recursos que pertenecen al area de audiovisuales
const Asignar_codigo_interno = (id, codigo_interno) => {
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/Modificar_codigo_interno`,
		dataType: "json",
		data: { id, codigo_interno },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se le informa al usuario
		switch (datos) {
			case "sin_session":
				close();
				break;
			case "1":
				Guardar_responsable();
				break;
			case "-11":
				MensajeConClase(
					"El codigo Interno ya esta registrado en el sistema",
					"info",
					"Oops..."
				);
				return true;
			default:
				MensajeConClase("Error al Asignar El codigo", "error", "Oops...");
				break;
		}
	});
};

//lA FUNCION Listar_peListar_perifericos() muestra los recursos conectados ya sea a un portatil, computador o cualquier otro recurso
const Listar_perifericos = () => {
	periferico_sele = 0;
	$("#tabla-perifericos tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)");
	var myTable = $("#tabla-perifericos").DataTable({
		destroy: true,
		ajax: {
			url: `${Traer_Server()}index.php/inventario_control/Listar_perifericos`,
			dataType: "json",
			data: {
				id: id_inventario,
			},
			dataSrc: (json) => (json.length == 0 ? Array() : json.data),
			type: "post",
		},
		processing: true,
		columns: [
			{
				data: "ver",
			},
			{
				data: "recurso",
			},
			{
				data: "serial",
			},
			{
				data: "fecha_registra",
			},
			{
				render: function (data, type, full, meta) {
					let { estado } = full;
					let estado_ = "";
					estado == 1 ? (estado_ = "Asignado") : (estado_ = "Retirado");
					return estado_;
				},
			},
			{
				data: "accion",
			},
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: get_botones(),
	});
	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tabla-perifericos tbody").on("click", "tr", function () {
		const { id } = myTable.row(this).data();
		periferico_sele = id;
		$("#tabla-perifericos tbody tr").removeClass("warning");
	});
	$("#tabla-perifericos tbody").on(
		"click",
		"tr td:nth-of-type(1)",
		function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_perifericos(data);
		}
	);
};

//Esta funcion le asigna uno o varios perifericos a un recurso
const guardar_perifericos = (id_recurso, perifericos) => {
	if (perifericos_sele_guardar.length == 0) {
		MensajeConClase(
			"Antes de terminar debe seleccionar los periféricos de la tabla inventario",
			"info",
			"Oops..."
		);
		return;
	}
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/guardar_perifericos`,
		dataType: "json",
		data: { id_recurso, perifericos },
		type: "post",
	}).done((datos) => {
		var datos_pos0 = datos[0];
		//si la accion se ejecuto de manera correcta se le informa al usuario
		if (datos_pos0 == "sin_session") {
			close();
			return false;
		}
		if (datos_pos0 == -1302) {
			MensajeConClase(
				"No tiene Permisos Para Realizar Esta Opereacion",
				"error",
				"Oops..."
			);
			return;
		}
		switch (datos_pos0) {
			case 1:
				MensajeConClase(
					"Perifericos Asignados con Exito al recurso.",
					"success",
					"Proceso Exitoso..!"
				);
				en_sele_peri = 0;
				$("#mensaje-sale-peri").hide("fast");
				Listar_perifericos();
				perifericos_sele_guardar = [];
				break;
			case 2:
				const ya_asignados = datos[1];
				MensajeConClase(
					"los siguientes seriales ya estan asignados como perifericos por tal motivo no fue posible asignarlos, retire estos perifericos de los recursos en los cuales estan asignado e intente de nuevo:\n\n" +
						ya_asignados,
					"info",
					"Oops..."
				);
				en_sele_peri = 0;
				$("#mensaje-sale-peri").hide("fast");
				Listar_perifericos();
				perifericos_sele_guardar = [];
				break;
			case 3:
				MensajeConClase(
					"Antes de terminar debe seleccionar los periféricos de la tabla inventario",
					"info",
					"Oops..."
				);
				break;
			default:
				MensajeConClase(
					"Error al Asignar los perifericos,contacte al administrador",
					"error",
					"Oops..."
				);
				break;
		}
	});
};

//Esta funcion valida si un periferico se encuentra asignado a un recurso
const Periferico_ya_asignado = (id, thiss) => {
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/Periferico_ya_asignado`,
		dataType: "json",
		data: { periferico: id },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se le informa al usuario
		if (datos == "sin_session") {
			close();
			return false;
		}
		if (datos.length != 0) {
			MensajeConClase(
				"El periferico seleccionado ya se encuetra asignado a otro recurso, retire el periferico del recurso en el cual esta asignado e intente de nuevo.\n\n Asignado el recurso con serial: " +
					datos[0].serial_recurso,
				"info",
				"Oops..."
			);
		} else {
			perifericos_sele_guardar.push(id);
			$(this).attr("class", "success");
		}
	});
};

//Esta funcion le retira un periferico en especifo de un recurso
const retirar_periferico = (id) => {
	//Llamada AJAX que ejecuta la accion
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/retirar_periferico`,
		dataType: "json",
		data: { id },
		type: "post",
	}).done((datos) => {
		//si la accion se ejecuto de manera correcta se le informa al usuario
		if (datos == 1) {
			swal.close();
			Listar_perifericos();
		} else {
			MensajeConClase(
				"Error al retirar el perifericos,contacte al administrador",
				"error",
				"Oops..."
			);
		}
		return;
	});
};

//Funcion que envia un mensaje de confirmacion al retirar_periferico
const confirmar_retirar_periferico = (id) => {
	if (id.length == 0) {
		MensajeConClase(
			"El id del periferico a retirar no fue encontrado, contacte con el administrador",
			"error",
			"Oops..."
		);
	} else {
		swal(
			{
				title: "Estas Seguro ?",
				text:
					"Tener en cuenta que al retirar el periferico estara disponible para otro recurso..!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			function (isConfirm) {
				if (isConfirm) {
					//Si confirma la accion se ejecuta la funcion retirar_periferico
					retirar_periferico(id);
				}
			}
		);
	}
};

const pintar_informacion_general = () =>
	$("#computadores").html(
		"<div class='login-body'><div id='login-access' class='tab-pane fade active in'><div id='general' class='form-group '><div class='col-md-6'><select name='procesador' required class='form-control inputt cbxprocesador CampoComputador'><option>Seleccione Procesador</option></select></div><div class='col-md-6'><select name='sistemaOperativo' required class='form-control inputt cbxSistemaOperativo CampoComputador' ><option>Seleccione Sistema Operativo</option></select></div><div class='col-md-6'><input type='number' name='memoria' required placeholder='Memoria RAM' min='0' class='form-control CampoComputador' ></div><div class='col-md-6'><input type='number' name='discoDuro' min='0' required placeholder='Disco Duro ' class='form-control CampoComputador' ></div></div><div class='btn-group btn-group-justified' role='group' aria-label='...'><div class='btn-group' role='group'></div></div></div></div>"
	);

const asignar_perifericos = () => {
	let data = new FormData(document.getElementById("frm_agregar_periferico"));
	data.append("tipo_periferico", tipo_periferico);
	// data.append('marca_periferico', marca_periferico);
	// data.append('modelo_periferico', modelo_periferico);
	let da = formDataToJson(data);
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/verificar_perifericos`,
		type: "post",
		dataType: "json",
		data,
		cache: false,
		contentType: false,
		processData: false,
	}).done((datos) => {
		// si la accion se ejecuto de manera correcta se le informa al usuario
		const { mensaje, tipo, titulo, data } = datos;
		if (tipo == "sin_session") {
			close();
		} else if (tipo == "success") {
			let sw_serial = false;
			let sw1_codigo = false;
			lista_perifericos.forEach(({ serial, codigo_interno, prefijo }) => {
				if (data.serial == serial) sw_serial = true;
				else if (
					`${data.prefijo}${data.codigo_interno}` ==
					`${prefijo}${codigo_interno}`
				) {
					if (`${data.prefijo}${data.codigo_interno}` == "") sw1_codigo = false;
					else sw1_codigo = true;
				}
			});
			if (sw_serial)
				MensajeConClase("El serial ingresado ya existe.", "info", "Oops.!");
			else if (sw1_codigo)
				MensajeConClase(
					"El codigo interno ingresado ya existe.",
					"info",
					"Oops.!"
				);
			else {
				lista_perifericos.push(data);
				MensajeConClase(mensaje, tipo, titulo);
				$("#frm_agregar_periferico").get(0).reset();
				listar_perifericos_solicitud();
			}
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const gestionar_ruta = (route) => {
	const pos = route.indexOf("index.php/");
	route = route.slice(pos + 10, route.length);
	ruta = route.replace(/[0-9]+/g, "");
	if (ruta[ruta.length - 1] === "/") ruta = ruta.substr(0, ruta.length - 1);
	if (ruta === "tecnologia/inventario") {
		tipo_modulo = "Inv_Tec";
		$("#add-especial").remove();
	} else if (ruta === "tecnologia/inventarioAUD") {
		tipo_modulo = "Inv_Aud";
		$("#btn_ver_inves").remove();
	} else if (ruta === "laboratorios") {
		tipo_modulo = "Inv_Lab";
		$("#add-especial").remove();
		$("#btn_ver_inves").remove();
	} else tipo_modulo = null;
};

const listar_personas_cargos_combo = (combo, mensaje, id) => {
	consulta_ajax(`${ruta_}listar_personas_cargos`, { id }, (resp) => {
		$(combo).html(`<option value=''>${mensaje}</option>`);
		resp.map((elemento) => {
			let { persona, id } = elemento;
			$(combo).append(`<option value= "${id}">${persona}</option>`);
		});
	});
};

const responsables_agregados = () => {
	let cantidad = responsables.length;
	$('#Guardar_inventario select[name="personal_asignado"]').html(
		`<option value = ''>(${cantidad}) Responsable(s)</option>`
	);
	responsables.map((elemento) => {
		$('#Guardar_inventario select[name="personal_asignado"]').append(
			`<option value = '${elemento.id}'>${elemento.nombre}</option>`
		);
	});
};

const eliminar_responsable = () => {
	let responsable_sele = $("#personal_asignado").val();
	let responsable_text = $(
		'#personal_asignado option[value="' + responsable_sele + '"]'
	).text();
	if (responsable_sele == "")
		MensajeConClase("Seleccione un responsable.", "info", "Oops...!");
	else {
		swal(
			{
				title: "Estas Seguro ?",
				text: `Esta seguro de eliminar el responsable ${responsable_text}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
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
					if (responsable_sele == "") {
						MensajeConClase("Seleccione un responsable.", "info", "Oops...!");
					} else {
						let respon = responsables.find(
							(element) => element.id == responsable_sele
						);
						responsables.splice(respon, 1);
						responsables_agregados();
						MensajeConClase(
							"Se eliminó con éxito",
							"success",
							"Proceso Exitoso!"
						);
					}
				}
			}
		);
	}
};

const ver_detalle_responsable = (data) => {
	let {
		persona,
		persona_agrega,
		persona_elimina,
		fecha_asigna,
		fecha_elimina,
		cargo,
		departamento,
		estado,
	} = data;

	$(".persona").html(persona);
	$(".departamento").html(departamento);
	$(".cargo").html(cargo);
	$(".persona_agrega").html(persona_agrega);
	$(".persona_elimina").html(persona_elimina);
	$(".fecha_asigna").html(fecha_asigna);
	$(".fecha_elimina").html(fecha_elimina);
	estado == 1 ? $(".estado").html("Asignado") : $(".estado").html("Retirado");
	$("#Modal_detalle_responsable").modal();
};

const obtener_permisos_parametros = (id_principal) => {
	return new Promise((resolve) => {
		let url = `${ruta_}listar_permisos_parametros`;
		consulta_ajax(url, { id_principal }, (resp) => {
			resolve(resp);
		});
	});
};
const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((elemento) => {
		$(combo).append(
			`<option value='${elemento.id}'> ${elemento.nombre}</option>`
		);
	});
	$(combo).val(sele);
};
const listar_ubicaciones = async (id_lugar, container) => {
	let ubicaciones = await obtener_permisos_parametros(id_lugar);
	pintar_datos_combo(ubicaciones, container, "Seleccione Ubicacion");
};

const listar_modelos = async (id_marca, container, id_modelo = "") => {
	let modelos = await obtener_permisos_parametros(id_marca);
	pintar_datos_combo(modelos, container, "Seleccione Modelo");
};

const buscar_persona = (dato, callbak) => {
	consulta_ajax(`${ruta_}buscar_persona`, { dato }, (resp) => {
		$(`#tabla_persona_busqueda tbody`)
			.off("click", "tr td .persona")
			.off("dblclick", "tr")
			.off("click", "tr");
		const myTable = $("#tabla_persona_busqueda").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ data: "nombre_completo" },
				{ data: "identificacion" },
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default persona" ></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_persona_busqueda tbody").on("click", "tr", function () {
			$("#tabla_persona_busqueda tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$("#tabla_persona_busqueda tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$("#tabla_persona_busqueda tbody").on(
			"click",
			"tr td .persona",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
			}
		);
	});
};

const seleccionar_persona = (data) => {
	let responsable = {};
	responsable.id = data.id;
	responsable.nombre = data.nombre_completo;
	responsable.id_cargo = data.id_cargo_dep;
	let responsable_ = responsables.find(
		(element) => element.id == responsable.id
	);
	if (responsable_)
		MensajeConClase("El responsable ya fue asignado.", "info", "Oops.!");
	else {
		responsables.push(responsable);
		MensajeConClase(
			"Responsable asignado con exito",
			"success",
			"Proceso Exitoso"
		);
	}
	$("#form_buscar_persona").get(0).reset();
	responsables_agregados();
	buscar_persona("", callbak_activo);
};

const listar_lugares = (id_inventario) => {
	$("#tabla_lugares tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)");
	const myTable = $("#tabla_lugares").DataTable({
		destroy: true,
		ajax: {
			url: `${Traer_Server()}index.php/inventario_control/listar_lugares`,
			dataType: "json",
			data: { id_inventario },
			dataSrc: ({ data }) => (data ? data : Array()),
			type: "post",
		},
		processing: true,
		columns: [
			{
				data: "ver",
			},
			{
				data: "lugar",
			},
			{
				data: "ubicacion",
			},
			{
				data: "fecha_asigna",
			},
			{
				data: "estado_v",
			},
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tabla_lugares tbody").on("click", "tr", function () {
		$("#tabla_lugares tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$("#tabla_lugares tbody").on("dblclick", "tr", function () {
		let data = myTable.row(this).data();
		ver_detalle_lugares(data);
	});
	$("#tabla_lugares tbody").on("click", "tr td:nth-of-type(1)", function () {
		let data = myTable.row(this).data();
		ver_detalle_lugares(data);
	});
	$("#tabla_lugares tbody").on(
		"click",
		"tr td:nth-last-child(1)",
		function () {}
	);
};
const ver_detalle_lugares = (data) => {
	let {
		lugar,
		ubicacion,
		fecha_asigna,
		fecha_retira,
		estado_v,
		usuario_asigna,
		usuario_retira,
	} = data;

	$(".lugar").html(lugar);
	$(".ubicacion").html(ubicacion);
	$(".fecha_asigna").html(fecha_asigna);
	$(".fecha_retira").html(fecha_retira);
	$(".usuario_asigna").html(usuario_asigna);
	$(".usuario_retira").html(usuario_retira);
	$(".estado").html(estado_v);

	$("#modal_lugares").modal();
};
const guardar_lugar_nuevo = (id = "") => {
	let data = formDataToJson(
		new FormData(document.getElementById("form_agregar_lugar_nuevo"))
	);
	data.articulos = acciones_masivas ? articulos : id;
	consulta_ajax(`${ruta_}guardar_lugar_nuevo`, data, (resp) => {
		let { titulo, mensaje, tipo: tipo_m } = resp;
		if (tipo_m == "success") {
			$("#modal_agregar_lugar_nuevo").modal("hide");
			$("#form_agregar_lugar_nuevo").get(0).reset();
			if (!acciones_masivas) {
				listar_lugares(id_inventario);
				Listar_articulos();
			} else
				Listar_articulos((id, thiss) => seleccionar_articulos_masi(id, thiss));
		}
		MensajeConClase(mensaje, tipo_m, titulo);
	});
};

const eliminar_responsable_asignado = (id, id_inventario) => {
	swal(
		{
			title: "Esta Seguro ?",
			text: "Esta seguro que desea retirar un responsable!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, retirar!",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(
					`${ruta_}eliminar_responsable_asignado`,
					{
						id,
						id_inventario,
					},
					(resp) => {
						let { titulo, mensaje, tipo: tipo_m } = resp;
						if (tipo_m == "info") {
							MensajeConClase(mensaje, tipo_m, titulo);
						} else {
							Listar_responsables(id_inventario);
							Listar_articulos();
							swal.close();
						}
					}
				);
			}
		}
	);
};
const guardar_nuevo_responsable = (data) => {
	data.id_inventario = id_inventario;
	consulta_ajax(`${ruta_}guardar_nuevo_responsable`, data, (resp) => {
		let { titulo, mensaje, tipo: tipo_m } = resp;
		if (tipo_m == "success") {
			Listar_responsables(id_inventario);
			$("#form_buscar_persona").get(0).reset();
			buscar_persona("", callbak_activo);
		}
		MensajeConClase(mensaje, tipo_m, titulo);
	});
};

const listar_dependencias = (tipo) => {
	consulta_ajax(
		`${ruta_}listar_dependencias`,
		{ tipo_modulo, tipo },
		({ dependencias, ad, per }) => {
			$("#tabla_dependencias tbody")
				.off("click", "tr td:nth-of-type(1)")
				.off("click", "tr")
				.off("dblclick", "tr");
			let myTable = $("#tabla_dependencias").DataTable({
				destroy: true,
				data: dependencias,
				processing: true,
				columns: [
					{
						defaultContent:
							"<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >ver</span></span>",
					},
					{ data: "dependencia" },
					{ data: "cantidad" },
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			//EVENTOS DE LA TABLA ACTIVADOS
			$("#tabla_dependencias tbody").on("click", "tr", function () {
				const { id } = myTable.row($(this)).data();
				dependencia_id = id;
				$("#tabla_dependencias tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_dependencias tbody").on("dblclick", "tr", function () {
				const { id } = myTable.row($(this)).data();
				ubicaciones_dependencias(id);
				$("#modal_ubicaciones").modal();
			});

			$("#tabla_dependencias tbody").on(
				"click",
				"tr td:nth-of-type(1)",
				function () {
					const { id } = myTable.row($(this).parent()).data();
					ubicaciones_dependencias(id);
					$("#modal_ubicaciones").modal();
				}
			);
		}
	);
};

const ubicaciones_dependencias = (dependencia) => {
	sw_buscado = "";
	consulta_ajax(
		`${ruta_}ubicaciones_dependencias`,
		{ dependencia, tipo_modulo, tipo_listar_dep },
		(data) => {
			$("#tabla_ubicaciones tbody")
				.off("click", "tr td:nth-of-type(1)")
				.off("dblclick", "tr")
				.off("click", "tr");
			let myTable = $("#tabla_ubicaciones").DataTable({
				destroy: true,
				data,
				processing: true,
				columns: [
					{
						defaultContent:
							"<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >ver</span></span>",
					},
					{ data: "ubicacion" },
					{ data: "cantidad" },
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: get_botones(),
			});
			//EVENTOS DE LA TABLA ACTIVADOS
			$("#tabla_ubicaciones tbody").on("click", "tr", function () {
				const { id } = myTable.row($(this)).data();
				id_ubicacion = id;
				$("#tabla_ubicaciones tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_ubicaciones tbody").on("dblclick", "tr", function () {
				const { id, id_aux } = myTable.row($(this)).data();
				$("#modal_inventario").modal();
				if (tipo_listar_dep === "ubi") {
					Listar_inventario(id, id_aux, dependencia_id);
				} else Listar_inventario(id, id_aux);
			});

			$("#tabla_ubicaciones tbody").on(
				"click",
				"tr td:nth-of-type(1)",
				function () {
					const { id, id_aux } = myTable.row($(this).parent()).data();
					$("#modal_inventario").modal();
					if (tipo_listar_dep === "ubi") {
						Listar_inventario(id, id_aux, dependencia_id);
					} else Listar_inventario(id, id_aux);
				}
			);
		}
	);
};

const listar_perifericos_solicitud = () => {
	$("#modal_listar_perifericos").modal();

	$("#tabla_perifericos_sol tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr td .eliminar")
		.off("click", "tr td .editar");
	let myTable = $("#tabla_perifericos_sol").DataTable({
		destroy: true,
		data: lista_perifericos,
		processing: true,
		columns: [
			{ data: "tipo_periferico" },
			{
				render: function (data, type, { codigo_interno, prefijo }, meta) {
					return `${prefijo}${codigo_interno}`;
				},
			},
			{ data: "serial" },
			{ data: "valor" },
			{ data: "descripcion" },
			{
				render: function (data, type, full, meta) {
					return `<span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default editar"></span> <span title="Retirar Periferico" data-toggle="popover" data-trigger="hover" class="btn btn-default fa fa-trash-o eliminar" style="color:red"></span>`;
				},
			},
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: get_botones(),
	});
	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tabla_perifericos_sol tbody").on("click", "tr", function () {
		$("#tabla_perifericos_sol tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$("#tabla_perifericos_sol tbody").on("click", "tr td .editar", function () {
		let data = myTable.row($(this).parent().parent()).data();
		codigo_interno_ = data.codigo_interno;
		serial_ = data.serial;
		listar_recursos_inventario(tipo, "#form_modificar_periferico", data.tipo);
		ver_periferico(data);
	});
	$("#tabla_perifericos_sol tbody").on("click", "tr td .eliminar", function () {
		let { serial } = myTable.row($(this).parent().parent()).data();
		eliminar_periferico(serial);
	});
};

const eliminar_periferico = (serial) => {
	swal(
		{
			title: "Estas Seguro ?",
			text: `Esta seguro de eliminar el periférico?, si desea continuar presione la opción de 'Si, Entiendo'.!`,
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
				eliminar_periferico_item(serial);
			}
		}
	);
};

const eliminar_periferico_item = (serial) => {
	lista_perifericos.map((element, index) => {
		if (element.serial == serial) {
			swal.close();
			lista_perifericos.splice(index, 1);
			listar_perifericos_solicitud();
			return;
		}
	});
};

const ver_periferico = (data) => {
	$(".tipo").val(data.tipo);
	$(".codigo_interno").val(data.codigo_interno);
	$(".serial").val(data.serial);
	$(".marca").val(data.marca);
	$(".modelo").val(data.modelo);
	$(".fecha_ingreso").val(data.fecha_ingreso);
	$(".fecha_garantia").val(data.fecha_garantia);
	$(".valor").val(data.valor);
	$(".descripcion").val(data.descripcion);
	$("#modal_modificar_periferico").modal("show");
};
const modificar_periferico = (serial_) => {
	modificar_perifericos_verificar(serial_);
};

const modificar_perifericos_verificar = (serial_) => {
	let data = new FormData(document.getElementById("form_modificar_periferico"));
	data.append("tipo_periferico", tipo_periferico);
	// data.append('marca_periferico', marca_periferico);
	// data.append('modelo_periferico', modelo_periferico);
	$.ajax({
		url: `${Traer_Server()}index.php/inventario_control/verificar_perifericos`,
		type: "post",
		dataType: "json",
		data,
		cache: false,
		contentType: false,
		processData: false,
	}).done((datos) => {
		const { mensaje, tipo, titulo, data } = datos;
		if (tipo == "sin_session") {
			close();
		} else if (tipo == "success") {
			//eliminar_periferico_item(serial_);

			let sw_serial = false;
			let sw1_codigo = false;
			lista_perifericos.forEach(({ serial, codigo_interno, prefijo }) => {
				if (data.serial == serial) sw_serial = true;
				else if (
					`${data.prefijo}${data.codigo_interno}` ==
					`${prefijo}${codigo_interno}`
				) {
					if (`${data.prefijo}${data.codigo_interno}` == "") sw1_codigo = false;
					else sw1_codigo = true;
				}
			});

			if (sw_serial)
				MensajeConClase("El serial ingresado ya existe.", "info", "Oops.!");
			else if (sw1_codigo)
				MensajeConClase(
					"El codigo interno ingresado ya existe.",
					"info",
					"Oops.!"
				);
			else {
				lista_perifericos.push(data);
				MensajeConClase(mensaje, tipo, titulo);
				$("#form_modificar_periferico").get(0).reset();
				$("#modal_modificar_periferico").modal("hide");
				listar_perifericos_solicitud();
			}
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const buscar_codigo = () => {
	idparametro_activo = 25;
	callbak_activo = (resp) => {
		let { id, nombre } = resp;
		id_codigo_sap = id;
		$("#sap_input").html(nombre);
		$("#Buscar_Codigo").modal("hide");
		$("#txtcodigo_sap").val("");
	};
	$("#Buscar_Codigo").modal();
	buscar_valor_parametro("$$$$++1");
};

const buscar_valor_parametro = (codigo = "", idparametro = "") => {
	const link = `${ruta_}buscar_valor_parametro`;
	consulta_ajax(link, { codigo, idparametro }, (resp) => {
		let { data, mensaje, titulo, tipo } = resp;
		if (tipo != "success") MensajeConClase(mensaje, tipo, titulo);
		$("#tabla_codigos tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td .seleccionar");
		const myTable = $("#tabla_codigos").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ data: "nombre" },
				{ data: "descripcion" },
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_codigos tbody").on("click", "tr", function () {
			$("#tabla_codigos tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$("#tabla_codigos tbody").on("dblclick", "tr", function () {
			const data = myTable.row(this).data();
			callbak_activo(data);
		});

		$("#tabla_codigos tbody").on("click", "tr td .seleccionar", function () {
			const data = myTable.row($(this).parent().parent()).data();
			callbak_activo(data);
		});
	});
};

const listar_recursos_inventario = (id_tipo, container, id_mod = "") => {
	consulta_ajax(`${ruta_}listar_recursos_inventario`, { id_tipo }, (resp) => {
		$(`${container} select[name='tipo']`).html("");
		$(`${container} select[name='tipo']`).html(
			`<option value = ''> Seleccione Tipo Periférico</option>`
		);
		resp.forEach((element) =>
			$(`${container} select[name='tipo']`).append(
				`<option value = '${element.id}'>${element.valor}</option>`
			)
		);
		if (id_mod) $(`${container} select[name='tipo']`).val(id_mod);
	});
};
const asignar_mantenimiento = (id = "") => {
	const data = formDataToJson(
		new FormData(document.getElementById("form_asignar_mantenimiento"))
	);
	// data.articulos = id ? id : articulos;
	data.articulos = acciones_masivas ? articulos : id;

	consulta_ajax(`${ruta_}asignar_mantenimiento`, data, (resp) => {
		const { titulo, mensaje, tipo: tipo_res } = resp;
		if (tipo_res == "success") {
			$("#form_asignar_mantenimiento").get(0).reset();
			$("#modal_asignar_mantenimiento").modal("hide");
			if (!acciones_masivas) {
				Listar_mantenimientos(id_inventario);
				Listar_articulos();
			} else
				Listar_articulos((id, thiss) => seleccionar_articulos_masi(id, thiss));
		}
		MensajeConClase(mensaje, tipo_res, titulo);
	});
};

const limpiar_filtros = () => {
	callbak_activo_acciones = (resp) => {
		MensajeConClase(
			"Seleccione la acción que desea realizar",
			"info",
			"Oops.!"
		);
	};
	acciones_masivas = false;
	estado_buscado = "";
	articulos = [];
	Listar_articulos();
	$("#accion_masiva").get(0).reset();
	$(".mensaje_notificacion").hide("fast");
	$(".acciones_tabla").show("fast");
};

const ver_detalle_mantenimiento = (data) => {
	let { tipo, fecha, estado, usuario, descripcion_man } = data;

	$(".tipo").html(tipo);
	$(".fecha").html(fecha);
	$(".estado").html(estado);
	$(".usuario").html(usuario);
	$(".descripcion").html(descripcion_man);
	$("#modal_detalle_mantenimiento").modal();
};
const ver_detalle_perifericos = (data) => {
	let {
		serial,
		recurso,
		modelo,
		marca,
		fecha_registra,
		persona,
		codigo_interno,
		estado,
	} = data;

	$(".serial").html(serial);
	$(".recurso").html(recurso);
	$(".codigo_interno").html(codigo_interno);
	$(".modelo").html(modelo);
	$(".marca").html(marca);
	$(".fecha_registra").html(fecha_registra);
	$(".persona").html(persona);
	if (estado == 1) $(".estado").html("Asignado");
	else $(".estado").html("Retirado");
	$("#modal_detalle_perifericos").modal();
};

const ver_detalle_responsable_recurso = (data) => {
	let {
		responsable,
		usuario,
		recurso,
		serial,
		codigo_interno,
		fecha_asigna,
		usuario_asigna,
		fecha_retira,
		fecha_elimina,
		usuario_retira,
		lugar,
		ubicacion,
		estado,
	} = data;

	$(".responsable").html(responsable);
	$(".usuario").html(usuario);
	$(".recurso").html(recurso);
	$(".serial").html(serial);
	$(".codigo_interno").html(codigo_interno);
	$(".fecha_asigna").html(fecha_asigna);
	$(".usuario_asigna").html(usuario_asigna);
	$(".lugar").html(lugar);
	$(".ubicacion").html(ubicacion);
	$(".fecha_elimina").html(fecha_elimina);
	$(".usuario_retira").html(usuario_retira);
	estado == 1 ? $(".estado").html("Asignado") : $(".estado").html("Retirado");
	$("#modal_detalle_responsable_recurso").modal();
};

const listar_modificaciones = (id) => {
	$("#tabla_modificaciones_solicitud tbody")
		.off("click", "tr")
		.off("click", "tr .eliminar")
		.off("click", "tr .asignar");
	consulta_ajax(`${ruta_}listar_modificaciones`, { id }, (resp) => {
		let i = 0;
		const myTable = $("#tabla_modificaciones_solicitud").DataTable({
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
					data: "nombre_campo",
				},
				{
					render: function (data, type, full, meta) {
						let { anterior, parametro_anterior, nombre_campo } = full;
						let resp_anterior = anterior;
						if (
							nombre_campo == "Sistema Operativo" ||
							nombre_campo == "Procesador"
						)
							resp_anterior = parametro_anterior;
						return resp_anterior;
					},
				},
				{
					render: function (data, type, full, meta) {
						let { actual, parametro_actual, nombre_campo } = full;
						let resp_actual = actual;
						if (
							nombre_campo == "Sistema Operativo" ||
							nombre_campo == "Procesador"
						)
							resp_actual = parametro_actual;
						return resp_actual;
					},
				},
				{
					data: "fecha",
				},
				{
					data: "usuario_modifica",
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_modificaciones_solicitud tbody").on("click", "tr", function () {
			$("#tabla_modificaciones_solicitud tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_modificaciones_solicitud tbody").on(
			"click",
			"tr td:nth-of-type(1)",
			function () {
				let data = myTable.row($(this).parent()).data();
			}
		);

		$("#tabla_modificaciones_solicitud tbody").on(
			"click",
			"tr .asignar",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
			}
		);
	});
};
const buscar_serial_mensaje = () => {
	// $("#modal_detalle_laboratorios").modal();
	swal(
		{
			title: "Buscar Serial",
			text:
				"Por favor ingrese el serial que desea buscar, tener en cuenta los espacios en blanco.",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#5cb85c",
			confirmButtonText: "Buscar",
			cancelButtonText: "Cancelar",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: "Ingrese Serial",
		},
		function (serial) {
			if (serial === false) return false;
			if (serial === "") {
				swal.showInputError("Debe Ingresar un serial.!");
			} else {
				callbak_activo_acciones = (resp) => {
					MensajeConClase(
						"Seleccione la acción que desea realizar",
						"info",
						"Oops.!"
					);
				};
				id_ubicacion = "";
				estado_buscado = "";
				tipo = serial;
				en_fecha = "";
				acciones_masivas = false;
				articulos = [];
				buscar_serial(serial);
				return false;
			}
		}
	);
};
const buscar_serial = (serial) => {
	consulta_ajax(`${ruta_}buscar_serial`, { serial }, (resp) => {
		let { id_recurso, tipo_modulo: tm, mensaje, tipo } = resp;
		if (tipo == "info") {
			if (tm != tipo_modulo)
				MensajeConClase(
					"El serial ya se encuentra registrado, pero esta asignado en un inventario diferente.",
					"info",
					"Oops.!"
				);
			else {
				$("#dispositivos_modal").modal();
				id_articulo = id_recurso;
				sw_buscado = "serial";
				Listar_articulos();
				swal.close();
			}
		} else {
			$(".mensaje-alert").html(mensaje);
			$("#Guardar_inventario").get(0).reset();
			$("#imagenmuestra").attr("src", "").hide();
			$("#Guardar_inventario input[name='serial']").val(serial);
			$("#myModal").modal();
			cargar_tipo_recursos();
			swal.close();
		}
	});
};

const administrar_modulo = (tipo, parametro = "") => {
	adm_activo = {
		tipo,
		parametro,
		valor_parametro: null,
	};

	if (tipo == "tipo_recurso") listar_tipo_recursos();
	else listar_valores_parametros(parametro);

	let item;
	if (tipo == "marcas") item = "Marca";
	else if (tipo == "modelos") item = "Modelo";
	else if (tipo == "procesador") item = "Procesador";
	else if (tipo == "so") item = "Sistema Operativo";
	else if (tipo == "proveedores") item = "Proveedor";
	else if (tipo == "tipo_recurso") item = "Tipo de Recurso";
	else item = "Item";

	$("#modal_nuevo_valor .modal-title").html(
		`<span class="fa fa-pencil-square-o "></span> Nuevo ${item}`
	);
	$("#ModalModificarParametro .modal-title").html(
		`<span class="fa fa-pencil-square-o "></span> Modificar ${item}`
	);
	$("#nombre_tabla_cu_or").html(`TABLA ${item.toLocaleUpperCase()}`);

	$("#container_admin_valores").css("display", "none");
	$("#div_administrar_permisos").css("display", "none");
	$("#container_admin_valores").fadeIn(1000);
};

const mostrar_personas_permisos = () => {
	$("#container_admin_valores").css("display", "none");
	$("#div_administrar_permisos").fadeIn();
	$("#txt_search_person").val("");
	$("#tabla_personas_permisos")
		.DataTable({
			destroy: true,
			processing: true,
			searching: false,
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		})
		.clear()
		.draw();
};

const listar_personas = (text, callback) =>
	consulta_ajax(`${ruta_}listar_personas`, { text }, (data) => callback(data));

const listar_tipo_recursos = () => {
	consulta_ajax(`${ruta_}cargar_tipo_recursos`, { tipo_modulo }, (data) => {
		$("#tabla_valores_parametros tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr .modelos")
			.off("click", "tr td:nth-of-type(1)");
		const myTable = $("#tabla_valores_parametros").DataTable({
			destroy: true,
			data,
			processing: true,
			columns: [
				{
					render: () =>
						`<span  style="background-color:white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>`,
				},
				{ data: "recurso" },
				{ data: "valorx" },
				{
					render: (data, type, { id }, meta) =>
						`<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default eliminar"></span>`,
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		eventosTablaParametros(myTable);
	});
};

const listar_valores_parametros = (idparametro) => {
	consulta_ajax(
		`${Traer_Server()}index.php/genericas_control/Cargar_valor_Parametros/true/2`,
		{ idparametro },
		({ data }) => {
			$("#tabla_valores_parametros tbody")
				.off("click", "tr")
				.off("dblclick", "tr")
				.off("click", "tr .modelos")
				.off("click", "tr td:nth-of-type(1)");
			const myTable = $("#tabla_valores_parametros").DataTable({
				destroy: true,
				data,
				processing: true,
				columns: [
					{
						render: () =>
							`<span  style="background-color:white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>`,
					},
					{ data: "valor" },
					{ data: "valorx" },
					{
						render: (data, type, { op }, meta) =>
							idparametro == 4
								? `${op} <span title="Permisos" style="color: #777;"  data-toggle="popover" data-trigger="hover" class="fa fa-cog pointer btn btn-default modelos"></span>`
								: op,
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: [],
			});
			eventosTablaParametros(myTable);
		}
	);
};

const eventosTablaParametros = (myTable) => {
	$("#tabla_valores_parametros tbody").on("click", "tr", function () {
		const data = myTable.row(this).data();
		$("#tabla_valores_parametros tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		adm_activo.valor_parametro = data.id;
	});

	$("#tabla_valores_parametros tbody").on("dblclick", "tr", function () {
		const data = myTable.row(this).data();
		ver_detalle_parametro(data);
	});

	$("#tabla_valores_parametros tbody").on(
		"click",
		"tr td:nth-of-type(1)",
		function () {
			const data = myTable.row($(this).parent()).data();
			ver_detalle_parametro(data);
		}
	);

	$("#tabla_valores_parametros tbody").on("click", "tr .modelos", function () {
		const { id } = myTable.row($(this).parent().parent()).data();
		get_modelos_marca(id);
	});

	$("#tabla_valores_parametros tbody").on("click", "tr .eliminar", function () {
		const { id, recurso } = myTable.row($(this).parent().parent()).data();
		confirmarAccion(
			() => {
				eliminarTipoRecurso(id, recurso);
			},
			`¿Eliminar ${recurso}?`,
			`¿Desea eliminar este tipo recurso?`
		);
	});
};

const eliminarTipoRecurso = (id, recurso) => {
	consulta_ajax(
		`${ruta_}eliminar_tipo_recurso`,
		{
			id,
			tipo_modulo,
			recurso,
		},
		({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === "success") {
				listar_tipo_recursos();
			}
		}
	);
};

const get_modelos_marca = (marca) => {
	$("#tabla_modelos tbody")
		.off("click", "tr .asignar")
		.off("click", "tr .desasignar");
	consulta_ajax(`${ruta_}get_modelos_marca`, { marca }, (modelos) => {
		$("#tabla_modelos tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr .modelos")
			.off("click", "tr td:nth-of-type(1)");
		let num = 1;
		const myTable = $("#tabla_modelos").DataTable({
			destroy: true,
			data: modelos,
			processing: true,
			columns: [
				{ render: (data, type, permiso, meta) => num++ },
				{ data: "nombre" },
				{
					render: (data, type, { permiso }, meta) =>
						permiso
							? `<span title="Eliminar" style="color: #d9534f;" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default desasignar"></span>`
							: `<span title="Eliminar" style="color: #5cb85c;" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o pointer btn btn-default asignar"></span>`,
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_modelos tbody").on("click", "tr", function () {
			const data = myTable.row(this).data();
			$("#tabla_modelos tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_modelos tbody").on("click", "tr .asignar", function () {
			const data = myTable.row($(this).parent().parent()).data();
			gestionar_modelo(data);
		});

		$("#tabla_modelos tbody").on("click", "tr .desasignar", function () {
			let data = myTable.row($(this).parent().parent()).data();
			gestionar_modelo(data);
		});

		$("#modal_gestion_modelos").modal();
	});
};

const gestionar_modelo = (data) => {
	const aux = data.permiso ? "Desasignar" : "Asignar";
	swal(
		{
			title: `¿ ${aux} Modelo ?`,
			text: `¿ Está seguro que desea ${aux.toLowerCase()} este modelo ?`,
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si!",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		(isConfirm) => {
			if (isConfirm) {
				data.marca = $("#tabla_valores_parametros")
					.DataTable()
					.row(".warning")
					.data().id;
				consulta_ajax(
					`${ruta_}gestionar_modelo`,
					data,
					({ mensaje, tipo, titulo }) => {
						if (tipo === "success") {
							const { id } = $("#tabla_valores_parametros")
								.DataTable()
								.row(".warning")
								.data();
							get_modelos_marca(id);
							swal.close();
						} else MensajeConClase(mensaje, tipo, titulo);
					}
				);
			}
		}
	);
};

const modificar_valor_parametro = () => {
	let url = `${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`;
	let data = new FormData(
		document.getElementById("form_modificar_valor_parametro")
	);
	data.append("idparametro", adm_activo.valor_parametro);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			$("#form_modificar_valor_parametro").get(0).reset();
			$("#ModalModificarParametro").modal("hide");
			MensajeConClase("", "success", "Datos Modificados!");
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == 2) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 3) {
			MensajeConClase(
				"El Nombre que desea guardar ya esta en el sistema",
				"info",
				"Oops..."
			);
		} else if (resp == -1302) {
			MensajeConClase(
				"No tiene Permisos Para Realizar Esta Opereacion",
				"error",
				"Oops..."
			);
		} else {
			MensajeConClase(
				"Error al Modificar la información, contacte con el administrador.",
				"error",
				"Oops..."
			);
		}
	});
};

const confirmar_eliminar_parametro = (id, estado) => {
	swal(
		{
			title: "Estas Seguro .. ?",
			text:
				"Tener en cuenta que al Eliminar este valor no estara disponible en las solicitudes de presupuesto, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
				eliminar_parametro(id, estado);
			}
		}
	);
};

function eliminar_parametro(idparametro, estado) {
	let url = `${Traer_Server()}index.php/genericas_control/cambio_estado_parametro`;
	let data = {
		idparametro,
		estado,
	};
	consulta_ajax(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			swal.close();
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == -1302) {
			MensajeConClase(
				"No tiene Permisos Para Realizar Esta Opereacion",
				"error",
				"Oops..."
			);
		} else {
			MensajeConClase(
				"Error al eliminar la información, contacte con el administrador.",
				"error",
				"Oops..."
			);
		}
	});
}

const mostrar_parametro_modificar = async (buscar) => {
	let data = await buscar_parametro_id(buscar);
	let { valory, valor, valorx, id, idparametro, relacion } = data[0];
	if (idparametro == 25) {
		orden_centro.id = valory;
		orden_centro.nombre = relacion;
		$(".txt_nombre_centro").val(relacion);
	}

	$("#txtValor_modificar").val(valor);
	$("#txtDescripcion_modificar").val(valorx);
	$("#ModalModificarParametro").modal();
};

const guardar_valor_parametro = () => {
	let url = `${Traer_Server()}index.php/genericas_control/guardar_valor_Parametro`;
	let data = new FormData(
		document.getElementById("form_guardar_valor_parametro")
	);
	data.append("idparametro", adm_activo.parametro);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 2) {
			$("#form_guardar_valor_parametro").get(0).reset();
			MensajeConClase("", "success", "Datos Guardados!");
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == 3) {
			MensajeConClase(
				"El Nombre que desea guardar ya esta en el sistema",
				"info",
				"Oops..."
			);
		} else if (resp == -1302) {
			MensajeConClase(
				"No tiene Permisos Para Realizar Esta Opereacion",
				"error",
				"Oops..."
			);
		} else {
			MensajeConClase(
				"Error al Guardar la información, contacte con el administrador.",
				"error",
				"Oops..."
			);
		}
	});
};

const agregar_responsable = (id, retirar = "") => {
	articulos = acciones_masivas ? articulos : id_inventario;
	consulta_ajax(
		`${ruta_}agregar_responsable`,
		{ articulos, id, retirar: retirar ? 1 : 0 },
		({ mensaje, tipo, titulo }) => {
			if (tipo === "success") {
				if (!acciones_masivas) {
					Listar_responsables(id_inventario);
					Listar_articulos();
				} else
					Listar_articulos((id, thiss) =>
						seleccionar_articulos_masi(id, thiss)
					);

				$("#modal_buscar_persona").modal("hide");
				$("#modal_confirmacion_responsable").modal("hide");
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
};

const seleccionar_responsable_new = ({ id }) => {
	callbak_activo_alt = (retirar) => agregar_responsable(id, retirar);
	$("#modal_confirmacion_responsable").modal();
};

const terminar_mantenimiento_masivo = (recursos) => {
	consulta_ajax(
		`${ruta_}terminar_mantenimiento_masivo`,
		{ recursos },
		({ mensaje, tipo, titulo }) => {
			if (tipo === "success") {
				articulos = [];
				Listar_articulos();
				swal.close();
			} else MensajeConClase(mensaje, tipo, titulo);
		}
	);
};

const gestionar_permiso_persona = (persona, permiso, sw, accion = "") => {
	consulta_ajax(
		`${ruta_}gestionar_permiso_persona`,
		{
			persona,
			permiso,
			sw,
			accion,
		},
		({ mensaje, tipo, titulo }) => MensajeConClase(mensaje, tipo, titulo)
	);
};

const get_permisos_persona = (persona) => {
	consulta_ajax(
		`${ruta_}get_permisos_persona`,
		{
			persona,
		},
		(data) => {
			if (data) {
				const { agregar, modificar, gestionar } = data;
				$("#per_agregar").prop("checked", agregar == 1 ? true : false);
				$("#per_modificar").prop("checked", modificar == 1 ? true : false);
				$("#per_gestionar").prop("checked", gestionar == 1 ? true : false);
			} else
				$(".funkyradio.permisos input[type='checkbox']").prop("checked", false);
		}
	);
};

const confirDarBajaRecurso = () => {
	swal(
		{
			title: "Estas Seguro ?",
			text:
				"Tener en cuenta que al dar de BAJA  el recurso se Desactivara y No se tendra en cuenta para sus diferentes usos..!",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Terminar!",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: "Ingrese Motivo",
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "") {
				swal.showInputError("Debe Ingresar el motivo.!");
			} else {
				dar_baja(id_inventario, mensaje);
				return false;
			}
		}
	);
};

const confirmarAccion = (
	callback,
	title = `¿ Estas Seguro ?`,
	text = `El periférico seleccionado será asignado al recurso, si desea continuar presione la opción de 'Si, Entiendo!'`
) => {
	swal(
		{
			title,
			text,
			type: "warning",
			html: true,
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
				callback();
			}
		}
	);
};

const seleccionar_articulos_masi = (id, thiss) => {
	let existe = articulos.indexOf(id);
	let id_row = `sele-${id}`;
	$(thiss).attr("id", id_row);
	if (existe !== -1) {
		$(`#${id_row}`).removeClass("warning");
		articulos.splice(existe, 1);
	} else {
		articulos.push(id);
		$(thiss).attr("class", "warning");
	}
};
const seleccionar_articulos_indi = (id, thiss) => {
	$("#tbl_articulos tbody tr").removeClass("warning");
	$(thiss).attr("class", "warning");
};

const seleccionar_todo_articulos = (recursos) => {
	articulos = [];
	recursos.map(({ id }) => articulos.push(id));
	if (recursos) $("#tbl_articulos tbody tr").attr("class", "warning");
};

const listar_tipos_recursos_asignados = (recurso) => {
	consulta_ajax(
		`${ruta_}listar_tipos_recursos_asignados`,
		{ recurso },
		(data) => {
			pintar_datos_combo(data, "#tipo_agregar", "Seleccione Tipo de Recurso");
		}
	);
};
const guardar_datos_tecnicos = (form) => {
	const formData = new FormData(form);
	formData.append("id", id_inventario);
	enviar_formulario(
		`${ruta_}guardar_datos_tecnicos`,
		formData,
		({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			$("#modal_datos_tecnicos").modal("hide");
		}
	);
};

const cargar_requerimientos_tecnicos = () => {
	const quitar =
		'<span class="btn btn-default quitar" title="Quitar Requerimiento"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span>';
	const agregar =
		'<span class="btn btn-default agregar" title="Agregar Requerimiento"><span class="fa fa-toggle-off"></span></span> ';
	consulta_ajax(
		`${ruta_}cargar_requerimientos_tecnicos`,
		{ id: id_inventario },
		(data) => {
			$("#tabla_requerimientos tbody")
				.off("click", "tr")
				.off("click", "tr span.quitar")
				.off("click", "tr span.agregar");
			const myTable = $("#tabla_requerimientos").DataTable({
				destroy: true,
				processing: true,
				data,
				columns: [
					{ data: "requerimiento" },
					{
						render: (data, type, { asignado }, meta) =>
							asignado ? quitar : agregar,
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_requerimientos tbody").on("click", "tr", function () {
				$("#tabla_requerimientos tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_requerimientos tbody").on(
				"click",
				"tr span.quitar",
				function () {
					const { id, asignado } = myTable.row($(this).parent()).data();
					gestionar_requerimiento(id, asignado);
				}
			);

			$("#tabla_requerimientos tbody").on(
				"click",
				"tr span.agregar",
				function () {
					const { id, asignado } = myTable.row($(this).parent()).data();
					gestionar_requerimiento(id, asignado);
				}
			);
		}
	);
};

const gestionar_requerimiento = (requerimiento, asignado) => {
	consulta_ajax(
		`${ruta_}gestionar_requerimiento`,
		{
			requerimiento,
			asignado,
			id: id_inventario,
		},
		({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			cargar_requerimientos_tecnicos();
		}
	);
};

const adjuntar_archivo = () => {
	let errores = 0;
	let tipo_cargue = 0;
	Dropzone.options.Subir = {
		url: `${ruta_}cargar_documento_articulo`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
		method: "post", //por defecto es post se puede poner get, put, etc.....
		withCredentials: false,
		parallelUploads: 20, //Cuanto archivos subir al mismo tiempo
		uploadMultiple: false,
		maxFilesize: 1000, //Maximo Tamaño del archivo expresado en mg
		paramName: "file", //Nombre con el que se envia el archivo a nivel de parametro
		createImageThumbnails: true,
		maxThumbnailFilesize: 1000, //Limite para generar imagenes (Previsualizacion)
		thumbnailWidth: 154, //Medida de largo de la Previsualizacion
		thumbnailHeight: 154, //Medida alto Previsualizacion
		filesizeBase: 1000,
		maxFiles: 20, //si no es nulo, define cuantos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
		params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
		clickable: true,
		ignoreHiddenFiles: true,
		acceptedFiles:
			"image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv,.gz,.gzip,.rar,.zip", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
		acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
		autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas
		error: (response) => {
			if (!response.xhr) {
				MensajeConClase(
					"Solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
					"info",
					"Oops!"
				);
			} else {
				errores.push(response.xhr.responseText);
				if (num_archivos_cargados == num_archivos) {
					tipo_cargue == 1
						? MensajeConClase(
								"La solicitud fue guardada con exito, pero ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
								"info",
								"Oops!"
						  )
						: MensajeConClase(
								"Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
								"info",
								"Oops!"
						  );
				}
			}
		},
		success: (file, response) => {
			let errorlist = "No ingresa";
			if (errores.length > 0) {
				errorlist = "";
				errores.forEach((error) => (errorlist += `${error},`));
				tipo_cargue == 1
					? MensajeConClase(
							"La solicitud fue guardada con exito, pero algunos Archivos No fueron cargados:\n\n" +
								errorlist +
								"\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
							"info",
							"Oops!"
					  )
					: MensajeConClase(
							"Algunos Archivos No fueron cargados:\n\n" +
								errorlist +
								"\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
							"info",
							"Oops!"
					  );
			} else {
				tipo_cargue == 1
					? MensajeConClase(
							"La solicitud fue Guardada con exito y Todos Los archivos fueron cargados.!",
							"success",
							"Proceso Exitoso!"
					  )
					: MensajeConClase(
							"Todos Los archivos fueron cargados.!",
							"success",
							"Proceso Exitoso!"
					  );
			}
		},

		init: function () {
			num_archivos = 0;
			myDropzone = this;
			this.on("addedfile", (file) => num_archivos++);
			this.on("removedfile", (file) => num_archivos--);
			this.on("sending", (file, xhr, formData) =>
				formData.append("tipo", tipo_documento_id)
			);
			myDropzone.on("complete", (file) => {
				myDropzone.removeFile(file);
				num_archivos_cargados++;
			});
		},
		autoQueue: true,
		addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacción.
		previewsContainer: null, //define donde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
		capture: null,
		dictDefaultMessage: "Arrastra los archivos aqui para subirlos",
		dictFallbackMessage:
			"Su navegador no soporta arrastrar y soltar para subir archivos.",
		dictFallbackText:
			"Por favor utilize el formuario de reserva de abajo como en los viejos timepos.",
		dictFileTooBig:
			"La imagen revasa el tamaño permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.",
		dictInvalidFileType: "No se puede subir este tipo de archivos.",
		dictResponseError: "Server responded with {{statusCode}} code.",
		dictCancelUpload: "Cancel subida",
		dictCancelUploadConfirmation: "¿Seguro que desea cancelar esta subida?",
		dictRemoveFile: "Eliminar archivo",
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: "Se ha excedido el numero de archivos permitidos.",
	};
};

const cargar_documentos_disponibles = (id) => {
	consulta_ajax(`${ruta_}cargar_documentos_disponibles`, { id }, (data) => {
		$("#tabla_requerimientos tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr span.adjuntar");
		const myTable = $("#tabla_documentos").DataTable({
			destroy: true,
			processing: true,
			data,
			columns: [
				{
					defaultContent:
						"<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >ver</span></span>",
				},
				{ data: "documento" },
				{
					render: (data, type, { adjuntado }, meta) =>
						adjuntado
							? "<span title='Adjuntar Documento' class='btn btn-default adjuntar'><span class='fa fa-folder-open red'></span></span>"
							: "<span title='Adjuntar Documento' class='btn btn-default adjuntar'><span class='fa fa-folder-open red'></span></span>",
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_documentos tbody").on("dblclick", "tr", function () {
			const data = myTable.row(this).data();
			get_documentos(id_inventario, data.id);
		});

		$("#tabla_documentos tbody").on(
			"click",
			"tr td:nth-of-type(1)",
			function () {
				const data = myTable.row($(this).parent()).data();
				get_documentos(id_inventario, data.id);
			}
		);

		$("#tabla_documentos tbody").on("click", "tr", function () {
			$("#tabla_documentos tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_documentos tbody").on("click", "tr span.adjuntar", function () {
			const { id } = myTable.row($(this).parent().parent()).data();
			tipo_documento_id = id;
			$("#modal_adjuntar_documento").modal();
			evento_documento();
		});
	});
};

const evento_documento = () => {
	$("#modal_adjuntar_documento #footermodal").html(
		'<button type="button" class="btn btn-danger active btnAgregar" id="enviar_adjuntos"><span class="glyphicon glyphicon-ok"></span> Adjuntar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>'
	);
	$("#enviar_adjuntos").click(function () {
		if (num_archivos != 0) {
			$("#id_archivo").val(id_inventario);
			myDropzone.processQueue();
			$("#modal_adjuntar_documento").modal("hide");
		} else MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
	});
};

const agregar_mantenimiento = (articulo) => {
	const formdata = new FormData(
		document.getElementById("form_asignar_mantenimiento")
	);
	formdata.append("id", articulo);
	enviar_formulario(
		`${ruta_}agregar_mantenimiento`,
		formdata,
		({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === "success") Listar_mantenimientos(articulo);
		}
	);
};

const PreviewImage = () => {
	const image = document.getElementById("imagenmuestra");
	const oFReader = new FileReader();
	oFReader.readAsDataURL(document.getElementById("image").files[0]);

	oFReader.onload = (oFREvent) => {
		image.src = oFREvent.target.result;
		image.style.display = "block";
	};
};

const get_documentos = (inventario_id, tipo_id) => {
	consulta_ajax(
		`${ruta_}get_documentos`,
		{
			inventario_id,
			tipo_id,
			tipo_modulo,
		},
		(data) => {
			$("#modal_documentos_tipo").modal();
			$("#modal_documentos_tipo tbody")
				.off("click", "tr .ver")
				.off("click", "tr .eliminar");
			const myTable = $("#tabla_documentos_tipo").DataTable({
				destroy: true,
				processing: true,
				data,
				columns: [
					{
						render: (data, type, { ruta_documento, estado }, meta) =>
							estado == 1
								? `<a target="_blank" class="form-control" href="${ruta_documentos}documentos/${ruta_documento}" style="width: 100%;"> Ver</a>`
								: `<button class="form-control" disabled style="width: 100%;"> Ver</button>`,
					},
					{ data: "nombre_documento" },
					{
						render: function (data, type, { acciones }, meta) {
							if (tipo_modulo === "Inv_Lab" && acciones) {
								return acciones;
							} else return [];
						},
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#modal_documentos_tipo tbody").on("click", "tr .ver", function () {
				let data = myTable.row($(this).parent().parent()).data();
				$(".h3__lab_places").html("Información del documento");
				$(".mod_usuario_asigna").html("Documento cargado por:");
				$(".mod_usuario_retira").html("Documento retirado por:");
				$(".mod_fecha_asigna").html("Fecha de cargue:");
				$(".mod_lugar").html("Nombre del documento:");
				$(".mod_ubicacion").css("display", "none");
				$(".mod_ubicacion");
				$("#modal_lugares").modal();
				info__documento(data);
			});

			$("#modal_documentos_tipo tbody").on(
				"click",
				"tr .eliminar",
				function () {
					let data = myTable.row($(this).parent().parent()).data();
					swal(
						{
							title: "¿Eliminar Documento?",
							text: `¿Está seguro que desea eliminar este documento? Esta acción no se puede reversar.`,
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
								inventario_id = data.id;
								tipo_id = data.tipo_id;
								eliminar__documento(inventario_id, tipo_id);
							}
						}
					);
				}
			);
		}
	);
};

const agregar_mantenimiento_lab = () => {
	let formData = new FormData(
		document.getElementById("form_agregar_mantenimiento")
	);
	const form = formDataToJson(formData);
	form.id = id_inventario;
	consulta_ajax(
		`${ruta_}agregar_mantenimiento_lab`,
		form,
		({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === "success") {
				$("#modal_agregar_mantenimiento").modal("hide");
				$("#form_agregar_mantenimiento").get(0).reset();
				get_mantenimientos_lab(id_inventario);
			}
		}
	);
};

const get_mantenimientos_lab = (id) => {
	consulta_ajax(`${ruta_}get_mantenimientos_lab`, { id }, (data) => {
		$("#tabla_mantenimientos_lab tbody")
			.off("dblclick", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr td .reparacion");
		const myTable = $("#tabla_mantenimientos_lab").DataTable({
			destroy: true,
			processing: true,
			data,
			columns: [
				{
					defaultContent:
						"<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >Ver</span></span>",
				},
				{ data: "tipo" },
				{ data: "persona_modifica" },
				{ data: "fecha_modifica" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_mantenimientos_lab tbody").on("dblclick", "tr", function () {
			const data = myTable.row(this).data();
			detalle_mantenimiento_lab(data);
		});

		$("#tabla_mantenimientos_lab tbody").on(
			"click",
			"tr td:nth-of-type(1)",
			function () {
				const data = myTable.row($(this).parent()).data();
				detalle_mantenimiento_lab(data);
			}
		);
	});
};

const detalle_mantenimiento_lab = ({
	fecha_modifica,
	fecha_registra,
	periodicidad,
	persona_modifica,
	persona_registra,
	tipo,
	ultima_fecha,
	descripcion,
}) => {
	$("#modal_detalle_mantenimiento_lab").modal();
	$("#tbl_detalle_mantenimiento .man_det_proceso").html(tipo);
	$("#tbl_detalle_mantenimiento .man_det_periodicidad").html(periodicidad);
	$("#tbl_detalle_mantenimiento .man_det_modifica").html(persona_modifica);
	$("#tbl_detalle_mantenimiento .man_det_fecha_modifica").html(ultima_fecha);
	$("#tbl_detalle_mantenimiento .man_det_registro").html(persona_registra);
	$("#tbl_detalle_mantenimiento .man_det_fecha_registra").html(fecha_registra);
	if (descripcion) {
		$("#row_descripcion_mantenimiento").show("fast");
		$("#tbl_detalle_mantenimiento .man_det_descripcion").html(descripcion);
	} else {
		$("#row_descripcion_mantenimiento").css("display", "none");
		$("#tbl_detalle_mantenimiento .man_det_descripcion").html("");
	}
};

const guardar_tipo_recurso = () => {
	const data = new FormData(
		document.getElementById("form_guardar_valor_parametro")
	);
	data.append("idparametro", adm_activo.parametro);
	data.append("tipo_modulo", tipo_modulo);

	enviar_formulario(
		`${ruta_}guardar_tipo_recurso`,
		data,
		({ titulo, tipo, mensaje }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === "success") {
				$("#form_guardar_valor_parametro").get(0).reset();
				$("#modal_nuevo_valor").modal("hide");
				listar_tipo_recursos();
			}
		}
	);
};
const buscar_proveedor = (dato, callback_activo) => {
	tabla = "#tabla_proveedor_buscar";
	consulta_ajax(`${ruta_}buscar_proveedor`, { dato }, (resp) => {
		$(`${tabla} tbody`)
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr td .proveedor");
		let i = 0;
		const myTable = $(tabla).DataTable({
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
						'<span style="color: #39B23B;" title="Seleccionar proveedor" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default proveedor" ></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
		$(`${tabla} tbody`).on("click", "tr", function () {
			$(`${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`${tabla} tbody`).on("click", "tr td .proveedor", function () {
			let data = myTable.row($(this).parent()).data();
			callback_activo(data);
		});
	});
};

const mostrar_notificaciones_inventario = async (tipo = 1) => {
	let modal =
		tipo == 1 ? "#modal_notificaciones_inventario" : "#modal_notificaciones";
	await pintar_notificaciones_comentarios_general(
		'cc.tipo = "proyectos"',
		["Per_Admin", "Per_Adm_index"],
		"#panel_notificaciones_generales",
		".n_notificaciones",
		modal,
		"Notificaciones Proyectos",
		abrir_proyecto
	);
};

const mostrar_notificaciones = async () => {
	await mostrar_notificaciones_inventario();
	consulta_ajax(
		`${ruta_}mostrar_notificaciones_mtto`,
		{ tipo_modulo },
		(datos) => {
			pintar_notificaciones_proyectos(
				{
					container: "#panel_notificaciones",
					titulo: "Equipos proximos a vencer mantenimiento",
				},
				datos
			);
			if (datos.length > 0) $("#modal_notificaciones_inventario").modal("show");
			consulta_ajax(
				`${ruta_}mostrar_notificaciones_garantia`,
				{ tipo_modulo },
				(datos) => {
					pintar_notificaciones_garantia(
						{
							container: "#panel_notificaciones_inventario",
							titulo: "Equipos proximos a vencer garantía",
						},
						datos
					);
					if (datos.length > 0)
						$("#modal_notificaciones_investigacion").modal("show");
					consulta_ajax(
						`${ruta_}mostrar_notificaciones_investigacion`,
						{ tipo_modulo },
						(datos) => {
							pintar_notificaciones_investigacion(
								{
									container: "#panel_notificaciones_investigacion",
									titulo: "Proyectos de investigación proximos a finalizar",
								},
								datos
							);
							if (datos.length > 0)
								$("#modal_notificaciones_inventario").modal("show");
							numero_notificaciones();
						}
					);
				}
			);
		}
	);
};
const abrir_proyecto = async (id_s) => {
	let data = await listar_proyecto_id(id_s);
	let { id_comite, id_estado_comite } = data;
	let id = id_comite;
	mostrar_detalle_comite({ id, id_estado_comite });
	ver_detalle_proyecto(data);
};
const pintar_notificaciones_proyectos = (data, resp) => {
	let { container, titulo } = data;
	let resultado = ``;
	resp.map((i) => {
		resultado = `
		  <a class="list-group-item">
			<span style="font-weight: 500;" class="badge pointer" onclick='ver_detalles("mantenimiento")'><span class="fa fa-eye"></span> Detalle</span>
			<span>
			  <h4 class="list-group-item-heading">Equipos proximos a vencer mantenimiento</h4>
			</span>
		  </a>
		`;
	});
	$(container).html(
		`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`
	);
};
const pintar_notificaciones_garantia = (data, resp) => {
	let { container, titulo } = data;
	let resultado = ``;
	resp.map((i) => {
		resultado = `
		  <a class="list-group-item">
			<span style="font-weight: 500;" class="badge pointer" onclick='ver_detalles("garantia")'><span class="fa fa-eye"></span> Detalle</span>
			<span>
			  <h4 class="list-group-item-heading">Equipos proximos a vencer garantía</h4>
			</span>
		  </a>
		`;
	});
	$(container).html(
		`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`
	);
};
const pintar_notificaciones_investigacion = (data, resp) => {
	let { container, titulo } = data;
	let resultado = ``;
	resp.map((i) => {
		resultado = `
		  <a class="list-group-item">
			<span style="font-weight: 500;" class="badge pointer" onclick='ver_detalles("proyectos")'><span class="fa fa-eye"></span> Detalle</span>
			<span>
			  <h4 class="list-group-item-heading">Proyectos de investigación proximos a finalizar</h4>
			</span>
		  </a>
		`;
	});
	$(container).html(
		`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`
	);
};
const ver_detalles = (id) => {
	en_fecha = id;
	id_ubicacion = "";
	tipo = "";
	sw_buscado = "";
	$("#limpiar_filtros").trigger("click");
	$("#dispositivos_modal").modal();
};

const numero_notificaciones = () => {
	let temp = 0;
	$("#notificaciones_body li.list-group-item .badge").each(function () {
		temp += parseInt($(this).html());
	});
	$("#inventario_notificaciones").html(temp);
};

const consulta = (id, ruta) => {
	return new Promise((resolve) => {
		let url = `${ruta_}` + ruta;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const info__documento = async (data) => {
	$(".estado").html("");
	$(".usuario_retira").html("");
	$(".usuario_asigna").html("");
	$(".fecha_retira").html("");
	$(".fecha_asigna").html("");
	$(".lugar").html("");
	let {
		estado,
		fecha_adjunta,
		fecha_elimina,
		nombre_documento,
		usuario_adjunta,
		usuario_elimina,
	} = data;

	let usuario_retira = await consulta(usuario_elimina, "buscar__nombre");
	let usuario_asigna = await consulta(usuario_adjunta, "buscar__nombre");

	$(".estado").html(
		estado == 1 ? "Registrado" : estado == 0 ? "Eliminado" : ""
	);
	$(".usuario_retira").html(usuario_elimina ? usuario_retira[0].persona : []);
	$(".usuario_asigna").html(usuario_adjunta ? usuario_asigna[0].persona : []);
	$(".fecha_retira").html(fecha_elimina);
	$(".fecha_asigna").html(fecha_adjunta);
	$(".lugar").html(nombre_documento);
};

const eliminar__documento = (inventario_id, tipo_id) => {
	consulta_ajax(
		`${ruta_}eliminar__documenento`,
		{ inventario_id, tipo_id },
		(resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				$("#modal_documentos_tipo").modal("hide");
				swal.close();
				//get_documentos(id_document, tipo_id);
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
};

const buscar__datos__tecnicos = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta_}buscar__datos__tecnicos`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const mostrar__datos__tecnicos = async (id) => {
	let {
		tecnologia,
		fase,
		vida_util,
		peso,
		potencia,
		unidades_id,
		voltaje,
		estado_recurso,
	} = await buscar__datos__tecnicos(id);
	$("#form_datos_tecnicos select[name='tecnologia']").val(tecnologia);
	$("#form_datos_tecnicos select[name='unidades']").val(unidades_id);
	$("#form_datos_tecnicos select[name='estado_activo']").val(estado_recurso);
	$("#form_datos_tecnicos select[name='fase']").val(fase);
	$("#form_datos_tecnicos input[name='vida_util']").val(vida_util);
	$("#form_datos_tecnicos input[name='peso']").val(peso);
	$("#form_datos_tecnicos input[name='voltaje']").val(voltaje);
	$("#form_datos_tecnicos input[name='potencia']").val(potencia);
};