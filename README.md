# Church Tools Proxy-Server
Simple Proxy Server für ChurchTools (church.tools) to forward API requests avoiding CORS problems.

Steps to initalized the project:
1) Import or clone
 import via composer: composer require stevenbuehner/churchtoolsproxyserver
 Or copy the github project and run `composer dump-autload`
2) Copy proxy.php and configure it with your credentials

For testing usecases you can run your server via:
`php -S localhost:8080`
