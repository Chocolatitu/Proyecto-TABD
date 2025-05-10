<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital - Añadir Paciente</title>
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
            
            <h1 class>Hospital</h1>
            <a href="../Main.html" >
                <img src="../Logo.png" alt = "Logo" class = "Logo">
            </a>
            <h2 class>Añadir Paciente</h2>
        </div>

        <nav class = "nav1">
            <ul class = "nav1_ul1">
                <li class="li"><a href="Paciente.html" class="a">Volver Paciente</a></li>
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
        
        try {
            // Establecer conexión
            $conn = oci_connect($db_user, $db_pass, $db_conn_str);
            if (!$conn) {
                throw new Exception("Error de conexión: " . oci_error()['message']);
            }
            
            // Recoger y validar datos
            $Nombre = trim($_POST['nNombre']);
            $Telefono = trim($_POST['nTelefono']);
            $FechaNacimiento = trim($_POST['nFechaNacimiento']);
            $Direccion = trim($_POST['nDireccion']);
            $Genero = trim($_POST['nGenero']);
            
            // Validaciones
            if(empty($Nombre)) throw new Exception("El nombre es requerido");
            if(strlen($Nombre) > 100) throw new Exception("Nombre demasiado largo (máx. 100 caracteres)");
            
            if(empty($Telefono)) throw new Exception("El teléfono es requerido");
            if(!preg_match('/^\d{9}$/', $Telefono)) throw new Exception("Teléfono debe tener 9 dígitos");
            
            if(empty($FechaNacimiento)) throw new Exception("La fecha de nacimiento es requerida");
            if(!preg_match('/^\d{2}-\d{2}-\d{4}$/', $FechaNacimiento)) {
                throw new Exception("Formato de fecha inválido. Use DD-MM-AAAA");
            }
            
            // Verificar fecha válida
            $partesFecha = explode('-', $FechaNacimiento);
            if(!checkdate($partesFecha[1], $partesFecha[0], $partesFecha[2])) {
                throw new Exception("Fecha de nacimiento no válida");
            }
            
            if(empty($Direccion)) throw new Exception("La dirección es requerida");
            if(strlen($Direccion) > 255) throw new Exception("Dirección demasiado larga (máx. 255 caracteres)");
            
            if(empty($Genero) || !in_array($Genero, ['M', 'F'])) {
                throw new Exception("Género no válido");
            }
            
            // Preparar la llamada al procedimiento
            $plsql = "BEGIN 
                        PaqueteHospital.AnadirPaciente(
                            :nombre, 
                            :telefono, 
                            TO_DATE(:fecha_nac, 'DD-MM-YYYY'), 
                            :direccion, 
                            :genero
                        ); 
                      END;";
            
            $stmt = oci_parse($conn, $plsql);
            
            // Bind de parámetros con tamaños adecuados
            oci_bind_by_name($stmt, ':nombre', $Nombre, 100);
            oci_bind_by_name($stmt, ':telefono', $Telefono);
            oci_bind_by_name($stmt, ':fecha_nac', $FechaNacimiento);
            oci_bind_by_name($stmt, ':direccion', $Direccion, 255);
            oci_bind_by_name($stmt, ':genero', $Genero, 1);
            
            // Ejecutar
            if(!oci_execute($stmt)) {
                $e = oci_error($stmt);
                throw new Exception("Error Oracle: " . $e['message']);
            }
            
            echo "<div class='exito'>Paciente añadido correctamente</div>";
            
        } catch(Exception $e) {
            echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
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