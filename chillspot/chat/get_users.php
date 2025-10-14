<?php
require_once '../db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$currentUserId = $_SESSION['id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name, dept, last_active FROM users WHERE id != ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // ✅ Users are online if last_active within last 2 minutes
    $isOnline = (strtotime($row['last_active']) > time() - 120); 
    $statusText = $isOnline ? 'Online' : 'Offline';

    $name = htmlspecialchars($row['name']);
    $dept = htmlspecialchars($row['dept'] ?? '');
    $id = $row['id'];

    echo "
    <div onclick='openChat($id, \"$name\")' 
         class='user-item flex items-center justify-between p-3 rounded-lg mb-1 hover:bg-emerald-50 cursor-pointer'
         data-id='$id'
         data-status='$statusText'>
        <div class='flex items-center space-x-3'>
            <div class='relative'>
                <div class='w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 text-white flex items-center justify-center font-semibold shadow-sm'>
                    ". strtoupper(substr($name,0,1)) ."
                </div>
                <div class='absolute bottom-0 right-0 w-3 h-3 ". ($isOnline ? 'bg-green-500 animate-pulse' : 'bg-gray-400') ." border-2 border-white rounded-full'></div>
            </div>
            <div>
                <div class='font-medium text-gray-800'>$name</div>
                <div class='text-xs text-gray-500 italic'>$dept</div>
                <div class='text-xs text-gray-400'>$statusText</div>
            </div>
        </div>
    </div>
    ";
}
?>
