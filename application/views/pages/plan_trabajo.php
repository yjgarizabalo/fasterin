<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>AGIL</title>
  <!-- Favicon-->
  <link href="<?php echo base_url(); ?>imagenes/logo_cuc2.png" type="image/png" rel="shortcut icon" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/stylesTalentoCuc.css">
</head>

<body id="page-top">
  <nav class="navbar navbar-expand-lg text-uppercase fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img alt="Agil" src="<?php echo base_url(); ?>imagenes/a_agil.png" width='35' />
      </a>
      <a class="navbar-brand js-scroll-trigger" href="#page-top">Plan de Trabajo</a>
      <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">Menu <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav align-self-end flex-wrap ml-auto" id="nav">
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_formacion">Formación</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_perfiles">Perfiles</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_horas">Horas</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_asignaturas">Asignaturas</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_horarios">Horarios</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_indicadores">Indicadores</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_lineas">Lineas</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#cont_observaciones">Observaciones</a>
          </li>
          <li class="nav-item mx-0 mx-lg-1" id="btn_buscar_periodo" style="cursor:pointer;">
            <a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" data-toggle="modal" data-target="#modal_periodos">
              <span class="fa fa-edit"></span> Periodo
            </a>
          </li>
          <li class="nav-item dropdown m-auto d-none">
            <a class="nav-link" href="#" id="navbarDropdownMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-bars"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenu">
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- MASTHEAD-->
  <header class="masthead bg-primary text-white ">
    <div class="container d-flex align-items-center flex-column">
      <!-- Masthead Avatar Image-->
      <span style="height: 40px;"></span>
      <img class="masthead-avatar mb-5 imagen_empleado_plan" src="<?php echo base_url(); ?>imagenes_personas/empleado.png" class="img-thumbnail imagen_empleado_plan" alt="Foto Empleado">
      <!-- Masthead Heading-->
      <h1 class="masthead-heading text-uppercase text-center mb-0" id='nombre_profesor'></h1>
      <!-- Icon Divider-->
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- Masthead Subheading-->
      <p class="font-weight-light mb-0 text-justify text-uppercase" id='informacion_general'>Coordinador de desarrollo - Profesional III</p>
      <p class="font-weight-light mb-0 text-justify text-uppercase mr-auto">Información de Contrato: <span id='informacion_contrato'></span></p>
      <br>
      <?php if ($profesor && $plan['firma_profesor']) { ?>
        <button class="btn btn-secondary " id='btn_modificar_firma'>Modificar Firma </button>
      <?php } ?>
      <?php if ($descargar) { ?>
        <a class="btn btn-secondary mt-2" href='<?php echo base_url(); ?>index.php/descargar_plan_trabajo/<?php echo $plan['id_persona'] ?>'>Descargar</a>
      <?php } ?>
    </div>
  </header>

  <!-- FORMACION -->
  <section class="page-section" id="cont_formacion">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0 ">FORMACIÓN</h2>
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
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
    </div>
  </section>

  <!-- PERFILES -->
  <section class="page-section bg-primary text-white" id="cont_perfiles">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase mb-0">PERFILES</h2>
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div class="table-responsive">
        <table class="table text-white" id="perfiles" cellspacing="0" width="100%">
          <thead class="">
            <tr class="font-weight-bold">
              <td>Nombre</td>
              <td>Rol</td>
              <td>Cobertura</td>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
  </section>

  <!-- HORAS -->
  <section class="page-section" id="cont_horas">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0 ">HORAS X PROGRAMA</h2>
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div class="table-responsive">
        <table class="table" id="horas_programa" cellspacing="0" width="100%">
          <thead class="">
            <tr class="font-weight-bold">
              <td>Programa</td>
              <td>Hora</td>
              <td>Cantidad</td>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ASIGNATURAS -->
  <section class="page-section bg-primary text-white" id="cont_asignaturas">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase mb-0">ASIGNATURAS</h2>
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div class="row" id='asignaturas'></div>
  </section>

  <!-- HORARIOS -->
  <section class="page-section" id="cont_horarios">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0 ">HORARIOS DE ATENCIÓN</h2>
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div class="row" id='atencion'></div>
    </div>
  </section>

  <!-- INDICADORES -->
  <section class="page-section bg-primary text-white" id="cont_indicadores">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase mb-0">INDICADORES</h2>
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4 mb-5">
          <div class="card">
            <img class="img-fluid" src="<?php echo base_url(); ?>imagenes/impactos.png" alt="" />
            <div class="card-body" style="height:300px">
              <h6 class="card-title" style='color : black'>Indicadores</h6>
              <p class="card-text" style='color : black'>Aquí podrás visualizar una lista detallada de tus indicadores del plan de trabajo.</p>
              <button class="btn btn-primary btn-block" id='ver_indicadores'>Ver</button>
            </div>
          </div>
        </div>
      </div>
  </section>

  <!-- LINEAS -->
  <section class="page-section" id="cont_lineas">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0 ">LINEAS DE INVESTIGACIÓN</h2>
      <div class="divider-custom">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div class="row" id='lineas'></div>
    </div>
  </section>

  <!-- OBSERVACIONES -->
  <section class="page-section bg-primary text-white" id="cont_observaciones">
    <div class="container">
      <h2 class="page-section-heading text-center text-uppercase mb-0">OBSERVACIONES</h2>
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <div>
        <ul class="list-group list-group-flush" id='observaciones'>
        </ul>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <div class="copyright py-4 text-center text-white">
    <div class="container"><small>Copyright © <?php echo date("Y") ?> Universidad de la Costa CUC</small></div>
  </div>
  <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
  <div class="scroll-to-top d-lg-none position-fixed">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i class="fa fa-chevron-up"></i></a>
  </div>

  <!-- MODAL AGREGAR FORMACIÓN -->
  <div class="portfolio-modal modal fade" id="modal_guardar_formacion" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">NUEVA</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_formacion_personal" enctype="multipart/form-data" method="post">
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

  <!-- MODAL LISTAR SOPORTES FORMACIÓN -->
  <div class="portfolio-modal modal fade" id="modal_listar_soportes" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <table class="table" id="tabla_soportes_academicos" cellspacing="0" width="100%">
                  <thead class="">
                    <tr class="filaprincipal">
                      <td>Nombre</td>
                      <td class="opciones_tbl" style="min-width: 120px;">Acción</td>
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

  <!-- MODAL AGREGAR SOPORTES FORMACIÓN -->
  <div class="portfolio-modal modal fade" id="modal_enviar_archivos" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">SOPORTES</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_soporte_academico" enctype="multipart/form-data">
                  <div class="row">
                    <div id="campo_soporte" class="input-group agrupado">
                      <label class="input-group-btn">
                        <span class="btn btn-primary">
                          <span class="fa fa-folder-open"></span> Buscar
                          <input name="soporte_form" type="file" style="display: none;">
                        </span>
                      </label>
                      <input type="text" id="soporte_form" class="form-control" readonly placeholder='Adjuntar documento' required>
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

  <!-- MODAL AGREGAR HORARIOS DE ATENCION -->
  <div class="portfolio-modal modal fade" id="modal_guardar_atencion" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">NUEVO</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_horario_atencion" enctype="multipart/form-data" method="post">
                  <div class="row">
                    <select name="id_tipo" required class="form-control mt-3 cbx_tipo_horario" style="display: none;">
                      <option value="">Seleccione Tipo</option>
                    </select>
                    <select name="id_asignatura" required class="form-control mt-3 cbx_asignatura_horario">
                      <option value="">Seleccione Asignatura</option>
                    </select>
                    <select name="id_dia" required class="form-control mt-3 cbx_dia_horario">
                      <option value="">Seleccione Dia</option>
                    </select>
                    <div class="form-group mt-3 col-12 p-0 text-left">
                      <label>Fecha Inicio</label>
                      <div class="input-group">
                        <input type="time" class="form-control" required="true" name='hora_inicio'>
                      </div>
                    </div>
                    <div class="form-group mt-3 col-12 p-0 text-left">
                      <label>Fecha Fin</label>
                      <div class="input-group">
                        <input type="time" class="form-control" required="true" name='hora_fin'>
                      </div>
                    </div>
                    <input name="lugar" required class="form-control mt-3" type="text" placeholder='Lugar'>
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

  <!-- MODAL INDICADORES -->
  <div class="portfolio-modal modal fade" id="modal_indicadores" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-10">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">INDICADORES</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <div class="row">
                  <div class="col-lg-12 ml-auto text-left">
                    <div class="">
                      <h6 class="divider-custom-icon"><i class="fas fa-filter"></i> Filtrar</h6>
                      <p>Coloca una fecha inicial y una fecha final, y te mostrará los indicadores que debes cumplir en ese rango de fechas(fecha meta).</p>
                    </div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-lg-6 mb-1 ">
                    <div class="input-group">
                      <input class="form-control sin_margin" value="" type="date" name="fecha_inicial" id="fecha_inicial" title="Fecha Inicial" placeholder="Fecha Inicial">
                    </div>
                  </div>
                  <div class="col-lg-6 mb-1 ">
                    <div class="input-group">
                      <input class="form-control sin_margin" value="" type="date" name="fecha_final" id="fecha_final" title="Fecha Final" placeholder="Fecha Final">
                    </div>
                  </div>
                  <div class="col-lg-6 mb-1 ">
                    <button class="btn btn-block btn-secondary" id='btn_generar_filtro_ind'>Generar</button>
                  </div>
                  <div class="col-lg-6 mb-1 ">
                    <button class="btn btn-block btn-secondary" id='btn_limpiar_filtro_ind'>Limpiar</button>
                  </div>
                  <div class="col-lg-12 mb-1 mt-5">
                    <div class="input-group">
                      <input type="text" placeholder="Buscar" class="form-control live-search-box">
                    </div>
                  </div>
                </div>
                <div class="row table-responsive m-0">
                  <table class="table mt-2 live-search-table text-left" id="tabla_indicadores_personal" cellspacing="0" width="100%">
                    <thead>
                      <tr class="font-weight-bold text-center text-uppercase">
                        <td colspan="7" class="">Lista Indicadores</td>
                      </tr>
                      <tr class="font-weight-bold">
                        <td class="align-middle">N°</td>
                        <td class="align-middle">Nombre</td>
                        <td class="align-middle" style="min-width: 120px;">Fecha Inicial</td>
                        <td class="align-middle">Estado Inicial</td>
                        <td class="align-middle" style="min-width: 120px;">Fecha Meta</td>
                        <td class="align-middle">Meta</td>
                        <td class="align-middle" style="max-width: 100px;">Numero Indicador</td>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL BUSCAR PERIODO -->
  <div class="portfolio-modal modal fade" id="modal_periodos" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">PERIODO</h2>
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <div class="row">
                  <select name="id_periodo" required class="form-control mt-3 cbx_periodos">
                    <option value="">Seleccione Periodo</option>
                  </select>
                </div>
                <button id="btn_cambiar_periodo_profesor" class="btn btn-primary btn-block mt-3"> Aceptar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL SUBIR FIRMA -->
  <?php
  $firma = $profesor && !$plan['firma_profesor'];
  if ($firma) { ?>
    <div class="portfolio-modal modal fade" id="cargar_firma" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-8">
                  <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">FIRMA</h2>
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <div class="row ">
                    <div class="col-lg-12 ml-auto p-0">
                      <div class="">
                        <p class="text-justify">Para los procesos internos de la Universidad, es necesario contar con su firma para aprobar el plan de trabajo.</p>
                      </div>
                    </div>
                  </div>
                  <form id="cargar_firma_digital" enctype="multipart/form-data">
                    <div class="row">
                      <div id="cont_firma_digital" class="input-group agrupado">
                        <label class="input-group-btn">
                          <span class="btn btn-primary">
                            <span class="fa fa-folder-open"></span> Buscar
                            <input name="firma_digital" type="file" accept="image/*" style="display: none;">
                          </span>
                        </label>
                        <input type="text" id="firma_digital_text" class="form-control" readonly placeholder='Firma Digital'>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Guardar</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- MODAL MODIFICAR FIRMA -->
  <?php if ($profesor && $plan['firma_profesor']) { ?>
    <div class="portfolio-modal modal fade" id="modal_modificar_firma" tabindex="-1" role="dialog" aria-labelledby="portfolioModal6Label" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
          </button>
          <div class="modal-body text-center">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-lg-8">
                  <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">MODIFICAR FIRMA</h2>
                  <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                  </div>
                  <div class="row ">
                    <div class="col-lg-12 ml-auto p-0">
                      <div class="">
                        <p class="text-justify">Para los procesos internos de la Universidad, es necesario contar con su firma para aprobar el plan de trabajo.</p>
                      </div>
                    </div>
                  </div>
                  <form id="modificar_firma_digital" enctype="multipart/form-data">
                    <div class="row">
                      <div id="cont_firma_digital_mod" class="input-group agrupado">
                        <label class="input-group-btn">
                          <span class="btn btn-primary">
                            <span class="fa fa-folder-open"></span> Buscar
                            <input name="firma_digital_mod" type="file" accept="image/*" style="display: none;">
                          </span>
                        </label>
                        <input type="text" id="firma_digital_text_mod" class="form-control" readonly placeholder='Firma Digital'>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Modificar</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- Bootstrap core JS-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Usuarios.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/profesores_csep.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Third party plugin JS-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.min.js"></script>
  <!-- Contact form JS-->
  <!-- Core theme JS-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/scriptsTalentoCuc.js"></script>
  <script>
    inactivityTime();
    ver_plan_profesor(<?php echo json_encode($plan) ?>)
    activarfile();
    pintar_periodos(<?php echo $plan['id_persona'] ?>, 2);
    Cargar_parametro_buscado(84, ".cbx_formacion", "Seleccione Formación");
    Cargar_parametro_buscado_aux(103, ".cbx_tipo_horario", "Seleccione Tipo");
    Cargar_parametro_buscado(100, ".cbx_dia_horario", "Seleccione Día");
    <?php if ($firma) { ?>
      $('#cargar_firma').modal('show');
    <?php } ?>
  </script>
</body>

</html>