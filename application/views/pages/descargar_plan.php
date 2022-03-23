<?php
$perfil = $_SESSION["perfil"] ;
$administra = $perfil == "Per_Admin"  || $perfil== "Per_Adm_plan" || $perfil == "Per_Csep" || $permiso ? true: false;
// Require composer autoload
require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

$fileName = "Plan - ".$datos['nombre_completo'] ;
$title = "Evaluación y Plan de Trabajo";
$css_boot = APP_PATH . '../../js-css/estaticos/css/bootstrap.min.css';
$css = APP_PATH . '../../js-css/genericos/css/MyStyle.css';
$ruta_firmas = base_url().'archivos_adjuntos/profesores/firmas/';
//Activa el almacenamiento en búfer de la salida
ob_start();

?>
<html>
    <head>
        <title><?= $title ?></title>
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
            <h4 class="title-header"><?php echo "EVALUACIÓN Y PLAN DE TRABAJO" ?></h4>
            <table class="report detalle_plan_des">
              <tr class="nombre_tabla">
                <td colspan='7'>Datos del profesor</td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre</td>
                <td class="nombre_completo" colspan='6'><?php echo $datos['nombre_completo'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">identificación</td>
                <td class="identificacion" colspan='6'><?php echo $datos['identificacion'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Dedicación</td>
                <td colspan='6' class="dedicacion"><?php echo $datos['dedicacion'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Escalafon</td>
                <td colspan='6' class="escalafon"><?php echo $datos['escalafon'] ?></td>
              </tr>
              <tr class="nombre_tabla formacion">
                <td colspan='7'>Formación</td>
              </tr>
              <?php 
                  if (empty($formacion)) {
                    echo "<tr class='tr_formacion'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  }else{
                    foreach ($formacion as $f) {
                      echo  "<tr class='tr_formacion'><td class='ttitulo'>".$f['formacion']."</td> <td colspan='6'>".$f['nombre']."</td></tr>";
                    }
                  }
                ?>
              <tr class="nombre_tabla">
                <td colspan='7'>Contrato</td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo</td>
                <td class="contrato" colspan='6'><?php echo $datos['contrato'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Inicio</td>
                <td colspan='3' class="fecha_inicio"><?php echo $datos['fecha_inicio'] ?></td>
                <td class="ttitulo">Fecha Fin</td>
                <td colspan='2' class="fecha_fin"><?php echo $datos['fecha_fin'] ?></td>
              </tr>
              <tr class="nombre_tabla">
                <td colspan='7'>Detalle</td>
              </tr>
              <tr>
                <td class="ttitulo">Departamento</td>
                <td colspan='6' class="departamento"><?php echo $datos['departamento'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Programa</td>
                <td colspan='6' class="programa"><?php echo $datos['programa'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Area de Conocimiento</td>
                <td class="area_conocimiento" colspan='6'><?php echo $datos['area_conocimiento'] ?></td>
              </tr>
              <tr>
                <td class="ttitulo">Grupo de Investigación</td>
                <td class="grupo" colspan='6'><?php echo $datos['grupo'] ?></td>
              </tr>
              <tr class="nombre_tabla perfiles">
                <td colspan='7'>Plan de Trabajo</td>
              </tr>
              <?php 
                  if (empty($perifles)) echo "<tr class='tr_perfiles'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    echo "<tr class=' tr_perfiles'><td colspan='2' class='ttitulo'>Perfil</td><td colspan='2' class='ttitulo'>Rol</td><td colspan='2' class='ttitulo'>Cobertura</td>";
                    foreach ($perifles as $f) echo  "<tr class='tr_perfiles'><td colspan='2'>".$f['perfil']."</td> <td colspan='2'>".$f['rol']."</td> <td colspan='2'>".$f['cobertura']."</td></tr>";
                  }
                  ?>
              <tr class="nombre_tabla horas_programas">
                <td colspan='7'>Horas por programa</td>
              </tr>
              <?php 
                  if (empty($horas)) echo "<tr class='tr_horas_programas'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    $total = 0;
                    echo "<tr class='tr_horas_programas'><td colspan='4' class='ttitulo'>Programa</td><td class='ttitulo'>Hora</td><td class='ttitulo'>Cantidad</td>";
                    foreach ($horas as $f){
                      $total = $total + $f["cantidad"];
                      echo  "<tr class='tr_horas_programas'><td colspan='4'>".$f["programa"]."</td> <td>".$f["hora"]."</td> <td>".$f["cantidad"]."</td></tr>";
                    } 
                    echo  "<tr class='tr_horas_programas'><td colspan='4'> Total</td> <td colspan='2'>".$total."</td></tr>";
                  }
                ?>
              <tr class="nombre_tabla asignaturas">
                <td colspan='7'>Asignaturas</td>
              </tr>
              <?php 
                  if (empty($asignaturas)) echo "<tr class='tr_asignaturas'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    echo  "<tr class='tr_asignaturas'><td class='ttitulo'>Nombre</td><td class='ttitulo'>Creditos</td><td class='ttitulo'>Grupo</td><td class='ttitulo'>Día</td><td class='ttitulo'>Horario</td><td class='ttitulo'>Salón</td></tr>";
                    foreach ($asignaturas as $f) echo  "<tr class='tr_asignaturas'><td>".$f["nombre"]."</td> <td>".$f["creditos"]."</td> <td>".$f["grupo"]."</td><td>".$f["dia"]."</td><td>".$f["horario"]."</td><td>".$f["salon"]."</td></tr>";                    
                  }
                ?>
              <tr class="nombre_tabla atencion">
                <td colspan='7'>Horario de atención</td>
              </tr>
              <?php 
                  if (empty($atencion))echo "<tr class='tr_atencion'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    echo  "<tr class='tr_atencion'><td class='ttitulo'>Día</td><td class='ttitulo'>Inicio</td><td class='ttitulo'>Fin</td><td colspan='4' class='ttitulo'>Lugar</td></tr>";
                    foreach ($atencion as $f) echo  "<tr class='tr_atencion'><td>".$f["nombre"]."</td> <td>".$f["hora_inicio"]."</td> <td>".$f["hora_fin"]."</td><td colspan='4'>".$f["lugar"]."</td></tr>";
                  }
                  ?>
              <tr class="nombre_tabla indicadores">
                <td colspan='7'>Indicadores</td>
              </tr>
              <?php 
                  if (empty($indicadores))echo "<tr class='tr_indicadores'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    echo  "<tr class='tr_indicadores'><td class='ttitulo'>Nombre</td><td class='ttitulo'>Fecha</td><td class='ttitulo'>Estado Inicial</td><td class='ttitulo'>Fecha Meta</td><td class='ttitulo'>Estado Meta</td><td class='ttitulo'>Estado Actual</td></tr>";
                    foreach ($indicadores as $f) echo  "<tr class='tr_indicadores'><td>".$f["nombre"]."</td> <td>".$f["fecha_inicial"]."</td> <td>".$f["estado_inicial"]."</td> <td>".$f["fecha_final"]."</td> <td>".$f["estado_final"]."</td> <td>".$f["estado_actual"]."</td></tr>";
                  }
                ?>
              <tr class="nombre_tabla lineas">
                <td colspan='7'>Lineas de Investigación</td>
              </tr>
              <?php 
                  if (empty($lineas))echo "<tr class='tr_lineas'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else{
                    echo  "<tr class='tr_lineas'><td class='ttitulo' colspan='3'>Linea</td><td class='ttitulo' colspan='4'>Sub Linea</td></tr>";
                    foreach ($lineas as $f) echo  "<tr class='tr_lineas'><td colspan='3'>".$f["linea"]."</td> <td colspan='4'>".$f["sub_linea"]."</td></tr>";
                  }
                ?>
              <tr class="nombre_tabla objetivos">
                <td colspan='7'>Observaciones Generales</td>
              </tr>
              <?php 
                  if (empty($objetivos)) echo "<tr  class='tr_objetivos'><td colspan='7'>Ningún dato disponible en esta sección</td></tr>";
                  else foreach ($objetivos as $f)   echo  "<tr class='tr_objetivos'><td colspan='7'>".$f["objetivo"]."</td></tr>";
                ?>
            </table>
            <div>
              <h3 class='mensajes_des'>Se recomienda implementar en su quehacer diario las siguientes políticas:</h3>  
              <ol class='politicas letra10'>
                <?php 
                  if (empty($politicas)) echo "<li>Ningún dato disponible en esta sección</li>";
                  else foreach ($politicas as $f)   echo  "<li>".$f["nombre"]."</li>";
                ?>
              </ol>
              <h3 class='mensajes_des'>Notas: </h3>  
              <ol class='notas letra10'>
                <?php 
                  if (empty($notas)) echo "<li>Ningún dato disponible en esta sección</li>";
                  else foreach ($notas as $f)   echo  "<li>".$f["nombre"]."</li>";
                ?>
              </ol>
            </div>
            <?php if($administra) { ?>
            <div class='con_firmas'>
                <h4 class='mensajes_des'>Fecha de generación: <?php print_r(date("Y-m-d H:i:s"))?></h4>
                <h4 class='mensajes_des'>Fecha de firma: 
                  <?php 
                    if($datos['fecha_firma']) print_r(date("Y-m-d",strtotime($datos['fecha_firma']))); 
                    else echo '______-____-____';
                  ?>
                </h4>
              </div>
            <div class='con_firmas'>
              <div class='espacio'>
                <?php if($datos['firma_profesor']) { ?>
                  <img class='img_firma' src="<?php echo $ruta_firmas.$datos['firma_profesor']?>">
                <?php } else { ?>
                  <hr class='firma_espacio'>
                <?php } ?>
                <h4 class='mensajes_des'>Profesor</h4>
                <h4 class='nombre_completo mensajes_des'><?php echo $datos['nombre_completo']?></h4>
              </div>
              <?php 
                foreach ($directores as $row) {
                  $contenido = "";
                  if($datos['firma_decano']) $contenido = "<img class='img_firma' src='".$ruta_firmas.$datos['firma_decano']."'>";
                  else $contenido = "<hr class='firma_espacio'>";
              
                  echo ("
                  <div class='espacio'>
                    ".$contenido."
                    <h4 class='mensajes_des'>Decano de Departamento</h4>
                    <h4 class='mensajes_des'>".$row['nombre_completo']."</h4>
                  </div>
                  ");
                }
                ?>
            </div>
              <?php } ?>
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
$stylesheet = file_get_contents($css_boo);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);


$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

// Escribe el contenido HTML (Template + View):
$mpdf->WriteHTML($html);

// Obliga la descarga del PDF
$mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);
