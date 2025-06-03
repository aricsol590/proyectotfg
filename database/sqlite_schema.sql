-- tabla repartidores
CREATE TABLE repartidores (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT
);

-- tabla productos
CREATE TABLE productos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT,
  precio REAL,
  tipo TEXT
);

-- tabla pedidos
CREATE TABLE pedidos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  id_repartidor INTEGER,
  direccion TEXT,
  telefono TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  updated_at TEXT DEFAULT (datetime('now')),
  estado TEXT NOT NULL DEFAULT 'en proceso',
  FOREIGN KEY(id_repartidor) REFERENCES repartidores(id) ON UPDATE CASCADE
);

-- tabla intermedia pedido_producto
CREATE TABLE pedido_producto (
  id_pedido INTEGER,
  id_producto INTEGER,
  cantidad INTEGER,
  PRIMARY KEY (id_pedido, id_producto),
  FOREIGN KEY(id_pedido)  REFERENCES pedidos(id)   ON UPDATE CASCADE,
  FOREIGN KEY(id_producto) REFERENCES productos(id) ON UPDATE CASCADE
);
