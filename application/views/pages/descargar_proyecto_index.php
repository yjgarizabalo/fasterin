<?php
$perfil = $_SESSION["perfil"] ;
$administra = $perfil == "Per_Admin"  || $perfil== "Per_Adm_plan" || $perfil == "Per_Csep" || $permiso ? true : false;
// Require composer autoload
require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Proyecto" . ($datos->nombre_proyecto ? " - " . $datos->nombre_proyecto : '');
$title = "Proyecto Index de $datos->nombre_tipo_proyecto";
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';

function vacio($tipo=1, $celdas=0) {
    if ($tipo == 2) {
        return "<tr>
                    <td colspan='$celdas'><p style='text-align: center'>Vacío</p></td>
                </tr>";
    } else {
        return "<p style='text-align: center'>Vacío</p>";
    }
}

//Activa el almacenamiento en búfer de la salida
ob_start();
?>

<html>

<head>
    <title><?= $title ?></title>
</head>

<body>
    <htmlpageheader name="myHeader1">
        <table width="100%" class="table_footer">
            <tr>
                <td width="33.33%" style="text-align: left; font-size: 10px;">Generado el <?php echo strftime("%Y-%m-%d a las %T", strtotime(date("Y-m-d H:i:s"))) ?></td>
                <td width="33.33%" style="text-align: center; font-size: 10px;">AGIL</td>
                <td width="33.33%" style="text-align: left; font-size: 10px;">Convocatoria CONV-15-2020 Enero 2020 - Diciembre 2023</td>
            </tr>
        </table>
    </htmlpageheader>
    <htmlpagebody>
        <table style="margin-bottom: 20px;">
            <tr>
                <td width="30%" style="text-align: center">
                    <img src="<?php echo base_url() ?>imagenes/LogocucF.png" alt="Logo CUC" style="width:130px; height:130px;">
                </td>
                <td width="70%" style="text-align: center">
                    FORMATO ÍNDEX PARA LA PRESENTACIÓN DE PROPUESTAS DE INVESTIGACIÓN, EXTENSIÓN,
                    INTERNACIONALIZACIÓN, DOCENCIA, BIENESTAR, LABORATORIO Y GESTIÓN UNIVERSITARIA
                </td>
            </tr>
        </table>
        <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <tr class="nombre_tabla">
                <td colspan="8">Información General del Proyecto</td>
            </tr>
            <tr>
                <td colspan="2" class="ttitulo">Título del Proyecto</td>
                <td colspan="6"><?php echo ($datos->nombre_proyecto && !empty($datos->nombre_proyecto)) ? $datos->nombre_proyecto : vacio() ?></td>
            </tr>
            <tr>
                <td colspan="2" class="ttitulo">Estado del Proyecto</td>
                <td colspan="6"><?= $datos->estado_proyecto ?></td>
            </tr>
            <tr>
                <td colspan="2" class="ttitulo">Tipo de Proyecto</td>
                <td colspan="6"><?= $datos->nombre_tipo_proyecto ?></td>
            </tr>
            <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Int') { ?>
                <tr>
                    <td colspan="1" class="ttitulo">¿Operacionaliza?</td>
                    <td colspan="3"><?php echo $datos->codigo_convenio != null ? 'Sí' : 'No' ?></td>
                    <td colspan="1" class="ttitulo">Código del Convenio</td>
                    <td colspan="3"><?php echo $datos->codigo_convenio != null ? $datos->codigo_convenio : vacio() ?></td>
                </tr>
                <tr>
                    <td colspan="1" class="ttitulo">¿Tiene Proceedings?</td>
                    <td colspan="3"><?php echo $datos->proceedings != null ? $datos->proceedings : vacio() ?></td>
                    <td colspan="1" class="ttitulo">Verificado por</td>
                    <td colspan="3"><?php echo $datos->verificado_por != null ? $datos->verificado_por : vacio() ?></td>
                </tr>
                <tr>
                    <td colspan="1" class="ttitulo">Código SAP</td>
                    <td colspan="3"><?php echo $datos->codigo_orden_sap != null ? $datos->codigo_orden_sap : vacio() ?></td>
                    <td colspan="1" class="ttitulo">Descripción</td>
                    <td colspan="3"><?php echo $datos->descripcion_orden_sap != null ? $datos->descripcion_orden_sap : vacio() ?></td>
                </tr>
                <tr>
                    <td colspan="1" class="ttitulo">Centro de Costo</td>
                    <td colspan="3"><?php echo $datos->centro_costo != null ? $datos->centro_costo : vacio() ?></td>
                    <td colspan="1" class="ttitulo">Departamento</td>
                    <td colspan="3"><?php echo $datos->departamento_centro_costo != null ? $datos->departamento_centro_costo : vacio() ?></td>
                </tr>
            <?php } ?>
            <tr><td style="height: 20px;" colspan="8"></td></tr>
            <tr class="nombre_tabla">
                <td colspan="8">Instituciones Participantes</td>
            </tr>
            <?php
                if (empty($datos->instituciones)) {
                    echo vacio(2, 8);
                } else {
                    foreach ($datos->instituciones as $institucion) {
                        echo "
                            <tr>
                                <td colspan='1' class='ttitulo'>Nombre</td>
                                <td colspan='3' style='font-weight: bold'>". $institucion['nombre'] . "</td>
                                <td colspan='1' class='ttitulo'>NIT</td>
                                <td colspan='3'>". $institucion['nit'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Teléfono</td>
                                <td colspan='3'>". $institucion['telefono'] . "</td>
                                <td colspan='1' class='ttitulo'>Pais</td>
                                <td colspan='3'>". $institucion['pais'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Correo</td>
                                <td colspan='3'>". $institucion['correo'] . "</td>
                                <td colspan='1' class='ttitulo'>Nombre Contacto</td>
                                <td colspan='3'>". $institucion['persona_contacto'] . "</td>
                            </tr>
                            <tr><td style='height: 20px' colspan='8'></td></tr>
                        ";
                    }
                }
            ?>
            <tr class="nombre_tabla">
                <td colspan="8">Investigadores Participantes</td>
            </tr>
            <?php
                if (empty($datos->participantes)) {
                    echo vacio(2, 8);
                } else {
                    foreach ($datos->participantes as $participante) {
                        echo "
                            <tr>
                                <td colspan='1' class='ttitulo'>Nombre</td>
                                <td colspan='3' style='font-weight: bold'>". $participante['nombre_completo'] . "</td>
                                <td colspan='1' class='ttitulo'>No. de Documento</td>
                                <td colspan='3'>". $participante['identificacion'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Grupo de Investigación</td>
                                <td colspan='3'>". $participante['grupo'] . "</td>
                                <td colspan='1' class='ttitulo'>Programa o Departamento</td>
                                <td colspan='3'>". $participante['programa'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Institución</td>
                                <td colspan='3'>". $participante['institucion'] . "</td>
                                <td colspan='1' class='ttitulo'>Teléfono</td>
                                <td colspan='3'>". $participante['telefono'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Correo</td>
                                <td colspan='3'>". $participante['correo'] . "</td>
                                <td colspan='1' class='ttitulo'>Vinculación</td>
                                <td colspan='3'>". $participante['vinculacion'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Rol</td>
                                <td colspan='3'>". $participante['tipo_participante'] . "</td>
                                <td colspan='1' class='ttitulo'>Formación</td>
                                <td colspan='3'>". $participante['formacion'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Responsable</td>
                                <td colspan='3'>". ($participante['id_aux_tipo_participante'] == 'Pro_Inv_Pri' ? 'Sí' : 'No') . "</td>
                                <td colspan='1' class='ttitulo'>Nombre de Usuario</td>
                                <td colspan='3'>". $participante['usuario'] . "</td>
                            </tr>
                            <tr>
                                <td colspan='1' class='ttitulo'>Escalafón</td>
                                <td colspan='3'>". $participante['escalafon'] . "</td>
                                <td colspan='1' class='ttitulo'>Tipo de Contratación</td>
                                <td colspan='3'>". $participante['contrato'] . "</td>
                            </tr>
                            <tr><td style='height: 20px' colspan='8'></td></tr>
                        ";
                    }
                }
            ?>
            <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Lab') { ?>
                <tr class="nombre_tabla">
                    <td colspan="8">Programas</td>
                </tr>
                <?php
                    if (empty($datos->programas)) {
                        echo vacio(2, 8);
                    } else {
                        foreach ($datos->programas as $programa) {
                            echo "
                                <tr>
                                    <td colspan='1' class='ttitulo'>Programa</td>
                                    <td colspan='3'>". $programa['programa'] . "</td>
                                    <td colspan='1' class='ttitulo'>Tipo de Interacción</td>
                                    <td colspan='3'>". $programa['tipo_interaccion'] . "</td>
                                </tr>
                            ";
                        }
                    }
                ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
                <tr class="nombre_tabla">
                    <td colspan="8">Asignaturas / Proyectos</td>
                </tr>
                <?php
                    if (empty($datos->asignaturas)) {
                        echo vacio(2, 8);
                    } else {
                        foreach ($datos->asignaturas as $asignatura) {
                            echo "
                                <tr>
                                    <td colspan='8'>". $asignatura['asignatura'] . "</td>
                                </tr>
                            ";
                        }
                    }
                ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Inv' || $datos->id_aux_tipo_proyecto == 'Pro_Ext' ||
                      $datos->id_aux_tipo_proyecto == 'Pro_Gra' || $datos->id_aux_tipo_proyecto == 'Pro_Doc') { ?>
                <tr class="nombre_tabla">
                    <td colspan="8">Líneas y Sublíneas de Investigación</td>
                </tr>
                <?php
                    if (empty($datos->sublineas)) {
                        echo vacio(2, 8);
                    } else {
                        foreach ($datos->sublineas as $sublinea) {
                            echo "
                                <tr>
                                    <td colspan='1' class='ttitulo'>Línea</td>
                                    <td colspan='3'>". $sublinea['linea'] . "</td>
                                    <td colspan='1' class='ttitulo'>Sublínea</td>
                                    <td colspan='3'>". $sublinea['sub_linea'] . "</td>
                                </tr>
                            ";
                        }
                    }
                ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Int' && $datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
                <tr class="nombre_tabla">
                    <td colspan="8">Objetivos de Desarrollo Sostenible</td>
                </tr>
                </table>
                    <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px;">
                        <p style="margin: 0; padding: 0;">
                            <ol>
                                <?php
                                    if (empty($datos->ods)) {
                                        echo vacio();
                                    } else {
                                        foreach ($datos->ods as $ods) {
                                            echo "<li>" . $ods['ods_completo'] . "</li>";
                                        }
                                    }
                                ?>
                            </ol>
                        </p>
                    </div>
                <table class="report detalle_plan_des" style="margin: 0; padding: 0">
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <tr class="nombre_tabla">
                <td colspan="8">Desarrollo del Proyecto</td>
            </tr>
            <tr>
                <?php if($datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
                    <td colspan="1" class="ttitulo">Fecha de inicio del proyecto</td>
                    <td colspan="3"><?php echo ($datos->fecha_inicial && !empty($datos->fecha_inicial)) ? $datos->fecha_inicial : vacio()?></td>
                    <td colspan="1" class="ttitulo">Fecha de finalización del proyecto</td>
                    <td colspan="3"><?php echo ($datos->fecha_final && !empty($datos->fecha_final)) ? $datos->fecha_final : vacio()?></td>
                <?php } else { ?>
                    <td colspan="1" class="ttitulo">Fecha de solicitud del proyecto</td>
                    <td colspan="3"><?= $datos->fecha_registra ?></td>
                <?php } ?>
            </tr>
            <tr>
                <td colspan="4" class="ttitulo">Lugar(es) de ejecución del proyecto (Ciudad y Departamento)</td>
                <td colspan="4">
                    <?php
                        if (empty($datos->lugares)) {
                            echo vacio();
                        } else {
                            $lugares = [];
                            foreach ($datos->lugares as $lugar) {
                                array_push($lugares, $lugar['ciudad']);
                            }
                            echo implode(', ', $lugares);
                        }
                    ?>
                </td>
            </tr>
            </table>
                <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                    <p style="margin: 0; padding: 0">
                        <strong>AVISO LEGAL</strong>: La fecha de iniciación del proyecto será aquella en la cual se
                        habilite el presupuesto en la plataforma SAP., condición que se cumplirá una vez impreso y entregado en la
                        Unidad de Seguimiento y Control de la Vicerrectoría de Investigación el formato firmado por los investigadores.<br/><br/>

                        <strong>AVISO LEGAL</strong>: Una vez cumplido el 50% del término de ejecución del proyecto de
                        investigación, extensión, internacionalización, docencia, bienestar, laboratorio o gestión universitaria, el
                        INVESTIGADOR PRINCIPAL deberá presentar a la Vicerrectoría de Investigación un informe de avance. Cumplido el
                        término para la elaboración de la misma se presentará un informe final, así como también los productos de
                        investigación esperados. Una vez presentado el informe de avance, dentro de los cinco días hábiles siguientes
                        podrán presentarse las solicitudes de modificación al formato index. Los términos para la presentación de informe
                        parcial y modificación de formato índex de acuerdo son improrrogables. El término para la presentación de informe
                        final podrá prorrogarse por una sola oportunidad dentro de los términos que autorice la Vicerrectoría de Investigación,
                        previa presentación del formato diligenciado para tal fin dirigido a la Unidad de Seguimiento y Control.<br/><br/>

                        En el evento de presentar la prórroga relacionada en el parágrafo anterior, ésta deberá solicitarse por lo menos
                        quince días hábiles antes de la fecha de culminación prevista en el cronograma de actividades.
                    </p>
                </div>
            <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
                <tr class="nombre_tabla">
                    <td colspan="8">Resumen</td>
                </tr>
            </table>
            <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                <p <?php echo ($datos->resumen && !empty($datos->resumen)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                    <?php echo ($datos->resumen && !empty($datos->resumen)) ? $datos->resumen : vacio() ?>
                </p>
            </div>
            <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Lab' || $datos->id_aux_tipo_proyecto == 'Pro_Gra') { ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
                <tr class="nombre_tabla">
                    <td colspan="8">Justificación</td>
                </tr>
            </table>
            <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                <p <?php echo ($datos->justificacion && !empty($datos->justificacion)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                    <?php echo ($datos->justificacion && !empty($datos->justificacion)) ? $datos->justificacion : vacio() ?>
                </p>
            </div>
            <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Int' && $datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
                <tr><td style="height: 20px" colspan="8"></td></tr>
                <tr class="nombre_tabla">
                    <td colspan="8">Planteamiento del problema</td>
                </tr>
                </table>
                    <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                        <p <?php echo ($datos->planteamiento_problema && !empty($datos->planteamiento_problema)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                            <?php echo ($datos->planteamiento_problema && !empty($datos->planteamiento_problema)) ? $datos->planteamiento_problema : vacio() ?>
                        </p>
                    </div>
                <table class="report detalle_plan_des" style="margin: 0; padding: 0">
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <tr class="nombre_tabla">
                <td colspan="8">Objetivos del proyecto</td>
            </tr>
            <tr>
                <td colspan="8" class="ttitulo">Objetivo General</td>
            </tr>
            <?php
                if (empty($datos->objetivos)) {
                    echo vacio(2, 8);
                } else {
                    $temp = true;
                    $resp = "<tr><td colspan='8' style='text-align: justify'><p>";
                    foreach ($datos->objetivos as $objetivo) {
                        if ($objetivo['tipo_objetivo'] == 'General') {
                            $temp = false;
                            $resp .= $objetivo['descripcion'];
                        }
                    }
                    $resp .= "</p></td></tr>";
                    if ($temp) {
                        echo vacio(2, 8);
                    } else {
                        echo $resp;
                    }
                }
                echo "<tr><td style='height: 20px' colspan='8'></td></tr>";
            ?>
            <tr>
                <td colspan="8" class="ttitulo">Objetivos Específicos</td>
            </tr>
            <?php
                if (empty($datos->objetivos)) {
                    echo vacio(2, 8);
                } else {
                    $temp = true;
                    $resp = "<tr><td colspan='8' style='text-align: justify'><p><ul>";
                    foreach ($datos->objetivos as $objetivo) {
                        if ($objetivo['tipo_objetivo'] == 'Específico') {
                            $temp = false;
                            $resp .= "<li>" . $objetivo['descripcion'] . "</li>";
                        }
                    }
                    $resp .= "</ul></p></td></tr>";
                    if ($temp) {
                        echo vacio(2, 8);
                    } else {
                        echo $resp;
                    }
                }
                echo "<tr><td style='height: 20px' colspan='8'></td></tr>";
            ?>
            <tr class="nombre_tabla">
                <td colspan="8">Obligaciones y Compromisos</td>
            </tr>
            </table>
            <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                <p style="margin: 0; padding: 0">
                    <strong>Obligaciones y compromisos de los participantes</strong>. La INVESTIGADOR PRINCIPAL se compromete a:
                    a) Adelantar las actividades previstas en el plan general propuesto para el proyecto, orientados a garantizar el
                    cumplimiento de los objetivos previstos en el mismo; b) Dedicar el tiempo otorgado por la CORPORACIÓN
                    UNIVERSIDAD DE LA COSTA CUC para el desarrollo del proyecto de manera responsable y comprometida; c)
                    manejar de manera diligente y responsable los recursos que la CORPORACIÓN UNIVERSIDAD DE LA COSTA
                    CUC pondrá a su disposición para el desarrollo del proyecto; d) participar en las actividades que organice la
                    Vicerrectoría de Investigación, encaminadas a garantizar una adecuada articulación y seguimiento a las
                    propuestas de investigación y extensión que se adelanten en la Institución; e) presentar los informes periódicos
                    y los otros que le sean solicitados en las fechas establecidas; f) cumplir con los objetivos planteados en los
                    tiempos estipulados en el cronograma de actividades; g) presentar el documento final del proyecto en el plazo
                    establecido en la presente FORMATO ÍNDEX; h) poner a disposición de la CORPORACIÓN UNIVERSIDAD
                    DE LA COSTA CUC, los equipos que hayan sido adquiridos con cargo a los recursos del proyecto; i) garantizar
                    la originalidad de la obra que se comprometen a realizar, con excepción de las citas o transcripciones de otras
                    citas, así como garantizar que la invención es de su creación exclusiva; j) Así mismo se comprometen a que
                    durante la vigencia del presente proyecto, no prepararán, reproducirán, distribuirán o intervendrán en la
                    preparación, reproducción, distribución o desarrollo, de otro proyecto por sí mismos o por medio de terceros, si
                    por su forma y contenido pudiese competir o perjudicar la explotación económica del resultado del proyecto
                    objeto de la presente FORMATO ÍNDEX, a menos que obtenga autorización escrita de la CORPORACIÓN
                    UNIVERSIDAD DE LA COSTA CUC; k) Se comprometen a que por ninguna causa culminaran el proyecto
                    objeto de la presente formato índex en otra institución, ni a titulo personal, ni por medio de terceros, sin la
                    autorización expresa y por escrito de la CORPORACIÓN UNIVERSIDAD DE LA COSTA CUC, en el evento en
                    que el mismo quedara inconcluso, l) Se comprometen a registrar los productos obtenidos del presente proyecto
                    en el GrupLAC de la Plataforma de SCIENTI de MinCiencias a nombre de la CORPORACIÓN
                    UNIVERSIDAD DE LA COSTA CUC.<br/><br/>

                    <strong>Obligaciones y compromisos de la CORPORACIÓN UNIVERSIDAD DE LA COSTA CUC</strong>. Esta se
                    compromete a: a) Desembolsar los recursos de acuerdo con los gastos previstos en la presente FORMATO ÍNDEX; b)
                    Otorgar tiempos específicos para que los participantes puedan adelantar las actividades previstas en el
                    proyecto; c) Propiciar espacios para que los participantes puedan recibir capacitación técnica calificada para el
                    desarrollo del proyecto; d) Respetar los derechos morales de autor y /o efectuar el reconocimiento y mención
                    como creador de la invención objeto de patente o del registro.<br/><br/>

                    <strong>Cesión de Obligaciones</strong>. Los participantes del proyecto no podrán ceder total ni
                    parcialmente las obligaciones contraídas en la presente ACTA a un tercero, salvo previa autorización expresa
                    y escrita de la Vicerrectoría de Investigación de la CORPORACIÓN UNIVERSIDAD DE LA COSTA CUC
                </p>
            </div>
            <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Inv' || $datos->id_aux_tipo_proyecto == 'Pro_Gra' || $datos->id_aux_tipo_proyecto == 'Pro_Doc') { ?>
                <tr class="nombre_tabla">
                    <td colspan="8">Marco Teórico y Estado del Arte</td>
                </tr>
                <tr>
                    <td colspan="8" class="ttitulo">Marco Teórico</td>
                </tr>
                </table>
                    <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                        <p <?php echo ($datos->marco_teorico && !empty($datos->marco_teorico)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                            <?php echo ($datos->marco_teorico && !empty($datos->marco_teorico)) ? $datos->marco_teorico : vacio() ?>
                        </p>
                    </div>
                <table class="report detalle_plan_des" style="margin: 0; padding: 0">
                <tr>
                    <td colspan="8" class="ttitulo">Estado del Arte</td>
                </tr>
                </table>
                    <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                        <p style="margin: 0; padding: 0" <?php echo ($datos->estado_arte && !empty($datos->estado_arte)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                            <?php echo ($datos->estado_arte && !empty($datos->estado_arte)) ? $datos->estado_arte : vacio() ?>
                        </p>
                    </div>
                <table class="report detalle_plan_des" style="margin: 0; padding: 0">
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Int' && $datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
                <tr class="nombre_tabla">
                    <td colspan="8">Diseño Metodológico</td>
                </tr>
                </table>
                    <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                        <p style="margin: 0; padding: 0" <?php echo ($datos->diseno_metodologico && !empty($datos->diseno_metodologico)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                            <?php echo ($datos->diseno_metodologico && !empty($datos->diseno_metodologico)) ? $datos->diseno_metodologico : vacio() ?>
                        </p>
                    </div>
                <table class="report detalle_plan_des" style="margin: 0; padding: 0">
                <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
            <tr class="nombre_tabla">
                <td colspan="8">Resultados Esperados</td>
            </tr>
            </table>
                <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                    <p style="margin: 0; padding: 0" <?php echo ($datos->resultados_esperados && !empty($datos->resultados_esperados)) ? "style='text-align: justify; margin: 0; padding: 0;'" : "style='text-align: center; margin: 0; padding: 0;'" ?>>
                        <?php echo ($datos->resultados_esperados && !empty($datos->resultados_esperados)) ? $datos->resultados_esperados : vacio() ?>
                    </p>
                </div>
            <table class="report detalle_plan_des" style="margin: 0; padding: 0">
            <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <tr class="nombre_tabla">
                <td colspan="8">Impactos y/o Efectos Esperados</td>
            </tr>
            <?php
                foreach ($datos->impactos as $impacto) {
                    $contenido = '';
                    foreach ($datos->impactos_digitados as $impacto_digitado) {
                        if ($impacto_digitado['id_tipo_impacto'] == $impacto['id']) {
                            $contenido = $impacto_digitado['descripcion'];
                        }
                    }
                    echo "
                    <tr>
                        <td colspan='8' class='ttitulo'>" . $impacto['valor'] . "</td>
                    </tr>
                    <tr>
                        <td colspan='8' " . ((!empty($contenido)) ? "style='text-align: justify'" : "style='text-align: center'") . ">
                            <p>
                                " . ((!empty($contenido)) ? $contenido : vacio()) . "
                            </p>
                        </td>
                    </tr>
                    ";
                }
            ?>
            <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
            <tr class="nombre_tabla">
                <td colspan="8">Productos Esperados</td>
            </tr>
            <tr>
                <td colspan="2" class="ttitulo">Prodcuto</td>
                <td colspan="3" class="ttitulo">Autores</td>
                <td colspan="3" class="ttitulo">Observaciones</td>
            </tr>
            <?php
                if (empty($datos->productos)) {
                    echo vacio(2, 8);
                } else {
                    foreach ($datos->productos as $producto) {
                        $participantes = [];
                        foreach ($producto['participantes'] as $participante) {
                            array_push($participantes, $participante['nombre_completo']);
                        }
                        echo "<tr>
                                <td colspan='2'>" . $producto['producto'] . "</td>
                                <td colspan='3'> " . implode(', ', $participantes) . " </td>
                                <td colspan='3'> " . $producto['observaciones'] . " </td>
                             </tr>";
                    }
                }
            ?>
            <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <?php if ($datos->id_aux_tipo_proyecto != 'Pro_Lab') { ?>
            <tr class="nombre_tabla">
                <td colspan="8">Cronograma</td>
            </tr>
            <tr>
                <td colspan="3" class="ttitulo">Actividad</td>
                <td colspan="2" class="ttitulo">Fecha Inicial / Fecha Final</td>
                <td colspan="3" class="ttitulo">Investigadores</td>
            </tr>
            <?php
                if (empty($datos->cronogramas)) {
                    echo vacio(2, 8);
                } else {
                    foreach ($datos->cronogramas as $cronograma) {
                        $participantes = [];
                        foreach ($cronograma['participantes'] as $participante) {
                            array_push($participantes, $participante['nombre_completo']);
                        }
                        echo "<tr>
                                <td colspan='3'>" . $cronograma['actividad'] . "</td>
                                <td colspan='2'> " . $cronograma['fecha_inicial'] . ' / ' . $cronograma['fecha_final'] . " </td>
                                <td colspan='3'> " . implode(', ', $participantes) . " </td>
                             </tr>";
                    }
                }
            ?>
            <tr><td style="height: 20px" colspan="8"></td></tr>
            <?php } ?>
            <tr class="nombre_tabla">
                <td colspan="8">Presupuesto</td>
            </tr>
            <tr>
                <td colspan="4" class="ttitulo">Tipo Recurso</td>
                <td colspan="4"><?= ($datos->nombre_tipo_recurso && !empty($datos->nombre_tipo_recurso)) ? $datos->nombre_tipo_recurso : vacio() ?></td>
            </tr>
        </table>

        <?php
            $informacion_imprimir = '';
            $total_efectivo_proyecto = 0;
            $total_especie_proyecto = 0;
            function imprimir_datos(/* $campos */$datos) {
                $result = '';
                foreach ($datos as $dato) {
                    $result .= "<td>" . ($dato['tipo_dato']== 'Select' ? $dato['valor_select'] : $dato['valor']) . "</td>";
                }
                // foreach ($campos as $campo) {
                //     foreach ($datos as $dato) {
                //         if ($campo['nombre_dato'] == $dato['nombre_dato']) {
                //             $result .= "<td>" . ($dato['tipo_dato']== 'Select' ? $dato['valor_select'] : $dato['valor']) . "</td>";
                //         }
                //     }
                // }
                return $result;
            }
            foreach ($datos->presupuestos as $presupuesto) {
                $thead = '';
                $count = count($presupuesto['campos']);
                foreach ($presupuesto['campos'] as $campo) {
                    $thead .= "<td class='ttitulo'> " . $campo['valor'] . "</td>";
                    // $thead .= "<td class='ttitulo'> " . $campo['nombre_dato'] . "</td>";
                }

                $tbody = '';
                $total_efectivo = 0;
                $total_especie = 0;
                if (empty($presupuesto['informacion'])) {
                    $tbody .= vacio('2', $count);
                } else {
                    foreach ($presupuesto['informacion'] as $informacion) {
                        if ($informacion['id_tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $informacion['valor_total'];
                        } else {
                            $total_especie += $informacion['valor_total'];
                        }
                        $tbody .= "<tr>" . imprimir_datos(/*$presupuesto['campos'],*/ $informacion['informacion_detallada']) ."</tr>";
                    }
                }
                
                $informacion_imprimir .= "
                    <table width='100%' class='report detalle_plan_des' style='margin: 0; padding: 0'>
                        <thead>
                            <tr class='nombre_tabla'>
                                <td colspan='$count'>" . $presupuesto['valor'] . "</td>
                            </tr>
                            <tr>
                                $thead
                            </tr>
                        </thead>
                        <tbody>
                            $tbody
                            <tr><td style='height: 20px' colspan='$count'></td></tr>
                        </tbody>
                    </table>
                    <table width='100%' class='report detalle_plan_des' style='margin: 0; padding: 0'>
                        <thead>
                            <tr>
                                <td class='ttitulo' style='width: 33.33%'>Total Efectivo</td>
                                <td class='ttitulo' style='width: 33.33%'>Total Especie</td>
                                <td class='ttitulo' style='width: 33.33%'>Total</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>$ " . number_format($total_efectivo, 2, ',', '.') . "</td>
                                <td>$ " . number_format($total_especie, 2, ',', '.') . "</td>
                                <td>$ " . number_format($total_efectivo + $total_especie, 2, ',', '.') . "</td>
                            </tr>
                            <tr><td style='height: 20px' colspan='3'></td></tr>
                        </tbody>
                    </table>
                ";
                $total_efectivo_proyecto += $total_efectivo;
                $total_especie_proyecto += $total_especie;
            }
            $total_presupuesto = $total_efectivo_proyecto + $total_especie_proyecto;
            $total_iva = ($total_presupuesto * $datos->iva) / 100;
            $total_con_iva = $total_presupuesto + $total_iva;
            $costo_por_beneficiario = $total_con_iva / $datos->no_beneficiarios;
            $tbody = '';
            if (!empty($datos->presupuesto_entidad)) {
                foreach ($datos->presupuesto_entidad as $presupuesto) {
                    $tbody .= "
                        <tr>
                            <td>" . $presupuesto['entidad_responsable'] . "</td>
                            <td>" . $presupuesto['efectivo'] . "</td>
                            <td>" . $presupuesto['especie'] . "</td>
                            <td>" . $presupuesto['total'] . "</td>
                            <td>" . $presupuesto['porcentaje'] . "</td>
                        </tr>
                    ";
                }
            } else {
                $tbody = vacio(2, 5);
            }
            $tbody2 = '';
            if (!empty($datos->presupuesto_entidad)) {
                foreach ($datos->presupuesto_entidad_rubro as $presupuesto) {
                    $tbody2 .= "
                        <tr>
                            <td>" . $presupuesto['entidad_responsable'] . "</td>
                            <td>" . $presupuesto['rubro'] . "</td>
                            <td>" . $presupuesto['efectivo'] . "</td>
                            <td>" . $presupuesto['especie'] . "</td>
                            <td>" . $presupuesto['total'] . "</td>
                        </tr>
                    ";
                }
            } else {
                $tbody2 = vacio(2, 5);
            }
            $informacion_imprimir .= "
                <table class='report detalle_plan_des' width='100%' style='margin: 0; padding: 0'>
                    <thead>
                        <tr class='nombre_tabla'>
                            <td colspan='5'>TABLA INVERSIÓN TOTAL DISCRIMINADO POR INSTITUCIÓN</td>
                        </tr>
                        <tr>
                            <td class='ttitulo' title='Nombre Institución'>Nombre Institución</td>
                            <td class='ttitulo' title='Total Efectivo'>Total Efectivo</td>
                            <td class='ttitulo' title='Total Especie'>Total Especie</td>
                            <td class='ttitulo' title='Total'>Total</td>
                            <td class='ttitulo' title='Porcentaje'>Porcentaje (100%)</td>
                        </tr>
                    </thead>
                    <tbody>
                        $tbody
                        <tr><td style='height: 20px' colspan='5'></td></tr>
                    </tbody>
                 </table>
                 <table class='report detalle_plan_des' width='100%' style='margin: 0; padding: 0'>
                    <thead>
                        <tr class='nombre_tabla'>
                            <td colspan='5'>TABLA INVERSIÓN TOTAL DISCRIMINADO POR INSTITUCIÓN Y RUBRO</td>
                        </tr>
                        <tr>
                            <td class='ttitulo' title='Nombre Institución'>Nombre Institución</td>
                            <td class='ttitulo' title='Rubro'>Rubro</td>
                            <td class='ttitulo' title='Total Efectivo'>Total Efectivo</td>
                            <td class='ttitulo' title='Total Especie'>Total Especie</td>
                            <td class='ttitulo' title='Total'>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        $tbody2
                        <tr><td style='height: 20px' colspan='5'></td></tr>
                    </tbody>
                </table>
                <table width='100%' class='report detalle_plan_des' style='margin: 0; padding: 0'>
                    <thead>
                        <tr class='nombre_tabla'>
                            <td colspan='3'>
                                INVERSIÓN TOTAL DEL PROYECTO DE " . strtoupper($datos->nombre_tipo_proyecto) . "
                            </td>
                        </tr>
                        <tr>
                            <td class='ttitulo'>Total Efectivo</td>
                            <td class='ttitulo'>Total Especie</td>
                            <td class='ttitulo'>Total</td>";
                if ($datos->id_aux_tipo_proyecto == 'Pro_Lab') {
                    $informacion_imprimir .= "
                            <td class='ttitulo'>Total IVA(" . $datos->iva  . "%)</td>
                            <td class='ttitulo'>Total Con IVA</td>
                            <td class='ttitulo'>COSTO POR BENEFICIARIO</td>
                    ";
                }
                $informacion_imprimir .= "
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$ " . number_format($total_efectivo_proyecto, 2, ',', '.') . "</td>
                            <td>$ " . number_format($total_especie_proyecto, 2, ',', '.') . "</td>
                            <td>$ " . number_format($total_presupuesto, 2, ',', '.') . "</td>";
                if ($datos->id_aux_tipo_proyecto == 'Pro_Lab') {
                    $informacion_imprimir .= "
                            <td>$ " . number_format($total_iva, 2, ',', '.') . "</td>
                            <td>$ " . number_format($total_con_iva, 2, ',', '.') . "</td>
                            <td>$ " . number_format($costo_por_beneficiario, 2, ',', '.') . "</td>
                        </tr>
                        <tr><td style='height: 20px' colspan='6'></td></tr>
                    ";
                } else {
                    $informacion_imprimir .= "
                        </tr>
                        <tr><td style='height: 20px' colspan='3'></td></tr>
                    ";
                }
                $informacion_imprimir .= "
                    </tbody>
                </table>
            ";
            echo $informacion_imprimir;
        ?>

        <?php if ($datos->id_aux_tipo_proyecto == 'Pro_Inv' || $datos->id_aux_tipo_proyecto == 'Pro_Ext' ||
                  $datos->id_aux_tipo_proyecto == 'Pro_Gra' || $datos->id_aux_tipo_proyecto == 'Pro_Doc') { ?>
            <table class='report detalle_plan_des' style='margin: 0; padding: 0'>
                <tr class="nombre_tabla">
                    <td colspan="8">Bibliografía</td>
                </tr>
            </table>
            <div class="report detalle_plan_des" style="margin: 0; padding: 0 5px">
                <?php
                    if (empty($datos->bibliografias)) {
                        echo vacio();
                    } else {
                        $resp = "<ul>";
                        foreach ($datos->bibliografias as $bibliografia) {
                            $resp .= "<li>" . $bibliografia['bibliografia'] . "</li>";
                        }
                        $resp .= "</ul>";
                        echo $resp;
                    }
                ?>
            </div>
        <?php } ?>

        <div class="con_firmas">
            <div class="espacio">
              <hr class="firma_espacio">
              <h4 class="nombre_completo mensajes_des" style="margin: 0">EDUARDO CRISSIEN BORRERO</h4>
              <h4 class="mensajes_des" style="margin-top: 5px">Rector</h4>
            </div>
            <?php
                foreach ($datos->vicerrectores as $vicerrector) {
                    echo "
                        <div class='espacio'>
                          <hr class='firma_espacio'>
                          <h4 class='nombre_completo mensajes_des' style='margin: 0'>" . $vicerrector['nombre_completo'] . "</h4>
                          <h4 class='mensajes_des' style='margin-top: 5px'>" . ucwords(mb_strtolower($vicerrector['cargo'])) . "</h4>
                        </div>
                    ";
                }
            ?>
            <?php
                foreach ($datos->participantes as $participante) {
                    echo "
                        <div class='espacio'>
                          <hr class='firma_espacio'>
                          <h4 class='nombre_completo mensajes_des' style='margin: 0'>" . $participante['nombre_completo'] . "</h4>
                          <h4 class='mensajes_des' style='margin-top: 5px'>" . ucwords(mb_strtolower($participante['tipo_participante'])) . "</h4>
                        </div>
                    ";
                }
            ?>
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

<style>
    .detalle_plan_des tr td, .detalle_plan_des p, .detalle_plan_des {
        font-size: 11px !important;
    }

    .detalle_plan_des div {
        border-left: 4px solid #6e1f7c;
        border-top: 1px solid #DDDDDD;
        border-bottom: 1px solid #DDDDDD;
        font-size: 11px !important;
    }
</style>

<?php
$html = ob_get_clean();
ob_clean();

try {
    // Crea una instancia de la clase y le pasa el directorio temporal
    $mpdf = new Mpdf(['margin-top' => 30, 'mode' => 'utf-8', 'default_font' => 'cuc', 'default_font_size' => 11]);

    // Carga el CSS externo
    $stylesheet = file_get_contents($css);
    $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

    // Escribe el contenido HTML (Template + View):
    $mpdf->WriteHTML($html);

    // Obliga la descarga del PDF
    $mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}
