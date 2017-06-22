
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
$CREADA_NOTA = 'Nota creada correctamente.';
$MARCADA_FAVORITA = 'Has marcado esta nota como favorita.';
$PARAMETROS_INCORRECTOS = 'Revise los parametros e intentelo de nuevo por favor.';
$NO_EXISTE_NOTA_CONCRETA = 'No se encontraron datos de la nota buscada.';
$NO_EXISTE_NOTA_PARA_FAVORITA = 'No se encontro la nota que quiere marcar como favorita';

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

$app->get('/pruebas', function ($request, $response, $args) {
    

    //Lanzamos la desconexion
    echo pruebas();

});

//Gestion y administracion

//Crear usuario


$app->run();

//Funciones

/**
* Creacion de notas
*
* Envio de parametros titulo 
* y contenido para crear
* una nota
*/
function crearNota($titulo, $contenido){
    
    //Comprobacion de parametros
    if(empty ($titulo) || empty($contenido) ){
        return $GLOBALS['PARAMETROS_INCORRECTOS'];
    }

    if(comprobarSession()){
        
        $idUsuario=obtenerIdUsuario();

        $link=conectar();    

        $sql1="insert into notas(titulo,contenido,ID_usuario) values ('$titulo','$contenido','$idUsuario')";
        $resultSQL=mysqli_query($link, $sql1);
        
        
        desconectar($link);
        return $GLOBALS['CREADA_NOTA'];;    
    }
    return $GLOBALS['AVISO_DESCONECTADO'];
}  

/**
* Consultar todas las notas
*
* Se recive un json con 
* todas las notas guardadas
* en la base de datos
*/
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

/**
* Consulta de una nota en concreto
*
* Se envia el id de la nota
* a consultar y la funcion
* devuelve un json con sus datos
*/
function consultarNota($idNota){

    //Comprobacion de parametros
    if(empty ($idNota) || !is_numeric($idNota) ){
        return $GLOBALS['PARAMETROS_INCORRECTOS'];
    }

    if(comprobarSession()){
        $link=conectar();
        
        $sql="SELECT titulo,contenido,ID_usuario FROM notas WHERE ID_nota=$idNota";
        
        $result=mysqli_query($link, $sql);
        $return= mysqli_fetch_assoc($result);

        //Comprobacion de recepcion de datos
        if(empty ($return['titulo']) || empty ($return["contenido"])){
            return $GLOBALS['NO_EXISTE_NOTA_CONCRETA'];
        }

        $json_Nota = '{"Notas": [{ "titulo":'. $return['titulo'] . ', "contenido":' . $return["contenido"] . '}]}';

        desconectar($link);

        return $json_Nota;
    }
    return $GLOBALS['AVISO_DESCONECTADO'];
}

/**
* Marcar nota como favorita
*
* Se envia el ID de  
* la nota que se desea
* establecer como favorita
*/
function marcarFavorita($idNota){

    //Comprobacion de parametros
    if(empty ($idNota) || !is_numeric($idNota) ){
        return $GLOBALS['PARAMETROS_INCORRECTOS'];
    }

    if(consultarNota($idNota)==$GLOBALS['NO_EXISTE_NOTA_CONCRETA']){
        return $GLOBALS['NO_EXISTE_NOTA_PARA_FAVORITA'];
    }

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

/**
* Consultar notas favoritas
*
* Se recive un json con todas
* las notas marcadas como
*  favoritas por ese usuario
*/
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

/**
* Obtener el id del usuario conectado
*
* La funcion devuelve el
* id correspondiente al usuario
* que esta conectado
*/
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

/**
* Comprobar la sesion
*
* La funcion devuelve el
* estado de la sesion actual
* del usuario
*/
function comprobarSession(){
    if (isset($_SESSION["nombre_usuario"])){
        return true;
    }
    return false;
}

/**
* Conectarse
*
* La funcion recibe el usuario
* y la contraseña y la coteja con
* la base de datos conectando o no
* al usuario
*/
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

/**
* Desconectarse
*
* La funcion elimina los datos
* de sesion desconectando al 
* usuario actualmente logeado
*/
function desconectarse(){
    session_destroy();

    return $GLOBALS['DESCONEXION_CORRECTA'];
}

/**
* Pruebas
*
* La funcion devuelve el
* resultado de las pruebas 
* realizadas a las funciones
* de este fichero
*/
function pruebas(){
    //Vamos a realizar las pruebas de las funciones en base a lo que NO deberian permitir

    //Creacion de notas con algun parametro vació
    echo 'Prueba creacion de notas con dos parametros vacios: Respuesta -> ' . crearNota("","") . '<br />';
    echo 'Prueba creacion de notas con parametro titulo vacio: Respuesta -> ' . crearNota("","contenido") . '<br />';
    echo 'Prueba creacion de notas con parametro contenido vacio: Respuesta -> ' . crearNota("titulo","") . '<br />';

    //Consulta de nota concreta enviando un parametro que no sea numero
    echo 'Prueba consulta de notas concreta enviando una cadena de texto: Respuesta -> ' . consultarNota("texto") . '<br />';

    //Consulta de nota concreta enviando un ID que no existe 
    echo 'Prueba consulta de notas concreta enviando un ID que no existe: Respuesta -> ' . consultarNota(99999) . '<br />';

    //Marcar nota favorita enviando un parametro que no sea numerico
    echo 'Prueba marcar nota como favorita enviando una cadena de texto: Respuesta -> ' . marcarFavorita("texto") . '<br />';

    //Marcar nota favorita enviando un ID que no existe 
    echo 'Prueba consulta de notas concreta enviando un ID que no existe: Respuesta -> ' . marcarFavorita(99999) . '<br />';

}

?>