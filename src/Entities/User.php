<?php

namespace Progresivjose\PianoOauth\Entities;

final class User
{
    public function __construct(private String $uid, private String $firstName, private String $lastName, private String $personalName, private String $email)
    {
    }

    public function getUID(): String
    {
        return $this->uid;
    }

    public function getFirstName(): String
    {
        return $this->firstName;
    }

    public function getLastName(): String
    {
        return $this->lastName;
    }

    public function getPersonalName(): String
    {
        return $this->personalName;
    }

    public function getEmail(): String
    {
        return $this->email;
    }

}
