<?php
include 'db_connect.php';

// Check if project ID is provided and valid
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid project ID");
}

$project_id = (int)$_GET['id'];

// Use prepared statement for project query
$stmt = $conn->prepare("SELECT * FROM project_list WHERE id = ?");
$stmt->execute([$project_id]);
$qry = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$qry) {
    die("Project not found");
}

foreach($qry as $k => $v){
    $$k = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// Manager query with prepared statement
$stmt = $conn->prepare("SELECT *, CONCAT(firstname,' ',lastname) as name FROM users WHERE id = ?");
$stmt->execute([$manager_id]);
$manager = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <span><b>Team Member/s:</b></span>
                <?php if($_SESSION['login_type'] != 3): ?>
                <div class="card-tools">
                    <button class="btn btn-primary btn-sm" id="new_task">
                        <i class="fa fa-plus"></i> New Task
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <ul class="users-list clearfix">
                    <?php 
                    if(!empty($user_ids)):
                        $user_ids_array = explode(',', $user_ids);
                        $placeholders = implode(',', array_fill(0, count($user_ids_array), '?'));
                        
                        $stmt = $conn->prepare("SELECT *, CONCAT(firstname,' ',lastname) as name FROM users WHERE id IN ($placeholders) ORDER BY CONCAT(firstname,' ',lastname) ASC");
                        $stmt->execute($user_ids_array);
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                            <li>
                                <img src="assets/uploads/<?php echo htmlspecialchars($row['avatar']) ?>" alt="User Image">
                                <a class="users-list-name" href="javascript:void(0)"><?php echo ucwords(htmlspecialchars($row['name'])) ?></a>
                            </li>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <!-- Task list table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-condensed m-0 table-hover">
                    <colgroup>
                        <col width="5%">
                        <col width="35%">
                        <col width="50%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <th>#</th>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $stmt = $conn->prepare("SELECT * FROM task_list WHERE project_id = ? ORDER BY task ASC");
                        $stmt->execute([$project_id]);
                        
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
                            unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                            $desc = strtr(html_entity_decode($row['description']),$trans);
                            $desc = str_replace(array("<li>","</li>"), array("",", "), $desc);
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++ ?></td>
                                <td class=""><b><?php echo ucwords(htmlspecialchars($row['task'])) ?></b></td>
                                <td class=""><p class="truncate"><?php echo strip_tags($desc) ?></p></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javascript:void(0)">View Details</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .users-list>li img {
        border-radius: 50%;
        height: 67px;
        width: 67px;
        object-fit: cover;
    }
    .users-list>li {
        width: 33.33% !important
    }
    .truncate {
        -webkit-line-clamp:1 !important;
    }
</style>
<script>
    $('#new_task').click(function(){
        uni_modal("New Task For <?php echo addslashes(htmlspecialchars($name)) ?>","manage_task.php?pid=<?php echo (int)$id ?>","mid-large")
    })
</script>