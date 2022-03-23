<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub" || $_SESSION["perfil"] == "Per_Admin_Tal") {
  $sw = true;
  $sw_super = true;
}
?>
<style>
  .evidencias_unique{
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #fff;
    border-radius: 5px;
    background-color: #ebebeb;
  }
  #evidencia_bon{
    background-color: #ffffff;
  }
</style>


<div class="container col-md-12 text-center" id="inicio-user">
  <!-- Modal del Nuevo Pago "pago_papers" -->
  <div class="modal fade scroll-modal" id="modal_nuevo_pago" role="dialog">
    <div class="modal-dialog modal-lg">
      <form id="form_solicitar_pago" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-dollar"></span> Nuevo Pago</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">
              <div class="alert alert-info">
                <p class="text-justify">
                  <strong><span class="fa fa-warning"></span> Aviso: </strong>Los pagos para cualquiera de los autores no deben haber superado 150smlv en un año fiscal, sí alguno de los autores llego a este valor los demás coautores no podrán efectuar la solicitud, los pagos serán descontables de la bonificación solicitada por el trabajo conforme al acuerdo vigente de plan de estímulo por producción en investigación. El pago estará sujeto a disponibilidad presupuestal.
                </p>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <label class="input-group-btn">
                    <span class="btn btn-default">
                      <span class="fa fa-calendar red"></span>
                      Fecha máxima de pago
                    </span>
                  </label>
                  <input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Fecha máxima de pago." id="fecha_maxima_pago" name="fecha_maxima_pago" class="form-control" required placeholder='Fecha máxima de pago'>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer btn_buscar_autor" style='background-color:white'><span class='fa fa-search red'></span> Autores</span>
                  <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Nombre de Artículo" class="form-control sin_margin sin_focus" name="nombre_articulo" id="nombre_articulo" placeholder="Nombre del artículo..." required>
                  <span class="input-group-addon pointer btn_buscar_art" style='background-color:white'><span class='fa fa-check red'></span> Nombre del Artículo</span>
                </div>
              </div>
              <select name="tipo_id" id="tipo_id" required class="form-control" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione si es Revista o Conferencia">
                <option disabled selected> Seleccione si es Revista o Conferencia</option>
              </select>
              <select name="nombre_revista" id="nombre_revista" required class="form-control" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Elija el nombre de la revista">
                <option value="cambiar" class="cambiar" disabled selected>Nombre de Revista</option>
              </select>
              <select name="cuartil_id" id="cuartil_id" required class="form-control" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Despliegue el menú y seleccione el cuartil.">
                <option disabled selected> Seleccione cuartil</option>
              </select>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Aquí puede buscar el Codigo Sap requerido." class="form-control sin_margin sin_focus" data-codsap_id="" name="num_sap" id="num_sap" placeholder="Buscar codigo SAP" required>
                  <span class="input-group-addon pointer btn_buscar_codsap" style='background-color:white'><span class='fa fa-search red'></span> COD. SAP</span>
                </div>
              </div>
              <div class="agro agrupado" style="display: flex;">
                <div class="agrupado" style="width: 50%">
                  <div class="input-group ">
                    <label class="input-group-btn"><span class="btn btn-primary">
                        <span class="fa fa-folder-open"></span>Buscar <input name="adj1" type="file" style="display: none;" id="carta_aceptacion"></span>
                    </label>
                    <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Adjunte su carta de aceptación" class="form-control" readonly placeholder='Adjuntar carta de aceptación'>
                  </div>
                </div>
                <div class="agrupado" style="width: 50%">
                  <div class="input-group ">
                    <label class="input-group-btn"><span class="btn btn-primary">
                        <span class="fa fa-folder-open"></span>Buscar <input name="adj2" type="file" style="display: none;" id="adj_rev_cuartil"></span>
                    </label>
                    <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Adjunte revisión de cuartil de la revista o proceedings" class="form-control" readonly placeholder='Adjuntar revisión de cuartil de la revista o proceedings'>
                  </div>
                </div>
              </div>
              <div class="agro agrupado">
                <select name="tipo_pago_select" id="tipo_pago_select" class="form-control">
                  <option value="">Escoger tipo de pago</option>
                </select>
              </div>
              <!-- Trabajando en contenido generado segun tipo de pago elegido -->
              <div id="segun" hidden>
                <div class="agro agrupado">
                  <div class="agrupado" style="display: flex;">
                    <select style="width: 50%;" name="banco_select" id="banco_select" class="form-control" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Aquí puede seleccionar el banco que va a usar para diligenciar el pago.">
                      <option value="">Seleccionar banco</option>
                    </select>
                    <select style="width: 50%;" name="tipo_tarjeta_select" id="tipo_tarjeta_select" class="form-control" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="¿Qué tipo de cuenta va a usar?.">
                      <option value="">Seleccione su tipo de cuenta bancaria</option>
                    </select>
                  </div>
                </div>
                <div class="agro agrupado">
                  <div class="agrupado" style="display: flex;">
                    <div class="input-group">
                      <span class="input-group-addon pointer btn_buscar_moneda" style='background-color:white'><span class='fa fa-search red'></span>Tipo de moneda</span>
                      <input type="text" data-divisa="" data-zeros="" data-moneda_id="" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Tipo de moneda con la cual va a diligenciar el pago." class="form-control sin_margin sin_focus" name="tipo_moneda" id="tipo_moneda" placeholder="" required>
                    </div>
                    <span class="input-group-btn" style="width: 50%">
                      <span>
                        <input disabled type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Valor del pago - solo se aceptan números en este campo" class="form-control rounded input_numerico dinero" style="border-radius: 3px;" name="pago_valor" id="pago_valor" required placeholder="Valor del pago">
                      </span>
                    </span>
                  </div>
                </div>
                <div class="agro-agrupado" style="display: flex;">
                  <div class="agrupado adjs_pagointer" style="width: 50%;">
                    <div class="input-group">
                      <label class="input-group-btn"><span class="btn btn-primary">
                          <span class="fa fa-folder-open"></span>Buscar <input name="adj3" type="file" style="display: none;" id="doc_pago_inter_online"></span>
                      </label><input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Adjunte procedimiento para pagos internacionales online" class="form-control" readonly placeholder='Adjuntar procedimiento para pagos internacionales online'>
                    </div>
                  </div>
                  <div class="agrupado adjs_monedainter" style="width: 50%;">
                    <div class="input-group">
                      <label class="input-group-btn"><span class="btn btn-primary">
                          <span class="fa fa-folder-open"></span>Buscar <input name="adj4" type="file" style="display: none;" id="adj_caso_moneda_inter"></span>
                      </label><input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Adjunte documento en caso de moneda internacional" class="form-control" readonly placeholder='Adjuntar formato de diligenciado de pago en moneda extranjera'>
                    </div>
                  </div>
                </div>
                <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Número de identificación del artículo" class="form-control input_numerico" name="num_identi_articulo" id="num_identi_articulo" placeholder="Número de identificación del artículo">
                <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Link del pago" class="form-control rounded links" style="border-radius: 3px;" name="pago_link" id="pago_link" placeholder="Inserte link del pago">
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Modal buscar articulo -->
  <form id="form_buscar_articulo" method="post">
    <div class="modal fade" id="modal_buscar_articulo" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Artículo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_articulo' name="txt_dato_articulo" class="form-control txt_dato_articulo" required autofocus placeholder="Ingrese nombre del artículo">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_art" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_articulo_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE ARTÍCULOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Nombre del Articulo</td>
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
  <!-- Modal de busqueda de autores -->
  <div class="modal fade" id="modal_buscar_autores" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Lista de autores</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_autor_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Autores</td>
                    <td>Procentaje</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="botton" class="btn btn-danger active btn_createArray"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal de busqueda de codigos SAP -->
  <form id="form_buscar_codsap" method="post">
    <div class="modal fade" id="modal_buscar_codsap" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Codigo SAP</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_codsap' class="form-control txt_dato_codsap" required placeholder="Ingrese el codigo SAP">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_codsap" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_codsap_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Codigo SAP</td>
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
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <!-- Modal busqueda de tipo de moneda -->
  <form id="form_buscar_moneda" method="post">
    <div class="modal fade" id="modal_buscar_moneda" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar tipo de moneda</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_moneda' name="txt_dato_moneda" class="form-control txt_dato_articulo" required autofocus placeholder="Ingrese el tipo de moneda que busca">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_money" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_moneda_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TIPOS DE MONEDA</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Abreviado</td>
                      <td>Moneda</td>
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
  <!-- Fin Pag Pep -->
  <div class="tablausu listado_solicitudes col-md-12 text-left <?php if (!$sw) echo 'oculto'; ?>">
    <div class="table-responsive col-sm-12 col-md-12  tablauser">
      <p class="titulo_menu pointer" id="regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_publicaciones" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="2" class="nombre_tabla"> TABLA PUBLICACIONES
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="5">
              <?php if ($sw) { ?>
                <span class="btn btn-default" id="btn_notificaciones">
                  <span class="badge" id="noti_n" style="background-color: #6e1f7c;"></span> Notificaciones
                </span>
                <span class="btn btn-default" id="btn_administrar">
                  <span class="fa fa-cogs red" id="admin_btn"></span> Administrar
                </span>
                <span class="btn btn-default" id="btn_administrar_revistas">
                  <span class="fa fa-book red"></span> Revistas
                </span>
              <?php } ?>
              <span class="btn btn-default btnModifica" id="modificar_solicitud_ini">
                <span class="fa fa-wrench red"></span> Modificar
              </span>
              <span class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#modal_crear_filtros">
                <span class="fa fa-filter red"></span> Filtrar
              </span>
              <span class="btn btn-default" id="limpiar_filtros_publicaciones">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Titulo Articulo</td>
            <td>Tipo</td>
            <td>Solicitante</td>
            <td>Fecha</td>
            <td>Estado</td>
            <td style='width:15%;'>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="tablausu col-md-12 <?php if ($sw) echo 'oculto'; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <!-- <div id="nueva_solicitud" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn form-control">Nueva Publicación</span>
            </div>
          </div>
        </div>
        <div id="nuevo_pago" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/pago_paperss.png" alt="...">
              <span class="btn form-control">Nuevo Pago</span>
            </div>
          </div>
        </div> -->
        <div id="bonificaciones" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/bonificaciones.png" alt="...">
              <span class="btn form-control">Bonificaciones</span>
            </div>
          </div>
        </div>
        <div class="" id="listado">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control">Estados Bonificaciones</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
    </div>
  </div>

  <form id="form_agregar_solicitud" method="post">
    <div class="modal fade scroll-modal" id="modal_agregar_solicitud" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Publicación</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin sin_focus txt_proyecto" name="comite_proyecto">
                  <span class="input-group-addon pointer btn_proyecto" style='background-color:white'><span class='fa fa-plus red'></span> Proyecto</span>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin sin_focus txt_revista" name="revista">
                  <span class="input-group-addon pointer btn_revista" style="background-color: white;"><span class="fa fa-plus red"></span> Revista</span>
                </div>
              </div>
              <div class="agro agrupado" style="display: flex;">
                <div class="input-group" style="width: 50%;">
                  <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Año de la publicación</span>
                  <input type="text" name="pub_year" id="pub_year" class="form-control" placeholder="Año de la publicación">
                </div>
                <div class="input-group" style="width: 50%;" id="idiomas_pub">
                  <select name="idiomas" id="idiomas_select" class="form-control">
                    <option value="">Idiomas</option>
                  </select>
                  <span class="input-group-addon pointer" id="agregarIdioma" style="background-color: white;" title="Agregar Idioma"><span class="fa fa-plus-circle red"></span></span>
                  <span class="input-group-addon pointer" id="removerIdioma" style="background-color: white;" title="Eliminar Idioma"><span class="fa fa-minus-circle red"></span></span>
                </div>
              </div>
              <div class="clearfix"></div>
              <input type="text" class="form-control" name="titulo" id="txt_titulo" placeholder="Titulo del articulo" required="true">
              <!-- <input type="text" class="form-control" name="issn" id="txt_issn" placeholder="ISSN" required="true"> -->
              <div class="clearfix"></div>
              <div class="agrupado">
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group" style="width: 100%">
                    <select name="id_ranking" id="ranking" required class="form-control cbxranking">
                      <option value="">Seleccione Ranking</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                  <div class="input-group" style="width: 100%">
                    <select name="indicador" id="indicador" required class="form-control cbx_nac_int_inst">
                      <option value="">NAC/INT/INST</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <select name="pubs_status" id="pubs_status" class="form-control">
                <option value="0" selected default>Seleccione estado de la publicación</option>
              </select>
              <div class="" id="adjs_pubs">
                <!-- Auto gen -->
                
              </div>
              <select name="cuartil" id="cuartil" required class="form-control cbxcuartiles">
                <option value="">Seleccione Cuartil</option>
              </select>
              <!-- <br> -->
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-condensed" id="tabla_autores_iniciales" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                      <td class="sin-borde text-right border-left-none" colspan="4">
                        <span class="btn btn-default btnAgregar" id="agregar_autor">
                          <span class="fa fa-plus red"></span> Agregar Autor</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">No.</td>
                      <td>Nombre Completo</td>
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
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_buscar_autor" method="post">
    <div class="modal fade" id="modal_buscar_autor" role="dialog">
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
                    <button class="btn btn-default" type="button" id="btn_nuevo_autor"><span class='fa fa-user-plus red'></span>
                      Nuevo
                    </button>
                  </span>
                  <input id='txt_aut_buscar' class="form-control" placeholder="Ingrese identificación o nombre del autor">
                  <span class="input-group-addon">
                    <input type="radio" name="tabla" id="autor_cuc" value="personas"> CUC
                    <input type="radio" name="tabla" id="autor_otro" value="otro"> Otro
                  </span>
                  <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_autores_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Ver</td>
                      <td>Nombre Completo</td>
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

  <div class="modal fade" id="modal_nuevo_autor" role="dialog">
    <div class="modal-dialog">
      <form id="form_nuevo_autor" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-user-plus"></span> Registro de Autor</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">
              <h6 class="ttitulo">
                <span class="glyphicon glyphicon-indent-left"></span> Datos del Solicitante
              </h6>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="apellido" id="txtApellido" class="form-control inputt2" placeholder="Primer Apellido" required>
                  <span class="input-group-addon">-</span>
                  <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2" placeholder="Segundo Apellido">
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre" required>
                  <span class="input-group-addon">-</span>
                  <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2" placeholder="Segundo Nombre">
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin sin_focus txt_afiliacion" name="afiliacion">
                  <span class="input-group-addon pointer btn_afilicacion" style='background-color:white'><span class='fa fa-university red'></span> Afiliación</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_notificaciones" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="panel_notificaciones" style="width: 100%" class="list-group">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_detalle_bonificacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la Bonificación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <nav class="navbar navbar-default" id="nav_ver_bonificaciones" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                  <li class="pointer info_bonificaciones active"><a><span class="fa fa-user red"></span>
                    Información Principal</a></li>
                  <li class="pointer autores_bonificaciones"><a><span class="fa fa-user red"></span>
                    Autores</a></li>
                  <li class="pointer evidencias_bonificaciones"><a><span class="fa fa-folder-open red"></span>
                    Evidencias</a></li>
                  <li class="pointer otros_aspectos_bonificaciones"><a><span class="fa fa-link red"></span>
                    Otros Aspectos</a></li>
                  <li class="pointer ver_porcentajes_bonificaciones"><a><span class="fa fa-link red"></span>
                    Porcentajes</a></li>
                  <li class="pointer ver_historial_bonificaciones"><a><span class="fa fa-link red"></span>
                    Historial</a></li>
                  <li class="pointer ver_liquidacion_bonificaciones"><a><span class="fa fa-link red"></span>
                    Liquidación</a></li>
              </ul>
            </div>
          </nav>
          <div class="btn_ver_informacion tabla_info_bonificaciones Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="4">Información de la Bonificación</th>
                <td class="sin-borde text-right border-left-none" colspan="4">
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Titulo de la publicación</td>
                <td class="titulo_arti" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registra" colspan="2"></td>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="persona_registro" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">ISSN</td>
                <td class="issn_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">DOI</td>
                <td class="doi_ver_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Cuartil Scopus</td>
                <td class="cuartil_scopus_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cuartil Wos</td>
                <td class="cuartil_wos_ver_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Proyecto</td>
                <td class="proyecto_ver_bon" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Revista</td>
                <td class="revista_ver_bon" colspan="6"></td>
              </tr>
              <tr id="url2_container">
                <td class="ttitulo" colspan="2">URL INDEX. WOS</td>
                <td class="url_index_wos" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL INDEX. SCOPUS</td>
                <td class="url_index_scopus" colspan="2"></td>
              </tr>
              <tr id="fechas2_container">
                <td class="ttitulo" colspan="2">Año de Indexación</td>
                <td class="año_indexacion" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Publicación</td>
                <td class="fecha_publicacion" colspan="2"></td>
              </tr>
              <tr id="lineas_container">
                <td class="ttitulo" colspan="2">Linea</td>
                <td class="lineas_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Sublineas</td>
                <td class="sublineas_ver_bon" colspan="2"></td>
              </tr>
              <tr id="lineas_container">
                <td class="ttitulo" colspan="2">Editorial</td>
                <td class="editorial_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL Articulo en Linea</td>
                <td class="urlinea_ver_bon" colspan="2"></td>
              </tr>
            </table>
          </div>

          <div class="tabla_categ_ver_bon btn_ver_informacion Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_categorias_bonificacion" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA TIPOS DE ESCRITURAS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Tipo de escritura</td>
                  <td>Persona Registra</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_autores_bon btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_bonificaciones" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre completo</td>
                  <td>Afiliación</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_evidencias btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_evidence" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE EVIDENCIAS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver</td>
                  <td>Comentario</td>
                  <td>Persona Registra</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_otr_asp btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_otros_aspectos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA OTROS ASPECTOS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Pregunta</td>
                  <td>Respuesta</td>
                  <td>Comentario</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          
          <div class="tabla_ver_porcentajes btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_porcentaje" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA PORCENTAJES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Documento</td>
                  <td>Nombres</td>
                  <td>Porcentaje de productividad del Autor en el Artículo</td>
                  <td>Porcentaje de productividad destinada a cumplimiento de Plan de Trabajo (PDT)</td>
                  <td>Productividad destinada a bonificación</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_historial btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estados" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">No.</td>
                  <td>Estado</td>
                  <td>Persona que registra</td>
                  <td>Fecha Registro</td>
                  <td>Observacion</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="btn_ver_informacion tabla_ver_liquidacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered" id="table_ver_liq_bon" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5">
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver Detalle</td>
                  <td>Documento</td>
                  <td>Nombres completos</td>
                  <td>Liquidacion</td>
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

  <div class="modal fade" id="modal_detalle_publicacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la publicación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <!-- Auto gen -->
            <table class="table table-bordered table-condensed tabla_publicaciones">
              <tr>
                <th class="nombre_tabla" colspan="4">Información de la Publicación</th>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <button class="btn btn-default btnArchivos"><span class="fa fa-file red"></span> Archivos</button>
                  <?php if ($sw) { ?>
                    <button class="btn btn-default btnEstados"><span class="fa fa-history red"></span> Historial</button>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Titulo de la publicación</td>
                <td class="titulo_art" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registro" colspan="2"></td>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="persona_registra" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">ISSN</td>
                <td class="issn" colspan="2"></td>
                <td class="ttitulo" colspan="2">ISBN</td>
                <td class="isbn" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Ranking</td>
                <td class="ranking" colspan="2"></td>
                <td class="ttitulo" colspan="2">NAC/INT/INST</td>
                <td class="indicador" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Cuartil</td>
                <td class="cuartil" colspan="2"></td>
                <td class="ttitulo" colspan="2">Idiomas</td>
                <td class="idiomas" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Proyecto</td>
                <td class="proyecto" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Revista</td>
                <td class="revista" colspan="6"></td>
              </tr>
              <tr id="url_container">
                <td class="ttitulo" colspan="2">URL</td>
                <td class="url" colspan="6"></td>
              </tr>
              <tr id="f_postulacion">
                <td class="ttitulo" colspan="2">Fecha de postulación</td>
                <td class="fecha_postulacion" colspan="6"></td>
              </tr>
              <tr id="f_aceptacion_rechazo">
                <td class="ttitulo" colspan="2">Fecha de aceptación/rechazo</td>
                <td class="fecha_aceptacion" colspan="6"></td>
              </tr>
              <tr id="f_publicacion">
                <td class="ttitulo" colspan="2">Fecha de publicación</td>
                <td class="fecha_publicacion" colspan="6"></td>
              </tr>
            </table>
            <!-- Tabla papers -->
            <table class="table table-bordered table-condensed tabla_papers" hidden>
              <tr>
                <th class="nombre_tabla" colspan="4">Información del artículo</th>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <button class="btn btn-default btnArchivosP"><span class="fa fa-file red"></span> Archivos</button>
                  <?php if ($sw) { ?>
                    <button class="btn btn-default btnEstados"><span class="fa fa-history red"></span> Historial</button>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Titulo del artículo</td>
                <td class="titulo_articulo" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registro" colspan="2"></td>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="persona_registra" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Tipo de publicación</td>
                <td class="rev_o_conf" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cuartil</td>
                <td class="cuartill" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre de Revista</td>
                <td class="rev_name" colspan="2"></td>
                <td class="ttitulo" colspan="2">Codigo SAP</td>
                <td class="cod_sap" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Tipo de pago</td>
                <td class="tpago_valor" colspan="2"></td>
                <td class="ttitulo" colspan="2">Tipo de cuenta bancaria</td>
                <td class="tcbancaria" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre del Banco</td>
                <td class="banck_name" colspan="2"></td>
                <td class="ttitulo" colspan="2">Tipo de moneda</td>
                <td class="money_type" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Valor del pago</td>
                <td class="pago_valor" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL/Link de Pago</td>
                <td class="pago_link" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Numero de identificacion del Artículo</td>
                <td class="num_art_id" colspan="6"></td>
              </tr>
            </table>
            <table class="table table-bordered table-condensed tabla_bonificaciones">
              <tr>
                <th class="nombre_tabla" colspan="4">Información de las Bonificaciones</th>
                <td class="sin-borde text-right border-left-none" colspan="4">
                  <button class="btn btn-default btnArchivos"><span class="fa fa-file red"></span> Archivos</button>
                  <?php if ($sw) { ?>
                    <button class="btn btn-default btnEstados"><span class="fa fa-history red"></span> Historial</button>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Titulo de la publicación</td>
                <td class="titulo_art_bon" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registro_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="persona_registra_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">DOI del articulo</td>
                <td class="doi_articulo__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Nombre de la revista</td>
                <td class="name_revista__bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">ISSN</td>
                <td class="issn__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cuartil en Scopus</td>
                <td class="cuartil_scopus__bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Cuartil en Wos</td>
                <td class="cuartil_wos__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cuartil liquidación bonificación</td>
                <td class="cuartil_liq_bon__bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de publicación</td>
                <td class="date_public__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL</td>
                <td class="url_art__bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Proyecto Index</td>
                <td class="name_proyecto__bon" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Inicio Proyecto</td>
                <td class="i_proyecto__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fin Proyecto</td>
                <td class="f_proyecto__bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Editorial</td>
                <td class="editorial__bon" colspan="6"></td>
              </tr>
              <!-- <tr>
                <td class="ttitulo" colspan="2" style="width: 100px;">¿Se trata  de un artículo publicado en revistas  Nature Q1?</td>
                <td class="pregunta1__bon" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2" style="width: 100px;">¿La revista hace parte del listado de revistas  o editoriales  predatorias descritas en el anexo 1 parágrafo  5 del artículo Nº 3?</td>
                <td class="pregunta2__bon" colspan="6"></td>
              </tr> -->
            </table>
          </div>
          <div hidden id="tabla_autores" class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_publicacion" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre completo</td>
                  <td>Afiliación</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <!-- Tabla de autores de pago paper-->
          <div hidden id="tabla_autores_pag" class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_pagos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Nombre Completo</td>
                  <td>Porcentaje asignado</td>
                  <td>Equivalente en dinero</td>
                  <td hidden></td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div hidden id="tabla_autores_bon" class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_bonificaciones" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre completo</td>
                  <td>Afiliación</td>
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

  <div class="modal fade" id="modal_gestionar_solicitud_bonificaciones" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Gestión de la solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <nav class="navbar navbar-default" id="nav_inventario" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer" id="aprobar_solicitud_bon"><a><span class="fa fa-thumbs-up green"></span>
                  Aprobar</a></li>
              </ul>
              <ul class="nav navbar-nav">
                <li class="pointer" id="Negar_solicitud_bon"><a><span class="fa fa-thumbs-down red"></span>
                  Negar</a></li>
              </ul>
              <ul class="nav navbar-nav">
                <li class="pointer" id="corregir_solicitud_bon"><a><span class="fa fa-long-arrow-left red"></span>
                  Corregir</a></li>
              </ul>
            </div>
          </nav>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_validacion_bon" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th colspan="2" class="nombre_tabla">TABLA DE VALIDACIONES</th>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    <button class="btn btn-default btnMostrarJuicios"><span class="fa fa-file red"></span> Juicios</button>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Requerimiento</td>
                  <td>Estado</td>
                  <td>Comentarios</td>
                  <td>Juicio</td>
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

  <div class="modal fade" id="modal_listar_juicios" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Listar Juicios</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <nav class="navbar navbar-default" id="nav_inventario" style="display: flex;">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer" id="ver_Gest_ini"><a>Gestores</a></li>
                </ul>
                <ul class="nav navbar-nav">
                  <li class="pointer" id="ver_aux_pub"><a>Auxiliar Pub.</a></li>
                </ul>
                <ul class="nav navbar-nav">
                  <li class="pointer" id="ver_dir_pub"><a>Director Pub.</a></li>
                </ul>
              </div>
            </nav>
            <section class="page-section text-white mb-0" id="">
              <div class="container">
                <div id='msj_aprobar'>
                </div>
              </div>
            </section>
            <table class="table table-bordered table-hover table-condensed" id="tabla_listar_respuestas_bon" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th colspan="2" class="nombre_tabla">TABLA DE JUICIOS</th>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Requerimiento</td>
                  <td>Tipo Gestion</td>
                  <td>Juicio</td>
                  <td>Comentarios</td>
                  <td>Evaluador</td>
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
  
  <div class="modal fade scroll-modal" id="modal_administrar_pub" role="dialog">
    <div class="modal-dialog modal-lg">
      <form id="form_articulos_aut" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <nav class="navbar navbar-default" id="menu_administrar_adm">
							<div class="container-fluid">
									<ul class="nav nav-tabs nav-justified">
										<li class="pointer btn_comite_ind active"><a><span class="fa fa-money red"></span> Comité</a></li>
										<li class="pointer btn_adm_permisos"><a><span class="fa fa-pencil-square-o red"></span> Permisos </a></li>
									</ul>
							</div>
					  </nav>

            <div class="admin_comite adm_proceso active">
              <table class="table table-bordered table-hover table-condensed" id="table_comite" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA COMITÉ</td>
                    <td colspan="5"class="sin-borde text-right border-left-none">
                      <span data-toggle="modal" data-target="#boton_guardar_comite" id="boton_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span> Nuevo</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Fecha de cierre</td>
                    <td>Descripción</td>
                    <td>#Bonificaciones</td>
                    <td>Creado Por</td>
                    <td>Estado</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div class="admin_permisos adm_proceso oculto">
              <div style="display: flex; justify-content: space-evenly;">
                <div id="permisos" style="width: 100%;">
                  <div id='container_mensaje_ap'></div>
                  <div class="form-group">
                    <div class="input-group agro col-md-8">
                      <input name="persona_soli" type="hidden" id="input_sele_re">
                      <span id="s_persona" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
                      <span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                    </div>
                  </div>
                  <table id="tabla_actividades" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                      <tr><td  class="nombre_tabla" colspan="4">TABLA PROCESOS</td></tr>
                      <tr class="filaprincipal ">
                        <td  class="opciones_tbl">No.</td>
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
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<div id="modal_elegir_estado" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_estados_adm" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr><th colspan="3" class="nombre_tabla">TABLA ESTADOS</th></tr>
						<tr class="filaprincipal">
							<td>Tipo</td>
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
          <h3 class="modal-title"><span class="fa fa-plus-circle"></span> Nuevo Comité</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-10 col-md-offset-1">

              <div class="alert alert-info">
                <p class="text-justify">
                  <strong><span class="fa fa-warning"></span> Tener en cuenta: </strong> Al momento de guardar un nuevo comité, los anteriores se finalizarán automaticamente.
                </p>
              </div>

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
  

  <form id="form_list_bonificaciones" method="post">
    <div class="modal fade" id="modal_list_bonificaciones" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Listar Bonificaciones</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_list_bon" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE ARTÍCULOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Ver</td>
                      <td>Tipo</td>
                      <td>Solicitante</td>
                      <td>Fecha</td>
                      <td>Estado</td>
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
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_gestionar" role="dialog">
    <div class="modal-dialog">
      <form id="form_gestion" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-tasks"></span> Gestionar Publicación</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row" data-id="fecha_campo">
              <h6 class="ttitulo">
                <span class="glyphicon glyphicon-indent-left"></span> Información Requerida
              </h6>
              <div id="container-files"></div>
              <div class="agro agrupado" id="datePost">
                <div class="input-group">
                  <span class="input-group-addon" style='background-color:white' id="need_date"></span>
                  <input class="form-control sin_margin" value='' type="date" id="fecha_campo">
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div id="tabla_distribucion" class="table-responsive" style="margin-bottom:20px;">
              <h6 class="ttitulo">
                <span class="glyphicon glyphicon-indent-left"></span> Distribución
              </h6>
              <table class="table table-bordered table-hover table-condensed" id="tabla_autores_distribucion" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE DISTRIBUCIÓN</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre completo</td>
                    <td>Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_autores_validacion" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-tasks"></span> Validar distribución</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_validacion" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre Completo</td>
                  <td>Porcentaje asignado (%)</td>
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

  <div class="modal fade" id="modal_firmar_bonificaciones" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-tasks"></span> Verificación y Firmar</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <div class="alert alert-info">
              <p class="text-justify">
                <strong><span class="fa fa-warning"></span> Aviso: </strong>Si está de acuerdo con la información consignada a continuación, por favor acepte presionando el botón ACEPTAR que se encuentra en la parte inferior.
              </p>
            </div>
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_firma"  cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre Completo</td>
                  <td>Porcentaje asignado (%)</td>
                  <td>Porcentaje No Bonificable (%)</td>
                  <td>Porcentaje Bonificable (%)</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <span class="btn btn-default btn_aceptacion_porcentajes" id="btn_aceptacion_porcentajes">
            <span class="badge" id="aceptacion_porcentajes" style="background-color: #6e1f7c;"></span> Aceptar
          </span>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_archivos_gestion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-tasks"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <nav class="navbar navbar-default" id="visto_bueno_nav" style="display: flex;">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer" id="visto_bueno"><a><span class="fa fa-check red" style="color:#2E79E5"></span> Dar Visto bueno</a></li>
                  <li class="pointer" id="visto_malo"><a><span class="fa fa-times" style="color:#cc0000"></span> Enviar a corrección</a></li>
                </ul>
              </div>
            </nav>
            <table class="table table-bordered table-hover table-condensed" id="tabla_archivos_gestion" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ARCHIVOS</td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver</td>
                  <td>Archivo</td>
                  <td>Nombre Guardado</td>
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

  <div class="modal fade" id="modal_archivos" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_archivos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ARCHIVOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">Ver.</td>
                  <td>Archivo</td>
                  <td>Nombre Guardado</td>
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

  <div id="modal_elegir_persona" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Buscar Persona</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<form id="frm_buscar_persona">
					<div class="input-group col-md-6">
						<input type="text" class="form-control sin_margin" required="true" id='txt_persona' placeholder="Buscar Persona"/>
						<span type="submit" class="input-group-addon pointer" id='btn_buscar_persona' style='	background-color:white'><span class='fa fa-search red'></span></span>
					</div><br>
				</form>
				<table id="tabla_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr><th colspan="3" class="nombre_tabla">TABLA PERSONAS</th></tr>
						<tr class="filaprincipal">
							<td>Nombre</td>
							<td class="opciones_tbl_btn">Gestión</td>
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

  <!-- Modal para mostrar los archivos de pago papers segun el id de la publicacion -->

  <div class="modal fade" id="modal_archivos_pagop" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed tabla_papers" id="papers_files">
              <tr>
                <td class="ttitulo">
                  <a target='_blank' href='' data-id="adj_ca" style="background-color: #5cb85c;color: white;" class="pointer form-control"><span>Ver</span></a>
                </td>
                <td class="cambiar">Carta de aceptacion</td>
              </tr>
              <tr>
                <td class="ttitulo">
                  <a target='_blank' href='' data-id="adj_rc" style="background-color: #5cb85c;color: white;" class="pointer form-control"><span>Ver</span></a>
                </td>
                <td class="cambiar">Revision de cuartil</td>
              </tr>
              <tr data-trid="adj_pi">
                <td class="ttitulo">
                  <a target='_blank' href='' data-id="adj_pi" style="background-color: #5cb85c;color: white;" class="pointer form-control"><span>Ver</span></a>
                </td>
                <td class="cambiar">Adjunto pago internacional</td>
              </tr>
              <tr data-trid="adj_me">
                <td class="ttitulo">
                  <a target='_blank' href='' data-id="adj_me" style="background-color: #5cb85c;color: white;" class="pointer form-control"><span>Ver</span></a>
                </td>
                <td class="cambiar">Adjunto moneda extranjera</td>
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

</div>

<!-- Fin del modal para mostrar archivos de pago papers -->


<div class="modal fade" id="modal_estados" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-history"></span> Estados</h3>
      </div>
      <div class="modal-body " id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estados" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
              </tr>
              <tr class="filaprincipal">
                <td class="opciones_tbl">No.</td>
                <td>Estado</td>
                <td>Persona que registra</td>
                <td>Fecha Registro</td>
                <td>Observacion</td>
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

<form id="form_modificar_solicitud" method="post">
  <div class="modal fade scroll-modal" id="modal_modificar_publicacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-pencil-square-o"></span> Modificar Publicacion</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus txt_proyecto" name="comite_proyecto">
                <span class="input-group-addon pointer btn_proyecto" style='background-color:white'><span class='fa fa-plus red'></span> Proyecto</span>
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus txt_revista" name="revista">
                <span class="input-group-addon pointer btn_revista" style="background-color: white;"><span class="fa fa-plus red"></span> Revista</span>
              </div>
            </div>
            <input type="text" class="form-control txt_titulo" name="titulo" placeholder="Titulo del articulo" required="true">
            <div class="clearfix"></div>
            <div class="agrupado">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="id_ranking" required class="form-control cbxranking ranking">
                    <option value="">Seleccione Ranking</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="indicador" required class="form-control cbx_nac_int_inst indicador">
                    <option value="">NAC/INT/INST</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_crear_filtros" role="dialog">
  <div class="modal-dialog">
    <!--Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" dta-simiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="class-body" id="bodymodal">
        <div class="row">
          <select name="id_estado" class="form-control cbxestados">
            <option value="">Filtrar por estado</option>
          </select>
          <select name="id_ranking" class="form-control cbxranking">
            <option value="">Filtrar por ranking</option>
          </select>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Desde</span>
              <input type="date" class="form-control sin_margin" value="" name="fecha_inicial">
            </div>
          </div>
          <div class="agro agrupado">
            <div class="input-group">
              <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Hasta</span>
              <input type="date" class="form-control sin_margin" value="" name="fecha_final">
            </div>
          </div>
        </div>
        <br>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active" id="btn_filtrar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<form id="form_buscar_proyecto" method="post">
  <div class="modal fade" id="modal_buscar_proyecto" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Proyecto</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_dato_proyecto' class="form-control txt_dato_proyecto" placeholder="Ingrese nombre del proyecto o del investigador">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_proyecto_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE PROYECTO</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre del Proyecto</td>
                    <td>Investigador</td>
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

<form id="form_buscar_idioma" method="post">
  <div class="modal fade" id="modal_buscar_idioma" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Idioma</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_idioma_buscar' class="form-control" placeholder="Ingrese el idioma que desea buscar">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_idiomas_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA IDIOMAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Idioma</td>
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

<form id="form_buscar_revista" method="post">
  <div class="modal fade" id="modal_buscar_revista" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Revista</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btn_nueva_revista"><span class='fa fa-plus-circle red'></span>
                    Crear
                  </button>
                </span>
                <input id='txt_dato_revista' class="form-control txt_dato_revista" placeholder="Ingrese el nombre de la revista">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_revista_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE REVISTAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre de la revista</td>
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

<form id="form_buscar_afiliacion" method="post">
  <div class="modal fade" id="modal_buscar_afiliacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Institución</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_dato_afiliacion' class="form-control txt_dato_afiliacion" placeholder="Ingrese el nombre de la institución">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_afiliacion_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE INSTITUCIONES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre de la Institución</td>
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


<div class="modal fade" id="modal_detalle_autor" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle del autor</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <tr>
              <th class="nombre_tabla" colspan="8">Información del autor</th>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Nombre</td>
              <td class="nombre_completo" colspan="6"></td>
            </tr>
            <tr id="id_identificacion_autor">
              <td class="ttitulo" colspan="2">Tipo identificación</td>
              <td class="tipo_identificacion" colspan="2"></td>
              <td class="ttitulo" colspan="2">No. identificación</td>
              <td class="identificacion" colspan="2"></td>
            </tr>
            <tr id="id_grupo_autor">
              <td class="ttitulo" colspan="2">Grupo</td>
              <td class="grupo" colspan="6"></td>
            </tr>
            <tr>
              <td class="ttitulo" colspan="2">Afiliación</td>
              <td class="afiliacion" colspan="6"></td>
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

<div class="modal fade" id="modal_almacenar_revista" role="dialog">
  <div class="modal-dialog">
    <form id="form_almacenar_revista" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span><span class="fa fa-book"></span> Crear revista</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <h6 class="ttitulo">
              <span class="glyphicon glyphicon-indent-left"></span> Datos de la revista
            </h6>
            <input type="text" class="form-control" name="nombre_revista" id="text_nombre_revista" placeholder="Nombre de la revista" required="true">
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="issn" id="txt_issn" class="form-control inputt2" placeholder="ISSN">
                <span class="input-group-addon">-</span>
                <input type="text" name="isbn" id="txt_isbn" class="form-control inputt2" placeholder="ISBN">
              </div>
            </div>
            <select name="cuartil" id="pub_cuartil_select" required class="form-control cbxcuartiles">
              <option value="">Seleccione Cuartil</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_registro_solicitud_bonificacion" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content" >
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-dollar"></span> Bonificaciones</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">

            <div class="opcion__cont" id="data__principal" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Información del Artículo">
              <img src="<?php echo base_url() ?>/imagenes/informacion.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Información del Artículo</span>
            </div>

            <div class="opcion__cont" id="data__autores" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Autores">
              <img src="<?php echo base_url()?>/imagenes/autores-bonificaciones.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Autores</span>
            </div>

            <div class="opcion__cont" id="data__otros_aspectos" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Otros aspectos">
              <img src="<?php echo base_url()?>/imagenes/info-adicional.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Otros aspectos</span>
            </div>

            <div class="opcion__cont" id="data__evidencias" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Evidencias de la solicitud">
              <img src="<?php echo base_url()?>/imagenes/evidencias.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Evidencias</span>
            </div>

          </div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
          <button type="button" class="btn btn-danger active" id="btn_send_sol"><span class="fa fa-arrow-right"></span> Enviar Solicitud</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade scroll-modal" id="modal_informacion_autores" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <form id="form_informacion_autores" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"></span> Información Autores</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <nav class="navbar navbar-default menu_autores_adm" id="menu_autores_adm">
					  <div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
								<li class="pointer btn_autores btn_autores_profesores active" id="btn_autores_profesores"><a><span class="fa fa-money red"></span> Profesores</a></li>
								<li class="pointer btn_autores btn_autores_estudiantes" id="btn_autores_estudiantes"><a><span class="fa fa-pencil-square-o red"></span> Estudiantes</a></li>
								<li class="pointer btn_autores btn_autores_externos" id="btn_autores_externos"><a><span class="fa fa-pencil-square-o red"></span> Externos</a></li>
							</ul>
					  </div>
          </nav>
          <section class="page-section text-white mb-0" id="">
            <div class="container">
              <div class="alert alert-info">
		            <p class="text-justify">
		              <strong><span class="fa fa-warning"></span> Tener en cuenta: </strong> En la opción <strong>Acciones</strong> de cada autor, podrá encontrar elementos que es necesario agregar antes de continuar.
		            </p>
              </div>
              <div id="autores autores_profesores" class="autores_profesores autores_div">
                <div class="msg_porcentaje"></div>
                <div class="col-md-6 col-lg-3 mb-5">
                  <div class="card">
                    <br>
                    <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/producto_formacion.png" alt=""/>
                      <div class="card-body">
                        <h6 class="card-title" style='color : black'>
                        AGREGAR
                        </h6>
                        <br>
                        <p class="card-text" style='color : black'>
                        Aquí puedes agregar un autor.
                        </p>
                        <span class="btn btn-primary btn-block btnAgregar" id="btn_agregar_persona">
                        Agregar
                        </span>
                      </div>
                  </div>
                </div>
                <div id="data_profesores"></div>
              </div>
              <div id="autores autores_estudiantes" class= "autores_estudiantes autores_div oculto">
                <div class="alert alert-warning">
		              <p class="text-justify">
		                <span class="fa fa-info"></span><strong> Tener en cuenta: </strong> Para tener en cuenta: La condición de estudiante será congruente por la establecida en el reglamento estudiantil vigente de la Universidad. Se considera la bonificación también para el egresado cuyo trabajo haya hecho parte de su proceso formativo. (Parágrafo 12 del acuerdo 1832)
		              </p>
                </div>
                <div class="col-md-6 col-lg-3 mb-5">
                  <div class="card">
                    <br>
                    <img class="img-fluid" style="max-width: 100%; height: 150px !important; margin: 0 auto;" src="<?php echo base_url(); ?>imagenes/producto_formacion.png" alt=""/>
                      <div class="card-body">
                        <h6 class="card-title" style='color : black'>
                        AGREGAR
                        </h6>
                        <br>
                        <p class="card-text" style='color : black'>
                        Aquí puedes agregar un autor.
                        </p>
                        <span class="btn btn-primary btn-block btnAgregar" id="btn_agregar_estudiante">
                        Agregar
                        </span>
                      </div>
                  </div>
                </div>
                <div id="data_estudiantes"></div>
              </div>
              <div id="autores autores_externos" class= "autores_externos autores_div oculto">
                <div class="col-md-6 col-lg-3 mb-5">
                  <div class="card">
                    <br>
                    <img class="img-fluid" style="max-width: 100%; height: 150px !important; margin: 0 auto;" src="<?php echo base_url(); ?>imagenes/producto_formacion.png" alt=""/>
                      <div class="card-body">
                        <h6 class="card-title" style='color : black'>
                        AGREGAR
                        </h6>
                        <br>
                        <p class="card-text" style='color : black'>
                        Aquí puedes agregar un autor.
                        </p>
                        <span class="btn btn-primary btn-block btnAgregar" id="btn_agregar_externo">
                        Agregar
                        </span>
                      </div>
                  </div>
                </div>
                <div id="data_externos"></div>
              </div>
            </div>
          </section>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<form id="form_buscar_autores_bonificaciones" method="post">
  <div class="modal fade" id="modal_buscar_autor_bonificaciones" role="dialog">
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
                  <button class="btn btn-default btn_nuevo_autor_bon oculto" type="button" id="btn_nuevo_autor_boninficaciones"><span class='fa fa-user-plus red'></span>
                    Nuevo
                  </button>
                </span>
                <input id='txt_search_author' class="form-control" placeholder="Ingrese identificación o nombre del autor">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="table_authors_bonus__search" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Nombre Completo</td>
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

<div class="modal fade" id="modal_informacion_investigacion" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Información Autor</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <div class="agro agrupado" id="grupos_aut">
            <div class="input-group" style="width: 100%">
              <select name="grupos" id="grupos_select" class="form-control">
                <option value="">Grupos</option>
              </select>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="agro agrupado" id="lineas_aut">
            <div class="input-group" style="width: 100%">
              <select name="lineas" id="lineas_select" class="form-control">
                <option value="">Lineas</option>
              </select>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="agro agrupado" id="sublineas_aut">
            <div class="input-group" style="width: 100%">
              <select name="sublineas" id="sublineas_select" class="form-control">
                <option value="">Sublineas</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="btn_agregar_autor"><span class="fa fa-arrow-right"></span> Continuar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detail_data__bonificaciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle del autor</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <nav class="navbar navbar-default" id="nav_ver_autores" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer info_autor active"><a><span class="fa fa-user red"></span>
                  Información del Autor</a></li>
                <li class="pointer info_adic_autors"><a><span class="fa fa-info red"></span>
                  Información Adicional</a></li>
                <li class="pointer ver_afil_inst"><a><span class="fa fa-university red"></span>
                  Afiliaciones</a></li>
                <li class="pointer ver_art_susc"><a><span class="fa fa-university red"></span>
                Articulos Suscritos</a></li>
                <li class="pointer ver_art_cumpl"><a><span class="fa fa-university red"></span>
                Articulos Cumplidos</a></li>
              </ul>
            </div>
          </nav>

          <div class="btn_ver_autor tabla_info_princ_autor Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed active">
              <tr>
                <th class="nombre_tabla" colspan="8">Información del autor</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre</td>
                <td class="nombre_completo__bon" colspan="6"></td>
              </tr>
              <tr id="id_identificacion_autor">
                <td class="ttitulo" colspan="2">Tipo identificación</td>
                <td class="tipo_identificacion__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">No. identificación</td>
                <td class="identificacion__bon" colspan="2"></td>
              </tr>
              <tr class="id_departamento_autor oculto">
                <td class="ttitulo" colspan="2">Departamento</td>
                <td class="departamento_aut__bon" colspan="6"></td>
              </tr>
              <tr class="id_inst_per_ext oculto">
                <td class="ttitulo" colspan="2">Institución filial</td>
                <td class="institucion_externa" colspan="6"></td>
              </tr>
              <tr class="id_program_acad oculto">
                <td class="ttitulo" colspan="2">Programa Academico</td>
                <td class="programa_academico" colspan="6"></td>
              </tr>
              <tr class="id_linea_autor oculto">
                <td class="ttitulo" colspan="2">Linea</td>
                <td class="linea__bon" colspan="6"></td>
              </tr>
              <tr class="id_sublinea_autor oculto">
                <td class="ttitulo" colspan="2">Sublinea</td>
                <td class="sublinea__bon" colspan="6"></td>
              </tr>
              <tr class="afil_vinc_bon">
                <td class="ttitulo" colspan="2">Afiliación</td>
                <td class="afiliacion__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Vinculación</td>
                <td class="Vinculacion__bon" colspan="2"></td>
              </tr>
            </table>
          </div>
          
          <div class="btn_ver_autor tabla_info_adic_autor oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed info_adicional_autor oculto">
              <tr>
                <th class="nombre_tabla" colspan="8">Información adicional del autor</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace CVLAC</td>
                <td class="enlace_cvlac" colspan="2"></td>
                <td class="ttitulo" colspan="2">Enlace Google Scholar</td>
                <td class="enlace_google" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace Research Gate</td>
                <td class="enlace_rg" colspan="2"></td>
                <td class="ttitulo" colspan="2">Enlace Red Academica</td>
                <td class="enlace_red_acad" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace Mendeley</td>
                <td class="enlace_mendeley" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace Publons</td>
                <td class="enlace_publons" colspan="2"></td>
                <td class="ttitulo" colspan="2">Enlace Gruplac</td>
                <td class="enlace_gruplac" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Categoría Investigador</td>
                <td class="cat_investigador" colspan="2"></td>
                <td class="ttitulo" colspan="2">Departamento</td>
                <td class="departamento_aut_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">H-Index (Scholar)</td>
                <td class="hi_index_scholar" colspan="2"></td>
                <td class="ttitulo" colspan="2">RG Score</td>
                <td class="rg_score" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">H-Index (Scopus)</td>
                <td class="hi_index_scopus" colspan="2"></td>
                <td class="ttitulo" colspan="2">ORCID ID</td>
                <td class="orcid_id_info" colspan="2"></td>
              </tr>
            </table>
          </div>

          <div class="btn_ver_autor tabla_afiliaciones_inst oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_afiliaciones" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE AFILIACIONES</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre</td>
                    <td>Pais</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="btn_ver_autor tabla_arti_cumpl oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_art_cump" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA ARTICULOS CUMPLIDOS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Cantidad</td>
                    <td>Cuartil</td>
                    <td>Titulo</td>
                    <td>Link</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="btn_ver_autor tabla_arti_susc oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_art_susc" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA ARTICULOS SUSCRITOS</td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Cuartil</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
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

<div class="modal fade" id="modal_info_estudiante_bon" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Información Autor</h3>
      </div>
      <form id="form_tipo_afiliaciones">
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="clearfix"></div>
            <div id="inst_externa" class="agro agrupado oculto">
              <div class="alert alert-info">
                <p class="text-justify">
                  <strong>
                    <span class="fa fa-warning"></span> Aviso:
                  </strong>
                  Si la institución de escogencia no se encuentra, por favor <a id="Agregar_instituciones_externas"><strong>Registrela Aquí</strong></a>
                </p>
              </div>
              <select name="inst_ext_bon" id="inst_ext_bon" data-live-search='true' class="selectpicker form-control sin_margin"> <option value="">Institución invitado</option> </select>
            </div>
            <div id="selec_program_est" class="agro agrupado oculto">
              <select name="programa_est_bon" id="programa_est_bon" class="form-control cbxprog_est" data-trigger="hover" data-toggle="popover" data-content="Seleccione el programa del estudiante">
                <option value="">Programa</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" id="btn_add_est_bon"><span class="fa fa-arrow-right"></span> Continuar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_asignar_porcentaje__bon" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Lista de autores</h3>
      </div>
      <form id="form_asignar_porcentaje">
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_asignar_porcentaje" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Autores</td>
                    <td>Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </form>
      <div class="modal-footer" id="footermodal">
        <button type="botton" class="btn btn-danger active btn_asignar_porc"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_agregar_institucion" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_agregar_institucion" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-comment"></span> Agregar Institucion</h3>
           </div>
           <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <input type="text" class="form-control" name='name_inst' placeholder='Nombre Institución'>
                </input>
              </div>  
              <div class="agro agrupado">
                <select name="name_country" id="select_pais_inst" data-live-search='true' class="selectpicker form-control sin_margin">
                  <option value="">Pais de Institución</option> 
                </select>
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
<div class="modal fade" id="modal_comentario_porcentaje" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_guardar_comentario_porcentaje" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-comment"></span> Comentario</h3>
           </div>
           <div class="modal-body" id="bodymodal">
            <div class="row">
              <div id="container_comment"></div>
              <textarea class="form-control inputt" name="comment" placeholder="Comentario"></textarea>
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
<div class="modal fade" id="modal_agregar_enlaces" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_enlaces" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-link"></span> Información del Autor</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="table-responsive col-md-12" style="width: 100%">
                <div class="agro agrupado">
                  <input type="text" class="form-control rounded links url_cvlac" name='url_cvlac' placeholder='*Enlace de consulta artículo agregado al CVLAC por cada uno de los autores CUC'>
                </input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_google_scholar" name='url_google_scholar' placeholder='*Enlace de consulta artículo agregado Google Scholar por cada uno de los autores CUC'></input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_research_gate" name='url_research_gate' placeholder='*Enlace de consulta artículo agregado Research Gate por cada uno de los autores CUC'></input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_red_acad_disc" name='url_red_acad_disc' placeholder='*Enlace de consulta artículo agregado Red Académica Disciplinar o academia.edu por cada uno de los'></input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_mendeley" name='url_mendeley' placeholder='*Enlace de consulta artículo agregado Mendeley por cada uno de los autores CUC'></input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_Gruplac" name='url_Gruplac' placeholder='*Enlace de consulta artículo agregado a Gruplac por cada uno de los autores CUC'></input>
              </div>
              <div class="agro agrupado">
                <input type="text" class="form-control rounded links url_Publons" name='url_Publons' placeholder='*Enlace de consulta artículo agregado a Publons por cada uno de los autores CUC'></input>
              </div>
              <div class="agro agrupado">
                <select name="categoria_minciencias__bon" id="bon_cat_minciencias" class="form-control cbxcatminciencias">
                  <option value="">Seleccione la categoría de investigador (Minciencias)</option>
                </select>
              </div>
              <div class="agro agrupado">
                <select name="departamento_autor__bon" id="bon_departamento" class="form-control cbxdepartamento">
                  <option value="">Departamento</option>
                </select>
              </div>
              <div class="agro agrupado">
                <input class="form-control" name="hindex_scholar__bon" type="number" placeholder="Digite H-Index (Scholar)">
              </div>
              <div class="agro agrupado">
                <input class="form-control" name="hindex_scopus__bon" type="number" placeholder="Digite H-Index (Scopus)">
              </div>
              <div class="agro agrupado">
                <input class="form-control" name="ResearchGate__bon" type="number" placeholder="Digite RG Score (Researchgate)">
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer" style='background-color:white; width: 10px;'> <strong>https://orcid.org/</strong></strong></span>
                  <input type="text" style="width: 253px;" class="form-control" name="id_orcid" id="txt_orcid" placeholder="XXXX-XXXX-XXXX-XXX">              
                </div>
              </div>
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

<div class="modal fade" id="modal_asignar_porcentajes" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Porcentajes</h3>
      </div>
      <form id="form_ingresar_porcentajes" enctype="multipart/form-data" method="post">
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Porcentaje de productividad del Autor en el Artículo
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="first_porcentage" id="first_porcentage" data-ide="first_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Porcentaje de productividad destinada a cumplimiento de Plan de Trabajo (PDT)
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="second_porcentage" id="second_porcentage" data-ide="second_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Productividad destinada a bonificación
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="third_porcentage" id="third_porcentage" data-ide="third_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="botton" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_solicitar_prog_dep" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_solicitar_prog_dep">
      <div class="modal-content" >
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Información de la publicación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="agro agrupado">
            <select name="id_programa" required class="form-control cbx_id_programa">
              <option value="">Programa</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger btn_guardar_id_programa active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_otros_aspectos__bon" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_otros_aspectos">
      <div class="modal-content" >
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Otros aspectos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <div id="container__otros_aspectos">
              </div>
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

  <div class="modal fade scroll-modal" id="modal_articulos_autor" role="dialog">
    <div class="modal-dialog modal-lg">
      <form id="form_articulos_aut_bon" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-dollar"></span> Articulos</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <nav class="navbar navbar-default" id="menu_articulos_adm">
							<div class="container-fluid">
									<ul class="nav nav-tabs nav-justified">
										<li class="pointer btn_articulos_suscritos active"><a><span class="fa fa-money red"></span> Articulos suscritos</a></li>
										<li class="pointer btn_articulos_cumplidos"><a><span class="fa fa-pencil-square-o red"></span> Articulos cumplidos</a></li>
									</ul>
							</div>
					  </nav>
            <div class="articulos_suscritos adm_proceso active">
              <div class="agro table-responsive">
                <table id="table_articulos_suscritos_aut" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
                <thead class="ttitulo ">
                    <tr ><th colspan="4" class="nombre_tabla">TABLA DE ARTICULOS SUSCRITOS</th></tr>
                    <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                    <td>Cuartil</td>
                </thead>
                <tbody>
                </tbody>
              </table>
              </div>

              
            </div>
            <div class="articulos_cumplidos adm_proceso oculto">
              <div class="agro table-responsive">
                <table class="table table-bordered table-hover table-condensed" id="table_articulos_cumplidos_aut" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="2" class="nombre_tabla">TABLA DE ARTICULOS CUMPLIDOS</td>
                      <td class="sin-borde text-right border-left-none" colspan="5">
                        <span class="btn btn-default btnAgregar" id="agregar_articulos_cumplidos_aut">
                        <span class="fa fa-plus-circle red"></span> Agregar</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal">
                      <td class="opciones_tbl">No.</td>
                      <td>Nombre Completo</td>
                      <td>Cantidad</td>
                      <td>Cuartil</td>
                      <td>Titulo</td>
                      <td>Link</td>
                      <td class="opciones_tbl_btn">Acciones</td>
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

  <div class="modal fade" id="gestionarAfiliaciones" role="dialog">
    <div class="modal-dialog modal-lg">
      <form action="#" id="FrmgestionarAfiliaciones" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-university"></span> Afiliaciones Institucionales</h3>
           </div>
            <div class="modal-body" id="bodymodal">
              <div class="row">
                <div class="agro agrupado">
                  <div class="input-group">
                    <input type="text" class="form-control sin_margin" placeholder="Digite el nombre institucional" id='txt_nombre_institucion' style='width: 250px;' required>
                    <span class="input-group-addon">-</span>               
                    <input type="text" class="form-control sin_margin sin_focus txt_nombre_pais" id="nombre_pais">
                    <span class="input-group-addon pointer btn_buscar_pais"> <span class="fa fa-search red"></span> Buscar Pais</span>

                    <span id="btn_asignar" class="input-group-addon red pointer"><span class="fa fa-plus"></span> Asignar</span>
                  </div>
                </div>
                <br><br>
              <table id="tabla_afiliacion_institucional" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
                <thead class="ttitulo ">
                    <tr ><th colspan="4" class="nombre_tabla">TABLA AFILIACIONES</th></tr>
                    <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                    <td>Nombre</td>
                    <td>Pais</td>
                    <td class="opciones_tbl">Acciones</td>
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
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_articulos_suscritos" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_articulos_suscritos" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-comment"></span> Articulo Suscrito</h3>
           </div>
           <div class="modal-body" id="bodymodal">
            <div class="row">
               <input type="text" value="" class="form-control text-center cantidad_autor" name="cantidad_autor" data-ide="cantidad_autor" placeholder="Ingrese la cantidad correspondiente" required></input>
               <select name="cuartil_autor" required class="form-control cuartil_autor">
                <option value="">Cuartil Autor</option>
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

  <div class="modal fade" id="modal_articulos_cumplidos" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_articulos_cumplidos" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-comment"></span> Articulo Cumplidos</h3>
           </div>
           <div class="modal-body" id="bodymodal">
            <div class="row">
               <input type="text" value="" class="form-control text-center cantidad_autor_cump" name="cantidad_autor_cump" data-ide="cantidad_autor_cump" placeholder="Ingrese la cantidad correspondiente" required></input>
               <select name="cuartil_autor_cump" required class="form-control cuartil_autor_cump">
                <option value="">Cuartil Autor</option>
               </select>
               <input type="text" value="" class="form-control text-center titulo_art" name="titulo_art" data-ide="titulo_art" placeholder="Ingrese el titulo del articulo" required></input>
               <input type="text" value="" class="form-control text-center link_autor_cump links" name="link_autor_cump" data-ide="link_autor_cump" placeholder="Ingrese el link" required></input>
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

  
<div class="modal fade" id="modal_evidencias_bonificaciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_evidencias_bonificaciones">
      <div class="modal-content" >
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Evidencias</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div id="content_evidencias">
              
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

<div class="modal fade" id="modal_otros_Adjuntos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-folder-open"></span> Evidencias</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="alert alert-info">
          Por favor arrastre las evidencias a presentar.
        </div>	      
        <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
          <input type="hidden" name="id" id="id_solicitud" val="0">
          <div class="dz-message needsclick"><p>Arrastre archivos o presione click aquí</p></div>
        </form>
      </div>
      <div class="modal-footer" id="footermodal">
        <button id="cargar_adj_soli" class="btn btn-danger active btnAgregarEvidencias"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_nuevo_autor_bonificaciones" role="dialog">
    <div class="modal-dialog">
      <form id="form_nuevo_autor_bon" enctype="multipart/form-data" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-user-plus"></span> Registro de Autor</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">
              <h6 class="ttitulo">
                <span class="glyphicon glyphicon-indent-left"></span> Datos del Solicitante
              </h6>
              <div class="agro agrupado">
                <div class="input-group">
                  <select name="tipo_documento" style="width: 183px;" id="tipo_documento" required class="form-control cbxtipo_documento">
                    <option value="">Tipo documento</option>
                  </select>
                  <span class="input-group-addon">-</span>
                  <input type="text" name="txtdocumento" style="width: 183px;" id="txtdocumento_naut" class="form-control inputt2" placeholder="Documento">
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="apellido" id="txtApellido_naut" class="form-control inputt2" placeholder="Primer Apellido" required>
                  <span class="input-group-addon">-</span>
                  <input type="text" name="segundoapellido" id="txtsegundoapellido_naut" class="form-control inputt2" placeholder="Segundo Apellido">
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="nombre" id="txtNombre_naut" class="form-control inputt2" placeholder="Primer Nombre" required>
                  <span class="input-group-addon">-</span>
                  <input type="text" name="segundonombre" id="txtSegundoNombre_naut" class="form-control inputt2" placeholder="Segundo Nombre">
                </div>
              </div>
              <!-- <div class="agro agrupado selec_program_acad_Est oculto">
              <select name="prog_acad_bon" id="prog_acad_bon" class="form-control cbxprog_acad_bon" data-trigger="hover" data-toggle="popover" data-content="Seleccione el programa del estudiante">
                <option value="">Programa Academico</option>
              </select>
            </div> -->
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<div class="modal fade scroll-modal" id="modal_principal_bonificaciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="form_solicitar_bonificacion" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-dollar"></span> Información Principal</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class="alert alert-info">
              <p class="text-justify">
                <strong><span class="fa fa-warning"></span> Tener en cuenta: </strong> Fecha en la que fue publicado el artículo. (Coloque la fecha final de publicación de el artículo se le asigno un número o código y no el first online)
              </p>
              <p class="text-justify">
                Revisar si su artículo está publicado en REVISTAS NATURE Q1 ISSN 14764687, 00280836 y Marcar según Corresponda. La correcta revisión y diligencia de esto podría aumentar el valor de bonificación.
              </p>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" style="width: 460px;" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Nombre de Artículo" class="form-control sin_margin sin_focus" name="nombre_articulo_bon" id="nombre_articulo_bon" placeholder="Nombre del artículo..." required>
                <span class="input-group-addon pointer btn_buscar_art_bon" style='background-color:white; width: 200px;'><span class='fa fa-check red'></span> Título del Artículo</span>                
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon pointer" style='background-color:white; width: 10px;'> <strong>10.</strong></strong></span>
                <input type="text" style="width: 418px;" class="form-control" name="id__doi" id="txt_doi" placeholder="---------">
                <span class="input-group-addon pointer" style='background-color:white; width: 230px;'> DOI del Artículo</span>                
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" style="width: 460px;" class="form-control sin_margin sin_focus txt_revista_bon" name="revista">
                <span class="input-group-addon pointer btn_revista_bon" style="background-color: white; width: 200px;"><span class="fa fa-plus red"></span> Revista</span>
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" class="form-control" style="width: 460px;" name="editorial" id="txt_editorial_bon" placeholder="Editorial" required="true">
                <span class="input-group-addon pointer" style='background-color:white; width: 200px;'> Editorial</span>                
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="issn_bon" id="txt_issn_bon" class="form-control inputt2" placeholder="ISSN">
                <span class="input-group-addon">-</span>
                <select name="index_scopus" id="index_scopus__bon" required class="form-control cbxindex_scopus">
                  <option value="">¿Indexado en Scopus (SJR)?</option>
                </select>
                <span class="input-group-addon">-</span>
                <select name="index_scopus" id="index_wos__bon" required class="form-control cbxindex_wos">
                  <option value="">¿Indexado en Web of Science (JCR)?</option>
                </select>
              </div>
            </div>
            <div class="agro agrupado oculto" id="div_cuartil_scopus" data-placement="bottom">
              <select name="cuartil_scopus" id="bon_cuartil_scopus" class="form-control cbxcuartiles_scopus" data-trigger="hover" data-toggle="popover" data-content="Seleccione el cuartil Scopus">
                <option  data-content="Ingrese el cuartil Scopus" value="">Seleccione Cuartil Scopus</option>
              </select>
            </div>
            <div class="agro agrupado oculto" id="div_cuartil_wos">
              <select name="cuartil_wos" id="bon_cuartil_wos" class="form-control cbxcuartiles_wos" data-trigger="hover" data-toggle="popover" data-content="Seleccione el cuartil Wos">
                <option  data-content="Ingrese el cuartil Wos" value="">Seleccione Cuartil Wos</option>
              </select>
            </div>
            <div class="agro agrupado oculto" id="div_cuartil_nuevo">
              <div class="input-group">
                <input type="text" style="width: 460px;" class="form-control" name="cuartil_otro" id="txt_cuartil_bon" placeholder="Nuevo Cuartil">
                <span class="input-group-addon pointer" style='background-color:white; width: 200px;'> Nuevo Cuartil</span>                
              </div>
            </div>
            <div class="agro agrupado">
              <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Link del articulo" class="form-control rounded links" style="border-radius: 3px;" name="articulo_link" id="articulo_link" placeholder="Inserte URL del articulo en linea">
            </div>
            <!-- <div class="agro agrupado" id="div_cuartil_liq_bon">
              <div class="input-group">
                <input type="text" style="width: 460px;" class="form-control" name="cuartil_liq_bon" id="txt_cuartil_liq_bon" placeholder="Cuartil de liquidación del bono" data-content="Ingrese aqui el cuartil de liquidación del bono">
                <span class="input-group-addon pointer" style='background-color:white; width: 200px;'> Cuartil liquidación</span>                
              </div>
            </div> -->
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha Publicacion</span>
                <input type="date" class="form-control sin_margin" value="" name="date__initial">
                <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Año de Indexación</span>
                <input class="form-control sin_margin date__indexing" data-date-format="yyyy" value="" name="date__indexing">
              </div>
            </div>
            <div class="agro agrupado oculto" id='div_url_indexacion_scopus'>
              <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="URL de Indexación" class="form-control rounded links" style="border-radius: 3px;" name="url_indexacion_scopus" id="url_indexacion_scopus" placeholder="Inserte URL de indexación de Scopus">
            </div>
            <div class="agro agrupado oculto" id='div_url_indexacion_wos'>
              <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="URL de Indexación" class="form-control rounded links" style="border-radius: 3px;" name="url_indexacion_wos" id="url_indexacion_wos" placeholder="Inserte URL de indexación de Wos">
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" style="width: 460px;" class="form-control sin_margin sin_focus txt_proyecto__bon" name="name_proyect__bon">
                <span class="input-group-addon pointer btn_proyecto__bon" style='background-color:white; width: 200px;'><span class='fa fa-plus red'></span> Proyecto</span>
              </div>
            </div>
            <div class="agro agrupado">
              <select name="idiomas" id="idiomas_select_bon" data-live-search='true' class="selectpicker form-control sin_margin"> <option value="">Idioma</option> </select>
            </div>
            <div class="agro agrupado">
              <select name="lineaInv__bon" id="bon_LineaInv" class="form-control cbxlineainv" data-trigger="hover" data-toggle="popover" data-content="Seleccione su linea de investigación">
                <option value="">Linea de investigación</option>
              </select>
            </div>
            <div class="agro agrupado">
              <select name="SublineaInv__bon" id="bon_SublineaInv" class="form-control cbxsublineainv" data-trigger="hover" data-toggle="popover" data-content="Seleccione la sublinea de investigación">
                <option value="">Sublinea de investigación</option>
              </select>
            </div>
            <div class="agro agrupado">
              <select name="ods__bon" id="bon_ods" class="form-control cbxsods" data-trigger="hover" data-toggle="popover" data-content="Seleccione un ODS">
                <option value="">ODS</option>
              </select>
            </div>
            <div class="agro agrupado">
              <br>
              <div class="agro agrupado">
                <div class="input-group ">
                  <select name="bon_categoria" id="bon_categoria" class="form-control cbxcategoria" data-trigger="hover" data-toggle="popover" data-content="Seleccione una categoria">
                    <option  data-content="Ingrese una categoría" value="">Seleccione una categoria</option>
                  </select>
                  <span id="btn_asignar_categoria" class="input-group-addon red pointer"><span class="fa fa-plus"></span> Agregar</span>
                </div>
              </div>
              <table id="tabla_tipo_escritura_art" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
                <thead class="ttitulo ">
                  <tr ><th colspan="4" class="nombre_tabla">TABLA TIPO DE ESCRITURA DEL ARTICULO</th></tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">No.</td>
                    <td>Categoria</td>
                    <td>Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            
            <!-- <div class="agro agrupado" id="pregunta1">
              <div class="">
                <p> ¿Se trata  de un artículo publicado en revistas  Nature Q1? </p>
                  <label for="pre1_res_1"><input  id="pre1_res_1" type="radio" name="respuesta1" value="0"><span> NO</span></label>
                  <label for="pre1_res_2"><input  id="pre1_res_2" type="radio" name="respuesta1" value="1"><span> SI</span></label>
              </div>
            </div>

            <div class="agro agrupado" id="pregunta2">
              <div class="">
                <p> ¿La revista hace parte del listado de revistas o editoriales predatorias descritas en el anexo 1 parágrafo 5 del artículo Nº 3 o LIstado de revistas no avaladas de EDUCOSTA? </p>
                  <label for="pre1_res_1"><input  id="pre2_res_1" type="radio" name="respuesta2" value="0"><span> NO</span></label>
                  <label for="pre1_res_2"><input  id="pre2_res_2" type="radio" name="respuesta2" value="1"><span> SI</span></label>
              </div>
            </div> -->
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<form id="form_buscar_idioma_Bon" method="post">
  <div class="modal fade" id="modal_buscar_idioma_bon" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Idioma</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_idioma_buscar_bon' class="form-control" placeholder="Ingrese el idioma que desea buscar">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_idiomas_busqueda_bon_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA IDIOMAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Idioma</td>
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

  <form id="form_buscar_articulo__bonificaciones" method="post">
    <div class="modal fade" id="modal_buscar_articulo__bonificaciones" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Artículo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_articulo__bonificaciones' name="txt_dato_articulo__bonificaciones" class="form-control txt_dato_articulo__bonificaciones" required autofocus placeholder="Ingrese nombre del artículo">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_art_bon" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_articulo_busqueda_bonif" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE ARTÍCULOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Nombre del Articulo</td>
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


  <div class="modal fade" id="modal_informacion_liquidacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <form action="#" id="form_informacion_liquidacion" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-usd"></span> Información Adicional</h3>
          </div>
          <div class="modal-body">
            
            <nav class="navbar navbar-default" id="nav_ver_liquidacion" style="display: flex;">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer info_liquidacion active"><a><span class="fa fa-user red"></span>
                      Información</a></li>
                      <li class="pointer autores_liquidacion"><a><span class="fa fa-user red"></span>
                      Autores</a></li>
                      <li class="pointer gestores_liquidacion"><a><span class="fa fa-user red"></span>
                      Gestores</a></li>
                      <li class="pointer director_liquidacion"><a><span class="fa fa-user red"></span>
                      Director</a></li>
                    </ul>
                  </div>
            </nav>

            <div class="container_liquidacion_total oculto">
              <div class="alert alert-warning">
			        <p class="text-justify">
				        <span class="fa fa-usd" id="liquidacion_personas"></span>
              </p>
		        </div> 
            <div class="alert alert-info">
              <p class="text-justify">
                <strong><span class="fa fa-warning"></span> Tener en cuenta: </strong> Si modifica la información de esta solicitud, será necesario generar nuevamente las liquidaciones.
              </p>
            </div>

            </div>
            <div class="btn_ver_informacion_liq tabla_info_liquidacion Active" style="margin-bottom:20px;">
              <div class="alert alert-info">
                <div class="agro agrupado">
                  <div class="input-group">
                    <select name="cuart_liq_fin" id="cuart_liq_fin" required class="form-control cbxcuart_liq_fin">
                      <option value="">Cuartil de Liquidación Final</option>
                    </select>
                    <span class="input-group-addon">-</span>
                    <select name="cat_liq_fin" id="cat_liq_fin" required class="form-control cbxcat_liq_fin">
                      <option value="">Categoría Liquidación Final</option>
                    </select>
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button" id="btn_guardar_info_liq"><span class='fa fa-plus-circle red'></span>
                        Guardar
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              
              
              <table class="table table-bordered table-condensed">
                <tr>
                  <th class="nombre_tabla" colspan="4"></th>
                  <td class="sin-borde text-right border-left-none" colspan="4">
                    </td>
                  </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Titulo del Artículo</td>
                  <td class="titulo_arti_liq" colspan="6"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Revista</td>
                  <td class="revista_liq" colspan="2"></td>
                  <td class="ttitulo" colspan="2">ISSN</td>
                  <td class="issn_liq" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Cuartil de la Revista en Scopus</td>
                  <td class="rev_scopus_liq" colspan="2"></td>
                  <td class="ttitulo" colspan="2">Link Indexado Scopus </td>
                  <td class="link_scopus_liq" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Cuartil de la Revista en Web of Science</td>
                  <td class="rev_wos_liq" colspan="2"></td>
                  <td class="ttitulo" colspan="2">Link Indexado WoS</td>
                  <td class="link_wos_liq" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Autor Correspondencia</td>
                  <td class="correspondencia_liq" colspan="2"></td>
                  <td class="ttitulo" colspan="2">Autor con afiliación Internacional</td>
                  <td class="aut_inter_liq" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Aporte a visibilidad institucional</td>
                  <td class="visibilidad_inst_liq" colspan="2"></td>
                  <td class="ttitulo" colspan="2">Solución a Problema Local, Regional o Nacional </td>
                  <td class="solucion_prob_liq" colspan="2"></td>
                </tr>
              </table>
              
              <div class="alert">
                <p class="text-justify">
                  Coautoría con Estudiante(s): <strong id="respuesta_coaut_est"></strong>
                </p>
              </div>
              
              <table class="table table-bordered" id="table_categorias_liq_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA TIPOS DE ESCRITURAS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Tipo de escritura</td>
                    <td>Persona Registra</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              
              <div class="div_coaut_est_liq_bon oculto">
                <table class="table table-bordered table_cout_est_liq_bon" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="2" class="nombre_tabla">TABLA COAUTORIA ESTUDIANTES</td>
                      <td class="sin-borde text-right border-left-none" colspan="5"></td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">Documento</td>
                      <td>Nombres completos</td>
                      <td>Programa</td>
                    </tr>
                  </thead>
                  <tbody>
                    </tbody>
                </table>
              </div>
              
              <div class="div_coaut_ext_liq_bon oculto">
                <table class="table table-bordered table_cout_ext_liq_bon" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="2" class="nombre_tabla">TABLA COAUTORIA EXTERNOS</td>
                      <td class="sin-borde text-right border-left-none" colspan="5"></td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">Documento</td>
                      <td>Nombres completos</td>
                      <td>Institución</td>
                      <td>Pais</td>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="btn_ver_informacion_liq tabla_autores_liquidacion oculto" style="margin-bottom:20px;">
              <table class="table table-bordered" id="table_autores_liq_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA AUTORES</td>
                    <td class="sin-borde text-right border-left-none" colspan="5">
                      <span class="btn btn-default btnLiquidar" id="generar_liquidacion">
                      <span class="fa fa-check-square-o red"></span> Liquidar</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">Ver Detalle</td>
                    <td>Documento</td>
                    <td>Nombres completos</td>
                    <td>Liquidacion</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div class="btn_ver_informacion_liq tabla_gestores_liquidacion oculto" style="margin-bottom:20px;">
              <table class="table table-bordered" id="table_gestores_liq_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA GESTORES</td>
                    <td class="sin-borde text-right border-left-none" colspan="5">
                      <span class="btn btn-default btnLiquidar_gestor" id="generar_liquidacion_gestor">
                      <span class="fa fa-check-square-o red"></span> Liquidar</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <!-- <td class="opciones_tbl">Ver Detalle</td> -->
                    <td>Documento</td>
                    <td>Nombres completos</td>
                    <td>Liquidacion</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div class="btn_ver_informacion_liq tabla_directores_liquidacion oculto" style="margin-bottom:20px;">
              <table class="table table-bordered" id="table_directores_liq_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DIRECTOR</td>
                    <td class="sin-borde text-right border-left-none" colspan="5">
                      <span class="btn btn-default btnLiquidar_director" id="generar_liquidacion_director">
                      <span class="fa fa-check-square-o red"></span> Liquidar</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <!-- <td class="opciones_tbl">Ver Detalle</td> -->
                    <td>Documento</td>
                    <td>Nombres completos</td>
                    <td>Liquidacion</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer" id="footermodal">          
            <button type="button" class="btn btn-danger active" id="btn_aprobar_liquidacion" ><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_info_autor_liq" role="dialog">
    <div class="modal-dialog modal-lg">
      <form action="#" id="form_info_autor_liq" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-user"></span> Información Autor</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">

            <table class="table table-bordered table-condensed">
              <tr>
                <td class="ttitulo" colspan="2">Autor</td>
                <td class="nombre_autor_liq" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Categoría Investigador</td>
                <td class="categ_invest_liq" colspan="2"></td>
                <td class="ttitulo" colspan="2">Departamento</td>
                <td class="cargo_aut_liq" colspan="2"></td>
              </tr>
              <!-- <tr>
                <td class="ttitulo" colspan="2">Plan de Trabajo</td>
                <td class="issn_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Artículos Cumplidos con descuento PDT</td>
                <td class="doi_ver_bon" colspan="2"></td>
              </tr> -->
              <tr>
                <td class="ttitulo" colspan="2">Productividad destinada a PDT</td>
                <td class="porc_pdt" colspan="2"></td>
                <td class="ttitulo" colspan="2">Productividad destinada a Bonificación</td>
                <td class="porc_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Base de Liquidacion</td>
                <td class="base_liquid" colspan="2"></td>
                <td class="ttitulo" colspan="2">Bonificación Base</td>
                <td class="bonific_base_liq" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Coautoría con Estudiantes</td>
                <td class="coaut_est_liq" colspan="2"></td>
                <td class="ttitulo" colspan="2">Aporte a visibilidad institucional</td>
                <td class="aport_vis_liq" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Solución a Problemática Local, Regional o Nacional</td>
                <td class="sol_prob_liq" colspan="2"></td>
                <td class="ttitulo" colspan="2"><strong>Total Liquidación</strong></td>
                <td class="total_liq" colspan="2"></td>
              </tr>
            </table>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <?php if ($sw_super) { ?>
              <span class="btn btn-default btn_cambiar_porc" id="cambiar_porcentaje_aut"><span class="fa fa-exchange red"></span> Cambiar Porcentaje</span>
            <?php } ?>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_mod_porcentajes_dir" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Porcentajes</h3>
      </div>
      <form id="form_mod_porcentajes_dir" enctype="multipart/form-data" method="post">
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Porcentaje de productividad del Autor en el Artículo
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="first_porcentage_cp" id="first_porcentage_cp" data-ide="first_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Porcentaje de productividad destinada a cumplimiento de Plan de Trabajo (PDT)
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="second_porcentage_cp" id="second_porcentage_cp" data-ide="second_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
              <div class="alert" role="alert" for="chkelementos">
                <label class="form-check-label" for="chkelementos">
                  * Productividad destinada a bonificación
                </label>
                <input type="text" value="" class="form-control text-center autores_porcen" name="third_porcentage_cp" id="third_porcentage_cp" data-ide="third_porcentage" placeholder="Ingrese el procenaje correspondiente" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="botton" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form id="form_buscar_revista_bon" method="post">
  <div class="modal fade" id="modal_buscar_revista_bon" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Revista</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" id="btn_nueva_revista_bon"><span class='fa fa-plus-circle red'></span>
                    Crear
                  </button>
                </span>
                <input id='txt_dato_revista_bon' class="form-control txt_dato_revista_bon" placeholder="Ingrese el nombre de la revista">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_revista_busqueda_bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE REVISTAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre de la revista</td>
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

<form id="form_buscar_proyecto__bon" method="post">
  <div class="modal fade" id="modal_buscar_proyecto__bon" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Proyecto</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
          <div class="alert alert-info">
              <p class="text-justify">
                <strong><span class="fa fa-warning"></span> Tener en cuenta: </strong> Si no encuentra el proyecto, por favor <strong><a id="btn_create_project">Agreguelo Aquí</a></strong>
              </p>
            </div>
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_dato_proyecto__bon' class="form-control txt_dato_proyecto__bon" placeholder="Ingrese nombre del proyecto o del investigador">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_proyecto_busqueda__bon" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE PROYECTO</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Nombre del Proyecto</td>
                    <td>Investigador</td>
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

<div class="modal fade" id="modal_create_new_project" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_create_new_project" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus-square-o"></span> Asignar Nuevo Proyecto</h3>
          </div>
          <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="agro agrupado">
              <input type="text" class="form-control" name='title_project' placeholder='Titulo Proyecto'></input>
            </div>  
            <div class="agro agrupado">
              <input type="text" class="form-control" name='project_serial' placeholder='Código del proyecto'></input>
            </div>  
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha Inicio</span>
                <input type="date" class="form-control sin_margin" value="" name="Project_date_initial">
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha Finalización</span>
                <input type="date" class="form-control sin_margin" value="" name="Project_date_end">
              </div>
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

<form id="form_listar_paises" method="post">
  <div class="modal fade" id="modal_listar_paises" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Paises</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="form-group agrupado col-md-8 text-left">
            <div class="input-group">                
              <input id='txt_pais_buscado' class="form-control txt_pais_buscado" placeholder="Buscar Pais">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
              </span>
            </div>
          </div>
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="table_list_countries" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA DE PAISES</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>No.</td>
                    <td>Pais</td>
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

<div class="modal fade" id="modal_almacenar_revista__bonificaciones" role="dialog">
  <div class="modal-dialog">
    <form id="form_almacenar_revista_bonificaciones" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span><span class="fa fa-book"></span> Crear revista</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <h6 class="ttitulo">
              <span class="glyphicon glyphicon-indent-left"></span> Datos de la revista
            </h6>
            <input type="text" class="form-control" name="nombre_revista" id="text_nombre_revista__bon" placeholder="Nombre de la revista" required="true">
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="issn_rev__bon" id="txt_issn_rev__bon" class="form-control inputt2" placeholder="ISSN">
                <span class="input-group-addon">-</span>
                <select name="cuartil_rev__bon" id="pub_cuartil_select__bon" required class="form-control cbxcuartiles">
                  <option value="">Seleccione Cuartil</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>



<!-- <div class="modal fade" id="modal_administrar" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button class="close" type="button" data-dismiss="modal"> X<button>
              <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
        </div>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_pub">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" id="admin_rev"><span class="fa fa-book red"></span> Revistas</li>
            </ul>
          </div>
        </nav>
        <div id="container_admin_pub">
          <div></div>
        </div>
      </div>
    </div>
  </div> -->

<!-- </div> -->

<style>
  .funkyradio input[type="checkbox"]:empty~label:before {
    display: inline-flex !important;
  }
</style>

<script>
  var base_url = "<?php echo base_url(); ?>";
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

<script type="text/javascript">
  $(".date__indexing").datetimepicker({
    format: 'yyyy',
    autoclose: true,
    todayBtn: true,
    startView: 4,
    maxView: 4, 
    minView: 4,
    endDate: '+0d', // deshabilita años posteriores al actual
  }).attr('readonly', 'readonly'); // el attr evita que el usuario pueda escribir la fecha, dejando solo selección
</script>
<script>
  $(document).ready(function() {
    Cargar_parametro_buscado(91, ".cbxdepartamento", "Seleccione el departamento");
    Cargar_parametro_buscado(78, ".cbxgrupoinv", "Seleccione el grupo de investigación");
    Cargar_parametro_buscado(87, ".cbxlineainv", "Seleccione la linea de investigación");
    //Cargar_parametro_buscado(88, ".cbxsublineainv", "Seleccione la sublinea de investigación");
    Cargar_parametro_buscado(86, ".cbxprog_est", "Seleccione el programa academico");
    //Cargar_parametro_buscado(86, ".cbxprog_acad_bon", "Seleccione el programa academico");
    //Cargar_parametro_buscado(288, "#inst_ext_bon", "Seleccione la institución externa");
    
    Cargar_parametro_buscado_aux(296, ".cbxafil_bon", "Seleccione el tipo de afiliación");
    Cargar_parametro_buscado(297, ".cbxcatminciencias", "Seleccione la categoría de investigador (Minciencias)");
    Cargar_parametro_buscado(283, ".cuartil_autor", "Seleccione el cuartil");
    Cargar_parametro_buscado(283, ".cuartil_autor_cump", "Seleccione el cuartil");
    Cargar_parametro_buscado(1, ".cbxtipo_documento", "Seleccione tipo de documento");
    Cargar_parametro_buscado(300, ".cbxcategoria", "Seleccione una categoria");
    Cargar_parametro_buscado(283, ".cbxcuartiles_scopus", "Seleccione Cuartil en Scopus");
    Cargar_parametro_buscado(283, ".cbxcuartiles_wos", "Seleccione Cuartil en Web of Science");
    Cargar_parametro_buscado(3, ".cbx_id_departamento", "Seleccione un departamento");
    Cargar_parametro_buscado(86, ".cbx_id_programa", "Seleccione un programa");
    Cargar_parametro_buscado(314, ".cbxsods", "Seleccione un ODS");
    //Cargar_parametro_buscado(251, ".cbxpaises", "Seleccione un pais");
    
    //inactivityTime();
    Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
    listar_indicador();
    listar_ranking();
    listar_estados_filt();
    
    <?php if ($sw) { ?>
      listar_publicaciones('<?php echo $id ?>');
      ver_notificaciones();
    <?php } ?>
    $('[data-toggle="popover"]').popover();
    activarfile();
    recibir_archivos();
  });
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
