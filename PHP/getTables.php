<?php

/*
    LLAMADA DEL METODO:
    getTables.php?strconn=host=localhost port=5432 dbname=cursoGIS user=postgres
 */

$strconn = $_REQUEST['strconn'];
$conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");

$query = "select f_table_schema, f_table_name, f_geometry_column, s.srid, type, auth_name||':'||auth_srid proj, srtext from geometry_Columns g
inner join spatial_ref_sys s
on g.srid = s.srid"; 
            
$result = pg_query($conn, $query) or die("Error al ejecutar la consulta");

function name($string)
{
    $i=8;
    while($i<strlen($string)){
        if($string[$i] == "\"")
        {
            return (substr($string, 8, $i-8));
        }
        $i++;
    }
    
}

while ($row = pg_fetch_row($result))
{
    $tables[] = array(
            "schema" => $row[0],
            "table" => $row[1],
            "column" => $row[2],
            "srid" => $row[3],
            "type" => $row[4],
            "proj" => $row[5],
            "name" => name($row[6])
            );
}

print_r(json_encode($tables));
