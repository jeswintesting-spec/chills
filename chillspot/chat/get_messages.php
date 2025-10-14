<?php
require_once '../db.php';

$currentUserId = $_SESSION['id'] ?? 0;
$receiverId = intval($_GET['receiver_id'] ?? 0);
if ($receiverId <= 0) exit;

$sql = "SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $currentUserId, $receiverId, $receiverId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $isSender = $row['sender_id'] == $currentUserId;
    $message = htmlspecialchars($row['message']);
    $time = date("H:i", strtotime($row['timestamp']));
    $align = $isSender ? "justify-end" : "justify-start";
    $bg = $isSender ? "bg-teal-500 text-white" : "bg-gray-200 text-gray-800";
    $rounded = $isSender ? "rounded-l-lg rounded-tr-lg" : "rounded-r-lg rounded-tl-lg";

    echo "<div class='flex $align mb-2'>
            <div class='max-w-[70%] p-2 $bg $rounded shadow-sm'>
                <div class='break-words'>$message</div>
                <div class='text-xs text-gray-600 mt-1 text-right'>$time</div>
            </div>
          </div>";
}
?>
