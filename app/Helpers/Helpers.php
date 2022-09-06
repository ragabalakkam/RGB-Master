<?php

# models
use App\Models\Locations\City;
use App\Models\Locations\Country;
use App\Models\Locations\District;
use App\Models\Locations\State;
use App\Models\User;
use App\Models\Configuration;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Validations

function is_image($file)
{
    if (!!$file && is_file($file) && in_array(substr(finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file), 6), array('jpeg', 'jpg', 'png', 'jpg')))
        return true;

    return false;
}

function isNull($value)
{
    return is_null($value) || $value == 'null' || $value == '';
}

function castNull($value)
{
    return isNull($value) ? null : $value;
}

function is_valid_address($address)
{
    return is_array($address) && count(array_filter($address)) == 7;
}

function is_valid_location($location)
{
    return is_array($location) && count(array_filter($location)) == 2;
}

function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return substr($haystack, 0, $length) === $needle;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if (!$length) return true;
    return substr($haystack, -$length) === $needle;
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Parsers & Text-Emphasis

function clean($string) {
    $string = str_replace(' ', '_', $string);
    return preg_replace('/[^A-Za-z0-9\-\_]/', '', $string);
}

function normalize($string, $default = null)
{
    $result = str_replace(' ', '', $string);
    $result = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $result));
    return $result ? $result : $default;
}

function normalize_email($email)
{
    if (!$email || !strlen($email))
        return null;

    $emailParts = explode('@', $email);
    return strtolower(str_replace('.', '', ($emailParts[0] ?? '')) . '@' . ($emailParts[1] ?? ''));
}

function normalize_username($username)
{
    $result = str_replace(' ', '', $username);
    $result = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $result));
    return $result ? $result : 'user';
}

function normalize_domain($domain)
{
    return str_replace('rgbksaco.com', '', str_replace('https://', '', $domain));
}

function pick_username($name)
{
    $username = normalize_username($name);
    while (User::withTrashed()->where('username', $username)->count()) {
        $username .= rand(1, 9999);
    }
    return $username;
}

function get_current_row($Model)
{
    $request = explode("/", $_SERVER["REDIRECT_URL"]);
    return $Model::find(intval($request[count($request) - 1])) ?? null;
}

function parseName($name, $lang = 'ar')
{
    return isNull($name) ? null : (is_string($name) ? json_decode($name) : (is_array($name) ? json_decode(json_encode($name)) : $name))->{$lang};
}

function getFullAddress($address = null, $separator = ['en' => ', ', 'ar' => ' ، '], $order = ['country', 'state', 'city', 'district', 'street', 'building_number', 'postal_code'])
{
    if (!$address || !count(array_filter($address)))
        return null;

    $data = [];
    $arr = [null, null];

    foreach ($order as $part) {
        $last = count($data);
        switch ($part) {
            case 'country':
                if ($country = Country::find($address[0]))
                {
                    $data[] = $country->name;
                }
                break;
            case 'state':
                if ($state = State::find($address[1]))
                {
                    $data[] = $state->name;
                    $data[$last]['en'] = en('stateX', ['x' => $data[$last]['en']]);
                    $data[$last]['ar'] = ar('stateX', ['x' => $data[$last]['ar']]);
                }
                break;
            case 'city':
                if ($city = City::find($address[2]))
                {
                    $data[] = $city->name;
                    $data[$last]['en'] = en('cityX', ['x' => $data[$last]['en']]);
                    $data[$last]['ar'] = ar('cityX', ['x' => $data[$last]['ar']]);
                }
                break;
            case 'district':
                if ($district = District::find($address[3]))
                {
                    $data[] = $district->name;
                    $data[$last]['en'] = en('districtX', ['x' => $data[$last]['en']]);
                    $data[$last]['ar'] = ar('districtX', ['x' => $data[$last]['ar']]);
                }
                break;
            case 'street':
                if ($address[4])
                {
                    $data[] = [
                        'en' => en('streetX', ['x' => $address[4]]),
                        'ar' => ar('streetX', ['x' => $address[4]])
                    ];
                }
                break;
            case 'building_number':
                if ($address[5])
                {
                    $data[] = [
                        'en' => en('buildingX', ['x' => $address[5]]),
                        'ar' => ar('buildingX', ['x' => $address[5]]),
                    ];
                }
                break;
            case 'postal_code':
                if ($address[6])
                {
                    $data[] = [
                        'en' => en('postalCodeX', ['x' => $address[6]]),
                        'ar' => ar('postalCodeX', ['x' => $address[6]]),
                    ];
                }
                break;
        }
    }    

    foreach (['en', 'ar'] as $key => $lang) {
        $arr[$key] = implode($separator[$lang], array_map(function ($el) use ($lang) {
            return $el[$lang];
        }, $data));
    }

    return localize($arr);
}

function firstName($name)
{
    return ucfirst(explode(' ', $name)[0]);
}

function capitalize($text, $separator = ' ')
{
    $is_string = is_string($text);
    $text = $is_string ? [$text] : $text;

    foreach ($text as $key => $value) {

        if ($key === "ar") continue;

        $value = explode($separator, $value);

        foreach ($value as $i => $word) {
            $value[$i] = ucfirst(mb_strtolower($word));
        }

        $text[$key] = implode($separator, $value);
    }

    return $is_string ? $text[0] : $text;
}

function pad($value, $count = 3)
{
    $pads = $count - strlen(strVal($value));
    return str_repeat('0', $pads >= 0 ? $pads : 0) . strVal($value);
}

function rand_digits($digits)
{
    return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
}

function castBarcode($val)
{
    return strtoupper(filter_var($val, FILTER_SANITIZE_SPECIAL_CHARS));
}

function arNum($num)
{
    $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
    return str_replace($standard, $eastern_arabic_symbols, $num);
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Time

function ArabicDate($date = null)
{
    if (!$date) 
        $date = date('y-m-d');

    # months
    $months = [
        "Jan" => "يناير",
        "Feb" => "فبراير",
        "Mar" => "مارس",
        "Apr" => "أبريل",
        "May" => "مايو",
        "Jun" => "يونيو",
        "Jul" => "يوليو",
        "Aug" => "أغسطس",
        "Sep" => "سبتمبر",
        "Oct" => "أكتوبر",
        "Nov" => "نوفمبر",
        "Dec" => "ديسمبر"
    ];
    $ar_month = $months[date("M", strtotime($date))];

    # days
    $days = [
        "Sat" => "السبت",
        "Sun" => "الأحد",
        "Mon" => "الإثنين",
        "Tue" => "الثلاثاء",
        "Wed" => "الأربعاء",
        "Thu" => "الخميس",
        "Fri" => "الجمعة",
    ];
    $ar_day = $days[date('D', strtotime($date))];

    return arNum($ar_day . ' ' . date('d', strtotime($date)) . ' / ' . $ar_month . ' / ' . date('Y', strtotime($date)));
}

function castTime($datetime, $sep = '-')
{
    $format = "Y{$sep}m{$sep}d h:i";
    return is_a($datetime, DateTimeInterface::class) ? date_format($datetime, $format) : date($format, strtotime($datetime));
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function calc_ratio($total, $ratio)
{
    return $total * ($ratio / 100);
}

function localize($arr)
{
    return ['ar' => $arr[1], 'en' => $arr[0]];
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Seeding

function random($collection)
{
    return $collection->random(1)->first();
}

function random_id($collection)
{
    return random($collection)->id;
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# App general information getters

function getGeneralInformation($key = null)
{
    return getConfig('organization')[$key] ?? null;
}

function getAppName()
{
    return getGeneralInformation('org_name');
}

function getAppLogo()
{
    return getGeneralInformation('logo');
}

function getAppEmail()
{
    return getGeneralInformation('email');
}

function getAppSlogan()
{
    return getGeneralInformation('slogan');
}

function getOption()
{
    return true;
}


#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Sales Settings getters

function getSalesSetting($name)
{
    return getConfig('sales_settings')[$name] ?? getConfig($name) ?? null;
}

function getTaxNumber()
{
    return hasModule('taxPayer') ? getSalesSetting('tax_number') : null;
}

function getSalesTaxRatio()
{
    return hasModule('taxPayer') ? getSalesSetting('sales_tax_ratio') : 0;
}

function getPurchasesTaxRatio()
{
    return hasModule('taxPayer') ? getSalesSetting('purchases_tax_ratio') : 0;
}

function getMaxDiscountRatio()
{
    return getSalesSetting('max_discount_ratio');
}

function getBusinessType()
{
    $config = Configuration::where('key', 'business_type')->first();
    return $config ? $config->value : 'commercial';
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# test

function print_collection($arr)
{
    echo '<pre>';
    print_r($arr->toArray());
    echo '</pre>';
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function generate_random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# excution-time

function get_execution_time($callback)
{
    $start = microtime(true);
    $callback();
    return microtime(true) - $start;
}

function calcTime(&$time)
{
    $old_time = $time;
    $time = microtime(true);
    return round($time - $old_time, 2);
}