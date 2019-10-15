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
