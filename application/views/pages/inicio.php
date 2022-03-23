<div id="principal2">
    <div class="logeo" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_principal.png")'>

        <div class="container-login">
            <div class="avatar" style="background-image: url(<?php echo base_url(); ?>/imagenes/User.png)"></div>
            <div class="form-box">

                <form class="form-signin"  method="post" id="logeo">
                    <span id="reauth-email" class="reauth-email"></span>
                    <input type="text" id="inputEmail" class="" style="margin:  0px;padding: 0px"placeholder="Usuario" required autofocus name="usuario">
                    <input type="password" id="inputPassword" style="margin:  0px;padding: 0px" class="" placeholder="Contraseña" required name="contrasena">
                    <div id="remember" class="checkbox">

                    </div>
                    <button type="submit" class="btn btn-danger btn-lg  btn-block btn-signin active"  id="btnentrar"><span class="glyphicon glyphicon-log-in"></span> Ingresar </button> </form><!-- /form -->
                </form>
            </div>
        </div>
    </div>

</div>
