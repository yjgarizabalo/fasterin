let ruta = `${Traer_Server()}index.php/proyectos_index_control/`;
let callbak_activo = (resp) => { };
let container_activo = '#txt_nombre_postulante';
let datos_postulante = null;
let datos_departamento = null;
let ruta_adjunto = 'archivos_adjuntos/proyectos/adjuntos/';
let ruta_archivos_proyetos = 'archivos_adjuntos/proyectos_index/';
let id_comite = null;
let tabla_buscar_persona = 'personas';
let id_proyecto = null;
let id_tipo_proyecto = null;
let id_aux_proyecto = null;
let tipo_mensaje_responsables = null;
let datos_text_area = [
  {
    id: 'resumen',
    texto: '',
    titulo: 'Agregar Resumen',
  },
  {
    id: 'justificacion',
    texto: '',
    titulo: 'Agregar Justificación'
  },
  {
    id: 'planteamiento_problema',
    texto: '',
    titulo: 'Agregar Planteamiento del Problema',
  },
  {
    id: 'marco_teorico',
    texto: '',
    titulo: 'Agregar Marco Teórico',
  },
  {
    id: 'estado_arte',
    texto: '',
    titulo: 'Agregar Estado del Arte',
  },
  {
    id: 'diseno_metodologico',
    texto: '',
    titulo: 'Agregar Diseño Metodológico',
  },
  {
    id: 'resultados_esperados',
    texto: '',
    titulo: 'Agregar Resultados Esperados',
  },
];
let responsables = [];
let cargados = 0;
let motivos_solicitud = null;
let tipo_solicitud = null;
let data_correo = { 'nombre_persona': null, 'nombre_proyecto': null, 'solicitud':null };
let no_beneficiarios = null;
let iva = null;
let cod_proyecto = null;

String.prototype.capitalize = function (lower) {
  return (lower ? this.toLowerCase() : this).replace(/(?:^|\s)\S/g, function (a) { return a.toUpperCase(); });
};

$(document).ready(() => {
  $('.alert-success').hide();
  $('textarea').attr('maxlength', '65535');
  $('input[type="text"]').attr('maxlength', '500');

  $("#btn_filtrar").click(() => {
    listar_proyectos(id_comite);
  });

  $("#limpiar_filtros").click(function () {
    datos_departamento = null;
    $('#codigo_proyecto_filtro_2').val('');
    $("#modal_crear_filtros input").val('');
    $("#modal_crear_filtros select").val('');
    listar_proyectos(id_comite);
  });

  $("#form_guardar_comite").submit((e) => {
    e.preventDefault();
    // guardar_comite_general('index');
    // cargar_comites();
    confirmar_guardar_comite('index');
  });

  $("#form_modificar_comite").submit((e) => {
    e.preventDefault();
    modificar_comite_general(id_comite);
    cargar_comites();
  });

  $('#limpiar_filtros_comite').click(() => {
    listar_comites();
  });

  $('#btn_buscar_postulante').click(() => {
    container_activo = '#txt_nombre_postulante';
    $("#txt_dato_buscar").val('');
    callbak_activo = (resp) => {
      mostrar_postulante_sele(resp);

    }
    buscar_postulante('', tabla_buscar_persona, callbak_activo);
    $("#modal_buscar_postulante").modal();
  });

  $('#btn_buscar_departamento').click(() => {
    $("#form_buscar_departamento").get(0).reset();
    container_activo = '#txt_departamento';
    $("#txt_dato_buscar").val('');
    callbak_activo = (resp) => {
      mostrar_departamento_sele(resp);
    }
    buscar_departamento('-1', callbak_activo);

    $("#modal_buscar_departamento").modal();
  });
  $('#btn_buscar_departamento_modi').click(() => {
    $("#form_buscar_departamento").get(0).reset();
    container_activo = '#txt_departamento_modi';
    $("#txt_dato_buscar").val('');
    callbak_activo = (resp) => {
      mostrar_departamento_sele(resp);
    }
    buscar_departamento('-1', callbak_activo);

    $("#modal_buscar_departamento").modal();
  });

  $('#btn_buscar_postulante_modi').click(() => {
    container_activo = '#txt_nombre_postulante_modi';
    $("#txt_dato_buscar").val('');
    callbak_activo = (resp) => {
      mostrar_postulante_sele(resp);
    }
    buscar_postulante('', tabla_buscar_persona, callbak_activo);
    $("#modal_buscar_postulante").modal();
  });

  $('#btn_buscar_investigador').click(() => {
    $("#txt_dato_buscar").val('');
    $('.radio_button').show();
    $('#docentes').prop('checked', true);
    callbak_activo = (resp) => agregar_participante(resp);
    tabla_buscar_persona = 'personas'
    buscar_postulante('', tabla_buscar_persona, callbak_activo);
    $("#modal_buscar_persona").modal();
  });

  const limpiar_tabla_personas = () => {
    $('#tabla_postulantes_busqueda').DataTable().clear().draw();
  }

  $('.tipo_persona_buscar').change(function () {
    if ($(this).attr('id') == 'docentes') {
      tabla_buscar_persona = 'personas';
      limpiar_tabla_personas();
    } else {
      tabla_buscar_persona = 'visitantes';
      limpiar_tabla_personas();
    }
  });

  const agregar_participante = (data) => {
    let form = '#form_agregar_participante';
    funcion_agregar(form, 'titulo_participante', 'Agregar Participante');

    let {
      id,
      nombre_completo
    } = data;

    $('#nombre_postulante').html(nombre_completo);
    $('#id_postulante').val(id);
    $('#tipo_tabla').val(tabla_buscar_persona == 'personas' ? 1 : 2);
  }

  $('#btn_buscar_departamento_filtro').click(() => {
    $("#form_buscar_departamento").get(0).reset();
    container_activo = '#txt_departamento_filtro';
    $("#txt_dato_departamento").val('');
    callbak_activo = (resp) => {
      mostrar_departamento_sele(resp);
    }
    buscar_departamento('-1', callbak_activo);
    $("#modal_buscar_departamento").modal();
  });

  $('#form_buscar_persona').submit(() => {
    let dato = $("#txt_dato_buscar").val();
    buscar_postulante(dato, tabla_buscar_persona, callbak_activo);
    return false;
  });
  $('#form_buscar_departamento').submit(() => {
    let dato = $("#txt_dato_departamento").val();
    buscar_departamento(dato, callbak_activo);
    return false;
  });

  $('#departamentos').change(function () {
    const id_departamento = $(this).val();
    listar_programas_departamentos(id_departamento);
  });

  $('#btn_administrar').click(() => {
    administrar_modulo('admin_aprobados_persona');
    $('#modal_administrar_index').modal();
  })

  $('#btn_asignar_persona_aprueba').click(() => {
    let persona = $("#cbx_personas_aprueban").val();
    asignar_persona_aprueba(persona);
  });

  $('#admin_aprobados_persona').click(function () {
    administrar_modulo('admin_aprobados_persona');
  });

  $('#admin_proyectos_persona').click(function () {
    administrar_modulo('admin_proyectos_persona');
  });

  $('#admin_proyectos_parametros').click(() => {
    administrar_modulo('admin_proyectos_parametros');
  });

  $('#btn_guardar_parametros_generales').click(() => {
    let formData = new FormData(document.getElementById('form_parametros_generales'));
    let data = formDataToJson(formData);
    consulta_ajax(`${ruta}guardar_datos_parametros_generales`, data, resp => {
      let { mensaje, tipo, titulo } = resp;
      MensajeConClase(mensaje, tipo, titulo);
    });
  });

  $('#admin_proyectos_instituciones').click(function () {
    administrar_modulo('admin_proyectos_instituciones');
  });

  $('#admin_proyectos_comite').click(function () {
    administrar_modulo('admin_proyectos_comite');
  });

  $("#form_guardar_comentario").submit(() => {
    guardar_comentario_comite(id_comite);
    return false;
  });

  $("#form_guardar_comentario_general").submit(() => {
    let comentario = $("#form_guardar_comentario_general input[name='comentario']").val();
    guardar_comentario_general({ comentario, 'id_solicitud': id_proyecto, 'tipo': 'proyectos' }, () => {
      $("#form_guardar_comentario_general").get(0).reset();
      pintar_comentarios_generales(id_proyecto, '#panel_comentarios_generales', 'Comentarios a este proyecto', 'proyectos');
    });
    return false;
  });

  $('.regresar_menu').click(() => {
    administrar_modulo('menu');
  });

  $('#listado_proyectos').click(() => {
    administrar_modulo('listado_proyectos');
  });

  $('#btn_nuevo_proyecto').click(async () => {
    let proyecto = await ultimo_proyecto();
    if (proyecto != null) {
      $('#btn_modificar_ultimo_proyecto').show();
    } else {
      $('#btn_modificar_ultimo_proyecto').hide();
    }
    $('#modal_pregunta_nuevo_proyecto').modal('show');
  });

  $('#btn_nuevo_proyecto_investigacion').click(() => {
    crear_proyecto('Pro_Inv');
    cod_proyecto = 'Pro_Inv'
  });

  $('#btn_nuevo_proyecto_bienestar_universitario').click(() => {
    crear_proyecto('Pro_Bien');
    cod_proyecto = 'Pro_Bien'
  });

  $('#btn_nuevo_proyecto_extension').click(() => {
    crear_proyecto('Pro_Ext');
    cod_proyecto = 'Pro_Ext'
  });

  $('#btn_nuevo_proyecto_docencia').click(() => {
    crear_proyecto('Pro_Doc');
    cod_proyecto = 'Pro_Doc'
  });

  $('#btn_nuevo_proyecto_laboratorios').click(() => {
    crear_proyecto('Pro_Lab');
    cod_proyecto = 'Pro_Lab'
  });

  $('#btn_nuevo_proyecto_gestion_universitaria').click(() => {
    crear_proyecto('Pro_Ges');
    cod_proyecto = 'Pro_Ges'
  });

  $('#btn_nuevo_proyecto_internacionalizacion').click(() => {
    crear_proyecto('Pro_Int');
    cod_proyecto = 'Pro_Int'
  });

  $('#btn_nuevo_proyecto_grado').click(() => {
    crear_proyecto('Pro_Gra');
    cod_proyecto = 'Pro_Gra'
  });

  const crear_proyecto = async (tipo_proyecto, id_departamento = null) => {
    let dato = await buscar_datos_valor_parametro(tipo_proyecto, 2);
    let tipo_participante = await buscar_datos_valor_parametro('Pro_Inv_Pri', 2);
    let institucion = await buscar_datos_valor_parametro('Pro_Ins_CUC', 2);
    let mensaje_crear_proyecto = 'Ten en cuenta que, si desea crear un nuevo proyecto, se creará en tu bandeja automáticamente para comenzar a editarlo, además verificar el tipo de proyecto ya que no se podrá cambiar una vez de creado.';
    mensaje_confirmar('¿Estás seguro?', mensaje_crear_proyecto, () => inicializar_proyecto(dato, tipo_participante.id, institucion.id, id_departamento));
  }

  $('#btn_modificar_ultimo_proyecto').click(() => {
    mostrar_ultimo_proyecto();
  });

  $('#btn_informacion_principal').click(() => {
    $('#modal_editar_proyecto').modal('show');
  });

  $('.text-area').click(function () {
    administrarModalArea($(this).attr('id').replace('modificar_', ''));
  });

  $('#btn_responsable_externo').click(() => {
    $("#txt_dato_buscar").val('');
    $('.radio_button').hide();
    callbak_activo = (resp) => {
      let { id, nombre_completo } = resp;
      $('#txt_nombre_responsable_externo').val(nombre_completo);
      $('#id_responsable_externo').val(id)
      $('#modal_buscar_persona').modal('hide');
    }
    tabla_buscar_persona = 'visitantes';
    buscar_postulante('', tabla_buscar_persona, callbak_activo);
    $('#modal_buscar_persona').modal('show');
  });

  $('#btn_eliminar_responsable_externo').click(() => {
    $('#txt_nombre_responsable_externo').val('');
    $('#id_responsable_externo').val(null);
  });

  $('#tipo_responsable').change(function () {
    if ($(this).val() == 'externo') {
      $('#input_responsable_externo').show('fast');
      $('#txt_nombre_responsable_externo').attr('required', true);
    } else {
      $('#input_responsable_externo').hide('fast');
      $('#txt_nombre_responsable_externo').removeAttr('required');
      $('#btn_eliminar_responsable_externo').click();
    }
  });

  $('#tipo_movilidad').change(async function () {
    let datos = await buscar_datos_valor_parametro($(this).val());
    if (datos.id_aux == 'Tipo_Mov_Otro') {
      $('.otro_tipo_movilidad').show('fast').attr('required', true);
    } else {
      $('.otro_tipo_movilidad').hide('fast').val('').removeAttr('required');
    }
  });

  $('#si_operacionaliza').click(() => {
    $('#codigo_convenio').show('fast').attr('required', true);
  });

  $('#no_operacionaliza').click(() => {
    $('#codigo_convenio').hide('fast').val('').removeAttr('required');
  });

  $('#btn_preguntas_convenio_proceedings').show().off('click').click(() => {
    let form = '#form_agregar_preguntas_convenio_proceedings';
    $(form).get(0).reset();
    traer_convenio_proceedings(id_proyecto);
  });

  let formulario = 'form_agregar_preguntas_convenio_proceedings';
  $(`#${formulario}`).off().submit((e) => {
    e.preventDefault();
    let formData = new FormData(document.getElementById(formulario));
    let data = formDataToJson(formData);
    data.id_proyecto = id_proyecto;
    consulta_ajax(`${ruta}guardar_preguntas_convenio_proceedings`, data, resp => {
      let { mensaje, tipo, titulo } = resp;
      MensajeConClase(mensaje, tipo, titulo);
      if (tipo == 'success') {
        $('#modal_agregar_preguntas_convenio_proceedings').modal('hide');
      }
    });
  });

  $('#btn_orden_sap').click(() => {
    $('#modal_buscar_codigo').modal('show');
    buscar_codigo_sap();
  });

  $("#form_buscar_codigo").submit((e) => {
    e.preventDefault();
    buscar_codigo_sap();
  });

  $('#btn_participantes').click(() => {
    listar_participantes(id_proyecto);
    $('#modal_participantes').modal('show');
  });

  $('#btn_agregar_persona').click(() => {
    callbak_activo = (resp) => {
      mostrar_postulante_sele(resp);
      $("#modal_agregar_persona").modal("hide");
      $("#form_agregar_persona").get(0).reset();
    }
    $("#modal_agregar_persona").modal();
  });

  $('#form_agregar_persona').submit(() => {
    agregar_persona();
    return false;
  });

  $('#btn_lugares').click(() => {
    listar_lugares(id_proyecto);
    $('#modal_lugares').modal('show');
  });

  $('#btn_agregar_lugar').click(() => {
    let form = '#form_agregar_lugar';
    funcion_agregar(form, 'titulo_lugar', 'Agregar Lugar');
  });

  $('#btn_instituciones').click(() => {
    listar_instituciones(id_proyecto);
    $('#modal_institucion').modal('show');
  });

  $('#btn_agregar_institucion').click(() => {
    let form = '#form_agregar_institucion';
    funcion_agregar(form, 'titulo_institucion', 'Agregar Institución');
  });

  $('#btn_agregar_institucion_bdd').click(() => {
    let form = '#form_agregar_institucion_bdd';
    funcion_agregar(form, 'titulo_institucion_bdd', 'Agregar Institución');
    $('#modal_agregar_institucion_bdd').modal('show');
  });

  $('#btn_programas').click(() => {
    listar_programas(id_proyecto);
    $('#modal_programas').modal('show');
  });

  $('#btn_agregar_programa').click(() => {
    let form = '#form_agregar_programa';
    funcion_agregar(form, 'titulo_programa', 'Agregar Programa');
  });

  $('#btn_asignaturas').click(() => {
    listar_asignaturas(id_proyecto);
    $('#modal_asignaturas').modal('show');
  });

  $('#btn_agregar_asignatura').click(() => {
    let form = '#form_agregar_asignatura';
    funcion_agregar(form, 'titulo_asignatura', 'Agregar Asignatura/Proyecto');
  });

  $('#btn_sublinea_investigacion').click(() => {
    listar_sublineas(id_proyecto);
    $('#modal_sublineas_investigacion').modal('show');
  });

  $('#btn_agregar_sublinea').click(() => {
    let form = '#form_agregar_sublinea';
    funcion_agregar(form, 'titulo_sublinea', 'Agregar Sublínea');
  });

  $('#linea_investigacion').change(async function () {
    let linea = $(this).val();
    if (linea) {
      let sub_lineas = await obtener_valores_permisos(linea, 88, 'inner');
      pintar_datos_parametros_combo(sub_lineas, '.cbx_sublinea_investigacion', 'Seleccione una Sub-Línea de Investigación');
    }
  });

  $('#btn_ods, #ver_detalle_ods').click(async () => {
    listar_ods(id_proyecto);
    cargar_listado_ods();
    $('#modal_ods').modal('show');
  });

  $('#btn_agregar_ods').click(() => {
    let form = '#form_agregar_ods';
    funcion_agregar(form, 'titulo_ods', 'Agregar Objetivo de Desarrollo Sostenible');
  });

  $('#btn_objetivos').click(() => {
    listar_objetivos(id_proyecto);
    $('#modal_objetivos').modal('show');
  });

  $('#btn_agregar_objetivo').click(() => {
    let form = '#form_agregar_objetivo';
    funcion_agregar(form, 'titulo_objetivo', 'Agregar Objetivo');
  });

  $('#btn_impactos').click(() => {
    listar_impactos(id_proyecto);
    $('#modal_impactos').modal('show');
  });

  $('#btn_agregar_impacto').click(() => {
    let form = '#form_agregar_impacto';
    funcion_agregar(form, 'titulo_impacto', 'Agregar Impacto');
  });

  $('#btn_productos').click(() => {
    tipo_mensaje_responsables = 1;
    listar_productos(id_proyecto);
    $('#modal_productos').modal('show');
  });

  $('#tipo_producto').change(async function () {
    let tipo_producto = $(this).val();
    if (tipo_producto) {
      let productos = await obtener_valores_permisos_general(tipo_producto, 175);
      pintar_datos_combo(productos, '.cbx_productos', 'Seleccione un Producto');
    }
  });

  $('#btn_agregar_producto').click(() => {
    responsables = [];
    $('#txt_numero_participantes_producto').val('Ningún Participante asignado');
    let form = '#form_agregar_producto';
    funcion_agregar(form, 'titulo_producto', 'Agregar Producto');
  });

  $('.btn_buscar_responsable').click(() => {
    listar_responsables(id_proyecto, tipo_mensaje_responsables);
    $('#modal_agregar_responsable').modal('show');
  });

  $('#btn_cronograma').click(() => {
    tipo_mensaje_responsables = 2;
    listar_cronogramas(id_proyecto);
    $('#modal_cronograma').modal('show');
  });

  $('#btn_agregar_cronograma').click(() => {
    responsables = [];
    $('#txt_numero_participantes_cronograma').val('Ningún Participante asignado');
    let form = '#form_agregar_cronograma';
    cargar_cbx_objetivos(id_proyecto);
    funcion_agregar(form, 'titulo_cronograma', 'Agregar Cronograma');
  });

  $('#btn_presupuesto').click(() => {
    listar_resumen_presupuestos(id_proyecto);
    listar_presupuesto_discriminado_entidad(id_proyecto);
    listar_presupuesto_discriminado_entidad_rubro(id_proyecto);
    listar_presupuesto_financiacion(id_proyecto);
    $('#modal_resumen_presupuesto').modal('show');
  });

  $('#btn_agregar_presupuesto').click(() => {
    let tipo_presupuesto = $('#ver_tipo_presupuesto').val();
    let form = '#form_agregar_presupuesto';
    funcion_agregar(form, 'titulo_presupuesto', 'Agregar Presupuesto', tipo_presupuesto);
    cambiar_form_presupuesto(tipo_presupuesto);
    $('#tipo_presupuesto').val(tipo_presupuesto);
    $('#modal_agregar_presupuesto').modal('show');
  });

  $('#btn_soportes').click(() => {
    listar_soportes(id_proyecto);
    $('#modal_agregar_soportes').modal('show');
  });

  $('#agregar_adjuntos_nuevos').click(() => {
    myDropzone.removeAllFiles(true);
    $('#modal_enviar_archivos').modal('show');
  });

  $('#cargar_adj').click(() => {
    if (cargados == 0) {
      MensajeConClase('Por favor cargar al menos un archivo', 'info', 'Oops!');
    } else {
      myDropzone.processQueue();
    }
  });

  $('#btn_bibliografia').click(() => {
    listar_bibliografias(id_proyecto);
    $('#modal_bibliografia').modal('show');
  });

  $('#btn_agregar_bibliografia').click(() => {
    let form = '#form_agregar_bibliografia';
    funcion_agregar(form, 'titulo_bibliografia', 'Agregar Bibliografía');
  });

  $('#form_agregar_comite').submit((e) => {
    e.preventDefault();
    let id_comite = $('#cbx_lista_comite').val();
    let observaciones = $('#observaciones_agregar_comite').val();
    consulta_ajax(`${ruta}asignar_proyecto_solicitud`, { id_proyecto, id_comite, observaciones }, resp => {
      let { tipo, mensaje, titulo } = resp
      if (tipo != 'success') {
        MensajeConClase(mensaje, tipo, titulo);
      } else {
        swal.close();
        listar_proyectos_usuario();
        listar_comites();
        $('#modal_lista_comite').modal('hide');
        $('#cbx_lista_comite').val('').change();
      }
    });
  });

  $('.buscar_item_motivo').submit((e) => {
    e.preventDefault();
    listar_items();
  });

  $('#btn_solicitud_proyecto').click(() => {
    if (tipo_solicitud == 1) {
      let motivos_enviar = motivos_solicitud.filter((e) => e.razones != null);
      if (motivos_enviar.length == 0) {
        MensajeConClase('No has seleccionado ningún motivo', 'info', 'Oops!');
      } else {
        mensaje_confirmar('¿Estás seguro?', 'Ten en cuenta que si envias la solicitud, no la vas a poder modificar', () => {
          swal.close();
          consulta_ajax(`${ruta}guardar_solicitud_proyecto`, { id_proyecto, motivos_enviar }, datos => {
            let { mensaje, tipo, titulo } = datos;
            MensajeConClase(mensaje, tipo, titulo);
            if (tipo == 'success') {
              $('#modal_solicitudes_modificar').modal('hide');
              listar_proyectos_usuario();
              data_correo.solicitudes = motivos_enviar;
              enviar_correo('solicitud_correccion', id_proyecto);
            }
          });
        });
      }
    } else {
      if (motivos_solicitud.find((e) => e.aprobado == null)) {
        MensajeConClase('Hay algún ítem sin aprobar o negar', 'info', 'Oops!');
      } else {
        mensaje_confirmar('¿Estás seguro?', 'Ten en cuenta que si envias la solicitud, no la vas a poder modificar', () => {
          swal.close();
          consulta_ajax(`${ruta}aprobar_negar_solicitud_proyecto`, { id_proyecto, motivos_solicitud }, datos => {
            let { mensaje, tipo, titulo } = datos;
            MensajeConClase(mensaje, tipo, titulo);
            if (tipo == 'success') {
              $('#modal_solicitudes_modificar').modal('hide');
              listar_proyectos_usuario();
              data_correo.solicitudes = motivos_solicitud;
              enviar_correo('respuesta_solicitud_correccion', id_proyecto);
            }
          });
        });
      }
    }
  });

  $('.guardar_proyecto').submit((e) => {
    e.preventDefault();
    guardar_proyecto()
  });

  $('#btn_notificaciones').click(() => {
    //mostrar_notificaciones_comentarios_comite('index', (id) => { abrir_proyectos_notificaciones(id); });
    pintar_notificaciones_comentarios_general('cc.tipo = "proyectos"', ['Per_Admin', 'Per_Adm_index'], '#panel_notificaciones_generales', '.n_notificaciones', '#modal_notificaciones', 'Notificaciones Proyectos', abrir_proyecto);
    $("#modal_notificaciones").modal();
  });

  $('#btn_notificaciones_proyectos').click(() => {
    mostrar_notificaciones();
    $("#modal_notificaciones_proyectos").modal('show');
  });

  $('#btn_filtrar_proyectos').click(() => {
    listar_proyectos_usuario();
  });

  $('#btn_limpiar_filtros').click(() => {
    $('.mensaje-filtro').hide();
    $('#codigo_proyecto_filtro').val('');
    $('#tipo_proyecto_filtro').val('');
    $('#estado_proyecto_filtro').val('');
    listar_proyectos_usuario();
  });

  $('#permiso_persona').click(() => {
    $("#txt_dato_buscar").val('');
    $('.radio_button').hide();
    callbak_activo = (resp) => {
      let { id, nombre_completo } = resp;
      $('#permiso_persona').html(nombre_completo);
      $('#persona_solicitud_id').val(id)
      listar_actividades(id);
      $('#modal_buscar_persona').modal('hide');
    }
    tabla_buscar_persona = 'personas';
    buscar_postulante('', tabla_buscar_persona, callbak_activo);
    $('#modal_buscar_persona').modal('show');
  });

  $("#form_seleccion_departamento").submit((e) => {
    e.preventDefault();
    let id_departamento = $("#departamento_proyecto").val();
    crear_proyecto(cod_proyecto, id_departamento)
  });

});

const funcion_agregar = (form, id, titulo, tipo_presupuesto = null) => {
  $(form).get(0).reset();
  $(form).off();
  $(form.replace('form', 'modal')).modal('show');

  let icono = 'fa fa-plus-circle';
  cambiar_titulo(icono, id, titulo);

  $(form).submit((e) => {
    e.preventDefault();
    if (form.replace('#form_agregar_', '') == 'producto' && responsables.length == 0) {
      MensajeConClase('No has asignado ningún participante al producto', 'info', 'Oops!');
      return false;
    }
    if (form.replace('#form_agregar_', '') == 'cronograma' && responsables.length == 0) {
      MensajeConClase('No has asignado ningún participante al cronograma', 'info', 'Oops!');
      return false;
    }
    guardar_item(form.replace('#', ''), form.replace('#form_agregar_', ''), tipo_presupuesto);
  });
}

const cambiar_informacion_tipo_proyecto = async (id, id_estado_proyecto, id_aux_tipo_proyecto) => {
  let impactos = await obtener_valores_permisos_general(id, 173);
  pintar_datos_combo(impactos, '.cbx_tipo_impacto', 'Seleccione un tipo de Impacto');

  $('.informacion_a_cambiar').hide().removeAttr('required');

  let datos_modificar_solicitud = await listar_motivos_solicitud(2);
  let datos_mostrar = await obtener_valores_permisos_general(id, 184);
  (id_estado_proyecto == 'Proy_Apr') ? $('#ver_detalle_correcciones').show() : $('#ver_detalle_correcciones').hide();
  (id_aux_tipo_proyecto == 'Pro_Lab') ? $('.presupuesto_laboratorio').show() : $('.presupuesto_laboratorio').hide();
  (id_aux_tipo_proyecto == 'Pro_Int') ? $('.internacionalizacion').show() : $('.internacionalizacion').hide();
  (id_aux_tipo_proyecto == 'Pro_Inv') ? $('.investigacion').show() : $('.investigacion').hide();

  datos_mostrar.forEach(dato => {
    $(`.${dato.valorz}`).show();

    if (datos_modificar_solicitud.length > 0) {
      let dato_modificar = datos_modificar_solicitud.find((e) => e.vp_id == dato.id);
      if (dato_modificar) $(`.${dato.valora}`).show().attr('required', true);
    } else if (id_estado_proyecto == 'Proy_For') {
      $(`.${dato.valora}`).show().attr('required', true);
    } else {
      $(`.${dato.valora}`).hide().removeAttr('required');
    }
  });
}

const cambiar_form_presupuesto = async (tipo_presupuesto) => {
  $('#tipo_de_valor').off('change');
  let inputs = await obtener_valores_permisos_general(tipo_presupuesto, '177');
  let div = $('#inputs_presupuesto').addClass('col-md-8 col-md-offset-2');
  let software = null;
  div.html('');
  await inputs.forEach(async input => {
    let { id_aux, valor, valorx, valory, valorz } = input;
    software = id_aux == 'Pre_Tipo_Sof';
    let input_html;
    switch (valorx) {
      case 'Texto':
        input_html = $('<input>').attr('type', 'text');
        break;
      case 'Numerico':
        input_html = $('<input>').attr('type', 'number');
        break;
      case 'Select':
        let nombre = valor.toLowerCase().replace(/ /g, '_');
        input_html = $('<select></select>').addClass(`cbx_${nombre}`).attr('id', nombre);
        div.append(input_html);
        if (valorz == 'Pre_Inv') {
          let temp = await traer_participantes(id_proyecto);
          let participantes = [];
          temp.forEach(participante => participantes.push({ 'id': participante.id, 'valor': participante.nombre_completo }));
          pintar_datos_combo(participantes, `.cbx_${nombre}`, `Seleccione el/la ${valor.toLowerCase()}`);
        } else {
          Cargar_parametro_buscado(parseInt(valorz), `.cbx_${nombre}`, valor.capitalize(true));
        }
        break;
    }
    if (valory == '1') input_html.attr('required', 'true');
    input_html.addClass('form-control').attr('name', valor.toLowerCase().replace(/ /g, '_')).attr('placeholder', valor).attr('title', valor);
    div.append(input_html);
  });
  change_tipo_presupuesto(null, software);
  change_tipo_software();
  change_tipo_recurso();
  change_tipo_financiacion();
  change_tipo_financiacion_internacional();
  change_tipo_financiacion_nacional();
}

const change_tipo_presupuesto = (id = null, software = null) => {
  $('#tipo_de_valor').change(async function () {
    let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
    if (datos.id_aux == 'Pre_Efec') {
      $('#consignado_a_la_cuc').show('fast').attr('required', true).removeAttr('disabled');
      if (software) { $('#grupo_investigacion').show('fast').attr('required', true).removeAttr('disabled') }
    } else {
      $('#consignado_a_la_cuc').val('').hide('fast').removeAttr('required').attr('disabled', true);
      if (software) { $('#grupo_investigacion').val('').hide('fast').removeAttr('required').attr('disabled', true) }
    }
  }).change()
}

const change_tipo_recurso = (id = null) => {
  $('#tipo_de_recurso').change(async function () {
    let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
    if (datos.id_aux == 'Rec_Int') {
      $('#consignado_a_la_cuc').show('fast').attr('required', true).removeAttr('disabled');
      $('#entidad_responsable').hide('fast').removeAttr('required').attr('disabled', true);
    } else if (datos.id_aux == 'Rec_Ext') {
      $('#consignado_a_la_cuc').val('').hide('fast').removeAttr('required').attr('disabled', true);
      $('#entidad_responsable').show('fast').attr('required', true).removeAttr('disabled');
    } else {
      $('#entidad_responsable').hide('fast').removeAttr('required').attr('disabled', true);
      $('#consignado_a_la_cuc').val('').hide('fast').removeAttr('required').attr('disabled', true);
    }
  }).change()
}

const change_tipo_financiacion = (id = null) => {
  $('#tipo_de_financiacion').change(async function () {
    if ($(this).val() === "") {
      $('#financiacion_nacional').val('').hide('fast').removeAttr('required').attr('disabled', true);
      $('#financiacion_internacional').val('').hide('fast').removeAttr('required').attr('disabled', true);
    } else {
      let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
      if (datos.id_aux == 'Fin_Nac') {
        $('#financiacion_nacional').show('fast').attr('required', true).removeAttr('disabled');
        $('#financiacion_internacional').val('').hide('fast').removeAttr('required').attr('disabled', true);
      } else if (datos.id_aux == 'Fin_Int') {
        $('#financiacion_internacional').show('fast').attr('required', true).removeAttr('disabled');
        $('#financiacion_nacional').val('').hide('fast').removeAttr('required').attr('disabled', true);
      }
    }
    $('#entidad_responsable').val('').hide('fast').removeAttr('required').attr('disabled', true);
  }).change()
}

const change_tipo_financiacion_internacional = (id = null, id_entidad = null) => {
  $('#financiacion_internacional').change(async function () {
    let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
    if (datos.id_aux == 'Fin_Int_Ins') {
      let temp = await traer_entidades(1);
      let entidades = [];
      temp.forEach(entidad => entidades.push({ 'id': entidad.id, 'valor': entidad.nombre }));
      pintar_datos_combo(entidades, `.cbx_entidad_responsable`, `Seleccione la Entidad Responsable`);
      $('#entidad_responsable').show('fast').attr('required', true).removeAttr('disabled');
      if (id_entidad != null) { $('#entidad_responsable').val(id_entidad) }
    } else {
      $('#entidad_responsable').val('').hide('fast').removeAttr('required').attr('disabled', true);
    }
  }).change()
}

const change_tipo_financiacion_nacional = (id = null, id_entidad = null) => {
  $('#financiacion_nacional').change(async function () {
    let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
    if (datos.id_aux == 'Fin_Nac_Ins') {
      let temp = await traer_entidades(2);
      let entidades = [];
      temp.forEach(entidad => entidades.push({ 'id': entidad.id, 'valor': entidad.nombre }));
      pintar_datos_combo(entidades, `.cbx_entidad_responsable`, `Seleccione la Entidad Responsable`);
      $('#entidad_responsable').show('fast').attr('required', true).removeAttr('disabled');
      if (id_entidad != null) { $('#entidad_responsable').val(id_entidad) }
    } else {
      $('#entidad_responsable').val('').hide('fast').removeAttr('required').attr('disabled', true);
    }
  }).change()
}

const change_tipo_software = (id = null, id_entidad = null, id_empresa = null, id_grupo = null) => {
  $('#tipo_de_software').change(async function () {
    let datos = await buscar_datos_valor_parametro(!$(this).val() ? id : $(this).val());
    if (datos.id_aux == 'Sof_Des_Int') {
      $('#empresa_prestadora_del_servicio').val('').hide('fast').removeAttr('required').attr('disabled', true);
      $('#entidad_responsable').val('').hide('fast').removeAttr('required').attr('disabled', true);
      $('#grupo_investigacion').show('fast').attr('required', true).removeAttr('disabled');
      if (id_grupo != null) { $('#grupo_investigacion').val(id_grupo) }
    } else {
      $('#empresa_prestadora_del_servicio').show('fast').attr('required', true).removeAttr('disabled');
      $('#entidad_responsable').show('fast').attr('required', true).removeAttr('disabled');
      $('#grupo_investigacion').val('').hide('fast').removeAttr('required').attr('disabled', true);
      if (id_entidad != null) { $('#entidad_responsable').val(id_entidad) }
      if (id_empresa != null) { $('#empresa_prestadora_del_servicio').val(id_empresa) }
    }
  }).change()
}

const cambiar_titulo = (icono, id, titulo) => {
  let span_icono = $('<span></span>').addClass(icono);
  $(`#${id}`).html(span_icono).append(` ${titulo}`);
}

const cargar_laboratorios = async () => {
  let datos = await obtener_valores_permisos_general('Bloq_Labs', 116, 2);
  $('#id_laboratorio').html(`<option value = ''>Seleccione un Laboratorio</option>`);
  datos.forEach(dato => {
    let { id, valor } = dato;
    $('#id_laboratorio').append(`<option value = "${id}">${valor}</option>`);
  });
}

const cargar_empresas = async () => {
  let datos = await traer_valores_parametro('178');
  $('.cbx_institucion').html(`<option value = ''>Seleccione una Institución</option>`);
  datos.forEach(dato => {
    let { id, valor } = dato;
    $('.cbx_institucion').append(`<option value = "${id}">${valor}</option>`);
  });
}

const cargar_comites = () => {
  consulta_ajax(`${ruta}listar_comites_cbx`, '', (resp) => {
    let select = '#cbx_lista_comite';
    $(select).html(`<option value = ''>Seleccione un comité</option>`);
    resp.forEach(dato => {
      let { id, nombre, estado_comite, id_estado_comite } = dato;
      $(select).append(`<option value="${id}" idestadocomite="${id_estado_comite}">${nombre} - ${estado_comite}</option>`);
    });
    $('#cbx_lista_comite').off('change').change(function () {
      let value = $(this).val();
      $(this).children().each(function() {
        let value2 = $(this).val();
        if (value == value2) {
          let id_estado_comite = $(this).attr('idestadocomite');
          if (id_estado_comite == 'Com_Not' || id_estado_comite == 'Com_Ter') {
            $('#observaciones_agregar_comite').show('fast').attr('required', true);
          } else {
            $('#observaciones_agregar_comite').val('').hide('fast').removeAttr('required');
          }
        }
      });
    });
  });
}

const cargar_listado_ods = async () => {
  let temp = await obtener_valores_parametro(172);
  datos = [];
  temp.forEach(elemento => {
    datos.push(elemento.valor);
  });
  let collator = new Intl.Collator(undefined, { numeric: true, sensitivity: 'base' });
  datos.sort(collator.compare);
  let combo = '.cbx_ods';
  $(combo).html(`<option value=''>Seleccione el Obejtivo de Desarrollo Sostenible</option>`);
  datos.forEach(elemento => {
    dato = temp.find((e) => e.valor == elemento);
    $(combo).append(`<option value='${dato.id}' title='${dato.valorx}'> ${dato.valor}</option>`);
  });
}

const buscar_codigo_sap = () => {
  buscar_codigo($("#txt_codigo_sap").val(), (data) => {
    $('#id_codigo_sap').val(data.id);
    $('#txt_nombre_orden_sap').val(data.valor);
    $('#modal_buscar_codigo').modal('hide');
    $('#txt_codigo_sap').val('');
  });
}

const buscar_codigo = (buscar, callback, idparametro = '25') => {
  let server_app = Traer_Server();
  $('#tabla_codigos tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(3)');
  const myTable = $("#tabla_codigos").DataTable({
    "destroy": true,
    "searching": false,
    "ajax": {
      url: `${server_app}index.php/presupuesto_control/buscar_codigo_sap`,
      data: {
        buscar,
        idparametro
      },
      "dataSrc": json => {
        return json.length == 0 ? Array() : json.data;
      },
      dataType: "json",
      type: "post",
    },
    "processing": true,
    "columns": [{
      "data": "valor"
    },
    {
      "data": "valorx"
    },
    {
      "data": "gestion"
    },
    ],
    "language": get_idioma(),
    dom: 'Bfrtip',
    "buttons": [],
  });

  //EVENTOS DE LA TABLA ACTIVADOS
  $('#tabla_codigos tbody').on('dblclick', 'tr', function () {
    let data = myTable.row(this).data();
    callback(data);
  });
  $('#tabla_codigos tbody').on('click', 'tr td:nth-of-type(3)', function () {
    let data = myTable.row($(this).parent()).data();
    callback(data);
  });

}

const mostrar_notificaciones_comite = async (tipo = 1) => {
  let modal = (tipo == 1) ? '#modal_notificaciones_proyectos' : '#modal_notificaciones';
  await pintar_notificaciones_comentarios_general('cc.tipo = "proyectos"', ['Per_Admin', 'Per_Adm_index'], '#panel_notificaciones_generales', '.n_notificaciones', modal, 'Notificaciones Proyectos', abrir_proyecto);
}

const mostrar_notificaciones = async () => {
  await mostrar_notificaciones_comite();
  consulta_ajax(`${ruta}mostrar_notificaciones_proyectos`, {}, datos => {
    pintar_notificaciones_proyectos({ 'container': '#panel_notificaciones', 'titulo': 'Proyectos devueltos o cancelados' }, datos);
    if (datos.length > 0) $('#modal_notificaciones_proyectos').modal('show');
    consulta_ajax(`${ruta}mostrar_notificaciones_solicitudes`, {}, datos => {
      pintar_notificaciones_solicitudes({ 'container': '#panel_notificaciones_solicitudes', 'titulo': 'Solicitudes para corrección de proyectos' }, datos);
      if (datos.length > 0) $('#modal_notificaciones_proyectos').modal('show');
      consulta_ajax(`${ruta}mostrar_notificaciones_solicitudes_respuestas`, {}, datos => {
        pintar_notificaciones_solicitudes_respuestas({ 'container': '#panel_notificaciones_solicitudes_respuestas', 'titulo': 'Respuestas de Solicitudes de Correción de proyectos' }, datos);
        if (datos.length > 0) $('#modal_notificaciones_proyectos').modal('show');
        numero_notificaciones();
      });
    });
  });
}

const pintar_notificaciones_proyectos = (data, resp) => {
  let { container, titulo } = data;
  let resultado = ``;
  let estado_proyecto = '';
  resp.map(i => {
    estado_proyecto = i.id_tipo == 'Proy_For' ? 'Proyecto Devuelto' : 'Proyecto Cancelado';
    resultado += `
        <a class="list-group-item">
          <span style="background-color: #39B23B; font-weight: 500;" class="badge pointer" onclick='marcar_visto(${i.id})'>Visto <span class="fa fa-check"></span></span>
          <span style="font-weight: 500;" class="badge pointer" onclick='ver_proyecto(${i.id_proyecto})'><span class="fa fa-eye"></span> Proyecto</span>
          <span>
            <h4 class="list-group-item-heading">${estado_proyecto}</h4>
            <p class="list-group-item-text text-left">Nombre: ${i.nombre_proyecto}</p>
            <p class="list-group-item-text text-left">Reporte No. ${i.id} - realizado el: ${i.fecha_registro}</p>
            <p class="list-group-item-text text-left" style="margin-top: 10px;"><b>${i.nombre_completo}</b>: ${i.observaciones}</p>
          </span>
        </a>
      `;
  });
  $(container).html(`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`);
}

const pintar_notificaciones_solicitudes = (data, resp) => {
  let { container, titulo } = data;
  let resultado = ``;
  resp.map(i => {
    let datos = [];
    i.items.forEach((item) => datos.push(item.nombre));
    resultado += `
        <a class="list-group-item">
          <span style="font-weight: 500;" class="badge pointer" onclick='ver_proyecto(${i.id_proyecto})'><span class="fa fa-eye"></span> Proyecto</span>
          <span>
            <h4 class="list-group-item-heading">Proyecto a Corregir</h4>
            <p class="list-group-item-text text-left">Nombre: ${i.nombre_proyecto}</p>
            <p class="list-group-item-text text-left">Reporte No. ${i.id} - realizado el: ${i.fecha_registro}</p>
            <p class="list-group-item-text text-left" style="margin-top: 10px;"><b>Datos a corregir</b>: ${datos.join(', ')}</p>
          </span>
        </a>
    `;
  });
  $(container).html(`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`);
}

const pintar_notificaciones_solicitudes_respuestas = (data, resp) => {
  let { container, titulo } = data;
  let resultado = ``;
  resp.map(i => {
    let mensaje = (i.id_tipo == 'Proy_Sol_Apr') ? `<b>N° de Items aprobados</b>: ${i.items.filter((e) => e.aprobado == '1').length}/${i.items.length}` : 'Ningún ítem fue aprobado';
    resultado += `
        <a class="list-group-item">
          <span style="background-color: #39B23B; font-weight: 500;" class="badge pointer" onclick='marcar_visto(${i.id})'>Visto <span class="fa fa-check"></span></span>
          <span style="background-color: #2E79E5; font-weight: 500;" class="badge pointer" onclick='ver_detalles(${JSON.stringify(i.items)})'><span class="fa fa-info-circle"></span> Detalles</span>
          <span style="font-weight: 500;" class="badge pointer" onclick='ver_proyecto(${i.id_proyecto})'><span class="fa fa-eye"></span> Proyecto</span>
          <span>
            <h4 class="list-group-item-heading">${(i.id_tipo == 'Proy_Sol_Apr') ? 'Solicitud Aprobada' : 'Solicitud Rechazada'}</h4>
            <p class="list-group-item-text text-left">Nombre: ${i.nombre_proyecto}</p>
            <p class="list-group-item-text text-left">Reporte No. ${i.id} - realizado el: ${i.fecha_registro}</p>
            <p class="list-group-item-text text-left" style="margin-top: 10px;">${mensaje}</p>
          </span>
        </a>
    `;
  });
  $(container).html(`<ul class="list-group"><li class="list-group-item active"><span class="badge">${resp.length}</span>${titulo}</li>${resultado}</ul>`);
}

const ver_detalles = (items) => {
  let icon = $('<span></span>').addClass('fa fa-info-circle');
  $('#ver_titulo').html(icon).append(' Detalles de la solicitud');
  $('#ver_descripcion').html('');

  items.forEach((item) => {
    let { nombre, aprobado, fecha_limite } = item;
    let badge = aprobado == 1 ? '<span style="background-color: #39B23B;" class="badge"><span class="fa fa-check"></span></span>' : '<span style="background-color: #CA3E33;" class="badge"><span class="fa fa-times"></span></span>';
    let item_mostrar = $(`<p>${badge} ${nombre} ${aprobado == 1 ? ' - fecha límite: ' + fecha_limite : ''}</p>`);
    $('#ver_descripcion').append(item_mostrar);
  });

  $('#modal_ver').modal('show');
}

const ver_proyecto = id => {
  listar_proyectos_usuario(id);
}

const marcar_visto = id => {
  consulta_ajax(`${ruta}marcar_visto`, { id }, resp => {
    if (resp.tipo == 'success') {
      mostrar_notificaciones();
    }
  })
}

const numero_notificaciones = () => {
  let temp = 0;
  $('#notificaciones_body li.list-group-item .badge').each(function() {
    temp += parseInt($(this).html());
  });
  $('#proyectos_notificaciones').html(temp);
}

const listar_comites = () => {
  consulta_ajax(`${ruta}listar_comites`, '', (resp) => {
    $(`#tabla_comite tbody`).off('click', 'tr td:nth-of-type(1)').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .modificar').off('click', 'tr td .enviar').off('click', 'tr td .terminar');
    const myTable = $("#tabla_comite").DataTable({
      "destroy": true,
      "processing": true,
      'data': resp,
      "columns": [
        {
          'data': 'ver'
        },
        {
          'data': 'nombre'
        },
        {
          'data': 'fecha_cierre'
        },
        {
          'data': 'descripcion'
        },
        {
          'data': 'total'
        },
        {
          'data': 'creado_por'
        },
        {
          'data': 'estado'
        },
        {
          'data': 'accion'
        },
      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    $('#tabla_comite tbody').on('click', 'tr', function () {
      let { id } = myTable.row(this).data();
      id_comite = id;
      $("#tabla_comite tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_comite tbody').on('dblclick', 'tr', function () {
      let data = myTable.row(this).data();
      if (data.antiguo == '1') {
        $('#departamento_filtro').hide();
        $('#id_programa').hide();
        $('#nombre_grupo').hide();
        $('#tipo_recurso').hide();
        $('#estado_proyecto').hide();
      } else {
        $('#departamento_filtro').show();
        $('#id_programa').show();
        $('#nombre_grupo').show();
        $('#tipo_recurso').show();
        $('#estado_proyecto').show();
      }
      mostrar_detalle_comite(data);
    });

    $('#tabla_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row(this).data();
      if (data.antiguo == '1') {
        $('#departamento_filtro').hide();
        $('#id_programa').hide();
        $('#nombre_grupo').hide();
        $('#tipo_recurso').hide();
        $('#estado_proyecto').hide();
      } else {
        $('#departamento_filtro').show();
        $('#id_programa').show();
        $('#nombre_grupo').show();
        $('#tipo_recurso').show();
        $('#estado_proyecto').show();
      }
      mostrar_detalle_comite(data);
    });
    $('#tabla_comite tbody').on('click', 'tr td .modificar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      mostrar_datos_comite_modificar(id);
    });
    $('#tabla_comite tbody').on('click', 'tr td .enviar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_comite(id, 'Com_Not');
    });

    $('#tabla_comite tbody').on('click', 'tr td .terminar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_comite(id, 'Com_Ter');
    });

  });

}

const cambiar_estado_comite = (id, estado) => {
  swal({
    title: "Cambiar Estado .?",
    text: "Tener en cuenta que, al modificar el estado del comité este se habilitara para el siguiente proceso, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
        consulta_ajax(`${ruta}cambiar_estado_comite`, { id, estado }, async (resp) => {
          let { tipo, mensaje, titulo, abrir } = resp;
          if (tipo == 'success') {
            swal.close();
            listar_comites();
            cargar_comites();
            listar_proyectos_usuario();
          } else MensajeConClase(mensaje, tipo, titulo);
          if (abrir) {
            listar_proyectos(id)
            $('#modal_detalle_solicitud').modal();
          }
        });
      }
    });
}

const buscar_departamento = (dato, callbak) => {
  consulta_ajax(`${ruta}buscar_departamento`, { dato }, (resp) => {
    $(`#tabla_buscar_departamento tbody`).off('click', 'tr td .departamento').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
    let i = 0;
    const myTable = $("#tabla_buscar_departamento").DataTable({
      "destroy": true,
      "searching": false,
      "processing": true,
      'data': resp,
      "columns": [
        {
          "render": function (data, type, full, meta) {
            i++; return i;
          }
        },
        {
          "data": "valor"
        },
        {
          'defaultContent': '<span style="color: #39B23B;" title="Seleccionar Departamento" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default departamento" ></span>'
        },

      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    $('#tabla_buscar_departamento tbody').on('click', 'tr', function () {
      $("#tabla_buscar_departamento tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $('#tabla_buscar_departamento tbody').on('dblclick', 'tr', function () {
      let data = myTable.row(this).data();
      callbak(data);
    });
    $('#tabla_buscar_departamento tbody').on('click', 'tr td .departamento', function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });

  });

}
const buscar_postulante = (dato, tabla, callbak) => {
  consulta_ajax(`${ruta}buscar_postulante`, { dato, tabla }, (resp) => {
    $(`#tabla_postulantes_busqueda tbody`).off('click', 'tr td .postulante').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
    const myTable = $("#tabla_postulantes_busqueda").DataTable({
      "destroy": true,
      "searching": false,
      "processing": true,
      'data': resp,
      "columns": [{
        "defaultContent": `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
      }, {
        "data": "nombre_completo"
      },
      {
        'data': 'identificacion'
      },
      {
        'defaultContent': '<span style="color: #39B23B;" title="Seleccionar Postulante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default postulante" ></span>'
      },

      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    $('#tabla_postulantes_busqueda tbody').on('click', 'tr', function () {
      $("#tabla_postulantes_busqueda tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $('#tabla_postulantes_busqueda tbody').on('dblclick', 'tr', function () {
      let data = myTable.row(this).data();
      callbak(data);
    });
    $('#tabla_postulantes_busqueda tbody').on('click', 'tr td .postulante', function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });

    $('#tabla_postulantes_busqueda tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row($(this).parent()).data();
      ver_detalle_postulante(data);
    });
  });

}
const ver_detalle_postulante = (data, container = '#tabla_detalle_persona', modal = '#modal_detalle_persona') => {
  let { id, nombre_completo, fecha_nacimiento, identificacion, tipo_identificacion } = data;
  datos_postulante = { id, nombre_completo }
  $(`${container} .tipo_identificacion`).html(tipo_identificacion);
  $(`${container} .fecha_nacimiento`).html(fecha_nacimiento);
  $(`${container} .nombre_completo`).html(nombre_completo);
  $(`${container} .identificacion`).html(identificacion);
  $(modal).modal();
}
const mostrar_postulante_sele = (data, tipo_c = 'add') => {
  let { id, nombre_completo } = data;
  datos_postulante = { id, nombre_completo }
  $(container_activo).val(nombre_completo);
  $("#modal_buscar_postulante").modal('hide');
  $("#modal_agregar_postulante").modal('hide');
}

const mostrar_departamento_sele = (data) => {
  let { id, valor } = data;
  datos_departamento = { id: id, valor: valor }
  $(container_activo).val(valor);
  listar_programas_departamentos(id);
  $("#modal_buscar_departamento").modal('hide');
}

const obtener_departamentos = buscar => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_departamentos`;
    consulta_ajax(url, { buscar }, (resp) => {
      resolve(resp);
    });
  });
}
const obtener_programas_departamento = id => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_programas_departamento`;
    consulta_ajax(url, { id }, (resp) => {
      resolve(resp);
    });
  });
}

const listar_departamentos = async () => {
  let departamentos = await obtener_departamentos(1);
  pintar_datos_combo(departamentos, ".cbxdepartamento", "Seleccione Departamento")
}
const listar_programas_departamentos = async (id, value = '') => {
  let programas = await obtener_programas_departamento(id);
  pintar_datos_combo(programas, ".cbxprograma", "Seleccione Programas", value)
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '', ) => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(elemento => {
    $(combo).append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
  });
  $(combo).val(sele);
}

const mostrar_detalle_comite = data => {
  let { id, id_estado_comite, antiguo } = data;
  id_comite = id;
  if (antiguo == '1') {
    $('#departamento_filtro').hide();
    $('#id_programa').hide();
    $('#nombre_grupo').hide();
    $('#tipo_recurso').hide();
    $('#estado_proyecto').hide();
  } else {
    $('#departamento_filtro').show();
    $('#id_programa').show();
    $('#nombre_grupo').show();
    $('#tipo_recurso').show();
    $('#estado_proyecto').show();
  }
  if (id_estado_comite == 'Com_Ini') $("#agregar_proyecto").show('fast');
  else $("#agregar_proyecto").hide('fast');
  // listar_comentarios_comite(id);
  listar_proyectos(id);
  $('#modal_detalle_solicitud').modal();
}

const listar_proyectos_usuario = (id = null) => {
  let tabla = '#tabla_proyectos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.enviar')
    .off('click', '.banco')
    .off('click', '.reanudar')
    .off('click', '.aceptar')
    .off('click', '.devolver')
    .off('click', '.comite')
    .off('click', '.quitar')
    .off('click', '.rechazar')
    .off('click', '.cancelar')
    .off('click', '.solicitud_correccion')
    .off('click', '.ver_solicitudes')
    .off('click', '.terminar_correccion');
  let tipo = $('#tipo_proyecto_filtro').val();
  let estado = $('#estado_proyecto_filtro').val();
  let codigo_proyecto = $('#codigo_proyecto_filtro').val();
  let mensaje_filtro = (id || tipo || estado || codigo_proyecto) ? true : false;
  consulta_ajax(`${ruta}listar_proyectos_usuario`, { id, tipo, estado, codigo_proyecto }, (resp) => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 15,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'nombre_proyecto' },
        { 'data': 'nombre_completo' },
        { 'data': 'nombre_tipo_proyecto' },
        { 'data': 'estado_proyecto' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      iva = datos.iva;
      no_beneficiarios = datos.no_beneficiarios;
      ver_detalle_proyecto(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      iva = datos.iva;
      no_beneficiarios = datos.no_beneficiarios;
      ver_detalle_proyecto(datos);
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      iva = datos.iva;
      no_beneficiarios = datos.no_beneficiarios;
      cargar_proyecto(datos);
      $('#modal_menu_proyecto').modal('show');
    });

    $(`${tabla} tbody`).on('click', '.enviar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Enviar a Revisión', '¿Estás seguro que deseas enviar el proyecto a revisión?', () => cambiar_estado_proyecto_usuario(id, 'Proy_Rev'));
    });

    $(`${tabla} tbody`).on('click', '.banco', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Enviar a Banco de Proyectos', '¿Estás seguro que deseas enviar el proyecto a banco de proyectos?', () => cambiar_estado_proyecto_usuario(id, 'Proy_Ban'));
    });

    $(`${tabla} tbody`).on('click', '.reanudar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Retomar Proyecto', '¿Estás seguro que deseas retomar el proyecto que está en banco de proyecto?', () => cambiar_estado_proyecto_usuario(id, 'Proy_For'));
    });

    $(`${tabla} tbody`).on('click', '.aceptar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Aceptar Proyecto', '¿Estás seguro que deseas aceptar el proyecto?', () => cambiar_estado_proyecto_usuario(id, 'Proy_Acp'));
    });

    $(`${tabla} tbody`).on('click', '.devolver', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_input('Devolver Proyecto', 'Observaciones', (mensaje) => {
        cambiar_estado_proyecto_usuario(id, 'Proy_For', mensaje);
        swal.close();
      });
    });

    $(`${tabla} tbody`).on('click', '.comite', function () {
      let { id } = myTable.row($(this).parent()).data();
      id_proyecto = id;
      $('#cbx_lista_comite').val('').change();
      $('#modal_lista_comite').modal();
    });

    $(`${tabla} tbody`).on('click', '.quitar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Quitar de Comité', '¿Estás seguro que deseas quitar de comité el proyecto?', () => cambiar_estado_proyecto_usuario(id, 'Proy_Acp'));
    });

    $(`${tabla} tbody`).on('click', '.rechazar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_input('Rechazar el Proyecto', 'Observaciones', (mensaje) => { 
        cambiar_estado_proyecto_usuario(id, 'Proy_Rec', mensaje);
        swal.close();
      });
    });

    $(`${tabla} tbody`).on('click', '.cancelar', function () {
      let { id } = myTable.row($(this).parent()).data();
      mensaje_confirmar('Cancelar el Proyecto', '¿Estás seguro que deseas cancelar el proyecto seleccionado?', () => cambiar_estado_proyecto_usuario(id, 'Proy_Can'));
    });

    $(`${tabla} tbody`).on('click', '.solicitud_correccion', function () {
      let { id, nombre_completo, nombre_proyecto } = myTable.row($(this).parent()).data();
      id_proyecto = id;
      consulta_ajax(`${ruta}listar_items_motivos`, { id_proyecto }, resp => {
        motivos_solicitud = resp;
        listar_items_motivos(motivos_solicitud);
        data_correo.nombre_persona = nombre_completo;
        data_correo.nombre_proyecto = nombre_proyecto;
      });
      tipo_solicitud = 1;
      $('#modal_solicitudes_modificar').modal();
    });

    $(`${tabla} tbody`).on('click', '.ver_solicitudes', async function () {
      let { id, nombre_completo, nombre_proyecto } = myTable.row($(this).parent()).data();
      id_proyecto = id;
      motivos_solicitud = await listar_motivos_solicitud();
      listar_items_motivos(motivos_solicitud);
      tipo_solicitud = 2;
      data_correo.nombre_persona = nombre_completo;
      data_correo.nombre_proyecto = nombre_proyecto;
      $('#modal_solicitudes_modificar').modal();
    });
  });

  if (mensaje_filtro) $('.mensaje-filtro').show('fast');
  else $('.mensaje-filtro').hide('fast');
};

const ver_detalle_proyecto = (datos) => {
  let {
    id,
    nombre_proyecto,
    nombre_tipo_proyecto,
    tipo_proyecto,
    id_aux_tipo_proyecto,
    tipo_recurso_name,
    fecha_inicial,
    fecha_final,
    no_beneficiarios,
    resumen,
    justificacion,
    planteamiento_problema,
    marco_teorico,
    estado_arte,
    diseno_metodologico,
    resultados_esperados,
    estado_proyecto,
    id_estado_proyecto,
    codigo_convenio,
    proceedings,
    verificado_por,
    codigo_orden_sap,
    descripcion_orden_sap,
    centro_costo,
    departamento_centro_costo,
    departamento,
    programa,
    grupo,
    investigador_name,
    efectivo_con2,
    especie_con,
    externo_con,
    observaciones,
    total_con2,
    tipo_especie,
    tipo_efectivo,
    tipo_externo,
    adjunto,
    aprobados,
    negados
  } = datos;
  const vacio = () => ($("<p class='text-muted text-center'>Vacío</p>"));
  id_proyecto = id;
  id_tipo_proyecto = tipo_proyecto;
  id_aux_proyecto = id_aux_tipo_proyecto;
  listar_estados_proyecto(id);
  $('#container_tabla_estados_proyectos').show();
  if (id_estado_proyecto == 'Proy_Reg' || id_estado_proyecto == 'Proy_Apr' || id_estado_proyecto == 'Proy_Neg') {
    pintar_comentarios_generales(id, '#panel_comentarios_generales', 'Comentarios a este proyecto', 'proyectos');
    $('#container_comentarios_generales').show();
    $('#aprobados_negados_proyecto').show();
  } else {
    $('#container_comentarios_generales').hide();
    $('#aprobados_negados_proyecto').hide();
  }
  $('#detalle_nombre_proyecto').html(nombre_proyecto || vacio());
  $(".num_aprobados").html(aprobados);
  $(".num_negados").html(negados);

  if (adjunto) {
    $('.proyectos_antiguos').show();
    $('.proyectos_nuevos').hide();
    $('#nav_proyectos_detalle').hide();

    $('.nombre_departamento').html(departamento);
    $('.nombre_programa').html(programa);
    $('.nombre_grupo').html(grupo);
    $('.tipo_proyecto').html(nombre_tipo_proyecto);
    $('.tipo_recurso').html(tipo_recurso_name);
    $('.nombre_proyecto').html(nombre_proyecto);
    $('.investigador').html(investigador_name);
    $('.efectivo').html(efectivo_con2);
    $('.especie').html(especie_con);
    $('.externo').html(externo_con);
    $('.total').html(total_con2);
    $('.tipo_efectivo').html(tipo_efectivo);
    $('.tipo_especie').html(tipo_especie);
    $('.tipo_externo').html(tipo_externo);
    $('.observaciones').html(observaciones || vacio());
    $('.estado_proyecto').html(estado_proyecto);
    $('.adjunto').html((adjunto != null) ? `<a target='_blank' href='${Traer_Server()}${ruta_adjunto}${adjunto}'><span class='fa fa-eye red'></span> Ver Adjunto <a>` : `<span><span class='fa fa-eye-slash red'></span> No hay adjunto.<span>`);
  } else {
    $('.proyectos_antiguos').hide();
    $('.proyectos_nuevos').show();
    $('#nav_proyectos_detalle').show();

    $('#btn_descargar_proyecto').attr('href', `${Traer_Server()}index.php/descargar_proyecto_index/${id_proyecto}`);
    $('#detalle_tipo_proyecto').html(nombre_tipo_proyecto);
    $('#detalle_tipo_recurso').html(tipo_recurso_name || vacio());
    $('#detalle_fecha_inicial').html(fecha_inicial || vacio());
    $('#detalle_fecha_final').html(fecha_final || vacio());
    $('#detalle_no_beneficiarios').html(no_beneficiarios || vacio());
    $('#detalle_estado_proyecto').html(estado_proyecto);
    $('#detalle_resumen').html(resumen || vacio());
    $('#detalle_justificacion').html(justificacion || vacio());
    $('#detalle_planteamiento_problema').html(planteamiento_problema || vacio());
    $('#detalle_marco_teorico').html(marco_teorico || vacio());
    $('#detalle_estado_arte').html(estado_arte || vacio());
    $('#detalle_diseno_metodologico').html(diseno_metodologico || vacio());
    $('#detalle_resultados_esperados').html(resultados_esperados || vacio());
    if (codigo_convenio != null) {
      $('#detalle_operacionaliza').html('Sí');
      $('#detalle_codigo_convenio').html(codigo_convenio);
    } else {
      $('#detalle_operacionaliza').html('No');
      $('#detalle_codigo_convenio').html(vacio());
    }
    $('#detalle_proceedings').html(proceedings || vacio());
    $('#detalle_verificado_por').html(verificado_por || vacio());
    $('#detalle_codigo_sap').html(codigo_orden_sap || vacio());
    $('#detalle_descripcion_codigo_sap').html(descripcion_orden_sap || vacio());
    $('#detalle_centro_costo').html(centro_costo || vacio());
    $('#detalle_departamento_centro_costo').html(departamento_centro_costo || vacio());
  
    $('#ver_detalle_participantes').off('click').click(() => {
      listar_participantes(id_proyecto);
      $('#modal_participantes').modal();
    });
    $('#ver_detalle_lugares').off('click').click(() => {
      listar_lugares(id_proyecto);
      $('#modal_lugares').modal();
    });
    $('#ver_detalle_instituciones').off('click').click(() => {
      listar_instituciones(id_proyecto);
      $('#modal_institucion').modal();
    });
    $('#ver_detalle_programas').off('click').click(() => {
      listar_programas(id_proyecto);
      $('#modal_programas').modal();
    });
    $('#ver_detalle_asignaturas').off('click').click(() => {
      listar_asignaturas(id_proyecto);
      $('#modal_asignaturas').modal();
    });
    $('#ver_detalle_sublineas').off('click').click(() => {
      listar_sublineas(id_proyecto);
      $('#modal_sublineas_investigacion').modal();
    });
    $('#ver_detalle_ods').off('click').click(() => {
      listar_ods(id_proyecto);
      cargar_listado_ods();
      $('#modal_ods').modal();
    });
    $('#ver_detalle_objetivos').off('click').click(() => {
      listar_objetivos(id_proyecto);
      $('#modal_objetivos').modal();
    });
    $('#ver_detalle_impactos').off('click').click(() => {
      listar_impactos(id_proyecto);
      $('#modal_impactos').modal();
    });
    $('#ver_detalle_productos').off('click').click(() => {
      listar_productos(id_proyecto);
      tipo_mensaje_responsables = 1;
      $('#modal_productos').modal();
    });
    $('#ver_detalle_cronogramas').off('click').click(() => {
      listar_cronogramas(id_proyecto);
      tipo_mensaje_responsables = 2;
      $('#modal_cronograma').modal();
    });
    $('#ver_detalle_presupuestos').off('click').click(() => {
      listar_resumen_presupuestos(id_proyecto);
      listar_presupuesto_discriminado_entidad(id_proyecto);
      listar_presupuesto_discriminado_entidad_rubro(id_proyecto);
      listar_presupuesto_financiacion(id_proyecto);
      $('#modal_resumen_presupuesto').modal();
    });
    $('#ver_detalle_soportes').off('click').click(() => {
      listar_soportes(id_proyecto);
      $('#modal_agregar_soportes').modal();
    });
    $('#ver_detalle_bibliografias').off('click').click(() => {
      listar_bibliografias(id_proyecto);
      $('#modal_bibliografia').modal();
    });
    $('#ver_detalle_correcciones').off('click').click(() => {
      listar_cambios(id_proyecto);
      $('#modal_correcciones').modal();
    });

    cambiar_informacion_tipo_proyecto(tipo_proyecto, id_estado_proyecto, id_aux_tipo_proyecto);
  }

  $('#modal_detalle_proyecto').modal('show');
}

const traer_convenio_proceedings = id_proyecto => {
  consulta_ajax(`${ruta}traer_datos_convenio_proceedings`, { id_proyecto }, resp => {
    let {
      codigo_convenio,
      proceedings,
      verificado_por,
      codigo_orden_sap
    } = resp;
    if (codigo_orden_sap != null) {
      if (codigo_convenio != null) {
        $('#si_operacionaliza').prop('checked', true).click();
        $('#codigo_convenio').val(codigo_convenio);
      } else {
        $('#no_operacionaliza').prop('checked', true).click();
      }
      proceedings == 'Sí' ? $('#si_proceedings').prop('checked', true) : $('#no_proceedings').prop('checked', true);
      $('#verificado_por').val(verificado_por);
      $('#txt_nombre_orden_sap').val(codigo_orden_sap);
    }
  });
  $('#modal_agregar_preguntas_convenio_proceedings').modal('show');
}

const listar_participantes = async id_proyecto => {
  let tabla = '#tabla_participantes';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  let participantes = await traer_participantes(id_proyecto);
  const myTable = $(`${tabla}`).DataTable({
    'destroy': true,
    'data': participantes,
    'pageLength': 8,
    'columns': [
      { 'data': 'ver' },
      { 'data': 'nombre_completo' },
      { 'data': 'tipo_participante' },
      { 'data': 'institucion' },
      { 'data': 'acciones' }
    ],
    'language': get_idioma(),
    dom: 'Bfrtip',
    'buttons': [get_botones()]
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass('warning');
    $(this).attr('class', 'warning');
  });

  $(`${tabla} tbody`).on('dblclick', 'tr', function () {
    let datos = myTable.row(this).data();
    ver_detalle_participante(datos);
  });

  $(`${tabla} tbody`).on('click', '.ver', function () {
    let datos = myTable.row($(this).parent()).data();
    ver_detalle_participante(datos);
  });

  $(`${tabla} tbody`).on('click', '.modificar', function () {
    let datos = myTable.row($(this).parent()).data();
    cargar_participante(datos);
    let formulario = 'form_agregar_participante';
    $(`#${formulario}`).off();
    $(`#${formulario}`).submit((e) => {
      e.preventDefault();
      modificar_item(formulario, 'participante', datos.id);
    });
  });

  $(`${tabla} tbody`).on('click', '.eliminar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_confirmar('Eliminar Participante', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('participante', datos.id));
  });
}

const ver_detalle_participante = async datos => {
  let {
    id_persona,
    identificacion,
    nombre_completo,
    institucion,
    tipo_tabla
  } = datos;
  let participante = await traer_informacion_participante(id_persona, tipo_tabla);
  let { departamento, programa, escalafon, grupo } = participante;
  $('#participante_identificacion').html(identificacion);
  $('#participante_nombre_completo').html(nombre_completo);
  $('#participante_institucion').html(institucion);
  const no_es_docente = () => ($('<span class="badge" style="font-weight: 400;">No es docente</span>'));
  $('#participante_departamento').html(departamento || no_es_docente());
  $('#participante_programa').html(programa || no_es_docente());
  $('#participante_escalafon').html(escalafon || no_es_docente());
  $('#participante_grupo').html(grupo || no_es_docente());

  $('#modal_ver_participante').modal('show');
}

const cargar_mensaje_participante = (input) => {
  let mensaje_participantes = (responsables.length == 0) ? 'Ningún Participante asignado' : `${responsables.length} Participantes asignados`;
  if (responsables.length == 1) mensaje_participantes = mensaje_participantes.replace('Participantes asignados', 'Participante asignado');
  $(input).val(mensaje_participantes);
}

const traer_participantes = id_proyecto => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_participantes`, { id_proyecto }, resp => {
      resolve(resp);
    });
  });
}

const traer_entidades = tipo => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_instituciones_bdd`, { tipo }, resp => {
      resolve(resp);
    });
  });
}

const listar_instituciones_bdd = () => {
  let tabla = '#tabla_instituciones_bdd';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_instituciones_bdd`, {}, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 5,
      'columns': [
        { 'data': 'nombre' },
        { 'data': 'nit' },
        { 'data': 'pais_origen' },
        { 'data': 'correo' },
        { 'data': 'telefono_contacto' },
        { 'data': 'nombre_contacto' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': []
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_institucion_bdd(datos);
      let formulario = 'form_agregar_institucion_bdd';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'institucion_bdd', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Institución', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('institucion_bdd', datos.id));
    });

  });
}

const listar_responsables = async (id_proyecto, tipo) => {
  let tabla = '#tabla_responsables';
  $(`${tabla} tbody`).off('click', '.agregar');
  let participantes = await traer_participantes(id_proyecto);
  const myTable = $(`${tabla}`).DataTable({
    'destroy': true,
    'data': participantes,
    'pageLength': 8,
    'columns': [
      { 'data': 'nombre_completo' },
      { 'data': 'identificacion' },
      {
        'defaultContent': '<span style="color: gray; width: 42px; height: 30px;" id="unchecked" title="Seleccionar Participante" data-toggle="popover" data-trigger="hover" class="btn btn-default postulante agregar fa fa-check"></span>',
        'createdCell': function (td, cellData, rowData, row, col) {
          let data = responsables.find((e) => e == rowData.id);
          if (data) {
            $(td).children().attr('id', 'checked').attr('title', 'Quitar Participante').css('width', '').css('height', '').css('color', '#39B23B');
          }
          (tipo == 1) ? cargar_mensaje_participante('#txt_numero_participantes_producto') : cargar_mensaje_participante('#txt_numero_participantes_cronograma');
        }
      }
    ],
    'language': get_idioma(),
    dom: 'Bfrtip',
    'buttons': []
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass('warning');
    $(this).attr('class', 'warning');
  });

  $(`${tabla} tbody`).on('click', '.agregar', function () {
    let datos = myTable.row($(this).parent()).data();
    if ($(this).attr('id') == 'checked') {
      $(this).attr('id', 'unchecked').attr('title', 'Seleccionar Participante').css('width', '42px').css('height', '30px').css('color', 'gray');
      responsables = responsables.filter(function (item) {
        if (item != datos.id) return item;
      });
    } else {
      $(this).attr('id', 'checked').attr('title', 'Quitar Participante').css('width', '').css('height', '').css('color', '#39B23B');
      responsables.push(datos.id);
    }
    (tipo == 1) ? cargar_mensaje_participante('#txt_numero_participantes_producto') : cargar_mensaje_participante('#txt_numero_participantes_cronograma');
  });
}

const listar_lugares = id_proyecto => {
  let tabla = '#tabla_lugares';
  $(`${tabla} tbody`)
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_lugares`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 5,
      'columns': [
        { 'data': 'pais' },
        { 'data': 'ciudad' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_lugar(datos);
      let formulario = 'form_agregar_lugar';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'lugar', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Lugar', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('lugar', datos.id));
    });

  });
}

const listar_instituciones = id_proyecto => {
  let tabla = '#tabla_instituciones';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_instituciones`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'nombre_institucion' },
        { 'data': 'persona_contacto' },
        { 'data': 'correo' },
        { 'data': 'telefonos' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_institucion(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_institucion(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_institucion(datos);
      let formulario = 'form_agregar_institucion';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'institucion', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Institución', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('institucion', datos.id));
    });
  });
}

const ver_detalle_institucion = (datos) => {
  let {
    nombre_institucion,
    persona_contacto,
    correo,
    telefonos,
    responsabilidad_contraparte,
    responsabilidad_cuc
  } = datos;
  $('#detalle_nombre_institucion').html(nombre_institucion);
  $('#detalle_persona_contacto_institucion').html(persona_contacto);
  $('#detalle_correo_institucion').html(correo);
  $('#detalle_telefonos_institucion').html(telefonos);
  $('#detalle_responsabilidad_contraparte_institucion').html(responsabilidad_contraparte);
  $('#detalle_responsabilidad_cuc_institucion').html(responsabilidad_cuc);

  $('#modal_ver_institucion').modal('show');
}

const listar_programas = id_proyecto => {
  let tabla = '#tabla_programas';
  $(`${tabla} tbody`)
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_programas`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'programa' },
        { 'data': 'tipo_interaccion' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_programa(datos);
      let formulario = 'form_agregar_programa';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'programa', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Programa', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('programa', datos.id));
    });
  });
}

const listar_asignaturas = id_proyecto => {
  let tabla = '#tabla_asignaturas';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_asignaturas`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        {
          'data': 'asignatura',
          render: function (data, type, row) {
            let n = 60;
            return data.length > n ? `${data.substr(0, n - 1)} ...` : `${data}`;
          }
        },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_asignatura(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_asignatura(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_asignatura(datos);
      let formulario = 'form_agregar_asignatura';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'asignatura', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Asignatura/Proyecto', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('asignatura', datos.id));
    });
  });
}

const ver_detalle_asignatura = (datos) => {
  let {
    asignatura
  } = datos;
  let icon = $('<span></span>').addClass('glyphicon glyphicon-th-list');
  $('#ver_titulo').html(icon).append('Asignatura/Proyecto');
  $('#ver_descripcion').html(asignatura);

  $('#modal_ver').modal('show');
}

const listar_sublineas = id_proyecto => {
  let tabla = '#tabla_sublineas_investigacion';
  $(`${tabla} tbody`)
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_sublineas`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 5,
      'columns': [
        { 'data': 'grupo' },
        { 'data': 'linea' },
        { 'data': 'sub_linea' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_sublinea(datos);
      let formulario = 'form_agregar_sublinea';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'sublinea', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Sub-línea', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('sublinea', datos.id));
    });

  });
}

const listar_ods = id_proyecto => {
  let tabla = '#tabla_ods';
  $(`${tabla} tbody`)
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_ods`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 5,
      'columns': [
        {
          'data': 'ods', 'createdCell': function (td, cellData, rowData, row, col) {
            $(td).attr('title', rowData.ods_completo).css('width', '80%');
          }
        },
        { 'data': 'acciones', 'createdCell': (td) => $(td).css('width', '20%') }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_ods(datos);
      let formulario = 'form_agregar_ods';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'ods', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar ODS', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('ods', datos.id));
    });

  });
}

const listar_objetivos = async id_proyecto => {
  let tabla = '#tabla_objetivos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  let resp = await traer_objetivos(id_proyecto);
  let count = 1;
  const myTable = $(`${tabla}`).DataTable({
    'destroy': true,
    'data': resp,
    'pageLength': 10,
    'columns': [
      { 'data': 'ver' },
      {
        'data': 'tipo_objetivo',
        render: function (data, type, row) {
          return data == 'Específico' ? `${count++}. ${data}` : data;
        }
      },
      {
        'data': 'descripcion',
        render: function (data, type, row) {
          let n = 50;
          return data.length > n ? `${data.substr(0, n - 1)} ...` : data;
        }
      },
      { 'data': 'acciones' }
    ],
    'language': get_idioma(),
    dom: 'Bfrtip',
    'buttons': [get_botones()]
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass('warning');
    $(this).attr('class', 'warning');
  });

  $(`${tabla} tbody`).on('dblclick', 'tr', function () {
    let datos = myTable.row(this).data();
    ver_detalle_objetivo(datos);
  });

  $(`${tabla} tbody`).on('click', '.ver', function () {
    let datos = myTable.row($(this).parent()).data();
    ver_detalle_objetivo(datos)
  });

  $(`${tabla} tbody`).on('click', '.modificar', function () {
    let datos = myTable.row($(this).parent()).data();
    cargar_objetivo(datos);
    let formulario = 'form_agregar_objetivo';
    $(`#${formulario}`).off();
    $(`#${formulario}`).submit((e) => {
      e.preventDefault();
      modificar_item(formulario, 'objetivo', datos.id);
    });
  });

  $(`${tabla} tbody`).on('click', '.eliminar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_confirmar('Eliminar Objetivo', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('objetivo', datos.id));
  });

}

const ver_detalle_objetivo = (datos) => {
  let {
    tipo_objetivo,
    descripcion
  } = datos;
  let icon = $('<span></span>').addClass('glyphicon glyphicon-th-list');
  $('#ver_titulo').html(icon).append(` Objetivo ${tipo_objetivo}`);
  $('#ver_descripcion').html(descripcion);

  $('#modal_ver').modal('show');
}

const listar_impactos = id_proyecto => {
  let tabla = '#tabla_impactos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_impactos`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'tipo_impacto' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_impacto(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_impacto(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_impacto(datos);
      let formulario = 'form_agregar_impacto';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'impacto', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Impacto y/o Efecto Esperado', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('impacto', datos.id));
    });

  });
}

const ver_detalle_impacto = (datos) => {
  let {
    tipo_impacto,
    descripcion
  } = datos;
  let icon = $('<span></span>').addClass('fa fa-line-chart');
  $('#ver_titulo').html(icon).append(` Impacto y/o Efecto ${tipo_impacto} Esperado`);
  $('#ver_descripcion').html(descripcion);

  $('#modal_ver').modal('show');
}

const listar_productos = id_proyecto => {
  let tabla = '#tabla_productos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_productos`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'tipo_producto' },
        { 'data': 'producto' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_producto(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_producto(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', async function () {
      let datos = myTable.row($(this).parent()).data();
      await cargar_producto(datos);
      let formulario = 'form_agregar_producto';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        if (responsables.length == 0) {
          MensajeConClase('No has asignado ningún participante al producto', 'info', 'Oops!');
          return false;
        }
        modificar_item(formulario, 'producto', datos.id);
      });
      cargar_mensaje_participante('#txt_numero_participantes_producto');
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Producto Esperado', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('producto', datos.id));
    });

  });
}

const ver_detalle_producto = (datos) => {
  let {
    tipo_producto,
    producto,
    observaciones,
    participantes
  } = datos;
  $('#ver_tipo_producto').html(tipo_producto);
  $('#ver_producto').html(producto);
  $('#ver_observaciones').html(observaciones);

  $('#tabla_responsables_producto').DataTable({
    'destroy': true,
    'data': participantes,
    'pageLength': 10,
    'columns': [
      { 'data': 'identificacion' },
      { 'data': 'nombre_completo' }
    ],
    'language': get_idioma(),
    dom: 'Bfrtip',
    'buttons': []
  });

  $('#modal_ver_producto').modal('show');
}

const listar_cronogramas = id_proyecto => {
  let tabla = '#tabla_cronogramas';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_cronogramas`, { id_proyecto }, resp => {
    let count = 1;
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        {
          'data': 'actividad',
          render: function (data, type, row) {
            let n = 60;
            return data.length > n ? `${count++}. ${data.substr(0, n - 1)} ...` : `${count++}. ${data}`;
          }
        },
        { 'data': 'fecha_inicial' },
        { 'data': 'fecha_final' },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_cronograma(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_cronograma(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', async function () {
      let datos = myTable.row($(this).parent()).data();
      await cargar_cronograma(datos);
      let formulario = 'form_agregar_cronograma';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        if (responsables.length == 0) {
          MensajeConClase('No has asignado ningún participante al cronograma', 'info', 'Oops!');
          return false;
        }
        modificar_item(formulario, 'cronograma', datos.id);
      });
      cargar_mensaje_participante('#txt_numero_participantes_cronograma');
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Cronograma', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('cronograma', datos.id));
    });

  });
}

const ver_detalle_cronograma = (datos) => {
  let {
    obj_especifico,
    fecha_inicial,
    fecha_final,
    actividad,
    participantes
  } = datos;
  $('#ver_objetivo_especifico').html(obj_especifico);
  $('#ver_fecha_inicial').html(fecha_inicial);
  $('#ver_fecha_final').html(fecha_final);
  $('#ver_actividad').html(actividad);

  $('#tabla_responsables_cronograma').DataTable({
    'destroy': true,
    'data': participantes,
    'pageLength': 10,
    'columns': [
      { 'data': 'identificacion' },
      { 'data': 'nombre_completo' }
    ],
    'language': get_idioma(),
    dom: 'Bfrtip',
    'buttons': []
  });

  $('#modal_ver_cronograma').modal('show');
}

const listar_resumen_presupuestos = (id_proyecto) => {
  let tabla = '#tabla_resumen_presupuestos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.agregar');
  consulta_ajax(`${ruta}listar_resumen_presupuestos`, { id_proyecto, id_tipo_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 8,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'rubro' },
        { 'data': 'efectivo' },
        { 'data': 'especie' },
        { 'data': 'acciones' }
      ],
      'footerCallback': async function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_efectivo = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_especie = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_presupuesto = parseInt(total_efectivo) + parseInt(total_especie);
        const total_iva = (total_presupuesto * iva) / 100;
        const total_valor_iva = total_presupuesto + total_iva;
        const costo_total_beneficiario = total_valor_iva / no_beneficiarios;

        $(api.column(2).footer()).html(`$ ${parseInt(total_efectivo).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total_especie).toLocaleString('es')}`);
        $('#total_presupuesto').html(`$ ${total_presupuesto.toLocaleString('es')}`);
        $('#titulo_total_iva').html(`Total IVA (${iva}%)`);
        $('#total_iva').html(`$ ${total_iva.toLocaleString('es')}`);
        $('#total_presupuesto_iva').html(`$ ${total_valor_iva.toLocaleString('es')}`);
        $('#costo_total_beneficiario').html(no_beneficiarios == 0 ? 'Por favor agregar el No. de Beneficiarios' : `$ ${costo_total_beneficiario.toLocaleString('es')}`);
      },
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      $('#titulo_tipo_presupuesto').html(datos.rubro.capitalize(true));
      $('#ver_tipo_presupuesto').val(datos.id);
      listar_presupuestos(id_proyecto, datos.id);
      $('#modal_presupuesto').modal('show');
    });

    $(`${tabla} tbody`).on('click', '.agregar', function () {
      let datos = myTable.row($(this).parent()).data();
      let tipo_presupuesto = datos.id;
      if (tipo_presupuesto != '') {
        let form = '#form_agregar_presupuesto';
        funcion_agregar(form, 'titulo_presupuesto', 'Agregar Presupuesto', tipo_presupuesto);
        cambiar_form_presupuesto(tipo_presupuesto);
        $('#tipo_presupuesto').val(tipo_presupuesto);
        $('#modal_agregar_presupuesto').modal('show');
      } else {
        MensajeConClase('Por favor seleccionar un tipo de presupuesto', 'info', 'Oops!');
      }
    });

  });
}

const listar_presupuestos = (id_proyecto, id_presupuesto) => {
  let tabla = '#tabla_presupuestos';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_presupuestos`, { id_proyecto, id_presupuesto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 8,
      'columns': [
        { 'data': 'ver' },
        { 'data': 'tipo_valor' },
        { 'data': 'valor_unitario_convertido' },
        { 'data': 'valor_total' },
        { 'data': 'acciones' }
      ],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_valor_unitario = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_valor_total = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        $(api.column(2).footer()).html(`$ ${parseInt(total_valor_unitario).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total_valor_total).toLocaleString('es')}`);
      },
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': []
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_presupuestos(datos.id, datos.tipo_presupuesto, datos.valor_unitario_convertido);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_presupuestos(datos.id, datos.tipo_presupuesto, datos.valor_unitario_convertido);
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_presupuesto(datos.id);
      let formulario = 'form_modificar_presupuesto';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'presupuesto', datos.id, datos.id_tipo_presupuesto);
      });

    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Presupuesto', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('presupuesto', datos.id, datos.id_tipo_presupuesto));
    });

  });
}

const ver_detalle_presupuestos = async (id_presupuesto, tipo_presupuesto, valor_unitario_convertido) => {
  let datos = await traer_presupuesto(id_proyecto, id_presupuesto);
  $('#detalle_tipo_presupuesto').html(tipo_presupuesto);
  let tbody = $('#datos_presupuestos').html('');
  datos.forEach(elemento => {
    let { id_aux_dato, nombre_dato, valor, valor_select, tipo_dato } = elemento;
    let td_titulo = $('<td></td>').addClass('ttitulo').attr('colspan', 2).html(nombre_dato);
    let td_dato = $('<td></td>').attr('colspan', 4);
    if (tipo_dato == 'Select') {
      td_dato.html(valor_select);
    } else {
      td_dato.html((id_aux_dato == 'Pre_Val_Uni') ? valor_unitario_convertido : valor);
    }
    let tr = $('<tr></tr>').append(td_titulo, td_dato);
    tbody.append(tr);
  });

  $('#modal_ver_presupuesto').modal('show');
}

const listar_presupuesto_discriminado_entidad = (id_proyecto) => {
  let tabla = '#tabla_presupuesto_discriminado_entidad';
  consulta_ajax(`${ruta}listar_presupuesto_discriminado_entidad`, { id_proyecto }, resp => {
    $(tabla).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      pageLength: 5,
      data: resp,
      columns: [
        { data: 'entidad_responsable' },
        { data: 'efectivo' },
        { data: 'especie' },
        { data: 'total' },
        { data: 'porcentaje' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: [],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_efectivo = api.column(1).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_especie = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        $(api.column(1).footer()).html(`$ ${parseInt(total_efectivo).toLocaleString('es')}`);
        $(api.column(2).footer()).html(`$ ${parseInt(total_especie).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total).toLocaleString('es')}`);
      }
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });
  });
}

const listar_presupuesto_discriminado_entidad_rubro = (id_proyecto) => {
  let tabla = '#tabla_presupuesto_discriminado_entidad_rubro';
  consulta_ajax(`${ruta}listar_presupuesto_discriminado_entidad_rubro`, { id_proyecto }, resp => {
    $(tabla).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      pageLength: 5,
      data: resp,
      columns: [
        { data: 'entidad_responsable' },
        { data: 'rubro' },
        { data: 'efectivo' },
        { data: 'especie' },
        { data: 'total' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: [],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_efectivo = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_especie = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total = api.column(4).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        $(api.column(2).footer()).html(`$ ${parseInt(total_efectivo).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total_especie).toLocaleString('es')}`);
        $(api.column(4).footer()).html(`$ ${parseInt(total).toLocaleString('es')}`);
      }
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });
  });
}

const listar_presupuesto_financiacion = (id_proyecto) => {
  if (id_aux_proyecto != 'Pro_Int') { return };

  let tabla_nacional = '#tabla_presupuesto_financiacion_nacional';
  let tabla_internacional = '#tabla_presupuesto_financiacion_internacional';

  consulta_ajax(`${ruta}listar_presupuesto_discriminado_financiacion`, { id_proyecto }, resp => {
    $(tabla_nacional).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      pageLength: 5,
      data: resp.financiacion_nacional,
      columns: [
        { data: 'financiacion' },
        { data: 'efectivo' },
        { data: 'especie' },
        { data: 'total' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: [],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_efectivo = api.column(1).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_especie = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        $(api.column(1).footer()).html(`$ ${parseInt(total_efectivo).toLocaleString('es')}`);
        $(api.column(2).footer()).html(`$ ${parseInt(total_especie).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total).toLocaleString('es')}`);
      }
    });

    $(tabla_internacional).DataTable({
      destroy: true,
      searching: false,
      processing: true,
      pageLength: 5,
      data: resp.financiacion_internacional,
      columns: [
        { data: 'financiacion' },
        { data: 'efectivo' },
        { data: 'especie' },
        { data: 'total' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: [],
      'footerCallback': function (row, data, start, end, display) {
        let api = this.api();
        api.data = data;

        let intVal = function (i) {
          return typeof i === 'string' ?
            i.replace(/[\$.]/g, '') * 1 :
            typeof i === 'number' ?
              i : 0;
        };

        const total_efectivo = api.column(1).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total_especie = api.column(2).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        const total = api.column(3).data().reduce(function (a, b) {
          return (intVal(a) + intVal(b)).toString() + '.'
        }, 0);

        $(api.column(1).footer()).html(`$ ${parseInt(total_efectivo).toLocaleString('es')}`);
        $(api.column(2).footer()).html(`$ ${parseInt(total_especie).toLocaleString('es')}`);
        $(api.column(3).footer()).html(`$ ${parseInt(total).toLocaleString('es')}`);
      }
    });

    $(`${tabla_nacional} tbody`).on('click', 'tr', function () {
      $(`${tabla_nacional} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla_internacional} tbody`).on('click', 'tr', function () {
      $(`${tabla_internacional} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });
  });
}

const listar_soportes = (id_proyecto) => {
  let tabla = '#tabla_soportes'
  $(`${tabla} tbody`).off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_soportes`, { id_proyecto }, resp => {
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
            else return `<a target='_blank' href='${Traer_Server()}${ruta_archivos_proyetos}${nombre_guardado}' style="background-color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
          }
        },
        { data: 'nombre_real' },
        { data: 'fecha_registra' },
        { data: 'nombre_completo' },
        { data: 'acciones' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Soporte', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('soporte', datos.id));
    });
  });
}

const listar_bibliografias = id_proyecto => {
  let tabla = '#tabla_bibliografia';
  $(`${tabla} tbody`)
    .off('click', '.ver')
    .off('click', '.modificar')
    .off('click', '.eliminar');
  consulta_ajax(`${ruta}listar_bibliografias`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'data': 'ver' },
        {
          'data': 'bibliografia',
          render: function (data, type, row) {
            let n = 80;
            return data.length > n ? `${data.substr(0, n - 1)} ...` : data;
          }
        },
        { 'data': 'acciones' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': [get_botones()]
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_bibliografia(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_bibliografia(datos)
    });

    $(`${tabla} tbody`).on('click', '.modificar', function () {
      let datos = myTable.row($(this).parent()).data();
      cargar_bibliografia(datos);
      let formulario = 'form_agregar_bibliografia';
      $(`#${formulario}`).off();
      $(`#${formulario}`).submit((e) => {
        e.preventDefault();
        modificar_item(formulario, 'bibliografia', datos.id);
      });
    });

    $(`${tabla} tbody`).on('click', '.eliminar', function () {
      let datos = myTable.row($(this).parent()).data();
      mensaje_confirmar('Eliminar Bibliografía', '¿Estás seguro que desea eliminar el registro seleccionado?', () => eliminar_item('bibliografia', datos.id));
    });

  });
}

const ver_detalle_bibliografia = (datos) => {
  let {
    bibliografia
  } = datos;
  let icon = $('<span></span>').addClass('glyphicon glyphicon-bookmark');
  $('#ver_titulo').html(icon).append(' Bibliografía');
  $('#ver_descripcion').html(bibliografia);

  $('#modal_ver').modal('show');
}

const listar_motivos_solicitud = (tipo = 1) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_motivos_solicitud`, { id_proyecto, tipo }, resp => {
      resolve(resp);
    });
  })
}

const listar_items_motivos = (data) => {
  let tabla = '#tabla_items_motivos';
  $(`${tabla} tbody`).off('click', '.ver')
                     .off('click', '.seleccionar')
                     .off('click', '.modificar')
                     .off('click', '.eliminar')
                     .off('click', '.aprobar')
                     .off('click', '.negar')
                     .off('click', '.cambiar');
  const myTable = $(`${tabla}`).DataTable({
    destroy: true,
    processing: true,
    data: data,
    columns: [
      {
        'render': function (data, type, row) {
          if (row.ver) {
            return row.ver;
          } else {
            return '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el motivo"><span>ver</span></span>';
          }
        }
      },
      { data: 'valor' },
      {
        'render': function (data, type, row) {
          if (row.acciones) {
            return row.acciones;
          } else {
            return `<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>
                    <span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>`;
          }
        }
      }
    ],
    language: idioma,
    dom: "Bfrtip",
    buttons: []
  });

  $(`${tabla} tbody`).on('click', 'tr', function () {
    $(`${tabla} tbody tr`).removeClass('warning');
    $(this).attr('class', 'warning');
  });

  $(`${tabla} tbody`).on('click', '.ver', function () {
    let { valor, razones, aprobado, fecha_limite } = myTable.row($(this).parent()).data();
    $('#detalle_item').html(valor);
    let tbody = $('#datos_motivo').html('');
    if (razones) {
      let tr_razones = $(`<tr><td class="ttitulo" colspan="2">Razones</td><td colspan="4">${razones}</td></tr>`);
      tbody.append(tr_razones);
      if (aprobado != null) {
        let tr_estado = $(`<tr><td class="ttitulo" colspan="2">Estado</td><td colspan="4">${aprobado == 1 ? 'Aprobado' : 'Negado'}</td></tr>`);
        tbody.append(tr_estado);
        if (aprobado == 1) {
          let tr_fecha_limite = $(`<tr><td class="ttitulo" colspan="2">Fecha Límite</td><td colspan="4">${fecha_limite}</td></tr>`);
          tbody.append(tr_fecha_limite);
        }
      }
    }

    $('#modal_detalle_motivo').modal('show');
  });

  $(`${tabla} tbody`).on('click', '.seleccionar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_input('¿Cuáles son las razones?', 'Razones', (mensaje) => {
      if (mensaje.length > 500) {
        swal.showInputError('Haz excedido el número máximo de carácteres (500)');
      } else {
        motivos_solicitud.map(motivo => {
          if (motivo.id == datos.id) {
            motivo.razones = mensaje;
            motivo.ver = '<span style="background-color: #39B23B; color: white; width: 100%" class="pointer form-control ver" title="Ver el motivo">ver</span>';
            motivo.acciones = null;
          }
        });
        $('#txt_item_buscar').val('');
        listar_items_motivos(motivos_solicitud);
        swal.close();
      }
    });
  });

  $(`${tabla} tbody`).on('click', '.modificar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_input('¿Cuáles son las razones?', 'Razones', (mensaje) => {
      if (mensaje.length > 500) {
        swal.showInputError('Haz excedido el número máximo de carácteres (500)');
      } else {
        motivos_solicitud.map((motivo) => {
          if (motivo.id == datos.id) {
            motivo.razones = mensaje;
          }
        });
        listar_items_motivos(motivos_solicitud);
        swal.close();
      }
    }, datos.razones);
  });

  $(`${tabla} tbody`).on('click', '.eliminar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_confirmar('Eliminar Motivo', '¿Estás seguro que deseas eliminar este motivo?', () => {
      motivos_solicitud.map(motivo => {
        if (motivo.id == datos.id) {
          motivo.razones = null;
          motivo.ver = null;
          motivo.acciones = '<span style="color: rgb(57, 178, 59);" title="Seleccionar Ítem" data-toggle="popover" data-trigger="hover" class="btn btn-default seleccionar fa fa-toggle-off"></span>';
        }
      })
      listar_items_motivos(motivos_solicitud);
      swal.close();
    });
  });

  $(`${tabla} tbody`).on('click', '.aprobar', function () {
    let datos = myTable.row($(this).parent()).data();
    $('#fecha_limite').val('');
    $('#modal_fecha_limite').modal();

    $('#btn_fecha_limite').off().click(() => {
      motivos_solicitud.map((e) => {
        if (e.id == datos.id) {
          e.aprobado = 1;
          e.fecha_limite = $('#fecha_limite').val() + ':00';
          e.ver = '<span style="background-color: #39B23B; color: white; width: 100%" class="pointer form-control ver" title="Ver el motivo">ver</span>';
          e.acciones = '<span class="fa fa-refresh pointer btn btn-default cambiar" title="Cambiar opción" style="color: #2E79E5; margin: 0 1px;"></span>';
        }
      });
      listar_items_motivos(motivos_solicitud);
      $('#modal_fecha_limite').modal('hide');
    });
  });

  $(`${tabla} tbody`).on('click', '.negar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_confirmar('Negar Ítem', '¿Estás seguro que deseas negar este ítem?', () => {
      motivos_solicitud.map((e) => {
        if (e.id == datos.id) {
          e.aprobado = 0;
          e.ver = '<span style="background-color: #CA3E33; color: white; width: 100%" class="pointer form-control ver" title="Ver el motivo">ver</span>';
          e.acciones = '<span class="fa fa-refresh pointer btn btn-default cambiar" title="Cambiar opción" style="color: #2E79E5; margin: 0 1px;"></span>';
        }
      });
      listar_items_motivos(motivos_solicitud);
      swal.close();
    });
  });

  $(`${tabla} tbody`).on('click', '.cambiar', function () {
    let datos = myTable.row($(this).parent()).data();
    mensaje_confirmar('Cambiar de estado', '¿Estás seguro que deseas cambiar de estado este ítem?', () => {
      motivos_solicitud.map((e) => {
        if (e.id == datos.id) {
          e.aprobado = null;
          e.fecha_limite = null;
          e.ver = null;
          e.acciones = `<span style="color: rgb(57, 178, 59);" title="Aprobar" data-toggle="popover" data-trigger="hover" class="btn btn-default aprobar fa fa-check"></span>
                        <span style="color: #CA3E33;" title="Negar" data-toggle="popover" data-trigger="hover" class="btn btn-default negar fa fa-times"></span>`;
        }
      });
      listar_items_motivos(motivos_solicitud);
      swal.close();
    });
  });
}

const listar_cambios = id_proyecto => {
  let tabla = '#tabla_cambios';
  $(`${tabla} tbody`)
    .off('click', '.ver');
  consulta_ajax(`${ruta}listar_cambios`, { id_proyecto }, resp => {
    const myTable = $(`${tabla}`).DataTable({
      'destroy': true,
      'data': resp,
      'pageLength': 10,
      'columns': [
        { 'defaultContent': '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver detalle del cambio"><span>ver</span></span>' },
        {
          'render': function (data, type, row) {
            if (row.tabla == 'comite_proyectos') {
              return row.nombre_campo;
            } else {
              return row.observaciones;
            }
          }
        },
        { 'data': 'tipo' },
        { 'data': 'fecha' }
      ],
      'language': get_idioma(),
      dom: 'Bfrtip',
      'buttons': []
    });

    $(`${tabla} tbody`).on('click', 'tr', function () {
      $(`${tabla} tbody tr`).removeClass('warning');
      $(this).attr('class', 'warning');
    });

    $(`${tabla} tbody`).on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_cambio(datos);
    });

    $(`${tabla} tbody`).on('click', '.ver', function () {
      let datos = myTable.row($(this).parent()).data();
      ver_detalle_cambio(datos)
    });
  });
}

const ver_detalle_cambio = async (datos) => {
  let {
    tabla,
    tipo,
    id_solicitud,
    nombre_campo,
    anterior,
    actual,
    observaciones
  } = datos;
  if (tipo === 'Actualizado') {
    $('#nombre_item').html(nombre_campo);
    $('#cambios_item').html(`
      <tr>
        <td colspan="2" class="ttitulo">Valor Anterior</td>
        <td colspan="4">${anterior}</td>
      </tr>
      <tr>
        <td colspan="2" class="ttitulo">Valor Actual</td>
        <td colspan="4">${actual}</td>
      </tr>
    `);
  } else {
    let datos = await traer_datos_item(tabla, id_solicitud);
    let contenido = '';
    let contenido_participantes = '<tr><td class="nombre_tabla text-left" colspan="6">Participantes</td></tr>';
    for (let [key, value] of Object.entries(datos[0])) {
      if (key == 'participantes') {
        datos[0].participantes.forEach(participante => {
          contenido_participantes += `<tr><td colspan="6">${participante.nombre_completo}</td></tr>`;
        });
      }
      if (key != 'id' && key != 'participantes') {
        if (value) {
          contenido += `
            <tr>
              <td colspan="2" class="ttitulo">${key.replace(/_/g, ' ').capitalize(true)}</td>
              <td colspan="4">${value}</td>
            </tr>
          `;
        }
      }
    }
    $('#nombre_item').html(observaciones);
    $('#cambios_item').html(contenido)
    if (tabla == 'comite_proyectos_productos' || tabla == 'comite_proyectos_cronogramas') $('#cambios_item').append(contenido_participantes);
  }

  $('#modal_detalle_correccion').modal();
}

const listar_proyectos = id => {
  id_proyecto = null;
  let id_departamento = datos_departamento ? datos_departamento.id : '';
  let id_programa = $("#id_programa").val();
  let nombre_grupo = $("#nombre_grupo").val();
  let tipo_proyecto = $("#tipo_proyecto").val();
  let tipo_recurso = $("#tipo_recurso").val();
  let estado_proyecto = $("#estado_proyecto").val();
  let codigo_proyecto = $("#codigo_proyecto_filtro_2").val();
  consulta_ajax(`${ruta}listar_proyectos`, { id, id_departamento, id_programa, nombre_grupo, tipo_proyecto, tipo_recurso, estado_proyecto, codigo_proyecto }, (resp) => {
    $(`#tabla_proyectos_comite tbody`).off('click', 'tr td:nth-of-type(1)')
                                      .off('click', 'tr')
                                      .off('dblclick', 'tr')
                                      .off('click', 'tr td .aprobar')
                                      .off('click', 'tr td .negar')
                                      .off('click', 'tr td .quitar')
                                      .off('click', 'tr td .consulta')
                                      .off('click', 'tr td .revertir')
                                      .off('click', 'tr td .aprobar2')
                                      .off('click', 'tr td .modificar')
                                      .off('click', 'tr td .codigo');
    const myTable = $("#tabla_proyectos_comite").DataTable({
      "destroy": true,
      "processing": true,
      'data': resp,
      "pageLength": 50,
      "columns": [
        {
          'data': 'ver'
        },
        {
          'data': 'nombre_proyecto'
        },
        {
          'data': 'investigador_name'
        },
        {
          'data': 'grupo'
        },
        {
          'data': 'nombre_tipo_proyecto'
        },
        {
          'data': 'tipo_recurso_name'
        },
        {
          'data': 'departamento'
        },
        {
          'data': 'programa'
        },
        {
          'render': function (data, type, row) {
            if (row.efectivo_con) {
              return row.efectivo_con;
            } else {
              return row.efectivo_con2;
            }
          }
        },
        {
          'data': 'especie_con'
        },
        {
          'render': function (data, type, row) {
            if (row.total_con) {
              return row.total_con;
            } else {
              return row.total_con2;
            }
          }
        },
        {
          'data': 'aprobados'
        },
        {
          'data': 'negados'
        },
        {
          'data': 'estado_proyecto'
        },
        {
          'data': 'accion'
        },

      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [get_botones()],
    });

    $('#tabla_proyectos_comite tbody').on('click', 'tr', function () {
      let { id } = myTable.row(this).data();
      id_proyecto = id;
      $("#tabla_proyectos_comite tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_proyectos_comite tbody').on('dblclick', 'tr', function () {
      let datos = myTable.row(this).data();
      ver_detalle_proyecto(datos);
    });

    $('#tabla_proyectos_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let datos = myTable.row(this).data();
      ver_detalle_proyecto(datos);
    });

    $('#tabla_proyectos_comite tbody').on('click', 'tr td .aprobar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_proyecto(id, 'Proy_Apr');
    });

    $('#tabla_proyectos_comite tbody').on('click', 'tr td .aprobar2', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_proyecto(id, 'Proy_Apr_2');
    });

    $('#tabla_proyectos_comite tbody').on('click', 'tr td .negar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_proyecto(id, 'Proy_Neg');
    });
    $('#tabla_proyectos_comite tbody').on('click', 'tr td .quitar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      mensaje_confirmar('Quitar de Comité', '¿Estás seguro que deseas quitar de comité el proyecto?', () => {
        cambiar_estado_proyecto_usuario(id, 'Proy_Acp');
        listar_proyectos(id_comite);
      });
    });
    $('#tabla_proyectos_comite tbody').on('click', 'tr td .consulta', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_proyecto(id, 'Proy_Con');
    });
    $('#tabla_proyectos_comite tbody').on('click', 'tr td .revertir', function () {
      let { id, gestionado } = myTable.row($(this).parent().parent()).data();
      cambiar_estado_proyecto(id, 'Proy_Rev', gestionado);
    });
    $('#tabla_proyectos_comite tbody').on('click', 'tr td .codigo', function () {
      let { id, id_tipo_proyecto } = myTable.row($(this).parent().parent()).data();
      mensaje_input('Código del proyecto', `Ejemplo: ${id_tipo_proyecto.replace('Pro_', '').toUpperCase()}.001-01-001-15`, (codigo_proyecto) => {
        consulta_ajax(`${ruta}guardar_codigo_proyecto`, { id, codigo_proyecto }, (resp) => {
          let { mensaje, tipo, titulo } = resp;
          MensajeConClase(mensaje, tipo, titulo);
          if (tipo == 'success') listar_proyectos(id_comite);
        });
      });
    });
    const ocultar_columnas = (columns, table) => columns.map((colum) => table.column(colum).visible(false))
    ocultar_columnas([3, 4, 5, 7, 9], myTable);


  });

}

const cambiar_estado_proyecto = (id, estado, gestionado) => {
  const confirm_normal = (id, estado, title, gestionado) => {
    swal({
      title,
      text: "Tener en cuenta que, al realizar esta accíon el proyecto sera habilitado para el siguiente  proceso, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
          ejecutar_gestion_proyecto(id, estado, '', gestionado);
        }
      });
  }

  const confirm_input = (id, estado, title, gestionado) => {
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
        ejecutar_gestion_proyecto(id, estado, mensaje, gestionado);
      }
    });
  }

  let datos = { 'title': 'Postulante Apto ..?', 'tipo': 3 };
  if (estado == 'Proy_Apr') datos = { 'title': 'Aprobar Proyecto ..?', 'tipo': 1 };
  else if (estado == 'Proy_Rev') datos = { 'title': 'Revertir Acción ..?', 'tipo': 1 };
  else if (estado == 'Proy_Neg') datos = { 'title': 'Negar Proyecto ..?', 'tipo': 2 };
  else if (estado == 'Proy_Can') datos = { 'title': 'Cancelar Proyecto..?', 'tipo': 2 };
  else if (estado == 'Proy_Con') datos = { 'title': 'Consultar Proyecto..?', 'tipo': 2 };
  else if (estado == 'Proy_Apr_2') { datos = { 'title': 'Aprobar Proyecto..?', 'tipo': 2 }; estado = 'Proy_Apr'; };


  let { title, tipo } = datos;
  if (tipo == 1) confirm_normal(id, estado, title, gestionado);
  else if (tipo == 2) confirm_input(id, estado, title, gestionado);
}

const ejecutar_gestion_proyecto = (id, estado, observaciones = '', gestionado) => {
  consulta_ajax(`${ruta}gestionar_proyecto`, { id, estado, observaciones, gestionado }, (resp) => {
    let { tipo, mensaje, titulo } = resp;
    if (tipo == 'success') {
      swal.close();
      listar_proyectos(id_comite);
    } else MensajeConClase(mensaje, tipo, titulo);
  });
}

const listar_estados_proyecto = id => {
  consulta_ajax(`${ruta}listar_estados_proyecto`, { id }, (resp) => {
    const myTable = $("#tabla_estados_proyecto").DataTable({
      "destroy": true,
      "order": [
        [1, "asc"]
      ],
      "processing": true,
      'data': resp,
      "columns": [
        {
          'data': 'valor'
        },
        {
          'data': 'fecha_registro'
        },
        {
          'data': 'observaciones'
        },
        {
          'data': 'persona'
        },

      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });
  });

}

const obtener_personas_index = () => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_personas_index`, '', (resp) => {
      resolve(resp);
    });
  });
}

const listar_personas_vb_index = async (persona = '') => {
  let personas = await obtener_personas_index();
  encargados_vb = personas;
  $("#cbx_personas_aprueban").html(`<option value=''> Seleccione Persona</option>`);
  personas.forEach(elemento => {
    let { id, nombre_completo } = elemento;
    $("#cbx_personas_aprueban").append(`<option value='${id}'> ${nombre_completo}</option>`);
  });
  $("#cbx_personas_aprueban").val(persona);
}

const asignar_persona_aprueba = id_persona => {
  swal({
    title: '¿ Asignar Permiso ?',
    text: "Tener en cuenta que, al realizar esta accíon sera necesario el aprobado de esta persona para cerrar un proyecto, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
        consulta_ajax(`${ruta}asignar_persona_aprueba`, { id_persona }, ({ mensaje, tipo, titulo }) => {
          if (tipo == 'success') {
            swal.close();
            listar_personas_vb_index();
            personas_aprueban_index();
          } else MensajeConClase(mensaje, tipo, titulo);

        });
      }
    });
}

const personas_aprueban_index = () => {
  $(`#tabla_personas_aprueban_index tbody`).off('click', 'tr td .eliminar').off('click', 'tr');
  consulta_ajax(`${ruta}personas_aprueban_index`, {}, (resp) => {
    const myTable = $("#tabla_personas_aprueban_index").DataTable({
      "destroy": true,
      "processing": true,
      'data': resp,
      "columns": [
        {
          'data': 'nombre_completo'
        },
        {
          'data': 'identificacion'
        },
        {
          'data': 'fecha_registra'
        },
        {
          'defaultContent': `<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-remove pointer btn btn-default eliminar"></span>`,
        },

      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    $('#tabla_personas_aprueban_index tbody').on('click', 'tr td .eliminar', function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      retirar_persona_aprueba(id);
    });

    $('#tabla_personas_aprueban_index tbody').on('click', 'tr', function () {
      $("#tabla_personas_aprueban_index tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

  });

}

const retirar_persona_aprueba = id => {
  swal({
    title: '¿ Retirar Permiso ?',
    text: "Tener en cuenta que, al realizar esta accíon ya no sera necesario el aprobado de esta persona para cerrar un proyecto, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
        consulta_ajax(`${ruta}retirar_persona_aprueba`, { id }, ({ mensaje, tipo, titulo }) => {
          if (tipo == 'success') {
            swal.close();
            listar_personas_vb_index();
            personas_aprueban_index();
          } else MensajeConClase(mensaje, tipo, titulo);

        });
      }
    });

}

const listar_actividades = persona => {
	consulta_ajax(`${ruta}listar_actividades`, { persona }, data => {
		$(`#tabla_actividades tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.quitar')
			.off('click', 'tr span.config')
			.off('dblclick', 'tr');
		const myTable = $("#tabla_actividades").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'nombre' },
				{
					render: (data, type, { asignado }, meta) => {
						let datos = asignado
							? '<span class="btn btn-default quitar" style="color: #5cb85c" title="Desactivar"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config" title="Configuraciones"><span class="fa fa-cog"></span></span>'
							: '<span class="btn btn-default asignar" title="Activar"><span class="fa fa-toggle-off" ></span></span> ';
						return datos;
					}
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_actividades tbody').on('click', 'tr', function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_actividades tbody').on('dblclick', 'tr', function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_actividades tbody').on('click', 'tr span.asignar', function () {
      const { id } = myTable.row($(this).parent()).data();
			asignar_actividad(id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.quitar', function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.config', function () {
			const { asignado } = myTable.row($(this).parent()).data();
			listar_estados(asignado, persona);
			$('#modal_elegir_estado').modal();
		});
	});

	const asignar_actividad = (id) => {
    const id_persona = $('#persona_solicitud_id').val();
		consulta_ajax(`${ruta}asignar_actividad`, { id, persona: id_persona }, ({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			listar_actividades(id_persona);
		});
	}

	const quitar_actividad = (asignado, id) => {
    const id_persona = $('#persona_solicitud_id').val();
    mensaje_confirmar('Remover Actividad', 'Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.', () => {
      consulta_ajax(`${ruta}quitar_actividad`, { id, persona: id_persona, asignado }, ({ mensaje, tipo, titulo }) => {
        listar_actividades(id_persona);
        swal.close();
      });
    });
	}
}

const listar_estados = (actividad, persona) => {
	const desasignar = '<span class="btn btn-default desasignar" title="Quitar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar = '<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
	const notificar = '<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
  const no_notificar = '<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
  const gestionar = '<span class="btn btn-default gestionar" title="Activar Gestión"><span class="fa fa-cog" style="color: gray;"></span></span> ';
	const no_gestionar = '<span class="btn btn-default no_gestionar" title="Desactivar Gestionar"><span class="fa fa-cog red"></span></span> ';
	consulta_ajax(`${ruta}listar_estados`, { actividad }, data => {
		$(`#tabla_estados tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.desasignar')
			.off('click', 'tr span.no_notificar')
      .off('click', 'tr span.notificar')
      .off('click', 'tr span.no_gestionar')
			.off('click', 'tr span.gestionar')
			.off('dblclick', 'tr');
		const myTable = $("#tabla_estados").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
        { data: 'parametro' },
				{ data: 'nombre' },
				{
					render: (data, type, { asignado, gestion, notificacion }, meta) => {
            let acciones = '';
            if (asignado) {
              acciones = desasignar;
              acciones += gestion === '1' ? no_gestionar : gestionar;
              acciones += notificacion === '1' ? no_notificar : notificar;
            } else {
              acciones = asignar;
            }
            return acciones;
					}
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_estados tbody').on('click', 'tr', function () {
			$("#tabla_estados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_estados tbody').on('click', 'tr span.asignar', function () {
			const { estado } = myTable.row($(this).parent()).data();
			asignar_estado(estado, actividad, persona);
		});

		$('#tabla_estados tbody').on('click', 'tr span.desasignar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			quitar_estado(estado, actividad, persona, asignado);
		});

		$('#tabla_estados tbody').on('click', 'tr span.notificar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			activar_notificacion(estado, actividad, persona, asignado);
		});

		$('#tabla_estados tbody').on('click', 'tr span.no_notificar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			desactivar_notificacion(estado, actividad, persona, asignado);
    });
    
    $('#tabla_estados tbody').on('click', 'tr span.gestionar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			activar_gestion(estado, actividad, persona, asignado);
		});

		$('#tabla_estados tbody').on('click', 'tr span.no_gestionar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			desactivar_gestion(estado, actividad, persona, asignado);
		});

		const activar_notificacion = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}activar_notificacion`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
		}

		const desactivar_notificacion = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}desactivar_notificacion`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
    }
    
    const activar_gestion = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}activar_gestion`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
		}

		const desactivar_gestion = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}desactivar_gestion`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
		}

		const asignar_estado = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}asignar_estado`, { estado, actividad, persona }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
		}

		const quitar_estado = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}quitar_estado`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) => listar_estados(actividad, persona));
		}
	});
}

const obtener_personas_adm_index = () => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_personas_adm_index`, '', (resp) => {
      resolve(resp);
    });
  });
}

const administrar_modulo = tipo => {
  if (tipo == 'admin_aprobados_persona') {
    listar_personas_vb_index();
    personas_aprueban_index();
    $("#container_admin_index").fadeIn(1000);
    $("#container_admin_proyectos_index").hide();
    $('#container_admin_proyectos_parametros').hide();
    $("#container_admin_proyectos_instituciones").hide();
    $('#container_admin_proyectos_comite').hide();

    $('#btn_guardar_parametros_generales').hide();
  } else if (tipo == 'admin_proyectos_persona') {
    $('#persona_solicitud_id').val('')
    $('#permiso_persona').html('Seleccione una persona');
    listar_actividades(null);
    $("#container_admin_index").hide();
    $("#container_admin_proyectos_index").fadeIn(1000);
    $('#container_admin_proyectos_parametros').hide();
    $("#container_admin_proyectos_instituciones").hide();
    $('#container_admin_proyectos_comite').hide();

    $('#btn_guardar_parametros_generales').hide();
  } else if (tipo == 'admin_proyectos_parametros') {
    cargar_datos_parametros_generales();
    $("#container_admin_index").hide();
    $("#container_admin_proyectos_index").hide();
    $('#container_admin_proyectos_parametros').fadeIn(1000);
    $("#container_admin_proyectos_instituciones").hide();
    $('#container_admin_proyectos_comite').hide();

    $('#btn_guardar_parametros_generales').show();
  } else if (tipo == 'admin_proyectos_instituciones') {
    $("#container_admin_index").hide();
    $("#container_admin_proyectos_index").hide();
    $('#container_admin_proyectos_parametros').hide();
    $("#container_admin_proyectos_instituciones").fadeIn(1000);
    $('#container_admin_proyectos_comite').hide();

    $('#btn_guardar_parametros_generales').hide();
  } else if (tipo == 'admin_proyectos_comite') {
    $("#container_admin_index").hide();
    $("#container_admin_proyectos_index").hide();
    $('#container_admin_proyectos_parametros').hide();
    $("#container_admin_proyectos_instituciones").hide();
    $('#container_admin_proyectos_comite').fadeIn(1000);

    $('#btn_guardar_parametros_generales').hide();
  } else if (tipo == 'menu') {
    $("#menu_principal").fadeIn(1000);
    $("#container_proyectos").css("display", "none");
    datos_text_area.forEach(elemento => {
      administrarBtnArea(elemento.id, 'inactive');
      elemento.texto = '';
    });
  } else if (tipo == 'listado_proyectos') {
    $("#container_proyectos").fadeIn(1000);
    $("#menu_principal").css("display", "none");
    $("#form_guardar_proyecto").get(0).reset();
    datos_text_area.forEach(elemento => {
      administrarBtnArea(elemento.id, 'inactive');
      elemento.texto = '';
    });
    listar_proyectos_usuario();
  }
  $("#nav_admin_index li").removeClass("active");
  $(`#${tipo}`).addClass("active");

}

const cargar_datos_parametros_generales = () => {
  consulta_ajax(`${ruta}traer_datos_parametros_generales`, {}, resp => {
    let { iva } = resp;
    $('#iva').val(iva);
  });
}

const abrir_proyectos_notificaciones = id => {
  id_comite = id;
  listar_comentarios_comite(id_comite);
  listar_proyectos(id_comite);
  $("#agregar_proyecto").hide('fast');
  $('#modal_detalle_solicitud').modal();
}

const abrir_proyecto = async (id_s) => {
  let data = await listar_proyecto_id(id_s);
  let { id_comite, id_estado_comite } = data;
  let id = id_comite;
  mostrar_detalle_comite({ id, id_estado_comite });
  ver_detalle_proyecto(data);
}

const listar_proyecto_id = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_proyecto_id`, { id }, (resp) => {
      resolve(resp);
    });
  });
}

const agregar_persona = () => {
  let data = new FormData(document.getElementById("form_agregar_persona"));
  enviar_formulario(`${ruta}agregar_persona`, data, (resp) => {
    let { tipo, mensaje, titulo, postulante } = resp;
    if (tipo == 'success') callbak_activo(postulante);
    MensajeConClase(mensaje, tipo, titulo);

  });
}

const administrarModalArea = (id) => {
  let data = datos_text_area.find((e) => e.id == id);
  if (data) {
    let nombre = data.titulo.replace('Agregar ', '');
    $('#data_text_area').val(data.texto).attr('title', nombre).attr('placeholder', nombre).attr('maxlength', '65535');
    $('#titulo_text_area').text(data.titulo);
    $('#agregar_text_area').off('submit');
    $('#agregar_text_area').submit((e) => {
      e.preventDefault();
      let dato = $('#data_text_area').val();
      if (dato) {
        data.texto = dato;
        administrarBtnArea(data.id, 'success');
        guardar_proyecto()
        $('#modal_text_area').modal('hide');
      }
    });
    $('#modal_text_area').modal();
    $('#data_text_area').focus();
  }
}

const administrarBtnArea = (id, tipo) => {
  if (tipo == 'success') {
    $(`#span_${id}`).css('color', 'green');
    $(`#${id}`).css('color', 'black');
  } else if (tipo == 'inactive') {
    $(`#span_${id}`).css('color', '');
    $(`#${id}`).css('color', 'black');
  } else if (tipo == 'error') {
    $(`#span_${id}`).css('color', '#ED4337');
    $(`#${id}`).css('color', '#ED4337');
  }
}

const inicializar_proyecto = (datos_tipo_proyecto, tipo_participante, institucion, id_departamento) => {
  consulta_ajax(`${ruta}inicializar_proyecto`, { tipo_proyecto: datos_tipo_proyecto.id, tipo_participante, institucion, id_departamento }, async resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      swal.close();
      let { id } = resp;
      $('#modal_pregunta_nuevo_proyecto').modal('hide');
      $("#modal_seleccion_departamento").modal('hide');
      $('#form_guardar_proyecto').get(0).reset();
      datos_text_area.forEach(elemento => {
        administrarBtnArea(elemento.id, 'inactive');
        elemento.texto = '';
      });
      id_proyecto = id;
      id_tipo_proyecto = datos_tipo_proyecto.id;
      id_aux_proyecto = datos_tipo_proyecto.id_aux;
      cambiar_informacion_tipo_proyecto(datos_tipo_proyecto.id, 'Proy_For', datos_tipo_proyecto.id_aux);
      $('#titulo_tipo_proyecto').html(datos_tipo_proyecto.valor);
      $('#modal_menu_proyecto').modal('show');
      if (datos_tipo_proyecto.id_aux == 'Pro_Int') {
        setTimeout(() => $('#modal_editar_proyecto').modal('show'), 200);
        setTimeout(() => $('#modal_agregar_preguntas_convenio_proceedings').modal('show'), 400);
      }
    } else if (tipo == 'sin_departamento'){
      swal.close();
      Cargar_parametro_buscado(91, '.cbx_seleccion_departamento', 'Seleccione su departamento');
      $("#modal_seleccion_departamento").modal();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const guardar_proyecto = () => {
  datos_text_area.forEach(elemento => {
    if (elemento.texto.trim() == '') administrarBtnArea(elemento.id, 'inactive');
  });

  let formulario = 'form_guardar_proyecto';
  let formData = new FormData(document.getElementById(formulario));
  let data = formDataToJson(formData);
  data.id_aux_proyecto = id_aux_proyecto;
  data.id_proyecto = id_proyecto;
  data.resumen = datos_text_area[0].texto.trim();
  data.justificacion = datos_text_area[1].texto.trim();
  data.planteamiento_problema = datos_text_area[2].texto.trim();
  data.marco_teorico = datos_text_area[3].texto.trim();
  data.estado_arte = datos_text_area[4].texto.trim();
  data.diseno_metodologico = datos_text_area[5].texto.trim();
  data.resultados_esperados = datos_text_area[6].texto.trim();
  consulta_ajax(`${ruta}guardar_proyecto`, data, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      $('#mensaje_editar_proyecto').html('¡Proyecto Guardado!');
      let alert = '.alert-success';
      if (!$(alert).is(':visible')) {
        $(alert).fadeIn(500);
        $(alert).fadeOut(5000);
      }
      listar_proyectos_usuario();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const ultimo_proyecto = () => {
  return new Promise(resolve => {
    let url = `${ruta}ultimo_proyecto`;
    consulta_ajax(url, {}, (resp) => {
      let { tipo } = resp;
      if (tipo == 'success') {
        let proyecto = resp.proyecto;
        if (typeof proyecto.id === 'string' || !Array.isArray(proyecto)) {
          resolve(proyecto);
        } else {
          resolve(null);
        }
      } else {
        resolve(null);
      }
    });
  });
}
const mostrar_ultimo_proyecto = async () => {
  let proyecto = await ultimo_proyecto();
  if (proyecto != null) {
    cargar_proyecto(proyecto);
    $('#modal_menu_proyecto').modal('show');
  } else {
    MensajeConClase('¡No tienes ningún proyecto creado!', 'info', '¡Sin Proyectos!');
  }
}

const cargar_proyecto = proyecto => {
  consulta_ajax(`${ruta}traer_informacion_proyecto`, { id: proyecto.id }, async (resp) => {
    let {
      id,
      id_estado_proyecto,
      nombre_proyecto,
      tipo_proyecto,
      id_aux_tipo_proyecto,
      nombre_tipo_proyecto,
      tipo_recurso,
      fecha_inicial,
      fecha_final,
      no_beneficiarios,
      id_tipo_movilidad,
      otro_tipo_movilidad,
      id_formacion_responsable,
      tipo_responsable,
      id_responsable_externo,
      nombre_completo_responsable_externo,
      tipo_proyecto_grado,
      resumen,
      justificacion,
      planteamiento_problema,
      marco_teorico,
      estado_arte,
      diseno_metodologico,
      resultados_esperados,
      laboratorio,
    } = resp;
    id_proyecto = id
    id_tipo_proyecto = tipo_proyecto;
    id_aux_proyecto = id_aux_tipo_proyecto;
    $('#nombre_proyecto').val(nombre_proyecto);
    await cambiar_informacion_tipo_proyecto(tipo_proyecto, id_estado_proyecto, id_aux_tipo_proyecto);
    $('#titulo_tipo_proyecto').html(nombre_tipo_proyecto);
    $('#id_tipo_recurso').val(tipo_recurso);
    $('#fecha_inicial').val(fecha_inicial);
    $('#fecha_final').val(fecha_final);
    $('#no_beneficiarios').val(no_beneficiarios);
    $('#tipo_movilidad').val(id_tipo_movilidad).change();
    $('#otro_tipo_movilidad').val(otro_tipo_movilidad);
    $('#formacion_responsable').val(id_formacion_responsable);
    $('#tipo_responsable').val(tipo_responsable).change();
    $('#id_responsable_externo').val(id_responsable_externo);
    $('#txt_nombre_responsable_externo').val(nombre_completo_responsable_externo);
    $('#tipo_responsable').change();
    $('#tipo_proyecto_grado').val(tipo_proyecto_grado);
    $('#id_laboratorio').val(laboratorio);
    datos_text_area[0].texto = (resumen == null) ? '' : resumen;
    datos_text_area[1].texto = (justificacion == null) ? '' : justificacion;
    datos_text_area[2].texto = (planteamiento_problema == null) ? '' : planteamiento_problema;
    datos_text_area[3].texto = (marco_teorico == null) ? '' : marco_teorico;
    datos_text_area[4].texto = (estado_arte == null) ? '' : estado_arte;
    datos_text_area[5].texto = (diseno_metodologico == null) ? '' : diseno_metodologico;
    datos_text_area[6].texto = (resultados_esperados == null) ? '' : resultados_esperados;
    datos_text_area.forEach(elemento => {
      if (elemento.texto.trim() != '') {
        administrarBtnArea(elemento.id, 'success')
      } else {
        administrarBtnArea(elemento.id, 'inactive');
      }
    });
  });
}

const obtener_valores_permisos = (id_valor, idparametro, tipo = 'left') => {
  return new Promise(resolve => {
    const ruta = `${Traer_Server()}index.php/profesores_csep_control/`;
    let url = `${ruta}obtener_valores_permisos`;
    consulta_ajax(url, { idparametro, id_valor, tipo }, resp => {
      resolve(resp);
    });
  });
}

const obtener_valores_permisos_general = (id_valor, idparametro, tipo = 1) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_valores_permisos`;
    consulta_ajax(url, { idparametro, id_valor, tipo }, resp => {
      resolve(resp);
    });
  });
}

const pintar_datos_parametros_combo = (datos, combo, mensaje, defecto = '', ) => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(elemento => { $(combo).append(`<option value='${elemento.id}'> ${elemento.nombre}</option>`); });
  $(combo).val(defecto);
}

const traer_datos_item = (item, id_solicitud) => {
  let buscar = null;
  let data = null;
  if (item === 'comite_proyectos') {
    buscar = `listar_proyectos_usuario`;
    data = { id: id_proyecto };
  } else {
    item = item.replace('comite_proyectos_', '');
    if (item == 'presupuestos') {
      buscar = 'traer_presupuesto';
      data = { id_proyecto, id_presupuesto: id_solicitud, tipo: 1 };
    } else {
      buscar = `listar_${item}`;
      data = { id_proyecto, id: id_solicitud, tipo: 1 };
    }
  }
  return new Promise((resolve) => {
    consulta_ajax(`${ruta + buscar}`, data, resp => {
      if (item == 'presupuestos') {
        let temp = [];
        let aux = {};
        resp.forEach(elemento => {
          aux[elemento.nombre_dato.replace(/ /g, '_')] = elemento.tipo_dato == 'Numerico' || elemento.tipo_dato == 'Texto' ? elemento.valor : elemento.valor_select;
        });
        temp.push(aux);
        resolve(temp)
      } else {
        resolve(resp);
      }
    });
  });
}

const listar_item = (item, id_proyecto, tipo_presupuesto) => {
  switch (item) {
    case 'participante':
      listar_participantes(id_proyecto);
      $('#modal_agregar_participante').modal('hide');
      break;
    case 'lugar':
      listar_lugares(id_proyecto);
      break;
    case 'institucion':
      listar_instituciones(id_proyecto);
      break;
    case 'institucion_bdd':
      listar_instituciones_bdd(id_proyecto);
      break;
    case 'programa':
      listar_programas(id_proyecto);
      break;
    case 'asignatura':
      listar_asignaturas(id_proyecto);
      break;
    case 'sublinea':
      listar_sublineas(id_proyecto);
      break;
    case 'ods':
      listar_ods(id_proyecto);
      break;
    case 'objetivo':
      listar_objetivos(id_proyecto);
      break;
    case 'impacto':
      listar_impactos(id_proyecto);
      break;
    case 'producto':
      listar_productos(id_proyecto);
      break;
    case 'cronograma':
      listar_cronogramas(id_proyecto);
      break;
    case 'presupuesto':
      listar_resumen_presupuestos(id_proyecto);
      listar_presupuestos(id_proyecto, tipo_presupuesto);
      listar_presupuesto_discriminado_entidad(id_proyecto);
      listar_presupuesto_discriminado_entidad_rubro(id_proyecto);
      listar_presupuesto_financiacion(id_proyecto);
      $('#tipo_presupuesto').val(tipo_presupuesto);
      break;
    case 'soporte':
      listar_soportes(id_proyecto);
      break
    case 'bibliografia':
      listar_bibliografias(id_proyecto);
      break;
  }
}

const guardar_item = async (formulario, item, tipo_presupuesto = null) => {
  let formData = new FormData(document.getElementById(formulario));
  let data = formDataToJson(formData);
  data.id_proyecto = id_proyecto;
  if (item == 'participante') {
    data.institucion = $(`#instituciones option[value="${$('#institucion').val()}"]`).attr('id');
    let dato = await buscar_datos_valor_parametro(data.tipo_participante);
    data.id_aux_tipo_participante = dato.id_aux;
  }
  if (item == 'programa') data.programa = $(`#lista_programas option[value="${$('#programa').val()}"]`).attr('id');
  if (item == 'producto' || item == 'cronograma') data.participantes = responsables;
  consulta_ajax(`${ruta}guardar_${item}`, data, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      $(`#${formulario}`).get(0).reset();
      if (item == 'producto' || item == 'cronograma') responsables = [];
      listar_item(item, id_proyecto, tipo_presupuesto);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const modificar_item = async (formulario, item, id, tipo_presupuesto = null) => {
  let formData = new FormData(document.getElementById(formulario));
  let data = formDataToJson(formData);
  data.id_proyecto = id_proyecto;
  data.id = id;
  if (item == 'participante') {
    data.institucion = $(`#instituciones option[value="${$('#institucion').val()}"]`).attr('id');
    let dato = await buscar_datos_valor_parametro(data.tipo_participante);
    data.id_aux_tipo_participante = dato.id_aux;
  }
  if (item == 'programa') data.programa = $(`#lista_programas option[value="${$('#programa').val()}"]`).attr('id');
  if (item == 'producto' || item == 'cronograma') data.participantes = responsables;
  consulta_ajax(`${ruta}modificar_${item}`, data, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      $(`#${formulario}`).get(0).reset();
      (item == 'presupuesto') ? $(`#${formulario.replace('form_modificar', 'modal_modificar')}`).modal('hide') : $(`#${formulario.replace('form', 'modal')}`).modal('hide');
      listar_item(item, id_proyecto, tipo_presupuesto);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const eliminar_item = (item, id, tipo_presupuesto = null) => {
  consulta_ajax(`${ruta}eliminar_${item}`, { id, id_proyecto }, resp => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      swal.close();
      listar_item(item, id_proyecto, tipo_presupuesto);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const cambiar_estado_proyecto_usuario = (id, estado, observaciones = null) => {
  consulta_ajax(`${ruta}gestionar_proyecto`, { id, estado, observaciones }, resp => {
    let { mensaje, tipo, titulo } = resp;
    listar_proyectos_usuario();
    if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
    else swal.close();
  });
}

let recibir_archivos = () => {
  Dropzone.options.Subir = {
    url: `${ruta}recibir_archivos`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
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
    acceptedFiles: "application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
    acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
    autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas

    error: function (response) {
      if (!response.xhr) {
        MensajeConClase("¡Solo se permite cargar archivos con formato odt, doc, docx, odp, ppt, ods, xls, pdf, csv!", "info", "Oops!");
      } else {
        MensajeConClase(response.xhr.responseText, 'info', 'Oops!');
      }
    },

    success: function (file, response) {
      MensajeConClase("Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
      listar_soportes(id_proyecto);
      $("#modal_enviar_archivos").modal('hide');
      num_archivos = 0;
    },

    init: function () {
      num_archivos = 0;
      myDropzone = this;
      this.on("addedfile", function (file) {
        num_archivos++;
        cargados++;
      });
      this.on("removedfile", function (file) {
        num_archivos--;
        cargados--;
      });
      myDropzone.on("complete", function (file) {
        myDropzone.removeFile(file);
      });
      myDropzone.on("processing", function (file) {
        this.options.params = { id_proyecto }
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

const cargar_participante = datos => {
  let {
    id,
    nombre_completo,
    id_tipo_participante,
    institucion
  } = datos;
  $('#id_postulante').val(id);
  $('#nombre_postulante').html(nombre_completo);
  $('#tipo_participante').val(id_tipo_participante);
  $('#institucion').val(institucion);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_participante', 'Modificar Participante');

  $('#modal_agregar_participante').modal('show');
}

const traer_informacion_participante = (id, tipo_tabla) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}traer_informacion_participante`, { id, tipo_tabla }, resp => {
      resolve(resp);
    });
  });
}

const cargar_lugar = lugar => {
  let {
    pais,
    ciudad
  } = lugar;
  $('#pais').val(pais);
  $('#ciudad').val(ciudad);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_lugar', 'Modificar Lugar');

  $('#modal_agregar_lugar').modal('show');
}

const cargar_institucion_bdd = institucion => {
  let {
    nombre,
    nit,
    pais_origen,
    correo,
    telefono_contacto,
    nombre_contacto
  } = institucion;
  $('#nombre_institucion').val(nombre);
  $('#nit_institucion').val(nit);
  $('#pais_origen_institucion').val(pais_origen);
  $('#correo_institucion_bdd').val(correo);
  $('#telefono_contacto_institucion').val(telefono_contacto);
  $('#nombre_contacto_institucion').val(nombre_contacto);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_institucion_bdd', 'Modificar Institución');

  $('#modal_agregar_institucion_bdd').modal('show');
}

const cargar_institucion = institucion => {
  let {
    id_institucion,
    responsabilidad_contraparte,
    responsabilidad_cuc
  } = institucion;

  $('#id_institucion').val(id_institucion);
  $('#responsabilidad_contraparte_institucion').val(responsabilidad_contraparte);
  $('#responsabilidad_cuc_institucion').val(responsabilidad_cuc);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_institucion', 'Modificar Institución');

  $('#modal_agregar_institucion').modal('show');
}

const cargar_programa = datos_programa => {
  let {
    programa,
    id_tipo_interaccion
  } = datos_programa;

  $('#programa').val(programa);
  $('#id_tipo_interaccion').val(id_tipo_interaccion);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_programa', 'Modificar Programa');

  $('#modal_agregar_programa').modal('show');
}

const cargar_asignatura = datos_asignatura => {
  let {
    asignatura
  } = datos_asignatura;

  $('#asignatura_proyecto').val(asignatura);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_asignatura', 'Modificar Asignatura');

  $('#modal_agregar_asignatura').modal('show');
}

const cargar_sublinea = async sublinea => {
  let {
    id_grupo,
    id_linea,
    id_sublinea
  } = sublinea;
  let sub_lineas = await obtener_valores_permisos(id_linea, 88, 'inner');
  pintar_datos_parametros_combo(sub_lineas, '.cbx_sublinea_investigacion', 'Seleccione una Sub-Línea de Investigación');
  $('#grupo_de_investigacion').val(id_grupo);
  $('#linea_investigacion').val(id_linea);
  $('#sublinea_investigacion').val(id_sublinea);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_sublinea', 'Modificar Sublínea');

  $('#modal_agregar_sublinea').modal('show');
}

const cargar_ods = ods => {
  let {
    id_ods
  } = ods;
  $('#ods').val(id_ods);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_ods', 'Modificar Objetivo de Desarrollo Sostenible');

  $('#modal_agregar_ods').modal('show');
}

const cargar_objetivo = objetivo => {
  let {
    tipo_objetivo,
    descripcion
  } = objetivo;
  (tipo_objetivo == 'General') ? $('#general').prop('checked', true) : $('#especifico').prop('checked', true);
  $('#descripcion_objetivo').val(descripcion);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_objetivo', 'Modificar Objetivo');

  $('#modal_agregar_objetivo').modal('show');
}

const traer_objetivos = id_proyecto => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_objetivos`, { id_proyecto }, (resp) => {
      resolve(resp);
    });
  });
}

const cargar_cbx_objetivos = async id_proyecto => {
  let datos = await traer_objetivos(id_proyecto);
  let combo = '.cbx_objetivos_especificos';
  $(combo).html(`<option value>Seleccione un Objetivo Específico</option>`);
  let count = 1;
  datos.map((elemento) => {
    let { tipo_objetivo, descripcion, id } = elemento;
    if (tipo_objetivo == 'Específico') {
      $(combo).append(`<option value = "${id}" title="${descripcion}">${count++}. ${(descripcion.length > 85) ? descripcion.substring(0, 85) + '...' : descripcion}</option>`);
    }
  });
}

const cargar_impacto = impacto => {
  let {
    id_tipo_impacto,
    descripcion
  } = impacto;
  $('#tipo_impacto').val(id_tipo_impacto);
  $('#descripcion_impacto').val(descripcion);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_impacto', 'Modificar Impacto');

  $('#modal_agregar_impacto').modal('show');
}

const cargar_producto = async producto => {
  let {
    participantes,
    id_tipo_producto,
    id_producto,
    observaciones
  } = producto;
  let productos = await obtener_valores_permisos_general(id_tipo_producto, 175);
  pintar_datos_combo(productos, '.cbx_productos', 'Seleccione un Producto');
  $('#tipo_producto').val(id_tipo_producto);
  $('#producto').val(id_producto);
  $('#observaciones').val(observaciones);
  responsables = [];
  participantes.forEach(p => responsables.push(p.id));
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_producto', 'Modificar Producto');

  $('#modal_agregar_producto').modal('show');
}

const cargar_cronograma = async cronograma => {
  let {
    participantes,
    id_objetivo_especifico,
    fecha_inicial,
    fecha_final,
    actividad
  } = cronograma;
  await cargar_cbx_objetivos(id_proyecto);
  $('#objetivo_especifico').val(id_objetivo_especifico);
  $('#fecha_inicial_cronograma').val(fecha_inicial);
  $('#fecha_final_cronograma').val(fecha_final);
  $('#actividad').val(actividad);
  responsables = [];
  participantes.forEach(p => responsables.push(p.id));
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_cronograma', 'Modificar Cronograma');

  $('#modal_agregar_cronograma').modal('show');
}

const traer_presupuesto = (id_proyecto, id_presupuesto) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}traer_presupuesto`, { id_proyecto, id_presupuesto }, resp => {
      resolve(resp);
    });
  });
}

const cargar_presupuesto = async id_presupuesto => {
  $('#tipo_de_valor').off('change');
  let id_valor = null;
  let id_tipo_software = null;
  let id_recurso = null;
  let id_entidad_responsable = null;
  let id_empresa_prestadora = null;
  let id_grupo = null;
  let id_tipo_financiacion = null;
  let id_financiacion_internacional = null;
  let id_financiacion_nacional = null;
  let presupuesto = await traer_presupuesto(id_proyecto, id_presupuesto);
  let div = $('#inputs_modificar_presupuesto').html('');
  presupuesto.forEach(async elemento => {
    let { id_aux_dato, nombre_dato, tipo_dato, valor, dato_requerido, id_datos, multiplica } = elemento;
    let input;
    let nombre = nombre_dato.toLowerCase().replace(/ /g, '_');
    if (id_aux_dato == 'Pre_Tipo_Val') id_valor = valor;
    if (id_aux_dato == 'Pre_Tipo_Sof') id_tipo_software = valor;
    if (id_aux_dato == 'Pre_Tipo_Rec') id_recurso = valor;
    if (id_aux_dato == 'Pre_Ent_Res') id_entidad_responsable = valor;
    if (id_aux_dato == 'Pre_Emp_Pre') id_empresa_prestadora = valor;
    if (id_aux_dato == 'Pre_Gru_Inv') id_grupo = valor;
    if (id_aux_dato == 'Pre_Tipo_Fin') id_tipo_financiacion = valor;
    if (id_aux_dato == 'Pre_Fin_Int') id_financiacion_internacional = valor;
    if (id_aux_dato == 'Pre_Fin_Nac') id_financiacion_nacional = valor;
    if (tipo_dato == 'Select') {
      input = $('<select></select>').addClass(`cbx_${nombre}`).attr('id', nombre);
      div.append(input);
      if (id_datos == 'Pre_Inv') {
        let temp = await traer_participantes(id_proyecto);
        let participantes = [];
        temp.forEach(participante => participantes.push({ 'id': participante.id, 'valor': participante.nombre_completo }));
        pintar_datos_combo(participantes, `.cbx_${nombre}`, 'Seleccione el/la Investigador(a)', valor);
      } else {
        let temp = await obtener_valores_parametro(id_datos);
        let datos = [];
        temp.forEach(dato => datos.push({ 'id': dato.id, 'valor': dato.valor }));
        pintar_datos_combo(datos, `.cbx_${nombre}`, nombre_dato.capitalize(true), valor);
      }
    } else {
      let type = (tipo_dato == 'Texto') ? 'text' : 'number';
      input = $('<input>').attr('placeholder', nombre_dato).attr('type', type);
    }
    input.addClass('form-control').attr('title', nombre_dato).attr('name', nombre).val(multiplica == '1' ? parseInt(valor.replace('$ ', '').replace(/\./g, '')) : valor);
    if (dato_requerido == '1') input.attr('required', true);
    div.append(input);
  });
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_presupuesto', 'Modificar Presupuesto');
  change_tipo_presupuesto(id_valor, id_tipo_software);
  change_tipo_software(id_tipo_software, id_entidad_responsable, id_empresa_prestadora, id_grupo);
  change_tipo_recurso(id_recurso);
  change_tipo_financiacion(id_tipo_financiacion);
  change_tipo_financiacion_internacional(id_financiacion_internacional, id_entidad_responsable);
  change_tipo_financiacion_nacional(id_financiacion_nacional, id_entidad_responsable);
  $('#modal_modificar_presupuesto').modal('show');
}

const cargar_bibliografia = bibliografia => {
  let descripcion_bibliografia = bibliografia.bibliografia;
  $('#descripcion_bibliografia').val(descripcion_bibliografia);
  cambiar_titulo('fa fa-pencil-square-o', 'titulo_bibliografia', 'Modificar Bibliografía');

  $('#modal_agregar_bibliografia').modal('show');
}
const mensaje_input = (title, inputPlaceholder, callback, inputvalue = null) => {
  swal({
    title,
    text: "",
    type: "input",
    inputValue: inputvalue ? inputvalue : '',
    showCancelButton: true,
    confirmButtonColor: "#D9534F",
    confirmButtonText: "Aceptar",
    cancelButtonText: "Cancelar",
    allowOutsideClick: true,
    closeOnConfirm: false,
    closeOnCancel: true,
    inputPlaceholder
  }, function (mensaje) {
    if (mensaje === false)
      return false;
    if (mensaje === "") {
      swal.showInputError(`Debe Ingresar una observación!`);
    } else {
      callback(mensaje);
    }
  });
}

const mensaje_confirmar = (title, text, callbak) => {
  swal({
    title,
    text,
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
        callbak();
      }
    });
}

const traer_proyecto = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}listar_proyectos_usuario`, { id }, (resp) => {
      resolve(resp);
    });
  });
}

const enviar_correo = async (estado, id) => {
  let { nombre_persona, nombre_proyecto, solicitudes } = data_correo;
  let sw = false;
  let tipo = -1;
  let mensaje = null;
  let asunto = 'Proyectos Index';
  let nombre = null;
  let correo_enviar = null;
  if (estado == 'solicitud_correccion') {
    sw = true;
    tipo = 3;
    correo_enviar = await obtener_personas_adm_index();
    nombre = 'Administrador(a) de Proyectos Index';
    let items = [];
    solicitudes.forEach(e => {
      if(e.razones) items.push(e.valor);
    });
    mensaje = `Se informa que el se&ntilde;or(a) ${nombre_persona} ha solicitado hacer ${solicitudes.length == 1 ? 'una correcci&oacute;n' : 'algunas correcciones'} en su proyecto 
              '${nombre_proyecto}' en ${solicitudes.length == 1 ? 'el &iacute;tem' : 'los items'}: ${items.join(', ')}.`;
  } else if (estado == 'respuesta_solicitud_correccion') {
    sw = true;
    tipo = 1;
    let resp = await traer_proyecto(id);
    let { nombre_completo, correo } = resp[0];
    nombre = nombre_completo;
    correo_enviar = correo;
    if (solicitudes.find(e => e.aprobado == 1)) {
      mensaje = `Se informa que su solicitud de correcci&oacute;n sobre el proyecto '${nombre_proyecto}', ha sido aprobada.`;
      let table = `<table style="background-color: #ffffff;">
                    <thead>
                      <tr>
                        <td style="color: black; font-size: 13px; border-left: 4px solid #6e1f7c !important; margin-top: 11px; font-weight: normal; text-transform: uppercase;" colspan="2">Items aprobados</td>
                      </tr>
                      <tr style="text-align: center;">
                        <td>Ítem</td>
                        <td>Fecha Límite</td>
                      </tr>
                    </thead>
                    <tbody>`;
        solicitudes.forEach(item => {
          if (item.aprobado == 1) {
            table += `<tr style="text-align: center;">
                        <td>${item.valor}</td>
                        <td>${item.fecha_limite}</td>
                      </tr>`;
          }
      });
      table += '</tbody></table>';
      mensaje += `<br/><br/> ${table}`;
    } else {
      mensaje = `Se informa que se le ha negado la solicitud de correcci&oacute;n sobre el proyecto '${nombre_proyecto}'.`;
    }
  }

  if (sw) enviar_correo_personalizado('index', mensaje, correo_enviar, nombre, asunto, asunto, 'ParCodAdm', tipo);
}
