# This file is part of Nepttune (https://www.peldax.com)
#
# Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
#
# This software consists of voluntary contributions made by many individuals
# and is licensed under the MIT license. For more information, see
# <https://www.peldax.com>.

CREATE TABLE IF NOT EXISTS `log_error`(
  `id`          INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address`  VARBINARY(16)               NOT NULL,
  `url`         VARCHAR(255)                NOT NULL,
  `return_code` VARCHAR(10)                 NOT NULL,
  `datetime`    DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `log_cron`(
    `id`          INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100)                NOT NULL,
    `value`       VARCHAR(100)                         DEFAULT NULL,
    `datetime`    DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `subscription` (
  `id`          INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT(10) unsigned        DEFAULT NULL,
  `endpoint`    VARCHAR(511)                NOT NULL,
  `key`         VARCHAR(255)                NOT NULL,
  `token`       VARCHAR(255)                NOT NULL,
  `encoding`    VARCHAR(255)                NOT NULL,
  `active`      TINYINT DEFAULT 1           NOT NULL,
  
  CONSTRAINT `subscription_endpoint_uindex` UNIQUE (`endpoint`),
  INDEX `subscription_active_index` (`active`)
) ENGINE = INNODB;
