<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set('Europe/Berlin');

require_once("AntiSpam.php");

$q = AntiSpam::getRandomQuestion();

header('Content-type: text/html; charset=utf-8');


#########################################################################
#	Kontaktformular.com         					                                #
#	http://www.kontaktformular.com        						                    #
#	All rights by KnotheMedia.de                                    			#
#-----------------------------------------------------------------------#
#	I-Net: http://www.knothemedia.de                            					#
#########################################################################
// Der Copyrighthinweis darf NICHT entfernt werden!


  $script_root = substr(__FILE__, 0,
                        strrpos(__FILE__,
                                DIRECTORY_SEPARATOR)
                       ).DIRECTORY_SEPARATOR;

require_once $script_root.'upload.php';

$remote = getenv("REMOTE_ADDR");

function encrypt($string, $key) {
$result = '';
for($i=0; $i<strlen($string); $i++) {
   $char = substr($string, $i, 1);
   $keychar = substr($key, ($i % strlen($key))-1, 1);
   $char = chr(ord($char)+ord($keychar));
   $result.=$char;
}
return base64_encode($result);
}
$sicherheits_eingabe = encrypt($_POST["sicherheitscode"], "8h384ls94");
$sicherheits_eingabe = str_replace("=", "", $sicherheits_eingabe);

@require('config.php');
require_once("AntiSpam.php");
if ($_POST['delete'])
{
unset($_POST);
}

if ($_POST["kf-km"]) {

  $anrede      = $_POST["anrede"];
$titel      = $_POST["titel"];

$vorname      = $_POST["vorname"];
$name      = $_POST["name"];
$firma   = $_POST["firma"];
   $telefon   = $_POST["telefon"];
$email      = $_POST["email"];
$betreff   = $_POST["betreff"];
$nachricht   = $_POST["nachricht"];for ($i=0; $i < $cfg['DATENSCHUTZ_ERKLAERUNG']; $i++) { $datenschutz = $_POST["datenschutz"]; }

   $date = date("d.m.Y | H:i");
   $ip = $_SERVER['REMOTE_ADDR'];
   $UserAgent = $_SERVER["HTTP_USER_AGENT"];
   $host = getHostByAddr($remote);

$anrede = stripslashes($anrede);
$vorname = stripslashes($vorname);
$name = stripslashes($name);
$email = stripslashes($email);
$betreff = stripslashes($betreff);
$nachricht = stripslashes($nachricht);for ($i=0; $i < $cfg['DATENSCHUTZ_ERKLAERUNG']; $i++) { $datenschutz = stripslashes($datenschutz); }

if (isset($anrede) && $anrede == "") {

          $fehler['anrede'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Bitte wählen Sie eine <strong>Anrede</strong> aus.</span>";

  }


if(!$vorname) {

 $fehler['vorname'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Geben Sie bitte Ihren <strong>Vornamen</strong> ein.</span>";

}


if(!$name) {

 $fehler['name'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Geben Sie bitte Ihren <strong>Nachnamen</strong> ein.</span>";

}





if (!preg_match("/^[0-9a-zA-ZÄÜÖ_.-]+@[0-9a-z.-]+\.[a-z]{2,6}$/", $email)) {
   $fehler['email'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Geben Sie bitte Ihre <strong>E-Mail-Adresse</strong> ein.</span>";
}


if(!$betreff) {

 $fehler['betreff'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Geben Sie bitte einen <strong>Betreff</strong> ein.</span>";


}

if(!$nachricht) {

 $fehler['nachricht'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Geben Sie bitte eine <strong>Nachricht</strong> ein.</span>";


}





//check antiSpam
if(isset($_POST["q_id"])){
	$answer = AntiSpam::getAnswerById(intval($_POST["q_id"]));
	if($_POST["q"] != $answer){
		$fehler['q_id12'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Bitte die <strong>Sicherheitsfrage</strong> richtig beantworten.</span>";
	}
}



for ($i=0; $i < $cfg['DATENSCHUTZ_ERKLAERUNG']; $i++) { if (isset($datenschutz) && $datenschutz == "") { $fehler['datenschutz'] = "<span class='errormsg' style='color:red;font-size:.75rem;'>Sie müssen die <strong>Datenschutzerklärung</strong> akzeptieren.</span><br />";} }

    if (!isset($fehler) || count($fehler) == 0) {
      $error             = false;
      $errorMessage      = '';
      $uploadErrors      = array();
      $uploadedFiles     = array();
      $totalUploadSize   = 0;

      if ($cfg['UPLOAD_ACTIVE'] && in_array($_SERVER['REMOTE_ADDR'], $cfg['BLACKLIST_IP']) === true) {
          $error = true;
          $fehler['upload'] = "<span class='errormsg'>Sie haben keine Erlaubnis Dateien hochzuladen.</span>";
      }

      if (!$error) {
          for ($i=0; $i < $cfg['NUM_ATTACHMENT_FIELDS']; $i++) {
              if ($_FILES['f']['error'][$i] == UPLOAD_ERR_NO_FILE) {
                  continue;
              }

              $extension = explode('.', $_FILES['f']['name'][$i]);
              $extension = strtolower($extension[count($extension)-1]);
              $totalUploadSize += $_FILES['f']['size'][$i];

              if ($_FILES['f']['error'][$i] != UPLOAD_ERR_OK) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  switch ($_FILES['f']['error'][$i]) {
                      case UPLOAD_ERR_INI_SIZE :
                          $uploadErrors[$j]['error'] = 'Die Datei ist zu groß (PHP-Ini Direktive).';
                      break;
                      case UPLOAD_ERR_FORM_SIZE :
                          $uploadErrors[$j]['error'] = 'Die Datei ist zu groß (MAX_FILE_SIZE in HTML-Formular).';
                      break;
                      case UPLOAD_ERR_PARTIAL :
						  if ($cfg['UPLOAD_ACTIVE']) {
                          	  $uploadErrors[$j]['error'] = 'Die Datei wurde nur teilweise hochgeladen.';
						  } else {
							  $uploadErrors[$j]['error'] = 'Die Datei wurde nur teilweise versendet.';
					  	  }
                      break;
                      case UPLOAD_ERR_NO_TMP_DIR :
                          $uploadErrors[$j]['error'] = 'Es wurde kein temporärer Ordner gefunden.';
                      break;
                      case UPLOAD_ERR_CANT_WRITE :
                          $uploadErrors[$j]['error'] = 'Fehler beim Speichern der Datei.';
                      break;
                      case UPLOAD_ERR_EXTENSION  :
                          $uploadErrors[$j]['error'] = 'Unbekannter Fehler durch eine Erweiterung.';
                      break;
                      default :
						  if ($cfg['UPLOAD_ACTIVE']) {
                          	  $uploadErrors[$j]['error'] = 'Unbekannter Fehler beim Hochladen.';
						  } else {
							  $uploadErrors[$j]['error'] = 'Unbekannter Fehler beim Versenden des Email-Attachments.';
						  }
                  }

                  $j++;
                  $error = true;
              }
              else if ($totalUploadSize > $cfg['MAX_ATTACHMENT_SIZE']*1024) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'Maximaler Upload erreicht ('.$cfg['MAX_ATTACHMENT_SIZE'].' KB).';
                  $j++;
                  $error = true;
              }
              else if ($_FILES['f']['size'][$i] > $cfg['MAX_FILE_SIZE']*1024) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'Die Datei ist zu groß (max. '.$cfg['MAX_FILE_SIZE'].' KB).';
                  $j++;
                  $error = true;
              }
              else if (!empty($cfg['BLACKLIST_EXT']) && strpos($cfg['BLACKLIST_EXT'], $extension) !== false) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'Die Dateiendung ist nicht erlaubt.';
                  $j++;
                  $error = true;
              }
              else if (preg_match("=^[\\:*?<>|/]+$=", $_FILES['f']['name'][$i])) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'Ungültige Zeichen im Dateinamen (\/:*?<>|).';
                  $j++;
                  $error = true;
              }
              else if ($cfg['UPLOAD_ACTIVE'] && file_exists($cfg['UPLOAD_FOLDER'].'/'.$_FILES['f']['name'][$i])) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'Die Datei existiert bereits.';
                  $j++;
                  $error = true;
              }
              else {
				  if ($cfg['UPLOAD_ACTIVE']) {
                     move_uploaded_file($_FILES['f']['tmp_name'][$i], $cfg['UPLOAD_FOLDER'].'/'.$_FILES['f']['name'][$i]);
				  }
                  $uploadedFiles[$_FILES['f']['tmp_name'][$i]] = $_FILES['f']['name'][$i];
              }
          }
      }

      if ($error) {
          $errorMessage = 'Es sind folgende Fehler beim Versenden des Kontaktformulars aufgetreten:'."\n";
          if (count($uploadErrors) > 0) {
              foreach ($uploadErrors as $err) {
                  $tmp .= '<strong>'.$err['name']."</strong><br/>\n- ".$err['error']."<br/><br/>\n";
              }
              $tmp = "<br/><br/>\n".$tmp;
          }
          $errorMessage .= $tmp.'';
          $fehler['upload'] = $errorMessage;
      }
  }



   if (!isset($fehler))
   {
		// ------------------------------------------------------------
		// -------------------- send mail to admin --------------------
		// ------------------------------------------------------------

		// ---- create mail-message for admin
	  $mailcontent  = "Folgendes wurde am ". $date ." Uhr per Formular geschickt:\n" . "-------------------------------------------------------------------------\n\n";
		$mailcontent .= "Name: " . $anrede . " " . $titel . "" . $vorname . " " . $name . "\n";
		$mailcontent .= "Firma: " . $firma . "\n\n";
		$mailcontent .= "E-Mail: " . $email . "\n";
		$mailcontent .= "Telefon: " . $telefon . "\n";
		$mailcontent .= "\nBetreff: " . $betreff . "\n";
		$mailcontent .= "Nachricht:\n" . $nachricht = preg_replace("/\r\r|\r\n|\n\r|\n\n/","\n",$nachricht) . "\n\n";
		if(count($uploadedFiles) > 0){
			if($cfg['UPLOAD_ACTIVE']){
				$mailcontent .= 'Es wurden folgende Dateien hochgeladen:'."\n";
				foreach ($uploadedFiles as $filename) {
					$mailcontent .= ' - '.$cfg['DOWNLOAD_URL'].'/'.$cfg['UPLOAD_FOLDER'].'/'.$filename."\n";
				}
			} else {
				$mailcontent .= 'Es wurden folgende Dateien als Attachment angehängt:'."\n";
				foreach ($uploadedFiles as $filename) {
					$mailcontent .= ' - '.$filename."\n";
				}
			}
		}
		for ($i=0; $i < $cfg['DATENSCHUTZ_ERKLAERUNG']; $i++) { $mailcontent .= "\n\nDatenschutz: " . $datenschutz . " \n"; }
    $mailcontent .= "\n\nIP Adresse: " . $ip . "\n";
		$mailcontent = strip_tags ($mailcontent);

		// ---- get attachments for admin
		$attachments = array();
		if(!$cfg['UPLOAD_ACTIVE'] && count($uploadedFiles) > 0){
			foreach($uploadedFiles as $tempFilename => $filename) {
				$attachments[$filename] = file_get_contents($tempFilename);
			}
		}

		$success = false;

        // ---- send mail to admin
        if($smtp['enabled'] !== 0) {
            require_once __DIR__ . '/smtp.php';
            $success = SMTP::send(
                $smtp['host'],
                $smtp['user'],
                $smtp['password'],
                $smtp['encryption'],
                $smtp['port'],
                $email,
                $ihrname,
                $empfaenger,
                $betreff,
                $mailcontent,
                $uploadedFiles,
                $cfg['UPLOAD_FOLDER'],
                $smtp['debug']
            );
        } else {
            $success = sendMyMail($email, $vorname." ".$name, $empfaenger, $betreff, $mailcontent, $attachments);
        }

    	// ------------------------------------------------------------
    	// ------------------- send mail to customer ------------------
    	// ------------------------------------------------------------
    	if($success){

    		// ---- create mail-message for customer
			$mailcontent  = "Vielen Dank für Ihre E-Mail. Wir werden schnellstmöglich darauf antworten.\n\n";
    		$mailcontent .= "Zusammenfassung: \n" .  "-------------------------------------------------------------------------\n\n";
    		$mailcontent .= "Name: " . $anrede . " " . $titel . "" . $vorname . " " . $name . "\n";
    		$mailcontent .= "Firma: " . $firma . "\n\n";
    		$mailcontent .= "E-Mail: " . $email . "\n";
    		$mailcontent .= "Telefon: " . $telefon . "\n";
    		$mailcontent .= "\nBetreff: " . $betreff . "\n";
    		$mailcontent .= "Nachricht:\n" . str_replace("\r", "", $nachricht) . "\n\n";
    		if(count($uploadedFiles) > 0){
    			$mailcontent .= 'Sie haben folgende Dateien übertragen:'."\n";
    			foreach($uploadedFiles as $file){
    				$mailcontent .= ' - '.$file."\n";
    			}
    		}
    		$mailcontent = strip_tags ($mailcontent);

    		// ---- send mail to customer
            if($smtp['enabled'] !== 0) {
                SMTP::send(
                    $smtp['host'],
                    $smtp['user'],
                    $smtp['password'],
                    $smtp['encryption'],
                    $smtp['port'],
                    $empfaenger,
                    $ihrname,
                    $email,
                    "Ihre Anfrage",
                    $mailcontent,
                    array(),
                    $cfg['UPLOAD_FOLDER'],
                    $smtp['debug']
                );
            } else {
                $success = sendMyMail($empfaenger, $ihrname, $email, "Ihre Anfrage", $mailcontent);
            }

            if($smtp['enabled'] === 0 || $smtp['debug'] === 0) {
    		    echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=".$danke."\">";
            }

    		exit;
		}
	}
}
// clean post
foreach($_POST as $key => $value){
    $_POST[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
}
?>
<?php




function sendMyMail($fromMail, $fromName, $toMail, $subject, $content, $attachments=array()){

	$boundary = md5(uniqid(time()));
	$eol = PHP_EOL;

	// header
	$header = "From: =?UTF-8?B?".base64_encode(stripslashes($fromName))."?= <".$fromMail.">".$eol;
	$header .= "Reply-To: <".$fromMail.">".$eol;
	$header .= "MIME-Version: 1.0".$eol;
	if(is_array($attachments) && 0<count($attachments)){
		$header .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"";
	}
	else{
		$header .= "Content-type: text/plain; charset=utf-8";
	}


	// content with attachments
	if(is_array($attachments) && 0<count($attachments)){

		// content
		$message = "--".$boundary.$eol;
		$message .= "Content-type: text/plain; charset=utf-8".$eol;
		$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$message .= $content.$eol;

		// attachments
		foreach($attachments as $filename=>$filecontent){
			$filecontent = chunk_split(base64_encode($filecontent));
			$message .= "--".$boundary.$eol;
			$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol;
			$message .= "Content-Transfer-Encoding: base64".$eol;
			$message .= "Content-Disposition: attachment; filename=\"".$filename."\"".$eol.$eol;
			$message .= $filecontent.$eol;
		}
		$message .= "--".$boundary."--";
	}
	// content without attachments
	else{
		$message = $content;
	}

	// subject
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";

	// send mail
	return mail($toMail, $subject, $message, $header);
}

?>
<!DOCTYPE html>
<html lang="de-DE">
	<head>
		<meta charset="utf-8">
		<meta name="language" content="de"/>
		<meta name="description" content="kontaktformular.com"/>
		<meta name="revisit" content="After 7 days"/>
		<meta name="robots" content="INDEX,FOLLOW"/>
		<title>kontaktformular.com</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<!-- Stylesheet -->
<link href="style-kontaktformular.css" rel="stylesheet">

<link href='https://fonts.googleapis.com/css?family=Heebo:700' rel='stylesheet' type='text/css'>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script></head>





<body>

<div>
<form class="kontaktformular" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">




     <div class="row">
<label class="col-sm-3 control-label">Firma:</label>
    <div class="col-sm-8">
     <input type="text" name="firma" class="field" placeholder="Firma"  value="<?php echo $_POST[firma]; ?>" maxlength="<?php echo $zeichenlaenge_firma; ?>" />
    </div>
  </div>



   <div class="row">
    <label class="col-sm-3 control-label">Anrede: *</label>
    <div class="col-sm-2"><?php if ($fehler["anrede"] != "") { echo $fehler["anrede"]; } ?>
     <select name="anrede" class="field unselected" style="background-color: #fff;" onchange="if(0!=this.selectedIndex){ this.setAttribute('class', 'field'); }">
							<option value="" selected >Anrede</option>
							<option style="color:#000" value="Herr" <?php if($_POST['anrede']=="Herr"){ echo "selected";}?> >Herr</option>
							<option style="color:#000" value="Frau" <?php if($_POST['anrede']=="Frau"){ echo "selected";}?> >Frau</option>
						</select>
						<script>if(0!=document.getElementsByName('anrede')[0].selectedIndex){ document.getElementsByName('anrede')[0].setAttribute('class', 'field'); }</script>
    </div>


    <label class="col-sm-4 control-label">Titel:</label>
    <div class="col-sm-2">
     <select name="titel" class="field unselected" style="background-color: #fff;" onchange="if(0!=this.selectedIndex){ this.setAttribute('class', 'field'); }else{ this.setAttribute('class', 'field unselected'); }">
							<option value="" selected >Titel</option>
							<option style="color:#000" value="Dr. " <?php if($_POST['titel']=="Dr. "){ echo "selected";}?> >Dr.</option>
							<option style="color:#000" value="Dr. med. " <?php if($_POST['titel']=="Dr. med. "){ echo "selected";}?> >Dr. med.</option>
							<option style="color:#000" value="Prof. " <?php if($_POST['titel']=="Prof. "){ echo "selected";}?> >Prof.</option>
							<option style="color:#000" value="Prof. Dr. " <?php if($_POST['titel']=="Prof. Dr. "){ echo "selected";}?> >Prof. Dr.</option>
							<option style="color:#000" value="Prof. Dr. med. " <?php if($_POST['titel']=="Prof. Dr. med. "){ echo "selected";}?> >Prof. Dr. med.</option>
						</select>
						<script>if(0!=document.getElementsByName('titel')[0].selectedIndex){ document.getElementsByName('titel')[0].setAttribute('class', 'field'); }</script>
    </div>
  </div>





   <div class="row">
    <label class="col-sm-3 control-label">Vorname: *</label>
    <div class="col-sm-2"><?php if ($fehler["vorname"] != "") { echo $fehler["vorname"]; } ?>
      <input type="text" name="vorname" class="field"  placeholder="Vorname" value="<?php echo $_POST[vorname]; ?>" maxlength="<?php echo $zeichenlaenge_vorname; ?>" />
    </div>



     <label class="col-sm-4 control-label">Nachname: *</label>
    <div class="col-sm-2"><?php if ($fehler["name"] != "") { echo $fehler["name"]; } ?>
      <input type="text" name="name" class="field" placeholder="Nachname" value="<?php echo $_POST[name]; ?>" maxlength="<?php echo $zeichenlaenge_name; ?>" />
    </div>
  </div>



  <div class="row">
    <label class="col-sm-3 control-label">E-Mail: *</label>
    <div class="col-sm-2"><?php if ($fehler["email"] != "") { echo $fehler["email"]; } ?>
      <input type="text" name="email" class="field" placeholder="E-Mail" value="<?php echo $_POST[email]; ?>" maxlength="<?php echo $zeichenlaenge_email; ?>" />
    </div>


    <label class="col-sm-4 control-label">Telefon:</label>
    <div class="col-sm-2">
      <input type="text" name="telefon" class="field" placeholder="Telefon/Mobil" value="<?php echo $_POST[telefon]; ?>" maxlength="<?php echo $zeichenlaenge_telefon; ?>" />
    </div>
  </div>

   <div class="row">
<label class="col-sm-3 control-label">Betreff: *</label>
    <div class="col-sm-8"><?php if ($fehler["betreff"] != "") { echo $fehler["betreff"]; } ?>
     <input type="text" name="betreff" class="field" placeholder="Betreff" value="<?php echo $_POST[betreff]; ?>" maxlength="<?php echo $zeichenlaenge_betreff; ?>" />
    </div>
  </div>




    <div class="row">
    <label class="col-sm-3 control-label" style="align-self: center;">Nachricht: *</label>
    <div class="col-sm-8"><?php if ($fehler["nachricht"] != "") { echo $fehler["nachricht"]; } ?>
       <textarea name="nachricht" class="field" rows="5" placeholder="Nachricht" style="height:100%;width:100%;"><?php echo $_POST[nachricht]; ?></textarea>
    </div>
  </div>





  	<?php
			if(0<$cfg['NUM_ATTACHMENT_FIELDS']){
				echo ' <div class="row">';
				echo '<label class="col-sm-3 control-label">Dateiupload:</label>';
				  for ($i=0; $i < $cfg['NUM_ATTACHMENT_FIELDS']; $i++) {
				  echo '<div class="col-sm-8"><input style="height:30px;width:109%;" type="file" size=12 name="f[]"/></div>';
				  }
			   echo '</div>';
			}
			?>




 








    <div class="row">
    <label class="col-sm-3 control-label">Spamschutz:</label>
    <div class="col-sm-8">
   <div style="width:90%;height:1.7em;"><?php echo $q[1]; ?></div> <input type="hidden" name="q_id" value="<?php echo $q[0]; ?>"/>
    </div>
  </div>

  <div class="row" id="answer">
    <label class="col-sm-3 control-label">Antwort: *</label>
    <div class="col-sm-8">
   <input placeholder="Bitte Aufgabe lösen." type="text" class="field<?php if ($fehler["q_id12"] != "") { echo ' errordesignfields'; } ?>" name="q"/><?php if ($fehler["q_id12"] != "") { echo $fehler["q_id12"]; } ?>
    </div>
  </div>



 <?php for ($i=0; $i < $cfg['DATENSCHUTZ_ERKLAERUNG']; $i++) {

include 'code.php'; } ?>



  <div class="row" id="send">
  <div class="col-sm-4 col-sm-offset-3">
     <br /> <b>Hinweis:</b> Felder mit <span class="pflichtfeld">*</span> müssen ausgefüllt werden.
      <br />
     <br />
  <input type="submit" class="senden" name="kf-km" value="Anfrage senden" onclick="tescht();"/>
  
  
  <div>
  <!-- Dieser Copyrighthinweis darf NICHT entfernt werden. --><br /><br /><br /><a href="../index.html" title="kontaktformular.com" style="text-decoration: none;color:#000000;font-size:13px;" target="_blank">&copy; by ekido-productions.ch - Alle Rechte vorbehalten.</a>
  </div>
  
  </div>
  </div>

</form>
</div>
</body>
</html>
