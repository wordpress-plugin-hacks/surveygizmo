<?php

/**
 * data returned from calls is split and stored in two variables: status and data
 *
 * Class SgApi
 */
class SgApi {

    /**
     * @var
     */
    protected $account;

    /**
     * @var
     */
    protected $token;

    /**
     * @var
     */
    protected $secret;

    /**
     * @var string
     */
    protected $version = "v4";

    /**
     * @var string
     */
    protected $url = "https://restapi.surveygizmo.com/";

    /**
     * @var int
     */
    protected $resultsperpage = 100;

    /**
     * @var string
     */
    protected $format = ".json";

    /**
     * @var
     */
    protected $filter;

    /**
     * @var
     */
    protected $sid;

    /**
     * IDs are entered by the user via the setIDs function
     *
     * @var array
     */
    var $ids = array(
        "survey"         => NULL,
        "surveypage"     => NULL,
        "surveyquestion" => NULL,
        "surveyresponse" => NULL,
        "surveyreport"   => NULL,
        "surveycampaign" => NULL,
        "surveyoption"   => NULL,
        "emailmessage"   => NULL,
        "accountuser"    => NULL,
        "contact"        => NULL
    );


    /**
     * a map of the API, so that it can build calls based on a single given object
     *
     * @var array
     */
    var $map = array(
        "survey"          => array( "survey" ),
        "surveypage"      => array( "survey", "surveypage" ),
        "surveyquestion"  => array( "survey", "surveyquestion" ),
        "surveyresponse"  => array( "survey", "surveyresponse" ),
        "surveyreport"    => array( "survey", "surveyreport" ),
        "surveycampaign"  => array( "survey", "surveycampaign" ),
        "surveyoption"    => array( "survey", "surveyquestion", "surveyoption" ),
        "surveystatistic" => array( "survey", "surveystatistic" ),
        "emailmessage"    => array( "survey", "surveycampaign", "emailmessage" ),
        "account"         => array( "account" ),
        "accountuser"     => array( "accountuser" ),
        "contact"         => array( "survey", "surveycampaign", "contact" )
    );

    /**
     * restapi constructor.
     * @param $token
     * @param $secret
     * @param $version
     * @param $resultsperpage
     * @param $url
     */
    public function __construct( $token, $secret, $resultsperpage, $sid )
    {
        $this->token = $token;
        $this->secret = $secret;
        $this->resultsperpage = $resultsperpage;
        $this->sid = $sid;
        $this->version;
        $this->url;
    }

    /**
     * @return string
     */
    public function get_url()
    {
       return $this->url; 
    }

    /**
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * used by the class to create the API call
     *
     * @param $object
     * @param string $slug
     * @param $method
     * @return string
     */
    public function buildURL( $object, $slug = '', $method )
    {
        $object = strtolower( $object );

        foreach ( $this->map[$object] as $obj )
        {
            if ( $this->ids[$obj] != NULL )
            {
                $slug .= $obj."/".$this->ids[$obj]."/";
            }else{
                $slug .= $obj."/";
                continue;
            }
        }

        $call = "{$this->url}{$this->version}/{$slug}{$this->format}?api_token={$this->token}&api_token_secret{$this->secret}&resultsperpage={$this->resultsperpage}{$this->filter}";


        //var_dump($call);
        // if ($method == "get") {
        // 	return $call;
        // }
        if ( $method == "put" )
        {
            if ( !is_array( $opts ) )
                die( "Error: PUT options not found." );

            foreach ( $opts as $key => $value )
                $string .= "&{$key}={$value}";
            //$call .= "&_method=put".$string;
        }
        return $call;
    }


    /**
     * used to set the IDs of objects. $ids must be an associative array
     *
     * @param $ids
     * @return array|string
     */
    public function setIDs( $ids )
    {
        if ( !is_array( $ids ) )
            return "Error: \$ids must be an array";

        foreach ( $ids as $key=>$value )
            $this->ids[$key] = $value;

        return $this->ids;
    }

    public function setFilter( $filterArr, $andOr="and" )
    {
        $i = 0;
        foreach ( $filterArr as $value )
        {
            $this->filter .= "&filter[field][$i]={$value[0]}&filter[operator][$i]={$value[1]}&filter[value][$i]={$value[2]}";
            $i++;
        }
        $this->filter .= "&filter_conjunction={$andOr}";

        return $this->filter;
    }

    /**
     * used to call an API object and return data (get and getList)
     *
     * @param $object
     * @param null $ids
     * @return array
     */
    public function get( $object, $ids=NULL )
    {
        if ( $ids != NULL )
            $this->setIDs( $ids );

        $this->data = array();
        $i = 0;
        do {
            $page = $i+1;
            $url = $this->buildURL( $object, $slug = '', $method='get' )."&page={$page}";

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $ch, CURLOPT_ENCODING, "" );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
            curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
            curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
            $files[$i] = curl_exec( $ch );
            $response = curl_getinfo( $ch );
            curl_close ( $ch );
            $json = json_decode( $files[$i] );

            $totalPages = ( isset( $json->total_pages ) ) ? $json->total_pages : 1;

            if ( isset( $json->page ) ) {
                if ( $json->page == 1 || !$json->page )
                    $this->status = array(
                        "result_ok" => $json->result_ok,
                        "total_count" => $json->total_count,
                        "total_pages" => $json->total_pages,
                        "results_per_page" => $json->results_per_page
                    );
            }

            if ( $this->ids[$object] == NULL )
                foreach ( $json->data as $data )
                {
                    if ( !is_array( $json->data ) )
                    {
                        $this->data = $json->data;
                        continue;
                    }
                    array_push( $this->data, $data );
                }
            else
                $this->data = $json->data;

            $i++;

        } while ( $i < $totalPages );

        return $this->data;
    }


    /**
     * use this function to "put" an object
     * requires opts to be passed as an associative array, and must contain values for "type" and "name"
     *
     * @param $object
     * @param $opts
     * @param null $ids
     * @return array|mixed|object
     */
    public function put( $object, $opts, $ids=NULL )
    {
        if ( $ids != NULL ) {
            $this->setIDs( $ids );
        }
        $this->qstring = "&_method=put";

        foreach ( $opts as $key=>$value )
            $this->qstring .= "&".$key."=".urlencode( $value );

        $url = @$this->buildURL( $object ) . $this->qstring;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );
        $file = curl_exec( $ch );
        $response = curl_getinfo( $ch );
        curl_close ( $ch );
        $json = json_decode($file);
        foreach ($json as $field=>$return)
            if ($field != "data")
                $this->status[$field] = $return;

        $this->data = $json->data;
        return $json;
    }

    /**
     * deletes the specified object
     *
     * @param $object
     * @param null $ids
     * @return mixed
     */
    public function delete( $object, $ids=NULL )
    {
        if ( $ids != NULL )
            $this->setIDs( $ids );

        $this->qstring = "&_method=delete";
        $url = $this->buildURL( $object ) . $this->qstring;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $file = curl_exec( $ch );
        $response = curl_getinfo( $ch );
        curl_close ( $ch );

        $json = json_decode( $file );


        foreach ( $json as $field=>$return )
            if ( $field != "data" )
                $this->status[$field] = $return;

        $this->data = $json->data;
        return $this->status;
    }


    /**
     * use this function to "post" an object
     * uses opts (an associative array)
     *
     * @param $object
     * @param $opts
     * @param null $ids
     * @return mixed
     */
    public function post( $object, $opts, $ids=NULL )
    {
        if ( $ids != NULL )
            $this->setIDs( $ids );

        $this->qstring = "&_method=post";
        foreach ( $opts as $key=>$value )
            $this->qstring .= "&{$key}={$value}";

        $url = $this->buildURL( $object ) . $this->qstring;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 15 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $file = curl_exec( $ch );
        $response = curl_getinfo( $ch );
        curl_close ( $ch );

        $json = json_decode( $file );

        foreach ( $json as $field=>$return )
            if ( $field != "data" )
                $this->status[$field] = $return;

        $this->data = $json->data;

        return $this->status;
    }
}