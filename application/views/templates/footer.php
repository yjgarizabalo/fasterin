<?php if(isset($_SESSION['perfil'])){?> 
    <!-- <div class="modal fade" id="modal_informacion_app" role="dialog">
      <div class="modal-dialog modal-95">
        <div class="modal-content">
          <div class="modal-header" id="headermodal">
            <button type="button" class="close" data-dismiss="modal"> X</button>
            <h3 class="modal-title"><span class="fa fa-file-video-o"></span> Videos Teams</h3>
          </div>
          <div class="modal-body" id="bodymodal">
          <p class='text-center'>AQUÍ TE MOSTRAMOS COMO USAR TEAMS PARA TU TRABAJO EN CASA.</p>
          <iframe style="width:100%; height : 400px; border : none" src="https://web.microsoftstream.com/embed/channel/11952363-9c9f-4744-836f-13c51a985636?sort=trending" allowfullscreen style='border:none;'></iframe>
          </div>
          <div class="modal-footer" id="footermodal">
            <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
          </div>
        </div>
      </div>
    </div> -->
<?php }?>
    
  <div class="modal fade" id="modal_notificaciones_general" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="headermodal">
          <button type="button" class="close" data-dismiss="modal"> X</button>
          <h3 class="modal-title"><span class="fa fa-bell"></span> Notificaciones Generales</h3>
        </div>
        <div class="modal-body" id="bodymodal">
          <div id="panel_notificaciones_general" style="width: 100%" class="list-group"></div>
          <div id="panel_notificaciones_solicitudes_general" style="width: 100%" class="list-group"></div>
        </div>
        <div class="modal-footer" id="footermodal">
          <button type="button" class="btn btn-default active" data-dismiss="modal"><span
              class="glyphicon glyphicon-resize-small"></span>Cerrar</button>
        </div>
      </div>
    </div>
  </div>

</section>
<footer class="footer">
    <p><span id="info-footer">Copyright © <?php echo date("Y") ?> Universidad de la Costa CUC | <a style=" color: #666666;"class="sinlink" href="<?php echo base_url(); ?>politicadedatos.pdf" target="_blank">Política de Protección de Datos. </a>| V2.1902.1</p> 
</footer>
<script>
$(document).ready(function () {
  num_notificaciones_general();
  mostrar_notificaciones_general();
<?php if(isset($_SESSION['perfil'])){?> traer_perfil_activo("<?php echo $_SESSION['perfil'];?>", "<?php echo $mensaje;?>") <?php }?>
});
</script>
</body>
</html>
