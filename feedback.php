<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Background */
        body {
            background-color: #333333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
        }

        /* Feedback container */
        .feedback-container {
            width: 90%;
            max-width: 400px;
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-height: 700px;
            justify-content: space-between; /* Ensure space is distributed */
        }

        /* Header and back arrow */
        .feedback-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-arrow {
            font-size: 24px;
            cursor: pointer;
            font-weight: bold;
        }

        .feedback-header h2 {
            font-size: 20px;
        }

        /* Rating container */
        .rating-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align rating to left */
            gap: 10px;
            margin-top: 20px; /* Add margin to move it down */
        }

        .rating-text {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            align-self: flex-start; /* Align text to left */
        }

        .rating {
            display: flex;
            gap: 2px;
            align-self: flex-start; /* Align stars to left */
        }

        .star {
            font-size: 40px;
            color: #FFD700;
            cursor: pointer;
        }

        /* Feedback instruction text */
        .feedback-instruction {
            font-size: 20px;
            color: #000; /* Changed to black */
            font-weight: bold; /* Made text bold */
            align-self: flex-start; /* Align to left */
            text-align: left; /* Align text to left */
            width: 100%; /* Ensure it takes full width */
        }

        /* Text area */
        .feedback-text {
            width: 100%;
            height: 120px;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid #ddd;
            outline: none;
            resize: none;
            font-size: 14px;
            margin-top: 10px; /* Adjust margin */
        }

        /* Submit button */
        .submit-button {
            width: 50%;
            padding: 12px;
            background-color: #9370DB; /* Changed to purple */
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            align-self: flex-end; /* Align button to the right */
            margin-top: 10px; /* Added margin to separate from textarea */
        }

        /* Footer navigation bar */
        .footer-nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #ffffff;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 -1px 10px rgba(0, 0, 0, 0.1);
            margin-top: auto; /* Push footer to the bottom */
        }

        .footer-nav a {
            text-decoration: none;
            color: #333;
            font-size: 24px;
        }

        .footer-nav .active {
            color: #9370DB; /* Changed to purple */
        }

        .footer-nav a i {
            font-size: 24px;
        }

        /* Specific home icon color */
        .footer-nav .home-icon {
            color: black; /* Set home icon color to black */
        }

        /* Media query for smaller screens */
        @media (max-width: 600px) {
            .feedback-container {
                padding: 15px;
            }

            .feedback-header h2 {
                font-size: 18px;
            }

            .rating-text,
            .feedback-instruction {
                font-size: 14px;
            }

            .star {
                font-size: 32px;
            }

            .feedback-text {
                height: 120px;
                font-size: 13px;
            }

            .submit-button {
                font-size: 14px;
                padding: 10px;
            }

            .footer-nav a {
                font-size: 20px;
            }

            .footer-nav a i {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        <div class="feedback-header">
            <span class="back-arrow">&larr;</span>
            <h2>Feedback</h2>
        </div>
        
        <form action="submit_feedback.php" method="post">
            <div class="rating-container">
                <div class="rating-text">Rating</div>
                <div class="rating">
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9734;</span>
                    <span class="star">&#9734;</span>
                </div>
                <div class="feedback-instruction">Berikan Feedback</div>
            </div>

            <textarea class="feedback-text" name="feedback" placeholder="Jika Anda memiliki masukan kritik dan saran, silakan ketik di sini..."></textarea>
            <button type="submit" class="submit-button">Submit</button>
        </form>

        <!-- Footer Navigation Bar -->
        <div class="footer-nav">
            <a href="#" class="home-icon"><i class="fas fa-home"></i></a>
            <a href="#"><i class="fas fa-qrcode"></i></a>
            <a href="#"><i class="fas fa-calendar-alt"></i></a>
            <a href="#" class="active"><i class="fas fa-user"></i></a>
        </div>
    </div>
</body>
</html>