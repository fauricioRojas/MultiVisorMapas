<?php

class Conexion {
    
    function getConexion()
    {        
        $user = "postgres";
        $password = "12345";
        $dbname = "cursoGIS";
        $port = "5432";
        $host = "localhost"; 

        $strconn = "host=$host port=$port dbname=$dbname user=$user password=$password";
        $conn = pg_connect($strconn) or die("Error de Conexion con la base de datos");
        return ($conn);      
    }
}
