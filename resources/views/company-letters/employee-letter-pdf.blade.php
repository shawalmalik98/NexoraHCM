<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <img src="{{ public_path('portal_assets/images/logo.png') }}" draggable="false" class="img-fluid w-100" alt="Source Code logo">
    @if(!empty($body_html))
        {!!$body_html!!}
    @endif
</body>
</html>