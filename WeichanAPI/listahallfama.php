<?php
require_once __DIR__ . '/vendor/autoload.php';
require "config.php";
require "./response/response.php";
require "./response/responselistahallfama.php";

use ReallySimpleJWT\Token;

header("Access-Control-Allow-Origin: *");

$resp = new Response();

if (!empty($_POST)){
    $token = $_POST["token"];
    
    if(Token::validate($token, $tsecret)) {
        if(Token::validateExpiration($token, $tsecret)) {
            
            DB::$user = $dbuser;
            DB::$password = $dbpass;
            DB::$dbName = $dbinstance;
            DB::$host = $dbhost; 
            DB::$encoding = 'utf8'; 
            
            $results = DB::query("SELECT idpuntaje, jugador.alias alias, puntaje FROM puntaje ".
                                 "INNER JOIN jugador ON jugador.idjugador = puntaje.idjugadorp ".  
                                 "ORDER BY puntaje DESC LIMIT 5");
            
            $resplista = new ResponseListaHallFama();
            $resplista->lista = $results;
            
            $resp->setOk()->setResponse($resplista);
        } else {
            $resp->setError()->setMensaje("Token vencido");
        }
    } else {
        $resp->setError()->setMensaje("Token inválido");
    }
    
} else {
    $resp = $resp->setError()->setMensaje("datos inválidos");
}
echo(json_encode($resp)); 