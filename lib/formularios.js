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

function isDigit(num)
{
  if (num.length>1)
  {
    return false;
  }
  var string="1234567890";
  if (string.indexOf(num)!=-1)
  {
    return true;
  }
  return false;
}

function quitarBlancosMedio(frmObj)
{
  var out = "", flag = 0;
      str = frmObj.value;
  for (i = 0; i < str.length; i++) {
    if (str.charAt(i) != " ") {
      out += str.charAt(i);
      flag = 0;
    }
    else {
      if(flag == 0) {
        out += " ";
        flag = 1;
      }
    }
  }
  frmObj.value = out;
}

function quitarBlancosPrincipio(frmObj)
{
  while(''+frmObj.value.charAt(0)==' ')
    frmObj.value=frmObj.value.substring(1,frmObj.value.length);
}

function quitarBlancosFinal(frmObj)
{
  while(''+frmObj.value.charAt(frmObj.value.length-1)==' ')
    frmObj.value=frmObj.value.substring(0,frmObj.value.length-1);
}

function quitarBlancos(frmObj)
{
  quitarBlancosPrincipio(frmObj);
  quitarBlancosFinal(frmObj);
  quitarBlancosMedio(frmObj);
}

function changeCase(frmObj) {
  var index;
  var tmpStr;
  var tmpChar;
  var preString;
  var postString;
  var strlen;
  tmpStr = frmObj.value.toLowerCase();
  strLen = tmpStr.length;
  if (strLen > 0)  {
    for (index = 0; index < strLen; index++)  {
      if (index == 0)  {
        tmpChar = tmpStr.substring(0,1).toUpperCase();
        postString = tmpStr.substring(1,strLen);
        tmpStr = tmpChar + postString;
      }
      else {
        tmpChar = tmpStr.substring(index, index+1);
        if (tmpChar == " " && index < (strLen-1))  {
          tmpChar = tmpStr.substring(index+1, index+2).toUpperCase();
          preString = tmpStr.substring(0, index+1);
          postString = tmpStr.substring(index+2,strLen);
          tmpStr = preString + tmpChar + postString;
        }
      }
    }
  }
  frmObj.value = tmpStr;
}

function checkNombre()
{
  nombre = document.form_registro.nombre.value;
  return (nombre.length != 0);
}

function checkTelefono()
{
  strTlf = document.form_registro.telefono.value;
  if (strTlf.length == 0)
    return true;
  if (strTlf.length != 9)
    return false;
  for (i = 0; i < strTlf.length; i++) {
    if (!isDigit(strTlf.charAt(i))) {
      return false;
    }
  }
  return true;
}

function checkLogin()
{
  login = document.form_registro.login.value;
  if (login.length == 0)
    return false;
  for (i = 0; i < login.length; i++) {
    if (login.charAt(i) == " ") {
      return false;
    }
  }
  return true;
}

function checkPw()
{
  pw1 = document.form_registro.password1.value;
  pw2 = document.form_registro.password2.value;
  if(pw1.length < 4)
  {
    document.form_registro.password1.focus();
    return false;
  }
  else if(pw2.length < 4)
  {
    document.form_registro.password2.focus();
    return false;
  }
  else if (pw1 != pw2)
  {
    return false;
  }
  else
    return true;
}

function checkEmail()
{
  var valor = document.form_registro.email.value;
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(valor)){
    return (true)
  }
  else {
    return (false);
  }
}

function checkPuntos(frmObj)
{
  var valor = frmObj.value;
  if (valor.length == 0)
    return (false);
  for (i = 0; i < valor.length; i++) {
    if (!isDigit(valor.charAt(i))) {
      return false;
    }
  }
  return true;
}

function checkResultado(frmObj)
{
  var valor = frmObj.value;
  if (valor.length == 0)
    return (true);
  for (i = 0; i < valor.length; i++) {
    if (!isDigit(valor.charAt(i))) {
      return false;
    }
  }
  return true;
}

function checkForm()
{
    var errores = "";
        hay_errores = false;

    if ( !checkPw() )
    {
        errores = "La contraseña no coincide o es menor de 4 caracteres.\n" + errores;
        document.form_registro.password1.value = "";
        document.form_registro.password2.value = "";
        document.form_registro.password1.focus();
        hay_errores = true;
    }
    if ( !checkLogin() )
    {
        errores = "El alias introducido es incorrecto.\n" + errores;
        document.form_registro.login.focus();
        hay_errores = true;
    }
    if ( !checkEmail() )
    {
        errores = "Dirección de correo electrónico incorrecta.\n" + errores;
        document.form_registro.email.focus();
        hay_errores = true;
    }
    if ( !checkTelefono() )
    {
        errores = "Número de teléfono incorrecto.\n" + errores;
        document.form_registro.telefono.focus();
        hay_errores = true;
    }
    if ( !checkNombre() )
    {
        errores = "Debe introducir su nombre.\n" + errores;
        document.form_registro.nombre.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
        document.form_registro.submit();
} 

function checkFormEdicion()
{
    var errores = "";
        hay_errores = false;

    if ( !checkLogin() )
    {
        errores = "El alias introducido es incorrecto.\n" + errores;
        document.form_registro.login.focus();
        hay_errores = true;
    }
    if ( !checkEmail() )
    {
        errores = "Dirección de correo electrónico incorrecta.\n" + errores;
        document.form_registro.email.focus();
        hay_errores = true;
    }
    if ( !checkTelefono() )
    {
        errores = "Número de teléfono incorrecto.\n" + errores;
        document.form_registro.telefono.focus();
        hay_errores = true;
    }
    if ( !checkNombre() )
    {
        errores = "Debe introducir el nombre del usuario.\n" + errores;
        document.form_registro.nombre.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
        document.form_registro.submit();
} 


function checkFormConfigLiga()
{
    var errores = "";
        hay_errores = false;

    if ( !checkPuntos(document.config_liga.ptosDerrota) )
    {
        errores = "Debe indicar cuántos puntos gana un equipo por derrota.\n" + errores;
        document.config_liga.ptosDerrota.focus();
        hay_errores = true;
    }
    if ( !checkPuntos(document.config_liga.ptosEmpate) )
    {
        errores = "Debe indicar cuántos puntos gana un equipo por empate.\n" + errores;
        document.config_liga.ptosEmpate.focus();
        hay_errores = true;
    }
    if ( !checkPuntos(document.config_liga.ptosVictoria) )
    {
        errores = "Debe indicar cuántos puntos gana un equipo por victoria.\n" + errores;
        document.config_liga.ptosVictoria.focus();
        hay_errores = true;
    }
    if ( document.config_liga.nombre.value.length == 0)
    {
        errores = "Debe introducir un nombre para la liga.\n" + errores;
        document.config_liga.nombre.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
        document.config_liga.submit();
} 

function checkFormNuevoEquipo()
{
    var errores = "";
        hay_errores = false;

    if ( document.form_nuevo_equipo.nombreEquipo.value.length == 0)
    {
        errores = "Debe introducir un nombre para el equipo.\n" + errores;
        document.form_nuevo_equipo.nombreEquipo.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        document.form_nuevo_equipo.submit();
    }
} 

function checkFormNuevaCategoria()
{
    var errores = "";
    hay_errores = false;

    if ( document.form_nueva_categoria.nombreDisciplina.value.length == 0)
    {
        errores = "Debe especificar un nombre para la nueva categoría.\n" + errores;
        document.form_nueva_categoria.nombreDisciplina.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        document.form_nueva_categoria.submit();
    }
} 

function checkFormNuevoPartido(frmObj)
{
    var errores = "";
    hay_errores = false;

    if ( !(checkResultado(frmObj.resLocal) == 
           checkResultado(frmObj.resVisitante) &&
           (checkResultado(frmObj.resLocal) == true)
          )
       )
    {
        errores = "El resultado no es correcto.\n" + errores;
        frmObj.resLocal.focus();
        hay_errores = true;
    }
    else if ( ((frmObj.resLocal.value.length == 0) ^
              (frmObj.resVisitante.value.length == 0)
             )
           )
    {
         errores = "El resultado no es correcto.\n" + errores;
         frmObj.resLocal.focus();
         hay_errores = true;
    }
    if ( frmObj.idLocal.value ==
         frmObj.idVisitante.value
       )
    {
        errores = "Un equipo no puede jugar contra sí mismo.\n" + errores;
        hay_errores = true;
    }
/*    if ( frmObj.fechaPartido.value.length == 0)
    {
        errores = "Debe especificar una fecha para el partido.\n" + errores;
        hay_errores = true;
    }*/
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        frmObj.submit();
    }
} 

function checkFormResultado(frmObj)
{
    var errores = "";
    hay_errores = false;

    if ( !(checkResultado(frmObj.resLocal) == 
           checkResultado(frmObj.resVisitante) &&
           (checkResultado(frmObj.resLocal) == true)
          )
       )
    {
        errores = "El resultado no es correcto.\n" + errores;
        frmObj.resLocal.focus();
        hay_errores = true;
    }
    else if ( ((frmObj.resLocal.value.length == 0) ^
              (frmObj.resVisitante.value.length == 0)
             )
           )
    {
         errores = "El resultado no es correcto.\n" + errores;
         frmObj.resLocal.focus();
         hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        frmObj.submit();
    }
} 

function checkFormEditarEquipo(frmObj)
{
    var errores = "";
    hay_errores = false;

    if ( frmObj.nombreEquipo.value.length == 0 )
    {
         errores = "Debe introducir un nombre para el equipo.\n" + errores;
         frmObj.nombreEquipo.focus();
         hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        frmObj.submit();
    }
}

function checkFormInstall()
{
    var errores = "";
        hay_errores = false;
        admin_passw = true;
    pw1 = document.form_install.admin_pass1.value;
    pw2 = document.form_install.admin_pass2.value;
    if(pw1.length < 4 || pw2.length < 4)
    {
      admin_passw = false;
    }
    else if (pw1 != pw2)
    {
      admin_passw = false;
    }

    if ( !admin_passw )
    {
        errores = "La contraseña de administrador no coincide o es menor de 4 caracteres.\n" + errores;
        document.form_install.admin_pass1.value = "";
        document.form_install.admin_pass2.value = "";
        document.form_install.admin_pass1.focus();
        hay_errores = true;
    }
    if ( document.form_install.sql_pass1.value != document.form_install.sql_pass2.value )
    {
        errores = "La contraseña de acceso a la base de datos en incorrecta.\n" + errores;
        document.form_install.sql_pass1.focus();
        hay_errores = true;
    }
    if ( document.form_install.sql_db.value.length == 0 )
    {
        errores = "Debe introducir un nombre para la base de datos nueva.\n" + errores;
        document.form_install.sql_db.focus();
        hay_errores = true;
    }
    if ( document.form_install.sql_host.value.length == 0 )
    {
        errores = "Debe introducir el nombre DNS o dirección IP del servidor de bases de datos.\n" + errores;
        document.form_install.sql_host.focus();
        hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
        document.form_install.submit();
} 

function checkFormNuevaJornada(frmObj)
{
    var errores = "";
    hay_errores = false;

    if ( !checkPuntos(frmObj.numeroJornada) )
    {
         errores = "Debe introducir un valor numérico único.\n" + errores;
         frmObj.numeroJornada.focus();
         hay_errores = true;
    }
    if (hay_errores)
    {
        alert(errores);
        return false;
    }
    else
    {
        frmObj.submit();
    }
}

function confirmation(link_borrado) {
    var answer = confirm("Si elimina el equipo, se borrarán todos los partidos en los que haya participado.\n¿Desea continuar?")
    if (answer){
        window.location = link_borrado;
    }
    else {
        return;
    }
}

function confirmationDelJornada(link_borrado) {
    var answer = confirm("Al eliminar la jornada, se borrarán todos los partidos que la conforman.\n¿Desea continuar?")
    if (answer){
        window.location = link_borrado;
    }
    else {
        return;
    }
}

function confirmationDelLiga(link_borrado) {
    var answer = confirm("Al eliminar la liga, se borrarán todos los equipos, partidos, resultados, etc. relacionados con la misma.\n¿Desea continuar?")
    if (answer){
        window.location = link_borrado;
    }
    else {
        return;
    }
}

function confirmationDelUsuario(link_borrado) {
    var answer = confirm("Al eliminar un usuario, se borrarán todos las ligas del usario, así como todos los datos relacionados con las mismas.\n¿Desea continuar?")
    if (answer){
        window.location = link_borrado;
    }
    else {
        return;
    }
}

function vertical(mensaje)
{
    cont =0;
    while (cont<mensaje.length)
    {
        var letra = mensaje.substring(cont,cont+1);
        document.write(letra+"<br>");
        cont+=1;
    }
}
