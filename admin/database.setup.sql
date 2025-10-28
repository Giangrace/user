-- Create database
CREATE DATABASE IF NOT EXISTS portfolio_db;

-- Use the database
USE portfolio_db;

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    file_path VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data (optional)
INSERT INTO projects (project_name, description, category) VALUES
('Sample Project 1', 'This is a sample web development project demonstrating modern UI/UX principles.', 'Web Development'),
('Sample Project 2', 'Mobile application for task management with cross-platform support.', 'Mobile App'),
('Sample Project 3', 'Logo and branding design for a tech startup company.', 'Design');