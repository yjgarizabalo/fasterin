<?php
$admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
$adm_bib = $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
$aux_bib = $_SESSION["perfil"] == "Per_Aux_Bib" ? true : false;
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/calendar.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/underscore-min.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/calendar.js"></script>
<div class="container col-md-12 text-center" id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo ($admin || $adm_bib || $aux_bib) || !empty($id) ? '' : 'oculto' ?>" id="container-listado-eventos">
    <div class="table-responsive col-sm-12 col-md-12">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr>
          <tr class="">
            <td colspan="4" class="nombre_tabla">TABLA SOLICITUDES <br><span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> <span id='mensaje_filtro'>La tabla tiene algunos filtros aplicados.</span></span></td>
            <td class="sin-borde text-right border-left-none" colspan="4">
              <a class="btn btn-default" id="btn_consolidado">
                <span class="fa fa-bar-chart red"></span>
                Consolidado
              </a>
              <a class="btn btn-default" id="btn_descargar_reseña">
                <span class="fa fa-download red"></span>
                Descargar
              </a>
              <?php if ($admin || $adm_bib || $aux_bib) { ?>
                <span class="black-color pointer btn btn-default" id="btn_calendario"><span class="fa fa-calendar red"></span> Calendario</span>
              <?php } ?>
              <?php if ($admin || $adm_bib) { ?>
                <span class="black-color pointer btn btn-default" id="btn_administrar"><span class="fa fa-cog red"></span> Administrar</span>
              <?php } ?>
              <span class="btn btn-default btnModifica " id="btn_modificar"><span class="fa fa-wrench red"></span> Modificar</span>
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_crear_filtros"> <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="limpiar_filtros"> <span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo Solicitud</td>
            <td>Solicitante</td>
            <td>Fecha Inicio</td>
            <td>Programa</td>
            <td>Asignatura</td>
            <td>No. Estudiantes</td>
            <td style="width:150px">Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="tablausu col-md-12 <?php echo !($admin || $adm_bib || $aux_bib) && empty($id) ? '' : 'oculto' ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="nueva_solicitud">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/libro-a-tu-clase.png" alt="...">
              <span id="titulo_evento" class="btn form-control btn-Efecto-men">Libro a tu clase</span>
            </div>
          </div>
        </div>
        <div id="nueva_sol_capa">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/presentation.png" alt="...">
              <span id="titulo_evento" class="btn form-control btn-Efecto-men">ACADEMIA UNIQUEST</span>
            </div>
          </div>
        </div>
        <div id="listado_solicitudes">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn  form-control btn-Efecto-men" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="En esta opción puedes verificar el estado de tus solicitudes, ademas te permite añadir información adicional a las solicitudes que tienes activas.">Mis Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

<form id="form_agregar_solicitud" method="post">
  <div class="modal fade scroll-modal" id="modal_agregar_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Solicitud</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <?php if ($admin || $adm_bib || $aux_bib) { ?>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin sin_focus" name="solicitante" id='txt_solicitante'>
                  <span class="input-group-addon pointer" id='btn_solicitante' style='background-color:white'><span class='fa fa-search red'></span> Solicitante</span>
                </div>
              </div>
            <?php } ?>
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
              <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha de Solicitud" type="text" value="" required="true" name="fecha_prestamo">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div class="clearfix"></div>
            <input type="number" class="form-control" name="celular" id="txt_numero_celular" placeholder="Numero de Celular">
            <div class="clearfix"></div>
            <div class="agro agrupado" id="niveles_bib_cap">
              <div class="input-group">
                <select name="capacitaciones" class="form-control capa_select">
                  <option value="">Niveles</option>
                </select>
                <span class="input-group-addon pointer" id="agregarCapa" style="	background-color:white" title="Agregar Capacitación"><span class="fa fa-plus-circle red"></span></span>
                <span class="input-group-addon pointer" id="removerCapa" style="	background-color:white" title="Eliminar Capacitación"><span class="fa fa-minus-circle red"></span></span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group">
                  <span class="input-group-addon">Hora de Inicio</span>
                  <input class="form-control time sin_focus" type="text" name="hora_entrega" id="hora_inicio">
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group">
                  <span class="input-group-addon">Hora de Fin</span>
                  <input class="form-control time sin_focus" type="text" name="hora_retiro" id="hora_fin">
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <select name="id_materia" id="id_materia" class="form-control cbxmaterias">
              <option value="">Seleccione Materia</option>
            </select>
            <div class="clearfix"></div>
            <div class="agro agrupado" id="tematicas_bib_lib">
              <div class="input-group">
                <select name="libros" id="libro_select" class="form-control ">
                  <option value="">Temática / Libro</option>
                </select>
                <span class="input-group-addon pointer" id="agregarLibro" style="	background-color:white" title="Agregar Temática / Libro"><span class="fa fa-plus-circle red"></span></span>
                <span class="input-group-addon pointer" id="removerLibro" style="	background-color:white" title="Eliminar Tematoca / Libro"><span class="fa fa-minus-circle red"></span></span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6" style="padding: 0px;">
              <div class="input-group" style="width: 100%">
                <select name="id_bloque" id="bloque" required class="form-control cbxbloque">
                  <option value="">Seleccione Bloque</option>
                </select>
              </div>
            </div>
            <div class="col-md-6" style="padding: 0px;">
              <div class="input-group" style="width: 100%">
                <select name="id_salon" id="salon" required class="form-control cbxsalon">
                  <option value="">Seleccione Salon</option>
                </select>
              </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_estudiantes" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE PERSONAS</td>
                    <td class="sin-borde text-right border-left-none" colspan="4">
                      <span class="btn btn-default btnAgregar" id="agregar_estudiantes">
                        <span class="fa fa-plus red"></span> Agregar persona</span>
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
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_gestionar" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Gestionar Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_auxiliares_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE AUXILIARES</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <span class="btn btn-default btnAgregar" id="agregar_auxiliar">
                    <span class="fa fa-plus red"></span> Agregar Auxiliar </span>
              </tr>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">Ver</td>
                <td>Nombre Auxiliar</td>
                <td>Cedula</td>
                <td>Fecha de registro</td>
                <td>Carga</td>
                <td class="opciones_tbl_btn">Acción</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btn_save_aux"><span class="fa fa-arrow-right"></span> Siguiente</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<form id="form_buscar_empleado" method="post">
  <div class="modal fade" id="modal_buscar_empleado" role="dialog">
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
                <input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del empleado">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_empleado_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA EMPLEADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre Completo</td>
                    <td>Identificacion</td>
                    <td>Carga</td>
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
          <button type="button" class="btn btn-default active" id="btn_cerrar_aux"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_carga" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button class="close" data-dismiss="modal">X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-calendar"></span> Calendario de Carga</h3>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <ul class="nav nav-pills">
            <li><span class="fa fa-circle" style="color: #005900;"></span> Libros a tu clase </li>
            <br>
            <li><span class="fa fa-circle" style="color: #1c88f0;"></span> Academia Uniquest </li>
          </ul>
        </div>
        <hr>
        <div id="botones_cal">
          <ul class="nav nav-pills">
            <?php if ($admin || $adm_bib || $aux_bib) { ?>
              <li>
                <span class="btn btn-default" data-toggle="modal" data-target="#modal_filtros_calendario"><span class="fa fa-filter red"></span> Filtrar</span>
              </li>
              <li>
                <span class="btn btn-default" id="btn_semana"><span class="fa fa-calendar-minus-o red"></span> Semana</span>
              </li>
              <li>
                <span class="btn btn-default" id="btn_mes"><span class="fa fa-calendar red"></span> Mes</span>
              </li>
              <li>
                <span class="btn btn-default" id="btn_ano"><span class="fa fa-calendar-plus-o red"></span> Año</span>
              </li>
            <?php } ?>
          </ul>
        </div>
        <div id="calendario"></div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <div>
              <tr>
                <th class="nombre_tabla" colspan="2">Información de la Solicitud</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <?php if ($admin  || $adm_bib || $aux_bib) { ?>
                    <button class="btn btn-default btnEncuesta"><span class="fa fa-inbox red"></span> Encuestas </button>
                    <button class="btn btn-default btnLog"> <span class="fa fa-history red"></span> Historial </button>
                    <span id='cont_btn_aux'></span>
                  <?php } ?>
                  <button type="button" class="btn btn-default" id="btn_estudiantes"><span class="fa fa-user red"></span> estudiante</button>
                  <span id="cont_btn_tematicas"></span>
                  <span id="cont_btn_capacitaciones"></span>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Solicitante</td>
                <td class="solicitante" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Celular</td>
                <td class="celular" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Tipo de solicitud</td>
                <td class="tipo_solicitud" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Registra</td>
                <td class="fecha_registra" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha Inicio</td>
                <td class="fecha_inicio" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Fin</td>
                <td class="fecha_fin" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Bloque</td>
                <td class="id_bloque" colspan="2"></td>
                <td class="ttitulo" colspan="2">Salon</td>
                <td class="id_salon" colspan="2"></td>
              </tr>
              <tr id="cont_ubicacion">
                <td class="ttitulo" colspan="2">ubicación</td>
                <td class="ubicacion" colspan="6"></td>
              </tr>
          </table>
        </div>
        <div id="tabla_libros" class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_libro_solicitudes" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE LIBROS</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <?php if ($admin || $adm_bib || $aux_bib) { ?>
                    <span class="btn btn-default btnAgregar" id="agregar_libro_cod">
                      <span class="fa fa-plus red"></span> Agregar Libro </span>
                  <?php } ?>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Codigo de barras</td>
                <td>Nombre del libro</td>
                <td>Asignado a</td>
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

<div class="modal fade" id="modal_auxiliares_mod" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Modificar Auxiliares</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_auxiliares_mod" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE AUXILIARES</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <span class="btn btn-default btnAgregar" id="agregar_auxiliar_mod">
                    <span class="fa fa-plus red"></span> Agregar Auxiliar </span>
              </tr>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">Ver</td>
                <td>Nombre Auxiliar</td>
                <td>Cedula</td>
                <td>Fecha de registro</td>
                <td>Carga</td>
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

<div class="modal fade" id="modal_estudiantes_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-user"></span> Estudiantes</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estudiante_solicitudes" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE PERSONAS</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <span class="btn btn-default btnAgregar" id="agregar_estudiantes_nuevos">
                    <span class="fa fa-plus red"></span> Agregar personas </span>
              </tr>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">Codigo</td>
                <td>Nombre Completo</td>
                <td>Identificación</td>
                <td>Fecha de registro</td>
                <td>Correo</td>
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

<div class="modal fade" id="modal_preparacion_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-book"></span> Libros Asignados</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_preparacion_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE LIBROS</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <?php if ($admin || $adm_bib || $aux_bib) { ?>
                    <span class="btn btn-default btnAgregar" id="agregar_libro_cod">
                      <span class="fa fa-plus red"></span> Agregar Libro </span>
                  <?php } ?>
                </td>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Codigo de barras</td>
                <td>Nombre del libro</td>
                <td>Asignado a</td>
                <td class="opciones_tbl_btn">Acción</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btn_save"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_libro" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-book"></span> Detalle del libro</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed" id="tabla_detalle_libro">
            <div>
              <tr>
                <th class="nombre_tabla" colspan="6">Información del libro</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <?php if ($admin || $adm_bib || $aux_bib) { ?>
                    <button class="btn btn-default btnAsig"> <span class="fa fa-history red"></span> Historial </button>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre del libro</td>
                <td class="nombre_libro" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Registra</td>
                <td class="fecha_registra" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Codigo de Barras</td>
                <td class="codigo" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="solicitante" colspan="6"></td>
              </tr>
              <tr id="estudiante_a">
                <td class="ttitulo" colspan="2">Estudiante asignado</td>
                <td class="estudiante_asignado" colspan="6"></td>
              </tr>
              <tr id="retiro_l">
                <td class="ttitulo" colspan="2">Razón Retiro</td>
                <td class="nota_retiro" colspan="2"></td>
                <td class="ttitulo" colspan="2">Retirado por</td>
                <td class="persona_retira"></td>
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

<div class="modal fade" id="modal_estudiantes_asignacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-check"></span> Asignar estudiante</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estudiante_asignacion" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE ESTUDIANTES</td>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Nombre Completo</td>
                <td>Identificación</td>
                <td>Fecha de registro</td>
                <td>Correo</td>
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

<div class="modal fade" id="modal_tematicas_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-book"></span> Libros / Tematicas</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_tematicas_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE LIBROS O TEMATICAS</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <span class="btn btn-default btnAgregar" id="agregar_libro_nuevo">
                    <span class="fa fa-plus red"></span> Agregar Libro </span>
              </tr>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Nombre Libro</td>
                <td>Solicitante</td>
                <td>Fecha de registro</td>
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

<div class="modal fade" id="modal_capacitaciones_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-group"></span> Niveles</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_capacitaciones_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE CAPACITACIONES</td>
                <td class="sin-borde text-right border-left-none" colspan="5">
                  <a class="btn btn-default" id="btn_descargar_reseña">
                    <span class="fa fa-download red"></span> Descargar </a>
              </tr>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">Nivel</td>
                <td>Nombre</td>
                <td>Duración (min)</td>
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

<div class="modal fade" id="modal_historial_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estado_solicitud" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
              <tr class="filaprincipal ">
                <td>Estado</td>
                <td>Bloque</td>
                <td>Salon</td>
                <td>Fecha</td>
                <td>Modificador</td>
                <td>Observaciones</td>
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

<form id="form_agregar_libro_nuevo" method="post">
  <div class="modal fade" id="modal_agregar_libro_nuevo" role="dialog">
    <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Agregar Temática / Libros</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input id='libro_input_new' name="nombre_libro" class="form-control" placeholder="Ingrese tematica o el libro que desea agregar" required>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form id="form_agregar_libro_cod" method="post">
  <div class="modal fade" id="modal_agregar_libro_cod" role="dialog">
    <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Agregar Libros</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input id='libro_codigo' name="codigo_de_barra" class="form-control" placeholder="Ingrese el codigo de barra del libro" required>
            <input id="nombre_libro_cod" name="nombre_libro" class="form-control" placeholder="Ingrese el nombre del libro" required>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form id="form_agregar_libro" method="post">
  <div class="modal fade" id="modal_agregar_libro" role="dialog">
    <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Agregar Temática / Libros</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input id='libro_input' required class="form-control" placeholder="Ingrese tematica o el libro que desea agregar">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form id="form_buscar_empleado_sol" method="post">
  <div class="modal fade" id="modal_buscar_empleado_sol" role="dialog">
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
                <input id='txt_dato_buscar_sol' class="form-control" placeholder="Ingrese identificación o nombre del empleado">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_empleado_busqueda_sol" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA EMPLEADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
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

<form id="form_buscar_estudiante" method="post">
  <div class="modal fade" id="modal_buscar_estudiante" role="dialog">
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
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btn_nuevo_estudiante"><span class='fa fa-user-plus red'></span>
                    Nuevo
                  </button>
                </span>
                <input id='txt_est_buscar' class="form-control" placeholder="Ingrese identificación o nombre del estudiante">
                <span class="input-group-addon">
                  <input type="radio" name="tabla" id="empleado" value="personas"> Empleado
                  <input type="radio" name="tabla" id="estudiante" value="visitantes"> Estudiante
                </span>
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_estudiantes_busqueda" cellspacing="0" width="100%">
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

<form id="form_modificar_solicitud" method="post">
  <div class="modal fade scroll-modal" id="modal_modificar_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-calendar"></span> Modificar Solicitud</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
              <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha de Prestamo" type="text" value="" required="true" name="fecha_prestamo">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group">
                  <span class="input-group-addon">Hora de Inicio</span>
                  <input class="form-control time" type="text" name="hora_entrega">
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group">
                  <span class="input-group-addon">Hora de Fin</span>
                  <input class="form-control time" type="text" name="hora_retiro">
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <div class="col-md-6" style="padding: 0px;">
              <div class="input-group" style="width: 100%">
                <select name="id_bloque" required class="form-control cbxbloque">
                  <option value="">Seleccione Bloque</option>
                </select>
              </div>
            </div>
            <div class="col-md-6" style="padding: 0px;">
              <div class="input-group" style="width: 100%">
                <select name="id_salon" required class="form-control cbxsalon">
                  <option value="">Seleccione Salon</option>
                </select>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="agro agrupado" id="niveles_capa">
              <div class="input-group">
                <select name="capacitaciones" id="capa_select" class="form-control">
                  <option value="">Capacitaciones</option>
                </select>
                <span class="input-group-addon pointer" id="agregarCapaMod" style="	background-color:white" title="Agregar Capacitación"><span class="fa fa-plus-circle red"></span></span>
                <span class="input-group-addon pointer" id="removerCapaMod" style="	background-color:white" title="Eliminar Capacitación"><span class="fa fa-minus-circle red"></span></span>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>

        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_nuevo_estudiante" role="dialog">
  <div class="modal-dialog">
    <form id="form_nuevo_estudiante" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-user-plus"></span> Registro de Estudiante</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <h6 class="ttitulo">
              <span class="glyphicon glyphicon-indent-left"></span>
              Datos del Solicitante
            </h6>
            <select name="tipo_identificacion" id="cbxtipoIdentificacion" required class="form-control  cbxtipoIdentificacion">
            </select>
            <input min="1" type="number" name="identificacion" id="txtIdentificacion" class="form-control inputt" placeholder="No. Identificación" required>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="apellido" id="txtApellido" class="form-control inputt2" placeholder="Primer Apellido" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2" placeholder="Segundo Apellido" required>
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2" placeholder="Segundo Nombre">
              </div>
            </div>
            <input min="1" type="number" name="celular" id="txtCelular" class="form-control" placeholder="Celular" required="">
            <input type="email" name="correo" id="txtCorreo" class="form-control" placeholder="Correo" required>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_estudiantes_entrega" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-user"></span> Estudiantes</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estudiante_entrega" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE PERSONAS</td>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Nombre Completo</td>
                <td>Identificación</td>
                <td>Fecha de registro</td>
                <td>Correo</td>
                <td class="opciones_tbl_btn">Acción</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active"><span class="glyphicon glyphicon-envelope"></span>Enviar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_crear_filtros" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select name="id_tipo_solicitud" class="form-control cbx_tipos">
            <option value="">Filtrar por Tipo</option>
          </select>
          <select name="id_estado_solicitud" class="form-control cbx_estados">
            <option value="">Filtrar por Estado</option>
          </select>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
              <input class="form-control sin_margin" value="" type="date" name="fecha_inicial">
            </div>
          </div>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
              <input class="form-control sin_margin" value="" type="date" name="fecha_final">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active" id="btn_filtrar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_filtros_calendario" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <?php if ($admin || $adm_bib) { ?>
            <select name="auxiliar" class="form-control cbx_aux_bib">
            </select>
          <?php } ?>
          <select name="auxiliar" class="form-control cbx_tipo_bib">
            <option value="">Seleccione tipo de visualización</option>
            <option value="asig">Por asignaciones</option>
            <option value="sol">Por Solicitudes</option>
          </select>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
              <input class="form-control sin_margin" value="" type="date" id="fecha_ini_cal">
            </div>
          </div>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
              <input class="form-control sin_margin" value="" type="date" id="fecha_fin_cal">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active" id="btn_filtrar_cal"><span class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_historial_libro" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_asignaciones_libro" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE ASIGNACIONES</td>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Estudiante</td>
                <td>Fecha</td>
                <td>Modificador</td>
                <td>Observaciones</td>
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


<div class="modal fade" id="modal_seleccion_carga" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X</button>
        <h3 class="modal-title"><span class="fa fa-mouse-pointer"></span> Seleccionar Carga</h3>
      </div>
      <div class="modal-body">
        <div class="row" style="width: 100%">
          <div class="col-sm-8 col-sm-offset-2">
            <span class="input-group-btn">
              <select name="id_carga" id="acciones_auxiliar" class="form-control" style="width: 160px;"></select>
            </span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="save_accion" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_auxiliar" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-user"></span> Detalle del auxiliar</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed" id="tabla_detalle_auxiliar">
            <div>
              <tr>
                <th class="nombre_tabla" colspan="6">Información del auxiliar</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <?php if ($admin || $adm_bib || $aux_bib) { ?>
                    <button class="btn btn-default btn_log_aux"> <span class="fa fa-history red"></span> Historial </button>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre del Auxiliar</td>
                <td class="nombre_aux" colspan="2"></td>
                <td class="ttitulo" colspan="2">Identificación</td>
                <td class="identificacion_aux" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Carga</td>
                <td class="carga" colspan="6"></td>
              </tr>
              <tr id="retiro_aux">
                <td class="ttitulo" colspan="2">Razón Retiro</td>
                <td class="razon_retiro" colspan="2"></td>
                <td class="ttitulo" colspan="2">Retirado por</td>
                <td class="persona_retiro"></td>
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

<div class="modal fade" id="modal_historial_auxiliar" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="margin-bottom:20px;">
          <table class="table table-bordered table-hover table-condensed" id="tabla_asignaciones_auxiliar" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE CARGAS AUXILIAR</td>
              <tr class="filaprincipal ">
                <td class="opciones_tbl">No.</td>
                <td>Auxiliar</td>
                <td>Fecha</td>
                <td>Carga</td>
                <td>Modificador</td>
                <td>Observaciones</td>
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

<?php if ($admin || $adm_bib) { ?>
  <div class="modal fade" id="administrar_biblioteca" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button class="close" type="button" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <nav class="navbar navbar-default" id="nav_admin_bib">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer active" id="admin_perm"><a><span class="fa fa-edit red"></span> Permisos</a></li>
                <li class="pointer" id="admin_turn"><a><span class="fa fa-clock-o red"></span> Turnos</a></li>
                </li>
              </ul>
            </div>
          </nav>
          <div id="container_admin_bib">
            <div class="form-group col-md-6">
              <div class="agro agrupado sin_margin">
                <select required class="form-control cbx_aux_bib"></select>
              </div>
            </div>
            <table class="table table-bordered table-hover table-condensed" id="tabla_procesos_bib" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td class="nombre_tabla">TABLA PROCESOS BIBLIOTECA</td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Nombre</td>
                  <td>Descripción</td>
                  <td>Acción</td>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div id="container_turnos_bib" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_turnos_bib" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th class="nombre_tabla">TABLA DE TURNOS</th>
                  <td class="sin-borde text-right border-left-none" colspan="6">
                    <?php if ($admin || $adm_bib) { ?>
                      <button class="btn btn-default btn_new_turn"> <span class="fa fa-plus red"></span> Nuevo turno</button>
                    <?php } ?>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td>Hora Entrada</td>
                  <td>Hora Salida</td>
                  <td>Acción</td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="administrar_estados_biblioteca" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button class="close" type="button" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Proceso</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="container_admin_proc">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estados_bib" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA ESTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td style="width:150px">Accion</td>
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

  <div class="modal fade" id="modal_negar_solicitud" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button class="close" type="button" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Causas de la negación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="container_causas_negar">
            <table class="table table-bordered table-hover table-condensed" id="tabla_causas_negar" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA DE CAUSAS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td style="width:150px">Accion</td>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" id="btn_negar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <form id="form_agregar_turno" method="post">
    <div class="modal fade" id="modal_agregar_turno" role="dialog">
      <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-clock-o"></span> Crear nuevo turno</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group">
                    <span class="input-group-addon">Hora de Entrada</span>
                    <input class="form-control time" type="text" name="hora_entrada">
                  </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group">
                    <span class="input-group-addon">Hora de Salida</span>
                    <input class="form-control time" type="text" name="hora_salida">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
              Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="administrar_turnos_biblioteca" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button class="close" type="button" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-calendar"></span> Asignar Turno</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="container_admin_proc">
            <table class="table table-bordered table-hover table-condensed" id="tabla_empleados_bib" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA EMPLEADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td>Identificación</td>
                  <td style="width:150px">Accion</td>
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

  <div class="modal fade" id="modal_encuestas" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-inbox"></span> Encuestas</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_encuesta_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ENCUESTAS</td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Programa</td>
                  <td>Rol Principal</td>
                  <td>Pregunta 1</td>
                  <td>Pregunta 2</td>
                  <td>Pregunta 3</td>
                  <td>Pregunta 4</td>
                  <td>Autorización</td>
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

  <form id="form_ubicacion_capa" method="post">
    <div class="modal fade" id="modal_ubicacion_capa" role="dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-map-marker"></span> Ubicación</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="clearfix"></div>
              <div class="agro agrupado">
                <div class="input-group" style="width: 100%">
                  <select name="id_recurso" id="recurso_cap" required class="form-control cbxrecurso">
                    <option value="">Seleccione Recurso</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <div id="cont_bloque_salon">
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group" style="width: 100%">
                    <select name="id_bloque" id="bloque_cap" class="form-control cbxbloque_cap">
                      <option value="">Seleccione Bloque</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group" style="width: 100%">
                    <select name="id_salon" id="salon_cap" class="form-control cbxsalon">
                      <option value="">Seleccione Salon</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
<?php } ?>

<div class="modal fade" id="modal_agregar_capacitacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Agregar Capacitación</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_capacitaciones_bib" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <th class="nombre_tabla">TABLA DE CAPACITACIONES</td>
            </tr>
            <tr class="filaprincipal">
              <td>Nivel</td>
              <td>Nombre</td>
              <td>Duración (min)</td>
              <td>Acción</td>
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

<div class="modal fade" id="modal_consolidado_biblioteca" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button class="close" type="button" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bar-chart"></span> Consolidado de Encuestas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_con_bib">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" id="con_bib_lib"><a><span class="fa fa-circle" style="color: #005900;"></span> Libros a tu clase</a></li>
              <li class="pointer" id="con_bib_cap"><a><span class="fa fa-circle" style="color: #1c88f0;"></span> Academia Uniquest</a></li>
              </li>
            </ul>
          </div>
        </nav>
        <div id="container_bib_lib">
          <table class="table table-bordered table-hover table-condensed" id="tabla_con_bib_lib" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla">TABLA CONSOLIDADO ENCUESTAS</td>
              </tr>
              <tr class="filaprincipal ">
                <td>Roles</td>
                <td>Programas</td>
                <td>Departamentos</td>
                <td>Pregunta 1 (Prom.)</td>
                <td>Pregunta 2 (Prom.)</td>
                <td>Pregunta 3 (Prom.)</td>
                <td>Pregunta 4 (Prom.)</td>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div id="container_bib_cap" class="oculto">
          <table class="table table-bordered table-hover table-condensed" id="tabla_con_bib_cap" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla">TABLA CONSOLIDADO ENCUESTAS</td>
              </tr>
              <tr class="filaprincipal ">
                <td>Roles</td>
                <td>Programas</td>
                <td>Departamentos</td>
                <td>Pregunta 1 (Prom.)</td>
                <td>Pregunta 2 (Prom.)</td>
                <td>Pregunta 3 (Prom.)</td>
                <td>Pregunta 4 (Prom.)</td>
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

<div class="modal fade" id="modal_consolidado_roles" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-user"></span> Consolidado Roles</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_roles" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <th class="nombre_tabla">TABLA DE ROLES</td>
            </tr>
            <tr class="filaprincipal">
              <td>Rol</td>
              <td>No.</td>
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

<div class="modal fade" id="modal_consolidado_programas" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-graduation-cap"></span> Consolidado Programas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_programas" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <th class="nombre_tabla">TABLA DE PROGRAMAS</td>
            </tr>
            <tr class="filaprincipal">
              <td>Programa</td>
              <td>No.</td>
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

<div class="modal fade" id="modal_consolidado_departamentos" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-university"></span> Consolidado Departamentos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_departamentos" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <th class="nombre_tabla">TABLA DE DEPARTAMENTOS</td>
            </tr>
            <tr class="filaprincipal">
              <td>Departamento</td>
              <td>No.</td>
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

<script type="text/javascript">
  let startDate = new Date();
  startDate.setDate(startDate.getDate() + <?php echo $dias ?>)
  $(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    startDate,
    todayBtn: true,
    maxView: 4,
    minView: 2,
    daysOfWeekDisabled: [0],

  });
  $(".time").datetimepicker({
    formatViewType: 'time',
    fontAwesome: true,
    autoclose: true,
    startView: 1,
    maxView: 1,
    minView: 0,
    minuteStep: 30,
    format: 'hh:ii',

  });
  $(".time").datetimepicker('setHoursDisabled', [0, 1, 2, 3, 4, 5, 6, 21, 22, 23])
</script>
<script>
  $(document).ready(function() {
    inactivityTime();
    listar_solicitud('<?php echo $id ?>');
    listar_bloques();
    Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
    Cargar_parametro_buscado_aux(125, ".cbx_estados", "Seleccione Estado");
    Cargar_parametro_buscado_aux(126, ".cbx_tipos", "Seleccione Tipo");
    pintar_materias_docente('<?php echo $_SESSION["persona"] ?>');
  });
</script>