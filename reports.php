<?php 
include 'db_connect.php';

// Check if session variables are set
if(!isset($_SESSION['login_type'])) {
    die("Session not properly initialized");
}
?>

<div class="col-md-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <b>Projects Report</b>
            <div class="card-tools">
                <button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" id="printable">
                <table class="table m-0 table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Total Tasks</th>
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
                            // Count tasks for each project
                            $tprog_stmt = $conn->prepare("SELECT COUNT(*) FROM task_list WHERE project_id = ?");
                            $tprog_stmt->execute([$row['id']]);
                            $tprog = $tprog_stmt->fetchColumn();
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <a><?php echo htmlspecialchars(ucwords($row['name'])); ?></a>
                                <br>
                                <small><?php echo htmlspecialchars($row['description']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($row['start_date']))); ?></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($row['end_date']))); ?></td>
                            <td class="text-center"><?php echo number_format($tprog); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>  
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('#print').click(function(){
        start_load();
        var _h = $('head').clone();
        var _p = $('#printable').clone();
        var _d = "<p class='text-center'><b>Projects Report as of (<?php echo date("F d, Y") ?>)</b></p>";
        _p.prepend(_d);
        _p.prepend(_h);
        var nw = window.open("","","width=900,height=600");
        nw.document.write(_p.html());
        nw.document.close();
        nw.print();
        setTimeout(function(){
            nw.close();
            end_load();
        },750);
    });
</script>