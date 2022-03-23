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
		margin-top: -4%;
		height: 10%;
		background-color: #6e1f7c;
		display: flex;
		justify-content: left;
        align-items: center;
		/* border-bottom-right-radius: 4px;
    	border-bottom-left-radius: 4px; */
		box-shadow: 15px -10px 3px #D3D2D2;
		-webkit-box-shadow: -15px -10px 20px #D3D2D2;
		-moz-box-shadow: 15px -10px 3px #D3D2D2;
		z-index: 999;
	}

	.barra{
		margin:25px;
		background-color:#6e1f7c;
		border:solid 1px white;
	}

	#header_encuesta{
		height: 18%;
		background-color: white;
		display: flex;
		flex-direction: column;
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
	.evaluaciones{
		display: flex;
		justify-content: center;
        align-items: center;
	}
</style>
<div class="container col-md-12  text-center" id="inicio-user">	
	<div id="container_principal">
		<div class="col-md-12" id="header_encuesta">
		    <h3 class="titulo_menu center"><span class="fa fa-question-circle"></span><?php echo $metodo_evaluacion ?></h3>
			<span id='info_evaluado'></span>
		</div>
		<div class="contenido panel con-scroll-modal">			
			<div class="col-md-12 row" id='container_encuesta'>
			<?php if($estado_solicitud){ ?>				
				<div class="col-md-12 text-center">
					<div>
						<img src="<?php echo base_url() ?>/imagenes/evaluacion.png" alt="..." style='width:20%;'>
					</div>
				</div>
				<div class="col-md-12 text-center" style='padding-top:10px;'>	
					<a class="btn btn-danger btn-lg btn_inicio_encuesta <?php if(!$estado_solicitud) echo 'oculto' ?>" style="background-color: #d57e1c!important;">Iniciar Evaluación</a>
				</div>
				<div class="col-md-12 evaluaciones">
					<div class="col-md-8" style='padding-top:20px;'>
						<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="nombre_tabla" colspan="2">Evaluaciones</th>
							</tr>
							<tr class="filaprincipal">
								<td>Nombre</td>
								<td class="opciones_tbl">Estado</td>
							</tr>
						</thead>
						<tbody>	
						<?php foreach ($tipo_evaluador as $per){
						    if($per['id_aux'] != 'Eval_Per'){ 
								if($per["completado"] == 0 ) $status = '<span title="En espera" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#D3D2D2"></span>';
								else $status = '<span title="Completado" data-toggle="popover" data-trigger="hover" class="fa fa-check" style="color:#5cb85c"></span>';
								?>
								<tr>
									<td class="text-left"><?php echo $per["valorx"] ?> <?php echo $per["nombre_evaluado"] ?></td>
									<td><?php echo $status ?></td>
								</tr>
						<?php } 
						} ?>
						</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-12 evaluaciones">
					<div class="col-md-8" style='padding-bottom:30px;'>
						<table class="table table-bordered table-condensed <?php if(!$indicadores) echo 'oculto' ?>">
						<thead>
							<tr>
								<th class="nombre_tabla" colspan="3">Personal a Cargo</th>
							</tr>
							<tr class="filaprincipal">
								<td>Nombre</td>
								<td class="opciones_tbl">Estado</td>
								<td class="opciones_tbl">Indicadores</td>
							</tr>
						</thead>
						<tbody>	
						<?php
						if($indicadores){
							foreach ($indicadores as $i){
								$btn_iniciar = '<span title="Gestionar" onclick="continuar_encuesta('.$id_solicitud.','.$i["id_persona"].')" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default" style="color:#2E79E5"></span>';
								if($i["evaluacion"] == 0 && $estado_tipo_evaluador == 3) $status = $btn_iniciar;
								else if($i["evaluacion"] == 0 && $estado_tipo_evaluador < 3) $status = '<span title="En espera" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#D3D2D2"></span>';
								else $status = '<span title="Completado" data-toggle="popover" data-trigger="hover" class="fa fa-check" style="color:#5cb85c"></span>';																
								
								if($i["id_estado"] == '' && $parte1 == 0) $indicador = '<span title="En espera" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#D3D2D2"></span>';
								else if(!$i["id_estado"] && $parte1 == 1) $indicador = $btn_iniciar;
								else $indicador = '<span title="Completado" data-toggle="popover" data-trigger="hover" class="fa fa-check" style="color:#5cb85c"></span>';							
							?>
							<tr>
								<td class="text-left"><?php echo $i["nombre_evaluado"] ?></td>
								<td><?php echo $status ?></td>
								<td><?php echo $indicador ?></td>
							</tr>
						<?php }
						} ?>
						</tbody>	
						</table>
					</div>
				</div>
			<?php }else{ ?>	
				<div class="col-md-12">
					<img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>EVALUACIÓN FINALIZADA</b></h4>
					</br>
					<a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>
			<?php } ?>		
			</div>
		</div>		
		<div class="col-md-12" id="footer_encuesta">
			<div class="col-md-4 text-right">
			 	<h5 class='nombre_tipo_evaluador' style="color:#fff"><?php echo $nombre_tipo_evaluador ?></h5>	
			</div>
			<div class="col-md-4">
				<div class="progress barra">
					<div class="progress-bar" id='bar_estado' role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="background-color:white;color:#d57e1c;width:<?php echo  $progress ?>">
						<b><span class="text_barra"><?php echo $progress ?></span></b>
					</div>
				</div>
			</div>
			<div class="col-md-4 text-left">
			 	<h5 class='nombre_evaluado' style="color:#fff"></h5>	
			</div>	
		</div>		
	</div>
</div>

<script>
    $(document).ready(function () {
		//inactivityTime();
		id_solicitud = <?php echo $id_solicitud ?>;
	});
</script>
