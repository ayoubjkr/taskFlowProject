<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
   	include 'db_connect.php';
    
    $this->db = $conn; 
	}
	function __destruct() {
	    $this->db = null; 
	}

	function login(){
		extract($_POST);
		$stmt = $this->db->prepare("SELECT *,concat(firstname,' ',lastname) as name FROM users where email = ? and password = ?");
		$stmt->execute([$email, md5($password)]);
		
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			foreach ($row as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			return 1;
		}else{
			return 2;
		}
	}
	
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	
	function login2(){
		extract($_POST);
		$stmt = $this->db->prepare("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM students where student_code = ?");
		$stmt->execute([$student_code]);
		
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			foreach ($row as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['rs_'.$key] = $value;
			}
			return 1;
		}else{
			return 3;
		}
	}
	
	function save_user(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		if(!empty($password)){
			$data .= ", password=? ";
			$params[] = md5($password);
		}
		
		$checkSql = "SELECT * FROM users where email = ?";
		$checkParams = [$email];
		if(!empty($id)) {
			$checkSql .= " and id != ?";
			$checkParams[] = $id;
		}
		
		$stmt = $this->db->prepare($checkSql);
		$stmt->execute($checkParams);
		
		if($stmt->rowCount() > 0){
			return 2;
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = ? ";
			$params[] = $fname;
		}
		
		if(empty($id)){
			$stmt = $this->db->prepare("INSERT INTO users set $data");
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE users set $data");
		}
		
		$save = $stmt->execute($params);
		
		if($save){
			return 1;
		}
	}
	
	function signup(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					$v = md5($v);
				}
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}

		$checkSql = "SELECT * FROM users where email = ?";
		$checkParams = [$email];
		if(!empty($id)) {
			$checkSql .= " and id != ?";
			$checkParams[] = $id;
		}
		
		$stmt = $this->db->prepare($checkSql);
		$stmt->execute($checkParams);
		
		if($stmt->rowCount() > 0){
			return 2; 
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = ? ";
			$params[] = $fname;
		}
		
		if(empty($id)){
			$stmt = $this->db->prepare("INSERT INTO users set $data");
			$save = $stmt->execute($params);
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE users set $data");
			$save = $stmt->execute($params);
		}

		if($save){
			if(empty($id))
				$id = $this->db->lastInsertId();
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			$_SESSION['login_id'] = $id;
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function update_user(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		$checkSql = "SELECT * FROM users where email = ?";
		$checkParams = [$email];
		if(!empty($id)) {
			$checkSql .= " and id != ?";
			$checkParams[] = $id;
		}
		
		$stmt = $this->db->prepare($checkSql);
		$stmt->execute($checkParams);
		
		if($stmt->rowCount() > 0){
			return 2;
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = ? ";
			$params[] = $fname;
		}
		
		if(!empty($password)){
			$data .= " ,password=? ";
			$params[] = md5($password);
		}
		
		if(empty($id)){
			$stmt = $this->db->prepare("INSERT INTO users set $data");
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE users set $data");
		}
		
		$save = $stmt->execute($params);

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}
	
	function delete_user(){
		extract($_POST);
		$stmt = $this->db->prepare("DELETE FROM users where id = ?");
		$delete = $stmt->execute([$id]);
		if($delete)
			return 1;
	}
	
	function save_system_settings(){
		extract($_POST);
		$data = '';
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = ? ";
			$params[] = $fname;
		}
		
		$chk = $this->db->prepare("SELECT * FROM system_settings");
		$chk->execute();
		
		if($chk->rowCount() > 0){
			$row = $chk->fetch(PDO::FETCH_ASSOC);
			$data .= " where id = ?";
			$params[] = $row['id'];
			$stmt = $this->db->prepare("UPDATE system_settings set $data");
			$save = $stmt->execute($params);
		}else{
			$stmt = $this->db->prepare("INSERT INTO system_settings set $data");
			$save = $stmt->execute($params);
		}
		
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}
	
	function save_project(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','user_ids')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		if(isset($user_ids)){
			$data .= ", user_ids=? ";
			$params[] = implode(',',$user_ids);
		}
		
		if(empty($id)){
			$stmt = $this->db->prepare("INSERT INTO project_list set $data");
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE project_list set $data");
		}
		
		$save = $stmt->execute($params);
		if($save){
			return 1;
		}
	}
	
	function delete_project(){
		extract($_POST);
		$stmt = $this->db->prepare("DELETE FROM project_list where id = ?");
		$delete = $stmt->execute([$id]);
		if($delete){
			return 1;
		}
	}
	
	function save_task(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		if(empty($id)){
			$stmt = $this->db->prepare("INSERT INTO task_list set $data");
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE task_list set $data");
		}
		
		$save = $stmt->execute($params);
		if($save){
			return 1;
		}
	}
	
	function delete_task(){
		extract($_POST);
		$stmt = $this->db->prepare("DELETE FROM task_list where id = ?");
		$delete = $stmt->execute([$id]);
		if($delete){
			return 1;
		}
	}
	
	function save_progress(){
		extract($_POST);
		$data = "";
		$params = [];
		
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'comment')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k=? ";
				}else{
					$data .= ", $k=? ";
				}
				$params[] = $v;
			}
		}
		
		$dur = abs(strtotime("2020-01-01 ".$end_time)) - abs(strtotime("2020-01-01 ".$start_time));
		$dur = $dur / (60 * 60);
		$data .= ", time_rendered=? ";
		$params[] = $dur;
		
		if(empty($id)){
			$data .= ", user_id=? ";
			$params[] = $_SESSION['login_id'];
			$stmt = $this->db->prepare("INSERT INTO user_productivity set $data");
		}else{
			$data .= " where id = ?";
			$params[] = $id;
			$stmt = $this->db->prepare("UPDATE user_productivity set $data");
		}
		
		$save = $stmt->execute($params);
		if($save){
			return 1;
		}
	}
	
	function delete_progress(){
		extract($_POST);
		$stmt = $this->db->prepare("DELETE FROM user_productivity where id = ?");
		$delete = $stmt->execute([$id]);
		if($delete){
			return 1;
		}
	}
	
	function get_report(){
		extract($_POST);
		$data = array();
		$stmt = $this->db->prepare("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between ? and ? order by unix_timestamp(t.date_created) desc");
		$stmt->execute([$date_from, $date_to]);
		
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);
	}
}