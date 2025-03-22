<?php
session_start();
include "../../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["conversation_id"])) {
    $sql = "SELECT 
    m.id, 
    m.conversation_id, 
    m.sender_id, 
    sender.username AS sender_name, 
    m.message, 
    m.timestamp,
    c.user_id AS receiver_id,
    receiver.username AS receiver_name
FROM messages m
JOIN users sender ON m.sender_id = sender.id
JOIN conversations c ON m.conversation_id = c.id
JOIN users receiver ON c.user_id = receiver.id
WHERE m.conversation_id = ?
ORDER BY m.timestamp ASC;";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("i", $_GET["conversation_id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => htmlspecialchars($row["id"]),
            "conversation_id" => htmlspecialchars($row["conversation_id"]),
            "receiver_name" => htmlspecialchars($row["receiver_name"]),
            "user_id" => htmlspecialchars($row["sender_id"]),
            "message" => htmlspecialchars($row["message"]),
            "timestamp" => htmlspecialchars($row["timestamp"]),
            "message_type" => $userId === $row["sender_id"] ? "sent" : "received"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);

    $stmt->close();
    $conn->close();
    exit();
}
?>