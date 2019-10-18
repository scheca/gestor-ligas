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
require_once('lib/liga-final.php');
require_once('config/bd_config.inc.php');

$nivel_acceso = 100; // Definir nivel de acceso para esta p�gina.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+permisos+suficientes.+Acceso+denegado.");
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
  if ($_GET['action'] == "nuevaCategoria") {
    $categoria  = $_POST['nombreDisciplina'];
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");
    // Discernir si se trata de una nueva liga o de una ya existente
    // Sentencia SQL para actualizar la informaci�n de la liga
    $ssql = "INSERT INTO categoria (deporte)
             VALUES ('".$categoria."')";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    mysqli_close($conn);
    // Volver a presentar la p�gina
    header("Location: liga-admin-categorias.php");
  }
  else if ($_GET['action'] == "borrar") {
    if (isset($_GET['id'])) {
      // Conectar con la base de datos
      $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
      // Seleccionar la BBDD
      mysqli_select_db($conn,"$sql_db");
      // Sentencia SQL para borrar la categor�a
      $ssql = "DELETE FROM categoria WHERE ID='".$_GET['id']."'";
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);
      mysqli_close($conn);
      // Volver a presentar la p�gina
      header("Location: liga-admin-categorias.php");
    }
  }
  else if ($_GET['action'] == "cambiarNombre") {
    if (isset($_GET['id'])) {
      // Conectar con la base de datos
      $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
      // Seleccionar la BBDD
      mysqli_select_db($conn,"$sql_db");
      // Sentencia SQL para actualizar el nombre de la categor�a
      $ssql = "UPDATE categoria SET deporte='".$_POST['nombreDisciplina']."' WHERE ID='".$_GET['id']."'";
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);
      mysqli_close($conn);
      // Volver a presentar la p�gina
      header("Location: liga-admin-categorias.php");
    }
  }
  else if ($_GET['action'] == "asignar") {
    $id_candidata    = $_POST['idCandidata'];
    $id_liga         = $_POST['idLiga'];
    $categoria_final = $_POST['disciplina'];

    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");
    // Sentencia SQL para borrar la categor�a candidata
    $ssql = "DELETE FROM categoria_candidata WHERE ID='".$id_candidata."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para obtener el ID de la categoria asignada
    $ssql = "SELECT ID FROM categoria WHERE deporte='".$categoria_final."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    // Comprobar que la categor�a existe
    if (mysqli_num_rows($rs) == 0) {
      // Volver a presentar la p�gina
      header("Location: liga-admin-categorias.php");
    }
    $id_categoria = mysqli_fetch_array($rs);
    mysqli_free_result($rs);

    // Sentencia SQL para actualizar la categoria de la liga
    $ssql = "UPDATE liga SET deporte='".$id_categoria['ID']."' WHERE ID='".$id_liga."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);


    mysqli_close($conn);
    // Volver a presentar la p�gina
    header("Location: liga-admin-categorias.php");
  }
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>Administraci&oacute;n</h1><br />
    <h2>Gesti&oacute;n de categor&iacute;as deportivas</h2>

    <div class=\"cbox\">
      <div class=\"cbox-title\">Categor&iacute;as disponibles</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">

      <div align=\"center\"><br />
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" ></td>
          <td class=\"heading\" >
            Nombre de la disciplina deportiva
          </td>
          <td class=\"heading\" ></td>
        </tr>
  ";

// Obtener el listado de categor�as
  
// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Sentencia SQL para obtener el listado de categor�as
$ssql = "SELECT ID,deporte FROM categoria ORDER BY deporte";

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

$indice = 0;
while($categoria = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada categor�a
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  echo "
      <tr>
        <td class=\"".$paridad."\">
          <a class=\"link\" href=\"liga-admin-categorias.php?action=borrar&id=".$categoria['ID']."\">
            <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
          </a>
        </td>
        <td class=\"".$paridad."\" >".$categoria['deporte']."</td>
        <td class=\"".$paridad."\" >
          <form action=\"liga-admin-categorias.php?action=cambiarNombre&id=".$categoria['ID']."\" method=\"post\" 
            name=\"form_asignar_categoria\" >
            <input type=\"text\" name=\"nombreDisciplina\" size=\"60\" maxlength=\"255\"
                   onChange=\"quitarBlancos(this); changeCase(this);\" value=\"".$categoria['deporte']."\" />
            &nbsp;
            <input type=\"submit\" name=\"asignar\" value=\"Cambiar nombre\" />
          </form>
        </td>
      </tr>
    ";
  $indice++;
}
mysqli_free_result($rs);
mysqli_close($conn);

echo "
      </table>
      </div>

      <br />

      <form action=\"liga-admin-categorias.php?action=nuevaCategoria\" method=\"post\" 
            name=\"form_nueva_categoria\">
        <table class=\"admin\">
          <tr>
            <td class=\"form\">Denominaci&oacute;n de la nueva categor&iacute;a <font color=\"red\">*</font></td>
            <td class=\"form\">
              <input type=\"text\" name=\"nombreDisciplina\" size=\"60\" maxlength=\"255\"
               onChange=\"quitarBlancos(this); changeCase(this);\" />
            </td>
          </tr>
          <tr>
            <td colspan=\"2\" class=\"button\">
              <input type=\"button\" name=\"nuevaCategoria\" value=\"A&ntilde;adir categor&iacute;a\" 
               onClick=\"checkFormNuevaCategoria();\" />
            </td>
          </tr>
        </table>
      </form>

      </div>
      </div>
    </div>
    <h4>Los campos marcados con <font color=\"red\">*</font> son obligatorios</h4>
    <br />
    <div class=\"cbox\">
      <div class=\"cbox-title\">Asignaci&oacute;n de categor&iacute;as a ligas a&uacute;n sin clasificar</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">
      <div align=\"center\"><br />
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-admin-categorias.php?order=nombre\">Nombre de la liga</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-admin-categorias.php?order=deporte\">Disciplina candidata</a>
          </td>
          <td class=\"heading\" >
            Disciplina a asignar
          </td>
        </tr>
  ";

// Obtener el listado de categor�as candidatas
  
// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");
  
// Sentencia SQL para obtener el listado de categor�as candidatas
$ssql = "SELECT liga.nombre,categoria_candidata.nombre as deporte,categoria_candidata.ID as id_candidata,
         liga.ID as id_liga
         FROM categoria_candidata,liga 
         WHERE liga.id = categoria_candidata.liga ORDER BY ".$order;
  
// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

// Sentencia SQL para obtener el listado de categor�as
$ssql = "SELECT ID,deporte FROM categoria ORDER BY deporte";
  
// Ejecutar la sentencia
$categorias = mysqli_query($conn,$ssql);
  
$indice = 0;
while($candidata = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada categor�a
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  echo "
      <tr>
        <td class=\"".$paridad."\" >".$candidata['nombre']."</td>
        <td class=\"".$paridad."\" >".$candidata['deporte']."</td>
        <td class=\"".$paridad."\" >
          <form action=\"liga-admin-categorias.php?action=asignar\" method=\"post\" 
            name=\"form_asignar_categoria\" >
            <input type=\"hidden\" name=\"idCandidata\" value=\"".$candidata['id_candidata']."\"
            <input type=\"hidden\" name=\"idLiga\" value=\"".$candidata['id_liga']."\"
          <select name=\"disciplina\">";
  while($categoria = mysqli_fetch_array($categorias)) {
    echo "
            <option value=\"".$categoria['deporte']."\" >".$categoria['deporte']."</option>
      ";
  }
  mysqli_data_seek($categorias, 0);
  echo"
          </select>&nbsp;
            <input type=\"submit\" name=\"asignar\" value=\"Asignar\" />
          </form>
        </td>
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
    </div>
    <br/>
    </div>
  </td>
";


final_pagina();

?>