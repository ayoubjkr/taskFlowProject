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
            <?php if($_SESSION['login_type'] != 3): ?>
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=src/projects/new_project">
                    <i class="fa fa-plus"></i> Add New project
                </a>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <table class="table tabe-hover table-condensed" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="45%">
                    <col width="20%">
                    <col width="20%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Project</th>
                        <th>Date Started</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Secure query conditions with prepared statements
                    $where = "";
                    $params = [];

                    if($_SESSION['login_type'] == 2) {
                        $where = " WHERE manager_id = ?";
                        $params = [$_SESSION['login_id']];
                    } elseif($_SESSION['login_type'] == 3) {
                        $where = " WHERE FIND_IN_SET(?, user_ids) > 0";
                        $params = [$_SESSION['login_id']];
                    }

                    // Get projects with prepared statement
                    $stmt = $conn->prepare("SELECT * FROM project_list $where ORDER BY name ASC");
                    $stmt->execute($params);
                    
                    $i = 1;
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                        <th class="text-center"><?php echo $i++; ?></th>
                        <td>
                            <p><b><?php echo htmlspecialchars(ucwords($row['name'])); ?></b></p>
                            <p class="truncate"><?php echo htmlspecialchars($row['description']); ?></p>
                        </td>
                        <td><b><?php echo htmlspecialchars(date("M d, Y", strtotime($row['start_date']))); ?></b></td>
                        <td><b><?php echo htmlspecialchars(date("M d, Y", strtotime($row['end_date']))); ?></b></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                Action
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item view_project" href="./index.php?page=view_project&id=<?php echo (int)$row['id']; ?>" data-id="<?php echo (int)$row['id']; ?>">View</a>
                                <div class="dropdown-divider"></div>
                                <?php if($_SESSION['login_type'] != 3): ?>
                                <a class="dropdown-item" href="./index.php?page=edit_project&id=<?php echo (int)$row['id']; ?>">Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_project" href="javascript:void(0)" data-id="<?php echo (int)$row['id']; ?>">Delete</a>
                                <?php endif; ?>
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
    
        $('.delete_project').click(function(){
            _conf("Are you sure to delete this project?","delete_project",[$(this).attr('data-id')]);
        });
    });
    
    function delete_project($id){
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_project',
            method: 'POST',
            data: {id: $id},
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully deleted",'success');
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            }
        });
    }
</script>