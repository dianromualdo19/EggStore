<?php
class Sales {
    private $db;

    public function __construct($dbConnection) {
        // If $db is null, it means the connection in index.php failed
        if ($dbConnection === null) {
            die("Sales Class Error: Database connection is null. Check your index.php and db.php.");
        }
        $this->db = $dbConnection;
    }

    public function recordSale($egg_id, $trays, $price_per_tray) {
        $total_price = $trays * $price_per_tray;

        $stmt = $this->db->prepare("SELECT type, trays FROM eggs WHERE id = ?");
        $stmt->execute([$egg_id]);
        $egg = $stmt->fetch();

        if ($egg['trays'] < $trays) {
            return "insufficient_stock";
        }

        $stmt = $this->db->prepare("INSERT INTO sales (egg_id, egg_type, trays_sold, total_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$egg_id, $egg['type'], $trays, $total_price]);

        $new_stock = $egg['trays'] - $trays;
        $update = $this->db->prepare("UPDATE eggs SET trays = ? WHERE id = ?");
        $update->execute([$new_stock, $egg_id]);

        return "success";
    }
    public function recordPurchase($egg_id, $trays, $cost_per_tray) {
    $total_cost = $trays * $cost_per_tray;
    $stmt = $this->db->prepare("INSERT INTO sales (egg_id, egg_type, trays_sold, total_price) VALUES (?, ?, ?, ?)");

    $typeStmt = $this->db->prepare("SELECT type, trays FROM eggs WHERE id = ?");
    $typeStmt->execute([$egg_id]);
    $egg = $typeStmt->fetch();

    $stmt->execute([$egg_id, $egg['type'], -$trays, -$total_cost]);

    $new_stock = $egg['trays'] + $trays;
    $update = $this->db->prepare("UPDATE eggs SET trays = ? WHERE id = ?");
    $update->execute([$new_stock, $egg_id]);

    $log = $this->db->prepare("INSERT INTO stock_history (egg_id, egg_type, old_trays, new_trays) VALUES (?, ?, ?, ?)");
    $log->execute([$egg_id, $egg['type'], $egg['trays'], $new_stock]);

    return "success";
}
    public function getTodaysTotal() {
        // Line 38 fix: ensure $this->db is not null
        $stmt = $this->db->query("SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
}
?>