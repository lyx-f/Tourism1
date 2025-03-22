<?php
session_start();
include "../../../config/database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];



?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .message-container {
        border: 1px solid lightgray;
        border-radius: 20px;

      
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .message-header{
        border-radius: 10px 10px 0px 0px;
        background: rgb(2, 90, 90);
    }
    .message-title{
        color: #e0e0e0;
        padding:10px;
        margin: 0;
    }
    .message-section {
        overflow-y: auto;
        padding: 20px;
        max-height: calc(100vh - 40%);
    }

    .message {
    padding: 10px;
    margin: 5px;
    border-radius: 10px;
    max-width: 40%; /* Maximum width constraint */
    min-width: fit-content; /* Ensures the width starts small */
    width: fit-content; /* Adjusts the width based on content */
    display: flex;
    flex-direction: column;
    gap: 10px;
    word-wrap: break-word; /* Ensures long words don't overflow */
}
   
    .sent {
        background: rgb(2, 90, 90);
        color: white;
        text-align: right;
        margin-left: auto;
    } 
    .sent>span{
        font-size: 10px;
        color:rgb(172, 172, 172) ;
    }

    .received {
        background: #e0e0e0;
        color: black;
        text-align: left;
    }
    .received>span{
        font-size: 10px;
        color:rgb(51, 50, 50);
    }
    .chat-container {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-direction: row;
        padding: 20px;
    }

    .chat-input {
        width: 100%;
        border-radius: 10px;
        border: 1px solid lightgray;
    }

    .send-btn {
        padding: 10px;
        background: rgb(2, 90, 90);
        color: white;
        width: 100px;
        border: none;
        cursor: pointer;
        border-radius: 10px;
    }
</style>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Overview</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="feedbacks.php">Feedbacks</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="edit-business.php?business_id=<?php echo $business_id; ?>">Edit Information</a></li>
                <li><a href="../logout.php">Logout</a></li>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for popup -->

            </ul>
        </nav>
        <main class="content">
            <h1>Messages</h1>
            <section class="message-container">
                <div class="message-header" id="messagesHeader">
                    <!-- Reciever name will be displayed here -->
                </div>
                <div class="message-section" id="messagesContainer">
                    <!-- Messages will be displayed here -->
                </div>
                <div class="chat-container">
                    <input type="text" id="chatInput" class="chat-input" placeholder="Type a message...">
                    <button class="send-btn" id="sendChatBtn">Send</button>
                </div>
            </section>

        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const messagesHeader = document.getElementById("messagesHeader");
            const messagesContainer = document.getElementById("messagesContainer");
            const chatInput = document.getElementById("chatInput");
            const sendChatBtn = document.getElementById("sendChatBtn");
            let conversationId = new URLSearchParams(window.location.search).get("conversation_id");

            async function fetchMessages() {
                if (!conversationId) return;

                try {
                    const response = await fetch(`fetch_messages.php?conversation_id=${conversationId}`);
                    const messages = await response.json();

                    if (!messages.length) {
                        messagesContainer.innerHTML = "<p>No messages found.</p>";
                        return;
                    }

                    // Set receiver's name in the message header
                    messagesHeader.innerHTML = `<h2 class="message-title">Chat with ${messages[0].receiver_name}</h2>`;
                  
                    messagesContainer.innerHTML = messages.map(msg => `
                <div class="message ${msg.message_type}">
                    ${msg.message}
                    <span>${msg.timestamp}</span>
                </div>
            `).join("");

                    messagesContainer.scrollTop = messagesContainer.scrollHeight; // Auto-scroll
                } catch (error) {
                    console.error("Error fetching messages:", error);
                }
            }

            async function sendMessage() {
                
                const message = chatInput.value.trim();
                if (!message || !conversationId) return;
                console.log(message)
                try {
                    const response = await fetch(`send_message.php?sendMessage=true`, {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `conversation_id=${encodeURIComponent(conversationId)}&message=${encodeURIComponent(message)}`
                    });

                    if (!response.ok) throw new Error("Failed to send message");

                    chatInput.value = ""; // Clear input field
                    fetchMessages(); // Refresh messages
                } catch (error) {
                    console.error("Error sending message:", error);
                }
            }

            setInterval(fetchMessages, 5000);
            fetchMessages();

             // Send message event
             sendChatBtn.addEventListener("click", sendMessage);
            chatInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") sendMessage();
            });
        });

    </script>
</body>

</html>