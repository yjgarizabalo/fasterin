<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Csep") {
  $sw = true;
  $sw_super = true;
}
?>
<script>
  var base_url = '<?php echo base_url(); ?>';
</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/MyStyle.css">
<div class="container col-md-12 text-center" id="inicio-user">

  <!-- Trabajar ahora sobre esta tabla para ver los aprobados o no y poner el boton de descarga segun su estado -->

  <div class="tablausu listado_solicitudes col-md-12 text-left <?php if (!$sw && !$id) echo 'oculto'; ?>">
    <div class="table-responsive col-sm-12 col-md-12  tablauser">
      <p class="titulo_menu pointer" id="regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="3" class="nombre_tabla"> TABLA ASCENSOS
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="6">
              <span class="btn btn-default" id="btn_aplicar_filtros">
                <span class="fa fa-filter red"></span> Filtrar
              </span>
              <span class="btn btn-default" id="limpiar_filtros_ascensos">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo de Ascenso</td>
            <td>Docente</td>
            <td>Fecha de registro</td>
            <td>Estado</td>
            <td style='width:100px;'>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Hasta aqui -->

  <div class="tablausu col-md-12 <?php if ($sw || $id) echo 'oculto'; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="nueva_solicitud" class="thumbnail">
          <div class="caption">
            <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
            <span class="btn form-control">Nueva Solicitud</span>
          </div>
        </div>
        <div class="" id="listado">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control">Estados Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
    </div>
  </div>

  <div class="modal fade" id="modal_nueva_solicitud" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-user-plus"></span> Nueva Solicitud de Ascenso</h3>
        </div>
        <div class="modal-body" id="bodymodal">

          <div class="opciones__container">

            <div class="opcion__cont" id="type_docencia" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Solicitud para Docencia">
              <img src="<?php echo base_url() ?>/imagenes/docencia.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Docencia</span>
            </div>

            <div class="opcion__cont" id="type_investigacion" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Solicitud para Investigación">
              <img src="<?php echo base_url() ?>/imagenes/investigacion.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Investigación</span>
            </div>

            <div class="opcion__cont" id="type_extension" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Solicitud para Extensión">
              <img src="<?php echo base_url() ?>/imagenes/extension.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Extensión</span>
            </div>

          </div>

        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
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
              <tr>
                <th class="nombre_tabla" colspan="2">Información de la Solicitud</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <button type="button" class="btn btn-default" id="btn_log"><span class="fa fa-history red"></span> Historial</button>
                  <button type="button" class="btn btn-default" id="btn_archivos"><span class="fa fa-file red"></span> Adjuntos</button>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Docente</td>
                <td class="docente" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Tipo de solicitud</td>
                <td class="tipo_solicitud" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Registra</td>
                <td class="fecha_registra" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Estado</td>
                <td class="estado" colspan="6"></td>
              </tr>
              <tr id="cont_exp_cvlac">
                <td class="ttitulo" colspan="2">Años de experiencia</td>
                <td class="experiencia" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL CVLac</td>
                <td class="cvlac" colspan="2"></td>
              </tr>
              <tr id="cont_scopus">
                <td class="ttitulo" colspan="2">Indice Scopus</td>
                <td class="indice" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL Scopus</td>
                <td class="url_scopus" colspan="2"></td>
              </tr>
              <tr id="cont_colciencias">
                <td class="ttitulo" colspan="2">Categoria COLCIENCIAS</td>
                <td class="colciencias" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL COLCIENCIAS</td>
                <td class="url_colciencias" colspan="2"></td>
              </tr>
              <tr id="cont_cargo">
                <td class="ttitulo" colspan="2">Cargo Actual</td>
                <td class="cargo_actual" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cargo Nuevo</td>
                <td class="cargo_nuevo" colspan="2"></td>
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

  <div class="modal fade" id="modal_items_solicitud">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-user-plus"></span> Información para Ascenso</h3>
        </div>
        <div class="modal-body" id="modalbody">
          <div id="container_informacion_general" style="padding: 20px;">
            <div class="row">
            </div>
          </div>
          <div id="container_adjuntos_solicitud" style="padding: 20px;" class="">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <!-- <button type="button" class="btn btn-danger" id="btn_agregar_informacion"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button> -->
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Modal ára informacion general al dar click en ajuntar - Ascensos -->

  <div class="modal fade" id="modal_gen_info">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-user-plus"></span> Información para Ascenso</h3>
        </div>
        <div class="modal-body" id="modalbody">

          <div id="container_informacion_general" style="padding: 20px;">
  
            <div class="row">
              <div class="col-lg-8 col-md-offset-2">
                <input type="number" class="form-control" name="experiencia_doc" id="txt_experiencia_doc" placeholder="Años de experiencia" data-toggle="popover" data-trigger="hover">
                <select name="id_colciencias" id="colciencias_select" class="form-control cbxcolciencias">
                  <option value="">Seleccione Categoria COLCIENCIAS</option>
                </select>
                <input type="url" name="url_colciencias" id="colciencias_url" class="form-control" placeholder="URL de su categoria en COLCIENCIAS" data-toggle="popover" data-trigger="hover">
                <input type="number" class="form-control" name="indice_scopus_value" id="txt_indice_scopus_value" placeholder="Indice scopus" data-toggle="popover" data-trigger="hover">
                <input type="url" class="form-control" name="indice_scopus" id="txt_indice_scopus" placeholder="URL de su indice scopus" data-toggle="popover" data-trigger="hover">
                <input type="url" class="form-control" name="cvlac" id="txt_cvlac" placeholder="URL de CVLac" data-toggle="popover" data-trigger="hover">
                <div class="agro agrupado">
                  <div class="input-group">
                    <input type="text" class="form-control sin_margin sin_focus" name="cargo_actual" id='txt_cargo_actual'>
                    <span class="input-group-addon pointer" id="btn_cargo_actual" style="background-color: white;"><span class="fa fa-search red"></span> Escalafón actual</span>
                  </div>
                </div>
                <div class="agro agrupado">
                  <div class="input-group">
                    <input type="text" class="form-control sin_margin sin_focus" name="cargo_nuevo" id='txt_cargo_nuevo'>
                    <span class="input-group-addon pointer" id="btn_cargo_nuevo" style="background-color: white;"><span class="fa fa-search red"></span> Escalafón Nuevo</span>
                  </div>
                </div>
                <div class="agrupado">
                  <button id="btn_addExp" class="btn-primary btn"><span class="fa fa-plus-square"></span> Agregar Experiencia laboral</button>
                </div>
              </div>
            </div>

          </div>

          <div id="container_adjuntos_solicitud" style="padding: 20px;" class="">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger" id="btn_agregar_informacion"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_formacion_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-book"></span> Formación</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_formacion_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE FORMACIÓN</td>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    <span class="btn btn-default agregar_formacion" id="agregar_formacion">
                      <span class="fa fa-plus red"></span>
                      Agregar Fomación
                    </span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">Ver.</td>
                  <td>Titulo</td>
                  <td>Nivel de formación</td>
                  <td>Fecha de registro</td>
                  <td>Acción</td>
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

  <div class="modal fade" id="modal_archivos_item" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">          
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_archivos_item" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ARCHIVOS</td>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    <span class="btn btn-default btn_adjuntar_archivo" id="agregar_archivo">
                      <span class="fa fa-plus red"></span>
                      Adjuntar archivos
                    </span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">Ver.</td>
                  <td>Archivo</td>
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

  <form id="form_agregar_formacion" method="post">
    <div class="modal fade scroll-modal" id="modal_agregar_formacion" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Agregar Formacion</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <select name="id_formacion" id="formacion_select" required class="form-control cbxformacion">
                <option value="">Seleccione Formación</option>
              </select>
              <select name="nivel_ingles" id="ingles_select" class="form-control cbxingles">
                <option value="">Seleccione Categoria</option>
              </select>
              <input type="text" class="form-control" name="nombre_formacion" id="txt_nombre_formacion" placeholder="Nombre del titulo">
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-danger" id="btn_crear_formacion"><span class="fa fa-arrow-right"></span> Continuar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_enviar_archivos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <form class="dropzone needsclick dz-clickable" id="Subir" action="">
            <input type="hidden" name="id" id="id_solicitud" val="0">
            <div class="dz-message needsclick">
              <p>Arrastre archivos o presione clic aquí</p>
            </div>
          </form>
        </div>
        <div class="modal-footer" id="footer_archivos">
        </div>
      </div>
    </div>
  </div>

  <form id="form_buscar_cargo" method="post">
    <div class="modal fade" id="modal_buscar_cargo" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Escalafón</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_buscar' class="form-control" placeholder="Ingres nombre del cargo">
                  <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_cargo_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA ESCALAFONES</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>No.</td>
                      <td>Escalafón</td>
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
                  <td>No.</td>
                  <td>Estado</td>
                  <td>Fecha</td>
                  <td>Persona Registra</td>
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
            <select name="id_tipo_solicitud" class="form-control cbxtipos" , id="id_tipo_select">
              <option value="">Filtrar por Tipo</option>
            </select>
            <select name="id_estado_solicitud" class="form-control cbxestados" id="id_estado_select">
              <option value="">Filtrar por Estado</option>
            </select>
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
                <input class="form-control sin_margin" value="" type="date" name="fecha_inicial" id="fecha_inicial">
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
                <input class="form-control sin_margin" value="" type="date" name="fecha_final" id="fecha_final">
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

</div>

<script>
  $(document).ready(function() {
    inactivityTime();
    listar_solicitudes(<?php echo $id ?>);
    recibir_archivos();
    $('[data-toggle="popover"]').popover();
  })
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>