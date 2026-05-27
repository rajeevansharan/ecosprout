CREATE DATABASE IF NOT EXISTS ecosprout_db;
USE ecosprout_db;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','staff') DEFAULT 'customer',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `botanical_name` varchar(150) DEFAULT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `care_instructions` text,
  `image` varchar(255) DEFAULT 'default_plant.jpg',
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plant_id`) REFERENCES `plants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `workshops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `workshop_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workshop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_workshop_user` (`workshop_id`, `user_id`),
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin and staff
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@ecosprout.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Nursery Staff', 'staff@ecosprout.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff');
-- Password for both accounts is 'password'

-- Insert sample plants
INSERT INTO `plants` (`name`, `botanical_name`, `description`, `price`, `category`, `care_instructions`, `image`, `stock`) VALUES
('Monstera Deliciosa', 'Monstera deliciosa', 'A classic indoor plant with beautiful split leaves.', 25.00, 'Indoor', 'Medium indirect light. Water when top 2 inches of soil are dry.', 'Monstera Deliciosa.jpg', 15),
('Snake Plant', 'Dracaena trifasciata', 'Very hardy plant, great for beginners.', 15.00, 'Indoor', 'Low light tolerant. Water sparingly.', 'Snake Plant.webp', 20),
('Tomato Plant', 'Solanum lycopersicum', 'Grow your own delicious tomatoes.', 5.00, 'Outdoor', 'Full sun. Keep soil consistently moist.', 'Tomato Plant.webp', 50),
('Aloe Vera', 'Aloe barbadensis miller', 'Medicinal succulent with fleshy leaves.', 12.00, 'Indoor', 'Bright direct light. Water infrequently.', 'Snake Plant.webp', 30);

-- Insert sample workshops
INSERT INTO `workshops` (`title`, `description`, `date`, `instructor`, `capacity`) VALUES
('Succulent Care for Beginners', 'Learn the basics of choosing, planting, and watering succulents so they thrive in your home.', '2026-06-15 10:00:00', 'Jane Doe (Nursery Specialist)', 15),
('Organic Vegetable Gardening', 'A complete guide to preparing soil, planting seeds, and harvesting organic tomatoes, lettuce, and herbs.', '2026-06-22 14:00:00', 'John Smith (Lead Botanist)', 20),
('Indoor Plant Styling Masterclass', 'Discover how to arrange plants in your living space to enhance lighting, aesthetics, and air purification.', '2026-07-05 11:00:00', 'Emily Rose (Garden Designer)', 12);

-- Insert sample services
INSERT INTO `services` (`name`, `description`, `price`) VALUES
('Garden Design Consultation', 'A 1-hour session with a professional garden designer to plan your home backyard landscape layout.', 50.00),
('Indoor Plant Styling & Setup', 'Our experts select, transport, and place the perfect plants in your office or living room based on light levels.', 75.00),
('Greenhouse Construction Advisory', 'Professional engineering and setup guide to construct customized home greenhouses or propagation chambers.', 120.00);
