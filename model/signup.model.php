<?php

class User {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Method to register a user with role support
    public function registerUser($username, $password, $email, $role) {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Prepare SQL query with role column
        $stmt = $this->conn->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, ?)");
    
        if (!$stmt) {
            printf("Error preparing statement: %s.\n", $this->conn->error);
            return false;
        }
    
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
    
        // Execute and check for errors
        if (!$stmt->execute()) {
            printf("Error: %s.\n", $stmt->error); // Debugging line
            return false;
        } else {
            return true;
        }
    }

    // Function to check if username and email are already taken
    public function checkUsernameAndEmail($username, $email) {
        $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0; // True if username or email exists
    }

    // Function to validate admin code
    public function validateAdminCode($adminCode, $email) {
        $query = "SELECT * FROM admin_codes WHERE admin_code = ? AND email = ? AND is_used = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $adminCode, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0; // True if valid
    }

    // Function to mark admin code as used
    public function markAdminCodeAsUsed($adminCode) {
        $updateQuery = "UPDATE admin_codes SET is_used = 1 WHERE admin_code = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("s", $adminCode);
        return $stmt->execute();
    }
}

?>
