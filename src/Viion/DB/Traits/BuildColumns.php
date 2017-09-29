<?php

namespace Viion\DB\Traits;

/**
 * Class BuilderColumns
 * @package XIVDB\Utils\Database
 */
trait BuildColumns
{
    /**
     * Build columns
     *
     * @param $columns
     * @return array|mixed|string
     */
    protected function buildColumns($columns)
    {
        // if no columns, just return (columns can be added later)
        if (!$columns) {
            return;
        }
        
        if ($columns == '*') {
            return $columns;
        }
        
        // if not an array, show error
        if (!is_array($columns)) {
            $this->throwError('Invalid columns, should be either: Empty, * or an Array[]');
        }
        
        foreach($columns as $table => $column)
        {
            // if the table is numeric (eg not a table prefix)
            // then assume it's an array of columns
            if (is_numeric($table)) {
                $column = str_replace(' as ', ' AS ', $column);
                $column = explode(' AS ', $column);
                
                if (isset($column[1])) {
                    $columns[$table] = sprintf('`%s` AS `%s`', $column[0], $column[1]);
                } else {
                    $columns[$table] = sprintf('`%s`', $column[0]);
                }
            }
            else
            {
                if ($column == '*') {
                    $columns[$table] = $table .'.*';
                    continue;
                }
                
                if (!is_array($column)) {
                    $this->throwError('Invalid columns in multi-array, should be either: Empty, * or an Array[]');
                }
                
                foreach($column as $i => $col)
                {
                    $col = str_replace(' as ', ' AS ', $col);
                    $col = explode(' AS ', $col);
                    if (isset($col[1])) {
                        $column[$i] = sprintf('%s.`%s` AS `%s`', $table, $col[0], $col[1]);
                    } else {
                        $column[$i] = sprintf('%s.`%s`', $table, $col[0]);
                    }
                }
                
                $columns[$table] = implode(',', $column);
            }
        }
        
        // Group columns
        $columns = implode(',', $columns);
        
        // Special ones
        $specialColumns = [
            '`[count]`' => 'count(*) AS total',
            '`count(*)`' => 'count(*)'
        ];
        
        $columns = str_ireplace(array_keys($specialColumns), $specialColumns, $columns);
        
        return $columns;
    }
    
    /**
     * Get column sql
     *
     * @param $column
     */
    protected function getColumnSqlType($column)
    {
        list($name, $desc, $type, $size) = $column;
        $desc = sprintf('%s - Size: %s', $desc, is_array($size) ? implode(',', $size) : $size);
        
        switch($type) {
            case 'int':
            case 'uint':
            case 'byte':
            case 'sbyte':
            case 'Color':
                $sql = sprintf(SQL::CREATE_INT, $name, $size, $desc);
                break;
            
            case 'bool':
            case 'bit':
            case 'tinyint':
                $sql = sprintf(SQL::CREATE_TINYINT, $name, 1, $desc);
                break;
            
            case 'str':
            case 'char':
            case 'varchar':
            case 'string':
            case 'Image':
                $sql = sprintf(SQL::CREATE_VARCHAR, $name, $size, $desc);
                break;
            
            case 'text':
                $sql = sprintf(SQL::CREATE_TEXT, $name, $desc);
                break;
            
            case 'single':
            case 'double':
            case 'float':
                $size = $size ? implode(',', $size) : '10,5';
                $sql = sprintf(SQL::CREATE_DOUBLE, $name, $size, $desc);
                break;
            
            default:
                $sql = sprintf(SQL::CREATE_INT, $name, $size, sprintf('[ID] %s', $desc));
                break;
        }
        
        return $sql;
    }
}