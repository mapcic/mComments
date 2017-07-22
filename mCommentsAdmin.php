<?php
$user = JFactory()->getUser();
if ($user->id) {
	getAdminForm();
}
?>