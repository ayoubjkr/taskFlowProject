<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
    $stmt = $conn->prepare("SELECT * FROM task_list where id = ?");
    $stmt->execute([$_GET['id']]);
    $qry = $stmt->fetch(PDO::FETCH_ASSOC);
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <dl>
        <dt><b class="border-bottom border-primary">Task</b></dt>
        <dd><?php echo ucwords($task) ?></dd>
    </dl>
    <dl>
        <dt><b class="border-bottom border-primary">Description</b></dt>
        <dd><?php echo html_entity_decode($description) ?></dd>
    </dl>
</div>