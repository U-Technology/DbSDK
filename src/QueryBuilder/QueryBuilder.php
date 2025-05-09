<?php

namespace UTechnology\DbSDK\QueryBuilder;

class QueryBuilder
{
    private $entityClass;
    private $objEntity;
    private $conditions = [];
    private $selects = [];
    private $orderBy = [];
    private $limit = null;
    private $offset = null;

    public function __construct($entityClass) {
        $this->entityClass = $entityClass;
        $this->objEntity = $this->createInstanceOfAllowedType($entityClass);
    }


    private function createInstanceOfAllowedType($entityClass)
    {
        // Verifica che la classe esista
        if (class_exists($entityClass)) {
            // Crea una nuova istanza della classe
            return new $entityClass();
        } else {
            throw new \Exception("La classe {$entityClass} non esiste");
        }
    }

    public function where(\Closure $predicate) {
        // Analizza la closure per estrarre la condizione
        $condition = $this->parseExpression($predicate);
        $this->conditions[] = $condition;
        return $this;
    }

    public function limit(int $limitRecord){
        $this->limit = $limitRecord;
    }

    private function parseExpression(\Closure $closure) {
        // Ottieni il codice sorgente della closure usando Reflection
        $reflection = new \ReflectionFunction($closure);
        $startLine = $reflection->getStartLine();
        $endLine = $reflection->getEndLine();
        $fileName = $reflection->getFileName();

        $source = file($fileName);
        $sourceCode = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));

        // Estrai la parte dell'espressione dopo la freccia =>
        preg_match('/\((.*?)\)\s*=>\s*(.*)/', $sourceCode, $matches);

        if (count($matches) < 3) {
            throw new \Exception("Impossibile analizzare l'espressione lambda");
        }

        $paramName = trim($matches[1]); // es. "u"
        $expression = trim($matches[2]); // es. "u.age > 18"

        // Analizza l'espressione per estrarre campo, operatore e valore
        return $this->parseConditionExpression($paramName, $expression);
    }

    private function parseConditionExpression($paramName, $expression) {
        // Rimuovi eventuali punto e virgola finali o parentesi
        $expression = rtrim($expression, ';)');

        // Modelli comuni per le espressioni di confronto
        $patterns = [
            // Pattern per confronti come "u.age > 18"
            '/'. preg_quote($paramName) .'\.(\w+)\s*([><=!]{1,2})\s*(.+)/',
            // Pattern per confronti di uguaglianza come "u.status == 'active'"
            '/'. preg_quote($paramName) .'\.(\w+)\s*==\s*[\'"](.+)[\'"]/',
            // Aggiungi altri pattern per diversi tipi di confronti
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $expression, $matches)) {
                // Estrai campo, operatore e valore
                $field = $matches[1];
                $operator = $matches[2] ?? '='; // Default a = se non specificato
                $value = trim($matches[3] ?? $matches[2], '\'"'); // Rimuovi eventuali apici

                // Normalizza operatori
                $operator = $this->normalizeOperator($operator);

                return [
                    'field' => $field,
                    'operator' => $operator,
                    'value' => $this->evaluateValue($value)
                ];
            }
        }

        throw new \Exception("Formato dell'espressione non supportato: $expression");
    }

    private function normalizeOperator($operator) {
        $map = [
            '==' => '=',
            '===' => '=',
            '!=' => '<>',
            '!==' => '<>'
        ];

        return $map[$operator] ?? $operator;
    }

    private function evaluateValue($value) {
        // Cerca di valutare il valore se è un'espressione PHP
        if (is_numeric($value)) {
            return $value;
        }

        // Se il valore è una stringa tra apici, rimuovi gli apici
        if (preg_match('/^[\'"](.+)[\'"]$/', $value, $matches)) {
            return $matches[1];
        }

        // Altrimenti, prova a valutarlo come espressione PHP
        // Nota: questo approccio è limitato e andrebbe migliorato per sicurezza
        try {
            return eval("return $value;");
        } catch (\Throwable $e) {
            return $value;
        }
    }

    // Gli altri metodi (select, orderBy, limit, toSql, execute...) rimangono simili
    // all'implementazione precedente, ma vanno adattati al nuovo sistema

    public function toSql() :string {
        // Ottieni i metadati per la mappatura entità-tabella
        $tableName = $this->objEntity->__tableName;

        // Costruisci la clausola SELECT
        $selectFields = empty($this->selects) ? '*' : implode(', ', $this->selects);
        $sql = "SELECT {$selectFields} FROM {$tableName}";

        // Aggiungi condizioni WHERE
        if (!empty($this->conditions)) {
            $whereClauses = [];
            foreach ($this->conditions as $condition) {
                $whereClauses[] = "{$condition['field']} {$condition['operator']} ?";
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $limitSql = "";
        if(isset($this->limit)){
            $limitSql = "LIMIT {$this->limit}";

            $sql .= " " . $limitSql;
        }

        // Aggiungi altre clausole (ORDER BY, LIMIT, ecc.)
        // ...

        return $sql;
    }

    public function getParameters() {
        $params = [];
        foreach ($this->conditions as $condition) {
            $params[] = $condition['value'];
        }
        return $params;
    }

    // ... altri metodi ...
}