CREATE DATABASE IF NOT EXISTS playbal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE playbal;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('player','admin') NOT NULL DEFAULT 'player',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS player_profiles (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    position VARCHAR(50) NULL,
    phone VARCHAR(40) NULL,
    social_media VARCHAR(255) NULL,
    profile_picture VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_profile_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    area VARCHAR(100) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 4.5,
    hourly_price DECIMAL(10,2) DEFAULT 20,
    format VARCHAR(20) DEFAULT '7v7',
    image_url VARCHAR(500) NULL,
    available TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL UNIQUE,
    format VARCHAR(20) NOT NULL,
    positions_json TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    field_id INT NULL,
    title VARCHAR(150) NOT NULL,
    match_date DATE NOT NULL,
    match_time TIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    match_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    max_players INT NOT NULL,
    min_players INT NOT NULL DEFAULT 1,
    required_positions TEXT NULL,
    skill_level VARCHAR(30) DEFAULT 'Casual',
    format VARCHAR(20) NOT NULL DEFAULT '7v7',
    formation_id INT NULL,
    status ENUM('open','full','completed','cancelled') NOT NULL DEFAULT 'open',
    about TEXT NULL,
    image_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_match_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_match_field FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE SET NULL,
    CONSTRAINT fk_match_formation FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS match_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    player_id INT NOT NULL,
    requested_position VARCHAR(50) NULL,
    status ENUM('pending','approved','rejected','removed') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(500) NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_match_player (match_id, player_id),
    CONSTRAINT fk_request_match FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    CONSTRAINT fk_request_player FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_request_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL UNIQUE,
    player_id INT NOT NULL,
    match_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method VARCHAR(30) NOT NULL,
    status ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_request FOREIGN KEY (request_id) REFERENCES match_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_payment_player FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_payment_match FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS lineup_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    request_id INT NOT NULL,
    player_id INT NOT NULL,
    position_code VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_lineup_player (match_id, player_id),
    UNIQUE KEY uq_lineup_position (match_id, position_code),
    CONSTRAINT fk_lineup_match FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    CONSTRAINT fk_lineup_request FOREIGN KEY (request_id) REFERENCES match_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_lineup_player FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO users (id, name, email, password_hash, role) VALUES
(1, 'PlayBal Admin', 'admin@playbal.local', '$2y$12$VnjUEcJHFoQVVDy9LIpdN.b4ASujEw7hQgKs8l2aQUD6W238oOOua', 'admin');

INSERT IGNORE INTO player_profiles (user_id, full_name, position) VALUES (1, 'PlayBal Admin', 'Organizer');

INSERT INTO fields (id,name,area,rating,hourly_price,format,image_url,available) VALUES
(1,'Phnom Penh Football Arena','BKK1',4.8,20,'7v7','https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=800&auto=format&fit=crop',1),
(2,'IZZI Sports Club','Tuol Kork',4.6,18,'5v5','https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?q=80&w=800&auto=format&fit=crop',1),
(3,'Diamond Island Turf Park','Koh Pich',4.9,25,'11v11','https://images.unsplash.com/photo-1522778119026-d647f0596c20?q=80&w=800&auto=format&fit=crop',1),
(4,'Chroy Changvar Riverside Pitch','Chroy Changvar',4.5,16,'7v7','https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?q=80&w=800&auto=format&fit=crop',1),
(5,'Camko City Sports Complex','Sen Sok',4.7,22,'7v7','https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?q=80&w=800&auto=format&fit=crop',1),
(6,'Old Stadium 7s Court','Daun Penh',4.4,15,'5v5','https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=800&auto=format&fit=crop',1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO formations (name,format,positions_json) VALUES
('4-4-2','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LB","label":"Left Back","x":20,"y":72},{"code":"CB1","label":"Centre Back","x":40,"y":76},{"code":"CB2","label":"Centre Back","x":60,"y":76},{"code":"RB","label":"Right Back","x":80,"y":72},{"code":"LM","label":"Left Midfielder","x":18,"y":50},{"code":"CM1","label":"Central Midfielder","x":40,"y":53},{"code":"CM2","label":"Central Midfielder","x":60,"y":53},{"code":"RM","label":"Right Midfielder","x":82,"y":50},{"code":"ST1","label":"Striker","x":42,"y":25},{"code":"ST2","label":"Striker","x":58,"y":25}]'),
('4-3-3','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LB","label":"Left Back","x":20,"y":72},{"code":"CB1","label":"Centre Back","x":40,"y":76},{"code":"CB2","label":"Centre Back","x":60,"y":76},{"code":"RB","label":"Right Back","x":80,"y":72},{"code":"CM1","label":"Central Midfielder","x":30,"y":52},{"code":"CM2","label":"Central Midfielder","x":50,"y":55},{"code":"CM3","label":"Central Midfielder","x":70,"y":52},{"code":"LW","label":"Left Winger","x":20,"y":28},{"code":"ST","label":"Striker","x":50,"y":22},{"code":"RW","label":"Right Winger","x":80,"y":28}]'),
('4-2-3-1','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LB","label":"Left Back","x":20,"y":72},{"code":"CB1","label":"Centre Back","x":40,"y":76},{"code":"CB2","label":"Centre Back","x":60,"y":76},{"code":"RB","label":"Right Back","x":80,"y":72},{"code":"DM1","label":"Defensive Midfielder","x":40,"y":60},{"code":"DM2","label":"Defensive Midfielder","x":60,"y":60},{"code":"LW","label":"Left Winger","x":22,"y":40},{"code":"CAM","label":"Attacking Midfielder","x":50,"y":38},{"code":"RW","label":"Right Winger","x":78,"y":40},{"code":"ST","label":"Striker","x":50,"y":22}]'),
('3-5-2','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"CB1","label":"Centre Back","x":30,"y":76},{"code":"CB2","label":"Centre Back","x":50,"y":79},{"code":"CB3","label":"Centre Back","x":70,"y":76},{"code":"LWB","label":"Left Wing Back","x":12,"y":55},{"code":"CM1","label":"Central Midfielder","x":34,"y":52},{"code":"CM2","label":"Central Midfielder","x":50,"y":55},{"code":"CM3","label":"Central Midfielder","x":66,"y":52},{"code":"RWB","label":"Right Wing Back","x":88,"y":55},{"code":"ST1","label":"Striker","x":42,"y":25},{"code":"ST2","label":"Striker","x":58,"y":25}]'),
('3-4-3','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"CB1","label":"Centre Back","x":30,"y":76},{"code":"CB2","label":"Centre Back","x":50,"y":79},{"code":"CB3","label":"Centre Back","x":70,"y":76},{"code":"LM","label":"Left Midfielder","x":18,"y":52},{"code":"CM1","label":"Central Midfielder","x":40,"y":55},{"code":"CM2","label":"Central Midfielder","x":60,"y":55},{"code":"RM","label":"Right Midfielder","x":82,"y":52},{"code":"LW","label":"Left Winger","x":22,"y":28},{"code":"ST","label":"Striker","x":50,"y":22},{"code":"RW","label":"Right Winger","x":78,"y":28}]'),
('5-3-2','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LWB","label":"Left Wing Back","x":12,"y":70},{"code":"CB1","label":"Centre Back","x":32,"y":76},{"code":"CB2","label":"Centre Back","x":50,"y":79},{"code":"CB3","label":"Centre Back","x":68,"y":76},{"code":"RWB","label":"Right Wing Back","x":88,"y":70},{"code":"CM1","label":"Central Midfielder","x":34,"y":53},{"code":"CM2","label":"Central Midfielder","x":50,"y":55},{"code":"CM3","label":"Central Midfielder","x":66,"y":53},{"code":"ST1","label":"Striker","x":42,"y":25},{"code":"ST2","label":"Striker","x":58,"y":25}]'),
('5-4-1','11v11','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LWB","label":"Left Wing Back","x":12,"y":70},{"code":"CB1","label":"Centre Back","x":32,"y":76},{"code":"CB2","label":"Centre Back","x":50,"y":79},{"code":"CB3","label":"Centre Back","x":68,"y":76},{"code":"RWB","label":"Right Wing Back","x":88,"y":70},{"code":"LM","label":"Left Midfielder","x":20,"y":50},{"code":"CM1","label":"Central Midfielder","x":40,"y":53},{"code":"CM2","label":"Central Midfielder","x":60,"y":53},{"code":"RM","label":"Right Midfielder","x":80,"y":50},{"code":"ST","label":"Striker","x":50,"y":23}]'),
('5v5-2-1-1','5v5','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LB","label":"Defender","x":30,"y":70},{"code":"RB","label":"Defender","x":70,"y":70},{"code":"CM","label":"Midfielder","x":50,"y":48},{"code":"ST","label":"Striker","x":50,"y":24}]'),
('7v7-2-3-1','7v7','[{"code":"GK","label":"Goalkeeper","x":50,"y":90},{"code":"LB","label":"Defender","x":30,"y":72},{"code":"RB","label":"Defender","x":70,"y":72},{"code":"LM","label":"Midfielder","x":20,"y":50},{"code":"CM","label":"Midfielder","x":50,"y":54},{"code":"RM","label":"Midfielder","x":80,"y":50},{"code":"ST","label":"Striker","x":50,"y":24}]')
ON DUPLICATE KEY UPDATE positions_json=VALUES(positions_json), format=VALUES(format);

INSERT INTO matches (admin_id,field_id,title,match_date,match_time,location,match_fee,max_players,min_players,required_positions,skill_level,format,formation_id,status,about,image_url)
SELECT 1, f.id, 'Friday Night Football', '2026-09-25','20:00:00',f.name,3.50,14,10,'GK,Defender,Midfielder,Winger,Striker','Intermediate','7v7',fo.id,'open','Casual 7v7 game. All skill levels welcome. Come have fun and meet new players.',f.image_url
FROM fields f JOIN formations fo ON fo.name='7v7-2-3-1' WHERE f.id=1 AND NOT EXISTS (SELECT 1 FROM matches WHERE title='Friday Night Football');

INSERT INTO matches (admin_id,field_id,title,match_date,match_time,location,match_fee,max_players,min_players,required_positions,skill_level,format,formation_id,status,about,image_url)
SELECT 1, f.id, 'Saturday Morning Kickoff', '2026-09-26','07:00:00',f.name,4.00,10,7,'GK,Defender,Midfielder,Striker','Casual','5v5',fo.id,'open','Early morning friendly football.',f.image_url
FROM fields f JOIN formations fo ON fo.name='5v5-2-1-1' WHERE f.id=2 AND NOT EXISTS (SELECT 1 FROM matches WHERE title='Saturday Morning Kickoff');
