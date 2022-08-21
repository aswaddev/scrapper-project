<?php
class Airline
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function bulkInsert($airlines, $batchId)
    {
        $values = [];

        $this->db->query('SELECT link FROM airlines WHERE batch_id = :batch_id');
        $this->db->bind(':batch_id', $batchId);
        $rows = $this->db->fetchAll();

        $existingLinks = [];

        foreach ($rows as $key => $row) {
            $existingLinks[] = $row->link;
        }

        foreach ($airlines as $airline) {
            if (!in_array("https://www.planespotters.net{$airline['link']}", $existingLinks)) {
                $name = addslashes($airline['name']);
                $link = "https://www.planespotters.net{$airline['link']}";
                $country = addslashes($airline['country']);
                $fleet = addslashes($airline['fleet'] ? $airline['fleet'] : 'N/A');
                $values[] = "('{$name}', '$link', '{$country}', '{$fleet}', $batchId)";
            }
        }

        if (!count($values)) return true;

        $values = implode(', ', $values);

        // Prepare query
        $this->db->query("INSERT INTO airlines (name, link, country, fleet, batch_id) VALUES $values");

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Fetching all airlines
    public function getAirlines($batchId)
    {
        $this->db->query("SELECT * FROM airlines WHERE batch_id = $batchId ORDER BY name");

        $results = $this->db->fetchAll();

        return $results;
    }

    // Fetching all airlines by given ids
    public function getAirlinesByIds($ids)
    {
        $idList = implode(', ', $ids);

        $this->db->query("SELECT * FROM airlines WHERE id IN ($idList) ORDER BY name");

        $results = $this->db->fetchAll();

        return $results;
    }

    // Fetching a single airline
    public function getAirlineById($id)
    {
        $this->db->query('SELECT * FROM airlines WHERE id = :id');
        $this->db->bind(':id', $id);
        $results = $this->db->fetchOne();
        return $results;
    }

    // Fetching airlines by batch id
    public function getAirlinesByBatchId($batchId)
    {
        $this->db->query('SELECT * FROM airlines WHERE batch_id = :batch_id');
        $this->db->bind(':batch_id', $batchId);
        $results = $this->db->fetchAll();
        return $results;
    }

    public function getAllForExport($batchId)
    {
        $this->db->query('SELECT * FROM airlines WHERE batch_id = :batch_id AND imported_at IS NOT NULL');
        $this->db->bind(':batch_id', $batchId);
        $results = $this->db->fetchAll();
        return $results;
    }

    public function getAirlinesByCompany($batchId, $company)
    {
        $this->db->query("SELECT (SUM(in_service) + SUM(parked)) as 'total_planes' FROM airlines 
        RIGHT JOIN airline_details ON airline_details.airline_id = airlines.id
        WHERE airlines.batch_id = $batchId 
        AND aircraft_type LIKE '$company%'
        GROUP BY airline_details.airline_id
        ORDER BY airlines.name");

        $results = $this->db->fetchAll();

        return $results;
    }

    public function getAirlineDetailsByCompany($batchId, $company)
    {
        $this->db->query("SELECT * FROM airlines 
        RIGHT JOIN airline_details ON airline_details.airline_id = airlines.id
        WHERE airlines.batch_id = $batchId
        AND aircraft_type LIKE '$company%'
        ORDER BY airlines.name");

        $results = $this->db->fetchAll();

        return $results;
    }

    public function getAirlineDetailsByBatch($batchId)
    {
        $this->db->query("SELECT * FROM airlines 
        RIGHT JOIN airline_details ON airline_details.airline_id = airlines.id
        WHERE airlines.batch_id = $batchId
        ORDER BY airlines.name");

        $results = $this->db->fetchAll();

        return $results;
    }


    public function getFleetTotal()
    {
        //         SELECT *, (SUM(in_service) + SUM(parked)) as 'total_planes' FROM airlines
        // RIGHT JOIN airline_details ON airline_details.airline_id = airlines.id
        // WHERE airlines.batch_id = 8
        // GROUP BY airline_details.airline_id
        // ORDER BY airlines.name;
    }
}
