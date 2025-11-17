# Script para crear deploy.zip con exclusiones
$excludePatterns = @(
    '*.git*',
    'node_modules',
    'tests',
    '.env',
    '.idea',
    '*.log',
    'storage/logs/*',
    'deploy.zip',
    'create-deploy.ps1'
)

# Obtener todos los archivos y carpetas
$allItems = Get-ChildItem -Path . -Recurse

# Filtrar elementos a excluir
$itemsToInclude = $allItems | Where-Object {
    $shouldInclude = $true
    foreach ($pattern in $excludePatterns) {
        if ($_.FullName -like "*$pattern*") {
            $shouldInclude = $false
            break
        }
    }
    return $shouldInclude
}

# Crear archivo ZIP
Compress-Archive -Path $itemsToInclude.FullName -DestinationPath "deploy.zip" -CompressionLevel Optimal

Write-Host "✅ deploy.zip creado exitosamente!" -ForegroundColor Green
Write-Host "📦 Tamaño del archivo: $((Get-Item 'deploy.zip').Length / 1MB) MB" -ForegroundColor Yellow