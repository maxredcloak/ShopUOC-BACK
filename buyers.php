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
	$name = $data->name;	
	$surname = $data->surname;
	$address = $data->address;		
	$mail = $data->mail;
	$userpassword = $data->password;
	if($name != null && $surname != null && $address != null && $mail != null && $userpassword != null){
		$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar al servidor de la bases de datos");
		$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
		$consulta = "SELECT * FROM `buyers` ORDER BY id DESC";
		$resultado = mysqli_query( $conexion, $consulta ) or die ("No se ha podido hacer la consulta");
		$fila = mysqli_fetch_array($resultado);
		$userId = $fila["id"] + 1;
		$createBuyer = "INSERT INTO `buyers`(`id`, `name`, `surname`, `address`, `mail`, `pass`) VALUES ('{$userId}','{$name}','{$surname}','{$address}','{$mail}','{$userpassword}')";
		$resultado = mysqli_query( $conexion, $createBuyer ) or die ("No se ha podido hacer la insercion");

		$date = date("Y-m-d", strtotime('tomorrow'));
		$obj = new \stdClass();
		$obj->id = $userId;
		$obj->dateend = $date;
		$token = encryptInfo($obj);

		$returnData = new LoginData(true, $token, $userId, $name, $surname, $mail, $address);
		$returnjson = json_encode($returnData);
		echo($returnjson);
		exit();
	}
?>