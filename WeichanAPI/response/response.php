<?php
class Response {
	public $tipo = "";
	public $mensaje = "";
	public $response = null;
	
	const TOK = "OK";
	const TERROR = "ERROR";
	
	public function setOk() {
		$this->tipo = self::TOK;
		return $this;
	}
	
	public function setError() {
		$this->tipo = self::TERROR;
		return $this;
	}
	
	public function setMensaje($msg) {
		$this->mensaje = $msg;
		return $this;
	}	
	
	public function setResponse($resp) {
		$this->response = $resp;
		return $this;
	}	
}