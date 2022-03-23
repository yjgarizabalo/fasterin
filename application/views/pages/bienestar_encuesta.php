<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AGIL</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="<?php echo base_url(); ?>imagenes/logo_cuc2.png" type="image/png" rel="shortcut icon" />

  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/buttons.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/MyStyle.css">
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.bootstrap.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Usuarios.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Bienestar.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery.serializejson.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/push.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
  <!--botones DataTables-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/buttons.bootstrap.min.js"></script>
  <!--Libreria para exportar Excel-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jszip.min.js"></script>
  <!--Librerias para exportar PDF-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/pdfmake.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/vfs_fonts.js"></script>
  <!--Librerias para botones de exportación-->
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/buttons.html5.min.js"></script>
</head>
<body style="overflow: auto;height: 100%;	background-image: url(<?php echo base_url(); ?>/imagenes/fondo.jpg)">
<?php if(isset($id_final) && !empty($id_final)){ ?>
<div class="container">
  <div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading" style="padding-left: 10%;">
        <div class="row">
            <div class="col-md-9">
                <h3 style="font-weight: 700;text-transform: uppercase;">Encuesta de satisfacción grupal</h3>
            </div>
            <div class="col-md-3">
                <img src="<?php echo base_url(); ?>/imagenes/logo_principal.png" width="80%" class="img-responsive">
            </div>
        </div>
    </div>
    <div class="panel-body">
    <form id="form_encuesta" method="post">

        <div class="form-group">
            <div>
                <h4>1) ¿Cómo le pareció la actividad desarrollada?</h4>
            </div>
            <br>
            <div class="col-md-4">
                <div class="radio">
                    <label><input type="radio" name="actividad" value="EXCELENTE">EXCELENTE</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="actividad" value="BUENO">BUENO</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="actividad" value="REGULAR">REGULAR</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="actividad" value="MALO">MALO</label>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
        </div>

        <div class="form-group">
            <div>
                <h4>2) La atención del servicio que usted recibió fue:</h4>
            </div>
            <br>
            <div class="col-md-4">
                <div class="radio">
                    <label><input type="radio" name="servicio" value="EXCELENTE">EXCELENTE</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="servicio" value="BUENO">BUENO</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="servicio" value="REGULAR">REGULAR</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="servicio" value="MALO">MALO</label>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
        </div>

        <div class="form-group">
            <div>
                <h4 style="font-weight:bold"> Completar las siguientes preguntas si la actividad es una charla, conferencia, curso ó capacitación</h4><br>
            </div>
            <div>
                <h4>3) Los temas tratados, ¿le resultan apropiados para ayudarle en su desempeño?</h4>
            </div>
            <br>
            <div class="col-md-4">
                <div class="radio">
                    <label><input type="radio" name="apropiado" value="SI">SI</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="apropiado" value="NO">NO</label>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group">
            <div>
                <h4>4) ¿Consideras que el tema abordado aporta a tu formación integral?</h4>
            </div>
            <br>
            <div class="col-md-4">
                <div class="radio">
                    <label><input type="radio" name="integral" value="SI">SI</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="integral" value="NO">NO</label>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
        </div>
        <div class="form-group">
            <div>
                <h4>5) ¿Cómo le pareció la metodología utilizada por la persona que dirigió la actividad?</h4>
            </div>
            <br>
            <div class="col-md-4">
                <div class="radio">
                    <label><input type="radio" name="metodologia" value="EXCELENTE">EXCELENTE</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="metodologia" value="BUENO">BUENO</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="metodologia" value="REGULAR">REGULAR</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="metodologia" value="MALO">MALO</label>
                </div>
            </div>
            <div class="clearfix"></div>
            <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
        </div>
        <div class="form-group">
            <div>
                <h4>6) ¿Qué otros cursos o talleres le gustaría que implementara la Vicerrectoría de Bienestar Universitario?</h4>
            </div>
            <br>
            <div class="col-md-8">
              <input type="text" name="otros" class="form-control inputt2" min="1"  data-toggle="popover" data-trigger="hover">
            </div><br><br>
            <div class="clearfix"></div>
            <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
        </div>
      <div class="alert alert-warning" style="text-align: justify;font-weight: 600;">
        Al diligenciar este formato usted otorga su autorización a UNIVERSIDAD DE LA COSTA - CUC para que utilice sus datos informales, con la única finalidad de prestarles a los usuarios una mejor atención contacto e información sobre nuestros productos, servicios, ofertas y promociones para mantener canales de comunicación, así como noticias relacionadas con el desarrollo de las actividades académicas (fotografías y videos). Si desea revocar esta autorización, envíe un correo electrónico a la dirección buzon@cuc.edu.co, o contáctenos en la página web www.cuc.edu.co, o a la Dirección Cll. 58 #55-66 - Barranquilla, Colombia. No cedemos datos personales a terceros sin su debida autorización, cumplimos con el principio de circulación restringida, necesidad y finalidad de la Ley 1581 de 2012 y sus decretos reglamentarios.
        </div>
        <div class="container-fluid">
          <button type="submit" class="btn btn-primary btn-lg btn-block">Enviar</button>
        </div>
        </form>
    </div>
  </div>
</div>

  <script>
    $(document).ready(function () {
      $("#form_encuesta").submit(() => {
        guardar_encuesta(<?php echo $id_final?>);
        return false;
	    });
    });
  </script>
<?php  }else{?>

<form action="<?php echo base_url();?>index.php/bienestar/encuesta" method='post' id='form_logear_encuesta'>
  <div class="modal fade" id="modal_logear" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-sign-in"></span> Hola <span id='nombre'></span></h3>
      </div>
      <div class="modal-body" style="padding: 60px;">
        <div>
        <h4 class='text-center'>Para realizar la encuesta solo ingresa tu contraseña institucional</h4>
        <input type="password" name='contrasena' class='form-control' placeholder='Ingrese Contraseña' autofocus="autofocus">
        <input type="text" name='usuario' class='form-control oculto'>
        <input type="text" name='codigo' class='form-control oculto'>
      </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" id="btn_login_encuesta" class="btn btn-danger active"><span class="fa fa-check"></span> Ingresar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
</form>
  <div class="tablausu col-md-12" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div> 
        <div id="container-principal2" class="container-principal-alt">
          <h3 class="titulo_menu"><span class="fa fa-list"></span> Bienestar</h3>
            <div class="row">
              <div class="panel-body text-center">
                <div class=" "><img src="<?php echo base_url(); ?>/imagenes/bienestar.png" style="width: 300px"></div>
                <h3>Estamos trabajando para ofrecerte un servicio con calidad.</h3> <br> 
              </div>  
            </div>
        </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function () {
      <?php if(!empty($id) && !empty($usuario)){ ?> logear_bienestar(<?php echo json_encode($nombre)?>, <?php echo  json_encode($usuario)?>, <?php echo json_encode($id)?>,<?php echo json_encode($codigo)?>)
        <?php }else{?>	MensajeConClase('El codigo ingresado es invalido o el usuario realizo la encuesta anteriormente.', 'info', 'Oops.!');<?php }?>
        <?php if(!empty($mensaje)){?>MensajeConClase('El usuario y/o contraseña son invalidos, el codigo ingresado es invalido o el usuario realizo la encuesta anteriormente.', 'info', 'Oops.!');<?php }?>
        <?php if(isset($success)){?>	MensajeConClase('Gracias por realizar la encuesta de satisfacción.', 'success', 'Encuesta Enviada!');<?php }?>
    });
  </script>

<?php  }?>
</body>
</html>
