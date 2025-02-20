<?php
@$pages = $_GET['pages'];
switch ($pages) {

    case 'home':
        include "../pages/master/home.php";
        break;


    default:
        include '../pages/master/home.php';
        break;
}
