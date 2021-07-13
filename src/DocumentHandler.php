<?php

namespace lib;

class DocumentHandler
{
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function process($message, $state = null)
    {
    }
}
