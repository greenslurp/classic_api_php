<?php
    // Uses Composer.
    require 'vendor/autoload.php';
    use GuzzleHttp\Client;
    use Symfony\Component\Yaml\Yaml;
    use GuzzleHttp\Cookie\CookieJar;

    // -------------------------------------------------------------------
    // Work in progress.  getLeftJoin.sjs conditions is slowing things up.
    // -------------------------------------------------------------------
    
    function initialize() {
        // Read the contents of credentials.yaml.  The credentials
        // are not valid if there's not a `supporter_KEY` parameter.
        $cred =  Yaml::parseFile('./credentials.yaml');
        if  (FALSE == array_key_exists('supporter_KEY', $cred)) {
            throw new Exception("File credentials.yaml must contain a supporter_KEY for this application.");
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

    // Read the supporter having the supporter_KEY in the credentials file.
    function get_supporter_and_donations($client, $cred) {
        $command = "/api/getLeftJoin.sjs";
        $first = true;
        $conditions = array(
            "supporter_KEY=" . $cred['supporter_KEY'],
            "RESULT=-1"
        );
        $conditions = join("&condition=", $conditions);
        $conditions = "condition=" . $conditions;
        
        $queries = [
            query => [
                json   => true,
                'object'  => 'supporter(supporter_KEY)donation',
                condition => 'supporter_KEY' .'=' . $cred['supporter_KEY'],
                condition => 'RESULT=-1'
            ],
            headers => [
                'Accept'     => 'application/json',
            ]
        ];
        $method = "GET";
        $response = $client->request($method, $command, $queries);
        $data = json_decode($response -> getBody() -> getContents());
        foreach ($data as $record) {
            if ($first) {
                $first = false;
                printf("%10d %-12s %-12s %-20s\n", $record->supporter_KEY, $record->First_Name, $record->LastName, $record->Email);
            }
            printf("%10d %15s %3s %10s\n", $record->donation_KEY, $record->Transaction_Date, $record->RESULT, $record->amount);
        }
        //echo json_encode($data, JSON_PRETTY_PRINT);
        if ($data->status == 'error') {
            throw new Exception($data->message);
        }
        return $data;
    }

    function main() {
        $cred = initialize();
        $client = authenticate($cred);
        $data = get_supporter_and_donations($client, $cred);
    }

    main();
?>
