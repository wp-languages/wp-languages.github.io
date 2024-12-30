<?php
$root = dirname( __DIR__ );
require_once $root . '/vendor/autoload.php';

error_reporting( E_ALL );
ini_set( 'memory_limit', '-1' );
date_default_timezone_set( 'Europe/Helsinki' );
set_time_limit( 900 ); // 15min
use WpOrg\Requests\Requests;

if ( ! file_exists( $root . '/vendor/illuminate/collections/helpers.php' ) ) {
    echo "Please install composer dependencies before continuing..." . PHP_EOL;
    exit( 2 );
}
require_once $root . '/vendor/illuminate/collections/helpers.php';

const HOUR_IN_SECONDS = 3600;
const DAY_IN_SECONDS  = HOUR_IN_SECONDS * 24;

/**
 * Request helper.
 *
 * @param string $url Request URL.
 *
 * @return object
 * @throws \JsonException
 */
function req( $url = '' ) {
    $response = Requests::get( $url, [
        'Accept' => 'application/json',
        'Content-Type: application/json',
    ], [
        'timeout'   => 30,
        'useragent' => 'Sorry for traffic! This is curl-bot for: https://wp-languages.github.io.',
    ] );

    if ( ! $response->success ) {
        error(
            'Code: %d / Message: %s',
            [ $response->status_code, $response->body ]
        );
    }

    return json_decode(
        $response->body,
        false,
        512,
        JSON_THROW_ON_ERROR
    );
}

/**
 * Is the cache still fresh enough, or should we refresh the cache.
 *
 * We shouldn't hammer the API's too much. So cache the results
 * and now we can run the queries as many times as we want.
 *
 * @param string $filename   Output filename.
 * @param int    $cache_time Cache length in seconds, 259200 = 3 days.
 *
 * @return bool
 */
function should_refresh_cache( $filename = '', $cache_time = 259200 ) {
    $filename = realpath( $filename );
    $file_age = (int) ( time() - filemtime( $filename ) );

    // echo '(?) File age: ' . $filename . ' = ' . $file_age . PHP_EOL;

    return ! file_exists( $filename ) || $file_age > $cache_time;
}

/**
 * Get Cached JSON as Collection.
 *
 * @param string $filename
 *
 * @return Illuminate\Support\Collection
 */
function get_cached( $filename = '' ) {
    if ( ! file_exists( $filename ) ) {
        error( 'Could not find file: %s', [ $filename ] );
    }

    try {
        $content = json_decode(
            file_get_contents( $filename ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return collect( $content );
    }
    catch ( JsonException $e ) {
        error(
            'Could not open file %s: %s',
            [ $filename, $e->getMessage() ]
        );
    }

    return collect( [] );
}

/**
 * Cache payload.
 *
 * @param \Illuminate\Support\Collection|array $payload  Payload to cache
 * @param string                               $filename Destination filename.
 *
 * @return bool
 * @throws \JsonException
 */
function cache( $payload, $filename ) {
    $encoded = $payload instanceof \Illuminate\Support\Collection
        ? $payload->toJson( JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES )
        : json_encode( $payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES );

    // True if successful, false if couldn't save.
    return file_put_contents( $filename, $encoded ) !== false;
}

/**
 * Get JSON file and return as an array.
 *
 * @param string $filename File to get.
 *
 * @return array
 */
function get_file( $filename = '' ) {
    try {
        $data = (array) json_decode(
            file_get_contents( $filename ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $data;
    }
    catch ( JsonException $e ) {
        error(
            'get_file Error on file %s, %s',
            [ $filename, $e->getMessage() ]
        );
    }

    return [];
}

/**
 * Error message helper.
 *
 * @param string       $msg    sprintf formatted error message.
 * @param array|string $values Array of values for the sprintf placeholders.
 */
function error( $msg = '', $values = [] ) {
    if ( ! is_array( $values ) ) {
        $values = [ $values ];
    }

    die( vsprintf( '[Error] ' . $msg, $values ) . PHP_EOL );
}

/**
 * Echo message.
 *
 * @param string $msg    Message to output.
 * @param string $symbol Prefix symbol.
 */
function msg( $msg = '', $symbol = '*' ) {
    echo sprintf( '(%s) %s%s', $symbol, $msg, PHP_EOL );
}

/**
 * Get file extension.
 *
 * @param string $filename Filename to get extension of.
 *
 * @return false|string
 */
function get_file_extension( $filename = '' ) {
    return substr( strrchr( $filename, '.' ), 1 );
}

/**
 * Build a query param from key-value array.
 *
 * @param array $query_params Query parameters.
 *
 * @return string
 */
function query_params( $query_params = [] ) {
    return collect( $query_params )
        ->map( function ( $val = '', $key = '' ) {
            return trim( $key . '=' . $val, " \t\n\r\0\x0B=" );
        } )
        ->flatten()
        ->join( '&' );
}

/**
 * Switch composer package vendor.
 *
 * @param string $type Package type.
 *
 * @return string
 */
function get_package_vendor( $type = '' ) {
    switch ( $type ) {
        case 'wordpress-plugin-language':
            return 'koodimonni-plugin-language';
        case 'wordpress-theme-language':
            return 'koodimonni-theme-language';
        default:
            return 'koodimonni-language';
    }
}

/**
 * Build Package Helper.
 *
 * @param string $vendor  Package Vendor
 * @param string $name    Package Name
 * @param string $lang    Language Code
 * @param string $version Package Version
 * @param string $package Package Download Url
 *
 * @return array
 */
function build_package( $vendor, $name, $lang, $version, $package, $description ) {
    return [
        'type'    => 'package',
        'package' => [
            'name'        => sprintf(
                '%s/%s-%s',
                $vendor,
                $name,
                $lang,
            ),
            'type'        => 'wordpress-language',
            'keywords'    => [ 'WordPress', 'Translation', $name, $lang ],
            'description' => $description,
            'version'     => $version,
            'dist'        => [
                'url'  => $package,
                'type' => get_file_extension( $package ),
            ],
            'require'     => [
                'koodimonni/composer-dropin-installer' => '>=0.2.3',
            ],
        ],
    ];
}
