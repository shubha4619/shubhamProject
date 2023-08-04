<?php
require_once 'vendor/autoload.php';

// Replace these with your credentials from the Google Cloud Platform project
$clientID = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$redirectUri = 'YOUR_REDIRECT_URI'; // Should be the URL to handle OAuth callback

// Create a new instance of the Google_Client class
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('https://www.googleapis.com/auth/calendar');

// If you're using OAuth, you'll need to handle the OAuth flow.
// Uncomment the following lines if you are using OAuth2 and want to get the access token
// if (isset($_GET['code'])) {
//     $client->authenticate($_GET['code']);
//     $_SESSION['access_token'] = $client->getAccessToken();
//     header('Location: ' . $redirectUri);
//     exit;
// }
// if (isset($_SESSION['access_token'])) {
//     $client->setAccessToken($_SESSION['access_token']);
// }
// // If the access token is expired, refresh it
// if ($client->isAccessTokenExpired()) {
//     $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
//     $_SESSION['access_token'] = $client->getAccessToken();
// }

// Create a new instance of the Google_Service_Calendar class
$service = new Google_Service_Calendar($client);

// Create the event for the Google Meet meeting
$event = new Google_Service_Calendar_Event(array(
    'summary' => 'Meeting Title',
    'description' => 'Meeting Description',
    'start' => array(
        'dateTime' => '2023-07-31T12:00:00', // Replace with the desired start time of the meeting
        'timeZone' => 'YOUR_TIMEZONE', // e.g., 'America/New_York'
    ),
    'end' => array(
        'dateTime' => '2023-07-31T13:00:00', // Replace with the desired end time of the meeting
        'timeZone' => 'YOUR_TIMEZONE', // e.g., 'America/New_York'
    ),
    'conferenceData' => array(
        'createRequest' => array(
            'requestId' => 'random-string-here', // Replace with a unique ID for the request
            'conferenceSolutionKey' => array(
                'type' => 'hangoutsMeet',
            ),
        ),
    ),
));

// Insert the event into the Google Calendar
$calendarId = 'primary'; // Use 'primary' for the default calendar associated with the authenticated user
$event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));

// The Google Meet URL will be available in the conference data
$meetLink = $event->conferenceData->getEntryPoints()[0]->uri;
echo 'Google Meet URL: ' . $meetLink;
