<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pas_Edina
 *
 * @author danielpett
 */
class Pas_Edina {

    const UNLOCK_URI = 'http://unlock.edina.ac.uk/ws/';

    /** Set up the cache
        *
        * @var type
        */
    protected $_cache;

    protected $_userAgent;

    protected $_formats = array('json', 'kml', 'xml', 'txt');

    public function __construct(){

    $frontendOptions = array(
        'lifetime' => 31556926,
        'automatic_serialization' => true
        );
    $backendOptions = array('cache_dir' => 'app/cache/edina');
    $this->_cache = Zend_Cache::factory(
            'Output',
            'File',
            $frontendOptions,
            $backendOptions)
            ;
    }

    /** Perform a curl request based on url provided
    * @access public
    * @uses Zend_Cache
    * @uses Zend_Json_Decoder
    * @param string $method
    * @param array $params
    */
    public function get($method, array $params) {
    $url = $this->createUrl($method, $params);
    if (!($this->_cache->test(md5($url)))) {
    $config = array(
    'adapter'   => 'Zend_Http_Client_Adapter_Curl',
    'curloptions' => array(
    CURLOPT_POST =>  true,
    CURLOPT_USERAGENT =>  $this->_userAgent,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_LOW_SPEED_TIME => 1,
    ),
    );

    $client = new Zend_Http_Client($url, $config);
    $response = $client->request();

    $data = $response->getBody();
    $this->_cache->save($data);
    } else {
    $data = $this->_cache->load(md5($url));
    }
    return Zend_Json_Decoder::decode($data, Zend_Json::TYPE_OBJECT);
    }

    /** Build a url string
        * @param string $method The method to use
        * @param array $params
        */
    public function createUrl($method, array $params){
    if(is_array($params)){
    return self::MAP_IT_URI . $method . http_build_query($params, null, '&');
    } else {
        throw new Pas_Edina_Exception('Parameters have to be an array',500);
    }
    }

    public function userAgent(){
    $useragent = new Zend_Http_UserAgent();
    $this->_userAgent = $useragent->getUserAgent();
    return $this->_userAgent;
    }
}
