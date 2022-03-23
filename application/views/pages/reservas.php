<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/starability-growRotate.min.css">
    <?php
    $sw = false;

    if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
        $sw = true;
    }
    
    ?>
<div class="listado_reservas <?php if(!$sw) echo 'oculto';?> ">
<div id="menu" class="">
</div>
</div>
<div class="container col-md-12 text-center" id="inicio-user">
   
    <div class="tablausu col-md-12 text-left <?php if(!$sw) echo 'oculto';?>  listado_reservas" >

        <div class="table-responsive col-sm-12 col-md-12  tablauser tablaReservas" >
        <?php
       if ($sw) {
            echo '<p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>'; 
              }else{
            echo ' <p class="titulo_menu pointer" id="regresar_add"><span class="fa fa-reply-all naraja"></span> Regresar</p>';
        }
        ?>

       
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tablaReservas"  cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                    <tr class=""><td colspan="3" class="nombre_tabla">TABLA RESERVAS <br><span class="mensaje-filtro oculto"><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td><td class="sin-borde text-right border-left-none" colspan="5" >  <?php if ($sw)echo '<span class="btn btn-default  btnAgregar agregar_reserva" ><span class="fa fa-plus red"></span> Agregar</span>';?> <span class="btn btn-default btnModifica " id="btnmodificar_reserva"><span class="fa fa-wrench red"></span> Modificar</span> <?php if ($sw)echo '<span  class="btn btn-default  btnAgregar " id="agregar_nueva_persona"><span class="fa fa-plus red"></span> Persona</span>';?>  <span class="btn btn-default" id="filtrar_datos_reserva" title="Filtrar" data-toggle="popover" data-trigger="hover"><span class="fa fa-filter red" ></span> Filtrar</span> <span class="btn btn-default" id="limpiar_filtros_reserva"> <span class="fa fa-refresh" ></span> Limpiar</span></td></tr>
                    <tr class="filaprincipal"><td  class="opciones_tbl">Ver</td><td>Solicitante</td><td>Fecha Entrega</td><td>Fecha Salida</td><td>Lugar</td><td>Tipo Entrega</td><td>Estado</td><td  class="opciones_tbl_btn">Acción</td></tr>
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



            <div class="agregar_reserva" >
                <div class="thumbnail">
                    <div class="caption">
                        <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
                        <span class = "btn form-control">Nueva Solicitud</span>                 
                    </div>
                </div>
            </div>


            <div class=""  id = "listado">
                <div class="thumbnail ">
                    <div class="caption">                     
                        <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
                        <span class = "btn form-control">Estados Solicitudes</span>                      
                    </div>


                </div>
            </div>
           
        </div>
        <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        </div>
    </div>
</div>
</div>
<form action="#" id="Guardar_reserva" method="post">
<div class="modal fade scroll-modal" id="Modal-add-reserva" role="dialog" >
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" >
            
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title "><span class="glyphicon glyphicon-edit"></span>   Reservar Recursos</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                    <div class="row">
                   
                    <?php if ($sw) {?> 
                        <div class="funkyradio facturacion">
                            <div class="funkyradio-success">
                              <input type="radio" id="btn_reserva_pru_gen" name="rating" value="1">
                              <label for="btn_reserva_pru_gen" title="Prueba Generica"> Evaluación Sumativa</label>
                            </div>
                            <div class="funkyradio-success">
                              <input type="radio" id="btn_reserva_nor" name="rating" value="2" checked>
                              <label for="btn_reserva_nor" title="Reserva Normal"> Reserva Normal</label>
                            </div>
                        </div>
                    <?php }?> 
                        <!-- <a href="#" id='btn_mostrar_pruebas_estudiante' class='form-control text-center'> <span class='fa fa-eye red'></span>Ver día Pruebas</a> -->
                        <?php
                            if ($sw) {
                                echo '   <div class="input-group agro">
                                <input name="persona_soli" type="hidden" id="input_sele_re">
                                <span class="form-control text-left pointer sin_margin" id="persona_solicita_seleccionada">Seleccione Persona Solicita</span>
                                <span class="input-group-addon red_primari pointer btn-Efecto-men" id="sele_perso" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                                </div>';
                            }
                            ?>  
                        <div class=''>
                            <?php if ($sw) echo '<label class="ttitulo form-control text-center margin1" style="color:black;font-weight:normal"><input type="checkbox" name="" value="1" id="por_fecha_reserva">RESERVA ENTRE FECHAS.</label>'; ?>
                            <div class="input-group date form_datetime form_datetime_reserva agro"  data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                <input class="form-control CampoGeneral sin_focus requerido_nor" size="16" placeholder="Fecha Entrega" type="text" value="" required="true" name="fecha_entrega" id="fecha_entrega_agrega">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                            <?php
                            $horas_reserva = $this->genericas_model->obtener_valores_parametro_aux("LiHge", 20);
                            if (empty($horas_reserva)) {
                                $horas_reserva = 3;
                            } else {
                                $horas_reserva = $horas_reserva[0]["valor"];
                            }
                            echo '<div id="div_entre_horas"> <input type="number" class="form-control CampoGeneral requerido_nor"  placeholder="Horas de Reserva" required="" step="1" min="1" max="' . $horas_reserva . '" name="fecha_salida" id="fecha_sale_agrega"></div>';
                            if ($sw) {
                                echo ' <div class="oculto" id = "div_entre_fecha"><div class="input-group date form_datetime form_datetime_reserva agro"  data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                <input class="form-control CampoGeneral sin_focus requerido_nor" size="16" placeholder="Fecha Salida" type="text" value="">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span></div></div>
                                ';
                            }
                            ?>
                        </div>
                        <div class="input-group agro" >
                            <select  name="personal_asignado"   class="form-control recursos_agregados sin_margin" id="recursos_agregados"> <option value="">0 Recurso(s) a Reservar</option> </select> 
                            <span class="input-group-addon  btnElimina pointer " id="retirar_recurso_sele"  title="Retirar Recurso" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-remove "></span></span>
                            <span class="input-group-addon  btnAgregar pointer" id="mas_recursos" title="Mas Recurso" data-toggle="popover" data-trigger="hover" ><span class="glyphicon glyphicon-plus "></span> </span>
                        </div>
                        <div class='container_reserva_normal'>
                            <select name="tipo_estudio"   required class="form-control inputt cbx_tipo_clase CampoGeneral requerido_nor">
                                <option>Seleccione Tipo Estudio</option>
                            </select> 
                            <select name="tipo_prestamo"   required class="form-control inputt cbx_tipo_prestamo CampoGeneral requerido_nor">
                                <option>Seleccione Tipo Prestamo</option>
                            </select> 
                            <select name="tipo_entrega"   required class="form-control inputt cbx_tipo_entrega CampoGeneral requerido_nor">
                                <option>Seleccione Tipo Entrega</option>
                            </select> 
                            <input type="text" class="form-control CampoGeneral" name="asignatura" placeholder="Asignatura o Tematica">
                        </div>
                        <input id="lugar_entrega"type="text" class="form-control CampoGeneral requerido_nor" name="lugar" placeholder="Lugar de Entrega" required>
                        <textarea class="form-control"  cols="1" rows="3" name="descripcion" placeholder="Observaciones" id='observaciones_reserva' ></textarea>


                    </div>

                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

                </div>


           
        </div>

    </div>

    </form>   

</div>


<div class="modal fade" id="modal_pruebas_estudiante" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Pruebas Estudiante</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_pruebas_estudiante"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">
                            <tr class=""><td colspan="3" class="nombre_tabla">TABLA PRUEBAS</td></tr>
                            <tr class="filaprincipal"><td>Fecha</td><td class="">Hora Inicio</td><td>Hora Fin</td></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer footer-add-persona" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>




  <form action="#" id="Modificar_reserva" method="post">
<div class="modal fade scroll-modal" id="Modal-mod-reserva" role="dialog" >


    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" >
          
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-wrench"></span>  Modificar reserva</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                    <div class="row">


                        <!--<div class="form-group">

                            <div class="input-group date form_datetime form_datetime_reserva" data-date="" data-date-format="dd MM yyyy - HH:ii p" data-link-field="dtp_input1">
                                <input class="form-control CampoGeneral" size="16" placeholder="Fecha Entrega" type="text" value="" readonly name="fecha_entrega" id="fecha_entrega_modi">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>

                        </div>
                        <div class="input-group agro">
                            <input  style="margin: 0;padding: 0" type="number" class="form-control CampoGeneral"  placeholder="Horas de Reserva" required="" step="1" min="1" max="15" name="fecha_salida" id="fecha_salida_modi">
                            <span class="input-group-addon glyphicon glyphicon-refresh pointer btn-Efecto-men" id="validar_disponibilidad_modifica" title="Validar Disponibilidad" data-toggle="popover" data-trigger="hover"></span>
                        </div>-->
                        <?php
                        if ($sw) {
                            ?>


                            <div class="input-group agro">
                                <input name="persona_soli" type="hidden" id="input_sele_re_modi">
                                <span class="form-control text-left pointer" id="persona_solicita_seleccionada_modi">Persona Solicita</span>
                                <span class="input-group-addon pointer" id="sele_perso_modi"><span class="glyphicon glyphicon-search red_primari"></span></span>
                            </div>


                            <?php

                        }
                        ?>  

                        <select name="tipo_estudio"   required class="form-control inputt cbx_tipo_clase CampoGeneral" id="tipo_estudio_modi">
                            <option>Seleccione Tipo Estudio</option>
                        </select> 
                        <select name="tipo_prestamo"   required class="form-control inputt cbx_tipo_prestamo CampoGeneral" id="tipo_prestamo_modi">
                            <option>Seleccione Tipo Prestamo</option>
                        </select> 
                        <select name="tipo_entrega"   required class="form-control inputt cbx_tipo_entrega CampoGeneral" id="tipo_entrega_modi">
                            <option>Seleccione Tipo Entrega</option>
                        </select> 
                        <div class="form-group agrupado">
                            <div class="input-group">
                                <input type="text" class="form-control CampoGeneral" name="lugar" placeholder="Lugar de Entrega" required="" id="lugar_entrega_modi">

                                <span class="input-group-addon">-</span>
                                <input type="text" class="form-control CampoGeneral" name="asignatura" placeholder="Asignatura o Tematica" id="asignatura_modi">

                            </div>
                        </div>
                        <textarea class="form-control"  cols="1" rows="3" name="descripcion" placeholder="Observaciones"  id="observaciones_modi"></textarea>


                    </div>

                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

                </div>


             
        </div>

    </div>



</div>

</form>  


<div class="modal fade" id="Modal-info-dispositivo" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon  glyphicon-random"></span>   Información Dispositivo</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <table class="table table-bordered table-condensed tabla_info_inventario" id="">
                    <tr><th class="ttitulo" colspan="2"> Información General</th></tr>
                    <tr><td class="ttitulo">Recurso:</td><td class="valor_recurso"></td></tr>
                    <tr><td class="ttitulo">Serial: </td><td  class="valor_serial"></td></tr>
                    <tr><td class="ttitulo">Codigo Interno: </td><td  class="valor_cod_in"></td></tr>
                    <tr><td class="ttitulo">Marca: </td><td class="valor_marca"></td></tr>
                    <tr><td class="ttitulo">Modelo:</td><td class="valor_modelo"></td></tr>
                    <tr><td class="ttitulo">Fecha Ingreso: </td><td class="valor_ingreso"></td></tr>
                    <tr><td class="ttitulo">Fecha Garantia:</td><td class="valor_garantia"></td></tr>
                    <tr><td class="ttitulo">Valor: </td><td class="valor_valor"></td></tr>
                    <tr><td class="ttitulo">Descripción:</td><td class="valor_descripcion"></td></tr>


                </table>

                <div id="detalle_inventario" class="pointer"><span class="glyphicon glyphicon-th-list"></span><span ><a class="ttitulo"> Detalle</A></span></div>
                <br>
                <table class="table table-bordered table-condensed  tabla_info_inventario oculto" id="tabla_info_portatil">
                    <tr><th class="ttitulo" colspan="2"> Información Adicional</th></tr>
                    <tr><td class="ttitulo">Sistema Operativo:</td><td class="valor_sistemaope"></td></tr>
                    <tr><td class="ttitulo">Procesador: </td><td  class="valor_procesador"></td></tr>
                    <tr><td class="ttitulo">Disco Duro: </td><td class="valor_discoduro"></td></tr>
                    <tr><td class="ttitulo">Memoria:</td><td class="valor_memoria"></td></tr>



                </table>

                <table class="table table-bordered table-condensed  tabla_info_inventario oculto" id="tabla_info_computador">
                    <tr><th class="ttitulo" colspan="2"> Información Adicional</th></tr>
                    <tr><td class="ttitulo">Sistema Operativo:</td><td class="valor_sistemaope"></td></tr>
                    <tr><td class="ttitulo">Procesador: </td><td  class="valor_procesador"></td></tr>
                    <tr><td class="ttitulo">Disco Duro: </td><td class="valor_discoduro"></td></tr>
                    <tr><td class="ttitulo">Memoria:</td><td class="valor_memoria"></td></tr>
                    <tr><td class="ttitulo">Serial Torre:</td><td class="valor_torre"></td></tr>
                    <tr><td class="ttitulo">Serial Monitor: </td><td  class="valor_monitor"></td></tr>
                    <tr><td class="ttitulo">Serial Teclado: </td><td class="valor_teclado"></td></tr>
                    <tr><td class="ttitulo">Serial Mouse:</td><td class="valor_mouse"></td></tr>


                </table>

            </div>
            <div class="modal-footer" id="footermodal">


                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>


        </div>


    </div>
</div>

<div class="modal fade" id="Modal_responder_reserva" role="dialog">

    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-download-alt"></span> Terminar Solicitud</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <b> <p>Quien Retira ..?</p></b>
                <select name="persona"   class="form-control inputt cbx_persona_reserva " id="cbx_persona_reserva_terminar">
                    <option>Seleccione Persona</option>
                </select>


            </div>
            <div class="modal-footer" id="footermodal">

                <button type="submit" class="btn btn-danger active" id="btn_terminar_solicitud_reserva"><span class="glyphicon glyphicon-ok"></span> Aceptar</button>

                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>


        </div>


    </div>
</div>

<div class="modal fade" id="Modal_seleccionar_persona" role="dialog">

    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <p id='cargando_data_persona'>Cargando...</p>
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-users"></span> Asignar Solicitante</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div id="persona_existente">
                    <div class="form-group agrupado col-md-8 text-left">
                        <div class="input-group">
                            <input id="input_persona_reserva" class="form-control sin_margin" placeholder="Ingrese Dato a Buscar">
                            <span class="btn btn-default input-group-addon" id="buscar_sele_perso"> <span class=" glyphicon glyphicon-search"></span></span>  </div>
                        </div>
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tablapersonas_reserva"  cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr class=""><td colspan="15" class="nombre_tabla">TABLA PERSONAS</td></tr>
                                <tr class="filaprincipal"><td>Nombre Completo</td><td class="">identificación</td><td>Correo</td><td>Acción</td></tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer footer-add-persona" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

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
<form id="guardar_calificacion" method="post" action="#">
<div class="modal fade" id="Modal_calificar_reserva" role="dialog">

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content" >  
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-star"></span> Calificar Reserva</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="">
                        <div class="alert alert-warning" role="alert">
                            <p><b class="ttitulo">Nota: </b>Tener en cuenta al momento de calificar que el Método de calificación se evalúa de la siguiente manera:</p>
                            <ul>
                                <li>1 estrella: Muy malo</li>
                                <li>2 estrellas: Malo</li>
                                <li>3 estrellas: Regular</li>
                                <li>4 estrellas: Bueno</li>
                                <li>5 estrellas: Excelente</li>
                            </ul>
                        </div>
                        <div style="width: 30%; margin: 0 auto;">
							<fieldset class="starability-growRotate"> 
								<input type="radio" id="rate1" name="estrellas" value="1" />
								<label for="rate1" title="Muy Malo">1 stars</label>

								<input type="radio" id="rate2" name="estrellas" value="2" />
								<label for="rate2" title="Malo">2 stars</label>

								<input type="radio" id="rate3" name="estrellas" value="3" />
								<label for="rate3" title="Regular">3 stars</label>

								<input type="radio" id="rate4" name="estrellas" value="4" />
								<label for="rate4" title="Bueno">4 stars</label>

								<input type="radio" id="rate5" name="estrellas" value="5" checked/>
								<label for="rate5" title="Excelente">5 star</label>
							</fieldset>
						</div>
                        <textarea  class="form-control" placeholder="Observación" name="observacion"></textarea>

                    </div>


                </div>
                <div class="modal-footer" id="footermodal">


                    <button type="submit" class="btn btn-danger active" id="btn_calificar" ><span class="glyphicon glyphicon-star"></span> Aceptar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            

        </div>


    </div>

</div>
</form>

<div class="modal fade" id="Modal_filtrar_reservas" role="dialog" >

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-filter"></span> Crear Filtros</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="row">

                    <select name=""   class="form-control inputt cbx_estados_reserva" id="estados_reserva_filtro">
                        <option value="">Filtrar reservas  por estado</option>



                    </select> 

                    <select name=""   class="form-control inputt cbx_tipo_entrega" id="tipos_entrega_filtro">
                        <option value="">Filtrar resevas por Tipo Entrega</option>



                    </select> 
                    <select name=""   class="form-control inputt" id="fechas_filtro">
                        <option value="">Filtrar resevas por Fechas</option>
                        <!--<option value="1">Reservas del día</option>-->
                        <option value="4">Reservas del día</option>
                        <option value="2">Reservas del Mes</option>
                        <option value="3">Reservas del Año</option>
                        
                        <!--<option value="4">Mayor que fecha de Entrega</option>
                        <option value="5">Menor que fecha de Entrega</option>
                        <option value="6">Mayor que fecha de Salida</option>
                        <option value="7">Mayor que fecha de Salida</option>
                        <option value="8">Entre Fechas de Entrega y Salida</option>-->


                    </select> 
                    <div class="oculto" id="div_fecha_inicio_filtro">
                        <input class="form-control CampoGeneral" size="16" placeholder="Fecha Inicial" type="date"   name="fecha_inicial" id="inicial_fecha_filtro">


                    </div>
                    <div class="oculto" id="div_fecha_salida_filtro">
                        <input class="form-control CampoGeneral" size="16" placeholder="Fecha Final" type="date"   name="fecha_final" id="final_fecha_filtro">

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

<div class="modal fade scroll-modal" id="Modal-info-dispositivo-reserva" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-th-list"></span>   Información Reserva</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed tabla_info_inventario" id="">

                        <tr><th class="nombre_tabla" colspan="4"> Información General</th></tr>
                        <tr><td class="ttitulo">Quien Solicita: </td><td  colspan="3"><span class="valor_solicitante"></span> <?php if ($sw)   echo '<span id="detalle_persona_solicita" class="pointer 	fa fa-edit red" title="Detalle Persona" data-toggle="popover" data-trigger="hover"> </span>'; ?> </td></tr>
                        <?php  if ($sw)  echo '<tr><td class="ttitulo">Quien Registra: </td><td class="valor_registra" colspan="3"></td></tr>'; ?>
                        <tr class="active-re"><td class="ttitulo">Quien Entrega: </td><td class="valor_persona_entrega" colspan="3"></tr>
                        <tr class="active-re"><td class="ttitulo">Quien Recibe: </td><td class="valor_persona_recibe" colspan="3"></td></tr>
                        <tr class="is-canc-re oculto"><td class="ttitulo">Quien Cancela: </td><td class="valor_persona_cancela" colspan="3"></td></tr>
                        <tr><td class="ttitulo">Recursos:</td><td class="valor_recurso" ><span id="ver-recursos-reserva" class="pointer">VER<span class=" glyphicon glyphicon-eye-open red" id= "mover_ojo"></span></span> </td><td class="ttitulo">Asignatura: </td><td class="valor_asignatura" ></td></tr>
                        <tr><td class="ttitulo">Lugar Entrega: </td><td class="valor_lugar"></td><td class="ttitulo">Tipo Entrega: </td><td class="valor_tipo_entrega"></td></tr>
                        <tr><td class="ttitulo">Fecha Entrega: </td><td class="valor_ingreso"></td><td class="ttitulo">Fecha Salida:</td><td class="valor_salida"></td></tr>
                        <tr><td class="ttitulo">Tipo Clase: </td><td class="valor_tipo_clase"></td><td class="ttitulo">Tipo Prestamo: </td><td class="valor_tipo_prestamo"></td></tr>

                        <tr><td class="ttitulo">Estado: </td><td colspan="3" class="valor_estado"></td></tr>
                        <tr><td class="ttitulo">Observaciones: </td><td class="valor_observaciones" colspan="3"></td></tr>
                        <tr><td class="ttitulo">Calificación: </td><td class="valor_calificacion"></td><td class="ttitulo">Obs. Calificación: </td><td class="valor_calificacion_obv"></td></tr>
                        
                    
                    </table>
                  
                </div>
                
                <div class="input-group agro" >
                <textarea  name="" id="comentario" cols="20" rows="1" class="form-control sin_margin comentarios" placeholder="Ingrese comentario"></textarea>
                    <span class="input-group-addon pointer fondo-red  active" id="comentar" ><span class="fa fa-send"> </span> Enviar</span>
                    <span class="input-group-addon pointer active" id="listar_comentarios" ><span class="fa fa-list"></span> Listar</span>
                </div>


            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>


        </div>


    </div>
</div>

<div class="modal fade" id="Modal_comentarios_reserva" role="dialog">

<div class="modal-dialog modal-lg" >

    <!-- Modal content-->
    <div class="modal-content" >
        <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-comments"></span> Comentarios Reserva</h3>
        </div>
        <div class="modal-body" id="bodymodal">


            <div class="table-responsive" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed" id="tabla_comentarios"  cellspacing="0" width="100%" >
                    <thead class="ttitulo ">

                        <tr class=""><td colspan="2" class="nombre_tabla">tabla comentarios</td></tr>
                        <tr class="filaprincipal"><td>Comentario</td><td>Usuario</td><td>Fecha</td></tr>
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

<div class="modal fade" id="Modal_mis_recursos" role="dialog">

    <div class="modal-dialog modal-lg" >

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-blackboard"></span> Recursos Reserva</h3>
            </div>
            <div class="modal-body" id="bodymodal">


                <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_recursos_por_reserva"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">
                            <tr class=""><td colspan="3" class="nombre_tabla">Recursos Solicitados</td><td class= "btnAgregar sin-borde text-center red"><span class="reserva_atendida btn btn-default" id="agregar_mas_recurso"><span class=' fa fa-plus red'></span> Agregar</span></td></tr>
                            <tr class="filaprincipal"><td>Tipo</td><td class="">Asignado</td><td class="">Fecha Registra</td><td  class="opciones_tbl">Acción</td></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>


            </div>
            <div class="modal-footer" id="footermodal">
                <?php
                if ($sw) {
                    echo '<div class="input-group agro reserva_atendida" >
                    <select name="persona"   class="form-control inputt cbx_persona_reserva sin_margin" id="cbx_persona_reserva_entrega"> <option>Seleccione Persona</option></select> 
                    <span class="input-group-addon pointer fondo-red active" id="btn_gestionar_entrega_retiro" ><span class="glyphicon glyphicon-ok "> </span> Entregar</span>
                    <span class="input-group-addon pointer active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</span>
                </div>';
                    echo ' <button type="button" class="btn btn-default active" data-dismiss="modal" id="cerrar_no"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
 ';
                } else {
                    echo ' <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
 ';
                }
                ?>
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



<div class="modal fade" id="Modal_mis_recursos_tipo" role="dialog">

    <div class="modal-dialog" >

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-edit"></span>Asignar Recurso</h3>
            </div>
            <div class="modal-body" id="bodymodal">


                <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed" id="tabla_recursos_por_tipo"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">

                            <tr class=""><td colspan="2" class="nombre_tabla">tabla Tipo recursos</td></tr>
                            <tr class="filaprincipal"><td>Estado</td><td class="">Codigo</td><td>Serial</td><td>Acción</td></tr>
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
<div class="modal fade" id="Modal_seleccionar_recursos" role="dialog">

    <div class="modal-dialog <?php echo $sw? 'modal-lg':''; ?>">

        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="glyphicon glyphicon-blackboard"></span> Recursos Actuales</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <p><span class="glyphicon glyphicon-map-marker red"></span><b><span class="rec_sele"> 0</span></b> a Reservar</p>
                <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_recursos_disponibles"  cellspacing="0" width="100%" style="">
                        <thead class="ttitulo ">

                            <tr class=""><td colspan="6" class="nombre_tabla">tabla recursos</td></tr>
                            <tr class="filaprincipal"><td>Tipo</td><td>Total</td><td>Normales</td><td>Especiales</td><td>Disponible</td><td>Acción</td></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>


            <div class="modal-footer footer-add-persona" id="footermodal">
            <button type="button" class="btn btn-danger active" id="Guardar_mas_recursos" ><span class="glyphicon glyphicon-floppy-disk"></span> Terminar</button>

                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>

            </div>


        </div>


    </div>
</div>

<?PHP
//CONFIGURACION DE LOS FECHAS Y HORA DE RESERVA
$this->load->model('genericas_model');
$dias = $this->genericas_model->obtener_valores_parametro_aux("limFec", 20);
if (empty($dias)) {
    $dias = 7;
} else {
    $dias = $dias[0]["valor"];
}

$max_recursos = $this->genericas_model->obtener_valores_parametro_aux("LimPres", 20);
if (empty($max_recursos)) {
    $max_recursos = 1;
} else {
    $max_recursos = $max_recursos[0]["valor"];
}
if ($sw) {
    $max_recursos = 100;
}

$hora = $this->genericas_model->obtener_valores_parametro_aux("LimRes", 20);
if (empty($hora)) {
    $hora = 10;
} else {
    $hora = $hora[0]["valor"];
}


$horas_dispo = $this->genericas_model->obtener_valores_parametro_aux("IniFin", 20);

if (empty($horas_dispo)) {
    $horas_dispo = array("06:30", "21:30");
} else {
    $horas_dispo = explode(",", $horas_dispo[0]["valor"]);
}

$hora_inicio = date_format(date_create($horas_dispo[0]), 'H');
$hora_fin = date_format(date_create($horas_dispo[1]), 'H');
$horas_no = "";

for ($i = 0; $i < 24; $i++) {
    $create = $i . ':00';
    $hora_sele = date_format(date_create($create), 'H');
    if (!($hora_sele >= $hora_inicio && $hora_sele <= $hora_fin)) {
        $horas_no = $horas_no . $i . ",";
    }

}
$horas_no = "[" . $horas_no . "]";

?>
<script type="text/javascript">
 

    var fecha = new Date();
    fecha.setDate(fecha.getDate() + <?php echo $dias ?>);
    $(".form_datetime_reserva").datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        minuteStep: <?php echo $hora ?>,
        hoursDisabled: <?php echo "$horas_no" ?>,
        startDate: new Date(),
        max: new Date(),

        <?php
        // Restricción de fecha para los que no son admins
            if (!$sw) {
                echo 'endDate:fecha,';
            }
        ?>});

</script>  
<script type="text/javascript">
    var fecha = new Date();
    fecha.setDate(fecha.getDate() + <?php echo $dias ?>);
    $(".form_datetime").datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate: new Date(),
        <?php
        // Restricción de fecha para los que no son admins
            if (!$sw) {
                echo "endDate: fecha,";
            }
        ?>
    }

    );

</script> 
<script>
    $(document).ready(function () {
        inactivityTime();
        obtener_permisos('<?php echo $sw ?>');
        Pasar_valor_max_recursos(<?php echo $max_recursos ?>);
        Listar_reservas_audivisuales(<?php echo $id ?>);
        Cargar_parametro_buscado(13, ".cbx_tipo_entrega", "Seleccione Tipo Entrega");
        Cargar_parametro_buscado_aux(12, "#estados_reserva_filtro", "Seleccione Estado");
        Cargar_parametro_buscado(14, ".cbx_tipo_prestamo", "Seleccione Tipo Prestamo")
        Cargar_parametro_buscado(15, ".cbx_tipo_clase", "Seleccione Tipo Estudio")
        Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo identificación")
        Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Departamento")
        Cargar_parametro_buscado_aux(24, ".cbxtipopersona", "Seleccione Tipo Persona");

        <?php if($sw){ ?>
            Cargar_personas_audiovisuales();
            traer_data_prueba();
        <?php } ?>

    });
</script>
<!--Start of Tawk.to Script
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5b636354df040c3e9e0c3c97/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
End of Tawk.to Script-->
