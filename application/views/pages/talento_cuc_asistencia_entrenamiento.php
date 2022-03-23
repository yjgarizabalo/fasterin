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
			<h3 class="titulo_menu"><span class="fa fa-user-plus"></span> Confirmar entrenamiento</h3>	
		</div>
		<div class="contenido panel con-scroll-modal" id="contenido_actas">
		<div class="col-md-12 text-center">
			<div>
				<img src="<?php echo base_url() ?>/imagenes/retroalimentacion.png" alt="..." style='width:20%;'>
			</div>
		</div>
		<div class="col-md-12 centrar">
			<div class="col-md-11 table-responsive">
				<table class="table table-bordered table-condensed table-hover table-responsive" id="tabla_entrenamientos_persona"  cellspacing="0" width="100%">
				<thead>
					<tr>
						<th class="nombre_tabla" colspan="4">Entrenamientos</th>
					</tr>
					<tr class="filaprincipal">
						<td>Nombre</td>
						<td>Identificación</td>
						<td>Oferta</td>
						<td>Fecha</td>
						<td>Acciones</td>
					</tr>
				</thead>
				<tbody>
				</tbody>	
				</table>
				<br/>
			</div>	
		</div>
	</div>
	<div class="col-md-12" id="footer_encuesta">
		<div class="col-md-4 text-right">
			<h5 class='nombre_evaluado' style="color:#fff"></h5>	
		</div>
		<div class="col-md-4">
			<div class="progress barra">
				<div class="progress-bar" id='bar_estado' role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="background-color:white;color:#d57e1c;width:<?php echo  $progress ?>">
					<b><span class="text_barra"><?php echo $progress ?></span></b>
				</div>
			</div>
		</div>	
	</div>

	
	<div class="modal fade" id="modal_solicitar_firma" role="dialog">
        <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-hand-o-right"></span> Confirmar</h3>
				</div>
				<div class="modal-body" id="bodymodal">
					<div class="row">
						<p class='text-justify'>Por medio de la presente, la Universidad de la Costa identificada con NIT 890.104. 530.9, institución privada de educación superior, sin ánimo de lucro, con domicilio en la ciudad de Barranquilla Calle. 58 #55-66, con dirección electrónica: buzon@cuc.edu.co - y teléfono 3362200, se confirma la realización del entrenamiento.</p>
						<div id="div_firmar"></div>
					</div>
				</div>
				<div class="modal-footer" id="footermodal">
					<button type="button" id="enviar_firma" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
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
		listar_asistencias_entrenamiento(<?php echo ($identificacion) ?>);
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>