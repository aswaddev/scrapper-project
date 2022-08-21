<?php

class Batches extends Controller
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

    public function index()
    {
        $batches = $this->batchesModel->getBatches();

        $this->loadView('batches/index', [
            'active' => 'batches/index',
            'batches' => $batches
        ]);
    }

    public function show($id)
    {
        $batch = $this->batchesModel->getBatchById($id);
        $airlines = $this->airlinesModel->getAirlinesByBatchId($batch->id);

        $this->loadView('batches/show', [
            'active' => 'batches/show',
            'batch' => $batch,
            'airlines' => $airlines
        ]);
    }

    public function store()
    {
        $name = $_POST['name'];

        $this->batchesModel->insert(compact('name'));

        return redirect('batches/index');
    }

    public function export($batchId)
    {
        set_time_limit(10000);

        $fleetData = $this->airlineDetailsModel->getAllByBatchId($batchId);

        $models = [];

        foreach ($fleetData as $airline) {
            $aircraftType =  $airline->aircraft_type;
            $explodedType = explode(' ', $aircraftType);
            $model = array_pop($explodedType);
            $company = implode(' ', $explodedType);
            if (!isset($models[$company])) $models[$company] = [];
            $models[$company][] = $model;
        }

        foreach ($models as $key => $model) {
            $models[$key] = array_unique($model);
        }

        $companies = array_keys($models);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $spreadsheet->getActiveSheet()->setTitle("Overall");

        foreach ($companies as $company) {
            $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $company));

            $spreadsheet->setActiveSheetIndexByName($company);

            $arrayData = [
                ['Airline']
            ];

            foreach ($models[$company] as $model) {
                $overallSheet[0][] = $company . ' ' . $model;
                $arrayData[0][] = $model;
            }

            $arrayData[0][] = 'Grand Total';

            $airlinesHavingCompany = $this->airlinesModel->getAirlineDetailsByCompany($batchId, $company);

            $airlines = [];

            foreach ($airlinesHavingCompany as $row) {
                if (!isset($airlines[$row->name])) {
                    $airlines[$row->name] = [];
                };
                $airlines[$row->name][$row->aircraft_type] = $row->in_service + $row->parked;
            }

            foreach ($airlines as $key => $airline) {
                $entry = [$key];

                $total = 0;

                foreach ($models[$company] as $model) {
                    if (isset($airline[$company . ' ' . $model]) && $airline[$company . ' ' . $model]) {
                        $entry[] = $airline[$company . ' ' . $model];
                        $total += $airline[$company . ' ' . $model];
                    } else {
                        $entry[] = '0';
                    }
                }

                if ($total === 0) $total = '0';

                $entry[] = $total;

                $arrayData[] = $entry;
            }


            $spreadsheet->getActiveSheet()
                ->fromArray(
                    $arrayData,  // The data to set
                    NULL        // Array values with this value will not be set
                );
        }

        $overallSheet = [
            ['Airline']
        ];

        $rows = $this->airlinesModel->getAirlineDetailsByBatch($batchId);
        $airlines = [];

        foreach ($rows as $row) {
            if (!isset($airlines[$row->name])) {
                $airlines[$row->name] = [];
            };

            $airlines[$row->name][$row->aircraft_type] = $row->in_service + $row->parked;
        }

        foreach ($companies as $key => $company) {
            foreach ($models[$company] as $model) {
                $overallSheet[0][] = $model;
            }
        }

        $overallSheet[0][] = 'Grand Total';


        foreach ($airlines as $key => $airline) {
            $entry = [$key];

            $total = 0;

            foreach ($companies as $key => $company) {
                foreach ($models[$company] as $model) {
                    if (isset($airline[$company . ' ' . $model]) && $airline[$company . ' ' . $model]) {
                        $entry[] = $airline[$company . ' ' . $model];
                        $total += $airline[$company . ' ' . $model];
                    } else {
                        $entry[] = '0';
                    }
                }
            }

            if ($total === 0) $total = '0';

            $entry[] = $total;

            $overallSheet[] = $entry;
        }

        $spreadsheet->setActiveSheetIndexByName('Overall');

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $overallSheet,  // The data to set
                NULL        // Array values with this value will not be set
            );

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        $filename = uniqid(rand(), true) . '.xlsx';

        $writer->save("exports/" . $filename);

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        $downloadLink = URLROOT . '/public/exports/' . $filename;

        flash('import_success', "Data exported successfully! Download from <a href='{$downloadLink}' target='_blank'>Here</a>");

        return redirect('batches/show/' . $batchId);
    }

    public function delete($id)
    {
        $this->batchesModel->delete($id);

        return redirect('batches/index');
    }
}
