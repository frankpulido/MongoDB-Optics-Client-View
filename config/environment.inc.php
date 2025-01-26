<?php

function setupDatabase($client) {
    $dbName = 'optics';
    $db = $client->selectDatabase($dbName);

    // Create collections with validators
    createCollections($db);

    // Import data from JSON files
    importData($db);

    echo "Database setup complete.\n";
}

function createCollections($db) {
    // Add your collection creation code here
    // Example:
    $db->createCollection("clients", [
        'validator' => [
            '$jsonSchema' => [
                // Your client validator schema here
            ]
        ]
    ]);
    // Repeat for other collections
}

function importData($db) {
    $collections = ['clients', 'employees', 'glassframes', 'suppliers'];
    foreach ($collections as $collection) {
        $jsonFile = __DIR__ . "/../data/{$collection}.json";
        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);
            $db->$collection->insertMany($data);
            echo "Imported data for {$collection}\n";
        }
    }
}

// Run the setup
setupDatabase($client);
?>