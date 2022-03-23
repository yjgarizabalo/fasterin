<?php
  $admin  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Admin_Sopor"?  true : false;
?>
<div class="container col-md-12 text-center" id="inicio-user">
  <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
  <!-- Vista Administrador -->
  <div class="tablausu lista_contratos col-md-12 text-left <?php echo $admin? '' :'oculto' ?>" id="vista_admin">
    <div class="table-responsive col-sm-12 col-md-12  tablauser">
      
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_estado_supervisor" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="3" class="nombre_tabla"> TABLA SUPERVISORES
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="5">
              <span class="btn btn-default" id="btn_admin"><span class="fa fa-cogs red"></span> Administrar</span>
              <span class="btn btn-default" id="btn_filtrar"><span class="fa fa-filter red"></span>Filtrar</span>
              <span class="btn btn-default" id="limpiar_filtros_sup"><span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Supervisor</td>
            <td>Entrada</td>
            <td>Salida</td>
            <td>Estado</td>
            <td style='width:15%;'>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <!-- Modales -->
    <div class="modal fade" id="modalAdministrar" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <nav class="navbar navbar-default" id="nav_admin_spv" style="display: flex;">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer active" id="admin_supervisor"><a><span class="fa fa-users red"></span> Supervisores</a></li>
                  <li class="pointer" id="admin_salas"><a><span class="fa fa-desktop red"></span> Salas</a></li>
                  <li class="pointer" id="admin_turno"><a><span class="fa fa-calendar red"></span> Turnos</a></li>
                </ul>
              </div>
            </nav>

            <!-- Tabla de supervisores-->
            <div id="container_admin_supervisores" class="contenedor_admsp">
              <table class="table table-bordered table-hover table-condensed" id="tablaSupervisores" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <th colspan="4" class="nombre_tabla">TABLA SUPERVISOR</th>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>Nombre</td>
                    <td>Identificación</td>
                    <td>Correo</td>
                    <td>Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div> 
            <!-- Tabla de turnos -->
            <div id="container_admin_turnos" class="oculto">
                        <table class="table table-bordered table-hover table-condensed" id="tabla_turnos_sup" cellspacing="0" width="100%">
                            <thead class="ttitulo">
                            <tr>
                            <th class="nombre_tabla">TABLA TURNOS</th>
                                <td class="sin-borde text-right border-left-none" colspan="4">
                                    <button class="btn btn-default btn_new_turno_spv"> <span class="fa fa-plus red"></span> Nuevo Turno</button>
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
            <!-- Tabla de salas -->
            <div id="container_admin_salas" class="oculto">
              <table class="table table-bordered table-hover table-condensed" id="tablaSalas_d" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <th class="nombre_tabla">TABLA SALAS</th>
                    <td class="sin-borde text-right border-left-none" colspan="6">
                      <?php if($admin){ ?>
                        <button class="btn btn-default spv_sala_new"> <span class="fa fa-plus red"></span> Nueva sala</button>
                      <?php } ?>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>Nombre</td>
                    <td>Descripción</td>
                    <td class="opciones_tbl_btn">Acción</td>
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
</div>

<!-- Modal Para la Creación del Turno -->
<div class="modal fade" id="modal_crear_turno_spv" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_guardar_turnospv" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> <span class="titulo_modal_spv"></span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="col-md-12" style="padding: 0px;">
                <select name="id_dia_spv" id="id_dia_spv" class="form-control cbxdia_spv"><option value="">Seleccione Día</option></select>
            </div>    
            <div class="agrupado"> 
                <div class="col-md-6" style="padding: 0px;">
                    <div class="input-group date time_spv agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                        <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Inicio" type="text" value="" required="true" name="hora_inicio_spv" id="hora_inicio_spv">
                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                    </div>                                  
                </div> 
                <div class="col-md-6" style="padding: 0px;">
                    <div class="input-group date time_spv agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                        <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Fin" type="text" value="" required="true" name="hora_fin_spv" id="hora_fin_spv">
                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                    </div>                                  
                </div>
            </div>
            <div class="col-md-12" style="padding: 0px;">
                <textarea class="form-control inputt" name="descripcion_spv" id="descripcion_spv" placeholder="Descripción"></textarea>
            </div>    
          </div> 
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" onclick="guardar_turno_spv();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Para la Creación de salas -->
<div class="modal fade" id="modal_crear_sala_spv" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_guardar_salaspv" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> <span class="titulo_modal_salaspv"></span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="nombre_sala" id="nombre_sala" class="form-control inputt" placeholder="Nombre" required>
						<textarea class="form-control inputt" name="descripcion_sala" id="descripcion_sala" placeholder="Descripcion"></textarea>
          </div> 
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" onclick="guardar_sala_spv();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal para ver y asignar los turnos  -->
<div class="modal fade" id="modal_buscar_turnos" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Supervisores</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed" id="tabla_asignar_turnos" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla">TABLA TURNOS</th>
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


    <!-- Modal Para ver a todas las Salas-->
    <form  id="form_buscar_salas"  method="post">
  <div class="modal fade" id="modal_buscar_salas" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Asignar Salas</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tablaSalas" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA SALAS</td>
                  </tr>
                  <tr class="filaprincipal">
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

<div class="modal fade" id="modal_detalle_supervisor" role="dialog">
    <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalles Supervisor</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_detalle_supervisor" cellspacing="0" width="100%">
                      <thead class="ttitulo ">
                        <tr class="filaprincipal">
                            <td>Proceso</td>
                            <td>Fecha Registro</td>
                            <td></td>
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

<div class="modal fade" id="modal_novedades" role="dialog">
    <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Novedades</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_novedades" cellspacing="0" width="100%">
                      <thead class="ttitulo ">
                        <tr class="filaprincipal">
                            <td>Sala</td>
                            <td>Descripción</td>
                            <td>Fecha registro</td>
                            <td></td>
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

<form  id="form_filtrar_supervisores"  method="post">
      <div class="modal fade" id="modal_filtrar_supervisores" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                            <div class="col-md-10" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha de registro" type="text" value="" name="fecha_registro" id="fecha_registro" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>                                  
                            </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-danger active" id="btn_generar_filtro"><span class="glyphicon glyphicon-ok"></span> Generar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </div>
      </div>
  </form>

<script type="text/javascript">
   $(".time_spv").datetimepicker({
      formatViewType: 'time',
      fontAwesome: true,
      autoclose: true,
      startView: 1,
      maxView: 1,
      minView: 0,
      minuteStep: 5,
      format: 'hh:ii',

  });

  $(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayBtn: true,
    maxView: 4,
    minView: 2,
    // daysOfWeekDisabled: [0],
});
</script>
<script>
  $(document).ready(function() {
    inactivityTime();
    Cargar_parametro_buscado(100, ".cbxdia_spv", "Seleccione Día");
    activarfile();
    $('[data-toggle="popover"]').popover();
  });
</script>