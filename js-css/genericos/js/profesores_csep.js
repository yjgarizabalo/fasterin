let ruta = `${Traer_Server()}index.php/profesores_csep_control/`;
let ruta_soportes = "archivos_adjuntos/profesores/soportes/";
let id_valor_parametro = null;
let id_valor_relacion = null;
let profesor_sele = null;
let id_indicador_prof = '';
let id_asignatura_prof = '';
let id_formacion_prof = '';
let id_objetivo_prof = '';
let id_atencion_prof = '';
let id_perfil_prof = '';
let id_hora_prof = '';
let id_linea_prof = '';
let id_profesor_personal = '';
let id_personal = '';
let data_firma = { plan: 0, tipo: '' };
let excelFile;
let callback_activo = () => { }
var str;
$(document).ready(function () {

  $("#body_plan .oculto").fadeIn(1000);
  $("#btn_administrar").click(() => {
    $("#modal_administrar_modulo").modal();
  });
  $("#btn_subir").click(() => {
    $("#modal_importar_excel").modal();
  });
  $("#btn_buscar_persona").click(() => {
    let dato = $("#txt_buscar_persona").val().trim();
    let periodo = $('.cbx_periodos').val();
    buscar_profesor(dato, periodo);
    //dato.length == 0 ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops.!") : buscar_profesor(dato);
  });

  $("#txt_buscar_persona").keypress(e => {
    if (e.which == 13) {
      let dato = $("#txt_buscar_persona").val().trim();
      let periodo = $('.cbx_periodos').val();
      buscar_profesor(dato, periodo);
      //dato.length == 0 ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops.!") : buscar_profesor(dato);
    }
  });

  $("#cbx_listado_parametros").change(function () {
    let idparametro = $(this).val();
    listar_valores_parametros(idparametro);
  });

  $(".cbx_periodos").change(function () {
    let periodo = $('.cbx_periodos').val();
    $("#descargar_info").attr("href", `${Traer_Server()}index.php/profesores/descargar_excel/${periodo ? periodo : 'actual'}`);
    let dato = $("#txt_buscar_persona").val().trim();
    let filtro = $('.cbx_filtro_firma').val();
    buscar_profesor(dato, periodo, filtro);
  });

  $(".cbx_filtro_firma").change(function () {
    let filtro = $('.cbx_filtro_firma').val();
    let dato = $("#txt_buscar_persona").val().trim();
    let periodo = $('.cbx_periodos').val();
    buscar_profesor(dato, periodo, filtro);
  });

  $("#cbx_listado_parametros_r").change(function () {
    let idparametro = $(this).val();
    if (id_valor_parametro) listar_valores_parametros_relaciones(id_valor_parametro, idparametro)
    else {
      MensajeConClase('Seleccione valor parametro', 'info', 'Oops.!');
      $('#cbx_listado_parametros_r').val('');
    }
  });

  $("#btn_nuevo_valor").click(() => {
    let idparametro = $('#cbx_listado_parametros').val();
    idparametro.length == 0 ? MensajeConClase("Seleccione Parametro.", "info", "Oops.!") : $("#modal_valor_parametro").modal();;
  });

  $("#btn_cambiar_periodo_profesor").click(() => {
    let periodo = $('.cbx_periodos').val();
    periodo.length == 0 ? MensajeConClase("Seleccione Periodo.", "info", "Oops.!") : window.open(`${Traer_Server()}index.php/plan_trabajo/${id_personal}/${periodo}`);
  });

  $("#btn_filtrar").click(() => {
    let periodo = $('.cbx_periodos').val();
    periodo.length == 0 ? MensajeConClase("Seleccione Periodo.", "info", "Oops.!") : buscar_profesor('', periodo);
  });

  $("#form_valor_parametro").submit(() => {
    let idparametro = $('#cbx_listado_parametros').val();
    guardar_valor_parametro(idparametro);
    return false;
  });

  $("#form_modificar_valor").submit(() => {
    modificar_valor_parametro(id_valor_parametro);
    return false;
  });

  $("#btn_imprimir_plan").click(() => {
    let imprimir = document.querySelector("#detalle_profesor");
    imprimirDIV(imprimir, true);
  });

  $("#form_guardar_plan_profesor").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_plan_profesor"));
    data.append("id_persona", profesor_sele.id_persona);
    guardar_plan_profesor(data);
    return false;
  });
  $("#form_guardar_indicador").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_indicador"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_indicador_prof);
    guardar_indicador(data);
    return false;
  });
  $("#form_guardar_asignatura").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_asignatura"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_asignatura_prof);
    guardar_asignatura(data);
    return false;
  });
  $("#form_guardar_formacion").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_formacion"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_formacion_prof);
    guardar_formacion(data);
    return false;
  });

  $("#form_guardar_formacion_personal").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_formacion_personal"));
    data.append("personal", true);
    data.append("id_profesor", id_profesor_personal);
    guardar_formacion(data, true);
    return false;
  });

  $("#form_guardar_horario_atencion").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_horario_atencion"));
    data.append("id_profesor", id_profesor_personal);
    guardar_atencion_plan_personal(data);
    return false;
  });

  $("#form_guardar_objetivo").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_objetivo"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_objetivo_prof);
    guardar_objetivo(data);
    return false;
  });
  $("#form_guardar_atencion").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_atencion"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_atencion_prof);
    guardar_atencion(data);
    return false;
  });
  $("#form_guardar_perfil").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_perfil"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_perfil_prof);
    guardar_perfil(data);
    return false;
  });
  $("#form_guardar_hora").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_hora"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_hora_prof);
    guardar_hora(data);
    return false;
  });
  $("#form_guardar_linea").submit(() => {
    let data = new FormData(document.getElementById("form_guardar_linea"));
    data.append("id_profesor", profesor_sele.id);
    data.append("id", id_linea_prof);
    guardar_linea(data);
    return false;
  });
  $("#btn_adm_indicadores").click(() => {
    let { id } = profesor_sele;
    if (id) listar_indicadores(id);
    else mensaje_info();
  });
  $("#btn_adm_asignaturas").click(() => {
    let { id } = profesor_sele;
    if (id) listar_asignaturas(id);
    else mensaje_info();
  });
  $("#btn_adm_formacion").click(() => {
    let { id } = profesor_sele;
    if (id) listar_formacion(id);
    else mensaje_info();
  });
  $("#btn_adm_objetivos").click(() => {
    let { id } = profesor_sele;
    if (id) listar_objetivos(id);
    else mensaje_info();
  });
  $("#btn_adm_atencion").click(async () => {
    let { id } = profesor_sele;
    let { horas_atencion, minutos_atencion, horas_mentoring, minutos_mentoring } = await calcular_horas_docente(id, 'Hor_Pae');
    $("#horas_estu").html(`${horas_atencion ? `${horas_atencion} Hora(s)` : ''} ${minutos_atencion ? `${minutos_atencion} Minuto(s) ` : ''}`);
    $("#horas_men").html(`${horas_mentoring ? `${horas_mentoring} Hora(s)` : ''} ${minutos_mentoring ? `${minutos_mentoring} Minuto(s) ` : ''}`);
    if (id) listar_atencion(id);
    else mensaje_info();
  });
  $("#btn_adm_perfiles").click(() => {
    let { id } = profesor_sele;
    if (id) listar_perfiles(id);
    else mensaje_info();
  });
  $("#btn_adm_horas").click(() => {
    let { id } = profesor_sele;
    if (id) listar_horas(id);
    else mensaje_info();
  });
  $("#btn_lineas").click(() => {
    let { id } = profesor_sele;
    if (id) listar_lineas(id);
    else mensaje_info();
  });
  $("#btn_nuevo_indicador").click(() => {
    id_indicador_prof = '';
    $("#modal_guardar_indicador .modal-title").html(`<span class="fa fa-plus"></span> Nuevo Indicador`);
    $("#modal_guardar_indicador").modal();
  });
  $("#btn_nueva_asignatura").click(() => {
    id_asignatura_prof = '';
    $("#modal_guardar_asignatura .modal-title").html(`<span class="fa fa-list"></span> Nueva Asignatura`);
    $("#modal_guardar_asignatura").modal();
  });
  $("#btn_nueva_formacion").click(() => {
    id_formacion_prof = '';
    $("#modal_guardar_formacion .modal-title").html(`<span class="fa fa-book"></span> Nueva Formación`);
    $("#modal_guardar_formacion").modal();
  });
  $("#btn_nuevo_objetivo").click(() => {
    id_objetivo_prof = '';
    $("#form_guardar_objetivo").get(0).reset();
    $("#modal_guardar_objetivo .modal-title").html(`<span class="fa fa-check-circle-o"></span> Nuevo Objetivo`);
    $("#modal_guardar_objetivo").modal();
  });
  $("#btn_nueva_atencion").click(async () => {
    id_atencion_prof = '';
    $("#form_guardar_atencion").get(0).reset(); 
    $("#modal_guardar_atencion .modal-title").html(`<span class="fa fa-calendar"></span> Nuevo Horario Atención`);
    let asig = await obtener_asignaturas_agrupadas(profesor_sele.id)
    pintar_datos_combo_general(asig, '.cbx_asignatura_atencion', 'Seleccione Asignatura')    
    $("#modal_guardar_atencion").modal();
  });
  $("#btn_nuevo_perfil").click(() => {
    id_perfil_prof = '';
    $("#modal_guardar_perfil .modal-title").html(`<span class="fa fa-sitemap"></span> Nuevo Perfil`);
    $("#modal_guardar_perfil").modal();
  });
  $("#btn_nueva_hora").click(() => {
    id_hora_prof = '';
    $("#modal_guardar_hora .modal-title").html(`<span class="fa fa-clock-o"></span> Nueva Hora`);
    $("#modal_guardar_hora").modal();
  });
  $("#btn_nuevo_linea").click(() => {
    id_linea_prof = '';
    $("#form_guardar_linea").get(0).reset();
    $("#modal_guardar_linea .modal-title").html(`<span class="fa fa-random"></span> Nueva Linea`);
    $("#modal_guardar_linea").modal();
  });

  $("#form_guardar_plan_profesor select[name = 'id_departamento']").change(async function () {
    let departamento = $(this).val();
    if (departamento) {
      let programas = await obtener_valores_permisos(departamento, 86, 'inner');
      pintar_datos_parametros_combo(programas, '.cbx_programas_add', 'Seleccione Programa');
    }
  });
  $("#form_guardar_linea select[name = 'id_linea']").change(async function () {
    let linea = $(this).val();
    if (linea) {
      let sub_lineas = await obtener_valores_permisos(linea, 88, 'inner');
      pintar_datos_parametros_combo(sub_lineas, '.cbx_sub_linea', 'Seleccione Sub-Linea');
    }
  });
  $("#form_buscar_persona").submit(e => {
    e.preventDefault();
    const dato = $("#txt_dato_buscar").val();
    buscar_persona(dato, callback_activo);
  });
  $("#btn_buscar_persona_parametro").click(() => {
    callback_activo = (data) => {
      $("#modal_asignar_permiso").modal();
      $("#id_persona_permiso").val(data.id);
      /*confirmar_accion(() => {
        asignar_persona_parametro(id_valor_parametro, data.id);
      });*/
    }
    buscar_persona("***wdwd")
    $("#modal_buscar_persona").modal();
  });

  $("#form_guardar_permiso").submit(e => {
    e.preventDefault();
    let id_persona = $("#form_guardar_permiso input[name='id_persona']").val();
    let tipo = $("#form_guardar_permiso select[name='tipo']").val();
    asignar_persona_parametro(id_valor_parametro, id_persona, tipo);
  });

  $(`#btn_agregar_sop_formacion`).click(function () {
    $("#modal_enviar_archivos").modal();
  });

  $("#btn_generar_filtro_ind").click(async () => {
    let fecha_inicial = $("#fecha_inicial").val();
    let fecha_final = $("#fecha_final").val();
    let indicadores = await obtener_indicadores_profesor(id_profesor_personal, '', fecha_inicial, fecha_final);
    pintar_indicadores_profesor_personal(indicadores);
  });

  $("#btn_limpiar_filtro_ind").click(async () => {
    $("#fecha_inicial").val('');
    $("#fecha_final").val('');
    let indicadores = await obtener_indicadores_profesor(id_profesor_personal);
    pintar_indicadores_profesor_personal(indicadores);
  });

  $("#cargar_firma_digital").submit(e => {
    e.preventDefault();
    guardar_firma()
  });

  $(`#form_soporte_academico`).submit(e => {
		e.preventDefault();
		callback_activo();
		return false;
	});

  $("#ver_indicadores").click(function () {
		$("#modal_indicadores").modal("show");
	});

  $("#btn_modificar_firma").click(function () {
		$("#modal_modificar_firma").modal("show");
	});

  $("#modificar_firma_digital").submit(e => {
    e.preventDefault();
    modificar_firma_digital()
  });
  
});
const buscar_profesor = (dato, periodo = '', filtro_firma = '') => {
  consulta_ajax(`${ruta}buscar_profesor`, { dato, periodo, filtro_firma }, (resp) => {
    $(`#tabla_profesores tbody`).off('click', 'tr td .abrir').off('click', 'tr td .administrar').off('click', 'tr td .firma').off('click', 'tr td .firma_mod').off('dblclick', 'tr').off('click', 'tr');
    const myTable = $("#tabla_profesores").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        {
          "render": function (data, type, full, meta) {
            let { id, id_persona, periodo } = full;
            resp = (id) ? `<a style="background-color: white;color: black; width: 100%;" class="pointer form-control" href='${Traer_Server()}index.php/plan_trabajo/${id_persona}/${periodo}' class="sinlink" target="_blank">Ver</a>`
              : `<span title="Sin PLan" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>`;
            return resp;
          }
        },
        { "data": "periodo" },
        { "data": "nombre_completo" },
        { "data": "identificacion" },
        { "data": "programa" },
        { "data": "dedicacion" },
        { "data": "estado_act" },
        { "data": "accion" },
      ],
      language: get_idioma(),
      dom: 'Bfrtip',
      buttons: [],
    });

    $('#tabla_profesores tbody').on('click', 'tr', function () {
      let { id_persona, identificacion, id } = myTable.row(this).data();
      profesor_sele = { id_persona, identificacion, id };
      $("#tabla_profesores tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $('#tabla_profesores tbody').on('dblclick', 'tr', function () {
      let data = myTable.row(this).data();
      ver_detalle_profesor(data);
    });

    $('#tabla_profesores tbody').on('click', 'tr td .abrir', function () {
      let data = myTable.row($(this).parent().parent()).data();
      ver_detalle_profesor(data);
    });
    $('#tabla_profesores tbody').on('click', 'tr td .administrar', function () {
      let data = myTable.row($(this).parent().parent()).data();
      mostrar_data_guardar_profesor(data);
    });
    $('#tabla_profesores tbody').on('click', 'tr td .firma', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      data_firma = { plan: id, tipo: 'decano' };
      $('#cargar_firma_digital').get(0).reset();
      $("#cargar_firma").modal('show');
    });
    $('#tabla_profesores tbody').on('click', 'tr td .firma_mod', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      data_firma = { plan: id, tipo: 'decano' };
      $('#modificar_firma_digital').get(0).reset();
      $("#modal_modificar_firma").modal('show');
    });
  });

}

const ver_detalle_profesor = async (data, container = '#detalle_profesor') => {
  let { nombre_completo, identificacion, dedicacion, escalafon, contrato, fecha_inicio, fecha_fin, departamento, programa, area_conocimiento, grupo, categoria_col, id, ev_360, ev_docente, indice_h, citas, id_persona, id_departamento, fecha_firma, firma_profesor, firma_decano } = data;
  if (id) {
    $("#btn_descargar_plan").attr("href", `${Traer_Server()}index.php/descargar_plan_trabajo/${id_persona}`);
    $("#btn_descargar_plan").show();
    $("#btn_imprimir_plan").show();
  } else {
    $("#btn_descargar_plan").hide();
    $("#btn_imprimir_plan").hide();
  }
  let new_fecha_firma = ''
  if (fecha_firma) {
    let f = new Date(fecha_firma);
    let year  = f.getFullYear()
    let month = f.getMonth() + 1
    let day   = f.getDate()
    if (month < 10) month = '0'+ month
    new_fecha_firma = year + "-"+ month  + "-" + day;
  } else {
    new_fecha_firma = '______-____-____'
  }
  $(`${container} .fecha_firma`).html(new_fecha_firma);
  $(`${container} .imagen_empleado_plan`).attr("src", `${Traer_Server()}/Fotos/${identificacion}.jpg`);
  $(`${container} .identificacion`).html(identificacion);
  $(`${container} .dedicacion`).html(dedicacion);
  $(`${container} .escalafon`).html(escalafon);
  $(`${container} .contrato`).html(contrato);
  $(`${container} .fecha_inicio`).html(fecha_inicio);
  $(`${container} .fecha_fin`).html(fecha_fin);
  $(`${container} .departamento`).html(departamento);
  $(`${container} .programa`).html(programa);
  $(`${container} .area_conocimiento`).html(area_conocimiento);
  $(`${container} .grupo`).html(grupo);
  $(`${container} .categoria_col`).html(categoria_col);
  $(`${container} .ev_360`).html(ev_360);
  $(`${container} .ev_docente`).html(ev_docente);
  $(`${container} .indice_h`).html(indice_h);
  $(`${container} .citas`).html(citas);
  $(`${container} .remitido_ced`).html(ev_docente < 4 ? 'SI' : 'NO');
  let formacion = await obtener_formacion_profesor(id);
  pintar_formacion_profesor(formacion, container);
  let indicadores = await obtener_indicadores_profesor(id);
  pintar_indicadores_profesor(indicadores, container);
  let asignaturas = await obtener_asignaturas_profesor(id);
  pintar_asignaturas_profesor(asignaturas, container);
  let atencion = await obtener_atencion_profesor(id);
  pintar_antencion_profesor(atencion, container);
  let horas = await obtener_horas_programa_profesor(id);
  pintar_horas_programa_profesor(horas, container);
  let objetivos = await obtener_objetivos_profesor(id);
  pintar_objetivos_profesor(objetivos, container);
  let perifles = await obtener_perfiles_profesor(id);
  pintar_perfiles_profesor(perifles, container);
  let lineas = await obtener_lineas_profesor(id);
  pintar_lineas_profesor(lineas, container);
  let politicas = await obtener_valores_parametros(101);
  pintar_politicas_profesor(politicas, container);
  let notas = await obtener_valores_parametros(102);
  pintar_notas_profesor(notas, container);
  let director = await obtener_parametros_personas(id_departamento, '', '', "pp.tipo = 'decano'");
  $(`${container} #conta_directores`).html('');
  director.forEach(({ nombre_completo }) => {
    $(`${container} #conta_directores`).append(`
    <div class='espacio'>
    ${firma_decano ? `<img class='img_firma' src='${Traer_Server()}archivos_adjuntos/profesores/firmas/${firma_decano}'>` : "<hr class='firma_espacio'>"}
    <h4>Decano de Departamento</h4>
    <h4 class='nombre_completo_director'>${nombre_completo}</h4>
  </div>
    `);

  })
  $(`${container} #conta_profesor`).html(`
  <div class='espacio'>
    ${firma_profesor ? `<img class='img_firma' src='${Traer_Server()}archivos_adjuntos/profesores/firmas/${firma_profesor}'>` : "<hr class='firma_espacio'>"}
    <h4>Profesor</h4>
    <h4 class='nombre_completo'>${nombre_completo}</h4>
  </div>
  `)

  $("#modal_detalle_profesor").modal();
}
const obtener_formacion_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}formacion_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_indicadores_profesor = (id_profesor, tipo = '', fecha_inicio = null, fecha_fin = null) => {
  return new Promise(resolve => {
    let url = `${ruta}indicadores_profesor`;
    consulta_ajax(url, { id_profesor, tipo, fecha_inicio, fecha_fin }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_asignaturas_agrupadas = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_asignaturas_agrupadas`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}

const obtener_asignaturas_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}asignaturas_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_horas_programa_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}horas_programa_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_atencion_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}atencion_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_objetivos_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}objetivos_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_perfiles_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}perfiles_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_lineas_profesor = (id_profesor) => {
  return new Promise(resolve => {
    let url = `${ruta}lineas_profesor`;
    consulta_ajax(url, { id_profesor }, (resp) => {
      resolve(resp);
    });
  });
}
const pintar_formacion_profesor = (data, container) => {
  $(".tr_formacion").remove();
  let pintar = data.length > 0 ? '' : `<tr class='tr_formacion'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ formacion, nombre }) => { pintar = `${pintar}<tr class='tr_formacion'><td class="ttitulo">${formacion}</td> <td colspan='5'>${nombre}</td></tr>` });
  $(`${container} .formacion`).after(pintar);
}
const pintar_indicadores_profesor = (data, container) => {
  $(".tr_indicadores").remove();
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_indicadores'><td>Nombre</td><td>Fecha Inicial</td><td>Estado Inicial</td><td>Fecha Fin</td><td>Estado Fin</td><td>Estado Actual</td></tr>` : `<tr class='tr_indicadores'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ nombre, estado_inicial, fecha_inicial, estado_final, fecha_final, estado_actual }) => { pintar = `${pintar}<tr class='tr_indicadores'><td>${nombre}</td> <td>${fecha_inicial}</td> <td>${estado_inicial}</td> <td>${fecha_final}</td> <td>${estado_final}</td> <td>${estado_actual}</td></tr>` });
  $(`${container} .indicadores`).after(pintar);
}
const pintar_antencion_profesor = (data, container) => {
  $(".tr_atencion").remove();
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_atencion'><td>Día</td><td>Inicio</td><td>Fin</td><td colspan='3'>Lugar</td></tr>` : `<tr class='tr_atencion'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ hora_inicio, hora_fin, lugar, nombre }) => { pintar = `${pintar}<tr class='tr_atencion'><td>${nombre}</td> <td>${hora_inicio}</td> <td>${hora_fin}</td> <td colspan='3'>${lugar}</td></tr>` });
  $(`${container} .atencion`).after(pintar);
}
const pintar_asignaturas_profesor = (data, container) => {
  $(".tr_asignaturas").remove();
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_asignaturas'><td>Nombre</td><td>Creditos</td><td>Grupo</td><td>Día</td><td>Horario</td><td>Salón</td></tr>` : `<tr class='tr_asignaturas'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ creditos, grupo, horario, nombre, cupo, salon, dia }) => { pintar = `${pintar}<tr class='tr_asignaturas'><td>${nombre}</td> <td>${creditos}</td> <td>${grupo}</td> <td>${dia}</td> <td>${horario}</td> <td>${salon}</td></tr>` });
  $(`${container} .asignaturas`).after(pintar);
}
const pintar_horas_programa_profesor = (data, container) => {
  $(".tr_horas_programas").remove();
  let n_horas = 0;
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_horas_programas'><td colspan='4'>Programa</td><td>Hora</td><td>Cantidad</td>` : `<tr class='tr_horas_programas'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ programa, hora, cantidad }) => {
    n_horas = n_horas + parseInt(cantidad);
    pintar = `${pintar}<tr class='tr_horas_programas'><td colspan='4'>${programa}</td> <td>${hora}</td> <td>${cantidad}</td></tr>`;
  });
  pintar = data.length > 0 ? `${pintar}<tr class='tr_horas_programas'><td colspan='4' class='ttitulo'>Total</td><td colspan='2'>${n_horas}</td>` : pintar;
  $(`${container} .horas_programas`).after(pintar);
}
const pintar_objetivos_profesor = (data, container) => {
  $(".tr_objetivos").remove();
  let pintar = data.length > 0 ? `` : `<tr  class='tr_objetivos'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ objetivo }) => { pintar = `${pintar}<tr class='tr_objetivos'><td colspan='6'>${objetivo}</td></tr>` });
  $(`${container} .objetivos`).after(pintar);
}
const pintar_perfiles_profesor = (data, container) => {
  $(".tr_perfiles").remove();
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_perfiles'><td colspan='2'>Perfil</td><td colspan='2'>Rol</td><td colspan='2'>Cobertura</td>` : `<tr class='tr_perfiles'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ perfil, rol, cobertura }) => { pintar = `${pintar}<tr class='tr_perfiles'><td colspan='2'>${perfil}</td> <td colspan='2'>${rol}</td> <td colspan='2'>${cobertura}</td></tr>` });
  $(`${container} .perfiles`).after(pintar);
}
const pintar_lineas_profesor = (data, container) => {
  $(".tr_lineas").remove();
  let pintar = data.length > 0 ? `<tr class='filaprincipal tr_lineas'><td colspan='3'>Linea</td><td colspan='3'>Sub Linea</td>` : `<tr class='tr_lineas'><td colspan='6'>Ningún dato disponible en esta sección</td></tr>`;
  data.map(({ linea, sub_linea }) => { pintar = `${pintar}<tr class='tr_lineas'><td colspan='3'>${linea}</td> <td colspan='3'>${sub_linea}</td></tr>` });
  $(`${container} .lineas`).after(pintar);
}
const pintar_politicas_profesor = (data, container) => {
  let pintar = data.length > 0 ? `` : `<li>Ningún dato disponible en esta sección</li>`;
  data.map(({ nombre }) => { pintar = `${pintar}<li>${nombre}</li>` });
  $(`${container} .politicas`).html(pintar);
}
const pintar_notas_profesor = (data, container) => {
  let pintar = data.length > 0 ? `` : `<li>Ningún dato disponible en esta sección</li>`;
  data.map(({ nombre }) => { pintar = `${pintar}<li>${nombre}</li>` });
  $(`${container} .notas`).html(pintar);
}

const obtener_parametros = (ids) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_parametros`;
    consulta_ajax(url, { ids }, resp => {
      resolve(resp);
    });
  });
}
const obtener_valores_parametros = (idparametro) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_valores_parametros`;
    consulta_ajax(url, { idparametro }, resp => {
      resolve(resp);
    });
  });
}
const obtener_valores_permisos = (id_valor, idparametro, tipo = 'left') => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_valores_permisos`;
    consulta_ajax(url, { idparametro, id_valor, tipo }, resp => {
      resolve(resp);
    });
  });
}

const pintar_datos_parametros_combo = (datos, combo, mensaje, defecto = '',) => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(elemento => { $(combo).append(`<option value='${elemento.id}'> ${elemento.nombre}</option>`); });
  $(combo).val(defecto);
}

const listar_parametros = async () => {
  let datos = await obtener_parametros([80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102]);
  pintar_datos_parametros_combo(datos, '.cbx_parametros', 'Seleccione Parametro');
}

const listar_valores_parametros = async (idparametro, tabla = "#tabla_valores_parametros",) => {
  id_valor_parametro = null;
  listar_valores_parametros_relaciones(-1, -1);
  $('#cbx_listado_parametros_r').val('');
  let data = await obtener_valores_parametros(idparametro);
  $(`${tabla} tbody`).off('click', 'tr td .editar').off('click', 'tr td .eliminar').off('click', 'tr td .persona').off('click', 'tr');
  const myTable = $(`${tabla}`).DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "nombre" },
      {
        "render": function (data, type, full, meta) {
          let { idparametro } = full;
          return `${idparametro == 91 || idparametro == 86 ? '<span  style="color: #39B23B" title="Persona" data-toggle="popover" data-trigger="hover" class="fa fa-user btn btn-default persona red" ></span> ' : ''}<span  style="color: #d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default eliminar red" ></span> <span  style="color: #5bc0de" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default editar red" ></span>`;
        }
      },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: [],
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass("warning");
    $(this).attr("class", "warning");
    let { id } = myTable.row(this).data();
    id_valor_parametro = id;
    let idparametro = $('#cbx_listado_parametros_r').val();
    if (idparametro) listar_valores_parametros_relaciones(id_valor_parametro, idparametro)
  });

  $(`${tabla} tbody`).on('click', 'tr td .eliminar', function () {
    let { id } = myTable.row($(this).parent().parent()).data();
    confirmar_eliminar_parametro(id, 0);
  });

  $(`${tabla} tbody`).on('click', 'tr td .editar', function () {
    let { valor, valorx } = myTable.row($(this).parent().parent()).data();
    $(`#form_modificar_valor input[name='nombre']`).val(valor);
    $(`#form_modificar_valor textarea[name='descripcion']`).val(valorx);
    $("#modal_modificar_valor").modal();
  });

  $(`${tabla} tbody`).on('click', 'tr td .persona', function () {
    let { id } = myTable.row($(this).parent().parent()).data();
    listar_parametros_personas(id);
    $("#modal_persona_parametro").modal();
  });

}

const listar_valores_parametros_relaciones = async (id_valor, idparametro, tabla = '#tabla_valores_parametros_r',) => {
  let data = await obtener_valores_permisos(id_valor, idparametro);
  $(`${tabla} tbody`).off('click', 'tr td .desabilitar').off('click', 'tr td .habilitar').off('click', 'tr');
  const myTable = $(`${tabla}`).DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "nombre" },
      {
        "render": function (data, type, full, meta) {
          let { id_permiso } = full;
          let resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-success active habilitar">Habilitar</label></div>';
          if (id_permiso != null) resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-danger2 active desabilitar">Desabiliar</label></div>';
          return resp;
        }
      }
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: [],
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass("warning");
    $(this).attr("class", "warning");
  });

  $(`${tabla} tbody`).on('click', 'tr td .habilitar', function () {
    let { id } = myTable.row($(this).parent().parent()).data();
    confirmar_accion(() => {
      habilitar_relacion(id_valor_parametro, id);
    })
  });

  $(`${tabla} tbody`).on('click', 'tr td .desabilitar', function () {
    let { id_permiso } = myTable.row($(this).parent().parent()).data();
    confirmar_accion(() => {
      deshabilitar_relacion(id_permiso)
    })
  });

  //agregar y quitar permisos
  const habilitar_relacion = (id_principal, id_secundario) => {
    consulta_ajax(`${ruta}habilitar_relacion`, { id_principal, id_secundario }, resp => {
      let { mensaje, tipo, titulo } = resp;
      if (tipo == 'success') {
        swal.close();
        listar_valores_parametros_relaciones(id_valor_parametro, $('#cbx_listado_parametros_r').val());
      } else MensajeConClase(mensaje, tipo, titulo);
    });
  }

  const deshabilitar_relacion = (id) => {
    consulta_ajax(`${ruta}deshabilitar_relacion`, { id }, resp => {
      let { mensaje, tipo, titulo } = resp;
      if (tipo == 'success') {
        swal.close();
        listar_valores_parametros_relaciones(id_valor_parametro, $('#cbx_listado_parametros_r').val());
      } else MensajeConClase(mensaje, tipo, titulo);
    });
  }
}
const guardar_valor_parametro = (idparametro) => {
  let formData = new FormData(document.getElementById("form_valor_parametro"));
  formData.append("idparametro", idparametro);
  enviar_formulario(`${Traer_Server()}index.php/genericas_control/guardar_valor_Parametro`, formData, (resp) => {
    if (resp == 1) {
      MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
    } else if (resp == 2) {
      $("#form_valor_parametro").get(0).reset();
      MensajeConClase("", "success", "Valor Guardado!");
      listar_valores_parametros(idparametro);
    } else if (resp == 3) {
      MensajeConClase("El Nombre del Parametro ya esta en el sistema", "info", "Oops...");
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al Guardar el Parametro", "error", "Oops...");
    }
  });
}

const modificar_valor_parametro = (parametro) => {
  let formData = new FormData(document.getElementById("form_modificar_valor"));
  formData.append("idparametro", parametro);
  enviar_formulario(`${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`, formData, (resp) => {
    if (resp == 1) {
      MensajeConClase("Valor Parametro Modificado con exito", "success", "Proceso Exitoso!");
      $("#form_modificar_valor").get(0).reset();
      $("#modal_modificar_valor").modal("hide");
      let idparametro = $('#cbx_listado_parametros').val();
      listar_valores_parametros(idparametro);
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else if (resp == 2) {
      MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
    } else if (resp == 3) {
      MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
    } else {
      MensajeConClase("Error al Modificar el Parametro", "error", "Oops...");
    }
  });
}

const confirmar_eliminar_parametro = (id, estado) => {

  swal({
    title: "Estas Seguro .. ?",
    text: "Tener en cuenta que al Eliminar este valor no estará disponible en el proceso de CSEP, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
        eliminar_valor_parametro(id, estado);
      }
    });
}

function eliminar_valor_parametro(idparametro, estado) {
  let url = `${Traer_Server()}index.php/genericas_control/cambio_estado_parametro`;
  let data = {
    idparametro,
    estado
  };
  consulta_ajax(url, data, (resp) => {
    if (resp == "sin_session") {
      close();
    } else if (resp == 1) {
      MensajeConClase("", "success", "Valor Eliminado!");
      let idparametro = $('#cbx_listado_parametros').val();
      listar_valores_parametros(idparametro);
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al eliminar la información, contacte con el administrador.", "error", "Oops...");
    }
  })
}

const mostrar_plan_sesion = () => {
  consulta_ajax(`${Traer_Server()}index.php/profesores_csep_control/mostrar_plan_sesion`, {}, resp => {
    if (resp.length > 0) {
      $("#container-principal2 .row").append(`
      <a style="color: black; font-style: oblique;font-weight: bold;" href='${Traer_Server()}index.php/plan_trabajo/${resp[0].id_persona}/${resp[0].periodo}' class="sinlink" target="_blank">        
        <div>
            <div class="thumbnail">
                <div class="caption">   
                <img src="${Traer_Server()}/imagenes/Viaticos_Transporte.png" alt="...">
                <span class = "btn form-control">Plan de Trabajo</span>
                </div>
            </div>
        </div>
      </a>
      `);
    }
  });
}

const ver_plan_profesor = async (data, container = '#contaniner_plan') => {
  let { id_persona, nombre_completo, identificacion, dedicacion, escalafon, contrato, fecha_inicio, fecha_fin, departamento, programa, area_conocimiento, grupo, categoria_col, id, ev_360, ev_docente, indice_h, citas } = data;
  id_profesor_personal = id;
  data_firma = { plan: id, tipo: 'profesor' };
  id_personal = id_persona;
  $("#informacion_general").html(`  Profesor ${dedicacion} - ${escalafon}, adscrito al programa ${programa} del departamento ${departamento}. Con conocimientos en el área de ${area_conocimiento} y perteneciente al grupo de investigación ${grupo}.`)
  $(`#nombre_profesor`).html(nombre_completo);
  $(`#informacion_contrato`).html(`Contrato ${contrato}, Inicio : ${fecha_inicio}${fecha_fin ? `, Fin :${fecha_fin}` : ''}.`);
  $(`#categoria_col`).html(categoria_col);
  $(`#ev_360`).html(ev_360);
  $(`#ev_docente`).html(ev_docente);
  $(`#indice_h`).html(indice_h);
  $(`#citas`).html(citas);
  $(`.imagen_empleado_plan`).attr("src", `${Traer_Server()}/Fotos/${identificacion}.jpg`);
  $(`#remitido_ced`).html(ev_docente < 4 ? 'SI' : 'NO');

  let formacion = await obtener_formacion_profesor(id);
  pintar_formacion_plan_personal(formacion);

  let indicadores = await obtener_indicadores_profesor(id);
  pintar_indicadores_profesor_personal(indicadores);

  let asignaturas = await obtener_asignaturas_profesor(id);
  let asig_agru = await obtener_asignaturas_agrupadas(id)
  pintar_datos_combo_general(asig_agru, '.cbx_asignatura_horario', 'Seleccione Asignatura')
	$('.cbx_tipo_horario').val('Ate_Est');

  asignaturas.map(({ inicio, nombre, fin, salon, dia }) => {
    $("#asignaturas").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/libros.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>${nombre}</h6>
					<ul class="list-group list-group-flush">
            <li class="list-group-item px-0" style='color: black'><i class="far fa-calendar"></i> ${dia}</li>
            <li class="list-group-item px-0" style='color: black'><i class="far fa-clock"></i> ${(inicio && fin) ? inicio + '-' + fin : 'N/A'}</li>
            <li class="list-group-item px-0" style='color: black'><i class="fas fa-map-pin"></i> ${salon}</li>
          </ul>
				</div>
			</div>
		</div>
	`);
  })

  let atencion = await obtener_atencion_profesor(id);
  pintar_atencion_personal(atencion)

  let can_horas = 0;
  let horas = await obtener_horas_programa_profesor(id);
  horas.map(({ programa, hora, cantidad }) => {
    can_horas = can_horas + parseInt(cantidad);
    $("#horas_programa tbody").append(`<tr><td>${programa}</td><td>${hora}</td><td>${cantidad}</td></tr>`)
  })
  $("#horas_programa tbody").append(`<tr><td colspan='2'>Total</td><td>${can_horas}</td></tr>`)

  let objetivos = await obtener_objetivos_profesor(id);
  objetivos.map(({ objetivo }) => {
    $("#observaciones").append(`<li class="list-group-item" style= "color:white; background-color: #6e1f7c">${objetivo}</li>`)
  })
  
  let perfiles = await obtener_perfiles_profesor(id);
  perfiles.map(({ perfil, rol, cobertura }) => {
    $("#perfiles tbody").append(`<tr><td>${perfil}</td><td>${rol}</td><td>${cobertura}</td></tr>`)
  });

  let lineas = await obtener_lineas_profesor(id);
  lineas.map(({ linea, sub_linea }) => {
    $("#lineas").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/laboratorios.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>${linea}</h6>
          <p class="card-text" style='color : black'> ${sub_linea} </p>
				</div>
			</div>
		</div>
	`);
  })

  $("#modal_detalle_profesor").modal();
}

const pintar_formacion_plan_personal = (data_formacion) => {
	$("#formacion").html("");
	$("#formacion").append(`
	  <div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/producto_formacion.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>NUEVO</h6>
					<p class="card-text" style='color : black'> Aquí puedes agregar tu formación.</p>
					<button class="btn btn-primary btn-block" id='btn_nueva_formacion'>
						Agregar
					</button>
				</div>
			</div>
		</div>`)

		$(`#btn_nueva_formacion`).off('click');
    $("#btn_nueva_formacion").click(() => {
      id_formacion_prof = '';
      $("#modal_guardar_formacion .modal-title").html(`<span class="fa fa-book"></span> Nueva Formación`);
      $("#modal_guardar_formacion").modal();
    });

	data_formacion.map(({ formacion, nombre, id }) => {
		$("#formacion").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/test.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>${formacion}</h6>
					<p class="card-text" style='color : black'> ${nombre} </p>
					
					<div class="dropdown" style='width : 100%'>
					<button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Acciones
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<span class="dropdown-item btn_agregar_sop_formacion${id}" ><span class='fa fa-upload'></span> Agregar Soportes</span>
						<span class="dropdown-item btn_ver_sop_formacion${id}" ><span class='fa fa-eye'></span> Ver soportes</span>
					</div>
					</div>
				
				</div>
			</div>
		</div>
	`);

		$(`.btn_agregar_sop_formacion${id}`).click(function () {
			$("#id_formacion_archivo").val(id);
			callback_activo = (resp) => guardar_un_soporte_formacion(id);
			$("#modal_enviar_archivos").modal();
		});

    $(`.btn_ver_sop_formacion${id}`).click(function () {
      listar_soportes_formacion_academica(id, 'formacion');
      $("#modal_listar_soportes").modal();
    });
	})
}

const pintar_atencion_personal = data_atencion => {
	$("#atencion").html("");
	$("#atencion").append(`
	  <div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/cronogramas.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>NUEVO</h6>
					<p class="card-text" style='color : black'> Aquí puedes agregar horarios de atención.</p>
					<button class="btn btn-primary btn-block" id='btn_nueva_atencion_plan'>
						Agregar
					</button>
				</div>
			</div>
		</div>`)

		$(`#btn_nueva_atencion_plan`).off('click');
    $("#btn_nueva_atencion_plan").click(() => {
      $("#modal_guardar_atencion").modal();
    });

  data_atencion.map(({ hora_inicio, hora_fin, lugar, nombre, tipo_atencion, asignatura }) => {
    $("#atencion").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/entregable.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>${tipo_atencion}</h6>
					<p class="card-text m-0" style='color : black'> ${asignatura ? asignatura : ''} </p>
					<p class="card-text m-0" style='color : black'> ${nombre} </p>
					<p class="card-text m-0" style='color : black'> ${lugar} </p>
					<p class="card-text m-0" style='color : black'> ${hora_inicio} </p>
					<p class="card-text m-0" style='color : black'> ${hora_fin} </p>
				</div>
			</div>
		</div>
	`);
  })
}

const obtener_valores_parametros_bloque = () => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_valores_parametros_bloque`;
    consulta_ajax(url, {}, resp => {
      resolve(resp);
    });
  });
}

const listar_valores_parametros_bloque = async () => {
  let datos = await obtener_valores_parametros_bloque();
  let perfiles = [];
  let roles = [];
  let indicadores = [];
  let horas = [];
  let formacion = [];
  let categorias = [];
  let programas = [];
  let lineas = [];
  let sub_lineas = [];
  let grupos = [];
  let areas = [];
  let departamentos = [];
  let coberturas = [];
  let dedicacion = [];
  let escalafon = [];
  let periodos = [];
  let contratos = [];
  let colciencias = [];
  let estados = [];
  let asignaturas = [];
  let dias = [];
  datos.forEach((element) => {
    let { idparametro } = element;
    if (idparametro == 80) perfiles.push(element);
    else if (idparametro == 81) roles.push(element);
    else if (idparametro == 82) indicadores.push(element);
    else if (idparametro == 83) horas.push(element);
    else if (idparametro == 84) formacion.push(element);
    else if (idparametro == 85) categorias.push(element);
    else if (idparametro == 86) programas.push(element);
    else if (idparametro == 87) lineas.push(element);
    else if (idparametro == 88) sub_lineas.push(element);
    else if (idparametro == 89) grupos.push(element);
    else if (idparametro == 90) areas.push(element);
    else if (idparametro == 91) departamentos.push(element);
    else if (idparametro == 92) coberturas.push(element);
    else if (idparametro == 93) dedicacion.push(element);
    else if (idparametro == 94) escalafon.push(element);
    else if (idparametro == 95) periodos.push(element);
    else if (idparametro == 96) contratos.push(element);
    else if (idparametro == 97) colciencias.push(element);
    else if (idparametro == 98) estados.push(element);
    else if (idparametro == 99) asignaturas.push(element);
    else if (idparametro == 100) dias.push(element);
  });

  pintar_datos_parametros_combo(estados, '.cbx_estados', 'Seleccione Estado');
  pintar_datos_parametros_combo(departamentos, '.cbx_departamentos', 'Seleccione Departamento');
  pintar_datos_parametros_combo(dedicacion, '.cbx_dedicaciones', 'Seleccione Dedicación');
  pintar_datos_parametros_combo(escalafon, '.cbx_escalafones', 'Seleccione Escalafon');
  pintar_datos_parametros_combo(contratos, '.cbx_contratos', 'Seleccione Contrato');
  pintar_datos_parametros_combo(areas, '.cbx_areas', 'Seleccione Area Conocimiento');
  pintar_datos_parametros_combo(colciencias, '.cbx_colciencias', 'Seleccione Cat. Colciencia');
  pintar_datos_parametros_combo(grupos, '.cbx_grupos', 'Seleccione Grupo Investigación');
  pintar_datos_parametros_combo(indicadores, '.cbx_indicadores', 'Seleccione Indicador');
  pintar_datos_parametros_combo(asignaturas, '.cbx_asignaturas', 'Seleccione Asignatura');
  pintar_datos_parametros_combo(dias, '.cbx_dias', 'Seleccione Día');
  pintar_datos_parametros_combo(formacion, '.cbx_formacion', 'Seleccione Formación');
  pintar_datos_parametros_combo(perfiles, '.cbx_perfil', 'Seleccione Perfil');
  pintar_datos_parametros_combo(roles, '.cbx_rol', 'Seleccione Rol');
  //pintar_datos_parametros_combo(programas, '.cbx_cobertura', 'Seleccione Cobertura');
  pintar_datos_parametros_combo(programas, '.cbx_programas', 'Seleccione Programa');
  pintar_datos_parametros_combo(horas, '.cbx_horas', 'Seleccione Hora');
  pintar_datos_parametros_combo(lineas, '.cbx_linea', 'Seleccione Linea');

}

const guardar_plan_profesor = (data) => {
  enviar_formulario(`${ruta}guardar_plan_profesor`, data, resp => {
    let { tipo, mensaje, titulo, id } = resp;
    if (tipo == 'success') {
      if (id) profesor_sele.id = id;
      buscar_profesor(profesor_sele.identificacion);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const guardar_indicador = (data) => {
  enviar_formulario(`${ruta}guardar_indicador`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_indicadores(id_profesor, 'todos');
      if (id) $("#modal_guardar_indicador").modal('hide');
      $("#form_guardar_indicador").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_asignatura = (data) => {
  enviar_formulario(`${ruta}guardar_asignatura`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_asignaturas(id_profesor);
      if (id) $("#modal_guardar_asignatura").modal('hide');
      $("#form_guardar_asignatura").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_formacion = (data, personal = false) => {
  enviar_formulario(`${ruta}guardar_formacion`, data, async (resp) => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      if (!personal) {
        listar_formacion(id_profesor);
        if (id) $("#modal_guardar_formacion").modal('hide');
        $("#form_guardar_formacion").get(0).reset();
      } else {
        let formacion = await obtener_formacion_profesor(id_profesor_personal);
        pintar_formacion_plan_personal(formacion);
        $("#form_guardar_formacion_personal").get(0).reset();
      }
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_objetivo = (data) => {
  enviar_formulario(`${ruta}guardar_objetivo`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_objetivos(id_profesor);
      if (id) $("#modal_guardar_objetivo").modal('hide');
      $("#form_guardar_objetivo").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_atencion = (data) => {
  enviar_formulario(`${ruta}guardar_atencion`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_atencion(id_profesor);
      if (id) $("#modal_guardar_atencion").modal('hide');
      $("#form_guardar_atencion").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const guardar_atencion_plan_personal = (data) => {
  enviar_formulario(`${ruta}guardar_atencion`, data, async resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      let atencion = await obtener_atencion_profesor(id_profesor);
      pintar_atencion_personal(atencion)
      $("#modal_guardar_atencion").modal('hide');
      $("#form_guardar_horario_atencion").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const guardar_perfil = (data) => {
  enviar_formulario(`${ruta}guardar_perfil`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_perfiles(id_profesor);
      if (id) $("#modal_guardar_perfil").modal('hide');
      $("#form_guardar_perfil").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_hora = (data) => {
  enviar_formulario(`${ruta}guardar_hora`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_horas(id_profesor);
      if (id) $("#modal_guardar_hora").modal('hide');
      $("#form_guardar_hora").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const guardar_linea = (data) => {
  enviar_formulario(`${ruta}guardar_linea`, data, resp => {
    let { tipo, mensaje, titulo, id_profesor, id } = resp;
    if (tipo == 'success') {
      listar_lineas(id_profesor);
      if (id) $("#modal_guardar_linea").modal('hide');
      $("#form_guardar_linea").get(0).reset();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const mostrar_data_guardar_profesor = async ({ id_programa, id_departamento, id_area, id_dedicacion, id_escalafon, id_contrato, fecha_inicio, fecha_fin, id_grupo, cvlac, google, scopus, id_estado }) => {
  let form = '#form_guardar_plan_profesor';
  $(form).get(0).reset();
  $(`${form} select[name="id_departamento"]`).val(id_departamento);
  $(`${form} select[name="id_area"]`).val(id_area);
  $(`${form} select[name="id_dedicacion"]`).val(id_dedicacion);
  $(`${form} select[name="id_escalafon"]`).val(id_escalafon);
  $(`${form} select[name="id_contrato"]`).val(id_contrato);
  $(`${form} input[name="fecha_inicio"]`).val(fecha_inicio);
  $(`${form} input[name="fecha_fin"]`).val(fecha_fin);
  $(`${form} select[name="id_grupo"]`).val(id_grupo);
  $(`${form} input[name="cvlac"]`).val(cvlac);
  $(`${form} input[name="google"]`).val(google);
  $(`${form} input[name="scopus"]`).val(scopus);
  $(`${form} select[name="id_estado"]`).val(id_estado);
  if (id_departamento) {
    let programas = await obtener_valores_permisos(id_departamento, 86, 'inner');
    pintar_datos_parametros_combo(programas, '.cbx_programas_add', 'Seleccione Programa', id_programa);
  }
  $("#modal_guardar_plan_profesor").modal();

}

const listar_indicadores = async id => {
  let data = await obtener_indicadores_profesor(id, 'todos');
  $(`#tabla_indicadores tbody`).off('click', 'tr td .eliminar').off('click', 'tr td .modificar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_indicadores").DataTable({
    destroy: true,
    processing: true,
    "order": [
      [3, "asc"]
    ],
    data,
    columns: [
      { "data": "tipo" },
      { "data": "nombre" },
      { "data": "fecha_inicial" },
      { "data": "estado_final" },
      { "data": "fecha_final" },
      { "data": "estado_final" },
      { "data": "estado_actual" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_indicadores tbody').on('click', 'tr', function () {
    $("#tabla_indicadores tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });
  $('#tabla_indicadores tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Indicador ?", tipo: 1 }, () => {
      listar_indicadores(id_profesor, 'todos');
    });
  });
  $('#tabla_indicadores tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = ({ id, id_indicador, estado_inicial, fecha_inicial, estado_final, fecha_final, estado_actual, tipo }) => {
    let form = '#form_guardar_indicador';
    id_indicador_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_indicador"]`).val(id_indicador);
    $(`${form} input[name="estado_inicial"]`).val(estado_inicial);
    $(`${form} input[name="fecha_inicial"]`).val(fecha_inicial);
    $(`${form} input[name="estado_final"]`).val(estado_final);
    $(`${form} input[name="fecha_final"]`).val(fecha_final);
    $(`${form} input[name="estado_actual"]`).val(estado_actual);
    let aplica = tipo == 'Aplica' ? '#rate1' : '#rate2';
    $(`${form} ${aplica}`).prop("checked", true);
    $("#modal_guardar_indicador .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Indicador`);
    $("#modal_guardar_indicador").modal();
  }

  $("#modal_adm_indicadores").modal();
}
const listar_asignaturas = async id => {
  let data = await obtener_asignaturas_profesor(id);
  $(`#tabla_asignaturas tbody`).off('click', 'tr td .eliminar').off('click', 'tr td .modificar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_asignaturas").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "nombre" },
      { "data": "creditos" },
      { "data": "grupo" },
      { "data": "dia" },
      { "data": "horario" },
      { "data": "cupo" },
      { "data": "salon" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_asignaturas tbody').on('click', 'tr', function () {
    $("#tabla_asignaturas tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });
  $('#tabla_asignaturas tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Asignatura ?", tipo: 2 }, () => {
      listar_asignaturas(id_profesor);
    });
  });
  $('#tabla_asignaturas tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = ({ id, id_asignatura, id_dia, creditos, cupo, grupo, horario, salon }) => {
    let form = '#form_guardar_asignatura';
    id_asignatura_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_asignatura"]`).val(id_asignatura);
    $(`${form} select[name="id_dia"]`).val(id_dia);
    $(`${form} input[name="creditos"]`).val(creditos);
    $(`${form} input[name="cupo"]`).val(cupo);
    $(`${form} input[name="grupo"]`).val(grupo);
    $(`${form} input[name="horario"]`).val(horario);
    $(`${form} input[name="salon"]`).val(salon);
    $("#modal_guardar_asignatura .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Asignatura`);
    $("#modal_guardar_asignatura").modal();
  }
  $("#modal_adm_asignaturas").modal();
}

const listar_formacion = async id => {
  let data = await obtener_formacion_profesor(id);
  $(`#tabla_formacion tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .soportes');
  const myTable = $("#tabla_formacion").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "formacion" },
      { "data": "nombre" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span> <span  style="color:#797979" title="Soportes" data-toggle="popover" data-trigger="hover" class="fa fa-file btn btn-default soportes" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_formacion tbody').on('click', 'tr', function () {
    $("#tabla_formacion tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_formacion tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Formación ?", tipo: 3 }, () => {
      listar_formacion(id_profesor);
    });
  });
  $('#tabla_formacion tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  $(`#tabla_formacion tbody`).on('click', 'tr td .soportes', function () {
    let { id } = myTable.row($(this).parent().parent()).data()
    $("#id_formacion_archivo").val(id)
    listar_soportes(id, 'formacion');
    $("#modal_listar_soportes").modal();
  });

  const mostrar_data = ({ id, id_formacion, nombre }) => {
    let form = '#form_guardar_formacion';
    id_formacion_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_formacion"]`).val(id_formacion);
    $(`${form} input[name="nombre"]`).val(nombre);
    $("#modal_guardar_formacion .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Formación`);
    $("#modal_guardar_formacion").modal();
  }
  $("#modal_adm_formacion").modal();
}
const listar_objetivos = async id => {
  let data = await obtener_objetivos_profesor(id);
  $(`#tabla_objetivos tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_objetivos").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "objetivo" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_objetivos tbody').on('click', 'tr', function () {
    $("#tabla_objetivos tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_objetivos tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Objetivo ?", tipo: 4 }, () => {
      listar_objetivos(id_profesor);
    });
  });

  $('#tabla_objetivos tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = ({ id, objetivo }) => {
    let form = '#form_guardar_objetivo';
    id_objetivo_prof = id;
    $(form).get(0).reset();
    $(`${form} textarea[name="objetivo"]`).val(objetivo);
    $("#modal_guardar_objetivo .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Objetivo`);
    $("#modal_guardar_objetivo").modal();
  }

  $("#modal_adm_objetivos").modal();
}
const listar_atencion = async id => {
  let data = await obtener_atencion_profesor(id);
  $(`#tabla_atencion tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_atencion").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "tipo_atencion" },
      { "data": "asignatura" },
      { "data": "nombre" },
      { "data": "hora_inicio" },
      { "data": "hora_fin" },
      { "data": "lugar" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_atencion tbody').on('click', 'tr', function () {
    $("#tabla_atencion tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_atencion tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Horario ?", tipo: 5 }, () => {
      listar_atencion(id_profesor);
    });
  });

  $('#tabla_atencion tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });
  const mostrar_data = ({ id, id_dia, hora_inicio, hora_fin, lugar, id_tipo }) => {
    let form = '#form_guardar_atencion';
    id_atencion_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_tipo"]`).val(id_tipo);
    $(`${form} select[name="id_dia"]`).val(id_dia);
    $(`${form} input[name="hora_inicio"]`).val(hora_inicio);
    $(`${form} input[name="hora_fin"]`).val(hora_fin);
    $(`${form} input[name="lugar"]`).val(lugar);
    $("#modal_guardar_atencion .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Horario Atención`);
    $("#modal_guardar_atencion").modal();
  }
  $("#modal_adm_atencion").modal();
}
const listar_horas = async id => {
  let data = await obtener_horas_programa_profesor(id);
  $(`#tabla_horas tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_horas").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "programa" },
      { "data": "hora" },
      { "data": "cantidad" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_horas tbody').on('click', 'tr', function () {
    $("#tabla_horas tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_horas tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Hora ?", tipo: 7 }, () => {
      listar_horas(id_profesor);
    });
  });

  $('#tabla_horas tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = ({ id, id_programa, id_hora, cantidad }) => {
    let form = '#form_guardar_hora';
    id_hora_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_programa"]`).val(id_programa);
    $(`${form} select[name="id_hora"]`).val(id_hora);
    $(`${form} input[name="cantidad"]`).val(cantidad);

    $("#modal_guardar_hora .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Hora`);
    $("#modal_guardar_hora").modal();
  }
  $("#modal_adm_horas").modal();
}
const listar_perfiles = async id => {
  let data = await obtener_perfiles_profesor(id);
  $(`#tabla_perfil tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_perfil").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "perfil" },
      { "data": "rol" },
      { "data": "cobertura" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_perfil tbody').on('click', 'tr', function () {
    $("#tabla_perfil tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_perfil tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Perfil ?", tipo: 6 }, () => {
      listar_perfiles(id_profesor);
    });
  });

  $('#tabla_perfil tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = ({ id_perfil, id_rol, id_cobertura, id }) => {
    let form = '#form_guardar_perfil';
    id_perfil_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_perfil"]`).val(id_perfil);
    $(`${form} select[name="id_rol"]`).val(id_rol);
    $(`${form} input[name="id_cobertura"]`).val(id_cobertura);
    $("#modal_guardar_perfil .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Perfil`);
    $("#modal_guardar_perfil").modal();
  }

  $("#modal_adm_perfiles").modal();
}

const listar_lineas = async id => {
  let data = await obtener_lineas_profesor(id);
  $(`#tabla_lineas tbody`).off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('dblclick', 'tr').off('click', 'tr');
  const myTable = $("#tabla_lineas").DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "linea" },
      { "data": "sub_linea" },
      { 'defaultContent': '<span  style="color:#2E79E5" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" ></span> <span style="color:#d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o red btn btn-default eliminar" ></span>' },
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: get_botones(),
  });

  $('#tabla_lineas tbody').on('click', 'tr', function () {
    $("#tabla_lineas tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

  $('#tabla_lineas tbody').on('click', 'tr td .eliminar', function () {
    let { id, id_profesor } = myTable.row($(this).parent().parent()).data();
    eliminar_datos({ id, title: "Eliminar Linea ?", tipo: 8 }, () => {
      listar_lineas(id_profesor);
    });
  });

  $('#tabla_lineas tbody').on('click', 'tr td .modificar', function () {
    let data = myTable.row($(this).parent().parent()).data();
    mostrar_data(data);
  });

  const mostrar_data = async ({ id_linea, id_sub_linea, id }) => {
    let form = '#form_guardar_linea';
    id_linea_prof = id;
    $(form).get(0).reset();
    $(`${form} select[name="id_linea"]`).val(id_linea);
    if (id_linea) {
      let sub_lineas = await obtener_valores_permisos(id_linea, 88, 'inner');
      pintar_datos_parametros_combo(sub_lineas, '.cbx_sub_linea', 'Seleccione Sub-Linea', id_sub_linea);
    }
    $("#modal_guardar_linea .modal-title").html(`<span class="fa fa-wrench"></span> Modificar Linea`);
    $("#modal_guardar_linea").modal();
  }

  $("#modal_adm_lineas").modal();
}

const eliminar_datos = (data, callback) => {
  let { title, id, tipo } = data;
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
    function (isConfirm) {
      if (isConfirm) {
        consulta_ajax(`${ruta}eliminar_datos`, { id, tipo }, resp => {
          let { tipo, mensaje, titulo } = resp;
          if (tipo == 'success') {
            callback();
            swal.close();
          } else MensajeConClase(mensaje, tipo, titulo);
        });
      }
    });
}

const mensaje_info = () => MensajeConClase("Debe cargar la información inicial para continuar.", "info", "Oops.!");

const obtener_parametros_personas = (id, id_persona = '', limit = '', tipo = '') => {
  return new Promise(resolve => {
    let url = `${ruta}parametros_persona`;
    consulta_ajax(url, { id, id_persona, limit, tipo }, (resp) => {
      resolve(resp);
    });
  });
}

const listar_parametros_personas = async (id, tabla = "#tabla_personas_parametro",) => {
  let data = await obtener_parametros_personas(id);
  $(`${tabla} tbody`).off('click', 'tr td .editar').off('click', 'tr td .eliminar').off('click', 'tr td .persona').off('click', 'tr');
  const myTable = $(`${tabla}`).DataTable({
    destroy: true,
    processing: true,
    data,
    columns: [
      { "data": "nombre_completo" },
      { "data": "identificacion" },
      { "data": "tipo" },
      { "defaultContent": `<span style = "color: #d9534f" title = "Eliminar" data-toggle = "popover" data-trigger="hover" class = "fa fa-remove btn btn-default eliminar red"></span >` }
    ],
    language: get_idioma(),
    dom: 'Bfrtip',
    buttons: [],
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass("warning");
    $(this).attr("class", "warning");
  });

  $(`${tabla} tbody`).on('click', 'tr td .eliminar', function () {
    let { id, id_parametro } = myTable.row($(this).parent().parent()).data();
    confirmar_accion(() => {
      eliminar_persona_parametro(id, id_parametro);
    });
  });
}

const buscar_persona = (dato, callbak) => {
  consulta_ajax(`${ruta}buscar_persona`, { dato }, (resp) => {
    $(`#tabla_personas_busqueda tbody`).off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
    const myTable = $("#tabla_personas_busqueda").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        //{ "defaultContent": `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>` },
        { data: "nombre_completo" },
        { data: 'identificacion' },
        { 'defaultContent': '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>' },
      ],
      language: get_idioma(),
      dom: 'Bfrtip',
      buttons: [],
    });

    $('#tabla_personas_busqueda tbody').on('click', 'tr', function () {
      $("#tabla_personas_busqueda tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $('#tabla_personas_busqueda tbody').on('dblclick', 'tr', function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });
    $('#tabla_personas_busqueda tbody').on('click', 'tr td .seleccionar', function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });

  });

}

const asignar_persona_parametro = (id_parametro, id_persona, tipo = 'normal') => {
  consulta_ajax(`${ruta}asignar_persona_parametro`, { id_parametro, id_persona, tipo }, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      listar_parametros_personas(id_parametro);
      $("#txt_dato_buscar").val('');
      buscar_persona("***", callback_activo);
      $("#modal_asignar_permiso").modal('hide');
      $("#form_guardar_permiso").get(0).reset();
      //swal.close();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const eliminar_persona_parametro = (id, id_parametro) => {
  consulta_ajax(`${ruta}eliminar_persona_parametro`, { id }, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      listar_parametros_personas(id_parametro);
      swal.close();
    } else MensajeConClase(mensaje, tipo, titulo);
  });
}

const confirmar_accion = (callback) => {
  swal({
    title: "Estas Seguro .. ?",
    text: "Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
        callback();
      }
    });
}

const calcular_horas_docente = (id_profesor, id_tipo) => {
  return new Promise(resolve => {
    let url = `${ruta}calcular_horas_docente_post`;
    consulta_ajax(url, { id_profesor, id_tipo }, (resp) => {
      resolve(resp);
    });
  });
}

const listar_soportes = (id_alterno, tipo) => {
  $(`#tabla_soportes tbody`).off('click', 'tr td .eliminar');
  consulta_ajax(`${ruta}listar_soportes`, { id_alterno, tipo }, (resp) => {
    const tabla = $("#tabla_soportes").DataTable({
      "destroy": true,
      "processing": true,
      'data': resp,
      "columns": [
        {
          "render": function (data, type, full, meta) {
            return "<a class='sin-decoration ' href='" + Traer_Server() + ruta_soportes + full.nombre_guardado + "' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>";
          }
        },
        {
          "data": "nombre_real"
        },
        {
          "data": "fecha_registro"
        },
        {
          "data": "nombre_completo"
        },
        {
          "render": function (data, type, full, meta) {
            return '<span  style="color: #d9534f" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default eliminar red" ></span>';
          }
        },
      ],
      "language": idioma,
      dom: 'Bfrtip',
      "buttons": []
    });

    $(`#tabla_soportes tbody`).on("click", "tr", function () {
      $(`#tabla_soportes tbody tr`).removeClass("warning");
      $(this).attr("class", "warning");
    });

    $(`#tabla_soportes tbody`).on("click", `tr td .eliminar`, function () {
      let { id } = tabla.row($(this).parent().parent()).data();
      eliminar_datos({ id, title: "Eliminar Soporte ?", tipo: 9 }, () => {
        listar_soportes(id_alterno, tipo);
      });
    });

  });

}

const listar_soportes_formacion_academica = (id_alterno, tipo) => {
  consulta_ajax(`${ruta}listar_soportes`, { id_alterno, tipo }, (data) => {
    $(`#tabla_soportes_academicos tbody`).html("").off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_adjunto').off('click', 'tr td .eliminar');
    data.map(({ id, nombre_real, nombre_guardado }) => {
      let accion = `<a target='_blank' class='btn btn-secondary ver_adjunto ver_adjunto_sop${id}' title='Descargar'><span class='fa fa-download'></span></a>  <span class='btn btn-danger eliminar eliminar_sop${id}' title='Eliminar'><span class='fa fa-trash'></span></span>`
      $("#tabla_soportes_academicos tbody").append(`<tr><td>${nombre_real}</td><td>${accion}</td></tr>`);

      $(`#tabla_soportes_academicos .eliminar_sop${id}`).on('click', function () {
        eliminar_datos({ id, title: "Eliminar Soporte ?", tipo: 9 }, () => {
          listar_soportes_formacion_academica(id_alterno, tipo);
        });
      });

      $(`#tabla_soportes_academicos .ver_adjunto_sop${id}`).on("click", function () {
        $(".ver_adjunto").attr("href", `${Traer_Server()}${ruta_soportes}${nombre_guardado}`);
      });
    })
  });
}

const pintar_indicadores_profesor_personal = data => {
  $(`#tabla_indicadores_personal tbody`).html("")
  data.map(({ nombre, fecha_inicial, estado_inicial, fecha_final, estado_final, estado_actual }, i) => {
    $("#tabla_indicadores_personal tbody").append(`<tr><td class="align-middle">${i+1}</td><td class="align-middle tabla_filtrar">${nombre}</td><td class="align-middle">${fecha_inicial}</td><td class="align-middle tabla_filtrar">${estado_inicial}</td><td class="align-middle">${fecha_final}</td><td class="align-middle tabla_filtrar">${estado_final}</td><td class="align-middle">${estado_actual}</td></tr>`)
  })
}


const obtener_periodos = (id_persona, tipo) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_periodos`, { id_persona, tipo }, (resp) => {
      resolve(resp);
    });
  });
}

const pintar_periodos = async (id_persona, tipo) => {
  let periodos = await obtener_periodos(id_persona, tipo);
  pintar_datos_parametros_combo(periodos, '.cbx_periodos', 'Seleccione Periodo');
}


const guardar_firma = () => {
  MensajeConClase("validando info", "add_inv", "Oops...");
  let data = new FormData(document.getElementById('cargar_firma_digital'));
  let { plan, tipo } = data_firma
  data.append('id_plan', plan);
  data.append('tipo', tipo);
  enviar_formulario(`${ruta}guardar_firma`, data, (resp) => {
    let { tipo, mensaje, titulo } = resp;
    if (tipo == 'success') {
      if (data_firma.tipo == 'decano' ) buscar_profesor(profesor_sele.identificacion);
      data_firma = { plan: 0, tipo: '' };
      $(`#cargar_firma`).modal("hide");
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
};

const guardar_un_soporte_formacion = (id) =>{
	let data = new FormData(document.getElementById("form_soporte_academico"));
	data.append('id_formacion', id);
	enviar_formulario(`${ruta}guardar_un_soporte_formacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_enviar_archivos").modal("hide");
			$("#form_soporte_academico").get(0).reset();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_firma_digital = () => {
  MensajeConClase("validando info", "add_inv", "Oops...");
  let data = new FormData(document.getElementById('modificar_firma_digital'));
  let { plan, tipo } = data_firma
  data.append('id_plan', plan);
  data.append('tipo', tipo);
  enviar_formulario(`${ruta}modificar_firma_digital`, data, (resp) => {
    let { tipo, mensaje, titulo } = resp;
    if (tipo == 'success') {
      if (data_firma.tipo == 'decano' ) {
        buscar_profesor(profesor_sele.identificacion);
        data_firma = { plan: 0, tipo: '' };
      }
      $(`#modal_modificar_firma`).modal("hide");
      $("#modificar_firma_digital").get(0).reset();

    }
    MensajeConClase(mensaje, tipo, titulo);
  });
};



function importar(excel){

if(!excel.files) {
  return;
}
var f = excel.files[0];
var reader = new FileReader();
reader.readAsBinaryString(f);
reader.onload = function(e) {
  var data = e.target.result;
  excelFile = XLSX.read(data, {
    type: 'binary'
  });

  str = XLSX.utils.sheet_to_json(excelFile.Sheets[excelFile.SheetNames[0]])
  
  // //let fecha_f = "";
  // for (x of str) {
  //   if(Number.isInteger(x.fecha_inicio)){
  //   var exdate = x.fecha_inicio;
  //   var e0date = new Date(0);
  //   var offset = e0date.getTimezoneOffset();
  //   var fecha = new Date(0, 0, exdate-1, 0, -offset, 0);
  //   x.fecha_inicio = fecha.toLocaleDateString();
  //   }
  //   if(Number.isInteger(x.fecha_fin)){
  //     var exdate = x.fecha_fin;
  //     var e0date = new Date(0);
  //     var offset = e0date.getTimezoneOffset();
  //     var fecha = new Date(0, 0, exdate-1, 0, -offset, 0);
  //     x.fecha_fin = fecha.getFullYear()+"-"+fecha.getMonth()+"-"+fecha.getDate();
  //     }
  //   // str.push(fecha_f);
  // }
  // console.log(str);
  
  $("#mostrar_import_excel").DataTable({
          destroy: true,
          processing: true,
          data: str,
          columns: [{
              data: "id_persona"
          },
          {
              data: "id_programa"
          },
          {
              data: "id_departamento"
          },
          {
              data: "id_area"
          },
          {
            data: "id_dedicacion"
          },
          {
              data: "id_escalafon"
          },
          {
              data: "id_contrato"
          },
          {
            data: "fecha_inicio"
          },
          {
              data: "fecha_fin"
          },
          {
              data: "id_grupo"
          },
          {
            data: "cvlac"
          },
          {
              data: "google"
          },
          {
              data: "scopus"
          },
          {
              data: "id_estado"
          },
          ],
          language: idioma,
          dom: "Bfrtip",
          buttons: get_botones(),

      });
    }
}

const GuardarImpor = () =>{
  let data = new FormData();
      data.append("array", JSON.stringify(str));
  enviar_formulario(`${ruta}guardar_import`, data, resp => {
    let { tipo, mensaje, titulo, id, guardados,noguardados } = resp;
    if (tipo == 'success') {
      $("#modal_importar_excel").modal("hide");
      var table = $('#mostrar_import_excel').DataTable();
      table.clear().draw();
      str.length = 0;
      document.getElementById("xFile").value = '';
      $("#modal_guardadosyno").modal();
      MostrarGuardaYNo(guardados,noguardados);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}
const MostrarGuardaYNo = (guardados,noguardados) =>{
  $("#mostrar_guardadosi").DataTable({
    destroy: true,
    processing: true,
    data: guardados,
    columns: [{
        data: "id_persona"
    },
    {
        data: "id_programa"
    },
    {
        data: "id_departamento"
    },
    {
        data: "id_area"
    },
    {
      data: "id_dedicacion"
    },
    {
        data: "id_escalafon"
    },
    {
        data: "id_contrato"
    },
    {
      data: "fecha_inicio"
    },
    {
        data: "fecha_fin"
    },
    {
        data: "id_grupo"
    },
    {
      data: "cvlac"
    },
    {
        data: "google"
    },
    {
        data: "scopus"
    },
    {
        data: "id_estado"
    },
    ],
    language: idioma,
    dom: "Bfrtip",
    buttons: get_botones(),

});
//
$("#mostrar_no_guardados").DataTable({
  destroy: true,
  processing: true,
  data: noguardados,
  columns: [{
      data: "id_persona"
  },
  {
      data: "id_programa"
  },
  {
      data: "id_departamento"
  },
  {
      data: "id_area"
  },
  {
    data: "id_dedicacion"
  },
  {
      data: "id_escalafon"
  },
  {
      data: "id_contrato"
  },
  {
    data: "fecha_inicio"
  },
  {
      data: "fecha_fin"
  },
  {
      data: "id_grupo"
  },
  {
    data: "cvlac"
  },
  {
      data: "google"
  },
  {
      data: "scopus"
  },
  {
      data: "id_estado"
  },
  ],
  language: idioma,
  dom: "Bfrtip",
  buttons: get_botones(),

});
}
const CambioTabla = (opc) =>{
  var x = document.getElementById("si_guardados");
  var y = document.getElementById("no_guardados");
  if (opc==1) {
      y.style.display = "block";
      x.style.display = "none";
  } else {
      x.style.display = "block";
      y.style.display = "none";
  }
}
// 
