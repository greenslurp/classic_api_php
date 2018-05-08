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

    function get_all_supporters($client, $cred) {
        // The Guzzle client contains a cookie jar.  That needs to go
        // to the downstream read.  PLEASE NOTE that this is a demonstration
        // of a single read.  Salsa limits the number of records to 500
        // per read.  Generally, that means that you won't get everyrhing
        // in the database.  See the "read_all" example.

        $command = "/api/getObjects.sjs";
        $method = "GET";
        $queries = [
            query => [
                json   => true,
                'object' => 'supporter',
                limit    => '0,500'
            ],
            headers => [
                'Accept'     => 'application/json',
            ]
        ];
        $offset = 0;
        $count = 500;
        $result = [];
        try {
            do {
                $count = min($count, 500);
                $queries['query']['limit'] = $offset . ',' + $count;
                $response = $client->request($method, $command, $queries);
                $data = json_decode($response -> getBody() -> getContents());
                // print_r json_encode($data, JSON_PRETTY_PRINT);
                if ($data->status == 'error') {
                    throw new Exception($data->message);
                }
                print_r("Offset " . $offset . ", count " . count($data) . "\n");
                $result = array_merge($result, $data);
                $count = count($data);

                //foreach ($data as $r) {
                //    print_r ("    " . $offset . "    " . $r->supporter_KEY . ": " . $r->Email . "\n");
                //}

                $offset = $offset + $count;
            } while ($count > 0);
        } catch (Exception $e) {
            print_r('Caught exception: ' . $e->getMessage() .  "\n");
        }
        print_r("Offset " . $offset . ", count " . count($result) . ", done!");
    }


    function main() {
        $cred = initialize();
        $client = authenticate($cred);
        get_all_supporters($client, $cred);
    }

    main();
?>
