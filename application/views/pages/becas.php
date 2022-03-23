<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php 
$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec"? true : false
?>
<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo $administra || $id >0 ?'':'oculto'; ?>" id="container_solicitudes">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed" id="tabla_listado_solicitudes_becas"
        cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="3" class="nombre_tabla">TABLA BECAS SOLICITUDES <br><span
                class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros
                aplicados.</span></td>
            <td class="sin-borde text-right border-left-none" colspan="7">
              <?php if($administra || $_SESSION["perfil"] == "Doc"):?><span class="btn btn-default" id="btn_notificaciones"><span id="num_notificaciones" class="badge btn-danger">0</span> Notificaciones</span><?php endif?>
              <?php if($administra) :?><span class="black-color pointer btn btn-default" id="btn_administrar"><span class="fa fa-cogs red" ></span> Administrar</span><?php endif ?> 
              <span class="btn btn-default" data-toggle="modal" id="btn_filtrar"> <span class="fa fa-filter red"></span> Filtrar</span>
              <span class="btn btn-default" id="btn_limpiar_filtros"><span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Solicitante</td>
            <td>Tipo Solicitud</td>
            <td>Fecha Registro</td>
            <td>Estado Solicitud</td>
            <td class="" style="width:150px">Acciones</td>
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

        <div id="btn_renovaciones">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/Viaticos_Transporte.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Renovaciones</span>
            </div>
          </div>
        </div>

        <div id="listado_solicitudes">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Mis Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

<!-- Modal listar estados de la solicitud y gestionarla -->
<div class="modal fade" id="modal_gestion_y_estados" role="dialog">
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
                                <li class="pointer" id="visto_bueno"><a><span class="fa fa-check red" style="color:#2E79E5"></span> Aceptar</a></li>
                                <li class="pointer" id="visto_malo"><a><span class="fa fa-times" style="color:#cc0000"></span> Rechazar</a></li>
                            </ul>
                        </div>
                    </nav>
                    <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_gestion" cellspacing="0" width="100%">
                        <thead class="ttitulo ">
                          <tr>
                            <th class="nombre_tabla" colspan="6">TABLA Estados</th>
                          </tr>
                          <tr class="filaprincipal">
                            <td>No</td>
                            <td>Nombre Estado</td>
                            <td>Fecha</td>
                            <td>Usuario</td>
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

<div class="modal fade" id="model_registro_solicitud_beca" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Solicitud</h3>
            </div>
            <div class="modal-body" id="bodymodal">
              <div class="opciones__container">
                <div id="informacion_principal" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Información principal" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Información principal</span>
                </div>

                <div id="ver_concepto" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Conceptos" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/conceptos.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Agregar Conceptos</span>
                </div>
                <!-- pausado -->
                <!-- <div id="ver_plan" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Plan de acción" data-content="">
                  <img src="<?php /*echo base_url()*/ ?>/imagenes/plan.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Plan de acción</span>
                </div> -->

                <div id="ver_entregable" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Entregables" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/entregable.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Entregables</span>
                </div>

                <div id="ver_exp" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Sector Productivo" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/productivo.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Sector productivo</span>
                </div>

                <div id="ver_intelectual" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Produccion Intelectual" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/intelectual.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Produccion intelectual</span>
                </div>

                <div id="ver_herramienta" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Herramientas" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/herramientas.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Herramientas</span>
                </div>

                <div id="ver_anexo" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Anexos" data-content="">
                  <img src="<?php echo base_url() ?>/imagenes/folder.png" alt="..." class="opcion__img">
                  <span class="opcion__span" >Anexos</span>
                </div>

              </div>
            </div>
            <div class="modal-footer" id="footermodal">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_informacion_principal" role="dialog">
  <div class="modal-dialog modal-lg">
  <form action="#" id="form_solicitud_beca" method="post">
    <div class="modal-content" >
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="col-md-6">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="admitido" name="admitido_al_programa" value="si">
                <label for="admitido" title="Admitido"> Admitido</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="funkyradio funkyradio-danger">
                <input type="radio" id="no_dmitido" name="admitido_al_programa" value="no">
                <label for="no_dmitido" title="No admitido"> No admitido</label>
              </div>
            </div>
            <div class="col-md-12">
              <input type="text" name="programa" id="programa" class="form-control" placeholder="Programa al que aspira" required>
            </div>
            <div class="col-md-6">
              <select class="form-control cbx_nivel_formacion" id="nivel_formacion" name="id_nivel_formacion" required>
                <option value="" selected disabled >Seleccione el nivel de formación</option>
              </select>
            </div>
            <div class="col-md-6">
              <select class="form-control" name="tipo_duracion_programa" id="tipo_year" required >
                <option value="" selected disabled >Seleccione el tipo de duración de su formación</option>
                <option value="tipo_year">Duración en Año</option>
                <option value="tipo_semestre">Duración en Semestre</option>
              </select>
            </div>
            <div class="col-md-6">
              <input type="text" name="institucion" id="institucion" class="form-control" placeholder="Institución que ofrece el programa" required>
            </div>
            <div class="col-md-6">
              <input type="number" name="ranking" id="ranking" class="form-control" placeholder="Ranking" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="pais_insti" id="pais_insti" class="form-control" placeholder="Pais de la Institución" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="ciudad_insti" id="ciudad_insti" class="form-control" placeholder="Ciudad de la Institución" required>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_semestre" name="id_semestre" id="id_semestre" required>
                <option value="" selected disabled >Seleccione</option>
              </select>
            </div>
            <div class="col-md-12">
              <select class="form-control cbx_year" name="id_year" id="id_year" required>
                <option value="" selected disabled >Seleccione</option>
              </select>
            </div>
            <div class="col-md-12">
              <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio sin_margin" id="fecha_inicio" size="16" placeholder="Fecha de inicio" type="text" value=""
                    name="fecha_inicio" required>
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
            <div class="col-md-12">
              <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio" id="fecha_fin" size="16" placeholder="Fecha estimada de culminación del programa de formación" type="text" value=""
                    name="fecha_termina"required>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
            <div class="col-md-6">
              <input type="text" name="linea_investigacion" id="linea_investigacion" class="form-control" placeholder="Línea de investigación a fortalecer" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="pin" id="pin" class="form-control" placeholder="Sublinea de investigación por fortalecer" required>
            </div>
            <div class="col-md-6">
              <select class="form-control cbx_comision" name="id_comision_estudio" id="id_comision_estudio" required>
              </select>
            </div>
            <div class="col-md-6">
              <select class="form-control cbx_beca oculto" name="id_beca" id="id_beca">
              </select>
            </div>
          </div> 
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
    </div>
    </form>
  </div>
</div>

<!-- modal tabla y agregar conceptos -->
<div class="modal fade" id="modal_becas_conceptos" role="dialog">
  <div class="modal-dialog modal-lg modal-80">
    <form action="#" id="form_agregar_solicitud" method="post" autocomplete="off">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> Conceptos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="">
            <div class="table-responsive">

              <table class="table table-bordered table-hover table-condensed" id="tabla_becas_conceptos"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <th class="nombre_tabla">TABLA Presupuesto</th>
                    <th colspan="5" class="nombre_tabla"><span class="pointer" id="btn_agregar_conceptos"><span
                          class="fa fa-plus pointer red"></span>Agregar
                        conceptos a esta solicitud</span></th>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Concepto</td>
                    <td>Incluye Beca</td>
                    <td>Apoyo solicitado</td>
                    <td>Por beca</td>
                    <td>Valor asumido</td>
                    <td>Total</td>
                    <td class="opciones_tbl_btn">Acciones</td>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <td class="ttitulo text-center">Total</td>
                    <td colspan="6"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <textarea class="form-control oculto" id="txt_observaciones" cols="1" rows="3" name="observaciones"
              placeholder="Observaciones"></textarea>
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

<!-- Modal notificaciones -->
<div class="modal fade" id="modal_notificaciones_becas" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones Becas</h3>
            </div>
            <div class="modal-body" id="bodymodal" >
                <div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
                <div id="panel_notificaciones_solicitudes" style="width: 100%" class="list-group"></div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php if($administra) { ?>
<!-- Modal Administrar Permisos-->
<div class="modal fade" id="administrar_permisos_becas" role="dialog">
  <div class="modal-dialog">
    <form  id="form_administrar"  method="post">
			<!-- Modal content-->
			<div class="modal-content" >
				<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Permisos</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="permisos adm_proceso active">
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
								<table id="tabla_tipo_solicitud" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
									<thead class="ttitulo ">
										<tr><td  class="nombre_tabla" colspan="4">TABLA SOLICITUDES</td></tr>
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
					<div class="csep active"></div>
				</div>  
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
    </form>
	</div>
</div>

<!-- Modal Elegir Persona-->
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

<!-- Modal Elegir Estado-->
<div id="modal_elegir_estado" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
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

<?php } ?>

<!-- MODALS DE LA PARTE DE VER SOLICITUD -->
<!-- modal informacion completa de la solicitud -->
<div class="modal fade" id="modal_detalle_solicitud_becas" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="alert alert-info" id="msg_soli_ini" role="alert">
            <span>Si desea ver la Solicitud Inicial <b><a id="info_soli_inicial" class="pointer">Haga Click Aqui</a></b></span> 
        </div>
        <nav class="navbar navbar-default" style="display: flex;">
          <div class="container-fluid" style="padding: 0;">
            <ul class="nav navbar-nav">
              <li class="pointer"><a id="detalle_concepto"><span class='fa fa-money red'></span> Conceptos</a></li>
              <li class="pointer"><a id="detalle_entregable"><span class='fa fa-file-text-o red'></span> Entregables</a></li>
              <li class="pointer"><a id="detalle_experiencia"><span class='fa fa-folder-open red'></span> Experiencia</a></li>
              <li class="pointer"><a id="detalle_intelectual"><span class='fa fa-newspaper-o red'></span> Intelectual</a></li>
              <li class="pointer"><a id="detalle_herramientas"><span class='fa fa-wrench red'></span> Herramientas</a></li>
              <li class="pointer"><a id="detalle_anexo"><span class='fa fa-folder red'></span> Anexos</a></li>
              <li class="pointer"><a id="ver_historial"><span class='fa fa-calendar red'></span> Estado</a></li>
            </ul>
          </div>
          </nav>
        <div class="table-responsive">
          <table class="table table-responsive table-condensed table-bordered" id="detalle_solicitud_becas">   
            <div> 
              <tr>
                <th class="nombre_tabla" colspan="4"> Información General</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Solicitante</td>
                <td colspan="2">
                  <div id="info_solicitante" class="pointer red btn" title="Detalle Salicitante" data-toggle="popover" data-trigger="hover">
                  <span class="solicitante"></span>
                  </div>
                </td>
              </tr> 
              <tr>
                <td colspan="2" class="ttitulo">Nivel de formación</td>
                <td colspan="2" class="nivel_formacion"></td>
              </tr> 
              <tr>
                <td class="ttitulo">Programa al que aspira</td>
                <td class="programa"></td>
                <td class="ttitulo">Institución</td>
                <td class="institucion"></td>  
              </tr>
              <tr>
                <td class="ttitulo">Pais Institución</td>
                <td class="pais_institucion"></td>
                <td class="ttitulo">Ciudad Institución</td>
                <td class="ciudad_institucion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Ranking Institución</td>
                <td class="ranking"></td>
                <td class="ttitulo">Tipo de duración</td>
                <td class="tipo_duracion_programa"></td>
              </tr> 
              <tr>
                <td class="ttitulo">Duración</td>
                <td class="duracion_programa"></td>
                <td class="ttitulo">Etapa en la que se encuentra</td>
                <td class="semestre"></td>
              </tr>  
              <tr>
                <td class="ttitulo">Admitido al programa</td>
                <td class="admitido_al_programa"></td>
                <td class="ttitulo">Fecha de inicio</td>
                <td class="fecha_inicio"></td>
              </tr> 
              <tr>
                <td class="ttitulo">Fecha de terminación</td>
                <td class="fecha_termina"></td>
                <td class="ttitulo">Línea de investigación</td>
                <td class="linea_investigacion"></td>
              </tr> 
              <tr>
                <td class="ttitulo">Pin a fortalecer</td>
                <td class="pin"></td>
                <td class="ttitulo">Estado de la solicitud</td>
                <td class="estado_soli"></td>
              </tr>
              <tr>
                <td class="ttitulo">Solicitud de comision de estudio?</td>
                <td class="comision_de_estudio"></td>
                <td class="ttitulo">Tiene beca?</td>
                <td class="tener_beca"></td>
              </tr>
              <tr>
                <td colspan="4" class="ttitulo">Observación</td>
              </tr>
              <tr>
                <td colspan="4" class="observacion"></td>
              </tr>
            </div>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal tabla y agregar herramientas -->
<div class="modal fade" id="modal_becas_herramientas" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Herramientas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_herramientas"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA Herramientas</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td>Descripcion</td>
                  <td>Horas de formación</td>
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
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal tabla y agregar productos intelectuales -->
<div class="modal fade" id="modal_becas_prod_intel" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Productos Intelectuales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_prod_intel"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA Productos</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Producto</td>
                  <td>Nombre del producto</td>
                  <td>Entidad de publicación</td>
                  <td>Fecha de publicación</td>
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
        <!-- <button type="submit" class="btn btn-danger active btnAgregar"><span
            class="glyphicon glyphicon-floppy-disk"></span>
          Guardar</button> -->
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal tabla y agregar experiencia en el sector productivo -->
<div class="modal fade" id="modal_becas_sector_prod" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Experiencia Sector Productivo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_sector_prod"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA experiencia</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Área general</td>
                  <td>Área especifica</td>
                  <td>Entidad</td>
                  <td>Años de experiencia</td>
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
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal tabla y agregar productos al compromisos -->
<div class="modal fade" id="modal_becas_entregables" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Entregables</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_entregable"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA Entregable</th>
                </tr>
                <tr class="filaprincipal">
                  <td>No.</td>
                  <td>Entregable</td>
                  <td>Producto</td>
                  <td>Compromisos</td>
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
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal tabla y agregar plan de accion -->
<div class="modal fade" id="modal_becas_plan_accion" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Plan de acción</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_plan_accion"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla">TABLA plan accion</th>
                </tr>
                <tr class="filaprincipal">
                  <td>No.</td>
                  <td>Meta</td>
                  <td class="opciones_tbl_btn">Actividades</td>
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

<!-- modal tabla y agregar productos al compromisos -->
<div class="modal fade" id="modal_becas_compromisos" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Compromisos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_compromiso"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla" colspan='3'>TABLA compromiso</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Compromiso</td>
                  <td>Año</td>
                  <td>Periodo</td>
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

<!-- modal tabla y agregar actividades -->
<div class="modal fade" id="modal_becas_actividades" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-text"></span> Actividades</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tabla_becas_actividades"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla" colspan='3'>TABLA Actividades</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Actividad</td>
                  <td>Recurso</td>
                  <td>Fecha inicio</td>
                  <td>Fecha Fin</td>
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

<!-- informacion del solicitante -->
<div class="modal fade" id="modal_info_solicitante" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style='width : 100%'>
          <div>
            <table class="table table-responsive table-condensed table-bordered" id="detalle_solicitante">
              <tr class="nombre_tabla text-left">
                <td colspan='5'>Informacion General</td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre</td>
                <td class="nombre_completo" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Departamento</td>
                <td class="departamento" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Programa</td>
                <td class="programa_del_solicitante" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Vinculación</td>
                <td colspan='5' class="vinculacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo de contrato</td>
                <td colspan='5' class="contrato"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tiempo laborando</td>
                <td colspan="2" class="tiempo_laborando"></td>
                <td class="ttitulo">Edad</td>
                <td colspan="2" class="edad"></td>
              </tr>
              <tr>
                <td class="ttitulo">Correo Institucional</td>
                <td colspan="2" class="correo_institucional"></td>
                <td class="ttitulo">Teléfono</td>
                <td colspan="2" class="telefono"></td>
              </tr>
              <tr>
                <td class="ttitulo">CvLac</td>
                <td colspan='5' class="cvlac"></td>
              </tr>
              <tr>
                <td class="ttitulo">Horas dedicadas a la investigación</td>
                <td colspan='5' class="horas_inv"></td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan='5'>Información Académica</td>
              </tr>
              <tr>
                <td class="ttitulo">Grupo de Investigación</td>
                <td class="grupo_investigacion" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">línea de Investigación</td>
                <td class="linea_investigacion" colspan='5'></td>
              </tr>
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_nivel_formacion" cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                      <tr>
                        <th class="nombre_tabla" colspan="6">Nivel de formación</th>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Formación</td>
                        <td>Nombre</td>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
              </div>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- modal detalle estados solicitud -->
<div class="modal fade" id="modal_detalle_estados_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Estados Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_estado_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla" colspan="6">TABLA Estados</th>
                </tr>
                <tr class="filaprincipal">
                  <td>No</td>
                  <td>Nombre Estado</td>
                  <td>Fecha</td>
                  <td>Usuario</td>
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

<!-- MODALS DE LA PARTE DE FORMULACION -->
<!-- modal detalle tabla herramientas -->
<div class="modal fade" id="modal_detalle_herramienta_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Herramientas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed" id="tabla_becas_herramientas_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="3">Tabla Herramientas</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_herramientas_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Herramientas</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td>Descripcion</td>
                <td>Horas de formación</td>
                <td>Acciones</td>
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

<!-- modal detalle tabla produccion intelectual -->
<div class="modal fade" id="modal_detalle_intelectual_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Produccion Intelectual</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_becas_prod_intelectual_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="4">Tabla Produccion intelectual</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_prod_intelectual_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Intelectual</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Producto</td>
                <td>Nombre del producto</td>
                <td>Entidad de publicación</td>
                <td>Fecha de publicación</td>
                <td>Acciones</td>
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

<!-- modal detalle tabla experiencia -->
<div class="modal fade" id="modal_detalle_experiencia_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Sector Productivo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_becas_exp_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="4">Tabla experiencia</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_exp_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Experiencia</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Área general</td>
                <td>Área especifica</td>
                <td>Entidad</td>
                <td>Años de experiencia</td>
                <td>Acciones</td>
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

<!-- modal detalle tabla plan de accion -->
<div class="modal fade" id="modal_detalle_plan_accion_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle plan de accion solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_becas_plan_accion_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="2">Tabla plan de acción</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_plan_accion_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Meta</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Objetivo</td>
                <td>Meta</td>
                <td>Acciones</td>
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

<!-- modal detalle tabla entregable -->
<div class="modal fade" id="modal_detalle_entregable_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Entregables</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_becas_compromiso_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="2">Tabla Entregables</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_entregable_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Entregable</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Producto</td>
                <td>Entregable</td>
                <td>Acciones</td>
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

<!-- modal detalle tabla concepto -->
<!-- REFACTORIZANDO LA TABLA DE CONCEPTOS DE LA SOLICITUD -->
<div class="modal fade" id="modal_detalle_concepto_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Concepto</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_becas_conceptos_detalle" cellspacing="0" width="100%">
            <thead class="ttitulo">
              <tr>
              <th class="nombre_tabla" colspan="6">Tabla Conceptos</th>
              <td class="sin-borde text-right border-left-none"><span id="btn_agregar_conceptos_detalle" class="btn btn-default"><span class='fa fa-plus red'></span> Concepto</span></td>
              </tr>
              <tr class="filaprincipal">
                <td>Por beca?</td>
                <td>Concepto</td>
                <td>Incluido en la beca</td>
                <td>Apoyo solicitado</td>
                <td>Valor asumido</td>
                <td>Total Concepto</td>
                <td>Acciones</td>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" class="ttitulo text-center">Total Solicitado Del Periodo: </td>
                <td colspan="5"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal detalle archivos adjuntos -->
<div class="modal fade" id="modal_detalle_archivos" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Archivos Adjuntos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed " id="tabla_archivos_becas"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE anexos</td>
                    <td class="sin-borde text-right border-left-none oculto"><span id="btn_agregar_certificado" class="btn btn-default"><span class='fa fa-plus red'></span> Certificado</span></td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Fecha Adjunto</td>
                    <td>Nombre usuario</td>
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

<!-- MODALS DE LA PARTE DE FORMULACION PARA AGREGAR O MODIFICAR-->
<!-- modal con formulario de agregar herramientas -->
<div class="modal fade scroll-modal" id="modal_agregar_herramientas" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_herramientas" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="nombre_herramienta" class="form-control" placeholder="Nombre de la herramienta" id="nombre_herramienta" required>
            <textarea type="text" id="descripcion_herramienta" name="descripcion_herramienta" class="form-control" placeholder="Descripción y uso" required></textarea> 
            <input type="number" id="horas_formacion" name="horas_formacion" class="form-control" placeholder="Horas de implementación o formación" required>  
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

<!-- modal con formulario de agregar productos intelectuales -->
<div class="modal fade scroll-modal" id="modal_agregar_prod_intel" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_prod_intel" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <select class="form-control cbx_prod_intel" name="producto_intel" id="producto_intel" required>
              <option value="" selected disabled ></option>
            </select>
            <input type="text" name="nombre_prod_intel" class="form-control" placeholder="Nombre del producto" id="nombre_prod_intel" required>
            <input type="text" name="entidad_prod_intel" class="form-control" placeholder="Entidad de implementación o publicación" id="entidad_prod_intel" required>
            <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio sin_margin" size="16" placeholder="Fecha publicación o finalización" type="text" value=""
                  name="fecha_finalizacion" id="fecha_finalizacion" required>
                <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
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

<!-- modal con formulario de agregar experiencia en el sector productivo -->
<div class="modal fade scroll-modal" id="modal_agregar_sector_prod" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_sector_prod" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="area_general_sector_prod" class="form-control" placeholder="Área general" id="area_general_sector_prod" required>
            <input type="text" name="area_especifica_sector_prod" class="form-control" placeholder="Área especifica" id="area_especifica_sector_prod" required>
            <input type="text" name="entidad_sector_prod" class="form-control" placeholder="Entidad" id="entidad_sector_prod" required>
            <input type="number" name="year_sector_prod" class="form-control" placeholder="Años de experiencia" id="year_sector_prod" required>
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

<!-- modal con formulario de agregar plan de accion -->
<div class="modal fade scroll-modal" id="modal_agregar_plan_accion" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_plan_accion" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <textarea name="meta_plan_accion" class="form-control" placeholder="Meta para el plan de accion" rows="5" id="meta_plan_accion"></textarea>
            <div class="input-group agro" id="contenedor_actividades">
                <span class="input-group-addon  btnAgregarActividades pointer red" id="mas_actividad" title="Más Actividades" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-plus "></span> </span>
                <select class="form-control actividades_agregadas sin_margin" id="actividad_asignada" > 
                  <option id="informacion_actividad" value="Act_Agre">0 Actividad(es)</option> 
                </select> 
                <span class="input-group-addon btnEliminaActividad pointer red" id="retirar_actividad_sele"  title="Retirar Actividad" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-remove "></span></span>
                <span class="input-group-addon pointer red btnModificarActividad" id="modificar_actividad_sele" title="Modificar Actividad" data-toggle="popover" data-trigger="hover" ><span class="fa fa-wrench"></span></span>
                <span class="input-group-addon pointer red btnVerActividad" id="ver_actividad_sele" title="Ver Actividad" data-toggle="popover" data-trigger="hover" ><span class="fa fa-eye"></span></span>
            </div>
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

<!-- modal con formulario de agregar actividades del plan de acción -->
<div class="modal fade scroll-modal" id="modal_agregar_gestion_plan_accion" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_gestion_plan_accion" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" name="actividad_gestion_plan_accion" class="form-control" placeholder="Actividad" id="actividad_gestion_plan_accion" required>
            <input type="text" name="recurso_gestion_plan_accion" class="form-control" placeholder="Recurso" id="recurso_gestion_plan_accion" required>
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha de inicio" type="text" value=""
                  name="fecha_inicio_gestion_plan_accion" id="fecha_inicio_gestion_plan_accion" required>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus f_inicio" size="16" placeholder="Fecha de finalización" type="text" value=""
                  name="fecha_finalizacion_gestion_plan_accion" id="fecha_finalizacion_gestion_plan_accion" required>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
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

<!-- modal con formulario de agregar entregable -->
<div class="modal fade scroll-modal" id="modal_agregar_entregable" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_entregable" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="text" class="form-control" name="producto_entregable" id="producto_entregable" placeholder="Producto" required>
            <textarea name="compromiso_entregable" class="form-control" placeholder="Entregable" rows="5" id="compromiso_entregable"></textarea>
            <div class="input-group agro" id="contenedor_compromisos">
                <span class="input-group-addon btnAgregarCompromisos pointer red" id="mas_compromiso" title="Más Compromiso" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-plus "></span></span>
                <select class="form-control compromisos_agregados sin_margin" id="compromiso_asignado"> 
                  <option id="informacion_compromiso" value="Comp_Agre">0 Compromiso(s)</option> 
                </select> 
                <span class="input-group-addon btnEliminarCompromisos pointer red" id="retirar_compromiso_sele"  title="Retirar Compromiso" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-remove "></span></span>
                <span class="input-group-addon pointer red" id="modificar_compromiso_sele" title="Modificar Compromiso" data-toggle="popover" data-trigger="hover" ><span class="fa fa-wrench"></span></span>
                <span class="input-group-addon btnVerCompromisos pointer red" id="ver_compromiso_sele" title="Ver Compromiso" data-toggle="popover" data-trigger="hover" ><span class="fa fa-eye"></span></span>
            </div>
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

<!-- modal con formulario de agregar compromisos -->
<div class="modal fade scroll-modal" id="modal_agregar_compromisos" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_compromisos" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <input type="number" name="year_compromiso" class="form-control" placeholder="Año" id="year_compromiso" required>
            <input type="text" name="fecha_periodo" class="form-control" placeholder="Periodo" id="fecha_periodo" required>
            <textarea type="text" id="compromiso_descripcion" name="compromiso_descripcion" class="form-control" placeholder="Compromiso" required></textarea> 
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

<!-- modal con formulario de conceptos -->
<div class="modal fade scroll-modal" id="modal_agregar_conceptos" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_conceptos" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="container_field">
            <div class="funkyradio funkyradio-success" id="container_beca">
              <input type="checkbox" id="beca_incluye" name="beca_incluye" value="si">
              <label for="beca_incluye" title="Admitido"> Marque para diligenciar lo que incluye la beca.</label>
            </div>
            <input type="text" name="incluye_beca" id="select_incluye_beca" class="form-control" placeholder="Que incluye la beca. Ej: Alimentación">
            <select class="form-control cbx_tipo_apoyo" name="tipo_apoyo" id="select_tipo_apoyo">
              <option value="" selected disabled >Seleccione el tipo de apoyo</option>
            </select>
            <input type="text" name="valor_total" id="valor_total" class="form-control valor_total_sin_punto" placeholder="Valor Total">
            <input type="text" id="apoyo_solicitado" name="apoyo_solicitado" class="form-control apoyo_sin_punto" placeholder="Apoyo solicitado CUC" >
            <br>
            <div class="margin1">
              <div class="alert alert-info" role="alert">
                <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                <p id="cart1" class="text-justify">Si pertenece a la Universidad de la costa y realizara estudios en está solo puede pedir <b>PAGO DE MATRICULA</b></p>
              </div>
            </div>
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

<!-- Modal para archivos -->
<div class="modal fade" id="modal_enviar_archivos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="alert alert-info">
          Tener en cuenta subir los anexos 1 y 2 (Solicitarlos al departamento de Talento Humano).
        </div>	      
        <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
          <input type="hidden" name="id" id="id_solicitud" val="0">
          <div class="dz-message needsclick"><p>Arrastre archivos o presione click aquí</p></div>
        </form>
      </div>
      <div class="modal-footer" id="footermodal">
        <button id="cargar_adj_soli" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal detalle de los conceptos  -->
<div class="modal fade" id="modal_becas_concepto" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style='width : 100%'>
          <div>
            <table class="table table-responsive table-condensed table-bordered" id="tabla_beca_conceptos">
            <thead class="ttitulo">
              <tr>
                <th class="nombre_tabla" colspan="5">Tabla Conceptos</th>
              </tr>
              <tr class="filaprincipal">
                <td>Por beca?</td>
                <td>Concepto</td>
                <td>Incluido en la beca</td>
                <td>Apoyo solicitado</td>
                <td>Valor asumido</td>
                <td>Total Concepto</td>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" class="ttitulo text-center">Total Solicitado Del Periodo: </td>
                <td colspan="4"></td>
              </tr>
            </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ver detalle compromisos  -->
<div class="modal fade" id="modal_detalle_solicitud_compromisos" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Compromiso</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-responsive table-condensed table-bordered" id="detalle_solicitud_compromiso">   
            <div> 
              </tr>
              <tr>
                <th class="nombre_tabla" colspan="2"> Información General</th>
              </tr>
              <tr>
                <td class="ttitulo">Año</td>
                <td class="detalle_year"></td>
              </tr>
              <tr>
                <td class="ttitulo">Periodo</td>
                <td class="detalle_periodo"></td>
              </tr>
              <tr>
                <td class="ttitulo">Compromiso</td>
                <td class="detalle_compromiso"></td>
              </tr>
            </div>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ver detalle actividades  -->
<div class="modal fade" id="modal_detalle_solicitud_actividades" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de las actividades</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-responsive table-condensed table-bordered" id="detalle_solicitud_actividad">   
            <div> 
              </tr>
              <tr>
                <th class="nombre_tabla" colspan="2"> Información General</th>
              </tr>
              <tr>
                <td class="ttitulo">Actividad</td>
                <td class="detalle_actividad"></td>
              </tr>
              <tr>
                <td class="ttitulo">Recurso</td>
                <td class="detalle_recurso"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Inicio</td>
                <td class="detalle_fecha_i"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Fin</td>
                <td class="detalle_fecha_f"></td>
              </tr>
            </div>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- filtro a la solicitud -->
<div class="modal fade" id="modal_filtrar_solicitudes" role="dialog" >
  <div class="modal-dialog">
  <form action="#" id="form_filtrar_solicitudes" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
        <div class="row">
            <!-- <div class="col-md-6" style="padding-left: 0px">
              <div class="funkyradio funkyradio-success">
                <input type="radio" id="filtro_admitido" name="filtro_admitido_al_programa" value="si">
                <label for="filtro_admitido" title="Admitido"> Admitido</label>
              </div>
            </div>
            <div class="col-md-6" style="padding-right: 0px">
              <div class="funkyradio funkyradio-danger">
                <input type="radio" id="filtro_no_dmitido" name="filtro_admitido_al_programa" value="no">
                <label for="filtro_no_dmitido" title="No admitido"> No admitido</label>
              </div>
            </div> -->
            <input type="text" class="form-control sin_margin" name='filtro_persona' placeholder="Buscar Persona"/>
            <select class="form-control cbx_estado" name="filtro_estado" ></select>
            <select class="form-control cbx_tipo" name="filtro_tipo" ></select>
            <!-- <select class="form-control cbx_nivel_formacion" name="filtro_id_nivel_formacion" ></select> -->
            <?php if($administra) :?>
            <select class="form-control cbx_departamentos" name="filtro_id_departamento" ></select>
            <select class="form-control cbx_programas" name="filtro_id_programa" ></select>
            <select class="form-control cbx_vinculacion" name="filtro_id_vinculacion" ></select>
            <?php endif?>
            <div class="col-md-6" style="padding-left: 0px">
              <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio sin_margin" size="16" placeholder="Fecha Inicio" type="text" value=""
                    name="filtro_fecha_inicio" >
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
            <div class="col-md-6" style="padding-right: 0px">
              <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio sin_margin" size="16" placeholder="Fecha Termina" type="text" value=""
                    name="filtro_fecha_termina">
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer red "><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
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

<!-- modal de finalizar -->
<div class="modal fade" id="modal_finalizar_solicitudes" role="dialog" >
  <div class="modal-dialog">
  <form action="#" id="form_finalizar_solicitudes" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-check-circle-o"></span> <span id="text_add_arts"></span> Finalizar Solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
        <div class="row">
            <select class="form-control cbx_finalizar" name="tipo_finalizar" required></select>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="fa fa-check"></span>Finalizar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_seleccion_info_docente" role="dialog" >
  <div class="modal-dialog">
    <form action="#" id="form_seleccion_info_docente" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-file-text"></span> <span id="text_add_arts"></span> Información Docente</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="alert alert-info">
              El docente no cuenta con la información docente registrada. Por favor seleccionar su información.
            </div>
            <select class="form-control cbx__dep_info" id="dep_info_beca" required></select>
            <select class="form-control cbx__pro_info" id="pro_info_beca" required></select>
            <select class="form-control cbx__vin_info" id="vin_info_beca" required></select>
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
let startDate = new Date();
    $(".form_datetime").datetimepicker({
      format: 'yyyy-mm-dd',
        autoclose: true,
        startDate,
        todayBtn: true,
        maxView: 4,
        minView: 2,
        daysOfWeekDisabled: [0],
    });
</script>

<script>
  let startDateBlock = new Date();
    $(".form_datetime_block").datetimepicker({
      format: 'yyyy-mm-dd',
        autoclose: true,
        startDateBlock,
        maxView: 4,
        minView: 2,
        daysOfWeekDisabled: [0],
    });
</script>

<script>
  $(document).ready(function(){
    number_sin_punto()
    Cargar_parametro_buscado(196, ".cbx_nivel_formacion", "Seleccione el Nivel de formación");
    Cargar_parametro_buscado(195, ".cbx_semestre", "Seleccione duración del programa");
    Cargar_parametro_buscado_aux(197, ".cbx_tipo_apoyo", "Seleccione el apoyo a solicitar");
    Cargar_parametro_buscado_aux(194, ".cbx_comision", "Su solicitud es de comisión de estudio?");
    Cargar_parametro_buscado_aux(200, ".cbx_beca", "Tiene beca?");
    Cargar_parametro_buscado(195, ".cbx_year", "Selección de semestre o año en que se encuentra");
    Cargar_parametro_buscado(199, ".cbx_prod_intel", "Seleccione el producto");
    Cargar_parametro_buscado_aux(201, ".cbx_tipo", "Seleccione el Tipo de solicitud");
    Cargar_parametro_buscado(91, ".cbx_departamentos", "Seleccione el Departamento");
    Cargar_parametro_buscado(86, ".cbx_programas", "Seleccione el Programa");
    Cargar_parametro_buscado(93, ".cbx_vinculacion", "Seleccione la Vinculacion");
    Cargar_parametro_buscado_aux(202, ".cbx_finalizar", "Seleccione el Tipo de Finalizado");
    Cargar_Parametro_estados_solicitud(193, ".cbx_estado", "Seleccione el Estado de la solicitud");
    listarSolicitudesBecas({ 'id' : <?php echo $id ?>});
    mostrar_solicitudes_notificaciones();
    recibir_archivos();
});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>

