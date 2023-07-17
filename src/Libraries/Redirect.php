<?php

namespace Progresivjose\PianoOauth\Libraries;

class Redirect
{
    public function to(String $url): void
    {
        header("Location: {$url}");
        exit();
    }
}
