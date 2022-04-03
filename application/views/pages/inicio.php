<main class="container">
	<div class="row align-items-center " style="min-height: 100vh">
		<div class="col-md-8">
			<div class="text-center">
				<img class="img-fluid" src="<?php echo base_url(); ?>/imagenes/logo_fasterin.svg" alt="fasterin banner" width="600">
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-signin">
				<form class="form-signin" method="post" id="logeo">

					<h1 class="h3  mb-3 text-center fw-normal">Iniciar sesión</h1>

					<div class="form-floating">
						<span id="reauth-email" class="reauth-email"></span>
						<input type="text" class="form-control mb-2" id="inputEmail" placeholder="Usuario" name="usuario">
						<label for="floatingInput">Usuario</label>
					</div>
					<div class="form-floating">
						<input type="password" class="form-control" id="inputPassword" placeholder="contraseña" name="contrasena">
						<label for="floatingPassword">Contraseña</label>
						<div id="remember" class="checkbox">
						</div>


						<div class="checkbox mb-3">
							<label>
								<input type="checkbox" value="remember-me"> Recordarme
							</label>
						</div>
						<button class="w-100 btn btn-lg btn-primary btn-signin active" type="submit" id="btnentrar"><i class='bx bx-log-in'></i> Ingresar</button>

						<p class="mt-3 mb-3 text-center">
							<a class="text-decoration-none link-secondary" href="/">
								Olvide la mi contraseña
							</a>
						</p>
				</form>
			</div>
		</div>

	</div>
</main>
