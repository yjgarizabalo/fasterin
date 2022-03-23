<style>
header,
footer {
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
	

	.clasificacion input[type="radio"] {
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
      <h3 class="titulo_menu titulo_menu_th"><span class="fa fa-check-circle red"></span> Satisfacción Entrenamiento</h3>
    </div>
    <div class="contenido panel con-scroll-modal" id="contenido_actas">
      <?php if($estado_encuesta){ ?>
      <div class="row col-md-12 centrar">
          <form action="#" id="form_encuesta_entrenamiento_general" method="post">
            <div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;">
              <h4>Click aquí para ver <a href="<?php echo base_url().'archivos_adjuntos/talento_cuc/entrenamiento/Entrenamiento_'.$identificacion.'.pdf'?>" target="_blank"><span class="fa fa-hand-o-right red"></span> Plan de Entrenamiento</a></h4>
            </div>
            <div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;">
              <h4>
                <ul>
                  <li>¿Los conocimientos que adquirió durante este proceso han sido suficientes para desempeñar su cargo?</li>
                </ul>
              </h4>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_1" value="1">
                <label class="custom-control-label" for="answer_1_si"> SI</label>
              </div>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_1" value="0">
                <label class="custom-control-label" for="answer_1_no"> NO</label>
              </div>
            </div>
            <div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;"> 
              <h4>
                <ul>
                  <li>¿Considera necesario recibir inducción/entrenamiento al cargo nuevamente?</li>
                </ul>
              </h4>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_2" value="1">
                <label class="custom-control-label" for="answer_2_si"> SI</label>
              </div>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_2" value="0">
                <label class="custom-control-label" for="answer_2_no"> NO</label>
              </div>
            </div>
            <div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;">  
              <h4>
                  <ul>
                    <li>Del 1 al 5 cual seria la puntuación de su Inducción / Entrenamiento al cargo: <strong><span id="nivel"></span></strong></li>
                  </ul>
                </h4>
            </div>
            <div class="col-md-12 text-center">
                <p class="clasificacion">
                  <input id="radio1" type="radio" name="calificacion" value="5" onclick="marcar_califiacacion()">
                  <label for="radio1">&#9733;</label>
                  <input id="radio2" type="radio" name="calificacion" value="4" onclick="marcar_califiacacion()">
                  <label for="radio2">&#9733;</label>
                  <input id="radio3" type="radio" name="calificacion" value="3" onclick="marcar_califiacacion()">
                  <label for="radio3">&#9733;</label>
                  <input id="radio4" type="radio" name="calificacion" value="2" onclick="marcar_califiacacion()">
                  <label for="radio4">&#9733;</label>
                  <input id="radio5" type="radio" name="calificacion" value="1" onclick="marcar_califiacacion()">
                  <label for="radio5">&#9733;</label>
                </p>		
            </div>
            <div class="col-md-12 text-center" style="padding-left: 40px;padding-bottom:10px;">
                <textarea name="sugerencias" class="form-control" rows="4" placeholder="Sugerencias"></textarea>
            </div>
            <div class="col-md-12" style="padding: 20px;">
                <button type="submit" class="btn btn-danger btn-lg active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            </div>
        </form> 
      </div>
      <?php }else{ ?>
      <div class="col-md-12">
        <img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'>
        <h4><b>ENCUESTA FINALIZADA</b></h4>
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

  </div>
</div>

<script>
$(document).ready(function() {
  inactivityTime();
});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>