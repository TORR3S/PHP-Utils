<?
function flipArr($arr){
    $out = array();
    foreach($arr as $key => $subarr) {
        foreach($subarr as $subkey => $subvalue) {
            $out[$subkey][$key] = $subvalue;
        }
    }
    return $out;
}
?>