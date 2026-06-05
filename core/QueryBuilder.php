<?php

namespace Core;

class QueryBuilder
{
    private \PDO $db;
    private string $table;
    private string $primaryKey;
    private string $modelClass;

    // ─── クエリ構築用プロパティ ───────────────────────────────
    private array   $selects    = [];
    private array   $wheres    = [];
    private array   $bindings  = [];
    private ?string $orderBy   = null;
    private string  $direction = 'ASC';
    private ?int    $limit     = null;

    public function __construct(
        string $table,
        string $modelClass,
        string $primaryKey = 'id'
    )
    {
        $this->db         = Database::connect();
        $this->table      = $table;
        $this->modelClass = $modelClass;
        $this->primaryKey = $primaryKey;
    }

    /** 全件取得 */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return array_map(fn($row) => $this->modelClass::fromArray($row), $stmt->fetchAll());
    }

    /** 主キーで1件取得 */
    public function findById(int|string $id): ?Model
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->modelClass::fromArray($row) : null;
    }

    /** 条件で取得 */
    public function findBy(string $column, mixed $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return array_map(fn($row) => $this->modelClass::fromArray($row), $stmt->fetchAll());
    }

    /** 条件で1件取得 */
    public function findOneBy(string $column, mixed $value): ?Model
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        $row = $stmt->fetch();
        return $row ? $this->modelClass::fromArray($row) : null;
    }

    /** 新規保存 */
    public function insert(Model $model, $isIgnore = false): bool
    {
        $data         = $model->toArray();
        $fields       = array_keys($data);
        $columns      = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $ignore = $isIgnore ? 'IGNORE' : '';

        $stmt   = $this->db->prepare("INSERT {$ignore} INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $result = $stmt->execute(array_values($data));

        $model->{$this->primaryKey} = $this->db->lastInsertId();
        return $result;
    }

    /** 一括保存 */
    public function bulkInsert(array $models, $isIgnore = false): bool
    {
        if (empty($models)) return false;

        // カラム名の取得（最初のモデルから）
        $data = $models[0]->toArray();
        $fields = array_keys($data);
        $columns = implode(', ', $fields);

        // プレースホルダーの生成 (?, ?, ?), (?, ?, ?) ...
        $rowPlaceholder = '(' . implode(', ', array_fill(0, count($fields), '?')) . ')';
        $placeholders = implode(', ', array_fill(0, count($models), $rowPlaceholder));

        $ignore = $isIgnore ? 'IGNORE' : '';
        $sql = "INSERT {$ignore} INTO {$this->table} ({$columns}) VALUES {$placeholders}";

        // 値のフラットな配列を作成
        $values = [];
        foreach ($models as $model) {
            $values = array_merge($values, array_values($model->toArray()));
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /** 更新 */
    public function update(Model $model): bool
    {
        $data   = $model->toArray();
        $pk     = $this->primaryKey;
        $fields = array_filter(array_keys($data), fn($k) => $k !== $pk);
        $set    = implode(', ', array_map(fn($f) => "{$f} = ?", $fields));
        $vals   = array_map(fn($f) => $data[$f], $fields);
        $vals[] = $data[$pk];

        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$pk} = ?");
        return $stmt->execute($vals);
    }

    /** 削除 */
    public function delete(Model $model): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$model->{$this->primaryKey}]);
    }

    /** SELECT */
    public function select(array $selects): static
    {
        $this->selects = $selects;
        return $this;
    }

    /** WHERE */
    public function where(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        $argc = func_num_args();
        $this->addWhere($column, $operatorOrValue, $value, 'AND', $argc);
        return $this;
    }

    public function orWhere(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        $argc = func_num_args();
        $this->addWhere($column, $operatorOrValue, $value, 'OR', $argc);
        return $this;
    }

    public function whereNull(string $column): static
    {
        $column = $this->sanitizeColumn($column);
        $this->wheres[] = ['condition' => "{$column} IS NULL", 'type' => 'AND'];
        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $column = $this->sanitizeColumn($column);
        $this->wheres[] = ['condition' => "{$column} IS NOT NULL", 'type' => 'AND'];
        return $this;
    }

    /** ORDER BY */
    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy    = $column;
        $this->direction  = $direction;
        return $this;
    }

    /** LIMIT */
    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /** メソッドチェーンで構築したクエリを実行して複数件取得 */
    public function get(): array
    {
        $select = !empty($this->selects) ? implode(',', $this->selects) : '*';
        $sql = "SELECT {$select} FROM {$this->table}";

        if (!empty($this->wheres)) {
            $conditions = '';
            foreach ($this->wheres as $i => $where) {
                $conditions .= $i === 0
                    ? $where['condition']
                    : " {$where['type']} {$where['condition']}";
            }
            $sql .= ' WHERE ' . $conditions;
        }

        if ($this->orderBy !== null) {
            $sql .= " ORDER BY {$this->orderBy} {$this->direction}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        $this->reset();

        return array_map(fn($row) => $this->modelClass::fromArray($row), $stmt->fetchAll());
    }

    private function addWhere(
        string $column,
        mixed $operatorOrValue,
        mixed $value,
        string $type,
        int $argc
    ): void
    {
        $column   = $this->sanitizeColumn($column);
        $operator = $argc === 3 ? $operatorOrValue : '=';
        $val      = $argc === 3 ? $value : $operatorOrValue;

        $this->wheres[]   = ['condition' => "{$column} {$operator} ?", 'type' => $type];
        $this->bindings[] = $val;
    }

    private function reset(): void
    {
        $this->wheres    = [];
        $this->bindings  = [];
        $this->orderBy   = null;
        $this->direction = 'ASC';
        $this->limit     = null;
    }

    /** メソッドチェーンで構築したクエリを実行して1件取得 */
    public function first(): ?object
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    /** カラム名のサニタイズ（SQLインジェクション対策） */
    private function sanitizeColumn(string $column): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }
        return $column;
    }
}
