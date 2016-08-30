<?php

namespace Zobr\Toolbox;

class Session {

    /**
     * A PSR-7 middleware for authentication.
     * @param  callable $onFailure
     * @return callable
     */
    public function authenticate(callable $onFailure = null) {
        $self = $this;
        return function ($req, $res, $next) use ($self) {
            // Authenticate
            if ($self->isAuthenticated()) {
                return $next($req, $res);
            }
            // Handle failure
            if ($onFailure) {
                return $onFailure($req, $res);
            }
            // Default response
            return $res->withStatus(401);
        };
    }

    /**
     * Logs in a user without any authentication.
     * This method must be implemented by the target class.
     * @param  string  $username
     * @param  string  $password
     * @return boolean Is authentication successful
     */
    public function login($username, $password) {
        $_SESSION['session_user'] = $username;
        return true;
    }

    /**
     * Logs out a user.
     * @return self
     */
    public function logout() {
        session_destroy();
        return $this;
    }

    /**
     * Checks if a session user is authenticated.
     * @return boolean
     */
    public function isAuthenticated() {
        return isset($_SESSION['session_user']);
    }

}
