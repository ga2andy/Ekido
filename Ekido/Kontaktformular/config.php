<?php
    $empfaenger = "jeremy.gilgen@student.ibz.ch";  // Bitte tragen Sie hier Ihre E-Mail Adresse ein. (zwischen den Anführungszeichen)
    $ihrname = "Ekido"; // Bitte tragen Sie hier Ihren Namen ein. (zwischen den Anführungszeichen) Dieser erscheint als Absender in der Danke Mail.
    
    
    $cfg['DATENSCHUTZ_ERKLAERUNG'] = 1;  //  0 = Ohne Datenschutzerklärung    1 = Mit Datenschutzerklärung
    
    $datenschutzerklaerung = "datenschutz.php";   //  Pfad zur Datenschutzerklärung. "datenschutz.php" kann durch einen Link/URL ersetzt werden. (muss mit "http://www." anfangen!)
    
    $danke = "index.php";  // Pfad zur Danke Seite. "danke.php" kann durch einen Link/URL ersetzt werden. (muss mit "http://www." anfangen!) Die entsprechende Danke Seite kann mit dem nachfolgenden Script auch außerhalb des iFrame angezeigt werden: http://www.kontaktformular.com/faq-script-php-kontakt-formular.html#Danke-Seite-außerhalb-vom-IFrame-anzeigen


    

// Die SMTP Funktion kann im nachfolgenden Abschnitt aktiviert werden. Wichtig: Auf Ihrem Webserver muss mind. PHP 5.5 oder höher installiert sein! Die aktuelle PHP Version können Sie prüfen, indem Sie die Datei phpinfo.php im Browser aufrufen. //

    $smtp = array();

    
    $smtp['enabled'] = 0; // Soll das Kontaktformular E-Mails über einen SMTP Server versenden? Ja = 1, Nein = 0

    
    $smtp['host'] = 'smtp.example.de'; // Der Host, unter welchem der SMTP Server erreichbar ist. (bspw. smtp.gmail.com)

    
    $smtp['user'] = ''; // Der Benutzername, mit welchem Sie sich bei Ihrem SMTP Server authentifizieren. (kann u.U. die oben genannte E-Mail Adresse sein!)

    
    $smtp['password'] = ''; // Das Passwort, mit welchem Sie sich bei Ihrem SMTP Server authentifizieren.

    
    $smtp['encrpytion'] = 'tls'; // Die Art der Verschlüsselung, die bei der Verbindung mit Ihrem SMTP Server verwendet wird: '', 'ssl' oder 'tls'

    
    $smtp['port'] = 587; // Der TCP Port, unter welchem Ihr SMTP Server erreichbar ist.

    
    $smtp['debug'] = 0; // Das Debuglevel (0 - 4)
    
    
    
    
    
// Maximale Zeichenlänge der Felder definieren //

    $zeichenlaenge_firma = "50";  // Maximale Zeichen - Feld "Firma" (zwischen den Anführungszeichen)

    $zeichenlaenge_vorname = "50"; // Maximale Zeichen - Feld "Vorname" (zwischen den Anführungszeichen)

    $zeichenlaenge_name = "50"; // Maximale Zeichen - Feld "Nachname" (zwischen den Anführungszeichen)

    $zeichenlaenge_email = "50"; // Maximale Zeichen - Feld "E-Mail" (zwischen den Anführungszeichen)

    $zeichenlaenge_telefon = "50"; // Maximale Zeichen - Feld "Telefon" (zwischen den Anführungszeichen)

    $zeichenlaenge_betreff = "50"; // Maximale Zeichen - Feld "Betreff" (zwischen den Anführungszeichen)
?> 