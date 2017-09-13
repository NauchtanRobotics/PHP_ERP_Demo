<?php
ob_start();
session_start();

ini_set('display_errors', 'On');
error_reporting(E_ALL);

ini_set('memory_limit', '128M');

include("header.php");
include("heading_title.php");

?>
<style>
  body .modal-dialog {
  width: 80%; /* desired relative width */
  /*left: 45%; /* (100%-width)/2 */
  /* place center */
  margin-left:auto;
  margin-right:auto; 
}
</style>

<!-- script type = "text/javascript"  src = "/jquery/jquery-3.1.1.js"></script -->
<script type = "text/javascript" 
         src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!--script type = "text/javascript" src = "adapter.js"></script-->
<link rel="stylesheet" href="videocapture.css">
<script language ="javascript">
 var eventSchedule;  // 
 var stopwatchCount = 0.0; // add to this the value of accumulated_min from the db.
 var serial;
 var step;
 var prod;
 
 function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    s = Math.round(s);
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}

$(document).ready(function() {

    var height = $(window).height();
    var width = $(window).width();
    $.post('window_size_script.php', { width: width, height: height, recordSize: 'true' }, function(data, status) {
        //alert("Got here. Data: " + data + "\nStatus: " + status);
        if(status !== 'success') {         
            alert('Unable to let PHP know what the screen resolution is.');
        }
    });
//});

    if ($("#status").length) { //recording_bar didn't work if ( $( "#myDiv" ).length ) 
         
         $("#duration").html("0");
         $("#goal").html( secondsTimeSpanToHMS($("#goal").html()*60));
         serial = $('#serial').val();//document.getElementById('age').value;
         step   = $('#step').val();//document.getElementById('age').value;
         prod   = $('#prod').val();// document.getElementById('age').value;
   // alert("Values are: "+serial+" "+step + " " + prod);

        $.post("assembly_restart.php",
        {
            serial: serial,
            step: step
        },
        function(data, status){
            //alert("Data: " + data + "\nStatus: " + status);
            if (status === "success"){
                eventSchedule = setInterval(myTimer, 1000);
                $("#status").html("Recording Duration");
                //alert(data);
                stopwatchCount = parseFloat(data); // already converted to seconds 
            }
            else
                stopwatchCount = "fail";
        });
    }
    else
        clearInterval(eventSchedule);
    
   /* $("#recording_bar > img").mouseenter(function () {
            $( this ).fadeOut( 100 );
            $( this ).fadeIn( 500 );  }
        );*/
    
    $("#recording_bar > img").mouseenter(function () {
            $( this ).fadeTo( "medium", 0.7 );  }
        ).mouseleave(function() {  $( this ).css( "opacity", 1 ) } );
            
   $("#recording_bar > img").click(function () {
            $( this ).fadeTo( 200, 0.2, function() {
      $( this ).css( "opacity", 1 ) } );  });
            
   $("#pause").click(function(){
       $( this ).fadeTo( "medium", 0.7 );
        var serial = $('#serial').val();//document.getElementById('age').value;
        var step   = $('#step').val();//document.getElementById('age').value;
        var prod   = $('#prod').val();// document.getElementById('age').value;
   // alert("Values are: "+serial+" "+step + " " + prod);
        
        $("#pause").attr('src',"pause_red_28x28.jpg");
        $("#restart").attr('src',"record_grey_28x28.jpg");
        
        $.post("assembly_pause.php",
        {
            serial: serial,
            step: step
        },
        function(data, status){
            alert("Data: " + data + "\nStatus: " + status);
            if (status === "success") {
                clearInterval(eventSchedule);
                $("#status").html("Recording Paused");
            }
        });
    });
    
    $("#restart").click(function(){
        serial = $('#serial').val();//document.getElementById('age').value;
        step   = $('#step').val();//document.getElementById('age').value;
        prod   = $('#prod').val();// document.getElementById('age').value;
        // alert("Values are: "+serial+" "+step + " " + prod);
        $("#restart").attr('src',"record_red_28x28.jpg");
        $("#pause").attr('src',"pause_grey_28x28.jpg");
        $.post("assembly_restart.php",
        {
            serial: serial,
            step: step
        },
        function(data, status){
            //alert("Data: " + data + "\nStatus: " + status);
            if (status === "success"){
                eventSchedule = setInterval(myTimer, 1000);
                $("#status").html("Recording Duration");
                //alert(data);
                stopwatchCount = parseFloat(data); // already converted to seconds 
            }
            else
                stopwatchCount = "fail";
        });
    });
    
	
});

</script>
   
 
    <div class='trademark'>
        <table>
            <?php if( isset($_SESSION['user']) ) { echo "<tr><td colspan = '3' style='width: 650px; text-align: right;'>Welcome ".$_SESSION['name'].", ".$_SESSION['bizname']."</td></tr>"; } ?>
            <tr>
                <td style="width: 75%; text-align: left;"><h4>Assembly Procedure</h4></td>
                <td style="width: 75px;"><h5><a style="border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;" href="flowchart.php">Home</a></h5></td>           
                <td style="width: 75px;"><?php if( !isset($_SESSION['user']) ) { echo "<a style='border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;' href='index.php'>Login</a>";} else { echo "<a style='border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;' href='logout.php?logout'>Logout</a>";} ?></td>           
            </tr>
        </table>       
    </div>
      
    <div class="todo">
    
    

<?php

#session_start();
#set_include_path 
#define( 'ROOT_DIR', dirname(__FILE__) );
set_include_path("./includes/");

if( isset($_SESSION['user']) ) {
include("config.php");
define('DB_DATABASE', $_SESSION['dbname']);

$link = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
}
else {
    exit;
}
$prod = $serial = "";
if (isset($_GET['product_id']) ) {
    $prod = test_input($_GET["product_id"]);
}
if (isset($_GET['serial_num']) ) {
    $serial = test_input($_GET["serial_num"]);
}
if (isset($_GET['phase']) ) {
    $phase = test_input($_GET["phase"]);
    if ($phase!=="c"){
        $phase = "a";
    }
} else { 
    $phase = "a";
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
 if(isset($_POST["diveIntoStep"])){ /////////// LAYOUT FORM2 /// a small table focussing on a single step ///////
    unset($_POST["diveIntoStep"]);
     // Create form2 for the selected item.
    $step = $_POST['stepnum'];
    $step2 = $step[0];
    $prod = $_POST['product_id'];
    $serial = $_POST['serial_num'];
    
   /* if (!onStart($serial, $step2, $link) ){
        echo "Failed to record start-time<br>";
        loadForm1FullProcedure($prod, $serial, $link); 
    } else {*/ //Now done via javascript
         loadStep($serial,$step2,$prod,$link);        

 } // end if(isset($_POST["diveIntoStep"]))

 else if (isset($_POST["form2"])) {
     
     $serial = intval(test_input($_POST['serial']));
     $step = intval(test_input($_POST['step']));
     $prod = intval(test_input($_POST['prod']));
     //echo "Next Step is: ".whatStepNext($step,$serial,$link)."<br>";
     //echo "Form 2 was posted! Value = ".$_POST['form2']."<br>";
     switch(test_input($_POST['form2'])){
         case 'restart': // can also function as start
             //onStart($serial, $step, $link); // RESTART IS NOW DONE VIA JQUERY AJAX and the file assembly_pause.php
             //loadForm2($serial,$step,$prod,$link);
             break;
         case 'pause':
             //if ( onPause($serial, $step, $link) )  // PAUSE IS NOW DONE VIA JQUERY AJAX and the file assembly_pause.php
                //clearLastStartTime($serial, $step, $link);  
             //loadForm2($serial,$step,$prod,$link);
             break;         
         case 'finish':           
             if(onFinish($serial, $step, $link)){  // INSERT UPDATE SET last_stop = now(); then UPDATE SET accumulated = accumulation + last_stop - last_start
                echo "Step completed<br>";
                loadForm1FullProcedure($prod, $serial, $link, $phase); 
             } else { 
                 echo "Failed to record finish time for step# $step<br>";               
             }
             break;    
         case 'next':
             if(onFinish($serial, $step, $link)){  // INSERT UPDATE SET last_stop = now(); then UPDATE SET accumulated = accumulation + last_stop - last_start
                echo "Step $step completed. ";//.$step2 = whatStepNext($step,$serial,$link);
                
                // if step2 is > next_step, that means a step in between has already been done.
                // if step2 is < next_step, some steps have been removed from the assembly procedure, so check whatStepNext again
                $isStepStillValid = FALSE;
                $MAX_TRIES = 20;
                $count_tries = 0;
                while (  !$isStepStillValid && $count_tries < $MAX_TRIES ){
                    $step2 = whatStepNext( $step, $serial, $link, $phase );
                    $isStepStillValid = checkStepStillExistsInAssProcedureExists( $prod, intval($step2), $link );
                    $count_tries++;
                    $step = $step2;
                }
                echo " $step2<br>";
                if (!empty($step2)) {
                    onStart($serial, $step2, $link);
                    loadStep($serial,$step2,$prod,$link);
                } else{
                    echo "End of procedure was reached.<br>";
                    // Now check if any steps were skipped.
                    if (checkForStepsNotCompleted($serial, $link)){
                        echo "There are skipped steps to complete<br>";
                    } else {
                        echo "All steps seem to be complete<br>";
                        createBtnSubmitForQualityChecking($serial, $link);
                    }
                    // provide link to do skipped steps.
                    // if no steps skipped, provide button to submit product for quality checking, where closed is changed to ++1
                    
                }
             } else { 
                 echo "Failed to record finish time for step# $step<br>";               
             }
             break;
         case 'cancel':
             onCancel($serial, $step, $link);
             loadForm1FullProcedure($prod, $serial, $link, $phase); //
             break;
         default:
             loadForm1FullProcedure($prod, $serial, $link, $phase); 
     } // end switch
     //unset ($_POST["form2"]);
   } // end else if (isset($_POST["form2"]))
   else if (isset($_POST['mark_completed'])){
       $serial = intval(test_input($_POST['serial']));
        actionOnMarkedCompleted($serial, $link);    
    }
 } // end if ($_SERVER["REQUEST_METHOD"] == "POST")
 
 
 else {  // Nothing has been posted from this page yet.
     onScaffoldLog($serial, $prod, $link, $phase);
     loadForm1FullProcedure($prod, $serial, $link, $phase);     

 } // end else /!if ($_SERVER["REQUEST_METHOD"] == "POST") {

 function onScaffoldLog($serial, $prod, $link, $phase) { // Advantage of scaffolding out the buildlog at the beginning of each job is being able to show the % complete in diveIntoStep. 
     
// 1. Select all of the steps from the assembly procedure for the selected product
     $sql  = "SELECT step_num ";
     $sql .= "FROM ass_procedures_tbl ";
     $sql .= "WHERE prod_id = ? ";
     $sql .= "AND phase = ? ";
     $sql .= "ORDER BY step_num";
       
    //onScaffoldLog Part Level 1
    if (!$stmt = $link->prepare($sql) ) {
       die('prepare() 938 failed: ' . htmlspecialchars($link->error)." in onScaffoldLog Level 1.<br>");  
    }
    $rc = $stmt->bind_param("is", $prod, $phase );           
    if ( false === $rc ){
       die('bind() 938 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 938 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                                                                     
    $stmt->bind_result( $step  );



    //2. Loop through all steps from procedure template for this product and build all of the rows in the buildlog not already existing
    while ($stmt->fetch()) { 
      //2a check if the step has already been logged for this serial
        //$sql2 = "SELECT count(*) FROM buildlog_tbl WHERE serial_num = $serial AND step_num = $step;";
         $sql2 = "SELECT count(*) FROM buildlog_tbl WHERE serial_num = ? AND step_num = ?";
         if (!$stmt2 = $link->prepare($sql2) ) { 
            die('prepare() 958 failed: ' . htmlspecialchars($link->error));  
         }
         $rc2 = $stmt2->bind_param("ii", $serial, $step );           
         if ( false === $rc2 ){
            die('bind() 958 failed: ' . htmlspecialchars($stmt2->error));
         }
         $rc2 = $stmt2->execute();    
         if ( false === $rc2 ) {
            die('execute() 958 failed: ' . htmlspecialchars($stmt2->error)); 
         }
        $stmt2->store_result();
        $num_of_rows = $stmt2->num_rows;
                                                                     
        $stmt2->bind_result( $count );
        $stmt2->fetch();
        
        if ( $count == 0 ){
            //echo "yes, didn't find step num $step in result2<br>";
            $sql3 ="INSERT INTO buildlog_tbl (serial_num, step_num) VALUES (?, ?)";
            if (!$stmt3 = $link->prepare($sql3) ) {
                 die('prepare() 959 failed: ' . htmlspecialchars($link->error));  
            }
            $rc3 = $stmt3->bind_param("ii", $serial, $step);  
            if ( false === $rc3 ){
                die('bind() 959 failed: ' . htmlspecialchars($stmt3->error));
            }
            $rc3 = $stmt3->execute();    
            if ( false === $rc3 ) {
                die('execute() 959 failed: ' . htmlspecialchars($stmt3->error)); 
            }
           
            $stmt3 -> close();
        } /*else {
            echo "$step already in the buildlog<br>";
        }  */    
        $stmt2->free_result();
        $stmt2->close();
     }// end while - stop looping through looking for steps that need to be inserted into build_log
     /* free results */
     $stmt->free_result();

     $stmt->close();
     return TRUE; // Success at inserting the record?
 }

 
 function onStart($serial, $step, $link) {
     //$sql1 = "SELECT count(first_start) FROM buildlog_tbl WHERE serial_num = $serial AND step_num = $step;";
    $sql = "SELECT count(first_start) FROM buildlog_tbl WHERE serial_num = ? AND step_num = ?";
    if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 918 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("ii", $serial, $step);           
    if ( false === $rc ){
       die('bind() 918 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 918 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                                                                     
    $stmt->bind_result( $count  );
    $stmt->fetch();
    $stmt->close();
     
     
     if ($count == 0 )
     {
        $sql = "UPDATE buildlog_tbl SET first_start = now(), last_start = now() WHERE serial_num = ? and step_num = ?";
        if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("ii", $serial, $step);           
            $success = $stmt->execute();    
            if(!$success) echo "Step # $step failed to have start time saved in onStart(2a) because ".$stmt->error." <br>"; 
        }
        else {
            echo "Step # $step failed to have start time saved in onStart(2b) because ".$stmt->error." <br>"; 
            
        }
        
        $stmt->close();
        return $success;
     } else {
         onRestart($serial, $step, $link);
     }
     return true;
 }
 
  
 function onRestart($serial, $step, $link){
     // 1. check whether there is a value in last_stop and last_start.
     // If last_start IS NULL and last_stop IS NOT NULL, then go ahead and UPDATE buildlog_tbl SET last_start = IF (..., now(),) WHERE serial_num...
             // UPDATE SET last_start = now() WHERE last_start IS NULL  ;
             // UPDATE SET first_start = last_start WHERE first_start IS NULL  
             // TODO: UPDATE SET last_stop = null;
     $sql ="UPDATE buildlog_tbl SET last_start = IF( (last_start IS NULL), now(), last_start), last_stop = IF( last_stop IS NOT NULL, null, last_stop) WHERE serial_num = ? AND step_num = ?";
     if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("ii", $serial, $step);           
            $success = $stmt->execute();  
            if (!$success) echo "Error message in onRestart Part 1a: ".$stmt->error." .<br>";
            //echo "Step # $step done <br>";
        }
        else {
            echo "Error message in onRestart Part 1b: ".$stmt->error." .<br>";
            return false;
        }
        
        $stmt->close();
        return $success;
 }
 
 function onPause($serial, $step, $link) {
     // 1. UPDATE SET last_stop = now()
     $sql = "UPDATE buildlog_tbl SET last_stop = now() WHERE serial_num = ? AND step_num = ?";
     if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("ii", $serial, $step);           
            $success = $stmt->execute();    
            //echo "Step # $step done <br>";
            if (!$success) echo "Error message: ".$stmt->error()." in onPause Part 1a.<br>"; 
    }
    else {            
       echo "Error message: ".$stmt->error()." in onPause Part 1b.<br>"; 
       return false;
    } 
    $stmt->close();
         
    // 2. UPDATE SET accumulated_min = TIMEDIFFERENCE(SECOND, last_start, last_stop)/60 - SAME AS FOR FINISHED (BUT RETURNS TO FORM2 NOT FORM1
    $sql = "UPDATE buildlog_tbl SET accumulated_min = IF(accumulated_min IS NULL, TIMESTAMPDIFF(SECOND,last_start, last_stop)/60, accumulated_min + TIMESTAMPDIFF(SECOND,last_start, last_stop)/60) WHERE serial_num = ? AND step_num = ?";
            if ($stmt = $link->prepare($sql) ) {           
                $stmt->bind_param("ii", $serial, $step);           
                $success = $stmt->execute();    
                if (!$success) echo "Failed to update accumulated time (onPause Part 2a) because ".$stmt->error()."<br>";
                //echo "Step # $step done <br>";
            }
            else {
                $success = false;
                echo "Error message: ".$stmt->error()." in onPause Part 2b.<br>"; 
            }
        
            $stmt->close();
                      
    return $success;
 }   
 
 function clearLastStartTime($serial, $step, $link){
             
    // 1. Null the last_start
          $sql = "UPDATE buildlog_tbl SET last_start = null WHERE serial_num = ? AND step_num = ?";
            if ($stmt = $link->prepare($sql) ) {           
                $stmt->bind_param("ii", $serial, $step);           
                $success = $stmt->execute();    
                if (!$success) echo "In clearLastStartTime, failed to null the last start time because ".$stmt->error()."<br>";
                //echo "Step # $step done <br>";
            }
            else {
                $success = false;
                echo "Error message in clearLastStartTime: ".$stmt->error()."<br>"; 
           
            }
        
            $stmt->close();
    }
 
 function reverseCompleted($serial, $step, $link) {
 // 1. Reverse any completed
          $sql = "UPDATE buildlog_tbl SET completed = 0 WHERE serial_num = ? AND step_num = ?";
            if ($stmt = $link->prepare($sql) ) {           
                $stmt->bind_param("ii", $serial, $step);           
                $success = $stmt->execute();    
                if (!$success) echo "Failed to null 'completed' (in reverseCompleted) because ".$stmt->error()."<br>";
                //echo "Step # $step done <br>";
            }
            else {
                $success = false;
                echo "Error message: ".$stmt->error()." in reverseCompleted<br>"; 
            }
            
        
            $stmt->close();
     return $success;
 }
 
 function onCancel($serial, $step, $link) {
     // if accumulated time is null or <0.5,  UPDATE SET first_start = null; 
     // else if accumulated time IS NOT NULL  AND accumulation >= 0.5, then UPDATE SET last_start = null
     clearLastStartTime($serial, $step, $link);
     reverseCompleted($serial, $step, $link);
     return;
 }   
 
 function onFinish($serial, $step, $link){
     // 1. Record last_stop and calculate time difference accumulated
     $success = onPause($serial, $step, $link);     
    
     // 2. UPDATE SET completed because step is Finished!
     $sql = "UPDATE buildlog_tbl SET completed = 1 WHERE serial_num = ? AND step_num = ?";
     if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("ii", $serial, $step);           
            $success1 = $stmt->execute();    
            //echo "Step # $step done <br>";
            if (!$success) echo "Error message in onFinish: ".$stmt->error()."<br>";
     }
     else {            
         echo "Error message in onFinish: ".$stmt->error()."<br>";
         return false;
     }
     $stmt->close();  
     return ($success && $success1);
 }

 function checkStepStillExistsInAssProcedureExists( $prod, $step, $link ){
    $sql = "SELECT id FROM ass_procedures_tbl WHERE step_num = ? AND prod_id = ? LIMIT 1";
    if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 028 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("ii", $step, $prod );           
    if ( false === $rc ){
       die('bind() 028 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 028 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
    if($num_of_rows > 0){
                       //b.description, a.extra_comment, b.critical_issues, a.using_tools, b.goal_dur_minutes, b.image_blob                                           
        $stmt->bind_result( $nextStep );
        $stmt->fetch();
        return TRUE;
    } else {
      return FALSE;  
    }
 }
 
 function whatStepNext($step, $serial, $link, $phase){
     
     $sql  = "SELECT step_num ";
     $sql .= "FROM buildlog_tbl ";
     $sql .= "WHERE serial_num = ? ";
     $sql .= "AND phase = ? ";
     $sql .= "AND step_num > ? ";
     $sql .= "AND completed != 1 ";
     $sql .= "ORDER BY step_num LIMIT 1";
     
     if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("isi", $serial, $phase, $step);           
            $success = $stmt->execute();    
            if (!$success) {
                echo "Error message in whatNextStep: ".$stmt->error()."<br>";
            }
            $stmt->bind_result($nextStep);
    }
    else {            
            echo "Error message: ".$stmt->error()."<br>"; 
            return false;
    }
    $stmt->fetch();
    $stmt->close(); 
    return ($nextStep);      
    
 }
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////// loadForm1 - full procedure //////////////////////
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
function loadForm1FullProcedure($prod, $serial, $link, $phase){
 // Initialise loop variables
$line_item = 1;

    $sql = "SELECT content FROM product_blobs_tbl WHERE prod_id = ? ORDER BY upload_date desc LIMIT 1";
    if (!$stmt = $link->prepare($sql) ) { 
        die('prepare() 528 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("i", $prod );           
    if ( false === $rc ){
       die('bind() 528 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 528 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
    if($num_of_rows > 0){
                       //b.description, a.extra_comment, b.critical_issues, a.using_tools, b.goal_dur_minutes, b.image_blob                                           
        $stmt->bind_result( $image_blob );
        $stmt->fetch();

        if ($_SESSION['screen_width']>0){
            $desired_width = floor(intval($_SESSION['screen_width'])* 0.7); // 200;
        } else {
            $desired_width = 300;
        }
        //echo "Desired width is $desired_width px<br>";
        $source_image   = imagecreatefromstring($image_blob);
        $width          = imagesx($source_image);
        $height         = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        ob_start();
        // generate the byte stream
        imagejpeg($virtual_image, NULL, 100);
        $type =  "JPEG";
        // and finally retrieve the byte stream
        $rawImageBytes = ob_get_clean();
        
        echo "<img style='max-width:100%;height:auto;' src='data:$type;base64,".base64_encode($rawImageBytes )."'><br><br>"; //$image_blob    
    } else { 
        echo "No product image found<br>";
    }
    $stmt->close();


$sql = "SELECT product_title FROM products_tbl WHERE product_id = ?";
if ($stmt = $link->prepare($sql) ) {           
            $stmt->bind_param("i", $prod);           
            $success1 = $stmt->execute();    
            $stmt->bind_result($prodName);
}
else {            
            echo "Prod_id = $prod could not be identified <br>"; 
            return false;
}
$stmt->fetch();
echo "Serial# 000$serial; Product Name: $prodName; Product ID = $prod<br>";
$stmt->close();

$sql1  = "SELECT sum(b.goal_dur_minutes) ";
$sql1 .= "FROM ass_procedures_tbl a, base_procedure_tbl b ";
$sql1 .= "WHERE a.sop_id = b.id  ";
$sql1 .= "AND   a.prod_id = ?";

if ($stmt = $link->prepare($sql1) ) {           
            $stmt->bind_param("i", $prod);           
            $success1 = $stmt->execute();    
            $stmt->bind_result($totalMinutesGoal);
}
else {            
            echo "Error message: ".$stmt->error()."<br>"; 
            return false;
}
$stmt->fetch();
echo "Total budgetted time (goal) is ".sprintf('%.1f',$totalMinutesGoal)." min; "; 
$stmt->close();
$sql2 = "SELECT sum(accumulated_min) FROM buildlog_tbl WHERE serial_num = ?";
if ($stmt = $link->prepare($sql2) ) {           
            $stmt->bind_param("i", $serial);           
            $success1 = $stmt->execute();    
            $stmt->bind_result($totalMinutesSoFar);
}
else {            
            echo "Error message: ".$stmt->error()."<br>"; 
            return false;
}
$stmt->fetch();
echo "consumed so far = ".sprintf('%.1f',$totalMinutesSoFar)." min;  <br>";
$stmt->close();

$sql =  "SELECT sum(b.goal_dur_minutes) ";
$sql .= "FROM ass_procedures_tbl a, base_procedure_tbl b, buildlog_tbl c ";
$sql .= "WHERE a.sop_id = b.id ";
$sql .= "AND a.prod_id = ? ";
$sql .= "AND a.phase = ? ";
$sql .= "AND c.serial_num = ? ";
$sql .= "AND c.step_num = a.step_num ";
$sql .= "AND c.completed = 1";

if ($stmt = $link->prepare($sql) ) {           
    $stmt->bind_param("isi", $prod, $phase, $serial);           
    $success1 = $stmt->execute();    
    $stmt->bind_result($budgetSoFar);
} else {            
    echo "Error message: ".$stmt->error()."<br>"; 
    return false;
}
$stmt->fetch();
if ( $totalMinutesSoFar > 0 ) {
    echo "Variance for items completed so far = ".(!empty($budgetSoFar)?sprintf('%.1f',(($totalMinutesSoFar-$budgetSoFar)*100.0/$budgetSoFar)):"")."%";
}
echo  "<br><br>";
$stmt->close();

////////////////////////////////////////// now the display part of loadForm1 ////////////////////////////////////////
//$sql = "SELECT a.step_num, b.description, c.completed, c.accumulated_min FROM ass_procedures_tbl a, base_procedure_tbl b, buildlog_tbl c WHERE b.id = a.sop_id AND a.prod_id = $prod AND c.serial_num = $serial AND a.step_num = c.step_num ORDER BY a.step_num;";
    $sql =  "SELECT a.step_num, a.part_num, b.description, c.completed, c.accumulated_min, d.part_description ";
    $sql .= "FROM ass_procedures_tbl a ";
    $sql .= "JOIN base_procedure_tbl b ON b.id = a.sop_id ";
    $sql .= "JOIN buildlog_tbl c ON a.step_num = c.step_num ";
    $sql .= "LEFT JOIN Parts_tbl d ON d.part_id = a.part_num ";
    $sql .= "WHERE a.prod_id = ? ";
    $sql .= "AND a.phase = ? ";
    $sql .= "AND c.serial_num = ? ";
    $sql .= "ORDER BY step_num";
    
    /*SELECT a.step_num, a.part_num, b.description, c.completed, c.accumulated_min, d.part_description
FROM ass_procedures_tbl a
JOIN base_procedure_tbl b ON b.id = a.sop_id
JOIN buildlog_tbl c ON a.step_num = c.step_num
LEFT JOIN Parts_tbl d ON d.part_id = a.part_num
WHERE a.prod_id = 27 AND c.serial_num = 59 
ORDER BY step_num*/
    if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 938 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("isi", $prod, $phase, $serial );           
    if ( false === $rc ){
       die('bind() 938 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 938 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                        //a.step_num, b.description, c.completed, c.accumulated_min                                             
    $stmt->bind_result( $step_num, $part_num, $description, $completed, $accumulated_min, $partTitle  );
    $doneOnce = false;
    while ( $stmt->fetch() ){
        if (!$doneOnce){
            echo "<form  action = '".$_SERVER['PHP_SELF']."' method='post'>";	
            echo "<input class='subbtn' type='submit' id='form1' name='diveIntoStep' value=' Start Selected Step '><br><br>";
            echo "<input type='hidden' name='serial_num' value ='$serial'>";
            echo "<input type='hidden' name='product_id' value ='$prod'>";
            echo "<table  border='1'><tr>";
            echo "<th width='40px'>Do</th><th width='40px'>Step</th><th width='300px' align='left'>Description </th><th width='50px'>Part#</th><th width='55px'>Progress</th><th width='55px'>Clocked (min)</th></tr> "; //<th width='65px'>Distinction</th><th width='160px'>Critical Issues</th><th width='65px'>Tool</th>
            $doneOnce = true;
        }
			
	// Start a new row
        //$step_num, $description, $completed, $accumulated_min
	echo "<tr>";
        // identify tags for JS using a line item number
        echo "<td><input type='radio' id='row$line_item' name='stepnum[]' value ='".$step_num."' /></td>"; 
        //echo "<td style='padding: 5px 10px 5px 5px;'><strong>".$line_item.".</strong></td>";
       
        echo "<td style='padding: 5px 10px 5px 5px;'><strong>".$step_num.".</strong></td>";

	// Now Write data from the received fabricating_list_tbl into the remaining columns
	echo"<td style='text-align: left; padding: 5px 10px 5px 5px;'>".substr($description,0,35)." ... </td>";
	
        echo "<td style='padding: 5px 10px 5px 5px;'>".(!empty($part_num)?"<button type='button' class='btn btn-info btn-xs' data-toggle='modal' data-target='#myModal$part_num'>$part_num</button>":"")."</td>";
        if (!empty($part_num)){
        ?>
<!-- Modal -->
  <div class="modal fade" id="myModal<?php echo "$part_num";?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><a target='_blank' href='addpart.php?partnum=<?php echo $part_num;?>'><?php echo "Part#: $part_num, $partTitle";?></a></h4>
	</div>
      </div>
      
    </div>
  </div>
        <?php }
       
        if ($completed == 1){
           echo "<td id='imagerow$line_item'>100%</td>";
        } else {
               echo "<td id='imagerow$line_item'></td>";
        }
        
        if (!empty($accumulated_min)){
              echo "<td>".sprintf('%.1f',$accumulated_min)."</td>"; 
        } else {
              echo "<td>&nbsp;</td>";
        }
			
        echo "</tr>";

        $line_item += 1;
                        
   } // end while fetch loop
                
   if ( $line_item > 0 ){
          echo "</table>";
           echo "</form>";
   } else {
        echo "No assembly procedure found for this product.<br>";
   }

   $stmt->close();

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////// loadForm2 - focus on one step ////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function loadStep($serial, $step, $prod, $link) {
    
    echo "Build of product number $prod, Serial# 000$serial.<br>";
    echo "Step number: <strong>$step</strong><br><br>"; 
    
  //$sql = "SELECT b.description, a.extra_comment, b.critical_issues, a.using_tools, b.goal_dur_minutes, b.image_blob FROM ass_procedures_tbl a, base_procedure_tbl b WHERE b.id = a.sop_id AND a.step_num = $step AND a.prod_id = $prod;";
    $sql  = "SELECT b.description, a.part_num, a.extra_comment, b.critical_issues, a.using_tools, b.goal_dur_minutes, a.image_blob ";
    $sql .= "FROM ass_procedures_tbl a, base_procedure_tbl b ";
    $sql .= "WHERE b.id = a.sop_id ";
    $sql .= "AND a.step_num = ? ";
    $sql .= "AND a.prod_id = ? LIMIT 1";
    
    if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 928 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("ii", $step, $prod );           
    if ( false === $rc ){
       die('bind() 928 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 928 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                       //b.description, a.extra_comment, b.critical_issues, a.using_tools, b.goal_dur_minutes, b.image_blob                                           
    $stmt->bind_result( $description, $part_num, $extra_comment, $critical_issues, $using_tools, $goal_dur_minutes, $image_blob  );
    $stmt->fetch();
    $stmt->close();
    if ($_SESSION['screen_width']>0){
        $desired_width = floor(intval($_SESSION['screen_width'])* 0.7); // 200;
    } else {
        $desired_width = 300;
    }
    //echo "Desired width is $desired_width px..<br>";
    if(!empty($image_blob)){
        try {
            $source_image   = imagecreatefromstring($image_blob);
            if(!$source_image) {
                echo "Blob returned FALSE image<br>";
            }
        } catch(Exception $e) {
            echo "David's Message: " .$e->getMessage();
        }

        $width          = imagesx($source_image);
        //echo "Width = $width<br>";
        $height         = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width)); 
        //echo "Source height is $height and Desired height is $desired_height px<br>";
        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    } else {
        $virtual_image = "";
    }
        echo "<form id='form2' action = '".$_SERVER['PHP_SELF']."' method='post'>";	           
            echo "<input type='hidden' id='prod'   name='prod' value ='$prod'>";
	    echo "<input type='hidden' id='serial' name='serial' value ='$serial'>";
            echo "<input type='hidden' id='step'   name='step' value ='".$step."'>";
            echo "<table  border='1'><tr>";
	    echo "<th width='100px' align='left'>Description </th><th width='65px'>Distinction</th><th width='100px'>Critical Issues</th><th width='65px'>Tool</th><th width='55px'>Duration</th></tr> "; 
	    echo "<tr>";
            //$description, $extra_comment, $critical_issues, $using_tools, $goal_dur_minutes, $image_blob
            echo"<td style='text-align: left; padding: 10px 10px 10px 10px;'>".$description."</td>";
            //echo"<td style='text-align: left; padding: 10px 10px 10px 10px;'>".$part_num."</td>";
            echo"<td style='text-align: left; padding: 10px 10px 10px 10px;'>".$extra_comment."</td>";
            echo"<td style='text-align: left; padding: 10px 10px 10px 10px;'>".$critical_issues."</td>";
            echo"<td style='text-align: left; padding: 10px 10px 10px 10px;'>".$using_tools."</td>";
            echo"<td style='text-align: center;'><div id='duration'></div>of<br><div id='goal'>".$goal_dur_minutes."</div></td>";
            echo "</tr>";
                echo "<tr><td colspan='6' style='align:left;'>";
                echo "<div id='status' style='align:top; padding-left:5px; padding-right:1px; padding-top:10px; width:100px; max-width:100px; display:inline-block;'></div>";
                
                echo "<div id='recording_bar'>";
                echo "<img id='restart' src='record_red_28x28.jpg' alt='recording time taken' style='vertical-align: top; padding-top: 6px; padding-left:8px; padding-right:8px;' border='0'/>";
                echo "<img id='pause' alt='pause the clock' src='pause_grey_28x28.jpg' style='vertical-align: top; padding-top: 6px; padding-left:8px; padding-right:8px;' border='0'/>";
                echo "<img id='next' onclick='onControlButton(this);' src='green_tick_28x28.png' style='vertical-align: top; padding-top: 6px; padding-right: 40px;' border='0'/>";
                echo "<img id='finish' onclick='onControlButton(this);' src='black_tick_28x28.jpg' style='vertical-align: top; padding-top: 6px; padding-right: 40px;' border='0'/>";
                echo "<img id='cancel' style='vertical-align: top; padding-top: 6px; padding-left: 40px;' onclick='onControlButton(this);' src='cancel_28x28.jpg' border='0'/>";
                echo "</div>"; //echo play, pause, finish, and cancel buttons;
                echo "<br>";
                echo "<a target='_blank' href='addpart.php?partnum=$part_num'>Part# $part_num (info)</a> <a target='_blank' href='inventory.php?partnum=$part_num'>(stock)</a><br>";
                $partimagecss = "width:300px; height:190px; margin:auto;";
                $iframeSRC ="showpartimage.php?partnum=$part_num";
                ?>
                    <iframe id="partimage" src="<?php echo $iframeSRC;?>" style="<?php echo $partimagecss;?>"></iframe>
                <?php
                if(!empty($image_blob)){
                    ob_start();
                    // generate the byte stream
                    imagejpeg($virtual_image, NULL, 100);
                    $type =  "JPEG";
                    // and finally retrieve the byte stream
                    $rawImageBytes = ob_get_clean();
                    echo "<img style='max-width:100%;height:auto;' src='data:$type;base64,".base64_encode($rawImageBytes )."'>"; //$image_blob
                }
	    echo "</td></tr>";
            echo "</table>";
            echo "<div style='display:none;'>";
            echo "<input type='submit' id='finish_input' name='form2' value='finish'>";
            echo "<input type='submit' id='pause_input' name='form2' value='pause'>";
            echo "<input type='submit' id='restart_input' name='form2' value= 'restart'>";
            echo "<input type='submit' id='cancel_input' name='form2' value='cancel'>";
            echo "<input type='submit' id='next_input' name='form2' value='next'>";
            echo "</div>";

}

function  createBtnSubmitForQualityChecking($serial, $link){
    echo "<form  action = '".$_SERVER['PHP_SELF']."' method='post'>";	
    echo "<input type='hidden' name='serial' value =$serial>";
    //echo "<input type='hidden' name='product_id' value ='$prod'>";
    echo "<input class='greybtn' type='submit' name='mark_completed' value='Submit for QC'>";
    echo "</form>";
}

function checkForStepsNotCompleted($serial, $link){
    $sql = "SELECT log_id FROM buildlog_tbl WHERE serial_num = ? AND completed = 0 LIMIT 1";
    if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 1918 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("i", $serial);           
    if ( false === $rc ){
       die('bind() 1918 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 1918 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                                                                     
    $stmt->bind_result( $log_id  );
    $stmt->fetch();
    $stmt->close();
    if(!empty($log_id)){
        return ($log_id);
    } else {
        return FALSE;
    }
}

function actionOnMarkedCompleted($serial, $link){
    // UPDATE RECORD IN assembly_plan_tbl to change number of units 'closed'
    echo "Marking this build as complete<br>";
    
    $sql = "UPDATE assembly_plan_tbl SET closed = 1 WHERE id = ?";
    if (!$stmt = $link->prepare($sql) ) {
        die('prepare() 1959 failed: ' . htmlspecialchars($link->error));  
    }
    $rc = $stmt->bind_param("i", $serial);  
    if ( false === $rc ){
        die('bind() 1959 failed: ' . htmlspecialchars($stmt->error));
    }
    $rc = $stmt->execute();    
    if ( false === $rc ) {
        die('execute() 1959 failed: ' . htmlspecialchars($stmt->error)); 
    }

    $stmt -> close();
    
    echo "<br><a href='assemblytodo.php'>Goto Assembly ToDo List</a>";
}
 ?>
                    <br><br>                                
                    </form>
                    
    <!--video id="gum" width="320" height="240" autoplay="" muted="" src="polyblob:1"></video>
    <video id="recorded" width="320" height="240" autoplay="" loop="" controls="" ></video-->

    <!--div>
      <button id="record" class="greybtn">Start Recording</button>
      <button id="play" class="greybtn">Play</button>
      <button id="download" class="greybtn">Download</button>
    </div-->
   </div>

</body>
<!--script src="https://webrtc.github.io/adapter/adapter-latest.js"></script--> <!-- or can use a local version that I've copied -->
<!--script src="webrtc_main.js"></script-->
<script>
function myTimer() {
    stopwatchCount += 1.0; // new Date();
    $("#duration").first().html(""+secondsTimeSpanToHMS(stopwatchCount) ); // secondsTimeSpanToHMS(125);
}

function onControlButton(e) {  
    
    var str = e.getAttribute('id');
    switch (str ) {
       case 'restart':
           //alert("Restart");
           // then change to red record button and grey pause 
           //document.getElementById("restart_input").click(); // post restart_input
           break;
       case 'pause':
           //alert("Pause");
            // then change to grey record button and  red pause 
           //document.getElementById("pause_input").click(); // post pause_input
           break;
       case 'finish':
           //alert("Are you sure?");
           document.getElementById("finish_input").click(); //post finish_input
           break;
       case 'next':
           document.getElementById("next_input").click(); //post finish_input
           break;
       case 'cancel':
           var fred = confirm("Are you sure that you want to not start this step (for the moment)?");
           if (fred) 
               document.getElementById("cancel_input").click();// post cancel_input
           break;
    }    
    
}

</script>
</html>
<?php 
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

ob_end_flush(); 
?>