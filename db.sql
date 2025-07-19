-- 1. USER TABLE
CREATE TABLE users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    User_Role ENUM('admin','warehouse_manager','employee','farmer','customer','retailer','transport_manager','driver') NOT NULL,
    Ref_ID INT,
    Created_At DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. FARMER
CREATE TABLE FARMER (
    FarmerID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Phone VARCHAR(20),
    Farm_Location VARCHAR(200)
);

-- 3. PRODUCT
CREATE TABLE PRODUCT (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    Product_Name VARCHAR(100),
    Product_Type VARCHAR(50),
    Harvest_date DATE,
    Shelf_Life_Days INT,
    FarmerID INT,
    FOREIGN KEY (FarmerID) REFERENCES FARMER(FarmerID)
);

-- 4. CROP_SOWING
CREATE TABLE CROP_SOWING (
    Farmer_ID INT,
    Product_ID INT,
    Sowing_Date DATE,
    field_name VARCHAR(100),
    Seed_Amount_Kg DECIMAL(10,2),
    PRIMARY KEY (Farmer_ID, Product_ID, Sowing_Date, field_name),
    FOREIGN KEY (Farmer_ID) REFERENCES FARMER(FarmerID),
    FOREIGN KEY (Product_ID) REFERENCES PRODUCT(ProductID)
);

-- 5. PACKAGING_BATCH
CREATE TABLE PACKAGING_BATCH (
    Batch_ID INT PRIMARY KEY AUTO_INCREMENT,
    Pack_Date DATE,
    Total_Weight DECIMAL(10,2),
    Product_ID INT,
    FOREIGN KEY (Product_ID) REFERENCES PRODUCT(ProductID)
);

-- 6. WAREHOUSE
CREATE TABLE WAREHOUSE (
    WarehouseID INT PRIMARY KEY AUTO_INCREMENT,
    Location VARCHAR(200),
    Max_Capacity DECIMAL(10,2)
);

-- 7. WAREHOUSE_EMPLOYEE
CREATE TABLE WAREHOUSE_EMPLOYEE (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    Employee_Name VARCHAR(100),
    Role VARCHAR(50),
    Contact VARCHAR(20),
    WarehouseID INT,
    FOREIGN KEY (WarehouseID) REFERENCES WAREHOUSE(WarehouseID)
);

-- 8. PRODUCT_STORAGE
CREATE TABLE PRODUCT_STORAGE (
    Storage_ID INT PRIMARY KEY AUTO_INCREMENT,
    Warehouse_ID INT,
    Batch_ID INT,
    Stored_weight DECIMAL(10,2),
    Employee_ID INT,
    FOREIGN KEY (Warehouse_ID) REFERENCES WAREHOUSE(WarehouseID),
    FOREIGN KEY (Batch_ID) REFERENCES PACKAGING_BATCH(Batch_ID),
    FOREIGN KEY (Employee_ID) REFERENCES WAREHOUSE_EMPLOYEE(EmployeeID)
);

-- 9. SENSOR
CREATE TABLE SENSOR (
    Sensor_ID INT PRIMARY KEY AUTO_INCREMENT,
    Sensor_type VARCHAR(50),
    Status VARCHAR(20),
    Last_reading DATETIME,
    Warehouse_ID INT,
    FOREIGN KEY (Warehouse_ID) REFERENCES WAREHOUSE(WarehouseID)
);

-- 10. DRIVER
CREATE TABLE DRIVER (
    DriverID INT PRIMARY KEY AUTO_INCREMENT,
    Driver_Name VARCHAR(100),
    LicenseNo VARCHAR(30),
    Phone VARCHAR(20)
);

-- 11. VEHICLE
CREATE TABLE VEHICLE (
    Vehicle_ID INT PRIMARY KEY AUTO_INCREMENT,
    Vehicle_Type VARCHAR(50),
    Max_Load DECIMAL(10,2),
    Driver_ID INT,
    FOREIGN KEY (Driver_ID) REFERENCES DRIVER(DriverID)
);

-- 12. SHIPMENT
CREATE TABLE SHIPMENT (
    ShipmentID INT PRIMARY KEY AUTO_INCREMENT,
    Shipment_Date DATE,
    Start_location VARCHAR(200),
    End_location VARCHAR(200),
    Distance DECIMAL(10,2),
    Delivery_status VARCHAR(50),
    Batch_ID INT,
    Vehicle_ID INT,
    Driver_ID INT,
    FOREIGN KEY (Batch_ID) REFERENCES PACKAGING_BATCH(Batch_ID),
    FOREIGN KEY (Vehicle_ID) REFERENCES VEHICLE(Vehicle_ID),
    FOREIGN KEY (Driver_ID) REFERENCES DRIVER(DriverID)
);

-- 13. CUSTOMER
CREATE TABLE CUSTOMER (
    CustomerID INT PRIMARY KEY AUTO_INCREMENT,
    Customer_Name VARCHAR(100),
    Phone VARCHAR(20),
    Address VARCHAR(200)
);

-- 14. RETAILER
CREATE TABLE RETAILER (
    Retailer_ID INT PRIMARY KEY AUTO_INCREMENT,
    Retailer_Name VARCHAR(100),
    Shop_Location VARCHAR(200),
    Phone VARCHAR(20)
);

-- 15. ORDER
CREATE TABLE `ORDER` (
    OrderID INT PRIMARY KEY AUTO_INCREMENT,
    Order_date DATE,
    Ordered_weight DECIMAL(10,2),
    Batch_ID INT,
    Customer_ID INT,
    Retailer_ID INT,
    FOREIGN KEY (Batch_ID) REFERENCES PACKAGING_BATCH(Batch_ID),
    FOREIGN KEY (Customer_ID) REFERENCES CUSTOMER(CustomerID),
    FOREIGN KEY (Retailer_ID) REFERENCES RETAILER(Retailer_ID)
);
