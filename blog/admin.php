<?php
	session_start();
	require_once("dbdefine.php");
	if(!isset($_SESSION['admin'])&&$_SESSION['user_id']!='1')
	{
		header("Location:http://localhost/blog/login.php");
		exit();
	}
	function display($act)
	{
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		$pagesize=20;
		if(!isset($_GET['page']))
		{
			$page=1;
			$start=0;
			$query="select * from $act";
			$result=mysqli_query($con,$query);
			$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
		}
		else {
			$page=$_GET['page'];
			$start=($_GET['page']-1)*$pagesize;
		}
		$query.=" limit $start,$pagesize";
		$result=mysqli_query($con,$query);
		echo "<table>";
		if($act=="user"){
			echo "<form action='?act=del' method='post'>";
			echo "<tr><th>用户名</th><th>性别</th><th>简介</th><th>注册时间</th><th>状态</th><th>操作</th><th><input type='submit' name='submit' value='批量处理'></th></tr>";
			while($row=mysqli_fetch_array($result)){
				if($row['userId']=='1')continue;
				echo "<tr><td><a href='?act=auser&userid=".$row['userId']."'>".$row['name']."</a></td>".
						"<td>".$row['gender']."</td><td>".$row['profile']."</td><td>".$row['signUpDate']."</td>";

				if($row['ban'])echo "<td>封号</td>";
				else if($row['active'])echo "<td>活跃</td>";
				else echo "<td>正常</td>";

				echo "<td><input type='checkbox' name='ban[]' value='".$row['userId']."'>";
				if($row['ban'])echo "解封";
				else echo "封号";

				echo "<input type='checkbox' name='delete[]' value='".$row['userId']."'>删号";

				echo "<input type='checkbox' name='active[]' value='".$row['userId']."'>";
				if($row['active'])echo "取消活跃用户";
				else echo "设为活跃用户";
				echo "</td>";
			}
			echo "</form>";
			if($_SESSION['page_num']>1)
			{
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=user&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
			}
		}
		else if($act=="article")
		{
			$query="select * from article,user where writeUser=userId limit $start,$pagesize";
			$result=mysqli_query($con,$query);
			echo "<form action='?act=del' method='post'>";
			echo "<tr><th>日志名</th><th>日期</th><th>作者</th><th>分类</th><th>操作</th><th><input type='submit' name='submit' value='批量处理'></th></tr>";
			$i=0;
			while($row=mysqli_fetch_array($result))
			{
				echo "<tr><td><a target='_blank' href='../blog/index.php?act=article&ownerid=".$row['writeUser']."&articleid=".$row['articleId']."'>".$row['title']."</a></td>".
						"<td>".$row['writeTime']."</td>".
						"<td><a href='?act=auser&userid=".$row['writeUser']."'>".$row['name']."</td>";

				echo "<td>";
				switch($row['category'])
				{
					case 'hot':echo "热门文章";
					break;
					case 'life':echo "生活动态";
					break;
					case 'tech':echo "科技人生";
					break;
					default:echo "无";
				}
				echo "</td>";
				echo "<td><input type='hidden' name='articleid'>";
				echo "<input type='hidden' name='category[$i][0]' value='".$row['articleId'].
				"'>改变分类<select name='category[$i][1]'><option value=''>无</option>"."<option value='hot' ";
						if($row['category']=='hot')echo "selected='selected'";
				echo ">热门文章</option>";
				echo "<option value='life' ";
						if($row['category']=='life')echo "selected='selected'";
				echo ">生活动态</option>";
				echo "<option value='tech' ";
						if($row['category']=='tech')echo "selected='selected'";
				echo ">科技人生</option></select>";
				echo "<input type='checkbox' name='del_article[]' value='".$row['articleId']."'>删除</td></tr>";
				$i++;
			}
		
			echo "</form>";
			if($_SESSION['page_num']>1)
			{
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
			}	
			
		}
		else if($act=="photo")
		{
			echo "<form action='?act=del' method='post'>";
			echo "<tr><th>预览</th><th>相片名</th><th>日期</th><th>博主</th><th>相册</th><th>操作</th><th><input type='submit' name='submit' value='批量处理'></th></tr>";
			while($row=mysqli_fetch_array($result))
			{
				$query1="select * from user where userId='".$row['ownerId']."'";
				$result1=mysqli_query($con,$query1);
				$row1=mysqli_fetch_array($result1);
				echo "<tr><td><img src='../blog/photo/".$row['ownerId']."/".$row['filename']."' width='100' height='100'></td>";
				echo "<td>".$row['nickname']."</td><td>".$row['addTime']."</td>";
				echo "<td><a href='?act=auser&userid=".$row1['userId']."'>".$row1['name']."</td>";
				echo "<td>".$row['belongAlbum']."</td>";
				echo "<td><input type='checkbox' name='del_photo[]' value='".$row['photoId']."'>删除</td>";
			}
			echo "</form>";
			
			if($_SESSION['page_num']>1)
			{
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=photo&page=$next_page'>下一页</a>";
					echo "<a href='?act=photo&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=photo&page=$begin_page'>首页</a>";
					echo "<a href='?act=photo&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=photo&page=$begin_page'>首页</a>";
					echo "<a href='?act=photo&page=$up_page'>上一页</a>";
					echo "<a href='?act=photo&page=$next_page'>下一页</a>";
					echo "<a href='?act=photo&page=$end_page'>尾页</a>";
				}
			}	
		}
		echo "</table>";
	}
	function auser(){
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		echo "<table>";
		$userid=$_GET['userid'];
		$query="select * from user where userId='$userid'";
		$result=mysqli_query($con,$query);
		$row=mysqli_fetch_array($result);
		echo "<form action='?act=del' method='post'>";
		echo "<tr><th>用户名</th><th>性别</th><th>简介</th><th>注册时间</th><th>状态</th><th>操作</th><th><input type='submit' name='submit' value='处理'></th></tr>";

		echo "<tr><td><a target='_blank' href='index.php?act=index&ownerid=".$row['userId']."'>".$row['name']."</a></td><td>".$row['gender']."</td><td>".$row['profile']."</td><td>".$row['signUpDate']."</td>";

		if($row['ban'])echo "<td>封号</td>";
		else if($row['active'])echo "<td>活跃</td>";
		else echo "<td>正常</td>";

		echo "<td><input type='checkbox' name='ban[]' value='".$row['userId']."'>";
		if($row['ban'])echo "解封";
		else echo "封号";

		echo "<input type='checkbox' name='delete[]' value='".$row['userId']."'>删号";

		echo "<input type='checkbox' name='active[]' value='".$row['userId']."'>";
		if($row['active'])echo "取消活跃用户";
		else echo "设为活跃用户";
		echo "</td>";


		$query="select * from article where writeUser='$userid'";
		$result=mysqli_query($con,$query);
		echo "<tr><th>日志名</th><th>日期</th><th>分类</th><th>操作</th></tr>";
		$i=0;
		while($row=mysqli_fetch_array($result))
		{
			echo "<tr><td><a target='_blank' href='../blog/index.php?act=article&ownerid=".$row['writeUser']."&articleid=".$row['articleId']."'>".$row['title']."</a></td>"."<td>".$row['writeTime']."</td>";

			echo "<td>";
			switch($row['category'])
			{
				case 'hot':echo "热门文章";
				break;
				case 'life':echo "生活动态";
				break;
				case 'tech':echo "科技人生";
				break;
				default:echo "无";
			}
			echo "</td>";
			echo "<td><input type='hidden' name='articleid'>";
			echo "<input type='hidden' name='category[$i][0]' value='".$row['articleId'].
				"'>改变分类<select name='category[$i][1]'><option value=''>无</option>"."<option value='hot' ";
					if($row['category']=='hot')echo "selected='selected'";
			echo ">热门文章</option>";
			echo "<option value='life' ";
					if($row['category']=='life')echo "selected='selected'";
			echo ">生活动态</option>";
			echo "<option value='tech' ";
					if($row['category']=='tech')echo "selected='selected'";
			echo ">科技人生</option></select>";
			echo "<input type='checkbox' name='del_article[]' value='".$row['articleId']."'>删除</td></tr>";
			$i++;
		}
		
		$query="select * from photo where ownerId = '$userid'";
		$result=mysqli_query($con,$query);
		
		echo "<tr><th>预览</th><th>相片名</th><th>日期</th><th>相册</th><th>操作</th></tr>";
		while($row=mysqli_fetch_array($result))
		{
			echo "<tr><td><a target='_blank' href='index.php?act=photo&ownerid=".$row['ownerId']."&photoid=".$row['photoId']."'><img src='../blog/photo/".$row['ownerId']."/".$row['filename']."' width='100' height='100'></a></td>";
			echo "<td>".$row['nickname']."</td><td>".$row['addTime']."</td>";
			echo "<td>".$row['belongAlbum']."</td>";
			echo "<td><input type='checkbox' name='del_photo[]' value='".$row['photoId']."'>删除</td></tr>";	
		}
		echo "</form></table>";
	}
	function search()
	{
		echo "<form action='?act=src' method='post'>";
		echo "<input type='text' name='search' >".
				 "<input type='submit' name='submit' value='查询用户'>";
		echo "</form>";
	}
	function src_result($src)
	{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$query="select * from user where name like '%".$src."%'";
			$result=mysqli_query($con,$query);
			echo "<form action='?act=del' method='post'>";
			echo "<table><tr><th>用户名</th><th>性别</th><th>简介</th><th>注册时间</th><th>状态</th><th>操作</th><th><input type='submit' name='submit' value='批量处理'></th></tr>";
			while($row=mysqli_fetch_array($result)){
				if($row['userId']=='1')continue;
				echo "<tr><td><a target='_blank 'href='?act=auser&userid=".$row['userId']."'>".$row['name']."</a></td>".
						"<td>".$row['gender']."</td><td>".$row['profile']."</td><td>".$row['signUpDate']."</td>";

				if($row['ban'])echo "<td>封号</td>";
				else if($row['active'])echo "<td>活跃</td>";
				else echo "<td>正常</td>";

				echo "<td><input type='checkbox' name='ban[]' value='".$row['userId']."'>";
				if($row['ban'])echo "解封";
				else echo "封号";

				echo "<input type='checkbox' name='delete[]' value='".$row['userId']."'>删号";

				echo "<input type='checkbox' name='active[]' value='".$row['userId']."'>";
				if($row['active'])echo "取消活跃用户";
				else echo "设为活跃用户";
				echo "</td>";
			}
			echo "</table></form>";
			mysqli_close($con);
	}
	function del()
	{
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		if(isset($_POST['ban']))
		{
			$ban=$_POST['ban'];
			for($i=0;$i<count($ban);$i++)
			{
				$query="select ban from user where userId='".$ban[$i]."'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				$query="update user set ban = ";
				if($row['ban'])$query.="0 ";
				else $query.="1 ";
				$query.="where userId='".$ban[$i]."'";
				mysqli_query($con,$query);
			}
		}
		if(isset($_POST['active']))
		{
			$active=$_POST['active'];
			for($i=0;$i<count($active);$i++)
			{
				$query="select active from user where userId='".$active[$i]."'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				$query="update user set active = ";
				if($row['active'])$query.="0 ";
				else $query.="1 ";
				$query.="where userId='".$active[$i]."'";
				mysqli_query($con,$query);
			}
		}
		if(isset($_POST['delete']))
		{
			$delete=$_POST['delete'];
			for($i=0;$i<count($delete);$i++){
				$query="delete from user where userId='".$delete[$i]."'";
				mysqli_query($con,$query);
				$query="delete from article where writeUser='".$delete[$i]."'";
				mysqli_query($con,$query);
				$query="delete from photo where ownerId='".$delete[$i]."'";
				mysqli_query($con,$query);
			}
		}
		if(isset($_POST['category']))
		{
			$category=$_POST['category'];
			for($i=0;$i<count($category);$i++)
			{
				$query="update article set category = '".$category[$i][1]."' where articleId = '".$category[$i][0]."'";
				mysqli_query($con,$query);
			}
		}
		if(isset($_POST['del_article']))
		{
			$del_article=$_POST['del_article'];
			for($i=0;$i<count($del_article);$i++)
			{
				$query="delete from article where articleId='".$del_article[$i]."'";
				mysqli_query($con,$query);
			}
		}
		if(isset($_POST['del_photo']))
		{
			$del_photo=$_POST['del_photo'];
			foreach($del_photo as $k=>$v)
			{
				$query="select * from photo where photoId='$v'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				unlink("D:\\xampp\\htdocs\\blog\\photo\\".$row['ownerId']."\\".$row['filename']);
				$query="delete from photo where photoId='$v'";
				mysqli_query($con,$query);
			}
		}
		mysqli_close($con);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>博客管理系统</title>
	<meta charset="utf-8">
</head>
<body>
	<div class="asidebar">
		<a href="?act=user">用户管理</a>
		<a href="?act=article">日志管理</a>
		<a href="?act=photo">相册管理</a>
	</div>
	<div class="main">
<?php
	search();
	if(isset($_POST['submit']))
	{
			if($_GET['act']=='src')
			{
				$src=$_POST['search'];
				src_result($src);
			}
			else if($_GET['act']=='del')
			{
				del();
				echo "操作成功！<a href='admin.php'>返回</a>";
			}
	}
	else{
		if(isset($_GET['act']))
			$act=$_GET['act'];
		else
			$act='user';
		if($act=='auser')
		{
			auser();
		}
		else {
			display($act);
		}
	}
	?>
	</div>
</body>
</html>
