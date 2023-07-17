<?php

namespace Progresivjose\PianoOauth\Responses;

use Progresivjose\PianoOauth\Enums\HttpStatusCode;

class Response
{
    public function __construct(protected HttpStatusCode $status, protected mixed $content, protected String $contentType = 'string')
    {
    }

    public function getStatus(): HttpStatusCode
    {
        return $this->status;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getContentType(): String
    {
        return $this->contentType;
    }
}
