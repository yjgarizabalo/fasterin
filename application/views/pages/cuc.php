<script src="<?php echo base_url(); ?>js-css/genericos/js/profesores_csep.js"></script>
<div class="text-center  container"  id="menu_principal">
   <div class="content-menu" style='background-image: url("<?php echo base_url(); ?>/imagenes/logo_fasterin.svg")'>
   </div> 
   <div id="container-principal2">
        <h3 class="titulo_menu"><i class="bx bx-menu"></i> MENÚ</h3>
        <div class="input-group col-sm-4" style="float: left">
          <input class="form-control input-sm" id="busca_params" style="border-radius: 5px" placeholder="Escriba el módulo">
        </div><br>        
        <div class="row">                
        <div id="container_listado_menu"></div>                
    </div>
<script>    
    $(document).ready(function () {
        inactivityTime();
        mostrar_plan_sesion();
        cargar_datos('<?php echo json_encode($actividades)?>')             
    });
</script>
<!--
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5b4eb90e91379020b95ef6ef/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
-->
