<?php
global $g_config;
$g_config = include 'config.php';

include 'vendor/autoload.php';

include 'HTTP/Request2.php';


/**
 *
 * @param Array $inputParams Input query sring parameters. Check the API reference for key value params to pass : https://msdn.microsoft.com/en-us/library/dn760791.aspx You can pass any numbers of permitted parameters according to the API  in a key value format

 * @param Array $outputParams output keys that you want the function to return. By default, all keys will be returned
 * Check the API reference for key value params to pass : https://msdn.microsoft.com/en-us/library/dn760791.aspx
 * You can pass any numbers of permitted parameters according to the API  in a key format. In addition to that you    * can map the parameters according to the alias you want,
 * So, you pass the below output params to this function :
 * array(
 * 'name',
 * 'contentUrl'
 * )
 * but, you want the function to return 'alt' instead of the actual api param 'name' . So, you can pass the following * array :
 * array(
 *  'name' => 'alt',
 *  'contentUrl'
 * )

 * @param int $noOfResults no of results to return
 * @param int $offset it can be used for pagination of results
 * @return array
 * @throws Exception
 * @throws HTTP_Request2_LogicException
 */
function bingImageSearch( $inputParams, $outputParams,  $noOfResults = 25, $offset = 0 ){

    global $g_config;


    $defInputParams = array(
      'q' => 'New York'
    );


    $finalInputParams = array_merge($defInputParams, $inputParams);

    $defOutputParams = array(

    );

    $tmpOutputParams = array_merge( $defOutputParams, $outputParams );

    $finalOutputParams = array();
    foreach( $tmpOutputParams as $k => $v ){

        $myKey = $k;

        if(is_numeric($myKey)){
            $myKey = $v;
        }

        $finalOutputParams[$myKey] = $v;
    }



    $request = new Http_Request2('https://api.cognitive.microsoft.com/bing/v5.0/images/search');
    $url = $request->getUrl();

    $headers = array(
        // Request headers
        'Content-Type' => 'multipart/form-data',
        'Ocp-Apim-Subscription-Key' => $g_config['bing_search_api_key'],
    );


    $request->setHeader($headers);

    $paramsToSet = array();

    foreach( $finalInputParams as $k=>$v){
        $paramsToSet[$k] = $v;
    }

    if(empty($paramsToSet['count']))
        $paramsToSet['count'] = $noOfResults;

    if(empty($paramsToSet['offset']))
        $paramsToSet['offset'] = $offset;


    $url->setQueryVariables($paramsToSet);

    $request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
    $request->setBody("{body}");

    $return = array();

    try {
        $response = $request->send();

        $tmp = $response->getBody();

        $imgObj = json_decode($tmp);



        if (!empty($imgObj)){

            $imgArr = $imgObj->value;

            foreach ($imgArr as $iaObj) {

                $tmpReturn = array();

                $vars = get_object_vars ( $iaObj );

                if(empty($finalOutputParams)){
                    $return[] = $vars;
                }
                else {

                    foreach ($vars as $k => $v) {


                        if (isset($finalOutputParams[$k])) {

                            $keyAlias = $finalOutputParams[$k];

                            $tmpReturn[$keyAlias] = $v;


                        }


                    }


                    $return[] = $tmpReturn;

                }
            }
        }



        //echo $response->getBody();
    }
    catch (HttpException $ex)
    {
        throw $ex;
    }

    return $return;


}


$bimgsArr = bingImageSearch(array(
    'q' => 'Sudip Das'
),
    array(

        'contentUrl' => 'image_url',
        'hostPageUrl' => 'url',
        'name'

    ),
    2
);

var_dump( $bimgsArr );
die();



?>
