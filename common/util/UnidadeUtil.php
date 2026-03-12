<?php

namespace app\common\util;

use app\models\UnidadeMedida;

/**
 * Utilitário para conversão entre unidades de medida.
 * Quantidades devem ser armazenadas sempre na unidade base do ingrediente.
 */
class UnidadeUtil
{
    /**
     * Converte quantidade de uma unidade para a unidade base do ingrediente.
     *
     * @param float $quantidade Quantidade na unidade de origem
     * @param UnidadeMedida $unidadeOrigem Unidade em que a quantidade está expressa
     * @param UnidadeMedida $unidadeBase Unidade base (ex.: unidade do ingrediente)
     * @return float Quantidade convertida para a unidade base
     */
    public static function converterParaBase(
        float $quantidade,
        UnidadeMedida $unidadeOrigem,
        UnidadeMedida $unidadeBase
    ): float {
        return Util::converter(
            $quantidade,
            (float) $unidadeOrigem->fator_base,
            (float) $unidadeBase->fator_base,
            $unidadeOrigem->categoria,
            $unidadeBase->categoria
        );
    }
}
