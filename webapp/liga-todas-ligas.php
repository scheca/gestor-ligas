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
require_once('lib/liga-col-central.php');
require_once('lib/liga-final.php');

require_once('config/bd_config.inc.php');
// Usamos la sesion de nombre definido.
session_name($usuarios_sesion);
// Iniciamos el uso de sesiones
session_start();

// Comprobar si se ha especificado un orden
$order = "nombre";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Todas las ligas</h1>
    <br />

    <div class=\"wikitext\">
      <br/>
";

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Sentencia SQL para obtener todas las ligas presentes en el sistema
$ssql = "(
         SELECT usuario.login,liga.id,liga.nombre,categoria.deporte
         FROM usuario,liga,categoria
         WHERE liga.deporte=categoria.id AND usuario.id=liga.usuario
         )
         UNION
         (
         SELECT usuario.login,liga.id,liga.nombre,liga.deporte
         FROM usuario,liga
         WHERE usuario.id=liga.usuario AND liga.deporte IS NULL
         )
         ORDER BY ".$order.",deporte,nombre,login ASC
        ";

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

echo "
    <div align=\"center\">
      Para ver los detalles de una liga (clasificaci&oacute;n, resultados, etc.), 
      se debe pinchar sobre el nombre de la misma.<br/>&nbsp;<br/>
      <table class=\"estrecha\">
        <tr>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-todas-ligas.php?order=nombre\">Nombre</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-todas-ligas.php?order=deporte\">Deporte</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-todas-ligas.php?order=login\">Usuario</a>
          </td>
        </tr>
";

$indice = 0;
while($liga = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada liga
  ($indice % 2 == 0) ? ($paridad="evenPartido") : ($paridad="oddPartido");
  if ($liga['deporte'] == NULL) {
    $nom_deporte = "<i>Sin especificar</i>";
  }
  else {
    $nom_deporte = $liga['deporte'];
  } 
  echo "
        <tr>
          <td class=\"".$paridad."\" >
            <a class=\"linkCal\" href=\"liga-clasificacion.php?idLiga=".$liga['id']."\">".$liga['nombre']."</a>
          </td>
          <td class=\"".$paridad."\" >".$nom_deporte."</td>
          <td class=\"".$paridad."\" >".$liga['login']."</td>
        </tr>
  ";
  $indice++;
}

mysqli_free_result($rs);
mysqli_close($conn);

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