// notification_logs.php
<?php
// ... database connection code ...
$logs = $db->query("SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 10")->fetchAll();
?>
<table class="table">
    <thead><tr><th>Email Sent To</th><th>Subject</th><th>Time</th></tr></thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?php echo $log['recipient_email']; ?></td>
            <td><?php echo $log['subject']; ?></td>
            <td><?php echo $log['sent_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>