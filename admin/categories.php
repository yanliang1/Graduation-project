<?php

require_once '../function.php';
// 判断用户是否登录最先做
bx_get_current_user();

// 是否为需要编辑的数据
if(!empty($_GET['id'])){
  // 客户端通过 URL 传递了一个 ID ==> 客户端是要拿一个修改数据表单
  // 需要那到用户想要修改的数据
  $current_edit_category = bx_fetch_one('select * from categories where id = ' . $_GET['id']);
}

function add_category(){
  if(empty($_POST['name']) || empty($_POST['slug'])){
    $GLOBALS['message'] = '请完整填写表单！';
    $GLOBALS['success'] = false;
    return;
  }

  // 接受并保存
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  // 增加语句
  $rows = bx_execute("insert into categories values(null,'{$slug}','{$name}');");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
  
}

function edit_category(){
  global $current_edit_category;
  // 只有当编辑并保存时触发
  // if(empty($_POST['name']) || empty($_POST['slug'])){
  //   $GLOBALS['message'] = '请完整填写表单！';
  //   $GLOBALS['success'] = false;
  //   return;
  // }

  // 接受并保存
  $id = $current_edit_category['id'];
  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;

  // 增加语句
  $rows = bx_execute("update categories set slug = '{$slug}' ,name = '{$name}' where id = {$id}");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
}

// 如果修改操作和查询操作都在页面存在，则先做修改操作，再查询
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  // 一旦表单提交，并且没有通过 URL 提交 ID 就意味着是要添加数据
  if(empty($_GET['id'])){
    add_category();
  }else{
    edit_category();
  }
}

// 查询全部的分类数据
$categories = bx_fetch_all('select * from categories;');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <?php if($success): ?>
      <div class="alert alert-success">
        <strong>正确！</strong><?php echo $message; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if(isset($current_edit_category)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
            <h2>编辑《<?php echo $current_edit_category['name']; ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
          <?php else: ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>;" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // 1.不要重复使用无意义的选择操作，应采用变量去本地化
    $(function($){
      // 在表格中的任意一个 checkbox 选中状态变化时
      var $tbodyCheckboxs = $('tbody input');
      var $btnDelete = $('#btn_delete');
      // 定义一个数组记录被选中的
      var allCheckeds = [];

      $tbodyCheckboxs.on('change',function(){
        var id = $(this).data('id');
        // 根据有没有选中当前这个 checkbox 决定是添加还是移除 
        if($(this).prop('checked')){
          allCheckeds.includes(id) || allCheckeds.push(id);
        }else{
          allCheckeds.splice(allCheckeds.indexOf(id),1);
        }
        // 根据剩下多少选中的checkbox决定是否显示删除
        allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search','?id=' + allCheckeds);
      })

      // 全选和全不选
      // 找一个合适的时机 做一件合适的事情
      $('thead input').on('change',function(){
        // 1.获取当前选中状态
        var checked = $(this).prop('checked');
        // 2.设置给标体中的每一个
        $tbodyCheckboxs.prop('checked',checked).trigger('change');
      })

        // // 有任意一个 checkbox 选中就显示，反之隐藏
        // var flag = false;
        // $tbodyCheckboxs.each(function(i,item){
        //   // attr 和 prop 区别：
        //   // -attr 访问的是 元素属性
        //   // -prop 访问的是 元素对应的DOM对象的属性
        //   if($(item).prop('checked')){
        //     flag = true;
        //   }
        // })
      //   flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
      // })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>