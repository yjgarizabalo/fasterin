<?php $sw  = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_Bin" ?  true : false; ?>
<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo $sw || !empty($id) ? '' : 'oculto' ?>" id="listar_solicitudes">
    <div class="table-responsive col-sm-12 col-md-12">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr>
            <td colspan="3" class="nombre_tabla">TABLA SOLICITUDES <br>
              <span class="mensaje-filtro oculto" id='mensaje-filtro-evento'>
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="7">
              <?php if ($sw) { ?>
                <span class="btn btn-default btnAgregar" id="admin_solicitudes"><span class="fa fa-cogs red"></span> Administrar</span>
                <a href="<?php echo base_url() ?>index.php/bienestar/exportar_solicitudes" type="button" class="btn btn-default" id="exportar_solicitudes"><span class="fa fa-cloud-download red"></span> Exportar Solicitudes</a>
                <a href="<?php echo base_url() ?>index.php/bienestar/exportar_todas_encuestas" type="button" class="btn btn-default"><span class="fa fa-cloud-download red"></span> Exportar Encuestas</a>
              <?php } ?>
              <span class="btn btn-default btnModifica" id="modificar_solicitud"><span class="fa fa-wrench red"></span> Modificar</span>
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_filtrar_solicitudes">
                <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="limpiar_filtros_sol">
                <span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Funcionario</td>
            <td>Dia</td>
            <td>Fecha Inicio</td>
            <td>Duracion</td>
            <td>Temática</td>
            <td>Solicitante</td>
            <td>Fecha Registro</td>
            <td>Estado</td>
            <td style="width:150px">Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="tablausu col-md-12 <?php echo !$sw && empty($id) ? '' : 'oculto' ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="solicitud_bienestar">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/bienestar.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Bienestar a tu clase</span>
            </div>
          </div>
        </div>
        <div id="listado_solicitudes">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Estados Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_solicitud_bienestar" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_solicitud_bienestar" method="post">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> <span> Nueva Solicitud</span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="alert alert-warning noDisponible oculto" role="alert">
              <span class="detalle_bloqueo"></span>
              <!-- <b><span class="tabla_bloqueos pointer">Ver disponibilidad</span></b> -->
            </div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group date datetime_bienestar agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha y Hora Inicio" type="text" value="" required="true" name="fecha_inicio">
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group date  agro" data-date="" data-link-field="dtp_input1" style="width: 100%;">
                  <select name="id_duracion" required class="form-control cbxduracion">
                    <option value="">Duracion</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="agrupado">
              <input type="number" class="form-control sin_margin" name='telefono' required="true" placeholder="Teléfono Solicitante">
            </div>
            <div class="clearfix"></div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <select name="id_materia" required class="form-control">
                  <option value=""">Seleccione Materia / Grupo</option></select>
                            </div>
                            <div class=" col-md-6" style="padding: 0px;">
                    <select name="id_tematica" required class="form-control cbxtematica">
                      <option value="">Seleccione Tematica</option>
                    </select>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="detalle_solicitud oculto">
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <div class="alert alert-success" role="alert">
                    <b>Programa:</b> <span class="programa"></span>
                  </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <div class="alert alert-success" role="alert">
                    <b>Semestre:</b> <span class="semestre"></span>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>

            </div>
            <div class="clearfix"></div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <select name="id_lugar" required class="form-control cbxlugar">
                  <option value="">Seleccione Lugar/ Bloque</option>
                </select>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <select name="id_ubicacion" required class="form-control cbxubicacion">
                  <option value="">Seleccione Ubicación / Salon</option>
                </select>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="agrupado estudiantes_solicitud oculto">
              <div class="col-md-12" style="padding: 0px;">
                <div class="alert alert-success" role="alert" style="text-align:center">
                  <b><span class="estudiantes"></span></b>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_estudiantes" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE ESTUDIANTE</td>
                    <td class="sin-borde text-right border-left-none" colspan="4">
                      <span class="btn btn-default btnAgregar oculto agregar_estudiantes">
                        <span class="fa fa-plus red"></span> Agregar Estudiante</span>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre Completo</td>
                    <td>Identificación</td>
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
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_modificar_bienestar" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_solicitud_bienestar_mod" method="post">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="alert alert-warning noDisponible oculto" role="alert">
              <span class="detalle_bloqueo"></span><b><span class="tabla_bloqueos pointer">Ver disponibilidad</span></b>
            </div>
            <div class="reprogramar">
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group date datetime_bienestar agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                    <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value="" required="true" name="fecha_inicio">
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                  </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group date  agro" data-date="" data-link-field="dtp_input1" style="width: 100%;">
                    <select name="id_duracion" required class="form-control cbxduracion">
                      <option value="">Duracion</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div id="mod_detalle">
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <select name="id_lugar" class="form-control cbxlugar_mod">
                    <option value="">Seleccione Lugar/ Bloque</option>
                  </select>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <select name="id_ubicacion" class="form-control cbxubicacion_mod">
                    <option value="">Seleccione Ubicación / Salon</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <input type="number" class="form-control sin_margin" name='telefono' placeholder="Teléfono Solicitante">
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <select name="id_tematica" class="form-control cbxtematica_mod">
                    <option value="">Seleccione Tematica</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <?php if ($sw) { ?>
              <textarea class="form-control comentarios" required cols="1" rows="3" name="observaciones" placeholder="Observaciones del administrador"></textarea>
            <?php } ?>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade con-scroll-modal" id="modal_disponibilidad" role="dialog">
  <div class="modal-dialog modal-95">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-calendar"></span> Disponibilidad</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <nav class="navbar navbar-default" id="nav_disponibilidad" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer" id="recibir_solicitud"><a><span class="fa fa-user-plus red" style="color:#2E79E5"></span> Recibir Solicitud</a></li>
                <li class="pointer" id="negar_solicitud"><a><span class="fa fa-ban" style="color:#cc0000"></span> Negar solicitud</a></li>
              </ul>
            </div>
          </nav>
          <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_disponibilidad" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="3" class="nombre_tabla">TABLA SOLICITUDES DISPONIBILIDAD <br>
                  <span class="mensaje-filtro oculto" id='mensaje-filtro-evento'>
                    <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span>
                </td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <span class="btn btn-default" data-toggle="modal" data-target="#modal_filtrar">
                    <span class="fa fa-filter red"></span> Filtrar</span>
                  <span class="btn btn-default" id="limpiar_filtros">
                    <span class="fa fa-refresh red"></span> Limpiar</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td class="opciones_tbl">Ver</td>
                <td>Funcionario</td>
                <td>Dia</td>
                <td>Fecha Inicio</td>
                <td>Duracion</td>
                <td>Temática</td>
                <td>Solicitante</td>
                <td>Fecha Registro</td>
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

<div class="modal fade con-scroll-modal" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <nav class="navbar navbar-default" id="nav_bienestar_detalle" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer" id="detalle_estudiantes"><a><span class="fa fa-graduation-cap red"></span> Estudiantes</a></li>
                <?php if ($sw) { ?>
                  <li class="pointer" id="funcionarios_solicitud"><a><span class="fa fa fa-users red"></span> Funcionarios</a></li>
                  <li class="pointer" id="ver_encuestas"><a><span class="fa fa-star red"></span> Encuestas</a></li>
                  <li class="pointer" id="ver_estados"><a><span class="fa fa-cogs red"></span> Estados</a></li>
                <?php } ?>
                <li class="pointer" id="ver_modificaciones"><a><span class="fa fa-edit red"></span> Modificaciones</a></li>

              </ul>
            </div>
          </nav>
          <div class="negado oculto">
            <div class="agrupado">
              <div class="col-md-12" style="padding: 0px;">
                <div class="alert alert-danger" style="margin-bottom: 13px;" role="alert">
                  <b>SOLICITUD NEGADA/CANCELADA:</b> <span class="motivo"></span>
                </div>
              </div>
            </div>
          </div><br>
          <table class="table table-bordered table-condensed" id="tabla_detalle_factura">
            <tr class="">
              <th class="nombre_tabla" colspan="8"> Información Solicitante</th>
            </tr>
            <tr>
              <td class="ttitulo" colspan="4" style="width: 20%;">Fecha solicitud </td>
              <td class="fecha_registra" colspan="4"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2" style="width: 20%;">Solicitante </td>
              <td class="solicitante" colspan="2"></td>
              <td class="ttitulo" colspan="2" style="width: 20%;">Teléfono </td>
              <td class="telefono" colspan="2"></td>
            </tr>
            <tr class="">
              <th class="nombre_tabla" colspan="8"> Información Clase</th>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2" style="width: 20%;">Fecha Inicio </td>
              <td class="fecha_inicio" colspan="2"></td>
              <td class="ttitulo" colspan="2" style="width: 20%;">Fecha Fin </td>
              <td class="fecha_fin" colspan="2"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Programa</td>
              <td class="programa" colspan="2"></td>
              <td class="ttitulo" colspan="2">Asignatura/Grupo</td>
              <td class="materia" colspan="2"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Dia/Duracion</td>
              <td class="dia_duracion" colspan="2"></td>
              <td class="ttitulo" colspan="2">Semestre/N° Estudiantes</td>
              <td class="" colspan="2"><span class="semestre_estudiantes"></span>/ <span class="cantidad_estudiantes"></span></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Ubicacion</td>
              <td class="ubicacion" colspan="2"></td>
              <td class="ttitulo" colspan="2">Lugar</td>
              <td class="lugar" colspan="2"></td>
            </tr>
            <tr class="">
              <th class="nombre_tabla" colspan="8"> Información Bienestar</th>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Estrategia PASPE</td>
              <td class="estrategia" colspan="2"></td>
              <td class="ttitulo" colspan="2">Tematica</td>
              <td class="tematica" colspan="2"></td>
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

<form id="form_asignar_funcionario" method="post">
  <div class="modal fade con-scroll-modal" id="modal_asignar_funcionario" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Asignar Funcionario</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_funcionarios" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA FUNCIONARIOS <br>
                  <td class="sin-borde text-right border-left-none" colspan="3">
                    <span class="btn btn-default" id="asignar_funcionario"> <span class="fa fa-plus red"></span> Asignar Funcionario</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Nombre Completo</td>
                  <td>Identificacion</td>
                  <td class="opciones_tbl_btn">Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active "><span class="glyphicon glyphicon-plus"></span><span class="Guardar"> Asignar</span></button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_estudiantes_solicitud" role="dialog" style="overflow-y: scroll;">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_estudiantes_solicitud" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">TABLA DE ESTUDIANTES</td>
              <td class="sin-borde text-right border-left-none" colspan="6">
                <span class="btn btn-default btnAgregar oculto" id="agregar_estudiantes_nuevos">
                  <span class="fa fa-plus red"></span> Agregar Estudiante</span>
            <tr class="filaprincipal ">
              <td class="opciones_tbl">No.</td>
              <td>Nombre Completo</td>
              <td>Identificación</td>
              <td class="opciones_tbl_btn">Acción</td>
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


<form id="form_asignar_funcionario_solicitud" method="post">
  <div class="modal fade con-scroll-modal" id="modal_asignar_funcionario_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Funcionarios Agregados</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_funcionarios_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA FUNCIONARIOS AGREGADOS<br>
                  <td class="sin-borde text-right border-left-none" colspan="3">
                    <span class="btn btn-default" id="asignar_funcionario_solicitud"> <span class="fa fa-plus red"></span> Asignar Funcionario</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Nombre Completo</td>
                  <td>Identificacion</td>
                  <td class="opciones_tbl_btn">Acción</td>
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
</form>

<div class="modal fade" id="modal_filtrar" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select id="estado_estrategia" class="form-control inputt cbxestrategia">
            <option value="">Filtrar Solicitudes por Estrategia</option>
          </select>
          <div class="agro agrupado">
            <div class="input-group">
              <input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_nombre_persona'>
              <span class="input-group-addon pointer" id='btn_buscar_persona' style='	background-color:white'><span class='fa fa-search red'></span> Funcionario</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btnfiltrar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade con-scroll-modal" id="modal_administracion" role="dialog">
  <div class="modal-dialog" style="width: 690px;">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Administracion</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <nav class="navbar navbar-default" id="nav_bienestar_detalle" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer active" id="asignar_funcionarios"><a><span class="fa fa-list red"></span> Temáticas</a></li>
                <li class="pointer" id="asignar_coordinadores"><a><span class="fa fa-book red"></span> Programas</a></li>
                <li class="pointer" id="bloqueos"><a><span class="fa fa-ban red"></span> Bloqueos</a></li>
                <li class="pointer" id="horario_funcionario"><a><span class="fa fa-calendar red"></span> Horario</a></li>
              </ul>
            </div>
          </nav>
          <div id="container_turnos_bib">
            <table class="table table-bordered table-hover table-condensed" id="tabla_tematicas" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th class="nombre_tabla">TABLA DE TEMÁTICAS</th>

                  <td class="sin-borde text-right border-left-none" colspan="6">
                    <button class="btn btn-default agregar_tematica"> <span class="fa fa-plus red"></span> Nueva Tematica</button>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td style='width : "80px"'>Acción</td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div id="container_bloqueos_bib" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_bloqueos" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th class="nombre_tabla">TABLA DE BLOQUEOS</th>

                  <td class="sin-borde text-right border-left-none" colspan="6">
                    <button class="btn btn-default agregar_bloqueo"> <span class="fa fa-plus red"></span> Nuevo Bloqueo</button>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>N°</td>
                  <td>Nombre</td>
                  <td>Fecha Inicio</td>
                  <td>Fecha Fin</td>
                  <td>Acción</td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div id="container_turnos_bib" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_turnos_bib" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th class="nombre_tabla">TABLA DE ESTRATEGIAS</th>
                  <td class="sin-borde text-right border-left-none" colspan="6">
                    <button class="btn btn-default btn_new_turn"> <span class="fa fa-plus red"></span> Nueva Estrategia</button>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>N°</td>
                  <td>Nombre</td>
                  <td>Acción</td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div id="container_funcionarios_bib" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_horarios" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th class="nombre_tabla">TABLA HORARIOS</th>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    <button class="btn btn-default btn_horario"> <span class="fa fa-plus red"></span> Nuevo Horario</button>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Día</td>
                  <td>Hora Inicio</td>
                  <td>Hora Fin</td>
                  <td>Acción</td>
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
<form id="form_funcionario_tematica" method="post">
  <div class="modal fade con-scroll-modal" id="modal_funcionario_tematica" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Asignar Funcionario</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_funcionarios_tematica" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA FUNCIONARIOS <br>
                  <td class="sin-borde text-right border-left-none" colspan="3">
                    <span class="btn btn-default" id="asignar_funcionario_tematica"> <span class="fa fa-plus red"></span> Asignar Funcionario</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Nombre Completo</td>
                  <td>Identificacion</td>
                  <td class="opciones_tbl_btn">Acción</td>
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
</form>

<!-- modal lista horarios funcionario -->
<div class="modal fade" id="modal_funcionarios_horarios" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Disponibilidad Funcionarios</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_funcionarios_horarios" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td class="nombre_tabla">TABLA FUNCIONARIOS</td>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    <span class="btn btn-default" id="asignar_funcionario_horario"> <span class="fa fa-plus red"></span> Asignar Funcionario</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Nombre Completo</td>
                  <td>Identificación</td>
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
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<form id="form_buscar_persona" method="post">
  <div class="modal fade" id="modal_buscar_persona" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Persona</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_per_buscar' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
                <span class="input-group-btn"><button class="btn btn-default test" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Nombre Completo</td>
                    <td>Identificacion</td>
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
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- modal crear horario funcionario -->
<div class="modal fade" id="modal_crear_horario" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_guardar_horario" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> <span class="titulo_modal"></span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="col-md-12" style="padding: 0px;">
              <select name="id_dia" id="id_dia" class="form-control cbxdia">
                <option value="">Seleccione Día</option>
              </select>
            </div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                  <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Inicio" type="text" value="" required="true" name="hora_inicio" id="hora_inicio">
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                  <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Fin" type="text" value="" required="true" name="hora_fin" id="hora_fin">
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="padding: 0px;">
              <textarea class="form-control inputt" name="descripcion" id="descripcion" placeholder="Descripción"></textarea>
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

<form id="form_buscar_estudiante" method="post">
  <div class="modal fade" id="modal_buscar_estudiante" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Estudiantes</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_est_buscar' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
                <span class="input-group-btn"><button class="btn btn-default test" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_estudiantes_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA ESTUDIANTES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Nombre Completo</td>
                    <td>Identificacion</td>
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
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_listar_estados" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Historial de Estados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed" id="tabla_estados_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="4" class="nombre_tabla">TABLA ESTADOS</th>
              </tr>
              <tr class="filaprincipal">
                <td>No.</td>
                <td>Fecha Registro</td>
                <td>Usuario</td>
                <td>Estado</td>
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


<div class="modal fade con-scroll-modal" id="modal_detalle_encuesta" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Encuesta</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed" id="tabla_detalle_factura">
            <tr class="">
              <th class="nombre_tabla" colspan="8"> Información General</th>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2" style="width: 20%;">1) De las siguientes actividades de Bienestar Estudiantil seleccione en la que usted participó.</td>
              <td class="participacion" colspan="2"></td>
              <td class="ttitulo" colspan="2" style="width: 20%;">¿Cómo le pareció la actividad desarrollada?</td>
              <td class="actividad" colspan="2"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2" style="width: 20%;">3) La atención del servicio que usted recibió fue:</td>
              <td class="servicio" colspan="2"></td>
              <td class="ttitulo" colspan="2">4) Los temas tratados, ¿le resultan apropiados para ayudarle en su desempeño?</td>
              <td class="apropiado" colspan="2"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">5) ¿Consideras que el tema abordado aporta a tu formación integral?</td>
              <td class="integral" colspan="2"></td>
              <td class="ttitulo" colspan="2">6) ¿Cómo le pareció la metodología utilizada por la persona que dirigió la actividad?</td>
              <td class="metodología" colspan="2"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">7) ¿Qué otros cursos o talleres le gustaría que implementara la Vicerrectoría de Bienestar Universitario?</td>
              <td class="" colspan="2"><span class="otros"></span>/ <span class="cantidad_estudiantes"></span></td>
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

<form id="form_logear" method='post' id='form_logear'>
  <div class="modal fade" id="modal_logear" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-sign-in"></span> Registro de asistencia <span id='nombre'></span></h3>
        </div>
        <div class="modal-body" style="padding: 60px;">
          <div>
            <h4 class='text-center'>Para validar tu datos solo ingresa tu contraseña institucional</h4>
            <input type="password" name='password' class='form-control' placeholder='Ingrese Contraseña'>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Ingresar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_filtrar_solicitudes" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select id="estrategia_filtro" class="form-control inputt cbxestrategia">
            <option value="">Filtrar Solicitudes por Estrategia</option>
          </select>
          <select id="estado_filtro" class="form-control inputt cbxestado">
            <option value="">Filtrar Solicitudes por Estado</option>
          </select>
          <!-- <input id="fecha_filtro" class="form-control" value="" placeholder="Filtrar Por Fecha Registro" type="month" name="fecha_filtro"> -->
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
              <input class="form-control sin_margin" value="" type="date" name="fecha_filtro" id="fecha_filtro">
            </div>
          </div>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
              <input class="form-control sin_margin" value="" type="date" name="fecha_filtro_2" id="fecha_filtro_2">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btnfiltrar_sol"><span class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_encuesta_solicitud" role="dialog" style="overflow-y: scroll;">
  <div class="modal-dialog modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-users"></span> Encuestas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <div class="alert alert-success" role="alert">
            <div class="container">
              <h3 style="text-align:center;margin-top: 5px;margin-bottom: 16px;font-weight: 600;">LEYENDA DE PREGUNTAS</h3>
              <div class="col-md-6">
                <b>Pregunta 1:</b> <span>Actividad de Bienestar Estudiantil en la que participó.</span><br>
                <b>Pregunta 2:</b> <span>¿Cómo le pareció la actividad desarrollada?</span><br>
                <b>Pregunta 3:</b> <span>La atención del servicio que usted recibió fue</span><br>
                <b>Pregunta 4:</b> <span>Los temas tratados, ¿le resultan apropiados para ayudarle en su desempeño?</span><br>
              </div>
              <div class="col-md-6">
                <b>Pregunta 5:</b> <span>¿Consideras que el tema abordado aporta a tu formación integral?</span><br>
                <b>Pregunta 6:</b> <span>¿Cómo le pareció la metodología utilizada por la persona que dirigió la actividad?</span><br>
                <b>Pregunta 7:</b> <span>¿Qué otros cursos o talleres le gustaría que implementara la Vicerrectoría de Bienestar Universitario?</span>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-hover table-condensed" id="tabla_encuesta" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE ENCUESTAS</td>
                <td class="sin-borde text-right border-left-none" colspan="6">
              <tr class="filaprincipal ">
                <td>N°</td>
                <td>Pregunta N° 1</td>
                <td>Pregunta N° 2</td>
                <td>Pregunta N° 3</td>
                <td>Pregunta N° 4</td>
                <td>Pregunta N° 5</td>
                <td>Pregunta N° 6</td>
                <td>Pregunta N° 7</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <a href="<?php echo base_url() ?>index.php/bienestar/exportar_encuestas" type="button" class="btn btn-danger" id='exportar_encuestas'><span class="fa fa-cloud-download"></span> Descargar</a>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_modificaciones_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Historial de Modificaciones</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed" id="tabla_modificaciones_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="4" class="nombre_tabla">TABLA MODIFICACIONES</th>
              </tr>
              <tr class="filaprincipal">
                <td>No.</td>
                <td>Campo</td>
                <td>Anterior</td>
                <td>Actual</td>
                <td>Observaciones</td>
                <td>Fecha</td>
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
<div class="modal fade" id="ModalModificarParametro" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_modificar_valor_parametro" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-truck"></span> Modificar Orden</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row divmodifica">
            <div id="container_costo_modi"></div>
            <div class="input-group agro">
              <select name="estrategia_asignada_modi" class="form-control estrategias_agregados_modi sin_margin" id="estrategias_agregadas_modi">
                <option value="">0 Estrategia(s) a Asignar</option>
              </select>
              <span class="input-group-addon  btnElimina pointer " id="retirar_estrategia_sele_modi" title="Retirar Estrategia" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove "></span></span>
              <span class="input-group-addon  btnAgregar pointer mas_estrategias" id="mas_estrategias" title="Mas Estrategia" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus "></span> </span>
            </div>
            <input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre" name="nombre" required>
            <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripción" name="descripcion" required></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnModifica"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="modal_nuevo_valor" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_guardar_valor_parametro" method="post">

      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Temática</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div id="container_costo"></div>
            <div class="input-group agro">
              <select name="estrategia_asignada" class="form-control estrategias_agregados sin_margin" id="estrategias_agregadas">
                <option value="">0 Estrategia(s) a Asignar</option>
              </select>
              <span class="input-group-addon  btnElimina pointer " id="retirar_estrategia_sele" title="Retirar Estrategia" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove "></span></span>
              <span class="input-group-addon  btnAgregar pointer mas_estrategias" id="mas_estrategias" title="Mas Estrategia" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus "></span> </span>
            </div>
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
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

<div class="modal fade" id="Modal_seleccionar_estrategia" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-blackboard"></span> Estrategias</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <p><span class="glyphicon glyphicon-map-marker red"></span><b><span class="rec_sele"> 0</span></b> a Asignar</p>

        <div class="table-responsive" style="width: 100%">
          <table class="table table-bordered table-hover table-condensed pointer" id="tabla_estrategias_disponibles" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr class="">
                <td colspan="6" class="nombre_tabla">tabla estrategias</td>
              </tr>
              <tr class="filaprincipal">
                <td>N°</td>
                <td>Nombre</td>
                <td>Acción</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="Guardar_mas_estrategia"><span class="glyphicon glyphicon-floppy-disk"></span> Terminar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_nuevo_bloqueo" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_guardar_bloqueo" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo Bloqueo</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <select name="idtematica" class="form-control inputt cbxtematica_bloqueo">
              <option value="0">Seleccione Tematica</option>
            </select>
            <div id="agrupado">
              <div class="input-group date datetime_bloqueo_inicio agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus sin_margin" size="16" placeholder="Fecha y Hora Inicio" type="text" value="" required="true" name="bloqueo_fecha_inicio">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
              </div>
              <div class="input-group date datetime_bloqueo_fin agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus sin_margin" size="16" placeholder="Fecha y Hora Fin" type="text" value="" required="true" name="bloqueo_fecha_fin">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
              </div>
            </div>
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
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

<div class="modal fade con-scroll-modal" id="modal_disponibilidad_bloqueo" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-calendar"></span> Fechas Disponibles</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <div class="alert alert-warning " role="alert">
            <b><span class="mensaje_disponibilidad">El horario seleccionado no se encuentra disponible, por favor seleccione un horario que se encuentre en los siguientes rangos.</span></b>
          </div>
          <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_disponibilidad_bloqueo" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="3" class="nombre_tabla">TABLA FECHAS DISPONIBLES </td>
              </tr>
              <tr class="filaprincipal">
                <td class="opciones_tbl">N°</td>
                <!-- <td>Nombre</td>
                            <td>Descripción</td> -->
                <td>Fecha Inicio</td>
                <td>Fecha fin</td>
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

<div class="modal fade con-scroll-modal" id="modal_detalle_bloqueo" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Bloqueo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed" id="tabla_detalle_factura">
            <tr class="">
              <th class="nombre_tabla" colspan="8"> Información Requistro</th>
            </tr>
            <tr>
              <td class="ttitulo" style="width:200px">Usuario Registra </td>
              <td class="usuario_registra"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Registra </td>
              <td class="fecha_registra"></td>
            </tr>
            <tr>
              <td class="ttitulo">Usuario Elimina </td>
              <td class="usuario_elimina"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Elimina </td>
              <td class="fecha_elimina"></td>
            </tr>
            <tr class="">
              <th class="nombre_tabla"> Información Bloqueo</th>
            </tr>
            <tr>
              <td class="ttitulo">Temática </td>
              <td class="tematica"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Inicio </td>
              <td class="fecha_inicio"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Fin </td>
              <td class="fecha_fin"></td>
            </tr>
            <tr>
              <td class="ttitulo">Nombre </td>
              <td class="nombre"></td>
            </tr>
            <tr>
              <td class="ttitulo">Descripcion </td>
              <td class="descripcion"></td>
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
<div class="modal fade" id="modal_modificar_bloqueo" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_modificar_bloqueo" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-edit"></span> Modificar Bloqueo</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <select name="idtematica" class="form-control inputt cbxtematica_bloqueo">
              <option value="0">Seleccione Tematica</option>
            </select>
            <div id="agrupado">
              <div class="input-group date datetime_bloqueo_inicio agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus sin_margin" size="16" placeholder="Fecha y Hora Inicio" type="text" value="" required="true" name="bloqueo_fecha_inicio">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
              </div>
              <div class="input-group date datetime_bloqueo_fin agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus sin_margin" size="16" placeholder="Fecha y Hora Fin" type="text" value="" required="true" name="bloqueo_fecha_fin">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
              </div>
            </div>
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnModifica"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="modal_modificar_tematica" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_modificar_tematica" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-edit"></span> Modificar Tematica</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <select name="id_tematica" id="id_tematica" class="form-control inputt cbxtematica">
              <option value="0">Seleccione Tematica</option>
            </select>
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




<script>
  $(document).ready(function() {
    inactivityTime();
    Cargar_parametro_buscado(119, ".cbxsemestre", "Seleccione Semestre");
    Cargar_parametro_buscado(115, ".cbxlugar", "Seleccione Lugar / Bloque");
    // Cargar_parametro_buscado(121, ".cbxtematica", "Seleccione Temática");
    Cargar_parametro_buscado(121, ".cbxtematica_bloqueo", "Seleccione Temática");
    Cargar_parametro_buscado(115, ".cbxlugar_mod", "Seleccione Lugar / Bloque");
    Cargar_parametro_buscado(121, ".cbxtematica_mod", "Seleccione Temática");
    Cargar_parametro_buscado(124, ".cbxduracion", "Seleccione Duracion");
    Cargar_parametro_buscado_aux(122, ".cbxestado", "Seleccione Estado");
    Cargar_parametro_buscado(120, ".cbxestrategia", "Seleccione Estrategia");
    Cargar_parametro_buscado(100, ".cbxdia", "Seleccione Día");
    listar_solicitudes(<?php echo $id ?>);
    listar_estudiantes();
    pintar_materias_docente(<?php echo $_SESSION["persona"] ?>);
    listar_valor_parametro(121);
  });
</script>
<script type="text/javascript">
  configurar_fechas(<?php echo $Bin_Cla ?>, '.datetime_bienestar', [0]);
  configurar_fechas(<?php echo $Bin_Cla ?>, '.datetime_bloqueo_inicio', [0]);
  configurar_fechas(<?php echo $Bin_Cla ?>, '.datetime_bloqueo_fin', [0]);
  configurar_horas('.time_bienestar_gipsy');

  $(".datetime_horario").datetimepicker({
    formatViewType: 'time',
    fontAwesome: true,
    autoclose: true,
    startView: 1,
    maxView: 1,
    minView: 0,
    minuteStep: 5,
    format: 'hh:ii',
  });
</script>