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
  $ssql = "SELECT * FROM liga WHERE ID='".$idLiga."'";

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

// Obtener la clasificaci�n

// Conectar con la base de datos
$conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
// Seleccionar la BBDD
mysqli_select_db($conn,"$sql_db");

// Sentencia SQL para obtener la clasificaci�n
$ssql = "
SELECT *
FROM
    (
            SELECT total.nombre,total.id,
                SUM(total.jugados) PJ_L,
                SUM(total.victorias) PG_L,
                SUM(total.empates) PE_L,
                SUM(total.derrotas) PP_L,
                SUM(total.GF) TF_L,
                SUM(total.GC) TC_L,
                SUM(total.GF)-SUM(total.GC) DIF_L,
                SUM(total.puntos) puntos_L
            FROM
            (
                (
                    SELECT equipo.nombre,equipo.id,
                        COUNT(partido.id) Jugados,
                        SUM(if (tantos_local>tantos_visitante,1,0)) victorias,
                        SUM(if (tantos_local=tantos_visitante,1,0)) empates,
                        SUM(if (tantos_local<tantos_visitante,1,0)) derrotas,
                        SUM(tantos_local) GF,
                        SUM(tantos_visitante) GC,
                        SUM(tantos_local)-SUM(tantos_visitante) DIF,
                        (SUM(if (tantos_local>tantos_visitante,".$datosLiga['ptos_victoria'].",0))+
                        SUM(if (tantos_local=tantos_visitante,".$datosLiga['ptos_empate'].",0))+
                        SUM(if (tantos_local<tantos_visitante,".$datosLiga['ptos_derrota'].",0))) puntos
                    FROM equipo,partido,juega
                    WHERE equipo.id=partido.local AND equipo.liga=".$idLiga." AND partido.id=juega.partido
                    GROUP BY equipo.id
                )
                UNION
                (
                    SELECT nombre,equipo.id,
                        0 PJ,
                        0 PG,
                        0 PE,
                        0 PP,
                        0 GF,
                        0 GC,
                        0 DIF,
                        0 puntos
                    FROM equipo
                    WHERE equipo.liga=".$idLiga." AND
                    (
                        equipo.id NOT IN
                            (SELECT local FROM partido,juega 
                             WHERE partido.liga=".$idLiga." AND partido.id=juega.partido GROUP BY local
                            )
                        OR
                        equipo.id NOT IN
                            (SELECT visitante FROM partido,juega
                             WHERE partido.liga=".$idLiga." AND partido.id=juega.partido GROUP BY visitante
                            )
                    )
                    ORDER BY nombre ASC
                )
            ) AS total
            GROUP BY total.id
    ) AS local

LEFT JOIN

    (
            SELECT total.id,
                SUM(total.jugados) PJ_V,
                SUM(total.victorias) PG_V,
                SUM(total.empates) PE_V,
                SUM(total.derrotas) PP_V,
                SUM(total.GF) TF_V,
                SUM(total.GC) TC_V,
                SUM(total.GF)-SUM(total.GC) DIF_V,
                SUM(total.puntos) puntos_V
            FROM
            (
                (
                    SELECT equipo.id,
                        COUNT(partido.id) Jugados,
                        SUM(if (tantos_local<tantos_visitante,1,0)) victorias,
                        SUM(if (tantos_local=tantos_visitante,1,0)) empates,
                        SUM(if (tantos_local>tantos_visitante,1,0)) derrotas,
                        SUM(tantos_visitante) GF,
                        SUM(tantos_local) GC,
                        SUM(tantos_visitante)-SUM(tantos_local) DIF,
                        (SUM(if (tantos_local<tantos_visitante,".$datosLiga['ptos_victoria'].",0))+
                        SUM(if (tantos_local=tantos_visitante,".$datosLiga['ptos_empate'].",0))+
                        SUM(if (tantos_local>tantos_visitante,".$datosLiga['ptos_derrota'].",0))) puntos
                    FROM equipo,partido,juega
                    WHERE equipo.id=partido.visitante AND equipo.liga=".$idLiga." AND partido.id=juega.partido
                    GROUP BY equipo.id
                )
                UNION
                (
                    SELECT equipo.id,
                        0 PJ,
                        0 PG,
                        0 PE,
                        0 PP,
                        0 GF,
                        0 GC,
                        0 DIF,
                        0 puntos
                    FROM equipo
                    WHERE equipo.liga=".$idLiga." AND
                    (
                        equipo.id NOT IN
                            (SELECT local FROM partido,juega 
                             WHERE partido.liga=".$idLiga." AND partido.id=juega.partido GROUP BY local
                            )
                        OR
                        equipo.id NOT IN
                            (SELECT visitante FROM partido,juega
                             WHERE partido.liga=".$idLiga." AND partido.id=juega.partido GROUP BY visitante
                            )
                    )
                )
            ) AS total
            GROUP BY total.id
    ) AS visitante

ON (local.id=visitante.id)
ORDER BY local.puntos_L + visitante.puntos_V DESC,
         local.DIF_L + visitante.DIF_V DESC,
         local.TF_L + visitante.TF_V DESC,
         local.nombre ASC
";

// Ejecutar la sentencia
$rs = mysqli_query($conn,$ssql);

cabecera();
comienzo_tabla_principal();
columna_izquierda();

// Obtener la inicial de los tantos de la liga
$inicialTantos = $datosLiga['metodo_puntuacion'][0];

echo "
  <td id=\"centercolumn\">
    <div id=\"tiki-center\">
    <h1>".$datosLiga['nombre']."</h1><br />
    <h2>Clasificaci&oacute;n</h2>
    <br />
    <div id=\"navcontainer\">
      <ul id=\"navlist\">
        <li id=\"active\"><a href=\"liga-clasificacion.php?idLiga=".$idLiga."\" id=\"current\">Clasificaci&oacute;n</a></li>
        <li><a href=\"liga-equipos.php?idLiga=".$idLiga."\">Equipos</a></li>
        <li><a href=\"liga-calendario.php?idLiga=".$idLiga."\">Calendario</a></li>
        <li><a href=\"liga-resultados.php?idLiga=".$idLiga."\">Resultados</a></li>
      </ul>
    </div>

    <div class=\"wikitext\">
    <br/>
    <div align=\"center\">
      <table class=\"normal\" id=\"clasificacion\">
        <tr>
          <td colspan=\"2\" bgcolor=\"lightgrey\"></td>
          <td class=\"heading\" colspan=\"8\" align=\"center\">
            <a class=\"tableheading\" href=\"#\">TOTAL</a>
          </td>
          <td class=\"heading\" colspan=\"8\" align=\"center\">
            <a class=\"tableheading\" href=\"#\">LOCAL</a>
          </td>
          <td class=\"heading\" colspan=\"8\" align=\"center\">
            <a class=\"tableheading\" href=\"#\">VISITANTE</a>
          </td>
        </tr>
        <tr>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\"></a>
          </td>
          <td class=\"heading\" >
            <a class=\"tableheading\" href=\"#\">Equipo</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PTOS</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PJ</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PG</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PE</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PP</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."F</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."C</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">DIF</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PTOS</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PJ</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PG</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PE</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PP</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."F</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."C</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">DIF</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PTOS</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PJ</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PG</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PE</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">PP</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."F</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">".$inicialTantos."C</a>
          </td>
          <td class=\"heading\" align=\"right\">
            <a class=\"tableheading\" href=\"#\">DIF</a>
          </td>
        </tr>
";

$posicion = 1;
$indice = 0;
while($puesto = mysqli_fetch_array($rs)) {
  // Escribir fila a fila cada equipo en la tabla clasificatoria
  ($indice % 2 == 0) ? ($paridad="evenPuesto") : ($paridad="oddPuesto");
  echo "
        <tr>
          <td class=\"".$paridad."\" >".$posicion."</td>
          <td class=\"".(($indice % 2 == 0) ? ("evenPuestoNombre") : ("oddPuestoNombre"))."\" >".$puesto['nombre']."</td>
          <td class=\"".$paridad."\" >".($puesto['puntos_L']+$puesto['puntos_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['PJ_L']+$puesto['PJ_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['PG_L']+$puesto['PG_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['PE_L']+$puesto['PE_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['PP_L']+$puesto['PP_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['TF_L']+$puesto['TF_V'])."</td>
          <td class=\"".$paridad."\" >".($puesto['TC_L']+$puesto['TC_V'])."</td>
          <td class=\"".$paridad."\" >".sprintf("%+d",$puesto['DIF_L']+$puesto['DIF_V'])."</td>

          <td class=\"".$paridad."\" >".$puesto['puntos_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['PJ_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['PG_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['PE_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['PP_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['TF_L']."</td>
          <td class=\"".$paridad."\" >".$puesto['TC_L']."</td>
          <td class=\"".$paridad."\" >".sprintf("%+d",$puesto['DIF_L'])."</td>

          <td class=\"".$paridad."\" >".$puesto['puntos_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['PJ_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['PG_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['PE_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['PP_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['TF_V']."</td>
          <td class=\"".$paridad."\" >".$puesto['TC_V']."</td>
          <td class=\"".$paridad."\" >".sprintf("%+d",$puesto['DIF_V'])."</td>
        </tr>
  ";
  $posicion++;
  $indice++;
}

mysqli_free_result($rs);
mysqli_close($conn);

echo "
    </table>
    <br />
    PTOS: Puntos / PJ: Partidos jugados
    <br/>
    PG: Partidos ganados / PE: Partidos empatados / PP: Partidos perdidos
    <br/>
    GF: ".$datosLiga['metodo_puntuacion']." a favor / GC: ".$datosLiga['metodo_puntuacion']." en contra /
    DIF: Diferencia de ".$datosLiga['metodo_puntuacion']."
    </div>
    </div>
    </div>
  </td>
";


final_pagina();

?>