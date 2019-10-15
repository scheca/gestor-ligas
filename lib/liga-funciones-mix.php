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


function parseFecha ($fecha)
{
    $anho = substr($fecha,6,4);
    $mes  = substr($fecha,3,2);
    $dia  = substr($fecha,0,2);

    return $anho."-".$mes."-".$dia;
}

function parseFechaRev ($fecha)
{
    $anho = substr($fecha,0,4);
    $mes  = substr($fecha,5,2);
    $dia  = substr($fecha,8,2);

    return $dia."-".$mes."-".$anho;
}

function completarFila ($colI, $colF,$fila)
{
  for ($i=$colI;$i<$colF;$i++) {
    if ($i == $fila) {
      $paridad = "oddRes";
    }
    else {
      $paridad="evenRes";
    }
    echo "
          <td class=\"".$paridad."\"></td>
    ";
  }
}

function crear_archivo ($nom_pag, $content)
{   
   if ($desc = fopen($nom_pag,"w+"))
   {     
      fputs ($desc, $content);
      fclose ($desc);
      return true;
   }
   else
     return false;
}

?>