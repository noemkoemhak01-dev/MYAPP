FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'root';
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';
CREATE USER IF NOT EXISTS 'enews'@'%' IDENTIFIED WITH mysql_native_password BY 'enews';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON enews.* TO 'enews'@'%';
FLUSH PRIVILEGES;
