<?php

$strconn = "host=localhost port=5432 dbname=cursoGIS user=postgres password=12345";

$srid = 5367;
$ancho = 480;  
$zoom = 0;
$despX = 0;
$despY = 0;

$type = $_GET['type'];
$table = $_GET['table'];
$column = $_GET['column'];

require './json.php';
$json = new json();

if($type == "POLYGON" || $type == "MULTIPOLYGON")
{
    $geojson = $json->jsonPolygon($strconn, $table, $column, $srid, $ancho, $zoom, $despX, $despY);
}

elseif($type == "POINT" || $type == "MULTIPOINT")
{
    $geojson = $json->jsonPoint($strconn, $table, $column, $srid, $ancho, $zoom, $despX, $despY);
}

elseif($type == "LINESTRING" || $type == "MULTILINESTRING")
{
    $geojson = $json->jsonLine($strconn, $table, $column, $srid, $ancho, $zoom, $despX, $despY);
}


print_r($geojson);