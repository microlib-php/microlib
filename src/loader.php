<?php
/**
 * Created by PhpStorm.
 * User: Kakashi
 * Date: 24.11.2020
 * Time: 20:23
 */


foreach (glob('./src/*.php') as $file) {
    if ($file != './src/loader.php') require_once $file;
}