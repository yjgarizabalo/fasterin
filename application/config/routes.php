<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
 */

$route['default_controller'] = "pages/Index";
$route['mensaje'] = "pages/Index/inicio/mensaje";
$route['inactivo'] = 'pages/Index/sin_session';

$route['personas'] = 'personas_control';
$route['genericas'] = 'genericas_control';

//Routes para contrataciones
$route['contrataciones']='contrataciones_control';
$route['contrataciones/(:num)'] = 'contrataciones_control/index/$1';
//$route['contratista'] = 'contrataciones_control/contratista/$1';
$route['contratista'] = 'contratista_control';
//$route['contratista/(:any)'] = 'contratista_control/contratista/$1';

$route['almacen'] = 'almacen_control';
$route['almacenADM'] = 'pages/cargar_modulo/almacenADM';
$route['almacenADM/solicitudes'] = 'almacen_control';
$route['almacenADM/inventario'] = 'almacen_inventario_control';
$route['almacen/(:num)'] = 'almacen_control/index/$1';
$route['almacenADM/solicitudes/(:num)'] = 'almacen_control/index/$1';

$route['permisos'] = 'genericas_control/index/actividades_perfil';
$route['solicitudesADM'] = 'solicitudes_adm_control';
$route['cargos'] = 'genericas_control/index/cargos_departamento';

$route['compras'] = 'compras_control/index/compras';
$route['compras/(:num)'] = 'compras_control/index/compras/$1';

$route['comite'] = 'compras_control/index/comite';
$route['comite/(:num)'] = 'compras_control/index/comite/$1';

$route['talento_humano'] = 'talento_humano_control';
$route['talento_humano/informe/(:num)/(:num)/(:num)'] = 'talento_humano_control/descargar_informe/$1/$2/$3';
$route['talento_humano/certificado/(:num)'] = 'talento_humano_control/descargar_certificado/$1';
$route['csep'] = 'talento_humano_control/index/csep';
$route['csep/solicitud/(:num)'] = 'talento_humano_control/index/csep/$1';
$route['talento_humano/csep/(:num)'] = 'talento_humano_control/index/talento_humano/$1';
$route['talento_humano/(:num)'] = 'talento_humano_control/index/talento_humano/$1';
$route['csep/(:num)'] = 'talento_humano_control/index/csep/$1';
$route['csep/requisicion/(:num)'] = 'talento_humano_control/index/csep/$1';
$route['comite_csep'] = 'talento_humano_control/index/comite_csep';
$route['comite_csep/(:num)'] = 'talento_humano_control/index/comite_csep/$1';
$route['talento_humano/detalle_req_posgrado/(:num)'] = 'talento_humano_control/exportar_detalle_req_posgrado/$1';
$route['talento_humano/detalle_requisicion/(:num)'] = 'talento_humano_control/exportar_detalle_requisicion/$1';

$route['mantenimiento'] = 'mantenimiento_control';
$route['mantenimiento/(:num)'] = 'mantenimiento_control/index/$1';
$route['mantenimientoADM'] = 'pages/cargar_modulo/mantenimientoADM';
$route['mantenimientoADM/solicitudes'] = 'mantenimiento_control';
$route['mantenimientoADM/solicitudes/(:num)'] = 'mantenimiento_control/index/$1';
$route['mantenimientoADM/inventario'] = 'almacen_inventario_control';
$route['mantenimiento_gestion'] = 'mantenimiento_control/index/mantenimiento_gestion';

$route['visitas'] = 'visitas_control';
$route['visitas/exportar_participantes/(:num)'] = 'visitas_control/exportar_excel/$1';
$route['eventos'] = 'visitas_control/auto_ingreso';
$route['eventos/generar_acta/(:num)'] = 'visitas_control/generar_acta/$1';

$route['comunicaciones'] = 'comunicaciones_control';
$route['comunicaciones/(:num)'] = 'comunicaciones_control/index/$1';

$route['presupuesto'] = 'presupuesto_control';
$route['presupuesto/(:num)'] = 'presupuesto_control/index/presupuesto/$1';

$route['biblioteca'] = 'biblioteca_control';
$route['biblioteca/(:num)'] = 'biblioteca_control/index/biblioteca/$1';
$route['biblioteca/libros_a_tu_clase/ingresar/(:num)'] = 'biblioteca_control/ingresar/$1';
$route['biblioteca/libros_a_tu_clase/encuesta'] = 'biblioteca_control/logear';
$route['biblioteca/libros_a_tu_clase/ingresar/(:num)/invalido'] = 'biblioteca_control/ingresar/$1/invalido';
$route['biblioteca/libros_a_tu_clase/encuesta_enviada'] = 'biblioteca_control/encuesta_success';
$route['biblioteca/descargar_nivel/(:num)'] = 'biblioteca_control/descargar_nivel/$1';




$route['tecnologia/reservas'] = 'reservas_control';
$route['tecnologia/reservas/(:num)'] = 'reservas_control/index/$1';

$route['tecnologia/almacen'] = 'almacen_control/index/';
$route['tecnologia/almacen/(:num)'] = 'almacen_control/index/$1';

$route['tecnologia/inventario'] = 'inventario_control';
$route['tecnologia/inventarioAUD'] = 'inventario_control';

$route['tecnologia'] = "pages/cargar_modulo/tecnologia";
$route['laboratorios'] = 'inventario_control';

$route['laboratorios'] = 'inventario_control';
$route['laboratorios/exportar_inventario'] = 'inventario_control/exportar_excel_inventario';

$route['facturacion'] = 'facturacion_control';
$route['facturacion/(:num)'] = 'facturacion_control/index/$1';

$route['bienestar'] = 'bienestar_control';
$route['bienestar/(:num)'] = 'bienestar_control/index/$1';

$route['bienestar/encuesta/ingresar/(:num)'] = 'bienestar_control/ingresar/$1';
$route['bienestar/encuesta'] = 'bienestar_control/verificar_credenciales';
$route['bienestar/asistencia/(:num)'] = 'bienestar_control/asistencia/$1';

$route['bienestar/encuesta/ingresar/(:num)/invalido'] = 'bienestar_control/ingresar/$1/invalido';
$route['bienestar/encuesta/encuesta_enviada'] = 'bienestar_control/encuesta_success';

$route['bienestar'] = 'bienestar_control';
$route['bienestar/exportar_encuestas/(:num)'] = 'bienestar_control/exportar_excel_bienestar/$1';
$route['bienestar/exportar_solicitudes/(:num)/(:num)/(:any)/(:any)/(:any)'] = 'bienestar_control/exportar_excel_solicitudes/$1/$2/$3/$4/$5';
$route['bienestar/exportar_todas_encuestas'] = 'bienestar_control/exportar_excel_encuestas';
$route['eventos'] = 'bienestar_control/auto_ingreso';

$route['comite_presupuesto'] = 'presupuesto_control/index/comite_presupuesto';
$route['comite_presupuesto/(:num)'] = 'presupuesto_control/index/comite_presupuesto/$1';

$route['comite_index'] = 'proyectos_index_control/index/comite_index';
$route['comite_index/(:num)'] = 'proyectos_index_control/index/comite_index/$1';
$route['proyectos_index'] = 'proyectos_index_control/index';
$route['descargar_proyecto_index/(:num)'] = 'proyectos_index_control/descargar_proyecto_index/$1';

//$route['planeacion'] = "pages/cargar_modulo/planeacion";
//$route['planeacion/csep'] = 'talento_humano_control/index/csep';
//$route['planeacion/profesores'] = 'profesores_csep_control';
$route['profesores'] = 'profesores_csep_control';
$route['plan_trabajo/(:num)/(:any)'] = 'profesores_csep_control/ver_plan_profesor/$1/$2';
$route['descargar_plan_trabajo/(:num)'] = 'profesores_csep_control/descargar_plan/$1';
$route['profesores/descargar_excel/(:any)'] = 'profesores_csep_control/descargar_excel/$1';

$route['ascensos'] = 'ascensos_control';
$route['ascensos/(:num)'] = 'ascensos_control/index/$1';
$route['ascensos/descargar_acta/(:num)'] = 'ascensos_control/descargar_acta/$1';


// $route['calidad'] = "pages/cargar_modulo/calidad";

$route['calidad'] = 'calidad_control';
$route['calidad/(:num)'] = 'calidad_control/index/$1';
$route['calidad/asignacion/(:num)'] = 'calidad_control/asignacion/$1';


$route['salud'] = 'salud_control';
$route['salud/(:num)'] = 'salud_control/index/$1';
$route['salud/exportar_solicitudes/(:any)/(:num)/(:any)/(:any)/(:any)/(:any)'] = 'salud_control/exportar_excel_solicitudes/$1/$2/$3/$4/$5/$6';



//rutas módulo becas
$route['becas'] = 'becas_control';
$route['becas/(:num)'] = 'becas_control/index/$1';

$route['404_override'] = '';

//rutas modulo bienestar laboral
$route['bienestar_laboral'] = 'bienestar_laboral_control';
$route['bienestar_laboral/(:num)'] = 'bienestar_laboral_control/index/$1';

//cartas asesorías
$route['bienestar_laboral/generar_carta/(:num)'] = 'bienestar_laboral_control/generar_carta/$1';

//rutas módulo tickets
$route['tecnologia/tickets'] = 'tickets_control';
$route['tecnologia/tickets/(:num)'] = 'tickets_control/index/$1';

//profesores evaluacion
$route['profesores_eval'] = 'profesores_eval_control';
$route['profesores_eval/(:num)'] = 'profesores_eval_control/index/$1'; 
$route['profesores_eval/descargar_plan_profe/(:num)'] = 'profesores_eval_control/descargar_eval/$1';

$route['evaluacion'] = 'evaluacion_control';
$route['evaluacion/(:num)'] = 'evaluacion_control/index/$1';
$route['evaluacion/encuesta/(:num)'] = 'evaluacion_control/encuesta/$1';
$route['evaluacion/acta/(:num)'] = 'evaluacion_control/acta/$1';
$route['evaluacion/confirmar_acta/(:num)'] = 'evaluacion_control/confirmar_acta/$1';
$route['evaluacion/exportar_evaluacion/(:num)/(:any)/(:any)/(:num)/(:num)/(:any)'] = 'evaluacion_control/exportar_excel_evaluacion/$1/$2/$3/$4/$5/$6';
$route['evaluacion/exportar_resultados/(:num)/(:any)/(:num)/(:num)'] = 'evaluacion_control/exportar_excel_resultados/$1/$2/$3/$4';
$route['evaluacion/exportar_acta_retro/(:num)'] = 'evaluacion_control/exportar_acta/$1';
$route['evaluacion/exportar_competencias/(:num)/(:any)'] = 'evaluacion_control/exportar_resultados_competencias/$1/$2';

/* Modulo: Plan accion */
$route['plan_accion'] = 'plan_accion_control';
/* Fin plan de accion */


$route['talento_cuc'] = 'talento_cuc_control';
$route['talento_cuc/(:num)'] = 'talento_cuc_control/index/$1';
$route['talento_cuc/asistencia_formacion/(:num)'] = 'talento_cuc_control/asistencia_formacion/$1';
$route['talento_cuc/certificado/(:num)/(:any)'] = 'talento_cuc_control/descargar_certificado/$1/$2';
$route['talento_cuc/hoja_vida/(:num)'] = 'talento_cuc_control/hoja_vida/$1';
$route['talento_cuc/detalle_entrenamiento/(:num)/(:num)'] = 'talento_cuc_control/exportar_detalle_entrenamiento/$1/$2';
$route['talento_cuc/asistencia_entrenamiento/(:num)'] = 'talento_cuc_control/asistencia_entrenamiento/$1';
$route['talento_cuc/encuesta_entrenamiento/(:num)'] = 'talento_cuc_control/encuesta_entrenamiento/$1';
$route['talento_cuc/acta_cargo/(:num)'] = 'talento_cuc_control/acta_aceptacion_cargo/$1';
$route['talento_cuc/exportar_acta_cargo/(:num)'] = 'talento_cuc_control/exportar_acta_cargo/$1';
$route['talento_cuc/validar_actas_entrenamiento/(:num)'] = 'talento_cuc_control/validar_actas_entrenamiento/$1';

$route['talento_humano_adm'] = 'talento_humano_control/adm';
$route['talento_humano_adm/talento_hum'] = 'talento_humano_control';
$route['talento_humano_adm/evaluacion'] = 'evaluacion_control';
$route['talento_humano_adm/talento_cuc'] = 'talento_cuc_control';

//modulo publicaciones
$route['publicaciones'] = 'publicaciones_control';
$route['publicaciones/(:num)'] = 'publicaciones_control/index/$1';
$route['publicaciones/pago_papers'] = 'publicaciones_control/pago_papers';
$route['pago_papers'] = 'publicaciones_control/pago_papers';
$route['comite_bonificaciones'] = 'publicaciones_control/index/comite_bonificaciones';

$route['encuesta'] = 'encuesta_detalle_control';
$route['encuesta/(:num)'] = 'encuesta_detalle_control/index/$1';
$route['encuesta/encuesta_agil/(:num)'] = 'encuesta_detalle_control/encuesta/$1';

//Routes para supervisor
$route['supervisor_adm'] = 'supervisor_control/index/supervisor_adm';
$route['supervisor'] = 'supervisor_control/supervisor';

/* End of file routes.php */
/* Location: ./application/config/routes.php */