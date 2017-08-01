<?php
$user = JFactory::getUser();
$isroot = $user->authorise('core.login.admin');

if ($isRoot == 1) {
	// Подгрузка библиотеки и ее инициализация
	mCommentsAdminInit();
}
?>

<?php 
function mCommentsAdminInit($value='') {
	mcGetLast();
}


?>