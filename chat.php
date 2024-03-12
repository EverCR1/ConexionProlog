<?php
// Definir los sinónimos de las relaciones
$sinonimos_relaciones = array(
    "papá" => "padre", "papi" => "padre", "papito" => "padre", "pa" => "padre", "papa" => "padre", // Sinónimos de Padre
    "mamá" => "madre", "mami" => "madre", "mamita" => "madre", "ma" => "madre", "mama" => "madre", // Sinónimos de Madre
    "hermanito" => "hermano", "brother" => "hermano", "bro" => "hermano", // Sinónimos de Hermano
    "hermanita" => "hermana", "sister" => "hermana", // Sinónimos de Hermana
    "tío" => "tio", // Sinónimos de Tío
    "tía" => "tia", // Sinónimos de Tía
    "progenitor" => "progenitorhijo", // Sinónimos de Progenitor
);

// Definir las palabras clave de las relaciones
$relaciones = ['padre', 'madre', 'hermano', 'hermana', 'hermanos', 'tio', 'tia', 'progenitorhijo'];


// Función para procesar el mensaje del usuario
function procesarMensaje($mensaje) {
    global $sinonimos_relaciones, $relaciones;

    $objetos = [];
    $relacion = '';
    $contador_quien = 0;

    $palabras = preg_split("/[\s,¿?]+/", strtolower($mensaje));
    foreach ($palabras as $index => $palabra) {
        if ($palabra && ($palabra !== 'es' && $palabra !== 'de')) { 
            if ($palabra === 'quién') {
                $contador_quien++;
                // Verificar si "quién" aparece antes de "es" o después de "de"
                if ($index > 0 && $palabras[$index - 1] !== 'es') {
                    // Si "quién" aparece después de "de", tomarla como W
                    $objetos[] = 'W';
                    // Verificar si la palabra anterior es una relación conocida
                    if (isset($palabras[$index - 1]) && in_array($palabras[$index - 1], $relaciones)) {
                        $relacion = $palabras[$index - 1];
                    }
                    continue; // Saltar al siguiente ciclo
                } else {
                    // Si "quién" aparece antes de "es", tomarla como X
                    $objetos[] = 'X';
                    // Verificar si la palabra siguiente es una relación conocida
                    if (isset($palabras[$index + 1]) && in_array($palabras[$index + 1], $relaciones)) {
                        $relacion = $palabras[$index + 1];
                    }
                }
            } elseif (isset($sinonimos_relaciones[$palabra])) {
                $relacion = $sinonimos_relaciones[$palabra];
            } elseif (in_array($palabra, $relaciones)) {
                $relacion = $palabra;
            } else {
                $objetos[] = $palabra;
            }
        }
    }

    return ['relacion' => $relacion, 'objetos' => $objetos, 'contador_quien' => $contador_quien];
}


// Función para ejecutar la consulta a Prolog
function ejecutarConsultaProlog($consulta) {
    return shell_exec("swipl -s ejemplo.pl -g \"$consulta\" -t halt.");
}

// Verificar si se ha recibido un mensaje del chat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_data = json_decode(file_get_contents("php://input"), true);
    if (isset($input_data['message'])) {
        $pregunta = $input_data['message'];

        $datos_procesados = procesarMensaje($pregunta);
        $relacion = $datos_procesados['relacion'];
        $objetos = $datos_procesados['objetos'];
        $contador = $datos_procesados['contador_quien'];

        if ($contador == 2) {
            // Construir la consulta Prolog para 'quién es padre de quién'
            $consulta_prolog = $relacion . '(X,W).';

            // Ejecutar la consulta Prolog
            $output = ejecutarConsultaProlog($consulta_prolog);

            // Analizar la salida de Prolog para extraer los valores de X y W
            $valores = [];
            if (strpos($output, "true") !== false) {
                // Extraer los valores de X y W
                preg_match_all("/X=(\w+),W=(\w+)/", $output, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $valores[] = $match[1] . "-" . $match[2];
                }
                $mensaje_respuesta = implode(", ", $valores);
                $mensaje_respuesta .= "\n¿Puedo ayudarte nuevamente?";
            }
                // Construir el mensaje de respuesta
            //     if (!empty($valores)) {
            //         $mensaje_respuesta = implode(", ", $valores);
            //         $mensaje_respuesta .= "\n¿Puedo ayudarte nuevamente?";
            //     } else {
            //         $mensaje_respuesta = "No tengo conocimiento sobre eso";
            //         $mensaje_respuesta .= "\n¿Puedo ayudarte con otra consulta?";
            //     }
            // } else {
            //     $mensaje_respuesta = "No tengo conocimiento sobre eso";
            //     $mensaje_respuesta .= "\n¿Puedo ayudarte con otra consulta?";
            // }

            echo $mensaje_respuesta;
            exit();
        } else {
            // Construir la consulta Prolog para otras relaciones
            $consulta_prolog = $relacion . '(' . implode(',', $objetos) . ').';

            // Ejecutar la consulta Prolog
            $output = ejecutarConsultaProlog($consulta_prolog);

            // Analizar la salida de Prolog para extraer los valores de X y W
            $valores = [];
            if (strpos($output, "true") !== false) {
                // Si la consulta tiene la palabra "quién", verificar si hay valores de X o W en la respuesta
                if (in_array('X', $objetos)) {
                    if (strpos($output, 'X=') !== false) {
                        // Extraer el valor de X
                        preg_match("/X=(\w+)/", $output, $matches);
                        if (isset($matches[1])) {
                            $valores['X'] = $matches[1];
                        }
                    }
                }
                if (in_array('W', $objetos)) {
                    if (strpos($output, 'W=') !== false) {
                        // Extraer todos los valores de W
                        preg_match_all("/W=(\w+)/", $output, $matches);
                        if (isset($matches[1])) {
                            $valores['W'] = $matches[1];
                        }
                    }
                }

                // Construir el mensaje de respuesta
                if (isset($valores['X'])) {
                    $mensaje_respuesta = $valores['X'];
                    $mensaje_respuesta .= "\n¿Puedo ayudarte nuevamente?";
                } elseif (isset($valores['W'])) {
                    // Imprimir todos los valores de W
                    $mensaje_respuesta = implode(', ', $valores['W']);
                    $mensaje_respuesta .= "\n¿Puedo ayudarte nuevamente?";
                } else {
                    $mensaje_respuesta = "Sí, tienen la relación '$relacion'";
                    $mensaje_respuesta .= "\n¿Puedo ayudarte nuevamente?";
                }
            } else {
                $mensaje_respuesta = "No tengo conocimiento sobre eso";
                $mensaje_respuesta .= "\n¿Puedo ayudarte con otra consulta?";
            }

            echo $mensaje_respuesta;
            exit();
        }
    }
}
