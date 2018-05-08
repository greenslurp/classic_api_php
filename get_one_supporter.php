<?php
    // Uses Composer.
    require 'vendor/autoload.php';
    use GuzzleHttp\Client;
    use Symfony\Component\Yaml\Yaml;
    use GuzzleHttp\Cookie\CookieJar;

    function initialize() {
        // Read the contents of credentials.yaml.  The credentials
        // are not valid if there's not a `supporter_KEY` parameter.
        $cred =  Yaml::parseFile('./credentials.yaml');
        if  (FALSE == array_key_exists('supporter_KEY', $cred)) {
            throw new Exception("File credentials.yaml just contain a supporter_KEY for this application.");
        }
        return $cred;
    }

    function authenticate($cred) {
        // Authenticate.
        $uri = $cred['api_host'];
        $command = "/api/authenticate.sjs";
        $queries = [
            'query' => [
                json => true,
                email => $cred['email'],
                password => $cred['password'],
            ]
        ];
        $method = "GET";
        $cookieJar = new CookieJar();

        $client = new GuzzleHttp\Client([
            'base_uri' => $cred['api_host'],
            'cookies'  => $cookieJar
        ]);
        
        $response = $client->request($method, $command, $queries);
        $data = json_decode($response -> getBody() -> getContents());
        if ($data->status == 'error') {
            throw new Exception($data->message);
        }
        return $client;
    }

    function get_supporter($client, $cred) {
        // The Guzzle client contains a cookie jar.  That needs to go
        // to the downstream read.  PLEASE NOTE that this is a demonstration
        // of a single read.  Salsa limits the number of records to 500
        // per read.  Generally, that means that you won't get everyrhing
        // in the database.  See the "read_all" example.

        $command = "/api/getObject.sjs";
        $queries = [
            query => [
                json   => true,
                'object' => 'supporter',
                key    => $cred['supporter_KEY']
            ],
            headers => [
                'Accept'     => 'application/json',
            ]
        ];
        $method = "GET";
        $response = $client->request($method, $command, $queries);
        $data = json_decode($response -> getBody() -> getContents());
        // echo json_encode($data, JSON_PRETTY_PRINT);
        if ($data->status == 'error') {
            throw new Exception($data->message);
        }
        return $data;
    }

    function main() {
        $cred = initialize();
        $client = authenticate($cred);
        $data = get_supporter($client, $cred);
        print_r("Supporter is " . $data->First_Name . " " . $data->Last_Name . " " . $data -> Email . "\n");
    }

    main();
?>
