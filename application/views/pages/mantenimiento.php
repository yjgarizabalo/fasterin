<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/starability-growRotate.min.css">
<?php $sw = ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Man") ? true : false; ?>
<div id="inicio-user" class="container col-md-12 text-center">
	<div class="tablausu col-md-12 text-left solicitudes mod_almacen <?php if (!$sw) echo 'oculto' ?>">
		<div class="table-responsive col-sm-12 col-md-12  tablauser">
			<p class="titulo_menu pointer btn_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table id="tabla_solicitudes" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr>
						<td colspan="5" class="nombre_tabla">TABLA SOLICITUDES DE MANTENIMIENTO <br>
							<span class="mensaje-filtro" id="textAlerta_solicitudes"></span>
						</td>
						<td class="sin-borde text-right border-left-none" colspan="7">
							<?php if ($sw) { ?>
								<span id="btn_administrar" class="btn btn-default" title="Administrar Módulo" data-toggle='popover' data-trigger='hover'> <span class="fa fa-cogs red"></span> Administrar</span>
							<?php } ?>
							<span class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#Modal_filtro">
								<span class="fa fa-filter red"></span>
								Filtrar
							</span>
							<span id="btn_limpiar_filtros" class="btn btn-default" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-refresh red"></span> Limpiar</span>
						</td>
					</tr>
					<tr class="filaprincipal ">
						<td class="opciones_tbl">Ver</td>
						<td class="opciones_tbl">No.</td>
						<td>Solicitante</td>
						<td>Ubicación</td>
						<td>Categoría</td>
						<td>Fecha Solicitud</td>
						<td>Fecha Recibido</td>
						<td>Fecha Ejecución</td>
						<td>Fecha Final</td>
						<td>Estado</td>
						<td>Calificacion</td>
						<!-- <td>Tiempo de Ejecución(Dias)</td> -->
						<td class="opciones_tbl_btn">Acción</td>
						<td>Comentario</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

	<div class="tablausu col-md-12 <?php if ($sw) echo 'oculto' ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
		<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
		</div>
		<div id="container-principal2" class="container-principal-alt">
			<h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
			<div class="row">
				<div class="" id="nueva_solicitud">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
							<span class="btn  form-control btn-Efecto-men" id="titulo_transporte" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Nueva Solicitud</span>
						</div>
					</div>
				</div>
				<div class="" id="solt3">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/Viaticos_Transporte.png" alt="...">
							<span class="btn  form-control btn-Efecto-men" id="titulo_transporte" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Estados Solicitudes</span>
						</div>
					</div>
				</div>
			</div>
			<p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
		</div>
	</div>
</div>

<div class="modal fade scroll-modal" id="modalSolicitud" role="dialog">
	<div class="modal-dialog">
		<form id="frmAgregarSolicitud" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<div>
						<h3 class="modal-title">
							<span class="fa fa-pencil-square-o"></span>
							<span id="art_titulo">Crear Solicitud</span>
						</h3>
					</div>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<?php if ($sw) { ?>
							<div id="div_responsable" class="input-group agro">
								<span class="form-control text-left pointer" id="persona">Seleccione Persona</span>
								<span class="input-group-addon red_primari pointer " title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="fa fa-search"></span></span>
								<span id="btn_borrar_persona" class="input-group-addon red_primari pointer " title="Eliminar Persona" data-toggle="popover" data-trigger="hover"><span class="fa fa-remove"></span></span>
							</div>
						<?php } ?>
						<textarea name="ubicacion" class="form-control" required placeholder="Ubicación del servicio"></textarea>
						<textarea name="descripcion_servicio" class="form-control" required placeholder="Descripción del servicio"></textarea>
						<input type="number" class="form-control" name="telefono" required min="0" placeholder="Teléfono fijo dependencia">
						<div class=''>
							<br>
							<div class="alert alert-info" role="alert" for="chkelementos">
								<label class="form-check-label" for="chkelementos">
									<input type="checkbox" class="form-check-input" name="chkelementos" id="chkelementos">
									<!-- Click aquí si necesita algún elemento para el servicio -->
									Click aquí para solicitar agua y café para su evento.
								</label>
							</div>
							<div id="elementos" class="collapse">
								<div class="agro" id="fecha_inicio_evento_div">
									<div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
										<input class="form-control CampoGeneral valor_fecha_inicio sin_focus" size="16" placeholder="Fecha Inicio Solicitud" type="text" value="" name="fecha_inicio_evento">
										<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
									</div>

									<div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
										<input class="form-control CampoGeneral valor_fecha_fin sin_focus" size="16" placeholder="Fecha Fin Solicitud" type="text" value="" name="fecha_fin_evento">
										<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
									</div>

								</div>
								<input type="number" class="form-control inputt2" name="participantes" placeholder="Cantidad de Participantes" min="0" autocomplete="off">
								<!-- <table id="tblarticulos_agregados" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
									<thead class="ttitulo ">
									<tr>
										<td colspan="2" class="nombre_tabla">TABLA ARTÍCULOS AGREGADOS</td>
										<td class="sin-borde text-right border-left-none" colspan="1" >
											<span id="sel_art" class="btn btn-default red"><span class="fa fa-plus"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal ">
										<td >Nombre Artículo</td>
										<td class="opciones_tbl">Cantidad</td>
										<td class="opciones_tbl">Opciones</td>
									</tr>
									</thead>
									<tbody>
									</tbody>
								</table> -->
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> <span class="msgboton">Solicitar</span></button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="Buscar_Articulo" role="dialog">
	<div class="modal-dialog">
		<form id="FrmBuscar_Articulo" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"> <span class="	fa fa-search"></span> Buscar Artículos</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<div class="alert alert-warning" role="alert">
							<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
							<p>En caso de que el artículo que desea no se encuentre en el inventario, por favor solicitarlo por el campo de descripción de la solicitud.</p>
						</div>
						<div class="input-group agro col-md-8">
							<input name="cod_art" type="hidden" id="input_codigo_art">
							<input type="text" class="form-control inputt2" name="articulo" placeholder="Buscar Artículo" id="txtarticulo" autocomplete="off">
							<span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_art" title="Buscar Artículo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
						</div><br>
						<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_buscar_articulos" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="4" class="nombre_tabla">TABLA ARTÍCULOS</th>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre</td>
									<td class="opciones_tbl">Opciones</td>
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
</div>

<div class="modal fade" id="Modal-info-solicitud" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="alert alert-info con_programacion">
					<strong><span class='fa fa-calendar'></span> En Programación!</strong> <span id='mensaje_programado'></span>
				</div>
				<div class="alert alert-info terceros" role="alert">
					<strong>Importante: </strong>En esta solicitud hay intervención de terceros, por lo tanto se puede presentar retraso para atender esta solicitud.
				</div>
				<table class="table table-bordered table-respon tabla_info_inventario" id="">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información General</th>
						<?php if ($sw) { ?><td class="sin-borde text-right border-left-none" colspan="2"><span id="cambiar_prioridad" class="btn btn-default cambiar_prioridad"><span class='fa fa-exchange red'></span> Cambiar Prioridad</span> <span id="ver_operarios" class="btn btn-default procesado"><span class='fa fa-users red'></span> Operarios</span> <span id="ver_historial" class="btn btn-default"><span class='fa fa-calendar red'></span> Estados</span></td> <?php } ?>
					</tr>
					<tr class="procesado">
						<td class="ttitulo"># Solicitud: </td>
						<td class="valor_num" colspan="3"></td>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante:</td>
						<td colspan="3">
							<?php if ($sw) { ?>
								<div id="detalle_persona_solicita" class="pointer red btn" title="Detalle Persona" data-toggle="popover" data-trigger="hover">
								<?php } else { ?>
									<div>
									<?php } ?>
									<span class="valor_solicitante"></span>
									</div>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Servicio:</td>
						<td class="valor_servicio" colspan="3"></td>
					</tr>
					<tr>
						<td class="ttitulo">Ubicación: </td>
						<td colspan="3" class="valor_ubicacion"></td>
					</tr>
					<tr class="tr_evento">
						<td class="ttitulo">Participantes: </td>
						<td class="valor_participantes"></td>
						<td colspan="2"><strong style="color: #d9534f">El evento requiere agua y café</strong> </td>
					</tr>
					<tr class="comentario">
						<td class="ttitulo">Comentario: </td>
						<td colspan="3" class="valor_comentario"></td>
					</tr>
					<tr>
						<td class="ttitulo">Teléfono Dependencia: </td>
						<td class="valor_telefono" colspan="3"></td>
					</tr>
					<tr class="evento">
						<td class="ttitulo">Fecha de Inicio: </td>
						<td class="valor_inicio"></td>
						<td class="ttitulo">Fecha de Fin: </td>
						<td class="valor_fin"></td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="valor_estado" colspan="3"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha Registro:</td>
						<td class="valor_fecha" colspan="3"></td>
					</tr>

					<tr class="procesado">
						<td class="ttitulo">Categoría: </td>
						<td class="valor_categoria" colspan="3"></td>
					</tr>
					<tr class="procesado">
						<td class="ttitulo">Prioridad:</td>
						<td class="valor_prioridad" colspan="3"></td>
					</tr>
					<tr class="star_rating">
						<td class="ttitulo">Calificaci&oacute;n:</td>
						<td colspan="3">
							<span class="fa fa-star ratingstar" id="1one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar" id="2one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar" id="3one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar" id="4one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar" id="5one" style="font-size:30px;"></span>
						</td>

					</tr>
					<tr class="star_rating">
						<td class="ttitulo">Fecha Calificación: </td>
						<td class="valor_fecha_calificacion"></td>
						<td class="ttitulo">Observaciones:</td>
						<td class="observacion_cal"></td>
					</tr>
					<tr class="observacion">
						<td class="ttitulo">Motivo Rechazo:</td>
						<td class="valor_observacion" colspan="3"></td>
					</tr>
					<tr id="row_firma">
						<td class="ttitulo">Firma Receptor: </td>
						<td colspan="3">
							<div id="div_firma" style="margin: 0 auto"></div>
						</td>
					</tr>
				</table>
				<div id="tabla_articulos_solicitud">
					<table id="tblarticulos_solicitud" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="4" class="nombre_tabla">TABLA ARTÍCULOS</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">No.</td>
								<td>Nombre Artículo</td>
								<td class="opciones_tbl">Cantidad</td>
								<td class="opciones_tbl">Acción</td>
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
<?php if ($sw) { ?>
	<div class="modal fade" id="gestionarSolicitud" role="dialog">
		<div class="modal-dialog">
			<form id="FrmgestionarSolicitud">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"> <span class="fa fa-calendar"></span> Gestionar Solicitud</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="input-group ">
							<select name="prioridad" required class="form-control cbxprioridad sin_margin">
								<option value="">Seleccione Prioridad</option>
							</select>
							<span class="input-group-addon">-</span>
							<select id="cbxcategoria" name="categoria" required class="form-control cbxcategoria sin_margin">
								<option value="">Seleccione Categoria</option>
							</select>
						</div><br>
						<div class="input-group ">
							<select id="cbxoperarios" class="form-control cbxoperarios sin_margin">
								<option value="">Seleccione Operario</option>
							</select>
							<span id="btn_asignar" class="input-group-addon red pointer"><span class="fa fa-plus"></span> Asignar</span>
						</div>
						<textarea name="comentario" class="form-control" placeholder="Comentario (Opcional)"></textarea><br>
						<select id="tiempo" name="tiempo" class="form-control">
							<option value="" selected>Seleccione una opción (Opcional)</option>
							<option value="programar">Programar Servicio</option>
							<option value="tercero">Intervención de Tercero</option>
						</select>
						<div id="fechas">
							<div style="padding-top: 10px;padding-bottom: 30px;">
								<div class="col-md-6" style="padding-left:0px;">
									<div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
										<input class="form-control CampoGeneral valor_fecha_inicio sin_focus" size="16" placeholder="Fecha Inicio Solicitud" type="text" value="" name="fecha_inicio_servicio" id="fecha_inicio_servicio">
										<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
									</div>
								</div>
								<div class="col-md-6" style="padding-right: 0px;">
									<div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
										<input class="form-control CampoGeneral valor_fecha_fin sin_focus" size="16" placeholder="Fecha Fin Solicitud" type="text" value="" name="fecha_fin_servicio" id="fecha_fin_servicio">
										<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
									</div>
								</div>
							</div>
						</div><br>
						<!-- <div class="agro" id="fecha_inicio_evento_div"></div> -->
						<table id="tbl_operarios" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="4" class="nombre_tabla">TABLA OPERARIOS</th>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">Ver</td>
									<td>Nombre</td>
									<td class="opciones_tbl">Opciones</td>
							</thead>
							<tbody>
							</tbody>
						</table>

					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> <span class="msgboton">Gestionar</span></button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>
<?php if ($sw) { ?>
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
								<tr>
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
<?php } ?>

<div class="modal fade" id="Modal-info-operarios" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-info-circle"></span> Información de los Operarios</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_operarios_solicitud" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="2" class="nombre_tabla">TABLA OPERARIOS ASIGNADOS</td>
								<td id="td_agregar_operario" class="sin-borde text-right border-left-none">
								</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">No.</td>
								<td>Nombre Operario</td>
								<td class="opciones_tbl">Acción</td>
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

<div class="modal fade" id="Modal-change-priority" role="dialog">
	<div class="modal-dialog modal-sm">
		<form id="frm_editar_prioridad">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"> <span class="fa fa-exchange"></span> Cambiar Prioridad</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<select class="form-control cbxprioridad sin_margin" id="cbxChangePriority" name="prioridad">
						<option value="">Seleccione Prioridad</option>
					</select><br>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> <span class="msgboton">Agregar</span></button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="Modal-info-historial" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-info-circle"></span> Historial de Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<table id="tbl_historial_solicitud" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="4" class="nombre_tabla">HISTORIAL DE ESTADOS SOLICITUD</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">No.</td>
								<td>Nombre Estado</td>
								<td class="opciones_tbl">fecha</td>
								<td>Usuario</td>
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

<div class="modal fade" id="modal_agregar_operario" role="dialog">
	<div class="modal-dialog modal-sm">
		<form id="frm_agregar_operario">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"> <span class="fa fa-calendar"></span> Agregar Operario</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<select class="form-control cbxoperarios sin_margin" id="cbx_agregar_operario">
						<option value="">Seleccione Operario</option>
					</select><br>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> <span class="msgboton">Agregar</span></button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="Modal_calificar_Solicitud" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->

		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="glyphicon glyphicon-star"></span> Calificar Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<!-- <div id="div_autenticacion">
						<div id="panel_firma" style="width: 410px; margin: 30px auto">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h2 id="titulo_panel" class="panel-title">¿Quien recibe?</h3>
								</div>
								<div class="panel-body">
									<div id="client_password">
										<div class="tab" >
											<button class="tablinks red active" onclick="openCity(event, 'Usuario')">Solicitante</button>
											<button class="tablinks red" onclick="openCity(event, 'Otro')">Otro</button>
										</div>

										<div id="Usuario" class="tabcontent">
											<form id="frm_solicitante" autocomplete="off" >
												<br>
												<div class="input-group">
												<input type="password" class="form-control limp_log" id="txtpassword" placeholder="Contraseña">
													<span class="input-group-btn">
													<button class="btn btn-danger">Confirmar</button>
													</span>
												</div>
											</form>
										</div>

										<div id="Otro" class="tabcontent oculto">
											<form id="frm_otro">
												<br>
												<div class="form-group">
													<input type="text" class="form-control limp_log" id="txt_usuario" placeholder="Usuario">
												</div>
												<div class="form-group">
													<input type="password" class="form-control limp_log" id="txt_password" placeholder="Contraseña">
												</div>
												<button type="submit" class="btn btn-danger">Confirmar</button>
											</form>
										</div>
									</div>
									<div id="div_firmar"></div>
								</div>
							</div>		
						</div>
					</div> -->
				<div id="div_calificacion">
					<div class="alert alert-warning" role="alert">
						<p><strong class="ttitulo">Nota: </strong>Tener en cuenta al momento de calificar que el Método de calificación se evalúa de la siguiente manera:</p>

						<ul>
							<li>1 estrella: Muy malo</li>
							<li>2 estrellas: Malo</li>
							<li>3 estrellas: Regular</li>
							<li>4 estrellas: Bueno</li>
							<li>5 estrellas: Excelente</li>
						</ul>
						<p>Si considera que la calidad del servicio es baja por favor dejenos una observación con los aspectos en los que podriamos mejorar.</p>
					</div>
					<form id="guardar_calificacion" method="post">
						<div style="width: 100%;">
							<div style="width: 30%; margin: 0 auto;">
								<fieldset class="starability-growRotate">
									<input type="radio" id="rate1" name="rating" class="rating" value="1" checked />
									<label for="rate1" title="Muy Malo">1 stars</label>

									<input type="radio" id="rate2" name="rating" class="rating" value="2" />
									<label for="rate2" title="Malo">2 stars</label>

									<input type="radio" id="rate3" name="rating" class="rating" value="3" />
									<label for="rate3" title="Regular">3 stars</label>

									<input type="radio" id="rate4" name="rating" class="rating" value="4" />
									<label for="rate4" title="Bueno">4 stars</label>

									<input type="radio" id="rate5" name="rating" class="rating" value="5" />
									<label for="rate5" title="Excelente">5 star</label>
								</fieldset>
							</div>
							<textarea id="rate_observation" class="form-control" placeholder="Observación" name="observacion" required></textarea>
						</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active" id="btn_calificar"><span class="glyphicon glyphicon-star"></span> Calificar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modalPersonas" role="dialog" style="overflow-y: scroll;">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Información Personas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div>
					<div>
						<form id="frmbuscar_persona">
							<div class="input-group" style="width: 300px;">
								<input id="txtbuscarPersona" class="form-control CampoGeneral" placeholder="Buscar Persona" type="text">
								<span class="input-group-addon btn" type="submit"><span class="fa fa-search"></span></span></input>
							</div>
						</form>
					</div>
					<table id="tblpersonas" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="3" class="nombre_tabla">TABLA PERSONAS</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">ID</td>
								<td>Nombre Persona</td>
								<td class="opciones_tbl">Acción</td>
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

<div class="modal fade" id="Modal_filtro" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_filter">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="categoria" class="form-control inputt cbxcategoria filtro">
							<option value="">Filtrar por Categoría</option>
						</select>
						<select name="estado" class="form-control inputt cbxestado filtro">
							<option value="">Filtrar por Estado</option>
						</select>
						<select name="departamento" class="form-control inputt cbxdepartamento filtro">
							<option value="">Filtrar por Departamento</option>
						</select>
						<div class="col-md-6" style="width:50%; padding-left: 0px;">
							<input name="fecha_inicio" class="form-control filtro" value="" type="month" name="fecha_inicio">
						</div>
						<div class="col-md-6" style="width:50%; padding-right: 0px;">
							<input class="form-control filtro" value="" type="month" name="fecha_fin">
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span> Filtrar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php if ($sw) { ?>
	<div class="modal fade scroll-modal" id="Modal-categorias" role="dialog">
		<div class="modal-dialog modal-lg">
			<form id="form_articulos_aut" enctype="multipart/form-data" method="post">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar</h3>
					</div>
					<div class="modal-body " id="bodymodal">
						<nav class="navbar navbar-default" id="nav_admin_mantenimiento">
							<div class="container-fluid">
								<ul class="nav nav-tabs nav-justified">
									<li class="pointer btn_admin_operarios active"><a><span class="fa fa-users red"></span> Operarios</a></li>
									<li class="pointer btn_admin_mantenimiento"><a><span class="fa fa-pencil-square-o red"></span> Mantenimiento Preventivo</a></li>
									<li class="pointer btn_admin_preventivo"><a><span class="fa fa-pencil-square-o red"></span> Mantenimiento Correctivo</a></li>
								</ul>
							</div>
						</nav>

						<!-- TABLA OPERARIOS -->

						<div class="container_admin_valores adm_proceso active">
							<table id="tbl_categorias" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<td colspan="2" class="nombre_tabla">TABLA OPERARIOS POR CATEGORIAS</td>
										<td class="sin-borde text-right border-left-none">
										</td>
									</tr>
									<tr class="filaprincipal">
										<td class="opciones_tbl">No.</td>
										<td>Nombre Categoría</td>
										<td class="opciones_tbl">Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<div class="modal-footer" id="footermodal">
								<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
							</div>
						</div>

						<!-- TABLA MANTENIMIENTO ANUAL -->

						<div class="articulos_cumplidos adm_proceso oculto">
							<div class="table-responsive">
								<table id="tbl_crear_mantenimiento" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
									<thead class="ttitulo">
										<tr>
											<td colspan="5" class="nombre_tabla">TABLA MANTENIMIENTO ANUAL</td>
											<td class="sin-borde text-right border-left-none" colspan="3">
												<span class="btn btn-default" title="Filtrar" data-toggle="modal" id="btn_filtrar_mantenimiento_periodico">
													<span class="fa fa-filter red"></span>
													Filtrar
												</span>
												<span class="btn btn-default" id="AgregarNuevoMtto"><span class="fa fa-plus red"></span> Nuevo</span>

											</td>

										</tr>
										<tr class="filaprincipal">
											<td class="opciones_tbl sorting_asc">Ver</td>
											<td class="opciones_tbl">No.</td>
											<td>Nombre de Mantenimiento</td>
											<td>Cantidad</td>
											<td>Periodicidad</td>
											<td>Estado</td>
											<td>Acciones</td>
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

						<!-- TABLA INSPECCIÓN PREVENTIVA LUGARES -->

						<div class="mantenimiento_preventivo adm_proceso oculto">
							<table id="tbl_crear_mantenimiento_preventivo" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<td colspan="2" class="nombre_tabla">TABLA INSPECCIÓN PREVENTIVA LUGAR</td>

										<td class="sin-borde text-right border-left-none" colspan="3">
											<span class="btn btn-default" title="Filtrar" data-toggle="modal" id="btn_filtrar_gestion">
												<span class="fa fa-filter red"></span>
												Filtrar
											</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td>Lugar</td>
										<td>Cantidad</td>
										<!-- <td class="opciones_tbl_btn sorting_asc">Acción</td> -->
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<div class="modal-footer" id="footermodal">
								<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
							</div>
						</div>

					</div>
				</div>
			</form>
		</div>
	</div>



	<!-- TABLA DE FILTRO MANTENIMIENTO PREVENTIVO -->
	<div class="modal fade" id="modal_filtro_gestion" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Filtrar Solicitudes</h3>
				</div>

				<div class="modal-body" style="width: 100%" id="bodymodal">
					<div style="width: 100%">
						<table style="margin-bottom: 0px;" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<td colspan="3" class="nombre_tabla">SELECCIONAR FILTRO</td>
								</tr>
							</thead>
						</table>
						<form id="form_filter_gestion">

							<div style="width: 100%">
								<div class="agrupado">
									<div class="col-md-6" style="padding: 3px;">
										<select name="lugar" id="lugar_gestion" class="form-control inputt cbxlugar filtro">
											<option value="">Filtrar por Lugar</option>
										</select>

									</div>
									<div class="col-md-6" style="padding: 3px">
										<select name="ubicacion" class="form-control inputt  filtro">
											<option value="">Filtrar por Ubicacion</option>
										</select>
									</div>
									<div class="col-md-6" style="padding: 3px">
										<select name="estado" class="form-control inputt cbxestado_gestion filtro">
											<option value="">Filtrar por Estados de solicitud</option>
										</select>
									</div>

									<div class="col-md-6" style="padding: 3px">
										<select name="tipo" class="form-control inputt cbxestado_objeto filtro">
											<option value="">Filtrar Tipo de Mantenimiento</option>
										</select>
									</div>

								</div>
							</div>

							<div>
								<div class="agrupado">
									<div class="col-md-6" style="width:50%; padding: 3px;">
										<input class="form-control filtro" type="month" name="fecha_inicio">
									</div>
									<div class="col-md-6" style="width:50%; padding: 3px;">
										<input class="form-control filtro" type="month" name="fecha_fin">
									</div>
								</div>
							</div>

						</form>

						<div class="modal-footer" id="footermodal" style="padding: 3px; margin-top: 20px; margin-bottom: 20px; background: transparent; border: none;">

							<span style="margin-top: 15px;" id="btn_limpiar_filtros_gestion" class="btn btn-default" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-refresh red"></span> Limpiar</span>

							<button style="margin-top: 15px" type="button" id="btn_generar_filtro" class="btn btn-danger active"><span class="fa fa-search"></span> Generar</button>
						</div>

					</div>
					<div>
						<table id="tbl_filtros_gestion_mantenimiento" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" class="nombre_tabla">SOLICITUDES MANTENIMIENTO PREVENTIVO</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Lugar</td>
									<td>Ubicacion</td>
									<td>Estado</td>
									<td>Fecha Inicio</td>
									<td>Fecha Fin</td>
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

	<!-- TABLA DE FILTROS MANTENIMIENTO PERIODICO -->
	<div class="modal fade" id="modal_filtro_mantenimiento_periodico" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog modal-lg">

			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Filtrar Solicitudes</h3>
				</div>

				<div class="modal-body" style="width: 100%" id="bodymodal">
					<div style="width: 100%">
						<table style="margin-bottom: 0px;" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<td colspan="3" class="nombre_tabla">SELECCIONAR FILTRO</td>
								</tr>
							</thead>
						</table>
						<form id="form_filter_mantenimiento_periodico">

							<div style="width: 100%">
								<div class="agrupado">
									<div class="col-md-6" style="padding: 3px;">
										<select name="lugar" id="lugar_gestion" class="form-control inputt cbxlugar filtro">
											<option value="">Filtrar por Lugar</option>
										</select>

									</div>
									<div class="col-md-6" style="padding: 3px">
										<select name="periodicidad" class="form-control inputt  filtro cbxperiodicidad">
											<option value="">Filtrar por periodicidad</option>
										</select>
									</div>
									<div class="col-md-6" style="padding: 3px">
										<select name="estado" class="form-control inputt cbxestado_gestion filtro">
											<option value="">Filtrar por Estados de solicitud</option>
										</select>
									</div>

									<div class="col-md-6" style="padding: 3px">
										<select name="tipo" class="form-control inputt cbxestado_objeto filtro">
											<option value="">Filtrar Tipo de Mantenimiento</option>
										</select>
									</div>

								</div>
							</div>

							<div>
								<div class="agrupado">
									<div class="col-md-6" style="width:50%; padding: 3px;">
										<input class="form-control filtro" type="month" name="fecha_inicio">
									</div>
									<div class="col-md-6" style="width:50%; padding: 3px;">
										<input class="form-control filtro" type="month" name="fecha_fin">
									</div>
								</div>
							</div>

						</form>

						<div class="modal-footer" id="footermodal" style="padding: 3px; margin-top: 20px; margin-bottom: 20px; background: transparent; border: none;">

							<span style="margin-top: 15px;" id="btn_limpiar_filtros_gestion" class="btn btn-default" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-refresh red"></span> Limpiar</span>

							<button style="margin-top: 15px" type="button" id="btn_generar_filtro_mantenimiento_periodico" class="btn btn-danger active"><span class="fa fa-search"></span> Generar</button>
						</div>

					</div>
					<div>
						<table id="tbl_filtros_mantenimiento_perodico" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" class="nombre_tabla">SOLICITUDES MANTENIMIENTO PERODICO</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Lugar</td>
									<td>Periodicidad</td>
									<td>Estado</td>
									<td>Fecha Inicio</td>
									<td>Fecha Fin</td>
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

	<!-- MANTENIMIENTOS PERIODICOS -->

	<!-- MODAL DETALLES SOLICITUD DE MANTENIMIENTO PERIODICO -->

	<!-- 2 -->
	<div class="modal fade" id="modal_detalles_mantemiento" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Detalles de Solicitud</h3>
				</div>

				<div class="modal-body" id="bodymodal">
					<table class="table table-bordered table-condensed tabla_info_inventario" id="tbl_detalles_mantenimiento">

						<tr>
							<td colspan="1" class="nombre_tabla text-left">Mantenimiento</td>
							<td colspan="1" class="editar_solicitud"><button id="editar_solicitud" type="button" class="btn btn-default active"><span class="fa fa-edit"></span> Editar</button></td>
						</tr>


						<tr>
							<td class="ttitulo" colspan="1">Nombre</td>
							<td class="nombre_mantenimiento" colspan="1"></td>
						</tr>
						<tr>
							<td class="ttitulo" colspan="1">Numero de Notificaciones: </td>
							<td class="numero_notificaciones" colspan="1"></td>
						</tr>
						<tr>
							<td class="ttitulo" colspan="1">Mes inicio de Notificaciones: </td>
							<td class="mes_inicio_notificacion" colspan="1"></td>

						</tr>
						<tr>
							<td class="ttitulo" colspan="1">Numero de dias entre Notificaciones: </td>
							<td class="dia_entre_notificacion" colspan="1"></td>
						</tr>
						<tr>
							<td class="ttitulo" colspan="1">Periodicidad </td>
							<td class="periodicidad" colspan="1"></td>
						</tr>

						<tr>
							<td class="ttitulo" colspan="1">Observaciones</td>
							<td class="observaciones" colspan="1"></td>
						</tr>

						<tr>
							<td class="ttitulo" colspan="1">Fecha Inicio</td>
							<td class="fecha_inicio" colspan="1"></td>
						</tr>

						<tr>
							<td class="ttitulo" colspan="1">Fecha Fin</td>
							<td class="fecha_fin" colspan="1"></td>
						</tr>

					</table>


					<div class="table-responsive">
						<table id="tbl_detalle_matenimiento_periodico" class="table  table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" class="nombre_tabla">TABLA DE SOLICITUDES DE MANTENIMIENTO PERIODICO</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Mantenimiento</td>
									<td>Fecha inicio</td>
									<td>Fecha fin</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<div class="alert alert-info">
						Tener en cuenta subir las evidencias fotografica del mantemiento realizado.
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL LUGALES MANTENIMIENTOS PERIODICOS EVIDENCIAS -->

	<!-- 3 -->
	<div class="modal fade" id="modal_lugares_mantenimiento_periodico_detalles_evidencia" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Lugares Mantenimiento Periodico</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbl_lugares_mantenmiento_periodicos_detalles_evidencias" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="" class="nombre_tabla">TABLA LUGARES MANTENIMIENTO PERIODICO DETALLES</td>
								</tr>
								<tr class="filaprincipal ">
									<td>No.</td>
									<td>Lugar</td>
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

	<!-- MODAL LUGARES MANTENIMIENTOS PERIODICOS -->

	<div class="modal fade" id="modal_lugares_mantenimiento_periodico" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Lugares Mantenimiento Periodico</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbl_lugares_mantenimaiento_periodico" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">

							<thead class="ttitulo">
								<tr>
									<td colspan="3" class="nombre_tabla">TABLA LUGARES MANTENIMIENTO PERIODICO</td>
									<td class="sin-borde text-right border-left-none" colspan="2">

										<span class="btn btn-default" title="Buscar lugar a reparar" data-toggle="modal" id="btn_agregar_lugar_mantenimiento_periodico">
											<span class="fa fa-search red"></span>
											Buscar
										</span>

									</td>

								</tr>
								<tr class="filaprincipal">
									<td>No.</td>
									<td>Lugar / Mes</td>
									<td>Tipo</td>
									<td>Acción</td>
								</tr>
							</thead>

							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" id="btn_finalizar_mantenimeinto1" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Finalizar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>


	<!-- MODAL ESTADOS LUGARES MANTENIMIENTO -->

	<form id="form_estado_lugares_mantenimiento" method="post">
		<div class="modal fade" id="modal_lugares_estados_matto" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">

					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Estado Lugares Mantenimiento</h3>
					</div>

					<div class="modal-body" id="bodymodal">
						<div class="row" id="" style="width: 100%">

							<div class="row">
								<select name="estado_lugares_matto" class="form-control inputt cbxestado_objetos_matto filtro">
									<option value="">Seleccionar Estado</option>
								</select>
							</div>

						</div>
					</div>

					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</form>


	<!-- MODAl CARGAR EVIDENCIAS - RECIBIR ARCHIVOS MANTENIMIENTOS PERIODICOS -->

	<div class="modal fade" id="modal_evidencias_mantenimiento_periodico" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-folder-open"></span> Evidencias</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="alert alert-info">
						Tener en cuenta subir las eviendicias de los mantenimiento periodicos.
					</div>

					<label>Adjuntar fotografias</label>
					<form class="dropzone needsclick dz-clickable" id="Subir" action="">
						<div>
							<label>Ingresar comentario</label>
							<input type="text" class="form-control rounded" name='comentario_evidencia' placeholder='Comentario'></input>
							<input type="hidden" name="id_solicitud" id="id_solicitud" val="0">
						</div>


						<div class="">
							<div class="dz-message needsclick">
								<p>Arrastre archivos o presione click aquí</p>
							</div>
						</div>


					</form>


				</div>
				<div class="modal-footer" id="footermodal">
					<button id="cargar_adjuntos_general" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>


	<!-- MODAL BUSCAR LUGAR MANTENIMIENTO PERIODICO -->
	<form id="frm_buscar_lugar" method="post">
		<div class="modal fade" id="modal_buscar_lugar_mantenimiento_periodico" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">

					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Lugar</h3>
					</div>

					<div class="modal-body" id="bodymodal">
						<div class="row" id="" style="width: 100%">

							<div class="form-group agrupado col-md-12 text-left">
								<div class="input-group">
									<input id='txt_lugar' class="form-control txt_parametro_buscado" placeholder="Buscar Parámetro">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
									</span>
								</div>
							</div>

							<div class="table-responsive col-md-12" style="width: 100%">
								<table class="table table-bordered table-hover table-condensed pointer" id="tbl_lugares_buscado" cellspacing="0" width="100%">
									<thead class="titulo">
										<tr>
											<th colspan="2" class="nombre_tabla">TABLA LUGARES</th>
											<td class="sin-borde text-right border-left-none" colspan="6">
											</td>
										</tr>
										<tr class="filaprincipal">
											<td>N°</td>
											<td>LUGARES</td>
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


	<!-- LISTAR EVIDENCIAS MANTENIMIENTO PERIODICO-->

	<div class="modal fade" id="modal_evidencia_mantenimiento_periodico" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-photo"></span> Evidencias Mantenimiento Periodico</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbl_evidencia_mantenimiento_periodico" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="6" class="nombre_tabla">TABLA EVIDENCIAS MANTENIMIENTO PERIODICO</td>

								</tr>
								<tr class="filaprincipal ">
									<td>Nombre</td>
									<td>Comentario</td>
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


	<!-- MENTENIMIENTOS PREVENTIVOS -->

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
									<td>Objetos</td>
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


	<!-- MODAL UBIACIONES MANTENIMIENTO -->

	<div class="modal fade" id="modal_ubiaciones_mantenimiento" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-location-arrow"></span> Ubicaciones</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbl_ubiaciones_mantenimaiento" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" class="nombre_tabla">TABLA UBICACIONES</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Ubicaciones</td>
									<td>Cantidad</td>
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

	<div class="modal fade" id="modal_objetos_mantenimiento" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-archive"></span> Objetos</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbl_objetos_mantenimaiento" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="" class="nombre_tabla">TABLA OBJETOS</td>
									<td class="sin-borde text-right border-left-none" colspan="6">
										<span class="btn btn-default" id="btn_agregar_objeto_mantenimiento" title="Agregar objeto a reparar"><span class="fa fa-search red"></span> Buscar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Nombre objeto</td>
									<!-- <td>Cantidad</td> -->
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

	<!-- MODAL BUSCAR OBJETOS -->
	<form id="frm_buscar_objeto" method="post">
		<div class="modal fade" id="modal_buscar_objetos_mantenimiento" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">

					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Objetos</h3>
					</div>

					<div class="modal-body" id="bodymodal">
						<div class="row" id="" style="width: 100%">

							<div class="form-group agrupado col-md-12 text-left">
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

	<div class="modal fade" id="Modal_operarios_categoria" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span>Información de los Operarios</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbloperarios_categoria" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="2" class="nombre_tabla">TABLA OPERARIOS</td>
									<td class="sin-borde text-right border-left-none">
										<span id="btnoperarios" class="btn btn-default red"><span class="fa fa-user-plus"></span> Agregar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre Operario</td>
									<td class="opciones_tbl">Acción</td>
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

	<!-- INFORMACION DE LOS OPERATIVOS -->

	<div class="modal fade" id="modalOperarios" role="dialog" style="overflow-y: scroll;">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span>Información de los Operarios</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table id="tbloperarios" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" class="nombre_tabla">TABLA OPERARIOS</td>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre Operario</td>
									<td class="opciones_tbl">Acción</td>
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

	<!-- CREAR MANTEMIENTO -->

	<div class="modal fade" id="Modal_Add_Mtto" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<form id="form_agregar_mantenimiento">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-cog"></span> Crear Mantenimiento Anual</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<label>Ingresar Nombre del Mantenimiento</label>
							<input type="text" name="nombre_mantenimiento" id="nombre_mantenimiento" class="form-control inputt" placeholder="Nombre Mantenimento" value="">
							<label style="margin-top: 10px">Seleccione la periodicidad</label>
							<select name="periodicidad" id="periodicidad" class="form-control inputt cbxperiodicidad">
								<option value="">Periodicidad</option>
							</select>

							<label style="margin-top: 10px">Numero de notificaciones</label>
							<input type="number" name="numero_notificaciones" id="numero_notificaciones" class="form-control inputt" placeholder="Numero notificaciones">
							<label style="margin-top: 10px">Mes de inicio de la notificacion</label>
							<select name="mes_inicio_notificacion" id="mes_inicio_notificacion" class="form-control inputt cbxmes_inicio_notificacion">
								<option value="">Mes inicio notificaciones</option>
							</select>
							<label style="margin-top: 10px">Días entre notificaciones</label>
							<input type="number" name="dia_entre_notificacion" id="dia_entre_notificacion" class="form-control inputt" placeholder="Numero de días entre notificaciones">
							<label style="margin-top: 10px">Comentario</label>
							<textarea class="form-control" cols="1" rows="3" name="observacion_mantenimiento" placeholder="Observaciones" id="observacion_mantenimiento" maxlength="199"></textarea>
							<input type="hidden" name="id_solicitud_mantenimiento" id="id_solicitud_mantenimiento">

							<div class="alert alert-info" style="margin-top: 10px">
								<strong><span class='fa fa-warning'></span> ¡Importante!</strong> <span>Para que se programen las notificaciones de manera correcta es necesario llenar los campos de <strong>número de notificaciones, mes de inicio de notificaciones y número de días entre notificaciones</strong>.</span>
								<p>
								</p>
							</div>
						</div>
					</div>


					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active"><span class=""></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- CREAR INSPECCION PREVENTINA -->

	<div class="modal fade" id="modal_inspeccion_preventiva" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<form id="form_inspeccion_preventivo">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-cog"></span> Crear Inspección preventiva</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<select name="lugar" class="form-control inputt cbxlugar">
								<option value="">Lugar</option>
							</select>
							<select name="ubicacion" class="form-control inputt cbxubicacion">
								<option value="">Ubicación</option>
							</select>

							<div class="input-group agro">
								<select name="objeto_asignado2" class="form-control recursos_agregados sin_margin" id="objeto_asignado_reparacion2">
									<option value="">0 Objeto(s) a Reservar</option>
								</select></select>

								<span class="input-group-addon  btnElimina pointer " id="retirar_objeto_sele2" title="Retirar Objeto" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove "></span></span>

								<span class="input-group-addon  btnAgregar pointer" id="mas_objetos_reparar2" title="Mas Objetos" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus "></span> </span>
							</div>

							<textarea class="form-control" cols="1" rows="3" name="observacion_mantenimiento" placeholder="Observaciones" id="observaciones_mantenimeinto" maxlength="199"></textarea>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active"><span class=""></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- MODAL SELECCIONAR OBEJETOS A REPARAR -->
	<div class="modal fade" id="modal_seleccionar_objeto" role="dialog">

		<div class="modal-dialog <?php echo $sw ? 'modal-lg' : ''; ?>">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cog"></span> Objetos a Reparar</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<p><span class="glyphicon glyphicon-map-marker red"></span><b><span class="objeto_sele"> 0</span></b> a reparar</p>
					<div class="table-responsive" style="width: 100%">
						<table class="table table-bordered table-hover table-condensed pointer" id="tbl_objetos_mantenimiento" cellspacing="0" width="100%">
							<thead class="ttitulo ">

								<tr class="">
									<td colspan="6" class="nombre_tabla">Tabla de Objetos</td>
								</tr>
								<tr class="filaprincipal">
									<td>Nombre</td>
									<td>Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>

					</div>
				</div>


				<div class="modal-footer footer-add-persona" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>

			</div>

		</div>
	</div>

	<div class="modal fade" id="modal_gest_mtto" role="dialog" style="overflow-y: scroll;">
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
									<td colspan="2" class="nombre_tabla">TABLA MANTENIMIENTO</td>
									<td class="sin-borde text-right border-left-none" colspan="6">
										<span class="btn btn-default" id="btn_agregar_objeto_mantenimiento_gestion" title="Agregar objeto a reparar"><span class="fa fa-search red"></span> Buscar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Ubicación</td>
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
								<td class="ttitulo">Periodo:</td>
								<td class="info_periodo"></td>
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
									<td>Nombre</td>
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




<?php } ?>
<script>
	$(document).ready(() => {
		listar_solicitudes(<?php echo $id; ?>);
		listar_mantenimiento_gestion();
		Cargar_parametro_buscado_aux(59, ".cbxprioridad", "Seleccione Prioridad");
		Cargar_parametro_buscado_aux(58, ".cbxcategoria", "Seleccione Categoría");
		Cargar_parametro_buscado_aux(57, '.cbxestado', 'Seleccione Estado');

		Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Departamento");
		Cargar_parametro_buscado(115, ".cbxlugar", "Seleccione Lugar");
		Cargar_parametro_buscado(231, ".cbxperiodicidad", "Seleccione Periodicidad");
		// Cargar_parametro_buscado_aux(16, '.cbxestado', 'Seleccione Estado');
		// Cargar_parametro_buscado_aux(268, '.cbxestado_gestion', 'Seleccione Estado'); // Development
		Cargar_parametro_buscado_aux(343, '.cbxestado_gestion', 'Seleccione Estado'); // Production
		// Cargar_parametro_buscado_aux(269, '.cbxestado_objeto', 'Seleccione Tipo de Mantenimiento'); // Development
		Cargar_parametro_buscado_aux(344, '.cbxestado_objeto', 'Seleccione Tipo de Mantenimiento'); // Production
		// Cargar_parametro_buscado_aux(269, ".cbxestado_objetos_matto", "Seleccione Estado"); // Developement
		Cargar_parametro_buscado_aux(344, ".cbxestado_objetos_matto", "Seleccione Estado"); // Production
		// Cargar_parametro_buscado(272, ".cbxmes_inicio_notificacion", "Selecciona el mes de inicio de noficación"); // Development
		Cargar_parametro_buscado(346, ".cbxmes_inicio_notificacion", "Selecciona el mes de inicio de noficación"); // Production
		inactivityTime();
		cargar_archivos_general(`${Traer_Server()}index.php/mantenimiento_control/guardar_evidencias_mantenimientos_periodicos`);
	});
</script>

<script type="text/javascript">
	let date = new Date();
	<?php if (!$sw) { ?>
		const weekDay = date.getDay();
		if (weekDay == 4) date.setDate(date.getDate() + 5);
		else if (weekDay == 5) date.setDate(date.getDate() + 4);
		else date.setDate(date.getDate() + 2);
		date = `${date.getFullYear()}-${date.getMonth()+1}-${date.getDate()} 06:30`;

	<?php } ?>
	$(".form_datetime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language: 'es',
		autoclose: true,
		initialDate: new Date(),
		startDate: date,
		<?php if ($sw) { ?>
			todayBtn: true,
			daysOfWeekDisabled: [0],
		<?php } else { ?>
			daysOfWeekDisabled: [0, 6],
		<?php } ?>
	});
</script>

<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>
