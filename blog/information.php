<?php
	define('HEADIMGPATH','../blog/headimg/');
	$info_query="select * from user where userId='".$ownerid."'";
	$info_result=mysqli_query($con,$info_query);
	$info_row=mysqli_fetch_array($info_result);
	if(empty($info_row['headImg']))
		echo "<div class='information'><table><tr><img src='".HEADIMGPATH."default.jpg' width='100' height='100'></tr>";
	else
		echo "<div class='information'><table><tr><img src='".HEADIMGPATH.$info_row['headImg']."' width='100' height='100'></tr>";
	echo "<tr><td>昵称：</td><td>".$info_row['name']."</td></tr>";
	echo "<tr><td>性别：</td><td>".$info_row['gender']."</td></tr>";
	echo "<tr><td>年龄：</td><td>".$info_row['age']."</td></tr>";
	echo "<tr><td>简介：</td><td>".$info_row['profile']."</td></tr>";
	echo "<tr><td>注册时间：</td><td>".$info_row['signUpDate']."</td></tr></table>";
	if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid)
	{
		echo "<a href='?act=edit&ownerid=$ownerid' class='button'>编辑我的信息</a>";
	}
	echo "</div>";
?>