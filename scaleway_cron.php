<?php
/*
*****************************************
******* SCALEWAY SNAPSHOT CREATOR *******
*****************************************
Written by Kaan Dogan
Version: 1.0
Usage: Just update variables and run this script via cron
*/

// Settings (Please change it)
$zones = "nl-ams-1"; // For Paris: fr-par-1
$token = ""; // Should be like that xxxx-xxxxxx-xxxxx-xxxxxxxxxx-xxxxxx You can generate your token via Scaleway accounts page.
$volume_id = ""; // Volume ID of source volume. Should be like that xxxx-xxxxxx-xxxxx-xxxxxxxxxx-xxxxxx
$name = "Server Backup on " . date("d.m.Y"); // The name of your Snapshot.
$organization = ""; // Your organization ID. Should be like that xxxx-xxxxxx-xxxxx-xxxxxxxxxx-xxxxxx




// End of settings

Function GetContent($url,$headers,$data){
	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	If ($data ==! "") {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_POST, 1);
		}
	If ($data == "DELETE") {curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");		}
	$execResult = curl_exec ($ch);
	curl_close ($ch);
	$obj = json_decode($execResult, true);
	return $obj;

} // FUNCTION END

$headers = array(
			'X-Auth-Token: '. $token,
			'Content-Type: application/json');
$url = "https://api.scaleway.com/instance/v1/zones/". $zones ."/snapshots";
$data = json_encode(array(
            'volume_id' => $volume_id,
            'organization' => $organization,
            'name' => $name), true);
			
// DELETE LAST SNAPSHOT FIRST

$snapshot_list = GetContent($url,$headers,"");
$last_snapshot_id = $snapshot_list["snapshots"][0]["id"];
$del_url = "https://api.scaleway.com/instance/v1/zones/". $zones ."/snapshots/". $last_snapshot_id;
$del_last_snapshot = GetContent($del_url,$headers,"DELETE");

// CREATE NEW SNAPSHOT

$results = GetContent($url,$headers,$data);
If ($results["snapshot"]["state"] == "snapshotting"){echo "Completed";} Else {echo "Failed!";}


?>
