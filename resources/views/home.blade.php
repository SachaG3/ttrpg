<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TTRPG</title>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            width: 100vw;
            height: 100dvh;
            background-image: url("img/background.jpg");
            background-size: 110% 110%;
            background-repeat: no-repeat;
            background-position: center;
        }

        .play-button {
            font-size: 1.5rem; /* Plus gros texte */
            padding: 1rem 2rem; /* Plus d'espace autour du texte */
            margin-bottom: 2rem; /* Positionnement un peu plus haut */
        }
    </style>
</head>
<body>

<a class="btn btn-primary mb-8 px-8 py-4 flex items-center justify-center" href="{{route("players.create")}}">
    PLAY
</a>

</body>
</html>
