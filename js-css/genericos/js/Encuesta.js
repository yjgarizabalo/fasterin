let ruta_ambiental = `${Traer_Server()}index.php/encuesta_detalle_control/`;
let i = 0;
let respuesta = [];//Array de respuestas
let pasos = [];//Array de pasos
let data_respuestas = [];//Array para almacenar las respuestas
let data_preguntas = [];//Array para almacenar las preguntas
let atras = false;

$(document).ready(function() {

	listar_encuestas();

	$("#regresar_index").click(() => regresar());

	descripcion_encuesta(idp);

	mostrar_pasos(idp);
});


const obtener_pasos = async (idpp) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta_ambiental}obtener_pasos`, { idpp }, (respuesta) => {
			resolve(respuesta);
		});
	});
};

const obtener_respuesta = async (id_pregunta) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta_ambiental}obtener_respuesta`,
			{ id_pregunta },
			(respuesta) => {
				resolve(respuesta);
			}
		);
	});
};

const cambiar_respuesta = (idpregunta, idrespuesta, tipo = '')=>{
	data_preguntas.map(function(dato){
		if(dato.id_pregunta == idpregunta){
			dato.id_respuesta = idrespuesta;
			if(tipo){
				let respuesta = $(`#answer_${idpregunta}_${idrespuesta}`).val();
				dato.respuesta_abierta = respuesta;
			}else dato.respuesta_abierta = '';
		}
	})
}

const mostrar_respuestas = async (id_pregunta) => {
	respuesta = await obtener_respuesta(id_pregunta);
	let sw = '';
	let id = '';
	respuesta.forEach((elemento) => {
		id = elemento.id;
		found = data_respuestas.find((row) => row.id_pregunta === id_pregunta && row.id_paso === pasos[i].id_paso); // encontrar respuesta guardada
		sw = (found && found['id_respuesta'] === elemento.id) ? 'checked' : '';
		if(elemento.id_aux === 'resp_abierta'){
			$(`#respuesta_${id_pregunta}`).append(`
			<${elemento.valorx} class="form-control ${id_pregunta}" id="answer_${id_pregunta}_${elemento.id}" style="width:40%; height:12%; font-family: cuc; font-size: 16px;" onkeyup="cambiar_respuesta('${id_pregunta}','${elemento.id}','${elemento.id_aux}')" placeholder="Escriba su respuesta"></${elemento.valorx} maxlength="250">
			`);
		}else{
			$(`#respuesta_${id_pregunta}`).append(`
				<input type="radio" name="answer_${id_pregunta}" id="answer_${id_pregunta}_${elemento.id}" onclick="cambiar_respuesta('${id_pregunta}','${elemento.id}')" ${sw}>
				<label class="custom-control-label" for="answer_${id_pregunta}_${elemento.id}" style="text-transform: uppercase;">	
					${elemento.valor}
				</label><br>
			`);
		}
	});

	$(`input:radio[name='answer_${id_pregunta}']`).click(function() {
		$("#texto_pregunta_" + id_pregunta).css("color", "#6e1f7c");
	});

	$(`textarea[id='answer_${id_pregunta}_${id}']`).keyup(function() {
		$("#texto_pregunta_" + id_pregunta).css("color", "#6e1f7c");
	})

};

const mostrar_pasos = async (idpp) => {
	pasos = await obtener_pasos(idpp);
	pasos.forEach((pasos, index) => {
		$("#pasos").append(`
			<div class="c-stepper__item">    
            <span class="StepLabel">
                <span class="StepLabel-Icon">
                    <svg class="SvgIcon" focusable="false" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="12" fill="grey"></circle>
                        <text class="StepLabel-Text" x="12" y="16" text-anchor="middle">${index + 1}</text>
                    </svg>
                </span>
                <span class="StepLabel-Icon">
                    <span class="StepLabel-label">
                        <h3 class="c-stepper__title" style="text-transform: uppercase; font-weight: 400; font-size: 15px;
                        font-family: "Roboto", "Helvetica", "Arial", sans-serif;">${pasos.valor}</h3>
                    </span>
                </span>
            </span>
			</div>
            `);
	});
    
	$("#btn-iniciar_encuesta").click(async () => {
			$(".tituloacti").empty();
			$("#preguntas").before(`
			<div class="bs-stepper navbar  navbar-fixed-top ">
                <div class="bs-stepper-header">
                    <div class="step ">
                        <span class="bs-stepper-circle" id="numero_paso">${i + 1}</span>
                        <span class="bs-stepper-label" id="paso_valor" style="color:#d57e1c;">${pasos[i].valor}</span>
                    </div>
                </div>
            </div>
            `);
			mostrar_preguntas(pasos[i].id_paso);
			$("#preguntas").after(`
			<div id="encuesta-footer">
				<nav aria-label="Page navigation example" style="padding-top: 10px; padding-left: 24px; padding-right: 24px;">
					<ul class="pagination">
						<li class="paginate_button page-item disabled" id="ant"><a class="btn page-link" style="margin-right: 10px; font-weight: 500;font-family: 'cuc';" id="prev" name="prev" type="submit">REGRESAR</a></li>
						<li class="paginate_button page-item" id="sig"><a class="btn btn-primary" type="submit" id="next" name="next" style="color: white; border: none; background:#6e1f7c; font-family: cuc; font-weight: 500;"><span class="fa fa-arrow-right" style="color: white;"></span>SIGUIENTE</a></li>
					</ul>
				</nav> 
			</div>
            `);
	});
	
	
	$(document).on("click", "#next", async () => {
		let seguir = true;
		data_preguntas.map(function(dato){
			if(!document.querySelector(`input[name='answer_${dato.id_pregunta}']:checked`) && $("#answer_" + dato.id_pregunta + "_" + dato.id_respuesta).length === 0){
				MensajeConClase("Pregunta(s) sin responder: para continuar debe responder todas las preguntas", "info", "Oops!");
				seguir = false;
			}
		})
		if(seguir){
			i++;
			if(!atras){
				data_preguntas.map(function (dato) {
					var objeto = {
						id_paso: dato.vp_principal_id,//id_paso
						id_encuesta: idp,
						id_pregunta: dato.id_pregunta,
						id_respuesta: dato.id_respuesta,
						respuesta_abierta: dato.respuesta_abierta,
					}
					data_respuestas.push(objeto);
				});
			}

			if (i == pasos.length - 1) {
				$("#paso_valor").text(pasos[i].valor);
				$("#numero_paso").text(i + 1);
				mostrar_preguntas(pasos[i].id_paso);
				$("#next").text("FINALIZAR");
				$("#next").attr("id", "finalizar");
				$("#ant").removeClass("disabled");
				$(".page-link").css("color", "black");
			} else {
				$("#paso_valor").text(pasos[i].valor);
				$("#numero_paso").text(i + 1);
				mostrar_preguntas(pasos[i].id_paso);
				$("#next").text("SIGUIENTE");
				$("#ant").removeClass("disabled");
				$(".page-link").css("color", "black");
			}
		}
	});

	$(document).on("click", "#prev", function () {
		let seguir = true;
		data_preguntas.map(function(dato){
			if(!document.querySelector(`input[name='answer_${dato.id_pregunta}']:checked`) && $("#answer_" + dato.id_pregunta + "_" + dato.id_respuesta).length === 0){
				MensajeConClase("Pregunta(s) sin responder: para continuar debe responder todas las preguntas", "info", "Oops!");
				seguir = false;
			}
		})
		if(seguir){
			atras = true;
			i = i - 1;
			data_preguntas.map(function (dato) {
				var objeto = {
					id_paso: dato.vp_principal_id,//id_paso
					id_encuesta: idp,
					id_pregunta: dato.id_pregunta,
					id_respuesta: dato.id_respuesta,
					respuesta_abierta: dato.respuesta_abierta,
				}
				data_respuestas.push(objeto);
			});
			$("#paso_valor").text(pasos[i].valor);
			$("#numero_paso").text(i + 1);
			mostrar_preguntas(pasos[i].id_paso);
			$("#finalizar").text("SIGUIENTE");
			$("#finalizar").attr("id", "next");
			if (i == 0) {
				$("#ant").addClass("disabled");
				$(".page-link").css("color", "#636c72");
				$("#finalizar").text("SIGUIENTE");
				$("#finalizar").attr("id", "next");
			}	
		}
	});

	$(document).on("click", "#finalizar", async () => {
		let seguir = true;
		data_preguntas.map(function(dato){
			if(!document.querySelector(`input[name='answer_${dato.id_pregunta}']:checked`) && $("#answer_" + dato.id_pregunta + "_" + dato.id_respuesta).length === 0){
				MensajeConClase("Pregunta(s) sin responder: para continuar debe responder todas las preguntas", "info", "Oops!");
				seguir = false;
			}
		})
		if(seguir){
			data_preguntas.map(function (dato) {
				var objeto = {
					id_paso: dato.vp_principal_id,//id_paso
					id_encuesta: idp,
					id_pregunta: dato.id_pregunta,
					id_respuesta: dato.id_respuesta,
					respuesta_abierta: dato.respuesta_abierta,
				}
				data_respuestas.push(objeto);
			});
			msj_confirmacion("¿Desea finalizar la encuesta?", "", () => {
				guardar_respuestas();
			})
			
		}
	});
};

const descripcion_encuesta = (idpp) => {
	consulta_ajax(`${ruta_ambiental}get_descripcion`, { idpp }, (respuesta) => {
		$("#descripcion").append(`
            ${respuesta.valorx}
        `);
	});
};

const obtener_preguntas = async (id_paso) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta_ambiental}get_preguntas`,
			{ id_paso },
			(respuesta) => {
				resolve(respuesta);
			}
		);
	});
};

const msj_confirmacion = (title, text, callback) => {
	swal({
		title,
		text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si!",
		cancelButtonText: "No!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	}, confirm => {
		if (confirm) callback();
	});
};

const mostrar_preguntas = async (id_paso) => {
	data_preguntas = await obtener_preguntas(id_paso);
	$("#preguntas").empty();
	data_preguntas.forEach((preguntas, index) => {
		$("#preguntas").append(`
			<div class="row list-dropdown mandatory" id="question-container" data-id="question" style="margin-left: 20;">
				<div class="" style="padding-top: 20;">
					<div class="">
						<h3 class="c-stepper__title" style="text-transform: uppercase; font-weight: 400; font-size: 15px;
						font-family: cuc; color=" id="texto_pregunta_${preguntas.id_pregunta}"><span style="text-transform: uppercase; font-weight: 400; font-size: 15px;
						font-family: cuc;">${index + 1}.</span>&nbsp;&nbsp;${preguntas.valor}*</h3><br>
						<div class="form-check text-left" id="respuesta_${preguntas.id_pregunta}">
							
						</div>
					</div>
				</div>
			<div/>  
			`);
		mostrar_respuestas(preguntas.id_pregunta);
	});
};

const guardar_respuestas = () => {
	const data = {data_respuesta: data_respuestas/* , id_encuesta: idp */};
	consulta_ajax(`${ruta_ambiental}guardar_respuestas`, data, respuesta => {
		let { tipo, mensaje, titulo } = respuesta;
		if (tipo == "success") {
			$("#preguntas").empty();
			$("#preguntas").append(`<div class="col-md-12 text-center">
			<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
			<h4><b>ENCUESTA FINALIZADA</b></h4>
			</br><a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>            
			</div>`)
			$("#encuesta-footer").empty();
			$(".bs-stepper-header").empty();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const listar_encuestas = () =>{

	$("#tabla_encuestas tbody").off("click", "tr td .ver");
	const table = $("#tabla_encuestas").DataTable({
        destroy: true,
        ajax: {
            url: `${ruta_ambiental}listar_encuestas`,
            dataType: "json",
            type: "post",
            data: respuesta,
            dataSrc: (json) => {
                return json.length == 0 ? Array() : json.data;
            },
        },
        searching: false,
        processing: true,
		columns: [
			{data: "ver" },
			{data: "id",}, 
			{data: "valor"}, 
			{data: "fecha_registra",},
		],
        language: idioma,
        dom: "Bfrtip",
        buttons: [],
    });
	$("#tabla_encuestas tbody").on("click", "tr td .ver",function() {
		const data = table.row($(this).parent()).data();
		listar_encuestas_usuario(data.id);
		$("#modal_ver_encuesta").modal("show");
	});
}

const listar_encuestas_usuario = (id_encuesta) =>{
	$("#tabla_detalle_encuesta tbody").off("click", "tr td .ver");
	const table = $("#tabla_detalle_encuesta").DataTable({
		destroy: true,
		ajax: {
			url: `${ruta_ambiental}listar_encuestas_usuario`,
			dataType: "json",
			type: "post",
			data: {id_encuesta},
			dataSrc: (json) => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		searching: false,
		processing: true,
		columns: [
			{data: "ver" },
			{data: "nombre_completo" },
			{data: "identificacion"}, 
			{data: "fecha"},
		],
		language: idioma,
		dom: "Bfrtip",
		buttons: [],
	});
	$("#tabla_detalle_encuesta tbody").on("click", "tr td .ver",function() {
		const data = table.row($(this).parent()).data();
		detalle_encuesta(data.id_persona);
		$("#modal_ver_detalle_encuesta").modal("show");
	});
}

const ver_respuesta = (id_paso, id_persona) =>{
	let dato = '';
	consulta_ajax(`${ruta_ambiental}ver_respuesta`, {id_paso, id_persona}, respuesta => {
		$(`#ver_resp_${id_paso}`).html('');
		respuesta.forEach((element, index) => {
			dato = !element.respuesta_abierta ? element.respuesta : element.respuesta_abierta;
			$(`#ver_resp_${id_paso}`).append(`
				<div class="row">
					<div class="col-md-12">
						<h5 class="card-title" style="font-size: 16px; text-align: initial; font-weight: 500;">${index + 1}. ${element.pregunta}</h5>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p class="card-text" style="font-size: 16px; text-align: initial; font-weight: 500;">RT:// ${dato}</p>
					</div>
				</div>
			`);
		})
	})
}

const detalle_encuesta = (id_persona) =>{
	consulta_ajax(`${ruta_ambiental}detalle_encuesta`, {id_persona}, respuesta => {
		$("#tarjeta").html('');
		respuesta.forEach((element) => {
			$("#tarjeta").append(`
			<div class="card-header">
				<h2 class="mb-0">
					<button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#resp_${element.id_paso}" aria-controls="resp_${element.id_paso}" style="text-decoration: none; color: black; text-transform: uppercase; font-family: 'cuc';
					font-weight: 600; text-align: initial;" onclick="ver_respuesta(${element.id_paso}, ${id_persona})"><span class="fa fa-check-circle fa-lg" aria-hidden="true" style="color: purple; margin-right: 10px;"></span>${element.paso}</button>
				</h2>
			</div>
			<div id="resp_${element.id_paso}" class="collapse" data-parent="#accordion">
				<div class="card-body" id="ver_resp_${element.id_paso}" style="margin-left: -90px;">
													
				</div> 
			</div>
			`);
		})
		$(".card-header").css({
			"padding": "0.75rem 1.25rem",
			"margin-bottom": "0",
			"background-color": "rgba(0,0,0,.03)",
			"border-bottom": "1px solid rgba(0,0,0,.125)",
		})
	})
}

