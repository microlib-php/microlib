<?php

namespace lib;

class InlineHandler
{
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function process($query)
    {
    }
}