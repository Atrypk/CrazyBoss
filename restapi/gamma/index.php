
<?php

header("Access-Control-Allow-Origin: *"); //Api publica
header('Access-Control-Allow-Credentials: true'); //Permite credenciales
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');  //Metodos permitidos
header("Access-Control-Allow-Headers: X-Requested-With"); //Cabeceras permitidas
header('Content-Type: text/html; charset=utf-8'); //Codificacion
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); //Acceso a la api via P3P

//Includes
//include_once '../include/llavesBD.php';


require '../../vendor/autoload.php';

$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new Slim\App($config);
$app->get('/', function () {
   echo "Funciona :)";
});

$app->run();

?>