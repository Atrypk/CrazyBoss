
<?php

//Sesion
session_start(); 

//Mensajes
$AVISO_DESCONECTADO = 'Estas descoenctado, por favor, logeate primero.';  
$LOGEADO_CORRECTAMENTE = 'Se ha conectado correctamente.'; 
$ERROR_LOGIN = 'Error, intentelo de nuevo.';
$DESCONEXION_CORRECTA = 'Te has desconectado.';
$SIN_NOTAS_FAVORITAS = 'No tiene ninguna nota en sus favoritos.';
$NO_EXISTEN_NOTAS = 'No existe ninguna nota, si te das prisa podras ser el primero.';
$MARCADA_FAVORITA = 'Has marcado esta nota como favorita.';

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

//Conexiones, recuerda que solo debe de haber una linea activa al mismo tiempo
//conectarse('Jefe','f4391d34a7d8d263d995772d23d08efb');
//conectarse('Marty','f2c34be0f565e6fa5fd0173d98cca3cf');
conectarse('Doc','8f52abbd80badb8d99b02219f8f02bc7');

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

    //var_dump($request);
    //Creamos la nota en la bd
    echo crearNota($objNota->titulo,$objNota->contenido);
    
});

//Consultar todas las notas
$app->get('/consultarNotas', function ($request, $response, $args) {

	
    //Mostramos
    echo consultarTodasLasNotas();
 
});

//Consultar una nota concreta
$app->get('/consultarNota/{idNota}', function ($request, $response, $args) {

	
    //Mostramos
    echo consultarNota($args["idNota"]);
 
});

//Marcar nota como favorita
$app->put('/marcarFavorita/{idNota}', function ($request, $response, $args) {

	
    //Mostramos
    echo marcarFavorita($args["idNota"]);
 
});

//Consultar todas las notas favoritas
$app->get('/consultarNotasFavoritas', function ($request, $response, $args) {
	
    //Mostramos
    echo consultarFavoritas();
 
});

$app->post('/conectar', function ($request, $response, $args) {
    //Recogemos el contenido enviado
    $json = $request->getBody();

    //Pasamos el json a array
    $data = json_decode($json, true);

    //creamos el objeto nota
    $objUsuario = (object)$data;

    //Lanzamos el login
    echo conectarse($objUsuario->usuario,$objUsuario->password);

});

$app->get('/desconectar', function ($request, $response, $args) {
    

    //Lanzamos la desconexion
    echo desconectarse();

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
    return $GLOBALS['AVISO_DESCONECTADO'];
}  

function consultarTodasLasNotas(){
    if(comprobarSession()){
        $link=conectar();

        $json_Notas = '{"Notas": [';

        $sql="SELECT ID_nota,titulo,contenido,ID_usuario FROM notas";
        $result = conectar()->query($sql);
        if($result!=false){
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
        return $GLOBALS['NO_EXISTEN_NOTAS'];
    }

    return $GLOBALS['AVISO_DESCONECTADO'];
}  

function consultarNota($idNota){
    if(comprobarSession()){
        $link=conectar();
        
        $sql="SELECT titulo,contenido,ID_usuario FROM notas WHERE ID_nota=$idNota";
        
        $result=mysqli_query($link, $sql);
        $return= mysqli_fetch_assoc($result);
        
        $json_Nota = '{"Notas": [{ "titulo":'. $return['titulo'] . ', "contenido":' . $return["contenido"] . '}]}';

        desconectar($link);

        return $json_Nota;
    }
    return $GLOBALS['AVISO_DESCONECTADO'];
}

function marcarFavorita($idNota){
    if(comprobarSession()){
        $link=conectar();
                
        $id_usuario = obtenerIdUsuario();

        $sql="insert into favoritos(id_nota,id_usuario) values ('$idNota','$id_usuario')";
        
        $resultSQL=mysqli_query($link, $sql);

        desconectar($link);

        return $GLOBALS['MARCADA_FAVORITA'];
    }
    return $GLOBALS['AVISO_DESCONECTADO'];
}

function consultarFavoritas(){
    if(comprobarSession()){
        $link=conectar();

        $id_usuario = obtenerIdUsuario();

        $idNotasFavoritas = "(";

        $preSql="SELECT id_nota FROM favoritos WHERE id_usuario='$id_usuario'";
        $preResult=conectar()->query($preSql);
        foreach ($preResult as $preRows){            
           $idNotasFavoritas .= $preRows['id_nota'] . ',';
        }

        //Quitamos la coma sobrante
        $idNotasFavoritas = substr($idNotasFavoritas,0,strlen($idNotasFavoritas)-1);

        //Cerramos el cojunto de datos con el formato correcto
        $idNotasFavoritas .=")";


        $json_Notas = '{"Notas": [';     

        $sql='SELECT ID_nota,titulo,contenido,ID_usuario FROM notas where id_nota in ' . $idNotasFavoritas;

        $result = conectar()->query($sql);
        if($result!=false){
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
        return $GLOBALS['SIN_NOTAS_FAVORITAS'];
    }

    return $GLOBALS['AVISO_DESCONECTADO'];
}

function obtenerIdUsuario(){
    if(comprobarSession()){
        $link=conectar();
        
        $usuario = $_SESSION["nombre_usuario"];

        $sql="SELECT ID_usuario FROM usuarios WHERE nombre='$usuario'";
        
        $result=mysqli_query($link, $sql);
        $return= mysqli_fetch_assoc($result);
        
        desconectar($link);

        return $return['ID_usuario'];
    }
}

function comprobarSession(){
    if (isset($_SESSION["nombre_usuario"])){
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

    if ($passObtenida == $pass) {
		$_SESSION["nombre_usuario"]=$usuario;
        return $GLOBALS['LOGEADO_CORRECTAMENTE'];
	}
        
    return $GLOBALS['ERROR_LOGIN'];
}

function desconectarse(){
    session_destroy();

    return $GLOBALS['DESCONEXION_CORRECTA'];
}

?>