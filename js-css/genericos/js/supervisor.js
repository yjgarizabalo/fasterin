const RUTA = `${Traer_Server()}index.php/supervisor_control/`;
const ADJSPATH = `${Traer_Server()}archivos_adjuntos/supervisor/`;
let currentActive = 1;
let active = 0;
let callbak_activo = resp => { };
let id_turno = '';
let persona = '';
let id_sala = '';
let tipo = '';
let tomo_foto = false;
let tiene_camara = true;
let localVideo;
let localStream;

$(document).ready(function () {

    estadoSupervisor();
    /* Eventon para btn administrar */
    $('#btn_admin').on('click', function () {
        MostrarModalAdministrar();
    });
    $('#btn_filtrar').on('click', function () {
        $('#modal_filtrar_supervisores').modal();
    });
    $('#limpiar_filtros_sup').on('click', function () {
        estadoSupervisor();
    });

    $("#admin_salas").click(() => {
        ObtenerSalas();
        $('#container_admin_turnos').addClass("oculto");
        $('#container_admin_supervisores').addClass("oculto");
        $('#container_admin_salas').removeClass("oculto");
        //  listar_salas_computo();
        $("#admin_supervisor").removeClass("active");
        $("#admin_turno").removeClass("active");
        $("#admin_salas").addClass("active");
    });

    $("#admin_supervisor").click(() => {
        listarSupervisores();
        $('#container_admin_salas').addClass("oculto");
        $('#container_admin_turnos').addClass("oculto");
        $('#container_admin_supervisores').removeClass("oculto");
        //  listar_salas_computo();
        $("#admin_turno").removeClass("active");
        $("#admin_supervisor").addClass("active");
        $("#admin_salas").removeClass("active");
    });

    $("#admin_turno").click(() => {
        listar_turnos_spv();
        $('#container_admin_supervisores').addClass("oculto");
        $('#container_admin_turnos').removeClass("oculto");
        $('#container_admin_salas').addClass("oculto");
        $("#admin_supervisor").removeClass("active");
        $("#admin_turno").addClass("active");
        $("#admin_salas").removeClass("active");
    });

    $(".btn_new_turno_spv").click(() => {
        id_turno = '';
        $(".titulo_modal_spv").html("Nuevo Turno");
        $("#form_guardar_turnospv").get(0).reset();
        $("#modal_crear_turno_spv").modal();
    });

    $(".spv_sala_new").click(() => {
        id_turno = '';
        $(".titulo_modal_salaspv").html("Nueva Sala");
        $("#form_guardar_salaspv").get(0).reset();
        $("#modal_crear_sala_spv").modal();
    });

    $("#fin_rev_uno").click(() => {
        cerrar_revisiones('Rev_Ent_Sup');
    });

    $("#fin_rev_dos").click(() => {
        cerrar_revisiones('Rev_Sal_Sup');
    });

    $("#form_guardar_entrada_salida").submit(() => {
        guardar_entrada_salida();
        return false;
    });

    $("#form_guardar_revision").submit(() => {
        guardar_revision();
        return false;
    });
    $("#btn_generar_filtro").click(() => {
        filtrar_supervisor();
    });
    // $(".avanzar").click(() => {
    //  circles=document.querySelectorAll(".circle");
    //   currentActive++;
    //   if(currentActive>circles.length){
    //     currentActive=circles.length;
    //   }
    //   rotar();
    // });
    $("#circle-uno").click(() => {
        currentActive = 1;
        active = 0;
        rotar();
    });
    $("#circle-dos").click(() => {
        currentActive = 2;
        active = 1;
        rotar();
    });
    $("#circle-tres").click(() => {
        currentActive = 3;
        active = 2;
        rotar();
    });
    $("#circle-cuatro").click(() => {
        currentActive = 4;
        active = 3;
        rotar();
    });

    // $(".retroceder").click(() => {
    //   currentActive--;
    //   if(currentActive<1){
    //     currentActive=1;
    //   }
    //   rotar();
    // });
    $("#btn_mis_salas").click(function () {
        $("#nombre_modal_salas_turnos").html('MIS SALAS');
        listar_salas_turnos(1);
        $("#modal_salas_turnos").modal();
    });

    $("#btn_mis_turnos").click(function () {
        $("#nombre_modal_salas_turnos").html('MIS TURNOS');
        listar_salas_turnos(2);
        $("#modal_salas_turnos").modal();
    });
    $("#btn_detalle_supervisor").click(function () {
        $("#modal_detalle_supervisor").modal();
    });

});

/*Llamadas a los modal*/

const cargar_modal_entrada = () =>{
    tomo_foto = false;
    configuracion_camara();
    $("#tipo").val("Ent_Sup");
    $(".titulo_entrada_salida").html("Entrada");
    $("#form_guardar_entrada_salida").get(0).reset();
    $("#Modal_Entrada").modal();
  
}

const cargar_modal_salida = () =>{
        tomo_foto = false;
        configuracion_camara();
        $("#foto_entrada").val("");
        $("#tipo").val("Sal_Sup");
        $(".titulo_entrada_salida").html("SALIDA");
        $("#form_guardar_entrada_salida").get(0).reset();
        $("#Modal_Entrada").modal();
}

const cargar_modal_rev_uno = () =>{
    cerrar_revisiones('Rev_Ent_Sup');
}
const cargar_modal_rev_dos = () =>{
    cerrar_revisiones('Rev_Sal_Sup');
}
/* FUNCIONES FUERA DEL READY */
const configuracion_camara = (btn = 'botonFoto', camara = 'camara', foto = 'foto_entrada',dir_cam="user") => {
    var canvas = document.getElementById(foto);
    canvas.width = canvas.width;
    localVideo = document.querySelector(`video#${camara}`);

    navigator.mediaDevices.getUserMedia({"video":{facingMode:dir_cam}})
        .then(gotStream)
        .catch(e => console.log('getUserMedia() error: ' + e.name));


    jQuery(`#${btn}`).on('click', e => {
        tomo_foto = true;
        let oCamara, oFoto, oContexto, w, h;

        oCamara = jQuery(`#${camara}`);
        oFoto = jQuery(`#${foto}`);
        w = oCamara.width();
        h = oCamara.height();
        oFoto.attr({
            'width': w,
            'height': h
        });
        oContexto = oFoto[0].getContext('2d');
        oContexto.drawImage(oCamara[0], 0, 0, w, h);

    });
}

function gotStream(stream) {
    localStream = stream;
    localVideo.srcObject = stream;
  }

const MostrarModalAdministrar = () => {
    listarSupervisores();
    $('#modalAdministrar').modal();
}

const estadoSupervisor = () => {
    perfil = 'Per_Sup';
    $("#tabla_estado_supervisor tbody")
        .off("click", "tr .ver_detalle");
    consulta_ajax(`${RUTA}estadoSupervisor`, { perfil }, data => {
        const myTable = $("#tabla_estado_supervisor").DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data,
            columns: [
                { data: 'ver' },
                { data: 'nombre' },
                { data: 'hora_entrada' },
                { data: 'hora_salida' },
                { data: 'estado' },
                { data: 'accion' },
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
            "order": [[ 4, "asc" ]]
        });
        $(`#tabla_estado_supervisor tbody`).on("click", "tr .ver_detalle", function () {
            let {id_solicitud, id_persona} = myTable.row($(this).parent().parent()).data();
            mostrar_detalles(id_solicitud,id_persona,"");
        });
    });
}

const mostrar_detalles=(id_solicitud,id_persona,fecha="")=>{
    $("#tabla_detalle_supervisor tbody").off("click", "tr .ver_evidencia").off("click", "tr .ver_novedad");
    consulta_ajax(`${RUTA}traer_detalles`, { id_solicitud,id_persona,fecha }, data => {
        const myTable = $("#tabla_detalle_supervisor").DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [
                { data: 'proceso' },
                { data: 'fecha_registro' },
                { data: 'accion' },
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
            "aaSorting": [],
        });
        $(`#tabla_detalle_supervisor tbody`).on("click", "tr .ver_evidencia", function () {
            let {imagen} = myTable.row($(this).parent().parent()).data();
                window.open(`${ADJSPATH}` + imagen+ `.png`, "Evidencia", "width=400, height=300");
        });
        $(`#tabla_detalle_supervisor tbody`).on("click", "tr .ver_novedad", function () {
            let {id_solicitud, id_estado} = myTable.row($(this).parent().parent()).data();
            mostrar_novedad(id_solicitud, id_estado);
        });
    });
    $("#modal_detalle_supervisor").modal();
}

const mostrar_novedad=(id_solicitud, id_estado,fecha)=>{
    $("#tabla_novedades tbody")
    .off("click", "tr .ver_evidencia");
    consulta_ajax(`${RUTA}traer_novedades`, {id_solicitud,id_estado,fecha}, data => {
        const myTable = $("#tabla_novedades").DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data,
            columns: [
                { data: 'sala' },
                { data: 'descripcion' },
                { data: 'fecha_registro' },
                { data: 'accion' },
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
        });
        $(`#tabla_novedades tbody`).on("click", "tr .ver_evidencia", function () {
            let data = myTable.row($(this).parent().parent()).data();
                window.open(`${ADJSPATH}` + data.evidencia+ `.png`, "Evidencia", "width=400, height=300");
        });
    });
    $("#modal_novedades").modal();
}

const ObservacionSala = (id, tipo) => {
    tomo_foto = false;
    configuracion_camara('botonFotoR', 'camara_r', 'foto_revision',"environment");
    $("#tipo_revision").val(tipo);
    $("#sala_sup").val(id);
    $(".titulo_revision").html("REVISIÓN");
    $("#form_guardar_revision").get(0).reset();
    $("#Modal_Revision").modal();
}

const listarSupervisores = () => {
    perfil = 'Per_Sup';
    $("#tablaSupervisores tbody")
        .off("click", "tr .sala")
        .off("click", "tr .turno")
        .off("click", "tr")
        .off("click", "tr td:nth-of-type(1)");
    consulta_ajax(`${RUTA}obtenerSupervisores`, { perfil }, data => {
        const myTable = $("#tablaSupervisores").DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data,
            columns: [
                { data: 'nombre' },
                { data: 'identificacion' },
                { data: 'correo' },
                { data: 'accion' },
                //{ render: () => { return btnAccion } }
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
        });
        $(`#tablaSupervisores tbody`).on("click", "tr .sala", function () {
            let data = myTable.row($(this).parent().parent()).data();
            persona = data;
            asignar_sala_spv();

        });
        $(`#tablaSupervisores tbody`).on("click", "tr .turno", function () {
            let data = myTable.row($(this).parent().parent()).data();
            persona = data;
            asignar_turno_spv();
        });
    });
}

//
const listar_asignar_salas = () => {
    id_persona = persona.id;
    consulta_ajax(`${RUTA}listar_asignar_salas`, { id_persona }, data => {
        $(`#tablaSalas tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .asignar").off("click", "tr .desasignar");
        const myTable = $("#tablaSalas").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data,
            columns: [
                {
                    data: "nombre"
                },
                {
                    data: "accion"
                },
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });
        $("#tablaSalas tbody").on("click", "tr", function () {
            $("#tablaSalas tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tablaSalas tbody").on("dblclick", "tr", function () {
            $("#tablaSalas tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tablaSalas tbody").on("click", "tr .asignar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            id_sala = id;
            asignar_sala_supervisor();
        });
        $("#tablaSalas tbody").on("click", "tr .desasignar", function () {
            let { id_desasignar } = myTable.row($(this).parent().parent()).data();
            desasignar_sala(id_desasignar);
        });
    });
}

//Crear Los Turnos 
const guardar_turno_spv = () => {
    let fordata = new FormData(document.getElementById("form_guardar_turnospv"));
    let data = formDataToJson(fordata);
    data.id_turno = id_turno;
    consulta_ajax(`${RUTA}guardar_turno_spv`, data, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            id_turno = '';
            $("#form_guardar_turnospv").get(0).reset();
            $("#modal_crear_turno_spv").modal("hide");
            listar_turnos_spv();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

const Botones = (val) => {
    alert(val);
}

//Crear Los Salas 
const guardar_sala_spv = () => {
    let fordata = new FormData(document.getElementById("form_guardar_salaspv"));
    let data = formDataToJson(fordata);
    data.id_sala = id_sala;
    consulta_ajax(`${RUTA}guardar_sala_spv`, data, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            id_sala = '';
            $("#form_guardar_salaspv").get(0).reset();
            $("#modal_crear_sala_spv").modal("hide");
            ObtenerSalas();
        }
        MensajeConClase(mensaje, tipo, titulo);
    });
}

//Lista principal de las Salas
const ObtenerSalas = (parametro = '') => {
    consulta_ajax(`${RUTA}obtenerSalas`, { parametro }, resp => {
        $(`#tablaSalas_d tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .modificar").off("click", "tr .eliminar");
        const myTable = $("#tablaSalas_d").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "nombre"
                },
                {
                    data: "valorx"
                },
                {
                    data: "accion"
                }
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });
        $("#tablaSalas_d tbody").on("click", "tr", function () {
            $("#tablaSalas_d tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tablaSalas_d tbody").on("dblclick", "tr", function () {
            $("#tablaSalas_d tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tablaSalas_d tbody").on("click", "tr .modificar", function () {
            let data = myTable.row($(this).parent()).data();
            ver_sala(data);
        });
        $("#tablaSalas_d tbody").on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            id_sala = id;
            delete_sala(id);
        });
    });
}
//Lista principal de los Turnos
const listar_turnos_spv = (parametro = '') => {
    consulta_ajax(`${RUTA}listar_turnos_spv`, { parametro }, resp => {
        $(`#tabla_turnos_sup tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .modificar").off("click", "tr .eliminar");
        const myTable = $("#tabla_turnos_sup").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "dia"
                },
                {
                    data: "hora_entrada"
                },
                {
                    data: "hora_salida"
                },
                {
                    data: 'accion'
                }
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });
        $("#tabla_turnos_sup tbody").on("click", "tr", function () {
            $("#tabla_turnos_sup tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tabla_turnos_sup tbody").on("dblclick", "tr", function () {
            $("#tabla_turnos_sup tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tabla_turnos_sup tbody").on("click", "tr .modificar", function () {
            let data = myTable.row($(this).parent()).data();
            ver_turno(data);
        });
        $("#tabla_turnos_sup tbody").on("click", "tr .eliminar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            id_turno = id;
            delete_turno(id);
        });
    });
}
//Listar Turnos para asignación
const listar_asignar_turnos = () => {
    id_persona = persona.id;
    consulta_ajax(`${RUTA}listar_asignar_turnos`, { id_persona }, resp => {
        $(`#tabla_asignar_turnos tbody`).off("dblclick", "tr").off("click", "tr").off("click", "tr .asignar").off("click", "tr .eliminar");
        const myTable = $("#tabla_asignar_turnos").DataTable({
            destroy: true,
            searching: true,
            processing: true,
            data: resp,
            columns: [
                {
                    data: "dia"
                },
                {
                    data: "hora_entrada"
                },
                {
                    data: "hora_salida"
                },
                {
                    data: 'accion'
                }
            ],
            language: get_idioma(),
            dom: "Bfrtip",
            buttons: []
        });
        $("#tabla_asignar_turnos tbody").on("click", "tr", function () {
            $("#tabla_asignar_turnos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tabla_asignar_turnos tbody").on("dblclick", "tr", function () {
            $("#tabla_asignar_turnos tbody tr").removeClass("warning");
            $(this).attr("class", "warning");
        });
        $("#tabla_asignar_turnos tbody").on("click", "tr .asignar", function () {
            let { id } = myTable.row($(this).parent().parent()).data();
            asignar_turno_supervisor(id);
        });
        $("#tabla_asignar_turnos tbody").on("click", "tr .desasignar", function () {
            let { id_desasignar } = myTable.row($(this).parent().parent()).data();
            desasignar_turno(id_desasignar);
        });
    });
}
//Eliminar Turnos
const delete_turno = (id) => {
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
            consulta_ajax(`${RUTA}delete_turno`, { id }, resp => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == "success") {
                    listar_turnos_spv();
                }
                MensajeConClase(mensaje, tipo, titulo);


            });
        }
    });

};

//Eliminar Salas
const delete_sala = (id) => {
    swal({
        title: "Estas Seguro ?",
        text: "Si desea eliminar esta sala presione la opción de 'Si, Entiendo'.!",
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
            consulta_ajax(`${RUTA}delete_sala`, { id }, resp => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == "success") {
                    ObtenerSalas();
                }
                MensajeConClase(mensaje, tipo, titulo);


            });
        }
    });

};
//Ver Turnos para Modificar
const ver_turno = (data) => {
    let { id, id_dia, hora_entrada, hora_salida, observacion } = data;
    id_turno = id;
    $(".titulo_modal_spv").html("Modificar Turno");
    $("#id_dia_spv").val(id_dia);
    $("#hora_inicio_spv").val(hora_entrada);
    $("#hora_fin_spv").val(hora_salida);
    $("#descripcion_spv").val(observacion);
    $("#modal_crear_turno_spv").modal();
}

//Ver Salas para Modificar
const ver_sala = (data) => {
    let { id, nombre, valorx } = data;
    id_sala = id;
    $(".titulo_modal_spv").html("Modificar Sala");
    $("#nombre_sala").val(nombre);
    $("#descripcion_sala").val(valorx);
    $("#modal_crear_sala_spv").modal();
}
//Asignar sala a Supervisor
const asignar_sala_supervisor = () => {
    id_persona = persona.id;
    consulta_ajax(`${RUTA}asignar_sala_supervisor`, { id_sala, id_persona }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            swal.close();
            listar_asignar_salas();
        } else { MensajeConClase(mensaje, tipo, titulo); }

    });
}

//Asignar Turno a supervisor
const asignar_turno_supervisor = (id_turno) => {
    id_persona = persona.id;
    consulta_ajax(`${RUTA}asignar_turno_supervisor`, { id_persona, id_turno }, resp => {
        let { titulo, mensaje, tipo } = resp;
        if (tipo == "success") {
            swal.close();
            listar_asignar_turnos();
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }

    });
}

//Retirar turno del supervisor 
const desasignar_turno = (id) => {
    swal({
        title: "Estas Seguro ?",
        text: "Si desea eliminar" + id + " el supervisor presione la opción de 'Si, Entiendo'.!",
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
            consulta_ajax(`${RUTA}desasignar_turno`, { id }, resp => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == "success") {
                    swal.close();
                    listar_asignar_turnos();
                } else {
                    MensajeConClase(mensaje, tipo, titulo);
                }


            });
        }
    });

};

//Retirar sala del supervisor
const desasignar_sala = (id) => {
    swal({
        title: "Estas Seguro ?",
        text: "Si desea retirar la sala presione la opción de 'Si, Entiendo'.!",
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
            consulta_ajax(`${RUTA}delete_sala_turno`, { id }, resp => {
                let { titulo, mensaje, tipo } = resp;
                if (tipo == "success") {
                    swal.close();
                    listar_asignar_salas()
                } else { MensajeConClase(mensaje, tipo, titulo); }

            });
        }
    });

};

//Mensaje de Asignar
const asignar_sala_spv = () => {
    callbak_activo = data => {
        swal({
            title: "Estas Seguro ?",
            text: `Está seguro de asignarle ${data.nombre} a ${persona.nombre}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
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
                    id_sala = data.id;
                    asignar_sala_supervisor();
                }
            }
        );

    };
    listar_asignar_salas(callbak_activo);
    $("#modal_buscar_salas").modal();
};

const asignar_turno_spv = () => {
    callbak_activo = data => {
        swal({
            title: "Estas Seguro ?",
            text: `Esta seguro de asignarle este turno a ${data.nombre}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
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
                    asignar_turno_supervisor(id_turno);
                }
            }
        );

    };
    listar_asignar_turnos('', callbak_activo);
    $("#modal_buscar_turnos").modal();

}


const rotar = (posicion = "") => {
    progress = document.getElementById("progress");
    circles = document.querySelectorAll(".circle");
    container = document.querySelectorAll(".conti");
    if (posicion != "") {
        posicion = posicion == 4 ? 3 : posicion;
        currentActive = posicion + 1;
        active = posicion;
    }
    circles.forEach((circle, idx) => {
        if (idx < currentActive) {
            circle.classList.add("active");
        } else {
            circle.classList.remove("active");
        }

    });
    container.forEach((cont, idx) => {
        if ((idx + 1) == currentActive) {
            cont.classList.remove("act");
        } else {
            cont.classList.add("act");
        }
    });
    progress.style.width = (active / (circles.length - 1)) * 100 + "%";
}


const listar_salas_turnos = (tipo) => {
    $("#tabla_salas_turnos thead").html('');
    let num = 1;
    if (tipo == 1) {
        $("#tabla_salas_turnos thead").append(`<tr>
			<th>No.</th>
            <th>NOMBRE</th>
			<th>DESCRIPCIÓN</th>
	  	</tr>`);
        consulta_ajax(`${RUTA}SalasSupervisor`, { tipo }, data => {
            $(`#tabla_salas_turnos tbody`).html("").off('dblclick', 'tr').off('click', 'tr');
            data.map(({ descripcion, nombre }) => {
                $("#tabla_salas_turnos tbody").append(`<tr><td>${num++}</td><td>${nombre}</td><td>${descripcion}</td></tr>`);
            });
        });
    } else {
        $("#tabla_salas_turnos thead").append(`<tr>
			<th>No.</th>
			<th>DIA</th>
			<th>HORA INICIO</th>
            <th>HORA FIN</th>
	  	</tr>`);
        consulta_ajax(`${RUTA}TurnosSupervisor`, { tipo }, data => {
            $(`#tabla_salas_turnos tbody`).html("").off('dblclick', 'tr').off('click', 'tr');
            data.map(({ dia, hora_entrada, hora_salida }) => {
                $("#tabla_salas_turnos tbody").append(`<tr><td>${num++}</td><td>${dia}</td><td>${hora_entrada}</td><td>${hora_salida}</td></tr>`);
            });
        });
    }
}

const guardar_entrada_salida = () => {
    let data = new FormData(document.getElementById("form_guardar_entrada_salida"));
    if (tomo_foto) {
        canvas = document.getElementById("foto_entrada");
        let foto = canvas.toDataURL("image/jpeg");
        let info = foto.split(",", 2);
        data.append("foto_entrada", info[1]);
    }
    enviar_formulario(`${RUTA}guardar_entrada_salida`, data, (resp) => {
        let { titulo, mensaje, tipo, id_persona } = resp;
        if (mensaje == "sin_session") {
            close();
        } else if (tipo == 'success') {
            MensajeConClase(mensaje, tipo, titulo);
            obtener_estado_supervisor(id_persona);
            $("#form_guardar_entrada_salida").get(0).reset();
            $("#Modal_Entrada").modal("hide");
            localStream.getTracks().forEach(track => track.stop());
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        tomo_foto = false;
    });
}

const obtener_estado_supervisor = (id_persona) => {
    consulta_ajax(`${RUTA}obtener_estado_supervisor`, { id_persona }, data => {
        let { entrada, salida, rev_entrada, rev_salida,novedad_salida, novedad_entrada,mis_salas,posicion } = data;
        let vista_entrada, vista_salida;
        if (entrada.length != 0) {
            for (ent of entrada) {
                vista_entrada = `<b class='' style='color: black'>Su entrada fue registrada en la siguiente fecha y hora</b><br><b class='' style='color: black'>${ent.fecha_registro}</b><br>`;
            }
        } else {
            vista_entrada = `<b class='' style='color: black'>Antes de avanzar recuerde registrar su entrada</b><br>
                            <div class='row '>
                            <div class='col-lg-12 ml-auto'>
                                <div class=''>
                                    <span class='btn btn-primary mt-3' onclick="cargar_modal_entrada()" >Registrar Entrada</span>
                                </div>
                            </div>
                            </div>`;
        }
        if (rev_entrada.length != 0) {
            for (sal of rev_entrada) {
                let mensaje;
                if(novedad_entrada.length == 0){
                    mensaje="Sin novedad";
                }else{
                    mensaje="Se registro "+novedad_entrada.length+" novedades";
                }
                $('#contenedor_rev_entrada').html("");
                $('#contenedor_btn_rev_entrada').html(`<b class='' style='color: black'>La revisión de entrada  se realizo en la siguiente fecha y hora: </b><br><b class='' style='color: black'>${sal.fecha_registro}</b><br><b class="" style="color: black">${mensaje}</b><br><br>`);
            }
        } else if (entrada.length != 0) {
            if(mis_salas.length==0){
                $('#contenedor_rev_entrada').append(`<b style="color: black">No cuenta con salas asignadas</b><br></br>`);
            }else{
                for (sala of mis_salas){
                    $('#contenedor_rev_entrada').append(`<div class="col-md-6 col-lg-4 mb-5">
                      <div class="card">
                        <br>
                        <img class="img-fluid" src="${Traer_Server()}imagenes/laboratorios.png" alt="" />
                        <div class="card-body" style="height:150px">
                          <h5 class="card-title text-center" style="color : black">${sala.nombre}</h5><br>
                            <!-- <p class="card-text" style="color : black"><strong>Novedades: 0</strong></p> -->
                            <button class="btn btn-secondary btn-block mt-3" type="button"  onclick="ObservacionSala(${sala.id_sala},'Rev_Ent_Sup');" aria-haspopup="true" aria-expanded="false" onclick="">Novedades</button>
                        </div>
                      </div>
                    </div>`);  
                 }
                 $('#contenedor_btn_rev_entrada').html(`<button class="btn btn-primary mt-3" onclick ="cargar_modal_rev_uno();">Finalizar</button> `);
            }
        } else {
            $('#contenedor_rev_entrada').html("");
              $('#contenedor_btn_rev_entrada').html(`<b class="" style="color: black">Para continuar con el proceso recuerde realizar los pasos anteriores</b><br>`);
        }
        if (rev_salida.length != 0) {
            for (sal of rev_salida) {
                let mensaje;
                if(novedad_salida.length == 0){
                    mensaje="Sin novedad";
                }else{
                    mensaje="Se registro "+novedad_salida.length+" novedades";
                }
                $('#contenedor_rev_salida').html("");
                $('#contenedor_btn_rev_salida').html(`<b class='' style='color: black'>La revisión de salida  se realizo en la siguiente fecha y hora: </b><br><b class='' style='color: black'>${sal.fecha_registro}</b><br><b class="" style="color: black">${mensaje}</b><br>`);
            }
        } else if (rev_entrada.length == 0) {
            $('#contenedor_rev_salida').html("");
             $('#contenedor_btn_rev_salida').html(`<b class="" style="color: black">Para continuar con el proceso recuerde realizar los pasos anteriores</b><br>`);
        } else {
            if(mis_salas.length==0){
                $('#contenedor_rev_salida').html(`<b style="color: black">No cuenta con salas asignadas</b><br></br>`);
            }else{
    
                for (sala of mis_salas){
                    $('#contenedor_rev_salida').append(`<div class="col-md-6 col-lg-4 mb-5">
                      <div class="card">
                        <br>
                        <img class="img-fluid" src="${Traer_Server()}imagenes/laboratorios.png" alt="" />
                        <div class="card-body" style="height:150px">
                          <h5 class="card-title text-center" style="color : black">${sala.nombre}</h5><br>
                            <!-- <p class="card-text" style="color : black"><strong>Novedades: 0</strong></p> -->
                            <button class="btn btn-secondary btn-block mt-3" type="button"  onclick="ObservacionSala(${sala.id_sala},'Rev_Sal_Sup');" aria-haspopup="true" aria-expanded="false" onclick="">Novedades</button>
                        </div>
                      </div>
                    </div>`);  
                 }
                 $('#contenedor_btn_rev_salida').html(`<button class="btn btn-primary mt-3" onclick ="cargar_modal_rev_dos();" id="fin_rev_dos">Finalizar</button> `);
            }
        }

        if (salida.length != 0) {
            for (sal of salida) {
                vista_salida = `<b class='' style='color: black'>Su salida fue registrada en la siguiente fecha y hora</b><br><b class='' style='color: black'>${sal.fecha_registro}</b><br>`;
            }
        } else if (rev_salida.length == 0) {
            vista_salida = `<b class="" style="color: black">Para continuar con el proceso recuerde realizar los pasos anteriores</b><br>`;
        } else {
            vista_salida = `<b style="color: black">Al terminar su jornada laboral recuerde registrar su salida</b>
            <div class="row ">
              <div class="col-lg-12 ml-auto">
                <div class="">
                  <span class="btn btn-primary mt-3" onclick="cargar_modal_salida()">Registrar Salida</span>
                </div>
              </div>
            </div>`;
        }
 
        $('#contenedor_entrada').html(vista_entrada);
        $('#contenedor_salida').html(vista_salida);
        rotar(posicion);
    });
    
}

const guardar_revision = () => {
    let data = new FormData(document.getElementById("form_guardar_revision"));
    if (tomo_foto) {
        canvas = document.getElementById("foto_revision");
        let foto = canvas.toDataURL("image/jpeg");
        let info = foto.split(",", 2);
        data.append("foto_revision", info[1]);
        console.log(canvas.toDataURL("image/jpeg"));
    }
    enviar_formulario(`${RUTA}guardar_revision`, data, (resp) => {
        let { titulo, mensaje, tipo } = resp;
        if (mensaje == "sin_session") {
            close();
        } else if (tipo == 'success') {
            MensajeConClase(mensaje, tipo, titulo);
            $("#form_guardar_revision").get(0).reset();
            if (tiene_camara) {
                document.getElementById('foto_revision').width = document.getElementById('foto_revision').width;
            }
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
        tomo_foto = false;
    });
}

const cerrar_revisiones = (tipo_rev) => {
    let data = new FormData();
    data.append("tipo", tipo_rev);
    enviar_formulario(`${RUTA}guardar_entrada_salida`, data, (resp) => {
        let { titulo, mensaje, tipo, id_solicitud, id_persona, novedades, supervisor } = resp;
        if (tipo == 'success') {
            MensajeConClase(mensaje, tipo, titulo);
            if (novedades.length != 0) {
                enviar_correo_novedad(id_solicitud, novedades, supervisor);
            }
            obtener_estado_supervisor(id_persona);
            localStream.getTracks().forEach(track => track.stop());
        } else {
            MensajeConClase(mensaje, tipo, titulo);
        }
    });
}

const enviar_correo_novedad = (id_solicitud, novedades, supervisor) => {
    let filas_tabla_nov = ''
    novedades.map((novedad) => {
        novedad.evidencia = novedad.evidencia != null ? `<a href="${ADJSPATH}` + novedad.evidencia + `.png"><b>Ver evidencia</b></a>` : 'sin evidencia';
        filas_tabla_nov = filas_tabla_nov +
            `<tr>
                <td>${novedad.sala}</td>
                <td>${novedad.descripcion}</td>
                <td>${novedad.evidencia}</td>
            </tr> `;
    })
    const ser = `<a href="${server}index.php/supervisor/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
    let tabla_novedades = `
            <table>
                <thead style="font-weight: bold;">
                    <tr>
                    <td>Sala</td>
                    <td>Novedad</td>
                    <td>Evidencia</td>
                    </tr>
                </thead>
                <tbody>${filas_tabla_nov}</tbody>
            </table>`
    let mensaje = `Se le notifica que el supervisor de sala ${supervisor},  al realizar la revisión de sus respectivas salas registro las siguientes novedades:
        <br><br>${tabla_novedades}
        <br>Mas información en : ${ser}`;
    enviar_correo_personalizado("comp", mensaje, 'acamargo@cuc.edu.co',  'ANIBAL CAMARGO GARCIA', "AGIL Supervisor de Salas CUC", "Novedades de las Salas",'ParCodAdm', 1);
    enviar_correo_personalizado("comp", mensaje, 'npena7@cuc.edu.co', 'NEYLA PEÑA MONCADA', "AGIL Supervisor de Salas CUC", "Novedades de las Salas", 'ParCodAdm', 1);
}

    const filtrar_supervisor = () => {
        let fecha_registro= $("#fecha_registro").val();
        $("#tabla_estado_supervisor tbody").off("click", "tr .detalle");
    consulta_ajax(`${RUTA}filtrar_supervisor`, {fecha_registro}, data => {
        const myTable = $("#tabla_estado_supervisor").DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data,
            columns: [
                { data: 'ver' },
                { data: 'nombre' },
                { data: 'entrada' },
                { data: 'salida' },
                { data: 'estado' },
                { data: 'accion' },
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
            "order": [[ 4, "asc" ]]
        });
        $(`#tabla_estado_supervisor tbody`).on("click", "tr .detalle", function () {
            let data = myTable.row($(this).parent().parent()).data();
            console.log(data.id_solicitud,data.id_persona);
            mostrar_detalles(data.id_solicitud,data.id_persona,fecha_registro);
        });
    });
    }
