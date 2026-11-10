<?php
require_once 'class/Member.php';
$member = new Member();

var_dump($member->getAllMembers());
var_dump($member->createMember('Tes Satu', '0811', 'tes@mail.com'));
var_dump($member->createMember('Tes Dua', '0822', 'tes@mail.com'));
?>