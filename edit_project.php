<?php
include 'db_connect.php';
$stmt = $conn->prepare("SELECT * FROM project_list where id = ?");
$stmt->execute([$_GET['id']]);
$qry = $stmt->fetch(PDO::FETCH_ASSOC);
foreach($qry as $k => $v){
    $$k = $v;
}
include 'new_project.php';