<?php

header('Content-Type: application/json');

include('parameters_details.php');
include('countries_iso3166.php');
include('countries_dict.php');


//$sql="select count(*),data from ISIC1Country GROUP BY data ORDER BY count(*) DESC";//ad
$sql = "select count(*),data from ISIkeyword GROUP BY data ORDER BY count(*) DESC";

$norm_country = array();
foreach ($base->query($sql) as $row) {
    $code = $country[$row["data"]];
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
array_push($colors, "FF0000");
array_push($colors, "FF1100");
array_push($colors, "FF2200");
array_push($colors, "FF3300");
array_push($colors, "FF4400");
array_push($colors, "FF5500");
array_push($colors, "FF6600");
array_push($colors, "FF7700");
array_push($colors, "FF8800");
array_push($colors, "FF9900");
array_push($colors, "FFAA00");
array_push($colors, "FFBB00");
array_push($colors, "FFCC00");
array_push($colors, "FFDD00");
array_push($colors, "FFEE00");
array_push($colors, "FFFF00");

foreach ($countries_occ_DESC as $key => $value) {
    if ($key < count($colors)) {
        $countries_occ_DESC[$key]["color"] = $colors[min(count($colors), $key)];
    }
    else
        $countries_occ_DESC[$key]["color"] = $colors[count($colors) - 1];
}

$temp = $countries_occ_DESC;

$theslices = array();
$thedata = array();
foreach ($temp as $key => $value) {
    $info = array();
    $info["min"] = $value["occ"]-1;
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
