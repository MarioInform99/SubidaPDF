<?php
/**
 * Funcion que nos permite subir archivos pdf a un directorio en nuestro servidor
 *
 * @author David Vioque
 * @author Mario Salvatierra
 */

 /***********************************INICIO DE SCRIPT******************************************* */
global $wpdb;
/*** */
//Nombre de la tabla
$table_name="archivos";
//Atributo de la tabla
$AtributoNombre="nombre";
$AtributoApellidos="apellidos";
$AtributoRuta="ruta";
//Hemos usado el id como referencia para saber diferenciarlo y poder despues aumentar el digito

/**************************CONTROL DEL USAURIO********************* */
if(isset($_POST["btnSubmit"])){
	if(isset($_REQUEST["nombre"]) && !empty($_REQUEST["nombre"])){
		if(SoloLetras($_REQUEST["nombre"])){
			if(isset($_REQUEST["apellidos"]) && !empty($_REQUEST["apellidos"])){
				if(SoloLetras($_REQUEST["apellidos"])){
				// Comprobamos que se haya subido algun archivo
				if($_FILES['archivo']['error']!=UPLOAD_ERR_NO_FILE && !empty($_FILES["archivo"]["name"])){
					// Comprobamos que el archivo tenga un nombre (creo que no haria falta)
						// Comprobamos que el archivo nos haya llegado correctamente
					if($_FILES['archivo']['error']==UPLOAD_ERR_OK){
						//Evitamos meter espacios  en los archivos
						$UserNombre=str_replace(" ","-", $_REQUEST["nombre"]);
						$UserApellidos=str_replace(" ","-", $_REQUEST["apellidos"]);
						/**-------------------------------------------- */
						$nombre = $_FILES["archivo"]["name"];
						$tamanio = $_FILES["archivo"]["size"];
						$tipo = $_FILES["archivo"]["type"];
						/**--------------------------------------------- */
						$destino = get_template_directory()."/subida/";
						$nuevoNombre=$UserNombre."_".$UserApellidos;
						// Si el archivo tiene extension pdf se almacenara en nuestro servidor
						if($tipo=="application/pdf"){		
							/**------------------------------------------------------------ */
							/*------------------Comprobar que no existe nombre iguales----------- */
							$ArrayObjt=$wpdb->get_results("SELECT*FROM $table_name WHERE $AtributoNombre='".$_REQUEST["nombre"]."' "
														." AND $AtributoApellidos='".$_REQUEST["apellidos"]."'"
														." AND id=(SELECT MAX(id) FROM $table_name WHERE $AtributoNombre='".$_REQUEST["nombre"]."'"
														." AND $AtributoApellidos='".$_REQUEST["apellidos"]."');");
							/**------------En caso de que exista aumentamos un digito al final------- */
								$path=trim($destino.$nuevoNombre);
								$path=str_replace("\\", "/", $path);
							if(count($ArrayObjt)>0){
								$path=AumentarDigito($ArrayObjt);//Aumentamos el codigo que nos devuelve
								//La ruta del archivo
							}else{
								$path.=".pdf";//En caso de no encontrar similitud añadimos la extension
							}
							//path es la ruta del archivo y el nombre con el que se va a guardar
							//Comprobamos que el archivo se a movido correctamente a sitio
							if(move_uploaded_file($_FILES["archivo"]["tmp_name"], $path)){
								//Insertar datos en la tabla
								//Consulta preparada para evitar inyecciones SQL
								$wpdb->query($wpdb->prepare("INSERT INTO $table_name ($AtributoNombre,$AtributoApellidos,$AtributoRuta) VALUES"
								." (%s,%s,%s);",array($_REQUEST["nombre"],$_REQUEST["apellidos"],$path)));
								$mensaje = "Su archivo se ha subido correctamente";
	/*****************************MENSAJES DE AVISO AL USUARIO******************************************** */								
							}else{
								$mensaje="Problema al mover el archivo";
							}
						}
						else{
							$mensaje = "Solo se permite la subida de archivos con extension PDF";
						}
					}
				}
				else{
						$mensaje = "No se ha subido ningun archivo";
				}
				}else{
					$mensaje="Solo se admiten letras";
				}
			}
			}else{
				$mensaje="Solo se adminten letras";
			}
	}else{
		$mensaje="Campo vacio de nombre";
	}
 }


/*******************************FUNCIONES COMPLEMENTARIAS********************************************* */
//Aumentamos el el digito para evitar que el archivo se sobre escriba 
function AumentarDigito($array){
	//En este caso recibimos un array de objetos de la sentencia get_results
	//Posiblemente si el campo o atributo no se llame igual habra que modificarlo
	$pathIgual= $array[0]->ruta; //Ruta con nombre de archivo igual
	//Inicializamos la variables vacias, para posteriormente usarlas
	$pathNuevo="";
	$path="";
		for ($i=0; $i < strlen($pathIgual)-4; $i++) { 
			$pathNuevo.=substr($pathIgual,$i,1); //Evitamos coger pdf
		}
		//Comprobar si el tenemos un numero
	if(is_numeric(substr($pathNuevo,-1,1))){
		for ($j=0; $j < strlen($pathNuevo); $j++) { 
			if(strlen($pathNuevo)-1==$j){
				$indice=substr($pathNuevo,$j,1);
				$indice=intval($indice);
				$path.=($indice+1);
			}else{
				$path.=substr($pathNuevo,$j,1);
			}
		}
		$path.=".pdf";
	}else{
		$path=$pathNuevo.'1.pdf';
	}
	//Devolvemos la nueva ruta para el archivo
	return $path;
 }


 function SoloLetras($request){
	$letras="/^[a-zA-Z\s]*$/";
	//~[0-9]+~
	 if(preg_match($letras,$request)){
		return true;
	 }else{
		 return false;
	 }
 }