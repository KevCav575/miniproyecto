
CREATE DATABASE mercado;
USE mercado;

CREATE TABLE categorias(
	id_categorias int AUTO_INCREMENT PRIMARY KEY,
	nombre varchar(100) NOT NULL
);

CREATE TABLE productos(
	id_productos INT AUTO_INCREMENT PRIMARY KEY,
	nombre varchar(150) NOT NULL,
	descripcion varchar(300) NOT NULL,
	precio decimal(10,2) NOT NULL,
	stock int NOT NULL DEFAULT 0,
	stock_minimo int NOT NULL DEFAULT 5,
	unidades_vendidas int NOT NULL DEFAULT 0,
	imagen varchar(255) DEFAULT NULL,
	id_categorias int NOT NULL,
	creado timestamp DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (id_categorias) REFERENCES categorias(id_categorias)
);

CREATE TABLE ventas(
	id_ventas int AUTO_INCREMENT PRIMARY KEY,
	folio varchar(20) NOT NULL,
	id_productos int NOT NULL,
	cantidad int NOT NULL, 
	precio_unitario decimal(10,2) NOT NULL, 
	fecha_venta timestamp DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (id_productos) REFERENCES productos(id_productos)
);

INSERT INTO categorias (nombre) VALUES
('Cómputo'),('Línea blanca'), ('Herramientas'), ('Papelería'), ('Mascotas');

INSERT INTO productos (nombre, descripcion, precio, stock, stock_minimo, unidades_vendidas, id_categorias) VALUES
('Monitor AOC 24" 75Hz', 'Panel IPS, HDMI y VGA, sin bordes', 2799.00, 22, 5, 310, 1),
('Teclado mecánico Redragon', 'Switch rojo, retroiluminado RGB', 899.00, 4, 6, 480, 1),
('Disco SSD Kingston 480GB', 'SATA III, lectura 500 MB/s', 649.00, 60, 10, 720, 1),
('Refrigerador Whirlpool 14 pies', 'Dos puertas, acabado grafito', 13499.00, 3, 2, 45, 2),
('Horno de microondas Mabe 0.9', '900 W, plato giratorio de vidrio', 2199.00, 18, 5, 130, 2),
('Taladro percutor DeWalt 1/2"', '650 W, incluye maletín', 1899.00, 9, 4, 260, 3),
('Juego de desarmadores 32 pzas', 'Punta magnética, cromo vanadio', 449.00, 75, 15, 390, 3),
('Cinta métrica 8 m', 'Carcasa reforzada, freno automático', 189.00, 120, 20, 510, 3),
('Cuaderno profesional 100 hojas', 'Raya, pasta dura, tamaño carta', 65.00, 300, 40, 880, 4),
('Croquera para dibujo A4', '120 g/m², 50 hojas', 145.00, 5, 8, 95, 4),
('Croquetas para perro adulto 15 kg', 'Sabor pollo y cereales', 899.00, 30, 6, 420, 5),
('Rascador para gato con torre', 'Yute natural, 90 cm de alto', 749.00, 2, 3, 70, 5);

INSERT INTO ventas (folio, id_productos, cantidad, precio_unitario, fecha_venta) VALUES
('F001',  9,  3,    65.00, '2026-05-14 09:12:00'),
('F001',  8,  1,   189.00, '2026-05-14 09:12:00'),
('F002',  3,  1,   599.00, '2026-05-14 11:40:00'),
('F003',  1,  1,  2799.00, '2026-05-18 16:05:00'),
('F003',  2,  1,   899.00, '2026-05-18 16:05:00'),
('F003',  3,  1,   599.00, '2026-05-18 16:05:00'),
('F004', 11,  2,   899.00, '2026-05-22 10:30:00'),
('F005',  7,  1,   449.00, '2026-06-02 13:15:00'),
('F005',  8,  2,   189.00, '2026-06-02 13:15:00'),
('F005',  6,  1,  1899.00, '2026-06-02 13:15:00'),
('F006',  9, 10,    65.00, '2026-06-07 18:22:00'),
('F007', 12,  1,   749.00, '2026-06-11 12:00:00'),
('F008',  5,  1,  2199.00, '2026-06-19 15:47:00'),
('F009',  3,  2,   649.00, '2026-07-01 09:55:00'),
('F010',  6,  1,  1899.00, '2026-07-08 14:20:00'),
('F010',  7,  2,   449.00, '2026-07-08 14:20:00'),
('F011',  1,  2,  2799.00, '2026-07-15 17:03:00'),
('F011',  3,  3,   649.00, '2026-07-15 17:03:00'),
('F012', 11,  1,   899.00, '2026-07-24 11:11:00'),
('F012',  9,  5,    65.00, '2026-07-24 11:11:00'),
('F013',  2,  2,   899.00, '2026-08-03 10:05:00'),
('F013',  8,  4,   189.00, '2026-08-03 10:05:00'),
('F014',  5,  2,  2199.00, '2026-08-11 16:38:00'),
('F014', 12,  1,   749.00, '2026-08-11 16:38:00'),
('F015',  7,  3,   449.00, '2026-08-19 12:50:00'),
('F015',  9,  8,    65.00, '2026-08-19 12:50:00'),
('F015',  8,  2,   189.00, '2026-08-19 12:50:00'),
('F016',  1,  1,  2799.00, '2026-08-24 09:30:00'),
('F016',  2,  1,   899.00, '2026-08-24 09:30:00');


UPDATE productos p
SET p.unidades_vendidas = (
    SELECT IFNULL(SUM(v.cantidad), 0)
    FROM ventas v
    WHERE v.id_productos = p.id_productos
);

SELECT id_productos, nombre, unidades_vendidas, stock, stock_minimo
FROM productos
ORDER BY unidades_vendidas DESC;

SELECT p.nombre, p.precio, p.stock, c.nombre AS categoria
FROM productos p
LEFT JOIN categorias c USING (id_categorias)
WHERE p.nombre LIKE '%croquetas%'
ORDER BY p.unidades_vendidas DESC;

CREATE PROCEDURE pa_listar_categorias()
BEGIN
    SELECT id_categorias, nombre
    FROM categorias
    ORDER BY nombre;
END;



CREATE PROCEDURE pa_listar_producto(
	IN pa_nombre varchar(255),
	IN pa_categoria int
)
BEGIN 
	SELECT p.id_productos, p.nombre, p.descripcion, p.precio, p.stock, p.stock_minimo, p.imagen, c.nombre AS categoria
	FROM productos p 
	INNER JOIN categorias c ON p.id_categorias = c.id_categorias
	WHERE (pa_nombre = '' OR p.nombre LIKE CONCAT('%', pa_nombre, '%'))
	AND (pa_categoria = 0 OR p.id_categorias = pa_categoria);
END;

CREATE PROCEDURE pa_precio_mayor()
BEGIN
	SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    ORDER BY precio DESC;
END;

CREATE PROCEDURE pa_precio_menor()
BEGIN
	SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    ORDER BY precio ASC;
END;

CREATE PROCEDURE pa_mas_vendidos()
BEGIN
	SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    ORDER BY unidades_vendidas DESC;
END;

CREATE PROCEDURE pa_mas_stock()
BEGIN
	SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    ORDER BY stock DESC;
END;

CREATE PROCEDURE pa_buscar_productos(
    IN pa_nombre VARCHAR(150)
)
BEGIN
    SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    WHERE nombre LIKE CONCAT('%', pa_nombre, '%')
    ORDER BY nombre;
END;

CREATE PROCEDURE pa_precio_bajo()
BEGIN
    SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    WHERE precio < (SELECT AVG(precio) FROM productos)
    ORDER BY precio;
END;

CREATE PROCEDURE pa_stock_bajo()
BEGIN
    SELECT id_productos, nombre, descripcion, precio, stock, stock_minimo, imagen
    FROM productos
    WHERE stock <= stock_minimo
    ORDER BY stock;
END;


