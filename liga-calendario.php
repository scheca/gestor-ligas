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

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>".$datosLiga['nombre']."</h1><br />
    <h2>Calendario</h2>
    <br />
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li><a href=\"liga-clasificacion.php?idLiga=".$idLiga."\">Clasificaci&oacute;n</a></li>
        <li><a href=\"liga-equipos.php?idLiga=".$idLiga."\">Equipos</a></li>
        <li id=\"active\"><a href=\"liga-calendario.php?idLiga=".$idLiga."\" id=\"current\">Calendario</a></li>
        <li><a href=\"liga-resultados.php?idLiga=".$idLiga."\">Resultados</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>    
    <div align=\"center\">
      Pulse sobre la jornada deseada para ver los partidos de la misma
      <br/>&nbsp;<br/>
      <table class=\"estrecha\">
        <tr>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">N&uacute;mero de jornada</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Fecha</a>
          </td>
        </tr>
";

// Obtener el listado de jornadas

// Conectar con la base de datos
$conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysql_select_db("$sql_db",$conn); 

// Sentencia SQL para obtener el listado de jornadas ya incluidas en la liga
$ssql = "SELECT ID,fecha FROM jornada WHERE liga='".$idLiga."'";

// Ejecutar la sentencia
$rs = mysql_query($ssql,$conn);

$indice = 0;
while($jornada = mysql_fetch_array($rs)) {
  // Escribir fila a fila cada jornada
  ($indice % 2 == 0) ? ($paridad="evenJornada") : ($paridad="oddJornada");
  echo "
      <tr>
        <td class=\"".$paridad."\" >
          <a class=\"linkCal\" 
             href=\"liga-calendario.php?idLiga=".$idLiga."&idJornada=".$jornada['ID']."\">
             Jornada ".$jornada['ID']."
          </a>
        </td>
        <td class=\"".$paridad."\" >".parseFechaRev($jornada['fecha'])."</td>
      </tr>
    ";
  $indice++;
}
mysql_free_result($rs);

echo "
      </table>
    <br/>
";

// Obtener la lista de partidos de una jornada (si es que se ha especificado una jornada)

if (isset($_GET['idJornada'])) {
  $idJornada = $_GET['idJornada'];

  $ssql = "
  (
     SELECT partido.id AS ID,local.nombre AS local,
            fecha,hora,juega.tantos_local,juega.tantos_visitante,visitante.nombre AS visitante,partido.campo
     FROM equipo AS local,partido,juega,equipo AS visitante
     WHERE local.id=partido.local AND
           visitante.id=partido.visitante AND
           partido.liga=".$idLiga." AND 
           partido.jornada=".$idJornada." AND 
           partido.id=juega.partido
  )
  UNION
  (
     SELECT partido.id,local.nombre,
            fecha,hora,NULL tantos_local,NULL tantos_visitante,visitante.nombre,partido.campo
     FROM equipo AS local,partido,equipo AS visitante
     WHERE local.id=partido.local AND
           visitante.id=partido.visitante AND
           partido.liga=".$idLiga." AND 
           partido.jornada=".$idJornada." AND 
           partido.id NOT IN (select juega.partido from juega)
  )
  ORDER BY ID
  ";

  // Ejecutar la sentencia
  $rs = mysql_query($ssql,$conn);

  echo "
      <div align=\"center\">
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Fecha</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Hora</a>
          </td>
          <td class=\"heading\" align=\"right\" >
            <a class=\"tableheading\" href=\"#\">Local</a>
          </td>
          <td class=\"heading\" align=\"center\">
            <a class=\"tableheading\" href=\"#\">Resultado</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Visitante</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Campo</a>
          </td>
        </tr>
  ";

  $indice = 0;
  while($partido = mysql_fetch_array($rs)) {
    // Escribir fila a fila cada partido
    ($indice % 2 == 0) ? ($paridad="evenPartido") : ($paridad="oddPartido");
    echo "
      <tr>
        <td class=\"".$paridad."\" >".parseFechaRev($partido['fecha'])."</td>
        <td class=\"".$paridad."\" >".$partido['hora']."</td>
        <td class=\"".$paridad."\"><div align=\"right\">".$partido['local']."</div></td>
        <td class=\"".$paridad."\"><div align=\"center\">".
        $partido['tantos_local']."&nbsp;-&nbsp;".$partido['tantos_visitante']."</div></td>
        <td class=\"".$paridad."\" align=\"left\">".$partido['visitante']."</td>
        <td class=\"".$paridad."\">".$partido['campo']."</td>
      </tr>
    ";
    $indice++;
  }
  mysql_free_result($rs);
  echo "
    </table>
    </div>
    <br/>
  ";
} //if

mysql_close();

echo "
    </div>
    </div>
    </div>
  </td>
";


final_pagina();

?>