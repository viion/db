<?php

namespace Viion\DB;

/**
 * Class SQL
 *
 * @package Viion\DB
 */
class SQL
{
    // build
    const CREATE_TABLE = 'CREATE TABLE `%s` (%s) ENGINE=\'InnoDB\' COLLATE \'utf8_general_ci\';';
    const CREATE_ID = '`id` int(32) NOT NULL';
    const CREATE_INT = '`%s` int(%s) NULL COMMENT \'%s\'';
    const CREATE_TINYINT = '`%s` tinyint(%s) NULL COMMENT \'%s\'';
    const CREATE_VARCHAR = '`%s` varchar(%s) NULL COMMENT \'%s\'';
    const CREATE_DOUBLE = '`%s` double(%s) NULL COMMENT \'%s\'';
    const CREATE_TEXT = '`%s` text NULL COMMENT \'%s\'';
    const CREATE_UPDATED = '`updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
    const SHOW_TABLES = 'SHOW TABLES';
    const SHOW_TABLES_LIKE = 'SHOW TABLES LIKE \'%s\'';
    const SHOW_TABLES_CREATE = 'SHOW CREATE TABLE `%s`';
    const DESCRIBE = 'DESCRIBE `%s`';
    const ALTER_TABLE_ADD = 'ALTER TABLE `%s` ADD %s';
    const ALTER_TABLE_RENAME = 'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` %s NOT NULL';
    const INDEX = 'INDEX `%s` (`%s`)';
    const UNIQUE = 'UNIQUE `%s` (`%s`)';
    const INDEX_GET = 'SHOW INDEX FROM %s WHERE KEY_NAME = \'%s\'';
    
    // manipulate
    const TRUNCATE = 'TRUNCATE TABLE `%s`';
    const SELECT = 'SELECT %s';
    const SELECT_BLANK = 'SELECT';
    const SELECT_DISTINCT = 'SELECT DISTINCT %s';
    const SELECT_COUNT = 'SELECT count(*) AS total';
    const UPDATE = 'UPDATE %s';
    CONST UPDATE_SET = '%s = %s';
    const INSERT = 'INSERT INTO %s';
    const INSERT_VALUES = 'VALUES';
    const DELETE = 'DELETE FROM %s';
    const FROM = 'FROM `%s`';
    const WHERE = 'WHERE';
    const WHERE_OR = ' OR ';
    const WHERE_AND = ' AND ';
    const LEFT_JOIN = 'LEFT JOIN `%s` ON `%s`.`%s` = `%s`.`%s`';
    const LEFT_JOIN_AS = 'LEFT JOIN `%s` AS `%s` ON `%s`.`%s` = `%s`.`%s`';
    const GROUP_BY = 'GROUP BY';
    const ORDER_BY = 'ORDER BY';
    const ORDER_DESC = 'DESC';
    const ORDER_ASC = 'ASC';
    const LIMIT = 'LIMIT %s,%s';
    const SCHEMA = '(`%s`)';
    const DUPLICATE = 'ON DUPLICATE KEY UPDATE %s';
    const DUPLICATE_ENTRY = '`%s`=VALUES(`%s`)';
    const IS_NOT_NULL = '%s IS NOT NULL';
    const IS_NOT_EMPTY = '%s != \'\'';
    const IS_EMPTY = '%s = \'\'';
    const IS_NOT = '%s != %s';
    const FORCE_INDEX = 'FORCE INDEX (%s)';
}