<?php


require_once(__DIR__ . "/IpaAnalysis.php");

class DatabaseManager extends SQLite3
{
    private static $instance = null;


    public function __construct()
    {
        // インスタンス作成時には何もしない。
        // シングルトンにする
        // SQLite3はファイル管理なので、MySQLみたいに接続管理しないので、複数 open しようとしてもロックされている
    }

    public function __destruct()
    {
    }


    public static function sharedInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();

            // 初回は、db open とテーブルの確認後作成動作
            $dataBaseDirectory = __DIR__ . '/../data/';
            if (!file_exists($dataBaseDirectory)) {
                if (!mkdir($dataBaseDirectory, 0755, TRUE)) {
                    throw new RuntimeException('アプリディレクトリの作成に失敗しました');
                }
            }
            self::$instance->open($dataBaseDirectory. 'list.db');
            $adhocTableCount = self::$instance->querySingle("select count(*) as count from sqlite_master where type='table' and name='adhoc'", true);
            if ($adhocTableCount["count"] == 0) {
                self::$instance->exec('CREATE TABLE adhoc (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, directoryName TEXT UNIQUE NOT NULL, ipa TEXT NOT NULL, ipaTmpHash TEXT NOT NULL, notes TEXT, developerNotes TEXT, ipaBundleIdentifier TEXT, ipaVersion TEXT, ipaBuild TEXT, infoPlistXml TEXT , isInvalidBackground INTEGER NOT NULL, isHide INTEGER NOT NULL, sortOrder INTEGER NOT NULL, isDelete INTEGER NOT NULL, createDate DATETIME NOT NULL, mobileprovisionXml TEXT, expirationDate DATETIME)');
            }
            $downloadTableCount = self::$instance->querySingle("select count(*) as count from sqlite_master where type='table' and name='download'", true);
            if ($downloadTableCount["count"] == 0) {
                self::$instance->exec('CREATE TABLE download (id INTEGER PRIMARY KEY AUTOINCREMENT, directoryName TEXT NOT NULL, ipaFileName TEXT NOT NULL, userAgent TEXT NOT NULL, downloadType INTEGER NOT NULL, ipAddress TEXT NOT NULL, createDate DATETIME NOT NULL)');
            }

            self::migrationsVer110();
        }

        return self::$instance;
    }

    private static function migrationsVer110() {

        // Migrations ver 1.1.0
        $sqliteResult = self::$instance->query("PRAGMA table_info(adhoc)");
        $adhocColumnArray = array();
        while ($result = $sqliteResult->fetchArray(SQLITE3_ASSOC)) {
            $adhocColumnArray[$result['name']] = $result['name'];
        }

        $isMigrations = true;
        // transaction を行わずに １つずつカラムを追加して、最後にカラムの確認を行う
        if(!isset($adhocColumnArray['mobileprovisionXml'])) {
            self::$instance->exec('ALTER TABLE adhoc ADD COLUMN mobileprovisionXml TEXT');
            $isMigrations = true;
        }
        if(!isset($adhocColumnArray['expirationDate'])) {
            self::$instance->exec('ALTER TABLE adhoc ADD COLUMN expirationDate DATETIME');
            $isMigrations = true;
        }
        if ($isMigrations) {
            $sqliteResult = self::$instance->query("PRAGMA table_info(adhoc)");
            $adhocColumnArray = array();
            while ($result = $sqliteResult->fetchArray(SQLITE3_ASSOC)) {
                $adhocColumnArray[$result['name']] = $result['name'];
            }

            if(!isset($adhocColumnArray['mobileprovisionXml']) || !isset($adhocColumnArray['expirationDate'])) {
                throw new Exception('Migrations 処理に失敗しました。 ver 1.1.0');
            }

            $datas = self::$instance->adhocSelectAll();

            self::$instance->query("BEGIN;");
            $isSuccess = true;
            foreach ($datas as $data) {
                $id = $data['id'];
                $filePath = __DIR__ . '/../data/app/' . $data['directoryName'] . '/' . $data['ipaTmpHash'];
                $ipa = new IpaAnalysis($filePath);
                $mobileprovisionArrayAndXml = $ipa->getMobileprovisionArrayAndXml();
                if (array_key_exists('ExpirationDate', $mobileprovisionArrayAndXml['array'])) {
                    $expirationDate = date('Y-m-d H:i:s', $mobileprovisionArrayAndXml['array']['ExpirationDate']);
                }
                $dbUpdateResult = self::$instance->adhocMigrationsVer110Update($id, $mobileprovisionArrayAndXml['xml'], $expirationDate);
                if ($dbUpdateResult === False) {
                    $isSuccess = false;
                }
            }
            if($isSuccess) {
                self::$instance->query("COMMIT;");
            } else {
                self::$instance->query("ROLLBACK;");
            }

        }

    }


    protected function getDatabaseNowDate()
    {
        date_default_timezone_set('Asia/Tokyo');
        return date('Y-m-d H:i:s');
    }


    public function adhocInsert(
        $title,
        $directoryName,
        $ipa,
        $ipaTmpHash,
        $notes,
        $developerNotes,
        $ipaBundleIdentifier,
        $ipaVersion,
        $ipaBuild,
        $infoPlistXml,
        $isHide,
        $mobileprovisionXml,
        $expirationDate)
    {
        $columnIdRow = $this->querySingle('select id from adhoc order by id desc limit 1');
        $sortOrder = 1;
        if (!is_null($columnIdRow) && $columnIdRow !== false) {
            $sortOrder = $columnIdRow + 1;
        }

        $date = $this->getDatabaseNowDate();
        $insertSql = <<<EOT
INSERT INTO adhoc
(
    title, 
    directoryName, 
    ipa, 
    ipaTmpHash, 
    notes, 
    developerNotes, 
    ipaBundleIdentifier, 
    ipaVersion, 
    ipaBuild, 
    infoPlistXml, 
    isInvalidBackground, 
    isHide, 
    sortOrder, 
    isDelete, 
    createDate, 
    mobileprovisionXml, 
    expirationDate
)
VALUES
(
    :title, 
    :directoryName, 
    :ipa, 
    :ipaTmpHash, 
    :notes, 
    :developerNotes, 
    :ipaBundleIdentifier, 
    :ipaVersion, 
    :ipaBuild, 
    :infoPlistXml, 
    :isInvalidBackground, 
    :isHide, 
    :sortOrder, 
    :isDelete, 
    :createDate, 
    :mobileprovisionXml, 
    :expirationDate
)
EOT;

        $stmt = $this->prepare($insertSql);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':directoryName', $directoryName, SQLITE3_TEXT);
        $stmt->bindValue(':ipa', $ipa, SQLITE3_TEXT);
        $stmt->bindValue(':ipaTmpHash', $ipaTmpHash, SQLITE3_TEXT);
        $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
        $stmt->bindValue(':developerNotes', $developerNotes, SQLITE3_TEXT);
        $stmt->bindValue(':ipaBundleIdentifier', $ipaBundleIdentifier, SQLITE3_TEXT);
        $stmt->bindValue(':ipaVersion', $ipaVersion, SQLITE3_TEXT);
        $stmt->bindValue(':ipaBuild', $ipaBuild, SQLITE3_TEXT);
        $stmt->bindValue(':infoPlistXml', $infoPlistXml, SQLITE3_TEXT);
        $stmt->bindValue(':isInvalidBackground', 0);
        $stmt->bindValue(':isHide', $isHide);
        $stmt->bindValue(':sortOrder', $sortOrder);
        $stmt->bindValue(':isDelete', 0);
        $stmt->bindValue(':createDate', $date, SQLITE3_TEXT);
        $stmt->bindValue(':mobileprovisionXml', $mobileprovisionXml, SQLITE3_TEXT);
        $stmt->bindValue(':expirationDate', $expirationDate, SQLITE3_TEXT);
        $result = $stmt->execute();

        if ($result === false) {
            return false;
        } else {
            $idNumber = $this->querySingle("select id from adhoc where ROWID = last_insert_rowid()");
            return $idNumber;
        }
    }

    public function adhocSelectAll()
    {
        $sqliteResult = $this->query('SELECT * FROM adhoc WHERE isDelete = 0 ORDER BY sortOrder DESC, id DESC');
        $resultArray = array();

        while ($result = $sqliteResult->fetchArray(SQLITE3_ASSOC)) {
            $resultArray[] = $result;
        }

        return $resultArray;
    }

    public function adhocSelect($directoryName)
    {
        $stmt = $this->prepare('SELECT * FROM adhoc WHERE directoryName = :directoryName');
        $stmt->bindValue(':directoryName', $directoryName);
        $sqliteResult = $stmt->execute()->fetchArray();

        return $sqliteResult;
    }

    public function adhocUpdate($id, $title, $notes, $developerNotes, $isInvalidBackground, $isHide, $sortOrder) {

        $stmt = $this->prepare('UPDATE adhoc SET title = :title, notes = :notes, developerNotes = :developerNotes, isInvalidBackground = :isInvalidBackground, isHide = :isHide, sortOrder = :sortOrder WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
        $stmt->bindValue(':developerNotes', $developerNotes, SQLITE3_TEXT);
        $stmt->bindValue(':isInvalidBackground', $isInvalidBackground);
        $stmt->bindValue(':isHide', $isHide);
        $stmt->bindValue(':sortOrder', $sortOrder);
        $sqliteResult = $stmt->execute();

        return $sqliteResult;
    }

    private function adhocMigrationsVer110Update($id, $mobileprovisionXml, $expirationDate) {

        $stmt = $this->prepare('UPDATE adhoc SET mobileprovisionXml = :mobileprovisionXml, expirationDate = :expirationDate WHERE id = :id');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':mobileprovisionXml', $mobileprovisionXml, SQLITE3_TEXT);
        $stmt->bindValue(':expirationDate', $expirationDate, SQLITE3_TEXT);
        $sqliteResult = $stmt->execute();

        return $sqliteResult;
    }

    public function adhocDelete($dir) {

        $stmt = $this->prepare('UPDATE adhoc SET isDelete = :isDelete WHERE directoryName = :dir');
        $stmt->bindValue(':isDelete', 1);
        $stmt->bindValue(':dir', $dir);
        $sqliteResult = $stmt->execute();

        return $sqliteResult;
    }

    public function downloadInsert($directoryName, $ipaTmpHash, $userAgent, $downloadType, $ipAddress)
    {
        $date = $this->getDatabaseNowDate();
        $stmt = $this->prepare('INSERT INTO download (directoryName, ipaFileName, userAgent, downloadType, ipAddress, createDate) VALUES (:directoryName, :ipaTmpHash, :userAgent, :downloadType, :ipAddress, :createDate)');
        $stmt->bindValue(':directoryName', $directoryName, SQLITE3_TEXT);
        $stmt->bindValue(':ipaTmpHash', $ipaTmpHash, SQLITE3_TEXT);
        $stmt->bindValue(':userAgent', $userAgent, SQLITE3_TEXT);
        $stmt->bindValue(':downloadType', (Int)$downloadType);
        $stmt->bindValue(':ipAddress', $ipAddress, SQLITE3_TEXT);
        $stmt->bindValue(':createDate', $date, SQLITE3_TEXT);
        $result = $stmt->execute();

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    public function downloadSelectAll()
    {
        $sqliteResult = $this->query('SELECT a.id, a.title, a.ipa, a.ipaBundleIdentifier, a.ipaVersion, a.ipaBuild, a.isDelete, d.userAgent, d.downloadType, d.ipAddress, d.createDate FROM adhoc AS a INNER JOIN download AS d ON a.directoryName = d.directoryName AND a.ipaTmpHash = d.ipaFileName ORDER BY d.createDate DESC');
        $resultArray = array();

        while ($result = $sqliteResult->fetchArray(SQLITE3_ASSOC)) {
            $resultArray[] = $result;
        }

        return $resultArray;
    }

} 