<?php
/* 
    @AUTHOR Mohammad Jawad Zein El Deen
    @Email  mjzd00@gmail.com
    @Email  mohammad0jawad@gmail.com
    @WEBSITE pdemia.com
    @WEBSITE jawad.online
*/
class PDemiaBaseMVC{
    public static function getURI(){
        $URI = trim($_SERVER['REQUEST_URI'],"/");
        $URI = str_replace(" ","_",$URI);
        $URI = str_replace(".php","",$URI);
        $URI = str_replace("%20","_",$URI);
        return $URI;
    }
    public static function getURI1($URI_str){
        $URI = explode("/",$URI_str);
        $f = (isset($URI[IX]) && $URI[IX] !="") ? $URI[IX] : "home";
        if(strpos($f,"?") > -1){
            $f = substr($f,0,strpos($f,"?"));
        }
        return $f;
    }
    public static function getURI2($URI_str){
        $URI = explode("/",$URI_str);
        $f = (isset($URI[IX+1]) && $URI[IX+1] !="") ? $URI[IX+1] : "";
        if(strpos($f,"?") > -1){
            $f = substr($f,0,strpos($f,"?"));
        }
        return $f;
    }
    public static function startWeb(){
        try {
            include(join(DIRECTORY_SEPARATOR, array(Classes,"fct.php" )));//contain p
            include(join(DIRECTORY_SEPARATOR, array(Classes,"set.php" )));//contain basic define
            include(join(DIRECTORY_SEPARATOR, array(Classes,"UTIL.php" )));//utilities for helping
            include(join(DIRECTORY_SEPARATOR, array(Classes,"DAL.php" )));//data access layer
            define('URI',self::getURI());
            define('URI1',self::getURI1(URI));
            define('URI2',self::getURI2(URI));
            define('LOGO',(LANG == "en") ? "logo.png" : "logo_ar.png");
            ($_SERVER['REQUEST_METHOD'] == 'GET') ? self::HGET() : self::HPOST();
        }catch(PDOException $e){
            if(ADMIN) {p($e->getFile() . $e->getLine() . $e->getMessage() );}
            else  p('Please Contact ADMIN');
		}catch(Error $e){
			if(ADMIN) {p($e->getFile() . $e->getLine() . $e->getMessage() );}
            else  p('Please Contact ADMIN');
		}
    }
    public static function HGET($f = null){
        if(isset($_GET['switchLang'])){
            $_SESSION[SI]['lang'] = ($_SESSION[SI]['lang'] == "en") ? "ar":"en";
            echo '<script>location = "'.SELF_DIR.'";</script>';
            die();
        }
        if($f == null) $f = URI1;
        $view =  join(DIRECTORY_SEPARATOR, array(Views,"main",$f.".php" ));
        $class = MAIN_CTRL;
        $obj = new $class;
        if($f == "Assets"){
            $URI = trim($_SERVER['REQUEST_URI'],"/");
            $r = str_replace("Assets/assets","Assets/AdminAssets",$URI);
            $rl =  substr($r,strpos($r,"Assets/AdminAssets"));
            if(file_exists($rl)){header('Location: '.SELF_DIR.$rl );}else{die();}
        }
        elseif(method_exists($obj,$f)){$class::$f();}
        elseif(file_exists($view)){include($view);}
        elseif(file_exists(Controllers."/$f.php")){include Controllers."/$f.php"; $f::startWeb(); }
        else{ $class::error();}
    }
    public static function HPOST($f = null){
        $URI = explode("/",URI);
        if($f == null) $f = (isset($URI[IX]) && $URI[IX]!="") ? $URI[IX] : "";
        $class = MAIN_CTRL;
        $obj = new $class;
        if(method_exists($obj,$f)){$class::$f();}
        else{
            $f = explode("/",$_POST[key($_POST)])[0];
            if(method_exists($obj,$f)){$class::$f();}
            elseif(file_exists(Controllers."/$f.php")){include Controllers."/$f.php"; $f::startWeb(); }
            else{echo "POST/$f not found";p($_POST);}
        }
    }
    public static function load_view($dir = "Main",$view){
        $view_path = join(DIRECTORY_SEPARATOR, array(Views,$dir,"$view.php" ));
        if(file_exists($view_path)){
            include $view_path;
        }
        else{
            echo "$view removed";
        }
    }
    public static function getImageUrl($name){
        $ext = UTIL::ext($name);
        $img_url = SELF_DIR."Assets/Images/$ext/$name";
        return $img_url;
    }
    public static function includeSVG($name){
        $name = str_replace(".svg","",$name);
        $path = join(DIRECTORY_SEPARATOR, array(getcwd(),"Assets","Images","svg","$name.svg" ));
        if(file_exists($path)){
            include $path;
        }
        else{
        }
    }
    public static function getCfg(){
        $fileName = join(DIRECTORY_SEPARATOR, array(Classes,"cst.php" ));
        if(!file_exists($fileName)){
            return [];
        }
        else{
            $str = file_get_contents($fileName);
            $str = base64_decode($str);
            $cnf_class = json_decode($str);
            $cnf = [];
            foreach($cnf_class as $k=>$v){
                $cnf[$k]=$v;
            }
            return $cnf;
        }
    }
    public static function logout(){
        session_destroy(); echo '<script>location="'.SELF_DIR.'";</script>';
    }
    public static function error(){
        self::load_view("Part","head");
        self::load_view("Part","nav");
        echo '<div class="container"><h1>requested page is not found</h1></div>';
        self::load_view("Part","foot");
    }
    public static function uploadImage(){
        $file = $_FILES['image'];
        if(file_exists($file['tmp_name'])){
            $ext = UTIL::ext($file['name']);
            $folder = join(DIRECTORY_SEPARATOR, array(Assets,"Images",$ext));
            $destination = join(DIRECTORY_SEPARATOR, array($folder,$file['name']));
            if(!is_dir($folder)){
                mkdir($folder);
            }
            $url = SELF_DIR . "Assets/Images/$ext/" . $file['name'];
            if(file_exists($destination)){
                if(md5_file($file['tmp_name']) == md5_file($destination)){
                    p("file with same name and content exist <a href='$url' target='_blank' >$url</a>");
                }
                else{
                    p("file with same name but different content exist <a href='$url' target='_blank' >$url</a>");
                    if(move_uploaded_file($file['tmp_name'],$destination)){
                        p("uploaded to <a href='$url' target='_blank' >$url</a>");
                    }
                    else{
                        p("could not upload file");
                    }
                }
            }
            else{
                if(move_uploaded_file($file['tmp_name'],$destination)){
                    p("uploaded to <a href='$url' target='_blank' >$url</a>");
                }
                else{
                    p("could not upload file");
                }
            }
        }
        else{
            p("could not upload file");
        }
    }
    public static function DALImageUpload($f = ""){
        $file = $_FILES['image'];
        if(file_exists($file['tmp_name'])){
            $ext = UTIL::ext($file['name']);
            $folder = join(DIRECTORY_SEPARATOR, array(Assets,"Images",$ext));
            $destination = join(DIRECTORY_SEPARATOR, array($folder,$file['name']));
            if(!is_dir($folder)){
                mkdir($folder);
            }
            $url = SELF_DIR . "Assets/Images/$ext/" . $file['name'];
            if(file_exists($destination)){
                echo json_encode(["result"=>true,"name"=>$file['name'],"url"=>$url]);
            }
            else{
                if(move_uploaded_file($file['tmp_name'],$destination)){
                    echo json_encode(["result"=>true,"name"=>$file['name'],"url"=>$url]);
                }
                else{
                    echo json_encode(["result"=>false,"msg"=>"could not upload file"]);
                }
            }
        }
        else{
            echo json_encode(["result"=>false,"msg"=>"could not upload file"]);
        }
    }
    public static function serialNumberParse($i){
        if($i > 1000)           return "$i";
        elseif($i > 100)        return "0$i";
        elseif($i > 10)         return "00$i";
        else                    return "000$i";
    }
    public static function getSelf(){return get_class();}
    public static function CALLM($m){self::$m();}
}
?>