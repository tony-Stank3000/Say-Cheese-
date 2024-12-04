<?php

// Check if video data is received via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $videoData = $_FILES['video']['tmp_name'];

    // Path where the video will be saved
    $outputVideoFile = "streams/stream_" . time() . ".webm";  // Temporary WebM format

    // Move the uploaded file (WebM) to the desired location
    if (move_uploaded_file($videoData, $outputVideoFile)) {
        // You can process the video (convert to MP4) here using FFmpeg

        // Convert WebM to MP4 (using FFmpeg)
        $mp4File = "streams/stream_" . time() . ".mp4";
        $command = "ffmpeg -i $outputVideoFile -c:v libx264 -preset fast -crf 22 -c:a aac -strict experimental $mp4File";

        // Execute the command
        exec($command, $output, $return_var);

        // Check if conversion was successful
        if ($return_var === 0) {
            echo json_encode(['status' => 'success', 'message' => 'Video chunk saved and converted to MP4 successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to convert video to MP4.']);
        }

        // Optionally, delete the temporary WebM file after conversion
        unlink($outputVideoFile);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save video chunk.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No video data received.']);
}
