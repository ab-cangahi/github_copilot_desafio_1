<?php

function luhnCheck($numero) {
    $soma = 0;
    $par = false;
    $numero = preg_replace('/\D/', '', $numero); // Remove não dígitos

    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $digito = intval($numero[$i]);
        if ($par) {
            $digito *= 2;
            if ($digito > 9) $digito -= 9;
        }
        $soma += $digito;
        $par = !$par;
    }
    return ($soma % 10) === 0;
}

function validarCartaoCredito($numero) {
    $bandeiras = [
        'mastercard' => '/^5[1-5][0-9]{14}$/',
        'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
        'visa_16' => '/^4[0-9]{15}$/',
        'hipercard' => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
        'aura' => '/^50[0-9]{14,17}$/',
        'amex' => '/^3[47][0-9]{13}$/',
        'voyager' => '/^8699[0-9]{11}$/',
        'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
        'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
        'enroute' => '/^(2014|2149)[0-9]{11}$/',
        'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/'
    ];

    foreach ($bandeiras as $nome => $regex) {
        if (preg_match($regex, $numero) && luhnCheck($numero)) {
            $bonus = 0;
            if ($nome === 'visa' || $nome === 'mastercard') {
                $bonus = 0.15;
            }
            return [
                'valido' => true,
                'bandeira' => $nome,
                'bonus' => $bonus
            ];
        }
    }
    return [
        'valido' => false,
        'bandeira' => null,
        'bonus' => 0
    ];
}

// Exemplo de uso:
$resultado = validarCartaoCredito('30276836438374');
if ($resultado['valido']) {
    echo "Cartão válido! Bandeira: {$resultado['bandeira']}";
    if ($resultado['bonus'] > 0) {
        echo " | Bônus: " . ($resultado['bonus'] * 100) . "%";
    }
} else{
    echo "Cartão inválido!";
}

