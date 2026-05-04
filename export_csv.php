<?php
require_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM sales"; 
$stmt = $db->prepare($query);
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, ['ID', 'Egg ID', 'Egg Type', 'Trays Sold', 'Total Price', 'Sale Date']);

foreach ($sales_data as $row) {
    fputcsv($output, [
        $row['id'],
        $row['egg_id'],
        $row['egg_type'],
        $row['trays_sold'], 
        $row['total_price'], 
        $row['sale_date'] 
    ]);
}

fclose($output);
exit();
?>
