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
    <form action="" id="manage-task">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
        <div class="form-group">
            <label for="">Task</label>
            <input type="text" class="form-control form-control-sm" name="task" value="<?php echo isset($task) ? $task : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="">Description</label>
            <textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
                <?php echo isset($description) ? $description : '' ?>
            </textarea>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    })
     })
    
    $('#manage-task').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_task',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast('Data successfully saved',"success");
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    })
</script>