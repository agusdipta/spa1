-- SQL schema for spa reservation site
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL DEFAULT 'Luxury Spa',
  subtitle VARCHAR(200) DEFAULT NULL,
  about_html TEXT,
  hero_image VARCHAR(255) DEFAULT 'assets/hero.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DOUBLE NOT NULL DEFAULT 0,
  duration_min INT NOT NULL DEFAULT 60,
  description TEXT,
  image_path VARCHAR(255),
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  service_id INT NULL,
  date_str VARCHAR(20),
  time_str VARCHAR(20),
  notes TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'NEW',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_res_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (username: admin / password: admin123) - CHANGE AFTER IMPORT
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$2w8dF7T2GJ2cXG4Rnn3GmeGfW8wKf5x9V0bI7U1yU6b6mw3hWb.2i'); -- hash of 'admin123'

-- Seed settings
INSERT INTO settings (title, subtitle, about_html, hero_image)
VALUES ('Kupu Kupu Mas Spa', 'Modern Bali-inspired treatments', '<p>We are a boutique spa offering curated wellness experiences with premium products and expert therapists.</p>', 'assets/hero.jpg');
