<?php
defiene('ALPHA_LOWER',   1);
defiene('ALPHA_UPPER',   2);
defiene('NUMBERS',       4);
defiene('SYMBOLS',       8);
defiene('SPECIALS_LOWER',16);
defiene('SPECIALS_UPPER',32);
// Retorna una cadena compuesta por caracteres aleatorios
function randStr($length=8, $charset=ALPHA_LOWER|ALPHA_UPPER|NUMBERS|SYMBOLS, $chars='') {
    if($chars=='') {
        if($charset & ALPHA_LOWER)    $chars .= 'abcdefghijklmnopqrstuvwxyz';
        if($charset & ALPHA_UPPER)    $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($charset & NUMBERS)        $chars .= '0123456789';
        if($charset & SYMBOLS)        $chars .= '¡!#$%&()+-.,;=@^¨[]_{}~€¿¬ºª';
        if($charset & SPECIALS_LOWER) $chars .= 'àáâãäèéêëìíîïòóôõöùúûüýÿŵŷçñ';
        if($charset & SPECIALS_UPPER) $chars .= 'ÀÁÂÃÄÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝŸŴŶÇÑ';
    }
    $len = strlen($chars)-1;
    $str = '';
    mt_srand((double)microtime()*1E6);
    while($length--) {
        $str .= $chars[mt_rand(0,$len)];
    }
    return $str;
}
?>
