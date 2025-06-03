-- database/sqlite_data.sql

INSERT INTO repartidores (id, nombre) VALUES
  (1, 'pepe'),
  (2, 'juan');

INSERT INTO productos (id, nombre, precio, tipo) VALUES
  (2, 'Barbacoa', 7.00, 'pizza'),
  (3, 'Nórdica',   9.00, 'pizza'),
  (4, 'Los arcos', 8.00, 'pasta'),
  (9, 'xvx',       1.00, '<zx<zx<zx'),
  (10,'cvsvs',   234.00, 'asdasdasd');

INSERT INTO pedidos (id, id_repartidor, direccion, telefono, created_at, updated_at, estado) VALUES
  (12, 1, NULL, NULL, '2025-05-23 16:48:26', '2025-05-23 16:48:26', 'en proceso'),
  (13, NULL, NULL, NULL, '2025-05-23 17:18:41', '2025-05-23 17:18:41', 'en proceso'),
  (15, 1, NULL, NULL, '2025-05-23 15:36:08', '2025-05-23 15:36:08', 'en proceso'),
  (16, NULL, NULL, NULL, '2025-05-26 12:34:56', '2025-05-26 12:34:56', 'en proceso');

INSERT INTO pedido_producto (id_pedido, id_producto, cantidad) VALUES
  (12, 3, 12),
  (13, 2, 1),
  (15, 3, 1),
  (16, 4, 1);
