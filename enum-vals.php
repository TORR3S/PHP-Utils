<?php
//metodo 1
    $query = "SELECT column_type
        FROM information_schema.COLUMNS
        WHERE table_schema = 'inmo'
        AND TABLE_NAME = 'inmuebles'
        AND column_name = 'actividad';";
    if($datos = $_ITE->bdd->consultar($query,"","", false)){
        $tmp = preg_replace(
            "/(enum|set)\('/",
            "",
            $datos[0]['column_type']);
        $tmp = preg_replace("/'\)/","",$tmp);
        $enum = explode("','",$tmp);
        $html = '';
        foreach($enum as $item){
            $html.= "<option value=\"$item\">$item</option>";
        }
    }
?>
<?php
//metodo 2
    $query = "SELECT column_type
        FROM information_schema.COLUMNS
        WHERE table_schema = 'schema_name'
        AND TABLE_NAME = 'table_name'
        AND column_name = 'column_name';";
    if($data = $ITE->bdd->consultar($query,'','',false)){
        $enum = explode("','",
            preg_replace("/(enum|set)\('(.+?)'\)/","\\2",
            $data[0]['column_type']));
        $html = '';
        foreach($enum as $item){
            $html.= "<option value=\"$item\">$item</option>";
        }
    }
?>