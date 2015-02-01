<?php
$data = $_POST['image'];

$data = substr($data, 21, strlen($data));
$data = base64_decode($data);

file_put_contents('image.png', $data);