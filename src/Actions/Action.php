<?php

namespace Progresivjose\PianoOauth\Actions;

use Progresivjose\PianoOauth\Entities\Result;

interface Action
{
    public function perform(): ?Result;

}
