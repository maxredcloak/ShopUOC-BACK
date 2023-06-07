<?php 
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json; charset=utf-8');

	class Category {
		public $id ;
		public $name;
		public $description;
		public function __construct(int $id, string $name, string $description) {
			$this->id = $id;
			$this->name = $name;
			$this->description = $description;
		}
	}

	$usuario = "root";
	$password = "";
	$servidor = "localhost";
	$basededatos = "test";
	$conexion = mysqli_connect( $servidor, $usuario, $password ) or die ("No se ha podido conectar
	al servidor de la bases de datos");
	$db=mysqli_select_db ( $conexion, $basededatos ) or die ("No se ha podido conectar a la base de datos");
	$consulta = "SELECT * FROM `categories` ";
	$resultado = mysqli_query( $conexion, $consulta ) or die ("No se ha podido hacer la consulta2");
	$categories = [];
	while ($fila = mysqli_fetch_array($resultado)) {	
		$category = new Category($fila['id'], $fila['name'],$fila['description']);
		array_push($categories,$category);
	}
	$categories = json_encode($categories);
	echo($categories);
	exit();
?>