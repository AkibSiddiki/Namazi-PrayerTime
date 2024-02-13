<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use IslamicNetwork\PrayerTimes\PrayerTimes;
use Illuminate\Support\Facades\Http;



class NamazController extends Controller
{
    public function index()
    {
        define('LATITUDE_ADJUSTMENT_METHOD_ANGLE', 'Angle');
        define('TIME_FORMAT_24H', '24H');

        $ipDetails = $this->getIPDetails();

        if ($ipDetails == null or $ipDetails['status'] == 'fail') {
            return abort(404);
        }

        $districtName = '';

        if ($ipDetails['district'] != '') {
            $districtName = $ipDetails['district'];
        } else if ($ipDetails['city'] != '') {
            $districtName = $ipDetails['city'];
        } else {
            return abort(404);
        }
        $districtName = strtolower($districtName);

        $district_centers = $this->getDistrictCoordinates();
        //Undefined array key then return 404
        if (!isset($district_centers[$districtName])) {
            return abort(404);
        }

        $timezone = 'Asia/Dhaka';
        $latitude = $district_centers[$districtName]['latitude'];
        $longitude = $district_centers[$districtName]['longitude'];

        $pt = new PrayerTimes('KARACHI'); //https://github.com/islamic-network/prayer-times/blob/master/src/PrayerTimes/Method.php
        $pt->tune($imsak = 0, $fajr = 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);

        // for today
        $times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = TIME_FORMAT_24H);
        return view('welcome', compact('times', 'ipDetails'));
    }

    public function districtPrayerTimes(string $districtName)
    {
        define('LATITUDE_ADJUSTMENT_METHOD_ANGLE', 'Angle');
        define('TIME_FORMAT_24H', '24H');

        $district_centers = $this->getDistrictCoordinates();

        $districtName = strtolower($districtName);

        if (!isset($district_centers[$districtName])) {
            return abort(404);
        }


        $timezone = 'Asia/Dhaka';
        $latitude = $district_centers[$districtName]['latitude'];
        $longitude = $district_centers[$districtName]['longitude'];

        $pt = new PrayerTimes('KARACHI'); //https://github.com/islamic-network/prayer-times/blob/master/src/PrayerTimes/Method.php
        $pt->tune($imsak = 5, $fajr = 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);


        $ipDetails =
            [
                'country' => 'Bangladesh',
                'district' => ucfirst($districtName),
            ];
        // for today
        $times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = TIME_FORMAT_24H);
        return view('welcome', compact('times', 'ipDetails'));
    }


    public function getIPDetails()
    {

        $ip = $this->getOnlineIPAddress();
        if ($ip == '') {
            return  [
                'status' => "fail",
            ];
        }
        $response = Http::get("http://ip-api.com/json/$ip?fields=status,country,city,district");

        if ($response->successful()) {

            $data = $response->json();

            $ipDetails = [
                'status' => $data['status'],
                'country' => $data['country'] ?? '',
                'city' => $data['city'] ?? '',
                'district' => $data['district'] ?? '',
            ];

            return $ipDetails;
        } else {
            $ipDetails = [
                'status' => "fail",
            ];

            return $ipDetails;
        }
    }

    public function getOnlineIPAddress()
    {
        $response = Http::get('https://api.ipify.org?format=json');

        if ($response->successful()) {
            $data = $response->json();
            $ip = $data['ip'];
            return $ip;
        } else {
            return '';
        }
    }

    private function getDistrictCoordinates()
    {
        return
            array(
                "dhaka" => array("latitude" => 23.8103, "longitude" => 90.4125),
                "chattogram" => array("latitude" => 22.3350, "longitude" => 91.8364),
                "khulna" => array("latitude" => 22.8158, "longitude" => 89.5686),
                "rajshahi" => array("latitude" => 24.3636, "longitude" => 88.6241),
                "barishal" => array("latitude" => 22.7010, "longitude" => 90.3535),
                "sylhet" => array("latitude" => 24.8898, "longitude" => 91.8697),
                "rangpur" => array("latitude" => 25.7439, "longitude" => 89.2752),
                "mymensingh" => array("latitude" => 24.7471, "longitude" => 90.4203),
                "cox's bazar" => array("latitude" => 21.4272, "longitude" => 92.0058),
                "narayanganj" => array("latitude" => 23.6139, "longitude" => 90.4993),
                "gazipur" => array("latitude" => 23.9981, "longitude" => 90.4267),
                "tangail" => array("latitude" => 24.2513, "longitude" => 89.9167),
                "cumilla" => array("latitude" => 23.4683, "longitude" => 91.1786),
                "brahmanbaria" => array("latitude" => 23.9574, "longitude" => 91.1110),
                "noakhali" => array("latitude" => 22.8691, "longitude" => 91.0998),
                "dinajpur" => array("latitude" => 25.6217, "longitude" => 88.6356),
                "faridpur" => array("latitude" => 23.6071, "longitude" => 89.8429),
                "pabna" => array("latitude" => 24.0130, "longitude" => 89.2445),
                "jamalpur" => array("latitude" => 24.9160, "longitude" => 89.9442),
                "kushtia" => array("latitude" => 23.9013, "longitude" => 89.1196),
                "narail" => array("latitude" => 23.1724, "longitude" => 89.5127),
                "joypurhat" => array("latitude" => 25.0946, "longitude" => 89.0187),
                "bogura" => array("latitude" => 24.8465, "longitude" => 89.3723),
                "manikganj" => array("latitude" => 23.8597, "longitude" => 90.0094),
                "chandpur" => array("latitude" => 23.2333, "longitude" => 90.6448),
                "sunamganj" => array("latitude" => 25.0657, "longitude" => 91.3959),
                "patuakhali" => array("latitude" => 22.3554, "longitude" => 90.3348),
                "moulvibazar" => array("latitude" => 24.4820, "longitude" => 91.7719),
                "jhenaidah" => array("latitude" => 23.5448, "longitude" => 89.1514),
                "pirojpur" => array("latitude" => 22.5818, "longitude" => 89.9720),
                "magura" => array("latitude" => 23.4870, "longitude" => 89.4194),
                "habiganj" => array("latitude" => 24.3740, "longitude" => 91.4167),
                "feni" => array("latitude" => 23.0238, "longitude" => 91.3945),
                "satkhira" => array("latitude" => 22.7223, "longitude" => 89.0705),
                "thakurgaon" => array("latitude" => 26.0330, "longitude" => 88.4617),
                "chuadanga" => array("latitude" => 23.6427, "longitude" => 88.8486),
                "sirajganj" => array("latitude" => 24.4500, "longitude" => 89.7167),
                "bagerhat" => array("latitude" => 22.6516, "longitude" => 89.7859),
                "lakshmipur" => array("latitude" => 22.9425, "longitude" => 90.8418),
                "nilphamari" => array("latitude" => 25.9348, "longitude" => 88.8567),
                "jashore" => array("latitude" => 23.1664, "longitude" => 89.2089),
                "gaibandha" => array("latitude" => 25.3288, "longitude" => 89.5281),
                "sherpur" => array("latitude" => 25.0205, "longitude" => 90.0171),
                "chapainawabganj" => array("latitude" => 24.5965, "longitude" => 88.2771),
                "madaripur" => array("latitude" => 23.1641, "longitude" => 90.1897),
                "natore" => array("latitude" => 24.4055, "longitude" => 89.2075),
                "laxmipur" => array("latitude" => 22.9425, "longitude" => 90.8418),
                "panchagarh" => array("latitude" => 26.3417, "longitude" => 88.5542),
                "kurigram" => array("latitude" => 25.8142, "longitude" => 89.5907),
                "rajbari" => array("latitude" => 23.7574, "longitude" => 89.6475),
                "meherpur" => array("latitude" => 23.7639, "longitude" => 88.6318),
                "khagrachari" => array("latitude" => 23.1193, "longitude" => 91.9847),
                "rangamati" => array("latitude" => 22.7324, "longitude" => 92.2987),
                "bandarban" => array("latitude" => 22.1953, "longitude" => 92.2187),
                "barguna" => array("latitude" => 22.0953, "longitude" => 90.1121),
                "munsiganj" => array("latitude" => 23.5436, "longitude" => 90.5113),
                "shariatpur" => array("latitude" => 23.2415, "longitude" => 90.4344),
            );
    }

    function calculatePrayerTimes($latitude, $longitude, $timezone, $date)
    {
        // Validate input parameters
        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($timezone)) {
            return "Invalid input parameters";
        }
        if (!filter_var($latitude, FILTER_VALIDATE_FLOAT, array("options" => array("min_range" => -90, "max_range" => 90)))) {
            return "Invalid latitude value";
        }
        if (!filter_var($longitude, FILTER_VALIDATE_FLOAT, array("options" => array("min_range" => -180, "max_range" => 180)))) {
            return "Invalid longitude value";
        }
        if (!filter_var($timezone, FILTER_VALIDATE_FLOAT, array("options" => array("min_range" => -12, "max_range" => 14)))) {
            return "Invalid timezone value";
        }

        // Set the timezone
        date_default_timezone_set("Etc/GMT" . ($timezone > 0 ? "-" : "+") . abs($timezone));

        // Convert date to timestamp
        $timestamp = strtotime($date);

        // Calculate the Julian date
        $julianDate = $timestamp / 86400 - 0.5 + 2440587.5;

        // Calculate the declination of the sun
        $n = $julianDate - 2451545.0;
        $L = 280.460 + 0.9856474 * $n;
        $g = 357.528 + 0.9856003 * $n;
        $lambda = $L + 1.915 * sin(deg2rad($g)) + 0.020 * sin(2 * deg2rad($g));
        $epsilon = 23.439 - 0.0000004 * $n;
        $delta = asin(sin(deg2rad($epsilon)) * sin(deg2rad($lambda)));

        // Convert latitude to radians
        $latInRadians = deg2rad($latitude);

        // Define angles for calculation
        $FAJR_ANGLE = 18;
        $ISHA_ANGLE = 18;

        // Calculate Fajr time
        $fajrTime = 12 - (1 / 15) * rad2deg(acos((sin(deg2rad($FAJR_ANGLE)) - sin($latInRadians) * sin($delta)) / (cos($latInRadians) * cos($delta))));

        // Get sunrise and sunset times
        $sunInfo = date_sun_info($timestamp, $latitude, $longitude);

        // Calculate Dhuhr time
        $dhuhrTime = 12 - ($longitude / 15);

        // Calculate Asr time
        $asrTime = $dhuhrTime + (1 / 15) * rad2deg(atan(1 + tan(deg2rad(abs($latInRadians - deg2rad($delta))))));

        // Calculate Maghrib time
        $maghribTime = 12 + (1 / 15) * rad2deg(acos((sin(deg2rad(-0.833)) - sin($latInRadians) * sin($delta)) / (cos($latInRadians) * cos($delta))));

        // Calculate Isha time
        $ishaTime = 12 + (1 / 15) * rad2deg(acos((sin(deg2rad($ISHA_ANGLE)) - sin($latInRadians) * sin($delta)) / (cos($latInRadians) * cos($delta))));

        // Format times
        $prayerTimes = array(
            'Fajr' => date("H:i", $timestamp + $fajrTime * 3600),
            'Sunrise' => date("H:i", $sunInfo['sunrise']),
            'Dhuhr' => date("H:i", $timestamp + $dhuhrTime * 3600),
            'Asr' => date("H:i", $timestamp + $asrTime * 3600),
            'Sunset' => date("H:i", $sunInfo['sunset']),
            'Maghrib' => date("H:i", $timestamp + $maghribTime * 3600),
            'Isha' => date("H:i", $timestamp + $ishaTime * 3600)
        );

        return $prayerTimes;
    }
}
