<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">

<?php
$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Proy" || $_SESSION["perfil"] == "Per_Adm_index" ? true : false;
?>

<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo $administra || $id > 0 ? '' : 'oculto'; ?>" id="container_proyectos">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed" id="tabla_proyectos" cellspacing="0" width="100%">
        <thead class="ttitulo">
          <tr class="">
            <td colspan="2" class="nombre_tabla">TABLA PROYECTOS INDEX<br>
                <span class="mensaje-filtro oculto"><span class="tiembla fa fa-bell red" style="animation: tiembla 1.5s infinite;"></span> La tabla tiene algunos filtros aplicados.</span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="4">
              <span class="btn btn-default" id="btn_notificaciones_proyectos"><span id="proyectos_notificaciones" class="badge btn-danger">0</span> Notificaciones</span>
              <?php if ($administra) echo '<span class="btn btn-default btnAgregar" id="btn_administrar"><span class="fa fa-cogs red"></span> Administrar</span>'; ?>
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_crear_filtros_proyectos"> <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="btn_limpiar_filtros"><span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td>Ver</td>
            <td width="45%">Nombre</td>
            <td>Investigador Principal</td>
            <td>Tipo</td>
            <td>Estado</td>
            <td>Acciones</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="modal_notificaciones_proyectos" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones Proyectos Index</h3>
            </div>
            <div class="modal-body" id="notificaciones_body" >
                <div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
                <?php if($administra) { ?><div id="panel_notificaciones_solicitudes" style="width: 100%" class="list-group"></div><?php } ?>
                <div id="panel_notificaciones_solicitudes_respuestas" style="width: 100%" class="list-group"></div>
                <div id="panel_notificaciones_comite" style="width: 100%" class="list-group"></div>
                <?php if($administra) { ?><div id="panel_notificaciones_generales" style="width: 100%" class="list-group text-left"></div><?php } ?>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
  </div>

  <div class="modal fade" id="modal_crear_filtros_proyectos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-filter"></span> Filtros</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="row">
              <div class="col-md-8 col-md-offset-2">
                <input type="text" name="codigo_proyecto" id="codigo_proyecto_filtro" placeholder="Código del proyecto" class="form-control">
                <select id="tipo_proyecto_filtro" class="form-control cbx_tipo_proyecto"></select>
                <select id="estado_proyecto_filtro" class="form-control cbx_estado_proyecto"></select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" id="btn_filtrar_proyectos"><span class="glyphicon glyphicon-ok"></span> Generar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_solicitudes_modificar" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Solicitud para modificar el proyecto</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group agrupado col-md-8 text-left">
            </div>
            <div class="table-responsive col-md-12">
              <table class="table table-bordered table-hover table-condensed" id="tabla_items_motivos" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr class="">
                    <td colspan="3" class="nombre_tabla">TABLA MOTIVOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td title="Ver Motivo" style="width: 10%;">Ver</td>
                    <td title="Nombre Ítem">Nombre</td>
                    <td>Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button id="btn_solicitud_proyecto" type="button" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_detalle_motivo" role="dialog">
    <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-eye"></span> Detalle Motivo</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 table-responsive">
              <table class="table" cellspacing="0" width="100%">
                <tr class="nombre_tabla text-left">
                  <td colspan="6">INFORMACIÓN GENERAL</td>
                </tr>
                <tr>
                  <td colspan="2" class="ttitulo">Ítem</td>
                  <td colspan="4" id="detalle_item"></td>
                </tr>
                <tbody id="datos_motivo">
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

  <?php if($administra){?>
  <div class="modal fade" id="modal_administrar_index" role="dialog">
    <div class="modal-dialog modal-lg modal-95">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
        </div>
        <div class="modal-body">
            <nav class="navbar navbar-default" id="nav_admin_index">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer active" id="admin_aprobados_persona"><a><span class="fa fa-users red"></span> Aprobados</a></li>
                <li class="pointer" id="admin_proyectos_persona"><a><span class="fa fa-folder red"></span> Permisos</a></li>
                <li class="pointer" id="admin_proyectos_parametros"><a><span class="fa fa-folder red"></span> Parámetros Generales</a></li>
                <li class="pointer" id="admin_proyectos_instituciones"><a><span class="fa fa-university red"></span> Instituciones</a></li>
                <li class="pointer" id="admin_proyectos_comite"><a><span class="fa fa-folder red"></span> Comité</a></li>
              </ul>
            </div>
          </nav>
          <div class="table-responsive">
            <div id="container_admin_index" class="">
              <div class="form-group agrupado col-md-8 text-left" style='padding:0px'>
                <div class="input-group">
                <select  required class="form-control" id='cbx_personas_aprueban'><option value="">Seleccione Persona</option></select> 
                  <span class="input-group-btn" id='btn_asignar_persona_aprueba'><button class="btn btn-default" type="button"><span class='fa fa-search red'></span> Asignar</button></span>
                </div>
              </div>
              <table class="table table-bordered table-hover table-condensed" id="tabla_personas_aprueban_index" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <td colspan='4' class="nombre_tabla">TABLA PERSONAS APRUEBAN</td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>Nombre</td>
                    <td>Identificación</td>
                    <td>Fecha Registra</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
  
            <div id="container_admin_proyectos_index" class="oculto">
              <div class='col-md-5' style='padding-left:0'>
                <div class="form-group">
									<div class="input-group agro">
										<input name="persona_solicitud_id" type="hidden" id="persona_solicitud_id">
										<span id="permiso_persona" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
										<span class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover" data-placement="bottom"><span class="glyphicon glyphicon-search"></span></span>
									</div>
								</div>
              </div>
              <table class="table table-bordered table-hover table-condensed" id="tabla_actividades" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <td colspan='4' class="nombre_tabla">TABLA TIPO DE PROYECTOS</td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>Nombre</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div id="container_admin_proyectos_parametros" class="oculto">
              <form id="form_parametros_generales">
                <div class="col-md-4 col-md-offset-4">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon">IVA</span>
                      <input id="iva" type="number" min="0" max="100" name="iva" class="form-control text-center" placeholder="Ejemplo: 19">
                      <span class="input-group-addon" title="Porcentaje del IVA"><strong>%</strong></span>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div id="container_admin_proyectos_instituciones" class="oculto">
              <table class="table table-bordered table-hover table-condensed" id="tabla_instituciones_bdd" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <td colspan="4" class="nombre_tabla">TABLA INSTITUCIONES</td>
                    <td colspan="3"class="sin-borde text-right border-left-none">
                      <span id="btn_agregar_institucion_bdd" class="btn btn-default"><span class="fa fa-plus red"></span> Nuevo</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Nombre</td>
                    <td>NIT</td>
                    <td>País de Origen</td>
                    <td>Correo</td>
                    <td>Teléfono de Contacto</td>
                    <td>Nombre de Contacto</td>
                    <td class="opciones_tbl_btn">Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div id="container_admin_proyectos_comite">
              <table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="3" class="nombre_tabla">TABLA COMITÉ</td>
                      <td colspan="5"class="sin-borde text-right border-left-none">
                        <span data-toggle="modal" data-target="#modal_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span> Nuevo</span>
                        <span class="black-color pointer btn btn-default" id="limpiar_filtros_comite" ><span class="fa fa-refresh red"></span> Limpiar</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">Ver</td>
                      <td>Nombre</td>
                      <td>Fecha de cierre</td>
                      <td>Descripción</td>
                      <td>#Proyectos</td>
                      <td>Creado Por</td>
                      <td>Estado</td>
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
          <button type="button" class="btn btn-danger active" id="btn_guardar_parametros_generales"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_agregar_institucion_bdd" role="dialog">
    <div class="modal-dialog modal-md">
      <form action="#" id="form_agregar_institucion_bdd" method="post" autocomplete="off">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title" id="titulo_institucion_bdd"><span class="fa fa-plus-circle"></span> Agregar Institución</h3>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12 col-md-6">
                <input type="text" name="nombre_institucion" id="nombre_institucion" class="form-control" placeholder="Nombre" required>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="nit_institucion" id="nit_institucion" class="form-control" placeholder="NIT" required>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="pais_origen_institucion" id="pais_origen_institucion" class="form-control" placeholder="País de Origen" required>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="email" name="correo_institucion_bdd" id="correo_institucion_bdd" class="form-control" placeholder="Correo" required>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="number" name="telefono_contacto_institucion" id="telefono_contacto_institucion" class="form-control" placeholder="Teléfono de Contacto" required>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="nombre_contacto_institucion" id="nombre_contacto_institucion" class="form-control" placeholder="Nombre de Contacto" required>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div id="modal_elegir_estado" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
        </div>
        <div class="modal-body">
          <table id="tabla_estados" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr><th colspan="3" class="nombre_tabla">TABLA ESTADOS</th></tr>
              <tr class="filaprincipal">
                <td>Parámetro</td>
                <td>Nombre</td>
                <td>Gestión</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_guardar_comite" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_guardar_comite" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-folder"></span> Nuevo Comité</h3>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-10 col-md-offset-1">
                <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
                <div class="input-group date form_datetime form_date agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                  <input class="form-control sin_focus" size="16" placeholder="Fecha Cierre" type="text" value="" required="true" name="fecha">
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_modificar_comite" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_modificar_comite" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Comité</h3>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-10 col-md-offset-1">
                <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
                <div class="input-group date form_datetime form_date agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                    data-link-field="dtp_input1">
                    <input class="form-control sin_focus" size="16" placeholder="Fecha Cierre" type="text" value="" required="true" name="fecha">
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>  Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
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
        <div class="modal-body">
            <div id='container_tabla_proyectos' class='table-responsive'>
                <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_proyectos_comite"
                  cellspacing="0" width="100%">
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
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 col-md-offset-2">
              <input type="text" id="codigo_proyecto_filtro_2" name="codigo_proyecto" class="form-control" placeholder="Código del proyecto">
              <div class="agro agrupado" id="departamento_filtro">
                <div class="input-group">
                    <input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_departamento_filtro'>
                    <span class="input-group-addon pointer" id='btn_buscar_departamento_filtro' style='	background-color:white'><span class='fa fa-search red'></span> Departamento</span>
                </div>
              </div>
              <select id="id_programa" id="programas" required class="form-control cbxprograma"><option value="">Seleccione Programa</option></select> 
              <select id="nombre_grupo" required class="form-control cbx_grupo_investigacion"></select> 
              <select id="tipo_proyecto" required class="form-control cbx_tipo_proyecto"></select> 
              <select id="tipo_recurso" required class="form-control cbx_tipo_recurso"></select> 
              <select id="estado_proyecto" required class="form-control cbx_estado_proyecto"></select> 
            </div>
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
  <?php }?>

  <div class="modal fade" id="modal_detalle_proyecto" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Proyecto</h3>
        </div>
        <div class="modal-body">
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
                  <li class="pointer" id="ver_detalle_correcciones"><a><span class="fa fa-cog red"></span> Correcciones</a></li>
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

  <div class="tablausu col-md-12 <?php echo $administra || $id > 0 ? 'oculto' : ''; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">

        <div id="btn_nuevo_proyecto">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn form-control btn-Efecto-men">Nuevo Proyecto</span>
            </div>
          </div>
        </div>

        <div id="listado_proyectos">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control btn-Efecto-men">Mis Proyectos</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pregunta_nuevo_proyecto" role="dialog">
  <div class="modal-dialog modal-lg">
    <form action="#" id="agregar_bibliografia" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus-circle"></span> Nuevo Proyecto</h3>
        </div>
        <div class="modal-body">
          <div id="seleccion_tipo">
            <div class="row" style="width: 100%;">
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_investigacion">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/investigacion.png" alt="...">
                    <span class = "btn form-control">Investigación</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_bienestar_universitario">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/bienestar universitario.png" alt="...">
                    <span class = "btn form-control">Bienestar Universitario</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_extension">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/extension.png" alt="...">
                    <span class = "btn form-control">Extensión</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_docencia">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/docencia.png" alt="...">
                    <span class = "btn form-control">Docencia</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_laboratorios">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/laboratorios.png" alt="...">
                    <span class = "btn form-control">Laboratorios</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_gestion_universitaria">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/gestion universitaria.png" alt="...">
                    <span class = "btn form-control">Gestión Universitaria</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_internacionalizacion">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/internacionalizacion.png" alt="...">
                    <span class = "btn form-control">Internacionalización</span>
                  </div>
                </div>
              </div>
              <div class="col-6 col-sm-4 col-md-3 text-center pointer">
                <div class="thumbnail" id="btn_nuevo_proyecto_grado">
                  <div class="caption text-center">
                    <img src="<?php echo base_url() ?>/imagenes/internacionalizacion.png" alt="...">
                    <span class = "btn form-control">Grado</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" style="margin: 0 25px;">
              <div class="col-12" style="word-break: break-word; height: auto;">
                <p>
                  Recuerde que al crear un proyecto de cualquier índole, estará aceptando los términos y referencia del <a href="<?php echo base_url() ?>archivos_adjuntos/proyectos_index_creacion/formatos/ACUERDO CD No. 1389.pdf" target="_blank"> acuerdo N° 1389</a>.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <div class="btn-opciones-grid" style="width: 100%;">
            <div class="opcion_grid">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
            </div>
            <div class="opcion_grid">
              <button type="button" class="btn btn-danger active" data-dismiss="modal" id="btn_modificar_ultimo_proyecto"><span class="fa fa-pencil-square-o"></span> Editar mi último proyecto</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_menu_proyecto" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cog"></span> Proyecto de <span id="titulo_tipo_proyecto"></span></h3>
      </div>
      <div class="modal-body">
        <div id="seleccion_tipo">
          <div class="row" style="width: 100%;">
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_nombre_proyecto mod_fecha_inicial mod_fecha_final mod_tipo_recurso mod_no_beneficiarios mod_resumen mod_planteamiento_problema mod_marco_teorico mod_estado_arte mod_diseno_metodologico mod_resultados_esperados mod_tipo_movilidad mod_tipo_responsable mod_formacion_responsable mod_algunas_preguntas">
              <div class="thumbnail" id="btn_informacion_principal">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/informacion principal.png" alt="...">
                  <span class = "btn form-control">Información Principal</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_participantes" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_participantes">
              <div class="thumbnail" id="btn_participantes">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/participantes.png" alt="...">
                  <span class = "btn form-control">Participantes</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_lugares">
              <div class="thumbnail" id="btn_lugares">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/lugares.png" alt="...">
                  <span class = "btn form-control">Lugares</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_instituciones" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_instituciones">
              <div class="thumbnail" id="btn_instituciones">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/instituciones.png" alt="...">
                  <span class = "btn form-control">Instituciones</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_programas" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_programas">
              <div class="thumbnail" id="btn_programas">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/contract.png" alt="...">
                  <span class = "btn form-control">Programas</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_programas" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_asignaturas">
              <div class="thumbnail" id="btn_asignaturas">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/comunicaciones_eventos.png" alt="...">
                  <span class = "btn form-control">Asignaturas/Proyectos</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_sublinea" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_sublineas">
              <div class="thumbnail" id="btn_sublinea_investigacion">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/sublineas.png" alt="...">
                  <span class = "btn form-control">Líneas y Sublíneas</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_ods" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_ods">
              <div class="thumbnail" id="btn_ods">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/ods.png" alt="...">
                  <span class = "btn form-control">ODS</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_objetivos">
              <div class="thumbnail" id="btn_objetivos">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/objetivos.png" alt="...">
                  <span class = "btn form-control">Objetivos</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_impactos">
              <div class="thumbnail" id="btn_impactos">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/impactos.png" alt="...">
                  <span class = "btn form-control">Impactos</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_productos">
              <div class="thumbnail" id="btn_productos">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/productos.png" alt="...">
                  <span class = "btn form-control">Productos</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_cronograma">
              <div class="thumbnail" id="btn_cronograma">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/cronogramas.png" alt="...">
                  <span class = "btn form-control">Cronograma</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_presupuestos">
              <div class="thumbnail" id="btn_presupuesto">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/presupuestos.png" alt="...">
                  <span class = "btn form-control">Presupuestos</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_soportes" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_soportes">
              <div class="thumbnail" id="btn_soportes">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/soportes.png" alt="...">
                  <span class = "btn form-control">Soportes</span>
                </div>
              </div>
            </div>
            <div id="btn_menu_bibliografia" class="col-6 col-sm-4 col-md-3 text-center informacion_a_cambiar mod_proyecto_bibliografias">
              <div class="thumbnail" id="btn_bibliografia">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/bibliografias.png" alt="...">
                  <span class = "btn form-control">Bibliografía</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <div class="opcion_grid">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_editar_proyecto" role="dialog">
  <div class="modal-dialog modal-lg">
    <form action="#" id="form_guardar_proyecto" class="guardar_proyecto" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal">X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> Información Principal</h3>
        </div>
        <div class="modal-body" id="body-modal-editar-proyecto">
          <div class="alert alert-success" role="alert"><span id="mensaje_editar_proyecto"></span></div>
          <a id="btn_preguntas_convenio_proceedings" class="informacion_a_cambiar pointer mod_algunas_preguntas" style="float: right; margin: 5px 0;"><span class="fa fa-question-circle"></span> Algunas preguntas</a>
          <input type="text" id="nombre_proyecto"name="nombre_proyecto" class="form-control informacion_a_cambiar mod_nombre_proyecto" placeholder="Nombre del Proyecto" required maxlength="499" style="margin-bottom: 10px">
          <div class="row">
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_fecha_inicial">
              <div class="input-group date form_datetime form_date_fecha_proyecto agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicial" id="fecha_inicial" type="text" value="" name="fecha_inicial" required>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_fecha_final">
              <div class="input-group date form_datetime form_date_fecha_proyecto agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"data-link-field="dtp_input1">
                <input class="form-control sin_focus f_final" size="16" placeholder="Fecha Final" id="fecha_final" type="text" value="" name="fecha_final" required>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>

            <div class="col-xs-12 col-sm-6">
              <select name="id_tipo_recurso" id="id_tipo_recurso" title="Tipo de recurso" required class="form-control cbx_tipo_recurso informacion_a_cambiar mod_tipo_recurso"></select>
            </div>

            <div class="col-xs-12 col-sm-6">
              <input type="number" placeholder="No Beneficiarios" name="no_beneficiarios" id="no_beneficiarios" class="form-control informacion_a_cambiar mod_no_beneficiarios" required minlength="1">
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_tipo_responsable">
              <select name="tipo_responsable" id="tipo_responsable" title="Tipo de Responsable" class="form-control informacion_a_cambiar mod_tipo_responsable">
                <option value>Seleccione el tipo de responsable</option>
                <option value="interno">Interno</option>
                <option value="externo">Externo</option>
              </select>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_tipo_proyecto_grado">
              <select name="tipo_proyecto_grado" id="tipo_proyecto_grado" title="Tipo de Proyecto de Grado" class="form-control cbx_tipo_proyecto_grado informacion_a_cambiar mod_tipo_proyecto_grado"></select>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_formacion_responsable">
              <select name="formacion_responsable" id="formacion_responsable" title="Formación del responsable" required class="form-control cbx_formacion_responsable informacion_a_cambiar mod_formacion_responsable"></select>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar" id="input_responsable_externo" style="display: none;">
              <input type="text" name="id_responsable_externo" id="id_responsable_externo" hidden>
              <div class="input-group" style="margin: 6px 0 0">
                <input type="text" class="form-control sin_margin sin_focus" id="txt_nombre_responsable_externo" placeholder="Responsable">
                <span class="input-group-addon pointer" id="btn_eliminar_responsable_externo" style="background-color:white" title="Eliminar responsable"><span class="fa fa-times red"></span></span>
                <span class="input-group-addon pointer" id="btn_responsable_externo" style="background-color:white" title="Buscar responsable"><span class="fa fa-search red"></span> Buscar</span>
              </div>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_tipo_movilidad">
              <select name="tipo_movilidad" id="tipo_movilidad" title="Tipo de movilidad" required class="form-control cbx_tipo_movilidad informacion_a_cambiar mod_tipo_movilidad"></select>
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar otro_tipo_movilidad">
              <input type="text" name="otro_tipo_movilidad" id="otro_tipo_movilidad" placeholder="Otro tipo de movilidad" title="Otro tipo de movilidad" class="form-control otro_tipo_movilidad">
            </div>

            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_laboratorio">
              <select name="id_laboratorio" id="id_laboratorio" title="Elegir laboratorio" required class="form-control cbx_laboratorios informacion_a_cambiar mod_laboratorio"></select>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_resumen" id="btn_resumen">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="resumen" style="width: 100%" title="Resumen del Proyecto"><span id="span_resumen" class="glyphicon glyphicon-ok" style="float:left;"></span> Resumen</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_justificacion" id="btn_justificacion">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="justificacion" style="width: 100%" title="Justificación del Proyecto"><span id="span_justificacion" class="glyphicon glyphicon-ok" style="float:left;"></span> Justificación</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_planteamiento_problema" id="btn_planteamiento_problema">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="planteamiento_problema" style="width: 100%" title="Planteamiento del Problema del Proyecto"><span id="span_planteamiento_problema" class="glyphicon glyphicon-ok" style="float:left;"></span> Planteamiento del Problema</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_marco_teorico" id="btn_marco_teorico">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="marco_teorico" style="width: 100%" title="Marco Teórico del Proyecto"><span id="span_marco_teorico" class="glyphicon glyphicon-ok" style="float:left;"></span> Marco Teórico</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_estado_arte" id="btn_estado_arte">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="estado_arte" style="width: 100%" title="Estado del Arte del Proyecto"><span id="span_estado_arte" class="glyphicon glyphicon-ok" style="float:left;"></span> Estado del Arte</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_diseno_metodologico" id="btn_diseno_metodologico">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="diseno_metodologico" style="width: 100%" title="Diseño Metodológico del Proyecto"><span id="span_diseno_metodologico" class="glyphicon glyphicon-ok" style="float:left;"></span> Diseño Metodológico</span></div>
            </div>
            <div class="col-xs-12 col-sm-6 informacion_a_cambiar mod_resultados_esperados" id="btn_resultados_esperados">
              <div style="margin-top: 5px;"><span class="btn btn-default btnAgregar text-area" id="resultados_esperados" style="width: 100%" title="Resultados esperados del Proyecto"><span id="span_resultados_esperados" class="glyphicon glyphicon-ok" style="float:left;"></span> Resultados Esperados</span></div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_agregar_preguntas_convenio_proceedings" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_preguntas_convenio_proceedings" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-question-circle"></span> Algunas preguntas</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 text-center">
              <h4>¿Operacionaliza un convenio?</h4>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="si_operacionaliza" name="operacionaliza" value="Sí">
                <label for="si_operacionaliza" title="Sí operacionaliza" style="margin: 5px 0;"> Sí</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-danger">
                <input type="radio" id="no_operacionaliza" name="operacionaliza" value="No">
                <label for="no_operacionaliza" title="No operacionaliza" style="margin: 5px 0;"> No</label>
              </div>
            </div>
            <div class="col-md-12">
              <input type="text" name="codigo_convenio" id="codigo_convenio" placeholder="Código del convenio" class="form-control" style="display: none;">
            </div>
            <div class="col-md-12 text-center" style="margin-top: 20px;">
              <h4>¿El evento tiene PROCEEDINGS?</h4>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="si_proceedings" name="proceedings" value="Sí">
                <label for="si_proceedings" title="Sí tiene proceedings" style="margin: 5px 0;"> Sí</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-danger">
                <input type="radio" id="no_proceedings" name="proceedings" value="No">
                <label for="no_proceedings" title="No tiene proceedings" style="margin: 5px 0;"> No</label>
              </div>
            </div>
            <div class="col-md-12">
              <p style="margin: 10px 0 4px 0;">Verificado por:</p>
              <input type="text" name="verificado_por" id="verificado_por" placeholder="Alguien" class="form-control" required>
            </div>
            <div class="col-md-12">
              <p style="margin: 10px 0 4px 0;">Rubros para financiar la movilidad</p>
              <div class="input-group margin1" id='orden_sap'>
                <input type="text" id="id_codigo_sap" name="id_codigo_sap" hidden>
                <input type="text" class="form-control sin_margin sin_focus" placeholder="Código SAP" name="codigo_orden_sap" id="txt_nombre_orden_sap" required>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btn_orden_sap" style='width:120px'>
                    <span class='red fa fa-search'></span>
                    Orden SAP
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar" id="guardar_convenio_proceedings"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_buscar_codigo" role="dialog">
  <div class="modal-dialog modal-lg ">
    <form action="#" id="form_buscar_codigo" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-search"></span> <span id='titulo_modal_buscar'>Buscar Código
              SAP</span></h3>
        </div>
        <div class="modal-body">
          <div>
            <div class="input-group col-md-8" style="margin: 0 0 20px 0;">
              <input type="text" class="form-control sin_margin" placeholder="Ingrese nombre" id="txt_codigo_sap">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit" id="buscar_cod_sap" style='width:100px'><span
                    class='red fa fa-search'></span>
                  Buscar!</button>
              </span>
            </div>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_codigos"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th colspan="3" class="nombre_tabla">TABLA CÓDIGOS SAP</th>
                </tr>
                <tr class="filaprincipal ">
                  <td>Código</td>
                  <td>Descripción</td>
                  <td>***</td>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA PARTICIPANTES</td>
                <td class="sin-borde text-right border-left-none" colspan="3">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_participantes" id='btn_buscar_investigador' title="Agregar Participante"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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
      <div class="modal-body">
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div id="datos_perso" class="">
              <table class="table" id="tabla_detalle_participante" width="100%">
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
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_buscar_persona" role="dialog">
  <div class="modal-dialog modal-lg">
    <form action="#" id="form_buscar_persona" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Personas</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
              <span class="input-group-btn"><button class="btn btn-default" type="button" id="btn_agregar_persona"><span class='fa fa-user-plus red'></span> Nuevo</button></span>
                <input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del docente">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="col-md-2 radio_button">
              <div class="funkyradio funkyradio-success" style="margin-top:5px;">
                <input class="tipo_persona_buscar" type="radio" id="docentes" name="tipo_persona_buscar" value="docentes">
                <label for="docentes" title="Docentes" style="margin-top:0; padding:0;">Docentes</label>
              </div>
            </div>
            <div class="col-md-2 radio_button">
              <div class="funkyradio funkyradio-success" style="margin-top:5px;">
                <input class="tipo_persona_buscar" type="radio" id="otros" name="tipo_persona_buscar" value="otros">
                <label for="otros" title="otros" style="margin-top:0; padding:0;">Otros</label>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width:100%;">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_postulantes_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA POSTULANTES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td style="width: 15%;">Ver</td>
                    <td title="Nombre completo de la persona">Nombre Completo</td>
                    <td style="width:15%;" title="Identificación de la persona">Identificación</td>
                    <td class="" style="width:15%;">Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_agregar_participante" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_participante" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_participante"><span class="fa fa-plus-circle"></span> Agregar Participante</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="text" name="id_postulante" id="id_postulante" hidden>
            <input type="number" name="tipo_tabla" id="tipo_tabla" hidden>
            <div class="col-xs-12">
              <label id="nombre_postulante" class="form-control"></label>
            </div>
            <div class="col-sm-6">
              <select name="tipo_participante" id="tipo_participante" class="form-control cbx_tipo_participante" title="Tipo de Participante"></select>
            </div>
            <div class="col-xs-6">
              <input type="text" list="instituciones" name="institucion" id="institucion" class="form-control" placeholder="Institución" title="Intitución del Participante">
              <datalist id="instituciones"></datalist>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_detalle_persona" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body">
        <div class="row" style="width: 100%">
          <div class="col-xs-12">
            <div id="datos_perso" class="">
              <table class="table" id=tabla_detalle_persona>
                <tr class="nombre_tabla">
                  <td colspan="2">Datos</td>
                </tr>
                <tr>
                  <td class="ttitulo">Nombre Completo</td>
                  <td class="nombre_completo"></td>
                </tr>
                <tr>
                  <td class="ttitulo">Tipo identificación</td>
                  <td class="tipo_identificacion"></td>
                </tr>
                <tr>
                  <td class="ttitulo">Identificación</td>
                  <td class="identificacion"></td>
                </tr>
                <tr>
                  <td class="ttitulo">Lugar Expedición</td>
                  <td class="lugar_expedicion"></td>
                </tr>
                <tr>
                  <td class="ttitulo">Fecha Nacimiento</td>
                  <td class="fecha_nacimiento"></td>
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

<div class="modal fade" id="modal_agregar_persona" role="dialog">
    <div class="modal-dialog">
        <form  id="form_agregar_persona" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-users"></span> Agregar Persona Externa</h3>
                </div>
                <div class="modal-body ">
                    <div class="row">
                      <div class="col-md-10 col-md-offset-1">
                        <select name="id_tipo_identificacion" required class="form-control  cbxtipoIdentificacion"> </select>   
                        <input min="1" type="number" name="identificacion"  class="form-control inputt" placeholder="Cedula" required>
                        <div class="agro agrupado">
                            <div class="input-group">
                                <input type="text" name="apellido" class="form-control" placeholder="Primer Apellido"  required>
                                <span class="input-group-addon">-</span>
                                <input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido" required>
                            </div>
                        </div>
                        <div class="agro agrupado">
                            <div class="input-group">
                                <input type="text" name="nombre" class="form-control" placeholder="Primer Nombre" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" name="segundo_nombre"  class="form-control" placeholder="Segundo Nombre" >
                            </div>
                        </div>
                      </div>
                    </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit"  class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
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
                <td colspan="1" class="nombre_tabla">TABLA LUGARES</td>
                <td class="sin-borde text-right border-left-none" colspan="2">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_lugares" id="btn_agregar_lugar" title="Agregar Lugar"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_lugar" role="dialog">
  <div class="modal-dialog modal-sm">
    <form action="#" id="form_agregar_lugar" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_lugar"><span class="fa fa-plus-circle"></span> Agregar Lugar</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <input type="text" name="pais" id="pais" class="form-control" placeholder="País" title="País" required>
              <input type="text" name="ciudad" id="ciudad" class="form-control" placeholder="Ciudad" title="Ciudad" required>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA INSTITUCIONES</td>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_instituciones" id="btn_agregar_institucion" title="Agregar institución"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_institucion" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_institucion" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_institucion"><span class="fa fa-plus-circle"></span> Agregar Institución</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <select name="id_institucion" id="id_institucion" class="form-control cbx_institucion" title="Intitución" required></select>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="responsabilidad_contraparte_institucion" id="responsabilidad_contraparte_institucion" class="form-control" placeholder="Responsabilidad de la contraparte">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="responsabilidad_cuc_institucion" id="responsabilidad_cuc_institucion" class="form-control" placeholder="Responsabilidad de la CUC">
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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

<div class="modal fade" id="modal_agregar_programa" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_programa" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_programa"><span class="fa fa-plus-circle"></span> Agregar Programa</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-10 col-md-offset-1">
              <input type="text" list="lista_programas" name="programa" id="programa" class="form-control" placeholder="Seleccione el Programa" title="Programa de pregrado o posgrado" required>
              <datalist id="lista_programas" ></datalist>
            </div>
            <div class="col-sm-10 col-md-offset-1">
              <select name="id_tipo_interaccion" id="id_tipo_interaccion" class="form-control cbx_tipo_interaccion" title="Tipo de Interacción" required></select>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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

<div class="modal fade" id="modal_agregar_asignatura" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_asignatura" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_asignatura"><span class="fa fa-plus-circle"></span> Agregar Asignatura/Proyecto</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-10 col-md-offset-1">
              <textarea name="asignatura_proyecto" id="asignatura_proyecto" class="form-control" rows="10"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA SUBLÍNEAS DE INVESTIGACIÓN</td>
                <td class="sin-borde text-right border-left-none" colspan="2">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_sublineas" id="btn_agregar_sublinea" title="Agregar Sublínea"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_sublinea" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_sublinea" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_sublinea"><span class="fa fa-plus-circle"></span> Agregar Sublínea de Investigación</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <select class="form-control cbx_grupo_investigacion" name="grupo_investigacion" id="grupo_de_investigacion" title="Grupo de Investigación" required></select>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_linea_investigacion" name="linea_investigacion" id="linea_investigacion" title="Línea de Investigación" required></select>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_sublinea_investigacion" name="sublinea_investigacion" id="sublinea_investigacion" title="Sublínea de Investigación" required><option>Seleccione una Sub-Línea de Investigación</option></select>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="1" class="nombre_tabla">TABLA ODS</td>
                <td class="sin-borde text-right border-left-none" colspan="1">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_ods" id="btn_agregar_ods" title="Agregar Bibliografía"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_ods" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_ods" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_ods"><span class="fa fa-plus-circle"></span> Agregar Objetivo de Desarrollo Sostenible</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <select name="ods" id="ods" class="form-control cbx_ods" required></select>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA OBJETIVOS</td>
                <td class="sin-borde text-right border-left-none" colspan="2">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_objetivos" id="btn_agregar_objetivo" title="Agregar Objetivo"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_objetivo" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_objetivo" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_objetivo"><span class="fa fa-plus-circle"></span> Agregar Objetivo</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="general" name="tipo_objetivo" value="general">
                <label for="general" title="General" style="margin: 5px 0;"> General</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="especifico" name="tipo_objetivo" value="especifico">
                <label for="especifico" title="Específico" style="margin: 5px 0;"> Específico</label>
              </div>
            </div>
            <div class="col-md-12">
              <textarea name="descripcion_objetivo" id="descripcion_objetivo" style="Width:100%; height:50%;" class="form-control" title="Descripción del Objetivo" placeholder="Descripción del Objetivo" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA IMPACTOS Y/O EFECTOS ESPERADOS</td>
                <td class="sin-borde text-right border-left-none" colspan="1">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_impactos" id="btn_agregar_impacto" title="Agregar Objetivo"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_impacto" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_impacto" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_impacto"><span class="fa fa-plus-circle"></span> Agregar Impacto y/o Efectos Esperados</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <select class="form-control cbx_tipo_impacto" id="tipo_impacto" name="tipo_impacto" title="Tipo de Impacto" required></select>
            </div>
            <div class="col-md-12">
              <textarea name="descripcion_impacto" id="descripcion_impacto" style="Width:100%; height:50%;" class="form-control comentarios" title="Descripción del Impacto" placeholder="Descripción del Impacto" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                <td colspan="2" class="nombre_tabla">TABLA PRODUCTOS ESPERADOS</td>
                <td class="sin-borde text-right border-left-none" colspan="2">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_productos" id="btn_agregar_producto" title="Agregar Producto Esperado"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
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

<div class="modal fade" id="modal_agregar_producto" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_producto" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_producto"><span class="fa fa-plus-circle"></span> Agregar Producto Esperado</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus" required id="txt_numero_participantes_producto" value="Ningún Participante asignado">
                <span class="input-group-addon pointer btn_buscar_responsable" style="background-color:white" title="Responsable(s) del producto esperado"><span class="fa fa-eye red"></span> Responsable(s)</span>
              </div>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_tipo_producto" name="tipo_producto" id="tipo_producto" title="Tipo de Producto" required></select>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_productos" name="producto" id="producto" title="Producto" required><option value>Seleccione un Producto</option></select>
            </div>
            <div class="col-md-12">
              <textarea name="observaciones" id="observaciones" style="Width:100%; height:20%;" class="form-control comentarios" title="Observaciones" placeholder="Observaciones"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
                  </tr>
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
  <div class="modal-dialog modal-lg">
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
                <td colspan="2" class="nombre_tabla">TABLA CRONOGRAMA</td>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_cronograma" id="btn_agregar_cronograma" title="Agregar Cronograma"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td style="width:10%">Ver</td>
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

<div class="modal fade" id="modal_agregar_cronograma" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_cronograma" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_cronograma"><span class="fa fa-plus-circle"></span> Agregar Cronograma</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus" required id="txt_numero_participantes_cronograma" value="Ningún Participante asignado">
                <span class="input-group-addon pointer btn_buscar_responsable" style="background-color:white" title="Responsable(s) del producto esperado"><span class="fa fa-eye red"></span> Responsable(s)</span>
              </div>
            </div>
            <div class="col-xs-12">
              <select class="form-control cbx_objetivos_especificos" name="objetivo_especifico" id="objetivo_especifico" title="Objetivo Específico" required></select>
            </div>
            <div class="col-xs-12">
              <div class="row">
                <div class="col-xs-12 col-sm-6">
                  <div class="input-group date form_datetime form_date_fecha_proyecto agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"data-link-field="dtp_input1">
                    <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicial" type="text" value="" name="fecha_inicial_cronograma" id="fecha_inicial_cronograma" required>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                  <div class="input-group date form_datetime form_date_fecha_proyecto agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"data-link-field="dtp_input1">
                    <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Final" type="text" value="" name="fecha_final_cronograma" id="fecha_final_cronograma" required>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <textarea name="actividad" id="actividad" style="Width:100%; height:20%;" class="form-control" title="Actividad" placeholder="Actividad" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_ver_cronograma" role="dialog">
  <div class="modal-dialog modal-lg">
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
                  <td style="text-align: center;">
                    <!-- <select class="ver_tipo_moneda form-control" id="ver_tipo_moneda_resumen_presupuesto">
                      <option value="COP">COP</option>
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                    </select> -->
                  </td>
                </tr>
                <tr>
                  <td colspan="2" class="ttitulo text-right">Total Presupuesto</td>
                  <td colspan="3" id="total_presupuesto"></td>
                </tr>
                <tr class="informacion_a_cambiar presupuesto_laboratorio">
                  <td colspan="2" id="titulo_total_iva" class="ttitulo text-right">Total IVA</td>
                  <td colspan="3" id="total_iva"></td>
                </tr>
                <tr class="informacion_a_cambiar presupuesto_laboratorio">
                  <td colspan="2" class="ttitulo text-right">Total Presupuesto con IVA</td>
                  <td colspan="3" id="total_presupuesto_iva"></td>
                </tr>
                <tr class="informacion_a_cambiar presupuesto_laboratorio">
                  <td colspan="2" class="ttitulo text-right">Costo total por beneficiario</td>
                  <td colspan="3" id="costo_total_beneficiario"></td>
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
                  <td colspan="3" class="nombre_tabla">TABLA PRESUPUESTOS</td>
                  <td class="sin-borde text-right border-left-none" colspan="2">
                    <input type="text" id="ver_tipo_presupuesto" hidden>
                    <span class="btn btn-default informacion_a_cambiar mod_proyecto_presupuestos" id="btn_agregar_presupuesto" title="Agregar Presupuesto"> <span class="fa fa-plus red"></span> Agregar</span>
                  </td>
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

<div class="modal fade" id="modal_agregar_presupuesto" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_presupuesto" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_presupuesto"><span class="fa fa-plus-circle"></span> Agregar Presupuesto</h3>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
            Tener en cuenta el <strong>Acuerdo CD 1568</strong> al agregar presupuestos.
            <a href="<?php echo base_url() ?>archivos_adjuntos/proyectos_index_creacion/formatos/ACUERDO CD No. 1568.pdf" target="_blank">Clic aqui, para ver</a>
          </div>
          <div class="row">
            <div class="col-md-8 col-md-offset-2">
              <input type="text" id="tipo_presupuesto" name="tipo_presupuesto" hidden>
            </div>
            <div id="inputs_presupuesto"></div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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

<div class="modal fade" id="modal_modificar_presupuesto" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_modificar_presupuesto" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-pencil-square-o"></span> Modificar Presupuesto</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 col-md-offset-2" id="inputs_modificar_presupuesto" style="margin-top: 12px;"></div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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
            <table class="table table-bordered table-hover table-condensed" id="tabla_soportes" cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE SOPORTES</td>
                    <td class="sin-borde text-right border-left-none" colspan="3" >
                      <span class="btn btn-default btnAgregar informacion_a_cambiar mod_proyecto_soportes" id="agregar_adjuntos_nuevos">
                        <span class="fa fa-plus red"></span> Agregar
                      </span>
                    </td>
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

<div class="modal fade" id="modal_enviar_archivos" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus-circle"></span> Agregar Soportes</h3>
      </div>
      <div class="modal-body">
        <div class="row" style="width: 100%;">
          <div class="col-md-12">
            <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
              <input type="hidden" name="id" id="id_solicitud" val="0">
              <div class="dz-message needsclick"><p>Arrastre archivos o presione click aquí</p></div>
            </form>
          </div>
          <div class="col-md-12">
            <p class="text-muted">ADJUNTAR LOS DOCUMENTOS SOLICITADOS DEPENDIENDO DE LA ACTIVIDAD:</p>
            <ul class="text-muted internacionalizacion">
              <li>Si es para Firma de Convenio: Correos o Cartas de Intención.</li>
              <li>Si estas son para operacionalización de convenios, Plan de Operacionalización aprobado por ambas instituciones.</li>
              <li>Si es Ponencia: Carta de Aceptación y Resumen de Ponencia. Eventos con proceedings.</li>
              <li>Certificación de Ingles B2 (Requisito Obligatorio).</li>
              <li>Carta de Invitación (Para movilidad Saliente).</li>
              <li>Agenda del evento.</li>
              <li>SI ES UNA MOVILIDAD ENTRANTE FAVOR ADJUNTAR PASAPORTE DEL INVITADO.</li>
            </ul>

            <ul class="text-muted investigacion">
              <li>Cotización o Factura del tipo de presupuesto Servicios Profesionales</li>
              <li>Si el proyecto se realizará dentro del marco de un convenio adjuntar soporte</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button id="cargar_adj" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
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
                <td colspan="1" class="nombre_tabla">TABLA BIBLIOGRAFÍA</td>
                <td class="sin-borde text-right border-left-none" colspan="2">
                  <span class="btn btn-default informacion_a_cambiar mod_proyecto_bibliografias" id="btn_agregar_bibliografia" title="Agregar Bibliografía"> <span class="fa fa-plus red"></span> Agregar</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 5%">Ver</td>
                <td title="Bibliografía">Bibliografía</td>
                <td>Acciones</td>
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

<div class="modal fade" id="modal_agregar_bibliografia" role="dialog">
  <div class="modal-dialog modal-md">
    <form action="#" id="form_agregar_bibliografia" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_bibliografia"><span class="fa fa-plus-circle"></span> Agregar Bibliografía</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <textarea name="bibliografia" id="descripcion_bibliografia" style="Width:100%; height:30%;" class="form-control comentarios" title="Bibliografía" placeholder="Bibliografía" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_correcciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cog"></span> Cambios</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_cambios" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="3" class="nombre_tabla">TABLA CAMBIOS</td>
              </tr>
              <tr class="filaprincipal">
                <td style="width: 20%">Ver</td>
                <td title="Ítem">Ítem</td>
                <td title="Tipo">Tipo</td>
                <td title="Fecha">Fecha</td>
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

<div class="modal fade" id="modal_detalle_correccion" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle del Cambio</h3>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_cambios" cellspacing="0" width="100%">
          <tr class="nombre_tabla text-left">
            <td colspan="6">INFORMACIÓN GENERAL</td>
          </tr>
          <tr>
            <td colspan="2" class="ttitulo">Ítem</td>
            <td colspan="4" id="nombre_item"></td>
          </tr>
          <tbody id="cambios_item">
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_text_area" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <form action="#" id="agregar_text_area" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> <span id='titulo_text_area'>Mi Titulo</span></h3>
        </div>
        <div class="modal-body" id="body-modal-text-area">
          <p>Ten en cuenta que, tienes que guardar tus cambios o se perderán.</p>
          <textarea class="form-control comentarios" id="data_text_area" style="width: 100%; height: 50%;" required></textarea>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
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

<div class="modal fade" id="modal_fecha_limite" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-clock-o"></span> Fecha Límite</h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div class="input-group date form_datetime fecha_limite agro" data-date="" data-date-format="yyyy-mm-dd - HH:ii" data-link-field="dtp_input1">
              <input class="form-control sin_focus" size="16" id="fecha_limite" placeholder="Fecha Límite" type="text" value="" required="true" name="fecha">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btn_fecha_limite"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_lista_comite" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_comite" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-clock-o"></span> Elegir comité</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-10 col-md-offset-1">
              <select name="id_comite" id="cbx_lista_comite" class="form-control" required></select>
              <textarea name="observaciones_agregar_comite" id="observaciones_agregar_comite" class="form-control oculto" rows="4" placeholder="Observaciones"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_agregar_responsable" role="dialog">
  <div class="modal-dialog modal-md">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 id="ver_titulo" class="modal-title"><span class="fa fa-plus-circle"></span> Agregar Responsable(s)</h3>
      </div>
      <div class="modal-body">
        <div class="table table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_responsables" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr class="">
                <td colspan="3" class="nombre_tabla">TABLA PARTICIPANTES</td>
              </tr>
              <tr class="filaprincipal">
                <td title="Nombre del Participante">Nombre Completo</td>
                <td title="Identificación del Participante">Identificación</td>
                <td class="" style="width:15%">Acciones</td>
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

<div class="modal fade" id="modal_seleccion_departamento" role="dialog" >
  <div class="modal-dialog">
    <form action="#" id="form_seleccion_departamento" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> <span id="text_add_arts"></span> Seleccionar Departamento</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="alert alert-info">
              El docente no cuenta con un departamento registrado. Por favor seleccionar su departameto de la lista.
            </div>
            <select class="form-control cbx_seleccion_departamento" id="departamento_proyecto" required></select>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
      </form>
  </div>
</div>

<script>
  $(".form_date_fecha_proyecto").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayBtn: true,
    maxView: 4,
    minView: 2,
  });
</script>

<script>
  let startDate = new Date();
  $(".form_date").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    startDate,
    todayBtn: true,
    maxView: 4,
    minView: 2,
  });
</script>

<script>
  $(".fecha_limite").datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    autoclose: true,
    startDate: new Date(),
  });
</script>

<script>
  $(document).ready(function(){
    inactivityTime();
    recibir_archivos();
    mostrar_notificaciones();
    listar_proyectos_usuario();
    <?php if($administra){?>
      listar_comites();
      listar_instituciones_bdd();
      <?php } ?>
    cargar_comites();
    Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
    Cargar_parametro_buscado(76, ".cbx_tipo_proyecto", "Seleccione el Tipo de Proyecto");
    Cargar_parametro_buscado(77, '.cbx_tipo_recurso', 'Seleccione el Tipo de Recurso');
    Cargar_parametro_buscado(78, '.cbx_grupo_investigacion', 'Seleccione un Grupo de Investigación');
    Cargar_parametro_buscado_aux(79, '.cbx_estado_proyecto', 'Seleccione un Estado de Proyecto');
    Cargar_parametro_buscado(86, '#lista_programas', 'Seleccione un Programa', 'datalist');
    Cargar_parametro_buscado(87, '.cbx_linea_investigacion', 'Seleccione una Línea de Investigación');
    Cargar_parametro_buscado(169, '.cbx_tipo_movilidad', 'Seleccione el Tipo de Movilidad');
    Cargar_parametro_buscado(170, '.cbx_formacion_responsable', 'Seleccione el tipo de formación del responsable');
    Cargar_parametro_buscado(171, '.cbx_tipo_participante', 'Seleccione el Tipo de Participante');
    Cargar_parametro_buscado(174, '.cbx_tipo_producto', 'Seleccione el Tipo de Producto');
    Cargar_parametro_buscado(178, '#instituciones', 'Seleccione la institución del Participante', 'datalist');
    Cargar_parametro_buscado(191, '.cbx_tipo_interaccion', 'Seleccione el tipo de interacción del programa');
    Cargar_parametro_buscado(192, '.cbx_tipo_proyecto_grado', 'Seleccione el tipo de proyecto de grado');
    cargar_empresas();
    cargar_laboratorios();
  })
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
