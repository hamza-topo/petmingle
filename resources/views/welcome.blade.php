<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Dating Coming Soon</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(to bottom right, #FAF3F0, #DBC4F0);
            font-family: 'Arial', sans-serif;
            animation: gradientAnimation 10s infinite alternate;
        }

        .container {
            text-align: center;
            background: linear-gradient(to top left, #FAF3F0, #effaff);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: containerAnimation 4s ease-out infinite;
        }

        h1 {
            color: #D4E2D4;
            animation: textAnimation 3s ease-in-out infinite;
        }

        p {
            color: #FFCACC;
            animation: textAnimation 3s ease-in-out infinite;
        }

        .countdown {
            color: #DBC4F0;
            font-size: 1.5em;
            animation: textAnimation 3s ease-in-out infinite;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 0%;
            }
            100% {
                background-position: 100% 100%;
            }
        }

        @keyframes containerAnimation {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes textAnimation {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
    comming soon
    </div>
    <script>
        // You can add your countdown script here
        // For simplicity, a placeholder "30 days" is used
    </script>
</body>
</html>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    Echo.channel(`new-match`)
        .listen('.new.match', (e) => {
            console.log(e);
        });
</script>
