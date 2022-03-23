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
        <title>Carta - Asesoria Jurídica</title>
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
            <p>Cordial Saludo.</p>
            <div style="padding-top: 10px;">
              <p>El consultorio Jurídico brinda a cada funcionario y a su familia de forma gratuita asesorías en todas las áreas del Derecho Administrativo, Civil-Familia, Comercial, Constitucional, Laboral – Seguridad Social y Penal.</p>
              <p>Ofrece a todos sus usuarios los siguientes servicios:</p>
            </div>
            <br>
            <div>
                <ul>
                    <li>Asesoría Jurídica personalizada en las diferentes acciones publicas constitucionales</li>
                    <li>Tramite de procesos judiciales y administrativos</li>
                    <li>Capacitación jurídica</li>
                    <li>Audiencias de conciliación</li>
                    <li>Brigadas Psicojurídicas</li>
                    <li>Practicas jurídicas empresariales</li>
                    <li>Acompañamiento psicológico</li>
                </ul>
            </div>
            <br>
            <div style="line-height: 4px;">
                <p>Los horarios de atención son:</p>    
                <p>lunes a viernes - 7:00 AM a 6:00 PM en jornada continua</p>
                <p>Sábados - 8:00 AM a 1:00 PM</p>
            </div>
            <br>
            <div style="line-height: 4px;">
                <p style="font-weight: bold;">CONSULTORIO JURIDICO Y CENTRO DE CONCILIACION CUC</p>    
                <p>Carrera 44 No. 55 – 03</p>
                <p>Teléfono: 3405130</p>
                <a href="#">consultoriojuridico@cuc.edu.co</a>
            </div>
            <br><br>
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
