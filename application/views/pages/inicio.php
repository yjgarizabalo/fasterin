<!-- <div>
	<div class="container-login">
		<div class="avatar" style="background-image: url(<?php echo base_url(); ?>/imagenes/User.png)"></div>
		<div class="form-box">

			<form class="form-signin"  method="post" id="logeo">
				<span id="reauth-email" class="reauth-email"></span>
				<input type="text" id="inputEmail" class="" style="margin:  0px;padding: 0px"placeholder="Usuario" required autofocus name="usuario">
				<input type="password" id="inputPassword" style="margin:  0px;padding: 0px" class="" placeholder="Contraseña" required name="contrasena">
				<div id="remember" class="checkbox">

				</div>
				<button type="submit" class="btn btn-danger btn-lg  btn-block btn-signin active"  id="btnentrar"><span class="glyphicon glyphicon-log-in"></span> Ingresar </button> </form>
			</form>
		</div>
	</div>
</div> -->

<main class="container">
	<div class="row align-items-center " style="min-height: 100vh">
		<div class="col-md-8">
			<div class="text-center">
			    <img class="img-fluid" src="<?php echo base_url(); ?>/imagenes/logo_fasterin.png" alt="fasterin banner"> 
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-signin">
				<form class="form-signin"  method="post" id="logeo">

					<h1 class="h3  mb-3 text-center fw-normal">Iniciar sesión</h1>

					<div class="form-floating">
					<span id="reauth-email" class="reauth-email"></span>
					<input type="text" class="form-control mb-2" id="inputEmail" placeholder="nombre@ejemplo.com" >
					<label for="floatingInput">Usuario</label>
					</div>
					<div class="form-floating">
					<input type="password" class="form-control" id="inputPassword" placeholder="contraseña">
					<label for="floatingPassword">Contraseña</label>
					<div id="remember" class="checkbox">
					</div>
					

					<div class="checkbox mb-3">
					<label>
						<input type="checkbox" value="remember-me"> Recordarme
					</label>
					</div>
					<button class="w-100 btn btn-lg btn-primary" type="submit" id="btnentrar"><i class='bx bx-log-in'></i>  Ingresar</button>

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
