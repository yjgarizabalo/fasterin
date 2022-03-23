
<div class="container col-md-12 text-center" id="inicio-user">
    <div class="tablausu listado_solicitudes col-md-12 text-left" >
        <div class="table-responsive col-sm-12 col-md-12  tablauser">
        <p class="titulo_menu pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <div class="input-group agro btnAgregar col-xs-12 col-sm-8 col-md-4 col-lg-4 " >
            <select  name="asignar_solicitud"   class="form-control sin_margin" id="listado_departamento_modulo"> <option value="">Seleccione departamento</option> </select> 
            <span class="input-group-addon pointer btn btn-default" id="Asignar_cargo"><span class="fa fa-plus red"></span> Cargo </span>
        </div>
        <br>
        <table class="table table-bordered table-hover"  id="tabla_cargos_departamentos"   >
                <thead class="ttitulo ">
                    <tr >
						<td class="nombre_tabla" colspan="5" >Tabla Cargos</td>
					</tr>
                    <tr class="filaprincipal">
						<td  class="opciones_tbl">No</td>
						<td>Nombre</td>
						<td>Jefe</td>
						<td>Estado</td>
						<td class="opciones_tbl_btn">***</td>
					</tr>
				</thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <form action="#" id="Guardar_cargo_Departamento" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-check"></span> Asignar Cargo</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <select name="idcargo"   required class="form-control inputt cbx_cargo" id="cbx_cargo">
                            <option>Seleccione Cargo</option>
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

<div class="modal fade" id="modalJefe" role="dialog">
	<div class="fixed excepciones oculto ind">
		<div class="reque">
			<div class="login-container">
				<table class="" id="tblexcepciones" style="width: 100%">
					<thead class="">
						<tr class="">
							<td colspan="" class="nombre_tabla"> Excepciones<span class="" id=""></span></td>
						</tr>
					</thead>
				</table>
				<div class="form-boxw text-left">
						<br>
				<ul id ="lista_excluidos"></ul>
				</div>
			</div>
		</div>
	</div>
    <div class="modal-dialog">
        <form action="#" id="Guardar_Jefe" method="post">
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header" id="headermodal">
                    <button type="button" class="close" data-dismiss="modal"> X</button>
                    <h3 class="modal-title"><span class="glyphicon glyphicon-check"></span> Asignar Jefe</h3>
                </div>
                <div class="modal-body" id="bodymodal">
                    <div class="row">
                        <select name="depar" class="form-control inputt cbxj cbx_depar" id="cbx_depar">
                            <option value=''>Seleccione Departamento</option>
                        </select> 
                    </div> 
					<div class="row">
                        <select name="idcargo" required class="form-control cbxj inputt cbx_cargos" id="cbxjefe">
                            <option value=''>Seleccione Cargo</option>
                        </select> 
                    </div>
					<div class="row">
						<div class="text-left ind" id="div_excep">  
							<label class="ttitulo"><input type="checkbox" name="con_inscrip" value="1" id="check_excep">Asignar a todos excepto...</label>
						</div>
					</div>
					<div class="row ind">
						<div id="cargos_excep" class="text-center oculto excepciones">
							<h4 class="ttitulo">
								<span class="glyphicon glyphicon-pencil"></span> 
								Agregar Excepciones
							</h4>
							<div class="input-group agro btnAgregar col-md-12 " >
								<select  name="excep" class="form-control inputt cbx_cargos cbxj sin_margin" id="cbx_cargo_excep"> 
									<option>Seleccione Cargo...</option>
								</select> 
								<span class="input-group-addon  pointer fondo-red" id ="btnadd_excep" ><span class="glyphicon glyphicon-ok"></span> Agregar </span>
							</div>
						</div>
					</div>
				</div>

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
        Listar_departamentos();
        Listar_cargos_departamento();
        //iniciar_tabla_permisos_perfil();
		Cargar_parametro_buscado(3, ".cbx_depar", "Seleccione Departamento");
		// Cargar_parametro_buscado(2, ".cbx_cargo", "Seleccione Cargo");
    });
</script>
