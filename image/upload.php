<?php
header('Access-Control-Allow-Origin: *');

$data = $_POST['image'];

$data = substr($data, 22, strlen($data));

$data = str_replace(' ', '+', $data);

$data = base64_decode($data);

file_put_contents('tmp/' . time() . '.png', $data);