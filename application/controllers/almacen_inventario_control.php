<?php

class almacen_inventario_control extends CI_Controller {

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;

    public function __construct() {
        parent::__construct();
        $this->load->model('almacen_model');
		$this->load->model('genericas_model');
		$this->load->model('compras_model');
		include('application/libraries/festivos_colombia.php');
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }

    public function index() {
        if ($this->Super_estado) {
			$pages = $this->get_route();
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
            if (!empty($datos_actividad)) {
				$pages = "almacen_inventario";
				$data['js'] = "Almacen_inventario";
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            }else{
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }
        }else{
			$pages = "inicio";
			$data['js'] = "";
			$data['actividad'] = "Ingresar";
		}
		$this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
	}

	public function Listar_articulos(){
        $articulos = array();
        if ($this->Super_estado == false) {
            echo json_encode($articulos);
            return;
		}
		$accion = $this->input->post("accion");
		$tipo_modulo = $this->input->post("tipo_modulo");
		$categoria = $this->input->post("categoria");
		if ($categoria != '%') {
			$categoria = "%$categoria%";
		}
		$bodega = $this->input->post("bodega");
		if ($bodega != '%') {
			$bodega = "%$bodega%";
		}
		$datos = $this->almacen_model->Listar_articulos($accion, $categoria, $bodega, $tipo_modulo);
		// sw sirve para identificar si hay artículos con cantidades por debajo de su stock minimo
		$sw = false;
		$sw_final = false;
        foreach ($datos as $row) {
			$bgcolor = '#white';
			$color = 'black';
			if ($row['cantidad'] <= $row['min_stock']) {
				$bgcolor = '#d9534f';
				$color = 'white';
				if (!$sw) {
					$sw_final = true;
				}
				$sw = true;
			}
			$row["ver"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: ' . $bgcolor . ';color: '. $color .'; width: 100%; ;" class="pointer form-control"><span>ver</span></span>';
			$row["gestion"] = "<span style='color: #d9534f;' title='Restar Artículos' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-minus-square' onclick='mostrar_restar_cantidad(". $row['id'] .")' ></span>". ' ' ."<span style='color: #2E79E5;' title='Sumar Artículos' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-plus-square' onclick='mostrar_agregar_cantidad(". $row['id'] .")' ></span>". ' ' . "<span style='color: #f2960d;' title='Cantidad Disponible' data-toggle='popover' data-trigger='hover' class='btn btn-default fa fa-archive pointer' onclick='cantidad_disponible(". $row['id'] .")'></span>";
			$articulos["data"][] = $row;
		}
		$articulos["sw"] = $sw;
        echo json_encode($articulos);
	}

	public function listar_permisos_por_parametro(){
        $perfiles = array();
        if ($this->Super_estado == false) {
            echo json_encode($perfiles);
            return;
		}
		$vp_p = $this->input->post('vp_p');
		$id_p = $this->input->post('id_p');
		$aux = $this->input->post('aux');
		$datos = $this->almacen_model->listar_permisos_por_parametro($vp_p);
		$i = 0;
        foreach ($datos as $row) {
			if ($aux) {
				$i++;
				$row["num"] = $i;
				if (is_null($row["estado"])) {
					$class = "fa-toggle-off";
					$msj = "Quitar perfil";
				}else{
					$class = "fa-toggle-on";
					$msj = "Asignar perfil";
				}
				$x=$row['id'];
				$x1=$row['id_aux'];

				$row["opciones"] = "<span id='btn$i' style='color:green' title='$msj' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa $class' onclick='gestionar_perfil($x,".json_encode($x1).",$id_p,".json_encode($vp_p).", $i)'></span>";
			}
			$perfiles["data"][] = $row;
		}
		echo json_encode($perfiles);
	}

	public function cargar_bodegas(){
		$bodegas = array();
        if ($this->Super_estado == false) {
            echo json_encode($bodegas);
            return;
		}
		$modulo = $this->input->post('tipo_modulo');
		$datos = $this->almacen_model->cargar_bodegas($modulo);
        foreach ($datos as $row) {
			$bodegas["data"][] = $row;
		}
		echo json_encode($bodegas);
	}

	public function guardar_articulo(){
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
				$tipo = $this->input->post("tipo");
				$tipo_modulo = $this->input->post("tipo_modulo");
				if ($tipo == 2 || $tipo == 3 || $tipo == 4 || $tipo == 5) {
					if ($tipo == 4 || $tipo == 5 || $tipo == 2) {
						if ($tipo != 2) {
							$id = $this->input->post("id");
							if (ctype_space($id) || empty($id) || !is_numeric($id)) {
								echo json_encode(-10);
								return; 
							}
						}
					}
					$articulo = $this->input->post("articulo");
					if (ctype_space($articulo) || empty($articulo) || !is_numeric($articulo)) {
						echo json_encode(-9);
						return; 
					}
				} else{
					//Se guardará en inventario. El Código debe ser único.
					$codigo = $this->input->post("codigo");
					if (strlen($codigo) > 10) {
						echo json_encode(-2);
						return;
					}
					$resp = (int)$this->almacen_model->validar_codigo($codigo);
					if ($resp > 0) {
						echo json_encode(-3);
						return;
					}
					$categoria = $this->input->post("categoria");
					$bodega = $this->input->post("bodega");
					$valor = $this->input->post("valor");
					$stock = $this->input->post("stock");
					$nombre = $this->input->post("nombre_art");
                	$marca = $this->input->post("marca");
                	$referencia = $this->input->post("referencia_art");
                	$unidades = $this->input->post("unidades_art");
				}
				$cantidad = $this->input->post("cantidad_art");
				$observaciones = $this->input->post("observaciones");
				$usuario = $_SESSION['persona'];
				
				//Se valida que la cantidad de artículos sea válido
                if (ctype_space($cantidad) || empty($cantidad) || !is_numeric($cantidad) || $cantidad < 1) {
                    echo json_encode(-4);
                    return;
				}

				//Se valida que el nombre del artículo sea válido
                if (ctype_space($nombre) || empty($nombre)) {
                    echo json_encode(-5);
                    return;
				}

				//Se valida que el stock del artículos sea válido
                if (ctype_space($stock) || empty($stock) || !is_numeric($stock) || $stock < 1) {
                    echo json_encode(-1);
                    return;
				}

				//Se valida que el valor del artículos sea válido
                if (ctype_space($valor) || empty($valor) || !is_numeric($valor) || $valor < 1) {
                    echo json_encode(-6);
                    return;
				}
				//Se valida que la bodega elegida sea válido
				if (ctype_space($bodega) || empty($bodega)) {
                    echo json_encode(-7);
                    return;
				}
				if (ctype_space($categoria) || empty($categoria)) {
                    echo json_encode(-8);
                    return;
				}
				if (ctype_space($unidades) || empty($unidades)) {
                    echo json_encode(-15);
                    return;
				}
                $resp = $this->almacen_model->guardar_articulo($codigo, $nombre, $categoria, $bodega, $cantidad, $stock, $marca, $referencia, $valor, $usuario, $observaciones, $tipo_modulo, $unidades);
                echo json_encode($resp);
            }
        }
	}

	public function cambiar_cant_articulo(){
		if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_modifica == 0) {
            echo json_encode(-1302);
            return;
        }
		$articulo = $this->input->post("art");
		if (ctype_space($articulo) || empty($articulo) || !is_numeric($articulo)) {
            echo json_encode(-1);
            return;
		}
		$cantidad = $this->input->post("cant");
		if (ctype_space($cantidad) || empty($cantidad) || !is_numeric($cantidad)) {
            echo json_encode(-2);
            return;
		}
		$obs = $this->input->post("obs");
		if (ctype_space($obs) || empty($obs)) {
            echo json_encode(-3);
            return;
		}
		$op = $this->input->post("op");
		if (ctype_space($op) || empty($op)) {
            echo json_encode(-5);
            return;
		}
		$resp = $this->almacen_model->cambiar_cant_articulo($articulo, $cantidad, $obs, $op);
		if ($resp == 0) {
			echo json_encode(-4);
			return;
		}
		echo json_encode($resp);
	}

	//Función Modificar Artículo
    public function modificar_articulo(){
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_modifica == 0) {
            echo json_encode(-1302);
            return;
        }
		$id = $this->input->post("id");
		$codigo_actual = $this->input->post("codigo_actual");
        $codigo = $this->input->post("codigo");
		$nombre = $this->input->post("nombre_articulo");
		$categoria = $this->input->post("categoria");
		$bodega = $this->input->post("bodega");
		$stock = $this->input->post("stock");
		$unidades = $this->input->post("unidades_art");
		$marca = $this->input->post("marca");
		$referencia = $this->input->post("referencia_art");
		$valor = $this->input->post("valor");
		$observacion = $this->input->post("observaciones");
		
		if ($codigo_actual != $codigo) {
			$resp = (int)$this->almacen_model->validar_codigo($codigo);
			if ($resp > 0) {
				echo json_encode(-7);
				return;
			}
		}
        if (ctype_space($codigo) || empty($codigo)) {
            echo json_encode(-1);
            return;
		}
		if (strlen($codigo) > 10) {
			echo json_encode(-8);
			return;
		}
		if (ctype_space($nombre) || empty($nombre)) {
            echo json_encode(-2);
            return;
        }
		if (ctype_space($unidades) || empty($unidades)) {
            echo json_encode(-10);
            return;
        }
        if (ctype_space($bodega) || empty($bodega)) {
            echo json_encode(-3);
            return;
		}
        if (ctype_space($valor) || empty($valor) || !is_numeric($valor) || ($valor < 0)) {
            echo json_encode(array(-5));
            return;
		}
		if (ctype_space($categoria) || empty($categoria)) {
            echo json_encode(-6);
            return;
		}
		if (ctype_space($stock) || empty($stock) || !is_numeric($stock) || ($stock < 0)) {
            echo json_encode(array(-9));
            return;
		}
        $resp = $this->almacen_model->Modificar_articulo($id, $codigo, $nombre, $categoria, $bodega, $marca, $referencia, $stock, $valor, $observacion, $unidades);
        echo json_encode($resp);
	}

	public function cantidad_disponible(){
		if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_modifica == 0) {
            echo json_encode(-1302);
            return;
        }
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
            echo json_encode(-1);
            return;
		}
		$stock = $this->almacen_model->get_existencia_articulo($id);
		$solicitados = (int)$this->almacen_model->get_cantidad_solicitada($id);
		$resp = $stock - $solicitados;
		echo json_encode([$resp, $solicitados]);
		return;
	}

	public function gestionar_perfil(){
		if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
		}
		if ($this->Super_modifica == 0) {
            echo json_encode(-1302);
            return;
        }
		$id_p = $this->input->post("id_p");
		if (ctype_space($id_p) || empty($id_p) || !is_numeric($id_p)) {
            echo json_encode(-1);
            return;
		}
		$id_s = $this->input->post("id_s");
		if (ctype_space($id_s) || empty($id_s) || !is_numeric($id_s)) {
            echo json_encode(-1);
            return;
		}
		$vp_secundario = $this->input->post("vp_secundario");
		$vp_principal = $this->input->post("vp_principal");
		$res = $this->almacen_model->gestionar_permiso($vp_principal, $vp_secundario, $id_p, $id_s);
		echo json_encode($res);
		return;
	}

	public function listar_categorias(){
        $cats = array();
        if ($this->Super_estado == false) {
            echo json_encode($cats);
            return;
		}
		$datos = $this->almacen_model->listar_categorias();
		$i = 1;
        foreach ($datos as $row) {
			$row["num"] = $i;
			$row["opciones"] = "<span style='color:#2E79E5' title='Administrar Categoria' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-cog' onclick='mostrar_perfiles(`". $row['id'] ."`, `".$row['id_aux']."`)'></span>";
			$cats["data"][] = $row;
			$i++;
		}
		echo json_encode($cats);
	}

	public function traer_articulo(){
        if ($this->Super_estado == false) {
            echo json_encode(array());
            return;
        }
        if ($this->Super_agrega == 0) {
            echo json_encode(array(-1302));
        } else {
            $idarticulo = $this->input->post("id");
            $articulo = $this->almacen_model->Traer_articulo($idarticulo);
            echo json_encode($articulo);
        }
	}

	public function get_route(){
		$pages = $_SERVER['REQUEST_URI'];
		$pos = strrpos($pages, "index.php/");
		$pages =  preg_replace('/[0-9]+/', '', substr($pages, $pos+10, strlen($pages)));
		$cant = strlen($pages);
		if($pages[$cant-1] == '/') $pages = substr($pages, 0, -1);
		return $pages;
	}

	public function listar_unidades(){
		if ($this->Super_estado == false) {
            echo json_encode(array());
            return;
        }
		$unidades = $this->almacen_model->get_where('valor_parametro', ['idparametro' => 186, 'estado' => 1])->result_array();
		echo json_encode($unidades);
	}

	public function guardar_nuevas_unidades(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else{
			if ($this->Super_agrega == 0) $res = ['mensaje'=> 'No tiene permisos para realizar esta acción', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			else {
				$nombre = $this->input->post("nombre");
				$validar = $this->almacen_model->get_where('valor_parametro', ['valor' => $nombre, 'idparametro' => 186, 'estado' => 1])->result_array();
				if(count($validar)){
					$res = ['mensaje'=> 'Ya existen unas unidades con este nombre', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				} else {
					$data = ['valor' => $nombre, 'idparametro' => 186, 'usuario_registra' => $_SESSION['persona']];
					$add = $this->almacen_model->guardar_datos($data, 'valor_parametro');
					$res = !$add
						? ['mensaje'=> 'Unidades agregadas exitosamente', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje'=> 'Ha ocurrido un error al intentar guardar las nuevas unidades', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				}
			}
		}

		echo json_encode($res);
	}

	public function eliminar_unidades(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else{
			if ($this->Super_elimina == 0) $res = ['mensaje'=> 'No tiene permisos para realizar esta acción', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			else {
				$id = $this->input->post("id");
				$validar = $this->almacen_model->get_where('articulos_almacen', ['unidades' => $id])->result_array();
				if(count($validar)) $res = ['mensaje'=> 'No se puede eliminar estas unidades porque está asignado a algunos artículos !', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				else{
					$elimina = $this->almacen_model->eliminar_datos('valor_parametro', $id);
					if(!$elimina) $res = ['mensaje'=> 'Unidades eliminadas exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
					else $res = ['mensaje'=> 'Ha ocurrido un error al intentar eliminar estas unidades', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				}
			}
		}
		echo json_encode($res);
	}

	public function listar_articulos_a_comprar(){
		$resp = [];
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$bodega = $this->input->post("bodega");
			$porc = $this->genericas_model->obtener_valores_parametro_aux('PorArtAlm', 20);
			$porc = $porc ? intval($porc[0]['valory'])/100 : 1;
			$resp = $this->almacen_model->listar_articulos_compras_por_bodega($bodega, $porc);
		}
		echo json_encode($resp);
	}

	public function enviar_compra(){
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$articulos = $this->input->post("articulos");
			$jefe = $this->input->post("jefe");
			$cod_sap = $this->input->post("cod_sap");
			if (!$jefe) $resp = ['mensaje' => "Seleccione el jefe directo.", 'tipo' => "info", 'titulo' => "Oops..!", 'usuario' => ''];
			else if (!$cod_sap) $resp = ['mensaje' => "Seleccione codigo SAP.", 'tipo' => "info", 'titulo' => "Oops..!", 'usuario' => ''];
			else if (!$articulos) $resp = ['mensaje' => "No hay articulos para enviar.", 'tipo' => "info", 'titulo' => "Oops..!", 'usuario' => ''];
			else {
				$fecha_solicitud = date("Y-m-d H:i");
				$depar = null;
				$adjunto = null;
				$tipo_compra = "Soli_Sin";
				$observaciones = "";
				$usuario = $_SESSION['persona'];

				$add_solicitud = $this->compras_model->guardar_solicitud(null, $tipo_compra, $usuario, $observaciones, $jefe, $depar, $fecha_solicitud, $adjunto);
				$info = $this->compras_model->obtener_correo_solicitante($add_solicitud);
				$info->{'solicitud'} = $add_solicitud;
				$resp = ['mensaje' => "Información almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso exitoso!", 'usuario' => $info];
				if ($add_solicitud <= 0) {
					$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!", 'usuario' => ''];
				} else {
					$articulos_a_guardar = array();
					foreach ($articulos as $art) {
						array_push($articulos_a_guardar, array(
							"id_solicitud" => $add_solicitud,
							"cod_sap" => $cod_sap,
							"nombre_articulo" => $art['nombre_art_comp'],
							"marca" => $art['marca_art_comp'],
							"referencia" => $art['referencia_art_comp'],
							"cantidad" => $art['cantidad_art_comp'],
							"observaciones" => $art['observaciones_comp'],
							"id_almacen" => $art['id_art_comp'],
							"fecha_compra_tarjeta" => null,
							"usuario_crea" => $usuario,
						));
					}

					$add_articulos = $this->compras_model->guardar_general($articulos_a_guardar, "articulos_solicitud");
					if ($add_articulos == "error") {
						$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!", 'usuario' => ''];
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function obtener_valores_permiso_almacen(){
        $vp_principal = $this->input->post('vp_principal');
        $idparametro = $this->input->post('idparametro');
        $resp = $this->Super_estado ? $this->almacen_model->obtener_valores_permiso($vp_principal, $idparametro) : array();
        echo json_encode($resp);
    }
	
}
