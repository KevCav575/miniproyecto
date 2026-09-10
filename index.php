<?php 
    require 'conexion.php'; 

    $nombre = $_GET['nombre'] ?? '';
    $categoria = $_GET['categoria'] ?? 0;
    $stock_bajo = $_GET['stock_bajo'] ?? 0;
    $orden = $_GET['orden'] ?? '';

    $stmt = $conn ->prepare("call pa_listar_categorias()");
    $categorias = $stmt->fetchAll();
    $stmt->closeCursor();

    $procedures = [
        'precio_mayor' => 'CALL pa_precio_mayor()',
        'precio_menor' => 'CALL pa_precio_menor()',
        'mas_vendidos' => 'CALL pa_mas_vendidos()',
        'mas_stock' => 'CALL pa_mas_stock()',
        'precio_bajo' => 'CALL pa_precio_bajo()',
        'stock_bajo' => 'CALL pa_stock_bajo()',
    ];

    if($orden != '') {
    $stmt = $conn->prepare($procedures[$orden]);
    $stmt->execute();
    } else {
        $stmt = $conn->prepare("CALL pa_listar_producto(?, ?)");
        $stmt->execute([$nombre, $categoria]);
    }

    $productos = $stmt->fetchAll();
    $stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercado Libre</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script defer src="carrito.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <header class="sticky-top" style="background-color: #FFE600;">
        <div class="container-fluid py-2">
            <div class="row align-items-center g-2">
            <div class="col-12 col-md-2"> 
                <img src="assets/mercado.jpg" alt="Logo de Mercado Libre" class="img-fluid mt-2 mb-2" style="max-height: 50px;">
            </div>
            <div class="col-12 col-md-6"> 
                <form class="d-flex" role="search" method="get" action="index.php">
                    <input class="form-control me-2" type="search" name="nombre"
                        value="<?= $nombre?>"
                        placeholder="¿Qué estás buscando?">
                    <button type="submit" class="btn btn-outline-success">Buscar</button>
                </form> 
            </div>
                <div class="col-12 col-md-4 d-none d-md-block">
                    <a href="https://www.mercadolibre.com.mx/suscripciones/melimas/planes?plan_selected=MEGA#origin=bannermenu-freetrial-agosto&amp;me.audience=freetrialmelimas_adqui_mlm&amp;me.bu=9&amp;me.bu_line=36&amp;me.component_id=banner_menu_web_ml&amp;me.content_id=BM_LOY_FREETRIAL_AGOSTO&amp;me.flow=146&amp;me.logic=campaigns&amp;me.position=0" class="exhibitor__picture"><img src="https://http2.mlstatic.com/D_NQ_750073-MLA115535346101_082026-OO.webp" alt="1 MES GRATIS DE ENVÍOS CON MELI+" class="img-fluid" style="max-height: 60px; object-fit: contain;"></a>
                </div>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg px-3" style="background-color:#FFE600;">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Abrir menú">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav w-100">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button"data-bs-toggle="dropdown" aria-expanded="false">
                                Categorías
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php">Todas</a></li>
                                <?php foreach ($categorias as $cat): ?>
                                    <li><a class="dropdown-item" href="index.php?categoria=<?= $cat['id_categorias'] ?>"><?= $cat['nombre'] ?></a></li>
                                <?php endforeach; ?>
                                <li><hr class="dropdown-divider"></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Ofertas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Ayuda</a>
                        </li>

                        <li class="nav-item ms-auto">
                            <a class="nav-link" href="carrito.php">
                                <img src="assets/carrito.svg" alt="Carrito" style="height: 28px;">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-12">
                <div class="container mb-4">
                    <form method="get" action="index.php" class="row g-2 align-items-center bg-light p-3 border rounded">
                        
                        <div class="col-auto">
                            <select class="form-select" name="orden">
                                <option value="">Ordenar por...</option>
                                <option value="precio_menor" <?= $orden == 'precio_menor' ? 'selected' : '' ?>>Menor precio</option>
                                <option value="precio_mayor" <?= $orden == 'precio_mayor' ? 'selected' : '' ?>>Mayor precio</option>
                                <option value="mas_vendidos" <?= $orden == 'mas_vendidos' ? 'selected' : '' ?>>Más vendidos</option>
                                <option value="mas_stock" <?= $orden == 'mas_stock' ? 'selected' : '' ?>>Mayor stock</option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stock_bajo" name="stock_bajo" value="1" <?= $stock_bajo == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="stock_bajo">Stock bajo</label>
                            </div>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-warning">Filtrar</button>
                            <a href="index.php" class="btn btn-secondary">Limpiar</a>
                        </div>

                    </form>
                </div>
                <h2 class="mb-3">Productos</h2>
 
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    <?php foreach ($productos as $p):
                        $stock = $p['stock'];
                        $minimo = $p['stock_minimo'];
 
                        $img = 'assets/' . $p['imagen'];
                    ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?= $img ?>" class="card-img-top p-3" style="height:180px; object-fit:contain;" alt="<?= $p['nombre'] ?>">
 
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?= $p['nombre'] ?></h6>
 
                                <p class="text-muted small flex-grow-1"><?= $p['descripcion'] ?></p>
 
                                <p class="fs-5 fw-bold mb-2">$ <?= number_format($p['precio'], 2) ?></p>
 
                                <div class="d-flex gap-2 mt-auto">
                                    <input type="number" id="cantidad-<?= $p['id_productos'] ?>" value="1" min="1" class="form-control form-control-sm" style="width:70px;">
                                    <button type="button" class="btn btn-warning btn-sm flex-grow-1"
                                            onclick="agregarCarrito(<?= $p['id_productos'] ?>, '<?= $p['nombre'] ?>', <?= $p['precio'] ?>, document.getElementById('cantidad-<?= $p['id_productos'] ?>').value)">
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>