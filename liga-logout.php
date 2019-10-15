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


// Cargamos variables
require ("config/bd_config.inc.php");

session_name($usuarios_sesion);
session_start();
unset($_SESSION['usuario_id']);
unset($_SESSION['usuario_nivel']);
unset($_SESSION['usuario_login']);
unset($_SESSION['usuario_password']);
session_destroy();

// Volver al usuario a la p�gina de inicio
header("Location: liga-index.php");
?>
