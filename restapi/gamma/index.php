
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

//Configuracion para evitar errores de headerLength
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];

//Creacion de objeto app
$app = new Slim\App($config);

//Llamadas

//Crear nota
$app->post('/crearNota', function ($request, $response, $args) {

	//Recogemos el contenido enviado
    $json = $request->getBody();

    //Pasamos el json a array
    $data = json_decode($json, true);

    //creamos el objeto nota
    $objNota = (object)$data;

    //Mostramos
    echo $objNota->name;
 
});

//Consultar todas las notas
$app->get('/consultarNotas', function ($request, $response, $args) {

	
    //Mostramos
    echo "Mostrando todas las notas";
 
});

//Consultar una nota concreta
$app->get('/consultarNota/{idNota}', function ($request, $response, $args) {

	
    //Mostramos
    echo "Mostrando nota " . $args["idNota"] ;
 
});

//Marcar nota como favorita
$app->put('/marcarFavorita/{idNota}', function ($request, $response, $args) {

	
    //Mostramos
    echo "Marcando como favorita la nota " . $args["idNota"] ;
 
});

//Consultar todas las notas favoritas
$app->get('/consultarNotasFavoritas', function ($request, $response, $args) {

	
    //Mostramos
    echo "Mostrando todas las notas favoritas";
 
});

$app->run();

?>