<?php

require '../BD/Conexion.php';


class graficos {      
    
    function CreatePoint($strconn, $scheme, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
                
        $imagen = imagecreatetruecolor($largo, $ancho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);
        
        $color = imagecolorallocatealpha($imagen, $r, $g, $b, $trans);
        $query = "
        SELECT 	((st_x(st_geometryN(geom,1)) - medidas.xinicial) / medidas.factor) x,
                ($ancho - ((st_y(st_geometryN(geom,1)) - medidas.yinicial) / medidas.factor)) y 
        FROM
           (SELECT tab.$column as geom FROM $scheme.\"$table\" tab
            WHERE st_intersects(
                (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                    (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
                ), tab.$column)
           ) geometria,
           (SELECT min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/ $ancho factor, min(st_ymin(geom)) yinicial 
            FROM 
               (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                    (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
               ) c 
           ) medidas";
        
        
        $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");        

        while ($row = pg_fetch_row($result))
        {
            imagefilledellipse($imagen, $row[0], $row[1], 6, 6, $color);
        }
        
        return ($imagen);
    }
    
    function CreatePolygon($strconn, $scheme, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
                
        $imagen = imagecreatetruecolor($largo, $ancho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);
        
        $color = imagecolorallocatealpha($imagen, $r, $g, $b, $trans);
        $query = "
        SELECT gid, ((geometria.x - medidas.xinicial)/medidas.factor) x, ($ancho - ((geometria.y - medidas.yinicial)/medidas.factor)) y 
        FROM 
           (SELECT gid, st_x((ST_DumpPoints(geom)).geom) x, st_y((ST_DumpPoints(geom)).geom) y 
            FROM 
               (SELECT gid, tab.$column geom FROM $scheme.\"$table\" tab
                WHERE st_intersects(
                        (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                            (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
                        ), tab.$column)
                ) s 
           ) geometria,
           (SELECT min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/$ancho factor,min(st_ymin(geom)) yinicial 
            FROM 
               (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                    (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
               ) c 
           ) medidas"; 
            
        
        $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");
            
        $gid='';
        $pointPolygonArray = array();

        while ($row =  pg_fetch_row($result))
        {
            if($gid=='')
            {
                $gid = $row[0];
                array_push($pointPolygonArray,$row[1],$row[2]);                        
            }
            else if($gid == $row[0])
            {
                array_push($pointPolygonArray,$row[1],$row[2]);
            }
            else 
            {   
                imagefilledpolygon($imagen, $pointPolygonArray, count($pointPolygonArray)/2, $color);
                $pointPolygonArray = array();
                $gid = $row[0];
                array_push($pointPolygonArray,$row[1],$row[2]);
            }
        }
        imagefilledpolygon($imagen, $pointPolygonArray, count($pointPolygonArray)/2, $color);
        
        return ($imagen);
    }

    function CreateLine($strconn, $scheme, $table, $column, $srid, $largo, $ancho, $r, $g, $b, $trans, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
                
        $imagen = imagecreatetruecolor($largo, $ancho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);
        
        $color = imagecolorallocatealpha($imagen, $r, $g, $b, $trans);
        $query = "
        SELECT  gid, string_agg((cast(((ST_X(ST_GeometryN(geometria.geom,1))-medidas.xinicial)/medidas.factor) as varchar)), ',') x,
                string_agg((cast(( $ancho -(ST_Y(ST_GeometryN(geometria.geom,1))-medidas.yinicial)/medidas.factor) as varchar)),',') y 
        FROM
          (SELECT min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/ $ancho factor,min(st_ymin(geom)) yinicial 
           FROM 
             (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
             ) c 
          ) medidas,
          (SELECT gid ,((ST_DumpPoints((ST_GeometryN(tab.$column,1)))).$column) geom FROM $scheme.\"$table\" tab
           WHERE st_intersects(
                (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                    (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
                ), tab.$column)
          ) geometria
        GROUP BY gid
        ";
        
        $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");
        while ($row = pg_fetch_row($result))
        {    
            $x = explode(",", $row[1]);
            $y = explode(",", $row[2]);

            for($i = 0; $i < count($x)-1; $i++)
            {
                imageline($imagen , $x[$i] , $y[$i] , $x[$i+1] , $y[$i+1] , $color);     
            }
        }
        
        return ($imagen);
    }
}
