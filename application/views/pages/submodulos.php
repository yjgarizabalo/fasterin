<div class="container col-md-12 text-center" id="inicio-user">
	<div class="tablausu col-md-12" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
			<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
			</div> 
			<div id="container-principal2" class="container-principal-alt">
			<h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
			<div class="row">
			<?php foreach ($actividades as $acti){?>
				<a class="sinlink" href="<?php echo base_url().'index.php/'.$acti['actividad']?>">
					<div class='thumbnail' >
						<div class='caption'>
							<img src='<?php echo base_url()?>imagenes/<?php echo $acti["icono"]?>' alt='...'>
							<span class = 'btn form-control'><?php echo  $acti["nombre"]  ?></span>
						</div>
					</div>
				</a>
			<?php }?>
			</div>
			<p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
			</div>
		</div>
	</div>
	</div>
</div>
