<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

switch ($datos[0]['id_tipo_solicitud']){
    case 'Hum_Prec': //pregrado
        $nombre_detalle = 'detalle_req_prec';
    break;
    case 'Hum_Admi': //administrativos
        $nombre_detalle = 'detalle_req_admi';
    break;
    case 'Hum_Apre': //aprendices
        $nombre_detalle = 'detalle_req_apre';
    break;
    default:
        $nombre_detalle = 'detalle_req';
    break;
}

$fileName =  $nombre_detalle.$datos[0]['id_solicitud'];
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
                <td class="text-center" rowspan="4"><?php echo strtoupper($datos[0]['tipo_solicitud']) ?></td>
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
                        <td class="info_t_solicitud" colspan='5'><?php echo $datos[0]['tipo_solicitud'] ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th class="nombre_tabla" colspan="6"> Información de la Requisición</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Tipo de Solicitud:</td>
                        <td class="" colspan="3"><?php echo $datos[0]['t_solicitud'] ?></td>
                        <td class="ttitulo">Tipo de Vacante:</td>
                        <td class=""><?php echo $datos[0]['t_vacante'] ?></td>
                    </tr>
                    <?php if($datos[0]['reemplazado'] != ''){?>
                    <tr>
                        <td class="ttitulo">Nombre:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['reemplazado'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="ttitulo">Departamento:</td>
                        <td class="" colspan="3"><?php echo $datos[0]['departamento'] ?></td>
                        <td class="ttitulo">Cargo:</td>
                        <td class=""><?php echo $datos[0]['cargo'] ?></td>
                    </tr>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Admi'){?>
                    <tr>
                        <td class="ttitulo">Nombre del Cargo:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['nombre_cargo'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Admi'){?>
                    <tr>
                        <td class="ttitulo">Experiencia Laboral:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['experiencia_laboral'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Conocimientos Especificacios del cargo:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['plan_trabajo'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Admi' || ($datos[0]['id_tipo_solicitud'] == 'Hum_Prec' && $datos[0]['t_solicitud_vac'] == 'Tcsep_Con')){?>
                    <tr>
                        <td class="ttitulo">Pregrado Requerido:</td>
                        <td class="" colspan="3"><?php echo $datos[0]['pregrado'] ?></td>
                        <td class="ttitulo">Posgrado Requerido:</td>
                        <td class=""><?php echo $datos[0]['posgrado'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Prec'){?>
                    <tr>
                        <td class="ttitulo">Horas de Clases:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['horas'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Prec' && $datos[0]['t_solicitud_vac'] == 'Tcsep_Con'){?>
                    <tr>
                        <td class="ttitulo">Línea de Investigación:</td>
                        <td class="" colspan="3"><?php echo $datos[0]['linea_investigacion'] ?></td>
                        <td class="ttitulo">Años de Experiencia:</td>
                        <td class=""><?php echo $datos[0]['anos_experiencia'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="ttitulo">Plan de Trabajo:</td>
                        <td class="" colspan='5'><?php echo $datos[0]['plan_trabajo'] ?></td>
                    </tr>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Admi'){?>
                    <tr>
                        <td class="ttitulo">Tipo de Contrato:</td>
                        <td class="" colspan="3"><?php echo $datos[0]['nombre_tipo_contrato'] ?></td>
                        <td class="ttitulo">Duración Contrato (Meses):</td>
                        <td class=""><?php echo $datos[0]['duracion_contrato'] ?></td>
                    </tr>
                    <?php } ?>
                    <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Prec' && $datos[0]['t_solicitud_vac'] == 'Tcsep_Eva'){?>
                    <tr>
                        <td class="ttitulo">Visto bueno pedagógico:</td>
                        <td class="" colspan="5"><?php echo $datos[0]['vb_pedagogico'] ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="ttitulo">Observaciones:</td>
                        <td class="" colspan='5'><?php echo $datos[0]['observaciones'] ?></td>
                    </tr>
                </table>
                <?php if($datos[0]['id_tipo_solicitud'] == 'Hum_Prec'){?>                   
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
							<th colspan="2" class="nombre_tabla">Programas Asignadas</th>
						</tr>
						<tr>
							<td class="ttitulo">#</td>
							<td class="ttitulo">Dependencia</td>
						</tr>
					</thead>
					<tbody>
                    <?php
                        $i=0;
                        foreach ($datos[0]['programs'] as $row){
                            $i++;
                            echo '<tr><td>'.$i.'</td><td class="">'.$row['nombre'].'</td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
							<th colspan="2" class="nombre_tabla">Materias Asignadas</th>
						</tr>
						<tr>
							<td class="ttitulo">#</td>
							<td class="ttitulo">Materia</td>
						</tr>
					</thead>
					<tbody>
                    <?php
                        $i=0;
                        foreach ($datos[0]['subjects'] as $row){
                            $i++;
                            echo '<tr><td>'.$i.'</td><td class="">'.$row['materia'].'</td></tr>';
                        }
                    ?>
                    </tbody>
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

$mpdf->Output("archivos_adjuntos/talentohumano/detalles_solicitudes/$fileName.pdf",\Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";    