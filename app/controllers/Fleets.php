<?php

class Fleets extends Controller
{
    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        };

        $this->batchesModel = $this->loadModel('Batch');
        $this->airlinesModel = $this->loadModel('Airline');
        $this->airlineDetailsModel = $this->loadModel('AirlineDetail');
    }

    public function scrap($batchId)
    {
        set_time_limit(10000);

        $body = json_decode(file_get_contents('php://input'), true);

        $airlinesToScrape = $body['airlines'];

        try {
            $airlines = $this->airlinesModel->getAirlinesByIds($airlinesToScrape);

            $columns = [
                'aircraft_type',
                'in_service',
                'parked',
                'current_total',
                'future',
                'historic',
                'avg_age',
                'overall_total',
            ];

            $airlineDetails = [];

            foreach ($airlines as $airline) {
                $page = getHTML($airline->link, true);

                $xpath = new DOMXPath($page);

                $heading = $xpath->evaluate('//th[@class="dt-first dt-last center"]');

                $rows = $xpath->evaluate('//tr');

                if (count($rows) === 0) {
                    http_response_code(500);

                    echo json_encode([
                        'status' => 'error'
                    ]);

                    return;
                }

                foreach ($rows as $row) {
                    if (strpos($row->getAttribute('class'), 'subtype') !== false) {
                        $airlineDetail = [
                            'airline' => "\"{$heading[0]->childNodes[0]->textContent}\"",
                            'airline_id' => $airline->id,
                        ];

                        $key = 0;

                        foreach ($row->childNodes as $column) {
                            if ($column->nodeName === 'td' || $column->nodeName === 'th') {
                                $innerText = rtrim(ltrim($column->textContent));

                                $airlineDetail[$columns[$key]] = "\"$innerText\"";

                                $key++;
                            }
                        }

                        $airlineDetails[] = $airlineDetail;
                    }
                }
            }

            $this->airlineDetailsModel->bulkInsert($airlineDetails, $batchId);
        } catch (\Throwable $th) {
            http_response_code(500);

            echo json_encode([
                'status' => 'error'
            ]);

            return;
        }

        echo json_encode([
            'status' => 'success'
        ]);
    }
}
