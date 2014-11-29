<?php

error_reporting(0);
session_start();
mb_internal_encoding("UTF-8");

$host = 'http://algoprog.com/cityreport';

$issues = array(
	array('title'=>'Πυρκαγιά','email'=>'xsamarinas@gmail.com'),
	array('title'=>'Τροχαίο ατύχημα','email'=>'test2@algoprog.com'),
	array('title'=>'Δεν υπάρχουν κάδοι','email'=>'test1@algoprog.com'),
	array('title'=>'Άλλο πρόβλημα','email'=>'test2@algoprog.com')
);

?>