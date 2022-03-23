<style>
	header, footer{
		display: none;
	}
	.contenido{
		height:70%;
		box-shadow: 15px -10px 3px #D3D2D2;
		-webkit-box-shadow: -15px -10px 20px #D3D2D2;
		-moz-box-shadow: 15px -10px 3px #D3D2D2;
		border-top-right-radius:0px;
		border-top-left-radius:0px;
	}

	.btn_iniciar{
		padding:10px;
		background-color: #6e1f7c;
		display: flex;
		z-index: 2;
		justify-content: center;
		align-items: center;
	}

	#footer_encuesta{
		margin-top: -2%;
		height: 8%;
		background-color: #6e1f7c;
		display: flex;
		justify-content: left;
        align-items: center;
		box-shadow: 15px -10px 3px #D3D2D2;
		-webkit-box-shadow: -15px -10px 20px #D3D2D2;
		-moz-box-shadow: 15px -10px 3px #D3D2D2;
	}

	.barra{
		margin:25px;
		background-color:#6e1f7c;
		border:solid 1px white;
	}

	#header_encuesta{
		height: 15%;
		background-color: white;
		display: flex;
		justify-content: center;
        align-items: center;
		border-top-right-radius: 4px;
    	border-top-left-radius: 4px;
	}
	#container_principal{
		width: 60%;
		margin:0 auto;
		margin-top: 2%;
		box-shadow: 15px -10px 3px #D3D2D2;
		-webkit-box-shadow: -15px -10px 20px #D3D2D2;
		-moz-box-shadow: 15px -10px 3px #D3D2D2;
	}
	.contenido::-webkit-scrollbar {
		width: 8px;     /* Tamaño del scroll en vertical */
		height: 8px;    /* Tamaño del scroll en horizontal */
	}
	.contenido::-webkit-scrollbar-thumb {
		background: #E7E6E6;
		border-radius: 4px;
	}
	/* Cambiamos el fondo y agregamos una sombra cuando esté en hover */
	.contenido::-webkit-scrollbar-thumb:hover {
		background: #b3b3b3;
		box-shadow: 0 0 2px 1px rgba(0, 0, 0, 0.2);
	}
	.centrar{
		display: flex;
		justify-content: center;
        align-items: center;
	}
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/html2canvas.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/th.css">
<div class="container col-md-12  text-center" id="inicio-user">	
	<div id="container_principal">
		<div class="col-md-12" id="header_encuesta">
			<h3 class="titulo_menu"><span class="fa fa-question-circle"></span>Acta de Retroalimentación</h3>	
		</div>
		<div class="contenido panel con-scroll-modal" id="contenido_actas">
		<?php if($estado_actas){ ?>
			<div class="col-md-12 text-center">
				<div>
					<img src="<?php echo base_url() ?>/imagenes/retroalimentacion.png" alt="..." style='width:20%;'>
				</div>
			</div>
			<div class="col-md-12 centrar">
				<div class="col-md-11 table-responsive">
					<table class="table table-bordered table-condensed table-hover table-responsive" id="tabla_personal_actas"  cellspacing="0" width="100%">
					<thead>
						<tr>
							<th class="nombre_tabla" colspan="4">Personal a Cargo</th>
						</tr>
						<tr class="filaprincipal">
							<td class="opciones_tbl">Ver</td>
							<td>Nombre</td>
							<td>Identificación</td>
							<td>Acciones</td>
						</tr>
					</thead>
					<tbody>
					</tbody>	
					</table>
					<br/>
				</div>	
			</div>
			<?php }else{ ?>	
				<div class="col-md-12">
					<img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>RETROALIMENTACIÓN FINALIZADA</b></h4>
					</br>
					<a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>
			<?php } ?>
		</div>	

		<div class="col-md-12" id="footer_encuesta">
			<div class="col-md-4 text-right">
			 	<h5 class='nombre_evaluado' style="color:#fff"></h5>	
			</div>
			<div class="col-md-4">
				<div class="progress barra">
					<div class="progress-bar" id='bar_estado_acta' role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="background-color:white;color:#d57e1c;width:<?php echo  $progress ?>">
						<b><span class="text_barra_acta"><?php echo $progress ?></span></b>
					</div>
				</div>
			</div>	
		</div>

	<div class="modal fade" id="modal_detalle_evaluacion" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Evaluación</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<table class="table table-bordered table-condensed">
						<tr>
							<th class="nombre_tabla" colspan="3"> Datos del Jefe Evaluador</th>
							<td colspan="2" class="sin-borde text-right border-left-none">
								<a href="<?php echo base_url()?>" target="_blank" class="btn btn-default oculto" id="btnActa"><span class="fa fa-folder-open-o red"></span> Retroalimentación</a>
							</td>
						</tr>
						<tr>
							<td class="ttitulo">Funcionario: </td><td class="info_jefe"></td>
							<td class="ttitulo">Identificación: </td><td class="info_identificacion_jefe"></td>
						</tr>
						<tr>
							<td class="ttitulo">Dependencia:</td><td class="info_dependencia_jefe"></td>
							<td class="ttitulo">Cargo:</td><td class="info_cargo_jefe"></td>
						</tr>
						<tr>
							<th class="nombre_tabla" colspan="4"> Datos del Funcionario Evaluado</th>
						</tr>
						<tr>
							<td class="ttitulo">Nombre: </td><td class="info_funcionario"></td>
							<td class="ttitulo">Identificación: </td><td class="info_identificacion"></td>
						</tr>
						<tr>
							<td class="ttitulo">Dependencia:</td><td class="info_dependencia"></td>
							<td class="ttitulo">Cargo:</td><td class="info_cargo"></td>
						</tr>
						<tr>
							<td class="ttitulo">Método de evaluación:</td><td colspan='3' class="info_metodo"></td>
						</tr>
						<tr>
							<td class="ttitulo">Periodo:</td><td class="info_periodo"></td>
							<td class="ttitulo">Fecha Retroalimentación:</td><td class="info_fecha_retro"></td>
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

	<div class="modal fade scroll-modal" id="modal_retroalimentacion" role="dialog">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> <span > Retroalimentación de Administrativos</span></h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
						<div class="col-md-12" id="info_evaluado"></div>
                            <div id="seleccion_tipo">
                                <div class="row" style="width:100%">
                                    <div id="btn_detalle_resultados">
                                        <div class="thumbnail">
                                        <div class="caption">
                                            <img src="<?php echo base_url() ?>/imagenes/productivo.png" alt="...">
                                            <span class = "btn form-control">Resultados</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div id="btn_mejora">
                                        <div class="thumbnail">
                                        <div class="caption">
                                            <img src="<?php echo base_url() ?>/imagenes/soste.png" alt="...">
                                            <span class = "btn form-control">Metas de Desempeño</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div id="btn_sugerencias">
                                        <div class="thumbnail">
                                        <div class="caption">
                                            <img src="<?php echo base_url() ?>/imagenes/sublineas.png" alt="...">
                                            <span class = "btn form-control">Sugerencias de Formación</span>                 
                                        </div>
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

	<div class="modal fade con-scroll-modal" id="modal_sugerencias" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Sugerencias de Formación</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">  
                        <div id="container_ant_familiar">
							<div class="alert alert-info text-left" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
								<p>Cada sugerencia se debe agregar de manera individual, haciendo click en el botón <strong> +Agregar</strong>.</p>
							</div>
							<table class="table table-bordered table-hover table-condensed" id="tabla_sugerencias" cellspacing="0" width="100%">
								<thead class="ttitulo">
								<tr>
								<th class="nombre_tabla" colspan="2">TABLA DE SUGERENCIAS</th>
									<td class="sin-borde text-right border-left-none">
										<button class="btn btn-default" id="agregar_sugerencia"> <span class="fa fa-plus red"></span> Agregar</button>
									</td> 
								</tr>
								<tr class="filaprincipal">
									<td class="opciones_tbl_btn">No.</td>
									<td>Observación</td>
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

	<div class="modal fade" id="modal_add_sugerencias" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_sugerencias" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Sugerencias de Formación</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="sugerencias" rows="9" placeholder="Sugerencias de Formación y Capacitación" title="Sugerenias"></textarea>        
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

	<div class="modal fade con-scroll-modal" id="modal_mejoras" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Metas de Desempeño</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">  
                        <div id="container_ant_familiar">
							<div class="alert alert-info text-left" role="alert">
								<h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
								<p>Cada meta se debe agregar de manera individual, haciendo click en el botón <strong> +Agregar</strong>.</p>
							</div>
							<table class="table table-bordered table-hover table-condensed" id="tabla_compromisos" cellspacing="0" width="100%">
								<thead class="ttitulo">
								<tr>
								<th class="nombre_tabla" colspan="2">TABLA METAS DE DESEMPEÑO</th>
									<td class="sin-borde text-right border-left-none">
										<button class="btn btn-default" id="agregar_compromiso"> <span class="fa fa-plus red"></span> Agregar</button>
									</td> 
								</tr>
								<tr class="filaprincipal">
									<td class="opciones_tbl_btn">No.</td>
									<td>Meta de Desempeño</td>
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

    <div class="modal fade" id="modal_add_compromiso" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_compromisos" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-edit"></span> Metas de Desempeño</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">
						<div class="alert alert-info text-left" role="alert">
							<h4><span class="fa fa-exclamation-circle"></span> Nota</h4>
							<p>No olvides establecer con tu colaborador el plan de trabajo de este año.</p>
						</div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='compromiso' rows="5" placeholder="Meta de Desempeño por parte del evaluado" title="Compromiso"></textarea>    
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

	<div class="modal fade con-scroll-modal" id="modal_detalle_resultados" role="dialog">
		<div class="modal-dialog modal-95">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-check"></span> <span class='nombre_evaluacion'>Resultado de Evaluación</span></h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<nav class="navbar navbar-default" id="menu_resultados_acta">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
							    <li class="pointer detalles_acta active"><a><span class="fa fa-list red"></span> Competencias</a></li>
								<li class="pointer metas_acta"><a><span class="fa fa-list red"></span> Metas de Desempeño</a></li>
								<li class="pointer tipoevaluador_acta"><a><span class="fa fa-list red"></span> Tipo Evaluador</a></li>
							</ul>
						</div>
					</nav>
					<div class="resultado_detalle_acta active row" style="margin:0px;width:100%;">
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_detalle_resultados" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="5" class="nombre_tabla">TABLA COMPETENCIAS</td>
										<td class="text-right">
											<a href="<?php echo base_url()?>index.php/evaluacion/exportar_competencias" target="blank_" class="btn btn-default" id="btn_competencias"><span class="fa fa-download red"></span> Exportar</a>
										</td>
									</tr>
									<tr class="filaprincipal">
										<td>Area de Apreciación</td>
										<td>Competencia</td>
										<td>Descripción</td>
										<td class="opciones_tbl_btn">Fortaleza</td>
										<td class="opciones_tbl_btn">Oportunidad de Mejora</td>
										<td class="opciones_tbl_btn">Puntaje</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>	
					<div class="resultado_metas_acta oculto row" style="margin:0px;width:100%;">
						<div class="table-responsive col-md-12" style="width: 100%">
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_metas_acta" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="4" class="nombre_tabla">TABLA PREGUNTAS</td>
									</tr>
									<tr class="filaprincipal">
										<td class="opciones_tbl_btn">No.</td>
										<td>Meta</td>
										<td class="opciones_tbl_btn">Puntaje</td>
										<td>Descripción</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>	
					<div class="resultado_tipoevaluador_acta oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="width: 100%">
							<table id="tabla_resultados_tevaluador_acta" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="4">TABLA EVALUADORES</th> 
									</tr>
									<tr class="filaprincipal">
										<td>Evaluador</td>
										<td class="opciones_tbl_btn">%</td>
										<td class="opciones_tbl_btn">Acumulado</td>
										<td class="opciones_tbl_btn">Producto</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
					<div style="margin-left: 5px;">
						<button type="button" class="btn btn-danger active" id="btn_resul_competencia"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_solicitar_firma_jefe" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-pencil"></span> Firma Jefe Inmediato</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row" id="" style="width: 100%">
						<p class='text-justify'>Por medio de la presente, la Universidad de la Costa identificada con NIT 890.104. 530.9, institución privada de educación superior, sin ánimo de lucro, con domicilio en la ciudad de Barranquilla Calle. 58 #55-66, con dirección electrónica: buzon@cuc.edu.co - y teléfono 3362200, me hace entrega de Acta de Retroalimentación del desempeño.</p>
						<div id="div_firmar"></div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" class="btn btn-danger active" id="enviar_firma_jefe"><span class="glyphicon glyphicon-check"></span> Aceptar</button>
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>

<script>
    $(document).ready(function () {
		inactivityTime();
		id_solicitud = <?php echo $id_solicitud ?>;
		listar_personal_actas(id_solicitud);
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>