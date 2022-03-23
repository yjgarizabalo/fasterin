<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php $sw  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Com" ?  true : false; ?>
<div class="container col-md-12 text-center" id="inicio-user" >
<div class="tablausu col-md-12 text-left <?php echo $sw || !empty($id) ? '' :'oculto' ?>" id="container-listado-eventos">
    <div class="table-responsive col-sm-12 col-md-12">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr>
            <tr class=""><td colspan="3" class="nombre_tabla">TABLA SOLICITUDES <br><span class="mensaje-filtro oculto">
              <span class="fa fa-bell red"></span> <span id='mensaje_filtro'>La tabla tiene algunos filtros aplicados.</span></span></td><td class="sin-borde text-right border-left-none" colspan="3" > 
              <?php if($sw){?><span class="btn btn-default" id="btn_notificaciones_com"  ><span class="badge btn-danger n_notificaciones_com">0</span> Notificaciones</span><?php }?>
              <!-- <span class="btn btn-default btnModifica " id="btn_modificar"><span class="fa fa-wrench red"></span> Modificar</span>   -->
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_crear_filtros"> <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="limpiar_filtros"> <span class="fa fa-refresh red" ></span> Limpiar</span></td>
            </tr>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo</td>
            <td>Solicitante</td>
            <td>Fecha Registro</td>
            <td>Estado</td>
            <td style= "width:200px;max-width:250px;">Acción</td>
            <td>Gestion</td>
            <td>Tiempo Demora</td>
            <td>Calificacion</td>
            <td>Obs Calificacion</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <div class="tablausu col-md-12 <?php echo !$sw && empty($id)? '' :'oculto' ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="agregar_evento">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/comunicaciones_eventos.png" alt="...">
              <span id="titulo_evento" class="btn form-control btn-Efecto-men" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content=""></span>
            </div>
          </div>
        </div>
        <div id="agregar_divulgacion">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/comunicaciones_divulgacion.png" alt="...">
              <span id="titulo_divulgacion" class="btn  form-control btn-Efecto-men" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content=""></span>
            </div>
          </div>
        </div>
        <div id="agregar_publicidad">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/comunicaciones_publicidad.png" alt="...">
              <span id="titulo_publicidad" class="btn  form-control btn-Efecto-men" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content=""></span>
            </div>
          </div>
        </div>
        <div id="agregar_cubrimiento">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/comunicaciones_cubrimiento.png" alt="...">
              <span id="titulo_cubrimiento" class="btn  form-control btn-Efecto-men" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content=""></span>
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
</div>

<div class="modal fade" id="modal_notificaciones" role="dialog">
  <div class="modal-dialog" >
      <!-- Modal content-->
      <div class="modal-content" >
          <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
          </div>
          <div class="modal-body" id="bodymodal" >
              <div id="panel_notificaciones" style="width: 100%" class="list-group">
              </div>
          </div>
          <div class="modal-footer" id="footermodal">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
      </div>
  </div>
</div>

<form id="form_agregar_solicitud" method="post">
  <div class="modal fade scroll-modal" id="modal_agregar_solicitud" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Solicitud</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row"> 
          <!-- 
          <div class="funkyradio presupuesto">
            <div class="funkyradio-success solicitud">
              <input type="radio" id="presu1" name="presupuesto" value="1"/>
              <label for="presu1" title="Con codigo Sap"> Tengo presupuesto</label>
            </div>
            <div class="funkyradio-danger solicitud">
              <input type="radio" id="presu2" name="presupuesto" value="0" />
              <label for="presu2" title="Sin codigo Sap"> No tengo presupuesto</label>
            </div>
          </div>
          -->
            <div class="agro" id='nombre_evento'>
                <input type="text" class="form-control sin_margin " name="nombre_evento" placeholder="Nombre del Evento">
            </div>
            <!-- <div id=ocultar_div> -->
            <input type="text" class="form-control" name="id_codigo_sap" placeholder="Codigo SAP" required id="id_codigo_sap">
            <div class='fechas'>
            <div id='fechas_nueva_f1' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            <div id='fechas_nueva_f2' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha2" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha2" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            <div id='fechas_nueva_f3' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha3" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha3" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            <div id='fechas_nueva_f4' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha4" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha4" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            </div>
              <select name="id_tipo_evento"  id="cbx_tipo_evento" required class="form-control inputt cbx_tipo_evento CampoGeneral">
                  <option value="">Seleccione Tipo Evento</option>
              </select>
              <div class="agro">
                <div class="input-group">
                    <input type="text" class="form-control requerido sin_margin" name="nombre_lugar" placeholder="Bloque - Lugar" required>
                    <span class="input-group-addon">-</span>
                    <input type="text" class="form-control  requerido sin_margin" name="direccion" placeholder="Salon - Direccion" required>
                </div>
              </div>
              <div class="agro">
                <div class="input-group">
                    <input type="number" step="1" min="1" class="form-control sin_margin" name="telefono" placeholder="Telefono" required>
                    <span class="input-group-addon">-</span>
                    <input type="number" step="1" min="1" class="form-control requerido sin_margin" name="nro_invitados" placeholder="Nro. aprox. de invitados" required>
                </div>
              </div>
              <!-- </div>                 -->
            <select name="id_categoria_divulgacion"  id="cbx_cat_divulgacion" required class="form-control inputt cbx_cat_divulgacion CampoGeneral">
                <option value="">Seleccione Categoria</option>
            </select>
            <textarea class="form-control" cols="1" rows="3" name="descripcion" placeholder="Descripcion"></textarea>
            <br>
            <div class="alert alert-info oculto" id="info_categoria" role="alert" >
            </div>

            <div class="btn-group btn-group-justified" role="group" aria-label="...">
              <div class="btn-group" role="group">
              <button id="agregar_servicios" type="button" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-plus-sign red"></span> Agregar servicios</button>
              </div>
              <div class="btn-group" role="group">
              <button id="conadjuntos" type="button" class="btn btn-default active btn-block"><span class="fa fa-folder-open red"></span> Adjuntar soportes</button>
              </div>
            </div>
          </div>
        </div> 
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>

      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la solicitud</h3>
      </div>
      <div  class="modal-body" id="bodymodal">
          <div class="table-responsive" >
            <table class="table table-responsive table-condensed table-bordered">   
              <div > 
                <tr>
                  <th class="nombre_tabla" colspan="4">Información de la Solicitud</th>
                </tr>   
                <tr class="codigo_s">
                  <td class="ttitulo oculto_cs" colspan="2">Codigo Sap</td>
                  <td class="id_codigo_sap oculto_cs" colspan="2"></td> 
                </tr> 
                <tr>
                  <td class="ttitulo oculto_nombre" colspan="2">Nombre Evento</td>
                  <td class="nombre_evento oculto_nombre" colspan="2"></td> 
                </tr> 
                <tr>
                  <td class="ttitulo" colspan="2">Tipo de Solicitud</td>
                  <td class="tipo_solicitud" colspan="2"></td> 
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Estado</td>
                  <td class="estado_solicitud" colspan="2"></td>
                </tr> 
                <tr class="tr_msj_negado">
                  <td class="ttitulo" colspan="2">Detalle Estado</td>
                  <td class="msj_negado" colspan="2"></td>
                </tr> 
                <tr class="tr_diseno">
                  <td class="ttitulo" colspan="2">Correo Diseñador</td>
                  <td class="correo_diseno" colspan="2"></td>
                </tr> 
                <tr class="tr_diseno">
                  <td class="ttitulo" colspan="2">Descripción Diseño</td>
                  <td class="msj_tramite" colspan="2"></td>
                </tr> 
                <tr class="tr_califica">
                  <td class="ttitulo" colspan="2">Calificación</td>
                  <td class="calificacion" colspan="2"></td>
                </tr> 
                <tr class="tr_califica">
                  <td class="ttitulo" colspan="2">Observaciones Calificación</td>
                  <td class="obs_califica" colspan="2"></td>
                </tr> 
                <tr class="tr_califica">
                  <td class="ttitulo" colspan="2">Fecha Calificación </td>
                  <td class="fecha_califica" colspan="2"></td>
                </tr> 
                <?php if ($sw) {?>
                <tr>
                  <td class="ttitulo"colspan="2">Historial</td>
                  <td colspan="2"><span id="ver_estados"><span class="fa fa-eye red"></span> Ver Estados</span></td>
                </tr>   
                <?php } ?>
                <tr>
                  <th class="nombre_tabla" colspan="4">Informacion Solicitante</th>
                </tr>
                <tr>
                  <td class="ttitulo"  colspan="2">Fecha registro</td>
                  <td class="fecha_registra" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo" colspan="2">Solicitante</td>
                  <td class="solicitante" colspan="2"></td>
                </tr>
                <tr>
                  <td class="ttitulo " colspan="2">Telefono</td>
                  <td class="telefono " colspan="2"></td>
                  </tr>
                <tr>
                  <th class="nombre_tabla" colspan="4">Detalle de la Solicitud</th>
                </tr>  
                <tr>
                  <td class="ttitulo ">Fecha Inicio</td>
                  <td class="fecha_inicio_evento "></td>
                  <td class="ttitulo ">Fecha Fin</td>
                  <td class="fecha_fin_evento "></td>
                </tr>
                <tr>
                <td class="ttitulo ">Tipo de evento</td>
                  <td class="tipo_evento "></td>
                  <td class="ttitulo ">Bloque - Lugar</td>
                  <td class="nombre_lugar "></td>
                </tr>
                <tr>
                  <td class="ttitulo ">Nro de Invitados</td>
                  <td class="nro_invitados "></td>
                  <td class="ttitulo ">Salon - Direccion</td>
                  <td class="direccion "></td>
                </tr> 
              </div>
              <tr>
                <td class="ttitulo mostrar_d" >Categoria</td>
                <td colspan="3" class="categoria_divulgacion mostrar_d"></td>
              </tr> 
              <tr>
                <td class="ttitulo">Archivos Adjunto</td>
                <td colspan="3"><span id="ver_adjuntos_lista"><span class="fa fa-eye red"></span> Ver Archivos</span></td>
              </tr> 
              <tr>
                <td class="ttitulo">Descripcion</td>
                <td class="descripcion" colspan="3"></td>
              </tr>               
            </table>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_servicios_solicitud"  cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE SERVICIOS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5" >
                      <span  class="btn btn-default btnAgregar" id="agregar_servicios_nuevos">
                      <span class="fa fa-plus red"></span> Agregar servicio</span> </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre Servicio</td>
                    <td>Fecha</td>
                    <td>Solicitante</td>
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

<div class="modal fade" id="modal_servicios" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content" >
          <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-list"></span> Agregar Servicios</h3>
          </div>
          <div class="modal-body" id="bodymodal">   
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_servicios"  cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr ><th colspan="3" class="nombre_tabla">TABLA DE SERVICIOS</th></tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre</td>
                    <td class="opciones_tbl_btn">Acción</td>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
         </div>
      </div>
    </div>
</div>

<div class="modal fade" id="modal_detalle_servicio_asignado" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span> Información Completa</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed  tabla_info_inscripcion" id="">
                    <tr id='tr_tipo_ser'><td class="ttitulo">Tipo:</td><td class="tipo_servicio"></td></tr>
                    <tr id='tr_tipo_entrega_ser'><td class="ttitulo">Tipo Entrega: </td><td  class="tipo_entrega_servicio"></td></tr>
                    <tr id='tr_cantidad_ser'><td class="ttitulo">Cantidad - Valor: </td><td  class="cantidad_servicio"></td></tr>
                    <tr id='tr_observaciones_ser'><td class="ttitulo">Observaciones: </td><td  class="observaciones_servicio"></td></tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<form id="form_modi_solicitud" method="post">
  <div class="modal fade scroll-modal" id="modal_modificar_solicitud" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">  
            <!-- <div id="ocultar_div_modi"> -->
              <input type="text" class="form-control" name="id_codigo_sap" placeholder="Codigo SAP">
              <input type="text" class="form-control " name="nombre_evento" placeholder="Nombre del Evento">
            <div class='fechas'>
            <div id='fechas_nueva_f1_modi' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            <div id='fechas_nueva_f2_modi' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha2" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha2" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>

            <div id='fechas_nueva_f3_modi' class='oculto'>
              <div class="input-group date form_datetime agro formato_fecha3" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha Inicio" type="text" value=""
                  required="true" name="fecha_inicio_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group date form_datetime agro formato_fecha3" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                data-link-field="dtp_input1">
                <input class="form-control sin_focus f_fin" size="16" placeholder="Fecha Fin" type="text" value="" 
                required="true" name="fecha_fin_evento">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>    
            </div>
            </div>


              <select name="id_tipo_evento" required class="form-control inputt cbx_tipo_evento CampoGeneral" >
                  <option value="" >Seleccione Tipo Evento</option>
              </select>
              <div class="agro">
                <div class="input-group">
                    <input type="text" class="form-control requerido sin_margin" name="nombre_lugar" placeholder="Bloque - Lugar" required>
                    <span class="input-group-addon">-</span>
                    <input type="text" class="form-control  requerido sin_margin" name="direccion" placeholder="Salon - Direccion" required>
                </div>
              </div>
              <div class="agro">
                <div class="input-group">
                    <input type="number" step="1" min="1" class="form-control sin_margin" name="telefono" placeholder="Telefono" required>
                    <span class="input-group-addon">-</span>
                    <input type="number" step="1" min="1" class="form-control requerido sin_margin" name="nro_invitados" placeholder="Nro. aprox. de invitados" required>
                </div>
              </div>
              <!-- </div>                 -->
            <select name="id_categoria_divulgacion"  required class="form-control inputt cbx_cat_divulgacion CampoGeneral">
                <option value="">Seleccione Categoria</option>
            </select>
            <textarea class="form-control" cols="1" rows="3" name="descripcion" placeholder="Descripcion"></textarea>
            <br>
            <div class="btn-group btn-group-justified" role="group" aria-label="...">
              <div class="btn-group" role="group">
                <button id="conadjuntos_modificar" type="button" class="btn btn-default active btn-block"><span class="fa fa-folder-open red"></span> Adjuntar soportes</button>
              </div>
            </div>
            <br>
            <div class="alert alert-info" role="alert">
              <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
              <p>Recuerde hacer cick en la opción <strong>Enviar Solicitud</strong> luego de guardar las correciones.</p>
            </div>
          </div>
        </div> 
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>

      </div>
    </div>
  </div>
</form>
<?php if ($sw) {?>
<div class="modal fade" id="modal_listar_estados" role="dialog" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Historial de Estados</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed columna50" cellspacing="0" width="100%">
              <tr class=""><th class="nombre_tabla" colspan="2"> Gestión de Comunicaciones </th></tr>
              <tr class="filaprincipal"><td>Días de Tramite</td><td>Días Gestión</td></tr>
              <tr class=""><td id="dias_demora">----</td><td id='dias_gestion'>----</td></tr>
            </table>
            <table class="table table-bordered table-hover table-condensed " id="tabla_estados_comunicaciones"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr><td colspan="3" class="nombre_tabla">TABLA DE ESTADOS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5" >
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Fecha registro</td>
                    <td>Usuario</td>
                    <td>Estado</td>
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

<div class="modal fade" id="modal_detalle_estado" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle del Estado</h3>
      </div>
      <div  class="modal-body" id="bodymodal">
          <div class="table-responsive" >
            <table class="table table-responsive table-condensed table-bordered">   
              <div > 
                <tr>
                  <th class="nombre_tabla" colspan="4">Información del Estado</th>
                </tr>   
                <tr class="oculto_revision">
                  <td class="ttitulo" colspan="2">Correo Diseñador</td>
                  <td class="correo_disenador" colspan="2"></td> 
                </tr> 
                
              <tr>
                <td class="ttitulo" colspan="2">Descripcion</td>
                <td class="observacion" colspan="2"></td>
              </tr>               
            </table>
          </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php } ?>

<form id="form_gestionar_solicitud" method="post">
<?php if ($sw) {?>
  <div class="modal fade scroll-modal" id="modal_gestionar_solicitud" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-refresh"></span> Gestionar Solicitud</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class='oculto text-center' id='cont_adjuntos'>
              <span id="con_adjuntos_rev" class="pointer "> <span class="fa fa-folder-open red"></span>Clic aqui para seleccionar los adjuntos a enviar. </span>
              <hr>
            </div>
            <div class='oculto' id='cont_Tramite'>
              <label for="req_diseno" class='form-control text-center' style='font-weight:normal'><input type="checkbox" name="diseno" id="req_diseno">ENVIAR CORREO A DISEÑADOR</label>  
              <div id='cont_diseno' class='oculto'>
                <input type="email" class="form-control" name="correo" placeholder="Correo diseñador" >
                <textarea class="form-control" cols="1" rows="3" name="descripcion" placeholder="Descripcion"></textarea>
              </div>
            </div>
          </div>
        </div> 
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Terminar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</form>
<div class="modal fade" id="modal_listar_archivos_adjuntos" role="dialog" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive"> 
            <table class="table table-bordered table-hover table-condensed " id="tabla_adjuntos_comunicaciones"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE ADJUNTOS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5" >
                      <span  class="btn btn-default btnAgregar" id="agregar_adjuntos_nuevos">
                      <span class="fa fa-plus red"></span> Agregar Adjunto</span> </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Fecha Adjunto</td>
                    <td>Nombre usuario</td>
                    <td>Accion</td>
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

<form id="form_guardar_calificacion" method="post" action="#">
<div class="modal fade" id="modal_calificar_solicitud" role="dialog">

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content" >  
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-star"></span> Calificar Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="container-fluid">
                    <div class="col-md-6">
                        <div class="funkyradio">
                            <div class="funkyradio-success">
                              <input type="radio" id="rate1" name="rating" value="1" />
                              <label for="rate1" title="Satisfecho"> Satisfecho</label>
                            </div>
                            <div class="funkyradio-danger">
                              <input type="radio" id="rate2" name="rating" value="2" />
                              <label for="rate2" title="No satisfecho"> No satisfecho</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                      <textarea class="form-control calificarComunicaciones" placeholder="Observaciones" name="observacion" rows="5" cols="6"></textarea>
                    </div>
                </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" id="btn_calificar" ><span class="glyphicon glyphicon-star"></span> Calificar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
        </div>
    </div>

</div>
</form>
<div class="modal fade" id="modal_crear_filtros" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select name="" class="form-control inputt cbxtipoSolicitud" id="tipo_sol_filtro">
            <option value="">Filtrar Solicitudes por Tipo</option>
          </select> 
          <select class="form-control inputt cbx_estados" id="estado_filtro">
            <option value="">Filtrar Solicitudes por Estado</option>
          </select>
          <input class="form-control" value="" placeholder="Filtrar Por Fecha" type="month" name="fecha_filtro"
            id="fecha_filtro">
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



<div class="modal fade" id="modal_enviar_archivos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content" >
        <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
            <input type="hidden" name="id" id="id_solicitud" val="0">
            <div class="dz-message needsclick"><p>Arrastre archivos o presione clic aquí</p></div>
          </form>
        </div>
        <div class="modal-footer" id="footermodal">
        </div>
      </div>
    </div>
</div>

<form id="form_detalle_servicio" method="post" action="#">
<div class="modal fade" id="modal_detalle_servicio" role="dialog">

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content" >  
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Servicio</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="row" id='conta_detalle_servicio'>
                  <div class="alert alert-info oculto" id="info_refrigerio" role="alert"></div>
                    <div id='mesas' class='oculto'>
                      <select  required class="form-control inputt cbx_tipo_mesas">
                        <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='platos' class='oculto'>
                      <select   required class="form-control inputt cbx_tipo_platos">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='cucharas' class='oculto'>                    
                      <select   required class="form-control inputt cbx_tipo_cucharas">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='tenedores' class='oculto'>                    
                      <select   required class="form-control inputt cbx_tipo_tenedores">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='copas' class='oculto'>                    
                      <select   required class="form-control inputt cbx_tipo_copas">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='almuerzo' class='oculto'>                    
                      <select   required class="form-control inputt cbx_tipo_almuerzo">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='refri' class='oculto'>
                      <select   required class="form-control inputt cbxrefrigerios">
                          <option value="">Seleccione Tipo</option>
                      </select>
                    </div>
                    <div id='entrega' class='oculto'>
                      <select  required class="form-control inputt cbxentrega">
                          <option value="">Seleccione Tipo</option>
                      </select>    
                    </div>
                    <div id='flores' class='oculto'>
                      <input type="number" step='1' min ='1' class="form-control"  placeholder="Valor">
                    </div>
                    <div id='cantidad' class='oculto'>
                      <input type="number" step='1' min ='1' class="form-control"  placeholder="Cantidad">
                    </div>
                    <div class="observaciones_adm">
                      <textarea class="form-control" cols="1" rows="3" name="observaciones" placeholder="Observaciones"></textarea>
                    </div>

                </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" id="btn_agregar_servicio" ><span class="fa fa-check"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
        </div>
    </div>

</div>
</form>
<script>
    $(document).ready(function () {
        inactivityTime();
       
    <?php if($sw){?> mostrar_solicitudes_terminadas_ext(); <?php }?>
        obtener_info_usuario('<?php echo  $_SESSION["perfil"]?>','<?php echo  $_SESSION["persona"]?>');     
        listar_solicitud('<?php echo $id?>');
        recibir_archivos();
        listar_servicios();
        cargar_informacion_menu();     
        Cargar_parametro_buscado(62, ".cbx_tipo_evento", "Seleccione Tipo de Evento");
        Cargar_parametro_buscado_aux(63, ".cbx_cat_divulgacion", "Seleccione Categoria");
        Cargar_parametro_buscado_aux(61, ".cbx_estados", "Seleccione Estado");
        Cargar_parametro_buscado_aux(60, ".cbxtipoSolicitud", "Seleccione Tipo de Solicitud");

        Cargar_parametro_buscado(30, ".cbx_tipo_mesas", "Seleccione Tipo Mesa");
        Cargar_parametro_buscado(32, ".cbx_tipo_platos", "Seleccione Tipo Platos");
        Cargar_parametro_buscado(31, ".cbx_tipo_cucharas", "Seleccione Tipo Cucharas");
        Cargar_parametro_buscado(28, ".cbxrefrigerios", "Seleccione Tipo Refrigerios");        
        Cargar_parametro_buscado(73, ".cbxentrega", "Seleccione Tipo Entrega");

        //AGREGAR VALOR PARAMETRO
        Cargar_parametro_buscado(112, ".cbx_tipo_tenedores", "Seleccione Tipo Tenedores");
        Cargar_parametro_buscado(113, ".cbx_tipo_copas", "Seleccione Tipo Copas");
        Cargar_parametro_buscado(114, ".cbx_tipo_almuerzo", "Seleccione Tipo Almuerzo");
        
    });
</script>
<?php
$t_evento = 0; 
$t_publicidad = 15; 
$t_cubrimiento = 8; 
$t_man = 3;
foreach ($tiempos as $t) {
  /*if ($t['id_aux'] == 'Com_Env') {
    $t_evento = $t['valory'];
  }else*/ 
  if ($t['id_aux'] == 'Com_Pub') {
    $t_publicidad = $t['valory'];
  }else if ($t['id_aux'] == 'Com_Cub') {
    $t_cubrimiento = $t['valory'];
  }
}
$t_man = empty($tiempos_man) ? 3 : $tiempos_man[0]['valor'];
?>
<script type="text/javascript">
configurar_fechas(<?php echo $t_evento?>,'.formato_fecha');
configurar_fechas(<?php echo $t_publicidad?>,'.formato_fecha2');
configurar_fechas(<?php echo $t_cubrimiento?>,'.formato_fecha3');
configurar_fechas(<?php echo $t_man?>,'.formato_fecha4');
</script>
 <script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
