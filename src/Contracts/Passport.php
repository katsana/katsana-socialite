<?php

namespace Katsana\Socialite\Contracts;

use Laravel\Socialite\Contracts\User;

interface Passport extends User
{
    /**
     * Get the raw user array.
     *
     * @return array
     */
    public function getRaw();
}
