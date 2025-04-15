<?php
include __DIR__ . '/../config/database.php';
define('BASE_URL', '/Tourism1');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["search-data"]) && !empty(trim($_POST["search-data"]))) {
    $searchData = trim($_POST["search-data"]);

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM businesses 
            WHERE location LIKE ? 
            OR name LIKE ? 
            OR category LIKE ?";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $searchData . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => htmlspecialchars(string: $row["id"]),
            "name" => htmlspecialchars($row["name"]),
            "location" => htmlspecialchars($row["location"]),
            "category" => htmlspecialchars($row["category"])
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/homepage_navbar.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: rgba(0, 109, 109);
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            /* text-align: center; */
            position: relative;
        }

        #modal-body {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .modal-cards {
            background-color: rgb(2, 90, 90);
            padding: 10px;
            /* border-radius: 5px; */
            border-bottom: 1px solid gray;
            text-align: start;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <header class="container">
        <a href="homepage.php" class="logo">
            <img src="<?php echo BASE_URL; ?>/assets/img/ok.png" alt="Logo" />
        </a>
        <nav>
            <a href="<?php echo BASE_URL; ?>/website/pages/homepage.php">Home</a>
            <a href="<?php echo BASE_URL; ?>/website/pages/destinations.php">Mati Experience</a>
            <a href="<?php echo BASE_URL; ?>/website/pages/contact.php">Contact Us</a>
        </nav>

        <div class="search-profile">
            <div class="weather-container">
                <span class="weather-icon">&#127782;</span>
                <span id="weather">Loading...</span>
            </div>
            <!--  buhatag query na search-->
            <form id="search-form" method="POST">
                <div class="search-container">
                    <input type="text" name="search-data" placeholder="Search..." class="search-bar">
                    <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <a href="user-profile.php" class="profile-icon">
                <i class="fas fa-user-circle fa-2x"></i>
            </a>
        </div>
    </header>

    <!-- Modal -->
    <div id="searchModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Search Results</h2>
            <div id="modal-body"></div>
        </div>
    </div>

    <script src="../../assets/js/weather.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchForm = document.getElementById("search-form");
            const searchModal = document.getElementById("searchModal");
            const modalBody = document.getElementById("modal-body");
            const closeModal = document.querySelector(".close");

            searchForm.addEventListener("submit", function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(searchForm);

                fetch("", {  // This sends the request to the same file
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        modalBody.innerHTML = ""; // Clear previous results
                        if (data.length > 0) {
                            data.forEach(item => {
                                const resultDiv = document.createElement("div");
                                resultDiv.innerHTML = `<div class="modal-cards"><strong>${item.name}</strong><br>
                                               Location: ${item.location}<br>
                                               Category: ${item.category}</div>`;
                                modalBody.appendChild(resultDiv);

                                resultDiv.addEventListener("click", () => {
                                    window.location.href = `des_info.php?id=${item.id}`;
                                });
                            });
                        } else {
                            modalBody.innerHTML = "<p>No results found.</p>";
                        }
                        searchModal.style.display = "flex"; // Show modal
                    })
                    .catch(error => console.error("Error:", error));
            });

            closeModal.addEventListener("click", function () {
                searchModal.style.display = "none"; // Hide modal
            });

            window.addEventListener("click", function (event) {
                if (event.target === searchModal) {
                    searchModal.style.display = "none";
                }
            });
        });
    </script>
</body>

</html>