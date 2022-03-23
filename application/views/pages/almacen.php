<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/starability-growRotate.min.css">

<?php
	$ruta = $_SERVER['REQUEST_URI'];
	$pos = strrpos($ruta, "index.php/");;
	$ruta =  substr($ruta, $pos+10, strlen($ruta));
    $sw = false;
	if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Alm") $sw = true;
?>

<div class="modal fade" id="modalAlertas" role="dialog">
    <div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content" >
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-file-text"></span> ALERTA ARTÍCULOS</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="">      
					<div class="table-responsive">
						<table id="tblalertas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr ><th class="nombre_tabla" colspan="6">TABLA ARTÍCULOS ALERTAS</th></tr>
								<tr class="filaprincipal ">
									<td>No.</td>
									<td>Código</td>
									<td>Nombre</td>
									<td>Cantidad Actual</td>
									<td >Stock Mínimo</td>
									<td class="opciones_tbl">Gestión</td>
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
				<!-- <button type="submit" class="btn btn-danger active btnAgregar" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button> -->
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
    </div>
</div>

<div id="inicio-user" class="container col-md-12 text-center" >    
    <div class="tablausu col-md-12 text-left solicitudes mod_almacen <?php if(!$sw) echo 'oculto';?>" >
        <div class="table-responsive col-sm-12 col-md-12  tablauser">
			<p class="titulo_menu pointer btn_return" ><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <table id="tabla_solicitudes" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
                <thead class="ttitulo ">
				<tr class="">
					<td colspan="6" class="nombre_tabla">TABLA SOLICITUDES DE ALMACEN <br> 
						<span class="mensaje-filtro" id="textAlerta_solicitudes"></span>
					</td>
					<td class="sin-borde text-right border-left-none" colspan="4" >
						<?php if($sw):?>	
							<span id="btn_encuestas" class="btn btn-default" title="Encuestas" data-toggle="modal" data-target="#modal_encuestas">
								<span class="fa fa-star red" ></span> Encuestas
							</span> 
						<?php endif;?>
						<span id="filtrar_solic_compra" class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#Modal_filtrar_compras">
							<span class="fa fa-filter red" ></span>
							Filtrar
						</span> 
						<span class="btn btn-default" id="btnlimpiar_solicitudes" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-refresh" ></span> Limpiar</span>
					</td>
				</tr>
				<tr class="filaprincipal ">
					<td class="opciones_tbl">Ver</td>
					<td class="opciones_tbl">No.</td>
					<td>Solicitante</td>
					<td>Cargo</td>
					<td>Fecha Solicitud</td>
					<td>Fecha Entrega</td>
					<td>Horas Entrega</td>
					<td>Calificación</td>
					<td>Estado</td>
					<td style="width: 200px;">Gestión</td>
				</tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
	</div>

	<div class="tablausu col-md-12 <?php if($sw) echo 'oculto';?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
          <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
         </div> 
         <div id="container-principal2" class="container-principal-alt">
        <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
        <div class="row">
            <div class=""  id = "solt2">
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_transporte" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Nueva Solicitud</span>
                    </div>
                </div>
            </div>
			<div class=""  id = "solt3">
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/Viaticos_Transporte.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_transporte" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Estados Solicitudes</span>
                    </div>
                </div>
            </div>
        </div>
        <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="modalSolicitud" role="dialog">
    <div class="modal-dialog modal-lg modal-80">
        <form action="#" id="Agregar_Solicitud" method="post" autocomplete="off">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text"></span> Nueva Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="">      
                        <div class="table-responsive">
        
                            <table class="table table-bordered table-hover table-condensed" id="tabla_articulos_solicitados"  cellspacing="0" width="100%">
                                <thead class="ttitulo ">
                                    <tr ><th class="nombre_tabla">TABLA ARTÍCULOS</th><th  colspan="5" class="nombre_tabla"><span class="pointer" id="btsagregar"><span class="fa fa-plus pointer red"></span>Agregar Artículos a esta solicitud</span></th></tr>
                                    <tr class="filaprincipal ">
                                        <td>Artículo</td>
                                        <td class="opciones_tbl">Cantidad</td>
                                        <td>Marca</td>
                                        <td>Referencia</td>
                                        <td>Observaciones</td>
                                        <td class="opciones_tbl_btn">Gestión</td>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <textarea class="form-control oculto"  id="txt_observaciones" cols="1" rows="3" name="observaciones" placeholder="Observaciones"  ></textarea>
         
                    </div> 
				</div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active btnAgregar" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="Modal_administrar" role="dialog">
    <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Módulo</h3>
                </div>
                <div class="modal-body" id="bodymodal">   
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-condensed" id="tabla_categorias"  cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr ><th colspan="3" class="nombre_tabla">TABLA CATEGORIAS</th></tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre</td>
									<td class="opciones_tbl_btn">Gestión</td>
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

<div class="modal fade" id="Modal_perfiles" role="dialog">
    <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-cogs"></span> Asignar Perfiles</h3>
                </div>
                <div class="modal-body" id="bodymodal">   
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-condensed" id="tabla_perfiles"  cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr ><th colspan="3" class="nombre_tabla">TABLA PERFILES</th></tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre</td>
									<td class="opciones_tbl_btn">Gestión</td>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
                <div class="modal-footer" id="footermodal">
                    <!-- <button type="submit" class="btn btn-danger active btnAgregar" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button> -->
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
    </div>
</div>


<div class="modal fade" id="Modal_filtro_inventario" role="dialog" >
	<div class="modal-dialog" >
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row">
					<select id="categoria_filtro" class="form-control inputt cbxcategoria">
						<option value="">Filtrar Artículos por Categoría</option>
					</select> 
					<select id="bodega_filtro" class="form-control inputt cbxbodega">
						<option value="">Filtrar Artículos por Bodega</option>
					</select>
					<!-- <input id="fecha_filtro" class="form-control" value="" placeholder="Filtrar Por Fecha" type="month" name="fecha_filtro"> -->
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active" id="btnreporte_inventario" ><span class="glyphicon glyphicon-ok"></span> Generar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="Modal_filtrar_compras" role="dialog" >
	<div class="modal-dialog" >
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row">
					<select id="estado_filtro" class="form-control inputt cbxestado">
						<option value="">Filtrar Solicitudes por Estado</option>
					</select>
					<input id="fecha_filtro" class="form-control" value="" placeholder="Filtrar Por Fecha" type="month" name="fecha_filtro">
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active" id="btnreporte_solicitudes" ><span class="glyphicon glyphicon-ok"></span> Generar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<?php if($sw){?>
<div class="modal fade" id="modal_restar_articulos" role="dialog" >
	<div class="modal-dialog" >
		<form action="#" id="restar_articulos"  method="post">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-archive"></span> <span class="t_sumar_restar"> Restar Artículos</span></h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<input type="number" name="cantidad" id="txtrest_cantidad" class="form-control inputt2" min="1" required placeholder="Cantidad">
						<textarea name="observaciones" id="txtrest_observacion" class="form-control" placeholder="Observaciones"></textarea>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="submit" class="btn btn-danger active" id="btnrestar" ><span class="glyphicon glyphicon-ok"></span><span class="sumar_restar"> Restar</span></button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php }?>

<div class="modal fade con-scroll-modal" id="modalInfo_Solicitud" role="dialog">
<div class="modal-dialog modal-lg modal_80">
	<!-- Modal content-->
	<div class="modal-content" >
		<div id="headermodal" class="modal-header">
			<button type="button" class="close" data-dismiss="modal"> X </button>
			<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
		</div>
		<div id="bodymodal" class="modal-body">
			<div class="table-responsive">
				<div class="error form-group has-error text-center oculto"></div>
				<table class="table table-bordered table-condensed tabla_info_solicitud">
					<tr><th class="nombre_tabla" colspan="2"> Informacion General</th></tr>
					<tr><td class="ttitulo">Solicitante: </td>
						<td>
							<!-- <span class="info_sol_sol"></span> -->
							<?php if($sw){?>
								<div id="detalle_persona_solicita" class="pointer red btn" title="Detalle Persona" data-toggle="popover" data-trigger="hover">
								<span class="info_sol_sol"></span>
								</div>
							<?php }else{?>
								<span class="info_sol_sol"></span>
							<?php }?>
						</td>
					</tr>
					<tr><td class="ttitulo">Cargo:</td><td class="info_sol_dep"></td></tr>
					<tr><td class="ttitulo">Fecha de Solicitud: </td><td class="info_sol_fec"></td></tr>
					<tr id="row_fecha_entrega"><td class="ttitulo">Fecha de Entrega: </td><td class="info_sol_ent"></td></tr>
					<tr>
						<td class="ttitulo">Estado:</td>
						<td>
							<div class="row" >
								<div class="col-md-<?php echo ($sw) ? '6' : '12'; ?> info_sol_est"></div>
								<div class="col-md-6">
								<?php if ($sw) {?>
									<span id="btn_historial" class="ttitulo" title="Mostrar historial" data-toggle="modal" data-target="#modal_historial_estados">
										<span class="fa fa-history red"></span>
										Ver Historial
									</span>
								<?php }?>
								</div>
							</div>
						</td>
					</tr>
					<tr id="star_rating">
						<td class="ttitulo">Calificaci&oacute;n:</td>
						<td>
							<span class="fa fa-star ratingstar"  id="1one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar"  id="2one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar"  id="3one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar"  id="4one" style="font-size:30px;"></span>
							<span class="fa fa-star ratingstar"  id="5one" style="font-size:30px;"></span> 
						</td>
					</tr>
					<tr id="observacion_solicitud" class="hide"><td class="ttitulo">Observaci&oacute;n:</td><td class="info_sol_obs"></td></tr>
				</table>
			</div>
			<div class="table-responsive">
				<div class="error form-group has-error text-center oculto"></div>
				<table id="tbl_articulos_solicitud" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
					<thead class="ttitulo ">
						<tr class="">
							<td colspan="4" class="nombre_tabla">ARTÍCULOS SOLICITADOS</td>
							<td class="sin-borde text-right border-left-none" colspan="3" id="gestion"></td>
						</tr>
						<tr class="filaprincipal ">
							<td class="ttitulo opciones_tbl">Ver</td>
							<td class="ttitulo opciones_tbl">Código</td>
							<td class="ttitulo">Nombre</td>
							<td class="ttitulo opciones_tbl">Cantidad</td>
							<td class="ttitulo opciones_tbl">Unidades</td>
							<td class="ttitulo opciones_tbl_btn">Gestión</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div id="panel_firma" style="width: 410px;">
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
											<button type="submit" class="btn btn-danger">Confirmar</button>
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
			</div>
		</div>
		<div class="modal-footer" id="footermodal">
			<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
		</div>
	</div>
</div>
</div>

<div class="modal fade con-scroll-modal" id="modal_detalle_articulo" role="dialog">
<div class="modal-dialog modal-md">
	<!-- Modal content-->
	<div class="modal-content" >
		<div id="headermodal" class="modal-header">
			<button type="button" class="close" data-dismiss="modal"> X </button>
			<h3 class="modal-title"><span class="fa fa-list"></span> Detalle Artículo</h3>
		</div>
		<div id="bodymodal" class="modal-body">
			<div class="table-responsive">
				<div class="error form-group has-error text-center oculto"></div>
				<table class="table table-bordered table-condensed tabla_info_solicitud">
					<tr><th class="nombre_tabla" colspan="2"> Informacion General del Ar&iacute;culo</th></tr>
					<tr>
						<td class="ttitulo">C&oacute;digo: </td>
						<td class="info_art_cod"></td>
					</tr>
					<tr><td class="ttitulo">Nombre:</td><td class="info_art_nom"></td></tr>
					<tr><td class="ttitulo">Unidades:</td><td class="info_art_uni"></td></tr>
					<tr><td class="ttitulo">Marca: </td><td class="info_art_mar"></td></tr>
					<tr><td class="ttitulo">Referencia:</td><td class="info_art_ref"></td></tr>
					<tr><td class="ttitulo">Cantidad:</td><td class="info_art_can"></td></tr>
					<tr><td class="ttitulo">Observaci&oacute;n:</td><td class="info_art_obs"></td></tr>
				</table>
			</div>
		</div>
		<div class="modal-footer" id="footermodal">
			<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="Mostrar_detalle_persona" role="dialog">
    <div class="modal-dialog" >
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row"  style="width: 80%">
                    <div class="error text-center"></div>
                    <div id="datos_perso" class="">
                        <table class="table">
                            <tr class="nombre_tabla"><td colspan="">Datos</td></tr>
                            <tr><td class="foto_perso margin0" colspan=""></td></tr>
                            <tr><td class="nombre_perso"></td></tr>
                            <tr><td class="apellido_perso"></td></tr>
                            <tr><td class="tipo_id_perso"></td></tr>
                            <tr><td class="identi_perso"></td></tr>
                            <tr><td class="cargo_perso"></td></tr>
                            <tr><td class="depar_perso"></td></tr>
                            <tr><td class="ubica_perso"></td></tr>
                            <tr><td class="celular"></td></tr>
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

<div class="modal fade scroll-modal" id="modalArticulos" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="Agregar_Articulos" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
					<div>
						<h3 class="modal-title">
							<span class="fa fa-shopping-cart"></span> 
							<span id="art_titulo">Agregar Artículos</span>
						</h3>
					</div>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div class="error form-group has-error text-center oculto"></div>
						<?php if($sw){?>
                        <input id="txtcodigo_art" type="text" name="codigo" class="form-control inputt2 div_inv" placeholder="Código Artículo">
						<?php } ?>
						<div id="div_articulo" class="input-group agro">
                            <input name="articulo" id="input_articulo"  class="oculto ">
                            <span class="form-control text-left pointer sel_art" id="cod_articulo">Seleccione Artículo</span>
                            <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="sele_cod_art" title="Buscar Artículo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                        </div>
						<input id="txtcantidad" type="number" name="cantidad_art" class="form-control inputt2" placeholder="Cantidad Artículos" min="1" required>
						<?php if($sw){?>
                        <input id="txtnombre_articulo" type="text" name="nombre_art" class="form-control inputt2 div_inv" placeholder="Nombre Artículo" required>
						<input id="txtunidades" type="text" name="unidades_art" class="form-control inputt2 div_inv" placeholder="Unidades del Artículo" required>
						<select  id="cbxcategoria" class="form-control cbxcategoria div_inv" required="true" name="categoria">
                            <option value="">Seleccione Categoria</option>
                        </select>
						<input id="txtstock" type="number" name="stock" class="form-control inputt2 div_inv" placeholder="Stock Mínimo" min="1" required>
                        <select  id="cbxbodega" class="form-control cbxbodega div_inv" required="true" name="bodega">
                            <option value="">Seleccione Bodega</option>
                        </select> 

                        <div class="agro agrupado div_inv">
                            <div class="input-group">
                                <input type="text" name="marca" id="txtmarca_articulo" class="form-control inputt2 " placeholder="Marca" >
                                <span class="input-group-addon">-</span>
                                <input type="text" name="referencia_art" id="txtreferencia" class="form-control inputt2 " placeholder="Referencia" >

                            </div>
                        </div>
                        
                        <div class="agro agrupado div_inv">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" name="valor" id="txtvalor" class="form-control inputt2 div_inv" placeholder="Valor Artículo" min="1" required>
                            </div>
                        </div>
						<?php }?>
						<textarea name="observaciones" id="txtobservaciones" class="form-control" placeholder="Observaciones"></textarea>
                    </div> 
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active btnAgregar" ><span class="glyphicon glyphicon-floppy-disk"></span> <span class="msgboton">Agregar</span></button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div> 




<div class="modal fade con-scroll-modal" id="modalInfo_Articulo" role="dialog">

    <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Artículo</h3>
                </div>
                <div class="modal-body" id="bodymodal">
					<div class="table-responsive">
						<div class="error form-group has-error text-center oculto"></div>
						<table class="table table-bordered table-condensed">
							<tr>
								<th class="nombre_tabla" colspan="4"> Informacion General</th>
							</tr>
							<tr>
								<td class="ttitulo">Nombre: </td><td colspan='3' class="info_art_nom"></td>
							</tr>
							<tr>
								<td class="ttitulo">Código</td><td  class="info_art_cod"></td>
								<td class="ttitulo">Valor:</td><td class="info_art_val"></td>
							</tr>
							<tr>
								<td class="ttitulo">Cantidad: </td><td class="info_art_can"></td>
								<td class="ttitulo">Stock Mínimo:</td><td class="info_art_sto"></td>
							</tr>
							<tr>
								<td class="ttitulo">Categoría:</td><td class="info_art_cat"></td>
								<td class="ttitulo">Bodega:</td><td class="info_art_bod"></td>
							</tr>
							<tr>
								<td class="ttitulo">Marca: </td><td class="info_art_mar"></td>
								<td class="ttitulo">Referencia:</td><td class="info_art_ref"></td>
							</tr>
							<tr>
								<td class="ttitulo">Observaciones:</td><td colspan='3' class="info_art_obs"></td>
							</tr>
                        </table>
					</div>
					<div class="table-responsive">
					<div class="error form-group has-error text-center oculto"></div>
						<table class="table table-bordered table-hover table-condensed table-responsive" id="tblhistorial"  cellspacing="0" width="100%" >
							<thead class="ttitulo ">
								<tr >
									<th colspan="5" class="nombre_tabla">HISTORIAL DEL ARTÍCULO</th>
								</tr>
								<tr class="filaprincipal ">
									<td class="ttitulo opciones_tbl">Fecha</td>
									<td class="ttitulo">Cant. Anterior</td>
									<td class="ttitulo">Cantidad</td>
									<td class="ttitulo">Usuario</td>
									<td class="ttitulo">Observación</td>
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

<div class="modal fade scroll-modal" id="modalModificarSolicitud" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="modificar_solicitud" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span> Modificar Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
						<label for="cbxmod_estado" class="mdflabel"><b>Estado de la Solicitud</b></label>
                        <select name="cbxmod_estado" required class="form-control inputt cbxestado" id="cbxmod_estado">
                            <option>Seleccione Estado</option>
                        </select>
                    </div> 
				</div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active btnAgregar" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if($sw){?>
<div class="modal fade scroll-modal" id="modificar_Articulos" role="dialog" >
    <div class="modal-dialog">
        <form action="#" id="modificar_Articulo" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-edit"></span> Modificar Artículo</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div class="error form-group has-error text-center oculto"></div>
                        <input id="txtinput_codigo" name="codigo" type="text" class="form-control inputt2 input_10" required placeholder="Codigo">
                        <input id="txtmod_nom_art" type="text" name="nombre_articulo" class="form-control inputt2 " required placeholder="Nombre">
                        <select  id="cbxmod_categoria" class="form-control cbxcategoria" required="true" name="categoria">
                            <option value="">Seleccione Categoria</option>
						</select>
                        <select  id="cbxmod_bodega" class="form-control cbxbodega" required="true" name="bodega">
                            <option value="">Seleccione Bodega</option>
						</select>
						<input id="txtmod_marca" type="text" name="marca" class="form-control inputt2" placeholder="Marca">
						<input id="txtmod_ref" type="text" name="referencia_art" class="form-control inputt2" placeholder="Referencia">
						<div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon">Stock mínimo</span>
                                <input id="txtmod_stock" type="number" name="stock" class="form-control inputt2" min="1" required placeholder="Stock Mínimo">
                            </div>
                        </div>
						<div class="agro agrupado">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input id="txtmod_valor" type="number" name="valor" class="form-control inputt2" min="0" required placeholder="Valor">
                            </div>
                        </div>
						<textarea name="observaciones" id="txtmod_observaciones" class="form-control" placeholder="Observaciones" ></textarea>
                    </div> 
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active btnModifica" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php }?>

<form id="guardar_calificacion" method="post" action="#">
<div class="modal fade" id="Modal_calificar_Solicitud" role="dialog">

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content" >  
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-star"></span> Calificar Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
					<div class="alert alert-warning" role="alert">
						<p><b class="ttitulo">Nota: </b>Tener en cuenta al momento de calificar que el Método de calificación se evalúa de la siguiente manera:</p>
						<ul>
							<li>1 estrella: Muy malo</li>
							<li>2 estrellas: Malo</li>
							<li>3 estrellas: Regular</li>
							<li>4 estrellas: Bueno</li>
							<li>5 estrellas: Excelente</li>
						</ul>
					</div>
                    <div style="width: 100%;" >
						<div style="width: 30%; margin: 0 auto;">
							<fieldset class="starability-growRotate"> 
								<input type="radio" id="rate1" name="rating" value="1" />
								<label for="rate1" title="Muy Malo">1 stars</label>

								<input type="radio" id="rate2" name="rating" value="2" />
								<label for="rate2" title="Malo">2 stars</label>

								<input type="radio" id="rate3" name="rating" value="3" />
								<label for="rate3" title="Regular">3 stars</label>

								<input type="radio" id="rate4" name="rating" value="4" />
								<label for="rate4" title="Bueno">4 stars</label>

								<input type="radio" id="rate5" name="rating" value="5" checked/>
								<label for="rate5" title="Excelente">5 star</label>
							</fieldset>
						</div>
						<textarea  class="form-control" placeholder="Observación" name="observacion"></textarea>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">


                    <button type="submit" class="btn btn-danger active" id="btn_calificar" ><span class="glyphicon glyphicon-star"></span> Calificar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            

        </div>


    </div>

</div>
</form>

<div class="modal fade" id="Buscar_Articulo" role="dialog" >
    <div class="modal-dialog">
        <form action="#" id="FrmBuscar_Articulo" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"> <span class="	fa fa-search"></span> Buscar Artículos</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div>
						<div class="alert alert-warning" role="alert">
							<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
							<p>En caso de que el artículo que desea no se encuentre en el inventario, por favor realizar la correspondiente solicitud con el departamento de compras.</p>
						</div>
                        <div class="input-group agro col-md-8">
                            <input name="cod_art" type="hidden" id="input_codigo_art">
                            <input type="text" class="form-control inputt2" name="articulo" placeholder="Buscar Artículo" id="txtarticulo" autocomplete="off">
                            <span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_art" title="Buscar Artículo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                        </div><br>
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_buscar_articulos"  cellspacing="0" width="100%" >
                            <thead class="ttitulo ">
                                <tr ><th colspan="4" class="nombre_tabla">TABLA ARTÍCULOS</th></tr>
                                <tr class="filaprincipal ">
									<td class="opciones_tbl">Id</td>
                                    <td>Nombre</td>
                                    <td class="opciones_tbl">Unidades</td>
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

<div class="modal fade con-scroll-modal" id="modal_historial_estados" role="dialog">
	<div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content" >
			<div id="headermodal" class="modal-header">
				<button type="button" class="close" data-dismiss="modal"> X </button>
				<h3 class="modal-title"><span class="fa fa-history"></span> Historial de Estados</h3>
			</div>
			<div id="bodymodal" class="modal-body">
				<div class="table-responsive">
					<div class="error form-group has-error text-center oculto"></div>
					<table id="tbl_historial_estados" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
						<thead class="ttitulo ">
							<tr class="">
								<td colspan="4" class="nombre_tabla">HISTORIAL DE ESTADOS</td>
							</tr>
							<tr class="filaprincipal ">
								<td class="ttitulo opciones_tbl">No.</td>
								<td class="ttitulo">Estado</td>
								<td class="ttitulo">Fecha Modificación</td>
								<td class="ttitulo">Responsable</td>
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

<?php if($sw):?>	
	<div class="modal fade con-scroll-modal" id="modal_encuestas" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" >
				<div id="headermodal" class="modal-header">
					<button type="button" class="close" data-dismiss="modal"> X </button>
					<h3 class="modal-title"><span class="fa fa-star"></span> ENCUESTAS</h3>
				</div>
				<div id="bodymodal" class="modal-body">
					<template>
						<div>
							<div id="ratingstar-num"></div>
							<div>
								<span class="fa fa-star ratingstar"  id="1one" style="font-size:30px;"></span>
								<span class="fa fa-star ratingstar"  id="2one" style="font-size:30px;"></span>
								<span class="fa fa-star ratingstar"  id="3one" style="font-size:30px;"></span>
								<span class="fa fa-star ratingstar"  id="4one" style="font-size:30px;"></span>
								<span class="fa fa-star ratingstar"  id="5one" style="font-size:30px;"></span>
							</div>
						</div>
					</template>
					<div class="table-responsive">
						<div class="error form-group has-error text-center oculto"></div>
						<table id="tabla_encuestas" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="3" class="nombre_tabla">ENCUESTAS</td>
									<td class="sin-borde text-right border-left-none" colspan="2" rowspan="1">
										<span id="filtrar_encuestas" class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#Modal_filtrar_encuestas">
											<span class="fa fa-filter red" ></span> Filtrar
										</span>
										<span class="btn btn-default" id="btnlimpiar_encuestas" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-refresh" ></span> Limpiar</span>
									</td>
								</tr>
								<tr class="filaprincipal ">
									<td class="ttitulo opciones_tbl">No.</td>
									<td class="ttitulo">Solicitante</td>
									<td class="ttitulo">Fecha</td>
									<td class="ttitulo">Calificación</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="table-responsive margin1">
						<table class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="2" class="nombre_tabla">PROMEDIO DE LAS ENCUESTAS</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="red">Promedio</td>
									<td id="prom_stars"></td>
								</tr>
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
	<div class="modal fade" id="Modal_filtrar_encuestas" role="dialog" >
		<div class="modal-dialog" >
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-filter"></span>Filtrar Encuestas</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">	
						<div class="agro agrupado">
							<div class="input-group d-flex">
								<span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha inicio</span>
								<input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de inicio." class="form-control sin_margin" name="fecha_inicio_encuestas" id="fecha_inicio_encuestas" required>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="agro agrupado">
							<div class="input-group">
								<span class="input-group-addon pointer" style='background-color:white'><span class='fa fa-calendar red'></span> Fecha fin</span>
								<input type="date" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccione fecha de terminación." class="form-control sin_margin" name="fecha_fin_encuestas" id="fecha_fin_encuestas" required>
							</div>
						</div>					
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-danger active" id="generar_filtro_encuestas" ><span class="glyphicon glyphicon-ok"></span> Generar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>
<?php endif;?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/2.9.0/jquery.serializejson.min.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>
<script>
    $(document).ready(function () {
		gestionar_ruta('<?php echo "$_SERVER[REQUEST_URI]" ?>');
        inactivityTime();
		<?php if ($id > 0) {?>
			$('.solicitudes').fadeIn();
			$('#menu_principal').css('display', 'none');
			$(".div_inv").removeAttr('required').hide();
		<?php } ?>
        Cargar_parametro_buscado_aux(40, ".cbxestado", "Seleccione Estado");
		Cargar_parametro_buscado_aux(41, ".cbxcategoria", "Seleccione Categoría");
		listar_solicitudes(<?php echo $id; ?>);
		$("#txtinput_codigo").maxLength = 10;
    });
</script>
