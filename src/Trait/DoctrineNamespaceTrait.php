<?php

declare(strict_types=1);

namespace App\Trait;

/** @internal */
trait DoctrineNamespaceTrait
{
    protected static function generateAddSqlCommand(string $query): string
    {
        $query = \var_export($query, true);

        return <<<SQL
            \$this->addSql({$query});
            SQL;
    }

    /** @param array<string, string> $dirs */
    protected function getDoctrineNamespace(array $dirs): string
    {
        \assert(\count($dirs) === 1);
        $namespace = \key($dirs);

        if (!isset($dirs[$namespace])) {
            throw new \Exception(\sprintf(
                'Path not defined for the namespace "%s"',
                $namespace,
            ));
        }

        \assert($namespace !== null);

        return $namespace;
    }
}
