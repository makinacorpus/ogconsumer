<?php

namespace OgConsumer;

/**
 * Supposedly fast fetcher using cURL
 */
class CurlFetcher implements FetcherInterface
{
    /**
     * Default cURL options
     */
    static protected $curlOptions = array(
        CURLOPT_FAILONERROR    => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => "OgConsumer",
    );

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetch()
     */
    public function fetch($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, self::$curlOptions);
        $output = curl_exec($ch);
        curl_close($ch);

        if (empty($output)) {
            throw new \RuntimeException(sprintf(
                "Could not fetch content using cURL from: %s", $url));
        }

        return $output;
    }

    /**
     * (non-PHPdoc)
     * @see \OgConsumer\FetcherInterface::fetchAll()
     */
    public function fetchAll(array $urlList)
    {
        $ret         = array();
        $resList     = array();
        $resMap      = array();

        // Initialize all cURL handlers
        $mh = curl_multi_init();
        foreach ($urlList as $key => $url) {
            $ch = curl_init($url);

            curl_setopt_array($ch, self::$curlOptions);
            curl_multi_add_handle($mh, $ch);

            $resList[(int)$ch] = $ch;
            $resMap[(int)$ch]  = $key;
        }

        do {
            // Execute all requests until something happens
            $stillRunning = true;
            do {
                $status = curl_multi_exec($mh, $stillRunning);
           } while (CURLM_CALL_MULTI_PERFORM === $status);

            if (CURLM_OK !== $status) {
                // An error happend
                break;
            }

            // Previous while() tells us we have results ready, find all of
            // them and do whatever we have to do with it
            while ($info = curl_multi_info_read($mh)) {
                $ch  = $info['handle'];
                $key = $resMap[(int)$ch];

                $ret[$key] = curl_multi_getcontent($ch);

                // Close request as they are dealt with
                curl_multi_remove_handle($mh, $ch);
                unset($resList[(int)$ch]);
            }

        } while ($stillRunning);

        // Close everything that has not been closed (in case of any error)
        if (!empty($resList)) {
            foreach ($resList as $ch) {
                curl_multi_remove_handle($mh, $ch);
                $ret[$resMap[(int)$ch]] = false;
            }
        }
        curl_multi_close($mh);

        return $ret;
    }
}
