<?php

  session_start();

?>
<!DOCTYPE html>


<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  
<link rel="stylesheet" href="views/views.css">
</head>
<body>

  <header>
  <h1>AGENDA CONTACTOS</h1>
  </header>

<div class="izq"><h1>Bienvenido <?= $_SESSION['name'] ?></h1>

<h3>LISTA DE CONTACTOS ACTUAL</h3>

<form action="?method=mostrar" method="post">
<input type="submit" value="mostrar">
</form>
<br>
<form action="?method=ocultar" method="post">
<input type="submit" value="ocultar">
</form>
<!-- Registros recoge de la varible moster el array y lo recorre para mostrarlo -->
<?php
foreach($registros as $fila){

 
 echo "<br> TIPO: " .$fila[0];
 echo "<br> nombre: " .$fila[1];
 if(!empty($fila[2])){
   echo "<br> apellido:". $fila[2];
 }else{
   echo "<br> email: ". $fila[3];
 }
 echo "<br> direccion: " .$fila[4];
 echo "<br> telefono: " .$fila[5];
 echo "<br>";
 
}

?>

</div>
<div class="right">
<h3>INSERTAR AGENDA DE CONTACTOS CON ARCHIVO XML</h3>
 <form action="?method=insertarXML" method="post">
 <input  type="submit" value="insertar">
 </form>



  <h3>INSERTAR CONTACTO EN LA AGENDA</h3>

  <p>Elige el tipo que quieres ingresar</p>

  <form action="?method=seleccionar" method="post">
   <select name="Tipo">
  <option value="1">EMPRESA</option>
  <option value="2" >PERSONA</option>
  <input type="submit" value="seleccionar">
  </select>
  </form>
  <!-- Mensaje muestra el formulario segun el tipo que has selecionado-->
  <?php echo $mensaje?>

  <?php echo $respuesta1?>

  <h3>ELIMINAR UN CONCTACTO POR NUMERO DE TELEFONO</h3>
  <form action="?method=eliminar" method="post">
  <label for="">telefono  </label>
  <input type="number" name="telefono">
    <input type="submit" value="borrar">
  </form>
  <?php echo $respuesta2?>
  <h3>VISUALIZAR INFORMACION DE UN CONTACTO</h3>
  <form action="?method=infoContacto" method="post">
  <label for="">telefono  </label>
  <input type="number" name="telefono">
  <input type="submit" value="mostrar">
  </form>
  <?php echo $respuesta4?>
  <?php

  /** Muestra los datos del contacto que has elegido por numero de telefono */

  foreach($contacto as $fila){

    
    echo "<br> TIPO: " .$fila[0];
    echo "<br> nombre: " .$fila[1];
    if(!empty($fila[2])){
      echo "<br> apellido:". $fila[2];
    }else{
      echo "<br> email: ". $fila[3];
    }
    echo "<br> direccion: " .$fila[4];
    echo "<br> telefono: " .$fila[5];
    
}



?>

<h3>ACTUALIZAR UN CONTACTO POR NUMERO DE TELEFONO</h3>
  

<form action="?method=seleccionarAc" method="post">
   <select name="tipo">
  <option value="1">EMPRESA</option>
  <option value="2" >PERSONA</option>
  <input type="submit" value="seleccionar">
  </select>
  </form>
  <?php echo $mensaje1?>


  <?php echo $respuesta3?>
  <form action="?method=subirfichero" method="post" enctype="multipart/form-data">
        <p>
        <label for="mifich" >Seleciona un fichero </label>
        <input type="file" name="myfile" id="mifich">
        <input type="submit" name= "envio" value="Enviar Fichero">
        </p>
     
    </form>
    <?php
    echo $flag
        
   ?>
</div>
 <!--link para cerrar la sesion -->

    <h4><a href="?method=close">Cerrar sesión</a></h4>


</body>
</html>