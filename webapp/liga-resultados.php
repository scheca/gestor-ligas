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
  $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysqli_select_db($conn,"$sql_db");

  // Sentencia SQL para obtener los datos de la liga
  $ssql = "SELECT nombre FROM liga WHERE ID='".$idLiga."'";

  // Ejecutar la sentencia
  $rs = mysqli_query($conn,$ssql);

  if (mysqli_num_rows($rs) <= 0){
    mysqli_free_result($rs);
    mysqli_close($conn);
    // Presentar la p�gina de todas las ligas
    header("Location: liga-todas-ligas.php");
    die;
  }
  $datosLiga = mysqli_fetch_array($rs);
  mysqli_free_result($rs);
  mysqli_close($conn);
}
else
{
  // Presentar la p�gina de todas las ligas
  header("Location: liga-todas-ligas.php");
  die;
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>".$datosLiga['nombre']."</h1><br/>
    <h2>Resultados</h2>
    <br />
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li><a href=\"liga-clasificacion.php?idLiga=".$idLiga."\">Clasificaci&oacute;n</a></li>
        <li><a href=\"liga-equipos.php?idLiga=".$idLiga."\">Equipos</a></li>
        <li><a href=\"liga-calendario.php?idLiga=".$idLiga."\">Calendario</a></li>
        <li id=\"active\"><a href=\"liga-resultados.php?idLiga=".$idLiga."\" id=\"current\">Resultados</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>
    <div align=\"center\">
      <table class=\"normal\">
        <tr ALIGN=\"center\">
          <td bgcolor=\"#EFEFE7\"></td>
";

// Obtener la lista de equipos participantes

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

$ssql = "SELECT ID,nombre FROM equipo WHERE liga=".$idLiga." ORDER BY nombre ASC,ID ASC";
$rs = mysqli_query($conn,$ssql);

$indice = 0;
while($equipo = mysqli_fetch_array($rs)) {
  ($indice % 2 == 0) ? ($paridad="evenRes") : ($paridad="oddRes");
  // Escribir columna a columna cada equipo de la liga
  echo "
          <td class=\"".$paridad."\">
            <script>vertical(\"".$equipo['nombre']."\");</script>
          </td>
  ";
  $indice++;
}
echo "
        </tr>
";

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Obtener todos los partidos de la liga
$sqln = "SET @n:=-1";
$sqlm = "SET @m:=-1";
$sqlPartidos = "
SELECT local.ID AS idL,
       visitante.ID AS idV,
       local.pos AS posL,
       visitante.pos AS posV,
       local.nombre AS nombreL,
       juega.tantos_local,
       juega.tantos_visitante,
       visitante.nombre AS nombreV
FROM
(
  SELECT @n := @n+1 AS pos,nombre,id
  FROM equipo
  WHERE liga=".$idLiga."
  ORDER BY nombre ASC,ID ASC
) AS local,
     partido,juega,
(
  SELECT @m := @m+1 AS pos,nombre,id
  FROM equipo
  WHERE liga=".$idLiga."
  ORDER BY nombre ASC,ID ASC
) AS visitante
WHERE local.id=partido.local AND
      visitante.id=partido.visitante AND
      partido.liga=".$idLiga." AND 
      partido.id=juega.partido
ORDER BY local.nombre ASC,visitante.nombre ASC,
         local.ID ASC,visitante ASC
";
// Ejecutar la sentencia
mysqli_query($conn,$sqln);
mysqli_query($conn,$sqlm);
$rsP = mysqli_query($conn,$sqlPartidos);

$fila = 0;
if (mysqli_num_rows($rs) > 0) {
  mysqli_data_seek($rs,0);
}
$numEquipos  = mysqli_num_rows($rs);
$numPartidos = mysqli_num_rows($rsP);
$masPartidos = true;
$ordPartido  = 0;
while($equipo = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada equipo de la liga
  ($fila % 2 == 0) ? ($paridad="evenRes") : ($paridad="oddRes");
  echo "
        <tr align=\"center\">
          <td class=\"".$paridad."\" align=\"right\">".$equipo['nombre']."</td>
  ";
  if (!$masPartidos) {
    completarFila(0,$numEquipos,$fila);
    echo "
        </tr>
    ";
    $fila++;
    continue;
  }
  $col = 0;
  // Analizar el siguiente partido
  while (($partido = mysqli_fetch_array($rsP)) &&
	 ($partido['idL'] == $equipo['ID'])){
    completarFila($col,$partido['posV'],$fila);
    $col = $partido['posV']+1;
    $ordPartido++;
    echo "
          <td class=\"evenRes\">".
            $partido['tantos_local']."-".$partido['tantos_visitante']."</td>
    ";
  }
  if ($ordPartido == $numPartidos) {
    $masPartidos = false;
  }
  else {
    // Dejar el array apuntando al mismo sitio que antes de hacer mysqli_fetch_array
    mysqli_data_seek($rsP,$ordPartido);
  }
  // Completar la fila
  completarFila($col,$numEquipos,$fila);
  echo "
        </tr>
  ";
  $fila++;
}

echo "
    </table>
    <br />
    </div>
    </div>
    </div>
  </td>
";

mysqli_free_result($rs);
mysqli_close($conn);
final_pagina();

?>