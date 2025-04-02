document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("bookingInfoModal");
  const openModalBtn = document.getElementById("bookingInfoBtn");
  const closeBtn = modal.querySelector(".close-btn");
  const bookingDetails = document.getElementById("bookingDetails");

  // Fetch bookings and populate the modal
  async function fetchBookings() {
    try {
      const response = await fetch(
        window.location.href + "?fetchBookings=true"
      );
      if (!response.ok) throw new Error("Failed to fetch bookings");

      const bookings = await response.json();
      if (bookings.error) throw new Error(bookings.error);

      if (bookings.length === 0) {
        bookingDetails.innerHTML = "<p>No bookings found.</p>";
        return;
      }
   
      bookingDetails.innerHTML = bookings
        .map(
          (booking) => `
                <div class="booking-item modal-cards">
                    <p><strong>Destination:</strong> ${booking.name}</p>
                    <p><strong>Date:</strong> ${new Date(
                      booking.date
                    ).toLocaleDateString()}</p>
                    <p><strong>Location:</strong> ${booking.location}</p>
                    <p><strong>Category:</strong> ${booking.category}</p>
                    <p><strong>Status:</strong> ${booking.status}</p>
                    <button class="chat-now-btn" data-business-id="${
                      booking.business_id
                    }" data-booking-name="${booking.name}">Chat Now</button>
                </div>
            `
        )
        .join("");

      if (window.attachChatEventListeners) {
        attachChatEventListeners();
      }
    } catch (error) {
      bookingDetails.innerHTML = `<p>Error loading bookings.</p>`;
      console.error(error);
    }
  }

  // Open booking modal
  if (openModalBtn) {
    openModalBtn.addEventListener("click", () => {
      modal.style.display = "flex";
      fetchBookings();
    });
  }

  // Close booking modal
  closeBtn.addEventListener("click", () => (modal.style.display = "none"));
  window.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
  });

  // Export functions if needed
  window.fetchBookings = fetchBookings;
});
