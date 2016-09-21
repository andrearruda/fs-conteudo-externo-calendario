<?php

namespace App\Action\Calendar;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Thapp\XmlBuilder\XMLBuilder;
use Thapp\XmlBuilder\Normalizer;

final class CommemorativeDatesAction
{
    private $fileCached = __DIR__ . '/../../../../cache/commemorative_dates/month.json';

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = json_decode(file_get_contents($this->getFileCached()), true);

        $xmlBuilder = new XmlBuilder('root');
        $xmlBuilder->setSingularizer(function ($name) {
            if ('days' === $name) {
                return 'day';
            }
            if ('itens' === $name) {
                return 'item';
            }
            return $name;
        });
        $xmlBuilder->load($data);
        $xml_output = $xmlBuilder->createXML(true);

        $response->write($xml_output);
        $response = $response->withHeader('content-type', 'text/xml');
        return $response;
    }

    public function getFileCached()
    {
        return $this->fileCached;
    }
}