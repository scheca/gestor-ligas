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

$nivel_acceso = 100; // Definir nivel de acceso para esta p�gina.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+permisos+suficientes.+Acceso+denegado.");
  exit;
}

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "submitEdit") {
    $id        = $_SESSION['usuario_id'];
    $login     = $_POST['login'];
    $nombre    = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono  = $_POST['telefono'];
    $email     = $_POST['email'];
    $direccion = $_POST['direccion'];
    
    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 
    
    // Sentencia SQL para comprobar si ya existe un usuario con ese login
    $ssql = "SELECT * FROM usuario WHERE login='$login'";

    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);

    if (mysql_num_rows($rs)==0){
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
      $rs = mysql_query($ssql,$conn);
      mysql_close();
      header ("Location: liga-user-datos.php");
      die;
    }
    else {
      mysql_close();
      // Si ya existe un usuario con ese login, se debe indicar el error
      header ("Location: liga-user-datos.php?login=$login&status=wrong");
      die;
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
    <h1>Datos personales</h1><br />
    <h2>Modificar datos personales</h2>
";

// Obtener la informaci�n del usuario que se quiere editar
	
// Conectar con la base de datos
$conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysql_select_db("$sql_db",$conn); 
    
// Sentencia SQL para obtener el listado de usuarios (menos el admin)
$ssql = "SELECT login,nombre,apellidos,email,direccion,telefono FROM usuario WHERE ID='".$_SESSION['usuario_id']."'";
   
// Ejecutar la sentencia
$rs = mysql_query($ssql,$conn);
$usuario = mysql_fetch_array($rs);
mysql_free_result($rs);
mysql_close();

// Presentar el formulario de edici�n de usuarios
echo "
    <h3>Si lo desea, puede modificar sus datos personales</h3>
    <div align=\"center\">
    <form action=\"liga-user-datos.php?action=submitEdit\" method=\"post\" name=\"form_registro\">
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