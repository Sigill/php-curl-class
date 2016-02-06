<?php
namespace Helper;

use Curl\Curl;

class Test
{
    const TEST_URL = 'http://127.0.0.1:8000/';
    const ERROR_URL = 'https://1.2.3.4/';

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function server($test, $request_method, $query_parameters = array(), $data = array())
    {
        $this->curl->setHeader('X-DEBUG-TEST', $test);
        $request_method = strtolower($request_method);
        if (is_array($data) && empty($data)) {
            $this->curl->$request_method(self::TEST_URL, $query_parameters);
        } else {
            $this->curl->$request_method(self::TEST_URL, $query_parameters, $data);
        }
        return $this->curl->response;
    }
}

function test($instance, $before, $after)
{
    $instance->server('request_method', $before);
    \PHPUnit_Framework_Assert::assertEquals($before, $instance->curl->responseHeaders['X-REQUEST-METHOD']);
    $instance->server('request_method', $after);
    \PHPUnit_Framework_Assert::assertEquals($after, $instance->curl->responseHeaders['X-REQUEST-METHOD']);
}

function create_png()
{
    // PNG image data, 1 x 1, 1-bit colormap, non-interlaced
    ob_start();
    imagepng(imagecreatefromstring(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')));
    $raw_image = ob_get_contents();
    ob_end_clean();
    return $raw_image;
}

function create_tmp_file($data)
{
    $tmp_file = tmpfile();
    fwrite($tmp_file, $data);
    rewind($tmp_file);
    return $tmp_file;
}

function get_png()
{
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, create_png());
    return $tmp_filename;
}

if (function_exists('finfo_open')) {
    function mime_type($file_path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime_type;
    }
} else {
    function mime_type($file_path)
    {
        $mime_type = mime_content_type($file_path);
        return $mime_type;
    }
}

// https://gist.github.com/inxilpro/6320414
if (!function_exists('http_response_code')) {
    function http_response_code($code = null)
    {
        static $defaultCode = 200;

        if (null != $code) {
            switch ($code) {
            case 100: $text = 'Continue'; break;                        // RFC2616
            case 101: $text = 'Switching Protocols'; break;             // RFC2616
            case 102: $text = 'Processing'; break;                      // RFC2518

            case 200: $text = 'OK'; break;                              // RFC2616
            case 201: $text = 'Created'; break;                         // RFC2616
            case 202: $text = 'Accepted'; break;                        // RFC2616
            case 203: $text = 'Non-Authoritative Information'; break;   // RFC2616
            case 204: $text = 'No Content'; break;                      // RFC2616
            case 205: $text = 'Reset Content'; break;                   // RFC2616
            case 206: $text = 'Partial Content'; break;                 // RFC2616
            case 207: $text = 'Multi-Status'; break;                    // RFC4918
            case 208: $text = 'Already Reported'; break;                // RFC5842
            case 226: $text = 'IM Used'; break;                         // RFC3229

            case 300: $text = 'Multiple Choices'; break;                // RFC2616
            case 301: $text = 'Moved Permanently'; break;               // RFC2616
            case 302: $text = 'Found'; break;                           // RFC2616
            case 303: $text = 'See Other'; break;                       // RFC2616
            case 304: $text = 'Not Modified'; break;                    // RFC2616
            case 305: $text = 'Use Proxy'; break;                       // RFC2616
            case 306: $text = 'Reserved'; break;                        // RFC2616
            case 307: $text = 'Temporary Redirect'; break;              // RFC2616
            case 308: $text = 'Permanent Redirect'; break;              // RFC-reschke-http-status-308-07

            case 400: $text = 'Bad Request'; break;                     // RFC2616
            case 401: $text = 'Unauthorized'; break;                    // RFC2616
            case 402: $text = 'Payment Required'; break;                // RFC2616
            case 403: $text = 'Forbidden'; break;                       // RFC2616
            case 404: $text = 'Not Found'; break;                       // RFC2616
            case 405: $text = 'Method Not Allowed'; break;              // RFC2616
            case 406: $text = 'Not Acceptable'; break;                  // RFC2616
            case 407: $text = 'Proxy Authentication Required'; break;   // RFC2616
            case 408: $text = 'Request Timeout'; break;                 // RFC2616
            case 409: $text = 'Conflict'; break;                        // RFC2616
            case 410: $text = 'Gone'; break;                            // RFC2616
            case 411: $text = 'Length Required'; break;                 // RFC2616
            case 412: $text = 'Precondition Failed'; break;             // RFC2616
            case 413: $text = 'Request Entity Too Large'; break;        // RFC2616
            case 414: $text = 'Request-URI Too Long'; break;            // RFC2616
            case 415: $text = 'Unsupported Media Type'; break;          // RFC2616
            case 416: $text = 'Requested Range Not Satisfiable'; break; // RFC2616
            case 417: $text = 'Expectation Failed'; break;              // RFC2616
            case 422: $text = 'Unprocessable Entity'; break;            // RFC4918
            case 423: $text = 'Locked'; break;                          // RFC4918
            case 424: $text = 'Failed Dependency'; break;               // RFC4918
            case 426: $text = 'Upgrade Required'; break;                // RFC2817
            case 428: $text = 'Precondition Required'; break;           // RFC6585
            case 429: $text = 'Too Many Requests'; break;               // RFC6585
            case 431: $text = 'Request Header Fields Too Large'; break; // RFC6585

            case 500: $text = 'Internal Server Error'; break;           // RFC2616
            case 501: $text = 'Not Implemented'; break;                 // RFC2616
            case 502: $text = 'Bad Gateway'; break;                     // RFC2616
            case 503: $text = 'Service Unavailable'; break;             // RFC2616
            case 504: $text = 'Gateway Timeout'; break;                 // RFC2616
            case 505: $text = 'HTTP Version Not Supported'; break;      // RFC2616
            case 506: $text = 'Variant Also Negotiates'; break;         // RFC2295
            case 507: $text = 'Insufficient Storage'; break;            // RFC4918
            case 508: $text = 'Loop Detected'; break;                   // RFC5842
            case 510: $text = 'Not Extended'; break;                    // RFC2774
            case 511: $text = 'Network Authentication Required'; break; // RFC6585

            default:
                $code = 500;
                $text = 'Internal Server Error';
            }

            $defaultCode = $code;

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
        }

        return $defaultCode;
    }
}
