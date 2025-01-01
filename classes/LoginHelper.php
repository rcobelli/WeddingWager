<?php

class LoginHelper extends Rybel\backbone\AuthHelper {
    public function isLoggedIn() {
        return isset($_SESSION['id']);
    }

    public function isAdmin() {
        return $_SESSION['admin'];
    }

    public function login($username, $admin, $id) {
        session_unset();
        $_SESSION['id'] = $id;
        $_SESSION['admin'] = $admin;
        $_SESSION['username'] = $username;
    }
}