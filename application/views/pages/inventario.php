<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<!-- Tabla Dependencia -->
<div class="container col-md-12 text-center" id="inicio-user">
    <div class="tablausu col-md-12 text-left">
        <div class="table-responsive col-sm-12 col-md-12  tablauser ">
            <?php if($tipo_modulo == 'Inv_Tec' || $tipo_modulo == 'Inv_Aud'){ ?>
            <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <?php } else { ?>
            <p class="titulo_menu pointer" id="btn_regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar
            </p>
            <?php } ?>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_dependencias"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                    <tr>
                        <td colspan="2" class="nombre_tabla">TABLA <span id='txtnombre_tabla'>UBICACIONES</span>
                        </td>
                        <td id="botones_tbl_dependencias" class="sin-borde text-right border-left-none" colspan="1"
                            style="display:flex;justify-content:flex-end;">
                            <?php if($admin || $_SESSION['perfil'] == 'Admin_Lab'){ ?>
                            <?php if($tipo_modulo == 'Inv_Lab'){ ?>
                            <a href="<?php echo base_url()?>index.php/laboratorios/exportar_inventario"
                                class="btn btn-default" id="btn_exportar"><span class="fa fa-cloud-download red"></span>
                                Exportar</a>
                            <?php } ?>
                            <span class="btn btn-default" id="btn_notificaciones"><span id="inventario_notificaciones"
                                    class="badge btn-danger">0</span> Notificaciones</span>
                            <div id="c_admin_solicitudes">
                                <span class="btn btn-default btnAgregar" style="margin-right: 5px;" id="cambiar_vista">
                                    <span class="fa fa-building red"></span> Cambiar lista
                                </span>
                            </div>
                            <div id="c_admin_solicitudes">
                                <span class="btn btn-default btnAgregar" id="btn_admin_solicitudes">
                                    <span class="fa fa-cogs red"></span> Administrar
                                </span>
                            </div>
                            <?php }?>
                            <?php if($admin || $permisos[0]['agregar'] == '1'){ ?>
                            <div id="c_buscar_serial" style="padding-right: 5px;padding-left: 5px;">
                                <span id="buscar_serial" class="btn btn-default"> <span class="fa fa-search red"></span>
                                    Buscar/Agregar </span>
                            </div>
                            <?php }?>
                            <?php if($admin || $_SESSION['perfil'] == 'Admin_Lab'){ ?>
                            <div id="c_buscar_responsables">
                                <span id='buscar_responsables' class='btn btn-default' style='margin-right: 0px;'>
                                    <span class='fa fa-user red'></span> Responsable
                                </span>
                            </div>
                            <?php }?>
                        </td>
                    </tr>
                    <tr class="filaprincipal ">
                        <td class="opciones_tbl">Ver</td>
                        <td>Dependencia</td>
                        <td class="opciones_tbl">Cantidad</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Fin Tabla Dependencia -->
<div class="modal fade" id="modal_notificaciones" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
            </div>
            <div class="modal-body" id="notificaciones_body">
                <div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
                <?php if($admin) { ?><div id="panel_notificaciones_inventario" style="width: 100%" class="list-group">
                </div><?php } ?>
                <div id="panel_notificaciones_investigacion" style="width: 100%" class="list-group"></div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubicaciones -->
<div class="modal fade" id="modal_ubicaciones" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-building"></span> Ubicaciones</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class='table-responsive'>
                    <table id="tabla_ubicaciones"
                        class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0"
                        width="100%">
                        <thead class="ttitulo ">
                            <tr>
                                <td colspan="3" class="nombre_tabla">LISTADO INVENTARIO</td>
                            </tr>
                            <tr class="filaprincipal ">
                                <td class="opciones_tbl">Ver</td>
                                <td>Ubicación</td>
                                <td>Cantidad</td>
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
<!-- Fin Modal Ubicaciones -->

<!-- Modal Inventario -->
<div class="modal fade" id="modal_inventario" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-desktop"></span> Tipos de Activos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-hover table-condensed table-responsive" id="tablainventario"
                    cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                        <tr>
                            <td colspan="4" class="nombre_tabla">TABLA INVENTARIO</td>
                        </tr>
                        <tr class="filaprincipal ">
                            <td class="opciones_tbl">Ver</td>
                            <td>Activos</td>
                            <td>Cantidad</td>
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
<!-- Fin Modal Inventario -->

<div class="modal fade" id="dispositivos_modal" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg modal-95">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Inventario</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class='table-responsive'>
                    <?php if($admin || $permisos[0]['gestionar'] == 1){ ?>
                    <div class="alert alert-warning oculto mensaje_notificacion" role="alert">
                        <h5>Seleccione la acción a realizar y haga click en <strong>Aceptar</strong></h5>
                        <form id="accion_masiva">
                            <label class="radio-inline">
                                <input type="radio" name="accion" value="ubi" required> Ubicación
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="accion" value="res" required> Responsable
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="accion" value="man" required> Mantenimiento
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="accion" value="rep"> Equipo Reparado
                            </label>
                            <br>
                            <span class="mensaje-filtro">
                                <span id="btncancelar" class="btn btn-xs"
                                    style="background-color: #d9534f; color: white;"><strong>Cancelar</strong></span> |
                                <span id="btnaceptar" class="btn btn-success btn-xs"><strong>Aceptar</strong></span>
                            </span>
                        </form>
                    </div>
                    <?php }?>
                    <table id="tbl_articulos" class="table table-bordered table-hover table-condensed table-responsive"
                        cellspacing="0" width="100%">
                        <thead class="ttitulo ">
                            <tr>
                                <td colspan="5" class="nombre_tabla">LISTADO INVENTARIO
                                    <!-- -<span id='tipo_recurso'></span><br>-->
                                    <span class="mensaje-filtro-detalle mensaje-filtro oculto">
                                        <span class="fa fa-bell red"></span>
                                        <span>La tabla tiene algunos filtros aplicados.</span>
                                    </span>
                                </td>
                                <td class="sin-borde text-right border-left-none" colspan="7">
                                    <div class='acciones_tabla'>
                                        <?php if($admin || $permisos[0]['modificar'] == '1'){ ?>
                                        <span id='add-especial' class='btn btn-default btnModifica'> <span
                                                class='fa fa-sort red'></span> Adm Estados </span>
                                        <span id="btnmodificar_inventario" class="btn btn-default btnModifica"> <span
                                                class="fa fa-wrench red"></span> Modificar </span>
                                        <?php } ?>
                                        <?php if($admin || $permisos[0]['gestionar'] == '1'){ ?>
                                        <span id="btnacciones" class="btn btn-default btnModifica"> <span
                                                class="fa fa-cogs red"></span> Acciones Masivas </span>
                                        <?php } ?>
                                        <span id="limpiar_filtros" class="btn btn-default"> <span
                                                class="fa fa-refresh red"></span> Limpiar</span>
                                    </div>
                                    <div class='mensaje_notificacion oculto'>
                                        <span id="seleccionar_todo" class="btn btn-default"> <span
                                                class="fa fa-check-square-o red"></span> Todo</span>
                                        <span id="limpiar_filtros_masivos" class="btn btn-default"> <span
                                                class="fa fa-refresh red"></span> Limpiar</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="filaprincipal ">
                                <td class="opciones_tbl">Ver</td>
                                <td>Activo</td>
                                <td>Serial</td>
                                <td>Nombre</td>
                                <td>Codigo_Int.</td>
                                <td>Marca</td>
                                <td>Modelo</td>
                                <td>Valor</td>
                                <td>Lugar</td>
                                <td>Ubicación</td>
                                <td>Estado</td>
                                <td>Accion</td>
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

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <form id="Guardar_inventario" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-floppy-disk"></span> Registro de Activo
                    </h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row" style="width: 100%;margin: 0;">
                        <div class="alert alert-warning" role="alert" style="margin-left: 15px;margin-right: 15px;">
                            <span class="mensaje-alert"></span>
                        </div>
                        <h4 class="title-form" style="margin-left: 15px;"><span class="fa fa-tv ttitulo"></span> Tipo de
                            Activo</h4>
                        <div class="row" style="width:100%;">
                            <div class="col-md-4">
                                <input id="lista_recursos" list="dataRecursos" class="form-control"
                                    placeholder="Seleccione Tipo de Activo" required>
                                <datalist id="dataRecursos"></datalist>
                            </div>
                            <?php if($tipo_modulo == "Inv_Lab"){?>
                            <div class="col-md-4">
                                <input type="text" name="nombre_activo" required class="form-control"
                                    placeholder="Nombre del Activo">
                            </div>
                            <div class="col-md-4">
                                <select name="uso_activo" required class="form-control cbxusos_activo">
                                    <option value="">Uso del Activo</option>
                                </select>
                            </div>
                            <?php }?>
                        </div>
                        <h4 class="title-form" style="margin-left: 15px;"><span class="fa fa-user ttitulo"></span>
                            Información Responsable</h4>
                        <div class="col-md-4">
                            <select name="id_lugar" required class="form-control"></select>
                        </div>
                        <div class="col-md-4">
                            <select name="id_ubicacion" required class="form-control">
                                <option value="">Seleccione Ubicación</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group agro">
                                <select name="personal_asignado" class="form-control sin_margin" id="personal_asignado">
                                    <option value="">0 Responsable(s)</option>
                                </select>
                                <span class="input-group-addon  btnEliminaResponsable pointer " id="retirar_responsable"
                                    title="Retirar Responsable" data-toggle="popover" data-trigger="hover"><span
                                        class="glyphicon glyphicon-remove red "></span></span>
                                <span class="input-group-addon  btnAgregarResponsable pointer" id="agregar_responsable"
                                    title="Agregar Responsable" data-toggle="popover" data-trigger="hover"><span
                                        class="glyphicon glyphicon-plus red "></span> </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div id="tipoGeneral">
                            <h4 class="title-form" style="margin-left: 15px;"><span
                                    class="fa fa-file-text-o ttitulo"></span> Información del Dispositivo</h4>
                            <div class="col-md-6 agrupado">
                                <input type="text" name="codigo_interno" class="form-control inputt2 sin_margin"
                                    placeholder="<?php if($tipo_modulo == "Inv_Lab"){?>Número del Activo<?php } else {?>Codigo Interno<?php }?>"
                                    id="codigo_interno" <?php if($tipo_modulo === 'Inv_Aud') echo "required" ?>>
                            </div>
                            <div class="col-md-6 agrupado">
                                <input type="text" name="serial" class="form-control inputt2 CampoGeneral sin_margin"
                                    placeholder="Serial" required>
                            </div>

                            <div class="col-md-6 agrupado">
                                <select name="marca" required class="form-control cbxmarcas sin_margin">
                                    <option value="">Seleccione Marca</option>
                                </select>
                            </div>
                            <div class="col-md-6 agrupado">
                                <select name="modelo" required class="form-control sin_margin cbxmodelo">
                                    <option value="">Seleccione Modelo</option>
                                </select>
                            </div>

                            <?php if($tipo_modulo == "Inv_Lab"){?>
                            <div class="col-md-6">
                                <input type="text" name="referencia" class="form-control" placeholder="Referencia">
                            </div>
                            <div class="agro agrupado col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control sin_margin sin_focus" name="buscar_proveedor"
                                        id='txt_Buscar_proveedor'>
                                    <span name="proveedor_asignado" class="input-group-addon pointer"
                                        id='btnBuscar_Proveedor' style='background-color:white'><span
                                            class='fa fa-search red'></span>
                                        Buscar Proveedor
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="text" name="lugar_origen" class="form-control"
                                    placeholder="Lugar de Origen">
                            </div>
                            <?php }?>

                            <br>
                            <div id="computadores"></div>
                            <div class="col-md-4">
                                <div class="agrupado input-group">
                                    <span class="input-group-addon" style='	background-color:white'><span
                                            class='fa fa-calendar red'></span> Fecha Ingreso</span>
                                    <input type="date" required class="form-control sin_margin" name='fecha_ingreso'>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="agrupado input-group">
                                    <span class="input-group-addon" style='	background-color:white'><span
                                            class='fa fa-calendar red'></span> Fecha Garantia</span>
                                    <input type="date" required class="form-control sin_margin" name='fecha_garantia'>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="agrupado input-group" ">
                                    <span class=" input-group-addon" style='	background-color:white'>$</span>
                                    <input type="number" class="form-control sin_margin" name='valor'
                                        placeholder="Valor (Opcional)" min="0">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <input type="text" required name="descripcion" placeholder="Descripcion del Artículo"
                                    class="form-control">
                            </div>
                            <?php if($tipo_modulo == "Inv_Lab"){?>
                            <div class="col-md-12">
                                <textarea class="form-control" id="observaciones" cols="1" rows="3" name="observaciones"
                                    placeholder="Observaciones"></textarea>
                            </div>
                            <?php }?>

                            <div class="col-md-12">
                                <div class="agro accordion">
                                    <div class="panel-group" id="accordion">
                                        <?php if($tipo_modulo == "Inv_Tec" || $tipo_modulo == "Inv_Aud") { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                                    <h4 class="panel-title">
                                                        <span class="fa fa-book ttitulo"></span> Proyecto de
                                                        Investigación
                                                    </h4>
                                                </a>
                                            </div>
                                            <div id="collapseTwo" class="panel-collapse collapse">
                                                <div class="panel-body" style="padding-bottom: 10px;">
                                                    <div class="col-md-4">
                                                        <div class="agro agrupado">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"
                                                                    style='	background-color:white'><span
                                                                        class='fa fa-calendar red'></span> Fecha
                                                                    Inicio</span>
                                                                <input type="date" class="form-control sin_margin"
                                                                    name='fecha_inicio_proyecto'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="agro agrupado">
                                                            <div class="input-group">
                                                                <span class="input-group-addon"
                                                                    style='	background-color:white'><span
                                                                        class='fa fa-calendar red'></span> Fecha
                                                                    Fin</span>
                                                                <input type="date" class="form-control sin_margin"
                                                                    name='fecha_fin_proyecto'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div id="div_articulo" class="input-group agro">
                                                            <span class="form-control text-left pointer sel_art"
                                                                id="sap_input">Seleccione Codigo SAP</span>
                                                            <span
                                                                class="input-group-addon red_primari pointer btn-Efecto-men sel_art"
                                                                id="sap_search" title="" data-toggle="popover"
                                                                data-trigger="hover"><span
                                                                    class="glyphicon glyphicon-search"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>



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

<div class="modal fade" id="ModalModificarInventario" role="dialog">
    <div class="modal-dialog">
        <form id="Modificar_inventario">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-random"></span> Modificar Inventario</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <h4><span class="fa fa-file-text-o ttitulo"></span> Información del Dispositivo</h4>
                        <div class="error form-group has-error text-center oculto"></div>
                        <div id="tipo_modificar">
                            <div style="display:flex; align-content: center;">
                                <label for="serial" style="margin: 5px 0px 0px 0px;width: 20%;">Serial:</label>
                                <input type="text" name="serial" class="form-control" placeholder="Serial">
                            </div>
                            <div style="display:flex;align-content: center;">
                                <label for="codigo_interno" style="margin: 5px 0px 0px 0px;width: 20%;">Código
                                    Interno:</label>
                                <input type="text" name="codigo_interno" class="form-control"
                                    placeholder="Código Interno">
                            </div>
                            <?php if($tipo_modulo != 'Inv_Lab'){?>
                            <div class="input-group margin1">
                                <select name='sistema_operativo' required
                                    class='form-control sin_margin inputt cbxSistemaOperativo valor_sistemaope_mod '></select>
                                <span class="input-group-addon">-</span>
                                <select name='procesador' required
                                    class='form-control inputt sin_margin cbxprocesador valor_procesador_mod'></select>
                            </div>
                            <div class="agro">
                                <div class="input-group">
                                    <input type="text" name="disco_duro" required
                                        class="form-control inputt2 sin_margin valor_discoduro_mod "
                                        placeholder="Disco Duro">
                                    <span class="input-group-addon">-</span>
                                    <input type="text" name="memoria" required
                                        class="form-control inputt2 sin_margin valor_memoria_mod" placeholder="Memoria">
                                </div>
                            </div>
                            <?php }?>
                        </div>
                        <?php if($tipo_modulo == 'Inv_Lab'){?>
                        <div style="display:flex; align-content: center;">
                            <label for="nombre_activo_modi" style="margin: 5px 0px 0px 0px;width: 20%;">Nombre
                                Activo:</label>
                            <input type="text" name="nombre_activo_modi" class="form-control"
                                placeholder="Nombre del Activo">
                        </div>
                        <div style="display:flex; align-content: center;">
                            <label for="referencia" style="margin: 5px 0px 0px 0px;width: 20%;">Referencia:</label>
                            <input type="text" name="referencia" class="form-control" placeholder="Referencia">
                        </div>
                        <div class="input-group margin1">
                            <select name='uso_equipo' required
                                class='form-control sin_margin inputt cbxusos_activo valor_uso_mod'></select>
                            <span class="input-group-addon">-</span>
                            <select name='tipo_activo' id="tipo_activo" required
                                class='form-control sin_margin inputt cbxtipo_activo valor_tipo_mod'></select>
                        </div>
                        <div class="input-group margin1">
                            <input type="text" name="lugar_origen" required
                                class="form-control inputt2 sin_margin lugar_origen_mod" placeholder="Lugar de origen">
                            <span class="input-group-addon">-</span>
                            <input type="number" name="valor" required id="valor_mod"
                                class="form-control inputt2 sin_margin" placeholder="Valor (Opcional)" min="0">
                        </div>


                        <div class="agrupado input-group margin1">
                            <span class="input-group-addon" style='	background-color:white'><span
                                    class='fa fa-calendar red'></span> Fecha Ingreso</span>
                            <input type="date" required class="form-control sin_margin" name='fecha_ingreso'>
                        </div>

                        <div class="agrupado input-group margin1">
                            <span class="input-group-addon" style='	background-color:white'><span
                                    class='fa fa-calendar red'></span> Fecha Garantia</span>
                            <input type="date" required class="form-control sin_margin" name='fecha_garantia'>
                        </div>

                        <div class="agro">
                            <div class="input">
                                <select name='marca' required id="cbxmarcas_mod"
                                    class='form-control margin1 sin_margin inputt marca_mod input-group margin1'
                                    placeholder="marca"></select>
                                <span class="input-group margin1"></span>
                                <select name='modelo_mod' required id="cbxmodeloMod"
                                    class='form-control margin1 sin_margin inputt input-group margin1'>Seleccione
                                    Modelo</select>
                            </div>
                        </div>
                        <div class="input-group margin1">
                            <input type="text" class="form-control sin_margin sin_focus" name="buscar_proveedor"
                                id='txt_Buscar_proveedor_mod'>
                            <span name="proveedor_asignado" class="input-group-addon pointer"
                                id='btnBuscar_Proveedor_mod' style='background-color:white'><span
                                    class='fa fa-search red'></span>
                                Buscar Proveedor
                            </span>
                        </div>
                        <?php }?>
                        <textarea class="form-control" id="descripcion_modi" cols="1" rows="3" name="descripcion"
                            placeholder="Descripcion Adicional"></textarea>
                        <?php if($tipo_modulo == 'Inv_Lab'){?>
                        <textarea class="form-control" id="observaciones_modi" cols="1" rows="3" name="observaciones"
                            placeholder="Observaciones"></textarea>
                        <?php }?>
                        <?php if($tipo_modulo != 'Inv_Lab'){?>
                        <div class="agro accordion">
                            <div class="panel-group" id="accordion_mod">
                                <div class="panel panel-default accordion_investigacion">
                                    <div class="panel-heading">
                                        <a data-toggle="collapse" data-parent="#accordion_mod" href="#collapseTwo_mod">
                                            <h4 class="panel-title">
                                                <span class="fa fa-book ttitulo"></span> Proyecto de Investigación
                                            </h4>
                                        </a>
                                    </div>
                                    <div id="collapseTwo_mod" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="agro agrupado">
                                                <div class="input-group">
                                                    <span class="input-group-addon"
                                                        style='	background-color:white'><span
                                                            class='fa fa-calendar red'></span> Fecha Inicio</span>
                                                    <input type="date" class="form-control sin_margin"
                                                        name='fecha_inicio_proyecto'>
                                                </div>
                                            </div>
                                            <div class="agro agrupado">
                                                <div class="input-group">
                                                    <span class="input-group-addon"
                                                        style='	background-color:white'><span
                                                            class='fa fa-calendar red'></span> Fecha Fin</span>
                                                    <input type="date" class="form-control sin_margin"
                                                        name='fecha_fin_proyecto'>
                                                </div>
                                            </div>
                                            <div id="div_articulo" class="input-group agro">
                                                <span class="form-control text-left pointer "
                                                    id="btn_modificar_cod"></span>
                                                <span
                                                    class="input-group-addon red_primari pointer btn-Efecto-men sel_art"
                                                    id="btn_buscar_mod" title="" data-toggle="popover"
                                                    data-trigger="hover" data-original-title="Buscar Codigo Sap"><span
                                                        class="glyphicon glyphicon-search"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span
                            class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="Modal-info-dispositivo" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span> Información Dispositivo</h3>
            </div>

            <div class="modal-body" id="bodymodal">
                <nav class="navbar navbar-default" id="nav_inventario" style="display: flex;">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <?php if($tipo_modulo != "Inv_Lab"){?>
                            <li class="pointer" id="inventario_perifericos"><a><span class="fa fa-plug red"></span>
                                    Perifericos</a></li>
                            <?php }?>
                            <?php if($tipo_modulo == "Inv_Lab") {?>
                            <li class="pointer" id="btndocumentos_recurso"><a><span class="fa fa-archive red"></span>
                                    Documentos</a></li>
                            <?php }?>
                            <li class="pointer" id="inventario_responsables"><a><span class="fa fa-user red"></span>
                                    Responsables</a></li>
                            <li class="pointer" id="inventario_lugares"><a><span class="fa fa-building red"></span>
                                    Lugares</a></li>
                            <li class="pointer" id="inventario_mantenimiento"><a><span class="fa fa-cogs red"></span>
                                    Mantenimiento</a></li>
                            <?php if($tipo_modulo == "Inv_Lab") {?>
                            <li class="pointer" id="btnBitacora_mantenimiento"><a><span class="fa fa-cogs red"></span>
                                    Bitacora</a></li>
                            <?php }?>
                            <li class="pointer" id="ver_modificaciones"><a><span class="fa fa-cogs red"></span>
                                    Modificaciones</a></li>
                        </ul>
                    </div>
                </nav>
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="6"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo" colspan="2">Activo:</td>
                        <td class="valor_recurso" colspan="2"></td>
                    </tr>
                    <tr>
                        <?php if($tipo_modulo == "Inv_Lab") {?>
                        <td class="ttitulo" colspan="1">Nombre:</td>
                        <td class="valor_nombre_activo" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Serial: </td>
                        <td class="valor_serial" colspan="1"></td>
                        <?php }else{?>
                        <td class="ttitulo" colspan="1">Serial: </td>
                        <td class="valor_serial" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Codigo Interno: </td>
                        <td class="valor_cod_in" colspan="1"></td>
                        <?php }?>
                    </tr>
                    <tr>
                        <td class="ttitulo" colspan="1">Marca: </td>
                        <td class="valor_marca" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Modelo:</td>
                        <td class="valor_modelo" colspan="1"></td>
                    </tr>
                    <?php if($tipo_modulo == "Inv_Lab") {?>
                    <tr>
                        <td class="ttitulo" colspan="1">Referencia: </td>
                        <td class="valor_referencia" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Codigo Interno: </td>
                        <td class="valor_cod_in" colspan="1"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo" colspan="1">Uso del Activo: </td>
                        <td class="valor_uso" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Lugar Origen:</td>
                        <td class="valor_lugar" colspan="1"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo" colspan="1">Proveedor:</td>
                        <td class="valor_proveedor" colspan="3"></td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td class="ttitulo" colspan="1">Fecha Ingreso: </td>
                        <td class="valor_ingreso" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Fecha Garantia:</td>
                        <td class="valor_garantia" colspan="1"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo" colspan="1">Valor: </td>
                        <td class="valor_valor" colspan="1"></td>
                        <td class="ttitulo" colspan="1">Descripción:</td>
                        <td class="valor_descripcion" colspan="1"></td>
                    </tr>
                    <tr id="msj_motivo_baja">
                        <td class="ttitulo" colspan="1">Motivo de Baja: </td>
                        <td class="valor_baja" colspan="3"></td>
                    </tr>
                    <?php if($tipo_modulo != "Inv_Lab") {?>
                    <tr id="msj_periferico" hidden>
                        <td class="ttitulo" colspan="1">Asignado a activo: </td>
                        <td class="valor_periferico" colspan="3"></td>
                    </tr>
                    <?php } else {?>
                    <tr>
                        <td class="ttitulo" colspan="1">Observaciones: </td>
                        <td class="valor_observaciones" colspan="3"></td>
                    </tr>
                    <?php }?>
                </table>

                <?php if($tipo_modulo == "Inv_Lab") {?>
                <table class="table table-bordered table-condensed" id="tabla_datos_tecnicos">
                    <tr>
                        <th class="nombre_tabla" colspan="6"> Datos Técnicos</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Tecnología predominante: </td>
                        <td colspan="5" class="valor_tecnologia"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fases:</td>
                        <td class="valor_fase"></td>
                        <td class="ttitulo">Estado: </td>
                        <td class="valor_estado"></td>
                        <td class="ttitulo">Vida Útil: </td>
                        <td class="valor_vida_util"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Potencia:</td>
                        <td class="valor_potencia"></td>
                        <td class="ttitulo">Voltaje: </td>
                        <td class="valor_voltaje"></td>
                        <td class="ttitulo">Peso: </td>
                        <td class="valor_peso"></td>
                    </tr>
                </table>
                <div id="div_requerimientos">
                    <legend>Requerimientos Técnicos</legend>
                    <ol class="breadcrumb" id="lista_requerimientos"></ol>
                </div>
                <?php }?>
                <!-- <div id="detalle_inventario" class="pointer"><span class="glyphicon glyphicon-th-list"></span><span ><a class="ttitulo"> Detalle</A></span></div> -->
                <br>
                <div id="tabla_detalle_recurso" class="oculto">
                    <table class="table table-bordered table-condensed  tabla_info_inventario">
                        <tr>
                            <th class="nombre_tabla" colspan="4"> Información Adicional<span
                                    id="ocultar_tabla_detalle_recurso" class="text-right pointer"></span></th>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="1">Sistema Operativo:</td>
                            <td class="valor_sistemaope" colspan="1"></td>
                            <td class="ttitulo" colspan="1">Procesador: </td>
                            <td class="valor_procesador" colspan="1"></td>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="1">Disco Duro: </td>
                            <td class="valor_discoduro" colspan="1"></td>
                            <td class="ttitulo" colspan="1">Memoria:</td>
                            <td class="valor_memoria" colspan="1"></td>
                        </tr>
                    </table>
                </div>
                <div id="tabla_detalle_proyecto" class="">
                    <table class="table table-bordered table-condensed  tabla_info_inventario">
                        <tr>
                            <th class="nombre_tabla" colspan="4"> Información Proyecto<span
                                    id="ocultar_tabla_detalle_recurso" class="text-right pointer"></span></th>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="1">Fecha Inicio Proyecto:</td>
                            <td class="fecha_inicio_proyecto" colspan="1"></td>
                            <td class="ttitulo" colspan="1">Fecha Fin Proyecto: </td>
                            <td class="fecha_fin_proyecto	" colspan="1"></td>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="2">Codigo Sap </td>
                            <td class="id_codigo_sap" colspan="2"></td>
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


<div class="modal fade" id="Modal-responsables" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-user"></span> Administrar Responsables</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive" id="mostrar-responsable">
                    <table class="table table-bordered table-hover" id="tabla-responsables" style="width: 100%">
                        <thead class="ttitulo">
                            <tr class="">
                                <td class=" nombre_tabla" colspan="4">Historial de Responsables</td>
                                <td class="btnAgregar sin-borde text-center">
                                    <?php if($admin || $permisos[0]['gestionar'] == 1){ ?>
                                    <span class="btn btn-default" id="nuevo-responsable" title="Nuevo Responsable"
                                        data-toggle="popover" data-trigger="hover">
                                        <span class="fa fa-plus red"> </span> Agregar
                                    </span>
                                    <?php }?>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Ver</td>
                                <td>Persona</td>
                                <td>Fecha Asigna</td>
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
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal_nuevo_responsable" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-indent-right"></span> Asignar Nuevo Responsable
                </h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div id="for-nuevo-responsbale">
                    <div class="input-group" style="padding:0px;margin: 0 auto;">
                        <select id="departamento_sele_traslado" name="departamento" required
                            class="form-control cbxlugares sin_margin"></select> <br><br>
                        <select id="cargo_traslado" name="modelo" required class="form-control sin_margin cbx_cargo">
                            <option value="">Seleccione Cargo Responsable</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button id="btn-guardar-responsable" class=" btn btn-danger active"><span
                        class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal_detalle_responsable" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span> Información Responsable</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="4"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Responsable </td>
                        <td class="persona"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Departamento </td>
                        <td class="departamento"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Cargo </td>
                        <td class="cargo"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Asignado por</td>
                        <td class="persona_agrega"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Asigna</td>
                        <td class="fecha_asigna"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Retirado por </td>
                        <td class="persona_elimina"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Elimina</td>
                        <td class="fecha_elimina"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Estado </td>
                        <td class="estado"></td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>



<form id="form_buscar_persona" method="post">
    <div class="modal fade" id="modal_buscar_persona" role="dialog">
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
                                <input id='txt_dato_buscar' class="form-control"
                                    placeholder="Ingrese identificación o nombre del docente">
                                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span
                                            class='fa fa-search red'></span> Buscar</button></span>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="width: 100%">
                            <table class="table table-bordered table-hover table-condensed pointer"
                                id="tabla_persona_busqueda" cellspacing="0" width="100%">
                                <thead class="ttitulo ">
                                    <tr class="">
                                        <td colspan="4" class="nombre_tabla">TABLA PERSONAS</td>
                                    </tr>
                                    <tr class="filaprincipal">
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
<div class="modal fade" id="modal_tb_lugares" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-building"></span> Lugares</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover " id="tabla_lugares" style="width: 100%">
                        <thead class="ttitulo">
                            <tr class="">
                                <td class=" nombre_tabla" colspan="4">Tabla de Lugares</td>
                                <td class="btnAgregar sin-borde text-center">
                                    <?php if($admin || $permisos[0]['gestionar'] == '1'){ ?>
                                    <span class="btn btn-default" id="nuevo_lugar" title="Nuevo Responsable"
                                        data-toggle="popover" data-trigger="hover">
                                        <span class="fa fa-plus red"> </span> Agregar
                                    </span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Ver</td>
                                <td>Lugar</td>
                                <td>Ubicación</td>
                                <td>Fecha Asignado</td>
                                <td>Estado</td>
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
<div class="modal fade" id="modal_agregar_lugar_nuevo" role="dialog">
    <div class="modal-dialog">
        <form id="form_agregar_lugar_nuevo" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-building"></span> Agregar Lugar</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <select name="id_lugar" required class="form-control"></select>
                        <select name="id_ubicacion" required class="form-control">
                            <option value="">Seleccione ubicación</option>
                        </select>
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
<div class="modal fade" id="modal_listar_perifericos" role="dialog">
    <div class="modal-dialog modal-lg modal-95">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span> Periféricos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive" id="mostrar-perifericos">
                    <table class="table table-bordered table-hover " id="tabla_perifericos_sol" style="width: 100%">
                        <thead class="ttitulo">
                            <tr class="">
                                <td class=" nombre_tabla" colspan="5">Listado Periféricos</td>
                                <td class="btnAgregar sin-borde text-center">
                                    <span class="btn btn-default " id="agregar_periferico" title="Agregar Periferico"
                                        data-toggle="popover" data-trigger="hover">
                                        <span class="fa fa-plus red"> </span> Agregar
                                    </span>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Tipo</td>
                                <td>Codigo Interno</td>
                                <td>Serial</td>
                                <td>Valor</td>
                                <td>Descripcion</td>
                                <td>Acción</td>
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

<div class="modal fade" id="modal_modificar_periferico" role="dialog">
    <div class="modal-dialog ">
        <form id="form_modificar_periferico" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-television"></span> Modificar Periférico</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div id="tipoGeneral">
                            <h4><span class="fa fa-file-text-o ttitulo"></span> Información del Dispositivo</h4>
                            <select name="tipo" required class="form-control tipo" id="tipo_agregar_mod"></select>
                            <div class="input-group margin1">
                                <input type="text" name="codigo_interno"
                                    class="form-control inputt2 sin_margin codigo_interno" placeholder="Codigo Interno">
                                <span class="input-group-addon">-</span>
                                <input type="text" name="serial"
                                    class="form-control inputt2 CampoGeneral sin_margin serial" placeholder="Serial"
                                    required>
                            </div>
                            <input type="number" name="valor" min="0" placeholder="Valor " class="form-control valor">
                            <textarea class="form-control descripcion" cols="1" rows="3" name="descripcion"
                                placeholder="Descripcion Adicional"></textarea>
                        </div>
                        <div id="computadores">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span
                            class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="Buscar_Codigo" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title">Buscar Codigo SAP</h3>
            </div>
            <div class="modal-body" id="bodymodal">

                <form id="Buscar_Codigo_Orden" method="post">
                    <div class="input-group agro col-md-8">
                        <input type="text" class="form-control inputt2" name="codigo_sap" placeholder="Ingrese Dato"
                            id="txtcodigo_sap" autocomplete="off">
                        <span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_cod_sap"
                            title="Buscar Código" data-toggle="popover" data-trigger="hover"><span
                                class="glyphicon glyphicon-search"></span></span>
                    </div>
                </form>
                <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_codigos"
                    cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                        <tr>
                            <th colspan="2" class="nombre_tabla">TABLA DATOS</th>
                        </tr>
                        <tr class="filaprincipal ">
                            <td>Nombre</td>
                            <td>Descripción</td>
                            <td>Acción</td>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="submit" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_periferico" role="dialog">
    <div class="modal-dialog">
        <form id="frm_agregar_periferico" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-television"></span> Asignar Periférico</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div id="tipoGeneral">
                            <h4><span class="fa fa-file-text-o ttitulo"></span> Información del Dispositivo</h4>
                            <select name="tipo" required class="form-control" id="tipo_agregar"></select>
                            <div class="input-group margin1">
                                <input type="text" name="codigo_interno" class="form-control inputt2 sin_margin"
                                    placeholder="Codigo Interno">
                                <span class="input-group-addon">-</span>
                                <input type="text" name="serial" class="form-control inputt2 CampoGeneral sin_margin"
                                    placeholder="Serial" required>
                            </div>
                            <input type="number" name="valor" min="0" placeholder="Valor " class="form-control">
                            <textarea class="form-control" cols="1" rows="3" name="descripcion"
                                placeholder="Descripcion Adicional"></textarea>
                        </div>
                        <div id="computadores">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span
                            class="glyphicon glyphicon-floppy-disk"></span> Asignar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_detalle_perifericos" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-plug"></span> Información Periférico</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="4"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Activo </td>
                        <td class="recurso"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Serial </td>
                        <td class="serial"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Codigo Interno</td>
                        <td class="codigo_interno"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Marca</td>
                        <td class="marca"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Modelo </td>
                        <td class="modelo"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Asigna</td>
                        <td class="fecha_registra"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Usuario Asigna</td>
                        <td class="persona"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Estado</td>
                        <td class="estado"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalle_responsable_recurso" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span> Información Responsable</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="4"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Activo </td>
                        <td class="recurso"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Serial </td>
                        <td class="serial"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Codigo Interno </td>
                        <td class="codigo_interno"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Lugar </td>
                        <td class="lugar"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Ubicacion </td>
                        <td class="ubicacion"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Asignado por</td>
                        <td class="usuario_asigna"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Asigna</td>
                        <td class="fecha_asigna"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Retirado por </td>
                        <td class="usuario_retira"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Elimina</td>
                        <td class="fecha_elimina"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Estado </td>
                        <td class="estado"></td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_modificaciones_solicitud" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Historial de Modificaciones</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-condensed" id="tabla_modificaciones_solicitud"
                        cellspacing="0" width="100%">
                        <thead class="ttitulo">
                            <tr>
                                <th colspan="4" class="nombre_tabla">TABLA MODIFICACIONES</th>
                            </tr>
                            <tr class="filaprincipal">
                                <td>No.</td>
                                <td>Campo</td>
                                <td>Anterior</td>
                                <td>Actual</td>
                                <td>Fecha</td>
                                <td>Usuario Modifica</td>
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

<div class="modal fade" id="Modal_administrar_solicitudes" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <nav class="navbar navbar-default" id="nav_admin_inventario" style="display: flex;">
                    <div class="container-fluid">

                        <ul class="nav navbar-nav">
                            <li class="pointer" id="admin_tipo_recursos"><a><span class="fa fa-desktop red"></span> Tipo
                                    Recursos</a></li>
                            <li class="pointer" id="admin_marcas"><a><span class="fa fa-copyright red"></span>
                                    Marcas</a></li>
                            <li class="pointer" id="admin_modelos"><a><span class="fa fa-clone red"></span> Modelos</a>
                            </li>
                            <?php if($tipo_modulo == 'Inv_Tec'){?>
                            <li class="pointer" id="admin_procesadores"><a><span class="fa fa-code-fork red"></span>
                                    Procesadores</a></li>
                            <li class="pointer" id="admin_so"><a><span class="fa fa-desktop red"></span> Sistemas
                                    Operativos</a></li>
                            <?php if($admin && $_SESSION['perfil'] != 'Admin_Aud'){ ?>
                            <li class="pointer" id="admin_permisos"><a><span class="fa fa-dot-circle-o"></span>
                                    Permisos</a></li>
                            <?php } } ?>
                        </ul>
                    </div>
                </nav>
                <div class="table-responsive">

                    <div id="container_admin_valores" class="oculto">
                        <table class="table table-bordered table-hover table-condensed" id="tabla_valores_parametros"
                            cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="3" class="nombre_tabla" id="nombre_tabla_cu_or"></td>
                                    <td class="btnAgregar sin-borde text-center">
                                        <span data-toggle="modal" data-target="#modal_nuevo_valor"
                                            class="btn btn-default"><span class="fa fa-plus red"></span>
                                            Nueva</span>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Ver</td>
                                    <td>Nombre</td>
                                    <td>Descripción</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <?php if($admin && $_SESSION['perfil'] !== 'Admin_Aud'){ ?>
                    <div id="div_administrar_permisos">
                        <form id="form_search_person">
                            <div class="input-group" style="width:350px; float: right;padding-bottom: 10px;">
                                <input type="search" id="txt_search_person" class="form-control"
                                    placeholder="Buscar Persona">
                                <span type="submit" id="btn_search_person" class="input-group-addon btn red"><span
                                        class="fa fa-search"></span></span>
                            </div>
                        </form>
                        <table class="table table-bordered table-hover table-condensed" id="tabla_personas_permisos"
                            cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="2" class="nombre_tabla" id="nombre_tabla_cu_or">Tabla Personas</td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td class="opciones_tbl">#</td>
                                    <td>Nombre</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal_administrar_permisos" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Permisos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <nav class="navbar navbar-default" id="nav_admin_permisos" style="display: flex;">
                    <div class="container-fluid">
                        <ul class="nav navbar-nav">
                            <li class="pointer" id="per_equipos_asignados"><a><span class="fa fa-check-square-o"></span>
                                    Asignados</a></li>
                        </ul>
                    </div>
                </nav>
                <div style="display: inline-flex; flex-direction: row;justify-content: center;width: 100%;">
                    <div class="funkyradio permisos">
                        <div class="funkyradio-success solicitud">
                            <input type="checkbox" id="per_agregar" name="agregar" />
                            <label for="per_agregar" title="Permiso Agregar"> Agregar</label>
                        </div>
                        <div class="funkyradio-success solicitud">
                            <input type="checkbox" id="per_modificar" name="modificar" />
                            <label for="per_modificar" title="Permiso Modificar"> Modificar</label>
                        </div>
                        <div class="funkyradio-success solicitud">
                            <input type="checkbox" id="per_gestionar" name="gestionar" />
                            <label for="per_gestionar" title="Permiso Gestionar"> Gestionar</label>
                        </div>
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

<div class="modal fade" id="modal_gestion_modelos" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"> <span class="fa fa-clone"></span> Modelos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-condensed" id="tabla_modelos" cellspacing="0"
                        width="100%">
                        <thead class="ttitulo">
                            <tr>
                                <th colspan="3" class="nombre_tabla">TABLA MODELOS</th>
                            </tr>
                            <tr class="filaprincipal">
                                <td>No.</td>
                                <td>Modelo</td>
                                <td>Acción</td>
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

<div class="modal fade con-scroll-modal" id="modal_detalle_parametro" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X </button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Información General</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed  margin1 ajustar"
                        id="tabla_detalle_traslado_comite">
                        <tr class="">
                            <th class="nombre_tabla" colspan="5"> Datos</th>
                        </tr>
                        <tr>
                            <td class="ttitulo"> Nombre: </td>
                            <td id="nombre_parametro"></td>
                        </tr>
                        <tr>
                            <td class="ttitulo"> Descripción: </td>
                            <td id="descripcion_parametro"></td>
                        </tr>
                        <tr class='tr_valory'>
                            <td class="ttitulo" id='text_valory'> </td>
                            <td id="valory_parametro"></td>
                        </tr>
                        <tr class='tr_valory'>
                            <td class="ttitulo" id='text_valory_des'></td>
                            <td id="des_valory_parametro"></td>
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


<div class="modal fade" id="ModalModificarParametro" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="form_modificar_valor_parametro" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-truck"></span> Modificar Orden</h3>
                </div>
                <div class="modal-body" id="bodymodal">

                    <div class="row divmodifica">
                        <div id="container_costo_modi"></div>
                        <input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre"
                            name="nombre" required>
                        <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar"
                            placeholder="Descripción" name="descripcion" required></textarea>
                    </div>

                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active btnModifica"><span
                            class="glyphicon glyphicon-floppy-disk"></span>
                        Modificar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modal_nuevo_valor" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="form_guardar_valor_parametro" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-truck"></span> Nueva Orden</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div id="container_costo"></div>
                        <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre"
                            id="valorparametro" required>
                        <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
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

<!-- Modal Confirmación Agregar Responsable -->
<div class="modal fade" id="modal_confirmacion_responsable" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-user"></span> Confirmar Cambio de Responsable</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <h4 class='text-center'>¿ Desea retirar los responsables actuales de este activo ? </h4>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"> Cancelar</button>
                <button id="btn_no_retirar" type="button" class="btn btn-primary active"> No Retirar</button>
                <button id="btn_retirar" type="button" class="btn btn-danger active"> Retirar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_detalle_laboratorios" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-laptop"></span> Información Adicional</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="opciones__container">
                    <?php if($tipo_modulo == "Inv_Lab"){ ?>
                    <div id="btn_datos_tecnicos" class="opcion__cont" data-toggle="popover" data-trigger="hover"
                        data-placement="bottom" title="Certificados"
                        data-content="Solicita tus certificados con un click.">
                        <img src="<?php echo base_url() ?>/imagenes/experiencia.png" alt="..." class="opcion__img">
                        <span class="opcion__span">INFORMACIÓN TÉCNICA</span>
                    </div>

                    <div id="btn_documentos" class="opcion__cont" data-toggle="popover" data-trigger="hover"
                        data-placement="bottom" title="Certificados"
                        data-content="Solicita tus certificados con un click.">
                        <img src="<?php echo base_url() ?>/imagenes/soportes.png" alt="..." class="opcion__img">
                        <span class="opcion__span">DOCUMENTOS</span>
                    </div>

                    <div id="btn_informacion_tecnica" class="opcion__cont" data-toggle="popover" data-trigger="hover"
                        data-placement="bottom" title="Certificados"
                        data-content="Solicita tus certificados con un click.">
                        <img src="<?php echo base_url() ?>/imagenes/sublineas.png" alt="..." class="opcion__img">
                        <span class="opcion__span">REQUERIMIENTOS TÉCNICOS</span>
                    </div>

                    <div id="btn_mantenimiento" class="opcion__cont" data-toggle="popover" data-trigger="hover"
                        data-placement="bottom" title="Mantenimiento"
                        data-content="Solicita tus certificados con un click.">
                        <img src="<?php echo base_url() ?>/imagenes/herramientas.png" alt="..." class="opcion__img">
                        <span class="opcion__span">MANTENIMIENTO Y CALIBRACIÓN</span>
                    </div>
                    <?php } else {?>
                    <div id="btn_agregar_perifericos" class="opcion__cont" data-toggle="popover" data-trigger="hover"
                        data-placement="bottom" title="Agregar Periféricos">
                        <img src="<?php echo base_url() ?>/imagenes/computer.png" alt="..." class="opcion__img">
                        <span class="opcion__span">AGREGAR PERIFÉRICOS</span>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php if($tipo_modulo == "Inv_Lab") {?>
<form id="form_datos_tecnicos">
    <div class="modal fade" id="modal_datos_tecnicos" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-laptop"></span> Datos Técnicos</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row sin_margin width100">
                        <div class="col-md-6">
                            <select name="tecnologia" class="form-control cbxtecnologia">
                                <option value="">Tecnología Predominante</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="estado_activo" class="form-control cbxestado">
                                <option value="">Estado del Activo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row sin_margin width100">
                        <div class="col-md-6">
                            <select name="fase" class="form-control cbxfases">
                                <option value="">Seleccion Fase</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="vida_util" class="form-control" placeholder="Vida Útil(años)">
                        </div>
                    </div>
                    <div class="row sin_margin width100">
                        <div class="col-md-6">
                            <input type="text" name="peso" class="form-control" placeholder="Peso(kg)">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="voltaje" class="form-control" placeholder="Voltaje(V)">
                        </div>
                    </div>
                    <div class="row sin_margin width100">
                        <div class="col-md-3">
                            <input type="text" name="potencia" class="form-control" placeholder="Potencia">
                        </div>
                        <div class="col-md-3">
                            <select name="unidades" class="form-control cbxpotencia">
                                <option value="">Unidades</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-floppy-disk"></span>
                        Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                            class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="tablausu col-md-12 " id="menu_principal"
    style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
        <h3 class="titulo_menu">
            <span class="fa fa-navicon"></span> MENÚ
        </h3>
        <div class="row" id="menu_th">
            <div id="btn_estado_solicitudes">
                <div class="thumbnail">
                    <div class="caption" style="text-align: center;">
                        <img src="<?php echo base_url() ?>/imagenes/Inventario.png" alt="...">
                        <span
                            class="btn  form-control btn-Efecto-men"><?php echo ($tipo_modulo == 'Inv_Lab') ? 'ACTIVOS' : 'INVENTARIO' ?></span>
                    </div>
                </div>
            </div>
        </div>
        <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span
                class="fa fa-reply-all naraja"></span>Regresar</p>
    </div>
</div>

<div class="modal fade" id="modal_requerimientos_tecnicos" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-laptop"></span> Requerimientos Técnicos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table id="tabla_requerimientos" class="table table-bordered table-hover table-condensed"
                        cellspacing="0" width="100%">
                        <thead class="ttitulo">
                            <tr>
                                <th colspan="4" class="nombre_tabla">TABLA REQUERIMIENTOS</th>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Nombre</td>
                                <td>Gestión</td>
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

<div class="modal fade" id="modal_documentos" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-archive"></span> Documentos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table id="tabla_documentos" class="table table-bordered table-hover table-condensed"
                        cellspacing="0" width="100%">
                        <thead class="ttitulo">
                            <tr>
                                <th colspan="4" class="nombre_tabla">TABLA DOCUMENTOS</th>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Ver</td>
                                <td>Documento</td>
                                <td>Gestión</td>
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

<div class="modal fade" id="modal_adjuntar_documento" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-archive"></span> Adjuntar Documento</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <form class="dropzone needsclick dz-clickable" id="Subir" action="">
                    <input type="hidden" name="id" id="id_archivo" val="0">
                    <div class="dz-message needsclick">
                        <p>Arrastre archivos o presione clic aquí</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_documentos_tipo" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span> Información Documentos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table id="tabla_documentos_tipo" class="table table-bordered table-hover table-condensed"
                    cellspacing="0" width="100%">
                    <thead class="ttitulo">
                        <tr>
                            <th colspan="4" class="nombre_tabla">TABLA DOCUMENTOS</th>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Ver</td>
                            <td>Documento</td>
                            <td>Acciones</td>
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

<div class="modal fade" id="modal_mantenimientos_lab" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-cogs"></span> Panel de Mantenimiento</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table id="tabla_mantenimientos_lab" class="table table-bordered table-hover table-condensed"
                    cellspacing="0" width="100%">
                    <thead class="ttitulo">
                        <tr>
                            <th colspan="2" class="nombre_tabla"> TABLA MANTENIMIENTOS</th>
                            <th colspan="3" class="sin-borde text-right border-left-none">
                                <button id="btn_agregar_mantenimiento_lab" class="btn btn-default ">
                                    <span class="fa fa-plus red"></span> Agregar
                                </button>
                            </th>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Ver</td>
                            <td>Tipo</td>
                            <td>Usuario</td>
                            <td>Ultima revisión</td>
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

<div class="modal fade" id="modal_agregar_mantenimiento" role="dialog" style="overflow-y: scroll;">
    <form id="form_agregar_mantenimiento">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span> Agregar Mantenimiento periodico</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row width100">
                        <div class="col-md-6">
                            <select name="tipo" class="form-control cbx_tipos_mantenimiento">
                                <option value="">Seleccion tipo de acción</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="periodicidad" class="form-control cbx_periodicidad">
                                <option value="">Seleccione periodicidad</option>
                            </select>
                            <!-- <input type="number" name="periodicidad" min="0" class="form-control" placeholder="Periodicidad"> -->
                        </div>
                    </div>
                    <div class="row width100">
                        <div class="col-md-12">
                            <div class="agrupado input-group">
                                <span class="input-group-addon" style='	background-color:white'><span
                                        class='fa fa-calendar red'></span> Ultimo mantenimiento</span>
                                <input type="date" required class="form-control sin_margin" name='fecha_mantenimiento'>
                            </div>
                        </div>
                    </div>
                    <div class="row width100">
                        <div class="col-md-12">
                            <textarea name="descripcion" class="form-control" placeholder="Descripción"></textarea>
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
        </div>
    </form>
</div>

<div class="modal fade" id="modal_detalle_mantenimiento_lab" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-wrench"></span> Detalle Mantenimiento</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed margin1 ajustar" id="tbl_detalle_mantenimiento">
                    <tr>
                        <th class="nombre_tabla" colspan="4"> Detalle Proceso</th>
                    </tr>
                    <tr>
                        <td class="ttitulo"> Proceso: </td>
                        <td class="man_det_proceso"></td>
                        <td class="ttitulo"> Periodicidad: </td>
                        <td class="man_det_periodicidad"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo"> Persona modifica: </td>
                        <td class="man_det_modifica"></td>
                        <td class="ttitulo"> Ultimo mantenimiento: </td>
                        <td class="man_det_fecha_modifica"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Persona registra:</td>
                        <td class="man_det_registro"></td>
                        <td class="ttitulo"> Fecha registra:</td>
                        <td class="man_det_fecha_registra"></td>
                    </tr>
                    <tr id="row_descripcion_mantenimiento">
                        <td class="ttitulo">Descripción: </td>
                        <td class="man_det_descripcion" colspan="3"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php }?>

<div class="modal fade" id="ModalMantenimiento" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-cogs"></span> Panel de Mantenimiento</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div id="mostrar-mantenimientos" class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabla-mantenimiento" style="width: 100%">
                        <thead class="ttitulo">
                            <tr class="">
                                <td colspan="5" class="nombre_tabla">Lista de Mantenimientos</td>

                                <td class="btnAgregar sin-borde text-center" rowspan="1" colspan="1">
                                    <?php if($admin || $permisos[0]['gestionar'] == '1'){ ?>
                                    <span class="btn btn-default" id="nuevo_mantenimiento" title="Nuevo Mantenimiento"
                                        data-toggle="popover" data-trigger="hover">
                                        <span class="fa fa-plus red"> </span> Agregar
                                    </span>
                                    <?php }?>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Ver</td>
                                <td>Tipo</td>
                                <td>Fecha</td>
                                <td>Usuario</td>
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
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_asignar_mantenimiento" role="dialog">
    <div class="modal-dialog">
        <form id="form_asignar_mantenimiento" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-cogs"></span> Asignación de mantenimiento</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <select name="tipo_mantenimiento" required class="form-control cbxtipomantenimiento"> Tipo de
                            mantenimiento</select>
                        <div class="agrupado input-group">
                            <span class="input-group-addon" style='	background-color:white'><span
                                    class='fa fa-calendar red'></span> Fecha Mantenimiento</span>
                            <input type="date" required class="form-control sin_margin" name='fecha_mantenimiento'>
                        </div>
                        <textarea name="descripcion" class="form-control" cols="1" rows="3"
                            placeholder="Descripcion"></textarea>
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

<div class="modal fade" id="modal_detalle_mantenimiento" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span> Información Mantenimiento</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="4"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo">Fecha Registro</td>
                        <td class="fecha"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Usuario</td>
                        <td class="usuario"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Estado </td>
                        <td class="estado"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Tipo mantenimiento</td>
                        <td class="tipo"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo">Descripcion</td>
                        <td class="descripcion"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal-Perifericos" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-plug"></span> Periféricos Conectados</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-hover" id="tabla-perifericos" style="width: 100%">
                    <thead class="ttitulo">
                        <tr>
                            <td colspan="5" class="nombre_tabla">Lista de Periféricos</td>
                            <td class="btnAgregar sin-borde text-center">
                                <?php if($admin || $permisos[0]['gestionar'] == 1){ ?>
                                <span id="nuevo-periferico" class='pointer btn btn-default' title="Nuevo Periferico"
                                    data-toggle="popover" data-trigger="hover"> <span class="fa fa-plus red"></span>
                                    Agregar</span>
                                <?php }?>
                            </td>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Ver</td>
                            <td>Activo</td>
                            <td>Serial</td>
                            <td>Fecha</td>
                            <td>Estado</td>
                            <td class="opciones_tbl">Acción</td>
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

<div class="modal fade" id="Modal_asignar_periferico" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg modal-80">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-plug"></span> Asignar Periferico</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="form-inline" style="float: right; margin-bottom: 10px;">
                    <div class="input-group">
                        <input type="search" id="txt_periferico" class="form-control" placeholder="Buscar Periférico"
                            autocomplete="off">
                        <span id="btn_buscar_periferico" class="input-group-addon btn red"><span
                                class="fa fa-search"></span></span>
                    </div>
                </div>
                <table id="tbl_perifericos" class="table table-bordered table-hover table-condensed table-responsive"
                    cellspacing="0" width="100%">
                    <thead class="ttitulo ">
                        <tr>
                            <td colspan="6" class="nombre_tabla">TABLA INVENTARIO ALMACEN</td>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Activo</td>
                            <td>Serial</td>
                            <td>Codigo_Int.</td>
                            <td>Marca</td>
                            <td>Modelo</td>
                            <td>Estado</td>
                            <td>Acción</td>
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

<div class="modal fade" id="modal_buscar_proveedor" role="dialog">
    <div class="modal-dialog modal-lg">
        <form id="form_buscar_proveedor" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-search"></span> Buscar proveedores</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="agrupado col-md-12 text-left">
                            <div class="col-md-8" style="padding:0px;">
                                <div class="input-group agro">
                                    <input id='txt_proveedores' class="form-control con_focus"
                                        placeholder="Ingrese el proveedor a buscar">
                                    <span class="input-group-btn"><button class="btn btn-default test"
                                            type="submit"><span class='fa fa-search red'></span>
                                            Buscar</button></span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12" style="width: 100%">
                            <table class="table table-bordered table-hover table-condensed pointer"
                                id="tabla_proveedor_buscar" cellspacing="0" width="100%">
                                <thead class="ttitulo ">
                                    <tr class="">
                                        <td colspan="4" class="nombre_tabla">TABLA PROVEEDORES</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>No.</td>
                                        <td>Nombre del proveedor</td>
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

<div class="modal fade" id="modal_lugares" role="dialog" style="overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title h3__lab_places"><span class="glyphicon  glyphicon-random"></span> Información
                    Lugares</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr>
                        <th class="nombre_tabla" colspan="2"> Información General</th>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_lugar">Lugar</td>
                        <td class="lugar"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_ubicacion">Ubicación </td>
                        <td class="ubicacion"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_fecha_asigna">Fecha Asignado </td>
                        <td class="fecha_asigna"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_fecha_retira">Fecha Retiro </td>
                        <td class="fecha_retira"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_usuario_asigna">Usuario Asignado por: </td>
                        <td class="usuario_asigna"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_usuario_retira">Usuario Retirado por: </td>
                        <td class="usuario_retira"></td>
                    </tr>
                    <tr>
                        <td class="ttitulo mod_estado">Estado </td>
                        <td class="estado"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                        class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
    gestionar_ruta('<?php echo "$_SERVER[REQUEST_URI]" ?>');
    obtener_info_sesion('<?php echo $_SESSION["perfil"]?>', '<?php echo $_SERVER["REQUEST_URI"] ?>',
        '<?php echo $_SESSION["persona"]?>');
    activarfile();
    inactivityTime();
    adjuntar_archivo();
    listar_dependencias('ubi');
    Cargar_parametro_buscado(6, ".cbx_tipo_recurso", "Seleccione Tipo activo");
    Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo identificacion");
    Cargar_parametro_buscado(4, ".cbxmarcas", "Seleccione Marca");
    Cargar_parametro_buscado(4, "#cbxmarcas_mod", "Seleccione Marca");
    Cargar_parametro_buscado(225, ".cbxusos_activo", "Seleccione Uso del Activo");
    Cargar_parametro_buscado(115, ".cbxlugar", "Seleccione Lugar");
    Cargar_parametro_buscado(115, "#Guardar_inventario select[name='id_lugar']", "Seleccione un lugar");
    Cargar_parametro_buscado(115, "#form_agregar_lugar_nuevo select[name='id_lugar']", "Seleccione un lugar");
    Cargar_parametro_buscado(9, ".cbxprocesador", "Procesador");
    Cargar_parametro_buscado(10, ".cbxSistemaOperativo", "Sistema Operativo");
    Cargar_parametro_buscado(11, ".cbxtipomantenimiento", "Seleccione Tipo Mantenimiento");
    Cargar_parametro_buscado_aux(8, ".cbxestado", "Seleccione Estado");
    Cargar_parametro_buscado(226, ".cbxtecnologia", "Seleccione Tecnología Predominante");
    Cargar_parametro_buscado(227, ".cbxfases", "Seleccione Fase");
    Cargar_parametro_buscado(231, ".cbxfases", "Seleccione Periodicidad");
    Cargar_parametro_buscado(37, ".cbxproveedor", "Seleccione Proveedor");
    Cargar_parametro_buscado(11, ".cbx_tipos_mantenimiento", "Seleccione Tipo Acción");
    Cargar_parametro_buscado(228, ".cbxpotencia", "Unidad");
    Cargar_parametro_buscado_aux(231, ".cbx_periodicidad", "Seleccione periodicidad");
});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>