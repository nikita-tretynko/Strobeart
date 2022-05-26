<?php

/**
 * COOKIE INSTAGRAM USER REGISTRATION SERVICE
 * @version 1.0.0
 */

namespace App\Libraries\CookieInstagram;

use App\Libraries\CookieInstagram\InstaLite\Exception;

class InstagramUserRegistrationService
{
    /**
     * Check user data
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function checkUserData(string $username, string $password): bool
    {
        try {
            new InstagramService($username, $password);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
