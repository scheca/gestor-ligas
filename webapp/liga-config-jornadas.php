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
require_once('lib/liga-funciones-mix.php');


$nivel_acceso = 100; // Definir nivel de acceso para esta p�gina.
if ($_SESSION['usuario_nivel'] > $nivel_acceso){
  header ("Location: liga-error.php?error=No+dispone+de+permisos+suficientes.+Acceso+denegado.");
  exit;
}

// Comprobar si se ha especificado un orden
$order = "ID";
if (isset($_GET['order']))
{
  $order = $_GET['order'];
}

// Comprobar que se est� trabajando sobre una liga en particular
if (!isset($_SESSION['idLiga']))
{
  header ("Location: liga-error.php?error=Acceso+denegado+a+la+creaci�n+de+equipos.");
  exit;
}

// Tratar las acciones que no requieren escribir html
if (isset($_GET['action'])) {
  if ($_GET['action'] == "nuevoPartido") {
    $fecha        = $_POST['fechaPartido'];
    $hora         = $_POST['hora'].":".$_POST['minuto'];
    $idLocal      = $_POST['idLocal' ];
    $idVisitante  = $_POST['idVisitante'];
    $resLocal     = $_POST['resLocal'];
    $resVisitante = $_POST['resVisitante'];
    $campo        = $_POST['campo'];
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");
    // Sentencia SQL para incluir el nuevo partido
    $ssql = "INSERT INTO partido (local,visitante,fecha,hora,campo,jornada,liga)
             VALUES ('".$idLocal."','".$idVisitante."','".parseFecha($fecha)."','".$hora."','".$campo."','".
                     $_SESSION['idJornada']."','".$_SESSION['idLiga']."')";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    // Obtener el ID del partido reci�n creado
    $idPartido = mysqli_insert_id($conn);

    // Guardar el resultado (si es que se ha especificado)
    if (strlen($resLocal) > 0 && strlen($resVisitante) > 0) {
      $ssql = "INSERT INTO juega (tantos_local,tantos_visitante,partido,jornada,liga)
               VALUES ('".$resLocal."','".$resVisitante."','".$idPartido."','".
                       $_SESSION['idJornada']."','".$_SESSION['idLiga']."')";
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);
    }

    mysqli_close($conn);
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "borrarJornada") {
    $id_a_borrar   = $_GET['id'];
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para borrar todos los resultados de partidos de la jornada
    $ssql = "DELETE FROM juega WHERE jornada=".$id_a_borrar." AND liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar todos los partidos de la jornada
    $ssql = "DELETE FROM partido WHERE jornada=".$id_a_borrar." AND liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    // Sentencia SQL para borrar la jornada
    $ssql = "DELETE FROM jornada WHERE ID=".$id_a_borrar." AND liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    mysqli_close($conn);
    // Si se est� borrando la jornada actual, se debe destruir la variable de sesi�n
    if ($id_a_borrar == $_SESSION['idJornada']) {
      unset($_SESSION['idJornada']);
    }
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "nuevaJornada") {
    $idJornada    = $_POST['numeroJornada' ];
    $fechaJornada = $_POST['fechaJornada'];
    $idLiga       = $_SESSION['idLiga'];

    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");

    // Sentencia SQL para comprobar si ya existe una jornada con ese ID en esa liga
    $ssql = "SELECT ID FROM jornada WHERE ID='".$idJornada."' AND liga='".$_SESSION['idLiga']."'";

    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    if (mysqli_num_rows($rs) > 0){
      mysqli_free_result($rs);
      mysqli_close($conn);
      $_SESSION['error'] = "El n&uacute;mero de jornada ".$idJornada." ya est&aacute; siendo utilizado";
      // Volver a presentar la p�gina
      header("Location: liga-config-jornadas.php?order=".$order);
      die;
    }
    mysqli_free_result($rs);

    // Sentencia SQL para incluir la nueva jornada
    $ssql = "INSERT INTO jornada (ID,fecha,liga)
             VALUES ('".$idJornada."','".parseFecha($fechaJornada)."','".$idLiga."')";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    mysqli_close($conn);

    // Establecer la variable de sesi�n con el identificador de la jornada actual
    $_SESSION['idJornada'] = $idJornada;
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "editarPartidos") {
    $idJornada   = $_GET['id'];
    // Establecer una variable de sesi�n con el ID de la jornada
    $_SESSION['idJornada'] = $idJornada;
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "editarPartido") {
    $idPartido   = $_GET['id'];
    // Establecer una variable de sesi�n con el ID del partido
    $_SESSION['idPartido'] = $idPartido;
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "cambiarPartido") {
    $resLocal     = $_POST['resLocal'];
    $resVisitante = $_POST['resVisitante'];
    $idPartido    = $_SESSION['idPartido'];
    
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");
    
    $ssql = "DELETE FROM juega WHERE
             partido='".$_SESSION['idPartido']."' AND
             jornada='".$_SESSION['idJornada']."' AND
             liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    
    // Guardar el resultado (si es que se ha especificado)
    if ((strlen($resLocal) > 0) && (strlen($resVisitante) > 0)) {
      $ssql = "INSERT INTO juega (tantos_local,tantos_visitante,partido,jornada,liga)
               VALUES ('".$resLocal."','".$resVisitante."','".$idPartido."','".
                       $_SESSION['idJornada']."','".$_SESSION['idLiga']."')";
      // Ejecutar la sentencia
      $rs = mysqli_query($conn,$ssql);
    }
    mysqli_close($conn);

    // Borrar la variable de sesi�n que guarda el ID del partido a editar
    unset($_SESSION['idPartido']);
    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
  else if ($_GET['action'] == "borrarPartido") {
    $idPartido = $_GET['id'];
    // Conectar con la base de datos
    $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
    // Seleccionar la BBDD
    mysqli_select_db($conn,"$sql_db");
    
    $ssql = "DELETE FROM juega WHERE
             partido='".$idPartido."' AND
             jornada='".$_SESSION['idJornada']."' AND
             liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);

    $ssql = "DELETE FROM partido WHERE
             ID='".$idPartido."' AND
             jornada='".$_SESSION['idJornada']."' AND
             liga='".$_SESSION['idLiga']."'";
    // Ejecutar la sentencia
    $rs = mysqli_query($conn,$ssql);
    mysqli_close($conn);

    // Volver a presentar la p�gina
    header("Location: liga-config-jornadas.php?order=".$order);
  }
}

cabecera();
comienzo_tabla_principal();
columna_izquierda();

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1><a class=\"pagetitle\" href=\"liga-user-ligas.php\">Mis ligas</a></h1><br />
    <h2>Definir el calendario de liga y/o los resultados</h2>
    <br/>
";

// Presentar una tabla con las jornadas ya creadas en esta liga
echo "
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li><a href=\"liga-config-liga.php\" >Configuraci&oacute;n</a></li>
        <li><a href=\"liga-config-equipos.php\">Equipos</a></li>
        <li id=\"active\"><a href=\"liga-config-jornadas.php\" id=\"current\">Jornadas</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>

    <div class=\"cbox\">
      <div class=\"cbox-title\">Jornadas</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">
      <div align=\"center\"><br />
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" ></td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">N&uacute;mero de jornada</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php?order=fecha\">Fecha</a>
          </td>
        </tr>
";

// Obtener el listado de jornadas

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Sentencia SQL para obtener el listado de jornadas ya incluidas en la liga
$ssql = "SELECT ID,fecha FROM jornada WHERE liga='".$_SESSION['idLiga']."' ORDER BY ".$order;

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

$indice = 0;
while($jornada = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada jornada
  ($indice % 2 == 0) ? ($paridad="even") : ($paridad="odd");
  $link_borrado = "liga-config-jornadas.php?order=".$order."&action=borrarJornada&id=".$jornada['ID'];
  echo "
      <tr>
        <td class=\"".$paridad."\"><div align=\"center\">
	  <a class=\"link\" href=\"liga-config-jornadas.php?order=".$order."&action=editarJornada&id=".$jornada['ID']."\">
            <img src='img/icons/edit.gif' border='0' alt='editar' title='editar' />
          </a>
          <a class=\"link\" href=\"#\" onClick='confirmationDelJornada(\"".$link_borrado."\")'>
            <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
          </a>
          </div>
        </td>
        <td class=\"".$paridad."\" >
          <a class=\"linkJornada\" 
             href=\"liga-config-jornadas.php?order=".$order."&action=editarPartidos&id=".$jornada['ID']."\">
             Jornada ".$jornada['ID']."
          </a>
        </td>
        <td class=\"".$paridad."\" >".parseFechaRev($jornada['fecha'])."</td>
      </tr>
    ";
  $indice++;
}
mysqli_free_result($rs);
mysqli_close($conn);

echo "
      </table>
      <br />
      <form action=\"liga-config-jornadas.php?action=nuevaJornada&order=".$order."\" method=\"post\" 
            name=\"form_nueva_jornada\">
        <table class=\"admin\">
          <tr>
            <td class=\"form\">N&uacute;mero de jornada (no puede estar repetido) <font color=\"red\">*&nbsp;&nbsp;</font>
              <input type=\"text\" name=\"numeroJornada\" size=\"5\" maxlength=\"5\"
               onChange=\"quitarBlancos(this);\" />
            </td>
          </tr>
          <tr>
            <td class=\"form\">Fecha (los partidos de la jornada pueden disputarse en fechas distintas;
               &eacute;ste es un dato meramente indicativo) <font color=\"green\">*&nbsp;&nbsp;</font>
              <input type=\"text\" name=\"fechaJornada\" size=\"10\" onFocus=\"this.blur();\" />
              <a href=\"javascript:show_yearly_calendar('form_nueva_jornada.fechaJornada');\" 
                 onmouseover=\"window.status='Date Picker'; return true;\" onmouseout=\"window.status='';return true;\" 
                 class=\"linkmenu\">
                 <img src=\"img/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt='calendario' title='calendario' />
              </a>
            </td>
          </tr>
            <td>
            </td>
          <tr>
          </tr>
          <tr>
";
if (isset($_SESSION['error'])) {
  echo "
            <h3>".$_SESSION['error']."</h3>
  ";
  unset($_SESSION['error']);
}

// Guardar en una variable un texto con el n�mero de jornada
if (isset($_SESSION['idJornada'])) {
  $partidosJornada = " de la jornada ".$_SESSION['idJornada'];
}
else {
  $partidosJornada = "";
}

echo "
          </tr>
          <tr>
            <td class=\"button\">
              <input type=\"button\" value=\"Nueva jornada\"
                     onClick=\"checkFormNuevaJornada(form_nueva_jornada);\" />
            </td>
          </tr>
        </table>
      </form>

      </div>
      </div>
      </div>
    </div>
";
//$_SESSION['idJornada'] = 1; // DEBUG!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
if (isset($_SESSION['idJornada']))
{
  echo "
    <br />
    <div class=\"cbox\">
      <div class=\"cbox-title\">Partidos".$partidosJornada."</div>
      <div class=\"cbox-data\">
      <div class=\"simplebox\">
      <div align=\"center\"><br />
      <table class=\"normal\">
        <tr>
          <td class=\"heading\" ></td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Fecha</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Hora</a>
          </td>
          <td class=\"heading\" align=\"right\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Local</a>
          </td>
          <td class=\"heading\" align=\"center\">
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Resultado</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Visitante</a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"liga-config-jornadas.php\">Campo</a>
          </td>
        </tr>
  ";

  // Conectar con la base de datos
  $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysqli_select_db($conn,"$sql_db");
  
  // Sentencia SQL para obtener el listado de partidos ya incluidos en la jornada
  $ssql = "
(
     SELECT partido.id AS ID,local.nombre AS local,
            fecha,hora,juega.tantos_local,juega.tantos_visitante,visitante.nombre AS visitante,partido.campo
     FROM equipo AS local,partido,juega,equipo AS visitante
     WHERE local.id=partido.local AND
           visitante.id=partido.visitante AND
           partido.liga=".$_SESSION['idLiga']." AND 
           partido.jornada=".$_SESSION['idJornada']." AND 
           partido.id=juega.partido
)
UNION
(
     SELECT partido.id,local.nombre,
            fecha,hora,NULL tantos_local,NULL tantos_visitante,visitante.nombre,partido.campo
     FROM equipo AS local,partido,equipo AS visitante
     WHERE local.id=partido.local AND
           visitante.id=partido.visitante AND
           partido.liga=".$_SESSION['idLiga']." AND 
           partido.jornada=".$_SESSION['idJornada']." AND 
           partido.id NOT IN (select juega.partido from juega)
)     
ORDER BY ID
";
  
  // Ejecutar la sentencia
  $rs = mysqli_query($conn,$ssql);
  
  // Comprobar si se tiene que editar los datos de alg�n partido
  if (isset($_SESSION['idPartido']))
    $idPartidoEdicion = $_SESSION['idPartido'];
  else
    $idPartidoEdicion = -1;
  
  $indice = 0;
  while($partido = mysqli_fetch_array($rs)) {
    // Escribir fila a fila cada partido
    ($indice % 2 == 0) ? ($paridad="evenPartido") : ($paridad="oddPartido");
    echo "
      <tr>
    ";
    if ($partido['ID'] == $idPartidoEdicion) {
      echo "
        <form action=\"liga-config-jornadas.php?action=cambiarPartido&order=".$order."\" method=\"post\" 
            name=\"form_cambiar_partido\">
          <td class=\"".$paridad."\"><div align=\"center\">
            <input type=\"button\" name=\"modificarPartido\" value=\"Guardar cambios\" 
             onClick=\"checkFormResultado(form_cambiar_partido);\" />
      ";
    }
    else {
      echo "
        <td class=\"".$paridad."\"><div align=\"center\">
          <a class=\"link\"
            href=\"liga-config-jornadas.php?order=".$order."&action=editarPartido&id=".$partido['ID']."\" >
            <img src='img/icons/edit.gif' border='0' alt='editar partido' title='editar' />
          </a>
          <a class=\"link\"
            href=\"liga-config-jornadas.php?order=".$order."&action=borrarPartido&id=".$partido['ID']."\" >
            <img src='img/icons2/delete.gif' border='0' alt='eliminar' title='eliminar' />
          </a>
      ";
    }
    echo "
          </div>
        </td>
        <td class=\"".$paridad."\" >".parseFechaRev($partido['fecha'])."</td>
        <td class=\"".$paridad."\" >".$partido['hora']."</td>
        <td class=\"".$paridad."\"><div align=\"right\">".$partido['local']."</div></td>
        <td class=\"".$paridad."\"><div align=\"center\">
    ";
    if ($partido['ID'] == $idPartidoEdicion) {
      echo "
          <input type=\"text\" name=\"resLocal\" size=\"4\" maxlength=\"3\" align=\"right\" 
                 value=\"".$partido['tantos_local']."\" />
          &nbsp;-&nbsp;
          <input type=\"text\" name=\"resVisitante\" size=\"4\" maxlength=\"3\" align=\"left\" 
                 value=\"".$partido['tantos_visitante']."\" />
          </div>
        </td>
      </form>
      ";
    }
    else {
      echo $partido['tantos_local']."&nbsp;-&nbsp;".$partido['tantos_visitante']."</div></td>";
    }
    echo "
        <td class=\"".$paridad."\" align=\"left\">".$partido['visitante']."</td>
        <td class=\"".$paridad."\">".$partido['campo']."</td>
      </tr>
      ";
    $indice++;
  }
  mysqli_free_result($rs);
  mysqli_close($conn);
  
  echo "
      <tr>
        <form action=\"liga-config-jornadas.php?action=nuevoPartido&order=".$order."\" method=\"post\" 
            name=\"form_nuevo_partido\">
          <td align=\"center\">
            <input type=\"button\" name=\"nuevoPartido\" value=\"A&ntilde;adir partido\" 
             onClick=\"checkFormNuevoPartido(form_nuevo_partido);\" />
          </td>
          <td>
            <input type=\"text\" name=\"fechaPartido\" size=\"10\" onFocus=\"this.blur();\" />
            <a href=\"javascript:show_yearly_calendar('form_nuevo_partido.fechaPartido');\" 
               onmouseover=\"window.status='Date Picker'; return true;\" onmouseout=\"window.status='';return true;\" 
               class=\"linkmenu\">
               <img src=\"img/cal.gif\" width=\"16\" height=\"16\" border=\"0\" alt='calendario' title='calendario'/>
            </a>
          </td>
          <td>
            <select name=\"hora\" size=\"1\">
  ";
  for ($i=0;$i<=23;$i++) {
    echo "
              <option>".sprintf("%02d",$i)."</option>
    ";
  }
  echo "
	    </select>
            :
            <select name=\"minuto\" size=\"1\">
  ";
  for ($i=0;$i<60;$i=$i+5) {
    echo "
              <option>".sprintf("%02d",$i)."</option>
    ";
  }
  echo "
	    </select>
          </td>
  ";

  // Obtener el listado de equipos
  
  // Conectar con la base de datos
  $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysqli_select_db($conn,"$sql_db");
  
  // Sentencia SQL para obtener el listado de equipos que participan en la liga
  $ssql = "SELECT ID,nombre,campo FROM equipo WHERE liga='".$_SESSION['idLiga']."' ORDER BY nombre ASC,ID ASC";
  
  // Ejecutar la sentencia
  $rs = mysqli_query($conn,$ssql);
  
  echo "
          <td align=\"right\">
            <select name=\"locales\" size=\"1\" onChange=\"changecontent(this);\">

            <script language=\"JavaScript\" type=\"text/javascript\">
              var thecontents = new Array()
              var idEquipo = new Array()
  ";
  $k = 0;
  while($equipo = mysqli_fetch_array($rs)) {
    echo "
              thecontents[".$k."]='".$equipo['campo']."'
              idEquipo[".$k."]='".$equipo['ID']."'
    ";
    $k++;
  }
  echo "
              function changecontent(which){
                document.form_nuevo_partido.campo.value = thecontents[which.selectedIndex]
                document.form_nuevo_partido.idLocal.value = idEquipo[which.selectedIndex]
              }
              function changeIdVisitante(which){
                document.form_nuevo_partido.idVisitante.value = idEquipo[which.selectedIndex]
              }

            </script>
  ";

  $rows = mysqli_num_rows($rs);
  if ($rows>0)
    mysqli_data_seek($rs, 0);

  while($equipo = mysqli_fetch_array($rs)) {
    // Escribir fila a fila cada equipo (LOCAL)
    echo "
              <option>".$equipo['nombre']."</option>
    ";
  }
  echo "
	    </select>

          </td>
          <td align=\"center\">
            <input type=\"hidden\" name=\"idLocal\" />
            <input type=\"hidden\" name=\"idVisitante\" />
            <input type=\"text\" name=\"resLocal\" size=\"4\" maxlength=\"3\" align=\"right\" />
            &nbsp;-&nbsp;
            <input type=\"text\" name=\"resVisitante\" size=\"4\" maxlength=\"3\" align=\"left\" />
          </td>
          <td align=\"left\">
            <select name=\"visitantes\" size=\"1\" onChange=\"changeIdVisitante(this);\">
  ";
  $rows = mysqli_num_rows($rs);
  if($rows>0)
    mysqli_data_seek($rs, 0);
  
  while($equipo = mysqli_fetch_array($rs)) {
    // Escribir fila a fila cada equipo (VISITANTE)
    echo "
              <option>".$equipo['nombre']."</option>
    ";
  }
  echo "
            </select>
          </td>
          <td>
            <input type=\"text\" name=\"campo\" maxlength=\"60\" />
            <script language=\"JavaScript\" type=\"text/javascript\">
              document.form_nuevo_partido.campo.value=thecontents[document.form_nuevo_partido.locales.selectedIndex];
              document.form_nuevo_partido.idLocal.value = idEquipo[document.form_nuevo_partido.locales.selectedIndex];
              document.form_nuevo_partido.idVisitante.value = idEquipo[document.form_nuevo_partido.visitantes.selectedIndex];
            </script>
          </td>
        </form>
      </tr>
  ";

  // Liberar los datos de la lista de equipos
  mysqli_free_result($rs);
  mysqli_close($conn);

  echo "
      </table>
      <br />
      </div>
      </div>
      </div>
    </div>
  ";
}
echo "
    <h4>&nbsp;Los campos marcados con <font color=\"red\">*</font> son obligatorios</h4>
    <h4>&nbsp;Los campos marcados con <font color=\"green\">*</font> 
        son opcionales, pero se recomienda su cumplimentaci&oacute;n</h4>

    </div>
    </div>
  </td>
  ";


final_pagina();

?>