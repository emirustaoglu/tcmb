<?php

namespace emirustaoglu\tcmb;

use emirustaoglu\filecache;
use emirustaoglu\tcmb\Exception\ConnectionFailed;
use emirustaoglu\tcmb\Exception\UnknownCurrencyCode;

class Doviz
{
    private $tcmbUrl = "https://www.tcmb.gov.tr/kurlar/";

    private array $dovizKurlari = array(
        'USD' => 'ABD DOLARI',
        'AUD' => 'AVUSTRALYA DOLARI',
        'DKK' => 'DANİMARKA KRONU',
        'EUR' => 'EURO',
        'GBP' => 'İNGİLİZ STERLİNİ',
        'CHF' => 'İSVİÇRE FRANGI',
        'SEK' => 'İSVEÇ KRONU',
        'CAD' => 'KANADA DOLARI',
        'KWD' => 'KUVEYT DİNARI',
        'NOK' => 'NORVEÇ KRONU',
        'SAR' => 'SUUDİ ARABİSTAN RİYALİ',
        'JPY' => 'JAPON YENİ',
        'BGN' => 'BULGAR LEVASI',
        'RON' => 'RUMEN LEYİ',
        'RUB' => 'RUS RUBLESİ',
        'IRR' => 'İRAN RİYALİ',
        'CNY' => 'ÇİN YUANI',
        'PKR' => 'PAKİSTAN RUPİSİ',
    );

    const alis = 'DovizAlis';
    const satis = 'DovizSatis';
    const efektif_alis = 'EfektifAlis';
    const efektif_satis = 'EfektifSatis';

    private $data;
    private $getCurrencyCode;
    private $cache;
    private $cacheDir = "";

    /**
     * @bool $cache Cache yapısını kullanmak istiyorsanız true olarak gönderiniz. Böylelikler her seferinde TCMB dan veriler her seferinde çekilmez.
     * @string $cacheDir Cache dosyasının yazılacağı dizini belirtiniz. Cache dosyası belirteceğiniz dizin altında talep edilen tarihin adı ile saklanacaktır. Örn: 2025-01-03.json
     */
    public function __construct($cache = false, $cacheDir = "")
    {
        $this->cache = $cache;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param $date
     * @return string
     */
    private function getEndPoint($date)
    {
        $Tarih = date("Y-m-d", strtotime($date));
        $HangiGun = date("l", strtotime($Tarih));
        switch ($HangiGun) {
            case "Sunday":
                $Tarih = date("Y-m-d", strtotime("-2 Day", strtotime($Tarih)));
                break;
            case "Saturday":
                $Tarih = date("Y-m-d", strtotime("-1 Day", strtotime($Tarih)));
                break;
        }
        if ($Tarih == date("Y-m-d", time())) {
            return $this->tcmbUrl . 'today.xml';
        } else {
            return $this->tcmbUrl . date("Ym", strtotime($Tarih)) . '/' . date("dmY", strtotime($Tarih)) . '.xml';
        }
    }

    /**
     * @param $date
     * @return void
     * @throws ConnectionFailed
     */
    private function getXml($date)
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->getEndPoint($date));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new ConnectionFailed("cURL Hatası: " . curl_error($ch));
            }

            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpStatusCode !== 200) {
                throw new ConnectionFailed("HTTP Hatası: $httpStatusCode - URL:");
            }

            curl_close($ch);

            $xml = simplexml_load_string($response);

            if (!$xml) {
                throw new ConnectionFailed("XML geçersiz veya yüklenemedi");
            }
            $this->data = $this->formatTcmbData((array)$xml);
            if ($this->cache) {
                $cacheSystem = new filecache($this->cacheDir, 0);
                $cacheSystem->createCache($this->data, $date);
            }
        } catch (\Exception $e) {
            throw new ConnectionFailed("Döviz kurları alınırken hata oluştu: " . $e->getMessage());
        }
    }

    /**
     * @param $data
     * @return array
     */
    private function formatTcmbData($data)
    {
        $kurlar = [];
        $i = 0;
        if (isset($data['Currency']) && count($data['Currency'])) {
            foreach ($data['Currency'] as $currency) {
                $dataKur = (array)$currency;
                $dataKurCode = $dataKur['@attributes']['CurrencyCode'];
                $efektifSatis = $i > 11 ? 0 : $dataKur["BanknoteBuying"] / $dataKur['Unit'];
                $efektifAlis = $i > 11 ? 0 : $dataKur["BanknoteSelling"] / $dataKur['Unit'];
                $kurlar[$dataKurCode] = [
                    self::alis => $dataKur["ForexBuying"] / $dataKur['Unit'],
                    self::satis => $dataKur["ForexSelling"] / $dataKur['Unit'],
                    self::efektif_alis => $efektifAlis,
                    self::efektif_satis => $efektifSatis,
                ];
                $i++;
            }
        }
        return $kurlar;
    }

    /**
     * @string $currencyCode Kur bilgisi alınacak döviz cinsini belirtiniz.
     * @datetime $date Kur bilgisi alınacak tarih bilgisini gönderiniz. Tarih bilgisi gönderilmediğinde günün tarihi alınır.
     * @bool $live Cache yapısını kullanıyorsanız verinin canlı olarak çekilmesi için true olarak gönderiniz.
     * @return $this
     * @throws ConnectionFailed
     * @throws UnknownCurrencyCode
     */
    public function kurGetir($currencyCode, $date = "", $live = false)
    {
        if ($date == "") {
            $date = date("Y-m-d", time());
        }
        if ($currencyCode != "" && !isset($this->dovizKurlari[$currencyCode])) {
            throw new UnknownCurrencyCode("Tanımsız döviz cinsi. Lütfen geçerli döviz tiplerini kontrol ediniz.");
        }
        if($live) {
            if ($this->cache) {
                //Cache yapısı aktif cachete veri var mı kontrol et
                $cacheData = $this->getCacheData($date);
                if ($cacheData != "") {
                    $this->data = (array)$cacheData;
                    return $this;
                }
            }
        }
        $this->getCurrencyCode = $currencyCode;
        $this->getXml($date);
        return $this;
    }

    /**
     * @param $cacheDate
     * @return false|mixed|string
     */
    private function getCacheData($cacheDate)
    {
        $cacheSystem = new filecache($this->cacheDir, 0, false);
        $cacheFile = $this->cacheDir . $cacheDate . ".json";

        if (!file_exists($cacheFile)) {
            error_log("Cache dosyası bulunamadı: " . $cacheFile);
            return "";
        }

        if ($cacheDate == date("Y-m-d", time())) {
            $fileCreationTime = filemtime($cacheFile);
            $currentTime = time();
            $timeDifference = $currentTime - $fileCreationTime;
            if ($timeDifference > 3 * 60 * 60) {
                error_log("Cache süresi dolmuş: " . $cacheFile);
                unlink($cacheFile);
                return "";
            }
        }

        $cacheData = $cacheSystem->getCache($cacheDate);
        if (!$cacheData) {
            error_log("Cache okunamadı: " . $cacheFile);
        }

        return $cacheData;
    }

    /**
     * @datetime $date Belirtmiş olduğunuz tarihteki tüm döviz kurlarının array listesini döner.
     * @return mixed
     * @throws ConnectionFailed
     * @throws UnknownCurrencyCode
     */
    public function kurListesiGetir($date)
    {
        $this->kurGetir("", $date);
        return $this->data;
    }

    /**
     * Kur bilgisi çekilecek Döviz Tiplerini döner.
     * @return array
     */
    public function gecerliKurListesi()
    {
        return (array)$this->dovizKurlari;
    }

    /**
     * Belirtmiş olduğunuz kur tipinin Döviz Satış tutarını döner.
     * @return float
     */
    public function satis()
    {
        return (float)$this->data[$this->getCurrencyCode][self::satis];
    }

    /**
     * Belirtmiş olduğunuz kur tipinin Döviz Alış tutarını döner.
     * @return float
     */
    public function alis()
    {
        return (float)$this->data[$this->getCurrencyCode][self::alis];
    }

    /**
     * Belirtmiş olduğunuz kur tipinin Efektif Satış tutarını döner.
     * @return float
     */
    public function efektifSatis()
    {
        return (float)$this->data[$this->getCurrencyCode][self::efektif_satis];
    }

    /**
     * Belirtmiş olduğunuz kur tipinin Efektif Alış tutarını döner.
     * @return float
     */
    public function efektifAlis()
    {
        return (float)$this->data[$this->getCurrencyCode][self::efektif_alis];
    }
}