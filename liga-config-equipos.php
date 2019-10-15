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

// Comprobar que se está trabajando sobre una liga en particular
if (!isset($_SESSION['idLiga']))
{
  header ("Location: liga-error.php?error=Acceso+denegado+a+la+creación+de+equipos.");
  exit;
}

// Comprobar si se ha especificado un orden
$order = "nombre";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}


// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "nuevoEquipo") {
    $nombre  = $_POST['nombreEquipo'];
    $campo   = $_POST['campo'];
    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 
    // Discernir si se trata de una nueva liga o de una ya existente
    // Sentencia SQL para actualizar la información de la liga
    $ssql = "INSERT INTO equipo (nombre,campo,liga)
             VALUES ('".$nombre."','".$campo."','".$_SESSION['idLiga']."')";
    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);
    mysql_close();
    // Volver a presentar la página
    header("Location: liga-config-equipos.php?order=".$order);

  }
  else if ($_GET['action'] == "borrar") {
    $id_a_borrar   = $_GET['id'];
    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 
    // Sentencia SQL para borrar el equipo
    $ssql = "DELETE FROM equipo WHERE ID=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);
    mysql_close();
    // Volver a presentar la página
    header("Location: liga-config-equipos.php?order=".$order);
  }
  else if ($_GET['action'] == "editarEquipo") {
    $idEquipo   = $_GET['id'];
    // Establecer una variable de sesión con el ID del equipo a editar
    $_SESSION['idEquipo'] = $idEquipo;
    // Volver a presentar la página
    header("Location: liga-config-equipos.php?order=".$order);
  }
  else if ($_GET['action'] == "cambiarEquipo") {
    $nombre   = $_POST['nombreEquipo' ];
    $campo    = $_POST['campoEquipo'];
    $idEquipo = $_SESSION['idEquipo'];

    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 
    
    $ssql = "UPDATE equipo
             SET nombre = '".$nombre."',
                 campo = '".$campo."'
             WHERE ID='".$idEquipo."' AND liga='".$_SESSION['idLiga']."'";

    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);
    mysql_close();

    // Borrar la variable de sesión que guarda el ID del equipo a editar
    unset($_SESSION['idEquipo']);
    // Volver a presentar la página
    header("Location: liga-config-equipos.php?order=".$order);
  }
}

cabecera();
echo "<body OnLoad=\"document.form_nuevo_equipo.nombreEquipo.focus();\">";
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1><a class=\"pagetitle\" href=\"liga-user-ligas.php\">Mis ligas</a></h1><br />
    <h2>Configurar los equipos participantes en la liga</h2>
    <br/>
";

// Presentar el formulario de creación de equipos
echo "
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li><a href=\"liga-config-liga.php\" >Configuraci&oacute;n</a></li>
        <li id=\"active\"><a href=\"liga-config-equipos.php\" id=\"current\">Equipos</a></li>
        <li><a href=\"liga-config-jornadas.php\">Jornadas</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>

    <div class=\"cbox\">
      <div class=\"cbox-title\">Equipos participantes</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">
      <div align=\"center\"><br />
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" ></td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-equipos.php?order=nombre\">Nombre</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-equipos.php?order=campo\">Campo</a>
          </td>
        </tr>
";

// Obtener el listado de equipos

// Conectar con la base de datos
$conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysql_select_db("$sql_db",$conn); 

// Sentencia SQL para obtener el listado de equipos que participan en la liga
$ssql = "SELECT ID,nombre,campo FROM equipo WHERE liga='".$_SESSION['idLiga']."' ORDER BY ".$order;

// Ejecutar la sentencia
$rs = mysql_query($ssql,$conn);

// Comprobar si se tiene que editar los datos de algún partido
if (isset($_SESSION['idEquipo'])) {
  $idEquipoEdicion = $_SESSION['idEquipo'];
}
else {
  $idEquipoEdicion = -1;
}

$indice = 0;
while($equipo = mysql_fetch_array($rs)) {
  // Escribir fila a fila cada equipo
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  $link_borrado = "liga-config-equipos.php?order=".$order."&action=borrar&id=".$equipo['ID'];
  echo "
      <tr>
  ";
  if ($equipo['ID'] == $idEquipoEdicion) {
    echo "
        <form action=\"liga-config-equipos.php?action=cambiarEquipo&order=".$order."\" method=\"post\" 
            name=\"form_cambiar_equipo\">
          <td class=\"".$paridad."\"><div align=\"center\">
            <input type=\"button\" name=\"modificarEquipo\" value=\"Guardar cambios\" 
             onClick=\"checkFormEditarEquipo(form_cambiar_equipo);\" />
      ";
  }
  else {
    echo "
        <td class=\"".$paridad."\"><div align=\"center\">
	  <a class=\"link\" href=\"liga-config-equipos.php?order=".$order."&action=editarEquipo&id=".$equipo['ID']."\">
            <img src='img/icons/edit.gif' border='0' alt='editar' title='editar' />
          </a>
          <a class=\"link\" name=\"equipo".$equipo['ID']."\" 
             href=\"#\"
             onClick='confirmation(\"".$link_borrado."\")'>
            <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
          </a>
    ";
  }
  echo "
          </div>
        </td>
  ";
  if ($equipo['ID'] == $idEquipoEdicion) {
    echo "
        <td class=\"".$paridad."\">
          <input type=\"text\" name=\"nombreEquipo\" size=\"50\" maxlength=\"255\" align=\"left\" 
                 value=\"".$equipo['nombre']."\" />
        </td>
        <td class=\"".$paridad."\">
          <input type=\"text\" name=\"campoEquipo\" size=\"50\" maxlength=\"255\" align=\"left\" 
                 value=\"".$equipo['campo']."\" />
        </td>
        </form>
    ";
  }
  else {
    echo "
        <td class=\"".$paridad."\" >".$equipo['nombre']."</td>
        <td class=\"".$paridad."\" >".$equipo['campo']."</td>
    ";
  }
  echo "
      </tr>
  ";
  $indice++;
}
mysql_free_result($rs);
mysql_close();

echo "
    </table>
    </div>
    <br />

        <form action=\"liga-config-equipos.php?action=nuevoEquipo&order=".$order."\" method=\"post\" 
              name=\"form_nuevo_equipo\">
          <table class=\"admin\">
            <tr>
              <td class=\"form\">Nombre del equipo <font color=\"red\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"nombreEquipo\" size=\"60\" maxlength=\"255\"
                 onChange=\"quitarBlancos(this);\" />
              </td>
            </tr>
            <tr>
              <td class=\"form\">Estadio o campo de juego <font color=\"green\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"campo\" size=\"60\" maxlength=\"255\"
                 onChange=\"quitarBlancos(this);\" />
              </td>
            </tr>
            <tr>
              <td colspan=\"2\" class=\"button\">
                <input type=\"button\" name=\"messprefs\" value=\"A&ntilde;adir equipo\" 
                 onClick=\"checkFormNuevoEquipo()\" />
              </td>
            </tr>
          </table>
        </form>
      </div>
      </div>
    </div>
    <h4>Los campos marcados con <font color=\"red\">*</font> son obligatorios</h4>
    <h4>Los campos marcados con <font color=\"green\">*</font> 
        son opcionales, pero se recomienda su cumplimentaci&oacute;n</h4>

    </div>
    </div>
  </td>
  ";


final_pagina();

?>