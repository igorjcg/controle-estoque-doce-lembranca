<?php

namespace app\common\util;

use yii\db\Exception;

class Util
{
    public static function formatDecimalTrimmed(
        float $valor,
        int $casasDecimais = 3,
        string $separadorDecimal = ',',
        string $separadorMilhar = '.'
    ): string {
        return rtrim(
            rtrim(number_format($valor, $casasDecimais, $separadorDecimal, $separadorMilhar), '0'),
            $separadorDecimal
        );
    }

    public static function formatQuantidadeComUnidade(
        float $quantidade,
        ?string $sigla,
        int $casasDecimais = 3
    ): string {
        $valorFormatado = static::formatDecimalTrimmed($quantidade, $casasDecimais);

        if ($sigla === null || $sigla === '') {
            return $valorFormatado;
        }

        return $valorFormatado . ' ' . $sigla;
    }

    /**
     * Converte quantidade entre unidade de origem e unidade de destino.
     */
    public static function converter(
        float $quantidade,
        float $fatorOrigem,
        float $fatorDestino,
        string $categoriaOrigem,
        string $categoriaDestino
    ): float {
        if ($categoriaOrigem !== $categoriaDestino) {
            throw new Exception('Conversão inválida entre categorias de unidade diferentes.');
        }

        if ($fatorOrigem <= 0 || $fatorDestino <= 0) {
            throw new Exception('Fator de conversão inválido.');
        }

        return $quantidade * ($fatorOrigem / $fatorDestino);
    }

    public static function depura()
    {
        foreach (func_get_args() as $arg) {
            echo "<div style=\"text-align:left;border:1px dashed #c20000;background-color:#FFFEAD;color:#c20000;font-weight:bold;margin:5px;padding:4px;padding-left:15px;font-size:12px;\"><pre style=\"font-family:verdana;\">";
            print_r($arg);
            echo "</pre></div>";
        }
    }
}
