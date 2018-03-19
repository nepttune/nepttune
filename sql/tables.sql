CREATE TABLE IF NOT EXISTS `log_error` 
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARBINARY(16)               NOT NULL,
  `url`        VARCHAR(255)                NOT NULL,
  `return_code`VARCHAR(10)                 NOT NULL,
  `datetime`   DATETIME                    NOT NULL
) ENGINE = INNODB;
