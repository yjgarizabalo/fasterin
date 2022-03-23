

<div class="container col-md-12 text-center" id="inicio-user">
   

    <div class="tablausu col-md-12 " >
        <div class="col-md-4 text-left" style="padding-left: 30px;">
            <h4>Personal Por departamento</h4>
            <select name="departamento"  id="departamento_sele" required class="form-control inputt cbxdepartamento ">
                <option>Seleccione Departamento</option>
            </select>  </div> <br> <br><br>
        <div class="tablausu col-md-12" >
            <div class="table-responsive col-sm-12 col-md-12  tablauser " >
                <table class="table table-bordered table-hover table-condensed table-responsive" id="tablapersonas_departamento"  cellspacing="0" width="100%" style="">
                    <thead class="ttitulo ">
                        <tr class="filaprincipal"><td>Primer Nombre</td><td class="">Segundo Nombre</td><td class="">Primer Apellido</td><td class="">Segundo Apellido</td><td class="">identificacion</td><td class="">Celular</td><td class="">Correo Personal</td></tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <div id="container" style="width: 80%; height: 400px; margin: 0 auto;clear: both"></div>
    <div id="container1" style="width: 80%; height: 400px; margin: 0 auto;clear: both"></div>
    <div id="container2" style="width: 80%; height: 400px; margin: 0 auto;clear: both"></div>
</div>

<script src="../../../Graficas/code/highcharts.js"></script>
<script src="../../../Graficas/modules/exporting.js"></script>
<script>
    $(document).ready(function () {
        inactivityTime();
        Grafica_barras();
        Grafico_barra2();
        grafica_linea();
        Cargar_parametro_buscado(3, ".cbxdepartamento", "Seleccione Departamento")

    });
</script>
