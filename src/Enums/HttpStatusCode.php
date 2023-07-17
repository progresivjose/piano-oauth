<?php

namespace Progresivjose\PianoOauth\Enums;

enum HttpStatusCode: int
{
    case OK = 200;
    case NOT_FOUND = 404;
    case BAD_REQUEST = 400;
    case NOT_AUTHORIZED = 403;
    case INTERNAL_SERVER_ERROR = 500;
}
