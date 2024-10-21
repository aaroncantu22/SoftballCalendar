<?php
session_start();
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret_658903576011-887mvp8v7ro1lac6i42n5t3nqo23hijj.apps.googleusercontent.com.json');
$client->setRedirectUri('http://localhost/SoftballCalendar/Calendar/oauth2callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: daily_calendar.php'); // Redirect to your Calendar page
    exit();
}