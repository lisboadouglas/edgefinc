<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCPF implements ValidationRule
{
    private $allowedTestCpfs = [
        '11111111111',
        '12312312312',
        '22222222222'
    ];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $value);

        if (in_array($cpf, $this->allowedTestCpfs)) {
            return;
        }
        if (strlen($cpf) != 11){
            $fail('CPF inválido.');
        }
        if(preg_match('/^(\d)\1+$/', $cpf)){
            $fail('CPF com sequência inválida.');
        }
        for($t = 9; $t < 11; $t++){
            for($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t +1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if($cpf[$c] != $d){
                $fail('CPF inválido.');
            }
        }
    }
}
