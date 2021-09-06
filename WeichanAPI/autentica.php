<?php
require_once __DIR__ . '/vendor/autoload.php';
require "config.php";
require "./response/response.php";
require "./response/responsetoken.php";

//Autenticación
use ReallySimpleJWT\Token;

header("Access-Control-Allow-Origin: *");

$resp = new Response();

if (!empty($_POST)){
	$user = $_POST["usuario"];
	$clave = $_POST["clave"];

	if($user == $usuario_weichan && $clave == $clave_weichan) {
		$token = Token::create($tuserid, $tsecret, $texpiration, $tissuer);
		$resptoken = new ResponseToken();
		$resptoken->token = $token;

		$resp->setOk()->setResponse($resptoken);
	} else {
		$resp->setError()->setMensaje("Datos de usuario incorrectos");		
	}

} else {
	$resp = $resp->setError()->setMensaje("datos inválidos");	
}
echo(json_encode($resp)); 

//Cambios
	
