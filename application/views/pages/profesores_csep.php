<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
<?php $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_plan" || $_SESSION["perfil"] == "Per_Csep"? true: false;?>
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
      <table class="table table-bordered table-hover table-condensed" id="tabla_profesores" cellspacing="0" width="100%" >
        <thead class="ttitulo ">
          <tr class="">
            <td colspan="5" class="nombre_tabla">TABLA PROFESORES</td>
            <td colspan="3" class="sin-borde text-right border-left-none"> <?php if($administra){?> <a href="<?= base_url('/index.php/profesores/descargar_excel/actual') ?>" class="btn btn-default" title="Descargar Información" data-toggle='popover' data-trigger='hover' id='descargar_info'> <span class="fa fa-cloud-download red" ></span> Descargar</a> <span id="btn_administrar" class="btn btn-default" title="Administrar Módulo" data-toggle='popover' data-trigger='hover'> <span class="fa fa-cogs red" ></span> Administrar</span><span id="btn_subir" class="btn btn-default" title="Subir registros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-cloud-upload red" ></span> Subir</span><?php }?> <span class="btn btn-default"   data-toggle="modal" data-target="#modal_periodos"> <span class="fa fa-filter red" ></span> Filtrar</span></td>
          </tr>
          <tr class="filaprincipal">
            <td>Ver</td>
            <td>Periodo</td>
            <td>Nombre Completo</td>
            <td>identificación</td>
            <td>Programa</td>
            <td>Dedicación</td>
            <td>Estado</td>
            <td class="opciones_tbl_btn" style="min-width: 130px;">Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_detalle_profesor" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Información Completa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style='width : 100%'>
          <!--<button type="button" class="btn btn-danger" id='btn_imprimir_plan'><span  class="fa fa-print"></span> Imprimir</button>-->
          <a class="btn btn-default" id='btn_descargar_plan'><span  class="fa fa-download"></span> Descargar</a>
          <div id="detalle_profesor" class="">
            <h4 class='text-center'> EVALUACIÓN Y PLAN DE TRABAJO</h4>
            <table class="table">
              <tr class="nombre_tabla text-left">
                <td colspan='6'>Datos del profesor</td>
              </tr>
              <tr>
                  <td  rowspan="5" width="25%" ><img class='foto_profesor imagen_empleado_plan'  alt="Foto Empleado"></td>
              </tr>
              <tr>
                <td class="ttitulo">Nombre</td>
                <td class="nombre_completo" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">identificación</td>
                <td class="identificacion" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Dedicación</td>
                <td colspan='5' class="dedicacion"></td>
              </tr>
              <tr>
                <td class="ttitulo">Escalafon</td>
                <td colspan='5' class="escalafon"></td>
              </tr>
              <tr class="nombre_tabla text-left formacion">
                <td colspan='6'>Formación</td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan='6'>Contrato</td>
              </tr>
              <tr>
                <td class="ttitulo">Tipo</td>
                <td class="contrato" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Fecha Inicio</td>
                <td colspan='2' class="fecha_inicio"></td>
                <td class="ttitulo">Fecha Fin</td>
                <td colspan='2' class="fecha_fin"></td>
              </tr>
              <tr class="nombre_tabla text-left">
                <td colspan='6'>Detalle</td>
              </tr>
              <tr>
                <td class="ttitulo">Departamento</td>
                <td colspan='5' class="departamento"></td>
              </tr>
              <tr>
                <td class="ttitulo">Programa</td>
                <td colspan='5' class="programa"></td>
              </tr>
              <tr>
                <td class="ttitulo">Area de Conocimiento</td>
                <td class="area_conocimiento" colspan='5'></td>
              </tr>
              <tr>
                <td class="ttitulo">Grupo de Investigación</td>
                <td class="grupo" colspan='5'></td>
              </tr>
              <tr class="nombre_tabla text-left perfiles">
                <td colspan='6'>Plan de Trabajo</td>
              </tr>
              <tr class="nombre_tabla text-left horas_programas">
                <td colspan='6'>Horas por programa</td>
              </tr>
              <tr class="nombre_tabla text-left asignaturas">
                <td colspan='6'>Asignaturas</td>
              </tr>
              <tr class="nombre_tabla text-left atencion">
                <td colspan='6'>Horario de atención</td>
              </tr>
              <tr class="nombre_tabla text-left indicadores">
                <td colspan='6'>Indicadores</td>
              </tr>
              <tr class="nombre_tabla text-left lineas">
                <td colspan='6'>Lineas de Investigación</td>
              </tr>
              <tr class="nombre_tabla text-left objetivos">
                <td colspan='6'>Observaciones Generales</td>
              </tr>
            </table>
            <div class='col-md-12'>
              <h3>Se recomienda implementar en su quehacer diario las siguientes políticas:</h3>  
              <ol class='politicas'></ol>
              <h3>Notas: </h3>  
              <ol class='notas'></ol>
            </div>
            <div class='col-md-12 con_firmas'>
                <h4>Fecha de generación: <?php print_r(date("Y-m-d H:i:s"))?></h4>
                <h4>Fecha de firma: <span class="fecha_firma"></span></h4>
            </div>
            <div class='col-md-12 con_firmas'>
              <!-- <div class='espacio'>
                <hr class='firma_espacio'>
                <h4>Profesor</h4>
                <h4 class='nombre_completo'></h4>
              </div> -->
              <div id='conta_profesor'>
              </div>
              <div id='conta_directores'>
              </div>
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span  class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php  if($administra){?>
<div class="modal fade" id="modal_administrar_modulo" role="dialog">
  <div class="modal-dialog modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <div id="container_admin_parametros">
          <div id='div_valores' class='col-md-6' style='padding-left:0;'>
            <div class="input-group" >
              <select  name="parametro" class="form-control cbx_parametros sin_margin" id="cbx_listado_parametros"> <option value="">Seleccione Parametro</option> </select>  
              <span class="input-group-addon pointer btn btn-default" id="btn_nuevo_valor"><span class="fa fa-plus red"></span> Nuevo </span>
            </div>
            <br>
            <table class="table table-bordered table-hover table-condensed" id="tabla_valores_parametros" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA VALORES</td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Valor</td>
                  <td style='width:150px !important'>Acción</td>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div id='div_relaciones' class='col-md-6' style='padding-right:0'>
            <select  name="parametro"   class="form-control cbx_parametros" id="cbx_listado_parametros_r"> <option value="">Seleccione Parametro</option> </select> 
            <br>
            <table class="table table-bordered table-hover table-condensed" id="tabla_valores_parametros_r" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA RELACIONES</td>
                </tr>
                <tr class="filaprincipal ">
                  <td>Valor</td>
                  <td class="opciones_tbl_btn">Acción</td>
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

<div class="modal fade" id="modal_valor_parametro" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="form_valor_parametro" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo Valor</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="text" name="nombre" class="form-control comentarios" placeholder="Nombre" required>
                        <textarea class="form-control comentarios oculto"  cols="1" rows="3" name="descripcion" placeholder="Descripcion" ></textarea>
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

<div class="modal fade" id="modal_modificar_valor" role="dialog">
  <div class="modal-dialog">
    <form action="#" id="form_modificar_valor" method="post">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-edit"></span>  Modificar Valor</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                  <div class="row">
                    <input type="text" name="nombre" class="form-control comentarios" placeholder="Nombre" required>
                    <textarea class="form-control comentarios oculto"  cols="1" rows="3" name="descripcion" placeholder="Descripcion" ></textarea>
                </div> 
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="submit" class="btn btn-danger active btnModifica" ><span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_persona_parametro" role="dialog">
    <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-user"></span> Asignar Persona</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                  <div class="table-responsive" style="width: 100%">
                    <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_parametro" cellspacing="0" width="100%">
                      <thead class="ttitulo ">
                        <tr class="">
                          <td colspan="3" class="nombre_tabla">TABLA PERSONA</td>
                          <td class="sin-borde text-right border-left-none">
                            <span  class="btn btn-default btnAgregar" id="btn_buscar_persona_parametro">
                            <span class="fa fa-plus red"></span> Nuevo</span> 
                          </tr>
                        </tr>
                        <tr class="filaprincipal">
                            <td>Nombre Completo</td>
                            <td>Identificación</td>
                            <td>Tipo</td>
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
                <input id='txt_dato_buscar' class="form-control" placeholder="Ingrese identificación o nombre de la persona">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button></span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_personas_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="4" class="nombre_tabla">TABLA PERSONA</td>
                  </tr>
                  <tr class="filaprincipal">
                      <!--<td>Ver</td>-->
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


<div class="modal fade" id="modal_guardar_plan_profesor" role="dialog">
    <div class="modal-dialog modal-lg">
        <form  id="form_guardar_plan_profesor" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-cog"></span> Administrar Profesor</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <div class='col-md-10'>
                      <select name="id_estado" required class="form-control cbx_estados"><option value="">Seleccione Estado</option></select> 
                      <select name="id_departamento" required class="form-control cbx_departamentos"><option value="">Seleccione Departamento</option></select> 
                      <select name="id_programa"  required class="form-control cbx_programas_add"><option value="">Seleccione Programa</option></select> 
                      <div class="agro agrupado">
                        <div class="input-group">
                          <select name="id_dedicacion" required class="form-control cbx_dedicaciones"><option value="">Seleccione Dedicación</option></select> 
                          <span class="input-group-addon">-</span>
                          <select name="id_escalafon" required class="form-control cbx_escalafones"><option value="">Seleccione Escalafon</option></select> 
                        </div>
                      </div>
                      <select name="id_contrato"  required class="form-control cbx_contratos"><option value="">Seleccione Contrato</option></select>
                      <div class="agro agrupado">
                        <div class="input-group">
                          <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
                          <input type="date" class="form-control sin_margin" required="true" name='fecha_inicio'> 
                        </div>
                      </div>
                      <div class="agro agrupado">
                        <div class="input-group">
                          <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Fin</span>
                          <input type="date" class="form-control sin_margin" name='fecha_fin'>
                        </div>
                      </div>
                      <div class="agro agrupado">
                        <div class="input-group">
                          <select name="id_grupo"  required class="form-control cbx_grupos"><option value="">Seleccione Grupo Investigación</option></select> 
                          <span class="input-group-addon">-</span>
                          <select name="id_area"  required class="form-control cbx_areas"><option value="">Seleccione Area Conocimiento</option></select> 
                        </div>
                      </div>
                      <input name="cvlac" class="form-control" type="url" placeholder='Link cvlac'>
                      <div class="agro agrupado">
                        <div class="input-group">
                          <input name="google" class="form-control" type="url" placeholder='Link google'>
                          <span class="input-group-addon">-</span>
                          <input name="scopus" class="form-control" type="url" placeholder='Link scopus'>
                        </div>
                      </div>
                    </div>
                    <div class='col-md-2 '>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_indicadores'><span class='red fa fa-bar-chart'></span> Indicadores</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_asignaturas'><span class='red fa fa-list'></span> Asignaturas</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_formacion'><span class='red fa fa-book'></span> Formación</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_objetivos'><span class='red fa fa-check-circle-o'></span> Objetivos</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_atencion'><span class='red fa fa-calendar'></span> Atención</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_perfiles'><span class='red fa fa-sitemap'></span> Perfiles</button>
                      <button type="button" class='btn btn-default mt10' id='btn_lineas'><span class='red fa fa-random'></span> Lineas</button>
                      <button type="button" class='btn btn-default mt10' id='btn_adm_horas'><span class='red fa fa-clock-o'></span> Horas</button>                      
                    </div>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_adm_indicadores" role="dialog">
    <div class="modal-dialog modal-95">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-bar-chart"></span> Administrar Indicadores</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_indicadores" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="7" class="nombre_tabla">TABLA INDICADORES</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nuevo_indicador" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Tipo</td>
                        <td>Nombre</td>
                        <td>Fecha Inicial</td>
                        <td>Estado Inicial</td>
                        <td>Fecha Meta</td>
                        <td>Meta</td>
                        <td>Estado Actual</td>
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

<div class="modal fade" id="modal_adm_asignaturas" role="dialog">
    <div class="modal-dialog modal-80">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-list"></span> Administrar Asignaturas</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_asignaturas" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="7" class="nombre_tabla">TABLA ASIGNATURAS</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nueva_asignatura" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Nombre</td>
                        <td>Creditos</td>
                        <td>Grupo</td>
                        <td>Día</td>
                        <td>Horario</td>
                        <td>Cupo</td>
                        <td>Salon</td>
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

<div class="modal fade" id="modal_adm_formacion" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-book"></span> Administrar Formación</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_formacion" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="2" class="nombre_tabla">TABLA FORMACIÓN</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nueva_formacion" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Formación</td>
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

<div class="modal fade" id="modal_adm_objetivos" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-check-circle-o"></span> Administrar Objetivos</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_objetivos" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td class="nombre_tabla">TABLA OBJETIVOS</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nuevo_objetivo" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Descripción</td>
                        <td class="opciones_tbl_btn_btn">Acción</td>
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

<div class="modal fade" id="modal_adm_atencion" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-calendar"></span> Administrar Atención</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <div class="alert alert-info">
                    <strong>Información!</strong> El docente debe dictar <span id='horas_estu'></span>de atención a los estudiantes y <span id='horas_men'></span> de mentoring durante la semana. 
                  </div>
                  <table class="table table-bordered table-hover table-condensed" id="tabla_atencion" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="6" class="nombre_tabla">TABLA ATENCION</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nueva_atencion" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Tipo</td>
                        <td>Asignatura</td>
                        <td>Día</td>
                        <td>Hora Inicio</td>
                        <td>Hora Fin</td>
                        <td>Lugar</td>
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

<div class="modal fade" id="modal_adm_perfiles" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-sitemap"></span> Administrar Perfiles</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_perfil" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="3" class="nombre_tabla">TABLA PERFILES</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nuevo_perfil" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Perfil</td>
                        <td>Rol</td>
                        <td>Cobertura</td>
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

<div class="modal fade" id="modal_adm_lineas" role="dialog">
    <div class="modal-dialog modal-lg modal-80">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-random"></span> Administrar Lineas</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_lineas" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="2" class="nombre_tabla">TABLA LINEAS</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nuevo_linea" class="btn btn-default"><span class="fa fa-plus red" ></span> Nueva</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Linea</td>
                        <td>Sub-Linea</td>
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


<div class="modal fade" id="modal_adm_horas" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-clock-o"></span> Administrar Horas X Programa</h3>
            </div>
            <div class="modal-body" id="bodymodal">
                <div class="">
                  <table class="table table-bordered table-hover table-condensed" id="tabla_horas" cellspacing="0" width="100%" >
                    <thead class="ttitulo ">
                      <tr class="">
                        <td colspan="3" class="nombre_tabla">TABLA PROGRAMAS</td>
                        <td class="sin-borde text-right border-left-none"><span id="btn_nueva_hora" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                      </tr>
                      <tr class="filaprincipal">
                        <td>Programa</td>
                        <td>Hora</td>
                        <td>Cantidad</td>
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

<div class="modal fade" id="modal_guardar_indicador" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_indicador" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Nuevo Indicador</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <div class="funkyradio facturacion" style='margin-top:0px;'>
                        <div class="funkyradio-success">
                          <input type="radio" id="rate1" name="tipo" value="Aplica" />
                          <label for="rate1" title="Aplica"> Aplica</label>
                        </div>
                        <div class="funkyradio-danger">
                          <input type="radio" id="rate2" name="tipo" value="No aplica" />
                          <label for="rate2" title="No Aplica"> No Aplica</label>
                        </div>
                    </div>
                    <select name="id_indicador" required class="form-control cbx_indicadores"><option value="">Seleccione Indicador</option></select> 
                    <input name="estado_inicial" required class="form-control" type="text" placeholder='Estado Inicial'>
                    <div class="agro agrupado">
                      <div class="input-group">
                        <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Inicio</span>
                        <input type="date" class="form-control sin_margin" required="true" name='fecha_inicial'> 
                      </div>
                    </div>
                    <input name="estado_final" required class="form-control" type="text" placeholder='Estado Final'>
                    <div class="agro agrupado">
                      <div class="input-group">
                        <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Fecha Fin</span>
                        <input type="date" class="form-control sin_margin" required="true" name='fecha_final'>
                      </div>
                    </div>
                    <input name="estado_actual" required class="form-control" type="text" placeholder='Estado Actual'>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_guardar_asignatura" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_asignatura" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-list"></span> Nuevo Asignatura</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_asignatura" required class="form-control cbx_asignaturas"><option value="">Seleccione Asignatura</option></select> 
                    <select name="id_dia" required class="form-control cbx_dias"><option value="">Seleccione Día</option></select> 
                    <div class="agro agrupado">
                        <div class="input-group">
                          <input name="horario" required class="form-control" type="time" placeholder='Horario'>
                          <span class="input-group-addon">-</span>
                          <input name="creditos" required class="form-control" type="number" placeholder='#Creditos'>
                        </div>
                    </div>
                    <input name="salon" required class="form-control" type="text" placeholder='Salon'>   
                    <div class="agro agrupado">
                        <div class="input-group">
                        <input name="grupo" required class="form-control" type="text" placeholder='Grupo'>
                          <span class="input-group-addon">-</span>
                          <input name="cupo" required class="form-control" type="number" placeholder='Cupos'>
                        </div>
                    </div>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_guardar_formacion" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_formacion" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-book"></span> Nueva Formación</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_formacion" required class="form-control cbx_formacion"><option value="">Seleccione Formación</option></select> 
                    <input name="nombre" required class="form-control" type="text" placeholder='Nombre'>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal_guardar_objetivo" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_objetivo" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-check-circle-o"></span> Nuevo Objetivo</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <textarea  name="objetivo" cols="20" rows="3" class="form-control comentarios" placeholder="Objetivo"></textarea>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_guardar_atencion" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_atencion" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-calendar"></span> Nuevo Horario Atención</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_tipo" required class="form-control cbx_tipo_atencion"><option value="">Seleccione Tipo</option></select> 
                    <select name="id_asignatura" required class="form-control cbx_asignatura_atencion"><option value="">Seleccione Asignatura</option></select> 
                    <select name="id_dia" required class="form-control cbx_dias"><option value="">Seleccione Día</option></select> 
                    <div class="agro agrupado">
                        <div class="input-group">
                          <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hora Inicio</span>
                          <input type="time" class="form-control sin_margin" required="true" name='hora_inicio'> 
                        </div>
                      </div>
                      <div class="agro agrupado">
                        <div class="input-group">
                          <span class="input-group-addon" style='	background-color:white'><span class='fa fa-calendar red'></span> Hora Fin</span>
                          <input type="time" class="form-control sin_margin" required="true" name='hora_fin'>
                        </div>
                      </div>
                    <input name="lugar" required class="form-control" type="text" placeholder='Lugar'>   
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_guardar_perfil" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_perfil" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-sitemap"></span> Nuevo Perfil</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_perfil" required class="form-control cbx_perfil"><option value="">Seleccione Perfil</option></select> 
                    <select name="id_rol" required class="form-control cbx_rol"><option value="">Seleccione Rol</option></select> 
                    <input type="text" class="form-control" required name='id_cobertura' placeholder='Cobertura'>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_guardar_hora" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_hora" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-clock-o"></span> Nueva Hora</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_programa" required class="form-control cbx_programas"><option value="">Seleccione Programa</option></select> 
                    <select name="id_hora" required class="form-control cbx_horas"><option value="">Seleccione Hora</option></select> 
                    <input type="number" class="form-control" step='1' min='0' required name='cantidad' placeholder='Cantidad'>
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_guardar_linea" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_linea" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-random"></span> Nueva Linea</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                    <select name="id_linea" required class="form-control cbx_linea"><option value="">Seleccione Linea</option></select> 
                    <select name="id_sub_linea" required class="form-control cbx_sub_linea"><option value="">Seleccione Sub-Linea</option></select> 
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_asignar_permiso" role="dialog">
    <div class="modal-dialog">
        <form  id="form_guardar_permiso" enctype="multipart/form-data" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-random"></span> Asignar Permiso</h3>
                </div>
                <div class="modal-body " id="bodymodal">
                  <div class="row">
                  <input name='id_persona' type="hidden" id='id_persona_permiso'>
                    <select name="tipo" required class="form-control" required>
                      <option value="">Seleccione Tipo</option>
                      <option value="decano">Decano</option>
                      <option value="normal">Normal</option>
                      <option value="coordinador">Coordinador</option>
                    </select> 
                  </div>
                </div>  
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal_listar_soportes" role="dialog" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"> <span class="fa fa-folder-open"></span> Soportes Adjuntos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="table-responsive"> 
            <table class="table table-bordered table-hover table-condensed " id="tabla_soportes"  cellspacing="0" width="100%" >
              <thead class="">
                  <tr>
                    <td colspan="4" class="nombre_tabla">TABLA DE ADJUNTOS</td>
                    <td class="sin-borde text-right border-left-none"><span id="btn_agregar_sop_formacion" class="btn btn-default"><span class="fa fa-plus red" ></span> Nuevo</span></td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Ver</td>
                    <td>Nombre</td>
                    <td>Fecha</td>
                    <td>Persona</td>
                    <td>Acción</td>
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

<!-- Modal para archivos -->
<div class="modal fade" id="modal_enviar_archivos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-folder-open"></span> Adjuntar soportes</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <form  class="dropzone needsclick dz-clickable" id="Subir" action="">
          <input type="hidden" name="id_formacion" id="id_formacion_archivo" val="0">
          <div class="dz-message needsclick"><p>Arrastre archivos o presione click aquí</p></div>
        </form>
      </div>
      <div class="modal-footer" id="footermodal">
        <button id="cargar_adjuntos_general" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Aceptar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_periodos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" >
      <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-filter"></span> Filtrar Planes</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row">
          <select name="id_periodo" required class="form-control cbx_periodos"><option value="">Seleccione Periodo</option></select> 
          <select name="id_filtro_firma" required class="form-control cbx_filtro_firma"><option value="">Seleccione Filtro</option></select> 
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <!-- <button id="btn_filtrar" class="btn btn-danger active btnAgregar"><span class="glyphicon glyphicon-floppy-disk"></span>Filtrar</button> -->
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php  }?>

<div class="modal fade" id="cargar_firma" role="dialog">
  <div class="modal-dialog modal-md">
    <form  id="cargar_firma_digital" enctype="multipart/form-data" method="post">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-pencil"></span> Cargar Firma</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="alert alert-info">
            Para los procesos internos de la Universidad, es necesario contar con su firma para aprobar el plan de trabajo.
          </div>
          <div id="cont_firma_digital" class="input-group agrupado">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                <span class="fa fa-folder-open"></span> Buscar
                <input name="firma_digital" type="file" accept="image/*" style="display: none;" id="firma_digital" >
              </span>
            </label>
            <input type="text" id="firma_digital_text" class="form-control" readonly placeholder='Firma Digital'>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modal_modificar_firma" role="dialog">
  <div class="modal-dialog modal-md">
    <form  id="modificar_firma_digital" enctype="multipart/form-data" method="post">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="glyphicon glyphicon-pencil"></span> Modificar Firma</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="alert alert-info">
            Para los procesos internos de la Universidad, es necesario contar con su firma para aprobar el plan de trabajo.
          </div>
          <div id="cont_firma_digital_md" class="input-group agrupado">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                <span class="fa fa-folder-open"></span> Buscar
                <input name="firma_digital_mod" type="file" accept="image/*" style="display: none;" id="firma_digital_mod" >
              </span>
            </label>
            <input type="text" id="firma_digital_text_mod" class="form-control" readonly placeholder='Firma Digital'>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active btnAgregar"> <span class="glyphicon glyphicon-floppy-disk"></span> Modificar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="modal_importar_excel" role="dialog">
  <div class="modal-dialog modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-upload"></span> Subir registros</h3>
      </div>

      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="form-group agrupado col-md-8 text-left">
            <div class="input-group">
              <span id="" class="btn btn-default" title="Subir registros" data-toggle='popover' data-trigger='hover'> <input type="file" id="xFile" name="" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="importar(this)"></span>
            </div>
          </div>
          
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="mostrar_import_excel" cellspacing="0" width="100%">
              <thead class="ttitulo ">
              <tr class="">
                <td class="sin-borde text-right border-left-none"> </td>
              </tr>
                <tr class="filaprincipal">
                  <td>ID</td>
                  <td>Programa</td>
                  <td>Departamento</td>
                  <td>Area</td>
                  <td>Dedicacion</td>
                  <td>Escalafon</td>
                  <td>contrato</td>
                  <td>Fecha inicio</td>
                  <td>Fecha fin</td>
                  <td>Grupo</td>
                  <td>cvlac</td>
                  <td>Google</td>
                  <td>scopus</td>
                  <td>Estado</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" id="btn_guardar_reg" onclick="GuardarImpor()"  class="btn btn-default" title="Guardar registros" data-toggle='popover' data-trigger='hover'><span class="fa fa-cloud-upload red"></span>Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_guardadosyno" role="dialog">
  <div class="modal-dialog modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title">...</h3>
      </div>

      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          
          <div id="si_guardados"class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="mostrar_guardadosi" cellspacing="0" width="100%">
              <thead class="ttitulo ">
              <tr class="">
                <td colspan="8" class="nombre_tabla">Listado de registros guardados</td>
                <td colspan="6" class="sin-borde text-right border-left-none"> <span class="btn btn-default" onclick="CambioTabla(1);">Ver No Guardados</span></td>
              </tr>
                <tr class="filaprincipal">
                  <td>ID</td>
                  <td>Programa</td>
                  <td>Departamento</td>
                  <td>Area</td>
                  <td>Dedicacion</td>
                  <td>Escalafon</td>
                  <td>contrato</td>
                  <td>Fecha inicio</td>
                  <td>Fecha fin</td>
                  <td>Grupo</td>
                  <td>cvlac</td>
                  <td>Google</td>
                  <td>scopus</td>
                  <td>Estado</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div id="no_guardados"class="table-responsive" style="width: 100%" style="display: none">
            <table class="table table-bordered table-hover table-condensed pointer" id="mostrar_no_guardados" cellspacing="0" width="100%">
              <thead class="ttitulo ">
              <tr class="">
                <td colspan="8" class="nombre_tabla">Listado de registros no guardados</td>
                <td colspan="6" class="sin-borde text-right border-left-none"> <span class="btn btn-default" onclick="CambioTabla(0);">Ver Guardados</span></td>
              </tr>
                <tr class="filaprincipal">
                  <td>ID</td>
                  <td>Programa</td>
                  <td>Departamento</td>
                  <td>Area</td>
                  <td>Dedicacion</td>
                  <td>Escalafon</td>
                  <td>contrato</td>
                  <td>Fecha inicio</td>
                  <td>Fecha fin</td>
                  <td>Grupo</td>
                  <td>cvlac</td>
                  <td>Google</td>
                  <td>scopus</td>
                  <td>Estado</td>
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

<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/xlsx.full.min.js" ></script>
<script>
  $(document).ready(function() {
    inactivityTime();
    buscar_profesor('');
    listar_parametros();
    listar_valores_parametros(-1);
    listar_valores_parametros_relaciones(-1,-1);
    listar_valores_parametros_bloque();
    Cargar_parametro_buscado_aux(103, ".cbx_tipo_atencion", "Seleccione Tipo");
    Cargar_parametro_buscado_aux(247, ".cbx_filtro_firma", "Seleccione Filtro");
    cargar_archivos_general(`${Traer_Server()}index.php/profesores_csep_control/recibir_archivos`,() => listar_soportes($("#id_formacion_archivo").val(), 'formacion'));
    pintar_periodos(-1, 1);
    activarfile();
  });

  </script>