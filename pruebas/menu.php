<?php
if (!file_exists("ejemplo.pl")) {
        die("No se puede localizar el archivo ejemplo.pl, el directorio actual es: " . __DIR__);
    }

    // Ejecutar la consulta
    $output = shell_exec('swipl -s ejemplo.pl -g "padre(X,teresa)." -t halt.');

    echo $output;
