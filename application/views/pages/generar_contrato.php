<?php
$perfil = $_SESSION["perfil"];
$administra = $perfil == "Per_Admin"  || $perfil == "Per_Adm_plan" || $perfil == "Per_Csep" || $permiso ? true : false;
// Require composer autoload
require_once(APPPATH . 'libraries/Mpdf/autoload.php');

use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Contrato - " . $contrato['nombre_tista'];
$title = "Contrato";
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();
?>
<html>

<head>
    <title><?= $title ?></title>
</head>

<body id='body_contrato'>
    <htmlpageheader name="myHeader1">
        <table width="100%" class="table_footer">
            <tr>
                <td width="50%" style="text-align: left;  font-size: 10px;">{DATE d/m/Y}</td>
                <td width="50%" style="text-align: left;  font-size: 10px;"></td>
            </tr>
        </table>
    </htmlpageheader>

    <htmlpagebody>
        <?php
        //print_r($contrato);
        //print_r($consideraciones);
        //print_r($json->title);
        /*foreach ($json->datos as $value) {
                    
                }*/
        ?>
        <img src="<?= $logo ?>" class="logo-contrato">
        <h4 class="contrato-header"><?php echo $contrato['contrato']; ?></h4>
        <table width="75%" style="margin:20px auto;">
            <tr>
                <td style="text-align: justify;">
                    Entre los suscritos a saber, <b>TITO JOSÉ CRISSIEN BORRERO</b>, vecino de la ciudad de Barranquilla,
                    identificado con cédula de ciudadanía No. 79.785.729, Rector y Representante legal de la <b>CORPORACIÓN
                        UNIVERSIDAD DE LA COSTA CUC</b>, institución de educación superior, de carácter privado, sin ánimo de
                    lucro, con domicilio en la ciudad de Barranquilla, con personería jurídica reconocida mediante Resolución 352
                    del 23 de abril de 1971 de la Gobernación del Atlántico, identificada con NIT. 890.104.530-9, quien en se
                    denominará <b>EL CONTRATANTE</b> y <b><?php echo $contrato['nombre_tista']; ?></b>, vecino de Bogotá D.C., identificado con cédula de ciudadanía
                    No. <?php echo $contrato['tista_cedula_nit']; ?> de XXXXXX, quien en adelante se denominará <b>EL CONTRATISTA</b>, hemos acordado celebrar
                    el presente contrato de prestación de servicio profesionales, que se regirá por las cláusulas adelante
                    señaladas, previa las siguientes:
                </td>
            </tr>
        </table>
        <h3 style="text-align: center; text-decoration: underline;line-height: 2px;">CONSIDERACIONES: </h3>
        <table width="75%" style="margin:20px auto;">
            <?php $num = 1; ?>
            <?php foreach ($consideraciones as $value) : ?>
                <tr>
                    <td style="vertical-align: top;"><?= $num . '. '; ?></td>
                    <td style="text-align: justify;"><?= $value['consideracion'] ?></td>
                </tr>
                <?php $num++; ?>
            <?php endforeach; ?>
        </table>
        <p width="75%" style="margin:60px auto;">Teniendo en cuenta las anteriores consideraciones, y en virtud de la autonomía de las partes estas acuerdan celebrar el presente contrato el cual se rige por las siguientes clausulas:</p>
        <h3 style="text-align: center; text-decoration: underline;line-height: 3px;">CLAUSULAS: </h3>
        <table width="75%" style="margin:20px auto;">
            <?php foreach ($clausulas as $clausula) : ?>
                <tr>
                    <td style="text-align: justify;"><?= $clausula['clausula']; ?></td>
                </tr>
                <br><br>
            <?php endforeach; ?>
        </table>
        <!--table width="75%" style="margin:20px auto;">
            <?php $num = 1; ?>
            <?php foreach ($clausulas as $clausula) : ?>
                <tr>
                    <td style="text-align: justify;">
                        <?= "<br>" . $clausula['nombre'] . ' ' . $clausula['clausula']; ?>
                        <?php if (!empty($clausula['compromisos'])) : ?>
                            <br>
                            <br>
                            <table width="100%" class="table-contrato">
                                <thead>
                                    <tr>
                                        <th>Compromiso</th>
                                        <th>Especificaciones Técnicas</th>
                                        <th>Medios de Verificación</th>
                                    </tr>
                                </thead>
                                <tbody>  
                                    <?php foreach ($clausula['compromisos'] as $compromiso) : ?>
                                        <tr>
                                            <td><?= $compromiso['compromiso'] ?></td>
                                            <td><?= $compromiso['especificaciones_tecnicas'] ?></td>
                                            <td><?= $compromiso['medios_verificacion'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        <?php
                        foreach ($clausula['otros_aspectos'] as $aspecto) {
                            echo "<br>" . $aspecto['nombre'] . ' ' . $aspecto['detalles'];
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>   
        </table-->
        <!--table width="75%" style="margin:20px auto;" class="table-contrato">
            <thead>
                <tr>
                <?php foreach ($json->title as $titulo) : ?>
                    <th><?= $titulo ?></th>
                <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>    
            <?php foreach ($json->datos as $data) : ?>
                <tr>
                <?php foreach ($data as $val) : ?>
                    <td><?= $val ?></td>
                <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table-->
        <p width="75%" style="margin:0 auto 60px;text-align: justify;">De conformidad con lo anterior, las partes suscriben el presente documento en dos ejemplares del mismo tenor.</p>
        <p width="75%" style="margin:60px auto;text-align: justify;">Para constancia se firma en la ciudad de Barranquilla, el primer (1) días del mes de octubre de dos mil veinte (2020).</p>
        <table width="75%" style="margin: auto;">
            <tr>
                <td style="display: inline-block;">
                    <p><img src="<?php echo $firmas[0]; ?>" class="firma-img"></p>
                    <p>C.C.79.785.729 de Bogotá	</p>
                    <p>CONTRATANTE</p>
                </td>
                <td style="display: inline-block;">
                    <p><img src="<?php echo $firmas[1]; ?>" class="firma-img"></p>
                    <p>C.C. No. <?php echo $contrato['tista_cedula_nit']; ?></p>
                    <p>CONTRATISTA</p>
                </td>
            </tr>              
        </table>
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
$mpdf = new Mpdf(['margin-top' => 30, 'default_font' => 'cuc', 'default_font_size' => 9,]);

// Carga el CSS externo
$stylesheet = file_get_contents($css_boo);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

// Escribe el contenido HTML (Template + View):
$mpdf->WriteHTML($html);
if (!is_null($contrato['prorroga'])) {
    $mpdf->AddPage();
    $mpdf->WriteHTML('<p width="75%" style="margin:150px auto;text-align: justify;">'.$contrato['prorroga'].'</p>');
}

// Obliga la descarga del PDF
$mpdf->Output("$fileName.pdf", Destination::INLINE);
