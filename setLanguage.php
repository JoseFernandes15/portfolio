<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';

use Gettext\Translator;
use Gettext\Translations;

$api_key=IP_INFO_API_KEY;

// Função para obter o país do usuário
function getUserCountry() {
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else {
        // Caso não esteja disponível, usa o IP remoto padrão
        $user_ip = $_SERVER['REMOTE_ADDR'];
    }
    $response = file_get_contents("https://ipinfo.io/{$user_ip}/json?token=");
    if ($response === false) {
        return 'EN'; // Retorna um valor padrão se a API falhar
    }
    $data = json_decode($response, true);
    $countryCode = $data['country'];
    return $countryCode; // Retorna o código do país (ex: 'PT', 'BR', etc.)
}

// Verifica se o cookie de idioma já está definido
if (!isset($_COOKIE['language'])) {
    // Obtém o país do usuário
    $countryCode = getUserCountry();
    // Define o idioma com base no país
    switch ($countryCode) {
        case 'PT':
            $language = 'pt_pt';
            break;
        case 'BR':
            $language = 'pt_pt';
            break;
        default:
            $language = 'en_en'; // Idioma padrão
            break;
    }

    // Define o cookie de linguagem por 30 dias
    setcookie('language', $language, time() + (30 * 24 * 60 * 60), '/'); // 30 dias
} else {
    // Se o cookie já estiver definido, use o idioma do cookie
    $language = $_COOKIE['language'];
    // Se o cookie não estiver com os valores esperados muda a linguagem para en e faz um novo cookie
    if($language!='pt_pt' && $language!='en_en'){
        $language = 'en_en';
        setcookie('language', $language, time() + (30 * 24 * 60 * 60), '/'); // 30 dias
    }
}

// Carregue as traduções com base no idioma
$translations = Translations::fromPoFile("locales/$language/LC_MESSAGES/texto.po");
$translator = new Translator();
$translator->loadTranslations($translations);

?>
