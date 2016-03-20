<?php
	//需要owner_id参数。

	$t_query="select * from user where userId='".$_SESSION['owner_id']."'";
	$t_result=mysqli_query($con,$t_query);
	$t_row=mysqli_fetch_array($t_result);
	echo "<h1>".$t_row['name']."的博客</h1>";
	echo "<a href='http://localhost/blog/index.php?act=index&id=".$_SESSION['owner_id']."'>主页</a>";
	echo " <a href='http://localhost/blog/index.php?act=article'>日志</a>";
	echo " <a href='http://localhost/blog/index.php?act=photo'>相册</a><br>";
	if(isset($_SESSION['user_id'])&&$_SESSION['owner_id']==$_SESSION['user_id'])
	{
		echo " <a href='http://localhost/blog/index.php?act=adda'>写日志</a>";
		echo " <a href='http://localhost/blog/index.php?act=addp'>传照片</a>"; 
	}
?>