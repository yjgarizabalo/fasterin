<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php 
  $administra = $_SESSION["perfil"] == "Per_Admin"  ? true :false;
  $soporte = $_SESSION["perfil"] == "Per_Sop"  ? true :false;
  $admin_soporte = $_SESSION['perfil'] == 'Admin_Sopor' ? true : false;
?>

<div class="container col-md-12 " id="inicio-user">
    <!-- Tabla de solicitudes -->
    <div class="tablausu col-md-12 text-left oculto" id="listar_solicitudes">
        <div class="table-responsive col-sm-12 col-md-12">
            <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes"
                cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                    <tr>
                        <td colspan="3" style="" class="nombre_tabla">TABLA solicitudes <br>
                            <div>
                                <span class="mensaje-filtro oculto" id='mensaje-filtro-evento'>
                                    <span class="fa fa-bell red"></span> La tabla tiene algunos filtros
                                    aplicados.</span>
                                </span>
                            </div>
                        </td>
                        <td class="sin-borde text-right border-left-none" colspan="7">
                            <?php if ($administra || $admin_soporte) {?>
                            <span class="black-color pointer btn btn-default" id="btnConfiguraciones">
                                <span class="fa fa-cogs red"></span> Administrar
                            </span>
                            <?php }?>
                            <span class="btn btn-default" id="filtrar_solicitudes" data-toggle="modal">
                                <span class="fa fa-filter red"></span> Filtrar</span>
                            <span class="btn btn-default" id="btn_limpiar_filtros">
                                <span class="fa fa-refresh red"></span> Limpiar</span>
                        </td>
                    </tr>
                    <tr class="filaprincipal">
                        <td style="width: 60px">Ver</td>
                        <td>Solicitante</td>
                        <td>Asunto</td>
                        <td>Fecha Solicitud</td>
                        <td>Tiempo Solucionado (minutos)</td>
                        <td>Calificación Solucionado</td>
                        <td>Tiempo Asignacion (minutos)</td>
                        <td>Calificación Asignación</td>
                        <td style="width: 75px">Estado</td>
                        <td style="width: 60px !important;">Acción</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="modal_crear_ticket" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <form id="form_crear_ticket">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-crosshairs"></span> <span
                                class="texto_seleccion">Crear </span> ticket</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <?php if($administra || $soporte || $admin_soporte){ ?>
                                <div class="agro agrupado">
                                    <div class="input-group">
                                        <input type="text" class="form-control sin_margin sin_focus" name="buscar_persona"
                                        id='txt_buscar_persona'>
                                        <span class="input-group-addon pointer" id='btn_buscar_persona'
                                        style='background-color:white'><span class='fa fa-search red'></span>
                                        Buscar Persona</span>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="tipo_solicitud" id="tipo_solicitud"
                                        class="form-control cbxtipo_solicitud" title="Tipo">
                                        <option value="">Tipo</option>
                                    </select>
                                </div>
                            </div>
                            <input type="text" name="asunto" class="form-control" placeholder="Asunto" required>
                            
                            <textarea name="descripcion" cols="1" rows="3" placeholder="Descripión" class="form-control"
                            required></textarea>
                                <?php if($administra){ ?>
                                    <div class="agrupado btn_evidencia_form_principal oculto">
                                        <div class="input-group">
                                            <label class="input-group-btn">
                                                <span class="btn btn-primary">
                                                    <span class="fa fa-folder-open"></span>
                                                    Buscar <input name="adj_evidencia_sol" type="file" style="display: none;" id="adj_evidencia_sol">
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" readonly placeholder='Adjunte evidencia' id="text_adj_evidencia">
                                        </div>
                                    </div>
                                <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active"><span
                                class="glyphicon glyphicon-floppy-disk"></span> <span
                                class="texto_seleccion">Crear</span></button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- MODALS DE LA PARTE DE VER SOLICITUD DE TICKET-->
    <!-- modal informacion completa de la solicitud -->
    <div class="modal fade" id="modal_detalle_solicitud_tickets" role="dialog">
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
                                <th class="nombre_tabla" colspan="2">Información del ticket</th>
                                <td class="sin-borde text-right border-left-none" colspan="6">
                                    <button type="button" class="btn btn-default btn_imprimir" id="btn_imprimir"><span
                                            class="fa fa-print red"></span> Imprimir</button>
                                    <button type="button" class="btn btn-default btn_log" id="btn_log"><span
                                            class="fa fa-history red"></span> Historial</button>
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
                                <td class="ttitulo" colspan="2">Fecha de cierre</td>
                                <td class="fecha_cierre" colspan="6"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Motivo del estado</td>
                                <td class="motivo" colspan="6"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Descripcion de la Solución</td>
                                <td class="description" colspan="6"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Documento(s) adjunto(s) </td>
                                <td colspan="6">
                                    <a id='adjunto' colspan="6" class='adjunto pointer'></a>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-responsive" id="tabla_responsables">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th class="nombre_tabla" colspan="8">Información de la Solicitud</th>
                            </tr>
                            <tr>
                                <td style="width: 50%;" class="ttitulo" colspan="2">Asunto</td>
                                <td style="width: 50%;" class="asunto" colspan="6"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Descripción</td>
                                <td class="descripcion" colspan="6"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="table-responsive" id="tabla_calificacion">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th class="nombre_tabla" colspan="8">Calificación</th>
                            </tr>
                            <tr>
                                <td style="width: 50%;" class="ttitulo" colspan="2">Tiempo Solucionado</td>
                                <td style="width: 50%;" class="t_solucion" colspan="6"></td>
                            </tr>
                            <tr>
                                <td style="width: 50%;" class="ttitulo" colspan="2">Calificación</td>
                                <td style="width: 50%;" class="c_solucion" colspan="6"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Tiempo Asignación</td>
                                <td class="t_asignacion" colspan="6"></td>
                            </tr>
                            <tr>
                                <td style="width: 50%;" class="ttitulo" colspan="2">Calificación</td>
                                <td style="width: 50%;" class="c_asignacion" colspan="6"></td>
                            </tr>
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

    <div class="modal fade" id="modal_historial_solicitud" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-history"></span> Historial</h3>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="margin-bottom:20px;">
                        <table class="table table-bordered table-hover table-condensed" id="tabla_estado_solicitud"
                            cellspacing="0" width="100%">
                            <thead class="ttitulo">
                                <tr>
                                    <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
                                <tr class="filaprincipal ">
                                    <td>No.</td>
                                    <td>Estado</td>
                                    <td>Descripcion</td>
                                    <td>Motivo</td>
                                    <td>Persona Registra</td>
                                    <td>Fecha</td>
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

    <form id="form_asignar_especialista" method="post">
        <div class="modal fade scroll-modal" id="modal_asignar_especialista" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-calendar"></span> Asignar Especialista</h3>
                    </div>
                    <div class="modal-body " id="bodymodal">
                        <div class="row">
                            <div class="agrupado col-md-14 text-left">
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="categoria" id="categoria" class="form-control cbxcategoria"
                                            title="Tipo">
                                            <option value="">Categoría</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="subcategoria" id="subcategoria"
                                            class="form-control cbxsubcategoria" title="Tipo">
                                            <option value="">Subcategoría</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body " id="bodymodal">
                        <div class="row">
                            <div class="agrupado col-md-14 text-left">
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="impacto" id="impacto" class="form-control cbximpacto"
                                            title="Tipo">
                                            <option value="">Impacto</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="urgencia" id="urgencia" class="form-control cbxurgencia"
                                            title="Tipo">
                                            <option value="">Urgencia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-14" style="padding:0px;">
                                    <div class="agro">
                                        <select name="prioridad" id="prioridad" class="form-control cbxprioridad"
                                            title="Tipo">
                                            <option value="">Prioridad</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if($administra || $soporte || $admin_soporte){ ?>
                            <div class="agro agrupado">
                                <div class="input-group">
                                    <input type="text" class="form-control sin_margin sin_focus" name="especialista"
                                        id='txt_especialista'>
                                    <span class="input-group-addon pointer" id='btn_especialista'
                                        style='background-color:white'><span class='fa fa-search red'></span>
                                        Buscar Especialista</span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active"><span
                                class="glyphicon glyphicon-floppy-disk"></span>
                            Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <div class="modal fade" id="modal_buscar_persona" role="dialog">
        <div class="modal-dialog modal-lg">
            <form id="form_buscar_persona" method="post">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Persona</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row" id="" style="width: 100%">
                            <div class="agrupado col-md-12 text-left">
                                <div class="col-md-8" style="padding:0px;">
                                    <div class="input-group agro">
                                        <input id='txt_documento_espec' class="form-control con_focus"
                                            placeholder="Ingrese usuario o nombre de la persona">
                                        <span class="input-group-btn"><button class="btn btn-default test"
                                                type="submit"><span class='fa fa-search red'></span>
                                                Buscar</button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive col-md-12" style="width: 100%">
                                <table class="table table-bordered table-hover table-condensed pointer"
                                    id="tabla_empleado_especialista" cellspacing="0" width="100%">
                                    <thead class="ttitulo ">
                                        <tr class="">
                                            <td colspan="4" class="nombre_tabla">TABLA EMPLEADOS</td>
                                        </tr>
                                        <tr class="filaprincipal">
                                            <td>No.</td>
                                            <td>Nombre Completo</td>
                                            <td>Usuario</td>
                                            <td>Hora inicio</td>
                                            <td>Hora final</td>
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
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal de administración -->
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
                        <nav class="navbar navbar-default" id="menu_administrar">
                            <div class="container-fluid">
                                <ul class="nav nav-tabs nav-justified">
                                    <li class="pointer active" id="permisos"><a><span class="fa fa-gears red"></span>
                                            Permisos</a></li>
                                    <li class="pointer" id="horario_funcionario"><a><span
                                                class="fa fa-calendar red"></span> Horario</a></li>
                                    </li>
                                </ul>
                            </div>
                        </nav>

                        <div id="container_permisos">
                            <div class="form-group">
                                <div class="input-group agro col-md-8">
                                    <input name="persona_soli" type="hidden" id="input_sele">
                                    <span id="s_persona" class="form-control text-left pointer sin_margin">Seleccione
                                        Persona</span>
                                    <span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men"
                                        title="Buscar Persona" data-toggle="popover" data-trigger="hover">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </span>
                                </div>
                            </div>
                            <table id="tabla_actividades" class="table table-bordered table-hover table-condensed"
                                cellspacing="0" width="100%">
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

                        <div id="container_horario_func" class="oculto">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_horarios"
                                cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                    <tr>
                                        <th class="nombre_tabla">TABLA HORARIOS</th>
                                        <td class="sin-borde text-right border-left-none" colspan="12">
                                            <button class="btn btn-default btn_horario"> <span
                                                    class="fa fa-plus red"></span> Nuevo Horario</button>
                                        </td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Día</td>
                                        <td>Hora Inicio</td>
                                        <td>Hora Descanso</td>
                                        <td>Tiempo Descanso</td>
                                        <td>Hora Fin</td>
                                        <td>Acción</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
    </form>

    <div class="modal fade" id="modal_suspender_servicio" role="dialog">
        <div class="modal-dialog">
            <form id="form_suspender_servicio" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"> <span> Suspender TICKET</span></h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <!-- <h4 class="ttitulo"><span>PACIENTE: </span> <span class="solicitante"></span></h4>        -->
                            <div id="mod_detalle">
                                <select name="id_motivo" class="form-control cbxsuspender">
                                    <option value=""">Seleccione motivo</option></select>  
                                     
                                        <div class=" clearfix">
                            </div>
                            <div>
                                <textarea name="descripcion_suspender" cols="1" rows="3" placeholder="Descripión"
                                    class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active"><span
                                class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>



<div class="modal fade" id="modal_buscar_empleado" role="dialog">
    <div class="modal-dialog modal-lg">
        <form id="form_buscar_empleado" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Empleado</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="agrupado col-md-12 text-left">
                            <div class="col-md-8" style="padding:0px;">
                                <div class="input-group agro">
                                    <input id='txt_documento_empleado' class="form-control con_focus"
                                        placeholder="Ingrese usuario o nombre de la persona">
                                    <span class="input-group-btn"><button class="btn btn-default test"
                                            type="submit"><span class='fa fa-search red'></span>
                                            Buscar</button></span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="width: 100%">
                            <table class="table table-bordered table-hover table-condensed pointer"
                                id="tabla_empleado_buscar" cellspacing="0" width="100%">
                                <thead class="ttitulo ">
                                    <tr class="">
                                        <td colspan="4" class="nombre_tabla">TABLA EMPLEADOS</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>No.</td>
                                        <td>Nombre Completo</td>
                                        <td>Usuario</td>
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
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>


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
                        <input type="text" class="form-control sin_margin" required="true" id='txt_persona'
                            placeholder="Buscar Persona" />
                        <span type="submit" class="input-group-addon pointer" id='btn_buscar_persona'
                            style='	background-color:white'><span class='fa fa-search red'></span></span>
                    </div><br>
                </form>
                <table id="tabla_personas" class="table table-bordered table-hover table-condensed" cellspacing="0"
                    width="100%">
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
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_solucionado" role="dialog">
    <div class="modal-dialog">
        <form id="form_solucionado" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-cloud-upload"></span> Brindar Solucion</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <label>Por favor agregue una breve descripcion de la solución</label>
                        <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
                        <div class="agrupado">
                            <div class="input-group">
                                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        <span class="fa fa-folder-open"></span>
                                        Buscar <input name="adj_evidencia" type="file" style="display: none;"
                                            id="adj_evidencia">
                                    </span>
                                </label>
                                <input type="text" class="form-control" readonly placeholder='Adjunte evidencia'
                                    id="text_adj_evidencia">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" id="btnfiltrar"><span
                            class="glyphicon glyphicon-ok"></span> Aceptar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
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
                <table id="tabla_estados" class="table table-bordered table-hover table-condensed" cellspacing="0"
                    width="100%">
                    <thead class="ttitulo">
                        <tr>
                            <th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
                        </tr>
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
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal crear horario funcionario -->
<div class="modal fade" id="modal_crear_horario" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="form_guardar_horario" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> <span class="titulo_modal"></span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_dia" id="id_dia" class="form-control cbxdia">
                                <option value="">Seleccione Día</option>
                            </select>
                        </div>
                        <div class="agrupado">
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy"
                                    data-link-field="dtp_input1">
                                    <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Inicio"
                                        type="text" value="" required="true" name="hora_inicio" id="hora_inicio">
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-remove red"></span></span>
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-calendar red"></span></span>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy"
                                    data-link-field="dtp_input1">
                                    <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Fin"
                                        type="text" value="" required="true" name="hora_fin" id="hora_fin">
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-remove red"></span></span>
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-calendar red"></span></span>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy"
                                    data-link-field="dtp_input1">
                                    <input class="form-control sin_focus sin_margin" size="16"
                                        placeholder="Hora Descanso" type="text" value="" required="true"
                                        name="hora_break" id="hora_break">
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-remove red"></span></span>
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-calendar red"></span></span>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date datetime_horario agro" data-date="" data-date-format="yyyy"
                                    data-link-field="dtp_input1">
                                    <input class="form-control sin_focus sin_margin" size="16"
                                        placeholder="Tiempo de Descanso" type="text" value="" required="true"
                                        name="tiempo_break" id="tiempo_break">
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-remove red"></span></span>
                                    <span class="input-group-addon pointer"><span
                                            class="glyphicon glyphicon-calendar red"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding: 0px;">
                            <textarea class="form-control inputt" name="descripcion" id="descripcion"
                                placeholder="Descripción"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span
                            class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- modal lista horarios funcionario -->
<div class="modal fade" id="modal_funcionarios_horarios" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Disponibilidad Funcionarios</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row" id="" style="width: 100%">
                    <div class="table-responsive col-md-12" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer"
                            id="tabla_funcionarios_horarios" cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                                <tr class="">
                                    <td class="nombre_tabla">TABLA FUNCIONARIOS</td>
                                    <td class="sin-borde text-right border-left-none" colspan="4">
                                        <span class="btn btn-default" id="asignar_funcionario_horario"> <span
                                                class="fa fa-plus red"></span> Asignar Funcionario</span>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Ver</td>
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
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<form id="form_buscar_persona_horario" method="post">
    <div class="modal fade" id="modal_buscar_persona_horario" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Persona</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="form-group agrupado col-md-8 text-left">
                            <div class="input-group">
                                <input id='txt_per_buscar' class="form-control"
                                    placeholder="Ingrese identificación o nombre de la persona">
                                <span class="input-group-btn"><button class="btn btn-default test" type="submit"><span
                                            class='fa fa-search red'></span> Buscar</button></span>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="width: 100%">
                            <table class="table table-bordered table-hover table-condensed pointer"
                                id="tabla_personas_busqueda" cellspacing="0" width="100%">
                                <thead class="ttitulo ">
                                    <tr class="">
                                        <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Ver</td>
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
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
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
                        <div class="col-md-6" style="padding: 0px;">
                            <div class="input-group date datetime_filtro agro" data-date="" data-date-format="yyyy"
                                data-link-field="dtp_input1">
                                <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Inicio"
                                    type="text" value="" name="hora_inicio_filtro" id="hora_inicio_filtro">
                                <span class="input-group-addon pointer"><span
                                        class="glyphicon glyphicon-remove red"></span></span>
                                <span class="input-group-addon pointer"><span
                                        class="glyphicon glyphicon-calendar red"></span></span>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding: 0px;">
                            <div class="input-group date datetime_filtro agro" data-date="" data-date-format="yyyy"
                                data-link-field="dtp_input1">
                                <input class="form-control sin_focus sin_margin" size="16" placeholder="Hora Fin"
                                    type="text" value="" name="hora_fin_filtro" id="hora_fin_filtro">
                                <span class="input-group-addon pointer"><span
                                        class="glyphicon glyphicon-remove red"></span></span>
                                <span class="input-group-addon pointer"><span
                                        class="glyphicon glyphicon-calendar red"></span></span>
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


<div class="tablausu col-md-12 " id="menu_principal"
    style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
    </div>
    <div id="container-principal2" class="container-principal-alt">
        <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
        <div class="row">
            <div class="pointer" id="nuevo_ticket">
                <div class="thumbnail">
                    <div class="caption" style="text-align:center;">
                        <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                        <span class="btn  form-control btn-Efecto-men">CREAR TICKETS</span>
                    </div>
                </div>
            </div>
            <div id="listado_solicitudes">
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
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



<script>
$(document).ready(function() {
    listado_solicitudes('<?php echo $id?>');
    Cargar_parametro_buscado_aux(17, ".cbxtipopersona", "Seleccione especialista");
    Cargar_parametro_buscado(233, ".cbxcategoria", "Seleccione categoría");
    Cargar_parametro_buscado(235, ".cbxsuspender", "Seleccione motivo");
    Cargar_parametro_buscado(236, ".cbxprioridad", "Seleccione Prioridad");
    Cargar_parametro_buscado(238, ".cbxurgencia", "Seleccione Urgencia");
    Cargar_parametro_buscado(239, ".cbximpacto", "Seleccione Impacto");
    Cargar_parametro_buscado_aux(237, ".cbxtipo_solicitud", "Seleccione Tipo Solitud");
    Cargar_parametro_buscado(100, ".cbxdia", "Seleccione Día");
    activarfile();
    inactivityTime();
});
</script>

<script type="text/javascript">
$(".datetime_horario").datetimepicker({
    formatViewType: 'time',
    fontAwesome: true,
    autoclose: true,
    startView: 1,
    maxView: 1,
    minView: 0,
    minuteStep: 5,
    format: 'hh:ii',
});
$(".datetime_filtro").datetimepicker({
    formatViewType: 'time',
    fontAwesome: true,
    autoclose: true,
    startView: 1,
    maxView: 1,
    minView: 0,
    minuteStep: 5,
    format: 'hh:ii',
});
</script>