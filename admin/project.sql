DROP TABLE IF EXISTS `projects`;

CREATE TABLE `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `project_name` VARCHAR(255) NOT NULL,
  `project_description` TEXT NOT NULL,
  `project_category` VARCHAR(100) NOT NULL,
  `file_path` VARCHAR(500) NULL,
  `file_name` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NULL
);