<?
// NOIDELVV CORP TUTORIAL

/* The array $_CORP_ACCESS_LEVELS contains the users' groups allowed to access the current website.
	Levels are the following:
	- member: NoidelVV Member access
	- admin: NoidelVV whole system's administrators
	- docente: VV's teacher
	- wifi: Wifi Administration (only for VV Wifi Manager)
*/
$_CORP_ACCESS_LEVELS = array(
	"member"
	);

// The array $_VVA_FW contains the requests for vvaccount's parts, in this case only the user-management file
$_VVA_FW = array(
	"user"
	);
// Now we'll include framework.php: it'll read $_VVA_FW and it'll give you access to User class
include("framework.php");
// This function calls session_start() specifically for noidelvv.org domain
User::startVVAccountSession();
// This variable will contain user info, whether he's logged in, otherwise it'll be false
$_user = false;

/* $_SESSION[] is a global variable (array) that contains
	- ID : ID of the current user
	- Email: email of the current user
	- loggedIn: (true/false) determines whether the user is logged in or not
	- Privileges: determines the account type (eg: member, admin, docente)
	All these variables are set by the framework and you can only read them (eg. $_SESSION['ID'] contains current user's ID)
*/
if($_SESSION['loggedIn']) {
	// If the user is logged in the system load his informations creating a new instance of the class User
	$_user = new User($_SESSION['ID']);
	/* 
		Now $_user (that's an object) contains:
			- logIn: determines whether the user is logged in or not (same as $_SESSION['loggedIn'])
			- name: user's name in UPPERCASE
			- surname: user's surname in UPPERCASE
		To access this information you use the "->" operator eg. $_user->name , $_user->logIn
		To obtain both user's name and surname in lowercase you can call $_user->getName()
	*/

	// Load the array containing the requested levels to access this page
	$_user->setAccessLevels($_CORP_ACCESS_LEVELS);
	
	if($_user->hasAccess()) {
		// This code will be executed only if the user is logged in and has right privileges.
		// You can use this eg to call the database or certain functions activated by forms
		// In this example we're creating a calculator, so let's go on
	
		if($_POST['calc']) {
			// I know there ain't any control to determine whether a || b are numbers, but who cares?
			header("Location: ?r=".((int)$_POST['a']+(int)$_POST['b']));
			die();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <meta name="theme-color" content="#192024">
    <title>NoidelVV Deep's Calculator</title>
</head>
<body>
	<? if($_user->hasAccess()): ?>
		<!-- THIS PART IS VISIBLE ONLY IF THE USER IS LOGGED IN AND HAS RIGHT PRIVILEGES -->
		<form method='post' action='index.php'>
		<input type='number' name='a' value='0'>&nbsp;+&nbsp;<input type='number' name='b' value='0'>&nbsp;<input type='submit' value='=' name='calc'>&nbsp;<input type='number' value='<? print($_GET['r']); ?>'>
		</form><br>
		<!-- $_user->getLogoutUrl() get a url targeting corp.noidelvv.it that redirect the user on this page again after log out -->
		<a href='<? print($_user->getLogoutUrl()); ?>'>Logout</a>
	<? elseif($_user->logIn): ?>
		<!-- THIS PART IS VISIBLE IF THE USER IS LOGGED IN BUT HASN'T THE RIGHT PRIVILEGES ON NOIDELVV CORP -->
        <center>You do not have the right privileges to see this page! <a href='http://corp.noidelvv.org/'>EXIT</a></center>
    <? else: ?>
		<!-- THIS PART IS VISIBLE TO ALL -->
		<!-- User::getLoginUrl() get a url targeting corp.noidelvv.it that redirect the user on this page again after log in -->
		<center>STOP!<br>NoidelVV Deep Calculator's access is restricted<br>To enter this page you must <a href='<? print(User::getLoginUrl()); ?>'>LOGIN</a></center>
     <? endif; ?>
	 
	 <!-- MIND THE DIFFERENCE between User (followed by "::") and $_user (followed by "->")! User refers to the class in general (eg. User::getLoginUrl() refers to any user), whilst $_user is an instance of User and refers to the current user (if logged in) (eg. $_user->getLogoutUrl() get the specific url for this user) -->
    </body>
</html>
