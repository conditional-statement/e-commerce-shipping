<?php
require_once('../inc/config.php');

if (isset($_POST['id'])) {
	$id = $_POST['id'];
	Session::removeItem($id);
}