<?php
session_start();

include "../../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "GET"  && isset($_GET["fetchMessages"]) && (isset($_GET["business-id"]) || is_numeric($_GET["business-id"]))) {
    $businessId = isset($_GET["business-id"]) ? intval($_GET["business-id"]) : null;

    if (!$businessId) {
        echo json_encode(["success" => false, "error" => "Missing business_id"]);
        exit();
    }

    // First, check if a conversation exists for the user and business
    $sql = "SELECT id FROM conversations WHERE user_id = ? AND business_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ii", $userId, $businessId);
    $stmt->execute();
    $stmt->bind_result($conversationId);
    $stmt->fetch();
    $stmt->close();

    // If no conversation exists, return an empty array
    if (!$conversationId) {
        echo json_encode([]);
        exit();
    }

    // Now fetch messages for the found conversation
    $sql = "SELECT m.id, m.conversation_id, u.id AS user_id, u.username AS sender_name, m.message, m.timestamp 
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE m.conversation_id = ? 
            ORDER BY m.timestamp ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [
        "conversation_id" => $conversationId, // Store conversation ID
        "messages" => [] // Initialize an empty array for messages
    ];

    while ($row = $result->fetch_assoc()) {
        $data["messages"][] = [ // Append messages inside "messages" array
            "id" => htmlspecialchars($row["id"]),
            "user_id" => htmlspecialchars($row["user_id"]),
            "message" => htmlspecialchars($row["message"]),
            "timestamp" => htmlspecialchars($row["timestamp"]),
            "message_type" => $userId === $row["user_id"] ? "sent" : "received"
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($data);


    // Close connections
    $stmt->close();
    $conn->close();
    exit();

}