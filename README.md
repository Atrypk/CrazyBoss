# CrazyBoss

Versiones de software en desarrollo:
	*PHP - 5.6.16
	*MySql - 5.7.9
	*Slim Framework - 3.8.1


Configuración necesaria
* mod_rewrite habilitado en el archivo .htaccess

	//////////////////////.htaccess////////////////////////
		RewriteEngine On
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteRule ^(.*)$ %{ENV:BASE}index.php [QSA,L]
	///////////////////////////////////////////////////////

*Modificar los datos de conexión a la base de datos del fichero llavesBD.php

	//////////////////////llavesBD.php//////////////////////
	define('DB_USERNAME', 'nombre-usuario');
	define('DB_PASSWORD', 'pass-usuario');
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'BD_restapi');

	//Contraseña generada en MD5 -> funcion MD5(contraseña)
	define('API_KEY','contraseña-MD5');
	///////////////////////////////////////////////////////

	
	Usuarios

	Jefe -> passJefe (En md5 -> f4391d34a7d8d263d995772d23d08efb )

	Marty -> passMarty (En md5 -> f2c34be0f565e6fa5fd0173d98cca3cf )

	Doc -> passDoc (En md5 -> 8f52abbd80badb8d99b02219f8f02bc7 )