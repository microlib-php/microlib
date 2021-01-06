<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 26.11.2020
 * Time: 16:57
 */

namespace lib;


interface Handler
{
    public function process($update,$state=null);
}