<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 26.11.2020
 * Time: 18:18
 */

namespace lib;


class CallbackHandler
{
    protected $api;

    function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function process($callback, $state = null)
    {

    }
}