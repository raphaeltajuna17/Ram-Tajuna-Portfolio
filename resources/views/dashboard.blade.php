<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Raphael's Desktop Companion</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            min-height: -webkit-fill-available;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #f0f3f4; 
            color: #333; 
            padding: 15px;
            transition: background 0.6s ease, background-color 0.6s ease;
            background-size: cover;
            background-position: center;
        }

        /* Dynamic Backgrounds */
        body.bg-default { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        body.bg-pet { background: linear-gradient(135deg, #fff1eb 0%, #ace0f9 100%); }
        body.bg-feed { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); }
        body.bg-clean { background: linear-gradient(135deg, #a8edf0 0%, #fecfef 100%); }
        body.bg-play { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        body.bg-sleep { background: linear-gradient(135deg, #2c3e50 0%, #000000 100%); color: #fff !important; }
        body.bg-medicine { background: linear-gradient(135deg, #ffc3a0 0%, #ffafbd 100%); }

        .dashboard { 
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.92); 
            padding: 25px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            backdrop-filter: blur(10px);
            transition: background 0.6s ease;
            position: relative;
        }
        
        body.bg-sleep .dashboard { background: rgba(44, 62, 80, 0.85); color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        body.bg-sleep h1, body.bg-sleep .info-bar { color: #fff; }
        body.bg-sleep .stat-box { background: #34495e; border-color: #2c3e50; color: #fff; }

        /* Highly Visible Settings Button */
        .btn-settings-toggle { 
            position: absolute; top: 20px; right: 20px; background: #4ecdc4; color: white;
            border: none; width: 45px; height: 45px; border-radius: 50%; font-size: 22px; 
            cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.15); 
            transition: transform 0.2s ease, background 0.2s ease; z-index: 10; 
            display: flex; align-items: center; justify-content: center;
        }
        .btn-settings-toggle:hover { transform: scale(1.1); background: #2ed573; }

        .settings-overlay {
            display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.95); border-radius: 20px; z-index: 100;
            flex-direction: column; align-items: center; justify-content: center; gap: 20px;
            backdrop-filter: blur(5px);
        }
        body.bg-sleep .settings-overlay { background: rgba(44, 62, 80, 0.95); }
        
        .settings-overlay h2 { font-size: 28px; margin-bottom: 10px; }
        .btn-new-game { background: #ff4757; color: white; border: none; padding: 15px 30px; font-size: 18px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 80%; transition: transform 0.1s; }
        .btn-continue { background: #2ed573; color: white; border: none; padding: 15px 30px; font-size: 18px; border-radius: 10px; font-weight: bold; cursor: pointer; width: 80%; transition: transform 0.1s; }
        .btn-new-game:active, .btn-continue:active { transform: scale(0.95); }

        .clock-container { margin-bottom: 20px; margin-top: 40px; text-align: center; }
        .digital-clock { font-size: 3rem; font-weight: 900; color: #2c3e50; background: #fff; border: 4px solid #4ecdc4; border-radius: 15px; padding: 10px 15px; display: inline-block; box-shadow: 0px 6px 0px #4ecdc4; font-family: 'Courier New', Courier, monospace; letter-spacing: 2px; width: 100%; }
        body.bg-sleep .digital-clock { color: #fff; background: #2c3e50; border-color: #9b59b6; box-shadow: 0px 6px 0px #9b59b6; }

        .name-wrapper { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 8px; }
        .name-wrapper h1 { margin: 0; font-size: 24px; color: #2c3e50; }
        .btn-rename-trigger { background: rgba(0,0,0,0.06); border: none; padding: 5px 12px; border-radius: 20px; font-size: 13px; cursor: pointer; font-weight: bold; color: inherit; }
        
        .edit-mode { display: none; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px; }
        .name-input { padding: 6px 10px; font-size: 16px; border: 2px solid #4ecdc4; border-radius: 8px; text-align: center; font-weight: bold; width: 150px; }
        .btn-save-name { background: #4ecdc4; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-cancel-name { background: #ff4757; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* Game Screen & Canvas Layering */
        .game-screen { background-color: #e3f2fd; height: 180px; border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #bce0fd; position: relative; }
        #cat-sprite { height: 120px; object-fit: contain; } 
        
        /* Hidden Game Assets */
        #obstacle-sprite { display: none; }
        #player-run-sprite { display: none; }
        
        #gameCanvas { display: none; width: 100%; height: 100%; background: #e3f2fd; cursor: pointer; }
        #mini-game-score { display: none; position: absolute; top: 10px; left: 15px; font-weight: bold; font-size: 16px; color: #2c3e50; font-family: monospace; }
        #sick-warning { display: none; position: absolute; top: 10px; right: 10px; background: #ff4757; color: white; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .is-sick-bg { background-color: #ffcccc !important; border-color: #ff4757 !important; }

        /* --- NEW: Game Over Overlay UI --- */
        .game-over-overlay {
            display: none; 
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(44, 62, 80, 0.85); /* Dark translucent background */
            flex-direction: column; align-items: center; justify-content: center;
            z-index: 20; color: white; border-radius: 12px; backdrop-filter: blur(3px);
        }
        .game-over-overlay h2 { color: #ff6b6b; margin-bottom: 5px; font-size: 24px; }
        .game-over-overlay img { height: 70px; object-fit: contain; margin-bottom: 5px; }
        .game-over-overlay p { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .btn-close-game { 
            background: #4ecdc4; color: white; border: none; padding: 8px 20px; 
            border-radius: 8px; font-weight: bold; cursor: pointer; transition: transform 0.1s;
        }
        .btn-close-game:active { transform: scale(0.95); }

        .info-bar { color: #666; font-style: italic; margin-bottom: 15px; font-size: 14px; text-align: center; }

        .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 15px; text-align: center; }
        .stat-box { padding: 8px 4px; border: 2px solid #e9ecef; border-radius: 10px; background: #ffffff; font-size: 12px; font-weight: bold; }
        .stat-value { font-size: 18px; font-weight: bold; color: #007bff; margin-top: 3px; }
        body.bg-sleep .stat-value { color: #f1c40f; }

        /* Perfect 6-Button Grid */
        .buttons-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .interaction-btn { padding: 12px; font-size: 14px; cursor: pointer; border: none; border-radius: 10px; font-weight: bold; transition: transform 0.1s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .interaction-btn:active { transform: scale(0.96); }
        .interaction-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        
        .btn-pet { background: #ff9ff3; color: #333; }
        .btn-feed { background: #ff6b6b; color: white; }
        .btn-clean { background: #4ecdc4; color: white; }
        .btn-play { background: #ffe66d; color: #333; }
        .btn-sleep { background: #a29bfe; color: white; } 
        .btn-minigame { background: #ff9f43; color: white; }
        .btn-medicine { background: #ff4757; color: white; grid-column: span 2; }

        @media (max-width: 480px) {
            body { padding: 0; }
            .dashboard { height: 100vh; max-width: 100%; border-radius: 0; display: flex; flex-direction: column; justify-content: center; }
            .digital-clock { font-size: 2.5rem; }
            .settings-overlay { border-radius: 0; }
        }
    </style>
</head>
<body class="bg-default">

    <div class="dashboard">
        <button class="btn-settings-toggle" onclick="toggleSettings(true)">⚙️</button>
        
        <div id="settings-menu" class="settings-overlay">
            <h2>Game Menu</h2>
            <p style="margin-bottom: 20px; font-style: italic; color: #888;">Progress is saved automatically.</p>
            <button class="btn-continue" onclick="toggleSettings(false)">▶ Continue Game</button>
            <button class="btn-new-game" onclick="startNewGame()">🔄 Start New Game</button>
        </div>

        <div class="clock-container">
            <div class="digital-clock" id="real-time-clock">--:--:--</div>
        </div>

        <div class="name-wrapper" id="display-mode">
            <h1 id="cat-name-display">🐱 {{ $cat->name }}</h1>
            <button class="btn-rename-trigger" onclick="toggleEditMode(true)">✏️ Rename</button>
        </div>
        
        <div class="edit-mode" id="edit-mode">
            <input type="text" id="cat-name-input" class="name-input" value="{{ $cat->name }}" maxlength="20">
            <button class="btn-save-name" onclick="saveNewName()">Save</button>
            <button class="btn-cancel-name" onclick="toggleEditMode(false)">Cancel</button>
        </div>

        <div class="info-bar">
            <span>Your companion is <strong id="stat-age">{{ intval($cat->created_at->diffInDays(now())) }}</strong> days old.</span>
        </div>
        
        <div id="screen-container" class="game-screen {{ $cat->is_sick ? 'is-sick-bg' : '' }}">
            <div id="sick-warning" style="display: {{ $cat->is_sick ? 'block' : 'none' }};">🤒 SICK!</div>
            
            <div id="mini-game-score">Score: 0</div>
            
            <img id="cat-sprite" src="{{ $cat->is_sick ? '/images/sick.gif' : '/images/idle.gif' }}" alt="Cat Sprite">
            
            <img id="obstacle-sprite" src="/images/walk.gif" alt="Obstacle">
            <img id="player-run-sprite" src="/images/run.gif" alt="Running Player">
            
            <canvas id="gameCanvas" width="450" height="180"></canvas>

            <div id="game-over-screen" class="game-over-overlay">
                <h2>Game Over!</h2>
                <img src="/images/gameover.gif" alt="Dizzy Cat">
                <p>Final Score: <span id="final-score-display">0</span></p>
                <button class="btn-close-game" onclick="closeGameOver()">OK</button>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-box"><div>Hunger</div><div id="stat-hunger" class="stat-value">{{ $cat->hunger }}</div></div>
            <div class="stat-box"><div>Energy</div><div id="stat-energy" class="stat-value">{{ $cat->energy }}</div></div>
            <div class="stat-box"><div>Hygiene</div><div id="stat-hygiene" class="stat-value">{{ $cat->hygiene }}</div></div>
            <div class="stat-box"><div>Happiness</div><div id="stat-happiness" class="stat-value">{{ $cat->happiness }}</div></div>
        </div>

        <hr style="border: 0; border-top: 2px solid rgba(0,0,0,0.1); margin: 15px 0;">

        <div class="buttons-grid">
            <button id="btn-pet" class="interaction-btn btn-pet" onclick="triggerMeow()" {{ $cat->is_sick ? 'disabled' : '' }}>🐾 Pet</button>
            <button id="btn-feed" class="interaction-btn btn-feed" onclick="performAction('feed', 'eat.gif')" {{ $cat->is_sick ? 'disabled' : '' }}>🐟 Feed</button>
            <button class="interaction-btn btn-clean" onclick="performAction('clean', 'clean.gif')">🧹 Clean</button>
            <button id="btn-play" class="interaction-btn btn-play" onclick="performAction('play', 'play.gif')" {{ $cat->is_sick ? 'disabled' : '' }}>🧶 Play</button>
            <button class="interaction-btn btn-sleep" onclick="performAction('sleep', 'sleep.gif')">💤 Sleep</button>
            <button id="btn-minigame" class="interaction-btn btn-minigame" onclick="startMiniGame()" {{ $cat->is_sick ? 'disabled' : '' }}>🎮 Mini Game</button>
            
            <button id="btn-medicine" class="interaction-btn btn-medicine" style="display: {{ $cat->is_sick ? 'inline-block' : 'none' }};" onclick="performAction('medicine', 'idle.gif')">💊 Medicine</button>
        </div>
    </div>

    <script>
        let animationTimer;
        let backgroundTimer;
        let isCatSick = {{ $cat->is_sick ? 'true' : 'false' }};
        let lastChimeHour = -1;

        // ==========================================
        //  ENDLESS RUNNER MINI-GAME ENGINE
        // ==========================================
        let canvas, ctx, gameLoopId;
        let isPlaying = false;
        let gameScore = 0;
        let obstacles = [];
        let frames = 0;

        let dino = { x: 50, y: 130, width: 40, height: 40, velocityY: 0, gravity: 0.6, jumpPower: -11, isGrounded: true };

        function startMiniGame() {
            if (isCatSick) return alert("Your cat is sick and doesn't want to play right now!");
            
            // Hide main sprites and overlays
            document.getElementById('cat-sprite').style.display = 'none';
            document.getElementById('game-over-screen').style.display = 'none';
            
            canvas = document.getElementById('gameCanvas');
            canvas.style.display = 'block';
            document.getElementById('mini-game-score').style.display = 'block';
            ctx = canvas.getContext('2d');

            isPlaying = true;
            gameScore = 0;
            obstacles = [];
            frames = 0;
            dino.y = 130;
            dino.velocityY = 0;
            document.getElementById('mini-game-score').innerText = "Score: " + gameScore;
            
            changeBackground('bg-play');
            gameLoop();
        }

        function jump() {
            if (dino.isGrounded && isPlaying) {
                dino.velocityY = dino.jumpPower;
                dino.isGrounded = false;
                playSound('play'); 
            }
        }

        document.addEventListener('keydown', (e) => { if (e.code === 'Space') jump(); });
        document.addEventListener('mousedown', jump);
        document.addEventListener('touchstart', jump);

        function gameLoop() {
            if (!isPlaying) return;
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            dino.velocityY += dino.gravity;
            dino.y += dino.velocityY;
            
            if (dino.y >= 130) {
                dino.y = 130;
                dino.isGrounded = true;
                dino.velocityY = 0;
            }
            
            // Draw custom running player sprite
            const playerImg = document.getElementById('player-run-sprite');
            ctx.drawImage(playerImg, dino.x, dino.y, dino.width, dino.height);
            
            frames++;
            if (frames % 100 === 0) { 
                obstacles.push({ x: canvas.width, y: 135, width: 35, height: 35, speed: 4, passed: false });
            }
            
            for (let i = 0; i < obstacles.length; i++) {
                let obs = obstacles[i];
                obs.x -= obs.speed; 
                
                const obsImg = document.getElementById('obstacle-sprite');
                ctx.drawImage(obsImg, obs.x, obs.y, obs.width, obs.height);
                
                // Forgiving Hitboxes
                let margin = 8;
                if (dino.x + margin < obs.x + obs.width - margin &&
                    dino.x + dino.width - margin > obs.x + margin &&
                    dino.y + margin < obs.y + obs.height - margin &&
                    dino.y + dino.height - margin > obs.y + margin) {
                    gameOver();
                }

                // Instant Scoring
                if (!obs.passed && obs.x + obs.width < dino.x) {
                    obs.passed = true;
                    gameScore += 10;
                    document.getElementById('mini-game-score').innerText = "Score: " + gameScore;
                }
            }
            
            if (obstacles.length > 0 && obstacles[0].x < -50) {
                obstacles.shift();
            }
            
            ctx.beginPath();
            ctx.moveTo(0, 170);
            ctx.lineTo(canvas.width, 170);
            ctx.strokeStyle = '#34495e';
            ctx.lineWidth = 2;
            ctx.stroke();

            if (isPlaying) {
                gameLoopId = requestAnimationFrame(gameLoop);
            }
        }

        // --- NEW: Custom Game Over Behavior ---
        function gameOver() {
            isPlaying = false;
            cancelAnimationFrame(gameLoopId);
            
            // Update and show the custom Game Over HTML menu instead of an alert!
            document.getElementById('final-score-display').innerText = gameScore;
            document.getElementById('game-over-screen').style.display = 'flex';
        }

        function closeGameOver() {
            // Hide the game over screen
            document.getElementById('game-over-screen').style.display = 'none';
            
            // Revert UI to normal
            canvas.style.display = 'none';
            document.getElementById('mini-game-score').style.display = 'none';
            document.getElementById('cat-sprite').style.display = 'block';
            
            changeBackground('bg-default');
        }
        // ==========================================


        // --- Standard UI Logic ---
        function toggleSettings(show) { document.getElementById('settings-menu').style.display = show ? 'flex' : 'none'; }

        function startNewGame() {
            if(confirm("Are you sure? This will delete your current companion and return you to the adoption screen.")) {
                toggleSettings(false);
                performAction('reset', 'idle.gif');
            }
        }

        function changeBackground(actionClass) {
            document.body.className = '';
            document.body.classList.add(actionClass);
            clearTimeout(backgroundTimer);
            backgroundTimer = setTimeout(() => {
                document.body.className = '';
                document.body.classList.add('bg-default');
            }, 3000);
        }

        function playSound(action) {
            const sounds = {
                'feed': '/audio/eat.mp3',
                'clean': '/audio/clean.mp3',
                'play': '/audio/play.mp3',
                'sleep': '/audio/sleep.mp3',
                'medicine': '/audio/medicine.mp3',
                'meow': '/audio/meow.mp3'
            };
            if (sounds[action]) {
                let sfx = new Audio(sounds[action]);
                sfx.volume = 0.5;
                sfx.play().catch(e => console.log("Audio blocked."));
            }
        }

        function toggleEditMode(showEdit) {
            document.getElementById('display-mode').style.display = showEdit ? 'none' : 'flex';
            document.getElementById('edit-mode').style.display = showEdit ? 'flex' : 'none';
        }

        function saveNewName() {
            const newName = document.getElementById('cat-name-input').value.trim();
            if (!newName) return alert("Name cannot be empty!");

            fetch('/interact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: 'rename', name: newName })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('cat-name-display').innerText = '🐱 ' + data.name;
                toggleEditMode(false);
            })
            .catch(error => console.error('Error:', error));
        }

        function triggerMeow() {
            if (isCatSick) return alert("Your cat is sick!");
            playSound('meow');
            changeBackground('bg-pet'); 

            const sprite = document.getElementById('cat-sprite');
            clearTimeout(animationTimer);
            sprite.src = '/images/meow.gif';
            
            animationTimer = setTimeout(() => {
                if (!isCatSick) sprite.src = '/images/idle.gif';
            }, 3000);
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('real-time-clock').innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            if (now.getMinutes() === 0 && now.getSeconds() === 0 && now.getHours() !== lastChimeHour) {
                lastChimeHour = now.getHours(); 
                if (!isCatSick) triggerMeow();
            }
        }
        
        updateClock();
        setInterval(updateClock, 1000);

        function performAction(actionName, animationFile) {
            if (isCatSick && (actionName === 'feed' || actionName === 'play')) {
                return alert("Your cat is sick!");
            }

            if (actionName !== 'reset') {
                if (actionName !== 'play') playSound(actionName); 
                changeBackground('bg-' + actionName); 
            }

            const sprite = document.getElementById('cat-sprite');
            clearTimeout(animationTimer);
            
            if (actionName === 'medicine' || actionName === 'reset' || !isCatSick) {
                sprite.src = '/images/' + animationFile;
            }

            fetch('/interact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: actionName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                document.getElementById('stat-hunger').innerText = data.hunger;
                document.getElementById('stat-energy').innerText = data.energy;
                document.getElementById('stat-hygiene').innerText = data.hygiene;
                document.getElementById('stat-happiness').innerText = data.happiness;
                document.getElementById('stat-age').innerText = data.age_in_days;
                
                isCatSick = data.is_sick;
                
                document.getElementById('sick-warning').style.display = isCatSick ? 'block' : 'none';
                document.getElementById('btn-feed').disabled = isCatSick;
                document.getElementById('btn-play').disabled = isCatSick;
                document.getElementById('btn-pet').disabled = isCatSick;
                document.getElementById('btn-minigame').disabled = isCatSick;
                document.getElementById('btn-medicine').style.display = isCatSick ? 'inline-block' : 'none';
                
                if (isCatSick) {
                    document.getElementById('screen-container').classList.add('is-sick-bg');
                    sprite.src = '/images/sick.gif'; 
                } else {
                    document.getElementById('screen-container').classList.remove('is-sick-bg');
                }

                if (!isCatSick) {
                    animationTimer = setTimeout(() => {
                        sprite.src = '/images/idle.gif';
                    }, 3000);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>