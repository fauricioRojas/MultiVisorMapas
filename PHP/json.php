<?php

class json{
    
    function jsonPolygon($strconn, $schema, $table, $column, $srid, $ancho, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
        
        $query = "
        SELECT gid, ((geometria.x - medidas.xinicial)/medidas.factor) x, ($ancho - ((geometria.y - medidas.yinicial)/medidas.factor)) y 
        FROM 
           (SELECT gid, st_x((ST_DumpPoints(geom)).geom) x, st_y((ST_DumpPoints(geom)).geom) y 
            FROM 
               (SELECT gid, tab.$column geom FROM $schema.\"$table\" tab
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
        
        $gid = '';
        $pointPolygonArray = array();

        while ($row =  pg_fetch_row($result))
        {
            if($gid == '')
            {
                $gid = $row[0];
                $pointPolygonArray[] = array("x" => $row[1], "y" => $row[2]);                        
            }
            else if($gid == $row[0])
            {
                $pointPolygonArray[] = array("x" => $row[1], "y" => $row[2]);  
            }
            else 
            {   
                $geojson[] = array("gid" => $gid, "puntos" => $pointPolygonArray);

                $pointPolygonArray = array();
                $gid = $row[0];
                $pointPolygonArray[] = array("x" => $row[1], "y" => $row[2]);  
            }
        }

        $geojson[] = array("gid" => $gid, "puntos" => $pointPolygonArray);

        return(json_encode($geojson));
    }
    
    function jsonPoint($strconn, $schema, $table, $column, $srid, $ancho, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
        
        $query = "
        SELECT 	gid, ((st_x(st_geometryN(geom,1)) - medidas.xinicial) / medidas.factor) x,
                ($ancho - ((st_y(st_geometryN(geom,1)) - medidas.yinicial) / medidas.factor)) y 
        FROM
           (SELECT tab.gid, tab.$column as geom FROM $schema.\"$table\" tab
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
        
        $gid = '';
        $pointArray = array();

        while ($row =  pg_fetch_row($result))
        {
            if($gid == '')
            {
                $gid = $row[0];
                $pointArray[] = array("x" => $row[1], "y" => $row[2]);                        
            }
            else if($gid == $row[0])
            {
                $pointArray[] = array("x" => $row[1], "y" => $row[2]);  
            }
            else 
            {   
                $geojson[] = array("gid" => $gid, "puntos" => $pointArray);

                $pointArray = array();
                $gid = $row[0];
                $pointArray[] = array("x" => $row[1], "y" => $row[2]);  
            }
        }

        $geojson[] = array("gid" => $gid, "puntos" => $pointArray);

        return(json_encode($geojson));
    }
    
    function jsonLine($strconn, $schema, $table, $column, $srid, $ancho, $zoom, $despX, $despY)
    {
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
        
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
          (SELECT gid ,((ST_DumpPoints((ST_GeometryN(tab.$column,1)))).$column) geom FROM $schema.\"$table\" tab
           WHERE st_intersects(
                (SELECT st_setsrid(Box2D(st_buffer(p.centroide,((375336.1936-(375336.1936 * $zoom ))/2))), $srid) geom FROM 
                    (SELECT ST_GeomFROMText(st_astext(st_point( 470971.458311897-((470971.458311897 * $despX )/2) , 1072807.08034292-((470971.458311897 * $despY )/2) )), $srid) centroide) p
                ), tab.$column)
          ) geometria
        GROUP BY gid";
        
        $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");
        while ($row = pg_fetch_row($result))
        {    
            $x = explode(",", $row[1]);
            $y = explode(",", $row[2]);

            $geojson[] = array("gid" => $row[0], "puntosx" => $x, "puntosy" => $y);
        }
        return(json_encode($geojson));
    }
    
}