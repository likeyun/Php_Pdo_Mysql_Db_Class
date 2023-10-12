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