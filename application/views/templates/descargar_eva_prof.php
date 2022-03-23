<?php

require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Plan - ".$datos['nombre_completo'] ;
$title = "Evaluación Docente";
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>

<head>
    <title> <?= $title ?> </title>
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
            <h4 class="title-header" style="color: #6e1f7c; width: 74%; text-align: center; float: right;">
                <?php echo "CIENCIAS EMPRESARIAL - ADMINISTRACION DE EMPRESAS" ?></h4>
            <h4 class="title-header"
                style="color: #6e1f7c; width: 54%; text-align: center; float: right; margin-top: -10;">
                <?php echo "AREA: ADMINISTRACIÓN Y ORGANIZACIÓN" ?>
            </h4>
        </div>
        <div class="modal-body" id="bodymodal">
            <div>
                <div class="col-md-12" style="padding: 0px !important;">
                    <div class="col-md-3" style="background-color: #ffffff; font-size: 20px;">
                        <table class="table detalle_plan_des">

                            <tr class="nombre_tabla">
                                <td colspan='7' style="font-size: 15px; font-weight: bold;">Datos del
                                    profesor</td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Nombre</td>
                                <td class="nombre_completo" colspan='6' style="font-size: 15px;">
                                    <?php echo $datos['nombre_completo'] ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">identificación</td>
                                <td class="identificacion" colspan='6' style="font-size: 15px;">
                                    <?php echo $datos['identificacion'] ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Contratación</td>
                                <td colspan='6' class="dedicacion" style="font-size: 15px;">
                                    <?php echo //$datos['dedicacion'] 
                                        "N/A" ?></td>
                            </tr>
                            <tr class="nombre_tabla contrato" style="font-size: 15px;">
                                <td colspan='7' style="font-size: 15px; font-weight: bold;">CONTRATO</td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Rol(es)</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "PROFESOR EN ENCARGO: VICERRECTORÍA DE EXTENSIÓN" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Tipo contrato</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "FIJO - TIEMPO COMPLETO - ASISTENTE 1" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Fecha Inicio</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "2020/01/01" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Fecha Final</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "2021/01/01" ?></td>
                            </tr>
                            <tr class="nombre_tabla Formacion" style="text-align: left; font-size: 15px;">
                                <td colspan='7' style="font-size: 15px; font-weight: bold;">FORMACIÓN</td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Formación</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "Pendiente" ?></td>
                            </tr>
                            <tr class="nombre_tabla otros" style="font-size: 15px;">
                                <td colspan='7' style="font-size: 15px; font-weight: bold;">OTROS</td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Grado</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "PREGRADO" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Ingles</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "-" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Grupo</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "N/A" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Linea</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "N/A" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Perfil</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "GESTIÓN UNIVERSITARIA" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Categoria</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Citas</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "" ?></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" style="text-align: left; font-size: 15px;">Indice H</td>
                                <td colspan='6' class="escalafon" style="font-size: 15px;">
                                    <?php echo // $datos['escalafon'] 
                                    "" ?></td>
                            </tr>
                        </table>

                    </div>

                    <div class="col-md-12"
                        style="background-color: #ffffff; margin-bottom: 10px; padding: 0px !important;">
                        <div class="col-md-12" style="margin: 15px 0 15px 0;">
                            <div class="col-md-12">
                                <p style="font-size: 20px; font-weight: bold;" class="cbxeva_desemp"></p>
                            </div>
                            <div class="col-md-12" style="padding: 0px !important;">
                                <div class="col-md-6">
                                    <canvas id="myChart" width="1" height="1"></canvas>
                                </div>
                                <div class="col-md-6 ">
                                    <p style="font-size: 20px; font-weight: bold; text-align: center;">PORCENTAJES</p>
                                    <canvas id="myChart2" width="100" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12"
                        style="background-color: #ffffff; margin-bottom: 10px; padding: 0px !important;">
                        <div class="col-md-4" style="text-align: center; margin-top: 10px;">
                            <div class="col-md-12 alert alert-info" role="alert">
                                <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                                <p><strong>Rango de la evaluacion de desempeño</strong></p>
                                <div class="col-md-4">
                                    <table class="tabla__plan" style="width: 100%">
                                        <tr>
                                            <th style="color: blue;  text-align: center;">
                                                SOBRESALIENTE</th>
                                            <th style="color: green; text-align: center; margin-right: 10px;">
                                                BUENO</th>
                                            <th style="color: orange; font-weight: bold; text-align: center;">
                                                ACEPTABLE</th>
                                            <th style="color: red; font-weight: bold; text-align: center;">
                                                DEFICIENTE</th>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center; width: 25%;">
                                                <strong>
                                                    < 4.5 </strong>
                                            </td>
                                            <td style="text-align: center; width: 25%;">
                                                <strong>
                                                    ≤ 4,5 & ≥4
                                                </strong>
                                            </td>
                                            <td style="text-align: center; width: 25%;">
                                                <strong>
                                                    < 4 & ≥3,75 </strong>
                                            </td>
                                            <td style="text-align: center; width: 25%;">
                                                <strong>
                                                    >3,75 </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <table class="tabla__plan" style="width: 100%">
                                <tr>
                                    <th style="color: #000000; font-size: 20px; font-weight: bold; text-align: center;">
                                        EVALUACIÓN DESEMPEÑO</th>
                                    <th
                                        style="color: #000000; font-size: 20px; font-weight: bold; text-align: center; margin-right: 10px;">
                                        EVALUACIÓN ESTUDIANTE</th>
                                </tr>
                                <tr>
                                    <td
                                        style="color: #000000; font-size: 17px; text-align: center; width: 33%; font-size: 16px;">
                                        BUENO
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="color: #000000; font-weight: bold; text-align: center; width: 33%; font-size: 15px">
                                        4,5</td>
                                    <td
                                        style="tcolor: #000000;  font-weight: bold;text-align: center; width: 33%; font-size: 20px;">
                                        4,5
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                    <div class="col-md-12"
                        style="background-color: ffffff; margin-bottom: 10px; padding: 0px !important; border-radius: 5px;">
                        <div class="col-md-12">
                            <p style="font-size: 20px; font-weight: bold; margin-top: 10px;" class="cbxlogros">LOGROS
                                20-1</p>
                        </div>
                        <div class="col-md-12">
                            <table class="tabla__plan" style="width: 100%">
                                <tr>
                                    <th style="color: #4f8df5; font-size: 30px; font-weight: bold; text-align: center;">
                                        0</th>
                                    <th
                                        style="color: #80816c; font-size: 30px; font-weight: bold; text-align: center; margin-right: 10px;">
                                        0</th>
                                    <th style="color: #2d9662; font-size: 30px; font-weight: bold; text-align: center;">
                                        0</th>
                                </tr>
                                <tr>
                                    <td style="text-align: center; width: 33%;">DTI</td>
                                    <td style="text-align: center; width: 33%;">ASC</td>
                                    <td style="text-align: center; width: 33%;">SUPERAVIT</td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <table style="width: 100%" class="tabla__articulos">
                                <tr class="tabla__des__title">
                                    <th>ARTICULOS</th>
                                    <th>Q4</th>
                                    <th>Q3</th>
                                    <th>Q2</th>
                                    <th>Q1</th>
                                </tr>
                                <tr>
                                    <td class="tabla__des__valor">ESTRUCTURA</td>
                                    <td>2</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td class="tabla__des__valor">POSTULADO</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td class="tabla__des__valor">ACEPTADA</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td class="tabla__des__valor">PUBLICADO</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                            </table>
                        </div>
                        <br>
                    </div>
                    <div class="col-md-12"
                        style="background-color: #ffffff; padding: 10px 0 0 0 !important; border-radius: 5px;">
                        <div class="col-md-6" style="margin-top: 15px;">
                            <div class="col-md-12">
                                <p style="font-size: 20px; font-weight: bold;" class="cbxplanTrab">PLAN DE TRABAJO
                                    2020-2
                                </p>
                            </div>
                            <div class="col-md-12 margin1">
                                <div class="alert alert-info" role="alert">
                                    <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                                    <p id="cart1" class="text-justify"><b>La información correspondiente al plan de
                                            trabajo podrá ser modificada y lo oficial está en el módulo de plan de
                                            trabajo</b></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table style="width: 100%" class="tabla__plan">
                                    <tr>
                                        <th style="text-align: center; width: 12%;">DO</th>
                                        <th style="text-align: center; width: 12%;">PAE</th>
                                        <th style="text-align: center; width: 12%;">DI</th>
                                        <th style="text-align: center; width: 12%;">INV</th>
                                        <th style="text-align: center; width: 12%;">ROL INV</th>
                                        <th style="text-align: center; width: 12%;">EXT</th>
                                        <th style="text-align: center; width: 12%;">GEST U</th>
                                        <th style="text-align: center; width: 12%;">TOTAL</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; width: 12%;">100</td>
                                        <td style="text-align: center; width: 12%;">40</td>
                                        <td style="text-align: center; width: 12%;">54</td>
                                        <td style="text-align: center; width: 12%;">100</td>
                                        <td style="text-align: center; width: 12%;">115</td>
                                        <td style="text-align: center; width: 12%;">15</td>
                                        <td style="text-align: center; width: 12%;">106</td>
                                        <td style="text-align: center; width: 12%;">789</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <p style="font-size: 20px; font-weight: bold;">METAS</p>
                            </div>
                            <div class="col-md-12">
                                <table class="tabla__plan" style="width: 100%">
                                    <tr>
                                        <th
                                            style="color: #4f8df5; font-size: 30px; font-weight: bold; text-align: center;">
                                            0</th>
                                        <th
                                            style="color: #80816c; font-size: 30px; font-weight: bold; text-align: center; margin-right: 10px;">
                                            0</th>
                                        <th
                                            style="color: #2d9662; font-size: 30px; font-weight: bold; text-align: center;">
                                            0</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center; width: 33%;">DTI</td>
                                        <td style="text-align: center; width: 33%;">ASC</td>
                                        <td style="text-align: center; width: 33%;">SUPERAVIT</td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <table style="width: 100%" class="tabla__articulos">
                                    <tr class="tabla__des__title">
                                        <th>ARTICULOS</th>
                                        <th>PUBLICADO</th>
                                    </tr>
                                    <tr>
                                        <td class="tabla__des__valor">Q1</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td class="tabla__des__valor">Q2</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td class="tabla__des__valor">Q3</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td class="tabla__des__valor">Q4</td>
                                        <td>0</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-top: 20px; font-size: 20px;">
                            <p>OBSERVACIONES</p>
                            <p style="background-color: #ffffff; padding: 5px;">ACTIVO</p>
                        </div>
                        <br>
                    </div>
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

$mpdf->WriteHTML($html);

$mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);