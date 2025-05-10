<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Es una página web dedicada a la gestion de datos de un Hospital">
    <meta name="author" content="Paula Venegas Roldán & Marco José Miranda Bahamonde">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hospital Virgen del Camino</title>

    <link rel="stylesheet" href="../styles_main.css">
</head>
<body>
    <header>   
        <div class="Cabezera">
            <button title = "button" class="Menu_Hamburgesa1">
                <svg class="svg1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                </svg>
            </button>
            
            <h1 class>Hospital Virgen del Camino</h1>
            <a href="../Main.html" >
                <img src="../Logo.png" alt = "Logo" class = "Logo">
            </a>
            <h2 class>Modificar Doctor</h2>
        </div>

        <nav class = "nav1">
            <ul class = "nav1_ul1">
                <li class="li"><a href="Doctor.html" class="a">Volver Doctor</a></li>
                <li class="li"><a href="../Main.html" class="a">Volver al Menu</a></li>
            </ul>
        </nav>

    </header>

    <div class="Fondo">
    <?php
    class OCIException extends \Exception {}
    error_reporting(E_ERROR | E_PARSE);

    if($_POST) {
        // Configuración de conexión
        $db_user = 'hospital';
        $db_pass = 'hospital';
        $db_conn_str = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))';
        
        $conn = oci_connect($db_user, $db_pass, $db_conn_str);
        
        if (!$conn) {
            $err = oci_error();
            die("Error de conexión: " . htmlentities($err['message']));
        }

        try {
            // Recoger datos del formulario
            $IdDoctor = $_POST['nIdDoctor'];
            $Nombre = $_POST['nNombre'];
            $Telefono = $_POST['nTelefono'];
            $Especialidad = $_POST['nEspecialidad'];
            $HoraInicio = $_POST['nHoraInicio'];
            $HoraFin = $_POST['nHoraFin'];
            $nDias = isset($_POST['nDias']) ? $_POST['nDias'] : array();
            $IdDepartamento = $_POST['nIdDepartamento']; // Corregido el nombre de la variable
            
            // Preparar la llamada al procedimiento
            if(empty($nDias)) {
                $plsql = "BEGIN PaqueteHospital.ModificarDoctor(
                            :IdDoctor, 
                            :Nombre, 
                            :Telefono, 
                            :Especialidad, 
                            :HoraInicio, 
                            :HoraFin, 
                            NULL, 
                            :IdDepartamento
                        ); END;";
            } else {
                // Construir array de días correctamente
                $diasArray = "TipoDiaSemana(";
                foreach($nDias as $dia) {
                    $diasArray .= "'" . $dia . "',";
                }
                $diasArray = rtrim($diasArray, ",") . ")";
                
                $plsql = "BEGIN PaqueteHospital.ModificarDoctor(
                            :IdDoctor, 
                            :Nombre, 
                            :Telefono, 
                            :Especialidad, 
                            :HoraInicio, 
                            :HoraFin, 
                            $diasArray, 
                            :IdDepartamento
                        ); END;";
            }
            
            $stmt = oci_parse($conn, $plsql);
            
            // Bind de parámetros
            oci_bind_by_name($stmt, ':IdDoctor', $IdDoctor);
            oci_bind_by_name($stmt, ':Nombre', $Nombre);
            oci_bind_by_name($stmt, ':Telefono', $Telefono);
            oci_bind_by_name($stmt, ':Especialidad', $Especialidad);
            oci_bind_by_name($stmt, ':HoraInicio', $HoraInicio);
            oci_bind_by_name($stmt, ':HoraFin', $HoraFin);
            oci_bind_by_name($stmt, ':IdDepartamento', $IdDepartamento);
            
            // Ejecutar
            $flag = oci_execute($stmt);
            
            if(!$flag) {
                $e = oci_error($stmt);
                throw new OCIException($e['message'], $e['code']);
            }
            
            echo "<div id=\"Resultado\"><p>Se ha modificado el doctor correctamente</p></div>";
            
        } catch(OCIException $e) {
            echo "<div id=\"Resultado\"><p>Error: " . htmlentities($e->getMessage()) . "</p></div>";
        } finally {
            if(isset($stmt)) oci_free_statement($stmt);
            if($conn) oci_close($conn);
        }
    }
    ?>
    </div>

    <footer>
        <div class = "Contacto">
            <a href="../Main.html" >
                <img src="../Logo.png" alt = "Logo" class = "Logo">
            </a>
            <p>Hospital</p>
        </div>
    </footer>
    
</body>
</html>
