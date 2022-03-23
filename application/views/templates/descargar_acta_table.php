<?php
require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Acta - ".$datos['nombre_completo'] ;
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
    <head>
        <title>Acta de Entrega</title>
    </head>
    <body id='body_des_plan'>
        <htmlpageheader name="myHeader1">
          <table width="100%" class="table_footer">
            <tr>
                <td width="50%" style="text-align: left;  font-size: 10px;">{DATE d/m/Y}</td>
                <td width="50%" style="text-align: left;  font-size: 10px;">AGIL</td>
            </tr>
          </table>
        </htmlpageheader>
        <htmlpagebody>
          <div>
            <h4 class="title-header"> <img src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="" width="100" height='100'></h4>
            <h4 class="title-header">ACTA DE ENTREGA TABLET A ESTUDIANTES NUEVOS</h4>
              <br>
              <p class='text-justify'>Por medio de la presente, la Universidad de la Costa identificada con NIT 890.104. 530.9, institución privada de educación superior, sin ánimo de lucro, con domicilio en la ciudad de Barranquilla Calle. 58 #55-66, con dirección electrónica: buzon@cuc.edu.co - y teléfono 3362200, me hace entrega de una Tablet marca XPECTWAY modelo TAB0001NG.</p>
              <p class='text-justify'>El estudiante tendrá 15 días después de la entrega, para reportar algún defecto de fábrica, el cual deberá hacer llegar en el plazo antes mencionado al Departamento de Tecnología (Bloque 2 piso 1 – Oficina de Recursos Audiovisuales), para su respectiva revisión. Por ningún motivo la Universidad de la Costa se hará responsable por daños debido al uso inadecuado de la misma, tales como golpes, señales de humedad u otros que en la apariencia del producto dé indicios de que ha sido utilizado en condiciones distintas a las comunes, cuando el producto haya sido alterado y/o reparado por personas no autorizadas, cuando el producto no hubiese sido operado de acuerdo con el instructivo de uso que le acompaña, ocasionando daños en el software, si el producto llevó acabo cualquier modificación en el software original pre instalado, así como tampoco por perdida o robo de la misma.</p>
              <p class='text-justify'>A partir de la firma de este documento, soy el responsable por cualquier daño, pérdida o robo, sucedido alguno de estos casos la tablet no será reemplazada, tampoco recibiré soporte hacia la misma, y me comprometo a darle un uso adecuado a la tablet, respetando las políticas de acceso de acuerdo a los lineamientos universitarios.</p>
              <p class='text-justify'>Conozco y entiendo que la tablet que recibo, la utilizaré solo en actividades con fines académicos que contribuyan a mi desarrollo profesional.</p>
          </div>
          <div>
          <div style="display: inline; width: 65%;">
            <img src="<?php echo base_url(); ?>archivos_adjuntos/visitas/firmas/<?php echo $firma ?>" alt="">
            <hr class='firma_espacio'>
            <p class=''>Estudiante : <?php echo $nombre_completo ?></p>         
            <p class=''>Fecha : <?php echo date("Y-m-d H:i:s") ?></p>         
          </div>
        </htmlpagebody>
        <htmlpagefooter name="myFooter1">
            <table width="100%" class="table_footer">
                <tr>
                    <td width="50%" style="text-align: right;  font-size: 10px;">{PAGENO}/{nbpg}</td>
                </tr>
            </table>
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

// Escribe el contenido HTML (Template + View):
$mpdf->WriteHTML($html);

// Obliga la descarga del PDF
// $mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);

$mpdf->Output("archivos_adjuntos/visitas/actas/$nombre_archivo", \Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";
