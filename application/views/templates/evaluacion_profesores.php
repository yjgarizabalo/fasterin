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
    <title>Evaluación</title>
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
        <div class="container">
            <div class="col-md-4">
                <div class="bg-info">
                    <img src="" alt="Images">
                    <p>ID: 1545024685</p>
                    <img src="" alt="Pais">
                    <p><strong>CONTRATACIÓN</strong></p>
                    <p>TIEMPO COMPLETO</p>
                    <p>ASISTENTE 1</p>
                    <p>FIJO</p>
                    <br>
                    <p>INICIO: 2020/01/01</p>
                    <p>FIN: 2021/01/01</p>
                    <br>
                    <p><strong>FORMACIÓN</strong></p>
                    <p>PENDIENTE</p>
                    <br>
                    <p>GRADO: PREGRADO</p>
                    <p>INGLES: -</p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <p>CIENCIAS EMPRESARIAL - ADMINISTRACION DE EMPRESAS</p>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-8">
                            <p>NOMBRE COMPLETO</p>
                        </div>
                        <div class="col-md-4">
                            <img src="" alt="UNIVERSIDAD">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p>AREA: ADMINISTRACIÓN Y ORGANIZACIÓN</p>
                    </div>
                </div>
                <div class=col-md-12>
                    <div class="col-md-6">
                        <p>PERFIL: GESTIÓN UNIVERSITARIA</p>
                        <p>ROL(ES): PROFESOR EN ENCARGO: VICERRECTORÍA DE EXTENSIÓN</p>
                    </div>
                    <div class="col-md-6">
                        <p>GRUPO: N/A</p>
                        <p>LÍNEA: N/A</p>
                        <div class="">
                            <span>CATEGORIA:</span>
                            <span>CITAS:</span>
                            <span>INDICE H:</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-12">
                        <p>EVALUACIÓN DE DESEMPEÑO 2020-1</p>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-8">
                            <table>
                                <tr>
                                    <th>DO</th>
                                    <th>INV</th>
                                    <th>ROL</th>
                                    <th>ROL 2</th>
                                    <th>ROL 3</th>
                                    <th>ROL 4</th>
                                </tr>
                                <tr>
                                    <td>10%</td>
                                    <td>10%</td>
                                    <td>70%</td>
                                    <td>10%</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>x</td>
                                    <td>x</td>
                                    <td>x</td>
                                    <td>x</td>
                                    <td>x</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <p>S</p>
                            <p>4.9</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p>EV. D: 4.3</p>
                    </div>
                </div>
            </div>
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

// $mpdf->Output("archivos_adjuntos/bienestar_laboral/asesorias/$nombre_archivo", \Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";