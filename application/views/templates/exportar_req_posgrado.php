<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "detalle_req_posgrado_".$datos[0]['id_solicitud'];
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
<head>
    <title>Detalle Solicitud Requisición</title>
</head>
<body id='body_des_plan'>
    <htmlpageheader name="myHeader1">        
    </htmlpageheader>
        <table width="100%" class="table table-bordered table_footer">
            <tr>
                <td class="text-center" rowspan="4"><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='60'></td>
                <td class="text-center" rowspan="4">REQUISICIÓN DE POSGRADO</td>
                <td class="text-center"><?php echo date("Y-m-d") ?></td>
            </tr>
        </table>
    <htmlpagebody>
        <div class="container">
            <div class="col-md-12">
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th class="nombre_tabla" colspan="6"> Información de la Solicitud</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Solicitante: </td>
                        <td colspan='5' ><?php echo $datos[0]['solicitante'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Estado:</td>
                        <td class="info_estado" colspan='3'><?php echo $datos[0]['estado'] ?></td>
                        <td class="ttitulo">Fecha de Solicitud:</td>
                        <td class="info_fecha"><?php echo $datos[0]['fecha_solicitud'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Tipo de Solicitud:</td>
                        <td class="info_t_solicitud" colspan='3'><?php echo $datos[0]['tipo_solicitud'] ?></td>
                        <td class="ttitulo">Tipo de Vacante:</td>
                        <td class="info_t_vacante"><?php echo $datos[0]['tipo_vacante'] ?></td>
                    </tr>
                    <tr id="tr_reemplazo_pos">
                        <td class="ttitulo">Reemplazo:</td>
                        <td class="info_reemplazo" colspan='5'><?php echo $datos[0]['reemplazo'] ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th class="nombre_tabla" colspan="2"> Información de la Requisición</th>
                        <th colspan="4" class="sin-borde text-right border-left-none"></th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Candidato:</td>
                        <td class="nombre_candidato" colspan='5'><?php echo $datos[0]['candidato'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Departamento:</td>
                        <td class="nombre_departamento" colspan="3"><?php echo $datos[0]['departamento'] ?></td>
                        <td class="ttitulo">Tipo de Programa:</td>
                        <td class="tipo_programa"><?php echo $datos[0]['tipo_programa'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Programa:</td>
                        <td class="nombre_programa" colspan="5"><?php echo $datos[0]['programa'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Nombre módulo:</td>
                        <td class="nombre_modulo" colspan="3"><?php echo $datos[0]['nombre_modulo'] ?></td>
                        <td class="ttitulo">Horas módulo:</td>
                        <td class="horas_modulo"><?php echo $datos[0]['horas_modulo'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Número de Promoción:</td>
                        <td class="numero_promocion"  colspan="3"><?php echo $datos[0]['numero_promocion'] ?></td>
                        <td class="ttitulo">Valor a pagar por hora:</td>
                        <td class="valor_hora"><?php echo $datos[0]['valor_hora'] ?></td>
                    </tr>
                    <tr>						
                        <td class="ttitulo">Dedicación/Cargo:</td>
                        <td class="dedicacion" colspan='3'><?php echo $datos[0]['cargo'] ?></td>
                        <td class="ttitulo">Ciudad de origen:</td>
                        <td class="ciudad_origen"><?php echo $datos[0]['ciudad_origen'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha inicio:</td>
                        <td class="fecha_inicio" colspan='3'><?php echo $datos[0]['fecha_inicio'] ?></td>
                        <td class="ttitulo">Fecha terminación:</td>
                        <td class="fecha_terminacion"><?php echo $datos[0]['fecha_terminacion'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Tipo Orden:</td>
                        <td class="tipOrden" colspan='2'><?php echo $datos[0]['tipo_orden'] ?></td>
                        <td class="ttitulo">Código SAP:</td>
                        <td class="codigoSap" colspan='2'><?php echo $datos[0]['codigo_sap'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Observaciones:</td>
                        <td class="observaciones" colspan='5'><?php echo $datos[0]['observaciones'] ?></td>
                    </tr>
                </table>
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

$mpdf->Output("archivos_adjuntos/talentohumano/detalles_solicitudes/$fileName.pdf",\Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";    