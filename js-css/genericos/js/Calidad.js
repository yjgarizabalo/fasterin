let ruta_ambiental = `${Traer_Server()}index.php/calidad_control/`;
let callback_activo = resp => {};
let ruta_adjuntos = "archivos_adjuntos/calidad/";
let ruta_adjuntos_correo = "archivos_adjuntos\\calidad\\";
let id_solicitud_global = null;
let id_lote_global = null;
let empresa_global = null;
let data_solicitud_global = null;
let filtro = null;
let persona_sele = [];
let callback_activo_aux = resp => {};
let auxiliar = null;
let id_proceso_global = null;
let tipo_persona = null;
let datos_grafica = null;
let titulo_grafica = "";
let accion = null;
let grafica = null;



$(document).ready(() => {

    $("#regresar_menu").click(function() {
        $(".listado_solicitudes").css("display", "none");
        $("#menu_principal").fadeIn(1000);
    });

    $("#listado").click(function() {
        $("#menu_principal").css("display", "none");
        $(".listado_solicitudes").fadeIn(1000);
        listar_solicitudes();
    });

    $("#nueva_solicitud").click(function() {
        obtener_estados_residuos('cbxestadoresiduo', "Seleccionar estado del residuo");
        obtener_cantidades_residuos('cbxcantidad', "Seleccionar unidad de medida");
        obtener_presentaciones_residuos('cbxpresentacion', "Seleccionar presentación del residuo");
        obtener_bloques();
        $("#form_modificar_solicitud").attr("id", "form_agregar_solicitud");
        $("#form_agregar_solicitud").get(0).reset();
        $("#modal_agregar_solicitud").modal();
    });

    $("#btn_activo").click(function() {
        if ($(this).is(":checked")) {
            $("#carta_activo").removeClass("oculto");
        } else if ($(this).is(":not(:checked")) {
            $("#carta_activo").addClass("oculto");
        }
    });

    $("#btn_activo_mod").click(function() {
        if ($(this).is(":checked")) {
            $("#carta_activo_mod").removeClass("oculto");
        } else if ($(this).is(":not(:checked")) {
            $("#carta_activo_mod").addClass("oculto");
        }
    });

    $("#form_agregar_solicitud").submit(e => {
        e.preventDefault();
        crear_solicitud();
        return false;
    });

    $(".btn_log").click(function() {
        listar_historial_estados();
    });

    $("#btn_log_lote").click(function() {
        listar_historial_estados_lotes();
    });

    $("#btn_limpiar_filtros").click(function() {
        listar_solicitudes();
    });

    $("#btn_auxiliar").click(function() {
        filtro = 1;
        container_activo = "#txt_auxiliar";
        $("#modal_buscar_auxiliar").modal();
        callback_activo = resp => {
            mostrar_nombre_persona(resp);
        };
        buscar_empleado("", filtro, callback_activo);
    });

    $("#form_buscar_auxiliar").submit(() => {
        let dato = $("#txt_dato_buscar").val();
        // callback_activo = resp => {
        // 	mostrar_nombre_persona(resp);
        // };
        buscar_empleado(dato, filtro, callback_activo);
        return false;
    });

    $("#form_asignar_solicitud").submit(e => {
        e.preventDefault();
        asignar_solicitud();
        return false;
    });

    $("#bloque").change(function() {
        const bloque = $(this).val();
        obtener_bloque_salon(bloque);
    });

    $("#bloque_mod").change(function() {
        const bloque = $(this).val();
        obtener_bloque_salon(bloque);
    });

    $("#btn_ver_lotes").click(function() {
        listar_lotes();
        $("#modal_ver_lotes").modal();
    });

    $("#btn_nuevo_lote").click(function() {
        obtener_empresas();
        $("#modal_crear_lote").modal();
    });

    $("#form_crear_lote").submit(e => {
        e.preventDefault();
        crear_lote();
        return false;
    });

    $("#form_formulario_empresa").submit(e => {
        e.preventDefault();
        enviar_formulario_correo();
        return false;
    });

    $("#form_remitir_lote").submit(e => {
        e.preventDefault();
        gestionar_lote('Est_Cal_Rem', 'form_remitir_lote');
        return false;
    });

    $("#form_finalizar").submit(e => {
        e.preventDefault();
        gestionar_lote('Est_Cal_Fin', 'form_finalizar');
        return false;
    });

    $("#btn_aplicar_filtros").click(function() {
        obtener_tipos_solicitud('cbxtiposol', "Filtrar por tipo solicitud");
        obtener_estados_residuos('cbxtipos', "Filtrar por Tipo residuo");
        obtener_presentaciones_residuos('cbxpresentacion', "Filtrar por presentacion");
        obtener_cantidades_residuos('cbxcantidad', "Filtrar por unidad de medida");
        obtener_parametros(209, 'cbxproceso', "Filtrar por tipo de Proceso");
        obtener_parametros(212, 'cbxorigen', "Filtrar por Origen - Fuente");
        obtener_estados();
        $("#modal_crear_filtros").modal();
    });

    $("#form_filtros").submit(e => {
        e.preventDefault();
        filtrar_solicitudes();
        return false;
    });

    $("#form_modificar_solicitud").submit(e => {
        e.preventDefault();
        modificar_solicitud();
        return false;
    });

    $("#btn_modificar").click(async function() {
        if (!data_solicitud_global) MensajeConClase('Seleccione un solicitud a modificar', 'info', 'Oops!')
        else {
            if (data_solicitud_global) {
                let { tipo_solicitud, id_estado, id } = data_solicitud_global;
                if (tipo_solicitud == 'Tip_Cal_Re') {
                    let { cantidad, tipo_cantidad, presentacion, estado_residuo, bloque, salon, descripcion, activo, carta_activo } = data_solicitud_global;
                    if (id_estado == 'Est_Cal_Neg' || id_estado == 'Est_Cal_Sol') {
                        await obtener_bloques()
                        await obtener_estados_residuos('cbxestadoresiduo', "Seleccionar estado del residuo");
                        await obtener_cantidades_residuos('cbxcantidad', "Seleccionar unidad de medida");
                        await obtener_presentaciones_residuos('cbxpresentacion', "Seleccionar presentación del residuo");
                        await obtener_bloque_salon(bloque);
                        $("#txt_cantidad_residuo_mod").val(cantidad);
                        $(".cbxcantidad").val(tipo_cantidad);
                        $(".cbxpresentacion").val(presentacion);
                        $(".cbxestadoresiduo").val(estado_residuo);
                        $(".cbxbloque").val(bloque);
                        $(".cbxsalon").val(salon);
                        $("#txt_descripcion_mod").val(descripcion);
                        $("#modal_modificar_solicitud").modal();
                        if (activo == 1) {
                            $("#carta_activo_mod").removeClass("oculto");
                            $("#carta_activo_text_mod").val(carta_activo);
                            $('#btn_activo_mod').prop('checked', true);
                        } else $("#carta_activo_mod").addClass("oculto");
                    } else MensajeConClase('El estado actual de la solicitud no permite que se modifique', 'info', 'Oops!');
                }
            }
        }
    });

    $("#nueva_sol_auditoria").click(function() {
        obtener_parametros(209, 'cbxproceso', "Seleccionar el Proceso");
        $("#form_agregar_sol_auditoria").get(0).reset();
        $("#modal_agregar_sol_auditoria").modal();
    });

    $("#form_agregar_sol_auditoria").submit(e => {
        e.preventDefault();
        crear_solicitud_auditoria();
        return false;
    });

    $("#btn_procesos").click(function() {
        listar_procesos();
        $("#modal_procesos").modal();
    });

    $("#btn_informes").click(function() {
        $("#modal_informes").modal();
    });

    $("#imprimir_informe_general").click(function() {
        var fecha_inicio = document.getElementById("fecha1_general").value;
        var fecha_fin = document.getElementById("fecha2_general").value;

        if (fecha_inicio > fecha_fin) {
            MensajeConClase('La fecha inicial no puede ser mayor a la fecha final', 'info', 'Oops!');
        } else {
            if (fecha_inicio == 0 || fecha_fin == 0) {
                MensajeConClase('Los campos de fecha deben ser Diligenciados', 'info', 'Oops!');
            } else {
                crear_informe_general(fecha_inicio, fecha_fin);
            }
        }
    });

    $("#mostar_informe_general").click(function() {
        $("#modal_informe_general").modal();
        $('#modal_informes').modal('hide');
    });

    $("#btn_generar_Graficos").click(function() {
        let data = [];
        if (accion == 1) {
            datos_grafica.forEach(element => {
                data.push({ 'valorTxt': element.nombre, 'valorNro': element.cantidad })
            });
        } else if (accion == 2) {
            datos_grafica.forEach(element => {
                data.push({ 'valorTxt': element.proceso, 'valorNro': element.porcentaje })
            });
        }
        graficar_datos(data, titulo_grafica);
        $("#modal_generar_grafica").modal();
    });

    $("#btn_generar_Graficos2").click(function() {
        graficar_datos2(datos_grafica, titulo_grafica);
        $("#modal_generar_grafica").modal();
    });

    $("#btn_generar_Graficos3").click(function() {
        let data = [];
        if (accion == 3) {
            datos_grafica.forEach(element => {
                data.push({ 'proceso': element.proceso, 'v1': element.correctiva, 'v2': element.preventiva, 'v3': element.mejora })
            });
            titulo1 = "correctiva";
            titulo2 = "preventiva";
            titulo3 = "mejora";
        } else if (accion == 4) {
            datos_grafica.forEach(element => {
                data.push({ 'proceso': element.proceso, 'v1': element.no_conformidad, 'v2': element.op_mejora, 'v3': element.observacion })
            });
            titulo1 = "no conformidad";
            titulo2 = "oportunidad de mejora";
            titulo3 = "observacion";
        } else if (accion == 5) {
            datos_grafica.forEach(element => {
                data.push({ 'proceso': element.origen, 'v1': element.ejecutada, 'v2': element.en_proceso, 'v3': element.solicitada })
            });
            titulo1 = "ejecutado";
            titulo2 = "proceso";
            titulo3 = "abierta";
        } else if (accion == 6) {
            datos_grafica.forEach(element => {
                data.push({ 'proceso': element.origen, 'v1': element.correctiva, 'v2': element.preventiva, 'v3': element.mejora })
            });
            titulo1 = "correctiva";
            titulo2 = "preventiva";
            titulo3 = "mejora";
        } else if (accion == 7) {
            datos_grafica.forEach(element => {
                data.push({ 'proceso': element.origen, 'v1': element.no_conformidad, 'v2': element.op_mejora, 'v3': element.observacion })
            });
            titulo1 = "no conformidad";
            titulo2 = "oportunidad de mejora";
            titulo3 = "observacion";
        }
        graficar_datos3(data, titulo1, titulo2, titulo3);
        $("#modal_generar_grafica").modal();
    });


    $("#btn_generar_informes").click(function() {
        var valor = document.getElementById("opciones_informes").value;
        var fecha_inicio = document.getElementById("fecha1").value;
        var fecha_fin = document.getElementById("fecha2").value;

        if (fecha_inicio > fecha_fin) {
            MensajeConClase('La fecha inicial no puede ser mayor a la fecha final', 'info', 'Oops!');
        } else {
            if (fecha_inicio == 0 || fecha_fin == 0) {
                MensajeConClase('Los campos de fecha deben ser Diligenciados', 'info', 'Oops!');
            } else {
                if (valor == 0) {
                    MensajeConClase('Debe seleccionar el tipo de Informe', 'info', 'Oops!');
                } else if (valor == 1) {
                    obtener_estado_informes(fecha_inicio, fecha_fin);
                } else if (valor == 2) {
                    obtener_detalle_estado(fecha_inicio, fecha_fin);
                } else if (valor == 3) {
                    obtener_tipo_accion(fecha_inicio, fecha_fin);
                } else if (valor == 4) {
                    obtener_tipo_hallazgo(fecha_inicio, fecha_fin);
                } else if (valor == 5) {
                    obtener_estados_auditoria(fecha_inicio, fecha_fin);
                } else if (valor == 6) {
                    obtener_tipos_origen(fecha_inicio, fecha_fin);
                } else if (valor == 7) {
                    obtener_cumplimiento_estados(fecha_inicio, fecha_fin);
                } else if (valor == 8) {
                    obtener_tipos_procesos(fecha_inicio, fecha_fin);
                } else if (valor == 9) {
                    obtener_hallazgos_origen(fecha_inicio, fecha_fin);
                } else if (valor == 10) {
                    obtener_hallazgos_procesos(fecha_inicio, fecha_fin);
                }
            }
        }
    });

    $('#tbltabla_estados').hide();
    $('#tbltabla_estados2').hide();
    $('#tbltabla_estados3').hide();
    $('#tbltabla_estados4').hide();
    $('#tbltabla_estados5').hide();
    $('#btn_generar_Graficos').hide();
    $('#btn_generar_Graficos2').hide();
    $('#btn_generar_Graficos3').hide();
    $('#torta').hide();
    $('#bar').hide();



    $(".agregar_proceso").click(function() {
        persona_sele = [];
        $('.personas_agregadas').html(`<option value=''> ${persona_sele.length} Personas(s) a Asignar</option>`);
        $("#form_guardar_proceso").get(0).reset();
        $("#modal_nuevo_proceso").modal();
    });

    $("#form_guardar_proceso").submit(e => {
        e.preventDefault();
        guardar_proceso();
        return false;
    });

    $("#form_modificar_proceso").submit(e => {
        e.preventDefault();
        modificar_proceso();
        return false;
    });

    $(".add_funcionario").click(function() {
        filtro = '';
        $("#modal_buscar_auxiliar").modal();
        callback_activo = resp => {
            callback_activo_aux = (resp2) => confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => guardar_funcionario_proceso(resp));
            $("#modal_tipo_persona").modal();
        };
        buscar_empleado("", filtro, callback_activo);
    });

    $("#form_datos_nc").submit(e => {
        e.preventDefault();
        guardar_datos_nc();
        return false;
    });

    $(".add_persona").click(function() {
        filtro = '';
        $("#form_buscar_auxiliar").get(0).reset();
        $("#modal_buscar_auxiliar").modal();
        callback_activo = resp => {
            callback_activo_aux = (resp2) => confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => seleccionar_persona(resp));
            $("#modal_tipo_persona").modal();
        };
        buscar_empleado("", filtro, callback_activo);
    });

    $("#btn_lider").click(function() {
        tipo_persona = 1;
        callback_activo_aux();
        $("#modal_tipo_persona").modal("hide");
    });

    $("#btn_agente").click(function() {
        tipo_persona = 2;
        callback_activo_aux();
        $("#modal_tipo_persona").modal("hide");
    });

    $("#retirar_persona").click(function() {
        confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => retirar_persona_sele(".personas_agregadas"));
    });

    $("#btn_datosgen").click(function() {
        obtener_parametros(210, 'cbxtipoaccion', "Seleccionar Tipo Acción");
        obtener_parametros(211, 'cbxtipohallazgo', "Seleccionar Tipo Hallazgo");
        obtener_parametros(212, 'cbxorigen', "Seleccionar Origen - Fuente");
        ver_formato_nc(id_solicitud_global);
        $("#modal_datos_generales").modal();
    });

    $("#btn_esquema").click(function() {
        $("#id_solicitud_archivo").val(id_solicitud_global);
        $("#tipo_archivo").val(1);
        $("#ver_adjuntos_esquema").removeClass("oculto");
        $("#modal_esquema").modal();
    });

    $("#ver_adjuntos_esquema").click(function() {
        listar_archivos_adjuntos(id_solicitud_global, 1, 'tabla_soportes');
        $("#modal_listar_soportes").modal();
    });

    $("#btn_analisis").click(function() {
        listar_herramienta(id_solicitud_global);
        $("#modal_herramienta").modal();
    });

    $("#asignar_herramienta").click(function() {
        listar_herramienta(id_solicitud_global);
        $("#container_herramienta").removeClass("oculto");
        $("#container_otra_herramienta").addClass("oculto");
        $("#asignar_herramienta").addClass("active");
        $("#asignar_otra").removeClass("active");
        $("#modal_herramienta").modal();
    });

    $(".agregar_herramienta").click(function() {
        callback_activo_aux = (resp) => guardar_herramienta(id_solicitud_global);
        $("#form_agregar_herramienta").get(0).reset();
        $("#modal_add_herramienta").modal();
    });

    $("#form_agregar_herramienta").submit(e => {
        e.preventDefault();
        callback_activo_aux();
        return false;
    });

    $("#asignar_otra").click(function() {
        listar_archivos_adjuntos(id_solicitud_global, 2, 'tabla_otra_herramienta');
        $("#container_otra_herramienta").removeClass("oculto");
        $("#container_herramienta").addClass("oculto");
        $("#asignar_otra").addClass("active");
        $("#asignar_herramienta").removeClass("active");
        $("#ver_adjuntos_esquema").addClass("oculto");
        $("#modal_herramienta").modal();
    });

    $(".agregar_otra_herramienta").click(function() {
        $("#id_solicitud_archivo").val(id_solicitud_global);
        $("#tipo_archivo").val(2);
        $("#modal_esquema").modal();
    });

    $("#btn_correcion").click(function() {
        listar_actividades('listar_correccion', 'tabla_correcciones', id_solicitud_global, id_proceso_global);
        $("#modal_correcciones").modal();
    });

    $("#btn_participantes").click(function() {
        listar_participantes(id_solicitud_global);
        $("#modal_participantes").modal();
    });

    $("#btn_plan").click(function() {
        listar_actividades('listar_plan_accion', 'tabla_plan_accion', id_solicitud_global, id_proceso_global);
        $("#modal_plan_accion").modal();
    });

    $(".add_participante").click(function() {
        $("#txt_actividad").hide();
        $("#form_agregar_actividad").get(0).reset();
        $("#persona_actividad").html(`<option value=''> Asignar Responsable</option>`);
        $("#modal_add_actividades").modal();
        callback_activo_aux = (resp) => guardar_datos('guardar_participante', 'listar_participantes', 'tabla_participantes', 1);
    });

    $(".add_actividad_correcion").click(function() {
        $("#txt_actividad").show();
        $("#form_agregar_actividad").get(0).reset();
        $("#persona_actividad").html(`<option value=''> Asignar Responsable</option>`);
        $("#modal_add_actividades").modal();
        callback_activo_aux = (resp) => guardar_datos('guardar_correccion', 'listar_correccion', 'tabla_correcciones');
    });

    $(".add_actividad_plan").click(function() {
        $("#txt_actividad").show();
        $("#form_agregar_actividad").get(0).reset();
        $("#persona_actividad").html(`<option value=''> Asignar Responsable</option>`);
        $("#modal_add_actividades").modal();
        callback_activo_aux = (resp) => guardar_datos('guardar_plan_accion', 'listar_plan_accion', 'tabla_plan_accion');
    });

    $("#form_agregar_actividad").submit(e => {
        e.preventDefault();
        callback_activo_aux();
        return false;
    });

    $(".add_persona_actividad").click(function() {
        filtro = '';
        $("#form_buscar_auxiliar").get(0).reset();
        $("#modal_buscar_auxiliar").modal();
        callback_activo = resp => {
            confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => seleccionar_persona_actividad(resp));
        };
        buscar_empleado("", filtro, callback_activo);
    });

    $("#retirar_persona_responsable").click(function() {
        confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => retirar_persona_sele(".persona_actividad"));
    });

    $(".add_avance").click(function() {
        $("#modal_agregar_avances").modal();
    });

    $("#form_agregar_avances").submit(e => {
        e.preventDefault();
        callback_activo_aux();
        return false;
    });

    $(".btn_imprimir").click(function() {
        imprimir_nc(id_solicitud_global, data_solicitud_global);
    });

    $("#id_tipo_sol_select").change(function() {
        const tipo_solicitud = $(this).val();
        if (tipo_solicitud == 'Tip_Cal_Aud') {
            $("#id_tipo_select").addClass('oculto');
            $("#id_presentacion_select").addClass('oculto');
            $("#id_cantidad_select").addClass('oculto');
            $("#id_proceso_select").removeClass('oculto');
            $("#id_origen_select").removeClass('oculto');
        } else {
            $("#id_tipo_select").removeClass('oculto');
            $("#id_presentacion_select").removeClass('oculto');
            $("#id_cantidad_select").removeClass('oculto');
            $("#id_proceso_select").addClass('oculto');
            $("#id_origen_select").addClass('oculto');
        }
    });

    $('#btnConfiguraciones').click(async() => {
        $('#form_administrar').get(0).reset();
        $('#modal_administrar').modal();
        id_persona = null;
        listar_actividades_adm(id_persona);
    });

    $('#menu_administrar').click(function() {
        $('#menu_administrar ').removeClass('active');
        $('#form_administrar .btnAgregar').hide();
        $('#s_persona').html('Seleccione Persona');

    });

    $('#s_persona').click(() => {
        $('#modal_elegir_persona').modal();
        listar_personas();
        $('#txt_persona').val('');
    });

    $('#frm_buscar_persona').submit((e) => {
        e.preventDefault();
        const persona = $('#txt_persona').val();
        listar_personas(persona);
    });

    $('#btn_buscar_persona').click(() => $('#frm_buscar_persona').trigger('submit'));

});

const listar_actividades_adm = (persona) => {
    let num = 0;
    consulta_ajax(`${ruta_ambiental}listar_actividades_adm`, { persona }, (data) => {
        $(`#tabla_actividades tbody`)
            .off('click', 'tr')
            .off('click', 'tr span.asignar')
            .off('click', 'tr span.quitar')
            .off('click', 'tr span.config')
            .off('dblclick', 'tr');
        const myTable = $('#tabla_actividades').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [
                { render: () => ++num },
                { data: 'nombre' },
                {
                    render: (data, type, { asignado }, meta) => {
                        let datos = asignado ?
                            '<span class="btn btn-default quitar" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config"><span class="fa fa-cog"></span></span>' :
                            '<span class="btn btn-default asignar"><span class="fa fa-toggle-off" ></span></span> ';
                        return datos;
                    }
                }
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tabla_actividades tbody').on('click', 'tr', function() {
            $('#tabla_actividades tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

        $('#tabla_actividades tbody').on('dblclick', 'tr', function() {
            $('#tabla_actividades tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

        $('#tabla_actividades tbody').on('click', 'tr span.asignar', function() {
            const { asignado, id } = myTable.row($(this).parent()).data();
            asignar_actividad(asignado, id);
        });

        $('#tabla_actividades tbody').on('click', 'tr span.quitar', function() {
            const { asignado, id } = myTable.row($(this).parent()).data();
            quitar_actividad(asignado, id);
        });

        $('#tabla_actividades tbody').on('click', 'tr span.config', function() {
            const { asignado, id } = myTable.row($(this).parent()).data();
            actividad_selec = asignado;
            $('#modal_elegir_estado').modal();
            listar_estados(asignado);
        });
    });

    const asignar_actividad = (asignado, id) => {
        consulta_ajax(
            `${ruta_ambiental}asignar_actividad`, { id, persona: id_persona, asignado },
            ({ mensaje, tipo, titulo }) => {
                MensajeConClase(mensaje, tipo, titulo);
                listar_actividades_adm(id_persona);
            }
        );
    };

    const quitar_actividad = (asignado, id) => {
        swal({
                title: 'Desasignar Actividad',
                text: 'Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D9534F',
                confirmButtonText: 'Si, Entiendo!',
                cancelButtonText: 'No, cancelar!',
                allowOutsideClick: true,
                closeOnConfirm: false,
                closeOnCancel: true
            },
            (isConfirm) => {
                if (isConfirm) {
                    consulta_ajax(
                        `${ruta_ambiental}quitar_actividad`, { id, persona: id_persona, asignado },
                        ({ mensaje, tipo, titulo }) => {
                            listar_actividades_adm(id_persona);
                        }
                    );
                    swal.close();
                } else MensajeConClase(mensaje, tipo, titulo);
            }
        );
    };
};

const listar_estados = (actividad) => {
    const desasignar =
        '<span class="btn btn-default desasignar" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
    const asignar =
        '<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
    const notificar =
        '<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
    const no_notificar =
        '<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
    consulta_ajax(`${ruta_ambiental}listar_estados`, { actividad, persona: id_persona }, (data) => {
        $(`#tabla_estados tbody`)
            .off('click', 'tr')
            .off('click', 'tr span.asignar')
            .off('click', 'tr span.desasignar')
            .off('click', 'tr span.no_notificar')
            .off('click', 'tr span.notificar')
            .off('dblclick', 'tr');
        const myTable = $('#tabla_estados').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [
                { data: 'parametro' },
                { data: 'nombre' },
                {
                    render: (data, type, { asignado, notificacion }, meta) => {
                        return asignado ?
                            notificacion == 1 ? desasignar + no_notificar : desasignar + notificar :
                            asignar;
                    }
                }
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tabla_estados tbody').on('click', 'tr', function() {
            $('#tabla_estados tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

        $('#tabla_estados tbody').on('click', 'tr span.asignar', function() {
            const { estado } = myTable.row($(this).parent()).data();
            asignar_estado(estado, actividad_selec, id_persona);
        });

        $('#tabla_estados tbody').on('click', 'tr span.desasignar', function() {
            const { asignado, estado } = myTable.row($(this).parent()).data();
            quitar_estado(estado, actividad_selec, id_persona, asignado);
        });

        $('#tabla_estados tbody').on('click', 'tr span.notificar', function() {
            const { estado } = myTable.row($(this).parent()).data();
            activar_notificacion(estado, actividad_selec, id_persona);
        });

        $('#tabla_estados tbody').on('click', 'tr span.no_notificar', function() {
            const { estado } = myTable.row($(this).parent()).data();
            desactivar_notificacion(estado, actividad_selec, id_persona);
        });


        const activar_notificacion = (estado, actividad, persona) => {
            consulta_ajax(`${ruta_ambiental}activar_notificacion`, { estado, actividad, persona }, ({ mensaje, tipo, titulo }) =>
                listar_estados(actividad)
            );
        };

        const desactivar_notificacion = (estado, actividad, persona) => {
            consulta_ajax(
                `${ruta_ambiental}desactivar_notificacion`, { estado, actividad, persona },
                ({ mensaje, tipo, titulo }) => listar_estados(actividad)
            );
        };

        const asignar_estado = (estado, actividad, persona) => {
            consulta_ajax(`${ruta_ambiental}asignar_estado`, { estado, actividad, persona }, ({ mensaje, tipo, titulo }) =>
                listar_estados(actividad)
            );
        };

        const quitar_estado = (estado, actividad, persona, id) => {
            consulta_ajax(`${ruta_ambiental}quitar_estado`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) =>
                listar_estados(actividad)
            );
        };
    });
};

const listar_personas = (texto = '') => {
    consulta_ajax(`${ruta_ambiental}listar_personas`, { texto }, (data) => {
        $(`#tabla_personas tbody`).off('click', 'tr').off('click', 'tr span.asignar').off('dblclick', 'tr');
        const myTable = $('#tabla_personas').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [
                { data: 'fullname' },
                {
                    render: (data, type, full, meta) =>
                        '<span class="btn btn-default asignar" title="Seleccionar Persona" style="color: #5cb85c"><span class="fa fa-check"></span></span>'
                }
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tabla_personas tbody').on('click', 'tr', function() {
            $('#tabla_personas tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

        $('#tabla_personas tbody').on('click', 'tr span.asignar', function() {
            let { id, fullname } = myTable.row($(this).parent().parent()).data();
            id_persona = id;
            $('#modal_elegir_persona').modal('hide');
            $('#s_persona').html(fullname);
            listar_actividades_adm(id);
        });

        $('#tabla_personas tbody').on('dblclick', 'tr', function() {
            let { id, fullname } = myTable.row($(this)).data();
            id_persona = id;
            $('#modal_elegir_persona').modal('hide');
            $('#s_persona').html(fullname);
            listar_actividades_adm(id);
        });
    });
};

const listar_lotes = () => {
    $("#tabla_lotes tbody")
        .off("click", "tr")
        .off("dblclick", "tr")
        .off("click", "tr td:nth-of-type(1)")
        .off("click", "tr td .enviar")
        .off("click", "tr td .remitir")
        .off("click", "tr td .finalizar");
    consulta_ajax(`${ruta_ambiental}listar_lotes`, { id: 0 }, resp => {
        const myTable = $("#tabla_lotes").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "ver"
                },
                {
                    data: "id"
                },
                {
                    data: "numero_remision"
                },
                {
                    data: "empresa"
                },
                {
                    data: "no_solicitudes"
                },
                {
                    data: "estado_lote"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $("#tabla_lotes tbody").on("click", "tr", function() {
            $("#tabla_lotes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
            id_lote_global = data.id
        });

        $("#tabla_lotes tbody").on("dblclick", "tr", function() {
            let data = myTable.row(this).data();
            id_lote_global = data.id
            ver_detalle_lote(data);
            $("#modal_solicitudes_lote").modal();
        });

        $("#tabla_lotes tbody").on("click", "tr td:nth-of-type(1)", function() {
            let data = myTable.row($(this).parent()).data();
            id_lote_global = data.id
            ver_detalle_lote(data);
            $("#modal_solicitudes_lote").modal();
        });

        $("#tabla_lotes tbody").on("click", "tr td .enviar", function() {
            let { id, empresa, correo_empresa, no_solicitudes } = myTable.row($(this).parent()).data();
            id_lote_global = id;
            empresa_global = {
                'nombre': empresa,
                'correo': correo_empresa
            }
            if (no_solicitudes < 1) MensajeConClase('No puede enviar un lote sin solicitudes asignadas', 'info', 'Oops!');
            else $("#modal_formulario_empresa").modal();
        });

        $("#tabla_lotes tbody").on("click", "tr td .remitir", function() {
            let { id } = myTable.row($(this).parent()).data();
            id_lote_global = id;
            $("#modal_formulario_remision").modal();
        });

        $("#tabla_lotes tbody").on("click", "tr td .finalizar", function() {
            let { id } = myTable.row($(this).parent()).data();
            id_lote_global = id;
            $("#modal_finalizar").modal();
        });
    });
}


const listar_lotes_activos = (id_lote) => {
    $("#tabla_lotes_activos tbody")
        .off("click", "tr td .agregar")
        .off("click", "tr td .eliminar");
    consulta_ajax(`${ruta_ambiental}listar_lotes_activos`, { id: id_solicitud_global, id_lote }, resp => {
        const myTable = $("#tabla_lotes_activos").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "id"
                },
                {
                    data: "empresa"
                },
                {
                    data: "persona_registra"
                },
                {
                    data: "fecha_registro"
                },
                {
                    data: "no_solicitudes"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $("#tabla_lotes_activos tbody").on("click", "tr td .agregar", function() {
            let { id } = myTable.row($(this).parent()).data();
            asignacion(id, 1);
        });

        $("#tabla_lotes_activos tbody").on("click", "tr td .eliminar", function() {
            let { id } = myTable.row($(this).parent()).data();
            asignacion(id, 0);
        });
    });
}

const ver_detalle_lote = data => {
    let {
        persona_registra,
        fecha_registro,
        estado_lote,
        empresa,
        formulario,
        numero_remision,
        certificado,
    } = data;
    $("#cont_formulario").hide();
    $("#cont_remision").hide();
    $("#cont_certificado").hide();
    $(".creador").html(persona_registra);
    $(".fecha_registra").html(fecha_registro);
    $(".estado").html(estado_lote);
    $(".empresa").html(empresa);
    if (formulario) $("#cont_formulario").show();
    if (numero_remision) $("#cont_remision").show();
    if (certificado) $("#cont_certificado").show();
    $(".formulario").html(`<a target='_blank' href="${Traer_Server()}${ruta_adjuntos}${formulario}">Formato empresa</a>`);
    $(".no_remision").html(numero_remision);
    $(".certificado").html(`<a target='_blank' href="${Traer_Server()}${ruta_adjuntos}${certificado}">Certificado</a>`);

    let id_lote = data.id
    $("#tabla_solicitudes_agrupadas tbody")
        .off("click", "tr")
        .off("dblclick", "tr")
        .off("click", "tr td:nth-of-type(1)")
        .off("click", "tr td .agregar")
        .off("click", "tr td .eliminar");
    consulta_ajax(`${ruta_ambiental}listar_solicitudes`, { id: 0, id_lote }, resp => {
        let i = 0;
        const myTable = $("#tabla_solicitudes_agrupadas").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "residuo_estado"
                },
                {
                    data: "solicitante"
                },
                {
                    data: "fecha_registra"
                },
                {
                    data: "estado_solicitud"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $("#tabla_solicitudes_agrupadas tbody").on("click", "tr", function() {
            $("#tabla_solicitudes_agrupadas tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
            id_solicitud_global = data.id
        });

        $("#tabla_solicitudes_agrupadas tbody").on("dblclick", "tr", function() {
            let data = myTable.row(this).data();
            ver_detalle_solicitud(data);
            id_solicitud_global = data.id
        });

        $("#tabla_solicitudes_agrupadas tbody").on("click", "tr td:nth-of-type(1)", function() {
            let data = myTable.row($(this).parent()).data();
            ver_detalle_solicitud(data);
            id_solicitud_global = data.id
        });

        $("#tabla_solicitudes_agrupadas tbody").on("click", "tr td .eliminar", function() {
            let data = myTable.row($(this).parent()).data();
            id_solicitud_global = data.id;
            asignacion(data.id_lote, 0);
            ver_detalle_lote(data);
            listar_lotes()
        });
    });
}

const listar_solicitudes = (id = 0, filtros = {}) => {
    let {
        id_tipo_solicitud,
        id_tipo_residuo,
        id_estado_solicitud,
        id_presentacion_residuo,
        id_cantidad_residuo,
        id_tipo_proceso,
        id_origen_proceso,
        fecha_inicial,
        fecha_final
    } = filtros
    $("#tabla_solicitudes tbody")
        .off("click", "tr")
        .off("dblclick", "tr")
        .off("click", "tr td:nth-of-type(1)")
        .off("click", "tr td .cancelar")
        .off("click", "tr td .asignar")
        .off("click", "tr td .confirmar")
        .off("click", "tr td .negar")
        .off("click", "tr td .agrupar")
        .off("click", "tr td .gestionar")
        .off("click", "tr td .en_proceso")
        .off("click", "tr td .finalizar");
    consulta_ajax(`${ruta_ambiental}listar_solicitudes`, { id, id_tipo_residuo, id_estado_solicitud, id_presentacion_residuo, id_cantidad_residuo, id_tipo_proceso, id_origen_proceso, id_tipo_solicitud, fecha_inicial, fecha_final }, resp => {
        const myTable = $("#tabla_solicitudes").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "ver"
                },
                {
                    data: "tipo_solicitud_nombre"
                },
                {
                    render: function(data, type, row, meta) {
                        if (row.proceso) {
                            return `${row.proceso}`;
                        } else {
                            return `${row.ubicacion_bloque} - ${row.ubicacion_salon}`;
                        }
                    }
                },
                {
                    data: "solicitante"
                },
                {
                    data: "fecha_registra"
                },
                {
                    data: "estado_solicitud"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $("#tabla_solicitudes tbody").on("click", "tr", function() {
            $("#tabla_solicitudes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
            data_solicitud_global = data;
            id_solicitud_global = data.id;
        });

        $("#tabla_solicitudes tbody").on("dblclick", "tr", function() {
            let data = myTable.row(this).data();
            if (data.tipo_solicitud == 'Tip_Cal_Aud') ver_detalle_auditoria(data);
            else ver_detalle_solicitud(data);
            id_solicitud_global = data.id
        });

        $("#tabla_solicitudes tbody").on("click", "tr td:nth-of-type(1)", function() {
            let data = myTable.row($(this).parent()).data();
            id_solicitud_global = data.id;
            data_solicitud_global = data;
            if (data.tipo_solicitud == 'Tip_Cal_Aud') ver_detalle_auditoria(data);
            else ver_detalle_solicitud(data);
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .cancelar", function() {
            let { id } = myTable.row($(this).parent()).data();
            gestionar_solicitud(id, 'Est_Cal_Can');
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .asignar", function() {
            let { id } = myTable.row($(this).parent()).data();
            id_solicitud_global = id;
            $("#modal_asignar_solicitud").modal();
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .confirmar", function() {
            let { id, tipo_solicitud } = myTable.row($(this).parent()).data();
            if (tipo_solicitud == 'Tip_Cal_Re') gestionar_solicitud(id, 'Est_Cal_Rec');
            else confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => gestionar_solicitud(id, 'Est_Cal_Conf'));
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .negar", function() {
            let { id } = myTable.row($(this).parent()).data();
            gestionar_solicitud(id, 'Est_Cal_Neg');
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .agrupar", function() {
            let { id, id_lote } = myTable.row($(this).parent()).data();
            id_solicitud_global = id;
            listar_lotes_activos(id_lote);
            $("#modal_lote_solicitud").modal();
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .gestionar", function() {
            let { id, id_proceso } = myTable.row($(this).parent()).data();
            id_solicitud_global = id;
            id_proceso_global = id_proceso;
            confirmar_accion_general(`Está a un paso de iniciar proceso de gestión de la solicitud, Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => gestionar_solicitud(id, 'Est_Cal_Pro'));
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .en_proceso", function() {
            let { id, id_proceso } = myTable.row($(this).parent()).data();
            id_solicitud_global = id;
            id_proceso_global = id_proceso;
            validar_permiso_usuario(id_proceso_global);
        });

        $("#tabla_solicitudes tbody").on("click", "tr td .finalizar", function() {
            let { id } = myTable.row($(this).parent()).data();
            confirmar_accion_general(`Está a un paso de finalizar la solicitud, Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => gestionar_solicitud(id, 'Est_Cal_Fin'));
        });
    });
};

const validar_permiso_usuario = async(id_proceso) => {
    let user = await validar_usuario(id_proceso);
    if (user.tipo == 2) {
        $("#btn_datosgen").addClass("oculto");
        $("#btn_esquema").addClass("oculto");
        $("#btn_analisis").addClass("oculto");
        $("#btn_participantes").addClass("oculto");
        $(".add_actividad_correcion").addClass("oculto");
        $(".add_actividad_plan").addClass("oculto");
    }
    $("#modal_menu_formato_nc").modal();
}

const validar_usuario = id_proceso => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}validar_usuario`;
        consulta_ajax(url, { id_proceso }, resp => {
            resolve(resp);
        });
    });
}

const crear_solicitud = () => {
    let fordata = new FormData(document.getElementById("form_agregar_solicitud"));
    enviar_formulario(`${ruta_ambiental}crear_solicitud`, fordata, resp => {
        let { mensaje, tipo, titulo } = resp;
        if (tipo == "success") {
            $("#form_agregar_solicitud")
                .get(0)
                .reset();
            $("#modal_agregar_solicitud").modal("hide");
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
};

const modificar_solicitud = () => {
    let fordata = new FormData(document.getElementById("form_modificar_solicitud"));
    fordata.append('id_solicitud', id_solicitud_global);
    enviar_formulario(`${ruta_ambiental}modificar_solicitud`, fordata, resp => {
        let { mensaje, tipo, titulo } = resp;
        if (tipo == "success") {
            $("#modal_agregar_solicitud").modal("hide");
            MensajeConClase(mensaje, tipo, titulo);
            listar_solicitudes();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const asignar_solicitud = () => {
    let fordata = new FormData(document.getElementById("form_asignar_solicitud"));
    let data = formDataToJson(fordata);
    data.id_auxiliar = auxiliar.id;
    data.estado_nuevo = 'Est_Cal_Asig';
    data.id = id_solicitud_global;
    consulta_ajax(`${ruta_ambiental}gestionar_solicitud`, data, resp => {
        let { mensaje, tipo, titulo } = resp;
        if (tipo == "success") {
            $("#form_asignar_solicitud")
                .get(0)
                .reset();
            $("#modal_asignar_solicitud").modal("hide");
            enviar_correo(data);
            MensajeConClase(mensaje, tipo, titulo);
            listar_solicitudes();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    })
}

const crear_lote = () => {
    let fordata = new FormData(document.getElementById("form_crear_lote"));
    let data = formDataToJson(fordata);
    consulta_ajax(`${ruta_ambiental}crear_lote`, data, resp => {
        let { mensaje, tipo, titulo } = resp;
        if (tipo == "success") {
            $("#form_crear_lote")
                .get(0)
                .reset();
            $("#modal_crear_lote").modal("hide");
            MensajeConClase(mensaje, tipo, titulo);
            listar_lotes();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    })
}

const obtener_valor_parametro = id => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}obtener_valor_parametro`;
        consulta_ajax(url, { id }, resp => {
            resolve(resp);
        });
    });
};

const obtener_permisos_parametro = id => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}obtener_permisos_parametro`;
        consulta_ajax(url, { id }, resp => {
            resolve(resp);
        });
    });
}

const pintar_datos_combo = (datos, combo, mensaje, tipo = 0) => {
    $(combo).html(`<option value=''> ${mensaje}</option>`);
    datos.forEach(element => {
        if (tipo) {
            $(combo).append(
                `<option value='${element.id_aux}'> ${element.valor} </option>`
            );
        } else {
            $(combo).append(
                `<option value='${element.id}'> ${element.valor} </option>`
            );
        }
    });
};

const obtener_estados_residuos = async(select, mensaje) => {
    let estados = await obtener_valor_parametro(138);
    pintar_datos_combo(
        estados,
        `.${select}`,
        mensaje
    );
};

const obtener_tipos_solicitud = async(select, mensaje) => {
    let tipos = await obtener_valor_parametro(142);
    pintar_datos_combo(
        tipos,
        `.${select}`,
        mensaje,
        1
    )
}

const obtener_estados = async() => {
    let estados = await obtener_valor_parametro(137);
    pintar_datos_combo(
        estados,
        ".cbxestados",
        "Filtrar por Estado",
        1
    );
};

const obtener_cantidades_residuos = async(select, mensaje) => {
    let cantidad = await obtener_valor_parametro(139);
    pintar_datos_combo(
        cantidad,
        `.${select}`,
        mensaje
    );
};

const obtener_presentaciones_residuos = async(select, mensaje) => {
    let presentaciones = await obtener_valor_parametro(140);
    pintar_datos_combo(
        presentaciones,
        `.${select}`,
        mensaje
    );
};

const obtener_bloques = async() => {
    let bloques = await obtener_valor_parametro(115);
    pintar_datos_combo(
        bloques,
        ".cbxbloque",
        "Seleccione el bloque"
    );
}

const obtener_bloque_salon = async bloque => {
    let salones = await obtener_permisos_parametro(bloque);
    pintar_datos_combo(
        salones,
        ".cbxsalon",
        "Seleccione el salon"
    );
}

const obtener_empresas = async() => {
    let empresas = await obtener_valor_parametro(141);
    pintar_datos_combo(
        empresas,
        ".cbxempresas",
        "Seleccione la empresa"
    )
}

const obtener_info_lote = id => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}consultar_lote_id`;
        consulta_ajax(url, { id }, resp => {
            resolve(resp);
        });
    });
}

const ver_detalle_solicitud = data => {
    $("#modal_detalle_solicitud").modal();
    let {
        solicitante,
        fecha_registra,
        residuo_estado,
        estado_solicitud,
        presentacion_text,
        cantidad,
        activo,
        carta_activo,
        descripcion,
        id_estado,
        auxiliar,
        fecha_asignacion,
        ubicacion_bloque,
        ubicacion_salon,
        id_lote,
        tipo_cantidad_text
    } = data;
    $("#cont_activo").hide();
    $("#tabla_asignacion").hide();
    $("#cont_lote").hide();
    $(".solicitante").html(solicitante);
    $(".residuo_estado").html(residuo_estado);
    $(".fecha_registro").html(fecha_registra);
    $(".estado").html(estado_solicitud);
    $(".presentacion").html(presentacion_text);
    $(".cantidad").html(`${cantidad} ${tipo_cantidad_text}`);
    $(".descripcion").html(descripcion);
    $(".ubicacion").html(`${ubicacion_bloque} - ${ubicacion_salon}`);
    if (activo == 1) $("#cont_activo").show();
    $(".carta_activo").html(`<a target='_blank' href="${Traer_Server()}${ruta_adjuntos}${carta_activo}">Carta formato activo</a>`);
    if (id_estado != 'Est_Cal_Sol' && id_estado != 'Est_Cal_Can') $("#tabla_asignacion").show();
    $(".auxiliar").html(auxiliar);
    $(".fecha_asignacion").html(fecha_asignacion);
    if (id_lote) $("#cont_lote").show();
    $(".lote").html(`<span style="background-color: white; width: fit-content;" class="pointer form-control" id="btn_lote_detalle"><span>${id_lote}</span></span>`);

    $("#btn_lote_detalle").on("click", async() => {
        let data_lote = await obtener_info_lote(id_lote);
        ver_detalle_lote(data_lote);
        $("#modal_solicitudes_lote").modal();
    })
}

const listar_historial_estados = () => {
    $("#modal_historial_solicitud").modal();
    let id_solicitud = id_solicitud_global;
    consulta_ajax(`${ruta_ambiental}listar_historial_estados`, { id_solicitud }, resp => {
        let i = 0;
        const myTable = $("#tabla_estado_solicitud").DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "estado"
                },
                {
                    data: "fecha_registro"
                },
                {
                    data: "nombre_completo"
                },
                {
                    data: "observacion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });
    });
}

const listar_historial_estados_lotes = () => {
    $("#modal_historial_lote").modal();
    let id_lote = id_lote_global;
    consulta_ajax(`${ruta_ambiental}listar_historial_estados_lote`, { id_lote }, resp => {
        let i = 0;
        const myTable = $("#tabla_estado_lote").DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "estado"
                },
                {
                    data: "fecha_registro"
                },
                {
                    data: "nombre_completo"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });
    });
}

const buscar_empleado = (dato, filtro, callback, id_persona = '') => {
    consulta_ajax(`${ruta_ambiental}buscar_empleado`, { dato, filtro, id_persona }, resp => {
        $("#tabla_auxiliar_busqueda tbody")
            .off("dblclick", "tr")
            .off("click", "tr")
            .off("click", "tr td:nth-of-type(1)")
            .off("click", "tr td .asignar");
        let i = 0;
        const myTable = $("#tabla_auxiliar_busqueda").DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "nombre_completo"
                },
                {
                    data: "identificacion"
                },
                {
                    defaultContent: '<span style="color: #39B23B;" title="Asignar empleado" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>'
                }
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });

        $(`#tabla_auxiliar_busqueda tbody`).on("click", "tr", function() {
            $(`#tabla_auxiliar_busqueda tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_auxiliar_busqueda tbody").on("click", "tr td .asignar", function() {
            let data = myTable.row($(this).parent().parent()).data();
            callback(data, $(this).parent().parent());
            $('#form_buscar_auxiliar').get(0).reset();

        });
    });
}

const mostrar_nombre_persona = (data) => {
    let { id, nombre_completo, correo } = data;
    auxiliar = { id, nombre_completo, correo };
    $(container_activo).val(nombre_completo);
    $("#modal_buscar_auxiliar").modal("hide");
};

const gestionar_solicitud = (id, estado_nuevo, origen = 2) => {
    let observacion = ""

    const gestionar_solicitud_texto = () => {
        swal({
            title: "¿ Negar ?",
            text: "",
            type: "input",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Aceptar!",
            cancelButtonText: "Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            inputPlaceholder: `Ingrese la razon por la que se niega la recolección`
        }, function(message) {
            if (message === false) return false;
            if (message === "") swal.showInputError(`Debe Ingresar una razón.`);
            else {
                observacion = message;
                gestionar();
            }
        });
    }

    const getsionar_solicitud_normal = () => {
        swal({
                title: `¿Cancelar Solicitud?`,
                text: `¿ Estas seguro que deseas cancelar la solicitud ?, una vez realizada esta acción debera realizar otra solicitud si aun desea realizar el proceso, si esta deacuerdo pulse en el botón "Si, Entiendo!", caso contrario pulse en el botón "No, Regresar!"`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#D9534F",
                confirmButtonText: "Si, Entiendo!",
                cancelButtonText: "No, Regresar!",
                allowOutsideClick: true,
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    consulta_ajax(`${ruta_ambiental}gestionar_solicitud`, { id, estado_nuevo }, resp => {
                        let = { titulo, mensaje, tipo } = resp;
                        if (tipo != "success") {
                            MensajeConClase(mensaje, tipo, titulo);
                        } else {
                            swal.close();
                            listar_solicitudes();
                        }
                    });
                }
            }
        );
    }

    const gestionar = () => {
        consulta_ajax(`${ruta_ambiental}gestionar_solicitud`, { id, estado_nuevo, observacion, origen }, resp => {
            let { mensaje, tipo, titulo } = resp;
            if (tipo == "success") {
                if (estado_nuevo != "Est_Cal_Pro") MensajeConClase(mensaje, tipo, titulo);
                if (origen == 1) setTimeout("location.reload(true);", 1500);
                else {
                    listar_solicitudes();
                    if (estado_nuevo == "Est_Cal_Pro") {
                        swal.close();
                        validar_permiso_usuario(id_proceso_global);
                    }
                }
            } else {
                MensajeConClase(mensaje, tipo, titulo);
            }
        });
    }

    if (estado_nuevo == 'Est_Cal_Neg') gestionar_solicitud_texto();
    if (estado_nuevo == 'Est_Cal_Rec' || estado_nuevo == 'Est_Cal_Conf' || estado_nuevo == 'Est_Cal_Fin' || estado_nuevo == "Est_Cal_Pro") gestionar();
    if (estado_nuevo == 'Est_Cal_Can') getsionar_solicitud_normal();

}

const enviar_correo = async data => {
    let { id, fecha_recoleccion } = data;
    let ser = `<a href="${server}index.php/calidad/asignacion/${id}"><b>agil.cuc.edu.co</b></a>`;
    let tipo = 1;
    let titulo = 'Ambiental - Asignacion';
    let correos = auxiliar.correo;
    let nombre = auxiliar.nombre_completo;
    let body = `Se informa que se le fue asignado una solicitud de Gestión de residuos para la fecha de ${fecha_recoleccion}.
	<br><br>
	Para ver la información de la solicitud, haga click en siguiente enlace:
	<br><br>
	${ser}`;
    enviar_correo_personalizado("Cal", body, correos, nombre, "Ambiental", titulo, "ParCodAdm", tipo);
}

const enviar_formulario_correo = () => {
    let fordata = new FormData(document.getElementById("form_formulario_empresa"));
    let data = formDataToJson(fordata);
    fordata.append('id', id_lote_global);
    enviar_formulario(`${ruta_ambiental}enviar_lote`, fordata, resp => {
        let { mensaje, tipo, titulo, archivo } = resp;
        if (tipo == "success") {
            $("#form_formulario_empresa")
                .get(0)
                .reset();
            $("#modal_formulario_empresa").modal("hide");
            MensajeConClase(mensaje, tipo, titulo);
            enviar_correo_empresa(archivo);
            listar_lotes();
            listar_solicitudes();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });

    const enviar_correo_empresa = (archivo) => {
        let ser = `<a href="${server}index.php/calidad/"><b>agil.cuc.edu.co</b></a>`;
        let tipo = 1;
        let titulo = 'Formulario de recolección';
        let correos = empresa_global.correo;
        let nombre = empresa_global.nombre;
        let body = `${data.mensaje}`;
        enviar_correo_personalizado("Cal", body, correos, nombre, "Calidad CUC", titulo, "ParCodAmb", tipo, [`../${ruta_adjuntos}${archivo}`, data.formulario_empresa.name], true)
    }
}

const asignacion = (id_lote, accion) => {
    consulta_ajax(`${ruta_ambiental}agrupar_solicitud`, { id_solicitud: id_solicitud_global, id_lote, accion }, resp => {
        let = { titulo, mensaje, tipo, id_lote_actual } = resp;
        if (tipo != "success") {
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
            listar_lotes_activos(id_lote_actual);
        }
    });
}

const gestionar_lote = (estado_nuevo, form) => {
    let fordata = new FormData(document.getElementById(`${form}`));
    fordata.append('id', id_lote_global);
    fordata.append('estado_nuevo', estado_nuevo)
    enviar_formulario(`${ruta_ambiental}gestionar_lote`, fordata, resp => {
        let { mensaje, tipo, titulo } = resp;
        if (tipo == "success") {
            $(`#${form}`).get(0).reset();
            $("#modal_formulario_remision").modal("hide");
            $("#modal_finalizar").modal("hide");
            MensajeConClase(mensaje, tipo, titulo);
            listar_lotes();
            listar_solicitudes();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const filtrar_solicitudes = () => {
    data = {
        'id_tipo_solicitud': $("#id_tipo_sol_select").val(),
        'id_tipo_residuo': $("#modal_crear_filtros select[name='id_tipo_residuo']").val(),
        'id_estado_solicitud': $("#modal_crear_filtros select[name='id_estado_solicitud']").val(),
        'id_presentacion_residuo': $("#modal_crear_filtros select[name='id_presentacion_residuo']").val(),
        'id_cantidad_residuo': $("#modal_crear_filtros select[name='id_cantidad_residuo']").val(),
        'id_tipo_proceso': $("#modal_crear_filtros select[name='id_tipo_proceso']").val(),
        'id_origen_proceso': $("#modal_crear_filtros select[name='id_origen_proceso']").val(),
        'fecha_inicial': $("#modal_crear_filtros input[name='fecha_inicial']").val(),
        'fecha_final': $("#modal_crear_filtros input[name='fecha_final']").val()
    }
    listar_solicitudes(0, data);
}

const obtener_parametros = async(idparametro, select, mensaje) => {
    let valor = await obtener_valor_parametro(idparametro);
    pintar_datos_combo(
        valor,
        `.${select}`,
        mensaje
    );
};

const ver_formato_nc = async(id_solicitud) => {
    let data = await consultar_nc(id_solicitud);
    let { id_tipo_accion, id_tipo_hallazgo, id_origen, descripcion } = data;
    $("#tipo_accion").val(id_tipo_accion);
    $("#tipo_hallazgo").val(id_tipo_hallazgo);
    $("#origen_fuente").val(id_origen);
    $("#txt_descripcion_pro").val(descripcion);
}

const consultar_nc = id_solicitud => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}consultar_nc`;
        consulta_ajax(url, { id_solicitud }, resp => {
            resolve(resp);
        });
    });
}

const crear_solicitud_auditoria = () => {
    let fordata = new FormData(document.getElementById("form_agregar_sol_auditoria"));
    enviar_formulario(`${ruta_ambiental}crear_solicitud_auditoria`, fordata, resp => {
        let { mensaje, tipo, titulo, id, proceso } = resp;
        if (tipo == "success") {
            $("#form_agregar_sol_auditoria").get(0).reset();
            $("#modal_agregar_sol_auditoria").modal("hide");
            enviar_correo_auditoria(id, proceso);
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const enviar_correo_auditoria = async(id, proceso) => {
    let ser = `<a href="${server}index.php/calidad"><b>agil.cuc.edu.co</b></a>`;
    let tipo = 3;
    let titulo = 'Planes de acción - Asignacion';
    let correos = await listar_funcionarios(proceso);
    let body = `Se informa que le fue asignado una solicitud de Planes de Acción.
	<br><br>
	Para ver la información de la solicitud, haga click en siguiente enlace:
	<br><br>
	${ser}`;
    enviar_correo_personalizado("Cal", body, correos, "", "Planes de acción", titulo, "ParCodAdm", tipo);
}

const ver_detalle_auditoria = async(data) => {
    let {
        solicitante,
        fecha_registra,
        residuo_estado,
        estado_solicitud,
        descripcion,
        proceso,
        id_proceso
    } = data;
    $(".lider").html('');
    $(".agente_cambio").html('');
    $(".solicitante").html(solicitante);
    $(".residuo_estado").html(residuo_estado);
    $(".fecha_registro").html(fecha_registra);
    $(".estado").html(estado_solicitud);
    $(".descripcion").html(descripcion);
    $(".proceso").html(proceso);
    let datos = await listar_funcionarios(id_proceso);
    if (datos) {
        datos.forEach(element => {
            if (element.id_tipo == 1) {
                $(".lider").html(`${element.nombre_completo}`);
            } else {
                if (element.id_tipo == 2) $(".agente_cambio").html(`${element.nombre_completo}`);
            }
        });
    }
    listar_actividades_imp('listar_correccion', 'tabla_correcciones_imp', id_solicitud_global, id_proceso);
    listar_actividades_imp('listar_plan_accion', 'tabla_plan_imp', id_solicitud_global, id_proceso);
    listar_participantes_imp('listar_participantes', 'tabla_participantes_imp', id_solicitud_global);
    listar_participantes_imp('listar_archivos_adjuntos', 'tabla_soporte_imp', id_solicitud_global, 2);
    listar_participantes_imp('listar_archivos_adjuntos', 'tabla_esquema_imp', id_solicitud_global, 1);
    listar_herramienta_imp(id_solicitud_global);
    $("#modal_detalle_solicitud_aud").modal();
}

const listar_funcionarios = id_proceso => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}listar_funcionarios`;
        consulta_ajax(url, { id_proceso }, resp => {
            resolve(resp);
        });
    });
}

const seleccionar_persona = (data, thiss) => {
    // persona_sele.length=0;
    data.tipo_persona = tipo_persona;
    $("#modal_buscar_auxiliar").modal("hide")
    if (!$(thiss).hasClass("warning")) {
        $(thiss).attr("class", "warning");
        persona_sele.push(data);
    } else {
        $(thiss).removeClass("warning");
        var i = persona_sele.indexOf(data);
        persona_sele.splice(i, 1);
    }
    swal.close();
    $('.personas_agregadas').html(`<option value=''> ${persona_sele.length} Personas(s) a Asignar</option>`);
    persona_sele.forEach(element => {
        $('.personas_agregadas').append(
            `<option value='${element.id}'> ${element.nombre} ${element.apellido} ${element.segundo_apellido}</option>`
        );
    });
}

const seleccionar_persona_actividad = (data, thiss) => {
    persona_sele.length = 0;
    $("#modal_buscar_auxiliar").modal("hide");
    if (!$(thiss).hasClass("warning")) {
        $(thiss).attr("class", "warning");
        persona_sele.push(data);
    } else {
        $(thiss).removeClass("warning");
        var i = persona_sele.indexOf(data);
        persona_sele.splice(i, 1);
    }
    swal.close();
    $('.persona_actividad').html(`<option value=''>Responsable Asignado</option>`);
    persona_sele.forEach(element => {
        $('.persona_actividad').append(
            `<option value='${element.id}' selected> ${element.nombre} ${element.apellido} ${element.segundo_apellido}</option>`
        );
    });

    $("#modal_buscar_auxiliar").modal("hide");
}

const retirar_persona_sele = (combo) => {
    let idpersona = $(combo).val();
    if (idpersona.length == 0) {
        MensajeConClase("Seleccione la Persona a Retirar..!", "info", "Oops...")
    } else {
        for (var i = 0; i < persona_sele.length; i++) {
            if (persona_sele[i].id == idpersona) {
                persona_sele.splice(i, 1);
                swal.close();
                $('.personas_agregadas').html(`<option value=''> ${persona_sele.length} Personas(s) a Asignar</option>`);
                persona_sele.forEach(element => {
                    $('.personas_agregadas').append(
                        `<option value='${element.id}'> ${element.nombre} ${element.apellido} ${element.segundo_apellido}</option>`
                    );
                });
            }
        }
        MensajeConClase("No fue posible retirar.!!", "info", "Oops..!");
    }
}

const guardar_datos_nc = () => {
    let fordata = new FormData(document.getElementById("form_datos_nc"));
    fordata.append('id_solicitud', id_solicitud_global);
    enviar_formulario(`${ruta_ambiental}guardar_datos_nc`, fordata, resp => {
        let { titulo, mensaje, tipo } = resp;
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const guardar_proceso = () => {
    if (persona_sele.length > 0) {
        let fordata = new FormData(document.getElementById("form_guardar_proceso"));
        let data = formDataToJson(fordata);
        data.personas_agregadas = persona_sele;
        consulta_ajax(`${ruta_ambiental}guardar_proceso`, data, (resp) => {
            let { titulo, mensaje, tipo } = resp;
            if (tipo == 'success') {
                listar_procesos();
                $("#modal_nuevo_proceso").modal('hide');
                $("#form_guardar_proceso").get(0).reset();
            }
            MensajeConClase(mensaje, tipo, titulo);
        });
    } else MensajeConClase("Es necesario seleccionar alguna persona responsable del proceso.", "info", "Oops...!");
}

const listar_procesos = () => {
    $("#tabla_procesos tbody").off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .modificar").off("click", "tr td .eliminar").off("click", "tr td .funcionario");
    consulta_ajax(`${ruta_ambiental}listar_procesos`, { idparametro: 209 }, resp => {
        const myTable = $("#tabla_procesos").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "valor"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $("#tabla_procesos tbody").on("click", "tr", function() {
            $("#tabla_procesos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_procesos tbody").on("dblclick", "tr", function() {
            $("#tabla_procesos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_procesos tbody").on("click", "tr td .modificar", function() {
            let { id, valor, valorx } = myTable.row($(this).parent()).data();
            id_proceso_global = id;
            $("#valorparametro_mod").val(valor);
            $("#descripcion_mod").val(valorx);
            $("#modal_modificar_proceso").modal();
        });

        $("#tabla_procesos tbody").on("click", "tr td .eliminar", function() {
            let { id } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar Proceso?", tabla: 'valor_parametro' }, () => {
                listar_procesos();
            });
        });

        $("#tabla_procesos tbody").on("click", "tr td .funcionario", function() {
            let { id } = myTable.row($(this).parent()).data();
            id_proceso_global = id;
            listar_funcionarios_proceso(id)
            $("#modal_responsable_proceso").modal();
        });
    });
}

const modificar_proceso = () => {
    let fordata = new FormData(document.getElementById("form_modificar_proceso"));
    let data = formDataToJson(fordata);
    data.id_proceso = id_proceso_global;
    consulta_ajax(`${ruta_ambiental}modificar_proceso`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_procesos();
            $("#modal_modificar_proceso").modal('hide');
            $("#form_modificar_proceso").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const listar_funcionarios_proceso = (id_proceso) => {
    $("#tabla_responsables_procesos tbody").off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .eliminar");
    consulta_ajax(`${ruta_ambiental}listar_funcionarios`, { id_proceso }, resp => {
        const myTable = $("#tabla_responsables_procesos").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "nombre_completo"
                },
                {
                    data: "identificacion"
                },
                {
                    "render": function(data, type, full, meta) {
                        if (full.id_tipo == 1) return "Lider";
                        else return "Agente de Cambio"
                    }
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $("#tabla_responsables_procesos tbody").on("click", "tr", function() {
            $("#tabla_responsables_procesos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_responsables_procesos tbody").on("dblclick", "tr", function() {
            $("#tabla_responsables_procesos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_responsables_procesos tbody").on("click", "tr td .eliminar", function() {
            let { id, id_proceso } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar Funcionario?", tabla: 'calidad_personas_procesos' }, () => {
                listar_funcionarios_proceso(id_proceso);
            });
        });
    });
}

const guardar_funcionario_proceso = (data) => {
    let { id } = data;
    consulta_ajax(`${ruta_ambiental}guardar_funcionario_proceso`, { id_persona: id, id_proceso: id_proceso_global, tipo_persona }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_funcionarios_proceso(id_proceso_global);
            $("#modal_buscar_auxiliar").modal('hide');
            $("#form_buscar_auxiliar").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const guardar_datos = (control, listar_control, tabla_html, participantes = '', id = '') => {
    let fordata = new FormData(document.getElementById("form_agregar_actividad"));
    let data = formDataToJson(fordata);
    data.id_solicitud = id_solicitud_global;
    data.id_data = id;
    consulta_ajax(`${ruta_ambiental}${control}`, data, (resp) => {
        let { titulo, mensaje, tipo, id_responsable } = resp;
        if (tipo == 'success') {
            if (participantes) {
                listar_participantes(id_solicitud_global);
            } else {
                enviar_correo_actividad(id_responsable);
                listar_actividades(listar_control, tabla_html, id_solicitud_global, id_proceso_global);
            }
            $("#modal_add_actividades").modal('hide');
            $("#form_agregar_actividad").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const buscar_empleado_id = id_persona => {
    return new Promise(resolve => {
        let url = `${ruta_ambiental}buscar_empleado`;
        consulta_ajax(url, { id_persona }, resp => {
            resolve(resp);
        });
    });
}

const enviar_correo_actividad = async(id_persona) => {
    let persona = await buscar_empleado_id(id_persona);
    let ser = `<a href="${server}index.php/calidad"><b>agil.cuc.edu.co</b></a>`;
    let tipo = 1;
    let titulo = 'Planes de acción - Asignacion de Actividad';
    let correos = persona.correo;
    let nombre = persona.nombre_completo;
    let body = `Se informa que le fue asignado una actividad.
	<br><br>
	Para ver la información de la solicitud, haga click en siguiente enlace:
	<br><br>
	${ser}`;
    enviar_correo_personalizado("Cal", body, correos, nombre, "Planes de acción", titulo, "ParCodAdm", tipo);
}

const guardar_herramienta = (id_solicitud) => {
    let fordata = new FormData(document.getElementById("form_agregar_herramienta"));
    let data = formDataToJson(fordata);
    data.id_solicitud = id_solicitud;
    consulta_ajax(`${ruta_ambiental}guardar_herramienta`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_herramienta(id_solicitud_global);
            $("#modal_add_herramienta").modal('hide');
            $("#form_agregar_herramienta").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const modificar_herramienta = (id_solicitud, id_data) => {
    let fordata = new FormData(document.getElementById("form_agregar_herramienta"));
    let data = formDataToJson(fordata);
    data.id_solicitud = id_solicitud;
    data.id_data = id_data;
    consulta_ajax(`${ruta_ambiental}modificar_herramienta`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_herramienta(id_solicitud_global);
            $("#modal_add_herramienta").modal('hide');
            $("#form_agregar_herramienta").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const listar_actividades = (listar_control, tabla_html, id_solicitud, id_proceso) => {
    $(`#${tabla_html} tbody`).off("click", "tr").off("dblclick", "tr").off("click", "tr td .modificar").off("click", "tr td .eliminar").off("click", "tr td .ver");
    let i = 0;
    consulta_ajax(`${ruta_ambiental}${listar_control}`, { id_solicitud: id_solicitud, id_proceso }, resp => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "actividad"
                },
                {
                    data: "responsable"
                },
                {
                    data: "ver"
                },
                {
                    data: "fecha_actividad"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $(`#${tabla_html} tbody`).on("click", "tr", function() {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $(`#${tabla_html} tbody`).on("dblclick", "tr", function() {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $(`#${tabla_html} tbody`).on("click", "tr td .ver", function() {
            let { id, id_solicitud } = myTable.row($(this).parent()).data();
            let tipo = '';
            if (listar_control == "listar_correccion") {
                tipo = 1;
            } else { tipo = 2; }
            callback_activo_aux = (resp) => guardar_avance(id, id_solicitud, tipo);
            listar_avances_actividad(id, id_solicitud, tipo);
            $("#modal_avances_actividad").modal();
        });

        $(`#${tabla_html} tbody`).on("click", "tr td .modificar", function() {
            let { id, id_solicitud, actividad, fecha_actividad, id_persona, responsable } = myTable.row($(this).parent()).data();
            id_solicitud_global = id_solicitud;
            if (listar_control == "listar_correccion") {
                callback_activo_aux = (resp) => guardar_datos('modificar_correccion', 'listar_correccion', 'tabla_correcciones', '', id);
            } else {
                callback_activo_aux = (resp) => guardar_datos('modificar_plan_accion', 'listar_plan_accion', 'tabla_plan_accion', '', id);
            }
            $(".persona_actividad").empty();
            $("#txt_actividad").val(actividad);
            $("#fecha_actividad").val(fecha_actividad);
            $(".persona_actividad").append(`<option value='${id_persona}' selected> ${responsable} </option>`);
            $("#modal_add_actividades").modal();
        });

        $(`#${tabla_html} tbody`).on("click", "tr td .eliminar", function() {
            let { id, id_solicitud } = myTable.row($(this).parent()).data();
            let tabla = '';
            if (listar_control == "listar_correccion") {
                tabla = 'calidad_correcciones';
            } else {
                tabla = 'calidad_plan_accion';
            }
            eliminar_datos({ id, title: "Eliminar Actividad ?", id_solicitud, tabla }, () => {
                listar_actividades(listar_control, tabla_html, id_solicitud, id_proceso);
            });
        });
    });
}

const listar_avances_actividad = (id_data, id_solicitud, tipo) => {
    $("#tabla_avances tbody").off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .eliminar").off("click", "tr td .modificar");
    let i = 0;
    consulta_ajax(`${ruta_ambiental}listar_avances_actividad`, { id_data, id_solicitud, tipo }, resp => {
        const myTable = $("#tabla_avances").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    "render": function(data, type, full, meta) {
                        return "<a class='sin-decoration ' href='" + Traer_Server() + ruta_adjuntos + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
                    }
                },
                {
                    data: "nombre_real"
                },
                {
                    data: "observacion"
                },
                {
                    data: "fecha_fin"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $("#tabla_avances tbody").on("click", "tr", function() {
            $("#tabla_avances tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_avances tbody").on("dblclick", "tr", function() {
            $("#tabla_avances tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_avances tbody").on("click", "tr td .modificar", function() {
            let { id, id_actividad, id_solicitud, observacion, fecha_fin } = myTable.row($(this).parent()).data();
            $("#observacion_act").val(observacion);
            $("#fecha_finactividad").val(fecha_fin);
            callback_activo_aux = (resp) => modificar_avance(id, id_actividad, id_solicitud, tipo);
            $("#modal_agregar_avances").modal();
        });

        $("#tabla_avances tbody").on("click", "tr td .eliminar", function() {
            let { id } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar Soporte ?", id_solicitud, tabla: 'calidad_avances_actividad' }, () => {
                listar_avances_actividad(id_data, id_solicitud, tipo);
            });
        });
    });
}

const guardar_avance = (id_data, id_solicitud, tipo_avance) => {
    let fordata = new FormData(document.getElementById("form_agregar_avances"));
    fordata.append('id_solicitud', id_solicitud);
    fordata.append('id_data', id_data);
    fordata.append('tipo', tipo_avance);
    enviar_formulario(`${ruta_ambiental}guardar_avance`, fordata, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_avances_actividad(id_data, id_solicitud, tipo_avance);
            $("#modal_agregar_avances").modal('hide');
            $("#form_agregar_avances").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const modificar_avance = (id_data, id_actividad, id_solicitud, tipo_avance) => {
    let fordata = new FormData(document.getElementById("form_agregar_avances"));
    fordata.append('id_data', id_data);
    enviar_formulario(`${ruta_ambiental}modificar_avance`, fordata, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_avances_actividad(id_actividad, id_solicitud, tipo_avance);
            $("#modal_agregar_avances").modal('hide');
            $("#form_agregar_avances").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const listar_participantes = (id_solicitud) => {
    $("#tabla_participantes tbody").off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .eliminar");
    let i = 0;
    consulta_ajax(`${ruta_ambiental}listar_participantes`, { id_solicitud: id_solicitud }, resp => {
        const myTable = $("#tabla_participantes").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "nombre"
                },
                {
                    data: "fecha_actividad"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $("#tabla_participantes tbody").on("click", "tr", function() {
            $("#tabla_participantes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_participantes tbody").on("dblclick", "tr", function() {
            $("#tabla_participantes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_participantes tbody").on("click", "tr td .eliminar", function() {
            let { id, id_solicitud } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar Participante ?", id_solicitud, tabla: 'calidad_participantes' }, () => {
                listar_participantes(id_solicitud);
            });
        });
    });
}

const listar_herramienta = (id_solicitud) => {
    $("#tabla_herramienta tbody").off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .eliminar").off("click", "tr td .modificar");
    consulta_ajax(`${ruta_ambiental}listar_herramienta`, { id_solicitud: id_solicitud }, resp => {
        const myTable = $("#tabla_herramienta").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    data: "idea"
                },
                {
                    data: "porque1"
                },
                {
                    data: "porque2"
                },
                {
                    data: "porque3"
                },
                {
                    data: "porque4"
                },
                {
                    data: "porque5"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $("#tabla_herramienta tbody").on("click", "tr", function() {
            $("#tabla_herramienta tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_herramienta tbody").on("dblclick", "tr", function() {
            $("#tabla_herramienta tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $("#tabla_herramienta tbody").on("click", "tr td .modificar", function() {
            let { id, id_solicitud, idea, porque1, porque2, porque3, porque4, porque5 } = myTable.row($(this).parent()).data();
            $("#txt_idea").val(idea);
            $("#txt_porque1").val(porque1);
            $("#txt_porque2").val(porque2);
            $("#txt_porque3").val(porque3);
            $("#txt_porque4").val(porque4);
            $("#txt_porque5").val(porque5);
            callback_activo_aux = (resp) => modificar_herramienta(id_solicitud_global, id);
            $("#modal_add_herramienta").modal();
        });

        $("#tabla_herramienta tbody").on("click", "tr td .eliminar", function() {
            let { id, id_solicitud } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar herramienta ?", id_solicitud, tabla: 'calidad_herramienta_nc' }, () => {
                listar_herramienta(id_solicitud);
            });
        });
    });
}

const listar_archivos_adjuntos = (id_solicitud, tipo, tabla_html) => {
    $(`#${tabla_html} tbody`).off("click", "tr").off("dblclick", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr td .eliminar");
    let i = 0;
    consulta_ajax(`${ruta_ambiental}listar_archivos_adjuntos`, { id_solicitud: id_solicitud, tipo: tipo }, resp => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                    "render": function(data, type, full, meta) {
                        return "<a class='sin-decoration ' href='" + Traer_Server() + ruta_adjuntos + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
                    }
                },
                {
                    data: "nombre_real"
                },
                {
                    data: "fecha_registra"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $(`#${tabla_html} tbody`).on("click", "tr", function() {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $(`#${tabla_html} tbody`).on("dblclick", "tr", function() {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
            let data = myTable.row(this).data();
        });

        $(`#${tabla_html} tbody`).on("click", "tr td .eliminar", function() {
            let { id, id_solicitud, tipo } = myTable.row($(this).parent()).data();
            eliminar_datos({ id, title: "Eliminar Soporte ?", id_solicitud, tabla: 'calidad_adjuntos_nc' }, () => {
                listar_archivos_adjuntos(id_solicitud, tipo, tabla_html);
            });
        });
    });
}

const eliminar_datos = (data, callback) => {
    let { title, id, tabla } = data;
    swal({
            title,
            text: "Tener en cuenta que no podra revertir esta acción.!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Si, Eliminar!",
            cancelButtonText: "No, Regresar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                consulta_ajax(`${ruta_ambiental}eliminar_datos`, { id, tabla }, resp => {
                    let { tipo, mensaje, titulo } = resp;
                    if (tipo == 'success') {
                        callback();
                        swal.close();
                    } else MensajeConClase(mensaje, tipo, titulo);
                });
            }
        });
}

const listar_actividades_imp = (control, tabla_html, id_solicitud, id_proceso = '') => {
    consulta_ajax(`${ruta_ambiental}${control}`, { id_solicitud, id_proceso }, resp => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "actividad"
                },
                {
                    data: "responsable"
                },
                {
                    data: "fecha_actividad"
                }
            ],
            language: idioma,
            dom: "",
            buttons: []
        });

    });
}

const listar_participantes_imp = (control, tabla, id_solicitud, tipo = '') => {
    let i = 0;
    consulta_ajax(`${ruta_ambiental}${control}`, { id_solicitud, tipo }, resp => {
        const myTable = $(`#${tabla}`).DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    render: function(data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    render: function(data, type, row, meta) {
                        if (tipo == '') {
                            return `${row.nombre}`;
                        } else {
                            return `${row.nombre_real}`;
                        }
                    }
                },
                {
                    render: function(data, type, row, meta) {
                        if (tipo == '') {
                            return `${row.fecha_actividad}`;
                        } else {
                            return `${row.fecha_registra}`;
                        }
                    }
                }
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
    });
}

const listar_herramienta_imp = (id_solicitud) => {
    consulta_ajax(`${ruta_ambiental}listar_herramienta`, { id_solicitud }, resp => {
        const myTable = $("#tabla_herramienta_imp").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "idea"
                },
                {
                    data: "porque1"
                },
                {
                    data: "porque2"
                },
                {
                    data: "porque3"
                },
                {
                    data: "porque4"
                },
                {
                    data: "porque5"
                }
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
    });
}

const imprimir_nc = async(id_solicitud, data) => {
    let {
        fecha_registra,
        descripcion,
        proceso,
        id_proceso
    } = data;
    $(".proceso").html(proceso);
    $(".fecha").html(fecha_registra);
    $(".descripcion").html(descripcion);
    let data_nc = await consultar_nc(id_solicitud);
    let { accion, hallazgo, origen } = data_nc;
    $(".tipo_accion").html(accion);
    $(".tipo_hallazgo").html(hallazgo);
    $(".origen_fuente").html(origen);
    let datos = await listar_funcionarios(id_proceso);
    if (datos) {
        datos.forEach(element => {
            if (element.id_tipo == 1) {
                $(".responsable_proceso").html(`${element.nombre_completo}`);
            } else {
                if (element.id_tipo == 2) $(".agente").html(`${element.nombre_completo}`);
            }
        });
    }

    let imprimir = document.querySelector("#imprimir_nc");
    imprimirDIV(imprimir, true);
}

const obtener_estado_informes = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_estado_informes`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "nombre"
                },
                {
                    data: "cantidad"
                },
                {
                    data: "porcentaje"
                },
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').show();
        $('#btn_generar_Graficos').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos3').hide();

        datos_grafica = resp;
        accion = 1;
    });

}

const obtener_detalle_estado = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_detalle_estado`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados2").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "nombre"
                },
                {
                    data: "cantidad"
                },
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados').hide();
        $('#tbltabla_estados2').show();
        $('#btn_generar_Graficos').hide();
        $('#btn_generar_Graficos2').show();
        $('#btn_generar_Graficos3').hide();

        datos_grafica = resp;
        accion = 1;
    });

}

const obtener_tipo_accion = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_tipo_accion`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "nombre"
                },
                {
                    data: "cantidad"
                },
                {
                    data: "porcentaje"
                },
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').show();
        $('#btn_generar_Graficos').show();
        $('#btn_generar_Graficos2').hide();

        datos_grafica = resp;
        accion = 1;
    });

}

const obtener_tipo_hallazgo = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_tipo_hallazgo`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [{
                    data: "nombre"
                },
                {
                    data: "cantidad"
                },
                {
                    data: "porcentaje"
                },
            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').show();
        $('#btn_generar_Graficos').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos3').hide();

        datos_grafica = resp;
        accion = 1;
    });



}

const obtener_cumplimiento_estados = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_cumplimiento_estados`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados3").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "proceso"
                },
                {
                    data: "ejecutada"
                },
                {
                    data: "en_proceso"
                },
                {
                    data: "solicitada"
                },
                {
                    data: "total"
                },
                {
                    data: "porcentaje"
                },

            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').show();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos3').hide();

        datos_grafica = resp;
        accion = 2;
    });

}

const obtener_tipos_procesos = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_tipos_procesos`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados4").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "proceso"
                },
                {
                    data: "correctiva"
                },
                {
                    data: "preventiva"
                },
                {
                    data: "mejora"
                },
                {
                    data: "total"
                },

            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').show();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos3').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos').hide();
        datos_grafica = resp;
        accion = 3;
    });

}

const obtener_hallazgos_procesos = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_hallazgos_procesos`, { fecha_inicio, fecha_fin }, resp => {

        const myTable = $("#tbltabla_estados5").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "proceso"
                },
                {
                    data: "no_conformidad"
                },
                {
                    data: "op_mejora"
                },
                {
                    data: "observacion"
                },
                {
                    data: "total"
                },


            ],
            language: idioma,
            dom: "",
            buttons: []
        });


        $('#tbltabla_estados5').show();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos3').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos').hide();

        datos_grafica = resp;
        accion = 4;

    });
}

const obtener_estados_auditoria = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_estados_auditoria`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados3").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "origen"
                },
                {
                    data: "ejecutada"
                },
                {
                    data: "en_proceso"
                },
                {
                    data: "solicitada"
                },
                {
                    data: "total"
                },
                {
                    data: "porcentaje"
                },

            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').show();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos3').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos').hide();

        datos_grafica = resp;
        accion = 5;
    });

}

const obtener_tipos_origen = (fecha_inicio, fecha_fin) => {

    consulta_ajax(`${ruta_ambiental}obtener_tipos_origen`, { fecha_inicio, fecha_fin }, resp => {
        const myTable = $("#tbltabla_estados4").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "origen"
                },
                {
                    data: "correctiva"
                },
                {
                    data: "preventiva"
                },
                {
                    data: "mejora"
                },
                {
                    data: "total"
                },

            ],
            language: idioma,
            dom: "",
            buttons: []
        });
        $('#tbltabla_estados5').hide();
        $('#tbltabla_estados4').show();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos3').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos').hide();
        datos_grafica = resp;
        accion = 6;
    });

}

const obtener_hallazgos_origen = (fecha_inicio, fecha_fin) => {
    consulta_ajax(`${ruta_ambiental}obtener_hallazgos_origen`, { fecha_inicio, fecha_fin }, resp => {

        const myTable = $("#tbltabla_estados5").DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [

                {
                    data: "origen"
                },
                {
                    data: "no_conformidad"
                },
                {
                    data: "op_mejora"
                },
                {
                    data: "observacion"
                },
                {
                    data: "total"
                },


            ],
            language: idioma,
            dom: "",
            buttons: []
        });


        $('#tbltabla_estados5').show();
        $('#tbltabla_estados4').hide();
        $('#tbltabla_estados3').hide();
        $('#tbltabla_estados2').hide();
        $('#tbltabla_estados').hide();
        $('#btn_generar_Graficos3').show();
        $('#btn_generar_Graficos2').hide();
        $('#btn_generar_Graficos').hide();

        datos_grafica = resp;
        accion = 7;

    });
}

const graficar_datos = (data, titulo) => {

    let ctx2 = document.getElementById("myGraph");

    if (grafica) {
        grafica.clear();
        grafica.destroy();
    }

    let labels = data.map((i) => i.valorTxt);
    let datos = data.map((i) => Number(i.valorNro));
    grafica = new Chart(ctx2, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                label: titulo,
                backgroundColor: [
                    "#ecb400",
                    "#ffe89e",
                    "#fff6d9",
                    "#b28800",
                    "#3c2e00",
                    "#fffbed",
                    "#ff637d",
                    "#f4f1bb",
                    "#66d7d1",
                    "#4DD0E1",
                    "#F06292",
                    "#BA68C8",
                    "#512DA8",
                ],
                data: !datos ? NULL : datos,
            }, ],
        },
    });
    $('#bar2').hide()
    $('#bar').hide()
    $('#torta').show()

    return grafica;
}

const graficar_datos2 = (data, titulo) => {

    let ctx2 = document.getElementById("myGraph2");
    let labels = data.map((i) => i.nombre);
    let datos = data.map((i) => Number(i.cantidad));

    let grafica = new Chart(ctx2, {
        type: 'bar',
        data: {

            labels: labels,
            datasets: [{
                label: titulo,
                backgroundColor: [
                    "#ecb400",
                    "#ffe89e",
                    "#fff6d9",
                    "#b28800",
                    "#3c2e00",
                    "#fffbed",
                ],
                data: !datos ? NULL : datos,
            }, ],
        },

        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    $('#bar2').hide()
    $('#torta').hide()
    $('#bar').show()
    return grafica;
}

const graficar_datos3 = (data, titulo1, titulo2, titulo3) => {
    let ctx2 = document.getElementById("myGraph3");

    if (grafica) {
        grafica.clear();
        grafica.destroy();
    }

    let labels = data.map((i) => i.proceso);
    grafica = new Chart(ctx2, {

        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: titulo1,
                    data: data.map((i) => Number(i.v1)),
                    backgroundColor: "#ff637d",
                    borderColor: "#ff637d",
                    borderWidth: 1
                },
                {
                    label: titulo2,
                    data: data.map((i) => Number(i.v2)),
                    backgroundColor: "#f4f1bb",
                    borderColor: "#f4f1bb",
                    borderWidth: 1
                },
                {
                    label: titulo3,
                    data: data.map((i) => Number(i.v3)),
                    backgroundColor: "#66d7d1",
                    borderColor: "#66d7d1",
                    borderWidth: 1
                }
            ],
        },

        options: {
            animation: {
                duration: 0
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }

    });

    $('#bar2').show()
    $('#torta').hide()
    $('#bar').hide()
    return grafica;
}

const crear_informe_general = async(fecha_inicio, fecha_fin) => {

    let imprimir = document.querySelector("#informe_general");
    let dato1 = await obtener_estado_informes2(fecha_inicio, fecha_fin);
    let dato2 = await obtener_detalle_estado2(fecha_inicio, fecha_fin);
    let dato3 = await obtener_tipo_accion2(fecha_inicio, fecha_fin);
    let dato4 = await obtener_tipo_hallazgo2(fecha_inicio, fecha_fin);
    let dato5 = await obtener_cumplimiento_estados2(fecha_inicio, fecha_fin);
    let dato6 = await obtener_tipos_procesos2(fecha_inicio, fecha_fin);
    let dato7 = await obtener_hallazgos_procesos2(fecha_inicio, fecha_fin);
    let dato8 = await obtener_estados_auditoria2(fecha_inicio, fecha_fin);
    let dato9 = await obtener_tipos_origen2(fecha_inicio, fecha_fin);
    let dato10 = await obtener_hallazgos_origen2(fecha_inicio, fecha_fin);
    let sin_clasificar = await obtener_sin_clasificar(fecha_inicio, fecha_fin);
    let total_porcentaje = 0;
    let cant_total = 0;
    let valor1 = 0;
    let valor2 = 0;
    let valor3 = 0;
    let dato = [];

    sin_clasificar.map((elemento) => { sin_clasificar = Number(elemento.sin_clasificar); })

    $("#informe_general").show();

    $("#tabla_estado_informes tbody").html('');
    dato1.map((elemento) => {
        $("#tabla_estado_informes tbody").append(`<tr><td>${elemento.nombre}</td><td>${elemento.cantidad}</td><td>${elemento.porcentaje + ' %'}</td></tr> `);
        total_porcentaje += elemento.porcentaje;
        cant_total += Number(elemento.cantidad);
    })
    $("#tabla_estado_informes tbody").append(`<tr><td>TOTAL</td><td>${cant_total}</td><td>${total_porcentaje + ' %'}</td></tr> `);
    let lienzo = "myGraph_informe1";
    let imagen = "imagen_grafica1";
    graficar1(dato1, lienzo, imagen);

    cant_total = 0;
    $("#tabla_detalle_estado tbody").html('');
    dato2.map((elemento) => {
        $("#tabla_detalle_estado tbody").append(`<tr><td>${elemento.nombre}</td><td>${elemento.cantidad}</td></tr> `);
        cant_total += Number(elemento.cantidad);
    })
    $("#tabla_detalle_estado tbody").append(`<tr><td>TOTAL</td><td>${cant_total}</td></tr> `);
    lienzo = "myGraph_informe2";
    imagen = "imagen_grafica2";
    graficar2(dato2, lienzo, imagen);


    total_porcentaje = 0;
    cant_total = 0;
    $("#tabla_tipo_accion tbody").html('');
    dato3.map((elemento) => {
        $("#tabla_tipo_accion tbody").append(`<tr><td>${elemento.nombre}</td><td>${elemento.cantidad}</td><td>${elemento.porcentaje + ' %'}</td></tr> `);
        dato.push({ 'nombre': elemento.nombre, 'cantidad': elemento.porcentaje });
        total_porcentaje += elemento.porcentaje;
        cant_total += Number(elemento.cantidad);
    })

    $("#tabla_tipo_accion tbody").append(`<tr><td>TOTAL</td><td>${cant_total}</td><td>${total_porcentaje + ' %'}</td></tr> `);
    lienzo = "myGraph_informe3";
    imagen = "imagen_grafica3";
    graficar1(dato, lienzo, imagen);


    total_porcentaje = 0;
    cant_total = 0;
    dato = [];
    $("#tabla_tipo_hallazgo tbody").html('');
    dato4.map((elemento) => {
        $("#tabla_tipo_hallazgo tbody").append(`<tr><td>${elemento.nombre}</td><td>${elemento.cantidad}</td><td>${elemento.porcentaje + ' %'}</td></tr> `);
        dato.push({ 'nombre': elemento.nombre, 'cantidad': elemento.porcentaje });
        total_porcentaje += elemento.porcentaje;
        cant_total += Number(elemento.cantidad);
    })
    $("#tabla_tipo_hallazgo tbody").append(`<tr><td>TOTAL</td><td>${cant_total}</td><td>${total_porcentaje + ' %'}</td></tr> `);
    lienzo = "myGraph_informe4";
    imagen = "imagen_grafica4";
    graficar1(dato, lienzo, imagen);


    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    total_porcentaje = 0;
    dato = [];
    $("#tabla_cumplimiento_estados tbody").html('');
    dato5.map((elemento) => {
        $("#tabla_cumplimiento_estados tbody").append(`<tr><td>${elemento.proceso}</td><td>${elemento.ejecutada}</td><td>${elemento.en_proceso}</td><td>${elemento.solicitada}</td><td>${elemento.total}</td><td>${elemento.porcentaje + ' %'}</td></tr> `);
        dato.push({ 'nombre': elemento.proceso, 'cantidad': elemento.porcentaje });
        valor1 += Number(elemento.ejecutada);
        valor2 += Number(elemento.en_proceso);
        valor3 += Number(elemento.solicitada);
    })
    cant_total = valor1 + valor2 + valor3;
    total_porcentaje = Math.round(((valor1) / cant_total) * 100);
    $("#tabla_cumplimiento_estados tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td><td>${total_porcentaje + ' %'}</td></tr> `);
    lienzo = "myGraph_informe5";
    imagen = "imagen_grafica5";
    graficar1(dato, lienzo, imagen);


    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    dato = [];
    $("#tabla_tipo_proceso tbody").html('');
    dato6.map((elemento) => {
        $("#tabla_tipo_proceso tbody").append(`<tr><td>${elemento.proceso}</td><td>${elemento.correctiva}</td><td>${elemento.preventiva}</td><td>${elemento.mejora}</td><td>${elemento.total}</td></tr> `);
        dato.push({ 'item': elemento.proceso, 'v1': elemento.correctiva, 'v2': elemento.preventiva, 'v3': elemento.mejora });
        valor1 += Number(elemento.correctiva);
        valor2 += Number(elemento.preventiva);
        valor3 += Number(elemento.mejora);
    })
    cant_total = valor1 + valor2 + valor3 + sin_clasificar;
    $("#tabla_tipo_proceso tbody").append(`<tr><td>SIN CLASIFICAR</td><td>-</td><td>-</td><td>-</td><td>${sin_clasificar}</td></tr> `);
    $("#tabla_tipo_proceso tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td></tr> `);
    lienzo = "myGraph_informe6";
    imagen = "imagen_grafica6";
    graficar3(dato, lienzo, imagen, t1 = "CORRECTIVA", t2 = "PREVENTIVA", t3 = "MEJORA");


    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    dato = [];
    $("#tabla_hallazgo_proceso tbody").html('');
    dato7.map((elemento) => {
        $("#tabla_hallazgo_proceso tbody").append(`<tr><td>${elemento.proceso}</td><td>${elemento.no_conformidad}</td><td>${elemento.op_mejora}</td><td>${elemento.observacion}</td><td>${elemento.total}</td></tr> `);
        dato.push({ 'item': elemento.proceso, 'v1': elemento.no_conformidad, 'v2': elemento.op_mejora, 'v3': elemento.observacion });
        valor1 += Number(elemento.no_conformidad);
        valor2 += Number(elemento.op_mejora);
        valor3 += Number(elemento.observacion);
    })
    cant_total = valor1 + valor2 + valor3 + sin_clasificar;
    $("#tabla_hallazgo_proceso tbody").append(`<tr><td>SIN CLASIFICAR</td><td>-</td><td>-</td><td>-</td><td>${sin_clasificar}</td></tr> `);
    $("#tabla_hallazgo_proceso tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td></tr> `);
    lienzo = "myGraph_informe7";
    imagen = "imagen_grafica7";
    graficar3(dato, lienzo, imagen, t1 = "NO CONFORMIDAD", t2 = "OPORTUNIDAD DE MEJORA", t3 = "OBSERVACION");


    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    total_porcentaje = 0;
    dato = [];
    $("#tabla_estado_auditoria tbody").html('');
    dato8.map((elemento) => {
        $("#tabla_estado_auditoria tbody").append(`<tr><td>${elemento.origen}</td><td>${elemento.ejecutada}</td><td>${elemento.en_proceso}</td><td>${elemento.solicitada}</td><td>${elemento.total}</td><td>${elemento.porcentaje + ' %'}</td></tr> `);
        dato.push({ 'item': elemento.origen, 'v1': elemento.ejecutada, 'v2': elemento.en_proceso, 'v3': elemento.solicitada });
        valor1 += Number(elemento.ejecutada);
        valor2 += Number(elemento.en_proceso);
        valor3 += Number(elemento.solicitada);
    })
    cant_total = valor1 + valor2 + valor3 + sin_clasificar;
    total_porcentaje = Math.round(((valor1) / cant_total) * 100);
    $("#tabla_estado_auditoria tbody").append(`<tr><td>SIN CLASIFICAR</td><td>-</td><td>-</td><td>-</td><td>${sin_clasificar}</td><td>-</td></tr> `);
    $("#tabla_estado_auditoria tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td><td>${total_porcentaje + ' %'}</td></tr> `);
    lienzo = "myGraph_informe8";
    imagen = "imagen_grafica8";
    graficar3(dato, lienzo, imagen, t1 = "EJECUTADA", t2 = "EN PROCESO", t3 = "FINALIZADA");


    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    dato = [];
    $("#tabla_tipo_origen tbody").html('');
    dato9.map((elemento) => {
        $("#tabla_tipo_origen tbody").append(`<tr><td>${elemento.origen}</td><td>${elemento.correctiva}</td><td>${elemento.preventiva}</td><td>${elemento.mejora}</td><td>${elemento.total}</td></tr> `);
        dato.push({ 'item': elemento.origen, 'v1': elemento.correctiva, 'v2': elemento.preventiva, 'v3': elemento.mejora });
        valor1 += Number(elemento.correctiva);
        valor2 += Number(elemento.preventiva);
        valor3 += Number(elemento.mejora);
    })
    cant_total = valor1 + valor2 + valor3 + sin_clasificar;
    $("#tabla_tipo_origen tbody").append(`<tr><td>SIN CLASIFICAR</td><td>-</td><td>-</td><td>-</td><td>${sin_clasificar}</td></tr> `);
    $("#tabla_tipo_origen tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td></tr> `);
    lienzo = "myGraph_informe9";
    imagen = "imagen_grafica9";
    graficar3(dato, lienzo, imagen, t1 = "CORRECTIVA", t2 = "PREVENTIVA", t3 = "MEJORA");

    valor1 = 0;
    valor2 = 0;
    valor3 = 0;
    cant_total = 0;
    dato = [];
    $("#tabla_hallazgo_origen tbody").html('');
    dato10.map((elemento) => {
        $("#tabla_hallazgo_origen tbody").append(`<tr><td>${elemento.origen}</td><td>${elemento.no_conformidad}</td><td>${elemento.op_mejora}</td><td>${elemento.observacion}</td><td>${elemento.total}</td></tr> `);
        dato.push({ 'item': elemento.origen, 'v1': elemento.no_conformidad, 'v2': elemento.op_mejora, 'v3': elemento.observacion });
        valor1 += Number(elemento.no_conformidad);
        valor2 += Number(elemento.op_mejora);
        valor3 += Number(elemento.observacion);
    })
    cant_total = valor1 + valor2 + valor3 + sin_clasificar;
    $("#tabla_hallazgo_origen tbody").append(`<tr><td>SIN CLASIFICAR</td><td>-</td><td>-</td><td>-</td><td>${sin_clasificar}</td></tr> `);
    $("#tabla_hallazgo_origen tbody").append(`<tr><td>TOTAL</td><td>${valor1}</td><td>${valor2}</td><td>${valor3}</td><td>${cant_total}</td></tr> `);
    lienzo = "myGraph_informe10";
    imagen = "imagen_grafica10";
    graficar3(dato, lienzo, imagen, t1 = "NO CONFORMIDAD", t2 = "OPORTUNIDAD DE MEJORA", t3 = "OBSERVACION");

    imprimirDIV(imprimir);
    $("#informe_general").hide();
}

const obtener_estado_informes2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_estado_informes`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_detalle_estado2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_detalle_estado`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_tipo_accion2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_tipo_accion`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_tipo_hallazgo2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_tipo_hallazgo`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_cumplimiento_estados2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_cumplimiento_estados`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_tipos_procesos2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_tipos_procesos`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_hallazgos_procesos2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_hallazgos_procesos`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_estados_auditoria2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_estados_auditoria`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_tipos_origen2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_tipos_origen`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_hallazgos_origen2 = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_hallazgos_origen`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const obtener_sin_clasificar = (fecha_inicio, fecha_fin) => {
    return new Promise(resolve => {
        consulta_ajax(`${ruta_ambiental}obtener_sin_clasificar`, { fecha_inicio, fecha_fin }, (resp) => {
            resolve(resp);
        });
    });
}

const graficar1 = (data, lienzo, imagen) => {
    let ctx2 = document.getElementById(lienzo);
    let labels = data.map((i) => i.nombre);
    let datos = data.map((i) => Number(i.cantidad));

    grafica = new Chart(ctx2, {
        type: "pie",
        data: {
            labels: labels,
            datasets: [{
                    label: "titulo",
                    backgroundColor: [
                        "#0000FF",
                        "#8A2BE2",
                        "#A52A2A",
                        "#DEB887",
                        "#5F9EA0",
                        "#7FFF00",
                        "#D2691E",
                        "#FF7F50",
                        "#6495ED",
                        "#FFF8DC",
                        "#DC143C",
                        "#00FFFF",
                        "#00008B",
                        "#FF8C00",
                        "#9932CC",
                        "#8B0000",
                        "#00FFFF"
                    ],
                    data: !datos ? NULL : datos,

                },

            ],
        },

        options: {

            animation: {
                duration: 0
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        if (value != 0) {
                            let percentage = (value * 100 / sum).toFixed(2) + "%";
                            return percentage;
                        }


                    },
                    color: '#fff',
                }
            }
        }

    });

    let image = ctx2.toDataURL();
    let id = document.getElementById(imagen);
    id.src = image;

    return grafica;
}

const graficar2 = (data, lienzo, imagen) => {

    let ctx2 = document.getElementById(lienzo);
    let labels = data.map((i) => i.nombre);
    let datos = data.map((i) => Number(i.cantidad));


    grafica = new Chart(ctx2, {

        type: 'bar',
        data: {
            labels: labels,

            datasets: [{
                label: "datos",
                backgroundColor: [
                    "#0000FF",
                    "#8A2BE2",
                    "#A52A2A",
                    "#DEB887",
                    "#5F9EA0",
                    "#7FFF00",
                    "#D2691E",
                    "#FF7F50",
                ],
                data: !datos ? NULL : datos,
            }, ],
        },
        options: {
            events: false,
            tooltips: {
                enabled: false
            },
            hover: {
                animationDuration: 0
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value);
                        return percentage;

                    },
                    color: '#ffff',
                    anchor: 'center',
                }
            },
            animation: {
                duration: 0,

            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }

                }]
            }
        }
    });

    let image = ctx2.toDataURL();
    let id = document.getElementById(imagen);
    id.src = image;

    return grafica;
}

const graficar3 = (data, lienzo, imagen, t1, t2, t3) => {
    let ctx2 = document.getElementById(lienzo);
    let labels = data.map((i) => i.item);

    grafica = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: t1,
                    data: data.map((i) => Number(i.v1)),
                    backgroundColor: "#0000FF",
                    borderColor: "#0000FF",
                    borderWidth: 1
                },
                {
                    label: t2,
                    data: data.map((i) => Number(i.v2)),
                    backgroundColor: "#A52A2A",
                    borderColor: "#A52A2A",
                    borderWidth: 1
                },
                {
                    label: t3,
                    data: data.map((i) => Number(i.v3)),
                    backgroundColor: "#8A2BE2",
                    borderColor: "#8A2BE2",
                    borderWidth: 1
                }
            ],
        },

        options: {


            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value);
                        return percentage;

                    },
                    color: '#ffff',
                    anchor: 'center',

                }
            },

            animation: {
                duration: 0,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });


    let image = ctx2.toDataURL();
    let id = document.getElementById(imagen);
    id.src = image;

    return grafica;
}