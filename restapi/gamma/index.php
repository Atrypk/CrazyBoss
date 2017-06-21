
<?php

//Sesion
session_start(); 



/*
header("Access-Control-Allow-Origin: *"); //Api publica
header('Access-Control-Allow-Credentials: true'); //Permite credenciales
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');  //Metodos permitidos
header("Access-Control-Allow-Headers: X-Requested-With"); //Cabeceras permitidas
header('Content-Type: text/html; charset=utf-8'); //Codificacion
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); //Acceso a la api via P3P
*/

//Includes
require '../include/bbdd.php';
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

//Gestion y administracion

//Crear usuario


$app->run();

//Funciones

function crearNota($titulo, $contenido){
    if(comprobarSession()){
        
        $idUsuario=obtenerIdUsuario();

        $link=conectar();    

        $sql1="insert into notas(titulo,contenido,ID_usuario) values ('$titulo','$contenido','$idUsuario')";
        $resultSQL=mysqli_query($link, $sql1);
        
        
        desconectar($link);
        return true;    
    }
    return false;
}  
var_dump(consultarTodasLasNotas());
function consultarTodasLasNotas(){
        $link=conectar();

        $json_Notas = '{"Notas": [';

        $sql="SELECT ID_nota, titulo,contenido,ID_usuario FROM notas";
        $result = conectar()->query($sql);
        foreach ($result as $rows){
            
           $json_Notas .= '{ "titulo":'. $rows['titulo'] . ', "contenido":' . $rows["contenido"] . '},';
        }

        //Quitamos la coma sobrante
        $json_Notas = substr($json_Notas,0,strlen($json_Notas)-1);

        //Cerramos el json
    $json_Notas = $json_Notas.']}';

        desconectar($link);

        return $json_Notas;
}  

function obtenerIdUsuario(){
    if(comprobarSession()){
        $link=conectar();
        
        $usuario = $_SESSION['id_usuario'];

        $sql="SELECT ID_usuario FROM usuarios WHERE nombre='$usuario'";
        
        $result=mysqli_query($link, $sql);
        $return= mysqli_fetch_assoc($result);
        
        desconectar($link);

        return $return['ID_usuario'];
    }
}

function comprobarSession(){
    if (isset($_SESSION['id_usuario'])){
        return true;
    }
    return false;
}

function conectarse($usuario,$pass){
		
    $link=conectar();
    
    $sql="SELECT password FROM usuarios WHERE nombre='$usuario'";
    
    $result=mysqli_query($link, $sql);
    $return= mysqli_fetch_assoc($result);
    
    desconectar($link);

    //Variables
    $passObtenida = $return['password']; 
    $respuestaOK = "Se ha conectado correctamente.";
    $respuestaFail = "Error, intentelo de nuevo.";

    if ($passObtenida == $pass) {
	  	$_SESSION["id_usuario"]=$usuario;
		$_SESSION["nombre_usuario"]=$usuario;
        return $respuestaOK;
	}
        
    return $respuestaFail;
}

function desconectarse(){
    session_destroy();
}

?>