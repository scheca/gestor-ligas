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
