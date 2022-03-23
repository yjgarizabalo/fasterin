<?php
$administra = $_SESSION["perfil"] == "Per_Admin" ? true : false;
?>
<style>
	.btn:focus {
		outline: thin dotted;
		outline: 5px auto rgb(110, 31, 124);
		outline-offset: -2px;
	}

	.btn {
		background-color: white;
	}
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/html2canvas.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/th.css">
<div class="container col-md-12 " id="inicio-user">
	<div class="tablausu col-md-12 text-left" id="container_solicitudes">
		<div class="table-responsive">
			<p class="titulo_menu pointer" id="regresar_index"><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_encuestas" cellspacing="0" width="100%">
				<thead class="ttitulo " style="display: table-header-group;">
					<tr>
						<td colspan="2" class="nombre_tabla" style="vertical-align: middle;" rowspan="1">
							TABLA ENCUESTAS<br>
							<!--span class="mensaje-filtro" hidden>
								<span class="fa fa-bell red"></span>
								La tabla tiene algunos filtros aplicados.
							</span-->
						</td>
						<td colspan="8" class="sin-borde text-right border-left-none" rowspan="1">
							<!-- <?php if ($administra) { ?>
								<a class="btn btn-default" id="btn_exportar"><span class="fa fa-cloud-download red"></span> Exportar</a>
							<?php } ?>
							<span id="btn_csep"></span>
							<span id="btn_filtros" class="black-color pointer btn btn-default">
								<span class="fa fa-filter red"></span> Filtrar
							</span>
							<span id="btn_limpiar" class="black-color pointer btn btn-default">
								<span class="fa fa-refresh red"></span> Limpiar
							</span> -->
						</td>
					</tr>
					<tr role="row" class="filaprincipal">
						<td class="sorting_1">ver</td>
						<td>ID Encuesta</td>
						<td>Encuesta</td>
						<td>Fecha</td>
					</tr>
				</thead>
				<tbody style="display: table-row-group;">
				</tbody>
			</table>
		</div>
	</div>
	<!-- Modal ver personas que realizaron la encuesta-->
	<div class="modal fade" id="modal_ver_encuesta" role="dialog">
		<div class="modal-dialog modal-lg">
			<form id="form_periodo" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-list"></span> Detalles Encuesta</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="table-responsive col-md-12" style="width: 100%" id='encuestas_resultados'>
							<table class="table table-bordered table-hover table-condensed pointer" id="tabla_detalle_encuesta" cellspacing="0" width="100%">
								<thead class="ttitulo ">
									<tr class="">
										<td colspan="6" class="nombre_tabla">TABLA RESULTADOS</td>
									</tr>
									<tr class="filaprincipal">
										<td class="opciones_tbl">ver</td>
										<td>Nombre Completo</td>
										<td>Identificación</td>
										<td>Fecha</td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="modal_ver_detalle_encuesta" role="dialog">
		<div class="modal-dialog modal-lg">
			<form id="form_periodo" method="post">
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-list"></span> Secciones </h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div id="accordion" style="margin: 20px 0">
							<div class="accordion-item" id="tarjeta">

							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</form>
		</div>
	</div>

</div>

<script>
	$(document).ready(function() {
		inactivityTime();
	});
	const idp = <?php echo $id; ?>;
</script>