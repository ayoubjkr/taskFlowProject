<?php 
include('db_connect.php');

// Check if session variables are set
if(!isset($_SESSION['login_type'])){
    die("Session not properly initialized");
}

$twhere = "";
if($_SESSION['login_type'] != 1) {
    $twhere = "  ";
}
?>

<!-- Info boxes -->
<div class="col-12">
    <div class="card">
        <div class="card-body">
            Welcome <?php echo htmlspecialchars($_SESSION['login_name'] ?? 'User'); ?>!
        </div>
    </div>
</div>
<hr>

<?php 
// Secure query conditions with prepared statements
$where = "";
$params = [];
$param_types = "";

if($_SESSION['login_type'] == 2) {
    $where = " WHERE manager_id = ?";
    $params = [$_SESSION['login_id']];
} elseif($_SESSION['login_type'] == 3) {
    $where = " WHERE FIND_IN_SET(?, user_ids) > 0";
    $params = [$_SESSION['login_id']];
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <b>Projects</b>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0 table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="50%">
                            <col width="35%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project</th>
                                <th>Due Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get projects with prepared statement
                            $stmt = $conn->prepare("SELECT * FROM project_list $where ORDER BY name ASC");
                            if(!empty($params)) {
                                $stmt->execute([$params[0]]);
                            } else {
                                $stmt->execute();
                            }
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $i = 1;
                            foreach($rows as $row):
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <a><?php echo htmlspecialchars(ucwords($row['name'])); ?></a>
                                    <br>
                                    <small>Start: <?php echo htmlspecialchars(date("Y-m-d", strtotime($row['start_date']))); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars(date("Y-m-d", strtotime($row['end_date']))); ?>
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="./index.php?page=view_project&id=<?php echo (int)$row['id']; ?>">
                                        <i class="fas fa-folder"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>  
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-12">
                <div class="small-box bg-light shadow-sm border">
                    <div class="inner">
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM project_list $where");
                        if(!empty($params)) {
                            $stmt->execute([$params[0]]);
                        } else {
                            $stmt->execute();
                        }
                        $project_count = $stmt->fetchColumn();
                        ?>
                        <h3><?php echo $project_count; ?></h3>
                        <p>Total Projects</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-12">
                <div class="small-box bg-light shadow-sm border">
                    <div class="inner">
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM task_list t INNER JOIN project_list p ON p.id = t.project_id $where");
                        if(!empty($params)) {
                            $stmt->execute([$params[0]]);
                        } else {
                            $stmt->execute();
                        }
                        $task_count = $stmt->fetchColumn();
                        ?>
                        <h3><?php echo $task_count; ?></h3>
                        <p>Total Tasks</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>