<?php


namespace React\HttpClient;


use Rx\Observable\BaseObservable;

class ResponseObservable extends BaseObservable
{

    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    protected function doStart($scheduler)
    {

        return $scheduler->schedule(function () {

            $this->response->on('data', function ($data, $response) {
                $this->onNext($data);
            });
            $this->response->on('error', function ($error) {
                $this->onError($error);
            });
            $this->response->on('end', function () {
                $this->onCompleted();
            });

        });

    }

    protected function onNext($data)
    {
        foreach ($this->observers as $observer) {
            $observer->onNext($data);
        }
    }

    protected function onError($error)
    {
        foreach ($this->observers as $observer) {
            $observer->onError($error);
        }
    }

    protected function onCompleted()
    {
        foreach ($this->observers as $observer) {
            $observer->onCompleted();
        }
    }

}