<?php 
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Content-Type: application/json; charset=utf-8');

	class LoginData {
		public $valid;
		public $token;
		public $id;
		public $name;
		public $surname;
		public $mail;
		public $address; 
		public function __construct(bool $valid, string $token,int $id,string $name,string $surname,string $mail,string $address) {
			$this->valid = $valid;
			$this->token = $token;
			$this->id = $id;
			$this->name = $name;
			$this->surname = $surname;
			$this->mail = $mail;
			$this->address = $address;
		}
	}
	function encryptInfo($obj){
		$pass = "1234";
		$method = "aes128";
		$iv= 1234567890123456;
		return openssl_encrypt(json_encode($obj), $method, $pass, null, $iv);
	}
	$usuario = "root";
	$password = "";
	$servidor = "localhost";
	$basededatos = "test";
	$json = file_get_contents('php://input');
	$data = json_decode($json);	
	$mail = $data->mail;
	$userpassword = $data->password;
	$hash = password_hash($password, PASSWORD_DEFAULT);
	$returnData = new \stdClass();

	if($mail != null && $userpassword != null){
		$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar al servidor de la bases de datos");
		$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
		$consultaMail = "SELECT * FROM `buyers` WHERE mail = '{$mail}' and pass = '{$userpassword}'";
		$resultado = mysqli_query( $conexion, $consultaMail ) or die ("No se ha podido hacer la consulta");
		$fila = mysqli_fetch_array($resultado);
		if($fila != null){
			$date = date("Y-m-d", strtotime('tomorrow'));
			$obj = new \stdClass();
			$obj->id = $fila["id"];
			$obj->dateend = $date;			
			$token = encryptInfo($obj);
			
			$returnData = new LoginData(true, $token, $fila["id"], $fila["name"], $fila["surname"], $fila["mail"], $fila["address"]);
			$returnjson = json_encode($returnData);
			echo($returnjson);
			exit();
		}
		$returnData -> valid = false; 	
		$returnjson = json_encode($returnData);
		echo($returnjson);
		exit();
	}
?>