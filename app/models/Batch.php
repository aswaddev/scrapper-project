<?php

class Batch
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Fetching all airlines
    public function getBatches()
    {
        $this->db->query('SELECT * FROM batches ORDER BY name');
        $results = $this->db->fetchAll();
        return $results;
    }

    // Fetching a single airline
    public function getBatchById($id)
    {
        $this->db->query('SELECT * FROM batches WHERE id = :id');
        $this->db->bind(':id', $id);
        $results = $this->db->fetchOne();
        return $results;
    }

    // Adding a batch
    public function insert($batch)
    {
        // Prepare query
        $this->db->query("INSERT INTO batches (name) VALUES(:name)");

        // Bind params
        $this->db->bind(':name', $batch['name']);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        $this->db->query('DELETE FROM batches WHERE id = :id');
        // Bind Values
        $this->db->bind(':id', $id);
        // Execute the query
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
