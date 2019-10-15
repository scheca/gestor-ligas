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


require_once('lib/liga-cabecera.php');
require_once('lib/liga-tabla-principal.php');
require_once('lib/liga-col-izquierda.php');
require_once('lib/liga-final.php');
require_once('lib/liga-funciones-mix.php');

if (isset($_GET['action'])) {
  $action = $_GET['action'];
  if ($action == "install") {
    $sql_host    = $_POST['sql_host'];
    $sql_db      = $_POST['sql_db'];
    $sql_login   = $_POST['sql_usuario'];
    $sql_pass    = $_POST['sql_pass1'];
    $admin_pass  = $_POST['admin_pass1'];

    // COMIENZO DE INSTALACION

    // Conectar con la base de datos
    $conn = mysql_connect($sql_host,$sql_login,$sql_pass);
    if (!$conn) {
      die("No se pudo conectar con el servidor de bases de datos: ".mysql_error());
    }
    if (!mysql_query("CREATE DATABASE ".$sql_db)) {
      die("No se pudo crear la base de datos: ".mysql_error());
    }
    
    $content = "<?php

// Identificador de la sesion
\$usuarios_sesion = \"gestor-liga\";

// Datos de conexión a la Base de datos (MySql)
\$sql_host    = \"".$sql_host."\";    // Host, nombre del servidor o IP del servidor Mysql.
\$sql_db      = \"".$sql_db."\";      // Nombre de la BD
\$sql_usuario = \"".$sql_login."\";   // Usuario de Mysql
\$sql_pass    = \"".$sql_pass."\";    // Contraseña de Mysql

?>
";    
    if (crear_archivo("config/bd_config.inc.php", $content)){
      // Seleccionar la BBDD
      mysql_select_db($sql_db,$conn);
      $crea_usuario = 
"
CREATE TABLE usuario (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  login TINYTEXT NOT NULL,
  password TINYTEXT NOT NULL,
  nivel_acceso SMALLINT(4) UNSIGNED NOT NULL DEFAULT '100',
  nombre TINYTEXT NOT NULL,
  apellidos TINYTEXT,
  telefono VARCHAR(9),
  email TINYTEXT NOT NULL,
  direccion TINYTEXT,
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID)
) TYPE=INNODB
";
      mysql_query($crea_usuario,$conn);

      $crea_categoria = 
"
CREATE TABLE categoria (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  deporte VARCHAR(100) NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY ID (ID),
  UNIQUE KEY (deporte)
) TYPE=INNODB
";
      mysql_query($crea_categoria,$conn);

      $crea_liga = 
"
CREATE TABLE liga (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario MEDIUMINT UNSIGNED NOT NULL,
  nombre TINYTEXT NOT NULL,
  deporte MEDIUMINT UNSIGNED,
  metodo_puntuacion TINYTEXT NOT NULL,
  ptos_victoria MEDIUMINT(6) NOT NULL,
  ptos_empate MEDIUMINT(6) NOT NULL,
  ptos_derrota MEDIUMINT(6) NOT NULL,
  INDEX (deporte),
  FOREIGN KEY (deporte) REFERENCES categoria (ID)
    ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX (usuario),
  FOREIGN KEY (usuario) REFERENCES usuario (ID)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID)
) TYPE=INNODB
";
      mysql_query($crea_liga,$conn);

      $crea_candidata = 
"
CREATE TABLE categoria_candidata (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre TINYTEXT NOT NULL,
  liga MEDIUMINT UNSIGNED NOT NULL,
  INDEX (liga),
  FOREIGN KEY (liga) REFERENCES liga (ID)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (ID),
  UNIQUE KEY ID (ID)
) TYPE=INNODB
";
      mysql_query($crea_candidata,$conn);

      $crea_equipo = 
"
CREATE TABLE equipo (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre TINYTEXT NOT NULL,
  campo TINYTEXT,
  liga MEDIUMINT UNSIGNED NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY ID (ID),
  INDEX (liga),
  FOREIGN KEY (liga) REFERENCES liga (ID)
    ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=INNODB
";
      mysql_query($crea_equipo,$conn);

      $crea_jornada = 
"
CREATE TABLE jornada (
  ID MEDIUMINT UNSIGNED NOT NULL, -- numero de jornada dentro de la liga
  fecha DATE,
  liga MEDIUMINT UNSIGNED NOT NULL,
  INDEX (liga),
  FOREIGN KEY (liga) REFERENCES liga (ID)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (ID, liga)
) TYPE=INNODB
";
      mysql_query($crea_jornada,$conn);

      $crea_partido = 
"
CREATE TABLE partido (
  ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, -- numero de partido dentro de la jornada
  local MEDIUMINT UNSIGNED NOT NULL,
  visitante MEDIUMINT UNSIGNED NOT NULL,
  fecha DATE,
  hora TIME,
  campo TINYTEXT,
  detalles TEXT,
  jornada MEDIUMINT UNSIGNED NOT NULL,
  liga MEDIUMINT UNSIGNED NOT NULL,
  INDEX (jornada, liga),
  FOREIGN KEY (jornada, liga) REFERENCES jornada (ID, liga)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (ID, jornada, liga)
) TYPE=INNODB
";
      mysql_query($crea_partido,$conn);

      $crea_juega = 
"
CREATE TABLE juega (
  tantos_local SMALLINT UNSIGNED,
  tantos_visitante SMALLINT UNSIGNED,
  partido MEDIUMINT UNSIGNED NOT NULL,
  jornada MEDIUMINT UNSIGNED NOT NULL,
  liga MEDIUMINT UNSIGNED NOT NULL,
  INDEX (partido, jornada, liga),
  FOREIGN KEY (partido, jornada, liga) REFERENCES partido (ID, jornada, liga)
    ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (partido, jornada, liga)
) TYPE=INNODB
";
      mysql_query($crea_juega,$conn);

      // Crear la cuenta de administrador
      $sql_admin = "INSERT INTO usuario (login,password,nombre,nivel_acceso)
                    VALUES ('admin',md5('".$admin_pass."'),'Administrador',0)";
      mysql_query($sql_admin,$conn);

      mysql_close();
      Header("Location: liga-index.php");
    }
    else {
      mysql_close($conn);
      die("No se pudo crear el fichero de configuración.<br/>Asegurese de que el directorio config tiene permisos de escritura");
    }
  }
}


cabecera();
comienzo_tabla_principal();
//columna_izquierda();

// Presentar el formulario de registro

echo "
  <div >
  <div >
  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" >
    <tr>
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1><a class=\"pagetitle\" href=\"#\">Instalaci&oacute;n</a></h1>
    <h2>Rellene el siguiente formulario con los datos necesarios para crear la base de datos</h2>
    <h4>(Si desconoce alguno de los datos requeridos o la versi&oacute;n de su servidor MySQL no es 4.1 o superior, consulte con su administrador de bases de datos)</h4>
    <form method=\"post\" name=\"form_install\" action=\"liga-instalar.php?action=install\">
      <table class=\"normalnoborder\">
        <tr>
          <td class=\"form\">Host donde est&aacute; ubicada la base de datos: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"sql_host\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Nombre de la base de datos: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"sql_db\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Nombre de usuario para el acceso a la base de datos: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"text\" name=\"sql_usuario\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Contrase&ntilde;a de acceso a la base de datos: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"sql_pass1\" value=\"\" size=\"60\" maxlength=\"9\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Repita la contrase&ntilde;a: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"sql_pass2\" value=\"\" size=\"60\" maxlength=\"255\"
             onChange=\"quitarBlancos(this);\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Contrase&ntilde;a de administrador (m&iacute;nimo 4 caracteres): <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"admin_pass1\" value=\"\" size=\"60\" maxlength=\"255\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\">Repita la contrase&ntilde;a de administrador: <font color=\"red\">*</font></td>
          <td class=\"form\">
            <input type=\"password\" name=\"admin_pass2\" value=\"\" size=\"60\" maxlength=\"255\"/>
          </td>
        </tr>
        <tr>
          <td class=\"form\"></td>
          <td class=\"form\">
            <input type=\"button\" name=\"send\" value=\"Instalar\" onClick=\"checkFormInstall()\" />
          </td>
        </tr>
      </table>
    </form>
    <br/>
    <h4>Se crear&aacute; la cuenta de administrador, cuyo login es 'admin' y su contrase&ntilde;a, la que se ha introducido como contrase&ntilde;a de administrador</h4>
    <h4>Los campos marcados con <font color=\"red\">*</font> son obligatorios, aunque se pueden dejar en blanco si es preciso</h4>
    </div>
  </td>
";


final_pagina();

?>