let ruta = `${Traer_Server()}index.php/bienestar_laboral_control/`;
let callbak_activo = resp => { };
let callbak_activo_alt = resp => { }
let ruta_archivos = "archivos_adjuntos/bienestar_laboral/seguridad_trabajo/";
let adjunto_seguridad = [];
let cargados = 0;
let id_tipo = null;
let id_solicitud = null;
let cantidad_adj = 0;
let tip_clasif = '';
let data_solicitante = { 'nombre': null, 'correo': null }
let data_seg = {}
let data_filtro = {};
let mtto = ''
let telefono = null
let personas = [];
let causas_gen = [];
let mensaje_correo = ''
let id_tipo_persona = null;
let descripcion_acto = null;
let id_persona = null;
let tipoPermiso = 'Aux';

$(document).ready(function () {
    // modulo
    $('#btn_administrar').click(function () {
        listar_procesos(tipoPermiso)
        listar_empleados(tipoPermiso)
        $('#administrar_biblioteca').modal('show')
    });

    $(".cbx_aux_lab").change(function () {
        const aux_perm = $(this).val();
        listar_procesos(tipoPermiso, aux_perm);
    });

    $('#btn_seguridad_trabajo').click(function () {
        $('#form_agregar_solicitud').get(0).reset()
        $('#adjs').show('fast');
        $('#modal_agregar_solicitud').modal('show')
        callbak_activo = resp => guardarSolicitudNew(resp)
        id_tipo = 'Lab_Seg_Tra'
        data_filtro = {}
        id_solicitud = null
    });

    $('#ver_estados').on('click', function () {
        $('#modal_detalle_estados_solicitud').modal('show');
        listar_estados_solicitud(id_solicitud)
    });

    $('#ver_mantenimiento').on('click', function () {
        $('#modal_detalle_estados_mantenimiento').modal('show');
        listar_estados_manteminiento(id_solicitud)
    });

    $(".regresar_menu").click(() => {
        administrar_elementos('menu');
    });

    $("#listado_solicitudes").click(() => {
        administrar_elementos('solicitudes');
    });

    $('#ver_notificaciones').on('click', function () {
        mostrar_notificaciones()
        $('#modal_notificaciones_seguridad').modal('show')
    })

    $('#btn_limpiar_filtros').on('click', function () {
        Con_filtros(false)
        $('#form_filtrar_solicitudes').get(0).reset()
        data_filtro = {}
        listarSolicitudes(data_filtro)
    });

    $('#form_agregar_solicitud').submit(e => {
        e.preventDefault();
        guardarSolicitud()
    });

    $('#form_modificar_solicitud_asesorias').submit(e => {
        e.preventDefault();
        modificarSolicitudAsesoria();
    });

    $('#agregar_archivos').on('click', function () {
        $('#cargar_adj_soli').hide()
        $('#modal_enviar_archivos').modal('show')
    });

    $('#agregar_adjuntos_nuevos').on('click', function () {
        $('#cargar_adj_soli').show()
        if (cantidad_adj >= 10) {
            MensajeConClase('No puede agregar más archivos', 'info', 'Ooops.!');
        } else {
            myDropzone.processQueue();
            $('#modal_enviar_archivos').modal('show')
        }
    });

    $('#id_lugar').change(async function () {
        if ($('#id_lugar').val() === '') {
            $('#id_ubicacion').hide('fast')
        } else {
            let lugar = $(this).val();
            let ubicacion = await obtener_valores_permiso(lugar, 116)
            $('#id_ubicacion').show('fast')
            pintar_datos_combo(ubicacion, '.cbx_ubicacion', 'Seleccione la ubicación');
        }
    })

    $('#id_ubicacion').hide('fast')

    $('#cargar_adj_soli').on('click', function () {
        myDropzone.processQueue();
    })

    $('#agregar_soporte_nuevos').on('click', function () {
        $('#cargar_adj_soli').show()
        if (cantidad_adj >= 10) {
            MensajeConClase('No puede agregar más archivos', 'info', 'Ooops.!');
        } else {
            myDropzone.processQueue();
            $('#modal_enviar_archivos').modal('show')
        }
    });

    $('#form_gestionar_solicitud').submit(e => {
        e.preventDefault()
        gestionar_solicitud()
        // $('#form_gestionar_solicitud').get(0).reset()
        $('#filtro_clas').css('display', 'none');
    })

    $('#btn_filtrar').on('click', function () {
        $("#modal_filtrar_solicitudes .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Crear filtro</span>');
        $('#modal_filtrar_solicitudes').modal('show')
        $('#filtro_clasificacion').hide('fast')
    })

    $('#filtro_tipos').change(async function () {
        let tipo = $(this).val();
        if (tipo == '') {
            $('#filtro_clasificacion').hide('fast')
        } else {
            let permisos = await obtener_valores_permiso(tipo, 144, 2)
            $('#filtro_clasificacion').show('fast')
            pintar_datos_combo(permisos, ".cbx_clasificacion", "Seleccione la clasificacion")
        }
    })

    $('#form_filtrar_solicitudes').submit(e => {
        e.preventDefault()
        filtrarSolicitud()
    })

    $('#con_adjuntos_rev').on('click', function () {
        $('#modal_enviar_archivos').modal('show')
        $('#cargar_adj_soli').hide()
    });

    $('#agregar_asoporte_nuevos').on('click', function () {
        $('#modal_enviar_archivos').modal('show');
    });

    $('#id_clasificacion').change(async function () {
        let clas = $(this).val();
        if (clas === 'Seg_Tip_Cond') {
            $('#admitido').on('click', function () { if ($(this).val() === '1') return $('#telefono').show('fast') })
            $('#no_dmitido').on('click', function () { if ($(this).val() === '0') return $('#telefono').hide('fast') })
            $('#filtro_clas').show('fast')
            $('#filtro_acto').hide('fast')
            $('#descripcion_acto').attr("required", false)
        } else if (clas === 'Seg_Tip_Acto') {
            $('#descripcion_acto').attr("required", true)
            $('#filtro_acto').show('fast')
            $('#filtro_clas').hide('fast')
            $('#id_tipo_persona').change(function () {
                personas = []
                let tipo = $(this).val()
                if (tipo === 'Tipo_Interna') {
                    $('#sel_agregar_persona_int').show("fast")
                    $('#id_tipo_persona').attr("required", true)
                    listarPersonasAgregadasInternas()
                } else if (tipo === '') {
                    $('#sel_agregar_persona_int').hide('fast')
                    $('#id_tipo_persona').attr("required", false)
                }
            })
        } else {
            $('#filtro_clas').hide('fast')
        }
        $('#telefono').hide('fast')
    })

    $('#admitido').on('change', function () {
        if ($(this).is(':checked')) {
            $("#telefono").show('fast');
        } else {
            $("#telefono").hide('fast');
        }
    });

    $('#retirar_persona_sele_int').on('click', function () {
        let id = document.getElementById('persona_asignada_int').value
        if (id === 'Per_Agre_Int') {
            MensajeConClase('Seleccione una persona para eliminar', 'info', 'Oops.!')
        } else {
            eliminarPersonasAgregadasInterna(id)
        }
    })

    $('#btn_agregar_persona_int').on('click', function () {
        $('#modal_buscar_persona').modal('show')
        $('#txt_dato_buscar').val('')
        listarPersonasAgregadasInternas()
        buscarPersonas('null')
    })

    $('#btn_buscar_persona').click(() => {
        let dato = $('#txt_dato_buscar').val().trim()
        dato.length == 0 ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") : buscarPersonas(dato);
    })

    /* MÓDULO ASESORÍAS */
    $('#btn_asesorias').click(function () {
        $('#modal_asesorias_modulos').modal('show')
        callbak_activo = resp => guardarSolicitudAsesoriaNew(resp)
        id_tipo = 'Lab_Ases'
        id_clasificacion = ''
        $('#form_agregar_solicitud_asesorias').get(0).reset()
        document.getElementById("solicitante_buscar").style.display = "block";
        $('.margin1').show('fast')
    });

    $('#btn_asesoria_financiera').click(function () {
        id_clasificacion = 'Ase_Tip_Fin'
        $('#bene').html('')
        $('#input_nombre').html('')
        $('#input_parentesco').html('')
        $('#input_contacto').html('')
        $('#cart1').html('<b> - </b> El programa de mejoramiento financiero de Bienestar Laboral.<br>Te brinda la posibilidad de conocer y aprender nuevas tecnicas para el manejo de tus finanzas personales.')
        $('#modal_agregar_solicitud_asesorias').modal('show')
        callbak_activo = (resp) =>info_solicitante(resp);//Llama a la funcion despues de dar check
        document.getElementById("div_busqueda_solicitante").style.display = "none";
        document.getElementById("div_formulario_solicitud").style.display = "block";
        });

    $('#btn_asesoria_vivienda').click(function () {
        id_clasificacion = 'Ase_Tip_Viv'
        $('#bene').html('')
        $('#input_nombre').html('')
        $('#input_parentesco').html('')
        $('#input_contacto').html('')
        $('#cart1').html('<b> - </b> Nuestra caja de compensación CajaCopi, tiene para ofrecernos múltiples beneficios para acogernos a sus subsidios.<br>Agenda tu cita y descubre que tener casa propia es fácil.')
        $('#modal_agregar_solicitud_asesorias').modal('show')
        callbak_activo = (resp) =>info_solicitante(resp);//Llama a la funcion despues de dar check
        document.getElementById("div_busqueda_solicitante").style.display = "none";
        document.getElementById("div_formulario_solicitud").style.display = "block";
     });

    $('#btn_asesoria_psicologica').click(function () {
        id_clasificacion = 'Ase_Tip_Psi'
        $('#cart1').html('<b> - </b> Procurando el cuidado de la Salud Mental.<br>Ofrecemos para ti y tu familia, a través de nuestro proveedor de servicios, las asesorías psicológicas.')
        $('#bene').html('<select class="form-control cbx_beneficiario" name="id_beneficiario" id="id_beneficiario" onchange="SelecBene(0)"></select>')
        Cargar_parametro_buscado_aux(319, ".cbx_beneficiario", "Seleccione el beneficiario");
        $('#modal_agregar_solicitud_asesorias').modal('show')
        callbak_activo = (resp) =>info_solicitante(resp);//Llama a la funcion despues de dar check
        document.getElementById("div_busqueda_solicitante").style.display = "none";
        document.getElementById("div_formulario_solicitud").style.display = "block";
    });

    $('#btn_asesoria_juridica').click(function () {
        id_clasificacion = 'Ase_Tip_Jur'
        $('#bene').html('')
        $('#input_nombre').html('')
        $('#input_parentesco').html('')
        $('#input_contacto').html('')
        $('#cart1').html('<b> - </b> El consultorio Jurídico brinda a cada funcionario y a su familia de forma gratuita, asesorías en todas las áreas del Derecho Administrativo, Civil - Familia, Comercial, Constitucional, Laboral – Seguridad Social y Penal.')
        $('#modal_agregar_solicitud_asesorias').modal('show')
        callbak_activo = (resp) =>info_solicitante(resp);//Llama a la funcion despues de dar check
        document.getElementById("div_busqueda_solicitante").style.display = "none";
        document.getElementById("div_formulario_solicitud").style.display = "block";
       });

    $('#form_agregar_solicitud_asesorias').submit(e => {
        e.preventDefault()
        guardarSolicitudAsesoria()
    });

    $("#detalle_persona_solicita").click(() => {
        obtener_datos_persona_id_completo(id_persona, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", "", "", ".perfil_perso", ".celular");
        $("#Mostrar_detalle_persona").modal("show");
    });

    $('#admin_perm').click(function () {
        tipoPermiso = 'Aux';
        listar_procesos(tipoPermiso)
        listar_empleados(tipoPermiso)
		$("#nav_admin_bib li").removeClass("active");
        $(this).addClass("active");
        $("#container_admin_bib").hide();
		$("#container_admin_bib").fadeIn(1000);
    });

	$('#admin_ases').click(function () {
        tipoPermiso = 'Ase';
        listar_procesos(tipoPermiso)
        listar_empleados(tipoPermiso)
		$("#nav_admin_bib li").removeClass("active");
		$(this).addClass("active");
		$("#container_admin_bib").hide();
		$("#container_admin_bib").fadeIn(1000);
    });
});



/*
INICIO DE FUNCIONES GENERICAS PARA EL MÓDULO DE BIENESTAR LABORAL
*/
const listarSolicitudes = (data) => {
    let sw = false;
    let tabla = '#tabla_listado_solicitudes_bienestar_laboral'
    let { id, filtro_estados, filtro_tipos, filtro_fecha_inicio, filtro_fecha_termina } = data
    if (filtro_estados || filtro_tipos || filtro_fecha_inicio || filtro_fecha_termina || id > 0) sw = true;
    $(`${tabla} tbody`)
        .off('click', '.ver')
        .off('click', '.modificar')
        .off('click', '.tramitar_seg')
        .off('click', '.finalizar_seg')
        .off('click', '.cancelar_seg')
        .off('click', '.rechazar_seg')
        .off('click', '.tramitar_ase')
        .off('click', '.modificar_ase')
        .off('click', '.rechazar_ase')
        .off('click', '.finalizar_ase')
        .off('click', '.cancelar_ase')
        .off('dblclick', 'tr')
        .off('click', 'tr');
    consulta_ajax(`${ruta}listar_solicitudes`, data, resp => {
        const myTable = $(`${tabla}`).DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: 'ver'
                },
                {
                    data: 'TipoSolicitud'
                },
                {
                    data: 'fullname'
                },
                {
                    data: 'fecha_registro'
                },
                {
                    data: 'estadoSolicitud'
                },
                {
                    data: 'accion'
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones()
        });

        $(`${tabla} tbody`).on('click', 'tr', function () {
            $(`${tabla} tbody tr`).removeClass("warning");
            let data = myTable.row(this).data();
            if (!data) {
                $(`${tabla} tbody tr`).removeClass("warning");
            } else {
                let { fullname, correo, id_clasificacion } = myTable.row(this).data();
                data_solicitante = { 'nombre': fullname, correo }
                tip_clasif = id_clasificacion;
                $(this).attr("class", "warning");
            }
        });

        $(`${tabla} tbody`).on('dblclick', 'tr', function () {
            let data = myTable.row(this).data();
            if (!data) {
                MensajeConClase('No hay informacion que mostrar', 'info', 'Oops.!')
            } else {
                $('#modal_detalle_solicitud').modal('show');
                id_solicitud = data.id
                id_persona = data.id_usuario_registro
                mostrarInformacionSolicitud(data)
                listar_archivos_adjuntos(data.id)
                ocultarBtn(data.id_estado_solicitud)
                btn_mtto(data.id_clasificacion, data.mtto)
                activarDesactivarCamposVer(data.id_tipo)
            }
        });

        $(`${tabla} tbody`).on('click', '.ver', function () {
            $('#modal_detalle_solicitud').modal('show');
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.id
            id_persona = data.id_usuario_registro
            mostrarInformacionSolicitud(data)
            listar_archivos_adjuntos(data.id)
            ocultarBtn(data.id_estado_solicitud)
            btn_mtto(data.id_clasificacion, data.mtto)
            activarDesactivarCamposVer(data.id_tipo)
        });

        $(`${tabla} tbody`).on('click', '.modificar', async function () {
            let data = myTable.row($(this).parent()).data();
            $('#adjs').hide('fast');
            $('#id_ubicacion').show('fast')
            let ubicacion = await obtener_valores_permiso(data.id_lugar, 116, 1)
            pintar_datos_combo(ubicacion, '.cbx_ubicacion', 'Seleccione la ubicación');
            mostrarSolicitud(data)
        });

        $(`${tabla} tbody`).on('click', '.tramitar_seg', async function () {
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.id
            data_seg = data
            $('#form_gestionar_solicitud').get(0).reset()
            $('#filtro_clas').css('display', 'none');
            let permisos = await obtener_valores_permiso(data.id_tipo, 144, 2)
            pintar_datos_combo(permisos, ".cbx_clasificacion", "Seleccione la clasificacion")
            $("#modal_gestion_y_estados .modal-title").html('<span class="fa fa-cogs"></span> <span id="text_add_arts">Gestionar Solicitud</span>');
            $('#modal_gestion_y_estados').modal('show');
            $('#filtro_acto').hide('fast')
            $('#sel_agregar_persona_int').hide('fast')
            callbak_activo = () => cambiar_estado_solicitud(data.id, "B_Lab_Prog", "Tramitar Solicitud", 1);
            callbak_activo_alt = () => cambiar_estado_solicitud(data.id, "B_Lab_Tram", "Tramitar Solicitud", 2);
        });

        $(`${tabla} tbody`).on('click', '.enviar_mtto_seg', function () {
            let data = myTable.row($(this).parent().parent()).data();
            id_solicitud = data.id
            data_seg = data
            cambiar_estado_solicitud(data.id, "B_Lab_Env", "Reenviar a mantenimiento", 2);
        });

        $(`${tabla} tbody`).on('click', '.finalizar_seg', function () {
            let { id } = myTable.row($(this).parent()).data();
            id_solicitud = id
            preparar_finalizar_sst(id)
            $('#modal_razones_fina').modal('show');
        });

        $(`${tabla} tbody`).on('click', '.cancelar_seg', function () {
            let { id } = myTable.row($(this).parent()).data();
            cambiar_estado_solicitud(id, "B_Lab_Canc", "Cancelar solicitud", 2);
        });

        $(`${tabla} tbody`).on('click', '.rechazar_seg', function () {
            let { id } = myTable.row($(this).parent()).data();
            cambiar_estado_solicitud(id, "B_Lab_Rech", "Rechazar solicitud", 2);
        });

        $(`${tabla} tbody`).on('click', '.tramitar_ase', function () {
            let data = myTable.row($(this).parent()).data();
            id_tipo = data.id_tipo
            cambiar_estado_solicitud(data.id, "B_Lab_Tram", "Tramitar solicitud", 1);
            // gestiona_carta(data.id, data.correo, data.fullname)
        });

        $(`${tabla} tbody`).on('click', '.modificar_ase', function () {
            let data = myTable.row($(this).parent()).data();
            Cargar_parametro_buscado_aux(319, ".cbx_beneficiario", "Seleccione el beneficiario");
            $('#modal_modificar_solicitud_asesorias').modal('show')
            $('.margin1').hide('fast')
            mostrarSolicitudAsesoria(data);
        });

        $(`${tabla} tbody`).on('click', '.rechazar_ase', function () {
            let { id } = myTable.row($(this).parent()).data();
            cambiar_estado_solicitud(id, "B_Lab_Rech", "Rechazar solicitud", 2);
        });

        $(`${tabla} tbody`).on('click', '.finalizar_ase', function () {
            let { id } = myTable.row($(this).parent()).data();
            cambiar_estado_solicitud(id, "B_Lab_Fina", "Finalizar solicitud", 1);
        });

        $(`${tabla} tbody`).on('click', '.cancelar_ase', function () {
            let { id } = myTable.row($(this).parent()).data();
            cambiar_estado_solicitud(id, "B_Lab_Canc", "Cancelar solicitud", 2);
        });
    });
    Con_filtros(sw)
}

const filtrarSolicitud = () => {
    let formulario = 'form_filtrar_solicitudes'
    let fordata = new FormData(document.getElementById(formulario));
    let data = formDataToJson(fordata);
    data_filtro = data
    listarSolicitudes(data_filtro)
}
/*
FIN DE FUNCIONES GENERICAS PARA EL MÓDULO DE BIENESTAR LABORAL
*/

/*
INICIO FUNCIONES PARA EL MODULO DE SST
*/
const guardarSolicitudNew = (data) => {
    let modal = '#modal_agregar_solicitud';
    let formulario = 'form_agregar_solicitud';
    data.tipo = id_tipo
    consulta_ajax(`${ruta}agregar_solicitud`, data, resp => {
        let { mensaje, tipo, titulo, id,  nombre, correo } = resp
        data_solicitante = {nombre, correo}
        if (tipo === 'success') {
            myDropzone.processQueue();
            $(`#${formulario}`).get(0).reset();
            $(`${modal}`).modal('hide')
            enviar_correo_estado('B_Lab_Soli', id, '', id_tipo, '');
        }
        MensajeConClase(mensaje, tipo, titulo)
        listarSolicitudes(data_filtro);
    })
}

const guardarSolicitud = () => {
    let fordata = new FormData(document.getElementById("form_agregar_solicitud"));
    let data = formDataToJson(fordata);
    consulta_ajax(`${ruta}agregar_solicitud_validacion`, data, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo === "success") {
            callbak_activo(data)
        } else if (tipo === "info") {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const mostrarInformacionSolicitud = async data => {
    let tabla = '#detalle_solicitud'
    let razones = await listar_razones(data.id)
    let personasActo = await listar_personas(data.id)
    let razones_detalle = ''
    let persona_detalle = ''
    let { lugar, ubicacion, descripcion, fecha_registro, clasificacion, detalle_acto, telefono, fullname, numero_contacto, nombre_persona, parentesco_persona, pariente } = data
    if (clasificacion === null || clasificacion === '') $('#id_clasificacion_solicitd').hide('fast')
    else $('#id_clasificacion_solicitd').show('fast')
    if (detalle_acto === null || detalle_acto === '') $('#detalle_acto').hide('fast')
    else $('#detalle_acto').show('fast')
    if (razones.length === 0) {
        $('#razones').hide('fast')
        razones_detalle = ''
    } else {
        $('#razones').show('fast')
    }
    if (personasActo.length === 0) {
        $('#personas_actos').hide('fast')
        persona_detalle = ''
    } else {
        $('#personas_actos').show('fast')
    }
    razones.map(i => razones_detalle += i.razon + ', ')
    personasActo.map(i => persona_detalle += i.nombre_completo + ', ')

    $(`${tabla} .lugar`).html(lugar)
    $(`${tabla} .valor_solicitante`).html(fullname)
    $(`${tabla} .ubicacion`).html(ubicacion)
    $(`${tabla} .descripcion`).html(descripcion)
    $(`${tabla} .fecha_registro`).html(fecha_registro)
    $(`${tabla} .clasificacion`).html(clasificacion)
    $(`${tabla} .detalle_acto`).html(detalle_acto)
    $(`${tabla} .razones`).html(razones_detalle)
    $(`${tabla} .personas_actos`).html(persona_detalle)

    if (nombre_persona == null || nombre_persona == '') $(`${tabla} .nombre_persona`).html('No Proporcionado.').addClass('fonts-italic')
    else $(`${tabla} .nombre_persona`).html(nombre_persona).removeClass('fonts-italic')
    
    if (numero_contacto == null || numero_contacto == '') $(`${tabla} .numero_contacto`).html('No Proporcionado.').addClass('fonts-italic')
    else $(`${tabla} .numero_contacto`).html(numero_contacto).removeClass('fonts-italic')

    if (parentesco_persona == null || parentesco_persona == '') $(`${tabla} .parentesco_persona`).html('No Proporcionado.').addClass('fonts-italic')
    else $(`${tabla} .parentesco_persona`).html(pariente).removeClass('fonts-italic')
}

const listar_archivos_adjuntos = (id_solicitud) => {
    let tabla = '#tabla_archivos_seguridad'
    $(`${tabla} tbody`)
        .off('click', '.eliminar_adj');
    consulta_ajax(`${ruta}listar_archivos_seguridad`, { id_solicitud }, resp => {
        cantidad_adj = resp.length
        resp.map(i => {
            if (i.tipo_adj == 2) i.tipo_adj = 'Soporte'
            if (i.tipo_adj == 1) i.tipo_adj = 'Evidencia'
        })
        const myTable = $(`${tabla}`).DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [
                {
                    render: function (data, type, full, meta) {
                        let { nombre_guardado } = full;
                        if (nombre_guardado == null) return 'N/A';
                        else return `<a target='_blank' href='${Traer_Server()}${ruta_archivos}${nombre_guardado}' style="background-color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
                    }
                },
                {
                    data: "nombre_real"
                },
                {
                    data: 'tipo_adj'
                },
                {
                    data: "fecha_registro"
                },
                {
                    data: "fullname"
                },
                {
                    data: "acciones"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $(`${tabla} tbody`).on('click', '.eliminar_adj', function () {
            let data = myTable.row($(this).parent()).data();
            let { id, id_solicitud } = data
            cambiarEstadoAdjuntos(id, id_solicitud)
        });
    })
}

const listar_estados_solicitud = (id_estado) => {
    let tabla = '#tabla_detalle_estado_solicitud'
    let i = 0
    consulta_ajax(`${ruta}detalle_estados`, { id_estado }, resp => {
        const myTable = $(`${tabla}`).DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
                {
                    render: function (data, type, full, meta) {
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
                    data: "fullname"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });
    })
}

const listar_estados_manteminiento = (id_sol) => {
    let tabla = '#tabla_detalle_estado_mantenimiento'
    let i = 0
    let msj = 'Petición ejecutada, por favor revisar'
    consulta_ajax(`${ruta}detalle_mantenimiento`, { id_sol }, resp => {
        resp.map(i => { if (i.estado_solicitud === 'Man_Eje') return i.observacion = msj })
        const myTable = $(`${tabla}`).DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
                {
                    data: "ver"
                },
                {
                    data: "estado"
                },
                {
                    data: "fecha_registra"
                },
                {
                    data: "fullname"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $(`${tabla} tbody`).on('click', 'tr', function () {
            $(`${tabla} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $(`${tabla} tbody`).on('dblclick', 'tr', function () {
            $('#modal_detalle_mtto').modal('show');
            let data = myTable.row(this).data();
            mostrarDetalleMtto(data)
        });

        $(`${tabla} tbody`).on('click', '.ver', function () {
            $('#modal_detalle_mtto').modal('show');
            let data = myTable.row($(this).parent()).data();
            mostrarDetalleMtto(data)
        });
    })
}

const mostrarSolicitud = (data) => {
    let { id_lugar, descripcion, id_ubicacion } = data

    callbak_activo = dataMod => {
        dataMod.id = data.id
        modificarSolicitud(dataMod)
    }

    $('#id_lugar').val(id_lugar)
    $('#descripcion').val(descripcion)
    $('#id_ubicacion').val(id_ubicacion)
}

const mostrarDetalleMtto = (data) => {
    let tabla = '#detalle_mtto'
    let { estado, fecha_registra, fullname, observacion, num_solicitud } = data
    $(`${tabla} .mtto_estado`).html(estado)
    $(`${tabla} .mtto_fecha`).html(fecha_registra)
    $(`${tabla} .mtto_usuario`).html(fullname)
    $(`${tabla} .mtto_observaciones`).html(observacion)
    $(`${tabla} .mtto_no_solicitud`).html(num_solicitud || '---')
}

const modificarSolicitud = (data) => {
    let formulario = '#form_agregar_solicitud'
    let modal = '#modal_agregar_solicitud'
    consulta_ajax(`${ruta}modificar_solicitud`, data, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            $(`${formulario}`).get(0).reset();
            $(`${modal}`).modal("hide");
            listarSolicitudes(data_filtro);
            MensajeConClase(mensaje, tipo, titulo);
        } else if (tipo === 'info') {
            MensajeConClase(mensaje, tipo, titulo);
            $(`${modal}`).hide()
            listarSolicitudes(data_filtro)
            $(`${formulario}`).get(0).reset()
        }
    });
}

const gestionar_solicitud = () => {
    let fordata = new FormData(document.getElementById("form_gestionar_solicitud"));
    let data = formDataToJson(fordata);
    tip_clasif = data.id_clasificacion
    mtto = data.mtto || '0'
    telefono = data.telefono
    data.personas = personas.length
    descripcion_acto = data.descripcion_acto
    id_tipo_persona = data.id_tipo_persona
    consulta_ajax(`${ruta}gestionar_validacion`, data, resp => {
        let { mensaje, titulo, tipo } = resp;
        if (tipo === "success") {
            if (data.id_clasificacion === 'Seg_Tip_Acto') {
                callbak_activo()
            } else {
                callbak_activo_alt()
            }
        } else if (tipo === "info") {
            MensajeConClase(mensaje, tipo, titulo)
        }
    });
}

const listarPersonasAgregadasInternas = () => {
    $('#persona_asignada_int').html(`<option selected disabled id="informacion_persona_int" value="Per_Agre_Int"> ${personas.length} Persona(s) </option>`)
    personas.map((i, key) => {
        $('#persona_asignada_int').append(`<option value='${i.id}'> ${i.nombre} </option>`)
    })
}

const buscarPersonas = (dato) => {
    consulta_ajax(`${ruta}buscar_persona`, { dato }, (resp) => {
        $(`#tabla_personas_busqueda tbody`)
            .off('click', '.personas')
        const myTable = $("#tabla_personas_busqueda").DataTable({
            "destroy": true,
            "searching": false,
            "processing": true,
            'data': resp,
            "columns": [
                {
                    "data": "nombre_completo"
                },
                {
                    "data": "correo"
                },
                {
                    'data': 'identificacion'
                },
                {
                    'defaultContent': '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default personas" ></span>'
                },

            ],
            "language": get_idioma(),
            dom: 'Bfrtip',
            "buttons": [],
        });

        $(`#tabla_personas_busqueda tbody`).on('click', '.personas', function () {
            let data = myTable.row($(this).parent()).data();
            validarPersonaAgregada(data)
        });
    });
}

const validarPersonaAgregada = (data) => {
    let { id, nombre_completo } = data
    if (personas.length === 0) {
        personas.push({ 'id': id, 'nombre': nombre_completo })
        listarPersonasAgregadasInternas()
        MensajeConClase('Persona agregada con exito', 'success', 'Proceso exitoso.!')
    } else {
        let per = 0
        personas.map(i => {
            if (id == i.id) per++
        })
        if (per >= 1) MensajeConClase('La Persona ya fue agregada', 'info', 'Oops.!')
        else {
            personas.push({ 'id': id, 'nombre': nombre_completo })
            MensajeConClase('Persona agregada con exito', 'success', 'Proceso exitoso.!')
            listarPersonasAgregadasInternas()
            per = 0
        }
    }
}

const eliminarPersonasAgregadasInterna = (comp) => {
    swal({
        title: "Estas Seguro ?",
        text: "Esta persona será eliminado",
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
                let sw = true;
                let data = personas.filter((key) => key.id != comp);
                personas = data
                if (sw) {
                    listarPersonasAgregadasInternas(personas)
                    swal.close();
                } else {
                    MensajeConClase("La persona no fue encontrada, intente de nuevo.", "info", 'Oops.!');
                }
                return;
            }
        });
}

const preparar_finalizar_sst = async (id) => {
    $("#modal_negar_solicitud").modal();
    causas_gen = await buscar_razones(143);
    listar_causas(causas_gen);
    $("#btn_negar").off("click");
    $("#btn_negar").on('click', async () => {
        causas_gen = causas_gen.filter(i => i.agregado != 0)
        if (causas_gen.length === 0) {
            causas_gen = await buscar_razones(143);
            MensajeConClase('Seleccione por lo menos una razón', 'info', 'Oops.!')
        } else {
            causas_gen.forEach(element => {
                if (element.agregado == 1) mensaje_correo += element.causa + ', ';
            });
            cambiar_estado_solicitud(id, "B_Lab_Fina", "Finalizar Solicitud", 1);
            $('#modal_razones_fina').modal('hide')
        }
    });
}
/*
FIN FUNCIONES PARA EL MODULO DE SST
*/

/* 
INICIO DE LAS FUNCIONES PARA MODULO ASESORIAS 
*/
const guardarSolicitudAsesoria = () => {
    let formulario = 'form_agregar_solicitud_asesorias';
    let fordata = new FormData(document.getElementById(formulario));
    let data = formDataToJson(fordata);
    consulta_ajax(`${ruta}agregar_solicitud_asesoria_validacion`, data, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo === "success") {
            guardarSolicitudAsesoriaNew(data)
        }
        MensajeConClase(mensaje, tipo, titulo);
        $('#modal_asesorias_modulos').modal('hide')
    });
}

const guardarSolicitudAsesoriaNew = (data) => {
    let formulario = 'form_agregar_solicitud_asesorias';
    let modal = '#modal_agregar_solicitud_asesorias'
    data.id_tipo = id_tipo
    data.id_clasificacion = id_clasificacion
    consulta_ajax(`${ruta}agregar_solicitud_asesoria`, data, resp => {
        let { mensaje, tipo, titulo, id, nombre, correo } = resp
        data_solicitante = {nombre, correo}
        if (tipo === 'success') {
            $(`#${formulario}`).get(0).reset();
            $(`${modal}`).modal('hide')
            enviar_correo_estado('B_Lab_Soli', id, '', id_tipo, id_clasificacion);
        }
        MensajeConClase(mensaje, tipo, titulo)
        listarSolicitudes(data_filtro);
    })
}

const mostrarSolicitudAsesoria = (data) => {
    let { descripcion, nombre_persona, numero_contacto, parentesco_persona } = data

    $("#select_beneficiario_md").css("display", "block");
    callbak_activo = dataMod => {
        dataMod.id = data.id
        modificarSolicitudAsesoria(dataMod)
    }
    if(parentesco_persona ==null){
        $("#select_beneficiario_md").css("display", "none");
    }else if(parentesco_persona =="Id_Benef_Trabajador"){
        $('#input_nombre_md').html('')
    }else{
      $('#input_nombre_md').html('<input type="text" id="nombre_persona_md" name="nombre_persona_md" class="form-control" placeholder="Nombre de la Persona" >')
      $('#nombre_persona_md').val(nombre_persona)
    }
    
    $("#id").val(data.id);
    $("#id_trabajador_solicitante_md").val(data.id_solicitante);
    $('#descripcion_asesoria_md').val(descripcion)
    $('#numero_contacto_md').val(numero_contacto)
    $('#id_beneficiario_md').val(parentesco_persona)
}

const modificarSolicitudAsesoria = (datas) => {
    let fordata = new FormData(document.getElementById("form_modificar_solicitud_asesorias"));
    let data = formDataToJson(fordata);
    consulta_ajax(`${ruta}modificar_solicitud_asesoria`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listarSolicitudes(data_filtro);
            $("#form_modificar_solicitud_asesorias").get(0).reset();
            $("#modal_modificar_solicitud_asesorias").modal("hide");
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const gestiona_carta = async (id, correo, nombre, nombre_correo, tipo) => {
    await generar_carta(id);
    const acta = `<a href="${Traer_Server()}archivos_adjuntos/bienestar_laboral/asesorias/${id}.pdf">Ver Carta</a>`;
    const server = `<a href="${Traer_Server()}index.php/bienestar_laboral/${id}"><b>agil.cuc.edu.co</b></a>`;
    let mensaje = `<p>Se le informa que la asesoria solicitada por ${nombre} ha sido tramitada, el funcionario debe dirigirse a la entidad encargada, presentando la carga adjuntada para la realizacion de la asesoria, en ella encontrara la informacion de contacto de la entidad e informacion complementaria acerca de la asesoria.</p>
    <p>En el siguiente enlace puedes encontrar la carta : ${acta}</p>
    <p>Puede ingresar al aplicativo AGIL para seguir el estado de la solicitud. ${server}</p>`;
    enviar_correo_personalizado("vist", mensaje, correo, nombre, "Bienestar laboral CUC", nombre_correo, "ParCodAdm", tipo);
}
/*
FIN DE LAS FUNCIONES PARA MODULO ASESORIAS 
*/

/*
INICIO FUNCIONES AUXILIARES PARA LOS MÓDULOS
*/
let recibir_archivos = () => {
    Dropzone.options.Subir = {
        url: `${ruta}recibir_archivos`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
        method: "post", //por defecto es post se puede poner get, put, etc.....
        withCredentials: false,
        parallelUploads: 10, //Cuanto archivos subir al mismo tiempo
        uploadMultiple: false,
        maxFilesize: 1000, //Maximo Tama�o del archivo expresado en mg
        paramName: "file", //Nombre con el que se envia el archivo a nivel de parametro
        createImageThumbnails: true,
        maxThumbnailFilesize: 1000, //Limite para generar imagenes (Previsualizacion)
        thumbnailWidth: 154, //Medida de largo de la Previsualizacion
        thumbnailHeight: 154, //Medida alto Previsualizacion
        filesizeBase: 1000,
        maxFiles: 10, //si no es nulo, define cu�ntos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
        params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
        clickable: true,
        ignoreHiddenFiles: true,
        acceptedFiles: "image/*, .pdf, .docx, .doc", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
        acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
        autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas

        error: function (response) {
            if (!response.xhr) {
                MensajeConClase("Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,csv!", "info", "Oops!");
            }
        },
        success: function (file, response) {
            let { mensaje, tipo, titulo } = JSON.parse(response)
            MensajeConClase(mensaje, tipo, titulo);
            $("#modal_enviar_archivos").modal('hide');
            if (id_solicitud) listar_archivos_adjuntos(id_solicitud)
        },

        init: function () {
            num_archivos = 0;
            myDropzone = this;
            this.on("addedfile", function (file) {
                num_archivos++;
            });
            this.on("removedfile", function (file) {
                num_archivos--;
            });
            myDropzone.on("complete", function (file) {
                myDropzone.removeFile(file);
                cargados++;
            });
            myDropzone.on("processing", function (file) {
                this.options.params = { solicitud: id_solicitud }
            });
            myDropzone.on("maxfilesexceeded", function (file) {
                MensajeConClase(`Ya has llegado al número máximo de archivos a subir.\n Solo se permite subir ${this.options.maxFiles} archivo(s)`, "info", "Oops!");
            });
        },
        autoQueue: true,
        addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacci�n.
        previewsContainer: null, //define d�nde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
        capture: null,
        dictDefaultMessage: "Arrastra los archivos aqui para subirlos",
        dictFallbackMessage: "Su navegador no soporta arrastrar y soltar para subir archivos.",
        dictFallbackText: "Por favor utilize el formuario de reserva de abajo como en los viejos timepos.",
        dictFileTooBig: "La imagen revasa el tamaño permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.",
        dictInvalidFileType: "No se puede subir este tipo de archivos.",
        dictResponseError: "Server responded with {{statusCode}} code.",
        dictCancelUpload: "Cancel subida",
        dictCancelUploadConfirmation: "¿Seguro que desea cancelar esta subida?",
        dictRemoveFile: "Eliminar archivo",
        dictRemoveFileConfirmation: null,
        dictMaxFilesExceeded: "Se ha excedido el numero de archivos permitidos.",
    };
}

const listar_empleados = async (tipoPermiso) => {
    let empleados = await obtener_empleados(tipoPermiso);
    pintar_empleados_combo(empleados, ".cbx_aux_lab", "Seleccionar Persona");
}

const enviar_correo_estado = async (estado, id, motivo, tipo_soli, clasificacion) => {
    let sw = false;
    let { nombre, correo } = data_solicitante;
    let ser = `<a href="${server}index.php/bienestar_laboral/${id}"><b>agil.cuc.edu.co</b></a>`;
    let tipo = 1;
    let personas_permiso = [];
    let correos = [{correo}];
    let titulo = 'Solicitud Enviada';
    let mensaje = `Se informa que la solicitud realizada por ${nombre}, ha sido enviada y se encuentra disponible para el proceso de verificacion, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br><br><b>Nota:</b> Las solicitudes que se registren en horas de la mañana seran tramitadas en la tarde y las solicitudes que se registren en horas de la tarde seran tramitadas en horas de la mañana del dia siguiente.`;
    if (estado == 'B_Lab_Rech') {
        sw = true;
        titulo = 'Solicitud Negada';
        mensaje = `Se informa que su solicitud ha sido devuelta porque no cumple con lo siguiente: ${motivo}.<br><br>Mas informaci&oacuten en ${ser}`;
    } else if (estado == 'B_Lab_Fina') {
        sw = true;
        titulo = 'Solicitud Finalizada';
        mensaje = `Se informa que su solicitud ha sido finalizada con exito, realizando lo siguiente: ${mensaje_correo} a partir de este momento puede ingresar al aplicativo AGIL para revisar la factura adjuntada si asi lo requirio. <br><br>Mas informaci&oacuten en ${ser}`;
    } else if (estado == 'B_Lab_Soli'){
        sw = true;
        tipo = 3;
        personas_permiso = await obtener_personas_permisos(tipo_soli, estado)
        personas_permiso.forEach(persona => {
            correos.push({correo : persona.correo})
        });
    } else if (estado == 'B_Lab_Tram'){
        let val_par_gen = ''
        tipo = 3;
        if(tipo_soli === 'Lab_Ases' && clasificacion === 'Ase_Tip_Viv'){
            val_par_gen = await obtener_parametros_generales('Info_Ase_Viv');
            sw = true;
            titulo = 'Solicitud Tramitada';
            mensaje = `<p>Se le informa que la asesoria solicitada por ${nombre} ha sido tramitada, ${val_par_gen.valor} le colaborara en la asesoria de vivienda, coordine su cita comunicandose al teléfono ${val_par_gen.valorz} o escribir al correo ${val_par_gen.valory}</p>
            <p>Descubre que tener casa propia es fácil.</p>
            <p>Puede ingresar al aplicativo AGIL para seguir el estado de la solicitud. ${ser}</p>`;
            personas_permiso = await obtener_personas_permisos(clasificacion, estado)
            personas_permiso.forEach(persona => {
                correos.push( {correo: persona.correo } );
            });      
        }else if(tipo_soli === 'Lab_Ases' && clasificacion === 'Ase_Tip_Fin'){
            val_par_gen = await obtener_parametros_generales('Info_Ase_Fin');
            sw = true;
            titulo = 'Solicitud Tramitada';
            mensaje = `<p>Se le informa que la asesoria solicitada por ${nombre} ha sido tramitada, ${val_par_gen.valor}, esta para colaborarle en su asesoria financiera, coordine su cita comunicandose al teléfono ${val_par_gen.valorz} o al correo ${val_par_gen.valory}, en el horario de ${val_par_gen.valora}.</p>
            <p>Puede ingresar al aplicativo AGIL para seguir el estado de la solicitud. ${ser}</p>`;       
            personas_permiso = await obtener_personas_permisos(clasificacion, estado)
            personas_permiso.forEach(persona => {
                correos.push( {correo: persona.correo } );
            });
        }
    }
    
    let correo_destinatario = (tipo === 3) ? correos : correo
    if (sw) enviar_correo_personalizado("blab", mensaje, correo_destinatario, nombre, "Bienestar Laboral CUC", `Bienestar Laboral - ${titulo}`, "ParCodAdm", tipo);
}

const cambiarEstadoAdjuntos = (id, id_solicitud) => {
    swal({
        title: "Estas Seguro ?",
        text: `El archivo será eliminado`,
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
                consulta_ajax(`${ruta}cambiar_estado_adj`, { id, id_solicitud }, resp => {
                    let { tipo } = resp;
                    if (tipo == "success") {
                        listar_archivos_adjuntos(id_solicitud);
                        swal.close();
                    }
                })
            }
        }
    )
}

const cambiar_estado_solicitud = (id, estado, titulo, tipo) => {
    const confirm_normal = (id, estado, title) => {
        swal({
            title,
            text: "Tener en cuenta que, al realizar esta accíon la solicitud sera habilitada para el siguiente  proceso, si desea continuar debe  presionar la opción de 'Si, Entiendo'!",
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
                    cambiarEstado(estado, id);
                    $('#modal_gestion_y_estados').modal('hide');
                }
            });
    }

    const confirm_input = (id, estado, title) => {
        swal({
            title,
            text: "Tener en cuenta que al realizar esta accíon, no podra revertir la decision.",
            type: "input",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Aceptar!",
            cancelButtonText: "Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            inputPlaceholder: `Observaciones`
        }, function (mensaje) {

            if (mensaje === false)
                return false;
            if (mensaje.trim() === "") {
                swal.showInputError(`Debe Ingresar una observación.!`);
            } else {
                cambiarEstado(estado, id, mensaje.trim());
                $('#modal_gestion_y_estados').modal('hide');
            }
        });
    }
    tipo == 1 ? confirm_normal(id, estado, titulo) : confirm_input(id, estado, titulo);
}

const cambiarEstado = (estado, id, observaciones = '') => {
    let { descripcion, lugar, ubicacion } = data_seg
    consulta_ajax(`${ruta}cambiarEstado`, { estado, id, observaciones, tipo: tip_clasif, mtto: mtto, tel: telefono, personas: personas, id_tipo_persona: id_tipo_persona, desp_acto: descripcion_acto, descripcion, lugar, ubicacion, data_razon: causas_gen, id_tipo }, resp => {
        const { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            myDropzone.processQueue();
            swal.close();
            //enviar_correo_estado(estado, id, observaciones, id_tipo, tip_clasif);
            mostrar_notificaciones()
            mensaje_correo = '';
            id_tipo_persona = null;
           // validar_envio_carta(id)
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        listarSolicitudes(data_filtro)
    });
}

const mostrar_notificaciones = () => {
    let url = Traer_Server() + "index.php/bienestar_laboral_control/mostrar_notificaciones_seguridad";
    consulta_ajax(url, {}, datos => {
        $(".n_notificaciones").html(datos.length);
        pintar_notificaciones_solicitudes({ 'container': '#panel_notificaciones_seguridad', 'titulo': 'Solicitudes enviadas a manteminiento' }, datos);
        if (datos.length > 0) $('#modal_notificaciones_seguridad').modal('show');
    });
}

const pintar_notificaciones_solicitudes = (data, resp) => {
    let { container, titulo } = data;
    let resultado = ``;
    let detalle = ''
    resp.map(i => {
        detalle = `Solicitud realizada el: ${i.fecha_registro}`;
        resultado = `${resultado}<a href="#" class="list-group-item"><span class="badge" onclick='auxiliar_listar(${i.id})'>Ver</span><p class="list-group-item-text">Solicitud No. ${i.id}</p><h4 class="list-group-item-heading">${detalle}</h4></a>`;
    })
    $(container).html(`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`);
}

const ocultarBtn = (estado) => {
    if (estado !== 'B_Lab_Soli') {
        if (estado === 'B_Lab_Fina') {
            $('#agregar_soporte_nuevos').show()
        } else {
            $('#agregar_soporte_nuevos').hide()
        }
        $('#agregar_adjuntos_nuevos').hide()
    } else {
        $('#agregar_adjuntos_nuevos').show()
        $('#agregar_soporte_nuevos').hide()
    }
}

const activarDesactivarCamposVer = (tipo) => {
    if (tipo == 'Lab_Ases') {
        $('#lugar_soli').hide('fast')
        $('#ubicacion_soli').hide('fast')
        $('#contenedor_tabla_archivos_seguridad').hide('fast')

        $('#numero_contacto_soli').show('fast')
        $('#nombre_persona_soli').show('fast')
        $('#parentesco_persona_soli').show('fast')
    } else if (tipo == 'Lab_Seg_Tra') {
        $('#lugar_soli').show('fast')
        $('#ubicacion_soli').show('fast')
        $('#contenedor_tabla_archivos_seguridad').show('fast')

        $('#numero_contacto_soli').hide('fast')
        $('#nombre_persona_soli').hide('fast')
        $('#parentesco_persona_soli').hide('fast')

    }
}

const btn_mtto = (clas, mtto) => {
    (clas === 'Seg_Tip_Cond' && mtto === '1') ? $('#ver_mantenimiento').removeClass('oculto') : $('#ver_mantenimiento').addClass('oculto')
}

function Con_filtros(sw) {
    if (sw) {
        $(".mensaje-filtro").show("fast");
    } else {
        $(".mensaje-filtro").css("display", "none");
    }
}

const auxiliar_listar = (id) => {
    return listarSolicitudes({ 'id': id })
}

const administrar_elementos = item => {
    if (item == 'menu') {
        $("#menu_principal").fadeIn(1000);
        $("#container_solicitudes").css("display", "none");
    } else if (item == 'solicitudes') {
        $("#menu_principal").css("display", "none");
        $("#container_solicitudes").fadeIn(1000);
    }
}


const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
    $(combo).html(`<option value=''> ${mensaje}</option>`);
    datos.forEach(element => {
        $(combo).append(
            `<option value='${element.id}'> ${element.valor} </option>`
        );
    });
    $(combo).val(sele);
}

const pintar_empleados_combo = (datos, combo, mensaje, sele = "") => {
    $(combo).html(`<option value=''> ${mensaje}</option>`);
    datos.forEach(element => {
        $(combo).append(
            `<option value='${element.id}'> ${element.nombre_completo}</option>`
        );
    });
    $(combo).val(sele);
}

const buscar_razones = buscar => {
    return new Promise(resolve => {
        let url = `${ruta}buscar_razones`;
        consulta_ajax(url, { buscar }, resp => {
            resolve(resp);
        });
    });
}

const listar_razones = id => {
    return new Promise(resolve => {
        let url = `${ruta}listar_razones`;
        consulta_ajax(url, { id }, resp => {
            resolve(resp);
        });
    });
}

const listar_personas = id => {
    return new Promise(resolve => {
        let url = `${ruta}listar_personas`;
        consulta_ajax(url, { id }, resp => {
            resolve(resp);
        });
    });
}

const obtener_empleados = (tipoPermiso, buscar ) => {
    return new Promise(resolve => {
        let url = `${ruta}obtener_empleados`;
        consulta_ajax(url, { tipoPermiso, buscar }, resp => {
            resolve(resp);
        });
    });
} 

const obtener_personas_permisos = (clasificacion, estado) => {
    return new Promise(resolve => {
        let url = `${ruta}obtener_personas_permisos`;
        consulta_ajax(url, { clasificacion, estado }, resp => {
            resolve(resp);
        });
    });
}

const obtener_parametros_generales = (id_aux) => {
    return new Promise(resolve => {
        let url = `${ruta}obtener_parametros_generales`;
        consulta_ajax(url, { id_aux}, resp => {
            resolve(resp);
        });
    });
}

const listar_causas = (causas) => {
    $('#tabla_razones_fina tbody')
        .off("click", ".asignar")
        .off("click", ".eliminar");
    consulta_ajax(`${ruta}verificar_causas`, { causas }, resp => {
        const myTable = $('#tabla_razones_fina').DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "causa"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        $("#tabla_razones_fina tbody").on("click", ".asignar", function () {
            let { causa, id_aux } = myTable.row($(this).parent().parent()).data();
            if (id_aux === 'Lab_Otro') {
                swal({
                    title: "Otro",
                    text: "Escriba la accion que llevo acabo para la finalizacion del la solicitud.",
                    type: "input",
                    showCancelButton: true,
                    confirmButtonColor: "#D9534F",
                    confirmButtonText: "Guardar!",
                    cancelButtonText: "Cancelar!",
                    allowOutsideClick: true,
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    inputPlaceholder: `Causa`
                },
                    function (message) {
                        if (!message) {
                            swal.showInputError(`Por Favor Ingrese la causa por la cual niega la solicitud`);
                        } else {
                            let sw = 0;
                            causas_gen.forEach(element => {
                                if (element.causa == message) sw = 1;
                            });
                            if (sw == 1) swal.showInputError(`Ya ingreso una causa igual a esta.`);
                            else {
                                let data = {
                                    'causa': message,
                                    'agregado': 1,
                                }
                                causas_gen.push(data);
                            }
                            swal.close();
                            listar_causas(causas_gen);
                        }
                    }
                );
            } else {
                let index = causas_gen.findIndex(obj => obj.causa == causa);
                causas_gen[index].agregado = 1;
                listar_causas(causas_gen);
            }
        })

        $("#tabla_razones_fina tbody").on("click", ".eliminar", function () {
            let { causa } = myTable.row($(this).parent().parent()).data();
            let index = causas_gen.findIndex(obj => obj.causa == causa);
            causas_gen[index].agregado = 0;
            listar_causas(causas_gen);
        });
    });
}

const listar_procesos = (tipoPermiso, id) => {
    $(`#tabla_permisos_lab tbody`)
        .off("click", "tr")
        .off("click", "tr td:nth-of-type(1)")
        .off("click", "tr .asignar_lab")
        .off("click", "tr .retirar_lab")
        .off("click", "tr .administrar_lab");
    consulta_ajax(`${ruta}listar_procesos_lab`, { tipoPermiso, id }, resp => {
        const myTable = $("#tabla_permisos_lab").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "nombre"
                },
                {
                    data: "descripcion"
                },
                {
                    data: "accion"
                }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });
        $(`#tabla_permisos_lab tbody`).on("click", "tr .asignar_lab", function () {
            let { id_aux } = myTable.row($(this).parent().parent()).data();
            asignar_proceso_persona(id, id_aux, tipoPermiso);
        });
        $(`#tabla_permisos_lab tbody`).on("click", "tr .retirar_lab", function () {
            let { tipo } = myTable.row($(this).parent().parent()).data();
            retirar_proceso_persona(id, tipo);
        });
        $(`#tabla_permisos_lab tbody`).on("click", "tr .administrar_lab", function () {
            let data = myTable.row($(this).parent().parent()).data();
            $("#administrar_estados_laboral").modal();
            listar_estados_procesos(data.tipo, id, data.id_aux);
        });
    });
}

const listar_estados_procesos = (id, id_auxiliar, tipo_sol) => {
    $(`#tabla_estados_lab tbody`)
        .off("click", "tr")
        .off("click", "tr td:nth-of-type(1)")
        .off("click", "tr .asignar_est")
        .off("click", "tr .retirar_est");
    let i = 0;
    consulta_ajax(`${ruta}listar_estados_lab`, { id }, resp => {
        const myTable = $("#tabla_estados_lab").DataTable({
            destroy: true,
            processing: true,
            "autoWidth": false,
            data: resp,
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
        $(`#tabla_estados_lab tbody`).on("click", "tr .asignar_est", function () {
            let { id_aux } = myTable.row($(this).parent().parent()).data();
            asignar_estado_proceso(id, id_aux, id_auxiliar, tipo_sol);
        });
        $(`#tabla_estados_lab tbody`).on("click", "tr .retirar_est", function () {
            let { tipo } = myTable.row($(this).parent().parent()).data();
            retirar_estado_proceso(id, tipo);
        });
    });
}

const asignar_proceso_persona = (id, id_aux, tipoPermiso) => {
    consulta_ajax(`${ruta}asignar_proceso_persona`, { id, id_aux, tipoPermiso }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        listar_procesos(tipoPermiso, id);
    });
}

const asignar_estado_proceso = (id, id_aux, id_auxiliar, tipo_sol) => {
    consulta_ajax(`${ruta}asignar_estado_proceso`, { id, id_aux, id_auxiliar, tipo_sol }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        listar_estados_procesos(id);
    });
}

const retirar_proceso_persona = (id, tipo) => {
    consulta_ajax(`${ruta}retirar_procesos_persona`, { tipo }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        listar_procesos(tipoPermiso, id);   
    });
}

const retirar_estado_proceso = (id, tipo) => {
    consulta_ajax(`${ruta}retirar_estado_proceso`, { tipo }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            MensajeConClase(mensaje, tipo, titulo);
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        listar_estados_procesos(id);
    });
}

const validar_envio_carta = (id) => {
    consulta_ajax (`${ruta}consultar_solicitud`, { id }, async (resp)  => {
        let { id_tipo, id_estado_solicitud, id_clasificacion, nombre_completo, correo } = resp
        let correos = [{correo}]
        if (id_tipo === 'Lab_Ases' && id_clasificacion === 'Ase_Tip_Psi' && id_estado_solicitud === 'B_Lab_Tram') {
            let val_par_gen_psi = await obtener_parametros_generales('Info_Ase_Psi');
            correos.push({correo : val_par_gen_psi.valory})
            gestiona_carta(id, correos, nombre_completo, 'Bienestar Laboral - Carta Asesoría Psicológica', 3)
        } else if (id_tipo === 'Lab_Ases' && id_clasificacion === 'Ase_Tip_Jur' && id_estado_solicitud === 'B_Lab_Tram') {
            let val_par_gen_jur = await obtener_parametros_generales('Info_Ase_Jur');
            correos.push({correo : val_par_gen_jur.valory})
            gestiona_carta(id, correos, nombre_completo, 'Bienestar Laboral - Carta Asesoría Jurídica', 3)
        } else {
            return false;
        }
    });
}

const generar_carta = id => {
    console.log("generando");
    const route = `${Traer_Server()}index.php/bienestar_laboral/generar_carta/${id}`;
    window.open(route, '_blank');
    window.focus()
    console.log("generado");
    return true;
}

function SelecBene(dato){
    if(dato==0){
        $Beneficiario = $("#id_beneficiario").val();
        if($Beneficiario !='Id_Benef_Trabajador'){
            $('#input_nombre').html('<input type="text" id="nombre_persona" name="nombre_persona" class="form-control" placeholder="Nombre de la Persona" >')
        }else{
            $('#input_nombre').html('')
        }
    }else{
        $Beneficiario = $("#id_beneficiario_md").val();
        if($Beneficiario !='Id_Benef_Trabajador'){
            $('#input_nombre_md').html('<input type="text" id="nombre_persona_md" name="nombre_persona_md" class="form-control" placeholder="Nombre de la Persona" >')
        }else{
            $('#input_nombre_md').html('')
        }
    }
   
}
function BuscarSolicitante(){
    let dato = $("#txt_solicitantes_buscar").val();
    let tipo = "Per_emp";
    id_tipo_persona = tipo;
    if (dato.length ==0) {
        MensajeConClase("Ingrese dato a buscar.", "info", "Oops.!");
    } else {
        let data_activa = { dato, tipo };
        buscar_personas(data_activa, callbak_activo, `${ruta}buscar_solicitantes`, "#tabla_solicitantes_busqueda", "persona");
        document.getElementById("div_busqueda_solicitante").style.display = "block";
        document.getElementById("div_formulario_solicitud").style.display = "none";
    }
    return false;
}

const info_solicitante = (data) => {
    id_solicitante=data.id;
    $("#id_trabajador_solicitante").val(id_solicitante);
    document.getElementById("div_busqueda_solicitante").style.display = "none";
    document.getElementById("div_formulario_solicitud").style.display = "block";
}
/*
FIN FUNCIONES AUXILIARES PARA LOS MODULOS
*/
