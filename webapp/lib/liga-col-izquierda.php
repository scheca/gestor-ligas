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


function columna_izquierda()
{
  include('config/bd_config.inc.php');

  echo "
      <td id=\"leftcolumn\">
        
        <div class=\"box\">
        <div class=\"box-title\">
          Inicio r&aacute;pido
        </div>
        <div class=\"box-data\">
    
        <div id=\"Navegaci&oacute;n\" style=\"display:block;\">
        <div>&nbsp;<a href=\"liga-index.php\" class=\"linkmenu\">P&aacute;gina inicial</a></div>
        <div>&nbsp;<a href=\"liga-registro.php\" class=\"linkmenu\">Registrarse</a></div>


        <script language='Javascript' type='text/javascript'></script>

        </div>
        </div>
        </div>
            
        <div class=\"box\">
        <div class=\"box-title\">
          Busca tu liga
        </div>
        <div class=\"box-data\">
        <form name=\"loginbox\" action=\"liga-busqueda.php\" method=\"post\" >
          <table border=\"0\">
            <tr>
              <td>
                <table>
                  <tr>
                    <td><input type=\"text\" name=\"texto-busqueda\" id=\"texto_busqueda\" size=\"20\" />
                    </td>
                  </tr>
                  <tr>
                    <td align=\"center\"><input type=\"submit\" name=\"buscar\" value=\"Buscar\" />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </form>
        </div>
        </div>

        <div class=\"box\">
        <div class=\"box-title\">
          Login
        </div>
        <div class=\"box-data\">
  ";

  // Comprobar si se ha iniciado sesion
  if (isset($_SESSION['usuario_login']) && isset($_SESSION['usuario_password'])) {
    echo "
      <div>&nbsp;logueado como: ".$_SESSION['usuario_login']."</div><br />
      <form name=\"logout\" action=\"liga-logout.php\" method=\"post\">
        <div>&nbsp;<input type=\"submit\" name=\"login\" value=\"Cerrar sesi&oacute;n\" /></div>
      </form>
";
  }
  else {
    echo "
        <form name=\"loginbox\" action=\"liga-mis-ligas.php\" method=\"post\" > 
          <table border=\"0\">
            <tr>
              <td>
                <table>
                  <tr>
                    <td class=\"module\"><label for=\"login-user\">usuario:</label>
                    </td>
                  </tr>
                  <tr>
                    <td><input type=\"text\" name=\"user\" id=\"login-user\" size=\"20\" />
                    </td>
                  </tr>
                  <tr>
                    <td class=\"module\"><label for=\"login-pass\">contrase&ntilde;a:</label>
                    </td>
                  </tr>
                  <tr>
                    <td><input type=\"password\" name=\"pass\" id=\"login-pass\" size=\"20\" />
                    </td>
                  </tr>
                  <tr>
                    <td align=\"center\"><input type=\"submit\" name=\"login\" value=\"Entrar\" />
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </form>
    ";
  }
  echo "
        </div>
        </div>
            
        <div class=\"box\">
        <div class=\"box-title\">
          Todas las ligas
        </div>
        <div class=\"box-data\">
    
        <div id=\"Navegaci&oacute;n\" style=\"display:block;\">
        <div>&nbsp;<a href=\"liga-todas-ligas.php\" class=\"linkmenu\">Todas las ligas</a></div>
  ";
  $ssql = "SELECT liga.id,liga.nombre,liga.deporte AS cod_deporte,categoria.deporte
           FROM liga,categoria
           WHERE liga.deporte=categoria.id
           ORDER BY liga.deporte, nombre ASC
           ";
  // Conectar con la base de datos
  $conn = mysqli_connect("$sql_host","$sql_usuario","$sql_pass");
  // Seleccionar la BBDD
  mysqli_select_db($conn,"$sql_db");
  // Ejecutar la sentencia
  $rs = mysqli_query($conn,$ssql);

  $catAnterior = -1;
  while($liga = mysqli_fetch_array($rs)) {
    if ($liga['cod_deporte'] != $catAnterior) {
      if ($catAnterior != -1) {
	echo "
        </div>
        <script type='text/javascript'>
          setfoldericonstate('".$catAnterior."');
        </script>
        ";
      }
      $catAnterior = $liga['cod_deporte'];
      echo "
        <div class=\"separator\">
        <a class=\"separator\" href=\"javascript:icntoggle('".$liga['cod_deporte']."');\">
          <img src=\"img/icons/fo.gif\" style=\"border: 0\" name=\"".$liga['cod_deporte']."icn\" 
            class=\"fldicn\" alt=\"".$liga['deporte']."\"/>
        </a>&nbsp;
        <a href=\"#\" class=\"separator\">".$liga['deporte']."</a>
        </div>
        <div id=\"".$liga['cod_deporte']."\" style=\"display:none;\">
        &nbsp;<a href=\"liga-clasificacion.php?idLiga=".$liga['id']."\" class=\"linkmenu\">".$liga['nombre']."</a><br />
      ";
    }
    else {
      echo "
        &nbsp;<a href=\"liga-clasificacion.php?idLiga=".$liga['id']."\" class=\"linkmenu\">".$liga['nombre']."</a><br />
      ";
    }
  }
  echo "
        <script language='Javascript' type='text/javascript'></script>

        </div>
        <script type='text/javascript'>
          setfoldericonstate('".$catAnterior."');
        </script>
  ";
  mysqli_free_result($rs);

  // Representar las ligas que no est�n clasificadas

  $ssql = "SELECT liga.id,liga.nombre
           FROM liga
           WHERE liga.deporte IS NULL
           ORDER BY liga.nombre ASC
           ";
  // Seleccionar la BBDD
  mysqli_select_db($conn,"$sql_db");
  // Ejecutar la sentencia
  $rs = mysqli_query($conn,$ssql);

  echo "
        <div class=\"separator\">
        <a class=\"separator\" href=\"javascript:icntoggle('-1');\">
          <img src=\"img/icons/fo.gif\" style=\"border: 0\" name=\"-1icn\"
            class=\"fldicn\" alt=\"Sin clasificar\"/>
        </a>&nbsp;
        <a href=\"#\" class=\"separator\">Sin clasificar</a>
        </div>
        <div id=\"-1\" style=\"display:none;\">
  ";
  
  while($liga = mysqli_fetch_array($rs)) {
    echo "
        &nbsp;<a href=\"liga-clasificacion.php?idLiga=".$liga['id']."\" class=\"linkmenu\">".$liga['nombre']."</a><br />
    ";
  }
  echo "
        </div>
        <script type='text/javascript'>
          setfoldericonstate('-1');
        </script>
        </div>
        </div>
        </div>
  ";

  mysqli_free_result($rs);

  // Comprobar si se ha iniciado sesion
  if (isset($_SESSION['usuario_login']) && isset($_SESSION['usuario_password'])) {
    $login = $_SESSION['usuario_login'];
    if ($login == "admin") {
      echo "
        <div class=\"box\">
        <div class=\"box-title\">
          Administraci&oacute;n
        </div>
        <div class=\"box-data\">
    
        <div id=\"Navegaci&oacute;n\" style=\"display:block;\">
        <div>&nbsp;<a href=\"liga-admin-usuarios.php\" class=\"linkmenu\">Gesti&oacute;n de usuarios</a></div>
        <div>&nbsp;<a href=\"liga-admin-passwd.php\" class=\"linkmenu\">Cambiar contrase&ntilde;a</a></div>
        <div>&nbsp;<a href=\"liga-admin-categorias.php\" class=\"linkmenu\">Categor&iacute;as deportivas</a></div>

        <script language='Javascript' type='text/javascript'></script>

        </div>
        </div>
        </div>
      ";
    }
    // Es un usuario normal
    else {
      echo "
        <div class=\"box\">
        <div class=\"box-title\">
          Personal
        </div>
        <div class=\"box-data\">
    
        <div id=\"Navegaci&oacute;n\" style=\"display:block;\">
        <div>&nbsp;<a href=\"liga-user-datos.php\" class=\"linkmenu\">Datos personales</a></div>
        <div>&nbsp;<a href=\"liga-user-passwd.php\" class=\"linkmenu\">Cambiar contrase&ntilde;a</a></div>

        <script language='Javascript' type='text/javascript'></script>

        </div>
        </div>
        </div>
      ";
      echo "
        <div class=\"box\">
        <div class=\"box-title\">
          Mis ligas
        </div>
        <div class=\"box-data\">
    
        <div id=\"Navegaci&oacute;n\" style=\"display:block;\">
        <div>&nbsp;<a href=\"liga-config-liga.php?action=nuevaLiga\" class=\"linkmenu\">Nueva liga</a></div>
        <div>&nbsp;<a href=\"liga-mis-ligas.php\" class=\"linkmenu\">Mis ligas</a></div>

        <script language='Javascript' type='text/javascript'></script>

        </div>
        </div>
        </div>
      ";
    }
  }

  echo "
      </td>
  ";
}

?>