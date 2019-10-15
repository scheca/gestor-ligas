<?php
//Nota de Copyright
//-----------------
//
//     Copyright (C) 2005 Sergio Checa Blanco, sergio.checa@gmail.com
//
//     Este documento puede ser usado en los términos descritos en la
//     Licencia Pública GNU versión 2 o posterior.
//
//
//-----------------------------------------------------------------------


require_once('lib/liga-autenticacion.inc.php');
require_once('lib/liga-cabecera.php');
require_once('lib/liga-tabla-principal.php');
require_once('lib/liga-col-izquierda.php');
require_once('lib/liga-final.php');
require_once('config/bd_config.inc.php');

$nivel_acceso = 100; // Definir nivel de acceso para esta página.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+permisos+suficientes.+Acceso+denegado.");
  exit;
}

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "submitCambio") {
    // Comprobar que está activa la variable de sesión 'usuario_id'
    if (!isset($_SESSION['usuario_id'])) {
      die (Header("Location: liga-error.php?error=No+ha+iniciado+sesión.+Acceso+denegado."));
      exit;
    }

    $id_usuario  = $_SESSION['usuario_id'];
    $pass_actual = $_POST['pass_actual'];
    $pass_nueva1 = $_POST['pass_nueva1'];
    $pass_nueva2 = $_POST['pass_nueva2'];
    
    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 

    // Sentencia SQL para comprobar la password actual
    $ssql = "SELECT login,password FROM usuario
             WHERE ID='".$id_usuario."'";
    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);

    if (mysql_num_rows($rs) != 1) {
      header("Location: liga-user-passwd.php?status=badPass");
      exit;
    }

    // Obtener los datos del usuario
    $usuario = mysql_fetch_array($rs);

    $pass_actual_md5 = md5($pass_actual);
    // Comprobar la contraseña actual
    if (!(($usuario['password'] == $pass_actual_md5) &&
	  ($pass_actual_md5 == $_SESSION['usuario_password']))) {
      header("Location: liga-user-passwd.php?status=badPass");
      exit;
    }
    mysql_free_result($rs);
    
    // Comprobar que la nueva contraseña se ha introducido bien
    if ($pass_nueva1 != $pass_nueva2 || strlen($pass_nueva1)<4) {
      header("Location: liga-user-passwd.php?status=badPass");
      exit;
    }
      
    // Sentencia SQL para actualizar la password de usuario
    $ssql = "UPDATE usuario SET
             password=md5('".$pass_nueva1."')
             WHERE ID='".$id_usuario."'";
    
    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);
    mysql_close();
    // Volver a presentar la página
    header("Location: liga-user-passwd.php?status=changed");
  }
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

// Mostrar un formulario de cambio de contraseña

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Datos personales</h1><br />
    <h2>Cambio de contrase&ntilde;a</h2>
    <div align=\"center\">
    <form action=\"liga-user-passwd.php?action=submitCambio\" method=\"post\" name=\"form_passwd\">
      <table class=\"normal\">
        <tr>
          <td class=\"formcolor\">Introduzca su contrase&ntilde;a actual:</td>
          <td class=\"formcolor\">
            <input type=\"password\" name=\"pass_actual\" size=\"60\" maxlength=\"255\" value=\"\" />
          </td>
        </tr>
        <tr>
          <td class=\"formcolor\">Introduzca la contrase&ntilde;a nueva (m&iacute;nimo 4 caracteres):</td>
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