<?php	
	/*echo '<p>Dump Session:</p><pre>';
	print_r($_SESSION['prods3']);
	echo '</pre>';*/
?>

<form method="post" enctype="multipart/form-data" action="" id="import_form">
	<div class="h">
		<div class="col-sm-2">
			<input type="file" name="file" class="btn-block ">
		</div>
		<!--<div class="col-sm-10">
			<label style="margin-left:20px;"><input type="radio" name="price" checked="checked" value="min"> Минимальные цены</label>
			<label style="margin-left:20px;"><input type="radio" name="price" value="middle"> Средние значения</label>		
		</div>-->
	</div>
	
	<br>
	<button class="btn btn-block btn-info h" style="margin:10px 0;"><i class="fa fa-upload" aria-hidden="true"></i> Произвести импорт каталога</button>
	<p class="h"><i>Перед импортом не забудьте сделать <a href="index.php?a=93"  target="main">резервную копию</a>, (вкладка резервное копирование).</i></p>
	
	<div class="progress" style="border:1px solid lightgrey; height:15px; width:100%; text-align:center; display:none;">
		<div style="height:15px; background:#5bc0de; transition:0.3s all"></div>
	</div>
	<p class="info" style="display:none;">	
		Процент выполнения - <span class="percent"></span><br>
		Время прошло - <span class="time"></span><br>
		Обработано строк - <span class="begin"></span><br>
		Всего строк - <span class="count"></span><br>
		Позиций найдено - <span class="found"></span><br> 		
		Позиций не найдено - <span class="notfound"></span><br> 		
	</p>
	
</form>

<script>
	$(function(){
		$('#import_form').on('submit', function(e){
			e.preventDefault();
			var $that = $(this),
			formData = new FormData($that.get(0)); 
			$.ajax({
				contentType: false, 
				processData: false, 
				url: './../assets/snippets/import/logic.php',
				method: 'post',
				data: formData,
				success: function(result){					
					if(result=='ok') 
					{
						$('.h').hide();
						$('.progress,.info').show();
						import_xls();
					}
				}
			});
		});
		
		function import_xls()
		{	
			$.ajax({
				contentType: false, 
				processData: false, 
				url: './../assets/snippets/import/logic.php?import&price='+$('input[name=price]').val(),
				method: 'post',				
				success: function(result){					
					console.log(result);
					$('.progress div').css({"width":result.percent+'%'});
					$('.time').html(result.time);
					$('.percent').html(result.percent+'%');
					$('.begin').html(result.begin);
					$('.count').html(result.count);
					$('.notfound').html(result.notfound);
					$('.found').html(result.found);
					if (result.status=='continue') import_xls();
					else $('.info').html('COMPLETE!');
				}
			});
		}
		
	});
</script>