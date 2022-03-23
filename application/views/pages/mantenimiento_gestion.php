<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">

<div class="tablausu col-md-12 text-left div_table" id="container-mante_gest">
	<div class="table-responsive">
		<p class="titulo_menu pointer" id='inicio_return'><span class="fa fa-reply-all naraja"></span> Regresar</p>
		<table class="table table-bordered table-hover table-condensed" id="table_mant_gest" cellspacing="0" width="100%">
			<thead class="ttitulo ">
				<tr>
					<td colspan="3" class="nombre_tabla">TABLA LUGARES MANTENIMIENTO</td>
				</tr>
				<tr class="filaprincipal ">
					<td class="opciones_tbl">Ver</td>
					<td>Lugar</td>
					<td>Cantidad</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>



<!-- TABLA UBICACIONES MANTENIMIENTO -->
<div class="modal fade" id="modal_ubicaciones_gest_mant" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Ubicaciones</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_ubicaciones_gest_mant" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="3" class="nombre_tabla">TABLA UBICACIONES MANTENIMIENTO</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">Ver</td>
								<td>Ubicacion</td>
								<td class="opciones_tbl">Cantidad</td>
								<td>Fecha</td>
								<td>Estado</td>
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


<!-- MODAL DETALLES DE SOLICITUD -->
<div class="modal fade" id="modal_detalle" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Detalles Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed">
						<tr>
							<th class="nombre_tabla" colspan="4"> Informacion de solicitud</th>
						</tr>
						<tr>
							<td class="ttitulo">Lugar: </td>
							<td><span class="info_lugar"></span></td>
							<td class="ttitulo">Ubicacion:</td>
							<td class="info_ubicacion"></td>
						</tr>
						<tr>
							<td class="ttitulo">Fecha:</td>
							<td class="info_fecha"></td>
							<td class="ttitulo">Fecha Final:</td>
							<td class="info_fecha_fin"></td>
						</tr>

					</table>
				</div>
				<div class="table-responsive">
					<table id="tbl_detalle_gest_mant" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="3" class="nombre_tabla">TABLA DE EVIDENCIAS MANTENIMIENTO</td>
							</tr>
							<tr class="filaprincipal ">
								<td>Ver</td>
								<td>Mantenimiento</td>
								<td>Fecha</td>
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

<!-- MODAL OBJETOS MANTENIMIENTO -->

<div class="modal fade" id="modal_objetos_gest_mant" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-archive"></span> Objetos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_objetos_gest_mant" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="2" class="nombre_tabla">TABLA OBJETOS MANTENIMIENTO</td>
								<td class="sin-borde text-right border-left-none" colspan="6">
									<span class="btn btn-default" id="btn_agregar_objeto_mantenimiento_gestion" title="Agregar objeto a reparar"><span class="fa fa-search red"></span> Buscar</span>
								</td>
							</tr>
							<tr class="filaprincipal ">
								<td>Ver</td>
								<td>Nombre objeto</td>
								<td>Estado</td>
								<td>Acción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" id="btn_finalizar_mantenimeinto" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Finalizar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL BUSCAR OBJETOS -->
<form id="frm_buscar_objeto" method="post">
	<div class="modal fade" id="modal_buscar_objetos_mantenimiento_gestion" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Objetos</h3>
				</div>

				<div class="modal-body" id="bodymodal">
					<div class="row" id="" style="width: 100%">

						<div class="form-group agrupado col-md-8 text-left">
							<div class="input-group">
								<input id='txt_objeto_buscado' class="form-control txt_parametro_buscado" placeholder="Buscar Parámetro">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
								</span>
							</div>
						</div>

						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tbl_objeto_buscado" cellspacing="0" width="100%">
								<thead class="titulo">
									<tr>
										<th colspan="2" class="nombre_tabla">TABLA OBJETOS</th>
										<td class="sin-borde text-right border-left-none" colspan="6">
											<span class="btn btn-default" id="btn_agregar_objeto_nuevo" title="Agregar objeto nuevo"><span class="fa fa-plus red"></span> Nuevo</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>N°</td>
										<td>OBJETO</td>
										<td class="opciones_tbl_btn">ACCION</td>
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


<!-- MODAL AGREGAR OBJETOS -->
<div class="modal fade" id="modal_guardar_objetos_mantenimiento" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="GuardarObjetosMantenimiento" method="post">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-plus"></span> Agregar objeto nuevo</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<input type="text" name="nombre_objeto_nuevo" class="form-control inputt" placeholder="Nombre del objeto" id="nombre_objeto">
						<textarea class="form-control inputt" name="descripcion" placeholder="Descripcion del Objeto"></textarea>
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


<!-- MODAL ESTADOS OBJETOS MANTENIMIENTO -->

<div class="modal fade" id="modal_objetos_estados_gest_mant" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_estado_objetos_mantenimiento">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-archive"></span> Estado Objetos Mantenimiento</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="estado_objetos_matto" class="form-control inputt cbxestado_objetos_matto filtro">
							<option value="">Seleccionar Estado</option>
						</select>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" id="btn_finalizar_mantenimeinto" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Seleccionar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- LISTA DETALLE OBJETOS MANTENIMIENTO -->

<div class="modal fade" id="modal_detalle_objetos_gest_mant" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-archive"></span> Objetos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_detalle_objetos_gest_mant" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="2" class="nombre_tabla">TABLA OBJETOS MANTENIMIENTO</td>
							</tr>
							<tr class="filaprincipal ">
								<td>Ver</td>
								<td>Objetos</td>
								<td>Fecha</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" id="btn_finalizar_mantenimeinto" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Finalizar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>


<!-- LISTAR DETALLE EVIDENCIAS MANTENIMIENTO -->

<div class="modal fade" id="modal_evidencia_gest_mant" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-photo"></span> Evidencias</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_evidencia_gest_mant" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="2" class="nombre_tabla">TABLA EVIDENCIAS MANTENIMIENTO</td>
							</tr>
							<tr class="filaprincipal ">
								<td></td>
								<td>Fecha</td>
								<td>Accion</td>
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

<!-- MODAL CARGAR EVIDENCIAS MANTENIMIENTO -->

<div class="modal fade" id="modal_registrar_evidencia" role="dialog">
	<div class="fixed fixed_viaticos div_camara">
		<div class="reque">
			<div class="login-container">
				<table class="" id="" style="width: 100%">
					<thead class="">
						<tr class="">
							<td colspan="" class="nombre_tabla"> VISTA PREVIA</td>
						</tr>
					</thead>
				</table>
				<BR>
				<div class="form-boxw text-left">
					<div class="">
						<canvas id="foto" class="img-thumbnail"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-dialog">
		<form id="form_registrar_evidencia" enctype="multipart/form-data" method="post"> -->
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Registro de Evidencia</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">

						<!-- <div>
							<select name="listaDeDispositivos" id="listaDeDispositivos"></select>
							<button id="boton">Tomar foto</button>
							<p id="estado"></p>
						</div>
						<br>
						<video muted="muted" id="video"></video>
						<canvas id="canvas" style="display: none;"></canvas> -->


						<!-- CAMARA -->
						<div class="div_camara">
							<video id="camara" autoplay muted class="img-thumbnail"></video>
							<span id='botonFoto' class="btn btn-default form-control"><span class="	fa fa-camera red"></span> Tomar Foto</span>
						</div>
						<!-- FECHA -->
						<div class="input-group oculto" style="padding-top: 6px">
							<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha</span>
							<input name="fecha_evidencia_mantenimiento" class="form-control sin_margin" title="Fecha de la evidencia mantenimiento" value="" type="date" id="fecha_evidencia">
						</div>
						<!-- OBSERVACIONES -->
						<textarea id="obseraciones_evidencia_camara" class="form-control" name="observacion_evidencia" rows="3" placeholder="Observaciones evidencias"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" id="btnGuardarVisitante" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span>
						Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>

			</div>
		</form>
	</div>
</div>





<script>
	$(document).ready(function() {
		inactivityTime();
		listar_mantenimiento_gestion();
		Cargar_parametro_buscado_aux(16, '.cbxestado', 'Seleccione Estado');
		// Cargar_parametro_buscado_aux(268, '.cbxestado_gestion', 'Seleccione Estado'); // Development
		Cargar_parametro_buscado_aux(343, '.cbxestado_gestion', 'Seleccione Estado'); // Productions
		Cargar_parametro_buscado(115, ".cbxlugar", "Seleccione Lugar");
		// Cargar_parametro_buscado_aux(269, ".cbxestado_objetos_matto", "Seleccione Estado"); // Development
		Cargar_parametro_buscado_aux(344, ".cbxestado_objetos_matto", "Seleccione Estado"); // Production

	});
</script>

<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
