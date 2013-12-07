<?php

header('Content-Type: application/json');

include('parameters_details.php');
include('countries_iso3166.php');
include('countries_dict.php');

$sql="";
$echoing=false;
$column="";
//if (strpos($dbname, 'Echoing') !== false){
//    $echoing=true;
//    $column="Office Country";
//    $sql='select count(*),"Office Country" from rock GROUP BY "Office Country" ORDER BY count(*) DESC';
//} else {


$query = str_replace( '__and__', '&', $_GET["query"] );
$elems = json_decode($query);

$selectiveQuery=true;
if(count($elems)==1){
    foreach ($elems as $e){
        if($e=="all") {
            $selectiveQuery=false;
            break;
        }
    }
}

$norm_country = array();

if($selectiveQuery){
    $countries_temp=array();
    foreach($elems as $e){
        $sql="SELECT ISIkeyword.data FROM ISIterms,ISIkeyword where ISIterms.data='".$e."' ".
                     "and ISIterms.id=ISIkeyword.id GROUP BY ISIkeyword.data";
        foreach ($base->query($sql) as $row) {
            if($countries_temp[$row["data"]]) $countries_temp[$row["data"]]+=1;
            else $countries_temp[$row["data"]]=1;
        }
    }
    arsort($countries_temp);
    
    foreach ($countries_temp as $key => $value) {
        //if($echoing) $code = $row[$column];
        $code = $country[$key];
        //$norm_country[$code] = array();//$row["count(*)"];
        $tempcount = 0;
        if ($norm_country[$code]) {
            $norm_country[$code]["value"]+=$value;
            $norm_country[$code]["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>Publications: " . $norm_country[$code]["value"];
        } else {
            $info = array();
            $info["code"] = $code;
            $info["value"] = $value;
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();

            //$info["tooltip"]["content"] = "%3Cspan%20style%3D%22font-weight%3Abold%3B%22%3E".$code."%3C%2Fspan%3E%3Cbr%2F%3Enb_Publishers%20%3A".$row["count(*)"];
            //$info["tooltip"]["content"] = "&lt;span&gt;".$code."&lt;&#47;span&gt;&lt;br;&lt;&gt;".$row["count(*)"];
            $info["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>Publications: " . $value;
            $norm_country[$code] = $info;
        }
    }
}
else {
    $column="data";
    $sql = "select count(*),data from ISIkeyword GROUP BY data ORDER BY count(*) DESC";
//}
//$sql="select count(*),data from ISIC1Country GROUP BY data ORDER BY count(*) DESC";//ademe

    foreach ($base->query($sql) as $row) {
        //if($echoing) $code = $row[$column];
        $code = $country[$row[$column]];
        //$norm_country[$code] = array();//$row["count(*)"];
        $tempcount = 0;
        if ($norm_country[$code]) {
            $norm_country[$code]["value"]+=$row["count(*)"];
            $norm_country[$code]["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>Publications: " . $norm_country[$code]["value"];
        } else {
            $info = array();
            $info["code"] = $code;
            $info["value"] = $row["count(*)"];
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();

            //$info["tooltip"]["content"] = "%3Cspan%20style%3D%22font-weight%3Abold%3B%22%3E".$code."%3C%2Fspan%3E%3Cbr%2F%3Enb_Publishers%20%3A".$row["count(*)"];
            //$info["tooltip"]["content"] = "&lt;span&gt;".$code."&lt;&#47;span&gt;&lt;br;&lt;&gt;".$row["count(*)"];
            $info["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>Publications: " . $row["count(*)"];
            $norm_country[$code] = $info;
        }
    }
}

$occToCC = array();
foreach ($norm_country as $c) {
    //echo "[".$c["code"]."]:".$c["value"];
    //echo "<br>";
    if (!$occToCC[$c["value"]]) {
        $occToCC[$c["value"]] = array();
    }
    array_push($occToCC[$c["value"]], $c["code"]);
}

krsort($occToCC);
$countries_occ_DESC = array();
foreach ($occToCC as $key => $value) {
    //pr($key.":".json_encode($value));
    $info = array();
    $info["occ"] = $key;
    $info["countries"] = $value;
    $info["color"] = "";
    array_push($countries_occ_DESC, $info);
}

$colors = array();
//array_push($colors, "FF0000");
//array_push($colors, "FF1100");
//array_push($colors, "FF2200");
//array_push($colors, "FF3300");
//array_push($colors, "FF4400");
//array_push($colors, "FF5500");
//array_push($colors, "FF6600");
//array_push($colors, "FF7700");
//array_push($colors, "FF8800");
//array_push($colors, "FF9900");
//array_push($colors, "FFAA00");
//array_push($colors, "FFBB00");
//array_push($colors, "FFCC00");
//array_push($colors, "FFDD00");
//array_push($colors, "FFEE00");
//array_push($colors, "FFFF00");

$colors = array();
include("ColourGradient.php");
$nbSteps=count($countries_occ_DESC)-1;
$instance = new Generator($nbSteps);
$instance->getColours();
$colors=$instance->thecolors;

foreach ($countries_occ_DESC as $key => $value) {
    if ($key < count($colors)) {
        $countries_occ_DESC[$key]["color"] = $colors[min(count($colors), $key)];
    } else $countries_occ_DESC[$key]["color"] = $colors[count($colors) - 1];
}

$temp = $countries_occ_DESC;

$theslices = array();
$thedata = array();
foreach ($temp as $key => $value) {
    $info = array();
    $info["min"] = $value["occ"];
    $info["max"] = $value["occ"]+1;
    $info["attrs"] = array();
    $info["attrs"]["fill"] = "#".$value["color"];
    $info["label"] = "[".$value["color"]."]  Papers: ".$value["occ"];
    array_push($theslices, $info);

    $temp2 = $value["countries"];
    foreach ($temp2 as $j) {
        if ($j != "") {
            $moreinfo = array();
            $moreinfo["value"] = $value["occ"];
            $moreinfo["attr"] = array();
            $moreinfo["attr"]["href"] = "#";
            $moreinfo["tooltip"] = array();
            $moreinfo["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$j] . "</span><br/>Publications: " . $value["occ"];
            $thedata[$j] = $moreinfo;
        }
    }
}

$finalarray=array();
$finalarray["areas"]=$thedata;
$finalarray["slices"]=$theslices;

echo json_encode($finalarray);
function pr($msg) {
    echo $msg . "<br>";
}

?>
