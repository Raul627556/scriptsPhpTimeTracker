<?php

if ($argc !== 3) {
    echo "Uso: php task_action.php <start|end> <task_name>\n";
    exit(1);
}

$action = $argv[1];
$taskName = $argv[2];
$apiUrl = 'http://time.trackerapi.com/task';

if ($action !== 'start' && $action !== 'end') {
    echo "Acción no válida. Usa 'start' o 'end'.\n";
    exit(1);
}

if ($action === 'end') {
    $url = $apiUrl . '/' . urlencode($taskName);
    $response = file_get_contents($url);
    if ($response === false) {
        echo "Error al obtener la tarea.\n";
        exit(1);
    }

    $responseData = json_decode($response, true);
    if (!isset($responseData['task'])) {
        echo "Tarea no encontrada.\n";
        exit(1);
    }

    if($responseData['task']['endTime'] !== null){
        echo "Esta tarea ya esta detenida" . "\n"; 
        exit(1);
    }

    $taskId = $responseData['task']['id'];

    $data = json_encode(['id' => $taskId]);
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];

    $stopUrl = $apiUrl . '/stop/' . urlencode($taskId);
    $context = stream_context_create($options);
    $stopResponse = file_get_contents($stopUrl, false, $context);
    if ($stopResponse === false) {
        echo "Error al realizar la solicitud para detener la tarea.\n";
        exit(1);
    }

    $stopResponseData = json_decode($stopResponse, true);
    echo $stopResponseData['message'] . "\n"; 
}

if ($action === 'start') {
    $data = json_encode(['name' => $taskName]);
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];

    $startUrl = $apiUrl . '/start';
    $context = stream_context_create($options);
    $startResponse = file_get_contents($startUrl, false, $context);
    if ($startResponse === false) {
        echo "Error al realizar la solicitud para iniciar la tarea.\n";
        exit(1);
    }

    $startResponseData = json_decode($startResponse, true);
    echo $startResponseData['message'] . "\n";
}
