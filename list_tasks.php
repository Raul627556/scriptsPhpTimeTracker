<?php

$apiUrl = 'http://time.trackerapi.com/tasks/list';

$response = file_get_contents($apiUrl);
if ($response === false) {
    echo "Error al obtener las tareas.\n";
    exit(1);
}

$decodedResponse = json_decode($response, true);

$groupedTasks = [];

foreach ($decodedResponse["tasks"] as $task) {
    $taskName = $task['name'];

    if (!isset($groupedTasks[$taskName])) {
        $groupedTasks[$taskName] = [
            'name' => $taskName,
            'tasks' => [],
            'totalElapsedTime' => 0
        ];
    }

    $groupedTasks[$taskName]['tasks'][] = $task;

    if ($task['endTime'] !== null) {
        $startTime = strtotime($task['startTime']);
        $endTime = strtotime($task['endTime']);
        $totalElapsedTime = $endTime - $startTime;
        $groupedTasks[$taskName]['totalElapsedTime'] += $totalElapsedTime;
    }
}

echo "Listado de Tareas:\n";
echo str_pad("Nombre", 20) . str_pad("Estado", 15) . str_pad("Inicio", 25) . str_pad("Fin", 30) . "Tiempo Total\n";
echo str_repeat("=", 85) . "\n";

foreach ($groupedTasks as $taskGroup) {
    foreach ($taskGroup['tasks'] as $task) {
        $status = $task['endTime'] === null ? "pending" : "finished";
        $endTime = $task['endTime'] ?? 'N/A';
        $totalElapsedTime = $taskGroup['totalElapsedTime'] > 0 ? gmdate("H:i:s", $taskGroup['totalElapsedTime']) : 'N/A';
        
        echo str_pad($taskGroup['name'], 20);
        echo str_pad($status, 15);
        echo str_pad($task['startTime']. '  ', 25);
        echo str_pad($endTime. '  ', 25);
        echo str_pad($totalElapsedTime, 20);
        echo "\n";
    }
}
