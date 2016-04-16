<?php 
	session_start();
	require_once('dbdefine.php');

	//验证登录信息
	if(isset($_POST['submit']))
	{	
		$name=$_POST['user_name'];
		$password=$_POST['user_password'];
		$captcha=$_POST['captcha'];
		if(isset($_POST['remember']))$remember=$_POST['remember'];
		$user_nameerr=$user_passworderr=$captcherr=$banerr="";
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
			if($row['ban']=='1')
				$banerr="此账号已被封号，无法登陆，请联系管理员邮箱：123456789@qq.com";
		}
		else
		{
			$nameerr="用户不存在";
		}
		if(empty($nameerr)&&empty($passworderr)&&empty($captchaerr)&&empty($banerr))
		{
			$query="select * from user where name='$name'";
			$result=mysqli_query($con,$query);
			$row=mysqli_fetch_array($result);
			$_SESSION['user_name']=$name;
			$_SESSION['user_id']=$row['userId'];
			if($name=="admin")
			{
				$_SESSION['admin']=1;
				header("Location:http://localhost/blog/admin.php");
			}
			else {
				if(isset($remember)){
					setcookie('user_name',$name,time()+60*60*24*7);
					setcookie('user_id',$row['userId'],time()+60*60*24*7);
				}
				header("Location:http://localhost/blog/home.php");
			}
			mysqli_close($con);
			exit();
		}
		mysqli_close($con);
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="main.css">
	<title>登录</title>
	<meta charset="utf-8">
</head>
<body>
<?php
	require_once('navigation.php');
?>
<div class="contain">
	<form action="login.php" method="post">
	<table>
		<input type="hidden" name="test" value="1">
		<tr><td>用户名：</td><td><input type="text" name="user_name" value="<?php if(isset($name)) echo $name;?>"></td></tr>
		<div class='error'><?php if(isset($nameerr)) echo $nameerr;?></div>
		<tr><td>密码：</td><td><input type="password" name="user_password" value="<?php if(isset($password)) echo $password;?>"></td></tr>
		<div class='error'><?php if(isset($passworderr))echo $passworderr;?></div>
		<tr><td>验证码：</td><td><input type="text" name="captcha"></td><td><img src="captcha.php"></td></tr>
		<div class='error'><?php if(isset($captchaerr))echo $captchaerr;?></div>
		<div class='error'><?php if(isset($banerr)) echo $banerr;?></div>
		<tr><td><input type="submit" name="submit" value="登录"></td>
		<td>记住我：<input type="checkbox" name="remember" value="1"></td></tr>
	</table>
	</form>
</div>
</body>
</html>