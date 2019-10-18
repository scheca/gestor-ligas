<?php
//Nota de Copyright
//-----------------
//
//     Copyright (C) 2005 Sergio Checa Blanco, sergio.checa@gmail.com
//
//     Este documento puede ser usado en los t�rminos descritos en la
//     Licencia P�blica GNU versi�n 2 o posterior.
//
//
//-----------------------------------------------------------------------


require_once('lib/liga-autenticacion.inc.php');
require_once('lib/liga-cabecera.php');
require_once('lib/liga-tabla-principal.php');
require_once('lib/liga-col-izquierda.php');
require_once('lib/liga-final.php');
require_once('config/bd_config.inc.php');

$nivel_acceso = 0; // Definir nivel de acceso para esta p�gina.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+privilegios+de+aministraci�n.+Acceso+denegado.");
  exit;
}

// Comprobar si se ha especificado un orden
$order = "login";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "submitEdit") {
    $id        = $_POST['idUsuario'];
    $login     = $_POST['login'];
    $nombre    = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono  = $_POST['telefono'];
    $email     = $_POST['email'];
    $direccion = $_POST['direccion'];
    
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para comprobar si ya existe un usuario con ese login
    $ssql = "SELECT * FROM usuario WHERE login='$login'";

    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    if (mysqli_num_rows($rs)==0){
      // Sentencia SQL para actualizar la informaci�n del usuario
      $ssql = "UPDATE usuario SET
               login='".$login."',
               nombre='".$nombre."',
               apellidos='".$apellidos."',
               telefono='".$telefono."',
               email='".$email."',
               direccion='".$direccion."'
               WHERE ID='".$id."'";

      // Ejecutar la sentencia de insercion
      $rs = mysqli_query($conn,$ssql);
      mysqli_close($conn);
      header ("Location: liga-admin-usuarios.php?order=".$order);
      die;
    }
    else {
      mysqli_close($conn);
      // Si ya existe un usuario con ese login, se debe indicar el error
      header ("Location: liga-admin-usuarios.php?login=$login&status=wrong&order=".$order);
      die;
    }
  }
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Tratar el borrado de usuarios (si se ha especificado)
    if ($_GET['action'] == "borrar") {
      // Conectar con la base de datos
      $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
      // Seleccionar la BBDD
      mysqli_select_db($conn,"$sql_db");
	
      // Sentencia SQL para obtener las ligas del usuario a borrar
      $ssql = "SELECT id FROM liga WHERE usuario='".$id."'";
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);

      // Borrar una a una las ligas del usuario
      while ($liga = mysqli_fetch_array($rs)) {
	$id_a_borrar = $liga['id'];
	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM juega WHERE liga=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);
	
	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM partido WHERE liga=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);
	
	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM jornada WHERE liga=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);

	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM equipo WHERE liga=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);
	
	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM categoria_candidata WHERE liga=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);
	
	// Sentencia SQL para borrar la liga
	$ssql = "DELETE FROM liga WHERE id=".$id_a_borrar;
	// Ejecutar la sentencia
	mysqli_query($conn,$ssql);
      }

      // Sentencia SQL para obtener las ligas del usuario a borrar
      $ssql = "DELETE FROM usuario WHERE id='".$id."'";
      // Ejecutar la sentencia
      mysqli_query($conn,$ssql);
      
      mysqli_free_result($rs);
      mysqli_close($conn);
      // Volver a presentar la p�gina
      header("Location: liga-admin-usuarios.php?order=".$order);
    }
  }
}


cabecera();
comienzo_tabla_principal();
columna_izquierda();

// Mostrar una tabla con la informacion de un usuario en cada fila

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Administraci&oacute;n</h1><br />
    <h2>Gesti&oacute;n de usuarios</h2>
";

// Tratar las acciones que necesitan escribir html
if (isset($_GET['action'])) {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == "editar") {
      // Obtener la informaci�n del usuario que se quiere editar
	
      // Conectar con la base de datos
      $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
      // Seleccionar la BBDD
      mysqli_select_db($conn,"$sql_db");
    
      // Sentencia SQL para obtener el listado de usuarios (menos el admin)
      $ssql = "SELECT login,nombre,apellidos,email,direccion,telefono FROM usuario WHERE ID='".$id."'";
   
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);
      $usuario = mysqli_fetch_array($rs);
      mysqli_free_result($rs);
      mysqli_close($conn);

      // Presentar el formulario de edici�n de usuarios
      echo "
    <h3>Edite la informaci&oacute;n del usuario</h3>
    <div align=\"center\">
    <form action=\"liga-admin-usuarios.php?order=".$order."&action=submitEdit\" method=\"post\" name=\"form_registro\">
      <input type=\"hidden\" name=\"idUsuario\" value=\"".$id."\" />
      <table class=\"normal\">
        <tr>
          <td class=\"formcolor\">Nombre: <font color=\"red\">*</font></td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"nombre\" size=\"60\" maxlength=\"255\" value=\"".$usuario['nombre']."\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Apellidos:</td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"apellidos\" size=\"60\" maxlength=\"255\" value=\"".$usuario['apellidos']."\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Direcci&oacute;n:</td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"direccion\" size=\"60\" maxlength=\"255\" value=\"".$usuario['direccion']."\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Tel&eacute;fono:</td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"telefono\" size=\"60\" maxlength=\"9\" value=\"".$usuario['telefono']."\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Correo electr&oacute;nico: <font color=\"red\">*</font></td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"email\" size=\"60\" maxlength=\"255\" value=\"".$usuario['email']."\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Alias (nickname): <font color=\"red\">*</font></td>
          <td class=\"formcolor\">
            <input type=\"text\" name=\"login\" size=\"60\" maxlength=\"255\" value=\"".$usuario['login']."\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">&nbsp;</td>
          <td class=\"formcolor\">
            <input type=\"button\" value=\"Aplicar cambios\" name=\"edit\" onClick=\"checkFormEdicion()\" />
          </td>
        </tr>
      </table>
    </form>
    </div>
    <br />
    <h4>Los campos marcados con <font color=\"red\">*</font> son obligatorios</h4>
    <br />
   ";
    }
  }
}


echo "
    <h3>Listado de usuarios</h3>
    <div align=\"center\">
    <table class=\"normal\">
      <tr>
        <td class=\"heading\" ></td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=login\">Login</a>
        </td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=nombre\">Nombre</a>
        </td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=apellidos\">Apellidos</a>
        </td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=email\">Correo electr&oacute;nico</a>
        </td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=direccion\">Direcci&oacute;n</a>
        </td>
        <td class=\"heading\" >
          <a class=\"tableheading\" href=\"liga-admin-usuarios.php?order=telefono\">Tel&eacute;fono</a>
        </td>
      </tr>
";

// Obtener el listado de usuarios (salvo el admin)

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Sentencia SQL para obtener el listado de usuarios (menos el admin)
$ssql = "SELECT ID,login,nombre,apellidos,email,direccion,telefono FROM usuario WHERE login!='admin' ORDER BY ".$order;

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

$indice = 0;
while($usuario = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada usuario
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  $link_borrado = "liga-admin-usuarios.php?order=".$order."&action=borrar&id=".$usuario['ID'];
  echo "
      <tr>
        <td class=\"".$paridad."\"><div align=\"center\">
	  <a class=\"link\" href=\"liga-admin-usuarios.php?order=".$order."&action=editar&id=".$usuario['ID']."\">
            <img src='img/icons/edit.gif' border='0' alt='editar' title='editar' />
          </a>
          <a class=\"link\" href=\"#\"
            onClick='confirmationDelUsuario(\"".$link_borrado."\")'>
            <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
          </a>
          </div>
        </td>
        <td class=\"".$paridad."\" >".$usuario['login']."</td>
        <td class=\"".$paridad."\" >".$usuario['nombre']."</td>
        <td class=\"".$paridad."\" >".$usuario['apellidos']."</td>
        <td class=\"".$paridad."\" >".$usuario['email']."</td>
        <td class=\"".$paridad."\" >".$usuario['direccion']."</td>
        <td class=\"".$paridad."\" >".$usuario['telefono']."</td>
      </tr>
    ";
  $indice++;
}
mysqli_free_result($rs);
mysqli_close($conn);

echo "
    </table>
    <br />
    </div>
";

if (isset($_GET['login']) && isset($_GET['status'])) {
  $login  = $_GET['login'];
  $status = $_GET['status'];
  if ($status == "registered") {
    echo "
      <br />
      <h3>Ha sido registrado con el alias <font color=\"red\">$login</font></h3>
    ";
  }
  else if ($status == "wrong") {
    echo "
      <br />
      <h3>El alias <font color=\"red\">$login</font> no est&aacute; disponible. Por favor, escoja otro</h3>
    ";
  }
}

echo "
    </div>
  </td>
";


final_pagina();

?>