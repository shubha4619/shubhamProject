<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;
use Google_Service_Calendar ;
use Google_Service_Calendar_Event ;


class MeetController extends Controller
{

    public function handleGoogleCallback()
    {

        $user = Socialite::driver('google')->user();

        $client = new Google_Client();
        $client->setAccessToken($user->token);

        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Meeting Title',
            'start' => array(
                'dateTime' => '2023-08-15T10:00:00',
                'timeZone' => 'America/New_York',
            ),
            'end' => array(
                'dateTime' => '2023-08-15T11:00:00',
                'timeZone' => 'America/New_York',
            ),
            'conferenceData' => array(
                'createRequest' => array(
                    'conferenceSolutionKey' => array(
                        'type' => 'hangoutsMeet',
                    ),
                    'requestId' => '123',
                ),
            ),
        ));

        $calendarId = 'primary'; // Use 'primary' for the user's primary calendar
        $event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));

        $meetLink = $event->hangoutLink;

        return $meetLink;

    }
}
