<?php
	include('../inc/config.inc.php');
	include('../inc/accounts.inc.php');
?>

<!DOCTYPE html>

<html>
	<?php
		include('../inc/head.inc.php');
	?>

	<body>
		<div id="HISTORY">
			<h1>10 DERNIERES INTERVENTIONS</h1>
			<table>
				<thead>
					<th>
						<b></b>
					</th>
					<th>
						<b>NUMERO</b>
					</th>
					<th>
						<b>DERNIERE MODIFICATION</b>
					</th>
					<th>
						<b>MODIFIEE PAR</b>
					</th>
					<th>
						<b>DATE DE CREATION</b>
					</th>
					<th>
						<b>CLIENT</b>
					</th>
					<th>
						<b>SOUS CONTRAT</b>
					</th>
					<th>
						<b>A FACTURER</b>
					</th>
				</thead>
				<?php
					$req = $bdd->prepare(
					"SELECT
						INTERVENTIONS.ID, LAST_UPDATE, USERS_INTER.NAME UPDATED_BY, DATE, CLIENT, CONTRAT, BILLING
					FROM
						INTERVENTIONS
					INNER JOIN
						USERS_INTER ON (INTERVENTIONS.UPDATED_BY = USERS_INTER.ID)
					ORDER BY
						ID DESC
					LIMIT
						0, 10"
					);
					$req->execute() or die(print_r($req->errorInfo()));
					$inter = $req->fetchAll(3);

					for($x = 0; $x < count($inter); $x++)
					{
						echo("<tr><td>");
							echo("<a target='_parent' href='../?_request=inter&_action=view&_inter=" . $inter[$x][0] . "'>Afficher / Editer</a>");
						echo("</td>");
							for($y = 0; $y < count($inter[$x]); $y++)
							{
								if($y == 5)
								{
									if($inter[$x][$y] == "1")
									{
										echo("<td style='background:#8bc34a;'>");
											echo("OUI");
									}
									else if($inter[$x][$y] == "0")
									{
										echo("<td style='background:#ff5722;'>");
											echo("NON");
									}
								}
								else
								{
									echo("<td>");
										echo($inter[$x][$y]);
									echo("</td>");
								}
							}
						echo("</tr>");
					}
				?>
			</table>
		</div>
		<div id="HISTORY_INSTALLATION">
			<h1>10 DERNIERES INSTALLATIONS</h1>
			<table>
				<thead>
					<th>
						<b></b>
					</th>
					<th>
						<b>NUMERO</b>
					</th>
					<th>
						<b>DERNIERE MODIFICATION</b>
					</th>
					<th>
						<b>MODIFIEE PAR</b>
					</th>
					<th>
						<b>DATE DE CREATION</b>
					</th>
					<th>
						<b>CLIENT</b>
					</th>
					<th>
						<b>SOUS CONTRAT</b>
					</th>
					<th>
						<b>A FACTURER</b>
					</th>
				</thead>
				<?php
					$req = $bdd->prepare("
					SELECT
						INTERVENTIONS.ID, LAST_UPDATE, USERS_INTER.NAME UPDATED_BY, DATE, CLIENT, CONTRAT, BILLING
					FROM
						INTERVENTIONS
					INNER JOIN
						USERS_INTER ON (INTERVENTIONS.UPDATED_BY = USERS_INTER.ID)
					WHERE
						INSTALLATION = 1
					ORDER BY
						ID DESC
					LIMIT
						0, 10"
					);
					$req->execute();
					$inter = $req->fetchAll(3);

					for($x = 0; $x < count($inter); $x++)
					{
						echo("<tr><td>");
							echo("<a target='_parent' href='../?_request=inter&_action=view&_inter=" . $inter[$x][0] . "'>Afficher / Editer</a>");
						echo("</td>");
							for($y = 0; $y < count($inter[$x]); $y++)
							{
								if($y == 5)
								{
									if($inter[$x][$y] == "1")
									{
										echo("<td style='background:#8bc34a;'>");
											echo("OUI");
									}
									else if($inter[$x][$y] == "0")
									{
										echo("<td style='background:#ff5722;'>");
											echo("NON");
									}
								}
								else
								{
									echo("<td>");
										echo($inter[$x][$y]);
									echo("</td>");
								}
							}
						echo("</tr>");
					}
				?>
			</table>
		</div>
		<?php if($_SESSION['rights'] < "2"):?>
			<div id="HISTORY_SELF">
				<h1>MES 10 DERNIERES INTERVENTIONS</h1>
				<table>
					<thead>
						<th>
							<b></b>
						</th>
						<th>
							<b>NUMERO</b>
						</th>
						<th>
							<b>DERNIERE MODIFICATION</b>
						</th>
						<th>
							<b>MODIFIEE PAR</b>
						</th>
						<th>
							<b>DATE DE CREATION</b>
						</th>
						<th>
							<b>CLIENT</b>
						</th>
						<th>
							<b>SOUS CONTRAT</b>
						</th>
						<th>
							<b>A FACTURER</b>
						</th>
					</thead>
					<?php
						$req = $bdd->prepare("
						SELECT
							INTERVENTIONS.ID, LAST_UPDATE, USERS_INTER.NAME UPDATED_BY, DATE, CLIENT, CONTRAT, BILLING
						FROM
							INTERVENTIONS
						INNER JOIN
							USERS_INTER ON (INTERVENTIONS.UPDATED_BY = USERS_INTER.ID)
						WHERE
							TECHNICIEN = :technicien
						ORDER BY
							ID DESC
						LIMIT
							0, 10"
						);
						$req->execute(array
							(
								'technicien' => $_SESSION['id']
							)
						);
						$inter = $req->fetchAll(3);

						for($x = 0; $x < count($inter); $x++)
						{
							echo("<tr><td>");
								echo("<a target='_parent' href='../?_request=inter&_action=view&_inter=" . $inter[$x][0] . "'>Afficher / Editer</a>");
							echo("</td>");
								for($y = 0; $y < count($inter[$x]); $y++)
								{
									if($y == 5)
									{
										if($inter[$x][$y] == "1")
										{
											echo("<td style='background:#8bc34a;'>");
												echo("OUI");
										}
										else if($inter[$x][$y] == "0")
										{
											echo("<td style='background:#ff5722;'>");
												echo("NON");
										}
									}
									else
									{
										echo("<td>");
											echo($inter[$x][$y]);
										echo("</td>");
									}
								}
							echo("</tr>");
						}
					?>
				</table>
			</div>
		<?php endif;?>
	</body>
</html>

<style>
	body{
		margin:		0;
		background:	lightgrey;
	}

	#HISTORY_SELF{
	}

	div{
		box-shadow:		0px 0px 8px black;
		width:			98vw;
		text-align:		center;
		margin:			0 auto 30px auto;
		border-radius:	5px;
		background:		grey;
	}

	h1{
		font-family:	'Arsenal';
	}

	table{
		text-align:		center;
		border-spacing:	0;
		background:		white;
		width:			100%;
		margin:			0 auto;
	}

	td>a{
		text-decoration:	none;
		color:				black;
	}

	td>a:hover{
		color:				blue;
	}

	thead{
		font-size:			1.3em;
		background:		grey;
	}

	td{
		border-top:		black solid 1px;
		background:			white;
	}
</style>
