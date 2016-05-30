(function($){

	"use strict";
	
	$(document).ready(function () {
		search.init();
	});
	
	var search = {
	
		init: function () {
			
			// SEARCH
			$('.advanced-search .f-row:nth-child(2)').hide();
			$('.lhdi:nth-child(2)').hide();
			$('.addMoreCities').hide();

			$('input[type=radio]#oneway').click(function() {
				$('.f-row:nth-child(2)').hide();
			});
			$('input[type=radio]#return').click(function() {
				$('.f-row:nth-child(2)').slideToggle();
			});

			$('input[type=radio]#byhours').click(function() {
				$('.lhdi:nth-child(2)').hide();
				$('.lhdi:nth-child(1)').show();
			});
			$('input[type=radio]#bydays').click(function() {
				$('.lhdi:nth-child(2)').show();
				$('.lhdi:nth-child(1)').hide();
			});

			$('input[type=radio]#onew').click(function() {
				var cityCount=$('.cities').size();
				if(cityCount>1)
				{
					for(var x=2; x<=cityCount; x++)
					{
						var t = "#cities"+ x;
						$(t).hide();
					}
				}
				$('.addMoreCities').hide();
			});
			$('input[type=radio]#roundw').click(function() {
				var cityCount=$('.cities').size();
				if(cityCount>1)
				{
					for(var x=2; x<=cityCount; x++)
					{
						var t = "#cities"+ x;
						$(t).hide();
					}
				}
				$('.addMoreCities').hide();
			});

			$('input[type=radio]#multicityw').click(function() {
				var cityCount=$('.cities').size();
				if(cityCount>1)
				{
					for(var x=2; x<=cityCount; x++)
					{
						var t = "#cities"+ x;
						$(t).show();
					}
				}
				$('.addMoreCities').show();
			});


			// DATE & TIME PICKER
			$('#dep-date,#ret-date').datetimepicker();

			jQuery('#pickup_date1, #pickup_date2, #dob').datetimepicker({
			 timepicker:false,
			 format:'Y-m-d'
			});

			jQuery('#pickup_time1, #pickup_time2').datetimepicker({
			 datepicker:false,
			 format:'H:i:s'
			});
		}
	}

})(jQuery);