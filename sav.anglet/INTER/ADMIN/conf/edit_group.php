<?php
	require('../inc/config.inc.php');
	require('/home/tacteose/sav/INTER/ADMIN/inc/accounts.inc.php');
	
	if((!$_SESSION['rights']['ADMIN'] && !$_SESSION['rights']['GROUPS_EDIT'] && !$_SESSION['rights']['ADMIN_EDIT']) || empty($_GET))
	{
		header('Location:	../errors/error_rights.php');
		die();
	}
	elseif(!empty($_POST))
	{
		$req = $bdd->query("
			SELECT
				*
			FROM
				RIGHTS
			WHERE
				ID =".$_SESSION['groupID'])
		or die(print_r($bdd->errorInfo()));
		$update = $req->fetch(2);	
		unset($update['ID']);
		for($x = 0; $x < sizeOf($update); $x++)
		{
			for($y = 0; $y < sizeOf($_POST); $y++)
			{
				if(key($update) == key($_POST))
				{
					$req = $bdd->prepare("
						UPDATE
							RIGHTS
						SET
							".key($update)." = :value
						WHERE
							ID = :groupID
					");
					
					$req->execute(array(
						'value'		=> current($_POST),
						'groupID'	=> $_SESSION['groupID']
					)) or die(print_r($req->errorInfo()));
					
					break;
				}
				else
				{
					$req = $bdd->prepare("
						UPDATE
							RIGHTS
						SET
							".key($update)." = NULL
						WHERE
							ID = :groupID
					");
					
					$req->execute(array(
						'groupID'	=> $_SESSION['groupID']
					)) or die(print_r($req->errorInfo()));
				}
				next($_POST);
			}
			reset($_POST);
			next($update);
		}
		
		$_GET['groupID'] = $_SESSION['groupID'];
	}
	elseif(isset($_GET['create']))
	{
		$temp = rand();
		$bdd->query("
			INSERT INTO
				RIGHTS (NAME)
			VALUES
				('".$temp."')
		") or die(print_r($bdd->errorInfo()));
		
		$req = $bdd->query("
			SELECT
				ID
			FROM
				RIGHTS
			WHERE
				NAME = ".$temp
			) or die(print_r($bdd->errorInfo()));
		$res = $req->fetch(2);
		
		$bdd->query("
			UPDATE
				RIGHTS
			SET
				NAME = 'Nouveau Groupe'
			WHERE
				NAME = ".$temp
			) or die(print_r($bdd->errorInfo()));
			
		$_GET['groupID'] = $res['ID'];
	}
	elseif(isset($_GET['delete']) && isset($_GET['groupID']))
	{
		if($_GET['groupID'] == "0")
		{
			header('Location:	./group_manager.php');
			die();
		}
		
		$bdd->query("
			DELETE FROM
				RIGHTS
			WHERE
				ID = ".$_GET['groupID']
		) or die(print_r($bdd->errorInfo()));
		header('Location:	./group_manager.php');
		die();
	}
	
	$_SESSION['groupID'] = $_GET['groupID'];
	
	$comments = $bdd->query("
		SELECT
			a.COLUMN_COMMENT DESCRIPTION
		FROM
			information_schema.COLUMNS a 
		WHERE
			a.TABLE_NAME = 'RIGHTS';
	") or die($bdd->errorInfo());
	
	$rights = $_SESSION['rights'];
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			RIGHTS
		WHERE
			ID = :groupId"
		) or die($req->errorInfo());
	
	$req->execute(array(
		'groupId'	=>	$_SESSION['groupID']
		)) or die($req->errorInfo());
	
	$group = $req->fetch(2);
?>

<html>

	<head>
		<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="../scripts/jQuery.min.js"></script>
		<script src="conf.js"></script>
	</head>

	<body>
		<div>
			<a href="group_manager.php" id="btnback"><p>X</p></a>
		</div>
		<div class="section">
			<h2>EDITER LE GROUPE : <i style="color:<?=$group['COLOR']?>"><?=$group['NAME']?></i></h2>
		</div>
		<form method="post" action="" id="edits">
			<div class="sectionContent">
				<table style="margin-bottom:20px;">
					<tr>
						<td>
							NOM :<input type="text" value="<?=$group['NAME']?>" name="NAME">
						</td>
						<td>
							COULEUR :<input type="color" value="<?=$group['COLOR']?>" name="COLOR">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							DESCRIPTION :
							<textarea style="resize:none;height:10em" name="DESCRIPTION"><?=$group['DESCRIPTION']?></textarea>
						</td>
					</tr>
				</table>
				<table class="permissions">
				<?php
					for($x = 4; $x > 0; $x--)
					{
						next($group);
						$comments->fetch();
					}
					while($infos = $comments->fetch(2)):
					$infos = explode(";",$infos['DESCRIPTION']);
					$permType = explode("_",key($group))[0];
				?>
					<?php if(($lastType != $permType[0]) && !empty($lastType)):?>
						</table>
						<table class="permissions">
					<?php endif; ?>
					<tr title="<?=$infos[1]?>" style="background:<?=$infos[2]?>CC">
						<td class="permName">
							<label for="<?=key($group)?>"><span><?=$infos[0]?></span></label>
						</td>
						<td class="check">
							<div class="button" onclick="check(this)" <?=!empty(current($group)) ? "style='background: lightgreen;'" : "" ?>>
								<div <?=!empty(current($group)) ? "style='margin-left: calc(100% - 28px);'" : "" ?>>
									<input type="checkbox" value="1" <?=!empty(current($group)) ? "checked" : "";?> name="<?=key($group)?>" id="<?=key($group)?>">
								</div>
							</div>
						</td>
					</tr>
				<?php
					$lastType = $permType[0];
					next($group);
					endwhile;
				?>
				</table>
			</div>
			<table>
				<tr>
					<td>
						<input type="button" value="Réinitialiser" id="btnRst" onclick="check('reset')">
					</td>
					<td>
						<input type="submit" value="Valider" id="btnOk">
					</td>
				</tr>
			</table>
		<form>
	</body>
	
</html>

<script>
	function check(button){
		if(button == "reset")
		{
			document.getElementById("edits").reset();
			var inputs = document.getElementsByTagName("input");
			for(var x = 0; x < inputs.length; x++)
			{
				var slide = inputs[x].parentNode;
				var button = slide.parentNode;
				if(!inputs[x].checked)
				{
					slide.removeAttribute("style");
					button.removeAttribute("style");
				}
				else
				{
					slide.style.marginLeft = "calc(100% - 28px)";
					button.style.background = "lightgreen";
				}
			}
		}
		else
		{
			var slide = button.getElementsByTagName("div")[0];
			var input = button.getElementsByTagName("input")[0];
			if(input.checked)
			{
				slide.removeAttribute("style");
				input.checked = "";
				button.removeAttribute("style");
			}
			else
			{
				slide.style.marginLeft = "calc(100% - 28px)";
				input.checked = "true";
				button.style.background = "lightgreen";
			}
		}
	}
</script>