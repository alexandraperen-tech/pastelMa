<?php
// session_start() SIEMPRE debe ir antes de cualquier salida HTML,
// por eso va en la primerísima línea del archivo.
session_start();

include('Backend/connection.php');

$con = connection();

$sql = "SELECT * FROM pedidos ORDER BY id DESC";
$query = mysqli_query($con, $sql);

// Si la consulta falla, mysqli_query() regresa "false" en vez de un resultado.
// Sin este chequeo, el while() de abajo intenta leer "false" como si fuera
// una tabla de resultados, y ahí es donde salta el error que viste.
// Con esto, en vez de adivinar, vas a ver la razón EXACTA (tabla mal escrita,
// columna que no existe, etc.)
if (!$query) {
    die('<div style="padding:2rem; font-family:sans-serif; color:#900; background:#fdecea;">
            <strong>Error en la consulta SQL:</strong> ' . mysqli_error($con) . '
         </div>');
}

$hoy = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>

     <!-- LINK DE BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- LINK DE CSS -->
    <link rel="stylesheet" href="styles.css">

    <!-- LINK DE BOOTSTRAP ICONS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
     <header>
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand me-5" href="#"><img src="imgs/Logu.png" alt="" style="height: 80px;"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        <li class="nav-item">

                            <a class="nav-link active nav-linking-dark" aria-current="page" href="index.html">Home</a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link active nav-linking-dark" href="conocenos.html">Conócenos</a>

                        </li>

                        <li class="nav-item dropdown">    
                            
                            <a class="nav-link active nav-linking-dark" href="catalogo.html">Catálogo</a>
                        
                        </li>

                        <li class="nav-item">
                            <a class="nav-link active nav-linking-dark" href="formulario.php">Ordena ya</a>
                        </li>

                    </ul>
                    <div>
                        <!-- Aquí va el ícono de carrito -->
                    </div>
                    </div>
                </div>
            </nav>
    </header>


    <main>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="container mt-4">
                <div class="alert alert-<?= htmlspecialchars($_SESSION['tipo']) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php
                // Se muestra UNA sola vez: lo borramos apenas lo leemos para
                // que no reaparezca si el usuario refresca la página.
                unset($_SESSION['mensaje'], $_SESSION['tipo']);
            ?>
        <?php endif; ?>

        <section class="banner">
            <h1 class="titles c text-center display-4" style="padding-top: 80px;">¡Ordena ahora!</h1>
            <div class="d-flex align-items-center justify-content-center flex-column mt-3">
                <p class="w-50 text-center">Ordena uno de nuestros pasteles personalizados usando el formulario y disfruta de tu postre único y con esencia.</p>
            </div>
        </section>


        <section>
            <div class="container-fluid">
                <div class="container">
                    <div class="row">
                        <form action="Backend/insert.php" method="POST">
                            <div class="col-lg-8">
                                <h1 class="titles c7 display-5 ms-4 mt-5 h1-header">Detalles del pedido</h1>
                                <p class="ms-4">
                                    ¡Ingresa tus datos en el formulario para contactar con nosotros de manera directa! Brindamos todo tipo de información y excelente atención.
                                </p>
                                <div class="form-wrapper">
                                
                                    <!-- Card -->
                                    <div class="p-5 shadow-pro rounded-5">
                                
                                    <!-- ① Datos personales -->
                                    <p class="section-label">DATOS DEL CLIENTE</p>
                                
                                    <div class="row g-3 mb-3">
                                        <div class="col-12">
                                        <label for="full_name" class="form-label text-muted">Nombre completo</label>
                                        <input
                                            type="text"
                                            id="full_name"
                                            name="full_name"
                                            class="form-control"
                                            required 
                                        />
                                        </div>
                                
                                        <div class="col-sm-6">
                                        <label for="phone_number" class="form-label text-muted">Número de teléfono</label>
                                        <input
                                            type="tel"
                                            id="phone_number"
                                            name="phone_number"
                                            class="form-control"
                                            placeholder="+(502) 0000-0000"
                                            required 
                                        />
                                        </div>
                                
                                        <div class="col-sm-6">
                                        <label for="event_date" class="form-label text-muted">Fecha de entrega</label>
                                        <input
                                            type="date"
                                            id="event_date"
                                            name="delivery_date"
                                            class="form-control"
                                            min="<?= $hoy ?>"
                                            required 
                                        />
                                        </div>
                                    </div>
                                
                                    <!-- ② Detalles del postre -->
                                    <p class="section-label mt-4">DETALLES DEL POSTRE</p>
                                
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-12">
                                            <label for="dessert_type" class="form-label text-muted">Tipo de postre</label>
                                            <select id="dessert_type" name="dessert_type" class="form-select" required >
                                                <option value="" disabled selected>Selecciona una opción</option>
                                                <option value="Croissant-Eiffel">Croissant-Eiffel</option>
                                                <option value="Rol de canela">Rol de canela</option>
                                                <option value="Strawberry Cupcakes">Strawberry Cupcakes</option>
                                                <option value="Tartaleta">Tartaleta</option>
                                                <option value="Tiramisú">Tiramisú</option>
                                                <option value="Red Velvet">Red Velvet</option>
                                                <option value="Churros">Churros</option>
                                                <option value="Trenzas Rellenas">Trenzas Rellenas</option>
                                            </select>
                                        </div>
                    
                                    </div>

                                    <!-- ③ Comentarios -->
                                    <p class="section-label mt-4">DETALLES DE FACTURACIÓN</p>

                                    <div class="row g-3 mb-3">
                                        <div class="col-lg-6">
                                            <label for="payment_type" class="form-label text-muted">Método de pago</label>
                                            <select id="payment_type" name="payment_type" class="form-select" required >
                                                <option value="" disabled selected>Selecciona una opción</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="POS">POS al momento de entrega</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-6">
                                        <label for="nit_code" class="form-label text-muted">NIT</label>
                                        <input
                                            type="text"
                                            id="nit_code"
                                            name="nit_code"
                                            class="form-control"
                                            placeholder="CF o número de NIT"
                                            required 
                                        />
                                        </div>
                    
                                    </div>

                                    <!-- ③ Comentarios -->
                                    <p class="section-label mt-4">INFORMACIÓN ADICIONAL</p>
                                
                                    <div class="mb-4">
                                        <label for="extra_comments" class="form-label text-muted">Comentarios extra</label>
                                        <textarea
                                        id="extra_comments"
                                        name="extra_comment"
                                        class="form-control"
                                        placeholder="Alergias, colores específicos, mensaje en el pastel, referencias de diseño…"
                                        ></textarea>
                                    </div>
                                
                                    <!-- Submit -->
                                    <button type="submit" class="btn btn-order w-100">
                                        Enviar orden
                                    </button>
                                
                                    </div><!-- /form-card -->
                                </div><!-- /form-wrapper -->
                            </div>
                        </form>
                        <div class="col-4 mt-5">
                        
                            <div class="">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </section>

        <section>
            <div class="container-fluid">
                <div class="container my-5">
                    <h2 class="titles c6 mb-4">Pedidos recibidos</h2>

                    <?php if (mysqli_num_rows($query) === 0): ?>

                        <p class="orders-empty">Todavía no hay pedidos registrados.</p>

                    <?php else: ?>

                        <div class="orders-card">
                          <div class="table-scroll">
                            <table class="table table-pedidos mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 text-dark">ID</th>
                                        <th class="text-dark">Nombre</th>
                                        <th class="text-dark">Teléfono</th>
                                        <th class="text-dark">Fecha de entrega</th>
                                        <th class="text-dark">Tipo de postre</th>
                                        <th class="text-dark">Método de pago</th>
                                        <th class="text-dark">NIT</th>
                                        <th class="text-dark">Comentario extra</th>
                                        <th class="pe-4 text-end text-dark">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_array($query)): ?>
                                    <tr>
                                        <td class="ps-4"> <?= htmlspecialchars($row['id']) ?> </td>
                                        <td> <?= htmlspecialchars($row['name']) ?> </td>
                                        <td> <?= htmlspecialchars($row['phone_number']) ?> </td>
                                        <td> <?= htmlspecialchars($row['delivery_date']) ?> </td>
                                        <td> <?= htmlspecialchars($row['dessert_type']) ?> </td>
                                        <td> <?= htmlspecialchars($row['payment_type']) ?> </td>
                                        <td> <?= htmlspecialchars($row['nit_code']) ?> </td>
                                        <td> <?= htmlspecialchars($row['extra_comment']) ?> </td>
                                        <td class="pe-4 text-end">
                                            <a href="Backend/delete.php?id=<?= $row['id']?>"
                                               class="btn-delete"
                                               onclick="return confirm('¿Cancelar este pedido? Esta acción no se puede deshacer.');">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                          </div>
                        </div>

                    <?php endif; ?>

                    <?php mysqli_close($con); // Ya no necesitamos la conexión de aquí en adelante ?>

                </div>
            </div>
        </section>
                        

          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </main>

    
    
    <footer>
        <div class="container-fluid bc6" >
            <div class="container pt-5 pb-4 h-100">
                <div style="border-bottom: #fff 2px solid;" class="pb-5">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="imgs/Logu-obs.png" alt="" class="mb-5" style="height: 80px;">  
                        </div>
                        <div class="col-2  text-white">
                            <h5 class="fw-bold">Enlaces</h5>
                            <p>Inicio</p>
                            <p>Conócenos</p>
                            <p>Productos</p>
                        </div>
                        <div class="col-md-3 text-white">
                            <h5 class="fw-bold">Información de contacto</h5>
                            <p><i class="bi bi-alarm-fill"></i> Horario: 7 a. m.–9 p. m.</p>
                            <p><i class="bi bi-envelope-at-fill"></i> Email: pedidos@cristy.com</p>
                        </div>
                        <div class="col-md-2 text-white">
                            <h5 class="fw-bold">Cobertura</h5>
                            <p>Disponibles: 7 a. m.–9 p. m.</p>
                        </div>
                    </div>
                </div>
                <div class="pt-4" >
                    <div class="row">

                        <div class="col-8"> <p style="color: #fff;" style="height: 100%;">© Copyright 2026 | Todos los derechos reservados </p></div>
                        <div class="col-md-4 text-white text-end pe-4 pb-4 mt-auto">
                            <a href="https://www.instagram.com/" target="blank" class="footer-a">

                                <i class="bi bi-instagram footer-icons"></i>

                            </a>
                            <a href="https://www.facebook.com" target="blank" class="footer-a">

                                <i class="bi bi-facebook footer-icons"></i>

                            </a>
                            <a href="https://www.whatsapp.com/" target="blank" class="footer-a">

                                <i class="bi bi-whatsapp footer-icons"></i>

                            </a>
                            <a href="https://x.com/" target="blank" class="footer-a">

                                <i class="bi bi-twitter-x footer-icons"></i>

                            </a>
                        </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </footer>



</body>
</html>
