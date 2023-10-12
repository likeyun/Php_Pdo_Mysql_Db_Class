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