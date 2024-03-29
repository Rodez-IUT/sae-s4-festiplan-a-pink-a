<?php
    use PHPUnit\Framework\TestCase;
    require_once('api/service/fonctionsBd.php');
    class TestPdo extends TestCase
    {
        protected static PDO $pdo;

        protected function setUp(): void
        {
            //Given a pdo connection poitning to the test database and the database is filled with the test data in
            // the file BD_SAE_TEST.sql
            try {
                $host = "SAE_S3_DevWeb_db";
                $port = '3306';
                $dbName = 'festiplanbfgi_sae_test';
                $user = 'root';
                $pass = 'root';
                $charset = 'utf8mb4';
                $ds_name = "mysql:host=$host;port=$port;dbname=$dbName;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => true
                ];
                self::$pdo = new PDO($ds_name, $user, $pass, $options);
                $contenu = file_get_contents("./BD_SAE_TEST.sql");
                if (!empty($contenu)) {
                    self::$pdo->exec($contenu);
                }
            } catch(\PDOException $e) {
                $this->fail("PDO error in setUp: " . $e->getMessage());
            }
        }
        public function testGetListeFestival(): void
        {
            //Given a pdo connection pointing to the test database and the database is filled with the test data in
            // the file BD_SAE_TEST.sql and the array of expected values of festivals
            $excpectedValues = [
                [1, 'Festival de musique', 'Festival de musique', '2020-06-01', '2020-06-06', 'musique.jpg'],
                [2, 'Festival de théatre', 'Festival de théatre', '2020-06-02', '2020-06-07', 'theatre.jpg'],
                [3, 'Festival de cirque', 'Festival de cirque', '2020-06-03', '2020-06-08', 'cirque.jpg'],
                [4, 'Festival de danse', 'Festival de danse', '2020-06-04', '2020-06-09', 'danse.jpg'],
                [5, 'Festival de projection de film', 'Festival de projection de film', '2020-06-05', '2020-06-10', 'film.jpg']
            ];
            //When we call the function getListeFestival
            $listeFestival = getListeFestival(self::$pdo, 1);
            //Then the function returns the excpected values of festivals
            $this->assertEquals(5, count($listeFestival));

        }


    }
?>
