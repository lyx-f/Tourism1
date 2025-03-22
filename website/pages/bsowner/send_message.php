<?php
session_start();
include "../../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["sendMessage"])) {
    // Validate and sanitize inputs
    $conversationId = isset($_POST["conversation_id"]) ? intval($_POST["conversation_id"]) : null;
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

    // Check if required fields are available
    if (!$conversationId || empty($message)) {
        echo json_encode(["success" => false, "error" => "Missing required fields"]);
        exit();
    }

    // Prepare SQL statement to insert message
    $sql = "INSERT INTO messages (conversation_id, sender_id, message, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit();
    }

    // Bind parameters (assuming $userId is the logged-in user's ID)
    $stmt->bind_param("iis", $conversationId, $userId, $message);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Message sent successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to send message"]);
    }

    // Close the statement
    $stmt->close();
    $conn->close();
    exit();
}