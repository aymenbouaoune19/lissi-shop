CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  short_name VARCHAR(80) NOT NULL,
  category VARCHAR(40) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  sizes VARCHAR(120) NOT NULL DEFAULT 'One size',
  color VARCHAR(60) NOT NULL DEFAULT 'Natural',
  image_color VARCHAR(20) NOT NULL DEFAULT '#e4ebe1',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  wilaya VARCHAR(80) NOT NULL,
  commune VARCHAR(100) NOT NULL,
  delivery_method VARCHAR(40) NOT NULL,
  shipping_cost DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'New',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);
INSERT INTO products (name, short_name, category, description, price, stock, sizes, color, image_color) VALUES
('Satin draped blouse','Satin blouse','Women','A softly draped everyday blouse.',4200,12,'S, M, L','Rose','#e9d8cc'),
('Everyday canvas tote','Canvas tote','Accessories','A roomy canvas carryall.',2800,8,'One size','Sage','#d9e0db'),
('Relaxed linen shirt','Linen shirt','Men','A light relaxed-fit linen shirt.',5600,5,'M, L, XL','Sky','#d6dde5'),
('Soft knit cardigan','Knit cardigan','Women','A soft layer for cooler evenings.',6400,7,'S, M, L','Oat','#e6e0d2'),
('Minimal leather wallet','Leather wallet','Accessories','A compact wallet with clean lines.',3500,15,'One size','Tan','#d8c4b3'),
('Cotton lounge set','Lounge set','Men','An easy cotton set for slow days.',7800,3,'M, L, XL','Lilac','#dedce7');
