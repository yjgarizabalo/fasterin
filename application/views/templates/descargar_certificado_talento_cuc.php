<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "certificado" ;
$title = "TALENTO CUC";
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>

<head>
  <title> <?= $title ?> </title>
</head>

<body>
  <htmlpagebody>
    <div id='container_certificado'>
      <div class="container_principal_item">
        <div class='container_certificado_item'
          style="background-image: url(<?php echo base_url(); ?>imagenes/fondo_Certificado.jpg);">
          <img class='img_certificado' src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="Logo cuc">
          <p class='titulo1_certificado'>LA UNIVERSIDAD DE LA COSTA CERTIFICA LA ASISTENCIA DE:</p>
          <h3><?php echo $nombre_completo?></h3>
          <hr class='ceparador_certificado'>
          C.C Nº <b><?php echo $identificacion?></b> DE <?php echo $lugar_expedicion?>
          <p class='titulo2_certificado'>PLAN DE FORMACIÓN INSTITUCIONAL</p>
          <b><?php echo date('Y') ?></b>
          <p class='titulo3_certificado'><span class="texto_rojo_certificado">Con una intensidad horaria de</span>
            <?php echo $horas ?> horas <span class="texto_rojo_certificado">realizado en</span> Barranquilla, Atlántico
          </p>
          <b><?php echo date('d-m-Y')?></b>
          <br>
          <br>
          <img class='img_certificado' src="<?php echo base_url(); ?>imagenes/firma_dir_th.png" alt="firma">
          <p>Adriana Cristina Vera Barbosa</p>
          <p>Directora de Talento Humano, Universidad de la Costa </p>
        </div>
      </div>
    </div>
    <?php foreach ($competencias as $row) { ?>
      <div id='container_certificado'>
      <div class="container_principal_item">
        <div class='container_certificado_item'
          style="background-image: url(<?php echo base_url(); ?>imagenes/fondo_Certificado.jpg);">
          <img class='img_certificado' src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="Logo cuc">
          <p class='titulo1_certificado'>LA UNIVERSIDAD DE LA COSTA CERTIFICA LA ASISTENCIA DE:</p>
          <h3><?php echo $nombre_completo?></h3>
          <hr class='ceparador_certificado'>
          C.C Nº <b><?php echo $identificacion?></b> DE <?php echo $lugar_expedicion?>
          <p class='titulo2_certificado'>PLAN DE FORMACIÓN INSTITUCIONAL</p>
          <b><?php echo date('Y') ?></b>
          <p class='titulo3_certificado'><span class="texto_rojo_certificado">En la competencia </span><?php echo $row['competencia'] ?> <span class="texto_rojo_certificado">Con una intensidad horaria de</span>
            <?php echo $row['horas'] ?> horas <span class="texto_rojo_certificado">realizado en</span> Barranquilla, Atlántico
          </p>
          <b><?php echo date('d-m-Y')?></b>
          <br>
          <br>
          <img class='img_certificado' src="<?php echo base_url(); ?>imagenes/firma_dir_th.png" alt="firma">
          <p>Adriana Cristina Vera Barbosa</p>
          <p>Directora de Talento Humano, Universidad de la Costa </p>
        </div>
      </div>
    </div>  
    <?php } ?>
    </htmlpagefooter>
</body>

</html>
<?php
$html = ob_get_clean();
ob_clean();

// Crea una instancia de la clase y le pasa el directorio temporal
$mpdf = new Mpdf(['margin-top' => 30,'default_font' => 'cuc', 'default_font_size' => 9,]);


$stylesheet = file_get_contents($css_boot);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
$mpdf->SetDefaultBodyCSS('background', "url('https://img.freepik.com/foto-gratis/fondo-acuarela-pintada-mano-forma-cielo-nubes_24972-1108.jpg?size=626&ext=jpg')");
$mpdf->SetDefaultBodyCSS('background-image-resize', 6);
$mpdf->WriteHTML($html);

$mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);