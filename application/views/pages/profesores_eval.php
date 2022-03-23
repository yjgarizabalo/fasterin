    <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css"> -->
    <?php 
    $administra = $_SESSION["perfil"] == "Per_Admin" ? true : false;
    ?>

    <style>
  #seleccion_tipo .thumbnail img {
      height: 90px;
      width: 90px;
      margin-top: 30px;
  }

  #seleccion_tipo .thumbnail {
      height: 180px;
      width: 150px;
      padding: 0;
      margin-left: 30px;
      float: left;
      /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e5e5e5+0,e5e5e5+99,ffffff+100&1+0,0+18,0+47,0+79,1+98,0+100 */
      background: -moz-linear-gradient(top, rgba(229, 229, 229, 1) 0%, rgba(229, 229, 229, 0) 18%, rgba(229, 229, 229, 0) 47%, rgba(229, 229, 229, 0) 79%, rgba(229, 229, 229, 1) 98%, rgba(229, 229, 229, 0.5) 99%, rgba(255, 255, 255, 0) 100%);
      /* FF3.6-15 */
      background: -webkit-linear-gradient(top, rgba(229, 229, 229, 1) 0%, rgba(229, 229, 229, 0) 18%, rgba(229, 229, 229, 0) 47%, rgba(229, 229, 229, 0) 79%, rgba(229, 229, 229, 1) 98%, rgba(229, 229, 229, 0.5) 99%, rgba(255, 255, 255, 0) 100%);
      /* Chrome10-25,Safari5.1-6 */
      background: linear-gradient(to bottom, rgba(229, 229, 229, 1) 0%, rgba(229, 229, 229, 0) 18%, rgba(229, 229, 229, 0) 47%, rgba(229, 229, 229, 0) 79%, rgba(229, 229, 229, 1) 98%, rgba(229, 229, 229, 0.5) 99%, rgba(255, 255, 255, 0) 100%);
      /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
      filter: progid: DXImageTransform.Microsoft.gradient(startColorstr='#e5e5e5', endColorstr='#00ffffff', GradientType=0);
      /* IE6-9 */
      border: 1px solid #ccc;
      border-radius: 5%;
  }

  #seleccion_tipo .thumbnail span {
      font-style: normal;
      text-transform: uppercase;
      margin-top: 10px;
      height: 20px;
      padding: 1px !important;
      background-color: #6e1f7c;
      background-size: 100%;
      border: #6e1f7c;
      color: white;
      font-family: cucinicio;
  }

  #seleccion_tipo .thumbnail:hover .btn {
      background-color: #d57e1c !important;
      border-color: #d57e1c !important;
  }

  .fonts-italic {
      font-style: italic;
  }

  .tabla__desempeno {
      width: 100%;
      font-size: 20px;
      text-align: center;

  }

  .tabla__desempeno tr td {
      padding: 5px;
      border: 1px solid white;
  }

  .tabla__des__title {
      background-color: #a5a5a5;
      color: white;
      text-align: center;
  }

  .tabla__des__title__description {
      background-color: #bfbfbf;
      color: white;
      border: 3px solid white;
  }

  .tabla__des__valor {
      background-color: #e1e1e1;
      color: black;
  }

  .tabla__desempeno tr th {
      padding: 10px;
      text-align: center;
  }

  .tabla__articulos {
      margin-bottom: 20px;
  }

  .tabla__articulos tr th {
      text-align: center;
      padding: 5px;
  }

  .tabla__plan tr th {
      text-align: center;
      padding: 5px;
      background-color: #a5a5a5;
      border: 2px solid white;
      color: white;
  }

  .tabla__plan tr td {
      text-align: center;
      padding: 5px;
      font-size: 20px;
      background-color: #e1e1e1;
      color: black;
  }

  .tabla__metas tr th {
      background-color: #a5a5a5;
      color: white;
      text-align: center;
      padding: 5px;
  }

  .tabla__metas tr td {
      padding: 5px;
      background-color: #e1e1e1;
  }

  .container-global {
      padding: 30px 30px 0 30px;
  }

  .container_cat {
      display: grid;
      grid-template-columns: repeat(3, 33%);
      border-radius: 0 0 25px 25px;
      border-bottom: 2px solid #f8ebfa;
  }

  .container_ind {
      display: grid;
      grid-template-columns: repeat(4, 25%);
      border-bottom: 2px solid #f8ebfa;
      border-radius: 0 0 25px 25px;
  }

  .container_meta {
      display: grid;
      grid-template-columns: repeat(2, 50%);
      border-bottom: 2px solid #f8ebfa;
      border-radius: 0 0 25px 25px;
  }

  .container_tipo {
      display: grid;
      grid-template-columns: repeat(3, 30%);
      border-bottom: 2px solid #f8ebfa;
      border-radius: 0 0 25px 25px;
  }

  .titulos {
      font-size: 18px;
      font-family: helvetica;
      font-weight: bold;
  }

  .descripcion {
      display: grid;
      font-size: 12px;
      font-family: helvetica;
  }

  .categoria_al {
      justify-self: center;
      text-align: center;
  }

  .descripcion_meta {
      display: grid;
      font-size: 12px;
      font-family: helvetica;
      justify-self: center;
      text-align: center;
      padding: 0 30px 0 30px;
  }

  .configuracion_seccion {
      font-size: 20px;
      padding-left: 10px;
      border-left: 5px solid #6e1f7c;
      cursor: pointer;
      border-radius: 5px;
  }
    </style>

    <div class="container col-md-12 " id="inicio-user">
        <div class="tablausu col-md-12 text-left <?php echo $administra || $id >0 ?'':'oculto'; ?>"
            id="container_solicitudes">
            <div class="table-responsive">
                <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
                <table class="table table-bordered table-hover table-condensed"
                    id="tabla_listado_solicitudes_profesores_evaluacion" cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                        <tr class="">
                            <td colspan="2" class="nombre_tabla">TABLA SOLICITUDES <br><span
                                    class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene
                                    algunos filtros
                                    aplicados.</span></td>
                            <td class="sin-borde text-right border-left-none" colspan="7">
                                <?php if($administra){?>
                                <span class="black-color pointer btn btn-default" id="btn_administrar"><span
                                        class="fa fa-cogs red"></span> Administrar</span>
                                <?php } ?>
                                <span class="btn btn-default" data-toggle="modal" id="filtrar_solicitudes"> <span
                                        class="fa fa-filter red"></span> Filtrar</span>
                                <span class="btn btn-default" id="btn_limpiar_filtros"><span
                                        class="fa fa-refresh red"></span> Limpiar</span>
                            </td>
                        </tr>
                        <tr class="filaprincipal">
                            <td class="opciones_tbl">Ver</td>
                            <td>Persona</td>
                            <td>Fecha registro</td>
                            <td>Estado</td>
                            <td class="" style="width:150px">Acciones</td>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tablausu col-md-12 <?php echo $administra  || $id >0 ?'oculto':''; ?>" id="menu_principal"
            style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
            <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
            </div>
            <div id="container-principal2" class="container-principal-alt">
                <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
                <div class="row">
                    <div id="listado_solicitudes">
                        <div class="thumbnail ">
                            <div class="caption">
                                <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
                                <span class="btn  form-control btn-Efecto-men">Mis Solicitudes</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span
                        class="fa fa-reply-all naraja"></span>
                    Regresar</p>
            </div>
        </div>
    </div>

    <!-- modal con formulario de la solicitud para asesorias -->
    <div class="modal fade scroll-modal" id="modal_abrir_plan_profesores" role="dialog">
        <div class="modal-dialog modal-lg modal-95">
            <!-- Modal content-->
            <div class="modal-content">
                <!-- <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> EVALUACIÓN<span id=""></span>
            </h3>
          </div> -->
                <div class="modal-body" id="bodymodal">
                    <div class="container-fluid" style="padding: 0px !important;">
                        <div class="col-md-12" style="padding: 0px !important;">
                            <div class="col-md-3" style="background-color: #ccc;">
                                <div class="" style="font-size: 20px; text-align: center; margin: 45px; 0 95px 0;">
                                    <img src="<?php echo base_url() ?>/imagenes/User.png" alt="Images" width="100"
                                        height="200" style="padding: 15px 0 10px 0;">
                                    <br><br>
                                    <p>ID: 1545024685</p>
                                    <img src="" alt="Pais">
                                    <p><strong>CONTRATACIÓN</strong></p>
                                    <p>TIEMPO COMPLETO</p>
                                    <p>ASISTENTE 1</p>
                                    <p>FIJO</p>
                                    <br>
                                    <p>INICIO: 2020/01/01</p>
                                    <p>FIN: 2021/01/01</p>
                                    <br>
                                    <p><strong>FORMACIÓN</strong></p>
                                    <p>PENDIENTE</p>
                                    <br>
                                    <p>GRADO: PREGRADO</p>
                                    <p>INGLES: -</p>
                                    <br>
                                    <p>PERFIL: GESTIÓN UNIVERSITARIA</p>
                                    <p>ROL(ES): PROFESOR EN ENCARGO: VICERRECTORÍA DE EXTENSIÓN</p>
                                    <br>
                                    <p>GRUPO: N/A</p>
                                    <p>LÍNEA: N/A</p>
                                    <br>
                                    <p>CATEGORIA:</p>
                                    <p>CITAS:</p>
                                    <p>INDICE H:</p>
                                </div>
                            </div>
                            <div class="col-md-9" style="padding-right: 0px !important;">
                                <div class="col-md-12" style="background-color: #ffc301; margin-bottom: 10px;">
                                    <div class="col-md-12">
                                        <p style="font-size: 20px; text-align: center; padding-top: 10px;">CIENCIAS
                                            EMPRESARIAL - ADMINISTRACION DE EMPRESAS</p>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-8" style="padding: 0px !important;">
                                            <p class="nombre-completo-profesor" style="font-size: 40px; color: white; ">
                                            </p>
                                            <p style="font-size: 20px;"> <span> AREA: </span> <span>ADMINISTRACIÓN Y
                                                    ORGANIZACIÓN </span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <img src="<?php echo base_url() ?>/imagenes/logo.png" alt="UNIVERSIDAD"
                                                width="250" height="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"
                                    style="background-color: #f2f2f2; margin-bottom: 10px; padding: 0px !important;">
                                    <div class="col-md-12" style="margin: 15px 0 15px 0;">
                                        <div class="col-md-12">
                                            <p style="font-size: 20px; font-weight: bold;" class="cbxeva_desemp"></p>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px !important;">
                                            <div class="col-md-6">
                                                <canvas id="myChart" width="1" height="1"></canvas>
                                            </div>
                                            <div class="col-md-6 ">
                                                <p style="font-size: 20px; font-weight: bold; text-align: center;">
                                                    PORCENTAJES</p>
                                                <canvas id="myChart2" width="100" height="100"></canvas>
                                            </div>
                                            <div class="col-md-12 alert alert-info" role="alert"
                                                style="margin-top: 30px !important;">
                                                <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                                                <p id="cart1" class="text-justify"><b>Si desea ver el detalle de la
                                                        evaluación de desempeño haga <span id="clickaqui"
                                                            style="color: red; cursor: pointer;">click aquí</span></b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"
                                    style="background-color: #f2f2f2; margin-bottom: 10px; padding: 0px !important;">
                                    <div class="col-md-4" style="text-align: center; margin-top: 10px;">
                                        <div class="col-md-12 alert alert-info" role="alert">
                                            <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                                            <p><strong>Rango de la evaluacion de desempeño</strong></p>
                                            <p> <span style="color: blue; font-weight: bold;">SOBRESALIENTE</span> &#62;
                                                4,5 </p>
                                            <p> 4 <span>&#8804;</span> <span
                                                    style="color: green; font-weight: bold;">BUENO</span>
                                                <span>&#8804;</span> 4,5 </p>
                                            <p> 3,75 <span>&#8804;</span> <span
                                                    style="color: orange; font-weight: bold;">ACEPTABLE</span> &#60; 4
                                            </p>
                                            <p> <span style="color: red; font-weight: bold;">DEFICIENTE</span> &#60; 3,75
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="text-align: center; margin-top: 50px;">
                                        <p style="font-size: 20px; font-weight: bold;">EVALUACIÓN DESEMPEÑO</p>
                                        <p style="font-size: 30px;"><span class="nota_cualitativa"></span></p>
                                        <p style="font-size: 30px; font-weight: bold; margin-top: -15px;"><span
                                                class="nota_cuantitativa"></span></p>
                                    </div>
                                    <div class="col-md-4" style="text-align: center; margin-top: 30px;">
                                        <p style="font-size: 20px; font-weight: bold;">EVALUACIÓN ESTUDIANTE</p>
                                        <p style="font-size: 70px; color: black;"><span class="nota_cuantitativa"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-12"
                                    style="background-color: #f2f2f2; margin-bottom: 10px; padding: 0px !important;">
                                    <div class="col-md-12">
                                        <p style="font-size: 20px; font-weight: bold; margin-top: 10px;"
                                            class="cbxlogros"></p>
                                    </div>
                                    <div class="col-md-12" style="font-size: 20px; text-align: center;">
                                        <div class="col-md-4">
                                            <span style="color: #4f8df5; font-size: 30px; font-weight: bold;">0</span>
                                            <p>DTI</p>
                                        </div>
                                        <div class="col-md-4">
                                            <span style="color: #80816c; font-size: 30px; font-weight: bold;">0</span>
                                            <p>ASC</p>
                                        </div>
                                        <div class="col-md-4">
                                            <span style="color: #2d9662; font-size: 30px; font-weight: bold;">0</span>
                                            <p>SUPERAVIT</p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table style="width: 100%" class="tabla__articulos">
                                            <tr class="tabla__des__title">
                                                <th>ARTICULOS</th>
                                                <th>Q4</th>
                                                <th>Q3</th>
                                                <th>Q2</th>
                                                <th>Q1</th>
                                            </tr>
                                            <tr>
                                                <td class="tabla__des__valor">ESTRUCTURA</td>
                                                <td>2</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla__des__valor">POSTULADO</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla__des__valor">ACEPTADA</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr>
                                                <td class="tabla__des__valor">PUBLICADO</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12"
                                    style="background-color: #dcdcdc; padding: 10px 0 0 0 !important; ">
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <div class="col-md-12">
                                            <p style="font-size: 20px; font-weight: bold;" class="cbxplanTrab">2</p>
                                        </div>
                                        <div class="col-md-12 margin1">
                                            <div class="alert alert-info" role="alert">
                                                <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                                                <p id="cart1" class="text-justify"><b>La información correspondiente al
                                                        plan de trabajo podrá ser modificada y lo oficial está en el
                                                        módulo de plan de trabajo</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table style="width: 100%" class="tabla__plan">
                                                <tr>
                                                    <th>DO</th>
                                                    <th>PAE</th>
                                                    <th>DI</th>
                                                    <th>INV</th>
                                                    <th>ROL INV</th>
                                                    <th>EXT</th>
                                                    <th>GEST U</th>
                                                    <th>TOTAL</th>
                                                </tr>
                                                <tr>
                                                    <td>100</td>
                                                    <td>40</td>
                                                    <td>54</td>
                                                    <td>100</td>
                                                    <td>115</td>
                                                    <td>15</td>
                                                    <td>106</td>
                                                    <td>789</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-12" style="padding-top: 20px; font-size: 20px;">
                                            <p>OBSERVACIONES</p>
                                            <p style="background-color: #bfbfbf; padding: 5px;">ACTIVO</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <p style="font-size: 20px; font-weight: bold;">METAS</p>
                                        </div>
                                        <div class="col-md-12" style="font-size: 20px; text-align: center;">
                                            <div class="col-md-4">
                                                <span style="color: #4f8df5; font-size: 30px; font-weight: bold;">0</span>
                                                <p>DTI</p>
                                            </div>
                                            <div class="col-md-4">
                                                <span style="color: #80816c; font-size: 30px; font-weight: bold;">0</span>
                                                <p>ASC</p>
                                            </div>
                                            <div class="col-md-4">
                                                <span style="color: #2d9662; font-size: 30px; font-weight: bold;">0</span>
                                                <p>SUPERAVIT</p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table style="width: 100%" class="tabla__metas">
                                                <tr>
                                                    <th>ARTÍCULOS</th>
                                                    <th>PUBLICADO</th>
                                                </tr>
                                                <tr>
                                                    <td>Q1</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td>Q2</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td>Q3</td>
                                                    <td>0</td>
                                                </tr>
                                                <tr>
                                                    <td>Q4</td>
                                                    <td>0</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <a class="btn btn-default" id="btn-descargar_plan_profesores">
                        <span class="fa fa-download red"></span> Descargar
                    </a>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_detalle_evaluacion" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-tasks"></span> Detalle</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_evaluacion"
                            cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                                <tr>
                                    <th class="nombre_tabla" colspan="6">Tabla detalle</th>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Ver</td>
                                    <td>Categoria</td>
                                    <td>Indicador</td>
                                    <td>Tipo</td>
                                    <td>Nota del indicador</td>
                                    <td>Nota cualitativa del indicador</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_detalle_indicadores_evaluacion" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <table class="table table-responsive table-condensed table-bordered"
                            id="detalle_indicadores_evaluacion">
                            <div class="container-global">
                                <div id="informacion_categoria" class="configuracion_seccion">
                                    Información de la categoría
                                    <span style="font-weight: 500; background-color: #6e1f7c" class="badge pointer">
                                        <span class="fa fa-eye"> Mostrar</span>
                                    </span>
                                </div>
                                <br>
                                <div class="container_cat">
                                    <div class="categoria_al">
                                        <p class="titulos">Categoría</p>
                                        <p class="data_categoria descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">% de la categoría</p>
                                        <p class="data_porcentaje descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Calificación de la Categoría</p>
                                        <p class="data_calificacion_cat descripcion"></p>
                                    </div>
                                </div>
                                <br>
                                <div id="informacion_indicador" class="configuracion_seccion">
                                    Información del indicador
                                    <span style="font-weight: 500; background-color: #6e1f7c" class="badge pointer">
                                        <span class="fa fa-eye"> Mostrar</span>
                                    </span>
                                </div>
                                <br>
                                <div class="container_ind">
                                    <div class="categoria_al">
                                        <p class="titulos">Indicador</p>
                                        <p class="data_indicador descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">% del indicador</p>
                                        <p class="data_peso_ind descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Nota del indicador</p>
                                        <p class="data_nota_cuan descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Nota cualitativa</p>
                                        <p class="data_nota_cual descripcion"></p>
                                    </div>
                                </div>
                                <br>
                                <div id="informacion_meta" class="configuracion_seccion">
                                    Información de la meta, logro y nota media
                                    <span style="font-weight: 500; background-color: #6e1f7c" class="badge pointer">
                                        <span class="fa fa-eye"> Mostrar</span>
                                    </span>
                                </div>
                                <br>
                                <div class="container_meta">
                                    <div class="categoria_al">
                                        <p class="titulos">Meta</p>
                                        <p class="data_meta descripcion_meta"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Logro</p>
                                        <p class="data_logro descripcion"></p>
                                    </div>
                                    <!-- <div class="categoria_al">
                      <p class="titulos">Valor medio</p>
                      <p class="data_valor descripcion"></p>
                    </div> -->
                                </div>
                                <br>
                                <div id="informacion_tipo" class="configuracion_seccion">
                                    Información del tipo y fechas.
                                    <span style="font-weight: 500; background-color: #6e1f7c" class="badge pointer">
                                        <span class="fa fa-eye"> Mostrar</span>
                                    </span>
                                </div>
                                <br>
                                <div class="container_tipo">
                                    <div class="categoria_al">
                                        <p class="titulos">Tipo</p>
                                        <p class="data_tipo descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Fecha inicio</p>
                                        <p class="data_fecha_inicio descripcion"></p>
                                    </div>
                                    <div class="categoria_al">
                                        <p class="titulos">Fecha fin</p>
                                        <p class="data_fecha_fin descripcion"></p>
                                    </div>
                                </div>
                            </div>
                        </table>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <form id="form_filtros" method="post">
        <div class="modal fade" id="modal_crear_filtros" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <select name="id_estado" class="form-control cbxestado" id="id_estado">
                                <option value="">Filtrar por Estado</option>
                            </select>

                            <div class="agro agrupado">
                                <div class="input-group">
                                    <span class="input-group-addon" style='background-color:white'><span
                                            class='fa fa-calendar red'></span> Desde</span>
                                    <input class="form-control sin_margin" value="" type="date" name="fecha_inicial"
                                        id="fecha_inicial">
                                </div>
                            </div>
                            <div class="agro agrupado">
                                <div class="input-group">
                                    <span class="input-group-addon" style='	background-color:white'><span
                                            class='fa fa-calendar red'></span> Hasta</span>
                                      <input class="form-control sin_margin" value="" type="date" name="fecha_final"
                                        id="fecha_final">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active" id="btn_filtrar"><span
                                class="glyphicon glyphicon-ok"></span> Generar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
  $(document).ready(function() {
      listar_solicitudes()
  });
    </script>

    <script>
  let startDateBlock = new Date();
  $(".form_datetime_block").datetimepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      startDateBlock,
      maxView: 4,
      minView: 2,
      daysOfWeekDisabled: [0],
  });
    </script>
    <script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>