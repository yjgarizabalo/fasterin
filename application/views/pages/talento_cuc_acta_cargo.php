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
			<h3 class="titulo_menu"><span class="fa fa-check-circle"></span>Acta aceptación del Cargo</h3>	
		</div>
		<div class="contenido panel con-scroll-modal" id="contenido_actas">
		<?php if($estado){ ?>
			<div class="row col-md-12 centrar">
			   <div class="col-md-10">
					<form  action="#" id="form_confirmar_acta" method="post">
						<div class="col-md-12 text-left">
							<a href="https://app.powerbi.com/view?r=eyJrIjoiYjY4OGI5M2QtNGVmZC00NTEzLWFjMzgtZTRiMmYxOWY5ZWE3IiwidCI6IjA1MDdlNWNlLTBmOTUtNDlhYS1hYmRlLWM5MGRjZGVkYmQxMiIsImMiOjR9&pageName=ReportSection" target="_blank" class="btn btn-default"><span class="fa fa-book red"></span> Manual de Funciones</a>
						</div>
						<div class="col-md-12 text-left">
							<br>
							<p>NOMBRE DEL JEFE: <b><?php echo $jefe_inmediato ?></b></p>
							<p>CARGO: <b><?php echo $cargo ?></b></p>
							<div class="alert alert-info">
								<p>Acepto todas las funciones y responsabilidades descritas en el ACUERDO VIGENTE DE MANUAL DESCRIPTIVO DE CARGOS POR COMPETENCIAS
								 para el cargo <b><?php echo $codigo_cargo ?></b> y me comprometo a cumplirlas según los lineamientos institucionales.</p>
							</div>
						</div>
						<div class="col-md-12 text-left">
							<textarea name="observaciones" class="form-control" rows="4" placeholder="Observaciones"></textarea>
						</div>						
						<div class="col-md-12" style="padding:20px;">
							<button type="submit" class="btn btn-danger btn-lg active"><span class="glyphicon glyphicon-floppy-disk"></span> Confirmar</button>
						</div>
					</form>
				</div>		
			</div>		
			<?php }else{ ?>	
				<div class="col-md-12">
					<img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>ACTA CONFIRMADA</b></h4>
					</br>
					<a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>
			<?php } ?>
		</div>
		<div class="col-md-12" id="footer_encuesta">
			<div class="col-md-12 text-center">
			 	<h4 class='nombre_completo' style="color:#fff"><?php echo $nombre_completo ?></h4>	
			</div>	
		</div>

		<div class="modal fade" id="modal_solicitar_firma" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-pencil"></span> Firma Funcionario</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row" id="" style="width: 100%">
							<p class='text-justify'>Por medio de la presente, la Universidad de la Costa identificada con NIT 890.104. 530.9, institución privada de educación superior, sin ánimo de lucro, con domicilio en la ciudad de Barranquilla Calle. 58 #55-66, con dirección electrónica: buzon@cuc.edu.co - y teléfono 3362200, me hace entrega de Acta de Retroalimentación del desempeño.</p>
							<div id="div_firmar"></div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-danger active" id="enviar_firma"><span class="glyphicon glyphicon-check"></span> Aceptar</button>
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
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>