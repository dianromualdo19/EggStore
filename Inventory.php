<?php
class Inventory {
    private $db;

    public function __construct($dbConnection) {
        if ($dbConnection === null) {
            die("Error: Database connection failed. Please check db.php.");
        }
        $this->db = $dbConnection;
    }

    public function getAllEggs() {
        // Using 'eggs' table based on your image_7af36a.png
        $stmt = $this->db->query("SELECT * FROM eggs ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getHistory($limit = 5) {
        $stmt = $this->db->prepare("SELECT * FROM stock_history ORDER BY change_date DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStock($id, $new_trays, $status) {
        $stmt = $this->db->prepare("SELECT type, trays FROM eggs WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch();

        $update = $this->db->prepare("UPDATE eggs SET trays = ?, status = ? WHERE id = ?");
        if ($update->execute([$new_trays, $status, $id])) {
            if ($current['trays'] != $new_trays) {
                $log = $this->db->prepare("INSERT INTO stock_history (egg_id, egg_type, old_trays, new_trays) VALUES (?, ?, ?, ?)");
                $log->execute([$id, $current['type'], $current['trays'], $new_trays]);
            }
            return true;
        }
        return false;
    }

    public function clearHistory() {
        return $this->db->query("DELETE FROM stock_history");
    }
}
?>