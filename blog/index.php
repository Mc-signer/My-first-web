<?php //验证
	session_start();
	require_once('dbdefine.php');

	if(isset($_GET['act']))
		$act=$_GET['act'];
	else header("Location:http://localhost/blog/home.php");
	if(isset($_GET['ownerid']))
		$ownerid=$_GET['ownerid'];
	else header("Location:http://localhost/blog/home.php");
	if(isset($_GET['articleid']))
		$articleid=$_GET['articleid'];
	if(isset($_GET['photoid']))
		$photoid=$_GET['photoid'];
	if(isset($_GET['album']))
		$album=$_GET['album'];
	if(isset($_GET['comment']))
		$comment=$_GET['comment'];
	if(isset($_GET['commentid']))
		$commentid=$_GET['commentid'];
	if(isset($_GET['replyto']))
		$replyto=$_GET['replyto'];
	if(isset($_POST['test'])&&$_POST['test']==1)
	{
		//添加日志检查
		if($act=='adda')
		{
			$title=$_POST['title'];
			$content=$_POST['content'];
			if(empty($title)&&empty($content))
			{
				header("Location:http://localhost/blog/index.php?act=article&ownerid=".$ownerid);
				exit();
			}
			else {
				if(empty($title))
					$title=substr($content,0,10);
				$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
				$query="insert into article(title,content,writeTime,writeUser) values('$title','$content','".date('Y-m-d H:i:s')."','".$_SESSION['user_id']."')";
				mysqli_query($con,$query);
				header("Location:http://localhost/blog/index.php?act=article&ownerid=".$ownerid);
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
			if(isset($_POST['album']))$album=$_POST['album'];
			$photoname=$_FILES['photo']['name'];
			$phototype=$_FILES['photo']['type'];
			$photosize=$_FILES['photo']['size'];
			if(empty($photoname)||($phototype!='image/png'&&$phototype!='image/jpg'&&$phototype!='image/jpeg'&&$phototype!='image/pjpeg'&&$phototype!='image/gif')||$photosize>$_POST['MAX_FILE_SIZE'])
			{
				$photoerr="图片不符合要求，请上传不大于10M的jpg、png、jpeg、pjpeg、gif格式的图片";
			}
			else {
				$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
				$query="select * from photo where filename='".$photoname."' and ownerId='$ownerid'";
				$result=mysqli_query($con,$query);
				if(mysqli_num_rows($result))
					$photoerr="图片已存在";
				else {	
					if(empty($nickname))
						$nickname=substr($photoname,0,10);
					$target=UPLOADPATH."/".$photoname;
					if(move_uploaded_file($_FILES['photo']['tmp_name'],$target))
					{
						$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
						$query="insert into photo (filename,nickname,addTime,ownerId,belongAlbum) values('$photoname','$nickname','".date('Y-m-d	H:i:s')."','".$_SESSION['user_id']."','$album')";
						mysqli_query($con,$query);
						header("Location:http://localhost/blog/index.php?act=photo&ownerid=".$ownerid);
						exit();
					}
				}
			}
		}
		//添加评论和回复
		if(isset($comment))
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			if($comment=='comment')
			{
				$comment=$_POST['comment'];

				$query="insert into comment(articleId,commentUser,commentTime,commentContent) values('$articleid','".$_SESSION['user_name']."','".date('Y-m-d H:i:s')."','$comment')";
				mysqli_query($con,$query);
				header("Location:http://localhost/blog/index.php?act=article&ownerid=$ownerid&articleid=$articleid");
				mysqli_close($con);
				exit();
			}
			else if($comment=='reply')
			{
				$reply=$_POST['reply'];
				$replyto=$_POST['replyto'];
				$commentid=$_POST['commentid'];
				$query="insert into reply(commentId,commentUser,replyUser,replyTime,replyContent) values('$commentid','$replyto','".$_SESSION['user_name']."','".date('Y-m-d H:i:s')."','$reply')";
				mysqli_query($con,$query);
				header("Location:http://localhost/blog/index.php?act=article&ownerid=$ownerid&articleid=$articleid");
				mysqli_close($con);
				exit();
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
			header("Location:http://localhost/blog/index.php?act=article&ownerid=".$ownerid);
			exit();
		}
		//删除照片检查
		if($act=='delp')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			if(isset($_POST['delete']))
			{
				$array=$_POST['del_id'];
				foreach($array as $k=>$v)
				{
					$query="select * from photo where photoId='$v'";
					$result=mysqli_query($con,$query);
					$row=mysqli_fetch_array($result);
					unlink("D:\\xampp\\htdocs\\blog\\photo\\".$row['ownerId']."\\".$row['filename']);
					$query="delete from photo where photoId='$v'";
					mysqli_query($con,$query);
				}
			}
			else if(isset($_POST['move']))
			{
				$array=$_POST['del_id'];
				$album=$_POST['album'];
				foreach($array as $k=>$v)
				{
					$query="update photo set belongAlbum = '$album' where photoId='$v'";
					mysqli_query($con,$query);
				}
			}
			mysqli_close($con);
			header(@"Location:http://localhost/blog/index.php?act=photo&ownerid=".$ownerid);
			exit();
		}
		//新建相册
		if($act=='addalbum')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$albumname=$_POST['albumname'];
			$query="insert into album(albumName,albumOwnerId) values('$albumname','$ownerid')";
			mysqli_query($con,$query);
			mysqli_close($con);
			header("Location:http://localhost/blog/index.php?act=photo&ownerid=$ownerid");
			exit();
		}
		//删除相册
		if($act=='delalbum')
		{
			$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
			$delalbum=$_POST['del_album'];
			foreach($delalbum as $k=>$v)
			{
				$query="select * from album where albumId = '$v'";
				$result=mysqli_query($con,$query);
				$row=mysqli_fetch_array($result);
				$query="update photo set belongAlbum = 'default' where belongAlbum = '".$row['albumName']."'";
				mysqli_query($con,$query);
				$query="delete from album where albumId = '$v'";
				mysqli_query($con,$query);
			}
			mysqli_close($con);
			header("Location:http://localhost/blog/index.php?act=photo&ownerid=$ownerid");
			exit();
		}
		//编辑个人信息
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
					header("Location:http://localhost/blog/index.php?act=index&ownerid=$ownerid");
				}
				else {
					$headimg=$_SESSION['user_id'].$photoname;
					move_uploaded_file($_FILES['photo']['tmp_name'],'../blog/headimg/'.$headimg);
					$query="update user set name='$name',gender='$gender',age='$age',headImg='$headimg',profile='$profile' where userId='".$_SESSION['user_id']."'";
					mysqli_query($con,$query);
					header("Location:http://localhost/blog/index.php?act=index&ownerid=".$ownerid);
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="main.css">
	<meta charset="utf-8">
	<title>博客-分享与记录</title>
</head>
<body>
<?php //状态栏
	

	require_once('navigation.php');

	$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);
	//主页
	if($act=="index")
	{
		//标题栏
		require_once('title.php');
		//个人信息栏
		echo "<div class='contain'>";
		require_once('information.php');

		echo "<div class='main'>";
		echo "<h3>最近文章</h3>";
		$query="select * from article where writeUser='$ownerid' limit 5";
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			echo "<a href='http://localhost/blog/index.php?act=article&ownerid=".$ownerid."&articleid=".$row['articleId']."'>".$row['title']."</a><br/>";
		}
		echo "<div class='photoarea'>";
		echo "<h3>最近上传的照片</h3>";
		$query="select * from photo where ownerId='".$ownerid."' order by addTime desc limit 5";
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			echo "<div class='photo'><a href='?act=photo&ownerid=$ownerid&photoid=".$row['photoId']."'><img src='../blog/photo/".$ownerid."/".$row['filename']."'  width='200px' height='200px'/></a>";
			echo "<div class='photo_name'>".$row['nickname']."</div></div>";
		}
		echo "</div></div></div>";
	}
	//博客页
	if($act=="article")
	{
		
		//展示日志
		if(isset($articleid))
		{
			$query="select * from article where articleId='$articleid'";
			if(!$result=mysqli_query($con,$query))
			{
				echo mysqli_error($con);
				exit('error');
			}
			$row=mysqli_fetch_array($result);
			require('title.php');
			echo "<div class='contain'>";
			require('information.php');
			
			echo "<div class='main'><h3>".$row['title']."</h3><div class='writetime'>".$row['writeTime']."</div><p>".$row['content']."</p>";
			$query="select * from comment where articleId='".$articleid."'";
			$result=mysqli_query($con,$query);
		
			echo "<div class='comment'>";
			while($row=mysqli_fetch_array($result))
			{
				echo "<div class='acomment'>";
				echo $row['commentUser']."：".$row['commentContent'];
				$query1="select * from reply where commentId='".$row['commentId']."'";
				$result1=mysqli_query($con,$query1);
				if(mysqli_num_rows($result1)){
					echo "<div class='reply'>";
					while($row1=mysqli_fetch_array($result1))
					{
						echo "<div class='areply'>";
						echo $row1['replyUser']."回复".$row1['commentUser'].":".$row1['replyContent'];
						echo "<div class='huifu'><a href='?act=article&ownerid=$ownerid&articleid=$articleid&replyto=".$row['commentUser']."&commentid=".$row['commentId']."'>回复</a></div>";
						echo "</div>";
					}
					echo "</div>";
				}
				
				echo "<div class='huifu'><a href='?act=article&ownerid=$ownerid&articleid=$articleid&replyto=".$row['commentUser']."&commentid=".$row['commentId']."'>回复</a></div>";
				echo "<div class='commenttime'>".$row['commentTime']."</div>";
				echo "</div>";
			}
			
			if(isset($_SESSION['user_id'])&&$_SESSION['user_id']!='1'&&!isset($replyto))
			{
				echo "<div class='addcomment'>";
				echo "<h5>评论</h5>";
				echo "<form action='?act=article&ownerid=$ownerid&articleid=$articleid&comment=comment' method='post'>";
				echo "<textarea name='comment'></textarea>";
				echo "<input type='hidden' name='test' value='1'>";
				echo "<input type='submit' name='submit' value='提交'>";
				echo "</form>";
				echo "</div>";
			}
			else {
				echo "<div class='addcomment'>";
				echo "<h5>回复".$replyto."</h5>";
				echo "<form action='?act=article&ownerid=$ownerid&articleid=$articleid&comment=reply' method='post'>";
				echo "<textarea name='reply'></textarea>";
				echo "<input type='hidden' name='commentid' value='$commentid'>";
				echo "<input type='hidden' name='replyto' value='$replyto'>";
				echo "<input type='hidden' name='test' value='1'>";
				echo "<input type='submit' name='submit' value='提交'>";
				echo "</form>";
				echo "</div>";
			}
			echo "</div></div></div>";
		}
		//日志主页
		else{
			require_once('title.php');
			echo "<div class='contain'>";
			require_once('information.php');
			echo "<div class='main'>";
			if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid)
				echo "<div><a href='?act=dela&ownerid=$ownerid' class='button'>编辑</a></div>";
			$pagesize=10;
			if(!isset($_GET['page']))
			{
				$page=1;
				$start=0;
				$query="select * from article where writeUser='".$ownerid."'";
				$result=mysqli_query($con,$query);
				$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
			}
			else {
				$page=$_GET['page'];
				$start=($_GET['page']-1)*$pagesize;
			}
			
			$query="select * from article where writeUser= '".$ownerid."' limit $start,$pagesize";
			$result=mysqli_query($con,$query);
			if(mysqli_num_rows($result))
			while($row=mysqli_fetch_array($result))
				echo "<a href='http://localhost/blog/index.php?act=article&ownerid=".$ownerid."&articleid=".$row['articleId']."'>".$row['title']."</a><br/>";
			else if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid)
				echo "您暂时没有任何日志，<a href='?act=adda&ownerid=$ownerid'>添加日志吧~</a>";
			else echo "他很懒，什么都没留下。";
			//显示分页
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
			echo "</div>";
			echo "</div>";
		}
	}
	//相册页
	if($act=="photo")
	{
		define('PATH',"../blog/photo/".$ownerid."/");
		//展示照片
		if(isset($photoid))
		{
			require_once('title.php');
			echo "<div class='contain'>";
			require_once('information.php');
			$query="select * from photo where photoId='$photoid'";
			$result=mysqli_query($con,$query);
			$row=mysqli_fetch_array($result);
			echo "<div class='main'>";
			echo "<img src='".PATH.$row['filename']."'><div>".$row['nickname']."</div><div>".$row['addTime']."</div>";
			echo "</div></div>";
		}
		//相册页
		else if(isset($album))
		{
			require_once('title.php');
			echo "<div class='contain'>";
			require_once('information.php');
			echo "<div class='main'>";
			if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid)
				echo "<div><a href='?act=delp&ownerid=$ownerid&album=$album' class='button'>编辑</a></div><br/>";
			$pagesize=10;
			if(!isset($_GET['page']))
			{
				$page=1;
				$start=0;
				$query="select * from photo where ownerId='".$ownerid."' and belongAlbum = '$album'";
				$result=mysqli_query($con,$query);
				$_SESSION['page_num']=ceil(mysqli_num_rows($result)/$pagesize);
			}
			else {
				$page=$_GET['page'];
				$start=($_GET['page']-1)*$pagesize;
			}
			
			$query="select * from photo where belongAlbum = '$album' and ownerId = '$ownerid' limit $start,$pagesize";
			$result=mysqli_query($con,$query);
			while($row=mysqli_fetch_array($result))
				echo "<div class='photo'><a href='?act=photo&ownerid=".$ownerid."&photoid=".$row['photoId']."'><img src='".PATH.$row['filename']."' width='200px' height='200px'/><div class='photoname'>".$row['nickname']."</div></div></a>";
			//显示分页
			if($_SESSION['page_num']>1)
			{
				echo "<div class='page'>";
				if($page==1)
				{
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
				else if($page==$_SESSION['page_num'])
				{
					$up_page=$page-1;
					$begin_page=1;
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
				}
				else{
					$up_page=$page-1;
					$begin_page=1;
					$next_page=$page+1;
					$end_page=$_SESSION['page_num'];
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
					echo "<a href='?act=photo&album=$album&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
				}
				echo "</div>";
			}
			echo "</div></div>";
		}
		//相片主页
		else {
			require_once('title.php');
			echo "<div class='contain'>";
			require_once('information.php');
			echo "<div class='main'>";
			if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid){
				echo "<a href='?act=addalbum&ownerid=$ownerid' class='button'>新建相册</a>";
				echo " <a href='?act=delalbum&ownerid=$ownerid' class='button'>删除相册</a>";
				echo "<br/><br/>";
			}
			
			$query="select * from album where albumOwnerId='".$ownerid."'";
			$result=mysqli_query($con,$query);
			$query="select * from photo where ownerId='$ownerid'";
			$result2=mysqli_query($con,$query);
				
			if(mysqli_num_rows($result2))
			{
				$query1="select * from photo where belongAlbum = 'default' and ownerId='$ownerid' limit 1";
				$result1=mysqli_query($con,$query1);
				if(mysqli_num_rows($result1)){
					$row1=mysqli_fetch_array($result1);
					echo "<div class='album'>";		
					echo "<a href='?act=photo&ownerid=$ownerid&album=default'>";
					echo "<img src='../blog/photo/".$ownerid."/".$row1['filename']."' width='180' height='180'>";						
					echo "<div class='albumname'>我的相片</div>";
					echo "</div>";
				}
				if(mysqli_num_rows($result))
				{	
					while($row=mysqli_fetch_array($result))
					{
						echo "<div class='album' width='200' height='200'>";
						$query1="select * from photo where belongAlbum = '".$row['albumName']."' limit 1";
						$result1=mysqli_query($con,$query1);
						if(mysqli_num_rows($result1)){
							$row1=mysqli_fetch_array($result1);
							echo "<a href='?act=photo&ownerid=$ownerid&album=".$row['albumName']."'>";
							echo "<img src='../blog/photo/".$ownerid."/".$row1['filename']."' width='180' height='180'>";						
						}
						else {
							echo "<a href='?act=photo&ownerid=$ownerid&album=".$row['albumName']."'>";
							echo "<img src='../blog/headimg/default.jpg' width='180' height='180'>";
						}
						echo "<div class='albumname'>".$row['albumName']."</div></a></div>";
					}
				}
			}
			else if(isset($_SESSION['user_id'])&&$_SESSION['user_id']==$ownerid)
				echo "您暂时没有任何相片，<a href='http://localhost/blog/index.php?act=addp&ownerid=$ownerid'>上传相片吧~</a>";
			else echo "他很懒，还没有任何相片。";
		}
	}
	//添加日志页
	if($act=="adda")
	{
		require_once('title.php');
		echo "<div class='contain'>";
		require_once('information.php');
		echo "<div class='main'>";

?>
<div>
	<form action="index.php?act=adda&ownerid=<?php echo $ownerid;?>" method="post">
		<input type="hidden" name="test" value="1">
		标题 <input type="text" name="title"><br/><br/>
		内容 <textarea name="content" ></textarea>
		<input type="submit" name="submit" value="提交">
	</form>
</div>
<?php
	echo "</div>";
	}
	
	//上传相片页
	if($act=="addp")
	{
		require_once('title.php');
		echo "<div class='contain'>";
		require_once('information.php');
		echo "<div class='main'>";
?>
<div>
<form enctype="multipart/form-data" method="post" action="http://localhost/blog/index.php?act=addp&ownerid=<?php echo $ownerid;?>">
	<input type="hidden" name="test" value="1">
	<input type="hidden" name="MAX_FILE_SIZE" value="10485760">
	图片 <input type="file" id="photo" name="photo">
	名称 <input type="text" name="nickname">
	相册 <select name="album"><option value='default'>我的相册</option>
<?php
	$query="select * from album where albumOwnerId = '".$_SESSION['user_id']."'";
	$result=mysqli_query($con,$query);
	while($row=mysqli_fetch_array($result))
	{
		echo "<option value='".$row['albumName']."'>".$row['albumName']."</option>";
	}
?>
	</select>
	<input type="submit" name="submit" value="上传">
</form>
</div>
<div class='error'>
<?php if(isset($photoerr))echo $photoerr;?>
</div>
相片名请不要包含中文。
<?php
		echo "</div></div>";
	}
	//新建相册页
	if($act=="addalbum")
	{
		require_once('title.php');
		require_once('information.php');
		echo "<div class='main'><form action='?act=addalbum&ownerid=$ownerid' method='post'>";
		echo "<input type='hidden' name='test' value='1'>";
		echo "相册名:<input type='text' name='albumname'>";
		echo "<input type='submit' name='submit' value='创建'>";
		echo "</form></div>";
	}
	//删除相册页
	if($act=="delalbum")
	{
		require_once('title.php');
		echo "<div class='contain'>";
		require_once('information.php');
		echo "<div class='main'><div>删除相册后相册内所有照片会归入默认相册中</div>";
		echo "<form action='?act=delalbum&ownerid=$ownerid' method='post'>";
		echo "<input type='hidden' name='test' value='1'>";
		echo "<input type='submit' name='submit' value='删除'>";
		$query="select * from album where albumOwnerId='".$ownerid."'";
		$result=mysqli_query($con,$query);
		while($row=mysqli_fetch_array($result))
		{
			
			echo "<div class='album' width='200' height='200'>";
			$query1="select * from photo where belongAlbum = '".$row['albumName']."' limit 1";
			$result1=mysqli_query($con,$query1);
			if(mysqli_num_rows($result1)){
				$row1=mysqli_fetch_array($result1);
				echo "<a href='?act=photo&ownerid=$ownerid&album=".$row['albumName']."'>";
				echo "<img src='../blog/photo/".$ownerid."/".$row1['filename']."' width='180' height='180'>";						
			}
			else {
				echo "<a href='?act=photo&ownerid=$ownerid&album=".$row['albumName']."'>";
				echo "<img src='../blog/headimg/default.jpg' width='180' height='180'>";
			}
			echo "<div class='albumname'><input type='checkbox' name='del_album[]' value='".$row['albumId']."'>".$row['albumName']."</div></a></div>";
		}
		echo "</form></div></div>";
	}
	//删除日志
	if($act=="dela")
	{
		require_once('title.php');
		echo "<div class='contain'>";
		require_once('information.php');
		$id=$_SESSION['user_id'];
		echo "<div class='main'><form action='?act=dela&ownerid=$ownerid' method='post'>";
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
			echo "<td><a href='?act=dela&id=".$row['articleId']."'>".$row['title']."</td>";
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
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
			}
			else if($page==$_SESSION['page_num'])
			{
				$up_page=$page-1;
				$begin_page=1;
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
			}
			else{
				$up_page=$page-1;
				$begin_page=1;
				$next_page=$page+1;
				$end_page=$_SESSION['page_num'];
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
				echo "<a href='?act=dela&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
			}
		}
		echo "</div></div>";
	}
	//删除相片
	if($act=="delp")
	{
		define('PATH','../blog/photo/'.$_SESSION['user_id'].'/');
		require_once('title.php');
		echo "<div class='contain'>";
		require_once('information.php');
		$id=$_SESSION['user_id'];
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
		$query="select * from photo where ownerId='$id' and belongAlbum = '$album' limit $start,$pagesize";
		$result=mysqli_query($con,$query);
		echo "<div class='main'><form action='?act=delp&ownerid=$ownerid' method='post'>";
		echo "<select name='album'>";
		$query1="select * from album where albumOwnerId = '$id'";
		$result1=mysqli_query($con,$query1);
		while($row1=mysqli_fetch_array($result1))
		{
			echo "<option value='".$row1['albumName']."'>".$row1['albumName']."</option>";
		}
		echo "<input type='submit' name='move' value='移动相片'></select>";
		echo "<input type='submit' name='delete' value='删除'><br/><br/>";
		echo "<input type='hidden' name='test' value='1'>";
		while($row=mysqli_fetch_array($result))
		{
			echo "<div class='photo'>";
			echo "<img src='".PATH.$row['filename']."' width='150px' height='150px'>";
			echo "<div><input type='checkbox' name='del_id[]' value='".$row['photoId']."'>".$row['nickname']."</div>";
			echo "</div>";
		}
		
		echo "</form>";
		if($_SESSION['page_num']>1)
		{
			if($page==1)
			{
				$next_page=$page+1;
				$end_page=$_SESSION['page_num'];
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
			}
			else if($page==$_SESSION['page_num'])
			{
				$up_page=$page-1;
				$begin_page=1;
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
			}
			else{
				$up_page=$page-1;
				$begin_page=1;
				$next_page=$page+1;
				$end_page=$_SESSION['page_num'];
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$begin_page'>首页</a>";
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$up_page'>上一页</a>";
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$next_page'>下一页</a>";
				echo "<a href='?act=delp&album=$album&ownerid=".$ownerid."&page=$end_page'>尾页</a>";
			}
		}
		echo "</div></div>";
	}
	//编辑个人信息
	if($act=="edit")
	{
		require_once('title.php');
		echo "<div class='contain'>";
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
			$_SESSION['user_name']=$name;
			if(isset($_COOKIE['user_name']))
			{
				setcookie('user_id',$_SESSION['user_id'],time()+60*60*24*7);
				setcookie('user_name',$name,time()+60*60*24*7);
			}
		}
		echo "<div class='main'>";
?>
<div>
<form action='?act=edit&ownerid=<?php echo $ownerid;?>' method='post' enctype="multipart/form-data">
<input type='hidden' name='test' value='1'>
<?php
	if(empty($headimg))
		echo "<img src='../blog/headimg/default.jpg' width='200px' height='200px'>";
	else echo "<img src='../blog/headimg/$headimg' width='200px' height='200px'>";
?>
<br/>
	<input type='hidden' name='MAX_FILE_SIZE' value='10485760'>
	头像：<input type='file' name='photo'><br/>
	昵称：<input type='text' name='name' <?php if(isset($name)) echo "value='$name'";?>>
	<br/>
	性别：
	<input type='radio' name='gender' value='男' <?php if(isset($gender)&&$gender=='男')echo "checked='checked'";?>>男
	<input type='radio' name='gender' value='女' <?php if(isset($gender)&&$gender=='女')echo "checked='checked'";?>>女<br/>
	年龄：<select name='age'>
<?php 
	for($i=1;$i<=100;$i++)
	{
		echo "<option value='$i' ";
		if($i==$age)echo "selected='selected'";
		echo ">$i</option>";
	}
?>
</select><br/>
个人简介：<textarea name='profile'><?php if(isset($profile))echo $profile;?></textarea><br/>
<input type='submit' name='submit' value='提交'>
</form>
</div>
<?php
	echo "</div></div>";
	}

	mysqli_close($con);
?>
</body>
</html>