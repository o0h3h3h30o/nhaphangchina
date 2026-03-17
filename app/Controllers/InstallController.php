<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class InstallController extends Controller
{
    /**
     * Check if already installed
     */
    private function isInstalled(): bool
    {
        return file_exists(WRITEPATH . 'installed.lock');
    }

    /**
     * Step 1: Check requirements
     */
    public function index()
    {
        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $checks = $this->checkRequirements();
        return view('install/index', ['checks' => $checks]);
    }

    /**
     * Step 2: Database config form
     */
    public function database()
    {
        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        return view('install/database');
    }

    /**
     * Step 3: Process database setup
     */
    public function setupDatabase()
    {
        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $hostname = $this->request->getPost('hostname') ?: '127.0.0.1';
        $username = $this->request->getPost('username') ?: 'root';
        $password = $this->request->getPost('password') ?: '';
        $database = $this->request->getPost('database') ?: 'vanchuyenhongphat';
        $port     = $this->request->getPost('port') ?: '3306';

        // Test connection
        try {
            // Try TCP first (127.0.0.1), then socket (localhost)
            $connected = false;
            $lastError = '';
            $hosts = [$hostname];
            if ($hostname === '127.0.0.1') $hosts[] = 'localhost';
            if ($hostname === 'localhost') array_unshift($hosts, '127.0.0.1');

            foreach ($hosts as $tryHost) {
                try {
                    $mysqli = @new \mysqli($tryHost, $username, $password, '', (int)$port);
                    if (!$mysqli->connect_error) {
                        $connected = true;
                        $hostname = $tryHost; // remember working host
                        break;
                    }
                    $lastError = $mysqli->connect_error;
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                }
            }

            if (!$connected) {
                return redirect()->to('/install/database')->with('error', 'Không thể kết nối MySQL: ' . $lastError);
            }

            // Create database if not exists
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            $mysqli->select_db($database);

            // Check if tables already exist
            $result = $mysqli->query("SHOW TABLES");
            if ($result->num_rows > 0) {
                return redirect()->to('/install/database')->with('error', 'Database "' . $database . '" đã có dữ liệu. Vui lòng chọn database trống hoặc xóa dữ liệu cũ.');
            }

            // Import schema
            $sqlFile = ROOTPATH . 'install/database.sql';
            if (!file_exists($sqlFile)) {
                return redirect()->to('/install/database')->with('error', 'Không tìm thấy file database.sql trong thư mục install/');
            }

            $sql = file_get_contents($sqlFile);
            $mysqli->multi_query($sql);

            // Process all results from multi_query
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            if ($mysqli->errno) {
                return redirect()->to('/install/database')->with('error', 'Lỗi import SQL: ' . $mysqli->error);
            }

            $mysqli->close();

            // Write Database.php config
            $this->writeDatabaseConfig($hostname, $username, $password, $database, $port);

            // Store DB info in session for admin step
            session()->set('install_db_done', true);

            return redirect()->to('/install/admin');

        } catch (\Exception $e) {
            return redirect()->to('/install/database')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Step 4: Create admin account
     */
    public function admin()
    {
        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        if (!session()->get('install_db_done')) {
            return redirect()->to('/install/database');
        }

        return view('install/admin');
    }

    /**
     * Step 5: Process admin creation
     */
    public function createAdmin()
    {
        if ($this->isInstalled()) {
            return redirect()->to('/');
        }

        $username = $this->request->getPost('username');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirm  = $this->request->getPost('password_confirm');

        if (!$username || !$email || !$password) {
            return redirect()->to('/install/admin')->with('error', 'Vui lòng điền đầy đủ thông tin.');
        }

        if ($password !== $confirm) {
            return redirect()->to('/install/admin')->with('error', 'Mật khẩu xác nhận không khớp.');
        }

        if (strlen($password) < 6) {
            return redirect()->to('/install/admin')->with('error', 'Mật khẩu phải từ 6 ký tự trở lên.');
        }

        try {
            $db = \Config\Database::connect();

            // Insert admin user
            $db->table('users')->insert([
                'username'       => $username,
                'email'          => $email,
                'password_hash'  => password_hash($password, PASSWORD_DEFAULT),
                'role'           => 'admin',
                'status'         => 'active',
                'email_verified' => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $userId = $db->insertID();

            // Create wallet
            $db->table('wallets')->insert([
                'user_id'    => $userId,
                'balance'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create profile
            $db->table('user_profiles')->insert([
                'user_id'    => $userId,
                'full_name'  => 'Administrator',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create lock file
            file_put_contents(WRITEPATH . 'installed.lock', date('Y-m-d H:i:s') . "\nInstalled by: " . $username);

            // Clean session
            session()->remove('install_db_done');

            return redirect()->to('/install/complete');

        } catch (\Exception $e) {
            return redirect()->to('/install/admin')->with('error', 'Lỗi tạo tài khoản: ' . $e->getMessage());
        }
    }

    /**
     * Step 6: Complete
     */
    public function complete()
    {
        return view('install/complete');
    }

    /**
     * Check system requirements
     */
    private function checkRequirements(): array
    {
        $checks = [];

        // PHP version
        $checks['PHP >= 8.1'] = version_compare(PHP_VERSION, '8.1.0', '>=');

        // Required extensions
        $extensions = ['intl', 'json', 'mbstring', 'mysqlnd', 'curl'];
        foreach ($extensions as $ext) {
            $checks["PHP Extension: {$ext}"] = extension_loaded($ext);
        }

        // Writable directories
        $dirs = [
            'writable/'         => WRITEPATH,
            'writable/cache/'   => WRITEPATH . 'cache',
            'writable/logs/'    => WRITEPATH . 'logs',
            'writable/session/' => WRITEPATH . 'session',
            'writable/uploads/' => WRITEPATH . 'uploads',
        ];
        foreach ($dirs as $label => $path) {
            $checks["Writable: {$label}"] = is_writable($path);
        }

        // Config writable (for Database.php)
        $checks['Writable: app/Config/'] = is_writable(APPPATH . 'Config');

        // Install SQL exists
        $checks['install/database.sql exists'] = file_exists(ROOTPATH . 'install/database.sql');

        return $checks;
    }

    /**
     * Write Database.php config file
     */
    private function writeDatabaseConfig(string $hostname, string $username, string $password, string $database, string $port): void
    {
        $password = addslashes($password);

        $config = <<<PHP
<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string \$filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string \$defaultGroup = 'default';

    public array \$default = [
        'DSN'          => '',
        'hostname'     => '{$hostname}',
        'username'     => '{$username}',
        'password'     => '{$password}',
        'database'     => '{$database}',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => {$port},
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public array \$tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT === 'testing') {
            \$this->defaultGroup = 'tests';
        }
    }
}

PHP;

        file_put_contents(APPPATH . 'Config/Database.php', $config);
    }
}
