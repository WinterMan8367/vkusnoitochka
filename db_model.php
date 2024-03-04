<?
    class MysqlModel
    {
        private $server = "localhost";
        private $db = "vkusnoitochka";
        private $user = "root";
        private $pass = "";
        private $code = "";
        public $error = "";
        private $connect = false;

        function __construct(){
            $this->server = "localhost";
            $this->db = "vkusnoitochka";
            $this->user = "root";
            $this->pass = "";
        }

        function getConnect() {
            global $c;
            $this->code = '';
            $this->error = '';
            if (!$c = mysqli_connect($this->server, $this->user, $this->pass, $this->db)) {
                $this->connect = false;
                $this->error = "Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error();
            }else{
                $this->connect = true;
                mysqli_set_charset($c, "utf8");
            }
            return $this->connect;
        }

        function closeConnect() {
            global $c;
            mysqli_close($c);
            $this->connect = false;
        }

        function goResult($sql_in){
            global $c;
            if (!$this->connect) $this->GetConnect();
            if(!$result = mysqli_query($c, $sql_in)) {
                $this->error = 'MySQL error ['.mysqli_error($c).']';
                $this->closeConnect();
                return false;
            }else{
                $res = [];
                while( $row = mysqli_fetch_assoc($result) ){
                    $res[] = ($row);
                }
                $this->closeConnect();
                return $res;
            }
        }

        function goResultOnce($sql_in){
            global $c;
            if (!$this->connect) $this->GetConnect();
            if(!$result = mysqli_query($c, $sql_in)) {
                $this->error = 'MySQL error ['.mysqli_error($c).']';
                $this->closeConnect();
                return false;
            }else{
                $res = mysqli_fetch_assoc($result);
                $this->closeConnect();
                return $res;
            }
        }

        function query($sql_in){
            global $c;
            if (!$this->connect) $this->GetConnect();
            if(!$result = mysqli_query($c, $sql_in)) {
                $this->error = 'MySQL error ['.mysqli_error($c).']';
                $this->closeConnect();
                return false;
            }else{
                $this->closeConnect();
                return true;
            }

        }
    }
?>