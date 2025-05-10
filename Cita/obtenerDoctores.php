<?php
header('Content-Type: application/json');

$db_user = 'hospital';
$db_pass = 'hospital';
$db_conn_str = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=XEPDB1)))';

$conn = oci_connect($db_user, $db_pass, $db_conn_str);

if (!$conn) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$especialidad = $_GET['especialidad'] ?? '';

$query = "SELECT IdDoctor, Nombre, HoraInicio, HoraFin 
          FROM Doctor 
          WHERE Baja = 'N' AND Especialidad = :especialidad
          ORDER BY Nombre";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':especialidad', $especialidad);

if (!oci_execute($stmt)) {
    echo json_encode(['error' => 'Error en la consulta']);
    exit;
}

$doctores = [];
while ($row = oci_fetch_assoc($stmt)) {
    $doctores[] = [
        'IdDoctor' => $row['IDDOCTOR'],
        'Nombre' => $row['NOMBRE'],
        'HoraInicio' => $row['HORAINICIO'],
        'HoraFin' => $row['HORAFIN']
    ];
}

echo json_encode($doctores);
oci_close($conn);
?>