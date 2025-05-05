<?php
session_start();
$username = $_SESSION['user']['username'];
echo $username;