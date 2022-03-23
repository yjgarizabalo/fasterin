<?php 
$super_admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_index" ? true :false;
$per_index =  $_SESSION["perfil"] == "Per_index" ? true :false;
?>

<div class="modal fade" id="modal_notificaciones" role="dialog">
  <div class="modal-dialog" >
      <!-- Modal content-->
      <div class="modal-content" >
          <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
          </div>
          <div class="modal-body" id="bodymodal" >
              <div id="panel_notificaciones_comite" style="width: 100%" class="list-group">
              </div>
              <div id="panel_notificaciones_generales" style="width: 100%" class="list-group">
              </div>
          </div>
          <div class="modal-footer" id="footermodal">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
      </div>
  </div>
</div>

<div class="container col-md-12 " id="inicio-user">
    <div class="tablausu col-md-12 text-left" id="container_comite">
      <div class="table-responsive">
      <p class="titulo_menu pointer" id='inicio_return'><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0"
              width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="3" class="nombre_tabla">TABLA COMITÉ</td>
                  <td colspan="5"class="sin-borde text-right border-left-none"> <span class="btn btn-default" id="btn_notificaciones"><span class="n_notificaciones red"></span> Notificaciones</span> <?php if($super_admin){?><!--span class="black-color pointer btn btn-default" id="btn_administrar" ><span class="fa fa-cog red" ></span> Administrar</!--span> <span data-toggle="modal" data-target="#modal_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span>Nuevo</span--> <?php }?> <span class="black-color pointer btn btn-default" id="limpiar_filtros_comite" ><span class="fa fa-refresh red" ></span> Limpiar</span> </td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver</td>
                  <td>Nombre</td>
                  <td>Fecha_Cierre</td>
                  <td>Descripción</td>
                  <td>#Proyectos</td>
                  <td>Creado Por</td>
                  <td>Estado</td>
                  <td class="opciones_tbl_btn">Acción</td>
              </thead>
              <tbody>
              </tbody>
          </table>
      </div>
    </div>
  </div>
<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
          <div id='container_tabla_proyectos' class='table-responsive'>
              <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_proyectos_comite"
                cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="12" class="nombre_tabla">TABLA PROYECTOS</td>
                      <td class="sin-borde text-right border-left-none" colspan="4" >
                      <span class="btn btn-default" data-toggle="modal" data-target="#modal_crear_filtros"> <span class="fa fa-filter red"></span> Filtrar</span>
                      <span class="btn btn-default" id="limpiar_filtros"> <span class="fa fa-refresh red" ></span> Limpiar</span></td>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td style= "width:300px">Nombre</td>
                    <td>Investigador</td>
                    <td>Grupo</td>
                    <td>Tipo Proyecto</td>
                    <td>Tipo Recurso</td>
                    <td>Departamento</td>
                    <td>Programa</td>
                    <td>$Efectivo</td>
                    <td>$Especie</td>
                    <td>$Total</td>
                    <td>#Aprobados</td>
                    <td>#Negados</td>
                    <td>Estado</td>
                    <td style= "width:150px">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
          </div>
          <!--<div id='container_comentarios'>
            <div  style="width: 100%" class="list-group margin1 text-left" id='panel_comentarios_presupuesto'></div>  
            <form action="" id='form_guardar_comentario'>   
              <div class="input-group col-md-6">
                <input type="text" class="form-control" placeholder="Nuevo Comentario" name='comentario'>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit">Enviar!</button>
                </span>
              </div
            </form>
          </div>-->
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_crear_filtros" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <div class="agro agrupado" id="departamento_filtro">
            <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_departamento_filtro'>
                <span class="input-group-addon pointer" id='btn_buscar_departamento_filtro' style='	background-color:white'><span class='fa fa-search red'></span> Departamento</span>
            </div>
          </div>
          <select id="id_programa" id="programas" required class="form-control cbxprograma"><option value="">Seleccione Programa</option></select> 
          <select id="nombre_grupo" required class="form-control cbx_grupo"></select> 
          <select id="tipo_proyecto" required class="form-control cbx_tipo_proyecto"></select> 
          <select id="tipo_recurso" required class="form-control cbx_tipo_recurso"></select> 
          <select id="estado_proyecto" required class="form-control cbx_estado_proyecto"></select> 
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btn_filtrar"><span
            class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_proyecto" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Proyecto</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_proyectos_detalle" style="display: flex;">
          <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer"><a id="btn_descargar_proyecto" target="_blank"><span class="fa fa-cloud-download red"></span> Descargar</a></li>
                <li class="pointer active"><a><span class="fa fa-list red"></span> Principal</a></li>
                <li class="pointer informacion_a_cambiar proyecto_participantes" id="ver_detalle_participantes"><a><span class="fa fa-users red"></span> Participantes</a></li>
                <li class="pointer informacion_a_cambiar proyecto_lugares" id="ver_detalle_lugares"><a><span class="fa fa-map-marker red"></span> Lugares</a></li>
                <li class="pointer informacion_a_cambiar proyecto_instituciones" id="ver_detalle_instituciones"><a><span class="fa fa-university red"></span> Instituciones</a></li>
                <li class="pointer informacion_a_cambiar proyecto_programas" id="ver_detalle_programas"><a><span class="fa fa-university red"></span> Programas</a></li>
                <li class="pointer informacion_a_cambiar proyecto_asignaturas" id="ver_detalle_asignaturas"><a><span class="fa fa-university red"></span> Asignaturas/Proyectos</a></li>
                <li class="pointer informacion_a_cambiar proyecto_sublineas" id="ver_detalle_sublineas"><a><span class="fa fa-file-text-o red"></span> Líneas y Sublíneas</a></li>
                <li class="pointer informacion_a_cambiar proyecto_ods" id="ver_detalle_ods"><a><span class="fa fa-leaf red"></span> ODS</a></li>
                <li class="pointer informacion_a_cambiar proyecto_objetivos" id="ver_detalle_objetivos"><a><span class="glyphicon glyphicon-th-list red"></span> Objetivos</a></li>
                <li class="pointer informacion_a_cambiar proyecto_impactos" id="ver_detalle_impactos"><a><span class="fa fa-line-chart red"></span> Impactos</a></li>
                <li class="pointer informacion_a_cambiar proyecto_productos" id="ver_detalle_productos"><a><span class="glyphicon glyphicon-briefcase red"></span> Productos</a></li>
                <li class="pointer informacion_a_cambiar proyecto_cronograma" id="ver_detalle_cronogramas"><a><span class="fa fa-bar-chart red"></span> Cronograma</a></li>
                <li class="pointer informacion_a_cambiar proyecto_presupuestos" id="ver_detalle_presupuestos"><a><span class="fa fa-usd red"></span> Presupuestos</a></li>
                <li class="pointer informacion_a_cambiar proyecto_soportes" id="ver_detalle_soportes"><a><span class="fa fa-cloud-upload red"></span> Soportes</a></li>
                <li class="pointer informacion_a_cambiar proyecto_bibliografias" id="ver_detalle_bibliografias"><a><span class="glyphicon glyphicon-bookmark red"></span> Bibliografías</a></li>
              </ul>
          </div>
        </nav>
        <div class="table-responsive" >
          <table class="table table-responsive table-condensed table-bordered">
              <tr>
                <th class="nombre_tabla" colspan="6">Información del Proyecto</th>
              </tr>
              <tr>
                <td colspan="3" class="ttitulo">Nombre</td>
                <td colspan="3" id="detalle_nombre_proyecto"></td>
              </tr>
              <tr class="proyectos_antiguos"> 
                <td class="ttitulo" colspan="3">Departamento</td>
                <td class="nombre_departamento" colspan="3"></td> 
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Programa</td>
                <td class="nombre_programa" colspan="3"></td> 
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Nombre del grupo</td>
                <td class="nombre_grupo" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Tipo de proyecto</td>
                <td class="tipo_proyecto" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Tipo de recurso</td>
                <td class="tipo_recurso" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Investigador</td>
                <td class="investigador" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Moneda</td>
                <td class="tipo_efectivo" colspan="1"></td>
                <td class="ttitulo" colspan="1">Efectivo</td>
                <td class="efectivo" colspan="1"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Moneda</td>
                <td class="tipo_especie" colspan="1"></td>
                <td class="ttitulo" colspan="1">Especie</td>
                <td class="especie" colspan="1"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Moneda</td>
                <td class="tipo_externo" colspan="1"></td>
                <td class="ttitulo" colspan="1">Externo</td>
                <td class="externo" colspan="1"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Total</td>
                <td class="total" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Estado</td>
                <td class="estado_proyecto" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Observaciones</td>
                <td class="observaciones" colspan="3"></td>
              </tr>
              <tr class="proyectos_antiguos">
                <td class="ttitulo" colspan="3">Adjunto</td>
                <td class="adjunto" colspan="3"></td>
              </tr>
              <tr class="proyectos_nuevos">
                <td colspan="3" class="ttitulo">Tipo de Proyecto</td>
                <td colspan="3" id="detalle_tipo_proyecto"></td>
              </tr>
              <tr class="proyectos_nuevos">
                <td colspan="3" class="ttitulo">Fecha Inicial</td>
                <td colspan="1" id="detalle_fecha_inicial"></td>
                <td colspan="1" class="ttitulo">Fecha Final</td>
                <td colspan="1" id="detalle_fecha_final"></td>
              </tr>
              <tr class="proyectos_nuevos">
                <td colspan="3" class="ttitulo">N° Beneficiarios</td>
                <td colspan="1" id="detalle_no_beneficiarios"></td>
                <td colspan="1" class="ttitulo">Estado del Proyecto</td>
                <td colspan="1" id="detalle_estado_proyecto"></td>
              </tr>
              <tr class="informacion_a_cambiar algunas_preguntas proyectos_nuevos">
                <td colspan="3" class="ttitulo">¿Operacionaliza?</td>
                <td colspan="1" id="detalle_operacionaliza"></td>
                <td colspan="1" class="ttitulo">Código del convenio</td>
                <td colspan="1" id="detalle_codigo_convenio"></td>
              </tr>
              <tr class="informacion_a_cambiar algunas_preguntas proyectos_nuevos">
                <td colspan="3" class="ttitulo">¿Tiene proceedings?</td>
                <td colspan="1" id="detalle_proceedings"></td>
                <td colspan="1" class="ttitulo">Verificado por</td>
                <td colspan="1" id="detalle_verificado_por"></td>
              </tr>
              <tr class="informacion_a_cambiar algunas_preguntas proyectos_nuevos">
                <td colspan="3" class="ttitulo">Código SAP</td>
                <td colspan="1" id="detalle_codigo_sap"></td>
                <td colspan="1" class="ttitulo">Descripción</td>
                <td colspan="1" id="detalle_descripcion_codigo_sap"></td>
              </tr>
              <tr class="informacion_a_cambiar algunas_preguntas proyectos_nuevos">
                <td colspan="3" class="ttitulo">Centro de Costo</td>
                <td colspan="1" id="detalle_centro_costo"></td>
                <td colspan="1" class="ttitulo">Departamento</td>
                <td colspan="1" id="detalle_departamento_centro_costo"></td>
              </tr>
              <tr class="nombre_tabla text-left proyectos_nuevos resumen informacion_a_cambiar">
                <td colspan="6">RESUMEN</td>
              </tr>
              <tr class="proyectos_nuevos resumen informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_resumen" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left proyectos_nuevos justificacion informacion_a_cambiar">
                <td colspan="6">Justificación</td>
              </tr>
              <tr class="proyectos_nuevos justificacion informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_justificacion" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left detalle_planteamiento_problema proyectos_nuevos planteamiento_problema informacion_a_cambiar">
                <td colspan="6">PLANTEAMIENTO DEL PROBLEMA</td>
              </tr>
              <tr class="detalle_planteamiento_problema proyectos_nuevos planteamiento_problema informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_planteamiento_problema" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left detalle_marco_teorico proyectos_nuevos marco_teorico informacion_a_cambiar">
                <td colspan="6">MARCO TEÓRICO</td>
              </tr>
              <tr class="detalle_marco_teorico proyectos_nuevos marco_teorico informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_marco_teorico" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left detalle_estado_arte proyectos_nuevos estado_arte informacion_a_cambiar">
                <td colspan="6">ESTADO DEL ARTE</td>
              </tr>
              <tr class="detalle_estado_arte proyectos_nuevos estado_arte informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_estado_arte" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left detalle detalle_diseno_metodologico proyectos_nuevos diseno_metodologico informacion_a_cambiar">
                <td colspan="6">DISEÑO METODOLÓGICO</td>
              </tr>
              <tr class="detalle detalle_diseno_metodologico proyectos_nuevos diseno_metodologico informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_diseno_metodologico" style="word-break: break-word;"></td>
              </tr>
              <tr class="nombre_tabla text-left proyectos_nuevos resultados_esperados informacion_a_cambiar">
                <td colspan="6">RESULTADOS ESPERADOS</td>
              </tr>
              <tr class="proyectos_nuevos resultados_esperados informacion_a_cambiar">
                <td class="text-left" colspan="6" id="detalle_resultados_esperados" style="word-break: break-word;"></td>
              </tr>
              <tr id="aprobados_negados_proyecto">
                <td colspan="3" class="ttitulo">#Aprobados</td>
                <td colspan="1" class="num_aprobados"></td>
                <td colspan="1" class="ttitulo">#Negados</td>
                <td colspan="1" class="num_negados"></td>
              </tr>
          </table>

          <div id="container_tabla_estados_proyectos">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estados_proyecto" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th colspan="4" class="nombre_tabla">TABLA GESTIÓN</th>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Acción</td>
                    <td>Fecha</td>
                    <td>Observaciones</td>
                    <td>Persona</td>
                </thead>
                <tbody>
                </tbody>
              </table>
          </div>

            <div id='container_comentarios_generales'>
              <div  style="width: 100%" class="list-group margin1 text-left" id='panel_comentarios_generales'></div>  
              <form action="" id='form_guardar_comentario_general'>   
                <div class="input-group col-md-6">
                  <input type="text" class="form-control comentarios" placeholder="Nuevo Comentario" name='comentario'>
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Enviar!</button>
                  </span>
                </div><!-- /input-group -->
              </form>
            </div>

        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_participantes" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-users"></span> Participantes</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_participantes" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="5" class="nombre_tabla">TABLA PARTICIPANTES</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 5%;">Ver</td>
                <td title="Nombre completo del participante">Nombre Completo</td>
                <td title="Tipo de participante en el proyecto">Tipo Participante</td>
                <td title="Institución en donde se encuentra el participante">Institución</td>
                <td style="width:150px">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver_participante" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 80%">
          <div id="datos_perso" class="">
            <table class="table" id=tabla_detalle_participante>
              <tr class="nombre_tabla">
                <td colspan="2">Datos</td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre Completo</td>
                <td id="participante_nombre_completo"></td>
              </tr>
              <tr>
                <td class="ttitulo">Identificación</td>
                <td id="participante_identificacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Institución</td>
                <td id="participante_institucion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Departamento</td>
                <td id="participante_departamento"></td>
              </tr>
              <tr>
                <td class="ttitulo">Programa</td>
                <td id="participante_programa"></td>
              </tr>
              <tr>
                <td class="ttitulo">Escalafón</td>
                <td id="participante_escalafon"></td>
              </tr>
              <tr>
                <td class="ttitulo">Grupo</td>
                <td id="participante_grupo"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_lugares" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-map-marker"></span> Lugares</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_lugares" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="3" class="nombre_tabla">TABLA LUGARES</td>
              </tr>
              <tr class="filaprincipal">
                <td title="País">País</td>
                <td title="Ciudad">Ciudad</td>
                <td style="width:20%;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_institucion" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-university"></span> Instituciones</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_instituciones" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="6" class="nombre_tabla">TABLA INSTITUCIONES</td>
              </tr>
              <tr class="filaprincipal">
                <td title="Ver">Ver</td>
                <td title="Nombre de la institución">Institcución</td>
                <td title="Persona de contacto">Persona de contacto</td>
                <td title="Correo de la persona de contacto">Correo</td>
                <td title="Teléfonos de la persona de contacto">Teléfonos</td>
                <td style="width:20%;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver_institucion" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-university"></span> Institución</h3>
      </div>
      <div class="modal-body">
        <div class="table table-responsive">
          <table class="table">
            <tr class="nombre_tabla text-left">
              <td colspan="6">INFORMACIÓN INSTITUCIÓN</td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Nombre de la Institcución</td>
              <td colspan="4" id="detalle_nombre_institucion"></td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Persona contacto</td>
              <td colspan="4" id="detalle_persona_contacto_institucion"></td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Correo</td>
              <td colspan="4" id="detalle_correo_institucion"></td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Teléfonos</td>
              <td colspan="4" id="detalle_telefonos_institucion"></td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Responsabilidad contraparte</td>
              <td colspan="4" id="detalle_responsabilidad_contraparte_institucion"></td>
            </tr>
            <tr>
              <td colspan="2" class="ttitulo">Responsabilidad CUC</td>
              <td colspan="4" id="detalle_responsabilidad_cuc_institucion"></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_programas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-university"></span> Programas</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_programas" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="2" class="nombre_tabla">TABLA PROGRAMAS</td>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_programas" id="btn_agregar_programa" title="Agregar programa"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td title="Programa de pregrado o posgrado">Programa</td>
                <td title="Tipo de Interacción">Tipo de Interacción</td>
                <td style="width:20%;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_asignaturas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-university"></span> Asignaturas</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_asignaturas" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="2" class="nombre_tabla">TABLA ASIGNATURAS/PROYECTOS</td>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_asignaturas" id="btn_agregar_asignatura" title="Agregar asignatura/proyecto"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td title="Ver">Ver</td>
                <td title="Asignatura/Proyecto">Asignatura/Proyecto</td>
                <td style="width:20%;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_sublineas_investigacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Sublíneas de Investigación</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_sublineas_investigacion" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="4" class="nombre_tabla">TABLA SUBLÍNEAS DE INVESTIGACIÓN</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 30%;" title="Grupo de la sublínea de investigación">Grupo</td>
                <td style="width: 30%;" title="Línea de la sublínea de investigación">Línea</td>
                <td style="width: 30%;" title="Sublínea de investigación">Sublínea</td>
                <td style="width:10%">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ods" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-leaf"></span> Objetivos de Desarrollo Sostenible</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_ods" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="2" class="nombre_tabla">TABLA ODS</td>
              </tr>
              <tr class="filaprincipal">
                <td title="ODS (Objetivo de Desarrollo Sostenible)">ODS</td>
                <td >Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_objetivos" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span> Objetivos</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_objetivos" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="4" class="nombre_tabla">TABLA OBJETIVOS</td>
              </tr>
              <tr class="filaprincipal">
                <td>Ver</td>
                <td title="Tipo de Objetivo">Tipo Objetivo</td>
                <td title="Descripción del Objetivo">Descripción</td>
                <td style="width: 150px;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_impactos" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-line-chart"></span> Impactos y/o Efectos Esperados</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_impactos" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="3" class="nombre_tabla">TABLA IMPACTOS Y/O EFECTOS ESPERADOS</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width:25%;">Ver</td>
                <td style="width: 50%;" title="Tipo de Objetivo">Tipo de Impacto</td>
                <td class="" style="width:25%;">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_productos" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-briefcase"></span> Productos Esperados</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_productos" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="4" class="nombre_tabla">TABLA PRODUCTOS ESPERADOS</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 10%">Ver</td>
                <td style="width: 40%" title="Tipo de producto esperado">Tipo de Producto</td>
                <td style="width: 40%" title="Producto Esperado">Producto</td>
                <td class="" style="width:10%">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver_producto" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-briefcase"></span> Producto Esperado</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_responsables_producto" width="100%">
                <thead class="ttitulo">
                  <tr class="">
                    <td colspan="2" class="nombre_tabla">RESPONSABLES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td title="Identificación del Responsable">Identificación</td>
                    <td title="Nombre completo del Responsable">Nombre completo</td>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-12" style="margin-top: 20px">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_informacion_producto" width="100%">
                <tr class="nombre_tabla text-left">
                  <td colspan="2">INFORMACIÓN DEL PRODUCTO</td>
                </tr>
                <tr>
                  <td colspan="1" style="width:40%" class="ttitulo" title="Tipo de Producto">Tipo de Producto</td>
                  <td colspan="1" style="width:60%" id="ver_tipo_producto"></td>
                </tr>
                <tr>
                  <td colspan="1" style="width:40%" class="ttitulo" title="Producto">Producto</td>
                  <td colspan="1" style="width:60%" id="ver_producto"></td>
                </tr>
                <tr>
                  <td colspan="2" class="ttitulo" title="Observaciones">Observaciones</td>
                </tr>
                <tr>
                  <td colspan="2" id="ver_observaciones"></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_cronograma" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bar-chart"></span> Cronograma Plan de Trabajo</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_cronogramas" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="6" class="nombre_tabla">TABLA CRONOGRAMA</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width:10%">Ver</td>
                <td style="width:20%" title="Objetivo Específico de la actividad">Obj. Específico</td>
                <td style="width:30%" title="Actividad">Actividad</td>
                <td style="width:15%" title="Fecha inicial de la actividad">Fecha Inicial</td>
                <td style="width:15%" title="Fecha final de la actividad">Fecha Final</td>
                <td class="" style="width:10%">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver_cronograma" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bar-chart"></span> Cronograma</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_responsables_cronograma" width="100%">
                <thead class="ttitulo">
                  <tr class="">
                    <td colspan="2" class="nombre_tabla">RESPONSABLES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td title="Identificación del Responsable">Identificación</td>
                    <td title="Nombre completo del Responsable">Nombre completo</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-12" style="margin-top: 20px">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_informacion_cronograma" width="100%">
                <tr class="nombre_tabla text-left">
                  <td colspan="4">INFORMACIÓN DEL CRONOGRAMA</td>
                </tr>
                <tr>
                  <td colspan="1" class="ttitulo" title="Objetivo Específico">Obj. Específico</td>
                  <td colspan="3" id="ver_objetivo_especifico"></td>
                </tr>
                <tr>
                  <td colspan="1" class="ttitulo" title="Fecha Inicial">Fecha Inicial</td>
                  <td colspan="1" id="ver_fecha_inicial"></td>
                  <td colspan="1" class="ttitulo" title="Fecha Final">Fecha Final</td>
                  <td colspan="1" id="ver_fecha_final"></td>
                </tr>
                <tr>
                  <td colspan="4" class="ttitulo" title="Actividad">Actividad</td>
                </tr>
                <tr>
                  <td colspan="4" id="ver_actividad"></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_resumen_presupuesto" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-usd"></span> Presupuesto</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_resumen_presupuestos" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA PRESUPUESTOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td style="width: 10%">Ver</td>
                  <td style="width: 35%" title="Rubro">Rubro</td>
                  <td style="width: 40%" title="Efectivo">Efectivo</td>
                  <td style="width: 40%" title="Especie">Especie</td>
                  <td style="width: 15%">Acciones</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="ttitulo text-right">Total</td>
                  <td></td>
                  <td></td>
                  <td style="text-align: center;"></td>
                </tr>
                <tr>
                  <td colspan="2" class="ttitulo text-right">Total Presupuesto</td>
                  <td colspan="3" id="total_presupuesto"></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="col-md-12 table-responsive internacionalizacion">
            <table class="table table-bordered table-hover table-condensed" id="tabla_presupuesto_financiacion_nacional" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA DE FINANCIACIÓN DISCRIMINADO (NACIONAL)</td>
                </tr>
                <tr class="filaprincipal">
                  <td title="Nombre Institución">FINANCIACIÓN</td>
                  <td title="Total Efectivo">Total Efectivo</td>
                  <td title="Total Especie">Total Especie</td>
                  <td title="Total">Total</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td class="ttitulo text-center">Total Recursos</td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="col-md-12 table-responsive internacionalizacion">
            <table class="table table-bordered table-hover table-condensed" id="tabla_presupuesto_financiacion_internacional" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA DE FINANCIACIÓN DISCRIMINADO (INTERNACIONAL)</td>
                </tr>
                <tr class="filaprincipal">
                  <td title="Nombre Institución">FINANCIACIÓN</td>
                  <td title="Total Efectivo">Total Efectivo</td>
                  <td title="Total Especie">Total Especie</td>
                  <td title="Total">Total</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td class="ttitulo text-center">Total Recursos</td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_presupuesto_discriminado_entidad" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA INVERSIÓN TOTAL DISCRIMINADO POR INSTITUCIÓN</td>
                </tr>
                <tr class="filaprincipal">
                  <td title="Nombre Institución">Nombre Institución</td>
                  <td title="Total Efectivo">Total Efectivo</td>
                  <td title="Total Especie">Total Especie</td>
                  <td title="Total">Total</td>
                  <td title="Porcentaje">Porcentaje</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td class="ttitulo text-center">Total</td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center">100 %</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_presupuesto_discriminado_entidad_rubro" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA INVERSIÓN TOTAL DISCRIMINADO POR INSTITUCIÓN Y RUBRO</td>
                </tr>
                <tr class="filaprincipal">
                  <td title="Nombre Institución">Nombre Institución</td>
                  <td title="Rubro">Rubro</td>
                  <td title="Total Efectivo">Total Efectivo</td>
                  <td title="Total Especie">Total Especie</td>
                  <td title="Total">Total</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="ttitulo text-center">Total</td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_presupuesto" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-usd"></span> Presupuesto <span id="titulo_tipo_presupuesto"></span></h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_presupuestos" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA PRESUPUESTOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td style="width: 10%">Ver</td>
                  <td style="width: 35%" title="Tipo Valor">Tipo Valor</td>
                  <td style="width: 35%" title="Valor Unitario">Valor Unitario</td>
                  <td style="width: 35%" title="Valor Total">Valor Total</td>
                  <td style="width: 15%">Acciones</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="ttitulo text-center">Total</td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver_presupuesto" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-usd"></span> Ver Presupuesto</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 table-responsive">
            <table class="table" cellspacing="0" width="100%">
              <tr class="nombre_tabla text-left">
                <td colspan="6">INFORMACIÓN GENERAL</td>
              </tr>
              <tr>
                <td colspan="2" class="ttitulo">Tipo de Presupuesto</td>
                <td colspan="4" id="detalle_tipo_presupuesto"></td>
              </tr>
              <tbody id="datos_presupuestos">
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_agregar_soportes" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" ><span class="fa fa-cloud-upload"></span> Soportes</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-bordered table-hover table-condensed" id="tabla_soportes"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="5" class="nombre_tabla">TABLA DE SOPORTES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Nombre</td>
                    <td>Fecha Adjunto</td>
                    <td>Nombre usuario</td>
                    <td>Acciones</td>
                  </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_bibliografia" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-bookmark"></span> Bibliografía</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_bibliografia" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="3" class="nombre_tabla">TABLA BIBLIOGRAFÍA</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 15%">Ver</td>
                <td style="width: 70%" title="Bibliografía">Bibliografía</td>
                <td style="width: 15%">Acciones</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_ver" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 id="ver_titulo" class="modal-title"></h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div id="ver_body" class="col-xs-12" style="break-word: word-break;">
            <p id="ver_descripcion"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<form  id="form_buscar_departamento"  method="post">
  <div class="modal fade" id="modal_buscar_departamento" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Departamento</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_dato_departamento' class="form-control" placeholder="Ingrese departamento">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_buscar_departamento" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DEPARTAMENTOS</td>
                  </tr>
                  <tr class="filaprincipal">
                      <td>Ver</td>
                      <td>Nombre</td>
                      <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script>
    $(document).ready(function () {
        listar_comites();
        inactivityTime();
        mostrar_notificaciones_comite(2);
        Cargar_parametro_buscado(78, ".cbx_grupo", "Seleccione Nombre del Grupo");
        Cargar_parametro_buscado(77, ".cbx_tipo_recurso", "Seleccione Tipo de Recurso");
        Cargar_parametro_buscado_aux(79, '.cbx_estado_proyecto', 'Seleccione un Estado de Proyecto');

        <?php if($_SESSION['perfil'] != 'Per_index'){?>
          Cargar_parametro_buscado(76, ".cbx_tipo_proyecto", "Seleccione Tipo de Proyecto");
        <?php }else{?>
            listar_proyectos_persona_combo('<?php echo $_SESSION["persona"]?>');
        <?php } ?>
    });
</script>
<script type="text/javascript">
$(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    startDate: new Date(),
    todayBtn: true,
    maxView: 4,
    minView: 2
});
</script>
