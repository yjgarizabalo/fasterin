let timer;
let timer2;
let server_app_general = "localhost";
var ENVIRONMENT = 'development';
let callback_comentarios_gen = () => { }
let cargados_general = 0;
let data_activ;

$(document).ready(function () {
	$("#btn_abrir_informacion").click(() => {
		$("#modal_informacion_app").modal();
	})
	$("#notificacion_general").click(() => {
		mostrar_notificaciones_general();
	})
	$('#cargar_adjuntos_general').on('click', function () {
		myDropzoneGeneral.processQueue();
	})

	$("#inicio_return").click(() => regresar());

	$(".return").click(() => regresar());
	$("#menu_principal .thumbnail").mouseover(function () {
		$(this).find("span").css({
			"background-color": "#d57e1c",
			"border-color": "#d57e1c"
		})
	});
	$("#menu_principal .thumbnail").mouseleave(function () {
		$(this).find("span").css({
			"background-color": "#6e1f7c",
			"border-color": "#6e1f7c"
		})
	});
	$("form").attr("autocomplete", "off");
	//longitud por maxima de cada capo de texto
	$("input[type='text']").attr("maxlength", "99");
	$("input[type='password']").attr("maxlength", "100");
	$("textarea").attr("maxlength", "199");
	$(".comentarios").attr("maxlength", "499");
	$(".ilimitado").removeAttr('maxlength');

	server_app_general = Traer_Server();
	$(".sin_focus").focus(function () {
		$(this).blur()
	});
	$("#cerrar-logeo").click(() => $(".logeo").fadeOut(1000));
	$("#Mostrar-logeo").click(() => $(".logeo").fadeIn(1000));
	$(".sinlink").click(function () {
		$(this).blur()
	});
	$("#Buscar_persona_identidades").click(() => {
		const dato = $("#dato_buscar_identidades").val().trim();
		if (dato.length == 0) {
			MensajeConClase("Ingrese Numero de Identificacion", "info", "Oops...")
		} else if (isNaN(dato)) {
			MensajeConClase("Ingrese Solo Numero en la Identificacion", "info", "Oops...")
		} else {
			Traer_Persona_Identidades(dato);
		}
	});

	$("#dato_buscar_identidades").keypress((e) => {
		if (e.which == 13) {
			const dato = $("#dato_buscar_identidades").val().trim();
			if (dato.length == 0) {
				MensajeConClase("Ingrese Numero de Identificacion", "info", "Oops...")
			} else if (isNaN(dato)) {
				MensajeConClase("Ingrese Solo Numero en la Identificacion", "info", "Oops...")
			} else {
				Traer_Persona_Identidades(dato);
			}
		}
		return;
	});

	$("nav  a").focus(function () {
		$(this).blur()
	});
	$("thead").show("slow");
	$("#menu button").focus(function () {
		$(this).blur()
	});
	$("#menu").show("slow");
	$("#inicio-user").show("slow");
	$("tbody").show("slow");
	$("#menu button").click(function () {
		$(".error").hide("fast");
		$(this).blur();
	});
	$("#inputEmail").change(function () {
		var valor = $(this).val().replace(/\@.*/, '').trim();
		$(this).val(valor);
	});
	$("input").change(function () {
		var valor = $(this).val().replace(/["']/g, "").trim();
		$(this).val(valor);
	});
	$("textarea").change(function () {
		var valor = $(this).val().replace(/["']/g, "").trim();
		$(this).val(valor);
	});
	$("input").hover(function () {
		var place = $(this).attr('placeholder');
		$(this).attr({
			"title": place,
			"data-toggle": "popover",
			"data-trigger": "hover"
		});
		return true;
	});

	$("#busca_params").each(function () {
		var elem = $(this);
		elem.bind("propertychange change click keyup input paste", function(event){
			var valor = $(this).val().toUpperCase();
			mostrar_actividades(data_activ,valor);
		});
	});

})

const MensajeConClase = (mensaje, atributo, titulo) => {
	if (atributo == "info") {
		titulo = "Oops..!";
	} else if (atributo == "error") {
		titulo = "Error..!";
	}
	let is_add_res = false;
	let imagen = false;

	if (atributo == "add_res") {
		titulo = 'Espere...'
		mensaje = `Enviando Informacion de la Reserva realizada al correo ${mensaje}.`;
		atributo = false;
		is_add_res = true;
		imagen = `${server_app_general}/imagenes/loading.gif`;
	} else if (atributo == "add_adm") {
		titulo = 'Espere...'
		mensaje = `Enviando Informacion al correo ${mensaje}.`;
		atributo = false;
		is_add_res = true;
		imagen = `${server_app_general}/imagenes/loading.gif`;
	} else if (atributo == "add_inv") {
		titulo = 'Espere...'
		mensaje = "Estamos Validando la Informacion.!";
		atributo = false;
		is_add_res = true;
		imagen = `${server_app_general}/imagenes/loading.gif`;
	} else if (atributo == "waiting_inf") {
		titulo = 'Espere...'
		mensaje = "Cargando toda la información!";
		atributo = false;
		is_add_res = true;
		imagen = `${server_app_general}/imagenes/loading.gif`;
	}
	swal({
		title: titulo,
		text: mensaje,
		type: atributo,
		imageUrl: imagen,
		showConfirmButton: !is_add_res,
		allowOutsideClick: !is_add_res,
		closeOnConfirm: !is_add_res,
	}, function () {

	});
}

const MensajeConClaseWarning = (mensaje, titulo) => {
    swal({
            title: titulo,
            text: mensaje,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Si, Enviar!",
            cancelButtonText: "No, Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                if (tipo == 2) {
                    MensajeConClase(correo_solicitante, "add_adm", "Proceso Exitoso!");
                }
                Cambiar_Estado(tipo, id, "");
            }
        });
}

const HabilitarModifica = capa => $(capa).show('fast');

const DesHabilitarModifica = capa => {
    clearTimeout(timer2);
    $(capa).hide('fast');
    timer2 = setTimeout(() => {
        $(".modal").modal('hide')
    }, 5000);
}

const obtener_datos_persona_identificacion = (identificacion, tipo, nombres, apellidos, identifi, tipo_iden, foto, ubicacion, departamento, cargo, muestra, id) => {
    $.ajax({
        url: `${server_app_general}index.php/personas_control/obtener_datos_persona_identificacion`,
        dataType: "json",
        data: {
            identificacion,
            id_tipo: tipo
        },
        type: "post",
    }).done(datos => {
        if (datos == "sin_session") {
            _close();
            return;
        }
        if (datos == "") {
            $(".txtUsuario").hide("slow");
            $("#txtpassword").val("");
            $("#btnGuardarusuario").hide("slow");
            MensajeConClase("Persona No encontrada", ".error");
            $(muestra).hide("slow");
            return 0;
        } else {
            $("#btnGuardarusuario").show("slow");
            $(".txtUsuario").show("slow");
            $(id).val(datos[0].id);
            const {
                identificacion,
                nombre,
                segundo_nombre,
                apellido,
                segundo_apellido,
                id_departamento,
                id_cargo
            } = datos[0];
            $("#txtpassword").val(identificacion);
            $("#btnmostrarpersona").show("slow");
            MensajeConClase("Persona Encontrada", ".error");
            $(nombres).html(`< span class= 'ttitulo' > Nombres:</span > ${nombre} ${segundo_nombre}`);
            $(apellidos).html(`< span class= 'ttitulo' > Apellidos:</span > ${apellido} ${segundo_apellido}`);
            $(identifi).html("<span class='ttitulo'>Identi:</span> " + datos[0].identificacion);
            $(tipo_iden).html(`< span class= 'ttitulo' > Tipo Identi:</span > ${id_tipo_identificacion}`);
            $(foto).html(`< img src = "../../../imagenes_personas/${datos[0].foto}" > `)
            $(ubicacion).html(`< span class= 'ttitulo' > Ubicacion:</span > ${datos[0].ubicacion}`);
            $(departamento).html(`< span class= 'ttitulo' > Departamento:</span > ${id_departamento}`);
            $(cargo).html(`< span class= 'ttitulo' > Cargo:</span > ${id_cargo}`);
            $(muestra).show("slow");
            return 1;
        }
    });
}

const obtener_datos_persona_id_completo = (id, nombres, apellidos, identifi, tipo_iden, foto, departamento, cargo, celular,direccion, barrio, lugar_residencia, correo_personal) => {
	$.ajax({
		url: `${server_app_general}index.php/personas_control/obtener_datos_persona_id_completo`,
		dataType: "json",
		data: {
			id
		},
		type: "post",
	}).done(datos => {
		if (datos == "sin_session") {
			_close();
			return;
		}
		$(correo_personal).html("<span class='ttitulo'>Correo Personal:</span> " + datos[0].correo_personal);
		$(nombres).html("<span class='ttitulo'>Nombres:</span> " + datos[0].nombre + " " + datos[0].segundo_nombre);
		$(apellidos).html("<span class='ttitulo'>Apellidos:</span> " + datos[0].apellido + " " + datos[0].segundo_apellido);
		$(identifi).html("<span class='ttitulo'>Identificación:</span> " + datos[0].identificacion);
		$(tipo_iden).html("<span class='ttitulo'>Tipo Identificación:</span> " + datos[0].id_tipo_identificacion);
		$(foto).html('<img src="' + server_app_general + '/imagenes/' + datos[0].foto + '">')
		$(cargo).html("<span class='ttitulo'>Cargo:</span> " + datos[0].cargo);
		$(celular).html("<span class='ttitulo'>Celular:</span> " + datos[0].telefono);
		$(direccion).html("<span class='ttitulo'>Direccion:</span> " + datos[0].direccion);
		$(barrio).html("<span class='ttitulo'>Barrio:</span> " + datos[0].barrio);
		$(lugar_residencia).html("<span class='ttitulo'>Lugar de Residencia:</span> " + datos[0].lugar_residencia);
		$(departamento).html("<span class='ttitulo'>Departamento</span> " + datos[0].departamento);
		


	});
}

const _close = () => window.location = `${server_app_general}index.php/inactivo`;

const Traer_Server = () => {
    let protocolo = window.location.protocol;
    let host = window.location.hostname;
    if (ENVIRONMENT == "production") {
        return protocolo + "//" + "fasterin";
    }
    return protocolo + "//" + host + "/fasterin/";
}

const Mostrar_Contenido = () => $('#cargando').fadeOut(1000);

const registrarPersona_identidades = ventana => {
	//tomamos el formulairo ingresar visitante
	const data = new FormData(document.getElementById("form-ingresar-persona-identidades"));
	//  Enviamos el formulario a nuestro archivo php con parametro guardar
	$.ajax({
		url: `${server_app_general}index.php/personas_control/guardar_persona`,
		type: "post",
		dataType: "json",
		data,
		cache: false,
		contentType: false,
		processData: false
	}).done(datos => {
		switch (datos) {
			case -1000:
				_close();
				break
			case -1:
				MensajeConClase("Todos Los campos son Obligatorios", "info", "Oops.!");
				break
			case -2:
				MensajeConClase("El Persona ya se encuentra en el Sistema", "info", "Oops.!");
				break
			case -3:
				MensajeConClase("Seleccione Cargo de la persona", "info", "Oops.!");
				break
			case -4:
				MensajeConClase("Ingrese correo de la persona", "info", "Oops.!");
				break
			case -5:
				MensajeConClase("Ingrese usuario de la persona", "info", "Oops.!");
				break
			case -6:
				MensajeConClase("El nombre de usuario ya se encuentra registrado.", "info", "Oops.!");
				break
			case -7:
				MensajeConClase("El correo ya se encuentra registrado.", "info", "Oops.!");
				break
			case 4:
				MensajeConClase("Persona Guardada Con exito", "success", "Proceso Exitoso!");
				$("#form-ingresar-persona-identidades").get(0).reset();
				break;
			case 6:
				MensajeConClase("El Nombre de usuario ya se encuentra Registrado", "info", "Oops.!");
				break;
			case -1302:
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops.!");
			default:
				MensajeConClase("Error al Guardar a la persona", "error", "Oops.!");
				break;
		}
	});
}

const PintarApartadoPersona = () => {
    $(".apartado_persona").html('<form  id="form-ingresar-persona-identidades" enctype="multipart/form-data" method="post"> <div class="panel panel-default" ><div class="panel-body "> <h4 class="ttitulo text-center"><span class="glyphicon glyphicon-refresh"></span> Nueva Persona</h4> <h6 class="ttitulo"><span class="glyphicon glyphicon-download"></span> Buscar en Identidades</h6> <div class="input-group agrupado"><input class="form-control text-left" id="dato_buscar_identidades" placeholder="Ingrese Nombre,Apellido o Identificacion"> <span class="input-group-addon glyphicon glyphicon-search red_primari pointer" id="Buscar_persona_identidades"></span> </div> <h6 class="ttitulo"><span class="glyphicon glyphicon-indent-left"></span> Datos del Solicitante</h6> <select name="tipo_identificacion"   required class="form-control  cbxtipoIdentificacion">  </select> <input min="1" type="number" name="identificacion" id="txtIdentificacion" class="form-control inputt" placeholder="No. Identificación" required><input type="text" name="apellido" id="txtApellido" class="form-control inputt2" placeholder="Primer Apellido"  required> <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2" placeholder="Segundo Apellido" required><input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre" required> <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2" placeholder="Segundo Nombre" ><select name="departamento"   required class="form-control inputt cbxdepartamento"  id="departamento_sele_guardar"> <option>Seleccione Departamento</option> </select> <select name="cargo"   required class="form-control inputt cbxcargos">  <option>Seleccione Cargo</option>  </select><input min="1" type="number" name="celular" id="txtCelular" class="form-control" placeholder="Celular" required=""> <input type="email" name="correo" id="txtCorreo" class="form-control inputt" placeholder="Correo Eléctronico"  required=""> <input type="text" name="usuario" id="txtusuario" class="form-control inputt2" placeholder="Usuario" required="" > <div class="oculto"> <label class="label">Foto persona</label><input class="form-control inputt" type="file" name="imagen"   id="FileImagen"> </div> </div><div class="panel-footer text-left"><button type="submit" id="btnGuardarVisitante"  class="btn btn-danger2 active">Guardar</button> <button type="button" class="btn btn-default" id="cerrar_apartado">Cancelar</button> </div> </div> </form>');
}

const Traer_Persona_Identidades = identi => {
    $.ajax({
        url: `${server_app_general}index.php/personas_control/Traer_Persona_Identidades`,
        dataType: "json",
        data: {
            id: identi
        },
        type: "post",
    }).done(datos => {
        if (datos == "sin_session") {
            _close();
            return;
        }
        if (datos == -1) {
            MensajeConClase("Persona No Encontrada en Identidades", "info", "Oops...")

            return false;
        }
        let nombres = datos.nombres.split(" ");
        let segundonombre = "";
        for (let i = 1; i < nombres.length; i++) {
            segundonombre = segundonombre + " " + nombres[i];
        }
        $("#txtNombre").val(nombres[0]);
        $("#txtApellido").val(datos.primer_apellido);
        $("#txtSegundoNombre").val(segundonombre);
        $("#txtsegundoapellido").val(datos.segundo_apellido);
        $("#txtIdentificacion").val(datos.num_documento);
        $("#cbxtipoIdentificacion").val("1");
        $("#txtCelular").val(datos.celular);
        $("#txtCorreo").val(datos.logon_name + "@cuc.edu.co");
        $("#txtusuario").val(datos.logon_name);
        $("#cbxtipopersona").val("PerInt");
        $(".datos_internos").show('slow');
        $(".datos_internos input").attr("required", "true");
        $(".datos_internos select").attr("required", "true");
    });
}


const isValidEmail = mail => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(mail);

function enviar_correo_personalizado(llama, mensaje, correo, nombre, from, adjunto, codigo, tipo, archivo, externo = false) {
	// return false;
	$.ajax({
		url: `${server_app_general}index.php/pages/enviar_correo_personalizado`,
		type: "post",
		data: {
			mensaje,
			correo,
			nombre,
			adjunto,
			from,
			codigo,
			tipo,
			archivo,
			externo
		},
		dataType: "json",
	}).done(datos => {
		if (datos == "sin_session") {
			_close();
			return;
		}
		if (datos == 1) {
			return;
		} else if (datos == -1) {
			MensajeConClase("No se encontro el correo encargado de notificar, Contacte al Administrador.!!", "info", "Oops...");
		} else if (datos == -2) {
			MensajeConClase("Ingrese correo destino.!!", "info", "Oops...");

		} else if (datos == -3) {
			MensajeConClase("Ingrese Mensaje a Enviar.!!", "info", "Oops...");

		} else if (datos == -4) {
			MensajeConClase("Ingrese Codigo del correo encargado de notificar!!", "info", "Oops...");

		} else {
			MensajeConClase("No se Envio la Informacion, Contacte al Administrador.!!", "error", "Oops...");
		}
	});
}

function activarfile() {
    $(function() {
        // We can attach the `fileselect` event to all file inputs on the page
        $(document).on('change', ':file', function() {
            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        // We can watch for our custom `fileselect` event like this
        $(document).ready(function() {
            $(':file').on('fileselect', function(event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;
                if (input.length) {
                    input.val(log);
                } else {
                    if (log)
                        console.log(log);
                }
            });
        });

    });
}

const get_idioma = () => ({
	"sProcessing": "Procesando...",
	"sLengthMenu": "Mostrar _MENU_ registros",
	"sZeroRecords": "No se encontraron resultados",
	"sEmptyTable": "Ningún dato disponible en esta tabla",
	"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
	"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
	"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
	"sInfoPostFix": "",
	"sSearch": "Buscar:",
	"sUrl": "",
	"sInfoThousands": ",",
	"sLoadingRecords": "Ningún dato disponible en esta tabla...",
	"oPaginate": {
		"sFirst": "Primero",
		"sLast": "Último",
		"sNext": "Siguiente",
		"sPrevious": "Anterior"
	},
	"oAria": {
		"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
		"sSortDescending": ": Activar para ordenar la columna de manera descendente"
	},
	"searchPlaceholder": "Buscar..."
});

const get_botones = (col = ':visible') => (
    [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o"></i>',
            titleAttr: 'Excel',
            className: 'btn btn-success',
            exportOptions: {
                columns: col
            }
        },
        {
            extend: 'csvHtml5',
            text: '<i class="fa fa-file-text-o"></i>',
            titleAttr: 'CSV',
            className: 'btn btn-default',
            exportOptions: {
                columns: col
            }
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf-o"></i>',
            titleAttr: 'PDF',
            className: 'btn btn-danger2',
            exportOptions: {
                columns: col
            }
        }
    ]
);

const formDataToJson = data => {
    let ConvertedJSON = {};
    for (const [key, value] of data.entries()) {
        ConvertedJSON[key] = value;
    }
    return ConvertedJSON
}

const regresar = () => {
    let ruta = window.location.pathname;
    const pos = ruta.indexOf("index.php/");
    ruta = ruta.slice(pos, ruta.length).replace(/[0-9]+/g, '');
    if (ruta[ruta.length - 1] === '/') ruta = ruta.substr(0, ruta.length - 1);
    ruta = ruta.split('/');
    ruta.pop();
    ruta = String(ruta).replace(/,/g, '/');
    window.location = `${server_app_general}${ruta}`;
}

const imprimirDIV = (elemento, style = false, ) => {
    let ventana = window.open('', 'PRINT');
    ventana.document.write(`<html><head><title>${document.title}</title>`);
    ventana.document.write(`<link rel="stylesheet" href="${server_app_general}js-css/estaticos/css/bootstrap.min.css">`);
    if (style) ventana.document.write(`<link rel="stylesheet" href="${server_app_general}js-css/genericos/css/MyStyle.css">`);

    //ventana.document.write(`<link rel="stylesheet" href="${server_app_general}js-css/genericos/css/StylePDF.css">`);
    ventana.document.write('</head><body >');
    ventana.document.write(elemento.innerHTML);
    ventana.document.write('</body></html>');
    ventana.document._close();
    ventana.focus();
    ventana.onload = function() {
        ventana.print();
        ventana._close();
    };
    return true;
}

/*
Funcion encargada de hacer el llamado AJAX con el controlador
*/
const consulta_ajax = (url, data, callback) => {

    $.ajax({
        url,
        data,
        type: "POST",
        dataType: "JSON",
        success: function(res) {
            if (res.tipo == "sin_session") _close();
            callback(res);
        },
        error: function(xhr, status, error) {
            MensajeConClase(`Error al realizar la operación ${error}`, "error", "Oops.!")

        }
    });
}
const enviar_formulario = (url, data, callback) => {

    $.ajax({
        url,
        data,
        type: "POST",
        dataType: "JSON",
        cache: false,
        contentType: false,
        processData: false,
        success: function(res) {
            if (res.tipo == "sin_session") _close();
            callback(res);
        },
        error: function(xhr, status, error) {
            MensajeConClase(`Error al realizar la operación ${error}`, "error", "Oops.!")

        }
    });
}

const guardar_comite_general = (tipo = 'presupuesto') => {
    let url = `${Traer_Server()}index.php/presupuesto_control/guardar_comite`;
    let data = new FormData(document.getElementById("form_guardar_comite"));
    data.append('tipo', tipo)
    enviar_formulario(url, data, (resp) => {
        let {
            mensaje,
            tipo,
            titulo,
        } = resp;
        if (tipo == "sin_session") {
            _close();
        } else if (tipo == 'success') {
            swal.close();
            listar_comites();
            $("#form_guardar_comite").get(0).reset();
            $("#modal_guardar_comite").modal('hide');
        } else MensajeConClase(mensaje, tipo, titulo);
    })
}

const confirmar_guardar_comite = (tipo = 'presupuesto') => {
    swal({
            title: "Nuevo Comité .?",
            text: "Tener en cuenta que, al agregar un nuevo comité este se covertira en el comité activo para las solicitudes, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
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
                guardar_comite_general(tipo);
				cargar_comites();
            }
        });
}

const modificar_comite_general = id => {
    let url = `${Traer_Server()}index.php/presupuesto_control/modificar_comite`;
    let data = new FormData(document.getElementById("form_modificar_comite"));
    data.append("id", id);
    enviar_formulario(url, data, (resp) => {
        let {
            mensaje,
            tipo,
            titulo,
        } = resp;
        if (tipo == "sin_session") {
            _close();
        } else if (tipo == 'success') {
            listar_comites();
            $("#modal_modificar_comite").modal('hide');
            $("#form_modificar_comite").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    })
}

const mostrar_datos_comite_modificar = id => {
    let url = `${Traer_Server()}index.php/presupuesto_control/traer_comite`;
    let data = {
        id,
    };
    consulta_ajax(url, data, (resp) => {
        let {
            mensaje,
            tipo,
            titulo,
            data
        } = resp;
        if (tipo == "sin_session") {
            _close();
        } else if (tipo == 'success') {
            id_comite = data.id;
            $("#form_modificar_comite input[name='nombre']").val(data.nombre);
            $("#form_modificar_comite textarea[name='descripcion']").val(data.descripcion);
            $("#form_modificar_comite input[name='fecha']").val(data.fecha_cierre);
            $("#modal_modificar_comite").modal();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    })

};
const guardar_comentario_comite = (id_comite) => {
    let url = `${Traer_Server()}index.php/presupuesto_control/guardar_comentario`;
    let data = new FormData(document.getElementById("form_guardar_comentario"));
    data.append('id_comite', id_comite);
    enviar_formulario(url, data, (resp) => {
        let {
            mensaje,
            tipo,
            titulo,
        } = resp;
        if (tipo == "sin_session") {
            _close();
        } else if (tipo == 'success') {
            listar_comentarios_comite(id_comite);
            $("#form_guardar_comentario").get(0).reset();
        }
        MensajeConClase(mensaje, tipo, titulo);
    })
}

const listar_comentarios_comite = (id_comite) => {
    let url = `${Traer_Server()}index.php/presupuesto_control/listar_comentarios`;
    let data = {
        id_comite
    }
    consulta_ajax(url, data, async(resp) => {
        let comentarios = '';
        for (let index = 0; index < resp.length; index++) {
            const { id, id_comite, persona, comentario } = resp[index];
            let respuestas = await listar_respuestas_comentario_comite(id);
            comentarios = comentarios + `<a href="#" class="list-group-item">
				<span class="badge" onclick='responder_comentario_comite(${id},${id_comite})'>Responder</span>
				<p class="list-group-item-text">${persona}</p>
				<h4 class="list-group-item-heading">${comentario} </h4>
				<br>
				<p>Respuestas:</p>
				${respuestas}
			</a>
			`;
        }
        $("#panel_comentarios_presupuesto").html(`
		<ul class="list-group">
			<li class="list-group-item active">
			<span class="badge">${resp.length}</span>
			Comentarios En este comité
		</li>
		${comentarios}
		</ul>
		`);
    })

};

const responder_comentario_comite = (id, id_comite, tipo_c = '', callback = '') => {
    swal({
        title: "Responder Comentario.!",
        text: "",
        type: "input",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Responder!",
        cancelButtonText: "Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true,
        inputPlaceholder: "Ingrese Respuesta"
    }, function(respuesta) {

        if (respuesta === false)
            return false;
        if (respuesta === "") {
            swal.showInputError("Debe Ingresar la respuesta.!");
            return false;
        } else {
            let url = `${Traer_Server()}index.php/presupuesto_control/guardar_comentario`;
            consulta_ajax(url, { id_comite, id, 'comentario': respuesta }, (resp) => {
                let { mensaje, tipo, titulo, } = resp;
                if (tipo == "sin_session") {
                    _close();
                } else if (tipo == 'success') {
                    swal._close();
                    if (tipo_c.length == 0) listar_comentarios_comite(id_comite);
                    else mostrar_notificaciones_comentarios_comite(tipo_c, callback);
                } else MensajeConClase(mensaje, tipo, titulo);
            })
            return false;
        }
    });
}
const listar_respuestas_comentario_comite = (id) => {
    return new Promise(resolve => {
        $.ajax({
            url: `${Traer_Server()}index.php/presupuesto_control/listar_respuestas_comentarios`,
            dataType: "json",
            data: {
                id,
            },
            type: "post",
            success: function(datos) {
                let respuestas = '';
                datos.map((elemento) => {
                    let { persona, comentario } = elemento;
                    respuestas = respuestas + `<p><b>${persona}: </b>${comentario}</p>`;
                })
                resolve(respuestas);
            },
            error: function(status, error) {
                console.log('Something went wrong', status, error);
            }
        });
    });

}

const mostrar_notificaciones_comentarios_comite = (tipo, callback) => {
    let url = `${Traer_Server()}index.php/presupuesto_control/mostrar_notificaciones_comentarios_comite`;
    consulta_ajax(url, { tipo }, async(resp) => {
        let comentarios = '';
        for (let index = 0; index < resp.length; index++) {
            const { id, id_comite, persona, comentario } = resp[index];
            let respuestas = await listar_respuestas_comentario_comite(id);
            comentarios = comentarios + `<a href="#" class="list-group-item">
				<span class="badge btn-danger2" onclick = 'terminar_comentario_comite(${id},"${tipo}",${callback})' > Terminar</span >
				<span class="badge" onclick='responder_comentario_comite(${id},${id_comite},"${tipo}",${callback})'>Responder</span>
				<span class="badge btn-danger" onclick='abrir(${id_comite},${callback})'> Abrir</span>
				<p class="list-group-item-text">${persona}</p>
				<h4 class="list-group-item-heading">${comentario} </h4>
				<br>
				<p>Respuestas:</p>
				${respuestas}
			</a>
			`;
        }
        $("#panel_notificaciones_comite").html(`
		<ul class="list-group">
			<li class="list-group-item active">
			<span class="badge">${resp.length}</span>
			Notificaciones Comité
		</li>
		${comentarios}
		</ul>
		`);
        $(".n_notificaciones").html(resp.length);
        if (resp.length > 0) $("#modal_notificaciones").modal();
    })

};
const abrir = (id, callback) => { callback(id) }

const terminar_comentario_comite = (id, tipo_c, callback) => {
    swal({
            title: "Terminar Comentario.?",
            text: "Tener en cuenta que al terminar un comentario no se enviaran mas notificaciones referente a este.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Ok, Terminar!",
            cancelButtonText: "No, Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                let url = `${Traer_Server()}index.php/presupuesto_control/terminar_comentario`;
                consulta_ajax(url, { id }, (resp) => {
                    let { mensaje, tipo, titulo, } = resp;
                    if (tipo == "sin_session") {
                        _close();
                    } else if (tipo == 'success') {
                        swal._close();
                        mostrar_notificaciones_comentarios_comite(tipo_c, callback)
                    } else MensajeConClase(mensaje, tipo, titulo);
                })
            }
        });

}
const consulta_solicitud_comunicaciones_id = id => {
    return new Promise(resolve => {
        let url = `${Traer_Server()}index.php/solicitudes_adm_control/consulta_solicitud_comunicaciones_id`;
        consulta_ajax(url, { id }, (resp) => {
            resolve(resp)
        });
    })
}

const buscar_persona_where = (dato) => {
        return new Promise(resolve => {
            let url = `${Traer_Server()}index.php/personas_control/buscar_persona_where`;
            consulta_ajax(url, { dato }, (resp) => {
                resolve(resp);
            })
        });

    }
    //comentarios generales
const guardar_comentario_general = (data, callback = () => {}) => {
    let url = `${Traer_Server()}index.php/pages/guardar_comentario_general`;
    consulta_ajax(url, data, ({ mensaje, tipo, titulo, }) => {
        if (tipo == 'success') callback();
        MensajeConClase(mensaje, tipo, titulo);
    })
}

const listar_comentarios_general = (id_solicitud, tipo) => {
    return new Promise(resolve => {
        let url = `${Traer_Server()}index.php/pages/listar_comentarios_general`;
        consulta_ajax(url, { id_solicitud, tipo }, async resp => {
            resolve(resp);
        })
    });
};

const listar_respuestas_comentario_general = id => {
    return new Promise(resolve => {
        let url = `${Traer_Server()}index.php/pages/listar_respuestas_comentario_general`;
        consulta_ajax(url, { id }, async resp => {
            resolve(resp);
        })
    });
}

const responder_comentario_general = (id_solicitud, id_comentario, tipo, container, titulo, tipo_resp = 1) => {
    swal({
        title: "Responder Comentario.!",
        text: "",
        type: "input",
        showCancelButton: true,
        confirmButtonColor: "#D9534F",
        confirmButtonText: "Responder!",
        cancelButtonText: "Cancelar!",
        allowOutsideClick: true,
        closeOnConfirm: false,
        closeOnCancel: true,
        inputPlaceholder: "Ingrese Respuesta"
    }, function(comentario) {
        if (comentario === false) return false;
        if (comentario === "") swal.showInputError("Debe Ingresar la respuesta.!");
        else {

            const callback = tipo_resp == 1 ? () => { pintar_comentarios_generales(id_solicitud, container, titulo); } : () => { callback_comentarios_gen() };
            guardar_comentario_general({ comentario, id_solicitud, id_comentario, tipo }, callback);
        }

    });
}


const terminar_comentario_comite_general = (id) => {
    swal({
            title: "Terminar Comentario.?",
            text: "Tener en cuenta que al terminar un comentario no se enviaran mas notificaciones referente a este.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText: "Ok, Terminar!",
            cancelButtonText: "No, Cancelar!",
            allowOutsideClick: true,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                let url = `${Traer_Server()}index.php/pages/terminar_comentario_general`;
                consulta_ajax(url, { id }, ({ mensaje, tipo, titulo, }) => {
                    if (tipo == 'success') {
                        swal._close();
                        callback_comentarios_gen();
                    } else MensajeConClase(mensaje, tipo, titulo);
                })
            }
        });
}

const pintar_comentarios_generales = async(id_solicitud, container, titulo, tipo_com) => {
    let comentarios = await listar_comentarios_general(id_solicitud, tipo_com);
    let resultado = ``;
    for (let index = 0; index < comentarios.length; index++) {
        let { id, comentario, persona, id_solicitud, tipo } = comentarios[index];
        let lista_res = '<br><p>Respuestas:</p>';
        let respuestas = await listar_respuestas_comentario_general(id);
        respuestas.map(({ persona: persona_res, comentario: comentario_res }) => lista_res = `${lista_res}<p><b>${persona_res}: </b>${comentario_res}</p>`);
        resultado = `${resultado}<a href="#" class="list-group-item"><span class="badge" onclick='responder_comentario_general(${id_solicitud},${id},"${tipo}","${container}","${titulo}")'>Responder</span><p class="list-group-item-text">${persona}</p><h4 class="list-group-item-heading">${comentario}</h4>${lista_res}</a>`;
    }
    $(container).html(`<ul class="list-group"><li class="list-group-item active">	<span class="badge">${comentarios.length}</span>${titulo}</li>${resultado}</ul>`);

};

const listar_notificaciones_comentarios_general = (tipos, adms) => {
    return new Promise(resolve => {
        let url = `${Traer_Server()}index.php/pages/listar_notificaciones_comentarios_general`;
        consulta_ajax(url, { tipos, adms }, async resp => {
            resolve(resp);
        })
    });
}

const pintar_notificaciones_comentarios_general = async(tipos, adms, container, n_notificaciones, modal, titulo, callback) => {
    callback_comentarios_gen = () => {
        pintar_notificaciones_comentarios_general(tipos, adms, container, n_notificaciones, modal, titulo);
    }

    let comentarios = await listar_notificaciones_comentarios_general(tipos, adms);
    let resultado = ``;
    for (let index = 0; index < comentarios.length; index++) {
        let { id, comentario, persona, id_solicitud, tipo } = comentarios[index];
        let lista_res = '<br><p>Respuestas:</p>';
        let respuestas = await listar_respuestas_comentario_general(id);
        respuestas.map(({ persona: persona_res, comentario: comentario_res }) => lista_res = `${lista_res}<p><b>${persona_res}: </b>${comentario_res}</p>`);
        resultado = `${resultado}<a href="#" class="list-group-item">
		<span class="badge btn-danger2" onclick = 'terminar_comentario_comite_general("${id}")' > Terminar</span >
		<span class="badge" onclick='responder_comentario_general(${id_solicitud},${id},"${tipo}","${container}","${titulo}",2)'>Responder</span>
		<span class="badge btn-danger" onclick='abrir_postulacion(${id_solicitud})'> Abrir</span>
		<p class="list-group-item-text">${persona}</p><h4 class="list-group-item-heading">${comentario}</h4>${lista_res}</a>`;
    }
    $(container).html(`<ul class="list-group"><li class="list-group-item active">	<span class="badge">${comentarios.length}</span>${titulo}</li>${resultado}</ul>`);
    $(n_notificaciones).html("<span class='fa fa-bell'></span>");
    if (comentarios.length > 0) {
        $(n_notificaciones).html("<span class='fa fa-bell red tiembla' style='-webkit-animation: tiembla .5s infinite;'></span>");
        $(modal).modal();
    }
};

const downloadCanvas = ({ canvasId, filename, path }, callback) => {
    // Obteniendo la etiqueta la cual se desea convertir en imagen
    var domElement = document.getElementById(canvasId);
    // Utilizando la función html2canvas para hacer la conversión
    html2canvas(domElement).then(canvas => {
        // Creando enlace para descargar la imagen generada
        var link = document.createElement('a');
        link.href = canvas.toDataURL("image/png");
        link.download = filename;
        consulta_ajax(`${ruta}uploadImgBase64`, {
            image: link.href,
            name: 'imagen',
            path,
        }, resp => callback(resp));
    });
}


const confirmar_accion_general = (text, callback, title = 'Estas Seguro .. ?', confirmButtonText = 'Si, Aceptar!', cancelButtonText = "No, Cancelar!") => {
    swal({
            title,
            text,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#D9534F",
            confirmButtonText,
            cancelButtonText,
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

const buscar_personas = (data, callbak, ruta_control, tabla = "#tabla_personas_busqueda", selec = "seleccionar") => {
    $(`${tabla} tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)').off("click", `tr td .${selec}`);
    consulta_ajax(ruta_control, data, (resp) => {
        let i = 0;
        const myTable = $(`${tabla}`).DataTable({
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
                    defaultContent: '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default ' + selec + '" ></span>'
                },
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: [],
        });

        $(`${tabla} tbody`).on("click", "tr", function() {
            $(`${tabla} tbody tr`).removeClass("warning");
            $(this).attr("class", "warning");
        });
        $(`${tabla} tbody`).on("dblclick", "tr", function() {
            let data = myTable.row($(this).parent().parent()).data();
            callbak(data);
        });
        $(`${tabla} tbody`).on("click", `tr td .${selec}`, function() {
            let data = myTable.row($(this).parent().parent()).data();
            callbak(data);
        });

    });
}

const valor_peso = valor => new Intl.NumberFormat("es-CO", { style: "currency", currency: "COP" }).format(valor);

const cargar_archivos_general = (url, callback = () => { }, data = {}, maxFiles = 10, acceptedFiles = 'image/*, .pdf, .docx, .doc', modal = '#modal_enviar_archivos', form = '#Subir') => {
	Dropzone.options.Subir = {
		url, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
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
		maxFiles, //si no es nulo, define cu�ntos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
		params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
		clickable: true,
		ignoreHiddenFiles: true,
		acceptedFiles, //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
		acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
		autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzoneGeneral.processQueue(); para subirlas

		error: function (response) {
			if (!response.xhr) MensajeConClase("Ningun archivo fue cargado", "info", "Oops!");
		},
		success: function (file, response) {
			let { mensaje, tipo, titulo } = JSON.parse(response)
			MensajeConClase(mensaje, tipo, titulo);
			$(modal).modal('hide');
			$(form).get(0).reset(); //  Cambios actuales
			callback();
		},

        init: function() {
            let num_archivos = 0;
            myDropzoneGeneral = this;
            this.on("addedfile", function(file) {
                num_archivos++;
            });
            this.on("removedfile", function(file) {
                num_archivos--;
            });
            myDropzoneGeneral.on("complete", function(file) {
                myDropzoneGeneral.removeFile(file);
                cargados_general++;
            });
            myDropzoneGeneral.on("processing", function(file) {
                this.options.params = data
            });
            myDropzoneGeneral.on("maxfilesexceeded", function(file) {
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

const mostrar_hoja_vida_menu = (id) => {
	$("#container-principal2 .row").prepend(`
	<a style="color: black; font-style: oblique;font-weight: bold;" href='${Traer_Server()}index.php/talento_cuc/hoja_vida/${id}' class="sinlink" target="_blank">
	  <div>
		  <div class="thumbnail">
			  <div class="caption">
			  <img src="${Traer_Server()}/imagenes/talento_cuc.png" alt="...">
			  <span class = "btn form-control">Talento cuc</span>
			  </div>
		  </div>
	  </div>
	</a>
	`);
}

const cargar_datos = (actividades) => {
	data_activ = JSON.parse(actividades);
	var info = ``
	
	for (let index = 0; index < data_activ.length; index++) {
		if(data_activ[index]['valora'] == 'alterno'){
			var ruta = data_activ[index]["valorb"];
		}else{
			var ruta = Traer_Server()+'index.php/'+data_activ[index]["actividad"];
		}
		info = info +`
		<a style="color: black; font-style: oblique;font-weight: bold;" href="${ruta}" class="sinlink">
			<div>
				<div class="thumbnail">
					<div class="caption">
					<img src="${Traer_Server()}/imagenes/${data_activ[index]["icono"]}" alt="...">
					<span class = "btn form-control">${data_activ[index]["nombre"]}</span>
					</div>
				</div>
			</div>
		</a>`;
	}
	$("#container_listado_menu").html(info)
}


const mostrar_actividades = (data, valor) => {

	var info = ``
	for (let index = 0; index < data.length; index++) {
		if(data[index]["nombre"].toUpperCase().includes(valor)){
			info = info +`
			<a style="color: black; font-style: oblique;font-weight: bold;" href='${Traer_Server()}index.php/${data[index]["actividad"]}' class="sinlink">
				<div>
					<div class="thumbnail">
						<div class="caption">
						<img src="${Traer_Server()}/imagenes/${data[index]["icono"]}" alt="...">
						<span class = "btn form-control">${data[index]["nombre"]}</span>
						</div>
					</div>
				</div>
			</a>`;
		}
	}
	if (info == ``){
		info = info + `
		<img src="${Traer_Server()}/imagenes/default.png" width="110px" alt="...">
		<h4 class="text-center">Módulo no encontrado</h4>
		`;
	}

	$("#container_listado_menu").html(info)
}

const soportes_plan_formacion = () => {
	window.open(`${Traer_Server()}index.php/talento_cuc`);
 }
const asistencia_entrenamiento = (idpersona) => {
	window.open(`${Traer_Server()}index.php/talento_cuc/asistencia_entrenamiento/${idpersona}`);
 }
const evaluacion_administrativa = (id) => {
	window.open(`${Traer_Server()}index.php/evaluacion/encuesta/${id}`);
 }
const actas_retroalimentacion = (id) => {
	window.open(`${Traer_Server()}index.php/evaluacion/acta/${id}`);
 }
const confirmar_acta = (id) => {
	window.open(`${Traer_Server()}index.php/evaluacion/confirmar_acta/${id}`);
 }

const mantenimiento = (id) => {
	window.open(`${Traer_Server()}index.php/mantenimiento/${id}`);
}


const mostrar_notificaciones_general = () => {
	let route = window.location.href;
	
	consulta_ajax(`${Traer_Server()}index.php/pages/mostrar_notificaciones_general`, {url:route}, async (datos) => {
		dibujar_comentario_general(datos.resp, "#panel_notificaciones_general");
		if (datos.resp.length > 0) $("#modal_notificaciones_general").modal("show");
	});
}

const num_notificaciones_general = () =>{
	let route = window.location.href;

	consulta_ajax(`${Traer_Server()}index.php/pages/mostrar_notificaciones_general`, {url:route}, async (datos) => {
		$("#notificaciones").html(datos.resp.length);
	});
}

const dibujar_comentario_general = async (datos, panel) => {
	if (datos == "sin_session") {
		_close();
		return;
	}
	let num = 0;
	let notificaciones = "";
	datos.forEach(element => {
		notificaciones += `<a class="list-group-item pointer">
			<span class="badge pointer" onclick="${element.accion}">Ver</span>
				<h4 class="list-group-item-heading">${element.nombre} </h4>
				<p class="list-group-item-text">Usted tiene ${element.cantidad} ${element.descripcion}</p>
			</a>`;
        num++;
    });
    $(panel).html(`
		<ul class="list-group">
			<li class="list-group-item active">
			<span class="badge">${num}</span>
			Tareas Pendientes
		</li>
		${notificaciones}
		</ul>
		`);
}
