摘要
---
数据库操作类可以封装数据库连接和操作，使代码更易于维护和扩展。它们提供了一种组织代码的方法，将数据库相关的功能放在一个类中，以便于复用。

良好的数据库操作类可以提供一定程度的安全性，通过参数化查询或准备语句来防止SQL注入攻击。这有助于保护数据库免受恶意输入的影响。

良好的数据库操作类可以提供一定程度的安全性，通过参数化查询或准备语句来防止SQL注入攻击。这有助于保护数据库免受恶意输入的影响。

数据库操作类有助于提高PHP应用程序的可维护性、安全性和性能，同时促进代码的重用和更好的代码组织。然而，选择适合项目需求的数据库操作类以及正确使用它们非常重要。

**Database.php**
```
<?php
    
    /**
     * PHP PDO MySQL数据库操作类
     * 作者：TANKING
     * 时间：2023-10-12
     * 博客：https://segmentfault.com/u/tanking
     */

    class DB_API {
        private $pdo;
        private $error;
        
        // 连接数据库
        public function __construct($config) {
            $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}";
            try {
                $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        }
        
        // 插入
        public function add($table, $data) {
            try {
                $columns = implode(', ', array_keys($data));
                $values = implode(', :', array_keys($data));
                $query = "INSERT INTO $table ($columns) VALUES (:$values)";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($data);
                return $this->pdo->lastInsertId();
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                return false;
            }
        }
        
        // 更新
        public function update($table, $where, $data) {
            try {
                
                // 构建SET子句
                $set = '';
                foreach ($data as $key => $value) {
                    $set .= "$key = :$key, ";
                }
                $set = rtrim($set, ', ');
        
                // 构建WHERE子句
                $whereClause = '';
                foreach ($where as $key => $value) {
                    $whereClause .= "$key = :where_$key AND ";
                }
                $whereClause = rtrim($whereClause, 'AND ');
        
                // 构建SQL查询
                $query = "UPDATE $table SET $set WHERE $whereClause";
        
                // 创建预处理语句
                $stmt = $this->pdo->prepare($query);
        
                // 绑定更新数据的参数
                foreach ($data as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
        
                // 绑定WHERE条件的参数
                foreach ($where as $key => $value) {
                    $stmt->bindValue(":where_$key", $value);
                }
    
                // 执行预处理语句
                $stmt->execute();
                
                // 操作成功
                return true;
        
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                
                // 操作失败
                return false;
            }
        }
    
        // 删除
        public function delete($table, $where, $params = array()) {
            try {
                // 构建WHERE子句
                $whereClause = '';
                foreach ($where as $key => $value) {
                    $whereClause .= "$key = :$key AND ";
                }
                $whereClause = rtrim($whereClause, 'AND ');
        
                // 构建SQL查询
                $query = "DELETE FROM $table WHERE $whereClause";
        
                // 创建预处理语句
                $stmt = $this->pdo->prepare($query);
        
                // 绑定WHERE条件的参数
                foreach ($where as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
        
                // 执行预处理语句
                $stmt->execute();
        
                // 操作成功
                return true;
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                
                // 操作失败
                return false;
            }
        }
    
        // 查询
        public function select($table, $fields = "*", $conditions = null, $likeConditions = null, $orderBy = null, $limit = null, $params = array()) {
            try {
                // 构建SELECT子句
                if (is_array($fields)) {
                    $fields = implode(', ', $fields);
                } elseif ($fields === "*") {
                    $fields = "*";
                } else {
                    $fields = "";
                }
        
                // 构建WHERE子句
                $whereClause = '';
                if (!is_null($conditions) && is_array($conditions)) {
                    foreach ($conditions as $key => $value) {
                        $whereClause .= "$key = :$key AND ";
                    }
                    $whereClause = rtrim($whereClause, 'AND ');
                }
        
                // 合并LIKE条件
                if (!is_null($likeConditions) && is_array($likeConditions)) {
                    if (!empty($whereClause)) {
                        $whereClause .= ' AND ';
                    }
                    foreach ($likeConditions as $key => $value) {
                        $whereClause .= "$key LIKE :like_$key AND ";
                        $params[":like_$key"] = $value;
                    }
                    $whereClause = rtrim($whereClause, 'AND ');
                }
        
                // 构建ORDER BY子句
                $orderByClause = '';
                if (!is_null($orderBy) && is_array($orderBy)) {
                    $orderByClause = "ORDER BY " . implode(', ', $orderBy);
                }
        
                // 构建LIMIT子句
                $limitClause = '';
                if (!is_null($limit)) {
                    $limitClause = "LIMIT $limit";
                }
        
                // 构建SQL查询
                $query = "SELECT $fields FROM $table";
                if (!empty($whereClause)) {
                    $query .= " WHERE $whereClause";
                }
                if (!empty($orderByClause)) {
                    $query .= " $orderByClause";
                }
                if (!empty($limitClause)) {
                    $query .= " $limitClause";
                }
        
                // 创建预处理语句
                $stmt = $this->pdo->prepare($query);
        
                // 绑定参数
                if (!is_null($conditions) && is_array($conditions)) {
                    foreach ($conditions as $key => $value) {
                        $stmt->bindValue(":$key", $value);
                    }
                }
                
                if (!is_null($likeConditions) && is_array($likeConditions)) {
                    foreach ($likeConditions as $key => $value) {
                        $stmt->bindValue(":like_$key", $value);
                    }
                }
        
                // 执行预处理语句
                $stmt->execute();
        
                // 获取查询结果
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                return $result; // 返回查询结果数组
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                return false; // 操作失败
            }
        }
    
        // 执行原生SQL语句
        public function execQuery($query, $params = array()) {
            try {
                // 创建预处理语句
                $stmt = $this->pdo->prepare($query);
        
                // 绑定参数
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
        
                // 执行预处理语句
                $stmt->execute();
                
                // 操作成功
                return true;
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                
                // 操作失败
                return false;
            }
        }
    
        // 错误信息
        public function errorMsg() {
            return $this->error;
        }
    }
```

**Db.php**
```
<?php
    
    // 引入操作类
    include 'Database.php';
    
    // 配置文件
    $config = array(
        'db_host' => 'localhost',
        'db_port' => 3306,
        'db_name' => 'xxx',
        'db_user' => 'xxx',
        'db_pass' => 'xxx'
    );
?>
```

使用示例
---
以表名为 **`test_table`** 为示例作为示例代码。

创建表SQL:
```
CREATE TABLE `test_table` (
  `id` int(10) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `title` varchar(32) NOT NULL,
  `content` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
>**insert示例**
```
<?php
    
    // 引入配置
    include 'Db.php';
    
    // 实例化
    $db = new DB_API($config);
    
    // 数据
    $insertData = array(
        'title' => 'this is title',
        'content' => 'this is content',
    );
    
    // 执行
    $insertSQL = $db->add('test_table', $insertData);
    
    // 结果
    if ($insertSQL) {
        
        // 成功
        echo '插入成功，ID是：' . $insertSQL;
    } else {
        
        // 失败
        echo '插入失败，原因：' . $db->errorMsg();
    }
?>
```
>**update示例**
```
<?php
    
    // 引入配置
    include 'Db.php';
    
    // 实例化
    $db = new DB_API($config);
    
    // 条件
    // 只支持=，不支持>，<，>=，<=之类的
    // 如需更复杂的条件请使用执行原生语句的方法
    $where = array(
        'id' => '1'
    );
    
    // 数据
    $updateData = array(
        'content' => 'content is updated'
    );
    
    // 执行
    $updatedSQL = $db->update('test_table', $where, $updateData);
    
    // 结果
    if ($updatedSQL) {
        
        // 成功
        echo '更新成功';
    } else {
        
        // 失败
        echo '更新失败，原因：' . $db->errorMsg();
    }

?>
```
>**delete示例**
```
<?php
    
    // 引入配置
    include 'Db.php';
    
    // 实例化
    $db = new DB_API($config);
    
    // 条件
    $where = array(
        'id' => 4,
    );
    
    // 执行
    $deleteSQL = $db->delete('test_table', $where);
    
    // 结果
    if ($deleteSQL) {
        
        // 成功
        echo '删除成功';
    } else {
        
        // 失败
        echo '删除失败，原因：' . $db->errorMsg();
    }

?>
```
>**select示例**
```
<?php
    
    // 引入配置
    include 'Db.php';
    
    // 实例化
    $db = new DB_API($config);
    
    // 使用方法
    // $db->select('表名', ['字段1','字段2',...], where条件, LIKE条件, ORDER条件, LIKIT条件);
    // 如果查询所有字段，使用'*'代替数组
    // $db->select('表名', '*', where条件, LIKE条件, ORDER条件, LIKIT条件);
    // 无需使用的条件传递null
    // $db->select('表名', '*', where条件, null, null, null);
    
    // 查询所有字段，没有查询条件
    $selectSQL = $db->select('test_table', '*');
    
    // 查询指定字段，没有查询条件
    // $selectSQL = $db->select('test_table', ['id', 'title']);
    
    // 根据以下条件
    // 查询所有字段
    // $where = array(
    //     'id' => 3
    // );
    // $selectSQL = $db->select('test_table', '*', $where);
    
    // 根据以下条件
    // 查询指定字段
    // $where = array(
    //     'id' => 3
    // );
    // $selectSQL = $db->select('test_table', ['title'], $where);
    
    // 使用LIKE条件
    // 如果没有where条件就直接传入null
    // '*'是查询所有字段，如需查询指定字段传入['字段1','字段2',....]
    // $likeWhere = array(
    //     'title' => '%一带一路%'
    // );
    // $selectSQL = $db->select('test_table', '*', null, $likeWhere);
    
    // 使用where条件和LIKE条件
    // '*'是查询所有字段，如需查询指定字段传入['字段1','字段2',....]
    // $where = array(
    //     'id' => 3
    // );
    // $likeWhere = array(
    //     'title' => '%一带一路%'
    // );
    // $selectSQL = $db->select('test_table', '*', $where, $likeWhere);
    
    // 使用排序条件
    // $orderBy = array('id DESC');
    // $selectSQL = $db->select('test_table', '*', null, null, $orderBy);
    
    // 使用限制条件
    // $limit = 2; // 取2条
    // $limit = '0,3'; // 第1条开始，取3条
    // $limit = '5,2'; // 第5条开始，取2条
    // $selectSQL = $db->select('test_table', '*', null, null, null, $limit);
    
    // 结果
    if ($selectSQL) {
        
        // 成功
        echo json_encode($selectSQL, JSON_UNESCAPED_UNICODE);
    } else {
        
        // 失败
        echo '查询失败，原因：' . $db->errorMsg();
    }

?>
```
>**执行原生语句示例**
```
<?php
    
    // 引入配置
    include 'Db.php';
    
    // 实例化
    $db = new DB_API($config);
    
    // SQL语句
    $query = "INSERT INTO test_table (title, content) VALUES (:title, :content)";
    
    // 数据绑定
    $params = array(
        ':title' => 'New Title By execQuery',
        ':content' => 'New Content By execQuery',
    );
    
    // 执行
    $querySQL = $db->execQuery($query, $params);
    
    // 结果
    if ($querySQL) {
        
        // 成功
        echo 'SQL执行成功！';
    } else {
        
        // 失败
        echo 'SQL执行失败，原因：' . $db->errorMsg();
    }

?>
```
作者
---
TANKING
