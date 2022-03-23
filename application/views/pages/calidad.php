<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/starability-growRotate.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">


<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin") {
  $sw = true;
  $sw_super = true;
}
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
</style>

<div class="container col-md-12 text-center" id="inicio-user">
  <div class="tablausu listado_solicitudes col-md-12 text-left <?php if (!$sw) echo 'oculto'; ?>">
    <div class="table-responsive col-sm-12 col-md-12 tablauser">
      <p class="titulo_menu pointer" id="regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes" cellspacing="0" width="100%">
        <thead class="ttitulo">
          <tr>
            <td class="nombre_tabla" colspan="3"> TABLA SOLICITUDES
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunes filtros aplicados
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="6">
              <?php if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal") { ?>
                <span class="black-color pointer btn btn-default" id="btnConfiguraciones">
                  <span class="fa fa-cogs red"></span> Administrar
                </span>
                <span class="btn btn-default" id="btn_procesos">
                  <span class="fa fa-cogs red"></span> Procesos
                </span>
                <span class="btn btn-default" id="btn_informes">
                  <span class="fa fa-file-pdf-o"></span> Informes
                </span>
                <span class="btn btn-default" id="btn_ver_lotes">
                  <span class="fa fa-archive red"></span> Lotes
                </span>
              <?php } ?>
              <span class="btn btn-default" id="btn_modificar">
                <span class="fa fa-pencil red"></span> Modificar
              </span>
              <span class="btn btn-default" id="btn_aplicar_filtros">
                <span class="fa fa-filter red"></span> Filtrar
              </span>
              <span class="btn btn-default" id="btn_limpiar_filtros">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo Solicitud</td>
            <td>Ubicación/Proceso</td>
            <td>Solicitante</td>
            <td>Fecha de registro</td>
            <td>Estado</td>
            <td style="width:150px">Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tablausu col-md-12 <?php if ($sw) echo 'oculto'; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <?php if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal") { ?>
          <div id="nueva_sol_auditoria" class="pointer">
            <div class="thumbnail">
              <div class="caption">
                <img src="<?php echo base_url() ?>/imagenes/auditoria.png" alt="...">
                <span class="btn form-control">Planes de Acción</span>
              </div>
            </div>
          </div>
        <?php } ?>
        <div id="nueva_solicitud" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/gestion_residuos.png" alt="...">
              <span class="btn form-control">Gestión de residuos</span>
            </div>
          </div>
        </div>
        <div class="" id="listado">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control">Estados Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
    </div>
  </div>

  <form id="form_agregar_sol_auditoria" method="post">
    <div class="modal fade" id="modal_agregar_sol_auditoria" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Solicitud</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="clearfix"></div>
              <select name="proceso_auditoria" class="form-control cbxproceso">
                <option value="">Seleccione el proceso</option>
              </select>
              <div class="clearfix"></div>
              <div class="clearfix"></div>
              <div class="form-group">
                <textarea name="observacion" id="txt_observacion" rows="5" class="form-control" placeholder="Observación"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_detalle_solicitud_aud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="2">Información de la Solicitud</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <button type="button" class="btn btn-default btn_imprimir" id="btn_imprimir"><span class="fa fa-print red"></span> Imprimir</button>
                  <button type="button" class="btn btn-default btn_log" id="btn_log"><span class="fa fa-history red"></span> Historial</button>
                </td>
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
                <td class="ttitulo" colspan="2">Proceso</td>
                <td class="proceso" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Descripción</td>
                <td class="descripcion" colspan="6"></td>
              </tr>
            </table>
          </div>

          <div class="table-responsive" id="tabla_responsables">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="8">Información de la Solicitud</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Lider</td>
                <td class="lider" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Agente de Cambio</td>
                <td class="agente_cambio" colspan="6"></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div id="imprimir_nc" class="oculto">
    <table class="table" style="font-size:10pt;">
      <tr>
        <td width='100'><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='100'></td>
        <td class='text-center' colspan='4'>
          <h4 class='text-center'>TRATAMIENTO DE NO CONFORMIDAD,<br />OPORTUNIDAES Y MEJORA CONTINUA</h4>
        </td>
        <td class="ttitulo">VERSION: 04 <br> MARZO 2019
          <hr> TDR:700-710-90
        </td>
      </tr>
      <tr>
        <td colspan='6' class="ttitulo" style="text-align:left;font-weight:bold;"> DATOS GENERALES</td>
      </tr>
      <tr>
        <td colspan='2' class="ttitulo"><b>PROCESO</b></td>
        <td colspan='2' class="ttitulo"><b>DIGILENCIADO POR</b></td>
        <td colspan='2' class="ttitulo"><b>FECHA</b></td>
      </tr>
      <tr>
        <td colspan='2' class="proceso"></td>
        <td colspan='2' class="agente"></td>
        <td colspan='2' class="fecha"></td>
      </tr>
      <tr>
        <td colspan='3' class="ttitulo"><b>TIPO DE ACCION</b></td>
        <td colspan='3' class="ttitulo"><b>TIPO DE HALLAZGO</b></td>
      </tr>
      <tr>
        <td colspan='3' class="tipo_accion"></td>
        <td colspan='3' class="tipo_hallazgo"></td>
      </tr>
      <tr>
        <td colspan='3' class="ttitulo"><b>ORIGEN - FUENTE:</b></td>
        <td colspan='3' class="origen_fuente"></td>
      </tr>
      <tr>
        <td colspan='6' class="ttitulo" style="text-align:left;font-weight:bold;">1 DESCRIPCIÓN DEL PROBLEMA (No Conformidad o Hallazgo) o aspecto por mejorar (oportunidad de mejora) debe ser clara y concisa</td>
      </tr>
      <tr>
        <td colspan='6' class="descripcion"></td>
      </tr>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_esquema_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='3'><b>ESQUEMA / FOTOGRAFÍA</b></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>N°</b></td>
          <td><b>NOMBRE</b></td>
          <td><b>FECHA</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_correcciones_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='3' style="text-align:left;font-weight:bold;">2 CORRECCIÓN (Acción Inmediata o Curita). <span style="font-size:8pt;">Diligencia el dueño del proceso.</span></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>ACTIVIDAD</b></td>
          <td><b>RESPONSABLE</b></td>
          <td><b>FECHA</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_participantes_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='3' style="text-align:left;font-weight:bold;">3 ANÁLISIS DE LA CAUSA RAÍZ (No conformidad) o Impacto o Beneficio (Oportunidad de mejora). <span style="font-size:8pt;">Diligencia el dueño del proceso.</span></td>
        </tr>
        <tr>
          <td colspan='3'><b>PARTICIPANTES EQUIPO DE MEJORA</b></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>N°</b></td>
          <td><b>RESPONSABLE</b></td>
          <td><b>FECHA</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_herramienta_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='6'><b>HERRAMIENTA 5 PORQUÉS?</b></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>LLUVIA DE IDEA</b></td>
          <td><b>1</b></td>
          <td><b>2</b></td>
          <td><b>3</b></td>
          <td><b>4</b></td>
          <td><b>5</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_soporte_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='3'><b>OTRA HERRAMIENTA</b></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>N°</b></td>
          <td><b>NOMBRE</b></td>
          <td><b>FECHA</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table table-bordered table-condensed" id='tabla_plan_imp' width="100%" style="font-size:10pt;">
      <thead class="ttitulo">
        <tr>
          <td colspan='3' style="text-align:left;font-weight:bold;">4 PLAN DE ACCIÓN. <span style="font-size:8pt;">Diligencia el dueño del proceso.</span></td>
        </tr>
        <tr class="filaprincipal">
          <td><b>ACTIVIDAD</b></td>
          <td><b>RESPONSABLE</b></td>
          <td><b>FECHA</b></td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="table" style="font-size:10pt;" width="100%">
      <tr>
        <td colspan='6' style="text-align:left;font-weight:bold;">5 RESPONSABLE DEL PROCESO</td>
      </tr>
      <tr>
        <td colspan='6' class="responsable_proceso"></td>
      </tr>
    </table>
  </div>

  <div class="modal fade con-scroll-modal" id="modal_procesos" role="dialog">
    <div class="modal-dialog" style="width: 690px;">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-list"></span> PROCESOS</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <div id="container_turnos_bib">
              <table class="table table-bordered table-hover table-condensed" id="tabla_procesos" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla">TABLA DE PROCESOS</th>
                    <td class="sin-borde text-right border-left-none">
                      <button class="btn btn-default agregar_proceso"> <span class="fa fa-plus red"></span> Nuevo Proceso</button>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Nombre</td>
                    <td class="opciones_tbl_btn">Acción</td>
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

  <div class="modal fade" id="modal_responsable_proceso" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Funcionarios del Proceso</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <div id="container_responsables">
              <table class="table table-bordered table-hover table-condensed" id="tabla_responsables_procesos" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA FUNCIONARIOS</td>
                    <td class="sin-borde text-right border-left-none">
                      <button class="btn btn-default add_funcionario"> <span class="fa fa-plus red"></span> Agregar</button>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Nombre</td>
                    <td>Identificacion</td>
                    <td>Tipo</td>
                    <td class="opciones_tbl_btn">Acción</td>
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

  <div class="modal fade" id="modal_nuevo_proceso" role="dialog">
    <div class="modal-dialog" style="width: 690px;">
      <form action="#" id="form_guardar_proceso" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo Proceso</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="input-group agro funcionario_proceso">
                <select name="personas_agregadas" class="form-control personas_agregadas sin_margin" id="personas_agregadas">
                  <option value="">0 Personas(s) a Asignar</option>
                </select>
                <span class="input-group-addon  btnElimina pointer " id="retirar_persona" title="Retirar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon  btnAgregar pointer add_persona" id="add_persona" title="Agregar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus red"></span> </span>
              </div>
              <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
              <textarea class="form-control inputt" name="descripcion" id="descripcion" placeholder="Descripción"></textarea>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_modificar_proceso" role="dialog">
    <div class="modal-dialog" style="width: 690px;">
      <form action="#" id="form_modificar_proceso" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Modificar Proceso</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro_mod" required>
              <textarea class="form-control inputt" name="descripcion" id="descripcion_mod" placeholder="Descripción"></textarea>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade scroll-modal" id="modal_menu_formato_nc" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> <span> Tratamiento de Hallazgos</span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="seleccion_tipo">
            <div class="row" style="width:100%">
              <div class="pointer" id="btn_datosgen">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                    <span class="btn form-control">Datos Generales</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_esquema">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/soportes.png" alt="...">
                    <span class="btn form-control">Esquema/Foto</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_correcion">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/sublineas.png" alt="...">
                    <span class="btn form-control">Corrección</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_analisis">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/investigacion.png" alt="...">
                    <span class="btn form-control">Análisis</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_participantes">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/participantes.png" alt="...">
                    <span class="btn form-control">Participantes</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_plan">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/presupuesto.png" alt="...">
                    <span class="btn form-control">Plan de Acción</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modal_datos_generales" role="dialog">
    <div class="modal-dialog">
      <form action="#" id="form_datos_nc" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-edit"></span> Datos Generales</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="tipo_accion" id="tipo_accion" required class="form-control cbxtipoaccion">
                    <option value="">Seleccione Tipo Accion</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="tipo_hallazgo" id="tipo_hallazgo" required class="form-control cbxtipohallazgo">
                    <option value="">Seleccione Tipo Hallazgo</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <select name="origen_fuente" id="origen_fuente" class="form-control cbxorigen">
                <option value="">Seleccione Origen - Fuente</option>
              </select>
              <div class="clearfix"></div>
              <div class="form-group">
                <textarea name="descripcion" id="txt_descripcion_pro" rows="5" class="form-control" placeholder="Descripción del problema"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade con-scroll-modal" id="modal_herramienta" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-wrench"></span> Herramientas</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <nav class="navbar navbar-default" style="display: flex;">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer active" id="asignar_herramienta"><a><span class="fa fa-list red"></span> 5 Por Qués?</a></li>
                  <li class="pointer" id="asignar_otra"><a><span class="fa fa-folder-open red"></span> Otra</a></li>
                </ul>
              </div>
            </nav>
            <div id="container_herramienta">
              <table class="table table-bordered table-hover table-condensed" id="tabla_herramienta" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="5">TABLA DE 5 POR QUÉ</th>
                    <td class="sin-borde text-right border-left-none" colspan="2">
                      <button class="btn btn-default agregar_herramienta"> <span class="fa fa-plus red"></span> Agregar</button>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Idea</td>
                    <td>1°</td>
                    <td>2°</td>
                    <td>3°</td>
                    <td>4°</td>
                    <td>5°</td>
                    <td>Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div id="container_otra_herramienta" class="oculto">
              <table class="table table-bordered table-hover table-condensed" id="tabla_otra_herramienta" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="3">TABLA DE ADJUNTOS</th>
                    <td class="sin-borde text-right border-left-none">
                      <button class="btn btn-default agregar_otra_herramienta"> <span class="fa fa-plus red"></span> Agregar</button>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td opciones_tbl>Ver</td>
                    <td>Nombre</td>
                    <td>Fecha</td>
                    <td class="opciones_tbl_btn">Acción</td>
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

  <form id="form_agregar_herramienta" method="post">
    <div class="modal fade" id="modal_add_herramienta" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-edit"></span> Herramienta de Análisis</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="form-group">
                <textarea name="idea" id="txt_idea" rows="5" class="form-control" placeholder="LLuvia de Ideas"></textarea>
                <textarea name="porque1" id="txt_porque1" rows="3" class="form-control" placeholder="Primer Por qué"></textarea>
                <textarea name="porque2" id="txt_porque2" rows="3" class="form-control" placeholder="Segundo Por qué"></textarea>
                <textarea name="porque3" id="txt_porque3" rows="3" class="form-control" placeholder="Tercer Por qué"></textarea>
                <textarea name="porque4" id="txt_porque4" rows="3" class="form-control" placeholder="Cuarto Por qué"></textarea>
                <textarea name="porque5" id="txt_porque5" rows="3" class="form-control" placeholder="Quinto Por qué"></textarea>

              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_esquema" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="text-right">
            <button type="button" class="btn btn-default" id="ver_adjuntos_esquema"><span class="fa fa-list red"></span> Ver Archivos</button>
          </div>
          <br>
          <form class="dropzone needsclick dz-clickable" id="Subir" action="">
            <input type="hidden" name="id_solicitud" id="id_solicitud_archivo" val="0">
            <input type="hidden" name="tipo" id="tipo_archivo" val="0">
            <div class="dz-message needsclick">
              <p>Arrastre archivos o presione clic aquí</p>
            </div>
          </form>
        </div>
        <div class="modal-footer" id="footermodal">
          <button id="cargar_adjuntos_general" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  
  <div class="modal fade" id="modal_listar_soportes" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed " id="tabla_soportes" cellspacing="0" width="100%">
              <thead class="">
                <tr>
                  <td colspan="3" class="nombre_tabla">TABLA DE ADJUNTOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">Ver</td>
                  <td>Nombre</td>
                  <td>Fecha</td>
                  <td class="opciones_tbl_btn">Acción</td>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_correcciones" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Correciones (Acción Inmediata)</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_correcciones" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA DE CORRECCIONES</td>
                  <td class="sin-borde text-right border-left-none">
                    <button class="btn btn-default add_actividad_correcion"> <span class="fa fa-plus red"></span> Agregar</button>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td>No.</td>
                  <td>Actividad</td>
                  <td>Responsable</td>
                  <td>Avances</td>
                  <td>Fecha Asignación</td>
                  <td class="opciones_tbl_btn">Acción</td>
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

  <div class="modal fade con-scroll-modal" id="modal_participantes" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X </button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Participantes</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <div id="container_participantes">
              <table class="table table-bordered table-hover table-condensed" id="tabla_participantes" cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE PARTICIPANTES</td>
                    <td class="sin-borde text-right border-left-none">
                      <button class="btn btn-default add_participante"> <span class="fa fa-plus red"></span> Agregar</button>
                    </td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td>No.</td>
                    <td>Nombre</td>
                    <td>Fecha</td>
                    <td class="opciones_tbl_btn">Acción</td>
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

  <div class="modal fade" id="modal_plan_accion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Plan de Acción</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_plan_accion" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="5" class="nombre_tabla">TABLA DE ACCIONES</td>
                  <td class="sin-borde text-right border-left-none">
                    <button class="btn btn-default add_actividad_plan"> <span class="fa fa-plus red"></span> Agregar</button>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td>No.</td>
                  <td>Actividades</td>
                  <td>Responsable</td>
                  <td>Avances</td>
                  <td>Fecha Asignación</td>
                  <td class="opciones_tbl_btn">Acción</td>
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

  <form id="form_agregar_actividad" method="post">
    <div class="modal fade" id="modal_add_actividades" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-edit"></span> Actividad</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="form-group">
                <textarea name="actividad" id="txt_actividad" rows="5" class="form-control" placeholder="Descripción del la actividad"></textarea>
              </div>
              <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                <input class="form-control sin_focus" size="16" placeholder="Fecha" type="text" value="" required="true" name="fecha_actividad" id="fecha_actividad">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
              <div class="input-group agro">
                <select name="persona_actividad" class="form-control persona_actividad sin_margin" id="persona_actividad">
                  <option value="">Asignar Responsable</option>
                </select>
                <span class="input-group-addon  btnElimina pointer " id="retirar_persona_responsable" title="Retirar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove red"></span></span>
                <span class="input-group-addon  btnAgregar pointer add_persona_actividad" id="add_persona_actividad" title="Agregar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus red"></span> </span>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_avances_actividad" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-tasks"></span> Avances Actividad</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_avances" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="4" class="nombre_tabla">TABLA DE ADJUNTOS</td>
                  <td class="sin-borde text-right border-left-none">
                    <button class="btn btn-default add_avance"> <span class="fa fa-plus red"></span> Agregar</button>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver</td>
                  <td>Nombre</td>
                  <td>Observación</td>
                  <td>Fecha Finalización</td>
                  <td class="opciones_tbl_btn">Acción</td>
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


  <form id="form_agregar_avances" method="post">
    <div class="modal fade" id="modal_agregar_avances" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-tasks"></span> Actividad</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agrupado" id="evidencia">
                <div class="input-group">
                  <label class="input-group-btn">
                    <span class="btn btn-primary">
                      <span class="fa fa-folder-open"></span>
                      Buscar <input type="file" style="display: none;" name="evidencia" id="evidencia_input">
                    </span>
                  </label>
                  <input type="text" id="evidencia_text" class="form-control" readonly placeholder="Evidencia actividad">
                </div>
              </div>
              <div class="form-group">
                <textarea name="observacion_act" id="observacion_act" rows="5" class="form-control" placeholder="Observación"></textarea>
                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                  <input class="form-control sin_focus" size="16" placeholder="Fecha Fin de Actividad" type="text" value="" required="true" name="fecha_finactividad" id="fecha_finactividad">
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                  <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_agregar_solicitud" method="post">
    <div class="modal fade" id="modal_agregar_solicitud" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Nueva Solicitud</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="funkyradio funkyradio-success">
                <input type="checkbox" id="btn_activo" name="activo" value="1">
                <label for="btn_activo" title="activo"> ¿es un activo de la universidad?</label>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%;">
                  <input type="number" class="form-control" name="cantidad" id="txt_cantidad_residuo" placeholder="Cantidad del residuo">
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%;">
                  <select name="cantidad_residuo" class="form-control cbxcantidad">
                    <option value="">Tipo de cantidad</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <select name="presentacion_residuo" class="form-control cbxpresentacion">
                <option value="">Seleccione la presentacion del resiudo</option>
              </select>
              <div class="clearfix"></div>
              <select name="estado_residuo" class="form-control cbxestadoresiduo">
                <option value="">Seleccione estado del residuo</option>
              </select>
              <div class="clearfix"></div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="id_bloque" id="bloque" required class="form-control cbxbloque">
                    <option value="">Seleccione Bloque</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="id_salon" id="salon" required class="form-control cbxsalon">
                    <option value="">Seleccione Salon</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="agrupado oculto" id="carta_activo">
                <div class="input-group">
                  <label class="input-group-btn">
                    <span class="btn btn-primary">
                      <span class="fa fa-folder-open"></span>
                      Buscar <input type="file" style="display: none;" name="carta_activo" id="carta_activo_input">
                    </span>
                  </label>
                  <input type="text" id="carta_activo_text" class="form-control" readonly placeholder="Carta de Activo">
                </div>
              </div>
              <div class="form-group">
                <textarea name="descripcion" id="txt_descripcion" rows="5" class="form-control" placeholder="Descripción del residuo"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_detalle_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="2">Información de la Solicitud</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <button type="button" class="btn btn-default btn_log" id="btn_log"><span class="fa fa-history red"></span> Historial</button>
                </td>
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
              <tr id="cont_lote">
                <td class="ttitulo" colspan="2">Codigo de lote</td>
                <td class="lote" colspan="6" align="center"></td>
              </tr>
            </table>
          </div>

          <div class="table-responsive" id="tabla_asignacion">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="8">Información de la Solicitud</th>
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
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_historial_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estado_solicitud" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
                <tr class="filaprincipal ">
                  <td>No.</td>
                  <td>Estado</td>
                  <td>Fecha</td>
                  <td>Persona Registra</td>
                  <td>Observación</td>
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

  <form id="form_asignar_solicitud" method="post">
    <div class="modal fade" id="modal_asignar_solicitud" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-calendar"></span> Asignar Solicitud</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin sin_focus" name="auxiliar" id='txt_auxiliar'>
                  <span class="input-group-addon pointer" id='btn_auxiliar' style='background-color:white'><span class='fa fa-search red'></span> Auxiliar</span>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                <input class="form-control sin_focus" size="16" placeholder="Fecha de Recolección" type="text" value="" required="true" name="fecha_recoleccion">
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_buscar_auxiliar" method="post">
    <div class="modal fade" id="modal_buscar_auxiliar" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Auxiliar</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre del empleado">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_auxiliar_busqueda" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA EMPLEADOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>No.</td>
                      <td>Nombre Completo</td>
                      <td>Identificacion</td>
                      <td class="opciones_tbl_btn">Acción</td>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal" id="btn_cerrar_aux"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_ver_lotes" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-archive"></span> Lotes actuales</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_lotes" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE LOTES</td>
                  <td class="sin-borde text-right border-left-none" colspan="6">
                    <span class="btn btn-default" id="btn_nuevo_lote">
                      <span class="fa fa-plus red"></span> Lotes
                    </span>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Ver</td>
                  <td>Codigo</td>
                  <td>No.Remisión</td>
                  <td>Empresa</td>
                  <td>No.Solicitudes</td>
                  <td>Estado</td>
                  <td>Acción</td>
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

  <form id="form_crear_lote" method="post">
    <div class="modal fade" id="modal_crear_lote" role="dialog">
      <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <select name="empresa" class="form-control cbxempresas">
              <option value="">Seleccione la empresa</option>
            </select>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade" id="modal_lote_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-archive"></span> Lotes activos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_lotes_activos" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="6" class="nombre_tabla">TABLA DE LOTES</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Codigo</td>
                  <td>Empresa</td>
                  <td>Persona Registra</td>
                  <td>Fecha Registra</td>
                  <td>No.Solicitudes</td>
                  <td>Acción</td>
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

  <div class="modal fade" id="modal_solicitudes_lote" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-archive"></span> Detalle Lote</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="2">Información de lote</th>
                <td class="sin-borde text-right border-left-none" colspan="6">
                  <button type="button" class="btn btn-default" id="btn_log_lote"><span class="fa fa-history red"></span> Historial</button>
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Creado por</td>
                <td class="creador" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registra" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Estado</td>
                <td class="estado" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Empresa</td>
                <td class="empresa" colspan="6"></td>
              </tr>
              <tr id="cont_formulario">
                <td class="ttitulo" colspan="2">Formato</td>
                <td class="formulario" colspan="6"></td>
              </tr>
              <tr id="cont_remision">
                <td class="ttitulo" colspan="2">Numero de remisión</td>
                <td class="no_remision" colspan="6"></td>
              </tr>
              <tr id="cont_certificado">
                <td class="ttitulo" colspan="2">Certificado</td>
                <td class="certificado" colspan="6"></td>
              </tr>
            </table>
          </div>

          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_solicitudes_agrupadas" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="6" class="nombre_tabla">TABLA DE SOLICITUDES</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">Ver</td>
                  <td>Tipo Residuo</td>
                  <td>Solicitante</td>
                  <td>Fecha de registro</td>
                  <td>Estado</td>
                  <td style="width:100px;">Acción</td>
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

  <div class="modal fade" id="modal_historial_lote" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
        </div>
        <div class="modal-body">
          <div class="table-responsive" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estado_lote" cellspacing="0" width="100%">
              <thead class="ttitulo">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
                <tr class="filaprincipal ">
                  <td>No.</td>
                  <td>Estado</td>
                  <td>Fecha</td>
                  <td>Persona Registra</td>
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

  <form id="form_formulario_empresa" method="post">
    <div class="modal fade" id="modal_formulario_empresa" role="dialog">
      <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-paper-plane"></span> Enviar Formato</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="agrupado" id="formulario_empresa">
              <div class="input-group">
                <label class="input-group-btn">
                  <span class="btn btn-primary">
                    <span class="fa fa-folder-open"></span>
                    Buscar <input type="file" style="display: none;" name="formulario_empresa" id="formulario_empresa_input">
                  </span>
                </label>
                <input type="text" class="form-control" readonly placeholder="Formato">
              </div>
              <div class="form-group">
                <textarea name="mensaje" id="txt_mensaje" rows="5" class="form-control" placeholder="Mensaje del correo electronico"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-paper-plane"></span> Enviar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_remitir_lote" method="post">
    <div class="modal fade" id="modal_formulario_remision" role="dialog">
      <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-barcode"></span> Remitir lote</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <input type="number" class="form-control" name="numero_remision" id="txt_numero_remision" placeholder="Numero de remisión">
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_finalizar" method="post">
    <div class="modal fade" id="modal_finalizar" role="dialog">
      <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-check"></span> Finalizar</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="agrupado" id="certificado_empresa">
              <div class="input-group">
                <label class="input-group-btn">
                  <span class="btn btn-primary">
                    <span class="fa fa-folder-open"></span>
                    Buscar <input type="file" style="display: none;" name="certificado" id="certificado_input">
                  </span>
                </label>
                <input type="text" class="form-control" readonly placeholder="Certificado">
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

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
              <select name="id_tipo_solicitud" id="id_tipo_sol_select" class="form-control cbxtiposol">
                <option value="">Filtrar por tipo solicitud</option>
              </select>
              <select name="id_tipo_residuo" class="form-control cbxtipos" id="id_tipo_select">
                <option value="">Filtrar por Tipo residuo</option>
              </select>
              <select name="id_estado_solicitud" class="form-control cbxestados" id="id_estado_select">
                <option value="">Filtrar por Estado</option>
              </select>
              <select name="id_presentacion_residuo" class="form-control cbxpresentacion" id="id_presentacion_select">
                <option value="">Filtrar por presentacion</option>
              </select>
              <select name="id_cantidad_residuo" class="form-control cbxcantidad" id="id_cantidad_select">
                <option value="">Filtrar por unidad de medida</option>
              </select>
              <select name="id_tipo_proceso" class="form-control cbxproceso" id="id_proceso_select">
                <option value="">Filtrar por tipo de proceso</option>
              </select>
              <select name="id_origen_proceso" class="form-control cbxorigen" id="id_origen_select">
                <option value="">Filtrar por Origen Fuente</option>
              </select>
              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
                  <input class="form-control sin_margin" value="" type="date" name="fecha_inicial" id="fecha_inicial">
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
                  <input class="form-control sin_margin" value="" type="date" name="fecha_final" id="fecha_final">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active" id="btn_filtrar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_modificar_solicitud" method="post">
    <div class="modal fade" id="modal_modificar_solicitud" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-pencil"></span> Modificar Solicitud</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="funkyradio funkyradio-success">
                <input type="checkbox" id="btn_activo_mod" name="activo" value="1">
                <label for="btn_activo_mod" title="activo"> ¿es un activo de la universidad?</label>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%;">
                  <input type="number" class="form-control" name="cantidad" id="txt_cantidad_residuo_mod" placeholder="Cantidad del residuo">
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%;">
                  <select name="cantidad_residuo" class="form-control cbxcantidad">
                    <option value="">Tipo de cantidad</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <select name="presentacion_residuo" class="form-control cbxpresentacion">
                <option value="">Seleccione la presentacion del resiudo</option>
              </select>
              <div class="clearfix"></div>
              <select name="estado_residuo" class="form-control cbxestadoresiduo">
                <option value="">Seleccione estado del residuo</option>
              </select>
              <div class="clearfix"></div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="id_bloque" id="bloque_mod" required class="form-control cbxbloque">
                    <option value="">Seleccione Bloque</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding: 0px;">
                <div class="input-group" style="width: 100%">
                  <select name="id_salon" id="salon_mod" required class="form-control cbxsalon">
                    <option value="">Seleccione Salon</option>
                  </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="agrupado oculto" id="carta_activo_mod">
                <div class="input-group">
                  <label class="input-group-btn">
                    <span class="btn btn-primary">
                      <span class="fa fa-folder-open"></span>
                      Buscar <input type="file" style="display: none;" name="carta_activo_mod" id="carta_activo_input_mod">
                    </span>
                  </label>
                  <input type="text" id="carta_activo_text_mod" class="form-control" readonly placeholder="Carta de Activo">
                </div>
              </div>
              <div class="form-group">
                <textarea name="descripcion" id="txt_descripcion_mod" rows="5" class="form-control" placeholder="Descripción del residuo"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="modal fade scroll-modal" id="modal_tipo_persona" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-user"></span> <span> Tipo Persona</span></h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="seleccion_tipo">
            <div class="row">
              <div class="pointer" id="btn_lider">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/presentation.png" alt="...">
                    <span class="btn form-control">Lider</span>
                  </div>
                </div>
              </div>
              <div class="pointer" id="btn_agente">
                <div class="thumbnail">
                  <div class="caption" style="text-align:center;">
                    <img src="<?php echo base_url() ?>/imagenes/participantes.png" alt="...">
                    <span class="btn form-control">Agente de Cambio</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

</div>


<div class="modal fade" id="modal_informes" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-pdf-o"></span> Informes</h3>
      </div>
      <div class="modal-body">
        <div class="input-group adicional_info" style="width: 100%;">
          <select name="opciones_informes" id="opciones_informes" class="form-control" required="" style="width: 30%; margin-left: 2%; ">
            <option value="0">Informe a Generar</option>
            <option value="1">Estado de las Acciones</option>
            <option value="2">Detalles del estado de las Acciones</option>
            <option value="3">Tipo de Accion</option>
            <option value="4">Tipo de Hallazgo</option>
            <option value="5">Estados de Hallazgo de Auditoria</option>
            <option value="6">Tipo de Acciones por Origen</option>
            <option value="7">Estado de Cumplimiento de las Acciones por Procesos </option>
            <option value="8">Tipos de las Acciones por Procesos </option>
            <option value="9">Tipo de Hallazgo por Origen</option>
            <option value="10">Tipos de los Hallazgos por Procesos </option>
          </select>

          <center>
            <div class="input-group adicional_info" style="width: 10%;">
              <span class="input-group-addon">Filtro</span>
              <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
              <input type="date" class="form-control sin_margin" required="true" name='fecha1' id="fecha1">
              <span class="input-group-addon">-</span>
              <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
              <input type="date" class="form-control sin_margin" required="true" name='fecha2' id="fecha2">
            </div>
            <br>
            <span class="btn btn-danger active" id="btn_generar_informes" style="background-color:white; width: auto; "><span class="glyphicon glyphicon-ok"></span> Generar</span>
            <button type="submit" class="btn btn-danger active" id="btn_generar_Graficos"><span class="glyphicon glyphicon-ok"></span> Graficar</button>
            <button type="submit" class="btn btn-danger active" id="btn_generar_Graficos2"><span class="glyphicon glyphicon-ok"></span> Graficar</button>
            <button type="submit" class="btn btn-danger active" id="btn_generar_Graficos3"><span class="glyphicon glyphicon-ok"></span> Graficar</button>
          </center>
        </div>

        <table id="tbltabla_estados" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">Informe</td>
            </tr>
            <tr class="filaprincipal ">
              <td>Estado</td>
              <td>Cantidad</td>
              <td>Porcentaje</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <table id="tbltabla_estados2" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">Informe</td>
            </tr>
            <tr class="filaprincipal ">
              <td>Estado</td>
              <td>Cantidad</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <table id="tbltabla_estados3" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">Informe</td>
            </tr>
            <tr class="filaprincipal ">
              <td>Item</td>
              <td>Ejecutadas</td>
              <td>En Proceso</td>
              <td>Abierta</td>
              <td>Total Acciones</td>
              <td>Porcentaje %</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <table id="tbltabla_estados4" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">Informe</td>
            </tr>
            <tr class="filaprincipal ">
              <td>Item</td>
              <td>Acciones Correctivas</td>
              <td>Acciones Preventivas</td>
              <td>Acciones Mejora</td>
              <td>Total</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <table id="tbltabla_estados5" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <td colspan="2" class="nombre_tabla">Informe</td>
            </tr>
            <tr class="filaprincipal ">
              <td>Item</td>
              <td>No Conformidad</td>
              <td>Oportunidad de Mejora</td>
              <td>Observacion</td>
              <td>Total</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active" id="mostar_informe_general" style="margin-right: 75%;"><span class="fa fa-file-pdf-o"></span> Informe General</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_generar_grafica" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cloud-download"></span> Generar Gráfica</h3>
      </div>

      <div class="col-md-12" style="padding: 0px !important;">

        <div class="col-md-8" id="torta">
          <canvas id="myGraph" class="oculto" width="1" height="1" style="margin-left: 25%; "></canvas>
        </div>

        <div class="col-md-8" id="bar">
          <canvas id="myGraph2" class="oculto" width="1" height="1" style="margin-left: 25%; "></canvas>
        </div>

        <div class="col-md-8" id="bar2">
          <canvas id="myGraph3" class="oculto" width="1" height="1" style="margin-left: 25%; "></canvas>
        </div>

      </div>

      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade scroll-modal" id="modal_informe_general" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-pdf-o"></span> <span> Informe General</span></h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <center>
          <div class="input-group adicional_info" ">
                    <span class=" input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Desde</span>
            <input type="date" class="form-control sin_margin" required="true" name='fecha1' id="fecha1_general">
            <span class="input-group-addon">-</span>
            <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Hasta</span>
            <input type="date" class="form-control sin_margin" required="true" name='fecha2' id="fecha2_general">
          </div>

        </center>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="submit" class="btn btn-danger active" id="imprimir_informe_general"><span class="fa fa-print"></span> Imprimir</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<?php if ($sw) { ?>

  <div id='informe_general' class="oculto">
    <div class="row">
      <div class="col-sm-12">
        <div class="thumbnail">
          <div>
            <img src="<?php echo base_url() ?>/imagenes/LogocucF.png" alt="..." width='90' heigth='90'>
            <div style="display: inline-block;">
              <p>INFORME PLAN DE SEGUIMIENTO DE ACCIONES CORRECTIVAS, PREVENTIVAS Y DE MEJORAS</p>
            </div>
          </div>
        </div>

        <div class="caption text-center">
          <p>ESTADO DE LAS ACCIONES</p>
          <div>
            <table class='table table-bordered' id='tabla_estado_informes'>
              <thead>
                <tr>
                  <td>
                    <center>Estado</center>
                  </td>
                  <td>
                    <center>Cantidad</center>
                  </td>
                  <td>
                    <center>Porcentaje</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br><br><br><br>
          <img id="imagen_grafica1" src="" alt="">
          <div class="col-md-8" id="torta_informe1">
            <canvas id="myGraph_informe1" style="display: none;"></canvas>
          </div>
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>


        <div class="caption text-center">
          <p>DETALLE DEL ESTADO DE LAS ACCIONES</p>
          <div>
            <table class='table table-bordered' id='tabla_detalle_estado'>
              <thead>
                <tr>
                  <td>
                    <center>Estado</center>
                  </td>
                  <td>
                    <center>Cantidad</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br><br><br><br>
          <img id="imagen_grafica2" src="" alt="">
          <div class="col-md-8" id="bar_informe2">
            <canvas id="myGraph_informe2" style="display: none;"></canvas>
          </div>
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br>

        <div class="caption text-center">
          <p>TIPO ACCION</p>
          <div>
            <table class='table table-bordered' id='tabla_tipo_accion'>
              <thead>
                <tr>
                  <td>
                    <center>Tipo Accion</center>
                  </td>
                  <td>
                    <center>Cantidad</center>
                  </td>
                  <td>
                    <center>Porcentaje</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br><br><br><br>
          <img id="imagen_grafica3" src="" alt="">
          <div class="col-md-8" id="torta_informe3">
            <canvas id="myGraph_informe3" style="display: none;"></canvas>
          </div>
        </div>
        <br><br><br><br><br><br><br><br><br><br><br><br>


        <div class="caption text-center">
          <p>TIPO DE HALLAZGO</p>
          <div>
            <table class='table table-bordered' id='tabla_tipo_hallazgo'>
              <thead>
                <tr>
                  <td>
                    <center>Tipo Hallazgo</center>
                  </td>
                  <td>
                    <center>Cantidad</center>
                  </td>
                  <td>
                    <center>Porcentaje</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br><br><br><br>
          <img id="imagen_grafica4" src="" alt="">
          <div class="col-md-8" id="torta_informe4">
            <canvas id="myGraph_informe4" style="display: none;"></canvas>
          </div>
        </div>
        <br><br><br><br><br><br><br>

        <div class="caption text-center">
          <p>ESTADO DE LOS HALLAZGOS DE AUDITORIA </p>
          <div>
            <table class='table table-bordered' id='tabla_estado_auditoria'>
              <thead>
                <tr>
                  <td>
                    <center>Origen</center>
                  </td>
                  <td>
                    <center>Ejecutado</center>
                  </td>
                  <td>
                    <center>Proceso</center>
                  </td>
                  <td>
                    <center>Abierta</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                  <td>
                    <center>Porcentaje</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br><br><br>
          <img id="imagen_grafica8" src="" alt="">
          <div class="col-md-6" id="torta_informe8">
            <canvas id="myGraph_informe8" style="display: none;"></canvas>
          </div>
          <br>
        </div>
        <br>

        <div class="caption text-center">
          <p>TIPO DE ACCIONES POR ORIGEN </p>
          <div>
            <table class='table table-bordered' id='tabla_tipo_origen'>
              <thead>
                <tr>
                  <td>
                    <center>Origen</center>
                  </td>
                  <td>
                    <center>Acciones Correctivas</center>
                  </td>
                  <td>
                    <center>Acciones Preventivas</center>
                  </td>
                  <td>
                    <center>Acciones de Mejora</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <br>
          <img id="imagen_grafica9" src="" alt="">
          <div class="col-md-6" id="bar_informe9">
            <canvas id="myGraph_informe9" style="display: none;"></canvas>
          </div>

        </div>
        <br>
        <div class="caption text-center">
          <p>ESTADO DE CUMPLIMIENTO DE LAS ACCIONES POR PROCESOS</p>
          <div>
            <table class='table table-bordered' id='tabla_cumplimiento_estados'>
              <thead>
                <tr>
                  <td>
                    <center>Procesos</center>
                  </td>
                  <td>
                    <center>Ejecutado</center>
                  </td>
                  <td>
                    <center>Proceso</center>
                  </td>
                  <td>
                    <center>Abierta</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                  <td>
                    <center>Porcentaje</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <img id="imagen_grafica5" src="" alt="">
          <div class="col-md-5" id="torta_informe5">
            <canvas id="myGraph_informe5" style="display: none;"></canvas>
          </div>
        </div>
        <br><br><br>

        <div class="caption text-center">
          <p>TIPOS DE ACCIONES POR PROCESOS</p>
          <div>
            <table class='table table-bordered' id='tabla_tipo_proceso'>
              <thead>
                <tr>
                  <td>
                    <center>Procesos</center>
                  </td>
                  <td>
                    <center>Acciones Correctivas</center>
                  </td>
                  <td>
                    <center>Acciones Preventivas</center>
                  </td>
                  <td>
                    <center>Acciones de Mejora</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <img id="imagen_grafica6" src="" alt="">
          <div class="col-md-5" id="bar_informe6">
            <canvas id="myGraph_informe6" style="display: none;"></canvas>
          </div>
        </div>
        <br>

        <div class="caption text-center">
          <p>TIPO DE HALLAZGO POR ORIGEN</p>
          <div>
            <table class='table table-bordered' id='tabla_hallazgo_origen'>
              <thead>
                <tr>
                  <td>
                    <center>Origen</center>
                  </td>
                  <td>
                    <center>No Conformidades</center>
                  </td>
                  <td>
                    <center>Oportunidad de Mejora</center>
                  </td>
                  <td>
                    <center>Observacion</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <img id="imagen_grafica10" src="" alt="">
          <div class="col-md-6" id="bar_informe10">
            <canvas id="myGraph_informe10" style="display: none;"></canvas>
          </div>

        </div>
        <br>

        <div class="caption text-center">
          <p>TIPOS DE HALLAZGOS POR PROCESOS</p>
          <div>
            <table class='table table-bordered' id='tabla_hallazgo_proceso'>
              <thead>
                <tr>
                  <td>
                    <center>Procesos</center>
                  </td>
                  <td>
                    <center>No Conformidades</center>
                  </td>
                  <td>
                    <center>Oportunidad de Mejora</center>
                  </td>
                  <td>
                    <center>Observacion</center>
                  </td>
                  <td>
                    <center>Total Acciones</center>
                  </td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <img id="imagen_grafica7" src="" alt="">
          <div class="col-md-5" id="bar_informe7">
            <canvas id="myGraph_informe7" style="display: none;"></canvas>
          </div>
        </div>
      </div>
    </div>

  </div>
<?php } ?>


<form id="form_administrar" method="post">
  <div class="modal fade" id="modal_administrar" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Módulo</h3>
        </div>
        <div class="modal-body " id="bodymodal">
            <div style="display: flex; justify-content: space-evenly;">
              <div id="permisos" style="width: 100%;">
                <div id='container_mensaje_ap'></div>
                <div class="form-group">
                  <div class="input-group agro col-md-8">
                    <input name="persona_soli" type="hidden" id="input_sele_re">
                    <span id="s_persona" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
                    <span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                  </div>
                </div>
                <table id="tabla_actividades" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td class="nombre_tabla" colspan="4">TABLA PROCESOS</td>
                    </tr>
                    <tr class="filaprincipal ">
                      <td class="opciones_tbl">No.</td>
                      <td>Nombre</td>
                      <td class="opciones_tbl_btn">Acción</td>
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
</form>


<div id="modal_elegir_persona" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Buscar Persona</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <form id="frm_buscar_persona">
          <div class="input-group col-md-6">
            <input type="text" class="form-control sin_margin" required="true" id='txt_persona' placeholder="Buscar Persona" />
            <span type="submit" class="input-group-addon pointer" id='btn_buscar_persona' style='	background-color:white'><span class='fa fa-search red'></span></span>
          </div><br>
        </form>
        <table id="tabla_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
          <thead class="ttitulo">
            <tr>
              <th colspan="3" class="nombre_tabla">TABLA PERSONAS</th>
            </tr>
            <tr class="filaprincipal">
              <td>Nombre</td>
              <td class="opciones_tbl_btn">Gestión</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div id="modal_elegir_estado" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<table id="tabla_estados" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
					<thead class="ttitulo">
						<tr><th colspan="3" class="nombre_tabla">TABLA ESTADOS</th></tr>
						<tr class="filaprincipal">
							<td>Parámetro</td>
							<td>Nombre</td>
							<td>Gestión</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer" id="footermodal">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
	</div>
</div>

<style>
  .funkyradio input[type="checkbox"]:empty~label:before {
    display: inline-flex !important;
  }
</style>

<script type="text/javascript">
  let startDate = new Date();
  $(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    autoclose: true,
    // startDate,
    todayBtn: true,
    daysOfWeekDisabled: [0]
  });
</script>

<script>
  $(document).ready(function() {
    listar_solicitudes('<?php echo $id ?>');
    activarfile();
    cargar_archivos_general(`${Traer_Server()}index.php/calidad_control/recibir_archivos`);

  })
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>