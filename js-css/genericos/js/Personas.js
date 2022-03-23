// DECLARACION DE VARIABLES
const ruta = `${Traer_Server()}index.php/personas_control/`;
//esta variable traduce el idioma de la tabla

var idioma = {
    sProcessing: "Procesando...",
    sLengthMenu: "Mostrar _MENU_ registros",
    sZeroRecords: "No se encontraron resultados",
    sEmptyTable: "Ningún dato disponible en esta tabla",
    sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
    sInfoPostFix: "",
    sSearch: "Buscar:",
    sUrl: "",
    sInfoThousands: ",",
    sLoadingRecords: "Ningún dato disponible en esta tabla...",
    oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
    },
    oAria: {
        sSortAscending: ": Activar para ordenar la columna de manera ascendente",
        sSortDescending: ": Activar para ordenar la columna de manera descendente",
    },
};
let idpersona = 0; //en esta variable guardo el id de la persona seleccioanda de la tabla
let persona = Array();
let tieneperfil = true; // en esta variable valido si una persona tiene o no perfil asignado
var server = "localhost"; // variable que me indica a que servidor se conectara la aplicacion por defecto es local
let dato_busqueda = "";
let identificacion = null;
//mis variables
let codigo_cargo_sap = null;
let valor_tipo_perfil = null;
let pSesion = false;
let excelFile;
let str;
//Se inicializan los eventos  cuando cargue la pagina
$(document).ready(function() {
    server = Traer_Server();

    /* Setup del select picker */ //
    $("#asignar_perfiles .selectpicker").selectpicker({
        liveSearchPlaceholder: "Filtre sus resultados...",
    });
    $("#cbxasignar_perfiles .selectpicker").selectpicker({
        liveSearchPlaceholder: "Filtre sus resultados...",
    });

    sesionActiva();

    $("#btn_buscar_persona").click(() => {
        let datos = $("#txt_buscar_persona").val().trim();
        datos.length == 0 ?
            MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") :
            pSesion == true ?
            listar_Personas(datos, 0) :
            listar_Personas_noAdmin(datos, 0);
    });

    $("#txt_buscar_persona").keypress((e) => {
        if (e.which == 13) {
            let datos = $("#txt_buscar_persona").val().trim();
            datos.length == 0 ?
                MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") :
                pSesion == true ?
                listar_Personas(datos, 0) :
                listar_Personas_noAdmin(datos, 0);
        }
    });

    $("#departamento_sele_guardar").change(function() {
        var valory = $(this).val().trim();
        Listar_cargos_departamento_combo(
            ".cbxcargos",
            "Seleccione Cargo",
            valory,
            0
        );
    });

    $("#cbxtipopersona").change(function() {
        let tipo = $(this).val().trim();
        if (tipo == "PerInt") {
            $(".datos_internos").show("slow");
            $(".usuario input").attr("required", "true");
            $(".datos_internos select").attr("required", "true");
        } else {
            $(".datos_internos").hide("slow");
            $(".usuario input").removeAttr("required");
            $(".datos_internos select").removeAttr("required");
        }
    });
    $("#tipo_persona_id_modifica").change(function() {
        let tipo = $(this).val().trim();
        if (tipo == "PerInt") {
            $(".datos_internos_modi").show("slow");
            $(".usuario input").attr("required", "true");
            $(".datos_internos_modi select").attr("required", "true");
        } else {
            $(".datos_internos_modi").hide("slow");
            $(".usuario input").removeAttr("required");
            $(".datos_internos_modi select").removeAttr("required");
        }
    });

    $("#cbxdepartamento_modifica").change(function() {
        var valory = $(this).val().trim();
        Listar_cargos_departamento_combo(
            "#cbxcargos_modifica",
            "Seleccione Cargo",
            valory,
            0
        );
    });

    $("#Recargar").click(() => location.reload());

    $("#btn_asignar_perfil_user").click(() => {
        if (idpersona == 0) {
            MensajeConClase(
                "Antes de continuar debe seleccionar la persona a la cual desea Asignar el Perfil..!",
                "info",
                "Oops..."
            );
        } else if (tieneperfil) {
            swal({
                    title: "No es lo que esperabas ?",
                    text: "La persona Seleccionada ya tiene un Perfil Seleccionado, Si desea Cambiar El perfil debe Ingresar al Panel de Modificacion.!! ",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#D9534F",
                    confirmButtonText: "Si, Ingresar!",
                    cancelButtonText: "No, cancelar!",
                    allowOutsideClick: true,
                    closeOnConfirm: true,
                    closeOnCancel: true,
                },
                (isConfirm) => {
                    if (isConfirm) {
                        obtener_datos_persona(idpersona);
                        $("#Modificar_persona").modal("show");
                    }
                }
            );
        } else {
            $("#Asignar_Perfil_modal").modal("show");
        }
    });

    $("#form-ingresar-persona").submit(() => {
        registrarPersona();
        return false;
    });

    $("#Asignar_Perfil_usuario").submit(() => {
        Asignar_Perfil();
        return false;
    });

    $("#form-modificar-persona").submit(() => {
        Modificar_persona();
        return false;
    });

    $("#btnmodificar_persona").click(() => {
        if (idpersona == 0) {
            MensajeConClase(
                "Antes de continuar debe seleccionar la persona a Modificar..!",
                "info",
                "Oops..."
            );
        } else {
            obtener_datos_persona(idpersona);
            $("#Modificar_persona").modal();
        }
    });

    $("#btnadministrar_perfiles").click(() => {
        if (idpersona == 0) {
            MensajeConClase(
                "Antes de continuar debe seleccionar la persona..!",
                "info",
                "Oops..."
            );
        } else {
            getPerfilesPersona(idpersona);
            cargar_perfiles_faltantes(idpersona);
            $("#Perfiles_modal").modal();
        }
    });

    $("#btnAsignar_perfiles").click(() => {
        if (idpersona == 0) {
            MensajeConClase(
                "Antes de continuar debe seleccionar la persona..!",
                "info",
                "Oops..."
            );
        } else {
            traerPerfilesPersona(idpersona);
            perfiles_faltantes(idpersona);

            $("#asignar_perfiles_modal").modal();
        }
    });

    $("#btneliminar_persona").click(() => {
        if (idpersona == 0) {
            MensajeConClase(
                "Antes de continuar debe seleccionar la persona a Eliminar..!",
                "info",
                "Oops..."
            );
        } else {
            swal({
                    title: "Estas Seguro ?",
                    text: "Tener en cuenta que antes de Eliminar una Persona, Esta no debe Tener Asociado Ninguna Actividad para que Pueda Ser Eliminada!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#D9534F",
                    confirmButtonText: "Si, Eliminar!",
                    cancelButtonText: "No, cancelar!",
                    allowOutsideClick: true,
                    closeOnConfirm: false,
                    closeOnCancel: true,
                },
                (isConfirm) => {
                    if (isConfirm) {
                        eliminar_Persona(idpersona);
                    }
                }
            );
        }
    });

    $("#btnEliminarpersona").click(() => {
        eliminar_Persona(idpersona);
        $("#MensajeEliminar").hide("fast");
        $("#MensajeEliminar").html("Persona Eliminada Con exito");
        $("#MensajeEliminar").show("slow");
        $(".botonesEliminar").hide("slow");
        $("#salirEliminar").show("slow");
    });

    $("#PerfilesUsuario").submit(() => {
        Asignar_Perfil();
        return false;
    });

    $("#asignarPerfiles").submit(() => {
        const perfil = $(`#asignarPerfiles select[name=asignar_perfiles]`).val();
        Asignar_Perfiles_usuario(perfil);
        return false;
    });

    //....................carga de modal y busqueda de cargo

    $("#Registrar-persona input[name='cargosSAP'], #btn_add_cargo").click(() => {
        accion_tabla_cargos = "sel";
        $("#modal_buscar_cargos").modal();
        $("#form_buscar_cargos").get(0).reset();

        buscar_cargos_sap();
    });

    $("#form_buscar_cargos").submit((e) => {
        e.preventDefault();
        const dato_buscar = $(`#form_buscar_cargos input[name=cargos]`).val();
        let callback = "";
        buscar_cargos_sap(dato_buscar);
    });



    $("#btn_aplicar_filtros").click(function() {
        $("#modal_crear_filtros").modal();
    });

    $("#btn_Importar_excel").click(function() {
        $("#modal_cargar_excel").modal();
    });

    $("#guardar_datos_excel").click(function() {
        guardar_datos_excel(str);
    });

    $("#form_filtros").submit((e) => {
        e.preventDefault();
        if (
            $("#modal_crear_filtros select[name=id_tipo_per_select]").val().length ==
            0 &&
            $("#modal_crear_filtros select[name=id_tipo_car_select]").val().length ==
            0 &&
            $("#modal_crear_filtros select[name=id_tipo_cont_select]").val().length ==
            0 &&
            $("#modal_crear_filtros select[name=id_tipo_perf_select]").val().length ==
            0 &&
            document.getElementById("fecha_inicial").value == 0 &&
            document.getElementById("fecha_final").value == 0
        ) {
            $("#mensaje-filtro-evento").hide();
        } else $("#mensaje-filtro-evento").show();
        filtrar_personas();
        return false;
    });
});
//BLOQUE DE FUNCIONES
// la funcion listar_Personas  se conecta al controlador Cargar_personas el cual trae toda la informacion para luego pintarla en la tabla

const sesionActiva = () => {
    $.ajax({
        url: `${server}index.php/personas_control/sesionActiva`,
        dataType: "json",
        type: "post",
    }).done((datos) => {
        if (datos == true) {
            pSesion = true;
        } else pSesion = false;
    });
};

const filtrar_personas = () => {
    data = {
        id_tipo_persona: $(
            "#modal_crear_filtros select[name=id_tipo_per_select]"
        ).val(),
        id_tipo_cargo: $(
            "#modal_crear_filtros select[name=id_tipo_car_select]"
        ).val(),
        id_tipo_contrato: $(
            "#modal_crear_filtros select[name=id_tipo_cont_select]"
        ).val(),
        id_tipo_perfil: $(
            "#modal_crear_filtros select[name=id_tipo_perf_select]"
        ).val(),
        fecha_inicial: $("#modal_crear_filtros input[name='fecha_inicial']").val(),
        fecha_final: $("#modal_crear_filtros input[name='fecha_final']").val(),
    };

    pSesion == true ? listar_Personas(0, data) : listar_Personas_noAdmin(0, data);

    $("#modal_crear_filtros").modal("hide");
    document.getElementById("form_filtros").reset();
    $("#id_tipo_car_select").selectpicker("refresh");
    $("#id_tipo_perf_select").selectpicker("refresh");
};

const listar_Personas_noAdmin = (buscar, filtros = {}) => {
    dato_busqueda = buscar;
    let {
        id_tipo_persona,
        id_tipo_cargo,
        id_tipo_contrato,
        id_tipo_perfil,
        fecha_inicial,
        fecha_final,
    } = filtros;


    $("#tablapersonas tbody").off("click", "tr");
    $("#tablapersonas tbody").off("dblclick", "tr");
    $("#tablapersonas tbody").off("click", "tr td:nth-of-type(1)");
    const table = $("#tablapersonas").DataTable({
        destroy: true,
        ajax: {
            url: `${ruta}Cargar_personas`,
            dataType: "json",
            type: "post",
            data: {
                buscar,
                id_tipo_persona,
                id_tipo_cargo,
                id_tipo_contrato,
                id_tipo_perfil,
                fecha_inicial,
                fecha_final,
            },
            dataSrc: (json) => (json.length == 0 ? Array() : json.data),
        },
        searching: false,
        processing: true,
        columns: [{
                data: "codigo",
            },
            {
                data: "id",
                visible: false,
            },
            {
                data: "nombre_completo",
            },
            {
                data: "identificacion",
            },
            {
                data: "cargo_sap",
            },
            {
                data: "correo",
            },
            {
                data: "usuario",
            },
            {
                data: "id_perfil",
                visible: false,
            },
        ],

        language: idioma,
        dom: "Bfrtip",
        buttons: [{
                // genera boton para exportar Excel
                extend: "excelHtml5",
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: "Excel",
                className: "btn btn-success",
            },
            {
                // genera boton para exportar csv
                extend: "csvHtml5",
                text: '<i class="fa fa-file-text-o"></i>',
                titleAttr: "CSV",
                className: "btn btn-default",
            },
            {
                // genera boton para exportar pdf
                extend: "pdfHtml5",
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: "PDF",
                className: "btn btn-danger2",
            },
        ],
    });

    // eventos al presionar click o doble click en la tabla
    $("#tablapersonas tbody").on("click", "tr", function() {
        const data = table.row(this).data();
        $("#tablapersonas tr").removeClass("warning");
        $(this).attr("class", "warning");
        identificacion = data.identificacion;
        idpersona = data.id;
    });

    $("#tablapersonas tbody").on("dblclick", "tr", function() {
        const data = table.row(this).data();
        obtener_datos_persona_tabla_id(data);
    });

    $("#tablapersonas tbody").on("click", "tr td:nth-of-type(1)", function() {
        const data = table.row($(this).parent()).data();
        obtener_datos_persona_tabla_id(data);
    });
};

const listar_Personas = (buscar, filtros = {}) => {
    dato_busqueda = buscar;
    let {
        id_tipo_persona,
        id_tipo_cargo,
        id_tipo_contrato,
        id_tipo_perfil,
        fecha_inicial,
        fecha_final,
    } = filtros;

    $("#tablapersonas tbody").off("click", "tr");
    $("#tablapersonas tbody").off("dblclick", "tr");
    $("#tablapersonas tbody").off("click", "tr td:nth-of-type(1)");
    const table = $("#tablapersonas").DataTable({
        destroy: true,
        ajax: {
            url: `${ruta}Cargar_personas`,
            dataType: "json",
            type: "post",
            data: {
                buscar,
                id_tipo_persona,
                id_tipo_cargo,
                id_tipo_contrato,
                id_tipo_perfil,
                fecha_inicial,
                fecha_final,
            },
            dataSrc: (json) => (json.length == 0 ? Array() : json.data),
        },
        searching: false,
        processing: true,
        columns: [{
                data: "codigo",
            },
            {
                data: "id",
            },
            {
                data: "nombre_completo",
            },
            {
                data: "identificacion",
            },
            {
                data: "cargo_sap",
            },
            {
                data: "correo",
            },
            {
                data: "usuario",
            },
            {
                data: "id_perfil",
            },
        ],

        language: idioma,
        dom: "Bfrtip",
        buttons: [{
                // genera boton para exportar Excel
                extend: "excelHtml5",
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: "Excel",
                className: "btn btn-success",
            },
            {
                // genera boton para exportar csv
                extend: "csvHtml5",
                text: '<i class="fa fa-file-text-o"></i>',
                titleAttr: "CSV",
                className: "btn btn-default",
            },
            {
                // genera boton para exportar pdf
                extend: "pdfHtml5",
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: "PDF",
                className: "btn btn-danger2",
            },
        ],
    });

    // eventos al presionar click o doble click en la tabla
    $("#tablapersonas tbody").on("click", "tr", function() {
        const data = table.row(this).data();
        $("#tablapersonas tr").removeClass("warning");
        $(this).attr("class", "warning");
        identificacion = data.identificacion;
        idpersona = data.id;
    });

    $("#tablapersonas tbody").on("dblclick", "tr", function() {
        const data = table.row(this).data();
        obtener_datos_persona_tabla_id(data);
    });

    $("#tablapersonas tbody").on("click", "tr td:nth-of-type(1)", function() {
        const data = table.row($(this).parent()).data();
        obtener_datos_persona_tabla_id(data);
    });
};

const obtener_datos_persona_tabla_id = ({
    nombre_completo,
    identificacion,
    id_tipo_identificacion,
    id_perfil,
    telefono,
    tipo_persona,
    cargo_sap,
    tipo_contrato,
    fecha_registra,
}) => {
    $(".nombre_perso").html(nombre_completo);
    $(".identi_perso").html(identificacion);
    $(".tipo_id_perso").html(id_tipo_identificacion);
    $(".cargo_perso").html(cargo_sap);
    $(".perfil_perso").html(id_perfil);
    $(".celular").html(telefono);
    $("#tipo_persona_id").html(tipo_persona);
    $(".tipo_cont").html(tipo_contrato);
    $(".fecha_registro").html(fecha_registra);
    $("#Mostrar_detalle_persona").modal();
};
//la funcion registrarPersona se encarga de guardar las personas al enviar el formulario llamado form-ingresar-persona
const registrarPersona = () => {
    //se obtiene el formulario a enviar
    var formData = new FormData(document.getElementById("form-ingresar-persona"));
    formData.append("codigo_cargo_sap", codigo_cargo_sap);
    valor_tipo_perfil = document.getElementById("tipo_perfil").value;
    formData.append("valor_tipo_perfil", valor_tipo_perfil);
    // llamada AJAX para enviar los datos
    $.ajax({
        destroy: true,
        url: `${server}index.php/personas_control/guardar_persona/personas`,
        type: "post",
        dataType: "json",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done((datos) => {
        ///si se conecto al controlador se le informa al usuario el estado de la accion
        switch (datos) {
            case -1000:
                close();
                break;
            case -1:
                MensajeConClase("Todos Los campos son Obligatorios", "info", "Oops.!");
                break;
            case -2:
                MensajeConClase(
                    "El Persona ya se encuentra en el Sistema",
                    "info",
                    "Oops.!"
                );
                break;
            case -3:
                MensajeConClase("Seleccione Cargo SAP de la persona", "info", "Oops.!");
                break;
            case -4:
                MensajeConClase("Ingrese correo de la persona", "info", "Oops.!");
                break;
            case -5:
                MensajeConClase("Ingrese usuario de la persona", "info", "Oops.!");
                break;
            case -6:
                MensajeConClase(
                    "El nombre de usuario ya se encuentra registrado.",
                    "info",
                    "Oops.!"
                );
                break;
            case -7:
                MensajeConClase(
                    "El correo ya se encuentra registrado.",
                    "info",
                    "Oops.!"
                );
                break;
            case 4:
                dato_busqueda = $("#txtIdentificacion").val();
                MensajeConClase(
                    "Persona Guardada Con exito",
                    "success",
                    "Proceso Exitoso!"
                );
                $("#form-ingresar-persona").get(0).reset();
                listar_Personas(dato_busqueda, 0);
                pSesion == true ?
                    listar_Personas(dato_busqueda, 0) :
                    listar_Personas_noAdmin(dato_busqueda, 0);

                break;
            case 6:
                MensajeConClase(
                    "El Nombre de usuario ya se encuentra Registrado",
                    "info",
                    "Oops.!"
                );
                break;
            case -1302:
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops.!"
                );
            default:
                MensajeConClase("Error al Guardar a la persona", "error", "Oops.!");
                break;
        }
    });
};

//la funcion eliminar_Persona se encarga de des habilitar una persona dentro de la aplicacion se recibe por parametro el id de la persona.
const eliminar_Persona = (idpersona) => {
    //llamada AJAX para des habilitar a la persona
    $.ajax({
        url: `${server}index.php/personas_control/Eliminar_persona`,
        dataType: "json",
        data: {
            idpersona,
        },
        type: "post",
    }).done((datos) => {
        ///si se conecto al controlador se le informa al usuario el estado de la accion
        switch (datos) {
            case "sin_session":
                close();
                break;
            case 1:
                MensajeConClase(
                    "Persona Eliminada Con exito con exito",
                    "success",
                    "Proceso Exitoso!"
                );
                break;
            case -1302:
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops..."
                );
                break;
            default:
                MensajeConClase("Error al Eliminar a la Persona", "error", "Oops...");
                break;
        }
    });
};

//la funcion modificar_persona se encarga de actualizar la persona seleccioanda de la tabla, la accion se ejecuta cuand oel usuario envia el formulario form-modificar-persona
const Modificar_persona = () => {
    //se obtiene el formulario a enviar
    var formData = new FormData(
        document.getElementById("form-modificar-persona")
    );
    formData.append("id", idpersona);
    formData.append("codigo_cargo_sap", codigo_cargo_sap);
    valor_tipo_perfil = document.getElementById("tipo_perfil_modificar").value;
    formData.append("valor_tipo_perfil_modificar", valor_tipo_perfil);

    // llamada AJAX para enviar los datos
    $.ajax({
        url: `${server}index.php/personas_control/modificar_persona/personas`,
        type: "post",
        dataType: "json",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done((datos) => {
        ///si se conecto al controlador se le informa al usuario el estado de la accion
        switch (datos) {
            case -1000:
                close();
                break;
            case -1:
                MensajeConClase("Todos Los campos son Obligatorios", "info", "Oops.!");
                break;
            case -2:
                MensajeConClase(
                    "El Persona ya se encuentra en el Sistema",
                    "info",
                    "Oops.!"
                );
                break;
            case -3:
                MensajeConClase("Seleccione Cargo SAP de la persona", "info", "Oops.!");
                break;
            case -4:
                MensajeConClase("Ingrese correo de la persona", "info", "Oops.!");
                break;
            case -5:
                MensajeConClase("Ingrese usuario de la persona", "info", "Oops.!");
                break;
            case -6:
                MensajeConClase(
                    "El nombre de usuario ya se encuentra registrado.",
                    "info",
                    "Oops.!"
                );
                break;
            case -7:
                MensajeConClase(
                    "El correo ya se encuentra registrado.",
                    "info",
                    "Oops.!"
                );
                break;
            case 4:
                $("#Modificar_persona").modal("hide");
                dato_busqueda = $("#identificacion_modifica").val();
                MensajeConClase(
                    "Persona Modificada Con exito",
                    "success",
                    "Proceso Exitoso!"
                );
                pSesion == true ?
                    listar_Personas(dato_busqueda, 0) :
                    listar_Personas_noAdmin(dato_busqueda, 0);
                break;
            case 6:
                MensajeConClase(
                    "El Nombre de usuario ya se encuentra Registrado",
                    "info",
                    "Oops.!"
                );
                break;
            case -1302:
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops.!"
                );
            default:
                MensajeConClase("Error al Guardar a la persona", "error", "Oops.!");
                break;
        }
    });
};

//la funcion obtener_datos_persona se encarga de buscar una persona por el id de esta y mostrar la informacion en el formulario de form-modificar-persona
const obtener_datos_persona = (id) => {
    // llamada AJAX para enviar los datos
    $.ajax({
        url: `${server}index.php/personas_control/obtener_datos_persona`,
        dataType: "json",
        data: {
            id,
        },
        type: "post",
    }).done((datos) => {
        //si se conecto al controlador se le informa al usuario el estado de la accion
        if (datos == "sin_session") {
            close();
            return;
        }

        $("#tipo_perfil_modificar").val(datos[0].id_perfil);
        $("#nombre_modifica").val(datos[0].nombre);
        $("#apellido_modifica").val(datos[0].apellido);
        $("#segundo_nombre_modifica").val(datos[0].segundo_nombre);
        $("#segundo_apellido_modifica").val(datos[0].segundo_apellido);
        $("#identificacion_modifica").val(datos[0].identificacion);
        $("#cbxtipoIdentificacion_modifica").val(datos[0].id_tipo_identificacion);
        $("#sueldo_modificar").val(datos[0].sueldo);
        $("#cargosSAP_modificar").val(datos[0].valor);
        $("#fecha_modificar").val(datos[0].fecha_inicio_contrato);
        $("#tipo_contrato_modificar").val(datos[0].tipo_contrato);

        codigo_cargo_sap = datos[0].id_cargo_sap;
        console.log(codigo_cargo_sap);

        Listar_cargos_departamento_combo(
            "#cbxcargos_modifica",
            "Seleccione Cargo",
            datos[0].id_departamento,
            datos[0].id_cargo
        );
        $("#tipo_persona_id_modifica").val(datos[0].id_tipo_persona);
        $("#telefono_modifica").val(datos[0].telefono);
        $("#correo_modifica").val(datos[0].correo);
        $("#usuario_modifica").val(datos[0].usuario);

        if (datos[0].id_tipo_persona == "PerInt") {
            $(".datos_internos_modi").show("slow");
            $(".usuario input").attr("required", "true");
            $(".datos_internos_modi select").attr("required", "true");
        } else {
            $(".datos_internos_modi").hide("slow");
            $(".usuario input").removeAttr("required");
            $(".datos_internos_modi select").removeAttr("required");
        }
    });
};

//la funcion Asignar_Perfil se encarga de asignarle un perfil a la persona seleccioanda de la tabla, se envia el id de la persona y el id del perfil seleccioando en el formulario Asignar_Perfil_usuario
const Asignar_Perfil = () => {
    //se obtiene el formulario a enviar
    var formData = new FormData(document.getElementById("PerfilesUsuario"));
    // se agrega al formulario el id de la persona
    formData.append("id", idpersona);
    //llamada AJAX para enviar los datos
    $.ajax({
        url: `${server}index.php/personas_control/Asignar_Perfil`,
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done((datos) => {
        //si se conecto al controlador se le informa al usuario el estado de la accion
        if (datos == "sin_session") {
            close();
            return;
        } else if (datos == 4) {
            getPerfilesPersona(idpersona);
            cargar_perfiles_faltantes(idpersona);
            MensajeConClase(
                "Actividad Asignada con Exito",
                "success",
                "Proceso Exitoso!"
            );
            return false;
        } else if (datos == -1302) {
            MensajeConClase(
                "No tiene Permisos Para Realizar Esta Opereacion",
                "error",
                "Oops..."
            );
        } else if (datos == -1) {
            MensajeConClase(
                "La persona ya tiene asignado este Perfil",
                "info",
                "Oops..."
            );
        } else {
            MensajeConClase("Error al Asignar el perfil", "error", "Oops...");
        }
    });
};

//la funcion Tiene_Perfil se encarga de validar por medio del id de la persona si esta tiene un perfil asignado
const Tiene_Perfil = () => {
    //llamada AJAX para enviar los datos
    $.ajax({
        url: server + "index.php/personas_control/Tiene_Perfil",
        dataType: "json",
        data: {
            idpersona,
        },
        type: "post",
    }).done((datos) => {
        //si se conecto al controlador se cambia el estado de la variable tieneperfil dependiendo de lo que envie el controlador
        if (datos == "sin_session") {
            close();
            return;
        }
        tieneperfil = datos == "Sin Asignar" ? false : true;
    });
};

const getPerfilesPersona = (id) => {
    $("#tblPerfilesPersonas tbody").off("click", "tr");
    $("#tblPerfilesPersonas tbody").off("dblclick", "tr");
    const table = $("#tblPerfilesPersonas").DataTable({
        destroy: true,
        ajax: {
            url: `${server}index.php/personas_control/Cargar_perfiles_persona`,
            dataType: "json",
            type: "post",
            data: {
                id,
            },
            dataSrc: (json) => {
                return json.length == 0 ? Array() : json.data;
            },
        },
        searching: false,
        processing: true,
        columns: [{
                data: "num",
            },
            {
                data: "perfil",
            },
            {
                data: "gestion",
            },
        ],
        language: idioma,
        dom: "Bfrtip",
        buttons: [],
    });
    // eventos al presionar click o doble click en la tabla
    $("#tblPerfilesPersonas tbody").on("click", "tr", function() {
        $("#tblPerfilesPersonas tr").removeClass("warning");
        $(this).attr("class", "warning");
    });
};

const eliminar_perfil_persona = (id) => {
    swal({
            title: "Estas seguro?",
            text: "El perfil será eliminado de forma permanente.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Si, Eliminar!",
            cancelButtonText: "No, cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: true,
            closeOnCancel: true,
        },
        (isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: `${server}index.php/personas_control/eliminar_perfil_persona`,
                    dataType: "json",
                    data: {
                        id,
                    },
                    type: "post",
                }).done((datos) => {
                    //si se conecto al controlador se cambia el estado de la variable tieneperfil dependiendo de lo que envie el controlador
                    switch (datos) {
                        case "sin_session":
                            close();
                            return;
                        case 1:
                            MensajeConClase(
                                "El perfil ha sido eliminado exitosamente.",
                                "success",
                                "Actividad Eliminada!"
                            );
                            getPerfilesPersona(idpersona);
                            cargar_perfiles_faltantes(idpersona);
                            break;
                        default:
                            break;
                    }
                });
            }
        }
    );
};

const cargar_perfiles_faltantes = (id) => {
    $.ajax({
        url: `${server}index.php/personas_control/Cargar_perfiles_faltantes`,
        dataType: "json",
        data: {
            id,
        },
        type: "post",
    }).done((datos) => {
        const combo = $("#cbxasignar_perfiles");
        combo.html("");
        combo.append("<option value=''>Seleccione Actividad</option>");
        for (let i = 0; i <= datos.length - 1; i++) {
            combo.append(
                `<option value="${datos[i].codigo}">${datos[i].nombre}</option>`
            );
        }
        $(".selectpicker").selectpicker("refresh");
    });
};

const buscar_cargos_sap = (dato_buscar, callback) => {
    $("#tabla_cargos_sap tbody").off("click", "tr td .seleccionar");
    let i = 0;
    consulta_ajax(`${ruta}buscar_cargos_sap`, { dato_buscar }, (data) => {
        const myTable = $("#tabla_cargos_sap").DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    },
                },

                { data: "nombre_cargo" },
                {
                    data: "id",
                    render: function(data) {
                        return `<span style="color: #39B23B;" data-art_id="${data}" title="Seleccionar Cargo" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`;
                    },
                },
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: [],
        });

        $("#tabla_cargos_sap tbody").on("click", "tr td .seleccionar", function() {
            let data = myTable.row($(this).parent()).data();
            codigo_cargo_sap = data.id;
            $("#modal_buscar_cargos").modal("hide");
            $("#form-ingresar-persona input[name='cargosSAP']").val(
                data.nombre_cargo
            );
            $("#form-modificar-persona input[name='cargosSAP']").val(
                data.nombre_cargo
            );

            MensajeConClase(
                "cargo seleccionado con exito!.",
                "success",
                data.nombre_cargo + " seleccionado!"
            );
        });
    });
};


const traerPerfilesPersona = (id) => {
    $("#PerfilesPersonas tbody").off("click", "tr");
    $("#PerfilesPersonas tbody").off("dblclick", "tr");
    $("#PerfilesPersonas tbody").off("click", "tr td .predeterminado_asig");
    $("#PerfilesPersonas tbody").off("click", "tr td .predeterminado");
    const table = $("#PerfilesPersonas").DataTable({
        destroy: true,
        ajax: {
            url: `${server}index.php/personas_control/traerPerfilesPersona`,
            dataType: "json",
            type: "post",
            data: {
                id,
            },
            dataSrc: (json) => {
                return json.length == 0 ? Array() : json.data;
            },
        },
        searching: false,
        processing: true,

        columns: [{
                data: "num",
            },
            {
                data: "perfil",
            },
            {
                data: "gestion",
            },
            {
                data: "pre",
            },
        ],
        language: idioma,
        dom: "Bfrtip",
        buttons: [],
    });
    // eventos al presionar click o doble click en la tabla
    $("#PerfilesPersonas tbody").on("click", "tr", function() {
        $("#PerfilesPersonas tr").removeClass("warning");
        $(this).attr("class", "warning");
    });

    $("#PerfilesPersonas tbody").on(
        "click",
        "tr td .predeterminado",
        function() {
            let data = table.row($(this).parent().parent()).data();
            perfil_predeterminado(data.id_perfil);
        }
    );

    $("#PerfilesPersonas tbody").on(
        "click",
        "tr td .predeterminado_asig",
        function() {
            MensajeConClase(
                "El perfil es Predeterminado",
                "info",
                "Imposible eliminar"
            );
        }
    );
};

const Asignar_Perfiles_usuario = (perfil) => {
    //se obtiene el formulario a enviar
    var formData = new FormData();
    // se agrega al formulario el id de la persona
    formData.append("id", idpersona);
    formData.append("idperfil", perfil);
    //llamada AJAX para enviar los datos
    $.ajax({
        url: `${server}index.php/personas_control/Asignar_Perfiles_usuario`,
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done((datos) => {
        //si se conecto al controlador se le informa al usuario el estado de la accion
        if (datos == "sin_session") {
            close();
            return;
        } else if (datos == 4) {
            traerPerfilesPersona(idpersona);
            perfiles_faltantes(idpersona);

            $.ajax({
                url: `${server}index.php/personas_control/obtener_datos_usuario`,
                dataType: "json",
                type: "post",
                success: function(data) {
                    if (data == "sin_session") {
                        close();
                        return;
                    }
                    detallePersona(); //Actualiza los perfiles del menu var
                },
                error: function() {
                    console.log("Something went wrong", status, err);
                },
            });

            MensajeConClase(
                "Perfil Asignado con Exito",
                "success",
                "Proceso Exitoso!"
            );
            return false;
        } else if (datos == -1302) {
            MensajeConClase(
                "No tiene Permisos Para Realizar Esta Opereacion",
                "error",
                "Oops..."
            );
        } else if (datos == -1) {
            MensajeConClase(
                "La persona ya tiene asignado este Perfil",
                "info",
                "Oops..."
            );
        } else {
            MensajeConClase("Error al Asignar el perfil", "error", "Oops...");
        }
    });
};

const eliminar_perfil = (id) => {
    swal({
            title: "Estas seguro?",
            text: "El perfil será eliminado de forma permanente.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Si, Eliminar!",
            cancelButtonText: "No, cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: true,
            closeOnCancel: true,
        },
        (isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    url: `${server}index.php/personas_control/eliminar_perfil`,
                    dataType: "json",
                    data: {
                        id,
                        idpersona,
                    },
                    type: "post",
                }).done((datos) => {
                    //si se conecto al controlador se cambia el estado de la variable tieneperfil dependiendo de lo que envie el controlador
                    switch (datos) {
                        case "sin_session":
                            close();
                            return;
                        case 1:
                            MensajeConClase(
                                "El perfil ha sido eliminado exitosamente.",
                                "success",
                                "Perfil Eliminado!"
                            );
                            traerPerfilesPersona(idpersona);
                            perfiles_faltantes(idpersona);

                            $.ajax({
                                url: `${server}index.php/personas_control/obtener_datos_usuario`,
                                dataType: "json",
                                type: "post",
                                success: function(data) {
                                    if (data == "sin_session") {
                                        close();
                                        return;
                                    }
                                    detallePersona(); //Actualiza los perfiles del menu var
                                },
                                error: function() {
                                    console.log("Something went wrong", status, err);
                                },
                            });

                            break;
                        default:
                            break;
                    }
                });
            }
        }
    );
};

const perfil_predeterminado = (id_perfil) => {
    var formData = new FormData();
    formData.append("id", idpersona);
    formData.append("identificacion", identificacion);
    formData.append("id_perfil", id_perfil);

    $.ajax({
        url: `${server}index.php/personas_control/actualizar_perfil`,
        type: "post",
        dataType: "json",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done((datos) => {
        switch (datos) {
            case -1000:
                close();
                break;
            case 4:
                traerPerfilesPersona(idpersona);
                MensajeConClase(
                    "Perfil Elegido como Predeterminado",
                    "success",
                    "Proceso Exitoso!"
                );
                pSesion == true ?
                    listar_Personas(dato_busqueda, 0) :
                    listar_Personas_noAdmin(dato_busqueda, 0);
                break;
            case -1302:
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops.!"
                );
            default:
                MensajeConClase("Error al actualizar perfil de la persona", "error", "Oops.!");
                break;
        }
    });
};

const perfiles_faltantes = (id) => {
    $.ajax({
        url: `${server}index.php/personas_control/perfiles_faltantes`,
        dataType: "json",
        data: {
            id,
        },
        type: "post",
    }).done((datos) => {
        const combo = $("#asignar_perfiles");
        combo.html("");
        combo.append("<option value=''>Seleccione Perfil</option>");
        for (let i = 0; i <= datos.length - 1; i++) {
            combo.append(
                `<option value="${datos[i].codigo}">${datos[i].nombre}</option>`
            );
        }

        $(".selectpicker").selectpicker("refresh");
    });
};

function importarExcel(excel) {
    if (!excel.files) {
        return;
    }
    var f = excel.files[0];
    var reader = new FileReader();
    reader.readAsBinaryString(f);
    reader.onload = function(e) {
        var data = e.target.result;
        excelFile = XLSX.read(data, {
            type: "binary",
        });

        str = XLSX.utils.sheet_to_json(excelFile.Sheets[excelFile.SheetNames[0]]);

        for (x of str) {
            var exdate = x.Fecha;
            var e0date = new Date(0);
            var offset = e0date.getTimezoneOffset();
            var fecha = new Date(0, 0, exdate, 0, -offset, 0);
            x.Fecha = moment(fecha).format("YYYY-MM-DD");
        }

        $("#tbl_excel").DataTable({
            destroy: true,
            processing: true,
            data: str,
            columns: [{
                    data: "ID",
                    "defaultContent": ""
                },
                {
                    data: "TipoIdentificacion",
                    "defaultContent": ""
                },
                {
                    data: "PrimerNombre",
                    "defaultContent": ""
                },
                {
                    data: "SegundoNombre",
                    "defaultContent": ""
                },
                {
                    data: "PrimerApellido",
                    "defaultContent": ""
                },
                {
                    data: "SegundoApellido",
                    "defaultContent": ""
                },
                {
                    data: "cargos",
                    "defaultContent": ""
                },
                {
                    data: "Fecha",
                    "defaultContent": ""
                },
                {
                    data: "claseContrato",
                    "defaultContent": ""
                },
                {
                    data: "Importe",
                    "defaultContent": ""
                },
                // {
                //     data: "Correo",
                //     "defaultContent": ""
                // },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones(),
        });
        $("#modal_importar_excel").modal();
        $("#modal_cargar_excel").modal('hide');
        document.getElementById("form_excel").reset();

    };
}

const guardar_datos_excel = (str) => {
    let info = formDataToJson(str);

    MensajeConClase("validando info", "add_inv", "Oops...");
    return new Promise((resolve) => {
        consulta_ajax(`${ruta}guardar_datos_excel`, { info }, async(resp) => {
            resolve(resp);

            let cargos = resp[0] ? resp[0] : 0;
            let registra = resp[1];
            let no_registra = resp[2];
            let modifica = resp[3];
            let no_modifica = resp[4];

            document.getElementById('labcargoResultado').innerHTML = "Cantidad: " + cargos.length;
            document.getElementById('labregistrado').innerHTML = "Cantidad: " + registra.length;
            document.getElementById('labno_registrado').innerHTML = "Cantidad: " + no_registra.length;
            document.getElementById('labactualizado').innerHTML = "Cantidad: " + modifica.length;
            document.getElementById('labno_actualizado').innerHTML = "Cantidad: " + no_modifica.length;
            MensajeConClase("Proceso Realizado", "success", "Proceso Exitoso...");
            $("#resultado").modal();

            $("#btn_cargos").off("click")
            $("#btn_cargos").click(function() {
                let i = 0;
                if (cargos.length == 0) {
                    MensajeConClase("No Se Agregaron Nuevos Cargos", "info", "Oops...");
                } else {
                    $("#cargos_guardados").modal();
                    $("#tblcargos_guardados").DataTable({
                        destroy: true,
                        processing: true,
                        data: cargos,
                        searching: true,
                        paging: true,
                        info: true,
                        columns: [{
                                render: function(data, type, full, meta) {
                                    i++;
                                    return i;
                                },
                            },
                            {
                                data: "cargo",
                            },
                        ],
                        language: get_idioma(),
                        dom: "Bfrtip",
                        buttons: get_botones(),
                    });
                }
            });

            $("#btn_personasAdd").off("click")
            $("#btn_personasAdd").click(function() {
                if (registra.length == 0) {
                    MensajeConClase("No Se Registraron Nuevas Personas", "info", "Oops...");
                } else {
                    $("#personas_registradas").modal();
                    $("#tblpersonas_registradas").DataTable({
                        destroy: true,
                        processing: true,
                        data: registra,
                        searching: true,
                        paging: true,
                        info: true,
                        columns: [{
                                data: "identificacion",
                            },
                            {
                                data: "PrimerNombre",
                            },
                            {
                                data: "PrimerApellido",
                            },
                            {
                                data: "cargos",
                            },
                            {
                                data: "claseContrato",
                            },
                        ],
                        language: get_idioma(),
                        dom: "Bfrtip",
                        buttons: get_botones(),
                    });
                }
            });

            $("#btn_personasUpd").off("click")
            $("#btn_personasUpd").click(function() {
                if (modifica.length == 0) {
                    MensajeConClase("No Se Actualizaron Personas Existentes", "info", "Oops...");
                } else {
                    $("#personas_actualizadas").modal();
                    $("#tblpersonas_actualizadas").DataTable({
                        destroy: true,
                        processing: true,
                        data: modifica,
                        searching: true,
                        paging: true,
                        info: true,
                        columns: [{
                                data: "identificacion",
                            },
                            {
                                data: "PrimerNombre",
                            },
                            {
                                data: "PrimerApellido",
                            },
                            {
                                data: "cargos",
                            },
                            {
                                data: "claseContrato",
                            },
                        ],
                        language: get_idioma(),
                        dom: "Bfrtip",
                        buttons: get_botones(),
                    });
                }
            });

            $("#btn_personas_noADD").off("click")
            $("#btn_personas_noADD").click(function() {
                if (no_registra.length == 0) {
                    MensajeConClase("No Existen Personas sin Registrar", "info", "Oops...");
                } else {
                    $("#personas_no_registradas").modal();
                    $("#tblpersonas_no_registradas").DataTable({
                        destroy: true,
                        processing: true,
                        data: no_registra,
                        searching: true,
                        paging: true,
                        info: true,
                        columns: [{
                                data: "identificacion",
                            },
                            {
                                data: "PrimerNombre",
                            },
                            {
                                data: "PrimerApellido",
                            },
                            {
                                data: "errores"
                            },

                        ],
                        language: get_idioma(),
                        dom: "Bfrtip",
                        buttons: get_botones(),
                    });
                }
            });

            $("#btn_personas_noUpd").off("click")
            $("#btn_personas_noUpd").click(function() {
                if (no_modifica.length == 0) {
                    MensajeConClase("No Existen Personas Sin Actualizar", "info", "Oops...");
                } else {
                    $("#personas_no_actualizadas").modal();
                    $("#tblpersonas_no_actualizadas").DataTable({
                        destroy: true,
                        processing: true,
                        data: no_modifica,
                        searching: true,
                        paging: true,
                        info: true,
                        columns: [{
                                data: "identificacion",
                            },
                            {
                                data: "PrimerNombre",
                            },
                            {
                                data: "PrimerApellido",
                            },
                            {
                                data: "errores"
                            },
                        ],
                        language: get_idioma(),
                        dom: "Bfrtip",
                        buttons: get_botones(),
                    });
                }
            });
        });
    });
};