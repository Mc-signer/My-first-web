<?php
	//开启会话，显示用户状态
	echo "<div class='nav' id='nav'>";
	echo "<a href='home.php'><img src='../blog/blog-logo.png' id='home'></a>";
	echo "<div class='nav1'>";
	if(isset($_SESSION['user_id']))
	{
		if($_SESSION['user_id']=='9')
		{
			echo "<a href='admin.php'>admin</a>";
			echo " <a href='logout.php'>退出</a><br/>";
		}
		else {
			echo "<a href='index.php?act=index&ownerid=".$_SESSION['user_id']."'>".$_SESSION['user_name']."</a>";
			echo "<a href='index.php?act=article&ownerid=".$_SESSION['user_id']."'>我的日志</a>";
			echo "<a href='index.php?act=photo&ownerid=".$_SESSION['user_id']."'>我的相册</a>";
			echo "<a href='logout.php'>退出</a><br/>";
		}
	}
	else if(isset($_COOKIE['user_id']))
	{
		if($_SESSION['user_id']=='9')
		{
			echo "<a href='admin.php'>admin</a>";
			echo " <a href='logout.php'>退出</a><br/>";
		}
		else {
			$_SESSION['user_id']=$_COOKIE['user_id'];
			$_SESSION['user_name']=$_COOKIE['user_name'];
			echo "<a href='index.php?act=index&ownerid=".$_COOKIE['user_id']."'>".$_COOKIE['user_name']."</a>";
			echo " <a href='index.php?act=article&ownerid=".$_COOKIE['user_id']."'>我的日志</a>";
			echo " <a href='index.php?act=photo&ownerid=".$_COOKIE['user_id']."'>我的相册</a>";
			echo " <a href='logout.php'>退出</a><br/>";
		}
	}
	else
	{
		echo "<a href='login.php'>登录</a>";
		echo " <a href='registration.php'>注册</a><br/>";
	}
	echo "</div></div>";
?>

