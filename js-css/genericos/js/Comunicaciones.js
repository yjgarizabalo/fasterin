let data_solicitante = { 'nombre': null, 'correo': null };
let url = `${Traer_Server()}index.php/comunicaciones_control/`;
let id_tipo_solicitud = null;
let errores = [];
let cargados = 0;
let id_solicitud = null;
let data_select = null;
let ruta_archivos_solicitudes = "archivos_adjuntos/comunicaciones/solicitudes/";
let servicios = [];
let solicitud_modi = null;
let cont_servicio = { 'normales': 0, 'especiales': 0, 'staff': 0, 'diseno': 0 };
let persona_sesion = null;
let estado_gestion = '';
let enviar_adjuntos = [];
let tipo_adj = 2;
let sw_alertas = false;
let tiempo = { 'tiempo_demora': '----', 'tiempo_gestion': '----' };
let id_servicio_detalle = null;
let data_detalle_ser = null;
let tipo_eje = 1;
$(document).ready(() => {
    $('#agregar_evento').click(() => {
        administrar_modulo('agregar_evento');
    });
    $('#req_diseno').click(function () {
        if ($(this).is(':checked')) {
            $("#cont_diseno").show("slow");
            $("#cont_diseno textarea").attr("required", "true");
            $("#cont_diseno input").attr("required", "true");
            $("#cont_adjuntos").show('fast');
        } else {
            $("#cont_diseno").hide("slow");
            $("#cont_diseno input").removeAttr("required", "true");
            $("#cont_diseno textarea").removeAttr("required", "true");
            $("#cont_adjuntos").hide('fast');
        }
    });
    $('#agregar_divulgacion').click(() => {
        administrar_modulo('agregar_divulgacion');
        $("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    });
    $('#agregar_publicidad').click(() => {
        administrar_modulo('agregar_publicidad');
        $("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    });
    $('#agregar_cubrimiento').click(() => {
        administrar_modulo('agregar_cubrimiento');
        $("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    });
    $('#listado_solicitudes').click(() => {
        administrar_modulo('listado_solicitudes');
        $("#modal_crear_filtros input").val('');
        $("#modal_crear_filtros select").val('');
        sw_alertas = false;
        listar_solicitud();
        $("#modal_enviar_archivos #footermodal").html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');

    });
    $(".regresar_menu").click(function () {
        $("#container-listado-eventos").css("display", "none");
        $("#menu_principal").fadeIn(1000);
    });
    $("#form_agregar_solicitud").submit(() => {
        guardar_solicitud(-1);
        return false;
    });
    $("#form_detalle_servicio").submit(() => {
        validar_detalle_servicio();
        return false;
    });
    $("#form_guardar_calificacion").submit(() => {
        guardar_encuesta_solicitud(id_solicitud);
        return false;
    });
    $("#conadjuntos").click(function () {
        removerPintarAdjunto("remover");
        $("#modal_enviar_archivos").modal("show");
    });
    $("#conadjuntos_modificar").click(function () {
        removerPintarAdjunto("remover");
        $("#modal_enviar_archivos").modal("show");
    });
    $("#con_adjuntos_rev").click(function () {
        listar_archivos_adjuntos(id_solicitud, 1);
        $("#modal_listar_archivos_adjuntos").modal("show");
    });
    $("#agregar_servicios").click(function () {
        let sw = true;
        /*if (!$("#presu1").is(':checked') && id_tipo_solicitud == 'Com_Env') {
            let fecha_inicial = $("#form_agregar_solicitud input[name = fecha_inicio_evento]").val();
            let fecha_final = $("#form_agregar_solicitud input[name = fecha_fin_evento]").val();
            if (fecha_final.length == 0 || fecha_inicial.length == 0) {
                MensajeConClase('Antes de continuar debe seleccionar las fechas del evento.', 'info', 'Oops.!');
                sw = false;
            }
        }*/
        if (sw) {
            listar_servicios();
            removerPintar("remover");
            $("#modal_servicios").modal("show");
        }

    });

    $("#agregar_servicios_nuevos").click(async function () {
        let { presupuesto, id_estado, id_tipo_solicitud } = data_select;
        if ((id_estado != 'Com_Sol_E') || (id_tipo_solicitud == 'Com_Env' && presupuesto == 0)) MensajeConClase('No es posible agregar mas servicios para este tipo de eventos', 'info', 'Oops.!');
        else {
            servicios = await obtener_servicios_nuevos(id_solicitud);
            listar_servicios();
            removerPintar();
            $("#modal_servicios").modal("show");
        }
    });

    $("#agregar_adjuntos_nuevos").click(function () {
        $("#modal_enviar_archivos").modal("show");
        removerPintarAdjunto();
    });
    $("#ver_adjuntos_lista").click(function () {
        listar_archivos_adjuntos(id_solicitud, 2);
        $("#modal_listar_archivos_adjuntos").modal("show");
    });
    $("#form_modi_solicitud").submit(() => {
        modificar_solicitud();
        return false;
    });
    $("#cbx_cat_divulgacion").change(() => {
        let tipo = $("#cbx_cat_divulgacion").val();
        cargar_mensajes_alert(63, tipo, "#info_categoria")
    });
    $(".cbxrefrigerios").change(() => {
        let tipo = $(".cbxrefrigerios").val();
        cargar_mensajes_alert(28, tipo, "#info_refrigerio")
    });
    // $('#btn_modificar').click(() => {
    //     if (id_solicitud == null) MensajeConClase('Seleccione solicitud a modificar', 'info', 'Oops.!');
    //     else if (data_select.id_estado != 'Com_Sol_E') MensajeConClase('No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.', 'info', 'Oops.!');
    //     else if (data_select.presupuesto == 0 && data_select.id_tipo_solicitud == 'Com_Env') MensajeConClase('Esta acción no se encuentra disponible para las solicitudes sin presupuesto.', 'info', 'Oops.!');
    //     else consulta_solicitud_id(id_solicitud);
    // });
    $("#ver_estados").click(function () {
        let { tiempo_demora, tiempo_gestion } = tiempo;
        tiempo_demora = tiempo_demora == null ? '----' : tiempo_demora;
        tiempo_gestion = tiempo_gestion == null ? '----' : tiempo_gestion;
        listar_estados(id_solicitud, tiempo_demora, tiempo_gestion);
        $("#modal_listar_estados").modal("show");
    });
    $("#limpiar_filtros").click(function () {
        $("#modal_crear_filtros input").val('');
        $("#modal_crear_filtros select").val('');
        sw_alertas = false;
        listar_solicitud();
    });

    $("#form_gestionar_solicitud").submit(() => {
        let diseno = $("#form_gestionar_solicitud input[name='diseno']").is(':checked') ? 1 : 0;
        let correo = $("#form_gestionar_solicitud input[name='correo']").val();
        let mensaje = $("#form_gestionar_solicitud textarea[name='descripcion']").val();
        gestionar_solicitud(id_solicitud, estado_gestion, correo, mensaje, diseno);
        return false;
    });
    $("#btn_filtrar").click(() => {
        listar_solicitud();
    });

    /* $("#presu1").click(function () {
         servicios = [];
         pasar_data_servicios(id_tipo_solicitud);
         mostrar_codigo_sap('show');
         configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f1');
 
     });*/
    /*$("#presu2").click(function () {
        servicios = [];
        servicios_con_mant();
        mostrar_codigo_sap('hide');
        configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f4');

    });*/
    $('#btn_notificaciones_com').click(() => {
        mostrar_solicitudes_terminadas_ext();
        $("#modal_notificaciones").modal();
    });
});
const administrar_modulo = async tipo => {
    $("#form_agregar_solicitud").get(0).reset();
    $("#info_categoria").hide('fast');
    $(".presupuesto").css('display', 'none');
    //$("#presu2").prop('checked', true)
    if (tipo == 'solicitudes') {
        $("#menu_principal").css("display", "none");
    } else if (tipo == 'menu') {
        $("#menu_principal").fadeIn(1000);
    } else if (tipo == 'agregar_evento') {
        id_tipo_solicitud = 'Com_Env';
        $(".presupuesto").show();
        //servicios_con_mant();
        pasar_data_servicios(id_tipo_solicitud);
        mostrar_codigo_sap('show');
        configurar_form_guardar('show');
        configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f1');
        //mostrar_codigo_sap('hide');
        //configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f4');
        $("#modal_agregar_solicitud").modal();
    } else if (tipo == 'agregar_divulgacion') {
        id_tipo_solicitud = 'Com_Div';
        pasar_data_servicios(id_tipo_solicitud);
        configurar_form_guardar('show');
        mostrar_codigo_sap('hide');
        configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f1');
        $("#modal_agregar_solicitud").modal();
    } else if (tipo == 'agregar_publicidad') {
        id_tipo_solicitud = 'Com_Pub';
        pasar_data_servicios(id_tipo_solicitud);
        configurar_form_guardar('show');
        mostrar_codigo_sap('show');
        configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f2');
        $("#modal_agregar_solicitud").modal();
    } else if (tipo == 'agregar_cubrimiento') {
        id_tipo_solicitud = 'Com_Cub';
        pasar_data_servicios(id_tipo_solicitud);
        configurar_form_guardar('show');
        mostrar_codigo_sap('hide');
        configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f3');
        $("#modal_agregar_solicitud").modal();
    } else if (tipo == 'listado_solicitudes') {
        $("#menu_principal").css("display", "none");
        $("#container-listado-eventos").fadeIn(1000);

    }
}

const configurar_form_guardar = (estado, categoria = '#form_agregar_solicitud select[name="id_categoria_divulgacion"]') => {
    if (estado == 'show') {
        if (id_tipo_solicitud == 'Com_Env' || id_tipo_solicitud == 'Com_Div' || id_tipo_solicitud == 'Com_Cub') {
            $(`#nombre_evento`).show("fast");
            $(`#nombre_evento input`).attr("required", "true");
        } else {
            $(`#nombre_evento`).hide("fast");
            $(`#nombre_evento input`).removeAttr("required", "true");
        }
    }
    if (id_tipo_solicitud == 'Com_Div') {
        $(`${categoria}`).show("fast");
        $(`${categoria}`).attr("required", "true");
    } else {
        $(`${categoria}`).hide("fast");
        $(`${categoria}`).removeAttr("required", "true");
    }
}

const mostrar_codigo_sap = (estado, sap = '#form_agregar_solicitud input[name="id_codigo_sap"]') => {
    if (estado == 'show') {
        $(`${sap}`).show("fast");
        $(`${sap}`).attr("required", "true");
    } else {
        $(`${sap}`).val("").hide("fast");
        $(`${sap}`).removeAttr("required", "true");
    }
}

const guardar_solicitud = (confirm) => {
    MensajeConClase("validando info", "add_inv", "Oops...");
    let fordata = new FormData(document.getElementById("form_agregar_solicitud"));
    let data = formDataToJson(fordata);
    data.id_tipo_solicitud = id_tipo_solicitud;
    data.servicios = servicios;
    data.cont_servicio = cont_servicio;
    data.confirm = confirm;
    consulta_ajax(`${url}guardar_solicitud`, data, (resp) => {
        let { titulo, mensaje, tipo, solicitud } = resp;
        if (tipo == 'success') {
            $("#id_solicitud").val(solicitud.id);
            if (num_archivos != 0) {
                tipo_cargue = 1;
                myDropzone.processQueue();
            } else {
                MensajeConClase(mensaje, tipo, titulo);
            }
            enviar_correo_estado('Com_Sol_E', solicitud.id, '');
            listar_solicitud();
            $("#form_agregar_solicitud").get(0).reset();
            $("#info_categoria").hide('fast');
            $("#form_agregar_solicitud textarea[name=descripcion]").removeAttr("required", "true");
            $("#modal_agregar_solicitud").modal('hide');
            pasar_data_servicios(id_tipo_solicitud);
            listar_servicios();
        } else if (tipo == 'confirm') {
            confirmar_add_solicitud(mensaje);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }

    });
}

const guardar_servicios_nuevos = (id_codigo_sap = '') => {
    consulta_ajax(`${url}guardar_servicios_nuevos`, { servicios, id_solicitud, id_codigo_sap }, async (resp) => {
        let { titulo, mensaje, tipo, refres, sw, data } = resp;
        if (tipo == 'success') {
            if (data) ver_detalle_solicitud(data);
            else listar_servicios_solicitud(id_solicitud);
            $("#modal_servicios").modal('hide');
        }
        if (refres) {
            servicios = await obtener_servicios_nuevos(id_solicitud);
            listar_servicios();
        }
        if (sw) {
            swal({
                title: "Importante !",
                text: mensaje,
                type: "input",
                showCancelButton: true,
                confirmButtonColor: "#D9534F",
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                allowOutsideClick: true,
                closeOnConfirm: false,
                closeOnCancel: true,
                inputPlaceholder: "Ingrese Codigo Sap"
            }, async function (id_codigo_sap) {
                if (id_codigo_sap === false)
                    return false;
                if (id_codigo_sap === "") {
                    swal.showInputError("Debe Ingresar el Codigo Sap.!");
                } else {
                    let resp = await validar_codigo_sap(id_codigo_sap);
                    let { titulo, mensaje, tipo, id } = resp;
                    if (tipo == 'success') guardar_servicios_nuevos(id);
                    else MensajeConClase(mensaje, tipo, titulo);
                    return false;
                }
            });
            return;
        }
        MensajeConClase(mensaje, tipo, titulo);

    });
}
const validar_codigo_sap = (id_codigo_sap) => {
    return new Promise(resolve => {
        consulta_ajax(`${url}validar_codigo_sap`, { id_codigo_sap }, (resp) => {
            resolve(resp)
        });
    })
}
const listar_solicitud = (id = '') => {

    let tipo = $("#tipo_sol_filtro").val();
    let estado = $("#estado_filtro").val();
    let fecha = $("#fecha_filtro").val();
    id_solicitud = null;
    tiempo = { 'tiempo_demora': '----', 'tiempo_gestion': '----' };
    $('#tabla_solicitudes tbody')
        .off('dblclick', 'tr')
        .off('click', 'tr')
        .off('click', 'tr td:nth-of-type(1)')
        .off('click', 'tr .tramitar')
        .off('click', 'tr .negar')
        .off('click', 'tr .cancelar')
        .off('click', 'tr .finalizar')
        .off('click', 'tr .encuesta')
        .off('click', 'tr .revision')
        .off('click', 'tr .aceptar')
        .off('click', 'tr .descartar')
        .off('click', 'tr .adjuntar')
        .off('click', 'tr .copiar')
        .off('click', 'tr .estados')
        .off('click', 'tr .correcion')
        .off('click', 'tr .editar')
        .off('click', 'tr .enviar');
    consulta_ajax(`${url}listar_solicitud`, { id, tipo, estado, fecha }, (resp) => {
        let { solicitudes, alertas } = resp;
        let mostrar = !sw_alertas ? solicitudes : alertas;
        const table = $("#tabla_solicitudes").DataTable({
            "destroy": true,
            "processing": true,
            'data': mostrar,
            "columns": [
                {
                    "render": function (data, type, full, meta) {
                        let { id_estado_solicitud } = full;
                        let stilo = '';
                        if (id_estado_solicitud == 'Com_Sol_E') stilo = 'background-color:white;color: black;'
                        else if (id_estado_solicitud == 'Com_Ent_E' || id_estado_solicitud == 'Com_Rev_E' || id_estado_solicitud == 'Com_Ace_E') stilo = 'background-color:#f0ad4e;color: white;'
                        else if (id_estado_solicitud == 'Com_Can_E') stilo = 'background-color:#d9534f;color: white;'
                        else if (id_estado_solicitud == 'Com_Rec_E') stilo = 'background-color:#d9534f;color: white;'
                        else if (id_estado_solicitud == 'Com_Fin_E') stilo = 'background-color:#39B23B;color: white;'
                        else if (id_estado_solicitud == 'Com_Des_E') stilo = 'background-color:#6e1f7c;color: white;'
                        else if (id_estado_solicitud == 'Com_Cor_E') stilo = 'background-color:#2E79E5;color: white;'
                        return `<span  style="${stilo}width: 100%; ;" class="pointer form-control" id="ver_lista" ><span >ver</span></span>`;
                    }
                },
                {
                    "data": "tipo_solicitud"
                },
                {
                    "data": "solicitante"
                },
                {
                    "data": "fecha_registra"
                },
                {
                    "data": "estado_solicitud"
                },
                {
                    "render": function (data, type, full, meta) {
                        let { id_estado_solicitud, id_usuario_registra, calificacion, id_tipo_solicitud, tiempo_gestion, tiempo_demora, estado_ext, id_categoria_divulgacion, id } = full;
                        let gestion = 'Pendiente';
                        if (tiempo_demora != null) {
                            if (parseInt(tiempo_demora) > (tiempo_gestion)) gestion = 'Fuera de Tiempo';
                            else gestion = 'OK';
                        }
                        let { perfil, persona } = persona_sesion;
                        let resp = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                        if (id_estado_solicitud == 'Com_Sol_E') {
                            tramitar = `<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="pointer fa fa-retweet btn btn-default tramitar" ></span>`;
                            negar = `<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                            finalizar = `<span title="Finalizado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default finalizar"></span>`;
                            cancelar = `<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-remove btn btn-default cancelar"></span>`;
                            correccion = `<span title="Enviar a Corrección" data-toggle="popover" data-trigger="hover" style="color:#DBAA04;margin-left: 5px"class="pointer fa fa-send btn btn-default correcion"></span>`;
                            if (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com') {
                                resp = id_categoria_divulgacion == 'Req_Vis' ? `${finalizar} ${correccion} ${negar}` : `${tramitar} ${correccion} ${negar}`;
                                if (perfil == 'Per_Admin' || persona == id_usuario_registra) {
                                    resp = `${resp} ${cancelar}`
                                }
                            } else {
                                resp = cancelar;
                            }
                        } else if (id_estado_solicitud == 'Com_Ent_E') {
                            resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            if (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com') {
                                if (id_tipo_solicitud == 'Com_Div' && id_categoria_divulgacion != 'Req_Vid') resp = `<span title="Revisión" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="pointer fa fa-reply btn btn-default revision"></span> <span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                                else resp = `<span title="Finalizado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default finalizar"></span> <span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                            }
                        } else if (id_estado_solicitud == 'Com_Fin_E') {
                            if (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com') {
                                if (parseInt(tiempo_demora) > parseInt(tiempo_gestion)) resp = `<span title="Gestion Fuera de Tiempo" data-toggle="popover" data-trigger="hover" style="color: #d9534f;margin-left: 5px"class="fa fa-thumbs-down btn btn-default estados"></span>`;
                                else resp = `<span title="Gestionada a Tiempo" data-toggle="popover" data-trigger="hover" style="color: #39B23B;margin-left: 5px"class="fa fa-thumbs-up btn btn-default estados"></span>`;
                            }
                            if ((perfil == 'Per_Admin' || persona == id_usuario_registra) && calificacion == null) resp = `${resp} <span title="Encuesta" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;margin-left: 5px"class="pointer fa fa-star btn btn-default encuesta"></span>`;
                        } else if (id_estado_solicitud == 'Com_Rev_E') {
                            resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            if (perfil == 'Per_Admin' || persona == id_usuario_registra) resp = ` <span title="Aceptar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-thumbs-up btn btn-default aceptar" ></span> <span title="Descartar" data-toggle="popover" data-trigger="hover" style="color: #d9534f;margin-left: 5px"class="pointer fa fa-thumbs-down btn btn-default descartar"></span> `;
                        } else if (id_estado_solicitud == 'Com_Ace_E') {
                            resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            if (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com') resp = `<span title="Finalizado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default finalizar"></span> <span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                        } else if (id_estado_solicitud == 'Com_Des_E') {
                            resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            if (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com') resp = ` <span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="pointer fa fa-retweet btn btn-default tramitar" ></span> <span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                        } else if (id_estado_solicitud == 'Com_Cor_E'){
                            resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            if (perfil == 'Per_Admin' || persona == id_usuario_registra)  resp = ` <span title="Editar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="pointer fa fa-edit btn btn-default editar" ></span> <span title="Enviar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-send btn btn-default enviar"></span>`;                            
                        } else if (id_estado_solicitud == 'Com_Rec_E' || id_estado_solicitud == 'Com_Can_E') {
                            if (persona == id_usuario_registra) {
                                resp = `<span title="Copiar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #d9534f;margin-left: 5px"class="pointer fa fa-copy btn btn-default copiar" ></span>`;
                            }
                        }
                        if (estado_ext != null && id_estado_solicitud == 'Com_Ent_E' && (perfil == 'Per_Admin' || perfil == 'Per_Admin_Com')) {
                            if (estado_ext == 'Sol_Apro') {
                                resp = `<span title="Finalizado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default finalizar"></span> <span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                            } else if (estado_ext == 'Man_Fin' || estado_ext == 'Man_Eje') {
                                resp = `<span title="Finalizado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default finalizar"></span>`;
                            } else if (estado_ext == 'Sol_Den' || estado_ext == 'Man_Rec' || estado_ext == 'Man_Can') {
                                resp = `<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default negar"></span>`;
                            } else {
                                resp = `<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>`;
                            }
                        }
                        return `${resp}${id_estado_solicitud == 'Com_Fin_E' ? id : ''}<span style='display:none'>${gestion}</span>`;
                    }

                }, {
                    "data": "tiempo_gestion"
                },
                {
                    "data": "tiempo_demora"
                },
                {
                    "data": "calificacion"
                },
                {
                    "data": "obs_califica"
                },
            ],
            "language": get_idioma(),
            'dom': 'Bfrtip',
            "buttons": get_botones(),
        });

        table.column(6).visible(false);
        table.column(7).visible(false);
        table.column(8).visible(false);
        table.column(9).visible(false);

        //EVENTOS DE LA TABLA ACTIVADOS
        $('#tabla_solicitudes tbody').on('click', 'tr', function () {
            let { id, id_estado_solicitud, solicitante, correo, presupuesto, id_tipo_solicitud } = table.row(this).data();
            id_solicitud = id;
            data_solicitante = { 'nombre': solicitante, correo }
            data_select = { 'id_estado': id_estado_solicitud, presupuesto, id_tipo_solicitud };
            $("#tabla_solicitudes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            tipo_eje = 1;
        });
        $('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
            let data = table.row(this).data();
            ver_detalle_solicitud(data);
        });
        $('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
            let data = table.row($(this).parent()).data();
            ver_detalle_solicitud(data);
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .tramitar', function () {
            let { id, id_tipo_solicitud } = table.row($(this).parent()).data();
            $("#form_gestionar_solicitud").get(0).reset();
            $("#cont_diseno").hide("slow");
            $("#cont_diseno input").removeAttr("required", "true");
            $("#cont_diseno textarea").removeAttr("required", "true");
            // gestionar_solicitud(id, 'Com_Ent_E');
            gestionar_solicitud(id, 'Com_Ent_E', '', '', diseno = 0, id_tipo_solicitud);
        });
        $('#tabla_solicitudes tbody').on('click', 'tr .negar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Rec_E');
        });
        $('#tabla_solicitudes tbody').on('click', 'tr .cancelar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Can_E');
        });
        $('#tabla_solicitudes tbody').on('click', 'tr .finalizar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Fin_E');
        });
        $('#tabla_solicitudes tbody').on('click', 'tr .encuesta', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Enc_E');
        });
        $('#tabla_solicitudes tbody').on('click', 'tr .revision', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Rev_E');
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .aceptar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Ace_E');
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .descartar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Des_E');
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .copiar', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Cop_E');
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .estados', function () {
            let { id, tiempo_demora, tiempo_gestion } = table.row($(this).parent()).data();
            listar_estados(id, tiempo_demora, tiempo_gestion);
            $("#modal_listar_estados").modal("show");
        });
        
        $('#tabla_solicitudes tbody').on('click', 'tr .correcion', function () {
            let { id } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Cor_E');
        });
        
        $('#tabla_solicitudes tbody').on('click', 'tr .editar', function () {
            let { id } = table.row($(this).parent()).data();
            consulta_solicitud_id(id)
        });

        $('#tabla_solicitudes tbody').on('click', 'tr .enviar', function () {
            let { id, id_estado_solicitud } = table.row($(this).parent()).data();
            gestionar_solicitud(id, 'Com_Sol_E', '', '', 0,'',id_estado_solicitud);
        });
        
        const con_filtros = (estado, fecha, id, tipo) => {
            if (estado.length != 0 || fecha.length != 0 || tipo.length != 0 || id.length != 0) {
                $(".mensaje-filtro").show("fast");
                $("#mensaje_filtro").html(`La tabla tiene algunos filtros aplicados.`);
            } else if (alertas.length > 0) {
                $("#mensaje_filtro").html(`Algunas solicitudes estan por vencerse. <a id='ver_alertas' href='#'>VER</a>`);
                $(".mensaje-filtro").show('fast');
                $("#ver_alertas").off('click');
                $("#ver_alertas").click(function () {
                    $("#modal_crear_filtros input").val('');
                    $("#modal_crear_filtros select").val('');
                    sw_alertas = true;
                    listar_solicitud();
                });
            } else {
                $(".mensaje-filtro").css("display", "none");
            }
        }
        con_filtros(estado, fecha, id, tipo);
    });
}
const ver_detalle_solicitud = (datos, footer = '<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>') => {
    tipo_adj = 2;
    let { id_codigo_sap,
        descripcion,
        tipo_solicitud,
        estado_solicitud,
        id_estado_solicitud,
        solicitante,
        tipo_evento,
        nro_invitados,
        telefono,
        direccion,
        nombre_lugar,
        fecha_registra,
        fecha_fin_evento,
        fecha_inicio_evento,
        id_tipo_solicitud,
        categoria_divulgacion,
        msj_negado,
        msj_tramite,
        correo_tramita,
        calificacion,
        obs_califica,
        fecha_califica,
        nombre_evento,
        id,
        tiempo_gestion,
        tiempo_demora,
        presupuesto,
        correo } = datos;
    tiempo = { tiempo_gestion, tiempo_demora }
    listar_servicios_solicitud(id);
    $(".id_codigo_sap").html(id_codigo_sap);
    $(".descripcion").html(descripcion);
    $(".tipo_solicitud").html(tipo_solicitud);
    $(".estado_solicitud").html(estado_solicitud);
    $(".solicitante").html(solicitante);
    $(".tipo_evento").html(tipo_evento);
    $(".nro_invitados").html(nro_invitados);
    $(".telefono").html(telefono);
    $(".direccion").html(direccion);
    $(".nombre_lugar").html(nombre_lugar);
    $(".fecha_registra").html(fecha_registra);
    $(".fecha_fin_evento").html(fecha_fin_evento);
    $(".fecha_inicio_evento").html(fecha_inicio_evento);
    $(".categoria_divulgacion").html(categoria_divulgacion);
    $(".msj_negado").html(msj_negado);
    $(".msj_tramite").html(msj_tramite);
    $(".correo_diseno").html(correo_tramita);
    $(".nombre_evento").html(nombre_evento);
    $(".calificacion").html(calificacion == 1 ? 'Satisfecho' : 'No Satisfecho');
    $(".obs_califica").html(obs_califica);
    $(".fecha_califica").html(fecha_califica);

    if ((id_estado_solicitud != 'Com_Sol_E') || (id_tipo_solicitud == 'Com_Env' && presupuesto == 0)) $("#agregar_servicios_nuevos").hide();
    else $("#agregar_servicios_nuevos").show();

    if (id_codigo_sap) $(".oculto_cs").show();
    else $(".oculto_cs").hide();
    if (nombre_evento) $(".oculto_nombre").show();
    else $(".oculto_nombre").hide();

    if (id_tipo_solicitud == "Com_Div") {
        $(".oculto_d").hide();
        $(".mostrar_d").show();
    } else if (id_tipo_solicitud == "Com_Env" || id_tipo_solicitud == "Com_Pub" || id_tipo_solicitud == "Com_Cub") {
        $(".oculto_d").show();
        $(".mostrar_d").hide();
    }

    if (id_estado_solicitud == "Com_Rec_E") $(".tr_msj_negado").show();
    else $(".tr_msj_negado").hide();
    if (correo_tramita != null) $(".tr_diseno").show();
    else $(".tr_diseno").hide();
    if (calificacion != null) $(".tr_califica").show();
    else $(".tr_califica").hide();
    $("#modal_detalle_solicitud").modal();
    $("#modal_detalle_solicitud .modal-footer").html(footer);
}
let recibir_archivos = () => {
    Dropzone.options.Subir = {
        url: `${url}recibir_archivos`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
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
        params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
        clickable: true,
        ignoreHiddenFiles: true,
        acceptedFiles: "image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv,.gz,.gzip,.rar,.zip,.ppt,.pptx", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
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
                    } else if (tipo_cargue == 2) {
                        MensajeConClase("La solicitud fue modificada con exito, pero ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
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
                } else if (tipo_cargue == 2) {
                    MensajeConClase("La solicitud fue modificada con exito, pero algunos Archivos No fueron cargados:\n\n" + errorlist + "\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
                } else {
                    MensajeConClase("Algunos Archivos No fueron cargados:\n\n" + errorlist + "\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
                }

            } else {
                if (tipo_cargue == 1) {
                    MensajeConClase("La solicitud fue Guardada con exito y Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
                } else if (tipo_cargue == 2) {
                    MensajeConClase("La solicitud fue modificada con exito y Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
                } else {
                    MensajeConClase("Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
                    $("#modal_enviar_archivos").modal('hide');
                    listar_archivos_adjuntos(id_solicitud, tipo_adj);
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
                        return "<a class='sin-decoration ' href='" + Traer_Server() + ruta_archivos_solicitudes + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
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
const pasar_data_servicios = async (id, tipo = '', con_aux = '') => {
    servicios = await obtener_servicios(id, tipo, con_aux);
}
const obtener_servicios = (id, tipo = '', con_aux = '') => {
    return new Promise(resolve => {
        consulta_ajax(`${url}listar_servicios`, { id, tipo, con_aux }, (resp) => {
            resolve(resp)
        });
    });
}
const obtener_servicios_nuevos = (id, con_aux = '') => {
    return new Promise(resolve => {
        consulta_ajax(`${url}listar_servicios_nuevos`, { id, con_aux }, (resp) => {
            resolve(resp)
        });
    });
}
const obtener_servicios_mantenimiento = () => {
    return new Promise(resolve => {
        consulta_ajax(`${url}listar_servicios_mantenimiento`, '', (resp) => {
            resolve(resp)
        });
    });
}

const listar_servicios = () => {
    id_servicio_detalle = null;
    $('#tabla_servicios tbody').off('click', 'tr .asignar').off('click', 'tr').off('click', 'tr .retirar').off('click', 'tr .ver_detalle_servicio');
    let i = 0;
    const table = $("#tabla_servicios").DataTable({

        "destroy": true,
        "processing": true,
        'data': servicios,
        "columns": [
            {
                "render": function (data, type, full, meta) {
                    let { id_aux, tipo_solicitud, valory } = full;
                    let disable = ' ';
                    let clase = 'ver_detalle_servicio';
                    if (tipo_solicitud == 'Com_Env') {
                        if (id_aux == null) { disable = 'disabled="disabled"'; clase = ' ' }
                        else if(id_aux == 'Ser_Staff') { clase = 'ver_detalle_servicio' }
                        else if(valory == 'Com') { disable = 'disabled="disabled"'; clase = ' ' };
                        return `<span style="width: 100%;" class="pointer form-control ${clase}" ${disable} ><span>Ver</span></span>`;
                    }else {
                        i++;
                        return i;
                    }
                }
            },
            {
                "data": "nombre"
            },
            {
                "render": function (data, type, full, meta) {
                    let { estado } = full;
                    let resp = '';
                    if (estado == 0) resp = '<span style="color:green" class="btn btn-default pointer fa fa-toggle-off asignar"></span>';
                    else resp = '<span style="color:green" class="btn btn-default pointer fa fa-toggle-on retirar"></span>';
                    return resp;
                }
            },
        ],
        "language": idioma,
        dom: 'Bfrtip',
        "buttons": []

    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $('#tabla_servicios tbody').on('click', 'tr', function () {
        $("#tabla_servicios tbody tr").removeClass("warning");
        $(this).attr("class", "warning");

    });
    $('#tabla_servicios tbody').on('click', 'tr .retirar', function () {
        let { id, id_tipo_solicitud, tipo_solicitud, valory, id_aux } = table.row($(this).parent().parent()).data();
        let servicio = servicios.find((elemento) => {
            return elemento.id == id;
        })
        servicio.estado = 0;
        servicio.id_tipo = '';
        servicio.id_tipo_entrega = '';
        servicio.cantidad = '';
        servicio.cobservaciones = '';
        //if (tipo_solicitud == 'Com_Env' && ((valory == 'Adm' && id_aux != null) || valory == 'Man')) validar_servicio_seleccionado(servicio.vp_secundario_id, 'menos', id_tipo_solicitud);
        listar_servicios();
    });
    $('#tabla_servicios tbody').on('click', 'tr .asignar', function () {
        let { id, id_aux, tipo_solicitud, valory, id_servicio } = table.row($(this).parent().parent()).data();
        let fecha_inicial = $("#form_agregar_solicitud input[name = fecha_inicio_evento]").val();
        let fecha_final = $("#form_agregar_solicitud input[name = fecha_fin_evento]").val();
        data_detalle_ser = { 'tipo_servicio': valory, id_servicio, fecha_inicial, fecha_final };
        if (tipo_solicitud == 'Com_Env' && ((valory == 'Adm' && id_aux != null) || valory == 'Man') || (valory == 'Com' && id_aux == 'Ser_Staff')) mostrar_detalle_servicio(id_aux, id);
        else asignar_servicio(id);
    });

    $('#tabla_servicios tbody').on('click', 'tr .ver_detalle_servicio', function () {
        let data = table.row($(this).parent().parent()).data();
        ver_detalle_servicio(data);
    });
}
const asignar_servicio = (id, detalle = null) => {
    let servicio = servicios.find((elemento) => { return elemento.id == id; })
    servicio.estado = 1;
    if (detalle != null) servicio = Object.assign(servicio, detalle);
    //if ($("#presu1").is(':checked')) validar_servicio_seleccionado(servicio.vp_secundario_id, 'add', id_tipo_solicitud);
    listar_servicios();
    $("#modal_detalle_servicio").modal('hide');
}
const eliminar_servicio_solicitud = (id, id_solicitud) => {
    swal({
        title: "Estas Seguro ?",
        text: "Tener en cuenta que, al eliminar el servicio no se tendra en cuenta para su solicitud, si desea continuar presione la opción de 'Si, Entiendo'.!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            consulta_ajax(`${url}eliminar_servicio_solicitud`, { id, id_solicitud }, (resp) => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == 'success') {
                    swal.close();
                    listar_servicios_solicitud(id_solicitud);
                } else MensajeConClase(mensaje, tipo, titulo);
            });
        }
    });

}

const eliminar_adjunto_solicitud = id => {
    swal({
        title: "Estas Seguro ?",
        text: "Tener en cuenta que, al eliminar el archivo adjunto no se tendra en cuenta para su solicitud, si desea continuar presione la opción de 'Si, Entiendo'.!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            consulta_ajax(`${url}eliminar_adjunto_solicitud`, { id }, (resp) => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == 'success') {
                    swal.close();
                    listar_archivos_adjuntos(id_solicitud);
                } else MensajeConClase(mensaje, tipo, titulo);
            });
        }
    });

}

const eliminar_solicitud = id => {
    swal({
        title: "Estas Seguro ?",
        text: "Tener en cuenta que su solicitud no sera tomada en cuenta, si desea continuar presione la opción de 'Si, Entiendo'.!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            consulta_ajax(`${url}eliminar_solicitud`, { id }, (resp) => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == 'success') listar_solicitud(id_solicitud);
                MensajeConClase(mensaje, tipo, titulo);
            });
        }
    });

}



const listar_servicios_solicitud = id => {
    $('#tabla_servicios_solicitud tbody').off('click', 'tr .eliminar').off('click', 'tr').off('click', 'tr .info_comp');
    let i = 0;
    consulta_ajax(`${url}listar_servicios_solicitud`, { id }, (resp) => {
        const table = $("#tabla_servicios_solicitud").DataTable({
            "destroy": true,
            "processing": true,
            'data': resp,
            "columns": [
                {
                    "render": function (data, type, full, meta) {
                        let { id_tipo_solicitud, tipo_ser, id_aux } = full;
                        if (id_tipo_solicitud == 'Com_Env') {                            
                            if (id_aux == null) return `<span style="width: 100%;" class="pointer form-control" disabled='disabled'><span>Ver</span></span>`;
                            if (id_aux == 'Ser_Staff') return `<span style=" background-color:white;color: black;width: 100%;" class="pointer form-control info_comp"><span>Ver</span></span>`;
                            if (tipo_ser == 'Com') return `<span style="width: 100%;" class="pointer form-control" disabled='disabled'><span>Ver</span></span>`;
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
                {
                    "render": function (data, type, full, meta) {
                        let { estado_solicitud, presupuesto, id_tipo_solicitud } = full;
                        let resp = '';
                        if ((estado_solicitud != 'Com_Sol_E') || (id_tipo_solicitud == 'Com_Env' && presupuesto == 0)) resp = '<span class="fa fa-toggle-off btn "></span>';
                        else resp = '<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
                        return resp;
                    }
                },
            ],
            "language": idioma,
            dom: 'Bfrtip',
            "buttons": []

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $('#tabla_servicios_solicitud tbody').on('click', 'tr', function () {
            $("#tabla_servicios_solicitud tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_servicios_solicitud tbody').on('click', 'tr .info_comp', function () {
            let data = table.row($(this).parent()).data();
            ver_detalle_servicio_asignado(data);
        });

        $('#tabla_servicios_solicitud tbody').on('click', 'tr .eliminar', function () {
            let { id, id_solicitud } = table.row($(this).parent().parent()).data();
            eliminar_servicio_solicitud(id, id_solicitud);
        });

        const ver_detalle_servicio_asignado = data => {
            let { cantidad, tipo, tipo_entrega, observaciones } = data;
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
            $("#modal_detalle_servicio_asignado").modal();
        }

    });

}
const removerPintar = (tipo = "") => {
    (tipo == "remover")
        ? $('#modal_servicios #footermodal').html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>')
        : $('#modal_servicios #footermodal').html('<button type="button" class="btn btn-danger active" id="btnAgregarServicio"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>  <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    $("#btnAgregarServicio").click(() => guardar_servicios_nuevos());
}

const removerPintarAdjunto = (tipo = "") => {
    (tipo == "remover")
        ? $('#modal_enviar_archivos #footermodal').html('<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>')
        : $('#modal_enviar_archivos #footermodal').html('<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    $("#btnAgregarAdjunto").click(function () {
        $("#id_solicitud").val(id_solicitud);
        if (num_archivos != 0) {
            tipo_cargue = 0;
            myDropzone.processQueue();
        } else {
            MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
        }
    });
}

const consulta_solicitud_id = (id) => {

    $("#form_modi_solicitud").get(0).reset();
    consulta_ajax(`${url}consulta_solicitud_id`, { id }, async (resp) => {


        let { id_codigo_sap,
            descripcion,
            tipo_solicitud,
            estado_solicitud,
            id_tipo_evento,
            nro_invitados,
            telefono,
            direccion,
            nombre_lugar,
            fecha_registra,
            fecha_fin_evento,
            fecha_inicio_evento,
            id_tipo_solicitud,
            id_categoria_divulgacion,
            id,
            presupuesto,
            nombre_evento
        } = resp;
        solicitud_modi = { id, id_tipo_solicitud };

        if (id_tipo_solicitud == "Com_Env") configurar_name_fechas('#form_modi_solicitud', '#fechas_nueva_f1_modi');
        else if (id_tipo_solicitud == "Com_Div") configurar_name_fechas('#form_modi_solicitud', '#fechas_nueva_f1_modi');
        else if (id_tipo_solicitud == "Com_Pub") configurar_name_fechas('#form_modi_solicitud', '#fechas_nueva_f2_modi');
        else if (id_tipo_solicitud == "Com_Cub") configurar_name_fechas('#form_modi_solicitud', '#fechas_nueva_f3_modi');

        $("#form_modi_solicitud input[name='nombre_evento']").val(nombre_evento);
        $("#form_modi_solicitud input[name='id_codigo_sap']").val(id_codigo_sap);
        $("#form_modi_solicitud textarea[name='descripcion']").val(descripcion);
        $("#form_modi_solicitud select[name='tipo_solicitud']").val(tipo_solicitud);
        $("#form_modi_solicitud input[name='estado_solicitud']").val(estado_solicitud);
        $("#form_modi_solicitud select[name='id_tipo_evento']").val(id_tipo_evento);
        $("#form_modi_solicitud input[name='nro_invitados']").val(nro_invitados);
        $("#form_modi_solicitud input[name='telefono']").val(telefono);
        $("#form_modi_solicitud input[name='direccion']").val(direccion);
        $("#form_modi_solicitud input[name='nombre_lugar']").val(nombre_lugar);
        $("#form_modi_solicitud input[name='fecha_registra']").val(fecha_registra);
        $("#form_modi_solicitud input[name='fecha_fin_evento']").val(fecha_fin_evento);
        $("#form_modi_solicitud input[name='fecha_inicio_evento']").val(fecha_inicio_evento);
        $("#form_modi_solicitud select[name='id_categoria_divulgacion']").val(id_categoria_divulgacion);

        // let normales = await verificar_servicios_normales(id);

        if (id_tipo_solicitud == 'Com_Div') {
            $("#form_modi_solicitud select[name='id_categoria_divulgacion']").show("fast");
            $("#form_modi_solicitud select[name='id_categoria_divulgacion']").attr("required", "true");
            $("#info_categoria").show('fast');

        } else {
            $("#form_modi_solicitud select[name='id_categoria_divulgacion']").hide("fast");
            $("#form_modi_solicitud select[name='id_categoria_divulgacion']").removeAttr("required", "true");
            $("#info_categoria").hide('fast');

        }
        if (id_tipo_solicitud == "Com_Env" || id_tipo_solicitud == "Com_Div") $("#form_modi_solicitud input[name='nombre_evento']").show("fast");
        else $("#form_modi_solicitud input[name='nombre_evento']").hide('fast');


        if (id_tipo_solicitud == "Com_Env" || id_tipo_solicitud == "Com_Pub") mostrar_codigo_sap('show', '#form_modi_solicitud input[name="id_codigo_sap"]');
        else mostrar_codigo_sap('hide', '#form_modi_solicitud input[name="id_codigo_sap"]');
        $("#modal_modificar_solicitud").modal();


    });
}

const modificar_solicitud = () => {
    MensajeConClase("validando info", "add_inv", "Oops...");
    let fordata = new FormData(document.getElementById("form_modi_solicitud"));
    let data = formDataToJson(fordata);
    data.id_tipo_solicitud = solicitud_modi.id_tipo_solicitud;
    data.id = solicitud_modi.id;
    consulta_ajax(`${url}modificar_solicitud`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            $("#id_solicitud").val(id_solicitud);
            if (num_archivos != 0) {
                tipo_cargue = 2;
                myDropzone.processQueue();
            } else {
                MensajeConClase(mensaje, tipo, titulo);
            }
            listar_solicitud();
            $("#modal_modificar_solicitud").modal("hide");
        } else{
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const obtener_informacion_modulo = (idparametro, fun = 'obtener_valores_parametro') => {
    return new Promise(resolve => {
        consulta_ajax(`${Traer_Server()}index.php/genericas_control/${fun}`, { idparametro }, (resp) => {
            resolve(resp)
        });
    });
}

const cargar_informacion_menu = async () => {
    let resp = await obtener_informacion_modulo(60);
    resp.forEach(({ id_aux, valorx, valor }) => {
        if (id_aux == "Com_Env") {
            $("#titulo_evento").html(valor);
            $("#agregar_evento span ").attr("data-content", valorx);
        } else if (id_aux == "Com_Div") {
            $("#titulo_divulgacion").html(valor);
            $("#agregar_divulgacion span").attr("data-content", valorx);
        } else if (id_aux == "Com_Pub") {
            $("#titulo_publicidad").html(valor);
            $("#agregar_publicidad span").attr("data-content", valorx);
        } else if (id_aux == "Com_Cub") {
            $("#titulo_cubrimiento").html(valor);
            $("#agregar_cubrimiento span").attr("data-content", valorx);

        }
    });
}

const cargar_mensajes_alert = async (idparametro, tipo, container) => {
    let resp = await obtener_informacion_modulo(idparametro);
    let data = resp.find(({ id }) => { return id == tipo; });
    if (data) $(container).html(data.valorx).show('slow');
    else $(container).html('').hide('slow');
}
const validar_servicio_seleccionado = async (id_servicio, tipo = 'add', tipo_solicitud) => {
    let data = await obtener_informacion_modulo(id_servicio, 'obtener_valor_parametro_id');
    cont_servicio.normales = tipo == 'add' ? cont_servicio.normales += 1 : cont_servicio.normales -= 1;
    let { id_aux } = data[0];
    if (id_aux == 'Ser_Staff' || id_aux == 'Ser_Dif') {
        cont_servicio.especiales = tipo == 'add' ? cont_servicio.especiales += 1 : cont_servicio.especiales -= 1;
        if (id_aux == 'Ser_Dif') {
            if (tipo == 'add') {
                $("#info_categoria").html('A seleccionado el servicio "Diseño y difusion para el evento", por favor especifique en el campo descripcion los detalles del diseño.').show('fast');
                $("#form_agregar_solicitud textarea[name=descripcion]").attr("required", "true");
            } else {
                $("#info_categoria").html('A seleccionado el servicio "Diseño y difusion para el evento", por favor especifique en el campo descripcion los detalles del diseño.').hide('fast');
                $("#form_agregar_solicitud textarea[name=descripcion]").removeAttr("required", "true");
            }
            cont_servicio.diseno = 1;
        }
    }
    if (cont_servicio.normales > cont_servicio.especiales && (tipo_solicitud == 'Com_Env' || tipo_solicitud == 'Com_Pub')) mostrar_codigo_sap('show');
    else mostrar_codigo_sap('hide');
}

const obtener_info_usuario = (perfil, persona) => {
    persona_sesion = { perfil, persona };
}

const gestionar_solicitud = (id, estado, correo, mensaje, diseno = 0, id_tipo_solicitud = '', estado_anterior = '') => {
    $("#cont_diseno input").removeAttr("required", "true");
    $("#cont_diseno textarea").removeAttr("required", "true");
    $("#form_gestionar_solicitud").get(0).reset();

    const gestionar_solicitud_copia = (id, estado, title = '¿ Copiar Solicitud ?', correo, mensaje, diseno) => {
        swal({
            title,
            text: "Tener en cuenta que es necesario agregar servicios y adjuntar nuevamente los soportes a su solicitud, si desea continuar debe presionar la opción de 'Si, Entiendo'!",
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
                    copiar_solicitud(id);
                }
            });
    }
    const gestionar_solicitud_normal = (id, estado, title = '¿ Cancelar Solicitud ?', correo, mensaje, diseno) => {
        swal({
            title,
            text: "Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'",
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
                    ejecutar_gestion(id, estado, mensaje, correo, diseno);
                }
            });
    }

    const gestionar_solicitud_texto = (id, estado, title = '¿ Negar Solicitud ?') => {

        swal({
            title,
            text: "",
            type: "input",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Aceptar!",
            cancelButtonText: "Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            inputPlaceholder: "Ingrese Motivo"
        }, function (mensaje) {

            if (mensaje === false)
                return false;
            if (mensaje === "") {
                swal.showInputError("Debe Ingresar el motivo.!");
            } else {
                ejecutar_gestion(id, estado, mensaje);
                return false;
            }
        });
    }
    const ejecutar_gestion = (id, estado, mensaje_e = '', correo = '', diseno = 0) => {
        consulta_ajax(`${url}gestionar_solicitud`, { id, estado, 'mensaje': mensaje_e, correo, diseno }, (resp) => {
            let { titulo, mensaje, tipo, refres } = resp;
            if (tipo == 'success' || refres == 1) {
                swal.close();
                enviar_correo_estado(estado, id, mensaje_e, correo, enviar_adjuntos, estado_anterior);
                enviar_adjuntos = [];
                listar_solicitud();
                $("#modal_gestionar_solicitud").modal("hide");
                $("#form_gestionar_solicitud").get(0).reset();
                $("#cont_diseno").hide("slow");
                $("#cont_diseno input").removeAttr("required", "true");
                $("#cont_diseno textarea").removeAttr("required", "true");
                if (tipo_eje == 2) {
                    $("#modal_detalle_solicitud").modal("hide");
                    mostrar_solicitudes_terminadas_ext();
                }
            } else {
                MensajeConClase(mensaje, tipo, titulo);
            }
        });
    }

    if (estado == 'Com_Rec_E') {
        gestionar_solicitud_texto(id, estado);
    } else if (estado == 'Com_Can_E') {
        gestionar_solicitud_normal(id, estado);
    } else if (estado == 'Com_Fin_E') {
        gestionar_solicitud_normal(id, estado, '¿ Finalizar Solicitud ?');
    } else if (estado == 'Com_Ace_E') {
        gestionar_solicitud_normal(id, estado, '¿ Aceptar Diseño ?');
    } else if (estado == 'Com_Des_E') {
        gestionar_solicitud_texto(id, estado, '¿ Descartar Diseño ?');
    } else if (estado == 'Com_Cor_E') {
        gestionar_solicitud_texto(id, estado, '¿ Enviar a Correción ?');
    } else if (estado == 'Com_Sol_E') {
        gestionar_solicitud_normal(id, estado, '¿ Enviar Solicitud ?');
    } else if (estado == 'Com_Ent_E') {
        if (id_tipo_solicitud == 'Com_Div') {
            $("#form_gestionar_solicitud .oculto").css('display', 'none');
            estado_gestion = 'Com_Ent_Ter';
            tipo_adj = 1;
            enviar_adjuntos = [];
            $("#cont_Tramite").show('fast');
            $("#modal_gestionar_solicitud").modal("show");
        } else {
            gestionar_solicitud_normal(id, estado, '¿ Tramitar Solicitud ?', correo, mensaje, diseno);
        }
    } else if (estado == 'Com_Ent_Ter') {
        estado = 'Com_Ent_E';
        gestionar_solicitud_normal(id, estado, '¿ Tramitar Solicitud ?', correo, mensaje, diseno);
    } else if (estado == 'Com_Enc_E') {
        $("#modal_calificar_solicitud").modal("show");
    } else if (estado == 'Com_Rev_E') {
        $("#form_gestionar_solicitud .oculto").css('display', 'none');
        estado_gestion = 'Com_Rev_Ter';
        $("#cont_adjuntos").show('fast');
        tipo_adj = 1;
        enviar_adjuntos = [];
        $("#modal_gestionar_solicitud").modal("show");
    } else if (estado == 'Com_Rev_Ter') {
        estado = 'Com_Rev_E';
        gestionar_solicitud_normal(id, estado, '¿ Solicitud a revisión ?');
    } else if (estado == 'Com_Cop_E') {
        estado = 'Com_Sol_E';
        gestionar_solicitud_copia(id, estado, '¿ Copiar Solicitud ?');

    }
}
const copiar_solicitud = id => {

    consulta_ajax(`${url}consulta_solicitud_id`, { id }, (resp) => {
        let { id_codigo_sap,
            descripcion,
            tipo_solicitud,
            estado_solicitud,
            id_tipo_evento,
            nro_invitados,
            telefono,
            direccion,
            nombre_lugar,
            fecha_registra,
            fecha_fin_evento,
            fecha_inicio_evento,
            id_tipo_solicitud: id_tipo_sol,
            id_categoria_divulgacion,
            nombre_evento,
        } = resp;

        if (id_tipo_sol == "Com_Env") configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f1');
        else if (id_tipo_sol == "Com_Div") configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f1');
        else if (id_tipo_sol == "Com_Pub") configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f2');
        else if (id_tipo_sol == "Com_Cub") configurar_name_fechas('#form_agregar_solicitud', '#fechas_nueva_f3');

        $("#form_agregar_solicitud input[name='nombre_evento']").val(nombre_evento);
        $("#form_agregar_solicitud input[name='id_codigo_sap']").val(id_codigo_sap);
        $("#form_agregar_solicitud textarea[name='descripcion']").val(descripcion);
        $("#form_agregar_solicitud select[name='tipo_solicitud']").val(tipo_solicitud);
        $("#form_agregar_solicitud input[name='estado_solicitud']").val(estado_solicitud);
        $("#form_agregar_solicitud select[name='id_tipo_evento']").val(id_tipo_evento);
        $("#form_agregar_solicitud input[name='nro_invitados']").val(nro_invitados);
        $("#form_agregar_solicitud input[name='telefono']").val(telefono);
        $("#form_agregar_solicitud input[name='direccion']").val(direccion);
        $("#form_agregar_solicitud input[name='nombre_lugar']").val(nombre_lugar);
        $("#form_agregar_solicitud input[name='fecha_registra']").val(fecha_registra);
        $("#form_agregar_solicitud input[name='fecha_fin_evento']").val(fecha_fin_evento);
        $("#form_agregar_solicitud input[name='fecha_inicio_evento']").val(fecha_inicio_evento);
        $("#form_agregar_solicitud select[name='id_categoria_divulgacion']").val(id_categoria_divulgacion);



        if (id_tipo_sol == "Com_Env" || id_tipo_sol == "Com_Pub") mostrar_codigo_sap('show');
        else mostrar_codigo_sap('hide');

        if (id_tipo_sol == "Com_Env" || id_tipo_sol == "Com_Div" || id_tipo_sol == "Com_Cub" ) {
            $(`#nombre_evento`).show("fast");
            $(`#nombre_evento input`).attr("required", "true");
        } else {
            $(`#nombre_evento`).hide("fast");
            $(`#nombre_evento input`).removeAttr("required", "true");
        }

        if (id_tipo_sol == 'Com_Div') {
            $('#form_agregar_solicitud select[name="id_categoria_divulgacion"]').show("fast");
            $('#form_agregar_solicitud select[name="id_categoria_divulgacion"]').attr("required", "true");
            $("#info_categoria").show('fast');

        } else {
            $('#form_agregar_solicitud select[name="id_categoria_divulgacion"]').hide("fast");
            $('#form_agregar_solicitud select[name="id_categoria_divulgacion"]').removeAttr("required", "true");
            $("#info_categoria").hide('fast');

        }

        id_tipo_solicitud = id_tipo_sol;
        pasar_data_servicios(id_tipo_sol);
        $("#modal_agregar_solicitud").modal();

    });
}
const listar_estados = (id, demora = '----', gestion = '----') => {
    $('#dias_demora').html(demora);
    $('#dias_gestion').html(gestion);
    $('#tabla_estados_comunicaciones tbody')
        .off('click', 'tr #ver_lista_estados')
    consulta_ajax(`${url}listar_estados`, { id }, (resp) => {
        const table = $("#tabla_estados_comunicaciones").DataTable({
            "destroy": true,
            "processing": true,
            'data': resp,
            'searching': false,
            "columns": [
                {
                    "render": function (data, type, full, meta) {
                        let { id_estado } = full;
                        let disable = ' ';
                        let id = 'id ="ver_lista_estados"';
                        if (id_estado == 'Com_Sol_E') { disable = 'disabled="disabled"'; id = ' ' }
                        else if (id_estado == 'Com_Rev_E') { disable = 'disabled="disabled"'; id = ' ' }
                        else if (id_estado == 'Com_Ace_E') { disable = 'disabled="disabled"'; id = ' ' }
                        else if (id_estado == 'Com_Fin_E') { disable = 'disabled="disabled"'; id = ' ' }
                        return `<span style="width: 100%;" class="pointer form-control" ${disable} ${id}><span>Ver</span></span>`;
                    }
                },
                {
                    "data": "fecha_registro"
                },
                {
                    "data": "solicitante"
                },
                {
                    "data": "estado_solicitud"
                },
            ],
            "language": idioma,
            dom: 'Bfrtip',
            "buttons": []

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $('#tabla_estados_comunicaciones tbody').on('click', 'tr', function () {
            $("#tabla_estados_comunicaciones tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_estados_comunicaciones tbody').on('click', 'tr #ver_lista_estados', function () {
            let data = table.row($(this).parent()).data();
            ver_detalle_estado(data);
        });
    });

}

const ver_detalle_estado = (datos) => {

    let { correo_disenador,
        observacion,
        id_estado, } = datos;

    $(".correo_disenador").html(correo_disenador);
    $(".observacion").html(observacion);

    if (id_estado == 'Com_Des_E' || !correo_disenador) $(".oculto_revision").hide('fast');
    else $(".oculto_revision").show('fast');

    $("#modal_detalle_estado").modal();
}

const guardar_encuesta_solicitud = (id) => {
    const data = new FormData(document.getElementById("form_guardar_calificacion"));
    data.append('id', id);
    enviar_formulario(`${url}guardar_encuesta_solicitud`, data, (resp) => {
        let { titulo, mensaje, tipo, refres } = resp;
        if (tipo == 'success' || refres) {
            listar_solicitud();
            $("#modal_calificar_solicitud").modal("hide");
            $("#form_guardar_calificacion").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}
const adjuntos_revision = (id) => {
    $('#modal_enviar_archivos #footermodal').html('<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>');
    $("#modal_enviar_archivos").modal("show");
    $("#btnAgregarAdjunto").click(function () {
        $("#id_solicitud").val(id);
        if (num_archivos != 0) {
            tipo_cargue = 0;
            myDropzone.processQueue();
        } else {
            MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
        }
    });
}
const verificar_servicios_normales = id => {
    return new Promise(resolve => {
        consulta_ajax(`${url}verificar_servicios_normales`, { id }, (resp) => {
            resolve(resp)
        });
    });
}

const get_personas_notificar = () => {
	return new Promise((resolve) => {
		consulta_ajax(`${url}get_personas_notificar`, {}, (data) => resolve(data))
	})
}

const enviar_correo_estado = async (estado, id, motivo, correo_di = '', enviar_adjuntos = [], estado_anterior='') => {
    let sw = false;
    let adjuntos = '';
    let { nombre, correo } = data_solicitante;
    let ser = `<a href="${server}index.php/comunicaciones/${id}"><b>agil.cuc.edu.co</b></a>`;
    let tipo = -1;
    let titulo = 'Solicitud de Comunicaciones';
    let mensaje = `Se informa que la solicitud realizada por usted, fue enviada y se encuentran en proceso de verificacion, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentran su solicitud.<br><br>Mas informaci&oacuten en :${ser}`;
    if (enviar_adjuntos.length != 0) enviar_adjuntos.map((e) => { adjuntos = `${adjuntos}${`<a href="${server}archivos_adjuntos/comunicaciones/solicitudes/${e.nombre_guardado}"><b>${e.nombre_real}</b></a><br>`}` });
    if (estado == 'Com_Rec_E') {
        sw = true;
        tipo = 1;
        mensaje = `Se informa que su solicitud ha sido negada porque no cumple con los siguientes requisitos ${motivo} para recepci&oacuten de la misma.<br><br>Mas informaci&oacuten en ${ser}`;
    } else if (estado == 'Com_Fin_E') {
        sw = true;
        tipo = 1;
        mensaje = `Se informa que su solicitud ha finalizado. A partir de este momento tiene 24 horas para dar respuesta a la encuesta, de lo contrario se asumir&aacute que recibi&oacute todo a conformidad.<br><br>Mas informaci&oacuten en ${ser}`;
    } else if (estado == 'Com_Ent_E' && correo_di.length != 0) {
        sw = true;
        tipo = 1;
        correo = correo_di;
        nombre = 'DISEÑADOR COMUNICACIONES';
        mensaje = `Se informa que ha recibido un nuevo Dise&ntilde;o por parte del departamento de Comunicaciones. El solicitante requiere lo siguiente: <br><br> ${motivo} <br><br> Enlaces: <br><br> ${adjuntos}`;
    } else if (estado == 'Com_Rev_E') {
        sw = true;
        tipo = 1;
        mensaje = `Se informa que su solicitud esta en revision, a partir de este momento puede ingresar al aplicativo AGIL para ACEPTAR o DESCARTAR el dise&ntilde;o de su solicitud, puede validar la informaci&oacute;n en ${ser} o verificar los siguientes enlaces: <br><br> ${adjuntos}`;
    } else if (estado == 'Com_Cor_E') {
        sw = true;
        tipo = 1;
        titulo = 'Corrección Solicitud de Comunicaciones';
        mensaje = `Se informa que su solicitud fue enviada a corrección por el siguiente motivo: ${motivo}, a partir de este momento puede ingresar al aplicativo AGIL para modificar su solicitud segun lo requerido. Recuerde hacer cick en la opción <strong>Enviar Solicitud</strong> luego de guardar las correciones.<br><br>Mas informaci&oacuten en ${ser}`;
    } else if (estado == 'Com_Sol_E'){
         sw = true;
        if(estado_anterior == 'Com_Cor_E'){
            tipo_adm = 3;
            titulo_adm = 'Solicitud de Comunicaciones Corregida';
            mensaje_adm = `Se informa que una solicitud fue corregida, a partir de este momento puede ingresar al aplicativo AGIL para gestionar la solicitud.<br><br>Mas informaci&oacuten en ${ser}`;
        }else{
            tipo_adm = 3;
            titulo_adm = 'Solicitud de Comunicaciones';
            mensaje_adm = `Se informa que una nueva solicitud fue enviada, a partir de este momento puede ingresar al aplicativo AGIL para gestionar la solictud.<br><br>Mas informaci&oacuten en ${ser}`;
        }
        let correos = await get_personas_notificar();
        enviar_correo_personalizado("com", mensaje_adm, correos, 'Funcionario', "Comunicaciones CUC", titulo_adm, "ParCodCom", tipo_adm);
    }

    if (sw) enviar_correo_personalizado("com", mensaje, correo, nombre, "Comunicaciones CUC", titulo, "ParCodCom", tipo);
}
const configurar_fechas = (dias = 0, elemento = '.formato_fecha') => {
    let startDate = new Date();
    startDate.setDate(startDate.getDate() + dias);
    $(elemento).datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate,
        todayBtn: false,
        daysOfWeekDisabled: [0],
    }
    );
}
const configurar_name_fechas = (form, container) => {
    $(`${form} .f_inicio`).removeAttr('name', 'fecha_inicio_evento').removeAttr('required', 'true');
    $(`${form} .f_fin`).removeAttr('name', 'fecha_fin_evento').removeAttr('required', 'true');
    $(`${container} .f_inicio`).attr('name', 'fecha_inicio_evento').attr('required', 'true');
    $(`${container} .f_fin`).attr('name', 'fecha_fin_evento').attr('required', 'true');
    $(".fechas .oculto").css("display", 'none');
    $(container).show();
}
const mostrar_detalle_servicio = (id_aux, id) => {
    id_servicio_detalle = id;

    if (id_aux == 'Ser_Flo') {
        configurar_detalle_servicios([{ 'div': '#flores', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Cuc') {
        configurar_detalle_servicios([{ 'div': '#cucharas', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Ten') {
        configurar_detalle_servicios([{ 'div': '#tenedores', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Cop') {
        configurar_detalle_servicios([{ 'div': '#copas', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Alm') {
        configurar_detalle_servicios([{ 'div': '#almuerzo', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Mes') {
        configurar_detalle_servicios([{ 'div': '#mesas', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    } else if (id_aux == 'Ser_Ref') {
        configurar_detalle_servicios([{ 'div': '#refri', 'name': 'id_tipo', 'tipo': 'select' }, { 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }, { 'div': '#entrega', 'name': 'id_tipo_entrega', 'tipo': 'select' }]);
    } else if (id_aux == 'Ser_CyA') {
        configurar_detalle_servicios([{ 'div': '#entrega', 'name': 'id_tipo_entrega', 'tipo': 'select' }]);
    } else {
        configurar_detalle_servicios([{ 'div': '#cantidad', 'name': 'cantidad', 'tipo': 'input' }]);
    }
    if (id_aux == 'Ser_Coc' || id_aux == 'Ser_Vid' || id_aux == 'Ser_Son' || id_aux == 'Ser_Por') asignar_servicio(id);
    else $("#modal_detalle_servicio").modal();

}
const configurar_detalle_servicios = (containers) => {
    $("#conta_detalle_servicio .oculto").hide();
    $("#conta_detalle_servicio input").removeAttr("required", "true").removeAttr('name');
    $("#conta_detalle_servicio select").removeAttr("required", "true").removeAttr('name');
    containers.map((elemento) => {
        let { div, name, tipo } = elemento;
        $(`${div}`).show("fast");
        $(`${div} ${tipo}`).attr("required", "true").attr('name', name);
    })
}

const validar_detalle_servicio = () => {
    let fordata = new FormData(document.getElementById("form_detalle_servicio"));
    let data = formDataToJson(fordata);
    data = Object.assign(data, data_detalle_ser);
    consulta_ajax(`${url}validar_detalle_servicio`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            asignar_servicio(id_servicio_detalle, data);
            $("#modal_detalle_servicio").modal("hide");
            $("#form_detalle_servicio").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}
const ver_detalle_servicio = (data) => {
    let { id_tipo, id_tipo_entrega, cantidad, id, id_aux, tipo_solicitud, valory, observaciones } = data;
    // if (tipo_solicitud == 'Com_Env' && (valory == 'Adm' || valory == 'Man')) {
    if (tipo_solicitud == 'Com_Env') {
        mostrar_detalle_servicio(id_aux, id);
        $("#form_detalle_servicio select[name='id_tipo']").val(id_tipo);
        $("#form_detalle_servicio select[name='id_tipo_entrega']").val(id_tipo_entrega);
        $("#form_detalle_servicio input[name='cantidad']").val(cantidad);
        $("#form_detalle_servicio textarea[name='observaciones']").val(observaciones);
    }
}

const servicios_con_mant = async () => {
    //let ser_com = await obtener_servicios(id_tipo_solicitud, 'Com', 'si');
    let ser_man = await obtener_servicios_mantenimiento();
    let ser_man_fin = [];
    ser_man.map((elemento) => {
        let servicio = {
            'estado': "0",
            'id': `M${elemento.id}`,
            'id_aux': '',
            'id_servicio': elemento.id,
            'nombre': elemento.nombre,
            'tipo_solicitud': "Com_Env",
            'valory': "Man",
            'vp_principal': '',
            'vp_principal_id': '',
            'vp_secundario': '',
            'vp_secundario_id': '',
        }
        ser_man_fin.push(servicio);
    })
    //servicios = ser_com.concat(ser_man_fin);
    servicios = ser_man_fin;
}
const traer_solicitudes_terminadas_man = () => {
    return new Promise(resolve => {
        consulta_ajax(`${url}traer_solicitudes_terminadas_man`, '', (resp) => {
            resolve(resp)
        });
    });
}
const traer_solicitudes_terminadas_adm = () => {
    return new Promise(resolve => {
        consulta_ajax(`${url}traer_solicitudes_terminadas_adm`, '', (resp) => {
            resolve(resp)
        });
    });
}
const mostrar_solicitudes_terminadas_ext = async () => {
    let man = await traer_solicitudes_terminadas_man();
    let adm = await traer_solicitudes_terminadas_adm();
    let resp = man.concat(adm)
    let notificaciones = '';
    resp.map((elemento) => {
        const { id, obs_man, estado_man, persona, nombre_evento, estado_solicitud_man, presupuesto } = elemento;
        notificaciones = notificaciones + `<a href="#" class="list-group-item">
            <span class="badge" onclick='abrir_solicitud_notificacion(${id},"${estado_solicitud_man}")'>ABRIR</span>
            <p class="list-group-item-text">La solicitud ${nombre_evento} realizada por ${persona} fue:</p>
            <h4 class="list-group-item-heading">${estado_man} </h4>
            <p class="list-group-item-text">${estado_solicitud_man == 'Man_Rec' || estado_solicitud_man == 'Sol_Den' || estado_solicitud_man == 'Sol_Apro' ? 'Mensaje : ' + obs_man + '.' : ''}</p>
        </a>
        `;
    })
    $("#panel_notificaciones").html(`
    <ul class="list-group">
        <li class="list-group-item active">
        <span class="badge">${resp.length}</span>
        Solicitudes gestionadas por administrativa
    </li>
    ${notificaciones}
    </ul>
    `);
    $(".n_notificaciones_com").html(resp.length);
    if (resp.length > 0) $("#modal_notificaciones").modal();
};

const abrir_solicitud_notificacion = (id, estado_solicitud_man) => {
    tipo_eje = 2;
    consulta_ajax(`${url}consulta_solicitud_id`, { id }, async (resp) => {
        let footer = estado_solicitud_man == 'Man_Rec' || estado_solicitud_man == 'Sol_Den' ? `<button onclick="gestionar_solicitud(${id}, 'Com_Rec_E');" type="button" class="btn btn-danger2 active"><span class="fa fa-ban"></span> Negar</button>` : `<button onclick="gestionar_solicitud(${id}, 'Com_Fin_E');" type="button" class="btn btn-success active"><span class="fa fa-check"></span> Finalizar</button>`;
        footer = `${footer}${estado_solicitud_man == 'Sol_Apro' ? `<button onclick="gestionar_solicitud(${id}, 'Com_Rec_E');" type="button" class="btn btn-danger2 active"><span class="fa fa-ban"></span> Negar</button>` : ''} <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`;
        data_solicitante = { 'nombre': resp.solicitante, 'correo': resp.correo };
        ver_detalle_solicitud(resp, footer);
    });
}

const confirmar_add_solicitud = text => {
    swal({
        title: "Tener en Cuenta...",
        text,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            guardar_solicitud(1);
        }
    });

}
