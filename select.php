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