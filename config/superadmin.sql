ALTER TABLE saranaprasarana.users MODIFY COLUMN role enum('Admin','User','superadmin') DEFAULT 'User';
INSERT INTO saranaprasarana.users (username, pass, role) VALUES ('superadmin', '$2y$10$fVIpgBoNpk55E1D3Z2X93OhuWyGiPrnh2KbjvEkwcUMCZyuHglHcC', 'superadmin') ON DUPLICATE KEY UPDATE pass='$2y$10$fVIpgBoNpk55E1D3Z2X93OhuWyGiPrnh2KbjvEkwcUMCZyuHglHcC', role='superadmin';
