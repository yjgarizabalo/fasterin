let ruta_interna = "";
let img_path = "";
let ruta_generica = "";
let nombre_area_selected = "";
let area_est_selected = "";
let prev_area_est = "";
let meta_upd_id = "";
let accion = '';
let tipo_accion = "";
let div_to_complete = "";
let dato_buscado = "";
let valora = "";
let valorb = "";
let meta_sel = "";
let lista_nombres = [];
let num_catego = '';
let id_presu_selected = '';
let acciones_array = [];
let acciones_adicionales = [];
let actions_to_del = [];
let cronoSelected = '';
let lastClickedId = '';
let factorSelected = '';
let trimestres_selected = {};
let action = '';
let modalPlace = '';
let targetPlace = '';
let liderSelected = '';
let directorSelected = '';
let gestorSelected = '';
let formatoSelected = '';
let tablaActiva = '';
let formatoActivo = '';
let personaFormatoAsignado = '';
let counter = 0;
let cantidadProgramas = 0;
let cronoInsId = '';
let formatoElegido = '';
let fi = '';
let fp = '';
let seguir = false;
let desde = '';
let ask = '';
let lastClickedPage = '';
let dataToUse = '';
let doit = false;
let lastSearched = "";

$(document).ready(function () {
  $('textarea').attr('maxlength', 9999999999999);
  $('input').attr('maxlength', 9999999999999);

  $("input").hover(function () {
    let place = $(this).attr('placeholder');
    $(this).attr({
      "title": '',
      "data-toggle": "popover",
      "data-trigger": "hover"
    });
    return true;
  });

  ruta_interna = `${Traer_Server()}index.php/plan_accion_control/`;
  img_path = `${Traer_Server()}/imagenes/`;

  /* Setup del select picker */
  $('.selectpicker').selectpicker({
    liveSearchPlaceholder: 'Filtre sus resultados...'
  });

  /* Eventos para tabla admin modulo aquiiii */
  $('#modal_admin_meta li').click(async function () {
    let activeObj = $(this).attr('id');
    tablaActiva = $(this).attr('data-place');
    active_place(`#modal_admin_meta`, activeObj);

    if (tablaActiva == 'lineamientos') {
      traerFactoresIns(lastClickedId);
    } else if (tablaActiva == 'responsables') {
      traerResponsables(lastClickedId);
    } else if (tablaActiva == 'cronograma') {
      let check = await check_entregable(lastClickedId);
      if (check.entregable == null || check.entregable == '') {
        let titulo = `¡Entregable no seleccionado!`;
        let mensaje = `Debe seleccionar un tipo de entregable para poder proseguir.`;
        let tipo = `info`;
        MensajeConClase(mensaje, tipo, titulo);
      } else {
        traerCronograma(lastClickedId);
      }
    } else if (tablaActiva == 'presupuestos') {
      traerPresupuestos(lastClickedId);
    }

  });

  /* Evento para el generar data de la base de datos */
  $("#btnGenData").click(function () {
    generarDatosDBPro();
  });

  $("#modal_db_view #datosGenerales").click(function () {
    let activeObj = $(this).attr('id');
    tablaActiva = $(this).attr('data-place');
    active_place(`#modal_db_view`, activeObj);
    generarDatosDBPro();
  });


  $("#modal_db_view #datosPresupuestos").click(function () {
    let activeObj = $(this).attr('id');
    tablaActiva = $(this).attr('data-place');
    active_place(`#modal_db_view`, activeObj);
    generarDatosDBpresu();
  });

  //----------------------//----------------------//
  $('#modal_administrar_modulo #admin_gestores').click(function () {
    let activeObj = $(this).attr('id');
    tablaActiva = $(this).attr('data-place');
    active_place(`#modal_administrar_modulo`, activeObj);
    listarFormatosPlanAccion();
  });

  $('#modal_administrar_modulo #admin_lideres').click(function () {
    let activeObj = $(this).attr('id');
    tablaActiva = $(this).attr('data-place');
    active_place(`#modal_administrar_modulo`, activeObj);
    listarLidersAssigned();
  });

  $('#sele_perso').click(function () {
    div_to_complete = $(this).prev().attr('id');
    targetPlace = '';
    targetPlace = 'gestores';
    $('#modal_buscar_responsables').modal();
    buscar_responsable('#5#');
    titulo_modal('#modal_buscar_responsables', '<span class="fa fa-search"></span>', ' Buscar gestor');
  });
  /* Fin de eventos de administrar modulo - cinta de opciones */

  /* Eventos para buscar lideres en modal de administrar */
  $('#btnAddLider').click(function () {
    $('#modal_buscar_responsables').modal();
    targetPlace = '';
    targetPlace = 'lideres';
    buscar_responsable('#543#', '');
    titulo_modal('#modal_buscar_responsables', '<span class="fa fa-search"></span>', ' Buscar lider');
  });

  /* Eventos de los btns en el inicio del modulo */
  $("#nueva_accion").click(function () {
    $("#modal_accion_select").modal();
    listar_areas_estrategicas();
  });

  /* Listar solcitudes */
  $("#lista_de_acciones").click(function () {
    $("#menu_principal").hide();
    lastSearched = "";
    listar_solicitudes();
    $(".lista_plan_acciones").fadeIn(1000);
  });

  /* Regresar al menu princial */
  $("#regresar_menu").click(function () {
    $("#menu_principal").show();
    $(".lista_plan_acciones").hide();
  });

  /* Administrar modulo aquii*/
  $("#btn_admin_modulo").on('click', function () {
    $("#modal_administrar_modulo").modal();
    $('#txt_nombre_persona').val('').attr('data-id', '');
    listarLidersAssigned();
  });

  $('#buscarPersona, #personaSelect').on('click', function () {
    $('#modal_personas').modal();
    $('#form_buscPers').trigger('reset');
    div_to_complete = $(this).prev().attr('id');
    buscarPersonas('#543#');
  });
  /* Fin de administrar modulo */

  /* Limpar filtros */
  $("#limpiar_filtros").click(function () {
    lastSearched = "";
    listar_solicitudes();
  });

  /* Eventos para buscar retos y etc */
  $("#form_metasplan_accion .buscar_dato .buscar").on('click', function () {
    div_to_complete = "";
    dato_buscado = "";
    $("#modal_buscar_datos").modal();
    div_to_complete = $(this).prev().attr("id");
    dato_buscado = $(this).attr("data-dato_buscado");
    valora = $("#form_metasplan_accion input[data-input_name='retos_search']").attr("data-v_a");
    buscar_retos_etc(area_est_selected, dato_buscado, valora, valorb, false);
    $("#form_buscar_datos #titulo_buscar_datos").html(`<span class="fa fa-search"></span> Buscar ${dato_buscado}`);
    $("#form_buscar_datos #txt_dato_buscar").attr("placeholder", `Busqueda de ${dato_buscado.toLowerCase()}...`);
    valora = $("#form_metasplan_accion input[data-input_name='retos_search']").attr("data-v_a");
  });


  $(`#form_metasplan_accion .responsables_list`).click(function () {
    let responsables = "<hr>";
    if (lista_nombres.length > 0) {
      lista_nombres.forEach((element, index) => {
        responsables += `<span class="text-left"><strong>${(index + 1)}.</strong> ${element}</span><br><br>`;
      });
    } else {
      responsables += `¡No hay personas seleccionadas!`;
    }
    responsables += `<hr>`;
    mostrar_info(responsables, "Responsables seleccionados");
  });

  /* Eventos formulario de agregar acciones - EVENTOS DE FORMULARIOS */
  $("#form_nueva_accion").submit(function () {
    let datos = $(this).serializeJSON();
    let id_meta = $(this).attr("data-meta_id");
    add_acciones_est(id_meta, datos, tipo_accion);
    return false;
  });

  /* Metas form_metasplan_accion submit */
  $("#form_metasplan_accion").submit(function () {
    let datos = new FormData(document.getElementById("form_metasplan_accion"));
    datos.append("id_area_selected", area_est_selected);
    datos.append("id_reto", $("#form_metasplan_accion #txt_nombre_reto").attr("data-id"));
    datos.append("id_plandes", $("#form_metasplan_accion #txt_nombre_plan_des").attr("data-id"));
    datos.append("id_indicador_es", $("#form_metasplan_accion .indics_content").attr("data-id_ind"));
    datos.append("id_meta_institucional", $("#form_metasplan_accion #vice_search").attr("data-id-meta"));
    datos.append("id_recomendacion", $("#form_metasplan_accion #recomendacionesPrograma").attr("data-id-reco"));
    datos.append("idFormato", formatoElegido);
    let tosend = formDataToJson(datos);
    guardar_metas_accion(tosend, tipo_accion, meta_upd_id);
    return false;
  });

  $(`#form_docs_soporte`).submit(function () {
    cantidad = '';
    if (cronoSelected != '') {
      let tosend = [];
      dataToUse == 'acciones_array' ? tosend = acciones_array : tosend = acciones_adicionales;
      upd_cronograma(meta_sel, trimestres_selected.entregable, trimestres_selected.codigo_item, '', cronoSelected, cantidad, tosend, actions_to_del, trimestres_selected.indicadorOp);
    } else {
      guardar_cronograma(meta_sel, trimestres_selected.entregable, trimestres_selected.codigo_item, trimestres_selected.indicadorOp, '', '', acciones_array);
    }
    return false;
  });

  /* Submit de form temporal para los cronos tipo numerico */
  $('#form_numeric_crono').submit(function () {
    let cantidad = $("#form_numeric_crono .action_amount").val();
    if (cronoSelected != '') {
      let tosend = [];
      dataToUse == 'acciones_array' ? tosend = acciones_array : tosend = acciones_adicionales;
      upd_cronograma(meta_sel, trimestres_selected.entregable, trimestres_selected.codigo_item, '', cronoSelected, cantidad, tosend, actions_to_del, trimestres_selected.indicadorOp);
    } else {
      guardar_cronograma(meta_sel, trimestres_selected.entregable, trimestres_selected.codigo_item, trimestres_selected.indicadorOp, '', cantidad, acciones_array);
    }
    return false;
  });

  /* Buscar personas evento submit */
  $("#form_buscar_responsables").submit(function () {
    let buscar = $("#form_buscar_responsables #txt_responsable_buscar").val();
    buscar_responsable(buscar, meta_sel);
    return false;
  });

  $('#presupuestos_form').submit(function () {
    let datos = $(this).serializeJSON();
    guardar_presupuestos(meta_sel, datos, id_presu_selected);
    return false;
  });

  /* Mini formulario para buscar personas en el apartado de admin modulo */
  $('#form_buscPers').submit(function () {
    let personaBuscada = $('#form_buscPers #perBus').val();
    buscarPersonas(personaBuscada);
    return false;
  });

  $('#form_buscPers #buscarPer').click(function () {
    let personaBuscada = $('#form_buscPers #perBus').val();
    buscarPersonas(personaBuscada);
  });

  /* Evento para aparecer div de agregar ITEMS */
  $(`#form_metasplan_accion #num_or_porcents`).change(async function () {
    let valor = $(this).val();
    let findIdpar = await find_idParametro(valor);
    if (findIdpar.idaux == 'ind_num') {
      $('#form_metasplan_accion .adicional_info').removeClass('oculto');
    } else {
      $('#form_metasplan_accion .adicional_info').addClass('oculto');
    }
  });

  /* Para inputs numericos */
  $('.input_numerico').on('keypress', function (e) {
    if (num_o_string("int", e.keyCode) == false) {
      return false;
    }
  });

  /* Evento para formulario que guarda el formato y el rol de vicerector */
  $('#saveFormatRol').click(async function () {
    let formato = $("#formatos").val();
    let viceRol = $("#vice_roles").val();
    let saveLider = await saveLideres(formato, viceRol, liderSelected);
    if (saveLider.tipo == 'success') {
      MensajeConClase(saveLider.mensaje, saveLider.tipo, saveLider.titulo);
      $('#modal_buscar_responsables').modal('hide');
      setTimeout(cerrar_swals, 1001);
      listarLidersAssigned(liderSelected);
      $('#modal_formato_select').modal('hide');
    } else {
      MensajeConClase(saveLider.mensaje, saveLider.tipo, saveLider.titulo);
      return false;
    }
    //saveFormatoRol(viceRol, liderSelected);
    return false;
  });

  /* Eventos para agregar items - Sacado de talento humano */
  $(`${modalPlace} .txt_nombre_accion`).keydown((event) => {
    if (event.which == 13 || event.keyCode == 13) {
      $(`${modalPlace} .btnagregar_accion`).trigger('click');
      return false;
    }
    return true;
  });

  /* Reset de cronogramas */
  $('#btnResetCrono').click(async function () {
    let titulo = '¡Atención!';
    let msg = 'Está a punto de restablecer este cronograma, lo que implica que debe volver a diligenciar la información requerida según corresponda. ¿Está seguro de realizar esta acción?';
    let tipo = 'warning';
    let btnsi = 'Si, restablecer';
    let btnno = 'No, cancelar';
    let confirm = await confirm_action(titulo, msg, tipo, btnsi, btnno);
    if (confirm == 1) {
      resetCrono(lastClickedId);
    } else {
      swal.close();
    }
  });

  //Agregar acciones al select
  $(`${modalPlace} .btnagregar_accion`).click(async function () {
    const accion = $(`${modalPlace} .txt_nombre_accion`).val();
    let doit = true;
    let acciones = await listar_acciones(cronoSelected);

    if (accion == '') {
      let msg = 'No se puede agregar un soporte vacío.';
      let titulo = 'Oops';
      let tipo = 'warning';
      MensajeConClase(msg, tipo, titulo);
      return false;
    } else {
      if (acciones_array.length > 0) {
        acciones_array.forEach(element => {
          if (element == accion) {
            doit = false;
            let msg = 'Este soporte ya existe en el listado a continuación';
            let titulo = 'Oops';
            let tipo = 'warning';
            MensajeConClase(msg, tipo, titulo);
            return false;
          }
        });
        if (doit) {
          acciones_array.push(accion);
          acciones_adicionales.push(accion);
        }
      } else {
        acciones_array.push(accion);
      }

      let n = acciones_array.length;

      $(`${modalPlace} .acciones_asignadas`).html(`<option value="">${n} Soportes agregadas</option>`);
      acciones_array.forEach(element => {
        $(`${modalPlace} .acciones_asignadas`).append(`<option value="${element}">${element}</option>`);
      });
      $(`${modalPlace} .txt_nombre_accion`).val('').focus();
    }

    if (acciones_adicionales.length > acciones.length) {
      dataToUse = 'acciones_array';
    } else if (acciones.length == actions_to_del.length) {
      dataToUse = 'acciones_array';
    } else {
      dataToUse = 'acciones_adicionales';
    }
  });

  //Retirar acciones del listar
  $(`${modalPlace} .retirar_accion`).click(async function () {
    const subject = $(`${modalPlace} .acciones_asignadas option:selected`).text();
    const val = $(`${modalPlace} .acciones_asignadas`).val();
    let pos = 0;
    let acciones = await listar_acciones(cronoSelected);

    if (acciones_array.length > 0) {
      acciones_array.forEach((element, index) => {
        if (element == val) {
          pos = acciones_array.indexOf(element);
          acciones_array.splice(pos, 1);
          return false;
        }
      });
    }

    acciones.forEach((element, index) => {
      if (element.accion == val) {
        actions_to_del.push(element.id);
      }
    });

    let n = acciones_array.length;
    if (n > 1) {
      $(`${modalPlace} .acciones_asignadas`).html(`<option value="">${n} Soportes agregados</option>`);
    } else {
      $(`${modalPlace} .acciones_asignadas`).html(`<option value="">${n} Soporte agregado</option>`);
    }
    acciones_array.forEach(element => {
      $(`${modalPlace} .acciones_asignadas`).append(`<option value="${element}">${element}</option>`);
    });

    if (actions_to_del.length == acciones.length) {
      dataToUse = 'acciones_array';
    } else {
      dataToUse = 'acciones_adicionales';
    }
  });

  $(`${modalPlace} .btn_cerrar_crono`).click(function () {
    acciones_array = [];
    acciones_adicionales = [];
    actions_to_del = [];
  });

  /* Fin de lo sacado de Talento Humano */

  $(`#btn_buscar_vice`).click(function () {
    $('#modalLiderList').modal();
    listarLidersAssigned2();
  });

  /* Listar programa acreditado */
  $(`#btn_buscarPrograma`).click(function () {
    $('#modalProgramList').modal();
    div_to_complete = $(this).prev().attr('id');
    counter = 0;
    listarProgramas(lastClickedId);
  });

  $('#modal_cronograma .cronoAviso').click(function () {
    $('#modal_cono_reusmen').modal()
    obtenerCronogramaInstitucional(cronoInsId);
  });

  $('#buscarPrograma').click(function () {
    $('#modalCucProgramList').modal();
    cantidadProgramas = 0;
    listarProgramasCuc(directorSelected);
  });
});

//---Funciones fuera del Ready---//
const listarLidersAssigned = (personaInf = '') => {
  let x = 1;
  $('#tabla_gestion .retirarLider').off('click');
  $('#tabla_gestion').off('click', '.asignarPers');
  consulta_ajax(`${ruta_interna}listarLidersAssigned`, { personaInf }, res => {
    /* Eventos desactivados */
    $('#tabla_gestion .asignar').off('click');
    $("#tabla_gestion").off('click', "tbody tr");
    const datos = $('#tabla_gestion').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        {
          render: function () {
            return `<span>${x++}</span>`;
          }
        },
        { data: "fullName" },
        { data: "formatoName" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $('#tabla_gestion').on('click', '.retirarLider', async function () {
      let data = datos.row($(this).parent().parent()).data();
      let titulo = '¡Atención!';
      let mensaje = `¿Desea a: ${data.fullName} como lider del ${data.formatoName}?`;
      let tipo = 'warning';
      let btnsi = 'Si, Eliminar';
      let btnno = 'No, Cancelar';
      let yuma = await confirm_action(titulo, mensaje, tipo, btnsi, btnno);
      if (yuma == 1) {
        delLider(data.id_lider, data.id, data.id_formato);
      } else {
        return false;
      }
    });

    /* Agregar directores al lider */
    $('#tabla_gestion').on('click', '.asignarPers', async function () {
      let data = datos.row($(this).parent().parent()).data();
      liderSelected = '';
      formatoSelected = ''
      liderSelected = data.id_lider;
      formatoSelected = data.id_formato;
      $('#modal_directores_asignados').modal();
      let modalid = '#modal_directores_asignados';
      let icono = `<span class="fa fa-check-square-o"></span>`;
      let titulo = `Directores asignados - ${data.fullName}`;

      titulo_modal(modalid, icono, titulo);
      listarDirectoresAssigned(data.id_lider, formatoSelected);

      //Escondemos los inputs que pertenezcan a programas
      if (data.id_aux == 'formato_programa') {
        $(`[data-id="programPlace"]`).show();
        $(`[data-id="programPlaceChild"]`).attr('disabled', false).show();
      } else {
        $(`[data-id="programPlace"]`).hide();
        $(`[data-id="programPlaceChild"]`).attr('disabled', true).hide();
      }

    });

    /* Evento para cambiar clase activa segun fila selecta */
    $("#tabla_gestion").on('click', "tbody tr", function () {
      $("#tabla_gestion tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Listar directores asignados a un lider */
const listarDirectoresAssigned = (idLider = '', idFormato = '') => {
  consulta_ajax(`${ruta_interna}listarDirectoresAssigned`, { idLider, idFormato }, res => {
    $("#tabla_directores_asignados").off('click', "#btn_add_director");
    $('#tabla_directores_asignados').off('click', '.delDirector');
    const datos = $('#tabla_directores_asignados').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "directorName" },
        { data: "usuario" },
        { data: "directorCargo" },
        {
          data: "presu", render: function (data) {
            return `<span>${divisas(data)}</span>`;
          }
        },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */

    /* Btn para agregar lideres; Btn + Agregar */
    $("#tabla_directores_asignados").on('click', "#btn_add_director", function () {
      targetPlace = '';
      targetPlace = 'directores';
      $('#modal_buscar_responsables').modal();
      buscar_responsable('#543#');
      titulo_modal('#modal_buscar_responsables', '<span class="fa fa-search"></span>', ' Buscar director');
    });

    $('#tabla_directores_asignados').on('click', '.delDirector', async function () {
      let data = datos.row($(this).parent().parent()).data();
      let titulo = '¡Atención!';
      let mensaje = `¿Desea eliminar al director ${data.directorName} del grupo de ${data.liderName}?`;
      let tipo = 'warning';
      let btnsi = 'Si, Eliminar';
      let btnno = 'No, Cancelar';
      let yuma = await confirm_action(titulo, mensaje, tipo, btnsi, btnno);
      if (yuma == 1) {
        delDirectorsToLider(data.id_lider, data.id_director, data.id);
      } else {
        return false;
      }
    });

    /* Evento para cambiar clase activa segun fila selecta */
    $("#tabla_directores_asignados").on('click', "tbody tr", function () {
      $("#tabla_directores_asignados tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Agregar directores a lider seleccionado aquii */
const addDirectorsToLider = (idLider, idDirector, presuDirect, nombreDirector = '', idFormato = '') => {
  consulta_ajax(`${ruta_interna}addDirectorsToLider`, { idLider, idDirector, presuDirect, idFormato }, res => {
    if (res.tipo == 'success') {
      $('#modal_presuSet').modal('hide');
      $('#modal_buscar_responsables #txt_responsable_buscar').focus();

      //Para mostrar el mensaje que se agrego a un usuario correctamente
      $('#modal_buscar_responsables .msg').fadeIn('slow').removeClass('oculto');
      $('#modal_buscar_responsables .textContent').html(`¡Director ${nombreDirector} ha sido agregado correctamente!`);
      setTimeout(cerrarAviso, 3500);
      function cerrarAviso() {
        $('#modal_buscar_responsables .msg').fadeOut('slow').addClass('oculto');
        $('#modal_buscar_responsables .textContent').html(``);
      }

      listarDirectoresAssigned(idLider, idFormato);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Eliminar director asignado a un lider */
const delDirectorsToLider = (idLider, idDirector, id) => {
  consulta_ajax(`${ruta_interna}delDirectorsToLider`, { idLider, idDirector, id }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      $('#modal_buscar_responsables').modal('hide');
      setTimeout(cerrar_swals, 1200);
      listarDirectoresAssigned(idLider, formatoSelected);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Eliminar lideres */
const delLider = (liderId, idPrincial, idFormato) => {
  consulta_ajax(`${ruta_interna}delLider`, { liderId, idPrincial, idFormato }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1200);
      listarLidersAssigned();
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
    }
  });
}

/* Asignar formato a un lider */
const asignarFormato = (idFormato, idPersona) => {
  consulta_ajax(`${ruta_interna}asignarFomato`, { idFormato, idPersona }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      $('#modal_buscar_responsables').modal('hide');
      setTimeout(cerrar_swals, 1200);
      listarLidersAssigned(idPersona);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Listar formatos plan accion para gestores */
const listarFormatosPlanAccion = (idGestor = '') => {
  $("#tabla_gestores").off('click', "tbody tr");
  $('#tabla_gestores').off('click', 'tbody tr .asignar');
  $('#tabla_gestores').off('click', 'tbody tr .quitar');
  $('#tabla_gestores').off('click', 'tbody tr .config');
  consulta_ajax(`${ruta_interna}listarFormatosPlanAccion`, { idGestor }, res => {
    let x = 1;
    const formatos = $("#tabla_gestores").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        {
          render: function () {
            return `<span>${x++}</span>`;
          }
        },
        { data: "formatName" },
        { data: "acciones" }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_gestores").on('click', "tbody tr", function () {
      $("#tabla_gestores tr").removeClass("warning");
      $(this).addClass("warning");
    });

    //Asignar formato a gestor
    $('#tabla_gestores').on('click', 'tbody tr .asignar', function () {
      let datos = formatos.row($(this).parent().parent()).data();
      let gestor = $(`#${div_to_complete}`).attr('data-id');
      asignarFormatoGestor(gestor, datos.id);
    });

    //Retirar formato a gestor
    $('#tabla_gestores').on('click', 'tbody tr .quitar', async function () {
      let datos = formatos.row($(this).parent().parent()).data();
      let gestor = $(`#${div_to_complete}`).attr('data-id');
      let gestorName = $(`#${div_to_complete}`).val();
      let titulo = '¡Atención!';
      let mensaje = `¿Desear retirar el ${datos.formatName} a ${gestorName}?`;
      let tipo = 'warning';
      let btnsi = 'Si, retirar';
      let btnno = 'No, cancelar';
      let confirm = await confirm_action(titulo, mensaje, tipo, btnsi, btnno);
      if (confirm == 1) {
        retirarFormatoGestor(gestor, datos.id);
      } else {
        return false;
      }
    });

    //Asignar estados del formato a gestor
    $('#tabla_gestores').on('click', 'tbody tr .config', function () {
      let datos = formatos.row($(this).parent().parent()).data();
      $('#modal_elegir_estado').modal();
      listarMetasEstados(datos.formatoIdAuto);
    });
  });
}

/* Asignar formato a un gestor - persona */
const asignarFormatoGestor = (idGestor, formatoId) => {
  consulta_ajax(`${ruta_interna}asignarFormatoGestor`, { idGestor, formatoId }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarFormatosPlanAccion(idGestor);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Retirar formato a gestor */
const retirarFormatoGestor = (idGestor, formatoId) => {
  consulta_ajax(`${ruta_interna}retirarFormatoGestor`, { idGestor, formatoId }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarFormatosPlanAccion(idGestor);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Listar estado de metas para los gestores */
const listarMetasEstados = (idGestor) => {
  $("#tabla_estados").off('click', "tbody tr");
  $('#tabla_estados').off('click', 'tbody tr .asignar');
  $('#tabla_estados').off('click', 'tbody tr .desasignar');
  $('#tabla_estados').off('click', 'tbody tr .notificar');
  $('#tabla_estados').off('click', 'tbody tr .no_notificar');
  consulta_ajax(`${ruta_interna}listarMetasEstados`, { idGestor }, res => {
    let x = 1;
    const estados = $("#tabla_estados").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        {
          render: function () {
            return `<span>${x++}</span>`;
          }
        },
        { data: "estado" },
        { data: "acciones" }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_estados").on('click', "tbody tr", function () {
      $("#tabla_estados tr").removeClass("warning");
      $(this).addClass("warning");
    });

    //Asignar formato a gestor
    $('#tabla_estados').on('click', 'tbody tr .asignar', function () {
      let datos = estados.row($(this).parent().parent()).data();
      asignarEstadoGestor(idGestor, datos.idaux);
    });

    //Retirar formato a gestor
    $('#tabla_estados').on('click', 'tbody tr .desasignar', async function () {
      let datos = estados.row($(this).parent().parent()).data();
      let gestorName = $(`#${div_to_complete}`).val();
      let titulo = '¡Atención!';
      let mensaje = `¿Desear retirar el estado: <strong><u>${datos.statusName}</u></strong> a ${gestorName}?`;
      let tipo = 'warning';
      let btnsi = 'Si, retirar';
      let btnno = 'No, cancelar';
      let confirm = await confirm_action(titulo, mensaje, tipo, btnsi, btnno);
      if (confirm == 1) {
        retirarEstadoGestor(idGestor, datos.idaux);
      } else {
        return false;
      }
    });

    //Notificaciones
    $('#tabla_estados').on('click', 'tbody tr .notificar', function () {
      let datos = estados.row($(this).parent().parent()).data();
      activarNotificacionesGestores(idGestor, datos.idaux);
    });

    $('#tabla_estados').on('click', 'tbody tr .no_notificar', async function () {
      let datos = estados.row($(this).parent().parent()).data();
      let gestorName = $(`#${div_to_complete}`).val();
      let titulo = '¡Atención!';
      let mensaje = `¿Desear retirar las notificaciones del estado <strong><u>${datos.statusName}</u></strong> a ${gestorName}?`;
      let tipo = 'warning';
      let btnsi = 'Si, retirar';
      let btnno = 'No, cancelar';
      let confirm = await confirm_action(titulo, mensaje, tipo, btnsi, btnno);
      if (confirm == 1) {
        desactivarNotificacionesGestores(idGestor, datos.idaux);
      } else {
        swal.close();
      }
    });
  });
}

/* Asignar formato a un gestor - persona */
const asignarEstadoGestor = (idGestor, metaEstado) => {
  consulta_ajax(`${ruta_interna}asignarEstadoGestor`, { idGestor, metaEstado }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarMetasEstados(idGestor, metaEstado);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Retirar estados a gestor */
const retirarEstadoGestor = (idGestor, metaEstado) => {
  consulta_ajax(`${ruta_interna}retirarEstadoGestor`, { idGestor, metaEstado }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarMetasEstados(idGestor, metaEstado);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Activar notificaciones en los estados - gestores */
const activarNotificacionesGestores = (idFormatoPrin, metaEstado) => {
  consulta_ajax(`${ruta_interna}activarNotificacionesGestores`, { idFormatoPrin, metaEstado }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarMetasEstados(idFormatoPrin, metaEstado);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Desactivar notificaciones en los estados - gestores */
const desactivarNotificacionesGestores = (idFormatoPrin, metaEstado) => {
  consulta_ajax(`${ruta_interna}desactivarNotificacionesGestores`, { idFormatoPrin, metaEstado }, res => {
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1000);
      listar_solicitudes();
      listarMetasEstados(idFormatoPrin, metaEstado);
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

const listar_formatos_planAccion = () => {
  $("#modal_formato_select #formatos").html("<option value=''>Seleccione un formato</option>");
  consulta_ajax(`${ruta_interna}listar_formatos_planAccion`, {}, res => {
    res.forEach(element => {
      $("#modal_formato_select #formatos").append(`
        <option value="${element.id}" data-id_text="${element.area}" data-id="${element.id}" id="${element.id}" data-idaux="${element.idaux}" data-area_name="${element.area}">${element.area}</option>
      `);
    });
    //Activado los popovers
    $('[data-toggle="popover"]').popover();
  });
}

/* Listar los roles, vice rectores */
const listarRolesVices = () => {
  $("#modal_formato_select #vice_roles").html("<option value=''>Seleccione un rol o Vicerectoría</option>");
  consulta_ajax(`${ruta_interna}listarRolesVices`, {}, res => {
    res.forEach(element => {
      $("#modal_formato_select #vice_roles").append(`
        <option value="${element.id}">${element.rolName}</option>
      `);
    });
    //Activado los popovers
    $('[data-toggle="popover"]').popover();
  });
}

/* Check de formato guardado */
const check_formato_selected = (idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}check_formato_selected`, { idMeta }, res => {
      resolve(res);
    });
  });
}

const listar_areas_estrategicas = () => {
  $("#modal_accion_select #areas_select").html("");
  $("#modal_accion_select .opcion__cont").off('click');

  consulta_ajax(`${ruta_interna}listar_areas_estrategicas`, {}, res => {
    res.forEach(element => {
      $("#areas_select").append(
        `<div class="opcion__cont opcion__cont_large" id="${element.id}" data-idaux="${element.idaux}" data-area_name="${element.area}" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="${element.descripcion}">
          <img src="${img_path}${element.ruta_img}" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
          <span class="opcion__span">${element.area}</span>
        </div>`
      );
    });

    //Activado los popovers
    $('[data-toggle="popover"]').popover();

    /* Eventos de items, activados */
    $("#modal_accion_select .opcion__cont").on('click', function () {
      nombre_area_selected = "";
      area_est_selected = "";
      area_est_selected = $(this).attr('id');
      nombre_area_selected = $(this).attr('data-area_name');
      $("#modal_metas_accion").modal();
      titulo_modal("#modal_metas_accion", `<span class="fa fa-flag"></span>`, `Metas de ${nombre_area_selected}`);
      listar_metas(area_est_selected);
    });
  });
}

/* listar solicitudes */
const listar_solicitudes = () => {
  MensajeConClase("", "waiting_inf", "Oops...");
  consulta_ajax(`${ruta_interna}listar_solicitudes`, {}, res => {
    $("#tabla_plan_acciones").off('click', "tbody tr");
    $("#tabla_plan_acciones").off('click', 'tr .setup_meta');
    $("#tabla_plan_acciones").off('click', 'tr .del_meta');
    $("#tabla_plan_acciones").off('click', 'tr .ver_detalles');
    $("#tabla_plan_acciones").off('click', 'tr .aprobar');
    $("#tabla_plan_acciones").off('click', 'tr .aprobar2');
    $("#tabla_plan_acciones").off('click', 'tr .aprobar3');
    $("#tabla_plan_acciones").off('click', 'tr .corregir');
    $('#modal_gestionar_datosmeta .btnCloseModal').off('click');
    $("#tabla_plan_acciones #tabla_plan_acciones_paginate").off('click');
    const solicitudes = $("#tabla_plan_acciones").DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "ver" },
        { data: "id" },
        { data: "meta_20xx" },
        { data: "area_est" },
        { data: "estado_meta" },
        { data: "usuario_registra" },
        { data: "formatoName" },
        { data: "propiedad" },
        { data: "accion" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: get_botones()
    });
    swal.close();
    doit = true;

    $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

    /* Eventos activados */

    /* Si el usuario hizo una modificacion, datatable al cargar, lo llevará a esa página */
    if (lastClickedPage != '') {
      let pageNum = lastClickedPage;
      solicitudes.page(pageNum).draw(false);
    }

    $("#tabla_plan_acciones_paginate").on('click', function (e) {
      let numPages = solicitudes.page.info();
      let currentPage = numPages.page;
      lastClickedPage = currentPage++;

      if (currentPage <= 0) {
        lastClickedPage = 1;
      } else if (numPages.pages < currentPage && numPages.pages > 1) {
        lastClickedPage = currentPage;
      }
    });

    /* Activa la clase que indica que la fila esta seleccionada */
    $("#tabla_plan_acciones").on('click', "tbody tr", function () {
      $("#tabla_plan_acciones tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Ver detalles de la meta */
    $("#tabla_plan_acciones").on('click', 'tr .ver_detalles', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();

      //Si vienen observaciones, entonces se pinta en alter de boostsrap
      if (datos.obs != null && datos.fecha_corrige != null && datos.meta_estado == "Meta_En_Cor") {
        $("#obsBoxx").removeClass("oculto");
        $("#obsBox").html(
          `<strong>- Observación:</strong> ${datos.obs}. <br>
          <strong>- Fecha en que se envió a corrección:</strong> ${datos.fecha_corrige}.`
        );
      } else {
        $("#obsBoxx").addClass("oculto");
        $("#obsBox").html('');
      }

      $('#modal_admin_meta').modal();

      //Cada vez que abra, mostrara la tabla por defecto que es la de detalles generales de la meta
      let activeObj = 'detalles';
      tablaActiva = 'detalles';
      active_place(`#modal_admin_meta`, activeObj);

      ver_metas_detalles(datos, datos.id);
      lastClickedId = '';
      lastClickedId = datos.id;
    });

    /* Gestionar meta desde la bandeja de solicitudes o metas */
    $("#tabla_plan_acciones").on('click', 'tr .setup_meta', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      area_est_selected = '';
      meta_sel = '';
      trimestres_selected = {};
      action = '';
      acciones_array = [];
      acciones_adicionales = [];
      actions_to_del = [];
      cronoSelected = '';
      lastClickedId = '';
      formatoElegido = '';
      lastClickedId = datos.id;
      area_est_selected = datos.id_area;
      meta_sel = datos.id;
      formatoElegido = datos.id_formato;
      desde = "listar_solicitudes";

      lastSearched = datos.id;
      $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

      let var_modalid = `#modal_gestionar_datosmeta`;
      let var_icono = `<span class="fa fa-list-alt"></span>`;
      let var_titulo = `Gestión de información - Acción: ${datos.id}`;
      titulo_modal(var_modalid, var_icono, var_titulo);

      let chk = await check_formato_selected(datos.id);

      if ((formatoElegido == fp.id) && datos.id == meta_sel) {
        //Factores de programa
        $(`[data-id="programPlace"]`).show();
        $(`[data-id="programPlaceChild"]`).attr('disabled', false).show();
        $('#modal_gestionar_datosmeta div[data-id="factor_insti"]').css({ display: 'none' });
        $('#modal_gestionar_datosmeta div[data-id="factor_program"]').css({ display: '' });

        let modalId = `#modal_buscar_factores`;
        let icono = `<span class="fa fa-search"></span>`;
        let titulo = `Gestionar factores de programa`;
        titulo_modal(modalId, icono, titulo);

      } else if ((formatoElegido == fi.id) && datos.id == meta_sel) {
        //Factores intitucionales
        $(`[data-id="programPlace"]`).hide();
        $(`[data-id="programPlaceChild"]`).attr('disabled', true).hide();
        $('#modal_gestionar_datosmeta div[data-id="factor_insti"]').css({ display: '' });
        $('#modal_gestionar_datosmeta div[data-id="factor_program"]').css({ display: 'none' });

        let modalId = `#modal_buscar_factores`;
        let icono = `<span class="fa fa-search"></span>`;
        let titulo = `Gestionar factores institucionales`;
        titulo_modal(modalId, icono, titulo);
      }

      if (chk == -1) {
        MensajeConClase('Para poder proseguir, debe tener un formato de acción asignado.', 'warning', '¡Atención!');
      } else {
        $("#modal_gestionar_datosmeta").modal();
        let inputs_id = [];
        $("#modal_gestionar_datosmeta .opcion__cont").each(function () {
          inputs_id.push($(this).attr('id'));
        });

        gestionar_meta([datos], meta_sel);
      }

    });

    /* Eliminar meta */
    $("#tabla_plan_acciones").on('click', 'tr .del_meta', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      desde = "listar_solicitudes";
      area_est_selected = '';
      meta_sel = '';

      area_est_selected = datos.id_area;
      meta_sel = datos.id;

      let titulo = "¡Atención!";
      let msg = "¿Desea cancelar esta meta?";
      let tipo = "warning";
      let btn_text_si = "Si, Eliminar";
      let btn_text_no = "No, Cancelar";

      let ans = await confirm_action(titulo, msg, tipo, btn_text_si, btn_text_no);
      ans == 1 ? del_meta(datos.id, area_est_selected) : false;
    });

    /* Aprobar meta */
    $("#tabla_plan_acciones").on('click', 'tr .aprobar', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      lastSearched = datos.id;
      $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

      let titulo = "¡Atención!";
      let msg = "¿Desea enviar esta acción?";
      let tipo = "warning";
      let btn_text_si = "Si, Continuar";
      let btn_text_no = "No, Cancelar";

      let ans = await confirm_action(titulo, msg, tipo, btn_text_si, btn_text_no);
      if (ans == 1) {
        changeStatus(datos.id);
      } else {
        $('#tabla_plan_acciones_filter input').focus().val('').trigger('keyup');
        $("#tabla_plan_acciones tr").removeClass("warning");
        swal.close();
      }
    });

    /* Aprobar meta 2, despues de enviada */
    $("#tabla_plan_acciones").on('click', 'tr .aprobar2', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      lastSearched = datos.id;
      $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

      let titulo = "¡Atención!";
      let msg = "¿Desea dar el aprobado a esta acción?";
      let tipo = "warning";
      let btn_text_si = "Si, Aprobar";
      let btn_text_no = "No, Cancelar";

      let ans = await confirm_action(titulo, msg, tipo, btn_text_si, btn_text_no);
      if (ans == 1) {
        changeStatus(datos.id);
      } else {
        $('#tabla_plan_acciones_filter input').focus().val('').trigger('keyup');
        $("#tabla_plan_acciones tr").removeClass("warning");
        swal.close();
      }
    });

    /* Aprobar meta 3, despues de avalada, pasa a avalado por planeacion */
    $("#tabla_plan_acciones").on('click', 'tr .aprobar3', async function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      lastSearched = datos.id;
      $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

      let titulo = "¡Atención!";
      let msg = "¿Desea aprobar esta acción?";
      let tipo = "warning";
      let btn_text_si = "Si, Aprobar";
      let btn_text_no = "No, Cancelar";

      let ans = await confirm_action(titulo, msg, tipo, btn_text_si, btn_text_no);
      if (ans == 1) {
        changeStatus(datos.id);
      } else {
        $('#tabla_plan_acciones_filter input').focus().val('').trigger('keyup');
        $("#tabla_plan_acciones tr").removeClass("warning");
        swal.close();
      }
    });

    /* Btn de correccion */
    $("#tabla_plan_acciones").on('click', 'tr .corregir', function () {
      let datos = solicitudes.row($(this).parent().parent()).data();
      lastSearched = datos.id;
      $('#tabla_plan_acciones_filter input').focus().val(lastSearched).trigger('keyup');

      swal({
        title: "¡Atención!",
        text: "¿Desea enviar esta acción a corrección?",
        type: "input",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Continuar!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true,
        inputPlaceholder: "Ingrese obervación..."
      }, function (obs) {
        if (obs == false || obs == "") {
          swal.showInputError("¡Debe ingresar una observación!");
          return false;
        } else {
          changeStatus(datos.id, obs);
          return false;
        }
      });
    });

    /* $('#modal_gestionar_datosmeta .btnCloseModal').click(function () {
      $('#tabla_plan_acciones_filter input').focus().val('').trigger('keyup');
    }); */
  });
}

/* Cambiar estado - BORRAR FUNCION DESPUES */
const changeStatus = (idMeta, metaObs = '') => {
  MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
  consulta_ajax(`${ruta_interna}changeStatus`, { idMeta, metaObs }, res => {
    lastSearched = $("#tabla_plan_acciones_filter input").val();
    if (res.tipo == 'success') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(dothat, 1001);
      function dothat() {
        listar_solicitudes();
      }
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Listar personas para el administrar modulo */
const buscarPersonas = (personaBuscada = '') => {
  $('#tabla_personas').off('click', 'tr .perSel');
  consulta_ajax(`${ruta_interna}buscarPersonas`, { personaBuscada }, res => {
    const info = $('#tabla_personas').DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: res,
      columns: [
        { data: 'fullName' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos tabla */
    $('#tabla_personas').on('click', 'tr .perSel', function () {
      let datos = info.row($(this).parent().parent()).data();
      $(`#${div_to_complete}`).val(datos.fullName).attr('data-id', datos.id);
      $('#modal_personas').modal('hide');
      listarLidersAssigned(datos.id);
    });
  });
}

/* Funcion para buscar datos como retos, indicadores y etc. */
const buscar_retos_etc = async (area_est_selected = "", reto_meta_indicador = "", val_a = "", val_b = "", ind_search = false) => {
  $("#tabla_datos_busqueda tbody tr").off('click', "tbody tr");
  $("#tabla_datos_busqueda").off('click', ".select");
  consulta_ajax(`${ruta_interna}buscar_retos_etc`, { area_est_selected, reto_meta_indicador, val_a, val_b, ind_search }, res => {
    const tabla = $("#tabla_datos_busqueda").DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: 'area' },
        { data: 'valor' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos de botones activados */

    /* Aqui, se traen el indicador estrategico una vez se haya seleccionado la meta de plan de accion */
    let last_results = tabla.row().data();

    if (last_results != undefined) {
      if (last_results.render_ind == true) {
        $('#modal_metas_form .indics_content').html(last_results.valor).attr("data-id_ind", last_results.id);
      } else {
        $('#modal_metas_form .indics_content').html(`<p class="indics_content"> ¡Seleccione una meta de plan de desarrollo primero! </p>`).attr("data-id_ind", "");
      }
    }
    /* Fin */

    /* Activa la clase que indica que la fila esta seleccionada */
    $("#tabla_datos_busqueda").on('click', "tbody tr", function () {
      $("#tabla_datos_busqueda tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Evento para cuando se seleccione un item del buscar retos, plan de accion o indicadores. */
    $("#tabla_datos_busqueda").on('click', ".select", function () {
      let datos = tabla.row($(this).parent().parent()).data();
      $(`#form_metasplan_accion #${div_to_complete}`).val(datos.valor).attr("data-id", datos.id).attr("data-v_a", datos.v_a);

      if (datos.v_a == 'reto_area_new') { //Cambiar los id a los de no aplica de produccion de metas plan de desarrollo
        $('#modal_metas_form #txt_nombre_plan_des').attr('data-id', 30782).attr('data-v_a', 'reto_area_new').val('No Aplica');
        $('#modal_metas_form .indics_content').attr('data-id_ind', 30782).html('No Aplica');
      } else {
        if (div_to_complete != 'txt_nombre_plan_des') {
          $('#modal_metas_form #txt_nombre_plan_des').val('').attr('data-id', '');
          $('#modal_metas_from .indicador_est').attr('data-id_ind', '');
        }
      }

      $("#form_buscar_datos").trigger('reset');
      $("#modal_buscar_datos").modal("hide");
      valorb = datos.v_b;
      if (valorb != undefined) {
        ind_search = true;
        buscar_retos_etc(area_est_selected, reto_meta_indicador = "Indicadores", valora, valorb, ind_search);
      }
    });
  });
}

/* Buscar responsables asignados */
const buscar_responsables_asignados = async (mtaId) => {
  $("#tabla_responsables_asignados #btn_add_respon").off('click');
  $("#tabla_responsables_asignados tbody tr").off('click', "tbody tr");
  $("#tabla_responsables_asignados").off('click', 'tr .remove_res');
  consulta_ajax(`${ruta_interna}buscar_responsables_asignados`, { mtaId }, res => {
    const inf = $("#tabla_responsables_asignados").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: res,
      columns: [
        { data: 'full_name' },
        { data: 'usuario' },
        { data: 'cargo_sap' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos para btns */

    /* Activa la clase que indica que la fila esta seleccionada */
    $("#tabla_responsables_asignados").on('click', "tbody tr", function () {
      $("#tabla_responsables_asignados tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Agregar personas responsables */
    $("#tabla_responsables_asignados #btn_add_respon").on('click', function () {
      $("#modal_buscar_responsables").modal();
      $('#form_buscar_responsables').trigger('reset');
      let buscar = $(`#form_buscar_responsables #txt_responsable_buscar`).val();
      buscar == "" ? buscar = 'undefined' : false;
      buscar_responsable(buscar, mtaId);
      titulo_modal('#modal_buscar_responsables', '<span class="fa fa-search"></span>', ' Buscar responsable');
    });

    /* Eliminar responsables */
    $("#tabla_responsables_asignados").on('click', 'tr .remove_res', async function () {
      let datos = inf.row($(this).parent().parent()).data();
      if (datos != undefined) {
        let borrar = await delete_responsable(mtaId, datos.id);
        if (borrar) {
          buscar_responsables_asignados(mtaId);
          let buscar = $("#form_buscar_responsables #txt_responsable_buscar").val();
          buscar_responsable(buscar, mtaId);
          titulo_modal('#modal_buscar_responsables', '<span class="fa fa-search"></span>', ' Buscar responsable');
        }
      }
    });
  });
}

/* Buscar responsables aquii */
const buscar_responsable = async (busqueda = "", meta_slectaa, idFormato = '') => {
  $("#tabla_responsables_busqueda").off('click', 'tr .disable');
  $("#tabla_responsables_busqueda").off('click', 'tr .enable');
  $("#tabla_responsables_busqueda").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}buscar_responsable`, { busqueda, meta_slectaa }, res => {
    const infoo = $(`#tabla_responsables_busqueda`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: res,
      columns: [
        { data: 'full_name' },
        { data: 'usuario' },
        { data: 'cargo_sap' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos para btns */
    /* Activa la clase que indica que la fila esta seleccionada */
    $("#tabla_responsables_busqueda").on('click', "tbody tr", function () {
      $("#tabla_responsables_busqueda tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $(`#tabla_responsables_busqueda`).on('click', 'tr .disable', async function () {
      let datos = infoo.row($(this).parent().parent()).data();
      if (datos != undefined) {
        if (targetPlace == 'responsables') {
          let save = await save_responsable(meta_slectaa, datos.id);
          if (save) {
            buscar_responsables_asignados(meta_slectaa);
            let buscar = $("#form_buscar_responsables #txt_responsable_buscar").val();
            buscar_responsable(buscar, meta_slectaa);
          }
        } else if (targetPlace == 'lideres') {

          liderSelected = '';
          $('#modal_formato_select').modal();
          listar_formatos_planAccion();
          listarRolesVices();
          liderSelected = datos.id;
          $('#modal_buscar_responsables #txt_responsable_buscar').val('');
          buscar_responsable('#543#');

        } else if (targetPlace == 'directores') {

          directorSelected = '';
          directorSelected = datos.id;
          let valorPresu = '';
          $('#modal_presuSet').modal();
          $('#modal_presuSet #dirPresu').focus();
          $('#modal_presuSet #asignarPresu').off('click');
          $('#modal_presuSet #asignarPresu').on('click', async function () {
            valorPresu = $('#dirPresu').val();
            if (valorPresu != '') {
              addDirectorsToLider(liderSelected, datos.id, valorPresu, datos.full_name, formatoSelected);
            }
          });

          $('#modal_buscar_responsables #txt_responsable_buscar').val('').focus();

          cantidadProgramas = 0;
          $('#dirPresu').val('').focus();
          listarProgramasCuc(directorSelected);
          buscar_responsable('#543#');

        } else if (targetPlace == 'gestores') {
          gestorSelected = '';
          gestorSelected = datos.id;
          $(`#${div_to_complete}`).attr('data-id', gestorSelected).val(datos.full_name);
          $('#modal_buscar_responsables').modal('hide');
          listarFormatosPlanAccion(datos.id);
        }
      }
    });

    $(`#tabla_responsables_busqueda`).on('click', 'tr .enable', async function () {
      let datos = infoo.row($(this).parent().parent()).data();
      if (datos != undefined) {
        if (targetPlace == 'responsables') {
          let borrar = await delete_responsable(meta_slectaa, datos.id);
          if (borrar) {
            buscar_responsables_asignados(meta_slectaa);
            let buscar = $("#form_buscar_responsables #txt_responsable_buscar").val();
            buscar_responsable(buscar, meta_slectaa);
          }
        }
      }
    });
  });
}

/* Guardar responsables seleccionados */
const save_responsable = (idmeta, idper) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}save_responsable`, { idmeta, idper }, res => {
      resolve(res);
    });
  });
}

/* Eliminar responsables o quitar seleccion de los mismos */
const delete_responsable = (idmeta, idper) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delete_responsable`, { idmeta, idper }, res => {
      resolve(res);
    });
  });
}

/* Guardar lideres */
const saveLideres = (idFormato, viceRol, idper) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}saveLideres`, { idFormato, viceRol, idper }, res => {
      resolve(res);
    });
  });
}

/* listar indicadores operativos */
const listar_indicadores_operativos = async (selected_option = "") => {
  $("#num_or_porcents").html(`<option class="defecto" value='' deafult>Seleccione tipo de indicador operativo</option>`);
  consulta_ajax(`${ruta_interna}listar_indicadores_operativos`, {}, async res => {
    res.forEach(element => {
      $("#num_or_porcents").append(
        `<option data-id="${element.valory}" value='${element.id}'>${element.dato}</option>`
      );
    });

    let findIdpar = await find_idParametro(selected_option);
    if (selected_option != "") {
      $(`#num_or_porcents option[value="${selected_option}"]`).attr("selected", true);
      if (findIdpar.idaux == 'ind_porcent') { //Porcentual o numerico para evento de la cifra de referencia. Cambiar en produccion
        $('#modal_metas_form #cifraRefCont').show();
        $('[data-toggle="popover"]').popover();
      } else {
        $('#modal_metas_form #cifraRefCont').hide();
      }
    } else {
      $(`#num_or_porcents option[class="defecto"]`).attr("selected", true);
    }

    /* Evento activo */
    $('#num_or_porcents').off('change');
    let valorActual = $('#num_or_porcents').val();
    $('#num_or_porcents').change(async function () {

      let valorCambia = $(this).val();
      if (valorActual != '') {
        if (valorCambia != '' && valorCambia != valorActual) {
          let msg = `Si selecciona un tipo de indicador operativo (numérico o porcentual) diferente al ya establecido previamente, AGIL restablecerá todo el cronograma asociado a esta acción. Por tanto, deberá diligenciar su cronograma nuevamente.`;
          let tipo = `warning`;
          let titulo = `¡Atención!`;
          MensajeConClase(msg, tipo, titulo);
        }
      }

      let currentVal = $(this).val();
      let findIdpar = await find_idParametro(currentVal);
      if (findIdpar.idaux == 'ind_porcent') { //Porcentual o numerico para evento de la cifra de referencia. Cambiar en produccion
        $('#modal_metas_form #cifraRefCont').show();
        $('[data-toggle="popover"]').popover();
      } else {
        $('#modal_metas_form #cifraRefCont').hide();
      }
    });
  });
}

/* Funcion para guardar metas */
const guardar_metas_accion = (datos = "", tipo_accion = "", meta_upd_id = "") => {
  let {
    id_area_selected,
    id_reto,
    id_plandes,
    id_indicador_es,
    meta_plan_accion,
    indicador_operativo,
    num_or_porcents,
    cifra_referencia,
    meta,
    nombre_accion,
    id_meta_institucional,
    idFormato
  } = datos;
  consulta_ajax(
    `${ruta_interna}guardar_metas_accion`,
    {
      meta_upd_id,
      tipo_accion,
      id_area_selected,
      id_reto, id_plandes,
      id_indicador_es,
      meta_plan_accion,
      indicador_operativo,
      num_or_porcents,
      cifra_referencia,
      meta,
      nombre_accion,
      idFormato,
      id_meta_institucional
    },
    res => {
      if (res.tipo == "success") {
        lastSearched = $("#tabla_plan_acciones_filter input").val();
        $("#form_metasplan_accion").trigger("reset");
        $("#modal_metas_form").modal('hide');
        $("#modal_admin_meta").modal('hide');
        $(`#modal_gestionar_datosmeta`).modal('hide');
        $(`#modal_formatos_asignados`).modal('hide');

        MensajeConClase(res.mensaje, res.tipo, res.titulo);
        setTimeout(dothat, 1001);

        function dothat() {
          //Condicion para no cargar data dos veces.
          if (desde == "listar_metas") {
            listar_metas(area_est_selected, formatoElegido);
          } else if (desde == "listar_solicitudes") {
            listar_solicitudes();
          }

          if (doit) {
            if (meta_upd_id != '') {
              seguir = false;
              setTimeout(clickear, 1001);
              function clickear() {
                if (desde == "listar_metas") {
                  $(`#modal_metas_accion .gestionar_meta span[data-id="${lastClickedId}"]`).trigger('click');
                } else if (desde == "listar_solicitudes") {
                  $(`#tabla_plan_acciones td span[id="${lastClickedId}"]`).trigger('click');
                }
              }
            } else {
              seguir = true;
            }
          }
        }
      } else {
        MensajeConClase(res.mensaje, res.tipo, res.titulo);
      }
    });
}

/* Funcion para listar metas */
const listar_metas = (area_est, idFormato = '') => {

  /* Eventos de botones desactivados */
  $("#modal_metas_accion #cartas_container .ver_detalles").off('click');
  $("#modal_metas_accion #cartas_container .upd_meta").off('click');
  $("#modal_metas_accion #cartas_container .del_meta").off('click');
  $("#modal_metas_accion #cartas_container .add_metas").off('click');
  $("#modal_metas_accion #cartas_container .gestionar_meta").off('click');

  let num = 0;
  let datos = {};
  consulta_ajax(`${ruta_interna}listar_metas`, { area_est, idFormato }, res => {
    let estilo = "";

    if (res.length < 0 || res.length == undefined) {
      $("#modal_metas_accion #cartas_container").html(res.carta_defecto);
    } else {
      $("#modal_metas_accion #cartas_container").html("");
      $("#modal_metas_accion #cartas_container").append(`${res[0].carta_defecto}`);

      res.forEach((r, i) => {
        let formato_name = '';
        num = r.id;
        r.formato_plan == null ? formato_name = 'Nueva' : formato_name = r.formato_plan;
        $("#modal_metas_accion #cartas_container").append(
          `<div style="padding: 1%; display: inline-table; overflow: hidden;">
            <div class="card carta_styles pointer new_meta" data-meta_id="${r.id}" style="overflow: hidden;">
              <div class="del_meta">
                <span class="fa fa-trash trash_icon" data-id="${r.id}"></span>
              </div>
              <div class="idDiv" title="Código único de Acción">
                <span style="background:rgba(236, 49, 49, 0.9); color: white; border-radius: 50px; padding: 6px;">${r.id}</span>
              </div>
              <div class="img_card_container">
                <img src="${img_path}plan_accion.png" alt="..." style="width: 48%; padding-top: 5px; margin: auto;">
              </div>
              <div class="card-body" style="height:auto; padding: 5%; padding-bottom: 15%;">
                <h4 class="card-title" style="font-weight: bold; font-size: 1.3em;">Meta - ${formato_name}</h4>
                <p class="card-text" style="font-size: 1.1em;">${cortar_textos(r.meta_20xx, 35)} <small data-id="${r.id}" class="pointer ver_detalles" style="font-style: italic; text-decoration: underline;">Ver más!</small></p>
                <span data-id="${r.id}" data-idpro="${r.id_formato}" data-pro="${r.formatoIdAux}" class="btn btn-default cards_btns gestionar_meta">
                  <span class="fa fa-pencil-square-o" data-id="${r.id}"></span> Gestionar
                </span>
              </div>
            </div>
          </div>
          `
        );
      });

      swal.close();

      if (seguir) {
        setTimeout(clickearr, 1002);
        function clickearr() {
          let var_modalid = `#modal_gestionar_datosmeta`;
          let var_icono = `<span class="fa fa-list-alt"></span>`;
          let var_titulo = `Gestión de información - Acción: ${num}`;
          titulo_modal(var_modalid, var_icono, var_titulo);
          $(`#modal_metas_accion .gestionar_meta span[data-id="${num}"]`).trigger('click');
        }
        seguir = false;
      }
    }

    /* EVENTOS PARA BOTONES ACTIVADO */

    $("#modal_metas_accion #cartas_container .gestionar_meta").on('click', async function (e) {
      e.preventDefault();
      e.stopPropagation();
      trimestres_selected = {};
      acciones_array = [];
      actions_to_del = [];
      acciones_adicionales = [];
      meta_sel = e.target.dataset.id;
      tipo_accion = "";
      tipo_accion = "upd";
      lastClickedId = '';
      formatoElegido = '';
      lastClickedId = $(this).attr('data-id');
      formatoActivo = $(this).attr('data-pro');
      formatoElegido = $(this).attr('data-idpro');
      desde = "listar_metas";

      let n = $(e.target).attr('data-id');
      let var_modalid = `#modal_gestionar_datosmeta`;
      let var_icono = `<span class="fa fa-list-alt"></span>`;
      let var_titulo = `Gestión de información - Acción: ${n}`;
      titulo_modal(var_modalid, var_icono, var_titulo);


      let chk = await check_formato_selected(meta_sel);

      if (formatoActivo == fi.idaux) {
        $('[data-id="programPlace"]').hide();
        $('[data-id="programPlaceChild"]').attr('disabled', true).hide();
      } else {
        $('[data-id="programPlace"]').show();
        $('[data-id="programPlaceChild"]').attr('disabled', false).show();
      }
      /*Fin del bloque de verificacion para mostrar inputs del formulario principal*/

      //Aqui vamos mostrando los items de factores insitucionales o los que sean necesarios
      res.forEach(element => {
        if (element.id_formato == fp.id && element.id == meta_sel) {
          //Factores de programa
          $('#modal_gestionar_datosmeta div[data-id="factor_insti"]').css({ display: 'none' });
          $('#modal_gestionar_datosmeta div[data-id="factor_program"]').css({ display: '' });
        } else if (element.id_formato == fi.id && element.id == meta_sel) {
          //Factores intitucionales
          $('#modal_gestionar_datosmeta div[data-id="factor_insti"]').css({ display: '' });
          $('#modal_gestionar_datosmeta div[data-id="factor_program"]').css({ display: 'none' });
        }
      });

      if (chk == -1) {
        MensajeConClase('Debe tener un formato asignado antes de empezar a diligenciar una metao acción', 'warning', '¡Atención!');
        return false;
      } else {
        gestionar_meta(res, meta_sel);
        $('#modal_gestionar_datosmeta').modal();
      }
    });

    /* Agregar o crear nueva meta */
    $("#modal_metas_accion #cartas_container .add_metas").on('click', async function (e) {
      e.preventDefault();
      desde = "listar_metas";
      doit = true;
      ask = true;

      let formatosActivos = await formatosAsignados();
      if (formatosActivos.tipo == "warning") {
        MensajeConClase(formatosActivos.mensaje, formatosActivos.tipo, formatosActivos.titulo);
        return false;
      }

      if (formatosActivos.length > 1) {
        $('#modal_formatos_asignados').modal();
        formatoElegido = await listar_formatos_activos(formatosActivos);
      } else {
        formatoElegido = formatosActivos[0].idFormato;
      }

      let close = await timeCheck(formatoElegido);
      if (close == false && ask == true) {
        let titulo = '¡Atención!';
        let msg = 'Al dar clic en <i style="color:#D0504C;"><strong>"Si, Continuar"</strong></i>, creará una meta automáticamente la cual podrá empezar a gestionar.';
        let tipo = 'warning';
        let answer = await confirm_action(titulo, msg, tipo, 'Si, Continuar', 'No, Cancelar');
        if (answer == 1) {
          datos = { "id_area_selected": area_est_selected, 'idFormato': formatoElegido };
          guardar_metas_accion(datos);
        }
      } else {
        MensajeConClase(close.mensaje, close.tipo, close.titulo);
      }
    });

    /* Ver detalles de la meta */
    $("#modal_metas_accion #cartas_container .ver_detalles").on('click', function (e) {
      $("#modal_admin_meta").modal();
      $("#obsBoxx").addClass("oculto");
      meta_id = e.target.dataset.id;
      ver_metas_detalles(res, meta_id);
    });


    /* Actualizar meta */
    $("#modal_metas_accion #cartas_container .upd_meta").on('click', function (e) {
      e.preventDefault();
      tipo_accion = "";
      tipo_accion = "upd";
      meta_target_id = e.target.dataset.id;
      upd(res, meta_target_id);
    });

    /* Eliminar meta */
    $("#modal_metas_accion #cartas_container .del_meta").on('click', async function (e) {
      e.preventDefault();
      desde = "listar_metas";
      let new_data = {};
      meta_id = e.target.dataset.id;

      res.forEach(r => {
        if (r.id == meta_id) {
          new_data = r;
        }
      });

      let titulo = "¡Atención!";
      let msg = "¿Desea eliminar esta meta?";
      let tipo = "warning";
      let btn_text_si = "Si, Eliminar";
      let btn_text_no = "No, Cancelar";

      let ans = await confirm_action(titulo, msg, tipo, btn_text_si, btn_text_no);
      ans == 1 ? del_meta(new_data.id, area_est_selected) : false;
    });
  });
}

/* Gestionar meta */
const gestionar_meta = async (datos, meta_s) => {
  let inputs_id = [];
  $("#modal_gestionar_datosmeta .opcion__cont").each(function () {
    inputs_id.push($(this).attr('id'));
  });
  btns_off('#modal_gestionar_datosmeta', inputs_id);

  $(`#modal_gestionar_datosmeta`).modal().attr('data-metaid', meta_s);
  $("#modal_gestionar_datosmeta #info_item").on('click', function (e) {
    e.preventDefault();
    upd(datos, meta_s);
    let modal_id = '#modal_metas_form';
    let icon = '<span class="fa fa-refresh"></span>';
    let titulo = `Actualizar meta de ${datos[0].area_est}`;
    titulo_modal(modal_id, icon, titulo);
    if (datos[0].indicador_estrategico == null) {
      $('#modal_metas_form .indics_content').html(`¡Seleccione una meta de plan de desarrollo primero!`);
    } else {
      $('#modal_metas_form .indics_content').html(`${datos[0].indicador_estrategico}`);
    }
    counter = 0;
    listarProgramas(lastClickedId);
  });

  //Gestionar presupuesto, seleccionando un tipo de categoria para ir llenando los demas selects en el form
  $("#modal_gestionar_datosmeta #presupuesto_item").on('click', function (e) {
    e.preventDefault();

    $(`#modal_listar_presupuestos`).modal();
    listar_presupuestos(meta_sel);

    $(`#modal_listar_presupuestos #btn_add_presu`).off('click');
    $(`#modal_listar_presupuestos #btn_add_presu`).on('click', function () {
      id_presu_selected = '';
      $("#infoAlert").hide();
      $('#divsToShow').show();
      reset_presuForm();
      $('#presupuestos_form').trigger('reset');
      $(`#modal_presupuestos`).modal();
      $('#modal_presupuestos .indics_content').html('¡Seleccione el tipo de presupuesto primero!');
      titulo_modal("#modal_presupuestos", `<span class="fa fa-money"></span>`, `Agregar presupuesto`);
      categorias_presupuestos();
      presupuestosDirector(formatoElegido);
    });
  });

  //Evento de buscar el tipo de presupuesto segun la categoria seleccionada.
  $(`#categoria_presupuesto`).off('change');
  $(`#categoria_presupuesto`).change(function () {
    let catego_buscado = $(this).find(':selected').data('catego_num');
    let idaux = $(this).find(':selected').data('idaux');

    if (idaux == "no_presupuesto") {
      $("#infoAlert").show();
      $('#categoria_presupuesto').selectpicker();
      $('#categoria_presupuesto').selectpicker("refresh");
      $('#divsToShow').hide();
    } else {
      $("#infoAlert").hide();
      $('#divsToShow').show();
      $('#categoria_presupuesto').selectpicker();
      reset_presuForm();
      tipos_presupuestos(catego_buscado);
    }

  });

  //Evento de buscar el item segun el tipo de presupuesto seleccionado.
  $(`#tipo_presupuesto`).off('change');
  $(`#tipo_presupuesto`).change(function () {
    let valor_selected = $(this).val();
    let item_buscado = $(this).find(':selected').data('tipo_num');
    let tipo_info = $(this).find(':selected').data('des');

    if (valor_selected != 0) {
      $(`#modal_presupuestos .indics_content`).html(tipo_info);
    } else {
      $(`#modal_presupuestos .indics_content`).html(`¡Seleccione un tipo de presupuesto primero!`);
    }

    items_presupuestos(item_buscado);
  });

  //Gestionar factores - seleccionar
  $("#modal_gestionar_datosmeta #factores_item").on('click', function (e) {
    e.preventDefault();
    $(`#modal_buscar_factores`).modal();
    listar_factores_ins(meta_s);
  });

  //Gestionar factores de programa - seleccionar //SEGUIR
  $("#modal_gestionar_datosmeta #factores_programa").on('click', function (e) {
    e.preventDefault();
    $(`#modal_buscar_factores`).modal();
    listar_factores_ins(meta_s, 'listar_programa_facts');
  });

  //Gestionar responsables
  $("#modal_gestionar_datosmeta #responsable_item").on('click', function () {
    targetPlace = '';
    targetPlace = 'responsables';
    $(`#modal_responsables_asignados`).modal();
    buscar_responsables_asignados(meta_s);
  });

  $('#cronograma_item').off('click');
  $('#cronograma_item').on('click', async function () {
    trimestres_selected = {};
    action = '';
    acciones_array = [];
    acciones_adicionales = [];
    actions_to_del = [];

    let check = await check_entregable(meta_sel);
    if (check.entregable == null || check.entregable == '') {
      let titulo = `¡Entregable no seleccionado!`;
      let mensaje = `Debe seleccionar un tipo de entregable para poder proseguir.`;
      let tipo = `info`;
      MensajeConClase(mensaje, tipo, titulo);
    } else {
      $('#modal_cronograma .btnSaveCrono').show();
      $('#modal_cronograma .btnResetCrono').show();
      traer_cronograma(meta_sel);
    }
  });
}

/* Ver detalles de metas */
const ver_metas_detalles = (datos, id_meta) => {
  let new_data = {};

  if (datos.length > 0) {
    datos.forEach(e => {
      if (e.id == id_meta) {
        new_data = e;
      }
    });
  } else {
    new_data = datos;
  }

  let {
    reto,
    meta_plan_desarrollo,
    indicador_estrategico,
    tipo_indicador_operativo,
    meta_20xx,
    indicador_operativo,
    cifra_referencia,
    meta,
    nombre_accion,
    usuario_registra,
    area_est,
    formatoName
  } = new_data;

  $(".titulo_formato").html(formatoName);
  $(".titulo_area_est").html(area_est);
  $(".titulo_reto").html(reto);
  $(".titulo_meta_planaccion").html(meta_plan_desarrollo);
  $(".titulo_ind_est").html(indicador_estrategico);
  $(".titulo_tipo_ind_op").html(tipo_indicador_operativo);
  $(".titulo_meta_accion").html(meta_20xx);
  $(".titulo_indi_op").html(indicador_operativo);
  $(".titulo_cifra_ref").html(cifra_referencia);
  $(".titulo_meta").html(meta);
  $(".titulo_nombre_accion").html(nombre_accion);
  $(".titulo_usuario_reg").html(usuario_registra);
}

/* Eliminar meta */
const del_meta = (meta_id, area_est_selected) => {
  consulta_ajax(`${ruta_interna}del_meta`, { meta_id }, res => {
    if (res.tipo == "success") {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(completarGestion, 1001);

      lastSearched = $("#tabla_plan_acciones_filter input").val();

      function completarGestion() {
        $("#modal_metas_form").modal('hide');
        $("#modal_admin_meta").modal('hide');
        $("#form_metasplan_accion").trigger('reset');

        if (desde == "listar_metas") {
          listar_metas(area_est_selected);
          swal.close();
        } else if (desde == "listar_solicitudes") {
          listar_solicitudes();
        }
      }

    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
    }
  });
}

/* Funcion para listar factores insitucionales */
const listar_factores_ins = (meta_selecta = "", searchPath = 'listar_factores_ins') => {
  $(`#tabla_factores_busqueda`).off('click', 'tr .facts_details');
  $(`#tabla_factores_busqueda`).off('click', 'tr .disable');
  $(`#tabla_factores_busqueda`).off('click', 'tr .enable');
  $("#tabla_factores_busqueda").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}${searchPath}`, { meta_selecta }, res => {
    const tabla = $("#tabla_factores_busqueda").DataTable({
      destroy: true,
      searching: true,
      processing: true,
      ordering: true,
      data: res,
      columns: [
        { data: 'ver' },
        { data: 'valor' },
        { data: 'estado' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos de btns activados */

    /* Activa la clase que indica que la fila esta seleccionada */
    $("#tabla_factores_busqueda").on('click', "tbody tr", function () {
      $("#tabla_factores_busqueda tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Ver detalles */
    $("#tabla_factores_busqueda").on('click', 'tr .facts_details', function () {
      let datos = tabla.row($(this).parent().parent()).data();
      $(`#modal_detalles_factores`).modal();
      factorSelected = '';
      factorSelected = datos.id;
      let place = '';
      if (searchPath == 'listar_factores_ins') {
        place = 'insti';
      } else if (searchPath == 'listar_programa_facts') {
        place = 'program';
      }

      ver_detalles_facts(datos.detalles, place, meta_selecta);
    });

    $(`#tabla_factores_busqueda`).on('click', 'tr .disable', async function () {
      let datos = tabla.row($(this).parent().parent()).data();
      let asigna = await save_factors(meta_selecta, datos.id);
      if (asigna) {
        listar_factores_ins(meta_selecta, searchPath);
      }
    });

    $(`#tabla_factores_busqueda`).on('click', 'tr .enable', async function () {
      let datos = tabla.row($(this).parent().parent()).data();
      let borrar = await delete_factors(meta_selecta, datos.id);
      if (borrar) {
        listar_factores_ins(meta_selecta, searchPath);
      }
    });
  });
}

/* Ver detalles del factor seleccionado, detalles como las caracteristicas del mismo */
const ver_detalles_facts = async (caract_buscada, insti_or_pro, idMeta) => {
  /* Eventos de btns desactivados */
  $('#tabla_factores_detalles').off('click', 'tr .enable');
  $('#tabla_factores_detalles').off('click', 'tr .disable');

  consulta_ajax(`${ruta_interna}detalles_facts`, { caract_buscada, insti_or_pro, idMeta }, res => {
    const tabla = $(`#tabla_factores_detalles`).DataTable({
      destroy: true,
      searching: true,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'caracteristica' },
        { data: 'estado' },
        { data: 'accion' }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos de btns activados */
    $('#tabla_factores_detalles').on('click', 'tr .enable', async function () {
      let datos = tabla.row($(this).parent().parent()).data();
      let del = await delete_caracts(idMeta, datos.id);
      if (del) {
        ver_detalles_facts(caract_buscada, insti_or_pro, idMeta);
      } else {
        return false;
      }
    });

    $('#tabla_factores_detalles').on('click', 'tr .disable', async function () {
      let datos = tabla.row($(this).parent().parent()).data();
      let saveC = await save_caracts(idMeta, datos.id, factorSelected);
      if (saveC) {
        ver_detalles_facts(caract_buscada, insti_or_pro, idMeta);
      } else {
        return false;
      }
    });
  });
}

/* Guardar factores seleccionados */
const save_caracts = (idmeta, idcaracts, idFactor) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}save_caracts`, { idmeta, idcaracts, idFactor }, res => {
      if (res.tipo == 'error') {
        MensajeConClase(res.mensaje, res.tipo, res.titulo);
      } else {
        resolve(res);
      }
    });
  });
}

/* Eliminar factores o quitar seleccion de los mismos */
const delete_caracts = (idmeta, idcaracts) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delete_caracts`, { idmeta, idcaracts }, res => {
      resolve(res);
    });
  });
}

/* Guardar factores seleccionados */
const save_factors = (idmeta, idfact) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}save_factors`, { idmeta, idfact }, res => {
      resolve(res);
    });
  });
}

/* Eliminar factores o quitar seleccion de los mismos */
const delete_factors = (idmeta, idfact) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delete_factors`, { idmeta, idfact }, res => {
      resolve(res);
    });
  });
}

/* Funcion para renderizar datos a actualizar */
const upd = (datos, metaa_id) => {
  $("#modal_metas_form").modal();
  titulo_modal("#modal_metas_form", `<span class="fa fa-refresh"></span>`, `Actualizar meta de ${nombre_area_selected}`);

  let new_data = {};
  datos.forEach(r => {
    if (r.id == metaa_id) {
      new_data = r;
    }
  });

  let {
    reto,
    reto_id,
    meta_plan_desarrollo,
    plan_desarrollo_id,
    indicador_estrategico,
    indicador_estrategico_id,
    meta_20xx,
    indicador_operativo,
    cifra_referencia,
    meta,
    nombre_accion,
    idPrograma,
    programName,
    idRecomendacion,
    recomendacion,
    tituloMetaInsti,
    idmi,
    liderRol,
  } = new_data;

  meta_upd_id = new_data.id;
  listar_indicadores_operativos(new_data.tipo_indicador_id);
  $("#txt_nombre_reto").attr("data-id", reto_id).val(reto);
  $("#txt_nombre_plan_des").attr("data-id", plan_desarrollo_id).val(meta_plan_desarrollo);
  $('#modal_metas_form .indics_content').html(indicador_estrategico).attr("data-id_ind", indicador_estrategico_id);
  $("#meta_plan_accion").val(meta_20xx);
  $("#indicador_operativo").val(indicador_operativo);
  $("#cifra_referencia").val(cifra_referencia);
  $("#meta").val(meta);
  $("#nombre_accion").val(nombre_accion);
  $('#programaSearch').attr('data-id-pro', idPrograma).val(programName);
  $('#recomendacionesPrograma').attr('data-id-reco', idRecomendacion).val(recomendacion);

  tituloMetaInsti == null ? tituloMetaInsti = 'N/A' : tituloMetaInsti = tituloMetaInsti;
  $('#nombreAccionInsti').html(tituloMetaInsti);

  $(`#vice_search`).attr("data-id-meta", idmi).val(liderRol);
}

/* Traer las categorias de los presupuestos */
const categorias_presupuestos = async (catego_selected = '', id_tipo = '', id_item = '') => {
  $(`#categoria_presupuesto`).html(`<option class="defecto" value="0" default>Seleccione una categoría</option>`);
  consulta_ajax(`${ruta_interna}categorias_presupuestos`, {}, res => {
    if (res.length > 0) {
      res.forEach(element => {
        $(`#categoria_presupuesto`).append(`<option value="${element.id}" data-idaux="${element.idaux}" data-catego_num="${element.catego_num}">${element.catego}</option>`);
        if (element.id == catego_selected) {
          tipos_presupuestos(element.catego_num, id_tipo, id_item);
        }
      });
    }
    if (catego_selected != "") {
      $(`#categoria_presupuesto option[value="${catego_selected}"]`).attr("selected", true);
      let categoSelected = $(`#categoria_presupuesto`).find(":selected").attr('data-idaux');
      if (categoSelected == "no_presupuesto") {
        $('#divsToShow').hide();
        $("#infoAlert").show();
      } else {
        $('#divsToShow').show();
        $("#infoAlert").hide();
      }
      $(`.selectpicker`).selectpicker('refresh');
    } else {
      $(`#categoria_presupuesto option[value="defecto"]`).attr("selected", false);
      $(`.selectpicker`).selectpicker('refresh');
    }
    $(`.selectpicker`).selectpicker('refresh');
  });
}

/* Traer las categorias de los presupuestos  */
const tipos_presupuestos = (categoria_selected = '', tipo_selected = '', id_item = '') => {
  $(`#tipo_presupuesto`).html(`<option class="defecto" value="0" default>Seleccione un tipo</option>`);
  consulta_ajax(`${ruta_interna}tipos_presupuestos`, { categoria_selected }, res => {
    if (res.length > 0) {
      res.forEach(element => {
        $(`#tipo_presupuesto`).append(`<option value="${element.id}" data-des="${element.tipo_inf}" data-tipo_num="${element.tipo_num}" data-catego_num="${element.catego_num}">${element.tipo}</option>`);
        if (tipo_selected == element.id) {
          $(`#modal_presupuestos .indics_content`).html(element.tipo_inf);
          items_presupuestos(element.tipo_num, id_item);
        }
      });
    }
    if (tipo_selected != "") {
      $(`#tipo_presupuesto option[value="${tipo_selected}"]`).attr("selected", true);
      $(`#tipo_presupuesto`).selectpicker('refresh');
    } else {
      $(`#tipo_presupuesto option[value="defecto"]`).attr("selected", false);
      $(`.selectpicker`).selectpicker('refresh');
    }
    $(`.selectpicker`).selectpicker('refresh');
  });
}

/* Traer los items segun tipo de lo presupuesto seleccionado  */
const items_presupuestos = (tipo_selected = '', item_selected = '') => {
  $(`#item_presupuesto`).html(`<option class="defecto" value="0" default>Seleccione item a diligenciar</option>`);
  consulta_ajax(`${ruta_interna}items_presupuestos`, { tipo_selected }, res => {
    if (res.length > 0) {
      res.forEach(element => {
        $(`#item_presupuesto`).append(`<option value="${element.id}" data-tipo_num="${element.tipo_num}" data-catego_num="${element.catego_num}">${element.tipo}</option>`);
      });
    }
    if (item_selected != "") {
      $(`#item_presupuesto option[value="${item_selected}"]`).attr("selected", true);
      $(`#item_presupuesto`).selectpicker('refresh');
    } else {
      $(`#item_presupuesto option[value="defecto"]`).attr("selected", false);
      $(`.selectpicker`).selectpicker('refresh');
    }
    $(`.selectpicker`).selectpicker('refresh');
  });
}

/* Reset de la tipo e items diligenciados */
const reset_presuForm = () => {
  $(`#tipo_presupuesto`).html(`<option class="defecto" value="0" default>Seleccione un tipo</option>`);
  $(`#item_presupuesto`).html(`<option class="defecto" value="0" default>Seleccione ítem a diligenciar</option>`);
  $(`#tipo_presupuesto option[class="defecto"], #item_presupuesto option[class="defecto"]`).attr("selected", true);
  $(`.selectpicker`).selectpicker('refresh');
  return;
}

/* Traer todos los presupuestos de los directores */
const presupuestosDirector = (idFormato = '') => {
  consulta_ajax(`${ruta_interna}presupuestosDirector`, { idFormato }, res => {
    if (res) {
      $('#presuInfo .titulo_topePresu').html(divisas(res[0].topePresu)); //Presupuesto del Director
      $('#presuInfo .titulo_totalSolicitado').html(divisas(res[0].totalSolicitado)); //Suma de todas las metas y lo que va gastando
      $('#presuInfo .titulo_topeDispo').html(divisas(res[0].topeDispo)); //Resta que muestra lo que le queda al director segun invierte.
    }
  });
}

/* Guardar los presupuestos */
const guardar_presupuestos = (idmeta, datos, id_presu) => {
  let {
    categoria_presupuesto,
    tipo_presupuesto,
    item_presupuesto,
    descripcion,
    valor_solicitado
  } = datos

  consulta_ajax(`${ruta_interna}guardar_presupuestos`, { idmeta, categoria_presupuesto, tipo_presupuesto, item_presupuesto, descripcion, valor_solicitado, id_presu }, res => {
    if (res.tipo == 'success') {
      lastSearched = $("#tabla_plan_acciones_filter input").val();
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(dothat, 1001);
      $('#presupuestos_form').trigger('reset');
      $('#modal_presupuestos').modal('hide');
      function dothat() {
        listar_solicitudes();
        listar_presupuestos(idmeta);
      }
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
    }
  });
}

/* Listar presupuestos */
const listar_presupuestos = async (id_meta = '') => {
  $("#tabla_listar_presupuestos tr").off('click', "tbody tr");
  $("#tabla_listar_presupuestos").off('click', "tbody tr .remove_presu");
  $("#tabla_listar_presupuestos").off('click', "tbody tr .upd_presu");
  consulta_ajax(`${ruta_interna}listar_presupuestos`, { id_meta }, res => {
    const datos = $(`#tabla_listar_presupuestos`).DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: 'categoria' },
        { data: 'tipo' },
        { data: 'item' },
        {
          data: 'valor_solicitado', render: function (data) {
            return `<span>${divisas(data)}</span>`
          }
        },
        { data: 'accion' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos Activados */
    $("#tabla_listar_presupuestos").on('click', "tbody tr", function () {
      $("#tabla_listar_presupuestos tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Eliminar presupuesto */
    $("#tabla_listar_presupuestos").on('click', "tbody tr .remove_presu", function () {
      let data = datos.row($(this).parent().parent()).data();
      del_presupuesto(data.id, id_meta);
    });

    /* Actualizar presupuesto */
    $("#tabla_listar_presupuestos").on('click', "tbody tr .upd_presu", function () {
      let data = datos.row($(this).parent().parent()).data();
      id_presu_selected = data.id;
      upd_presupuesto(data, id_meta);
      presupuestosDirector(formatoElegido);
    });
  });
}

/* Eliminar presupuesto */
const del_presupuesto = async (id_presu, metasel) => {
  if (id_presu) {
    let titulo = '¡Atención!';
    let msg = '¿De verdad desea eliminar este presupuesto?';
    let tipo = 'warning';
    let confirm = await confirm_action(titulo, msg, tipo, 'Si, eliminar', 'No, cancelar');

    if (confirm == 1) {
      consulta_ajax(`${ruta_interna}del_presupuestos`, { id_presu, metasel }, res => {
        if (res == true) {
          cerrar_swals();
          listar_solicitudes();
          listar_presupuestos(metasel);
        } else {
          MensajeConClase(res.mensaje, res.tipo, res.titulo);
        }
      });
    }

  }
}

/* Actualizar presupuesto seleccionado */
const upd_presupuesto = async (datos, metasel) => {
  $(`#modal_presupuestos`).modal();
  titulo_modal("#modal_presupuestos", `<span class="fa fa-refresh"></span>`, `Actualizar de presupuesto`);
  accion = 'upd';
  categorias_presupuestos(datos.idcategoria, datos.idtipo, datos.iditem);
  $(`#modal_presupuestos #descripcion`).val(datos.descripcion);
  $(`#modal_presupuestos #valor_solicitado`).val(datos.valor_solicitado);
}

/* Check si el entregable esta seteado en BD */
const check_entregable = async (id_meta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}check_entregable`, { id_meta }, res => {
      resolve(res);
    });
  });
}

/* Listar o traer cronograma */
const traer_cronograma = (idMeta) => {
  modalPlace = '';
  $(`#modal_cronograma .acciones_asignadas`).html(`<option value="">0 Soportes agregados</option>`);
  $(`#tabla_cronograma`).off('click', 'tr .enable');
  $(`#tabla_cronograma`).off('click', 'tr .disable');
  $(`#tabla_cronograma`).off('click', 'tr .upd_crono');
  $(`#tabla_cronograma`).off('click', 'tr .del_crono');
  $(`#tabla_cronograma`).off('click', 'tr .checkCrono');
  consulta_ajax(`${ruta_interna}traer_cronograma`, { idMeta }, async res => {
    if (res.tipo == 'warning') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    } else if (res.length >= 1) {
      //Con esta funcion, seteamos todo para cargar cronos porcentuales o numericos
      setup_cronos(res);
      $(`#modal_cronograma`).modal();
      const MyTable = $(`#tabla_cronograma`).DataTable({
        destroy: true,
        searching: false,
        processing: true,
        ordering: false,
        data: res,
        columns: [
          { data: 'trime' },
          { data: 'acciones' },
        ],
        language: get_idioma(),
        dom: "Bfrtip",
        buttons: []
      });

      /* Eventos btns activados */
      //Enable event
      $(`#tabla_cronograma`).on('click', 'tr .enable', async function () {
        let datos = MyTable.row($(this).parent().parent()).data();
        let estado = $(this).attr('data-status');
        let idpar = await find_idParametro('ind_porcent');
        if (datos.indicador_op == idpar.id) {//Si es porcentual
          trimestres_selected = { "id_meta": datos.id_meta, "entregable": datos.entregable, "indicadorOp": datos.indicador_op, "codigo_item": datos.codigo_item };
          if (estado == 'off') {
            $('#tabla_cronograma span[data-des="1"]').removeClass('fa fa-toggle-on disable').addClass('fa fa-toggle-off enable').attr('data-status', 'off');
            $(this).removeClass('fa fa-toggle-off enable').addClass('fa fa-toggle-on disable').attr('data-status', 'on');
          } else {
            $(this).removeClass('fa fa-toggle-on disable').addClass('fa fa-toggle-off enable').attr('data-status', 'off');
          }
        }
      });

      //Disable event
      $(`#tabla_cronograma`).on('click', 'tr .disable', async function () {
        let datos = MyTable.row($(this).parent().parent()).data();
        let estado = $(this).attr('data-status');
        trimestres_selected = {};
        if (estado == 'on') {
          $('#tabla_cronograma span[data-des="1"]').removeClass('fa fa-toggle-on disable').addClass('fa fa-toggle-off enable').attr('data-status', 'off');
          $(this).removeClass('fa fa-toggle-on disable').addClass('fa fa-toggle-off enable').attr('data-status', 'off');
        } else {
          $(this).removeClass('fa fa-toggle-on disable').addClass('fa fa-toggle-off enable').attr('data-status', 'off');
        }
      });

      //Check Event
      $(`#tabla_cronograma`).on('click', 'tr .checkCrono', async function () {
        let datos = MyTable.row($(this).parent().parent()).data();
        let idpar = await find_idParametro('ind_num');
        if (idpar != '') {
          if (datos.indicador_op == idpar.id) {
            trimestres_selected = {};
            acciones_array = [];
            actions_to_del = [];
            acciones_adicionales = [];
            cronoSelected = '';
            cronoSelected = datos.id;
            trimestres_selected = { "id_meta": datos.id_meta, "entregable": datos.entregable, "indicadorOp": datos.indicador_op, "codigo_item": datos.codigo_item };
            let modId = `#modal_numeric_crono`;
            let titulo = `Documentos soporte ${datos.trime}`;
            let icon = `<span class="fa fa-file-text"></span>`;
            modalPlace = modId;
            titulo_modal(modId, icon, titulo);
            $('#modal_numeric_crono').modal();
            $(`#modal_numeric_crono .action_amount`).val(datos.cantidad);
            $(`${modalPlace} .action_amounts`).show('fast');
            renderSavedAcciones(datos.id, modId);
          }
        }
      });
    }
  });
}

/* Setup de cronogramas */
const setup_cronos = async (res) => {
  //Verificamos si pintar area de cargue de info de docs soporte
  if (!res[0].porcents || res[0].porcents == false) {
    $('#form_docs_soporte .porcentual_container').addClass('oculto');
    $('#form_docs_soporte .btnSaveCrono').hide();
    $('#form_docs_soporte .porcentual_container input[data-id="porcentual_items"]').prop("disabled", true);
    $('#form_docs_soporte .porcentual_container select[data-id="porcentual_items"]').prop("disabled", true);
  } else {
    $('#form_docs_soporte .porcentual_container').removeClass('oculto');
    $('#form_docs_soporte .btnSaveCrono').removeClass('oculto');
    $('#form_docs_soporte .porcentual_container input[data-id="porcentual_items"]').prop("disabled", false);
    $('#form_docs_soporte .porcentual_container select[data-id="porcentual_items"]').prop("disabled", false);
  }
  $('#modal_cronograma .titulo_checks').html(`<span class="fa fa-th-large"></span> Descripción de documentos soporte de la Meta del Plan de Acción`);
  $('#modal_cronograma .indics_content').html(`${res[0].aviso}`);

  trimestres_selected = {};
  acciones_array = [];
  actions_to_del = [];
  acciones_adicionales = [];
  let cronoId = '';
  let indicadorSelecto = await find_idParametro(res[0].indicador_op);

  if (res[0].idmi != null) {
    cronoInsId = res[0].idmi;
    $('#modal_cronograma .cronoAviso').parent().show();
    $('#modal_cronograma .cronoAviso').html(`${res[0].cronoAviso}`);
  } else {
    $('#modal_cronograma .cronoAviso').parent().hide();
    $('#modal_cronograma .cronoAviso').html(``);
  }

  res.forEach(e => {
    if (e.id != '') {
      cronoId = e.id;
      cronoSelected = e.id;
      if (indicadorSelecto.idaux == 'ind_porcent') {
        modalPlace = `#modal_cronograma`;
        $(`${modalPlace} .action_amounts`).hide('fast');
        $('#modal_numeric_crono .action_amount').prop('required', false);
        renderSavedAcciones(cronoId, `#modal_cronograma`);
        trimestres_selected = { "id_meta": e.id_meta, "entregable": e.entregable, "indicadorOp": e.indicador_op, "codigo_item": e.codigo_item };
      }
      return false;
    } else {
      if (indicadorSelecto.idaux == 'ind_num') {
        modalPlace = `#modal_numeric_crono`;
        $(`${modalPlace} .action_amounts`).removeClass('oculto');
        $('#modal_numeric_crono .action_amount').prop('required', true);
      } else if (indicadorSelecto.idaux == 'ind_porcent') {
        modalPlace = `#modal_cronograma`;
        $(`${modalPlace} .action_amounts`).addClass('oculto');
        $('#modal_numeric_crono .action_amount').prop('required', false);
      }
    }
  });
}

/* Render de nombres de documentos soportes guardados */
const renderSavedAcciones = async (cronoId, modalId) => {
  let acciones = await listar_acciones(cronoId);
  if (acciones.length > 0) {
    let n = acciones.length;
    if (n > 1) {
      $(`${modalId} .acciones_asignadas`).html(`<option value="">${n} Soportes agregados</option>`);
    } else {
      $(`${modalId} .acciones_asignadas`).html(`<option value="">${n} Soporte agregado</option>`);
    }

    $(`${modalId} .acciones_asignadas`).html(`<option value="">${n} Soportes agregados</option>`);
    acciones.forEach(element => {
      acciones_array.push(element.accion);
      $(`${modalId} .acciones_asignadas`).append(`<option data-id="${element.id}" value="${element.accion}">${element.accion}</option>`);
    });
  } else {
    let n = acciones.length;
    if (n > 1) {
      $(`${modalId} .acciones_asignadas`).html(`<option value="">${n} Soportes agregados</option>`);
    } else {
      $(`${modalId} .acciones_asignadas`).html(`<option value="">${n} Soporte agregado</option>`);
    }
  }
}

/* Guardar cronogramas */
const guardar_cronograma = (idMeta, entregable, codigo_item, indiOp, especi, cantidad, acciones) => {
  consulta_ajax(`${ruta_interna}guardar_cronograma`, { idMeta, entregable, codigo_item, indiOp, especi, cantidad, acciones }, res => {
    if (res.tipo == "success") {
      lastSearched = $("#tabla_plan_acciones_filter input").val();
      $('#modal_check_cronograma').modal('hide');
      $('#modal_cronograma').modal('hide');
      $('#modal_numeric_crono').modal('hide');
      $('#form_check_cronos, #form_numeric_crono').trigger('reset');
      trimestres_selected = {};
      action = '';
      acciones_array = [];
      acciones_adicionales = [];
      actions_to_del = [];
      cronoSelected = '';
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(proseguir, 1001);

      function proseguir() {
        listar_solicitudes();
        traer_cronograma(idMeta);
      }

    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Actualizar cronograma */
const upd_cronograma = (idMeta, entregable, codigo_item, especi, idCrono, cantidad, acciones, actions_del, indiOp) => {
  consulta_ajax(`${ruta_interna}upd_cronograma`, { idMeta, entregable, codigo_item, especi, idCrono, cantidad, acciones, actions_del, indiOp }, res => {
    if (res.tipo == 'success') {
      lastSearched = $("#tabla_plan_acciones_filter input").val();
      $('#modal_check_cronograma').modal('hide');
      $('#modal_cronograma').modal('hide');
      $('#modal_numeric_crono').modal('hide');
      $('#form_check_cronos').trigger('reset');
      trimestres_selected = {};
      action = '';
      acciones_array = [];
      acciones_adicionales = [];
      actions_to_del = [];
      cronoSelected = '';
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(proseguir, 1001);

      function proseguir() {
        listar_solicitudes();
        traer_cronograma(idMeta);
      }

    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Eliminar cronograma */
const del_cronograma = (idMeta, idCrono, itemCod) => {
  consulta_ajax(`${ruta_interna}del_cronograma`, { idCrono, itemCod, idMeta }, res => {
    if (res.tipo == 'success') {
      /* MensajeConClase(res.mensaje, res.tipo, res.titulo);
      setTimeout(cerrar_swals, 1300); */
      listar_solicitudes();
      traer_cronograma(idMeta);
      $('#form_check_cronos').trigger('reset');
      acciones_array = [];
      acciones_adicionales = [];
      actions_to_del = [];
      cronoSelected = '';
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Listar acciones */
const listar_acciones = (idCrono) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}listar_acciones`, { idCrono }, res => {
      resolve(res);
    });
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

/* AQUII */
const listarLidersAssigned2 = (personaInf = '') => {
  let x = 1;
  $("#tabla_vices2").off('click', "tbody tr");
  $("#tabla_vices2").off('click', "tr .verMetas");
  consulta_ajax(`${ruta_interna}listarLidersAssigned2`, { personaInf }, res => {
    const datos = $('#tabla_vices2').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "fullName", "visible": false },
        { data: "rolName" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */

    $("#tabla_vices2").on('click', "tr .verMetas", function () {
      let data = datos.row($(this).parent().parent()).data();
      $('#modalMetasDirectors2').modal();
      directoresMetas(area_est_selected, data.id_lider);
    });

    /* Evento para cambiar clase activa segun fila selecta */
    $("#tabla_vices2").on('click', "tbody tr", function () {
      $("#tabla_vices2 tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

const obtenerCronogramaInstitucional = (idMeta) => { //arreglar
  $("#tablaCronogramaIns").off('click', "tbody tr");
  $("#tablaCronogramaIns").off('click', "tr .verMetas");
  $("#tablaCronogramaIns").off('click', "tr .verDocsNames");
  consulta_ajax(`${ruta_interna}obtenerCronogramaInstitucional`, { idMeta }, res => {
    const datos = $('#tablaCronogramaIns').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "trimestreName" },
        { data: "cantidad" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tablaCronogramaIns").on('click', "tr .verMetas", function () {
      let data = datos.row($(this).parent().parent()).data();
      $('#modalMetasDirectors2').modal();
      $(`#vice_search`).val(data.rolName);
      directoresMetas(area_est_selected, data.id_lider);
    });

    $("#tablaCronogramaIns").on('click', "tr .verDocsNames", function () {
      let data = datos.row($(this).parent().parent()).data();
      $('#modal_docs_soporte').modal();
      traerDocsSoporte(idMeta, data.id);
    });

    /* Evento para cambiar clase activa segun fila selecta */
    $("#tablaCronogramaIns").on('click', "tbody tr", function () {
      $("#tabla_gestion tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Listar metas de directores de un lider */
const directoresMetas = (idArea, idLider) => {
  $("#tabla_metasDirectors2").off('click', "tbody tr");
  $("#tabla_metasDirectors2").off('click', "tr .metaSelect");
  $("#tabla_metasDirectors2").off('click', "tr .seeDetails");
  consulta_ajax(`${ruta_interna}listarMetasDirector`, { idArea, idLider }, res => {
    const meta = $(`#tabla_metasDirectors2`).DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "ver" },
        { data: "id" },
        { data: "nombre_accion" },
        { data: "area_est" },
        { data: "usuario_registra" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_metasDirectors2").on('click', "tbody tr", function () {
      $("#tabla_metasDirectors2 tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Ver metas del director selecto */
    $("#tabla_metasDirectors2").on('click', "tr .metaSelect", async function () {
      let datos = meta.row($(this).parent().parent()).data();
      $(`#vice_search`).attr("data-id-meta", datos.id);
      $(`#txt_nombre_reto`).val(datos.reto).attr("data-id", datos.reto_id);
      $(`#txt_nombre_plan_des`).val(datos.meta_plan_desarrollo).attr("data-id", datos.plan_desarrollo_id);
      $(`.indics_content`).html(datos.indicador_estrategico).attr("data-id_ind", datos.indicador_estrategico_id);
      $('#nombreAccionInsti').html(`${datos.nombre_accion}`);
      $(`#vice_search`).val(datos.liderRol);
      $('#modalMetasDirectors2, #modalLiderList').modal('hide');
    });

    /* Ver detalles de la meta institucional selecta */
    $("#tabla_metasDirectors2").on('click', "tr .seeDetails", async function () {
      let datos = meta.row($(this).parent().parent()).data();

      //Si vienen observaciones, entonces se pinta en alter de boostsrap
      if (datos.obs != null && datos.fecha_corrige != null && datos.meta_estado == "Meta_En_Cor") {
        $("#obsBoxx").removeClass("oculto");
        $("#obsBox").html(
          `<strong>- Observación:</strong> ${datos.obs}. <br>
          <strong>- Fecha en que se envió a corrección:</strong> ${datos.fecha_corrige}.`
        );
      } else {
        $("#obsBoxx").addClass("oculto");
        $("#obsBox").html('');
      }

      lastClickedId = datos.id;
      let activeObj = 'detalles';
      tablaActiva = 'detalles';
      active_place(`#modal_admin_meta`, activeObj);
      $('#modal_admin_meta').modal();
      ver_metas_detalles(datos, datos.id);
    });
  });
}

/* Poner titulos a los modales */
const titulo_modal = (modal_id, icono, titulo) => {
  return $(`${modal_id} .modal-title`).html(`${icono} ${titulo}`);
}

/* Cortar textos para resumen */
const cortar_textos = (texto, limite) => {
  if (texto.length > limite) {
    return `${texto.substring(0, limite)}...`;
  } else {
    return `${texto}`;
  }
}

/* Divisas */
const divisas = (valor_dinero) => {
  let num = Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    minimumFractionDigits: 0
  });
  return num.format(valor_dinero);
}

/* Funcion para mostrar info de responsables o de un array deseado */
const mostrar_info = (info, swal_title) => {
  return swal({
    title: swal_title,
    text: info,
    html: info,
    type: "info",
    confirmButtonColor: "#7DA8F0",
    confirmButtonText: "Conitnuar",
    allowOutsideClick: true,
    closeOnConfirm: false,
    closeOnCancel: true
  });
}

/* Funcion que solicita confirmacion del usuario */
const confirm_action = (titulo, msg, tipo, si_btn_text, no_btn_text) => {
  return new Promise(resolve => {
    let btn_color = "";
    if (tipo == "warning") {
      btn_color = "#D9534F";
    } else if (btn_color == info) {
      btn_color = "#7DA8F0";
    }
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
    },
      function (isConfirm) {
        if (isConfirm) {
          resolve(1);
        } else {
          resolve(0);
        }
      });
  });
}

const listarProgramas = (idMeta) => {
  $("#tabla_programs").off('click', "tbody tr");
  $('#tabla_programs').off('click', 'tr .selectProgram');
  $('#tabla_programs').off('click', 'tr .delProgram');
  $('#tabla_programs').off('click', 'tr .verRecs');
  $('#tabla_programs').off('click', 'tr .verAsp');
  consulta_ajax(`${ruta_interna}listarProgramas`, { idMeta }, res => {

    //Contamos los programas que esten guardados en la meta para pintarlos en el input
    if (res) {
      res.forEach(e => {
        if (e.estado == 'Asignado') {
          counter++;
        }
      });
      $('#programaSearch').val(`${counter} programas asignados`);
    }
    //Conitnua la ejecucion

    const programs = $('#tabla_programs').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "programaName" },
        { data: "estado" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_programs").on('click', "tbody tr", function () {
      $("#tabla_programs tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Seleccionar programas */
    $('#tabla_programs').on('click', 'tr .selectProgram', async function () {
      let data = programs.row($(this).parent().parent()).data();
      $('#modal_metas_form #recomendacionesPrograma').attr('data-id-reco', '').val('');
      let savedProgramsRec = await guardar_programa_recomendacion(data.id, lastClickedId);
      if (savedProgramsRec == true) {
        counter = 0;
        listarProgramas(lastClickedId);
        $(`#${div_to_complete}`).val(`${counter} Programas seleccionados`);
      }
    });

    /* Ver recomendaciones del programa seleccionado */
    $('#tabla_programs').on('click', 'tr .verRecs', async function () {
      let data = programs.row($(this).parent().parent()).data();
      $('#modalRecomendacionesPrograms').modal();
      listarRecsPrograms(data.id, lastClickedId);
    });

    /* Eliminar programas */
    $('#tabla_programs').on('click', 'tr .delProgram', async function () {
      let data = programs.row($(this).parent().parent()).data();
      let del = await eliminar_programa_recomendacion(data.id, lastClickedId);
      if (del == true) {
        counter = 0;
        listarProgramas(lastClickedId);
      } else {
        MensajeConClase(res.mensaje, res.tipo, res.titulo);
      }
    });

    /* Ver aspectos positivos */
    $('#tabla_programs').on('click', 'tr .verAsp', async function () {
      let data = programs.row($(this).parent().parent()).data();
      $('#modal_aspectos_positivos').modal();
      listarAspectosPositivos(data.id, lastClickedId);
    });
  });
}

/* Listar las recomendaciones de los programas acreditados */
const listarRecsPrograms = (idPrograma, idMeta) => {
  $('#tabla_recprograms').off('click', 'tr .selectReco');
  $('#tabla_recprograms').off('click', 'tr .delReco');
  consulta_ajax(`${ruta_interna}listarRecsPrograms`, { idPrograma, idMeta }, res => {
    $('#tabla_recprograms').off('click', 'tr .selectReco');
    const info = $('#tabla_recprograms').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "recomendacion" },
        { data: "estado" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $('#tabla_recprograms').on('click', 'tr .selectReco', async function () {
      let data = info.row($(this).parent().parent()).data();
      let saveReco = await saveRecomendacionPrograma(data.vpid, lastClickedId);
      if (saveReco == true && saveReco.tipo != "warning") {
        listarRecsPrograms(idPrograma, lastClickedId);
      } else {
        MensajeConClase(saveReco.mensaje, saveReco.tipo, saveReco.titulo);
      }
    });

    $('#tabla_recprograms').on('click', 'tr .delReco', async function () {
      let data = info.row($(this).parent().parent()).data();
      let del = await delRecomendacionPrograma(data.vpid, data.idmeta);
      if (del == true) {
        listarRecsPrograms(idPrograma, lastClickedId);
      } else {
        MensajeConClase(res.mensaje, res.tipo, res.titulo);
      }
    });
  });
}

/* Guardar programa recomendado */
const guardar_programa_recomendacion = (idPrograma, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}guardar_programa_recomendacion`, { idPrograma, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Eliminar programa recomendado */
const eliminar_programa_recomendacion = (idPrograma, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}eliminar_programa_recomendacion`, { idPrograma, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Guardar recomendacion de programa seleccionado */
const saveRecomendacionPrograma = (idReco, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}saveRecomendacionPrograma`, { idReco, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Eliminar recomendacion de programa seleccionado */
const delRecomendacionPrograma = (idReco, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delRecomendacionPrograma`, { idReco, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Listar aspectos positivos */
const listarAspectosPositivos = (idPrograma, idMeta) => {
  $('#tabla_aspectos').off('click', 'tr .selectAsp');
  $('#tabla_aspectos').off('click', 'tr .delAsp');
  consulta_ajax(`${ruta_interna}listarAspectosPositivos`, { idPrograma, idMeta }, res => {
    const info = $('#tabla_aspectos').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "aspecto_positivo" },
        { data: "estado" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $('#tabla_aspectos').on('click', 'tr .selectAsp', async function () {
      let data = info.row($(this).parent().parent()).data();
      let saveAsp = await saveAspectosPositivos(data.id_aspecto, lastClickedId);
      if (saveAsp == true && saveAsp.tipo != "warning") {
        listarAspectosPositivos(idPrograma, lastClickedId);
      } else {
        MensajeConClase(saveAsp.mensaje, saveAsp.tipo, saveAsp.titulo);
      }
    });

    $('#tabla_aspectos').on('click', 'tr .delAsp', async function () {
      let data = info.row($(this).parent().parent()).data();
      let del = await delAspectosPositivos(data.id_aspecto, data.idmeta);
      if (del == true) {
        listarAspectosPositivos(idPrograma, lastClickedId);
      } else {
        MensajeConClase(res.mensaje, res.tipo, res.titulo);
      }
    });
  });
}

/* Guardar aspectos positivos del programa seleccionado */
const saveAspectosPositivos = (idAsp, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}saveAspectosPositivos`, { idAsp, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Eliminar aspectos positivos del programa seleccionado */
const delAspectosPositivos = (idAsp, idMeta) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delAspectosPositivos`, { idAsp, idMeta }, res => {
      resolve(res);
    });
  });
}

/* Listar programas CUC para asignar a las personas cuando se va a agregar el director */
//MEJORAR CONTADOR
let contar = 0;
const listarProgramasCuc = (idPersona) => {
  $('#cucPrograms').off('click', 'tr .selectProgram');
  $('#cucPrograms').off('click', 'tr .delProgram');
  consulta_ajax(`${ruta_interna}listarProgramasCuc`, { idPersona }, res => {

    //Contamos cuantos programas tienen activos las personas para ponerlos en los inputs necesarios.
    res.forEach(e => {
      if (e.programaAsignado != null && e.personaAsignado != null) {
        cantidadProgramas++;
      }
    });
    $('#programaBuscar').val(`${cantidadProgramas} programas asignados`);
    //Continua la ejecucion

    const programs = $('#cucPrograms').DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: res,
      columns: [
        { data: "programaName" },
        { data: "estado" },
        { data: "acciones" },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $('#cucPrograms').on('click', 'tr .selectProgram', async function () {
      let data = programs.row($(this).parent().parent()).data();
      let saveprogram = await asignarProgramasCuc(data.id, directorSelected);
      if (saveprogram == true) {
        cantidadProgramas = 0;
        listarProgramasCuc(directorSelected);
        $('#programaBuscar').val(`${cantidadProgramas} programas asignados`);
      } else {
        MensajeConClase(saveprogram.mensaje, saveprogram.tipo, saveprogram.titulo);
      }
    });

    $('#cucPrograms').on('click', 'tr .delProgram', async function () {
      let data = programs.row($(this).parent().parent()).data();
      let delProgram = await delProgramasCuc(data.id, directorSelected);
      if (delProgram == true) {
        cantidadProgramas = 0;
        listarProgramasCuc(directorSelected);
      } else {
        MensajeConClase(delProgram.mensaje, delProgram.tipo, delProgram.titulo);
      }
    });
  });
}

/* Asignar programas en el adminstrar */
const asignarProgramasCuc = (idPrograma, idPersona) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}asignarProgramasCuc`, { idPrograma, idPersona }, res => {
      resolve(res);
    });
  });
}

/* Asignar programas en el adminstrar */
const delProgramasCuc = (idPrograma, idPersona) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}delProgramasCuc`, { idPrograma, idPersona }, res => {
      resolve(res);
    });
  });
}

/* Reset cronogramas */
const resetCrono = (idMeta) => {
  consulta_ajax(`${ruta_interna}resetCrono`, { idMeta }, res => {
    if (res.tipo == 'success') {
      $('#form_docs_soporte').trigger('reset');
      trimestres_selected = {};
      action = '';
      acciones_array = [];
      acciones_adicionales = [];
      actions_to_del = [];
      cronoSelected = '';
      $(`#modal_cronograma .acciones_asignadas`).html(`<option value="">0 Soportes agregados</option>`);
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      traer_cronograma(idMeta);
      setTimeout(cerrar_swals, 1001);
      $('#modal_numeric_crono .action_amount').val('');
    } else {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    }
  });
}

/* Funcion para darle el active al item seleccionado */
const active_place = (modal_id, target) => {
  //Cinta de opciones
  $(`${modal_id} li`).removeClass('active');
  $(`${modal_id} li[id="${target}"]`).addClass('active');

  //Tablas
  $(`${modal_id} div[data-sw="sw"]`).hide();
  $(`${modal_id} div[data-place="${tablaActiva}"]`).fadeIn('fast');
}

/* Traer los factores institucionales en detalles */
const traerFactoresIns = (idMeta) => {
  $("#tabla_lineamientos_details").off('click', "tbody tr");
  $("#tabla_lineamientos_details").off('click', "tr .verCaracts");
  consulta_ajax(`${ruta_interna}traerFactoresIns`, { idMeta }, res => {
    const info = $(`#tabla_lineamientos_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'factorName' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_lineamientos_details").on('click', "tbody tr", function () {
      $("#tabla_lineamientos_details tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Ver caracteristicas del factor seleccionado */
    $("#tabla_lineamientos_details").on('click', "tr .verCaracts", function () {
      let data = info.row($(this).parent().parent()).data();
      $('#modal_caracts_factores').modal();
      traerCaracteristicasFactoresIns(idMeta, data.valora);
    });
  });
}

const traerCaracteristicasFactoresIns = (idMeta, valora) => {
  $("#tabla_factorsCarats_details").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}traerCaracteristicasFactoresIns`, { idMeta, valora }, res => {
    const info = $(`#tabla_factorsCarats_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'caractName' }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_factorsCarats_details").on('click', "tbody tr", function () {
      $("#tabla_factorsCarats_details tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Traer lista de responsables en los detalles de la meta */
const traerResponsables = (idMeta) => {
  $("#tabla_responsables_details").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}traerResponsables`, { idMeta, valora }, res => {
    const info = $(`#tabla_responsables_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'full_name' },
        { data: 'cargo_sap' },
        { data: 'usuario' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_responsables_details").on('click', "tbody tr", function () {
      $("#tabla_responsables_details tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Traer presupuesto de la meta seleccionada y renderizar en el detalles de la meta */
const traerPresupuestos = (idMeta) => {
  $("#tabla_presupuesto_details").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}traerPresupuestos`, { idMeta }, res => {
    const info = $(`#tabla_presupuesto_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'categoriaName' },
        { data: 'tipoName' },
        { data: 'itemName' },
        { data: 'descripcion' },
        {
          data: 'valor_solicitado', render: function (data) {
            return `<span>${divisas(data)}</span>`;
          }
        },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_presupuesto_details").on('click', "tbody tr", function () {
      $("#tabla_presupuesto_details tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Traer cronograma de la meta seleccionada y renderiza en el detalles de la meta */
const traerCronograma = (idMeta) => {
  $("#tabla_cronograma_details").off('click', "tbody tr");
  $("#tabla_cronograma_details").off('click', "tr .verDocsName");
  consulta_ajax(`${ruta_interna}traerCronograma`, { idMeta }, res => {
    const info = $(`#tabla_cronograma_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'triName' },
        { data: 'cantidad' },
        { data: 'acciones' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_cronograma_details").on('click', "tbody tr", function () {
      $("#tabla_cronograma_details tr").removeClass("warning");
      $(this).addClass("warning");
    });

    /* Ver el o los nombres de documentos soportes cargados a ese semestre */
    $("#tabla_cronograma_details").on('click', "tr .verDocsName", function () {
      let data = info.row($(this).parent().parent()).data();
      $('#modal_docs_soporte').modal();
      traerDocsSoporte(idMeta, data.cronoId);
    });
  });
}

/* Traer los docs soporte de un trimestre el cual pertenece a una meta en detalles de meta */
const traerDocsSoporte = (idMeta, cronoId) => {
  $("#tabla_docsSoporte_details").off('click', "tbody tr");
  consulta_ajax(`${ruta_interna}traerDocsSoporte`, { idMeta, cronoId }, res => {
    const info = $(`#tabla_docsSoporte_details`).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      ordering: false,
      data: res,
      columns: [
        { data: 'docName' },
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    /* Eventos activados */
    $("#tabla_docsSoporte_details").on('click', "tbody tr", function () {
      $("#tabla_docsSoporte_details tr").removeClass("warning");
      $(this).addClass("warning");
    });
  });
}

/* Traer lista de formatos asignados del lider del director que gestiona o desea crear una accion */
const formatosAsignados = () => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}formatosActivos`, {}, res => {
      resolve(res);
    });
  });
}

/* Listar los formatos activos de una persona */
const listar_formatos_activos = (torender) => {
  let datos = {};
  return new Promise(resolve => {
    $("#modal_formatos_asignados #formatos_select").html("");
    $("#modal_formatos_asignados .opcion__cont").off('click');

    torender.forEach(element => {
      $("#formatos_select").append(
        `<div class="opcion__cont opcion__cont_large" data-id_text="${element.area}" data-id="${element.idFormato}" id="${element.idFormato}" data-idaux="${element.idaux}" data-area_name="${element.area}" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="${element.descripcion}">
        <img src="${img_path}${element.ruta_img}" data-id_text="${element.area}" data-id="${element.idFormato}" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
        <span class="opcion__span" data-id_text="${element.area}" data-id="${element.idFormato}">${element.area}</span>
      </div>`
      );
    });

    $("#modal_formatos_asignados .opcion__cont").on('click', async function (e) {
      formatoElegido = e.target.dataset.id;
      ask = false;
      let close = await timeCheck(formatoElegido);
      if (close == false) {
        let titulo = '¡Atención!';
        let msg = 'Al dar clic en <i style="color:#D0504C;"><strong>"Si, Continuar"</strong></i>, creará una meta automáticamente la cual podrá empezar a gestionar.';
        let tipo = 'warning';
        let answer = await confirm_action(titulo, msg, tipo, 'Si, Continuar', 'No, Cancelar');
        if (answer == 1) {
          datos = { "id_area_selected": area_est_selected, 'idFormato': formatoElegido };
          guardar_metas_accion(datos);
        } else {
          return false;
        }
      } else {
        MensajeConClase(close.mensaje, close.tipo, close.titulo);
      }
      resolve(formatoElegido);
    });

    $('[data-toggle="popover"]').popover();
  });
}

/* Funcion para obligar a que sea numeros o string */
const num_o_string = (tipo, key) => {
  if (tipo == "int") {
    if (key < 48 || key > 57) {
      return false;
    }
  } else if (tipo == "str") {
    if (key > 47 && key < 58) {
      return false;
    }
  }
}

/* Funcion cerrar swals */
function cerrar_swals() {
  swal.close();
}

/* Apagar clicks en btns */
const btns_off = (parent_place, array_inputs) => {
  if (array_inputs) {
    array_inputs.forEach(element => {
      $(`${parent_place} #${element}`).off('click');
    });
  }
}

const hideDivs = (dataId) => {
  $(`div[data-id="${dataId}"]`).hide();
}
/* Comprobar si es hora habil para permitir realizar metas */
const timeCheck = (idFormato = '') => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta_interna}timeCheck`, { idFormato }, res => {
      resolve(res);
    });
  });
}

/* Listar de forma global los datos de cada formato */
const datosFormatos = async () => {
  fi = await find_idParametro('formato_institucional');
  fp = await find_idParametro('formato_programa');
}

//Hacer switch para mostrar tabla dinamica.
/* Generar datos DB - Programas */
const generarDatosDBPro = () => {
  $("#modal_db_view div[data-sw='sw']").hide();
  MensajeConClase("", "waiting_inf", "Oops...");
  consulta_ajax(`${ruta_interna}generarDatosDBPro`, {}, res => {
    if (res.tipo == 'warning' || res.tipo == 'error') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    } else {
      const info = $(`#tabla_dataGenPro_details`).DataTable({
        destroy: true,
        searching: true,
        processing: true,
        ordering: true,
        data: res,
        columns: [
          { data: 'codigo' },
          { data: 'usuario_registra' },
          { data: 'cargo_sap' },
          { data: 'area_estrategica' },
          { data: 'reto' },
          { data: 'meta_plan_desarrollo' },
          { data: 'indicador_estaretgico' },
          { data: 'meta_PA' },
          { data: 'indicador_operativo' },
          { data: 'tipo_indicador_operativo' },
          { data: 'cifra_referencia' },
          { data: 'meta' },
          { data: 'nombre_accion' },
          { data: 'factores' },
          { data: 'factor_caracteristicas' },
          { data: 'responsables' },
          { data: 'trimestre' },
          { data: 'cantidad' },
          { data: 'nombre_docs_soporte' },
          { data: 'formato' },
          { data: 'estado' },
          { data: 'programas_seleccionados' },
          { data: 'recomendaciones_programa' },
          { data: 'aspecto_positivo_programa' },
          { data: 'dependencia' },
          { data: 'fecha_registra' },
          { data: 'accion_institucional' },
          { data: 'codigo_institucional' }
        ],
        language: get_idioma(),
        dom: "Bfrtip",
        buttons: get_botones()
      });
    }
    swal.close();
    $("#modal_db_view").modal();
    active_place(`#modal_db_view`, "datosGenerales");
    $("#modal_db_view .container_detallesPro_data").show();
    $("#modal_db_view .container_detalles_presupuesto").hide();
  });
}

/* Generar resumen de preuspuestos de todas las acciones */
const generarDatosDBpresu = () => {
  $("#modal_db_view div[data-sw='sw']").hide();
  MensajeConClase("", "waiting_inf", "Oops...");
  consulta_ajax(`${ruta_interna}generarDatosDBpresu`, {}, res => {
    if (res.tipo == 'warning' || res.tipo == 'error') {
      MensajeConClase(res.mensaje, res.tipo, res.titulo);
      return false;
    } else {
      const info = $(`#tabla_presu_details`).DataTable({
        destroy: true,
        searching: true,
        processing: true,
        ordering: true,
        data: res,
        columns: [
          { data: 'codigo' },
          { data: 'categoria_presupuesto' },
          { data: 'tipo_presupuesto' },
          { data: 'item_presupuesto' },
          { data: 'descripcion' },
          {
            data: 'valor_solicitado', render: function (data) {
              return `<span>${divisas(data)}</span>`
            }
          },
          {
            data: 'valor_aprobado', render: function (data) {
              return `<span>${divisas(data)}</span>`
            }
          },
          {
            data: 'valor_ejecutado', render: function (data) {
              return `<span>${divisas(data)}</span>`
            }
          },
          { data: 'cuenta_sap' },
          { data: 'estado_presupuesto' },
          { data: 'usuario_registra' },
          { data: 'dependencia' },
          { data: 'meta_estado' },
          { data: 'formato' }
        ],
        language: get_idioma(),
        dom: "Bfrtip",
        buttons: get_botones()
      });
      $("#modal_db_view .container_detalles_presupuesto").show();
      swal.close();
    }
  });
}