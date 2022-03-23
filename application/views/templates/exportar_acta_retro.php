<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "ACTA_".$datos[0]['id_solicitud'];
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
<head>
    <title>Acta de Retroalimentación</title>
</head>
<body id='body_des_plan'>
    <htmlpageheader name="myHeader1">
        <table width="100%" class="table table-bordered table_footer">
            <tr>
                <td class="text-center" rowspan="4"><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='40'></td>
                <td class="text-center" rowspan="4" style="font-size:8px;">FORMATO DE RETROALIMENTACIÓN INSTITUCIONAL</td>
                <td style="font-size:8px;">&nbsp;<?php echo $version ?><br>&nbsp;<?php echo $fecha?><br>&nbsp;<?php echo $trd?></td>
            </tr>
        </table>        
    </htmlpageheader>
    
    <htmlpagebody>
        <div class="container">
            <div class="col-md-12">
                <table class="table table-bordered" style="font-size:10px;">
                    <tr>
                        <td colspan='4'><p><span style="font-weight: bold;">Instrucciones</span>: De acuerdo con la evaluación realizada por favor retroalimente al colaborador los resultados de la evaluación e indique cada uno de los aspectos mencionados a continuación.</p></td>
                    </tr>
                    <tr>
                        <td colspan='4'><p><span style="font-weight: bold;">Objetivo</span>: Retroalimentar a los colaboradores acerca de su desempeño, para que tengan una idea clara de sus fortalezas y oportunidades de mejora y puedan mejorar o mantener el mismo.</p></td>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr class="nombre_tabla">
                        <th colspan='4' style="font-size: 10px; background-color: #ECF1F4;">I.	DATOS DEL FUNCIONARIO EVALUADO</th>
                    </tr>
                    <tr>
                        <td class="ttitulo"  style="font-size: 10px;">Nombre:</td>
                        <td style="font-size: 10px;"><?php echo $datos[0]['nombre_completo'] ?></td>
                        <td class="ttitulo"  style="font-size: 10px;">Identificación:</td>
                        <td style="font-size: 10px;"><?php echo $datos[0]['identificacion'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo" style="font-size: 10px;">Cargo/Dependencia:</td>
                        <td style="font-size: 10px;" colspan='3'><?php echo $datos[0]['departamento'] ?></td>
                    </tr>
                    <tr class="nombre_tabla">
                        <th colspan='4' style="font-size: 10px; background-color: #ECF1F4;">II.	DATOS DEL JEFE EVALUADOR</th>
                    </tr>
                    <tr>
                        <td class="ttitulo" style="font-size: 10px;">Nombre:</td>
                        <td style="font-size: 10px;"><?php echo $datos[0]['nombre_jefe'] ?></td>
                        <td class="ttitulo" style="font-size: 10px;">Identificación:</td>
                        <td style="font-size: 10px;"><?php echo $datos[0]['cc_jefe_inmediato'] ?></td>
                    </tr>
                    <tr>
                        <td class="ttitulo" style="font-size: 10px;">Cargo/Dependencia:</td>
                        <td style="font-size: 10px;" colspan='3'><?php echo $datos[0]['departamento_jefe'] ?></td>
                    </tr>
                    <tr class="nombre_tabla">
                        <th colspan='3' style="font-size: 10px; background-color: #ECF1F4;">III. PERIODO A EVALUAR:</th>
                        <td style="font-size: 10px;"><?php echo $datos[0]['periodo'] ?></td>
                    </tr>
                    <tr class="nombre_tabla">
                        <th colspan='3' style="font-size: 10px; background-color: #ECF1F4;">IV. FECHA DE LA RETROALIMENTACIÓN:</th>
                        <td style="font-size: 10px;"><?php echo $datos[0]['fecha'] ?></td>
                    </tr>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr class="nombre_tabla">
                        <th colspan="4" style="font-size: 10px; background-color: #ECF1F4;">V. RESULTADO DETALLADO DE LA EVALUACIÓN DE DESEMPEÑO </th>
                    </tr>
                    <tr class="ttitulo">
                        <th colspan="4" class="text-center" style="font-size: 10px; background-color: #ECF1F4;">COMPETENCIAS (<?php echo $peso_comp ?>%)</th>
                    </tr>
                    <tr class="filaprincipal" style="background-color: #ECF1F4;">
                        <td class="ttitulo text-center" style="font-size: 10px;">COMPETENCIAS</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">Fortaleza</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">Oportunidad<br>de Mejora</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">PUNTAJE</td>
                    </tr>
                    <?php 
                        $suma = 0;
                        $i = 0;
                        foreach ($competencias as $row){
                        if($row['id_aux'] == 'Eval_Comp'){
                            $f = $row['fortaleza'] == 1 ? 'x' : '';
                            $m = $row['mejora'] == 1 ? 'x' : '';
                            $suma += $row['puntaje'];
                            $i++;
                            echo ' <tr>
                                    <td class="text-left" style="font-size: 8px;">'.$row['competencia'].': '.$row['pregunta'].'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$f.'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$m.'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$row['puntaje'].'</td>
                                </tr>';
                            }
                        }
                    ?>
                    <tr>
                        <th colspan='3' class="text-left" style="font-size: 10px;">PUNTAJE TOTAL DE EVALUACIÓN DE COMPETENCIAS:</th>
                        <th class="text-center" style="font-size: 10px;"><?php echo ($suma/$i) ?></th>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="ttitulo">
                        <th colspan="4" class="text-center" style="font-size: 10px; background-color: #ECF1F4;">CUMPLIMIENTO (<?php echo $peso_cump ?>%)</th>
                    </tr>
                    <tr class="filaprincipal" style="background-color: #ECF1F4;">
                        <td class="ttitulo text-center" style="font-size: 10px;">INDICADORES DE DESEMPEÑO TÉCNICO</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">Fortaleza</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">Oportunidad<br>de Mejora</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">PUNTAJE</td>
                    </tr>
                    <?php 
                        $suma = 0;
                        $i = 0;
                        foreach ($competencias as $row){
                            if($row['id_aux'] == 'Eval_Cump'){
                            $f = $row['fortaleza'] == 1 ? 'x' : '';
                            $m = $row['mejora'] == 1 ? 'x' : '';
                            $suma += $row['puntaje'];
                            $i++;
                            echo ' <tr>
                                    <td class="text-left" style="font-size: 8px;">'.$row['competencia'].': '.$row['pregunta'].'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$f.'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$m.'</td>
                                    <td class="text-center" style="font-size: 10px;">'.$row['puntaje'].'</td>
                                </tr>';
                            }
                        }
                    ?>
                    <tr>
                        <th colspan='3' class="text-left" style="font-size: 10px;">PUNTAJE TOTAL DE EVALUACIÓN DE CUMPLIMIENTO:</th>
                        <th class="text-center" style="font-size: 10px;"><?php echo ($suma/$i) ?></th>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>                            
                    <tr class="ttitulo">
                        <th colspan="4" class="text-center" style="font-size: 10px; background-color: #ECF1F4;">METAS DE DESEMPEÑO (<?php echo $peso_metas ?>%)</th>
                    </tr>
                    <tr class="filaprincipal" style="background-color: #ECF1F4;">
                        <td class="ttitulo text-center" colspan='3' style="font-size: 10px;">METAS</td>
                        <td class="ttitulo text-center" style="font-size: 10px;">PUNTAJE POR META DE DESEMPEÑO</td>
                    </tr>
                    <?php 
                    $suma = 0;
                    $i = 0;
                        foreach ($metas as $ind){
                            $i++;
                            if($ind['periodo'] == '2020'){
                                $suma += $ind['puntaje'];                                
                                $puntaje = $ind['puntaje'].' - '.$ind['descripcion'];
                            }else{
                                $puntaje = $ind['cumplimiento'].'%';
                            }
                        echo ' <tr>
                                <td class="text-left" colspan="3" style="font-size: 10px;">'.$ind['pregunta'].'</td>
                                <td class="text-center" style="font-size: 10px;">'.$puntaje.'</td>
                            </tr>';
                        }
                        if($i > 0 && $suma > 0) $resultado_meta = ($suma/$i);
                    ?>
                    <tr>
                        <th colspan='3' class="text-left" style="font-size: 10px;">PUNTAJE TOTAL DE METAS DE DESEMPEÑO:</th>
                        <th class="text-center" style="font-size: 10px;"><?php echo $resultado_meta ?></th>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr class="ttitulo">
                        <th colspan="2" rowspan="<?php echo (count($tipo_evaluador) + 3) ?>" class="text-center" style="font-size: 10px; background-color: #ECF1F4;">RESULTADO DE <?php echo strtoupper($datos[0]['metodo']); ?></th>
                    </tr>
                    <tr class="filaprincipal">
                        <td class="ttitulo" style="font-size: 10px;">Evaluador</td>
                        <td class="ttitulo" style="font-size: 10px;">Puntuación</td>
                    </tr>
                    <?php 
                        foreach ($tipo_evaluador as $t){
                        echo ' <tr>
                                <td class="text-left" style="font-size: 10px;">'.$t['tipo_evaluador'].'</td>
                                <td class="text-center" style="font-size: 10px;">'.$t['producto'].'</td>
                            </tr>';
                        }
                    ?>
                    <tr>
                        <td class="text-left" style="font-weight: bold; font-size: 10px;">TOTAL:</td>
                        <td class="text-center" style="font-weight: bold; font-size: 10px;"><?php echo $puntuacion_directa ?></td>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="2" style="font-size: 10px; background-color: #ECF1F4;">PORCENTAJE DE DESEMPEÑO DEL COLABORADOR:</th>
                        <th class="text-left" colspan="2" style="font-size: 10px;"><?php echo $puntuacion_centil.'% '.$valoracion ?></th>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr class="nombre_tabla">
                        <th style="font-size: 10px; background-color: #ECF1F4;">VI. METAS DE DESEMPEÑO POR PARTE DEL EVALUADO</th>
                    </tr>
                    <tr>
                        <td class="text-left" style="font-size: 10px;"><span style="font-weight: bold;">METAS DE DESEMPEÑO POR PARTE DEL EVALUADO</span><br>Indique las Metas de Desempeño por parte del colaborador que posibiliten su oportunidad de mejora.</td>
                    </tr>
                    <tr>
                        <td class="text-center" style="font-weight: bold; font-size: 10px;">Metas de Desempeño por parte del evaluado</td>
                    </tr>
                    <?php 
                    $i=1;
                        foreach ($compromisos as $con){
                        echo '<tr>
                                <td class="text-left" style="font-size: 10px;">'.$i.'. '.$con['compromiso'].'</td>
                                </tr>';
                            $i++;
                        }
                    ?>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="nombre_tabla">
                        <th style="font-size: 10px; background-color: #ECF1F4;">VII. SUGERENCIAS DE FORMACIÓN Y CAPACITACIÓN</th>
                    </tr>
                    <tr>
                        <td class="text-left" style="font-size: 10px;">Indique los aspectos en los que sugiere formación y/o capacitación por parte del colaborador.</td>
                    </tr>
                    <?php 
                    $i=1;
                        foreach ($sugerencias as $sg){
                        echo '<tr>
                                <td class="text-left" style="font-size: 10px;">'.$i.'. '.$sg['observacion'].'</td>
                                </tr>';
                            $i++;
                        }
                    ?>   
                </table>
                <table class="table table-bordered" style="font-size: 10px;">
                    <tr>
                        <td width="50%" >FIRMA DEL COLABORADOR EVALUADO</td>
                        <td width="50%" ><?php if($datos[0]['firma_colaborador']){ ?>
                            <img src="<?php echo base_url(); ?>/archivos_adjuntos/talentohumano/actas/firmas/<?php echo $datos[0]['firma_colaborador'] ?>" width="200">
                        <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" >FIRMA DEL RETROALIMENTADOR O JEFE INMEDIATO</td>
                        <td width="50%" ><img src="<?php echo base_url(); ?>/archivos_adjuntos/talentohumano/actas/firmas/<?php echo $datos[0]['firma_jefe'] ?>" width="200"></td>
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

$mpdf->Output("archivos_adjuntos/talentohumano/actas/$fileName.pdf",\Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";