<?php

namespace Progresivjose\PianoOauth\Adapters;

use Progresivjose\PianoOauth\Responses\Response;

interface HttpClientAdapter
{
    public function get(String $url, array $options = []): Response;

    public function post(String $url, array $options = []): Response;
}
