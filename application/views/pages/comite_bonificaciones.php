<?php
$sw = false;
$sw_super = false;
if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub" || $_SESSION["perfil"] == "Per_Admin_Tal") {
  $sw = true;
  $sw_super = true;
}
?>
<div class="tablausu col-md-12 text-left div_table" id="container_comite">
    <div class="table-responsive">
        <p class="titulo_menu pointer" id='inicio_return'><span class="fa fa-reply-all naraja"></span> Regresar</p>
        <table class="table table-bordered table-hover table-condensed" id="table_comite" cellspacing="0" width="100%">
            <thead class="ttitulo ">
                <tr>
                <td colspan="3" class="nombre_tabla">TABLA CONSEJO</td>
                <td colspan="5"class="sin-borde text-right border-left-none">
                    <span data-toggle="modal" data-target="#boton_guardar_comite" id="boton_guardar_comite" class="btn btn-default"><span class="fa fa-plus red"></span> Nuevo</span>
                </td>
                </tr>
                <tr class="filaprincipal ">
                <td class="opciones_tbl">Ver</td>
                <td>Nombre</td>
                <td>Fecha de cierre</td>
                <td>Descripción</td>
                <td>#Bonificaciones</td>
                <td>Creado Por</td>
                <td>Estado</td>
                <td class="opciones_tbl_btn">Acción</td>
                </tr>
            </thead>
                <tbody>
                </tbody>
        </table>
    </div>
</div>

<form id="form_list_bonificaciones" method="post">
    <div class="modal fade" id="modal_list_bonificaciones" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-search"></span> Listar Bonificaciones</h3>
          </div>
          <div class="modal-body" id="bodymodal">
            <div class="row" id="" style="width: 100%">
              <div class="table-responsive col-md-12" style="width: 100%">
                <table class="table table-bordered table-hover table-condensed pointer" id="tabla_list_bon" cellspacing="0" width="100%">
                  <thead class="ttitulo ">
                    <tr>
                      <td colspan="3" class="nombre_tabla">TABLA DE ARTÍCULOS</td>
                      <td colspan="4" class="sin-borde text-right border-left-none">
                        <span data-toggle="modal" id="boton_aprob_masivo" class="btn btn-default"><span class="fa fa-check red"></span> Aprobar</span>
                        <span data-toggle="modal" id="boton_deneg_masivo" class="btn btn-default"><span class="fa fa-times red"></span> Rechazar</span>
                      </td>
                    </tr>
                    <tr class="filaprincipal">
                      <td>Ver</td>
                      <td>Tipo</td>
                      <td>Solicitante</td>
                      <td>Fecha</td>
                      <td>Estado</td>
                      <td>Acciones</td>
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


  <div class="modal fade" id="modal_detalle_bonificacion" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-list"></span> Detalle de la Bonificación</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <nav class="navbar navbar-default" id="nav_ver_bonificaciones" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                  <li class="pointer info_bonificaciones active"><a><span class="fa fa-user red"></span>
                    Información Principal</a></li>
                  <li class="pointer autores_bonificaciones"><a><span class="fa fa-user red"></span>
                    Autores</a></li>
                  <li class="pointer evidencias_bonificaciones"><a><span class="fa fa-folder-open red"></span>
                    Evidencias</a></li>
                  <li class="pointer otros_aspectos_bonificaciones"><a><span class="fa fa-link red"></span>
                    Otros Aspectos</a></li>
                  <li class="pointer ver_porcentajes_bonificaciones"><a><span class="fa fa-link red"></span>
                    Porcentajes</a></li>
                  <li class="pointer ver_historial_bonificaciones"><a><span class="fa fa-link red"></span>
                    Historial</a></li>
              </ul>
            </div>
          </nav>
          <div class="btn_ver_informacion tabla_info_bonificaciones Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed">
              <tr>
                <th class="nombre_tabla" colspan="4">Información de la Bonificación</th>
                <td class="sin-borde text-right border-left-none" colspan="4">
                </td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Titulo de la publicación</td>
                <td class="titulo_arti" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Fecha de registro</td>
                <td class="fecha_registra" colspan="2"></td>
                <td class="ttitulo" colspan="2">Registrado por</td>
                <td class="persona_registro" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">ISSN</td>
                <td class="issn_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">DOI</td>
                <td class="doi_ver_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Cuartil Scopus</td>
                <td class="cuartil_scopus_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Cuartil Wos</td>
                <td class="cuartil_wos_ver_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Proyecto</td>
                <td class="proyecto_ver_bon" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Revista</td>
                <td class="revista_ver_bon" colspan="6"></td>
              </tr>
              <tr id="url2_container">
                <td class="ttitulo" colspan="2">URL INDEX. WOS</td>
                <td class="url_index_wos" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL INDEX. SCOPUS</td>
                <td class="url_index_scopus" colspan="2"></td>
              </tr>
              <tr id="fechas2_container">
                <td class="ttitulo" colspan="2">Año de Indexación</td>
                <td class="año_indexacion" colspan="2"></td>
                <td class="ttitulo" colspan="2">Fecha Publicación</td>
                <td class="fecha_publicacion" colspan="2"></td>
              </tr>
              <tr id="lineas_container">
                <td class="ttitulo" colspan="2">Linea</td>
                <td class="lineas_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Sublineas</td>
                <td class="sublineas_ver_bon" colspan="2"></td>
              </tr>
              <tr id="lineas_container">
                <td class="ttitulo" colspan="2">Editorial</td>
                <td class="editorial_ver_bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">URL Articulo en Linea</td>
                <td class="urlinea_ver_bon" colspan="2"></td>
              </tr>
            </table>
          </div>

          <div class="tabla_categ_ver_bon btn_ver_informacion Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_categorias_bonificacion" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA TIPOS DE ESCRITURAS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Tipo de escritura</td>
                  <td>Persona Registra</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_autores_bon btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_autores_bonificaciones" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE AUTORES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Nombre completo</td>
                  <td>Afiliación</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_evidencias btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_evidence" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE EVIDENCIAS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Ver</td>
                  <td>Comentario</td>
                  <td>Persona Registra</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_otr_asp btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_otros_aspectos" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA OTROS ASPECTOS</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">No.</td>
                  <td>Pregunta</td>
                  <td>Respuesta</td>
                  <td>Comentario</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          
          <div class="tabla_ver_porcentajes btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="table_ver_porcentaje" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA PORCENTAJES</td>
                  <td class="sin-borde text-right border-left-none" colspan="5"></td>
                </tr>
                <tr class="filaprincipal ">
                  <td class="opciones_tbl">Documento</td>
                  <td>Nombres</td>
                  <td>Porcentaje de productividad del Autor en el Artículo</td>
                  <td>Porcentaje de productividad destinada a cumplimiento de Plan de Trabajo (PDT)</td>
                  <td>Productividad destinada a bonificación</td>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="tabla_ver_historial btn_ver_informacion oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-hover table-condensed" id="tabla_estados" cellspacing="0" width="100%">
              <thead class="ttitulo ">
                <tr>
                  <td colspan="2" class="nombre_tabla">TABLA DE ESTADOS</td>
                </tr>
                <tr class="filaprincipal">
                  <td class="opciones_tbl">No.</td>
                  <td>Estado</td>
                  <td>Persona que registra</td>
                  <td>Fecha Registro</td>
                  <td>Observacion</td>
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

  <div class="modal fade" id="modal_detail_data__bonificaciones" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="headermodal">
        <button type="button" class="close" data-dismiss="modal"> X</button>
        <h3 class="modal-title"><span class="fa fa-list"></span> Detalle del autor</h3>
      </div>
      <div class="modal-body" id="bodymodal">
        <div class="table-responsive">
          <nav class="navbar navbar-default" id="nav_ver_autores" style="display: flex;">
            <div class="container-fluid">
              <ul class="nav navbar-nav">
                <li class="pointer info_autor active"><a><span class="fa fa-user red"></span>
                  Información del Autor</a></li>
                <li class="pointer info_adic_autors"><a><span class="fa fa-info red"></span>
                  Información Adicional</a></li>
                <li class="pointer ver_afil_inst"><a><span class="fa fa-university red"></span>
                  Afiliaciones</a></li>
                <li class="pointer ver_art_susc"><a><span class="fa fa-university red"></span>
                Articulos Suscritos</a></li>
                <li class="pointer ver_art_cumpl"><a><span class="fa fa-university red"></span>
                Articulos Cumplidos</a></li>
              </ul>
            </div>
          </nav>

          <div class="btn_ver_autor tabla_info_princ_autor Active" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed active">
              <tr>
                <th class="nombre_tabla" colspan="8">Información del autor</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Nombre</td>
                <td class="nombre_completo__bon" colspan="6"></td>
              </tr>
              <tr id="id_identificacion_autor">
                <td class="ttitulo" colspan="2">Tipo identificación</td>
                <td class="tipo_identificacion__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">No. identificación</td>
                <td class="identificacion__bon" colspan="2"></td>
              </tr>
              <tr class="id_departamento_autor oculto">
                <td class="ttitulo" colspan="2">Departamento</td>
                <td class="departamento_aut__bon" colspan="6"></td>
              </tr>
              <tr class="id_inst_per_ext oculto">
                <td class="ttitulo" colspan="2">Institución filial</td>
                <td class="institucion_externa" colspan="6"></td>
              </tr>
              <tr class="id_program_acad oculto">
                <td class="ttitulo" colspan="2">Programa Academico</td>
                <td class="programa_academico" colspan="6"></td>
              </tr>
              <tr class="id_linea_autor oculto">
                <td class="ttitulo" colspan="2">Linea</td>
                <td class="linea__bon" colspan="6"></td>
              </tr>
              <tr class="id_sublinea_autor oculto">
                <td class="ttitulo" colspan="2">Sublinea</td>
                <td class="sublinea__bon" colspan="6"></td>
              </tr>
              <tr class="afil_vinc_bon">
                <td class="ttitulo" colspan="2">Afiliación</td>
                <td class="afiliacion__bon" colspan="2"></td>
                <td class="ttitulo" colspan="2">Vinculación</td>
                <td class="Vinculacion__bon" colspan="2"></td>
              </tr>
            </table>
          </div>
          
          <div class="btn_ver_autor tabla_info_adic_autor oculto" style="margin-bottom:20px;">
            <table class="table table-bordered table-condensed info_adicional_autor oculto">
              <tr>
                <th class="nombre_tabla" colspan="8">Información adicional del autor</th>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace CVLAC</td>
                <td class="enlace_cvlac" colspan="2"></td>
                <td class="ttitulo" colspan="2">Enlace Google Scholar</td>
                <td class="enlace_google" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace Research Gate</td>
                <td class="enlace_rg" colspan="2"></td>
                <td class="ttitulo" colspan="2">Enlace Red Academica</td>
                <td class="enlace_red_acad" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Enlace Mendeley</td>
                <td class="enlace_mendeley" colspan="6"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">Categoría Investigador</td>
                <td class="cat_investigador" colspan="2"></td>
                <td class="ttitulo" colspan="2">Departamento</td>
                <td class="departamento_aut_bon" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">H-Index (Scholar)</td>
                <td class="hi_index_scholar" colspan="2"></td>
                <td class="ttitulo" colspan="2">RG Score</td>
                <td class="rg_score" colspan="2"></td>
              </tr>
              <tr>
                <td class="ttitulo" colspan="2">H-Index (Scopus)</td>
                <td class="hi_index_scopus" colspan="2"></td>
                <td class="ttitulo" colspan="2">ORCID ID</td>
                <td class="orcid_id_info" colspan="2"></td>
              </tr>
            </table>
          </div>

          <div class="btn_ver_autor tabla_afiliaciones_inst oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_afiliaciones" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA DE AFILIACIONES</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Nombre</td>
                    <td>Pais</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="btn_ver_autor tabla_arti_cumpl oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_art_cump" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA ARTICULOS CUMPLIDOS</td>
                    <td class="sin-borde text-right border-left-none" colspan="5"></td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Cantidad</td>
                    <td>Cuartil</td>
                    <td>Titulo</td>
                    <td>Link</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>

          <div class="btn_ver_autor tabla_arti_susc oculto" style="margin-bottom:20px;">
            <div class="table_afiliaciones_inst oculto" style="margin-bottom:20px;">
              <table class="table table-bordered table-hover table-condensed" id="table_art_susc" cellspacing="0" width="100%">
                <thead class="ttitulo ">
                  <tr>
                    <td colspan="2" class="nombre_tabla">TABLA ARTICULOS SUSCRITOS</td>
                  </tr>
                  <tr class="filaprincipal ">
                    <td class="opciones_tbl">No.</td>
                    <td>Cuartil</td>
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



<script>
    $(document).ready(function () {
        inactivityTime();
        listar_comites('<?php echo $tipo_modulo ?>');
    });
</script>