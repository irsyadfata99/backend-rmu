<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMU Backend API</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .api-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
        .endpoint {
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            background: rgba(0, 0, 0, 0.2);
            padding: 0.5rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #10b981;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 RMU Backend API</h1>
        <p>Your Laravel backend is running successfully!</p>
        
        <div class="api-info">
            <h3>Available API Endpoints:</h3>
            <div class="endpoint">POST /register - User Registration</div>
            <div class="endpoint">POST /login - User Login</div>
            <div class="endpoint">POST /logout - User Logout</div>
        </div>
        
        <div class="status">✅ Server Status: Online</div>
    </div>
</body>
</html>