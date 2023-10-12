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