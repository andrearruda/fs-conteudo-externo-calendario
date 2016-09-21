<?php
// Routes

$app->get('/', function(){
    return $this->response->withRedirect($this->router->pathFor('commemorative-dates'));
});

$app->get('/capture', App\Action\Calendar\CaptureAction::class)->setName('capture');
$app->get('/commemorative-dates', App\Action\Calendar\CommemorativeDatesAction::class)->setName('commemorative-dates');