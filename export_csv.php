<?php
require_once 'db.php';
$database = new Database();
$db = $database->getConnection();

// Query para makuha ang data
$query = "SELECT * FROM sales"; 
$stmt = $db->prepare($query);
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// I-set ang headers para maging CSV file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

$output = fopen('php://output', 'w');

// I-set ang column headers sa Excel/CSV
fputcsv($output, ['ID', 'Egg ID', 'Egg Type', 'Trays Sold', 'Total Price', 'Sale Date']);

// I-loop ang data galing sa database
foreach ($sales_data as $row) {
    // Gagamitin natin ang eksaktong key names na nakita natin sa debug
    fputcsv($output, [
        $row['id'],
        $row['egg_id'],
        $row['egg_type'],
        $row['trays_sold'], // Dito nag-error dati
        $row['total_price'], // Dito nag-error dati
        $row['sale_date']    // Dito nag-error dati
    ]);
}

fclose($output);
exit();
?>