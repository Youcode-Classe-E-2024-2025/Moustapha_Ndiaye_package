CREATE DATABASE IF NOT EXISTS PackageManagement;
USE PackageManagement;

CREATE TABLE IF NOT EXISTS Authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    author_id INT,
    FOREIGN KEY (author_id) REFERENCES Authors(id) ON DELETE SET NULL,
    UNIQUE (name, author_id)
);

CREATE TABLE IF NOT EXISTS Versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT,
    num_version VARCHAR(50) NOT NULL,
    release_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES Packages(id) ON DELETE CASCADE,
    UNIQUE (package_id, num_version)
);

CREATE TABLE IF NOT EXISTS Tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Packages_Tags (
    package_id INT,
    tag_id INT,
    PRIMARY KEY (package_id, tag_id),
    FOREIGN KEY (package_id) REFERENCES Packages(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES Tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Dependencies (
    package_id INT,
    depends_on_package_id INT,
    PRIMARY KEY (package_id, depends_on_package_id),
    FOREIGN KEY (package_id) REFERENCES Packages(id) ON DELETE CASCADE,
    FOREIGN KEY (depends_on_package_id) REFERENCES Packages(id) ON DELETE CASCADE,
    CHECK (package_id <> depends_on_package_id)
);
