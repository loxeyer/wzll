<?php
require_once '../ThumbLib.inc.php';

$thumb = PhpThumbFactory::create('test.jpg');
$thumb->crop(100, 100, 300, 200);
$thumb->createWatermark('github.png', 'rb', 10);
$thumb->show();

