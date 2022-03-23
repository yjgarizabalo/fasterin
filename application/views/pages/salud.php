<?php $sw  = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_salud" ?  true : false; ?>
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
<div class="container col-md-12 " id="inicio-user">
    <!-- Tabla de atenciones -->
    <div class="tablausu col-md-12 text-left oculto" id="listar_atenciones">
        <div class="table-responsive col-sm-12 col-md-12">
            <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_atenciones" cellspacing="0" width="100%" style="">
                <thead class="ttitulo ">
                    <tr>
                        <td colspan="3" style="" class="nombre_tabla">TABLA ATENCIONES <br>
                        <span class="mensaje-filtro oculto" id='mensaje-filtro-evento'>
                        <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
                        <td class="sin-borde text-right border-left-none" colspan="7"> 
                            <?php if ($sw){?>
                                <span class="btn btn-default btnAgregar" id="admin_atenciones"><span class="fa fa-cogs red"></span> Administrar</span>
                            <?php }?>    
                            <a href="<?php echo base_url()?>index.php/salud/exportar_solicitudes" type="button" class="btn btn-default" id="exportar_solicitudes"><span class="fa fa-cloud-download red"></span> Exportar</a>                             
                            <span class="btn btn-default filtrar_pacientes" data-toggle="modal">
                            <span class="fa fa-filter red"></span> Pacientes</span>
                            <span class="btn btn-default filtrar_atenciones" data-toggle="modal">
                            <span class="fa fa-filter red"></span> Solicitudes</span>
                            <span class="btn btn-default" id="limpiar_filtros_ate"> 
                            <span class="fa fa-refresh red"></span> Limpiar</span>
                        </td>
                    </tr>
                    <tr class="filaprincipal">
                            <td>Ver</td>
                            <td>Paciente</td>
                            <td>Tipo Solicitud</td>
                            <td>Profesional</td>
                            <td>Fecha Solicitud</td>
                            <td>Estado</td>
                            <td style="width:150px">Acción</td>
                        </tr>
                </thead>
                <tbody >
                </tbody>
            </table>
        </div>
    </div>

    <!-- Menu principal  -->
    <div class="tablausu col-md-12" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
            <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
            <div id="container-principal2" class="container-principal-alt">
                <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
                <div class="row">
                    <?php foreach ($tipo_solicitud as $per){ ?>
                        <div class="pointer" id="<?php echo $per["id_aux"] ?>">
                            <div class="thumbnail">
                            <div class="caption" style="text-align:center;">
                                <img src="<?php echo base_url() ?>/imagenes/<?php echo $per["valory"]  ?>" alt="...">
                                <span class = "btn form-control"><?php echo $per["valor"] ?></span>                 
                            </div>
                            </div>
                        </div>    
                    <?php } ?>
                    <div id="listado_atenciones">
                        <div class="thumbnail">
                            <div class="caption">
                                <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
                                <span class="btn  form-control btn-Efecto-men">Planilla de atenciones</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span>
                    Regresar</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_filtrar_pacientes" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-filter"></span> Filtrar Pacientes</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div class="col-md-12" style="padding: 0px;">
                            <select id="antecendete_filtro" class="form-control inputt cbxantecedente">
                                <option value="">Filtrar por Antecedentes</option>
                            </select>
                        </div>
                        <div class="col-md-12" style="padding: 0px;">    
                            <select id="servicio_habito" class="form-control inputt cbxhabitos">
                                <option value="">Filtrar por Hábitos</option>
                            </select>
                        </div>    
                        <div class="agrupado">
                            <div class="col-md-12" style="padding: 0px;">    
                                <div class="input-group agro" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Diagnostico" type="text" name="diagnostico" id="diagnostico" title="Diagnostico" data-toggle="popover" data-trigger="hover" disabled>
                                    <span class="input-group-addon  btnElimina pointer retirar_diagnostico red" title="Retirar Diagnostico" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove "></span></span>                   
                                    <span class="input-group-addon  btnAgregar pointer agregar_diagnostico red" title="Mas Diagnostico" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus "></span> </span>
                                </div>
                            </div>    
                        </div>     
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha Inicio" type="text" value="" name="fecha_inicio" id="fecha_inicio" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>                                  
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha Fin" type="text" value="" name="fecha_fin" id="fecha_fin" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>                                  
                            </div>
                        </div>        
                    </div>
                    <br/>
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_filtro_pac" cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                            <tr class="">
                                <td class="nombre_tabla" colspan="2">TABLA PACIENTES</td>
                                <td class="sin-borde text-right border-left-none" colspan="3">
                                    <span class="btn btn-danger active" id="btn_filtro"> <span class="fa fa-check"></span> Generar</span>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Nombre</td>
                                <td>Dependencia</td>
                                <td>Tipo Examen</td>
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

    <!-- Modal detalle solicitud -->
    <div class="modal fade con-scroll-modal" id="modal_detalle_solicitud" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <input type="hidden" name="soli_id" id="soli_id">
                    <div class="table-responsive">
                        <div class="oculto" id="container_nav_salud_detalle">
                            <nav class="navbar navbar-default" id="nav_salud_detalle" style="display: flex;">
                                <div class="container-fluid">
                                    <ul class="nav navbar-nav">
                                        <li class="pointer btn_revsistemas"><a><span class="fa fa-check red"></span> Revisión por Sistemas</a></li>
                                        <li class="pointer btn_examen_fisico"><a><span class="fa fa fa-stethoscope red"></span> Examen Físico</a></li>
                                        <li class="pointer btn_resultados"><a><span class="fa fa-file-text-o red"></span> Paraclínicos</a></li>
                                        <li class="pointer btn_diagnostico"><a><span class="fa fa-pencil-square-o red"></span> Diagnósticos</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>   
                        <br>
                        <table class="table table-bordered table-condensed" id="tabla_detalle_factura">
                            <tr class="">
                                <th class="nombre_tabla" colspan="6"> Información Solicitante</th>
                                <th colspan="1" class="sin-borde text-right border-left-none"> <span id="ver_tp_reporte" class="btn btn-default" title="Agregar nueva observación" data-toggle='popover' data-trigger='hover'> 
                                    <span class="fa fa-list-alt red" ></span> Tipo de reporte</span>
                                </th>
                                <th colspan="1" class="sin-borde text-right border-left-none"> <span id="ver_observacion" class="btn btn-default" title="Agregar nueva observación" data-toggle='popover' data-trigger='hover'> 
                                    <span class="fa fa-eye red" ></span> Observaciones</span>
                                </th>

                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="4" style="width: 20%;">Fecha solicitud </td>
                                <td class="fecha_registra" colspan="4" ></td>   
                            </tr>
                            <tr>        
                                <td class="ttitulo" colspan="2" style="width: 20%;">Solicitante </td>
                                <td class="solicitante" colspan="2" ></td> 
                                <td class="ttitulo" colspan="2" style="width: 20%;">Identificación </td>
                                <td class="identificacion" colspan="2" ></td> 
                            </tr>
                            <tr>  
                                <td class="ttitulo" colspan="2" style="width: 20%;">Género </td>
                                <td class="genero" colspan="2"></td>   
                                <td class="ttitulo" colspan="2" style="width: 20%;">Edad </td>
                                <td class="edad" colspan="2"></td>   
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2">Población</td>
                                <td class="tipopersona" colspan="2"></td>   
                                <td class="ttitulo" colspan="2">Dependencia/Programa</td>
                                <td class="programa" colspan="2"></td>                            
                            </tr>
                            <tr class="">
                                <th class="nombre_tabla" colspan="8"> Información Salud</th>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2" >Servicio</td>
                                <td class="servicio" colspan="2"></td>
                                <td class="ttitulo" colspan="2" >Profesional</td>
                                <td class="profesional" colspan="2"></td>
                            </tr>
                            <tr class="oculto"  id="detalle_hm">
                                <td class="ttitulo" colspan="2" >Motivo de Consulta</td>
                                <td class="motivo_consulta" colspan="2"></td>
                                <td class="ttitulo" colspan="2" >Enfermedad Actual</td>
                                <td class="enfermedad_actual" colspan="2"></td>
                            </tr>
                            <tr id="tr_control">
                                <td class="ttitulo" colspan="2">Control/Plan</td>
                                <td class="control" colspan="6"></td>
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

    
    <div class="modal fade con-scroll-modal" id="modal_detalle_historia_clin" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"> </span><span class="nombre_modal"></span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">  
                        <div id="container_detalle_revsistema" class="oculto">                  
                            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_detalle_revsistema" cellspacing="0" width="100%" style="">
                                <thead class="ttitulo ">
                                    <tr>
                                        <td colspan="2" class="nombre_tabla">TABLA SISTEMAS - ÓRGANOS</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Sistema/Órgano</td>
                                        <td>Observación</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div> 
                        <div id="container_detalle_examenes" class="oculto">
                            <table class="table table-bordered table-condensed" id="tabla_signos_vitales">
                            <thead class="ttitulo ">
                                <tr class="">
                                    <td colspan="8" class="nombre_tabla">TABLA SIGNOS VITALES</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>  
                                    <td class="ttitulo" colspan="2">Peso (kg)</td>
                                    <td class="peso" colspan="2"></td>   
                                    <td class="ttitulo" colspan="2">Talla (cm)</td>
                                    <td class="talla" colspan="2"></td>   
                                </tr>
                                <tr>
                                    <td class="ttitulo" colspan="2">Índice de Masa Corporal IMC</td>
                                    <td class="imc" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Rango IMC</td>
                                    <td class="rango_imc" colspan="2"></td>
                                </tr>
                                <tr>
                                   <td class="ttitulo" colspan="2" >Frecuencia Cardiaca (min)</td>
                                    <td class="frecuencia_c" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Tensión Arterial</td>
                                    <td class="tension_a" colspan="2"></td>                           
                                </tr>
                                <tr>                    
                                    <td class="ttitulo" colspan="2" >Frecuencia Respiratoria (min)</td>
                                    <td class="frecuencia_r" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Mano Dominante</td>
                                    <td class="mano_dominate" colspan="2"></td>
                                </tr>
                                <tr>
                                   <td class="ttitulo" colspan="2">Temperatura</td>
                                    <td class="temperatura" colspan="2"></td>  
                                    <td class="ttitulo" colspan="2">Detalle</td>
                                    <td class="detalle_examenf" colspan="2"></td>
                                </tr>
                            </tbody>

                            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_detalle_examenes" cellspacing="0" width="100%" style="">
                                <thead class="ttitulo ">
                                    <tr>
                                        <td colspan="2" class="nombre_tabla">TABLA EXÁMENES</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Tipo Examen</td>
                                        <td>Estado</td>
                                        <td>Observación</td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div> 
                        <div id="container_detalle_diagnostico" class="oculto">     
                            <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_detalle_diagnostico" cellspacing="0" width="100%" style="">
                                <thead class="ttitulo ">
                                    <tr>
                                        <td colspan="2" class="nombre_tabla">TABLA DIAGNÓSTICOS</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Código</td>
                                        <td>Diagnóstico</td>
                                        <td></td>
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

    <!-- Modal de administración -->
    <div class="modal fade con-scroll-modal" id="modal_administracion" role="dialog">
        <div class="modal-dialog" style="width: 690px;">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Administracion</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <nav class="navbar navbar-default" style="display: flex;">
                            <div class="container-fluid">
                                <ul class="nav navbar-nav">
                                <li class="pointer active" id="crear_servicio"><a><span class="fa fa-list red"></span> Servicios</a></li>
                                <li class="pointer" id="crear_permisos"><a><span class="fa fa-list red"></span> Permisos</a></li>
                                </ul>
                            </div>
                        </nav>   
                        <div id="container_turnos_bib">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_servicios" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                <th class="nombre_tabla">TABLA DE SERVICIOS</th>

                                    <td class="sin-borde text-right border-left-none" colspan="6">
                                        <button class="btn btn-default agregar_servicio"> <span class="fa fa-plus red"></span> Nueva Servicio</button>
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
                        <div id="container_permisos" class="oculto">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_permisos" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                <th class="nombre_tabla" colspan="2">TABLA DE PERMISOS</th>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Tipo de Solicitud</td>
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

    <form  id="form_asignar_permiso"  method="post">
        <div class="modal fade con-scroll-modal" id="modal_asignar_permiso" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X </button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Asignar Permiso</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="table-responsive">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_permisos_parametros" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="3" style="" class="nombre_tabla">TABLA PERMISOS</td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>No.</td>
                                    <td>Nombre Perfil</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody >
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
    </form>

    <!-- Modal asignacion de profesional a servicio -->
    <form  id="form_profesional_servicio"  method="post">
        <div class="modal fade con-scroll-modal" id="modal_profesional_servicio" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X </button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Asignar Funcionario</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="table-responsive">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_profesional_servicio" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="2" style="" class="nombre_tabla">TABLA FUNCIONARIOS <br>
                                    <td class="sin-borde text-right border-left-none" colspan="4"> 
                                        <span class="btn btn-default" id="asignar_profesional_servicio"> <span class="fa fa-plus red"></span> Asignar Funcionario</span>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>No.</td>
                                    <td>Nombre Completo</td>
                                    <td>Identificacion</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody >
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
    </form>

    <!-- buscar_personas atenciones -->
    <form  id="form_buscar_persona_ate"  method="post">
        <div class="modal fade" id="modal_buscar_persona_ate" role="dialog">
            <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Persona</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                <div class="row" id="" style="width: 100%">
                    <div class="agrupado col-md-12 text-left"> 
                        <div class="col-md-4" style="padding:0px;">
                            <div class="agro">
                                <select name="tipopersona" id="tipopersona" class="form-control cbxtipopersona" title="Tipo"  onchange="TPUsu()"><option value="">Seleccione Población</option></select>
                            </div>
                        </div>      
                        <div class="col-md-8" style="padding:0px;">
                            <div class="input-group agro">
                                <input id='txt_per_buscar_ate' class="form-control con_focus" placeholder="Ingrese identificación o nombre de la persona">
                                <span class="input-group-btn"><button class="btn btn-default test" id="botonsub" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
                            </div>                                  
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="table-responsive col-md-12" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda_ate" cellspacing="0" width="100%">
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

                </div>
                </div>
                <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
            </div>
        </div>
    </form>

    <!-- buscar_personas -->
    <form  id="form_buscar_persona"  method="post">
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
                        <input id='txt_per_buscar' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
                        <span class="input-group-btn"><button class="btn btn-default test" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
                    </div>
                    </div>
                    <div class="table-responsive col-md-12" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda" cellspacing="0" width="100%">
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

                </div>
                </div>
                <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="modal_nueva_atencion" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Atención</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div id="seleccion_tipo">
                        <div class="row" style="vertical-align:middle;text-align:center;">
                            <div class="pointer btn_paciente">
                                <div class="thumbnail">
                                <div class="caption" style="text-align:center;">
                                    <img src="<?php echo base_url() ?>/imagenes/salud_paciente.png" alt="...">
                                    <span class = "btn form-control">Paciente</span>                 
                                </div>
                                </div>
                            </div>   
                            <div class="pointer" id="btn_atencion">
                                <div class="thumbnail">
                                <div class="caption" style="text-align:center;">
                                    <img src="<?php echo base_url() ?>/imagenes/salud.png" alt="...">
                                    <span class = "btn form-control">Nueva Atención</span>                 
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

    <div class="modal fade" id="modal_historia_medicina_general" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Historia Clínica Medicina General</h3>
            </div>
            <div class="modal-body" id="bodymodal">
            <div id="seleccion_tipo">
                <div class="row" style="vertical-align:middle;text-align:center;" id="container_menu">
                    <div class="pointer btn_paciente">
                        <div class="thumbnail">
                        <div class="caption" style="text-align:center;">
                            <img src="<?php echo base_url() ?>/imagenes/salud_paciente.png" alt="...">
                            <span class = "btn form-control">Paciente</span>                 
                        </div>
                        </div>
                    </div>           
                    <div class="pointer" onclick="confirmar_solicitud('Historia Clínica Medicina General','','Sal_His_Med_Gen')">
                        <div class="thumbnail">
                        <div class="caption" style="text-align:center;">
                            <img src="<?php echo base_url() ?>/imagenes/salud_examen_medico.png" alt="...">
                            <span class = "btn form-control">Nueva Historia</span>                 
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pointer alert alert-warning btn_ultima_sol oculto" role="alert">
                <span class="detalle_bloqueo"></span><b><span>Tiene una solicitud habilitada, para continuar la gestión haga clic aquí.</span></b>
            </div>
            </div>
            <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
        </div>
    </div>

    <div class="modal fade scroll-modal" id="modal_menu_historia_general" role="dialog">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> <span > Historia Clínica Medicina General</span></h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                            <div id="seleccion_tipo">
                                <div class="row" style="width:100%">
                                    <div class="pointer btn_antecedentes">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_antecedentes.png" alt="...">
                                            <span class = "btn form-control">Antecendentes</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_dato_familiar">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_paciente.png" alt="...">
                                            <span class = "btn form-control">Acompañante</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_anamnesis">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_reporte.png" alt="...">
                                            <span class = "btn form-control">Anamnesis</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_revsistemas_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_examen_medico.png" alt="...">
                                            <span class = "btn form-control">Revisión Sistemas</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_examen_fisico_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_examen_fisico.png" alt="...">
                                            <span class = "btn form-control">Examen Físico</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_diagnostico_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_bitacora.png" alt="...">
                                            <span class = "btn form-control">Diagnóstico</span>                 
                                        </div>
                                        </div>
                                    </div> 
                                    <div class="pointer" id="btn_plan_hm">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud.png" alt="...">
                                            <span class = "btn form-control">Plan Terapéutico</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_historial_hm">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_historial.png" alt="...">
                                            <span class = "btn form-control">Historial Atenciones</span>                 
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
    
    <div class="modal fade" id="modal_historia_ocupacional" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Historia Médica Ocupacional</h3>
            </div>
            <div class="modal-body" id="bodymodal">
            <div id="seleccion_tipo">
                <div class="row" style="width: 100%" id="container_menu">
                    <div class="pointer btn_paciente">
                        <div class="thumbnail">
                        <div class="caption" style="text-align:center;">
                            <img src="<?php echo base_url() ?>/imagenes/salud_paciente.png" alt="...">
                            <span class = "btn form-control">Paciente</span>                 
                        </div>
                        </div>
                    </div>
                <?php foreach ($tipo_examen as $acti){ ?>
                    <div class="pointer" onclick="confirmar_solicitud('<?php echo 'Examen '.$acti['valorx']  ?>','<?php echo $acti['id']  ?>','Sal_His_Ocup')">
                        <div class="thumbnail">
                        <div class="caption" style="text-align:center;">
                            <img src="<?php echo base_url() ?>/imagenes/<?php echo $acti["valory"]  ?>" alt="...">
                            <span class = "btn form-control"><?php echo $acti["valor"]  ?></span>                 
                        </div>
                        </div>
                    </div>    
                <?php } ?>
                </div>
            </div>
            <div class="pointer alert alert-warning btn_ultima_sol oculto" role="alert">
                <span class="detalle_bloqueo"></span><b><span>Tiene una solicitud habilitada, para continuar la gestión haga clic aquí.</span></b>
            </div>   
            </div>
            <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
        </div>
    </div>

    <div class="modal fade scroll-modal" id="modal_menu_historia_ocupacional" role="dialog">
        <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> <span > Historia Médica Ocupacional</span></h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                            <div id="seleccion_tipo">
                                <div class="row" style="width:100%">
                                    <div class="pointer" id="btn_escolaridad">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_escolaridad.png" alt="...">
                                            <span class = "btn form-control">Escolaridad</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_hlaboral">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_historia_ocupacional.png" alt="...">
                                            <span class = "btn form-control">H-Laboral</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_antecedentes">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_antecedentes.png" alt="...">
                                            <span class = "btn form-control">Antecendentes</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_revsistemas_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_examen_medico.png" alt="...">
                                            <span class = "btn form-control">Revisión Sistemas</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_examen_fisico_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_examen_fisico.png" alt="...">
                                            <span class = "btn form-control">Examen Físico</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_resultados">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_reporte.png" alt="...">
                                            <span class = "btn form-control">Resultado Exámenes</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer btn_diagnostico_h">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_bitacora.png" alt="...">
                                            <span class = "btn form-control">Diagnóstico</span>                 
                                        </div>
                                        </div>
                                    </div> 
                                    <div class="pointer" id="btn_valoracion">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud.png" alt="...">
                                            <span class = "btn form-control">Concepto Médico</span>                 
                                        </div>
                                        </div>
                                    </div>
                                    <div class="pointer" id="btn_historial_ho">
                                        <div class="thumbnail">
                                        <div class="caption" style="text-align:center;">
                                            <img src="<?php echo base_url() ?>/imagenes/salud_historial.png" alt="...">
                                            <span class = "btn form-control">Historial Atenciones</span>                 
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

    <div class="modal fade" id="modal_bitacora" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Bitácora de Enfermería</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div id="seleccion_tipo">
                        <div class="row" style="vertical-align:middle;text-align:center;">
                            <div class="pointer btn_paciente">
                                <div class="thumbnail">
                                <div class="caption" style="text-align:center;">
                                    <img src="<?php echo base_url() ?>/imagenes/salud_paciente.png" alt="...">
                                    <span class = "btn form-control">Paciente</span>                 
                                </div>
                                </div>
                            </div>
                            <div class="pointer" id="estado_bitacora">
                                <div class="thumbnail">
                                <div class="caption" style="text-align:center;">
                                    <img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
                                    <span class = "btn form-control">Estado Bitácoras</span>                 
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

    <div class="modal fade" id="modal_atenciones_bitacora" role="dialog">
        <div class="modal-dialog modal-95">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Bitácora de Enfermería</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div id="container_atenciones_bitacora">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_atenciones_bitacora" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo">
                                <tr>
                                    <td colspan="4" style="" class="nombre_tabla">TABLA ATENCIONES</td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Tipo Solicitud</td>
                                    <td>Servicio</td>
                                    <td>Fecha Atención</td>
                                    <td>Acción</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div id="container_bitacoras_paciente" class="oculto">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_bitacoras_paciente" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo">
                                <tr>
                                    <td colspan="5" style="" class="nombre_tabla">TABLA BITACORAS</td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Ver</td>
                                    <td>Tipo Solicitud</td>
                                    <td>Servicio</td>
                                    <td>Fecha Atención</td>
                                    <td>acción</td>
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

    <div class="modal fade con-scroll-modal" id="modal_detalle_bitacora" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Bitácora</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th class="nombre_tabla" colspan="8"> Información Solicitante</th>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="4" style="width: 20%;">Fecha</td>
                                <td class="fecha_atencion" colspan='4'></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2" style="width: 20%;">Nombres y Apellidos</td>
                                <td class="nombre_apellido" colspan="2"></td>
                                <td class="ttitulo" colspan="2" style="width: 20%;">No. Identificación</td>
                                <td class="identificacion" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="2" style="width: 20%;">Dependencia</td>
                                <td class="dependencia" colspan="2"></td>
                                <td class="ttitulo" colspan="2" style="width: 20%;">Edad</td>
                                <td class="edad" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="3" style="width: 20%;">Observaciones de Ingreso</td>
                                <td class="observacion_ingreso" colspan='5'></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="3" style="width: 20%;">Motivo de Ingreso</td>
                                <td class="motivo_ingreso" colspan='5'></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="3" style="width: 20%;">Condición General del Paciente</td>
                                <td class="condicion_general" colspan='5'></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="3" style="width: 20%;">Reporte de Atención</td>
                                <td class="reporte_atencion" colspan='5'></td>
                            </tr>
                            <tr>
                                <td class="ttitulo" colspan="3" style="width: 20%;">Observaciones de Salida</td>
                                <td class="observacion_salida" colspan='5'></td>
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

    <div class="modal fade" id="modal_crear_bitacora" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_bitacora" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Bitácora</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="observacion_ingreso" id="observacion_ingreso" placeholder="Observaciones de Ingreso" title="Observaciones de Ingreso"></textarea>        
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="motivo_ingreso" id="motivo_ingreso" placeholder="Motivo de Ingreso" title="Motivo de Ingreso"></textarea>      
                            </div> 
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="condiciones_pac" id="condiciones_pac" placeholder="Condiciones generales del paciente" title="Condiciones generales del paciente"></textarea>        
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="reporte_atencion" id="reporte_atencion" placeholder="Reporte de atención" title="Reporte de atención"></textarea>        
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="observacion_salida" id="observacion_salida" placeholder="Observaciones de salida" title="Observaciones de salida"></textarea>        
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_bitacora"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_paciente" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_paciente" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-edit"></span> Datos Paciente</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                        <h4 class="ttitulo"><span>PACIENTE: </span> <span class="paciente"></span></h4>    
                        <div class="agrupado">
                                <div class="col-md-12" id="content_genero_hmo" style="padding: 0px;">
                                    <div class="funkyradio facturacion" >
                                        <div class="funkyradio-success">
                                            <input type="radio" id="genero_f_hmo" name="genero_hmo" value="1">
                                            <label for="genero_f_hmo" title="Femenino"> Género Femenino</label>
                                        </div>
                                        <div class="funkyradio-danger">
                                            <input type="radio" id="genero_m_hmo" name="genero_hmo" value="2">
                                            <label for="genero_m_hmo" title="Masculino"> Género Masculino</label>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12" id="content_fnacimiento_hmo" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha Nacimiento" type="text" value="" name="fecha_nacimiento_hmo" id="fecha_nacimiento_hmo" maxlength="99" title="Fecha Nacimiento" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>  
                            </div>
                            <div class="col-md-12" id="content_lnacimiento" style="padding: 0px;">
                                <input type="text" class="form-control" name='lugar_nacimiento' id="lugar_nacimiento" placeholder="Lugar de Nacimiento" required="true"> 
                            </div> 
                            <div class="col-md-12" id="content_direccion" style="padding: 0px;">
                                <input type="text" class="form-control" name='direccion' id="direccion" placeholder="Dirección Residencia" required="true">    
                            </div>  
                            <div class="col-md-12" id="content_profesion" style="padding: 0px;">
                                <input type="text" class="form-control" name='profesion' id="profesion" placeholder="Profesión" required="true">    
                            </div>  
                            <div class="col-md-12" id="content_estadocivil" style="padding: 0px;">
                                <select name="id_estadocivil" class="form-control cbxestadocivil" required="true" title="Estado Civil"><option value="">Seleccione Estado Civil</option></select>    
                            </div>
                            <div class="col-md-12" id="content_smilitar" style="padding: 0px;">
                                <select name="smilitar" class="form-control cbxsmilitar" required="true" title="Servicio Militar"><option value="0">Servicio Militar?</option>
                                    <option value="1">SI</option><option value="2">NO</option>
                                </select>    
                            </div>                        
                            <div class="col-md-12" id="content_arl" style="padding: 0px;">
                                <input type="text" class="form-control" name="arl" id="arl" placeholder="Afiliación ARL" required="true">
                            </div>
                            <div class="col-md-12" id="content_eps" style="padding: 0px;">
                                <input type="text" class="form-control" name="eps" id="eps" placeholder="Afiliación EPS" required="true"> 
                            </div>                          
                            <div class="col-md-12" id="content_fingreso" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha de Ingreso" type="text" value="" name="fecha_ingreso" id="fecha_ingreso" maxlength="99" title="Fecha de Ingreso" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_paciente"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal fade" id="modal_dato_familiar" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_dato_familiar" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Datos del Acompañante</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                         <div class="row">     
                            <div class="col-md-12" style="padding: 0px;">
                               <input type="text" class="form-control" name="nombre_acomp" id="nombre_acomp" placeholder="Nombre apellido del acompañante" required="true">      
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                               <input type="number" class="form-control" name="telefono_acomp" id="telefono_acomp" placeholder="Teléfono" required="true">      
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                               <input type="text" class="form-control" name="nombre_resp" id="nombre_resp" placeholder="Nombre apellido de la persona responsable del usuario" required="true">      
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                               <input type="number" class="form-control" name="telefono_resp" id="telefono_resp" placeholder="Teléfono" required="true">      
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <select name="id_parentesco_hm" id="id_parentesco_hm" class="form-control cbxparentesco" required="true" title="Parentesco"><option value="">Seleccione Parentesco</option></select>    
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_dato_familiar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="modal_anamnesis" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_anamnesis" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Datos Anamnesis</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                         <div class="row">     
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="motivo_consulta" id="motivo_consulta" placeholder="Motivo de la Consulta" title="Motivo de la Consulta"></textarea>      
                            </div> 
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="enfermedad_actual" id="enfermedad_actual" placeholder="Enfermedad Actual" title="Enfermedad Actual"></textarea>        
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_anamnesis"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_plan_terapeutico" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_plan_terapeutico" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Plan Terapéutico</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                         <div class="row">
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="plan" id="plan" placeholder="Plan Terapéutico" title="Plan Terapéutico" rows="10" cols="100"></textarea>        
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_plan"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_valoracion" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_valoracion" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Concepto Médico</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                         <div class="row">     
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="aplazamiento" id="aplazamiento" placeholder="Aplazamiento" title="Aplazamiento"></textarea>      
                            </div> 
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="recomendaciones" id="recomendaciones" placeholder="Recomendaciones" title="Recomendaciones"></textarea>        
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <textarea class="form-control" name="control_valoracion" id="control_valoracion" placeholder="Control" title="Control"></textarea>        
                            </div>
                            <div class="col-md-12" style="padding: 0px;">
                                <select name="valoracion" id="valoracion" class="form-control cbxvaloracion" required="true" title="Concepto Médico"></select> 
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_valoracion"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_historial_ocupacional" role="dialog">
        <div class="modal-dialog modal-95">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-duplicate"></span> Historial Médico Ocupacional</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div id='container_tabla_atenciones'>
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_historial_atenciones" cellspacing="0" width="100%" style="">
                        <thead class="ttitulo">
                            <tr>
                                <td colspan="10" style="" class="nombre_tabla">TABLA HISTORIAL ATENCIONES</td>
                            </tr>
                            <tr class="filaprincipal">
                                <td class="opciones_tbl">ver</td>
                                <td>Tipo Examen</td>
                                <td>Fecha Atención</td>
                                <td>Recomendaciones</td>
                                <td>Control</td>
                                <td>Valoración</td>
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


    <div class="modal fade" id="modal_historial_mgeneral" role="dialog">
        <div class="modal-dialog modal-95">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-duplicate"></span> Historia Clínica Medicina General</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div id='container_tabla_atenciones'>
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_historial_mgeneral" cellspacing="0" width="100%" style="">
                        <thead class="ttitulo">
                            <tr>
                                <td colspan="10" style="" class="nombre_tabla">TABLA HISTORIAL ATENCIONES</td>
                            </tr>
                            <tr class="filaprincipal">
                                <td class="opciones_tbl">ver</td>
                                <td>Fecha Atención</td>
                                <td>Motivo</td>
                                <td>Enfermedad Actual</td>
                                <td>Plan Terapéutico</td>
                                <td>Profesional</td>
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

    <div class="modal fade con-scroll-modal" id="modal_detalle_historial" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-duplicate"></span><span class="titulo_modal"> </span></h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <nav class="navbar navbar-default" style="display: flex;">
                            <div class="container-fluid">
                                <ul class="nav navbar-nav">
                                <li class="pointer rev_sistemas active"><a><span class="fa fa-check-square-o red"></span> Revisión por Sistemas</a></li>
                                <li class="pointer exa_fisico"><a><span class="fa fa-stethoscope red"></span> Examen Físico</a></li>
                                <li class="pointer exa_paraclinico"><a><span class="fa fa-pencil-square-o red"></span> Examen Paraclínico</a></li>
                                <li class="pointer diagnostico_pac"><a><span class="fa fa-file red"></span> Diagnóstico</a></li>
                                </ul>
                            </div>
                        </nav>   
                        <div id="container_rev_sistemas">
                            <div class="table-responsive" style="width:100%">
                                <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_rev_sistemas" cellspacing="0" width="100%" style="">
                                    <thead class="ttitulo ">
                                        <tr>
                                            <td colspan="3" class="nombre_tabla">TABLA SISTEMAS - ÓRGANOS</td>
                                        </tr>
                                        <tr class="filaprincipal">
                                            <td>Sistema/Órgano</td>
                                            <td>Observación</td>
                                            <td></td>
                                        </tr>
                                    </thead>
                                    <tbody >
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="container_exa_fisico" class="oculto">
                            <table class="table table-bordered table-condensed" id="tabla_signos_vit">
                            <thead class="ttitulo ">
                                <tr class="">
                                    <td colspan="8" style="" class="nombre_tabla">TABLA SIGNOS VITALES</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>  
                                    <td class="ttitulo" colspan="2">Peso (kg)</td>
                                    <td class="peso" colspan="2"></td>   
                                    <td class="ttitulo" colspan="2">Talla (cm)</td>
                                    <td class="talla" colspan="2"></td>   
                                </tr>
                                <tr>
                                    <td class="ttitulo" colspan="2">Índice de Masa Corporal IMC</td>
                                    <td class="imc" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Temperatura</td>
                                    <td class="temperatura" colspan="2"></td>
                                </tr>
                                <tr>                            
                                    <td class="ttitulo" colspan="2" >Frecuencia Cardiaca (min)</td>
                                    <td class="frecuencia_c" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Tensión Arterial</td>
                                    <td class="tension_a" colspan="2"></td>                           
                                </tr>
                                <tr>                    
                                    <td class="ttitulo" colspan="2" >Frecuencia Respiratoria (min)</td>
                                    <td class="frecuencia_r" colspan="2"></td>
                                    <td class="ttitulo" colspan="2">Mano Dominante</td>
                                    <td class="mano_dominate" colspan="2"></td>
                                </tr>
                                <tr>
                                    <td class="ttitulo" colspan="2">Detalle</td>
                                    <td class="detalle_examenf" colspan="6"></td>
                                </tr>
                            </tbody>                            
                            </table>
                            <div class="table-responsive" style="width: 100%">
                                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_exa_fisico" cellspacing="0" width="100%">
                                    <thead class="ttitulo ">
                                    <tr class="">
                                        <td class="nombre_tabla" colspan="4">TABLA DE EXAMENES</td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Tipo Examen</td>
                                        <td>Estado</td>
                                        <td>Observaciones</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>    
                        <div id="container_exa_paraclinico" class="oculto">
                            <div class="table-responsive" style="width: 100%">
                                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_exa_paraclinico" cellspacing="0" width="100%">
                                    <thead class="ttitulo ">
                                    <tr class="">
                                        <td class="nombre_tabla" colspan="4">TABLA EXÁMENES PARACLÍNICOS </td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Examen</td>
                                        <td>Estado</td>
                                        <td>Observaciones</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="container_diagnostico_pac" class="oculto">
                            <div class="table-responsive" style="width:100%">
                                <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_diagnostico_pac" cellspacing="0" width="100%" style="">
                                    <thead class="ttitulo ">
                                        <tr>
                                            <td colspan="4" class="nombre_tabla">TABLA DIAGNÓSTICOS</td>
                                        </tr>
                                        <tr class="filaprincipal">
                                            <td class="opciones_tbl_btn">Código</td>
                                            <td>Diagnósticos</td>
                                            <td></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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

    <div class="modal fade con-scroll-modal" id="modal_antecedentes" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-search-plus"></span> Antecedentes</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <nav class="navbar navbar-default" style="display: flex;">
                            <div class="container-fluid">
                                <ul class="nav navbar-nav">
                                <li class="pointer active" id="ant_familiar"><a><span class="fa fa-users red"></span> Familiares</a></li>
                                <li class="pointer" id="ant_personal"><a><span class="fa fa-user red"></span> personales</a></li>
                                <li class="pointer" id="vacuna"><a><span class="fa fa-user red"></span> Vacunas</a></li>
                                <li class="pointer" id="ant_gineco"><a><span class="fa fa-user red"></span> Gineco-Obstétricos</a></li>
                                <li class="pointer" id="ant_habitos"><a><span class="fa fa-user red"></span> Hábitos</a></li>
                                </ul>
                            </div>
                        </nav>   
                        <div id="container_ant_familiar">
                                <table class="table table-bordered table-hover table-condensed" id="tabla_ant_familiar" cellspacing="0" width="100%">
                                    <thead class="ttitulo">
                                    <tr>
                                    <th class="nombre_tabla">TABLA DE ANTECEDENTES FAMILIARES</th>
                                        <td class="sin-borde text-right border-left-none" colspan="6">
                                            <button class="btn btn-default" id="agregar_antecedente_f"> <span class="fa fa-plus red"></span> Agregar</button>
                                        </td> 
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Enfermedad</td>
                                        <td>Parentesco</td>
                                        <td>Observación</td>
                                        <td class="opciones_tbl_btn">Acción</td>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                        </div>    

                        <div id="container_ant_personal" class="oculto"> 
                                <table class="table table-bordered table-hover table-condensed" id="tabla_ant_personal" cellspacing="0" width="100%">
                                    <thead class="ttitulo">
                                    <tr>
                                    <th class="nombre_tabla" colspan="3">TABLA DE ANTECEDENTES PERSONALES</th>
                                        <td class="sin-borde text-right border-left-none">
                                            <button class="btn btn-default" id="agregar_antecedente_p"> <span class="fa fa-plus red"></span> Agregar</button>
                                        </td> 
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Antecedente</td>
                                        <td>Observación</td>
                                        <td></td>
                                        <td class="opciones_tbl_btn">Acción</td>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                        </div>    

                        <div id="container_vacuna" class="oculto">
                                <table class="table table-bordered table-hover table-condensed" id="tabla_vacunas" cellspacing="0" width="100%">
                                    <thead class="ttitulo">
                                    <tr>
                                      <th class="nombre_tabla" colspan="3">TABLA DE VACUNAS</th>
                                      <td class="sin-borde text-right border-left-none">
                                            <button class="btn btn-default" id="agregar_vacuna"> <span class="fa fa-plus red"></span> Agregar</button>
                                        </td>
                                    </tr>
                                    <tr class="filaprincipal">
                                        <td>Vacuna</td>
                                        <td>Observación</td>
                                        <td></td>
                                        <td class="opciones_tbl_btn">Acción</td>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                        </div>    

                        <div id="container_ant_gineco" class="oculto">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_ant_gineco" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                <th class="nombre_tabla" colspan="3">TABLA DE ANTECEDENTES GINECO-OBSTÉTRICOS</th>
                                    <td class="sin-borde text-right border-left-none">
                                        <button class="btn btn-default" id="agregar_ant_gineco"> <span class="fa fa-plus red"></span> Agregar</button>
                                    </td> 
                                </tr>
                                <tr class="filaprincipal">
                                    <td>FUP</td>
                                    <!-- <td>FUP</td> -->
                                    <td>Planificación</td>
                                    <td>Fecha Citología</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="container_habitos" class="oculto">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_habitos" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                    <th class="nombre_tabla" colspan="3">TABLA DE HÁBITOS</th>
                                    <td class="sin-borde text-right border-left-none">
                                        <button class="btn btn-default" id="agregar_habito"> <span class="fa fa-plus red"></span> Agregar</button>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Hábito</td>
                                    <td>Frecuencia</td>
                                    <td></td>
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

    <div class="modal fade" id="modal_add_antfamiliar" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_antfamiliar" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Antecedentes Familiares</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_tipo_enfermedad" id="id_tipo_enfermedad" required class="form-control cbxenfermedad_fam" title="Tipo de Enfermedad">
                            <option value="">Seleccione Tipo de Enfermedad</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_parentesco" id="id_parentesco" required class="form-control cbxparentesco" title="Parentesco">
                            <option value="">Seleccione Parentesco</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='observacion_antf' id='observacion_antf' placeholder="Observaciones" title="Observaciones"></textarea>    
                        </div>    
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_antfamiliar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <div class="modal fade" id="modal_add_antpersonal" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_antpersonal" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Antecedentes Personales</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_tipo_antecedente" id="id_tipo_antecedente" required class="form-control cbxantecedente" title="Tipo Antecedente">
                            <option value="">Seleccione Tipo Antecedente</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='observacion_antp' id='observacion_antp' placeholder="Observaciones" title="Observaciones"></textarea>    
                        </div>    
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_antpersonal"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <div class="modal fade" id="modal_add_vacuna" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_vacuna" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Vacunas</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_vacuna" id="id_vacuna" required class="form-control cbxvacuna" title="Vacuna">
                            <option value="">Seleccione Vacuna</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='observacion_vacuna' id='observacion_vacuna' placeholder="Observaciones" title="Observaciones"></textarea>    
                        </div>    
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_vacuna"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 

     <div class="modal fade" id="modal_ant_gineco" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_ant_gineco" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Gineco-Obstétricos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                     <div class="row"> 
                        <div class="agrupado">        
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" name='menarquia' id='menarquia' required="true" placeholder="Menarquía Años">    
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <select name="ciclos" id="ciclos" required class="form-control"  title="Ciclo Menstrual"><option value="">Ciclos Menstruales</option>
                                <option value="1">REGULAR</option><option value="2">IRREGULAR</option><option value="3">MENOPAUSICA</option>
                            </select> 
                        </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Fecha de última menstruación" type="text" value="" name="fur" id="fur" maxlength="99" title="Fecha de última menstruación" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>   
                        </div>
                        <div class="clearfix"></div>
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" name='cantidad_g' id='cantidad_g' required="true" placeholder="Cantidad de Gestaciones">    
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" name='cantidad_p' id='cantidad_p' required="true" placeholder="Cantidad de Partos">    
                            </div>
                        </div>    
                        <div class="clearfix"></div>
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" name='cantidad_c' id='cantidad_c' required="true" placeholder="Cantidad de Cesárea">    
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" name='cantidad_a' id='cantidad_a' required="true" placeholder="Cantidad de Abortos">    
                            </div>
                        </div>    
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="number" class="form-control" name='cantidad_v' id='cantidad_v' required="true" placeholder="Cantidad Vivo">    
                        </div>
                        <div class="clearfix"></div>
                        <!-- <div class="col-md-12" style="padding: 0px;"> -->
                        <!-- <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Fecha de último Período" type="text" value="" name="fup" id="fup" maxlength="99" title="Fecha de último Período" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>                                  
                        </div>
                        <div class="clearfix"></div> -->
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <select name="planifica" id="planifica" required class="form-control" title="Planificación"><option value="">Planifica?</option>
                                <option value="1">SI</option><option value="2">NO</option>
                                </select> 
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <select name="tipo_planificacion" id="tipo_planificacion" required class="form-control cbxplanificacion" title="Tipo Planificación"><option value="">---</option>
                                </select> 
                            </div>
                        </div>    
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="dismenorreas" id="dismenorreas" required class="form-control" title="Dismenorreas"><option value="">Dismenorreas?</option>
                            <option value="1">SI</option><option value="2">NO</option>
                            </select> 
                        </div>
                        <div class="clearfix"></div> 
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Fecha de última Citología" type="text" value="" name="fecha_citologia" id="fecha_citologia" maxlength="99" title="Fecha de última Citología" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>     
                        </div>
                        <div class="clearfix"></div> 
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="tipo_citologia" id="tipo_citologia" class="form-control" title="Citología Normal"><option value="0">Citología Normal?</option>
                            <option value="1">SI</option><option value="2">NO</option>
                            </select> 
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='observacion_gineco' id='observacion_gineco' placeholder="Observación" title="Observación">    
                        </div>   
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_ant_gineco"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_add_habito" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_habito" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Hábitos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                         <div class="row">     
                            <div class="col-md-12" style="padding: 0px;">
                                <select name="habito" id="habito" required="true" class="form-control cbxhabitos" onchange="tipo_habito()" title="Hábitos">
                                <option value="">Seleccione el Hábito</option>
                                </select>  
                            </div>
                            <div class="clearfix"></div>
                            <div class="agrupado"> 
                                <div class="col-md-6" style="padding: 0px;">
                                    <div class="input-group date form_datetime_habito agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                                        <input class="form-control" size="100" placeholder="Desde (Año)" type="text" value="" name="fecha_desde" id="fecha_desde" maxlength="99" title="Desde" data-toggle="popover" data-trigger="hover">
                                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                                    </div>                                  
                                </div>
                                <div class="col-md-6" style="padding: 0px;">
                                    <div class="input-group date form_datetime_habito agro" data-date="" data-date-format="yyyy" data-link-field="dtp_input1">
                                        <input class="form-control" size="100" placeholder="Hasta (Año)" type="text" value="" name="fecha_hasta" id="fecha_hasta" maxlength="99" title="Hasta" data-toggle="popover" data-trigger="hover">
                                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove red"></span></span>
                                        <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar red"></span></span>
                                    </div>                                  
                                </div>
                            </div>    
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="padding: 0px;">
                                <select name="id_frecuencia" id="id_frecuencia" required class="form-control cbxfrecuencia" title="Frecuencia"><option value="1">Seleccione la Frecuencia</option></select>   
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="padding: 0px;">
                                <input type="text" class="form-control oculto" name='cantidad' id='cantidad' placeholder="Cantidad Cigarrillos al Día">      
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="padding: 0px;">
                                <input type="text" class="form-control oculto" name='tipo_ejercicio' id='tipo_ejercicio' placeholder="Tipo de Ejercicio">      
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="padding: 0px;">
                                <select name="id_duracion" id="id_duracion" class="form-control cbxduracion oculto" title="Duración del Ejercicio">
                                <option value="">Seleccione Duración</option>
                                </select>  
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_habito"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>                       

    <div class="modal fade con-scroll-modal" id="modal_historial_laboral" role="dialog">
        <div class="modal-dialog modal-95">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X </button>
                    <h3 class="modal-title"><span class="fa fa-history"></span> Historial Laboral</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive">
                        <nav class="navbar navbar-default" style="display: flex;">
                            <div class="container-fluid">
                                <ul class="nav navbar-nav">
                                <li class="pointer active" id="historia_laboral"><a><span class="fa fa-list red"></span> Historia Laboral</a></li>
                                <li class="pointer" id="accidentes"><a><span class="fa fa-list red"></span> Accidentes de Trabajo</a></li>
                                </ul>
                            </div>
                        </nav>   
                        <div id="container_historia_laboral">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_historia_laboral" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                    <th class="nombre_tabla" colspan="6">TABLA DE HISTORIAL LABORAL</th>
                                    <td class="sin-borde text-right border-left-none">
                                        <button class="btn btn-default" id="agregar_historia"> <span class="fa fa-plus red"></span> Agregar</button>
                                    </td> 
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Empresa</td>
                                    <td>Cargo</td>
                                    <td>Fecha</td>
                                    <td class="opciones_tbl_btn">Riesgos</td>
                                    <td>Protección</td>
                                    <td>Tiempo</td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>        

                        <div id="container_accidentes" class="oculto">
                            <table class="table table-bordered table-hover table-condensed" id="tabla_accidentes" cellspacing="0" width="100%">
                                <thead class="ttitulo">
                                <tr>
                                    <th class="nombre_tabla" colspan="7">TABLA ACCIDENTES DE TRABAJO</th>
                                    <td class="sin-borde text-right border-left-none">
                                        <button class="btn btn-default" id="agregar_accidentes"> <span class="fa fa-plus red"></span> Agregar</button>
                                    </td> 
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Fecha</td>
                                    <td>Empresa</td>
                                    <td>Incapacidad</td>
                                    <td>Lesíon</td>
                                    <td>ARL</td>
                                    <td>Enfermedad</td>
                                    <td>Secuelas</td>
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

    <div class="modal fade" id="modal_riesgos_laborales" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Riesgos Laborales</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_riesgos"  cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr class="">
                                    <td colspan="2" class="nombre_tabla">TABLA RIESGOS</td>
                                    <td class="sin-borde text-right border-left-none">
                                        <button class="btn btn-default add_riesgos"> <span class="fa fa-plus red"></span> Agregar</button>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>N°</td>
                                    <td>Nombre</td>
                                    <td>Acción</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer" id="footerm odal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Terminar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_add_historia_laboral" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_historia_laboral" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Historia Laboral</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">       
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='empresa' id='empresa' required="true" placeholder="Empresa"> 
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='cargo' id='cargo' required="true" placeholder="Cargo">    
                        </div>
                        <div class="clearfix"></div>     
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Fecha" type="text" value="" name="fecha_hl" id="fecha_hl" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>    
                        </div>   
                        <!-- <div class="clearfix"></div>
                        <div class="input-group agro" id="container_add_riesgos">
                            <select name="riesgo" class="form-control riesgos_agregados sin_margin" id="riesgo" title="Riesgos Laborales"><option value="">0 Riesgo(s)</option></select>
                            <span class="input-group-addon  btnElimina pointer red" id="retirar_riesgo" title="Retirar Riesgo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-remove "></span></span>                   
                            <span class="input-group-addon  btnAgregar pointer mas_riesgos red" id="mas_riesgos" title="Mas Riesgo" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-plus "></span> </span>
                        </div>
                        <div class="col-md-12 oculto" style="padding: 0px;" id="container_riesgos">
                            <select name="riesgos_agregados" class="form-control" id="riesgos_agregados" title="Riesgos Laborales"><option value="">Riesgos</option></select> 
                        </div> -->
                        <div class="clearfix"></div>       
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="proteccion" id="proteccion" required class="form-control" title="Protección">
                            <option value="">Protección?</option>
                            <option value="1">SI</option>
                            <option value="2">NO</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <select name="tiempo" id="tiempo" required class="form-control" title="Tiempo Laborado"><option value="">Tiempo</option>
                                <option value="1">Años</option><option value="2">Meses</option>
                                </select> 
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <input type="number" class="form-control" required name='cantidad_tiempo' id='cantidad_tiempo' required="true" placeholder="Cantidad">    
                            </div>
                        </div>  
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_historia_laboral"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="Modal_seleccionar_riesgo" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Riesgos Laborales</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <p><span class="glyphicon glyphicon-map-marker red"></span><b><span class="rec_sele"> 0</span></b> a Asignar</p>

                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_riesgos_laborales"  cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr class=""><td colspan="6" class="nombre_tabla">Tabla Riesgos</td></tr>
                                <tr class="filaprincipal"><td>N°</td><td>Nombre</td><td>Acción</td></tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Terminar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_add_accidentes" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_accidentes" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Accidentes Laborales</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Fecha" type="text" value="" name="fecha_al" id="fecha_al" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>    
                        </div>   
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_empresa" id="id_empresa" required class="form-control cbxempresas" title="Empresa">
                            <option value="">Seleccione Empresa</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="number" class="form-control" name='incapacidad' id='incapacidad' required="true" placeholder="Días de Incapacidad">    
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='lesion' id='lesion' required="true" placeholder="Lesíon">    
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='arp' id='arp' required="true" placeholder="ARL">    
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='secuelas' id='secuelas' required="true" placeholder="Secuelas">    
                        </div>  
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" class="form-control" name='enfermedad' id='enfermedad' required="true" placeholder="Enfermedad Laboral">    
                        </div>                          
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_accidente"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_examen_fisico" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="#" id="form_examen_fisico" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-stethoscope"></span> Examen Físico</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <table class="table table-bordered table-condensed" id="tabla_detalle_examenf">
                    <thead class="ttitulo ">
                        <tr class="">
                            <td colspan="4" style="" class="nombre_tabla">TABLA SIGNOS VITALES <br>
                            <td class="sin-borde text-right border-left-none" colspan="4">
                                <span class="btn btn-default oculto" id="modificar_signos_vitales"> <span class="fa fa-plus red"></span> Modificar Examen</span>
                                <span class="btn btn-default oculto" id="agregar_signos_vitales"> <span class="fa fa-plus red"></span> Agregar Examen</span>
                            </td>
                        </tr>
                    </thead>
                    <tbody> 
                        <tr>  
                            <td class="ttitulo" colspan="2">Peso (kg)</td>
                            <td class="peso" colspan="2"></td>   
                            <td class="ttitulo" colspan="2">Talla (cm)</td>
                            <td class="talla" colspan="2"></td>   
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="2">Índice de Masa Corporal IMC</td>
                            <td class="imc" colspan="2"></td>
                            <td class="ttitulo" colspan="2">Rango</td>
                            <td class="rango_imc" colspan="2"></td>
                        </tr>
                        <tr>                         
                            <td class="ttitulo" colspan="2" >Frecuencia Cardiaca (min)</td>
                            <td class="frecuencia_c" colspan="2"></td>
                            <td class="ttitulo" colspan="2">Tensión Arterial</td>
                            <td class="tension_a" colspan="2"></td>                           
                        </tr>
                        <tr>                    
                            <td class="ttitulo" colspan="2" >Frecuencia Respiratoria (min)</td>
                            <td class="frecuencia_r" colspan="2"></td>
                            <td class="ttitulo" colspan="2">Mano Dominante</td>
                            <td class="mano_dominate" colspan="2"></td>
                        </tr>
                        <tr>
                            <td class="ttitulo" colspan="2">Temperatura</td>
                            <td class="temperatura" colspan="2"></td> 
                            <td class="ttitulo" colspan="2">Detalle</td>
                            <td class="detalle_examenf" colspan="2"></td>
                        </tr>
                    </tbody>                            
                </table>
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_examen_fisico" cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                            <tr class="">
                                <td class="nombre_tabla" colspan="4">TABLA DE EXAMENES</td>
                                <td class="sin-borde text-right border-left-none"> 
                                    <span class="btn btn-default" id="agregar_examen_fisico"> <span class="fa fa-plus red"></span> Agregar Examen</span>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Tipo Examen</td>
                                <td>Estado</td>
                                <td>Observaciones</td>
                                <td></td>
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
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_add_examenf" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_examen_fisico" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Examen Físico</h3>
                    </div>
                        <div class="modal-body" id="bodymodal">
                            <div class="row">       
                                <div class="col-md-12" style="padding: 0px;">
                                    <select name="id_tipo_examen" id="id_tipo_examen" required class="form-control cbxtipoexamenf" title="Tipo de Examen Físico">
                                    <option value="">Seleccione Tipo Examen</option>
                                    </select>  
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <select name="id_estado_examenf" id="id_estado_examenf" required class="form-control cbxparametro2" title="Estado">
                                    <option value="">Seleccione Estado</option>
                                    </select>  
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <textarea class="form-control" name='observacion_examen_fisico' id='observacion_examen_fisico' placeholder="Observaciones" title="Observaciones"></textarea>    
                                </div>                         
                            </div>
                        </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_examen_fisico"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_add_signos_vitales" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_signos_vitales" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Signos Vitales</h3>
                    </div>
                        <div class="modal-body" id="bodymodal">
                            <div class="row">
                                <div class="col-md-12" style="padding: 0px;">
                                    <input type="number" class="form-control" name='peso' id="peso" min="1" required="true" placeholder="Peso (kg)">     
                                </div>  
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <input type="number" class="form-control" name='talla' id='talla' min="1" required="true" placeholder="Talla (cm)">    
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <input type="number" class="form-control" name='temp' id='temp' min="1" required="true" placeholder="Temperatura">    
                                </div>
                                <div class="clearfix"></div>
                                <div class="agrupado">
                                    <div class="col-md-12" style="padding: 0px;height:50px;">
                                        <div class="alert alert-success" role="alert">
                                        <b>Índice de Masa Corporal IMC: <span class="indice_masa"></span></b>
                                        </div>                            
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="agrupado">
                                    <div class="col-md-12" style="padding: 0px;height:50px;">
                                        <div class="alert alert-success" role="alert">
                                        <b>Rango: <span class="rango_imc"></span></b> &nbsp; <a class="ver_tabla_imc pointer"><b>Ver Tabla IMC</b></a>
                                        </div>                          
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="agrupado">
                                    <div class="col-md-6" style="padding: 0px;">
                                         <input type="number" class="form-control" name='ta_sistolica' id="ta_sistolica" min="1" required="true" placeholder="Presión Arterial Sistólica">   
                                    </div> 
                                    <div class="col-md-6" style="padding: 0px;">
                                         <input type="number" class="form-control" name='ta_diastolica' id="ta_diastolica" min="1" required="true" placeholder="Presión Arterial Diastólica">   
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="agrupado">
                                    <div class="col-md-12" style="padding: 0px;">
                                        <input type="number" class="form-control" name='fc' id="fc" required="true" placeholder="Frecuencia Cardiaca">                               
                                    </div>
                                </div>
                                <div class="clearfix"></div> 
                                <div class="col-md-12" style="padding: 0px;">
                                    <input type="number" class="form-control" name='fr' id="fr" required="true" placeholder="Frecuencia Respiratoria (min)">    
                                </div>                                
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <select name="id_mano" id="id_mano" required class="form-control" title="Mano Dominante">
                                     <option value="">Seleccione Mano Dominante</option>
                                     <option value="1">IZQUIERDA</option>
                                     <option value="2">DERECHA</option>
                                     <option value="3">AMBIDEXTRO</option>
                                    </select> 
                                </div>
                                <div class="clearfix"></div>  
                                <div class="col-md-12" style="padding: 0px;">
                                    <textarea class="form-control" name='detalle' id="detalle_examen" placeholder="Detalle" title="Detalle"></textarea>     
                                </div>                                
                            </div>
                        </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active oculto" id="editar_signos_vitales"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-danger active" id="guardar_signos_vitales"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="modal fade" id="modal_tabla_imc" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-"></span> Clasificación IMC</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_imc" cellspacing="0" width="100%">
                        <thead class="ttitulo ">
                            <tr class="">
                                <td class="nombre_tabla" colspan="2">TABLA CLASIFICACIÓN IMC</td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>IMC</td>
                                <td>RANGO</td>
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
                    

    <div class="modal fade" id="modal_escolaridad" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="#" id="form_escolaridad" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-graduation-cap"></span> Escolaridad</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_escolaridad" cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                            <tr class="">
                                <td class="nombre_tabla" colspan="3">TABLA ESCOLARIDAD</td>
                                <td class="sin-borde text-right border-left-none"> 
                                    <span class="btn btn-default" id="agregar_escolaridad"> <span class="fa fa-plus red"></span> Agregar</span>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Escolaridad</td>
                                <td>Estado</td>
                                <td></td>
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
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_valor_parametro" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_valor_parametro" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> Escolaridad</h3>
                    </div>
                        <div class="modal-body" id="bodymodal">
                            <div class="row">       
                                <div class="col-md-12" style="padding: 0px;">
                                    <select name="valor_param1" id="valor_param1" required class="form-control cbxparametro1" title="Nivel de Escolaridad">
                                    <option value="">Seleccione</option>
                                    </select>  
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="padding: 0px;">
                                    <select name="valor_param2" id="valor_param2" required class="form-control cbxestado_escolaridad" title="Estado">
                                    <option value="">Seleccione</option>
                                    </select>  
                                </div>                        
                            </div>
                        </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_escolaridad"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_resualtado_examenes" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="#" id="form_resualtado_examenes" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-file-text-o"></span> Exámenes Paraclínicos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="table-responsive" style="width: 100%">
                        <table class="table table-bordered table-hover table-condensed pointer" id="tabla_resultado_examenes" cellspacing="0" width="100%">
                            <thead class="ttitulo ">
                            <tr class="">
                                <td class="nombre_tabla" colspan="4">TABLA EXÁMENES PARACLÍNICOS </td>
                                <td class="sin-borde text-right border-left-none"> 
                                    <span class="btn btn-default" id="agregar_resultado_examen"> <span class="fa fa-plus red"></span> Agregar</span>
                                </td>
                            </tr>
                            <tr class="filaprincipal">
                                <td>Examen</td>
                                <td>Estado</td>
                                <td>Observaciones</td>
                                <td>Adjunto</td>
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
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_add_examenpar" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_examenpar" enctype="multipart/form-data" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Exámenes Paraclínicos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_examenpar" id="id_examenpar" required class="form-control cbxexamenpar" tile="Examen Paraclínico">
                            <option value="">Seleccione Examen</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_estado_examen" id="id_estado_examen" required class="form-control cbxparametro2" title="Estado">
                            <option value="">Seleccione Estado</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='observacion_paraclinicos' id='observacion_paraclinicos' placeholder="Observaciones" title="Observaciones"></textarea>    
                        </div>
                        <div class="clearfix"></div>
                        <div class="agrupado">
							<div class="input-group ">
                                <label class="input-group-btn"><span class="btn btn-primary">
                                    <span class="fa fa-folder-open"></span>
                                    Buscar <input name="adjunto_resultado" type="file" style="display: none;" id="adjunto_resultado">
                                    </span>
                                </label>
                                <input type="text" class="form-control soporte" readonly placeholder='Adjuntar Resultado'>
                            </div>
						</div> 

                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_examenpar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <div class="modal fade" id="modal_diagnostico" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="#" id="form_diagnostico" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-pencil-square-o"></span> Diagnósticos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="table-responsive" style="width:100%">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_diagnostico" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="4" style="" class="nombre_tabla">TABLA DIAGNÓSTICOS <br>
                                    <td class="sin-borde text-right border-left-none" colspan="3"> 
                                        <span class="btn btn-default agregar_diagnostico"> <span class="fa fa-plus red"></span> Agregar</span>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td class="opciones_tbl_btn">Código</td>
                                    <td>Diagnósticos</td> 
                                    <td></td><td></td>                                   
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody >
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form  id="form_add_diagnostico"  method="post">
        <div class="modal fade" id="modal_add_diagnostico" role="dialog">
            <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-search"></span> Diagnósticos</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                <div class="row" id="" style="width: 100%">
                    <div class="form-group agrupado col-md-8 text-left">
                    <div class="input-group">
                        <input id='txt_diag_buscar' class="form-control" placeholder="Ingrese Código o descripción del Diagnóstico">
                        <span class="input-group-btn"><button class="btn btn-default test" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
                    </div>
                    </div>
                    <div class="table-responsive col-md-12" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_diagnostico_busqueda" cellspacing="0" width="100%">
                        <thead class="ttitulo ">
                        <tr class="">
                            <td colspan="3" class="nombre_tabla">TABLA DIAGNÓSTICOS</td>
                        </tr>
                        <tr class="filaprincipal">
                            <td class="opciones_tbl_btn">Código</td>
                            <td>Descripción</td>
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

    <div class="modal fade" id="modal_revision_sistemas" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="#" id="form_revision_sistemas" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-check"></span> Revisión por Sistemas</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="table-responsive" style="width:100%">
                        <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_revision_sistemas" cellspacing="0" width="100%" style="">
                            <thead class="ttitulo ">
                                <tr>
                                    <td colspan="4" style="" class="nombre_tabla">TABLA SISTEMAS - ÓRGANOS</td>
                                    <td class="sin-borde text-right border-left-none"> 
                                        <span class="btn btn-default" id="agregar_rev_sistema"> <span class="fa fa-plus red"></span> Agregar</span>
                                    </td>
                                </tr>
                                <tr class="filaprincipal">
                                    <td>Sistema/Órgano</td>
                                    <td>Observación</td>
                                    <td></td><td></td>
                                    <td class="opciones_tbl_btn">Acción</td>
                                </tr>
                            </thead>
                            <tbody >
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_add_revsistemas" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_add_revsistemas" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Sistemas - Órganos</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">     
                        <div class="col-md-12" style="padding: 0px;">
                            <select name="id_sistema" id='id_sistema' required class="form-control cbxsistemas" title="Sistemas/Órganos">
                            <option value="">Seleccione Sistema/Órgano</option>
                            </select>  
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="padding: 0px;">
                             <textarea class="form-control" name='observacion_rev' id='observacion_rev' placeholder="Observaciones" title="Observaciones"></textarea>    
                        </div>    
                      </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                        <button type="button" class="btn btn-danger active" id="guardar_revsistemas"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div> 
    
    <div id="imprimir_historia" class="oculto">
        <table class="table" style="font-size:10pt;">
            <tr>
                <td width='100'><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='100'></td>
                <td class='text-center' colspan='5'><h3 class='text-center titulo_print'></h3></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">FECHA</td>
                <td class="fecha_atencion" colspan='2'></td>
                <td class="ttitulo">No. HISTORIA</td>
                <td class="n_historia" colspan='2'></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>IDENTIFICACIÓN</b></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">NOMBRES Y APELLIDOS</td>
                <td class="nombre_apellido" colspan='2'></td>
                <td class="ttitulo">No. IDENTIFICACIÓN</td>
                <td class="identificacion" colspan='2'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">NATURAL DE</td>
                <td class="lugar_nacimiento" colspan='2'></td>   
                <td class="ttitulo">FECHA NACIMIENTO</td>
                <td class="fecha_nacimiento" colspan='2'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">EDAD</td>
                <td class="edad" colspan='2'></td>
                <td class="ttitulo">SEXO</td> 
                <td class="genero" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">
                <td class="ttitulo">ESTADO CIVIL</td>
                <td class="estado_civil" colspan='2'></td>
                <td class="ttitulo">FECHA INGRESO</td>
                <td class="fecha_ingreso" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">    
                <td class="ttitulo">S. MILITAR</td>
                <td class="servicio_militar" colspan='2'></td>
                <td class="ttitulo">EPS</td>
                <td class="eps" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">   
                <td class="ttitulo">ARL</td>
                <td class="arl" colspan='2'></td>  
                <td class="ttitulo">TIPO EXAMEN</td>
                <td class="servicio" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">
                <td class="ttitulo">DIRECCIÓN</td>
                <td class="direccion" colspan='2'></td>
                <td class="ttitulo">TELÉFONO</td>
                <td class="telefono" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">
                <td class="ttitulo">PROFESIÓN</td>
                <td class="	profesion" colspan='2'></td>
                <td class="ttitulo">EMPRESA</td>
                <td class="empresa" colspan='2'></td>
            </tr>
            <tr class="text-left detalle_oculto">
                <td class="ttitulo">CARGO</td>
                <td class="cargo" colspan='2'></td>   
                <td class="ttitulo">DEPENDENCIA</td>
                <td class="dependencia" colspan='2'></td>
            </tr>
        </table>
        <table class="table table-bordered table-condensed" id='tab_escolaridad' cellspacing="0" width="100%" style="font-size:8pt;">
            <thead class="ttitulo">
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>ESCOLARIDAD</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>ESCOLARIDAD</b></td>
                    <td><b>ESTADO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class='table dato_familiar oculto' style="font-size:10pt;">    
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>DATOS DEL ACOMPAÑANTE</b></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">NOMBRE</td>
                <td class="nombre_acomp" colspan='2'></td>
                <td class="ttitulo">TELÉFONO</td>
                <td class="telefono_acomp" colspan='2'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">RESPONSABLE</td>
                <td class="nombre_resp" colspan='2'></td>
                <td class="ttitulo">TELÉFONO</td>
                <td class="telefono_resp" colspan='2'></td>
            <tr>
            <tr class="text-left">    
                <td class="ttitulo">PARENTESCO</td>
                <td class="parentesco_resp" colspan='5'></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>ANAMNESIS</b></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">MOTIVO CONSULTA</td>
                <td class="motivo_consulta" colspan='2'></td>
                <td class="ttitulo">ENFERMEDAD ACTUAL</td>
                <td class="enfermedad_actual" colspan='2'></td>
            </tr>
        </table>
        <table class="table table-bordered table-condensed oculto" id="tab_historia_laboral" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='6'><b>HISTORIA LABORAL</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>FECHA</b></td>
                    <td><b>EMPRESA</b></td>
                    <td><b>CARGO</b></td>
                    <td><b>PROTECCIÓN</b></td>
                    <td><b>TIEMPO</b></td>
                    <td><b>CANTIDAD</b></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed oculto" id="tab_accidente_laboral" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='6'><b>ACCIDENTES DE TRABAJO</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>FECHA</b></td>
                    <td><b>EMPRESA</b></td>
                    <td><b>DIAS INCAPACIDAD</b></td>
                    <td><b>LESIÓN</b></td>
                    <td><b>ARL</b></td>
                    <td><b>ENFERMEDAD</b></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_antecedente_familiar" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>ANTECEDENTES FAMILIARES</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>TIPO ENFERMEDAD</b></td>
                    <td><b>PARENTESCO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_antecedente_personales" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>ANTECEDENTES PERSONALES</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>TIPO ANTECEDENTES</b></td>
                    <td><b>OBSERVACIÓN</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table oculto ant_gineco" style="font-size:10pt;">
                <tr class="nombre_tabla text-left">
                    <td colspan='6'><b>ANTECEDENTES GINECO-OBSTÉTRICOS</b></td>
                </tr>
                <tr class="text-left">
                    <td class="ttitulo">MENARQUÍA</td>
                    <td class="menarquia"></td>
                    <td class="ttitulo">CICLOS MENSTRUALES</td>
                    <td class="ciclo"></td>
                    <td class="ttitulo">FUR</td>
                    <td class="fur"></td>
                </tr>
                <tr class="text-left">
                    <td class="ttitulo">GESTACIONES</td>
                    <td class="g"></td>
                    <td class="ttitulo">PARTOS</td>
                    <td class="p"></td>
                    <td class="ttitulo">CESAREAS</td>
                    <td class="c"></td>
                </tr>
                <tr class="text-left">
                    <td class="ttitulo">ABORTOS</td>
                    <td class="a"></td>
                    <td class="ttitulo">VIVOS</td>
                    <td class="v"></td>
                    <td class="ttitulo">PLANIFICACIÓN</td>
                    <td class="planificacion"></td>
                    <!-- <td class="ttitulo">FUP</td>
                    <td class="fup"></td> -->
                </tr>
                <tr class="text-left">
                    <td class="ttitulo">DISMENORREAS</td>
                    <td class="dismenorreas"></td>
                    <td class="ttitulo">FECHA ULTIMA CITOLOGÍA</td>
                    <td class="fuc"></td>
                    <td class="ttitulo">CITOLOGÍA NORMAL</td>
                    <td class="citologia"></td>
                </tr>
                <tr class="text-left">
                    <td class="ttitulo">OBSERVACIÓN</td>
                    <td class="observacion" colspan='5'></td>
                </tr>   
        </table>
        <table class="table table-bordered table-condensed oculto" id="tab_habitos" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>HÁBITOS</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>HÁBITO</b></td>
                    <td><b>FRECUENCIA</b></td>
                    <td><b>AÑOS</b></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_revision_sistema" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>REVISIÓN POR SISTEMAS</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>SISTEMA U ÓRGANOS</b></td>
                    <td><b>OBSERVACIÓN</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_examen_fisico" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>EXAMEN FÍSICO</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>TIPO EXAMEN</b></td>
                    <td><b>ESTADO</b></td>
                    <td><b>OBSERVACIÓN</b></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table" style="font-size:10pt;">
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>SIGNOS VITALES</b></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">PESO KG</td>
                <td class="peso"></td>
                <td class="ttitulo">TALLA CM</td>
                <td class="talla"></td>
                <td class="ttitulo">TEMPERATURA °</td>
                <td class="temperatura"></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">TENSIÓN ARTERIAL</td>
                <td class="tension_a"></td>
                <td class="ttitulo">FREC. CARDIACA MIN</td>
                <td class="frecuencia_c"></td>
                <td class="ttitulo">FREC. RESPIRATORIA MIN</td>
                <td class="frecuencia_c"></td>
            </tr>
            <tr class="text-left">    
                <td class="ttitulo">IMC</td>
                <td class="imc"></td>
                <td class="ttitulo">RANGO IMC</td>
                <td class="clasificacion_imc" colspan='3'></td>                
            </tr>
            <tr class="text-left">
                <td class="ttitulo">MANO DOMINANTE</td>
                <td class="mano_dominate"></td>
                <td class="ttitulo">DETALLE</td>
                <td class="detalle_examenf" colspan='3'></td>
            </tr> 
        </table>
        <table class="table table-bordered table-condensed oculto" id="tab_paraclinicos" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>RESULTADO DE EXÁMENES PARACLÍNICOS</b></td>
                </tr>
                <tr class="filaprincipal">                    
                    <td><b>EXAMEN</b></td>
                    <td><b>ESTADO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_diagnosticos" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>DIAGNÓSTICO PRESUNTIVOS</b></td>
                </tr>
                <tr class="filaprincipal">                    
                    <td><b>DIAGNÓSTICO</b></td>
                    <td><b>CÓDIGO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table" style="font-size:10pt;">
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>CONCEPTO MÉDICO</b></td>
            </tr>
            <tr class="text-left oculto valoracion_pac">
                <td class="ttitulo">RESULTADO</td>
                <td class="resultado" colspan='5'></td>
            </tr>
            <tr class="text-left oculto valoracion_pac">
                <td class="ttitulo">APLAZAMIENTO</td>    
                <td class="aplazamiento" colspan='5'></td>
            </tr>
            <tr class="text-left oculto valoracion_pac">
                <td class="ttitulo">RECOMENDACIONES</td>
                <td class="recomendaciones" colspan='5'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">OBSERVACIONES</td>
                <td class="observacion" colspan='5'></td>
            </tr>
            <tr class="text-left">    
                <td class="ttitulo">PLAN TERAPÉUTICO/CONTROL</td>
                <td class="control" colspan='5'></td>
            </tr>
            <tr class="text-left clausura oculto">
                <td colspan='6' style="border: medium transparent"><b>Autorizo a la Dr.(a) <span class="profesional_ocup"></span> , la entrega de este estudio a la Corporación Universidad de la Costa, CUC.</b></td>
            </tr>
            <tr>
                <td colspan='6' style="border: medium transparent"></td>
            </tr>
            <tr>
                <td class="ttitulo" colspan='3' style="border: medium transparent"><hr/><b>FIRMA Y REGISTRO MÉDICO</b></td>
                <td class="ttitulo" colspan='3' style="border: medium transparent"><hr/><b>FIRMA E IDENTIFICACIÓN DEL TRABAJADOR</b></td>
            </tr>
        </table>   
    </div>

    <div id="imprimir_bitacora" class="oculto">
        <table class="table" style="font-size:10pt;">
            <tr>
                <td width='100'><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='100'></td>
                <td class='text-center' colspan='5'><h3 class='text-center'>NOTAS DE ENFERMERIA</h3></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">FECHA</td>
                <td class="fecha_atencion" colspan='5'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">NOMBRES Y APELLIDOS</td>
                <td class="nombre_apellido" colspan='2'></td>
                <td class="ttitulo">No. IDENTIFICACIÓN</td>
                <td class="identificacion" colspan='2'></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">DEPENDENCIA</td>
                <td class="dependencia" colspan='2'></td>
                <td class="ttitulo">EDAD</td>
                <td class="edad" colspan='2'></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>OBSERVACIONES DE INGRESO</b></td>
            </tr>
            <tr class="text-left">
                <td class="observacion_ingreso" colspan='6'></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>MOTIVO DE INGRESO</b></td>
            </tr>
            <tr class="text-left">
                <td class="motivo_ingreso" colspan='6'></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>CONDICIÓN GENERAL DEL PACIENTE</b></td>
            </tr>
            <tr class="text-left">
                <td class="condicion_general" colspan='6'></td>
            </tr>
        </table>
        <table class="table" style="font-size:10pt;" id="signos_vitales">
            <tr class="nombre_tabla text-left">
                <td colspan='6'><b>SIGNOS VITALES</b></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">PESO KG</td>
                <td class="peso"></td>
                <td class="ttitulo">TALLA CM</td>
                <td class="talla"></td>
                <td class="ttitulo">TEMPERATURA °</td>
                <td class="temperatura"></td>
            </tr>
            <tr class="text-left">
                <td class="ttitulo">TENSIÓN ARTERIAL</td>
                <td class="tension_a"></td>
                <td class="ttitulo">FREC. CARDIACA MIN</td>
                <td class="frecuencia_c"></td>
                <td class="ttitulo">FREC. RESPIRATORIA MIN</td>
                <td class="frecuencia_r"></td>
            </tr>
            <tr class="text-left">    
                <td class="ttitulo">IMC</td>
                <td class="imc"></td>
                <td class="ttitulo">RANGO IMC</td>
                <td class="clasificacion_imc" colspan='3'></td>                
            </tr>
            <tr class="text-left">
                <td class="ttitulo">MANO DOMINANTE</td>
                <td class="mano_dominate"></td>
                <td class="ttitulo">DETALLE</td>
                <td class="detalle_examenf" colspan='3'></td>
            </tr>
        </table>
        <table class="table table-bordered table-condensed" id="tab_antecedente_familiar_bit" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>ANTECEDENTES FAMILIARES</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>TIPO ENFERMEDAD</b></td>
                    <td><b>PARENTESCO</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table table-bordered table-condensed" id="tab_antecedente_personales_bit" cellspacing="0" width="100%" style="font-size:10pt;">
            <thead>
                <tr class="nombre_tabla text-left">
                    <td colspan='3'><b>ANTECEDENTES PERSONALES</b></td>
                </tr>
                <tr class="filaprincipal">
                    <td><b>TIPO ANTECEDENTES</b></td>
                    <td><b>OBSERVACIÓN</b></td>
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>    
        </table>
        <table class="table" style="font-size:10pt;">
            <tr class="nombre_tabla text-left">
                <td><b>REPORTE DE ATENCIÓN</b></td>
            </tr>
            <tr class="text-left">
                <td class="reporte_atencion"></td>
            </tr>
            <tr class="nombre_tabla text-left">
                <td><b>OBSERVACIONES DE SALIDA</b></td>
            </tr>
            <tr class="text-left">
                <td class="observacion_salida"></td>
            </tr>
            <tr>
                <td style="border: medium transparent"></td>
            </tr>
            <tr>
                <td class="ttitulo" style="border: medium transparent"><hr style="width:250px;"/><b>FIRMA EMFERMERA</b></td>
            </tr>
        </table>
    </div> 

    <div id="politica_proteccion_datos" class="oculto">
        <table class="table" style="font-size:12pt;" >
            <tr>
                <td width='100' style="border: medium transparent"><img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='100'></td>
                <td class='text-center' colspan='5' style="border: medium transparent"><h3 class='text-center'>Consentimiento Informado<br/>Política Protección de Datos</h3></td>
            </tr>
            <tr>
                <td colspan="6" style="border: medium transparent" class='text-justify'><p>En _________________ a los _____ dias del mes de _____________ de 2018 yo, ___________________________ identificado con número de documento _____________
                tipo de documento _________________ he sido informado y entiendo que la información que se consigne en este documento puede ser usado con fines médicos.
                He comprendido la información anterior y mis preguntas han sido respondidas de manera satisfactoria.
                Además, reconozco que para el tratamiento de los datos personales que haya suministrado y/o le hayan sido solicitados a través del procedimiento médico realizado en la enfermería de la Universidad de la Costa
                CUC, se encuentra en conformidad con lo establecido en la ley 1581 de 2012, y demás normas que la complemente o modifique.</p>
      
                <p>En ese sentido, la enfermería de la Universidad de la Costa CUC le informa que los datos personales de carácter semiprivado, privado y/o sensible suministrados por usted como son: nombre, apellidos, identificación,
                antecendente personales y familiares, diagnósticos, recomendaciones y demás datos necesarios que le sean solicitados al momento de realizar la actualización o registro de sus datos en el procedimiento médico, serán
                incorporados a una base de datos, titularidad de la Universidad de la Costa CUC, con el fin de identificarlo como usuario de nuestro servicio, suscriptor y nos autoriza para enviarle información de su procedimiento
                y/o información de nuestras actividades, en los medios de contacto.</p>
               
                <p>Los datos personales que sean facilitado por usted serán tratados con el grado de protección adecuado exigido por la ley y de conformidad con las politicas de tratamiento de datos y politicas internas. En este sentido,
                la enfermería de la Universidad de la Costa CUC, se compromete a tratar los datos con la finalidad exclusiva con que fueron recabados. Y se reserva la facultad de responder las consultas que versen sobre datos sensibles
                o datos pertenecientes a menores de edad.</p>
              
                <p>A usted como titular de los datos le asisten los derechos establecidos en el artículo 8 de la ley 1581 de 2012 para la cual puede dirigirse a nuestros puntos de atención y/o enfermería Bloque 5 al frente de bloque 7, o 
                comunicarse a nuestra línea 3689223.</p>
                </td>
            </tr>
            <tr>
                <td colspan='6' style="border: medium transparent"></td>
            </tr>
            <tr>
                <td class="ttitulo text-left" colspan='3' style="border: medium transparent"><b>Firma:</b></td>
                <td class="ttitulo text-left" style="border: medium transparent"><hr/></td>
                <td class="ttitulo text-left" colspan='2' style="border: medium transparent"></td>
            </tr>
            <tr>
                <td class="ttitulo text-left" colspan='3' style="border: medium transparent"><b>Documento:</b></td>
                <td class="ttitulo text-left" style="border: medium transparent"><hr/></td>
                <td class="ttitulo text-left" colspan='2' style="border: medium transparent"></td>
            </tr>
        </table>
    </div> 

    <!-- Modal asignacion de servicio -->
    <div class="modal fade" id="modal_asignar_servicio" role="dialog">
        <div class="modal-dialog">
            <form id="form_asignar_servicio" method="post">
                <div class="modal-content">
                        <div class="modal-header" id="headermodal">
                            <button type="button" class="close" data-dismiss="modal"> X</button>
                            <h3 class="modal-title"><span class="fa fa-plus"></span> <span > Asignación de Servicio</span></h3>
                        </div>
                        <div class="modal-body" id="bodymodal">
                          <div class="row">
                            <!-- <h4 class="ttitulo"><span>PACIENTE: </span> <span class="solicitante"></span></h4>        -->
                                <div id="mod_detalle">    
                                    <select name="id_servicio" class="form-control cbxservicio"><option value=""">Seleccione Servicio</option></select>  
                                     
                                            <!-- <div class="input-group agro">
                                                <select name="id_profesional" required class="form-control cbxprofesional"><option value="">Seleccione Profesional</option></select>
                                                <span class="input-group-addon pointer"><span class="fa fa-plus red" title="Nuevo"></span></span>
                                            </div>     -->
                                        
                                       <textarea class="form-control comentarios" name="observaciones" placeholder="Observaciones del Profesional"></textarea>                               
                                    <div class="clearfix"></div>
                             </div>                   
                        </div>
                    <br/>    
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
              </div>
            </form>
        </div>
    </div>                 
    <!--  -->
    
    <!-- Modal modificacion de solicitud -->
    <div class="modal fade" id="modal_modificar_atencion" role="dialog">
        <div class="modal-dialog">
            <form id="form_modificar_atencion" method="post">
                <div class="modal-content">
                        <div class="modal-header" id="headermodal">
                            <button type="button" class="close" data-dismiss="modal"> X</button>
                            <h3 class="modal-title"><span class="fa fa-edit"></span> <span > Modificar Atencion</span></h3>
                        </div>
                        <div class="modal-body" id="bodymodal">
                          <div class="row">
                            <h4 class="ttitulo"><span>PACIENTE: </span> <span class="solicitante_mod"></span></h4>       
                                <div id="mod_detalle">   
                                    <select name="id_servicio_mod" id="id_servicio_mod" class="form-control cbxservicio" required><option value="">Seleccione Servicio</option></select>  
                                    <textarea class="form-control comentarios" name="observaciones_mod" id="observaciones_mod" placeholder="Observaciones del Profesional"></textarea>                               
                                    <div class="clearfix"></div>
                             </div>                   
                        </div>
                    <br/>    
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-ok"></span><span class="Guardar"> Guardar</span></button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
              </div>
            </form>
        </div>
    </div>                 

     <!-- Modal creación de servicio -->
     <div class="modal fade" id="modal_nuevo_valor" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_guardar_valor_parametro" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo Servicio</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
                        <textarea class="form-control inputt" name="descripcion" id="valorx" placeholder="Descripción"></textarea>
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

    <!--HHHHHH-->
    <div class="modal fade" id="ModalEditarE" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_cambiar_estado_covid" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal">X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Modificar Estado</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="hidden" id="idsolicitudcov" name="idsolicitudcov">
                            <select name="EstadoCambio" id='EstadoCambio' class="form-control cbxestadoproto" title="Estado" onchange="TpEstado(0)">
                            </select>
                            <select name="motivocambio" id='motivocambio' class="form-control cbxcambioestado" title="Motivos de Cambio">
                                <option value="">Seleccione el motivo...</option>
                            </select>  
                        </div>
                        <textarea class="form-control inputt" name="observacionescambio" id="observacionescambio" placeholder="Observaciones"></textarea>
                    </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Cambiar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div> 
    </div>
    <!--HHHHHH-->
    <!-- Modal modificacion servicio -->
    <div class="modal fade" id="ModalModificarParametro" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_modificar_valor_parametro" method="post">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-truck"></span> Modificar Servicio</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                <div class="row divmodifica">
                <input type="text" id="txtValor_modificar" class="form-control" placeholder="Nombre" name="nombre" required>
                <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripción"
                name="descripcion" required></textarea>
                </div>
                </div>
                <div class="modal-footer" id="footermodal">
                <button type="submit" class="btn btn-danger active btnModifica"><span
                    class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <!-- Modal creación de filtros  -->
    <div class="modal fade" id="modal_filtrar_atenciones" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <select id="tipo_solicitud_filtro" class="form-control inputt cbxtiposolicitud">
                            <option value="">Seleccione Tipo Solicitud</option>
                        </select>
                        <select id="estado_filtro" class="form-control inputt cbxestado">
                            <option value="">Filtrar Atenciones por Estados</option>
                        </select>
                        <select id="servicio_filtro" class="form-control inputt cbxservicio">
                            <option value="">Filtrar Atenciones por Servicio</option>
                        </select>
                        <select id="tipopersona_filtro" class="form-control inputt cbxtipopersona">
                            <option value="">Filtrar Atenciones por Población</option>
                        </select>
                        <div class="agrupado"> 
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha Inicio" type="text" value="" name="fecha_filtro" id="fecha_filtro" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>                                  
                            </div>
                            <div class="col-md-6" style="padding: 0px;">
                                <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                    <input class="form-control" size="100" placeholder="Fecha Fin" type="text" value="" name="fecha_filtro_2" id="fecha_filtro_2" maxlength="99" title="Fecha" data-toggle="popover" data-trigger="hover">
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                    <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>                                  
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="footermodal">
                    <button type="button" class="btn btn-danger active" id="btnfiltrar_sol"><span class="glyphicon glyphicon-ok"></span> Generar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="Modal_Covid" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="GuardarRepProCovid" method="post">
                <div class="modal-content" >
                    <div class="modal-header" id="headermodal">
                        <button type="button" class="close" data-dismiss="modal"> X</button>
                        <h3 class="modal-title"><span class="fa fa-plus"></span> REPORTE DE PROTOCOLO COVID-19</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                        <div class="row">
                            <select id="subclasifi_salud" name="subclasifi_salud" class="form-control inputt cbxsubcla">
                                <option value="">Subclasificación</option>
                            </select>
                            <select id="eps_salud" name="eps_salud" class="form-control inputt cbxeps">
                                <option value="">EPS</option>
                            </select>
                            <input type="text" name="barrio" class="form-control inputt" placeholder="Barrio" id="barrio">
                            <select id="med_rep" name="med_rep" class="form-control inputt cbxmedrepor">
                                <option value="">Medio de reporte</option>
                            </select>
                            <select id="mot_rep" name="mot_rep" class="form-control inputt cbxmotproto">
                                <option value="">Motivo de reporte</option>
                            </select>
                            <select id="tipo_rep" name="tipo_rep" class="form-control inputt cbxtiporepor">
                                <option value="">Tipo de reporte</option>
                            </select>
                            <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input1">
                                <input class="form-control" size="100" placeholder="Inicio de sintomas" type="text" value="" name="fe_ini_sinto" id="fe_ini_sinto" maxlength="99" title="Fecha Nacimiento" data-toggle="popover" data-trigger="hover">
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon pointer"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div> 
                            <textarea class="form-control" name="observacion_salud" id="observacion_salud" placeholder="Observaciones" title="Observaciones"></textarea>        
                            <div class="funkyradio-success" id="div_sintomas">
                                <input type="checkbox" id="sintomas" name="sintomas" value="1">
                                <label for="sintomas" title="Con cupos" style="margin : 5 0 5 0;"> ¿Presenta sintomas?</label>
                            </div>
                            <div class="funkyradio-success">
                                <input type="checkbox" id="act_pro" name="act_pro" value="1">
                                <label for="act_pro" title="Con cupos" style="margin : 5 0 5 0;"> Activar protocolo</label>
                            </div>
                        </div> 
                    <div class="modal-footer" id="footermodal">
                        <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <div class="modal fade" id="modal_ver_observacion" role="dialog">
    <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-eye"></span> Observaciones</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_salud_observaciones" cellspacing="0" width="100%">
                      <thead class="ttitulo ">
                        <tr class="">
                            <span id="nueva_observacion" class="btn btn-default" title="Agregar nueva observación" data-toggle='popover' data-trigger='hover'> 
                            <span class="fa fa-plus-circle red" ></span> Nueva observación</span>
                          </tr>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Observación</td>
                            <td>Profesional</td>
                            <td>Fecha registro</td>
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
<div class="modal fade" id="modal_nueva_observacion" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_nueva_observacion" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Nueva Observación</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="hidden" id="idsolic" name="idsolic">
                        <textarea class="form-control inputt" name="newobserv" id="newobserv" placeholder="Observación"></textarea>
                    </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                    <button class="btn btn-danger active" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_editar_treporte" role="dialog">
        <div class="modal-dialog">
            <form action="#" id="form_editar_treporte" method="post">
                <div class="modal-content">
                    <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Tipo de reporte</h3>
                    </div>
                    <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="hidden" id="idsolic_rep" name="idsolic_rep">
                        <select id="treportecambio" name="treportecambio" class="form-control inputt cbxtiporepor">
                            <option value="">Tipo de reporte</option>
                        </select>
                    </div>
                    </div>
                    <div class="modal-footer" id="footermodal">
                    <button class="btn btn-danger active" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


<script>
    $(document).ready(function() {
        inactivityTime();
        Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Dependencia");
        Cargar_parametro_buscado(124, ".cbxduracion", "Seleccione Duración Ejercicio"); 
        Cargar_parametro_buscado(147, ".cbxservicio", "Seleccione Servicio");       
        Cargar_parametro_buscado_aux(149, ".cbxestado", "Seleccione Estado");
        Cargar_parametro_buscado(150, ".cbxestadocivil", "Seleccione Estado Civil");
        Cargar_parametro_buscado(153, ".cbxriesgo", "Seleccione Tipo de Riesgo");
        Cargar_parametro_buscado(154, ".cbxenfermedad_fam", "Seleccione Tipo de Enfermedad");
        Cargar_parametro_buscado(155, ".cbxparentesco", "Seleccione Parentesco");
        Cargar_parametro_buscado(156, ".cbxantecedente", "Seleccione Tipo Antecedente");
        Cargar_parametro_buscado(157, ".cbxvacuna", "Seleccione Vacuna");
        Cargar_parametro_buscado(158, ".cbxplanificacion", "Tipo de Planifición");
        Cargar_parametro_buscado(159, ".cbxfrecuencia", "Seleccione la Frecuencia");
        Cargar_parametro_buscado(160, ".cbxsistemas", "Seleccione Sistema/Órgano");
        Cargar_parametro_buscado(161, ".cbxtipoexamenf", "Seleccione Tipo Examen");
        Cargar_parametro_buscado(162, ".cbxexamenpar", "Seleccione Examen");
        Cargar_parametro_buscado(164, ".cbxparametro2", "Seleccione Estado");
        Cargar_parametro_buscado(166, ".cbxestado_escolaridad", "Seleccione Estado")
        Cargar_parametro_buscado_aux(165, ".cbxhabitos", "Seleccione Hábito");
        Cargar_parametro_buscado(167, ".cbxvaloracion", "Seleccione el Concepto Médico"); 
        Cargar_parametro_buscado_aux(185, ".cbxtipopersona", "Seleccione Tipo de Población");   
        Cargar_parametro_buscado(279, ".cbxsubcla", "Seleccione La Subclasificación");
        Cargar_parametro_buscado(278, ".cbxeps", "Seleccione la EPS");  
        Cargar_parametro_buscado(280, ".cbxtiporepor", "Seleccione el Tipo de Reporte");
        Cargar_parametro_buscado(281, ".cbxmedrepor", "Seleccione el Medio de Reporte");
        Cargar_parametro_buscado(282, ".cbxmotproto", "Seleccione el Motivo de Reporte");
        Cargar_parametro_buscado_aux(321, ".cbxestadoproto", "Seleccione el tipo de estado");
        Cargar_parametro_buscado(322, ".cbxcambioestado", "Seleccione el motivo...");
        listar_atenciones(<?php echo $id?>);
        activarfile();
    });
</script>

<script type="text/javascript">
$(".form_datetime").datetimepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayBtn: true,
    maxView: 4,
    minView: 2,
    // daysOfWeekDisabled: [0],
});

$(".form_datetime_habito").datetimepicker({
    format: 'yyyy',
    autoclose: true,
    todayBtn: true,
    startView: 4,
    maxView: 4, 
    minView: 4,
});
</script>
