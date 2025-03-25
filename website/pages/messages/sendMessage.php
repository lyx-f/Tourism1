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
    $businessId = isset($_POST["business_id"]) ? intval($_POST["business_id"]) : null;
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

    if (!$businessId || empty($message)) {
        echo json_encode(["success" => false, "error" => "Missing required fields"]);
        exit();
    }

    // If conversation ID is null, create a new conversation
    if (!$conversationId) {
        $sql = "INSERT INTO conversations (business_id, user_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("ii", $businessId, $userId);

        if ($stmt->execute()) {
            $conversationId = $stmt->insert_id; // Get newly created conversation ID
        } else {
            echo json_encode(["success" => false, "error" => "Failed to create conversation"]);
            exit();
        }

        $stmt->close();
    }

    // Insert the message into the messages table
    $sql = "INSERT INTO messages (conversation_id, sender_id, message, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("iis", $conversationId, $userId, $message);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Message sent successfully", "conversation_id" => $conversationId]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to send message"]);
    }

    $stmt->close();
    $conn->close();
    exit();

}