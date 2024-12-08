<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Remove default margin and padding */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }

        .image-container {
            width: 50%; /* Takes half the page width */
            height: 100vh; /* Full viewport height */
            position: fixed; /* Fixes the container on the left */
            top: 0;
            left: 0;
            display: flex;
            align-items: center; /* Centers the image vertically */
            justify-content: center; /* Centers the image horizontally */
            box-sizing: border-box;
        }

        img {
            max-width: 100%; /* Prevents overflow from the container */
            max-height: 100%; /* Maintains aspect ratio */
            object-fit: cover; /* Ensures the image fills the container proportionally */
            display: block; /* Removes any inline spacing issues */
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="image/ITcollege.jpeg" alt="IT College">
    </div>
</body>
</html>
