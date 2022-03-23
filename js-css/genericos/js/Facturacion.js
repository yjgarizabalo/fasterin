let data_solicitante = { 'nombre': null, 'correo': null };
let url = `${Traer_Server()}index.php/facturacion_control/`;
let url_genericas = `${Traer_Server()}index.php/genericas_control/`;
let callbak_activo = (resp) => { };
let idparametro_activo = null;
let datos_solicitud = { 'id_codigo_sap': null, 'id_empresa': null };
let checkbox = 0;
let modcheckbox = 0;
let id_solicitud = null;
let estado_solicitud = null;
let ruta_banco = 'archivos_adjuntos/facturacion/banco/';
let ruta_facturas = 'archivos_adjuntos/facturacion/facturas/';
let sw = false;
let adj_rut_global = '';
let empresa_global = '';
let adm_activo = null;
let tipo_correo = null;
let tiempo = 10000;


$(document).ready(function () {
  let get_milisegundos = new Promise(resolve => {
    consulta_ajax(`${url}milisegundos`, {}, ({ valor }) => resolve(valor));
  });

  get_milisegundos.then(miliseg => activar_notificaciones(miliseg));
  $("#nueva_factura").submit(e => {
    guardarFactura();
    return false;
  });

  $("#form_guardar_valor_parametro").submit(() => {
    guardar_valor_parametro();
    return false;
  });

  $("#form_modificar_valor_parametro").submit(() => {
    modificar_valor_parametro();
    return false;
  });

  $("#form_gestion_aprobar").submit(e => {
    guardar_factura_aprobada();
    listar_facturas();
    return false;
  });
  $("#form_agregar_empresa_banco").submit(e => {
    guardar_empresa();
    return false;
  });


  $("#agregar_factura").click(() => {
    $("#div_empresa").show();
    $("#empresa_mensaje").hide('slow');
    $(".div_adj_rut").hide('slow');
    $("#Btncod_orden_sele").html('Seleccione Codigo SAP');
    $("#Btnbuscar_empresa").html('Buscar Empresa');
    $("#txtcodigo_sap").val('');
    $(".banco_cuenta").removeAttr('required').val('');
    $("#content").hide('slow');
    $("#nueva_factura").get(0).reset();
    $("#modal_factura").modal();
    $("#sin_cuenta").prop("checked", true);
    datos_solicitud.id_empresa = '';
    datos_solicitud.id_codigo_sap = '';

  });
  $("#agregar_empresa").click(() => {
    $("#modal_agregar_empresa_banco").modal();

  });


  $("#Buscar_Codigo_Orden").submit(e => {
    const codigo = $("#txtcodigo_sap").val();
    buscar_valor_parametro(codigo, idparametro_activo);
    e.preventDefault();
  });
  $("#buscar_cod_sap").click(() => {
    const codigo = $("#txtcodigo_sap").val();
    buscar_valor_parametro(codigo, idparametro_activo);
    e.preventDefault();
  });

  $("#Btncod_orden_sele").click(() => {
    configurar_codigo('hide', 'hide', 'agregar')
  });

  $("#Btnbuscar_cod").click(() => {
    configurar_codigo('hide', 'hide', 'agregar')
  });

  $("#Btnbuscar_empresa").click(() => {
    configurar_empresa('show');
  });

  $("#btn_modificar_empresa").click(() => {
    $("#adj_rut_mensaje").hide();
    $("#adj_rut_mensaje_mod").show();
    buscar_empresa()
  });

  $("#Btnbuscar_emp").click(() => {
    configurar_empresa('show');
  });

  $("#adj_rut_mensaje").click(() => {
    $("#adj_rut_input").attr('required', 'true')
    $("#Btnbuscar_empresa").html('Buscar Empresa');
    datos_solicitud.id_empresa = '';
    configurar_empresa('', 'hide', 'agregar')
  });
  $("#adj_rut_mensaje_mod").click(() => {
    $("#adj_rut_input_mod").val(adj_rut_global);
    configurar_empresa('', 'hide', 'modificar');

  });

  $("#empresa_mensaje").click(() => {
    sw = false;
    $("#adj_rut").val('');
    $("#div_empresa").show();
    $("#empresa_mensaje").hide('slow');
    $(".div_adj_rut").hide('slow');
    $("#adj_rut_input").removeAttr('required').val('');

  });


  $("#empresa_mensaje_mod").click(() => {
    mensaje_empresa_existe();

  });

  $("#adj_rut_mensaje_mod").click(() => {
    $("#adj_rut_input_mod").attr('required', 'true')
    $("#btn_modificar_empresa").html('Buscar Empresa');
    datos_solicitud.id_empresa = '';

  });

  $("#con_cuenta").click(() => {
    checkbox = 1;
    $("#content").show('slow');
    $(".banco_cuenta").attr('required', 'true')
  });
  $("#sin_cuenta").click(() => {
    checkbox = 0;
    $(".banco_cuenta").removeAttr('required').val('');
    $("#content").hide('slow');

  });
  $("#con_cuenta_mod").click(() => {
    modcheckbox = 1;
    $("#contenido").show('slow');
    $(".banco_cuenta").attr('required', 'true')

  });
  $("#sin_cuenta_mod").click(() => {
    modcheckbox = 0;
    $(".banco_cuenta").removeAttr('required').val('');
    $("#contenido").hide('slow');

  });

  $(".regresar_menu").click(function () {
    $("#container-listado-facturas").css("display", "none");
    $("#menu_principal").fadeIn(1000);

  });
  $("#ver_estados").click(function () {
    listar_estados(id_solicitud);
    $("#modal_listar_estados").modal();
  });

  $("#listado_solicitudes").click(function () {
    administrar_facturas(1);

  });

  $("#btnfiltrar").click(() => {
    listar_facturas();
  });

  $("#limpiar_filtros").click(() => {
    limpiar_filtros();
    listar_facturas();
  });

  $("#btn_modificar_evento").click(() => {
    mensaje_empresa_existe();
    if (id_solicitud == null) MensajeConClase('Seleccione solicitud a modificar', 'info', 'Oops.!');
    else if (estado_solicitud != 'Fact_Sol') MensajeConClase('No es posible realizar esta acción ya que La solicitud se encuentra terminada.', 'info', 'Oops.!');
    else modificar_solicitud(id_solicitud);

  });

  $("#btn_modificar_cod").click(() => {
    idparametro_activo = 25;
    callbak_activo = (resp) => {
      let { id, nombre } = resp;
      datos_solicitud.id_codigo_sap = id;
      $("#btn_modificar_cod").html(nombre);
      $("#Buscar_Codigo").modal('hide');
      $("#txtcodigo_sap").val('');
    }
    $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Codigo SAP');
    $("#Buscar_Codigo").modal();
    buscar_valor_parametro('$$$$++1');
  });


  $("#btn_buscar_mod").click(() => {
    idparametro_activo = 25;
    callbak_activo = (resp) => {
      let { id, nombre } = resp;
      datos_solicitud.id_codigo_sap = id;
      $("#btn_modificar_cod").html(nombre);
      $("#Buscar_Codigo").modal('hide');
      $("#txtcodigo_sap").val('');
    }
    $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Codigo SAP');
    $("#Buscar_Codigo").modal();
    buscar_valor_parametro('$$$$++1');
  });

  $("#btn_modificar_empresa").click(() => {
    idparametro_activo = 106;
    callbak_activo = (resp) => {
      let { id, nombre } = resp;
      datos_solicitud.id_empresa = id;
      $("#btn_modificar_empresa").html(nombre);
      $("#Buscar_Codigo").modal('hide');
      $("#txtcodigo_sap").val('');
    }
    $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Empresa');
    $("#Buscar_Codigo").modal();
    buscar_valor_parametro('$$$$+11');
  });
  $("#btn_buscar_emp_mod").click(() => {
    idparametro_activo = 106;
    callbak_activo = (resp) => {
      let { id, nombre } = resp;
      datos_solicitud.id_empresa = id;
      $("#btn_modificar_empresa").html(nombre);
      $("#Buscar_Codigo").modal('hide');
      $("#txtcodigo_sap").val('');
    }
    $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Empresa');
    $("#Buscar_Codigo").modal();
    buscar_valor_parametro('$$$$+11');
  });

  $("#modificar_factura").submit(e => {
    modificarFactura(id_solicitud);
    return false;
  });
  $("#btn_admin_solicitudes").click(() => {
    administrar_modulo('banco', 109);
    $("#Modal_administrar_solicitudes").modal("show");
  });

  $('#admin_banco').click(function () {
    $("#nav_admin_compras li").removeClass("active");
    $(this).addClass("active");
    administrar_modulo('banco', 109);
  });
  $('#admin_empresa').click(function () {
    $("#nav_admin_compras li").removeClass("active");
    $(this).addClass("active");
    administrar_modulo('empresa', 106);
  });
});

function buscar_empresa() {
  idparametro_activo = 106;
  callbak_activo = (resp) => {
    let { id, nombre } = resp;
    datos_solicitud.id_empresa = id;
    $("#Btnbuscar_empresa").html(nombre);
    $("#Buscar_Codigo").modal('hide');
    $("#txtcodigo_sap").val('');
  }
  $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Empresa');
  $("#Buscar_Codigo").modal();
  buscar_valor_parametro('$$$$+11');

}
function buscar_codigo() {
  idparametro_activo = 25;
  callbak_activo = (resp) => {
    let { id, nombre } = resp;
    datos_solicitud.id_codigo_sap = id;
    $("#Btncod_orden_sele").html(nombre);
    $("#Buscar_Codigo").modal('hide');
    $("#txtcodigo_sap").val('');
  }
  $("#Buscar_Codigo .modal-title").html('<span class="fa fa-search"></span> Buscar Codigo SAP');
  $("#Buscar_Codigo").modal();
  buscar_valor_parametro('$$$$++1');
}
function guardarFactura() {
  const link = `${url}guardarFactura`;
  let { id_empresa, id_codigo_sap } = datos_solicitud;
  const data = new FormData(document.getElementById("nueva_factura"));
  data.append('id_codigo_sap', id_codigo_sap);
  data.append('id_empresa', id_empresa);
  data.append('checkbox', checkbox);

  enviar_formulario(link, data, resp => {
    let { mensaje, titulo, tipo, id } = resp;

    if (tipo == 'success') {
      listar_facturas();
      enviar_correo_estado('Fact_Sol', id, '');
      $("#modal_factura").modal('hide');
      $("#nueva_factura").get(0).reset();
      $("#Btncod_orden_sele").text('Seleccione Código SAP');
      $("#Btnbuscar_empresa").text('Buscar Empresa');
      $("#content").hide('');
      checkbox = 0;
    }
    MensajeConClase(mensaje, tipo, titulo);
  });
}

const buscar_valor_parametro = (codigo = '', idparametro = '') => {

  const link = `${url}buscar_valor_parametro`;
  consulta_ajax(link, { codigo, idparametro }, resp => {
    let { data, mensaje, titulo, tipo } = resp;
    if (tipo != 'success') MensajeConClase(mensaje, tipo, titulo);
    $('#tabla_codigos tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .seleccionar');
    const myTable = $("#tabla_codigos").DataTable({
      destroy: true,
      data,
      processing: true,
      searching: false,
      columns: [
        { data: "nombre" },
        { data: "descripcion" },
        { 'defaultContent': '<span style="color: #39B23B;" title="Seleccionar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>' }
      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": [],
    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $('#tabla_codigos tbody').on('click', 'tr', function () {
      $("#tabla_codigos tbody tr").removeClass("warning");
      $(this).addClass("warning");
    });

    $('#tabla_codigos tbody').on('dblclick', 'tr', function () {
      const data = myTable.row(this).data();
      callbak_activo(data);
    });

    $('#tabla_codigos tbody').on('click', 'tr td .seleccionar', function () {
      const data = myTable.row($(this).parent().parent()).data();
      callbak_activo(data);
    });

  });
}

const administrar_facturas = item => {
  if (item == 1) {
    $("#menu_principal").css("display", "none");
    $("#container-listado-facturas").fadeIn(1000);
    limpiar_filtros();
  }
}

const listar_facturas = (id = '', push = false) => {
  if (id) administrar_facturas(1);
  let estado = $("#estado_filtro").val();
  let empresa = $("#empresa_filtro").val();
  let banco = $("#banco_filtro").val();
  let plazo = $("#plazo_filtro").val();
  let fecha = $("#fecha_filtro").val();


  const link = `${url}listar_facturas`;
  consulta_ajax(link, { id, estado, empresa, banco, plazo, fecha }, ({ notifica, data }) => {
    if (notifica && push) mostrar_notificacion('Modulo Facturación', `Tiene ${notifica} Facturas Pendientes.`);
    $('#tabla_facturas  tbody')
      .off('dblclick', 'tr')
      .off('click', 'tr')
      .off('click', 'tr td:nth-of-type(1)')
      .off('click', 'tr .tramitar')
      .off('click', 'tr .negar')
      .off('click', 'tr .copiar')
      .off('click', 'tr .cancelar')
      .off('click', 'tr .aprobar');


    const myTable = $("#tabla_facturas").DataTable({
      destroy: true,
      data,
      processing: true,
      searching: true,
      columns: [
        { data: 'ver' },
        { data: 'persona' },
        { data: 'fecha_registra' },
        { data: 'plazo' },
        { data: 'state' },
        { data: 'accion' },
      ],
      "language": get_idioma(),
      dom: 'Bfrtip',
      "buttons": get_botones(),
    });

    //EVENTOS DE LA TABLA ACTIVADOS
    $('#tabla_facturas tbody').on('click', 'tr', function () {
      $("#tabla_facturas tbody tr").removeClass("warning");
      $(this).addClass("warning");
      let { id, persona, correo, id_estado_solicitud } = myTable.row(this).data();
      estado_solicitud = id_estado_solicitud;
      data_solicitante = { 'nombre': persona, correo }
      id_solicitud = id;
    });


    $('#tabla_facturas tbody').on('dblclick', 'tr', function () {
      const data = myTable.row(this).data();
      modal_detalle_factura(data);
    });

    $('#tabla_facturas tbody').on('click', 'tr td:nth-of-type(1)', function () {
      let data = myTable.row($(this).parent()).data();
      modal_detalle_factura(data);
      id_solicitud = data.id;

    });

    $('#tabla_facturas tbody').on('click', 'tr .aprobar', function () {
      let { id, id_estado_solicitud, tipo_entrega_aux } = myTable.row($(this).parent()).data();
      id_solicitud = id;
      estado_solicitud = id_estado_solicitud;
      tipo_correo = 'Fact_Fin';
      gestion_aprobar(id_solicitud, tipo_entrega_aux);

    });
    $('#tabla_facturas tbody').on('click', 'tr .tramitar', function () {
      let { id, id_estado_solicitud } = myTable.row($(this).parent()).data();
      let { state } = myTable.row($(this).parent()).data();
      estado_solicitud = state;
      finalizar_solicitud(id, 'Fact_Tra', "Tramitar Solicitud", "Está seguro que desea tramitar la solicitud?");
      cambiar_estado_mensaje(id_estado_solicitud);
    });

    $('#tabla_facturas tbody').on('click', 'tr .negar', function () {
      let { id } = myTable.row($(this).parent()).data();
      let { state } = myTable.row($(this).parent()).data();
      estado_solicitud = state;
      tipo_correo = 'Fact_Neg';
      finalizar_solicitud(id, 'Fact_Neg', "Negar Solicitud", "Está seguro que desea negar la solicitud?");
    });
    $('#tabla_facturas tbody').on('click', 'tr .cancelar', function () {
      let { id, id_estado_solicitud } = myTable.row($(this).parent()).data();
      let { state } = myTable.row($(this).parent()).data();
      estado_solicitud = state;
      finalizar_solicitud(id, 'Fact_Can', "Cancelar Solicitud", "Está seguro que desea cancelar la solicitud?");
      cambiar_estado_mensaje(id_estado_solicitud);
    });
    $('#tabla_facturas tbody').on('click', 'tr .copiar', function () {
      let { id } = myTable.row($(this).parent()).data();
      guardar_solicitud_copia(id);
    });



    const modal_detalle_factura = ({
      empresa,
      sap,
      valor,
      concepto,
      plazo,
      banco,
      tipo,
      num_cuenta,
      fecha_registra,
      state,
      persona,
      adj_banco,
      adj_rut,
      adj_aprobado,
      id_estado_solicitud,
      msj_negado,
      tipo_entrega
    }) => {
      empresa ? $(".empresa").html(empresa) : $(".empresa").html('---');
      $(".sap").html(sap);
      $(".valor").html("$ " + valor);
      $(".concepto").html(concepto);
      $(".plazo").html(plazo);
      banco ? $(".tr_banco").show() : $(".tr_banco").hide();
      $(".banco").html(banco);
      $(".tipo").html(tipo);
      $(".num_cuenta").html(num_cuenta);
      $(".fecha_registra").html(fecha_registra);
      $(".estado_factura").html(state);
      $(".negada").html(msj_negado);
      $(".nombre_solicitante").html(persona);
      $(".nombre_solicitante").html(persona);
      $(".tipo_entrega").html(tipo_entrega);
      $("#ver_adjuntos_lista").attr('href', `${Traer_Server()}${ruta_banco}${adj_banco}`);
      $("#ver_adjuntos_factura").attr('href', `${Traer_Server()}${ruta_facturas}${adj_aprobado}`);

      if (adj_rut) {
        $("#ver_adjuntos_rut").attr('href', `${Traer_Server()}${ruta_banco}${adj_rut}`);
        $(".tr_rut").show();
      }
      else $(".tr_rut").hide();


      if (id_estado_solicitud == 'Fact_Neg')
        $(".tr_negada").show()
      else $(".tr_negada").hide();
      $("#modal_detalle_factura").modal();


      if (id_estado_solicitud == 'Fact_Fin')
        $(".tr_banco_factura").show()
      else $(".tr_banco_factura").hide();
      $("#modal_detalle_factura").modal();
    }
  });


}




const modificarFactura = id_solicitud => {
  const link = `${url}modificarFactura`;
  let { id_empresa, id_codigo_sap } = datos_solicitud;
  const data = new FormData(document.getElementById("modificar_factura"));
  data.append("id", id_solicitud);
  data.append('id_codigo_sap', id_codigo_sap);
  data.append('id_empresa', id_empresa);
  data.append('modcheckbox', modcheckbox);
  data.append('adj_rut_global', adj_rut_global);


  enviar_formulario(link, data, resp => {
    let { mensaje, titulo, tipo } = resp;
    if (tipo == 'success') {
      $("#modificar_factura")[0].reset();
      $("#modal_modificar_factura").modal('hide');
      $("#btn_modificar_cod").text('Seleccione Código SAP');
      $("#btn_modificar_empresa").text('Buscar Empresa');
      $("#contenido").hide('');
      modcheckbox = 0;
      listar_facturas();
    }
    MensajeConClase(mensaje, tipo, titulo);
  });


}
const cambiarEstado = (id, estado, mensaje_c = '') => {

  const link = `${url}cambiarEstado`;
  consulta_ajax(link, { id, estado, id, "mensaje": mensaje_c }, data => {
    const { titulo, mensaje, tipo } = data;
    if (tipo == 'success') {
      swal.close();
      listar_facturas();
      id_solicitud = null;
      $("#tabla_facturas tbody tr").removeClass("warning");
      enviar_correo_estado(tipo_correo, id, mensaje_c);
    } else {
      MensajeConClase(mensaje, tipo, titulo);
    }

  });
}
const cambiar_estado_mensaje = (estado) => {
  if (estado != 'Fact_Sol')
    MensajeConClase('No es posible realizar esta acción ya que La solicitud se encuentra terminada.', 'info', 'Oops.!');
}
const finalizar_solicitud = (id, estado, title, mensaje) => {

  const gestionar_solicitud = (id, estado, title) => {
    swal({
      title: title,
      text: mensaje,
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#D9534F",
      confirmButtonText: "Si, continuar!",
      cancelButtonText: "No, cancelar!",
      allowOutsideClick: true,
      closeOnConfirm: false,
      closeOnCancel: true
    },
      isConfirm => {
        if (isConfirm) {
          cambiarEstado(id, estado, title);

        }

      });
  }
  const gestionar_solicitud_texto = (id, estado) => {

    swal({
      title: '¿ Negar Solicitud ?',
      text: "Si desea negar la solicitud debe llenar el siguiente campo.",
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
        cambiarEstado(id, estado, mensaje);
        return false;
      }
    });
  }
  if (estado == 'Fact_Neg') gestionar_solicitud_texto(id, estado);
  else gestionar_solicitud(id, estado, title, mensaje);
}
function gestion_aprobar(id_solicitud, id_tipo_entrega) {

  if (id_tipo_entrega == 'Fact_Per') {
    swal({
      title: 'Aprobar Solicitud',
      text: 'Esta seguro que desea aprobar la solicitud?',
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#D9534F",
      confirmButtonText: "Si, finalizar!",
      cancelButtonText: "No, cancelar!",
      allowOutsideClick: true,
      closeOnConfirm: true,
      closeOnCancel: true
    },
      isConfirm => {
        if (isConfirm) {
          cambiarEstado(id_solicitud, 'Fact_Fin');
        }
      });
  } else {
    $("#modal_gestion_aprobar").modal();

  }
}

function guardar_factura_aprobada() {
  swal({
    title: 'Finalizar Solicitud',
    text: 'Esta seguro que desea finalizar la solicitud ?',
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#D9534F",
    confirmButtonText: "Si, finalizar!",
    cancelButtonText: "No, cancelar!",
    allowOutsideClick: true,
    closeOnConfirm: false,
    closeOnCancel: true
  },
    isConfirm => {
      if (isConfirm) {
        const link = `${url}guardar_factura_aprobada`;
        const data = new FormData(document.getElementById("form_gestion_aprobar"));
        data.append("id", id_solicitud);

        enviar_formulario(link, data, resp => {
          let { mensaje, titulo, tipo } = resp;
          if (tipo == 'success') {
            swal.close();
            listar_facturas();
            $("#modal_gestion_aprobar").modal('hide');
            id_solicitud = null;
            $("#form_gestion_aprobar").get(0).reset();
          } else {
            MensajeConClase(mensaje, tipo, titulo);
          }
        });
      }
    });
}

const listar_estados = id_solicitud => {
  const link = `${url}listar_estados`;
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


const modificar_solicitud = id => {
  modcheckbox = 0;
  $("#modificar_factura").get(0).reset();
  $("#ver_banco_modi").removeAttr('href').val('');

  consulta_ajax(`${url}consulta_solicitud_id`, { id }, (resp) => {
    let { id_codigo_sap,
      id_empresa,
      empresa,
      sap,
      valor,
      concepto,
      id_plazo,
      id_banco,
      id_tipo_entrega,
      id_tipo_cuenta,
      num_cuenta,
      adj_banco,
      adj_rut
    } = resp
    adj_rut_global = adj_rut;
    empresa_global = empresa;

    datos_solicitud.id_codigo_sap = id_codigo_sap;
    datos_solicitud.id_empresa = id_empresa ? id_empresa : '';

    $("#btn_modificar_cod").text(sap);
    $("#btn_modificar_empresa").text(empresa);
    $("#modificar_factura input[name='valor_mod']").val(valor);
    $("#modificar_factura textarea[name='concepto_mod']").val(concepto);
    $("#modificar_factura select[name='id_plazo_mod']").val(id_plazo);
    $("#modificar_factura select[name='id_entrega_mod']").val(id_tipo_entrega);

    if (id_banco) {
      modcheckbox = 1;

      $("#con_cuenta_mod").prop("checked", true);
      $("#contenido").show('fast');
      $(".banco_cuenta").attr('required', 'true')
      $("#modificar_factura select[name='id_banco_mod']").val(id_banco);
      $("#modificar_factura select[name='id_tipo_cuenta_mod']").val(id_tipo_cuenta);
      $("#modificar_factura input[name='num_cuenta_mod']").val(num_cuenta);
      $("#text_adj_banco").val(adj_banco);
      $("#ver_banco_modi").attr('href', `${Traer_Server()}${ruta_banco}${adj_banco}`);

    } else {
      $("#sin_cuenta_mod").prop("checked", true);
      $(".banco_cuenta").removeAttr('required').val('');
      $("#contenido").hide('fast');
    }

    if (adj_rut) {
      sw = true;
      $("#div_rut").show('fast');
      $("#div_empresa_mod").hide();
      $(".div_adj_rut_mod").show('fast');
      $("#empresa_mensaje_mod").show('fast');
      $("#ver_rut_modi").attr('href', `${Traer_Server()}${ruta_banco}${adj_rut}`);
      $("#adj_rut_input_mod").val(adj_rut);

    } else {
      sw = false;
      $("#div_rut").hide('fast');
      $("#ver_rut_modi").removeAttr('href').val('');

    }
    $("#modal_modificar_factura").modal();
  });
}

const guardar_empresa = () => {
  swal({
    title: 'Agregar Empresa',
    text: 'Esta seguro que desea agregar una nueva empresa?',
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#D9534F",
    confirmButtonText: "Si, continuar!",
    cancelButtonText: "No, cancelar!",
    allowOutsideClick: true,
    closeOnConfirm: false,
    closeOnCancel: true
  },
    isConfirm => {
      if (isConfirm) {
        const link = `${url_genericas}guardar_valor_Parametro`;
        const data = new FormData(document.getElementById("form_agregar_empresa_banco"));
        data.append("idparametro", 106);

        enviar_formulario(link, data, resp => {
          $("#modal_agregar_empresa_banco").modal('hide');
          $("#form_agregar_empresa_banco").get(0).reset();
          MensajeConClase('Empresa agregada exitosamente', 'success', 'Proceso Exitoso.!');
        });

      }
    });
}

const guardar_solicitud_copia = (id) => {
  swal({
    title: '¿ Copiar Solicitud ?',
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
        copiar_solicitud(id);
      }
    });
}

const copiar_solicitud = id => {
  consulta_ajax(`${url}consulta_solicitud_id`, { id }, (resp) => {
    let { id_codigo_sap,
      id_empresa,
      empresa,
      sap,
      valor,
      concepto,
      id_plazo,
      id_banco,
      id_tipo_cuenta,
      num_cuenta,
      id_tipo_entrega,
      adj_banco } = resp

    datos_solicitud.id_codigo_sap = id_codigo_sap;
    datos_solicitud.id_empresa = id_empresa ? id_empresa : '';

    $("#Btncod_orden_sele").text(sap);
    (empresa) ? $("#Btnbuscar_empresa").text(empresa) : $("#Btnbuscar_empresa").text('Buscar empresa');
    $("#nueva_factura input[name='valor']").html("$ " + valor);
    $("#nueva_factura textarea[name='concepto']").val(concepto);
    $("#nueva_factura select[name='id_plazo']").val(id_plazo);
    $("#nueva_factura select[name='id_entrega']").val(id_tipo_entrega);

    if (id_banco) {
      checkbox = 1;

      $("#con_cuenta").prop("checked", true);
      $("#content").show('fast');
      $(".banco_cuenta").attr('required', 'true')
      $("#nueva_factura select[name='id_banco']").val(id_banco);
      $("#nueva_factura select[name='id_tipo_cuenta']").val(id_tipo_cuenta);
      $("#nueva_factura input[name='num_cuenta']").val(num_cuenta);
      $("#text_adj_banco").val(adj_banco);
      $("#ver_banco").attr('href', `${Traer_Server()}${ruta_banco}${adj_banco}`);

    } else {
      $("#sin_cuenta").prop("checked", true);
      $(".banco_cuenta").removeAttr('required').val('');
      $("#content").hide('fast');
    }
    $("#modal_factura").modal();
  });
}
const limpiar_filtros = () => {
  $("#estado_filtro").val('');
  $("#banco_filtro").val('');
  $("#empresa_filtro").val('');
  $("#plazo_filtro").val('');
  $("#fecha_filtro").val('');
}

const configurar_empresa = (modal_codigo = '', mensaje_codigo = '', tipo = '') => {

  if (modal_codigo == 'show') {
    $("#adj_rut_mensaje").show();
    $("#adj_rut_mensaje_mod").hide();
    $("#Buscar_Codigo_Orden")[0].reset();
    buscar_empresa()
  }
  if (mensaje_codigo == 'hide') {
    sw = true;
    $("#Buscar_Codigo").modal('hide');
    if (tipo == 'agregar') {
      $("#div_empresa").hide();
      $(".div_adj_rut").show('slow');
      $("#empresa_mensaje").show('slow');
    } else {
      $("#div_rut").show('fast');
      $("#div_rut").removeClass("oculto");
      $("#div_empresa_mod").hide();
      $(".div_adj_rut_mod").show('fast');
      $("#empresa_mensaje_mod").show('fast');
    }
  }
}
const configurar_codigo = (modal_codigo = '', mensaje_codigo = '', tipo = '') => {

  if (modal_codigo == 'hide') {
    $("#adj_rut_mensaje").hide();
    $("#Buscar_Codigo_Orden")[0].reset();
    buscar_codigo();
  }

  if (mensaje_codigo == 'hide' && tipo == 'agregar') {
    $("#adj_rut_mensaje_mod").hide();

  }
}

const mensaje_empresa_existe = () => {
  sw = false;
  empresa_ = empresa_global ? empresa_global : 'Buscar empresa';
  $("#btn_modificar_empresa").html(empresa_);
  $("#div_empresa_mod").show();
  $("#empresa_mensaje_mod").hide('fast');
  $(".div_adj_rut_mod").hide('fast');
  $("#adj_rut_input_mod").removeAttr('required').val('');
}

const administrar_modulo = (tipo, parametro = '') => {
  adm_activo = {
    tipo,
    parametro,
    'valor_parametro': null
  };
  listar_valores_parametros(parametro);
  if (tipo == 'banco') {
    $("#container_admin_valores").css("display", "none");
    $("#modal_nuevo_valor .modal-title").html('<span class="fa fa-pencil-square-o"></span> Nuevo Banco');
    $("#ModalModificarParametro .modal-title").html('<span class="fa fa-pencil-square-o"></span> Modificar Banco');
    $("#nombre_tabla_cu_or").html('TABLA BANCOS');
    $("#container_admin_valores").fadeIn(1000);
    $("#container_admin_comite").css("display", "none");
  } else if (tipo == 'empresa') {
    $("#container_admin_valores").css("display", "none");
    $("#modal_nuevo_valor .modal-title").html('<span class="fa fa-fax "></span> Nueva Empresa');
    $("#ModalModificarParametro .modal-title").html('<span class="fa fa-fax "></span> Modificar Empresa');
    $("#nombre_tabla_cu_or").html('TABLA EMPRESAS');
    $("#container_admin_valores").fadeIn(1000);
    $("#container_admin_comite").css("display", "none");
  }

}

const guardar_valor_parametro = () => {
  let url = `${Traer_Server()}index.php/genericas_control/guardar_valor_Parametro`;
  let data = new FormData(document.getElementById("form_guardar_valor_parametro"));
  data.append("idparametro", adm_activo.parametro);
  enviar_formulario(url, data, (resp) => {
    if (resp == "sin_session") {
      close();
    } else if (resp == 1) {
      MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
    } else if (resp == 2) {
      $("#form_guardar_valor_parametro").get(0).reset();
      MensajeConClase("", "success", "Datos Guardados!");
      listar_valores_parametros(adm_activo.parametro);
    } else if (resp == 3) {
      MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al Guardar la información, contacte con el administrador.", "error", "Oops...");
    }
  })
}

const listar_valores_parametros = idparametro => {
  $('#tabla_valores_parametros tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
  let myTable = $("#tabla_valores_parametros").DataTable({
    "destroy": true,
    "ajax": {
      url: `${Traer_Server()}index.php/genericas_control/Cargar_valor_Parametros/true/2`,
      dataType: "json",
      type: "post",
      data: {
        idparametro
      },
      "dataSrc": function (json) {
        return json.length == 0 ? Array() : json.data;
      },
    },
    "processing": true,
    "columns": [{

      "render": function (data, type, full, meta) {
        return `<span  style="background-color:white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>`;
      }
    }, {
      "data": "valor"
    },
    {
      "data": "valorx"
    },
    {
      "data": "op"
    },
    ],
    "language": idioma,
    dom: 'Bfrtip',
    "buttons": []
  });

  $('#tabla_valores_parametros tbody').on('click', 'tr', function () {
    let data = myTable.row(this).data();
    $("#tabla_valores_parametros tbody tr").removeClass("warning");
    $(this).attr("class", "warning");
    adm_activo.valor_parametro = data.id;
  });
  $('#tabla_valores_parametros tbody').on('dblclick', 'tr', function () {
    let data = myTable.row(this).data();
    ver_detalle_parametro(data);
  });

  $('#tabla_valores_parametros tbody').on('click', 'tr td:nth-of-type(1)', function () {
    let data = myTable.row($(this).parent()).data();
    ver_detalle_parametro(data);
  });

}

const modificar_valor_parametro = () => {
  let url = `${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`;
  let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
  data.append("idparametro", adm_activo.valor_parametro);
  enviar_formulario(url, data, (resp) => {
    if (resp == "sin_session") {
      close();
    } else if (resp == 1) {
      $("#form_modificar_valor_parametro").get(0).reset();
      $("#ModalModificarParametro").modal("hide");
      MensajeConClase("", "success", "Datos Modificados!");
      listar_valores_parametros(adm_activo.parametro);
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
      //MensajeConClase("", "success", "Dato Eliminado!");
      swal.close();
      listar_valores_parametros(adm_activo.parametro);
    } else if (resp == -1302) {
      MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
    } else {
      MensajeConClase("Error al eliminar la información, contacte con el administrador.", "error", "Oops...");
    }
  })
}

const mostrar_parametro_modificar = async (buscar) => {
  let data = await buscar_parametro_id(buscar);
  let { valory, valor, valorx, id, idparametro, relacion } = data[0];
  if (idparametro == 25) {
    orden_centro.id = valory;
    orden_centro.nombre = relacion;
    $(".txt_nombre_centro").val(relacion);
  }

  $("#txtValor_modificar").val(valor);
  $("#txtDescripcion_modificar").val(valorx);
  $("#ModalModificarParametro").modal();
}

const enviar_correo_estado = (estado, id, motivo, ) => {
  let sw = false;
  let { nombre, correo } = data_solicitante;
  let ser = `<a href="${server}index.php/facturacion/${id}"><b>agil.cuc.edu.co</b></a>`;
  let tipo = -1;
  let titulo = 'Solicitud Enviada';
  let mensaje = `Se informa que la solicitud realizada por usted, fue enviada y se encuentran en proceso de verificacion, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentran su solicitud.<br><br>Mas informaci&oacuten en :${ser}<br><br><b>Nota:</b> Las solicitudes que se registren en horas de la mañana seran tramitadas en la tarde y las solicitudes que se registren en horas de la tarde seran tramitadas en horas de la mañana del dia siguiente.`;
  if (estado == 'Fact_Neg') {
    sw = true;
    tipo = 1;
    titulo = 'Solicitud Negada';
    mensaje = `Se informa que su solicitud ha sido devuelta porque no cumple con los siguientes requisitos: ${motivo}.<br><br>Mas informaci&oacuten en ${ser}`;
  } else if (estado == 'Fact_Fin') {
    sw = true;
    tipo = 1;
    titulo = 'Solicitud Finalizada';
    mensaje = `Se informa que su solicitud ha sido aprobada, a partir de este momento puede ingresar al aplicativo AGIL para revisar la factura adjuntada si asi lo requirio. <br>Mas informaci&oacuten en ${ser}`;
  } else if (estado == 'Fact_Sol') sw = true;

  mensaje = `${mensaje}.<br><br>A partir del 01 de octubre del 2020, la Institución implementó el proceso de facturación electrónica. Por lo tanto toda factura que usted solicite será enviada al cliente vía electrónica de manera inmediata.`;

  if (sw) enviar_correo_personalizado("fact", mensaje, correo, nombre, "Vicerrectoria Financiera CUC", 'Vicerrectoria Financiera', "ParCodAdm", tipo);
}

const mostrar_notificacion = (mensaje, cuerpo, icon = "success.png") => {
  Push.clear();
  Push.create(mensaje, {
    body: cuerpo,
    icon: `${Traer_Server()}imagenes/${icon}`,
    timeout: 7000,
    onClick: function () {
      console.log(this);
    }
  });
}

let activar_notificaciones = (miliseg) => {
  setInterval(function () { listar_facturas('', true); }, miliseg * 60000);
};
