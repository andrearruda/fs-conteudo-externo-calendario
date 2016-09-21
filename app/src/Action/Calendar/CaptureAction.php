<?php

namespace App\Action\Calendar;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use phpQuery;

final class CaptureAction
{
    private $html, $fileCached;
    private $url = 'http://www.calendarr.com/brasil/datas-comemorativas-2016/';

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->setHtml(phpQuery::newDocumentFileHTML($this->getUrl()));
        $this->setFileCached(__DIR__ . '/../../../../cache/commemorative_dates/month.json');

        $data = array();

        $doc = phpQuery::newDocument($this->getHtml());

        foreach($doc['div.holidays-box-col2 span.holiday-month'] as $key => $span)
        {
            $data[$key]['month'] = pq($span)->text();
            $data[$key]['days'] = array();

            $totalDaysInMonth = date('t', strtotime(date('Y') . '-' . str_pad(($key + 1), 2, '0', STR_PAD_LEFT) . '-01'));

            for($i = 1; $i <= $totalDaysInMonth; $i++)
            {
                $data[$key]['days'][] = array(
                    'date' => date('Y') . '-' . str_pad(($key + 1), 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'itens' => ''
                );
            }

            foreach($doc['div.holidays-box-col2 div.row:eq(' . $key . ') .list-holiday-box'] as $i => $li)
            {
                $day = (int) pq($li)->find('span.holiday-day')->text();
                $data[$key]['days'][($day - 1)]['itens'][] = pq($li)->find('.holiday-name')->text();
            }
        }

        file_put_contents($this->getFileCached(), json_encode($data));

        $response->write(json_encode($data));
        $response = $response->withHeader('content-type', 'application/json');
        return $response;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @return mixed
     */
    public function getFileCached()
    {
        return $this->fileCached;
    }

    /**
     * @param mixed $fileCached
     */
    public function setFileCached($fileCached)
    {
        $this->fileCached = $fileCached;
    }


}