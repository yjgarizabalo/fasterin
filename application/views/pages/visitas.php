<?php 
$administra = false;
  if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_vis") $administra = true;
?>

<div class="container col-md-12 " id="inicio-user">
  <?php if($administra){ ?>
  <div class="tablausu col-md-12 text-left oculto" id="container_visitas_departamento">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <div id="div_entradas_salidas" class="text-center">
        <h4><span class="fa fa-map-marker red"></span> Marcar entrada o salida <span id="mensaje_visita"
            style="color:green" class="oculto"> || Visitante Registrado</span></h4>
        <form class="form-inline" id="guardar_visita_departamento">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Codigo Departamento" name="departamento" required id="codigo_departamento">
          </div>
          <div class="form-group">
            <input type="number" class="form-control" placeholder="No. Identificacion" step="1" min="1"  name="identificacion" id="identificacion_departamento" required>
          </div>
          <div class="input-group">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit"><span class="fa fa-calendar-check-o red"></span>  Aceptar</button>
              <button type="button" class="btn btn-danger" id="consultar_ingresos_persona"><span class="fa fa-list"></span> Historial</button>
            </span>
          </div>
        </form>
      </div>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_departamentos"
        cellspacing="0" width="100%" style="">
        <thead class="ttitulo ">
          <tr>
            <td colspan="5" style="" class="nombre_tabla">TABLA DEPARTAMENTOS</td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td class="opciones_tbl">Cod.</td>
            <td>Empresa</td>
            <td>Nombre</td>
            <td>Ubicacion</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
  <?php }?>
  <?php if($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud"){ ?>
    <div class="tablausu col-md-12 text-left oculto" id="container_visitantes">
      <div class="table-responsive col-sm-12 col-md-12">
        <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <div class="form-group" style="width:100%">
          <div class="input-group col-md-4" style="float: right">
            <input class="form-control" id="txt_buscar_visitantes" value="" placeholder="Ingrese Nombre  o Identificación">
            <span class="input-group-addon pointer" title="Buscar Visitantes" data-toggle="popover" data-trigger="hover" id="btn_buscar_visitante"><span class="glyphicon glyphicon-search"></span></span>
          </div>
        </div>
        <br><br>
        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_visitantes" cellspacing="0" width="100%" style="">
          <thead class="ttitulo ">
            <tr>
              <td colspan="5" style="" class="nombre_tabla">TABLA VISITANTES</td>
              <td class="sin-borde text-right border-left-none">
                  <button class="btn btn-default" id="btn_visitante"> <span class="fa fa-plus red"></span> Nueva</button>
              </td> 
            </tr>
            <tr class="filaprincipal">
              <td class="opciones_tbl">Ver</td>
              <td>Nombre Completo</td>
              <td>Tipo Identificación</td>
              <td>Identificación</td>
              <td>Estado</td>
              <td class="opciones_tbl_btn">Acción</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
  </div>

  <div class="modal fade" id="modal_detalle_visitante" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 100%">
          <div id="tabla_detalle_visitante" class="table-responsive text-left">
            <table class="table text-center">
              <tr class="nombre_tabla">
                <td colspan="3">Datos</td>
              </tr>
              <tr>
                <td class="foto_visi foto" rowspan="6"></td>
              <tr>
                <td class="ttitulo">Nombre Completo</td>
                <td class="nombre"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo identificación</td>
                <td class="tipo_identificacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">identificación</td>
                <td class="identificacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Registrado por</td>
                <td class="registrado_por" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Registro</td>
                <td class="fecha_registra" colspan="2"></td>
              </tr>
            </table>
            
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_ingresos_visitante" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr><td colspan="4" style="" class="nombre_tabla">HISTORIAL DE INGRESOS</td></tr>
                <tr class="filaprincipal">
                  <td>Ver</td>
                  <td>Departamento</td>
                  <td>Hora Entrada</td>
                  <td>Hora Salida</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_sanciones_visitante" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr><td colspan="3" style="" class="nombre_tabla">SANCIONES</td>
                <td class="sin-borde text-right border-left-none"> <span class="btn btn-default"  id="btn_sancionar_visitante"> <span class="fa fa-plus red"></span> Agregar</span></td></tr>
                <tr class="filaprincipal">
                  <td>Motivo</td>
                  <td>Fecha</td>
                  <td>Sancionado por</td>
                  <td>Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


  <?php }?>
  <div class="tablausu col-md-12 text-left oculto" id="container-listado-eventos">
    <div class="table-responsive col-sm-12 col-md-12">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla-eventos"
        cellspacing="0" width="100%" style="">
        <thead class="ttitulo ">
          <tr>
            <td colspan="5" style="" class="nombre_tabla">TABLA EVENTOS <br><span class="mensaje-filtro oculto" id='mensaje-filtro-evento'><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
            <td class="sin-borde text-right border-left-none" colspan="5"> 
                <span class="btn btn-default btnModifica" id="btn_buscar_participantes"><span class="fa fa-search red"></span> Participante</span>
                <span class="btn btn-default btnModifica" id="btn_modificar_evento"><span class="fa fa-wrench red"></span> Modificar</span>
              <span class="btn btn-default" data-toggle="modal" data-target="#modal_filtrar_eventos"><span class="fa fa-filter red"></span> Filtrar</span> 
              <span class="btn btn-default" id="limpiar-filtros-eventos"> <span class="fa fa-refresh red"></span> Limpiar</span></td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>Tipo</td>
            <td>Nombre</td>
            <td>Fecha Inicio</td>
            <td>Fecha Fin</td>
            <td>Ubicación</td>
            <td>Solicitante</td>
            <td>Estado</td>
            <td class="opciones_tbl_btn" style='width:200px'>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>


  <div class="tablausu col-md-12 " id="menu_principal"
    style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <?php if($administra){ ?>
        <div id="listado_departamentos">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Logistica Ingresos</span>
            </div>
          </div>
        </div>
        <?php }?>
        <div id="agregar_evento">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/presupuesto.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Nuevo Evento</span>
            </div>
          </div>
        </div>
        <div id="agregar_visita">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/Personas.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Nueva Visita</span>
            </div>
          </div>
        </div>
        <?php if($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud"){ ?>
        <div id="adm_visitantes">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/talento_humano.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Visitantes</span>
            </div>
          </div>
        </div>
        <?php }?>
        <div id="listado_eventos">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
              <span class="btn  form-control btn-Efecto-men">Estados Solicitudes</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
        Regresar</p>
    </div>
  </div>
</div>

</div>
<form action="#" id="form_registrar_evento" method="post">
  <div class="modal fade scroll-modal" id="modal_registrar_evento" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-calendar"></span> Nuevo Evento</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <select name="tipo" class='oculto'>
              <option value="Evento">Evento</option>
              <option value="Visita">Visita</option>
            </select>
            <div class="funkyradio">
                <div class="funkyradio-success">
                  <input type="checkbox" id="con_cupos" name="con_cupos" value="1">
                  <label for="con_cupos" title="Con cupos" style="margin : 5 0 5 0;"> ¿ Cupos Limitados ?</label>
                </div>
            </div>
            <input type="text" class="form-control" name="nombre" placeholder="Nombre" required="">
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
              data-link-field="dtp_input1">
              <input class="form-control sin_focus" size="16" placeholder="Fecha Inicio" type="text" value=""
                required="true" name="fecha_inicio">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
              data-link-field="dtp_input1">
              <input class="form-control sin_focus" size="16" placeholder="Fecha Fin" type="text" value=""
                required="true" name="fecha_fin">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <input type="number" placeholder="#Cupos" class="form-control oculto" name="cupos" step="1" min="1">
            <select name="pre_inscripcion" required class="form-control" id="pre_inscripcion" >
              <option value="">Seleccione Tipo Ingreso</option>
              <option value="1">Entrada Libre</option>
              <option value="3">Entrada Libre - Multiple</option>
              <option value="2">Pre-inscripción</option>
            </select>
            <div class="funkyradio div_firma">
                <div class="funkyradio-success">
                  <input type="checkbox" id="firma" name="firma" value="1">
                  <label for="firma" title="Con firma" style="margin : 5 0 0 0;"> ¿ requiere firma de asistencia ?</label>
                </div>
            </div>
            <input type="text" class="form-control" name="ubicacion" placeholder="Ubicación" required="">
            <textarea class="form-control" cols="1" rows="3" name="descripcion" placeholder="Observaciones"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>

      </div>
    </div>
  </div>
</form>
<form action="#" id="form_modificar_evento" method="post">
  <div class="modal fade scroll-modal" id="modal_modificar_evento" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title "><span class="fa fa-calendar"></span> Modificar Evento</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
              <div class="funkyradio">
                <div class="funkyradio-success">
                  <input type="checkbox" id="con_cupos_modi" name="con_cupos" value="1">
                  <label for="con_cupos_modi" title="Con cupos" style="margin : 5 0 5 0;"> ¿ Cupos Limitados ?</label>
                </div>
            </div>
            <input type="text" class="form-control" name="nombre" placeholder="Nombre" required="">
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
              data-link-field="dtp_input1">
              <input class="form-control sin_focus" size="16" placeholder="Fecha Inicio" type="text" value=""
                required="true" name="fecha_inicio">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy - HH:ii p"
              data-link-field="dtp_input1">
              <input class="form-control sin_focus" size="16" placeholder="Fecha Fin" type="text" value=""
                required="true" name="fecha_fin">
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
              <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <input type="number" placeholder="#Cupos" class="form-control oculto" name="cupos" step='1' min='1'>
            <select name="pre_inscripcion" required class="form-control" id='pre_inscripcion_mod'>
              <option value="">Seleccione Tipo Ingreso</option>
              <option value="1">Entrada Libre</option>
              <option value="3">Entrada Libre - Multiple</option>
              <option value="2">Pre-inscripción</option>
            </select>
            <div class="funkyradio div_firma_mod">
                <div class="funkyradio-success">
                  <input type="checkbox" id="firma_modi" name="firma" value="1">
                  <label for="firma_modi" title="Con firma" style="margin : 5 0 0 0;"> ¿ requiere firma de asistencia ?</label>
                </div>
            </div>
            <input type="text" class="form-control" name="ubicacion" placeholder="Ubicación" required="">
            <textarea class="form-control" cols="1" rows="3" name="descripcion" placeholder="Observaciones"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="fa fa-wrench"></span> Modificar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_ingresos_departamentos" role="dialog">
  <div class="modal-dialog modal-lg modal-80">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-calendar"></span> Historial Visitas</h3>
      </div>
      <div class="modal-body " id="bodymodal">
        <form class="form-inline oculto" id="form_consulta_ingresos">
          <div class="form-group">
            <input class="form-control" size="16" type="date" value="<?php echo date('Y-m-d');?>" name="inicial"
              id="fecha_inicial_consulta">
          </div>
          <div class="form-group">
            <input class="form-control" size="16" type="date" value="" name="final" id="fecha_final_consulta"
              style="display:none">
          </div>
          <div class="form-group">
            <input type="number" class="form-control" placeholder="No. Identificacion" step="1" min="1"
              name="identificacion" id="identificacion_consulta">
          </div>
          <div class="input-group">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit"><span class="fa fa-calendar-check-o red"></span>
                Buscar</button>
            </span>
          </div>
          <div><label for="todas_visitas" class="sin_negrita"><input type="checkbox" name="todas"
                id="todas_visitas">Todas
              por persona</label> <label for="todas_visitas_general" class="sin_negrita"><input type="checkbox"
                name="todas" id="todas_visitas_general">Todas por Fecha</label> <label for="entre_fechas"
              class="sin_negrita"><input type="checkbox" name="entre_fechas" id="entre_fechas">Filtrar entre
              fechas</label> <label for="formato_horas" class="sin_negrita"><input type="checkbox" name="formato_horas"
                id="formato_horas">Formato Horas</label></div>
        </form>
        <div class="table-responsive" style="width: 100%">
          <table class="table table-bordered table-hover table-condensed pointer" id="tabla_ingresos_departamento"
            cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="">
                <td colspan="6" class="nombre_tabla">TABLA INGRESOS GENERAL</td>
              </tr>
              <tr class="filaprincipal">
                <td>Ver</td>
                <td>Nombre Completo</td>
                <td class="">identificación</td>
                <td>Departamento</td>
                <td>Hora Entrada</td>
                <td>Hora Salida</td>
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

<div class="modal fade" id="modal_detalle_ingreso_departamento_persona" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 80%">
          <div id="datos_perso" class="table-responsive">
            <table class="table text-center">
              <tr class="nombre_tabla">
                <td colspan="3">Datos</td>
              </tr>
              <tr>
                <td class="foto_visi foto" rowspan="6"></td>
              <tr>
                <td class="ttitulo">Nombre Completo</td>
                <td class="nombre_visi"></td>
              </tr>
              <tr>
                <td class="ttitulo">identificación</td>
                <td class="identificacion_visi"></td>
              </tr>
              <tr>
                <td class="ttitulo">Departamento</td>
                <td class="departamento_visi"></td>
              </tr>
              <tr>
                <td class="ttitulo">Hora Entrada</td>
                <td class="hora_entrada_visi"></td>
              </tr>
              <tr>
                <td class="ttitulo">Hora Salida</td>
                <td class="hora_salida_visi"></td>
              </tr>
              <tr class="nombre_tabla">
                <td colspan="3">Historial de Gestión</td>
              </tr>
              <tr>
                <td class="ttitulo">Entrada Marcada por</td>
                <td class="usuario_registra_visi" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo">Salida Marcada por</td>
                <td class="fecha_registra_visi" colspan="2"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_detalle_evento" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-calendar"></span> Información Evento</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row width100">
          <div class="table-responsive">
            <div class="alert alert-info" id='mostrar_codigo'>
              <strong><span class='evento_codigo'></span></strong> - Este es el codigo de ingreso al evento, vence <span class='fecha_vence_evento'></span>.
            </div>
            <table class="table">
              <tr class="nombre_tabla text-left">
                <td colspan="4">Datos</td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre </td>
                <td class="evento_nombre" colspan='3'></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Inicio</td>
                <td class="evento_inicio"></td>
                <td class="ttitulo">Fecha Fin</td>
                <td class="evento_fin"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo Ingreso</td>
                <td class="evento_ingreso" colspan='3'></td>
              </tr>
              <tr>
                <td class="ttitulo">Con firma</td>
                <td class="evento_firma"></td>
                <td class="ttitulo">Cupos</td>
                <td class="evento_cupos"></td>
              </tr>
              <tr>
                <td class="ttitulo">Ubicacion</td>
                <td class="evento_ubicacion"></td>
                <td class="ttitulo">Estado</td>
                <td class="evento_estado"></td>
              </tr>
              <tr>
                <td class="ttitulo">Observaciones</td>
                <td class="evento_descripcion" colspan='3'></td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan="4">Historial de Gestión</td>
              </tr>
              <tr>
                <td class="ttitulo">Registrado por</td>
                <td class="evento_solicitado_por"></td>
                <td class="ttitulo">Fecha Registro</td>
                <td class="evento_solicitado_fecha"></td>
              </tr>
              <tr class="tr_evento_cancelado_por oculto">
                <td class="ttitulo">Cancelado por</td>
                <td class="evento_cancelado_por"></td>
                <td class="ttitulo">Fecha Cancelado</td>
                <td class="evento_cancelado_fecha"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<form id="form_filtrar_eventos">
  <div class="modal fade" id="modal_filtrar_eventos" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-filter"></span> Crear Filtros</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">

          <div class="container-fluid sin_margin" style="padding: 0px">
								<div class="row-fluid" >
									<div class="col-md-6 sin_margin" style="padding: 0px">
										<h4 class="ttitulo center text-center">Fecha Inicial</h4>
										<input type="date" class="form-control CampoGeneral sin_margin text-center" name="fecha_inicio" required>
									</div>
									<div class="col-md-6 sin_margin" style="padding: 0px">
										<h4 class="ttitulo center text-center">Fecha Final</h4>
										<input type="date" class="form-control CampoGeneral sin_margin text-center" name="fecha_fin" required>
									</div>	
								</div>
							</div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active" id="generar_reporte"><span
              class="glyphicon glyphicon-ok"></span>
            Generar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_asignar_participantes" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-users"></span> Asignar Participantes</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="form-group agrupado col-md-8 text-left">
            <div class="input-group">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="btn_nuevo_participante"><span
                    class='fa fa-user-plus red'></span>
                  Nuevo</button>
              </span>
              <input id='txt_buscar_participante' class="form-control" placeholder="Ingrese identificación o nombre del participante" value='11434'>
              <span class="input-group-addon btn btn-default" id="btn_buscar_participante"><span class="glyphicon glyphicon-search"></span></span>
            </div>
          </div>
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_participantes"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" class="nombre_tabla">TABLA PARTICIPANTES</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre Completo</td>
                  <td>Identificación</td>
                  <td class='opciones_tbl'>Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<form id="form_agregar_nuevo_participante">
  <div class="modal fade" id="modal_agregar_nuevo_participante" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Agregar Participante</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div id="datos_participante" class="table-responsive">
              <table class="table text-center">
                <tr class="nombre_tabla">
                  <td colspan="3">Participante</td>
                </tr>
                <tr>
                  <td class="foto_visi foto" colspan='2'></td>
                <tr>
                  <td class="nombre_visi"></td>
                </tr>
              </table>
              <select name="id_tipo" required class="form-control tipo_participante">
                <option value="">Seleccione Tipo Participante</option>
              </select>
            
              <div class="input-group agro" id='container_hijos' >
                <select name="id_hijo" required class="form-control sin_margin cbx_hijos"> <option value="">Seleccione Hijo</option></select>
                <span class="input-group-addon pointer" id="eliminar_hijo"  title="Eliminar Hijo" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-remove"></span></span>
                <span class="input-group-addon pointer" id="agregar_hijo" title="Agregar Hijo" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-plus"></span> </span>
              </div>
              <textarea name="observaciones" id="" cols="5" rows="3" class='form-control' placeholder='Observaciones'></textarea>
              <div class="funkyradio">
                  <div class="funkyradio-success">
                    <input type="checkbox" id="con_vehiculo" name="con_vehiculo" value="1">
                    <label for="con_vehiculo">Con vehículo</label>
                  </div>
              </div>
              <div class="dato_vehi oculto">
                <input type="text" placeholder="Placa vehículo" class="form-control" name="placa_vehiculo">
                <input type="number" placeholder="#Acompanantes" class="form-control" name="acom_vehiculo" step="1"  min="0">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span
              class="glyphicon glyphicon-floppy-disk"></span>Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_asignar_hijo" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-users"></span> Asignar Hijo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="form-group agrupado col-md-8 text-left">
            <div class="input-group">
              <span class="input-group-btn">
              </span>
              <input id='txt_buscar_hijo' class="form-control" placeholder="Ingrese identificación o nombre del hijo" value=''>
              <span class="input-group-addon btn btn-default" id="btn_buscar_hijo"><span class="glyphicon glyphicon-search"></span></span>
            </div>
          </div>
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_hijos"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" class="nombre_tabla">TABLA PERSONAS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre Completo</td>
                  <td>Identificación</td>
                  <td class='opciones_tbl'>Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_listado_participantes_en_evento" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-users"></span> Listado Participantes</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div id='container_buscar_participante'></div>
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer"  id="tabla_listado_participantes_en_evento" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" class="nombre_tabla">TABLA PARTICIPANTES ASIGNADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class='opciones_tbl'>Ver</td>
                  <td>Nombre Evento</td>
                  <td>Nombre Persona</td>
                  <td>Identificación</td>
                  <td>tipo Ingreso</td>
                  <td>Hora Ingreso</td>
                  <td>Hora Salida</td>
                  <td>Correo</td>
                  <td>Programa</td>
                  <td>Celular</td>
                  <td>nombre hijo</td>
                  <td>identificación Hijo</td>
                  <td>Programa Hijo</td>
                  <td class='opciones_tbl'>Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <a href="<?php echo base_url()?>index.php/visitas/exportar_participantes" type="button" class="btn btn-danger" id='descargar_participantes'><span class="fa fa-cloud-download"></span> Descargar</a>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_solicitar_firma" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-pencil"></span> Firma Participante</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
        <p class='text-justify'>Por medio de la presente, la Universidad de la Costa identificada con NIT 890.104. 530.9, institución privada de educación superior, sin ánimo de lucro, con domicilio en la ciudad de Barranquilla Calle. 58 #55-66, con dirección electrónica: buzon@cuc.edu.co - y teléfono 3362200, me hace entrega de una Tablet marca XPECTWAY modelo TAB0001NG.</p>
        <p class='text-justify'>El estudiante tendrá 15 días después de la entrega, para reportar algún defecto de fábrica, el cual deberá hacer llegar en el plazo antes mencionado al Departamento de Tecnología (Bloque 2 piso 1 – Oficina de Recursos Audiovisuales), para su respectiva revisión. Por ningún motivo la Universidad de la Costa se hará responsable por daños debido al uso inadecuado de la misma, tales como golpes, señales de humedad u otros que en la apariencia del producto dé indicios de que ha sido utilizado en condiciones distintas a las comunes, cuando el producto haya sido alterado y/o reparado por personas no autorizadas, cuando el producto no hubiese sido operado de acuerdo con el instructivo de uso que le acompaña, ocasionando daños en el software, si el producto llevó acabo cualquier modificación en el software original pre instalado, así como tampoco por perdida o robo de la misma.</p>
        <p class='text-justify'>A partir de la firma de este documento, soy el responsable por cualquier daño, pérdida o robo, sucedido alguno de estos casos la tablet no será reemplazada, tampoco recibiré soporte hacia la misma, y me comprometo a darle un uso adecuado a la tablet, respetando las políticas de acceso de acuerdo a los lineamientos universitarios.</p>
        <p class='text-justify'>Conozco y entiendo que la tablet que recibo, la utilizaré solo en actividades con fines académicos que contribuyan a mi desarrollo profesional.</p>
        <div id="div_firmar"></div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active" id="enviar_firma"><span class="glyphicon glyphicon-check"></span> Aceptar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_participante_evento" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Ingreso</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row width100">
          <div class="table-responsive">
            <table class="table" id='tabla_detalle_participante_evento'>
              <tr class="nombre_tabla text-left">
                <td colspan="4">Datos</td>
              </tr>
              <tr>
                <td class="foto_participante foto" rowspan='7'></td>
              <tr>
              <tr>
                <td class="ttitulo">Nombre Completo </td>
                <td class="nombre_participante" colspan='2'></td>
              </tr>
              <tr>
                <td class="ttitulo">Identificación </td>
                <td class="identificacion_parti" colspan='2'></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo Ingreso</td>
                <td class="tipo_ingreso_participante" colspan='2'></td>
              </tr>
              <tr>
                <td class="ttitulo">vehículo</td>
                <td colspan='2'>Placa : <span  class="placa_participante"></span> | Acompañantes : <span class="acompanantes_participantes"></span></td>
              </tr>
              <tr>
                <td class="ttitulo">Observaciones</td>
                <td  colspan='2' class='observaciones'></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Entrada</td>
                <td class="entrada_participante"></td>
                <td class="ttitulo">Marcada por</td>
                <td class="entrada_marcada_por"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Salida</td>
                <td class="salida_participante"></td>
                <td class="ttitulo">Marcada por</td>
                <td class="salida_marcada_por"></td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan="4">Historial de Gestión</td>
              </tr>
              <tr>
                <td class="ttitulo">Registrado por:</td>
                <td class="participante_regi_por"></td>
                <td class="ttitulo">Fecha:</td>
                <td class="fecha_registro_par"></td>
              </tr>
              <tr class="nombre_tabla text-left con_firma">
                <td colspan="2">Firma</td>
              </tr>
              <tr class="nombre_tabla text-left con_firma">
                <td colspan="2" class='firma_participante'></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_registrar_visitante" role="dialog">
  <div class="fixed fixed_viaticos div_camara">
    <div class="reque">
      <div class="login-container">
        <table class="" id="" style="width: 100%">
          <thead class="">
            <tr class="">
              <td colspan="" class="nombre_tabla"> VISTA PREVIA</td>
            </tr>
          </thead>
        </table>
        <BR>
        <div class="form-boxw text-left">
          <div class="">
            <canvas id="foto" class="img-thumbnail"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-dialog">
    <form id="form_registrar_visitante" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Registro de Visitantes</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class="div_camara">
              <video id="camara" autoplay muted class="img-thumbnail"></video>
              <span id='botonFoto' class="btn btn-default form-control"><span class="	fa fa-camera red"></span> Tomar Foto</span>
            </div>
            <h6 class="ttitulo"><span class="glyphicon glyphicon-indent-left"></span> Datos Básicos</h6>
            <select name="tipo" required class="form-control  tipos_persona" id="tipo_persona">
            </select>
            <select name="tipo_identificacion" id="cbxtipoIdentificacion" required
              class="form-control  cbxtipoIdentificacion">
            </select>
            <input min="1" type="number" name="identificacion" id="txtIdentificacion" class="form-control inputt"
              placeholder="No. Identificación" required>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="apellido" id="txtApellido" class="form-control inputt2"
                  placeholder="Primer Apellido" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2"
                  placeholder="Segundo Apellido">
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre"
                  required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2"
                  placeholder="Segundo Nombre">
              </div>
            </div>
            <div id="datos_adicionales">
              <h6 class="ttitulo"><span class="glyphicon glyphicon-indent-left"></span> Datos Adicionales</h6>
              <select name="id_programa" required class="form-control  cbxprogramas req">
              </select>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="celular" id="celular" class="form-control" placeholder="Celular o Telefono">
                  <span class="input-group-addon">-</span>
                  <input type="email" name="correo" id="correo" class="form-control" placeholder="Correo">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" id="btnGuardarVisitante" class="btn btn-danger active btnAgregar"> <span
              class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_datos_adicionales_persona" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Datos Adicionales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row width100">
          <div class="table-responsive">
            <table class="table">
              <tr class="nombre_tabla text-left">
                <td colspan="2">Datos</td>
              </tr>
              <tr>
                <td class="ttitulo">Programa Academico</td>
                <td class="programa_visitante"></td>
              </tr>
              <tr>
                <td class="ttitulo">Correo </td>
                <td class="correo_visitante"></td>
              </tr>
              <tr>
                <td class="ttitulo">Celular</td>
                <td class="celular_visitante"></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_sanciones_visitante" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-ban"></span> Visitante Sancionado</h3>
      </div>
      <div class="modal-body" id="bodymodal">
    
        <div class="row" style="width: 100%">
          <div id="" class="table-responsive text-left">
            <table class="table table-bordered table-condensed table-responsive" cellspacing="0" width="100%">
              <tr><td colspan="2"class="nombre_tabla">DATOS VISITANTE</td>
              <tr><td colspan="2"class="foto" id='foto_sancionado'></td></tr>
              <tr><td colspan="2"><h3 id='nombre_sancionado'></h3></td></tr>
              <tr><td> <img src="<?php echo base_url() ?>/imagenes/sancionado.png" alt="..."  style='width: 40%;-webkit-animation: tiembla .5s infinite;'></td><td><p>El visitante se encuentra sancionado, por favor informar a las personas encargadas de la seguridad y llevar acabo el plan de seguimiento para estos casos.</p></td></tr>
            </table>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_sanciones_visitante_alert" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr><td colspan="4" style="" class="nombre_tabla">SANCIONES</td>
                <tr class="filaprincipal">
                  <td>Motivo</td>
                  <td>Fecha</td>
                  <td>Sancionado por</td>
                  <td>Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active btnAgregar" id="btn_continuar_ingreso_sancion"> <span class="fa fa-check"></span> Continuar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_registrar_visita" role="dialog">
  <div class="fixed fixed_viaticos div_camara div_camara_ingreso">
      <div class="reque">
        <div class="login-container">
          <table class="" id="" style="width: 100%">
            <thead class="">
              <tr class="">
                <td colspan="" class="nombre_tabla"> VISTA PREVIA</td>
              </tr>
            </thead>
          </table>
          <BR>
          <div class="form-boxw text-left">
            <div class="">
              <canvas id="foto_ingreso" class="img-thumbnail"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="modal-dialog">
    <form id="form_registrar_visita" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Ingreso de Visitante</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <div class='div_camara div_camara_ingreso' id = 'foto_ingreso'>
                  <video id="camara_ingreso" autoplay muted class="img-thumbnail"></video>
                  <span id='btn_foto_ingreso' class="btn btn-default form-control"><span class="fa fa-camera red"></span> Tomar Foto</span>
            </div>
            <hr>
            <table class="table table-bordered table-condensed table-responsive" cellspacing="0" width="100%">
              <tr><td colspan="2"class="nombre_tabla">DATOS VISITANTE</td>
              <tr id='foto_salida'><td class="foto"></td></tr>
              <tr><td><h3 id='nombre_ingreso'></h3></td></tr>
              <tr><td><p id='mensaje_ingreso'></p></td></tr>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"> <span class="fa fa-sign-in"></span>  Aceptar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="<?php echo base_url(); ?>js-css/estaticos/js/firmas.js"></script>
<script type="text/javascript">
$(".form_datetime").datetimepicker({
  format: 'yyyy-mm-dd hh:ii',
  autoclose: true,
});
</script>

<script>
$(document).ready(function() {
  inactivityTime();
  configuracion_camara('<?php echo $_SESSION["perfil"]?>');
  configuracion_camara('<?php echo $_SESSION["perfil"]?>',"btn_foto_ingreso","camara_ingreso","foto_ingreso");
  Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
  Cargar_parametro_buscado_aux(24, ".tipos_persona", "Seleccione Tipo Persona");
  Cargar_parametro_buscado(44, ".tipo_participante", "Seleccione Tipo Participante");
  Cargar_parametro_buscado(50, ".cbxprogramas", "Seleccione Programa");
  listar_visitantes("##%57");
});
</script>
