<?php
include 'config.php';
include 'func.php';
//session_start(); 
//if(!isset($_SESSION['logged']))
//{
//	$_SESSION['logged']=0;
//}
//if($_SESSION['logged']!=1&&(!$no_visible_elements||!$loginpage))
//{
//	header('Location: login.php'); exit;
//}
//require_once('func.php');
$user=new users;

echo '<script src="ckeditor/ckeditor.js"></script>'; require('header.php');
    //if($_SESSION['login']!=="kolyanok") { die("??"); }
    if($_POST['ntext']&&$_POST['title']&&$user->getToken()===$_POST['token'])
	{
		require_once('htmlpurifier/library/HTMLPurifier.auto.php');
		$purifier=new HTMLPurifier;
		$text=$purifier->purify($_POST['ntext']);
		$title=htmlspecialchars($_POST['title'], ENT_QUOTES);
		$news=new tasks;
		$news->create($title, $text);
		echo '<meta http-equiv="Refresh" content="0;URL=tasks.php?ok">';
	}
?>
<div>
    <ul class="breadcrumb">
        <li>
            <a href="index.php">Админочка</a>
        </li>
        <li>
            <a href="news.php">Задачки</a>
        </li>
		<li>
            <a href="writenews.php">Добавлять</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="box col-md-12">
        <div class="box-inner">
            <div class="box-header well" data-original-title="">
                <h2><i class="glyphicon glyphicon-edit"></i> Пишем задачки</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round btn-default"><i
                            class="glyphicon glyphicon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round btn-default"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
				<?php if(isset($err)) { ?>
				<div class="alert alert-danger"><?=$err?></div>
				<?php } ?>
				<form action="newtask.php" method="post"  enctype="multipart/form-data">
					<input type="hidden" name="token" value="<?=$user->getToken()?>">
					<div class="form-group">
						<label for="newstitle">Задача будет называться...</label>
						<input type="text" id="newstitle" name="title" class="form-control" placeholder="Заголовокъ">
						
					</div>
					<div class="form-group">
						<textarea name="ntext" id="editor1" rows="10" cols="80">
							
						</textarea>
						<script type="text/javascript">
							CKEDITOR.replace( 'editor1' );
						</script>
					</div>
					<input type="submit" class="btn btn-default" name="submit" value="Вот так">
				</form>
            </div>
        </div>
    </div>
    <!--/span-->

</div><!--/row-->



<?php require('footer.php'); ?>

