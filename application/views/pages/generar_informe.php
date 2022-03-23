<?php

// Require composer autoload
include('application/libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Informe de Selección";
$title = "Informe de Selección";
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
			.cabecera {
				font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
				border-collapse: collapse;
				width: 100%;
			}

			.cabecera td, .cabecera th {
				border: 1px solid #000000;
				padding: 8px;
			}

			.cabecera tr:nth-child(even){background-color: #f2f2f2;}
			.cabecera th {
				padding-top: 12px;
				padding-bottom: 12px;
				text-align: left;
			}

			.nombre_tabla{
				border: none !important;
				color: black;
				font-size: 13px;
				border-left: 4px solid #6e1f7c !important;
				margin-top: 11px;
				padding-top: 10px;
				font-weight: normal;
				text-transform: uppercase;
			}
			h3{
				text-transform: uppercase;
			}
			.sin-borde {
				border: none !important;
				border-left: 1px solid #cccccc !important;
			}
			.dato{
				font-weight: 500;
			}
			.no-border{
				border:none !important;
			}
		</style>
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
			<table id="datos" style="width: 100%;" cellspacing="0" cellpadding="5">
				<thead class="cabecera">
					<tr>
						<th style="width: 20%;text-align:center;"><img src="<?php echo base_url(); ?>imagenes/LogocucF.png" alt="" height=80 width=100></th>
						<th style="width: 60%;text-align:center;"><h2>INFORME FINAL SELECCIÓN PERSONAL</h2></th>
						<th style="width: 20%;text-align:center;">
							<p>VERSIÓN: 08</p><hr>
							<p>JUNIO 2019</p><hr>
							<p>CÓDIGO</p><hr>
							<p>TRD: 700-730-90</p>
						</th>
					</tr>
				</thead>
				<!-- <tbody> -->
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>DATOS IDENTIFICACIÓN</h2></td></tr>
								<tr>
									<td style="width:40%;"><h3>NOMBRE:</h3></td>
									<td style="width:60%;"><h3 class="dato"><?php echo $fullname?></h3></td>
								</tr>
								<tr>
									<td><h3>CÉDULA:</h3></td>
									<td><h3 class="dato"><?php echo $identificacion?></h3></td>
								</tr>
								<tr>
									<td><h3>FECHA DE NACIMIENTO:</h3></td>
									<td><h3 class="dato"><?php echo $fecha_nacimiento ?></h3></td>
								</tr>
								<tr>
									<td><h3>CORREO ELECTRÓNICO:</h3></td>
									<td><h3 class="dato"><?php echo strtoupper($correo)?></h3></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #000000;"><h3>CELULAR:</h3></td>
									<td style="border-bottom: 1px solid #000000;"><h3 class="dato"><?php echo $telefono?></h3></td>
								</tr>
								<tr>
									<td><h3>ÁREA/DEPENDENCIA:</h3></td>
									<td><h3 class="dato"><?php echo strtoupper($departamento) ?></h3></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #000000;"><h3>CARGO:</h3></td>
									<td style="border-bottom: 1px solid #000000;"><h3 class="dato"><?php echo strtoupper($cargo)?></h3></td>
								</tr>
								<tr>
									<td><h3>CATEGORÍA COLCIENCIAS:</h3></td>
									<td><h3 class="dato"><?php echo strtoupper($categoria_colciencias) ?></h3></td>
								</tr>
								<tr>
									<td><h3>ÍNDICE H:</h3></td>
									<td><h3 class="dato"><?php echo strtoupper($indiceh) ?></h3></td>
								</tr>
								<tr>
									<td><h3>CVLAC:</h3></td>
									<td><h3 class="dato"><?php echo strtoupper($cvlac) ?></h3></td>
								</tr>
							</table><br><br>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>FORMACIÓN</h2></td></tr>
								<?php if(sizeof($estudios) > 0){
									foreach ($estudios as $estudio) { ?>
										<tr>
											<td style="width:40%;">
												<h3>TÍTULO</h3>
												<h3>UNIVERSIDAD</h3>
												<h3>FECHA DE GRADO/CONVALIDADO</h3>
											</td>
											<td style="width:60%;">
												<h3><?php echo strtoupper($estudio['formacion']) ?></h3>
												<h3 style="font-weight: 400"><?php echo strtoupper($estudio['universidad']) ?></h3>
												<h3 style="font-weight: 400"><?php echo strtoupper($estudio['fecha_graduacion']) ?></h3>
											</td>
										</tr>
								<?php }}?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>EXPERIENCIA</h2></td></tr>
								<tr>
									<td style="width:40%;"><h3>TIEMPO EXPERIENCIA DOCENCIA:</h3></td>
									<td style="width:60%;"><P class="dato"><?php echo strtoupper($exp_docente)?></P></td>
								</tr>
								<tr>
									<td><h3>TIEMPO EXPERIENCIA INVESTIGACIÓN:</h3></td>
									<td><P class="dato"><?php echo strtoupper($exp_investigacion)?></P></td>
								</tr>
								<tr>
									<td><h3>TIEMPO EXPERIENCIA PROFESIONAL:</h3></td>
									<td><p class="dato"><?php echo strtoupper($exp_profesional)?></p></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>PRODUCCIÓN</h2></td></tr>
								<tr>
									<td style="width:40%;"><h3>PRODUCCIÓN CIENTÍFICA:</h3></td>
									<td><p class="dato"><?php echo strtoupper($produccion)?></p></td>
								</tr>
								<tr>
									<td style="width:40%;"><h3>PRODUCTOS DE FORMACIÓN:</h3></td>
									<td><p class="dato"><?php echo strtoupper($formacion)?></p></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>REQUISITOS</h2></td></tr>
								<tr>
									<td style="width:40%;"><h3>CERTIFICADO SUFICIENCIA INGLÉS:</h3></td>
									<td style="width:60%;"><h3 class="dato"><?php echo strtoupper($suficiencia_ingles) ?></h3></td>
								</tr>
							</table><br><br>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>PRUEBAS</h2></td></tr>
								<tr>
									<td style="width:40%;"></td>
									<td style="width:60%;"><P class="dato"><?php echo strtoupper($pruebas)?></P></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="5" style="width:100%">
								<tr rowspan="2"><td class="nombre_tabla"><h2>CONCEPTO</h2></td></tr>
								<tr>
									<td colspan="2"><h3>De acuerdo con el proceso evaluativo se puede concluir lo siguiente:</h3></td>
								</tr>
								<tr>
									<td colspan="2"><h3>Fortalezas identificadas:</h3></td>
								</tr>
								<?php if(sizeof($competencias) > 0){
									$n = 0;
									foreach ($competencias as $competencia) { 
										if($competencia['tipo'] == 1){ $n++; ?>
											<tr>
												<td colspan="2"><p class="dato"><?php echo $n.'. '.strtoupper($competencia['nombre']).': '.strtoupper($competencia['observaciones']) ?></p></td>
											</tr>
								<?php }}}?>
								<tr>
									<td colspan="2"><h3>Oportunidad de mejora identificadas:</h3></td>
								</tr>
								<?php if(sizeof($competencias) > 0){
									$n = 0;
									foreach ($competencias as $competencia) { 
										if($competencia['tipo'] == 0){ $n++; ?>
											<tr>
												<td colspan="2"><p class="dato"><?php echo $n.'. '.strtoupper($competencia['nombre']).': '.strtoupper($competencia['observaciones']) ?></p></td>
											</tr>
								<?php }}}?>
								<tr>
									<td colspan="2"><p class="dato"><?php echo strtoupper($concepto) ?></p></td>
								</tr>
							</table>
						</td>
					</tr>
				<!-- </tbody> -->
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
$mpdf = new Mpdf(['margin-top' => 30,'default_font' => 'cuc', 'default_font_size' => 9,]);

// Carga el CSS externo
// $stylesheet = file_get_contents($css_boot);
// $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


// $stylesheet = file_get_contents($css);
// $mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

// Escribe el contenido HTML (Template + View):
$mpdf->WriteHTML($html);

// Obliga la descarga del PDF
if($descargar){
	$mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);
}
$mpdf->Output("archivos_adjuntos/talentohumano/hojas_vidas/$nombre_archivo", \Mpdf\Output\Destination::FILE);
echo  "<script type='text/javascript'>window.close();</script>";
?>
