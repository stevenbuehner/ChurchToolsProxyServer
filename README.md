# Church Tools Proxy-Server
Simple proxy server for ChurchTools (church.tools) to forward API requests and avoiding CORS problems.

## Setup
Steps to initialize the project:
1) Either import via composer: `composer require stevenbuehner/churchtoolsproxyserver`
 or copy the github project and run `composer dump-autoload`
2) Copy proxy.php and configure it with your credentials

For testing use cases you can run your server via:
`php -S localhost:8080`

## Usage
Send a POST-request from any allowed Host (see setup of proxy.php) to your proxy.php.

Required POST Parameters are:

| Parameter-Name | Values | Required | Example |
|----------------|:------:|----------|---------|
| m | Name of ChurchTools Module | true| "churchcal/ajax" |
| f | Name of ChurchTools function ([see](https://api.churchtools.de/package-CT.API.html)) | true | "getMasterData"|
| data | Additional data to forward to ChurchTools | false | ['category_ids' => [1,2,3]]|


# Example-Client
An example VueJs App using this proxy can be found at: https://github.com/stevenbuehner/ChurchToolsVuejsClientExample