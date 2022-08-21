<?php
class AirlineDetail
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function bulkInsert($airlines, $batchId)
    {
        $values = [];

        $airlineIds = array_map(function ($airline) {
            return $airline['airline_id'];
        }, $airlines);

        $airlinesToImport = array_unique($airlineIds);

        $this->deleteByAirlineIds($airlinesToImport);
        $this->updateAirlineImportDates($airlinesToImport);

        foreach ($airlines as $airline) {
            $values[] = '(' . implode(', ', array_values($airline)) . ", $batchId)";
        }

        $values = implode(', ', $values);

        // Prepare query
        $this->db->query("INSERT INTO airline_details (airline, airline_id, aircraft_type, in_service, parked, current_total, future, historic, avg_age, overall_total, batch_id) VALUES $values");

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Fetching all airlines
    public function getAllByBatchId($batchId)
    {
        $this->db->query('SELECT * FROM airline_details WHERE batch_id = :batch_id ORDER BY airline');
        $this->db->bind(':batch_id', $batchId);

        $results = $this->db->fetchAll();

        return $results;
    }

    public function deleteByAirlineIds($ids)
    {
        $airlinesToDelete = implode(', ', $ids);

        $this->db->query("DELETE FROM airline_details WHERE airline_id IN ($airlinesToDelete)");

        // Execute the query
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateAirlineImportDates($importedAirlines)
    {
        $airlinesToUpdate = implode(', ', $importedAirlines);

        $this->db->query("UPDATE `airlines` SET `imported_at`= NOW() WHERE id IN ($airlinesToUpdate)");

        // Execute the query
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
