- https://cloudapi-docs.acquia.com/

# Required dependencies

Add the `oauth2-client` package to your application as follows.

```
composer require league/oauth2-client 
```

Copy the backup.php file into a folder outside of your docroot e.g. `<project_root>/scripts`

# Configuring on Acquia Cloud

- Create an account on Acquia Cloud with appropriate access to your application.
- Generate a client ID and client secret for the new account - https://docs.acquia.com/cloud-platform/develop/api/auth/ 
- Create a secrets file and add the client ID and secret
  - By default on Acquia Cloud this file will be loaded from `$HOME/secrets.cloudapi.php`

```php
<?php

$clientId = "<client_id>";
$clientSecret = "<client_secret>";
```

- Configure cron to run the code as required for the frequency of backups you would like

```bash
ENVIRONMENT_ID=<> /path/to/backup.php <database1>...<database n>
```

- `ENVIRONMENT_ID` can be obtained by navigating to the environment in the Acquia Cloud console and then extracting the UUID from the URL.

As an example, to backup the databases `test` and `mydb` on the environment `12345-321abc11-991e`, and the `backup.php` script living in `scripts` the command would be.

```bash
ENVIRONMENT_ID="12345-321abc11-991e" /var/www/html/scripts/backup.php test mydb
```
