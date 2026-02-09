<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            background-color: #f3f4f6;
            color: #333;
            font-family: 'Open Sans", sans-serif', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 4em;
            margin: 0 0 20px;
        }

        p {
            font-size: 1.5em;
            margin: 0 0 30px;
        }

        .emoji {
            font-size: 5em;
        }

        .home-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .home-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<?php
// Check if the HTTP_REFERER header is set
if (isset($_SERVER['HTTP_REFERER'])) {
    $previousUrl = $_SERVER['HTTP_REFERER'];
} else {
    // Default to a fallback URL if the referer is not set
    $previousUrl = '/';
}
?>

<body>
    <div class="container">
        <div class="emoji">🚫😜</div>
        <h1>Oops!</h1>
        <p>You don't have permission to see that page.</p>
        <a href="<?php echo $previousUrl; ?>" class="btn btn-secondary mr-2 my-1">Go Back</a>
        <a href="/" class="btn btn-primary my-1">Go Home</a>
    </div>
</body>

</html>