<?php $sw  = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_Fac" ?  true : false; ?>
<div class="container col-md-12 " id="inicio-user">
    <div class="tablausu col-md-12 text-left <?php if(!$sw) echo 'oculto';?>" id="container-listado-facturas">
        <div class="table-responsive col-sm-12 col-md-12">
            <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_facturas" cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                    <tr>
                        <td colspan="3" style="" class="nombre_tabla">TABLA FACTURAS <br>
                        <span class="mensaje-filtro oculto" id='mensaje-filtro-evento'>
                        <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
                        <td class="sin-borde text-right border-left-none" colspan="3"> 
                            <?php if ($sw) echo '<span class="btn btn-default btnAgregar" id="btn_admin_solicitudes"><span class="fa fa-cogs red"></span> Administrar</span>';?>
                            <span class="btn btn-default btnModifica" id="btn_modificar_evento"><span class="fa fa-wrench red"></span> Modificar</span> 
                            <span class="btn btn-default" data-toggle="modal" data-target="#modal_filtrar">
                            <span class="fa fa-filter red"></span> Filtrar</span> 
                            <span class="btn btn-default" id="limpiar_filtros"> 
                            <span class="fa fa-refresh red"></span> Limpiar</span>
                        </td>
                    </tr>
                    <tr class="filaprincipal">
                        <td class="opciones_tbl">Ver</td>
                        <td>Solicitante</td>
                        <td>Fecha Solicitud</td>
                        <td>Plazo</td>
                        <td>Estado</td>
                        <td class="opciones_tbl_btn">Acción</td>
                    </tr>
                </thead>
                <tbody >
                </tbody>
            </table>
        </div>
    </div>

    <div class="tablausu col-md-12 <?php if($sw) echo 'oculto';?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
        <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
        <div id="container-principal2" class="container-principal-alt">
            <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
            <div class="row">
                <div id="agregar_factura">
                    <div class="thumbnail">
                        <div class="caption">
                            <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                            <span class="btn  form-control btn-Efecto-men">Nueva factura</span>
                        </div>
                    </div>
                </div>
                <div id="listado_solicitudes">
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

<div class="modal fade" id="modal_factura" role="dialog">
    <div class="modal-dialog">
        <form id="nueva_factura" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> <span class="nueva_factura"> Nueva Factura</span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <!--  -->
                        <div id="div_articulo" class="input-group agro">
                            <span class="form-control text-left pointer sel_art" id="Btncod_orden_sele"></span>
                            <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="Btnbuscar_cod" title="" data-toggle="popover" data-trigger="hover" data-original-title="Buscar Codigo Sap"><span class="glyphicon glyphicon-search"></span></span>
                        </div>
                        <div id="div_empresa" class="input-group agro">
                            <span class="form-control text-left pointer" id="Btnbuscar_empresa"></span>
                            <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="Btnbuscar_emp" title="" data-toggle="popover" data-trigger="hover" data-original-title="Buscar Empresa"><span class="glyphicon glyphicon-search"></span></span>
                            <!-- <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="agregar_empresa" title="" data-toggle="popover" data-trigger="hover" data-original-title="Agregar Empresa"><span class="glyphicon glyphicon-plus"></span></span> -->
                        </div>  
                        <div class="agrupado div_adj_rut oculto">
                            <div class="input-group">
                                <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                        <span class="fa fa-folder-open"></span>
                                            Buscar <input name="adj_rut" type="file" style="display: none;" id="adj_rut">
                                    </span>
                                </label>
                                <input type="text" class="form-control" readonly placeholder='Adjunte RUT Actualizado' id="adj_rut_input">
                            </div>
                        </div>
                        <div class="alert alert-warning pointer oculto agro" id="empresa_mensaje" role="alert" style="margin-bottom: 6px">
                            Si desea agregar una empresa existente, haz click aqui.
                        </div>
                        <input type="text" name="valor" class="form-control inputt2 number" required placeholder="Valor Factura">
                        <textarea name="concepto" class="form-control comentarios" placeholder="Concepto detallado" ></textarea>

                        <select name="id_plazo" required class="form-control cbx_plazos"></select>
                        <select name="id_entrega" required class="form-control cbx_entrega"></select>
                        <div class="">
                        <div class="funkyradio facturacion" >
                            <div class="funkyradio-success">
                              <input type="radio" id="con_cuenta" name="rating" value="1">
                              <label for="con_cuenta" title="Con cuenta"> Con cuenta exclusiva</label>
                            </div>
                            <div class="funkyradio-danger">
                              <input type="radio" id="sin_cuenta" name="rating" value="2">
                              <label for="sin_cuenta" title="Sin cuenta"> Sin cuenta exclusiva</label>
                            </div>
                        </div>
                    </div>
                        <div class="oculto" id="content" name="content">
                            <select name="id_banco" class="form-control cbx_bancos banco_cuenta"></select>
                            <select name="id_tipo_cuenta" class="form-control cbx_tipo banco_cuenta"></select>
                            <input type="number" placeholder="Numero Cuenta" class="form-control banco_cuenta" name="num_cuenta" step="1" min="1">
                            <div class="agrupado">
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            <span class="fa fa-folder-open"></span>
                                             Buscar <input name="adj_banco" type="file" style="display: none;" id="adj_banco">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly placeholder='Certificado Bancario'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_modificar_factura" role="dialog">
    <div class="modal-dialog">
        <form id="modificar_factura" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span> <span class="modificar_factura"> Modificar Factura</span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                    <div id="div_articulo" class="input-group agro">
                    <span class="form-control text-left pointer " id="btn_modificar_cod"></span>
                        <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="btn_buscar_mod" title="" data-toggle="popover" data-trigger="hover" data-original-title="Buscar Codigo Sap"><span class="glyphicon glyphicon-search"></span></span>
                        </div>
                        <div id="div_empresa_mod" class="input-group agro">
                        <span class="form-control text-left pointer" id="btn_modificar_empresa"></span>
                        <span class="input-group-addon red_primari pointer btn-Efecto-men sel_art" id="btn_buscar_emp_mod" title="" data-toggle="popover" data-trigger="hover" data-original-title="Buscar Empresa"><span class="glyphicon glyphicon-search"></span></span>
                        </div>  
                        <div class="div_rut">
                            <div class="agrupado div_adj_rut_mod oculto">
                                <div class="input-group">
                                    <label class="input-group-btn">
                                    <a class="btn btn-default" id="ver_rut_modi" target="_blank"><span class="fa fa-eye red"></span>ver</a>
                                        <span class="btn btn-primary">
                                            <span class="fa fa-folder-open"></span>
                                                Buscar <input name="adj_rut_mod" type="file" style="display: none;" id="text_rut_mod">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly placeholder='Adjunte RUT Actualizado' id="adj_rut_input_mod">
                                </div>
                            </div>
                            <div class="alert alert-warning pointer oculto agro" id="empresa_mensaje_mod" role="alert" style="margin-bottom: 6px">
                                Si desea agregar una empresa existente, haz click aqui.
                            </div> 
                        </div>
                        <input type="text" name="valor_mod" class="form-control inputt2 valor_facturado" min="1" required placeholder="Valor Factura">
                        <textarea name="concepto_mod" class="form-control" placeholder="Concepto detallado"></textarea>

                        <select name="id_plazo_mod" required class="form-control cbx_plazos"></select>
                        <select name="id_entrega_mod" required class="form-control cbx_entrega"></select>

                        <div class="">
                        <div class="funkyradio facturacion">
                            <div class="funkyradio-success">
                              <input type="radio" id="con_cuenta_mod" name="rating" value="1">
                              <label for="con_cuenta_mod" title="Con cuenta"> Con cuenta exclusiva</label>
                            </div>
                            <div class="funkyradio-danger">
                              <input type="radio" id="sin_cuenta_mod" name="rating" value="2">
                              <label for="sin_cuenta_mod" title="Sin cuenta"> Sin cuenta exclusiva</label>
                            </div>
                        </div>
                    </div>
                        <div class="oculto" id="contenido" name="content">
                            <select name="id_banco_mod" class="form-control cbx_bancos banco_cuenta"></select>
                            <select name="id_tipo_cuenta_mod" class="form-control cbx_tipo banco_cuenta"></select>
                            <input type="number" placeholder="Numero Cuenta" class="form-control banco_cuenta" name="num_cuenta_mod" step="1" min="1">
                            <div class="agrupado">
                                <div class="input-group">
                                    <label class="input-group-btn">
                                    <a class="btn btn-default" id="ver_banco_modi" target="_blank"><span class="fa fa-eye red"></span>ver</a>
                                        <span class="btn btn-primary">
                                            <span class="fa fa-folder-open"></span>
                                             Buscar <input name="adj_banco_mod" type="file" style="display: none;" id="adj_banco_mod" >
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly placeholder='Certificado Bancario' id='text_adj_banco'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="modificar"> Modificar</span></button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
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
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div id="adj_rut_mensaje" class="oculto alert alert-warning pointer" role="alert">
                    Si no encuentra la empresa puede adjuntar el RUT aqui.
                </div>
                <div id="adj_rut_mensaje_mod" class="oculto alert alert-warning pointer" role="alert">
                    Si no encuentra la empresa puede adjuntar el RUT aqui.
                </div>
                <form id="Buscar_Codigo_Orden" method="post">
                    <div class="input-group agro col-md-8">
                        <input type="text" class="form-control inputt2" name="codigo_sap" placeholder="Ingrese Dato" id="txtcodigo_sap" autocomplete="off">
                        <span class="input-group-addon red_primari pointer btn-Efecto-men" id="buscar_cod_sap" title="Buscar Código" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                    </div>
                </form>
                <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_codigos" cellspacing="0" width="100%">
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
                <button type="submit" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade con-scroll-modal" id="modal_detalle_factura" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X </button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Factura</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed  margin1 ajustar" id="tabla_detalle_factura">
                        <tr class="">
                            <th class="nombre_tabla" colspan="8"> Información General</th>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="4">Estado </td>
                            <td class="estado_factura" colspan="4"></td>   
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="4" >Fecha Solicitud</td>
                            <td class="fecha_registra" colspan="4"></td>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="4">Solicitante</td>
                            <td class="nombre_solicitante" colspan="4"></td>
                        </tr>
                        <?php if ($sw) {?>
                        <tr>
                        <td class="ttitulo"colspan="4">Historial</td>
                        <td colspan="4"><span id="ver_estados"><span class="fa fa-eye red"></span> Ver Estados</span></td>
                        </tr>   
                        <?php } ?>
                        <tr class='tr_negada' >
                            <td class="ttitulo"  colspan="4">Negada por:</td>
                            <td class="negada" colspan="4"></td>
                        </tr>
                        <tr class="">
                            <th class="nombre_tabla" colspan="8"> Información Factura</th>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="2">Nombre de empresa </td>
                            <td class="empresa" colspan="2"></td>
                            <td class="ttitulo" colspan="2">Orden SAP </td>
                            <td class="sap" colspan="2"></td>
                        </tr>
                        <tr >
                            <td class="ttitulo" colspan="2">Valor factura </td>
                            <td class="valor" colspan="2"></td>
                            <td class="ttitulo" colspan="2">Plazo de pago </td>
                            <td class="plazo" colspan="2"></td>
                        </tr>
                        <tr >
                            <td class="ttitulo" colspan="4" style="width:50%">Tipo de Entrega </td>
                            <td class="tipo_entrega" colspan="4"></td>
                        </tr> 
                        <tr >
                            <td class="ttitulo" colspan="4" style="width:50%">Concepto </td>
                            <td class="concepto" colspan="4"></td>
                        </tr>                        
                        <tr class="tr_banco">
                            <th class="nombre_tabla" colspan="8"> Información Cuenta</th>
                        </tr>
                        <tr class='tr_banco'>
                            <td class="ttitulo" colspan="4">Banco </td>
                            <td class="banco" colspan="4"></td>
                        </tr>
                        <tr class='tr_banco'>
                            <td class="ttitulo" colspan="4">Tipo de cuenta </td>
                            <td class="tipo"  colspan="4"></td>
                        </tr>
                        </tr>
                        <tr class='tr_banco' >
                            <td class="ttitulo"  colspan="4">Numero de cuenta </td>
                            <td class="num_cuenta" colspan="4"></td>
                        </tr>
                        <tr class='tr_banco' >
                            <td class="ttitulo"  colspan="4">Certificación Bancaria </td>
                            <td colspan="4"><a id="ver_adjuntos_lista" target="_blank"><span class="fa fa-eye red"></span> Ver Archivo</a></td>
                        </tr>
                        <tr class='tr_banco_factura' >
                            <td class="ttitulo"  colspan="4">Factura </td>
                            <td colspan="4"><a id="ver_adjuntos_factura" target="_blank"><span class="fa fa-eye red"></span> Ver Archivo</a></td>
                        </tr>
                        </tr>
                        <tr class='tr_rut' >
                            <td class="ttitulo"  colspan="4">RUT </td>
                            <td colspan="4"><a id="ver_adjuntos_rut" target="_blank"><span class="fa fa-eye red"></span> Ver Archivo</a></td>
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


<div class="modal fade" id="modal_filtrar" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row">
                    <select id="estado_filtro" class="form-control inputt cbxestado">
                        <option value="">Filtrar Solicitudes por Estado</option>
                    </select>
                    <!--<select id="empresa_filtro" class="form-control inputt cbxempresa">
                        <option value="">Filtrar Solicitudes por Empresa</option>
                    </select>
                    <select id="banco_filtro" class="form-control inputt cbxbanco">
                        <option value="">Filtrar Solicitudes por Banco</option>
                    </select>-->
                    <select id="plazo_filtro" class="form-control inputt cbx_plazos">
                        <option value="">Filtrar Solicitudes por Plazo</option>
                    </select>
                    <input id="fecha_filtro" class="form-control" value="" placeholder="Filtrar Por Fecha" type="month" name="fecha_filtro">
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-danger active" id="btnfiltrar"><span class="glyphicon glyphicon-ok"></span> Generar</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_gestion_aprobar" role="dialog">
    <div class="modal-dialog">
    <form id="form_gestion_aprobar" method="post">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-cloud-upload"></span> Aprobar solicitud</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row">
                    <div class="agrupado">
                        <div class="input-group">
                            <label class="input-group-btn">
                                <span class="btn btn-primary">
                                    <span class="fa fa-folder-open"></span>
                                        Buscar <input name="adj_aprobar" type="file" style="display: none;" id="adj_aprobar">
                                </span>
                            </label>
                            <input type="text" class="form-control" readonly placeholder='Adjunte la Factura'>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="submit" class="btn btn-danger active" id="btnfiltrar"><span class="glyphicon glyphicon-ok"></span> Aprobar</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </form>
    </div>
</div>
<div class="modal fade" id="modal_listar_estados" role="dialog" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Historial de Estados</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">

            <table class="table table-bordered table-hover table-condensed" id="tabla_estados_solicitud" cellspacing="0" width="100%">
                <thead class="ttitulo">
                    <tr>
                        <th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
                    </tr>
                    <tr class="filaprincipal">
                        <td>No.</td>
                        <td>Fecha Registro</td>
                        <td>Usuario</td>
                        <td>Estado</td>
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

<div class="modal fade" id="modal_agregar_empresa_banco" role="dialog">
    <div class="modal-dialog">
        <form id="form_agregar_empresa_banco" method="post">

            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Agregar Empresa</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input id="nombre_empresa" name="nombre" class="form-control" placeholder="Nombre de la Empresa">
                        <input id="descrip_empresa" name="descripcion" class="form-control" placeholder="Descripción de la Empresa">
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" id="btn_guardar_empresa"><span class="glyphicon glyphicon-ok"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
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
        <?php  if($sw){?>
        <nav class="navbar navbar-default" id="nav_admin_compras">
          <div class="container-fluid">

            <ul class="nav navbar-nav">
              <li class="pointer" id="admin_banco"><a><span class="fa fa-sitemap red"></span> Bancos</a></li>
              <li class="pointer" id="admin_empresa"><a><span class="fa fa-pencil-square-o red"></span> Empresas</a></li>

              </li>
            </ul>
          </div>
        </nav>
        <?php  }?>
        <div class="table-responsive">

          <div id="container_admin_valores" class="oculto">
            <table class="table table-bordered table-hover table-condensed" id="tabla_valores_parametros"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="3" class="nombre_tabla" id="nombre_tabla_cu_or">TABLA PROVEEDORES</td>
                  <td class="btnAgregar sin-borde text-center">
                    <span data-toggle="modal" data-target="#modal_nuevo_valor" class="btn btn-default"><span
                        class="fa fa-plus red"></span>
                      Nueva</span>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td>***</td>
                  <td>Nombre</td>
                  <td>Descripción</td>
                  <td class="opciones_tbl_btn">***</td>

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

<div class="modal fade con-scroll-modal" id="modal_detalle_parametro" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X </button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Información General</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-condensed  margin1 ajustar" id="tabla_detalle_traslado_comite">
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
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
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
            <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
            <textarea class="form-control inputt" name="descripcion" placeholder="Descripción"></textarea>
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
            <input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre" name="nombre" required>
            <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripción"
              name="descripcion" required></textarea>
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

<style>
    .ajustar tr td:nth-of-type(1) {
        width: 0%;
    }
</style>
<script>
    $(document).ready(function() {
        <?php if($_SESSION["perfil"] != "Per_Fac"){ ?>
            inactivityTime();
        <?php }?>
        Cargar_parametro_buscado(107, ".cbx_plazos", "Seleccione plazo");
        Cargar_parametro_buscado(108, ".cbx_entrega", "Seleccione Tipo de Entrega");
        Cargar_parametro_buscado(109, ".cbx_bancos", "Seleccione Banco");
        Cargar_parametro_buscado(110, ".cbx_tipo", "Tipo de Cuenta");
        Cargar_parametro_buscado_aux(111, ".cbxestado", "Seleccione Estado");

        activarfile();
        listar_facturas(<?php echo $id ?>);

    });
</script>


