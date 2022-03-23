<?php 
  $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Eval" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true :false;
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
					<td colspan="2" class="nombre_tabla">
						TABLA SOLICITUDES<br>
						<span class="mensaje-filtro" hidden> 	
							<span class="fa fa-bell red"></span> 
							La tabla tiene algunos filtros aplicados.
						</span>
					</td>
					<td colspan="5" class="sin-borde text-right border-left-none"> 
					<?php if ($administra) {?>
					<span class="black-color pointer btn btn-default" id="btnResultadoMasivo" >
						<span class="fa fa-bar-chart red"></span> Generar Resultados
					</span>
					<span class="black-color pointer btn btn-default" id="btninforme" >
						<span class="fa fa-download red"></span> Informes
					</span>
						<!-- <a href="<?php echo base_url()?>index.php/evaluacion/exportar_resultados" class="btn btn-default" id="btn_resultados"><span class="fa fa-cloud-download red"></span> Resultados</a> -->
						<a class="btn btn-default" id="btn_exportar"><span class="fa fa-cloud-download red"></span> Exportar</a>
					<span class="black-color pointer btn btn-default" id="btnNotificaciones" >
						<span class="fa fa-bell red"></span> Notificar
					</span>
					<!-- <span class="black-color pointer btn btn-default" id="btnConfiAsignaciones" >
						<span class="fa fa-users red"></span> Asignaciones
					</span> -->
					<span class="black-color pointer btn btn-default" id="btnConfiguraciones" >
						<span class="fa fa-cogs red"></span> Administrar
					</span>
					<span class="black-color pointer btn btn-default" id="btnperiodo">
						<span class="fa fa-calendar red"></span> Periodo Actual
					</span>
					<?php }?>
					<span id="btn_csep"></span>
					<span id="btn_filtros" class="black-color pointer btn btn-default" >
						<span class="fa fa-filter red" ></span> Filtrar
					</span>
						<span id="btn_limpiar" class="black-color pointer btn btn-default" >
							<span class="fa fa-refresh red" ></span> Limpiar
						</span>
					</td>
				</tr>
				<tr class="filaprincipal">
					<td class="opciones_tbl">ver</td>
					<td>Periodo</td>
					<td>Tipo</td>
					<td>Funcionario</td>
					<td>Estado</td>
					<td>Resultado</td>
					<td>Acción</td>
				</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>	

	<div class="tablausu col-md-12 " id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
		<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
			<div id="container-principal2" class="container-principal-alt">
				<h3 class="titulo_menu">
					<span class="fa fa-navicon"></span> MENÚ
				</h3>
				<div class="row" id="menu_th">
					<div id="nueva_solicitud">
						<div class="thumbnail">
							<div class="caption">
								<img src="<?php echo base_url() ?>/imagenes/evaluacion.png" alt="...">
								<span class="btn  form-control btn-Efecto-men">NUEVA EVALUACIÓN</span>
							</div>
						</div>
					</div>
					<div id="listado_solicitudes">
						<div class="thumbnail">
							<div class="caption">
								<img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
								<span class="btn  form-control btn-Efecto-men">MIS SOLICITUDES</span>
							</div>
						</div>
					</div>
				</div>
				<p class="titulo_menu titulo_menu_alt pointer" id="btn_regresar"><span class="fa fa-reply-all naraja"></span>Regresar</p>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="modal_periodo_activo" role="dialog">
		<div class="modal-dialog">
		<form id="form_periodo" method="post">
			<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-calendar"></span> Periodo Activo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row">
				<div class="col-md-12 input-group" style="margin-bottom: 3px;">
					<input type="text" class="form-control sin_margin" name="periodo" placeholder="Perido Activo" required>
					<span class="input-group-addon pointer" style='background-color:white'>
					<span class='fa fa-calendar red'></span> Periodo
					</span>
				</div>
				</div>  
			</div>
			<div class="modal-footer" id="footermodal">
			<button type="submit" class="btn btn-danger active"><span class="fa fa-save"></span> Guardar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span
					class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
			</div>
		</form>  
		</div>
	</div>

	<div class="modal fade" id="Modal_resultados" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<form id="form_resultados">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-filter"></span> Generar Resultados</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<select name="id_metodo_eval" class="form-control cbxmetodo">
								<option value="">Seleccione Método de Evaluación</option>
							</select>
							<select name="id_estado" class="form-control id_estado">
								<option value="">Selccione Estado</option>
							</select>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_inicio">
							</div>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_fin">
							</div>
							<div class="col-md-12 input-group" style="padding: 5px 0;">
								<input type="text" class="form-control sin_margin" name="periodo" placeholder="Periodo" title="Periodo" required>
								<span class="input-group-addon pointer" style="background-color:white"><span class="fa fa-calendar red"></span> Periodo</span>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-danger active" id="generar_resultados" ><span class="fa fa-download"></span> Generar</button>
						<button type="button" class="btn btn-danger2 active" id="resetear_resultados" ><span class="fa fa-trash"></span> Resetear</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="modal fade con-scroll-modal" id="modal_evaluacion_resultados" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Resultados de Evaluación</span></h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive col-md-12" style="width: 100%" id='evaluaciones_resultados'>
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_evaluacion_resultados" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="6" class="nombre_tabla">TABLA RESULTADOS</td>
								</tr>
								<tr class="filaprincipal">
									<td>Método</td>
									<td>Funcionario</td>
									<td>Identificación</td>
									<td class="opciones_tbl_btn">Puntuación</td>
									<td>Valoración</td>
									<td class="opciones_tbl_btn">Acción</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
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

	<div class="modal fade" id="Modal_notificacion" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<form id="form_notificacion">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-bell"></span> Enviar Notificación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="funkyradio facturacion" >
								<div class="funkyradio-success">
									<input type="radio" id="rd_cal" name="vb_cal" value="1">
									<label for="rd_cal" title="Evaluaciones Calificadas"> Resultado</label>
								</div>
								<div class="funkyradio-danger">
									<input type="radio" id="rd_sin_cal" name="vb_cal" value="0" checked>
									<label for="rd_sin_cal" title="Evaluaciones sin Calificar"> Sin Resultado</label>
								</div>
							</div>
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" name='filtroperiodo' placeholder="Filtrar por periodo">
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
							<select name="id_estado" class="form-control id_estado">
								<option value="">Selccione Estado</option>
							</select>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_inicio">
							</div>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_fin">
							</div>
							<div class="col-md-12" style="padding: 0px;">
								<textarea name="mensaje" class="form-control" rows="3" placeholder="Mensaje"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="fa fa-send"></span> Enviar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="Modal_filtro" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<form id="form_filtro">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" name='filtro_periodo' placeholder="Filtrar por periodo">
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
							<select name="tipo" class="form-control inputt cbxtipo filtro">
								<option value="">Filtrar por Método de Evaluación</option>
							</select> 
							<select id="estado_filtro" name="estado" class="form-control inputt cbxestado filtro">
								<option value="">Filtrar por Estado</option>
							</select>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_i">
							</div>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_f">
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="fa fa-filter"></span> Generar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="Modal_informe" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<form id="form_informe">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-list"></span> Generar Informe</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" name='filtro_periodo' placeholder="Periodo">
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
							<select name="metodo" class="form-control inputt cbxmetodo">
								<option value="">Seleccione Método de Evaluación</option>
							</select> 
							<select name="tipo_informe" class="form-control inputt cbxinforme">
								<option value="">Seleccione Tipo de Informe</option>
							</select> 
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="fa fa-download"></span> Generar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- <div class="modal fade scroll-modal" id="modal_administrar_asignaciones" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Asignaciones</h3>
				</div>
				<div class="modal-body">
					<nav class="navbar navbar-default" id="menu_administrar_asignaciones">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
							    <li class="pointer personas"><a><span class="fa fa-users red"></span> Personas</a></li>
								<li class="pointer indicadores"><a><span class="fa fa-question red"></span> Indicadores</a></li>
							</ul>
						</div>
					</nav>

					<div class="asignacion_personas active row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<div class="form-group">
								<div class="input-group agro col-md-6">
									<input name="persona" type="hidden" id="input_sele">
									<span id="s_persona" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
									<span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>							
							<table id="tabla_asignacion_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="3">TABLA PERSONAS</th>
										<td class="sin-borde text-right border-left-none">
											<span type="button" class="btn btn-default add_persona oculto" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Nombre</td>
										<td>Identificación</td>
										<td>Periodo</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="asignacion_indicadores oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<div class="form-group">
								<div class="input-group agro col-md-6">
									<input name="persona_ind" type="hidden" id="input_sele_ind">
									<span id="s_persona_ind" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
									<span id="sele_perso_ind" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>
							<table id="tabla_asignacion_indicadores" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA INDICADORES</th>
										<td class="sin-borde text-right border-left-none">
											<span type="button" class="btn btn-default add_indicador oculto" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td> 
									</tr>
									<tr class="filaprincipal">
										<td>Descripción</td>
										<td>Periodo</td>
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
	</div> -->

	<div class="modal fade scroll-modal" id="modal_administrar" role="dialog">
		<div class="modal-dialog modal-95">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Módulo</h3>
				</div>
				<div class="modal-body">
					<nav class="navbar navbar-default" id="menu_administrar">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
							    <li class="pointer metodo_evaluacion"><a><span class="fa fa-puzzle-piece red"></span> Método</a></li>
								<li class="pointer tipo_evaluacion"><a><span class="fa fa-check-square red"></span> Tipo evaluador</a></li>
								<li class="pointer categoria"><a><span class="fa fa-crosshairs red"></span> Área Competencia</a></li>
								<li class="pointer pasos"><a><span class="fa fa-list red"></span> Competencias</a></li>
								<li class="pointer preguntas"><a><span class="fa fa-question red"></span> Preguntas</a></li>
							    <li class="pointer personas"><a><span class="fa fa-users red"></span> Personas</a></li>
							</ul>
						</div>
					</nav>

					<div class="metodo_evaluacion adm_proceso active row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_metodo_evaluacion" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA METODO EVALUACION</th>
										<td class="sin-borde text-right border-left-none" colspan="4">
											<span type="button" class="btn btn-default add_metodo_evaluacion" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td>Nombre</td>
										<td>Estado</td>
										<td>Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="tipo_evaluacion adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_tipo_evaluacion" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA TIPO EVALUADOR</th>
										<td class="sin-borde text-right border-left-none" colspan="3">
											<span type="button" class="btn btn-default add_tipo_evaluacion" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td style="width:60%">Nombre</td>
										<td>Peso %</td>
										<td>Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="categoria adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_categoria" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA AREAS DE COMPETENCIAS</th>
										<td class="sin-borde text-right border-left-none" colspan="3">
											<span type="button" class="btn btn-default add_categoria" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td style="width:60%">Nombre</td>
										<td>Estado</td>
										<td>Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="pasos adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_pasos" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA COMPETENCIAS</th>
										<td class="sin-borde text-right border-left-none" colspan="3">
											<span type="button" class="btn btn-default add_paso" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td style="width:60%">Nombre</td>
										<td>Estado</td>
										<td>Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="preguntas adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_preguntas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA PREGUNTAS</th>
										<td class="sin-borde text-right border-left-none" colspan="3">
											<span type="button" class="btn btn-default add_pregunta" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td> 
									</tr>
									<tr class="filaprincipal">
										<td>Ver</td>
										<td style="width:70%">Nombre</td>
										<td>Estado</td>
										<td>Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="asignacion_personas adm_proceso row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<div class="form-group">
								<div class="input-group agro col-md-6">
									<input name="persona" type="hidden" id="input_sele">
									<span id="s_persona" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
									<span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>							
							<table id="tabla_asignacion_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="3">TABLA PERSONAS</th>
										<td class="sin-borde text-right border-left-none">
											<span type="button" class="btn btn-default add_persona oculto" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Nombre</td>
										<td>Identificación</td>
										<td>Periodo</td>
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

	<div class="modal fade" id="modal_detalle_permiso" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Detalle</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive" >
						<table class="table table-bordered table-condensed">   
							<tr>
								<th class="nombre_tabla" colspan="8">Información del parametro</th>
							</tr> 
							<tr>
								<td class="ttitulo">Nombre</td>
								<td class="valor" colspan="7"></td>
							</tr>
							<tr>
								<td class="ttitulo">Descripción</td>
								<td class="valorx" colspan="7"></td>
							</tr>
							<tr>
								<td class="ttitulo"><span class="nombre_parametro">Valor Y</span></td>
								<td class="valory" colspan="3"></td>
								<td class="ttitulo">Tipo de Pregunta</td>
								<td class="valorz" colspan="3"></td>
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
  

	<div class="modal fade" id="modal_valor_parametro" role="dialog">
		<div class="modal-dialog">
			<form  action="#" id="form_valor_parametro" method="post">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-edit fa-lg"></span> Nuevo</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<h4>
								<span class="fa fa-pencil-square-o red"></span> Nombre
							</h4>
							<div class="col-md-12" style="padding: 0px;">
								<input type="text" class="form-control" name="valor" placeholder="Nombre" required maxlength="499">
							</div>
						</div>
						<div class="row valory">
							<h4>
								<span class="fa fa-pencil-square-o red"></span> <span class="nombre_parametro"></span>
							</h4>
							<div class="col-md-12" style="padding: 0px;">
								<input type="number" min='1' class="form-control" name="valory" placeholder="valor">
							</div>
						</div>
						<div class="row apreciacion">
							<h4>
								<span class="fa fa-pencil-square-o red"></span> Tipo Area de Apreciación
							</h4>
							<div class="col-md-12" style="padding: 0px;">
								<select name="area_apreciacion" class="form-control cbxarea_apreciacion" title="Area de Apreciación"><option value="">Seleccione Area de Apreciación</option></select>
							</div>
						</div>
						<div class="row valorz">
							<h4>
								<span class="fa fa-pencil-square-o red"></span> Tipo de Pregunta
							</h4>
							<div class="col-md-12" style="padding: 0px;">
								<select name="valorz" class="form-control cbxvalorz" title="Tipo de Pregunta"><option value="">Seleccione Tipo de Pregunta</option></select>
							</div>
						</div>
						<div class="row">
							<h4>
								<span class="fa fa-pencil-square-o red"></span> Descripción
							</h4>
							<div class="col-md-12" style="padding: 0px;">
								<textarea name="valorx" class="form-control" rows="5" placeholder="Descripción"></textarea>
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

	<div class="modal fade" id="ModalPermiso" role="dialog">
		<div class="modal-dialog modal-lg">        
			<!-- Modal content-->
			<div class="modal-content" >
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span>  Gestionar Permiso</h3>
				</div>
				<div class="modal-body" id="bodymodal">
				<input type="hidden" id="listado_parametros_permiso">
				    <div class="alert alert-success alertpeso oculto" role="alert">
						<b>Peso Porcentual acumulado: <span class="detalle_peso"></span></b> 
                    </div>
					<!--inicio de la tabla-->
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="tablapermisoparametro" cellspacing="0" width="100%">                                 
								<thead class="ttitulo ">
									<tr class="" ><td colspan="5" class="nombre_tabla"> tabla permiso</td></tr>                                    
									<tr class="filaprincipal"><td class="opciones_tbl">No.</td><td class="">Nombre</td><td class="">Peso %</td><td>***</td></tr>
								</thead>
								<tbody>
								</tbody>                                
							</table>

						</div>
					<!--fin-->
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>       
		</div>
	</div>

	<form id="form_nueva_asignacion" method="post">
		<div class="modal fade" id="modal_nueva_asignacion" role="dialog">
			<div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-user"></span> Nueva Asignación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="col-md-12 input-group" style="margin-bottom: 3px;">
								<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_persona" name="id_evaluado" placeholder="Buscar Persona" required readonly>
								<span class="input-group-addon pointer input_buscar_persona" style='background-color:white'>
									<span class='fa fa-search red'></span> Buscar
								</span>
							</div>
							<div class="agro agrupado">
								<div class="alert alert-info" role="alert">
									<p><b class="ttitulo">Datos del Evaluado:</b></p>
									<ul>
										<li>Nombre: <span class="nombre_completo"></span></li>
										<li>Identificación: <span class="identificacion"></span></li>
										<li>Cargo: <span class="cargo"></span></li>										
									</ul>
								</div>                            
							</div>
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" required="true" name='periodo_evaluado'>
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<form id="form_nueva_solicitud" method="post">
		<div class="modal fade" id="modal_nueva_solicitud" role="dialog">
			<div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-file-text"></span> Nueva Evaluación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="col-md-12 input-group" style="margin-bottom: 3px;">
								<input type="text" class="form-control sin_margin sin_focus pointer input_buscar_persona" name="evaluado" placeholder="Buscar Persona" required readonly>
								<span class="input-group-addon pointer input_buscar_persona" style='background-color:white'>
									<span class='fa fa-search red'></span> Buscar
								</span>
							</div>
							<div class="agro agrupado">
								<div class="alert alert-info" role="alert">
									<p><b class="ttitulo">Datos del Evaluado:</b></p>
									<ul>
										<li>Nombre: <span class="nombre_completo"></span></li>
										<li>Cargo: <span class="cargo"></span></li>
										<!-- <li>Departamento: <span class="departamento"></span></li> -->
									</ul>
								</div>                            
							</div>
							<select name="id_metodo" class="form-control cbxmetodo" title="Método de Evaluación" required="true"><option value="">Seleccione Método de Evaluación</option></select>
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_nombre_jefe'>
									<span class="input-group-addon pointer" id='btn_buscar_jefe' style='	background-color:white'><span class='fa fa-search red'></span> JEFE INMEDIATO</span>
								</div>
							</div>
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control sin_margin sin_focus" required="true" id='txt_nombre_coevaluado'>
									<span class="input-group-addon pointer" id='btn_buscar_coevaluado' style='	background-color:white'><span class='fa fa-search red'></span> COEVALUADO</span>
								</div>
							</div>
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" required="true" name='periodo'>
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
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

	<div class="modal fade" id="modal_detalle_evaluacion" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="alert alert-info oculto" role="alert" id="info_confirmacion">
						<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
						<p>Esta persona marco como recibido el acta de retroalimentación. Para ver su firma, <a href="<?php echo base_url()?>" target="_blank" id="btnfirma"><b>haga click aquí!.</b></a></p>
						<p><b>Observaciones</b>: <span class="observacion"></span><p>
						<p><b>Calificación</b>: <span class="calificacion"></span>&#9733;</p>
					</div> 
					<table class="table table-bordered table-condensed">
						<tr>
							<th class="nombre_tabla" colspan="2"> Información de la Solicitud</th>
							<td colspan="2" class="sin-borde text-right border-left-none">
							    <span class="btn btn-default btn_formacion"><span class="fa fa-address-card red"></span> Talento CUC</span> 
								<span class="btn btn-default btn_ver_resultado"><span class="fa fa-eye red"></span> Resultados</span>
								<a href="<?php echo base_url()?>" target="_blank" class="btn btn-default btn_ver_acta" id="btnActa"><span class="fa fa-folder-open-o red"></span> Retroalimentación</a>
							</td>
						</tr>
						<tr>
							<td class="ttitulo">Funcionario: </td>
							<td colspan='3' ><span class="info_funcionario"></span></td>
						</tr>
						<tr>
							<td class="ttitulo">Identificación: </td><td class="info_identificacion"></td>
							<td class="ttitulo">Fecha de Solicitud:</td><td class="info_fecha"></td>
						</tr>
						<tr>
							<!-- <td class="ttitulo">Dependencia:</td><td class="info_dependencia"></td> -->
							<td class="ttitulo">Cargo:</td><td class="info_cargo" colspan='3'></td>
						</tr>
						<tr>
							<td class="ttitulo">Método Evaluación:</td><td class="info_metodo"></td>
							<td class="ttitulo">Estado:</td><td class="info_estado"></td>
						</tr>
						<tr>
							<td class="ttitulo">Puntuación Directa:</td><td class="puntuacion_directa"></td>
							<td class="ttitulo">Puntuación Centil:</td><td class="puntuacion_centil"></td>
						</tr>
						<tr>
							<td class="ttitulo">Valoración: </td>
							<td colspan='3' ><span class="info_valoracion"></span></td>
						</tr>							
					</table>
					<table class="table table-bordered table-hover table-condensed pointer" id="tabla_tipoEvaluador" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr class="">
								<td colspan="4" class="nombre_tabla">TABLA TIPO EVALUADOR</td>
							</tr>
							<tr class="filaprincipal">
								<td>No.</td>
								<td>Tipo Evaluador</td>
								<td>Nombre Completo</td>
								<td class="opciones_tbl_btn">Acción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_cargo" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr class="">
								<td colspan="3" class="nombre_tabla">TABLA PERSONAL A CARGO</td>
								<td class="sin-borde text-right border-left-none">
									<button class="btn btn-default oculto" id="agregar_pesonal"><span class="fa fa-plus red"></span> Agregar</button>
								</td> 
							</tr>
							<tr class="filaprincipal">
								<td>No.</td>
								<td>Nombre Completo</td>
								<td>Identificación</td>
								<td class="opciones_tbl_btn">Acción</td>
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

	<div class="modal fade con-scroll-modal" id="modal_plan_formacion" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Formación por Competencias</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="margin:0px;width:100%;">
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_planformacion" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="3" class="nombre_tabla">TABLA COMPETENCIAS</td>
										<td colspan="3" class="sin-borde text-right border-left-none">
											<span class="btn btn-default btn_entrenamiento"><span class="fa fa-cogs red"></span> Plan de Entrenamiento</span>
										</td>		
									</tr>
									<tr class="filaprincipal">
										<td>Area de Apreciación</td>
										<td>Competencia</td>
										<td>Observaciones</td>
										<td class="opciones_tbl_btn">Puntaje</td>
										<td class="opciones_tbl_btn">Horas</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade con-scroll-modal" id="modal_listar_plan_entrenamiento" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Plan de Entrenamiento</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="margin:0px;width:100%;">
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_entrenamiento" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="5" class="nombre_tabla">TABLA PLAN DE ENTRENAMIENTO</td>	
									</tr>
									<tr class="filaprincipal">
										<td>Etapa</td>
										<td>Facilitador</td>
										<td>Duracion</td>
										<td>Lugar</td>
										<td>Fecha</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_detalle_planformacion" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-address-book"></span> Detalle Plan de Formación</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive" >
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_pformacion_persona" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="7" class="nombre_tabla">TABLA PLAN DE FORMACION</td>	
								</tr>
								<tr class="filaprincipal">
									<td>Tema</td>
									<td>Facilitador</td>
									<td>Duracion</td>
									<td>Lugar</td>
									<td>Fecha</td>
									<td>Competencia</td>
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

	<div class="modal fade con-scroll-modal" id="modal_evaluacion_respuestas" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-question"></span> <span class='nombre_evaluacion'>Evaluación</span></h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive col-md-12" style="width: 100%" id='evaluaciones'>
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_evaluacion_respuestas" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="4" class="nombre_tabla">TABLA PREGUNTAS</td>
								</tr>
								<tr class="filaprincipal">
									<td style="width:50%;">Pregunta</td>
									<td>Area Apreciación</td>
									<td>Competencia</td>
									<td>Respuesta</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
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

	<div class="modal fade scroll-modal" id="modal_personal_acargo" role="dialog">
		<div class="modal-dialog modal-95">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Evaluación e Indicadores</h3>
				</div>
				<div class="modal-body">
					<nav class="navbar navbar-default" id="menu_admin_indicadores">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
							    <li class="pointer evaluacion active"><a><span class="fa fa-question red"></span> Evaluación</a></li>
								<li class="pointer indicadores"><a><span class="fa fa-question-circle red"></span> Indicadores</a></li>
								<li class="pointer formacion_Esc"><a><span class="fa fa-certificate red"></span> Formación Esencial</a></li>
								<li class="pointer funciones"><a><span class="fa fa-cog red"></span> Funciones</a></li>
							</ul>
						</div>
					</nav>

					<div class="preguntas oculto row resp" style="margin:0px;width:100%;">
						<div class="col-md-12">
							<table id="tabla_preguntas_evaluacion" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="3">TABLA PREGUNTAS</th>
									</tr>
									<tr class="filaprincipal">
										<td style="width:50%;">Pregunta</td>
										<td>Area Apreciación</td>
										<td>Competencia</td>
										<td>Respuesta</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="preguntas_indicadores oculto row resp" style="margin:0px;width:100%;">
						<div class="col-md-12">
							<table id="tabla_preguntas_indicadores" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA PREGUNTAS</th> 
									</tr>
									<tr class="filaprincipal">
										<td>Pregunta</td>
										<td></td>
										<td></td>
										<td>Respuesta / % Cumplimiento</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="preguntas_formacion_Esc oculto row resp" style="margin:0px;width:100%;">
						<div class="col-md-12">
							<table id="tabla_formacion_Esc" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA PREGUNTAS</th> 
									</tr>
									<tr class="filaprincipal">
										<td>Pregunta</td>
										<td>Respuesta</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="preguntas_funciones oculto row resp" style="margin:0px;width:100%;">
						<div class="col-md-12">
							<table id="tabla_funciones" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA PREGUNTAS</th> 
									</tr>
									<tr class="filaprincipal">
										<td>Pregunta</td>
										<td>Respuesta</td>
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

	<div class="modal fade scroll-modal" id="modal_resultados" role="dialog">
		<div class="modal-dialog modal-95">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Resultados Evaluación</h3>
				</div>
				<div class="modal-body">
					<nav class="navbar navbar-default" id="menu_resultados">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
							    <li class="pointer detalles active"><a><span class="fa fa-list red"></span> Area Apreciación</a></li>
								<li class="pointer tipoEvaluador"><a><span class="fa fa-list red"></span> Tipo Evaluador</a></li>
							</ul>
						</div>
					</nav>

					<div class="resultado_detalle active row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_resultado_detalles" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="8">TABLA PREGUNTAS</th>
									</tr>
									<tr class="filaprincipal">										
										<td>Evaluador</td>
										<td>Identificación</td>
										<td>Area Apreciación</td>
										<td>Acumulado</td>
										<td>Total Preguntas</td>
										<td>Promedio</td>
										<td>% Apreciación</td>
										<td>% Evaluador</td>
										<td>Resultado</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="resultado_tevaluador oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<table id="tabla_resultados_tevaluador" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="4">TABLA PREGUNTAS</th> 
									</tr>
									<tr class="filaprincipal">
										<td>Evaluador</td>
										<td>%</td>
										<td>Acumulado</td>
										<td>Producto</td>
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

	  <form  id="form_buscar_persona"  method="post">
		<div class="modal fade" id="modal_buscar_persona" role="dialog">
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
									<input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit">
											<span class='fa fa-search red'></span> Buscar
										</button>
									</span>
								</div>
							</div>
							<div class="table-responsive col-md-12" style="width: 100%">
								<table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda" cellspacing="0" width="100%">
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
	
	<div class="modal fade" id="Modal_desc_reporte" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<form id="form_desc_reporte">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-download"></span> Descargar Reporte</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" name='filtro_periodo' placeholder="Filtrar por periodo">
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
							<select name="tipo" class="form-control inputt cbxtipo filtro">
								<option value="">Filtrar por Método de Evaluación</option>
							</select> 
							<select id="estado_filtro" name="estado" class="form-control inputt cbxestado filtro">
								<option value="">Filtrar por Estado</option>
							</select>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_i">
							</div>
							<div class="col-md-6 sol-sm-12" style="padding: 0 0;">
								<input class="form-control filtro" type="date" name="fecha_f">
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="fa fa-filter"></span> Generar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

</div>

<script>
    $(document).ready(function () {
		inactivityTime();
		listar_solicitudes();
		Cargar_parametro_buscado(219, ".cbxvalorz", "Seleccione Tipo de Pregunta");
		Cargar_parametro_buscado_aux(214, ".cbxtipo", "Filtrar por Método de Evaluación");
		Cargar_parametro_buscado_aux(214, ".cbxmetodo", "Selecciona Método de Evaluación");
		Cargar_parametro_buscado_aux(221, ".cbxestado", "Filtrar por Estado");
		Cargar_parametro_buscado_aux(221, ".id_estado", "Seleccione Estado");
		Cargar_parametro_buscado(223, ".cbxarea_apreciacion", "Seleccione Area de Apreciación");
		Cargar_parametro_buscado(115, ".cbxlugares", "Seleccione Lugar");
		Cargar_parametro_buscado_aux(250, ".cbxinforme", "Seleccione Tipo Informe");
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
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
