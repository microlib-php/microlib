<?php

namespace lib;

class AudioHandler
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
