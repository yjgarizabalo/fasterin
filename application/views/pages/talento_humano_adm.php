
<?php 
  $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true :false;
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/html2canvas.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/th.css">
<div class="container col-md-12 " id="inicio-user">
<div class="tablausu col-md-12 " id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
	<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
		<div id="container-principal2" class="container-principal-alt">
			<h3 class="titulo_menu">
				<span class="fa fa-navicon"></span> MENÚ
			</h3>
			<div class="row" id="menu_th">
				<?php foreach($permisos as $row){ ?>
					<a style="color: black; font-style: oblique;font-weight: bold;" href="<?php echo base_url().'index.php/'.$row['id_actividad'] ?>" class="sinlink">
					<div>
						<div class="thumbnail">
							<div class="caption">
								<img src="<?php echo base_url() ?>/imagenes/<?php echo $row['icono'] ?>" alt="...">
								<span class="btn  form-control btn-Efecto-men"><?php echo $row['actividad'] ?></span>
							</div>
						</div>
					</div>
				</a>	
			<?php } ?>
			</div>
			<p class="titulo_menu titulo_menu_alt pointer" id="btn_regresar_adm"><span class="fa fa-reply-all naraja"></span>Regresar</p>
		</div>
	</div>
</div>
</div>

<script>
    $(document).ready(function () {
		inactivityTime();
	});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
