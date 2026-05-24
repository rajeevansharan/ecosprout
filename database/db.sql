CREATE DATABASE IF NOT EXISTS ecosprout_db;
USE ecosprout_db;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `plants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
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
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@ecosprout.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password for admin is 'password'

-- Insert sample plants
INSERT INTO `plants` (`name`, `description`, `price`, `category`, `care_instructions`, `image`, `stock`) VALUES
('Monstera Deliciosa', 'A classic indoor plant with beautiful split leaves.', 25.00, 'Indoor', 'Medium indirect light. Water when top 2 inches of soil are dry.', 'monstera.jpg', 15),
('Snake Plant', 'Very hardy plant, great for beginners.', 15.00, 'Indoor', 'Low light tolerant. Water sparingly.', 'snake_plant.jpg', 20),
('Tomato Plant', 'Grow your own delicious tomatoes.', 5.00, 'Outdoor', 'Full sun. Keep soil consistently moist.', 'tomato.jpg', 50),
('Aloe Vera', 'Medicinal succulent with fleshy leaves.', 12.00, 'Indoor', 'Bright direct light. Water infrequently.', 'aloe_vera.jpg', 30);
