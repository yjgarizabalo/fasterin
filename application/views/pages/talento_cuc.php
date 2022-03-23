<?php 
  $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Eval" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true :false;
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="<?php echo base_url(); ?>js-css/estaticos/js/html2canvas.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/th.css">
<div class="container col-md-12 " id="inicio-user">
  <div class="tablausu col-md-12 text-left" id="container_solicitudes">
    <div class="table-responsive">
      <p class="titulo_menu pointer regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <div class="form-group" style="width:100%">
        <div class="input-group col-md-4" style="float: right">
          <input class="form-control" id="txt_buscar_persona" value=""
            placeholder="Ingrese Nombre, Apellido, Usuario o Identificacion">
          <span class="input-group-addon pointer" title="Buscar Persona" data-toggle="popover" data-trigger="hover"
            id="btn_buscar_persona"><span class="glyphicon glyphicon-search"></span></span>
        </div>
      </div>
      <br><br>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_solicitudes"
        cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr>
            <td colspan="3" class="nombre_tabla">
              TABLA SOLICITUDES<br>
              <span class="mensaje-filtro" hidden>
                <span class="fa fa-bell red"></span>
                La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td colspan="3" class="sin-borde text-right border-left-none">
              <span class="btn btn-default" id="ver_notificaciones">
                <span class="badge btn-danger n_notificaciones"></span> Notificaciones
              </span>
              <?php if ($administra) {?>
              <!-- <span class="black-color pointer btn btn-default" id="btnformacionGen">
                <span class="fa fa-book red"></span> Formación General
              </span> -->
              <span class="black-color pointer btn btn-default" id="btnasistencias">
                <span class="fa fa-users red"></span> Asistencias
              </span>
              <span class="black-color pointer btn btn-default" id="btnasignaciones">
                <span class="fa fa-user red"></span> Asignaciones
              </span>
              <span class="black-color pointer btn btn-default" id="btnConfiguraciones">
                <span class="fa fa-cogs red"></span> Administrar
              </span>
              <span class="black-color pointer btn btn-default" id="btnPermisos" >
                <span class="fa fa-cog red"></span> Permisos
              </span>
              <?php }?>
              <span id="btn_csep"></span>
              <span id="btn_filtros" class="black-color pointer btn btn-default">
                <span class="fa fa-filter red"></span> Filtrar
              </span>
              <span id="btn_limpiar" class="black-color pointer btn btn-default">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="opciones_tbl">ver</td>
            <td>Nombre Completo</td>
            <td>Identificación</td>
            <td>Cargo</td>
            <td>Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <!-- <div class="tablausu col-md-12 " id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
		<div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
			<div id="container-principal2" class="container-principal-alt">
				<h3 class="titulo_menu">
					<span class="fa fa-navicon"></span> MENÚ
				</h3>
				<div class="row" id="menu_th">
					<div id="listado_solicitudes">
						<div class="thumbnail">
							<div class="caption">
								<img src="<?php echo base_url() ?>/imagenes/otrassolicitudes.png" alt="...">
								<span class="btn  form-control btn-Efecto-men">MIS SOLICITUDES</span>
							</div>
						</div>
					</div>
				</div>
				<p class="titulo_menu titulo_menu_alt pointer" id="btn_regresar"><span class="fa fa-reply-all naraja"></span>Regresar</p>
			</div>
		</div>
	</div> -->

  <div class="modal fade" id="modal_notificaciones_compras" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="panel_notificaciones" style="width: 100%" class="list-group"></div>
          <?php if ($administra) { ?><div id="panel_notificaciones_solicitudes" style="width: 100%" class="list-group">
          </div><?php } ?>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_asistencias" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Asistencias Plan de formación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <table class="table table-bordered table-hover table-condensed " id="tabla_asistencias_formacion" cellspacing="0" width="100%">
            <thead class="">
              <tr>
                <td colspan="3" class="nombre_tabla">TABLA ASISTENCIAS PLAN FORMACIÓN</td>
                <td class="sin-borde text-right border-left-none">
                  <span class="btn btn-default btn_filtrar_asistencias"><span class="fa fa-filter red"></span>Filtrar</span>
                </td>    
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td>Tema</td>
                <td>Facilitador</td>
                <td>Fecha Asistencia</td>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade scroll-modal" id="modal_administrar_asignacion" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" id="headermodal">
					<button type="button" class="close" data-dismiss="modal"> X</button>
					<h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Asignaciones</h3>
				</div>
				<div class="modal-body">
					<nav class="navbar navbar-default" id="menu_administrar_asignaciones">
						<div class="container-fluid">
							<ul class="nav nav-tabs nav-justified">
								  <li class="pointer indicadores"><a><span class="fa fa-list red"></span> Indicadores</a></li>
								  <li class="pointer form_esencial"><a><span class="fa fa-book red"></span> Formación Esencial</a></li>
								  <li class="pointer funciones"><a><span class="fa fa-cogs red"></span> Funciones</a></li>
							</ul>
						</div>
					</nav>

					<div class="asignacion_indicadores adm_proceso row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<div class="form-group">
								<div class="input-group agro col-md-6">
									<input name="persona_ind" type="hidden" id="input_sele_ind">
									<span id="selec_persona_ind" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
									<span id="sele_perso_ind" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>
							<table id="tabla_asignacion_indicadores" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="4">TABLA INDICADORES</th>
										<td class="sin-borde text-right border-left-none">
											<span type="button" class="btn btn-default add_indicador oculto" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td> 
									</tr>
									<tr class="filaprincipal">
										<td>Descripción</td>
										<td>Tipo Meta</td>
										<td>Meta</td>
										<td>Periodo</td>
										<td class="opciones_tbl_btn">Acción</td>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

          <div class="asignacion_fun adm_proceso oculto row" style="margin:0px;width:100%;">
						<div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
							<div class="form-group">
								<div class="input-group agro col-md-6">
									<input name="persona" type="hidden" id="input_sele_fun">
									<span id="selec_persona_fun" class="form-control text-left pointer sin_margin">Seleccione Persona</span>
									<span id="sele_perso_fun" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
								</div>
							</div>
							<table id="tabla_asignaciones" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
								<thead class="ttitulo">
									<tr>
										<th class="nombre_tabla" colspan="2">TABLA <span id="nombre_tab"></span></th>
										<td class="sin-borde text-right border-left-none">
											<span type="button" class="btn btn-default add_asignacion oculto" style="margin: 10px 0 5px auto"><span class="fa fa-plus red"></span> Agregar</span>
										</td> 
									</tr>
									<tr class="filaprincipal">
										<td>Descripción</td>
										<td>Periodo</td>
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
					<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<form id="form_nueva_asignacion_indicador" method="post">
		<div class="modal fade" id="modal_nueva_asignacion_indicador" role="dialog">
			<div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-bar-chart"></span> Nueva Asignación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" required="true" name='periodo_indicador'>
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
							<!-- <div class="col-md-12" style="padding: 0px;">
								<select name="tipo_pregunta_ind" class="form-control cbxvalorz" title="Tipo de Respuesta"><option value="">Seleccione Tipo de Pregunta</option></select>
							</div> -->
							<div class="col-md-12" style="padding: 0px;">
								<select name="tipo_meta_ind" class="form-control cbxmeta" title="Tipo de Meta"><option value="">Seleccione Tipo de Meta</option></select>
							</div>
              <div class="col-md-12 agro agrupado" style="padding: 0px;">
								<div class="input-group">
									<input type="text" class="form-control input_numerico" required="true" name='meta_indicador'>
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-bar-chart red'></span> Meta</span>
								</div>
							</div>
							<div class="col-md-12" style="padding: 0px;">
								<textarea name="descripcion_ind" class="form-control" rows="5" placeholder="Descripción del Indicador"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<form id="form_nueva_asignacion" method="post">
		<div class="modal fade" id="modal_nueva_asignacion" role="dialog">
			<div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header" id="headermodal">
						<button type="button" class="close" data-dismiss="modal"> X</button>
						<h3 class="modal-title"><span class="fa fa-question"></span> Nueva Asignación</h3>
					</div>
					<div class="modal-body" id="bodymodal">
						<div class="row">
							<div class="agro agrupado">
								<div class="input-group">
									<input type="text" class="form-control" required="true" name='periodo_fun'>
									<span class="input-group-addon" style='background-color:white'><span class='fa fa-calendar red'></span> PERIODO</span>
								</div>
							</div>
              <div class="col-md-12" style="padding: 0px;">
								<select name="tipo_pregunta_fun" class="form-control cbxvalorz" title="Tipo de Respuesta"><option value="">Seleccione Tipo de Pregunta</option></select>
							</div>
              <div class="col-md-12 formacion" style="padding: 0px;">
                <div class="agro agrupado conv_div_padre" style="margin-top:4px;">
                  <div class="funkyradio contrataciones formacion_escencial" style="width: 100%;">
                    <!-- <div class="funkyradio-success" style="display: inline-block;width:48%;">
                      <input type="radio" id="form_si" name="formacion_es" value="1">
                      <label for="form_si" title="Con Formación"> SI</label>
                    </div>
                    <div class="funkyradio-danger" style="display: inline-block;width: 48%;">
                      <input type="radio" id="form_no" name="formacion_es" value="0">
                      <label for="form_no" title="Sin Formación"> NO</label>
                    </div> -->
                  </div>
                </div>
              </div>
							<div class="col-md-12" style="padding: 0px;">
								<textarea name="descripcion_fun" class="form-control" rows="5" placeholder="Descripción"></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer" id="footermodal">
						<button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
						<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</form>

<form  id="form_administrar"  method="post">
  <div class="modal fade scroll-modal" id="modal_administrar" role="dialog">
    <div class="modal-dialog modal-95">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Módulo</h3>
        </div>
        <div class="modal-body">
          <nav class="navbar navbar-default" id="menu_administrar">
            <div class="container-fluid">
              <ul class="nav nav-tabs nav-justified">
                <li class="pointer btn_planformacion"><a><span class="fa fa-address-book red"></span> Plan de
                    Formación</a></li>
                <li class="pointer btn_oferta"><a><span class="fa fa-user-plus red"></span> Oferta de Entrenamiento</a></li>
                <li class="pointer btn_horas_formacion"><a><span class="fa fa-calendar red"></span> Horas Formación</a>
                </li>
                <li class="pointer btn_encuesta"><a><span class="fa fa-question red"></span> Encuesta</a></li>
                <li class="pointer btn_notificacion"><a><span class="fa fa-send red"></span> Notificaciones</a></li>
              </ul>
            </div>
          </nav>

          <div class="planformacion adm_proceso active row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_pformacion"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA PLAN DE FORMACION</td>
                    <td class="sin-borde text-right border-left-none">
                      <span class="btn btn-default btn_filtrar_formacion"><span class="fa fa-filter red"></span>Filtrar</span>
                      <span class="btn btn-default btn_nuevo_planformacion"><span class="fa fa-address-book red"></span> Nuevo</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Ver</td>
                    <td>Tema</td>
                    <!-- <td>Facilitador</td>
                    <td>Duracion</td>
                    <td>Lugar</td> -->
                    <td>Fecha Inicio</td>
                    <td>Fecha Cierre</td>
                    <td>Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <div class="ofertaEntrenamiento adm_proceso active row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_ofertaEntrenamiento" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA OFERTA DE ENTRENAMIENTO</td>
                    <td class="sin-borde text-right border-left-none">
                      <span class="btn btn-default btn_nueva_oferta"><span class="fa fa-user-plus red"></span> Nuevo</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Tema</td>
                    <td>Adscrito</td>
                    <td>Departamento</td>
                    <td>Área Especifica</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <div class="hora_formacion adm_proceso oculto row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table id="tabla_horas_formacion" class="table table-bordered table-hover table-condensed" cellspacing="0"
                width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="3">TABLA HORAS FORMACIÓN</th>
                  </tr>
                  <tr class="filaprincipal">
                    <td></td>
                    <td>Puntaje</td>
                    <td>Horas</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="preguntas adm_proceso oculto row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table id="tabla_preguntas" class="table table-bordered table-hover table-condensed" cellspacing="0"
                width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="2">TABLA PREGUNTAS</th>
                    <td class="sin-borde text-right border-left-none" colspan="3">
                      <span class="btn btn-default add_pregunta"><span class="fa fa-question red"></span> Agregar</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Casificación</td>
                    <td>Pregunta</td>
                    <td>Tipo de Respuesta</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="notificaciones adm_proceso oculto row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display: flex;justify-content: center;align-items: center;">
              <div class="col-md-6">
                <div class="form-group agrupado text-center">
                  <label for="pension">Ingrese correos Electrónico de los Responsables de TH para notificar los entrenamientos finalizados. Utilice PUNTO Y COMA (;) para separarlos.</label>
                  <div class="input-group">
                    <input type="text" id="txt_correo_responsable" name="correos_th" class="form-control text-center" placeholder="Correos Electrónico Responsable">
                    <span class="input-group-addon"><strong>@</strong></span>
                  </div>
                </div>
              </div> 
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
           <button id="btnguardar_config" type="submit" class="btn btn-danger active oculto"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>  

  <div class="modal fade" id="modal_permisos" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-cog"></span> Permisos</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div  id="permisos" style="width: 100%;">
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
                  <tr><td  class="nombre_tabla" colspan="4">TABLA PROCESOS</td></tr>
                  <tr class="filaprincipal ">
                    <td  class="opciones_tbl">No.</td>
                    <td>Nombre</td>
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

  <div class="modal fade" id="modal_filtrar_formacion" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_filtro_formacion">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-filter"></span> Filtrar Formaciòn</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" name='text_filtro' placeholder="Filtrar por Facilitador ó Tema">
                  <span class="input-group-addon" style='background-color:white'><span class='fa fa-user red'></span> Facilitador/Tema</span>
                </div>
              </div>
              <select name="filtro_id_lugar" class="form-control cbxlugares">
                <option value="">Filtrar por Lugar</option>
              </select>
              <div class="col-md-6 sol-sm-12" style="padding: 0 0;">
                <input class="form-control" type="date" name="fecha_i">
              </div>
              <div class="col-md-6 sol-sm-12" style="padding: 0 0;">
                <input class="form-control" type="date" name="fecha_f">
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-filter"></span> Generar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_detalle_formacion" role="dialog">
    <div class="modal-dialog modal-80">
		<!-- Modal content-->
		<div class="modal-content" >
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-users"></span> Detalle Formación</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed">
						<tr>
							<th class="nombre_tabla" colspan="4"> Informacion del Plan de Formación</th>
						</tr>
						<tr>
							<td class="ttitulo">Facilitador: </td><td><span class="info_facilitador"></span></td>
              <td class="ttitulo">Tema:</td><td class="info_tema"></td>
						</tr>
						<tr>
							<td class="ttitulo">Lugar:</td><td class="info_lugar"></td>
							<td class="ttitulo">Duración(Horas):</td><td class="info_duracion"></td>
            </tr>
            <tr>
              <td class="ttitulo">Fecha Inicio:</td><td class="info_fecha"></td>
              <td class="ttitulo">Fecha Cierre:</td><td class="info_fecha_cierre"></td>
						</tr>
					</table>
				</div>
				<div class="table-responsive">
					<table id="tabla_competencias_formacion" class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%">
						<thead class="ttitulo ">
							<tr><td colspan="3" class="nombre_tabla">TABLA COMPETENCIAS</td></tr>
							<tr class="filaprincipal">
								<td class="opciones_tbl">No.</td>
								<td>Nombre</td>
								<td>Descripción</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
				<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
			</div>
		</div>
    </div>
</div>

  <div class="modal fade" id="modal_nuevo_valor_parametro" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_valor_parametro" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-question"></span> Agregar Pregunta</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <select name="id_clasificacion" class="form-control cbxclasificacion" required="true">
                <option value="">Seleccione Clasificación</option>
              </select>
              <textarea class="form-control" required="true" name="pregunta" placeholder="Pregunta"></textarea>
              <select name="id_tipo_respuesta" class="form-control cbxtipo_respuesta" required="true">
                <option value="">Seleccione Tipo Respuesta</option>
              </select>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-save"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_nuevo_planformacion" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_planformacion" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-address-book"></span> Nuevo Plan de Formación</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name='funcionario'
                    placeholder="Nombre y Apellido" />
                  <span class="input-group-addon" style='background-color:white'><span class='fa fa-user red'></span>
                    Facilitador</span>
                </div>
              </div>
              <select name="id_lugar" class="form-control cbxlugares" required="true">
                <option value="">Seleccione Lugar</option>
              </select>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name="tema" placeholder="Tema" />
                  <span class="input-group-addon" style='background-color:white'><span class='fa fa-edit red'></span>
                    Tema</span>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="number" class="form-control" required="true" name='duracion'
                    placeholder="Duración en Horas" />
                  <span class="input-group-addon" style='background-color:white'><span
                      class='fa fa-calendar red'></span> Duración</span>
                </div>
              </div>
              <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy"
                data-link-field="dtp_input1">
                <label for="fecha_formacion"></label>
                <input class="form-control sin_focus pointer" size="16" placeholder="Fecha entrenamiento" type="text"
                  value="" required="true" name="fecha_formacion" maxlength="99" title="Fecha Formación"
                  data-toggle="popover" data-trigger="hover">
                <span class="input-group-addon pointer">
                  <span class="glyphicon glyphicon-remove"></span>
                </span>
                <span class="input-group-addon pointer">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-save"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>


  <div class="modal fade" id="modal_nuevo_oferta_entrenamiento" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_oferta_entrenamiento" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-user-plus"></span> <span id="nombre_modal">Nueva Oferta de Entrenamiento</span></h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name="tema" placeholder="Tema" />
                  <span class="input-group-addon" style='background-color:white'><span class='fa fa-edit red'></span> Tema</span>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name='text_depto_adscrito' placeholder="Seleccione Departamento Adscrito" />
                  <span class="input-group-addon add_dept_adscrito pointer" style='background-color:white'><span class='fa fa-search red'></span> Vicerrectoria</span>
                  <input type="hidden" name="dept_adscrito"/>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name='text_departamento' placeholder="Seleccione Departamento" />
                  <span class="input-group-addon add_departamento pointer" style='background-color:white'><span class='fa fa-search red'></span> Departamento</span>
                  <input type="hidden" name="departamento"/>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name='text_area_especifica' placeholder="Seleccione Área" />
                  <span class="input-group-addon add_area_especifica pointer" style='background-color:white'><span class='fa fa-search red'></span> Área especifica</span>
                  <input type="hidden" name="area_especifica"/>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-save"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_buscar_departamento" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" id="headermodal">
				<button type="button" class="close" data-dismiss="modal"> X</button>
				<h3 class="modal-title"><span class="fa fa-search"></span> Buscar Programa/Dependencia</h3>
			</div>
			<div class="modal-body" id="bodymodal">
				<div class="row" id="" style="width: 100%">
					<div class="form-group agrupado col-md-8 text-left">
						<form  id="form_buscar_departamento"  method="post">
							<div class="input-group">
								<input name="departamento" class="form-control" placeholder="Ingrese nombre del programa o dependencia">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<span class='fa fa-search red'></span> Buscar
									</button>
								</span>
							</div>
						</form>
					</div>
					<div class="table-responsive col-md-12" style="width: 100%">
						<table class="table table-bordered table-hover table-condensed pointer" id="tabla_dependencia" cellspacing="0" width="100%">
							<thead class="ttitulo ">
								<tr class="">
									<td colspan="3" class="nombre_tabla">TABLA DEPENDENCIAS</td>
								</tr>
								<tr class="filaprincipal">
									<td>#</td>
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
          
  <div class="modal fade" id="modal_buscar_competencia" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Competencias</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <table id="tabla_buscar_competencias"
            class="table table-bordered table-hover table-condensed table-responsive" cellspacing="0" width="100%"
            style="margin-top: 40px;">
            <thead class="ttitulo ">
              <tr>
                <td colspan="4" class="nombre_tabla">TABLA COMPETENCIAS</td>
              </tr>
              <tr class="filaprincipal">
                <td>Nombre</td>
                <td>Descripción</td>
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

  <div class="modal fade con-scroll-modal" id="modal_listar_plan_entrenamiento" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-cogs"></span> Plan de Entrenamiento</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" style="margin:0px;width:100%;">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_entrenamiento"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="2" class="nombre_tabla">TABLA PLAN DE ENTRENAMIENTO</td>
                    <td colspan="4" class="sin-borde text-right border-left-none">
                      <span class="btn btn-default btn_ver_actas"><span class="fa fa-list red"></span>
                        Ver Actas</span>
                      <span class="btn btn-default btn_send_acta"><span class="fa fa-send red"></span>
                        Enviar Acta</span>
                      <span class="btn btn-default btn_enviar_entrenamiento"><span class="fa fa-send red"></span>
                        Enviar Entrenamiento</span>
                      <span class="btn btn-default btn_nuevo_entrenamiento"><span class="fa fa-edit red"></span>
                        Nuevo</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Oferta Entrenamiento</td>
                    <td>Facilitador</td>
                    <td>Duracion</td>
                    <td>Lugar</td>
                    <td>Fecha</td>
                    <td class="">Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
          <div style="margin-left: 5px;">
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_actas_cargo" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Actas de Aceptación Cargo</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <table class="table table-bordered table-hover table-condensed " id="tabla_actas_cargo" cellspacing="0" width="100%">
            <thead class="">
              <tr>
                <td colspan="4" class="nombre_tabla">TABLA ACTAS</td>
              </tr>
              <tr class="filaprincipal">
                <td>No.</td>
                <td>Cargo</td>
                <td>Fecha Entrega</td>
                <td>Fecha Recibido</td>
                <td>Acción</td>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_enviar_acta_cargo" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_acta_cargo" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-address-book"></span> Acta Aceptación del Cargo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <h4 class="text-left">¿Requiere firma del jefe inmediato?</h4>
                <div class="funkyradio facturacion" >
                  <div class="funkyradio-success">
                    <input type="radio" id="rd_vb_a" name="req_firma" value="1" checked>
                    <label for="rd_vb_a"> SI</label>
                  </div>
                  <div class="funkyradio-danger">
                    <input type="radio" id="rd_vb_d" name="req_firma" value="0">
                    <label for="rd_vb_d"> NO</label>
                  </div>
                </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="nombre_jefe" class="form-control" required="true" placeholder="Jefe inmediato">
                  <span class="input-group-addon buscar_jefe pointer" style='background-color:white'><span class='fa fa-search red'></span> Jefe Inmediato</span>
                  <input type="hidden" name="id_jefe"/>
                </div>
              </div>
							<div class="agro agrupado">
                <div class="input-group adicional_info" style="padding-top: 6px;">
                  <input type="hidden" name="cargo_id">
                  <input type="text" name="nombre_cargo" class="form-control" required="true" placeholder="Cargo SAP">
                  <span class="input-group-addon buscar_cargo pointer" style='background-color:white'><span class='fa fa-user red'></span> Cargo SAP</span>
                </div>
					    </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name='codigo_cargo' placeholder="Código del Cargo" />
                  <span class="input-group-addon" style='background-color:white'><span
                      class='fa fa-user red'></span> Código del cargo</span>
                </div>
              </div>              
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-send"></span> Enviar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_nuevo_planentrenamiento" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_planentrenamiento" method="post">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-address-book"></span> Nuevo Plan de Entrenamiento</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="col-md-12 input-group" style="margin-bottom: 3px;">
                <input type="text" class="form-control sin_margin sin_focus pointer input_buscar_persona"
                  name="id_evaluado" placeholder="Buscar Facilitador" required readonly>
                <span class="input-group-addon pointer input_buscar_persona" style='background-color:white'>
                  <span class='fa fa-search red'></span> Buscar
                </span>
              </div>
              <div class="agro agrupado text-left">
                <div class="alert alert-info" role="alert">
                  <p><b class="ttitulo">Datos del Facilitador:</b></p>
                  <ul>
                    <li>Nombre: <span class="nombre_completo"></span></li>
                    <li>Identificación: <span class="identificacion"></span></li>
                  </ul>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" name="oferta" class="form-control" required="true" placeholder="Seleccione oferta de entrenamiento">
                  <span class="input-group-addon buscar_oferta pointer" style='background-color:white'><span class='fa fa-search red'></span> Buscar</span>
                  <input type="hidden" name="id_oferta"/>
                </div>
              </div>
              <!-- <select name="id_oferta" class="form-control cbxoferta" required="true">
                <option value="">Seleccione Oferta de Entrenamiento </option>
              </select> -->
              <select name="id_lugar" class="form-control cbxlugares" required="true">
                <option value="">Seleccione Lugar</option>
              </select>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="number" class="form-control" required="true" name='duracion'
                    placeholder="Duración en Horas" />
                  <span class="input-group-addon" style='background-color:white'><span
                      class='fa fa-calendar red'></span> Duración</span>
                </div>
              </div>
              <div class="input-group date form_datetime agro" data-date="" data-date-format="dd MM yyyy"
                data-link-field="dtp_input1">
                <label for="fecha_formacion"></label>
                <input class="form-control sin_focus pointer" size="16" placeholder="Fecha Entrenamiento" type="text"
                  value="" required="true" name="fecha_entrenamiento" maxlength="99" title="Fecha Entrenamiento"
                  data-toggle="popover" data-trigger="hover">
                <span class="input-group-addon pointer">
                  <span class="glyphicon glyphicon-remove"></span>
                </span>
                <span class="input-group-addon pointer">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" required="true" name="link_reunion"
                    placeholder="LInk de la reunión" />
                  <span class="input-group-addon" style='background-color:white'><span class='fa fa-link red'></span>
                    Link</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-save"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_buscar_oferta" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Oferta</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <form  id="form_buscar_oferta"  method="post">
                <div class="input-group">
                  <input id="txt_buscar_oferta" class="form-control" placeholder="Ingrese nombre, departamento o Área">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                      <span class='fa fa-search red'></span> Buscar
                    </button>
                  </span>
                </div>
              </form>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_ofertas" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="3" class="nombre_tabla">TABLA OFERTAS DE ENTRENAMIENTO</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Nombre</td>
                    <td>Vicerrectoria</td>
                    <td>Departamento</td>
                    <td>Área</td>
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

  <div class="modal fade" id="Modal_filtro" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form id="form_filtro">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-filter"></span> Crear Filtros</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control" name='filtro_periodo' placeholder="Filtrar por periodo">
                  <span class="input-group-addon" style='background-color:white'><span
                      class='fa fa-calendar red'></span> PERIODO</span>
                </div>
              </div>
              <div class="col-md-6 sol-sm-12" style="padding: 0 0;">
                <input class="form-control filtro" type="date" name="fecha_i">
              </div>
              <div class="col-md-6 sol-sm-12" style="padding: 0 0;">
                <input class="form-control filtro" type="date" name="fecha_f">
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-filter"></span> Generar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal_detalle_evaluacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-users"></span> Detalle Solicitud</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="alert alert-info oculto text-left" role="alert" id="info_confirmacion">
            <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
            <p>Esta persona marco como recibido el acta de retroalimentación. Para ver su firma, <a
                href="<?php echo base_url()?>" target="_blank" id="btnfirma"><b>haga click aquí!.</b></a></p>
            <p><b>Observaciones</b>: <span class="observacion"></span>
            <p>
            <p><b>Calificación</b>: <span class="calificacion"></span>&#9733;</p>
          </div>
          <table class="table table-bordered table-condensed">
            <tr>
              <th class="nombre_tabla" colspan="2"> Información de la Solicitud</th>
              <td colspan="2" class="sin-borde text-right border-left-none">
                <span class="btn btn-default btn_ver_resultado"><span class="fa fa-eye red"></span> Resultados</span>
                <a href="<?php echo base_url()?>" target="_blank" class="btn btn-default btn_ver_acta"
                  id="btnActa"><span class="fa fa-folder-open-o red"></span> Retroalimentación</a>
              </td>
            </tr>
            <tr>
              <td class="ttitulo">Funcionario: </td>
              <td colspan='3'><span class="info_funcionario"></span></td>
            </tr>
            <tr>
              <td class="ttitulo">Identificación: </td>
              <td class="info_identificacion"></td>
              <td class="ttitulo">Fecha de Solicitud:</td>
              <td class="info_fecha"></td>
            </tr>
            <tr>
              <!-- <td class="ttitulo">Dependencia:</td><td class="info_dependencia"></td> -->
              <td class="ttitulo">Cargo:</td>
              <td class="info_cargo" colspan='3'></td>
            </tr>
            <tr>
              <td class="ttitulo">Método Evaluación:</td>
              <td class="info_metodo"></td>
              <td class="ttitulo">Estado:</td>
              <td class="info_estado"></td>
            </tr>
            <tr>
              <td class="ttitulo">Periodo:</td>
              <td class="info_periodo" colspan='3'></td>
            </tr>
            <tr>
              <td class="ttitulo">Puntuación Directa:</td>
              <td class="puntuacion_directa"></td>
              <td class="ttitulo">Puntuación Centil:</td>
              <td class="puntuacion_centil"></td>
            </tr>
            <tr>
              <td class="ttitulo">Valoración: </td>
              <td colspan='3'><span class="info_valoracion"></span></td>
            </tr>
          </table>
        </div>
        <div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
          <div style="margin-left: 5px;">
            <button type="button" class="btn btn-default active" data-dismiss="modal">
              <span class="glyphicon glyphicon-resize-small"></span> Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade scroll-modal" id="modal_resultados" role="dialog">
    <div class="modal-dialog modal-95">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Resultados Evaluación</h3>
        </div>
        <div class="modal-body">
          <nav class="navbar navbar-default" id="menu_resultados">
            <div class="container-fluid">
              <ul class="nav nav-tabs nav-justified">
                <li class="pointer detalles active"><a><span class="fa fa-list red"></span> Area Apreciación</a></li>
                <li class="pointer tipoEvaluador"><a><span class="fa fa-list red"></span> Tipo Evaluador</a></li>
              </ul>
            </div>
          </nav>

          <div class="resultado_detalle active row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table id="tabla_resultado_detalles" class="table table-bordered table-hover table-condensed"
                cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="8">TABLA PREGUNTAS</th>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Evaluador</td>
                    <td>Identificación</td>
                    <td>Area Apreciación</td>
                    <td>Acumulado</td>
                    <td>Total Preguntas</td>
                    <td>Promedio</td>
                    <td>% Apreciación</td>
                    <td>% Evaluador</td>
                    <td>Resultado</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="resultado_tevaluador oculto row" style="margin:0px;width:100%;">
            <div class="col-md-12" style="display:flex;justify-content:flex-end;flex-flow: wrap column;">
              <table id="tabla_resultados_tevaluador" class="table table-bordered table-hover table-condensed"
                cellspacing="0" width="100%">
                <thead class="ttitulo">
                  <tr>
                    <th class="nombre_tabla" colspan="4">TABLA PREGUNTAS</th>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Evaluador</td>
                    <td>%</td>
                    <td>Acumulado</td>
                    <td>Producto</td>
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

  <div class="modal fade con-scroll-modal" id="modal_plan_formacion_masivo" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Formación por Competencias</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" style="margin:0px;width:100%;">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_planformacion_masivo" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="6" class="nombre_tabla">TABLA COMPETENCIAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Nombre</td>
                    <td>Competencia</td>
                    <td>Observaciones</td>
                    <td class="opciones_tbl_btn">Puntaje</td>
                    <td class="opciones_tbl_btn">Horas</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
          <div style="margin-left: 5px;">
            <button type="button" class="btn btn-danger active" id="btn_guardar_planformacion_masivo"><span
                class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade con-scroll-modal" id="modal_plan_formacion" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Formación por Competencias</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" style="margin:0px;width:100%;">
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_planformacion"
                cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="6" class="nombre_tabla">TABLA COMPETENCIAS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td>Competencia</td>
                    <td>Observaciones</td>
                    <td class="opciones_tbl_btn">Puntaje</td>
                    <td class="opciones_tbl_btn">Horas</td>
                    <td class="opciones_tbl_btn">Acción</td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal" style="display: flex; justify-content: flex-end">
          <div style="margin-left: 5px;">
            <button type="button" class="btn btn-danger active" id="btn_calcular_planformacion"><span
                class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span
                class="glyphicon glyphicon-resize-small"></span> Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_detalle_planformacion" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-address-book"></span> Detalle Plan de Formación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_pformacion_persona"
              cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="7" class="nombre_tabla">TABLA PLAN DE FORMACION</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Tema</td>
                  <td>Facilitador</td>
                  <td>Duracion</td>
                  <td>Lugar</td>
                  <td>Fecha</td>
                  <td>Competencia</td>
                  <td>Asistencia</td>
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

  <div class="modal fade" id="modal_avalar_soportes_plan_formacion" role="dialog">
    <div class="modal-dialog modal-95">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Avalar Soportes</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <table class="table table-bordered table-hover table-condensed " id="tabla_avalar_soportes_plan_formacion"
            cellspacing="0" width="100%">
            <thead class="">
              <tr>
                <td colspan="5" class="nombre_tabla">TABLA SOPORTES PLAN FORMACIÓN</td>
                <td class="sin-borde text-right border-left-none" colspan="3">
                  <span class="btn btn-default btn_aprobar_todo"><span class="fa fa-thumbs-up" style="color: #5cb85c"></span> Aprobar Todos</span>
                </td>
              </tr>
              <tr class="filaprincipal">
                <td>Ver</td>
                <td>Competencia</td>
                <td>Nombre Formación</td>
                <td>Fecha</td>
                <td>Horas</td>
                <td class="opciones_tbl_btn">Acción</td>
              </tr>
            </thead>
            <tbody></tbody>
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
                  <input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                      <span class='fa fa-search red'></span> Buscar
                    </button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda"
                  cellspacing="0" width="100%">
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
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal">
              <span class="glyphicon glyphicon-resize-small"></span> Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <form id="form_buscar_cargo" method="post">
    <div class="modal fade" id="modal_buscar_cargo" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Cargo</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="form-group agrupado col-md-8 text-left">
                <div class="input-group">
                  <input id='txt_cargo_buscar' class="form-control" placeholder="Ingrese nombre">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">
                      <span class='fa fa-search red'></span> Buscar
                    </button>
                  </span>
                </div>
              </div>
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_cargo_busqueda"
                  cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr class="">
                      <td colspan="4" class="nombre_tabla">TABLA CARGOS</td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>No.</td>
                      <td>Nombre</td>
                      <td>Descripción</td>
                      <td class="opciones_tbl_btn">Acción</td>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal">
              <span class="glyphicon glyphicon-resize-small"></span> Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>


</div>

<script>
$(document).ready(function() {
  mostrar_notificaciones();
  num_notificaciones_general();
  inactivityTime();
  listar_personas('', <?php echo $id ?>);
  Cargar_parametro_buscado_aux(214, ".cbxtipo", "Filtrar por Método de Evaluación");
  Cargar_parametro_buscado_aux(221, ".cbxestado", "Filtrar por Estado");
  Cargar_parametro_buscado_aux(221, ".id_estado", "Seleccione Estado");
  Cargar_parametro_buscado(115, ".cbxlugares", "Seleccione Lugar");
  Cargar_parametro_buscado(219, ".cbxtipo_respuesta", "Seleccione Tipo Respuesta");
  Cargar_parametro_buscado(242, ".cbxclasificacion", "Seleccione Clasificación");
  Cargar_parametro_buscado(219, ".cbxvalorz", "Seleccione Tipo de Pregunta");
  Cargar_parametro_buscado(251, ".cbxmeta", "Seleccione Tipo de Meta");
});

$(".form_datetime").datetimepicker({
  language: 'es',
  format: 'yyyy-mm-dd hh:ii',
  autoclose: true,
  initialDate: new Date(),
  // startDate: new Date(),
  minuteStep: 30,
  daysOfWeekDisabled: [0, 6],
});
</script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>