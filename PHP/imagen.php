<?php
    require './graficos.php';
    header('Content-Type: image/png');
    
    $type = $_REQUEST['type'];    
    $capa = $_REQUEST['capa'];
    $trans = $_REQUEST['trans'];
    $i = $_REQUEST['i'];
    $j = $_REQUEST['j'];
    
    $width = $_REQUEST['x'];
    $height = $_REQUEST['y'];
    $filas = $_REQUEST['rowsColumns'];
    $columnas = $_REQUEST['rowsColumns'];
    $zoom = $_REQUEST['zoom']; // 0.0 a 0.95
    $despX = $_REQUEST['despX']; //-0.9 a 0.9 Se puede mas pero queda en blanco no se controla eso
    $despY = $_REQUEST['despY']; //-0.9 a 0.9 Se puede mas pero queda en blanco no se controla eso
    
    $graficos = new graficos();
    
    if($type == "Polygon")
    {
        $img = $graficos->CreatePolygon($capa, $width, $height, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j);
    }
    if($type == "Point")
    {
        $img = $graficos->CreatePoint($capa, $width, $height, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j);
    }
    if($type == "Line")
    {
        $img = $graficos->CreateLine($capa, $width, $height, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j);
    }       
    
    imagepng($img);
    imagedestroy($img);