<?php

use Tyea\Aviator\App;
use Tyea\Aviator\Db;

function env(string $key, $default = null)
{
	return $_ENV[$key] ?? $default;
}

function now(): DateTime
{
	return new DateTime("now", new DateTimeZone("UTC"));
}

function dd($var = null): void
{
	ob_start();
	var_dump($var);
	$dump = ob_get_clean();
	$content = "<pre>" . htmlspecialchars($dump, ENT_COMPAT, "UTF-8") . "</pre>";
	App::response($content, 500);
}

function migrate(string $pattern): void
{
    Db::execute("
			CREATE TABLE IF NOT EXISTS `migrations` (
				`id` BIGINT AUTO_INCREMENT NOT NULL,
				`name` VARCHAR(255) NOT NULL,
				`created_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			);
		");
    $files = glob($pattern);
    foreach ($files as $file) {
        $name = basename($file);
        $row = Db::row("SELECT * FROM `migrations` WHERE `name` = ?;", [$name]);
        if (!$row) {
            $query = file_get_contents($file);
            Db::execute($query);
            $row = [
                "name" => $name,
                "created_at" => now()->format(MYSQL_DATETIME)
            ];
            Db::insert("migrations", $row);
        }
    }
}