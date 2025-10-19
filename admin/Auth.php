<?php
// ----------- SIMPLE AUTH -----------
session_start();
class Auth {
    public static function login($user, $pass, $mysqli) {
        $stmt = $mysqli->prepare("SELECT id, username, password_hash FROM admins WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($pass, $row['password_hash'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_name'] = $row['username'];
                return true;
            }
        }
        return false;
    }
    public static function check() {
        return isset($_SESSION['admin_id']);
    }
    public static function requireLogin() {
        if (!self::check()) {
            header("Location: login.php"); exit;
        }
    }
    public static function logout() {
        session_destroy();
        header("Location: login.php"); exit;
    }
}
?>
