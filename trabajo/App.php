<?php
class App
{
  public $dsn ="mysql:dbname=agenda;host=localhost";
  public $user="root";
  public $bd;
  //constructor de la sesion
  public function __construct()
  {
    

    session_start();
        try {
            $this->bd = new PDO($this->dsn, $this->user);  
        } catch (PDOException $e) {
            echo 'Falló la conexión: ' . $e->getMessage();
        }    
    }


  //metodo run carga la app y envia al metodo login

  public function run()
  {
    if (isset($_GET['method'])) {
      $method = $_GET['method'];
    } else {
      $method = 'login';
    }

    $this->$method();
  }

    // Si existe una cookie rederige directamente al home si no se va al login
    
  public function login()
  {
 

    if (isset($_SESSION['name'])) {
     
      header('Location: ?method=home');

      return;
     
    } else {
     
    
      setcookie(session_name(), '', time() - 7200, '/');
      // este else es para borrar la sesion vacia que se crea al entrar al metodo login en el caso de estar los campos vacios de name y password
    }
    

    include('views/login.php');
  }
  
  // Comprueba que los campos se hayan enviado guardas los post en dos variables y crea las sesiones con ellas
  

  //Para este metodo auth primero busco si existe un nombre igual al introducido dentro de la base de datos y extraigo
  //su contraseña debido a que esta contraseña se encuentra en codigo hash para ello lo que hacemos es utilizar la funcion
  //password verify que comprueba si el hash coincide con la contraseña introducida y devuelve true o false
  //En este entraria dentro del if y dropearia la tabla contactos en el caso de existir y la crearia otra vez de nuevo
  //aparte creia una session para el usuario
  public function auth(){
   $nombre= $_POST['name'];
   $sql = "select password from credenciales where usuario='".$nombre."'";
   $hash = $this->bd->query($sql);
  foreach($hash as $fila){
  $contra=$fila[0];    
  }

    if (password_verify($_POST['password'], $contra) && !empty($_POST['name'])) {
      
      $sql = "DROP TABLE IF EXISTS 
      `contactos`";
      $create = $this->bd-> prepare($sql);
      $create->execute();
      $name = $_POST['name'];
      $password = $_POST['password'];
      $sql1 ="CREATE TABLE  contactos( ".
      "tipo VARCHAR(100) NOT NULL, ".
      "nombre VARCHAR(100) NOT NULL, ".
      "apellido VARCHAR(100), ".
      "email VARCHAR(100), ".
      "direccion VARCHAR(100) NOT NULL, ".
      "numero INT NOT NULL); ";
    
      $create = $this->bd-> prepare($sql1);
      $create->execute();
    } else {
      header('Location: ?method=login');
      return;
    }
    $_SESSION['name'] = $name;
    $_SESSION['password'] = $password;
    header('Location: index.php?method=home');
  }


  
  public function home()

  {


    if (!isset($_SESSION['name'])) {
      header('Location: ?method=login');
      return;
    }
   
    include('views/home.php');
  }


 
//al cerrar sesion borro las sesiones y borra la tabla contactos
  public function close()
  {
    session_destroy();
    setcookie(session_name(), '', time() - 7200, '/');
    header('Location: index.php?method=login');
    $sql = "drop table contactos; ";
  
    $create = $this->bd-> prepare($sql);
    $create->execute();
  }

  //Para esta funcion utilizo un forulario de tipo fichero para recoger el fichero
  // con los ifs compruebo el tamaño y la extension si es adecuada
  // y con move_uploaded_file subo el fichero al carpeta destino uploads
  public function subirfichero(){
   $type= $_FILES["myfile"]['type'];
    if(isset($_POST["envio"])){
      if($_FILES["myfile"]['size']<5242880){
        if($type=='image/png'||$type=='image/jpg'||$type=='application/pdf'){

        
          $nametemp=$_FILES["myfile"]["tmp_name"];
          $destino = 'uploads/'.$_FILES["myfile"]["name"];
         
         $flag= move_uploaded_file($nametemp,$destino);
         $flag ? "fichero subido correctamente" : "<br>fallo en la subida";
         
      } 
  }else{
   
  }
  include('views/home.php');
  }
}

/**  */

public function crear(){
  $sql = "CREATE TABLE contactos( ".
  "tipo VARCHAR(100) NOT NULL, ".
  "nombre VARCHAR(100) NOT NULL, ".
  "apellido VARCHAR(100), ".
  "email VARCHAR(100), ".
  "direccion VARCHAR(100) NOT NULL, ".
  "numero INT NOT NULL); ";

  $create = $this->bd-> prepare($sql);
  $create->execute();
}

/**Para esta funcion lo que hago es extaigo toda la info del xml en una variable datos
 * el cml con load_file crea un array asociativo por lo que la recorro y voy extrañendo su info para a posteriori
 * introducirla dentro de la base de datos
 * para el atributo tipo uso la funcion atrributes() y se lo saco con su nombre
 */
 public function insertarXML(){

$datos = simplexml_load_file("agenda.xml");

foreach ($datos->children() as $fila) {

    $sentencia = $this->bd-> prepare("INSERT INTO contactos (tipo,nombre,apellido,email,direccion,numero) VALUES (?,?,?,?,?,?)");
   $atributo = $fila->attributes(); 

    $tipo1 = $atributo['tipo'];
    $nombre = $fila->nombre;
    $apellido = $fila->apellidos;
    $email=$fila->email;
    $direccion= $fila->direccion;
    $telefono= $fila->telefono;
    $sentencia->bindParam(1,$tipo1);
    $sentencia->bindParam(2,$nombre);
    $sentencia->bindParam(3,$apellido);
    $sentencia->bindParam(4,$email);
    $sentencia->bindParam(5,$direccion);
    $sentencia->bindParam(6,$telefono);
    $sentencia->execute();
   
   
}

include('views/home.php');

 }


 public function mostrar(){


  $sql = ("SELECT * FROM contactos");
  $registros =$this->bd->query($sql);
  $contador=0;
  

include('views/home.php');
 }

 public function ocultar(){
$registros=null;
  

include('views/home.php');
 }

 /** muy similar a la funcion de insertar xml pero recogiendo de un post los datos */
public function insertar(){

  if(!empty($_POST['name'])&&!empty($_POST['direccion'])&&!empty($_POST['telefono'])){

  $sentencia = $this->bd-> prepare("INSERT INTO contactos (tipo,nombre,apellido,email,direccion,numero) VALUES (?,?,?,?,?,?)");
  
 

    $sentencia->bindParam(1,$_POST['tipo']);
    $sentencia->bindParam(2,$_POST['name']);
    $sentencia->bindParam(3,$_POST['apellido']);
    $sentencia->bindParam(4,$_POST['email']);
    $sentencia->bindParam(5,$_POST['direccion']);
    $sentencia->bindParam(6,$_POST['telefono']);
    $sentencia->execute();
  }else{
    $respuesta1="<p style= 'color:red'> Rellena todos los campos necesarios</p>";
  }
    include('views/home.php');

}

/** cargo sentencia y ejecuto */
public function eliminar(){
  if(!empty($_POST['telefono'])){
  $sentencia = $this->bd-> prepare("delete from contactos where numero ='".$_POST['telefono']."'");
  $sentencia->execute();
  }else{
    $respuesta2="<p style= 'color:red'> Tienes que rellenar el campo telefono </p>";
  }
  include('views/home.php');
}
/** cargo sentencia y ejecuto */
public function actualizar(){
  if(!empty($_POST['telefono'])){
    if($_POST['tipo']=='persona'){
      $_POST['email']==null;

    }
  
  $sentencia = $this->bd-> prepare("UPDATE contactos SET tipo='".$_POST['tipo']."',nombre='".$_POST['name']."',apellido='".$_POST['apellido']."',email='".$_POST['email']."',direccion='".$_POST['direccion']."',numero='".$_POST['telefono2']."'  where numero ='".$_POST['telefono']."'");
  
  $sentencia->execute();
  }else{
    $respuesta3="<p style= 'color:red'>Tienes que rellenar el campo telefono que quieres actualizar  </p>";
  }
  include('views/home.php');

}
/** cargo sentencia y ejecuto */

public function infoContacto(){

  if(!empty($_POST['telefono'])){

  $sql = ("SELECT * FROM contactos where numero ='".$_POST['telefono']."'");
  $contacto =$this->bd->query($sql);
  }else{
    $respuesta4=" <p style= 'color:red'> Tienes que rellenar el campo telefono </p>";
  }
  include('views/home.php');


}

/**Las dos funciones siguientes son para cuando selecciones un tipo de contacto te salga
 * un formulario acorde a lo que puedas introducirme ya que si es empresa no tendras el campo apellido
 * pero si tendras el campo email
 */
public function seleccionar(){

  if($_POST['Tipo']=='1'){
    $mensaje= '<form action="?method=insertar" method="post">
    <br>
      <select style= "visibility: hidden;" type="hidden" name="tipo">
    
      <option value="empresa" type="hidden" selected>EMPRESA</option>
      </select>
      <br>
      <p> TIPO EMPRESA </p>
      <br>
      <label for="">nombre </label>
      <input type="text" name="name">
      <br>
      <label for="">email  </label>
      <input type="text" name="email">
      <br>
      <label for="">direccion </label>
      <input type="text" name="direccion">
      <br>
      <label for="">telefono  </label>
      <input type="number" name="telefono">
      <br>
      <input type="submit" value="insertar">
    </form>';

  }else{
    
    $mensaje='<form action="?method=insertar" method="post">
    <br>
    <select style= "visibility: hidden;" name="tipo">

    <option value="persona" selected  >PERSONA</option>
    </select>
    <p> TIPO PERSONA </p>
    <br>
      <label for="">nombre </label>
      <input type="text" name="name">
      <br>
      <label for="">apellido  </label>
      <input type="text" name="apellido">
      <br>
      <label for="">direccion </label>
      <input type="text" name="direccion">
      <br>
      <label for="">telefono  </label>
      <input type="number" name="telefono">
      <br>
      <input type="submit" value="insertar">
    </form>';
  }
  include('views/home.php');
}

public function seleccionarAc(){

  if($_POST['tipo']=='1'){

  $mensaje1= '  <form action="?method=actualizar" method="post">
  
  <label for="">telefono ANTIGUO </label>
  <input type="number" name="telefono">
  <br>
      <select style= "visibility: hidden;" type="hidden" name="tipo">
    
      <option value="empresa" type="hidden" selected>EMPRESA</option>
      </select>
  <br>
  <p> TIPO EMPRESA </p>
  <br>
  <label for="">nombre </label>
  <input type="text" name="name">
 
  <br>
  <label for="">email  </label>
  <input type="text" name="email">
  <br>
  <label for="">direccion </label>
  <input type="text" name="direccion">
  <br>
  <label for="">telefono  NUEVO </label>
  <input type="number" name="telefono2">
  <br>
  <input type="submit" value="ACTUALIZAR">
</form>';
  
}if($_POST['tipo']=='2'){
  $mensaje1=
  '<form action="?method=actualizar" method="post">
  <label for="">telefono ANTIGUO </label>
  <input type="number" name="telefono">
  <br>
  <select style= "visibility: hidden;" name="tipo">

  <option value="persona" selected  >PERSONA</option>
  </select>
  <p> TIPO PERSONA </p>
  <br>
  <label for="">nombre </label>
  <input type="text" name="name">
  <br>
  <label for="">apellido  </label>
  <input type="text" name="apellido">
<br>
  <label for="">direccion </label>
  <input type="text" name="direccion">
  <br>
  <label for="">telefono  NUEVO </label>
  <input type="number" name="telefono2">
  <br>
  <input type="submit" value="ACTUALIZAR">
</form>';
  

}
include('views/home.php');
}
  
}
