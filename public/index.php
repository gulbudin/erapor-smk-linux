<?php

$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}


// DAPODIK_URL_SANITIZER
if (!empty($_POST['ipserver'])) {
    $_POST['ipserver'] = preg_replace('#^https?://#i', '', trim($_POST['ipserver']));
    $_POST['ipserver'] = preg_replace('#:[0-9]+.*$#', '', $_POST['ipserver']);
    $_POST['ipserver'] = rtrim($_POST['ipserver'], '/');
}
if (!empty($_REQUEST['ipserver'])) {
    $_REQUEST['ipserver'] = preg_replace('#^https?://#i', '', trim($_REQUEST['ipserver']));
    $_REQUEST['ipserver'] = preg_replace('#:[0-9]+.*$#', '', $_REQUEST['ipserver']);
    $_REQUEST['ipserver'] = rtrim($_REQUEST['ipserver'], '/');
}

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

require FCPATH . '../app/Config/Paths.php';

$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

class LinuxBoot extends \CodeIgniter\Boot
{
    protected static function initializeCodeIgniter(): \CodeIgniter\CodeIgniter
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'eraporsmk.edukasidata.id';
        $host = preg_replace('/:\d+$/', '', $host);

        $dbName = 'dbraporsmk25';
        try {
            $sqlitePath = '/var/www/adminerapor/database/database.sqlite';
            if (file_exists($sqlitePath)) {
                $pdo = new \PDO("sqlite:{$sqlitePath}", null, null, [\PDO::ATTR_TIMEOUT => 2]);
                $stmt = $pdo->prepare("SELECT db_name FROM erapor_instances WHERE domain = :domain LIMIT 1");
                $stmt->execute([':domain' => $host]);
                $found = $stmt->fetchColumn();
                if ($found) {
                    $dbName = $found;
                }
            }
        } catch (\Throwable $e) {}

        config("Database")->default['database'] = $dbName;
        config("App")->baseURL = "https://{$host}/";
        config("App")->allowedHostnames = [];
        return parent::initializeCodeIgniter();
    }
}
exit(LinuxBoot::bootWeb($paths));
