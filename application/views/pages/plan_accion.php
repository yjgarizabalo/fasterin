<?php
$module_admin = false;
$menu_princial = "";
$tabla_solicitudes = "";
if (isset($_SESSION["perfil"])) {
  if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro") {
    $module_admin = true;
    $menu_princial = "oculto";
    $tabla_solicitudes = "";
  } else {
    $module_admin = false;
    $menu_princial = "";
    $tabla_solicitudes = "oculto";
  }
}
?>

<script>
  var personaEnSession = <?php echo $_SESSION['persona']; ?>;
</script>

<link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">

<div class="container col-md-12 text-center" id="inicio_user">
  <div class="tablausu lista_plan_acciones col-md-12 text-left <?php echo $tabla_solicitudes; ?>">
    <div class="table-responsive col-sm-12 col-md-12  tablauser">
      <p class="titulo_menu pointer" id="regresar_menu"><span class="fa fa-reply-all naraja"></span> Regresar</p>
      <table class="table table-bordered table-hover table-condensed table-responsive" id="tabla_plan_acciones" cellspacing="0" width="100%">
        <thead class="ttitulo ">
          <tr class="tablaAcciones">
            <td colspan="2" style="vertical-align: middle;" class="nombre_tabla"> LISTADO DE ACCIONES
              <br>
              <span class="mensaje-filtro oculto">
                <span class="fa fa-bell red"></span> La tabla tiene algunos filtros aplicados.
              </span>
            </td>
            <td class="sin-borde text-right border-left-none" colspan="8">
              <?php if ($module_admin) { ?>
                <span class="btn btn-default" id="btnGenData" data-toggle="modal" title="Generar base de datos de las acciones.">
                  <span class="fa fa-database red"></span> Generar base de datos
                </span>
                <!-- <span class="btn btn-default" id="btn_notificaciones" data-toggle="modal">
                  <span class="fa fa-bell red"></span> Notificaciones
                </span> -->
                <span class="btn btn-default btnAgregar" id="btn_admin_modulo"><span class="fa fa-cogs red"></span> Administrar</span>
              <?php } ?>
              <!-- <span class="btn btn-default" title="Filtrar" data-toggle="modal" data-target="#modal_filtrar">
                <span class="fa fa-filter red"></span> Filtrar
              </span> -->
              <span class="btn btn-default" id="limpiar_filtros">
                <span class="fa fa-refresh red"></span> Limpiar
              </span>
            </td>
          </tr>
          <tr class="filaprincipal">
            <td class="verBox">Ver</td>
            <td style="width:5% !important;">Código</td>
            <td>Nombre de la meta</td>
            <td>Área Estratégica</td>
            <td>Estado</td>
            <td>Solicitante</td>
            <td>Formato</td>
            <td>Propiedad</td>
            <td class="smallBox">Acción</td>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tablausu col-md-12 <?php echo $menu_princial; ?>" id="menu_principal" style='background-image: url("<?php echo base_url(); ?>/imagenes/LogocucF.png")'>
    <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_agil.png")'></div>
    <div id="container-principal2" class="container-principal-alt">
      <h3 class="titulo_menu"><span class="fa fa-navicon"></span> MENÚ</h3>
      <div class="row">
        <div id="nueva_accion" class="pointer">
          <div class="thumbnail">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/logistica.png" alt="...">
              <span class="btn form-control">Nueva Acción</span>
            </div>
          </div>
        </div>
        <div class="" id="lista_de_acciones">
          <div class="thumbnail ">
            <div class="caption">
              <img src="<?php echo base_url() ?>/imagenes/misolicitudes.png" alt="...">
              <span class="btn form-control">Lista de Acciones</span>
            </div>
          </div>
        </div>
      </div>
      <p class="titulo_menu titulo_menu_alt pointer" id="inicio_return"><span class="fa fa-reply-all naraja"></span> Regresar</p>
    </div>
  </div>
</div>


<!-- Modales -->

<!-- Modal para nueva solicitud -->
<div class="modal fade" id="modal_accion_select" role="dialog">
  <div class="modal-dialog modal-80">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bookmark"></span> Selección de área estratégica</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="opciones__container" id="areas_select">
          <!-- Auto generado -->
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal metas del area estrategica seleccionada previamente -->
<div class="modal fade" id="modal_metas_accion" role="dialog">
  <div class="modal-dialog modal-80">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-flag"></span> Metas -Area estratégica-</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="container-fluid cartas" id="cartas_container">
          <!-- Auto generado -->
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para seleccionar el item a gestionar una vez elejida el area estrategica y se le de en crear meta -->
<div class="modal fade" id="modal_gestionar_datosmeta" role="dialog">
  <div class="modal-dialog modal-80" style="width:60%;">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close btnCloseModal" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list-alt"></span> Gestión de información</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="container-fluid cartas">
          <div class="opciones__container">
            <div class="opcion__cont" style="width:21%;" id="info_item" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Gesionar información cómo: Selección de retos, meta del plan de desarrollo y más.">
              <img src="<?php echo base_url(); ?>imagenes/form_img.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Información general</span>
            </div>

            <div class="opcion__cont" style="width:21%;" id="factores_item" data-id="factor_insti" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccionar lineamientos de acreditación">
              <img src="<?php echo base_url(); ?>imagenes/factores_img.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Lineamientos de Acreditación</span>
            </div>

            <div class="opcion__cont" style="width:21%;" id="factores_programa" data-id="factor_program" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccionar Lineamientos de acreditación">
              <img src="<?php echo base_url(); ?>imagenes/formato_programa_item.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Lineamientos de Acreditación</span>
            </div>

            <div class="opcion__cont" style="width:21%;" id="responsable_item" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Seleccionar responsables">
              <img src="<?php echo base_url(); ?>imagenes/responsables_img.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Responsables</span>
            </div>

            <div class="opcion__cont" style="width:21%;" id="cronograma_item" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Gestión de Cronograma">
              <img src="<?php echo base_url(); ?>imagenes/cronograma_img.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Cronograma</span>
            </div>

            <div class="opcion__cont" style="width:21%;" id="presupuesto_item" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Gestionar presupuesto">
              <img src="<?php echo base_url(); ?>imagenes/presupuesto_img.png" style="width: 90px; margin-bottom:9px;" class="opcion__img" alt="...">
              <span class="opcion__span">Presupuesto</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default btnCloseModal active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para el formulario donde se guardan las metas de plan de accion segun area estrategica seleccionada previamente -->
<form id="form_metasplan_accion" name="form_metasplan_accion" method="post">
  <div class="modal fade" id="modal_metas_form" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title">
            <!-- Auto generado -->
          </h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <div class="alert alert-info" style="width: 100%; margin:auto;" role="alert" data-id="programPlace">
              <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
              <p class="">
                "Seleccionar dependencia": seleccione esta opción si su acción estará vinculada a una dependencia institucional.
                <br>
                <br>
                "Buscar programas": seleccione esta opción si la acción que va a cargar apunta a una recomendación y/o aspecto positivo de programas acreditados.
              </p>
            </div>
            <div class="agro agrupado buscar_dato">
              <div class="input-group" data-id="programPlace">
                <input data-id="programPlaceChild" type="text" class="form-control sin_margin sin_focus" data-original-title="Asignar vicerectoría" data-input_name="vice_search" id="vice_search" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Buscar vicerrectoría encargada.">
                <span class="input-group-addon pointer btn btn-default buscarVice" data-dato_buscado="Vicerector" id="btn_buscar_vice" style="background-color:white"><span class="fa fa-search red"></span> Seleccionar dependencia</span>
              </div>
            </div>
            <div class="agro agrupado buscar_dato">
              <div class="input-group" data-id="programPlace">
                <input data-id="programPlaceChild" type="text" class="form-control sin_margin sin_focus" data-original-title="Asignar programa" data-input_name="programaSearch" id="programaSearch" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Buscar programas CUC">
                <span class="input-group-addon pointer btn btn-default buscarVice" data-dato_buscado="Programas" id="btn_buscarPrograma" style="background-color:white"><span class="fa fa-search red"></span> Buscar programa</span>
              </div>
            </div>
            <div class="agro agrupado buscar_dato">
              <div class="input-group">
                <input type="text" class="form-control sin_margin sin_focus" data-original-title="Reto:" data-input_name="retos_search" id="txt_nombre_reto" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Desafíos o propósitos estipulados por cada área estratégica que la Universidad trabajará en su Plan de Desarrollo Institucional.">
                <span class="input-group-addon pointer btn btn-default buscar" data-dato_buscado="Retos" id="btn_buscar_reto" style="	background-color:white"><span class="fa fa-search red"></span> Buscar Reto</span>
              </div>
            </div>
            <div class="agro agrupado buscar_dato">
              <div class="input-group buscar_dato">
                <input type="text" class="form-control sin_margin sin_focus" data-original-title="Meta Plan de Desarrollo Institucional 2023:" id="txt_nombre_plan_des" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Métricas que se desea obtener para alcanzar los desafíos y propósitos establecidos en el PDI 2020-2023.">
                <span class="input-group-addon pointer btn btn-default buscar" data-dato_buscado="Metas" id="btn_buscar_planD" style="background-color:white"><span class="fa fa-search red"></span> Meta del Plan de desarrollo</span>
              </div>
            </div>

            <!-- Aviso para programas, titulo de la acción traida del pull de datos -->
            <div class="agro agrupado">
              <div class="alert alert-warning" role="alert" data-original-title="Indicador Estratégico:" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Datos cuantitativos que medirán el desempeño o magnitud de las metas establecidas en el PDI 2020-2023.">
                <div data-id="programPlace">
                  <h4><span class="fa fa-bookmark"></span> Acción de la dependencia: </h4>
                  <p id="nombreAccionInsti">N/A</p>
                </div>
                <hr data-id="programPlace">
                <div>
                  <h4><span class="fa fa-bookmark"></span> Indicador estratégico: </h4>
                  <p class="indics_content"> ¡Seleccione una meta del plan de desarrollo primero! </p>
                </div>
              </div>
            </div>

            <div class="agro agrupado">
              <textarea class="form-control" name="meta_plan_accion" id="meta_plan_accion" data-original-title="Meta Plan de Acción 2022:" placeholder="Ingrese meta plan de acción..." data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Métricas que identifican el objetivo a alcanzar u obtener en el año 2022."></textarea>
            </div>
            <div class="agro agrupado">
              <textarea class="form-control" data-original-title="Indicador Operativo :" name="indicador_operativo" id="indicador_operativo" placeholder="Indicador operativo..." data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Datos cuantitativos que midirán el desempeño o magnitud de la Meta del Plan de Acción 2022."></textarea>
            </div>
            <div class="agro agrupado" data-original-title="Tipo de indicador operativo:" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Corresponde a la clasificación del indicador operativo para ser medido o cuantificable, está dado por: 
                   a) Numérico: expresa las magnitudes numéricas reflejadas en los datos cuantitativos del Indicador Operativo.
                   b) Porcentual: refleja una proporcionalidad numérica identificada en los datos cuantitativos del indicador operativo">

              <select class="form-control" data-live-search="true" name="num_or_porcents" id="num_or_porcents">
                <!-- Auto generado -->
              </select>
            </div>
            <div class="agro agrupado" id="cifraRefCont">
              <textarea class="form-control input_numerico" data-original-title="Cifra de referencia 2021:" name="cifra_referencia" id="cifra_referencia" placeholder="Cifra de referencia" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Dato numérico identificado en la Meta del Plan de Acción 2021 que servirá como base o guía para indicar las unidades de medición que se establecerán en la Meta del Plan de Acción 2022."></textarea>
            </div>
            <div class="agro agrupado">
              <textarea class="form-control input_numerico" data-original-title="Meta 2022:" name="meta" id="meta" placeholder="Meta" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Escala numérica del 1 al infinito que se desea alcanzar u obtener en la Meta del Plan de Acción 2022."></textarea>
            </div>
            <div class="agro agrupado">
              <textarea type="text" class="form-control" data-original-title="Acciones:" name="nombre_accion" id="nombre_accion" placeholder="Escribir la acción" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="Actividades, procesos y procedimientos que se realizarán para alcanzar las metas e indicadores  estalecidos en el año 2022. Su redacción deberá realizarse en verbo infinitivo."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Modal de busqueda de retos, metas plan de accion, etc -->
<form id="form_buscar_datos" name="form_buscar_datos" method="post">
  <div class="modal fade" id="modal_buscar_datos" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_buscar_datos"><span class="fa fa-search"></span> Buscar Retos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <!-- <div class="input-group">
                <input id='txt_dato_buscar' name="txt_dato_buscar" class="form-control txt_dato_buscar" value="" required placeholder="">
                <span class="input-group-btn">
                  <button class="btn btn-default btn_buscar_datos" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div> -->
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_datos_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr class="">
                    <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="smallBox">Area Estratégica</td>
                    <td class="largeBox">Nombre</td>
                    <td class="smallBox">Acción</td>
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

<!-- Modal para listar los responsables seleccionados segun meta seleccionada -->
<div class="modal fade" id="modal_responsables_asignados" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_responsables_asignados"><span class="fa fa-check-square-o"></span> Responsables Asignados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_responsables_asignados" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  <td colspan="1" class="text-right" style="border-collapse: collapse; border: none;">
                    <span class="btn btn-default btnAgregar" id="btn_add_respon"><span class="fa fa-plus red"></span> Agregar</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td class="sorting_1x">Nombre</td>
                  <td>Usuario</td>
                  <td>Cargo Sap</td>
                  <td class="sorting_1">Acción</td>
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

<!-- Modal de busqueda de factores institucionales -->
<div class="modal fade" id="modal_buscar_factores" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_buscar_factores"><span class="fa fa-search"></span> Factores Institucionales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="alert alert-info" role="alert">
            <h4 class="text-center"><span class="fa fa-bookmark"></span> ¡Atención! </h4>
            <p class="text-center"> Una vez seleccione el factor requerido, aparecerá una ventana donde deberá escoger las características del mismo. </p>
          </div>
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_factores_busqueda" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="verBox">Ver Detalles</td>
                  <td class="largeBox">Nombre</td>
                  <td class="sorting_1">Estado</td>
                  <td class="sorting_1">Acción</td>
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

<!-- Modal para ver detalles del factor institucional seleccionado -->
<div class="modal fade" id="modal_detalles_factores" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_detalles_factores"><span class="fa fa-search"></span> Características de los Factores Institucionales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_factores_detalles" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="largeBox">Características</td>
                  <td class="smallBox">Estado</td>
                  <td class="smallBox">Acciones</td>
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

<!-- Modal para listar los presupuestos de cada solicitud -->
<div class="modal fade" id="modal_listar_presupuestos" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_listar_presupuestos"><span class="fa fa-list-ol"></span> Lista de presupuestos</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_listar_presupuestos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  <td colspan="1" class="text-right" style="border-collapse: collapse; border: none;">
                    <span class="btn btn-default btnAgregar" id="btn_add_presu"><span class="fa fa-plus red"></span> Agregar</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td class="sorting_1x">Categoría</td>
                  <td>Tipo</td>
                  <td class="sorting_1">Nombre del presupuesto</td>
                  <td>Valor solicitado</td>
                  <td class='sorting_1'>Acción</td>
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

<!-- Modal para gestionar presupuestos -->
<form action="" method="post" name="presupuestos_form" id="presupuestos_form">
  <div class="modal fade" id="modal_presupuestos" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_gestion_presupuestos"><span class="fa fa-money"></span> Gestionar presupuestos</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" style="width: 100%">
            <div class='agro agrupado' id="infoAlert">
              <div class="alert alert-info" role="alert">
                <h4><span class="fa fa-warning"></span> Atención: </h4>
                <p> Al dar seleccionar la opción "No Aplica", puede guardar su presupuesto sin diligenciar el resto de ítems. </p>
              </div>
            </div>
            <div class="agro agrupado" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="¡Diligenciar categoría a la que pertenece el presupuesto!">
              <select class="selectpicker form-control" data-live-search="true" required name="categoria_presupuesto" id="categoria_presupuesto">
                <option value="0" default>Seleccione una categoría</option>
              </select>
            </div>
            <div id="divsToShow">
              <div class="agro agrupado" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="¡Diligenciar tipo de presupuesto según categoría seleccionada!">
                <select class="selectpicker form-control" data-live-search="true" required name="tipo_presupuesto" id="tipo_presupuesto">
                  <option value="0" default>¡Seleccione una categoría de presupuesto!</option>
                </select>
              </div>
              <div class='agro agrupado' id="infoPresuBox">
                <div class="alert alert-info" role="alert">
                  <h4><span class="fa fa-bookmark"></span> Información a diligenciar: </h4>
                  <p class="indics_content"> ¡Seleccione el tipo de presupuesto primero! </p>
                </div>
              </div>
              <div class="agro agrupado" data-toggle="popover" data-placement="top" data-trigger="hover" data-content="¡Diligenciar el ítem según el tipo de presupuesto seleccionado previamente!">
                <select class="selectpicker form-control" data-live-search="true" required name="item_presupuesto" id="item_presupuesto">
                  <option value="0" default>Seleccione ítem a diligenciar</option>
                </select>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <textarea class="form-control requerido sin_margin" name="descripcion" id="descripcion" placeholder="Inserte descripcion..." data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="¡Inserte descripción del presupuesto!"></textarea>
                  <span class="input-group-addon">-</span>
                  <textarea class="form-control requerido sin_margin input_numerico" name="valor_solicitado" id="valor_solicitado" placeholder="Inserte valor solicitado..." data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="¡Inserte valor necesario de su presupuesto!"></textarea>
                </div>
              </div>
            </div>
            <div class="agro agrupado">
              <div class="alert alert-warning" role="alert">
                <h4><span class="fa fa-bookmark"></span> Informacion del presupuesto del director </h4>
                <div class="table-responsive">
                  <table class="table table-bordered table-condensed" id="presuInfo">
                    <tr>
                      <th style="vertical-align: middle;" class="text-center">Tope Presupuestal</th>
                      <th style="vertical-align: middle;" class="text-center">Presupuesto Solicitado</th>
                      <th style="vertical-align: middle;" class="text-center">Tope Disponible</th>
                    </tr>
                    <tr>
                      <td style="vertical-align: middle;" class="titulo_topePresu" colspan=""></td>
                      <td style="vertical-align: middle;" class="titulo_totalSolicitado" colspan=""></td>
                      <td style="vertical-align: middle;" class="titulo_topeDispo" colspan=""></td>
                    </tr>
                    <tr>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Modal para cronogramas -->
<form action="" name="form_docs_soporte" id="form_docs_soporte" method="post">
  <div class="modal fade" id="modal_cronograma" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_cronograma"><span class="fa fa-calendar-check-o"></span> Cronograma</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row" style="width: 100%">
            <div class="alert alert-info" style="width: 80%; margin:auto;" role="alert">
              <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
              <p class="indics_content text-center"></p>
            </div>
            <br>
            <div class="alert alert-warning" style="width: 80%; margin:auto;" role="alert">
              <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
              <p class="cronoAviso text-center"></p>
            </div>
            <table class="table table-bordered table-hover table-condensed" id="tabla_cronograma" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <th colspan="1" class="nombre_tabla" style="vertical-align: middle;">RESULTADOS</th>
                  <td colspan="1" class="text-right" style="border-collapse: collapse; border: none;" rowspan="1">
                    <span class="btn btn-default btnResetCrono" id="btnResetCrono" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Si posee problemas con el cronograma, puede resetearlo y reinsertar la información."><span class="fa fa-repeat red"></span> Restablecer</span>
                  </td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="sorting_1x">Trimestre</td>
                  <td class="sorting_1">Acciones</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <div class="porcentual_container">
              <div class="agro agrupado">
                <div class="adicional_info">
                  <hr>
                  <h4 class="red text-center titulo_checks" style="background-color: #6e1f7c; margin-top: 5%; margin-bottom: 5%; color: white; padding: 1%; border-radius: 30px;">
                    <!-- Auto gen -->
                    <span class="fa fa-th-large "></span>
                    Descripción de los documentos soporte de la Meta del Plan de Acción
                  </h4>
                  <div class="agro agrupado action_amounts">
                    <div class="input-group">
                      <input type="text" class="form-control sin_margin input_numerico action_amount" placeholder="Cantidad de la meta a alcanzar en el trimestre..." name="action_amount">
                      <span class="input-group-addon pointer btnagregar_cantidad" name="btnagregar_cantidad" style='background-color:white'>
                        <span class='fa fa-cubes red'></span> Cantidad
                      </span>
                    </div>
                  </div>
                  <div class="agro agrupado">
                    <div class="input-group">
                      <input type="text" class="form-control sin_margin txt_nombre_accion" placeholder="Digite el nombre de los documentos soporte..." name="txt_nombre_accion">
                      <span class="input-group-addon pointer btnagregar_accion" style='background-color:white'>
                        <span class='fa fa-plus red'></span> Agregar
                      </span>
                    </div>
                  </div>
                  <div class="input-group agro">
                    <select name="acciones_asignadas" class="form-control sin_margin acciones_asignadas">
                      <option value="">0 Documentos agregados</option>
                    </select>
                    <span class="input-group-addon red btnElimina pointer retirar_accion" title="Retirar nombre de documento soporte" data-toggle="popover" data-trigger="hover">
                      <span class="glyphicon glyphicon-remove "></span>
                    </span>
                  </div>
                </div>
                <hr style="margin-bottom: 3%;">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger btnSaveCrono active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Modal acronogramas institucionales -->
<div class="modal fade" id="modal_cono_reusmen" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_acciones_areas"><span class="fa fa-search"></span> Cronogramas </h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="container-fluid" id="personas_container">
          <table id="tablaCronogramaIns" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla" style="vertical-align: middle;" colspan="">TABLA CRONOGRAMA INSTITUCIONAL</td>
              </tr>
              <tr class="filaprincipal ">
                <td class="largeBox">Trimestre</td>
                <td class="largeBox">Cantidad</td>
                <td class="smallBox">Acción</td>
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

<!-- Modal asignar o retirar factores institucionales -->
<form action="" method="post" id="form_numeric_crono">
  <div class="modal fade" id="modal_numeric_crono" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-bar-chart"></span> - Cambiar -</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="agro agrupado">
            <div class="adicional_info">
              <hr>
              <h4 class="red text-center titulo_checks" style="background-color: #6e1f7c; margin-top: 5%; margin-bottom: 5%; color: white; padding: 1%; border-radius: 30px;">
                <!-- Auto gen -->
                <span class="fa fa-th-large "></span>
                Descripción de los documentos soporte de la Meta del Plan de Acción
              </h4>
              <div class="agro agrupado action_amounts">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin input_numerico action_amount" placeholder="Cantidad de la meta a alcanzar en el trimestre..." name="action_amount">
                  <span class="input-group-addon pointer btnagregar_cantidad" name="btnagregar_cantidad" style='background-color:white'>
                    <span class='fa fa-cubes red'></span> Cantidad
                  </span>
                </div>
              </div>
              <div class="agro agrupado">
                <div class="input-group">
                  <input type="text" class="form-control sin_margin txt_nombre_accion" placeholder="Digite el nombre de los documentos soporte..." name="txt_nombre_accion">
                  <span class="input-group-addon pointer btnagregar_accion" style='background-color:white'>
                    <span class='fa fa-plus red'></span> Agregar
                  </span>
                </div>
              </div>
              <div class="input-group agro">
                <select name="acciones_asignadas" class="form-control sin_margin acciones_asignadas">
                  <option value="">0 Documentos agregados</option>
                </select>
                <span class="input-group-addon red btnElimina pointer retirar_accion" title="Retirar nombre de documento soporte" data-toggle="popover" data-trigger="hover">
                  <span class="glyphicon glyphicon-remove "></span>
                </span>
              </div>
            </div>
            <hr style="margin-bottom: 3%;">
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active btn_cerrar_crono" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Modal administrar modulo -->
<div class="modal fade" id="modal_administrar_modulo" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Administrar Modulo</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_compras">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <?php if ($module_admin) { ?>
                <li class="clickMe pointer active" data-place="lideres" data-id="sw" id="admin_lideres"><a><span class="fa fa-gears red"></span> Permisos Líderes</a></li>
                <li class="clickMe pointer" data-place="gestores" data-id="sw" id="admin_gestores"><a><span class="fa fa-gears red"></span> Permisos Gestores</a></li>
              <?php } ?>
            </ul>
          </div>
        </nav>
        <br>
        <div class="table-responsive">
          <div class="permisos_container" data-sw="sw" data-place="lideres" style="width: 100%;">
            <div id="permisos_lideres" style="width: 100%;">
              <div id='container_mensaje_ap'></div>
              <table id="tabla_gestion" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td class="nombre_tabla" style="vertical-align: middle;" colspan="3">LÍDERES ASIGNADOS</td>
                    <td class="text-center" style="border-collapse: collapse; border: none;">
                      <span class="btn btn-default btnAgregar" id="btnAddLider"><span class="fa fa-plus red"></span> Agregar</span>
                    </td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="opciones_tbl">Detalles</td>
                    <td>Nombre</td>
                    <td class="smallBox">Formato</td>
                    <td class="smallBox">Acciones</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Tabla de los permisos gestores -->
          <div class="permisos_container" style="display: none;" data-sw="sw" data-place="gestores" style="width: 100%;">
            <div id="permisos_gestores" style="width: 100%;">
              <div class="form-group">
                <div class="input-group agro col-md-8">
                  <input type="text" class="form-control sin_margin sin_focus" name="gestor_soli" id="gestor_soli" placeholder="Buscar gestor...">
                  <span id="sele_perso" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar gestor" data-toggle="popover" data-trigger="hover"><span class="glyphicon glyphicon-search"></span></span>
                </div>
              </div>
              <table id="tabla_gestores" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td class="nombre_tabla" style="vertical-align: middle;" colspan="2">Formatos del plan de acción</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="smallBox">No.</td>
                    <td class="largeBox">Nombre</td>
                    <td class="smallBox">Acciones</td>
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

<!-- Modal para listar a los directores asignador al lider selecto -->
<div class="modal fade" id="modal_directores_asignados" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_directores_asignados"><span class="fa fa-check-square-o"></span> Directores Asignados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_directores_asignados" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  <td colspan="1" class="text-right" style="border-collapse: collapse; border: none;">
                    <span class="btn btn-default btnAgregar" id="btn_add_director"><span class="fa fa-plus red"></span> Agregar</span>
                  </td>
                </tr>
                <tr class="filaprincipal">
                  <td class="sorting_1x">Nombre</td>
                  <td>Usuario</td>
                  <td>Cargo Sap</td>
                  <td>Tope Presupuestal</td>
                  <td class="sorting_1">Acción</td>
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

<!-- Modal para buscar personas, se usa en buscar responsables, lideres y directores -->
<form id="form_buscar_responsables" name="form_buscar_responsables" method="post">
  <div class="modal fade" id="modal_buscar_responsables" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title" id="titulo_buscar_responsables"><span class="fa fa-search"></span> Buscar Responsables</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="alert alert-info msg oculto" style="width: 80%; margin:auto;" role="alert">
            <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso </h4>
            <p class="text-center textContent"> Cambiar texto </p>
          </div>
          <div class="row" id="" style="width: 100%">
            <div class="form-group agrupado col-md-8 text-left">
              <div class="input-group">
                <input id='txt_responsable_buscar' name="txt_responsable_buscar" class="form-control txt_dato_buscar" value="" required placeholder="Buscar responsable/s...">
                <span class="input-group-btn">
                  <button class="btn btn-default btn_buscar_datos" type="submit"><span class='fa fa-search red'></span> Buscar</button>
                </span>
              </div>
            </div>
            <div class="table-responsive col-md-12" style="width: 100%">
              <table class="table table-bordered table-hover table-condensed pointer" id="tabla_responsables_busqueda" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                  </tr>
                  <tr class="filaprincipal">
                    <td class="sorting_1x">Nombre</td>
                    <td>Usuario</td>
                    <td>Cargo Sap</td>
                    <td class="smallBox">Acción</td>
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

<!-- Modal para nueva solicitud - Seleccionar formato de plan de accion aquiii -->
<form id="form_choose" method="post">
  <div class="modal fade" id="modal_formato_select" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-bookmark"></span> Selección del formato de Plan de Acción</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="alert alert-info" style="width: 80%; margin:auto;" role="alert">
            <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
            <p class="indics_content text-center"> - Para empezar a gestionar su meta, primero, debe seleccionar el Formato de Plan de Acción y así, se solicite la información correspondiente al formato seleccionado. </p>
          </div>
          <br>
          <div class="agro agrupado">
            <select class="form-control" name="formatos" id="formatos">
              <option value="">Seleccione formato</option>
            </select>
          </div>
          <div class="agro agrupado">
            <select class="form-control" name="vice_roles" id="vice_roles">
              <option value="">Seleccione rol o vicerectoría</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-danger active" id="saveFormatRol"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Modal para buscar personas aquiiii -->
<div class="modal fade" id="modal_personas" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_acciones_areas"><span class="fa fa-search"></span> Buscar Personas </h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="container-fluid" id="personas_container">
          <form action="" id="form_buscPers" name="form_buscPer">
            <div class="form-group">
              <div class="input-group">
                <input type="text" name="persona_buscada" id="perBus" class="form-control text-left pointer sin_margin" placeholder="Buscar persona...">
                <span id="buscarPer" class="input-group-addon red_primari pointer btn-Efecto-men" title="Buscar Persona" data-toggle="popover" data-trigger="hover">
                  <span class="glyphicon glyphicon-search"></span>
                </span>
              </div>
            </div>
          </form>
          <table id="tabla_personas" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla" style="vertical-align: middle;" colspan="">TABLA DE RESULTADOS</td>
              </tr>
              <tr class="filaprincipal ">
                <td class="largeBox">Nombre</td>
                <td class="smallBox">Acción</td>
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

<!-- Modal para nueva solicitud -->
<div class="modal fade" id="modal_lista_acciones" role="dialog">
  <div class="modal-dialog modal-80">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title" id="titulo_acciones_areas"><span class="fa fa-folder-open"></span> Acciones de - Autocompletado - </h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="container-fluid cartas" id="acciones_container">
          <!-- Auto generado -->
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para agregar nuevas acciones la meta seleccionada -->
<form action="" method="post" id="form_nueva_accion" name="form_nueva_accion">
  <div class="modal fade" id="modal_nueva_accion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-plus"></span> Agregar Acciones</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div class="row">
            <textarea type="text" class="form-control" name="action_name" id="action_name" placeholder="Escribir nombre de la acción..." data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Nombre de la Acción!"></textarea>
            <textarea type="text" class="form-control" name="enunciado_resp" id="enunciado_resp" placeholder="Enunciado..." data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Nombre el enunciado!"></textarea>
            <input type="text" class="form-control" name="valor_presupuesto" id="valor_presupuesto" placeholder="Valor del presupuesto" data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Valor del presupuesto!">
            <textarea type="text" class="form-control" name="prioridad_presupuestal" id="prioridad_presupuestal" placeholder="Tipo de prioridad presupuestal" data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Tipo de prioridad presupuestal!"></textarea>
            <textarea type="text" class="form-control" name="clasificacion_presupuestal" id="clasificacion_presupuestal" placeholder="Calificacion presupuestal" data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="Calificación prosupuestal!"></textarea>
          </div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="submit" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Ver detalles de acciones de plan estratégico -->
<div class="modal fade" id="modal_detalle_acciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la acción</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_metas">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <?php if ($module_admin) { ?>
                <li class="pointer active" id="add_meta"><a><span class="fa fa-info-circle red"></span> Detalles</a></li>
              <?php } ?>
            </ul>
          </div>
        </nav>
        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <tr>
              <th style="vertical-align: middle;" class="nombre_tabla" colspan="3" style="vertical-align: middle;">Información de la acción</th>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Nombre de la acción: </td>
              <td style="vertical-align: middle;" class="titulo_actionname" colspan=""></td>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Enunciado: </td>
              <td style="vertical-align: middle;" class="titulo_enunciado" colspan=""></td>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Tipo de prioridad presupuestal: </td>
              <td style="vertical-align: middle;" class="titulo_prioridad_presu" colspan=""></td>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Clasificación del Presupuesto: </td>
              <td style="vertical-align: middle;" class="titulo_presupuesto" colspan=""></td>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Valor del presupuesto: </td>
              <td style="vertical-align: middle;" class="titulo_valor_presu" colspan=""></td>
            </tr>
            <tr>
              <td style="vertical-align: middle;" class="ttitulo" colspan="">Usuario registra: </td>
              <td style="vertical-align: middle;" class="titulo_usuario_reg" colspan=""></td>
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

<!-- Modal asignar o retirar factores institucionales -->
<div class="modal fade" id="modal_factores_etc" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bar-chart"></span> - Cambiar -</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table class="table table-bordered table-hover table-condensed" id="tabla_factores_etc" cellspacing="0" width="100%">
          <thead class="ttitulo ">
            <tr>
              <th colspan="3" class="nombre_tabla">RESULTADOS</th>
            </tr>
            <tr class="filaprincipal ">
              <td class="opciones_tbl">Nº</td>
              <td style="width: 10px;">Factor</td>
              <td class="opciones_tbl">Acciones</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para preguntar el presupuesto del director seleccionado -->
<div class="modal fade" id="modal_presuSet" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-money"></span>Asignar presupuesto</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="agro agrupado">
          <input type="text" class="form-control input_numerico" autocomplete="off" name="dirPresu" id="dirPresu" data-original-title="Presupuesto del director:" placeholder="Ingrese presupuesto" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Ingrese presupuesto que tendrá disponible el director seleccionado.">
        </div>
        <div class="agro agrupado buscar_dato">
          <div class="input-group" data-id="programPlace">
            <input data-id="programPlaceChild" type="text" class="form-control sin_margin sin_focus" data-original-title="Asignar programa" data-input_name="programaBuscar" id="programaBuscar" placeholder="Ningún dato seleccionado..." data-id="" data-toggle="popover" data-trigger="hover" data-placement="bottom" data-content="Buscar programas CUC">
            <span class="input-group-addon pointer btn btn-default" data-dato_buscado="Programas" id="buscarPrograma" style="background-color:white"><span class="fa fa-search red"></span> Buscar programa</span>
          </div>
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" id="asignarPresu" class="btn btn-danger active"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para listar los programas de la cuc y asignarlo al director en el administrar -->
<div class="modal fade" id="modalCucProgramList" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Programas CUC</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="cucPrograms" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="largeBox">Programa</td>
                  <td>Estado</td>
                  <td class="smallBox">Acción</td>
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

<div id="modal_elegir_estado" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Asignar Estados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <table id="tabla_estados" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
          <thead class="ttitulo">
            <tr>
              <th colspan="3" class="nombre_tabla">TABLA ESTADOS</th>
            </tr>
            <tr class="filaprincipal">
              <td>Estado</td>
              <td>Nombre</td>
              <td>Acciones</td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para listar los lideres -->
<div id="modalLiderList" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Vicerrectorías</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table id="tabla_vices2" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla" style="vertical-align: middle;" colspan="3">LISTA DE LÍDERES</td>
              </tr>
              <tr class="filaprincipal">
                <td class="smallBox oculto">Rol</td>
                <td class="smallBox">Dependencia</td>
                <td class="smallBox">Acciones</td>
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

<!-- Modal para listar programas -->
<!-- <div id="modalProgramList" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Programas</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table id="tabla_programs" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla" style="vertical-align: middle;" colspan="3">LISTA DE PROGRAMAS</td>
              </tr>
              <tr class="filaprincipal">
                <td class="smallBox">No</td>
                <td class="largeBox">Programa</td>
                <td class="smallBox">Acciones</td>
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
</div> -->

<!-- Modal para listar los programas acreditados -->
<div class="modal fade" id="modalProgramList" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Programas Acreditados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_programs" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="largeBox">Programa</td>
                  <td>Estado</td>
                  <td class="smallBox">Acción</td>
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

<!-- Modal para ver recomendaciones de cada programa -->
<div class="modal fade" id="modalRecomendacionesPrograms" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Recomendaciones de programas acreditados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_recprograms" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="largeBox">Recomiendación</td>
                  <td class="smallBox">Estado</td>
                  <td class="smallBox">Acciones</td>
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

<!-- Modal para ver los ascpectos positivos de cada programa -->
<div class="modal fade" id="modal_aspectos_positivos" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Aspectos positivos de programas acreditados</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_aspectos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="4" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="largeBox">Aspecto positivo</td>
                  <td class="smallBox">Estado</td>
                  <td class="smallBox">Acciones</td>
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

<!-- Modal para listar los lideres -->
<div id="modalRecnesPrograms" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-check"></span> Recomendaciones de programa</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table id="tablaRecPrograms" class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr>
                <td class="nombre_tabla" style="vertical-align: middle;" colspan="3">Resultados</td>
              </tr>
              <tr class="filaprincipal">
                <td class="smallBox">No</td>
                <td class="largeBox">Recomendación</td>
                <td class="smallBox">Acciones</td>
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

<!-- Modal para listar las metas de los directores segun lider -->
<div id="modalMetasDirectors2" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Acciones Institucionales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed pointer" id="tabla_metasDirectors2" cellspacing="0" width="100%">
            <thead class="ttitulo ">
              <tr class="">
                <td colspan="5" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
              </tr>
              <tr class="filaprincipal">
                <td class="smallBox">Detalles</td>
                <td>Código</td>
                <td>Nombre de la Acción</td>
                <td>Área Estratégica</td>
                <td>Responsable</td>
                <td class="smallBox">Acción</td>
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

<!-- Modal de detalles de cartas (meta del area estrategica) -->
<div class="modal fade" id="modal_admin_meta" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-cogs"></span> Detalles</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_metas">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" data-sw="sw" data-place="detalles" id="detalles"><a><span class="fa fa-info-circle red"></span> Detalles</a></li>
              <li class="pointer" data-sw="sw" data-place="lineamientos" id="lineamientos"><a><span class="fa fa-info-circle red"></span> Lineamiento de Acreditación</a></li>
              <li class="pointer" data-sw="sw" data-place="responsables" id="responsables"><a><span class="fa fa-info-circle red"></span> Responsables</a></li>
              <li class="pointer" data-sw="sw" data-place="cronograma" id="cronograma"><a><span class="fa fa-info-circle red"></span> Cronograma</a></li>
              <li class="pointer" data-sw="sw" data-place="presupuestos" id="presupuesto"><a><span class="fa fa-info-circle red"></span> Presupuesto</a></li>
            </ul>
          </div>
        </nav>
        <div class="alert alert-info oculto" id="obsBoxx" role="alert" style="margin: auto;">
          <h4 class="text-left"><span class="fa fa-warning"></span> Aviso: </h4>
          <p id="obsBox">
            <!-- Auto Gen -->
          </p>
        </div>
        <br>
        <div class="container_detalles_metas visible" data-sw="sw" data-place="detalles" data-containers="containers">
          <div class="table-responsive">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="3" style="vertical-align: middle;">Información de la meta</th>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Formato: </td>
                <td style="vertical-align: middle;" class="titulo_formato" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Área estratégica: </td>
                <td style="vertical-align: middle;" class="titulo_area_est" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Reto: </td>
                <td style="vertical-align: middle;" class="titulo_reto" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Meta PDI 2023: </td>
                <td style="vertical-align: middle;" class="titulo_meta_planaccion" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Indicador estratégico: </td>
                <td style="vertical-align: middle;" class="titulo_ind_est" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Tipo de indicador operativo: </td>
                <td style="vertical-align: middle;" class="titulo_tipo_ind_op" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Meta plan de acción 2022: </td>
                <td style="vertical-align: middle;" class="titulo_meta_accion" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Indicador operativo: </td>
                <td style="vertical-align: middle;" class="titulo_indi_op" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Cifra de referencia: </td>
                <td style="vertical-align: middle;" class="titulo_cifra_ref" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Meta: </td>
                <td style="vertical-align: middle;" class="titulo_meta" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Acción: </td>
                <td style="vertical-align: middle;" class="titulo_nombre_accion" colspan=""></td>
              </tr>
              <tr>
                <td style="vertical-align: middle;" class="ttitulo" colspan="">Usuario registra: </td>
                <td style="vertical-align: middle;" class="titulo_usuario_reg" colspan=""></td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Tabla de lineamientos -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="lineamientos">
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_lineamientos_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="sorting_1x">Factor</td>
                  <td class="sorting_1">Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabla de cronograma -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="cronograma">
          <div class="alert alert-info" role="alert">
            <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
            <p class="text-center"> ¡Esta lista hace referencia a los trimestres seleccionados por el director al momento de crear el cronograma! </p>
          </div>
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_cronograma_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="sorting_1x">Trimestre</td>
                  <td class="smallBox">Cantidad de meta a alcanzar</td>
                  <td class="sorting_1">Acción</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabla de responsables -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="responsables">
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_responsables_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre</td>
                  <td>Cargo Sap</td>
                  <td>Usuario</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabla de presupuesto -->
        <div class="container_detalles_metas oculto" data-sw="sw" data-place="presupuestos">
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_presupuesto_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="3" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Categoría</td>
                  <td>Tipo</td>
                  <td>Ítem</td>
                  <td>Descripción</td>
                  <td>Valor solicitado</td>
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

<!-- Modal para ver caracteristicas del factor seleccionado en detalles de meta -->
<div class="modal fade" id="modal_caracts_factores" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-search"></span> Características de los Factores Institucionales</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_factorsCarats_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="1" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Características</td>
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

<!-- Modal para ver los docs soporte del trimestre seleccionado en detalles de meta -->
<div class="modal fade" id="modal_docs_soporte" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Nombres de documentos soporte</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="row" id="" style="width: 100%">
          <div class="table-responsive col-md-12" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_docsSoporte_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr class="">
                  <td colspan="1" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Nombre de documentos soporte</td>
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

<!-- Modal para seleccionar el formato deseado según los formatos que tenga asignado el lider del director en session -->
<div class="modal fade" id="modal_formatos_asignados" role="dialog">
  <div class="modal-dialog modal-80">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-bookmark"></span> Selección del formato de Plan de Acción</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="alert alert-info" style="width: 80%; margin:auto;" role="alert">
          <h4 class="text-center"><span class="fa fa-bookmark"></span> Aviso: </h4>
          <p class="indics_content text-center"> - Esta ventana, aparece solo a los líderes y/o directores cuyos formatos asignados, son más de uno.</p>
        </div>
        <div class="opciones__container" id="formatos_select">
          <!-- Auto generado -->
        </div>
      </div>
      <div class="modal-footer" id="footermodal">
        <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para visualizar la data que se va a generar de la base de datos -->
<div class="modal fade" id="modal_db_view" role="dialog">
  <div class="modal-dialog modal-95">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Resumen de acciones</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <nav class="navbar navbar-default" id="nav_admin_metas">
          <div class="container-fluid">
            <ul class="nav navbar-nav">
              <li class="pointer active" data-sw="sw" data-place="datosGenerales" id="datosGenerales"><a><span class="fa fa-info-circle red"></span> Datos generales</a></li>
              <li class="pointer" data-sw="sw" data-place="datosPresupuestos" id="datosPresupuestos"><a><span class="fa fa-info-circle red"></span> Presupuestos</a></li>
            </ul>
          </div>
        </nav>
        <!-- Tabla de datos generales, resumen unificado -->
        <div class="container_detallesPro_data" style="display:none;" data-sw="sw" data-place="datosGenerales">
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_dataGenPro_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Codigo</td>
                  <td>Usuario registra</td>
                  <td>Cargo Sap</td>
                  <td>Área estretégica</td>
                  <td>Reto</td>
                  <td>Meta Plan de Desarrollo</td>
                  <td>Indicador Estratégico</td>
                  <td>Meta PA</td>
                  <td>Indicador Operativo</td>
                  <td>Tipo de indicador operativo</td>
                  <td>Cifra de referencia</td>
                  <td>Meta</td>
                  <td>Nombre de la acción</td>
                  <td>Factores</td>
                  <td>Característica del factor</td>
                  <td>Responsables</td>
                  <td>Trimestre</td>
                  <td>Cantidad</td>
                  <td>Nombre de documentos soporte</td>
                  <td>Formato</td>
                  <td>Estado</td>
                  <td>Programas seleccionados</td>
                  <td>Recomendaciones de programas</td>
                  <td>Aspectos positivos de programa</td>
                  <td>Dependencia</td>
                  <td>Fecha registra</td>
                  <td>Acción institucional</td>
                  <td>Código institucional</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tabla de presupuestos generales, resumen unificado -->
        <div class="container_detalles_presupuesto" style="display:none;" data-sw="sw" data-place="datosPresupuestos">
          <div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-hover table-condensed pointer" id="tabla_presu_details" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" style="vertical-align: middle;" class="nombre_tabla">TABLA DE RESULTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td>Código</td>
                  <td>Categoría presupuesto</td>
                  <td>Tipo de presupuesto</td>
                  <td>Ítem de presupuesto</td>
                  <td>Descripción</td>
                  <td>Valor solicitado</td>
                  <td>Valor aprobado</td>
                  <td>Valor ejecutado</td>
                  <td>Cuenta SAP</td>
                  <td>Estado del presupuesto</td>
                  <td>Usuario registra</td>
                  <td>Dependencia</td>
                  <td>Estado Acción</td>
                  <td>Formato</td>
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

<style>
  .opcion__cont_large {
    width: 200px !important;
  }

  .sorting_1 {
    width: 13% !important;
  }

  .sorting_1x {
    width: 30% !important;
  }

  .smallBox {
    width: 17% !important;
  }

  .verBox {
    width: 7% !important;
  }

  .largeBox {
    width: 65% !important;
  }

  input[type="checkbox"] {
    width: 17px;
    height: 17px;
    vertical-align: text-bottom;
  }
</style>

<script>
  $(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    <?php if ($module_admin) { ?>
      listar_solicitudes();
    <?php } ?>
    datosFormatos();
    $("#plan_accion_titulo").html("");
    let current_year = new Date();
    $("#meta_plan_accion").attr("placeholder", `Ingrese metas de plan de acción ${current_year.getFullYear()+1}...`);
  });
</script>