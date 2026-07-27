<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
$app = new yii\web\Application($config);

echo "<h3>Current DSN:</h3>";
echo Yii::$app->db->dsn;

echo "<hr>";

echo "<h3>Current Database:</h3>";
echo Yii::$app->db->createCommand("SELECT DATABASE()")->queryScalar();

echo "<hr>";

echo "<h3>Tables:</h3><pre>";
print_r(Yii::$app->db->schema->getTableNames());
echo "</pre>";