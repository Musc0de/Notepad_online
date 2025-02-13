<?php
// Total Requests Modal
echo '<div class="modal fade" id="totalRequestsModal" tabindex="-1" aria-labelledby="totalRequestsModalLabel" aria-hidden="true">';
echo '<div class="modal-dialog modal-dialog-scrollable">'; 
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title" id="totalRequestsModalLabel">Total Requests Details</h5>';
echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
echo '</div>';
echo '<div class="modal-body">';

// Get unique IP addresses and timestamps
$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$uniqueIpTimestamps = []; 

for ($i = 0; $i < count($lines); $i += 2) {
    $ip = $lines[$i];
    $timestamp = intval($lines[$i + 1]);
    $uniqueIpTimestamps[$ip] = $timestamp; 
}

// Display the information in a table format
echo '<table class="table table-striped">';
echo '<thead><tr><th>IP Address</th><th>Last Request Time</th></tr></thead>';
echo '<tbody>';

foreach ($uniqueIpTimestamps as $ip => $timestamp) {
    echo "<tr><td>$ip</td><td>" . date('Y-m-d H:i:s', $timestamp) . "</td></tr>";
}
echo '</tbody>';
echo '</table>';
echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


// Unique IPs Modal
echo '<div class="modal fade" id="uniqueIpsModal" tabindex="-1" aria-labelledby="uniqueIpsModalLabel" aria-hidden="true">';
echo '<div class="modal-dialog">';
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title" id="uniqueIpsModalLabel">Unique IPs Details</h5>';
echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
echo '</div>';
echo '<div class="modal-body">';
$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$uniqueIps = array_unique($lines);
echo '<ul>';
foreach ($uniqueIps as $ip) {
    if ($ip != "\n") {
        echo "<li>$ip</li>";
    }
}
echo '</ul>';
echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


// Last Visited Modal
echo '<div class="modal fade" id="lastVisitedModal" tabindex="-1" aria-labelledby="lastVisitedModalLabel" aria-hidden="true">';
echo '<div class="modal-dialog">';
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title" id="lastVisitedModalLabel">Last Visited Details</h5>';
echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
echo '</div>';
echo '<div class="modal-body">';
echo 'Last visited time: ' . htmlspecialchars($data['last_visited']);
echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

// Total Notes Created Modal
echo '<div class="modal fade" id="totalNotesModal" tabindex="-1" aria-labelledby="totalNotesModalLabel" aria-hidden="true">';
echo '<div class="modal-dialog modal-dialog-scrollable">'; 
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title" id="totalNotesModalLabel">Total Notes Created Details</h5>';
echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
echo '</div>';
echo '<div class="modal-body">';

// Baca isi file notes_log.txt
$notesContent = file_get_contents($logFilePath);

// Tampilkan isi file dalam elemen <pre> agar formatnya tetap terjaga
echo "<pre>$notesContent</pre>";

echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
