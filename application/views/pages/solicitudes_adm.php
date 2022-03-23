<?php 
$sw = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_adm") {
    $sw = true;
}
?>
<div id="menu" >
    <div class= "<?php if(!$sw) echo 'oculto';?> listado_solciitudes">
       <!-- <button   type="button" class="btn btn-link active btnAgregar btn_asignar_detalle" id="btn_asignar_detalle"><span class="btn-Efecto-men fa fa-plus" title="Asignar Detalle" data-toggle="popover" data-trigger="hover"></span></button>-->
       

    </div>

</div>

<div class="container col-md-12 text-center" id="inicio-user" >

    
   
    <div class="tablausu col-md-12 text-left <?php if(!$sw) echo 'oculto';?> listado_solciitudes" >
        <div class="table-responsive col-sm-12 col-md-12  tablaViaticos_transporte" >
        <p class="titulo_menu pointer" id="btnAgregar_solicitud"><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes_principal"  cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                    <tr ><td colspan="2" style="" class="nombre_tabla">TABLA SOLICITUDES <br><span class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td><td class="sin-borde text-right border-left-none" colspan="5" >  <span  class="btn btn-default btnModifica" id="btnmodificar_solciitud"><span class="fa fa-wrench red"></span> Modificar</span> <span  class="btn btn-default btnAgregar" id="agregar_nueva_persona"><span class="fa fa-plus red"></span> Persona</span> <span class="btn btn-default" id="filtrar_datos_solicitud"><span class="fa fa-filter red" ></span> Filtrar</span> <span class="btn btn-default" id="limpiar_filtros_solicitud"> <span class="fa fa-refresh" ></span> Limpiar</span></td></tr>
                    <tr class="filaprincipal"><td  class="opciones_tbl">***</td><td>Nombre</td><td>Tipo</td><td>Solicitante</td><td>Fecha_Solicitud</td><td>Estado</td><td  class="opciones_tbl_btn">***</td></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


    </div>
    <div class="tablausu col-md-12 <?php if($sw) echo 'oculto';?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
          <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'>
         </div> 

         <div id="container-principal2" class="container-principal-alt">
        <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
        
        <div class="row">

            <div class=""  id = "solt1">
                <div class="thumbnail">
                    <div class="caption">
                        
                        <img src="<?php echo base_url() ?>/imagenes/viajes.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_viajes" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Viajes</span>
                        
                    </div>


                </div>
            </div>



            <div class=""  id = "solt2">
                <div class="thumbnail">

                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/transporte.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_transporte" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Transporte</span>
                       
                    </div>


                </div>
            </div>


            <div class=""  id = "solt4">
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_logistica" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Logística Eventos</span>

                       
                    </div>


                </div>
            </div>

            <div class=""  id = "solt3">
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men" id ="titulo_otras" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="">Otras Solicitudes</span>

                        
                    </div>


                </div>
            </div>

            <div class=" "  id = "listado">
                <div class="thumbnail ">
                    <div class="caption">
                     
                        <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
                        <span class = "btn  form-control btn-Efecto-men"  data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="En esta opción puedes verificar el estado de tus solicitudes, ademas te permite añadir información adicional a las solicitudes que tienes activas.">Estados Solicitudes</span>

                       
                    </div>


                </div>
            </div>
           
        </div>
        <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal-add-via" role="dialog">
    <div class="modal-dialog">
        <!-- Aqui es -->
        <form action="#" id="Guardar_solicitud_general" method="post" enctype="multipart/form-data">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text"></span>   <span id="nombre_solicitud">Nueva Solicitud</span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="" id="" style="width: 100%">
                        <div class="text-center row" id="panel_info_evento">
                            <input type="text" class="form-control CampoGeneral valor_evento btn-Efecto-men"  id="nombre_evento" name="nombre_evento" placeholder="Nombre de la Solicitud" required="" >
                            <select name="tipo_calificacion"  id="cbx_tipo_calificacion" required class="form-control inputt cbx_tipo_calificacion CampoGeneral" >
                                <option>Seleccione Tipo Calificación</option>
                            </select> 
                            <select name="tipo_evento"  id="cbx_tipo_evento" class="form-control inputt cbxtipo CampoGeneral">
                                <option value="">Seleccione Tipo Evento</option>
                            </select>
                            <div class="agro" id="fecha_inicio_evento_div">
                                <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                    <input class="form-control CampoGeneral valor_fecha_inicio sin_focus" size="16" placeholder="Fecha Inicio Solicitud" type="text" value=""  name="fecha_inicio_evento" id="fecha_inicio_evento" required="true">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                            <div class="agro" id="fecha_final_evento_div">
                                <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                    <input class="form-control CampoGeneral valor_fecha_fin sin_focus" size="16" placeholder="Fecha Fin Solicitud" type="text" value=""  name="fecha_final_evento" id="fecha_final_evento" required="true">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>
                            </div>
                            <div class="text-left" id="div_req_inscripcion">  <label class="ttitulo"><input type="checkbox" name="con_inscrip" value="1" id="check_inscr">Requiere Inscripción? </label></div>
                            <div id="requiere_inscrip" class="text-center oculto">
                                <h4 class="ttitulo"><span class="glyphicon glyphicon-pencil"></span> Datos de Inscripción</h4>
                                <div class="text-left">   <input type="number" class="form-control CampoGeneral requerido" name="valor" placeholder="Valor Inscripcion" id="txtvalor_inscripcion"  step="1" min="1">
                                    <div class="text-left"> <label class="ttitulo"><input type="checkbox" name="descuento" value="1" id="check_descuento">Incluye Descuento </label></div>
                                    <input type="text" class="form-control CampoGeneral requerido" name="contacto" placeholder="Contacto" id="txtContacto">
                                    <div class="agro">
                                        <div class="input-group">
                                            <input type="number" class="form-control CampoGeneral sin_margin" name="telefono_contacto" placeholder="Telefono Contacto" id="txtTelefono_contacto"  step="1" min="1">
                                            <span class="input-group-addon">-</span>
                                            <input type="number" class="form-control CampoGeneral requerido sin_margin" name="celular_contacto" placeholder="Celular Contacto" id="txtCelular_contacto"  step="1" min="1">
                                        </div>
                                    </div>
                                    <div class="agro">
                                        <div class="input-group">
                                            <input type="url" class="form-control CampoGeneral sin_margin" name="web_contacto" placeholder="Pagina Web Contacto" id="txtWeb_contacto">
                                            <span class="input-group-addon">-</span>
                                            <input type="email" class="form-control CampoGeneral requerido sin_margin" name="correo_contacto" placeholder="Correo Contacto" id="txtCorreo_contacto">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active "><span class="fa fa-mail-forward"></span> Continuar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div> 
        </form>
    </div>
</div>



<div class="modal fade" id="Modal-modificar-solicitud" role="dialog">
    <div class="modal-dialog ">
        <form action="#" id="Modificar_solicitud_general" method="post" enctype="multipart/form-data">
            <!-- Modal content-->
            <div class="modal-content" >

                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text"></span>   Modificar Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="" id="" style="width: 100%">


                        <div class="text-center row" id="panel_info_evento_modi">


<!--                            <select name="tipo_solicitud"   required class="form-control inputt cbx_tipo_solicitud" id="cbx_tipo_solicitud_modi" >
                                <option>Seleccione Tipo Solicitud</option>
                            </select> -->
                            <span class="form-control text-left" id="cbx_tipo_solicitud_modi">Mi solicitud</span>
                            <input type="text" class="form-control CampoGeneral valor_evento btn-Efecto-men"  id="nombre_evento_modi" name="nombre_evento" placeholder="Nombre" required="" >
                            <select name="tipo_evento"  id="cbx_tipo_evento_modi" required class="form-control inputt cbx_tipo_evento CampoGeneral">
                                <option value="">Seleccione Tipo Evento</option>
                            </select>
                            <div class="agro" id="fecha_inicio_evento_div_modi">

                                <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                    <input class="form-control CampoGeneral valor_fecha_inicio sin_focus" size="16" placeholder="Fecha Inicio" type="text" value=""  name="fecha_inicio_evento" id="fecha_inicio_evento_modi">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>

                            </div>

                            <div class="agro" id="fecha_final_evento_div_modi">

                                <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                    <input class="form-control CampoGeneral valor_fecha_fin sin_focus" size="16" placeholder="Fecha Fin" type="text" value=""  name="fecha_final_evento" id="fecha_final_evento_modi">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                </div>

                            </div>


                            <div class="text-left" id="div_req_inscripcion_modi">  <label class="ttitulo"><input type="checkbox" name="con_inscrip" value="1" id="check_inscr_modi">Requiere Inscripción? </label></div>

                            <div id="requiere_inscrip_modi" class="text-center oculto">


                                <h4 class="ttitulo"><span class="glyphicon glyphicon-pencil"></span> Datos de Inscripción</h4>


                                <div class="text-left">   <input type="number" class="form-control CampoGeneral requerido" name="valor" placeholder="Valor Inscripcion" id="txtvalor_inscripcion_modi">
                                    <div class="text-left"> <label class="ttitulo"><input type="checkbox" name="descuento" value="1" id="check_descuento_modi">Incluye Descuento </label></div>
                                    <input type="text" class="form-control CampoGeneral requerido" name="contacto" placeholder="Contacto" id="txtContacto_modi">


                                    <div class="agro">

                                        <div class="input-group">
                                            <input type="number" class="form-control CampoGeneral sin_margin" name="telefono_contacto" placeholder="Telefono Contacto" id="txtTelefono_contacto_modi" step="1" min="1">
                                            <span class="input-group-addon">-</span>
                                            <input type="number" class="form-control CampoGeneral requerido sin_margin" name="celular_contacto" placeholder="Celular Contacto" id="txtCelular_contacto_modi" step="1" min="1">
                                        </div>
                                    </div>

                                    <div class="agro">

                                        <div class="input-group">
                                            <input type="url" class="form-control CampoGeneral" name="web_contacto" placeholder="Pagina Web Contacto" id="txtWeb_contacto_modi">
                                            <span class="input-group-addon">-</span>
                                            <input type="email" class="form-control CampoGeneral requerido" name="correo_contacto" placeholder="Correo Contacto" id="txtCorreo_contacto_modi">

                                        </div>
                                    </div>

                                </div>





                            </div>



                        </div>





                    </div>



                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active "><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

            </div>  </form>
    </div></div>


<div class="modal fade scroll-modal" id="Modal-info-solicitud" role="dialog" >
    <div id="modal-dialog" class="modal-dialog modal-lg" >

        <!-- Modal content-->
        <div class="modal-content " >
            <div class="modal-header" id="headermodal" >
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span>   Información Solicitud</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                    <table class="table table-bordered table-condensed tabla_info_solicitud" id="tabla_info_solicitud">
                        <tr class=""><th class="nombre_tabla" colspan="2"> Información General</th></tr>
                        <tr><td class="ttitulo">Tipo Solicitud:</td><td class="valor_solicitud"></td></tr>
                        <tr id='tr_codigo_sap_com'><td class="ttitulo">Codigo SAP:</td><td id="codigo_sap_com"></td></tr>
                        <tr id='tr_lugar_evento_com'><td class="ttitulo">Bloque - Lugar</td><td id="lugar_evento_com"></td></tr>
                        <tr id='tr_direccion_com'><td class="ttitulo">Salon - Dirección</td><td id="direccion_com"></td></tr>
                        <tr><td class="ttitulo">Nombre: </td><td  class="valor_evento"></td></tr>
                        <tr class="tipo_clasificacion"><td class="ttitulo">Tipo Clasificación: </td><td  class="valor_clasificacion"></td></tr>
                        <tr class="tipo_viaje"><td class="ttitulo">Tipo de Viaje: </td><td  class="valor_tipo"></td></tr>
                        <tr class=""><td class="ttitulo">Fecha Inicio Solicitud: </td><td class="valor_fecha_inicio"></td></tr>
                        <tr class=" fecha_final_v"><td class="ttitulo">Fecha fin Solicitud:</td><td class="valor_fecha_fin"></td></tr>
                        <tr class=""><td class="ttitulo">Con Inscripción: </td><td class="valor_con_inscripcion"></td></tr>
                        <tr><td class="ttitulo">Solicitante:</td><td><span class="valor_solicitante"> </span> 
                        <?php if ($sw) {
                        echo '<span id="detalle_persona_solicita" class="pointer fa fa-edit red" title="Detalle Persona" data-toggle="popover" data-trigger="hover"> </span>';
                                    } ?>
                        </td>
                        </tr>
                        <tr><td class="ttitulo">Fecha Registro:</td><td class="valor_fecha_solicitud"></td></tr>
                        <tr><td class="ttitulo">Estado:</td><td class="valor_estado"></td></tr>
                        <tr class="tr_valor_motivo"><td class="ttitulo">Mensaje:</td><td class="valor_motivo"></td></tr>            
                        <tr id="adjunto_comunicaciones">
                            <td class="ttitulo">Archivos Adjunto</td>
                            <td colspan="3"><span id="ver_adjuntos_lista"><span class="fa fa-eye red"></span> Ver Archivos</span></td>
                        </tr> 
                    </table>
                    <div id="datos-tipo-3">
                        <table class="table table-bordered table-condensed" >
                            <tr class=""><th class="nombre_tabla" colspan=""> Detalle Solicitud </th><th class=" text-right btnModifica" colspan="2">  <span id="btnmodificar_tipo3" class="pointer active btn-Efecto-men" title="Modificar Detalle" data-toggle="popover" data-trigger="hover"><span class="btn-Efecto-men fa fa-wrench"></span></span>
                                </th></tr>
                            <tr class="datos-tipo-3"><td  class="ttitulo">Categoria</td><td class="valor_categoria_tipo3"></td></tr>
                            <tr class="datos-tipo-3 valor_codigo_sap_tipo3_tr" ><td   class="ttitulo">Codigo SAP</td><td class="valor_codigo_sap_tipo3"></td></tr>
                            <tr class="datos-tipo-3 tipo_refrigerios" ><td  class="ttitulo">Tipo Refrigerios</td><td class="valor_tipo_refrigerios"></td></tr>
                            <tr class="datos-tipo-3 tipo_proveedor" ><td  class="ttitulo">Proveedor</td><td class="valor_tipo_proveedor"></td></tr>
                            <tr class="datos-tipo-3 " id="responsables_tipo3" ><td  class="ttitulo">Responsables</td><td id="ver_responsables_tipo3"></td></tr>
                            <tr class="datos-tipo-3 tipo_polizas" ><td  class="ttitulo">Tipo Poliza</td><td class="valor_tipo_poliza"></td></tr>

                            <tr class="datos-tipo-3 columna columna1"><td class="ttitulo">Columna1</td><td class="valor_Columna1"></td></tr>
                            <tr class="datos-tipo-3 columna columna2"><td class="ttitulo">Columna2</td><td class="valor_Columna2"></td></tr>
                            <tr class="datos-tipo-3 columna columna3"><td class="ttitulo">Columna3</td><td class="valor_Columna3"></td></tr>
                            <tr class="datos-tipo-3 columna columna4"><td class="ttitulo">Columna4</td><td class="valor_Columna4"></td></tr>
                            <tr class="datos-tipo-3 columna columna5"><td class="ttitulo">Columna5</td><td class="valor_Columna5"></td></tr>
                            <tr class="datos-tipo-3" ><td  id="observaciones_tipo3" class="ttitulo">Observaciones</td><td class="valor_observaciones_reserva"></td></tr>

                        </table>
                    </div> 

                    <div class="table-responsive oculto" id="datos_detalle_tipo1" style="width: 100%">
                        <table class="table table-bordered table-condensed table-hover" id="tabla_info_tipo1" style="width: 100%">
                            <thead class="ttitulo ">
                                <tr class=""><td colspan="5" class="nombre_tabla">VIAJES SOLICITADOS</td></tr>
                                <tr class="filaprincipal"><td  class="opciones_tbl">***</td><td>Persona</td><td>Codigo-SAP</td><td>Destino</td><td  class="opciones_tbl">***</td></tr>
                            </thead><tbody></tbody>

                        </table>
                    </div>

                    <div class="table-responsive oculto" id="datos_detalle_tipo2" style="width: 100%">
                        <table class="table table-bordered table-condensed table-hover" id="tabla_info_tipo2" style="width: 100%">
                            <thead class="ttitulo ">
                                <tr class=""><td colspan="9" class="nombre_tabla">TRANSPORTE SOLICITADOS </td></tr>

                                <tr class="filaprincipal"><td  class="opciones_tbl">***</td><td>CodigoSAP</td><td>#Personas</td><td>Destino</td><td  class="opciones_tbl">***</td></tr>
                            </thead><tbody></tbody>

                        </table>
                    </div>
                    <div class="table-responsive oculto" id="datos_detalle_tipo4" style="width: 100%">
                        <table class="table table-bordered table-condensed table-hover" id="tabla_info_tipo4" style="width: 100%">
                            <thead class="ttitulo ">
                                <tr class=""><td colspan="9" class="nombre_tabla">PEDIDOS SOLICITADOS </td></tr>
                                <tr class="filaprincipal"><td  class="opciones_tbl">***</td><td>CodigoSAP</td><td>Fecha Entrega</td><td>Categoria</td><td  class="opciones_tbl">***</td></tr>
                            </thead><tbody></tbody>

                        </table>
                    </div>

                    <div class="table-responsive" id="datos_servicios_com" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed" id="tabla_servicios_solicitud"  cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                            <tr>
                                <td colspan="4" class="nombre_tabla">TABLA DE SERVICIOS</td>
                            </tr>
                            <tr class="filaprincipal ">
                                <td class="opciones_tbl">Ver</td>
                                <td>Nombre Servicio</td>
                                <td>Fecha</td>
                                <td>Solicitante</td>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="modal-footer" id="footermodal">
                <button   type="button" class="btn btn-danger active btnAgregar btn_asignar_detalle" id="btn_asignar_detalle_info"><span class="btn-Efecto-men  fa fa-plus"></span> Detalle</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>


        </div>


    </div>
</div>



<div class="modal fade scroll-modal" id="Modal-info-solicitud-tipo4" role="dialog" >
    <div id="modal-dialog" class="modal-dialog modal-lg" >

        <!-- Modal content-->
        <div class="modal-content " >
            <div class="modal-header" id="headermodal" >
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span>   Información Solicitud</h3>
            </div>
            <div class="modal-body" id="bodymodal">       

                <table class="table table-bordered table-condensed" >
                    <tr class=""><th class="nombre_tabla" colspan="2"> Detalle Solicitud </th></tr>
                    <tr class="datos-tipo-4 "><td class="ttitulo">Categoria</td><td class="valor_categoria_tipo4"></td></tr>
                    <tr class="datos-tipo-4 "><td class="ttitulo">Archivos Adjuntos</td><td class="valor_adjuntos_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td  class="ttitulo">Codigo SAP</td><td class="valor_codigo_sap_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td class="ttitulo">Fecha Entrega</td><td class="valor_fecha_entrega_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td class="ttitulo">lugar</td><td class="valor_lugar_entrega_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td class="ttitulo">#Personas</td><td class="valor_personas_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td class="ttitulo">Responsable</td><td class="valor_responsable_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td class="ttitulo">Celular Responsable</td><td class="valor_celular_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_manteles t4_oculto"><td  class="ttitulo">#Manteles</td><td class="valor_manteles_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_sillas t4_oculto"><td  class="ttitulo">#Sillas</td><td class="valor_sillas_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_carpas t4_oculto"><td  class="ttitulo">#Carpas</td><td class="valor_carpas_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_vasos t4_oculto"><td  class="ttitulo">#Vasos</td><td class="valor_vasos_tipo4"></td></tr>

                    <tr class="datos-tipo-4 tr_tenedores t4_oculto"><td class="ttitulo">#Tenedores</td><td class="valor_tenedores_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_mesas t4_oculto"><td class="ttitulo">Tipo mesas</td><td class="valor_tipo_mesa_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_mesas t4_oculto"><td class="ttitulo">#Mesas</td><td class="valor_mesa_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_cuchillos t4_oculto"><td class="ttitulo">#Cuchillos</td><td class="valor_cuchillos_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_platos t4_oculto"><td class="ttitulo">Tipo platos</td><td class="valor_tipo_plato_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_platos t4_oculto"><td class="ttitulo">#Platos</td><td class="valor_platos_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_cucharas t4_oculto"><td class="ttitulo">Tipo Cucharas</td><td class="valor_tipo_cucharas_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_cucharas t4_oculto"><td class="ttitulo">#cucharas</td><td class="valor_cucharas_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_flores t4_oculto"><td class="ttitulo">Valor Flores</td><td class="valor_flores_tipo4"></td></tr>

                    <tr class="datos-tipo-4 tr_refri t4_oculto"><td class="ttitulo">Tipo Refrigerios</td><td class="valor_tipo_refri_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_refri t4_oculto"><td class="ttitulo">Cantidad X persona</td><td class="valor_cantidad_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_refri t4_oculto"><td class="ttitulo">Entrega Refrigerios</td><td class="valor_entrega_re_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_coctel t4_oculto"><td class="ttitulo">Coctel: </td><td class="valor_coctel_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_cafe t4_oculto"><td class="ttitulo">Entrega Cafe y Agua</td><td class="valor_entr_caf_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_almu t4_oculto"><td class="ttitulo">Con Almuerzo</td><td class="valor_alm_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_recur t4_oculto"><td class="ttitulo">Con Video Beam</td><td class="valor_recu_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_por t4_oculto"><td class="ttitulo">Con Portatil</td><td class="valor_port_tipo4"></td></tr>
                    <tr class="datos-tipo-4 tr_son t4_oculto"><td class="ttitulo">Con Sonido</td><td class="valor_sonido_tipo4"></td></tr>
                    <tr class="datos-tipo-4"><td  class="ttitulo">Observaciones</td><td class="valor_observaciones_tipo4"></td></tr>

                </table>
             
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="Modal-info-inscripcion" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span>   Información Inscripción</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed  tabla_info_inscripcion" id="">
                    <tr><th class="ttitulo text-center" colspan="2"> Datos</th></tr>
                    <tr><td class="ttitulo">Valor:</td><td class="valor_inscripcion"></td></tr>
                    <tr><td class="ttitulo">Con Descuento: </td><td  class="valor_con_descuento"></td></tr>
                    <tr><td class="ttitulo">Contacto: </td><td  class="valor_contacto"></td></tr>
                    <tr><td class="ttitulo">Telefono: </td><td class="valor_telefono_contacto"></td></tr>
                    <tr><td class="ttitulo">Celular:</td><td class="valor_celular_contacto"></td></tr>
                    <tr><td class="ttitulo">Pagina WEB: </td><td class="valor_pagina"></td></tr>
                    <tr><td class="ttitulo">Correo:</td><td class="valor_correo_contacto"></td></tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detalle_servicio" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span> Información Completa</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed  tabla_info_inscripcion" id="">
                    <tr id='tr_tipo_ser'><td class="ttitulo">Tipo:</td><td class="tipo_servicio"></td></tr>
                    <tr id='tr_tipo_entrega_ser'><td class="ttitulo">Tipo Entrega: </td><td  class="tipo_entrega_servicio"></td></tr>
                    <tr id='tr_cantidad_ser'><td class="ttitulo">Cantidad - Valor: </td><td  class="cantidad_servicio"></td></tr>
                    <tr id='tr_observaciones_ser'><td class="ttitulo">Observaciones: </td><td  class="observaciones_servicio"></td></tr>
                </table>
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Modal-info-tiquete-seleccionado" role="dialog" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span>   Detalle del Itinerario</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inscripcion" id="">
                    <tr ><td colspan="2" style="" class="nombre_tabla text-left"><p> Datos</p></td></tr>
                    <tr><td class="ttitulo">Persona:</td><td class="valor_persona_ti"></td></tr>
                    <tr><td class="ttitulo">Identificación: </td><td  class="valor_identificacion_ti"></td></tr>
                    <tr><td class="ttitulo">Codigo SAP:</td><td class="valor_codigo_sap_ti"></td></tr>
                    <tr><td class="ttitulo">Lugar Origen:</td><td class="valor_lugar_origen"></td></tr>
                    <tr><td class="ttitulo">Lugar Destino: </td><td  class="valor_lugar_destino"></td></tr>
                    <tr><td class="ttitulo">Requiere Viaticos:</td><td class="valor_req_viaticos"></td></tr>
                    <tr><td class="ttitulo">Requiere Tiquetes: </td><td class="valor_req_tiquete"></td></tr>
                    <tr class="fechas_tiquetes oculto"><td class="ttitulo" >Fecha Salida Tiquetes:</td><td class="valor_fecha_salida"></td></tr>
                    <tr class="fechas_tiquetes oculto"><td class="ttitulo">Fecha Retorno Tiquetes: </td><td class="valor_fecha_retorno"></td></tr>

                    <tr><td class="ttitulo">Requiere Hotel:</td><td class="valor_req_hotel"></td></tr>
                    <tr class="fechas_hoteles oculto"><td class="ttitulo" >Fecha Ingreso Hotel:</td><td class="valor_fecha_ingreso_hotel"></td></tr>
                    <tr class="fechas_hoteles oculto"><td class="ttitulo" >Fecha Salida Hotel: </td><td class="valor_fecha_salida_hotel"></td></tr>
                    <tr><td class="ttitulo">Requiere Seguro Medico:</td><td class="valor_req_seguro"></td></tr>
                    <tr class="datos_adjuntos oculto"><td class="ttitulo">Pasaporte: </td><td class="valor_datos_Adjuntos"></td></tr>
                    <tr class="datos_visa oculto"><td class="ttitulo">VISA: </td><td class="valor_datos_visa"></td></tr>
                    <tr class="datos_otro oculto"><td class="ttitulo">Agenda o información del Evento: </td><td class="valor_datos_otro"></td></tr>
                    <tr><td class="ttitulo">Observaciones:</td><td class="valor_observaciones_tique"></td></tr>
                    <tr><td class="ttitulo">Fecha Registro: </td><td  class="valor_fecha_registro_ti"></td></tr>
                </table>


            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>


        </div>


    </div>
</div>
<div class="modal fade scroll-modal" id="Modal-info-transporte-seleccionado" role="dialog" >
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span>   Detalle del Transporte</h3>
            </div>
            <div class="modal-body" id="bodymodal">
            <div>  
                <table class="table table-bordered table-condensed  tabla_info_inscripcion" id="">

                    <tr ><td colspan="2" style="" class="nombre_tabla text-left"><p> Datos</p></td></tr>
                    <tr><td class="ttitulo">Codigo SAP:</td><td class="valor_codigosap_trans"></td></tr>
                    <tr><td class="ttitulo">#Personas:</td><td class="valor_personas_transp"></td></tr>
                    <tr><td class="ttitulo">Lugar Origen:</td><td class="valor_lugar_origen_transp"></td></tr>
                    <tr><td class="ttitulo">Lugar Destino: </td><td  class="valor_lugar_destino_transp"></td></tr>
                    <!--<tr ><td class="ttitulo" >Fecha Salida :</td><td class="valor_fecha_salida_transp"></td></tr>
                    <tr ><td class="ttitulo">Fecha Retorno : </td><td class="valor_fecha_retorno_transp"></td></tr>-->
                    <tr><td class="ttitulo">Fecha Registro: </td><td class="valor_fecha_registro_trans"></td></tr>
                    <tr><td class="ttitulo">Observaciones:</td><td class="valor_observaciones_trans"></td></tr>
                </table>
                </div>  
                <div class="table-responsive" >
                    <table class="table table-bordered table-hover table-condensed table-responsive" id="tablaresponsables_buses"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">
                        <tr ><td colspan="4" style="" class="nombre_tabla text-left"><p> Responsables Asignados</p></td><td class="sin-borde text-center"><span title="Mas Responsables" data-toggle="popover" data-trigger="hover" class="fa fa-plus btn-Efecto-men pointer" id="asignar_mas_responsables"></span></td></tr>
                            <tr class="filaprincipal"><td>Nombre Completo</td><td>Identificación</td><td>Telefono</td><td>Correo</td><td  class="opciones_tbl">***</td></tr>
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



<div class="modal fade scroll-modal" id="Modal-info-responsables-tipo3" role="dialog" >
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span> Datos Responsables</h3>
            </div>
            <div class="modal-body" id="bodymodal">

                <div class="table-responsive" >
                    <table class="table table-bordered table-hover table-condensed table-responsive" id="tablaresponsables_tipo3"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">
                        <tr ><td colspan="4" style="" class="nombre_tabla text-left"><p> Responsables Asignados</p></td><td class="sin-borde text-center"><span title="Mas Responsables" data-toggle="popover" data-trigger="hover" class="btn-Efecto-men pointer black-color" id="asignar_mas_responsables_capa"><span class="fa fa-plus red" ></span></span></td></tr>
                            <tr class="filaprincipal"><td>Nombre Completo</td><td>Identificación</td><td>Telefono</td><td>Correo</td><td  class="opciones_tbl">***</td></tr>
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




<div class="modal fade scroll-modal" id="Modal-add-transporte" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="Guardar_trasnporte" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-navicon"></span> Detalle Solicitud</h3>
                </div>

                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="" id="panel_info_buses">

                            <div id="panel" class="">
                                <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>

                                <div class="" >

                                    <div class="panel-body">
                                        <div class="input-group agro">

                                            <select name="personal_asignado" class="form-control cbx_personal_Asignado sin_margin" id="personal_asignado-combo"> <option>Responsables Asignados</option> </select> 
                                            <span class="input-group-addon "> <span  class="glyphicon glyphicon-remove btn-Efecto-men pointer" id="Retirar_persona_sele" title="Retirar Responsable" data-toggle="popover" data-trigger="hover"></span>  </span>  
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-plus btn-Efecto-men btnAgregar pointer"id="buscar_persona_sele" title="Mas Responsables" data-toggle="popover" data-trigger="hover" ></span> </span>

                                        </div>


                                        <input type="text" class="form-control"  placeholder="Codigo SAP"  name="codsapbuses" id="codigo_sap_input" required="">

                                        <input type="number" class="form-control CampoGeneral"  placeholder="# Personas" required="" step="1" min="1"  name="num_personas">

                                        <div class="agro" >

                                            <div class="input-group"> 
                                                <input type="text" class="form-control CampoGeneral requerido sin_margin" name="origen" placeholder="Direccion Origen" required="">
                                                <span class="input-group-addon">-</span>
                                                <input type="text" class="form-control CampoGeneral requerido sin_margin" name="destino" placeholder="Direccion Destino" required="">

                                            </div>
                                        </div>
                                        <!--
                                        <div class="agro">

                                            <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                                <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida" type="text" value=""  name="hora_salida" required="true">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                            </div>

                                        </div>

                                        <div class="agro">

                                            <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                                <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno" type="text" value=""  name="hora_retorno" required="true">
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                                <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                            </div>

                                        </div>-->

                                        <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones"></textarea>

                                    </div>


                                </div>

                            </div>


                        </div>



                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" id="" ><span class=" glyphicon glyphicon-log-out"></span> Guardar</button> 

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>


            </div>

        </form>
    </div>
</div>

<form action="#" id="Guardar_bodega" method="post">
    <div class="modal fade scroll-modal" id="Modal-add-bodega" role="dialog">  
        <div class="fixed requerimientos_tipo4 oculto">
            <div class="reque">

                <div class="login-container">


                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Requerimientos<span class="" id=""></span></td><td> <p class="glyphicon glyphicon-remove pointer cerrar-reque" id="" title="Cerrar" data-toggle="popover" data-trigger="hover"></p></td></tr>

                        </thead>

                    </table>

                    <div class="form-boxw text-left">
                        <ul class="sin-decoration">
                            <div class="" id="to_eventos">
                                <li><label> <input type="checkbox" name="re_platos" value="1" id="re_platos">Requiere Platos.</label></li>
                                <li><label><input type="checkbox" name="re_cucharas" value="1" id="re_cucharas">Requiere Cucharas.</label></li>
                                <li><label><input type="checkbox" name="re_mesas" value="1" id="re_mesas">Requiere Mesas.</label></li>
                                <li><label> <input type="checkbox" name="re_cuchillos" value="1" id="re_cuchillos">Requiere Cuchillos.</label></li>
                                <li><label> <input type="checkbox" name="re_tenedores" value="1" id="re_tenedores">Requiere Tenedores.</label></li>
                                <li><label><input type="checkbox" name="re_vasos" value="1" id="re_vasos">Requiere Vasos.</label></li>
                                <li><label><input type="checkbox" name="re_carpas" value="1" id="re_carpas">Requiere Carpas.</label></li>
                                <li><label><input type="checkbox" name="re_sillas" value="1" id="re_sillas">Requiere Sillas.</label></li>
                                <li><label><input type="checkbox" name="re_manteles" value="1" id="re_manteles">Requiere Manteles.</label></li> 
                                <li><label><input type="checkbox" name="re_coctel" value="1" id="coctel">Requiere coctel.</label></li> 
                            </div>
                            <li><label><input type="checkbox" name="re_flores" value="1" id="re_flores">Requiere Flores.</label></li> 
                            <li><label><input type="checkbox" name="re_refri" value="1" id="re_refri">Requiere Refrigerios.</label></li> 
                            <li><label><input type="checkbox" name="re_almuerzo" value="1" id="re_almuerzo">Requiere Almuerzo.</label></li> 

                        </ul>
                    </div>
                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Solo Aplica para reservas<span class="" id=""></span></td></tr>

                        </thead>

                    </table>

                    <div class="form-boxw text-left margin1">
                        <ul class="sin-decoration">
                            <li><label><input type="checkbox" name="re_agua" value="1" id="re_agua">Requiere Cafe y Agua.</label></li> 
                            <li><label><input type="checkbox" name="re_vb" value="1" id="re_vb">Requiere Video Beam.</label></li> 
                            <li><label><input type="checkbox" name="re_soni" value="1" id="re_soni">Requiere Sonido.</label></li> 
                            <li><label><input type="checkbox" name="re_port" value="1" id="re_port">Requiere Portatil.</label></li> 

                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-navicon"></span> Detalle Solicitud</h3>
                </div>

                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="" id="panel_info_buses">

                            <div id="panel" class="">
                                <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>
                                <div class="row text-right">
                                    <div>
                                        <span class = "fa fa-check-square-o pointer mostrar-reque requerimientos_tipo4 oculto">Ver Requerimientos</span>
                                    </div>
                                    <select class="form-control  cbx_categorias_tipo4" id="cbx_categorias_tipo4" name="tipo_logistica" required=""><option>Seleccione Categoria</option></select>
                                    <div class="agrupado "><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivologistica" type="file" style="display: none;"></span></label><input type="text" class="form-control" readonly placeholder="Adjuntar Archivo(Opcional)"></div></div>
                                    <div class="input-group agro">
                                        <input name="responsable" type="hidden" id="input_sele_responsable_bodega">
                                        <span class="form-control text-left pointer persona_responsable_bodega sin_margin sele_perso_bodega" id="persona_responsable_bodega">Seleccione Responsable</span>
                                        <span class="input-group-addon sele_perso_bodega" id="sele_perso_bodega" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class = "glyphicon glyphicon-search red_primari pointer btn-Efecto-men "></span></span>
                                    </div> 
                                    <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                     <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Entrega" type="text" value="" required="true" name="fecha_entrega">
                                     <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                     <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                   </div>
                                    <input type="number" class="form-control CampoGeneral"  required="true" placeholder="# Personas"  step="1" min="1"  name="num_personas">
                                    <input type="text" class="form-control"  placeholder="Codigo SAP"  name="codigosap" id="codigo_sap_input_bodega" required="">
                                    <div class="agro ">
                                        <input type="text" class="form-control CampoGeneral"  placeholder="Lugar" required="true" name="lugar_entrega">

                                        <div id="campos_for_t4">
                                            <input type="number" class="form-control CampoGeneral  inp_manteles oculto"  placeholder="# Manteles"  step="1" min="1"  name="num_manteles">
                                            <input type="number" class="form-control CampoGeneral  inp_sillas oculto"  placeholder="# Sillas"  step="1" min="1"  name="num_sillas">
                                            <input type="number" class="form-control CampoGeneral  inp_carpas oculto"  placeholder="# Carpas"  step="1" min="1"  name="num_carpas">
                                            <input type="number" class="form-control CampoGeneral  inp_vasos oculto"  placeholder="# Vasos"  step="1" min="1"  name="num_vasos">
                                            <input type="number" class="form-control CampoGeneral  inp_tenedores oculto"  placeholder="# Tenedores"  step="1" min="1"  name="num_tenedores">
                                            <input type="number" class="form-control CampoGeneral  inp_cuchillos oculto"  placeholder="# Cuchillos"  step="1" min="1"  name="num_cuchillos">

                                            <select class="form-control  cbx_tipo_mesas oculto inp_mesas" id="cbx_tipo_mesas" name="tipo_mesas" ><option>Tipo Mesa</option></select>
                                            <input type="number" class="form-control CampoGeneral  inp_mesas oculto"  placeholder="# Mesas"  step="1" min="1"  name="num_mesas">
                                            <select class="form-control  cbx_tipo_cucharas oculto inp_cucharas" name="tipo_cucharas" id="cbx_tipo_cucharas"><option>Tipo Cucharas</option></select>
                                            <input type="number" class="form-control CampoGeneral  inp_cucharas oculto"  placeholder="# Cucharas"  step="1" min="1"  name="num_cucharas">
                                            <select class="form-control  cbx_tipo_platos oculto inp_platos " id="cbx_tipo_platos" name="tipo_platos" ><option>Tipo Platos</option></select>
                                            <input type="number" class="form-control CampoGeneral  inp_platos oculto"  placeholder="# Platos"  step="1" min="1"  name="num_platos">
                                            <input type="number" class="form-control CampoGeneral  inp_flores oculto"  placeholder="Valor Flores"  step="1" min="1"  name="num_flores">
                                            <select class="form-control  oculto inp_refrigerios cbxrefrigerios" id="cbxrefrigerios_eve" name="tipo_refrigerios" ><option>Tipo Refrigerios</option></select>
                                            <input type="number" class="form-control CampoGeneral  inp_refrigerios oculto"  placeholder="Cantidas por persona"  step="1" min="1"  name="canxperso">
                                            <select class="form-control  oculto inp_refrigerios"  name="tipo_refrigerios_entrega" ><option value="">Entrega de Refrigerios</option><option value="Mañana">Mañana</option><option value="Tarde">Tarde</option><option value="Mañana y Tarde">Mañana y Tarde</option></select>
                                            <select class="form-control  oculto inp_cafe_agua"  name="tipo_agua_cafe_entrega" ><option value="">Entrega de Cafe y Agua</option><option value="Mañana">Mañana</option><option value="Tarde">Tarde</option><option value="Mañana y Tarde">Mañana y Tarde</option></select>
                                            <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones"></textarea>
                                        </div>
                                    </div>





                                </div>


                            </div>



                        </div>
                    </div>



                </div>
                <div class="modal-footer margin1" id="">
                    <button type="submit" class="btn btn-danger active" id="" ><span class=" glyphicon glyphicon-log-out"></span> Guardar</button> 

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

            </div>
        </div>
    </div>
</form>

<form action="#" id="Modificar_bodega" method="post">
    <div class="modal fade scroll-modal" id="Modal-modificarbodega" role="dialog">
        <div class="fixed fixed_eventos_modi requerimientos_tipo4_modi" >
            <div class="reque">

                <div class="login-container">


                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Requerimientos<span class="" id=""></span></td><td> <p class="glyphicon glyphicon-remove pointer cerrar-reque" id="" title="Cerrar" data-toggle="popover" data-trigger="hover"></p></td></tr>

                        </thead>

                    </table>

                    <div class="form-boxw text-left">
                        <ul class="sin-decoration">
                            <div class="" id="to_eventos_modi">
                                <li><label> <input type="checkbox" name="re_platos" value="1" id="re_platos_modi">Requiere Platos.</label></li>
                                <li><label><input type="checkbox" name="re_cucharas" value="1" id="re_cucharas_modi">Requiere Cucharas.</label></li>
                                <li><label><input type="checkbox" name="re_mesas" value="1" id="re_mesas_modi">Requiere Mesas.</label></li>
                                <li><label> <input type="checkbox" name="re_cuchillos" value="1" id="re_cuchillos_modi">Requiere Cuchillos.</label></li>
                                <li><label> <input type="checkbox" name="re_tenedores" value="1" id="re_tenedores_modi">Requiere Tenedores.</label></li>
                                <li><label><input type="checkbox" name="re_vasos" value="1" id="re_vasos_modi">Requiere Vasos.</label></li>
                                <li><label><input type="checkbox" name="re_carpas" value="1" id="re_carpas_modi">Requiere Carpas.</label></li>
                                <li><label><input type="checkbox" name="re_sillas" value="1" id="re_sillas_modi">Requiere Sillas.</label></li>
                                <li><label><input type="checkbox" name="re_manteles" value="1" id="re_manteles_modi">Requiere Manteles.</label></li> 
                                <li><label><input type="checkbox" name="re_coctel" value="1" id="re_coctel_modi">Requiere coctel.</label></li> 
                            </div>
                            <li><label><input type="checkbox" name="re_flores" value="1" id="re_flores_modi">Requiere Flores.</label></li> 
                            <li><label><input type="checkbox" name="re_refri" value="1" id="re_refri_modi">Requiere Refrigerios.</label></li> 
                            <li><label><input type="checkbox" name="re_almuerzo" value="1" id="re_almuerzo_modi">Requiere Almuerzo.</label></li> 
                        </ul>
                    </div>
                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Solo Aplica para reservas<span class="" id=""></span></td></tr>

                        </thead>

                    </table>
                    <div class="form-boxw text-left margin1">
                        <ul class="sin-decoration">
                            <li><label><input type="checkbox" name="re_agua" value="1" id="re_agua_modi">Requiere Cafe y Agua.</label></li> 
                            <li><label><input type="checkbox" name="re_vb" value="1" id="re_vb_modi">Requiere Video Beam.</label></li> 
                            <li><label><input type="checkbox" name="re_soni" value="1" id="re_soni_modi">Requiere Sonido.</label></li> 
                            <li><label><input type="checkbox" name="re_port" value="1" id="re_port_modi">Requiere Portatil.</label></li> 

                        </ul>
                    </div>                                                                                            

                </div>
            </div>
        </div>
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class=" fa fa-wrench"></span> Modificar Detalle Solicitud</h3>
                </div>

                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">
                        <div class="" id="panel_info_buses">

                            <div id="panel" class="">
                                <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>

                                <div class="" >



                                    <div class="row text-right">
                                        <div>
                                            <span class = "fa fa-check-square-o pointer mostrar-reque-modi requerimientos_tipo4_modi oculto">Ver Requerimientos</span>
                                        </div>
                                        <select class="form-control  cbx_categorias_tipo4" id="cbx_categorias_tipo4_modi" name="tipo_logistica" required=""><option>Seleccione Categoria</option></select>
                                        <div class="agrupado "><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivologistica" type="file" style="display: none;"></span></label><input type="text" class="form-control" readonly placeholder="Adjuntar Archivo(Opcional)"></div></div>
                                        <div class="input-group agro">
                                            <input name="responsable" type="hidden" id="input_sele_responsable_bodega_modi">
                                            <span class="form-control text-left pointer persona_responsable_bodega_modi sele_perso_bodega_modi sin_margin" id="persona_responsable_bodega_modi">Seleccione Responsable</span>
                                            <span class="input-group-addon  red_primari pointer btn-Efecto-men sele_perso_bodega_modi" id="sele_perso_bodega_modi" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                                        </div>  
                                        <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                            <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Entrega" type="text" value="" required="true" name="fecha_entrega" id="fecha_entrega_modi_t4">
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                        </div>
                                        <input type="number" class="form-control CampoGeneral"  required="true" placeholder="# Personas"  step="1" min="1"  name="num_personas" id="inp_num_per_modi">
                                        <input type="text" class="form-control"  placeholder="Codigo SAP"  name="codigosap" id="codigo_sap_input_bodega_modi" required="" >
                                        <input type="text" class="form-control CampoGeneral"  placeholder="Lugar Entrega" id="inp_lugar_entrega_modi" required="true" name="lugar_entrega">

                                        <div id="campos_for_t4_modi">
                                            <input type="number" class="form-control CampoGeneral oculto inp_manteles_modi" id="inp_manteles_modi"  placeholder="# Manteles"  step="1" min="1"  name="num_manteles">
                                            <input type="number" class="form-control CampoGeneral oculto inp_sillas_modi" id="inp_sillas_modi" placeholder="# Sillas"  step="1" min="1"  name="num_sillas">
                                            <input type="number" class="form-control CampoGeneral oculto inp_carpas_modi" id="inp_carpas_modi" placeholder="# Carpas"  step="1" min="1"  name="num_carpas">
                                            <input type="number" class="form-control CampoGeneral oculto inp_vasos_modi"  id="inp_vasos_modi" placeholder="# Vasos"  step="1" min="1"  name="num_vasos">
                                            <input type="number" class="form-control CampoGeneral oculto inp_tenedores_modi"  id="inp_tenedores_modi" placeholder="# Tenedores"  step="1" min="1"  name="num_tenedores">
                                            <input type="number" class="form-control CampoGeneral oculto inp_cuchillos_modi"  id="inp_cuchillos_modi" placeholder="# Cuchillos"  step="1" min="1"  name="num_cuchillos">

                                            <select class="form-control  cbx_tipo_mesas oculto inp_mesas_modi" id="cbx_tipo_mesas_modi" name="tipo_mesas" ><option>Tipo Mesa</option></select>
                                            <input type="number" class="form-control CampoGeneral oculto inp_mesas_modi" id="inp_mesas_modi" placeholder="# Mesas"  step="1" min="1"  name="num_mesas">
                                            <select class="form-control  cbx_tipo_cucharas oculto inp_cucharas_modi" name="tipo_cucharas" id="cbx_tipo_cucharas_modi"><option>Tipo Cucharas</option></select>
                                            <input type="number" class="form-control CampoGeneral oculto inp_cucharas_modi" id="inp_cucharas_modi"  placeholder="# Cucharas"  step="1" min="1"  name="num_cucharas">
                                            <select class="form-control  cbx_tipo_platos oculto inp_platos_modi" id="cbx_tipo_platos_modi" name="tipo_platos" ><option>Tipo Platos</option></select>
                                            <input type="number" class="form-control CampoGeneral oculto inp_platos_modi" id="inp_platos_modi"  placeholder="# Platos"  step="1" min="1"  name="num_platos">
                                            <input type="number" class="form-control CampoGeneral  inp_flores_modi oculto" id="valor_flores_modi" placeholder="Valor Flores"  step="1" min="1"  name="num_flores">
                                            <select class="form-control  oculto inp_refrigerios_modi cbxrefrigerios" name="tipo_refrigerios" id="cbx_refrigerios_modi"><option>Tipo Refrigerios</option></select>
                                            <input type="number" class="form-control CampoGeneral  inp_refrigerios_modi oculto" id="canxpersona_modi" placeholder="Cantidas por persona"  step="1" min="1"  name="canxperso">
                                            <select class="form-control  oculto inp_refrigerios_modi"  name="tipo_refrigerios_entrega" id="tipo_entre_ref_modi"><option value="">Entrega de Refrigerios</option><option value="Mañana">Mañana</option><option value="Tarde">Tarde</option><option value="Mañana y Tarde">Mañana y Tarde</option></select>
                                            <select class="form-control  oculto inp_cafe_agua_modi" id="tipo_entr_ca_modi" name="tipo_agua_cafe_entrega" ><option value="">Entrega de Cafe y Agua</option><option value="Mañana">Mañana</option><option value="Tarde">Tarde</option><option value="Mañana y Tarde">Mañana y Tarde</option></select>
                                        </div>

                                        <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id="observaciobes_bode_modi"></textarea>

                                    </div>


                                </div>

                            </div>


                        </div>



                    </div>
                </div>

                <div class="modal-footer " id="">
                    <button type="submit" class="btn btn-danger active" id="" ><span class=" glyphicon glyphicon-log-out"></span> Modificar</button> 

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>


            </div>


        </div>
    </div>
</form>
<!-- aqui 2 -->
<form action="#" id="Guardar_Itinerario" method="post">
    <div class="modal fade scroll-modal" id="Modal-add-itinerario" role="dialog">
        <div class="fixed fixed_viaticos" >
            <div class="reque">

                <div class="login-container">


                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Requerimientos<span class="" id=""></span></td><td> <p class="glyphicon glyphicon-remove pointer cerrar-reque" id="" title="Cerrar" data-toggle="popover" data-trigger="hover"></p></td></tr>

                        </thead>

                    </table>
<!-- Aqui es el modal -->
                    <div class="form-boxw text-left">
                        <ul class="sin-decoration">
                            <li class="oculto" id="viaticos"><label> <input type="checkbox" name="re_viaticos" value="1" id="re_viaticos">Requiere Viaticos.</label></li>
                            <li class="oculto" id="tiquete"><label><input type="checkbox" name="re_tiquete" value="1" id="re_tiquete">Requiere Tiquetes.</label></li>
                            <li class="oculto" id="hotel"><label ><input type="checkbox" name="re_hotel" value="1"  id="re_hotel">Requiere Hotel.</label></li>
                            <li class="oculto" id="seguro"><label ><input type="checkbox" name="re_seguro" value="1" id="re_seguro">Requiere Seguro Medico.</label></li>
                            <li class="oculto" id="mul_des" ><label ><input type="checkbox" name="re_mul_des" value="1" id="re_mul_des"> Multiples Destinos</label></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-navicon"></span>  Detalle Solicitud</h3>
                </div>

                <div class="modal-body" id="bodymodal">
                    <div class="row" id="" style="width: 100%">

                        <div class="text-center" id="panel_info_itinerario">
                            <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>


                            <table class="table table-bordered margin1 sin-borde" id="">
                                <thead class="ttitulo ">
                                    <tr ><td colspan="15" style="" class="nombre_tabla" id="nombre_itine"><p>Datos del Itinerario</p></td></tr>
                                </thead>
                            </table>
                            <div class="row text-right">
                                <div id="datos_destino">
                                    <span class = "fa fa-check-square-o pointer mostrar-reque-viaticos">Ver Requerimientos</span>

                                    <input type="text" class="form-control"  placeholder="Codigo SAP"  name="codsap" required="" id="codigo_sap_tipo1">
                                    <div class="input-group agro">

                                    <select name="personal_asignado" class="form-control cbx_personal_Asignado sin_margin" id="personal_asignado-combo-par"> <option>Participantes Asignados</option> </select> 
                                    <span class="input-group-addon "> <span  class="glyphicon glyphicon-remove btn-Efecto-men pointer" id="Retirar_persona_sele_tique" title="Retirar Responsable" data-toggle="popover" data-trigger="hover"></span>  </span>  
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-plus btn-Efecto-men btnAgregar pointer" id="sele_perso_tiquetes" title="Mas Participantes" data-toggle="popover" data-trigger="hover" ></span> </span>

                                    </div>
<!-- aqui adjunto -->
                                    <div id="adjunto_inte" class="text-left">
                                        <div class="agrupado ocultar_adjunto oculto"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivopersona" type="file" style="display: none;" id="archivopersona"></span></label><input type="text" class="form-control" readonly></div></div>
                                        <div class="ocultar_adjunto oculto"><p>Adjuntar Pasaporte</p></div> 
                                        <div class="agrupado ocultar_adjunto oculto"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivovisa" type="file" style="display: none;" id="archivovisa"></span></label><input type="text" class="form-control" readonly></div></div>
                                        <div class="ocultar_adjunto oculto"><p>Adjuntar VISA</p></div>  
                                        <!-- <div class="agrupado"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivootro" type="file" style="display: none;" id="archivootro"></span></label><input type="text" class="form-control" readonly></div></div> -->
                                        <div id="adjuntar_ev_adm"></div> 


                                    </div>

                                </div>
                                <div id="destinos" class="text-right">
                                    <span class = "fa fa-mail-reply-all pointer oculto" id="regresar_multip">Regresar</span>
                                    <input type="text" list="lugares" class="form-control CampoGeneral requerido" name="origen" placeholder="Lugar Origen" required="" >
                                    <input type="text" list="lugares" class="form-control CampoGeneral requerido" name="destino" placeholder="Lugar Destino" required="" >

                                    <datalist id="lugares" >
                                    </datalist>

                                    <div id="requiere_tiquetes" class="oculto">

                                        <div class="agro"><div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Tiquete" type="text" value=""  name="fecha_salida_tiqu" id="fecha_salida_tiqu"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno Tiquete" type="text" value=""   name="fecha_retorno_tiqu" id="fecha_retorno_tiqu"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                                    </div>
                                    <div id="requiere_hoteles" class="oculto">

                                        <div class="agro"><div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Ingreso Hotel" type="text" value=""  name="fecha_ingreso_hotel" id="fecha_ingreso_hotel"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Hotel" type="text" value=""   name="fecha_salida_hotel" id="fecha_salida_hotel"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                                    </div>


                                    <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id=""></textarea>
                                </div>

                            </div>

                        </div>
                    </div>



                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-danger active oculto" id="guardar_mas_datos"><span class="fa fa-plus"></span> Continuar</button>   
                    <button type="submit" class="btn btn-danger active" id="guardar_fin_datos"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

            </div>
        </div>
    </div>
</form>
<div class="modal fade" id="Modal-modificar-tipo3" role="dialog">


    <div class="modal-dialog">
        <form action="#" id="Modificar_solicitud_tipo3" method="post" enctype="multipart/form-data">
            <!-- Modal content-->
            <div class="modal-content" >

                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text"></span>   Modificar Detalle</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="" id="" style="width: 100%">
                        <div class="row" >
                            <h4 class=""><span class="ttitulo">Categoria:</span>  <span class="" id="tipo_reserva_Adm_modi"></span></h4>


                            <input type="text" class="form-control requerido"  placeholder="Codigo SAP"  name="codigo_sap" id="codigo_sap_input_Reserva_modi" required="true">
                            <select name="tipo_refrigerios" class="form-control CampoGeneral cbxrefrigerios  oculto"  id="tipo_refrigerios_modi"><option value="">Seleccione Tipo Refrigerios</option></select>
                            <select name="proveedor" class="form-control CampoGeneral cbxproveedor  oculto"  id="proveedor_modi"><option value="">Seleccione Proveedor</option></select>
                            <select name="tipo_poliza" class="form-control CampoGeneral cbxpolizas  oculto"  id="tipo-polizas_modi"><option value="">Seleccione Tipo Poliza</option></select>
                            <select name="req_viaticos_reserva" class="form-control CampoGeneral oculto"  id="re_viaticos_resereva_modi"><option value="">¿ Seleccione Opcion?</option><option value="Viaticos">Viaticos</option><option value="Tiquetes">Tiquetes</option><option value="Viaticos y tiquetes">Viaticos y tiquetes</option><option value="No Aplica">No Aplica</option></select>
                            <div id="requiere_tiquetes_Reserva_modi" class="oculto">

                                <div class="agro"><div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Tiquete" type="text" value="" required="true"  name="fecha_salida_tiqu_reserva" id="fecha_salida_tiqu_Reserva_modi"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno Tiquete" type="text" value="" required="true" name="fecha_retorno_tiqu_reserva" id="fecha_retorno_tiqu_reserva_modi"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                            </div>
                            <div id="columnas_modi">
                                <input type="text" class="form-control"  placeholder="columna1" required="true" name="columna1" id="columna1_modi" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna2" required="true" name="columna2" id="columna2_modi" step="1" min="1" >
                                <input type="text" class="form-control"  placeholder="columna3" required="true" name="columna3" id="columna3_modi" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna4" required="true" name="columna4" id="columna4_modi" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna5" required="true" name="columna5" id="columna5_modi" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna6" required="true" name="columna6" id="columna6_modi" step="1" min="1">
                            </div>
                            <div id="adjuntos_reserva_modi">

                            </div>
                            <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id="observaciones_reserva_modi"></textarea>





                        </div>

                    </div>
                </div>



                <div class="modal-footer" >
                    <button type="submit" class="btn btn-danger active "><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

            </div>  </form>
    </div>
</div>

<div class="modal fade" id="Modal-guardar-tipo3" role="dialog">


    <div class="modal-dialog">
        <form action="#" id="Guardar_solicitud_tipo3" method="post" enctype="multipart/form-data">
            <!-- Modal content-->
            <div class="modal-content" >

                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text"></span> Detalle Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="" id="" style="width: 100%">
                        <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>

                        <div class="row" >




                            <select name="tipo_reserva_Adm" class="form-control CampoGeneral cbxtiporeserva requerido" required="true" id="tipo_reserva_Adm"><option value="">Seleccione tipo reserva</option></select>

                            <input type="text" class="form-control requerido"  placeholder="Codigo SAP"  name="codigo_sap" id="codigo_sap_input_Reserva" required="true">
                           <div  class="oculto" id="div_responsable_capa"> 

                                <div class="input-group agro">
                                    <select name="personal_asignado" class="form-control sin_margin cbx_personal_Asignado" id="persona_responsable_capa"> <option>Seleccione Responsables</option> </select> 
                                    <span class="input-group-addon "> <span  class="glyphicon glyphicon-remove btn-Efecto-men pointer" id="Retirar_persona_sele_capa" title="Retirar Responsable" data-toggle="popover" data-trigger="hover"></span>  </span>  
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-plus btn-Efecto-men btnAgregar pointer"id="buscar_persona_sele_capa" title="Mas Responsables" data-toggle="popover" data-trigger="hover" ></span> </span>
                                </div>

                            </div>                                                                               

                            <select name="tipo_refrigerios" class="form-control CampoGeneral cbxrefrigerios  oculto"  id="tipo_refrigerios"><option value="">Seleccione Tipo Refrigerios</option></select>
                            <select name="proveedor" class="form-control CampoGeneral cbxproveedor  oculto"  id="proveedor"><option value="">Seleccione Proveedor</option></select>
                            <select name="tipo_poliza" class="form-control CampoGeneral cbxpolizas  oculto"  id="tipo-polizas"><option value="">Seleccione Tipo Poliza</option></select>
                            <select name="req_viaticos_reserva" class="form-control CampoGeneral oculto"  id="re_viaticos_resereva"><option value="">¿ Seleccione Opcion?</option><option value="Viaticos">Viaticos</option><option value="Tiquetes">Tiquetes</option><option value="Viaticos y tiquetes">Viaticos y tiquetes</option><option value="No Aplica">No Aplica</option></select>
                            <div id="requiere_tiquetes_Reserva" class="oculto">

                                <div class="agro"><div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Tiquete" type="text" value=""  name="fecha_salida_tiqu_reserva" id="fecha_salida_tiqu_Reserva"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno Tiquete" type="text" value=""  name="fecha_retorno_tiqu_reserva" id="fecha_retorno_tiqu_reserva"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                            </div>
                            <div id="columnas">
                                <input type="text" class="form-control"  placeholder="columna1" required="true" name="columna1" id="columna1" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna2" required="true" name="columna2" id="columna2" step="1" min="1" >
                                <input type="text" class="form-control"  placeholder="columna3" required="true" name="columna3" id="columna3" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna4" required="true" name="columna4" id="columna4" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna5" required="true" name="columna5" id="columna5" step="1" min="1">
                                <input type="text" class="form-control"  placeholder="columna6" required="true" name="columna6" id="columna6" step="1" min="1">
                            </div>
                            <!-- <div class="agro" id="fecha_reserva_div">
 
                                 <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                     <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Reserva" type="text" value="" required="true" name="fecha_entrega_reserva" id="fecha_Reserva">
                                     <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                     <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                 </div>
                             </div>-->
                            <div id="adjuntos">

                            </div>
                            <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id="observaciones_reserva"></textarea>




                        </div>

                    </div>
                </div>



                <div class="modal-footer" >
                    <button type="submit" class="btn btn-danger active "><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

            </div>  </form>
    </div>
</div>
<form action="#" id="modificar_itinerario" method="post">
    <div class="modal fade" id="Modal-modificar-itinerario" role="dialog">
        <div class="fixed fixed_viaticos_modi" >
            <div class="reque">

                <div class="login-container">


                    <table class="" id="" style="width: 100%">
                        <thead class="">
                            <tr class=""><td colspan="" class="nombre_tabla"> Requerimientos<span class="" id=""></span></td><td> <p class="glyphicon glyphicon-remove pointer cerrar-reque" id="" title="Cerrar" data-toggle="popover" data-trigger="hover"></p></td></tr>

                        </thead>

                    </table>

                    <div class="form-boxw text-left">
                        <ul class="sin-decoration">
                            <li><label> <input type="checkbox" name="re_viaticos" value="1" id="re_viaticos_modifica">Requiere Viaticos.</label></li>
                            <li><label><input type="checkbox" name="re_tiquete" value="1" id="re_tiquete_modifica">Requiere Tiquetes.</label></li>
                            <li><label><input type="checkbox" name="re_hotel" value="1"  id="re_hotel_modifica">Requiere Hotel.</label></li>
                            <li><label><input type="checkbox" name="re_seguro" value="1" id="re_seguro_modifica">Requiere Seguro Medico.</label></li>

                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" >    

                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span>   Modificar Detalle</h3>
                </div>
                <div class="modal-body " id="bodymodal">

                    <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>
                    <div class="row">
                        <input type="text" list="lugares" class="form-control CampoGeneral requerido" name="origen" placeholder="Lugar Origen" required="" id="lugar_origen_modifica">
                        <input type="text" list="lugares" class="form-control CampoGeneral requerido" name="destino" placeholder="Lugar Destino" required="" id="lugar_destino_modifica">
                        <div id="requiere_tiquetes_modifica" class="oculto">
                            <div class="agro"><div class="input-group date form_datetime" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Tiquete" type="text" value="" required="true" name="fecha_salida_tiqu" id="fecha_salida_tiqu_modifica"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno Tiquete" type="text" value="" required="true" name="fecha_retorno_tiqu" id="fecha_retorno_tiqu_modifica"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                        </div>
                        <div id="requiere_hoteles_modifica" class="oculto">
                            <div class="agro"><div class="input-group date form_datetime" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Ingreso Hotel" type="text" value="" required="true" name="fecha_ingreso_hotel" id="fecha_ingreso_hotel_modifica"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div><div class="agro"> <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1"><input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida Hotel" type="text" value="" required="true" name="fecha_salida_hotel" id="fecha_salida_hotel_modifica"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span><span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span></div></div>
                        </div>
                        <input type="text" class="form-control"  placeholder="Codigo SAP"  name="codsap" id="codigo_sap_input_modifica" required="">
                        <div id="adjunto_inte_modi" class="text-left">
                            <div class="agrupado ocultar_adjunto_modi oculto"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivopersona" type="file" style="display: none;" id="archivopersona_modi"></span></label><input type="text" class="form-control" readonly></div></div>
                            <div class="ocultar_adjunto_modi oculto"><p>Adjuntar Pasaporte</p></div> 
                            <div class="agrupado ocultar_adjunto_modi oculto"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivovisa" type="file" style="display: none;" id="archivovisa_modi"></span></label><input type="text" class="form-control" readonly></div></div>
                            <div class="ocultar_adjunto_modi oculto"><p>Adjuntar VISA</p></div>  
                            <div class="agrupado"><div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input name="archivootro" type="file" style="display: none;" id="archivootro_modi"></span></label><input type="text" class="form-control" readonly></div></div>
                            <div><p>Adjuntar Agenda o información del Evento</p></div> 

                        </div>
                        <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id="observaciones_input_modifica"></textarea>


                    </div>
                </div>
                <div class="modal-footer" >

                    <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>


            </div> 

        </div>
    </div>
</form>
<form action="#" id="modificar_trasnporte" method="post">
<div class="modal fade" id="Modal-modificar-transporte" role="dialog" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" >    
          
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal" > X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span>   Modificar Transporte</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="text-uppercase text-left"> <p><span class="fa fa-book ttitulo"> <b>Solicitud:</b> </span> <span class="nombre_evento_guardar"></span></p></div>

                    <div class="panel-body" >

                        <input required="" type="text" class="form-control"  placeholder="Codigo SAP"  name="codsapbuses" id="codigo_sap_input_modi">

                        <input type="number" class="form-control CampoGeneral margin1"  placeholder="# Personas" required="" step="1" min="1"  name="num_personas" id="num_personas_modi">

                        <div class="agro" >

                            <div class="input-group"> 
                                <input type="text" class="form-control CampoGeneral requerido" name="origen" placeholder="Direccion Origen" required="" id="dir_origen_modi">
                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control CampoGeneral requerido" name="destino" placeholder="Direccion Destino" required="" id="dir_destino_modi">

                            </div>
                        </div>
                        <!--
                        <div class="agro">

                            <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Salida" type="text" value="" required="true" name="hora_salida" id="hora_salida_modi">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                            </div>

                        </div>

                        <div class="agro">

                            <div class="input-group date form_datetime " data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                <input class="form-control CampoGeneral sin_focus" size="16" placeholder="Fecha Retorno" type="text" value="" required="true" name="hora_retorno" id="hora_retono_modi">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                            </div>

                        </div>
                        -->
                        <textarea class="form-control" placeholder="Observaciones/Información Adicional" name="observaciones" id="observaciones_input_modi"></textarea>



                    </div>



                </div>
                <div class="modal-footer" id="footermodal">

                    <button type="submit" class="btn btn-danger active" id="guardar_fin_via"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>

        
        </div>


    </div>
</div>
</form>
<div class="modal fade" id="Registrar-persona" role="dialog">
    <div class="modal-dialog">
        <form  id="form-ingresar-persona-identidades" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-floppy-disk"></span> Registro de Personas</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                    <div class="row">


                        <h6 class="ttitulo"><span class="glyphicon glyphicon-download"></span> Buscar en Identidades</h6>
                        <div class="input-group agrupado">

                            <input class="form-control text-left sin_margin" id="dato_buscar_identidades" placeholder="Ingrese Identificación de la persona">
                            <span class="input-group-addon" ><span id="Buscar_persona_identidades" class="glyphicon glyphicon-search red_primari pointer"></span></span>
                        </div>



                        <h6 class="ttitulo"><span class="glyphicon glyphicon-indent-left"></span> Datos del Solicitante</h6>
                        <select name="tipo_persona"   required class="form-control  cbxtipopersona" id="cbxtipopersona">  </select>   

                        <select name="tipo_identificacion" id="cbxtipoIdentificacion"  required class="form-control  cbxtipoIdentificacion">  </select>   
                        <input min="1" type="number" name="identificacion" id="txtIdentificacion" class="form-control inputt" placeholder="No. Identificación" required>
                        <div class="agro agrupado">
                            <div class="input-group">
                                <input type="text" name="apellido" id="txtApellido" class="form-control inputt2" placeholder="Primer Apellido"  required>

                                <span class="input-group-addon">-</span>
                                <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2" placeholder="Segundo Apellido" required>

                            </div>
                        </div>

                        <div class="agro agrupado">
                            <div class="input-group">
                                <input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre" required>
                                <span class="input-group-addon">-</span>
                                <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2" placeholder="Segundo Nombre" >

                            </div>
                        </div>

                        <div class="agro">
                                <input type="email" name="correo" id="txtCorreo" class="form-control inputt sin_margin" placeholder="Correo Eléctronico">
                        </div>
                        </div>
                </div>  
                <div class="modal-footer" id="footermodal">

                    <button type="submit" id="btnGuardarVisitante"  class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

                </div>


            </div>
        </form>

    </div>
</div>
<div class="modal fade" id="Modal-selec-personas-gen" role="dialog" >
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-users"></span>  Asignar Persona</h3>
            </div>

            <div class="modal-body" id="bodymodal">
                <div class="row" id="" style="width: 100%">


                    <div id="persona_existente">
                        <div class="text-center" id="panel-selec-personas">

                            <div class="agro col-md-8 text-left">

                                <div class="input-group">
                                    <input id="input_persona_reserva" class="form-control sin_margin" placeholder="Ingrese identificacion, nombre o correo de la persona">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-search pointer" id="buscar_sele_perso"></span>  </span> 

                                </div>

                            </div>

                            <div class="table-responsive col-md-12" style="width: 100%">
                                <!--                                        <div class="text-justify col-md-8" id="con_codigo_tipo1">
                                                                            <p><b class="ttitulo">Atención:</b> Al realizar una solicitud el sistema toma por defecto el código SAP del Departamento del Solicitante, <b>si desea</b> manejar un código SAP por persona presiona <b><label>Aquí: <input type="checkbox" id="concodigo" value="1"></label></b></p>       </div>-->

                                <table class="table table-bordered table-hover table-condensed pointer" id="tablapersonas_general_sele"  cellspacing="0" width="100%" style="">
                                    <thead class="ttitulo ">
                                        <tr class=""><td colspan="15" class="nombre_tabla">TABLA PERSONAS</td></tr>
                                        <tr class="filaprincipal"><td>Nombre Completo</td><td class="">Identificación</td><td>Correo</td></tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

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

<div class="modal fade" id="Modal_filtrar_solcicitud" role="dialog" >

<div class="modal-dialog" >

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="glyphicon glyphicon-filter"></span> Crear Filtros</h3>
        </div>
        <div class="modal-body" id="bodymodal">
            <div class="row">

                <select name=""   class="form-control inputt cbx_estados_solicitud" id="estados_solicitud_filtro">
                    <option value="">Filtrar por estado</option>
                </select> 

                <select name=""   class="form-control inputt cbx_tipo_solicitud" id="tipos_solicitud_filtro">
                    <option value="">Filtrar Tipo de Solicitud</option>
                 </select> 
                <label><input type="checkbox" id="filtrar_solicitud_fecha">Filtrar por Fecha</label>
                <div class="oculto" id="div_fecha_inicio_filtro">
                    <input class="form-control CampoGeneral" size="16" placeholder="Fecha Solicitud" type="month"   name="fecha_inicial" id="inicial_fecha_filtro">


                </div>


            </div>

        </div>
        <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active" id="generar_reporte" ><span class="glyphicon glyphicon-ok"></span> Generar</button>

            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>


    </div>


</div>
</div>
</div>
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
                            <tr><td class="cargo_perso"></td></tr>
                            <tr><td class="depar_perso"></td></tr>
                            <tr><td class="ubica_perso"></td></tr>
                            <tr><td class="celular"></td></tr>
                            <tr><td class="correo_perso"></td></tr>
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

<div class="modal fade" id="modal_listar_archivos_adjuntos" role="dialog" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Archivos Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive"> 
            <table class="table table-bordered table-hover table-condensed " id="tabla_adjuntos_comunicaciones"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="3" class="nombre_tabla">TABLA DE ADJUNTOS</td>
                </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Fecha Adjunto</td>
                    <td>Nombre usuario</td>
                    <td>Accion</td>
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
<?php
$lmit_dias = $this->genericas_model->obtener_valores_parametro_aux("LimAdm", 20);
if (empty($lmit_dias)) {
    $lmit_dias = 3;
} else {
    $lmit_dias = $lmit_dias[0]["valor"];
}
?>
<script>
    $(document).ready(function () {
        inactivityTime();
        Listar_solciitudes();
        Cargar_parametro_buscado_aux(21, ".cbx_tipo_solicitud", "Seleccione Tipo Solicitud");
        Cargar_parametro_buscado_aux(23, ".cbx_tipo_evento", "Seleccione Tipo de Viaje");
        Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo Identificación");
        Cargar_parametro_buscado_aux(29, ".cbxpolizas", "Seleccione Tipo Poliza");
        Cargar_parametro_buscado(27, ".cbxproveedor", "Seleccione Proveedor");
        Cargar_parametro_buscado(28, ".cbxrefrigerios", "Seleccione Tipo Refrigerios");
        Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Departamento");
        Cargar_parametro_buscado_aux(26, ".cbxtiporeserva", "Seleccione Categoria");
        Cargar_parametro_buscado_aux(24, ".cbxtipopersona", "Seleccione Tipo Persona");
        Cargar_parametro_buscado(30, ".cbx_tipo_mesas", "Seleccione Tipo Mesa");
        Cargar_parametro_buscado(32, ".cbx_tipo_platos", "Seleccione Tipo Platos");
        Cargar_parametro_buscado(31, ".cbx_tipo_cucharas", "Seleccione Tipo Cucharas");
        Cargar_parametro_buscado_aux(35, ".cbx_categorias_tipo4", "Seleccione Categoria");
        Cargar_parametro_buscado(36, "#lugares", "Seleccione Lugar",'datalist');
        Cargar_parametro_buscado_aux(22, ".cbx_estados_solicitud", "Filtrar por  Estado");
        Cargar_parametro_buscado_aux(1237, ".cbx_tipo_calificacion", "Seleccione Tipo de Calificación")
        Pasar_valor_limite_dias(<?php echo $lmit_dias ?>);
    });
</script>

<script type="text/javascript">
    $(".form_datetime").datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate: new Date(),
        todayBtn: true,
    }

    );
    activarfile();
</script> 

<!--Start of Tawk.to Script
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5b86f837f31d0f771d8443ff/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
End of Tawk.to Script-->
