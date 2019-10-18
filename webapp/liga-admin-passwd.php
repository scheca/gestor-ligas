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

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "submitCambio") {
    // Comprobar que el due�o de la sesi�n actual es el admin
    if ($_SESSION['usuario_login'] != "admin") {
      header("Location: liga-admin-passwd.php?status=badPass");
      exit;
    }

    $pass_actual = $_POST['pass_actual'];
    $pass_nueva1 = $_POST['pass_nueva1'];
    $pass_nueva2 = $_POST['pass_nueva2'];
    
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para comprobar la password actual
    $ssql = "SELECT ID,login,password FROM usuario
             WHERE login='admin'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    if (mysqli_num_rows($rs) != 1) {
      header("Location: liga-admin-passwd.php?status=badPass");
      exit;
    }

    // Obtener los datos del administrador
    $admin = mysqli_fetch_array($rs);

    $pass_actual_md5 = md5($pass_actual);
    // Comprobar la contrase�a actual
    if (!(($admin['password'] == $pass_actual_md5) &&
	  ($pass_actual_md5 == $_SESSION['usuario_password']))) {
      header("Location: liga-admin-passwd.php?status=badPass");
      exit;
    }
    mysqli_free_result($rs);
    
    // Comprobar que la nueva contrase�a se ha introducido bien
    if ($pass_nueva1 != $pass_nueva2 || strlen($pass_nueva1)<6) {
      header("Location: liga-admin-passwd.php?status=badPass");
      exit;
    }
      
    // Sentencia SQL para actualizar la password de admin
    $ssql = "UPDATE usuario SET
             password=md5('".$pass_nueva1."')
             WHERE login='admin'";
    
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    mysqli_close($conn);
    // Volver a presentar la p�gina
    header("Location: liga-admin-passwd.php?status=changed");
  }
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

// Mostrar un formulario de cambio de contrase�a

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Administraci&oacute;n</h1><br />
    <h2>Cambio de contrase&ntilde;a</h2>
    <div align=\"center\">
    <form action=\"liga-admin-passwd.php?action=submitCambio\" method=\"post\" name=\"form_passwd\">
      <table class=\"normal\">
        <tr>
          <td class=\"formcolor\">Introduzca su contrase&ntilde;a actual:</td>
          <td class=\"formcolor\">
            <input type=\"password\" name=\"pass_actual\" size=\"60\" maxlength=\"255\" value=\"\" />
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Introduzca la contrase&ntilde;a nueva (m&iacute;nimo 6 caracteres):</td>
          <td class=\"formcolor\">
            <input type=\"password\" name=\"pass_nueva1\" size=\"60\" maxlength=\"255\" value=\"\" />
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Repita la contrase&ntilde;a nueva:</td>
          <td class=\"formcolor\">
            <input type=\"password\" name=\"pass_nueva2\" size=\"60\" maxlength=\"255\" value=\"\" />
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">&nbsp;</td>
          <td class=\"formcolor\">
            <input type=\"submit\" value=\"Cambiar\" name=\"cambiar\" />
          </td>
        </tr>
      </table>
    </form>
    </div>
";

if (isset($_GET['status'])) {
  $status = $_GET['status'];
  if ($status == "changed") {
    echo "
      <br />
      <h3>El cambio de contrase&ntilde;a concluy&oacute; con &eacute;xito</h3>
    ";
  }
  else if ($status == "badPass") {
    echo "
      <br />
      <h3>La contrase&ntilde;a no se cambi&oacute;. Compruebe los datos introducidos</h3>
    ";
  }
}

echo "
    <br />
    </div>
  </td>
";

final_pagina();

?>