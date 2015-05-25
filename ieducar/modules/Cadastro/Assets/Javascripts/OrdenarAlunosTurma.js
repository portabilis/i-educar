function onChangeOrderElements(){
	$that = $j(this);
	$j("[id^=sequencia]").each(function(){
		if($j(this).attr('id') != $that.attr('id')){
			if($j(this).val() == $that.val()){
				$j(this).val(parseInt($that.val())+1);
				$j(this).trigger('change');
			}
		} 
	});
}

$j("[id^=sequencia]").on('change', onChangeOrderElements);