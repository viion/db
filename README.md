# Database

A custom abstract layer was made for XIVDB. I couldn't find any I liked and 
Doctrine ORM entity manager was overkill for the hugely dynamic nature of the 
game files. Thus a custom implementation was born! It is MySQL ONLY!

## Features

- PHP 7 compliant
- PDO Integration

---

## API

### TRUNCATE (RESETS)

```php
QueryBuilder->truncate($table)
```
- `$table` The table to truncate


### SELECT (RESETS)
```php
QueryBuilder->select($columns = false, $isDistinct = false)
```
- `$columns` The columns to select, eg:
  - An array: `['colA', 'colB']`
  - All columns: `'*'` *Any other string will fail*
  - Empty assumes `addColumns()` will be used

Multi-dimentional arrays can be used to prefix columns with tables, eg:
```php
QueryBuilder
  ->select([
      'members' => ['id', 'username', 'avatar AS picture'],
      'comments' => ['id', 'subject', 'message AS content'],
  ]);
```
```mysql
SELECT 
  members.`id`,members.`username`,members.`avatar` AS `picture`,
  comments.`id`,comments.`subject`,comments.`message` AS `content`
```
```php
QueryBuilder
  ->select([
      'members' => '*',
      'comments' => ['post'],
  ]);
```
```mysql
SELECT members.*,comments.`post`
```
  
  
### ADD COLUMNS
```php
QueryBuilder->addColumns($columns = '*')
```
- Add new columns onto the select section.


### UPDATE (RESETS)
```php
QueryBuilder->update($table)
```
- Update a table


### INSERT (RESETS)
```php
QueryBuilder->insert($table)
```
- Insert into a table


### DELETE (RESETS)
```php
QueryBuilder->delete($table)
```
- Delete from a table

### SET
```php
QueryBuilder->set($column, $value = null)
```
- Set some data, usually for UPDATE, eg:
  - `set('username', 'mynewname')`
  
### VALUES
```php
QueryBuilder->values($values, $autoBind = false);
```
- **Requires: SCHEMA**
- Add some new values, usually for INSERT, eg:
  - `values([ 'mynewname', 'mynewpassword'])`
  - Setting auto bind will bind all params to a random bind, eg: 'mynename = :129301'


### SCHEMA
```php
QueryBuilder->schema($values)
```
- used mainly for `values()` by explaining the schema, eg:
  - `schema(['username', 'password'])`
  

### COUNT (RESETS)
```php
QueryBuilder->count()
```
- Perform a count as:
  - `count(*) AS total`
  - Removes all existing columns in the table.


### FROM
```php
QueryBuilder->from($table)
```
- The table to select from.


### WHERE
```php
QueryBuilder->where($condition, $equal = 'AND)
```
- Perform a WHERE query, eg:
  - `where('user_id = 3')`
  - `where('item_id = :item')->bind('item', 1675)`
  - `where(['admin = 1', 'mod = 1'], 'OR')
  
Examples:
```php
QueryBuilder
  ->select()
  ->from('members')
  ->where(['id = 3', 'id = 4'], 'OR')
  ->where('banned = 0');
```
```mysql
SELECT FROM members WHERE (id = 3 OR id = 4) AND banned = 0
```
  
### NOT NULL
```php
QueryBuilder->notnull($column)
```
- Performs a "column IS NOT NULL" query


### NOT EMPTY
```php
QueryBuilder->notempty($column)
```
- Performs a "column != ''" query


### IS EMPTY
```php
QueryBuilder->isempty($column)
```
- Performs a "column = ''" query

### NOT
```php
QueryBuilder->not($column, $value)
```
- Performs a "column != value" query


### JOIN
```php
QueryBuilder->where($main, $table, $as = null)
```
- Perform a LEFT JOIN, eg:
  - `join(['users' => 'id], ['comments' => 'user_id]` = LEFT JOIN comments ON comments.user_id = users.id
  - `join(['users' => 'id], ['comments' => 'user_id], 'posts')` = LEFT JOIN comments AS posts ON posts.user_id = users.id
  
Examples
```php
QueryBuilder
  ->select()
  ->from('members')
  ->join(['members' => 'id'], ['posts' => 'id'])
  ->join(['posts' => 'reply'], ['posts' => 'id'], 'reply');
```
```mysql
SELECT FROM `members` 
  LEFT JOIN `members` ON `members`.`id` = `posts`.`id` 
  LEFT JOIN `posts` AS reply ON reply.`reply` = `posts`.`id`
```
  
  
### GROUP
```php
QueryBuilder->group($main)
```
- Perform a GROUP BY, eg:
  - `group('user_id')`
  - Can be called in multiple chains to combine groups:
    - `group('user_id')->group('rating')` > `GROUP BY user_id,rating`
  

### ORDER
```php
QueryBuilder->order($order, $direction = 'desc');
```
- Perform an order


### LIMIT
```php
QueryBuilder->limit($start, $amount);
```
- Limit a query
 
 
 
### BIND
```php
QueryBuilder->bind($param, $variable, $isWild = false)
```
- Attempt to bind a variable
  - if `$isWild` is set, then the variable will be bound correctly for wild card search
  

### REPLACE
```php
QueryBuilder->replace($find, $replace)
```
- Replace strings in the query before submitting it, eg:
  - `from('{main}')->replace('{main}', 'sometable')`
  

### DUPLICATE
```php
QueryBuilder->duplicate($columns = [], $include = false)
```
- Handle `ON DUPLICATE KEY UPDATE`
  - If `$include = false` then all columns except those provided will be updated
    - `duplicate(['user_id'])` = user_id does not update
  - if `$include = true`
    - `duplicate(['user_id'])` = user_id is only updated
    

### FORCE INDEX
```php
QueryBuilder->forceIndex($keys)
```
- Force index of a table key.



 