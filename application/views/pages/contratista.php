<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Contratos CUC</title>
  <!-- Favicon-->
  <link href="<?php echo base_url(); ?>imagenes/logo_cuc2.png" type="image/png" rel="shortcut icon" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/stylesContratos.css">
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    a,
    a:hover {
      color: #428bca;
      text-decoration: none;
    }

    body {
      font-family: 'Open Sans', Arial, Helvetica, Sans-serif;
    }

    #canvas {
      border: 1px solid #d67e1c;
      border-radius: 5px;
      width: 100%;
      max-width: 300px;
      background: #fff;
    }

    #header {
      background-size: auto 100%;
      color: #fff;
      background-color: #f4f4f4;
      padding: 10px;
    }

    #logo {
      margin: auto;
      width: 100%;
      max-width: max-content;
      max-width: -moz-max-content;
    }

    #logo img {
      width: 100%;
    }

    #slideshow {
      border-style: solid;
      border-top-color: #f4f4f4 !important;
      border-width: 30px 135px 0 135px;
      color: transparent;
      width: max-content;
      height: auto;
      margin: auto;
    }

    .panel-heading {
      color: #424242;
      font-weight: normal;
    }

    .panel-heading h1 {
      font-family: 'Open Sans', Arial, Helvetica, Sans-serif;
    }

    .btn-cuc {
      margin-top: 5px;
      background: #d67e1c;
      color: #ffff;
      outline: none !important;
      float: right;
    }

    .btn-cuc:hover,
    .btn-cuc:active,
    .btn-cuc:focus {
      color: #fff;
      background: #6e1f7c;
    }

    .fidel {
      width: 90%;
      border: 1px solid #aeaeae;
      border-radius: 5px;
      margin: auto;
      margin-bottom: 15px;
      margin-top: 10px;
    }

    .fidel>input {
      border: none;
      background: transparent;
      width: 100%;
      padding: 12px 9px;
      border-radius: 5px;
      outline: none;
      position: relative;
      margin-top: -9px;
      z-index: 99;
      font-size: 17px
    }

    .fidel>legend {
      width: auto;
      font-size: 14px;
      margin-top: -9px;
      margin-left: 9px;
      padding: 0 5px;
      margin-bottom: 0;
      position: relative;
      z-index: 100;
      border: none;
    }

    .btn-login {
      transition: 250ms;
      display: block;
      margin: auto;
      width: 90%;
      margin-bottom: 30px;
      background: #d67e1c;
      color: #ffff;
      outline: none !important;
      box-shadow: 0px 2.5px 4px #4f4f4f;
      border: 1px #d67e1c;
      padding: 9px;
      border-radius: 5px;
      position: relative;
      top: 0px;
    }

    .btn-login:hover {
      transition: 250ms;
      color: #fff;
      background: #6e1f7c;
      box-shadow: 0px 2.5px 4px #4f4f4f;
      border: 1px #6e1f7c;
    }

    .btn-login:active {
      transition: 250ms;
      box-shadow: inset 0px 0px 4px #4f4f4f;
      top: 4px;
    }

    .login-container {
      margin-top: 10%;
    }

    .pointer {
      cursor: pointer;
    }

    .purple {
      color: #6e1f7c;
    }
  </style>
</head>

<body id="page-top" style="background-image: url(<?= $session == false ? base_url() . '/imagenes/fondo.jpg' : false; ?>);background-size: cover;background-repeat: no-repeat;">
  <!-- Si no ha iniciado session -->
  <?php if ($session == false) : ?>
    <div class="login-container">
      <header id="header" style="background-color: transparent;">
        <div id="logo">
          <img class="logo" src="<?php echo base_url(); ?>/imagenes/agil_header.png" alt="Universidad de la Costa CUC">
        </div>
      </header>
      <!--p style="text-align: center;">¡Se ha firmado correctamente!</p-->
      <h2 style="text-align: center;">Iniciar Sesión</h2>
      <div style="background: transparent;border: none; max-width:450px;margin:auto;">
        <fieldset class="fidel">
          <legend>NIT/CC *</legend>
          <input type="user" placeholder="Usuario" id="user">
        </fieldset>
        <fieldset class="fidel">
          <legend>Contraseña *</legend>
          <input type="password" placeholder="Contraseña" id="pass">
        </fieldset>
        <input type="submit" class="btn-login" value="INGRESAR">
      </div>
      <p style="color:red; text-align: center;" id="login-error"></p>
    </div>
  <?php endif; ?>

  <!-- Si el contratista no tiene los datos requeridos o el link esta mal -->
  <?php if (isset($estado)) : ?>
    <header id="header">
      <div id="logo">
        <img class="logo" src="<?php echo base_url(); ?>/imagenes/agil_header.png" alt="Universidad de la Costa CUC">
      </div>
    </header>
    <div id="slideshow"></div>
    <div class="row" id="" style="width: 90%;margin: 30px auto;">
      <div class="panel panel-cuc">
        <div class="panel-body">
          <?= $mensaje; ?>
        </div>
      </div>
    </div>
    <?= die(); ?>
  <?php endif; ?>

  <!-- Si ha iniciado session -->
  <?php if ($session == true) : ?>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg  text-uppercase fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img alt="Agil" src="<?php echo base_url(); ?>imagenes/a_agil.png" width='35' />
        </a>
        <a class="navbar-brand js-scroll-trigger" href="#page-top">Contratos CUC</a>
        <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">Menu <i class="fas fa-bars"></i></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item mx-0 mx-lg-1">
              <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#contratos">Contratos</a>
            </li>
            <li class="nav-item mx-0 mx-lg-1" id="btn_buscar_hv" style="cursor:pointer;">
              <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" id="logout"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Salir</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Masthead-->
    <header class="masthead bg-primary text-white ">
      <div class="container d-flex align-items-center flex-column">
        <!-- Masthead Avatar Image-->
        <!--span id='avatarImage' class="btn text-white">Editar</span-->
        <img class="masthead-avatar mb-5" src="<?php echo base_url(); ?>imagenes_personas/empleado.png" alt="" />
        <!-- Masthead Heading-->
        <h1 class="masthead-heading text-uppercase mb-0 text-center"><?= $nombre; ?></h1>
        <!-- Icon Divider-->
        <div class="divider-custom divider-light">
          <div class="divider-custom-line"></div>
          <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
          <div class="divider-custom-line"></div>
        </div>
        <!-- Masthead Subheading-->
        <p class="masthead-subheading font-weight-light mb-0">NIT/CC <?= $identity ?></p>
        <p><?= $correo ?></p>
        <br>
        <!--button class="btn btn-secondary" id='btn_detalle'>Detalles</button-->
      </div>
    </header>

    <!-- Contratos Section-->
    <section class="page-section contratos" id="contratos">
      <div class="container">
        <!-- Contratos Section Heading-->
        <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">CONTRATOS</h2>
        <!-- Icon Divider-->
        <div class="divider-custom">
          <div class="divider-custom-line"></div>
          <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
          <div class="divider-custom-line"></div>
        </div>
      </div>
      <!-- Contratos Grid Items-->
      <div id="lista-contratos"></div>
    </section>
    <!-- Footer-->
    <!-- Copyright Section-->
    <div class="copyright py-4 text-center text-white">
      <div class="container"><small>Copyright © <?php echo date("Y") ?> Universidad de la Costa CUC</small></div>
    </div>
    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
    <div class="scroll-to-top d-lg-none position-fixed">
      <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i class="fa fa-chevron-up"></i></a>
    </div>

    <!--  modal detalles contrato -->
    <div class="contratos-modal modal fade" id="detalles_contra" tabindex="-1" role="dialog" aria-labelledby="contratosModal1Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- contratos Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal1Label">Detalles del contrato
                  </h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- contratos Modal - Text-->
                  <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav_admin_compras">
                    <div class="container">
                      <ul class="navbar-nav m-auto">
                        <li class="pointer nav-item btnEstados" data-toggle="modal" data-target="#modal_estados"><a class="nav-link"><span class="fa fa-history purple"></span> Historial</a></li>
                        <li class="pointer nav-item btnArchivos"><a class="nav-link"><span class="fa fa-file purple"></span> Archivos</a></li>
                        <li class="pointer nav-item download_contrato"><a class="nav-link"><span class="fas  fa-file-pdf purple"></span> Contrato</a></li>
                      </ul>
                    </div>
                  </nav>
                  <div class="table">
                    <!-- Auto gen -->
                    <table class="table table-condensed tabla_contratos text-left">
                      <tr class="codSAP_tr">
                        <td class="ttitulo codSAP w-auto">Código SAP</td>
                        <td class="codSAP_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo contratante w-auto">Contratante</td>
                        <td class="tante_space w-auto"></td>
                      </tr>
                      <tr class="tr_pg">
                        <td class="ttitulo num_contrato w-auto">Número de contrato</td>
                        <td class="num_contrato_space w-auto"></td>
                      </tr>
                      <tr class="tr_conv">
                        <td class="ttitulo contratom w-auto">Contrato</td>
                        <td class="contratom_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo objetivo w-auto">Objetivo</td>
                        <td class="objetivo_space w-auto"></td>
                      </tr>
                      <tr class="tr_conv">
                        <td class="ttitulo garantia w-auto">Garantía</td>
                        <td class="garantia_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo valor w-auto">Valor</td>
                        <td class="valor_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo fechaini w-auto">Fecha de inicio</td>
                        <td class="fechaini_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo fechafin w-auto">Fecha de finalización</td>
                        <td class="fechafin_space w-auto"></td>
                      </tr>
                      <tr class="tr_conv">
                        <td class="ttitulo plazo w-auto">Plazo</td>
                        <td class="plazo_space w-auto"></td>
                      </tr>
                      <tr class="tr_conv">
                        <td class="ttitulo plazo w-auto">Firma</td>
                        <td class="firma_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo contratista w-auto">Contratista</td>
                        <td class="tista_space w-auto"></td>
                      </tr>
                      <tr class="tr_conv">
                        <td class="ttitulo tipo_pers w-auto">Tipo de persona</td>
                        <td class="tipo_pers_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo cedunit w-auto">Cédula/Nit</td>
                        <td class="cedunit_space w-auto"></td>
                      </tr>
                      <tr>
                        <td class="ttitulo fechasus w-auto">Fecha de suscripción</td>
                        <td class="fechasus_space w-auto"></td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de la firma -->
    <div class="contratos-modal modal fade" id="firmar" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- contratos Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">FIRMAR CONTRATO
                  </h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- contratos Modal - Image-->
                  <input type="hidden" name="id" id="id_firma" value="">
                  <div class="row p-4">
                    <p class='text-justify'><b>Una vez el contratista y/o proveedor de bienes y servicios le de aceptar en la plataforma, acepta todo los terminos y condiciones establecidos en el documento y se entiende sucrito con el envio de la aceptación.</b></p>
                    <div class="input-group" style="width: 100%;">
                      <div class="d-inline-block">
                        <input type="checkbox" id="check_contra" name="check_contra">
                        <label for="check_contra" title="Con cuenta"><small>Acepto.</small></label>
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-primary btn-block mt-3 enviar_firma" value="subir" name="firmaBtn">Guardar firma</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal del historial -->
    <div class="contratos-modal modal fade" id="modal_estados" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- historial Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">Estados</h2>
                  <br>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- historial Modal - table-->
                  <table class="table table-sm table-responsive-sm" id="tabla_estados" cellspacing="0" width="100%">
                    <thead class="">
                      <tr class="filaprincipal">
                        <th>No.</th>
                        <th>Estado</th>
                        <th>Fecha Registra</th>
                        <th>Observación</th>
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

    <!-- Modal archivos adjuntos-->
    <div class="contratos-modal modal fade" id="modal_archivos_gestion" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- archivos adjuntos Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">ADJUNTOS</h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- archivos adjuntos Modal - table-->
                  <table id="tabla_adjs_cont" class="table table-sm table-responsive-sm" cellspacing="0" width="100%" style="overflow-wrap: break-word;">
                    <thead class="ttitulo">
                      <tr class="filaprincipal">
                        <th>Ver</th>
                        <th>Nombre Real</th>
                        <th>Fecha de guardado</th>
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

    <!-- Modal del cronograma -->
    <div class="contratos-modal modal fade" id="modal_cronograma" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- cronograma Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">Cronogramas</h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- cronograma Modal - table-->
                  <table class="table table-sm table-responsive-sm" id="tabla_cronograma" cellspacing="0" width="100%">
                    <thead class="">
                      <tr class="filaprincipal">
                        <th>Ver</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
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

    <!-- Modal adjuntos cronograma -->
    <div class="contratos-modal modal fade" id="modal_adjuntos_cronograma" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- cronograma Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">Adjuntar al cronograma</h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <br>
                  <!-- cronograma Modal - dropzone-->
                  <form class="dropzone needsclick dz-clickable" id="Subir" action="">
                    <input type="hidden" name="id" id="id_archivo" val="0">
                    <div class="dz-message needsclick">
                      <p>Arrastre archivos o presione clic aquí</p>
                    </div>
                  </form>
                  <br>
                  <button type="button" class="btn btn-success active" id="enviar_adjuntos"><span class="fas fa-save"></span> Temrinar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal archivos adjuntados cornograma-->
    <div class="contratos-modal modal fade" id="modal_adjuntados_cronograma" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- archivos adjuntos Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">ADJUNTOS</h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <!-- archivos adjuntos Modal - table-->
                  <table id="tabla_cronograma_adjuntos" class="table table-hover table-condensed" cellspacing="0" width="100%" style="overflow-wrap: break-word;">
                    <thead class="ttitulo">
                      <tr class="filaprincipal">
                        <th>Ver</th>
                        <th>Nombre Real</th>
                        <th>Fecha de guardado</th>
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

    <!-- Modal para ver el contrato adjuntado-->
    <div class="contratos-modal modal fade" id="modal_ver_contrato" tabindex="-1" role="dialog" aria-labelledby="contratosModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-11">
                  <!-- archivos adjuntos Modal - Title-->
                  <h2 class="contratos-modal-title text-secondary text-uppercase mb-0" id="contratosModal6Label">CONTRATO</h2>
                  <!-- Icon Divider-->
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>

                </div>
              </div>
            </div>
            <object id="verContratoPDF" height="900px" width="100%" type="application/pdf"></object>
          </div>
        </div>
      </div>
    </div>

  <?php endif; ?>
  <!-- Bootstrap core JS-->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script>
    $(document).ready(function() {
      $("input").change().stop();
    });
  </script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/contratistas.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Third party plugin JS-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
  <!-- Contact form JS-->
  <!-- Core theme JS-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/scriptsContratos.js"></script>
  <?php if ($session == true) : ?>
    <script>
      $(document).ready(function() {
        listarContratos();
      });
    </script>
  <?php endif; ?>
</body>

</html>