<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php 
$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Lab"  ? true : false;
$notifica = $administra || $_SESSION["perfil"] == 'Per_Aux_Lab' ? true : false;
$gestiona = $notifica || $_SESSION["perfil"] == 'Per_Ase_Lab'  ? true : false;
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

  .fonts-italic{
    font-style: italic;
  }
</style>

<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left <?php echo $gestiona || $id >0 ?'':'oculto'; ?>" id="container_solicitudes">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed" id="tabla_listado_solicitudes_bienestar_laboral"
        cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="2" class="nombre_tabla">TABLA SOLICITUDES <br><span
                class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros
                aplicados.</span></td>
            <td class="sin-borde text-right border-left-none" colspan="7">
                <?php if ($notifica) echo '<span class="btn btn-default" id="ver_notificaciones"><span class="badge btn-danger n_notificaciones"></span> Notificaciones</span>';?>
                <?php if($administra){?>
                  <span class="black-color pointer btn btn-default" id="btn_administrar"><span class="fa fa-cogs red" ></span> Administrar</span>
                <?php } ?> 
              <span class="btn btn-default" data-toggle="modal" id="btn_filtrar"> <span class="fa fa-filter red"></span> Filtrar</span> 
              <span class="btn btn-default" id="btn_limpiar_filtros"><span class="fa fa-refresh red"></span> Limpiar</span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo</td>
            <td>Solicitante</td>
            <td>Fecha de registro</td>
            <td>Estado </td>
            <td class="" style="width:150px">Acciones</td>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tablausu col-md-12 <?php echo $gestiona  || $id >0 ?'oculto':''; ?>" id="menu_principal"
    style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">

        <div id="btn_seguridad_trabajo">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/seguridad_trabajo.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Seguridad y Salud</span>
            </div>
          </div>
        </div>

        <div id="btn_asesorias">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/asesorias.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Asesorías</span>
            </div>
          </div>
        </div>

        <div id="listado_solicitudes">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Mis Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

<!-- modal con formulario de la solicitud para seguridad y salued en el trabajo -->
<div class="modal fade scroll-modal" id="modal_agregar_solicitud" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_solicitud" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva solicitud<span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
            <div class="row">
                <select class="form-control cbx_lugar" name="id_lugar" id="id_lugar" required>
                  <option value="" selected disabled >Seleccione</option>
                </select>
                <select class="form-control cbx_ubicacion" name="id_ubicacion" id="id_ubicacion" required>
                  <option value="" selected disabled >Seleccione</option>
                </select>
                <textarea type="text" id="descripcion" name="descripcion" rows="5" class="form-control" placeholder="Descripción" required></textarea>
                <br>
                <div class="btn-group btn-group-justified" role="group" aria-label="..." id="adjs">
                    <div class="btn-group" role="group">
                    <button id="agregar_archivos" type="button" class="btn btn-default active btn-block"><span class="glyphicon glyphicon-plus-sign red"></span> Agregar Imagenes</button>
                </div>
            </div>
        </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- modal con submódulos del módulo asesorias  -->
<div class="modal fade" id="modal_asesorias_modulos" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-plus-circle"></span> Asesorías</h3>
      </div>
      <div class="modal-body">
        <div id="seleccion_tipo">
          <div class="row" style="width: 100%;">
            <div class="col-6 col-sm-4 col-md-3 text-center pointer">
              <div class="thumbnail" id="btn_asesoria_financiera">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/asesoria_financiera.png" alt="...">
                  <span class = "btn form-control">FINANCIERA</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center pointer">
              <div class="thumbnail" id="btn_asesoria_vivienda">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/asesoria_vivienda.png" alt="...">
                  <span class = "btn form-control">VIVIENDA</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center pointer">
              <div class="thumbnail" id="btn_asesoria_psicologica">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/asesoria_psicologica.png" alt="...">
                  <span class = "btn form-control">PSICOLOGICA</span>
                </div>
              </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 text-center pointer">
              <div class="thumbnail" id="btn_asesoria_juridica">
                <div class="caption text-center">
                  <img src="<?php echo base_url() ?>/imagenes/asesoria_juridica.png" alt="...">
                  <span class = "btn form-control">JURIDICA</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal con formulario de la solicitud para asesorias -->
<div class="modal fade scroll-modal" id="modal_agregar_solicitud_asesorias" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_agregar_solicitud_asesorias" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva solicitud<span id=""></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
            <div class="row">
            <?php if($administra){?>
              <div id="solicitante_buscar">
                <div class="input-group agrupado">
                  <input id='txt_solicitantes_buscar' class="form-control con_focus" placeholder="Ingrese identificación o nombre del solicitante">
                  <span class="input-group-btn"><button class="btn btn-default test" id="botonsub" onclick="BuscarSolicitante()"><span class='fa fa-search red'></span> Buscar</button></span>
                </div> 
              </div>
                <div class="table-responsive" id="div_busqueda_solicitante" style="width: 100%" style="display: none">
                  <table class="table table-bordered table-hover table-condensed pointer" id="tabla_solicitantes_busqueda" cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>N°</td>
                        <td>Nombre Completo</td>
                        <td>Identificacion</td>
                        <td class="opciones_tbl_btn">Acción</td>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              <?php }?>
            <div id="div_formulario_solicitud">   
              <div class="form-group" id="bene"></div>
              <div class="form-group" id="input_nombre"></div>
              <input type="hidden" name="id_trabajador_solicitante" id="id_trabajador_solicitante">
              <input type="number" id="numero_contacto" name="numero_contacto" class="form-control" placeholder="Numero de Contacto" >
              <textarea type="text" id="descripcion_asesoria" name="descripcion_asesoria" rows="10" class="form-control comentarios" placeholder="Detalle la asesoría requerida" required></textarea>
              <br>
              <div class="margin1">
                  <div class="alert alert-info" role="alert">
                    <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                    <p id="cart1" class="text-justify"><b></b></p>
                  </div>
              </div>
            </div> 
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- modal con formulario para modificar la solicitud para asesorias -->
<div class="modal fade scroll-modal" id="modal_modificar_solicitud_asesorias" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_modificar_solicitud_asesorias" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Modificar solicitud<span id=""></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
            <div class="row">
            
            <div id="div_formulario_solicitud">   
              <p>En caso de no poder comunicarse, Contactar a :</p>
              <div id="select_beneficiario_md">
              <select class="form-control cbx_beneficiario_md" name="id_beneficiario_md" id="id_beneficiario_md" onchange="SelecBene(1)"></select></div>
              <div class="form-group" id="input_nombre_md"></div>
              <input type="hidden" name="id" id="id">
              <input type="hidden" name="id_trabajador_solicitante_md" id="id_trabajador_solicitante_md">
              <input type="number" id="numero_contacto_md" name="numero_contacto_md" class="form-control" placeholder="Numero de Contacto" >
              <textarea type="text" id="descripcion_asesoria_md" name="descripcion_asesoria_md" rows="10" class="form-control comentarios" placeholder="Detalle la asesoría requerida" required></textarea>
              <br>
              <div class="margin1">
                  <div class="alert alert-info" role="alert">
                    <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
                    <p id="cart1" class="text-justify"><b></b></p>
                  </div>
              </div>
            </div> 
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal para el detalle de las solicitudes del módulo de seguridad y salud en el trabajo (SST) -->
<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <div id="contenedor_tabla_detalle">
            <table class="table table-responsive table-condensed table-bordered" id="detalle_solicitud">   
              <div> 
                <tr>
                  <td class="text-right border-left-none" colspan="4">
                    <span id="ver_mantenimiento" class="btn btn-default oculto"><span class='fa fa-users red'></span> Mantenimiento</span>
                    <span id="ver_estados" class="btn btn-default"><span class='fa fa-calendar red'></span> Estado</span>
                  </td>
                </tr>
                <tr>
                  <th class="nombre_tabla" colspan="2"> Información General</th>
                </tr>
                <tr>
                  <td class="ttitulo">Solicitante:</td>
                  <td colspan="2">
                  <?php if($administra){?>
                    <div id="detalle_persona_solicita" class="pointer red btn" title="Detalle Persona" data-toggle="popover" data-trigger="hover">
                  <?php }else{?>
                    <div>
                  <?php }?>
                    <span class="valor_solicitante"></span>
                    </div>
                  </td>
                </tr>
                <tr id="id_clasificacion_solicitd">
                  <td colspan="2" style="width:50%" class="ttitulo red">Tipo</td>
                  <td colspan="2" style="width:50%" class="clasificacion"></td>
                </tr>
                <tr id="detalle_acto">
                  <td colspan="2" style="width:50%" class="ttitulo red">Detalle Acto</td>
                  <td colspan="2" style="width:50%" class="detalle_acto"></td>
                </tr>
                <tr id="personas_actos">
                  <td colspan="2" style="width:50%" class="ttitulo">Persona(s) que estan cometiendo actos inseguros</td>
                  <td colspan="2" style="width:50%" class="personas_actos"></td>
                </tr>
                <tr id="lugar_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Lugar </td>
                  <td colspan="2" style="width:50%" class="lugar"></td>
                </tr>
                <tr id="ubicacion_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Ubicación</td>
                  <td colspan="2" style="width:50%" class="ubicacion"></td>
                </tr>
                <tr id="descripcion_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Descripción</td>
                  <td colspan="2" style="width:50%" class="descripcion"></td>
                </tr> 
                <tr id="nombre_persona_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Nombre Persona</td>
                  <td colspan="2" style="width:50%" class="nombre_persona"></td>
                </tr> 
                <tr id="parentesco_persona_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Parentesco Persona</td>
                  <td colspan="2" style="width:50%" class="parentesco_persona"></td>
                </tr> 
                <tr id="numero_contacto_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Numero de Contacto</td>
                  <td colspan="2" style="width:50%" class="numero_contacto"></td>
                </tr> 
                <tr id="fecha_registro_soli">
                  <td colspan="2" style="width:50%" class="ttitulo">Fecha de emisión</td>
                  <td colspan="2" style="width:50%" class="fecha_registro"></td>
                </tr>
                <tr id="razones">
                  <td colspan="2" style="width:50%" class="ttitulo">Razones de la finalización</td>
                  <td colspan="2" style="width:50%" class="razones"></td>
                </tr>
              </div>
              </table>
          </div>

          <div id="contenedor_tabla_archivos_seguridad">
            <table class="table table-bordered table-hover table-condensed " id="tabla_archivos_seguridad"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE evidencias y soportes</td>
                    <td class="sin-borde text-right border-left-none" colspan="5" >
                      <span  class="btn btn-default btnAgregar" id="agregar_soporte_nuevos">
                      <span class="fa fa-plus red"></span> Agregar soporte</span>
                      <span  class="btn btn-default btnAgregar" id="agregar_adjuntos_nuevos">
                      <span class="fa fa-plus red"></span> Agregar imagenes</span> </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Tipo</td>
                    <td>Fecha Adjunto</td>
                    <td>Nombre usuario</td>
                    <td>Acciones</td>
                  </tr>
              </thead>
              <tbody></tbody>
            </table>

          </div> 
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal detalle estados solicitud SST-->
<div class="modal fade" id="modal_detalle_estados_solicitud" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle estado solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_estado_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla" colspan="6">TABLA Estados</th>
                </tr>
                <tr class="filaprincipal">
                  <td>No</td>
                  <td>Nombre Estado</td>
                  <td>Fecha</td>
                  <td>Usuario</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- modal detalle estados solicitud en mantenimiento, pedentiendo el tipo de solicitud con el módulo de SST-->
<div class="modal fade" id="modal_detalle_estados_mantenimiento" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle estado Mantenimiento</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tabla_detalle_estado_mantenimiento" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th class="nombre_tabla" colspan="6">TABLA Estados</th>
                </tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Nombre Estado</td>
                  <td>Fecha</td>
                  <td>Usuario</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_mtto" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Mantenimiento</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-responsive table-condensed table-bordered" id="detalle_mtto">   
            <div> 
              <tr>
                <th class="nombre_tabla" colspan="2"> Información General</th>
              </tr>
              <tr>
                <td style="width:50%" class="ttitulo red">No. Solicitud</td>
                <td style="width:50%" class="mtto_no_solicitud"></td>
              </tr>
              <tr>
                <td style="width:50%" class="ttitulo">Estado </td>
                <td style="width:50%" class="mtto_estado"></td>
              </tr>
              <tr>
                <td style="width:50%" class="ttitulo">Fecha</td>
                <td style="width:50%" class="mtto_fecha"></td>
              </tr>
              <tr>
                <td style="width:50%" class="ttitulo">Usuario</td>
                <td style="width:50%" class="mtto_usuario"></td>
              </tr> 
              <tr>
                <td style="width:50%" class="ttitulo">Observaciones</td>
                <td style="width:50%" class="mtto_observaciones"></td>
              </tr> 
            </div>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal listar estados de la solicitud y gestionarla en SST-->
<div class="modal fade" id="modal_gestion_y_estados" role="dialog">
  <div class="modal-dialog ">
    <form action="#" id="form_gestionar_solicitud" method="post">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-tasks"></span> Archivos</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="container-fluid row">
              <select class="form-control cbx_clasificacion" name="id_clasificacion" id="id_clasificacion" >
                <option value="" selected disabled >Seleccione </option>
              </select>
              <div class="row oculto width100" id="filtro_clas">
                  <div class="funkyradio funkyradio-success">
                    <input type="checkbox" id="admitido" name="mtto" value='1'>
                    <label for="admitido" title="Enviar a mantenimiento"> Enviar a mantenimiento</label>
                  </div>
                <div>
                  <input type="number" name="telefono" class="form-control" placeholder="Teléfono" id="telefono">
                </div>
              </div>
              <div class="oculto" id="filtro_acto">
                <textarea type="text" id="descripcion_acto" name="descripcion_acto" rows="5" class="form-control" placeholder="Detalle el acto inseguro" ></textarea>
                <select class="form-control" name="id_tipo_persona" id="id_tipo_persona" >
                  <option value="" >Seleccione el tipo de persona</option>
                  <option value="Tipo_Interna" >Interna</option>
                </select>
                <div class="input-group agro" id="sel_agregar_persona_int" style="display: none">
                  <span class="input-group-addon btnAgregarCompromisos pointer" id="btn_agregar_persona_int" title="Agregar personas" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-search"></span> Buscar</span>
                  <select class="form-control sin_margin" id="persona_asignada_int"> 
                    <option id="informacion_persona_int" value="Per_Agre_Int">0 Persona(s)</option> 
                  </select> 
                  <span class="input-group-addon btnEliminarCompromisos pointer red" id="retirar_persona_sele_int"  title="Retirar persona" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-remove "></span></span>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- modal para personas internas -->
<div class="modal fade" id="modal_buscar_persona" role="dialog">
  <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Personas</h3>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_dato_buscar' name="dato" class="form-control" placeholder="Ingrese identificación o nombre de la persona">
                <span class="input-group-btn"><button class="btn btn-default" type="button" id="btn_buscar_persona"><span class='fa fa-search'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width:100%;">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA Personas</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td title="Nombre completo de la persona">Nombre Completo</td>
                    <td>Correo</td>
                    <td style="width:15%;" title="Identificación de la persona">Identificación</td>
                    <td class="" style="width:15%;">Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
  </div>
</div>

<!-- filtro a las solicitudes -->
<div class="modal fade" id="modal_filtrar_solicitudes" role="dialog" >
  <div class="modal-dialog">
  <form action="#" id="form_filtrar_solicitudes" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-retweet"></span> <span id="text_add_arts"></span>
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
        <div class="row">
            <select class="form-control cbx_estados" name="filtro_estados" ></select>
            <select class="form-control cbx_tipos" name="filtro_tipos" id="filtro_tipos"></select>
            <select class="form-control cbx_clasificacion" name="filtro_clasificacion" id="filtro_clasificacion"></select>
            <div class="col-md-6" style="padding-left: 0px">
              <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio sin_margin" size="16" placeholder="Fecha Inicio" type="text" value=""
                    name="filtro_fecha_inicio" id="filtro_fecha_inicio">
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
            <div class="col-md-6" style="padding-right: 0px">
              <div class="input-group date form_datetime_block agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
                  data-link-field="dtp_input1">
                  <input class="form-control sin_focus f_inicio sin_margin" size="16" placeholder="Fecha Termina" type="text" value=""
                    name="filtro_fecha_termina" id="filtro_fecha_termina">
                  <span class="input-group-addon pointer red"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer red "><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"><span
              class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- datos solicitante -->
<div class="modal fade" id="Mostrar_detalle_persona" role="dialog">
    <div class="modal-dialog" >
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row"  style="width: 80%">
                  <div class="error text-center"></div>
                  <div id="datos_perso" class="">
                    <table class="table">
                      <tr class="nombre_tabla"><td colspan="">Datos</td></tr>
                      <tr><td class="foto_perso margin0" colspan=""></td></tr>
                      <tr><td class="nombre_perso"></td></tr>
                      <tr><td class="apellido_perso"></td></tr>
                      <tr><td class="tipo_id_perso"></td></tr>
                      <tr><td class="identi_perso"></td></tr>
                      <tr><td class="ubica_perso"></td></tr>
                      <tr><td class="celular"></td></tr>
                    </table>
                  </div>
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal notificaciones -->
<?php if($notifica):?>
<div class="modal fade" id="modal_notificaciones_seguridad" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones Bienestar Laboral</h3>
            </div>
            <div class="modal-body" id="bodymodal" >
                <div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
                <?php if($notifica){?><div id="panel_notificaciones_seguridad" style="width: 100%" class="list-group"></div><?php }?>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<!-- modal administrar módulo -->
<?php if($administra) { ?>
<div class="modal fade" id="administrar_biblioteca" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button class="close" type="button" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_bib">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" id="admin_perm"><a><span class="fa fa-cog red"></span> Auxiliares</a></li>
              <li class="pointer" id="admin_ases"><a><span class="fa fa-check red"></span> Asesores</a></li>
            </ul>
          </div>
        </nav>
        <div id="container_admin_bib">
          <div class="form-group col-md-6">
              <div class="agro agrupado sin_margin">
              <select required class="form-control cbx_aux_lab"></select> 
            </div>
          </div>
          <table class="table table-bordered table-hover table-condensed" id="tabla_permisos_lab" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td colspan="3" class="nombre_tabla">TABLA PERMISOS BIENESTAR LABORAL</td>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td>Descripción</td>
                <td>Acción</td>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="modal fade" id="administrar_estados_laboral" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button class="close" type="button" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Proceso</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div id="container_admin_proc">
          <table class="table table-bordered table-hover table-condensed" id="tabla_estados_lab" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <th class="nombre_tabla">TABLA ESTADOS</td>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td style= "width:150px">Accion</td>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_razones_fina" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button class="close" type="button" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Gestionar Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div id="container_causas_negar">
          <table class="table table-bordered table-hover table-condensed" id="tabla_razones_fina" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <th class="nombre_tabla">TABLA DE RAZONES</th>
                <td class="sin-borde text-right border-left-none" colspan="2">
                      <span class="btn btn-default btnAgregar" id="con_adjuntos_rev">
                      <span class="fa fa-plus red"></span> Agregar soporte</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td style= "width:150px">Accion</td>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
      <button type="button" class="btn btn-danger active" id="btn_negar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para archivos -->
<div class="modal fade" id="modal_enviar_archivos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
          <input type="hidden" name="id" id="id_solicitud" val="0">
          <div class="dz-message needsclick"><p>Arrastre archivos o presione click aquí</p></div>
        </form>
      </div>
      <div class="modal-footer" id="footermodal">
        <button id="cargar_adj_soli" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    <?php if($notifica){ ?> 
      mostrar_notificaciones(1); <?php } ?>
    listarSolicitudes({ 'id' : <?php echo $id ?>});
    Cargar_parametro_buscado(115, ".cbx_lugar", "Seleccione el lugar");
    Cargar_parametro_buscado_aux(319, ".cbx_beneficiario_md", "Seleccione el beneficiario");
    Cargar_parametro_buscado_aux(144, ".cbx_clasificacion", "Seleccione la clasificacion");
    Cargar_parametro_buscado_aux(145, ".cbx_estados", "Seleccione el estado");
    Cargar_parametro_buscado_aux(146, ".cbx_tipos", "Seleccione el tipo");
    recibir_archivos()
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
