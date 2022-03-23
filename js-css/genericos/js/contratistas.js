const ruta = `${Traer_Server()}index.php/contratista_control/`;
let contratos = "";
let solicitud = "";
let errores = [];
let cargados = 0;

$(document).ready(function () {
	$(".fidel input").focusin(function () {
		let fidel = this.parentElement;
		$(fidel).css("border", "1.3px solid #d67e1c");
	});

	$(".fidel input").focusout(function () {
		let fidel = this.parentElement;
		$(fidel).removeAttr("style");
	});

	$(".btn-login").on("click", function () {
		let user = $("#user").val();
		let pass = $("#pass").val();
		login(user, pass);
	});

	$("#pass, #user").on("keypress", function (e) {
		if (e.keyCode == 13) {
			let user = $("#user").val();
			let pass = $("#pass").val();
			if ($(this).attr("id") == "pass" && user === "") {
				$("#user").focus();
			} else if (user != "") {
				$("#pass").focus();
			}

			if (user != "" && pass != "") {
				login(user, pass);
			}
		}
	});

	$("#logout").on("click", function () {
		$.ajax({
			url: `${ruta}logout`,
			type: "POST",
			dataType: "json",
			success: function (res) {
				if (res == 0) {
					window.location.reload();
				}
			},
			error: function (xhr, status, error) {
				MensajeConClase(
					`Error al realizar la operación ${error}`,
					"error",
					"Oops.!"
				);
			},
		});
	});

	$(".download_contrato").on("click", function () {
		consulta_ajax(
			`${Traer_Server()}index.php/contrataciones_control/verificar_contrato_adj`,
			{ id: solicitud.id, tipo: 'adj_contrato'},
			(res) => {
				let data = res["data"];
				if (res["status"] === 1) {
					url = `${Traer_Server()}archivos_adjuntos/contrataciones/${
						data.adj_contrato
					}`;
					window.open(url, "_blank");
				}
			}
		);
	});

	$(".modal").on("hidden.bs.modal", function () {
		if ($(".modal").is(":visible")) {
			$("body").addClass("modal-open");
		}
	});

	$(".enviar_firma").on("click", function () {
		let checkbox;
		if ($("#check_contra").is(":checked")) {
			checkbox = 1;
		} else {
			checkbox = 0;
		}
		let id = $("#id_firma").val();
		MensajeConClase("validando info", "add_inv", "Oops...");
		consulta_ajax(`${ruta}guardar_firma`, { id: id, checkbox: checkbox }, async res => {
			if (res.tipo == "success") {
				for (const key in res.persona) {
					const correos = res.persona[key];					
					await enviar_correo_estado(correos, id, '');
				}
				listarContratos();			
				MensajeConClase(res.mensaje, res.tipo, res.titulo);				
				$("#firmar").modal("hide");
			} else {
				MensajeConClase(res.mensaje, res.tipo, res.title);
			}
		});
	});
});

const enviar_correo_estado = async ({persona, correo, estado}, id, motivo) => {
	let sw = false;
	let ser = `<a href="${Traer_Server()}index.php/contrataciones/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 3;
	switch (estado) {
		case 'Cont_En_Ver':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en verificaión.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> se encuentra en verificaión, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
	}
	if(sw) return new Promise(resolve => {
		enviar_correo_personalizado("cont", mensaje, correo, persona, "Contrataciones CUC", `Contrataciones AGIL - ${titulo}`, "ParCodAdm", tipo)
		resolve(true);
	});
}

const verContrato = (id_contrato) => {
	$("#detalles_contra").modal();
	for (const key in contratos) {
		if (contratos[key].id == id_contrato) {
			solicitud = contratos[key];
		}
	}
	let {
		id,
		codSAP,
		fecha_ini,
		fecha_ini_gar,
		fecha_sus,
		fecha_ter,
		garantia,
		garantia_val,
		nombre_tante,
		nombre_tista,
		num_con,
		contrato,
		objetivo,
		plazo,
		tista_cedula_nit,
		valor,
		solicitante,
		type_person,
		firma_contratista,
	} = solicitud;

	//let tipo_contrato = "Convenio";
	let codSAP_tr = $(".tabla_contrataciones tr.codSAP_tr");
	//$(".tr_conv").removeClass("oculto");

	$(".solicitante_space").html(solicitante);
	/*if (contrato.length > 35) {
		$(".contratom_space").attr('title', contrato);
		$(".contratom_space").html(contrato.replace(contrato.substr(35, contrato.length), '...'));
	}else{*/
		$(".contratom_space").attr('title', contrato);
		$(".contratom_space").html(contrato);
	/*}*/
	
	$(".num_contrato_space").html(num_con);
	codSAP == null || codSAP == 0
		? $(".codSAP_tr").attr('hidden', 'hidden')
		: $(".codSAP_tr").removeAttr('hidden');
		
	$(".codSAP_tr").attr('hidden', 'hidden');
	$(".codSAP_space").html(codSAP);
	$(".tante_space").html(nombre_tante);
	$(".tista_space").html(nombre_tista);
	$(".objetivo_space").html(objetivo);
	$(".valor_space").html(
		new Intl.NumberFormat("es-CO", {
			style: "currency",
			currency: "COP",
		}).format(valor)
	);
	$(".fechasus_space").html(formatDate(fecha_sus, 'long'));
	$(".fechaini_space").html(formatDate(fecha_ini, 'long'));
	$(".fechafin_space").html(formatDate(fecha_ter, 'long'));
	$(".cedunit_space").html(tista_cedula_nit);
	$(".plazo_space").html(plazo);
	$(".tipo_pers_space").html(type_person);
	$(".garantia_space").html(garantia);
	if (firma_contratista == null || firma_contratista == "") {
		$(".firma_space").html("Por firmar");
	} else {
		$(".firma_space").html("Firmado de manera digital");
	}

	$(".btnEstados").on("click", function () {
		let x = 1;
		consulta_ajax(
			`${ruta}listar_estados`,
			{
				id: id_contrato,
			},
			(res) => {
				$("#tabla_estados tbody").html("");
				for (const key in res) {
					$("#tabla_estados tbody").append(`<tr>
                    <td>${x++}</td>
                    <td>${res[key].estado}</td>
                    <td>${formatDate(res[key].fecha_registra)}</td>
                    <td>${res[key].observacion}</td>
                </tr>`);
				}
			}
		);
	});

	$(".btnArchivos").on("click", function () {
		$("#modal_archivos_gestion").modal();
		console.log(id);
		consulta_ajax(
			`${ruta}listar_archivos_contratos`,
			{
				id_solicitud: id,
			},
			(res) => {
				$("#tabla_adjs_cont tbody").html("");
				for (const key in res) {
					$("#tabla_adjs_cont tbody").append(`<tr>
                    <td><a href="${Traer_Server()}archivos_adjuntos/contrataciones/${
						res[key].nombre_guardado
					}" target="_blank" style="background-color: #5cb85c;color: white;" class="pointer form-control">Ver</a></td>
                    <td>${res[key].nombre_real}</td>
                    <td>${formatDate(res[key].fecha_registra)}</td>
                </tr>`);
				}
			}
		);
	});
};

const login = (user, pass) => {
	$.ajax({
		url: `${ruta}login`,
		data: {
			user: user,
			pass: pass,
		},
		type: "POST",
		dataType: "json",
		success: function (res) {
			if (res.status === 0) {
				window.location.reload();
			} else {
				$("#login-error").html(res.message);
			}
		},
		error: function (xhr, status, error) {
			MensajeConClase(
				`Error al realizar la operación ${error}`,
				"error",
				"Oops.!"
			);
		},
	});
};

const listarContratos = (id) => {
	$("#contratos #lista-contratos").html("");
	consulta_ajax(`${ruta}listarContratos`, { id: id }, (res) => {
		contratos = res;
		for (const key in res) {
			let element = 
			`<div class="mt-1">
				<div class="d-flex m-4 align-items-stretch justify-content-center father-container">                                                        
					<div class="p-3 bd-highlight card card-contra ${res[key].contractive}">
						<img class="mb-3" src="${Traer_Server()}imagenes/req_aprendices.png" alt="" />
						<h6 class="card-title text-center">N° CONTRATO: ${res[key].num_con}</h6>
						<p class="small d-block m-3">
							<b>Inicio:</b> ${formatDate(res[key].fecha_ini)}<br>
							<b>Fin:</b> ${formatDate(res[key].fecha_ter)}<br>
							<b>Valor:</b> ${convertir_moneda(res[key].valor)}<br>
							<b>Estado:</b> ${res[key].estado_solicitud}
						</p>                                     
						${res[key].ver}                       
					</div>
					<div class="p-3 bd-highlight card card-crono ${res[key].cronoactive}">
						<img class="mb-3" src="${Traer_Server()}imagenes/facturacion.png" alt="" />
						<h6 class="card-title text-center">PAGO: N° <span class="c-item">${res[key].item_crono}</span></h6>
						<div class="small d-block m-3">
							<p class="mb-0"><b>Fecha:</b><span class="c-fecha"> ${formatDate(res[key].fecha_pago)}</span></p>
							<p class="mb-0"><b>Valor:</b><span> ${convertir_moneda(res[key].valor)}</span></p>
							<p class="mb-0"><b>Estado:</b><span class="c-estado"> ${res[key].crono_estado}</span></p>
						</div>                                        
						${res[key].ver_cronogramas}                        
					</div>
					<div class='container-contrato flex-grow-1'>
						<nav class="navbar-expand-lg navbar-light p-2" id="nav_admin_compras">
							<ul class="navbar-nav">
								<li class="pointer nav-item nav-link crea_contrato ${res[key].contractive}" data-id="${res[key].id}"><span class="fa fa-history purple"></span> Creacion del contrato</li>                        
								<li class="pointer nav-item nav-link soporte_pagos ${res[key].cronoactive}" data-id="${res[key].id}"><span class="fas fa-money-bill-wave purple"></span> Soportes de pagos</li>
							</ul>                 
						</nav>                             
						${res[key].stepper_contra}
						<div class='content-pagos p-4 ${res[key].cronoactive}'>                        
							${res[key].stepper_crono}
						</div>
					</div> 
				</div>
			</div>`
			$("#lista-contratos").append(element);
		}

		$(".crea_contrato").on("click", function () {			
			let container = this.closest(".father-container");
			this.classList.toggle('active', true)
			container.querySelector(".soporte_pagos").classList.toggle('active', false);
			container.querySelector(".content-contra").classList.toggle('active', true);
			container.querySelector(".content-pagos").classList.toggle('active', false);
			container.querySelector(".card-contra").classList.toggle('active', true);
			container.querySelector(".card-crono").classList.toggle('active', false);
		});

		$(".soporte_pagos").on("click", function () {
			let container = this.closest(".father-container");
			this.classList.toggle('active', true)
			container.querySelector(".crea_contrato").classList.toggle('active', false);
			container.querySelector(".content-contra").classList.toggle('active', false);
			container.querySelector(".content-pagos").classList.toggle('active', true);
			container.querySelector(".card-contra").classList.toggle('active', false);
			container.querySelector(".card-crono").classList.toggle('active', true);
		});

		$(".btnfirma").on("click", function () {
			$("#firmar").modal();
			let id = $(this).attr("data-id");
			$("#id_firma").val(id);
		});

		$(document).on("click", ".adjs_cronograma", function () {
			$("#modal_adjuntos_cronograma").modal();
			let id = $(this).attr("data-id");
			$("#id_archivo").val(id);
			$("#enviar_adjuntos").click(function () {
				if (num_archivos != 0) {
					tipo_cargue = 0;
					myDropzone.processQueue();
				} else {
					MensajeConClase("Seleccione Archivos a adjuntar.", "info", "Oops.!");
				}
			});
		});

		$(".ver_cronogramas").on("click", function () {
			let container = this.closest(".father-container");
			$("#modal_cronograma").modal();
			consulta_ajax(`${ruta}listar_cronogramas`, {id_solicitud: $(this).attr("data-id")} , (res) => {
				let tbody = document.querySelector("#tabla_cronograma tbody");
				tbody.innerHTML = '';
				let tr = '';
				if (res === 0 || !res.length) {
					tbody.innerHTML = '<tr><td colspan="4">No hay cronogramas disponibles</td></tr>';
				} else {
					for (const key in res) {
						tr += 
						`<tr>
							<td>${res[key].ver}</td>
							<td>${formatDate(res[key].especificaciones)}</td>
							<td>${res[key].estado}</td>
							<td>${res[key].acciones}</td>
						</tr>`
					}
					tbody.innerHTML = tr;
				}

				$(".adjuntados_cronogramas").on("click", function () {
					$("#modal_adjuntados_cronograma").modal();
					let id = $(this).attr("data-id");
					let tr = '';
					consulta_ajax(`${ruta}listar_adjuntos_cronograma`, { id: id }, (res) => {
						let tbody = document.querySelector("#tabla_cronograma_adjuntos tbody");
						tbody.innerHTML = '';
						if (res === 0 || !res.length) {
							tbody.innerHTML = '<tr><td colspan="4">No hay archivos disponibles</td></tr>';							
						} else {
							for (const key in res) {
								tr += 
								`<tr>
									<td>${res[key].ver}</td>
									<td>${res[key].nombre_real}</td>
									<td>${formatDate(res[key].fecha_registro)}</td>
								</tr>`
							}
							tbody.innerHTML = tr;
						}
					});
				});

				$(".select_crono").on("click", function () {
					consulta_ajax(`${ruta}stepper_cronograma`, { id_crono: $(this).attr("data-id")}, (res) => {
						container.querySelector(".content-pagos").innerHTML = res.stepper;
						container.querySelector(".card-crono .c-item").innerHTML = res.item;
						container.querySelector(".card-crono .c-fecha").innerHTML = "&nbsp;" + formatDate(res.fecha);
						container.querySelector(".card-crono .c-estado").innerHTML = "&nbsp;" + res.estado;
						$("#modal_cronograma").modal("hide");
					});
				});
			});
		});
	});
};

const convertir_moneda = (valor) => {
	let convierte = new Intl.NumberFormat("es-CO", {
		style: "currency",
		currency: "COP",
		minimumFractionDigits: 0,
	});
	return convierte.format(valor);
};

const formatDate = (date, style = 'medium') => {
	let formateador;
	date = date.split("-");
	let fecha = new Date(date);
	if (fecha == "Invalid Date") {
		return date;
	}
	let tiempo = `${fecha.getHours()}:${fecha.getMinutes()}:${fecha.getSeconds()}`;
	if (tiempo == '0:0:0') {
		formateador = new Intl.DateTimeFormat('es-CO', { dateStyle: style});	
	}else{
		formateador = new Intl.DateTimeFormat('es-CO', { dateStyle: style, timeStyle: 'medium' });
	}
	return formateador.format(fecha);
};

Dropzone.options.Subir = {
	url: ruta + "cargar_adj_cronograma", //se especifica cuando el form no tiene el aributo action, por default toma la url del action en el formulario
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
	acceptedFiles:
		"image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv,.gz,.gzip,.rar,.zip", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
	acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
	autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas
	error: function (response) {
		if (!response.xhr) {
			MensajeConClase(
				"Solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
				"info",
				"Oops!"
			);
		} else {
			errores.push(response.xhr.responseText);

			if (cargados == num_archivos) {
				if (tipo_cargue == 1) {
					MensajeConClase(
						"La solicitud fue guardada con exito, pero ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
						"info",
						"Oops!"
					);
				} else {
					MensajeConClase(
						"Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
						"info",
						"Oops!"
					);
				}
			}
		}
	},
	success: function (file, response) {
		let errorlist = "No ingresa";
		if (errores.length > 0) {
			errorlist = "";
			for (let index = 0; index < errores.length; index++) {
				errorlist = errorlist + errores[index] + ",";
			}
			if (tipo_cargue == 1) {
				MensajeConClase(
					"La solicitud fue guardada con exito, pero algunos Archivos No fueron cargados:\n\n" +
						errorlist +
						"\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
					"info",
					"Oops!"
				);
			} else {
				MensajeConClase(
					"Algunos Archivos No fueron cargados:\n\n" +
						errorlist +
						"\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!",
					"info",
					"Oops!"
				);
			}
		} else {
			if (tipo_cargue == 1) {
				MensajeConClase(
					"La solicitud fue Guardada con exito y Todos Los archivos fueron cargados.!",
					"success",
					"Proceso Exitoso!"
				);
			} else {
				MensajeConClase(
					"Todos Los archivos fueron cargados.!",
					"success",
					"Proceso Exitoso!"
				);
			}
		}
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
	},
	autoQueue: true,
	addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacci�n.
	previewsContainer: null, //define d�nde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
	capture: null,
	dictDefaultMessage: "Arrastra los archivos aqui para subirlos",
	dictFallbackMessage:
		"Su navegador no soporta arrastrar y soltar para subir archivos.",
	dictFallbackText:
		"Por favor utilize el formuario de reserva de abajo como en los viejos timepos.",
	dictFileTooBig:
		"La imagen revasa el tama�o permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.",
	dictInvalidFileType: "No se puede subir este tipo de archivos.",
	dictResponseError: "Server responded with {{statusCode}} code.",
	dictCancelUpload: "Cancel subida",
	dictCancelUploadConfirmation: "�Seguro que desea cancelar esta subida?",
	dictRemoveFile: "Eliminar archivo",
	dictRemoveFileConfirmation: null,
	dictMaxFilesExceeded: "Se ha excedido el numero de archivos permitidos.",
};