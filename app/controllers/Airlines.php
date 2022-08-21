<?php

class Airlines extends Controller
{
    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        };

        $this->airlinesModel = $this->loadModel('Airline');
        $this->airlineDetailsModel = $this->loadModel('AirlineDetail');
    }

    // Start scrapping
    public function links($batchId)
    {
        $alphabets = range('A', 'Z') + ['9'];

        $airlines = [];

        foreach ($alphabets as $alphabet) {
            $this->getAirlinesListing('https://www.planespotters.net/airlines', $alphabet, 1, $airlines);
        }

        $this->airlinesModel->bulkInsert($airlines, $batchId);

        $importedAirlinesCount = count($airlines);

        flash('import_success', "{$importedAirlinesCount} airlines imported successfully!");

        return redirect('batches/show/' . $batchId);
    }

    // Get all airlines for a particular alphabet
    public function getAirlinesListing($url, $alphabet, $pageNo, &$result)
    {
        $columns = [
            1 => 'name',
            2 => 'country',
            3 => 'fleet',
        ];

        $xpath = new DOMXPath(getHTML($url . '/' . $alphabet . '/' . $pageNo, false));

        $rows = $xpath->evaluate('//div[@class="dt-tr"]');
        $nextPage = $xpath->evaluate('//a[@rel="next"]');

        foreach ($rows as $key => $row) {
            if ($key) {
                $cells = $row->getElementsByTagName('div');

                $cellData = [];

                foreach ($cells as $key => $cell) {
                    if (isset($columns[$key])) {
                        $anchor = $cell->getElementsByTagName('a');
                        if ($anchor[0]) {
                            $cellData[$columns[$key]] = $anchor[0]->nodeValue;
                            if ($key === 1) $cellData['link'] = $anchor[0]->getAttribute('href');
                        } else {
                            $cellData[$columns[$key]] = null;
                        }
                    }
                }

                $result[] = $cellData;
            }
        }

        // GO TO THE NEXT PAGE 
        if (isset($nextPage[0]) && $nextPage[0]) {
            $nextPageLink = $nextPage[0]->getAttribute('href');
            $explodedNextPageLink = explode('/', $nextPageLink);
            $nextPageNo = end($explodedNextPageLink);

            if ($nextPageNo)
                $this->getAirlinesListing('https://www.planespotters.net/airlines', $alphabet, $nextPageNo, $result);
        }
    }

    public function getAirlineDetails($batchId)
    {
        try {
            $airlines = $this->airlinesModel->getAirlines($batchId);

            $airlines = [$airlines[0], $airlines[1]];

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
            throw $th;
        }

        flash('import_success', 'Fleet data scraped successfully!');
        redirect('batches/show/' . $batchId);
    }
}
