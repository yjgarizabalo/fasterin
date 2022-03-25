<html>

<head>
	<title>Fasterin</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo base_url(); ?>imagenes/favicon_fasterin.png" type="image/png" rel="shortcut icon" />

	<!-- Boostrap 5 -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/boostrap-5/bootstrap.css">

	<!-- Iconos boxicons -->
	<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/boxicons/boxicons.css"> -->



	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/buttons.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/MyStyle.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/stepper.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bs-stepper.min.css">


  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/Chart.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/Chart.min.css"> -->

	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/bs-stepper.min.js"></script> -->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap.min.js"></script> -->
  <!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery.dataTables.min.js"></script> -->
  <!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.js"></script> -->
  <!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.min.js"></script> -->
	<script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Usuarios.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>


	<!-- <?php if (!empty($js)) { ?>
    <script src="<?php echo base_url(); ?>js-css/genericos/js/<?php echo $js; ?>.js"></script>
  <?php
			} ?> -->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery.serializejson.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/push.min.js"></script> -->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script> -->
	<!--botones DataTables-->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/buttons.bootstrap.min.js"></script> -->
	<!--Libreria para exportar Excel-->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/jszip.min.js"></script> -->
	<!--Librerias para exportar PDF-->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/pdfmake.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/vfs_fonts.js"></script> -->
	<!--Librerias para botones de exportación-->
	<!-- <script src="<?php echo base_url(); ?>js-css/estaticos/js/buttons.html5.min.js"></script>

  <script src="<?php echo base_url(); ?>js-css/estaticos/js/Chart.bundle.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/Chart.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/Chart.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/Chart.bundle.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/chartjs-plugin-datalabels.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/xlsx.full.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/moment.min.js"></script> -->

	<!-- Select de boostrap -->
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/boostrap_select/bootstrap-select.css">
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/boostrap_select/bootstrap-select.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/boostrap_select/i18n/defaults-es_ES.js"></script> -->

</head>

<body>

	<header>

		<!-- Navegacion fasterin -->

		<nav class="navbar navbar-expand navbar-light bg-light border-start border-6 border-primary">
			<div class="container-fluid">
				<span class="navbar-brand mb-0 h1 text-orange"><i class='bx bxs-bolt'></i>
					<?php echo $actividad ?>
				</span>

				<?php if (!empty($_SESSION["usuario"])) {
					echo '
						<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon navbar-inverse"></span>
							<span class="navbar-toggler-icon navbar-inverse"></span>
							<span class="navbar-toggler-icon navbar-inverse"></span>  
						</button>
						';
				}
				?>

				<?php if (!empty($_SESSION["usuario"])) {
					echo '
					<div class="collapse navbar-collapse " id="navbarSupportedContent">
						<ul class="navbar-nav ms-auto mb-2 ml-lg-0">
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <i class="bx bxs-user"></i>
								' . $_SESSION["nombre"] . ' ' . $_SESSION["apellido"] . '
								</a>
								<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
									<li id="presentation"><a class="dropdown-item" href="#">Cuenta</a></li>
									<li><a class="dropdown-item" href="#">Another action</a></li>
									<li>
										<hr class="dropdown-divider">
									</li>
									<li><a class="dropdown-item" href="#">Something else here</a></li>
								</ul>
							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bxs-book"></i> Manuales</a>

								<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
								
									<li><a class="dropdown-item" href="' . base_url() . 'manuales/ManualAlmacen.pdf" target="_blank"><i class="bx bxs-component"></i> Almacen</a></li>

									<li><a class="dropdown-item" href="' . base_url() . 'manuales/ManualAscensos.pdf" target="_blank"><i class="bx bxs-flag"></i> Acensos</a></li>

								</ul>
							</li>

							<li class="nav-item">
								<p type="button" id="notificacion_general"><span <i class="bx bxs-bell"></i> <span id="notificaciones">0</span> Notificaciones</p>
							</li>
							
						</ul>
					</div>
					';
				} ?>


			</div>
		</nav>

		<!-- <div class="collapse navbar-collapse" id="myNavbar">
			<ul class="nav navbar-nav navbar-right nav-user pointer">
				<li class="dropdown ">
					<p class=" dropdown-toggle " type="button" data-toggle="dropdown"><span class="fa fa-user"></span> ' . $_SESSION["nombre"] . ' ' . $_SESSION["apellido"] . '
						<span class="caret"></span>
					</p>
					<ul class="dropdown-menu">
						<li role="presentation" class="dropdown-header">Cuenta</li>
						<li id="cuentauser"><a href="#"><span class="fa fa-edit"></span> Mi Cuenta</a></li>

						<li role="presentation" class="dropdown-header">Perfiles</li>

						<li id="perfiluser">

							<a href="#">

								<select style="outline:none;	appearance:none; border-left:none; border-right:none; border-top:none;" name="perfiles" id="perfiles">
								</select>

							</a>
						</li>

						<hr>
						<li data-toggle="modal" data-target="#ModalLogeo" id="salir"><a href="#"><span class="fa fa-sign-out"></span> Salir</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right nav-user pointer" style="margin-right:15px;">
				<li class="dropdown ">
					<p class=" dropdown-toggle " type="button" data-toggle="dropdown"><span class="fa fa-book"></span> MANUALES<span class="caret"></span></p>
					<ul class="dropdown-menu">
						<li role="presentation" class="dropdown-header">Manuales</li>
						<li><a href="' . base_url() . 'manuales/ManualAlmacen.pdf" target="_blank"><span class="fa fa-cubes"></span> Almacen</a></li>
						<li><a href="' . base_url() . 'manuales/ManualAscensos.pdf" target="_blank"><span class="fa fa-flag"></span> Ascensos</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right nav-user pointer" style="margin-right:15px;">
				<li>
					<p type="button" id="notificacion_general"><i class='bx bxs-bell'></i> <span id="notificaciones"><b>0</b></span> NOTIFICACIONES</p>
				</li>
			</ul>
		</div> -->

		<!-- Fin navegación fasterin -->



	</header>

	<section id="seccion-vista">
		<div class="modal fade" id="Modal-cuenta" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Mi cuenta</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row" style="width: 80%">
							<div class="error text-center"></div>
							<div id="datos_perso" class="">
								<table class="table">
									<tr class="nombre_tabla">
										<td colspan="2">Datos</td>
									</tr>
									<tr>
										<td class="ttitulo">Nombre Completo</td>
										<td id="nombre_cuenta"></td>
									</tr>
									<tr>
										<td class="ttitulo">Tipo identificación</td>
										<td id="tipo_id_cuenta"></td>
									</tr>
									<tr>
										<td class="ttitulo">identificación</td>
										<td id="identi_cuenta"></td>
									</tr>
									<tr>
										<td class="ttitulo">Usuario</td>
										<td id="usuario_cuenta"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- <script>
      $(document).ready(function() {
        $('ul .dropdown-menu').click(function(e) {
          e.stopPropagation();
        });
      });
    </script> -->


		<div class="modal fade" id="s" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Notificación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<iframe width="960" height="540" src="https://web.microsoftstream.com/embed/channel/11952363-9c9f-4744-836f-13c51a985636?sort=trending" allowfullscreen style='border:none;'></iframe>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
