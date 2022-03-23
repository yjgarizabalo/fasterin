<style>
header,
footer {
  display: none;
}

.contenido {
  height: 70%;
  box-shadow: 15px -10px 3px #D3D2D2;
  -webkit-box-shadow: -15px -10px 20px #D3D2D2;
  -moz-box-shadow: 15px -10px 3px #D3D2D2;
  border-top-right-radius: 0px;
  border-top-left-radius: 0px;
}

.btn_iniciar {
  padding: 10px;
  background-color: #6e1f7c;
  display: flex;
  z-index: 2;
  justify-content: center;
  align-items: center;
}

#footer_encuesta {
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

.barra {
  margin: 25px;
  background-color: #6e1f7c;
  border: solid 1px white;
}

#header_encuesta {
  height: 15%;
  background-color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  border-top-right-radius: 4px;
  border-top-left-radius: 4px;
}

.titulo_menu_th {
  width: 80% !important;
  text-align: center !important;
}

#container_principal {
  width: 60%;
  margin: 0 auto;
  margin-top: 2%;
  box-shadow: 15px -10px 3px #D3D2D2;
  -webkit-box-shadow: -15px -10px 20px #D3D2D2;
  -moz-box-shadow: 15px -10px 3px #D3D2D2;
}

.contenido::-webkit-scrollbar {
  width: 8px;
  /* Tamaño del scroll en vertical */
  height: 8px;
  /* Tamaño del scroll en horizontal */
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

.centrar {
  display: flex;
  justify-content: center;
  align-items: center;
}

label:hover,
label:hover~label {
  color: #d57e1c !important;
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
      <h3 class="titulo_menu titulo_menu_th"><span class="fa fa-check-circle red"></span> Encuesta de satisfacción y
        Asistencia</h3>
    </div>
    <div class="contenido panel con-scroll-modal" id="contenido_actas">
      <?php if($estado_formacion){ ?>
      <div class="row col-md-12 centrar">
        <form action="#" id="form_asistencia" method="post">
          <div class="col-md-12 text-left" style="padding:20px 20px 20px 80px;">
            <h4><b class="ttitulo">Facilitador</b>: <?php echo $facilitador ?></h4>
            <h4><b class="ttitulo">Competencias en Formación</b>:</h4>
            <?php 
              foreach ($competencias as $row) {
                echo '<h4>'.$row['competencia'].'</h4>';
              }
            ?>
          </div>
          <div class="col-md-12" id="conten_preguntas">
            <div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;">
              <h4>
                <ul>
                  <li>Asistió a la capacitación:</li>
                </ul>
              </h4>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_asistencia" value="1">
                <label class="custom-control-label" for="answer_asistencia_si"> SI</label>
              </div>
              <div class="custom-control custom-radio text-left" style="padding-left:40px;">
                <input type="radio" class="custom-control-input" name="answer_asistencia" value="0">
                <label class="custom-control-label" for="answer_asistencia_no"> NO</label>
              </div>
            </div>
          </div>
          <div class="col-md-12" style="padding:20px 20px 20px 80px;">
            <textarea name="sugerencias" class="form-control" rows="4"
              placeholder="Sugerencias para el mejoramiento"></textarea>
          </div>
          <div class="col-md-12" style="padding: 20px;">
            <button type="submit" class="btn btn-danger btn-lg active"><span class="glyphicon glyphicon-floppy-disk"></span>
              Confirmar</button>
          </div>
        </form>
      </div>
      <?php }else{ ?>
      <div class="col-md-12">
        <img src="<?php echo base_url() ?>/imagenes/final.png" alt="..." style='width:30%;'>
        <h4><b>ENCUESTA FINALIZADA</b></h4>
        </br>
        <a href="<?php echo base_url() ?>index.php" class="btn btn-danger btn-lg btn_agil"
          style="background-color: #d57e1c!important;">Regresar a Agil</a>
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
  listar_preguntas();
  id_solicitud = <?php echo $id_formacion ?>;
});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>