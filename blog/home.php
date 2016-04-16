<?php
	session_start();
	require_once('dbdefine.php');
	function getBlog($query)
	{
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			echo "<a target='_blank' href='index.php?act=article&ownerid=".$row['writeUser']."&articleid=".$row['articleId']."'>".$row['title']."</a><br/>";
		}
		mysqli_close($con);
	}
	function category($act)
	{
		switch($act)
		{
			case "hot":
				echo "<div class='platetitle'>热门文章</div>";
				break;
			case "new":
				echo "<div class='platetitle'>最新文章</div>";
				break;
			case "life":
				echo "<div class='platetitle'>生活动态</div>";
				break;
			case "tech":
				echo "<div class='platetitle'>技术人生</div>";
				break;
		}
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		if($act!="new")
			$query="select * from article where category='$act' order by writeTime desc limit 20";
		else $query="select * from article order by writeTime desc limit 20";
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			echo "<a target='_blank' href='index.php?act=article&ownerid=".$row['writeUser']."&articleid=".$row['articleId']."'>".$row['title']."</a><br/>";
		}
		mysqli_close($con);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>首页-分享与记录</title>
	<link rel="stylesheet" type="text/css" href="main.css"/>
	<meta charset="utf-8">
</head>
<body>
<?php
	require_once('navigation.php');
?>
<!-- <img src='logo.jpg'>-->
<div class="contain">
	<div class="activeuser">
	<div class='platetitle'>活跃用户</div>
	<?php
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		$query="select * from user where active='1' limit 10";
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			echo "<div class='auser'>";
			echo "<a target='_blank' href='index.php?act=index&ownerid=".$row['userId']."'>";
			if(!empty($row['headImg']))echo "<img src='../blog/headimg/".$row['headImg']."' width='50px' height='50px'></a>";
			else echo "<img src='../blog/headimg/default.jpg' width='50px' height='50px'></a>";
			echo "<a class='ausername' target='_blank' href='index.php?act=index&ownerid=".$row['userId']."'>".$row['name']."</a>";
			echo "</div>";
		}
	?>
	</div>
	<div class="main">
		<?php
			if(isset($_GET['act'])){
				$act=$_GET['act'];
				category($act);
			}
			else {
		?>
		<div class="plate" name="hot">
		<a href="?act=hot"><div class='platetitle'>热门文章</div></a>
		<?php
			$query="select * from article where category ='hot' order by writeTime desc limit 10";
			getBlog($query);
		?>
		</div>
		<div class="plate" name="new">
		<a href="?act=new"><div class='platetitle'>最新文章</div></a>
		<?php
			$query="select * from article order by writeTime desc limit 10 ";
			getBlog($query);
		?>
		</div>
		<div class="plate" name="life">
		<a href="?act=life"><div class='platetitle'>生活动态</div></a>
		<?php
			$query="select * from article where category ='life' order by writeTime desc limit 10";
			getBlog($query);
		?>
		</div>
		<div class="plate" name="tech">
		<a href="?act=tech"><div class='platetitle'>技术人生</div></a>
		<?php
			$query="select * from article where category ='tech' order by writeTime desc limit 10";
			getBlog($query);
		?>
		</div>
	</div>
</div>
<?php
		}
?>
</body>
</html>