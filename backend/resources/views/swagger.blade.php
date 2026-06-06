<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PawTrainer API Documentation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist/swagger-ui.css" />
    <style>
        body { margin: 0; padding: 0; }
        #swagger-ui { width: 100%; height: 100vh; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist/swagger-ui-bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist/swagger-ui-standalone-preset.js"></script>
<script>
window.onload = function() {
    SwaggerUIBundle({
        url: '/swagger.json',
        dom_id: '#swagger-ui',
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        layout: 'StandaloneLayout',
        deepLinking: true,
        docExpansion: 'none',
        defaultModelsExpandDepth: -1
    });
};
</script>
</body>
</html>
