<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
</head>

<body>
    <div class="simple-spinner">
        <span></span>
    </div>
    <nav>
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
    </nav>
    <div class="container">
        <div class="app">
            <img src="{{ asset('img/white.png') }}" alt="" width="150" height="150">
            <br>
            <button class="btn">Download</button>
            <div class="download">
                <img src="https://static.cdninstagram.com/rsrc.php/v3/yr/r/093c-DX36-y.png" width="100"
                    height="40">
                <img src="https://static.cdninstagram.com/rsrc.php/v3/yk/r/NtqqucWkedn.png" width="100"
                    height="40">
            </div>
        </div>
        <div class="hero-section">
            <div class="slides">
                <input type="radio" name="slide" id="c1">
                <label for="c1" class="card">
                    <div class=row>
                        <div class="icon">
                            < </div>
                                <div class="description">

                                </div>
                        </div>
                </label>
                <input type="radio" name="slide" id="c2">
                <label for="c2" class="card">
                    <div class=row>
                        <div class="icon"></div>
                        <div class="description">
                        </div>
                    </div>
                </label>
                <input type="radio" name="slide" id="c3">
                <label for="c3" class="card">
                    <div class="card-title-right">
                        <h1>Lorem Ipsum</h1>
                    </div>
                    <div class=row>
                        <div class="icon"></div>
                        <div class="description">
                        </div>
                    </div>
                </label>
                <input type="radio" name="slide" id="c4" checked>
                <label for="c4" class="card">
                    <div class="card-title-left">
                        <h1>Lorem Ipsum</h1>
                    </div>
                    <div class=row>
                        <div class="icon">></div>
                        <div class="description">
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </div>
</body>

</html>
