```tcmb

  ____  _   _ ____            _____ ____ __  __ ____  
 |  _ \| | | |  _ \          |_   _/ ___|  \/  | __ ) 
 | |_) | |_| | |_) |  _____    | || |   | |\/| |  _ \ 
 |  __/|  _  |  __/  |_____|   | || |___| |  | | |_) |
 |_|   |_| |_|_|               |_| \____|_|  |_|____/ 
                                                      

```


## 📜 Genel Bilgiler

**Proje Adı:** `tcmb`

`tcmb` ile TCMB üzerinde listelenen kurları Döviz Alış, Döviz Satış, Efektif Alış ve Efektif Satış tiplerine göre çekebilirsiniz.

## 🚀 Kurulum

**PHP TCMB** kurulumunu, proje ana dizininde aşağıdaki komutu yazarak kolayca gerçekleştirebilirsiniz:

```bash
composer require emirustaoglu/tcmb
```

veya `composer.json` dosyanıza aşağıdaki satırları ekleyerek manuel kurulum yapabilirsiniz

```bash
{
    "require": {
        "emirustaoglu/tcmb": "^1.0.0"
    }
}
```

Daha sonrasında aşağıdaki komutu çalıştırın

```bash
composer install
```

## ⚙️ Gereksinimler

- PHP Sürümü:
    - Minimum: PHP 7.4
    - Tavsiye Edilen: PHP 8.1 veya üzeri

## 💻 Kullanım Örneği

```php
use emirustaoglu\tcmb\Doviz;

/**
 * @bool $cache Cache yapısını kullanmak istiyorsanız true olarak gönderiniz. Böylelikler her seferinde TCMB dan veriler her seferinde çekilmez.
 * @string $cacheDir Cache dosyasının yazılacağı dizini belirtiniz. Cache dosyası belirteceğiniz dizin altında talep edilen tarihin adı ile saklanacaktır. Örn: 2025-01-03.json
 */
$tcmbDoviz = new Doviz();

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

echo $dovizSatisKuru;
```
### Alış Kuru Çekme

```php

$dovizAlisKuru = $tcmbDoviz->kurGetir("USD","2025-01-02")->alis();

```

### Efektif Alış Kuru

```php

$dovizEfektifAlisKuru = $tcmbDoviz->kurGetir("USD","2025-01-02")->efektifAlis();

```

### Efektis Satış Kuru

```php

$dovizEfektifAlisKuru = $tcmbDoviz->kurGetir("USD","2025-01-02")->efektifAlis();

```

## 🗂️ Cache Kullanımı

```php

/**
 * @bool $cache Cache yapısını kullanmak istiyorsanız true olarak gönderiniz. Böylelikler her seferinde TCMB dan veriler her seferinde çekilmez.
 * @string $cacheDir Cache dosyasının yazılacağı dizini belirtiniz. Cache dosyası belirteceğiniz dizin altında talep edilen tarihin adı ile saklanacaktır. Örn: 2025-01-03.json
 */
$tcmbDoviz = new Doviz(true, __DIR__ . "/cache/");

```
Cache yapısını kullanırken tüm veriler mevcut ise cache dosyası üzerinden döner. Eğer çekmek istediğiniz kurun cache üzerinden gelmesini istemiyorsanız `live` değerini `true` olarak gönderiniz.

```php

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
$dovizSatisKuru = $tcmbDoviz->kurGetir("USD","2025-01-02", true)->satis();

```

## 🤝 Katkıda Bulunma
Bu proje açık kaynaklıdır. İsteyen herkes katkıda bulunabilir.

1. Projeyi forklayın ( https://github.com/emirustaoglu/numbertoword/fork )
2. Özellik dalınızı (branch) oluşturun (git checkout -b yeni-ozellik)
3. Değişikliklerinizi commitleyin (git commit -am 'Yeni özellik eklendi')
4. Dalınıza push yapın (git push origin yeni-ozellik)
5. Yeni bir Pull Request oluşturun

## 📜 Lisans
Bu proje [MIT](http://opensource.org/licenses/MIT) Lisansı altında lisanslanmıştır. Dilediğiniz gibi kullanabilir ve dağıtabilirsiniz.

### 🎉 Kullandığınız için Teşekkürler!
