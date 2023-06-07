<?php 
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Content-Type: application/json; charset=utf-8');
	class Order {
		public $id ;
		public $date;
		public $name;
		public $surname;
		public $mail;
		public $lines;
		public function __construct(int $id, string $date, string $name, string $surname,string $mail) {
			$this->id = $id;
			$this->date = $date;
			$this->name = $name;
			$this->surname = $surname;
			$this->mail = $mail;
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

	function decryptInfo($data){
		$buyer= $data->buyer;
		$pass = "1234";
		$method = "aes128";
		$iv= 1234567890123456;
		$dada = openssl_decrypt($buyer, $method, $pass,null,$iv);
		return json_decode($dada);
	}

	$usuario = "root";
	$password = "";
	$servidor = "localhost";
	$basededatos = "test";
	$json = file_get_contents('php://input');
	$data = json_decode($json);	
	if(!property_exists($data,"buyer")){
		$errorm = new Error("invalid input");
		echo $errorm;
		exit;
	}
	$returnData = [];
	$dada = decryptInfo($data);
	$dateend = $dada->dateend;
	$dateend = $dateend > date("Y-m-d");
	if($dateend != 1){
		$errorm = new Error("token expired");
		echo $errorm;
		exit;
	}
	$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar al servidor de la bases de datos");
	$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
	$consulta = "SELECT * FROM `orders` where buyerId= '{$dada->id}'";
	$resultado = mysqli_query( $conexion, $consulta ) or die ("No se ha podido hacer la consulta");
	$consultaBuyer = "SELECT * FROM `buyers` where id = {$dada->id}";
	$resultadoBuyer = mysqli_query( $conexion, $consultaBuyer ) or die ("No se ha podido hacer la consulta");
	$res = mysqli_fetch_array($resultadoBuyer);
	while ($fila = mysqli_fetch_array($resultado)) {	
		$order = new Order($fila['id'], $fila['creationDate'], $res['name'], $res['surname'], $res['mail']);
		$consultaLines = "SELECT * FROM `products_ordered` where orderId = {$fila['id']}";
		$resultadoLines = mysqli_query( $conexion, $consultaLines ) or die ("No se ha podido hacer la consulta");
		while ($line = mysqli_fetch_array($resultadoLines)) {	
			$consultaProduct = "SELECT * FROM `products` where id = {$line['productId']}";
			$resultadoProduct = mysqli_query( $conexion, $consultaProduct ) or die ("No se ha podido hacer la consulta");
			$prod = mysqli_fetch_array($resultadoProduct);
			$articleLine = new OrderLine($prod['name'], $line['quantity']);
			array_push($order->lines, $articleLine);
		}
		array_push($returnData,$order);
	}
	$returnData = json_encode($returnData);
	echo($returnData);
	exit();
?>
