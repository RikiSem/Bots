<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Полный список полезных Telegram-ботов: чат-боты, игры, утилиты и многое другое. Найдите лучших ботов для Telegram здесь!">
    <meta name="keywords" content="Telegram боты, список ботов, полезные боты Telegram, лучшие боты в Telegram, чат-боты, боты для тг">
    <meta name="robots" content="index, follow">
    <meta name="yandex-verification" content="ce3a233c18efa0b5" />
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>{{ env('APP_DOMEN') }} - Боты</title>
    <style>
        body{
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            background-color: rgb(53, 53, 53);
        }
        p{
            font-size: 30pt;
            color: white;
            text-align: center;
        }
        .bots-list{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }
        a{
            text-decoration: none;
        }
        .bot-card{
            background-color: #16202c;
            border-radius: 1.2rem;
            margin: 10px;
            padding: 10px;
            width: fit-content;
            box-shadow: 0 0 40px 4px black;
            color: white;
            text-align: center;
            font-size: 1.5rem;
        }
        .bot-card p{
            padding: 0 0 20px 0;
            margin: 0 auto 0 auto;
        }
        .bot-card img{
            width: 30rem;
        }
    </style>
</head>
<body>
    <p>Наши боты в Телеграм</p>
    <div class="bots-list">
        @foreach ($data as $botInfo)
            <a target="_blank" href={{$botInfo['url']}}>
                <div class="bot-card">
                    <img src="{{ $botInfo['img'] }}" alt="((">
                    <p>{{ $botInfo['name'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</body>
</html>