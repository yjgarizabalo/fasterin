<?php if($estado_encuesta) { ?>

<form id="encuesta_formulario" method="POST">
    <div id="preguntas">
        <div class="text-center log">

            <div class="fondo" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
        </div>
        <div class="text-center" style="width: 100%; margin-bottom: 3px;">
            <p class="tipografia" id="descripcion"></p>
        </div>

        <div class="text-center" style="width: 100%; margin-bottom: 3px;">
            <p class="tipografia" id="descripcionEncuesta"></p>
        </div>
        <div class="wrapper">
            <div class="c-stepper"  id="pasos">
                
            </div>
        </div>

        <div class="text-center">
            <button type="button" class="btn-stepper btn-lg" id="btn-iniciar_encuesta">INICIAR</button>
        </div>
    </div>
</form>

<?php } else { ?>

    <div class="col-md-12 text-center">
        <img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'> 
        <h4><b>ENCUESTA HA SIDO FINALIZADA</b></h4>
        </br>
        <a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
    </div>

<?php } ?>

<script>
    const idp = <?php echo $id; ?>;
</script>