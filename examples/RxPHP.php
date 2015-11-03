<?php
require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dnsResolver        = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$factory = new React\HttpClient\Factory();
$client  = $factory->create($loop, $dnsResolver);

$client->request('GET', 'https://github.com/')
  ->selectMany(function (\React\HttpClient\ResponseObservable $response) {
      return $response;
  })
  ->reduce(function ($buffer, $data) {
      return $buffer .= $data;
  }, '')
  ->subscribe(new \Rx\Observer\CallbackObserver(
    function ($data) {
        echo $data;
    },
    function ($error) {
        echo $error;
    },
    function () {
        echo "complete";
    }
  ));

$loop->run();
