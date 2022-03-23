<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left" id="container_solicitudes">
    <div class="table-responsive">
      <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="4" class="nombre_tabla">TABLA COMITÉS DE PRESUPUESTO<br><span  class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
            <td class="sin-borde text-right border-left-none">
             <span class="btn btn-default" id="btn_notificaciones"  ><span class="badge btn-danger n_notificaciones">0</span> Notificaciones</span>
             <span class="btn btn-default" id="btn_limpiar_filtros_comite"><span class="fa fa-refresh"></span> Limpiar</span>
             </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Nombre</td>
            <td>Descripción</td>
            <td>#Traslados</td>
            <td>Estado</td>
        </thead>
        <tbody>
        </tbody>
      </table>
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
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade con-scroll-modal" id="modal_detalle_traslado_comite" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Traslado</h3>
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
                <td class="ttitulo">Origen: </td>
                <td class="orden_origen"></td>
              </tr>
              <tr>
                <td class="ttitulo"> Destino: </td>
                <td class="orden_destino"></td>
              </tr>
              <tr>
                <td class="ttitulo">Valor: </td>
                <td class="valor"></td>
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

            <table class="table table-bordered table-hover table-condensed" id="tabla_aprobados_traslados"
              cellspacing="0" width="100%">
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

  <script>
  $(document).ready(function() {
    inactivityTime();
    listar_comites(2);
    mostrar_notificaciones_comentarios_comite('presupuesto', (id) => { abrir_traslados_comite_comentario(id) });
  });
  </script>
