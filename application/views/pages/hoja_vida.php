<?php 
  $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Eval" || $_SESSION["perfil"] == "Per_Admin_Tal" || $persona_estado? true :false;
  // $administra = false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Talento CUC</title>
  <!-- Favicon-->
  <link href="<?php echo base_url(); ?>imagenes/logo_cuc2.png" type="image/png" rel="shortcut icon" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet"
    type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/stylesTalentoCuc.css">
</head>

<body id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg  text-uppercase fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img alt="Agil" src="<?php echo base_url(); ?>imagenes/a_agil.png" width='35' />
      </a>
      <a class="navbar-brand js-scroll-trigger" href="#page-top">Talento CUC</a>
      <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded"
        type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive"
        aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger"
              href="#contact">Contacto</a></li>
          <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger"
              href="#about">Sobre Mi</a></li>
      <?php if($administra && $entrenamiento){?> 
          <li class="nav-item mx-0 mx-lg-1 dropdown"><a class="nav-link dropdown-toggle py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             Planes</a>
             <div class="dropdown-menu" aria-labelledby="navbarDropdown">
               <a class="dropdown-item py-3 px-0 px-lg-3 rounded js-scroll-trigger font-weight-bold" href="#portfolio">Plan de Formación</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item py-3 px-0 px-lg-3 rounded js-scroll-trigger font-weight-bold" href="#entrenamiento">Plan de Entrenamiento</a>
              </div>
          </li> 
      <?php }else{ ?>
          <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger"
              href="#portfolio">Plan de Formación</a></li>
      <?php } ?>
          <li class="nav-item mx-0 mx-lg-1" id="btn_buscar_hv" style="cursor:pointer;"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger">
            <span class="fa fa-search"></span> Buscar</a></li>    
        </ul>
      </div>
    </div>
  </nav>
  <!-- Masthead-->
  <header class="masthead bg-primary text-white ">
    <div class="container d-flex align-items-center flex-column">
      <!-- Masthead Avatar Image-->
      <?php if($administra){?>
      <span id='avatarImage' class="btn text-white">Editar</span>
      <?php } ?>
      <img class="masthead-avatar mb-5" src="<?php echo base_url(); ?>imagenes_personas/<?php echo $foto; ?>" alt="" />
      <!-- Masthead Heading-->
      <h1 class="masthead-heading text-uppercase mb-0"><?php echo $plan['nombre_completo'] ?></h1>
      <!-- Icon Divider-->
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- Masthead Subheading-->
      <p class="masthead-subheading font-weight-light mb-0" id='cargo'>Coordinador de desarrollo - Profesional
        III</p>
      <br>
      <button class="btn btn-secondary" id='btn_detalle'>Detalles</button>
    </div>
  </header>
  <!-- Portfolio Section-->
    <!-- Contact Section-->
    <section class="page-section" id="contact">
    <div class="container">
      <!-- Contact Section Heading-->
      <h2 class="page-section-heading text-center text-uppercase  text-secondary mb-0">
        Contacto</h2>
      <!-- Icon Divider-->
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- Contact Section Form-->
      <div class="row ">
        <div class="col-lg-8 mx-auto">
          <div id='contacto' style='font-size: 20px;'></div>
          <?php if($administra){?>
          <button href="#" class="btn btn-primary btn-block" id='btn_info_contacto'>ACTUALIZAR</button>
          <?php }?>
        </div>
      </div>
    </div>
    </div>
  </section>

   <!-- About Section-->
   <section class="page-section bg-primary text-white mb-0" id="about">
    <div class="container">
      <!-- About Section Heading-->
      <h2 class="page-section-heading text-center text-uppercase  mb-0 ">
        SOBRE
        MI</h2>
      <!-- Icon Divider-->
      <div class="divider-custom divider-light ">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- About Section Content-->
      <div class="row ">
        <div class="col-lg-12 ml-auto">
          <div class="">
            <h6 class="divider-custom-icon"><i class="fa fa-bookmark"></i> Formación Académica</h6>
            <p>A continuación te contaré cómo ha sido mi formación académica en los últimos años:</p>
          </div>
        </div>
      </div>
      <br>
      <div class="row" id='formacion'></div>
      <br>
      <div class="row">
        <div class="col-lg-12 ml-auto">
          <div class="">
            <h6 class="divider-custom-icon"><i class="fa fa-bookmark"></i> Te puedo ayudar</h6>
            <p>A continuación te mostraré algunos temas los cuales son de mi interés y te puedo ayudar en alguno de
              ellos si lo deseas:</p>
          </div>
        </div>
      </div>
      <br>
      <div class="row" id='tab_observaciones'></div>
    </div>
  </section>

  <section class="page-section portfolio" id="portfolio">
    <div class="container">
      <!-- Portfolio Section Heading-->
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">PLAN DE FORMACIÓN Y RUTA DE TRABAJO</h2>
      <!-- Icon Divider-->
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>

      <div class="col-md-6 col-lg-4 mb-5">
        <div class="dropdown" style="margin-left:-7%">
          <button class="btn btn-primary dropdown-toggle btn-block" type="button" id="dropdownMenuButton"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Filtra aquí por periodo de formación
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <?php 
              foreach ($periodos_evaluados as $row) { 
                echo "<span class='dropdown-item' onclick='listar_plan_formacion(".$row['id'].")'><span lass='fa fa-list'></span> ".$row['periodo']."</span>";
              }  
            ?>
            <span class='dropdown-item' onclick='listar_plan_formacion()'><span lass='fa fa-list'></span> Todos</span>
          </div>
        </div>
      </div>
      <!-- Portfolio Grid Items-->
      <div class="row justify-content-center" id="conten_formacion">
        
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/formacion.png" alt="" />
            <div class="card-body" style="height:300px">
              <?php if($administra){?>
              <h6 class="card-title">Certificado Institucional</h6>              
              <p class="card-text"><?php if(!$plan_formacion) echo 'En este momento estamos generando su plan de formación. '; ?>Al finalizar usted podra generar un certificado en el cual se evidenciara su
                participación en el plan de formación.</p>
              <button class="btn btn-primary btn-block" id='descargar_certificado' <?php echo !$descarga ? "disabled" : "" ?>>Descargar</button>
              <?php }else{ ?>
              <h6 class="card-title">Certificado Institucional</h6>
              <p class="card-text">En estos momentos me encuentro trabajando en mi plan de formación para obtener mi
                certificado institucional, te invito ha iniciar con el tuyo.</p>
              <button class="btn btn-primary btn-block" onclick="window.open(`${Traer_Server()}index.php`)">Mi
                plan</button>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php if($administra){?>
        <?php foreach ($plan_formacion as $f) { ?>
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <img class="img-fluid"
              src="<?php echo base_url(); ?>imagenes/<?php echo $f["icono"] ?>"
              alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title"><?php echo $f["competencia"]?></h6>
              <p class="card-text">Usted debe cumplir un total de <b><?php echo $f["hora_formacion"]?></b> horas para
                aprobar
                esta competencia, hasta el momento usted lleva un total de
                <b><?php echo $f["tiempo"] ? $f["tiempo"] : 0?></b>
                horas.
              </p>
              <div class="dropdown" style='width : 100%'>
                <button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Acciones
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <span class="dropdown-item"
                    onclick="listar_soportes_hv(<?php echo $f['id_competencia']?>,  <?php echo $f['id_persona']?>)"><span
                      class='fa fa-list'></span> Ver Soportes</span>
                  <span class="dropdown-item"
                    onclick="guardar_soportes_hv(<?php echo $f['id_competencia']?>,<?php echo $f['id_persona']?>)"><span
                      class='fa fa-upload'></span> Agregar Soporte</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php } ?>
        <?php } ?>
      </div>  
        <!-- metas -->
      <div class="row justify-content-center">  
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <br>
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/objetivo.png" alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title text-center">Metas de Desempeño</h6>
                <p class="card-text"><?php echo 'Resultado: <strong>'.$promedio_metas.'%</strong> de cumplimiento' ?> </p>
                <button class="btn btn-secondary btn-block" type="button" id="btn_detalle_metas" aria-haspopup="true" aria-expanded="false">Detalles</button>
            </div>
          </div>
        </div>
        <!-- formacion esencial -->
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <br>
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/sublineas.png" alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title text-center">Formación Esencial</h6>
                <p class="card-text"><?php echo 'Resultado: <strong>'.$promedio_formacion .'%</strong> de Formación' ?> </p>
                <button class="btn btn-secondary btn-block" type="button" id="btn_detalle_formacion" aria-haspopup="true" aria-expanded="false">Detalles</button>
            </div>
          </div>
        </div>
        <!-- funciones -->
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <br>
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/funciones.png" alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title text-center">Desempeña las Funciones del Rol</h6>             
                <p class="alert alert-info card-text"><?php echo '<strong>'.$promedio_funciones.'%</strong>' ?> APRECIACIÓN DEL JEFE INMEDIATO</p>
            </div>
          </div>
        </div>
      </div>  

      </div>
  </section>

<?php if($administra && $entrenamiento){ ?>
  <section class="page-section entrenamiento" id="entrenamiento">
    <div class="container">
      <!-- Portfolio Section Heading-->
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">PLAN DE ENTRENAMIENTO</h2>
      <!-- Icon Divider-->
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- Portfolio Grid Items-->
      <div class="row justify-content-center">
        <?php foreach ($plan_entrenamiento as $f) { ?>
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/didactico.png" alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title"><?php echo $f["oferta"]?></h6>
              <?php if(!$f["asistencia"] || !$f["aprobacion"]){ 
                  echo '<p class="card-text">Usted debe cumplir un total de <b>'.$f['duracion'].'</b> horas para aprobar esta oferta de entrenamiento.</p>';
              }else if($f["aprobacion"]){
                  echo '<p class="card-text">Usted cumplió satisfactoriamente con la oferta de entrenamiento, con un total de <b>'.$f['duracion'].'</b> horas.</p>';
              }?>
              <div class="dropdown" style='width : 100%'>
                <button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Acciones
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id='<?php echo $f['id']?>'>
                <?php if($f["asistencia"] == 0){ ?>
                    <span class="dropdown-item" id="oferta<?php echo $f['id']?>" onclick="marcar_asistencia_entrenamiento(<?php echo $f['id']?>,<?php echo $f['id_evaluado']?>)"><span
                      class='fa fa-check'></span> Marcar Asistencia</span>
                <?php }else{ ?>
                    <span class="dropdown-item"><span class='fa fa-toggle-off'></span> Sin acciones</span>
                <?php } ?>   
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
    </div>
  </section> 
  <?php } ?>
  <!-- <section class="footer text-center page-section" id="ayudar">
    <div class="container">

      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Te puedo ayudar</h2>

      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
    </div>
  </section> -->
  <!-- Footer-->
  <!-- Copyright Section-->
  <div class="copyright py-4 text-center text-white">
    <div class="container"><small>Copyright © <?php echo date("Y")?> Universidad de la Costa CUC</small></div>
  </div>
  <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
  <div class="scroll-to-top d-lg-none position-fixed">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i
        class="fa fa-chevron-up"></i></a>
  </div>


  <div class="portfolio-modal modal fade" id="encuesta_entrenamiento" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">ENCUESTA
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Text-->
                <form id="form_asistencia_entrenamiento" method="post">
                  <div class="row">
                    <div class="text-left">
                      <p><ul><li>Del 1 al 5 cual seria la puntuación de su Inducción / Entrenamiento al cargo: <span id="nivel" class="font-weight-bold"></span></li></ul></p>	
                      <p class="px-lg-5 clasificacion">
                        <input id="radio1" type="radio" class="radio_nivel" name="calificacion" value="5" onclick="marcar_califiacacion()">
                        <label class="label_nivel" for="radio1">&#9733;</label>
                        <input id="radio2" type="radio" class="radio_nivel" name="calificacion" value="4" onclick="marcar_califiacacion()">
                        <label class="label_nivel" for="radio2">&#9733;</label>
                        <input id="radio3" type="radio" class="radio_nivel" name="calificacion" value="3" onclick="marcar_califiacacion()">
                        <label class="label_nivel" for="radio3">&#9733;</label>
                        <input id="radio4" type="radio" class="radio_nivel" name="calificacion" value="2" onclick="marcar_califiacacion()">
                        <label class="label_nivel" for="radio4">&#9733;</label>
                        <input id="radio5" type="radio" class="radio_nivel" name="calificacion" value="1" onclick="marcar_califiacacion()">
                        <label class="label_nivel" for="radio5">&#9733;</label>
                      </p>
                      <p><ul><li>Sugerencias (Si su puntuación fue por debajo de 3, cuentanos aquí tus comentarios.)</li></ul></p>                      
                      <textarea name="sugerencias" class="form-control" rows="4" placeholder="Sugerencias"></textarea>
                      <button class="btn btn-primary btn-block mt-3">Aceptar</button>
                    </div>
                  </div>
                </form>  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_buscar_persona" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">BUSCAR HOJAS DE VIDA
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Text-->
                <form id="form_buscar_persona_hv" method="post">

                  <div class="input-group agrupado">
                      <input type="text" id="txt_dato_buscar" class="form-control" placeholder='Ingrese identificación o nombre'>
                        <label class="input-group-btn">
                         <button class="btn btn-primary" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                      </label>
                    </div>

                  <table class="table  table-hover table-condensed " id="tabla_busqueda_hv" cellspacing="0"
                    width="100%">
                    <thead>
                      <tr>
                        <th>NOMBRE COMPLETO</th>
                        <th>IDENTIFICACIÓN</th>
                        <th>ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </form>  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_funciones_cargo" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">DETALLES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Image-->
                  <div class="row">
                    <p class="masthead-subheading text-justify"><b>Propósito del Cargo</b>: <?php echo $plan['proposito'] ?></p>
                    <p class="masthead-subheading text-justify"><b>Código del Cargo</b>: <?php echo $plan['codigo_cargo'] ?></p>
                    <div class="divider-custom">
                      <div class="divider-custom-icon"><i class="fas fa-hand-point-right "></i></div>
                        <a href="https://app.powerbi.com/view?r=eyJrIjoiYjY4OGI5M2QtNGVmZC00NTEzLWFjMzgtZTRiMmYxOWY5ZWE3IiwidCI6IjA1MDdlNWNlLTBmOTUtNDlhYS1hYmRlLWM5MGRjZGVkYmQxMiIsImMiOjR9&pageName=ReportSection" 
                        class="text-secondary " target="blan_">Haga clic aquí para consultar las Funciones por Cargo!.</a>                      
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_soportes_plan_formacion" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Text-->
                <table class="table  table-hover table-condensed " id="tabla_soportes_plan_formacion" cellspacing="0"
                  width="100%">
                  <thead>
                    <tr>
                      <td>Nombre</td>
                      <td>Horas</td>
                      <td>Estado</td>
                      <td>Acción</td>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_soporte_capacitaciones" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Image-->
                <form id="from_soporte_capacitaciones" enctype="multipart/form-data">
                  <div class="row">
                    <div id="campo_soporte" class="input-group agrupado">
                      <label class="input-group-btn">
                        <span class="btn btn-default">
                          <span class="fa fa-folder-open"></span> Buscar
                          <input name="soporte_plan_for" type="file" style="display: none;">
                        </span>
                      </label>
                      <input type="text" id="soporte_plan_for" class="form-control" readonly
                        placeholder='Adjuntar documento'>
                    </div>
                    <input type="text" name="nombre_formacion" class="form-control mt-3"
                      placeholder="Nombre de la formación" required>
                    <input type="number" name="horas_formacion" class="form-control mt-3"
                      placeholder="Horas de capacitación" required>
                    <input type="date" name="fecha_formacion" class="form-control mt-3" placeholder="Fecha Formación"
                      required>
                    <input type="text" name="link_soporte" class="form-control mt-3" placeholder="Link del soporte">
                  </div>
                  <button id="cargar_soporte_capacitaciones" class="btn btn-primary btn-block mt-3">Enviar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_observaciones" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">Te puedo
                  ayudar</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_observacion" method="post">
                  <div class="row">
                    <textarea name="observacion" required class="form-control mt-3"
                      placeholder='Observación'></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary btn-block mt-3"> Guardar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_guardar_formacion_academica" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">NUEVA
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_formacion_academica" enctype="multipart/form-data" method="post">
                  <!-- Modal content-->
                  <div class="row">
                    <select name="id_formacion" required class="form-control mt-3 cbx_formacion">
                      <option value="">Seleccione Formación</option>
                    </select>
                    <input name="nombre" required class="form-control mt-3" type="text" placeholder='Nombre'>
                  </div>
                  <button type="submit" class="btn btn-primary btn-block mt-3"> Guardar</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_soportes_formacion_academica" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <table class="table  " id="tabla_soportes_academicos" cellspacing="0" width="100%">
                  <thead class="">
                    <tr class="filaprincipal">
                      <td>Nombre</td>
                      <!-- <td>Persona</td> -->
                      <td class="opciones_tbl">Acción</td>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_enviar_archivos" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="from_soporte_academico" enctype="multipart/form-data">
                  <div class="row">
                    <div id="campo_soporte" class="input-group agrupado">
                      <label class="input-group-btn">
                        <span class="btn btn-primary">
                          <span class="fa fa-folder-open"></span> Buscar
                          <input name="soporte_aca" type="file" style="display: none;">
                        </span>
                      </label>
                      <input type="text" id="soporte_aca" class="form-control" readonly
                        placeholder='Adjuntar documento' required>
                    </div>
                  </div>
                  <button id="cargar_adjuntos_general" class="btn btn-primary btn-block mt-3">Aceptar</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_cambiar_avatar" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label"> Foto
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="from_avatar" enctype="multipart/form-data">
                  <div class="row">
                    <div id="campo_soporte" class="input-group agrupado">
                      <label class="input-group-btn">
                        <span class="btn btn-default">
                          <span class="fa fa-folder-open"></span> Buscar
                          <input name="avatarInput" type="file" style="display: none;">
                        </span>
                      </label>
                      <input type="text" id="avatarInput" class="form-control" readonly placeholder='Seleccionar foto'
                        required>
                    </div>
                  </div>
                  <button class="btn btn-primary btn-block mt-3">ACTUALIZAR</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_soporte_capacitaciones" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="from_soporte_capacitaciones" enctype="multipart/form-data">
                  <div class="row">
                    <div id="campo_soporte" class="input-group agrupado">
                      <label class="input-group-btn">
                        <span class="btn btn-primary">
                          <span class="fa fa-folder-open"></span> Buscar
                          <input name="soporte_plan_for" type="file" style="display: none;">
                        </span>
                      </label>
                      <input type="text" id="soporte_plan_for" class="form-control" readonly
                        placeholder='Adjuntar documento' required>
                    </div>
                    <input type="text" name="nombre_formacion" class="form-control mt-3"
                      placeholder="Nombre de la formación" required>
                    <input type="number" name="horas_formacion" class="form-control mt-3"
                      placeholder="Horas de capacitación" required>
                    <div class="input-group agrupado date form_datetime agro" data-date=""
                      data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                      <label for="fecha_formacion"></label>
                      <input class="form-control mt-3 sin_focus pointer" size="16" placeholder="Fecha Formación"
                        type="text" value="" required name="fecha_formacion">
                      <span class="input-group-addon pointer"><span
                          class="glyphicon glyphicon-remove red"></span></span>
                      <span class="input-group-addon pointer"><span
                          class="glyphicon glyphicon-calendar red"></span></span>
                    </div>
                  </div>
                  <button id="cargar_soporte_capacitaciones" class="btn btn-primary btn-block mt-3">Aceptar</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_info_contacto" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label"> Contacto
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="from_info_contacto" enctype="multipart/form-data">
                  <div class="row">
                    <input type="text" name="info_lugar_residencia" class="form-control mt-3"
                      placeholder="Lugar de Residencia" required>
                    <input type="text" name="info_direccion" class="form-control mt-3" placeholder="Dirección" required>
                    <input type="text" name="info_oficina" class="form-control mt-3" placeholder="Oficina" required>
                    <input type="text" name="info_correo_personal" class="form-control mt-3"
                      placeholder="Correo Personal" required>
                    <input type="number" name="info_telefono" class="form-control mt-3" placeholder="Teléfono" required>
                  </div>
                  <button class="btn btn-primary btn-block mt-3">Aceptar</button>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_detalle_indicadores" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="nombre_modal">DETALLE</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Text-->
                  <table class="table  table-hover table-condensed " id="tabla_detalles" cellspacing="0" width="100%">
                    <thead></thead>
                    <tbody></tbody>
                  </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap core JS-->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Usuarios.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/talento_cuc.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Third party plugin JS--> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
  <!-- Contact form JS-->
  <!-- Core theme JS-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/scriptsTalentoCuc.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>
  <script>
  inactivityTime();
  activarfile();
  ver_info_hv(<?php echo json_encode($plan)?>,<?php echo json_encode($administra)?>)
  habilitarDescargueCertificado(<?php echo ($plan["id"])?>);
  Cargar_parametro_buscado(84, ".cbx_formacion", "Seleccione Formación");
  </script>
</body>

</html>