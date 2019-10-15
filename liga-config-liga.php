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

//$_SESSION['idLiga'] = 32; // DEBUG!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "configLiga") {
    $usuario        = $_SESSION['usuario_id'];
    $nombre         = $_POST['nombre'];

    if (strlen($_POST['disciplinaPer']) != 0)
      $disciplina = $_POST['disciplinaPer'];
    else
      $disciplina = $_POST['disciplina'];

    if (strlen($_POST['nombreTantosPer']) != 0)
      $nombreTantos = $_POST['nombreTantosPer'];
    else
      $nombreTantos = $_POST['nombreTantos'];
    $ptosVictoria   = $_POST['ptosVictoria'];
    $ptosEmpate     = $_POST['ptosEmpate'];
    $ptosDerrota    = $_POST['ptosDerrota'];
    
    // Conectar con la base de datos
    $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysql_select_db("$sql_db",$conn); 
    // Discernir si se trata de una nueva liga o de una ya existente
    if (isset($_SESSION['idLiga'])) {
      // Sentencia SQL para actualizar la información de la liga
      $ssql = "UPDATE liga SET
               usuario='".$usuario."',
               nombre='".$nombre."',
               metodo_puntuacion='".$nombreTantos."',
               ptos_victoria='".$ptosVictoria."',
               ptos_empate='".$ptosEmpate."',
               ptos_derrota='".$ptosDerrota."'
               WHERE ID='".$_SESSION['idLiga']."'";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
    }
    else {
      // Sentencia SQL para crear la liga
      $ssql = "INSERT INTO liga (usuario,nombre,metodo_puntuacion,ptos_victoria,ptos_empate,ptos_derrota)
               VALUES (
                       '".$usuario."',
                       '".$nombre."',
                       '".$nombreTantos."',
                       '".$ptosVictoria."',
                       '".$ptosEmpate."',
                       '".$ptosDerrota."'
                      )";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
      // Le damos un mobre a la sesion.
      session_name($usuarios_sesion);
      // Iniciar la sesion
      session_start();
      // Establecer la variable de sesión con el ID de la liga recién creada
      $_SESSION['idLiga'] = mysql_insert_id($conn);
      if ($_SESSION['idLiga'] == 0) {
	unset($_SESSION['idLiga']);
      }
    }
    // Sentencia SQL para borrar categorías candidatas previas de la liga
    $ssql = "DELETE FROM categoria_candidata
             WHERE liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysql_query($ssql,$conn);
    if (strlen($_POST['disciplinaPer']) != 0) {
      // Si se ha especificado una disciplina personalizada, se debe insertar en
      // la tabla categoria_candidata
      // Sentencia SQL para crear la categoría candidata
      $ssql = "INSERT INTO categoria_candidata (nombre,liga)
               VALUES (
                       '".$disciplina."',
                       '".$_SESSION['idLiga']."'
                      )";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
      // También hay que establece a NULL la categoría de la liga
      $ssql = "UPDATE liga SET deporte=NULL
               WHERE ID='".$_SESSION['idLiga']."'";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
    }
    else {
      // Sentencia SQL para obtener el id numérico de la categoría
      $ssql = "SELECT ID FROM categoria WHERE deporte = '".$disciplina."'";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
      $id_categoria = mysql_fetch_array($rs);
      mysql_free_result($rs);
      // Sentencia SQL para actualizar la categoria de la liga configurada
      $ssql = "UPDATE liga SET deporte='".$id_categoria['ID']."'
               WHERE ID='".$_SESSION['idLiga']."'";
      // Ejecutar la sentencia
      $rs = mysql_query($ssql,$conn);
    }	

    mysql_close();
    // Volver a presentar la página
    header("Location: liga-config-liga.php");
  }
  else if ($_GET['action'] == "nuevaLiga") {
    unset($_SESSION['idLiga']);
  }
  else if ($_GET['action'] == "edit") {
    $_SESSION['idLiga'] = $_GET['id'];
    header("Location: liga-config-liga.php");
  }
}

// Si se está modificando una liga, se carga el formulario con sus parámetros
if (isset($_SESSION['idLiga']))
{
  // Obtener la configuración de la liga
  
  // Conectar con la base de datos
  $conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysql_select_db("$sql_db",$conn); 
  
  // Sentencia SQL para obtener los datos de configuración de la liga
  $ssql = "SELECT * FROM liga WHERE ID='".$_SESSION['idLiga']."'";
  
  // Ejecutar la sentencia
  $rs = mysql_query($ssql,$conn);
  
  $configLiga = mysql_fetch_array($rs);
  // Liberar el la memoria de los datos de configuración de la liga
  mysql_free_result($rs);

  // Obtener el nombre personalizado de la categoria de la liga (si lo tuviese)
  // Sentencia SQL para obtener el nombre personalizado de la categoria de la liga
  $ssql = "SELECT nombre FROM categoria_candidata WHERE liga='".$_SESSION['idLiga']."'";
  // Ejecutar la sentencia
  $rs = mysql_query($ssql,$conn);
  if (mysql_num_rows($rs) == 0) {
    // La liga no tiene una categoría temporal, sino que tiene una categoría existente
    // Buscar el nombre de la categoría
    $ssql = "SELECT categoria.deporte as nombre FROM categoria,liga
             WHERE liga.ID='".$_SESSION['idLiga']."'
             AND   liga.deporte=categoria.ID";
    // Ejecutar la sentencia
    $rs1 = mysql_query($ssql,$conn);
    $nombreCategoriaTmp = mysql_fetch_array($rs1);
    mysql_free_result($rs1);
    // El nombre de la categoría temporal se pone a vacío
    $nombreCategoriaPer = "";
  }
  else {
    // El nombre de la categoría aún es temporal
    $nombreCategoriaTmp = mysql_fetch_array($rs);
    $nombreCategoriaPer = $nombreCategoriaTmp['nombre'];
  }
  $nombreCategoria = $nombreCategoriaTmp['nombre'];
  mysql_free_result($rs);
  mysql_close();
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1><a class=\"pagetitle\" href=\"liga-user-ligas.php\">Mis ligas</a></h1><br />
    <h2>Configurar una liga</h2>
    <br/>
";

// Presentar el formulario de creación de una liga nueva
echo "
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li id=\"active\"><a href=\"liga-config-liga.php\" id=\"current\">Configuraci&oacute;n</a></li>
";
if (isset($_SESSION['idLiga']))
{
  echo "
        <li><a href=\"liga-config-equipos.php\">Equipos</a></li>
        <li><a href=\"liga-config-jornadas.php\">Jornadas</a></li>
  ";
}
echo "
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>

    <div class=\"cbox\">
      <div class=\"cbox-title\">Par&aacute;metros generales</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">
        <form action=\"liga-config-liga.php?action=configLiga\" method=\"post\" name=\"config_liga\">
          <table class=\"admin\">
            <tr>
              <td class=\"form\">Nombre de la liga <font color=\"red\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"nombre\" size=\"60\" maxlength=\"255\"
                 onChange=\"quitarBlancos(this);\" value=\"".$configLiga['nombre']."\"/>
              </td>
            </tr>
            <tr>
              <td class=\"form\">Disciplina deportiva <font color=\"#00008b\">*</font></td>
              <td class=\"form\">
                <select name=\"disciplina\">
";
// Obtener el listado de categorías

// Conectar con la base de datos
$conn = mysql_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysql_select_db("$sql_db",$conn); 

// Sentencia SQL para obtener el listado de categorias deportivas en el sistema
$ssql = "SELECT deporte FROM categoria ORDER BY deporte";

// Ejecutar la sentencia
$rs = mysql_query($ssql,$conn);

while($categorias = mysql_fetch_array($rs)) {
  if ($categorias['deporte'] == $nombreCategoria)
    $select = "selected=\"selected\"";
  else
    $select = "";
  echo "            <option ".$select." value=\"".$categorias['deporte']."\" >".$categorias['deporte']."</option>
  ";
  $indice++;
}
mysql_free_result($rs);
mysql_close();

echo "
              </select>
              &nbsp;&nbsp;Personalizado&nbsp;
              <input type=\"text\" name=\"disciplinaPer\" size=\"30\" maxlength=\"255\"
               onChange=\"quitarBlancos(this);\" value=\"".$nombreCategoriaPer."\"/>
              </td>
            </tr>
            <tr>
              <td class=\"form\">Nombre que reciben los tantos (en plural) <font color=\"#00008b\">*</font></td>
              <td class=\"form\">
                <select name=\"nombreTantos\">
                  <option value=\"Goles\" >Goles</option>
                  <option value=\"Puntos\" >Puntos</option>
                  <option value=\"Ssets\" >Sets</option>
                  <option value=\"Carreras\" >Carreras</option>
                </select>
                &nbsp;&nbsp;Personalizado&nbsp;
                <input type=\"text\" name=\"nombreTantosPer\" size=\"30\" maxlength=\"255\"
                 onChange=\"quitarBlancos(this);\" value=\"".$configLiga['metodo_puntuacion']."\"/>
              </td>
            </tr>
            <tr>
              <td class=\"form\">Puntos por ganar <font color=\"red\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"ptosVictoria\" size=\"10\" maxlength=\"3\"
                 onChange=\"quitarBlancos(this);\" value=\"".$configLiga['ptos_victoria']."\"/>
              </td>
            </tr>
            <tr>
              <td class=\"form\">Puntos por empatar <font color=\"red\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"ptosEmpate\" size=\"10\" maxlength=\"3\"
                 onChange=\"quitarBlancos(this);\" value=\"".$configLiga['ptos_empate']."\"/>
              </td>
            </tr>
            <tr>
              <td class=\"form\">Puntos por perder <font color=\"red\">*</font></td>
              <td class=\"form\">
                <input type=\"text\" name=\"ptosDerrota\" size=\"10\" maxlength=\"3\"
                 onChange=\"quitarBlancos(this);\" value=\"".$configLiga['ptos_derrota']."\"/>
              </td>
            </tr>
            <tr>
              <td colspan=\"2\" class=\"button\">
                <input type=\"button\" name=\"messprefs\" value=\"Guardar cambios\" onClick=\"checkFormConfigLiga();\" />
              </td>
            </tr>
          </table>
        </form>
      </div>
      </div>
    </div>
    <h4>Los campos marcados con <font color=\"red\">*</font> son obligatorios</h4>
    <h4>Los campos marcados con <font color=\"#00008b\">*</font> son obligatorios. Se puede seleccionar
        un elemento de la lista desplegable o bien introducir un valor personalizado en el cuadro de texto
        adyacente. Si el cuadro de texto se deja en blanco, se tomar&aacute; el valor seleccionado en
        la lista desplegable</h4>

    </div>
    </div>
  </td>
  ";


final_pagina();

?>