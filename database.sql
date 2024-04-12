CREATE TABLE Associate (
    AssociateName VARCHAR(255) NOT NULL,
    AssociateID INT PRIMARY_KEY NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Commission DECIMAL NOT NULL,
    Address VARCHAR(255) NOT NULL,
);



CREATE TABLE Quote(
    Quote_ID INT PRIMARY KEY AUTO_INCREMENT,

    CustomerName VARCHAR(255) NOT NULL,
    Address VARCHAR(255) NOT NULL,
    ContactInfo VARCHAR(255) NOT NULL,
    SecretNote VARCHAR(255) NOT NULL,

    Discount INT,
    Price FLOAT NOT NULL,

    AssociateID INT NOT NULL,
    Status VARCHAR(255) NOT NULL,
    Date TIMESTAMP,

    FOREIGN KEY (AssociateID) REFERENCES Associate(AssociateID)
);



create table LineItem (
    ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    DESCRIPTION VARCHAR(255),
    PRICE INT NOT NULL,
    Quote_ID INT NOT NULL,

    FOREIGN KEY (Quote_ID) REFRERENCES Quote(Quote_ID)
)