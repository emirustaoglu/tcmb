<?php

require __DIR__ . '/vendor/autoload.php';

use emirustaoglu\tcmb\Doviz;

/**
 * @bool $cache Cache yapısını kullanmak istiyorsanız true olarak gönderiniz. Böylelikler her seferinde TCMB dan veriler her seferinde çekilmez.
 * @string $cacheDir Cache dosyasının yazılacağı dizini belirtiniz. Cache dosyası belirteceğiniz dizin altında talep edilen tarihin adı ile saklanacaktır. Örn: 2025-01-03.json
 */
$tcmbDoviz = new Doviz(true, __DIR__ . "/cache/");

/**
 * @string $currencyCode Kur bilgisi alınacak döviz cinsini belirtiniz.
 * @datetime $date Kur bilgisi alınacak tarih bilgisini gönderiniz. Tarih bilgisi gönderilmediğinde günün tarihi alınır.
 * @bool $live Cache yapısını kullanıyorsanız verinin canlı olarak çekilmesi için true olarak gönderiniz.
 * @return $this
 * @throws ConnectionFailed
 * @throws UnknownCurrencyCode
 */
/**
 * Belirtmiş olduğunuz kur tipinin Döviz Satış tutarını döner.
 * @return float
 */
$dovizSatisKuru = $tcmbDoviz->kurGetir("USD","2025-01-02")->satis();


//print_r($a->kurListesiGetir("2025-01-03"));
echo $b;
