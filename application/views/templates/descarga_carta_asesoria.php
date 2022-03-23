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
        <title>Carta - Asesoria Psicolgica</title>
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
            <h4 style=" display: block; padding-top: 40px; text-align: right;"> <img src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="" width="100" height='100'></h4>
            <div style="line-height: 2px; padding-top: 10px;">
              <p>Señores</p>
              <p style="font-weight: bold;">FUNDACIÓN FORT DA</p>
              <p>Calle 79 # 51-55</p>
              <p>Teléfono fijo: 3035846</p>
              <p>Celular: 301-697-0879</p>
              <p>Ciudad.</p>
            </div>
            <br>
            <p>Estimados señores.</p>
            <br>
            <p>La Universidad de la Costa-CUC, autoriza dos (2) asesorías psicológicas, de acuerdo al convenio entre nuestras empresas al funcionario(a) <?php echo $nombre_completo ?> identificado(a) con Cedula de Ciudadanía <?php echo $cedula ?> </p>
            <br>
            <p>Al presentar esta autorización se debe adjuntar los siguientes documentos en la Fundación el día de la consulta:</p>
            <br>
            <p>- Carnet de la Universidad</p>
            <p>- Cédula de ciudadanía del funcionario</p>
            <br>
            <p>De antemano agradezco su atención y colaboración a esta solicitud.</p>
            <br><br><br>
            <p>Cordialmente.</p>
            <h4 style="position: relative;"> <img style="position: absolute; left: 0px;" src="<?php echo base_url(); ?>archivos_adjuntos/bienestar_laboral/firmas/firma_director.jpg" alt="" width="100" height='100'></h4>
            <p style="font-weight: bold;">SUGEY MATURANA ROSENSTAND</p>
            <P>Directora de bienestar laboral</P>
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

$mpdf->Output("archivos_adjuntos/bienestar_laboral/asesorias/$nombre_archivo", \Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";
