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