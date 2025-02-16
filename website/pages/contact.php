<?php include("../../includes/homepage_navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css"> 
    <link rel="stylesheet" href="../../assets/css/contact.css">
</head>
<body>
<div class="contact-container">
    <h1>Contact Us</h1>
    <p>Have a question, suggestion, or need assistance? Feel free to reach out to us through our chat support. We're always here to help you make the most of your Mati adventure.</p>

    <div class="contact-content">
        <div class="contact-info">
            <div class="info-item">
                <span class="icon">📍</span>
                <p>Guang-guang, Dahican, Mati City, Davao Oriental, 8200</p>
            </div>
            <div class="info-item">
                <span class="icon">📧</span>
                <p>tourmatic@gmail.com</p>
            </div>
            <div class="info-item">
                <span class="icon">📞</span>
                <p>+1 8588 95488 55</p>
            </div>
        </div>

        <div class="contact-form-container">
            <form class="contact-form" action="submit_message.php" method="POST">
                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message:</label>
                    <textarea id="message" name="message" rows="4" placeholder="Write your message here..." required></textarea>
                </div>
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>
</div>

</body>
<?php include('../../includes/footer.php'); ?>
</html>
