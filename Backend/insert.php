<?php
session_start(); // Necesario para poder mandar mensajes de éxito/error a formulario.php
include('connection.php'); // insert.php vive DENTRO de Backend/, junto a connection.php
$con = connection();
 
// --- 1) Recibimos y limpiamos los datos ---
// trim() quita espacios en blanco accidentales al inicio/final que el
// usuario pudo haber dejado sin querer.
$name          = trim($_POST['full_name']     ?? '');
$phone_number  = trim($_POST['phone_number']  ?? '');
$delivery_date = trim($_POST['delivery_date'] ?? '');
$dessert_type  = trim($_POST['dessert_type']  ?? '');
$payment_type  = trim($_POST['payment_type']  ?? '');
$nit_code      = trim($_POST['nit_code']      ?? '');
$extra_comment = trim($_POST['extra_comment'] ?? '');
 
// --- 2) Validación en el servidor ---
// El HTML5 (required, type="date", etc.) del formulario se puede saltar
// fácilmente (Postman, Inspeccionar elemento, o apagando JS), así que
// SIEMPRE hay que revalidar aquí antes de tocar la base de datos.
$errores = [];
 
if ($name === '' || mb_strlen($name) > 100) {
    $errores[] = "El nombre completo es obligatorio.";
}
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone_number)) {
    $errores[] = "El número de teléfono no es válido.";
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $delivery_date) || $delivery_date < date('Y-m-d')) {
    $errores[] = "La fecha de entrega no es válida.";
}
 
$postres_validos = ["Croissant-Eiffel", "Rol de canela", "Strawberry Cupcakes", "Tartaleta", "Tiramisú", "Red Velvet", "Churros", "Trenzas Rellenas"];
if (!in_array($dessert_type, $postres_validos, true)) {
    $errores[] = "Selecciona un tipo de postre válido.";
}
 
$pagos_validos = ["Efectivo", "POS"];
if (!in_array($payment_type, $pagos_validos, true)) {
    $errores[] = "Selecciona un método de pago válido.";
}
 
if ($nit_code === '' || mb_strlen($nit_code) > 20) {
    $errores[] = "El NIT es obligatorio.";
}
 
// --- 3) Si algo falló, regresamos SIN tocar la base de datos ---
if (!empty($errores)) {
    $_SESSION['mensaje'] = "No se pudo guardar tu pedido: " . implode(" ", $errores);
    $_SESSION['tipo']    = "danger";
    header("Location: ../formulario.php");
    exit; // exit es clave: sin esto, el script seguiría corriendo después del header
}
 
// --- 4) Consulta preparada ---
// Los "?" son placeholders. mysqli sustituye los valores de forma segura,
// así que aunque alguien escriba algo como  ' OR 1=1 --  en el nombre,
// NO rompe ni manipula la consulta. Esto reemplaza la concatenación directa
// que tenías antes (la falla de seguridad más grave del proyecto).
// Nota: ya no mandamos "id" -> dejamos que el AUTO_INCREMENT lo genere solo.
$sql = "INSERT INTO pedidos (name, phone_number, delivery_date, dessert_type, payment_type, nit_code, extra_comment)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
 
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sssssss", $name, $phone_number, $delivery_date, $dessert_type, $payment_type, $nit_code, $extra_comment);
$exito = mysqli_stmt_execute($stmt);
 
if ($exito) {
    $_SESSION['mensaje'] = "¡Tu pedido se guardó con éxito! Te contactaremos pronto.";
    $_SESSION['tipo']    = "success";
} else {
    $_SESSION['mensaje'] = "Ocurrió un error al guardar tu pedido. Intenta de nuevo.";
    $_SESSION['tipo']    = "danger";
}
 
mysqli_stmt_close($stmt);
mysqli_close($con);
 
header("Location: ../formulario.php");
exit;
?>
