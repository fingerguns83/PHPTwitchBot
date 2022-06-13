<?php

$query = "SELECT input FROM commands ORDER BY input";
$response = mysqli_fetch_all($conn->query($query));

foreach ($response as $i){
    $outputArr[] = '!' . $i[0];
}

$output = implode(', ', $outputArr);

echo $output;

