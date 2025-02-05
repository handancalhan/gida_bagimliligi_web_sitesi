<?php
session_start();
// Formdan geldiğinden emin olalım
if (!isset($_POST["formdangeliyor"])) {
    // JavaScript yardımıyla bir mesaj verip kayıt formuna yönlendirelim...
    echo "<script language='javascript'>
                alert('Lütfen önce kayıt formunu doldurunuz!');
                window.location.href='sign-up.html';
          </script>";
    exit;
}


// Veri tabanına bağlanalım...
try {
  $vt = new PDO("mysql:dbname=gidabagimli;host=localhost;charset=utf8","root", "");
  // SQL hata kodlarının görünmesini sağlayalım...
  $vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo $e->getMessage();
}

// Şifreler aynı mı?
if ($_POST["password"] != $_POST["confirm_password"]) {
    //$_SESSION["hata"] = "Şifreler aynı değil!";
    header("Location: sign-up.html");
    exit;
}
// Şifre en az 5 karakter mi?
if (strlen($_POST["password"]) < 5) {
    //$_SESSION["hata"] = "Şifre en az 5 karakter olmalıdır!";
    header("Location: sign-up.html");
    exit;
}

// Şifreyi şifrele
$sifre = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Ad soyad işlemleri
// Hem değer ataması yapıldığını hem de boş değer atanmadığını kontrol ettiriyoruz.
if (!isset($_POST["isim"]) or (empty($_POST["isim"]))) {
    //$_SESSION["hata"] = "Ad soyad boş bırakılamaz!";
    header("Location: sign-up.html");
    exit;
}
// En az 5 karakter olsun
// Önce trim ile baştaki ve sondaki ekstra boşlukları silelim.
$adsoyad = trim($_POST["isim"]);
if(strlen($adsoyad)<5) {
    //$_SESSION["hata"] = "Ad soyad çok kısa!";
    header("Location: sign-up.html");
    exit;
}


// Sorgular ve diğer işlemler burada...
$sql = "insert into uye (adsoyad, eposta, sifre) values (:adsoyad, :eposta, :sifre)";
$ifade = $vt->prepare($sql);
$sonuc = $ifade->execute(Array(":adsoyad"=>$adsoyad, ":eposta"=>$_POST["email"], ":sifre"=>$sifre ));
// Kayıt oldu mu?
if ($sonuc == false) {
    //$_SESSION["hata"] = "Kayıt olurken bir hata oluştu, lütfen sonra tekrar deneyiniz!";
    header("Location: sign-up.html");
    exit;
}
//Bağlantıyı yok edelim... Biz etmezsekte, kodun çalışması bittiğinde kendiside yok olacak.
$ifade = null;
$vt = null;
// Bu sayfada kalırsak, kullanıcı F5'e bastığında veriler VT'ye tekrar kaydolmaya çalışır
// Başka sayfaya yönlendirelim...
//$_SESSION["basari"] = "Kayıt başarıyla oluşturuldu!";
header('Location: index.html');
?>