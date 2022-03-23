<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Admin_Cont") {
  $sw = true;
  $sw_super = true;
}
?>
<div class="container col-md-12 text-center" id="inicio-user">
  <div class="tablausu lista_contratos col-md-12 text-left oculto">
    <div class="table-responsive col-sm-12 col-md-12  tablauser">
      <p class="titulo_menu pointer" id="regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_contra" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="3" class="nombre_tabla"> TABLA CONTRATACIONES
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="5">
              <?php if ($sw) : ?>
                <span class="btn btn-default" id="btn_notificaciones" data-toggle="modal" data-target="#modal_notificaciones">
                  <span class="badge" id="noti_n" style="background-color: #6e1f7c;">0</span> Notificaciones
                </span>
              <?php endif; ?>
              <?php if ($sw) : ?>
                <span class="btn btn-default btnAgregar" id="btn_admin_solicitudes"><span class="fa fa-cogs red"></span> Administrar</span>
              <?php endif; ?>
              <span class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#modal_filtrar">
                <span class="fa fa-filter red"></span> Filtrar
              </span>
              <span class="btn btn-default" id="limpiar_filtros_conts">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo</td>
            <td>Solicitante</td>
            <td>Fecha de inicio</td>
            <td>Fecha de finalización</td>
            <td>Estado</td>
            <td style='width:15%;'>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tablausu col-md-12" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="nuevo_contrato" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn form-control">Nuevo contrato</span>
            </div>
          </div>
        </div>
        <?php if ($sw) : ?>
          <div id="nueva_prorroga" class="pointer">
            <div class="thumbnail">
              <div class="caption">
                <img src="<?php echo base_url() ?>/imagenes/Viaticos_Transporte.png" alt="...">
                <span class="btn form-control">Nueva prorroga</span>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <div class="" id="lista_contratos">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control">Lista de contratos</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
    </div>
  </div>

  <!-- Modales -->
  <!-- Formulario para nueva prorroga -->
  <form id="form_nueva_prorroga" method="post">
    <div class="modal fade scroll-modal" id="modal_nueva_prorroga" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Prorroga</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer btn_buscar_numcontra_prorroga" id="btn_buscar_numcontra_prorroga" style='background-color:white'><span class='fa fa-list-ol red'></span> Nº Contrato</span>
                  <input type="text" class="form-control sin_margin txt_contrato sin_focus" id="num_contrato_prorroga" name="num_contrato_prorroga" placeholder="Ingrese el número del contrato..." data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Busque su contrato" required>
                </div>
              </div>

              <div class="clearfix"></div>

              <div id="adjs_tipo_garantia" class="agro agrupado"></div>

              <div class="agro agrupado">
                <div class="input-group d-flex">
                  <span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha de Inicio</span>
                  <input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de inicio." class="form-control sin_margin" name="fecha_inicio_prorroga" id="fecha_inicio_prorroga" required>
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha de Terminacíon</span>
                  <input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de terminación." class="form-control sin_margin" name="fecha_termina_prorroga" id="fecha_termina_prorroga" required>
                </div>
              </div>

              <!-- Inputs de carga de archivos -->

              <div id="adjs_container_prorroga" class="agro agrupado">
                <div class="agrupado">
                  <div class="input-group">
                    <label class="input-group-btn">
                      <span class="btn btn-primary">
                        <span class="fa fa-folder-open"></span>
                        Buscar <input type="file" style="display: none;" id='prorroga_adj' name='prorroga_adj' accept=".pdf">
                      </span>
                    </label>
                    <input type="text" class="form-control" id="prorroga_adj_name" data-text=prorroga_adj_name" readonly placeholder='Prorroga' value=''>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>

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

  <!-- Modal buscar contrato -->
  <form id="form_buscar_contrato" method="post">
    <div class="modal fade" id="modal_buscar_contrato" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Contrato</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_id_contra' class="form-control txt_dato_contra" required placeholder="Ingrese el codigo del contrato">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_contra" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_contrato_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Ver</td>
                      <td>Contrato</td>
                      <td>Solicitante</td>
                      <td>Contratista</td>
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
  <!-- Modal para detalles del contrato -->
  <div class="modal fade" id="modal_detalle_contrato" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalles del contrato</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <?php if ($sw || $perm) : ?>
            <nav class="navbar navbar-default" id="nav_admin_compras">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer btnEstados" data-toggle="modal" data-target="#modal_estados"><a><span class="fa fa-history red"></span> Historial</a></li>
                  <li class="pointer btnArchivos"><a><span class="fa fa-file red"></span> Archivos</a></li>
                  <li class="pointer download_contrato"><a><span class="fa fa-file-pdf-o red"></span> Contrato</a></li>
                  <li class="pointer download_prorroga" style="display: none;"><a><span class="fa fa-file-pdf-o red"></span> Prorroga</a></li>
                </ul>
              </div>
            </nav>
          <?php endif; ?>
          <div class="table">
            <!-- Auto gen -->
            <table class="table table-condensed tabla_contrataciones">
              <tr>
                <td class="ttitulo contratom w-auto">Persona solicitante</td>
                <td class="solicitante_space w-auto"></td>
              </tr>
              <tr class="tr_conv">
                <td class="ttitulo contratom w-auto">Contrato</td>
                <td class="contratom_space w-auto"></td>
              </tr>
              <tr class="tr_contra">
                <td class="ttitulo valor w-auto">Valor</td>
                <td class="valor_space w-auto"></td>
              </tr>
              <tr class="tr_pg oculto">
                <td class="ttitulo num_contrato w-auto">Número de contrato (AUTO GENERADO)</td>
                <td class="num_contrato_space w-auto"></td>
              </tr>
              <tr class="codSAP_tr">
                <td class="ttitulo codSAP w-auto">Código SAP</td>
                <td class="codSAP_space w-auto"></td>
              </tr>
              <tr class="tr_contra">
                <td class="ttitulo contratante w-auto">Contratante</td>
                <td class="tante_space w-auto"></td>
              </tr>
              <tr class="tr_contra">
                <td class="ttitulo contratista w-auto">Contratista</td>
                <td class="tista_space w-auto"></td>
              </tr>
              <tr class="tr_contra">
                <td class="ttitulo cedunit w-auto">Cédula/Nit</td>
                <td class="cedunit_space w-auto"></td>
              </tr>
              <tr class="tr_contra">
                <td class="ttitulo objetivo w-auto">Objetivo</td>
                <td class="objetivo_space w-auto"></td>
              </tr>
              <tr class="tr_conv">
                <td class="ttitulo garantia w-auto">Garantía</td>
                <td class="garantia_space w-auto"></td>
              </tr>
              <tr class="tr_conv">
                <td class="ttitulo tipo_pers w-auto">Tipo de persona</td>
                <td class="tipo_pers_space w-auto"></td>
              </tr>
              <tr>
                <td class="ttitulo fechasus w-auto">Fecha de suscripción</td>
                <td class="fechasus_space w-auto"></td>
              </tr>
              <tr>
                <td class="ttitulo fechaini w-auto">Fecha de inicio</td>
                <td class="fechaini_space w-auto"></td>
              </tr>
              <tr>
                <td class="ttitulo fechafin w-auto">Fecha de finalización</td>
                <td class="fechafin_space w-auto"></td>
              </tr>
              <tr class="tr_conv">
                <td class="ttitulo plazo w-auto">Plazo</td>
                <td class="plazo_space w-auto"></td>
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

  <!-- Modal para el historial de contratos y sus estados -->
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
                  <td>Fecha Registra</td>
                  <td>Usuario gestiona</td>
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

  <!-- Modal para filtrar por fecha los contratos -->
  <div class="modal fade" id="modal_filtrar" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_filtro" name="form_filtro">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="col-sm-auto" style="width:auto; padding: 0 0;">
                <span>Fecha de inicio: </span>
                <input class="form-control filtro" type="date" name="fecha_i">
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-filter"></span> Generar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Formulario para nuevo contrato -->
  <form id="form_nuevo_contrato" method="post">
    <div class="modal fade scroll-modal" id="modal_nuevo_contrato" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Nuevo contrato</h3>
          </div>
          <div class="modal-body " id="bodymodal">
            <div class="row">

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer btn_buscar_codsap" style='background-color:white'><span class='fa fa-search red'></span> COD. SAP</span>
                  <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Aquí puede buscar el Codigo Sap requerido." class="form-control sin_margin sin_focus conv_inputs" data-codsap_id="" data-tipo_cont="" name="num_sap" id="num_sap" placeholder="Buscar codigo SAP" required>
                </div>
              </div>

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer btn_buscar_numcontra" id="btn_buscar_numcontra" style='background-color:white'><span class='fa fa-list-ol red'></span> Nº Contrato</span>
                  <input type="text" class="form-control sin_margin txt_contrato sin_focus" id="num_contrato" name="num_contrato" placeholder="Ingrese el nuevo número de contrato..." data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Busque su contrato macro o número de presupuesto." required>
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="agro agrupado conv_div_padre">
                <div class="agrupado">
                  <select class="form-control conv_inputs" name="tante_select" id="tante_select" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione al contratante." required>
                    <option value="" selected default>Seleccione al contratante</option>
                  </select>
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer btn_buscar_contratista" style='background-color:white'><span class='fa fa-search red'></span> Contratista</span>
                  <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Aquí puede buscar al Contratista." data-tista="" class="form-control sin_margin sin_focus" name="contratista" id="contratista" placeholder="Buscar contratista" required>
                </div>
              </div>

              <div class="clearfix"></div>

              <textarea class="form-control" id="objetivo" name="objetivo" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Describa datos tales como: la asignatura, grupo/periodo, numero de horas, valor hora catedra, valor del contrato, etc." placeholder="Objeto del contrato!"></textarea>

              <div class="clearfix"></div>

              <input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Valor del pago - solo se aceptan números en este campo." class="form-control rounded input_numerico dinero conv_inputs" style="border-radius: 3px;" name="pago_valor" id="pago_valor" required placeholder="Valor del contrato">

              <textarea class="form-control" id="plazo" name="plazo" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Escriba el plazo determinado." placeholder="Escriba aquí el plazo determinado!"></textarea>

              <div class="clearfix"></div>

              <select class="form-control" name="tipo_persona" id="tipo_persona">
                <option value="0">Seleccione tipo de persona</option>
              </select>

              <select class="form-control" name="contrato_garantia" id="contrato_garantia" required>
                <option value="0">Seleccione garantia del contrato</option>
              </select>

              <div class="clearfix"></div>

              <div id="adjs_tipo_garantia" class="agro agrupado"></div>

              <div class="agro agrupado">
                <div class="input-group d-flex">
                  <span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha de inicio contrato</span>
                  <input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de inicio." class="form-control sin_margin" name="fecha_inicio" id="fecha_inicio" required>
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha de Terminacíon</span>
                  <input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de terminación." class="form-control sin_margin" name="fecha_termina" id="fecha_termina" required>
                </div>
              </div>

              <!-- Inputs de carga de archivos -->

              <div id="adjs_container" class="agro agrupado"></div>
              <div class="clearfix"></div>

              <div id="otros_adjs" class="agro agrupado">
                <span data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione otros archivos adjuntos" class="form-control otros_adjuntos" style="cursor:pointer">
                  <span class="fa fa-folder-open"></span> Añadir otro adjunto
                </span>
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

  <!-- Modal buscar codigo SAP -->
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

  <!-- Modal buscar numero de contrato -->
  <form id="form_buscar_numcontra" method="post">
    <div class="modal fade" id="modal_buscar_numcontra" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span>Buscar Contrato Macro</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_numcontra' class="form-control txt_dato_numcontra" required placeholder="Número de Contrato...">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_numcontra" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_numcontra_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Nº</td>
                      <td>Contrato Macro</td>
                      <td>Entidad</td>
                      <td>Codigo SAP</td>
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

  <!-- Modal buscar contratista -->
  <form id="form_buscar_contratista" method="post">
    <div class="modal fade" id="modal_buscar_contratista" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Contratistas</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_contratista' class="form-control txt_dato_contratista" required placeholder="Contratista...">
                  <span class="input-group-btn">
                    <button class="btn btn-default btn_busc_contratista" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_contratista_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA DE RESULTADOS</td>
                      <td class="btnAgregar sin-borde text-center" rowspan="1" colspan="1">
                        <span data-toggle="modal" data-target="#modal_nuevo_contratista" class="btn btn-default"><span class="fa fa-plus red"></span> Nueva</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Nº</td>
                      <td>Nombre</td>
                      <td>Cédula/Nit</td>
                      <td>Correo</td>
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

  <!-- Modal notificaciones -->
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

  <!-- Modal administrar contratos -->
  <?php if ($sw) { ?>
    <div class="modal fade" id="modal_administrar_contratos" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <nav class="navbar navbar-default" id="nav_admin_compras">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer" id="admin_permisos"><a><span class="fa fa-gears red"></span> Permisos</a></li>
                  <li class="pointer" id="admin_tista"><a><span class="fa fa-users red"></span> Contratistas</a></li>
                  <li class="pointer" id="admin_ncm"><a><span class="fa fa-file-text red"></span> Contratos Macro</a></li>
                </ul>
              </div>
            </nav>
            <div class="permisos adm_proceso active" id="adm_permi">
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
                      <tr>
                        <td class="nombre_tabla" colspan="4">TABLA PROCESOS</td>
                      </tr>
                      <tr class="filaprincipal ">
                        <td class="opciones_tbl">No.</td>
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

            <!-- Tabla de admin personas -->
            <div class="table-responsive">
              <div id="container_permisos_admin" class="oculto">
                <table id="tabla_actividades" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td class="nombre_tabla" colspan="4">TABLA PROCESOS</td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">No.</td>
                      <td>Nombre</td>
                      <td class="opciones_tbl_btn">Acción</td>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tabla de admin contratistas -->
            <div class="table-responsive">
              <div id="container_admin_tistas" class="oculto">
                <table class="table table-bordered table-hover table-condensed" id="tabla_adm_tistas" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="4" class="nombre_tabla" id="nombre_tabla_cu_or">TABLA CONTRATISTAS</td>
                      <td class="btnAgregar sin-borde text-center">
                        <?php if ($sw || $perm): ?>
                          <span data-toggle="modal" data-target="#modal_nuevo_contratista" class="btn btn-default"><span class="fa fa-plus red"></span>
                            Nueva</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td>***</td>
                      <td>Nombre</td>
                      <td>Cédula/Nit</td>
                      <td>Correo Electónico</td>
                      <td class="opciones_tbl_btn">***</td>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>

              <!-- Tabla para administrar contratos macro -->
              <div id="container_admin_ncm" class="oculto">
                <table class="table table-bordered table-hover table-condensed" id="tabla_adm_ncm" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="3" class="nombre_tabla" id="nombre_tabla_cu_or">TABLA CONTRATOS MACRO</td>
                      <td class="btnAgregar sin-borde text-center">
                        <span data-toggle="modal" data-target="#modal_nuevo_valor" class="btn btn-default"><span class="fa fa-plus red"></span>
                          Nueva</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td>***</td>
                      <td>Nombre</td>
                      <td>Descripción</td>
                      <td class="opciones_tbl_btn">***</td>
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
  <?php  } ?>

  <!-- Modal detalle contratos -->
  <div class="modal fade con-scroll-modal" id="modal_detalle_parametro" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Información General</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed  margin1 ajustar" id="tabla_detalle_traslado_comite">
              <tr class="">
                <th class="nombre_tabla" colspan="5"> Datos</th>
              </tr>
              <tr>
                <td class="ttitulo"> Nombre: </td>
                <td id="nombre_parametro"></td>
              </tr>
              <tr>
                <td class="ttitulo"> Descripción: </td>
                <td id="descripcion_parametro"></td>
              </tr>
              <tr class='tr_valory'>
                <td class="ttitulo" id='text_valory'> </td>
                <td id="valory_parametro"></td>
              </tr>
              <tr class='tr_valory'>
                <td class="ttitulo" id='text_valory_des'></td>
                <td id="des_valory_parametro"></td>
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

  <?php if ($sw) { ?>
    <!-- Modal nuevo valor -->
    <div class="modal fade" id="modal_nuevo_valor" role="dialog">
      <div class="modal-dialog">
        <form action="#" id="form_guardar_valor_parametro" method="post">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-truck"></span> Nueva Orden</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <div class="row">
                <div id="container_costo"></div>
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
  <?php } ?>

  <?php if ($sw) : ?>
    <!-- Modal modificar valor -->
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
                <input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre" name="nombre" required>
                <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripción" name="descripcion" required></textarea>
              </div>
            </div>
            <div class="modal-footer" id="footermodal">
              <button type="submit" class="btn btn-danger active btnModifica"><span class="glyphicon glyphicon-floppy-disk"></span>Modificar</button>
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>


  <!-- Modal para visualizar archivos -->
  <div class="modal fade" id="modal_archivos_gestion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="table-responsive">
            <table id="tabla_adjs_cont" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <th colspan="3" class="nombre_tabla">TABLA ADJUNTOS</th>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl_btn">VER</td>
                  <td>Nombre Real</td>
                  <td>Nombre de guardado</td>
                  <td>Fecha de guardado</td>
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

  <!-- Modal buscar personas para otorgar permisos -->
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
              <input type="text" class="form-control sin_margin" required="true" id='txt_persona' placeholder="Buscar Persona" />
              <span type="submit" class="input-group-addon pointer" id='btn_buscar_persona' style='	background-color:white'><span class='fa fa-search red'></span></span>
            </div><br>
          </form>
          <table id="tabla_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="3" class="nombre_tabla">TABLA PERSONAS</th>
              </tr>
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

  <!-- Modal elegir estado -->
  <div id="modal_elegir_estado" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <table id="tabla_elegir_estados" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
              </tr>
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

  <div class="modal fade" id="modal_solicitar_firma" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_solicitar_firma" method="post">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-pencil"></span> Firma</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <p class='text-justify'><b>Una vez el contratista y/o proveedor de bienes y servicios le de aceptar en la plataforma, acepta todo los terminos y condiciones establecidos en el documento y se entiende sucrito con el envio de la aceptación.</b></p>
              <div class="funkyradio" style="width: 100%;">
                <div class="funkyradio-success" style="display: inline-block;">
                  <input type="checkbox" id="check_contra" name="check_contra" value="1">
                  <label for="check_contra" title="Con cuenta"> Acepto.</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-check"></span> Aceptar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal adjuntar contrato -->
  <?php if ($sw || $perm) : ?>
    <div class="modal fade" id="modal_adjuntar_contrato" role="dialog">
      <div class="modal-dialog">
        <form action="#" id="form_adjuntar_contrato" method="post">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-truck"></span> Adjuntar contrato</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <div class="input-group">
                <label class="input-group-btn">
                  <span class="btn btn-primary">
                    <span class="fa fa-folder-open"></span>
                    Buscar <input type="file" style="display: none;" id="adjs_contrato" name="adjs_contrato" accept=".pdf">
                  </span>
                </label>
                <input type="text" class="form-control" id="Contrato" data-text="adjs_contrato" readonly="" placeholder="Contrato" value="">
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
  <?php endif; ?>

  <!-- Modal tasks -->
  <?php if ($sw || $perm) : ?>
    <div class="modal fade" id="modal_tareas" role="dialog">
      <div class="modal-dialog">
        <form action="#" id="form_tareas" method="post">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-truck"></span> Tareas</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <ul class="list-group"></ul>
            </div>
            <div class="modal-footer" id="footermodal">
              <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($sw || $perm) { ?>
    <!-- Modal nuevo contratista -->
    <div class="modal fade" id="modal_nuevo_contratista" role="dialog">
      <div class="modal-dialog">
        <form action="#" id="form_guardar_nuevo_contratista" method="post">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-truck"></span> Nuevo contratista</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <div class="row">
                <div id="container_costo"></div>
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-user red"></i>
                    <span>Apellido, Nombre*</span>
                  </span>
                  <input type="text" name="nombre" class="form-control sin_margin inputt h1" placeholder="Apellido, Nombre" required>
                </label>
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-legal red"></i>
                    <span>Cédula/Nit*</span>
                  </span>
                  <input type="text" name="cc_nit" class="form-control sin_margin input-number h1" placeholder="Cédula/Nit" required>
                </label class="input-group">
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-envelope red"></i>
                    <span>Correo*</span>
                  </span>
                  <input type="email" name="correo" class="form-control sin_margin inputt h1" placeholder="Correo" required>
                </label>
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
  <?php } ?>

  <?php if ($sw || $perm) { ?>
    <!-- Modal nuevo contratista -->
    <div class="modal fade" id="modal_modificar_contratista" role="dialog">
      <div class="modal-dialog">
        <form action="#" id="form_modificar_contratista" method="post">
          <input type="hidden" name="id">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-truck"></span> Modificar contratista</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <div class="row">
                <div id="container_costo"></div>
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-user red"></i>
                    <span>Apellido, Nombre*</span>
                  </span>
                  <input type="text" name="nombre" class="form-control sin_margin inputt h1" placeholder="Apellido, Nombre" required>
                </label>
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-legal red"></i>
                    <span>Cédula/Nit*</span>
                  </span>
                  <input type="text" name="cc_nit" class="form-control sin_margin input-number h1" placeholder="Cédula/Nit" required>
                </label class="input-group">
                <label class="input-group">
                  <span class="input-group-addon pointer" style="background-color:white">
                    <i class="fa fa-envelope red"></i>
                    <span>Correo*</span>
                  </span>
                  <input type="email" name="correo" class="form-control sin_margin inputt h1" placeholder="Correo" required>
                </label>
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
  <?php } ?>
</div>

<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>
<script>
  $(document).ready(function() {
    inactivityTime();
    activarfile();
    $('[data-toggle="popover"]').popover();
    <?php if ($sw || ($perm || $id > 0)): ?>
      listar_contratos(<?=$id?>);
      <?php if ($sw): ?>
        ver_notificaciones();
      <?php endif; ?> 
    <?php endif; ?>    
  });
</script>