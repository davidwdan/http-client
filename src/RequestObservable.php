<?php


namespace React\HttpClient;


use Rx\Observable\BaseObservable;

class RequestObservable extends BaseObservable
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function doStart($scheduler)
    {

        return $scheduler->schedule(function () {

            $this->request->on('response', function (Response $response) {
               $this->onNext(new ResponseObservable($response));
            });

            $this->request->on('error', function ($error) {
                $this->onError($error);
            });

            $this->request->on('end', function () {
                $this->onCompleted();
            });

            $this->request->end();
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