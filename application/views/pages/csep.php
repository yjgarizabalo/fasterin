<?php
$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" ? true : false;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="tablausu col-md-12 <?php if ($administra || !empty($id)) { ?>oculto<?php } ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
	<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
	<div id="container-principal2" class="container-principal-alt">
		<h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
		<div class="row">
			<div id="capa_csep"></div>
			<div id="btn_precsep">
				<div class="thumbnail">
					<div class="caption text-center">
						<img src="<?php echo base_url() ?>/imagenes/Inventario.png" alt="...">
						<span class="btn  form-control btn-Efecto-men">Requisición</span>
					</div>
				</div>
			</div>
			<div id="btn_nuevo_csep">
				<div class="thumbnail">
					<div class="caption text-center">
						<img src="<?php echo base_url() ?>/imagenes/talento_humano.png" alt="...">
						<span class="btn  form-control btn-Efecto-men">Nuevo CSEA</span>
					</div>
				</div>
			</div>
			<div id="btn_modulo_csep">
				<div class="thumbnail">
					<div class="caption text-center">
						<img src="<?php echo base_url() ?>/imagenes/Viaticos_Transporte.png" alt="...">
						<span class="btn  form-control btn-Efecto-men">MIS SOLICITUDES</span>
					</div>
				</div>
			</div>
		</div>
		<p class="titulo_menu titulo_menu_alt pointer regresar" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
	</div>
</div>

<div class="modal fade" id="modal_solicitud_vacante" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<?php if ($_SESSION['perfil'] != 'Per_Csep') { ?>
			<form id="form_vacante" enctype="multipart/form-data" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-address-book-o"></span> <span class="texto_accion"></span> Requisición</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div id="botones_modificar" style="display: flex;flex-direction: row-reverse;" hidden>
								<span id="btn_ver_materias" class="btn btn-default"><span class="fa fa-list materias red"></span> Materias</span>
								<span id="btn_ver_dependencias" class="btn btn-default"><span class="fa fa-building dependencias red"></span> Dependencias</span>
							</div>
							<select name="tipo_cargo" class="form-control cbxtipo_cargo" required></select>
							<select id="cbxtipo_solicitud" name="tipo_solicitud" class="form-control no-revisar"></select>
							<select id="cbxtipo_vacante" name="tipo_vacante" class="form-control cbxtipo_vacante no-revisar"></select>
							<div id="div_persona" class="agro agrupado div_oculto no-revisar" hidden></div>
							<select name="id_programa" class="form-control cbxdepartamento">
								<option value="">Seleccione Departamento</option>
							</select>
							<select name="cargo_id" class="form-control">
								<option value="">Seleccione Cargo</option>
							</select>
							<div class="req_adm">
								<div class="div_oculto no-revisar div_evaluacion" hidden></div>
								<input type="text" class="form-control" placeholder="Pregrado Requerido" name="pregrado">
								<input type="text" class="form-control" placeholder="Posgrado Requerido" name="posgrado">
								<textarea name="experiencia" class="form-control" placeholder="Experiencia Laboral"></textarea>
								<textarea name="conocimientos_especificos" class="form-control" placeholder="Conocimientos específicos para el cargo"></textarea>
								<textarea name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
							</div>
							<div class="req_aca">
								<div class="input-group adicional_info" style="padding-top: 6px;">
									<select class="sin_margin form-control" id="cbxprogramas">
										<option value="">0 Programas/Dependencias Asignados</option>
									</select>
									<span class="input-group-addon pointer" id='btn_add_dpto'>
										<span class='fa fa-plus red'></span>
									</span>
									<span class="input-group-addon pointer" id="btn_remove_dpto">
										<span class='glyphicon glyphicon-remove  red'></span>
									</span>
								</div>
								<input type="number" min='1' class="form-control no-revisar" placeholder="Horas de Clases" name="horas">
								<div class="div_oculto no-revisar div_evaluacion" hidden></div>
								<div id="div_apertura" class="div_oculto no-revisar"></div>
								<textarea name="plan_trabajo" class="form-control" placeholder="Digite aquí el plan de trabajo"></textarea>
								<div class="adicional_info">
									<h4 class="red">Agregar Materias</h4>
									<div class="agro agrupado">
										<div class="input-group">
											<input type="text" class="form-control sin_margin" placeholder="Digite Nombre de la Materia" id='txt_nombre_materia'>
											<span class="input-group-addon pointer" id='btnagregar_materia' style='background-color:white'>
												<span class='fa fa-plus red'></span> Agregar
											</span>
										</div>
									</div>
									<div class="input-group agro">
										<select id="materias_asignadas" name="materias_asignadas" class="form-control materias_asignadas sin_margin">
											<option value="">0 Materias Asignadas</option>
										</select>
										<span class="input-group-addon red  btnElimina pointer" id="retirar_materia" title="Retirar MAteria" data-toggle="popover" data-trigger="hover">
											<span class="glyphicon glyphicon-remove "></span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> <span class="texto_accion"></span></button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		<?php } else { ?>
			<form id="form_vacante" enctype="multipart/form-data" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-address-book-o"></span> <span class="texto_accion"></span> Solicitud</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<label class="red" for="id_programa">Departamento</label>
							<select id="cbxprograma" name="id_programa" class="form-control cbxdepartamento" required></select>
							<select name="cargo_id" class="form-control" required></select>
							<div class="input-group" style="padding-top: 6px;">
								<select class="sin_margin form-control" id="cbxprogramas">
									<option value="">0 Programas/Dependencias Asignados</option>
								</select>
								<span class="input-group-addon pointer" id='btn_add_dpto'>
									<span class='fa fa-plus red'></span>
								</span>
								<span class="input-group-addon pointer" id="btn_remove_dpto">
									<span class='glyphicon glyphicon-remove  red'></span>
								</span>
							</div>
							<label class="red" for="plan_trabajo" style="padding-top: 10px;">Plan de Trabajo</label>
							<textarea name="plan_trabajo" class="form-control" placeholder="Digite aquí el plan de trabajo"></textarea>

						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> <span class="texto_accion"></span></button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		<?php } ?>
	</div>
</div>

<?php if ($administra) { ?>
	<div class="modal fade" id="modal_revisar_requisicion" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<form id="form_revisar_requisicion" enctype="multipart/form-data" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-address-book-o"></span> <span class="texto_accion"></span> Requisición</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div id="botones_modificar" style="display: flex;flex-direction: row-reverse;" hidden>
								<span id="btn_materias" class="btn btn-default"><span class="fa fa-list materias red"></span> Materias</span>
								<span id="btn_dependencias" class="btn btn-default"><span class="fa fa-building dependencias red"></span> Dependencias</span>
							</div>
							<select id="cbxprograma" name="id_programa" class="form-control cbxdepartamento" required></select>
							<select name="cargo_id" class="cbxcargos form-control" required>
								<option value="">Seleccione Cargo</option>
							</select>
							<textarea name="plan_trabajo" class="form-control" placeholder="Digite aquí el plan de trabajo"></textarea>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> <span class="texto_accion"></span></button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>

<div class="modal fade" id="modal_notificaciones" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div id="panel_notificaciones_comite" style="width: 100%" class="list-group">
				</div>
				<div id="panel_notificaciones_generales" style="width: 100%" class="list-group">
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?php if ($administra) { ?>

	<div class="modal fade" id="modal_administrar_solicitudes" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<?php if ($administra) { ?>
						<nav class="navbar navbar-default" id="nav_admin_csep">
							<div class="container-fluid">
								<ul class="nav navbar-nav">
									<li class="pointer active" id="admin_comite"><a><span class="fa fa-folder red"></span> Comité</a></li>
									<li class="pointer" id="admin_csep"><a><span class="fa fa-edit red"></span> Permisos</a></li>
								</ul>
							</div>
						</nav>
					<?php  } ?>
					<div class="table-responsive">
						<div id="container_admin_comite">
							<table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<td colspan="5" class="nombre_tabla">TABLA COMITÉ</td>
										<td class="btnAgregar sin-borde text-center"><span data-toggle="modal" data-target="#modal_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span>Nuevo</span></td>
									</tr>
									<tr class="filaprincipal ">
										<td class="opciones_tbl">Ver</td>
										<td>Nombre</td>
										<td>Descripción</td>
										<td>#Post.</td>
										<td>Estado</td>
										<td class="opciones_tbl_btn">Acción</td>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div id="container_admin_csep" class="oculto">
							<div id='container_mensaje_ap'></div>
							<div id='container_mensaje_ap_cat'></div>
							<div class="form-group col-md-6">
								<div class="agro agrupado sin_margin">
									<select required class="form-control" id='cbx_personas_vb_csep'>
										<option value="">Seleccione Persona</option>
									</select>
								</div>
							</div>
							<table class="table table-bordered table-hover table-condensed" id="tabla_programas_csep" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<td class="nombre_tabla">TABLA PROGRAMAS</td>
										<td colspan="3" class="sin-borde text-right border-left-none"> <span class="black-color btn btn-default " id="btn_asignar_todo"><span class="fa fa-check" style='color: #39B23B'></span> Asignar Todo</span> <span class="black-color btn btn-default" id="btn_retirar_todo"><span class="fa fa-remove" style='color: #d9534f'></span> Retirar Todo</span></td>
									</tr>
									<tr class="filaprincipal ">
										<td>Nombre</td>
										<td>Descripción</td>
										<td>Estado</td>
										<td class="opciones_tbl_btn">Acción</td>
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
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" required>
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

	<div class="modal fade" id="modal_cambiar_persona" role="dialog">
		<div class="modal-dialog">
			<form action="#" id="form_cambiar_persona" method="post">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-refresh"></span> Cambiar Persona</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<select required class="form-control" id='cbx_personas_change'>
								<option value="">Seleccione Persona</option>
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
<?php } ?>

<div id="container_solicitudes" class='div_table' <?php echo (!$administra && !empty($id) || $administra) ? '' : 'hidden'; ?> <div class="container col-md-12" id="inicio-user">
	<div class="tablausu col-md-12 text-left">
		<div class="table-responsive">
			<p class="titulo_menu pointer regresar" id='inicio_return_csep'><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr>
						<td colspan="3" class="nombre_tabla">
							TABLA SOLICITUDES CSEA<br>
							<span class="mensaje-filtro" hidden>
								<span class="fa fa-bell red"></span>
								La tabla tiene algunos filtros aplicados.
							</span>
						</td>
						<td class="sin-borde text-right border-left-none" colspan="4">
							<?php if ($administra) { ?>
								<span class="btn btn-default" id="btn_notificaciones">
									<span class="n_notificaciones red">0</span> Notificaciones
								</span>
								<span class="black-color pointer btn btn-default" id="btn_administrar">
									<span class="fa fa-cog red"></span> Administrar
								</span>
							<?php } ?>
							<span class="black-color pointer btn btn-default" id="filtros">
								<span class="fa fa-filter red"></span> Filtrar
							</span>
							<span class="black-color pointer btn btn-default" id="limpiar_filtros">
								<span class="fa fa-refresh red"></span> Limpiar
							</span>
						</td>
					</tr>
					<tr class="filaprincipal">
						<td class="opciones_tbl">ver</td>
						<td>Tipo</td>
						<td>Fecha</td>
						<td>Solicitante</td>
						<td>Estado</td>
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

<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
	<div class="modal-dialog modal-lg modal-95">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div id='container_tabla_postulantes' class='table-responsive'>
					<table class="table table-bordered table-hover table-condensed " id="tabla_postulantes_csep" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="9" class="nombre_tabla">TABLA POSTULANTES CSEA</td>
								<td class="sin-borde text-center border-left-none"></td>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">ver</td>
								<td>Tipo</td>
								<td>Postulante</td>
								<td>HV</td>
								<td>Programa</td>
								<td>Cargo</td>
								<td>#Apr.</td>
								<td>#Neg.</td>
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

<div class="modal fade" id="Modal_filtro" role="dialog">
	<div class="modal-dialog">
		<form id="form_filtro">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select id="tipo_filtro" name="tipo_solicitud" class="form-control inputt cbxtipo filtro">
							<option value="">Filtrar por Tipo de Solicitud</option>
						</select>
						<select id="estado_filtro" name="estado" class="form-control inputt cbxestado filtro">
							<option value="">Filtrar por Estado</option>
						</select>
						<div class="col-md-6" style="width:50%; padding-left: 0px;">
							<input id="fecha_i_filtro" name="fecha_inicio" class="form-control filtro" value="" type="month">
						</div>
						<div class="col-md-6" style="width:50%; padding-right: 0px;">
							<input id="fecha_f_filtro" class="form-control filtro" value="" type="month" name="fecha_fin">
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="btn_filtrar"><span class="fa fa-filter"></span> Filtrar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<form id="form_gestionar_postulante" method="post">
	<div class="modal fade" id="modal_gestionar_postulante" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-refresh"></span> Gestionar Postulante</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="id_programa" class="form-control  cbxprogramas"> </select>
						<select name='id_comite' class="form-control comites_combo" required="true">
							<option value="">Seleccione Comité</option>
						</select>
						<textarea name="plan_trabajo" class="form-control" placeholder="Plan Trabajo" required="true"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Terminar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>

		</div>
	</div>
</form>

<form id="form_modificar_postulante_cmt" method="post">
	<div class="modal fade" id="modal_modificar_postulante_cmt" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Postulante</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="id_programa" class="form-control  cbxprogramas"> </select>
						<select name='id_comite' class="form-control comites_combo" required="true">
							<option value="">Seleccione Comité</option>
						</select>
						<textarea name="plan_trabajo" class="form-control" placeholder="Plan Trabajo" required="true"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Modificar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>

		</div>
	</div>
</form>

<div class="modal fade" id="modal_asignar_postulante" role="dialog">
	<div class="modal-dialog">
		<form id="form_asignar_postulante" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Nuevo CSEA</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div id='msj_tipo_cambio'></div>
						<select name="id_tipo" required class="form-control  cbx_tipo_postulante"> </select>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_nombre_postulante'>
								<span class="input-group-addon pointer" id='btn_buscar_postulante' style='	background-color:white'><span class='fa fa-search red'></span> Postulante</span>
							</div>
						</div>
						<span class='nombre_tabla form-control container_tip_Ca oculto'>Información Actual</span>
						<div class='container_tip_Ca oculto'>
							<select name="id_departamento_actual" class="form-control  cbxdepartamento"> </select>
							<select name="id_cargo_actual" class="form-control">
								<option value="">Seleccione Cargo</option>
							</select>
						</div>
						<div id='cont_nuevos'>
							<span class='nombre_tabla form-control container_tip_Ca oculto'>Información Nueva</span>
							<input type="text" name="procedencia" class="form-control" placeholder="Ciudad Procedencia" required>
							<select name="id_departamento" required class="form-control  cbxdepartamento"> </select>
							<select name="id_cargo" required class="form-control">
								<option value="">Seleccione Cargo</option>
							</select>

							<select name="id_formacion" required class="form-control cbxformacion"></select>
							<div class="agrupado">
								<div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="hoja_vida" type="file" style="display: none;" id="hoja_vida"></span></label><input type="text" class="form-control" readonly placeholder='Hoja de vida'></div>
							</div>
							<div class="agrupado" id='container_adj_prueba'>
								<div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="prueba_psicologia" type="file" style="display: none;" id="prueba_psicologia"></span></label><input type="text" class="form-control" readonly placeholder='Informe Evaluativo'></div>
							</div>
						</div>
						<textarea class="form-control comentarios" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_modificar_postulante_solicitud" role="dialog">
	<div class="modal-dialog">
		<form id="form_modificar_postulante_solicitud" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Modificar Postulante</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div id='msj_tipo_cambio_modi'></div>
						<select name="id_tipo" required class="form-control cbx_tipo_postulante"> </select>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_nombre_postulante_modi'>
								<span class="input-group-addon pointer" id='btn_buscar_postulante_modi' style='background-color:white'><span class='fa fa-search red'></span> Postulante</span>
							</div>
						</div>
						<span class='nombre_tabla form-control container_tip_Ca_modi oculto'>Información Actual</span>
						<div class='container_tip_Ca_modi oculto'>
							<select name="id_departamento_actual" class="form-control  cbxdepartamento"> </select>
							<select name="id_cargo_actual" class="form-control">
								<option value="">Seleccione Cargo</option>
							</select>
						</div>
						<div id='cont_nuevos_modi'>
							<span class='nombre_tabla form-control container_tip_Ca_modi oculto'>Información Nueva</span>
							<input type="text" name="procedencia" class="form-control" placeholder="Ciudad Procedencia" required>
							<select name="id_departamento" required class="form-control  cbxdepartamento"> </select>
							<select name="id_cargo" required class="form-control">
								<option value="">Seleccione Cargo</option>
							</select>
							<select name="id_formacion" required class="form-control cbxformacion"></select>
							<div class="agrupado">
								<div class="input-group "><label class="input-group-btn"><a class="btn btn-default" id='ver_hoja_modi' href='#' target='_blank'><span class="fa fa-eye red"></span>ver</a><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="hoja_vida" type="file" style="display: none;" id="hoja_vida"></span></label><input type="text" class="form-control" readonly placeholder='Hoja de vida' id='text_hoja_vida'></div>
							</div>
							<div class="agrupado" id='container_adj_prueba_modi'>
								<div class="input-group "><label class="input-group-btn"><a class="btn btn-default" id='ver_prueba_modi' href='#' target='_blank'><span class="fa fa-eye red"></span>ver</a><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="prueba_psicologia" type="file" style="display: none;" id="prueba_psicologia"></span></label><input type="text" class="form-control" readonly placeholder='Informe Evaluativo' id='text_prueba_psicologia'></div>
							</div>
						</div>
						<textarea class="form-control comentarios" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="	fa fa-wrench"></span> Modificar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<form id="form_buscar_persona" method="post">
	<div class="modal fade" id="modal_buscar_postulante" role="dialog">
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
								<span class="input-group-btn"><button class="btn btn-default" type="button" id="btn_agregar_postulante"><span class='fa fa-user-plus red'></span> Nuevo</button></span>
								<input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del docente">
								<span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
							</div>
						</div>
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_postulantes_busqueda" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="4" class="nombre_tabla">TABLA POSTULANTES</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td>Nombre Completo</td>
										<td>Identificacion</td>
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


<div class="modal fade" id="modal_agregar_postulante" role="dialog">
	<div class="modal-dialog">
		<form id="form_agregar_postulante" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Agregar Postulante</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<select name="id_tipo_identificacion" required class="form-control  cbxtipoIdentificacion"> </select>
						<div class="agro agrupado">
							<div class="input-group">
								<input min="1" type="number" name="identificacion" class="form-control inputt" placeholder="Cedula" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="lugar_expedicion" class="form-control" placeholder="Lugar Expedición" required>
							</div>
						</div>
						<!--
                        <div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Expedición</span>
                                <input type="date" class="form-control sin_margin" required="true" name='fecha_expedicion'>
                            </div>
                        </div>
                        -->
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Nacimiento</span>
								<input type="date" class="form-control sin_margin" required="true" name='fecha_nacimiento'>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="apellido" class="form-control" placeholder="Primer Apellido" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido" required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre" class="form-control" placeholder="Primer Nombre" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_nombre" class="form-control" placeholder="Segundo Nombre">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_modificar_postulante" role="dialog">
	<div class="modal-dialog">
		<form id="form_modificar_postulante" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Modificar Postulante</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<select name="id_tipo_identificacion" required class="form-control  cbxtipoIdentificacion"> </select>
						<div class="agro agrupado">
							<div class="input-group">
								<input min="1" type="number" name="identificacion" class="form-control inputt" placeholder="Cedula" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="lugar_expedicion" class="form-control" placeholder="Lugar Expedición" required>
							</div>
						</div>
						<!--
                        <div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Expedición</span>
                                <input type="date" class="form-control sin_margin" required="true" name='fecha_expedicion'>
                            </div>
                        </div>
                        -->
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Nacimiento</span>
								<input type="date" class="form-control sin_margin" required="true" name='fecha_nacimiento'>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="apellido" class="form-control" placeholder="Primer Apellido" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido" required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre" class="form-control" placeholder="Primer Nombre" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_nombre" class="form-control" placeholder="Segundo Nombre">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"> <span class="fa fa-wrench"></span> Modificar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>


<div class="modal fade" id="modal_detalle_persona" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row" style="width: 80%">
					<div id="datos_perso" class="">
						<table class="table" id=tabla_detalle_persona>
							<tr class="nombre_tabla">
								<td colspan="2">Datos</td>
							</tr>
							<tr>
								<td class="ttitulo">Nombre Completo</td>
								<td class="nombre_completo"></td>
							</tr>
							<tr>
								<td class="ttitulo">Tipo identificación</td>
								<td class="tipo_identificacion"></td>
							</tr>
							<tr>
								<td class="ttitulo">Identificación</td>
								<td class="identificacion"></td>
							</tr>
							<tr>
								<td class="ttitulo">Lugar Expedición</td>
								<td class="lugar_expedicion"></td>
							</tr>
							<tr>
								<td class="ttitulo">Fecha Nacimiento</td>
								<td class="fecha_nacimiento"></td>
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

<div class="modal fade" id="modal_detalle_postulante" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Información Completa</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div id='msj_tipo_cambio_sol' class='oculto'>
					<nav class="navbar navbar-default" id="nav_admin_contratos">
						<div class="container-fluid">
							<ul class="nav navbar-nav">
								<li class="pointer active" id="btn_ver_nuevo"><a><span class="fa fa-folder-open red"></span> Contrato Nuevo</a></li>
								<li class="pointer" id="btn_ver_actual"><a><span class="fa fa-folder red"></span> Contrato Actual</a></li>
								</li>
							</ul>
						</div>
					</nav>
				</div>
				<div id="tabla_detalle_postulante" class="table-responsive">
					<table class="table text-center" id=>
						<tr class="nombre_tabla text-left">
							<td colspan="4">Datos Personales</td>
						</tr>
						<tr>
							<td class="hoja_vida" colspan='2'></td>
							<td class="prueba_psicologia" colspan='2'></td>
						</tr>
						<tr>
							<td class="ttitulo">Nombre Completo</td>
							<td class="nombre_completo" colspan='3'></td>
						</tr>
						<tr>
							<td class="ttitulo">Tipo identificación</td>
							<td class="tipo_identificacion"></td>
							<td class="ttitulo">Identificación</td>
							<td class="identificacion"></td>
						</tr>
						<tr>
							<td class="ttitulo">Fecha Nacimiento</td>
							<td class="fecha_nacimiento"></td>
							<td class="ttitulo">Lugar Expedición</td>
							<td class="lugar_expedicion"></td>
						</tr>
						<tr class="nombre_tabla text-left tr_actuales">
							<td colspan="4">Datos Actuales</td>
						</tr>
						<tr class='tr_actuales'>
							<td class="ttitulo">Dependencia</td>
							<td class="dependencia_actual"></td>
							<td class="ttitulo">Cargo</td>
							<td class="cargo_actual"></td>
						</tr>
						<tr class="nombre_tabla text-left">
							<td colspan="4">Datos Nuevos</td>
						</tr>
						<tr>
							<td class="ttitulo">Tipo</td>
							<td class="tipo" colspan='3'></td>
						</tr>
						<tr class='tr_nuevos'>
							<td class="ttitulo">Procedencia</td>
							<td class="procedencia"></td>
							<td class="ttitulo">Formación</td>
							<td class="formacion"></td>
						</tr>
						<tr class='tr_nuevos'>
							<td class="ttitulo">Dependencia</td>
							<td class="dependencia"></td>
						</tr>
						<tr>
							<td class="ttitulo">Programa</td>
							<td class="programa" colspan='3'></td>
						</tr>
						<tr class='tr_nuevos'>
							<td class="ttitulo">Cargo</td>
							<td class="cargo" colspan='3'></td>
						</tr>
						<tr>
							<td class="ttitulo">Plan Trabajo</td>
							<td class="" colspan='3'><span class='plan_trabajo'></span></td>
						</tr>
						<tr class='tr_motivo'>
							<td class="ttitulo">Motivo</td>
							<td class="motivo" colspan='3'></td>
						</tr>
						<tr>
							<td class="ttitulo">Estado</td>
							<td class="estado" colspan='3'></td>
						</tr>
						<tr class='tr_fechas'>
							<td class="ttitulo">Inicio Contrato</td>
							<td class="fecha_inicio_contrato"></td>
							<td class="ttitulo">Fin Contrato</td>
							<td class="fecha_fin_contrado"></td>
						</tr>
						<tr class=''>
							<td class="ttitulo">#Aprobados</td>
							<td class="vb"></td>
							<td class="ttitulo">#Negados</td>
							<td class="vm"></td>
						</tr>
					</table>
					<table class="table text-center">
						<tr class="nombre_tabla text-left">
							<td>Observaciones</td>
						</tr>
						<tr>
							<td class="observaciones"></td>
						</tr>
					</table>
					<table class="table table-bordered table-hover table-condensed" id="tabla_estados_csep" cellspacing="0" width="100%">
						<thead class="ttitulo">
							<tr>
								<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
							</tr>
							<tr class="filaprincipal">
								<td>Nombre</td>
								<td>Fecha</td>
								<td>Usuario</td>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div id='container_comentarios_generales'>
						<div style="width: 100%" class="list-group margin1 text-left" id='panel_comentarios_generales'></div>
						<form action="" id='form_guardar_comentario_general'>
							<div class="input-group col-md-6">
								<input type="text" class="form-control comentarios" placeholder="Nuevo Comentario" name='comentario'>
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Enviar!</button>
								</span>
							</div>
						</form>
					</div>
				</div>

			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_detalle_vacante" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud Requisición</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud</th>
						<th id="botones_detalle" colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='5'>
							<span <?php if ($administra) { ?>class="red btn" onclick="mostrar_info_persona()" <?php } ?>>
								<span class="info_solicitante"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="info_estado" colspan='3'></td>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td class="info_fecha"></td>
					</tr>
					<tr>
						<td class="ttitulo">Tipo de Solicitud:</td>
						<td class="info_t_solicitud" colspan='3'></td>
						<td class="ttitulo">Tipo de Vacante:</td>
						<td class="info_t_vacante"></td>
					</tr>
					<tr>
						<td class="ttitulo">Cargo:</td>
						<td class="info_cargo" colspan='3'></td>
						<td class="ttitulo t_detalle">Horas de Clases:</td>
						<td class="info_horas"></td>
					</tr>
					<tr id="tr_reemplazo" hidden>
						<td class="ttitulo">Reemplazo:</td>
						<td class="info_reemplazo" colspan='5'></td>
					</tr>
				</table>
				<table id="tabla_apertura" class="table table-bordered table-condensed" hidden>
					<tr>
						<th class="nombre_tabla" colspan="6"> Apertura de Convocatoria</th>
					</tr>
					<tr>
						<td class="ttitulo">Pregrado Requerido:</td>
						<td class="info_pregrado" colspan='3'></td>
						<td class="ttitulo">Posgrado Requerido:</td>
						<td class="info_posgrado"></td>
					</tr>
					<tr id="row_hv" hidden>
						<td class="ttitulo">Hoja de Vida:</td>
						<td class="info_hv" colspan='5'></td>
					</tr>
				</table>
				<table id="tabla_investigacion" class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="6"> Datos Investigación</th>
					</tr>
					<tr>
						<td class="ttitulo">Linea de Investigación:</td>
						<td class="info_investigacion" colspan='3'></td>
						<td class="ttitulo">Experiencia:</td>
						<td class="info_experiencia"></td>
					</tr>
				</table>
			</div>

			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div id="btnaprobar" style="margin-right: 5px;"></div>
				<div id="btnimprimir"></div>
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span>
						Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal_materias" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Materias Asignadas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_materias" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<th colspan="3" class="nombre_tabla">TABLA MATERIAS</th>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Materia</td>
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

<div id="modal_materias_mod" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Materias Asignadas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_materias_mod" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<td class="nombre_tabla" colspan="2">TABLA MATERIAS</td>
							<td class="sin-borde border-left-none" style="display: flex;flex-direction: row-reverse;">
								<span id="btn_agregar_materias" class="btn btn-default"><span class="fa fa-plus red"></span> Agregar</span>
							</td>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Materia</td>
							<td class="opciones_tbl_btn">Acción</td>
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

<div id="modal_estados" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Historial Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_historial_estados_requisicion" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Estado</td>
							<td>Fecha</td>
							<td>Usuario</td>
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

<div id="modal_programas" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-building"></span> Programas/Dependencias Asignadas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_programas" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<th colspan="3" class="nombre_tabla">TABLA DEPENDENCIAS</th>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Dependencia</td>
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

<div id="modal_programas_mod" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-building"></span> Programas/Dependencias Asignadas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_programas_mod" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<td class="nombre_tabla" colspan="2">TABLA DEPENDENCIAS</td>
							<td class="sin-borde border-left-none" style="display: flex;flex-direction: row-reverse;">
								<span id="btn_agregar_dependencia" class="btn btn-default"><span class="fa fa-plus red"></span> Agregar</span>
							</td>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Dependencia</td>
							<td class="opciones_tbl_btn">Acción</td>
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

<?php if ($administra) { ?>
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
								<tr>
									<td class="correo_perso"></td>
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
							<td>Dependencia</td>
							<td class="opciones_tbl_btn">Gestión</td>
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

<div id="modal_elegir_estado" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_estados" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
						</tr>
						<tr class="filaprincipal">
							<td>Nombre</td>
							<td>Gestión</td>
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

<div class="modal fade" id="modal_buscar_departamento" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Programa/Dependencia</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row" id="" style="width: 100%">
					<div class="form-group agrupado col-md-8 text-left">
						<form id="form_buscar_departamento" method="post">
							<div class="input-group">
								<input name="departamento" class="form-control" placeholder="Ingrese nombre del programa o dependencia">
								<span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
							</div>
						</form>
					</div>
					<div class="table-responsive col-md-12" style="width: 100%">
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_dependencia" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="3" class="nombre_tabla">TABLA DEPENDENCIAS</td>
								</tr>
								<tr class="filaprincipal">
									<td>#</td>
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

<script>
	$(document).ready(function() {
		obtener_vista_llama('<?php echo $vista ?>', '<?php echo $_SESSION["perfil"] ?>', '<?php echo $id ?>', '<?php echo $_SERVER["REQUEST_URI"] ?>', '<?php echo $_SESSION["persona"] ?>');
		<?php if ($administra) { ?>
			listar_programas_persona_tabla('');
			Cargar_parametro_buscado_aux(56, "#id_tipo_permiso", "Seleccione Tipo");
		<?php } ?>
		listar_solicitudes_csep(<?php echo $id ?>);
		inactivityTime();
		activarfile();
		Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
		get_estados_asignados_actividad();
		Cargar_parametro_buscado(52, ".cbxformacion", "Seleccione Formación");
		listar_departamentos();
		listar_programas();
		Cargar_parametro_buscado(53, ".cbxcategoria", "Seleccione Categoria");
		Cargar_parametro_buscado_aux(118, ".cbxtipo_vacante", "Seleccione Tipo de Vacante");
		Cargar_parametro_buscado_aux(117, "#cbxtipo_solicitud", "Seleccione Tipo Solicitud");
		listar_tipos_postulantes();
		Cargar_parametro_buscado_aux(119, ".cbxtipo_cargo", "Seleccione Tipo de Cargo");
		mostrar_requisicion();
	});
</script>
