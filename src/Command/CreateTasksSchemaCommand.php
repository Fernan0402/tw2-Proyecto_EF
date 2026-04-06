<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Throwable;

/**
 * Crea `tasks` y `tasks_translations` usando config/schema_multilingual_tasks.sql.
 * Útil cuando aún no se importó el SQL en MariaDB/phpMyAdmin.
 */
class CreateTasksSchemaCommand extends Command
{
    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Crea las tablas tasks y tasks_translations en la base configurada.';
    }

    /**
     * @param \Cake\Console\ConsoleOptionParser $parser Parser
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription(static::getDescription());

        return $parser;
    }

    /**
     * @param \Cake\Console\Arguments $args Argumentos
     * @param \Cake\Console\ConsoleIo $io Consola
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $path = CONFIG . 'schema_multilingual_tasks.sql';
        if (!is_readable($path)) {
            $io->error('No se encuentra el archivo: ' . $path);

            return static::CODE_ERROR;
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            $io->error('No se pudo leer el archivo SQL.');

            return static::CODE_ERROR;
        }

        $sql = preg_replace('/^--.*$/m', '', $sql) ?? $sql;
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn (string $s): bool => $s !== '',
        );

        $connection = ConnectionManager::get('default');

        try {
            foreach ($statements as $statement) {
                $connection->execute($statement);
                $io->verbose('OK: ' . substr(str_replace(["\n", "\r"], ' ', $statement), 0, 72) . '…');
            }
        } catch (Throwable $e) {
            $io->error('Error al ejecutar SQL: ' . $e->getMessage());
            $io->out('Si las tablas ya existen, puede ignorar errores de "already exists".');

            return static::CODE_ERROR;
        }

        $io->success('Tablas `tasks` y `tasks_translations` creadas (o ya existían).');

        return static::CODE_SUCCESS;
    }
}
