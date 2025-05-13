-- Kurumiçi Yazılım Veritabanı Şeması
-- Bu dosya, Kullanıcı, Yetkilendirme, Stok, Customer, Fatura, Sipariş, Kasa, Banka ve Çek Senet modülleri için tüm tabloları içerir
-- Oluşturma Tarihi: 2025-05-13
-- Kodlama: UTF-8

-- Kullanıcılar tablosu (Kullanıcı Modülü)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Roller tablosu (Yetkilendirme Modülü)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- İzinler tablosu (Yetkilendirme Modülü)
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rol-izin eşleşmeleri tablosu (Yetkilendirme Modülü)
CREATE TABLE role_permissions (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Kullanıcı-rol eşleşmeleri tablosu (Yetkilendirme Modülü)
CREATE TABLE user_roles (
    user_id INT,
    role_id INT,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Stok grupları tablosu (Stok Modülü)
CREATE TABLE stock_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ara gruplar tablosu (Stok Modülü)
CREATE TABLE stock_sub_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES stock_groups(id) ON DELETE CASCADE
);

-- Alt gruplar tablosu (Stok Modülü)
CREATE TABLE stock_sub_sub_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    sub_group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sub_group_id) REFERENCES stock_sub_groups(id) ON DELETE CASCADE
);

-- Ürünler tablosu (Stok Modülü)
CREATE TABLE stock_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 0,
    min_quantity DECIMAL(10,2) DEFAULT 0,
    description TEXT,
    stock_group_id INT NOT NULL,
    sub_group_id INT NOT NULL,
    sub_sub_group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (stock_group_id) REFERENCES stock_groups(id) ON DELETE RESTRICT,
    FOREIGN KEY (sub_group_id) REFERENCES stock_sub_groups(id) ON DELETE RESTRICT,
    FOREIGN KEY (sub_sub_group_id) REFERENCES stock_sub_sub_groups(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Ürün özellikleri tablosu (Stok Modülü)
CREATE TABLE stock_product_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    attribute_type ENUM('color', 'size', 'weight') NOT NULL,
    attribute_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE CASCADE
);

-- Ürün resimleri tablosu (Stok Modülü)
CREATE TABLE stock_product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE CASCADE
);

-- Stok giriş işlemleri tablosu (Stok Modülü)
CREATE TABLE stock_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    entry_date DATETIME NOT NULL,
    invoice_no VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Stok çıkış işlemleri tablosu (Stok Modülü)
CREATE TABLE stock_exits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    exit_date DATETIME NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Müşteri grupları tablosu (Customer Modülü)
CREATE TABLE customer_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Müşteri hesapları tablosu (Customer Modülü)
CREATE TABLE customer_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('customer', 'supplier', 'other') NOT NULL,
    title VARCHAR(255) NOT NULL,
    tax_number VARCHAR(20),
    tax_office VARCHAR(100),
    group_id INT,
    iskonto1 DECIMAL(5,2) DEFAULT 0.00,
    iskonto2 DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (group_id) REFERENCES customer_groups(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Müşteri adresleri tablosu (Customer Modülü)
CREATE TABLE customer_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    type ENUM('invoice', 'delivery', 'other') NOT NULL,
    title VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE CASCADE
);

-- Müşteri yetkili iletişim bilgileri tablosu (Customer Modülü)
CREATE TABLE customer_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE CASCADE
);

-- Müşteri işlemleri tablosu (Customer Modülü)
CREATE TABLE customer_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    type ENUM('purchase', 'sale', 'payment', 'collection') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    transaction_date DATETIME NOT NULL,
    invoice_no VARCHAR(50),
    invoice_address_id INT,
    delivery_address_id INT,
    description TEXT,
    stock_entry_id INT,
    stock_exit_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (invoice_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (stock_entry_id) REFERENCES stock_entries(id) ON DELETE SET NULL,
    FOREIGN KEY (stock_exit_id) REFERENCES stock_exits(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Faturalar tablosu (Fatura Modülü)
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('sale', 'purchase', 'return', 'proforma') NOT NULL,
    customer_id INT NOT NULL,
    invoice_date DATETIME NOT NULL,
    due_date DATETIME,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    net_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    invoice_address_id INT,
    delivery_address_id INT,
    description TEXT,
    status ENUM('paid', 'pending', 'partially_paid', 'canceled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (invoice_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Fatura kalemleri tablosu (Fatura Modülü)
CREATE TABLE invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE RESTRICT
);

-- Siparişler tablosu (Sipariş Modülü)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    order_date DATETIME NOT NULL,
    delivery_date DATETIME,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'in_production', 'ready_for_shipment', 'shipped', 'completed', 'canceled') NOT NULL DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Sipariş kalemleri tablosu (Sipariş Modülü)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    invoiced BOOLEAN NOT NULL DEFAULT FALSE,
    invoice_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES stock_products(id) ON DELETE RESTRICT,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
);

-- Kasalar tablosu (Kasa Modülü)
CREATE TABLE cash_registers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    branch_id INT,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Kasa işlemleri tablosu (Kasa Modülü)
CREATE TABLE cash_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cash_register_id INT NOT NULL,
    type ENUM('in', 'out', 'virman', 'tahsilat', 'odeme') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    transaction_date DATETIME NOT NULL,
    customer_id INT,
    invoice_id INT,
    order_id INT,
    description TEXT,
    status ENUM('pending', 'controlled', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Banka hesapları tablosu (Banka Modülü)
CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    branch_code VARCHAR(20),
    branch_name VARCHAR(100),
    account_number VARCHAR(50),
    iban VARCHAR(34) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    branch_id INT,
    balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'passive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Banka işlemleri tablosu (Banka Modülü)
CREATE TABLE bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_account_id INT NOT NULL,
    type ENUM('in', 'out', 'havale', 'eft', 'virman', 'tahsilat', 'odeme') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    transaction_date DATETIME NOT NULL,
    customer_id INT,
    invoice_id INT,
    order_id INT,
    description TEXT,
    status ENUM('pending', 'controlled', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Çek/Senet tablosu (Çek Senet Modülü)
CREATE TABLE checks_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('check', 'note') NOT NULL,
    document_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    issue_date DATETIME NOT NULL,
    due_date DATETIME NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    bank_id INT,
    check_number VARCHAR(50),
    serial_number VARCHAR(50),
    invoice_id INT,
    order_id INT,
    description TEXT,
    status ENUM('pending', 'due', 'collected', 'paid', 'returned', 'protested') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP NULL,
    updated_by INT,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (bank_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Çek/Senet işlemleri tablosu (Çek Senet Modülü)
CREATE TABLE check_note_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    check_note_id INT NOT NULL,
    type ENUM('collection', 'payment') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'TL',
    transaction_date DATETIME NOT NULL,
    method ENUM('cash', 'bank', 'cash_register') NOT NULL,
    bank_account_id INT,
    cash_register_id INT,
    description TEXT,
    status ENUM('pending', 'controlled', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (check_note_id) REFERENCES checks_notes(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);