<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AppService extends Facade
{
    /**
     * The IoC key accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'appservice';
    }
	
	
	public static function getBrowserInfo ()
    {
        $u_agent = \Request::header('user-agent');
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";
        $ub = "";
        //First get the platform?
        if (preg_match('/linux/i', $u_agent))
        {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent))
        {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent))
        {
            $platform = 'windows';
        }
        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif (preg_match('/Firefox/i', $u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif (preg_match('/OPR/i', $u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif (preg_match('/Chrome/i', $u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif (preg_match('/Safari/i', $u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif (preg_match('/Netscape/i', $u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        // finally get the correct version number
        $known = array(
            'Version',
            $ub,
            'other');
        $pattern = '#(?<browser>'.join('|', $known).
                ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches))
        {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        $version = null;
        if ($i != 1)
        {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub))
            {
                $version = $matches['version'][0];
            }
            else if (isset($matches['version'][1]))
            {
                $version = $matches['version'][1];
            }
        }
        else
        {
            $version = $matches['version'][0];
        }
        // check if we have a number
        if ($version == null || $version == "")
        {
            $version = "?";
        }
        return array(
            'userAgent'=>$u_agent,
            'name'=>$bname,
            'version'=>$version,
            'platform'=>$platform,
            'pattern'=>$pattern
        );
        return true;
    }
	
	public static function slug ($text)
    {
        //replace non letter or digits by (_)
        $text = preg_replace("/\W|_/", '_', $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, '_')); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }
	
	
	public static function getTransID ($profix)
    {
        $function_ret = '';
        $iLoop = true;
        $disp = self::rKeyGen(3, 1);
        $profix = $profix.date('dmYHis');
        $disp1 = $disp.$profix;
        return $disp1;
    }

    public static function  rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }        
        return $key;
    }

    public static function  rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
// Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
// Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
// Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

// Function to get the client IP address
    public function get_client_ip ()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
	
	
	public static function validate_string ($str)
    {
        return (preg_match("/^[a-zA-Z0-9\s]+$/", $str)) ? true : false;
    }	
	
	public static  function decimal_places ($amt)
    {
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
        }
        return $decimal_places;
    }
	
	public function validate_password ($str)
    {
        return preg_match("/^[a-zA-Z0-9 .,:;{}\|!@#$%^&*()-_+=?><\s]+$/", $str);
    }

    //Convert Number To Text
    public static function convertNumber ($number)
    {
        $number = str_replace(',', '', $number);
        list($integer, $fraction) = explode(".", (string) $number);
        $output = "";
        if ($integer{0} == "-")
        {
            $output = "negative ";
            $integer = ltrim($integer, "-");
        }
        else if ($integer{0} == "+")
        {
            $output = "positive ";
            $integer = ltrim($integer, "+");
        }
        if ($integer{0} == "0")
        {
            $output .= "zero";
        }
        else
        {
            $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
            $group = rtrim(chunk_split($integer, 3, " "), " ");
            $groups = explode(" ", $group);
            $groups2 = array();
            foreach ($groups as $g)
            {
                $groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});
            }
            for ($z = 0; $z < count($groups2); $z++)
            {
                if ($groups2[$z] != "")
                {
                    $output .= $groups2[$z].$this->convertGroup(11 - $z).(
                            $z < 11 && !array_search('', array_slice($groups2, $z + 1, -1)) && $groups2[11] != '' && $groups[11]{0} == '0' ? " and " : ", "
                            );
                }
            }
            $output = rtrim($output, ", ");
        }
        if ($fraction > 0)
        {
            $output .= " point";
            for ($i = 0; $i < strlen($fraction); $i++)
            {
                $output .= " ".$this->convertDigit($fraction{$i});
            }
        }
        return $output;
    }

    public static function convertGroup ($index)
    {
        switch ($index)
        {
            case 11:
                return " decillion";
            case 10:
                return " nonillion";
            case 9:
                return " octillion";
            case 8:
                return " septillion";
            case 7:
                return " sextillion";
            case 6:
                return " quintrillion";
            case 5:
                return " quadrillion";
            case 4:
                return " trillion";
            case 3:
                return " billion";
            case 2:
                return " million";
            case 1:
                return " thousand";
            case 0:
                return "";
        }
    }

    public static function convertThreeDigit ($digit1, $digit2, $digit3)
    {
        $buffer = "";
        if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
        {
            return "";
        }
        if ($digit1 != "0")
        {
            $buffer .= $this->convertDigit($digit1)." hundred";
            if ($digit2 != "0" || $digit3 != "0")
            {
                $buffer .= " and ";
            }
        }
        if ($digit2 != "0")
        {
            $buffer .= $this->convertTwoDigit($digit2, $digit3);
        }
        else if ($digit3 != "0")
        {
            $buffer .= $this->convertDigit($digit3);
        }
        return $buffer;
    }

    public static function convertTwoDigit ($digit1, $digit2)
    {
        if ($digit2 == "0")
        {
            switch ($digit1)
            {
                case "1":
                    return "ten";
                case "2":
                    return "twenty";
                case "3":
                    return "thirty";
                case "4":
                    return "forty";
                case "5":
                    return "fifty";
                case "6":
                    return "sixty";
                case "7":
                    return "seventy";
                case "8":
                    return "eighty";
                case "9":
                    return "ninety";
            }
        }
        else if ($digit1 == "1")
        {
            switch ($digit2)
            {
                case "1":
                    return "eleven";
                case "2":
                    return "twelve";
                case "3":
                    return "thirteen";
                case "4":
                    return "fourteen";
                case "5":
                    return "fifteen";
                case "6":
                    return "sixteen";
                case "7":
                    return "seventeen";
                case "8":
                    return "eighteen";
                case "9":
                    return "nineteen";
            }
        }
        else
        {
            $temp = $this->convertDigit($digit2);
            switch ($digit1)
            {
                case "2":
                    return "twenty-$temp";
                case "3":
                    return "thirty-$temp";
                case "4":
                    return "forty-$temp";
                case "5":
                    return "fifty-$temp";
                case "6":
                    return "sixty-$temp";
                case "7":
                    return "seventy-$temp";
                case "8":
                    return "eighty-$temp";
                case "9":
                    return "ninety-$temp";
            }
        }
    }
	
}
