<?php
/**
 * Funcion que nos permite subir archivos pdf a un directorio en nuestro servidor
 *
 * @author David Vioque
 */
global $wpdb;

if(isset($_POST["btnSubmit"])){
	// Comprobamos que se haya subido algun archivo
	if((isset($_POST["nombre"]) && !empty($_POST["nombre"])) && (isset($_POST["apellidos"])) && !empty($_POST["apellidos"])){
		$nombreUsuario = $_POST["nombre"];
		$apellidos = str_replace(' ', '', $_POST["apellidos"]);
		if($_FILES['archivo']['error']!=UPLOAD_ERR_NO_FILE){
		// Comprobamos que el archivo tenga un nombre (creo que no haria falta)
			if(!empty($_FILES["archivo"]["name"])){
				// Comprobamos que el archivo nos haya llegado correctamente
				if($_FILES['archivo']['error']==UPLOAD_ERR_OK){
					$tamanio = $_FILES["archivo"]["size"];
					$tipo = $_FILES["archivo"]["type"];
					$destino = get_template_directory()."/subida/";
					$nombre = $nombreUsuario."_".$apellidos;
					$existeUsuario = $wpdb->get_results("select ruta from archivos where nombre='".$nombreUsuario."' and apellidos='".$apellidos."'");
					if(!empty($existeUsuario)){
						$numero = count($existeUsuario);
						$aux = $existeUsuario[0]->ruta;
						$ultimaLetra = substr($existeUsuario[0]->ruta, -5);
						$esNumero = $ultimaLetra[0];
						if(is_numeric($esNumero)){
							$nombre = substr_replace($aux, $numero, -5, 1);
							$ruta = $nombre;
							// Si el archivo tiene extension pdf se almacenara en nuestro servidor
							if($tipo=="application/pdf"){		
								move_uploaded_file($_FILES["archivo"]["tmp_name"], $nombre);
								$wpdb->insert("archivos", array("nombre" => $nombreUsuario, "apellidos" => $apellidos, "ruta"=>$ruta));
								$mensaje = "Su archivo se ha subido correctamente";
							}
							else{
								$mensaje = "Solo se permite la subida de archivos con extension PDF";
							}
						}
						else{
							$nombre = substr_replace($aux, $numero, -4, 0);
							$ruta = $nombre;
							// Si el archivo tiene extension pdf se almacenara en nuestro servidor
							if($tipo=="application/pdf"){		
								move_uploaded_file($_FILES["archivo"]["tmp_name"], $nombre);
								$wpdb->insert("archivos", array("nombre" => $nombreUsuario, "apellidos" => $apellidos, "ruta"=>$ruta));
								$mensaje = "Su archivo se ha subido correctamente";
							}
							else{
								$mensaje = "Solo se permite la subida de archivos con extension PDF";
							}
						}

					}
					else{
						echo "entramos en el else de no hay archivo";
						$ruta = $destino.$nombre.".pdf";
						// Si el archivo tiene extension pdf se almacenara en nuestro servidor
						if($tipo=="application/pdf"){		
							move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino.$nombre.".pdf");
							$wpdb->insert("archivos", array("nombre" => $nombreUsuario, "apellidos" => $apellidos, "ruta"=>$ruta));
							$mensaje = "Su archivo se ha subido correctamente";
						}
						else{
							$mensaje = "Solo se permite la subida de archivos con extension PDF";
						}
					}
				}
	 		}
			else{
					$mensaje = "No se ha subido ningun archivo";
	 		}
	}
	else{
		$mensaje = "error nombre";
	}
 }
}

?>