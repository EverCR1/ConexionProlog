<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica la Relación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Verifica la Relación</h2>
    <form method="post" class="form-container">
        <div class="input-group">
            <label for="nombre1">Persona 1:</label>
            <input type="text" id="nombre1" name="nombre1" required>
        </div>
        <div class="input-group">
            <label for="relacion">Relación:</label>
            <select id="relacion" name="relacion">
                <option value="padre">Es Padre de</option>
                <option value="madre">Es Madre de</option>
                <option value="hermanos">Son Hermanos</option>
                <option value="tio">Es Tío de</option>
                <option value="tia">Es Tía de</option>
                <option value="progenitorhijo">Es progenitor de</option>
            </select>
        </div>
        <div class="input-group">
            <label for="nombre2">Persona 2:</label>
            <input type="text" id="nombre2" name="nombre2" required>
        </div>
        <button type="submit" name="submit">Verificar</button>
    </form>
</div>

<?php

// $objetos = [pedro, juan];
// $relaciones = [padre];
if (isset($_POST['submit'])) {
    // Obtener los nombres ingresados por el usuario
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $relacion = $_POST['relacion']; // Obtener la relación seleccionada

    if (!file_exists("ejemplo.pl")) {
        die("No se puede localizar el archivo ejemplo.pl, el directorio actual es: " . __DIR__);
    }

    // Ejecutar consulta Prolog con la relación seleccionada
    $output = shell_exec("swipl -s ejemplo.pl -g \"$relacion($nombre1,$nombre2).\" -t halt.");

    // Verificar el resultado de la consulta
    if (strpos($output, "true") !== false) {
        echo "<div class='resultado positivo'>$nombre1 y $nombre2 tienen la relación '$relacion'</div>";
    } else {
        echo "<div class='resultado negativo'>$nombre1 y $nombre2 no tienen la relación '$relacion'</div>";
    }

}
?>
</body>
</html>

