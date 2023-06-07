<?php 
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json; charset=utf-8');

	class Product {
		public $id ;
		public $name;
		public $description;
		public $price;
		public function __construct(int $id, string $name, string $description, string $price) {
			$this->id = $id;
			$this->name = $name;
			$this->description = $description;
			$this->price = $price;
		}
	}
	
	$usuario = "root";
	$password = "";
	$servidor = "localhost";
	$basededatos = "test";
	$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar
	al servidor de la bases de datos");
	$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
	$consulta= "SELECT * FROM `products`";
	if(isset($_GET['category'])) {
		$category = $_GET['category'];	
		$consulta = "SELECT * FROM `products` WHERE CategoryId={$category}";
	}
	if(isset($_GET['id'])) {
		$id = $_GET['id'];	
		$consulta = "SELECT * FROM `products` WHERE Id={$id}";
	}
	$resultado = mysqli_query( $conexion, $consulta ) or die ("No se ha podido hacer la consulta2");
	$products = [];
	while ($fila = mysqli_fetch_array($resultado)) {	
		$tmp = new Product($fila['id'],$fila['name'], $fila['description'],$fila['price']);
		array_push($products,$tmp);
	}	
	$returnData = json_encode($products);
	echo($returnData);
	exit();
?>