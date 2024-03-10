<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use IslamicNetwork\PrayerTimes\PrayerTimes;
use Illuminate\Support\Facades\Http;



class NamazController extends Controller
{
    public function permissionView()
    {
        return view('permission-view');
    }

    public function geoLocationPrayerTimes(request $request)
    {
        define('LATITUDE_ADJUSTMENT_METHOD_ANGLE', 'Angle');
        define('TIME_FORMAT_24H', '24H');

        $validation = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);



        $timezone = 'Asia/Dhaka';
        $latitude = $validation['latitude'];
        $longitude = $validation['longitude'];

        $pt = new PrayerTimes('KARACHI'); //https://github.com/islamic-network/prayer-times/blob/master/src/PrayerTimes/Method.php
        $pt->tune($imsak = 5, $fajr = 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);


        $geoData =
            [
                'country' => 'Bangladesh',
                'district' => '',
                'city' => '',
            ];
        // for today
        $times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = TIME_FORMAT_24H);
        return view('prayer-times', compact('times', 'geoData'));
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
        $pt->tune($imsak = 0, $fajr = 0, $sunrise = 0, $dhuhr = 0, $asr = 0, $maghrib = 0, $sunset = 0, $isha = 0, $midnight = 0);


        $geoData =
            [
                'country' => 'BD',
                'district' => ucfirst($districtName),
            ];
        // for today
        $times = $pt->getTimesForToday($latitude, $longitude, $timezone, $elevation = null, $latitudeAdjustmentMethod = LATITUDE_ADJUSTMENT_METHOD_ANGLE, $midnightMode = null, $format = TIME_FORMAT_24H);
        return view('prayer-times', compact('times', 'geoData'));
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
}
