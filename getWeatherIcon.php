<?php
//===================Get weather data======================================
function getWeather($city,$query_type="weather",$api_append="") {
    if(get_transient( $city.$query_type.$api_append ))
        return get_transient( $city.$query_type.$api_append );
    $city = urlencode($city);
    $weather_api_response = wp_remote_get( "http://api.openweathermap.org/data/2.5/{$query_type}?q={$city}&APPID=af60c4fdd1bca77a3d2b036828c809a9" . $api_append );
    $weather_data_json = wp_remote_retrieve_body( $weather_api_response );
    $weather_data = json_decode($weather_data_json,true);
    if(!empty($weather_data['cod']) && $weather_data['cod'] == '200') {
        set_transient( $city.$query_type.$api_append , $weather_data );
        return $weather_data;
    }else
        return false;
}

function getWeatherIcon($city,$wi = false,$weather_data=null) {
    if(!$weather_data) {
        $weather_data = getWeather($city);
        $weather_data = $weather_data['weather'][0]['main'];
    }
    if($weather_data) {
        if($wi) {
            switch($weather_data) {
                case 'Clouds': return 'wi wi-cloudy';  break;
                case 'Rain':   return 'wi wi-showers'; break;
                case 'Clear':  return 'wi wi-day-sunny fa-spin'; break;
                case 'Haze':   return 'wi wi-fog';     break;
                case 'Mist':   return 'wi wi-mist';    break;
                case 'Snow':   return 'wi wi-snow';    break;
                default:       return 'wi wi-day-sunny fa-spin';
            }
        }
        return "http://openweathermap.org/img/w/{$weather_data['weather'][0]['icon']}.png";
    }
    return FALSE;
}

?>