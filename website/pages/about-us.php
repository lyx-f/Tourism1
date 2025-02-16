
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <title>About Us</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../img/mati-dav.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            line-height: 1.6;
        }

        .about-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 40px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #333;
            text-align: center;
        }

        h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #007a73;
        }

        h1::after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background-color: #007a73;
            margin: 10px auto 0;
        }

        .developers {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .developer {
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 250px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .developer:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .developer img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #007a73;
        }

        .developer h2 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .developer p {
            font-size: 14px;
            margin-bottom: 10px;
            color: #555;
        }

        .role {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role i {
            color: #007a73;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="about-container">
        <h1>Meet the Team</h1>
        <p> Our team of dedicated developers is passionate about enhancing your travel experience. </p>
        <div class="developers">
            <div class="developer">
                <img src="../../assets/img/sabel.jpeg" alt="Jessabyl Enderio">
                <h2>Enderio, Jessabyl B.</h2>
                <p class="role"><i class="fa fa-code"></i> Lead Developer</p>
                <p>Hey! I'm Jessa, the technical fairy ensuring a smooth and efficient experience. I'm working hard to power the platform behind the scenes.</p>
            </div>

            <div class="developer">
                <img src="../../assets/img/duday.jpg" alt="Jaycel Blanco">
                <h2>Blanco, Jaycel Joy B.</h2>
                <p class="role"><i class="fa fa-paint-brush"></i> Frontend Developer</p>
                <p>Hi there! I'm Jaycel, the creative mind behind Tourmatic's user-friendly interface. I'm here to make your travel planning a breeze.</p>
            </div>

            <div class="developer">
                <img src="../../assets/img/enchang.jpeg" alt="Denise Castillo">
                <h2>Castillo, Denise Rachelle P.</h2>
                <p class="role"><i class="fa fa-tasks"></i> Project Manager</p>
                <p>Hello! I'm Denise, your dedicated guide in bringing Tourmatic to life. I'm here to oversee the project and make sure you have a fantastic travel experience.</p>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; 2024 TourMatic. Mara G. All rights reserved.
    </div>
</body>
</html>
