<?php declare(strict_types=1);

namespace Sas\SyncerModule\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class SyncerTaskHandler extends ScheduledTaskHandler
{
    public static function getHandledMessages(): iterable
    {
        return [ SyncerTask::class ];
    }

    public function run(): void
    {
    	error_log( '['.date("F j, Y, g:i a e O").']'.": Cron is working. \n", 3, "log.txt" );

    	// get auth token to call sync API

    	$base_url = "http://localhost:8088";

    	$url = $base_url. "/api/oauth/token";
		$username = "admin";
		$password = "123456789";

		$ch = curl_init();
		$headers = array(
		    'Content-type: application/json'
		);

		$post = array(
		    'client_id' => 'administration',
		    'grant_type' => 'password',
		    'scopes'   => 'write',
		    'username' => $username,
		    'password' => $password
		);

		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

		$server_output = curl_exec($ch);

		curl_close( $ch );

		$server_output = json_decode( $server_output );

		if( isset($server_output->access_token )){
			$access_token = $server_output->access_token;
			$refresh_token = $server_output->refresh_token;

			error_log( "Access-Token: $access_token \n", 3, "log.txt" );
		}
		else{
			echo "Authentication failed. 401 unauthorized";
			exit;
		}

    	// call sync
		$url = $base_url."/api/v1/sas-syncer/my-action-api";

		$headers = array(
		    'Content-type: application/json',
		    'Authorization: Bearer '.$access_token
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);

		$server_output = json_decode( $server_output );

		if( !isset( $server_output->error ))
		{
			print_r( $server_output );
		}
		else
		{
			echo "Error.";
			die;
		}

        echo 'Do stuff!';
    }
}