let ruta = `${Traer_Server()}index.php/becas_control/`;
let callbak_activo = resp => { };
let callbak_activo_alt = resp => { };
let callback_solicitud = resp => { }
let tabla = '';
let mensaje = '';
let id_solicitud = null;
let data_solicitud = {};
let data_filtro = {};
let informacion_principal = {}
let tipo_guardado = 0; //1 - < 1 db y 0 array >
let id_plan_ent = null;
let id_anexo = null;
let id_estado = null;
let actividades = [];
let compromisos = [];
let inicial = null
let renovar = null
let id_persona = null;
let permiso_selec = null;
let data_solicitante = { 'nombre': null, 'correo': null }
let data_solicitud_noti = { 'tipo_soli': null, 'programa': null }
let ruta_archivos = "archivos_adjuntos/becas/";
let ruta_soportes = "archivos_adjuntos/becas/soportes/";
let cargados = 0;

$(document).ready(function () {
  validar_cantidad_de_solicitud()

  $('#btn_nueva_solicitud').click(function () {
    if (inicial.cantidad === '1') {
      enviar_a_solicitud('solicitudes', 'Oops..!', inicial.id)
    } else if (inicial.cantidad === '0') {
      guardarSolicitud()
    }
  });

  $('#btn_renovaciones').click(async function () {
    let solicitud = await solicitud_a_renovar()
    if ($.isEmptyObject(solicitud)) {
      MensajeConClase('No cuenta con solicitudes para renovar', 'info', 'success')
    } else {
      if (renovar.cantidad === '1') {
        enviar_a_solicitud('renovaciones', 'Oops..!', renovar.id)
      } else if (renovar.cantidad === '0') {
        guardarSolicitud(solicitud.id);
      }
    }
  });

  $("#form_solicitud_beca").submit((e) => {
    e.preventDefault()
    guardarInformacionPrincipal();
  });

  $(".regresar_menu").click(() => {
    administrar_elementos('menu');
  });

  $("#listado_solicitudes").click(() => {
    administrar_elementos('solicitudes');
  });

  $('#btn_limpiar_filtros').click(() => {
    Con_filtros(false)
    data_filtro = {}
    listarSolicitudesBecas();
    $('#form_filtrar_solicitudes').get(0).reset()
  })

  $('#btn_filtrar').on('click', function () {
    $("#modal_filtrar_solicitudes .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Crear Filtro</span>');
    $('#modal_filtrar_solicitudes').modal('show')
  })

  $('#form_filtrar_solicitudes').submit(e => {
    e.preventDefault()
    filtrarSolicitud()
  })

  $('#btn_notificaciones').on('click', function () {
    mostrar_solicitudes_notificaciones()
    $('#modal_notificaciones_becas').modal('show')
  })

  $("#btn_administrar").click(() => {
    $("#form_administrar").get(0).reset();
    $("#s_persona").html('Seleccione Persona');
    $("#administrar_permisos_becas").modal();
    id_persona = null;
    listar_tipo_solicitud(id_persona);
  });

  $("#s_persona").click(() => {
    listar_personas();
    $("#modal_elegir_persona").modal('show');
    $("#txt_persona").val('');
  });

  $("#btn_buscar_persona").click(() => $("#frm_buscar_persona").trigger('submit'));

  $("#frm_buscar_persona").submit(e => {
    e.preventDefault();
    let persona = $("#txt_persona").val();
    listar_personas(persona);
  });

  //REFACTORIZANDO INFORMACION PRINCIPAL
  $('#informacion_principal').click(function () {
    $('#modal_informacion_principal').modal('show')
    mostrarInformacionPrincipalAgregada(informacion_principal)
  })

  $('#select_incluye_beca').hide('fast')
  $('#select_tipo_apoyo').show('fast')
  $('#id_comision_estudio').change(function () {
    let comision = $(this).val()
    if (comision === 'Bec_Com_C') {
      $('#id_beca').show('fast')
      $('#id_beca').attr('required', true)
    } else {
      $('#id_beca').hide('fast')
      $('#id_beca').attr('required', false)
    }
  })

  //REFACTORIZANDO CONCEPTOS
  $("#form_agregar_conceptos").submit((e) => {
    e.preventDefault();
    guardarConceptosNew();
  });

  //REFACTORIZANDO 
  $('#ver_concepto').on('click', () => {
    validarOpciones(informacion_principal)
    listarSolicitudesBecasConcepto(id_solicitud)
  })

  $('#btn_agregar_conceptos_detalle').click(() => {
    $("#modal_agregar_conceptos .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Concepto</span>');
    $('#select_incluye_beca').hide('fast')
    $('#select_tipo_apoyo').show('fast')
    $('#valor_total').show('fast')
    $('#apoyo_solicitado').show('fast')
    callbak_activo = (resp) => AgregarConceptoSolicitud(resp)
    $('#modal_agregar_conceptos').modal('show');
    $(`#form_agregar_conceptos`).get(0).reset()
    $('#select_tipo_apoyo').attr('disabled', false)
    $("#beca_incluye").attr('disabled', false)
  });

  //PLAN DE ACCION
  $('#btn_agregar_plan_accion_detalle').click(() => {
    $("#modal_agregar_plan_accion .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Compromiso</span>');
    // limpiarSeleActividades()
    callbak_activo = (resp) => agregarPlanDatabaseNew(resp)
    tipo_guardado = 0;
    $('#modal_agregar_plan_accion').modal('show');
  })

  //SECTOR PRODUCTIVO
  $('#btn_agregar_exp_detalle').click(() => {
    $("#modal_agregar_sector_prod .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Experiencia</span>');
    $(`#form_agregar_sector_prod`).get(0).reset();
    callbak_activo = (resp) => agregarExperienciaDatabaseNew(resp)
    $('#modal_agregar_sector_prod').modal('show');
  });

  $("#form_agregar_sector_prod").submit((e) => {
    e.preventDefault();
    guardarExperiencia();
  });

  $('#ver_exp').on('click', () => {
    $('#modal_detalle_experiencia_solicitud').modal('show')
    listarSolicitudesBecasExperiencia(id_solicitud)
  });

  $('#btn_agregar_prod_intelectual_detalle').click(() => {
    $("#modal_agregar_prod_intel .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Producción Intelectual</span>');
    $(`#form_agregar_prod_intel`).get(0).reset();
    callbak_activo = (resp) => agregarIntelectualDatabaseNew(resp)
    $('#modal_agregar_prod_intel').modal('show');
  });

  $("#form_agregar_prod_intel").submit((e) => {
    e.preventDefault();
    guardarIntelectual();
  });

  $('#ver_intelectual').on('click', () => {
    $('#modal_detalle_intelectual_solicitud').modal('show')
    listarSolicitudesBecasProdIntelectual(id_solicitud)
  });

  $('#mas_compromiso').on('click', () => {
    if (tipo_guardado === 0) {
      callbak_activo_alt = resp => agregarCompromisoArrayNew(resp)
    } else {
      callbak_activo_alt = (resp) => agregarCompromisoDatebase(resp)
    }
    $('#modal_agregar_compromisos .modal-title').html('<span class="fa fa-wrench"></span> <span id="text_add_arts">Agregar Compromiso</span>');
    $('#modal_agregar_compromisos').modal('show');
  })

  $('#btn_agregar_entregable_detalle').click(() => {
    $("#modal_agregar_entregable .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Entregable</span>');
    $(`#form_agregar_entregable`).get(0).reset();
    limpiarSeleCompromisos()
    tipo_guardado = 0;
    callbak_activo = (resp) => agregarEntregableDatabaseNew(resp)
    $('#modal_agregar_entregable').modal('show');
  });

  $('#info_solicitante').click(() => {
    listarDatosSolicitante(data_solicitud.id_usuario_registro)
  });

  $('#btn_agregar_herramientas_detalle').click(() => {
    $("#modal_agregar_herramientas .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Herramienta</span>');
    $(`#form_agregar_herramientas`).get(0).reset();
    callbak_activo = (resp) => agregarHerramientaDatabaseNew(resp)
    $('#modal_agregar_herramientas').modal('show');
  });

  $('#beca_incluye').on('change', function () {
    if ($(this).is(':checked')) {
      $('#select_incluye_beca').show('fast')
      $('#select_tipo_apoyo').hide('fast')
      $('#apoyo_solicitado').hide('fast')
      $('#valor_total').hide('fast')
    } else {
      $('#select_incluye_beca').hide('fast')
      $('#select_tipo_apoyo').show('fast')
      $('#apoyo_solicitado').show('fast')
      $('#valor_total').show('fast')
    }
  });

  $("#form_agregar_herramientas").submit((e) => {
    e.preventDefault();
    guardarHerramientasNew();
  });

  $('#detalle_herramientas').click(function () {
    $('#modal_becas_herramientas').modal('show')
    listarHerramientas(id_solicitud)
  })

  $('#detalle_intelectual').click(function () {
    $('#modal_becas_prod_intel').modal('show')
    listarIntelectual(id_solicitud)
  })

  $('#detalle_experiencia').click(function () {
    $('#modal_becas_sector_prod').modal('show')
    listarExperiencia(id_solicitud)
  })

  $('#detalle_entregable').click(function () {
    $('#modal_becas_entregables').modal('show')
    listarEntregable(id_solicitud)
  })

  $('#detalle_plan').click(function () {
    $('#modal_becas_plan_accion').modal('show')
    listarPlanAccion(id_solicitud)
  })

  $('#detalle_concepto').click(function () {
    $('#modal_becas_concepto').modal('show')
    listarConcepto(id_solicitud)
  })

  $("#form_agregar_entregable").submit((e) => {
    e.preventDefault();
    guardarEntregableNew();
  });

  $("#form_agregar_compromisos").submit((e) => {
    e.preventDefault();
    guardarCompromisosNew();
  });
  //END - REFACTORIZACIÓN DE LOS CONCEPTOS

  //BEGIN - REFACTORIZACIÓN DEL VER DETALLE DE ANEXOS
  $('#ver_historial').on('click', function () {
    $('#modal_detalle_estados_solicitud').modal('show');
    listar_estados_solicitud(id_solicitud)
  });

  $('#ver_herramienta').on('click', () => {
    $('#modal_detalle_herramienta_solicitud').modal('show')
    listarSolicitudesBecasHerramientas(id_solicitud)
  });

  $('#ver_plan').on('click', () => {
    $('#modal_detalle_plan_accion_solicitud').modal('show')
    limpiarSeleActividades()
    listarSolicitudBecasPlanAccion(id_solicitud)
  });

  $('#ver_anexo').click(function () {
    $('#modal_enviar_archivos').modal('show')
  });

  $('#cargar_adj_soli').on('click', function () {
    myDropzone.processQueue();
  })

  $('#detalle_anexo').click(function () {
    listar_archivos_adjuntos(id_solicitud)
    $("#modal_detalle_archivos .modal-title").html('<span class="fa fa-list"></span> <span id="text_add_arts">Detalle Archivos Adjuntos</span>');
    $("#modal_detalle_archivos .nombre_tabla").html('Tabla de anexos');
    $('#btn_agregar_certificado').parent().hide()
    $('#modal_detalle_archivos').modal('show')
  })

  $('#ver_entregable').on('click', () => {
    $('#modal_detalle_entregable_solicitud').modal('show')
    limpiarSeleCompromisos();
    listarSolicitudesBecasEntregable(id_solicitud)
  })

  $("#form_agregar_plan_accion").submit((e) => {
    e.preventDefault();
    guardarPlanAccionNew();
  });

  $("#form_agregar_gestion_plan_accion").submit((e) => {
    e.preventDefault();
    guardarActividadesDelPlanAccionNew();
  });
  //END - REFACTORIZACIÓN DE ENTREGABLE CON SUS COMPROMISOS

  //BEGIN DEL AGREGAR ACTIVIDADES AL PLAN DE ACCION
  $('#mas_actividad').on('click', () => {
    if (tipo_guardado === 0) {
      callbak_activo_alt = (resp) => agregarActividadArrayNew(resp)
    } else {
      callbak_activo_alt = (resp) => agregarActividadDatebase(resp)
    }
    $('#modal_agregar_gestion_plan_accion .modal-title').html('<span class="fa fa-wrench"></span> <span id="text_add_arts">Agregar Actividades</span>');
    $('#modal_agregar_gestion_plan_accion').modal('show');
    // callbak_activo_alt = (resp) => agregarActividadDatebase(resp)
  });

  //BEGIN DEL PLAN DE ACCION CON SUS ACTIVIDADES
  $('#ver_actividad_sele').on('click', function () {
    let id = document.getElementById('actividad_asignada').value
    if (id === 'Act_Agre') {
      MensajeConClase('Seleccione una actividad para ver el detalle', 'info', 'Oops.!')
    } else {
      id = parseInt(id);
      let dataDB = actividades.find((key) => { return key.id == id }) || actividades.find((key, index) => { return index == id });
      verDetalleActividad(dataDB)
    }
  })

  $("#modificar_actividad_sele").click(function () {
    let s = document.getElementById('actividad_asignada').value
    if (s === 'Act_Agre') {
      MensajeConClase('Seleccione una actividad para modificarla', 'info', 'Oops.!')
    } else {
      s = parseInt(s);
      let data = actividades.find((key, index) => { return index == s });
      if (tipo_guardado === 0) {
        mostrarActividadPlanAccion(data, s)
      } else {
        let id = document.getElementById('actividad_asignada').value
        let dataDB = actividades.find((key) => { return key.id == id })
        mostrarActividadSolicitud(dataDB)
      }
    }
  })

  $('#retirar_actividad_sele').on('click', function () {
    let del = document.getElementById('actividad_asignada').value
    if (del === 'Act_Agre') {
      MensajeConClase('Seleccione una actividad para eliminar', 'info', 'Oops.!')
    } else {
      if (tipo_guardado === 0) {
        eliminarActividadPlanAccionArray(del)
      } else {
        let id = document.getElementById('actividad_asignada').value
        cambiarEstadoEliminar(id, null, 'becas_plan_accion_gestion', 'La actividad');
      }
    }
  })

  //Modificar el compromiso asociado a una entrega
  $('#ver_compromiso_sele').on('click', function () {
    let id = document.getElementById('compromiso_asignado').value
    if (id === 'Comp_Agre') {
      MensajeConClase('Seleccione un compromiso para ver el detalle', 'info', 'Oops.!')
    } else {
      id = parseInt(id);
      let dataDB = compromisos.find((key) => { return key.id == id }) || compromisos.find((key, index) => { return index == id });
      verDetalleCompromiso(dataDB)
    }
  })

  $('#modificar_compromiso_sele').on('click', function () {
    let comp = document.getElementById('compromiso_asignado').value
    if (comp === 'Comp_Agre') {
      MensajeConClase('Seleccione un compromiso para modificarlo', 'info', 'Oops.!')
    } else {
      comp = parseInt(comp);
      let data = compromisos.find((key, index) => { return index == comp });
      if (tipo_guardado === 0) {
        mostrarCompromisoDelEntregable(data, comp)
      } else {
        let id = document.getElementById('compromiso_asignado').value
        let dataDB = compromisos.find((key) => { return key.id == id })
        mostrarCompromisoSolicitud(dataDB)
      }
    }
  })

  //Elimina el compromiso asociado a una entrega
  $('#retirar_compromiso_sele').on('click', function () {
    let eli_comp = document.getElementById('compromiso_asignado').value
    if (eli_comp === 'Comp_Agre') {
      MensajeConClase('Seleccione un compromiso para eliminar', 'info', 'Oops.!')
    } else {
      if (tipo_guardado === 0) {
        eliminarCompromisoDelEntregableArray(eli_comp)
      } else {
        //cambiarEstadoConceptos(eli_comp, null, 'becas_compromisos', 'El compromiso');
        cambiarEstadoEliminar(eli_comp, id_solicitud, 'becas_compromisos', 'El compromiso');
      }
    }
  })
  //end DEL ENTREGABLE CON SUS COMPROMISOS

  $('#info_soli_inicial').click(function () {
    listarSolicitudesBecas({ 'id': data_solicitud.id_renovacion })
    $('#modal_detalle_solicitud_becas').modal('hide')
  })

  $('#btn_agregar_certificado').click(function () {
    $('#modal_enviar_archivos').modal('show')
  })

  $('#form_finalizar_solicitudes').submit(e => {
    e.preventDefault()
    let fordata = new FormData(document.getElementById('form_finalizar_solicitudes'));
    let data = formDataToJson(fordata);
    cambiar_estado_solicitud(id_solicitud, "Bec_Fina", "Finalizar Solicitud", 1, data.tipo_finalizar);
    $('#modal_finalizar_solicitudes').modal('hide')
  })

  $("#form_seleccion_info_docente").submit((e) => {
    e.preventDefault();
    let id_depa = $("#dep_info_beca").val();
    let id_prog = $("#pro_info_beca").val();
    let id_vinc = $("#vin_info_beca").val();
    guardarSolicitud('', id_depa, id_prog, id_vinc)
  });

});

/*  
INICIO DE LAS FUNCIONES DE LA SOLICITUD
*/
const listarSolicitudesBecas = (data = {}) => {
  let sw = false;
  let tabla = '#tabla_listado_solicitudes_becas'
  let { id, filtro_estado, filtro_fecha_inicio, filtro_fecha_termina, filtro_id_departamento, filtro_id_programa, filtro_id_vinculacion, filtro_persona, filtro_tipo } = data
  if (id > 0 || filtro_estado || filtro_fecha_inicio || filtro_fecha_termina || filtro_id_departamento || filtro_id_programa || filtro_id_vinculacion || filtro_persona || filtro_tipo) sw = true;
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('dblclick', 'tr')
    .off('click', 'tr')
    .off('click', '.formulacion')
    .off('click', '.modificar')
    .off('click', '.cancelar')
    .off('click', '.enviar')
    .off('click', '.revision')
    .off('click', '.finalizar')
    .off('click', '.aprobar')
    .off('click', '.negar')
    .off('click', '.gestionar')
    .off('click', '.gestionar_inve')
    .off('click', '.gestionar_acad')
    .off('click', '.gestionar_secr')
    .off('click', '.correcion')
    .off('click', '.continuar');
  consulta_ajax(`${ruta}listar_solicitudes_becas`, data, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      data: resp,
      columns: [
        {
          data: "ver"
        },
        {
          data: "fullname"
        },
        {
          data: "tipo_solicitud"
        },
        {
          data: "fecha_registro"
        },
        {
          data: "estado_soli"
        },
        {
          data: "acciones"
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
        let { fullname, correo, id_tipo, id_programa_persona } = data;
        data_solicitante = { 'nombre': fullname, correo }
        data_solicitud_noti = { 'tipo_soli': id_tipo, 'programa': id_programa_persona }
        id_solicitud = data.id
        $(this).addClass("warning");
      }
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', async function () {
      let data = myTable.row(this).data();
      if (!data) {
        MensajeConClase('No hay informacion que mostrar', 'info', 'Oops.!')
      } else {
        if (data.id_tipo === 'Soli_Tip_Ren') {
          informacion_ren = await info_solicitud_renovacion(data.id)
          data = informacion_ren;
        }
        data_solicitud = data;
        id_estado = data.id_estado_solicitud
        validar_tipo_ver()
        detalleSolicitud(data)
        $('#modal_detalle_solicitud_becas').modal('show');
      }
    });

    $(`${tabla} tbody`).on('click', '.ver', async function () {
      $('#modal_detalle_solicitud_becas').modal('show');
      let data = myTable.row($(this).parent()).data();
      if (data.id_tipo === 'Soli_Tip_Ren') {
        informacion_ren = await info_solicitud_renovacion(data.id)
        data = informacion_ren;
      }
      data_solicitud = data;
      id_estado = data.id_estado_solicitud
      validar_tipo_ver()
      detalleSolicitud(data)
    });

    $(`${tabla} tbody`).on('click', '.formulacion', async function () {
      let data = myTable.row($(this).parent()).data();
      if (data.id_tipo === 'Soli_Tip_Ren') {
        informacion_ren = await info_solicitud_renovacion(data.id)
        data = informacion_ren;
      }
      id_solicitud = data.id
      informacion_principal = data
      validar_tipo_solicitud()
      $('#model_registro_solicitud_beca').modal('show');
    });

    $(`${tabla} tbody`).on('click', '.enviar', function () {
      let data = myTable.row($(this).parent()).data();
      ValidarRevision(data.id)
    });

    $(`${tabla} tbody`).on('click', '.revision', function () {
      let { id } = myTable.row($(this).parent()).data();
      cambiar_estado_solicitud(id, "Bec_Revi", "Revisar Solicitud", 1);
    });

    $(`${tabla} tbody`).on('click', '.cancelar', function () {
      let { id } = myTable.row($(this).parent()).data();
      cambiar_estado_solicitud(id, "Bec_Canc", "Cancelar Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.aprobar', function () {
      let { id } = myTable.row($(this).parent()).data();
      cambiar_estado_solicitud(id, "Bec_Apro", "Aprobar Solicitud", 1);
    });

    $(`${tabla} tbody`).on('click', '.finalizar', function () {
      let { id } = myTable.row($(this).parent()).data();
      id_solicitud = id
      $('#form_finalizar_solicitudes').get(0).reset()
      $('#modal_finalizar_solicitudes').modal('show')
    });

    $(`${tabla} tbody`).on('click', '.negar', function () {
      let { id } = myTable.row($(this).parent()).data();
      cambiar_estado_solicitud(id, "Bec_Rech", "Rechazar Solicitud", 2);
    });

    //GESTION DECANOS, TALENTO HUMANO , VICE INVESTIGACIÓN, VICE ACADEMICO Y SECRETARIA GENERAL
    $(`${tabla} tbody`).on('click', '.gestionar', function () {
      let data = myTable.row($(this).parent()).data();
      data_solicitud = data;
      $("#modal_gestion_y_estados .modal-title").html('<span class="fa fa-cogs"></span> <span id="text_add_arts">Gestionar Solicitud</span>');
      $('#modal_gestion_y_estados').modal('show');
      gestionar_solicitud(data.id)
      callbak_activo = () => cambiar_estado_solicitud(data.id, "Bec_Vis_Buen", "Aceptar Solicitud", 1);
      callbak_activo_alt = () => cambiar_estado_solicitud(data.id, "Bec_Rech", "Rechazar Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.gestionar_inve', function () {
      let data = myTable.row($(this).parent()).data();
      data_solicitud = data;
      $("#modal_gestion_y_estados .modal-title").html('<span class="fa fa-cogs"></span> <span id="text_add_arts">Gestionar Solicitud</span>');
      $('#modal_gestion_y_estados').modal('show');
      gestionar_solicitud(data.id)
      callbak_activo = () => cambiar_estado_solicitud(data.id, "Bec_Gest_Inve", "Aceptar Solicitud", 1);
      callbak_activo_alt = () => cambiar_estado_solicitud(data.id, "Bec_Rech", "Rechazar Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.gestionar_acad', function () {
      let data = myTable.row($(this).parent()).data();
      data_solicitud = data;
      $("#modal_gestion_y_estados .modal-title").html('<span class="fa fa-cogs"></span> <span id="text_add_arts">Gestionar Solicitud</span>');
      $('#modal_gestion_y_estados').modal('show');
      gestionar_solicitud(data.id)
      callbak_activo = () => cambiar_estado_solicitud(data.id, "Bec_Tram", "Aceptar Solicitud", 1);
      callbak_activo_alt = () => cambiar_estado_solicitud(data.id, "Bec_Rech", "Rechazar Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.gestionar_secr', function () {
      let data = myTable.row($(this).parent()).data();
      data_solicitud = data;
      $("#modal_gestion_y_estados .modal-title").html('<span class="fa fa-cogs"></span> <span id="text_add_arts">Gestionar Solicitud</span>');
      $('#modal_gestion_y_estados').modal('show');
      gestionar_solicitud(data.id)
      callbak_activo = () => cambiar_estado_solicitud(data.id, "Bec_Acep", "Aceptar Solicitud", 1);
      callbak_activo_alt = () => cambiar_estado_solicitud(data.id, "Bec_Rech", "Rechazar Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.correcion', function () {
      let { id } = myTable.row($(this).parent()).data();
      cambiar_estado_solicitud(id, "Bec_Corr", "Enviar a Corrección Solicitud", 2);
    });

    $(`${tabla} tbody`).on('click', '.continuar', async function () {
      let data = myTable.row($(this).parent()).data();
      let estado_anterior = await obtener_ultimo_estado(data.id)
      ValidarRevision(data.id, estado_anterior)
    });
  });
  Con_filtros(sw)
}

const enviar_a_solicitud = (name, title, id) => {
  swal({
    title,
    text: `No puede realizar mas ${name} por el momento, porque ya cuenta con una en proceso, si desea verla, presione la opción de 'Si, Acepto'`,
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: "#D9534F",
    confirmButtonText: "Si, Acepto!",
    cancelButtonText: "No, Cancelar!",
    allowOutsideClick: true,
    closeOnConfirm: true,
    closeOnCancel: true
  },
    function (isConfirm) {
      if (isConfirm) {
        data_filtro = { 'id': id }
        listarSolicitudesBecas(data_filtro)
        administrar_elementos('solicitudes');
      }
    });
}

const cambiar_estado_solicitud = (id, estado, titulo, tipo, tipo_fin = '') => {
  const confirm_normal = (id, estado, title) => {
    swal({
      title,
      text: "Tener en cuenta que, al realizar esta accíon la solicitud sera habilitada para el siguiente  proceso, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
          cambiarEstado(estado, id, '', tipo_fin);
          $('#modal_gestion_y_estados').modal('hide');
        }
      });
  }

  const confirm_input = (id, estado, title) => {
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
      inputPlaceholder: `Observaciones`
    }, function (mensaje) {

      if (mensaje === false)
        return false;
      if (mensaje === "") {
        swal.showInputError(`Debe Ingresar una observación.!`);
      } else {
        cambiarEstado(estado, id, mensaje);
        $('#modal_gestion_y_estados').modal('hide');
      }
    });
  }
  tipo == 1 ? confirm_normal(id, estado, titulo) : confirm_input(id, estado, titulo);
}

const detalleSolicitud = (data, tabla = '#detalle_solicitud_becas') => {
  let {
    admitido_al_programa,
    tipo_duracion_programa,
    year,
    fullname,
    estado_soli,
    fecha_inicio,
    ranking,
    fecha_termina,
    institucion,
    linea_investigacion,
    nivel_formacion,
    pin,
    id,
    programa,
    semestre,
    observaciones,
    tipo_comision,
    beca,
    ciudad_institucion,
    pais_institucion
  } = data
  id_solicitud = id

  tipo_duracion_programa === 'tipo_year' && tipo_duracion_programa !== null ? tipo_duracion_programa = 'Año(s)' : tipo_duracion_programa = 'Semestre(s)'

  $(`${tabla} .solicitante`).html(fullname);
  $(`${tabla} .admitido_al_programa`).html(admitido_al_programa ? admitido_al_programa : 'Vacio');
  $(`${tabla} .duracion_programa`).html(`${semestre ? semestre : 'Vacio'} ${tipo_duracion_programa}`); //
  $(`${tabla} .estado_soli`).html(estado_soli);
  $(`${tabla} .fecha_inicio`).html(fecha_inicio ? fecha_inicio : 'Vacio');
  $(`${tabla} .ranking`).html(ranking ? ranking : 'Vacio');
  $(`${tabla} .fecha_termina`).html(fecha_termina ? fecha_termina : 'Vacio');
  $(`${tabla} .institucion`).html(institucion ? institucion : 'Vacio');
  $(`${tabla} .pais_institucion`).html(pais_institucion ? pais_institucion : 'Vacio');
  $(`${tabla} .ciudad_institucion`).html(ciudad_institucion ? ciudad_institucion : 'Vacio');
  $(`${tabla} .linea_investigacion`).html(linea_investigacion ? linea_investigacion : 'Vacio');
  $(`${tabla} .nivel_formacion`).html(nivel_formacion ? nivel_formacion : 'Vacio');
  $(`${tabla} .pin`).html(pin ? pin : 'Vacio');
  $(`${tabla} .programa`).html(programa ? programa : 'Vacio');
  $(`${tabla} .observacion`).html(observaciones ? observaciones : 'Vacio');
  $(`${tabla} .comision_de_estudio`).html(tipo_comision ? tipo_comision : 'Vacio');
  $(`${tabla} .tener_beca`).html(`${beca ? beca : 'No aplica'}`);
  $(`${tabla} .semestre`).html(`${year ? year : 'Vacio'} ${tipo_duracion_programa}`); //
  $(`${tabla} .tipo_duracion_programa`).html(`${tipo_duracion_programa}`);
}

/**/
const mostrarOpcionesConceptos = (soli, data) => {
  mostrarConceptoSolicitud(data)
  $("#beca_incluye").attr('disabled', true)
  $('#select_incluye_beca').hide('fast')

  if (soli.id_comision === 'Bec_Com_C' && soli.id_beca == 'Bec_Beca_S') {
    if (data.id_concepto) {
      $('#select_tipo_apoyo').show('fast')
      $('#valor_total').show('fast')
      $('#apoyo_solicitado').show('fast')
      $('#select_incluye_beca').hide('fast')
    } else {
      $('#select_incluye_beca').show('fast')
      $('#select_tipo_apoyo').hide('fast')
      $('#valor_total').hide('fast')
      $('#apoyo_solicitado').hide('fast')
    }
  }
}
/**/
const consulta_solicitud_id = id => {
  return new Promise(resolve => {
    let url = `${ruta}consulta_solicitud_id`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}

const cambiarEstadoEliminar = (id, id_solicitud, tabla, mensaje) => {
  swal({
    title: "Estas Seguro ?",
    text: `${mensaje} será eliminado`,
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
        limpiarSeleActividades();
        limpiarSeleCompromisos();
        consulta_ajax(`${ruta}cambiar_estado_eliminar`, { id, tabla, id_solicitud }, resp => {
          let { titulo, mensaje, tipo } = resp;
          if (tipo == "success") {
            if (tabla === 'becas_concepto') {
              listarSolicitudesBecasConcepto(id_solicitud);
            } else if (tabla === 'becas_manejo_tech') {
              listarSolicitudesBecasHerramientas(id_solicitud)
            } else if (tabla === 'becas_prod_intelectual') {
              listarSolicitudesBecasProdIntelectual(id_solicitud)
            } else if (tabla === 'becas_sector_productivo') {
              listarSolicitudesBecasExperiencia(id_solicitud)
            } else if (tabla === 'becas_compromiso_entregable') {
              listarSolicitudesBecasEntregable(id_solicitud)
            } else if (tabla === 'becas_plan_accion') {
              listarSolicitudBecasPlanAccion(id_solicitud)
            } else if (tabla === 'becas_plan_accion_gestion') {
              listarSolicitudBecasActividades(id_plan_ent)
            } else if (tabla === 'becas_compromisos') {
              listarSolicitudesBecasCompromisos(id_plan_ent)
            }
          }
          MensajeConClase(mensaje, tipo, titulo);
        })
      }
    }
  )
}

const cargar_estados = (id_parametro) => {
  return new Promise(resolve => {
    let url = `${ruta}cargar_estados`;
    consulta_ajax(url, { id_parametro }, resp => {
      resolve(resp);
    });
  });
}

const validar_cantidad_de_solicitud = () => {
  return new Promise(resolve => {
    let url = `${ruta}validar_cantidad_de_solicitud`;
    consulta_ajax(url, {}, resp => {
      inicial = resp.inicial
      renovar = resp.renovar
      resolve(resp);
    });
  });
}

const solicitud_a_renovar = () => {
  return new Promise(resolve => {
    let url = `${ruta}solicitud_a_renovar`;
    consulta_ajax(url, {}, resp => {
      resolve(resp);
    });
  });
}

const info_solicitud_renovacion = (id) => {
  return new Promise(resolve => {
    let url = `${ruta}info_solicitud_renovacion`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}

const obtener_personas_permisos = (tipo_soli, estado, programa) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_personas_permisos`;
    consulta_ajax(url, { tipo_soli, estado, programa }, resp => {
      resolve(resp);
    });
  });
}

const validar_revisiones = (id) => {
  return new Promise(resolve => {
    let url = `${ruta}validar_revisiones`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}

const obtener_ultimo_estado = (id) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_ultimo_estado`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}

const Cargar_Parametro_estados_solicitud = async (idparametro, combo, mensaje, sele = '') => {
  let estados = await cargar_estados(idparametro);
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  estados.forEach(element => {
    $(combo).append(
      `<option value='${element.id_aux}'> ${element.valor} </option>`
    );
  });
  $(combo).val(sele);
}

const number_sin_punto = () => {
  const number = document.querySelector('.valor_total_sin_punto');
  const apoyo_sol = document.querySelector('.apoyo_sin_punto');

  const formatNumber = (n) => {
    n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
  }

  number.addEventListener('keyup', (e) => {
    const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
  });

  apoyo_sol.addEventListener('keyup', (e) => {
    const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
  });
}
/*  
FIN DE LAS FUNCIONES AUXILIARES
*/

/*
INICIO DEL PLAN DE ACCIÓN Y SUS ACTIVIDADES
*/
const guardarPlanAccionNew = () => {
  let formulario = 'form_agregar_plan_accion'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  data.id = id_solicitud
  data.actividades = actividades
  consulta_ajax(`${ruta}agregar_plan_accion_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data);
      $(`#${formulario}`).get(0).reset()
    }
    MensajeConClase(mensaje, tipo, titulo)
  })
}

const guardarActividadesDelPlanAccionNew = () => {
  let formulario = 'form_agregar_gestion_plan_accion'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  consulta_ajax(`${ruta}agregar_plan_gestion_accion_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo_alt(data);
      $(`#${formulario}`).get(0).reset()
    }
    MensajeConClase(mensaje, tipo, titulo)
  })
}

const listarPlanAccion = (id_solicitud, tabla = '#tabla_becas_plan_accion') => {
  $(`${tabla} tbody`)
    .off('click', '.plan_actividades')
  consulta_ajax(`${ruta}detalle_plan_btn_ver`, { id_solicitud }, resp => {
    let i = 0
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
          data: "meta"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.plan_actividades', function () {
      let data = myTable.row($(this).parents('tr')).data();
      listarActividades(data.id);
      $('#modal_becas_actividades').modal('show')
    });
  })
}

const listarActividades = (id_plan, tabla = '#tabla_becas_actividades') => {
  consulta_ajax(`${ruta}detalle_actividades`, { id_plan }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        { data: "actividad" },
        { data: "recurso" },
        { data: "fecha_inicio" },
        { data: "fecha_final" }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}

const listarActividadesDelPlanAccion = (actividades) => {
  $('#actividad_asignada').html(`<option selected disabled id="informacion_actividad" value="Act_Agre"> ${actividades.length} Actividad(es) </option>`)
  actividades.map((i, index) => {
    $('#actividad_asignada').append(`<option value='${index}'> ${i.actividad_gestion_plan_accion} </option>`)
  })
}

const agregarActividadArrayNew = data => {
  actividades.push(data);
  listarActividadesDelPlanAccion(actividades);
}

const modificarActividadArrayNew = data => {
  actividades.splice(data.id, 1);
  agregarActividadArrayNew(data);
  $('#modal_agregar_gestion_plan_accion').modal('hide')
}

const mostrarActividadPlanAccion = (data, s) => {
  let {
    actividad_gestion_plan_accion,
    fecha_finalizacion_gestion_plan_accion,
    fecha_inicio_gestion_plan_accion,
    recurso_gestion_plan_accion
  } = data

  $('#actividad_gestion_plan_accion').val(actividad_gestion_plan_accion)
  $('#fecha_finalizacion_gestion_plan_accion').val(fecha_finalizacion_gestion_plan_accion)
  $('#fecha_inicio_gestion_plan_accion').val(fecha_inicio_gestion_plan_accion)
  $('#recurso_gestion_plan_accion').val(recurso_gestion_plan_accion)

  callbak_activo_alt = (resp) => {
    resp.id = s;
    modificarActividadArrayNew(resp);
  }
  $("#modal_agregar_gestion_plan_accion .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Actividad</span>');
  $('#modal_agregar_gestion_plan_accion').modal('show')
}

const eliminarActividadPlanAccionArray = (act) => {
  swal({
    title: "Estas Seguro ?",
    text: "Esta actividad será eliminado",
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
        let data = actividades.filter((key, index) => index != act);
        actividades = data
        if (sw) {
          listarActividadesDelPlanAccion(actividades);
          swal.close();
        } else {
          MensajeConClase("El plan no fue encontrado, intente de nuevo.", "info", 'Oops.!');
        }
        return;
      }
    });
}

const agregarPlanDatabaseNew = (data) => {
  let formulario = '#form_agregar_plan_accion'
  let modal = '#modal_agregar_plan_accion'
  consulta_ajax(`${ruta}agregar_plan_accion_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudBecasPlanAccion(data.id);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const agregarActividadDatebase = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_gestion_plan_accion'
  let modal = '#modal_agregar_gestion_plan_accion'
  consulta_ajax(`${ruta}agregar_actividad_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      limpiarSeleActividades()
      listarSolicitudBecasActividades(data.id);
    }
    MensajeConClase(mensaje, tipo, titulo);
    $(`${modal}`).modal("hide");
  });
}

const modificarActividadSolicitud = (data) => {
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_gestion_plan_accion'
  let modal = '#modal_agregar_gestion_plan_accion'
  consulta_ajax(`${ruta}modificar_actividad_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      limpiarSeleActividades();
      listarSolicitudBecasActividades(data.id_plan);
    }
    MensajeConClase(mensaje, tipo, titulo);
    $(`${modal}`).modal("hide");
  });
}

const mostrarActividadSolicitud = data => {
  let {
    actividad,
    recurso,
    fecha_final,
    fecha_inicio
  } = data

  $('#actividad_gestion_plan_accion').val(actividad)
  $('#fecha_finalizacion_gestion_plan_accion').val(fecha_final)
  $('#fecha_inicio_gestion_plan_accion').val(fecha_inicio)
  $('#recurso_gestion_plan_accion').val(recurso)

  callbak_activo_alt = (resp) => {
    resp.id = data.id;
    resp.id_plan = data.id_plan
    modificarActividadSolicitud(resp);
  }
  $("#modal_agregar_gestion_plan_accion .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Actividad</span>');
  $('#modal_agregar_gestion_plan_accion').modal('show')
}

const mostrarPlanAccionsSolicitud = (data) => {
  callbak_activo = dataMod => {
    modificarPlanAccionSolicitud(dataMod)
  }

  let { meta } = data

  $('#meta_plan_accion').val(meta)

  $("#modal_agregar_plan_accion .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Meta</span>');
  $('#modal_agregar_plan_accion').modal('show')
}

const modificarPlanAccionSolicitud = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_plan_accion'
  let modal = '#modal_agregar_plan_accion'
  consulta_ajax(`${ruta}modificar_plan_accion_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudBecasPlanAccion(data.id_solicitud);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const listarSolicitudBecasPlanAccion = (id_solicitud) => {
  let tabla = '#tabla_becas_plan_accion_detalle'
  $(`${tabla} tbody`)
    .off('click', '.modificar_plan_sol')
    .off('click', '.eliminar_plan')
    .off('click', '.plan_actividades')

  consulta_ajax(`${ruta}detalle_plan_accion`, { id_solicitud }, resp => {
    let i = 0
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
          data: "meta"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.modificar_plan_sol', function () {
      let data = myTable.row($(this).parents('tr')).data();
      tipo_guardado = 1;
      plan_accion = data
      listarSolicitudBecasActividades(data.id)
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      id_plan_ent = data.id
      limpiarSeleActividades()
      mostrarPlanAccionsSolicitud(data)
      mostrarBtnsPlanEntregable(id_estado)
    });

    $(`${tabla} tbody`).on('click', '.eliminar_plan', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_plan_accion';
      mensaje = 'La meta';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });

    $(`${tabla} tbody`).on('click', '.plan_actividades', function () {
      let data = myTable.row($(this).parents('tr')).data();
      ocultarBtnsPlanEntregable(id_estado);
      listarSolicitudBecasActividades(data.id)
      limpiarSeleActividades()
      mostrarPlanAccionsSolicitud(data)
    });
  });
}

const listarSolicitudBecasActividades = (id_plan) => {
  consulta_ajax(`${ruta}detalle_actividades`, { id_plan }, resp => {
    $('#informacion_actividad').html(`<option selected disabled value="Act_Agre"> ${resp.length} Actividad(es) </option>`);
    actividades = resp
    resp.map(i => {
      $('#actividad_asignada').append(`<option value='${i.id}'> ${i.actividad} </option>`)
    })
  })
}

const verDetalleActividad = (data, tabla = "#detalle_solicitud_actividad") => {
  $(`${tabla} .detalle_actividad`).html(data.actividad || data.actividad_gestion_plan_accion);
  $(`${tabla} .detalle_recurso`).html(data.recurso || data.recurso_gestion_plan_accion);
  $(`${tabla} .detalle_fecha_i`).html(data.fecha_inicio || data.fecha_inicio_gestion_plan_accion);
  $(`${tabla} .detalle_fecha_f`).html(data.fecha_final || data.fecha_finalizacion_gestion_plan_accion);

  $('#modal_detalle_solicitud_actividades').modal('show');
}
/*
FIN DEL PLAN DE ACCIÓN Y SUS ACTIVIDADES
*/

/*
INICIO DEL ENTREGABLE Y SUS RESPECTIVOS COMPROMISOS
*/
const guardarEntregableNew = () => {
  let formulario = 'form_agregar_entregable'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  data.compromisos = compromisos
  consulta_ajax(`${ruta}agregar_entregable_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data)
      $(`#${formulario}`).get(0).reset()
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const guardarCompromisosNew = () => {
  let formulario = 'form_agregar_compromisos'
  let modal = 'modal_agregar_compromisos'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  consulta_ajax(`${ruta}agregar_compromisos_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo_alt(data)
      $(`#${formulario}`).get(0).reset();
      $(`#${modal}`).modal("hide");
    }
    MensajeConClase(mensaje, tipo, titulo)
  })
}

const agregarCompromisoArrayNew = data => {
  compromisos.push(data);
  listarCompromisosDelEntregable(compromisos)
}

const listarEntregable = (id_solicitud, tabla = '#tabla_becas_entregable') => {
  $(`${tabla} tbody`)
    .off('click', '.entregable_compromisos')
  consulta_ajax(`${ruta}detalle_entregables_btn_ver`, { id_solicitud }, resp => {
    let i = 0
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
          data: "entregable"
        },
        {
          data: "producto"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.entregable_compromisos', function () {
      let data = myTable.row($(this).parents('tr')).data();
      listarCompromisos(data.id)
      $('#modal_becas_compromisos').modal('show')
    });
  })
}

const listarCompromisos = (id_entrega, tabla = '#tabla_becas_compromiso') => {
  consulta_ajax(`${ruta}detalle_compromisos`, { id_entrega }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        { data: "compromiso" },
        { data: "year" },
        { data: "periodo" }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}

const listarCompromisosDelEntregable = (compromisos) => {
  $('#compromiso_asignado').html(`<option selected disabled id="informacion_compromiso" value="Comp_Agre"> ${compromisos.length} Compromiso(s) </option>`)
  compromisos.map((i, index) => {
    $('#compromiso_asignado').append(`<option value='${index}'> ${i.year_compromiso} - ${i.fecha_periodo} </option>`)
  })
}

const mostrarCompromisoDelEntregable = (data, c) => {
  let {
    year_compromiso,
    fecha_periodo,
    compromiso_descripcion
  } = data

  $('#year_compromiso').val(year_compromiso)
  $('#fecha_periodo').val(fecha_periodo)
  $('#compromiso_descripcion').val(compromiso_descripcion)

  callbak_activo_alt = (resp) => {
    resp.id = c;
    modificarCompromisoArrayNew(resp);
  }
  $("#modal_agregar_compromisos .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Compromiso</span>');
  $('#modal_agregar_compromisos').modal('show')
}

const modificarCompromisoArrayNew = (data) => {
  compromisos.splice(data.id, 1);
  agregarCompromisoArrayNew(data);
  $('#modal_agregar_compromisos').modal('hide')
}

const eliminarCompromisoDelEntregableArray = (comp) => {
  swal({
    title: "Estas Seguro ?",
    text: "Esta compromiso será eliminado",
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
        let data = compromisos.filter((key, index) => index != comp);
        compromisos = data
        if (sw) {
          listarCompromisosDelEntregable(compromisos);
          swal.close();
        } else {
          MensajeConClase("El compromiso no fue encontrado, intente de nuevo.", "info", 'Oops.!');
        }
        return;
      }
    });
}

const agregarEntregableDatabaseNew = (data) => {
  data.id = id_solicitud
  let formulario = '#form_agregar_entregable'
  let modal = '#modal_agregar_entregable'
  consulta_ajax(`${ruta}agregar_entregable_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudesBecasEntregable(data.id);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const mostrarEntregablesSolicitud = (data) => {
  callbak_activo = dataMod => {
    modificarEntregableSolicitud(dataMod)
  }

  let { producto, entregable } = data

  $('#producto_entregable').val(producto)
  $('#compromiso_entregable').val(entregable)

  $("#modal_agregar_entregable .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Entregable</span>');
  $('#modal_agregar_entregable').modal('show')
}

const modificarEntregableSolicitud = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_entregable'
  let modal = '#modal_agregar_entregable'
  consulta_ajax(`${ruta}modificar_entregable_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasEntregable(data.id_solicitud);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const agregarCompromisoDatebase = data => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_compromisos'
  let modal = '#modal_agregar_compromisos'
  consulta_ajax(`${ruta}agregar_compromiso_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      limpiarSeleCompromisos();
      listarSolicitudesBecasCompromisos(data.id);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const modificarCompromisoSolicitud = (data) => {
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_compromisos'
  let modal = '#modal_agregar_compromisos'
  consulta_ajax(`${ruta}modificar_compromiso_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      limpiarSeleCompromisos();
      listarSolicitudesBecasCompromisos(data.id_entregable);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const mostrarCompromisoSolicitud = data => {
  let {
    year,
    compromiso,
    periodo
  } = data

  $('#year_compromiso').val(year)
  $('#fecha_periodo').val(periodo)
  $('#compromiso_descripcion').val(compromiso)

  callbak_activo_alt = (resp) => {
    resp.id = data.id;
    resp.id_entregable = data.id_entregable
    modificarCompromisoSolicitud(resp);
  }
  $("#modal_agregar_compromisos .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Compromiso</span>');
  $('#modal_agregar_compromisos').modal('show')
}

const listarSolicitudesBecasEntregable = (id_solicitud) => {
  let tabla = '#tabla_becas_compromiso_detalle'
  $(`${tabla} tbody`)
    .off('click', '.eliminar_entregable')
    .off('click', '.modificar_entregable_sol');
  consulta_ajax(`${ruta}detalle_entregables`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "producto"
        },
        {
          data: "entregable"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.modificar_entregable_sol', function () {
      let data = myTable.row($(this).parents('tr')).data();
      tipo_guardado = 1;
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      id_plan_ent = data.id
      limpiarSeleCompromisos()
      listarSolicitudesBecasCompromisos(data.id)
      mostrarEntregablesSolicitud(data)
      mostrarBtnsPlanEntregable(id_estado)
    });

    $(`${tabla} tbody`).on('click', '.eliminar_entregable', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_compromiso_entregable';
      mensaje = 'El entregable';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });
  })
}

const listarSolicitudesBecasCompromisos = (id_entrega) => {
  consulta_ajax(`${ruta}detalle_compromisos`, { id_entrega }, resp => {
    $('#informacion_compromiso').html(`<option selected disabled value="Comp_Agre"> ${resp.length} Compromiso(s) </option>`);
    compromisos = resp
    resp.map(i => {
      $('#compromiso_asignado').append(`<option value='${i.id}'> ${i.year} - ${i.periodo} </option>`)
    })
  });
}

const verDetalleCompromiso = (data, tabla = '#detalle_solicitud_compromiso') => {
  $(`${tabla} .detalle_compromiso`).html(data.compromiso || data.compromiso_descripcion);
  $(`${tabla} .detalle_year`).html(data.year || data.year_compromiso);
  $(`${tabla} .detalle_periodo`).html(data.periodo || data.fecha_periodo);

  $('#modal_detalle_solicitud_compromisos').modal('show');
}
/*
FIN DEL ENTREGABLE Y SUS RESPECTIVOS COMPROMISOS
*/

/*
INICIO DE LAS HERRAMIENTAS
*/
const guardarHerramientasNew = () => {
  let formulario = 'form_agregar_herramientas'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  consulta_ajax(`${ruta}agregar_herramientas_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data);
      $(`#${formulario}`).get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  })
}

const agregarHerramientaDatabaseNew = data => {
  data.id = id_solicitud
  let formulario = '#form_agregar_herramientas'
  let modal = '#modal_agregar_herramientas'
  consulta_ajax(`${ruta}agregar_herramientas_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasHerramientas(data.id);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const modificarHerramientaSolicitud = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_herramientas'
  let modal = '#modal_agregar_herramientas'
  consulta_ajax(`${ruta}modificar_herramienta_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasHerramientas(data.id_solicitud);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const mostrarHerramientasSolicitud = (data) => {
  callbak_activo = (dataMod) => {
    modificarHerramientaSolicitud(dataMod);
  }

  let { nombre, hora_implementacion, descripcion } = data

  $('#nombre_herramienta').val(nombre)
  $('#descripcion_herramienta').val(descripcion)
  $('#horas_formacion').val(hora_implementacion)
}
// lista
const listarSolicitudesBecasHerramientas = (id_solicitud) => {
  let tabla = '#tabla_becas_herramientas_detalle'
  $(`${tabla} tbody`)
    .off('click', '.eliminar_herramienta')
    .off('click', '.modificar_herramienta_sol');

  consulta_ajax(`${ruta}detalle_herramientas`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "nombre"
        },
        {
          data: "descripcion"
        },
        {
          data: "hora_implementacion"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.eliminar_herramienta', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_manejo_tech';
      mensaje = 'La herramienta';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });

    $(`${tabla} tbody`).on('click', '.modificar_herramienta_sol', function () {
      let data = myTable.row($(this).parents('tr')).data();
      $("#modal_agregar_herramientas .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Herramienta</span>');
      $('#modal_agregar_herramientas').modal('show');
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      mostrarHerramientasSolicitud(data)
    });
  })
}

// lista
const listarHerramientas = (id_solicitud, tabla = '#tabla_becas_herramientas') => {
  consulta_ajax(`${ruta}detalle_herramientas`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      "destroy": true,
      "processing": true,
      data: resp,
      columns: [
        {
          data: 'nombre'
        },
        {
          data: 'descripcion'
        },
        {
          data: 'hora_implementacion'
        },
      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });
  })
}
/*
FIN DE LAS HERRAMIENTAS
*/

/*
INICIO DE LA PRODUCCIÓN INTELECTUAL
*/

const guardarIntelectual = () => {
  let formulario = 'form_agregar_prod_intel'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  let nombreProducto = $(`#${formulario} select[name="producto_intel"] option:selected`).text();
  data.nombreProducto = nombreProducto
  consulta_ajax(`${ruta}agregar_prod_intel_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data);
      $(`#${formulario}`).get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  })
}

// lista
const agregarIntelectualDatabaseNew = (data) => {
  data.id = id_solicitud
  let formulario = '#form_agregar_prod_intel'
  let modal = '#modal_agregar_prod_intel'
  consulta_ajax(`${ruta}agregar_intelectual_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasProdIntelectual(data.id);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const modificarIntelectualSolicitud = data => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_prod_intel'
  let modal = '#modal_agregar_prod_intel'
  consulta_ajax(`${ruta}modificar_intelectual_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasProdIntelectual(data.id_solicitud);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const mostrarIntelectualSolicitud = data => {
  callbak_activo = (dataMod) => {
    modificarIntelectualSolicitud(dataMod);
  }

  let { entidad_publicacion, fecha_publicacion, id_producto, nombre_producto } = data

  $('#producto_intel').val(id_producto)
  $('#nombre_prod_intel').val(nombre_producto)
  $('#entidad_prod_intel').val(entidad_publicacion)
  $('#fecha_finalizacion').val(fecha_publicacion)
}
// lista
const listarSolicitudesBecasProdIntelectual = (id_solicitud) => {
  let tabla = '#tabla_becas_prod_intelectual_detalle'
  $(`${tabla} tbody`)
    .off('click', '.eliminar_intelectual')
    .off('click', '.modificar_intelectual_sol');
  consulta_ajax(`${ruta}detalle_produccion_intelectual`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "nombreProducto"
        },
        {
          data: "nombre_producto"
        },
        {
          data: "entidad_publicacion"
        },
        {
          data: "fecha_publicacion"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.eliminar_intelectual', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_prod_intelectual';
      mensaje = 'El producto intelectual';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });

    $(`${tabla} tbody`).on('click', '.modificar_intelectual_sol', function () {
      let data = myTable.row($(this).parents('tr')).data();
      $("#modal_agregar_prod_intel .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Producción Intelectual</span>');
      $('#modal_agregar_prod_intel').modal('show');
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      mostrarIntelectualSolicitud(data)
    });
  })
}

const listarIntelectual = (id_solicitud, tabla = '#tabla_becas_prod_intel') => {
  consulta_ajax(`${ruta}detalle_produccion_intelectual`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "nombreProducto"
        },
        {
          data: "nombre_producto"
        },
        {
          data: "entidad_publicacion"
        },
        {
          data: "fecha_publicacion"
        },
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}
/*
FIN DE LA PRODUCCIÓN INTELECTUAL
*/

/*
INICIO DEL SECTOR PRODUCTIVO
*/
const guardarExperiencia = () => {
  let formulario = 'form_agregar_sector_prod'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  consulta_ajax(`${ruta}agregar_sector_prod_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data)
      $(`#${formulario}`).get(0).reset()
    }
    MensajeConClase(mensaje, tipo, titulo)
  })
}

const listarSolicitudesBecasExperiencia = (id_solicitud) => {
  let tabla = '#tabla_becas_exp_detalle'
  $(`${tabla} tbody`)
    .off('click', '.eliminar_exp')
    .off('click', '.modificar_exp_sol');
  consulta_ajax(`${ruta}detalle_experiencia_sector`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "area_general"
        },
        {
          data: "area_especifica"
        },
        {
          data: "entidad"
        },
        {
          data: "year_exp"
        },
        {
          data: "acciones"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', '.eliminar_exp', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_sector_productivo';
      mensaje = 'La experiencia';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });

    $(`${tabla} tbody`).on('click', '.modificar_exp_sol', function () {
      let data = myTable.row($(this).parents('tr')).data();
      $("#modal_agregar_sector_prod .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Experiencia</span>');
      $('#modal_agregar_sector_prod').modal('show');
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      mostrarExperienciaSolicitud(data)
    });
  })
}

const listarExperiencia = (id_solicitud, tabla = '#tabla_becas_sector_prod') => {
  consulta_ajax(`${ruta}detalle_experiencia_sector`, { id_solicitud }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "area_general"
        },
        {
          data: "area_especifica"
        },
        {
          data: "entidad"
        },
        {
          data: "year_exp"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}
// lista
const agregarExperienciaDatabaseNew = (data) => {
  data.id = id_solicitud
  let modal = '#modal_agregar_sector_prod'
  let formulario = 'form_agregar_sector_prod'
  consulta_ajax(`${ruta}agregar_experiencia_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`#${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasExperiencia(data.id);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const modificarExperienciaSolicitud = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = '#form_agregar_sector_prod'
  let modal = '#modal_agregar_sector_prod'
  consulta_ajax(`${ruta}modificar_experiencia_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`${formulario}`)
        .get(0)
        .reset();
      listarSolicitudesBecasExperiencia(data.id_solicitud);
    }
    $(`${modal}`).modal("hide");
    MensajeConClase(mensaje, tipo, titulo);
  });
}
// lista
const mostrarExperienciaSolicitud = data => {
  callbak_activo = (dataMod) => {
    modificarExperienciaSolicitud(dataMod)
  }

  let { area_general, area_especifica, entidad, year_exp } = data

  $('#area_general_sector_prod').val(area_general)
  $('#area_especifica_sector_prod').val(area_especifica)
  $('#entidad_sector_prod').val(entidad)
  $('#year_sector_prod').val(year_exp)
}
/*
FIN DEL SECTOR PRODUCTIVO
*/

/** INICIO DE FUNCIONE LISTAS */
const guardarBorrador = (id_ren, id_depa, id_prog, id_vinc) => {
  consulta_ajax(`${ruta}guardarBorrador`, { id_ren, id_depa, id_prog, id_vinc }, resp => {
    let { titulo, mensaje, tipo, solicitud } = resp;
    if (tipo == "success") {
      id_solicitud = solicitud.id
      informacion_principal = solicitud;
      data_solicitante = { 'nombre': informacion_principal.fullname, 'correo': informacion_principal.correo }
      validar_tipo_solicitud()
      $("#modal_seleccion_info_docente").modal('hide');
      $('#model_registro_solicitud_beca').modal('show');
      validar_cantidad_de_solicitud()
      mostrar_solicitudes_notificaciones();
      enviar_correo_estado('Bec_Form', solicitud.id, '');
      listarSolicitudesBecas()
    } else if (tipo == 'sin_info_docente'){
      Cargar_parametro_buscado(91, '.cbx__dep_info', 'Seleccione su departamento');
      Cargar_parametro_buscado(86, '.cbx__pro_info', 'Seleccione su programa');
      Cargar_parametro_buscado(93, '.cbx__vin_info', 'Seleccione su tipo de vinculacion');
      $("#modal_seleccion_info_docente").modal();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}
// lista
const guardarSolicitud = (id_ren = '', id_depa = null, id_prog = null, id_vinc = null) => {
  swal({
    title: "Estas Seguro?",
    text: "Tener en cuenta que, al realizar esta acción se creara una nueva solicitud, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
        guardarBorrador(id_ren, id_depa, id_prog, id_vinc);
        swal.close();
      }
    });
}
/** FIN DE FUNCIONE LISTAS */

const guardarInformacionPrincipal = () => {
  $('#id_comision_estudio').attr('disabled', false)
  $('#id_beca').attr('disabled', false)
  let formulario = 'form_solicitud_beca'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  let modal = '#modal_informacion_principal'
  data.id = id_solicitud
  consulta_ajax(`${ruta}agregar_solicitud_validacion`, data, resp => {
    let { titulo, mensaje, tipo, solicitud } = resp;
    informacion_principal = solicitud;
    if (tipo === "success") {
      $(`#${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudesBecas()
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const mostrarInformacionPrincipalAgregada = (data) => {

  let { admitido_al_programa, fecha_inicio, fecha_termina, id_duracion, id_nivel_formacion, id_semestre, institucion, linea_investigacion, pin, programa, ranking, tipo_duracion_programa, id_comision, id_beca, ciudad_institucion, pais_institucion } = data

  if (id_comision) {
    $('#id_comision_estudio').attr('disabled', true)
  } else {
    $('#id_comision_estudio').attr('disabled', false)
  }

  if (id_beca) {
    $('#id_beca').attr('disabled', true)
    $('#id_beca').val(id_beca)
    $('#id_beca').show('fast')
  } else {
    $('#id_beca').attr('disabled', false)
    $('#id_beca').hide('fast')
  }

  $('#programa').val(programa)
  $('#nivel_formacion').val(id_nivel_formacion)
  $('#tipo_year').val(tipo_duracion_programa)
  $('#institucion').val(institucion)
  $('#pais_insti').val(pais_institucion)
  $('#ciudad_insti').val(ciudad_institucion)
  $('#fecha_inicio').val(fecha_inicio)
  $('#fecha_fin').val(fecha_termina)
  $('#id_semestre').val(id_semestre)
  $('#id_year').val(id_duracion)
  $('#pin').val(pin)
  $('#ranking').val(ranking)
  $('#linea_investigacion').val(linea_investigacion)
  $('#id_comision_estudio').val(id_comision)
  admitido_al_programa === 'si' ? $("#admitido").prop('checked', true) : $("#no_dmitido").prop('checked', true)
}

const validarOpciones = async data => {
  let comision = data.id_comision
  let beca = data.id_beca
  if (comision == null) {
    MensajeConClase('Seleccionar su tipo de comision (Informacion Principal)', 'info', 'Ooops!')
  } else if (comision === 'Bec_Com_C') {
    if (beca === null) {
      MensajeConClase('Seleccionar si tiene beca (Informacion Principal)', 'info', 'Ooops!')
    } else {
      $('#modal_detalle_concepto_solicitud').modal('show')
    }
  }
  if (comision === 'Bec_Com_C' && beca === 'Bec_Beca_S') {
    let concepto = await obtener_valores_permiso(comision, 197, 2);
    pintar_datos_combo_general(concepto, '.cbx_tipo_apoyo', 'Seleccione apoyo a solicitar');
    $('#container_beca').show('fast')
    $('#select_tipo_apoyo').show('fast')
    $('#valor_total').show('fast')
    $('#apoyo_solicitado').show('fast')
    $('#modal_detalle_concepto_solicitud').modal('show')
  } else if (comision === 'Bec_Com_C' && beca === 'Bec_Beca_N') {
    let concepto = await obtener_valores_permiso(comision, 197, 2);
    pintar_datos_combo_general(concepto, '.cbx_tipo_apoyo', 'Seleccione apoyo a solicitar');
    $('#container_beca').hide('fast')
    $('#select_tipo_apoyo').show('fast')
    $('#valor_total').show('fast')
    $('#apoyo_solicitado').show('fast')
    $('#modal_detalle_concepto_solicitud').modal('show')
  } else if (comision === 'Bec_Com_S') {
    let concepto = await obtener_valores_permiso(comision, 197, 2);
    pintar_datos_combo_general(concepto, '.cbx_tipo_apoyo', 'Seleccione apoyo a solicitar');
    $('#container_beca').hide('fast')
    $('#valor_total').show('fast')
    $('#apoyo_solicitado').show('fast')
    $('#modal_detalle_concepto_solicitud').modal('show')
  }
}

const guardarConceptosNew = () => {
  $('#select_tipo_apoyo').attr('disabled', false)
  $("#beca_incluye").attr('disabled', false)
  let formulario = 'form_agregar_conceptos'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  let nombreIncluyeBeca = $(`#${formulario} select[name="incluye_beca"] option:selected`).text();
  let nombreTipoApoyo = $(`#${formulario} select[name="tipo_apoyo"] option:selected`).text();
  data.nombreTipoApoyo = data.incluye_beca !== '' ? '' : nombreTipoApoyo
  data.nombreIncluyeBeca = data.incluye_beca !== '' ? nombreIncluyeBeca : ''
  data.nombreIncluye = data.beca_incluye === "si" ? 'SI' : 'NO'
  data.valorTotal = data.valor_total.replace(/\W/g, '')
  data.apoyoSolicitado = data.apoyo_solicitado.replace(/\W/g, '')
  consulta_ajax(`${ruta}agregar_conceptos_validacion`, data, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') {
      callbak_activo(data);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  })
}

const AgregarConceptoSolicitud = (data) => {
  let formulario = 'form_agregar_conceptos'
  let modal = '#modal_agregar_conceptos'
  data.id_solicitud = id_solicitud
  consulta_ajax(`${ruta}agregar_concepto_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`#${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudesBecasConcepto(data.id_solicitud);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const modificarConceptoSolicitud = (data) => {
  data.id = id_anexo
  data.id_solicitud = id_solicitud
  let formulario = 'form_agregar_conceptos'
  let modal = '#modal_agregar_conceptos'
  consulta_ajax(`${ruta}modificar_concepto_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      $(`#${formulario}`)
        .get(0)
        .reset();
      $(`${modal}`).modal("hide");
      listarSolicitudesBecasConcepto(data.id_solicitud);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const listarSolicitudesBecasConcepto = (id_solicitud) => {
  let tabla = '#tabla_becas_conceptos_detalle'
  $(`${tabla} tbody`)
    .off('click', 'tr')
    .off('click', '.eliminar_concepto')
    .off('click', '.modificar_concepto');
  consulta_ajax(`${ruta}detalle_concepto`, { id_solicitud }, resp => {
    resp.map(i => {
      i.nombreIncluye = i.incluye_beca ? 'SI' : 'NO'
      i.incluye_beca = i.incluye_beca ? i.incluye_beca : ''
      i.valorTotal = new Intl.NumberFormat().format(i.valor_total)
      i.apoyoUniversidad = new Intl.NumberFormat().format(i.apoyo_universidad)
      i.apoyo_universidad == null ? i.apoyo_universidad = "0" : i.apoyo_universidad
      i.valor_asumido = new Intl.NumberFormat().format((Number(i.valor_total) - Number(i.apoyo_universidad)))
    })
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "nombreIncluye"
        },
        {
          data: "concepto"
        },
        {
          data: "incluye_beca"
        },
        {
          data: "apoyoUniversidad"
        },
        {
          data: "valor_asumido"
        },
        {
          data: "valorTotal"
        },
        {
          data: "acciones"
        }
      ],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        if (data.length === 1) {
          total = data.map(i => (Number(i.apoyo_universidad)))
        } else {
          total = data
            .map(i => (Number(i.apoyo_universidad)))
            .reduce((i, j) => (i + j), 0)
        }
        $(api.column(2).footer()).html(`$ ${new Intl.NumberFormat().format(total)}`);
      },
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass("warning");
      $(this).addClass("warning");
    });

    $(`${tabla} tbody`).on('click', '.eliminar_concepto', function () {
      let data = myTable.row($(this).parents('tr')).data();
      let { id, id_solicitud } = data
      tabla = 'becas_concepto';
      mensaje = 'El concepto';
      cambiarEstadoEliminar(id, id_solicitud, tabla, mensaje)
    });

    $(`${tabla} tbody`).on('click', '.modificar_concepto', function () {
      let data = myTable.row($(this).parents('tr')).data();
      $("#modal_agregar_conceptos .modal-title").html('<span class="fa fa-retweet"></span><span id="text_add_arts">Modificar Concepto</span>');
      $('#modal_agregar_conceptos').modal('show');
      $(`#form_agregar_conceptos`).get(0).reset();
      id_anexo = data.id
      id_solicitud = data.id_solicitud
      mostrarOpcionesConceptos(informacion_principal, data)
    });
  })
}

const listarConcepto = (id_solicitud, tabla = '#tabla_beca_conceptos') => {
  consulta_ajax(`${ruta}detalle_concepto`, { id_solicitud }, resp => {
    resp.map(i => {
      i.nombreIncluye = i.incluye_beca ? 'SI' : 'NO'
      i.incluye_beca = i.incluye_beca ? i.incluye_beca : ''
      i.valorTotal = new Intl.NumberFormat().format(i.valor_total)
      i.apoyoUniversidad = new Intl.NumberFormat().format(i.apoyo_universidad)
      i.apoyo_universidad == '' ? i.apoyo_universidad = "0" : i.apoyo_universidad
      i.valor_asumido = new Intl.NumberFormat().format((Number(i.valor_total) - Number(i.apoyo_universidad)))
    })
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: resp,
      columns: [
        {
          data: "nombreIncluye"
        },
        {
          data: "concepto"
        },
        {
          data: "incluye_beca"
        },
        {
          data: "apoyoUniversidad"
        },
        {
          data: "valor_asumido"
        },
        {
          data: "valorTotal"
        }
      ],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        if (data.length === 1) {
          total = data.map(i => Number(i.apoyo_universidad))
        } else {
          total = data
            .map(i => Number(i.apoyo_universidad))
            .reduce((i, j) => (i + j), 0)
        }
        $(api.column(2).footer()).html(`$ ${new Intl.NumberFormat().format(total)}`);
      },
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}

const mostrarConceptoSolicitud = data => {
  callbak_activo = (dataMod) => {
    modificarConceptoSolicitud(dataMod)
  }
  let { id_concepto, valor_total, apoyo_universidad, incluye_beca } = data

  if (id_concepto) {
    $('#select_tipo_apoyo').val(id_concepto)
    $('#select_tipo_apoyo').attr('disabled', true)
    $('#valor_total').val(valor_total)
    $('#apoyo_solicitado').val(apoyo_universidad)
  } else {
    $("#beca_incluye").prop('checked', true)
    $('#select_incluye_beca').val(incluye_beca)
  }
}

//Funcion encargada de limpiar el select de actividades
const limpiarSeleActividades = () => {
  $('#actividad_asignada').html("").append(`<option value="Act_Agre" selected disabled id="informacion_actividad"> ${0} Actividad(es) </option>`)
}

const limpiarSeleCompromisos = () => {
  compromisos = []
  $('#compromiso_asignado').html("").append(`<option value="Comp_Agre" selected disabled id="informacion_compromiso"> ${0} Compromiso(s) </option>`)
}
// auxiliares (lista)
const calcularEdad = (fecha) => {
  let hoy = new Date();
  let cumpleanos = new Date(fecha);
  let edad = hoy.getFullYear() - cumpleanos.getFullYear();
  let m = hoy.getMonth() - cumpleanos.getMonth();
  if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
    edad--;
  }
  return edad;
}
// auxiliares (lista)
const calcularTiempoLaborando = (fecha) => {
  let hoy = new Date();
  let inicio_lab = new Date(fecha);
  let diferencia = ((hoy - inicio_lab) / 31540000000).toFixed(1)
  let year = 0
  let month = 1
  while (diferencia > 1.00) {
    year++
    diferencia -= 1.00
  }
  while (diferencia >= 0.0) {
    month++
    diferencia -= 0.1
  }
  return year === 0 ? `${month} Meses` : `${year} años y ${month} Meses`
}

const ocultarBtnsPlanEntregable = (estado) => {
  if (estado !== 'Bec_Form') {
    $('#retirar_actividad_sele').hide('fast')
    $('#modificar_actividad_sele').hide('fast')
    $('#mas_actividad').hide('fast')
    $('#meta_plan_accion').attr('disabled', 'true')
    $('#retirar_compromiso_sele').hide('fast')
    $('#modificar_compromiso_sele').hide('fast')
    $('#mas_compromiso').hide('fast')
    $('#producto_entregable').attr('disabled', 'true')
    $('#compromiso_entregable').attr('disabled', 'true')
  }
}

const mostrarBtnsPlanEntregable = (estado) => {
  if (estado === 'Bec_Form') {
    $('#retirar_actividad_sele').show('fast')
    $('#modificar_actividad_sele').show('fast')
    $('#mas_actividad').show('fast')
    $('#retirar_compromiso_sele').show('fast')
    $('#modificar_compromiso_sele').show('fast')
    $('#mas_compromiso').show('fast')
    $('#meta_plan_accion').removeAttr('disabled')
    $('#producto_entregable').removeAttr('disabled')
    $('#compromiso_entregable').removeAttr('disabled')
  }
}
// lista
const filtrarSolicitud = () => {
  let formulario = 'form_filtrar_solicitudes'
  let fordata = new FormData(document.getElementById(formulario));
  let data = formDataToJson(fordata);
  data_filtro = data
  listarSolicitudesBecas(data_filtro)
}
// lista
const administrar_elementos = item => {
  if (item == 'menu') {
    $("#menu_principal").fadeIn(1000);
    $("#container_solicitudes").css("display", "none");
  } else if (item == 'solicitudes') {
    $("#menu_principal").css("display", "none");
    $("#container_solicitudes").fadeIn(1000);
  }
}
// lista
const cambiarEstado = (estado, id, observaciones = '', tipo_fin = '') => {
  consulta_ajax(`${ruta}cambiarEstado`, { estado, id, observaciones, tipo_fin }, resp => {
    const { titulo, mensaje, tipo } = resp;
    if (tipo == 'success') {
      validar_cantidad_de_solicitud();
      mostrar_solicitudes_notificaciones();
      enviar_correo_estado(estado, id, observaciones);
      swal.close();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
    listarSolicitudesBecas(data_filtro)
  });
}
// lista
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
          data: "fecha"
        },
        {
          data: "fullname"
        },
        {
          data: "observacion"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })
}
// lista
const gestionar_solicitud = (id_estado) => {
  let { id_usuario_registro } = data_solicitud
  let tabla = '#tabla_detalle_gestion'
  let i = 0
  $(`${tabla} tbody`)
    .off('click', '#visto_bueno')
    .off('click', '#visto_malo');
  consulta_ajax(`${ruta}detalle_estados`, { id_estado }, resp => {
    let estados = resp.filter(aux => (aux.id_estado === 'Bec_Vis_Buen' || aux.id_estado === 'Bec_Gest_Inve' || aux.id_estado === 'Bec_Tram') && aux.id_usuario_registra != id_usuario_registro)
    const myTable = $(`${tabla}`).DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data: estados,
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
          data: "fecha"
        },
        {
          data: "fullname"
        },
        {
          data: "observacion"
        }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  })

  $('#visto_bueno').on('click', function () {
    callbak_activo()
  });

  $('#visto_malo').on('click', function () {
    callbak_activo_alt()
  });
}
// lista
const listarDatosSolicitante = (id_solicitante) => {
  consulta_ajax(`${ruta}datos_solicitante`, { id_solicitante }, resp => {
    if (resp.length > 0) {
      detalleDelSolicitante(resp)
      $('#modal_info_solicitante').modal('show')
    } else {
      MensajeConClase('No hay datos que mostrar', 'info', 'Oops..!');
    }
  });
}
// lista
const detalleDelSolicitante = (data, tabla = '#detalle_solicitante') => {
  let { departamento, contrato, vinculacion, fullname, grupo_investigacion, edad, fecha_inicio, linea, formacion, correo_inst, telefono, programa, cvlac, cantidad, 0: estudios } = data[0]

  listarNivelDeFormacion(estudios)

  $(`${tabla} .nombre_completo`).html(fullname);
  $(`${tabla} .programa_del_solicitante`).html(programa);
  $(`${tabla} .departamento`).html(departamento);
  $(`${tabla} .vinculacion`).html(vinculacion);
  $(`${tabla} .contrato`).html(contrato);
  $(`${tabla} .grupo_investigacion`).html(grupo_investigacion);
  $(`${tabla} .edad`).html(`${calcularEdad(edad)} Años`);
  $(`${tabla} .tiempo_laborando`).html(`${calcularTiempoLaborando(fecha_inicio)}`);
  $(`${tabla} .linea_investigacion`).html(linea);
  $(`${tabla} .nivel_formacion_actual`).html(formacion);
  $(`${tabla} .cvlac`).html(cvlac);
  $(`${tabla} .correo_institucional`).html(correo_inst);
  $(`${tabla} .horas_inv`).html(`${cantidad || 0} Horas`);
  $(`${tabla} .telefono`).html(telefono);
}
// lista
const listarNivelDeFormacion = (resp, tabla = '#tabla_detalle_nivel_formacion') => {
  $(`${tabla} tbody`).off('dblclick', 'tr').off('click', 'tr');
  const myTable = $(`${tabla}`).DataTable({
    "destroy": true,
    "processing": true,
    searching: false,
    paging: false,
    info: false,
    data: resp,
    columns: [
      {
        data: 'nivel'
      },
      {
        data: 'formacion'
      }
    ],
    "language": get_idioma(),
    dom: 'Bfrtip',
    "buttons": [],
  });
}
// lista
const ValidarRevision = (id, estado_siguiente = 'Bec_Envi') => {
  consulta_ajax(`${ruta}validarRevision`, { id }, resp => {
    let { tipo, titulo, mensaje } = resp
    if (tipo === 'success') {
      cambiar_estado_solicitud(id, estado_siguiente, "Enviar Solicitud", 1);
    } else if (tipo === 'info') {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const mostrar_solicitudes_notificaciones = () => {
  consulta_ajax(`${ruta}listar_solicitudes_notificaciones`, {}, resp => {
    let perfil = resp.perfil
    $("#num_notificaciones").html(resp.solicitudes.length);
    pintar_notificaciones(resp.solicitudes)
    if ((perfil === "Per_Admin" || perfil === "Per_Adm_Bec") && resp.solicitudes.length > 0) $('#modal_notificaciones_becas').modal('show');
  });
}

const pintar_notificaciones = (data) => {
  let container = '#panel_notificaciones_solicitudes'
  let titulo = 'Solicitudes Becas a Finalizar'
  let detalle = ``;
  data.map(i => {
    detalle += `
        <a class="list-group-item">   
          <span style="background-color: #2E79E5; font-weight: 500;" class="badge pointer" onclick='ver_certificados(${i.id})'><span class="fa fa-folder-open"></span> Certificados</span>
          <span style="font-weight: 500;" class="badge pointer" onclick='ver_solicitud(${i.id})'><span class="fa fa-eye"></span> Solicitud</span>
          <span>
            <h4 class="list-group-item-heading">${i.fullname}</h4>
            <p class="list-group-item-text text-left">Solicitud No. ${i.id}</p>
            <p class="list-group-item-text text-left">Fecha de Registro: ${i.fecha_registro}</p>
          </span>
        </a>
    `;
  });
  $(container).html(`<ul class="list-group"><li class="list-group-item active"><span class="badge">${data.length}</span>${titulo}</li>${detalle}</ul>`);
}

const ver_solicitud = (id) => {
  listarSolicitudesBecas({ 'id': id })
  $('#modal_notificaciones_becas').modal('hide')
}

const ver_certificados = (id) => {
  id_solicitud = id
  listar_archivos_adjuntos(id_solicitud, 'certificados')
  $("#modal_detalle_archivos .modal-title").html('<span class="fa fa-list"></span> <span id="text_add_arts">Detalle Certificados</span>');
  $("#modal_detalle_archivos .nombre_tabla").html('Tabla de Certificados');
  $('#btn_agregar_certificado').parent().show()
  $('#modal_detalle_archivos').modal('show')
}


const validar_tipo_solicitud = () => {
  if (informacion_principal.id_tipo === 'Soli_Tip_Ren') {
    $("#model_registro_solicitud_beca .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Renovar Solicitud</span>');
    $('#informacion_principal').hide()
    $('#ver_entregable').hide()
    $('#ver_exp').hide()
    $('#ver_intelectual').hide()
    $('#ver_herramienta').hide()
  } else {
    $("#model_registro_solicitud_beca .modal-title").html('<span class="fa fa-plus"></span> <span id="text_add_arts">Nueva Solicitud</span>');
    $('#informacion_principal').show()
    $('#ver_entregable').show()
    $('#ver_exp').show()
    $('#ver_intelectual').show()
    $('#ver_herramienta').show()
  }
}

const validar_tipo_ver = () => {
  if (data_solicitud.id_tipo === 'Soli_Tip_Ren') {
    $('#msg_soli_ini').show()
    $('#detalle_entregable').parent().hide()
    $('#detalle_experiencia').parent().hide()
    $('#detalle_intelectual').parent().hide()
    $('#detalle_herramientas').parent().hide()
  } else {
    $('#msg_soli_ini').hide()
    $('#detalle_entregable').parent().show()
    $('#detalle_experiencia').parent().show()
    $('#detalle_intelectual').parent().show()
    $('#detalle_herramientas').parent().show()
  }
}

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
      let { mensaje, tipo, titulo, listar } = JSON.parse(response)
      if (tipo === 'success') {
        if (id_solicitud) listar_archivos_adjuntos(id_solicitud, listar)
        MensajeConClase(mensaje, tipo, titulo);
      } else if (tipo === 'info') {
        MensajeConClase(mensaje, tipo, titulo);
      }
      $("#modal_enviar_archivos").modal('hide');
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

const listar_archivos_adjuntos = (id_solicitud, tipo = 'anexos') => {
  let tabla = '#tabla_archivos_becas'
  ruta_adjuntos = (tipo === 'certificados') ? ruta_soportes : ruta_archivos;
  $(`${tabla} tbody`)
    .off('click', '.eliminar_adj');
  consulta_ajax(`${ruta}listar_archivos_adjuntos`, { id_solicitud, tipo }, resp => {
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
            else return `<a target='_blank' href='${Traer_Server()}${ruta_adjuntos}${nombre_guardado}' style="background-color: white;width: 100%;" class="fa fa-eye pointer form-control"></a>`;
          }
        },
        {
          data: "nombre_real"
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

const listar_personas = (persona = '') => {
  consulta_ajax(`${ruta}listar_personas`, { persona }, data => {
    $(`#tabla_personas tbody`)
      .off('click', 'tr')
      .off('click', 'tr span.asignar')
      .off('dblclick', 'tr');
    const myTable = $("#tabla_personas").DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data,
      columns: [
        { data: 'fullname' },
        {
          render: (data, type, full, meta) => '<span class="btn btn-default asignar" title="Seleccionar Persona" style="color: #5cb85c"><span class="fa fa-check"></span></span>'
        },
      ],
      language: get_idioma(),
      dom: 'Bfrtip',
      buttons: [],
    });

    $('#tabla_personas tbody').on('click', 'tr', function () {
      $("#tabla_personas tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $('#tabla_personas tbody').on('click', 'tr span.asignar', function () {
      let { id, fullname } = myTable.row($(this).parent().parent()).data();
      id_persona = id;
      $("#modal_elegir_persona").modal('hide');
      $("#s_persona").html(fullname);
      listar_tipo_solicitud(id);
    });

    $('#tabla_personas tbody').on('dblclick', 'tr', function () {
      let { id, fullname } = myTable.row($(this)).data();
      id_persona = id;
      $("#modal_elegir_persona").modal('hide');
      $("#s_persona").html(fullname);
      listar_tipo_solicitud(id);
    });
  });
}

const listar_tipo_solicitud = (persona) => {
  let num = 0;
  consulta_ajax(`${ruta}listar_tipo_solicitud`, { persona }, data => {
    $(`#tabla_tipo_solicitud tbody`)
      .off('click', 'tr')
      .off('click', 'tr span.asignar')
      .off('click', 'tr span.quitar')
      .off('click', 'tr span.config')
      .off('dblclick', 'tr');
    const myTable = $("#tabla_tipo_solicitud").DataTable({
      destroy: true,
      processing: true,
      searching: false,
      data,
      columns: [
        { render: () => ++num },
        { data: 'nombre' },
        {
          render: (data, type, { asignado }, meta) => {
            let datos = asignado
              ? '<span class="btn btn-default quitar" title="Desasignar Permiso" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config"><span class="fa fa-cog"></span></span>'
              : '<span class="btn btn-default asignar" title="Asignar Permiso"><span class="fa fa-toggle-off" ></span></span> ';
            return datos;
          }
        },
      ],
      language: get_idioma(),
      dom: 'Bfrtip',
      buttons: [],
    });

    $('#tabla_tipo_solicitud tbody').on('click', 'tr', function () {
      $("#tabla_tipo_solicitud tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $('#tabla_tipo_solicitud tbody').on('dblclick', 'tr', function () {
      $("#tabla_tipo_solicitud tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $('#tabla_tipo_solicitud tbody').on('click', 'tr span.asignar', function () {
      const { asignado, id } = myTable.row($(this).parent()).data();
      asignar_permiso(asignado, id);
    });

    $('#tabla_tipo_solicitud tbody').on('click', 'tr span.quitar', function () {
      const { asignado, id } = myTable.row($(this).parent()).data();
      desasignar_permiso(asignado, id);
    });

    $('#tabla_tipo_solicitud tbody').on('click', 'tr span.config', function () {
      const { asignado } = myTable.row($(this).parent()).data();
      permiso_selec = asignado;
      $("#modal_elegir_estado").modal();
      listar_estados_permisos(asignado);
    });
  });
}

const asignar_permiso = (asignado, id) => {
  consulta_ajax(`${ruta}asignar_permiso`, { id, persona: id_persona, asignado }, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') listar_tipo_solicitud(id_persona);
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const desasignar_permiso = (asignado, id) => {
  swal({
    title: 'Desasignar Permiso',
    text: "Tener en cuenta que al desasignarle este permiso al usuario, no podrá visualizar ninguna solicitud de este tipo.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#D9534F",
    confirmButtonText: "Si, Entiendo!",
    cancelButtonText: "No, cancelar!",
    allowOutsideClick: true,
    closeOnConfirm: false,
    closeOnCancel: true
  },
    isConfirm => {
      if (isConfirm) {
        consulta_ajax(`${ruta}desasignar_permiso`, { id, persona: id_persona, asignado }, resp => {
          let { mensaje, tipo, titulo } = resp
          if (tipo === 'success') listar_tipo_solicitud(id_persona);
          MensajeConClase(mensaje, tipo, titulo);
        });
        swal.close();
      } else MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
    });
}

const listar_estados_permisos = (permiso) => {
  consulta_ajax(`${ruta}listar_estados_permisos`, { permiso, persona: id_persona }, data => {
    $(`#tabla_estados tbody`)
      .off('click', 'tr')
      .off('dblclick', 'tr')
      .off('click', 'tr span.asignar_estado')
      .off('click', 'tr span.quitar_estado');
    const myTable = $("#tabla_estados").DataTable({
      destroy: true,
      processing: true,
      searching: true,
      data,
      columns: [
        { data: 'parametro' },
        { data: 'nombre' },
        {
          render: (data, type, { asignado }, meta) => {
            let datos = asignado
              ? '<span class="btn btn-default quitar_estado" title="Desasignar Estado" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span>'
              : '<span class="btn btn-default asignar_estado" title="Asignar Estado"><span class="fa fa-toggle-off" ></span></span> ';
            return datos;
          }
        },
      ],
      language: get_idioma(),
      dom: 'Bfrtip',
      buttons: [],
    });

    $('#tabla_estados tbody').on('click', 'tr', function () {
      $("#tabla_estados tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $('#tabla_estados tbody').on('dblclick', 'tr', function () {
      $("#tabla_estados tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });


    $('#tabla_estados tbody').on('click', 'tr span.asignar_estado', function () {
      const { id_estado } = myTable.row($(this).parent()).data();
      asignar_estado_permiso(id_estado, permiso_selec);
    });

    $('#tabla_estados tbody').on('click', 'tr span.quitar_estado', function () {
      const { asignado, id_estado } = myTable.row($(this).parent()).data();
      desasignar_estado_permiso(asignado, id_estado, permiso_selec);
    });
  });
}

const asignar_estado_permiso = (id_estado, id_permiso) => {
  consulta_ajax(`${ruta}asignar_estado_permiso`, { id_estado, id_permiso }, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') listar_estados_permisos(id_permiso);
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const desasignar_estado_permiso = (asignado, id_estado, id_permiso) => {
  consulta_ajax(`${ruta}desasignar_estado_permiso`, { asignado, id_estado, id_permiso }, resp => {
    let { mensaje, tipo, titulo } = resp
    if (tipo === 'success') listar_estados_permisos(id_permiso);
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const enviar_correo_estado = async (estado, id, motivo) => {
  let sw = false;
  let { nombre, correo } = data_solicitante;
  let { tipo_soli, programa } = data_solicitud_noti;
  let ser = `<a href="${server}index.php/becas/${id}"><b>agil.cuc.edu.co</b></a>`;
  let tipo = 3;
  let titulo = '';
  // let nombre_solicitante = nombre;
  let mensaje = `Se informa que hay una solicitud realizada por ${nombre}, lista para ser gestionada por usted, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;

  switch (estado) {
    case 'Bec_Form':
      tipo = 1;
      sw = true;
      titulo = 'Solicitud Creada';
      mensaje = `Se informa que su solicitud ha sido creada con exito, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
      break;
    case 'Bec_Corr':
      tipo = 1;
      sw = true;
      titulo = 'Solicitud a Corrección';
      mensaje = `Se informa que su solicitud ha sido enviada a Corrección por el siguiente motivo : <br> ${motivo}. <br>Puede ingresar al aplicativo AGIL para realizar las correcciones pertinentes.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
      break;
    case 'Bec_Rech':
      tipo = 1;
      sw = true;
      titulo = 'Solicitud Rechazada';
      mensaje = `Se informa que su solicitud ha sido rechazada por el siguiente motivo :<br> ${motivo}.<br><br>Mas informaci&oacuten en ${ser}`;
      break;
    case 'Bec_Apro':
      tipo = 1;
      sw = true;
      titulo = 'Solicitud Aprobada';
      mensaje = `Se informa que su solicitud ha sido aprobada con exito, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
      break;
    case 'Bec_Fina':
      tipo = 1;
      sw = true;
      titulo = 'Solicitud Finalizada';
      mensaje = `Se informa que sus solicitudes han sido finalizada con exito, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
      break;
    case 'Bec_Envi':
      nombre = 'Funcionario CUC';
      sw = true;
      titulo = 'Solicitud Enviada';
      correo = await obtener_personas_permisos(tipo_soli, estado, programa)
      break;
    case 'Bec_Revi':
      nombre = 'Funcionario CUC';
      sw = true;
      titulo = 'Solicitud En Revision';
      correo = await obtener_personas_permisos(tipo_soli, estado, programa)
      break;
    case 'Bec_Vis_Buen':
      let validar_revision = await validar_revisiones(id);
      if (validar_revision) {
        nombre = 'Vicerrector CUC'
        sw = true;
        titulo = 'Solicitud Gestionada';
        correo = await obtener_personas_permisos(tipo_soli, 'Bec_Gest', programa)
      }
      break;
    case 'Bec_Gest_Inve':
      nombre = 'Vicerrector CUC'
      sw = true;
      titulo = 'Solicitud Gestionada';
      correo = await obtener_personas_permisos(tipo_soli, estado, programa)
      break;
    case 'Bec_Tram':
      nombre = 'Funcionario CUC'
      sw = true;
      titulo = 'Solicitud Tramitada';
      correo = await obtener_personas_permisos(tipo_soli, estado, programa)
      break;
    case 'Bec_Acep':
      nombre = 'Funcionario CUC'
      sw = true;
      titulo = 'Solicitud Aceptada';
      correo = await obtener_personas_permisos(tipo_soli, estado, programa)
      break;
  }
  if (sw) enviar_correo_personalizado("blab", mensaje, correo, nombre, "Becas CUC", `Becas AGIL - ${titulo}`, "ParCodAdm", tipo);
}

function Con_filtros(sw) {

	if (sw) {
		$(".mensaje-filtro").show("fast");
	} else {
		$(".mensaje-filtro").css("display", "none");
	}
}
