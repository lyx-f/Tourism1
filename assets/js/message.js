document.addEventListener("DOMContentLoaded", function () {
  const chatModal = document.getElementById("chatModal");
  const chatClose = document.querySelector(".chat-close");
  const chatBookingName = document.getElementById("chatBookingName");
  const messagesContainer = document.getElementById("messages");
  const chatInput = document.getElementById("chatInput");
  const sendChatBtn = document.getElementById("sendChatBtn");

  let currentBusinessId = null;
  let fetchMessagesInterval = null;
  let currentConversationId = null;

  // Attach event listeners to "Chat Now" buttons
  function attachChatEventListeners() {
    document.querySelectorAll(".chat-now-btn").forEach((button) => {
      button.addEventListener("click", function () {
        currentBusinessId = this.getAttribute("data-business-id");
        chatBookingName.textContent = this.getAttribute("data-booking-name");
        chatModal.style.display = "block";
        fetchMessages(); // Load messages when chat opens
        startFetchingMessages();
      });
    });
  }

  // Fetch messages from the server
  async function fetchMessages() {
    if (!currentBusinessId) return;
    try {
      const url = `messages/fetchMessages.php?business-id=${encodeURIComponent(
        currentBusinessId
      )}&fetchMessages=true`;
      const response = await fetch(url, {
        method: "GET",
        headers: { "Content-Type": "application/json" },
      });

      const text = await response.text(); // Get raw response text
      try {
        const messages = JSON.parse(text);
        currentConversationId = messages["conversation_id"] || null;

        if (
          messages &&
          messages["messages"] &&
          messages["messages"].length > 0
        ) {
          displayMessages(messages["messages"]);
        }
      } catch (jsonError) {
        console.error("JSON Parsing Error:", jsonError, "Raw response:", text);
      }
    } catch (error) {
      console.error("Error fetching messages:", error);
    }
  }

  // Display messages in chat modal
  function displayMessages(messages) {
    messagesContainer.innerHTML = messages
      .map(
        (msg) => `
            <div class="message ${msg.message_type}">${msg.message}</div>
        `
      )
      .join("");
    messagesContainer.scrollTop = messagesContainer.scrollHeight; // Auto-scroll to latest message
  }

  // Send message function
  async function sendMessage() {
    const message = chatInput.value.trim();
    if (!message || !currentBusinessId) return;

    try {
      const response = await fetch(
        "messages/sendMessage.php?sendMessage=true",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `conversation_id=${encodeURIComponent(
            currentConversationId
          )}&business_id=${encodeURIComponent(
            currentBusinessId
          )}&message=${encodeURIComponent(message)}`,
        }
      );

      if (!response.ok) throw new Error("Failed to send message");

      chatInput.value = ""; // Clear input field
      fetchMessages(); // Refresh messages after sending
    } catch (error) {
      console.error("Error sending message:", error);
    }
  }

  // Start interval to fetch messages every 5 seconds
  function startFetchingMessages() {
    if (fetchMessagesInterval) clearInterval(fetchMessagesInterval);
    fetchMessagesInterval = setInterval(fetchMessages, 5000);
  }

  // Stop fetching messages
  function stopFetchingMessages() {
    if (fetchMessagesInterval) {
      clearInterval(fetchMessagesInterval);
      fetchMessagesInterval = null;
    }
  }

  // Close chat modal
  chatClose.addEventListener("click", () => {
    chatModal.style.display = "none";
    stopFetchingMessages();
  });

  window.addEventListener("click", (e) => {
    if (e.target === chatModal) {
      chatModal.style.display = "none";
      stopFetchingMessages();
    }
  });

  // Send message event
  sendChatBtn.addEventListener("click", sendMessage);
  chatInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") sendMessage();
  });

  // Export functions if needed
  window.attachChatEventListeners = attachChatEventListeners;
});
