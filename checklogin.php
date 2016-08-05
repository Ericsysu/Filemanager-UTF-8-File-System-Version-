	<?php 
		if(!$_POST['username'] || !$_POST['password']){
			echo "<script>alert('用户名和密码不能为空！')</script>";
			echo '<meta http-equiv="refresh" content="0;url=login.php">';
		}
		if($_POST['username'] == $_POST['password']){
			echo '<meta http-equiv="refresh" content="0;url=index.php">';
			$expire = time()+86400;
			setcookie('username',$_POST['username'],$expire);
		}else{
			echo "<script>alert('用户名或密码错误')</script>";
			echo '<meta http-equiv="refresh" content="1;url=login.php">';
		}
	?>