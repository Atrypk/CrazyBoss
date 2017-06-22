# CrazyBoss

Versiones de software en desarrollo:
	*PHP - 5.6.16
	*MySql - 5.7.9
	*Slim Framework - 3.8.1

	///////////////////////////
	//Configuración necesaria//
	///////////////////////////

	* mod_rewrite habilitado en el archivo .htaccess

	//////////////////////.htaccess////////////////////////
		RewriteEngine On
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteRule ^(.*)$ %{ENV:BASE}index.php [QSA,L]
	///////////////////////////////////////////////////////

	*Creacion de los datos de conexión a la base de datos del fichero llavesBD.php

	//////////////////////llavesBD.php//////////////////////
	<?php
		$user="user";
		$password="pass";
		$host="localhost";
		$dbname="BD_restapi";
		$link = new mysqli($host,$user,$password,$dbname);
	?>
	///////////////////////////////////////////////////////



	////////////
	//Usuarios//
	////////////

	Jefe -> passJefe (En md5 -> f4391d34a7d8d263d995772d23d08efb )

	Marty -> passMarty (En md5 -> f2c34be0f565e6fa5fd0173d98cca3cf )

	Doc -> passDoc (En md5 -> 8f52abbd80badb8d99b02219f8f02bc7 )

	//Como conectarse
	Creamos el json del usuario que vamos a conectar de la siguiente forma
	{ "usuario":AquiTuUsuario , "password":AquiLaPassEnMD5 }

	Ejemplo:
	{ "usuario":"doc" , "password":"8f52abbd80badb8d99b02219f8f02bc7" }

	Enviamos el JSON mediante POST a la ruta TuServidor/trunk/restapi/gamma/conectar

	Dado que no hay creada una interfaz de conexión web tambien puedes descomentar una de las lineas de login del fichero trunk/restapi/gamma/index.php y accediendo a la ruta TuServidor/trunk/restapi/gamma/consultarNotas (Por ejemplo)



	/////////
	//Notas//
	/////////

	//Crear notas
	Para crear una nota enviamos el JSON de nuestra nota mediante POST a la ruta TuServidor/trunk/restapi/gamma/crearNota y  

	{ "titulo": "AquiTuTitulo", "contenido":"AquiTuContenido" }

	Ejemplo:
	{ "titulo":"Mi titulo", "contenido":"Mi contenido" }

	//Consultar todas las notas
	Para consultar todas las notas hacemos una peticion GET a la ruta TuServidor/trunk/restapi/gamma/consultarNotas

	//Consultar una nota en concreto
	Para consultar una nota en concreto hacemos una peticion por GET enviando el id de la notaa la ruta TuServidor/trunk/restapi/gamma/consultarNota/AquiElIdDeLaNota

	Ejemplo:
	TuServidor/trunk/restapi/gamma/consultarNota/3

	//Marcar nota como favorita
	Para establecer una nota como favorita hacemos una peticion PUT enviando el id de la nota a la ruta TuServidor/trunk/restapi/gamma/marcarFavorita/AquiElIdDeLaNota

	Ejemplo:
	TuServidor/trunk/restapi/gamma/marcarFavorita/2

	//Consultar favoritas
	Para consultar las notas favoritas hacemos una peticion GET a la ruta TuServidor/trunk/restapi/gamma/consultarNotasFavoritas