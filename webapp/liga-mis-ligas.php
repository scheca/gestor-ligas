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
require_once('lib/liga-col-central.php');
require_once('lib/liga-final.php');

require_once('config/bd_config.inc.php');
// Iniciamos el uso de sesiones
session_start();

$nivel_acceso = 100; // Definir nivel de acceso para esta p�gina.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+permisos+suficientes.+Acceso+denegado.");
  exit;
}

// Comprobar si se ha iniciado sesion
if (!isset($_SESSION['usuario_login']) || !isset($_SESSION['usuario_password'])) {
  header ("Location: liga-error.php?error=Acceso+denegado+a+la+p�gina+personal+de+ligas.");
  exit;
}
else {
  if ($_SESSION['usuario_login'] == "admin") {
    //Si es el administrador, se le redirige a la p�gina principal
    header ("Location: liga-index.php");
    exit;
  }
  $idUsuario = $_SESSION['usuario_id'];
}

// Comprobar si se ha especificado un orden
$order = "nombre";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "borrar") {
    $id_a_borrar   = $_GET['id'];
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM juega WHERE liga=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM partido WHERE liga=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM jornada WHERE liga=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM equipo WHERE liga=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM categoria_candidata WHERE liga=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la liga
    $ssql = "DELETE FROM liga WHERE id=".$id_a_borrar;
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    mysqli_close($conn);
    // Volver a presentar la p�gina
    header("Location: liga-mis-ligas.php?order=".$order);
  }
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Mis ligas</h1>
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
         SELECT liga.id,liga.nombre,categoria.deporte
         FROM liga,categoria
         WHERE liga.deporte=categoria.id AND liga.usuario=".$idUsuario."
         )
         UNION
         (
         SELECT liga.id,liga.nombre,liga.deporte
         FROM liga
         WHERE liga.deporte IS NULL AND liga.usuario=".$idUsuario."
         )
         ORDER BY ".$order.",deporte,nombre ASC
        ";

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

echo "
    <div align=\"center\">
      Para ver los detalles de una liga (clasificaci&oacute;n, resultados, etc.), 
      se debe pinchar sobre el nombre de la misma.<br/>&nbsp;<br/>
      <table class=\"estrecha\">
        <tr>
          <td class=\"heading\" ></td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-mis-ligas.php?order=nombre\">Nombre</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-mis-ligas.php?order=deporte\">Deporte</a>
          </td>
        </tr>
";

$indice = 0;
while($liga = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada liga
  ($indice % 2 == 0) ? ($paridad="evenJornada") : ($paridad="oddJornada");
  if ($liga['deporte'] == NULL) {
    $nom_deporte = "<i>Sin especificar</i>";
  }
  else {
    $nom_deporte = $liga['deporte'];
  }
  $link_borrado = "liga-mis-ligas.php?order=".$order."&action=borrar&id=".$liga['id'];
  echo "
        <tr>
          <td class=\"".$paridad."\"><div align=\"center\">
	    <a class=\"link\" href=\"liga-config-liga.php?action=edit&id=".$liga['id']."\">
              <img src='img/icons/edit.gif' border='0' alt='editar' title='editar' />
            </a>
            <a class=\"link\" name=\"equipo".$liga['ID']."\" 
               href=\"#\"
               onClick='confirmationDelLiga(\"".$link_borrado."\")'>
              <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
            </a>
          </td>
          <td class=\"".$paridad."\" >
            <a class=\"linkCal\" href=\"liga-clasificacion.php?idLiga=".$liga['id']."\">".$liga['nombre']."</a>
          </td>
          <td class=\"".$paridad."\" >".$nom_deporte."</td>
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