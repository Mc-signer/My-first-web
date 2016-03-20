<?php
	//开启会话，显示用户状态
	if(isset($_SESSION['user_id']))
	{
		echo "<a href='index.php?act=index&id=".$_SESSION['user_id']."'>".$_SESSION['user_name']."</a>";
		echo " <a href='index.php?act=article&back=1'>我的日志</a>";
		echo " <a href='index.php?act=photo&back=1'>我的相册</a>";
		echo " <a href='logout.php'>退出</a><br/>";
	}
	else if(isset($_COOKIE['user_id']))
	{
		echo "<a href='index.php?act=index&id=".$_COOKIE['user_id']."'>".$_COOKIE['user_name']."</a>";
		echo " <a href='index.php?act=article&back=1'>我的日志</a>";
		echo " <a href='index.php?act=photo&back=1'>我的相册</a>";
		echo " <a href='logout.php'>退出</a><br/>";		
	}
	else
	{
		echo "<a href='index.php?act=login'>登录</a>";
		echo " <a href='registration.php'>注册</a><br/>";
	}
?>

