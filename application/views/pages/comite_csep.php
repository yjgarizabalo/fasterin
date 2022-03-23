<div class="tablausu col-md-12 text-left div_table" id="container_comite">
		<div class="table-responsive">
			<p class="titulo_menu pointer" id='inicio_return'><span class="fa fa-reply-all naraja"></span> Regresar</p>
			<table class="table table-bordered table-hover table-condensed" id="tabla_comite" cellspacing="0" width="100%">
				<thead class="ttitulo ">
					<tr>
						<td colspan="3" class="nombre_tabla">TABLA COMITÉ</td>
						<td colspan="3"class="sin-borde text-right border-left-none"> 
							<span class="btn btn-default" id="btn_notificaciones">
								<span class="n_notificaciones red">0</span> Notificaciones
							</span>
							<span class="black-color pointer btn btn-default" id="limpiar_filtros_comite" >
								<span class="fa fa-refresh red"></span> Limpiar
							</span>
						</td>
					</tr>
					<tr class="filaprincipal ">
						<td class="opciones_tbl">Ver</td>
						<td>Nombre</td>
						<td>Descripción</td>
						<td>#Post.</td>
						<td>Estado</td>
						<td class="opciones_tbl_btn">Acción</td>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

  <div class="modal fade" id="modal_notificaciones" role="dialog">
  <div class="modal-dialog" >
      <!-- Modal content-->
      <div class="modal-content" >
          <div class="modal-header" id="headermodal">
              <button type="button" class="close" data-dismiss="modal"> X</button>
              <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones</h3>
          </div>
          <div class="modal-body" id="bodymodal" >
              <div id="panel_notificaciones_comite" style="width: 100%" class="list-group">
              </div>
              <div id="panel_notificaciones_generales" style="width: 100%" class="list-group">
              </div>
          </div>
          <div class="modal-footer" id="footermodal">
              <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="modal_detalle_solicitud" role="dialog">
  <div class="modal-dialog modal-lg modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle Solicitud</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div id='container_tabla_postulantes' class='table-responsive'>
            <table class="table table-bordered table-hover table-condensed " id="tabla_postulantes_csep"
              cellspacing="0" width="100%" style="">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="9" style="" class="nombre_tabla">TABLA POSTULANTES CSEP</td>
                  <td class="sin-borde text-center border-left-none"> <span class="black-color  btn btn-default" id="btn_aprobar_todo" ><span style="color: #39B23B;"  class="fa fa-check-square-o" ></span> Aprobar Todo</span></td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">ver</td>
                  <td>Tipo</td>
                  <td>Postulante</td>
                  <td>HV</td>
                  <td>Programa</td>
                  <td>Cargo</td>
                  <td>#Apr.</td>
                  <td>#Neg.</td>
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

<div class="modal fade" id="modal_detalle_postulante" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
           <div id='msj_tipo_cambio_sol' class='oculto'>
            <nav class="navbar navbar-default" id="nav_admin_contratos">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li class="pointer active" id="btn_ver_nuevo"><a><span class="fa fa-folder-open red"></span> Contrato Nuevo</a></li>
                  <li class="pointer" id="btn_ver_actual"><a><span class="fa fa-folder red"></span> Contrato Actual</a></li>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
          <div id="tabla_detalle_postulante" class="table-responsive">
            <table class="table text-center" id=>
              <tr class="nombre_tabla text-left">
                <td colspan="4">Datos Personales</td>
              </tr>
              <tr>
                <td class="hoja_vida" colspan ='2'></td>
                <td class="prueba_psicologia" colspan ='2'></td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre Completo</td>
                <td class="nombre_completo" colspan ='3'></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo identificación</td>
                <td class="tipo_identificacion"></td>
                <td class="ttitulo">Identificación</td>
                <td class="identificacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Nacimiento</td>
                <td class="fecha_nacimiento" ></td>
                <td class="ttitulo">Lugar Expedición</td>
                <td class="lugar_expedicion"></td>
              </tr>
              <tr class="nombre_tabla text-left tr_actuales">
                <td colspan="4">Datos Actuales</td>
              </tr>
              <tr class='tr_actuales'>
                <td class="ttitulo">Dependencia</td>
                <td class="dependencia_actual"></td>
                <td class="ttitulo">Cargo</td>
                <td class="cargo_actual"></td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan="4">Datos Nuevos</td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo</td>
                <td class="tipo" colspan ='3'></td>
              </tr>
              <tr class='tr_nuevos'>
                <td class="ttitulo">Procedencia</td>
                <td class="procedencia"></td>
                <td class="ttitulo">Formación</td>
                <td class="formacion"></td>
              </tr>
              <tr class='tr_nuevos'>
                <td class="ttitulo">Dependencia</td>
                <td class="dependencia"></td>
              </tr>
              <tr>
                <td class="ttitulo">Programa</td>
                <td class="programa" colspan ='3'></td>
              </tr>
              <tr class='tr_nuevos'>
                <td class="ttitulo">Cargo</td>
                <td class="cargo" colspan ='3'></td>
              </tr>
              <tr>
                <td class="ttitulo">Plan Trabajo</td>
                <td class="plan_trabajo" colspan ='3'></td>
              </tr>
              <tr class='tr_motivo'>
                <td class="ttitulo">Motivo</td>
                <td class="motivo"  colspan ='3'></td>
              </tr>
              <tr>
                <td class="ttitulo">Estado</td>
                <td class="estado" colspan ='3'></td>
              </tr>
              <tr class='tr_fechas'>
                <td class="ttitulo">Inicio Contrato</td>
                <td class="fecha_inicio_contrato"></td>
                <td class="ttitulo">Fin Contrato</td>
                <td class="fecha_fin_contrado"></td>
              </tr>
              <tr class=''>
                <td class="ttitulo">#Aprobados</td>
                <td class="vb"></td>
                <td class="ttitulo">#Negados</td>
                <td class="vm"></td>
              </tr>
            </table>
            <table class="table text-center">
              <tr class="nombre_tabla text-left">
                <td >Observaciones</td>
              </tr>
              <tr>
                <td class="observaciones"></td>
              </tr>
						</table>
						<table class="table table-bordered table-hover table-condensed" id="tabla_estados_csep" cellspacing="0" width="100%">
							<thead class="ttitulo">
								<tr>
									<th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
								</tr>
								<tr class="filaprincipal">
									<td>Nombre</td>
									<td>Fecha</td>
									<td>Usuario</td>
							</thead>
							<tbody>
							</tbody>
						</table>
            <div id='container_comentarios_generales'>
              <div  style="width: 100%" class="list-group margin1 text-left" id='panel_comentarios_generales'></div>  
              <form action="" id='form_guardar_comentario_general'>   
                <div class="input-group col-md-6">
                  <input type="text" class="form-control comentarios" placeholder="Nuevo Comentario" name='comentario'>
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Enviar!</button>
                  </span>
                </div>
              </form>
            </div>
          </div>

      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>


<form action="#" id="form_gestionar_postulante" method="post">
  <div class="modal fade" id="modal_gestionar_postulante" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-refresh"></span> Gestionar Postulante</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row">
              <select name="id_programa"  class="form-control  cbxprogramas"> </select> 
              <select  name = 'id_comite' class="form-control comites_combo" required="true"><option value="">Seleccione Comité</option> </select> 
              <textarea name="plan_trabajo" class="form-control" placeholder="Plan Trabajo" required="true"></textarea>
            </div>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="submit" class="btn btn-danger active"><span class="fa fa-check"></span>  Terminar</button>
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>

    </div>
  </div>
</form>


  <script>
    $(document).ready(function () {
			obtener_vista_llama('<?php echo $vista?>','<?php echo $_SESSION["perfil"]?>','<?php echo $id?>','<?php echo $_SERVER["REQUEST_URI"] ?>','<?php echo $_SESSION["persona"]?>');
			inactivityTime();
      listar_comites("(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter')");
    });
</script>
