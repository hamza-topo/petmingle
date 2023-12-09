<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('title')
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            color: #e44d26;
        }

        p {
            line-height: 1.6;
            color: #555;
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            margin-top: 30px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .emoji {
            font-size: 24px;
            margin-right: 10px;
        }

        @media screen and (max-width: 600px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
