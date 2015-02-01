<?php
header('Access-Control-Allow-Origin: *');

$data = $_POST['image'];

$data = substr($data, 22, strlen($data));
$data = base64_decode($data);

file_put_contents('image.png', $data);