<?php 
include 'db_connect.php';

// Check if session variables are set
if(!isset($_SESSION['login_type'])) {
    die("Session not properly initialized");
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project">
                    <i class="fa fa-plus"></i> Add New project
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table tabe-hover table-condensed" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="25%">
                    <col width="30%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Project Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Secure query conditions with prepared statements
                    $where = "";
                    $params = [];

                    if($_SESSION['login_type'] == 2) {
                        $where = " WHERE p.manager_id = ?";
                        $params = [$_SESSION['login_id']];
                    } elseif($_SESSION['login_type'] == 3) {
                        $where = " WHERE FIND_IN_SET(?, p.user_ids) > 0";
                        $params = [$_SESSION['login_id']];
                    }

                    // Get tasks with prepared statement
                    $stmt = $conn->prepare("SELECT t.*, p.name as pname, p.start_date, p.end_date, p.id as pid 
                                        FROM task_list t 
                                        INNER JOIN project_list p ON p.id = t.project_id 
                                        $where 
                                        ORDER BY p.name ASC");
                    $stmt->execute($params);
                    
                    $i = 1;
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                        // Sanitize description
                        $desc = strip_tags(html_entity_decode($row['description']));
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td>
                            <p><b><?php echo htmlspecialchars(ucwords($row['pname'])); ?></b></p>
                        </td>
                        <td>
                            <p><b><?php echo htmlspecialchars(ucwords($row['task'])); ?></b></p>
                            <p class="truncate"><?php echo htmlspecialchars($desc); ?></p>
                        </td>
                        <td><b><?php echo htmlspecialchars(date("M d, Y", strtotime($row['end_date']))); ?></b></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                Action
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="./index.php?page=view_project&id=<?php echo (int)$row['pid']; ?>">View Project</a>
                            </div>
                        </td>
                    </tr>    
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    table p{
        margin: unset !important;
    }
    table td{
        vertical-align: middle !important
    }
</style>
<script>
    $(document).ready(function(){
        $('#list').dataTable();
    });
</script>