<?php
$error = '';

if(isset($_POST['submit'])){
	$passwordq = $this->_db->query("SELECT value 
					FROM things t
					JOIN things_data d ON d.ID = t.ID 
					WHERE t.type = 'user' AND t.Name = '{$_POST['name']}' AND t.Status = 1 AND d.Field = 'Password'");
	if ($password = $passwordq->fetch()){
		if ($password['password'] = MD5($_POST['password'])) {
			setcookie('_e_admin','1',0,'/');
			if(isset($_POST['destination'])){
				$this->_goto($_POST['destination']);
			} else {
				$this->_goto('admin');
			}
		} else {
			$error = 'Password incorrect';
		}
	} else {
		$error = 'User name does not exist';
	}
}

if(isset($_GET['logout'])){
	setcookie('_e_admin','-1',0,'/');
	$this->_goto('');
}

?>

<form action='admin/user?debug=true' method='POST'>
	<h1>Please Log In to Continue</h1>
	<?php if($error !== '') { print "<h2>$error</h2>"; } ?>
	<h3>User Name</h3>
	<input type='text' name='name' size='50'><br>
	<h3>Password</h3>
	<input type='password' name='password' size='50'><br><br>
	<input type='submit' name='submit' value='Log In' />
</form>
