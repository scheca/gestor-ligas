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


require_once('lib/liga-cabecera.php');
require_once('lib/liga-tabla-principal.php');
require_once('lib/liga-col-izquierda.php');
require_once('lib/liga-final.php');

require_once('config/bd_config.inc.php');
// Usamos la sesion de nombre definido.
session_name($usuarios_sesion);
// Iniciamos el uso de sesiones
session_start();

if (isset($_GET['action'])) {
  $action = $_GET['action'];
  if ($action == "register") {
    $login     = $_POST['login'];
    $passwd    = $_POST['password1'];
    $nombre    = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono  = $_POST['telefono'];
    $email     = $_POST['email'];
    $direccion = $_POST['direccion'];

    // COMIENZO DEL REGISTRO

    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para comprobar si ya existe un usuario con ese login
    $ssql = "SELECT * FROM usuario WHERE login='$login'";

    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    if (mysqli_num_rows($rs)==0){
      // Insertar el usuario con su contrase�a
      $ssql =
	"INSERT INTO usuario
         (login, password, nombre, apellidos, telefono, email, direccion)
         VALUES
         ('$login',md5('$passwd'),'$nombre','$apellidos','$telefono','$email','$direccion')";
      // Ejecutar la sentencia de insercion
      $rs = mysqli_query($conn,$ssql);
      header ("Location: liga-registro.php?login=$login&status=registered");
      die;
    }
    else {
      // Si ya existe un usuario con ese login, se debe indicar el error
      header ("Location: liga-registro.php?login=$login&status=wrong");
      die;
    }
  }
  mysqli_free_result($rs);
  mysqli_close($conn);
}


cabecera();
comienzo_tabla_principal();
columna_izquierda();

// Presentar el formulario de registro

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1><a class=\"pagetitle\" href=\"liga-registro.php\">Registro de usuarios</a></h1><br />
    <h2>Rellene el siguiente formulario para ser dado de alta</h2>
    <form method=\"post\" name=\"form_registro\" action=\"liga-registro.php?action=register\">
      <table class=\"normalnoborder\">
        <tr>
          <td class=\"form\">Nombre: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"nombre\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Apellidos:</td>
          <td class=\"form\">
            <input type=\"text\" name=\"apellidos\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Direcci&oacute;n:</td>
          <td class=\"form\">
            <input type=\"text\" name=\"direccion\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);changeCase(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Tel&eacute;fono de contacto:</td>
          <td class=\"form\">
            <input type=\"text\" name=\"telefono\" value=\"\" size=\"60\" maxlength=\"9\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Correo electr&oacute;nico: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"email\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Alias (nickname): <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"login\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Contrase&ntilde;a (m&iacute;nimo 4 caracteres): <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"password1\" value=\"\" size=\"60\" maxlength=\"255\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Repita contrase&ntilde;a: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"password2\" value=\"\" size=\"60\" maxlength=\"255\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\"></td>
          <td class=\"form\">
            <input type=\"button\" name=\"send\" value=\"Registrarse\" onClick=\"checkForm()\" />
          </td>
        </tr>
      </table>
    </form>
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