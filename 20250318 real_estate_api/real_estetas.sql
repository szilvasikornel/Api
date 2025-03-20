CREATE DATABASE real_estate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE real_estate;

CREATE TABLE apartments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    size INT NOT NULL COMMENT 'Méret négyzetméterben',
    rooms INT NOT NULL COMMENT 'Szobák száma',
    price DECIMAL(10,2) NOT NULL COMMENT 'Ár forintban',
    owner_name VARCHAR(100) NOT NULL,
    owner_phone VARCHAR(20) NOT NULL,
    listed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Hirdetés feladási dátuma',
    description TEXT COMMENT 'Lakás rövid leírása',
    image_url VARCHAR(255) DEFAULT NULL COMMENT 'Kép URL a lakásról'
);
INSERT INTO apartments (address, city, postal_code, size, rooms, price, owner_name, owner_phone, description, image_url)
VALUES 
('Kossuth Lajos utca 10.', 'Budapest', '1053', 85, 3, 75000000, 'Kovács János', '+36201234567', 'Kiváló állapotú lakás a belvárosban', 'https://example.com/lakas1.jpg'),
('Petőfi Sándor utca 15.', 'Debrecen', '4025', 65, 2, 45000000, 'Nagy Mária', '+36203334444', 'Felújított panel lakás parkra néző kilátással', 'https://example.com/lakas2.jpg'),
('Táncsics Mihály tér 8.', 'Szeged', '6720', 120, 4, 98000000, 'Szabó Péter', '+36204445555', 'Új építésű családi ház csendes környezetben', 'https://example.com/lakas3.jpg');
