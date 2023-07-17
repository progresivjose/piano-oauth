<?php

namespace Progresivjose\PianoOauth\Entities;

final class Result
{
    public function __construct(private mixed $content, private String $type)
    {
    }

    public function get(): mixed
    {
        return $this->content;
    }

    public function getType(): String
    {
        return $this->type;
    }
}
