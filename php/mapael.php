<?php

header('Content-Type: application/json');
/*
 geomap/php/mapael.php?db=["data/nci/graph.db"]&query=["biomass","fuel","cooperatives","energy use","energy poverty","wind power","clean energy","social business","remote areas","Solar","battery","kerosene lanterns","retailers","lamps","energy production","distribution","grid electricity"]
 */

include('parameters_details.php');
include('countries_iso3166.php');

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
    $country_docs=array();
    foreach($elems as $e){
        $sql="SELECT ISIkeyword.data,ISIkeyword.id FROM ISIterms,ISIkeyword where ISIterms.data='".$e."' ".
                     "and ISIterms.id=ISIkeyword.id GROUP BY ISIkeyword.data";
        foreach ($base->query($sql) as $row) {
            if(!$country_docs[$row["data"]]){
                $country_docs[$row["data"]]=array();
            }
            $country_docs[$row["data"]][$row["id"]]=1;
        }
    }
    
    foreach ($country_docs as $key => $value)
        $country_docs[$key]=count($value);
    
    arsort($country_docs);
    
    foreach ($country_docs as $key => $value) {
        $code = strtoupper($key);        
        if($CC[$code]){
            $tempcount = 0;
            if ($norm_country[$code]) {
                $norm_country[$code]["value"]+=$value;
                $norm_country[$code]["tooltip"]["content"] = "" ;
            } else {
                $info = array();
                $info["code"] = $code;
                $info["value"] = $value;
                $info["attrs"] = array();
                $info["attrs"]["href"] = "";
                $info["tooltip"] = array();
                $info["tooltip"]["content"] = "";
                $norm_country[$code] = $info;
            }
        }
    }   
    $country_divisor=getDivisors($mainpath.$dbname);    
    foreach ($norm_country as $key => $value){            
        if($CC[$key]){
            $finalval=$value["value"]/($country_divisor[$key]+1);
//	    pr($key." : ".$value["value"]." / ".($country_divisor[$key]+1)." = ".$finalval);
            $info = array();
            $info["code"] = $key;
            $info["value"] = $value["value"];
            $info["floatval"] = $finalval;
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();
            $info["tooltip"]["content"] = "";
            $norm_country[$key] = $info;
        }
    }
    
    $maxzeros=0;
    foreach ($norm_country as $key => $value) {    
        $dafloat="".$value["floatval"];
        $aaa=explode(".", $dafloat);
        $right=$aaa[1];
        $thesize=count($right)+1;

        $zerosCount=0;
        foreach (range(0, $thesize) as $i) {
            if($right[$i]=="0") $zerosCount+=1;
        }
        if($zerosCount>$maxzeros)$maxzeros=$zerosCount;
    }
    $maxzeros+=3;//more zeros, more precision!
    
    $mult=pow(10,$maxzeros);
    $minF=100000.0;
    $maxF=0.0;
    foreach ($norm_country as $key => $value){

            $realOCC=$value["value"];
            $floatVal=$value["floatval"];
            $fakeOCC=ceil($floatVal*$mult);
            $percentage=round(($floatVal*100),2);
//	    pr($floatVal." - ".$fakeOCC." - ".$percentage);
            $info = array();
            $info["code"] = $key;
            $info["realValue"] = $realOCC;
            $info["percentage"] = $percentage;
            $info["value"] = $fakeOCC;
            $info["attrs"] = array();
            $info["attrs"]["href"] = "#";
            $info["tooltip"] = array();
            //$info["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$key] . "</span><br/>" . $realOCC.'  documents ('.$info["percentage"].'%)';
            $norm_country[$key] = $info;
            
            if($percentage>$maxF) $maxF=$percentage;
            if($percentage<$minF) $minF=$percentage;
    }
    $fmin=$minF;
    $fmax=100.000000;
//    pr($fmin);
//    pr($fmax);
//    pr($minF);
//    pr($maxF);
//    pr("-----");
//    pr((($fmax-$fmin)/($maxF-$minF))."*");
//    pr("-----");
    $constant=(($fmax-$fmin)/($maxF));
//    pr($constant);
    foreach ($norm_country as $key => $value){
        $old=$value["percentage"];
        $new=$old;//($fmax-$fmin)*($old-$fmin)/($maxF-$minF)+$fmin;//$old*$constant;# da formula!
        $norm_country[$key]["percentage"]=round($new,2);
        $norm_country[$key]["tooltip"]["content"]= "<span style='font-weight=bold;'>" . $CC[$key] . "</span><br/>" . $value["realValue"].'  documents ('.round($new,2).'%)';
//	pr($key." : ".$new);
//        pr($value["code"].": ".$value["realValue"].", ".$value["percentage"].", div:".($country_divisor[$key]+1));
    }
}
else {
    $column="data";
    $sql = "select count(*),data from ISIkeyword GROUP BY data ORDER BY count(*) DESC";
//}
//$sql="select count(*),data from ISIC1Country GROUP BY data ORDER BY count(*) DESC";//ademe

    foreach ($base->query($sql) as $row) {
        $code = strtoupper($row[$column]);
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
//pr($max);

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
            if($selectiveQuery){
                if($norm_country[$j]["realValue"]==1){
                    $moreinfo["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$j] . "</span><br/>" . $norm_country[$j]["realValue"]. ' document ('.$norm_country[$j]["percentage"].'%)';
                } else {
                    $moreinfo["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$j] . "</span><br/>" . $norm_country[$j]["realValue"]. ' documents ('.$norm_country[$j]["percentage"].'%)';
                }                    
            } else {
                $moreinfo["tooltip"]["content"] = "<span style='font-weight=bold;'>" . $CC[$j] . "</span><br/>" . $value["occ"]. ' documents';
            }
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

if($selectiveQuery){
    $minInt=100000;
    $maxInt=0;
    $minFloat=100000.0;
    $maxFloat=0.0;
    foreach ($norm_country as $key => $value) {
        if($value["realValue"]>$maxInt) $maxInt=$value["realValue"];
        if($value["realValue"]<$minInt) $minInt=$value["realValue"];
        if($value["percentage"]>$maxFloat) $maxFloat=$value["percentage"];
        if($value["percentage"]<$minFloat) $minFloat=$value["percentage"];
    }
    $min=$minFloat."%";
    $max=$maxFloat."%";
//    pr("Results:");
//    pr("min: ".$min);
//    pr("max: ".$max);
}
$finalarray["min"]=$min;
$finalarray["max"]=$max;

echo json_encode($finalarray);
//revert

function getDivisors($mainpath,$dbnam){    
    include('countries_iso3166.php');
    $conn = new PDO("sqlite:" .$mainpath.$dbnam);
    $sql = "select count(*),data from ISIkeyword GROUP BY data ORDER BY count(*) DESC";
    $column="data";
    $country_divisor=array();
    foreach ($conn->query($sql) as $row) {
        $code = strtoupper($row[$column]);
        if($CC[$code]){
            $tempcount = 0;
            if ($country_divisor[$code]) {
                $country_divisor[$code]+=$row["count(*)"];
            } else {
                $country_divisor[$code] = $row["count(*)"];
            }
        }
    }
    return $country_divisor;
}
/* selectiveQuery:
 * ["NCI"]
 * query=["rural communities","recruitment","nutritional status","credit","loans",
 *      "financial support","farm","entrepreneurship","behavior change","micro-finance",
 *      "tourism","agriculture","local market","sustainable development","self-sustainability",
 *      "household income","small business owners","start-ups","social entrepreneurship","saw",
 *      "rice","value chain","ecosystem","economic development","cooperation","income generating projects",
 *      "social development","adaptation","livestock","capacity building workshops","Diaspora","business support",
 *      "habitat","business","entrepreneurs","vegetables","conservation","economic growth","low income","youth employment"]
 * Case: Country KH
 * SELECT * FROM ISIkeyword where ISIkeyword.data="kh"
 * 12 documents (493,494,495,496,1465,1466,1467,1468,1993,1994,1995,1996) <= Global Divisor
 * but:
 * SELECT * FROM ISIterms,ISIkeyword where ISIkeyword.data="kh" and ISIterms.id=ISIkeyword.id group by ISIterms.data
 *  96 keywords (one country has * docs, but one doc could have * keywords
 * so when i'm doing the first loop of all (foreach($elems as $e) inside selectiveQuery)
 * i count 26 keywords that are associated with KH and: 26 / GlovalDivisor = 26/12 ... mistaaaaake!!
 */
function pr($msg) {
    echo $msg . "\n";
}

?>

