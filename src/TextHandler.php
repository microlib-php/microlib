<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 26.11.2020
 * Time: 16:47
 */

namespace lib;


class TextHandler implements Handler
{
    protected $api;

    function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function process($message,$state=null)
    {
    }
}