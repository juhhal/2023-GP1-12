<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmFwcGVhci5pbiIsImF1ZCI6Imh0dHBzOi8vYXBpLmFwcGVhci5pbi92MSIsImV4cCI6OTAwNzE5OTI1NDc0MDk5MSwiaWF0IjoxNzEwODc0OTI3LCJvcmdhbml6YXRpb25JZCI6MjE5NDk2LCJqdGkiOiJkMDdjNDkwZS00MjJlLTQwMzctYWViOS00ODM2NTc1ZDQxZTMifQ.QnhnMtHYeDa__GtBYDNBAwJ31_dJ0SMhFigPwUKCrTg";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    '{
    "endDate": "2099-02-18T14:23:00+03:00",
    "roomMode":"group",
    "fields": ["hostRoomUrl"]}'
  );

  $headers = [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
  ];

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  if ($httpcode !== 201) {
    $error = [
      "message" => "error",
      "error" => [
        "code" => $httpcode
      ]
    ];
    echo json_encode($error);
  } else {
    $dataURL = json_decode($response);
    $roomUrl = $dataURL->{'roomUrl'};
    $hostURL = $dataURL->{'hostRoomUrl'};

    $roomData = [
      "message" => "success",
      "roomUrl" => $roomUrl,
      "hostRoomUrl" => $hostURL
    ];
    echo json_encode($roomData);
  }
}
