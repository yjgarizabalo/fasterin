<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin") {
  $sw = true;
  $sw_super = true;
}
?>
<div id="menu">
</div>


<div class="container col-md-12 text-left" id="inicio-user">
  <div class="tablausu col-md-12">
    <div class="table-responsive text-left">
      <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <div class="form-group" style="width:100%">
        <div class="input-group col-md-4" style="float: right">
          <input class="form-control" id="txt_buscar_persona" value="" placeholder="Ingrese Nombre, Apellido, Usuario o Identificacion">
          <span class="input-group-addon pointer" title="Buscar Persona" data-toggle="popover" data-trigger="hover" id="btn_buscar_persona"><span class="glyphicon glyphicon-search"></span></span>
        </div>
      </div>
      <br><br>
      <table class="table table-bordered table-hover table-condensed" id="tablapersonas" cellspacing="0" width="100%" style="">
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="4" class="nombre_tabla">TABLA PERSONAS <br><span class="mensaje-filtro oculto" id='mensaje-filtro-evento'><span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.</span></td>
            <td colspan="5" class="sin-borde text-right border-left-none">
              <span class="btn btn-default btnAgregar " data-toggle="modal" data-target="#Registrar-persona">
                <span class="fa fa-plus red"></span> Nueva</span>
              <span class="btn btn-default btnModifica" id="btnmodificar_persona">
                <span class="fa fa-wrench red"></span> Modificar</span>

              <?php if ($sw) { ?>
                <span class="btn btn-default btnModifica" id="btnadministrar_perfiles"><span class="fa fa-eye red"></span>
                  Actividades</span>
                <span class="btn btn-default btnPerfiles" id="btnAsignar_perfiles"><span class="fa fa-users red"></span>
                  Perfiles</span>
              <?php } ?>

              <span class="btn btn-default btnImportar" id="btn_Importar_excel">
                <span class="fa fa-file-excel-o red"></span> Importar
              </span>

              <span class="btn btn-default btnFiltros" id="btn_aplicar_filtros">
                <span class="fa fa-filter red"></span> Filtrar
              </span>

            </td>
          </tr>

          <tr class="filaprincipal">
            <td class="opciones_tbl">Ver</td>
            <td>ID</td>
            <td>Nombre Completo</td>
            <td class="">identificación</td>
            <td>Cargos SAP</td>
            <td class="">Correo</td>
            <td>Usuario</td>
            <td>Perfil</td>
          </tr>
      
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>




<div class="modal fade" id="Registrar-persona" role="dialog">
  <div class="modal-dialog">
    <form id="form-ingresar-persona" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-floppy-disk"></span> Registro de Personas</h3>
        </div>
        <div class="modal-body " id="bodymodal">
          <div class="row">
            <h6 class="ttitulo"><span class="glyphicon glyphicon-download"></span> Buscar en Identidades</h6>
            <div class="input-group agrupado">
              <input class="form-control text-left sin_margin" id="dato_buscar_identidades" placeholder="Ingrese identificación de la persona">
              <span class="input-group-addon">
                <span id="Buscar_persona_identidades" class="glyphicon glyphicon-search red_primari pointer"></span>
              </span>
            </div>
            <h6 class="ttitulo">
              <span class="glyphicon glyphicon-indent-left"></span>
              Datos del Solicitante
            </h6>
            <select name="tipo_persona" class="form-control" disabled="disabled">
              <option>Empleado</option>
            </select>
            <select name="tipo_identificacion" id="cbxtipoIdentificacion" required class="form-control  cbxtipoIdentificacion">
            </select>
            <input min="1" type="number" name="identificacion" id="txtIdentificacion" class="form-control inputt" placeholder="No. Identificación" required>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="apellido" id="txtApellido" class="form-control inputt2" placeholder="Primer Apellido" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundoapellido" id="txtsegundoapellido" class="form-control inputt2" placeholder="Segundo Apellido" required>
              </div>
            </div>
            <div class="agro agrupado">
              <div class="input-group">
                <input type="text" name="nombre" id="txtNombre" class="form-control inputt2" placeholder="Primer Nombre" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundonombre" id="txtSegundoNombre" class="form-control inputt2" placeholder="Segundo Nombre">
              </div>
            </div>
            <div class='datos_internos'>



              <div class="agro">
                <div class="input-group adicional_info" style="padding-top: 6px;">

                  <input type="text" name="cargosSAP" class="form-control sin_margin sin_focus pointer" placeholder="Cargos SAP" id="cargosSAP">
                  <span class="input-group-addon pointer " id='btn_add_cargo' style='background-color:white'><span class='fa fa-search red'></span>Cargos SAP</span>

                </div>
              </div>


              <div class="agro">
                <div class="input-group">
                  <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Fecha inicio</span>
                  <input type="date" class="form-control sin_margin" name='fecha'>
                </div>
              </div>

              <div class="agro">
                <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="number" name="sueldo" id="sueldo" class="form-control inputt sin_margin" placeholder="Sueldo">
                  <span class="input-group-addon">-</span>
                  <input type="text" name="tipo_contrato" id="tipo_contrato" class="form-control inputt sin_margin" placeholder="Tipo de contrato">
                </div>
              </div>

              <div class="agro usuario">
                <div class="input-group">
                  <input type="email" name="correo" id="txtCorreo" class="form-control inputt sin_margin" placeholder="Correo Eléctronico">
                  <span class="input-group-addon">-</span>
                  <input type="text" name="usuario" id="txtusuario" class="form-control inputt2 sin_margin" placeholder="Usuario">
                </div>
              </div>
            </div>
            <select name="perfil" class="form-control" id="tipo_perfil">
              <option>Seleccione Perfil</option>
              <option value="0">Administrativos</option>
              <option value="1">Docentes</option>
            </select>
            <input min="1" type="number" name="celular" id="txtCelular" class="form-control" placeholder="Celular" required="">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="modal fade" id="Modificar_persona" role="dialog">
  <div class="modal-dialog">
    <form id="form-modificar-persona" enctype="multipart/form-data" method="post">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-wrench"></span> Modificar Persona</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="error text-center"></div>
          <div class="row divmodifica">
            <select name="tipo_persona" required class="form-control  cbxtipopersona" id="tipo_persona_id_modifica">
            </select>
            <select name="tipo_identificacion" required class="form-control  cbxtipoIdentificacion" id="cbxtipoIdentificacion_modifica">
            </select>
            <input min="1" type="number" name="identificacion" id="identificacion_modifica" class="form-control inputt" placeholder="No. Identificación" required>
            <div class="agro">
              <div class="input-group">
                <input type="text" name="nombre" id="nombre_modifica" class="form-control inputt2 sin_margin" placeholder="Primer Nombre" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundonombre" id="segundo_nombre_modifica" class="form-control inputt2 sin_margin" placeholder="Segundo Nombre">

              </div>
            </div>
            <div class="agro">
              <div class="input-group">
                <input type="text" name="apellido" id="apellido_modifica" class="form-control inputt2 sin_margin" placeholder="Primer Apellido" required>
                <span class="input-group-addon">-</span>
                <input type="text" name="segundoapellido" id="segundo_apellido_modifica" class="form-control inputt2 sin_margin" placeholder="Segundo Apellido" required>
              </div>
            </div>
            <div class='datos_internos_modi'>

              <div class="agro">
                <div class="input-group adicional_info" style="padding-top: 6px;">
                  <input type="text" name="cargosSAP" id="cargosSAP_modificar" class="form-control sin_margin sin_focus pointer" placeholder="Cargos SAP">
                  <span class="input-group-addon" id='btn_add_cargo' style='background-color:white'><span class='fa fa-search red'></span>Cargos SAP</span>
                </div>
              </div>

              <div class="agro agrupado">
                <div class="input-group">
                  <span class="input-group-addon" style=' background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
                  <input type="date" class="form-control sin_margin" name='fecha' id="fecha_modificar">
                </div>
              </div>

              <div class="agro">
                <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="number" name="sueldo" id="sueldo_modificar" class="form-control inputt sin_margin" placeholder="Sueldo">
                  <span class="input-group-addon">-</span>
                  <input type="text" name="tipo_contrato" id="tipo_contrato_modificar" class="form-control inputt sin_margin" placeholder="Tipo de Contrato">
                </div>
              </div>

              <div class="agro usuario">
                <div class="input-group">
                  <input type="email" name="correo" id="correo_modifica" class="form-control inputt sin_margin" placeholder="Correo Eléctronico">
                  <span class="input-group-addon">-</span>
                  <input type="text" name="usuario" id="usuario_modifica" class="form-control inputt2 sin_margin" placeholder="Usuario">
                </div>
              </div>
            </div>
            <select name="perfil_modificar" class="form-control" id="tipo_perfil_modificar">
              <option>Seleccione Perfil</option>
              <option value="0">Administrativos</option>
              <option value="1">Docentes</option>
            </select>

            <input min="1" type="number" name="celular" id="telefono_modifica" class="form-control inputt" placeholder="Celular">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span>
            Modificar</button>

          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if ($sw) { ?>
  <div class="modal fade" id="Perfiles_modal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-check"></span> Actividades Persona</h3>
        </div>
        <div class="modal-body" id="bodymodal">

          <form action="#" id="PerfilesUsuario" method="post">
            <div>
              <div class="input-group">
                <select name="idperfil" required class="selectpicker form-control sin_margin" data-live-search="true" id="cbxasignar_perfiles">
                  <option value="">Seleccione Actividad</option>
                </select>
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-default" type="button"><span class='fa fa-check red'></span>
                    Asignar</button>
                </span>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="tblPerfilesPersonas" cellspacing="0" width="100%" style="">
              <thead class="ttitulo ">
                <tr>
                  <td class="nombre_tabla">TABLA PERFILES</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre</td>
                  <td class="opciones_tbl">Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <!-- <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Asignar</button> -->
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>


<div class="modal fade" id="Mostrar_detalle_persona" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-user"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 80%">

          <div class="error text-center"></div>

          <div id="datos_perso" class="">
            <table class="table">
              <tr class="nombre_tabla">
                <td colspan="2">Datos</td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo Persona</td>
                <td id="tipo_persona_id"></td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre Completo</td>
                <td class="nombre_perso"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo identificación</td>
                <td class="tipo_id_perso"></td>
              </tr>
              <tr>
                <td class="ttitulo">identificación</td>
                <td class="identi_perso"></td>
              </tr>
              <tr>
                <td class="ttitulo">Cargo SAP</td>
                <td class="cargo_perso"></td>
              </tr>
              <?php if ($sw) { ?>
                <tr>
                  <td class="ttitulo">Perfil</td>
                  <td class="perfil_perso"></td>
                </tr>
              <?php } ?><tr>
              <tr>
                <td class="ttitulo">Celular</td>
                <td class="celular"></td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo Contrato</td>
                <td class="tipo_cont"></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Registro</td>
                <td class="fecha_registro"></td>
              </tr>
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

<div class="modal fade" id="modal_buscar_cargos" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">

        <button type="button" class="close" data-dismiss="modal"> X</button>

        <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Cargos SAP</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="form-group agrupado col-md-8 text-left">
            <form id="form_buscar_cargos" method="post">
              <div class="input-group">
                <input name="cargos" class="form-control" placeholder="Ingrese nombre o codigo del cargo">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit">
                    <span class='fa fa-search red'></span> Buscar
                  </button>
                </span>
              </div>
            </form>
          </div>
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_cargos_sap" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" class="nombre_tabla">TABLA CARGOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>ID</td>
                  <td>Cargo</td>
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

<?php if ($sw) { ?>
  <div class="modal fade" id="asignar_perfiles_modal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-check"></span> Perfiles Persona</h3>
        </div>
        <div class="modal-body" id="bodymodal">

          <form action="#" id="asignarPerfiles" method="post">
            <div>
              <div class="input-group">

                <select name="asignar_perfiles" required class="selectpicker form-control inputt" data-live-search="true" id="asignar_perfiles">
                  <option>Seleccione Perfil</option>
                </select>

                <span class="input-group-btn">
                  <button type="submit" class="btn btn-default" type="button"><span class='fa fa-check red'></span>
                    Asignar</button>
                </span>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-condensed" id="PerfilesPersonas" cellspacing="0" width="100%" style="">
              <thead class="ttitulo ">
                <tr>
                  <td class="nombre_tabla">PERFILES</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre</td>
                  <td class="opciones_tbl">Acción</td>
                  <td class="opciones_tbl">Pred</td>

                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">

          <!-- <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Asignar</button> -->
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

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
            <select name="id_tipo_per_select" id="id_tipo_per_select" class="form-control cbxtipopersonaFiltrar" style="margin-bottom: 1%;">
              <option value="">Filtrar por Tipo de Persona</option>
            </select>
            <select name="id_tipo_car_select" class="selectpicker form-control cbxcargosFiltrar" data-live-search="true" id="id_tipo_car_select" style="margin-left: 4%;">
              <option value="">Filtrar por Cargo SAP</option>
            </select>
            <select name="id_tipo_cont_select" class="form-control cbxtipo_contratoFiltrar" id="id_tipo_cont_select" style="margin-bottom: 1%;">
              <option value="">Filtrar por Tipo Contrato</option>
            </select>
            <select name="id_tipo_perf_select" class="selectpicker form-control cbx_perfilesFiltrar" data-live-search="true" id="id_tipo_perf_select">
              <option value="">Filtrar por Tipo Perfil</option>
            </select>
            <span class="mensaje-filtro" id='mensaje-fecha'><span class="fa fa-bell red"></span> Se filtrará por la Fecha en la que se registró la persona.</span>
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
          <button type="submit" class="btn btn-danger active" id="btn_filtrar"><span class="glyphicon glyphicon-ok"></span> Filtrar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="modal_cargar_excel" role="dialog">
  <form id="form_excel" method="post">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Importar Excel</h3>
        </div>
        <div>
          <span id="" class="btn btn-default" title="Subir Excel con Personas" data-toggle='popover' data-trigger='hover'>

            <div class="agrupado">
              <div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span>Buscar <input style="display: none;" type="file" id="xFile" name="" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="importarExcel(this)"> </span></label><input type="text" class="form-control" readonly placeholder='Personas'></div>
            </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="modal_importar_excel" role="dialog">
  <div class="modal-dialog modal-lg " style="width: 100%;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Importar Excel</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">


          <table class="table table-bordered table-hover table-condensed" id="tbl_excel" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>Identificación</td>
                <td>Tipo Identificación</td>
                <td>Primer Nombre</td>
                <td>Segundo Nombre</td>
                <td>Primer Apellido</td>
                <td>Segundo Apellido</td>
                <td>Cargos</td>
                <td>Fecha</td>
                <td>Clase Contrato</td>
                <td>Sueldo</td>
                <!-- <td>Correo</td> -->
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>

        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-danger active col-md-1.8" data-dismiss="modal" id="guardar_datos_excel"><span class="glyphicon glyphicon-ok"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade scroll-modal" id="resultado" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-file-excel-o"></span> <span> Resultados de Ingreso Excel</span></h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div id="seleccion_tipo">
          <div class="row" style="width:100%">

            <div class="pointer" id="btn_cargos">
              <div class="thumbnail">
                <div class="caption" style="text-align:center;">
                  <img src="<?php echo base_url() ?>/imagenes/Cargos_departamentos.png" alt="...">
                  <span class="btn form-control">Cargos Guardados</span>
                  <br>
                  <label id="labcargoResultado"></label>
                </div>
              </div>
            </div>

            <div class="pointer" id="btn_personasAdd">
              <div class="thumbnail">
                <div class="caption" style="text-align:center;">
                  <img src="<?php echo base_url() ?>/imagenes/ModificadaPersonas.png" alt="...">
                  <span class="btn form-control">Personas Almacenadas
                  </span>
                  <br>
                  <label id="labregistrado"></label>
                </div>
              </div>
            </div>

            <div class="pointer" id="btn_personas_noADD">
              <div class="thumbnail">
                <div class="caption" style="text-align:center;">
                  <img src="<?php echo base_url() ?>/imagenes/noModificadaPersonas.png" alt="...">
                  <span class="btn form-control">Personas no Almacenadas</span>
                  <br>
                  <label id="labno_registrado"></label>
                </div>
              </div>
            </div>

            <div class="pointer" id="btn_personasUpd">
              <div class="thumbnail">
                <div class="caption" style="text-align:center;">
                  <img src="<?php echo base_url() ?>/imagenes/ModificadaPersonas.png" alt="...">
                  <span class="btn form-control">Personas Actualizadas </span>
                  <br>
                  <label id="labactualizado"></label>
                </div>
              </div>
            </div>
            <div class="pointer" id="btn_personas_noUpd">
              <div class="thumbnail">
                <div class="caption" style="text-align:center;">
                  <img src="<?php echo base_url() ?>/imagenes/noModificadaPersonas.png" alt="...">
                  <span class="btn form-control">Personas no Actualizadas </span>
                  <br>
                  <label id="labno_actualizado"></label>
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


<div class="modal fade" id="cargos_guardados" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Cargos Guardados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed" id="tblcargos_guardados" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>No.</td>
                <td>Cargos Creados</td>
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

<div class="modal fade" id="personas_actualizadas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Personas Actualizadas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed" id="tblpersonas_actualizadas" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>Identificacion</td>
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Cargo</td>
                <td>Clase Contrato</td>
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

<div class="modal fade" id="personas_registradas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Personas Registradas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">

          <table class="table table-bordered table-hover table-condensed" id="tblpersonas_registradas" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>Identificacion</td>
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Cargo</td>
                <td>Clase Contrato</td>
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

<div class="modal fade" id="personas_no_registradas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Personas No Registradas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tblpersonas_no_registradas" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>Identificacion</td>
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Errores</td>
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


<div class="modal fade" id="personas_no_actualizadas" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="glyphicon glyphicon-upload"></span> Personas No Actualizadas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed" id="tblpersonas_no_actualizadas" cellspacing="0" width="100%" style="">
            <thead class="ttitulo ">
              <tr class="filaprincipal">
                <td>Identificacion</td>
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Errores</td>
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

<script>
  $(document).ready(function() {
    inactivityTime();
    listar_Personas("");
    Cargar_parametro_buscado(1, ".cbxtipoIdentificacion", "Seleccione Tipo identificación")
    //Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Departamento")
    Cargar_parametro_buscado(188, ".cbxcargos", "Seleccione Cargo");
    Cargar_parametro_buscado_aux(17, ".cbx_perfiles", "Seleccione Perfil");
    Cargar_parametro_buscado_aux(24, ".cbxtipopersona", "Seleccione Tipo Persona");
    Cargar_parametro_buscado_aux(65, ".cbxtipo_contrato", "Tipo de Contrato");

    Cargar_parametro_buscado_aux(24, ".cbxtipopersonaFiltrar", "Filtrar por Tipo de Persona");
    Cargar_parametro_buscado(188, ".cbxcargosFiltrar", "Filtrar por Cargo SAP");
    Cargar_parametro_buscado_aux(65, ".cbxtipo_contratoFiltrar", "Filtrar por Tipo Contrato");
    Cargar_parametro_buscado_aux(17, ".cbx_perfilesFiltrar", "Filtrar por Tipo Perfil");

  })
</script>