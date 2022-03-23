<?php  session_destroy(); ?>
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

<div class="container col-md-12 " id="inicio-user">
    <div class="tablausu col-md-12 text-left" id="">
        <div class="table-responsive col-sm-12 col-md-12">
            <!-- <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p> -->
            <table class="table table-bordered table-hover table-condensed" id="tabla_estudiantes_solicitud"  cellspacing="0" width="100%">
                <div class="row" style="background-color:#fff;background-color: #fff;padding: 19px 8px;margin-bottom: -27px;text-align: center;color: #6e1f7c;margin-top: 10px;margin-right: 0px;margin-left: 2px;">
                  <div class="col-md-3 img-responsive center-block""><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" width="80%" class="img-responsive" style="display: flex;
    margin: auto;"></div>
                  <div class="col-md-6">
                    <h1 style="font-weight: 800;">ASISTENCIA</h1><p style="font-size:20px;font-weight: 100;">BIENESTAR A TU CLASE</p>
                  </div>
                  <div class="col-md-3"><img src="<?php echo base_url(); ?>/imagenes/logo_agil.png" width="80%" class="img-responsive" style="display: flex;
    margin: auto;margin-top: 22px;"></div>

                </div>
                
                <thead class="ttitulo ">
                    <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE ESTUDIANTES</td>
                    <td class="sin-borde text-right border-left-none" colspan="6" >
                        <span  class="btn btn-default btnAgregar oculto" id="agregar_estudiantes_nuevos">
                        <span class="fa fa-plus red"></span> Agregar Estudiante</span> </tr>
                    <tr class="filaprincipal ">
                        <td class="opciones_tbl">No.</td>
                        <td>Nombre Completo</td>
                        <td>Identificación</td>
                        <td class="opciones_tbl_btn">Acción</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<form id="form_logear" method='post' id='form_logear'>
  <div class="modal fade" id="modal_logear" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-sign-in"></span> Registro de asistencia <span id='nombre'></span></h3>
      </div>
      <div class="modal-body" style="padding: 60px;">
        <div>
        <h4 class='text-center'>Para validar tu datos solo ingresa tu contraseña institucional</h4>
        <input type="password" name='password' class='form-control' placeholder='Ingrese Contraseña'>
      </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span> Ingresar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
</form>

  <script>
    $(document).ready(function () {
        listar_estudiantes_solicitud(<?php echo $id_solicitud?>,'si');
    });
  </script>
</body>
</html>
