<?php
// Definir los sinónimos de las relaciones
$sinonimos_relaciones = array(
    "papá" => "padre", "papi" => "padre", "pa" => "padre", "papa" => "padre", // Sinónimos de Padre
    "mamá" => "madre", "mami" => "madre", "ma" => "madre", "mama" => "madre", // Sinónimos de Madre
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

    $palabras = preg_split("/[\s,¿?]+/", strtolower($mensaje));
    foreach ($palabras as $index => $palabra) {
        if ($palabra && ($palabra !== 'es' && $palabra !== 'de')) { 
            if ($palabra === 'quién') {
                // Si se encuentra la palabra "quién", reemplazarla por una variable X en la consulta Prolog
                $objetos[] = 'X';
                // Verificar si la palabra siguiente es una relación conocida
                if (isset($palabras[$index + 1]) && in_array($palabras[$index + 1], $relaciones)) {
                    $relacion = $palabras[$index + 1];
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

    return ['relacion' => $relacion, 'objetos' => $objetos];
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

        // Si la consulta contiene la palabra "quién", reemplazarla por una variable X en la consulta Prolog
        if (in_array('X', $objetos)) {
            $consulta_prolog = $relacion . '(X,' . implode(',', array_slice($objetos, 1)) . ').';
        } else {
            $consulta_prolog = $relacion . '(' . implode(',', $objetos) . ').';
        }
        
        $output = ejecutarConsultaProlog($consulta_prolog);
        //echo $output;
        if (strpos($output, "true") !== false) {
            // Si la consulta contiene una variable X, obtenemos el valor de X de la respuesta de Prolog
            if (in_array('X', $objetos)) {
                // Obtener el segundo valor después de un salto de línea en la salida de Prolog
                $lineas = explode("\n", $output);
                if (isset($lineas[1])) {
                    $valor_X = trim($lineas[1]); // Trim para eliminar espacios en blanco adicionales
                    $mensaje_respuesta = $valor_X ;
                } else {
                    $mensaje_respuesta = "No tengo conocimiento sobre eso";
                }
            } else {
                $mensaje_respuesta = "Sí, tienen la relación '$relacion' \n ¿Puedo ayudarte nuevamente?";
            }
        } else {
            $mensaje_respuesta = "No tengo conocimiento sobre eso";
        }

        echo $mensaje_respuesta;
        exit();
    }
}

