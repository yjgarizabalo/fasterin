
<div class="container col-md-12 text-center" id="inicio-user">
    <div class="tablausu listado_solicitudes col-md-12 text-left" >
        <div class="table-responsive col-sm-12 col-md-12  tablauser">
        <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <div class="input-group agro btnAgregar col-xs-12 col-sm-8 col-md-4 col-lg-4 " >
            <!-- <select  name="asignar_solicitud"   class="form-control sin_margin" id="listado_parametros"> <option value="">Seleccione Parametro</option> </select> 
            <span class="input-group-addon pointer btn btn-default" id="AgregarValorParametro"><span class="fa fa-plus red"></span> Nuevo </span> -->
            <div class="agro agrupado">
              <div class="input-group">                
                <input type="text" class="form-control sin_margin sin_focus txt_nombre_parametro_x" id="nombre_parametro_x">
                <span class="input-group-addon pointer btn_buscar_parametro"> <span class="fa fa-search red"></span> Buscar Parametro</span>
              </div>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-hover margin1"  id="tablavalorparametros">
            <thead class="ttitulo ">
                <tr class="">
                <td colspan="9" class="nombre_tabla"> 
                    Tabla Valor Parametro
                    <br>                
                </td>
                <td class="sin-borde text-right border-left-none" colspan="4">            
                    <span class="btn btn-default" id="AgregarValorParametro">
                        <span class="fa fa-plus red"></span> Nuevo
                    </span>
                </td>
                </tr>     
                <tr class="filaprincipal">
                    <td class="opciones_tbl">ID</td>
                    <td>Id_aux</td>
                    <td class="">Nombre</td>
                    <td>Descripcion</td>
                    <td>Valor Y</td>
                    <td>Valor Z</td>
                    <td>Valor A</td>
                    <td>Valor B</td>
                    <td>Estado</td>
                    <td style='width:150px'>Acción</td>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
</div>

<form id="frm_buscar_parametro" method="post">
  <div class="modal fade" id="modal_buscador_parametros" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Parametro</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">                
                <input id='txt_parametro_buscado' class="form-control txt_parametro_buscado" placeholder="Buscar Parámetro">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="table_parametro_buscado" cellspacing="0" width="100%">
              <thead class="titulo">
                    <tr>
                        <th colspan="3" class="nombre_tabla">TABLA PARAMETROS</th>
                    </tr>
                    <tr class="filaprincipal">
                        <td>ID</td>
                        <td>PARAMETRO</td>
                        <td class="opciones_tbl_btn">ACCION</td>
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

<form id="frm_buscar_permiso" method="post">
  <div class="modal fade" id="modal_buscador_permisos" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close close_modal_p" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-search"></span> Buscar Parametro</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">                
                <input id='txt_parametro_permiso' class="form-control txt_parametro_permiso" placeholder="Buscar Parámetro">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="table_parametro_permiso" cellspacing="0" width="100%">
              <thead class="titulo">
                    <tr>
                        <th colspan="3" class="nombre_tabla">TABLA PARAMETROS PARA LOS PERMISOS</th>
                    </tr>
                    <tr class="filaprincipal">
                        <td>ID</td>
                        <td>PARAMETRO</td>
                        <td class="opciones_tbl_btn">ACCION</td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active close_modal_p" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="GuardarParametro" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-floppy-disk"></span> Creación de Genericas</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="text" name="nombre" class="form-control inputt2" placeholder="Nombre" required>
                        <textarea class="form-control"  cols="1" rows="3" name="descripcion" placeholder="Descripcion" required="" ></textarea>
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

<div class="modal fade" id="ValorParmetro" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="GuardarValorParametro" method="post">
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-plus"></span> Asignación Valor Genericas</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <input type="text" name="id_aux" class="form-control inputt" placeholder="Id Aux" id="id_aux">
                        <input type="text" name="nombre" class="form-control inputt" placeholder="Nombre" id="valorparametro" required>
                        <textarea class="form-control inputt" name="descripcion" placeholder="Descripcion del Parametro"></textarea>
                        <input type="text" name="valory" class="form-control inputt" placeholder="Valor Y" id="valory">
                        <input type="text" name="valorz" class="form-control inputt" placeholder="Valor Z" id="valorz">
                        <input type="text" name="valora" class="form-control inputt" placeholder="Valor A" id="valora">
                        <input type="text" name="valorb" class="form-control inputt" placeholder="Valor B" id="valorb">
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

<div class="modal fade" id="ModalModificarParametro" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="Modificar_valor_parametro" method="post">
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="fa fa-edit"></span> Modificar Valor Parametro</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row divmodifica">
                        <input type="text" name="id" id="id" required hidden>
                        <input type="text" name="id_aux" class="form-control inputt" placeholder="Id Aux" id="txtIdAux_modificar">
                        <input type="text" id="txtValor_modificar" class="form-control" placeholder="Valor" name="nombre" required>
                        <textarea rows="3" cols="100" class="form-control" id="txtDescripcion_modificar" placeholder="Descripcion..." name="descripcion" required></textarea>
                        <input type="text" name="valory" class="form-control inputt" placeholder="Valor Y" id="txtValory_modificar">
                        <input type="text" name="valorz" class="form-control inputt" placeholder="Valor Z" id="txtValorz_modificar">
                        <input type="text" name="valora" class="form-control inputt" placeholder="Valor A" id="txtValora_modificar">
                        <input type="text" name="valorb" class="form-control inputt" placeholder="Valor B" id="txtValorb_modificar">
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
 <!--modal permiso-->
<div class="modal fade" id="ModalPermiso" role="dialog">
    <div class="modal-dialog modal-lg">        
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header" id="headermodal">
                <button type="button" class="close" data-dismiss="modal"> X</button>
                <h3 class="modal-title"><span class="fa fa-cogs"></span>  Gestionar Permiso</h3>
            </div>
            <div class="modal-body" id="bodymodal">                    
                <div class="form-group"> 
                <div class="agro agrupado">
                <div class="input-group">                
                    <input type="text" class="form-control sin_margin sin_focus nombre_parametro_per" id="nombre_parametro_per">
                    <span class="input-group-addon pointer btn_buscar_permiso"> <span class="fa fa-search red"></span> Buscar Parametro</span>
                </div>
                </div>         
                <!-- <select  name="asignar_solicitud"   class="form-control sin_margin" id="listado_parametros_permiso"> <option value="">Dar permisos</option> </select> -->
                </div> 
                <!--inicio de la tabla-->
                     <div class="table-responsive">
                         <table class="table table-bordered table-hover" id="tablapermisoparametro" cellspacing="0" width="100%">                                 
                            <thead class="ttitulo ">
                                <tr class="" ><td colspan="5" class="nombre_tabla"> tabla permiso</td></tr>                                    
                                <tr class="filaprincipal"><td class="opciones_tbl">No.</td><td class="">Nombre</td><td>Descripcion</td><td>***</td></tr>
                            </thead>
                             <tbody>
                            </tbody>                                
                         </table>
                     </div>
                <!--fin-->
            </div>
            <div class="modal-footer" id="footermodal">
                <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
            </div>
        </div>       
    </div>
</div>

<script>
    $(document).ready(function () {
        inactivityTime();
        Listar_Parametros();
        Listar_valor_Parametros(-1);
        Listar_permiso(-1);
        traer_valores_permisos(-1);
        //traer_valores_permisos_2(-1);

    });
</script>