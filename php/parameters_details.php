<?php
// Common parameters for all function proposing insight into the corpora
$mainpath=dirname(dirname(getcwd()))."/"; // -> /var/www/ademe/data/
$db=json_decode($_GET["db"]);

$dbname="";
foreach($db as $d){
    if (strpos($d, 'graph.db') !== false){
        $dbname=$d;
    } 
}
//$dbname=$db[0];//getDB($mainpath);//'homework-20750-1-homework-db.db';
$base = new PDO("sqlite:" .$mainpath.$dbname);
$max_item_displayed=6;

/*
 * This function gets the first db name in the data folder
 * IT'S NOT SCALABLE! (If you want to use several db's)
 */


?>
