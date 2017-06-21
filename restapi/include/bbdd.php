<?php
	////CONEXION
	////////////////////////
	function conectar(){
	    include 'llavesBD.php';
	    $link=mysqli_connect($host, $user, $password) or die ("error de conexión");
	    mysqli_select_db($link, $dbname) or die ("error en selección de bbdd");
	    mysqli_set_charset($link, "utf8");
	    return $link;
	}
	function desconectar($link){
	    mysqli_close($link);
	}

?>