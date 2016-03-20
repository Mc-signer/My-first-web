<?php //验证
	session_start();
	require_once('dbdefine.php');

	if(isset($_POST['test'])&&$_POST['test']==1)
	{
		$act=$_GET['act'];
		//验证登录信息		
		if($act=="login")
		{	

			$name=$_POST['user_name'];
			$password=$_POST['user_password'];
			$captcha=$_POST['captcha'];
			if(isset($_POST['remember']))$remember=$_POST['remember'];
			$user_nameerr=$user_passworderr=$captcherr="";
			if(empty($name))
				$nameerr="用户名不能为空";
			if(empty($password))
				$passworderr="密码不能为空";
			if(empty($captcha)||$captcha!=$_SESSION['captcha'])
				$captchaerr="验证码不正确，请重试";
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$query="select * from user where name='$name'";
			$result=mysqli_query($con,$query);
			if(mysqli_num_rows($result)!=0)
			{
				$row=mysqli_fetch_array($result);
				if(sha1($password)!=$row['password'])
					$passworderr="密码不正确，请重试";
			}
			else
			{
				$nameerr="用户不存在";
			}
			if(empty($nameerr)&&empty($passworderr)&&empty($captchaerr))
			{
				$query="select * from user where name='$name'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				$_SESSION['user_name']=$name;
				$_SESSION['user_id']=$row['userId'];
				if(isset($remember)){
					setcookie('user_name',$name,time()+60*60*24*7);
					setcookie('user_id',$row['userId'],time()+60*60*24*7);
				}
				mysqli_close($con);
				header("Location:http://localhost/blog/index.php?act=index");
				exit();
			}
			mysqli_close($con);
		}
		//添加日志检查
		if($act=='adda')
		{
			$title=$_POST['title'];
			$content=$_POST['content'];
			if(empty($title)&&empty($content))
			{
				header("Location:http://localhost/blog/index.php?act=article");
				exit();
			}
			else {
				if(empty($title))
					$title=substr($content,0,10);
				$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
				$query="insert into article(title,content,writeTime,writeUser) values('$title','$content','".date('Y-m-d H:i:s')."','".$_SESSION['user_id']."')";
				mysqli_query($con,$query);
				header("Location:http://localhost/blog/index.php?act=article");
				exit();
			}
		}
		//添加照片检查
		if($act=='addp')
		{
			$photoerr='';
			define('UPLOADPATH','../blog/photo/'.$_SESSION['user_id']);
			if(!file_exists(UPLOADPATH))
				mkdir(UPLOADPATH);
			$nickname=$_POST['nickname'];
			$photoname=$_FILES['photo']['name'];
			$phototype=$_FILES['photo']['type'];
			$photosize=$_FILES['photo']['size'];
			if(empty($photoname)||($phototype!='image/png'&&$phototype!='image/jpg'&&$phototype!='image/jpeg'&&$phototype!='image/pjpeg'&&$phototype!='image/gif')||$photosize>$_POST['MAX_FILE_SIZE'])
			{
				$photoerr="图片不符合要求，请上传不大于10M的jpg、png、jpeg、pjpeg、gif格式的图片";
			}
			else {
				$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
				$query="select * from photo where filename='".$filename."'";
				$result=mysqli_query($con,$query);
				if(mysqli_num_rows($result)!=0)
					$photoerr="图片已存在";
				else {	
					if(empty($nickname))
						$nickname=$photoname;
					$target=UPLOADPATH."/".$photoname;
					if(move_uploaded_file($_FILES['photo']['tmp_name'],$target))
					{
						$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
						$query="insert into photo (filename,nickname,addTime,ownerId) values('$photoname','$nickname','".date('Y-m-d	H:i:s')."','".$_SESSION['user_id']."')";
						mysqli_query($con,$query);
						header("Location:http://localhost/blog/index.php?act=photo");
						exit();
					}
				}
			}
		}
		//删除日志检查
		if($act=='dela')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$array=$_POST['del_id'];
			foreach($array as $i=>$id)
			{	
				$query="delete from article where articleId='$id'";
				if(!$result=mysqli_query($con,$query))
					echo mysqli_error($con);
			}
			mysqli_close($con);
			header('Location:http://localhost/blog/index.php?act=article');
			exit();
		}
		if($act=='delp')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$array=$_POST['del_id'];
			foreach($array as $k=>$v)
			{
				$query="select * from photo where photoId='$v'";
				mysqli_query($con,$query);
			}
			mysql_close($con);
			header('Location:http://localhost/blog/index.php?act=photo');
			exit();
		}
		if($act=='edit')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$nameerr=$photoerr='';
			$name=mysqli_real_escape_string($con,trim($_POST['name']));
			$gender=$_POST['gender'];
			$age=$_POST['age'];
			$profile=mysqli_real_escape_string($con,trim($_POST['profile']));
			$photoname=mysqli_real_escape_string($con,trim($_FILES['photo']['name']));
			$phototype=$_FILES['photo']['type'];
			$photosize=$_FILES['photo']['size'];
			if(!empty($photoname)&&(($phototype!='image/png'&&$phototype!='image/jpg'&&$phototype!='image/jpeg'&&$phototype!='image/pjpeg'&&$phototype!='image/gif')||$photosize>$_POST['MAX_FILE_SIZE']))
			{
				$photoerr="图片不符合要求，请上传不大于10M的jpg、png、jpeg、pjpeg、gif格式的图片";
			}
			if(empty($name))
			{
				$nameerr='用户名不能为空';
			}
			if(empty($nameerr)&&empty($photoerr))
			{
				if(empty($photoname))
				{
					$query="update user set name='$name',gender='$gender',age='$age',profile='$profile' where userId='".$_SESSION['user_id']."'";
					mysqli_query($con,$query);
					header("Location:http://localhost/blog/index.php?act=index");
				}
				else {
					$headimg=$_SESSION['user_id'].$photoname;
					move_uploaded_file($_FILES['photo']['tmp_name'],'../blog/headimg/'.$headimg);
					$query="update user set name='$name',gender='$gender',age='$age',headImg='$headimg',profile='$profile' where userId='".$_SESSION['user_id']."'";
					mysqli_query($con,$query);
					header("Location:http://localhost/blog/index.php?act=index");
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<style>
	
	</style>
	<meta charset="utf-8">
	<title>博客-分享与记录</title>
</head>
<body>
<?php 
	//状态栏

	require_once('navigation.php');

	//初始化$act,$id
	if(isset($_GET['act']))
		$act=$_GET['act'];
	else if(isset($_COOKIE['user_id']))
	{
		$_SESSION['user_id']=$_COOKIE['user_id'];
		$_SESSION['user_name']=$_COOKIE['user_name'];
		$act="index";
	}
	else $act="login";

	if(isset($_GET['id']))
		$id=$_GET['id'];

	//登录页面
	if($act=='login')
	{
?>
<div class="login">
	<form action="index.php?act=login" method="post">
		<input type="hidden" name="test" value="1">
		用户名：<input type="text" name="user_name" value="<?php if(isset($name)) echo $name;?>">
		<br/><?php if(isset($nameerr)) echo $nameerr;?><br/>
		密码：<input type="password" name="user_password" value="<?php if(isset($password)) echo $password;?>">
		<br/><?php if(isset($passworderr))echo $passworderr;?>
		验证码：<input type="text" name="captcha"><img src="captcha.php">
		<br/><?php if(isset($captchaerr))echo $captchaerr;?><br/>
		<input type="submit" name="submit" value="登录">
		记住我：<input type="checkbox" name="remember" value="1">
	</form>
</div>
<?php
	}
	//个人页面
	else
	{
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
		//主页
		if($act=="index")
		{
			if(!isset($id)){
				$_SESSION['owner_id']=$_SESSION['user_id'];
				$id=$_SESSION['owner_id'];
			}
			else $_SESSION['owner_id']=$id;
			//标题栏
			require_once('title.php');
			//个人信息栏
			require_once('information.php');

			echo "<div>最近文章</div><br/>";
			$query="select * from article where writeUser='$id' limit 5";
			$result=mysqli_query($con,$query);
			while($row=mysqli_fetch_array($result))
			{
				echo "<a href='http://localhost/blog/index.php?act=article&id=".$row['articleId']."'>".$row['title']."</a><br/>";
			}

			echo "<div>最近上传的照片<div>";
			$query="select * from photo where ownerId='".$_SESSION['owner_id']." limit 5'";
			$result=mysqli_query($con,$query);
			while($row=mysqli_fetch_array($result))
			{
				echo "<a href='?act=photo&id=".$row['photoId']."'><img src='../blog/photo/".$_SESSION['owner_id']."/".$row['filename']."' width='200' height='200'/></a>";
				echo $row['nickname'];
			}
		}
		//博客页
		if($act=="article")
		{
			//展示日志
			if(isset($id))
			{
				$query="select * from article inner join user on article.writeUser=user.userId where articleId='$id';";
				if(!$result=mysqli_query($con,$query))
				{
					echo mysqli_error($con);
					exit();
				}
				$row=mysqli_fetch_array($result);
				$_SESSION['owner_id']=$row['userId'];
				require('title.php');
				require('information.php');
				
				echo "<div><h3>".$row['title']."</h3><p>".$row['writeUser']." ".$row['writeTime']."</p><p>".$row['content']."</p>";
				
			}
			//日志主页
			else{
				if(!isset($_SESSION['owner_id'])||isset($_GET['back']))
					$_SESSION['owner_id']=$_SESSION['user_id'];
				$id=$_SESSION['owner_id'];
				require_once('title.php');
				require_once('information.php');
				echo "<div>";
				if($_SESSION['user_id']==$_SESSION['owner_id'])
					echo "<a href='?act=dela'>编辑</a>";
				$pagesize=10;
				if(!isset($_GET['page']))
				{
					$page=1;
					$start=0;
					$query="select * from article where writeUser='".$id."'";
					$result=mysqli_query($con,$query);
					$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
				}
				else {
					$page=$_GET['page'];
					$start=($_GET['page']-1)*$pagesize;
				}
				
				$query="select * from article where writeUser= '$id' limit $start,$pagesize";
				$result=mysqli_query($con,$query);
				while($row=mysqli_fetch_array($result))
					echo "<a href='http://localhost/blog/index.php?act=article&id=".$row['articleId']."'>".$row['title']."</a><br/>";
				//显示分页
				if($_SESSION['page_num']>1)
				{
					if($page==1)
					{
						$next_page=$page+1;
						$end_page=$_SESSION['page_num'];
						echo "<a href='?act=article&page=$next_page'>下一页</a>";
						echo "<a href='?act=article&page=$end_page'>尾页</a>";
					}
					else if($page==$_SESSION['page_num'])
					{
						$up_page=$page-1;
						$begin_page=1;
						echo "<a href='?act=article&page=$begin_page'>首页</a>";
						echo "<a href='?act=article&page=$up_page'>上一页</a>";
					}
					else{
						$up_page=$page-1;
						$begin_page=1;
						$next_page=$page+1;
						$end_page=$_SESSION['page_num'];
						echo "<a href='?act=article&page=$begin_page'>首页</a>";
						echo "<a href='?act=article&page=$up_page'>上一页</a>";
						echo "<a href='?act=article&page=$next_page'>下一页</a>";
						echo "<a href='?act=article&page=$end_page'>尾页</a>";
					}
				}
				echo "</div>";
			}
		}
		//相册页
		if($act=="photo")
		{
			define('PATH',"../blog/photo/".$_SESSION['owner_id']."/");
			//展示照片
			if(isset($id))
			{
				require_once('title.php');
				require_once('information.php');
				$query="select * from photo where photoId='$id'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				echo "<img src='".PATH.$row['filename']."'>".$row['nickname'].$row['addTime'];
			}
			else {
				if(!isset($_SESSION['owner_id'])||isset($_GET['back']))
					$_SESSION['owner_id']=$_SESSION['user_id'];
				$id=$_SESSION['owner_id'];
				require_once('title.php');
				require_once('information.php');
				echo "<div>";
				if($_SESSION['user_id']==$_SESSION['owner_id'])
					echo "<a href='?act=delp'>编辑</a>";
				$pagesize=10;
				if(!isset($_GET['page']))
				{
					$page=1;
					$start=0;
					$query="select * from photo where ownerId='".$id."'";
					$result=mysqli_query($con,$query);
					$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
				}
				else {
					$page=$_GET['page'];
					$start=($_GET['page']-1)*$pagesize;
				}
				
				$query="select * from photo where ownerId= '$id' limit $start,$pagesize";
				$result=mysqli_query($con,$query);
				while($row=mysqli_fetch_array($result))
					echo "<a href='?act=photo&id=".$row['photoId']."'><img src='".PATH.$row['filename']."'/>".$row['nickname'].$row['addTime']."</a>";
				//显示分页
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
				echo "</div>";
			}
		}
		//添加日志页
		if($act=="adda")
		{
			require_once('title.php');
			require_once('information.php');
?>
<div>
	<form action="index.php?act=adda" method="post">
		<input type="hidden" name="test" value="1">
		标题 <input type="text" name="title">
		内容 <textarea name="content"></textarea>
		<input type="submit" name="submit" value="提交">
	</form>
</div>
<?php
		}
		//上传相片页
		if($act=="addp")
		{
			require_once('title.php');
			require_once('information.php');
?>
<div>
	<form enctype="multipart/form-data" method="post" action="http://localhost/blog/index.php?act=addp">
		<input type="hidden" name="test" value="1">
		<input type="hidden" name="MAX_FILE_SIZE" value="10485760">
		图片 <input type="file" id="photo" name="photo">
		名称 <input type="text" name="nickname">
		<?php if(isset($photoerr))echo $photoerr;?>
		<input type="submit" name="submit" value="上传">
	</form>
</div>
<?php
		}
		//删除日志
		if($act=="dela")
		{
			require_once('title.php');
			require_once('information.php');
			$id=$_SESSION['user_id'];
			echo "<div><form action='?act=dela' method='post'>";
			echo "<input type='submit' name='submit' value='删除'><br/>";
			echo "<input type='hidden' name='test' value='1'>";
			$query="select * from article where writeId='$id'";
			$result=mysqli_query($con,$query);
			$pagesize=10;
			if(!isset($_GET['page']))
			{
				$page=1;
				$start=0;
				$query="select * from article where writeUser='".$id."'";
				$result=mysqli_query($con,$query);
				$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
			}
			else {
				$page=$_GET['page'];
				$start=($_GET['page']-1)*$pagesize;
			}
				
			$query="select * from article where writeUser= '$id' limit $start,$pagesize";
			$result=mysqli_query($con,$query);
			echo "<table>";
			while($row=mysqli_fetch_array($result))
			{
				echo "<tr>";
				echo "<td width='50'><input type='checkbox' name='del_id[]' value='".$row['articleId']."'/></td>";
				echo "<td><a href='?act=article&id=".$row['articleId']."'>".$row['title']."</td>";
				echo "</tr>";
			}
			echo "</table>";
			//显示分页
			if($_SESSION['page_num']>1)
			{
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=article&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&page=$up_page'>上一页</a>";
					echo "<a href='?act=article&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&page=$end_page'>尾页</a>";
				}
			}
			echo "</div>";
		}
		if($act=="delp")
		{
			define('PATH','../blog/photo/'.$_SESSION['user_id'].'/');
			require_once('title.php');
			require_once('information.php');
			$id=$_SESSION['user_id'];
			$query="select * from photo where ownerId='$id'";
			$result=mysqli_query($con,$query);
			$pagesize=10;
			if(!isset($_GET['page']))
			{
				$page=1;
				$start=0;
				$query="select * from photo where photoId='".$id."'";
				$result=mysqli_query($con,$query);
				$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
			}
			else {
				$page=$_GET['page'];
				$start=($_GET['page']-1)*$pagesize;
			}
			$query="select * from photo where owner='$id' limtt $start,$pagesize";
			echo "<div><form action='?act=photo' method=post><table>";
			echo "<tr><input type='submit' name='submit' value='删除'>";
			while($result=mysqli_query($con,$query))
			{
				echo "<tr>";
				echo "<td><input type='checkbox' name='del_id[]' value='".$rowp['photoId']."'><img src='".PATH.$row['filename']."'></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "</form></div>";
			if($_SESSION['page_num']>1)
			{
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=article&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=article&page=$begin_page'>首页</a>";
					echo "<a href='?act=article&page=$up_page'>上一页</a>";
					echo "<a href='?act=article&page=$next_page'>下一页</a>";
					echo "<a href='?act=article&page=$end_page'>尾页</a>";
				}
			}
		}
		if($act=="edit")
		{
			require_once('title.php');
			if(!isset($_POST['submit']))
			{
				$query="select * from user where userId='".$_SESSION['user_id']."'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				$name=$row['name'];
				$headimg=$row['headImg'];
				$gender=$row['gender'];
				$age=$row['age'];
				$profile=$row['profile'];
			}
?>
<div>
<form action='?act=edit' method='post' enctype="multipart/form-data">
	<input type='hidden' name='test' value='1'>
	<?php
		if(empty($headimg))
			echo "<img src='../blog/headimg/default.jpg'>";
		else echo "<img src='../blog/headimg/$headimg'>";
	?>
	<br/>
	<input type='hidden' name='MAX_FILE_SIZE' value='10485760'>
	头像：<input type='file' name='photo' ><br/>
	昵称：<input type='text' name='name' <?php if(isset($name)) echo "value='$name'";?>>
	<span ><br/>
	性别：
	<input type='radio' name='gender' value='男' <?php if(isset($gender)&&$gender=='男')echo "checked='checked'";?>>男
	<input type='radio' name='gender' value='女' <?php if(isset($gender)&&$gender=='女')echo "checked='checked'";?>>女<br/>
	年龄：<select name='age'>
	<?php 
		for($i=1;$i<=100;$i++)
		{
			echo "<option value='$i' ";
			if($i==$age)echo "checked='checked'";
			echo ">$i</option>";
		}
	?>
	</select><br/>
	个人简介：<textarea name='profile'><?php if(isset($profile))echo $profile;?></textarea><br/>
	<input type='submit' name='submit' value='提交'>
</form>
</div>
<?php
		}
		mysqli_close($con);
	}
?>
</body>
</html>