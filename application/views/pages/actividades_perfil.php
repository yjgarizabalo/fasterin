
<div class="container col-md-12 text-center" id="inicio-user">
    <div class="tablausu  col-md-12 text-left" >
        <div class="table-responsive col-sm-12 col-md-12  tablauser">
        <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <div class="input-group agro btnAgregar col-xs-12 col-sm-8 col-md-4 col-lg-4 " >
            <select  name="asignar_solicitud"   class="form-control sin_margin" id="listado_perfiles"> <option value="">Seleccione Perfil</option> </select> 
            <span class="input-group-addon pointer btn btn-default" id="Asignar_Activiad"><span class="fa fa-plus red"></span> Nueva </span>
        </div>
        <br>
        <table class="table table-bordered table-hover margin1"  id="tabla_permisos_perfiles">
            <thead class="ttitulo ">
                <tr class="" ><td colspan="4" class="nombre_tabla">Tabla Actividades</td></tr>
                <tr class="filaprincipal"><td class="opciones_tbl">No.</td><td class="">Actividad</td><td class="opciones_tbl">***</td></tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
</div>


<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="Guardar_actividad_perfil" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-check"></span> Asignar Actividades Perfil</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">

                        <select name="idactividad"   required class="form-control inputt cbx_actividades" id="cbx_Actividades">
                            <option>Seleccione Actividad</option>
                        </select> 

                    </div> </div>
                <div class="modal-footer" id="footermodal">
                    <button type="submit" class="btn btn-danger active" ><span class="glyphicon glyphicon-floppy-disk"></span> Asignar</button>

                    <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
                </div>


            </div>
        </form>

    </div>
</div> 
<script>
    $(document).ready(function () {
        inactivityTime();
        Listar_perfiles_usuarios();
        iniciar_tabla_permisos_perfil();
        //Cargar_parametro_buscado_aux(18, ".cbx_actividades", "Seleccione Actividad");
    });
</script>
