<?php
class db
{
	 public function __construct()
	 {
		$this -> dbConnect();
	 }
	
	public function query($q)
	{
		$qq=$this -> mysqli -> query($q);
		if(!$this->mysqli->errno)
		{
		
			return $qq;
		}
		else 
		{
			return false;
		}
	}
	public function error()
	{
		return $this->mysqli->error;
	}
	public function st($s)
	{
		return $this -> mysqli -> real_escape_string($s);
	}
	 protected function dbConnect()
    {
        $this -> mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$this -> mysqli->set_charset("utf8");
	    if (mysqli_connect_errno())
        {
            die("Database connection failed".mysqli_connect_errno());
        }
        return true;
    }
	public function __destruct()
    {
        $this -> mysqli -> close();
    }
}
class users
{
	public function __construct()
	{
		if(!isset($_SESSION['token']))
		{
			$_SESSION['token'] = $this->generateToken();
		}
	}
	protected function generateToken()
    {
        return md5(uniqid('', true));
    }
	protected function cryptpassword($passw)
	{
		return crypt($passw, '$2a$10$G.owrAnx7k8gLULjO8EX0g$');
	}
	public function login($login,$passw)
	{
		$db=new db;
		$passw=$this->cryptpassword($passw);
		$login=$db->st($login);
		$passw=$db->st($passw);
		$q=$db->query("SELECT * FROM users WHERE username='$login' AND password='$passw' LIMIT 1");
		if(!$q) { die("DB ERROR"); }
		if($q->num_rows==1)
		{	
			$q->close();
			return true;
		}
		else
		{
			$q->close();
			return false;
		}
	}
	public function getToken()
	{
		return $_SESSION['token'];
	}
	public function modify ($id, $pass)
	{
		$id=intval($id);
		$pass=$this->cryptpassword($pass);
		$db= new db;
		$q=$db->query("UPDATE users SET password='$pass' WHERE id='$id'");
		if(!$q) { die("DB ERROR"); }
		return true;
	}
	public function del($id)
	{
		$db= new db;
		$id=intval($id);
		$q=$db->query("DELETE FROM users WHERE id='$id'");
		if(!$q) { die("DB ERROR"); }
		
		return true;
	}

	public function checkwelcome()
	{
		$login=$_SESSION['login'];
		$db= new db;
		$login=$db->st($login);
		$q=$db->query("SELECT id FROM users WHERE username='$login' AND welcomecheck='1' LIMIT 1");
		if(!$q) { die("DB ERROR"); }
		if($q->num_rows==1)
		{	
			$q->close();
			return true;
		}
		else
		{
			$q->close();
			return false;
		}
	}
	public function setwelcome()
	{
		$login=$_SESSION['login'];
		$db= new db;
		$login=$db->st($login);
		$q=$db->query("UPDATE users SET welcomecheck='1' WHERE username='$login'");
		if(!$q) { die("DB ERROR"); }
		return true;
	}
	public function checkusernamea($login)
	{
		$db= new db;
		$login=$db->st($login);
		$q=$db->query("SELECT id FROM users WHERE username='$login'");
		if($q->num_rows===0)
		{
			$q->close();
			return true;
		}
		else
		{
			$q->close();
			return false;
		}
	}
	public function create($name,$pass)
	{
		$db= new db;
		$name=$db->st($name);
		$pass=$this->cryptpassword($pass);
		$q=$db->query("INSERT INTO users (`username`, `password`, `welcomecheck`) VALUES ('$name', '$pass', '0')");
		if(!$q) { die("DB ERROR"); }
	}
}

class tasks
{
    public function create($name, $desp)
    {
        $db= new db;
        //$desp=str_replace( "\n", '<br />', ($desp));
        $q=$db->query("INSERT INTO zd (`name`, `text`) VALUES ('".$db->st($name)."', '".$db->st($desp)."')");
        if(!$q) { die("DB ERROR"); }
        return true;
    }
	public function modify ($id, $title, $text)
	{
		$id=intval($id);
		$db= new db;
		//$text=nl2br($text);
		$text=$db->st($text);
		$title=$db->st($title);
		$q=$db->query("UPDATE zd SET name='$title', text='$text' WHERE id='$id'");
		if(!$q) { die("DB ERROR"); }
		return true;
	}
	public function del($id)
	{
		$db= new db;
		$id=intval($id);
		$q=$db->query("DELETE FROM zd WHERE id='$id'");
		if(!$q) { die("DB ERROR"); }
		
		return true;
	}
    public function check($login)
	{
      $db= new db;
		$login=intval($login);
		$q=$db->query("SELECT name FROM zd WHERE id='$login'");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
			$q->close();
			return true;
		}  
    }
    public function getmaxandname($id)
    {
          $db= new db;
		$id=intval($id);
		$q=$db->query("SELECT author, prc FROM rh WHERE toid=$id ORDER BY prc DESC");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
            $name=$q->fetch_assoc();
			$q->close();
			return $name;
		}  
    }
    public function checksol($login)
	{
      $db= new db;
		$login=intval($login);
		$q=$db->query("SELECT prc FROM rh WHERE id='$login'");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
			$q->close();
			return true;
		}  
    }
    public function getname($id)
    {
         $db= new db;
		$id=intval($id);
		$q=$db->query("SELECT name FROM zd WHERE id='$id'");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
            $name=$q->fetch_assoc();
			$q->close();
			return $name['name'];
		}  
    }
    public function addsol($id, $num, $code, $ntext, $author,$text)
    {
        $db= new db;
        $num=(intval($num)>100)?100:intval($num);
		$q=$db->query("INSERT INTO rh (`toid`, `code`, `desp`, `despn`, `author`, `prc`) VALUES (".intval($id).", '".$db->st($code)."', '".$db->st($text)."', '".$db->st($ntext)."', '".$db->st($author)."', $num)");
        if(!$q) { die("DB ERROR"); }
    }
    public function updsol($id, $num, $code, $ntext, $author,$text)
    {
        $db= new db;
        $num=(intval($num)>100)?100:intval($num);
		$q=$db->query("UPDATE rh SET code='".$db->st($code)."', desp='".$db->st($text)."', despn='".$db->st($ntext)."', author='".$db->st($author)."', prc=$num WHERE id=$id");
        if(!$q) { die("DB ERROR"); }
    }
    public function delsol($id)
    {
        $db= new db;
		$id=intval($id);
		$q=$db->query("DELETE FROM rh WHERE id=$id");
		if(!$q) { die("DB ERROR"); }
		
		return true;
    }
    public function getnumofsols($id)
    {
        $db= new db;
        $id=intval($id);
        $q=$db->query("SELECT sps FROM rh WHERE toid=$id");
        $r=$q->num_rows;
        $q->close();
        return $r;
    }
    public function getauthorsol($id)
    {
        $db= new db;
		$id=intval($id);
		$q=$db->query("SELECT author FROM rh WHERE id=$id");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
            $name=$q->fetch_assoc();
			$q->close();
			return $name['author'];
		}  
    }
     public function getsoltoid($id)
     {
        $db= new db;
		$id=intval($id);
		$q=$db->query("SELECT toid FROM rh WHERE id=$id");
		if($q->num_rows===0)
		{
			$q->close();
			return false;
		}
		else
		{
            $name=$q->fetch_assoc();
			$q->close();
			return $name['toid'];
		}  
     }
}
class comments
{
    public function add($type, $subj, $cachesubj, $user, $id)
    {
        if ($type=="tasks")
        {
            $db= new db;
            $subj=$db->st($subj);
            $cachesubj=$db->st($cachesubj);
            $user=$db->st($user);
            $id=intval($id);
            $db= new db;
            $q=$db->query("INSERT INTO comments (`type`, `comm`, `cachecomm`, `author`, `toid`) VALUES ('tasks', '$subj', '$cachesubj', '$user', '$id')");
            if(!$q) { die("DB ERROR"); }
        }
        if ($type=='sol')
        {
            $db= new db;
            $subj=$db->st($subj);
            $cachesubj=$db->st($cachesubj);
            $user=$db->st($user);
            $id=intval($id);
            $db= new db;
            $q=$db->query("INSERT INTO comments (`type`, `comm`, `cachecomm`, `author`, `toid`) VALUES ('sol', '$subj', '$cachesubj', '$user', '$id')");
            if(!$q) { die("DB ERROR"); }
        }
        return true;
    }
}
