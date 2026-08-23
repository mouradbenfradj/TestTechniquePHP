<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\FizzBuzzRequest;

final class FizzBuzzGenerator
{
    /**
     * Genere les valeurs FizzBuzz de 1 jusqu'a la limite demandee.
     *
     * Un nombre peut recevoir le premier mot, le second mot, ou les deux
     * mots concaténés lorsqu'il est multiple des deux diviseurs.
     *
     * @return list<string> La sequence generee dans l'ordre numerique
     */
    public function generate(FizzBuzzRequest $request): array
    {
        $result = [];

        // Chaque position de la liste correspond a un nombre de 1 a limit.
        for ($number = 1; $number <= $request->limit; ++$number) {
            $value = '';

            // Ajoute le premier mot si le nombre est divisible par int1.
            if (0 === $number % $request->int1) {
                $value .= $request->str1;
            }

            // Ajoute le second mot si le nombre est divisible par int2.
            if (0 === $number % $request->int2) {
                $value .= $request->str2;
            }

            // Conserve le nombre lorsqu'il n'est multiple d'aucun diviseur.
            $result[] = $value !== '' ? $value : (string) $number;
        }

        // Retourne la sequence complete au provider API Platform.
        return $result;
    }
}
