<?php
require_once(APPPATH . 'libraries/Mpdf/autoload.php');
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;

//$datos = $plan[0];
$fileName = "Formato Descripcion nivel";
$title = "Descripción nivel de capacitación";
$css = APP_PATH . '../../../js-css/estaticos/css/bootstrap.min.css';

ob_start();

?>

<html>
  <head>
    <title><?= $title ?></title>
  </head>
  <body style="margin: 0;">
    <?php
    $i = 0;
    foreach($niveles as $row){?>
      <div style="border: 3px solid #703144; border-radius: 8px; padding: 10px;">
        <div class="container">
        <table width="100%" class="table_footer">
          <tr>
            <td style="text-align: center; font-size:13; font-weight: bold;">Programa de Formación Academica Uniquest / <?php echo $row['nombre'] ?><p style="display: block;">★ Nivel <?php echo $row['nivel'] ?> ★</p></td>
          </tr>
        </table>
        <br>
        <br>
        <div style="text-align:center; height: 30px; width: 100%; background-color: #703144; border-radius: 50% 50% 50% 50%;">
        <div style="height: 90px; width: 8%; background-color: #e8c34c; border-radius: 50% 50% 50% 50% / 80% 80%; float:left;">
          </div>
          <div style="width: 92%; float:left;">
            <p style="text-align: center; color: #ffffff; font-size: 33px; font-family: times; width: 90%; margin: 0">PROGRAMA DE FORMACIÓN ACADEMICA UNIQUEST</p>
          </div>
        </div>
        <br>
        <h1 style="text-align: left; font-size: 28px; text-transform: uppercase;"><?php echo $row['nombre'] ?></h1>
        <?php if($row['tipo'] == 'Id_Mc_E'){?>
          <p style="text-align: justify; font-size: 15px;">Taller enfocado en el modelado de la imagen profesional, académica e investigativa y en el desarrollo de habilidades y destrezas necesarias para consolidar la identidad digital como investigador. En el taller se presentarán los diferentes perfiles digitales disponibles para la creación y consolidación de una identidad científica en el mundo digital: ORCID, Google Scholar, Researcher ID, Scopus Author ID. Se manejaran herramientas como Scopus - Web of Science - JCR -SJR</p>
          <p style="text-align: justify; font-size: 15px;">Al finalizar este taller los estudiantes, semilleros de investigación, docentes y administrativos estarán en capacidad de usar las herramientas de identificación especializadas para consolidar su perfil académico/científico en el entorno digital.</p>
          <h2 style="text-align: left;">Esto significa que podrán: </h2>
          <ul>
            <li style="font-size: 15px;">Normalizar su nombre y su afiliación institucional</li>
            <li style="font-size: 15px;">Crear y/o actualizar su perfil ORCID</li>
            <li style="font-size: 15px;">Crear y/o actualizar su perfil Google Scholar</li>
            <li style="font-size: 15px;">Crear y/o actualizar su perfil Researcher ID</li>
            <li style="font-size: 15px;">Crear y/o actualizar su perfil Scopus Author ID</li>
            <li style="font-size: 15px;">Conectar sus perfiles Researcher ID y Scopus Author ID</li>
            <li style="font-size: 15px;">Conocer el indice H de autores y universidades</li>
            <li style="font-size: 15px;">Conocer el factor de impacto de una revista</li>
          </ul>
        <?php }else if($row['tipo'] == 'Gb_A'){?>
          <p style="text-align: justify; font-size: 15px;">Taller enfocado en la apropiación de conocimientos y uso de la Información a través del gestor bibliográfico Mendeley y Booklick web. Mostrar su utilidad en las tareas académicas e investigadoras, así como proporcionar los conocimientos básicos para poder aprovechar sus ventajas en el manejo de bibliografías.</p>
          <p style="text-align: justify; font-size: 15px;">Al finalizar este taller los estudiantes, semilleros de investigación, docentes y administrativos estarán en la capacidad de crear, mantener, organizar y dar forma a referencias bibliográficas obtenidas de diferentes fuentes.</p>
          <h3 style="text-align: left;">Esto significa que podrán: </h3>
          <ul>
            <li style="font-size: 15px;">Editar perfil</li>
            <li style="font-size: 15px;">Área de conocimiento</li>
            <li style="font-size: 15px;">Crear RED de investigadores</li>
            <li style="font-size: 15px;">Subir sus documentos</li>
            <li style="font-size: 15px;">Crear Booklick</li>
            <li style="font-size: 15px;">Descargar documentos</li>
            <li style="font-size: 15px;">Crear sus cuenta de usuario en Mendeley</li>
            <li style="font-size: 15px;">Buscar referencias bibliográficas (Booklick)</li>
            <li style="font-size: 15px;">Indexar usuarios al grupo Universidad de la Costa</li>
            <li style="font-size: 15px;">Crear directorios de carpeta</li>
            <li style="font-size: 15px;">Utilizar diferentes estilos citación</li>
            <li style="font-size: 15px;">Extraer referencias bibliográficas a word</li>
          </ul>
        <?php }else if($row['tipo'] == 'Int_DB_I'){?>
          <p style="text-align: justify; font-size: 15px;">Taller enfocado en el uso de las Bases de Datos, librerías electrónicas, repositorio institucional y Booklick Apps.</p>
          <p style="text-align: justify; font-size: 15px;">Al finalizar este taller los estudiantes, semilleros de investigación, docentes y administrativos estarán en capacidad consultar información científica en los diferentes recursos electrónicos, consultar las diferentes investigaciones internas y conocer la importancia de la herramienta Booklick a nivel investigativo.</p>
          <h3 style="text-align: left;">Esto significa que podrán: </h3>
          <ul>
            <li style="font-size: 15px;">Crear su perfil en bases de datos referenciales</li>
            <li style="font-size: 15px;">Crear alertas para la identificación de material científico</li>
            <li style="font-size: 15px;">Realizar los diferentes tipos de búsqueda (completa)</li>
            <li style="font-size: 15px;">Realizar filtros de información y ordenamientos</li>
            <li style="font-size: 15px;">Analizar los resultados de búsqueda</li>
            <li style="font-size: 15px;">Conocer la producción científica de alto impacto (institución o país)</li>
            <li style="font-size: 15px;">Conocer el indice H de autores</li>
            <li style="font-size: 15px;">Descargar documentos</li>
            <p style="font-weight: bold; font-size: 12px;">Booklick</p>
            <li style="font-size: 15px;">Editar su perfil - Área de conocimiento - Crear RED de investigadores - Cargar documentos - Crear Booklick</li>
          </ul>
        <?php }else if($row['tipo'] == 'Int_DB_B'){?>
          <p style="text-align: justify; font-size: 15px;">Taller enfocado en el manejo e introducción a las Bases de Datos, repositorio institucional y Booklick Apps.</p>
          <p style="text-align: justify; font-size: 15px;">Al finalizar este taller los estudiantes, investigadores o docentes estarán en capacidad consultar información científica en diferentes recursos electrónicos, conocer la importancia de la herramienta Booklick a nivel investigativo.</p>
          <h3 style="text-align: left;">Esto significa que podrán: </h3>
          <ul>
            <li style="font-size: 15px;">Realizar los diferentes tipos de búsqueda (Básica y avanzada)</li>
            <li style="font-size: 15px;">Realizar filtros de información</li>
            <li style="font-size: 15px;">Realizar ordenamientos de información</li>
            <li style="font-size: 15px;">Descargar documentos</li>
            <p style="font-weight: bold; font-size: 12px;">Booklick</p>
            <li style="font-size: 15px;">Editar su perfil</li>
            <li style="font-size: 15px;">Área de conocimiento</li>
            <li style="font-size: 15px;">Crear RED de investigadores</li>
            <li style="font-size: 15px;">Cargar documentos</li>
          </ul>
        <?php }?>

        <h3 style="text-align: justify; font-size: 15px;">NOTA:</h3><p style="text-align: justify; display: inline; font-size: 15px;">Este taller tiene una duración de <?php echo $row['duracion'] ?> minutos, cuenta con una encuesta de satisfacción y un examen de conocimiento que se realizan al final.</p>
        <div class="row">
          <div style="float: left; width: 35%;">
            <p style="text-align: left; font-size:10;"><span style="font-weight: bold;">Date:</span> <?php echo $row['fecha'] ?></p>
            <p style="text-align: left; font-size:10;"><span style="font-weight: bold;">Time:</span> <?php echo $row['hora_inicio'] ?> - <?php echo $row['hora_fin']?></p>
            <p style="text-align: left; font-size:10;"><span style="font-weight: bold;">Location:</span> <?php echo $row['bloque'] ?> <?php echo $row['salon'] ?></p>
            <p style="text-align: left; font-size:10;"><span style="font-weight: bold;">Campus:</span> Universidad de la Costa</p>
            <p style="text-align: left; font-size:10;"><span style="font-weight: bold;">Audience:</span>: Estudiantes Pregrado</p>
          </div>
          <div style="display: inline; width: 65%;">
            <img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width="200" height="200">
          </div>
        </div>
        </div>
      </div>
      <?php $i++; ?> 
      <?php if($i <= count($niveles) - 1){?>
        <pagebreak>
      <?php }?>
    <?php }?>
  </body>
</html>
<?php
$html = ob_get_clean();
ob_clean();

$mpdf = new Mpdf(['margin-header' => 0, 'margin-footer' => 0, 'margin-top' => 0, 'margin-bottom' => 0, 'margin_right' => 3, 'margin_left' => 3, 'default_font' => 'cuc', 'default_font_size' => 9,]);
//$mpdf->showImageErrors = true;

$stylesheet = file_get_contents($css);
$mpdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);

$mpdf->WriteHTML($html);

$mpdf->Output("$fileName.pdf", Destination::DOWNLOAD);
