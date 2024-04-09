#!/usr/bin/env php

<?php
require __DIR__ . '/vendor/autoload.php';

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Token\AccessTokenInterface;

// See https://docs.acquia.com/cloud-platform/develop/api/auth/
// for how to generate a client ID and Secret.
$env_id = getenv("ENVIRONMENT_ID");

// Include a secrets file.
$secrets_file = getenv("SECRETS_FILE");

if (empty($secrets_file)) {
    $secrets_file = $_ENV['HOME'] . "secrets.cloudapi.php";
}

require_once $secrets_file;

// Get list of databases to backup from command line.
array_shift($argv);
$databases = $argv;

if (empty($databases)) {
    echo "Please provide a list of databases to be backed up." . PHP_EOL;
    exit(1);
}

echo "List of databases to backup: " . PHP_EOL;
var_dump($databases);

backup_databases($databases, $env_id, $clientId, $clientSecret);


/**
 * Backup one or more databases.
 */
function backup_databases($databases, $environment_uuid, $clientId, $clientSecret) {
    if (empty($databases)) {
        echo "No databases specified.";
        return;
    }

    $endpoint = "https://cloud.acquia.com/api/environments/{environmentId}/databases/{databaseName}/backups";

    $provider = new GenericProvider(
        [
            'clientId'                => $clientId,
            'clientSecret'            => $clientSecret,
            'urlAuthorize'            => '',
            'urlAccessToken'          => 'https://accounts.acquia.com/api/auth/oauth/token',
            'urlResourceOwnerDetails' => '',
        ]
    );

    $accessToken = $provider->getAccessToken('client_credentials');

    foreach ($databases as $database) {
        try {
            $url = str_replace("{environmentId}", $environment_uuid, $endpoint);
            $url = str_replace("{databaseName}", $database, $url);

            $request = $provider->getAuthenticatedRequest(
                'POST',
                $url,
                $accessToken
            );

            $client = new Client();
            $response = $client->send($request);

            var_dump($response->getBody()->getContents());
        }
        catch (Exception $ex) {
            echo "An exception occured during backup:" . PHP_EOL;
            echo $ex->getMessage();
        }
    }
}
