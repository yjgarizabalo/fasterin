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
  <script src="<?php echo base_url(); ?>js-css/genericos/js/biblioteca.js"></script>
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
<?php if(isset($codigo) && !empty($codigo)){ ?>
<div class="container-fluid">
  <div class="container-fluid" style="padding: 50px; height: 100%; width: 100%; ">
    <div class="row">
      <div class="col">
      <div class="panel panel-default">
      <div class="container-fluid panel-heading">
        <img src="<?php echo base_url(); ?>/imagenes/logo_principal.png" width="300" height="100" class="img-responsive" align="right">
        <h3><?php echo $tipo_solicitud ?> - Encuesta de satisfacción</h3>
      </div>
      <div class="panel-body">
        <h5>¡Hola! Queremos conocer tu opinión sobre nuestro servicio especial de préstamo. Toma menos de 5 minutos y sin duda podrá ayudarnos a mejorar la calidad de nuestros servicios.</h5>
      </div>
      <div>
        <form id="form_encuesta" method="post">
          <div class="container-fluid" style="padding: 5px 80px 20px; border: 1px solid #ddd">
            <div class="form-group">
              <h4>El servicio <?php echo $tipo_solicitud ?> fue útil y agradable para la clase (donde 1 es "No, en lo absoluto" y 5 es "Si, Completamente")</h4>
              <div class="radio" style="padding-bottom: 30px;">
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="utilidad" value="1"> 1
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="utilidad" value="2"> 2
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="utilidad" value="3"> 3
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="utilidad" value="4"> 4
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="utilidad" value="5"> 5
                </label>
                <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
              </div>
            </div>
            <div class="form-group">
              <h4>El servicio <?php echo $tipo_solicitud ?> fue puntual (donde 1 es "No, en lo absoluto" y 5 es "Si, Completamente")</h4>
              <div class="radio" style="padding-bottom: 30px;">
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="puntualidad" value="1"> 1
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="puntualidad" value="2"> 2
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="puntualidad" value="3"> 3
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="puntualidad" value="4"> 4
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="puntualidad" value="5"> 5
                </label>
                <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
              </div>
            </div>
            <div class="form-group">
              <h4>El auxiliar que presto el servicio fue atento y educado (donde 1 es "No, en lo absoluto" y 5 es "Si, Completamente")</h4>
              <div class="radio" style="padding-bottom: 30px;">
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="auxiliar" value="1"> 1
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="auxiliar" value="2"> 2
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="auxiliar" value="3"> 3
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="auxiliar" value="4"> 4
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="auxiliar" value="5"> 5
                </label>
                <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
              </div>
            </div>
            <div class="form-group">
              <h4>¿Qué probabilidades hay de que nos recomiendes a un amigo o compañero? (donde 1 es "Nada probable" y 5 es "Muy probable")</h4>
              <div class="radio" style="padding-bottom: 30px;">
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="recomendacion" value="1"> 1
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="recomendacion" value="2"> 2
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="recomendacion" value="3"> 3
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="recomendacion" value="4"> 4
                </label>
                <label class="radio-inline" style="margin-right: 50px;">
                  <input type="radio" name="recomendacion" value="5"> 5
                </label>
                <hr style="height:1px;border:none;color:#ddd;background-color:#ddd;">
              </div>
            </div>
            <div class="container-fluid">
              <button type="submit" class="btn btn-primary btn-lg btn-block">Enviar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function () {
      listar_programas();
      $("#form_encuesta").submit(() => {
        guardar_encuesta(<?php echo json_encode($codigo)?>);
        return false;
	    });
    });
  </script>
<?php  }else{?>
  <form action="<?php echo base_url();?>index.php/biblioteca/libros_a_tu_clase/encuesta" method='post' id='form_logear'>
    <div class="modal fade" id="modal_logear" role="dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-sign-in"></span> Hola </h3>
          </div>
          <div class="modal-body">
            <div>
              <h4 class='text-center'>Para realizar la encuesta solo ingresa tu usuario y contraseña institucional</h4>
              <div class="form-group">
                <input type="text" name='usuario' class='form-control' placeholder='Ingrese Usuario'>
              </div>
              <div class="form-group">
                <input type="password" name='contrasena' class='form-control' placeholder='Ingrese Contraseña'>
              </div>
              <input type="text" name='id_solicitud' class='form-control oculto sin_focus'>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" id="btn_login_encuesta" class="btn btn-danger active"><span class="fa fa-check"></span> Ingresar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="tablausu col-md-12" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div> 
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-list"></span> Biblioteca</h3>
        <div class="row">
          <div class="panel-body text-center">
            <div class=" "><img src="<?php echo base_url(); ?>/imagenes/fondo_libros.png" style="width: 300px"></div>
            <h3>Estamos trabajando para ofrecerte un servicio con calidad.</h3> <br> 
          </div>  
        </div>
    </div>
  </div>
</div>
  <script type="text/javascript">
    $(document).ready(function () {
      <?php if(!empty($id)){ ?> logear_biblioteca(<?php echo json_encode($id)?>)
      <?php }else{?>	MensajeConClase('No existe una encuesta asociada a este enlace.', 'info', 'Oops.!');<?php }?>
      <?php if(!empty($mensaje)){?>
        <?php if( $mensaje == 3){?>MensajeConClase('El usuario y/o contraseña son invalidos.', 'info', 'Oops.!');<?php }?>
        <?php if( $mensaje == 2){?>MensajeConClase('Usted no se encuentra asociado a esta encuesta', 'info', 'Oops.!');<?php }?>
        <?php if( $mensaje == 1){?>MensajeConClase('Usted ya ha realizado esta encuesta anteriormente', 'info', 'Oops.!');<?php }?>
      <?php }?>
      <?php if(isset($success)){?>	MensajeConClase('Gracias por realizar la encuesta de satisfacción.', 'success', 'Encuesta Enviada!');<?php }?>
    });
  </script>
<?php }?>
</body>
</html>
