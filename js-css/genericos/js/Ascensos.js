let ruta = `${Traer_Server()}index.php/ascensos_control/`;
let callbak_activo = resp => { };
let id_global_sol = null;
let id_item = null;
let id_secc = null;
let id_tipo_sol = null;
let id_estado_sol = null;
let ruta_archivos_items = "archivos_adjuntos/ascensos/items/";
let tipo_adjunto = '';
let columna = null;
let errores = [];
let cargados = 0;
let sw_b = false;
let tipo_form = null;
callbak_activo = resp => { };
let cargo_actual = {};
let cargo_nuevo = {};
let sw_modal = false

$(document).ready(() => {
  $("#regresar_menu").click(function () {
    $(".listado_solicitudes").css("display", "none");
    $("#menu_principal").fadeIn(1000);
  });

  $("#container-files").change(function () {
    activarfile();
  });

  $("#formacion_select").change(function () {
    let form = $('select[name="id_formacion"] :selected').attr('class');
    if (form == 'Est_Ing') {
      listar_niveles_ingles();
      $("#ingles_select").show();
      $("#txt_nombre_formacion").hide();
    } else if ($("#formacion_select").val() == '') {
      $("#ingles_select").hide();
      $("#txt_nombre_formacion").hide();
    } else {
      $("#ingles_select").hide();
      $("#txt_nombre_formacion").show();
    }
  })

  $("#listado").click(function () {
    $("#menu_principal").css("display", "none");
    $(".listado_solicitudes").fadeIn(1000);
    listar_solicitudes();
  });

  $("#nueva_solicitud").click(function () {
    consulta_ajax(`${ruta}consultar_solicitudes_docente`, {}, resp => {

      let { pendiente, solicitud, tipo, estado } = resp;
      if (pendiente == 0) {
        $("#modal_nueva_solicitud").modal();
      } else {

        if (pendiente) {

          swal({
            title: "¡Acción requerida!",
            text: "Hay un proceso incompleto (Borrador) o pendiente por aprobación.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Ir a la solicitud",
            cancelButtonText: "Ok, comprendo",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true
          },
            function (isConfirm) {
              if (isConfirm) {
                $("#menu_principal").css("display", "none");
                $(".listado_solicitudes").fadeIn(1000);
                listar_solicitudes(solicitud);
                swal.close();
              }
            });

        }

      }
    });
  });

  $("#seleccion_tipo .thumbnail").mouseover(function () {
    $(this).find("span").css({
      "background-color": "#d57e1c",
      "border-color": "#d57e1c"
    })
  });

  $("#seleccion_tipo .thumbnail").mouseleave(function () {
    $(this).find("span").css({
      "background-color": "#6e1f7c",
      "border-color": "#6e1f7c"
    })
  });

  $("#type_docencia").click(function () {
    crear_solicitud('Asc_Doc');
  });

  $("#type_investigacion").click(function () {
    crear_solicitud('Asc_Inv');
  });

  $("#type_extension").click(function () {
    crear_solicitud('Asc_Ext');
  });

  $("#botton").click(function () {
    mostrar_elementons();
  });

  $("#btn_archivos").click(function () {
    obtener_items(id_global_sol);
    obtener_informacion_general(id_global_sol);
    $("#txt_experiencia_doc").attr('readonly', true);
    $("#colciencias_select").attr('readonly', true);
    $("#colciencias_url").attr('readonly', true);
    $("#txt_indice_scopus").attr('readonly', true);
    $("#txt_indice_scopus_value").attr('readonly', true);
    $("#txt_cargo_nuevo").attr('readonly', true);
    $("#txt_cargo_actual").attr('readonly', true);
    $("#txt_cvlac").attr('readonly', true);
    $("#btn_agregar_informacion").hide();
    $("#modal_items_solicitud").modal();
    sw_b = false
  });

  $("#agregar_formacion").click(function () {
    sw_modal = false;
    listar_tipo_formacion();
    $("#ingles_select").hide();
    $("#txt_nombre_formacion").hide();
    $("#modal_agregar_formacion").modal();
  });

  $("#btn_crear_formacion").click(function () {
    $("#modal_enviar_archivos").modal();
    $("#footer_archivos").html(`<button class="btn btn-danger" id="btn_adjuntos_formacion"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
    if (tipo_form == 'estudio') $("#txt_nombre_formacion").attr('placeholder', "Nombre del titulo");
    else if (tipo_form == 'producto') $("#txt_nombre_formacion").attr('placeholder', "Nombre del producto de formación");
    $("#btn_adjuntos_formacion").click(function () {
      guardar_formacion()
    });
  });

  $("#btn_agregar_informacion").click(function () {
    let a_experiencia = $("#txt_experiencia_doc").val();
    let cat_colciencias = $("#colciencias_url").val();
    let id_colciencias = $("#colciencias_select").val();
    let ind_scopus = $("#txt_indice_scopus").val();
    let ind_value = $("#txt_indice_scopus_value").val();
    let cvlac = $("#txt_cvlac").val();
    guardar_informacion_solicitud(a_experiencia, cat_colciencias, ind_scopus, id_colciencias, ind_value, cvlac);
  });


  $("#btn_cargo_actual").click(() => {
    let add = id_estado_sol != 'Asc_Bor_E' ? true : false;
    if (!add) {
      container_activo = "#txt_cargo_actual";
      $("#txt_dato_buscar").val("");
      callbak_activo = resp => {
        mostrar_nombre_cargo(resp, 1);
      };
      buscar_cargo("F**D", callbak_activo);
      $("#modal_buscar_cargo").modal();
    }
  });

  $("#btn_cargo_nuevo").click(() => {
    let add = id_estado_sol != 'Asc_Bor_E' ? true : false;
    if (!add) {
      container_activo = "#txt_cargo_nuevo";
      $("#txt_dato_buscar").val("");
      callbak_activo = resp => {
        mostrar_nombre_cargo(resp, 2);
      };
      buscar_cargo("F**D", callbak_activo);
      $("#modal_buscar_cargo").modal();
    }
  });

  $("#form_buscar_cargo").submit(() => {
    let dato = $("#txt_dato_buscar").val();
    buscar_cargo(dato, callbak_activo);
    return false;
  });

  $("#limpiar_filtros_ascensos").click(function () {
    $("#id_tipo_select").val('');
    $("#id_estado_select").val('');
    $("#fecha_inicial").val('');
    $("#fecha_final").val('');
    listar_solicitudes();
  });

  $("#btn_log").click(function () {
    listar_historial_estados();
  });

  $("#btn_aplicar_filtros").click(function () {
    listar_tipos_solicitud();
    listar_estados_solicitud();
    $("#modal_crear_filtros").modal();
  });

  $("#btn_filtrar").click(function () {
    let tipo_solicitud = $("#id_tipo_select").val();
    let estado_solicitud = $("#id_estado_select").val();
    let fecha_inicio = $("#fecha_inicial").val();
    let fecha_fin = $("#fecha_final").val();
    listar_solicitudes('', tipo_solicitud, estado_solicitud, fecha_inicio, fecha_fin);
  });
});

const mostrar_nombre_cargo = (data, num) => {
  let { id, valor } = data;
  if (num == 1) {
    cargo_actual = { id, valor };
  } else {
    cargo_nuevo = { id, valor };
  }
  $(container_activo).val(valor);
  $("#modal_buscar_cargo").modal('hide');
}

const buscar_cargo = (dato, callback) => {
  consulta_ajax(`${ruta}buscar_cargo`, { dato }, resp => {
    $("#tabla_cargo_busqueda tbody")
      .off("click", "tr td .elegir");
    let i = 0;
    const myTable = $("#tabla_cargo_busqueda").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        {
          render: function (data, type, full, meta) {
            i++;
            return i;
          }
        },
        { data: "valor" },
        { data: "accion" }
      ],
      language: get_idioma(),
      dom: "Bfrtip",
      buttons: []
    });

    $("#tabla_cargo_busqueda tbody").on("click", "tr td .elegir", function () {
      let data = myTable.row($(this).parent().parent()).data();
      console.log(data);
      callback(data);
      $("#form_buscar_cargo").get(0).reset();
    })
  });
}

const crear_solicitud = (tipo) => {
  let tipo_ascenso = '';
  let data = {}
  if (tipo == 'Asc_Doc') tipo_ascenso = 'Docencia';
  else if (tipo == 'Asc_Inv') tipo_ascenso = 'Investigación';
  else if (tipo == 'Asc_Ext') tipo_ascenso = 'Extensión';
  swal(
    {
      title: `Ascenso por ${tipo_ascenso}`,
      text: `Esta a un paso de crear una solicitud de ascenso por la ruta de ${tipo_ascenso}, si esta seguro de escoger esta ruta por favor presione en el botón de "Si, Entiendo!", de lo contrario presione el botón de "No, Regresar!"`,
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
        data.tipo = tipo;
        consulta_ajax(`${ruta}crear_solicitud`, data, resp => {
          let = { titulo, mensaje, tipo, id_solicitud, tipo_solicitud, estado } = resp;
          if (tipo != "success") {
            MensajeConClase(mensaje, tipo, titulo);
          } else {
            $("#modal_nueva_solicitud").modal('hide');
            swal.close();
            id_tipo_sol = tipo_solicitud;
            id_global_sol = id_solicitud;
            id_estado_sol = estado;
            obtener_items(tipo_solicitud);
            $("#txt_experiencia_doc").removeAttr("readonly");
            $("#colciencias_select").removeAttr("readonly");
            $("#colciencias_url").removeAttr("readonly");
            $("#txt_indice_scopus").removeAttr("readonly");
            $("#txt_indice_scopus_value").removeAttr("readonly");
            $("#txt_cargo_actual").removeAttr("readonly");
            $("#txt_cargo_nuevo").removeAttr("readonly");
            $("#txt_cvlac").removeAttr("readonly");
            $("#btn_agregar_informacion").show();
            $("#modal_items_solicitud").modal();
            sw_b = true;
          }
        })
      }
    }
  );
};

const listar_solicitudes = (id = '', id_tipo_solicitud = '', id_estado_solicitud = '', fecha_inicio = '', fecha_fin = '') => {
  (id || id_tipo_solicitud || id_estado_solicitud || fecha_inicio || fecha_fin) ? $('.mensaje-filtro').show() : $('.mensaje-filtro').hide();
  $("#tabla_solicitudes tbody")
    .off("click", "tr")
    .off("dblclick", "tr")
    .off("click", "tr td:nth-of-type(1)")
    .off("click", "tr td .adjuntar")
    .off("click", "tr td .cancelar")
    .off("click", "tr td .enviar")
    .off("click", "tr td .aceptar")
    .off("click", "tr td .rechazar")
    .off("click", "tr td .reviar")
  consulta_ajax(`${ruta}listar_solicitudes`, { id, id_tipo_solicitud, id_estado_solicitud, fecha_inicio, fecha_fin }, resp => {

    const myTable = $("#tabla_solicitudes").DataTable({
      destroy: true,
      processing: true,
      data: resp,
      columns: [
        { data: "ver" },
        { data: "tipo" },
        { data: "docente" },
        { data: "fecha_registro" },
        { data: "estado" },
        { data: 'accion' }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: get_botones()
    });

    swal.close();

    $("#tabla_solicitudes tbody").on("click", "tr", function () {
      $("#tabla_solicitudes tbody tr").removeClass("warning");
      $(this).attr("class", "warning");
      let data = myTable.row(this).data();
      id_global_sol = data.id;
      id_estado_sol = data.id_estado;
      id_tipo_sol = data.id_tipo;
    });

    $("#tabla_solicitudes tbody").on("dblclick", "tr", function () {
      let data = myTable.row($(this).parent()).data();
      id_global_sol = data.id;
      id_tipo_sol = data.id_tipo;
      id_estado_sol = data.id_estado;
      ver_detalle_solicitud(data);
    });

    $("#tabla_solicitudes tbody").on("click", "tr td:nth-of-type(1)", function () {
      let data = myTable.row($(this).parent()).data();
      id_global_sol = data.id;
      id_tipo_sol = data.id_tipo;
      id_estado_sol = data.id_estado;
      ver_detalle_solicitud(data);
    });

    $("#tabla_solicitudes tbody").on("click", "tr td .adjuntar", function () {
      let { id, id_tipo, id_estado } = myTable.row($(this).parent()).data();

      id_estado_sol = id_estado;
      if (id_estado == 'Asc_Bor_E') {
        id_global_sol = id;
        id_tipo_sol = id_tipo;
        obtener_items();
        if (id_tipo != 'Asc_Inv') {
          $("#colciencias_url").hide();
          $("#colciencias_select").hide();
        }
        else {
          $("#colciencias_select").show();
          $("#colciencias_url").show();
        }
        $("#txt_experiencia_doc").removeAttr("readonly");
        $("#colciencias_select").removeAttr("readonly");
        $("#colciencias_url").removeAttr("readonly");
        $("#txt_indice_scopus").removeAttr("readonly");
        $("#txt_cvlac").removeAttr("readonly");
        $("#txt_indice_scopus_value").removeAttr("readonly");
        $("#txt_cargo_actual").removeAttr("readonly");
        $("#txt_cargo_nuevo").removeAttr("readonly");
        $("#btn_agregar_informacion").show();
        $("#modal_items_solicitud").modal();
        sw_b = true;
      } else {
        MensajeConClase("Esta solicitud ya esta finalizada", "info", "Oops.!")
      }

    });

    /*DESCARGAR PDF*/

    $(".obtener_pdf").each(function () {
      let { id } = myTable.row($(this).parent()).data();
      $(this).attr("id", id);
    });

    $(".obtener_pdf").click(function (e) {
      window.location = base_url + 'index.php/ascensos_control/descargar_acta/' + e.target.id;
    });

    /*FIN DESCARGAR PDF*/

    $("#tabla_solicitudes tbody").on("click", "tr td .cancelar", function () {
      let { id } = myTable.row($(this).parent()).data();
      cancelar_solicitud(id);
    });

    $("#tabla_solicitudes tbody").on("click", "tr td .enviar", function () {
      let { id, docente, correo, id_docente, cargo_nuevo_valor } = myTable.row($(this).parent()).data();
      gestionar_solicitud({ id: id, estado: 'Asc_Env_E', accion: 'enviar', docente: docente, correo: correo, id_docente: id_docente, cargo_nuevo: cargo_nuevo_valor });
    });

    $("#tabla_solicitudes tbody").on("click", "tr td .rechazar", function () {
      let { id, docente, correo, id_docente, cargo_nuevo_valor } = myTable.row($(this).parent()).data();
      gestionar_solicitud({ id: id, estado: 'Asc_Neg_E', accion: 'negar', docente: docente, correo: correo, id_docente: id_docente, cargo_nuevo: cargo_nuevo_valor });
    });

    $("#tabla_solicitudes tbody").on("click", "tr td .aceptar", function () {
      let { id, docente, correo, id_docente, cargo_nuevo_valor } = myTable.row($(this).parent()).data();
      gestionar_solicitud({ id: id, estado: 'Asc_Ace_E', accion: 'aceptar', docente: docente, correo: correo, id_docente: id_docente, cargo_nuevo: cargo_nuevo_valor });
    });

    $("#tabla_solicitudes tbody").on("click", "tr td .revisar", function () {
      let { id, docente, correo, id_docente, cargo_nuevo_valor } = myTable.row($(this).parent()).data();
      gestionar_solicitud({ id: id, estado: 'Asc_Bor_E', accion: 'revisar', docente: docente, correo: correo, id_docente: id_docente, cargo_nuevo: cargo_nuevo_valor });
    });

  })

}

const obtener_items = () => {
  let tipo = id_tipo_sol;
  $(".req_item").off("click");
  $("#btn_formacion").off("click");
  $("#btn_experiencia").off("click");
  consulta_ajax(`${ruta}obtener_items`, { tipo }, resp => {
    console.log(resp);
    var all = `<div class="opciones__container">`;
    resp.forEach(element => {
      let result_items = ``
      element.items.forEach(item => {

        result_items += String.raw`
        
          <div class="opcion__cont ${item.id_aux ? '' : 'req_item'}" data-div="${item.require_inf ? item.require_inf : ''}" id="${item.id_aux ? item.id_aux : item.id}" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="${item.adicional}">
          
            <img src="${base_url}/imagenes/${item.imagen}" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
            <span class="opcion__span" id="${item.seccion}">${item.nombre}</span>
          
          </div>  `

      });

      all += `${result_items}`;

    });

    all += `</div>`

    $("#modalbody").html(all);
    $('[data-toggle="popover"]').popover();
    $('#Ite_Asc_Gen').on('click', function () {
      $('#modal_gen_info').modal();
      obtener_informacion_general(id_global_sol);
    });

    $(".req_item").on("click", function () {
      if ($(this).attr('data-div') == "inf_gen") {
        id_item = $(this).attr('id');
        id_secc = $(`#${id_item} span`).attr('id');
        obtener_informacion_general(id_global_sol);
        $("#modal_gen_info").modal();
        $("#btn_addExp").click(function () {
          sw_modal = true;
          $("#footer_archivos").html(`<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
          columna = null;
          tipo_adjunto = 'item'
          listar_archivos_adjuntos();
          $("#btnAgregarAdjunto").off("click");
          $("#btnAgregarAdjunto").click(function () {
            if (num_archivos != 0) {
              myDropzone.processQueue();
            } else {
              MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
            }
          });
        });
        return false;
      }
      sw_modal = true;
      $("#footer_archivos").html(`<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
      id_item = $(this).attr('id');
      id_secc = $(`#${id_item} span`).attr('id');
      columna = null;
      tipo_adjunto = 'item'
      listar_archivos_adjuntos()

      $("#btnAgregarAdjunto").off("click");
      $("#btnAgregarAdjunto").click(function () {
        if (num_archivos != 0) {
          myDropzone.processQueue();
        } else {
          MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
        }
      });
    });

    $("#Ite_Asc_Form").on("click", function () {
      let add = id_estado_sol != 'Asc_Bor_E' ? true : false;
      if (add) $("#agregar_formacion").hide();
      else $("#agregar_formacion").show();
      sw_modal = false;
      tipo_form = 'estudio';
      listar_formacion();
    });

    $("#Ite_Asc_PF").on("click", function () {
      let add = id_estado_sol != 'Asc_Bor_E' ? true : false;
      if (add) $("#agregar_formacion").hide();
      else $("#agregar_formacion").show();
      sw_modal = false;
      tipo_form = 'producto';
      listar_formacion();
    });

    $("#Ite_Asc_Exp").on("click", function () {
      $("#footer_archivos").html(`<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
      columna = 'experiencia'
      tipo_adjunto = 'solicitud'
      id_item = null;
      id_secc = null;
      listar_archivos_adjuntos();

      $("#btnAgregarAdjunto").off("click");
      $("#btnAgregarAdjunto").click(function () {
        if (num_archivos != 0) {
          myDropzone.processQueue();
        } else {
          MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
        }
      });
    });
  });


}

const listar_archivos_adjuntos = () => {
  $("#modal_archivos_item").modal();
  let item = id_item;
  let id_solicitud = id_global_sol;
  let seccion = id_secc;
  let id_columna = columna;
  let add = id_estado_sol != 'Asc_Bor_E' ? true : false;
  if (add) $("#agregar_archivo").hide();
  else $("#agregar_archivo").show();
  consulta_ajax(`${ruta}listar_archivos_item`, { id_solicitud, item, seccion, id_columna }, resp => {
    const myTable = $("#tabla_archivos_item").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        {
          render: function (data, type, full, meta) {
            let { nombre_guardado, estado } = full;
            if (nombre_guardado == null) return 'N/A';
            else return `<a target='_blank' href='${Traer_Server()}${ruta_archivos_items}${nombre_guardado}' style="background-color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
          }
        },
        {
          data: "archivo"
        },
        {
          data: "fecha_registra"
        },
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $("#agregar_archivo").click(() => {
      sw_modal = true;
      $("#modal_enviar_archivos").modal();
    });
  })
}

const listar_formacion = () => {
  $("#modal_formacion_solicitud").modal()
  let id_solicitud = id_global_sol;
  let formacion = tipo_form;
  consulta_ajax(`${ruta}listar_formacion_solicitud`, { id_solicitud, formacion }, resp => {
    $("#tabla_formacion_solicitud tbody")
      .off("click", "tr td .ver")
      .off("click", "tr td .eliminar")
    const myTable = $("#tabla_formacion_solicitud").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        { defaultContent: '<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control ver"><span >ver</span></span>' },
        { data: "nombre" },
        { data: "nivel_formacion" },
        { data: "fecha_registro" },
        { data: "accion" }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });

    $("#tabla_formacion_solicitud tbody").on("click", "tr td .ver", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      $("#footer_archivos").html(`<button class="btn btn-danger" id="btnAgregarAdjunto"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
      <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
      id_item = id;
      tipo_adjunto = 'formacion';
      id_secc = null;
      listar_archivos_adjuntos();

      $("#btnAgregarAdjunto").off("click");
      $("#btnAgregarAdjunto").click(function () {
        if (num_archivos != 0) {
          myDropzone.processQueue();
        } else {
          MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
        }
      });
    });

    $("#tabla_formacion_solicitud tbody").on("click", "tr td .eliminar", function () {
      let { id } = myTable.row($(this).parent().parent()).data();
      eliminar_formacion(id);
    });
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
    acceptedFiles: "image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
    acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
    autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas
    error: function (response) {
      errores.push(response.xhr.responseText);
    },

    queuecomplete: function (file, response) {
      let errorlist = "No ingresa";
      if (errores.length > 0) {
        if (errores.length == num_archivos) {
          MensajeConClase("Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
        } else {
          errorlist = "";
          for (let index = 0; index < errores.length; index++) {
            errorlist = errorlist + errores[index] + ",";
          }
          MensajeConClase("Informacion almacenada con exito, pero algunos archivos No fueron cargados:\n\n" + errorlist + "\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!", "info", "Oops!");
        }
      } else {
        MensajeConClase("Todos Los archivos fueron cargados.!", "success", "Proceso Exitoso!");
        $("#modal_enviar_archivos").modal('hide');
        if (sw_modal) listar_archivos_adjuntos();
      }
      errores = [];
      num_archivos = 0;
    },

    init: function () {
      num_archivos = 0;
      myDropzone = this;
      this.on("addedfile", function (file) {
        num_archivos++;
      });
      /*
      this.on("removedfile", function (file) {
        num_archivos--;
      });
      */

      myDropzone.on("complete", function (file) {
        myDropzone.removeFile(file);
        cargados++;
      });

      myDropzone.on("processing", function (file) {
        this.options.params = { solicitud: id_global_sol, item: id_item, seccion: id_secc, tipo: tipo_adjunto, columna: columna }
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

const obtener_tipo_formacion = buscar => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_tipo_formacion`;
    consulta_ajax(url, { buscar }, resp => {
      resolve(resp);
    });
  });
};

const obtener_niveles_ingles = buscar => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_niveles_ingles`;
    consulta_ajax(url, { buscar }, resp => {
      resolve(resp);
    });
  });
}

const obtener_categorias_colciencias = buscar => {
  return new Promise(resolve => {
    let url = `${ruta}obtener_categorias_colciencias`;
    consulta_ajax(url, { buscar }, resp => {
      resolve(resp);
    })
  })
}

const listar_tipo_formacion = async () => {
  let formaciones = await obtener_tipo_formacion(52);
  pintar_datos_combo(formaciones, ".cbxformacion", "Seleccione Formación");
}

const listar_niveles_ingles = async () => {
  let niveles = await obtener_niveles_ingles(136);
  pintar_datos_combo_valor(niveles, ".cbxingles", "Selecciona Nivel de Inglés");
}

const listar_categorias_colciencias = async () => {
  let categorias = await obtener_categorias_colciencias(97);
  pintar_datos_combo(categorias, ".cbxcolciencias", "Seleccione su categoria en COLCIENCIAS")
}

const pintar_datos_combo = (datos, combo, mensaje, tipo = 1, sele = "") => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(element => {
    $(combo).append(
      `<option value='${tipo == 2 ? element.id_aux : element.id}' class='${element.id_aux}'> ${element.valor} </option>`
    );
  });
  $(combo).val(sele);
}

const pintar_datos_combo_valor = (datos, combo, mensaje, sele = "") => {
  $(combo).html(`<option value=''> ${mensaje}</option>`);
  datos.forEach(element => {
    $(combo).append(
      `<option value='${element.valor}' class='${element.id_aux}'> ${element.valor} </option>`
    );
  });
  $(combo).val(sele);
}

const guardar_formacion = () => {
  let fordata = new FormData(document.getElementById("form_agregar_formacion"));
  let data = formDataToJson(fordata);
  data.id_solicitud = id_global_sol;
  data.id_tipo_formacion = tipo_form;
  consulta_ajax(`${ruta}guardar_formacion`, data, resp => {
    let { titulo, mensaje, tipo, id_form } = resp;
    if (tipo == "success") {
      tipo_adjunto = 'formacion';
      id_item = id_form;
      if (num_archivos != 0) {
        myDropzone.processQueue();
        $("#form_agregar_formacion").get(0).reset();
        $("#modal_agregar_formacion").modal('hide');
        listar_formacion();
      } else {
        MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
      }
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  });
}

const guardar_informacion_solicitud = (a_experiencia, cat_colciencias, ind_scopus, id_colciencias, ind_value, cvlac) => {
  let id_solicitud = id_global_sol;
  let id_nuevo = cargo_nuevo.id;
  let id_actual = cargo_actual.id;
  let data = {
    id_solicitud,
    a_experiencia,
    cat_colciencias,
    id_colciencias,
    ind_scopus,
    id_actual,
    id_nuevo,
    ind_value,
    cvlac
  }
  consulta_ajax(`${ruta}guardar_informacion_solicitud`, data, resp => {
    let { titulo, mensaje, tipo } = resp;
    if (tipo == "success") {
      MensajeConClase(mensaje, tipo, titulo);
      obtener_informacion_general(id_solicitud);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }
  })
}

const obtener_informacion_general = (id_solicitud) => {
  listar_categorias_colciencias();
  consulta_ajax(`${ruta}obtener_informacion_solicitud`, { id_solicitud }, resp => {
    let { experiencia, categoria_colciencias, indice_scopus, id_tipo, cargo_actual_valor, cargo_nuevo_valor } = resp;
    $("#txt_experiencia_doc").val(experiencia);
    if (id_tipo != 'Asc_Inv') {
      $("#colciencias_select").hide();
      $("#colciencias_url").hide();
    }
    else {
      $("#colciencias_select").show();
      $("#colciencias_url").show();
      $("#colciencias_url").val(categoria_colciencias);
      $("#colciencias_select").val(resp.id_colciencias);
    }
    $("#txt_cargo_actual").val(cargo_actual_valor);
    $("#txt_cargo_nuevo").val(cargo_nuevo_valor);
    $("#txt_indice_scopus").val(indice_scopus);
    $("#txt_indice_scopus_value").val(resp.indice_scopus_valor);
    $("#txt_cvlac").val(resp.cvlac);
  });
}

const cancelar_solicitud = (id) => {
  swal(
    {
      title: `¿Cancelar Solicitud?`,
      text: `Si esta deacuerdo pulse en el botón "Si, Entiendo!", caso contrario pulse en el botón "No, Regresar!"`,
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
        consulta_ajax(`${ruta}cancelar_solicitud`, { id }, resp => {
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

const ver_detalle_solicitud = (data) => {
  $("#modal_detalle_solicitud").modal();
  let { docente, estado, fecha_registro, tipo } = data;
  $("#cont_exp_cvlac").hide();
  $("#cont_scopus").hide();
  $("#cont_colciencias").hide();
  $("#cont_cargo").hide();
  $(".docente").html(docente);
  $(".estado").html(estado);
  $(".fecha_registra").html(fecha_registro);
  $(".tipo_solicitud").html(tipo);
  if (data.experiencia && data.cvlac) $("#cont_exp_cvlac").show();
  if (data.indice_scopus_valor && data.indice_scopus) $("#cont_scopus").show();
  if (data.colciencias && data.categoria_colciencias) $("#cont_colciencias").show();
  if (data.cargo_nuevo_valor && data.cargo_actual_valor) $("#cont_cargo").show();
  $(".experiencia").html(data.experiencia);
  $(".cvlac").html(`<a href="${data.cvlac}">CVLac</a>`);
  $(".indice").html(data.indice_scopus_valor);
  $(".url_scopus").html(`<a href="${data.indice_scopus}">Scopus</a>`);
  $(".colciencias").html(data.colciencias);
  $(".url_colciencias").html(`<a href="${data.categoria_colciencias}">COLCIENCIAS</a>`);
  $(".cargo_actual").html(data.cargo_actual_valor);
  $(".cargo_nuevo").html(data.cargo_nuevo_valor);
  $("#txt_experiencia_doc").attr("readonly");
  $("#txt_indice_scopus").attr("readonly");
  $("#txt_indice_scopus_value").attr("readonly");
  $("#txt_cvlac").attr("readonly");
}

const gestionar_solicitud = (data) => {
  const gestionar_solicitud_normal = (data, title) => {
    swal(
      {
        title,
        text:
          "Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'",
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
          MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
          ejecutar_gestion(data);
        }
      }
    );
  }

  const gestionar_solicitud_texto = (data, title) => {
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
      inputPlaceholder: `Ingrese el observación`,
      inputType: "text",
    }, function (mensaje) {
      if (mensaje === false) return false;
      if (mensaje === "") swal.showInputError(`Debe Ingresar la observación`);
      else {
        data.mensaje = mensaje.trim();
        ejecutar_gestion(data);
      }
    });
  }

  const ejecutar_gestion = (data) => {
    consulta_ajax(`${ruta}gestionar_solicitud`, data, resp => {
      let { titulo, mensaje, tipo } = resp;
      if (tipo == "success") {
        enviar_correo(data);
        listar_solicitudes();
      } else {
        MensajeConClase(mensaje, tipo, titulo);
      }
    });
  }

  if (data.accion == "enviar") gestionar_solicitud_normal(data, '¿ Enviar solicitud ?');
  else if (data.accion == "aceptar") gestionar_solicitud_normal(data, '¿ Aceptar Ascensos ?');
  else if (data.accion == "negar") gestionar_solicitud_texto(data, '¿ Negar Ascensos ?');
  else if (data.accion == "revisar") gestionar_solicitud_texto(data, 'Dejar Observación');
}

const eliminar_formacion = (id) => {
  swal(
    {
      title: `Elimar Formacion`,
      text: `Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'`,
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
        consulta_ajax(`${ruta}eliminar_formacion`, { id }, resp => {
          let = { titulo, mensaje, tipo } = resp;
          if (tipo == 'success') {
            swal.close();
            listar_formacion();
          } else {
            MensajeConClase(mensaje, tipo, titulo);
          }
        })
      }
    }
  );
}

const obtener_correo_talento = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_correos`, { id }, resp => {
      resolve(resp);
    });
  });
}

const obtener_observacion = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_observacion`, { id }, resp => {
      resolve(resp);
    });
  });
}

const obtener_info_docente = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_info_docente`, { id }, resp => {
      resolve(resp);
    })
  })
}

const enviar_correo = async (data) => {
  let { id, estado, docente, correo, id_docente, cargo_nuevo } = data;
  let ser = `<a href="${server}index.php/ascensos/${id}"><b>agil.cuc.edu.co</b></a>`;
  let tipo = -1;
  let titulo = `Ascensos CUC`;
  let nombre = docente;
  let correo_talento_humano = await obtener_correo_talento(id);
  let observacion = await obtener_observacion(id);
  let info_docente = await obtener_info_docente(id_docente);
  let { departamento } = info_docente;
  let correo_end = correo;
  let body = `Se informa que su solicitud de Ascenso, fue recibida y se encuentra en proceso de verificaci&oacute;n, usted puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra su solicitud.
    <br>
    <br>
    M&aacute;s informaci&oacute;n en : ${ser}`;
  let sw_mail = false;
  if (estado == 'Asc_Env_E') {
    tipo = 1;
    correo_end = correo;
    sw_mail = true;
  } else if (estado == 'Asc_Ace_E') {
    body_docente = `Se informa que su solicitud de Ascenso fue aprobada. El departamento de Talento Humano ya se encuentra notificado y pr&oacute;ximamente se estar&aacute; comunicando con usted.`
    body_talento_humano = `Se informa que el siguiente docente se le ha aprobado un ascenso 
    <br>
    <br>
    Docente: ${docente}
    <br>
    Departamento: ${departamento}
    <br>
    Cargo al que Asciende: ${cargo_nuevo}
    `;
    enviar_correo_personalizado("Asc", body_docente, correo, nombre, "Ascensos CUC", titulo, "ParCodAdm", 1);
    enviar_correo_personalizado("Asc", body_talento_humano, correo_talento_humano, 'Talento Humano', "Ascensos CUC", titulo, "ParCodAdm", 1);
  } else if (estado == 'Asc_Bor_E') {
    body = `Su solicitud presento un inconveniente debido a que: ${observacion}, por favor contin&uacute;e gestionando su solicitud en el aplicativo AGIL 
    <br>
    <br>
    M&aacute;s informaci&oacute;n en : ${ser}`;
    sw_mail = true;
  } else if (estado == 'Asc_Neg_E') {
    body = `Se informa que su solicitud de Ascenso fue negada debido a que: ${observacion}.`;
    sw_mail = true;
  }
  if (sw_mail) enviar_correo_personalizado("Asc", body, correo, nombre, "Ascenos CUC", titulo, "ParCodAdm", 1);
}

const listar_historial_estados = () => {
  $("#modal_historial_solicitud").modal();
  let id_solicitud = id_global_sol;
  consulta_ajax(`${ruta}listar_historial_estados`, { id_solicitud }, resp => {
    let i = 0;
    const myTable = $("#tabla_estado_solicitud").DataTable({
      destroy: true,
      searching: false,
      processing: true,
      data: resp,
      columns: [
        {
          render: function (data, type, full, meta) {
            i++;
            return i;
          }
        },
        { data: "estado" },
        { data: "fecha_registra" },
        { data: "nombre_completo" },
        { data: "observaciones" }
      ],
      language: idioma,
      dom: "Bfrtip",
      buttons: []
    });
  });
}

const obtener_tipos_solicitud = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_tipos_solicitud`, { id }, resp => {
      resolve(resp);
    })
  })
}

const obtener_estados_solicitud = (id) => {
  return new Promise(resolve => {
    consulta_ajax(`${ruta}obtener_estados_solicitud`, { id }, resp => {
      resolve(resp);
    })
  })
}

const listar_tipos_solicitud = async () => {
  let tipos = await obtener_tipos_solicitud(131)
  pintar_datos_combo(tipos, ".cbxtipos", "Seleccione Tipo", 2)
}

const listar_estados_solicitud = async () => {
  let estados = await obtener_estados_solicitud(132)
  pintar_datos_combo(estados, ".cbxestados", "Seleccione Estado", 2)
}
