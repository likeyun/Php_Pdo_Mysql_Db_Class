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