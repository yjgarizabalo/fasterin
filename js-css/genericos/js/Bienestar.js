let ruta = `${Traer_Server()}index.php/bienestar_control/`;
let callbak_activo = resp => { };
let estudiantes = [];
let funcionarios = [];
let materias_array = [];
let semestre = '';
let programa = '';
let cod_programa = '';
let cod_materia = '';
let materia_grupo = '';
let cant_estudiantes = 0;
let id_solicitud = '';
let id_estado_sol = '';
let id_tipo_solicitud = null;
let fecha_inicio = '';
let fecha_fin = '';
let id_anterior = '';
let datos_persona = null;
let usuario = '';
let id_estudiante = '';
let nombre_estudiante = '';
let correo_estudiante = '';
let tipo_solicitud = '';
let id_tematica = '';
let filtro_funcionario = 1;
let parametro_activo = 121;
let funcionario_nombre = '';
let funcionario_correo = '';
let id_programa_sol = '';
let solicitante = '';
let correo_solicitante = '';
let sw = 'tematicas';
let tematica_actividad = '';
let solicitante_anterior = '';
let reprogramado = false;
let tipo = false;
let id_solicitante = '';
let id_idparametro = '';
let id_bloqueo = '';
let estrategia_sele = [];
let id_horario = '';
let seguir = false;



$(document).ready(function () {


  $("#form_modificar_valor_parametro").submit(() => {
    modificar_valor_parametro();
    return false;
  });

  $("#form_modificar_bloqueo").submit(() => {
    modificar_bloqueo();
    return false;
  });

  $(".regresar_menu").click(function () {
    solicitante_anterior = '';
    reprogramado = false;
    administrar_modulo('regresar_menu')
  });

  $("#solicitud_bienestar").click(() => {
    administrar_modulo('solicitud_bienestar')
  });

  $(".tabla_bloqueos").click(() => {
    $("#modal_disponibilidad_bloqueo").modal();
  });

  $('#listado_solicitudes').click(() => {
    administrar_modulo('listado_solicitudes')
  });

  $(".cbxlugar").change(function (e = '', id_ubicacion = '') {
    const id = $(this).val().trim();
    listar_ubicaciones(id, '.cbxubicacion', id_ubicacion);
  });

  $(".cbxlugar_mod").change(function (e, id_ubicacion = '') {
    const id = $(this).val().trim();
    listar_ubicaciones(id, '.cbxubicacion_mod', id_ubicacion);
  });

  $("#form_solicitud_bienestar").submit(() => {
    guardar_solicitud();
    return false;
  });

  $("#form_logear").submit(() => {
    verificar_firma('asistencia');
    return false;
  });

  $("#form_asignar_funcionario").submit(() => {
    if (funcionarios == '') MensajeConClase("No hay funcionarios asignados.", "info", "Oops...");
    else gestionar_solicitud(id_solicitud, 'Bin_Rev_E', funcionarios);
    return false;
  });

  $("#form_solicitud_bienestar select[name=id_materia]").change(async function () {
    MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
    let materia = $(this).val();
    let codigo_grupo = $(`#form_solicitud_bienestar select[name=id_materia]`).find(":selected").data("grupo");
    if (materia) {
      estudiantes = await obtener_estudiantes_por_materia(codigo_grupo);
      let materia_array = materias_array.find(x => x.id === materia);
      semestre = materia_array.semestre;
      programa = materia_array.nombre_programa;
      cod_programa = materia_array.cod_programa;
      materia_grupo = materia_array.valor;
      cod_materia = materia_array.id;
      let coordinadores = await obtener_coordinadores_por_programa(cod_programa);
      pintar_datos_combo(coordinadores, '.cbxcoordinador', 'Seleccione Coordinador');

      $(".programa").html(programa);
      $(".semestre").html(semestre);
      // $(".coordinador").html(coordinador.coordinador);
      $(".detalle_solicitud").fadeIn('slow');
      $(".estudiantes_solicitud").fadeIn('slow');
      $(".agregar_estudiantes").fadeIn('slow');
      cant_estudiantes = estudiantes.length;
      $(".estudiantes").html('Se esperan ' + cant_estudiantes + ' estudiantes en la clase.');


    } else {
      materia_grupo = '';
      $(".detalle_solicitud").fadeOut('slow');
      $(".estudiantes_solicitud").fadeOut('slow');
      $(".agregar_estudiantes").fadeOut('slow');
      estudiantes = await obtener_estudiantes_por_materia(materia);

    }
    listar_estudiantes();
  });

  $("#form_copiar_bienestar select[name=id_materia]").change(async function () {
    let materia = $(this).val();
    if (materia) {
      estudiantes = await obtener_estudiantes_por_materia(materia);
      let materia_array = materias_array.find(x => x.id === materia);
      semestre = materia_array.semestre;
      programa = materia_array.nombre_programa;
      cod_programa = materia_array.cod_programa;
      materia_grupo = materia_array.valor;
      cod_materia = materia_array.id;
      $(".programa").html(programa);
      $(".semestre").html(semestre);
      $(".detalle_solicitud").fadeIn('slow');
      $(".estudiantes_solicitud").fadeIn('slow');
      $(".agregar_estudiantes").fadeIn('slow');
      cant_estudiantes = estudiantes.length;
      $(".estudiantes").html('Se esperan ' + cant_estudiantes + ' estudiantes en la clase.');

    } else {
      $(".detalle_solicitud").fadeOut('slow');
      $(".estudiantes_solicitud").fadeOut('slow');
      $(".agregar_estudiantes").fadeOut('slow');
    }
    listar_estudiantes();
  });

  $(".agregar_estudiantes").click(function () {
    $("#form_buscar_estudiante").get(0).reset();
    callbak_activo = data => {
      let estudiante = estudiantes.find(element => element.identificacion == data.identificacion);
      if (estudiante)
        MensajeConClase("El estudiante ya fue asignado.", "info", "Oops.!");
      else {
        estudiantes.push(data);
        let table = $("#tabla_estudiantes_busqueda").DataTable();
        table.clear().draw();
        MensajeConClase("Estudiante asignado con exito", "success", "Proceso Exitoso");
      }
      cant_estudiantes = estudiantes.length;
      $(".estudiantes").html('Se esperan ' + cant_estudiantes + ' estudiantes en la clase.');
    };

    buscar_estudiante();
    $("#modal_buscar_estudiante").modal();
  });

  $("#form_buscar_estudiante").submit(() => {
    let dato = $("#txt_est_buscar").val();
    buscar_estudiante(dato, callbak_activo);
    return false;
  });

  $("#asignar_funcionario").click(function () {
    filtro_funcionario = 2;
    $("#form_buscar_persona").get(0).reset();
    callbak_activo = data => {
      let funcionario = funcionarios.find(element => element.identificacion == data.identificacion);
      if (funcionario) MensajeConClase("El funcionario ya fue asignado.", "info", "Oops.!");
      else {
        let identificacion_fun = data.identificacion;
        consulta_ajax(`${ruta}validar_funcionario`, { identificacion_fun, id_solicitud }, resp => {
          let { titulo, mensaje, tipo } = resp;
          if (tipo == "success") {
            funcionarios.push(data);
            let table = $("#tabla_personas_busqueda").DataTable();
            table.clear().draw();
            MensajeConClase("Funcionario seleccionado con exito", "success", "Proceso Exitoso");
            listar_funcionarios();
          } else {
            MensajeConClase(mensaje, tipo, titulo);
          }
        });
      }
    };
    let data_activa = { filtro_funcionario, id_tematica, dato: "", fecha_inicio, fecha_fin };
    buscar_persona(data_activa, callbak_activo);
    $("#modal_buscar_persona").modal();
  });

  $("#form_buscar_persona").submit(() => {
    let dato = $("#txt_per_buscar").val();
    buscar_persona({ dato, filtro_funcionario, id_tematica, fecha_inicio, fecha_fin }, callbak_activo);
    // buscar_persona({dato}, callbak_activo);
    return false;
  });

  $('#detalle_estudiantes').click(function () {
    listar_estudiantes_solicitud(id_solicitud);
    /* $("#modal_estudiantes_solicitud .modal-title").html('<span class="fa fa-users"></span> ESTUDIANTES');
    $("#modal_estudiantes_solicitud .nombre_tabla").html('TABLA DE ESTUDIANTES');
    administrar_vista('estudiantes'); */
  });

  $('#funcionarios_solicitud').click(() => {
    if (id_estado_sol == 'Bin_Sol_E' || id_estado_sol == 'Bin_Rev_E' || id_estado_sol == 'Bin_Rep_E') $("#asignar_funcionario_solicitud").show();
    else $("#asignar_funcionario_solicitud").hide();

    if (id_estado_sol == 'Bin_Sol_E') MensajeConClase('No hay funcionarios asignados.', 'info', 'Oops.!');
    else {
      $("#modal_asignar_funcionario_solicitud").modal();
      buscar_persona();
      listar_funcionarios_solicitud(id_solicitud);
    }



  });

  $("#agregar_estudiantes_nuevos").click(function () {

    buscar_estudiante();
    $("#form_buscar_estudiante").get(0).reset();
    callbak_activo = data => {
      swal({
        title: "Estas Seguro ?",
        text: `Esta seguro de guardar a ${data.nombre_completo}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
      },
        function (isConfirm) {
          if (isConfirm) {
            id_persona = data.id;
            guardar_estudiante_nuevo(id_persona, id_solicitud, data.identificacion);
          }
        }
      );

    };
    buscar_estudiante();
    $("#modal_buscar_estudiante").modal();
  });


  $("#asignar_funcionario_solicitud").click(function () {
    filtro_funcionario = 2;
    // // buscar_persona();
    $("#form_buscar_persona").get(0).reset();
    callbak_activo = data => {
      swal({
        title: "Estas Seguro ?",
        text: `Esta seguro de guardar a ${data.nombre_completo}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
      },
        function (isConfirm) {
          if (isConfirm) {
            id_persona = data.id;
            let identificacion_fun = data.identificacion;
            consulta_ajax(`${ruta}validar_funcionario`, { identificacion_fun, id_solicitud }, resp => {
              let { titulo, mensaje, tipo } = resp;
              if (tipo == "success") {
                guardar_funcionario_nuevo(id_persona, id_solicitud, data.identificacion);
              } else {
                MensajeConClase(mensaje, tipo, titulo);
              }
            });
          }
        }
      );
    };
    let data_activa = { filtro_funcionario, id_tematica, dato: "", fecha_inicio, fecha_fin };
    buscar_persona(data_activa, callbak_activo);
    $("#modal_buscar_persona").modal();

    // Reasignarfuncionario(); //funcion para asignar funcionario automático desde botón asignar funcionario

  });

  $('#modificar_solicitud').click(() => {
    if (id_solicitud == '') MensajeConClase('Seleccione la solicitud a modificar', 'info', 'Oops.!');
    else if (id_estado_sol != 'Bin_Sol_E') MensajeConClase('No es posible realizar esta acción ya que La solicitud se encuentra en proceso o a finalizado.', 'info', 'Oops.!');
    else {
      id_tipo_solicitud = 'Bin_Cla';
      tipo = 'modificar';
      consulta_solicitud_id(id_solicitud);
      configurar_form_modificar('show');
    }

  });

  $("#form_solicitud_bienestar_mod").submit(() => {
    modificar_solicitud(tipo);
    return false;
  });

  $('#recibir_solicitud').click(() => {
    $("#modal_asignar_funcionario").modal();
    listar_funcionarios();

  });

  $('#negar_solicitud').click(() => {
    gestionar_solicitud(id_solicitud, 'Bin_Neg_E');
  });

  $("#ver_estados").click(function () {
    listar_estados(id_solicitud);
    $("#modal_listar_estados").modal();
  });

  $("#limpiar_filtros").click(() => {
    limpiar_filtros();
    verificarDisponibilidad();
  });

  $("#limpiar_filtros_sol").click(() => {
    limpiar_filtros_sol();
    $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/bienestar/exportar_solicitudes/${0}/${0}/${0}/${0}/${0}`);

    listar_solicitudes();
  });

  $("#btnfiltrar").click(() => {
    verificarDisponibilidad();
  });

  $("#btnfiltrar_sol").click(() => {
    let estrategia = $("#estrategia_filtro").val();
    let estado = $("#estado_filtro").val();
    let fecha = $("#fecha_filtro").val();
    let fecha2 = $("#fecha_filtro_2").val();
    estrategia = estrategia ? estrategia : 0;
    estado = estado ? estado : 'vacio';
    fecha = fecha ? fecha : 0;
    fecha2 = fecha2 ? fecha2 : 0;
    id = 0;
    listar_solicitudes();
    $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/bienestar/exportar_solicitudes/${id}/${estrategia}/${estado}/${fecha}/${fecha2}`);

  });

  $("#btn_buscar_persona").click(() => {
    container_activo = '#txt_nombre_persona';
    $("#txt_dato_buscar").val('');
    callbak_activo = (resp) => mostrar_persona_sele(resp);
    buscar_persona('', callbak_activo);
    $("#modal_buscar_persona").modal();
  });

  $("#asignar_funcionarios").click(() => { //NAV TEMATICAS
    filtro_funcionario = 1;
    sw = 'tematicas';
    $('#container_bloqueos_bib').addClass("oculto");
    $('#container_funcionarios_bib').addClass("oculto");
    $('#container_turnos_bib').removeClass("oculto");
    $("#tabla_tematicas .nombre_tabla").html("Tabla temáticas");
    listar_valor_parametro(121);
    $('.agregar_bloqueo').removeClass("active");
    $('#asignar_coordinadores').removeClass("active");
    $('#bloqueos').removeClass("active");
    $('#horario_funcionario').removeClass("active");
    $("#asignar_funcionarios").addClass("active");
    $(".agregar_tematica").show("fast");
  });

  $("#asignar_coordinadores").click(() => {
    filtro_funcionario = '';
    sw = 'programas';
    $('#container_bloqueos_bib').addClass("oculto");
    $('#container_funcionarios_bib').addClass("oculto");
    $('#container_turnos_bib').removeClass("oculto");
    $("#tabla_tematicas .nombre_tabla").html("Tabla programas");
    listar_valor_parametro(3);
    $('#asignar_funcionarios').removeClass("active");
    $('#bloqueos').removeClass("active");
    $('#horario_funcionario').removeClass("active");
    $("#asignar_coordinadores").addClass("active");
    $(".agregar_tematica").hide("fast");
  });

  $("#bloqueos").click(() => {
    $('#container_turnos_bib').addClass("oculto");
    $('#container_funcionarios_bib').addClass("oculto");
    $('#container_bloqueos_bib').removeClass("oculto");
    $("#tabla_bloqueos .nombre_tabla").html("Tabla Bloqueos");
    listar_bloqueos();
    $('#asignar_funcionarios').removeClass("active");
    $('#asignar_coordinadores').removeClass("active");
    $('#horario_funcionario').removeClass("active");
    $("#bloqueos").addClass("active");
    $(".agregar_tematica").hide("fast");
  });

  $("#horario_funcionario").click(() => {
    $('#container_turnos_bib').addClass("oculto");
    $('#container_bloqueos_bib').addClass("oculto");
    $('#container_funcionarios_bib').removeClass("oculto");
    listar_horarios_funcionarios();
    $('#asignar_funcionarios').removeClass("active");
    $('#asignar_coordinadores').removeClass("active");
    $("#bloqueos").removeClass("active");
    $("#horario_funcionario").addClass("active");
    $(".agregar_tematica").hide("fast");
  });

  $("#admin_solicitudes").click(() => {
    $("#modal_administracion").modal();
    id_tematica = '';
    filtro_funcionario = 1;
    fecha_inicio = '',
      fecha_fin = '';
    sw == 'tematicas';
  });

  $("#asignar_funcionario_tematica").click(function () {
    $("#form_buscar_persona").get(0).reset();
    callbak_activo = data => {
      swal({
        title: "Estas Seguro ?",
        text: `Esta seguro de guardar a ${data.nombre_completo}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
      },
        function (isConfirm) {
          if (isConfirm) {
            id_persona = data.id;
            guardar_funcionario_tematica(id_persona, id_tematica, data.identificacion);
          }
        }
      );

    };
    let data_activa = { filtro_funcionario, dato: "", id_tematica, fecha_inicio, fecha_fin };
    buscar_persona(data_activa, callbak_activo);
    $("#modal_buscar_persona").modal();
  });

  $("#form_guardar_horario").submit(() => {
    guardar_horario_funcionario();
    return false;
  });

  $("#ver_encuestas").click(() => {
    if (id_estado_sol != 'Bin_Tra_E' && id_estado_sol != 'Bin_Fin_E') MensajeConClase("La solicitud no ha sido tramitada aun.", "info", "Oops...");
    else {
      listar_encuestas(id_solicitud);
      $("#modal_encuesta_solicitud").modal();
    }
  });

  $("#ver_modificaciones").click(() => {
    listar_modificaciones(id_solicitud);
    $("#modal_modificaciones_solicitud").modal();
  });

  $(".agregar_tematica").click(() => {
    id_tematica = '';
    estrategia_sele = [];
    $("#estrategias_agregadas").empty().append("<option value=''>0 Estrategia(s) a Asignar</option>");
    $(".rec_sele").html("0");
    $("#modal_nuevo_valor").modal();
  });

  $("#form_guardar_valor_parametro").submit(() => {
    guardar_valor_parametro();
    return false;
  });

  $(".agregar_bloqueo").click(() => {
    $("#modal_nuevo_bloqueo").modal();
  });

  $("#form_guardar_bloqueo").submit(() => {
    guardar_bloqueo();
    return false;
  });

  $("#modal_solicitud_bienestar select[name='id_duracion']").change(async function () {
    let fecha = $("#modal_solicitud_bienestar input[name='fecha_inicio']").val();
    let duracion = $("#modal_solicitud_bienestar select[name='id_duracion']").val();
    let tematica = $("#modal_solicitud_bienestar select[name='id_tematica']").val();
    let bloqueo = 1;
    if (fecha && duracion){
      fechasDisponibilidad(fecha, duracion, bloqueo, tematica);
      const cargos = await get_tematicas_disponibles(fecha, duracion);
      pintar_datos_combo(cargos, '.cbxtematica', 'Seleccione Temática');
    }
  });

  $("#modal_solicitud_bienestar input[name='fecha_inicio']").change(async function () {
    let fecha = $("#modal_solicitud_bienestar input[name='fecha_inicio']").val();
    let duracion = $("#modal_solicitud_bienestar select[name='id_duracion']").val();
    let tematica = $("#modal_solicitud_bienestar select[name='id_tematica']").val();
    let bloqueo = 1;
    if (fecha && duracion){
      fechasDisponibilidad(fecha, duracion, bloqueo, tematica);
      const cargos = await get_tematicas_disponibles(fecha, duracion);
		  pintar_datos_combo(cargos, '.cbxtematica', 'Seleccione Temática');
    }
  });

  $("#modal_solicitud_bienestar select[name='id_tematica']").change(function () {
    let fecha = $("#modal_solicitud_bienestar input[name='fecha_inicio']").val();
    let duracion = $("#modal_solicitud_bienestar select[name='id_duracion']").val();
    let tematica = $("#modal_solicitud_bienestar select[name='id_tematica']").val();
    let bloqueo = 1;
    if (fecha && duracion && tematica) fechasDisponibilidad(fecha, duracion, bloqueo, tematica);
  });

  $("#modal_modificar_bienestar input[name='fecha_inicio']").change(function () {
    let fecha = $("#modal_modificar_bienestar input[name='fecha_inicio']").val();
    let duracion = $("#modal_modificar_bienestar select[name='id_duracion']").val();
    let tematica = id_tematica;
    let bloqueo = 1;
    if (fecha && duracion) fechasDisponibilidad(fecha, duracion, bloqueo, tematica);
  });

  $("#modal_modificar_bienestar select[name='id_duracion']").change(function () {
    let fecha = $("#modal_modificar_bienestar input[name='fecha_inicio']").val();
    let duracion = $("#modal_modificar_bienestar select[name='id_duracion']").val();
    let tematica = id_tematica;
    let bloqueo = 1;
    if (fecha && duracion) fechasDisponibilidad(fecha, duracion, bloqueo, tematica);
  });


  $(".mas_estrategias").click(() => {
    let id_parametro = 120;
    listar_estrategias_disponibles(id_parametro, id_tematica);
  });

  $("#Guardar_mas_estrategia").click(() => {
    $("#Modal_seleccionar_estrategia").modal("hide");
  });

  $("#retirar_estrategia_sele").click(function () {
    retirar_estrategia_sele(".estrategias_agregados", 1);
  });

  $("#retirar_estrategia_sele_modi").click(function () {
    retirar_estrategia_sele(".estrategias_agregados_modi", 2);
  });

  $(".btn_horario").click(() => {
    id_horario = '';
    $(".titulo_modal").html("Nuevo Horario");
    $("#form_guardar_horario").get(0).reset();
    $("#modal_crear_horario").modal();
  });

  $("#asignar_funcionario_horario").click(function () {
    $("#form_buscar_persona").get(0).reset();
    callbak_activo = data => {
      swal({
        title: "Estas Seguro ?",
        text: `Esta seguro de guardar a ${data.nombre_completo}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Si, Entiendo!",
        cancelButtonText: "No, Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true
      },
        function (isConfirm) {
          if (isConfirm) {
            id_persona = data.id;
            guardar_funcionario_horario(id_persona, id_horario);

          }
        }
      );

    };
    let data_activa = { filtro_funcionario: 1, dato: "" };
    buscar_persona(data_activa, callbak_activo);
    $("#modal_buscar_persona").modal();
  });

  $("#form_modificar_tematica").submit(() => {
    modificar_tematica();
    return false;
  });

});

const administrar_modulo = tipo => {
  if (tipo == 'listado_solicitudes') {
    listar_solicitudes();
    $("#menu_principal").css("display", "none");
    $("#listar_solicitudes").fadeIn();
  } else if (tipo == 'regresar_menu') {
    $("#listar_solicitudes").css("display", "none");
    $("#menu_principal").fadeIn(1000);
  } else if (tipo == 'solicitud_bienestar') {
    id_tipo_solicitud = 'Bin_Cla';
    estudiantes = [];
    listar_estudiantes();
    $("#modal_solicitud_bienestar").modal();
    $("#form_solicitud_bienestar").get(0).reset();
    $(".detalle_solicitud").fadeOut('slow');
    $(".estudiantes_solicitud").fadeOut('slow');
    $(".agregar_estudiantes").fadeOut('slow');
    $(".noDisponible").hide();

  }
}


const pintar_datos_combo = (datos, combo, mensaje, sele = '') => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(elemento => {
    $(combo).append(`<option value='${elemento.id}' data-grupo="${elemento.cod_grupo}"> ${elemento.valor}</option>`);
  });
  $(combo).val(sele);
}

const listar_ubicaciones = async (id_lugar, container, id_ubicacion = '') => {
  let ubicaciones = await obtener_ubicaciones(id_lugar);
  pintar_datos_combo(ubicaciones, container, 'Seleccione Ubicación / Salon', id_ubicacion);
}

const obtener_ubicaciones = (id_lugar) => {
  return new Promise(resolve => {
    let url = `${ruta}listar_ubicaciones`;
    consulta_ajax(url, { id_lugar }, (resp) => {
      resolve(resp);
    });
  });
}

const get_tematicas_disponibles = (fecha, duracion) => {
  return new Promise(resolve => {
    let url = `${ruta}get_tematicas_disponibles`;
    consulta_ajax(url, { fecha, duracion }, (resp) => {
      resolve(resp);
    });
  });
}
const listar_estrategias = async (id_tematica, container, id_estrategia = '') => {
  let estrategias = await obtener_estrategias(id_tematica);
  pintar_datos_combo(estrategias, container, 'Seleccione Estrategia PASPE', id_estrategia);
}

const obtener_estrategias = (id_estrategia, filtro = '') => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_estrategias`;
    consulta_ajax(url, { id_estrategia, filtro }, (resp) => {
      resolve(resp);
    });
  });
}

const guardar_solicitud = () => {
  MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
  let fordata = new FormData(document.getElementById("form_solicitud_bienestar"));
  let data = formDataToJson(fordata);
  data.id_anterior = id_anterior;
  data.semestre = semestre;
  data.programa = programa;
  data.cod_programa = cod_programa;
  data.id_programa_sol = id_programa_sol;
  data.solicitante_anterior = solicitante_anterior;
  data.cant_estudiantes = cant_estudiantes;
  data.estudiantes = estudiantes;
  data.cod_materia = cod_materia;
  data.materia_grupo = materia_grupo;
  data.id_tipo_solicitud = id_tipo_solicitud;
  consulta_ajax(`${ruta}guardar_solicitud`, data, (resp) => {
    let { titulo, mensaje, tipo, solicitante, correo, fecha, disponibilidad, tipo_solicitud, solicitud_id } = resp;
    if (tipo == 'success') {
      if (reprogramado) enviar_correo(tipo_solicitud, '', 'Reprogramado', solicitante, correo, '', fecha, '', solicitud_id);
      // notificar_funcionario_asignado(solicitud_id);
      listar_solicitudes();
      listar_estudiantes();
      $("#form_solicitud_bienestar").get(0).reset();
      $("#modal_solicitud_bienestar").modal('hide');
      $(".detalle_solicitud").fadeOut('fast');
      $(".estudiantes_solicitud").fadeOut('fast');
      $(".agregar_estudiantes").fadeOut('fast');
      MensajeConClase(mensaje, tipo, titulo);
      $(".noDisponible").hide();

    } else if (disponibilidad) {
      $(".noDisponible").show();
      //     listar_disponibilidad(disponibilidad, data.fecha_inicio, data.id_duracion);
      //     // listar_disponibilidad_bloqueo(disponibilidad);
      //     $("#modal_disponibilidad_bloqueo").modal();   
      MensajeConClase(mensaje, tipo, titulo);
    } else {
      $(".noDisponible").hide();
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const listar_solicitudes = (id = '') => {
  solicitante_anterior = '';
  reprogramado = false;
  $("#exportar_solicitudes").attr("href", `${Traer_Server()}index.php/bienestar/exportar_solicitudes/${0}/${0}/${0}/${0}/${0}`);
  let estrategia = $("#estrategia_filtro").val();
  let estado = $("#estado_filtro").val();
  let fecha = $("#fecha_filtro").val();
  let fecha_2 = $("#fecha_filtro_2").val();

  $('#tabla_solicitudes tbody')
    .off('dblclick', 'tr')
    .off('click', 'tr')
    .off('click', 'tr td:nth-of-type(1)')
    .off('click', 'tr .cancelar')
    .off('click', 'tr .disponibilidad')
    .off('click', 'tr .reprogramar')
    .off('click', 'tr .tramitar')
    .off('click', 'tr .asistencia')
    .off('click', 'tr .finalizar')
    .off('click', 'tr .negar')
    .off('click', 'tr .modificar');
  consulta_ajax(`${ruta}listar_solicitudes`, { id, estrategia, estado, fecha, fecha_2, id_programa_sol }, (resp) => {
    const table = $("#tabla_solicitudes").DataTable({
      "destroy": true,
      "processing": true,
      "data": resp,
      columns: [{
        "data": "ver"
      },
      {
        "render": function (data, type, full, meta) {
          let { funcionario } = full;
          let show = '---';
          if (funcionario) show = funcionario;
          else show;
          return show;
        }
      },
      {
        "render": function (data, type, full, meta) {
          let { dia_semana } = full;
          let dia = '';
          if (dia_semana == 1) dia = 'Domingo';
          else if (dia_semana == 2) dia = 'Lunes';
          else if (dia_semana == 3) dia = 'Martes';
          else if (dia_semana == 4) dia = 'Miercoles';
          else if (dia_semana == 5) dia = 'Jueves';
          else if (dia_semana == 6) dia = 'Viernes';
          else if (dia_semana == 7) dia = 'Sabado';
          return dia;
        }
      },
      {
        "data": "fecha_inicio"
      },
      {
        "data": "duracion"
      },
      {
        "data": "tematica"
      },
      {
        "data": "solicitante"
      },
      {
        "data": "fecha_registra"
      },
      {
        "data": "estado_sol"
      },
      {
        "data": "accion"
      },

      ],
      "language": get_idioma(),
      'dom': 'Bfrtip',
      "buttons": get_botones(),
    });

    //EVENTOS DE LA TABLA ACTIVADOS

    $('#tabla_solicitudes tbody').on('click', 'tr', function () {
      let { id, id_estado_sol: id_estado, tipo_solicitud, solicitante: solicit, correo, id_tematica: it, id_solicitante: is } = table.row(this).data();
      id_solicitud = id;
      id_estado_sol = id_estado;
      tipo_solicitud = tipo_solicitud;
      solicitante = solicit;
      correo_solicitante = correo;
      id_tematica = it;
      id_solicitante = is;
      $("#exportar_encuestas").attr("href", `${Traer_Server()}index.php/bienestar/exportar_encuestas/${id_solicitud}`);
      $("#tabla_solicitudes tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
      let data = table.row(this).data();
      ver_detalle_solicitud(data);
    });

    $('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = table.row($(this).parent()).data();
      id_solicitud = data.id;
      id_estado_sol = data.id_estado_sol;
      id_programa_sol = data.id_programa;
      solicitante = data.id_solicitante;
      fecha_inicio = data.fecha_inicio;
      fecha_fin = data.fecha_fin;
      id_tematica = data.id_tematica;
      filtro_funcionario = 2;
      ver_detalle_solicitud(data);
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .cancelar', async function () {
      let { id, id_usuario_registra } = table.row($(this).parent()).data();
      let { persona, perfil } = await data_sesion();
      if (perfil == 'Per_Admin' || perfil == 'Per_Bin') gestionar_solicitud(id, 'Bin_Can_E');
      else if (id_usuario_registra == persona) gestionar_solicitud(id, 'Cancelar_Docente');
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .disponibilidad', function () {
      let { fecha_inicio: fi, fecha_fin: ff, id_tematica: it, dia_semana } = table.row($(this).parent()).data();
      fecha_inicio = fi;
      fecha_fin = ff;
      funcionarios = [];
      id_tematica = it;
      gestionar_solicitud(id, 'Verificacion');
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .reprogramar', function () {
      let { id, id_estado_sol: estado } = table.row($(this).parent()).data();
      // gestionar_solicitud(id, 'Bin_Rep_E');
      tipo = 'reprogramar';
      consulta_solicitud_id(id);
      configurar_form_modificar('show');


    });

    $('#tabla_solicitudes tbody').on('click', 'tr .tramitar', function () {
      let { id } = table.row($(this).parent()).data();
      gestionar_solicitud(id, 'Bin_Tra_E');
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .asistencia', function () {
      let { id, tipo_solicitud: tipo } = table.row($(this).parent()).data();
      tipo_solicitud = tipo;
      id_solicitud = id;
      window.location.replace(`${Traer_Server()}index.php/bienestar/asistencia/${id_solicitud}`);
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .finalizar', function () {
      let { id } = table.row($(this).parent()).data();
      gestionar_solicitud(id, 'Bin_Fin_E');
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .modificar', function () {
      let { id, id_tematica } = table.row($(this).parent()).data();
      $('#id_tematica').val(id_tematica);
      $('#modal_modificar_tematica').modal();
    });

    $('#tabla_solicitudes tbody').on('click', 'tr .negar', function () {
      let { id, } = table.row($(this).parent()).data();
      gestionar_solicitud(id, 'Bin_Neg_E');
    });
  });
}

const modificar_tematica = () => {
  let data = new FormData(document.getElementById("form_modificar_tematica"));
  data.append("id_solicitud", id_solicitud);
  enviar_formulario(`${ruta}modificar_tematica`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      listar_solicitudes();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const ver_detalle_solicitud = (data) => {
  let {
    solicitante,
    dia_semana,
    duracion,
    departamento,
    lugar,
    estrategia,
    programa,
    semestre,
    tematica,
    fecha_registra,
    fecha_inicio,
    fecha_fin,
    cant_estudiantes,
    ubicacion,
    materia,
    coordinador,
    motivo,
    telefono
  } = data;

  $(".solicitante").html(solicitante);
  $(".materia").html(materia);
  $(".duracion").html(duracion);
  $(".departamento").html(departamento);
  $(".lugar").html(lugar);
  $(".estrategia").html(estrategia);
  $(".programa").html(programa);
  $(".tematica").html(tematica);
  $(".ubicacion").html(ubicacion);
  $(".fecha_registra").html(fecha_registra);
  $(".fecha_inicio").html(fecha_inicio);
  $(".fecha_fin").html(fecha_fin);
  $(".semestre_estudiantes").html(semestre);
  $(".cantidad_estudiantes").html(cant_estudiantes);
  $(".motivo").html(motivo);
  $(".coordinador").html(coordinador);
  $(".telefono").html(telefono);

  if (id_estado_sol == 'Bin_Neg_E' || motivo) $(".negado").show();
  else $(".negado").hide();

  let dia = '';
  if (dia_semana == 1) dia = 'Domingo';
  else if (dia_semana == 2) dia = 'Lunes';
  else if (dia_semana == 3) dia = 'Martes';
  else if (dia_semana == 4) dia = 'Miercoles';
  else if (dia_semana == 5) dia = 'Jueves';
  else if (dia_semana == 6) dia = 'Viernes';
  else if (dia_semana == 7) dia = 'Sabado';
  $(".dia_duracion").html(dia + ' / ' + duracion);

  $("#modal_detalle_solicitud").modal();
}

const obtener_materias_por_docente = async (id) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_materias_por_docente`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}
const obtener_coordinadores_por_programa = async (id) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_coordinadores_por_programa`;
    consulta_ajax(url, { id }, resp => {
      resolve(resp);
    });
  });
}

const obtener_estudiantes_por_materia = async (materia) => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_estudiantes_por_materia`;
    consulta_ajax(url, { materia }, resp => {
      resolve(resp);
      swal.close();
    });
  });
}

const pintar_materias_docente = async (id, cod_materia = "") => {
  let materias = await obtener_materias_por_docente(id);
  materias_array = materias;
  id_programa = materias.codigo_programa;
  pintar_datos_combo(materias, "#form_solicitud_bienestar select[name=id_materia]", "Seleccione Materia", cod_materia);

}

const listar_estudiantes = container => {
  $("#tabla_estudiantes tbody").off("click", "tr").off("click", "tr .eliminar");
  let i = 0;
  const myTable = $("#tabla_estudiantes").DataTable({
    destroy: true,
    searching: false,
    processing: true,
    data: estudiantes,
    columns: [{
      render: function (data, type, full, meta) {
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
      defaultContent: `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`
    }
    ],
    language: idioma,
    dom: "Bfrtip",
    buttons: []
  });

  //EVENTOS DE LA TABLA ACTIVADOS
  $("#tabla_estudiantes tbody").on("click", "tr", function () {
    $("#tabla_estudiantes tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });
  $("#tabla_estudiantes tbody").on("click", "tr .eliminar", function () {
    let data = myTable.row($(this).parent().parent()).data();
    estudiantes.forEach((key, indice) => {
      if (key.id == data.id) {
        if (estudiantes.length <= 1) {
          swal("Oops", "Debe tener por lo menos un estudiante", "info");
          return;
        } else {
          estudiantes.splice(indice, 1);
          return;
        }
      }
    });
    cant_estudiantes = estudiantes.length;
    $(".estudiantes").html('Se esperan ' + cant_estudiantes + ' estudiantes en la clase.');
    listar_estudiantes();
  });
};

const buscar_estudiante = (dato, callbak) => {
  consulta_ajax(`${ruta}buscar_estudiante`, { dato }, resp => {
    $(`#tabla_estudiantes_busqueda tbody`).off("click", "tr td .estudiante").off("dblclick", "tr").off("click", "tr").off("click", "tr td:nth-of-type(1)");
    let i = 0;
    const myTable = $("#tabla_estudiantes_busqueda").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
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
        defaultContent: '<span style="color: #39B23B;" title="Seleccionar Estudiante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default estudiante" ></span>'
      }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });
    $("#tabla_estudiantes_busqueda tbody").on("click", "tr", function () {
      $("#tabla_estudiantes_busqueda tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_estudiantes_busqueda tbody").on("dblclick", "tr", function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });
    $("#tabla_estudiantes_busqueda tbody").on("click", "tr td .estudiante", function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
      listar_estudiantes();
    });
  });
}

const listar_estudiantes_solicitud = (id, asistencia_ = '') => {
  MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
  id_solicitud = id;
  $("#tabla_estudiantes_solicitud tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .firmar").off("click", "tr td:nth-of-type(1)");
  consulta_ajax(`${ruta}listar_estudiantes_solicitud`, { id, asistencia_ }, (resp) => {
    $(".cantidad_estudiantes").html(resp.length);
    let i = 0;
    const myTable = $("#tabla_estudiantes_solicitud").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
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
        data: "accion"
      },

      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: [],

    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $("#tabla_estudiantes_solicitud tbody").on("click", "tr", function () {
      $("#tabla_estudiantes_solicitud tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $("#tabla_estudiantes_solicitud tbody").on("click", "tr .eliminar", function () {
      let { id, id_solicitud } = myTable.row($(this).parent().parent()).data();
      eliminar_estudiante_solicitud(id, id_solicitud);
    });

    $('#tabla_estudiantes_solicitud tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let { id, id_solicitud, realizo } = myTable.row($(this).parent()).data();
      if (realizo) ver_detalle_encuesta(id, id_solicitud);
    });

    $('#tabla_estudiantes_solicitud tbody').on('click', 'tr .firmar', function () {
      let { id, correo, nombre_completo, tematica } = myTable.row($(this).parent()).data();
      if (correo != null) {
        id_estudiante = id;
        nombre_estudiante = nombre_completo;
        correo_estudiante = correo;
        tematica_actividad = tematica;
        usuario = correo.replace(/\@.*/, '').trim();
        $("#form_logear").get(0).reset();
        $('#modal_logear').modal();
      } else {
        mensaje = "Error al confirmar asistencia, contacte con el administrador";
        tipo = "info";
        titulo = "Oops.!";
        MensajeConClase(mensaje, tipo, titulo);
      }
    });
    swal.close();
    pintar_modal();
  });
}

function pintar_modal() {
  $("#modal_estudiantes_solicitud .modal-title").html('<span class="fa fa-users"></span> ESTUDIANTES');
  $("#modal_estudiantes_solicitud .nombre_tabla").html('TABLA DE ESTUDIANTES');
  administrar_vista('estudiantes');
}

const eliminar_estudiante_solicitud = (id, id_solicitud) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar el estudiante presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_estudiante_solicitud`, { id, id_solicitud }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_estudiantes_solicitud(id_solicitud);
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });

}
const guardar_estudiante_nuevo = (id_persona, id_solicitud, identificacion) => {
  data = { id_persona, id_solicitud, identificacion };
  consulta_ajax(`${ruta}guardar_estudiante_nuevo`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      let table = $("#tabla_estudiantes_busqueda").DataTable();
      table.clear().draw();
      swal.close();
      listar_estudiantes_solicitud(id_solicitud);
      listar_solicitudes();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const guardar_funcionario_nuevo = (id_persona, id_solicitud, identificacion) => {
  data = { id_persona, id_solicitud, identificacion };
  consulta_ajax(`${ruta}guardar_funcionario_nuevo`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      let table = $("#tabla_funcionarios_solicitud").DataTable();
      table.clear().draw();
      swal.close();
      // notificar_funcionario_asignado(id_solicitud);
      listar_funcionarios_solicitud(id_solicitud);
      buscar_persona();
      // $('#modal_buscar_persona').hide();
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const consulta_solicitud_id = id => {
  $("#form_solicitud_bienestar_mod").get(0).reset();

  consulta_ajax(`${ruta}consulta_solicitud_id`, { id }, async (resp) => {
    let {
      fecha_i,
      id_lugar,
      id_ubicacion,
      id_estrategia,
      id_tematica,
      id_duracion,
      telefono,
      id_coordinador,
      programa_id,
    } = resp

    if (id_estado_sol == 'Bin_Tra_E') {
      $("#modal_modificar_bienestar input[name='fecha_inicio']").val(fecha_i);
      $("#modal_modificar_bienestar select[name='id_duracion']").val(id_duracion);

    } else {
      $("#modal_modificar_bienestar input[name='fecha_inicio']").val(fecha_i);
      $("#modal_modificar_bienestar select[name='id_duracion']").val(id_duracion);
      $("#modal_modificar_bienestar select[name='id_lugar']").val(id_lugar).trigger('change', id_ubicacion);
      $("#modal_modificar_bienestar select[name='id_ubicacion']").val(id_ubicacion);
      $("#modal_modificar_bienestar select[name='id_tematica']").val(id_tematica).trigger('change', id_estrategia);
      $("#modal_modificar_bienestar select[name='id_estrategia']").val(id_estrategia);
      $("#modal_modificar_bienestar input[name='telefono']").val(telefono);
      let coordinadores_mod = await obtener_coordinadores_por_programa(programa_id);
      pintar_datos_combo(coordinadores_mod, '.cbxcoordinador_mod', 'Seleccione Coordinador');
      $("#modal_modificar_bienestar select[name='id_coordinador_mod']").val(id_coordinador);
    }


    $("#modal_modificar_bienestar").modal();
  });
}

const modificar_solicitud = tipo => {
  let fordata = new FormData(document.getElementById("form_solicitud_bienestar_mod"));
  let data = formDataToJson(fordata);
  data.id_solicitud = id_solicitud;
  data.id_tipo_solicitud = id_tipo_solicitud;
  data.id_estado_solicitud = id_estado_sol;
  data.tipo = tipo;
  consulta_ajax(`${ruta}modificar_solicitud`, data, async (resp) => {
    let { titulo, mensaje, tipo, motivos, tipo_solicitud, disponibilidad } = resp;
    if (tipo == 'success') {
      listar_solicitudes();
      let { perfil } = await data_sesion();
      if (perfil == 'Per_Admin' || perfil == 'Per_Bin')
        enviar_correo(tipo_solicitud, '', 'mod', solicitante, correo_solicitante, '', motivos, '', id_solicitud);
      // notificar_funcionario_asignado(id_solicitud, motivos);
      $("#form_solicitud_bienestar_mod").get(0).reset();
      $("#modal_modificar_bienestar").modal("hide");

    } else if (tipo == 'no_disponible') {
      $(".noDisponible").show();
      listar_disponibilidad(disponibilidad, data.fecha_inicio, data.id_duracion);
      $("#modal_disponibilidad_bloqueo").modal();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const gestionar_solicitud = (id, estado, funcionarios = '') => {
  const gestionar_solicitud_normal = (id, estado, title = '¿ Cancelar Solicitud ?') => {
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
          ejecutar_gestion(id, estado, funcionarios);
          $("#modal_asignar_funcionario").modal('hide');
          $("#modal_disponibilidad").modal('hide');
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

  const gestionar_solicitud_copia = (id, estado, title = '¿ Copiar Solicitud ?') => {
    swal({
      title,
      text: "Si desea continuar debe presionar la opción de 'Si, Entiendo'!",
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
          id_anterior = id;
          copiar_solicitud(id);
        }
      });
  }

  const ejecutar_gestion = (id, estado, mensaje_) => {
    consulta_ajax(`${ruta}gestionar_solicitud`, { id, estado, 'mensaje': mensaje_, funcionarios }, (resp) => {
      let { titulo, mensaje, tipo, tipo_solicitud } = resp;
      if (tipo == 'success') {
        swal.close();
        listar_solicitudes();
        $("#modal_disponibilidad").modal("hide");
        if (estado == 'Bin_Rev_E') {
          // enviar_correo(tipo_solicitud, '', estado, funcionario_nombre, funcionario_correo, '', '', 'Funcionario', id_solicitud);
          enviar_correo(tipo_solicitud, '', estado, solicitante, correo_solicitante, '', '', 'Docente', id_solicitud);
        } else if (estado == 'Bin_Neg_E' || estado == 'Bin_Can_E') {
          enviar_correo(tipo_solicitud, '', estado, solicitante, correo_solicitante, '', mensaje_, '', id_solicitud);
        } else if (estado == 'Cancelar_Docente') {
          enviar_correo(tipo_solicitud, '', estado, 'Bienestar Estudiantil', 'bestudiantil1@cuc.edu.co', '', mensaje_, '', id_solicitud);
        }
      } else {
        listar_solicitudes();
        MensajeConClase(mensaje, tipo, titulo);
      }
    });
  }

  if (estado == 'Verificacion') {
    verificarDisponibilidad();
  } else if (estado == 'Bin_Neg_E') {
    gestionar_solicitud_texto(id, estado);
  } else if (estado == 'Bin_Can_E') {
    gestionar_solicitud_texto(id, estado, '¿ Cancelar Solicitud ?');
  } else if (estado == 'Cancelar_Docente') {
    gestionar_solicitud_texto(id, estado, '¿ Cancelar Solicitud ?');
  } else if (estado == 'Bin_Rev_E') {
    gestionar_solicitud_normal(id, estado, '¿ Esta Seguro ?');
  } else if (estado == 'Bin_Rep_E') {
    gestionar_solicitud_copia(id, estado, '¿ Reprogramar Solicitud ?');
  } else if (estado == 'Bin_Tra_E') {
    gestionar_solicitud_normal(id, estado, '¿ Tramitar Solicitud ?');
  } else if (estado == 'Bin_Fin_E') {
    gestionar_solicitud_normal(id, estado, '¿ Finalizar Solicitud ?');
  }

}

const configurar_fechas = (dias = 0, container = '.datetime_bienestar', habiles = [0, 6]) => {
  let startDate = new Date();
  startDate.setDate(startDate.getDate() + dias);
  $(container).datetimepicker({
    format: 'yyyy-mm-dd hh:ii:00',
    autoclose: true,
    minuteStep: 30,
    startDate,
    todayBtn: false,
    daysOfWeekDisabled: habiles,
  });
}

const configurar_horas = (container) => {
  $(container).datetimepicker({
    formatViewType: 'time',
    fontAwesome: true,
    autoclose: true,
    startView: 1,
    maxView: 1,
    minView: 0,
    minuteStep: 30,
    format: 'hh:ii',
  });
}
const verificarDisponibilidad = () => {
  let estrategia = $("#estado_estrategia").val();
  let funcionario = (datos_persona == null) ? '' : datos_persona.id;

  $("#modal_disponibilidad").modal();
  consulta_ajax(`${ruta}verificarDisponibilidad`, { fecha_inicio, fecha_fin, id_solicitud, estrategia, funcionario }, resp => {
    $(`#tabla_disponibilidad tbody`).off("click", "tr td .estudiante").off("dblclick", "tr").off("click", "tr").off("click", "tr td:nth-of-type(1)");
    const myTable = $("#tabla_disponibilidad").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        "data": "ver"
      },
      {
        "data": "funcionario"
      },
      {
        "render": function (data, type, full, meta) {
          let { dia_semana } = full;
          let dia = '';
          if (dia_semana == 1) dia = 'Domingo';
          else if (dia_semana == 2) dia = 'Lunes';
          else if (dia_semana == 3) dia = 'Martes';
          else if (dia_semana == 4) dia = 'Miercoles';
          else if (dia_semana == 5) dia = 'Jueves';
          else if (dia_semana == 6) dia = 'Viernes';
          else if (dia_semana == 7) dia = 'Sabado';
          return dia;
        }
      },
      {
        "data": "fecha_inicio"
      },
      {
        "data": "duracion"
      },
      {
        "data": "tematica"
      },
      {
        "data": "solicitante"
      },
      {
        "data": "fecha_registra"
      },

      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });
    $("#tabla_disponibilidad tbody").on("click", "tr", function () {
      $("#tabla_disponibilidad tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $('#tabla_disponibilidad tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row($(this).parent()).data();
      ver_detalle_solicitud(data);
    });
  });
}

const buscar_persona = (data, callbak) => {

  consulta_ajax(`${ruta}buscar_persona`, data, resp => {
    $(`#tabla_personas_busqueda tbody`).off("click", "tr td .funcionario").off("dblclick", "tr").off("click", "tr").off("click", "tr td:nth-of-type(1)");
    let i = 0;
    const myTable = $("#tabla_personas_busqueda").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
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
        defaultContent: '<span style="color: #39B23B;" title="Seleccionar Funcionario" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default funcionario" ></span>'
      }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });
    $("#tabla_personas_busqueda tbody").on("click", "tr", function () {
      $("#tabla_personas_busqueda tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_personas_busqueda tbody").on("dblclick", "tr", function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
    });
    $("#tabla_personas_busqueda tbody").on("click", "tr td .funcionario", function () {
      let data = myTable.row($(this).parent().parent()).data();
      callbak(data);
      funcionario_nombre = data.nombre_completo;
      funcionario_correo = data.correo;
      listar_funcionarios();

    });
  });
}
const listar_funcionarios = () => {
  $("#tabla_funcionarios tbody").off("click", "tr").off("click", "tr .eliminar");
  let i = 0;
  const myTable = $("#tabla_funcionarios").DataTable({
    destroy: true,
    searching: false,
    processing: true,
    data: funcionarios,
    columns: [{
      render: function (data, type, full, meta) {
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
      defaultContent: `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`
    }
    ],
    language: idioma,
    dom: "Bfrtip",
    buttons: []
  });

  //EVENTOS DE LA TABLA ACTIVADOS
  $("#tabla_funcionarios tbody").on("click", "tr", function () {
    $("#tabla_funcionarios tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });
  $("#tabla_funcionarios tbody").on("click", "tr .eliminar", function () {
    let data = myTable.row($(this).parent().parent()).data();
    funcionarios.forEach((key, indice) => {
      if (key.id == data.id) {
        if (funcionarios.length <= 1) {
          swal("Oops", "Debe tener por lo menos un funcionario", "info");
          return;
        } else {
          funcionarios.splice(indice, 1);
          return;
        }
      }
    });
    listar_funcionarios();
  });
};

const copiar_solicitud = id => {

  consulta_ajax(`${ruta}consulta_solicitud_id`, { id }, async (resp) => {
    let {
      fecha_i,
      fecha_f,
      cod_materia,
      id_lugar,
      id_ubicacion,
      id_tematica,
      id_solicitante,
      id_duracion,
      telefono,
      id_coordinador,
    } = resp

    //pintar_materias_docente(43, cod_materia);
    pintar_materias_docente(id_solicitante, cod_materia);
    solicitante_anterior = id_solicitante;
    reprogramado = true;

    $("#modal_solicitud_bienestar input[name='telefono']").val(telefono);
    $("#modal_solicitud_bienestar input[name='fecha_inicio']").val(fecha_i);
    $("#modal_solicitud_bienestar input[name='fecha_fin']").val(fecha_f);
    $("#modal_solicitud_bienestar select[name='id_materia']").val(cod_materia).trigger("change");
    $("#modal_solicitud_bienestar select[name='id_lugar']").val(id_lugar).trigger('change', id_ubicacion);
    $("#modal_solicitud_bienestar select[name='id_ubicacion']").val(id_ubicacion);
    $("#modal_solicitud_bienestar select[name='id_tematica']").val(id_tematica);
    $("#modal_solicitud_bienestar select[name='id_duracion']").val(id_duracion);
    $("#modal_solicitud_bienestar select[name='id_coordinador']").val(id_coordinador);

    $("#modal_solicitud_bienestar").modal();
  });
}



const agregar_funcionarios_nuevos = (id) => {
  consulta_ajax(`${ruta}agregar_funcionarios_nuevos`, id, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {

    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}


const listar_funcionarios_solicitud = (id = '') => {
  $('#tabla_funcionarios_solicitud tbody').off('click', 'tr').off('click', 'tr .eliminar');
  consulta_ajax(`${ruta}listar_funcionarios_solicitud`, { id }, (resp) => {
    let i = 0;
    const myTable = $("#tabla_funcionarios_solicitud").DataTable({

      destroy: true,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
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
        "render": function (data, type, full, meta) {
          if (id_estado_sol == 'Bin_Sol_E' || id_estado_sol == 'Bin_Rev_E' || id_estado_sol == 'Bin_Rep_E') return `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`
          else return `<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off pointer"></span>`
        }
      }
      ],
      "language": get_idioma(),
      'dom': 'Bfrtip',
      "buttons": get_botones(),
    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $("#tabla_funcionarios_solicitud tbody").on("click", "tr", function () {
      $("#tabla_funcionarios_solicitud tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_funcionarios_solicitud tbody").on("click", "tr .eliminar", function () {
      let { id, id_solicitud } = myTable.row($(this).parent().parent()).data();
      eliminar_funcionario_solicitud(id, id_solicitud);
      listar_funcionarios();
    });
  });
}

const eliminar_funcionario_solicitud = (id, id_solicitud) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar al al funcionario presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_funcionario_solicitud`, { id, id_solicitud }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_funcionarios_solicitud(id_solicitud);
          listar_solicitudes();
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });

}


const listar_estados = id_solicitud => {
  const link = `${ruta}listar_estados`;
  consulta_ajax(link, { id_solicitud }, data => {
    $('#tabla_estados_solicitud  tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
    let i = 0;
    const myTable = $("#tabla_estados_solicitud").DataTable({
      destroy: true,
      data,
      processing: true,
      searching: false,
      columns: [
        { "render": function (data, type, full, meta) { i++; return i; } },
        { data: 'fecha' },
        { data: 'persona' },
        { data: 'estado' },
      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $('#tabla_estados_solicitud tbody').on('click', 'tr', function () {
      $("#tabla_estados_solicitud tbody tr").removeClass("warning");
      $(this).addClass("warning");
      let { id } = myTable.row(this).data();
      let { state } = myTable.row(this).data();
      estado_solicitud = state;
      id_solicitud = id;
    });

    $('#tabla_estados_solicitud tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row($(this).parent()).data();
      modal_detalle_factura(data);
    });
  });
}

const limpiar_filtros = () => {
  $("#estado_estrategia").val('');
  $("#txt_nombre_persona").val('');
  datos_persona = [];
}

const limpiar_filtros_sol = () => {
  $("#estrategia_filtro").val('');
  $("#estado_filtro").val('');
  $("#fecha_filtro").val('');
  $("#fecha_filtro_2").val('');
}

const mostrar_persona_sele = data => {
  let { id, nombre_completo } = data;
  datos_persona = { id, nombre_completo }
  $(container_activo).val(nombre_completo);
  $("#modal_buscar_persona").modal('hide');
}

const guardar_encuesta = (code) => {
  let fordata = new FormData(document.getElementById("form_encuesta"));
  let data = formDataToJson(fordata);
  data.codigo = code;
  consulta_ajax(`${ruta}guardar_encuesta`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      window.location.replace(`${Traer_Server()}index.php/bienestar/encuesta/encuesta_enviada`);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });

}

const ver_detalle_encuesta = (id, id_solicitud) => {

  consulta_ajax(`${ruta}ver_detalle_encuesta`, { id, id_solicitud }, (resp) => {

    let {
      participacion,
      actividad,
      servicio,
      apropiado,
      integral,
      metodología,
      otros,
      estudiante,
    } = resp;

    $(".participacion").html(participacion);
    $(".actividad").html(actividad);
    $(".servicio").html(servicio);
    $(".apropiado").html(apropiado);
    $(".integral").html(integral);
    $(".metodología").html(metodología);
    $(".otros").html(otros);
    $(".estudiante").html(estudiante);
    $(".ubicacion").html(ubicacion);

    $("#modal_detalle_encuesta").modal();
  });
}

const logear = (user, pass) => {
  consulta_ajax(`${ruta}logear`, { user, pass }, (resp) => {
    let { correo } = resp;
    usuario = correo.replace(/\@.*/, '').trim();
  });

}

const verificar_firma = tipo_vista => {
  let fordata = new FormData(document.getElementById("form_logear"));
  let data = formDataToJson(fordata);
  data.usuario = usuario;
  data.id_estudiante = id_estudiante;
  data.id_solicitud = id_solicitud;
  consulta_ajax(`${ruta}verificar_firma`, data, (resp) => {
    let { titulo, mensaje, tipo, codigo, tipo_solicitud: tipo_s, id_estado_sol: id_estado, nombre_completo, correo, tematica } = resp;
    if (tipo == 'success') {
      if (tipo_vista == 'asistencia') {
        enviar_correo(tipo_s, codigo, id_estado, nombre_completo, correo, tematica);
        listar_estudiantes_solicitud(id_solicitud, 'si');
        $("#modal_logear").modal('hide');
      } else {
        $("#form_logear_encuesta").submit();
      }
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const enviar_correo = (tipo_solicitud, codigo, estado_solicitud, nombre_completo, correo, tematica, motivo = '', tipo_ = '', id = "") => {

  let sw = false;
  let ser = `<a href="${Traer_Server()}index.php/bienestar/encuesta/ingresar/${codigo}"><b>agil.cuc.edu.co</b></a>`;
  let tipo = -1;
  // let titulo = `Bienestar - Bienestar a tu Clase`;
  let titulo = `Bienestar - ${tipo_solicitud}`;
  let mensaje = `Se informa que la solicitud de ${tipo_solicitud}, fue enviada y se encuentran en proceso de verificacion, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentran su solicitud.<br><br>Mas informaci&oacuten en :${ser}`;
  if (estado_solicitud == 'Bin_Tra_E') {
    sw = true;
    tipo = 1;
    mensaje = `Por medio del siguiente enlace podr&aacute realizar la encuesta de satisfacci&oacuten de la actividad ${tematica}, que fue abordada en el aula de clase por un funcionario de Bienestar Estudiantil <br> Enlace de la Encuesta: ${ser} <br><br> Agradecemos su valoración frente al servicio recibido`;
  } else if (estado_solicitud == 'Bin_Rev_E') {
    sw = true;
    tipo = 1;
    if (tipo_ == "Docente") mensaje = `Su solicitud de Bienestar a tu clase a sido Programada. Para ver su solicitud haga clic en el siguiente enlace: <a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
    // else mensaje = `Usted ha sido asignado a una nueva solicitud de Bienestar a tu Clase. En el siguiente enlace podr&aacute ver sus solicitudes pendientes a tramitar <a href="${Traer_Server()}index.php/bienestar/"><b>agil.cuc.edu.co</b></a>`;
  }
  else if (estado_solicitud == 'Bin_Neg_E') {
    sw = true;
    tipo = 1;
    mensaje = `Se informa que su solicitud a sido negada por los siguientes motivos: <br>${motivo} <br> <a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
  } else if (estado_solicitud == 'mod') {
    sw = true;
    tipo = 1;
    mensaje = `Se informa que su solicitud a sido modificada por los siguientes motivos: <br>${motivo} <br> <a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
  } else if (estado_solicitud == 'Cancelar_Docente') {
    sw = true;
    tipo = 1;
    mensaje = `Se informa que una solicitud ha sido cancelada por los siguientes motivos: <br>${motivo} <br>. <a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
  } else if (estado_solicitud == 'Reprogramado') {
    sw = true;
    tipo = 1;
    mensaje = `Se informa que su solicitud a sido reprogramada para la siguiente fecha: ${motivo} . <a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
  }

  if (sw) {
    enviar_correo_personalizado("bin", mensaje, correo, nombre_completo, titulo, 'Solicitud de Bienestar', "ParCodBin", tipo);
  }
}

const get_funcionario_solicitud = async (solicitud_id) => {
  return new Promise(resolve => {
    let url = `${ruta}get_funcionario_solicitud`;
    consulta_ajax(url, { id: solicitud_id }, resp => {
      resolve(resp);
    });
  });
}

const get_consulta_solicitud_id = async (solicitud_id) => {
  return new Promise(resolve => {
    let url = `${ruta}consulta_solicitud_id`;
    consulta_ajax(url, { id: solicitud_id }, resp => {
      resolve(resp);
    });
  });
}

const notificar_funcionario_asignado = async (id, motivo = null) => {
  let { tipo_solicitud, id_estado_sol, fecha_i } = await get_consulta_solicitud_id(id);
  let sw = false;
  let ser = `<a href="${Traer_Server()}index.php/bienestar/${id}"><b>agil.cuc.edu.co</b></a>`;
  let tipo = 3;
  let titulo = `Bienestar - ${tipo_solicitud}`;
  let correo = await get_funcionario_solicitud(id);
  if (id_estado_sol == 'Bin_Rev_E') {
    sw = true;
    mensaje = `Se le informa que fue asignado a una intervención de <strong>Bienestar a tu clase</strong> para la siguiente fecha: <strong>${fecha_i}</strong>. 
        La asignación de la intervención puede encontrarse sujeta a cambios según disponibilidad, cruces o errores, por lo que se le recomienda siempre verificar en su cuenta ágil al menos una semana antes de su realización. <br>
        Para más información haga clic en el siguiente enlace: <br> ${ser}`;

  } else if (id_estado_sol == 'Bin_Rep_E') {
    sw = true;
    mensaje = `Se informa que la solicitud de <strong>Bienestar a tu clase</strong>, a sido reprogramada para la siguiente fecha: <strong>${fecha_i}</strong>. 
        La asignación de la intervención puede encontrarse sujeta a cambios según disponibilidad, cruces o errores, por lo que se le recomienda siempre verificar en su cuenta ágil al menos una semana antes de su realización. <br>
        Por motivos de: ${motivo} . <br>
        Para más información haga clic en el siguiente enlace: <br> ${ser}`;
  }

  if (sw) {
    enviar_correo_personalizado("bin", mensaje, correo, 'funcionario', titulo, 'Solicitud de Bienestar', "ParCodBin", tipo);
  }
}

const logear_bienestar = (nombre, usuario, id, codigo) => {
  $("#nombre").html(nombre);
  usuario = usuario.replace(/\@.*/, '').trim();
  $("#form_logear_encuesta input[name='usuario']").val(usuario);
  $("#form_logear_encuesta input[name='id']").val(id);
  $("#form_logear_encuesta input[name='codigo']").val(codigo);
  $("#modal_logear").modal();

}



const listar_valor_parametro = (id_parametro) => {
  $("#tabla_tematicas tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .asignar").off("click", "tr .modificar");
  consulta_ajax(`${ruta}listar_valor_parametro`, { id_parametro }, (resp) => {
    let i = 0;
    const myTable = $("#tabla_tematicas").DataTable({
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
    $("#tabla_tematicas tbody").on("click", "tr", function () {
      $("#tabla_tematicas tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_tematicas tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let { id, id_solicitud, realizo } = myTable.row($(this).parent()).data();
      if (realizo) ver_detalle_encuesta(id, id_solicitud);
    });

    $("#tabla_tematicas tbody").on("click", "tr .asignar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      $("#modal_funcionario_tematica").modal();
      id_tematica = id;
      listar_funcionarios_tematicas(id);
    });

    $("#tabla_tematicas tbody").on("click", "tr .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      confirmar_eliminar_parametro(id);
    });

    $("#tabla_tematicas tbody").on("click", "tr .modificar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      id_idparametro = id;
      id_tematica = id;
      mostrar_parametro_modificar(id_idparametro);
      listar_permiso_parametro(id_tematica, '.estrategias_agregados_modi', 'Estrategia(s) Asignadas');
      $(".rec_sele").html("0");
    });

  });
}

const listar_permiso_parametro = async (id_tematica, container, mensaje, id_estrategia = '') => {
  let filtro = 2;
  let estrategias = await obtener_estrategias(id_tematica, filtro);
  pintar_datos_combo(estrategias, container, mensaje, id_estrategia);
}

const listar_funcionarios_tematicas = id_tematica => {
  $("#tabla_funcionarios_tematica tbody").off("click", "tr").off("click", "tr .eliminar");
  consulta_ajax(`${ruta}listar_funcionarios_tematicas`, { id_tematica }, (resp) => {
    let i = 0;
    const myTable = $("#tabla_funcionarios_tematica").DataTable({
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
    $("#tabla_funcionarios_tematica tbody").on("click", "tr", function () {
      $("#tabla_funcionarios_tematica tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $("#tabla_funcionarios_tematica tbody").on("click", "tr .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      eliminar_funcionario_tematica(id_tematica, id);

    });
  });
}

const listar_horarios_funcionarios = (parametro = '') => {
  consulta_ajax(`${ruta}listar_horarios_funcionarios`, { parametro }, resp => {
    $(`#tabla_horarios tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .funcionario").off("click", "tr .modificar").off("click", "tr .eliminar");
    const myTable = $("#tabla_horarios").DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: resp,
      columns: [
        {
          data: "dia"
        },
        {
          data: "hora_inicio"
        },
        {
          data: "hora_fin"
        },
        {
          data: 'accion'
        }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });
    $("#tabla_horarios tbody").on("click", "tr", function () {
      $("#tabla_horarios tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_horarios tbody").on("dblclick", "tr", function () {
      $("#tabla_horarios tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_horarios tbody").on("click", "tr .funcionario", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      id_horario = id;
      listar_funcionarios_horarios(id_horario);
      $("#modal_funcionarios_horarios").modal();
    });
    $("#tabla_horarios tbody").on("click", "tr .modificar", function () {
      let data = myTable.row($(this).parent()).data();
      ver_horario(data);
    });
    $("#tabla_horarios tbody").on("click", "tr .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      id_horario = id;
      eliminar_horario_funcionario(id);
    });
  });
}

const ver_horario = (data) => {
  let { id, id_dia, hora_inicio, hora_fin, observacion } = data;
  id_horario = id;
  $(".titulo_modal").html("Modificar Horario");
  $("#id_dia").val(id_dia);
  $("#hora_inicio").val(hora_inicio);
  $("#hora_fin").val(hora_fin);
  $("#descripcion").val(observacion);
  $("#modal_crear_horario").modal();
}

const listar_funcionarios_horarios = (id_horario) => {
  consulta_ajax(`${ruta}listar_funcionarios_horarios`, { id_horario }, resp => {
    $(`#tabla_funcionarios_horarios tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .eliminar");
    let i = 0;
    const myTable = $("#tabla_funcionarios_horarios").DataTable({
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
          data: "nombre_completo"
        },
        {
          data: "identificacion"
        },
        {
          data: 'accion'
        }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });
    $("#tabla_funcionarios_horarios tbody").on("click", "tr", function () {
      $("#tabla_funcionarios_horarios tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_funcionarios_horarios tbody").on("dblclick", "tr", function () {
      $("#tabla_funcionarios_horarios tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });
    $("#tabla_funcionarios_horarios tbody").on("click", "tr .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      eliminar_funcionario_horario(id);
    });
  });
}

const guardar_funcionario_tematica = (id_persona, id_tematica, identificacion) => {
  data = { id_persona, id_tematica, identificacion };
  consulta_ajax(`${ruta}guardar_funcionario_tematica`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      let table = $("#tabla_funcionarios_tematica").DataTable();
      table.clear().draw();
      swal.close();
      listar_funcionarios_tematicas(id_tematica);
      $("#form_buscar_persona").get(0).reset();
      // $("#modal_buscar_persona").modal('hide');

    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const eliminar_funcionario_tematica = (id_tematica, id) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar al funcionario presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_funcionario_tematica`, { id_tematica, id }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_funcionarios_tematicas(id_tematica);
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });

}

const eliminar_horario_funcionario = (id) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar el horario presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_horario_funcionario`, { id }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_horarios_funcionarios();
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });
}

const guardar_horario_funcionario = () => {
  let fordata = new FormData(document.getElementById("form_guardar_horario"));
  let data = formDataToJson(fordata);
  data.id_horario = id_horario;
  consulta_ajax(`${ruta}guardar_horario_funcionario`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      id_horario = '';
      $("#form_guardar_horario").get(0).reset();
      $("#modal_crear_horario").modal("hide");
      listar_horarios_funcionarios();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const guardar_funcionario_horario = (id_persona, id_horario) => {
  consulta_ajax(`${ruta}guardar_funcionario_horario`, { id_persona, id_horario }, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      swal.close();
      $("#form_buscar_persona").get(0).reset();
      $("#modal_buscar_persona").modal("hide");
      listar_funcionarios_horarios(id_horario);
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const eliminar_funcionario_horario = (id) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar el funcionario presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_funcionario_horario`, { id }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_funcionarios_horarios(id_horario);
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });
}

const listar_encuestas = id => {
  $("#tabla_encuesta tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .asignar");
  consulta_ajax(`${ruta}listar_encuestas`, { id }, (resp) => {
    let i = 0;
    const myTable = $("#tabla_encuesta").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
          i++;
          return i;
        }
      },
      {
        data: "estrategia"
      },
      {
        data: "actividad"
      },
      {
        data: "servicio"
      },
      {
        data: "apropiado"
      },
      {
        data: "integral"
      },
      {
        data: "metodologia"
      },
      {
        data: "otros"
      },
      ],
      "language": get_idioma(),
      'dom': 'Bfrtip',
      "buttons": [],

    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $("#tabla_encuesta tbody").on("click", "tr", function () {
      $("#tabla_encuesta tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_encuesta tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let { id, id_solicitud, realizo } = myTable.row($(this).parent()).data();
      if (realizo) ver_detalle_encuesta(id, id_solicitud);
    });

    $("#tabla_encuesta tbody").on("click", "tr .asignar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      $("#modal_funcionario_tematica").modal();
      id_tematica = id;
      listar_funcionarios_tematicas(id);
    });

  });
}

const administrar_vista = async tipo => {

  let { persona, perfil } = await data_sesion();
  administra = perfil == 'Per_Admin' || perfil == 'Per_Bin' ? true : false;
  funcionario = perfil == 'Bin_Fun' ? true : false;

  if (tipo = "estudiantes") {

    if ((id_estado_sol == "Bin_Sol_E" || id_estado_sol == "Bin_Rev_E") && (administra || funcionario || id_solicitante == persona)) $("#agregar_estudiantes_nuevos").show();
    else $("#agregar_estudiantes_nuevos").hide();
    $("#modal_estudiantes_solicitud").modal();
  }
}

const data_sesion = () => {
  return new Promise(resolve => {
    let url = `${ruta}data_sesion`;
    consulta_ajax(url, {}, (resp) => {
      resolve(resp);
    });
  });
}



const listar_modificaciones = id => {
  $("#tabla_modificaciones_solicitud tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .asignar");
  consulta_ajax(`${ruta}listar_modificaciones`, { id }, (resp) => {
    let i = 0;
    const myTable = $("#tabla_modificaciones_solicitud").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [{
        render: function (data, type, full, meta) {
          i++;
          return i;
        }
      },
      {
        data: "nombre_campo"
      },
      {
        data: "anterior"
      },
      {
        data: "actual"
      },
      {
        data: "observaciones"
      },
      {
        data: "fecha"
      },
      ],
      "language": get_idioma(),
      'dom': 'Bfrtip',
      "buttons": [],

    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $("#tabla_modificaciones_solicitud tbody").on("click", "tr", function () {
      $("#tabla_modificaciones_solicitud tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_modificaciones_solicitud tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let { id, id_solicitud, realizo } = myTable.row($(this).parent()).data();
      if (realizo) ver_detalle_encuesta(id, id_solicitud);
    });

    $("#tabla_modificaciones_solicitud tbody").on("click", "tr .asignar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      $("#modal_funcionario_tematica").modal();
      id_tematica = id;
      listar_funcionarios_tematicas(id);
    });

  });
}


const configurar_form_modificar = (estado, container = '#form_solicitud_bienestar_mod') => {
  if (estado == 'show') {
    $(`${container} #mod_detalle`).show("fast");
    $(`${container} #mod_detalle input`).attr("required", "true");
    $(`${container} #mod_detalle select`).attr("required", "true");
    $(`${container} .modal-title`).html("<span class='fa fa-plus'></span> <span > Modificar Solicitud</span>");
    $(`${container} .reprogramar`).hide("fast");
    $(`${container} .reprogramar input`).removeAttr("required", "true");
    $(`${container} .reprogramar select`).removeAttr("required", "true");

  } else {
    $(`${container} #mod_detalle`).hide("fast");
    $(`${container} #mod_detalle select`).removeAttr("required", "true");
    $(`${container} #mod_detalle input`).removeAttr("required", "true");
    $(`${container} .modal-title`).html("<span class='fa fa-calendar'></span> <span > Reprogramar Solicitud</span>");
    $(`${container} .reprogramar`).show("fast");
    $(`${container} .reprogramar input`).attr("required", "true");
    $(`${container} .reprogramar select`).attr("required", "true");
  }
  // $('#modal_modificar_bienestar').modal();
}


const confirmar_eliminar_parametro = (id, estado) => {

  swal({
    title: "Estas Seguro .. ?",
    text: "Tener en cuenta que al Eliminar este valor no estara disponible en las solicitudes de presupuesto, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
        eliminar_parametro(id, estado);
      }
    });
}

function eliminar_parametro(idparametro, estado) {
  let url = `${Traer_Server()}index.php/genericas_control/cambio_estado_parametro`;
  let data = {
    idparametro,
    estado
  };
  consulta_ajax(url, data, (resp) => {
    if (resp == "sin_session") {
      close();
    } else if (resp == 1) {
      swal.close();
      listar_valor_parametro(121);
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al eliminar la información, contacte con el administrador.", "error", "Oops...");
    }
  })
}


const modificar_valor_parametro = () => {
  let url = `${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`;
  let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
  data.append("idparametro", id_idparametro);
  let data_ = formDataToJson(data);
  enviar_formulario(url, data, (resp) => {
    if (resp == "sin_session") {
      close();
    } else if (resp == 1) {
      id_tematica = '';
      $("#form_modificar_valor_parametro").get(0).reset();
      $("#ModalModificarParametro").modal("hide");
      MensajeConClase("", "success", "Datos Modificados!");
      listar_valor_parametro(121);
    } else if (resp == 2) {
      MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
    } else if (resp == 3) {
      MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al Modificar la información, contacte con el administrador.", "error", "Oops...");
    }
  })
}

const mostrar_parametro_modificar = async (buscar) => {
  let data = await buscar_parametro_id(buscar);
  let { valor, valorx } = data[0];

  $("#txtValor_modificar").val(valor);
  $("#txtDescripcion_modificar").val(valorx);
  $("#ModalModificarParametro").modal();
}


// const guardar_valor_parametro = () => {
//     if (estrategia_sele.length > 0) {
//         let url = `${Traer_Server()}index.php/genericas_control/guardar_valor_Parametro`;
//         let data = new FormData(document.getElementById("form_guardar_valor_parametro"));
//         data.append("idparametro", 121);
//         enviar_formulario(url, data, (resp) => {
//             if (resp == "sin_session") {
//                 close();
//             } else if (resp == 1) {
//                 MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
//             } else if (resp == 2) {
//                 let data_new = formDataToJson(data);
//                 data_new.estrategias = estrategia_sele;
//                 consulta_ajax(`${ruta}tematica_estrategia`, data_new, (resp) => {
//                     let { tipo, titulo, mensaje } = resp;
//                     MensajeConClase(mensaje, tipo, titulo);
//                 });
//                 $("#form_guardar_valor_parametro").get(0).reset();
//                 $("#modal_nuevo_valor").modal("hide");
//                 listar_valor_parametro(121);
//             } else if (resp == 3) {
//                 MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
//             } else if (resp == -1302) {
//                 MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
//             } else {
//                 MensajeConClase("Error al Guardar la información, contacte con el administrador.", "error", "Oops...");
//             }
//         })
//     } else MensajeConClase("Es necesario seleccionar alguna estrategía.", "info", "Oops...!");
// }

const guardar_valor_parametro = async () => {
  if (estrategia_sele.length > 0) {
    let fordata = new FormData(document.getElementById("form_guardar_valor_parametro"));
    fordata.append("idparametro", 121);
    let data = formDataToJson(fordata);
    data.estrategias = estrategia_sele;
    let { tipo, titulo, mensaje } = await nuevo_valor_Parametro(data);
    if (tipo == "success") {
      habilitar_permiso_parametro(data);
    } else MensajeConClase(mensaje, tipo, titulo);
  } else MensajeConClase("Es necesario seleccionar alguna estrategía.", "info", "Oops...!");
}

const nuevo_valor_Parametro = (data) => {
  return new Promise(resolve => {
    let url = `${Traer_Server()}index.php/genericas_control/nuevo_valor_Parametro`;
    consulta_ajax(url, data, (resp) => {
      resolve(resp);
    });
  });
}

const habilitar_permiso_parametro = (data) => {
  consulta_ajax(`${ruta}tematica_estrategia`, data, (resp) => {
    let { tipo, titulo, mensaje } = resp;
    MensajeConClase(mensaje, tipo, titulo);
  });
  $("#form_guardar_valor_parametro").get(0).reset();
  $("#modal_nuevo_valor").modal("hide");
  listar_valor_parametro(121);
}

const listar_bloqueos = () => {
  $("#tabla_bloqueos tbody").off("click", "tr").off("click", "tr .eliminar").off("click", "tr .modificar").off("click", "tr td:nth-of-type(1)");
  consulta_ajax(`${ruta}listar_bloqueos`, {}, (resp) => {
    let i = 0;
    const myTable = $("#tabla_bloqueos").DataTable({
      destroy: true,
      searching: true,
      processing: true,
      data: resp,
      columns: [{
        data: "ver"

      },
      {
        data: "nombre"
      },
      {
        data: "fecha_inicio"
      },
      {
        data: "fecha_fin"
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
    $("#tabla_bloqueos tbody").on("click", "tr", function () {
      $("#tabla_bloqueos tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
    });

    $('#tabla_bloqueos tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row($(this).parent()).data();
      ver_detalle_bloqueo(data);
    });

    $("#tabla_bloqueos tbody").on("click", "tr .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      eliminar_bloqueo(id);
    });

    $("#tabla_bloqueos tbody").on("click", "tr .modificar", function () {
      let data = myTable.row($(this).parent().parent()).data();
      consulta_bloqueo_id(data);
    });

  });
}

const guardar_bloqueo = () => {
  let fordata = new FormData(document.getElementById("form_guardar_bloqueo"));
  let data = formDataToJson(fordata);
  consulta_ajax(`${ruta}guardar_bloqueo`, data, (resp) => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == 'success') {
      listar_bloqueos();
      $("#form_guardar_bloqueo").get(0).reset();
      $("#modal_nuevo_bloqueo").modal('hide');
      MensajeConClase(mensaje, tipo, titulo);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const ver_detalle_bloqueo = data => {
  let {
    fecha_inicio,
    fecha_fin,
    descripcion,
    usuario_registra,
    usuario_elimina,
    fecha_registra,
    fecha_elimina,
    nombre,
    tematica
  } = data;
  $(".fecha_inicio").html(fecha_inicio);
  $(".fecha_fin").html(fecha_fin);
  $(".descripcion").html(descripcion);
  $(".usuario_registra").html(usuario_registra);
  $(".fecha_registra").html(fecha_registra);
  $(".fecha_elimina").html(fecha_elimina);
  $(".nombre").html(nombre);
  $(".tematica").html(tematica);
  $("#modal_detalle_bloqueo").modal();
}


const eliminar_bloqueo = (id) => {
  swal({
    title: "Estas Seguro ?",
    text: "Si desea eliminar el bloqueo presione la opción de 'Si, Entiendo'.!",
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
      consulta_ajax(`${ruta}eliminar_bloqueo`, { id }, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == 'success') {
          swal.close();
          listar_bloqueos();
        } else MensajeConClase(mensaje, tipo, titulo);
      });
    }
  });

}


const consulta_bloqueo_id = data => {
  let {
    fecha_inicio,
    fecha_fin,
    descripcion,
    nombre,
    id,
    id_tematica
  } = data
  id_bloqueo = id
  if (id_tematica == 0) { id_tematica = ''; }
  $("#modal_modificar_bloqueo select[name='idtematica']").val(id_tematica);
  $("#modal_modificar_bloqueo input[name='bloqueo_fecha_inicio']").val(fecha_inicio);
  $("#modal_modificar_bloqueo input[name='bloqueo_fecha_fin']").val(fecha_fin);
  $("#modal_modificar_bloqueo input[name='nombre']").val(nombre);
  $("#modal_modificar_bloqueo textarea[name='descripcion']").val(descripcion);

  $("#modal_modificar_bloqueo").modal();
}

const modificar_bloqueo = () => {
  let fordata = new FormData(document.getElementById("form_modificar_bloqueo"));
  let data = formDataToJson(fordata);
  data.id_bloqueo = id_bloqueo;
  consulta_ajax(`${ruta}modificar_bloqueo`, data, async (resp) => {
    let { mensaje, tipo, titulo } = resp;
    if (tipo == 'success') {
      listar_bloqueos();
      $("#form_modificar_bloqueo").get(0).reset();
      $("#modal_modificar_bloqueo").modal("hide");

    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}


const listar_disponibilidad_bloqueo = data => {
  $("#tabla_disponibilidad_bloqueo tbody").off("click", "tr").off("click", "tr .eliminar");
  let i = 0;
  const myTable = $("#tabla_disponibilidad_bloqueo").DataTable({
    destroy: true,
    searching: false,
    processing: true,
    data,
    columns: [{
      render: function (data, type, full, meta) {
        i++;
        return i;
      }
    },
    // {
    //     data: "nombre"
    // },
    // {
    //     data: "descripcion"
    // },
    {
      data: "fecha_inicio"
    },
    {
      data: "fecha_fin"
    },
    ],
    language: idioma,
    dom: "Bfrtip",
    buttons: []
  });

  //EVENTOS DE LA TABLA ACTIVADOS
  $("#tabla_disponibilidad_bloqueo tbody").on("click", "tr", function () {
    $("#tabla_disponibilidad_bloqueo tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
  });

};

const fechasDisponibilidad = (fecha, duracion, bloqueo, tematica) => {
  consulta_ajax(`${ruta}fechasDisponibilidad`, { fecha, duracion, tematica }, (resp) => {
    let { mensaje, tipo, disponibilidad } = resp;
    if (tipo == 'no_disponible') {
      let motivo = '';
      disponibilidad.map((elemento) => { motivo = motivo + `<p><b>${elemento.descripcion}. Inicio: ${elemento.fecha_inicio} - Fin: ${elemento.fecha_fin}</b></p>`; })
      $(".detalle_bloqueo").html(`${mensaje}: ${motivo}`);
      $(".noDisponible").show();
      // listar_disponibilidad_bloqueo(disponibilidad);
      // consulta_ajax(`${ruta}fechasDisponibilidad`, { fecha, duracion, bloqueo, tematica }, (resp) => {
      //     let { disponibilidad } = resp;
      //     listar_disponibilidad(disponibilidad, fecha, duracion);
      // });

    } else $(".noDisponible").hide();
  });
}


const listar_disponibilidad = (disponibilidad, fecha, duracion) => {
  consulta_ajax(`${ruta}listar_disponibilidad`, { disponibilidad, fecha, duracion }, (resp) => {
    let { mensaje, tipo, lista_disponibilidad } = resp;
    if (tipo == 'success') {
      listar_disponibilidad_bloqueo(lista_disponibilidad);
    } else {
      $(".mensaje_disponibilidad").html(mensaje);
    }
  });
}


const listar_estrategias_disponibles = (id_parametro, id_tematica) => {
  let filtro = 2;
  let combo = '.estrategias_agregados_modi';
  if (id_tematica == '') {
    id_tematica = 0;
    combo = '.estrategias_agregados';
  }
  $('#tabla_estrategias_disponibles tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr .seleccionar');
  consulta_ajax(`${ruta}listar_valor_parametro`, { id_parametro, filtro, id_tematica }, (resp) => {
    let i = 0;
    const table = $("#tabla_estrategias_disponibles").DataTable({
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
    $('#tabla_estrategias_disponibles tbody').on('dblclick', 'tr', function () {
      let data = table.row(this).data();
      sele_articulo_add(data, this);

    });
    $('#tabla_estrategias_disponibles tbody').on('click', 'tr .seleccionar', function () {
      let data = table.row($(this).parent().parent()).data();
      sele_articulo_add(data, $(this).parent().parent());
    });
  });

  estrategia_sele = [];
  const sele_articulo_add = (data, thiss) => {
    if (!$(thiss).hasClass("warning")) {
      $(thiss).attr("class", "warning");
      estrategia_sele.push(data);
      if (id_tematica > 0) {
        swal({
          title: "Asignar Estrategía.?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#D9534F",
          confirmButtonText: "Si, Asignar!",
          cancelButtonText: "No, Regresar!",
          allowOutsideClick: true,
          closeOnConfirm: false,
          closeOnCancel: true
        },
          function (isConfirm) {
            if (isConfirm) {
              let url = `${Traer_Server()}index.php/genericas_control/habilitar`;
              let form = new FormData(document.getElementById("form_modificar_valor_parametro"));
              form.append("vp_principal_id", id_tematica);
              form.append("vp_secundario_id", data.id);
              enviar_formulario(url, form, (resp) => {
                let { mensaje, tipo, titulo } = resp;
                if (tipo == 'success') {
                  titulo = 'Estrategía Asignada';
                  listar_estrategias_disponibles(id_parametro, id_tematica);
                  listar_permiso_parametro(id_idparametro, '.estrategias_agregados_modi', 'Estrategia(s) Asignadas');
                }
                MensajeConClase(mensaje, tipo, titulo);
              })
            }
          });
      }
    } else {
      $(thiss).removeClass("warning");
      var i = estrategia_sele.indexOf(data);
      estrategia_sele.splice(i, 1);
    }
    Cargar_estrategias_combo(combo, estrategia_sele, id_tematica);
  }

  $("#Modal_seleccionar_estrategia").modal("show");
}

function Cargar_estrategias_combo(combo, datos, id_tematica) {
  $(combo).html("");
  var sw = true;
  var total = 0;
  var mostrar = [];
  for (var i = 0; i <= datos.length - 1; i++) {
    total = 0;
    sw = true;
    for (var j = 0; j <= datos.length - 1; j++) {
      var datos_actuales = datos[i];
      if (datos[j].id == datos[i].id) {
        total++;
      }
    }
    for (let index = 0; index < mostrar.length; index++) {
      if (mostrar[index][0] == datos[i].id) {
        sw = false;
      }
    }
    if (sw) {
      var armar = [datos_actuales.id, datos_actuales.valor, datos_actuales.idparametro];
      mostrar.push(armar);
    }
  }

  $(combo).append("<option  value=''> " + mostrar.length + " Estrategias a Asignar</option>");
  for (let index = 0; index < mostrar.length; index++) {
    const element = mostrar[index];
    $(combo).append("<option  value= " + element[0] + ">" + element[1] + "</option>");
  }

  $(".rec_sele").html(mostrar.length);
}

const retirar_estrategia_sele = (combo, action) => {
  let id_permiso = $(combo).val();
  if (id_permiso.length == 0) {
    MensajeConClase("Seleccione Estrategia a Retirar..!", "info", "Oops...")
  } else {
    swal({
      title: "Estas Seguro ?",
      text: "Atencion antes de continuar tener en cuenta que al retirar la estrategia este no se tendra en cuenta al momento de terminar la asignacion.!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#D9534F",
      confirmButtonText: "Si, Retirar!",
      cancelButtonText: "No, Regresar!",
      allowOutsideClick: true,
      closeOnConfirm: false,
      closeOnCancel: true
    },
      function (isConfirm) {
        if (isConfirm) {
          if (action > 1) {
            let url = `${Traer_Server()}index.php/genericas_control/deshabilitar`;
            let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
            data.append("id_permiso", id_permiso);
            enviar_formulario(url, data, (resp) => {
              let { mensaje, tipo, titulo } = resp;
              if (tipo == 'success') {
                titulo = 'Estragía retirada.!'
                listar_permiso_parametro(id_idparametro, '.estrategias_agregados_modi', 'Estrategia(s) Asignadas');
              }
              MensajeConClase(mensaje, tipo, titulo);
            })
          } else Retirar_estrategia(id_permiso, combo);
        }
      });
  }
}

const Retirar_estrategia = (dato, combo) => {
  for (var i = 0; i < estrategia_sele.length; i++) {
    if (estrategia_sele[i].id == dato) {
      estrategia_sele.splice(i, 1);
      swal.close();
      Cargar_estrategias_combo(combo, estrategia_sele);
      return true;
    }
  }
  MensajeConClase("No fue posible retirar la estrategia.!!", "info", "Oops..!");
}



// const Reasignarfuncionario = () => { //funcion para asignar funcionario automático desde botón asignar_funcionario

//     consulta_ajax(`${ruta}Reasignarfuncionario`, {id_tematica,id_solicitud }, (resp) => {
//         let { mensaje,tipo,titulo } = resp;
//         console.log(resp);
//         if (tipo == 'success') {
//             listar_funcionarios_solicitud(id_solicitud);
//             MensajeConClase(mensaje, tipo, titulo);
//         }else{
//             MensajeConClase(mensaje, tipo, titulo);
//         }
//     });
// }
