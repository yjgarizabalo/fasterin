<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com") {
	$sw = true;
	$sw_super = true;
}
if ($_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm") {
	$sw = true;
}

$show = false;

?>
<style>
	.colum_large {
		width: 80% !important;
	}

	.options_colum {
		width: 7% !important;
	}
</style>
<div class="listado_solicitudes <?php if (!$sw) echo 'oculto'; ?> ">
	<div id="menu">


	</div>
</div>

<div class="container col-md-12 text-center" id="inicio-user">
	<div class="tablausu listado_solicitudes col-md-12 text-left <?php if (!$sw) echo 'oculto'; ?>">
		<div class="table-responsive col-sm-12 col-md-12  tablauser">
			<p class="titulo_menu pointer" id="regresar_add"><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr class="">
						<td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA SOLICITUDES DE COMPRA <br><span class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
						<td class="sin-borde text-right border-left-none" colspan="6">
							<?php if ($sw) echo '<span class="btn btn-default" id="ver_notificaciones"  ><span class="badge btn-danger n_notificaciones"></span> Notificaciones</span>'; ?>
							<!-- Masivos -->
							<span class="btn btn-default" id="do_massives" title="Realizar masivos">
								<span class="fa fa-cubes red"></span> Masivos
							</span>
							<!-- Masivos -->
							<?php if ($sw_super || $permiso) echo '<span class="btn btn-default btnAgregar" id="admin_solicitudes"><span class="fa fa-cogs red"></span> Administrar</span> <span  class="btn btn-default btnModifica" id="modificar_solicitud_ini"><span class="fa fa-wrench red"></span> Modificar</span>'; ?>
							<span class="btn btn-default" id="filtrar_solic_compra" title="Filtrar" data-toggle="modal" data-target="#Modal_filtrar_compras">
								<span class="fa fa-filter red"></span> Filtrar
							</span>
							<span class="btn btn-default" id="limpiar_filtros_compras">
								<span class="fa fa-refresh red"></span> Limpiar
							</span>
							<?php if ($sw_super || $permiso) echo '<span class="btn btn-default btngen" id="generar_promedios"><span class="fa fa-info-circle red" style="font-size: large;"></span> Generar Promedios</span>'; ?>
						</td>
					</tr>
					<tr class="filaprincipal ">
						<td class="opciones_tbl">Ver</td>
						<td>Tipo</td>
						<td>Solicitante</td>
						<td>Fecha Solicitud</td>
						<td class="opciones_tbl">No.</td>
						<td>#Orden</td>
						<td>Fecha Entrega</td>
						<td>Estado</td>
						<td class="options_colum">Acción</td>
						<td>Pregunta 1</td>
						<td>Pregunta 2</td>
						<td>Pregunta 3</td>
						<td>Observaciones Calificación</td>
						<td>Proveedor</td>
						<td>Reevaluación de Proveedor</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<div class="tablausu col-md-12 <?php if ($sw) echo 'oculto'; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
		<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
		</div>

		<div id="container-principal2" class="container-principal-alt">
			<h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>

			<div class="row">



				<div id="nueva_solicitud" class="pointer">
					<div class="thumbnail">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
							<span class="btn form-control">Nueva Solicitud</span>
						</div>
					</div>
				</div>


				<div class="" id="listado">
					<div class="thumbnail ">
						<div class="caption">
							<img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
							<span class="btn form-control">Estados Solicitudes</span>
						</div>


					</div>
				</div>

			</div>
			<p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
		</div>
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
				<nav class="navbar navbar-default" id="nav_admin_compras">
					<div class="container-fluid">
						<ul class="nav navbar-nav">
							<?php if ($sw_super) { ?><li class="pointer" id="admin_permisos"><a><span class="fa fa-check red"></span> Permisos</a></li> <?php } ?>
							<?php if (($sw_super || $permiso->solicitudes) && $show) { ?><li class="pointer " id="admin_responsables"><a><span class="fa fa-pencil-square-o red"></span> Solicitudes</a></li> <?php } ?>
							<?php if ($sw_super || $permiso->comite) { ?><li class="pointer" id="admin_comite"><a><span class="fa fa-folder red"></span> Comité</a></li> <?php } ?>
							<?php if ($sw_super || $permiso->proveedores) { ?><li class="pointer" id="admin_proveedor"><a><span class="fa fa-truck red"></span> Proveedor</a></li> <?php } ?>
							<?php if ($sw_super || $permiso->proveedores) { ?><li class="pointer" id="admin_criterios"><a><span class="fa fa-puzzle-piece red"></span> Criterios</a></li> <?php } ?>
							<?php if ($sw_super || $permiso->proveedores) { ?><li class="pointer" id="admin_ponderados"><a><span class="fa fa-check-square red"></span> Ponderados</a></li> <?php } ?>
							<?php if ($sw_super) { ?><li class="pointer" id="admin_encuestas_pendientes"><a><span class="fa fa-check-square-o red"></span> Encuestas Pendientes</a></li> <?php } ?>
						</ul>
					</div>
				</nav>
				<div class="table-responsive">
					<div id="container_admin_respo">
						<?php if ($show) { ?>
							<table class="table table-bordered table-hover table-condensed" id="tabla_responsables_proc" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th colspan="4" class="nombre_tabla">TABLA RESPONSABLES</th>
									</tr>
									<tr class="filaprincipal ">
										<td>Tipo</td>
										<td>Nombre</td>
										<td>Identificación</td>
										<td>Correo</td>
										<td class="opciones_tbl">Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						<?php } ?>
					</div>

					<div id="container_admin_provedor" class="oculto">
						<table class="table table-bordered table-hover table-condensed" id="tabla_proveedores" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th class="nombre_tabla" style="vertical-align: text-top;">TABLA PROVEEDORES</th>
									<td class="btnAgregar sin-borde text-right border-left-none" style="vertical-align: text-top;" colspan="2">
										<span class="btn btn-default" id="filtrar_promedio_provs" title="Filtrar" data-toggle="modal" data-target="#modal_filtrar_proveedores">
											<span class="fa fa-filter red"></span> Generar
										</span>
										<span data-toggle="modal" data-target="#ValorParmetro" class="btn btn-default" title="Agregar Proveedor" data-toggle="popover" data-trigger="hover">
											<span class="fa fa-plus red"></span> Agregar
										</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Nombre</td>
									<td>Descripción</td>
									<td class="opciones_tbl">Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<!-- Admin comite -->
					<div id="container_admin_comite" class="oculto">
						<table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="5" class="nombre_tabla">TABLA COMITÉ</th>
									<td class="btnAgregar sin-borde text-center"><span data-toggle="modal" data-target="#comiteadd" class="fa fa-plus pointer" title="Agregar Comité" data-toggle="popover" data-trigger="hover"></span></td>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">Ver</td>
									<td>Nombre</td>
									<td>Descripción</td>
									<td>#Soli.</td>
									<td>Estado</td>
									<td class="opciones_tbl_btn">Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<!-- Admin ponderados -->
					<div id="container_admin_ponderados" class="oculto">
						<table class="table table-bordered table-hover table-condensed" id="tabla_mostrar_ponderados" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<th colspan="1" class="nombre_tabla" style="vertical-align:middle;">TABLA DE PONDERADOS</th>
									<td class="sin-borde text-right border-left-none" colspan="4" rowspan="1">
										<span type="button" class="btn btn-default create_porcentaje" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Valor Inicial</td>
									<td>Valor Final</td>
									<td>Porcentaje</td>
									<td>Acciones</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<div id="container_admin_permisos">
						<table class="table table-bordered table-hover table-condensed" id="tabla_personas_compras" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="4" class="nombre_tabla">TABLA PERSONAS PERMISO</th>
								</tr>
								<div class="input-group agro col-md-8">
									<input name="usuario_buscar" type="hidden" id="input_usuario_buscar">
									<input type="text" class="form-control inputt2" name="usuario_buscar" placeholder="Buscar persona." id="txtusuario_buscar" autocomplete="off">
									<span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_usuario" title="Buscar persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
								<tr class="filaprincipal ">
									<td>Nombre</td>
									<td>Identificación</td>
									<td>Correo</td>
									<td>Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<!-- Admin encuestas por areas -->
					<?php if ($show) { ?>
						<div id="container_mostrar_areas" class="oculto">
							<table class="table table-bordered table-hover table-condensed" id="tabla_mostrar_areas" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th colspan="1" class="nombre_tabla">TABLA DE ÁREAS</th>
									</tr>
									<tr class="filaprincipal ">
										<td style="width: 10px;">Areas</td>
										<td class="opciones_tbl">Acciones</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					<?php } ?>

					<div id="container_mostrar_criterios" class="oculto">
						<table class="table table-bordered table-hover table-condensed" id="tabla_mostrar_criterios" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="3" class="nombre_tabla" style="vertical-align: middle;">TABLA DE CRITERIOS</th>
									<td class="sin-borde text-right border-left-none" colspan="4" rowspan="1">
										<span type="button" class="btn btn-default add_criterio" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Nombre</td>
									<td>Estado</td>
									<td style="width: 18% !important;">Accion</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>

					<!-- Admin solicitudes encuestas pendientes -->
					<div id="container_admin_encuestas_pendientes" class="oculto">
						<table class="table table-bordered table-hover table-condensed" id="tabla_pers_encuestas_pendientes" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<th colspan="4" class="nombre_tabla" style="vertical-align:middle;">TABLA DE PERSONAS CON ENCUESTAS PENDIENTES</th>
								</tr>
								<tr class="filaprincipal ">
									<td>Ver</td>
									<td>Solicitante</td>
									<td>No. de Solicitudes</td>
									<td>Accion</td>
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

<!-- Modal para mostrar los promedios de los proveedores -->
<div class="modal fade" id="modal_encuestas_pendientes" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-bar-chart"></span> Solicitudes con encuestas pendientes</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_encuestas_pendientes" cellspacing="0" width="100%">
						<thead class="ttitulo">
							<tr>
								<th colspan="4" class="nombre_tabla" style="vertical-align:middle;">TABLA DE SOLICITUDES</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Tipo</td>
								<td>Solicitante</td>
								<td>Fecha Solicitud</td>
								<td>No.</td>
								<td>#Orden</td>
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

<!-- Modal para mostrar los promedios de los proveedores -->
<div class="modal fade" id="modal_promedios_proveedores" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-bar-chart"></span> Calificación total de Proveedores</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_promedios_proveedores" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="6" style="vertical-align: middle;" class="nombre_tabla">
									TABLA DE PROMEDIOS - PROVEEDORES
									<br>
									<span class="mensaje-filtro oculto" style="display: inline-block;"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span>
								</td>
								<td colspan="2" class="sin-borde text-right border-left-none">
									<span class="btn btn-default" id="filtrar_proms_provs" title="Filtrar" data-toggle="modal" data-target="#modal_filtrar_proms">
										<span class="fa fa-filter red"></span> Filtrar
									</span>
									<span class="btn btn-default" id="clear_results" title="Limpiar Filtros">
										<span class="fa fa-refresh red"></span> Limpiar
									</span>
								</td>
							</tr>
							<tr class="filaprincipal ">
								<td style="width: 10px;">Proveedor</td>
								<td class="opciones_tbl">Solicitudes</td>
								<td class="opciones_tbl">Tiempo de entrega</td>
								<td class="opciones_tbl">SGA</td>
								<td class="opciones_tbl">Materiales</td>
								<td class="opciones_tbl">Servicio</td>
								<td class="opciones_tbl">SST</td>
								<td class="opciones_tbl">Promedio Total</td>
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

<!-- Modal para mostrar los promedios de los proveedores -->
<div class="modal fade" id="modal_solicitudes_promedios" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-bar-chart"></span> SOLICITUDES - PROMEDIOS PROVEEDORES</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_solicitudes_promedios" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="5" style="vertical-align: middle;" class="nombre_tabla">
									TABLA SOLICITUDES DE <span class="persona"></span>
									<br>
									<span class="mensaje-filtro oculto" style="display: inline-block;"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span>
								</td>
							</tr>
							<tr class="filaprincipal ">
								<td style="width: 10px;">Ver</td>
								<td class="opciones_tbl">Solicitante</td>
								<td class="opciones_tbl">Tipo</td>
								<td class="opciones_tbl">Fecha Solicitud</td>
								<td class="opciones_tbl">#Orden</td>
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

<!-- Modal para filtrar promedios totales de proveedores segun fechas -->
<form action="" name="form_promedios_provs" id="form_promedios_provs">
	<div class="modal fade" id="modal_filtrar_proms" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="alert alert-info oculto" id="alerta_faltantes" role="alert">
							<h4 class="text-center"> ¡Aviso! </h4>
							<p>En el rango de fechas seleccionado, hay encuestas pendientes por realizar.</p>
							<br>
							<div id="provs_restantes_conatiner">
								<!-- Auto gen -->
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
								<input class="form-control sin_margin" value="" type="date" name="fecha_ini" id="fecha_ini">
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
								<input class="form-control sin_margin" value="" type="date" name="fecha_fin" id="fecha_fin">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="btngenerarr"><span class="glyphicon glyphicon-ok"></span> Generar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal para mostrar los proveedores con falta de calificacion -->
<div class="modal fade" id="modal_rp_faltante" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-puzzle-piece"></span> Reevaluación de proveedores faltantes</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_rp_faltantes" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<td colspan="7" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
							</tr>
							<tr class="filaprincipal ">
								<td>Nº de solicitud</td>
								<td>Solicitante o Responsable</td>
								<td>Tipo de compra</td>
								<td>Fecha de solicitud</td>
								<td>Tipo de orden</td>
								<td>Ambiental (SGA)</td>
								<td>Seguridad y salud (SST)</td>
								<td>Material/Servicio</td>
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

<!-- Modal para realizar encuestas masivas AQUII -->
<form action="" id="form_masivos" name="form_masivos">
	<div class="modal fade" id="modal_masivos_compras" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-puzzle-piece"></span> Realizar encuestas masivas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_rp_massives" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<td colspan="3" style="vertical-align: middle;" class="nombre_tabla">
										TABLA DE RESULTADOS <br>
										<span style="font-size: 12px;	color: #d57e1c" class="filtro_msg oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span>
									</td>
									<td colspan="3" style="border: none; border-collapse: collapse; text-align: right;">
										<select class="form-control" name="massives_prov" id="massives_prov">
											<option value="">Filtrar por proveedor...</option>
										</select>
										<select class="form-control" name="massives_encs" id="massives_encs">
											<option value="">Filtrar por tipo de encuesta...</option>
										</select>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td class="options_colum">Ver</td>
									<td style="width:30% !important;">Solicitante</td>
									<td style="width:20% !important;">Proveedor</td>
									<td>Tipo de orden</td>
									<td>Fecha Registra</td>
									<td class="options_colum">Nº de solicitud</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="btnmasive"><span class="glyphicon glyphicon-ok"></span> Calificar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal para mostrar los detalles de las solicitudes, mostrar los articulos de esa solicitud -->
<div class="modal fade con-scroll-modal" id="modal_detalles_masivos" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-calendar-check-o "></span> Detalles de la solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_articulos_masivos" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="2" class="nombre_tabla">TABLA ARTÍCULOS Y/O SERVICIOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Artículo y/o Servicio</td>
								<td>Cantidad</td>
								<td>Cod.SAP</td>
								<td>Fecha registra</td>
								<td>Observaciones</td>
							</tr>
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

<!-- Modal mostrar contenido de gestionar encuestas. -->
<div class="modal fade" id="modal_mostrar_areas" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Preguntas de encuesta</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_mostrar_areas" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<th colspan="1" class="nombre_tabla">TABLA DE ÁREAS</th>
						</tr>
						<tr class="filaprincipal ">
							<td class="colum_large">Areas</td>
							<td class="option_colum">Acciones</td>
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

<!-- Modal mostrar solicitudes -->
<div class="modal fade" id="modal_mostrar_solicitudes" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Preguntas de encuesta</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_responsables_proc" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<th colspan="4" class="nombre_tabla">TABLA RESPONSABLES</th>
						</tr>
						<tr class="filaprincipal ">
							<td>Tipo</td>
							<td>Nombre</td>
							<td>Identificación</td>
							<td>Correo</td>
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

<!-- Modal mostrar preguntas segun area. -->
<div class="modal fade" id="Modal_Mostrar_Preguntas" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Preguntas de encuesta</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_mostrar_preguntas" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="1" style="vertical-align: middle;" class="nombre_tabla">TABLA DE PREGUNTAS</th>
								<th class="btnAgregar sin-borde text-center">
									<button class="btn btn-default" data-toggle="modal" data-target="#modal_agregar_pregunta_encuesta" class="btn-Efecto-men" title="Agregar pregunta!" data-toggle="popover" data-trigger="hover">
										<span class="fa fa-plus pointer red"></span>
										<span>Agregar</span>
									</button>
								</th>
							</tr>
							<tr class="filaprincipal ">
								<td class="colum_large">Pregunta</td>
								<td class="options_colum">Acción</td>
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
<!-- Fin del Modal mostrar preguntas segun area -->

<!-- Modal mostrar detalle del criterio seleccionado -->
<div class="modal fade" id="modal_detalle_permiso" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
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
							<td class="ttitulo">Porcentaje asignado</td>
							<td class="porcentaje" colspan="7"></td>
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

<!-- Modal para la asignacion de encuestas a usuarios -->
<div class="modal fade" id="modal_asignar_personas_encuestas" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Asignar usuarios</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_personas_RP" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">TABLA PERSONAS - REEVALUACIÓN DE PROVEEDORES</th>
							</tr>
							<div class="input-group agro col-md-8">
								<input name="search_user" type="hidden" id="input_search_user">
								<input type="text" class="form-control inputt2" name="search_user" placeholder="Buscar persona." id="txtsearch_user" autocomplete="off">
								<span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_user" title="Buscar persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
							</div>
							<tr class="filaprincipal ">
								<td>Nombre</td>
								<td>Identificación</td>
								<td>Correo</td>
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

<!-- Modal para gestionar procentajes de criterios de evaluacion -->
<div class="modal fade" id="ModalPermiso" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-cogs"></span> Gestionar Permiso</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="alert alert-success alertpeso_c" role="alert">
					<b>Peso Porcentual acumulado: <span class="detalle_peso"></span></b>
				</div>
				<!--inicio de la tabla-->
				<div class="table-responsive">
					<table class="table table-bordered table-hover" id="tablapermisoparametro" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr class="">
								<td colspan="5" class="nombre_tabla"> tabla permiso</td>
							</tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">No.</td>
								<td class="">Nombre</td>
								<td class="">Peso %</td>
								<td>***</td>
							</tr>
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

<!-- Modal para editar los criterios seleccionados -->
<div class="modal fade" id="modal_valor_parametro" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="form_valor_parametro" method="post">
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
							<input type="text" class="form-control" id="valor" name="valor" placeholder="Nombre" required maxlength="499">
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-pencil-square-o red"></span> Descripción
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<textarea id="valorx" name="valorx" class="form-control" rows="5" placeholder="Descripción"></textarea>
						</div>
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

<!-- Modal para modificar porcentajes -->
<div class="modal fade" id="modal_valor_porcentaje" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="form_valor_porcentaje" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit fa-lg"></span> Actualizar Porcentajes</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<h4>
							<span class="fa fa-pencil-square-o red"></span> Valor inicial
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<input type="text" class="form-control" id="valor_ini" name="valor_ini" placeholder="Valor inicial" required maxlength="499">
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-pencil-square-o red"></span> Valor final
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<input type="text" id="valor_fin" name="valor_fin" class="form-control" rows="5" placeholder="Descripción">
						</div>
					</div>
					<div class="row">
						<h4>
							<span class="fa fa-pencil-square-o red"></span> Porcentaje
						</h4>
						<div class="col-md-12" style="padding: 0px;">
							<input type="text" id="porcentaje" name="porcentaje" class="form-control" rows="5" placeholder="Porcentaje">
						</div>
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

<!-- Otro modal -->
<div class="modal fade" id="Modal_solicitudes_por_comite" role="dialog">
	<div class="modal-dialog modal-lg modal-95">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Solicitudes Asignadas</h3>
			</div>
			<div class="modal-body" id="bodymodal">

				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_solicitudes_comite" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA SOLICITUDES</th>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">Ver</td>
								<td>No.</td>
								<td>Solicitante</td>
								<td>Descripción</td>
								<td>Observaciones</td>
								<td>#Aprobados</td>
								<td>Acción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active" id="btn_imprimir_acta"><span class="fa fa-print"></span> Imprimir</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>


<!-- Filtrar que esta en listar solicitudes -->
<div class="modal fade" id="Modal_filtrar_compras" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row">

					<select name="" class="form-control inputt cbxtipocompra" id="tipo_compra_filtro">
						<option value="">Filtrar Solicitudes por Tipo</option>
					</select>
					<select name="" class="form-control inputt cbxestado" id="estado_filtro">
						<option value="">Filtrar Solicitudes por Estado</option>
					</select>
					<select name="proveedor" class="form-control inputt cbxproveedores sin_margin" id="cbxproveedores_ordenn">
						<option value="">Seleccione Proveedor</option>
					</select>
					<div class="agro agrupado">
						<div class="input-group">
							<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
							<input class="form-control sin_margin" value="" type="date" name="fecha_filtro" id="fecha_filtro">
						</div>
					</div>
					<div class="agro agrupado">
						<div class="input-group">
							<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
							<input class="form-control sin_margin" value="" type="date" name="fecha_filtro_2" id="fecha_filtro_2">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active" id="btnreporte"><span class="glyphicon glyphicon-ok"></span> Generar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="Modal_listar_proveedores_articulo" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"> <span class="fa fa-folder-open"></span> Solicitud en Comité</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="div_negados"></div>
				<div class="table-responsive">

					<table class="table table-bordered table-condensed" id="">
						<tr class="">
							<th class="nombre_tabla"> Información General</th>
							<th class="text-right btnModifica"> <span id="btnmodificar_sol_comit" class="pointer active btn-Efecto-men red" title="Modificar" data-toggle="popover" data-trigger="hover"><span class="btn-Efecto-men fa fa-wrench"></span></span></th>
						</tr>
						<tr>
							<td class="ttitulo">Comité: </td>
							<td class="valor_comite"></td>
						</tr>
						<!-- <tr class=""><td class="ttitulo">Cierre Comite: </td><td  class="valor_fecha_cierre"></td></tr>-->
						<tr class="">
							<td class="ttitulo">Descripción: </td>
							<td class="valor_descripcion_cmt"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Observaciones: </td>
							<td class="valor_observaciones_cmt"></td>
						</tr>
					</table>

					<table class="table table-bordered table-hover table-condensed " id="tabla_proveedores_articulo" cellspacing="0" width="100%">
						<thead class=" ">
							<tr>
								<th colspan="6" class="nombre_tabla">TABLA PROVEEDORES</th>
								<th class="btnAgregar sin-borde text-center"><span data-toggle="modal" data-target="#Modal_asignar_proveedor_articulo" class="btn-Efecto-men" title="Agregar Proveedor" data-toggle="popover" data-trigger="hover"><span class="fa fa-plus pointer red"></span></span></th>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">Ver</td>
								<td>Nombre</td>
								<td>$Pesos</td>
								<td>$Dolar</td>
								<td>Aprobados</td>
								<td>Sugeridos</td>
								<td class="opciones_tbl">Acción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div style="width: 100%" class="list-group margin1 panel_comentarios_formato_2"></div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active" id="terminar_comite"><span class="glyphicon glyphicon-ok"></span> Terminar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>

	</div>
</div>


<div class="modal fade" id="Modal_compra_negada" role="dialog">

	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-cart-arrow-down"></span> Compra Negada</h3>
			</div>
			<div class="modal-body" id="bodymodal">

				<div class="table-responsive" style="width: 100%">
					<table class="table table-bordered table-hover table-condensed" id="tabla_negados_compra" cellspacing="0" width="100%">
						<thead class="ttitulo ">

							<tr class="">
								<td colspan="4" class="nombre_tabla">Tabla Personas</td>
							</tr>
							<tr class="filaprincipal">
								<td>Persona</td>
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

<?php if ($sw) { ?>
	<form action="#" id="gestionar_solicitud">
		<div class="modal fade" id="Modal_gestionar_solicitud" role="dialog">
			<div class="fixed-comentarios pointer" style="padding-left: 80%">
				<div class="reque">
					<div class="login-container">
						<table class="" id="" style="width: 100%">
							<thead class="">
								<tr class="filaprincipal">
									<td colspan="" class="nombre_tabla"> INGRESE COMENTARIO</td>
									<td title="Cerrar Panel" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn cerrar_comentarios"></td>
								</tr>
							</thead>
						</table>
						<div class="form-boxw text-left">
							<textarea name="" id="comentario_directo_1" cols="20" rows="5" class="form-control sin_margin comentarios" placeholder="Redactar"></textarea>
							<div class="input-group agro">
								<span class="input-group-addon pointer fondo-red  active" id="comentar_directo_1"><span class="fa fa-send"> </span> Enviar</span>
								<span class="input-group-addon pointer active listar_comentarios_directos" id=""><span class="fa fa-list"></span> Listar</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-retweet"></span> Gestionar Solicitud</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="form-group text-right">
								<span class="badge btn-danger pointer conadjuntos_gestion">Adjuntar</span>
								<span class="badge  pointer ver_comentarios">Comentar</span>
							</div>
							<select id="estados_siguientes" class="form-control" required="true" name="estado">
								<option value="">Seleccione Estado</option>
							</select>
							<div id="tipo_compra_asi" class="oculto">
								<select name="tipo_compra" required class="form-control inputt cbxtipocompra" required>
									<option>Seleccione Tipo Compra</option>
								</select>
							</div>
							<div id="campo_or_comp" class="oculto">
								<input type="text" name="orden_comp" class="form-control" placeholder="No. orden compra" required="true">
								<select require="true" name="id_tipo_orden" class="form-control inputt cbx_tipo_orden" id="cbx_tipo_orden">
									<option value="">Seleccione Tipo de orden</option>
								</select>
								<div class="input-group agro">
									<select require="true" name="proveedor" class="form-control inputt cbxproveedores sin_margin" id="cbxproveedores_orden">
										<option value="">Seleccione Proveedor</option>
									</select>
									<span class="input-group-addon  red_primari pointer btn-Efecto-men" data-toggle="modal" data-target="#ValorParmetro" class="fa fa-plus pointer" title="Agregar Proveedor" data-toggle="popover" data-trigger="hover"><span class="fa fa-plus red"></span></span>
								</div>
								<input type="number" require="true" name="fecha_entrega_est" class="form-control" placeholder="Días Entrega estimados" step="1" min="1">
								<div class="agro agrupado">
									<select class="form-control" require="true" name="clasi_proveedor" class="form-control inputt clasi_proveedor sin_margin" id="clasi_proveedor">
										<option value="">Clasificación de Proveedores</option>
									</select>
								</div>
								<div class="ago agrupado">
									<select id="seleccion_area" class="form-control oculto" name="seleccion_area">
										<option value="">Seleccione Área</option>
									</select>
									<hr>
								</div>
							</div>
							<div id="campo_comi" class="oculto">
								<select id="comites_compras" class="form-control comites_compras" required="true" name="comite">
									<option value="">Seleccione Comité</option>
								</select>
								<textarea name="descripcion" class="form-control" placeholder="Descripcion" required id="descripcion_cmt"></textarea>
								<textarea name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
							</div>
							<div id="causal_dev" class="oculto">
								<select name="causal_compra" id="causal_compra" required class="form-control inputt cbxcausalcompra" required>
									<option>Seleccione Causal de Devolucion</option>
								</select>
							</div>
							<div id="campo_par" class="oculto">
								<hr>
								<span class="pointer" id="gestionar_entregas_par"><span class="fa fa-calendar-check-o red"></span> Gestionar Entregas</span>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btngestionar"><span class="glyphicon glyphicon-ok"></span> Aceptar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade con-scroll-modal" id="Modal_Entregas_parciales" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">

						<button type="button" class="close" data-dismiss="modal"> X </button>
						<h3 class="modal-title"><span class="fa fa-calendar-check-o "></span> Gestionar Entregas</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="table-responsive">

							<table class="table table-bordered table-hover table-condensed" id="tabla_articulos_parciales" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th colspan="2" class="nombre_tabla">TABLA ARTÍCULOS Y/O SERVICIOS</th>
										<td class="text-center sin-borde cantidades" colspan="3">Cantidad</td>
									</tr>
									<tr class="filaprincipal ">
										<td>Artículo y/o Servicio</td>
										<td>Marca</td>
										<td>Solicitada</td>
										<td>Entregada</td>
										<td>Pendiente</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"><span class="fa fa-history"></span> Temrinar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>

			</div>
		</div>
	</form>
<?php } else { ?>
	<form action="#" id="gestionar_solicitud">
		<div class="modal fade" id="Modal_gestionar_solicitud" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-retweet"></span> Servicio Recibido</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row text-center">
							<span class="pointer conadjuntos_gestion"> <span class="fa fa-folder-open red"></span>Clic aqui para adjuntar soportes a tu solicitud.</span>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btngestionar"><span class="glyphicon glyphicon-ok"></span> Aceptar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</form>
<?php } ?>


<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog modal-lg modal-95">
		<form action="#" id="Agregar_Solicitud" method="post" autocomplete="off">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-file-text"></span> Nueva Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="">
						<div class="agrupado">
							<span id="conadjuntos" class="pointer"> <span class="fa fa-folder-open red"></span>Clic aqui para adjuntar soportes a tu solicitud: </span>
							<ul>
								<li>Cotizaciones y/o Propuestas</li>
							</ul>
						</div>
						<div class="agrupado" style="margin-bottom: 10px; max-width: 500px;">
							<div class="input-group" id="div_jefe">
								<input type="text" class="form-control sin_margin sin_focus buscar_jefe" placeholder="Seleccionar Jefe" id='txt_nombre_jefe' required>
								<span class="input-group-addon pointer buscar_jefe" id='btn_buscar_jefe' style='background-color:white'><span class='fa fa-search red'></span> Jefe de Area</span>
							</div>
						</div>
						<div class="table-responsive">

							<table class="table table-bordered table-hover table-condensed" id="tabla_articulos_solicitados" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th class="nombre_tabla">TABLA ARTÍCULOS Y/O SERVICIOS</th>
										<th colspan="5" class="nombre_tabla"><span class="pointer" id="btsagregar"><span class="fa fa-plus pointer red"></span>Agregar Artículos y/o Servicio a esta solicitud</span></th>
									</tr>
									<tr class="filaprincipal ">
										<td>Código SAP</td>
										<td>Artículo y/o Servicio</td>
										<td>Cantidad</td>
										<td>Marca</td>
										<td>Referencia</td>
										<td>Fecha Compra</td>
										<td>Observaciones</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<textarea class="form-control oculto" id="txt_observaciones" cols="1" rows="3" name="observaciones" placeholder="Observaciones"></textarea>

					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>





<div class="modal fade" id="Modal_listar_proveedores_solicitud_comite" role="dialog">
	<div class="fixed-comentarios  pointer" style="padding-left: 80%">
		<div class="reque">

			<div class="login-container">


				<table class="" id="" style="width: 100%">
					<thead class="">
						<tr class="filaprincipal">
							<td colspan="" class="nombre_tabla"> INGRESE COMENTARIO</td>
							<td title="Cerrar Panel" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn cerrar_comentarios"></td>
						</tr>

					</thead>

				</table>

				<div class="form-boxw text-left">
					<textarea name="" id="comentario" cols="20" rows="5" class="form-control sin_margin comentarios" placeholder="Redactar"></textarea>
					<div class="input-group agro">
						<span class="input-group-addon pointer fondo-red  active" id="comentar"><span class="fa fa-send"> </span> Enviar</span>
						<span class="input-group-addon pointer active" id="listar_comentarios"><span class="fa fa-list"></span> Listar</span>
					</div>

				</div>

			</div>
		</div>

	</div>
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"> <span class="fa fa-list"></span> Proveedores Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="form-group text-right">
					<span class="badge  pointer ver_comentarios">Comentar</span>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed " id="tabla_proveedores_solicitud_comite" cellspacing="0" width="100%">
						<thead class=" ">
							<tr>
								<th colspan="6" class="nombre_tabla">TABLA PROVEEDORES</th>
							</tr>
							<tr class="filaprincipal ">
								<td class="opciones_tbl">Ver</td>
								<td>Nombre</td>
								<td>$Pesos</td>
								<td>$Dolar</td>
								<td>Aprobados</td>
								<td>Sugeridos</td>
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




<form action="#" id="modificar_sol_comite">
	<div class="modal fade" id="Modal_modificar_sol_comite" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Datos</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">

						<select id="comites_compras_modi" class="form-control comites_compras" required="true" name="comite">
							<option value="">Seleccione Comité</option>
						</select>
						<textarea name="descripcion" class="form-control" placeholder="Descripcion" required id="descripcion_cmt_modi"></textarea>
						<textarea name="observaciones" class="form-control" placeholder="Observaciones" id="observaciones_cmt_modi"></textarea>


					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btngestionar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>





<div class="modal fade con-scroll-modal" id="modalArticulos_Solicitud" role="dialog">
	<div class="modal-dialog modal-lg  modal-80">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<div class="input-group agro">
						<textarea name="" id="comentario_directo_2" cols="20" rows="1" class="form-control comentarios sin_margin" placeholder="Ingrese Comentario"></textarea>
						<span class="input-group-addon pointer fondo-red  active" id="comentar_directo_2"><span class="fa fa-send"> </span> Enviar</span>
						<span class="input-group-addon pointer active listar_comentarios_directos" id=""><span class="fa fa-list"></span> Listar</span>
					</div>
					<table class="table table-bordered table-condensed tabla_info_solicitud margin1" id="">
						<tr class="">
							<th class="nombre_tabla" colspan="3"> Información General</th>
						</tr>
						<!--<tr><td class="ttitulo">Nombre: </td><td colspan="2" class="valor_nombre"></td></tr>-->
						<tr class="">
							<td class="ttitulo">Tipo Solicitud: </td>
							<td colspan="2" class="valor_tipo_sol"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Solicitante:</td>
							<td colspan="2"><span class="valor_solicitante"></span>
								<?php if ($sw)  echo '<span id="detalle_persona_solicita" class="pointer 	fa fa-edit red" title="Detalle Persona" data-toggle="popover" data-trigger="hover"> </span>'; ?>
							</td>
						</tr>
						<tr>
							<td class="ttitulo">Cargo:</td>
							<td colspan="2" class="valor_cargo_sap"></td>
						</tr>
						<tr>
							<td class="ttitulo">Jefe Encargado:</td>
							<td colspan="2" class="valor_jefe"></td>
						</tr>
						<tr class="sin_info">
							<td class="ttitulo">No Orden: </td>
							<td colspan="2"><span class="valor_orden_cod"></span> <?php if ($sw) echo '<span class="btn-Efecto-men fa fa-wrench" id="editar_cod_orden" title="Modificar" data-toggle="popover" data-trigger="hover"></span>'; ?> </td>
						</tr>
						<tr class="sin_info">
							<td class="ttitulo">Tipo Orden: </td>
							<td colspan="2" class="valor_tipo_orden"></td>
						</tr>
						<tr class="sin_info">
							<td class="ttitulo">Tiempo Entrega: </td>
							<td colspan="2"><span class="valor_fe_estimada"></span> <?php if ($sw) echo '<span class="btn-Efecto-men fa fa-wrench" id="editar_tiempo_entrega" title="Modificar" data-toggle="popover" data-trigger="hover"></span>'; ?></td>
						</tr>
						<tr class="sin_info">
							<td class="ttitulo">Proveedor: </td>
							<td colspan="2"><span class="valor_proveedor"></span> <?php if ($sw) echo '<span class="btn-Efecto-men fa fa-wrench" id="cambiar_proveedor" title="Modificar" data-toggle="popover" data-trigger="hover"></span>'; ?></td>
						</tr>
						<!-- <tr><td class="ttitulo">Fecha Solicitud:</td><td colspan="2"  class="valor_fecha_solicitud"></td></tr>-->
						<tr>
							<td class="ttitulo">Fecha Solicitud:</td>
							<td colspan="2" class="valor_fecha_registro"></td>
						</tr>
						<tr>
							<td class="ttitulo">Estado: </td>
							<td colspan="2"><span><span class="valor_estado_sol"></span></span></td>
						</tr>
						<tr class="tr_valor_causal_dev">
							<td class="ttitulo">Causal de Devolucion: </td>
							<td colspan="2"><span><span class="valor_causal_dev"></span></span></td>
						</tr>
						<tr class="tr_valor_obs_devolucion">
							<td class="ttitulo">Motivo: </td>
							<td colspan="2"><span><span class="valor_obs_devolucion"></span></span></td>
						</tr>
						<tr class="oculto">
							<td class="ttitulo">Observaciones:</td>
							<td colspan="2" class="valor_observaciones"></td>
						</tr>
						<tr class="tr_nombre_comite">
							<td class="ttitulo">Comité: </td>
							<td colspan="2" class="valor_nombre_comite"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Archivos Adjunto:</td>
							<td colspan="2" class=""><span id="ver_adjuntos_lista"><span class="fa fa-eye red"></span> Ver</span></td>
						</tr>
						<tr class="tr_cronogramas">
							<td class="ttitulo">Cronogramas:</td>
							<td colspan="2" class=""><span id="ver_cronogramas_lista"><span class="fa fa-calendar-check-o red"></span> Ver cronogramas</span></td>
						</tr>
						<?php
						if ($sw) {
							echo '<tr class="auditoria-tr pointer" ><td id="auditarsolo"> <span class="fa fa-edit red_primari"></span> Ver Encuestas</td><td id="ver_historial_estado" ><span class="fa fa-clock-o red_primari"></span> Gestion Compras</td><td id="ver_historial_entregas" ><span class="fa fa-calendar red_primari"></span> Gestion Proveedor</td></tr>';
						}
						?>
					</table>
				</div>

				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_articulos" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="7" class="nombre_tabla">TABLA ARTÍCULOS Y/O SERVICIOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Ver</td>
								<td>Código SAP</td>
								<td>Artículo y/o Servicio</td>
								<td>Cantidad</td>
								<td>$Tarjeta</td>
								<td class="opciones_tbl">Acción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active btnAgregar mas_articulos" id="mas_articulos"><span class="btn-Efecto-men  fa fa-plus"></span> Articulos</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>





<div class="modal fade" id="Modal_listar_archivos_adjuntos" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
			</div>
			<div class="modal-body" id="bodymodal">

				<div class="table-responsive">

					<table class="table table-bordered table-hover table-condensed " id="tabla_adjuntos_compras" cellspacing="0" width="100%">
						<thead class=" ">
							<tr>
								<th colspan="2" class="nombre_tabla">TABLA ADJUNTOS</th>
								<td class="btnAgregar sin-borde text-center"><span><span class="fa fa-plus conadjuntos_gestion pointer red" title="Adjuntar Mas Archivos" data-toggle="popover" data-trigger="hover"></span></span></td>
							</tr>
							<tr class="filaprincipal ">
								<td>Nombre</td>
								<td>Fecha Adjunto</td>
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

<!-- Modal para cronogramas - Compras -->
<form action="" name="form_tipo_entregable" id="form_tipo_entregable" method="post">
	<div class="modal fade" id="modal_cronograma" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title" id="titulo_cronograma"><span class="fa fa-list-ol"></span> Lista de Cronogramas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="width: 100%">
						<div class="alert alert-info oculto" role="alert">
							<h4 class="text-center"><span class="fa fa-exclamation-triangle"></span> ¡Aviso!</h4>
							<p class="aviso_alert">
								Asegurece de gestionar correctamente las entregas, debido a que al aprobar el último recibido, la solicitud
								cambiará automáticamente al estado <u><strong>"Servicio Recibido"</strong></u>.
							</p>
						</div>

						<div class="detalle-entregable" role="alert"></div>
						<table class="table table-bordered table-hover table-condensed" id="tabla_cronograma" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr id="tabla-title-cronogramas">
									<th colspan="3" class="nombre_tabla">RESULTADOS</th>
								</tr>
								<tr class="filaprincipal ">	
									<td>Ver</td>	
									<td>Ítem</td>
									<td>Fecha</td>									
									<td class="showdiv">Comentario</td>
									<td>Estado</td>									
									<td class="sorting_1 accion">Acciones</td>
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
</form>

<!-- Modal para adjuntos cronogramas - Compras -->
<div class="modal fade" id="modal_ver_archvos_cronograma" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title" id="titulo_cronograma"><span class="fa fa-list-ol"></span> Detalles Cronograma</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="width: 100%">
						<nav class="navbar navbar-default" id="nav_admin_cronogramas">
							<div class="container-fluid nav_btns">
								<ul class="nav navbar-nav">
									<li class="pointer active" id="show_adj_crono"><a><span class="fa fa-folder red"></span> Adjuntos</a></li>
									<li class="pointer" id="show_estados_crono"><a><span class="fa fa-history red"></span> Estados</a></li>
								</ul>
							</div>
						</nav>
						<div id="container_adjuntos_cronograma">
							<table class="table table-bordered table-hover table-condensed" id="tabla_adjuntos_cronograma" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th colspan="3" class="nombre_tabla" style="vertical-align: middle;">RESULTADOS</th>
										<th class="sin-borde text-center">
											<span title="Agregar archivos" style="color: #0078d7;" data-toggle="popover" data-trigger="hover" class="fa fa-folder-open btn btn-default agregar_archvos_cronograma"></span>
										</th>
									</tr>
									<tr class="filaprincipal ">
										<td style='width: 7% !important;'>Ver</td>
										<td style='width: 15% !important;'>Nombre real</td>
										<td style='width: 10% !important;'>Nombre guardado</td>
										<td style='width: 13% !important;'>Fecha</td>									
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<div id="container_estados_cronograma" class="oculto">	
							<table class="table table-bordered table-hover table-condensed" id="tabla_estados_cronograma" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr>
										<th colspan="3" class="nombre_tabla" style="vertical-align: middle;">RESULTADOS</th>									
									</tr>
									<tr class="filaprincipal ">
										<td style='width: 7% !important;'>N°</td>
										<td style='width: 15% !important;'>Estado</td>
										<td style='width: 10% !important;'>Observación</td>
										<td style='width: 13% !important;'>Fecha</td>									
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

<!-- Modal para actualizar cronograma -->
<form action="" name="form_upd_crono" id="form_upd_crono" method="post">
	<div class="modal fade" id="modal_upd_crono" role="dialog">
		<div class="modal-dialog modal-sm">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title" id="titulo_upd_crono"><span class="fa fa-list-ol"></span> Actualizar Fecha</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="width: 100%">
						<input type="date" class="form-control" name="fecha_upd" id="fecha_upd">
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal para dar los checks de cronograma -->
<form action="" name="form_crono_checks" id="form_crono_checks" method="post">
	<div class="modal fade" id="modal_crono_checks" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title" id="titulo_crono_checks"><span class="fa fa-list-ol"></span> Insertar comentario</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" style="width: 100%">
						<div class="margin1">
							<div class="alert alert-info text-center" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> ¡Aviso!</h4>
								<p class="aviso_alert"><b> - </b>Por favor, deje un comentario respecto al servicio recibido</p>
							</div>
						</div>
						<div class="agro agrupado">
							<textarea class="form-control" name="especify" id="especify" cols="30" rows="7" placeholder="Inserte comentario aquí..."></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade con-scroll-modal" id="modal_detalle_pro_articulo" role="dialog">

	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">

				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Proveedor</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed" id="" width="100">


						<tr class="">
							<th class="nombre_tabla" colspan="3"> Información General</th>
						</tr>
						<tr class="">
							<td class="ttitulo">Nombre: </td>
							<td colspan="2" class="valor_nombre_proveedor"></td>
						</tr>
						<tr class="tr-adjunto">
							<td class="ttitulo">Propuesta: </td>
							<td colspan="2" class="valor_propuesta"></td>
						</tr>
						<tr class="sin_dolar">
							<td class="ttitulo">$Dolar: </td>
							<td colspan="2" class="valor_precio_dolar"></td>
						</tr>
						<tr>
							<td class="ttitulo">Fecha Registro:</td>
							<td colspan="2" class="valor_fecha_registro_prove"></td>
						</tr>
						<tr class="">
							<th class="nombre_tabla" colspan="3"> Detalle Compra</th>
						</tr>
						<tr class="">
							<td class="ttitulo">Moneda: </td>
							<td class="ttitulo">COL</td>
							<td class="ttitulo">USD</td>
						</tr>
						<tr class="">
							<td class="ttitulo">$Total: </td>
							<td class="valor_pesos"></td>
							<td class="valor_dolares"></td>
						</tr>

						<tr class="sin_info_proveedor">
							<td class="ttitulo">Administracion %<span class="valor_administracion"></span>: </td>
							<td class="pesos_administracion"></td>
							<td class="dolar_administracion"></td>
						</tr>
						<tr class="sin_info_proveedor">
							<td class="ttitulo">Imprevistos %<span class="valor_imprevistos"></span>: </td>
							<td class="pesos_imprevisto"></td>
							<td class="dolar_imprevisto"></td>
						</tr>
						<tr class="sin_info_proveedor">
							<td class="ttitulo">Utilidad %<span class="valor_utilidad"></span>: </td>
							<td class="pesos_utilidad"></td>
							<td class="dolar_utilidad"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">IVA %<span class="valor_iva"></span>:</td>
							<td class="pesos_iva"></td>
							<td class="dolar_iva"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Total Compra:</td>
							<td class="total_compra"></td>
							<td class="dolar_total_compra"></td>
						</tr>


					</table>
					<table class="table table-bordered table-hover table-condensed" id="tabla_vb_personas" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="6" class="nombre_tabla">Tabla vistos buenos</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Tipo</td>
								<td>Persona</td>
								<td>correo</td>
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

<!-- Asignar proveedor solicitud -->
<form action="#" id="asignar_proveedor_solicitud">
	<div class="modal fade" id="Modal_asignar_proveedor_articulo" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-truck"></span> Asignar Proveedor</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<input type="text" name="nombre" placeholder="Nombre" class="form-control" required>
						<div class="input-group agro">


						</div>

						<div class="input-group agro width100">
							<input type="text" name="valor_total" placeholder="$TOTAL" class="form-control sin_margin width100" required step="any" min="1">
							<span class="input-group-btn" style="width:20%">
								<select name="moneda" id="moneda" class="sin_margin form-control">
									<option value="cop">COP</option>
									<option value="usd">USD</option>
								</select>

							</span>
						</div>


						<input id="precio_dolar" type="text" name="precio_dolar" placeholder="$USD HOY" class="form-control oculto" step="any" min="1">
						<input type="number" name="iva" placeholder="%IVA" class="form-control" step="1" min="0" required>
						<div class="input-group margin1"><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar Propuesta<input name="adjunto" type="file" style="display: none;"></span></label><input type="text" class="form-control sin_margin" readonly></div>
						<span class="margin1 pointer" id="mostar_otras_cargas"><span class="fa fa-plus red"></span> Compra con AIU</span>
						<div id="otras_cargas" class="oculto">
							<input type="number" name="administracion" placeholder="%Administración" class="form-control" step="1" min="0">
							<input type="number" name="imprevistos" placeholder="%Imprevistos" class="form-control" step="1" min="0">
							<input type="number" name="utilidad" placeholder="%Utilidad" class="form-control" step="1" min="0">

						</div>

						<div>

						</div>


					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Agregar nuevas preguntas a una encuesta -->
<form action="#" id="agregar_preguntas_encuestas" method="post">
	<div class="modal fade" id="modal_agregar_pregunta_encuesta" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check-square-o"></span> Agregar pregunta</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agrupado">
							<small>Agregue el número campos necesarios para nuevas preguntas:</small>
							<div class="input-group">
								<input type="button" id="decremento" style="float: left;" class="btn red" value="-">
								<input type="number" value="1" class="form-control text-center" style="width: 50%;" name="nums_inputs" id="nums_inputs" placeholder="Inserta número de casillas necesarias!">
								<input type="button" id="incremento" class="btn red" value="+">
							</div>
							<hr>
						</div>
						<div id="preguntas_container">
							<input type="text" class="form-control" name="pregunta[]" data-item="1" data-id="preg" value="" placeholder="Escriba la pregunta aqui!" required>
							<hr>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form action="#" id="form_modificar_proveedor_solicitud">
	<div class="modal fade" id="Modal_modificar_proveedor_solicitud" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Proveedor</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<input type="text" name="nombre" placeholder="Nombre" class="form-control" required>
						<div class="input-group agro">


						</div>

						<div class="input-group agro width100">
							<input type="text" name="valor_total" placeholder="$TOTAL" class="form-control sin_margin width100" required step="any" min="1">
							<span class="input-group-btn" style="width:20%">
								<select name="moneda" id="moneda_modi" class="sin_margin form-control">
									<option value="cop">COP</option>
									<option value="usd">USD</option>
								</select>

							</span>
						</div>


						<input id="precio_dolar_modi" type="text" name="precio_dolar" placeholder="$USD HOY" class="form-control oculto" step="any" min="1">
						<input type="number" name="iva" placeholder="%IVA" class="form-control" step="1" min="0" required>
						<div class="input-group margin1"><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar Propuesta<input name="adjunto" type="file" style="display: none;"></span></label><input type="text" class="form-control sin_margin" readonly></div>
						<span class="margin1 pointer" id="mostar_otras_cargas_modi"><span class="fa fa-plus red"></span> Compra con AIU</span>
						<div id="otras_cargas_modi" class="oculto">
							<input type="number" name="administracion" placeholder="%Administración" class="form-control" step="1" min="0">
							<input type="number" name="imprevistos" placeholder="%Imprevistos" class="form-control" step="1" min="0">
							<input type="number" name="utilidad" placeholder="%Utilidad" class="form-control" step="1" min="0">

						</div>

						<div>

						</div>


					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>


<div class="modal fade scroll-modal" id="modalModificarSolicitud" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="Modificar_Solicitud" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Solicitud</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<select name="tipo_compra" required class="form-control inputt cbxtipocompra" id="cbxmod_tipo_compra">
							<option>Seleccione Tipo Compra</option>
						</select>
						<textarea class="form-control" id="mod_txtobservaciones" cols="1" rows="3" name="observaciones" placeholder="Ingrese Motivo del cambio" required></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="fa fa-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade scroll-modal" id="Editar_Articulos" role="dialog">
	<div class="modal-dialog modal-lg">
		<form action="#" id="Editar_Articulo" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Modificar Artículo</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="funkyradio">
							<div class="funkyradio-success">
								<input type="checkbox" id="con_tarjeta_modi" name="con_tarjeta" value="1">
								<label for="con_tarjeta_modi"> Compra con Tarjeta de crédito</label>
							</div>
						</div>
						<div class="input-group agro">
							<input name="codigo_sap" type="hidden" id="txtinput_codigo_orden" required>
							<span class="form-control text-left pointer cod_sel sel_cod_modi" id="cod_sap">Seleccione Código SAP</span>
							<span class="input-group-addon red_primari pointer btn-Efecto-men sel_cod_modi" id="" title="Buscar Código" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre_articulo" class="form-control inputt2" placeholder="Nombre Artículo y/o Servicio" id="txtnom_art" required>
								<span class="input-group-addon">-</span>
								<input type="number" name="cantidad_art" class="form-control inputt2" placeholder="Cantidad de Articulos y/o Servicios" id="txtcant" min="1" required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="marca_art" class="form-control inputt2" placeholder="Marca (opcional)" id="txtmarca">
								<span class="input-group-addon">-</span>
								<input type="text" name="referencia_art" class="form-control inputt2" placeholder="Referencia (opcional)" id="txtref">
							</div>
						</div>
						<div id='container_con_tarjeta_modi' class='oculto'>
							<div class="agro agrupado">
								<div class="input-group">
									<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Compra</span>
									<input name='fecha_compra_tarjeta' class="form-control sin_margin" type='date' id="fecha_compra_tarjeta_modi">
								</div>
							</div>
						</div>
						<textarea name="observaciones" class="form-control" placeholder="Descripción" id="txtobservaciones_art" required></textarea>
						<div class="margin1">
							<div class="alert alert-info" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
								<p><b> - </b>Ser claro y especifico en el nombre de articulo o descripción/ Concepto del servicio.</p>
								<p><b> - </b>Especificar en el campo de descripción el lugar donde sera utilizada la compra</p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnModifica"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade scroll-modal" id="modalArticulos" role="dialog">
	<div class="modal-dialog modal-lg">
		<form action="#" id="Agregar_Articulos" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-shopping-cart"></span> <span id="text_add_arts">Agregar Artículos y/o Servicio</span></h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="margin1">
							<div class="alert alert-info oculto" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
								<p>Si la solicitud que va a realizar se encuentra en el grupo de inversiones institucionales, le informamos que solo podrá hacerlo hasta el 5 de diciembre del 2020. Las rubros de inversión son: Muebles y enseres, Computadores,Equipos de laboratorios,Equipos de Audiovisuales y comunicaciones,Herramientas,Libros,Licencias Software nuevas Y Aires Acondicionados.</p>
							</div>
						</div>
						<div class="funkyradio">
							<div class="funkyradio-success" id='con_tarjeta_che'>
								<input type="checkbox" id="con_tarjeta" name="con_tarjeta" value="1">
								<label for="con_tarjeta"> Compra con Tarjeta de crédito</label>
							</div>
						</div>
						<div class="input-group agro">
							<input name="codigo_orden" type="hidden" id="input_codigo_orden">
							<span class="form-control text-left pointer cod_sel sel_cod" id="cod_orden_sele">Seleccione Código SAP</span>
							<span class="input-group-addon red_primari pointer btn-Efecto-men sel_cod" id="sele_cod_orden" title="Buscar Código" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="nombre_art" class="form-control" placeholder="Nombre Artículo y/o Servicio" id="txtnombre_articulo" required>
								<span class="input-group-addon">-</span>
								<input type="number" name="cantidad_art" class="form-control" placeholder="Cantidad Artículos y/o Servicios" id="txtcantidad" min="1" required>
							</div>
						</div>
						<div class="agro agrupado">
							<div class="input-group">
								<input type="text" name="marca_art" class="form-control" placeholder="Marca (opcional)" id="txtmarca_articulo">
								<span class="input-group-addon">-</span>
								<input type="text" name="referencia_art" class="form-control" placeholder="Referencia (opcional)" id="txtreferencia">
							</div>
						</div>
						<div id='container_con_tarjeta' class='oculto'>
							<div class="agro agrupado">
								<div class="input-group">
									<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Compra</span>
									<input name='fecha_compra_tarjeta' class="form-control sin_margin" type='date' id="fecha_compra_tarjeta">
								</div>
							</div>
						</div>
						<textarea name="observaciones" class="form-control" placeholder="Descripción" required id="txt_observaciones_articulo"></textarea>
						<div class="margin1">
							<div class="alert alert-info" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
								<p><b> - </b>Ser claro y especifico en el nombre de articulo o descripción/ Concepto del servicio.</p>
								<p><b> - </b>Especificar en el campo de descripción el lugar donde sera utilizada la compra</p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="Buscar_Codigo" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="Buscar_Codigo_Orden" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"> <span class="	fa fa-search"></span> Buscar Código SAP</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>

						<div class="input-group agro col-md-8">
							<input type="text" class="form-control inputt2" name="codigo_sap" placeholder="Buscar Código Orden SAP" id="txtcodigo_sap" autocomplete="off">
							<span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_cod_sap" title="Buscar Código" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
						</div>

						<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_codigos" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="2" class="nombre_tabla">TABLA CÓDIGOS SAP</th>
								</tr>
								<tr class="filaprincipal ">
									<td>Código</td>
									<td>Descripción</td>
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
		</form>
	</div>
</div>

<div class="modal fade" id="Modal_jefe" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Jefe Encargado</h3>
			</div>

			<div class="modal-body" id="bodymodal">
				<div class="row" id="" style="width: 100%">
					<div id="persona_existente">
						<div class="text-center" id="panel-selec-personas">

							<div class="form-group agrupado col-md-8 text-left">

								<div class="input-group">
									<input id="input_persona_reserva" class="form-control" placeholder="Ingrese identificacion, nombre o apellido de la persona">
									<span class="btn btn-default  input-group-addon" id="buscar_sele_perso"><span class="glyphicon glyphicon-search"> </span> </span>
								</div>
							</div>
							<div class="table-responsive col-md-12" style="width: 100%">
								<table class="table table-bordered table-hover table-condensed pointer" id="tabla_peronas_jefe" cellspacing="0" width="100%">
									<thead class="ttitulo ">
										<tr class="">
											<td colspan="3" class="nombre_tabla">TABLA PERSONAS</td>
										</tr>
										<tr class="filaprincipal">
											<td>Nombre Completo</td>
											<td class="">Identificación</td>
											<td>Correo</td>
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
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="Modal_solicitudes_usuario" role="dialog">


	<div class="fixed exceptuando oculto">
		<div class="reque">
			<div class="login-container">
				<table class="" id="" style="width: 100%">
					<thead class="">
						<tr class="">
							<td colspan="" class="nombre_tabla"> Solicitudes<span class="" id=""></span></td>
						</tr>

					</thead>

				</table>

				<div class="form-boxw text-left">
					<br>
					<ul id="lista_excluida">


					</ul>
				</div>
			</div>
		</div>
	</div>



	<div class="modal-dialog">


		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Solicitudes Usuario</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="input-group agro btnAgregar col-xs-12 col-sm-10 col-md-8 col-lg-8 ">
					<select name="asignar_solicitud" class="form-control sin_margin" id="solicitudes_no_asignadas">
						<option value="">Asignar tipo solicitud</option>
					</select>
					<span class="input-group-addon  pointer fondo-red" id="agregar_soliciutd_usuario"><span class="glyphicon glyphicon-ok"></span> Asignar </span>
				</div>
				<div class="table-responsive margin1">

					<table class="table table-bordered table-hover table-condensed" id="tabla_solicitudes_usuario" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA SOLICITUDES</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Nombre</td>
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

<div class="modal fade" id="Modal_estados_solicitudes" role="dialog">
	<div class="fixed exceptuando_estados oculto">
		<div class="reque">
			<div class="login-container">
				<table class="" id="" style="width: 100%">
					<thead class="">
						<tr class="">
							<td colspan="" class="nombre_tabla"> Estados<span class="" id=""></span></td>
						</tr>

					</thead>

				</table>

				<div class="form-boxw text-left">
					<br>
					<ul id="lista_excluida_estados">


					</ul>
				</div>
			</div>
		</div>
	</div>


	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Estados Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="input-group agro btnAgregar col-xs-12 col-sm-10 col-md-8 col-lg-8 ">
					<select name="asignar_estado" class="form-control sin_margin" id="estados_no_asignados">
						<option value="">Asignar Estado</option>
					</select>
					<span class="input-group-addon  pointer fondo-red" id="agregar_estado_usuario"><span class="glyphicon glyphicon-ok"></span> Asignar </span>
				</div>
				<div class="table-responsive">

					<table class="table table-bordered table-hover table-condensed" id="tabla_estados_solicitudes" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">TABLA ESTADOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Nombre</td>
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

<div class="modal fade" id="modal_tiempos_gestion" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-edit"></span> Encuesta de satisfacción </h3>
			</div>

			<div class="modal-body" id="bodymodal">
				<nav class="navbar navbar-default" id="nav_admin_encuestas">
					<div class="container-fluid nav_btns">
						<ul class="nav navbar-nav">
							<?php if ($sw_super || $sw) { ?><li class="pointer" id="show_satis_enc"><a><span class="fa fa-check red"></span> Encuesta de satisfacción</a></li> <?php } ?>
							<?php if ($sw_super || $sw || $permiso->comite) { ?><li class="pointer" id="show_rp_encs"><a><span class="fa fa-eye red"></span> Reevaluación de Proveedores</a></li> <?php } ?>
						</ul>
					</div>
				</nav>
				<div class="table-responsive" id="tabla_satis_enc">
					<table class="table table-bordered table-condensed" cellspacing="0" width="100%">
						<tr class="filaprincipal">
							<td>No.</td>
							<td>Pregunta</td>
							<td>Valoracion</td>
						</tr>
						<tr class="">
							<td>1</td>
							<td colspan="">¿ Cual es su nivel de satisfacción respecto a la atención prestada por el departamento de compras desde la recepción de la solicitud hasta la entrega final del bien o servicio requerido ?</td>
							<td class="valor_enc_pre_1">0</td>
						</tr>
						<tr class="">
							<td>2</td>
							<td colspan="">¿ El bien o servicio entregado cumple con las especificaciones requeridas en la solicitud de adquisición ?</td>
							<td class="valor_enc_pre_2">0</td>
						</tr>
						<tr class="">
							<td>3</td>
							<td colspan="">¿ Su nivel de satisfacción en el tiempo que recibió el bien o servicio solicitado fue ?</td>
							<td class="valor_enc_pre_3">0</td>
						</tr>
						<tr class="">
							<td>4</td>
							<td colspan="2">
								<p>Observaciones:<span class="valor_enc_pre_4"></span></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Tabla de las encuestas RP -->
				<div class="table-responsive oculto" id="div_encs_rp">
					<table class="table table-bordered table-hover table-condensed" id="tabla_encs_rp" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">ENCUESTAS DE REEVALUACIÓN DE PROVEEDORES </th>
							</tr>
							<tr class="filaprincipal ">
								<td>No.</td>
								<td>Pregunta</td>
								<td class="opciones_tbl">Acciones</td>
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

<!-- Modal para mostrar encuestas realizadas RP -->
<div class="modal fade" id="modal_finished_rp" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title" id="enc_title"><span class="fa fa-edit"></span> Encuestas </h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_finised_rp" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">ENCUESTA </th>
							</tr>
							<tr class="filaprincipal ">
								<td>No.</td>
								<td>Pregunta</td>
								<td class="opciones_tbl">Valoración</td>
								<td class="opciones_tbl">Observaciones</td>
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

<!-- Modal para mostrar encuestas realizadas RP -->
<div class="modal fade" id="modal_tiempo_entrega" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title" id="enc_title"><span class="fa fa-edit"></span> Encuestas </h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed" id="tabla_tiempo_entrega" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">ENCUESTA </th>
							</tr>
							<tr class="filaprincipal ">
								<td>Entrega Estimada</td>
								<td>Entrega Real</td>
								<td>Valoracion</td>
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

<!-- Modal encuesta de satisfaccion RP -->
<form id="encuesta_rp" name="encuesta_rp" method="post">
	<div class="modal fade" id="modal_encuestas_rp" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Encuesta </h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<nav class="navbar navbar-default">
						<div class="container-fluid">
							<ul class="nav navbar-nav encs_nav">
								<!-- Auto gen -->
							</ul>
						</div>
					</nav>
					<div id="tabla_enc_rp"></div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="BtnAccion"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="modal fade con-scroll-modal" id="modal_historial_estados" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-clock-o"></span> Historial de Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="form-btn-group">
					<span class=" pointer" id="calcular_tiempos"> <span class="fa fa-refresh"></span> Calcular Tiempo</span>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-condensed columna50" cellspacing="0" width="100%">
						<tr class="">
							<th class="nombre_tabla" colspan="2"> Gestión de Compras </th>
						</tr>
						<tr class="filaprincipal">
							<td>Dias Habil</td>
							<td>Dias Gestión</td>
						</tr>
						<tr class="">
							<td class="valor_gestion_habiles">----</td>
							<td class='valor_gestion_enges'>----</td>
						</tr>

					</table>
					<table class="table table-bordered table-hover table-condensed" id="tabla_historial" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="4" class="nombre_tabla">TABLA ESTADOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>No.</td>
								<td>Estado</td>
								<td>Persona</td>
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

<div class="modal fade con-scroll-modal" id="modal_historial_entregas" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-calendar"></span> Historial de Entregas</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed columna50" cellspacing="0" width="100%">
						<tr class="">
							<th class="nombre_tabla" colspan="3"> Gestión del Proveedor</th>
						</tr>
						<tr class="filaprincipal">
							<td>Entrega Estimada</td>
							<td>Entrega Real</td>
							<td>Calificación del Proveedor</td>
						</tr>
						<tr class="">
							<td class="valor_fe_estimada_real">----</td>
							<td class="valor_fe_real">----</td>
							<td class="resultado_final_rp">----</td>
						</tr>
					</table>
					<div class="form-group col-md-6">
						<select name="" class="form-control" id="cbx_articulos_historial">
							<option value="">Seleccione Articulo</option>
						</select>
					</div>
					<table class="table table-bordered table-hover table-condensed" id="tabla_historial_entregas" cellspacing="0" width="100%">
						<thead class="ttitulo ">

							<tr>
								<th colspan="3" class="nombre_tabla">TABLA ENTREGAS PARCIALES</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Entregada</td>
								<td>Fecha</td>
								<td>Persona</td>
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


<form action="" id="guardar_encuesta">
	<div class="modal fade" id="modal_encuesta_usuario" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-edit"></span> Encuesta</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<nav class="navbar navbar-default">
						<div class="container-fluid">
							<ul class="nav navbar-nav encs_nav">
								<!-- Auto gen -->
							</ul>
						</div>
					</nav>
					<div id="pregunta1" class="preguntas">
						<p>1. ¿ Cual es su nivel de satisfacción respecto a la atención prestada por el departamento de compras desde la recepción de la solicitud hasta la entrega final del bien o servicio requerido ?</p>
						<div class="respuestas">
							<span><input id="pre1_res_1" type="radio" name="respuesta1" value="1"><label for="pre1_res_1">1</label></span>
							<span><input id="pre1_res_2" type="radio" name="respuesta1" value="2"><label for="pre1_res_2">2</label></span>
							<span><input id="pre1_res_3" type="radio" name="respuesta1" value="3"><label for="pre1_res_3">3</label></span>
							<span><input id="pre1_res_4" type="radio" name="respuesta1" value="4"><label for="pre1_res_4">4</label></span>
							<span><input id="pre1_res_5" type="radio" name="respuesta1" value="5"><label for="pre1_res_5">5</label></span>
						</div>
					</div>

					<div id="pregunta2" class="preguntas">
						<p>2. ¿ El bien o servicio entregado cumple con las especificaciones requeridas en la solicitud de adquisición ?</p>
						<div class="respuestas">
							<span><input id="pre2_res_1" type="radio" name="respuesta2" value="1"><label for="pre2_res_1">1</label></span>
							<span><input id="pre2_res_2" type="radio" name="respuesta2" value="2"><label for="pre2_res_2">2</label></span>
							<span><input id="pre2_res_3" type="radio" name="respuesta2" value="3"><label for="pre2_res_3">3</label></span>
							<span><input id="pre2_res_4" type="radio" name="respuesta2" value="4"><label for="pre2_res_4">4</label></span>
							<span><input id="pre2_res_5" type="radio" name="respuesta2" value="5"><label for="pre2_res_5">5</label></span>
						</div>
					</div>

					<div id="pregunta3" class="preguntas">
						<p>3. ¿ Su nivel de satisfacción en el tiempo que recibió el bien o servicio solicitado fue ?</p>
						<div class="respuestas">
							<span><input id="pre3_res_1" type="radio" name="respuesta3" value="1"><label for="pre3_res_1">1</label></span>
							<span><input id="pre3_res_2" type="radio" name="respuesta3" value="2"><label for="pre3_res_2">2</label></span>
							<span><input id="pre3_res_3" type="radio" name="respuesta3" value="3"><label for="pre3_res_3">3</label></span>
							<span><input id="pre3_res_4" type="radio" name="respuesta3" value="4"><label for="pre3_res_4">4</label></span>
							<span><input id="pre3_res_5" type="radio" name="respuesta3" value="5"><label for="pre3_res_5">5</label></span>
						</div>
					</div>

					<div id="pregunta4" class="preguntas oculto">
						<p> Justifique su respuesta, a las preguntas donde respondió (2)malo o (1)muy malo.</p>
						<textarea name="observaciones_encu" id="" rows="3" placeholder="Redactar..." class="form-control" value=""></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="terminar_encuesta"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>


<div class="modal fade" id="ValorParmetro" role="dialog">
	<div class="modal-dialog">
		<form action="#" id="GuardarProveedor" method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-truck"></span> Nuevo Proveedor</h3>
				</div>

				<div class="modal-body" id="bodymodal">
					<div class="row">

						<div class="error form-group has-error text-center oculto"></div>

						<div class="div_id_aux oculto">


						</div>
						<input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
						<textarea class="form-control inputt" name="descripcion" placeholder="Descripcion"></textarea>

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
	<div class="modal-dialog" id="modal_modpara_container">
		<form action="#" id="ModificarItem" method="post">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title" id="modal_modpara_titulo"><span class="fa fa-truck"></span> Modificar Proveedor</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row divmodifica">
						<input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre" name="nombre" required>
						<textarea rows="3" cols="100" class="form-control oculto" readonly id="txtDescripcion_modificar" placeholder="Descripcion" name="descripcion" required></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active btnModifica"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="comiteadd" role="dialog">
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
						<!--<input type="date" name="fecha" class="form-control inputt" placeholder="Fecha Cierre"  required>-->
						<textarea class="form-control inputt" name="descripcion" placeholder="Descripcion"></textarea>

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

<div class="modal fade" id="comitemodi" role="dialog">
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

						<input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="nombre_comi_modi" required>
						<!--<input type="date" name="fecha" class="form-control inputt" placeholder="Fecha Cierre"  id="fecha_comi_modi"required>-->
						<textarea class="form-control inputt" name="descripcion" placeholder="Descripcion" id="descripcion_comi_modi"></textarea>

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


<div class="modal fade" id="Modal_comentarios_compras" role="dialog">

	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-commenting "></span> Comentarios Compra</h3>
			</div>
			<div class="modal-body" id="bodymodal">


				<div class="table-responsive" style="width: 100%">
					<table class="table table-bordered table-hover table-condensed" id="tabla_comentarios" cellspacing="0" width="100%">
						<thead class="ttitulo ">

							<tr class="">
								<td colspan="4" class="nombre_tabla">tabla comentarios</td>
							</tr>
							<tr class="filaprincipal">
								<td>Ver</td>
								<td>Comentario</td>
								<td>Persona</td>
								<td>Fecha</td>
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

<div class="modal fade" id="modal_notificaciones_compras" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones Compras</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
				<?php if ($sw) { ?><div id="panel_notificaciones_solicitudes" style="width: 100%" class="list-group"></div><?php } ?>
				<?php if ($sw) { ?><div id="panel_notificaciones_solicitudes_proxima" style="width: 100%" class="list-group"></div><?php } ?>
				<?php if ($sw) { ?><div id="panel_notificaciones_solicitudes_serviciorec" style="width: 100%" class="list-group"></div><?php } ?>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="Modal_comentarios_pregunta_compra" role="dialog">

	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-comments"></span> Respuestas Comentario</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive" style="width: 100%">
					<div style="width: 100%" class="list-group">
						<a href="#" class="list-group-item">
							<span class="badge btn-danger" id="ver_compra">Ver Compra</span>
							<span class="badge " id="btn_terminar_comentario">Terminar</span>
							<h4 class="list-group-item-heading usuario_pre_info"></h4>
							<p class="list-group-item-text pregunta_info"></p>
						</a>
					</div>
					<table class="table table-bordered table-hover table-condensed" id="tabla_comentarios_respuestas" cellspacing="0" width="100%">
						<thead class="ttitulo ">

							<tr class="">
								<td colspan="2" class="nombre_tabla">tabla respuestas</td>
							</tr>
							<tr class="filaprincipal">
								<td>Respuesta</td>
								<td>Usuario</td>
								<td>Fecha</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>

				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="submit" class="btn btn-danger active" id="btn_responder_comentario"><span class="glyphicon glyphicon-ok"></span> Responder</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>

		</div>


	</div>

</div>


<div class="modal fade con-scroll-modal" id="modal_detalle_solicitud_noti" role="dialog">
	<div class="modal-dialog modal-lg  modal-80">


		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">

				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed tabla_info_solicitud_notificacion" id="">


						<tr class="">
							<th class="nombre_tabla" colspan="3"> Información General</th>
						</tr>
						<!--<tr><td class="ttitulo">Nombre: </td><td colspan="2" class="valor_nombre"></td></tr>-->
						<tr class="">
							<td class="ttitulo">Tipo Solicitud: </td>
							<td colspan="2" class="valor_tipo_sol_noti"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Solicitante:</td>
							<td colspan="2"><span class="valor_solicitante_noti"></span>
								<?php if ($sw) echo '<span id="detalle_persona_solicita_noti" class="pointer 	fa fa-edit red" title="Detalle Persona" data-toggle="popover" data-trigger="hover"> </span>'; ?>
							</td>
						</tr>
						<tr>
							<td class="ttitulo">Departamento:</td>
							<td colspan="2" class="valor_departamento_noti"></td>
						</tr>
						<tr>
							<td class="ttitulo">Jefe Encargado:</td>
							<td colspan="2" class="valor_jefe_noti"></td>
						</tr>
						<tr class="sin_info_noti">
							<td class="ttitulo">No Orden: </td>
							<td colspan="2"><span class="valor_orden_cod_noti"></span></td>
						</tr>
						<tr class="sin_info_noti">
							<td class="ttitulo">Tiempo Entrega: </td>
							<td colspan="2" class="valor_fe_estimada_noti">----</td>
							</td>
						</tr>
						<tr class="sin_info_noti">
							<td class="ttitulo">Proveedor: </td>
							<td colspan="2" class="valor_proveedor_noti"></td>
						</tr>
						<!-- <tr><td class="ttitulo">Fecha Solicitud:</td><td colspan="2"  class="valor_fecha_solicitud"></td></tr>-->
						<tr>
							<td class="ttitulo">Fecha Solicitud:</td>
							<td colspan="2" class="valor_fecha_registro_noti"></td>
						</tr>
						<tr>
							<td class="ttitulo">Estado: </td>
							<td colspan="2"><span><span class="valor_estado_sol_noti"></span></span></td>
						</tr>
						<tr class="tr_valor_obs_devolucion_noti">
							<td class="ttitulo">Motivo: </td>
							<td colspan="2"><span><span class="valor_obs_devolucion_noti"></span></span></td>
						</tr>
						<tr class="oculto">
							<td class="ttitulo">Observaciones:</td>
							<td colspan="2" class="valor_observaciones_noti"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Archivos Adjunto:</td>
							<td colspan="2" class=""><span id="ver_adjuntos_lista_noti"><span class="fa fa-eye red"></span>Ver</span></td>
						</tr>
					</table>
					<table class="table table-bordered table-condensed" id="">
						<tr class="tr_comite">
							<th class="nombre_tabla" colspan="3"> Datos Comité</th>
						</tr>
						<tr class="tr_comite">
							<td class="ttitulo">Descripcion:</td>
							<td colspan="2" class="valor_descripcion_comite"></td>
						</tr>
						<tr class="tr_comite">
							<td class="ttitulo">Observaciones:</td>
							<td colspan="2" class="valor_observaciones_comite"></td>
						</tr>
					</table>
				</div>

				<div class="table-responsive">

					<table class="table table-bordered table-hover table-condensed" id="tabla_articulos_notificacion" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr>
								<th colspan="7" class="nombre_tabla">TABLA ARTÍCULOS Y/O SERVICIOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Ver</td>
								<td>Código SAP</td>
								<td>Artículo y/o Servicio</td>
								<td>Cantidad</td>
								<td>$Tarjeta</td>
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

<div class="modal fade" id="modal_detalle_articulo" role="dialog">

	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">

				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Artículo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="alert alert-info tr_fecha_compra_art">
					<strong>Atención!</strong> Compra con tarjeta de crédito para el día <span class="valor_fecha_compra_tarjeta_art"></span>.
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-condensed" id="" width="100">
						<tr class="">
							<th class="nombre_tabla" colspan="2"> Información General</th>
						</tr>
						<tr class="">
							<td class="ttitulo">código SAP: </td>
							<td class="valor_codigo_art"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Nombre: </td>
							<td class="valor_nombre_art"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Cantidad: </td>
							<td class="valor_cantidad_art"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Marca: </td>
							<td class="valor_marca_art"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Referencia: </td>
							<td class="valor_referencia_art"></td>
						</tr>
						<tr class="">
							<td class="ttitulo">Observaciones: </td>
							<td class="valor_observaciones_art"></td>
						</tr>
						<tr>
							<td class="ttitulo">Fecha Registro:</td>
							<td class="valor_fecha_cr_art"></td>
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

<!-- Modal para crear filtros de proveedores usados en determinadas fechas según AQUI -->
<form method="post" id="form_prov_filter" name="form_prov_filter">
	<div class="modal fade" id="modal_filtrar_proveedores" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
								<input class="form-control sin_margin" value="" type="date" name="fecha_desde" id="fecha_desde">
							</div>
						</div>

						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
								<input class="form-control sin_margin" value="" type="date" name="fecha_hasta" id="fecha_hasta">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="btngenerar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- Modal para mostrar proveedores generados -->
<div class="modal fade" id="modal_prov_list" role="dialog">
	<div class="modal-dialog modal-lg modal-95">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Lista de proveedores</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table class="table table-bordered table-hover table-condensed" id="tabla_proveedores_f" cellspacing="0" width="100%">
					<thead class="ttitulo ">
						<tr>
							<th colspan="2" class="nombre_tabla" style="vertical-align: text-top;">TABLA PROVEEDORES</th>
						</tr>
						<tr class="filaprincipal ">
							<td>Tipo</td>
							<td>Número de orden</td>
							<td>Proveedor</td>
							<td>Fecha registra</td>
							<td>Días estimados</td>
							<td>Fecha de entrega real</td>
							<td>Fecha de entrega ideal</td>
							<td>Estado</td>
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


<div class="modal fade" id="Modal_listar_archivos_adjuntos_noti" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
			</div>
			<div class="modal-body" id="bodymodal">

				<div class="table-responsive">

					<table class="table table-bordered table-hover table-condensed " id="tabla_adjuntos_compras_noti" cellspacing="0" width="100%">
						<thead class=" ">
							<tr>
								<th colspan="3" class="nombre_tabla">TABLA ADJUNTOS</th>
							</tr>
							<tr class="filaprincipal ">
								<td>Nombre</td>
								<td>Fecha Adjunto</td>
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
					<input type="hidden" name="idCrono" id="id_cronograma" val="0">
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

<form id="form_buscar_persona" method="post">
	<div class="modal fade" id="modal_buscar_jefe" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Buscar Jefe</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" id="" style="width: 100%">
						<div class="form-group agrupado col-md-8 text-left">
							<div class="input-group">
								<input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación, usuario o nombre del jefe">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
							</div>
						</div>
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_jefes" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
									</tr>
									<tr class="filaprincipal">
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


<?php if ($sw_super) { ?>
	<div id='acta_compra' class='oculto'>
		<div class="row">
			<div class="col-sm-12">
				<div class="thumbnail">
					<img src="<?php echo base_url() ?>/imagenes/LogocucF.png" alt="..." width='200' heigth='200'>
					<div class="caption text-center">
						<h2>COMITÉ DE COMPRAS</h2>
						<h3>ACTA <span id='nombre_pri'></span> - <span id='ano_pri'></span></h3>
						<table class='table' id='tabla_miembros'>
							<thead>
								<tr>
									<td>CIUDAD: </td>
									<td>Barranquilla</td>
								</tr>
								<tr>
									<td>FECHA INICIO: </td>
									<td id='fecha_inicio_pri'>08/02/2019</td>
								</tr>
								<tr>
									<td>FECHA FIN: </td>
									<td id='fecha_fin_pri'>08/02/2019</td>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
						<div class='text-left'>

							<p>A continuación, se relacionan las solicitudes recibidas y su estado en el comité de compras</p>
						</div>
						<div>
							<br><br><br>
							<table class='table table-bordered' id='tabla_solicitudes_acta'>
								<thead>
									<tr>
										<td>No. de solicitud</td>
										<td>Solicitante</td>
										<td>Tipo</td>
										<td>Descripción</td>
										<td>Observaciones</td>
										<td>Proveedor</td>
										<td>Valor Total</td>
										<td>Aprobados</td>
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

	<!-- modal de permisos -->
	<div class="modal fade" id="modal_administrar_permisos" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button class="close" type="button" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Permisos</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div>
						<table class="table table-bordered table-hover table-condensed" id="tabla_permisos_com" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th class="nombre_tabla">TABLA PERMISOS</td>
								</tr>
								<tr class="filaprincipal">
									<td>Nombre</td>
									<td style="width:150px">Accion</td>
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

<?php if ($sw) { ?>
	<!-- modal de cambio de proveedor -->
	<div class="modal fade" id="modal_cambiar_proveedor" role="dialog">
		<div class="modal-dialog">
			<form action="#" id="form_cambiar_proveedor" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-wrench"></span> <span id="text_add_arts"></span> Cambiar Proveedor</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<label for="select_cambiar_apoyo" style="font-weight: 500;">Seleccione el nuevo Proveedor :</label>
							<select id="select_cambiar_apoyo" class="form-control cbxproveedores" name="cambiar_proveedor" required></select>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active btnAgregar"><span class="fa fa-check"></span> Cambiar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cancelar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>

<!-- Modal para la asignacion de encuestas a usuarios -->
<?php if ($sw): ?>
	<div class="modal fade" id="modal_permisos_cronogramas" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-list"></span> Asignar permiso</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-condensed" id="tabla_permisos_cronogramas" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="4" class="nombre_tabla">TABLA PERSONAS - PERMISOS CRONOGRAMAS</th>
								</tr>				
								<tr class="filaprincipal ">
									<td>Nombre</td>
									<td>Gestión</td>
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
<?php endif; ?>

<script>
	$(document).ready(function() {
		<?php if ($sw) { ?> mostrar_notificaciones(1);
		<?php } ?>
		guardar_solicitud_2();
		inactivityTime();
		activarfile();
		Listar_solicitudes(0, <?php echo $comite ?>);
		Cargar_parametro_buscado_aux(34, ".cbxtipocompra", "Seleccione Tipo Compra");
		Cargar_parametro_buscado_aux(72, ".cbx_tipo_orden", "Seleccione Tipo orden");
		Cargar_parametro_buscado_aux(33, ".cbxestado", "Seleccione Estado");
		Cargar_parametro_buscado(3, ".cbxdepartamento", "Para ser usado en");
		Cargar_parametro_buscado(37, ".cbxproveedores", "Seleccione proveedor");
		Cargar_parametro_buscado_aux(213, ".cbxcausalcompra", "Seleccione causal");
		// Cargar_parametro_buscado(213, ".cbxcausalcompra", "Seleccione causal");
		listar_clasificacion_proveedores();
		listar_seleccion_area();
		<?php if ($sw_super) { ?>
			listar_responsables_procesos();
			listar_proveedores();
			listar_comites();
			Listar_personas_por_perfil("Per_Dir")
		<?php  } ?>
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>

<!--Start of Tawk.to Script
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5b4eb90e91379020b95ef6ef/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
End of Tawk.to Script-->