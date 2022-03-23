<?php

// Require composer autoload
include('application/libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Certificado Laboral";
$title = "Certificado Laboral";
// $css_boot = 'js-css\estaticos\css\bootstrap.min.css';
// $css = 'js-css\genericos\css\MyStyle.css';

//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
    <head>
		<title><?= $title ?></title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<style>
            .introduccion{
                width: 100%;
                height: 300px;
                text-align: center;
            }
            .introduccion__img{
                width: 100px;
                padding-bottom: 70px;
            }
			.introduccion__text{
				font-size: 14px;
			}
			.introduccion__text p{
				margin: 0px;
			}
			.introduccion__title{
				padding-top: 50px;
			}
			.cuerpo{
				width: 90%;
				margin: 0 auto;
				font-size: 14px;
				text-align: justify;
			}
			.firma{
				width: 85%;
				margin: 0 auto;
				padding-top: 80px;
			}
			.firma p{
				margin: 0px;
			}
			.firma__img{
				padding: -15px -20px;
				margin: 0;
				width: 250px;
			}
			.firma__nombre{
				font-size: 16px;
				font-weight: bold;
			}
			.firma__cargo{
				font-size: 14px;
			}
		</style>
    </head>
    <body>
        <htmlpageheader name="myHeader1">
        <table width="100%" class="table_footer">
                <tr>
                    <td width="50%" style="text-align: left;  font-size: 10px;">{DATE d/m/Y}</td>
                    <td width="50%" style="text-align: left;  font-size: 10px;">AGIL</td>
                </tr>
            </table>
        </htmlpageheader>

        <htmlpagebody>
            <section class="introduccion">
				<img class="introduccion__img" src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="Logo CUC">
				<div class="introduccion__text">
					<p>LA SUSCRITA DIRECTORA DE TALENTO HUMANO</p>
					<p>DE LA CORPORACIÓN UNIVERSIDAD DE LA COSTA</p>
					<p>NIT. 890104530-9</p>
				</div>
                <h3 class="introduccion__title">CERTIFICA:</h3>
			</section>

			<section class="cuerpo">
				<p>Que el señor <strong><?php echo $nombre; ?></strong>, identificado con cedula de ciudadania No. <?php echo $identificacion; if($lugar_expedicion) echo " expedida en " . ucwords(strtolower($lugar_expedicion));?>, se encuentra vinculado a nuestra Institución en el cargo de <?php echo $cargo;?>, mediante contrato individual de trabajo a Término <?php echo $tipo_contrato; ?>, desde el <?php echo $fecha_inicio_contrato; ?>.</p>
				<p>Devenga un salario fijo mensual de <?php echo $salario; ?> pesos m/cte. ($<?php echo number_format($valor_salario); ?>).</p>
				<p>Se expide la presente en Barranquilla, a los <?php echo $fecha_hoy; ?>.</p>
				<p>Esta certificación tiene vigencia de 30 días calendarios y es válida única y exclusivamente después de confirmar los datos al correo electrónico anavas1@cuc.edu.co</p>
			</section>

			<section class="firma">
				<img class="firma__img" src="<?php echo base_url(); ?>archivos_adjuntos/talentohumano/firmath.png" alt="Logo CUC">
				<p class="firma__nombre">Adriana Vera Barbosa</p>
				<p class="firma__cargo">Director de Talento Humano</p>
			</section>
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

// Carga el CSS externo
// $stylesheet = file_get_contents($css_boot);
// $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


// $stylesheet = file_get_contents($css);
// $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

// Escribe el contenido HTML (Template + View):
$mpdf->WriteHTML($html);

// Obliga la descarga del PDF
// if($descargar){
	$mpdf->Output("$fileName.pdf", Destination::INLINE);
// }
$nombre_archivo = '1047348314';
$mpdf->Output("archivos_adjuntos/talentohumano/certificados/$nombre_archivo", \Mpdf\Output\Destination::FILE);
// echo  "<script type='text/javascript'>window.close();</script>";
?>
