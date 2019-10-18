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

cabecera();
comienzo_tabla_principal();
columna_izquierda();

if (isset($_GET['error'])) {
  $texto_error = $_GET['error'];
}

echo "
      <td id=\"centercolumn\">

        <div id=\"tiki-center\">
        <h2>
          ERROR
        </h2>
        <table class=\"wikitopline\">
          <tr>
            <td>
              <small></small>
            </td>
          </tr>
        </table>

        <div class=\"wikitext\">
        <div align=\"center\">
        <div class='titlebar'>Error
        </div>
        </div>".$texto_error."<br/>
        <br />
        <a href=\"javascript:history.back()\" class=\"linkbody\">Atr&aacute;s</a><br /><br />
        <a href=\"liga-index.php\" class=\"linkbody\">Volver al inicio</a>   
        </div>
        </div>
      </td>
";

final_pagina();

?>