<?php
declare(strict_types=1);

function db_connect(array $config): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = mysqli_connect(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name'],
        $config['db']['port']
    );
    mysqli_set_charset($connection, 'utf8mb4');
    return $connection;
}

function db(): mysqli
{
    return $GLOBALS['db'];
}

function db_bind(mysqli_stmt $statement, string $types, array $params): void
{
    if ($types !== '') {
        mysqli_stmt_bind_param($statement, $types, ...$params);
    }
}

function db_one(string $sql, string $types = '', array $params = []): ?array
{
    $rows = db_all($sql, $types, $params);
    return $rows[0] ?? null;
}

function db_all(string $sql, string $types = '', array $params = []): array
{
    $statement = mysqli_prepare(db(), $sql);
    db_bind($statement, $types, $params);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function db_exec(string $sql, string $types = '', array $params = []): int
{
    $statement = mysqli_prepare(db(), $sql);
    db_bind($statement, $types, $params);
    mysqli_stmt_execute($statement);
    return mysqli_stmt_affected_rows($statement);
}

function db_insert(string $sql, string $types, array $params): int
{
    db_exec($sql, $types, $params);
    return (int)mysqli_insert_id(db());
}
function db_connect(array $config): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name'], $config['db']['port']);
    $db->set_charset('utf8mb4');
    return $db;
}
function db(): mysqli { return $GLOBALS['db']; }
function db_one(string $sql, string $types = '', array $params = []): ?array {
    $rows = db_all($sql, $types, $params); return $rows[0] ?? null;
}
function db_all(string $sql, string $types = '', array $params = []): array {
    $stmt = db()->prepare($sql); if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute(); $result = $stmt->get_result(); return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
function db_exec(string $sql, string $types = '', array $params = []): int {
    $stmt = db()->prepare($sql); if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute(); return $stmt->affected_rows;
}
function db_insert(string $sql, string $types, array $params): int { db_exec($sql, $types, $params); return (int)db()->insert_id; }
