<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PETMINGLE</title>
</head>

<body>
    <h1>Comming soon</h1>
</body>

</html>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    Echo.channel(`new-match`)
        .listen('.new.match', (e) => {
            console.log(e);
        });
</script>
