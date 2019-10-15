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

require_once('config/bd_config.inc.php');
// Usamos la sesion de nombre definido.
session_name($usuarios_sesion);
// Iniciamos el uso de sesiones
session_start();

//$_GET['idLiga'] = 32; //DEBUG!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
if (isset($_GET['idLiga'])) {
  $idLiga = $_GET['idLiga'];

  // Conectar con la base de datos
  $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysql_select_db("$sql_db",$conn); 

  // Sentencia SQL para obtener los datos de la liga
  $ssql = "SELECT nombre FROM liga WHERE ID='".$idLiga."'";

  // Ejecutar la sentencia
  $rs = mysql_query($ssql,$conn);

  if (mysql_num_rows($rs) <= 0){
    mysql_free_result($rs);
    mysql_close();
    // Presentar la página de todas las ligas
    header("Location: liga-todas-ligas.php");
    die;
  }
  $datosLiga = mysql_fetch_array($rs);
  mysql_free_result($rs);
  mysql_close();
}
else
{
  // Presentar la página de todas las ligas
  header("Location: liga-todas-ligas.php");
  die;
}

// Comprobar si se ha especificado un orden
$order = "nombre";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}

// Obtener los equipos participantes

// Conectar con la base de datos
$conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysql_select_db("$sql_db",$conn); 

// Sentencia SQL para obtener los equipos participantes
$ssql = "SELECT ID,nombre,campo FROM equipo WHERE liga='".$idLiga."' ORDER BY ".$order;

// Ejecutar la sentencia
$rs = mysql_query($ssql,$conn);

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>".$datosLiga['nombre']."</h1><br />
    <h2>Equipos participantes</h2>
    <br />
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li><a href=\"liga-clasificacion.php?idLiga=".$idLiga."\">Clasificaci&oacute;n</a></li>
        <li id=\"active\"><a href=\"liga-equipos.php?idLiga=".$idLiga."\" id=\"current\">Equipos</a></li>
        <li><a href=\"liga-calendario.php?idLiga=".$idLiga."\">Calendario</a></li>
        <li><a href=\"liga-resultados.php?idLiga=".$idLiga."\">Resultados</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>
    <div align=\"center\">
      <table class=\"estrecha\">
        <tr>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">#</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-equipos.php?idLiga=".$idLiga."&order=nombre\">Nombre</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-equipos.php?idLiga=".$idLiga."&order=campo\">Campo</a>
          </td>
        </tr>
";
$posicion = 1;
$indice = 0;
while($equipo = mysql_fetch_array($rs)) {
  // Escribir fila a fila cada equipo de la liga
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  echo "
        <tr>
          <td class=\"".$paridad."\" >".$posicion."</td>
          <td class=\"".$paridad."\" >".$equipo['nombre']."</td>
          <td class=\"".$paridad."\" >".$equipo['campo']."</td>
        </tr>
  ";
  $posicion++;
  $indice++;
}

echo "
    </table>
    <br />
    </div>
    </div>
    </div>
  </td>
";


final_pagina();

?>