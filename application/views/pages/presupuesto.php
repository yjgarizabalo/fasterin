<?php 
$administra = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Pre") {
    $administra = true;
}
?>
<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo $administra || $id >0 ?'':'oculto'; ?>" id="container_solicitudes">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed" id="tabla_traslados_detalle_solicitud"
        cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="4" class="nombre_tabla">TABLA SOLICITUDES DE TRASLADOS <br><span
                class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros
                aplicados.</span></td>
            <td class="sin-borde text-right border-left-none" colspan="10">
              <?php if ($administra) echo '<span class="btn btn-default" id="btn_notificaciones"  ><span class="badge btn-danger n_notificaciones">0</span> Notificaciones</span>';?>
              <?php if ($administra) echo '<span class="btn btn-default btnAgregar" id="btn_admin_solicitudes"><span class="fa fa-cogs red"></span> Administrar</span>';?>
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_crear_filtros"> <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="btn_limpiar_filtros"><span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td>***</td>
            <td>Tipo</td>
            <td>Año</td>
            <td>Solicitante</td>
            <td>Origen</td>
            <td>Cuenta Origen</td>
            <td>Destino</td>
            <td>Cuenta Destino</td>
            <td>Centro Origen</td>
            <td>Centro Destino</td>
            <td>Justificación</td>
            <td>Valor</td>
            <td>Estado</td>
            <td class="" style="width:150px">***</td>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>


  <div class="tablausu col-md-12 <?php echo $administra || $id >0 ?'oculto':''; ?>" id="menu_principal"
    style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">

        <div id="btn_nueva_solicitud">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Nueva Solicitud</span>
            </div>
          </div>
        </div>

        <div id="listado_solicitudes">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
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
          </div>
          <div class="modal-footer" id="footermodal">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="modal_nueva_solicitud" role="dialog">
  <div class="modal-dialog modal-lg modal-80">
    <form action="#" id="form_agregar_solicitud" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> Nueva Solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="">
            <div class="table-responsive">

              <table class="table table-bordered table-hover table-condensed" id="tabla_traslados_solicitud"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <th class="nombre_tabla">TABLA TRASLADOS</th>
                    <th colspan="5" class="nombre_tabla"><span class="pointer" id="btn_agregar_traslados"><span
                          class="fa fa-plus pointer red"></span>Agregar
                        Traslados a esta solicitud</span></th>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>Orden Origen</td>
                    <td>Cuenta Origen</td>
                    <td>Orden Destino</td>
                    <td>Cuenta Destino</td>
                    <td>Valor</td>
                    <td>Justificación</td>
                    <td class="opciones_tbl_btn">***</td>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <textarea class="form-control oculto" id="txt_observaciones" cols="1" rows="3" name="observaciones"
              placeholder="Observaciones"></textarea>

          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade scroll-modal" id="modal_agregar_traslado" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_traslado" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Traslado</span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
              <select class="form-control" required="true" name="tipo_traslado" id='tipo_traslado'>
                <option value="">Seleccione Tipo</option>
              </select>
            <select class="form-control cbx_anos" required="true" name="ano">
              <option value="">Seleccione Años Traslado</option>
            </select>
            <div class="input-group margin1" id='cont_orden_origen'>
              <input type="text" class="form-control sin_margin sin_focus" placeholder="" id="txt_nombre_orden_origen"
                required="true">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btn_orden_origen" style='width:120px'><span
                    class='red fa fa-search'></span>
                  Orden Origen</button>
              </span>
            </div>
            <div class="input-group margin1" id='cont_cuenta_origen'>
              <input type="text" class="form-control sin_margin sin_focus" placeholder="" id="txt_nombre_cuenta_origen"
                required="true">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btn_cuenta_origen" style='width:120px'><span
                    class='red fa fa-search'></span>
                  Cuenta Origen</button>
              </span>
            </div>

            <div class="input-group margin1" id='cont_orden_destino'>
              <input type="text" class="form-control sin_margin sin_focus" placeholder="" id="txt_nombre_orden_destino"
                required="true">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btn_orden_destino" style='width:120px'><span
                    class='red fa fa-search'></span>
                  Orden Destino</button>
              </span>
            </div>

            <div class="input-group margin1" id='cont_cuenta_destino'>
              <input type="text" class="form-control sin_margin sin_focus" placeholder="" id="txt_nombre_cuenta_destino"
                required="true">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btn_cuenta_destino" style='width:120px'><span
                    class='red fa fa-search'></span>
                  Cuenta Destino</button>
              </span>
            </div>

            <input type="text" name="valor" class="form-control valor_sin_punto" placeholder="Valor" min="1" step="1" required>
            <textarea name="justificacion" class="form-control comentarios" placeholder="Justificación" required></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade con-scroll-modal" id="modal_detalle_traslado" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Traslado</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed  margin1 ajustar" id="tabla_detalle_traslado">
            <tr class="">
              <th class="nombre_tabla" colspan="5"> Información General</th>
            </tr>
            <tr class="">
            <tr>
              <td class="ttitulo">Año Traslado: </td>
              <td class="ano_traslado"></td>
            </tr>
            <tr>
              <td class="ttitulo">Tipo: </td>
              <td class="tipo_traslado"></td>
            </tr>
            <td class="ttitulo">Solicitante:</td>
            <td> <span class="nombre_solicitante"></span>
              <?php if ($administra) echo '<span class="pointer fa fa-edit red detalle_persona_solicita" title="Detalle Persona" data-toggle="popover" data-trigger="hover"> </span>';?>
            </td>
            </tr>
            <tr class='tr_cuenta_origen'>
              <td class="ttitulo">Origen: </td>
              <td class="cuenta_origen"></td>
            </tr>
            <tr class='oculto tr_centro_origen'>
              <td class="ttitulo">Centro Costo Origen: </td>
              <td class="centro_origen"></td>
            </tr>
            <tr class='tr_cuenta_destino'>
              <td class="ttitulo">Destino: </td>
              <td class="cuenta_destino"></td>
            </tr>
            <tr class='oculto tr_centro_destino'>
              <td class="ttitulo">Centro Costo Destino: </td>
              <td class="centro_destino"></td>
            </tr>
            <tr>
              <td class="ttitulo">Valor Solicitado: </td>
              <td class="valor"></td>
            </tr>
            </tr>
            <tr class="tr_valor_aprobado">
              <td class="ttitulo">Valor Aprobado: </td>
              <td class="valor_aprobado"></td>
            </tr>
            <tr>
              <td class="ttitulo">Justificación: </td>
              <td class="justificacion"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Solicitud:</td>
              <td class="fecha_registra"></td>
            </tr>
            <tr>
              <td class="ttitulo">Estado: </td>
              <td class="estado_traslado"></td>
            </tr>
            <tr  class="tr_persona_avala">
              <td class="ttitulo">AVAL de: </td>
              <td class="nombre_avala"></td>
            </tr>
            <tr class='oculto tr_mensaje'>
              <td class="ttitulo">Negado Por: </td>
              <td class="mensaje"></td>
            </tr>
          </table>

          <table class="table table-bordered table-hover table-condensed" id="tabla_estados_solicitud" cellspacing="0"
            width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="4" class="nombre_tabla">TABLA ESTADOS</th>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td>Fecha</td>
                <td>Usuario</td>
                <td>Observaciones</td>
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
  </div>
</div>


<div class="modal fade" id="Modal_administrar_solicitudes" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <?php  if($administra){?>
        <nav class="navbar navbar-default" id="nav_admin_compras">
          <div class="container-fluid">

            <ul class="nav navbar-nav">
              <li class="pointer active" id="admin_comite"><a><span class="fa fa-folder red"></span> Comité</a></li>
              <li class="pointer" id="admin_costo"><a><span class="fa fa-sitemap red"></span> Centro Costo</a></li>
              <li class="pointer" id="admin_ordenes"><a><span class="fa fa-pencil-square-o red"></span> Ordenes</a></li>
              <li class="pointer" id="admin_cuentas"><a><span class="fa fa-fax red"></span> Cuentas</a></li>
              <li class="pointer" id="admin_presupuestos"><a><span class="fa fa-calendar red"></span> Traslados</a>
              </li>
            </ul>
          </div>
        </nav>
        <?php  }?>
        <div class="table-responsive">

          <div id="container_admin_valores" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_valores_parametros"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="3" class="nombre_tabla" id="nombre_tabla_cu_or">TABLA PROVEEDORES</td>
                  <td class="btnAgregar sin-borde text-center">
                    <span data-toggle="modal" data-target="#modal_nuevo_valor" class="btn btn-default"><span
                        class="fa fa-plus red"></span>
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

          <div id="container_admin_comite">
            <table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0"
              width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA COMITÉ</td>
                  <td class="btnAgregar sin-borde text-center"><span data-toggle="modal"
                      data-target="#modal_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span>
                      Nuevo</span></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">***</td>
                  <td>Nombre</td>
                  <td>Descripción</td>
                  <td>#Trasl.</td>
                  <td>Estado</td>
                  <td class="opciones_tbl_btn">***</td>

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
            <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripción"
              name="descripcion" required></textarea>
          </div>

        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnModifica"><span
              class="glyphicon glyphicon-floppy-disk"></span>
            Modificar</button>

          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

        </div>
      </div>
    </form>
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

        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>

          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
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
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="modal_solicitudes_comite" role="dialog">
    <div class="modal-dialog modal-lg modal-95">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> Traslados Comité</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="">
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-condensed" id="tabla_traslados_comite"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <th class="nombre_tabla" colspan='8'>TABLA TRASLADOS</th>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">Ver</td>
                    <td>Departamento</td>
                    <td>Origen</td>
                    <td>Destino</td>
                    <td>Justificación</td>
                    <td>Valor</td>
                    <td>#Aprobados</td>
                    <td>#Negados</td>
                    <td class="opciones_tbl_btn">Acción</td>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <div  style="width: 100%" class="list-group margin1 text-left" id='panel_comentarios_presupuesto'>
            
            </div>  
   
            <form action="" id='form_guardar_comentario'>   
              <div class="input-group col-md-6">
                <input type="text" class="form-control" placeholder="Comentario" name='comentario'>
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit">Enviar!</button>
                </span>
              </div><!-- /input-group -->
            </form>

          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" id='imprimir_acta'><span class="fa fa-print"></span> Imprimir</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade con-scroll-modal" id="modal_detalle_traslado_comite" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Traslado Comité</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed  margin1 ajustar" id="tabla_detalle_traslado_comite">
            <tr class="">
              <th class="nombre_tabla" colspan="5"> Información General</th>
            </tr>
            <tr class="">
              <td class="ttitulo">Solicitante:</td>
              <td> <span class="nombre_solicitante"></span>
                <span class="pointer fa fa-edit red detalle_persona_solicita" title="Detalle Persona"
                  data-toggle="popover" data-trigger="hover"> </span>
              </td>
            </tr>
            <tr>
              <td class="ttitulo"> Origen: </td>
              <td class="orden_origen"></td>
            </tr>
            <tr>
              <td class="ttitulo"> Destino: </td>
              <td class="orden_destino"></td>
            </tr>
            <tr>
              <td class="ttitulo">Valor: </td>
              <td><span class="valor"></span><span id="editar_valor_traslado"><span class='fa fa-wrench red'></span>
              </td>
            </tr>
            <tr>
              <td class="ttitulo">Justificación: </td>
              <td class="justificacion_comite"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Solicitud:</td>
              <td class="fecha_registra"></td>
            </tr>
            <tr>
              <td class="ttitulo">Estado: </td>
              <td class="estado_traslado"></td>
            </tr>
            <tr class='oculto tr_mensaje'>
              <td class="ttitulo">Negado Por: </td>
              <td class="mensaje"></td>
            </tr>
          </table>

          <table class="table table-bordered table-hover table-condensed" id="tabla_aprobados_traslados" cellspacing="0"
            width="100%">
            <thead class="ttitulo">
              <tr>
                <th colspan="3" class="nombre_tabla">TABLA APROBADOS - NEGADOS</th>
              </tr>
              <tr class="filaprincipal">
                <td>Tipo</td>
                <td>Nombre</td>
                <td>Fecha</td>
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
  </div>
</div>


<div class="modal fade" id="Mostrar_detalle_persona" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 80%">
          <div class="error text-center"></div>
          <div id="datos_perso" class="">
            <table class="table">
              <tr class="nombre_tabla">
                <td colspan="">Datos</td>
              </tr>
              <tr class='oculto'>
                <td class="foto_perso margin0" colspan=""></td>
              </tr>
              <tr>
                <td class="nombre_perso"></td>
              </tr>
              <tr>
                <td class="apellido_perso"></td>
              </tr>
              <tr>
                <td class="tipo_id_perso"></td>
              </tr>
              <tr>
                <td class="identi_perso"></td>
              </tr>
              <tr>
                <td class="cargo_perso"></td>
              </tr>
              <tr>
                <td class="depar_perso"></td>
              </tr>
              <tr>
                <td class="ubica_perso"></td>
              </tr>
              <tr>
                <td class="celular"></td>
              </tr>
              <tr>
                <td class="correo_perso"></td>
              </tr>
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

<div class="modal fade" id="modal_crear_filtros" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select class="form-control inputt cbx_estados" id="estado_filtro">
            <option value="">Filtrar Traslados por Estado</option>
          </select>
          <input class="form-control" value="" placeholder="Filtrar Por Fecha" type="date" name="fecha_filtro" id="fecha_filtro">
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

  <form action="#" id="form_gestionar_solicitud">
    <div class="modal fade" id="modal_gestionar_solicitud" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-retweet"></span> Gestionar Solicitud</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <select class="form-control" required="true" name="estado">
                <option value="">Seleccione Estado</option>
              </select>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span>
              Aceptar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
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
        <div class="modal-body" id="bodymodal">
          <div>
            <div class="input-group col-md-8">
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
                  <th colspan="2" class="nombre_tabla">TABLA CÓDIGOS SAP</th>
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

  <?php if($administra){?>
    <div id='acta_comite' class='oculto'>
    <div class="row">
        <div class="col-sm-12">
          <div class="thumbnail">
            <img src="<?php echo base_url()?>/imagenes/LogocucF.png" alt="..." width='200' heigth='200'>
            <div class="caption text-center">
              <h2>COMITÉ DE PRESUPUESTO</h2>
              <h3>ACTA <span id='nombre_pri'></span> - <span id='ano_pri'></span></h3>
              <table class='table' id='tabla_miembros'>
                <thead>
                  <tr><td>CIUDAD: </td><td>Barranquilla</td></tr>
                  <tr><td>FECHA INICIO: </td><td id='fecha_inicio_pri'>08/02/2019</td></tr>
                  <tr><td>FECHA FIN: </td><td id='fecha_fin_pri'>08/02/2019</td></tr>
                </thead>
                <tbody>

                </tbody>
              </table>
              <div class='text-left'>
                <p>El Rector somete a consideración de los asistentes el siguiente orden del día, el cual es aprobado.</p>
                <h3>ORDEN DEL DÍA:</h3>
                <h3>1. VERIFICACIÓN DEL QUÓRUM</h3>
                <p>Después de verificado el Quórum y aprobado el orden del día se da inicio a la reunión.</p>
                <h3>2. PRESENTACIÓN DE TRASLADOS PRESUPUESTALES</h3>
                <p  class='text-justify'>El Rector presenta y justifica la propuesta de efectuar unas modificaciones en el presupuesto, considerando algunas solicitudes recibidas. Se analiza y determina la necesidad de realizar algunos traslados presupuestales, que permitan asegurar la adecuada finalización de las actividades académicas y administrativas de la presente vigencia.</p>
                <br>
                <p>A continuación, se relacionan las solicitudes recibidas y su estado:</p>
              </div>
              <div>
                <br><br><br>
                <table class='table table-bordered' id='tabla_traslados_pri'>
                  <thead>
                    <tr>
                      <td>No.</td>
                      <td>Dependencia</td>
                      <td>Origen</td>
                      <td>Destino</td>
                      <td>Monto Solicitado</td>
                      <td>Descripción</td>
                      <td>Monto Aprobado</td>
                      <td>Estado</td>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
    
  <?php }?>

  <script type="text/javascript">
  $(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    autoclose: true,
  });
  </script>

  <script>
  $(document).ready(function() {
    number_sin_punto();
    inactivityTime();
    obtener_permisos('<?php echo $administra ?>');
    listar_traslados_solicitudes('<?php echo $id ?>');
    Cargar_parametro_buscado_aux(48, ".cbx_estados", "Seleccione Estado");
    listar_tipos_traslados();
    <?php if($administra){?>
    listar_comites();
    mostrar_notificaciones_comentarios_comite('presupuesto', (id) => { abrir_traslados_comite_comentario(id) });
    <?php }?>;
  });
  </script>
