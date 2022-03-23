<?php
  $administra  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Admin_Sopor"?  true : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>AGIL</title>
  <!-- Favicon-->
  <link href="<?php echo base_url(); ?>imagenes/logo_cuc2.png" type="image/png" rel="shortcut icon" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet"
    type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/sweetalert.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/dropzone.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/genericos/css/stylesTalentoCuc.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  :root{
    /* #bdbdbd */
    --line-border-empty:#bdbdbd;
    --line-border-fill:#6e1f7c;
  }
  .container {
    text-align: center;
  }
  .progress-container{
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 1px;
    max-width: 100%;
    width:100%;
  }
  .progress-container::before{
    content: "";
    background-color: var(--line-border-empty);
    position:absolute;
    top: 50%;
    left:0;
    transform: translateY(-50%);
    height: 4px;
    width:100%;
    z-index:-1;
    transition: 0.4s ease;
  }
  .progress{
    background-color: var(--line-border-fill);
    position:absolute;
    top: 50%;
    left:0;
    transform: translateY(-50%);
    height: 4px;
    width:0%;
    z-index:-1;
    transition: 0.4s ease;
  }
  .circle{
    background-color:#fff;
    color:#999;
    border-radius: 50%;
    height: 50px;
    width:50px;
    border: 3px solid #b2bec3;
    display: flex;
    align-items: center;
    justify-content:center;
    transition: 0.4s ease;
  }

  .circle.active{
    border-color: var(--line-border-fill);
  }
  .btns{
    background-color:var(--line-border-fill);
    color: #fff;
    border-radius: 6px;
    border:0;
    cursor:pointer;
    padding:8px 30px;
    margin:5px;
  }
  .btns:focus{
    outline:0;
  }
  .btns.active{
    transform:scale(0.97);
  }
  .btns:disabled{
    background-color:var(--line-border-empty);
    cursor: not-allowed;
  } 
  .conti.act{
    display: none;
  } 

.login-container {
    position: relative;
    width: 300px;
    animation: mymove;
    animation-duration: 3s;
    padding: 20px 40px 40px;
    text-align: center;
    background: #fff;
    border: 1px solid #ccc;
    float: left;
    animation-timing-function: ease;
    animation-delay: 0s;
    animation-iteration-count: 1;
    animation-direction: normal;
    animation-fill-mode: none;
    animation-play-state: running;
    animation-name: mymove;
}
.login-container::before, .login-container::after {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    top: 3.5px;
    left: 0;
    background: #fff;
    z-index: -1;
    -webkit-transform: rotateZ(4deg);
    -moz-transform: rotateZ(4deg);
    -ms-transform: rotateZ(4deg);
    border: 1px solid #ccc;
}

.nombre_tabla {
    border: none !important;
    color: black;
    font-size: 13px;
    border-left: 4px solid #6e1f7c !important;
    margin-top: 11px;
    font-weight: normal;
    text-transform: uppercase;
    font-family: cuc;
}
.btn_cam {
  border: #6e1f7c 2px solid;
}
.btn_cam:hover {
    background-color: #6e1f7c; /* morado */
    color: white;
}
</style>
</head>

<body id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg  text-uppercase fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img alt="Agil" src="<?php echo base_url(); ?>imagenes/a_agil.png" width='35' />
      </a>
      <a class="navbar-brand js-scroll-trigger" href="#page-top">SUPERVISOR DE SALAS</a>
      <button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded"
        type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive"
        aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item mx-0 mx-lg-1" id="btn_mis_salas" style="cursor:pointer;"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger">Mis Salas</a></li>  
          <li class="nav-item mx-0 mx-lg-1" id="btn_mis_turnos" style="cursor:pointer;"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger">Mis Turnos</a></li>    
        </ul>
    </div>
  </nav>

  <header class="masthead bg-primary text-white ">
    <div class="container d-flex align-items-center flex-column">
      <!-- Masthead Avatar Image-->
      <img class="masthead-avatar mb-5" src="<?php echo base_url(); ?>imagenes_personas/<?php echo $foto; ?>" alt="" />
      <!-- Masthead Heading-->
      <h1 class="masthead-heading text-uppercase mb-0"><?php echo $nombre ?></h1>
      <!-- Icon Divider-->
      <div class="divider-custom divider-light">
        <div class="divider-custom-line"></div>
        <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
        <div class="divider-custom-line"></div>
      </div>
      <!-- Masthead Subheading-->
      <p class="masthead-subheading font-weight-light mb-0" id='cargo'><?php echo $cargo; ?></p>
      <br>
      <button class="btn btn-secondary" id='btn_detalle_supervisor'>Detalles</button>
    </div>
  </header>

     <!-- About Section-->
     <section class="page-section bg-secundary text-secondary mb-0" id="entrada">
      <div class="container d-flex align-items-center flex-column">
        <input type="hidden" name="estado" id="estado">
        <h1 class="page-section-heading text-center text-uppercase text-secondary mb-0">SEGUIMIENTO SUPERVISOR DE SALA</h2>
        <!-- Icon Divider-->
        <div class="divider-custom">
          <div class="divider-custom-line"></div>
          <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
          <div class="divider-custom-line"></div>
        </div>
        <?php if(sizeof($turno)!=0){
          ?>
            <div class="progress-container">
              <div class="progress" id="progress"></div>
                <div class="circle active" id="circle-uno"><i class="fa-solid fa-door-open" style="font-size: 1.5rem; color:black;"></i></div>
                <div class="circle" id="circle-dos"><i class="fa-solid fa-check" style="font-size: 1.5rem;color:black;"></i></div>
                <div class="circle" id="circle-tres"><i class="fa-solid fa-check-double" style="font-size: 1.5rem;color:black;"></i></div>
                <div class="circle" id="circle-cuatro"><i class="fa-solid fa-door-closed" Style="font-size: 1.5rem;color:black;"></i></div>
            </div>  <br>
            <!-- <button class="btns retroceder" disabled id="prev">PREV</button>
            <button class="btns avanzar" id="next">NEXT</button> -->
        <?php } ?>    
      </div>
      <?php if(sizeof($turno)!=0){  ?>
      <div class="container conti">
        <br><br>
      
        <h2 class="page-section-heading text-center text-uppercase mb-0 text-black">ENTRADA</h2>      <!-- Icon Divider-->
          <div class="divider-custom divider-black">
            <div class="divider-custom-line"></div>
            <div class="divider-custom-icon"><i class="fa-solid fa-door-open"></i></div>
            <div class="divider-custom-line"></div>
          </div>
          <div id="contenedor_entrada"></div>
      </div>
      <div class="container conti act"><br><br>
      <h2 class="page-section-heading text-center text-uppercase  mb-0 text-black">REVISIÓN ENTRADA</h2>
        <!-- Icon Divider-->
        <div class="divider-custom divider-black">
          <div class="divider-custom-line"></div>
          <div class="divider-custom-icon"><i class="fa-solid fa-check"></i></div>
          <div class="divider-custom-line"></div>
        </div>
        <div id="contenedor_rev_entrada" class="row justify-content-center"></div>
        <div id="contenedor_btn_rev_entrada"></div>
      </div>
      <div class="container conti act"><br><br>
        <h2 class="page-section-heading text-center text-uppercase  mb-0 text-black">REVISIÓN SALIDA</h2>
          <!-- Icon Divider-->
          <div class="divider-custom divider-black">
            <div class="divider-custom-line"></div>
            <div class="divider-custom-icon"><i class="fa-solid fa-check-double"></i></div>
            <div class="divider-custom-line"></div>
          </div>
          <div id="contenedor_rev_salida" class="row justify-content-center"></div>
          <div id="contenedor_btn_rev_salida"></div>
          
      </div>
      <div class="container conti act">
        <br><br><br>
        <h2 class="page-section-heading text-center text-uppercase mb-0 text-black">SALIDA</h2>
        <!-- Icon Divider-->
        <div class="divider-custom divider-black">
          <div class="divider-custom-line"></div>
          <div class="divider-custom-icon" ><i  class="fa-solid fa-door-closed"></i></div>
          <div class="divider-custom-line" ></div>
        </div>
        <div id="contenedor_salida"></div>
      </div>
      <?php } else{?>  
        <div class="container"><br><br>
        <br>    
          <h5 class=" mb-0 text-black">Usted no se encuentra de turno el dia de hoy</h5>
          <div class="divider-custom divider-black">
            <div class="divider-custom-icon" ><i class="fa-solid fa-calendar-xmark"  style="font-size: 2.5rem;"></i></i></div>
          </div>
        </div>
      <?php  
      } ?>  
  </section>
    <!-- Footer-->
  <!-- Copyright Section-->
  <div class="copyright py-4 text-center text-white">
    <div class="container"><small>Copyright © <?php echo date("Y")?> Universidad de la Costa CUC</small></div>
  </div>
  <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
  <div class="scroll-to-top d-lg-none position-fixed">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i
        class="fa fa-chevron-up"></i></a>
  </div>
<!-- Modals -->
  <div class="portfolio-modal modal fade" id="Modal_Entrada" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="fixed fixed_viaticos div_camara">
        <div class="reque">
            <div class="login-container">
            <table class="" id="" style="width: 100%">
              <thead class="" style="display: table-header-group;">
                <tr class="">
                  <td colspan="" class="nombre_tabla"> VISTA PREVIA</td>
                </tr>
              </thead>
            </table>
                <div class="form-boxw text-left">
                    <div class="">
                        <canvas id="foto_entrada" class="img-thumbnail"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-center  text-uppercase mb-0 titulo_entrada_salida" id="portfolioModal6Label">Entrada</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_entrada_salida">
                  <div class="row">
                        <div class="div_camara">
                            <video id="camara" playsinline autoplay muted class="img-thumbnail"></video>
                            <span id='botonFoto' class="btn btn-default btn_cam mt-3"><span class=" fa fa-camera red"></span> Tomar Foto</span>
                        </div>
                    <input type="hidden" name="tipo" id="tipo">
                  </div>
                  <button type="submit"class="btn btn-primary btn-block mt-3">REGISTRAR</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="Modal_Revision" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="fixed fixed_viaticos div_camara">
        <div class="reque">
            <div class="login-container">
            <table class="" id="" style="width: 100%">
              <thead class="" style="display: table-header-group;">
                <tr class="">
                  <td colspan="" class="nombre_tabla"> VISTA PREVIA</td>
                </tr>
              </thead>
            </table>
                <div class="form-boxw text-left">
                    <div class="">
                        <canvas id="foto_revision" class="img-thumbnail"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-center  text-uppercase mb-0 titulo_revision" id="portfolioModal6Label">Revision</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <form id="form_guardar_revision">
                  <div class="row">
                        <div class="div_camara">
                            <video id="camara_r" autoplay muted class="img-thumbnail"></video>
                            <span id='botonFotoR' class="btn btn-default btn_cam"><span class=" fa fa-camera red"></span> Tomar Foto</span>
                        </div>
                    <input type="hidden" name="tipo_revision" id="tipo_revision">
                    <input type="hidden" name="sala_sup" id="sala_sup">
                    <textarea name="observacion_revision" required class="form-control mt-3" placeholder='Descripción'></textarea>
                  </div>
                  <button type="submit"class="btn btn-primary btn-block mt-3">REGISTRAR</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="portfolio-modal modal fade" id="modal_salas_turnos" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="nombre_modal_salas_turnos">DETALLE</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Text-->
                  <table class="table  table-hover table-condensed " id="tabla_salas_turnos" cellspacing="0" width="100%">
                    <thead></thead>
                    <tbody></tbody>
                  </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="portfolio-modal modal fade" id="modal_detalle_supervisor" tabindex="-1" role="dialog"
    aria-labelledby="portfolioModal6Label" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
        <div class="modal-body text-center">
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <!-- Portfolio Modal - Title-->
                <h2 class="portfolio-modal-title text-secondary text-uppercase mb-0" id="portfolioModal6Label">DETALLES
                </h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                  <div class="divider-custom-line"></div>
                  <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                  <div class="divider-custom-line"></div>
                </div>
                <!-- Portfolio Modal - Image-->
                  <div class="row">
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



  <!-- Bootstrap core JS-->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/jquery-2.2.1.js"></script>
  <script src="<?php echo base_url(); ?>js-css/estaticos/js/sweetalert.min.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/General.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Genericas.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/Usuarios.js"></script>
  <script src="<?php echo base_url(); ?>js-css/genericos/js/supervisor.js"></script> 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Third party plugin JS--> 
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

  <!-- Contact form JS-->
  <!-- Core theme JS-->
 
<script>
  //  activarfile();
  console.log(<?php echo $ultimo_estado ?>);
obtener_estado_supervisor(<?php echo $_SESSION['persona'] ?>);
</script>
  
  </body>
</html>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/scriptsTalentoCuc.js"></script>
<script src="<?php echo base_url(); ?>js-css/estaticos/js/dropzone.js"></script>
