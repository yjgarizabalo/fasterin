<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName =  $persona[0]['codigo_cargo'].'_'.$persona[0]['identificacion'];
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
<head>
    <title>Acta Aceptación de Cargo</title>
</head>
<body id='body_des_plan'>
    <htmlpageheader name="myHeader1">        
    </htmlpageheader>
        <table width="100%" class="table table-bordered table_footer">
            <tr>
                <td class="text-center" rowspan="4"><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='80'></td>
                <th class="text-center" rowspan="4">ACTA DE ACEPTACIÓN DE CARGO</th>
                <th class="text-center" rowspan="4"><br><?php echo $version?> <br> <?php echo $fecha ?> <hr> <?php echo $trd ?> <br></th>
            </tr>
        </table>
    <htmlpagebody>
        <div class="">
            <table class="table table-bordered table-condensed" width="100%">
                <tr>
                    <th class="ttitulo">&nbsp;FECHA ENTREGA: </th>
                    <td height='30' colspan="2">&nbsp;<?php echo $datos->{'fecha_entrega'} ?></td>
                </tr>
                <tr>
                    <th class="ttitulo">&nbsp;NOMBRE JEFE INMEDIATO: </th>
                    <td height='30' colspan="2">&nbsp;<?php echo $datos->{'nombre_jefe'} ?></td>
                </tr>
                <tr>
                    <th class="ttitulo">&nbsp;CARGO: </th>
                    <td height='30' colspan="2">&nbsp;<?php echo $persona[0]['cargo'] ?></td>
                </tr>
                <tr>
                    <th class="ttitulo">&nbsp;NOMBRE DEL FUNCIONARIO QUE RECIBE EL &nbsp;CARGO: </th>
                    <td height='30' colspan="2">&nbsp;<?php echo $persona[0]['nombre_completo'] ?></td>
                </tr>
                <tr>
                    <th colspan="3" height='40' class="ttitulo text-center">ACEPTACIÓN</th>
                </tr>
                <tr>
                    <td colspan="3" height='100' class="text-left" style="padding: 5px;"><p>Acepto todas las funciones y responsabilidades descritas en el ACUERDO VIGENTE DE MANUAL DESCRIPTIVO DE CARGOS POR COMPETENCIAS para el cargo <b><?php echo $persona[0]['codigo_cargo'] ?></b> y me comprometo a cumplirlas según los lineamientos institucionales.</p></td>
                </tr>
                <tr>
                    <th colspan="3" height='30' class="ttitulo text-left">&nbsp;OBSERVACIONES</th>
                </tr>
                <tr>
                    <td colspan="3" height='100' class="text-left" style="padding: 5px;"><p><?php echo $datos->{'observacion_acta'} ?></p></td>
                </tr>
                <?php if($datos->{'solicitar_firma_jefe'} == 1){ ?>
                <tr>
                    <th class="ttitulo">&nbsp;FIRMA DEL JEFE INMEDIATO: </th>
                    <td height='50' colspan="2"><img src="<?php echo base_url(); ?>/archivos_adjuntos/talento_cuc/entrenamiento/firmas/<?php echo $datos->{'firma_jefe'} ?>" alt="" width='150'></td>
                </tr>
                <?php } ?>
                <tr>
                    <th class="ttitulo">&nbsp;FIRMA DEL FUNCIONARIO: </th>
                    <td height='50' colspan="2"><img src="<?php echo base_url(); ?>/archivos_adjuntos/talento_cuc/entrenamiento/firmas/<?php echo $datos->{'firma_fun'} ?>" alt="" width='150'></td>
                </tr>
                <tr>
                    <th class="ttitulo">&nbsp;FECHA DE RECIBIDO: </th>
                    <td height='30' colspan="2">&nbsp;<?php echo $datos->{'fecha_recibido'} ?></td>
                </tr>
            </table>
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
$mpdf = new Mpdf(['margin-top' => 30,'default_font' => 'cuc', 'default_font_size' => 9, 'format' => 'letter', ]);


$stylesheet = file_get_contents($css_boot);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

$mpdf->WriteHTML($html);

$mpdf->Output("archivos_adjuntos/talento_cuc/entrenamiento/$fileName.pdf",\Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";    