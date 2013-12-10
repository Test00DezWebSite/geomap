<?php

header('Content-Type: application/json');

include('parameters_details.php');
include('countries_iso3166.php');
include('countries_dict.php');

$sql="";
$column="";
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
        $code = $country[$key];
        $tempcount = 0;
        if ($norm_country[$code]) {
            $norm_country[$code]["value"]+=$value;
            $norm_country[$code]["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>" . $norm_country[$code]["value"].' documents' ;
        } else {
            $info = array();
            $info["code"] = $code;
            $info["value"] = $value;
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();
            $info["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>" . $value.' documents';
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
        $code = $country[$row[$column]];
        $tempcount = 0;
        if ($norm_country[$code]) {
            $norm_country[$code]["value"]+=$row["count(*)"];
            $norm_country[$code]["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>" . $norm_country[$code]["value"].' documents';
        } else {
            $info = array();
            $info["code"] = $code;
            $info["value"] = $row["count(*)"];
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();
            $info["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$code] . "</span><br/>" . $row["count(*)"].' documents';
            $norm_country[$code] = $info;
        }
    }
}

$occToCC = array();
foreach ($norm_country as $c) {
    if (!$occToCC[$c["value"]]) {
        $occToCC[$c["value"]] = array();
    }
    array_push($occToCC[$c["value"]], $c["code"]);
}

krsort($occToCC);
$countries_occ_DESC = array();
foreach ($occToCC as $key => $value) {
    $info = array();
    $info["occ"] = $key;
    $info["countries"] = $value;
    $info["color"] = "";
    array_push($countries_occ_DESC, $info);
}
$min=$countries_occ_DESC[count($countries_occ_DESC)-1]["occ"];
$max=$countries_occ_DESC[0]["occ"];

$colors = array();
include_once("ColourGradient.php");
$nbSteps=count($countries_occ_DESC)-1;
$instance = new ColorGenerator($nbSteps);
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
            $moreinfo["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$j] . "</span><br/>" . $value["occ"]. ' documents';
            $thedata[$j] = $moreinfo;
        }
    }
}

$info = array();
$info["min"] = 0;
$info["max"] = 1;
$info["attrs"] = array();
$info["attrs"]["fill"] = "#FFFFFF";
$info["label"] = "[WHITE]  Papers: 0";
array_push($theslices, $info);

$finalarray=array();
$finalarray["areas"]=$thedata;
$finalarray["slices"]=$theslices;
$finalarray["min"]=$min;
$finalarray["max"]=$max;

echo json_encode($finalarray);

function pr($msg) {
    echo $msg . "<br>";
}

?>
