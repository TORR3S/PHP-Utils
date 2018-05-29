<?php
define('IMG_NOPROC',1); // La imagen original no se redimensiona ni se recorta
define('IMG_RESIZE',2); // Redimensiona solo para reducir
define('IMG_CROP',3); // La imagen original se encaja dentro de las dimensiones del tamaño permitido pudiendo ser recortadas franjas verticales u horizontales de la imagen original
define('IMG_FIT',4); // La imagen original se encaja dentro de las dimensiones del tamaño permitido pudiendo quedar franjas verticales u horizontales sin imprimir en la imagen de destino

function uploadImg(
            $image,$name, $upFolder,
            $mode=IMG_RESIZE, $width=1024,$height=768, $mime='jpeg',
            $prefix='', $sufix='', $del=TRUE, $quality=75) {
    if($image['error']!=0){
        trigger_error('Error en el archivo subido',E_USER_ERROR);
    }
    $ext = pathinfo($image['tmp_name'], PATHINFO_EXTENSION);
    $src_path = $upFolder.$name.'_src.'.$ext;
    if(!file_exists($src_path) && !rename($image['tmp_name'], $src_path)) {
        trigger_error('No ha sido posible mover el archivo subido',E_USER_ERROR);
    }
    $img_info = getimagesize($src_path);
    $dst_x = 0; $dst_w = $width;
    $dst_y = 0; $dst_h = $height;      $dst_r = $dst_w/$dst_h;
    $src_x = 0; $src_w = $img_info[0];
    $src_y = 0; $src_h = $img_info[1]; $src_r = $src_w/$src_h;
    switch($mode){
        case IMG_NOPROC:
            $width  = $dst_w = $img_info[0];
            $height = $dst_h = $img_info[1];
            break;
        case IMG_RESIZE:
                  if($src_r >  $dst_r && $src_w > $dst_w) { //ratio de ancho mayor que la imagen de destino
                $height = $dst_h = $src_h/($src_w/$dst_w);
            }else if($src_r <= $dst_r && $src_h > $dst_h) { //ratio de alto mayor que la imagen de destino
                $width  = $dst_w = $src_w/($src_h/$dst_h);
            }else{                      //no se redimensiona al no superar las dimensiones máximas permitidas
                $width  = $dst_w = $img_info[0];
                $height = $dst_h = $img_info[1];
            }
            break;
        case IMG_CROP:
            if($dst_r > $src_r) {       //proporción de anchura superior a la de destino
                $tmp_h = $src_w/$dst_r;     //calcula el alto aprobechable
                $src_y = ($src_h-$tmp_h)/2; //desechar el margen inferior de la imagen (la mitad de lo que sobra)
                $src_w = $src_w;            //aprobecha todo el ancho puesto que solo se deve recortar el alto
                $src_h = $tmp_h;            //altura aprobechable
            } else {                     //proporción de altura superior a la de destino
                $tmp_w = $src_h*$dst_r;     //calcula el ancho aprobechable
                $src_x = ($src_w-$tmp_w)/2; //desechar el margen izquierdo de la imagen (la mitad de lo que sobra)
                $src_w = $tmp_w;            //anchura aprobechable
                $src_h = $src_h;            //aprobecha todo el alto puesto que solo se deve recortar el ancho
            }
            break;
        case IMG_FIT:
            if($dst_r > $src_r) {       //proporción de anchura superior a la de destino
                $tmp_w = $dst_w*$src_r;     //calcula el ancho aprobechable
                $dst_x = ($dst_w-$tmp_w)/2; //desechar el margen izquierdo de la imagen (la mitad de lo que sobra)
                $dst_w = $dst_x+$tmp_w;     //anchura aprobechable
            } else {                     //proporción de altura superior a la de destino
                $tmp_h = $dst_h/$src_r;     //calcula el alto aprobechable
                $dst_y = ($dst_h-$tmp_h)/2; //desechar el margen inferior de la imagen (la mitad de lo que sobra)
                $dst_h = $dst_y+$tmp_h;     //altura aprobechable
            }
            break;
        default: trigger_error('Error: modo de redimensionado no reconocido',E_USER_ERROR);
    }
    switch($img_info['mime']) {
        case 'jpeg': $src_img = imagecreatefromjpeg($src_path); break;
        case 'png':  $src_img = imagecreatefrompng($src_path);  break;
        case 'gif':  $src_img = imagecreatefromgif($src_path);  break;
        default: trigger_error('Error: formato de imagen no compatible',E_USER_ERROR);
    }
    $dst_img = imagecreatetruecolor($width,$height);
    imagecopyresampled($dst_img,$src_img, $dst_x,$dst_y, $src_x,$src_y, $dst_w,$dst_h, $src_w,$src_h);
    $dst_path = $upFolder.$prefix.$name.$sufix.'.';
    switch($dst_mime) {
        case 'jpeg': $dst_path .= 'jpg'; imagejpeg($dst_img,$dst_path,$quality); break;
        case 'png':  $dst_path .= 'png'; imagepng($dst_img,$dst_path,$quality); break;
        case 'gif':  $dst_path .= 'gif'; imagegif($dst_img,$dst_path); break;
        default: trigger_error('Error: formato de imagen no compatible',E_USER_ERROR);
    }
    chmod($dst_path,0777);
    if($del) unlink($src_path);
    return TRUE;
}

//function registerFileinDB($file, $name, $code){
//    $file_type = $file['type'];
//    $file_size = $file['size'];
//    $file_name = $file['name'];
//    $campos  = array('file', 'file_name', 'file_type', 'file_size', 'description',   'folder', 'date', 'code');
//    $valores = array($name,  $file_name,  $file_type,  $file_size,             '', $upFolder,   NOW(), $code);
//    $idImg = $ITE->insertar('files', $campos, $valores);
//    return TRUE;
//}

//uploadImg($image,$name, $upFolder, 285,185,  IMG_CROP,   '_S', FALSE) &&
//uploadImg($image,$name, $upFolder, 1024,768, IMG_RESIZE, '_L') &&
//    registerFileinDB($image, $name, $code);
//	
//uploadImg($image,$name, $upFolder, 1024,768, IMG_FIT, '_L') &&
//    registerFileinDB($image, $name, $code);
?>
