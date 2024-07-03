CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    inStock BOOLEAN NOT NULL,
    gallery JSON NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(255) NOT NULL,
    attributes JSON NOT NULL,
    prices JSON NOT NULL,
    brand VARCHAR(255) NOT NULL
);