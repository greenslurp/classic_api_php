<?php
    // Uses Composer.
    require 'vendor/autoload.php';
    use GuzzleHttp\Client;
    use Symfony\Component\Yaml\Yaml;

    // Read the contents of credentials.yaml
    try {
        $cred =  Yaml::parseFile('./credentials.yaml');
    } catch (ParseException $exception) {
        printf('Unable to parse ./credentials.yaml', $exception->getMessage());
        exit();
    }

    $uri = $cred['api_host'];
    $command = "/api/authenticate.sjs";
    $queries = [
        'query' => [
        "json" => true,
        "email" => $cred['email'],
        "password" => $cred['password'],
        ]
    ];
    $method = "GET";

    // Pass this client to all downstream API operations.
    // Yes, this should be a class with an `authenticate` method.
    $client = new GuzzleHttp\Client([
        'base_uri' => $cred['api_host'],
        'cookies'  => true
    ]);
    
    try {
        $response = $client->request($method, $command, $queries);
        $data = json_decode($response -> getBody() -> getContents());
        echo json_encode($data, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
    // var_dump($e);
}

?>

