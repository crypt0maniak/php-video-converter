<?php 
$uploads_dir = 'original/';
$file_name = basename($_FILES['file']['name']);
$output_name = explode('.', $file_name)[0];
$uploaded_file = $uploads_dir . $file_name;
$convert_status = ['mp4' => 0];

if(isset($_POST['submit'])) {
  if(move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
    // Make sure to get the correct path to ffmpeg
    // Run $ where ffmpeg to get the path
    $ffmpeg = '/usr/bin/ffmpeg';
    $video_mp4 = $output_name . '.mp4';
    exec($ffmpeg . ' -i "' . $uploaded_file . '" -c:v libx264 -an "./converted/' . $video_mp4 . '" -y 1>log.txt 2>&1', $output, $convert_status['mp4']);
  }
}

ob_clean();
$data = '{"convertedvideo":"/converted/' . $video_mp4 . "}';
$json = json_encode($data);
if ($json === false) {
  $json = json_encode(["jsonError" => json_last_error_msg()]);
  if ($json === false) {
    $json = '{"jsonError":"unknown"}';
  }
  http_response_code(500);
}
  
header("Content-type: application/json; charset=utf-8");
echo $json;
echo ($convert_status['mp4'] != 0) ? '{"status":"failed"}' : '{"status":"success"}';

exit();
?>
