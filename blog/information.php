<?php
	define('HEADIMGPATH','../blog/headimg/');
	$query="select * from user where userId='".$_SESSION['owner_id']."'";
	$result=mysqli_query($con,$query);
	$row=mysqli_fetch_array($result);
	if(empty($row['headImg']))
		echo "<div><table><tr><img src='".HEADIMGPATH."default.jpg' width='100' height='100'></tr>";
	else
		echo "<div><table><tr><img src='".HEADIMGPATH.$row['headImg']."' width='100' height='100'></tr>";
	echo "<tr><td>昵称：</td><td>".$row['name']."</td></tr>";
	echo "<tr><td>性别：</td><td>".$row['gender']."</td></tr>";
	echo "<tr><td>年龄：</td><td>".$row['age']."</td></tr>";
	echo "<tr><td>简介：</td><td>".$row['profile']."</td></tr>";
	echo "<tr><td>注册时间：</td><td>".$row['signUpDate']."</td></tr>";
	if($_SESSION['user_id']==$_SESSION['owner_id'])
	{
		echo "<a href='?act=edit'>编辑我的信息</a>";
	}
	echo "</table></div>";
?>