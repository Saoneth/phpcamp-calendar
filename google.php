<?php
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
// Print available event colors.
$colors = $service->colors->get();
foreach ($colors->getEvent() as $key => $color) {
	printf('<span style="color:%2$s;background:%3$s">colorId: %1$s</span><br />', $key, $color->getForeground(), $color->getBackground());
}

// Add new Event
$event = new Google_Service_Calendar_Event(array(
  'summary' => 'Google I/O 2015',
  'location' => '800 Howard St., San Francisco, CA 94103',
  'description' => 'A chance to hear more about Google\'s developer products.',
  'colorId' => 1,
  'start' => array(
	'dateTime' => '2017-06-28T09:00:00-07:00',
	'timeZone' => 'America/Los_Angeles',
  ),
  'end' => array(
	'dateTime' => '2017-06-28T17:00:00-07:00',
	'timeZone' => 'America/Los_Angeles',
  ),
  'attendees' => array(
	array('email' => 'lpage@example.com', 'displayName'=>'Imie Nazwisko', 'comment'=>'Komentarxfwefwef'),
  ),
));
/**/
$event = $service->events->insert($calendarId, $event);
printf('Event created: %s\n', $event->htmlLink);

// List all events
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);
$eventList = $service->events->listEvents($calendarId, $optParams);
while(true) {
  foreach ($eventList->getItems() as $event) {
    $start = $event->start->dateTime;
    $end = $event->end->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    printf("%s (from: %s to: %s) '%s'<br />", $event->getSummary(), $event->start->dateTime, $event->end->dateTime, $event->getDescription());
  }
  $pageToken = $eventList->getNextPageToken();
  if(!$pageToken)
    break;
  $optParams['pageToken'] = $pageToken;
  $eventList = $service->events->listEvents($calendarId, $optParams);
}
