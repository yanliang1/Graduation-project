<?php
/**
 * 根据客户端传递的ID删除对应的数据
 */

require_once '../function.php';

if(empty($_GET['id'])){
     exit('缺少必要参数');
}

// 防止sql注入
$id = $_GET['id'];

$rows = bx_execute('delete from categories where id in (' . $id . ');');

header('Location:/admin/categories.php');
