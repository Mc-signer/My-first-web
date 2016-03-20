<?php 
	session_start();
	require_once('dbdefine.php');
	//验证表单信息
	$flag=0;
	if(isset($_POST['submit']))
	{
		$con=mysqli_connect(DB_HOST,DB_USER,DB_PW,DB_NAME);

		//检测输入是否正确
		$nameerr=$passworderr=$rpassworderr=$captchaerr='';
		$name=trim($_POST['name']);
		$password=trim($_POST['password']);
		$rpassword=trim($_POST['rpassword']);
		$gender=$_POST['gender'];
		$profile=$_POST['profile'];
		$captcha=$_POST['captcha'];
		$age=$_POST['age'];
		if(empty($name)){
			$nameerr="用户名不能为空";
		}
		else {
			$query="select name from user where name='".$name."'";
			$result=mysqli_query($con,$query);
			if(mysqli_num_rows($result)!=0)
				$nameerr="该用户名已存在";
		}
		if(empty($password)){
			$passworderr="密码不能为空";
		}
		/*有错误
		else if(!preg_match('/^.[6,16]$/',$password))
			$passworderr="密码最小为6位，最多16位";*/
		if(empty($rpassword)){
			$rpassworderr="请输入确认密码";
		}
		else if($password!=$rpassword)
			$rpassword="确认密码与密码不一致";
		if(empty($captcha))
			$captchaerr="请输入验证码";
		else if(sha1($captcha)!=sha1($_SESSION['captcha']))
			$captchaerr="验证码错误，请重试";

		//如果正确输入到数据库
		if(!$nameerr&&!$passworderr&&!$rpassworderr&&!$captchaerr)
		{
			
			$query="insert into user(name,password,gender,age,signUpDate) ".
					"values('$name','".sha1($password)."','$gender','$age','".date('Y-m-d')."')";
			if(mysqli_query($con,$query))
			{
				header("Refresh: 3; url=http://localhost/blog/index.php");			
			}
			else echo mysqli_error($con);
		}
		mysqli_close($con);
	}
?>
<!DOCTYPE html>
<html>
<head>
<style>
.error{
	color:red;
}
</style>
	<meta charset="utf-8">
	<title>注册</title>
</head>
<body>
<?php 
	require_once('navigation.php');

//提示注册成功，3秒后跳转
	if($flag){
		echo "注册成功！即将跳转至首页";
		exit();
	}
?>
<div class='form'>
  <form action="registration.php" method="post">
	<table>
		<tr>
			<td>用户名：</td>
			<td><input type="text" name="name" value="<?php if(isset($name))echo $name;?>"/></td><td class="error">*
			<?php if(isset($nameerr)&&!empty($nameerr)) echo $nameerr;?></td>
		</tr>
		<tr>
			<td>密码：</td>
			<td><input type="password" name="password"/></td><td class="error">*
			<?php if(isset($passworderr)&&!empty($passworderr)) echo $passworderr;?></td>
		</tr>
		<tr>
			<td>确认密码：</td>
			<td><input type="password" name="rpassword"/></td><td class="error">*
			<?php if(isset($rpassworderr)&&!empty($nameerr)) echo $rpassworderr;?></td>
		</tr>
		<tr>
			<td>性别：</td>
			<td><input type="radio" name="gender" value="男" checked='checked'/>男</td>
			<td><input type="radio" name="gender" value="女" <?php if(isset($gender)&&$gender=='女')echo "checked='checked'"?>/>女</td>
		</tr>
		<tr>
			<td>年龄：</td>
			<td><?php
				echo "<select name='age'>";
				for($i=1;$i<=100;$i++)
				{
					echo "<option value='$i'>$i</option>";
				}
				echo "</select>";
			?>
			</td>
		</tr>
		<tr>
			<td>个人简介：</td>
			<td><textarea name="profile"><?php if(isset($profile))echo $profile;?></textarea></td>
		</tr>
		<tr>
			<td>验证码：</td>
			<td><input type="text" name="captcha"/></td>
			<td><img src="captcha.php" alt="Veitication pass-phrase"></td><td class="error">*
				<?php if(isset($captchaerr)&&!empty($captchaerr))echo $captchaerr;?>
		</tr>
	</table>
	<input type="submit" name="submit" value="注册">
  </form>
</div>
</body>
</html>
