<?php

namespace App\Application\Session;

class Session
{
    public function getUser() : User
    {
        return new User();
    }
}
