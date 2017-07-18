<?php 
define('_JEXEC', 1); define('DS', DIRECTORY_SEPARATOR);    
define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){3}$/', '', dirname(__FILE__)));

require_once (JPATH_BASE .DS.'includes'.DS.'defines.php');
require_once (JPATH_BASE .DS.'includes'.DS.'framework.php');

$email = $_POST['email'];
$msg = $_POST['msg'];
$parent = $_POST['parent'];

$comment = (object) array(
	'email' => $email,
	'message' => $msg,
	'parent' => $parent,
	'utime' => date('U')
);

$id = 0;
$db = JFactory::getDbo();
$db->insertObject('#__mcomments', $comment, $id);

$comment->id = $id;

echo json_incode($comment);
return 1;
?>