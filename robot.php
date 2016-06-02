<html>
 <head>
  <title>Robot</title>
 </head>
 <body>
 <?php 

date_default_timezone_set('Europe/Paris');


 
$today = date('l jS \of F Y h:i:s A');
$alert = -1;
$alert_type =array('No alert','Motion','Lux','Temperature');    

$motor_state      =$_POST["motor_state"];
$SpeedMotorRight  =$_POST["SpeedMotorRight"];
$SpeedMotorLeft   =$_POST["SpeedMotorLeft"];
$TickRight        =$_POST["TickRight"];
$TickLeft         =$_POST["TickLeft"];
$direction        =$_POST["direction"];
$distance         =$_POST["distance"];
$temperature      =$_POST["temperature"];
$brightness       =$_POST["brightness"];
$alert            =(int)$_POST["alert"]; 
$no_picture       =(int)$_POST["no_picture"];


if ($alert >= 0){
    //write to file
    $fp = fopen("robot.txt","a"); // ouverture du fichier en écriture
    fputs($fp, "\n"); // on va a la ligne
    fputs($fp, "$today|$alert_type[$alert]|$no_picture|$motor_state |$SpeedMotorRight|$SpeedMotorLeft|$SpeedMotorRight|$TickRight|$TickLeft|$direction|$distance|$temperature|$brightness|");
    fclose($fp);

    //write to DB
    $con = mysql_connect("localhost","edh","edh");

    if (!$con) {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db("CHIPKIT", $con);
    
    $sth = mysql_query("INSERT into robot_infos (source, alert, no_picture, motor_state, SpeedMotorRight, SpeedMotorLeft, TickRight, TickLeft, direction, distance, temperature, brightness) values (1, '$alert','$no_picture','$motor_state','$SpeedMotorRight','$SpeedMotorLeft','$TickRight','$TickLeft','$direction','$distance','$temperature','$brightness')");    
 
    mysql_close($con);
 
    if ($alert> 0){ // alert
        send_alert();
    }
}

$target_path = $target_path . basename( $_FILES['picture']['name']);
move_uploaded_file($_FILES['picture']['tmp_name'], $target_path);


function send_alert() {
    
    global $today, $alert, $alert_type, $no_picture;
   
    // clé aléatoire de limite
    $boundary = md5(uniqid(microtime(), TRUE));

    $to = 'eric.delahoupliere@free.fr';
    
    $subject = 'Alert '.$alert_type[$alert].' on '.$today;
    
    // Headers
    $headers  = 'From: robot'."\r\n";
    $headers .= 'Mime-Version: 1.0'."\r\n";
    $headers .= 'Content-Type: multipart/mixed;boundary='.$boundary."\r\n";
    $headers .= "\r\n" ;
    
    // Message
    $msg  = 'This is a multipart/mixed message.'."\r\n\r\n";

    $msg .= '--'.$boundary."\r\n"; 
    $msg .= 'Content-type:text/plain;charset=iso-8859-1'."\r\n";
    $msg .= 'Content-Transfer-Encoding:8bit'."\r\n";
	$msg .= "\r\n";
	
    // Pièce jointes 
        $pictfile= 'PICT'.$no_picture.'.jpg'; 

    send_pictfile();

   // $no_picture = $no_picture-1;
   // send_pict();
 
   // $no_picture = $no_picture-1;
   // send_pict();
 
    // Fin
    $msg .= '--'.$boundary.'--'."\r\n";;

    // Function mail()
    mail($to, $subject, $msg, $headers);

} //function send_alert


function send_pictfile() {
    
    global $pictfile, $msg, $boundary;
      
    if (file_exists($pictfile))
    {
	    $file_type = filetype($pictfile);
	    $file_size = filesize($pictfile);

	    $handle = fopen($pictfile, 'r') or die('File '.$pictfile.'can t be open');
	    $content = fread($handle, $file_size);
	    $content = chunk_split(base64_encode($content));
	    $f = fclose($handle);
 
	    $msg .= '--'.$boundary."\r\n";
	    $msg .= 'Content-type:image/jpeg; name='.$pictfile."\r\n";
	    $msg .= 'Content-transfer-encoding:base64'."\r\n";
        $msg .= 'Content-Disposition: attachment; filename='.$pictfile."\r\n"; 
	    $msg .= "\r\n";
	    $msg .= $content."\r\n";	     
    }
    else
    {
        $msg .= 'Error retreiving picture: '.$pictfile."\r\n";
    }

} //function send_pictfile   
	?>
 </body>
</html>
