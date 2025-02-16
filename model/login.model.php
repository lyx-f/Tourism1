<?php
class UserModel {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Verify user credentials
    public function verifyUser($usernameOrEmail, $password) {
        $sql = "SELECT id, username, email, password_hash, role FROM users WHERE username = ? OR email = ?";
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bind_param('ss', $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify the password hash
            if (password_verify($password, $user['password_hash'])) {
                return $user; // Return user data if valid
            }
        }
        return false; // Invalid credentials
    }
}
?>
