<?php
/**
 * Created by PhpStorm.
 * User: iosrd
 * Date: 01/10/2018
 * Time: 21:16
 */

class HttpUtiles
{
    public static function http_get_request($baseurl = "", $path_and_query = "")
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt($curl, CURLOPT_URL, $baseurl.'/'.$path_and_query);
        curl_setopt($curl, CURLOPT_HEADER,  array("content-type" => "application/json"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Send the request & save response to $resp
        $http_response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return $http_response;
    }

    public static function http_post_request($baseurl = "", array $params) {
        $query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, array("content-type" => "application/json"));
        curl_setopt($ch, CURLOPT_URL, $baseurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $http_response = curl_exec($ch); //json result expected
        curl_close($ch);
        return $http_response;
    }
}