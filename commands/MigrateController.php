<?php

namespace app\commands;

use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Estende o MigrateController do Yii2 para adicionar o comando fresh.
 */
class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     * Recria o banco: reverte todas as migrations e aplica novamente.
     *
     * Uso:
     *   php yii migrate/fresh
     *   php yii migrate/fresh --interactive=0   # sem confirmação
     *
     * @return int
     */
    public function actionFresh(): int
    {
        $history = $this->getMigrationHistory(null);
        if (!empty($history)) {
            $migrations = array_keys($history);
            $n = count($migrations);
            $this->stdout("Revertendo $n " . ($n === 1 ? 'migration' : 'migrations') . "...\n", Console::FG_YELLOW);
            foreach (array_reverse($migrations) as $migration) {
                if (!$this->migrateDown($migration)) {
                    $this->stdout("Falha ao reverter: $migration\n", Console::FG_RED);
                    return ExitCode::UNSPECIFIED_ERROR;
                }
                $this->stdout("\t$migration revertida.\n", Console::FG_GREEN);
            }
            $this->stdout("Todas as migrations foram revertidas.\n\n", Console::FG_GREEN);
        } else {
            $this->stdout("Nenhuma migration aplicada. Aplicando do zero.\n\n", Console::FG_YELLOW);
        }

        $this->stdout("Aplicando migrations...\n", Console::FG_YELLOW);
        return (int) $this->actionUp(0);
    }
}
