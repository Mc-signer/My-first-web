<?php
	//需要ownerid参数。

	$t_query="select * from user where userId='".$ownerid."'";
	$t_result=mysqli_query($con,$t_query);
	$t_row=mysqli_fetch_array($t_result);
	echo "<div class='title'>";
	echo "<h1>".$t_row['name']."的博客</h1>";
	echo "<a href='http://localhost/blog/index.php?act=index&ownerid=$ownerid'>主页</a>";
	echo " <a href='http://localhost/blog/index.php?act=article&ownerid=$ownerid'>日志</a>";
	echo " <a href='http://localhost/blog/index.php?act=photo&ownerid=$ownerid'>相册</a><br>";
	if(isset($_SESSION['user_id'])&&$ownerid==$_SESSION['user_id'])
	{
		echo " <a href='http://localhost/blog/index.php?act=adda&ownerid=$ownerid' class=button>写日志</a>";
		echo " <a href='http://localhost/blog/index.php?act=addp&ownerid=$ownerid' class=button>传照片</a>";
	}
	echo "</div>";
?>