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