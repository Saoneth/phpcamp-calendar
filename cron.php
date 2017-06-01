<?php
require_once 'IdosellApi.php';
require_once 'vendor/autoload.php';
define('APPLICATION_NAME', 'PHPCamp Calendar');
define('CREDENTIALS_PATH', __DIR__ . '/creditials.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
  $client->setApprovalPrompt('force');

  // Load previously authorized credentials from a file.
  if (file_exists(CREDENTIALS_PATH)) {
    $accessToken = json_decode(file_get_contents(CREDENTIALS_PATH), true);
  } else {
    if(empty($_GET['code'])) {
      header('Location: '.$client->createAuthUrl());
	  exit;
	}
    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Store the credentials to disk.
    if(!file_exists(dirname(CREDENTIALS_PATH))) {
      mkdir(dirname(CREDENTIALS_PATH), 0700, true);
    }
    file_put_contents(CREDENTIALS_PATH, json_encode($accessToken));
    printf("Credentials saved to %s\n", CREDENTIALS_PATH);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents(CREDENTIALS_PATH, json_encode($client->getAccessToken()));
  }
  return $client;
}

function findEvent($fEvent) {
	global $calendarId, $service;
	$optParams = array(
	  'maxResults' => 100,
	  'orderBy' => 'startTime',
	  'singleEvents' => TRUE,
	  'timeMin' => date('c', strtotime($fEvent['start']['dateTime'])-1),
	);
	$eventList = $service->events->listEvents($calendarId, $optParams);
	while(true) {
		foreach ($eventList->getItems() as $event) {
			if($fEvent['start']['dateTime'] == $event->start->dateTime && $fEvent['end']['dateTime'] == $event->end->dateTime)
				return true;
		}
		$pageToken = $eventList->getNextPageToken();
		if(!$pageToken)
			break;
		$optParams['pageToken'] = $pageToken;
		$eventList = $service->events->listEvents($calendarId, $optParams);
	}
	return false;
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

$calendarId = null;
$calendarList = $service->calendarList->listCalendarList();
while(true) {
  foreach ($calendarList->getItems() as $calendarListEntry) {
    if($calendarListEntry->getSummary() == 'phpcamp')
      $calendarId = $calendarListEntry->id;
      break 2;
    }
  $pageToken = $calendarList->getNextPageToken();
  if(!$pageToken)
    break;
  $optParams = array('pageToken' => $pageToken);
  $calendarList = $service->calendarList->listCalendarList($optParams);
}
if(is_null($calendarId)) {
  echo 'Calendar not found! You must have calendar named "phpcamp".';
  exit;
}

$api = new IdosellApi('3216', 'phpcamp5', 'qwerty321');

echo '<pre>';
$page = 1;
while(true) {
	$reservations = $api ->getReservations($page++);
	if(!$reservations)
		break;

	foreach ($reservations as $reservation) {
		$client = $reservation['client'];
		$tmp = array();
		foreach ($reservation["items"] as $item) 
			$tmp[] = $item["objectName"] . ' / ' . $item["itemCode"] . ' / ' . $item["prices"][0]["price"];
		$request = Array(
			'summary' => $client['firstName'].' / '.$client['lastName'].' / '.$client['phone'],
			'localization' => implode(';',$tmp),
			// 'colorId' => 1,
			'start' => Array(
				'dateTime' => date('c', strtotime($reservation["reservationDetails"]["dateFrom"])),
				'timeZone' => 'Europe/Warsaw'
			),
			'end' => Array(
				'dateTime' => date('c', strtotime($reservation["reservationDetails"]["dateTo"])),
				'timeZone' => 'Europe/Warsaw'
			),
			'attendees' => Array(),
		);
		
		
		$request['attendees'][] = array(
			'email' => $reservation['client']['email'],
			'displayName' => $reservation['client']['firstName'].' '.$reservation['client']['lastName'],
			'comment'=> $reservation['reservationDetails']['clientNote']
			//'comment'=> $reservation['reservationDetails']['phone']
		);
		// Add new Event
		if(!findEvent($request)) {
			$event = new Google_Service_Calendar_Event($request);
			$event = $service->events->insert($calendarId, $event);
			printf('Event created: %s<br />', $event->htmlLink);
		}
		break;
	}
}
echo 'done';