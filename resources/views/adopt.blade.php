<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Start New Game</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #2c3e50; padding: 15px;
        }
        .setup-card { 
            width: 100%; max-width: 400px; text-align: center;
            background: rgba(255, 255, 255, 0.92); 
            padding: 40px 30px; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            backdrop-filter: blur(10px);
        }
        h1 { font-size: 2.5rem; margin-bottom: 10px; }
        p { color: #7f8c8d; margin-bottom: 30px; font-size: 16px; }
        .name-input { 
            width: 100%; padding: 15px; font-size: 18px; 
            border: 2px solid #4ecdc4; border-radius: 10px; 
            text-align: center; font-weight: bold; margin-bottom: 20px;
            outline: none;
        }
        .name-input:focus { border-color: #2ed573; }
        .btn-start { 
            background: #4ecdc4; color: white; border: none; 
            padding: 15px; font-size: 18px; border-radius: 10px; 
            font-weight: bold; cursor: pointer; width: 100%; 
            transition: transform 0.1s, background 0.2s;
        }
        .btn-start:hover { background: #2ed573; }
        .btn-start:active { transform: scale(0.96); }
    </style>
</head>
<body>

    <div class="setup-card">
        <h1>🐱 Welcome!</h1>
        <p>Adopt and name your new companion to start the game.</p>
        
        <form action="/adopt" method="POST">
            @csrf
            <input type="text" name="name" class="name-input" placeholder="Enter a pet name..." required maxlength="20" autofocus>
            <button type="submit" class="btn-start">Start Game</button>
        </form>
    </div>

</body>
</html>