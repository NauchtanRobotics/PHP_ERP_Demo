<?php
ob_start();
session_start();

ini_set('display_errors', 'On');
error_reporting(E_ALL);

include("heading.php");
?>
    <div class='trademark'>
        <table>
            <?php if( isset($_SESSION['user']) ) { echo "<tr><td colspan = '3' style='width: 650px; text-align: right;'>Welcome ".$_SESSION['name'].", ".$_SESSION['bizname']."</td></tr>"; } ?>
            <tr>
                <td style="width: 600px; text-align: left;"><h4>Shipping ToDo</h4></td>
                <td style="width: 75px;"><h5><a style="border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;" href="flowchart.php">Home</a></h5></td>           
                <td style="width: 75px;"><?php if( !isset($_SESSION['user']) ) { echo "<a style='border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;' href='index.php'>Login</a>";} else { echo "<a style='border-radius: 10px; color: white; padding-left: 10px; padding-right: 10px; background-color: #477201;' href='logout.php?logout'>Logout</a>";} ?></td>           
            </tr>
        </table>       
    </div>
      
    <div class='todo'>
    
    

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

 # It works like this: 
# 1. shippingtodo.php runs a query to show what full and partial orders can be shipped
# 2. Fully ready orders are marked F. Partially ready orders are marked P.
# 3. User selects an order to ship by clicking the link which points at shipping.php?order_num=X

# Objective is to select orders for which ALL or ANY line items are checked and ready to be shipped, but not shipped already.
$sql  = "SELECT * FROM ";
$sql .= "(SELECT b.id AS orderNum, sum(a.r_ord_line) AS count_items, sum(a.checked) AS count_checked, sum(a.shipping_id) AS count_shipped  ";
$sql .= "FROM assembly_plan_tbl a, r_ord_id_tbl b ";
$sql .= "WHERE a.r_ord_id = b.id ";
$sql .= "AND b.cancelled = 0 ";
$sql .= "AND a.shipping_id IS NULL ";
$sql .= "GROUP BY b.id) AS t1 ";
$sql .= "WHERE t1.count_items = ifnull(t1.count_checked,0) ";
$sql .= "UNION ";
$sql .= "SELECT * FROM (SELECT b.id AS orderNum, sum(a.r_ord_line) AS count_items, sum(a.checked) AS count_checked, sum(a.shipping_id) AS count_shipped ";
$sql .= "FROM assembly_plan_tbl a, r_ord_id_tbl b ";
$sql .= "WHERE a.r_ord_id = b.id ";
$sql .= "AND b.cancelled = 0 ";
$sql .= "AND a.shipping_id IS NULL ";
$sql .= "GROUP BY b.id) AS t2 ";
$sql .= "WHERE t2.count_items > ifnull(t2.count_checked,0) ";
$sql .= "AND ifnull(t2.count_checked,0) > 0";

if (!$stmt = $link->prepare($sql) )  {
        die('prepare() 1918 failed: ' . htmlspecialchars($link->error));  
    }
    /*$rc = $stmt->bind_param("i", $serial);           
    if ( false === $rc ){
       die('bind() 1918 failed: ' . htmlspecialchars($stmt->error));
    }*/
    $rc = $stmt->execute();    
    if ( false === $rc ) {
       die('execute() 1918 failed: ' . htmlspecialchars($stmt->error)); 
    }
    $stmt->store_result();
    $num_of_rows = $stmt->num_rows;
                                                
    $stmt->bind_result( $orderNum, $count_items, $count_checked, $count_shipped  );
    $line_item = 0;
    $doneOnce = FALSE;
    while ($stmt->fetch()){  // go to next stage of query   
        $line_item++;
        if (!$doneOnce){
                echo "Click on the buttons below to commence the shipping process.<br>";
		echo "<br><table  border='1'>";
		echo "<tr><th width='40px'>Rdy?</th><th width='40px'>Ord. #</th><th width='260px' align='left'>Customer</th><th width='120px'>Due Date</th></tr> ";
                $doneOnce = TRUE;
        }
        echo "<tr>";
        echo "<td><a class='linkbtn' href='shipping.php?order_num=$orderNum' style='margin-left:20px;'>";

        if ( $count_items === $count_checked ){
            echo "<b>F</b>";
        }else{
            echo "P";
        }
        echo "</a></td>";
	
        echo "<td>".$orderNum."</td>";
        echo "<td>TBC</td>";
        echo "<td>TBC</td>";                      
        echo "</tr>";	
    } // end if result
    if ($line_item > 0){
        echo "</table>";
    } else { 
        echo "Nothing is ready to ship.<br>";
    }
    $stmt->close();  
        ?>
    </div>
 </body>
</html>
<?php 

ob_end_flush(); ?>