<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ações</title>
	<script src="http://modernizr.com/downloads/modernizr-latest.js"></script>
	<style>
	body{
		font: 12px Verdana;
	}
	.actions .action{
		float: left;
		border: solid #e8e8e8 1px;
		margin: 15px;
	}
	.actions .action ul{
		float: left;
		list-style: none;
		margin: 0;
		padding: 8px 8px 6px 8px;
	}
	.actions .action ul li{
		float: left;
		clear: both;
		margin-bottom: 2px;
	}
	.actions .action .up{
		color: #27a816;
	}
	.actions .action .down{
		color: #ff0000;
	}
	</style>
</head>
<body>

	<div id="main">
		<section class="actions">
			<?
			foreach ($actions as $key => $value) {
				$action = (array_key_exists($value, $wallet)) ? $wallet[$value] : false;
				?>

			<div class="action" id="<?=strtolower($value)?>">
				<ul>
					<li><?=$value?></li>
					<li class="oscillation up">&nbsp;</li>
					<li class="value">&nbsp;</li>
					<li class="date">&nbsp;</li>
				</ul>
				<?
				if($action){
				?>
				<ul class="report">
					<li class="quantity"><?=$action["quantity"]?></li>
					<li><?=$action["value"]?></li>
					<li class="vested" dir="<?=($action["quantity"] * $action["value"])?>"><?=($action["quantity"] * $action["value"])?></li>
					<li class="current">&nbsp;</li>
				</ul>
				<?
				}
				?>
			</div>

				<?
			}
			?>
		</section>
	</div>


	<script src="http://code.jquery.com/jquery.min.js"></script>
	<script>
	<?
	$param = implode("|", $actions);
	?>

	getActions();
	setInterval(function(){
		getActions();
	},20000);

	function getActions(){
		$.ajax({
			url: 'json/<?=$param?>',
			success: function(data){
				insertActions(data);
			}
		});
	}

	var oscillation, action, quantity, dom = ['oscillation','value','date','quantity','vested','current'];
	function insertActions(data){
		$.each(data.papel, function(i, val) {
			action = val['@attributes'];
			dom['date'] = $('#'+action.codigo.toLowerCase()).find('.date');

			if(action.oscilacao != "" && dom['date'].text() != action.data){
				dom['oscillation'] = $('#'+action.codigo.toLowerCase()).find('.oscillation');
				dom['value'] = $('#'+action.codigo.toLowerCase()).find('.value');
				dom['quantity'] = $('#'+action.codigo.toLowerCase()).find('.quantity');
				dom['vested'] = $('#'+action.codigo.toLowerCase()).find('.vested');
				dom['current'] = $('#'+action.codigo.toLowerCase()).find('.current');

				quantity = dom['quantity'].text();

				oscillation = (!action.oscilacao.indexOf('-')) ? action.oscilacao : '+'+action.oscilacao;

				dom['oscillation'].text(oscillation+'%');
				dom['value'].text(action.medio);
				dom['date'].text(action.data.split(" ")[1]);
				dom['vested'].text(formatCurrency(dom['vested'].attr('dir')) + " / " + formatCurrency(quantity * dom['value'].text().replace(",",".")));

				var value = dom['vested'].attr('dir')-(quantity * dom['value'].text().replace(",","."));
				dom['current'].text(formatCurrency(value));

				if(dom['vested'].attr('dir') > (quantity * dom['value'].text().replace(",",".")))
					dom['current'].removeClass('up').addClass('down');
				else
					dom['current'].removeClass('up').addClass('down');

				if(!action.oscilacao.indexOf('-'))
					dom['oscillation'].removeClass('up').addClass('down');
				else
					dom['oscillation'].removeClass('down').addClass('up');
			}
		});
	}

	function formatCurrency(num) {
		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
			num = "0";

		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();

		if(cents<10)
			cents = "0" + cents;

		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
			num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));

		return (((sign)?'':'-') + num + '.' + cents);
	}
	</script>
</body>
</html>