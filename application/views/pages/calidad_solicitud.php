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
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Calidad.js"></script>
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
  <?php if($solicitud && ($solicitud->{'id_estado'} == 'Est_Cal_Asig' || $solicitud->{'id_estado'} == 'Est_Cal_Conf')) { ?>
    <div class="container-fluid">
      <div class="container-fluid" style="padding: 30px; height: 100%; width: 100%; ">
        <div class="panel panel-default container" style="padding: 40px; padding-top: 0px; box-shadow: 10px 10px 10px rgba(0,0,0,.05); background-color: #fafafa;">
          <div class="row">
            <div class="col">
                <div class="container-fluid panel-heading">
                  <div class="row" style="padding: 19px 8px;margin-bottom: -27px;text-align: center;color: #6e1f7c;margin-top: 10px;margin-right: 0px;margin-left: 2px;">
                    <div class="col-md-3 img-responsive center-block""><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" width="80%" class="img-responsive" style="display: flex; margin: auto;"></div>
                    <div class="col-md-6">
                      <h1 style="font-weight: 800;">CALIDAD</h1><p style="font-size:20px;font-weight: 100;">ETIQUETA DEL RESIDUO</p>
                    </div>
                    <div class="col-md-3"><img src="<?php echo base_url(); ?>/imagenes/logo_agil.png" width="80%" class="img-responsive" style="display: flex; margin: auto;margin-top: 22px;"></div>
                  </div>
                </div>
              <div class="panel panel-default">
                <div class="table-responsive" >
                  <table class="table table-bordered table-condensed">   
                    <tr>
                      <th class="nombre_tabla" colspan="6">Información de la Solicitud</th>
                    </tr>   
                    <tr>
                      <td class="ttitulo" colspan="2">Solicitante</td>
                      <td class="solicitante" colspan="6"></td> 
                    </tr> 
                    <tr>
                      <td class="ttitulo" colspan="2">Fecha de registro</td>
                      <td class="fecha_registro" colspan="6"></td>
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Estado</td>
                      <td class="estado" colspan="6"></td>
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Estado del residuo</td>
                      <td class="residuo_estado" colspan="2"></td> 
                      <td class="ttitulo" colspan="2">Presentación del residuo</td>
                      <td class="presentacion" colspan="2"></td>
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Cantidad</td>
                      <td class="cantidad" colspan="6"></td> 
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Ubicación</td>
                      <td class="ubicacion" colspan="6"></td>
                    </tr>
                    <tr id="cont_activo">
                      <td class="ttitulo" colspan="2">Activo</td>
                      <td class="carta_activo" colspan="6"></td>
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Descripción</td>
                      <td class="descripcion" colspan="6"></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div> 
          </div>

          <div class="row">
            <div class="col">
              <div class="panel panel-default">
                <div class="table-responsive" id="tabla_asignacion">
                  <table class="table table-bordered table-condensed">   
                    <tr>
                      <th class="nombre_tabla" colspan="8">Asignación</th>
                    </tr>
                    <tr>
                      <td class="ttitulo" colspan="2">Auxiliar</td>
                      <td class="auxiliar" colspan="6"></td> 
                    </tr> 
                    <tr>
                      <td class="ttitulo" colspan="2">Fecha de Asignación</td>
                      <td class="fecha_asignacion" colspan="6"></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div> 
          </div>

          <div class="row">
            <?php if($solicitud->{'id_estado'} == 'Est_Cal_Asig') { ?>
              <div class="col-md-6">
                <button type="button" class="btn btn-primary btn-lg btn-block" id="btn_confirmar">Confirmar <span class="fa fa-thumbs-up"></span></button>
              </div>
              <div class="col-md-6">
                <button type="button" class="btn btn-lg btn-block" id="btn_negar" style="background-color: #d9534f; color: white">Negar <span class="fa fa-thumbs-down"></span></button>
              </div>
            <?php } else { ?>
              <div class="col">
                <button type="button" class="btn btn-success btn-lg btn-block" disabled><span class="fa fa-check"></span></button>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    
  <?php } else { ?>
    <div class="tablausu col-md-12" id="menu_principal" style="background-image: url(<?php echo base_url(); ?>/imagenes/LogocucF.png)">
      <div class="content-menu" style="background-image: url(<?php echo base_url(); ?>/imagenes/logo_agil.png)"></div>
      <div id="container-principal2" class="container-principal-alt">
        <h3 class="titulo_menu"><span class="fa fa-list"></span> Calidad</h3>
        <div class="row">
          <div class="panel-body text-center">
            <div class=" "><img src="<?php echo base_url(); ?>/imagenes/no_found.png" style="width: 300px"></div>
            <h3>No encontramos una solicitud pendiente para este enlace, por favor verifique.</h3><br>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <script>
    $(document).ready(function (){
      $("#cont_activo").hide();

      $(".solicitante").html(<?php echo json_encode($solicitud->{'solicitante'}) ?>);
      $(".fecha_registro").html(<?php echo json_encode($solicitud->{'fecha_registra'}) ?>);
      $(".estado").html(<?php echo json_encode($solicitud->{'estado_solicitud'}) ?>);
      $(".residuo_estado").html(<?php echo json_encode($solicitud->{'residuo_estado'}) ?>);
      $(".presentacion").html(<?php echo json_encode($solicitud->{'presentacion'}) ?>);
      $(".cantidad").html(<?php echo json_encode($solicitud->{'cantidad'}) ?> + ' ' + <?php echo json_encode($solicitud->{'tipo_cantidad'}) ?>);
      $(".carta_activo").html(`<a target='_blank' href="${Traer_Server()}archivos_adjuntos/calidad/` +<?php echo json_encode($solicitud->{'carta_activo'}) ?> + `">Carta formato activo</a>`);
      $(".descripcion").html(<?php echo json_encode($solicitud->{'descripcion'}) ?>)
      $(".auxiliar").html(<?php echo json_encode($solicitud->{'auxiliar'}) ?>);
      $(".fecha_asignacion").html(<?php echo json_encode($solicitud->{'fecha_asignacion'}) ?>);
      $(".ubicacion").html(<?php echo json_encode($solicitud->{'ubicacion_bloque'}); ?> + "-" + <?php echo json_encode($solicitud->{'ubicacion_salon'}); ?>);

      if(<?php echo json_encode($solicitud->{'activo'}) ?> == 1) $("#cont_activo").show();

      if(<?php echo json_encode($solicitud->{'id_estado'}) ?> != 'Est_Cal_Asig' && <?php echo json_encode($solicitud->{'id_estado'}) ?> != 'Est_Cal_Conf') MensajeConClase('Esta solicitud no ha sido asignada o ya fue confirmada, por favor verifique.', 'info', 'Oops!');

      $("#btn_confirmar").click(() => {
        gestionar_solicitud(<?php echo json_encode($solicitud->{'id'}) ?>, 'Est_Cal_Rec', 1);
      });

      $("#btn_negar").click(() => {
        gestionar_solicitud(<?php echo json_encode($solicitud->{'id'}) ?>, 'Est_Cal_Neg', 1);
      })
    })
  </script>
</body>
</html>
