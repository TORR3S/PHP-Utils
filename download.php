<?php
function download($file, $rate){
    $local_file = 'assets'.DIRECTORY_SEPARATOR.$download_file; // apply file path
    if(file_exists($local_file) && is_file($local_file)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($local_file));
        ob_clean();
        flush();
        
        $file = fopen($local_file, "r");
        while(!feof($file)) {
            print fread($file, round($rate*1024));
            flush();
            sleep(1);
        }
        fclose($file);
    }else{
        die('Error: The file "'.$local_file.'" does not exist!');
    }
}
?>
