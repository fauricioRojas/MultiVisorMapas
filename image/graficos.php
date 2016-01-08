<?php

require '../BD/Conexion.php';


class graficos {
      
    
    function CreatePoint($capa, $largo, $ancho, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j)
    {
        $conexion = new Conexion();
        $conn = $conexion->getConexion();
        
        $nLargo = $largo/$filas;
        $nAncho = $ancho/$columnas;
        
        $factorLargo = $nLargo*$j;
        $factorAncho = $nAncho*$i;
        
        $Xde = $factorLargo;
        $Xa = $factorLargo + $nLargo;
        
        $Yde = $factorAncho;
        $Ya = $factorAncho + $nAncho;
                
        $imagen = imagecreatetruecolor($nLargo, $nAncho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);
        
        
        $query = "";
        if($capa == "Hospitales")
        {
            $purple = imagecolorallocatealpha($imagen, 191, 48, 153, $trans);
            $query = "select ((st_x(st_geometryN(geom,1))-o.xinicial)/o.factor) as x, ($ancho - ((st_y(st_geometryN(geom,1))-o.yinicial)/o.factor)) as y 
                        from 
                         (
                          select gid, h.geom FROM hospitales h
                            where st_intersects((
                            select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                              (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                              (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                            ), h.geom)
                         ) c,
                         (
                          select min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/$ancho factor, min(st_ymin(geom)) yinicial from 
                            (
                            select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                              (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                              (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                            ) c 
                         ) o
                        where (((st_x(st_geometryN(geom,1))-o.xinicial)/o.factor) between $Xde and $Xa) and
                              (($ancho-((st_y(st_geometryN(geom,1))-o.yinicial)/o.factor)) between $Yde and $Ya)"; 
            
            $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");        
        
            while ($row = pg_fetch_row($result))
            {
                imagefilledellipse($imagen, ($row[0]-$factorLargo), ($row[1]-$factorAncho), 6, 6, $purple);
            }
        }
        else if($capa == "Escuelas")
        {
           $orange = imagecolorallocatealpha($imagen, 242, 117, 7, $trans);
           $query = "select ((st_x(st_geometryN(geom,1))-o.xinicial)/o.factor) as x, ($ancho - ((st_y(st_geometryN(geom,1))-o.yinicial)/o.factor)) as y 
                        from 
                         (
                          select gid, h.geom FROM escuelaspu h
                            where st_intersects((
                            select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                              (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                              (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                            ), h.geom)
                         ) c,
                         (
                          select min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/$ancho factor, min(st_ymin(geom)) yinicial from 
                            (
                            select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                              (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                              (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                            ) c 
                         ) o
                        where (((st_x(st_geometryN(geom,1))-o.xinicial)/o.factor) between $Xde and $Xa) and
                              (($ancho-((st_y(st_geometryN(geom,1))-o.yinicial)/o.factor)) between $Yde and $Ya)";
            
            $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");        
        
            while ($row = pg_fetch_row($result))
            {
                imagefilledellipse($imagen, ($row[0]-$factorLargo), ($row[1]-$factorAncho), 6, 6, $orange);
            } 
        }
        
        return ($imagen);
    }
    
    function CreatePolygon($capa, $largo, $ancho, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j)
    {
        $conexion = new Conexion();
        $conn = $conexion->getConexion();
        
        $nLargo = $largo/$filas;
        $nAncho = $ancho/$columnas;
        
        $factorLargo = $nLargo*$j;
        $factorAncho = $nAncho*$i;
        
        $Xde = $factorLargo;
        $Xa = $factorLargo + $nLargo;
        
        $Yde = $factorAncho;
        $Ya = $factorAncho + $nAncho;
        
        
        $imagen = imagecreatetruecolor($nLargo, $nAncho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);
        
        $negro = imagecolorallocatealpha($imagen, 0, 0, 0, 0);
        
        $query = "";
        if($capa == "Distritos")
        {
            $verde = imagecolorallocatealpha($imagen, 0, 178, 48, $trans);
            $query = "SELECT gid, ((d.x - c.xinicial)/c.factor)- $factorLargo x, ($ancho - ((d.y - c.yinicial)/c.factor))- $factorAncho y FROM 
                        (
                        SELECT gid, st_x((ST_DumpPoints(geom)).geom) x, st_y((ST_DumpPoints(geom)).geom) y 
                         FROM 
                          (
                          select gid, d.geom from distritos d
                          where st_intersects((
                            select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                              (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                              (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                            ), d.geom)
                          ) s 
                        ) d,
                        (
                        select min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/$ancho factor,min(st_ymin(geom)) yinicial from 
                          (
                          select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                            (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                            (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                          ) c 
                        ) c
                      where ( ((d.x - c.xinicial)/c.factor) between $Xde and $Xa) and 
                            ( ( $ancho -((d.y - c.yinicial)/c.factor)) between $Yde and $Ya)";
            
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
                    imagefilledpolygon($imagen, $pointPolygonArray, count($pointPolygonArray)/2, $verde);
                    $pointPolygonArray = array();
                    $gid = $row[0];
                    array_push($pointPolygonArray,$row[1],$row[2]);
                }
            }
            imagefilledpolygon($imagen, $pointPolygonArray, count($pointPolygonArray)/2, $verde);
        
        }        
        
        return ($imagen);
    }
    
    function CreateLine($capa, $largo, $ancho, $filas, $columnas, $trans, $zoom, $despX, $despY, $i, $j)
    {
        $conexion = new Conexion();
        $conn = $conexion->getConexion();
        
        $nLargo = $largo/$filas;
        $nAncho = $ancho/$columnas;
        
        $factorLargo = $nLargo*$j;
        $factorAncho = $nAncho*$i;
        
        $Xde = $factorLargo;
        $Xa = $factorLargo + $nLargo;
        
        $Yde = $factorAncho;
        $Ya = $factorAncho + $nAncho;
        
        
        $imagen = imagecreatetruecolor($nLargo, $nAncho);
        $transparencia = imagecolorallocatealpha($imagen, 0, 0, 0, 127);       
        imagefilltoborder($imagen, 50, 50, $transparencia, $transparencia);
        imagesavealpha($imagen, true);       
        
        $query = "";
        if($capa == "Caminos")
        {
            $red = imagecolorallocatealpha($imagen, 229, 0, 0, $trans);
            $query = "SELECT gid, string_agg( (cast( ((ST_X(ST_GeometryN(ca.geom,1))-c.xinicial)/c.factor)- $factorLargo as varchar)), ',') x,
                            string_agg( (cast( ($ancho - (ST_Y(ST_GeometryN(ca.geom,1))-c.yinicial)/c.factor)- $factorAncho as varchar)),',' ) y FROM
                       (
                       select min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/ $ancho factor,min(st_ymin(geom)) yinicial from 
                         (
                         select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                           (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                           (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                         ) c 
                       ) c ,
                       (
                       select gid ,((ST_DumpPoints((ST_GeometryN(geom,1)))).geom) geom FROM caminos e
                       where st_intersects((
                        select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geomB from 
                           (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                           (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                        ), e.geom)
                       ) ca
                     where ( ((ST_X(ST_GeometryN(ca.geom,1))-c.xinicial)/c.factor) between $Xde and $Xa) and 
                           ( ($ancho - (ST_Y(ST_GeometryN(ca.geom,1))-c.yinicial)/c.factor) between $Yde and $Ya)
                     group by gid";
            
            $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");
            while ($row = pg_fetch_row($result))
            {    
                $x = explode(",", $row[1]);
                $y = explode(",", $row[2]);
                
                $xi = 0;
                $xf = 0;
                $yi = 0;
                $yf = 0;
                
                for($i = 0; $i < count($x); $i++)
                {
                    if($i == 0)
                    {
                        $xi = $x[$i];
                        $yi = $y[$i];
                    }
                    else
                    {
                        imageline($imagen , $xi, $yi , $x[$i] , $y[$i] , $red);
                        $xi = $x[$i];
                        $yi = $y[$i];
                    }                    
                }
            }
        }
        else if($capa == "Rios")
        {
            $blue = imagecolorallocatealpha($imagen, 30, 115, 190, $trans);
            $query = "SELECT gid, string_agg( (cast( ((ST_X(ST_GeometryN(ca.geom,1))-c.xinicial)/c.factor)- $factorLargo as varchar)), ',') x,
                            string_agg( (cast( ($ancho - (ST_Y(ST_GeometryN(ca.geom,1))-c.yinicial)/c.factor)- $factorAncho as varchar)),',' ) y FROM
                       (
                       select min(st_xmin(geom)) xinicial, (max(st_xmax(geom))-min(st_xmin(geom)))/ $ancho factor,min(st_ymin(geom)) yinicial from 
                         (
                         select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geom from 
                           (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                           (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                         ) c 
                       ) c ,
                       (
                       select gid ,((ST_DumpPoints((ST_GeometryN(geom,1)))).geom) geom FROM rios e
                       where st_intersects((
                         select st_setsrid( Box2D( st_buffer( p.centroide, ((c.distancia-(c.distancia * $zoom ))/2)) ), 5367 ) geomB from 
                            (select ST_GeomFromText(st_astext(st_point( st_x(st_centroid(geom))-((st_x(st_centroid(geom))* $despX )/2) , st_y(st_centroid(geom))-((st_x(st_centroid(geom))* $despY )/2) )),5367)  centroide
                                from distritos 
                                where gid = 302) p,
                            (select (max(st_xmax(geom))-min(st_xmin(geom))) distancia from distritos) c
                         ), e.geom)
                       ) ca
                     where ( ((ST_X(ST_GeometryN(ca.geom,1))-c.xinicial)/c.factor) between $Xde and $Xa) and 
                           ( ($ancho - (ST_Y(ST_GeometryN(ca.geom,1))-c.yinicial)/c.factor) between $Yde and $Ya)
                     group by gid";
            
            $result = pg_query($conn, $query) or die("Error al ejecutar la consulta");
            while ($row = pg_fetch_row($result))
            {    
                $x = explode(",", $row[1]);
                $y = explode(",", $row[2]);
                
                $xi = 0;
                $xf = 0;
                $yi = 0;
                $yf = 0;
                
                for($i = 0; $i < count($x); $i++)
                {
                    if($i == 0)
                    {
                        $xi = $x[$i];
                        $yi = $y[$i];
                    }
                    else
                    {
                        imageline($imagen , $xi, $yi , $x[$i] , $y[$i] , $blue);
                        $xi = $x[$i];
                        $yi = $y[$i];
                    }                    
                }
            }
        }
        
        return ($imagen);
    }
}
