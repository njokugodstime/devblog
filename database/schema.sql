CREATE DATABASE IF NOT EXISTS devblog_db;
USE devblog_db;

-- Users (admins and authors)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'author') DEFAULT 'author',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Sample categories
INSERT INTO categories (name, slug) VALUES
('Web Development', 'web-development'),
('Career Advice', 'career-advice'),
('Tech News', 'tech-news');

-- Sample admin user (password: admin123 - hashed below is for 'admin123')
INSERT INTO users (name, email, password, role) VALUES
('Godstime Njoku', 'admin@devblog.com', '$2y$10$8K1p/a0dURXAXHNsQwYnBOWdVQZ4YuOJRXFcAA6qgWjJyfgHm3bYW', 'admin');

-- Sample post
INSERT INTO posts (user_id, category_id, title, slug, content, status) VALUES
(1, 1, 'Welcome to DevBlog', 'welcome-to-devblog', 'This is the first post on DevBlog, a multi-author blogging platform built with PHP and MySQL.', 'published');