
<div class="tablausu col-md-12 text-left inventario" id="inventario_almacen">
	<div class="table-responsive col-sm-12 col-md-12  tablauser">
		<p id="inicio_return" class="titulo_menu pointer" ><span class="fa fa-reply-all naraja"></span> Regresar</p>
		<table id="tabla_articulos" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%" >
			<thead class="ttitulo ">
				<tr>
					<td colspan="4" class="nombre_tabla">TABLA INVENTARIO ALMACEN <br>
						<span class="mensaje-filtro oculto" id="textAlerta">
							<span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados, si desea ver toda la información disponible debe presionar el botón de <b>Limpiar</b>
						</span>
					</td>
					<td class="sin-borde text-right border-left-none" colspan="6" >
						<span id="btncompras" class="btn btn-default oculto" title="Compras" data-toggle='popover' data-trigger='hover'> 
							<span class="btn-Efecto-men fa fa-shopping-cart red"></span> 
							Compras
						</span>
						<span id="btnadministrar" class="btn btn-default" title="Administrar" data-toggle='popover' data-trigger='hover'> 
							<span class="btn-Efecto-men fa fa-cogs red"></span> 
							Administrar
						</span>
						<span id="btnabrir" class="btn btn-default btnAgregar" title="Agregar Artículo" data-toggle='popover' data-trigger='hover'> 
							<span class="btn-Efecto-men fa fa-plus red"></span> 
							Agregar
						</span>
						<span id="btnmodificar" class="btn btn-default btnModifica" title="Modificar Artículo" data-toggle='popover' data-trigger='hover'> 
							<span class="btn-Efecto-men fa fa-wrench red"></span> 
							Modificar
						</span>
						<span id="filtrar_inventario" class="btn btn-default" title="Filtrar Artículo" data-toggle="modal" data-target="#Modal_filtro_inventario">
							<span class="fa fa-filter red" ></span> 
							Filtrar
						</span> 
						<span id="btnLimpiar_articulos" class="btn btn-default" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> 
							<span class="fa fa-refresh red" ></span> 
							Limpiar
						</span>
					</td>
				</tr>
				<tr class="filaprincipal ">
					<td class="opciones_tbl">***</td>
					<td class="opciones_tbl">Código</td>
					<td>Nombre</td>
					<td>Valor</td>
					<td>Bodega</td>
					<td>Cantidad</td>
					<td>Unidades</td>
					<td>Stock Mínimo</td>
					<td>Categoría</td>
					<td style="width: 150px;">Gestión</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

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
									<td class="opciones_tbl">***</td>
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
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="modal_administrar" role="dialog">
    <div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content" >
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-file-text"></span> Administrar Módulo</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="">      
					<div class="table-responsive">
						<table id="tbl_unidades" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th class="nombre_tabla" colspan="2">TABLA UNIDADES</th>
									<th class="sin-borde text-right border-left-none"><span id="btn_agregar_unidad" class="fa fa-plus red btn"></span></th>
								</tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre</td>
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
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
    </div>
</div>

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
								<div id="detalle_persona_solicita" class="pointer red btn" title="Detalle Persona" data-toggle="popover" data-trigger="hover">
								<span class="info_sol_sol"></span>
								</div>
							</td>
						</tr>
						<tr><td class="ttitulo">Dependencia:</td><td class="info_sol_dep"></td></tr>
						<tr><td class="ttitulo">Fecha de Solicitud: </td><td class="info_sol_fec"></td></tr>
						<tr id="row_fecha_entrega"><td class="ttitulo">Fecha de Entrega: </td><td class="info_sol_ent"></td></tr>
						<tr>
							<td class="ttitulo">Estado:</td>
							<td>
								<div class="row" >
									<div class="col-md-6 info_sol_est"></div>
									<div class="col-md-6">
										<span id="btn_historial" class="ttitulo" title="Mostrar historial" data-toggle="modal" data-target="#modal_historial_estados">
											<span class="fa fa-history red"></span>
											Ver Historial
										</span>
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
								<td class="ttitulo opciones_tbl">***</td>
								<td class="ttitulo opciones_tbl">Código</td>
								<td class="ttitulo">Nombre</td>
								<td class="ttitulo opciones_tbl">Cantidad</td>
								<td class="ttitulo opciones_tbl_btn">***</td>
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
                        <input id="txtcodigo_art" type="text" name="codigo" class="form-control inputt2 div_inv" placeholder="Código Artículo">
						<div id="div_articulo" class="input-group agro">
                            <input name="articulo" id="input_articulo"  class="oculto ">
                            <span class="form-control text-left pointer sel_art" id="cod_articulo">Seleccione Artículo</span>
                            <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="sele_cod_art" title="Buscar Artículo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                        </div>
						<input id="txtcantidad" type="number" name="cantidad_art" class="form-control inputt2" placeholder="Cantidad Artículos" min="1" required>
                        <input id="txtnombre_articulo" type="text" name="nombre_art" class="form-control inputt2 div_inv" placeholder="Nombre Artículo" required>
						<select id="cbxunidades" class="form-control cbxunidades div_inv" required="true" name="unidades_art">
                            <option value="">Seleccione Unidades del Artículo</option>
                        </select>
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

<div class="modal fade scroll-modal" id="modificar_Articulos" role="dialog" >
    <div class="modal-dialog">
        <form id="modificar_Articulo" method="post">
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
						<select  id="cbxmod_unidades" class="form-control cbxunidades" required="true" name="unidades_art">
                            <option value="">Seleccione Unidades del Artículo</option>
						</select>
                        <select  id="cbxmod_categoria" class="form-control cbxcategoria" required="true" name="categoria">
                            <option value="">Seleccione Categoria</option>
						</select>
                        <select  id="cbxmod_bodega" class="form-control cbxbodega" required="true" name="bodega">
                            <option value="">Seleccione Bodega</option>
						</select>
						<input id="txtmod_marca" type="text" name="marca" class="form-control inputt2" placeholder="Marca">
						<input id="txtmod_ref" type="text" name="referencia_art" class="form-control inputt2" placeholder="Referencia">
						<input id="txtmod_stock" type="number" name="stock" class="form-control inputt2" min="1" required placeholder="Stock Mínimo">
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
									<td class="opciones_tbl_btn">***</td>
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
                    <h3 class="modal-title"><span class="fa fa-cogs"></span> Asignar Bodegas</h3>
                </div>
                <div class="modal-body" id="bodymodal">   
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-condensed" id="tabla_perfiles"  cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr ><th colspan="3" class="nombre_tabla">TABLA PERFILES</th></tr>
								<tr class="filaprincipal ">
									<td class="opciones_tbl">No.</td>
									<td>Nombre</td>
									<td class="opciones_tbl_btn">***</td>
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
								<th class="nombre_tabla" colspan="6"> Informacion General</th>
							</tr>
							<tr>
								<td class="ttitulo">Nombre: </td><td colspan='5' class="info_art_nom"></td>
							</tr>
							<tr>
								<td class="ttitulo">Código</td><td colspan='2' class="info_art_cod"></td>
								<td class="ttitulo">Valor:</td><td colspan='2' class="info_art_val"></td>
							</tr>
							<tr>
								<td class="ttitulo">Cantidad: </td><td class="info_art_can"></td>
								<td class="ttitulo">Stock Mínimo:</td><td class="info_art_sto"></td>
								<td class="ttitulo">Unidades:</td><td class="info_art_uni"></td>
							</tr>
							<tr>
								<td class="ttitulo">Categoría:</td><td colspan='2' class="info_art_cat"></td>
								<td class="ttitulo">Bodega:</td><td colspan='2' class="info_art_bod"></td>
							</tr>
							<tr>
								<td class="ttitulo">Marca: </td><td colspan='2' class="info_art_mar"></td>
								<td class="ttitulo">Referencia:</td><td colspan='2' class="info_art_ref"></td>
							</tr>
							<tr>
								<td class="ttitulo">Observaciones:</td><td colspan='5' class="info_art_obs"></td>
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
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active" id="btnreporte_inventario" ><span class="glyphicon glyphicon-ok"></span> Generar</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_compras" role="dialog">
    <div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content" >
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-shopping-cart"></span> Compras</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<nav class="navbar navbar-default" id="nav_compras">
					<div class="container-fluid">
						<ul class="nav navbar-nav">
							<li class="pointer" id="Bod_Ase"><a><span class="fa fa-trash red"></span> Aseo</a></li>
							<li class="pointer" id="Bod_Caf"><a><span class="fa fa-coffee red"></span> Cafetería</a></li>
							<li class="pointer" id="Bod_Fer"><a><span class="fa fa-wrench red"></span> Ferreteria</a></li>
							<li class="pointer" id="Bod_Pap"><a><span class="fa fa-file-text-o red"></span> Papeleria</a></li>
						</ul>
					</div>
				</nav>
				<div class="agrupado" style="margin-bottom: 10px;">
					<div class="input-group" id="div_jefe">
						<input type="text" class="form-control sin_margin sin_focus buscar_jefe_compra" placeholder="Seleccionar Jefe" id='jefe_compras' required>
						<span class="input-group-addon pointer buscar_jefe_compra" id='btn_buscar_jefe_com' style='background-color:white'><span class='fa fa-search red'></span> Jefe Directo</span>
					</div>
				</div>
				<div class="agrupado" style="margin-bottom: 10px;">
					<select id="cbx_cod_sap" class="form-control" required="true" name="cod_sap_compras">
          	<option value="">Seleccione Codigo SAP</option>
					</select>
				</div>
				<br>
				<div class="table-responsive">
					<div id="container_tbl_articuloss_compras">
						<table class="table table-bordered table-hover table-condensed" id="tbl_articulos_compras" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr>
									<th colspan="5" class="nombre_tabla">TABLA ARTICULOS</th>
								</tr>
								<tr class="filaprincipal ">
									<td>Codigo</td>
									<td>Nombre</td>
									<td>Cantidad</td>
									<td>Stock Minimo</td>
									<td class="opciones_tbl">Acción</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-danger active btnAgregar" id="enviar_compra"><span class="glyphicon glyphicon-floppy-disk"></span> Enviar Compra</button>
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_articulos" role="dialog" >
  <div class="modal-dialog">
  <form action="#" id="form_agregar_articulos" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Agregar Articulo</span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
			<div class="alert alert-info oculto" id="alert_compras">
				Este articulo cuenta con un proceso activo en el módulo de Compras.
			</div>
        	<div class="row">
				<div class="agro agrupado">
					<div class="input-group">
						<input type="text" name="nombre_art_comp" class="form-control" placeholder="Nombre Artículo" id="nombre_art_comp" required>
						<span class="input-group-addon">-</span>
						<input type="number" name="cantidad_art_comp" class="form-control" placeholder="Cantidad Artículos" id="cantidad_art_comp" min="1" required>
					</div>
				</div>
				<div class="agro agrupado">
					<div class="input-group">
						<input type="text" name="marca_art_comp" class="form-control" placeholder="Marca (opcional)" id="marca_art_comp">
						<span class="input-group-addon">-</span>
						<input type="text" name="referencia_art_comp" class="form-control" placeholder="Referencia (opcional)" id="referencia_art_comp">
					</div>
				</div>
				<textarea name="observaciones_comp" class="form-control" placeholder="Descripción" required id="observaciones_comp"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="fa fa-check"></span> Agregar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="modal_buscar_jefe" role="dialog">
	<div class="modal-dialog modal-md">
		<form id="form_buscar_persona" method="post">
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
					<input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del jefe">
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
						<td>Nombre</td>
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
		</form>
    </div>
</div>


<script>
	$(document).ready(()=>{
		gestionar_ruta('<?php echo "$_SERVER[REQUEST_URI]" ?>');
		Cargar_parametro_buscado_aux(41, ".cbxcategoria", "Seleccione Categoría");
		Cargar_parametro_buscado(186, ".cbxunidades", "Seleccione Unidades del Artículo");
		listar_articulos();
	});
</script>
