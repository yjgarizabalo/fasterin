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
	

	input[type="radio"] {
		display: none;
	}

	label {
		color: grey;
	}

	.clasificacion {
		direction: rtl;
		unicode-bidi: bidi-override;
		font-size: 0;
		display: inline-block;
	}

	.clasificacion label {
		text-decoration: none;
		display: inline-block;
		/* Volver a dar tamaño al texto */
		font-size: 42px;
		font-size: 5rem;
	}

	label:hover,
	label:hover ~ label {
		color: #d57e1c!important;
	}

	input[type="radio"]:checked ~ label {
		color: #d57e1c!important;
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
			<h3 class="titulo_menu"><span class="fa fa-check-circle"></span>Acta de Retroalimentación</h3>	
		</div>
		<div class="contenido panel con-scroll-modal" id="contenido_actas">
		<?php if($estado){ ?>
			<div class="row col-md-12 centrar">
			   <div class="col-md-8">
					<form  action="#" id="form_confirmar_acta" method="post">
						<div class="col-md-12 text-center">
							<img src="<?php echo base_url() ?>/imagenes/retroalimentacion.png" alt="..." style='width:25%;'>
						</div>
						<div class="col-md-12 text-left">
							<a href="<?php echo base_url()?>" target="_blank" class="btn btn-default btn_ver_acta" id="btnActa"><span class="fa fa-eye red"></span> Retroalimentación</a>
						</div>
						<div class="col-md-12 text-left">
							<textarea name="observaciones" class="form-control" rows="4" placeholder="Observaciones"></textarea>
						</div>		   	
						<div class="col-md-12 text-center">
							<h4 class="ttitulo">Seleccione el nivel de satisfacción de Retroalimentación de su jefe: <span id="nivel"></span></h4>				
							<p class="clasificacion">
								<input id="radio1" type="radio" name="calificacion" value="5" onclick="marcar_nivel()">
								<label for="radio1">&#9733;</label>
								<input id="radio2" type="radio" name="calificacion" value="4" onclick="marcar_nivel()">
								<label for="radio2">&#9733;</label>
								<input id="radio3" type="radio" name="calificacion" value="3" onclick="marcar_nivel()">
								<label for="radio3">&#9733;</label>
								<input id="radio4" type="radio" name="calificacion" value="2" onclick="marcar_nivel()">
								<label for="radio4">&#9733;</label>
								<input id="radio5" type="radio" name="calificacion" value="1" onclick="marcar_nivel()">
								<label for="radio5">&#9733;</label>
							</p>
						</div>						
						<div class="col-md-12">
							</br>
							<button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Confirmar</button>              
						</div>
					</form>
				</div>		
			</div>		
			<?php }else{ ?>	
				<div class="col-md-12">
					<img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>RETROALIMENTACIÓN CONFIRMADA</b></h4>
					</br>
					<a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>
			<?php } ?>
		</div>
		<div class="col-md-12" id="footer_encuesta">
			<div class="col-md-12 text-center">
			 	<h4 class='nombre_evaluado' style="color:#fff"><?php echo $nombre_evaluado ?></h4>	
			</div>	
		</div>

		<div class="modal fade" id="modal_solicitar_firma" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-pencil"></span> Firma Participante</h3>
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
		// pedir_firma();
		id_solicitud = <?php echo $id_solicitud ?>;
		$("#btnActa").attr("href", `${Traer_Server()}archivos_adjuntos/talentohumano/actas/ACTA_${id_solicitud}.pdf`);
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>