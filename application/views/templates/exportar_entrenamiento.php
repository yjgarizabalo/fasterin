<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName =  'Entrenamiento_'.$persona[0]['id_persona'];
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
<head>
    <title>Plan de Entrenamiento</title>
</head>
<body id='body_des_plan'>
    <htmlpageheader name="myHeader1">        
    </htmlpageheader>
        <table width="100%" class="table table-bordered table_footer">
            <tr>
                <td class="text-center" rowspan="4"><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='60'></td>
                <td class="text-center" rowspan="4">PLAN DE ENTRENAMIENTO</td>
                <td class="text-center"><?php echo date("Y-m-d") ?></td>
            </tr>
        </table>
    <htmlpagebody>
        <div class="container">
            <div class="col-md-12">
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th class="nombre_tabla" colspan="6"> Información del Funcionario</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Nombre: </td>
                        <td colspan='5' ><?php echo $persona[0]['nombre_completo'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">identificación:</td>
                        <td class="info_estado" colspan='2'><?php echo $persona[0]['id_persona'] ?></td>
                        <td class="ttitulo">Cargo:</td>
                        <td class="info_fecha" colspan='2'><?php echo $persona[0]['cargo'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha de ingreso:</td>
                        <td class="info_t_solicitud" colspan='5'><?php echo $persona[0]['fecha_ingreso'] ?></td>
                    </tr>
                </table>
                <?php
                $i=0;
                foreach($datos as $row){ 
                    $i++;    
                ?>
                <table class="table table-bordered table-condensed">
                <tr>
                        <th class="nombre_tabla" colspan="6"><?php echo $i ?>. Oferta de Entrenamiento</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Nombre Oferta: </td>
                        <td colspan='5' ><?php echo $row['oferta'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Facilitador:</td>
                        <td colspan='2'><?php echo $row['facilitador'] ?></td>
                        <td class="ttitulo">Duración:</td>
                        <td colspan='2'><?php echo $row['duracion'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha:</td>
                        <td colspan='2'><?php echo $row['fecha'] ?></td>
                        <td class="ttitulo">Hora:</td>
                        <td colspan='2'><?php echo $row['hora'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Lugar de Entrenamiento: </td>
                        <td colspan='2' ><?php echo $row['lugar'] ?></td>
                        <td class="ttitulo">Asistencia: </td>
                        <td colspan='2'><?php if($row['asistencia'] == 1) echo 'SI'; else echo 'NO'; ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Link de la Reunión: </td>
                        <td colspan='5' ><a href="<?php  echo $row['link'] ?>" target="_blank">Link Reunión</a></td>
                    </tr>
                </table>
                <?php } ?>
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
$mpdf = new Mpdf(['margin-top' => 30,'default_font' => 'cuc', 'default_font_size' => 9, 'format' => 'letter', ]);


$stylesheet = file_get_contents($css_boot);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

$mpdf->WriteHTML($html);

$mpdf->Output("archivos_adjuntos/talento_cuc/entrenamiento/$fileName.pdf",\Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";    