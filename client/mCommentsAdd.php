<?php 
define('_JEXEC', 1); define('DS', DIRECTORY_SEPARATOR);    
define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){3}$/', '', dirname(__FILE__)));

require_once (JPATH_BASE .DS.'includes'.DS.'defines.php');
require_once (JPATH_BASE .DS.'includes'.DS.'framework.php');

$email = $_POST['email'];
$level = $_POST['level'];
$msg = $_POST['msg'];
$parent = $_POST['parent'];
$branchId = $_POST['branchId'];
$table = $_POST['table'];

$db = JFactory::getDbo();

if ( !preg_match('/'.$db->getPrefix().'mcomments_.+/', $table) ) {
	return 0;
}

$comment = (object) array(
	'email' => $email,
	'message' => $msg,
	'parent' => $parent,
	'branchId' => $branchId,
	'level' => $level,
	'utime' => date('U')
);

$id = 0;
$db->insertObject($table, $comment);

$comment->id = (int)$db->insertid();
if ($comment->level == 0) {
	$comment->branchId = $comment->id;
	$db->updateObject($table, $comment, 'id');
}

$lastComment = (object) array(
	'mcid' => $comment->id,
	'table_name' => $table
);
$db->insertObject('#__mcomments_last', $lastComment);

echo json_encode($comment);
return 1;
?>