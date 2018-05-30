<?
/*
* getSlug()
* @action:return cleaned text of given string
*   Used to get url friendly title
* @parameters:
*   $str: string to be cleaned
*   $replace: array of characters to be cleaned; default empty
*   $delimiter: delimiter to separate words; default is '-'
* @return: string of given length
* @modified : 10th July 2014
* @modified by: FTorres
*/
setLocale(LC_ALL, 'es_ES.UTF8', 'en_US.UTF8');
function getSlug($str, $replace=array(), $delimiter='-'){
    if(!empty($replace)){
        $str = str_replace((array)$replace, ' ', $str);
    }
    $replace = array(
        'illegal' => array('@','€','$','£','¥'),
        'legal'   => array('o','e','s','l','y')
    );
    $str = str_replace($replace['illegal'], $replace['legal'], $str);
    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '-', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
    return $clean;
}
?>