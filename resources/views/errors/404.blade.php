<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Azure Hotel</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0; 
            padding: 0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            color: #333;
        }
        .error-container { 
            background: white; 
            padding: 3rem; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            text-align: center; 
            max-width: 500px; 
            width: 90%;
        }
        .error-code { 
            font-size: 4rem; 
            font-weight: bold; 
            color: #667eea; 
            margin-bottom: 1rem;
        }
        .error-message { 
            font-size: 1.5rem; 
            margin-bottom: 2rem; 
            color: #555;
        }
        .btn { 
            display: inline-block; 
            padding: 12px 30px; 
            background: #667eea; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            transition: background 0.3s; 
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover { 
            background: #764ba2; 
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Page Not Found</div>
        <p>The page you are looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
    </div>
</body>
</html>