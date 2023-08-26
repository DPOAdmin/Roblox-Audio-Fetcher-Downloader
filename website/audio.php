<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $placeId = $_POST["placeId"];
    $assetId = $_POST["assetId"];
    $robloxCookie = "_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_4530B47244A75BF6FAEBE649553EE969C47098CCEF7AF409D5A902E155FB054C645C199335D6FAC75FCF101797DC7229009A5702D5974C632F8D4FF969C1EA568DBDEF583BB0403090B500C362580B39930AF3F692D58027A197671A3724897F1F2FC1CBBACE70FD0723D5548DE7E5F11ADD1BD7288FBA7D2B54E3C754E8CA8557B5D1E49D3B3D95187ED75EDE3254516E80038CD415A283563473B98B1A436FECDA3322F6019E176E0CF1D7B16CF23B7147DBD8A147543712EC5453F0C513B883D559DF667681ACA9878F8DD7FA3737836D942D14877D5784AFC8A12FE904CC88B3D6B4C37A28FF391078BD22DBB88E76E68675017A6DFDA37F143F53D28F8BA1FD1A1250247F231F544523D4AFE9FCC1FD2363D1B47AFFE64BFA62810A273F43B5C7FDA86131A0152B4FEC3FD54A2E24A1A008CF05DBFF530969B113C67484DADEF2A7328D9D5E389209AF767629D623DA8433175DFA650D6CD4C6903FCA62D4F8AF2FA7A2C0FAE7DBB3479DD35B3956EA1F2A508C955238D53E96D4EC2A73632F4FE019D971D0F887F2B830970BF00F858E6534731025F10ABD02B4AAF8BC1E441981F23D2B833535D0379D3F2C12497401425C8A3DA8E58C14BE9BE2664B6596C700"; // Replace with your preset cookie

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
