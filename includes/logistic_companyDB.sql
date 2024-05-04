create database logistic_companyDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

create table company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(50) NOT NULL
);

create table address (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_type VARCHAR(50) NOT NULL,
    country VARCHAR(50) NOT NULL,
    city VARCHAR(50) NOT NULL,
    street VARCHAR(50) NOT NULL,
    street_number INT NOT NULL
);

create table office (
    id INT AUTO_INCREMENT PRIMARY KEY,
    office_name VARCHAR(50) NOT NULL,
    company_id INT NOT NULL,
    address_id INT NOT NULL,
    FOREIGN KEY (company_id) REFERENCES company(id),
    FOREIGN KEY (address_id) REFERENCES address(id)
);

create table user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    office_id INT DEFAULT NULL,
    FOREIGN KEY (office_id) REFERENCES office(id)
);

create table price (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weight_class VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00
);

create table shipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    statusShipment VARCHAR(255) NOT NULL,
    ship_weight DECIMAL(10,2) DEFAULT 0.00,
    passenger_amount INT DEFAULT 0,
    date_sent DATETIME NOT NULL,
    date_received DATETIME DEFAULT NULL,
    deliver_from_user_id INT NOT NULL,
    deliver_to_user_id INT DEFAULT NULL,
    deliverer_user_id INT NOT NULL,
    registered_by_user_id INT NOT NULL,
    from_address_id INT NOT NULL,
    to_address_id INT NOT NULL,
    delivery_contact_info VARCHAR(255) DEFAULT NULL,
    exact_price DECIMAL(10,2) NOT NULL,
    is_paid BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (deliver_from_user_id) REFERENCES user(id),
    FOREIGN KEY (deliver_to_user_id) REFERENCES user(id),
    FOREIGN KEY (deliverer_user_id) REFERENCES user(id),
    FOREIGN KEY (registered_by_user_id) REFERENCES user(id),
    FOREIGN KEY (from_address_id) REFERENCES address(id),
    FOREIGN KEY (to_address_id) REFERENCES address(id)
);

create table role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id)
);