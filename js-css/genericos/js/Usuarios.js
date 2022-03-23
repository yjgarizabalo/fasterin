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
let perfil_activo = "";
var acept = 0;
var idperfil = 0;
var id_permisos_perfil = 0;
var server = "localhost";
let ruta_pages = "";

$(document).ready(function () {
    ruta_pages = `${Traer_Server()}index.php/pages`;
    server = Traer_Server();
    Mostrar_Contenido();
    detallePersona();

    $('#perfiles').change(function () {
        let perfil = $(this).val();
        perfilEnSesion(perfil);
    });

    $("#listado_perfiles").change(function () {
        idperfil = $(this).val();
        Listar_permisos_perfiles_usuarios(idperfil);
    });
    $("#perfiles_cuenta").change(function () {
        idperfil = $(this).val();
        cambiar_perfil(idperfil);
    });

    $("#cuentauser").click(function () {
        Datos_Cuenta();
    });

    $("#Recargar").click(function () {
        location.reload();
    });

    $("#logeo").submit(function () {
        Logear();
        return false;
    });

    $("#Asignar_Activiad").click(function () {
        if (idperfil == 0) {
            MensajeConClase(
                "Seleccione Perfil al Cual desea Asignar la Actividad",
                "info",
                "Oops..."
            );
        } else {
            Listar_Actividades_Sin_Asignar_Perfil();
        }
    });

    $("#Guardar_actividad_perfil").submit(function () {
        registrar_Actividad_perfil();
        return false;
    });
    $("#salir").click(function () {
        swal({
            title: "Salir del sistema ?",
            text: 'Para cerrar la Sesión actual presionar la opción "Si,Salir"',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Si, Salir!",
            cancelButtonText: "No, cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true,
        },
            function (isConfirm) {
                if (isConfirm) {
                    cerrar_sesion(1);
                }
            }
        );

        return false;
    });
});

/* Funcion para checkear que el codigo enviado es el correcto */
const check_validation_code = async (usuario, codigo) => {
    return new Promise((resolve) => {
        if (codigo == "" || codigo == "undefined" || codigo == undefined) {
            resolve(false);
        } else {
            let url = `${ruta_pages}/check_validation_code`;
            consulta_ajax(url, { usuario, codigo }, (res) => {
                if (res.resp == "valido") {
                    Logear(usuario, "si");
                    resolve(true);
                } else {
                    resolve(false);
                }
            });
        }
    });
};

const Logear = async (usuario = "", checked = "no") => {
    var formData = new FormData(document.getElementById("logeo"));
    formData.append("acept", acept);
    formData.append("checked", checked);
    $.ajax({
        url: server + "index.php/pages/Logear",
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done(function (datos) {
        /* Agregado */
        let data = JSON.parse(datos);
        if (data.tipo == "info") {
            MensajeConClase(data.mensaje, data.tipo, data.titulo);
            return false;
        }
        /* Fin de mod */
        if (datos == 1) {
            location.reload();
            // window.location.href = `${Traer_Server()}index.php/mensaje`
        } else if (datos == 2) {
            $("input").val("");
            MensajeConClase("Ingrese Usuario y Contraseña", "info", "Oops...");
            return true;
        } else if (datos == 33) {
            MensajeConClase("Usuario y/o contraseña incorrectos", "info", "Oops...");
            return true;
        } else if (datos == 3) {
            MensajeConClase("Usuario y/o contraseña incorrectos", "info", "Oops...");
            return true;
        } else if (datos == 5) {
            MensajeConClase(
                "El usuario no se encuentra habilitado para ingresar a AGIL, por favor contacte con el departamento de sistemas y solicite el acceso.",
                "info",
                "Oops..."
            );
            return true;
        } else if (data.resp == 1000) {
            //Aqui es donde envia el codigo de verificacion generado
            enviar_correo_personalizado(
                "Cal",
                data.mail_body,
                data.correo_recibe,
                data.nombre_recibe,
                "Codigo de verificación.",
                data.mail_title,
                "ParCodAdm",
                data.tipo
            );
            swal({
                title: "Atención!",
                text: "Para iniciar sesión, hemos enviado un código de verificación a su correo electrónico, digítelo en la casilla de texto a continuación:",
                type: "input",
                showCancelButton: true,
                confirmButtonColor: "#D9534F",
                confirmButtonText: "Verificar!",
                cancelButtonText: "Regresar!",
                allowOutsideClick: true,
                closeOnConfirm: false,
                closeOnCancel: true,
                inputPlaceholder: `Escriba el código aquí:`,
            },
                async function (mensaje) {
                    if (mensaje === false) return false;
                    if (mensaje === "") {
                        swal.showInputError(`Escriba el código por favor`);
                    } else {
                        //codigo para enviar el codigo de verificacion que se va a asignar en base de datos al usuario
                        let codigo = $("fieldset input[type='text']").val();
                        let resp = await check_validation_code(data.usuario, codigo);
                        if (!resp) swal.showInputError(`El código no es valido`);
                    }
                }
            );
            return true;
        } else if (datos == 4) {
            var a = document.createElement("a");
            a.target = "_blank";
            a.href = server + "politicadedatos.pdf";
            a.click();
            swal({
                title: "Politicas",
                text: "He leído y acepto la Política de Protección de Datos de la universidad de la costa CUC.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#D9534F",
                confirmButtonText: "Si, Aceptar!",
                cancelButtonText: "No, cancelar!",
                allowOutsideClick: true,
                closeOnConfirm: false,
                closeOnCancel: true,
            },
                function (isConfirm) {
                    if (isConfirm) {
                        acept = 1;
                        Logear();
                    }
                }
            );
            return true;
        } else {
            window.location = server;
        }
    });
};

function cerrar_sesion(tipo) {
    $.ajax({
        url: server + "index.php/pages/cerrar",
        type: "post",
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false,
    }).done(function (datos) {
        if (tipo == 1) {
            window.location = server;
        } else {
            window.location = server + "index.php/inactivo";
        }
    });
}

function Listar_perfiles_usuarios() {
    idperfil = 0;
    $.ajax({
        url: server + "index.php/genericas_control/Cargar_valor_Parametros_normal",
        dataType: "json",
        data: {
            idparametro: 17,
        },
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            $("#listado_perfiles").html("");

            $("#listado_perfiles").append(
                "<option value=''> Seleccione Perfil</option>"
            );
            for (var i = 0; i <= datos.length - 1; i++) {
                $("#listado_perfiles").append(
                    "<option  title='" +
                    datos[i].valorx +
                    "' data-toggle='popover' data-trigger='hover' value= " +
                    datos[i].id_aux +
                    ">" +
                    datos[i].valor +
                    "</option>"
                );
            }
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function Listar_permisos_perfiles_usuarios() {
    id_permisos_perfil = 0;

    $("#tabla_permisos_perfiles tbody").off("dblclick", "tr");
    $("#tabla_permisos_perfiles tbody").off("click", "tr");

    var myTable = $("#tabla_permisos_perfiles").DataTable({
        destroy: true,
        ajax: {
            url: server + "index.php/genericas_control/Listar_permisos_perfil",
            dataType: "json",
            data: {
                idperfil: idperfil,
            },
            type: "post",
            dataSrc: function (json) {
                if (json.length == 0) {
                    return Array();
                }
                return json.data;
            },
        },
        processing: true,

        columns: [{
            data: "indice",
        },
        {
            data: "id_actividad",
        },
        //{ "data": "agrega" },
        //{ "data": "elimina" },
        //{ "data": "modifica" },
        {
            data: "op",
        },
        ],
        language: idioma,
        dom: "Bfrtip",
        buttons: [{
            extend: "excelHtml5",
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: "Excel",
            className: "btn btn-success",
        },
        {
            extend: "csvHtml5",
            text: '<i class="fa fa-file-text-o"></i>',
            titleAttr: "CSV",
            className: "btn btn-default",
        },
        {
            extend: "pdfHtml5",
            text: '<i class="fa fa-file-pdf-o"></i>',
            titleAttr: "PDF",
            className: "btn btn-danger2",
        },
        ],
    });

    $("#tabla_permisos_perfiles tbody").on("click", "tr", function () {
        var data = myTable.row(this).data();
        id_permisos_perfil = data.id;
        $("#tabla_permisos_perfiles tbody tr").removeClass("warning");
        $(this).attr("class", "warning");
    });
    $("#tabla_permisos_perfiles tbody").on("dblclick", "tr", function () {
        var data = myTable.row(this).data();
    });
}

function iniciar_tabla_permisos_perfil() {
    var myTable = $("#tabla_permisos_perfiles").DataTable({
        destroy: true,
        language: idioma,
        dom: "Bfrtip",
        buttons: [{
            extend: "excelHtml5",
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: "Excel",
            className: "btn btn-success",
        },
        {
            extend: "csvHtml5",
            text: '<i class="fa fa-file-text-o"></i>',
            titleAttr: "CSV",
            className: "btn btn-default",
        },
        {
            extend: "pdfHtml5",
            text: '<i class="fa fa-file-pdf-o"></i>',
            titleAttr: "PDF",
            className: "btn btn-danger2",
        },
        ],
    });
}
//En este metodo Guardo los parametros que maneja el sistema
function registrar_Actividad_perfil() {
    //obtengo el formulario de registro de parametros
    var formData = new FormData(
        document.getElementById("Guardar_actividad_perfil")
    );
    formData.append("idperfil", idperfil);
    // Envio los datos a mi archivo PHP y le envio por get la funcion que va a realizar
    $.ajax({
        url: server + "index.php/genericas_control/guardar_actividad_perfil",
        type: "post",
        dataType: "html",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
    }).done(function (datos) {
        if (datos == "sin_session") {
            close();
            return;
        }
        //Recibo los datos del php
        //si es un quiere decir que los campos estan vacios
        if (datos == 1) {
            MensajeConClase("Seleccione Actividad", "info", "Oops...");
            return true;
            //si es dos es por que guardo el parametro
        } else if (datos == 2) {
            MensajeConClase(
                "Actividad Asignada Con exito",
                "success",
                "Proceso Exitoso!"
            );
            Listar_permisos_perfiles_usuarios();
            Listar_Actividades_Sin_Asignar_Perfil();

            return true;
            // si es tres es por que el nombre del parametro existe
        } else if (datos == -1302) {
            MensajeConClase(
                "No tiene Permisos Para Realizar Esta Opereacion",
                "error",
                "Oops..."
            );
        } else {
            MensajeConClase("Error al Asignar la actividad", "error", "Oops...");
        }
    });
}

function Listar_Actividades_Sin_Asignar_Perfil() {
    $.ajax({
        url: server +
            "index.php/genericas_control/Listar_Actividades_Sin_Asignar_Perfil",
        dataType: "json",
        data: {
            idperfil: idperfil,
        },
        type: "post",
        success: function (datos) {
            var sw = 0;

            if (datos == "sin_session") {
                close();
                return;
            }
            $("#cbx_Actividades").html("");
            $("#cbx_Actividades").append(
                "<option value=''>Seleccione Actividad</option>"
            );
            for (var i = 0; i <= datos.length - 1; i++) {
                if (datos[i].id_actividad == null) {
                    $("#cbx_Actividades").append(
                        "<option value=" +
                        datos[i].id_aux +
                        ">" +
                        datos[i].valor +
                        "</option>"
                    );
                    sw = 1;
                }
            }
            if (sw == 0) {
                $("#cbx_Actividades").html("");
                $("#cbx_Actividades").append(
                    "<option value=''>Sin Actividades Por Asignar</option>"
                );
            }

            $("#myModal").modal("show");
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function CambiarPermisos(id, estado, col) {
    if (estado == 1) {
        estado = 0;
    } else {
        estado = 1;
    }

    $.ajax({
        url: server + "index.php/genericas_control/Cambiar_estado_Permiso",
        dataType: "json",
        data: {
            id: id,
            estado: estado,
            col: col,
        },
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            if (datos == 4) {
                MensajeConClase(
                    "Permiso Modificado Con éxito",
                    "success",
                    "Proceso Exitoso!"
                );
                Listar_permisos_perfiles_usuarios();
            } else if (datos == -1302) {
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops..."
                );
            } else {
                MensajeConClase("Error al Cambiar el Permiso", "error", "Oops...");
            }
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function Administra_estado_Permiso(id) {
    $.ajax({
        url: server + "index.php/genericas_control/Administra_estado_Permiso",
        dataType: "json",
        data: {
            id: id,
        },
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            if (datos == 4) {
                MensajeConClase(
                    "Permisos Modificados Con éxito",
                    "success",
                    "Proceso Exitoso!"
                );
                Listar_permisos_perfiles_usuarios();
            } else if (datos == -1302) {
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops..."
                );
            } else {
                MensajeConClase("Error al Cambiar los Permisos", "error", "Oops...");
            }
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function AceptarCambioEstado(id, estado, col) {
    swal({
        title: "Estas Seguro .. ?",
        text: "Tener en cuenta que al Cambiar el Permiso, este afectara en las funciones del Perfil que esta Asignado",
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
                CambiarPermisos(id, estado, col);
            }
        }
    );
}

function Aceptar_administra_modulo(id) {
    swal({
        title: "Estas Seguro .. ?",
        text: "Tener en cuenta que al asignar todos los Permisos el perfil podra administrar toda la actividad",
        type: "warning",
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
                Administra_estado_Permiso(id);
            }
        }
    );
}

function Eliminar_Actividad(id) {
    $.ajax({
        url: server + "index.php/genericas_control/Eliminar_Actividad",
        dataType: "json",
        data: {
            id: id,
        },
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            if (datos == 4) {
                MensajeConClase(
                    "Actividad Retirada Con éxito",
                    "success",
                    "Proceso Exitoso!"
                );
                Listar_permisos_perfiles_usuarios();
            } else if (datos == -1302) {
                MensajeConClase(
                    "No tiene Permisos Para Realizar Esta Opereacion",
                    "error",
                    "Oops..."
                );
            } else {
                MensajeConClase("Error al Retirar la Actividad", "error", "Oops...");
            }
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function Confirmar_Retirar_Actividad(id) {
    swal({
        title: "Estas Seguro .. ?",
        text: "Tener en cuenta que al Retirar la Actividad afectara a los Usuarios con este Perfil Asignado",
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
                Eliminar_Actividad(id);
            }
        }
    );
}

function Datos_Cuenta() {
    $.ajax({
        url: server + "index.php/pages/obtener_datos_persona_usuario_session",

        dataType: "json",
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            if (datos.length == 0) {
                MensajeConClase(
                    "Error al Cargar Los datos del Usuario",
                    "error",
                    "Oops..."
                );
            } else {
                $("#nombre_cuenta").html(
                    datos[0].nombre +
                    " " +
                    datos[0].apellido +
                    " " +
                    datos[0].segundo_apellido
                );
                $("#identi_cuenta").html(datos[0].identificacion);
                $("#tipo_id_cuenta").html(datos[0].id_tipo_identificacion);
                $("#usuario_cuenta").html(datos[0].usuario);
                $("#perfil_cuenta").html(datos[0].id_perfil);
                $("#Modal-cuenta").modal("show");

            }
        },
        error: function () {
            console.log("Something went wrong", status, err);
        },
    });
}

function Permisos_Actividad_vista(actividad) {
    $.ajax({
        url: server + "index.php/pages/Permisos_perfil_vista",
        dataType: "json",
        data: {
            actividad,
        },
        type: "post",
        success: function (datos) {
            if (datos == -2) {
                close();
            } else {
                if (datos[0].agrega == 1) {
                    $(".btnAgregar").show("fast");
                } else {
                    $(".btnAgregar").css("display", "none");
                }
                if (datos[0].elimina == 1) {
                    $(".btnElimina").show("fast");
                } else {
                    $(".btnElimina").css("display", "none");
                }
                if (datos[0].modifica == 1) {
                    $(".btnModifica").show("fast");
                } else {
                    $(".btnModifica").css("display", "none");
                }
                Mostrar_Contenido();
            }
        },
        error: function () {
            close();
            console.log("Something went wrong", status, err);
        },
    });
}
var inactivityTime = function () {
    //return false;
    var t;
    window.onload = resetTimer;
    // DOM Events
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;

    function logout() {
        cerrar_sesion(2);
    }

    function resetTimer() {
        clearTimeout(t);
        t = setTimeout(logout, 86400000);
        // 1000 milisec = 1 sec
    }
};

function cambiar_perfil(perfil) {
    $.ajax({
        url: server + "index.php/pages/cambiar_perfil",
        dataType: "json",
        data: {
            perfil,
        },
        type: "post",
        success: function (datos) {
            if (datos == "sin_session") {
                close();
                return;
            }
            if (datos == true) {
                location.reload();
            } else {
                MensajeConClase(
                    "No es posible cambiar al perfil seleccionado.",
                    "info",
                    "Oops.!"
                );
            }
        },
        error: function (err) {
            console.log("Something went wrong", status, err);
        },
    });
}



function detallePersona() {
    $.ajax({
        url: `${server}index.php/personas_control/obtener_persona_sesion`,
        dataType: "json",
        type: "post",
        success: function (datos) {
            let perfil = datos[0];
            let perfiles = datos[1];

            const combo = $("#perfiles");
            combo.html("");

            for (let i = 0; i <= perfiles.length - 1; i++) {
                if (perfil == perfiles[i].id_perfil) {
                    combo.append(`<option value="${perfiles[i].id_perfil}" selected='selected'>${perfiles[i].perfil}</option>`);
                }
                if (perfil != perfiles[i].id_perfil) {
                    combo.append(`<option value="${perfiles[i].id_perfil}" >${perfiles[i].perfil}</option>`);
                }
            }
        },
    });
}



const perfilEnSesion = (id_perfil) => {

    swal({
        title: "Estas Seguro .. ?",
        text: "Tener en cuenta que al Cambiar el perfil se Mostrarán los modulos a los que tiene acceso el Perfil Elegido",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#920FBF",
        confirmButtonText: "Si, Cambiar!",
        cancelButtonText: "No, cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true,
    },

        function (isConfirm) {

            if (isConfirm) {

                $.ajax({
                    url: server + "index.php/personas_control/obtener_datos_usuario",
                    dataType: "json",
                    type: "post",
                    success: function (datos) {
                        if (datos == "sin_session") {
                            close();
                            return;
                        }
                        if (datos.length == 0) {
                            MensajeConClase(
                                "Error al Cargar Los datos del Usuario",
                                "error",
                                "Oops..."
                            );
                        } else {
                            var formData = new FormData();
                            formData.append("id", datos[0].id);
                            formData.append("identificacion", datos[0].identificacion);
                            formData.append("id_perfil", id_perfil);

                            $.ajax({
                                url: `${server}index.php/personas_control/perfilEnSesion`,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                            }).done((data) => {
                                switch (data) {
                                    case -1000:
                                        close();
                                        break;
                                    case 4:
                                        perfil_activo = id_perfil;
                                        window.location.href = `${Traer_Server()}`

                                        break;
                                    case -1302:
                                        MensajeConClase(
                                            "No tiene Permisos Para Realizar Esta Opereacion",
                                            "error",
                                            "Oops.!"
                                        );
                                    default:
                                        MensajeConClase(
                                            "Error al Cambiar de perfil",
                                            "error",
                                            "Oops.!"
                                        );
                                        break;

                                }
                            });
                        }
                    },
                    error: function () {
                        console.log("Something went wrong", status, err);
                    },
                });
            } else {
                detallePersona()
            }

        }
    );
};

const traer_perfil_activo = (perfil, mensaje = '') => {
    perfil_activo = perfil;
    if (mensaje) $("#modal_informacion_app").modal()
}