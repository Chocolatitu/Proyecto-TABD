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
            <h2 class>Añadir Departamento</h2>
        </div>

        <nav class = "nav1">
            <ul class = "nav1_ul1">
                <li class="li"><a href="Departamento.html" class="a">Volver Departamento</a></li>
                <li class="li"><a href="../Main.html" class="a">Volver al Menu</a></li>
            </ul>
        </nav>

    </header>

    <div class  = "Fondo">
    <?php
        class OCIException extends \Exception {} //poder usar excepciones del tipo OCI
        error_reporting(E_ERROR | E_PARSE);
        
        if($_POST)
        {
            // Configurar las variables de conexión
            $db_user = 'hospital';
            $db_pass = 'hospital';
            $db_conn_str = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))';
        
            // Establecer la conexión
            $conn = oci_connect($db_user, $db_pass, $db_conn_str);
        
            // Verificar si la conexión fue exitosa
            if (!$conn) {
                $err = oci_error();
                trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);
            }
            else { //nNombre IN VARCHAR2, nDescripcion IN VARCHAR2, nPlanta IN NUMBER
                // Preparar la consulta
                $Nombre = $_POST['nNombre'];
                $Planta = $_POST['nPlanta'];               
                $Descripcion = $_POST['nDescripcion'];
                try
                {
                    $plsql = "BEGIN PaqueteHospital.AnadirDepartamento('$Nombre', $Planta, '$Descripcion'); END;";
                    $stmt = oci_parse($conn, $plsql);     
                    // Ejecutar la consulta
                    $flag1 = oci_execute($stmt);

                    if(!$flag1)
                    {
                        throw new OCIException('Ha ocurrido algun error',499);
                    }        
    
                    $resultado = "<div id=\"Resultado\"><p> Se ha registrado el departamento correctamente </p></div>";
                    echo $resultado;
                }
                catch(OCIException $e)
                {
                    if($e->getCode() === 499)
                    {
                        $resultado = "<div id=\"Resultado\"><p>Error: Ha ocurrido algun error </p></div>";
                        echo $resultado;
                    }
                    else
                    {
                        $resultado = "<div id=\"Resultado\"><p>Error: No se ha podido realizar la acción</p></div>";
                        echo $resultado;
                    }
                }
                
                // Liberar recursos
                oci_free_statement($stmt);
                oci_close($conn);
            }
        }
    ?>
    </div>

    <footer>
        <div class = "Contacto">
            <a href="Main.html" >
                <img src="../Logo.png" alt = "Logo" class = "Logo">
            </a>
            <p>Hospital</p>
        </div>
    </footer>
    
</body>
</html>

