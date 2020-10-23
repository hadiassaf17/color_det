<?php
define('MAIN_CTRL','WEB');  require_once("base.php");
class WEB extends PDemiaBaseMVC{
    public static function StartWeb(){
        try {
            include(join(DIRECTORY_SEPARATOR, array(Classes,"fct.php" )));//contain p
            include(join(DIRECTORY_SEPARATOR, array(Classes,"set.php" )));//contain basic define
            include(join(DIRECTORY_SEPARATOR, array(Classes,"UTIL.php" )));//utilities for helping
            include(join(DIRECTORY_SEPARATOR, array(Classes,"DAL.php" )));//data access layer
            include(join(DIRECTORY_SEPARATOR, array(Classes,"SimpleImage.php" )));//contain SimpleImage class
            define('URI',self::getURI());
            define('URI1',self::getURI1(URI));
            define('URI2',self::getURI2(URI));
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
        if($f == null) $f = URI1;
        $view =  join(DIRECTORY_SEPARATOR, array(Views,"Main",$f.".php" ));
        $resource = $_SERVER['REQUEST_URI'];
        $resource = str_replace("index.php/","",$resource);
        $resource = str_replace("/index.php","",$resource);
        $resource = str_replace("index.php","",$resource);
        $resource = str_replace("/Assets","Assets",$resource);
        if(strpos($f,"?") > -1){
            $f = substr($f,0, strpos($f,"?"));        
        }
        if(method_exists(new WEB(),$f)){self::$f();}
        elseif(file_exists($view)){include($view);}
        else{ self::home(); }
    }
    public static function HPOST($f = null){
        $URI = explode("/",URI);
        if($f == null) $f = (isset($URI[IX]) && $URI[IX]!="") ? $URI[IX] : "";
        if(method_exists(new WEB(),$f)){self::$f();}
        else{
            $f = explode("/",$_POST[key($_POST)])[0];
            if(method_exists(new WEB(),$f)){self::$f();}
            else{echo "POST/$f not found";p($_POST);}
        }
    }
    public static function getImageUrl($name){
        $ext = UTIL::ext($name);
        $img_url = SELF_DIR."Assets/Images/$ext/$name";
        return $img_url;
    }
    public static function Images(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 : 0;
        $Images = Assets."/Images";
        $Images = str_replace("\\","/",$Images);
        if(!isset($f[$IX])){
            p($Images);
            p($f);
        }
        else{

        }
    }
    //CUSTOM WEB PAGES
    public static function home(){
        self::load_view("Main","home");
    }
    public static function login($f = null){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(GORP == "GET"){
            include join(DIRECTORY_SEPARATOR, array(Views,"Main","login.php" ));
        }
        elseif(isset($f[1]) && $f[1] == "submit"){
            if(!isset($_SESSION[SI]['login_attempt'])) $_SESSION[SI]['login_attempt'] = 1;
            $usr = $_POST['email'];
            $pas = $_POST['password'];
            $d= DAL::call_sp("select * from account where email=:usr or name=:usr",[
                ["k"=>"usr","v"=>$usr]
            ]);
            if(count($d) > 0){
                $d = $d[0];
                if($d["password"] == md5($pas.$d['salt'])){ //validate the hash of the password
                    $_SESSION[SI]['user'] = $d;
                    p('login successful');
                    echo '<script>location="'.SELF_DIR.'admin";</script>';
                }
                else {
                    p($_POST['password']);
                    p($d["password"]);
                    p(md5($pas.$d['salt']));
                    echo '<script>SYS.dialog("wrong password","Error");</script>';
                }
            }
            else echo '<script>SYS.dialog("'.$usr.' is not registered","Error");</script>';
        }
    }
    public static function register(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        if(GORP == "GET"){
            include join(DIRECTORY_SEPARATOR, array(Views,"Part","head.php" ));
            include join(DIRECTORY_SEPARATOR, array(Views,"Part","nav.php" ));
        }
        if(!isset($f[$IX])){
            echo '<div class="container RegisterFormContaienr">';
            $html = DAL::getFormForTable("account",[],
            ["salt","tstp","active","active_verified","html_about","enum_gender","active_enabled","account_role_fk"]
            ,"register/submit");
            echo $html;
            echo '</div>';        
        }
        elseif($f[$IX]=="submit"){
            $d = $_POST;
            $table = $d['table'];
            unset($d['key']);
            unset($d['table']);
            if($table == "account"){
                $exist = DAL::call_sp("select count(*) exist from account where email=:email",[
                    ["k"=>"email","v"=>$d['email']]
                ]);
                if($exist[0]['exist'] > 0){
                    die("email used by another account use to login");
                }
                $time = time();
                $d['salt'] = md5($time);
                $d['password'] = md5($d['password'].$d['salt']);
                $r = DAL::insert($table,$d);
                if($f > 0){
                    p("account registered");
                }
                else{
                    p("account not registered");
                }
            }
        }
        else{
            die("unknown request $f");
        }
    }
    public static function colors(){
        $f = (GORP == "GET") ? explode("/",URI) : explode("/",$_POST[key($_POST)]);
        $IX = (GORP == "GET") ? IX+1 :1;
        function instructions(){
            $d = DAL::call_sp("select html from web_pages where name='image_color_detect_instructions'");
            if(isset($d[0]['html'])) echo $d[0]['html'];
        }
        function new_image(){
            $html = DAL::getFormForTable("stored_image",[],
            ["active","active_read"]
            ,"colors/new_image_submit");
            echo $html;
        }
        function new_image_submit(){
            $d = $_POST;
            $t = $d['table'];
            unset($d['key']);
            unset($d['table']);
            foreach($d as $e=>$v){
                if($v == ""){
                    die("missing $e");
                }
            }
            $r = DAL::insert($t,$d);
            if($r > 0){
                p("inserted");
                echo '<script>$("input").parents("form")[0].reset();</script>';
            }
            else{
                p("fail to insert image data");
            }
        }
        function load_images(){
            $d = DAL::call_sp("select * from stored_image where active = 1");
            echo '<table class="table" id="table"><thead>
            <th>name</th>
            <th>image</th>
            <th>option</th>
            </thead><tbody>';
            foreach($d as $r){
                echo '<tr>';
                echo '<td>'.$r["name"].'</td>';
                echo '<td><img src="' . WEB::getImageUrl($r["image"]) .'" height=60></td>';
                echo '<td>';
                
                
                if($r["active_read"] == 1){
                    echo '<button class="btn" onclick="SYS.LoadXHR(\'CT1\',\'colors/load_image_pixels/'.$r["id"].'\');" >load from db</button>';
                }
                else{
                    echo '<button class="btn" onclick="SYS.LoadXHR(\'CT1\',\'colors/read_image_pixels/'.$r["id"].'\');" >read through php</button>';
                    //echo '<button class="btn" onclick="SYS.py_read_pixels(\'CT1\',\''.$r["id"].'\',\''.$r["image"].'\');" >read now</button>';
                    //echo '<button class="btn" onclick="SYS.py_read_pixels(\'CT1\',\''.$r["id"].'\',\''.$r["image"].'\');" >read now</button>';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>
            <script>$("#table").DataTable();</script>
            <div id="CT1" class="CT1"></div>
            ';
        }
        function read_image_pixels($f){
            $id = $f[2];
            $d = DAL::call_sp("select image,active_read from stored_image where id = :id",[
                ["k"=>"id","v"=>$id]
            ]);
            if(count($d) == 0) die("image removed");
            $d= $d[0];
            if($d["active_read"] == 1){
                die("image has been read already");
            }
            $d = $d['image'];
            $ext = UTIL::ext($d);
            $loc = Assets."/Images/$ext/$d";
            $loc = str_replace("\\","/",$loc);
            if(!file_exists($loc)){die(p("image exist"));}
            $image = new SimpleImage();
            $image->fromFile($loc);
            $image->fitToHeight(25);
            $w = $image->getWidth();
            $h = $image->getHeight();
            for($i = 0 ; $i < $w; $i++){
                for($j =0 ; $j < $h; $j++){
                    $rgba = $image->getColorAt($i,$j);
                    try{
                        $data = [
                            "r"=>$rgba["red"],
                            "g"=>$rgba["green"],
                            "b"=>$rgba["blue"],
                            "a"=>$rgba["alpha"],
                        ];
                        $rgba_list_id = DAL::call_sp("select id from rgba_list where r=:r and g=:g and b=:b and a=:a limit 1",[
                            ["k"=>"r","v"=>$data["r"]],
                            ["k"=>"g","v"=>$data["g"]],
                            ["k"=>"b","v"=>$data["b"]],
                            ["k"=>"a","v"=>$data["a"]]
                        ]);
                        
                        if(count($rgba_list_id) == 0){
                            $rgba_list_id = DAL::insert("rgba_list",$data);
                        }
                        else{
                            $rgba_list_id = $rgba_list_id[0]["id"];
                        }
                        $data = [
                            "stored_image_fk"=>$id,
                            "rgba_list_fk"=>$rgba_list_id
                        ];
                        
                        $exist_rgba = DAL::call_sp("select id from image_rgba where stored_image_fk=:stored_image_fk and rgba_list_fk=:rgba_list_fk",[
                            ["k"=>"stored_image_fk","v"=>$data["stored_image_fk"]],
                            ["k"=>"rgba_list_fk","v"=>$data["rgba_list_fk"]]
                        ]);
                        if(count($exist_rgba) == 0){
                            DAL::insert("image_rgba",$data);
                        }
                    }
                    catch(Error $e){/* ignore */}
                    //break;
                }
                //break;
            }
            //die("pre update");
            $d = [
                "active_read"=>1
            ];
            DAL::update("stored_image",$d,"id",$id);
            p("done");
            echo '<script>SYS.LoadXHR(\'CT1\',\'colors/load_image_pixels/'.$id.'\');</script>';
        }
        function load_image_pixels($f){
            $id = $f[2];
            $d = DAL::call_sp("SELECT `rgba_list`. * 
            FROM `image_rgba`, `rgba_list` 
            where `image_rgba`.`rgba_list_fk`=`rgba_list`.`id` and stored_image_fk=:stored_image_fk limit 10",[
                ["k"=>"stored_image_fk","v"=>$id]
            ]);
            $color_names = DAL::call_sp("select id,name from colors_names");
            $rgba_color_map = DAL::call_sp("select * from rgba_color_map");
            echo '<script> SYS.RGBALISTFORIMAGE = '.json_encode($d).';</script>';
            echo '<script> SYS.COLORNAMES = '.json_encode($color_names).';</script>';
            echo '<script> SYS.RGBACOLORMAP = '.json_encode($rgba_color_map).';</script>';
            echo '<div id="color_table_container" class="container"></div>';
            echo '<script>
                // console.log(SYS.RGBALISTFORIMAGE);
                // console.log(SYS.COLORNAMES);
                // console.log(SYS.RGBACOLORMAP);
                SYS.initializeRGBTable(`#color_table_container`);
            </script>';
            
        }
        function add_color_rgb_map(){
            $rgbid =$_POST['rgbid'];
            $color = $_POST['color'];
            $data = [
                "colors_names_fk"=>$_POST['color'],
                "rgba_list_fk"=>$_POST['rgbid']
            ];
            $r = DAL::call_sp("select id from rgba_color_map where colors_names_fk=:colors_names_fk and rgba_list_fk=:rgba_list_fk",[
                ["k"=>"colors_names_fk","v"=>$data["colors_names_fk"]],
                ["k"=>"rgba_list_fk","v"=>$data["rgba_list_fk"]]
            ]);
            if(count($r) ==0){
               $r = DAL::insert("rgba_color_map",$data);
               echo 'Added';
            }
            else{
                echo 'Exists';
            }
        }
        if(!isset($f[$IX])){
            p($IX);
            p($f);
        }
        elseif(function_exists($f[$IX])){
            $f[$IX]($f);
        }
        else{
            p($IX);
            p($f);
        }
    }
    



}
?>