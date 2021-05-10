<?php
	/*
	* Template Name: subirArchivos
	* Template Post Type: post, page, product
	*/
	get_header();
	include_once get_template_directory()."/funciones/archivosModel.php";
 ?>
	<form method="POST" action="" enctype="multipart/form-data">
	<label>Nombre:
		<input type="text" name="nombre"/>
	</label>
	<label>Apellidos:
		<input type="text" name="apellidos"/>
	</label>

		<input type="file" name="archivo"/><br/><br/>
		<input type="submit" name="btnSubmit"/><br><br/>
	</form>
 <?php
	if(isset($mensaje)){
		echo "<p>$mensaje</p>";
	}
	get_footer();
 ?>