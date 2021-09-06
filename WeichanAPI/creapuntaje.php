<?php
require_once __DIR__ . '/vendor/autoload.php';
require "config.php";
require "./response/response.php";
require "./response/responsecreapuntaje.php";

use ReallySimpleJWT\Token;

header("Access-Control-Allow-Origin: *");

$resp = new Response();

if (!empty($_POST)){
    $token = $_POST["token"];
    $alias = $_POST["alias"];
    $puntaje = intval($_POST["puntaje"]);
    
    if(Token::validate($token, $tsecret)) {
        if(Token::validateExpiration($token, $tsecret)) {
            
            DB::$user = $dbuser;
            DB::$password = $dbpass;
            DB::$dbName = $dbinstance;
            DB::$host = $dbhost; 
            DB::$encoding = 'utf8'; 
            
            $idpuntaje = actualizaDatosJugadorPuntaje($alias, $puntaje);
            
            $resppuntaje = new ResponseCreaPuntaje();
            $resppuntaje->id = $idpuntaje;
            
            $resp->setOk()->setResponse($resppuntaje);
        } else {
            $resp->setError()->setMensaje("Token vencido");
        }
    } else {
        $resp->setError()->setMensaje("Token inválido");
    }
    
} else {
    $resp = $resp->setError()->setMensaje("datos inválidos");
}

function actualizaDatosJugadorPuntaje($alias, $puntaje) {
    $idpuntaje = 0;
    
    $idjugador = DB::queryFirstField("SELECT idjugador FROM jugador WHERE alias=%s", $alias);
    
    if($idjugador != null) { //Si el jugador existe actualizamos su puntaje
        //Recuperamos el id del puntaje anterior
        $idpuntaje = DB::queryFirstField("SELECT idpuntaje FROM puntaje WHERE idjugadorp=%i", $idjugador);
        //Actualizamos el puntaje
        DB::query("UPDATE puntaje SET puntaje = %i WHERE idpuntaje = %i", $puntaje, $idpuntaje);
     
        
    } else { //si no existe agregamos al jugador y su puntaje
        //Agregamos al jugador primero
        DB::insert('jugador', array(
            'alias' => $alias
        )); 
        
        $idjugador = intval(DB::insertId());
        
        //Luego agregamos el puntaje
        DB::insert('puntaje', array(
            'puntaje' => $puntaje,
            'idjugadorp' => $idjugador
        ));
        
        $idpuntaje = intval(DB::insertId());
    }
    
    agregaLogPuntaje($idpuntaje);
    return $idpuntaje;
}

function agregaLogPuntaje($idpuntaje) {
    DB::insert('logpuntaje', array(
        'idpuntajel' => $idpuntaje 
    )); 
}

echo(json_encode($resp)); 