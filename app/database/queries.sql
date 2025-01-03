-- Удаление таблиц
DROP TABLE IF EXISTS users;

-- Создание таблицы users
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  login TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  password TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'user',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Наполнение таблиц
INSERT INTO 
  users (login, name, password, created_at)
VALUES 
  ('jus_iljas', 'Ilyas Khairullin', '$2y$10$/0na1LI77gxCwM99XAkdp..20VP3kzT2kavZ42OWOhEBmxKPs1vhm', datetime('now', 'localtime')),
  ('jus_doe', 'John Doe', '$2y$10$Ppf8jw5LSX3H/GKd83de3u1/mr0k0ma/x5dYyDoqczw1zEWkSzKey', datetime('now', 'localtime')),
  ('jus_morrison', 'Jack Morrison', '$2y$10$6i/RXObECeheLU7dQTyC4Ov23XJg.h2t8fu6lieJAMShL/l23O9TC', datetime('now', 'localtime'));