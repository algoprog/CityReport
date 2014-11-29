<?php

error_reporting(0);
session_start();
mb_internal_encoding("UTF-8");

$host = 'http://algoprog.com/cityreport';

$issues = array(
	array('title'=>'Πυρκαγιά','email'=>'test1@gmail.com'),
	array('title'=>'Τροχαίο ατύχημα','email'=>'test2@algoprog.com'),
	array('title'=>'Δεν υπάρχουν κάδοι','email'=>'test3@algoprog.com'),
	array('title'=>'Άλλο πρόβλημα','email'=>'test4@algoprog.com')
);

?>