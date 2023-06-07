<?php 
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Content-Type: application/json; charset=utf-8');

	class CreatedOrder {
		public $id ;
		public $buyer;
		public $lines;
		public function __construct(int $id, string $buyer) {
			$this->id = $id;
			$this->buyer = $buyer;
			$this->lines = [];
		}
	}
	class OrderLine {
		public $name ;
		public $quantity;
		public function __construct(string $name, int $quantity) {
			$this->name = $name;
			$this->quantity = $quantity;
		}
	}

	function decryptInfo($token){
		$pass = "1234";
		$method = "aes128";
		$iv= 1234567890123456;
		$dada = openssl_decrypt($token, $method, $pass,null,$iv);
		return json_decode($dada);
	}

	$usuario = "root";
	$password = "";
	$servidor = "localhost";
	$basededatos = "test";
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	if(!property_exists($data,"lines")){
		$errorm = new Error("invalid lines");
		echo $errorm;
		exit;
	}
	if(!property_exists($data,"token")){
		$errorm = new Error("invalid token");
		echo $errorm;
		exit;
	}
	$lines = $data->lines;
	$token = $data->token;
	$dada = decryptInfo($token);
	$dateend = $dada->dateend;
	$dateend = $dateend > date("Y-m-d");
	if($dateend != 1){
		$errorm = new Error("token expired");
		echo $errorm;
		exit;
	}
	$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar
	al servidor de la bases de datos");
	$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
	$consulta = "SELECT * FROM `orders` ORDER BY id DESC";
	$resultado = mysqli_query( $conexion, $consulta ) or die ("No se ha podido hacer la consulta");
	$fila = mysqli_fetch_array($resultado);
	$orderId = $fila["id"] + 1;
	$createOrder = "INSERT INTO `orders`(`id`, `buyerId`) VALUES ('{$orderId}','{$dada->id}')";
	$resultado = mysqli_query( $conexion, $createOrder ) or die ("No se ha podido hacer la insercion");
	if($resultado = '1'){
		$returnValue = new CreatedOrder($orderId, $dada->id);
		foreach($lines as $value){
			$createproductorder = "INSERT INTO `products_ordered`(`orderId`, `productId`,`quantity`) VALUES ('{$orderId}','{$value->id}','{$value->quantity}')";
			$resultado = mysqli_query( $conexion, $createproductorder ) or die ("No se ha podido hacer la insercion de linea");
			$line = new OrderLine($value->id, $value->quantity);
			array_push($returnValue,$line);
		}
		$returnData = json_encode($returnValue);
		echo($returnData);
		exit();
	}
?>
