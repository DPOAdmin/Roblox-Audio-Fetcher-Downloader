<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $placeId = $_POST["placeId"];
    $assetId = $_POST["assetId"];
    $robloxCookie = "ROBLOX_COOKIE"; // Replace with your preset cookie

    $audioUrl = fetchAudioLocation($assetId, $placeId, $robloxCookie);
    $assetName = fetchAssetName($assetId); // Fetch asset name

    if ($audioUrl) {
        echo '<h3>' . $assetName . '</h3>'; // Echo asset name
        echo '<video controls autoplay name="media">';
        echo '<source src="' . $audioUrl . '" type="audio/ogg">';
        echo '</video>';
    } else {
        echo "Audio location not found.";
    }
}

function fetchAudioLocation($assetId, $placeId, $robloxCookie) {
    while (true) {
        try {
            $bodyArray = [
                [
                    "assetId" => $assetId,
                    "assetType" => "Audio",
                    "requestId" => "0"
                ]
            ];

            $headers = [
                "User-Agent: Roblox/WinInet",
                "Content-Type: application/json",
                "Cookie: .ROBLOSECURITY=" . $robloxCookie,
                "Roblox-Place-Id: " . $placeId,
                "Accept: */*",
                "Roblox-Browser-Asset-Request: false"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://assetdelivery.roblox.com/v2/assets/batch");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyArray));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $locations = json_decode($response, true);
                if ($locations && count($locations) > 0) {
                    $obj = $locations[0];
                    if (isset($obj["locations"]) && isset($obj["locations"][0]["location"])) {
                        $audioUrl = $obj["locations"][0]["location"];
                        return $audioUrl;
                    }
                }
            }
        } catch (Exception $error) {
            error_log($error);
        }

        // Simulate waiting before retrying
        usleep(500000); // 500 milliseconds
    }
}

function fetchAssetName($assetId) {
    while (true) {
        $response = file_get_contents("https://economy.roproxy.com/v2/assets/{$assetId}/details");
        if ($response) {
            $assetInfo = json_decode($response, true);
            $assetName = $assetInfo["Name"];
            if ($assetName) {
                return $assetName;
            }
        }
        usleep(500000); // 500 milliseconds
    }
}
?>
