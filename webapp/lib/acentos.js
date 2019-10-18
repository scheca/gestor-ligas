function alert1(x) { alert(acentos(x)) }

function confirm1(x) { confirm(acentos(x)) }

function acentos(x) {
	// version 040623
	// Spanish - Espa�ol
	// Portuguese - Portugu�s - Portugu�s
	// Italian - Italiano
	// French - Franc�s - Fran�ais
	// Also accepts and converts single and double quotation marks, square and angle brackets
	// and miscelaneous symbols.
	// Also accepts and converts html entities for all the above.
//	if (navigator.appVersion.toLowerCase().indexOf("windows") != -1) {return x}
	x = x.replace(/�/g,"\xA1");	x = x.replace(/&iexcl;/g,"\xA1")
	x = x.replace(/�/g,"\xBF");	x = x.replace(/&iquest;/g,"\xBF")
	x = x.replace(/�/g,"\xC0");	x = x.replace(/&Agrave;/g,"\xC0")
	x = x.replace(/�/g,"\xE0");	x = x.replace(/&agrave;/g,"\xE0")
	x = x.replace(/�/g,"\xC1");	x = x.replace(/&Aacute;/g,"\xC1")
	x = x.replace(/�/g,"\xE1");	x = x.replace(/&aacute;/g,"\xE1")
	x = x.replace(/�/g,"\xC2");	x = x.replace(/&Acirc;/g,"\xC2")
	x = x.replace(/�/g,"\xE2");	x = x.replace(/&acirc;/g,"\xE2")
	x = x.replace(/�/g,"\xC3");	x = x.replace(/&Atilde;/g,"\xC3")
	x = x.replace(/�/g,"\xE3");	x = x.replace(/&atilde;/g,"\xE3")
	x = x.replace(/�/g,"\xC4");	x = x.replace(/&Auml;/g,"\xC4")
	x = x.replace(/�/g,"\xE4");	x = x.replace(/&auml;/g,"\xE4")
	x = x.replace(/�/g,"\xC5");	x = x.replace(/&Aring;/g,"\xC5")
	x = x.replace(/�/g,"\xE5");	x = x.replace(/&aring;/g,"\xE5")
	x = x.replace(/�/g,"\xC6");	x = x.replace(/&AElig;/g,"\xC6")
	x = x.replace(/�/g,"\xE6");	x = x.replace(/&aelig;/g,"\xE6")
	x = x.replace(/�/g,"\xC7");	x = x.replace(/&Ccedil;/g,"\xC7")
	x = x.replace(/�/g,"\xE7");	x = x.replace(/&ccedil;/g,"\xE7")
	x = x.replace(/�/g,"\xC8");	x = x.replace(/&Egrave;/g,"\xC8")
	x = x.replace(/�/g,"\xE8");	x = x.replace(/&egrave;/g,"\xE8")
	x = x.replace(/�/g,"\xC9");	x = x.replace(/&Eacute;/g,"\xC9")
	x = x.replace(/�/g,"\xE9");	x = x.replace(/&eacute;/g,"\xE9")
	x = x.replace(/�/g,"\xCA");	x = x.replace(/&Ecirc;/g,"\xCA")
	x = x.replace(/�/g,"\xEA");	x = x.replace(/&ecirc;/g,"\xEA")
	x = x.replace(/�/g,"\xCB");	x = x.replace(/&Euml;/g,"\xCB")
	x = x.replace(/�/g,"\xEB");	x = x.replace(/&euml;/g,"\xEB")
	x = x.replace(/�/g,"\xCC");	x = x.replace(/&Igrave;/g,"\xCC")
	x = x.replace(/�/g,"\xEC");	x = x.replace(/&igrave;/g,"\xEC")
	x = x.replace(/�/g,"\xCD");	x = x.replace(/&Iacute;/g,"\xCD")
	x = x.replace(/�/g,"\xED");	x = x.replace(/&iacute;/g,"\xED")
	x = x.replace(/�/g,"\xCE");	x = x.replace(/&Icirc;/g,"\xCE")
	x = x.replace(/�/g,"\xEE");	x = x.replace(/&icirc;/g,"\xEE")
	x = x.replace(/�/g,"\xCF");	x = x.replace(/&Iuml;/g,"\xCF")
	x = x.replace(/�/g,"\xEF");	x = x.replace(/&iuml;/g,"\xEF")
	x = x.replace(/�/g,"\xD1");	x = x.replace(/&Ntilde;/g,"\xD1")
	x = x.replace(/�/g,"\xF1");	x = x.replace(/&ntilde;/g,"\xF1")
	x = x.replace(/�/g,"\xD2");	x = x.replace(/&Ograve;/g,"\xD2")
	x = x.replace(/�/g,"\xF2");	x = x.replace(/&ograve;/g,"\xF2")
	x = x.replace(/�/g,"\xD3");	x = x.replace(/&Oacute;/g,"\xD3")
	x = x.replace(/�/g,"\xF3");	x = x.replace(/&oacute;/g,"\xF3")
	x = x.replace(/�/g,"\xD4");	x = x.replace(/&Ocirc;/g,"\xD4")
	x = x.replace(/�/g,"\xF4");	x = x.replace(/&ocirc;/g,"\xF4")
	x = x.replace(/�/g,"\xD5");	x = x.replace(/&Otilde;/g,"\xD5")
	x = x.replace(/�/g,"\xF5");	x = x.replace(/&otilde;/g,"\xF5")
	x = x.replace(/�/g,"\xD6");	x = x.replace(/&Ouml;/g,"\xD6")
	x = x.replace(/�/g,"\xF6");	x = x.replace(/&ouml;/g,"\xF6")
	x = x.replace(/�/g,"\xD8");	x = x.replace(/&Oslash;/g,"\xD8")
	x = x.replace(/�/g,"\xF8");	x = x.replace(/&oslash;/g,"\xF8")
	x = x.replace(/�/g,"\xD9");	x = x.replace(/&Ugrave;/g,"\xD9")
	x = x.replace(/�/g,"\xF9");	x = x.replace(/&ugrave;/g,"\xF9")
	x = x.replace(/�/g,"\xDA");	x = x.replace(/&Uacute;/g,"\xDA")
	x = x.replace(/�/g,"\xFA");	x = x.replace(/&uacute;/g,"\xFA")
	x = x.replace(/�/g,"\xDB");	x = x.replace(/&Ucirc;/g,"\xDB")
	x = x.replace(/�/g,"\xFB");	x = x.replace(/&ucirc;/g,"\xFB")
	x = x.replace(/�/g,"\xDC");	x = x.replace(/&Uuml;/g,"\xDC")
	x = x.replace(/�/g,"\xFC");	x = x.replace(/&uuml;/g,"\xFC")
	
	x = x.replace(/\"/g,"\x22")
	x = x.replace(/\'/g,"\x27")
	x = x.replace(/\</g,"\x3C")
	x = x.replace(/\>/g,"\x3E")
	x = x.replace(/\[/g,"\x5B")
	x = x.replace(/\]/g,"\x5D")

	x = x.replace(/�/g,"\xA2");	x = x.replace(/&cent;/g,"\xA2") 
	x = x.replace(/�/g,"\xA3");	x = x.replace(/&pound;/g,"\xA3")
	x = x.replace(/�/g,"\u20AC");	x = x.replace(/&euro;/g,"\u20AC") 
	x = x.replace(/�/g,"\xA9");	x = x.replace(/&copy;/g,"\xA9") 
	x = x.replace(/�/g,"\xAE");	x = x.replace(/&reg;/g,"\xAE") 
	x = x.replace(/�/g,"\xAA");	x = x.replace(/&ordf;/g,"\xAA") 
	x = x.replace(/�/g,"\xBA");	x = x.replace(/&ordm;/g,"\xBA") 
	x = x.replace(/�/g,"\xB0");	x = x.replace(/&deg;/g,"\xB0") 
	x = x.replace(/�/g,"\xB1");	x = x.replace(/&plusmn;/g,"\xB1")
	x = x.replace(/�/g,"\xD7");	x = x.replace(/&times;/g,"\xD7") 
	
		
	return x
}

