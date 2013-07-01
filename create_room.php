<?php 
    $counter_file = 'room_counter';
    $room_counter = file_get_contents($counter_file);
    $room_counter++;
    echo base64_encode($room_counter);
    file_put_contents($counter_file, $room_counter);
?>
