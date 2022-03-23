const ruta_salud = () => `${Traer_Server()}index.php/salud_control/`;
const RUTA = `${Traer_Server()}index.php/salud_control/`;
let callbak_activo = resp => { };
let callbak_listar = resp => { };
let id_idparametro = '';
let id_solicitante = '';
let id_solicitud = '';
let riesgos_sele = [];
let id_historia_clinica = '';
let modal_menu = '';
let sel_diagnostico = 1;
let cod_diagnostico = '';
let id_tipo_persona = '';
const ruta_adjuntos = 'archivos_adjuntos/salud/resultado_examen/';

$(document).ready(function () {
    $('textarea').attr('maxlength', '1000');
    $(".regresar_menu").click(function () {
        administrar_modulo('regresar_menu')
    });

    $('#listado_atenciones').click(() => {
        callbak_listar = (resp) => mostrar_seguimiento_covid(resp);
        administrar_modulo('listado_atenciones')
    });

    $("#Sal_Sol_Ate").click(() => {
        callbak_activo = (resp) => nueva_atencion(resp);
        administrar_modulo('solicitud_atencion')
    });

    $("#btn_atencion").click(() => {
        $("#modal_asignar_servicio").modal();
    });

    $("#Sal_His_Ocup").click(() => {
        callbak_activo = (resp) => historia_ocupacional(resp);
        administrar_modulo('historia_medica');
    });

    $("#Sal_His_Med_Gen").click(() => {
        callbak_activo = (resp) => historia_medicina_general(resp);
        administrar_modulo('solicitud_atencion');
    });

    $("#Sal_Bit_Enf").click(() => {
        callbak_activo = (resp) => bitacora(resp);
        administrar_modulo('solicitud_atencion');
    });
//Protocolo covid
    $("#Sal_Sol_Cov").click(() => {
        callbak_activo = (resp) =>mostrar_formucovid(resp);//Llama a la funcion despues de dar check
        administrar_modulo('solicitud_atencion'); //llama al modal principal
    });
//hasta aqui
    $("#estado_bitacora").click(() => {
        bitacoras_paciente(id_solicitante);
        $("#container_atenciones_bitacora").addClass("oculto");
        $("#container_bitacoras_paciente").removeClass("oculto");
        $("#modal_atenciones_bitacora").modal();
    });

    $("#guardar_bitacora").click(() => {
        guardar_datos_paciente('add_bitacora', 'form_bitacora', id_solicitud, '0', 'modal_crear_bitacora');
        callbak_listar = (resp) => bitacoras_paciente(id_solicitante);
    });

    $("#crear_permisos").click(() => {
        $("#container_turnos_bib").addClass("oculto");
        $("#container_permisos").removeClass("oculto");
        $("#crear_servicio").removeClass("active");
        $("#crear_permisos").addClass("active");
        listar_valor_parametro(148,'tabla_permisos');
    });

    $("#crear_servicio").click(() => {
        $("#container_turnos_bib").removeClass("oculto");
        $("#container_permisos").addClass("oculto");
        $("#crear_servicio").addClass("active");
        $("#crear_permisos").removeClass("active");
        listar_valor_parametro(147,'tabla_servicios');
    });

    $(".filtrar_atenciones").click( async function () {
        callbak_listar = (resp) => mostrar_seguimiento_covid(resp);
        servicios = await obtener_valor_parametro(147);
        pintar_datos_combo(servicios, '.cbxservicio', 'Seleccione Servicio');
        consulta_ajax(`${ruta_salud()}listar_permisos_funcionario`, { idparametro:148 }, async (resp) => { 
            $('.cbxtiposolicitud option').remove(); 
            $('.cbxtiposolicitud').html(`<option value=''>Seleccione Tipo de Solicitud</option>`);          
            resp.forEach(elemento => {
                $(".cbxtiposolicitud").append(`<option value='${elemento.id_aux}'> ${elemento.valor}</option>`);
            });
            let found = resp.find(element => element.id_aux == 'Sal_His_Ocup');
            if(found){
                tipo_examen = await obtener_valor_parametro(152);
                tipo_examen.forEach(elemento => {
                    $(".cbxservicio").append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
                });
            }
        });
        $("#modal_filtrar_atenciones").modal();
    });
    $("#nueva_observacion").click(() => {
        $("#modal_nueva_observacion").modal();
      });
      $("#ver_observacion").click(() => {
        $("#modal_ver_observacion").modal();
        Mostrarobservacion();
      });
      $("#ver_tp_reporte").click(() => {
        $("#modal_editar_treporte").modal();
      });

    $(".filtrar_pacientes").click(() => {
        let fecha_filtro = $("#fecha_filtro").val();
        let fecha_filtro_2 = $("#fecha_filtro_2").val();
        let habito = $("#servicio_habito").val();
        let antecedente = $("#antecendete_filtro").val();
        $("#fecha_inicio").val(fecha_filtro);
        $("#fecha_fin").val(fecha_filtro_2);
        $("#diagnostico").val('');
        sel_diagnostico = 2;
        filtrar_pacientes(habito, antecedente, cod_diagnostico, fecha_filtro, fecha_filtro_2);
        $("#modal_filtrar_pacientes").modal();
        $("#modal_filtrar_atenciones").modal("hide");
    });

    $("#btn_filtro").click(() => {
        let habito = $("#servicio_habito").val();
        let antecedente = $("#antecendete_filtro").val();
        let fecha_inicio = $("#fecha_inicio").val();
        let fecha_fin = $("#fecha_fin").val();
        filtrar_pacientes(habito, antecedente, cod_diagnostico, fecha_inicio, fecha_fin);
    });

    $("#limpiar_filtros_ate").click(() => {
        limpiar_filtros_ate();
        $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/salud/exportar_solicitudes/${0}/${0}/${0}/${0}/${0}/${0}`);

    });

    $(".personas").click(() => {
        $("#content_fnacimiento").hide('slow');
        $("#content_genero").hide('slow');
    });

    $("#admin_atenciones").click(() => {
        $("#modal_administracion").modal();
        listar_valor_parametro(147,'tabla_servicios');  
    });

    $(".agregar_servicio").click(() => {
        $("#modal_nuevo_valor").modal();
    });

    $("#form_guardar_valor_parametro").submit(() => {
        guardar_valor_parametro();
        return false;
    });

    $("#form_modificar_valor_parametro").submit(() => {
        modificar_valor_parametro();
        return false;
    });

    $("#form_nueva_observacion").submit(() => {
        NewObservacion();
        return false;
    });

    //
    $("#GuardarRepProCovid").submit(() => {
        Guardar_Repor_Covid();
        return false;
    });

    $("#form_cambiar_estado_covid").submit(() => {
        EditarEstadoCovid();
        return false;
    });

    
    $("#form_editar_treporte").submit(() => {
        EditarTpReporte();
        return false;
    });

    //

    $("#form_asignar_servicio").submit(() => {
        guardar_atencion();
        return false;
    });

    $("#modal_modificar_atencion").submit(() => {
        modificar_atencion();
        return false;
    });

    $("#asignar_profesional_servicio").click(() => {
        $("#modal_buscar_persona").modal();
        $("#form_buscar_persona").get(0).reset();
        callbak_activo = (resp) => asignar_profesional(resp);
        buscar_personas({ dato: '', tipo: 'Per_emp' }, callbak_activo, `${ruta_salud()}buscar_persona`, "#tabla_personas_busqueda");
    });

    //  $("#form_buscar_persona_ate input[name=tipo_persona]").change(async function () {
    //     let tipo_persona =  $(this).val();
    //     id_tipo_persona = tipo_persona;
    //     if(tipo_persona == 1){
    //         Cargar_parametro_buscado('', ".cbxformacion", "---");
    //     }else{
    //         Cargar_parametro_buscado(15, ".cbxformacion", "Seleccione Tipo Formación");
    //     }
    // });

    // $("#form_asignar_servicio select[name=id_servicio]").change(async function () {
    //     let id_servicio = $(this).val();
    //     if (id_servicio){
    //         profesionales = await obtener_profesional_servicio(id_servicio);
    //         pintar_datos_combo(profesionales, '.cbxprofesional', 'Seleccione Profesional');
    //     }
    // });

    $("#form_buscar_persona_ate").submit(() => {
        let dato = $("#txt_per_buscar_ate").val();
        let tipo = $("#tipopersona").val();
        id_tipo_persona = tipo;
        if (dato.length == 0 || tipo == '') {
            MensajeConClase("Ingrese dato y tipo de población a buscar.", "info", "Oops.!");
        } else {
            let data_activa = { dato, tipo };
            buscar_personas(data_activa, callbak_activo, `${ruta_salud()}buscar_persona`, "#tabla_personas_busqueda_ate", "persona");
        }
        return false;
    });

    $("#form_buscar_persona").submit(() => {
        let dato = $("#txt_per_buscar").val();
        let tipo = 'Per_emp';
        id_tipo_persona = tipo;
        if (dato.length == 0 ) {
              MensajeConClase("Ingrese dato de la persona a buscar.", "info", "Oops.!");
        }else{
            let data_activa = { dato, tipo };
            buscar_personas(data_activa, callbak_activo, `${ruta_salud()}buscar_persona`, "#tabla_personas_busqueda");
        }
        return false;
    });

    $("#btnfiltrar_sol").click(() => {
        let tipo_solicitud = $("#tipo_solicitud_filtro").val();
        let servicio = $("#servicio_filtro").val();
        let estado = $("#estado_filtro").val();
        let tipo_persona = $("#tipopersona_filtro").val();
        let fecha = $("#fecha_filtro").val();
        let fecha2 = $("#fecha_filtro_2").val();
        tipo_solicitud = tipo_solicitud ? tipo_solicitud : 0;
        servicio = servicio ? servicio : 0;
        estado = estado ? estado : 0;
        tipo_persona = tipo_persona ? tipo_persona : 0;
        fecha = fecha ? fecha : 0;
        fecha2 = fecha2 ? fecha2 : 0;
        listar_atenciones();
        $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/salud/exportar_solicitudes/${tipo_solicitud}/${servicio}/${estado}/${tipo_persona}/${fecha}/${fecha2}`);
    });

    $(".btn_ultima_sol").click(() => {
        let url = `${ruta_salud()}editar_ultima_atencion`;
        consulta_ajax(url, { id_solicitud }, (resp) => {
            let { mensaje, tipo, titulo } = resp;
            if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
        });
        $(`#${modal_menu}`).modal();
    });

    $(".btn_paciente").click(() => {
        let url = `${ruta_salud()}buscar_paciente`;
        let tipo_solicitante = id_tipo_persona;
        let id_persona = id_solicitante;
        consulta_ajax(url, { id_persona, tipo_solicitante }, (resp) => {
            let { nombre_completo, genero_y, fecha_nacimiento, lugar_nacimiento, direccion, profesion, id_tipo_estadocivil, servicio_militar, arl, eps, fecha_ingreso } = resp;
            $(".paciente").html(nombre_completo);
            $("#fecha_nacimiento_hmo").val(fecha_nacimiento);
            $("#lugar_nacimiento").val(lugar_nacimiento);
            $("#direccion").val(direccion);
            $("#profesion").val(profesion);
            $(".cbxestadocivil").val(id_tipo_estadocivil);
            $(".cbxsmilitar").val(servicio_militar);
            $("#eps").val(eps);
            $("#arl").val(arl);
            if (fecha_ingreso != '0000-00-00') $("#fecha_ingreso").val(fecha_ingreso);
            else $("#fecha_ingreso").val('');
            if (genero_y == 1) $("#genero_f_hmo").prop("checked", true);
            else if (genero_y == 2) $("#genero_m_hmo").prop("checked", true);
        });
        $("#modal_paciente").modal();
    });

    $("#guardar_paciente").click(() => {
        guardar_datos_paciente('modificar_paciente', 'form_paciente', '', '', 'modal_paciente');
    });

    $("#btn_escolaridad").click( async function () { 
        valor = await obtener_valor_parametro(151);
        pintar_datos_combo(valor, '.cbxparametro1', 'Seleccione Escolaridad');
        listar_tablas_antecendetes(id_solicitante, 'tabla_escolaridad', 'salud_escolaridad_paciente', 'listar_escolaridad', 'escolaridad');
        $("#modal_escolaridad").modal();
    });

    $("#agregar_escolaridad").click(() => {
        $("#modal_valor_parametro").modal();
        $("#form_valor_parametro").get(0).reset();
        callbak_activo = (resp) => guardar_datos_paciente('add_escolaridad', 'form_valor_parametro');
    });

    $("#guardar_escolaridad").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_antecendetes(id_solicitante, 'tabla_escolaridad', 'salud_escolaridad_paciente', 'listar_escolaridad', 'escolaridad');
    });

    $("#btn_hlaboral").click(() => {
        listar_historia_laboral(id_solicitante, 'listar_historia_laboral');
        $("#modal_historial_laboral").modal();
    });

    $("#agregar_historia").click(() => {
        $("#form_add_historia_laboral").get(0).reset();
        $("#container_riesgos").addClass("oculto");
        $("#container_add_riesgos").removeClass("oculto");
        $("#modal_add_historia_laboral").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_historia_laboral', 'form_add_historia_laboral');
    });

    $(".mas_riesgos").click(() => {
        listar_riesgos_laborales(153,1);
    });

    $("#retirar_riesgo").click(function () {
        confirmar_accion_general(`Si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => retirar_riesgo_sele(".riesgos_agregados"));
    });

    $("#guardar_historia_laboral").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_historia_laboral(id_solicitante, 'listar_historia_laboral');
    });

    $("#historia_laboral").click(() => {
        $('#container_accidentes').addClass("oculto");
        $('#container_historia_laboral').removeClass("oculto");
        $('#historia_laboral').addClass("active");
        $('#accidentes').removeClass("active");
    });

    $("#accidentes").click(() => {
        $('#container_historia_laboral').addClass("oculto");
        $('#container_accidentes').removeClass("oculto");
        $('#accidentes').addClass("active");
        $('#historia_laboral').removeClass("active");
        listar_accidentes(id_solicitante, 'listar_accidentes');
    });

    $("#agregar_accidentes").click(async function () {
        $("#modal_add_accidentes").modal();
        $("#form_add_accidentes").get(0).reset();
        valor = await cargar_empresas(id_solicitante);
        pintar_datos_combo(valor, '.cbxempresas', 'Seleccione Empresa');
        callbak_activo = (resp) => guardar_datos_paciente('add_accidente', 'form_add_accidentes');
    });

    $("#guardar_accidente").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_accidentes(id_solicitante, 'listar_accidentes');
    });

    $(".btn_antecedentes").click(() => {
        $("#modal_antecedentes").modal();
        listar_tablas_antecendetes(id_solicitante, 'tabla_ant_familiar', 'salud_antecedentes_familiares', 'listar_antfamiliar', 'antfamiliar');
    });

    $("#ant_familiar").click(() => {
        $('#container_ant_personal').addClass("oculto");
        $('#container_vacuna').addClass("oculto");
        $('#container_ant_gineco').addClass("oculto");
        $('#container_habitos').addClass("oculto");
        $('#container_ant_familiar').removeClass("oculto");
        $('#ant_personal').removeClass("active");
        $("#vacuna").removeClass("active");
        $("#ant_gineco").removeClass("active");
        $("#ant_habitos").removeClass("active");
        $('#ant_familiar').addClass("active");
        listar_tablas_antecendetes(id_solicitante, 'tabla_ant_familiar', 'salud_antecedentes_familiares', 'listar_antfamiliar', 'antfamiliar');
    });

    $("#agregar_antecedente_f").click(() => {
        $("#form_add_antfamiliar").get(0).reset();
        $("#modal_add_antfamiliar").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_antfamiliar', 'form_add_antfamiliar', id_solicitud);
    });

    $("#guardar_antfamiliar").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_antecendetes(id_solicitante, 'tabla_ant_familiar', 'salud_antecedentes_familiares', 'listar_antfamiliar', 'antfamiliar');
    });

    $("#ant_personal").click(() => {
        $('#container_ant_familiar').addClass("oculto");
        $('#container_vacuna').addClass("oculto");
        $('#container_ant_gineco').addClass("oculto");
        $('#container_habitos').addClass("oculto");
        $('#container_ant_personal').removeClass("oculto");
        $('#ant_familiar').removeClass("active");
        $("#vacuna").removeClass("active");
        $("#ant_gineco").removeClass("active");
        $("#ant_habitos").removeClass("active");
        $('#ant_personal').addClass("active");
        listar_tablas_antecendetes(id_solicitante, 'tabla_ant_personal', 'salud_antecedentes_personales', 'listar_antpersonal', 'antpersonal');
    });

    $("#agregar_antecedente_p").click(() => {
        $("#form_add_antpersonal").get(0).reset();
        $("#modal_add_antpersonal").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_antpersonal', 'form_add_antpersonal');
    });

    $("#guardar_antpersonal").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_antecendetes(id_solicitante, 'tabla_ant_personal', 'salud_antecedentes_personales', 'listar_antpersonal', 'antpersonal');
    });

    $("#vacuna").click(() => {
        $('#container_ant_familiar').addClass("oculto");
        $('#container_ant_personal').addClass("oculto");
        $('#container_ant_gineco').addClass("oculto");
        $('#container_habitos').addClass("oculto");
        $('#container_vacuna').removeClass("oculto");
        $('#ant_familiar').removeClass("active");
        $('#ant_personal').removeClass("active");
        $("#ant_gineco").removeClass("active");
        $("#ant_habitos").removeClass("active");
        $("#vacuna").addClass("active");
        listar_tablas_antecendetes(id_solicitante, 'tabla_vacunas', 'salud_vacuna_paciente', 'listar_vacunas', 'vacunas');
    });

    $("#agregar_vacuna").click(() => {
        $("#form_add_vacuna").get(0).reset();
        $("#modal_add_vacuna").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_vacuna', 'form_add_vacuna');
    });

    $("#guardar_vacuna").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_antecendetes(id_solicitante, 'tabla_vacunas', 'salud_vacuna_paciente', 'listar_vacunas', 'vacunas');
    });

    $("#ant_gineco").click(() => {
        $('#container_ant_familiar').addClass("oculto");
        $('#container_ant_personal').addClass("oculto");
        $('#container_vacuna').addClass("oculto");
        $('#container_habitos').addClass("oculto");
        $('#container_ant_gineco').removeClass("oculto");
        $('#ant_familiar').removeClass("active");
        $('#ant_personal').removeClass("active");
        $("#vacuna").removeClass("active");
        $("#ant_habitos").removeClass("active");
        $("#ant_gineco").addClass("active");
        listar_ant_gineco(id_solicitante, 'listar_ant_gineco');
    });

    $("#agregar_ant_gineco").click(() => {
        $("#form_ant_gineco").get(0).reset();
        $("#modal_ant_gineco").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_ant_gineco', 'form_ant_gineco');
    });

    $("#guardar_ant_gineco").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_ant_gineco(id_solicitante, 'listar_ant_gineco');
    });

    $("#ant_habitos").click(() => {
        $('#container_ant_familiar').addClass("oculto");
        $('#container_ant_personal').addClass("oculto");
        $('#container_vacuna').addClass("oculto");
        $('#container_ant_gineco').addClass("oculto");
        $('#container_habitos').removeClass("oculto");
        $('#ant_familiar').removeClass("active");
        $('#ant_personal').removeClass("active");
        $("#vacuna").removeClass("active");
        $("#ant_gineco").removeClass("active");
        $("#ant_habitos").addClass("active");
        listar_tablas_antecendetes(id_solicitante, 'tabla_habitos', 'salud_habitos_paciente', 'listar_habitos', 'habito');
    });

    $("#agregar_habito").click(() => {
        $("#form_add_habito").get(0).reset();
        $("#modal_add_habito").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_habito', 'form_add_habito');
    });

    $("#guardar_habito").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_antecendetes(id_solicitante, 'tabla_habitos', 'salud_habitos_paciente', 'listar_habitos', 'habito');
    });

    $(".btn_revsistemas_h").click(() => {
        $("#modal_revision_sistemas").modal();
        listar_tablas_historia(id_solicitud, 'tabla_revision_sistemas', 'salud_revision_sistema', 'listar_revision_sistema', 'revsistemas');
    });

    $(".btn_revsistemas").click(() => {
        $("#container_detalle_diagnostico").addClass("oculto");
        $("#container_detalle_examenes").addClass("oculto");
        $("#tabla_signos_vitales").addClass("oculto");
        $("#container_detalle_revsistema").removeClass("oculto");
        $(".nombre_modal").html("Revisión por Sistemas");
        detalle_historia_clinica(id_solicitud, 'tabla_detalle_revsistema', 'salud_revision_sistema', 'listar_revision_sistema');
        $("#modal_detalle_historia_clin").modal();
    });

    $("#agregar_rev_sistema").click(() => {
        $("#form_add_revsistemas").get(0).reset();
        $("#modal_add_revsistemas").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_revision_sistemas', 'form_add_revsistemas', id_solicitud);
    });

    $("#guardar_revsistemas").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_historia(id_solicitud, 'tabla_revision_sistemas', 'salud_revision_sistema', 'listar_revision_sistema', 'revsistemas');
    });

    $(".btn_examen_fisico_h").click(() => {
        $("#modal_examen_fisico").modal();
        ver_examen_fisico(id_solicitud);
        listar_tablas_historia(id_solicitud, 'tabla_examen_fisico', 'salud_examen_fisico', 'listar_examen_fisico', 'examen_fisico');
    });

    $(".btn_examen_fisico").click(() => {
        $("#container_detalle_diagnostico").addClass("oculto");
        $("#container_detalle_revsistema").addClass("oculto");
        $("#tabla_signos_vitales").removeClass("oculto");
        $("#container_detalle_examenes").removeClass("oculto");
        $(".nombre_modal").html("Examen Físico");
        ver_examen_fisico(id_solicitud);
        detalle_historia_clinica(id_solicitud, 'tabla_detalle_examenes', 'salud_examen_fisico', 'listar_examen_fisico');
        $("#modal_detalle_historia_clin").modal();
    });

    $("#agregar_examen_fisico").click(() => {
        $("#form_add_examen_fisico").get(0).reset();
        $("#modal_add_examenf").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_examen_fisico', 'form_add_examen_fisico', id_solicitud);
    });

    $("#guardar_examen_fisico").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_historia(id_solicitud, 'tabla_examen_fisico', 'salud_examen_fisico', 'listar_examen_fisico', 'examen_fisico');
    });

    $("#agregar_signos_vitales").click(() => {
        $("#form_add_signos_vitales").get(0).reset();
        $(".indice_masa").html("");
        $("rango_imc").html("");
        $("#editar_signos_vitales").addClass("oculto");
        $("#guardar_signos_vitales").removeClass("oculto");
        $("#modal_add_signos_vitales").modal();
    });

    $("#modificar_signos_vitales").click(async function () {
        let tabla_salud = 'salud_signos_vitales';
        let filtro = 'id_solicitud';
        let id_buscar = id_solicitud;
        let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
        let { peso,
            talla,
            ta_sistolica,
            ta_diastolica,
            frecuencia_cardiaca,
            frecuencia_respiratoria,
            observacion,
            mano_dominante,
            temperatura } = resp;
        let imc = (peso / (talla * talla)).toFixed(2);
        let data = await calcular_rango(imc);
        let { rango } = data;
        $("#peso").val(peso);
        $("#talla").val(talla);
        $("#fc").val(frecuencia_cardiaca);
        $("#fr").val(frecuencia_respiratoria);
        $("#ta_sistolica").val(ta_sistolica);
        $("#ta_diastolica").val(ta_diastolica);
        $(".indice_masa").html(imc);
        $(".rango_imc").html(rango);
        $("#temp").val(temperatura);
        $("#id_mano").val(mano_dominante);
        $("#detalle_examen").val(observacion);
        $("#guardar_signos_vitales").addClass("oculto");
        $("#editar_signos_vitales").removeClass("oculto");
        $("#modal_add_signos_vitales").modal();
    });

    $("#talla").keyup(async function (e) {
        const talla = $(this).val();
        let peso = $("#peso").val();
        let imc = '';
        if (talla != '' && peso != '') imc = (peso / (talla * talla)).toFixed(2);
        else imc = 0;
        let data = await calcular_rango(imc);
        let { rango } = data;
        $(".indice_masa").html(imc);
        $(".rango_imc").html(rango);
    });

    $("#peso").keyup(async function (e) {
        const peso = $(this).val();
        let talla = $("#talla").val();
        let imc = '';
        if (talla != '' && peso != '') imc = (peso / (talla * talla)).toFixed(2);
        else imc = 0;
        let data = await calcular_rango(imc);
        let { rango } = data;
        $(".indice_masa").html(imc);
        $(".rango_imc").html(rango);
    });

    $("#guardar_signos_vitales").click(() => {
        guardar_datos_paciente('add_signos_vitales', 'form_add_signos_vitales', id_solicitud);
        $(".indice_masa").html("");
        $(".rango_imc").html("");
        callbak_listar = (resp) => ver_examen_fisico(id_solicitud);
    });

    $("#editar_signos_vitales").click(() => {
        guardar_datos_paciente('editar_signos_vitales', 'form_add_signos_vitales', id_solicitud);
        $(".indice_masa").html("");
        $(".rango_imc").html("");
        callbak_listar = (resp) => ver_examen_fisico(id_solicitud);
    });


    $("#btn_resultados").click(() => {
        $("#modal_resualtado_examenes").modal();
        listar_tablas_historia(id_solicitud, 'tabla_resultado_examenes', 'salud_examenes_paraclinicos', 'listar_resultado_examenes', 'resultado_examenes');
    });

    $(".btn_resultados").click(() => {
        $("#container_detalle_diagnostico").addClass("oculto");
        $("#container_detalle_revsistema").addClass("oculto");
        $("#tabla_signos_vitales").addClass("oculto");
        $("#container_detalle_examenes").removeClass("oculto");
        $(".nombre_modal").html("Exámenes Paraclínicos");
        detalle_historia_clinica(id_solicitud, 'tabla_detalle_examenes', 'salud_examenes_paraclinicos', 'listar_resultado_examenes');
        $("#modal_detalle_historia_clin").modal();
    });

    $("#agregar_resultado_examen").click(() => {
        $("#form_add_examenpar").get(0).reset();
        $("#modal_add_examenpar").modal();
        callbak_activo = (resp) => guardar_datos_paciente('add_examenpar', 'form_add_examenpar', id_solicitud);
    });

    $("#guardar_examenpar").click(() => {
        callbak_activo();
        callbak_listar = (resp) => listar_tablas_historia(id_solicitud, 'tabla_resultado_examenes', 'salud_examenes_paraclinicos', 'listar_resultado_examenes', 'resultado_examenes');
    });

    $(".btn_diagnostico_h").click(() => {
        sel_diagnostico = 1;
        $("#modal_diagnostico").modal();
        listar_tablas_historia(id_solicitud, 'tabla_diagnostico', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
    });

    $(".agregar_diagnostico").click(() => {
        $("#txt_diag_buscar").val('');
        buscar_diagnostico();
        $("#modal_add_diagnostico").modal();
    });

    $(".retirar_diagnostico").click(() => {
        cod_diagnostico = '';
        $("#diagnostico").val('');
    });

    $(".btn_diagnostico").click(() => {
        $("#container_detalle_revsistema").addClass("oculto");
        $("#container_detalle_examenes").addClass("oculto");
        $("#tabla_signos_vitales").addClass("oculto");
        $("#container_detalle_diagnostico").removeClass("oculto");
        $(".nombre_modal").html("Diagnósticos");
        detalle_historia_clinica(id_solicitud, 'tabla_detalle_diagnostico', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
        $("#modal_detalle_historia_clin").modal();
    });

    $("#form_add_diagnostico").submit(() => {
        let solicitud = '';
        if (sel_diagnostico == 1) solicitud = id_solicitud;
        let data = $("#txt_diag_buscar").val();
        if (data.length == 0) MensajeConClase("Ingrese Código o descripción del Diagnóstico a buscar", "info", "Oops.!");
        else buscar_diagnostico(data, sel_diagnostico, solicitud);
        return false;
    });

    $("#btn_dato_familiar").click(() => {
        $("#modal_dato_familiar").modal();
        ver_dato_familiar(id_solicitud);
    });

    $("#guardar_dato_familiar").click(() => {
        guardar_datos_paciente('add_dato_familiar', 'form_dato_familiar', id_solicitud, '', 'modal_dato_familiar');
        callbak_listar = (resp) => ver_dato_familiar(id_solicitud);
    });

    $("#btn_anamnesis").click(() => {
        $("#modal_anamnesis").modal();
        ver_anamnesis(id_solicitud);
    });

    $("#guardar_anamnesis").click(() => {
        guardar_datos_paciente('add_anamnesis', 'form_anamnesis', id_solicitud, '', 'modal_anamnesis');
        callbak_listar = (resp) => ver_anamnesis(id_solicitud);
    });

    $("#btn_plan_hm").click(() => {
        $("#modal_plan_terapeutico").modal();
        ver_plan(id_solicitud);
    });

    $("#guardar_plan").click(() => {
        guardar_datos_paciente('add_plan_terapeutico', 'form_plan_terapeutico', id_solicitud, '', 'modal_plan_terapeutico');
        callbak_listar = (resp) => ver_plan(id_solicitud);
    });

    $("#btn_valoracion").click(() => {
        $("#modal_valoracion").modal();
        ver_valoracion(id_solicitud);
    });

    $("#guardar_valoracion").click(() => {
        guardar_datos_paciente('add_valoracion', 'form_valoracion', id_solicitud, '', 'modal_valoracion');
        callbak_listar = (resp) => ver_valoracion(id_solicitud);
    });

    $("#btn_historial_ho").click(() => {
        $("#modal_historial_ocupacional").modal();
        listar_historial_ocupacional(id_solicitante);
    });

    $("#btn_historial_hm").click(() => {
        $("#modal_historial_mgeneral").modal();
        listar_historial_mgeneral(id_solicitante);
    });

    $(".rev_sistemas").click(() => {
        detalle_historia_clinica(id_historia_clinica, 'tabla_rev_sistemas', 'salud_revision_sistema', 'listar_revision_sistema');
        $("#container_exa_paraclinico").addClass("oculto");
        $("#container_diagnostico_pac").addClass("oculto");
        $("#container_exa_fisico").addClass("oculto");
        $("#container_rev_sistemas").removeClass("oculto");
        $('.exa_fisico').removeClass("active");
        $('.exa_paraclinico').removeClass("active");
        $('.diagnostico_pac').removeClass("active");
        $('.rev_sistemas').addClass("active");
    });

    $(".exa_fisico").click(() => {
        detalle_historia_clinica(id_historia_clinica, 'tabla_exa_fisico', 'salud_examen_fisico', 'listar_examen_fisico');
        $("#container_rev_sistemas").addClass("oculto");
        $("#container_exa_paraclinico").addClass("oculto");
        $("#container_diagnostico_pac").addClass("oculto");
        $("#container_exa_fisico").removeClass("oculto");
        $('.rev_sistemas').removeClass("active");
        $('.exa_paraclinico').removeClass("active");
        $('.diagnostico_pac').removeClass("active");
        $('.exa_fisico').addClass("active");
        ver_examen_fisico(id_historia_clinica);
    });

    $(".exa_paraclinico").click(() => {
        detalle_historia_clinica(id_historia_clinica, 'tabla_exa_paraclinico', 'salud_examenes_paraclinicos', 'listar_resultado_examenes');
        $("#container_rev_sistemas").addClass("oculto");
        $("#container_exa_fisico").addClass("oculto");
        $("#container_diagnostico_pac").addClass("oculto");
        $("#container_exa_paraclinico").removeClass("oculto");
        $('.rev_sistemas').removeClass("active");
        $('.exa_fisico').removeClass("active");
        $('.diagnostico_pac').removeClass("active");
        $('.exa_paraclinico').addClass("active");
    });

    $(".diagnostico_pac").click(() => {
        detalle_historia_clinica(id_historia_clinica, 'tabla_diagnostico_pac', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
        $("#container_rev_sistemas").addClass("oculto");
        $("#container_exa_fisico").addClass("oculto");
        $("#container_exa_paraclinico").addClass("oculto");
        $("#container_diagnostico_pac").removeClass("oculto");
        $('.rev_sistemas').removeClass("active");
        $('.exa_fisico').removeClass("active");
        $('.exa_paraclinico').removeClass("active");
        $('.diagnostico_pac').addClass("active");
    });

    $(".ver_tabla_imc").click(() => {
        consulta_ajax(`${ruta_salud()}listar_valor_parametro`, { idparametro:168 }, (resp) => {
            const myTable = $("#tabla_imc").DataTable({
                destroy: true,
                searching: true,
                processing: true,
                data: resp,
                columns: [
                    {
                        "render": function (data, type, full, meta) {
                            let { valorx, valory } = full;
                            if (valory != null) return `${valorx} - ${valory}`;
                            else return `${valorx} +`;
                        }
                    },
                    {
                        data: "valor"
                    },
                ],
                language: idioma,
                dom: "Bfrtip",
                buttons: [],

            });

            //EVENTOS DE LA TABLA ACTIVADOS
            $(`#tabla_imc tbody`).on("click", "tr", function () {
                $(`#tabla_imc tbody tr`).removeClass("warning");
                $(this).attr("class", "warning");
            });
        });
        $('#modal_tabla_imc').modal();
    });

});

const calcular_rango = async (imc) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}calcular_rango_imc`;
        consulta_ajax(url, { imc }, resp => {
            resolve(resp);
        });
    });
}

const obtener_valor_parametro = async (idparametro) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}obtener_valor_parametro`;
        consulta_ajax(url, { idparametro }, resp => {
            resolve(resp);
        });
    });
}

const cargar_empresas = async (id_persona) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}cargar_empresas`;
        consulta_ajax(url, { id_persona }, resp => {
            resolve(resp);
        });
    });
}

const obtener_profesional_servicio = async (id_idparametro) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}listar_profesional_servicio`;
        consulta_ajax(url, { id_idparametro }, resp => {
            resolve(resp);
        });
    });
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '') => {
    $(combo).html(`<option value=''> ${mensaje}</option>`);
    datos.forEach(elemento => {
        $(combo).append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
    });
    $(combo).val(sele);
}

const administrar_modulo = tipo => {
    if (tipo == 'listado_atenciones') {
        listar_atenciones();
        $("#menu_principal").css("display", "none");
        $("#listar_atenciones").fadeIn();
    } else if (tipo == 'regresar_menu') {
        $("#listar_atenciones").css("display", "none");
        $("#menu_principal").fadeIn(1000);
    } else if (tipo == 'solicitud_atencion') {
        $("#modal_buscar_persona_ate").modal();
        $("#form_buscar_persona_ate").get(0).reset();
        $("#txt_per_buscar_ate").focus();
        let data_activa = { dato: "", tipop: "1" };
        buscar_personas(data_activa, callbak_activo, `${ruta_salud()}buscar_persona`, "#tabla_personas_busqueda_ate");
    } else if (tipo == 'historia_medica') {
        $("#modal_buscar_persona").modal();
        $("#form_buscar_persona").get(0).reset();
        buscar_personas({ dato: "", tipop: "1" }, callbak_activo, `${ruta_salud()}buscar_persona`, "#tabla_personas_busqueda");
    }
}

const listar_riesgos_laborales = (idparametro, accion, id_historia_laboral = '') => {
    $('#tabla_riesgos_laborales tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr .seleccionar');
    consulta_ajax(`${ruta_salud()}listar_valor_parametro`, { idparametro }, (resp) => {
        let i = 0;
        const table = $("#tabla_riesgos_laborales").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                render: function (data, type, full, meta) {
                    i++;
                    return i;
                }
            },
            {
                data: "valor"
            },
            {
                defaultContent: `<span style="color: #39B23B;" title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`
            }
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: []
        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $('#tabla_riesgos_laborales tbody').on('dblclick', 'tr', function () {
            let data = table.row(this).data();
            sele_riesgo_add(data, this);

        });
        $('#tabla_riesgos_laborales tbody').on('click', 'tr .seleccionar', function () {
            let data = table.row($(this).parent().parent()).data();
            let id_riesgo = data.id;
            if (accion == 1) sele_riesgo_add(data, $(this).parent().parent());
            else confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => guardar_riesgo(id_riesgo, id_historia_laboral));
        });
    });

    riesgos_sele = [];
    const sele_riesgo_add = (data, thiss) => {
        if (!$(thiss).hasClass("warning")) {
            $(thiss).attr("class", "warning");
            riesgos_sele.push(data);
        } else {
            $(thiss).removeClass("warning");
            var i = riesgos_sele.indexOf(data);
            riesgos_sele.splice(i, 1);
        }
        swal.close();
        $(".rec_sele").html(riesgos_sele.length);
        pintar_datos_combo(riesgos_sele, '.riesgos_agregados', `${riesgos_sele.length} Riesgos(s) a Asignar`);
    }

    $("#Modal_seleccionar_riesgo").modal("show");
}

const retirar_riesgo_sele = (combo) => {
    let id_parametro = $(combo).val();
    if (id_parametro.length == 0) {
        MensajeConClase("Seleccione Riesgo laboral a Retirar..!", "info", "Oops...")
    } else {
        for (var i = 0; i < riesgos_sele.length; i++) {
            if (riesgos_sele[i].id == id_parametro) {
                riesgos_sele.splice(i, 1);
                swal.close();
                pintar_datos_combo(riesgos_sele, combo, `${riesgos_sele.length} Riesgos(s) a Asignar`);
            }
        }
        MensajeConClase("No fue posible retirar el Riesgo laboral.!!", "info", "Oops..!");
    }
}

const tipo_habito = () => {
    let habito = $("#habito").val();
    if (habito == 'Hab_Dep') {
        $("#tipo_ejercicio").removeClass("oculto");
        $("#id_duracion").removeClass("oculto");
    } else {
        $("#tipo_ejercicio").addClass("oculto");
        $("#id_duracion").addClass("oculto");
    }

    if (habito == 'Hab_fum' || habito == 'Hab_Fba') $("#cantidad").removeClass("oculto");
    else $("#cantidad").addClass("oculto");
}

const limpiar_filtros_ate = () => {
    $("#tipo_solicitud_filtro").val('');
    $("#estado_filtro").val('');
    $("#servicio_filtro").val('');
    $("#tipopersona_filtro").val('');
    $("#fecha_filtro").val('');
    $("#fecha_filtro_2").val('');
    listar_atenciones();
}

const guardar_atencion = () => {
    let fordata = new FormData(document.getElementById("form_asignar_servicio"));
    let data = formDataToJson(fordata);
    data.id_solicitante = id_solicitante;
    data.tipo_persona = $("#tipopersona").val();
    consulta_ajax(`${ruta_salud()}guardar_atencion`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            limpiar_filtros_ate();
            listar_atenciones();
            $("#modal_asignar_servicio").modal('hide');
            $("#form_asignar_servicio").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const listar_atenciones = () => {
    $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/salud/exportar_solicitudes/${0}/${0}/${0}/${0}/${0}/${0}`);
    let tipo_solicitud = $("#tipo_solicitud_filtro").val();
    let estado = $("#estado_filtro").val();
    let servicio = $("#servicio_filtro").val();
    let tipo_persona = $("#tipopersona_filtro").val();
    let fecha = $("#fecha_filtro").val();
    let fecha_2 = $("#fecha_filtro_2").val();
    $("#tabla_atenciones tbody").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)').off("click", "tr .cancelar").off("click", "tr .modificar").off("click", "tr .seguimiento");
    consulta_ajax(`${ruta_salud()}listar_atenciones`, { tipo_solicitud, estado, servicio, tipo_persona, fecha, fecha_2 }, (resp) => {
        let i = 0;
        const myTable = $("#tabla_atenciones").DataTable({
            destroy: true,
            processing: true,
            data: resp,
            columns: [{
                data: "ver"
            },
            {
                data: "nombre_completo"
            },
            {
                data: "tipo_solicitud"
            },
            {
                data: "profesional"
            },
            {
                data: "fecha_solicitud"
            },
            {
                data: "estado"
            },
            {
                data: "accion"
            },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones(),

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_atenciones tbody").on("click", "tr", function () {
            $("#tabla_atenciones tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_atenciones tbody').on('dblclick', 'tr', function () {
            let data = myTable.row(this).data();
            id_solicitud = data.id;
            if (data.id_tipo_solicitud == 'Sal_His_Ocup') {
                $("#container_nav_salud_detalle").removeClass("oculto");
                $("#detalle_hm").addClass("oculto");
                $(".btn_resultados").show('slow');
            } else if (data.id_tipo_solicitud == 'Sal_His_Med_Gen') {
                $("#container_nav_salud_detalle").removeClass("oculto");
                $("#detalle_hm").removeClass("oculto");
                $(".btn_resultados").hide('slow');
            } else if (data.id_tipo_solicitud == 'Sal_Sol_Ate') {
                $("#container_nav_salud_detalle").addClass("oculto");
            } else if (data.id_tipo_solicitud == 'Sal_Bit_Enf') {
                $("#container_nav_salud_detalle").addClass("oculto");
            }else if(data.id_tipo_solicitud == 'Sal_Sol_Cov'){ //Neyla
                $("#container_nav_salud_detalle").addClass("oculto");
            }
            ver_detalle_atencion(data);
        });

        $('#tabla_atenciones tbody').on('click', 'tr td:nth-of-type(1)', function () {
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.id;
            if (data.id_tipo_solicitud == 'Sal_His_Ocup') {
                $("#container_nav_salud_detalle").removeClass("oculto");
                $("#detalle_hm").addClass("oculto");
                $(".btn_resultados").show('slow');
            } else if (data.id_tipo_solicitud == 'Sal_His_Med_Gen') {
                $("#container_nav_salud_detalle").removeClass("oculto");
                $("#detalle_hm").removeClass("oculto");
                $(".btn_resultados").hide('slow');
            } else if (data.id_tipo_solicitud == 'Sal_Sol_Ate') {
                $("#container_nav_salud_detalle").addClass("oculto");
            } else if (data.id_tipo_solicitud == 'Sal_Bit_Enf') {
                $("#container_nav_salud_detalle").addClass("oculto");
            }
            ver_detalle_atencion(data);
        });

        $("#tabla_atenciones tbody").on("click", "tr .modificar", function () { 
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.id;
            mostrar_atencion_modificar(data);
        });

        $("#tabla_atenciones tbody").on("click", "tr .seguimiento", function () { //NPM
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.id;
            mostrar_seguimiento_covid(data);
            console.log(data.tipo_reporte)
        });

        $("#tabla_atenciones tbody").on("click", "tr .cancelar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general(`Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => ejecutar_gestion(id, "Sal_Can_E"));
        });

    });
}

const ver_detalle_atencion = async (data) => {
    let {id,
        fecha_registra,
        nombre_completo,
        profesional,
        observacion,
        tipo_solicitante,
        id_persona,
        id_servicio,
        enfermedad_actual,
        motivo_consulta,
        control,
        valoracion_examen,
        id_tipo_solicitud,
        tipo_reporte,
        tipopersona } = data;
    let dato_paciente = await buscar_datos_paciente(id_persona, tipo_solicitante);
    let { identificacion, n_genero, edad, dependencia } = dato_paciente;
    if (id_servicio) {
        let data_servicio = await buscar_parametro_id(id_servicio);
        let { valor } = data_servicio[0];
        $(".servicio").html(valor);
    }
    if (id_tipo_solicitud == 'Sal_Sol_Ate') {
        $("#tr_control").addClass("oculto");
    } else if (id_tipo_solicitud == 'Sal_His_Ocup') {
        observacion = valoracion_examen;
        $("#tr_control").removeClass("oculto");
        $("#tr_observaciones").removeClass("oculto");
    } else if (id_tipo_solicitud == 'Sal_His_Med_Gen') {
        $(".servicio").html('Historia Clínica Medicina General');
        $("#tr_control").removeClass("oculto");
        $("#tr_observaciones").removeClass("oculto");
    }else if (id_tipo_solicitud == 'Sal_Sol_Cov') {
        $(".servicio").html('Protocolo Covid-19');
        $("#tr_control").removeClass("oculto");
    }
    $(".fecha_registra").html(fecha_registra);
    $(".solicitante").html(nombre_completo);
    $(".identificacion").html(identificacion);
    $(".genero").html(n_genero);
    $(".edad").html(edad);
    $(".tipopersona").html(tipopersona);
    $(".programa").html(dependencia);
    $(".profesional").html(profesional);
    $(".observaciones").html(observacion);
    $(".enfermedad_actual").html(enfermedad_actual);
    $(".motivo_consulta").html(motivo_consulta);
    $(".control").html(control);
    $("#soli_id").val(id);
    $("#idsolic").val(id);
    $("#idsoli").val(id)
    $("#idsolic_rep").val(id);
    $("#treportecambio").val(tipo_reporte)
    $("#modal_detalle_solicitud").modal();
}

const buscar_datos_paciente = async (id_persona, tipo_solicitante) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}buscar_paciente`;
        consulta_ajax(url, { id_persona, tipo_solicitante }, resp => {
            resolve(resp);
        });
    });
}

const mostrar_atencion_modificar = (data) => {
    let {
        nombre_completo,
        id_servicio,
    } = data;
    $(".solicitante_mod").html(nombre_completo);
    $("#id_servicio_mod").val(id_servicio);
    $("#modal_modificar_atencion").modal();
}

const mostrar_seguimiento_covid = (data) => {//NPM
    let {id,id_estado_sol, observacion_mod, motivo_consulta} = data;
        document.getElementById ('idsolicitudcov').value=id;
        if(motivo_consulta){
            $("#EstadoCambio").val(322)
            $("#motivocambio").val(motivo_consulta)
            $("#observacionescambio").val(observacion_mod); 
        }else{
            $("#EstadoCambio").val("")
            $("#motivocambio").val("")
            $("#observacionescambio").val(""); 
        }
    $("#ModalEditarE").modal();
}

const modificar_atencion = () => {
    let url = `${ruta_salud()}modificar_atencion`;
    let fordata = new FormData(document.getElementById("form_modificar_atencion"));
    let data = formDataToJson(fordata);
    data.id_solicitud = id_solicitud;
    consulta_ajax(url, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            listar_atenciones();
            $("#form_modificar_atencion").get(0).reset();
            $("#modal_modificar_atencion").modal("hide");
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}


const ejecutar_gestion = (id, estado, title = '¿ Cancelar Solicitud ?') => {
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
            let data = { id, estado, mensaje };
            consulta_ajax(`${ruta_salud()}gestionar_solicitud`, data, (resp) => {
                let { tipo, titulo, mensaje } = resp;
                if (tipo == 'success') {
                    swal.close();
                    listar_atenciones()
                } else MensajeConClase(mensaje, tipo, titulo);
            });
            return false;
        }
    });
}

const asignar_profesional = (data) => {
    confirmar_accion_general(`Esta seguro de guardar a ${data.nombre_completo}, si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => guardar_profesional_servicio(id_idparametro, data.id));
}

const guardar_profesional_servicio = (id_idparametro, id_persona) => {
    let url = `${ruta_salud()}guardar_profesional_servicio`;
    let data = { id_idparametro, id_persona };
    consulta_ajax(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        if (tipo == 'success') {
            let table = $("#tabla_profesional_servicio").DataTable();
            table.clear().draw();
            swal.close();
            listar_profesional_servicio(id_idparametro)
            $("#form_buscar_persona").get(0).reset();
        } else MensajeConClase(mensaje, tipo, titulo);
    });
}

const guardar_valor_parametro = () => {
    let fordata = new FormData(document.getElementById("form_guardar_valor_parametro"));
    fordata.append("idparametro", 147);
    let data = formDataToJson(fordata);
    let url = `${Traer_Server()}index.php/genericas_control/nuevo_valor_Parametro`;
    consulta_ajax(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        MensajeConClase(mensaje, tipo, titulo);
        $("#form_guardar_valor_parametro").get(0).reset();
        $("#modal_nuevo_valor").modal("hide");
        listar_valor_parametro(147);   
    });
}

const listar_valor_parametro = (idparametro, tabla_html) => {
    $(`#${tabla_html} tbody`).off("click", "tr").off("click", "tr .asignar").off("click", "tr .eliminar").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_valor_parametro`, { idparametro }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "valor"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#${tabla_html} tbody`).on("click", "tr", function () {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $(`#${tabla_html} tbody`).on("click", "tr .asignar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            id_idparametro = id;
            $("#modal_profesional_servicio").modal();
            listar_profesional_servicio(id_idparametro);
        });

        $(`#${tabla_html} tbody`).on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Tener en cuenta que al Eliminar este valor no estara disponible en las atenciones, si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_parametro(id, '', idparametro));
        });

        $("#tabla_servicios tbody").on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            id_idparametro = id;
            mostrar_parametro_modificar(id);
        });

    });
}

const listar_profesional_servicio = id_idparametro => {
    $("#tabla_profesional_servicio tbody").off("click", "tr").off("click", "tr .eliminar");
    consulta_ajax(`${ruta_salud()}listar_profesional_servicio`, { id_idparametro }, (resp) => {
        let i = 0;
        const myTable = $("#tabla_profesional_servicio").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [{
                render: function (data, type, full, meta) {
                    i++;
                    return i;
                }
            },
            {
                data: "valor"
            },
            {
                data: "identificacion"
            },
            {
                data: "accion"
            },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_profesional_servicio tbody").on("click", "tr", function () {
            $("#tabla_profesional_servicio tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_profesional_servicio tbody").on("click", "tr .eliminar", function () {
            let { idrelacion } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_profesional_servicio(id_idparametro, idrelacion));
        });

    });
}

const eliminar_profesional_servicio = (id_idparametro, idrelacion) => {
    let url = `${ruta_salud()}eliminar_profesional_servicio`;
    consulta_ajax(url, { idrelacion }, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
        swal.close();
        listar_profesional_servicio(id_idparametro);
    });
}

const mostrar_parametro_modificar = async (buscar) => {
    let data = await buscar_parametro_id(buscar);
    let { valor, valorx } = data[0];
    $("#txtValor_modificar").val(valor);
    $("#txtDescripcion_modificar").val(valorx);
    $("#ModalModificarParametro").modal();
}

const modificar_valor_parametro = (id_parametro) => {
    let url = `${Traer_Server()}index.php/genericas_control/mod_valor_parametro`;
    let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
    data.append("id_idparametro", id_idparametro);
    enviar_formulario(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        MensajeConClase(mensaje, tipo, titulo);
        $("#form_modificar_valor_parametro").get(0).reset();
        $("#ModalModificarParametro").modal("hide");
        listar_valor_parametro(id_parametro);
    });
}

const eliminar_parametro = (id_idparametro, estado, id_parametro) => {
    let url = `${Traer_Server()}index.php/genericas_control/cambiar_estado_parametro`;
    let data = { id_idparametro, estado };
    consulta_ajax(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
        swal.close();
        listar_valor_parametro(id_parametro);
    });
}

const nueva_atencion = (data) => {
    let { nombre_completo, id } = data;
    id_solicitante = id;
    $("#form_asignar_servicio").get(0).reset();
    $(".solicitante").html(nombre_completo);
    $("#modal_buscar_persona_ate").modal('hide');
    $("#modal_nueva_atencion").modal();
}

const historia_ocupacional = (data) => {
    let url = `${ruta_salud()}validar_ultima_atencion`;
    let id_persona = data.id;
    let tipo_solicitud = 'Sal_His_Ocup';
    consulta_ajax(url, { id_persona, tipo_solicitud }, (resp) => {
        let { solicitud, editando } = resp;
        if (editando == 1) {
            $(".btn_ultima_sol").removeClass("oculto");
            id_solicitud = solicitud;
        } else {
            $(".btn_ultima_sol").addClass("oculto");
            id_solicitud = '';
        }
    });
    if (data.genero == 1) $("#ant_gineco").show('slow');
    else if (data.genero == 2) $("#ant_gineco").hide('slow');
    id_solicitante = data.id;
    modal_menu = 'modal_menu_historia_ocupacional';
    $("#modal_buscar_persona").modal('hide');
    $("#modal_historia_ocupacional").modal();
}

const mostrar_formucovid = (data) => {
    id_solicitante = data.id;
    let tipo_solicitud = 'Sal_Sol_Cov';
    $("#modal_buscar_persona").modal('hide');
    $("#Modal_Covid").modal();
}

const historia_medicina_general = (data) => {
    let url = `${ruta_salud()}validar_ultima_atencion`;
    let id_persona = data.id;
    let tipo_solicitud = 'Sal_His_Med_Gen';
    consulta_ajax(url, { id_persona, tipo_solicitud }, (resp) => {
        let { solicitud, editando } = resp;
        if (editando) {
            $(".btn_ultima_sol").removeClass("oculto");
            id_solicitud = solicitud;
        } else {
            $(".btn_ultima_sol").addClass("oculto");
            id_solicitud = '';
        }
    });
    id_solicitante = data.id;
    modal_menu = 'modal_menu_historia_general';
    $("#modal_buscar_persona_ate").modal('hide');
    $("#modal_historia_medicina_general").modal();
}

const bitacora = (data) => {
    id_solicitante = data.id;
    $("#modal_buscar_persona_ate").modal('hide');
    $("#modal_bitacora").modal();
}

const confirmar_solicitud = (tipo_examen, id_tipo_examen, tipo_solicitud) => {
    if (id_solicitud != '') {
        confirmar_accion_general(`Tiene una solicitud habilitada para seguir editando, si esta seguro de realizar nueva solicitud de ${tipo_examen}, por favor presione en el botón de "Si, Aceptar!"`, () => guardar_historia(id_solicitante, id_tipo_examen, tipo_solicitud));
    } else {
        confirmar_accion_general(`Esta a un paso de crear una solicitud de ${tipo_examen}, si esta seguro de escoger esta ruta por favor presione en el botón de "Si, Aceptar!"`, () => guardar_historia(id_solicitante, id_tipo_examen, tipo_solicitud));
    }
}

const guardar_historia = (id_persona, id_tipo_examen, tipo_solicitud) => {
    consulta_ajax(`${ruta_salud()}guardar_historia`, { id_persona, id_tipo_examen, tipo_solicitud, id_tipo_persona }, (resp) => {
        let { titulo, mensaje, tipo, id } = resp;
        if (tipo == 'success') {
            id_solicitud = id;
            $(".btn_ultima_sol").removeClass("oculto");
            $(`#${modal_menu}`).modal();
            swal.close();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}


const guardar_datos_paciente = (control, formulario, id_solicitud = '', id_dato = '', modal = '', modal_p = '') => {
    let data = new FormData(document.getElementById(`${formulario}`));
    data.append('id_persona', id_solicitante);
    data.append('id_solicitud', id_solicitud);
    data.append('id_dato',id_dato);
    data.append('riesgos', riesgos_sele);
    data.append('id_tipo_persona', id_tipo_persona);
    enviar_formulario(`${ruta_salud()}${control}`,data, (resp) => {
        let { titulo, mensaje, tipo, editando } = resp;
        if (tipo == 'success') {
            $(`#${formulario}`).get(0).reset();
            callbak_listar();
            if (id_dato != '') $(`#${modal}`).modal("hide");
        } else if (editando == 0) {
            id_solicitud = '';
            $(`#${modal}`).modal("hide");
            if (modal_p != '') $(`#${modal_p}`).modal("hide");
            $(`#${modal_menu}`).modal("hide");
            $(".btn_ultima_sol").addClass("oculto");
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const eliminar_dato_paciente = (id, tabla_salud) => {
    let url = `${ruta_salud()}eliminar_dato_paciente`;
    consulta_ajax(url, { id, tabla_salud }, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
        swal.close();
        callbak_activo();
    });
}

const ver_examen_fisico = async (id_solicitud) => {
    let tabla_salud = 'salud_signos_vitales';
    let filtro = 'id_solicitud';
    let id_buscar = id_solicitud;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id,
        peso,
        talla,
        ta_sistolica,
        ta_diastolica,
        frecuencia_cardiaca,
        frecuencia_respiratoria,
        observacion,
        mano_dominante, temperatura } = resp;
    let imc = (peso / (talla * talla)).toFixed(2);
    let data = await calcular_rango(imc);
    let { rango } = data;
    if (!id) {
        $("#modificar_signos_vitales").addClass('oculto');
        $("#agregar_signos_vitales").removeClass('oculto');
        $(".peso").html("");
        $(".talla").html("");
        $(".frecuencia_c").html("");
        $(".frecuencia_r").html("");
        $(".tension_a").html("");
        $(".imc").html("");
        $(".rango_imc").html("");
        $(".clasificacion_imc").html("");
        $(".temperatura").html("");
        $(".mano_dominate").html("");
        $(".detalle_examenf").html("");
    } else {
        let mano = '';
        if (mano_dominante == 1) mano = 'Izquierda';
        else if (mano_dominante == 2) mano = 'Derecha';
        else if (mano_dominante == 3) mano = 'Ambidextro';
        $(".peso").html(peso);
        $(".talla").html(talla);
        $(".frecuencia_c").html(frecuencia_cardiaca);
        $(".frecuencia_r").html(frecuencia_respiratoria);
        $(".tension_a").html(`${ta_sistolica}/${ta_diastolica}`);
        $(".imc").html(imc);
        $(".rango_imc").html(rango);
        $(".clasificacion_imc").html(rango);
        $(".temperatura").html(temperatura + '°');
        $(".mano_dominate").html(mano);
        $(".detalle_examenf").html(observacion);
        $("#agregar_signos_vitales").addClass('oculto');
        $("#modificar_signos_vitales").removeClass('oculto');
    }
}

const ver_dato_familiar = async (id_solicitud) => {
    let tabla_salud = 'salud_datos_acompanante';
    let filtro = 'id_solicitud';
    let id_buscar = id_solicitud;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { nombre_acomp, telefono_acomp, nombre_resp, telefono_resp, id_parentesco } = resp;
    $("#nombre_acomp").val(nombre_acomp);
    $("#telefono_acomp").val(telefono_acomp);
    $("#nombre_resp").val(nombre_resp);
    $("#telefono_resp").val(telefono_resp);
    $("#id_parentesco_hm").val(id_parentesco);
}

const ver_anamnesis = async (id_solicitud) => {
    let tabla_salud = 'salud_solicitudes';
    let filtro = 'id';
    let id_buscar = id_solicitud;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { motivo_consulta, enfermedad_actual } = resp;
    $("#motivo_consulta").val(motivo_consulta);
    $("#enfermedad_actual").val(enfermedad_actual);
}

const ver_plan = async (id_solicitud) => {
    let tabla_salud = 'salud_solicitudes';
    let filtro = 'id';
    let id_buscar = id_solicitud;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { control } = resp;
    $("#plan").val(control);
}

const ver_valoracion = async (id_solicitud) => {
    let tabla_salud = 'salud_solicitudes';
    let filtro = 'id';
    let id_buscar = id_solicitud;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { valoracion, aplazamiento, recomendaciones, control } = resp;
    $("#aplazamiento").val(aplazamiento);
    $("#recomendaciones").val(recomendaciones);
    $("#valoracion").val(valoracion);
    $("#control_valoracion").val(control);
}

const listar_historia_laboral = (id_persona, model) => {
    $("#tabla_historia_laboral tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .ver_riesgos").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_tablas_antecendetes`, { id_persona, model }, (resp) => {
        const myTable = $("#tabla_historia_laboral").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "empresa"
                },
                {
                    data: "cargo"
                },
                {
                    data: "fecha"
                },
                {
                    defaultContent: '<span style="background-color:#ffff; color:#000; width:100%;" class="pointer form-control ver_riesgos"><span>ver</span></span>'
                },
                {
                    "render": function (data, type, full, meta) {
                        let { proteccion } = full;
                        if (proteccion == 1) return 'SI';
                        else return 'NO';
                    }
                },
                {
                    "render": function (data, type, full, meta) {
                        let { tiempo, cantidad } = full;
                        if (tiempo == 1) return `${cantidad} Años`;
                        else return `${cantidad} Meses`;
                    }
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_historia_laboral tbody").on("click", "tr", function () {
            $("#tabla_historia_laboral tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_historia_laboral tbody").on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            editar_historia_laboral(id);
        });

        $("#tabla_historia_laboral tbody").on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, 'salud_historia_laboral'));
            callbak_activo = (resp) => listar_historia_laboral(id_persona, model);
        });

        $("#tabla_historia_laboral tbody").on("click", "tr .ver_riesgos", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            ver_riesgo_laboral(id, 'salud_riesgo_laboral');
        });

    });
}

const ver_riesgo_laboral = (id, tabla_db) => {
    $(`#tabla_riesgos tbody`).off("click", "tr").off("click", "tr .eliminar").off("click", "tr .add_riesgos");
    consulta_ajax(`${ruta_salud()}ver_riesgo_laboral`, { id }, (resp) => {
        let i = 0;
        const myTable = $(`#tabla_riesgos`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    render: function (data, type, full, meta) {
                        i++;
                        return i;
                    }
                },
                {
                    data: "valor"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });


        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#tabla_riesgos tbody`).on("click", "tr", function () {
            $(`#tabla_riesgos tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $(`#tabla_riesgos tbody`).on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, tabla_db));
            callbak_activo = (resp) => ver_riesgo_laboral(id, tabla_db);
        });

        $(`#tabla_riesgos thead`).on("click", "tr .add_riesgos", function () {
            listar_riesgos_laborales(153,2,id);
        });
    });

    $("#modal_riesgos_laborales").modal();
}

const guardar_riesgo = (id_riesgo, id_historia_laboral) => {
    consulta_ajax(`${ruta_salud()}guardar_riesgo`, { id_riesgo, id_historia_laboral }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
            ver_riesgo_laboral(id_historia_laboral, 'salud_riesgo_laboral');
            $("#Modal_seleccionar_riesgo").modal('hide');
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}


const listar_accidentes = (id_persona, model) => {
    $("#tabla_accidentes tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_tablas_antecendetes`, { id_persona, model }, (resp) => {
        const myTable = $("#tabla_accidentes").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "fecha"
                },
                {
                    data: "empresa"
                },
                {
                    data: "dias_incapacidad"
                },
                {
                    data: "lesion"
                },
                {
                    data: "arp"
                },
                {
                    data: "enfermedad_profesional"
                },
                {
                    data: "secuelas"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_accidentes tbody").on("click", "tr", function () {
            $("#tabla_accidentes tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_accidentes tbody").on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            editar_accidente_laboral(id);
        });

        $("#tabla_accidentes tbody").on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, 'salud_accidentes_laborales'));
            callbak_activo = (resp) => listar_accidentes(id_persona, model);
        });

    });
}


const listar_ant_gineco = (id_persona, model) => {
    $("#tabla_ant_gineco tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_tablas_antecendetes`, { id_persona, model }, (resp) => {
        const myTable = $("#tabla_ant_gineco").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "fur"
                },
                // {
                //     data: "fup"
                // },
                {
                    data: "tipo"
                },
                {
                    data: "fecha_ultima_citologia"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_ant_gineco tbody").on("click", "tr", function () {
            $("#tabla_ant_gineco tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $("#tabla_ant_gineco tbody").on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            editar_ant_gineco(id);
        });

        $("#tabla_ant_gineco tbody").on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, 'salud_antecedentes_gineco'));
            callbak_activo = (resp) => listar_ant_gineco(id_persona, model);
        });

    });
}

const listar_tablas_antecendetes = (id_persona, tabla_html, tabla_db, model, tabla_editar) => {
    $(`#${tabla_html} tbody`).off("click", "tr").off("click", "tr .eliminar").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_tablas_antecendetes`, { id_persona, model }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "valor"
                },
                { render: (data, type, { valorx }, meta) => valorx ? valorx : '----' },
                { render: (data, type, { valorz }, meta) => valorz ? valorz : '----' },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        if (tabla_html == 'tabla_ant_personal' || tabla_html == 'tabla_vacunas' || tabla_html == 'tabla_escolaridad' || tabla_html == 'tabla_habitos') {
            myTable.column(2).visible(false);
        }

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#${tabla_html} tbody`).on("click", "tr", function () {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $(`#${tabla_html} tbody`).on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            editar_tablas_historia(id, tabla_editar);
        });

        $(`#${tabla_html} tbody`).on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, `${tabla_db}`));
            callbak_activo = (resp) => listar_tablas_antecendetes(id_persona, tabla_html, tabla_db, model);
        });

    });
}

const listar_tablas_historia = (id_solicitud, tabla_html, tabla_db, model, tabla_editar) => {
    $(`#${tabla_html} tbody`).off("click", "tr").off("click", "tr .eliminar").off("click", "tr .modificar");
    consulta_ajax(`${ruta_salud()}listar_tablas_historia`, { id_solicitud, tabla_db, model }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "valor"
                },
                {
                    data: "valorx"
                },
                { render: (data, type, { valorz }, meta) => valorz ? valorz : '----' },
                {
					render: function (data, type, full, meta) {
						let { adjunto } = full;
						return adjunto == null ? 'N/A' : `<a target='_blank' href='${Traer_Server()}${ruta_adjuntos}${adjunto}' style="background-color: white;color: black;width: 60%;" class="pointer form-control"><span >Abrir</span></a>`;
					}
				},
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        if (tabla_html == 'tabla_revision_sistemas' || tabla_html == 'tabla_diagnostico') myTable.column(2).visible(false);
        if (tabla_html != 'tabla_resultado_examenes') myTable.column(3).visible(false);
        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#${tabla_html} tbody`).on("click", "tr", function () {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $(`#${tabla_html} tbody`).on("click", "tr .modificar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            editar_tablas_historia(id, tabla_editar);
        });

        $(`#${tabla_html} tbody`).on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            confirmar_accion_general("Si desea continuar debe presionar la opción de 'Si, Aceptar'.", () => eliminar_dato_paciente(id, `${tabla_db}`));
            callbak_activo = (resp) => listar_tablas_historia(id_solicitud, tabla_html, tabla_db, model);
        });

    });
}

const editar_tablas_historia = (id, tabla_editar) => {
    if (tabla_editar == 'escolaridad') editar_escolaridad(id);
    if (tabla_editar == 'antfamiliar') editar_antfamiliar(id);
    if (tabla_editar == 'antpersonal') editar_antpersonal(id);
    if (tabla_editar == 'vacunas') editar_vacunas(id);
    if (tabla_editar == 'habito') editar_habito(id);
    if (tabla_editar == 'revsistemas') editar_revsistemas(id);
    if (tabla_editar == 'examen_fisico') editar_examen_fisico(id);
    if (tabla_editar == 'resultado_examenes') editar_resultado_examenes(id);
}

const editar_escolaridad = async (id) => {
    let tabla_salud = 'salud_escolaridad_paciente';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_escolaridad, id_tipo_estado } = resp;
    $("#valor_param1").val(id_escolaridad);
    $("#valor_param2").val(id_tipo_estado);
    $("#modal_valor_parametro").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_escolaridad', 'form_valor_parametro', id_solicitud, id, 'modal_valor_parametro', 'modal_escolaridad');
}

const editar_historia_laboral = async (id) => {
    let tabla_salud = 'salud_historia_laboral';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { empresa, cargo, fecha, proteccion, tiempo, cantidad } = resp;
    $("#empresa").val(empresa);
    $("#cargo").val(cargo);
    $("#fecha_hl").val(fecha);
    $("#proteccion").val(proteccion);
    $("#tiempo").val(tiempo);
    $("#cantidad_tiempo").val(cantidad);
    $("#container_add_riesgos").addClass("oculto");
    $("#container_riesgos").removeClass("oculto");
    consulta_ajax(`${ruta_salud()}ver_riesgo_laboral`, { id }, async (resp) => {
        pintar_datos_combo(resp, '#riesgos_agregados ', 'Riesgos Asignados');
    });
    $("#modal_add_historia_laboral").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_historia_laboral', 'form_add_historia_laboral', id_solicitud, id, 'modal_add_historia_laboral', 'modal_historial_laboral');
}

const editar_accidente_laboral = async (id) => {
    let tabla_salud = 'salud_accidentes_laborales';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { fecha, dias_incapacidad, lesion, arp, enfermedad_profesional, secuelas, id_historia_laboral } = resp;
    $("#fecha_al").val(fecha);
    $("#incapacidad").val(dias_incapacidad);
    $("#lesion").val(lesion);
    $("#arp").val(arp);
    $("#enfermedad").val(enfermedad_profesional);
    $("#secuelas").val(secuelas);
    valor = await cargar_empresas(id_solicitante);
    pintar_datos_combo(valor, '.cbxempresas', 'Seleccione Empresa');
    $("#id_empresa").val(id_historia_laboral);
    $("#modal_add_accidentes").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_accidente_laboral', 'form_add_accidentes', id_solicitud, id, 'modal_add_accidentes', 'modal_historial_laboral');
}

const editar_antfamiliar = async (id) => {
    let tabla_salud = 'salud_antecedentes_familiares';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_tipo_enfermedad, id_parentesco, observacion } = resp;
    $("#id_tipo_enfermedad").val(id_tipo_enfermedad);
    $("#id_parentesco").val(id_parentesco);
    $("#observacion_antf").val(observacion);
    $("#modal_add_antfamiliar").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_antfamiliar', 'form_add_antfamiliar', id_solicitud, id, 'modal_add_antfamiliar', 'modal_antecedentes');
}

const editar_antpersonal = async (id) => {
    let tabla_salud = 'salud_antecedentes_personales';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_tipo_antecedente, observacion } = resp;
    $("#id_tipo_antecedente").val(id_tipo_antecedente);
    $("#observacion_antp").val(observacion);
    $("#modal_add_antpersonal").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_antpersonal', 'form_add_antpersonal', id_solicitud, id, 'modal_add_antpersonal', 'modal_antecedentes');
}

const editar_vacunas = async (id) => {
    let tabla_salud = 'salud_vacuna_paciente';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_vacuna, observacion } = resp;
    $("#id_vacuna").val(id_vacuna);
    $("#observacion_vacuna").val(observacion);
    $("#modal_add_vacuna").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_vacunas', 'form_add_vacuna', id_solicitud, id, 'modal_add_vacuna', 'modal_antecedentes');
}

const editar_ant_gineco = async (id) => {
    let tabla_salud = 'salud_antecedentes_gineco';
    let filtro = 'id';
    let id_buscar = id;
    let data = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    $("#menarquia").val(data.menarquia);
    $("#ciclos").val(data.ciclos);
    $("#fur").val(data.fur);
    $("#cantidad_g").val(data.cantidad_gestaciones);
    $("#cantidad_p").val(data.cantidad_partos);
    $("#cantidad_c").val(data.cantidad_cesarea);
    $("#cantidad_a").val(data.cantidad_abortos);
    $("#cantidad_v").val(data.cantidad_vivo);
    // $("#fup").val(data.fup);
    $("#planifica").val(data.planifica);
    $("#tipo_planificacion").val(data.id_tipo_planificacion);
    $("#dismenorreas").val(data.dismenorreas);
    $("#fecha_citologia").val(data.fecha_ultima_citologia);
    $("#tipo_citologia").val(data.citologia_normal);
    $("#observacion_gineco").val(data.observacion);
    $("#modal_ant_gineco").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_ant_gineco', 'form_ant_gineco', id_solicitud, id, 'modal_ant_gineco', 'modal_antecedentes');
}

const editar_habito = async (id) => {
    let tabla_salud = 'salud_habitos_paciente';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_habito, id_frecuencia, tipo, cantidad, fecha_desde, fecha_hasta, id_duracion } = resp;
    $("#habito").val(id_habito);
    $("#fecha_desde").val(fecha_desde);
    $("#fecha_hasta").val(fecha_hasta);
    $("#id_frecuencia").val(id_frecuencia);
    $("#cantidad").val(cantidad);
    $("#tipo_habito").val(tipo);
    $("#id_duracion").val(id_duracion);
    $("#modal_add_habito").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_habito', 'form_add_habito', id_solicitud, id, 'modal_add_habito', 'modal_antecedentes');
}

const editar_revsistemas = async (id) => {
    let tabla_salud = 'salud_revision_sistema';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_tipo_sistema, observacion } = resp;
    $("#id_sistema").val(id_tipo_sistema);
    $("#observacion_rev").val(observacion);
    $("#modal_add_revsistemas").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_revision_sistemas', 'form_add_revsistemas', id_solicitud, id, 'modal_add_revsistemas', 'modal_revision_sistemas');
}

const editar_examen_fisico = async (id) => {
    let tabla_salud = 'salud_examen_fisico';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_tipo_examen, id_tipo_estado, observacion } = resp;
    $("#id_tipo_examen").val(id_tipo_examen);
    $("#id_estado_examenf").val(id_tipo_estado);
    $("#observacion_examen_fisico").val(observacion);
    $("#modal_add_examenf").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_examen_fisico', 'form_add_examen_fisico', id_solicitud, id, 'modal_add_examenf', 'modal_examen_fisico');
}

const editar_resultado_examenes = async (id) => {
    let tabla_salud = 'salud_examenes_paraclinicos';
    let filtro = 'id';
    let id_buscar = id;
    let resp = await traer_ultima_atencion(id_buscar, tabla_salud, filtro);
    let { id_tipo_examen_par, id_estado_examen, observacion, nombre_real } = resp;
    $("#id_examenpar").val(id_tipo_examen_par);
    $("#id_estado_examen").val(id_estado_examen);
    $("#observacion_paraclinicos").val(observacion);
    $(".soporte").val(nombre_real);
    $("#modal_add_examenpar").modal();
    callbak_activo = (resp) => guardar_datos_paciente('editar_examenpar', 'form_add_examenpar', id_solicitud, id, 'modal_add_examenpar', 'modal_resualtado_examenes', 'modal_diagnostico');
}

const buscar_diagnostico = (data, sel_diagnostico, solicitud) => {
    consulta_ajax(`${ruta_salud()}buscar_diagnostico`, { data, solicitud }, resp => {
        $(`#tabla_diagnostico_busqueda tbody`).off("click", "tr td .diagnostico").off("click", "tr").off("click", "tr td:nth-of-type(1)");
        let i = 0;
        const myTable = $("#tabla_diagnostico_busqueda").DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "codigo"
                },
                {
                    data: "descripcion"
                },
                {
                    defaultContent: '<span style="color: #39B23B;" title="Seleccionar Diagnóstico" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default diagnostico" ></span>'
                }
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });
        $("#tabla_diagnostico_busqueda tbody").on("click", "tr", function () {
            $("#tabla_diagnostico_busqueda tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tabla_diagnostico_busqueda tbody").on("click", "tr td .diagnostico", function () {
            let data = myTable.row($(this).parent().parent()).data();
            let id_diagnostico = data.id;
            if (sel_diagnostico == 1) {
                confirmar_accion_general(`Esta seguro de seleccionar el Diagnostico ${data.codigo}, si desea continuar debe presionar la opción de 'Si, Aceptar'.`, () => guardar_datos_paciente('add_diagnostico', 'form_add_diagnostico', id_solicitud, id_diagnostico));
                callbak_listar = (resp) => listar_tablas_historia(id_solicitud, 'tabla_diagnostico', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
                myTable.row($(this).parents('tr')).remove().draw();
            } else {
                cod_diagnostico = id_diagnostico;
                $("#diagnostico").val(data.descripcion);
                $("#modal_add_diagnostico").modal("hide");
            }
        });
    });
}

const detalle_historia_clinica = (id_solicitud, tabla_html, tabla_db, model) => {
    $(`#${tabla_html} tbody`).off("click", "tr");
    consulta_ajax(`${ruta_salud()}listar_tablas_historia`, { id_solicitud, tabla_db, model }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "valor"
                },
                {
                    data: "valorx"
                },
                { render: (data, type, { valorz }, meta) => valorz ? valorz : '----' },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        if (tabla_html == 'tabla_rev_sistemas' || tabla_html == 'tabla_diagnostico_pac' || tabla_html == 'tabla_detalle_revsistema' || tabla_html == 'tabla_detalle_diagnostico') myTable.column(2).visible(false);

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#${tabla_html} tbody`).on("click", "tr", function () {
            $(`#${tabla_html} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

    });
}

const listar_historial_ocupacional = (id_persona) => {
    $(`#tabla_historial_atenciones tbody`).off("click", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr .imprimir");
    consulta_ajax(`${ruta_salud()}listar_historial_ocupacional`, { id_persona }, (resp) => {
        const myTable = $(`#tabla_historial_atenciones`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "ver"
                },
                {
                    data: "tipo_examen"
                },
                {
                    data: "fecha_registra"
                },
                { render: (data, type, { recomendaciones }, meta) => recomendaciones ? recomendaciones : '---' },
                { render: (data, type, { control }, meta) => control ? control : '---' },
                { render: (data, type, { valoracion_examen }, meta) => valoracion_examen ? valoracion_examen : '---' },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#tabla_historial_atenciones tbody`).on("click", "tr", function () {
            $(`#tabla_historial_atenciones tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_historial_atenciones tbody').on('click', 'tr td:nth-of-type(1)', function () {
            let data = myTable.row($(this).parent()).data();
            id_historia_clinica = data.id;
            detalle_historia_clinica(id_historia_clinica, 'tabla_rev_sistemas', 'salud_revision_sistema', 'listar_revision_sistema');
            ver_examen_fisico(id_historia_clinica);
            detalle_historia_clinica(id_historia_clinica, 'tabla_exa_fisico', 'salud_examen_fisico', 'listar_examen_fisico');
            detalle_historia_clinica(id_historia_clinica, 'tabla_exa_paraclinico', 'salud_examenes_paraclinicos', 'listar_resultado_examenes');
            detalle_historia_clinica(id_historia_clinica, 'tabla_diagnostico_pac', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
            $(".titulo_modal").html("Historia Médica Ocupacional");
            $(".exa_paraclinico").show('slow');
            $("#modal_detalle_historial").modal();
        });

        $("#tabla_historial_atenciones tbody").on("click", "tr .imprimir", async function () {
            let data = myTable.row($(this).parent()).data();
            id_historia_clinica = data.id;
            await listar_detalle_historia_ocupacional(id_persona, id_solicitud, data);
            imprimir_historia(data, 'HISTORIA MÉDICA OCUPACIONAL');
        });

    });
}

const listar_detalle_historia_ocupacional = async (id_persona, id_solicitud, data) => {
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_escolaridad', 'listar_escolaridad');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_familiar', 'listar_antfamiliar');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_personales', 'listar_antpersonal');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_habitos', 'listar_habitos');
    // listar_tablas_laboral_imprimir(id_persona, id_solicitud, 'tab_historia_laboral', 'listar_historia_laboral');
    // listar_tablas_laboral_imprimir(id_persona, id_solicitud, 'tab_accidente_laboral', 'listar_accidentes');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_revision_sistema', 'listar_revision_sistema');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_examen_fisico', 'listar_examen_fisico');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_paraclinicos', 'listar_resultado_examenes');
    // listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_diagnosticos', 'listar_diagnosticos');
    $("#tab_escolaridad").addClass("oculto");
    $("#tab_paraclinicos").addClass("oculto");
    $("#tab_historia_laboral").addClass("oculto");
    $("#tab_accidente_laboral").addClass("oculto");
    $("#tab_habitos").addClass("oculto");
    $("#tab_antecedente_familiar").addClass("oculto");
    $("#tab_antecedente_personales").addClass("oculto");
    $("#tab_revision_sistema").addClass("oculto");
    $("#tab_examen_fisico").addClass("oculto");
    $("#tab_diagnosticos").addClass("oculto");
    $(".detalle_oculto").addClass("oculto");
    $(".valoracion_pac").removeClass("oculto");
    $(".dato_familiar").addClass("oculto");
    $(".clausura").removeClass("oculto");
    $(".ant_gineco").addClass("oculto");
    $(".profesional_ocup").html(data.profesional);
    await ver_examen_fisico(id_solicitud);
    // let dato = await traer_ultima_atencion(id_persona, 'salud_antecedentes_gineco', 'id_persona');
    // if (dato.length > 0) {
    //     let { citologia_normal, dismenorreas, menarquia, ciclos, fur, cantidad_gestaciones, cantidad_partos, cantidad_cesarea, cantidad_abortos, cantidad_vivo, fup, id_tipo_planificacion, fecha_ultima_citologia, observacion_gineco } = dato;
    //     if (citologia_normal == 1) citologia = 'SI';
    //     else citologia = 'NO';
    //     if (dismenorreas == 1) dis = 'SI';
    //     else dis = 'NO';
    //     if (ciclos == 1) ciclo = 'Regular';
    //     else if (ciclos == 2) ciclo = 'Irregular';
    //     else if (ciclos == 3) ciclo = 'Menopausica';
    //     $(".menarquia").html(menarquia);
    //     $(".ciclo").html(ciclo);
    //     $(".fur").html(fur);
    //     $(".g").html(cantidad_gestaciones);
    //     $(".p").html(cantidad_partos);
    //     $(".c").html(cantidad_cesarea);
    //     $(".a").html(cantidad_abortos);
    //     $(".v").html(cantidad_vivo);
    //     // $(".fup").html(fup);
    //     let parametro = await buscar_parametro_id(id_tipo_planificacion);
    //     let { valor } = parametro[0];
    //     $(".planificacion").html(valor);
    //     $(".dismenorreas").html(dis);
    //     $(".fuc").html(fecha_ultima_citologia);
    //     $(".citologia").html(citologia);
    //     $(".observacion").html(observacion_gineco);
    // }
}

const listar_historial_mgeneral = (id_persona) => {
    $(`#tabla_historial_mgeneral tbody`).off("click", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr .imprimir");
    consulta_ajax(`${ruta_salud()}listar_historial_mgeneral`, { id_persona }, (resp) => {
        const myTable = $(`#tabla_historial_mgeneral`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "ver"
                },
                {
                    data: "fecha_registra"
                },
                { render: (data, type, { motivo_consulta }, meta) => motivo_consulta ? motivo_consulta : '---' },
                { render: (data, type, { enfermedad_actual }, meta) => enfermedad_actual ? enfermedad_actual : '---' },
                { render: (data, type, { control }, meta) => control ? control : '---' },
                {
                    data: "profesional"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#tabla_historial_mgeneral tbody`).on("click", "tr", function () {
            $(`#tabla_historial_mgeneral tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_historial_mgeneral tbody').on('click', 'tr td:nth-of-type(1)', function () {
            let data = myTable.row($(this).parent()).data();
            id_historia_clinica = data.id;
            detalle_historia_clinica(id_historia_clinica, 'tabla_rev_sistemas', 'salud_revision_sistema', 'listar_revision_sistema');
            ver_examen_fisico(id_historia_clinica);
            detalle_historia_clinica(id_historia_clinica, 'tabla_exa_fisico', 'salud_examen_fisico', 'listar_examen_fisico');
            detalle_historia_clinica(id_historia_clinica, 'tabla_diagnostico_pac', 'salud_diagnosticos_paciente', 'listar_diagnosticos');
            $(".titulo_modal").html("Historia Clínica Medicina General");
            $(".exa_paraclinico").hide('slow');
            $("#modal_detalle_historial").modal();
        });

        $("#tabla_historial_mgeneral tbody").on("click", "tr .imprimir", async function () {
            let data = myTable.row($(this).parent()).data();
            id_historia_clinica = data.id;
            await listar_detalle_historial_mgeneral(id_persona, id_solicitud, data);
            imprimir_historia(data, 'HISTORIA CLINICA MÉDICINA GENERAL');
        });
    });
}

const listar_detalle_historial_mgeneral = async (id_persona, id_solicitud, data) => {
    listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_familiar', 'listar_antfamiliar');
    listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_personales', 'listar_antpersonal');
    listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_revision_sistema', 'listar_revision_sistema');
    listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_examen_fisico', 'listar_examen_fisico');
    listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_historia', 'tab_diagnosticos', 'listar_diagnosticos');
    $("#tab_escolaridad").addClass("oculto");
    $("#tab_paraclinicos").addClass("oculto");
    $("#tab_historia_laboral").addClass("oculto");
    $("#tab_accidente_laboral").addClass("oculto");
    $("#tab_habitos").addClass("oculto");
    $(".valoracion_pac").addClass("oculto");
    $(".ant_gineco").addClass("oculto");
    $(".clausura").addClass("oculto");
    let data_familiar = await traer_ultima_atencion(id_solicitud, 'salud_datos_acompanante', 'id_solicitud');
    let { nombre_acomp, telefono_acomp, nombre_resp, telefono_resp, id_parentesco } = data_familiar;
    $(".nombre_acomp").html(nombre_acomp);
    $(".telefono_acomp").html(telefono_acomp);
    $(".nombre_resp").html(nombre_resp);
    $(".telefono_resp").html(telefono_resp);
    $(".parentesco_resp").html(id_parentesco);
    $(".dato_familiar").removeClass("oculto");
    await ver_examen_fisico(id_solicitud);
}

const imprimir_historia = async (data, historia) => {
    $(".titulo_print").html(historia);
    let { fecha_registra, observacion, id_persona, id_servicio, enfermedad_actual, motivo_consulta, control, tipo_solicitante, valoracion_examen, aplazamiento, recomendaciones } = data;
    let dato_paciente = await buscar_datos_paciente(id_persona, tipo_solicitante);
    let { identificacion, nombre_completo, n_genero, genero_y, edad, dependencia, lugar_nacimiento, fecha_nacimiento, id_tipo_estadocivil, eps, arl, telefono, direccion, profesion, cargo, servicio_militar, fecha_ingreso } = dato_paciente;
    if (genero_y == 1) {
        $(".ant_gineco").removeClass("oculto");
    } else if(genero_y == 2){
        $(".ant_gineco").addClass("oculto");
    }
    if (id_servicio != null) {
        let data_servicio = await buscar_parametro_id(id_servicio);
        let { valor } = data_servicio[0];
        $(".servicio").html(valor);
    } else {
        $(".servicio").html('Historia Clínica Medicina General');
        $(".ant_gineco").addClass("oculto");
    }
    if (servicio_militar == 1) smilitar = 'SI';
    else smilitar = 'NO';
    $(".fecha_atencion").html(fecha_registra);
    $(".nombre_apellido").html(nombre_completo);
    $(".n_historia").html(identificacion);
    $(".identificacion").html(identificacion);
    $(".lugar_nacimiento").html(lugar_nacimiento);
    $(".fecha_nacimiento").html(fecha_nacimiento);
    $(".genero").html(n_genero);
    $(".edad").html(edad);
    $(".eps").html(eps);
    $(".arl").html(arl);
    $(".telefono").html(telefono);
    $(".direccion").html(direccion);
    $(".profesion").html(profesion);
    $(".servicio_militar").html(smilitar);
    let estado_civil = await buscar_parametro_id(id_tipo_estadocivil);
    if (estado_civil.length > 0) {
        let { valor } = estado_civil[0];
        $(".estado_civil").html(valor);
    }
    $(".fecha_ingreso").html(fecha_ingreso);
    $(".dependencia").html(dependencia);
    $(".cargo").html(cargo);
    $(".observacion").html(observacion);
    $(".enfermedad_actual").html(enfermedad_actual);
    $(".motivo_consulta").html(motivo_consulta);
    $(".control").html(control);
    $(".resultado").html(valoracion_examen);
    $(".aplazamiento").html(aplazamiento);
    $(".recomendaciones").html(recomendaciones);
    $(".empresa").html('Universidad de la Costa CUC');
    let imprimir = document.querySelector("#imprimir_historia");
    imprimirDIV(imprimir, true);
}

const traer_ultima_atencion = async (id_buscar, tabla_salud, filtro) => {
    return new Promise(resolve => {
        let url = `${ruta_salud()}traer_ultima_atencion`;
        consulta_ajax(url, { id_buscar, tabla_salud, filtro }, resp => {
            resolve(resp);
        });
    });
}

const listar_tablas_imprimir = (id_persona, id_solicitud, control, tabla_html, model) => {
    consulta_ajax(`${ruta_salud()}${control}`, { id_persona, id_solicitud, model }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [
                {
                    data: "valor"
                },
                {
                    data: "valorx"
                },
                { render: (data, type, { valorz }, meta) => valorz ? valorz : '----' },
            ],
            language: idioma,
            dom: "",
            buttons: [],
        });
        if (tabla_html != 'tab_habitos' && tabla_html != 'tab_examen_fisico') myTable.column(2).visible(false);

        if (resp.length == 0) $(`#${tabla_html}`).addClass("oculto");
        else $(`#${tabla_html}`).removeClass("oculto");
    });
}

const listar_tablas_laboral_imprimir = (id_persona, id_solicitud, tabla_html, model) => {
    consulta_ajax(`${ruta_salud()}listar_tablas_antecendetes`, { id_persona, model }, (resp) => {
        const myTable = $(`#${tabla_html}`).DataTable({
            destroy: true,
            searching: false,
            processing: false,
            data: resp,
            columns: [
                {
                    data: "fecha"
                },
                {
                    data: "empresa"
                },
                {
                    data: "valor"
                },
                {
                    "render": function (data, type, full, meta) {
                        let { valorx } = full;
                        if (valorx == 1) {
                            return 'SI';
                        } else if (valorx == 1) {
                            return 'NO';
                        } else {
                            return valorx;
                        }
                    }
                },
                {
                    "render": function (data, type, full, meta) {
                        let { valory } = full;
                        if (valory == 1) {
                            return "Años";
                        } else if (valory == 1) {
                            return "Meses";
                        } else {
                            return valory;
                        }
                    }
                },
                {
                    data: "valorz"
                },
            ],
            language: idioma,
            dom: "",
            buttons: [],
        });

        if (resp.length == 0) $(`#${tabla_html}`).addClass("oculto");
        else $(`#${tabla_html}`).removeClass("oculto");
    });
}

const filtrar_pacientes = (habito = '', antecedente = '', cod_diagnostico = '', fecha_inicio = '', fecha_fin = '') => {
    $(`#tabla_filtro_pac tbody`).off("click", "tr");
    consulta_ajax(`${ruta_salud()}filtrar_pacientes`, { habito, antecedente, cod_diagnostico, fecha_inicio, fecha_fin }, (resp) => {
        const myTable = $(`#tabla_filtro_pac`).DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "nombre_completo"
                },
                {
                    data: "dependencia"
                },
                {
                    data: "tipo_examen"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: get_botones(),

        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $(`#tabla_filtro_pac tbody`).on("click", "tr", function () {
            $(`#tabla_filtro_pac tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });
    });
}

const bitacoras_paciente = (id_persona) => {
    $("#tabla_bitacoras_paciente tbody").off("click", "tr").off("click", "tr td:nth-of-type(1)").off("click", "tr .imprimir").off("click", "tr .bitacora");
    consulta_ajax(`${ruta_salud()}consultar_bitacoras`, { id_persona }, (resp) => {
        const myTable = $("#tabla_bitacoras_paciente").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "ver"
                },
                {
                    data: "tipo_solicitud"
                },
                {
                    render: (data, type, { servicio }, meta) => servicio ? servicio : '----'
                },
                {
                    data: "fecha_solicitud"
                },
                {
                    data: "accion"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],
        });

        //EVENTOS DE LA TABLA ACTIVADOS
        $("#tabla_bitacoras_paciente tbody").on("click", "tr", function () {
            $("#tabla_bitacoras_paciente tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });

        $('#tabla_bitacoras_paciente tbody').on('click', 'tr td:nth-of-type(1)', function () {
            let data = myTable.row($(this).parent()).data();
            ver_detalle_bitacora(data);
            $("#modal_detalle_bitacora").modal();
        });

        $("#tabla_bitacoras_paciente tbody").on("click", "tr .bitacora", function () {
            let data = myTable.row($(this).parent()).data();
            id_solicitud = data.idsolicitud;
            $("#modal_crear_bitacora").modal();
        });

        $("#tabla_bitacoras_paciente tbody").on("click", "tr .imprimir", async function () {
            let data = myTable.row($(this).parent()).data();
            let { id_persona, id_solicitud, id_tipo_solicitud } = data;
            listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_familiar_bit', 'listar_antfamiliar');
            listar_tablas_imprimir(id_persona, id_solicitud, 'listar_tablas_antecendetes', 'tab_antecedente_personales_bit', 'listar_antpersonal');
            await ver_examen_fisico(id_solicitud);
            if (id_tipo_solicitud == "Sal_Sol_Ate") $("#signos_vitales").addClass("oculto");
            else $("#signos_vitales").removeClass("oculto");
            imprimir_bitacora(data);
        });
    });
}

const ver_detalle_bitacora = async (data) => {
    let { fecha_registra, id_persona, tipo_solicitante, id_solicitud } = data;
    let dato_paciente = await buscar_datos_paciente(id_persona, tipo_solicitante);
    let { identificacion, nombre_completo, genero, edad, dependencia } = dato_paciente;
    let gen = '';
    if (genero == 1) gen = 'Femenino';
    else gen = 'Masculino';
    $(".fecha_atencion").html(fecha_registra);
    $(".nombre_apellido").html(nombre_completo);
    $(".identificacion").html(identificacion);
    $(".genero").html(gen);
    $(".edad").html(edad);
    $(".dependencia").html(dependencia);
    let resp = await traer_ultima_atencion(id_solicitud, 'salud_bitacora', 'id_solicitud');
    let { observacion_ingreso, observacion_salida, motivo_ingreso, condicion_general, reporte_atencion } = resp;
    $(".observacion_ingreso").html(observacion_ingreso);
    $(".observacion_salida").html(observacion_salida);
    $(".motivo_ingreso").html(motivo_ingreso);
    $(".condicion_general").html(condicion_general);
    $(".reporte_atencion").html(reporte_atencion);
}

const imprimir_bitacora = async (data) => {
    await ver_detalle_bitacora(data);
    let imprimir = document.querySelector("#imprimir_bitacora");
    imprimirDIV(imprimir, true);
}
//Agregado por Neyla
const Guardar_Repor_Covid = () => {
    let data = new FormData(document.getElementById("GuardarRepProCovid"));
    data.append('id_persona', id_solicitante);
    data.append('id_tipo_persona', id_tipo_persona);
    let url = `${ruta_salud()}GuardarReporCov`;
    enviar_formulario(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        MensajeConClase(mensaje, tipo, titulo);
        $("#GuardarRepProCovid").get(0).reset();
        $("#Modal_Covid").modal("hide"); 
       // location.reload();
    });
}

const EditarEstadoCovid = () => {
    $id = $("#idsolicitudcov").val();
    $texto = $("#EstadoCambio").text();
    var combo = document.getElementById("EstadoCambio");
    var selected = combo.options[combo.selectedIndex].text;
    let fordata = new FormData(document.getElementById("form_cambiar_estado_covid"));
    fordata.append('id', $id);
    fordata.append('Estado', selected);
    let data = formDataToJson(fordata);
    let url = `${ruta_salud()}EditarEstadoCovid`;
    consulta_ajax(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
            MensajeConClase(mensaje, tipo, titulo);
            listar_atenciones()
            $("#form_cambiar_estado_covid").get(0).reset();
            $("#ModalEditarE").modal("hide"); 
            Cargar_parametro_buscado(322, ".cbxcambioestado", "Seleccione el motivo...");
     });
}

const EditarTpReporte = () => {
    let data = new FormData(document.getElementById("form_editar_treporte"));
    let url = `${ruta_salud()}EditarTpReporte`;
    enviar_formulario(url, data, (resp) => {
        let { tipo, titulo, mensaje } = resp;
        MensajeConClase(mensaje, tipo, titulo);
            listar_atenciones();
            $("#form_editar_treporte").get(0).reset();
            $("#modal_editar_treporte").modal("hide");//
            $("#modal_detalle_solicitud").modal("hide");
     });
}
//
function TPUsu(){
    $tipo = $("#tipopersona").val();
    $txt = $("#txt_per_buscar_ate").val();
    if($txt!=""){
        $("#botonsub").submit();
    }
   if($tipo=='Per_emp'){
    document.getElementById("div_sintomas").style.display="block";
    document.getElementById("subclasifi_salud").style.display="block";
    document.getElementById("eps_salud").style.display="block";
    document.getElementById("barrio").type="text";
   }else{
    document.getElementById("div_sintomas").style.display="none";
    document.getElementById("subclasifi_salud").style.display="none";
    document.getElementById("eps_salud").style.display="none";
    document.getElementById("barrio").type="hidden";
   }
}

function TpEstado(est){
    $Estado = $("#EstadoCambio").val();
        Cargar_parametro_buscado($Estado, ".cbxcambioestado", "Seleccione el motivo...");
}
const NewObservacion=()=>{
    let data = new FormData(document.getElementById("form_nueva_observacion"));
   let url = `${ruta_salud()}AgregarNuevaObservacion`;
   enviar_formulario(url, data, (resp) => {
       let { tipo, titulo, mensaje } = resp;
       MensajeConClase(mensaje, tipo, titulo);
            $("#form_nueva_observacion").get(0).reset();
            $("#modal_nueva_observacion").modal("hide");
            Mostrarobservacion();
     });
}
const Mostrarobservacion=()=>{
    $slp = $("#soli_id").val();
    consulta_ajax(`${ruta_salud()}listar_salud_observaciones`, { solicitud:$slp }, (resp) => {
        const myTable = $("#tabla_salud_observaciones").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "observacion"
                },
                {
                    data: "profesional"
                },
                {
                    data: "fecha_registro"
                },
            ],
            language: idioma,
            dom: "Bfrtip",
            buttons: [],
            "aaSorting": [],

        });
    });

}