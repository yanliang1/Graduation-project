<?php

require_once 'config.php';
/**
 * 封装大家共用的函数
 */
session_start();

// 定义函数时一点要注意函数名与内置函数冲突问题
// JS 判断方式:typeof fn == 'function'
// PHP 判断方式：function_exit

/**
 * 获取当前登录的用户信息，如果没有获取到则自动跳转到登录页面
 */
function bx_get_current_user(){
    if(empty($_SESSION['current_login_user'])){
        // 没用当前登录用户信息，意味着没有登录
        header('Locaton:/admin/login.php');
        exit(); // 没有必要在执行之后的代码
    }
    return $_SESSION['current_login_user'];
}

/**
 * 通过一个数据库查询获取多条数据
 * 返回的是索引数组
 */
function bx_fetch_all($sql){
    $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASS, BX_DB_NAME);
    if(!$conn){
        exit("连接失败");
    }

    $query = mysqli_query($conn,$sql);
    if(!$query){
        // 查询失败
        return false;
    }

    while ($row = mysqli_fetch_assoc($query)){
        $result[] = $row;
    }

    return $result;
}

/**
 * 获取单条数据
 * 返回的是关联数组
 */
function bx_fetch_one($sql){
    $res = bx_fetch_all($sql);
    return isset($res[0]) ? $res[0] : null;
}