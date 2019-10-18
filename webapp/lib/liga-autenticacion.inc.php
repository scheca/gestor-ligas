<?php

//  Autentificator
//  Gesti�n de Usuarios PHP+Mysql+sesiones
//  by Pedro Noves V. (Cluster)
//  clus@hotpop.com
//  v1.0  - 17/04/2002 Versi�n inicial.
//  v1.01 - 24/04/2002 Solucionado error sintactico en aut_verifica.inc.php.
//  v1.05 - 17/05/2002 Optimizaci�n c�digo aut_verifia.inc.php
//  v1.06 - 03/06/2002 Correcci�n de errores de la versi�n 1.05 y error con navegadores Netscape
//  v2.00 - 18/08/2002 Optimizaci�n c�digo + Seguridad.
//                     Ahora funciona con la directiva registre_globals= OFF. (PHP > 4.1.x)
//                     Optimizaci�n Tablas SQL. (rangos de tipos).
//  v2.01 - 16/10/2002 Solucionado "despistes" de la versi�n 2.00 de Autentificator
//                     en aut_verifica.inc.php y aut_gestion_usuarios.php que ocasinaban errores al trabajar
//                     con la directiva registre_globals= OFF.
//                     Solucionado error definici�n nombre de la sessi�n.
//
// Descripci�n:
// Gesti�n de P�ginas restringidas a Usuarios, con nivel de acceso
// y gesti�n de errores en el Login
// + administraci�n de usuarios (altas/bajas/modificaciones)
//
// Licencia GPL con estas extensiones:
// - Uselo con el fin que quiera (personal o lucrativo).
// - Si encuentra el c�digo de utilidad y lo usas, mandeme un mail si lo desea.
// - Si mejora el c�digo o encuentra errores, hagamelo saber el mail indicado.
//
// Instalaci�n y uso del Gestor de usuarios en:
// documentacion.htm
// ----------------------------------------------------------------------------


// Motor autentificaci�n usuarios.

// Cargar datos conexion y otras variables.
require ("config/bd_config.inc.php");

// Chequear p�gina que lo llama para devolver errores a dicha p�gina.

$url = explode("?",$_SERVER['HTTP_REFERER']);
$redir=$url[0];

// Chequear si se llama directo al script.
if ($_SERVER['HTTP_REFERER'] == "") {
  die (Header
       ("Location: liga-error.php?error=El+acceso+a+trav�s+de+la+barra+del+navegador+est�+prohibido.+Acceso+denegado."));
  exit;
}


// Chequeamos si se est� autenticando un usuario por medio del formulario
if (isset($_POST['user']) && isset($_POST['pass'])) {
  $login    = $_POST['user'];
  $password = $_POST['pass'];

  // Conexi�n base de datos.
  // Si no se puede conectar a la BD salimos del scrip con error 0 y
  // redireccionamos a la pagina de error.
  $conn= mysqli_connect("$sql_host", "$sql_usuario", "$sql_pass")
    or die(header ("Location: liga-error.php?error=Fall�+la+conexi�n+con+la+base+de+datos."));
  mysqli_select_db($conn,"$sql_db");

  // Realizamos la consulta a la BD para chequear datos del Usuario.
  $usuario_consulta = mysqli_query($conn,"SELECT ID,login,password,nivel_acceso FROM usuario WHERE login='$login'")
    or die(header ("Location: liga-error.php?error=Usuario+o+contrase�a+inv�lidos."));

  // Miramos el total de resultados de la consulta (si es distinto de 0 es que existe el usuario)
  if (mysqli_num_rows($usuario_consulta) != 0) {

    // Eliminamos barras invertidas y dobles en sencillas
    $login = stripslashes($login);
    // encriptamos el password en formato md5 irreversible.
    $password = md5($password);

    // Almacenamos datos del Usuario en un array para empezar a chequear.
    $usuario_datos = mysqli_fetch_array($usuario_consulta);
  
    // Liberamos la memoria usada por la consulta, ya que tenemos estos datos en el Array.
    mysqli_free_result($usuario_consulta);
    // cerramos la Base de dtos.
    mysqli_close($conn);
    
    // Chequeamos el nombre del usuario otra vez contrastandolo con la BD
    // esta vez sin barras invertidas, etc ...
    // si no es correcto, salimos del script y redireccionamos a la
    // p�gina de error.
    if ($login != $usuario_datos['login']) {
      Header ("Location: liga-error.php?error=Usuario+o+contrase�a+inv�lidos.");
      exit;
    }

    // Si el password no es correcto ..
    // salimos del script y redireccinamos hacia la p�gina de error
    if ($password != $usuario_datos['password']) {
      Header ("Location: liga-error.php?error=Usuario+o+contrase�a+inv�lidos.");
      exit;
    }

    // Paranoia: destruimos las variables login y password usadas
    unset ($login);
    unset ($password);

    // En este punto, el usuario ya esta validado.
    // Grabamos los datos del usuario en una sesion.
    
     // Le damos un mobre a la sesion.
    session_name($usuarios_sesion);
    // Paranoia: decimos al navegador que no "cachee" esta p�gina.
    session_cache_limiter('nocache,private');
     // Iniciar la sesion
    session_start();
    
    // Asignamos variables de sesi�n con datos del usuario para el uso en el
    // resto de p�ginas autentificadas.

    // Definimos usuarios_id como IDentificador del usuario en nuestra BD de usuarios
    $_SESSION['usuario_id']=$usuario_datos['ID'];
    
    // Definimos usuario_nivel con el Nivel de acceso del usuario de nuestra BD de usuarios
    $_SESSION['usuario_nivel']=$usuario_datos['nivel_acceso'];
    
    // Definimos usuario_nivel con el Nivel de acceso del usuario de nuestra BD de usuarios
    $_SESSION['usuario_login']=$usuario_datos['login'];

    // Definimos usuario_password con el password del usuario de la sesi�n actual (formato md5 encriptado)
    $_SESSION['usuario_password']=$usuario_datos['password'];


    // Hacemos una llamada a si mismo (script) para que queden disponibles
    // las variables de session en el array asociado $HTTP_...
    $pag=$_SERVER['PHP_SELF'];
    Header ("Location: $pag");
    exit;
    
  }
  else {
    // Si no esta el nombre de usuario en la BD o el password ..
    // se devuelve a pagina q lo llamo con error
    Header ("Location: liga-error.php?error=Usuario+o+contrase�a+inv�lidos.");
    exit;}
}
else {

  // -------- Chequear sesi�n existe -------

  // usamos la sesion de nombre definido.
  session_name($usuarios_sesion);
  // Iniciamos el uso de sesiones
  session_start();
  
  // Chequeamos si estan creadas las variables de sesi�n de identificaci�n del usuario,
  // El caso mas comun es el de una vez "matado" la sesion se intenta volver hacia atras
  // con el navegador.
  
  if (!isset($_SESSION['usuario_login']) && !isset($_SESSION['usuario_password'])){
    // Borramos la sesion creada por el inicio de session anterior
    session_destroy();
    die (Header("Location: liga-error.php?error=No+ha+iniciado+sesi�n.+Acceso+denegado."));
    exit;
  }
}
?>
