<?php
class Banco {
    private static $dbNome = 'db_beauty_hub';
    private static $dbHost = 'localhost';
    private static $dbUsuario = 'root';
    private static $dbSenha = '';
    private static $cont = null;

    public static function conectar() {
        if (null == self::$cont) {
            try {
                self::$cont = new PDO(
                    "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbNome,
                    self::$dbUsuario,
                    self::$dbSenha
                );
            } catch (PDOException $exception) {
                die("Erro na conexão: " . $exception->getMessage());
            }
        }
        return self::$cont;
    }

    public static function desconectar() {
        self::$cont = null;
    }
}
?>
