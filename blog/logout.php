<?
	session_start();
	$_SESSION=array();
	session_destroy;
	if(isset($_COOKIE['user_id']))
	{
		setcookie('user_id','',time()-60*60);
		setcookie('user_name','',time()-60*60);
	}
	if(isset($_COOKIE[session_name()]))
	{
		setcookie(session_name(),'',time()-60*60);
	}
	header('Location:http://localhost/blog/index.php');
?>