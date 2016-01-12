<?php
    require './graficos.php';
    header('Content-Type: image/png');
    
    //http://localhost:8000/image/imagen.php?type=POINT&strconn=host=localhost%20port=5432%20dbname=cursoGIS%20user=postgres%20password=12345&schema=public&table=distritos&column=centroide&srid=5367&x=640&y=480&r=23&g=23&b=43&trans=10&zoom=0&despX=0&despY=0
    
    //http://localhost:8000/image/imagen.php?type=MULTIPOLYGON&strconn=host=localhost%20port=5432%20dbname=cursoGIS%20user=postgres%20password=12345&schema=public&table=distritos&column=geom&srid=5367&x=640&y=480&r=23&g=23&b=43&trans=10&zoom=0&despX=0&despY=0
    
    //http://localhost:8000/image/imagen.php?type=MULTILINESTRING&strconn=host=localhost%20port=5432%20dbname=cursoGIS%20user=postgres%20password=12345&schema=public&table=rios&column=geom&srid=5367&x=640&y=480&r=23&g=23&b=43&trans=10&zoom=0&despX=0&despY=0
 
    
    $type = $_REQUEST['type'];
    
    $strconn = $_REQUEST['strconn'];
    $schema = $_REQUEST['schema'];
    $table = $_REQUEST['table'];
    $column = $_REQUEST['column'];
    $srid = $_REQUEST['srid'];
    $largo = $_REQUEST['x'];
    $ancho = $_REQUEST['y'];
    $r = $_REQUEST['r'];
    $g = $_REQUEST['g'];
    $b = $_REQUEST['b'];   
    $trans = $_REQUEST['trans'];   
    $zoom = $_REQUEST['zoom'];
    $despX = $_REQUEST['despX'];
    $despY = $_REQUEST['despY'];
    
    $graficos = new graficos();
    
    if($type == "POLYGON" || $type == "MULTIPOLYGON")
    {
        $img = $graficos->CreatePolygon($strconn, $schema, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY);
    }
    if($type == "POINT" || $type == "MULTIPOINT")
    {
        $img = $graficos->CreatePoint($strconn, $schema, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY);
    }
    if($type == "LINESTRING" || $type == "MULTILINESTRING")
    {
        $img = $graficos->CreateLine($strconn, $schema, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY);
    }       
    
    imagepng($img);
    imagedestroy($img);