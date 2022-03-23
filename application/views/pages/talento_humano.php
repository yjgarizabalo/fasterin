<?php
$administra = $_SESSION["perfil"] == "Per_Admin" ||  $_SESSION["perfil"] == "Per_Adm_Bec" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true : false;
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/html2canvas.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/th.css">
<div class="container col-md-12 " id="inicio-user">
	<div class="tablausu col-md-12 text-left oculto" id="container_solicitudes">
		<div class="table-responsive">
			<p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr>
					<td colspan="3" class="nombre_tabla">
					TABLA SOLICITUDES<br>
					<span class="mensaje-filtro" hidden>
						<span class="fa fa-bell red"></span>
						La tabla tiene algunos filtros aplicados.
					</span>
				</td>
				<td colspan="3" class="sin-borde text-right border-left-none">
				<?php if ($administra) {?>
				<span class="black-color pointer btn btn-default" id="btnConfiguraciones" >
					<span class="fa fa-cogs red"></span> Administrar
				</span>
				
				<?php }?>
				<span id="btn_csep"></span>
				<span id="btn_filtros" class="black-color pointer btn btn-default" >
					<span class="fa fa-filter red" ></span> Filtrar
				</span>
				<span class="black-color pointer btn btn-default" id="btnReportes" >
					<span class="fa fa-bar-chart red"></span> Reportes
				</span>
					<span id="btn_limpiar" class="black-color pointer btn btn-default" >
						<span class="fa fa-refresh red" ></span> Limpiar
					</span>
				</td>
			</tr>
					<tr class="filaprincipal">
						<td class="opciones_tbl">ver</td>
						<td>Tipo</td>
						<td>Solicitante</td>
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

	<div class="modal fade" id="modal_tipos_requisicion" role="dialog">
		<div class="modal-dialog <?php if ($administra || ($actividades && in_array("Hum_Admi", $actividades, true) && in_array("Hum_Posg", $actividades, true) && in_array("Hum_Apre", $actividades, true) && in_array("Hum_Prec", $actividades, true))) { ?>modal-lg<?php } ?>">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Tipo Requisición</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="opciones__container">
						<?php if ($administra || ($actividades && in_array("Hum_Admi", $actividades, true))) { ?>
							<div id="btn_req_administrativas" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Requisición de Administrativos">
								<img src="<?php echo base_url() ?>/imagenes/req_administrativos.png" alt="..." class="opcion__img">
								<span class="opcion__span">Administrativos</span>
							</div>
						<?php } ?>
						<?php if ($administra || ($actividades && in_array("Hum_Apre", $actividades, true))) { ?>
							<div id="btn_req_aprendices" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Requisición de Aprendices">
								<img src="<?php echo base_url() ?>/imagenes/req_aprendices.png" alt="..." class="opcion__img">
								<span class="opcion__span">Aprendices</span>
							</div>
						<?php } ?>
						<?php //if($administra || ($actividades && (in_array("Hum_Prec", $actividades, true) || in_array("Hum_Admi", $actividades, true)))) { 
						?>
						<?php if ($administra || ($actividades && (in_array("Hum_Prec", $actividades, true)))) { ?>
							<div id="btn_req_pregrado" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Requisición de Pregrado">
								<img src="<?php echo base_url() ?>/imagenes/req_pregrado.png" alt="..." class="opcion__img">
								<span class="opcion__span">PREGRADO</span>
							</div>
						<?php } ?>
						<?php if ($administra || ($actividades && in_array("Hum_Posg", $actividades, true))) { ?>
							<div id="btn_req_posgrado" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Requisición de Posgrado">
								<img src="<?php echo base_url() ?>/imagenes/req_posgrado.png" alt="..." class="opcion__img">
								<span class="opcion__span">POSGRADO</span>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<?php if ($administra || ($actividades && in_array("Hum_Posg", $actividades, true))) { ?>
		<form id="form_req_posgrado" enctype="multipart/form-data">
			<div class="modal fade" id="modal_requisicion_posgrado" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header" id="headermodal">
							<button type="button" class="close" data-dismiss="modal"> X</button>
							<h3 class="modal-title"><span class="fa fa-plus icono_requisicion"></span> <span class="accion_requisicion">Crear</span> Requisición Posgrado</h3>
						</div>
						<div class="modal-body" id="bodymodal">
							<div class="row">
								<select name="tipo_vacante" class="form-control cbxtipo_vacante" style="margin-bottom: 6px;"></select>
								<div id="div_reemplazado_req_pos" class="oculto">
									<div class="input-group" style="margin-bottom: 6px;">
										<input type="text" class="form-control sin_margin sin_focus pointer" name="reemplazado" placeholder="Buscar persona a reemplazar" required readonly>
										<span class="input-group-addon pointer txt_persona_reemplazar" style='background-color:white'>
											<span class='fa fa-search red'></span> Buscar
										</span>
									</div>
								</div>
								<select name="tipo_programa" class="form-control cbxtipo_posgrado" style="margin-bottom: 6px;"></select>
								<select name="req_programa" class="form-control cbxreq_programa" style="margin-bottom: 6px;">
									<option value=''>Seleccione Programa</option>
								</select>
								<div class="input-group" style="margin-bottom: 6px;">
									<input type="text" class="form-control sin_margin sin_focus pointer" name="candidato" placeholder="Buscar Candidato" required readonly>
									<span class="input-group-addon pointer txt_buscar_persona" style='background-color:white'>
										<span class='fa fa-search red'></span> Buscar
									</span>
								</div>
								<select name="id_departamento" class="form-control cbxdepartamento">
									<option value="">Seleccione Departamento</option>
								</select>
								<select name="cargo" class="form-control cbxcargos">
									<option value="">Seleccione Cargo</option>
								</select>
								<input type="text" name="nombre_modulo" class="form-control" placeholder="Nombre de Módulo" required>
								<div class="input-group agrupado">
									<div class="input-group-addon">
										<span class="fa fa-clock-o"></span>
									</div>
									<input type="number" name="horas_modulo" class="form-control" placeholder="Horas Módulo" required>
								</div>
								<input type="number" name="numero_promocion" class="form-control" placeholder="Número Promoción" required>
								<div class="input-group agrupado">
									<div class="input-group-addon">
										<span class="fa fa-dollar"></span>
									</div>
									<input type="number" class="form-control" name="valor_hora" min="0" placeholder="Valor a pagar por hora(Escribir monto sin puntos)" required><br>
								</div>
								<div class="input-group agrupado">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-globe"></span>
									</div>
									<input type="text" class="form-control" name="ciudad_origen" placeholder="Ciudad de Residencia" required>
								</div>
								<div class="agro agrupado">
									<div class="input-group">
										<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
										<input type="date" class="form-control sin_margin" required="true" name='fecha_inicio'>
									</div>
								</div>
								<div class="agro agrupado">
									<div class="input-group">
										<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Terminación</span>
										<input type="date" class="form-control sin_margin" required="true" name='fecha_terminacion'>
									</div>
								</div>
								<!-- Adjunto ejemplo -->
								<div id="campo_hoja_vida" class="input-group agrupado">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span> Buscar
											<input name="hoja_vida" type="file" style="display: none;">
										</span>
									</label>
									<input type="text" id="hoja_vida" class="form-control" readonly placeholder='Adjuntar documentos' required>
								</div>
								<?php if ($estados && $estados[array_search("Hum_Posg", array_column($estados, 'actividad'))]['estado'] === 'Tal_Pro') { ?>
									<select name="tipo_orden" class="form-control cbx_tipo_orden">
										<option value="">Seleccione Tipo de Orden</option>
									</select>
								<?php } ?>
								<textarea name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
							</div>
						</div>
						<div class="modal-footer" id="footermodal">
							<button class="btn btn-danger" type="submit"><span class="fa fa-plus icono_requisicion"></span> <span class="accion_requisicion">Crear</span></button>
							<button class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php } ?>

	<?php if ($administra || ($actividades && (in_array("Hum_Prec", $actividades, true) || in_array("Hum_Admi", $actividades, true) || in_array("Hum_Apre", $actividades, true)))) { ?>
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
									<!-- <select name="tipo_cargo" class="form-control cbxtipo_cargo" required></select> -->
									<select id="cbxtipo_solicitud" name="tipo_solicitud" class="form-control no-revisar"></select>
									<select id="cbxtipo_vacante" name="tipo_vacante" class="form-control cbxtipo_vacante no-revisar"></select>
									<div id="div_persona" class="agro agrupado div_oculto no-revisar" hidden></div>
									<select name="id_programa" class="form-control cbxdependencias">
										<option value="">Seleccione Departamento</option>
									</select>
									<select name="cargo_id" class="form-control cbxcargos">
										<option value="">Seleccione Cargo</option>
									</select>
									<div class="req_adm">
										<!-- <input type="text" class="form-control" placeholder="Nombre cargo" name="nombre_cargo"> -->
										<div class="div_oculto no-revisar div_evaluacion" hidden></div>
										<input type="text" class="form-control" placeholder="Nombre cargo" name="nombre_cargo">
										<input type="text" class="form-control" placeholder="Pregrado Requerido" name="pregrado">
										<input type="text" class="form-control" placeholder="Posgrado Requerido" name="posgrado">
										<textarea name="experiencia" class="form-control" placeholder="Experiencia Laboral"></textarea>
										<textarea name="conocimientos_especificos" class="form-control" placeholder="Conocimientos específicos para el cargo"></textarea>
									</div>
									<textarea name="observaciones" class="form-control req_obs" placeholder="Observaciones"></textarea>
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
									<select id="cbxprograma" name="id_programa" class="form-control cbxdependencias" required></select>
									<select name="cargo_id" class="form-control cbxcargos" required></select>
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
	<?php } ?>

	<div class="modal fade" id="Modal_filtro" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<form id="form_filtro">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<select name="tipo" class="form-control inputt cbxtipo filtro">
								<option value="">Filtrar por Tipo de Solicitud</option>
							</select>
							<select id="estado_filtro" name="estado" class="form-control inputt cbxestado filtro">
								<option value="">Filtrar por Estado</option>
							</select>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="month" name="fecha_i">
							</div>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="month" name="fecha_f">
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



	<div class="modal fade" id="Modal_pruebas" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-folder"></span> Gestionar Pruebas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<h4><strong>Agregar Prueba</strong></h4>
						<form id="form_pruebas" enctype="multipart/form-data" method="post">
							<div class="col-md-10 col-sm-12">
								<div class="agrupado">
									<div class="input-group ">
										<label class="input-group-btn">
											<span class="btn btn-primary">
												<span class="fa fa-folder-open"></span> Buscar
												<input name="archivo" type="file" style="display: none;" id="prueba">
											</span>
										</label>
										<input type="text" class="form-control" name="prueba" readonly placeholder='Prueba'>
									</div>
								</div>
							</div>
							<div class="col-md-2 col-sm-12">
								<div class="agrupado">
									<div class="input-group">
										<button type="submit" class="btn btn-primary form-control"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<table id="tabla_pruebas" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" style="margin-top: 40px;">
						<thead class="ttitulo ">
							<tr>
								<td colspan="4" class="nombre_tabla">TABLA DESCUENTOS</td>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">Ver</td>
								<td>Nombre</td>
								<td>Acción</td>
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


	<div class="tablausu col-md-12 " id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
		<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
		<div id="container-principal2" class="container-principal-alt">
			<h3 class="titulo_menu">
				<span class="fa fa-navicon"></span> MENÚ
			</h3>
			<div class="row" id="menu_th">
				<div id="capa_csep"></div>
				<div id="btn_autogestion">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/certificaciones.png" alt="...">
							<span class="btn  form-control btn-Efecto-men">AUTOGESTIÓN</span>
						</div>
					</div>
				</div>
				<div id="nueva_seleccion">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/seleccion.png" alt="...">
							<span class="btn  form-control btn-Efecto-men">NUEVA SELECCIÓN</span>
						</div>
					</div>
				</div>
				<div id="capa_requisicion"></div>


				<div id="listado_solicitudes">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
							<span class="btn  form-control btn-Efecto-men">Mis Solicitudes</span>
						</div>
					</div>
				</div>
			</div>
			<p class="titulo_menu titulo_menu_alt pointer" id="btn_regresar"><span class="fa fa-reply-all naraja"></span>Regresar</p>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_seleccion" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_seleccion">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-crosshairs"></span> <span class="texto_seleccion">Crear </span> Proceso de Selección</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="col-md-12 input-group" style="margin-bottom: 3px;">
							<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_requisicion" name="" placeholder="Buscar Requisición" readonly>
							<span class="input-group-addon pointer input_buscar_requisicion" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
						<input type="text" name="nombre_vacante" class="form-control" placeholder="Nombre de la Vacante" required>
						<input type="number" min="1" name="numero_vacantes" class="form-control" placeholder="Número de Vacantes" required>
						<div class="agro agrupado">
							<div class="input-group adicional_info" style="padding-top: 6px;">
								<span class="input-group-addon pointer" style='background-color:white'>Tipo Cargo</span>
								<select name="tipo_cargo" class="form-control cbxtipo_cargo" required></select>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group adicional_info" style="padding-top: 6px;">
								<input type="text" name="dependencia" class="form-control sin_margin sin_focus pointer" placeholder="Seleccione Dependencia" required>
								<span class="input-group-addon pointer" id='btn_add_dependencia' style='background-color:white'><span class='fa fa-search red'></span> Dependencia</span>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group adicional_info" style="padding-top: 6px;">
								<span class="input-group-addon pointer" style='background-color:white'>Cargo</span>

								<select name="cargo" class="form-control cbxcargos" required>
									<option>Seleccione Cargo</option>
								</select>
							</div>
						</div>
						<textarea name="perfil" cols="1" rows="3" placeholder="Perfil de la vacante" class="form-control" required></textarea>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre_responsable" class="form-control sin_margin sin_focus pointer" placeholder="Seleccione Responsable TH">
								<span class="input-group-addon pointer" id='btn_buscar_responsable' style='background-color:white'><span class='fa fa-search red'></span> Responsable TH</span>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input id="responsable_entrevista" type="text" name="nombre_jefe_responsable" class="form-control sin_margin sin_focus pointer" placeholder="Seleccione Jefe Inmediato" required>
								<span class="input-group-addon pointer" id='btn_buscar_jefe' style='background-color:white'><span class='fa fa-search red'></span> Jefe Inmediato</span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> <span class="texto_seleccion">Crear</span></button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_requisicion" role="dialog">
	<div class="modal-dialog modal-lg modal-80">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-crosshairs"></span> Solicitudes de Requisición</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row" style="margin: 0px;width: 100%">
					<div class="table-responsive col-md-12">
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_requisiciones" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="6" class="nombre_tabla">TABLA SOLICITUDES REQUISICIÓN</td>
								</tr>
								<tr class="filaprincipal">
									<td>#</td>
									<td>Solicitante</td>
									<td>Cargo</td>
									<td>Departamento</td>
									<td>Tipo</td>
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

<form id="frmaprobar_prestamo">
	<div class="modal fade" id="modal_detalle_solicitud_prestamo" role="dialog">
		<div class="modal-dialog modal-lg modal-80">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div id='container_tabla_postulantes' class="table-responsive">
						<table class="table table-bordered table-condensed">
							<tr>
								<th class="nombre_tabla" colspan="<?php if ($administra) { ?>2<?php } else { ?> 4<?php } ?>"> Informacion del Prestamo</th>
								<th class="sin-borde text-right border-left-none" colspan='4'>
									<?php if ($administra) { ?>
										<span id="btnhistorial" class="btn btn-default"><span class="red fa fa-calendar"></span> Estados</span>
									<?php } ?>
									<a id="btn_adjuntos" class="btn btn-default"> <span class="red fa fa-folder-open-o"></span> Ver Adjuntos</a>
								</th>
							</tr>
							<tr>
								<td class="ttitulo">Solicitante: </td>
								<td colspan='3'>
									<span <?php if ($administra || (isset($estados) && !empty($estados))) { ?>class="red btn" onclick="mostrar_info_persona()" <?php } ?>>
										<span class="info_solicitante"></span></span>
								</td>
								<td class="ttitulo">Fecha de Solicitud:</td>
								<td class="info_fecha"></td>
							</tr>
							<tr>
								<td class="ttitulo">Estado:</td>
								<td class="info_estado"></td>
								<td class="ttitulo">Tipo Prestamo:</td>
								<td class="info_tipo"></td>
								<td class="ttitulo">Cupo Disponible:</td>
								<td class="info_cupo_disponible"></td>
							</tr>
							<tr>
								<td class="ttitulo">Valor Solicitado:</td>
								<td class="info_valor"></td>
								<td class="ttitulo">Cuotas Solicitadas:</td>
								<td class="info_cuotas"></td>
								<td class="ttitulo">Valor Cuota:</td>
								<td class="info_cuota"></td>
							</tr>
							<tr class="aprobado oculto">
								<td class="ttitulo">Valor Aprobado:</td>
								<td class="info_val_aprobado" colspan="2"></td>
								<td class="ttitulo">Cuotas Aprobadas:</td>
								<td class="info_cuotas_aprobadas" colspan="2"></td>
							</tr>
							<tr id="modificar_prestamo"></tr>
							<tr class="revisado oculto">
								<td class="ttitulo">Salario:</td>
								<td class="info_salario"></td>
								<td class="ttitulo">Saldo Pendiente:</td>
								<td class="info_saldo"></td>
								<td class="ttitulo">Capacidad de Pago:</td>
								<td class="info_cupo"></td>
							</tr>
							<tr>
								<td class="ttitulo">Motivo de Prestamo:</td>
								<td colspan='5' class="info_motivo"></td>
							</tr>
							<tr class="comentario oculto">
								<td class="ttitulo">Motivo Desaprobado:</td>
								<td colspan='5' class="info_comentario"></td>
							</tr>
						</table>
					</div>
					<div id='tabla_detalle_prestamo' class="table-responsive">
						<table id="tabla_descuentos_detalle" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="4" class="nombre_tabla">TABLA DESCUENTOS</td>
								</tr>
								<tr class="filaprincipal">
									<td class="opciones_tbl">No.</td>
									<td>Tipo de Descuento</td>
									<td>Concepto</td>
									<td>Valor Descuento</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div id="btnaprobar" style="margin-right: 5px;"></div>
					<div id="btnimprimir"></div>
					<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
				</div>
			</div>
		</div>
	</div>
</form>

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
					<tr id="tr_departamento">
						<td class="ttitulo">Departamento: </td>
						<td colspan='5' class="info_departamento"></td>
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
						<td class="ttitulo t_detalle det_req_aca">Horas de Clases:</td>
						<td class="info_horas det_req_aca"></td>
						<td class="ttitulo det_req">Departamento:</td>
						<td class="info_departamento det_req"></td>
					</tr>
					<tr id="tr_reemplazo" hidden>
						<td class="ttitulo">Reemplazo:</td>
						<td class="info_reemplazo" colspan='5'></td>
					</tr>
					<tr class="tabla_experiencia">
						<td class="ttitulo">Experiencia Laboral:</td>
						<td class="info_experiencia" colspan='5'></td>
					</tr>
					<tr class="tabla_experiencia">
						<td class="ttitulo">Conocimientos Específicos:</td>
						<td class="info_conocimientos" colspan='5'></td>
					</tr>
					<tr class="tr_observaciones">
						<td class="ttitulo">Observaciones:</td>
						<td class="info_observaciones" colspan='5'></td>
					</tr>
					<tr class="tr_vb_pedagogico">
						<td class="ttitulo">Visto Bueno Pedagógico:</td>
						<td class="info_vb_pedagogico" colspan='5'></td>
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
					<tr id="row_adm" class="oculto">
						<td class="ttitulo">Tipo Contrato:</td>
						<td class="info_tipo_contrato" colspan='3'></td>
						<td class="ttitulo">Duración Contrato:</td>
						<td class="info_duracion_contrato"></td>
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

<div class="modal fade" id="modal_detalle_cambio_eps" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud Cambio Eps</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud </th>
						<th id="botones_detalle" colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td colspan='1'>
							<a target="blank_" class="btn btn-default float-right" id="mostrar_documentos">
								<span class="fa fa-tasks red"></span> Documentos
							</a>
						</td>
					</tr>
					<tr>
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
						<td class="ttitulo">Correo:</td>
						<td class="info_correo"></td>
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de Cambio de EPS</th>
						<th colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Eps Actual:</td>
						<td class="info_eps_actual" colspan='5'></td>
					</tr>
					<tr>
						<td class="ttitulo">Eps Destino:</td>
						<td class="info_eps_destino" colspan='5'></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_detalle_ecargo" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud Entrega de Cargo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud </th>
						<th id="botones_detalle" colspan="4" class="sin-borde text-right border-left-none"></th>
					<tr>
					<td colspan="5"></td>
						<td colspan='1'>
							<span class="btn btn-default" id="btnestados_ecargo">
								<span class="fa fa-tasks red"></span> Estados
							</span>
							<a target="blank_" class="btn btn-default float-right" id="mostrar_documentos_ecargo">
								<span class="fa fa-file-text red"></span> Documentos
							</a>
							<a  class="btn btn-default float-right" id="imprimir_ecargo">
								<span class="fa fa-print red"></span> Imprimir
							</a>
						</td>
					</tr>
					</tr>
					<tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='5'>
							<span class="red btn" onclick="mostrar_info_persona()" >
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
						<td class="ttitulo">Correo:</td>
						<td class="info_correo"></td>
					<tr>
						<th class="nombre_tabla" colspan="2"> Información Entrega de Cargo</th>
						<th colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
					<td class="ttitulo">Motivo Entrega de Cargo:</td>
						<td class="info_motivo" colspan='6'></td>
					</tr>
					<tr>
						<td class="ttitulo">Jefe Inmediato:</td>
						<td class="info_jefe_e" colspan='3'></td>
						<td class="ttitulo">Jefe Inmediato 2:</td>
						<td class="info_jefe_e1" colspan='3'></td>
					</tr>
					<tr>
						<td class="ttitulo">Colaborador</td>
						<td colspan='6'>
								<input type="hidden" id="colaborador_id">
								<span class="red btn" onclick='mostrar_info_persona($("#colaborador_id").val())' >
									<span class="info_colaborador"></span>
							</td>
					</tr>
					<tr>
						<th class="nombre_tabla" colspan="2"> Información Entrega de Puesto</th>
						<th colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
					<td class="ttitulo">Responsabilidades:</td>
						<td class="info_responsabilidades" colspan='6'></td>
					</tr>
					<tr>
						<td class="ttitulo">Accesos:</td>
						<td class="info_accesos" colspan='3'></td>
						<td class="ttitulo">Informes:</td>
						<td class="info_informes" colspan='3'></td>
					</tr>
					<tr>
						<td class="ttitulo">Comites:</td>
						<td class="info_comites" colspan='3'></td>
						<td class="ttitulo">Logros:</td>
						<td class="info_logros" colspan='3'></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_detalle_inc_ben" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Inclusión Beneficiario</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud </th>
						<th id="botones_detalle" colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td colspan='1'>
							<a target="blank_" class="btn btn-default float-right" id="mostrar_documentos_inc">
								<span class="fa fa-tasks red"></span> Documentos
							</a>
						</td>
					</tr>
					<tr>
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
						<td class="ttitulo">Correo:</td>
						<td class="info_correo"></td>
					<tr>
						<th class="nombre_tabla" colspan="2"> Información Beneficario</th>
						<th colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Tipo de beneficiario:</td>
						<td class="info_t_beneficiario" colspan='5'></td>
					<tr>
					<tr>
						<td class="ttitulo">Tipo de Documento:</td>
						<td class="info_t_documento" colspan='3'></td>
						<td class="ttitulo">Documento:</td>
						<td class="info_identificacion"></td>
					<tr>
					<tr>
						<td class="ttitulo">Dirección:</td>
						<td class="info_direccion"></td>
						<td class="ttitulo">Barrio:</td>
						<td class="info_barrio"></td>
						<td class="ttitulo">Ciudad:</td>
						<td class="info_ciudad"></td>
					<tr>
					<tr>
						<td class="ttitulo">Telefono:</td>
						<td class="info_telefono" colspan='3'></td>
						<td class="ttitulo">Correo:</td>
						<td class="info_correo_ben"></td>
					<tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_detalle_traslado_afp" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Notificación Traslados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud </th>
						<th id="botones_detalle" colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td colspan='1'>
							<a target="blank_" class="btn btn-default float-right" id="mostrar_documentos_tras">
								<span class="fa fa-tasks red"></span> Documentos
							</a>
						</td>
					</tr>
					<tr>
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
						<td class="ttitulo">Correo:</td>
						<td class="info_correo"></td>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_detalle_documentos" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Documentos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed pointer" id="documentos_gestion_entidades" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<td colspan="7" class="nombre_tabla">Documentos</td>
						</tr>
						<tr>
							<td>#</td>
							<td>Nombre</td>
							<td>ver</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_detalle_vacante_posgrado" role="dialog">
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
						<th colspan="4" class="sin-borde text-right border-left-none">
							<span class="btn btn-default" id="btnestados_req_posgrado">
								<span class="fa fa-tasks red"></span> Estados
							</span>
							<span id="boton_documentos"></span>
						</th>
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
					<tr id="tr_reemplazo_pos" hidden>
						<td class="ttitulo">Reemplazo:</td>
						<td class="info_reemplazo" colspan='5'></td>
					</tr>
				</table>
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Requisición</th>
						<th colspan="4" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Candidato:</td>
						<td class="nombre_candidato" colspan='5'></td>
					</tr>
					<tr id="tr_tipo_orden">
						<td class="ttitulo">Tipo de Orden:</td>
						<td class="tipo_orden" colspan='3'></td>
						<td class="ttitulo">Orden SAP:</td>
						<td class="info_orden_sap"></td>
					</tr>
					<tr>
						<td class="ttitulo">Departamento:</td>
						<td class="nombre_departamento" colspan="3"></td>
						<td class="ttitulo">Tipo de Programa:</td>
						<td class="tipo_programa"></td>
					</tr>
					<tr>
						<td class="ttitulo">Programa:</td>
						<td class="nombre_programa" colspan="5"></td>
					</tr>
					<tr>
						<td class="ttitulo">Nombre módulo:</td>
						<td class="nombre_modulo" colspan="3"></td>
						<td class="ttitulo">Horas módulo:</td>
						<td class="horas_modulo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Número de Promoción:</td>
						<td class="numero_promocion" colspan='3'></td>
						<td class="ttitulo">Valor a pagar por hora:</td>
						<td class="valor_hora"></td>
					</tr>
					<tr>
						<td class="ttitulo">Dedicación/Cargo:</td>
						<td class="dedicacion" colspan='3'></td>
						<td class="ttitulo">Ciudad de origen:</td>
						<td class="ciudad_origen"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha inicio:</td>
						<td class="fecha_inicio" colspan='3'></td>
						<td class="ttitulo">Fecha terminación:</td>
						<td class="fecha_terminacion"></td>
					</tr>
					<tr id="tr_ordensap">
						<td class="ttitulo">Tipo Orden:</td>
						<td class="tipOrden" colspan='3'></td>
						<td class="ttitulo">Código SAP:</td>
						<td class="codigoSap"></td>
					</tr>
					<tr>
						<td class="ttitulo">Observaciones:</td>
						<td class="observaciones" colspan='5'></td>
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

<div class="modal fade" id="modal_detalle_seleccion" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud Selección</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información de la Solicitud</th>
						<th colspan="2" class="sin-borde text-right border-left-none">
							<span id="btn_show_candidatos" class="btn btn-default"><span class="fa fa-users red"></span> <strong>Candidatos</strong></span>
						</th>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='3'>
							<span <?php if ($administra) { ?>class="red btn" onclick="mostrar_info_persona()" <?php } ?>>
								<span class="info_solicitante"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="info_estado"></td>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td class="info_fecha"></td>
					</tr>
					<tr>
						<td class="ttitulo">Nombre de Vacante:</td>
						<td class="info_nombre_vacante"></td>
						<td class="ttitulo">Cantidad de Vacantes:</td>
						<td class="info_cantidad_vacante"></td>
					</tr>
					<tr>
						<td class="ttitulo">Dependencia:</td>
						<td class="info_dependencia"></td>
						<td class="ttitulo">Cargo:</td>
						<td class="info_cargo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Tipo de Cargo:</td>
						<td class="info_tipo_cargo"></td>
						<td class="ttitulo">Responsable:</td>
						<td class="info_responsable"></td>
					</tr>
					<tr>
						<td class="ttitulo">Jefe Inmediato:</td>
						<td class="info_jefe_responsable" colspan='3'></td>
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

<div class="modal fade" id="modal_historial_prestamo" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Historial de Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div id='tabla_historial' class="revisado">
					<table id="tabla_historial_estados" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="4" class="nombre_tabla">TABLA ESTADOS</td>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">No.</td>
								<td>Estado</td>
								<td>Fecha</td>
								<td>Responsable</td>
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
							<select name="id_cargo" required class="form-control cbxcargo">
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
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
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

<form id="form_candidato" method="post">
	<div class="modal fade" id="modal_candidato" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-user"></span> Agregar Candidato</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="id_tipo_identificacion" required class="form-control  cbxtipoIdentificacion"> </select>
						<div class="agro agrupado">
							<div class="input-group">
								<input min="1" type="number" name="identificacion" class="form-control inputt" placeholder="Cedula" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="lugar_expedicion" class="form-control" placeholder="Lugar Expedición" required>
							</div>
						</div>
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
								<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido">
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
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>



<div class="modal fade" id="modal_autogestion" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Autogestión</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<div id="agregar_prestamo" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Prestamo" data-content="Realiza tu solicitud de prestamo de manera facil y rápida.">
						<img src="<?php echo base_url() ?>/imagenes/trasladopresupuesto.png" alt="..." class="opcion__img">
						<span class="opcion__span">SOLICITAR PRESTAMO</span>
					</div>
					<div id="btn_certificado_personalizado" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Certificados" data-content="Solicita tus certificados con un click.">
						<img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="..." class="opcion__img">
						<span class="opcion__span">SOLICITAR CERTIFICADO</span>
					</div>

					<div id="btn_arl" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="ARL" data-content="Realiza tus solicitudes de ARL con un click.">
						<img src="<?php echo base_url() ?>/imagenes/hospital.png" alt="..." class="opcion__img">
						<span class="opcion__span">ARL</span>
					</div>

					<div id="btn_ausentismos" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Ausentismo" data-content=" con un click.">
						<img src="<?php echo base_url() ?>/imagenes/cronogramas.png" alt="..." class="opcion__img">
						<span class="opcion__span">Licencias y vacaciones</span>
					</div>

					<div id="btn_gestionP" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Gestión de Entidades" data-content="Gestión de solicitudes de EPS, Caja de compensación y Pensiones y Cesantías">
						<img src="<?php echo base_url() ?>/imagenes/instituciones.png" alt="..." class="opcion__img">
						<span class="opcion__span">Gestión de Entidades</span>
					</div>

					<div id="btn_Ecargo" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Entrea de Cargo" data-content="Gestión de entrega de cargo">
						<img src="<?php echo base_url() ?>/imagenes/Entrega_cargo.png" alt="..." lass="opcion__img">
						<span class="opcion__span">ENTREGA DE CARGO</span>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_tipo_ausentismo" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-id-card-o"></span> Control ausentismo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<div id="btn_vacaciones" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Vacaciones" data-content="Formulario de vacaciones">
						<img src="<?php echo base_url() ?>/imagenes/viajes.png" alt="..." class="opcion__img">
						<span class="opcion__span">VACACIONES</span>
					</div>
					<div id="btn_licencia" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Licencia remunerada" data-content="Formulario de lincencias.">
						<img src="<?php echo base_url() ?>/imagenes/sublineas.png" alt="..." class="opcion__img">
						<span class="opcion__span">LICENCIAS</span>
					</div>
					<!--
					<div id="btn_licencia_no_remunerada"
					class="opcion__cont"
					data-toggle="popover"
					data-trigger="hover"
					ata-placement="bottom"
					title="Licencia no remunerada"
					data-content="Formulario licencia no remunerada">
						<img src="<?php echo base_url() ?>/imagenes/test.png" alt="Certificado de ingresos y retencion" class="opcion__img">
						<span class="opcion__span">LICENCIA NO REMUNERADA</span>
					</div> -->
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_ausentismo_vacaciones" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_ausentismo_vacaciones">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Solicitar vacaciones</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">

						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
								<input type="date" class="form-control sin_margin" required="true" name='fecha_inicio'>
							</div>
							<div class="alert alert-warning alert-dismissible" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<strong> A tener en cuenta !</strong> si la fecha a seleccionar es menor a 15 dias, su solicitud sera recibida pero estara sujeta a aprobacion.
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Dias solicitados</span>
								<input type="number" class="form-control sin_margin" required="true" name='dias_solicitados' min="1" max="30">

							</div>

						</div>
						<div class="col-md-12 input-group margin1">
							<span class="input-group-addon" style=' background-color:white'><span class='fa fa-user-circle red'></span> Asignar Jefe directo</span>
							<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_jefe" name="jefe_inmediato" placeholder="Buscar Persona" required readonly>
							<span class="input-group-addon pointer input_buscar_jefe" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones_ausentismo"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-filter"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal_ausentismo_licencia" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_ausentismo_licencia">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Solicitar licencia</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agro agrupado">
							<select name="tipo_licencia" class="form-control sin_margin cbxtipo_licencia">
								<option value="">seleccione el tipo de licencia</option>
							</select>
						</div>
					</div>
					<div class="col-md-12 input-group" style="margin-top: 10px;">
						<span class="input-group-addon" style=' background-color:white'><span class='fa fa-user-circle red'></span> Asignar Jefe directo</span>
						<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_jefe_licencia" name="jefe_inmediato" placeholder="Buscar Persona" required readonly>
						<span class="input-group-addon pointer input_buscar_jefe_licencia" style='background-color:white'>
							<span class='fa fa-search red'></span> Buscar
						</span>
					</div>
					<div class="agro agrupado">
						<div class="input-group">
							<span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
							<input type="date" class="form-control sin_margin" required="true" name='fecha_inicio'>
						</div>
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong> A tener en cuenta !</strong> si la fecha a seleccionar es menor a 15 dias, su solicitud sera recibida pero estara sujeta a aprobacion.
						</div>
					</div>
					<div class="agro agrupado">
						<div class="input-group">
							<span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Dias solicitados</span>
							<input type="number" class="form-control sin_margin" required="true" name='dias_solicitados' min="1" max="15">

						</div>
					</div>
					<div class="agro agrupado">
						<div class="input-group">
							<span class="input-group-addon" style=' background-color:white'><span class='fa fa-clock red'></span> Motivo de licencia</span>
							<textarea class="form-control sin_margin" cols="1" rows="3" placeholder="Ingrese por favor la descripcion de su solicitud" name="motivo_licencia"></textarea>
						</div>
					</div>

					<div class="agrupado">
						<div class="input-group ">
							<label class="input-group-btn">
								<span class="btn btn-primary">
									<span class="fa fa-folder-open"></span>Adjuntar Archivo <input name="archivo_adjunto" type="file" style="display: none;" id="archivo_adjunto"></span></label><input type="text" class="form-control" readonly placeholder='Soporte'>
						</div>
					</div>
					<textarea class="form-control sin_margin" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>




				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active"><span class="fa fa-filter"></span> Guardar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
	</div>
	</form>
</div>
</div>

<div class="modal fade" id="modal_nuevo_prestamo" role="dialog">
	<div class="modal-dialog">
		<form id="form_nuevo_prestamo" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-money"></span> Nuevo Prestamo</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<select id="cbxtipo_prestamo" name="tipo_prestamo" class="form-control"></select>
						<div id="volante_matricula" hidden class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar <input name="volante" type="file" style="display: none;" id="volante">
									</span>
								</label>
								<input type="text" class="form-control" readonly placeholder='Volante de Matrícula'>
							</div>
						</div>
						<input id="txtvalor_prestamo" type="number" step='1' min='1' class="form-control" placeholder="Valor" name="valor" required>
						<input type="number" step='1' min='1' max='10' class="form-control" placeholder="Numero Cuotas" name="cuotas" required>
						<textarea class="form-control" cols="1" rows="3" placeholder="Motivo" name="motivo" required></textarea>
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

<div class="modal fade" id="modal_arl" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> ARL</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<?php if ($administra || ($actividades && in_array("Hum_Afi_Arl", $actividades, true))) { ?>
						<div id="btn_afiliacion" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Afiliaciones" data-content="Realiza solicitud de Afiliación Estudiantes en Practica.">
							<img src="<?php echo base_url() ?>/imagenes/seguridad_trabajo.png" alt="..." class="opcion__img">
							<span class="opcion__span">SOLICITAR AFILICIACIÓN</span>
						</div>
					<?php } ?>
					<div id="btn_cobertura" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Coberturas" data-content="Reliza solicitud de Cobertura ARL.">
						<img src="<?php echo base_url() ?>/imagenes/comitedepresupuesto.png" alt="..." class="opcion__img">
						<span class="opcion__span">SOLICITAR COBERTURA</span>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_gestionP" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Gestion Entidades</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<div id="btn_avisoEps" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Cambio" data-content="Reliza solicitud de cambio de EPS.">
						<img src="<?php echo base_url() ?>/imagenes/hospital.png" alt="..." class="opcion__img">
						<span class="opcion__span">SOLICITAR CAMBIO DE EPS</span>
					</div>
					<div id="btn_inc_ben" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Inclusion" data-content="Reliza solicitud de inclusión de beneficiario de EPS y Caja de compensación.">
						<img src="<?php echo base_url() ?>/imagenes/Cargos_departamentos.png" alt="..." class="opcion__img">
						<span class="opcion__span">INCLUSIÓN DE BENEFCIARIOS</span>
					</div>
					<div id="btn_traslado" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Traslado" data-content="Realiza la notificación de tu cambio de  EPS, Fondo de Pensión y/o cesantía">
						<img src="<?php echo base_url() ?>/imagenes/certificaciones.png" alt="..." class="opcion__img">
						<span class="opcion__span">NOTIFICACIÓN DE TRASLADO</span>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_avisoEps" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Aviso Importante</h3>
			</div>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<h4 align="center"> <strong> A tener en cuenta ! </strong> </h4>
				<br>
				<p align="justify">El gobierno nacional ha dispuesto un portal web para la gestión de novedades de seguridad social, por medio del cual los ciudadanos pueden tramitar la novedad de traslado de EPS sin necesidad de intermediarios. <br> Para acceder al portal web, de clic en el siguiente enlace: <a href=" https://miseguridadsocial.gov.co/" target="_blank">Mi Seguridad Social</a>
					<br>
					<dd>Antes de solicitar su traslado, tenga en cuenta lo siguiente:</dd>
					<br> 1- Debe tener mínimo un año continuo en la EPS actual.
					<br> 2- Su grupo familiar actual también será trasladado a la EPS destino.
					<br>
					<dd> 3- El tiempo de respuesta del traslado es hasta de 30 días calendario y dependerá de la entidad a la que usted desea trasladarse.</dd>
					<br> Una vez realice su solicitud y reciba respuesta de aprobación del traslado, deberá enviar la respuesta de dicha aprobación al correo: <a href="mailto:lgomez28@cuc.edu.co">lgomez28@cuc.edu.co </a> y <a href="mailto:aorozco54@cuc.edu.co">aorozco54@cuc.edu.co </a>.
					<br> Si no le fue posible realizar su solicitud por medio del portal web, por favor de clic en CONTINUAR para recibir acompañamiento por parte de un funcionario de Talento Humano.
				</p>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-danger active" data-dismiss="modal" id='btn_cambio_eps'> <span class="fa fa-check"></span> Continuar</button></div>
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_nuevo_arl" role="dialog">
	<div class="modal-dialog">
		<form id="form_nuevo_arl" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_afil_arl"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">

					<div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
                                <input type="date" class="form-control sin_margin" required="true" name='fecha_inicio'>
                            </div>
								<div class="alert alert-warning alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<strong> A tener en cuenta !</strong> si la fecha a seleccionar es menor a 15 dias, su solicitud sera recibida pero estara sujeta a aprobacion.
								</div>
                	</div>
                        <div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Dias solicitados</span>
  								<input type="number" class="form-control sin_margin" required="true" name='dias_solicitados' min="1" max="30">

                            </div>

                        </div>
						<div class="col-md-12 input-group margin1" >
							<span class="input-group-addon" style=' background-color:white'><span class='fa fa-user-circle red'></span> Asignar Jefe directo</span>
							<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_jefe" name="jefe_inmediato" placeholder="Buscar Persona" required readonly>
							<span class="input-group-addon pointer input_buscar_jefe" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha de Nacimiento</span>
								<input name='fecha_nacimiento' class="form-control sin_margin" type='date' required>
							</div>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<select name="id_genero" class="form-control cbxgenero" required>
								<option value="">Seleccione Género</option>
							</select>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<input type="text" name="eps" class="form-control" placeholder="Eps" required>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<input type="text" name="empresa" class="form-control" placeholder="Empresa" required>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<input type="text" name="ciudad" class="form-control" placeholder="Ciudad" required>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<select name="id_nriesgo" class="form-control cbxnriesgo" required>
								<option value="">Seleccione Nivel Riesgo</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio Labor</span>
								<input name='fecha_inicio_lab' class="form-control sin_margin" type='date' required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Finalización</span>
								<input name='fecha_fin_lab' class="form-control sin_margin" type='date' required>
							</div>
						</div>
						<textarea class="form-control oculto" cols="1" rows="3" placeholder="Observaciones" name="motivo" maxlength="199"></textarea>
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

<div class="modal fade" id="modal_cobertura_arl" role="dialog">
	<div class="modal-dialog">
		<form id="form_cobertura_arl" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_cob_arl"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="col-md-12 input-group" style="margin-bottom: 3px;">
							<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_persona" name="candidato" placeholder="Buscar Persona" required readonly>
							<span class="input-group-addon pointer input_buscar_persona" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha de Nacimiento</span>
								<input name='fecha_nacimiento' class="form-control sin_margin" type='date' required>
							</div>
						</div>
						<div class="col-md-12" style="padding: 0 0;">
							<select name="id_genero" class="form-control cbxgenero" required>
								<option value="">Seleccione Género</option>
							</select>
						</div>
						<div class="col-md-12 info_persona_arl" style="padding: 0 0;">
							<input type="text" name="eps" class="form-control" placeholder="Eps" required>
						</div>
					</div>
					<div class="row">
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha de Viaje</span>
								<input name='fecha_viaje' class="form-control sin_margin" type='date' required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Regreso</span>
								<input name='fecha_regreso' class="form-control sin_margin" type='date' required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0 0;">
							<select name="id_cobertura" class="form-control cbxcobertura" required>
								<option value="">Seleccione Cobertura</option>
								<option value="1">Nacional</option>
								<option value="2">Internacional</option>
							</select>
						</div>
					</div>
					<div class="row">
						<textarea class="form-control" cols="1" rows="3" placeholder="Actividad a Realizar" name="actividad" maxlength="199" required></textarea>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0 0;">
							<input type="text" name="empresa" class="form-control" placeholder="Empresa" required>
						</div>
					</div>
					<div class="row">
						<div id="destinos" class="text-right">
							<input type="text" list="lugares" class="form-control CampoGeneral requerido" name="destino" placeholder="Ciudad - País" required>
							<datalist id="lugares">
							</datalist>
						</div>
					</div>
					<div class="row">
						<input type="text" name="idioma" class="form-control" placeholder="Idioma">
						<textarea class="form-control oculto" cols="1" rows="3" placeholder="Observaciones" name="motivo" maxlength="199"></textarea>
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

<div class="modal fade" id="modal_cambio_eps" role="dialog">
	<div class="modal-dialog">
		<form id="form_cambio_eps" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_cam_eps"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="direccion" class="form-control" placeholder="Dirección de residencia" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="barrio" class="form-control" placeholder="Barrio" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" list="lugares" class="form-control CampoGeneral requerido" name="ciudad" placeholder="Ciudad - País" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="number" name="telefono" class="form-control" placeholder="Teléfono o celular de contacto" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="eps_actual" class="form-control" placeholder="EPS Actual" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="eps_destino" class="form-control" placeholder="EPS destino" required>
						</div>
					</div>
					<div class="row">
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong> Nota: </strong> Por favor adjuntar el documento en formato PDF legible, a fin de evitar negaciones en su solicitud.
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<div id="campo_documentos_eps" class="input-group agrupado">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="documentos_eps" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" id="documentos_eps" class="form-control" readonly placeholder='Adjuntar Documento de Identidad' required>
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

<div class="modal fade" id="modal_inc_ben" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Inclusión de Beneficiarios</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<div id="btn_incavisoEps" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Inclusión beneficiarios" data-content="Solicita inclusión de beneficiario(s) en EPS.">
						<img src="<?php echo base_url() ?>/imagenes/hospital.png" alt="..." class="opcion__img">
						<span class="opcion__span">INCLUSIÓN BENEFCIARIOS EPS</span>
					</div>
					<div id="btn_avisocaja" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Inclusión beneficiarios" data-content="Solicita inclusión de beneficiario(s) en tu caja de compesación.">
						<img src="<?php echo base_url() ?>/imagenes/instituciones.png" alt="..." class="opcion__img">
						<span class="opcion__span">INCLUSIÓN BENEFCIARIOS CAJA</span>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_inc_eps" role="dialog">
	<div class="modal-dialog">
		<form id="form_inc_eps" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_inc_eps"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="agro agrupado">
							<select id="cbxtipo_beneficiario" name="tipo_beneficiario" class="form-control sin_margin cbxtipo_beneficiario">
								<option value="">Seleccione el tipo de beneficiario</option>
							</select>
						</div>
					</div>
					<div class="row oculto" id="div_hijom" style="margin-top: 5px;">
						<label>
							<input type="checkbox" name="check_documento">
							Marque ésta opción si su tiene entre 18 años de edad o mas.
						</label>
					</div>
					<div class="row">
						<div class="agro agrupado">
							<select name="tipo_documento" class="form-control sin_margin cbxtipoIdentificacion_entidades">
								<option value="">Seleccione el tipo de documento</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="number" name="documento" class="form-control" placeholder="Numero de Documento" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="direccion" class="form-control" placeholder="Dirección de residencia" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="barrio" class="form-control" placeholder="Barrio" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" list="lugares" class="form-control CampoGeneral requerido" name="ciudad" placeholder="Ciudad - País" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="number" name="telefono" class="form-control" placeholder="Teléfono o celular de contacto" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
						</div>
					</div>
					<div class="row">
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong> Nota: </strong> Por favor adjuntar el documento en formato PDF legible, a fin de evitar negaciones en su solicitud.
							<br>Por favor adjuntar certificado de estudio si su hijo(a) tiene 18 o mas.
						</div>
					</div>
					<div class="row oculto" id="div_hijo">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="registro_civil" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="registro_civil" class="form-control" readonly placeholder='Registro Civil Beneficiario' required>
								</div>
							</div>
						</div>
					</div>
					<div class="row oculto" id="div_padre">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="registro_civilp" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="registro_civilp" class="form-control" readonly placeholder='Registro Civil Cotizante' required>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<div id="campo_documentos_eps" class="input-group agrupado">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="documentos_eps" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" id="documentos_eps" class="form-control" readonly placeholder='Adjuntar Documento de Identidad Beneficiario' required>
							</div>
						</div>
					</div>
					<div class="row oculto" id="div_hestudio">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="certificado_estudio" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="certificado_estudio" class="form-control" readonly placeholder='Certificado de Estudio Beneficiario' required>
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
</div>

<div class="modal fade" id="modal_inc_caj" role="dialog">
	<div class="modal-dialog">
		<form id="form_inc_caj" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_inc_caj"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="agro agrupado">
							<select id="cbxtipo_beneficiario" name="tipo_beneficiario" class="form-control sin_margin cbxtipo_beneficiario">
							</select>
						</div>
					</div>
					<div class="row oculto" id="div_mayor" style="margin-top: 5px;">
						<label>
							<input type="checkbox" name="check_edad">
							Marque ésta opción si su hijo tiene 12 años o más.
						</label>
					</div>
					<div class="row">
						<div class="agro agrupado">
							<select name="tipo_documento" class="form-control sin_margin cbxtipoIdentificacion_entidades">
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="number" name="documento" class="form-control" placeholder="Numero de Documento" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="direccion" class="form-control" placeholder="Dirección de residencia" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" name="barrio" class="form-control" placeholder="Barrio" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="text" list="lugares" class="form-control CampoGeneral requerido" name="ciudad" placeholder="Ciudad - País" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="number" name="telefono" class="form-control" placeholder="Teléfono o celular de contacto" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
						</div>
					</div>
					<div class="row">
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong> Nota: </strong> Por favor adjuntar el documento en formato PDF legible, a fin de evitar negaciones en su solicitud.
							<br>Por favor adjuntar certificado de estudio si su hijo(a) tiene 12 o mas.
						</div>
					</div>
					<div class="row oculto" id="div_pacon" style="margin-bottom: 10px;">
						<a target="blank_" class="btn btn-default float-right" id="mostrar_convivencia">
							<span class="fa fa-download"></span> Declaración Juramentada de Convivencia
						</a>
					</div>
					<div class="row oculto" id="div_con" style="margin-bottom: 10px;">
						<a target="blank_" class="btn btn-default float-right" id="mostrar_dconvivencia">
							<span class="fa fa-download"></span> Declaración Juramentada de Convivencia
						</a>
					</div>
					<div class="row oculto" id="div_padres">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="registro_civilp" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="registro_civilp" class="form-control" readonly placeholder='Registro Civil Cotizante' required>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<div id="campo_documentos_eps" class="input-group agrupado">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="documentos_eps" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" id="documentos_eps" class="form-control" readonly placeholder='Adjuntar Documento de Identidad Beneficiario' required>
							</div>
						</div>
					</div>
					<div class="row oculto" id="div_conpadres">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="convivenciap" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="convivenciap" class="form-control" readonly placeholder='Declaración Juramentada de Convivencia' required>
								</div>
							</div>
						</div>
					</div>
					<div class="row oculto" id="div_conyuge">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="convivencia" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="convivencia" class="form-control" readonly placeholder='Declaración Juramentada de Convivencia' required>
								</div>
							</div>
						</div>
					</div>
					<div class="row oculto" id="div_estudio">
						<div class="col-md-12 " style="padding: 0 0;">
							<div class="agrupado">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<span class="fa fa-folder-open"></span>Buscar
											<input name="certificado_estudio" type="file" style="display: none;" class="evaluacion_input">
										</span>
									</label>
									<input type="text" id="certificado_estudio" class="form-control" readonly placeholder='Certificado de Estudio Beneficiario' required>
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
</div>

<div class="modal fade" id="modal_tras_afp" role="dialog">
	<div class="modal-dialog">
		<form id="form_tras_afp" method="post">
			<!-- Modal content -->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_tras_afp"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="alert alert-warning alert-dismissible" role="alert">
							<strong> Nota: </strong>
							<p align="justify"> Tenga en cuenta que debe adjuntar el radicado que le han dado.
								<br>Para mayor información, puede comunicarse al correo: <a href="mailto:lgomez28@cuc.edu.co">lgomez28@cuc.edu.co </a>.
								<br> Por favor adjuntar el documento en formato PDF legible, a fin de evitar negaciones en su solicitud.
							</p>

						</div>
					</div>
					<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<div id="campo_documentos_traslado" class="input-group agrupado">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="documento_traslado" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" id="documento_traslado" class="form-control" readonly placeholder='Adjuntar Radicado' required>
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
</div>

<div class="modal fade" id="modal_avisoEpsInc" role="dialog">
	<div class="modal-dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Aviso Importante</h3>
				</div>
				<div class="alert alert-warning alert-dismissible" role="alert">
					<h4 align="center"> <strong> A tener en cuenta ! </strong> </h4><br>
					<p align="justify">El gobierno nacional ha dispuesto un portal web para la gestión de novedades de seguridad social, por medio del cual los ciudadanos pueden tramitar la novedad de traslado de EPS sin necesidad de intermediarios.<br> Para acceder al portal web, de clic en el siguiente enlace: <a href=" https://miseguridadsocial.gov.co/" target="_blank">Mi Seguridad Social</a>
						<br>
						<dd>Antes de solicitar su traslado, tenga en cuenta lo siguiente:</dd>
						<br> 1- Debe tener mínimo un año continuo en la EPS actual.
						<br> 2- Su grupo familiar actual también será trasladado a la EPS destino.
						<br>
						<dd> 3- El tiempo de respuesta del traslado es hasta de 30 días calendario y dependerá de la entidad a la que usted desea trasladarse.</dd>
						<br> Una vez realice su solicitud y reciba respuesta de aprobación del traslado, deberá enviar la respuesta de dicha aprobación al correo: <a href="mailto:lgomez28@cuc.edu.co">lgomez28@cuc.edu.co </a> y <a href="mailto:aorozco54@cuc.edu.co">aorozco54@cuc.edu.co </a>.
						<br> Si no le fue posible realizar su solicitud por medio del portal web, por favor de clic en CONTINUAR para recibir acompañamiento por parte de un funcionario de Talento Humano.
					</p>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;"><button type="button" class="btn btn-danger active" data-dismiss="modal" id='btn_inc_eps'> <span class="fa fa-check"></span> Continuar</button></div>
					<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_avisocaja" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Aviso Importante</h3>
			</div>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<h4 align="center"> <strong> A tener en cuenta ! </strong> </h4>
				<dd><br>Por favor tenga en cuenta la constitución del grupo familiar:</dd>
				<br> - Cotizante
				<br> - Cónyuge o compañero(a) permanente.
				<br> - Hijos menores de 25 años que dependan económicamente del cotizante.
				<br> - Hijos de cualquier edad si tienen incapacidad permanente y dependen económicamente del cotizante.
				<br>
				<dd> - Se podrá afiliar a los padres del cotizante que tengan 60 años en adelante, que no estén pensionados, que dependan económicamente del cotizante, y que figuren como beneficiarios del cotizante en la EPS. </dd>
				<br> Se dará respuesta en un máximo de 72 hrs.
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-danger active" data-dismiss="modal" id='btn_inc_caj'> <span class="fa fa-check"></span> Continuar</button></div>
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_traslado" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Aviso Importante</h3>
			</div>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<h4 align="center"> <strong> A tener en cuenta ! </strong> </h4>
				<dd><br>Tenga en cuenta que la solicitud de traslados de Fondo de pensiones y cesantías debe ser realizado por el empleado directamente en la entidad a la que desee trasladarse. Posterior a ello, usted deberá reportar la respuesta de su traslado por este medio.</dd>
				<br> Para mayor información, puede comunicarse al correo: <a href="mailto:lgomez28@cuc.edu.co">lgomez28@cuc.edu.co </a>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;"><button type="button" class="btn btn-danger active" data-dismiss="modal" id='btn_tras'> <span class="fa fa-check"></span> Continuar</button></div>
				<div style="margin-left: 5px;"><button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_detalle_solicitud_arl" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud ARL</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_detalle_arl" class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla"> Información de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='3'>
							<span class="red btn" onclick="mostrar_info_persona()">
								<span class="info_solicitante"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="info_estado"></td>
						<td class="ttitulo">Tipo Solicitud:</td>
						<td class="info_tipo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td colspan='3' class="info_fecha"></td>
					</tr>
					<tr id="observaciones_arl">
						<td class="ttitulo">Motivo rechazo:</td>
						<td colspan="3" class="info_observaciones"></td>
					</tr>
					<tr>
						<th class="nombre_tabla"> Detalle de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Beneficiario: </td>
						<td colspan='3' class="info_beneficiario"></td>
					</tr>
					<tr>
						<td class="ttitulo">Identificación: </td>
						<td colspan='3' class="info_cc_beneficiario"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de Nacimiento: </td>
						<td colspan='3' class="info_fecha_nac_beneficiario"></td>
					</tr>
					<tr>
						<td class="ttitulo">Empresa: </td>
						<td colspan='3' class="info_empresa"></td>
					</tr>
					<tr>
						<td class="ttitulo">Ciudad - País:</td>
						<td colspan='3' class="info_ciudad"></td>
					</tr>
					<tr class="detalle_afiliacion">
						<td class="ttitulo">Nivel de Riesgo:</td>
						<td colspan='3' class="info_nriesgo"></td>
					</tr>
					<tr class="detalle_afiliacion">
						<td class="ttitulo">Fecha Inicio Labor:</td>
						<td class="info_fechaini"></td>
						<td class="ttitulo">Fecha Finalización:</td>
						<td class="info_fechafin"></td>
					</tr>
					<tr class="detalle_cobertura">
						<td class="ttitulo">Idioma:</td>
						<td class="info_idioma"></td>
						<td class="ttitulo">Cobertura:</td>
						<td class="info_cobertura"></td>
					</tr>
					<tr class="detalle_cobertura">
						<td class="ttitulo">Fecha Viaje:</td>
						<td class="info_fviaje"></td>
						<td class="ttitulo">Fecha Regreso:</td>
						<td class="info_fregreso"></td>
					</tr>
					<tr class="detalle_cobertura">
						<td class="ttitulo">Actividad:</td>
						<td colspan='3' class="info_actividad"></td>
					</tr>
					<tr>
						<td class="ttitulo">Observaciones:</td>
						<td colspan='3' class="info_motivo"></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_detalle_solicitud_talento_humano_ausentismo" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud </h3>
			</div>
			<div class="modal-body" id="bodymodal">

				<table id="tabla_detalle_talento_humano_ausentismo" class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla"> Información de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					<tr>
						<!-- trabajando -->
						<th colspan="4" class="sin-borde text-right border-left-none">
							<span class="btn btn-default" id="btnestados_ausentismo_vacaciones">
								<span class="fa fa-tasks red"></span> Estados
							</span>

						</th>
					</tr>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='3'>
							<span class="red btn" onclick="mostrar_info_persona()">
								<span class="info_solicitante"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Jefe Inmediato: </td>
						<td colspan='3'>
							<span class="red btn" onclick="mostrar_info_persona()">
								<span class="jefe_inmediato"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="info_estado"></td>
						<td class="ttitulo">Tipo Solicitud:</td>
						<td class="info_tipo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td colspan='3' class="info_fecha"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de inicio:</td>
						<td colspan='3' class="fecha_inicio"></td>
					</tr>
					<tr>
						<td class="ttitulo">Dias solicitados:</td>
						<td colspan='3' class="dias_solicitados"></td>
					</tr>
					<tr id="observaciones_talento_humano">
						<td class="ttitulo">Motivo rechazo:</td>
						<td colspan="3" class="info_motivo"></td>
					</tr>
					<tr>
						<th class="nombre_tabla"> Detalle de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Observaciones:</td>
						<td colspan='3' class="observaciones"></td>

					</tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_detalle_solicitud_talento_humano_licencia" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud </h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_detalle_talento_humano_licencia" class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla"> Información de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					<tr>
						<!-- trabajando -->
						<th colspan="4" class="sin-borde text-right border-left-none">
							<span class="btn btn-default" id="btnestados_ausentismo_licencia">
								<span class="fa fa-tasks red"></span> Estados
							</span>

						</th>
					</tr>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='3'>
							<span class="red btn" onclick="mostrar_info_persona()">
								<span class="info_solicitante"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Jefe Inmediato: </td>
						<td colspan='3'>
							<span class="red btn" onclick="mostrar_info_persona()">
								<span class="jefe_inmediato"></span>
							</span>
						</td>
					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td colspan='3' class="info_estado"></td>
					</tr>
					<tr>
						<td class="ttitulo">Tipo Solicitud:</td>
						<td colspan='3' class="info_tipo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Tipo Licencia:</td>
						<td class="info_tipo_licencia"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td colspan='3' class="info_fecha"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de inicio:</td>
						<td colspan='3' class="fecha_inicio"></td>
					</tr>
					<tr>
						<td class="ttitulo">Dias solicitados:</td>
						<td colspan='3' class="dias_solicitados"></td>
					</tr>
					<tr>
						<td class="ttitulo">Motivo de la licencia:</td>
						<td colspan='3' class="motivo_licencia"></td>
					</tr>
					<tr>
						<td class="ttitulo">Archivo adjunto:</td>
						<td colspan='3' class="archivo_adjunto"></td>
					</tr>
					<tr id="observaciones_talento_humano">
						<td class="ttitulo">Motivo rechazo:</td>
						<td colspan="3" class="info_motivo"></td>
					</tr>
					<tr>
						<th class="nombre_tabla"> Detalle de la Solicitud</th>
						<th id="botones_detalle" colspan="3" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Observaciones:</td>
						<td colspan='3' class="observaciones"></td>

					</tr>
				</table>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<form id="form_vb_arl" enctype="multipart/form-data" method="post">
	<div class="modal fade" id="modal_vb_arl" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check"></span> Aprobación ARL</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="certificado_arl" type="file" style="display: none;" id="certificado_arl">
									</span>
								</label>
								<input type="text" class="form-control" name="prueba" readonly placeholder='Certificado ARL'>
							</div>
						</div>
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="rd_vb_a" name="vb_arl" value="1" checked>
								<label for="rd_vb_a" title="Aprobar Candidato"> Aprobar</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="rd_vb_d" name="vb_arl" value="0">
								<label for="rd_vb_d" title="Rechazar Candidato"> Desaprobar</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-danger"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>


<!-- Adjuntar documentos -->
<form id="form_entidades" enctype="multipart/form-data" method="post">
	<div class="modal fade" id="modal_entidades" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check"></span> Visto Bueno Entidades Publicas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="certificado_entidades" type="file" style="display: none;" id="certificado_entidades">
									</span>
								</label>
								<input type="text" class="form-control" name="prueba" readonly placeholder='Adjuntar Documento'>
							</div>
						</div>
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="rd_vb_e" name="vb_eps" value="1" checked>
								<label for="rd_vb_e" title="Aprobar Solicitud"> Aprobar</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="rd_vb_ec" name="vb_eps" value="0">
								<label for="rd_vb_ec" title="Rechazar Solicitud"> Desaprobar</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-danger"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_vb_ecargo" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-cogs"></span> Vistos Buenos Entrega de Cargo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
			<table class="table table-bordered table-hover table-condensed pointer" id="tabla_vb_ecargo" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr class="">
						<td colspan="4" class="nombre_tabla">Vistos Buenos</td>
					</tr>
					<tr class="filaprincipal">
						<td>Visto Bueno</td>
					<td class="opciones_tbl_btn">Acción</td>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="modal-footer" id="footermodal">
		<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button></div>
		</div>
	</div>
</div>

<form id="form_buscar_persona_arl" method="post">
	<div class="modal fade" id="modal_buscar_persona_arl" role="dialog">
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
								<span class="input-group-btn agregar_postulante_arl">
									<button class="btn btn-default" type="button" id="btn_agregar_postulante_arl">
										<span class='fa fa-user-plus red'></span> Nuevo
									</button>
								</span>
								<input id='txt_buscar_persona_arl' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
							</div>
						</div>
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_persona_arl" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td>Nombre Completo</td>
										<td>Identificacion</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_agregar_cargo" method="post">
<div class="modal fade" id="modal_agregar_cargo" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-suitcase"></span>  Entrega de Puesto de Trabajo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_metas">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" data-sw="sw" data-place="responsabilidades" id="responsabilidades"><a><span class="fa fa-info-circle red"></span> Responsabilidades</a></li>
              <li class="pointer" data-sw="sw" data-place="accesos" id="accesos"><a><span class="fa fa-info-circle red"></span> Accesos</a></li>
              <li class="pointer" data-sw="sw" data-place="informes" id="informes"><a><span class="fa fa-info-circle red"></span> Informe</a></li>
              <li class="pointer" data-sw="sw" data-place="comites" id="comites"><a><span class="fa fa-info-circle red"></span> Comites </a></li>
              <li class="pointer" data-sw="sw" data-place="logros" id="logros"><a><span class="fa fa-info-circle red"></span> Logros </a></li>
			  <li class="pointer" data-sw="sw" data-place="adjunto" id="adjunto"><a><span class="fa fa-info-circle red"></span> Archivos </a></li>
            </ul>
          </div>
        </nav>
        <div class="container_detalles_metas visible" data-sw="sw" data-place="responsabilidades" data-containers="containers">
		  <div class="modal-body" id="bodymodal">
		 	 <div class="row">
				<label class="col-md-12 "> 1.Relaciones las responsabilidades y actividades que tenía a su cargo: </label>
					<div class="col-md-12">
						<textarea name="responsabilidades_ecargo" id="responsabilidades_ecargo" style="width:100%" rows="5"></textarea>
					</div>
          		</div>
        	</div>
		</div>
		<!-- Text area de accesos -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="accesos">
			<div class="modal-body" id="bodymodal">
				<div class="row">
					<label class="col-md-12 "> 2.Relacione la información a cargo y Claves De Acceso: </label>
						<div class="col-md-12 ">
							<textarea name="accesos_ecargo" id="accesos_ecargo" style="width:100%" rows="5"></textarea>
						</div>
				</div>
          </div>
        </div>

        <!-- Text area de informes -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="informes">
		<div class="modal-body" id="bodymodal">
				<div class="row">
				<label class="col-md-12 "> 2.1 Relacione los informes, fechas de entrega y fuentes de informacion utilizados para el desarrollo de sus funciones: </label>
					<div class="col-md-12 ">
						<textarea name="informes_ecargo" id="informes_ecargo" style="width:100%" rows="5"></textarea>
					</div>
				</div>
          </div>
        </div>

        <!-- Tabla de comites -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="comites">
		<div class="modal-body" id="bodymodal">
				<div class="row">
				<label class="col-md-12 "> 2.2 Relación De Los Comités En Los Cuales Participa Como Miembro O Delegatario: </label>
					<div class="col-md-12 ">
						<textarea name="comites_ecargo" id="comites_ecargo" style="width:100%" rows="5"></textarea>
					</div>
				</div>
        	</div>
        </div>

        <!-- Tabla de logros -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="logros">
		<div class="modal-body" id="bodymodal">
				<div class="row">
				<label class="col-md-12 " style="margin-top: 2%"> 3. Mencione los Logros, Registros Y Documentación De su  Gestión: </label>
							<div class="col-md-12 ">
								<textarea name="logros_ecargo" id="logros_ecargo" style="width:100%" rows="5"></textarea>
							</div>
				</div>
        	</div>
        </div>
      </div>
	   <!-- Tabla de adjunto -->
	   <div class="container_detalles_metas oculto" data-sw="sw" data-place="adjunto">
		<div class="modal-body" id="bodymodal">
		<div class="row">
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong> Nota: </strong> Por favor adjuntar el documento en formato PDF.
							<br>Recuerde que debe adjuntar un solo PDF con todos los documentos que desee entregar.
						</div>
		</div>
		<div class="row">
						<div class="col-md-12 " style="padding: 0 0;">
							<div id="adjunto_ecargo" class="input-group agrupado">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="adjunto_ecargo" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" id="adjunto_ecargo" class="form-control" readonly placeholder='Adjuntar Documento' required>
							</div>
						</div>
					</div>
        </div>
      </div>

      <div class="modal-footer" id="footermodal">
	  	<button type="button" class="btn btn-danger active" id='btn_guardar_ecargo'><span class="fa fa-check"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
</form>

<form id="form_buscar_persona_ausentismo" method="post">
	<div class="modal fade" id="modal_buscar_persona_ausentismo" role="dialog">
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
								<span class="input-group-btn agregar_postulante_arl">
									<button class="btn btn-default" type="button" id="btn_agregar_postulante_ausentismo">
										<span class='fa fa-user-plus red'></span> Nuevo
									</button>
								</span>
								<input id='txt_buscar_jefe' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
							</div>
						</div>
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_persona_ausentismo" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td>Nombre Completo</td>
										<td>Identificacion</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_revisar_prestamo" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-money"></span> Revisar Prestamo</h3>
			</div>
			<div class="modal-body " id="bodymodal">
				<div class="panel panel-info" style="width: 90%; margin: 0 auto;">
					<div class="panel-heading" style="display: flex;flex-flow: row nowrap;justify-content:space-between;">
						<strong>Descuentos del Usuario</strong>
						<span><strong>Cupo Disponible: </strong><span id="maximo">0</span></span>
						<span id="texto_cupo"><strong>Capacidad de Pago: </strong><span id="saldo">0</span></span>
					</div>
					<div class="panel-body">
						<div class="row" style="width: 100%; margin: 0px;">
							<div class="col-md-6 col-sm-12 agrupado">
								<div class="input-group">
									<div class="input-group-addon"><span class="fa fa-dollar"></span></div>
									<input type="number" class="form-control" id="txtsalario" placeholder="Salario de la persona"><br>
									<div id="btnlimpiar_descuentos" class="input-group-addon btn-danger" style="cursor: pointer"><span class="fa fa-remove"></span></div>
								</div>
							</div>
							<div class="col-md-6 col-sm-12 agrupado">
								<div class="input-group">
									<div class="input-group-addon"><span class="fa fa-dollar"></span></div>
									<input type="number" class="form-control" id="txtsaldo_pendiente" placeholder="Saldo Prestamo Pendiente" disabled><br>
								</div>
							</div>
						</div><br>
						<!-- Inicio Formulario Descuentos-->
						<form id="form_descuentos">
							<div class="row" style="width: 100%;">
								<div class="col-md-6 col-sm-12">
									<select id="cbxtipo_descuento" name="tipo_descuento" required class="form-control  cbxtipo_descuento"> </select>
								</div>
								<div class="col-md-6 col-sm-12">
									<input type="text" class="form-control" name="concepto" id="txtconcepto" placeholder="Concepto" required>
								</div>
							</div>
							<div class="row" style="width: 100%; margin: 0px;">
								<div class="col-md-6 agrupado col-sm-12">
									<div class="input-group">
										<div class="input-group-addon"><span class="fa fa-dollar"></span></div>
										<input type="number" class="form-control" name="valor" id="txtvalor" min="0" placeholder="Valor Cuota" required><br>
									</div>
								</div>
								<div id="campo_deuda" class="col-md-6 col-sm-12 agrupado" hidden>
									<div id="input_deuda" class="input-group"></div>
								</div>
							</div>
							<div class="row" style="margin-left: 0px;">
								<div class="col-md-6 agrupado">
									<button type="submit" class="btn btn-danger">Agregar Descuento</button>
								</div>
							</div>
						</form>
						<!-- Fin Formulario Descuentos-->
						<div id="descuentos"></div>
						<div class="table-responsive">
							<table id="tabla_descuentos" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th colspan="4" class="nombre_tabla">TABLA DESCUENTOS</th>
									</tr>
									<tr class="filaprincipal">
										<td class="opciones_tbl">No.</td>
										<td>Concepto</td>
										<td>Valor</td>
										<td>Total</td>
										<td>Gestión</td>
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
				<button type="button" id="btnrevisar" class="btn btn-danger active btnAgregar"> <span class="fa fa-check"></span> Aprobar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<form id="form_gestionar_solicitud" method="post">
	<div class="modal fade" id="modal_gestionar_solicitud" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Gestionar Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div id='div_procesando' class="row" style="display: flex; justify-content: center;">
						<span class="pointer conadjuntos"> <span class="fa fa-folder-open red"></span>Clic aqui para adjuntar soportes a la solicitud</span>
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

<form id="form_administrar" method="post">
	<div class="modal fade" id="modal_administrar" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Módulo</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<nav class="navbar navbar-default" id="menu_administrar">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
								<li class="pointer permisos"><a><span class="fa fa-gears red"></span> Permisos</a></li>
								<li class="pointer prestamos"><a><span class="fa fa-money red"></span> Prestamos</a></li>
								<li class="pointer seleccion"><a><span class="fa fa-crosshairs red"></span> Selección</a></li>
								<li class="pointer certificados"><a><span class="fa fa-address-card red"></span> Certificados</a></li>
							</ul>
						</div>
					</nav>
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
					<div class="prestamos adm_proceso oculto">
						<div style="display: flex; justify-content: space-evenly;">
							<div id="prestamos" style="width: 100%;">
								<nav class="navbar navbar-default" id="menu_administrar_prestamos">
									<div class="container-fluid">
										<ul class="nav nav-tabs nav-justified">
											<li class="pointer cuotas active"><a><span class="fa fa-money red"></span> Cuotas</a></li>
											<li class="pointer descuentos"><a><span class="fa fa-pencil-square-o red"></span> Descuentos</a></li>
										</ul>
									</div>
								</nav>
								<div class="cuotas oculto">
									<div style="display: flex; justify-content: space-evenly;">
										<div class="form-group">
											<label for="cuotas_libre">Máximo Cuotas Prestamo Libre</label>
											<input id="txtcuotas_libre" type="number" min="0" max="100" name="cuotas_libre" class="form-control text-center" placeholder="Máximo Cuotas Libre">
										</div>
										<div class="form-group">
											<label for="cuotas_cruce">Máximo Cuotas Prestamo Cruce</label>
											<input id="txtcuotas_cruce" type="number" min="0" max="100" name="cuotas_cruce" class="form-control text-center" placeholder="Máximo Cuotas Cruce">
										</div>
									</div>
								</div>
								<div class="descuentos oculto">
									<div style="display: flex; justify-content: space-evenly;">
										<div class="form-group">
											<label for="salud">Porcentaje Descuento Salud</label>
											<div class="input-group">
												<input id="txtSalud" type="number" min="0" max="100" name="salud" class="form-control text-center" placeholder="Porcentaje Descuento de Salud">
												<span class="input-group-addon"><strong>%</strong></span>
											</div>
										</div>
										<div class="form-group">
											<label for="pension">Porcentaje Descuento Pensión</label>
											<div class="input-group">
												<input id="txtPension" type="number" min="0" max="100" name="pension" class="form-control text-center" placeholder="Porcentaje Descuento de Pension">
												<span class="input-group-addon"><strong>%</strong></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="seleccion adm_proceso oculto row">
						<div class="col-md-12">
							<div class="form-group agrupado text-center">
								<label for="pension">Correo Electrónico Responsable</label>
								<div class="input-group">
									<input type="email" id="txt_correo_responsable" name="correo" class="form-control text-center" placeholder="Correo Electrónico Responsable">
									<span class="input-group-addon"><strong>@</strong></span>
								</div>
							</div>
						</div>
					</div>
					<div class="certificados adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<span type="button" class="btn btn-default" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
							<table id="tabla_opciones_certificado" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th colspan="3" class="nombre_tabla">TABLA OPCIONES</th>
									</tr>
									<tr class="filaprincipal">
										<td>No.</td>
										<td>Opción</td>
										<td>Gestión</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="csep active"></div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button id="btnguardar_config" type="submit" class="btn btn-danger active btnAgregar btnCuotas"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_terminos_condiciones" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="	fa fa-edit"></span> Terminos y Condiciones</h3>
			</div>
			<div class="modal-body " id="bodymodal">
				<div id='text_terminos'>

				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active btnAgregar" id='btn_aceptar_terminos'> <span class="fa fa-check"></span> Continuar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<form id="frm_terminar_requisicion_posgrado">
	<div class="modal fade" id="modal_terminar_requisicion_posgrado" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title">
						<span class="fa fa-edit"></span> Tipo de Orden
					</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<select name="tipo_orden" id="" class="form-control cbx_tipo_orden">
						<option value="">-- Seleccione Tipo de Orden --</option>
					</select>
					<div class="form-group input-group agro">
						<input name="codigo_sap" type="hidden">
						<span id="span_codigo_sap" class="form-control text-left pointer sin_margin">Seleccionar código SAP</span>
						<span id="sele_cod_sap" class="input-group-addon red_primari pointer btn-Efecto-men"><span class="glyphicon glyphicon-search"></span></span>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar" id='btn_aceptar_terminos'> <span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_opciones_certificados" role="dialog">
	<form id="form_crear_opcion_certificado">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Nueva Opción</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="width:100%">
						<input type="text" class="form-control" name="nombre_item" placeholder="Nombre nuevo item" required>
						<input type="text" class="form-control" name="nombre_clave_item" placeholder="Nombre clave" required>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-plus"></span> Crear</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</form>
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

<div class="modal fade" id="modal_administrar_solicitudes" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar CSEA</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<div id="container_admin_comite">
						<table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="6" class="nombre_tabla">TABLA COMITÉ</td>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">Ver</td>
									<td>Nombre</td>
									<td>Descripción</td>
									<td>#Post.</td>
									<td>Estado</td>
									<td class="opciones_tbl_btn">Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div id="container_admin_csep" class="oculto">
						<div id='container_mensaje_ap'></div>
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
									<td>Tipo</td>
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

<div class="modal fade" id="modal_listar_archivos_adjuntos" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed " id="tabla_adjuntos_th" cellspacing="0" width="100%">
						<thead class="">
							<tr>
								<td colspan="3" class="nombre_tabla">TABLA DE ADJUNTOS</td>
								<td class="sin-borde text-right border-left-none" colspan="5">
									<span class="btn btn-default btnAgregar" id="agregar_adjuntos_nuevos">
										<span class="fa fa-plus red"></span> Agregar Adjunto</span>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">Ver</td>
								<td>Nombre</td>
								<td>Fecha Adjunto</td>
								<td>Nombre usuario</td>
								<!-- <td>Accion</td> -->
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

<form id="form_subir_archivo_adjunto" method="post">
	<div class="modal fade" id="modal_archivos_adjuntos" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Gestionar Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div id='div_procesando' class="row" style="display: flex; justify-content: center;">
						<span class="pointer conadjuntos"> <span class="fa fa-folder-open red"></span>Clic aqui para adjuntar soportes a la solicitud</span>
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

<div class="modal fade" id="modal_enviar_archivos" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<form class="dropzone needsclick dz-clickable" id="Subir" action="">
					<input type="hidden" name="id" id="id_archivo" val="0">
					<div class="dz-message needsclick">
						<p>Arrastre archivos o presione clic aquí</p>
					</div>
				</form>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
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
				<div id='container_tabla_postulantes'>
					<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_postulantes_csep" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="10" class="nombre_tabla">TABLA POSTULANTES CSEA</td>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">ver</td>
								<td>Tipo</td>
								<td>Postulante</td>
								<td>HV</td>
								<td>Dependencia</td>
								<td>Cargo</td>
								<td>#Apr.</td>
								<td>#Neg.</td>
								<td>Estado</td>
								<td>Acción</td>
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
								<li class="pointer active" id="btn_ver_nuevo">
									<a><span class="fa fa-folder-open red"></span> Contrato Nuevo</a>
								</li>
								<li class="pointer" id="btn_ver_actual">
									<a><span class="fa fa-folder red"></span> Contrato Actual</a>
								</li>
							</ul>
						</div>
					</nav>
				</div>
				<div id="tabla_detalle_postulante" class="table-responsive">
					<table class="table text-center">
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
							<td class="dependencia" colspan='3'></td>
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
						<tr class="tr_observaciones_contrato">
							<td class="ttitulo">Observaciones Contrato</td>
							<td class="observaciones_contrato" colspan='3'></td>
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
					<div class="row table-responsive" style="width: 100%">
						<table class="table table-bordered table-hover table-condensed" id="tabla_estados_csep" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
								</tr>
								<tr class="filaprincipal">
									<td>Nombre</td>
									<td>Fecha</td>
									<td>Usuario</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
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
				<button type="button" class="btn btn-default active" data-dismiss="modal" style="float: right;"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
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
						<span class='nombre_tabla form-control container_tip_Ca_modi oculto'>Información Nueva</span>
						<input type="text" name="procedencia" class="form-control" placeholder="Ciudad Procedencia" required>
						<select name="id_departamento" required class="form-control  cbxdepartamento"> </select>
						<select name="id_cargo" required class="form-control">
							<option value="">Seleccione Cargo</option>
						</select>
						<select name="id_formacion" required class="form-control cbxformacion"></select>
						<div class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<a class="btn btn-default" id='ver_hoja_modi' href='#' target='_blank'>
										<span class="fa fa-eye red"></span>ver
									</a>
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span>Buscar
										<input name="hoja_vida" type="file" style="display: none;" id="hoja_vida">
									</span>
								</label>
								<input type="text" class="form-control" readonly placeholder='Hoja de vida' id='text_hoja_vida'>
							</div>
						</div>
						<div class="agrupado" id='container_adj_prueba_modi'>
							<div class="input-group ">
								<label class="input-group-btn">
									<a class="btn btn-default" id='ver_prueba_modi' href='#' target='_blank'>
										<span class="fa fa-eye red"></span>ver
									</a>
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span>Buscar
										<input name="prueba_psicologia" type="file" style="display: none;" id="prueba_psicologia">
									</span>
								</label>
								<input type="text" class="form-control" readonly placeholder='Informe Evaluativo' id='text_prueba_psicologia'>
							</div>
						</div>
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
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

<form id="form_gestionar_postulante" method="post">
	<div class="modal fade" id="modal_gestionar_postulante" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Gestionar Postulante</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<label for="req_fechas_contrato" class='form-control text-center' style='padding : 5px;font-weight:normal'>
							<input value="1" type="checkbox" name="req_fechas_contrato" id="req_fechas_contrato"> Definir Fechas de contrato
						</label>
						<div id='container_fechas_cot' class='oculto'>
							<div class="agro agrupado">
								<div class="input-group">
									<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Inicio Contrato</span>
									<input name='fecha_inicio_contrato' class="form-control sin_margin" type='date'>
								</div>
							</div>
							<div class="agro agrupado">
								<div class="input-group">
									<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Cierre Contrato</span>
									<input name='fecha_fin_contrado' class="form-control sin_margin" type='date'>
								</div>
							</div>
						</div>
						<textarea name="observaciones" class="form-control comentarios" placeholder="Observaciones" required></textarea>
						<div id="test_imagen"></div>
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

<div id="modal_estados_ecargo" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Historial Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_historial_estados_ecargo" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr>
							<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">#</td>
							<td>Estado</td>
							<td>Fecha</td>
							<td>Usuario</td>
							<td>Comentario</td>
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

<div id="modal_materias" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title">
					<span class="fa fa-list"></span> Materias Asignadas
				</h3>
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
						</tr>
					</thead>
					<tbody></tbody>
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
				<h3 class="modal-title">
					<span class="fa fa-building"></span> Programas/Dependencias Asignadas
				</h3>
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
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_candidatos" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Gestionar Candidatos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_candidatos" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<td colspan="2" class="nombre_tabla">TABLA CANDIDATOS</td>
							<th colspan="2" class="sin-borde text-right border-left-none">
								<div style="display:flex;flex-direction:row-reverse;">
									<div id="boton_agradecimiento" style="margin-left: 15px;"></div>
									<div id="botones_candidatos"></div>
								</div>
								</td>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">No.</td>
							<td>Nombre</td>
							<td>Proceso Actual</td>
							<td style="width:80px;">Acción</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
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

<div class="modal fade" id="modal_detalle_candidato" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Candidato</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Información del Candidato</th>
						<th id="btn_hv_candidato" colspan="2" class="sin-borde text-right border-left-none"></th>
					</tr>
					<tr>
						<td class="ttitulo">Nombre Candidato: </td>
						<td colspan='3'><span><span class="info_candidato"></span></span></td>
					</tr>
					<tr>
						<td class="ttitulo">Identificación:</td>
						<td class="info_identificacion"></td>
						<td class="ttitulo">Cargo Actual:</td>
						<td class="info_cargo_candidato"></td>
					</tr>
					<tr>
						<td class="ttitulo">Correo:</td>
						<td class="info_correo"></td>
						<td class="ttitulo">Teléfono:</td>
						<td class="info_telefono"></td>
					</tr>
					<tr>
						<td class="ttitulo">Proceso Actual:</td>
						<td class="info_proceso_actual"></td>
						<td class="ttitulo">Fecha Asignación:</td>
						<td class="info_fecha_asignacion"></td>
					</tr>
					<tr>
						<td class="ttitulo">Jefe Inmediato:</td>
						<td class="info_jefe_inmediato" colspan='3'></td>
					</tr>
					<tr id="tr_entrevista">
						<td class="ttitulo">Lugar Entrevista:</td>
						<td class="info_lugar_entrevista"></td>
						<td class="ttitulo">Fecha Entrevista:</td>
						<td class="info_fecha_entrevista"></td>
					</tr>
					<tr id="row_motivo">
						<td class="ttitulo">Observación:</td>
						<td colspan="3" class="info_observación"></td>
					</tr>
					<tr id="row_exoneracion">
						<td class="ttitulo">Motivo Exoneración:</td>
						<td colspan="3" class="info_exoneración"></td>
					</tr>
					<tr id="row_rechazo_jefe">
						<td class="ttitulo">Motivo Rechazo Jefe Inmediato:</td>
						<td colspan="3" class="info_rechazo_jefe"></td>
					</tr>
				</table>
			</div>

			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div id="btnaprobar" style="margin-right: 5px;"></div>
				<div id="btnimprimir"></div>
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span>Cerrar
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_procesos_seleccion" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-edit"></span> Gestionar Proceso</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row">
					<div class="col-md-12">
						<div id="alert_jefe"></div>
						<div class="panel-group" id="accordion">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<a id="btn_seleccion" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" style="text-align:center;text-decoration:none;">
										<h4 class="panel-title" style="color:white;">
											<span class="glyphicon glyphicon-screenshot"></span> Selección
										</h4>
									</a>
								</div>
								<div id="collapseOne" class="panel-collapse collapse">
									<div class="panel-body" style="padding:0px;">
										<div id="procesos_seleccion" class="list-group"></div>
									</div>
								</div>
							</div>
							<div class="panel panel-primary">
								<div class="panel-heading">
									<a id="btn_contratacion" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" style="text-align:center;text-decoration:none;">
										<h4 class="panel-title" style="color:white;">
											<span class="fa fa-handshake-o">
											</span> Contratación
										</h4>
									</a>
								</div>
								<div id="collapseTwo" class="panel-collapse collapse">
									<div class="panel-body" style="padding:0px;">
										<div id="procesos_contratacion" class="list-group"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal"></div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_examen_medico" role="dialog">
	<div class="modal-dialog">
		<form id="form_examen_medico" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Citar Exámenes Médicos</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="input-group agrupado date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii" data-link-field="dtp_input1">
							<label for="fecha_examenes"></label>
							<input class="form-control sin_focus pointer" size="16" placeholder="Fecha Exámenes Médicos" type="text" value="" required name="fecha_examenes">
							<span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
							<span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
						</div>
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="en_ayunas" name="ayuno" value="1">
								<label for="en_ayunas" title="Ir en ayuno"> En Ayuno</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="sin_ayuno" name="ayuno" value="0">
								<label for="sin_ayuno" title="Candidato Descartado"> Sin Ayuno</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-calendar-check-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_adjuntos_seleccion" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Citar Exámenes Médicos</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_adjuntos_seleccion" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<td colspan="4" class="nombre_tabla">TABLA ADJUNTOS</td>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">Ver</td>
							<td>Nombre Archivo</td>
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

<div class="modal fade" id="modal_citacion" role="dialog">
	<div class="modal-dialog">
		<form id="form_citacion" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Citar Entrevista</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<label for="identificacion"></label>
						<input min="1" type="number" name="identificacion" class="form-control inputt" placeholder="Identificación del Candidato" readonly>
						<label for="nombre"></label>
						<input type="text" name="nombre" class="form-control" placeholder="Nombre Candidato" readonly>
						<label for="nombre_proceso"></label>
						<input type="text" name="nombre_proceso" class="form-control" placeholder="Nombre del Proceso" readonly>
						<div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
							<label for="fecha_entrevista"></label>
							<input class="form-control sin_focus pointer" size="16" placeholder="Fecha Entrevista" type="text" value="" required name="fecha_entrevista">
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-remove"></span>
							</span>
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
						<label for="lugar"></label>
						<select name="lugar" class="form-control cbxlugares">
							<option>Seleccione Lugar</option>
						</select>
						<label for="ubicacion"></label>
						<select name="ubicacion" class="form-control">
							<option>Seleccione Ubicación</option>
						</select>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-calendar-check-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<form id="form_enviar_pruebas" method="post">
	<div class="modal fade" id="modal_pruebas_psicotecnicas" role="dialog">
		<div class="modal-dialog modal-sm">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check-square-o"></span> Enviar Pruebas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div id="pruebas_psicotecnicas" class="row" style="text-align: center">
						<h3>No hay pruebas asignadas</h3>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="submit" class="btn btn-danger active"><span class="fa fa-send-o"></span> Enviar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_aval_medico" method="post">
	<div class="modal fade" id="modal_aval_medico" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Aval Médico</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="aval" type="file" style="display: none;" id="file_aval_medico">
									</span>
								</label>
								<input type="text" class="form-control" name="prueba" readonly placeholder='Aval Médico'>
							</div>
						</div>
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="vb_medico1" name="vb_medico" value="1">
								<label for="vb_medico1" title="Candidato Aprobado"> Candidato Aprobado</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="vb_medico2" name="vb_medico" value="2">
								<label for="vb_medico2" title="Candidato Descartado"> Candidato Descartado</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Adjuntar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_aval_seguridad" method="post">
	<div class="modal fade" id="modal_aval_seguridad" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Aval de Seguridad</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="aval" type="file" style="display: none;" id="file_aval_seguridad">
									</span>
								</label>
								<input type="text" class="form-control" name="prueba" readonly placeholder='Aval de Seguridad'>
							</div>
						</div>
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="aprobacion1" name="vb_seguridad" value="1">
								<label for="aprobacion1" title="Candidato Aprobado"> Candidato Aprobado</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="aprobacion2" name="vb_seguridad" value="2">
								<label for="aprobacion2" title="Candidato Descartado"> Candidato Descartado</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Adjuntar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_add_reemplazo" method="post">
	<div class="modal fade" id="modal_add_reemplazo" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Reemplazado</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre_reemplazado" class="form-control sin_margin sin_focus pointer" placeholder="Seleccione Reemplazado">
								<span class="input-group-addon pointer" id='btn_buscar_reemplazado' style='background-color:white'>
									<span class='fa fa-search red'></span> Reemplazado
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_contratacion" method="post">
	<div class="modal fade" id="modal_contratacion" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-handshake-o"></span> Contratación</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="input-group agrupado date form_month agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
							<label for="fecha_contratacion"></label>
							<input class="form-control sin_focus pointer" size="16" placeholder="Fecha de Contratación" type="text" value="" required name="fecha_contratacion">
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-remove red"></span>
							</span>
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-calendar red"></span>
							</span>
						</div>
						<select name="tipo_contrato" class="form-control cbxtipo_contrato" required></select>
						<input type="number" class="form-control" name="duracion_contrato" placeholder="Duración Contrato (Meses)" style="display:none">
						<!-- <div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon pointer">
									<span class='glyphicon glyphicon-usd red'></span>
								</span>
								<input type="number" class="form-control" name="salario" placeholder="Salario" required>
							</div>
						</div> -->
						<textarea name="observaciones" class="form-control" placeholder="Observaciones" maxlength="500" required></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-handshake-o"></span> Contratar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_generar_informe" method="post" action='/informe'>
	<div class="modal fade" id="modal_informe" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-address-card fa-lg"></span> Generar Informe</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<h4>
							<span class="fa fa-building red"></span> DEPENDENCIA Y CARGO
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" readonly name="dependencia" placeholder="Dependencia">
									<span class="input-group-addon">-</span>
									<input type="text" class="form-control" readonly name="cargo" placeholder="Cargo">
								</div>
							</div>
						</div>
						<hr>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-graduation-cap red"></span> FORMACIÓN
						</h4>
						<div class="col-md-12" style="padding:0px;">
							<div class="input-group adicional_info" style="padding-top: 6px;">
								<select class="sin_margin form-control" id="cbxestudios">
									<option value="">0 Estudios Agregados</option>
								</select>
								<span class="input-group-addon pointer" onClick="$('#modal_formacion').modal();">
									<span class='fa fa-plus red'></span>
								</span>
								<span class="input-group-addon pointer" id="btn_remove_formacion">
									<span class='glyphicon glyphicon-remove  red'></span>
								</span>
							</div>
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-history red"></span> EXPERIENCIA
						</h4>
						<div class="col-sm-12 col-md-6" style="padding-left: 0px; padding-right:5px;">
							<input type="text" class="form-control" name="categoria_colciencias" placeholder="Categoría Colciencias" required>
						</div>
						<div class="col-sm-12 col-md-6" style="padding-left: 5px; padding-right:0px;">
							<input type="text" class="form-control" name="indiceh" placeholder="indiceh" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<input type="text" name="cvlac" class="form-control" placeholder="CVLAC" required />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="exp_docente" class="form-control ilimitado" rows="5" placeholder="Experiencia en Docencia" required></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="exp_investigacion" class="form-control ilimitado" rows="5" placeholder="Experiencia en Investigación" required></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="exp_profesional" class="form-control ilimitado" rows="5" placeholder="Experiencia Profesional" required></textarea>
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-book red"></span> PRODUCCIÓN CIENTÍFICA
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="produccion" class="form-control ilimitado" rows="5" placeholder="Producción Científica" required></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="formacion" class="form-control ilimitado" rows="5" placeholder="Productos de Formacion" required></textarea>
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-pencil-square-o red"></span> PRUEBAS
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="pruebas" class="form-control ilimitado" rows="5" placeholder="Pruebas" required></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12" style="padding: 0px;">
							<input type="text" class="form-control" name="suficiencia_ingles" placeholder="Certificado de Suficiencia Inglés" required>
						</div>
					</div>
					<div class="row">
						<div class="input-group adicional_info" style="padding-top: 6px;">
							<select class="sin_margin form-control" id="cbxcompetencias">
								<option value="">0 Competencias Asignadas</option>
							</select>
							<span class="input-group-addon pointer" id='btn_add_competencia'>
								<span class='fa fa-plus red'></span>
							</span>
							<span class="input-group-addon pointer" id='btn_edit_competencia'>
								<span class='fa fa-edit red'></span>
							</span>
							<span class="input-group-addon pointer" id="btn_remove_comp">
								<span class='glyphicon glyphicon-remove  red'></span>
							</span>
						</div>
						<h4>
							<span class="fa fa-newspaper-o red"></span> CONCEPTO
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<textarea name="concepto" class="form-control ilimitado" rows="5" placeholder="Concepto" maxlength="500" required></textarea>
						</div>
					</div>
					<input type="hidden" name="accion" value="">
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="submit" class="btn btn-danger active"><span class="fa fa-address-card"></span> Generar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_buscar_competencia" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Competencia</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row" id="" style="width: 100%">
					<div class="form-group agrupado col-md-8 text-left">
						<form id="form_buscar_competencia" method="post">
							<div class="input-group">
								<input name="competencia" class="form-control" placeholder="Ingrese nombre">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
								</span>
							</div>
						</form>
					</div>
					<div class="table-responsive col-md-12" style="width: 100%">
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_competencias" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="3" class="nombre_tabla">TABLA COMPETENCIAS</td>
								</tr>
								<tr class="filaprincipal">
									<td>#</td>
									<td>Nombre</td>
									<td class="opciones_tbl_btn">Acción</td>
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

<form id="form_nivel_competencia" method="post">
	<div class="modal fade" id="modal_nivel_competencia" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-level-up-alt"></span> Nivel de Competencia</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="rd_vb_f" name="vb_comp" value="1">
								<label for="rd_vb_f" title="Fortaleza"> Fortaleza</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="rd_vb_o" name="vb_comp" value="0">
								<label for="rd_vb_o" title="Oportunidad de Mejora"> Oportunidad de Mejora</label>
							</div>
						</div>
						<select name="nivel_competencia" class="form-control cbxnivel"></select>
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="submit" class="btn btn-danger active"><span class="fa fa-plus"></span> Agregar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_formacion" method="post">
	<div class="modal fade" id="modal_formacion" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-graduation-cap"></span> Agregar Formación</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="tipo_formacion" class="form-control cbxformacion"></select>
						<input type="text" class="form-control" name="formacion" placeholder="Título" required>
						<input type="text" class="form-control" name="universidad" placeholder="Universidad" required>
						<div class="row" style="margin-left:0px;margin-right:0px;width:100%;">
							<div class="col-md-4 col-sm-12" style="margin-top:10px;padding:0px;display: flex;justify-content: center;">
								<label for="fecha_graduacion">Año Graduación</label>
							</div>
							<div class="col-md-8 col-sm-12" style="padding:0px;">
								<div class="agrupado">
									<div class="input-group date form_date agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
										<input class="form-control sin_focus pointer" size="16" placeholder="Año Graduación" type="text" value="" required name="fecha_graduacion">
										<span class="input-group-addon pointer">
											<span class="glyphicon glyphicon-remove"></span>
										</span>
										<span class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="submit" class="btn btn-danger active"><span class="fa fa-plus"></span> Agregar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_historial_candidato" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Historial del Candidato</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_historial_candidato" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<td colspan="4" class="nombre_tabla">TABLA HISTORIAL DE PROCESOS</td>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">No.</td>
							<td>Proceso</td>
							<td>Fecha</td>
							<td>Responsable</td>
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

<div class="modal fade" id="modal_citacion_entrevista_jefe" role="dialog">
	<div class="modal-dialog">
		<form id="form_citacion_entrevista_jefe" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Citar Entrevista</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
							<label for="fecha_entrevista"></label>
							<input class="form-control sin_focus pointer" size="16" placeholder="Fecha Entrevista" type="text" value="" required name="fecha_entrevista">
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-remove"></span>
							</span>
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
						<!-- <div class="agro agrupado">
							<div class="input-group">
								<input id="responsable_entrevista" type="text" name="nombre_responsable" class="form-control sin_margin sin_focus pointer" placeholder="Seleccione Jefe Inmediato" required>
								<span class="input-group-addon pointer" id='btn_buscar_responsable' style='background-color:white'><span class='fa fa-search red'></span> Jefe Inmediato</span>
							</div>
						</div> -->
						<div class="agro agrupado">
							<div class="input-group">
								<input id="responsable_entrevista" type="text" name="nombre_responsable" class="form-control sin_margin sin_focus" placeholder="Jefe Inmediato" disabled>
								<span class="input-group-addon" style='background-color:white'><span class='fa fa-user red'></span> Jefe Inmediato</span>
							</div>
						</div>
						<label for="lugar"></label>
						<select name="lugar" class="form-control cbxlugares" required>
							<option>Seleccione Lugar</option>
						</select>
						<label for="ubicacion"></label>
						<select name="ubicacion" class="form-control" required>
							<option>Seleccione Ubicación</option>
						</select>
						<textarea class="form-control" cols="1" rows="3" placeholder="Recomendaciones" name="recomendaciones"></textarea>
						<div class="agrupado">
							<div class="input-group">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="hoja_vida" type="file" style="display: none;">
									</span>
								</label>
								<input type="text" class="form-control" readonly placeholder='Hoja de Vida'>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-calendar-check-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_fecha_ingreso" role="dialog">
	<div class="modal-dialog">
		<form id="form_fecha_ingreso" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Fecha De Ingreso del Candidato</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="input-group agrupado date form_month agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
							<label for="fecha_ingreso"></label>
							<input class="form-control sin_focus pointer" size="16" placeholder="Fecha de Ingreso del Candidato" type="text" value="" required name="fecha_ingreso">
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-remove red"></span>
							</span>
							<span class="input-group-addon pointer">
								<span class="glyphicon glyphicon-calendar red"></span>
							</span>
						</div>
						<!-- <select name="tipo_contrato" required class="form-control cbxtipo_contrato" required></select> -->
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-calendar-check-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_tipo_csep" role="dialog">
	<div class="modal-dialog">
		<form id="form_tipo_csep" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-calendar-check-o"></span> Tipo de CSEA</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="csep_virtual" name="csep" value="1">
								<label for="csep_virtual" title="CSEP Virtual"> CSEA Virtual</label>
							</div>
							<div class="funkyradio-success">
								<input type="radio" id="csep_presencial" name="csep" value="0">
								<label for="csep_presencial" title="CSEP Presencial"> CSEP Presencial</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-check"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="modal_candidato_csep" role="dialog">
	<div class="modal-dialog">
		<form id="form_candidato_csep" enctype="multipart/form-data" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Nuevo CSEA</h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div id='cont_nuevos'>
							<input type="text" name="procedencia" class="form-control" placeholder="Ciudad Procedencia" required>
							<select name="id_formacion" required class="form-control cbxformacion"></select>
						</div>
						<textarea class="form-control" cols="1" rows="3" placeholder="Observaciones" name="observaciones"></textarea>
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

<div class="modal fade" id="modal_ecargo" role="dialog">
	<div class="modal-dialog">
		<form id="form_ecargo" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-id-card-o"></span> <span class="titulo_modal_ecargo"></span></h3>
				</div>
				<div class="modal-body " id="bodymodal">
					<div class="row">
						<div class="agro agrupado" style="margin-bottom: 6px;" >
							<select id="cbxmotivo_ec" name="motivo" class="form-control sin_margin cbxmotivo_ec">
							</select>
						</div>
					</div>
					<?php if ($administra) { ?>
						<div class="row">
							<div class="input-group" style="margin-bottom: 6px;" id='buscar_colaborador_cargo'>
								<input type="text" class="form-control sin_margin sin_focus pointer" name="colaborador" placeholder="Buscar Colaborador" required readonly>
								<span class="input-group-addon pointer txt_colaborador" style='background-color:white'>
									<span class='fa fa-search red'></span> Buscar
								</span>
							</div>
						</div>
					<?php } ?>
					<div class="row">
						<div class="input-group" style="margin-bottom: 6px;" id='buscar_jefe_cargo'>
							<input type="text" class="form-control sin_margin sin_focus pointer" name="jefe" placeholder="Buscar Jefe Inmediato" required readonly>
							<span class="input-group-addon pointer txt_jefe_cargo" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
					</div>
					<div class="row">
						<div class="input-group" style="margin-bottom: 6px;" id='buscar_jefe_cargo1'>
							<input type="text" class="form-control sin_margin sin_focus pointer" name="jefe1" placeholder="Buscar Jefe Inmediato 2" required readonly>
							<span class="input-group-addon pointer txt_jefe_cargo" style='background-color:white'>
								<span class='fa fa-search red'></span> Buscar
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
				<?php if ($administra) { ?>
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<?php  } ?>
					<button type="button" class="btn btn-danger active" data-dismiss="modal" id='btn_acargo' ><span class="fa fa-check"></span>Continuar</button>
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
								<span class="input-group-btn">
									<button class="btn btn-default" type="button" id="btn_agregar_postulante">
										<span class='fa fa-user-plus red'></span> Nuevo
									</button>
								</span>
								<input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del docente">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
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
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
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
						<select required name="genero" class="form-control cbxgenero"></select>
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
								<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido" >
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre" class="form-control" placeholder="Primer Nombre" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_nombre" class="form-control" placeholder="Segundo Nombre">
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="email" name="correo" class="form-control inputt" placeholder="Correo Electrónico" required>
								<span class="input-group-addon">-</span>
								<input type="number" name="telefono" class="form-control" placeholder="Teléfono" required>
							</div>
						</div>
						<!-- <div id="info_adicional_persona" class="agro agrupado" hidden></div> -->
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

<form id="form_enviar_invitacion">
	<div class="modal fade" id="modal_invitacion" role="dialog">
		<div class="modal-dialog modal-lg modal-95">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Invitación Inducción</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<div class="col-md-4">
						<label for="fecha">Fecha inicio de labores</label>
							<input type="text" name="fecha" class="form-control" placeholder="Ej: Miércoles 4 de Septiembre de 2019.">
						</div>
						<div class="col-md-4">
						<label for="">Cargo</label>
							<input type="text" name="ubicacion" class="form-control" placeholder="Ej: Auxiliar 1.">
						</div>
						<div class="col-md-4">
						<label for="jornada">Información adicional</label>
							<input type="text" name="jornada" class="form-control" placeholder="Ej: En sus dos jornadas de 8:00 am a 11:00 am y de 2:00 pm a 5:00 pm.">
						</div>
					</div>
					<div id="imagen_induccion" style="background-image: url('<?php echo base_url(); ?>imagenes/induccion.png');">
						<div id="info_induccion">
							<p id="invitacion_fecha">- Miércoles 4 de Septiembre de 2019.</p>
							<p id="invitacion_ubicacion">- Auxiliar 1.</p>
							<p id="invitacion_jornada">- En sus dos jornadas de 8:00 am a 11:00 am y de 2:00 pm a 5:00 pm.</p>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-envelope-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

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
						<select id="cbxprograma" name="id_programa" class="form-control cbxdependencias" required></select>
						<select name="cargo_id" class="form-control cbxcargos" required>
							<option value="">Seleccione Cargo</option>
						</select>
						<textarea name="plan_trabajo" class="form-control" placeholder="Digite aquí el plan de trabajo"></textarea>
						<div class="agro agrupado oculto" id='fingreso'>
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha de Ingreso</span>
								<input type="date" class="form-control sin_margin" name='fecha_ingreso'>
							</div>
						</div>
						<div class="rev_final oculto">
							<select name="tipo_contrato" class="form-control cbxtipo_contrato"></select>
							<input type="number" class="form-control" name="duracion_contrato" placeholder="Duración Contrato (Meses)">
						</div>
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
					<tbody></tbody>
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
					<tbody></tbody>
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
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
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

<div class="modal fade" id="modal_tipos_certificado_laboral" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-id-card-o"></span> Certificados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="opciones__container">
					<div id="btn_certificado_basico" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Certificado Básico" data-content="Certificado con información básica del empleado.">
						<img src="<?php echo base_url() ?>/imagenes/contract.png" alt="..." class="opcion__img">
						<span class="opcion__span">CERTIFICADO BÁSICO</span>
					</div>

					<div id="btn_certificado_laboral" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Certificado Personalizado" data-content="Certificado con opciones elegibles segun las necesidades del empleado.">
						<img src="<?php echo base_url() ?>/imagenes/file.png" alt="..." class="opcion__img">
						<span class="opcion__span">CERTIFICADO PERSONALIZADO</span>
					</div>

					<div id="btn_cir" class="opcion__cont" data-toggle="popover" data-trigger="hover" data-placement="bottom" title="Certificado de ingresos" data-content="Realiza tus solicitudes de Certificado de ingresos y retención con un click.">
						<img src="<?php echo base_url() ?>/imagenes/tax.png" alt="Certificado de ingresos y retencion" class="opcion__img">
						<span class="opcion__span">Certificado de ingresos</span>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>


<form id="form_certificado_personalizado">
	<div class="modal fade" id="modal_certificado_personalizado" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-address-card-o"></span> Certificado Personalizado</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<h5>Seleccion los datos que desea en el certificado</h5>
						<div id="div_opciones_disponibles"></div>
						<div class="form-group">
							<textarea name="especificaciones" class="form-control form_certificado__textarea" placeholder="Otras especificaciones(Opcional)"></textarea>
						</div>
					</div>
					<div class="alert alert-info text-center" role="alert">
						Esta solicitud será atendida en un plazo máximo de 8 días hábiles. Una vez listo el certificado será notificado via correo electrónico.
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="fa fa-address-card-o"></span> Solicitar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade" id="modal_detalle_solicitud_certificado" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_detalle_certificado" class="table table-bordered table-condensed">
					<tr>
						<th class="nombre_tabla" colspan="2"> Informacion de la Solicitud</th>
						<th colspan="2" class="sin-borde border-left-none" style="text-align: right;">
							<a id="btn_descargar_certificado" class="btn btn-default oculto">
								<i class="fa fa-address-card red"></i> Descargar Certificado
							</a>
						</th>
					</tr>
					<tr>
						<td class="ttitulo">Solicitante: </td>
						<td colspan='3'>
							<span <?php if ($administra) { ?>class="red btn" onclick="mostrar_info_persona()" <?php } ?>>
								<span class="info_solicitante"></span>
							</span>
						</td>

					</tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td class="info_estado"></td>
						<td class="ttitulo">Tipo Solicitud:</td>
						<td class="info_tipo"></td>
					</tr>
					<tr>
						<td class="ttitulo">Fecha de Solicitud:</td>
						<td class="info_fecha"></td>
						<td class="ttitulo label_tipo_certificado">Tipo Certificado:</td>
						<td class="info_tipo_certificado"></td>
					</tr>
					<tr id="especificaciones_certificado">
						<td class="ttitulo">Especificaciones:</td>
						<td colspan="3" class="info_especificaciones"></td>
					</tr>
					<tr id="observaciones_certificado">
						<td class="ttitulo">Motivo rechazo:</td>
						<td colspan="3" class="info_observaciones"></td>
					</tr>
					<tr id="info_entrega_certificado">
						<td class="ttitulo">Fecha Entrega:</td>
						<td colspan="3" class="info_fecha_entrega"></td>
					</tr>
				</table>
				<ul id="especificaciones_certificado_opciones" class="list-group"></ul>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<div style="margin-left: 5px;">
					<button type="button" class="btn btn-default active" data-dismiss="modal">
						<span class="glyphicon glyphicon-resize-small"></span> Cerrar
					</button>
				</div>
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
				<div class="row" style="width: 90%">
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
								<td class="celular"></td>
							</tr>
							<tr>
								<td class="direccion"></td>
							</tr>
							<tr>
								<td class="barrio"></td>
							</tr>
							<tr>
								<td class="lugar_residencia"></td>
							</tr>
							<tr>
								<td class="correo_personal"></td>
							</tr>
							<tr>
								<td class="cargo_perso"></td>
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
						<select required name="genero" class="form-control cbxgenero"></select>
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
								<input type="text" name="segundo_apellido" class="form-control" placeholder="Segundo Apellido">
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre" class="form-control" placeholder="Primer Nombre" required>
								<span class="input-group-addon">-</span>
								<input type="text" name="segundo_nombre" class="form-control" placeholder="Segundo Nombre">
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="email" name="correo" class="form-control inputt" placeholder="Correo Electrónico" required>
								<span class="input-group-addon">-</span>
								<input type="number" name="telefono" class="form-control" placeholder="Teléfono" required>
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

<form id="form_adjuntar_certificado">
	<div class="modal fade" id="modal_adjuntar_certificado" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-envelope-o"></span> Enviar Certificado</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado" id="file_certificado_cir">
							<div class="input-group ">
								<label class="input-group-btn">
									<span class="btn btn-primary">
										<span class="fa fa-folder-open"></span> Buscar
										<input name="file_certificado" type="file" style="display: none;" id="file_certificado">
									</span>
								</label>
								<input type="text" class="form-control" name="certificado" readonly placeholder='Adjuntar Certificado'>
							</div>
						</div>
					</div>
					<div class="row">
						<textarea name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger"><span class="fa fa-envelope-o"></span> Enviar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_vb_pedagogico">
	<div class="modal fade" id="modal_vb_pedagogico" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check"></span> Visto Bueno Pedagógico</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="rd_vb_ap" name="vb" value="1" checked>
								<label for="rd_vb_ap" title="Aprobar Candidato"> Aprobar</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="rd_vb_de" name="vb" value="0">
								<label for="rd_vb_de" title="Rechazar Candidato"> Desaprobar</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-danger"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

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

<div class="modal fade" id="modal_cir" role="dialog">
	<div class="modal-dialog">
		<form id="form_nuevo_cir" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-money"></span> Certíficado de Ingresos y Retención</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select id="cbx_anios" name="anio_certificado" class="form-control"></select>
					</div>
					<div class="row" style="margin-top: 5px;">
						<label>
							<input type="checkbox" name="check_prestacion_servicio">
							Marque ésta opción si su contrato fue por prestación de servicios
						</label>
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

<div class="modal fade" id="modal_codigo_sap" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-hashtag"></span> Código SAP</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_codigos" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" style="margin-top: 40px;">
					<thead class="ttitulo ">
						<tr>
							<td colspan="4" class="nombre_tabla">TABLA CÓDIGOS SAP</td>
						</tr>
						<tr class="filaprincipal">
							<td>Código</td>
							<td>Nombre</td>
							<td class="opciones_tbl">Acción</td>
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

<div class="modal fade" id="modal_crear_reportes" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<form id="form_reporte">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-bar-chart"></span> Crear Reportes</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="tipo" class="form-control inputt cbxtipo filtro">
							<option value="">Tipo de Solicitud</option>
						</select>
						<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
							<input class="form-control filtro" type="date" name="fecha_inicio">
						</div>
						<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
							<input class="form-control filtro" type="date" name="fecha_fin">
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

<form id="form_vb_ausentismo">
	<div class="modal fade" id="modal_vb_ausentismo" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check"></span> Visto Bueno Ausentismo</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="funkyradio facturacion">
							<div class="funkyradio-success">
								<input type="radio" id="rd_vb_ausentismo" name="vb_ausentismo" value="1" checked>
								<label for="rd_vb_ausentismo" title="Aprobar Solicitud"> Aprobar</label>
							</div>
							<div class="funkyradio-danger">
								<input type="radio" id="rd_vd_ausentismo" name="vb_ausentismo" value="0">
								<label for="rd_vd_ausentismo" title="Rechazar Solicitud"> Desaprobar</label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-danger"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="form_pro_cambio_eps">
	<div class="modal fade" id="modal_pro_cambio_eps" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-comments"></span> Comentarios y Cargue de Documentos Cambio EPS</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<h1>Gola</h1>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button class="btn btn-danger"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
    $(document).ready(function () {
		obtener_vista_llama('<?php echo $vista?>','<?php echo $_SESSION["perfil"]?>','<?php echo $id?>','<?php echo $_SERVER["REQUEST_URI"] ?>','<?php echo $_SESSION["persona"]?>');
		inactivityTime();
		activarfile();
		Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
		Cargar_parametro_buscado_aux(1, ".cbxtipoIdentificacion_entidades", "Seleccione Tipo Identificación");
		Cargar_parametro_buscado(52, ".cbxformacion", "Seleccione Formación");
		Cargar_parametro_buscado_aux(74, "#cbxtipo_prestamo", "Seleccione Tipo de Prestamo");
		Cargar_parametro_buscado_aux(75, "#cbxtipo_descuento", "Seleccione Tipo de Descuento");
		Cargar_parametro_buscado(53, ".cbxcategoria", "Seleccione Categoria");
		Cargar_parametro_buscado(187, ".cbxgenero", "Seleccione Genero");
		get_estados_asignados_actividad();
		Cargar_parametro_buscado(115, ".cbxlugares", "Seleccione Lugar");
		Cargar_parametro_buscado_aux(117, "#cbxtipo_solicitud", "Seleccione Tipo Solicitud");
		Cargar_parametro_buscado_aux(118, ".cbxtipo_vacante", "Seleccione Tipo de Vacante");
		Cargar_parametro_buscado_aux(119, ".cbxtipo_cargo", "Seleccione Tipo de Cargo");
		Cargar_parametro_buscado_aux(65, ".cbxtipo_contrato", "Seleccione Tipo de Contrato");
		Cargar_parametro_buscado(190, ".cbx_tipo_orden", "Seleccione Tipo de Orden")
		Cargar_parametro_buscado(207, ".cbxtipo_posgrado", "Tipo Programa")
		Cargar_parametro_buscado(203, ".cbxnriesgo", "Seleccione Nivel de Riesgo");
		Cargar_parametro_buscado(36, "#lugares", "Seleccione Lugar",'datalist');
		Cargar_parametro_buscado(240, ".cbxnivel", "Seleccione Nivel de competencia"); 
		Cargar_parametro_buscado(248, ".cbxtipo_licencia", "Seleccione el tipo de licencia");
		Cargar_parametro_buscado_aux(276, ".cbxtipo_beneficiario", "Seleccione el tipo de beneficiario"); //276 pro 1233 local
		Cargar_parametro_buscado_aux(338, ".cbxmotivo_ec", "Seleccione motivo de entrega de cargo"); //338 pro 1234 local

		adjuntar_archivo();
		// listar_programas();
		listar_tipos_postulantes();
		mostrar_csep();
		mostrar_requisicion();
		listar_departamentos();
		mostrar_hoja_vida_menu(<?php echo $_SESSION["persona"]?>);
	});
	$(".form_datetime").datetimepicker({
		language: 'es',
		format: 'yyyy-mm-dd hh:ii',
		autoclose: true,
		initialDate: new Date(),
		startDate: new Date(),
		minuteStep: 30,
		daysOfWeekDisabled: [0, 6],
	});

	$(".form_date").datetimepicker({
		format: "yyyy",
		startView: 'decade',
		minView: 'decade',
		viewSelect: 'decade',
		autoclose: true,
	});

	$(".form_month").datetimepicker({
		format: "yyyy-mm-dd",
		startView: 'month',
		minView: 'month',
		viewSelect: 'month',
		autoclose: true,
	});
</script>

<style>

  .active{

    background-color: rgba(0,0,0,0.9);

  }

</style>

<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>