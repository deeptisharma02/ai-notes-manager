<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Notes Manager — API Docs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        body { margin: 0; background: #fafafa; }
        .topbar-wrap { background: #1a1d21; color: #fff; padding: .9rem 1.5rem; font: 600 1rem 'Inter', system-ui, sans-serif; display: flex; align-items: center; gap: .6rem; }
        .topbar-wrap .pill { margin-left: auto; font-size: .78rem; font-weight: 500; background: #2dd4bf22; color: #14b8a6; border: 1px solid #14b8a655; padding: .25rem .65rem; border-radius: 999px; }
    </style>
</head>
<body>
    <div class="topbar-wrap">
        AI Notes Manager — API Documentation
        <span class="pill">OpenAPI 3.0</span>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: '/openapi.yaml',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis],
                layout: 'BaseLayout',
            });
        };
    </script>
</body>
</html>
