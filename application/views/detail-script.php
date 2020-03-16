<script src="<?php echo base_url('assets/js/owl.carousel.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/Chart.bundle.min.js'); ?>"></script>

<script>
	$(document).ready(function () {
		setGalery(<?php echo $id; ?>); 
		setData(<?php echo $id; ?>); 
		setPrice(<?php echo $id; ?>); 			
	});

	function setData(id) {		
		axios({
            method: 'post',
            url: '/api/product/read',
            data: {
               filter: "id = " + id
            }
        })
        .then(function (response) {            
            if(!response.data.status.error){
				data = response.data.data[0];				
				
				$('#product-name').html(data.name);
				$('#product-description').html(data.description);
				$('#product-detail').html(data.detail);

				let sale_price = numeral(data.sale_price).format('$ 0,0[.]00');
				let price = numeral(data.price).format('$ 0,0[.]00');
				$('#product-price').html(((data.price == data.sale_price)? price : sale_price + ' <small class="text-muted sale">'+price+'</small>'));

				                          
            }else{                
                $('#alert-info').html('<p class="text-muted">'+response.data.message+'</p>');
                $('#alert-info').removeAttr('hidden');
            }
        })
        .catch(function (error) {
            $('#alert-info').html('<p class="text-muted">'+error.response.data.message+'</p>');
            $('#alert-info').removeAttr('hidden');
        }).then(function(){            
			$('.preloader').attr('hidden', 'true');
		});		
	}

	function setGalery(id){
		axios({
            method: 'post',
            url: '/api/product/galery/'+id,
            data: {
               sort: 'order ASC'
            }
        })
        .then(function (response) {            
            if(!response.data.status.error){
				let index = 0;
				let data = response.data.data;				
				
				data.forEach(dt => {
					$('#galery-indicator').append('<li data-target="#galery" data-slide-to="'+index+'" class="' + ( (index==0) ? 'active' : '' ) + '"></li>');
					$('#galery-image').append('<div class="carousel-item ' + ( (index==0) ? 'active' : '' ) + '"><img class="d-block w-100"src="'+dt.image+'"alt="slide-'+index+'"><div class="carousel-caption d-none d-md-block"><small>'+dt.caption+'</small></div></div>');
					index++;
				});
				
            }else{                
                $('#alert-info').html('<p class="text-muted">'+response.data.message+'</p>');
                $('#alert-info').removeAttr('hidden');
            }
        })
        .catch(function (error) {
            $('#alert-info').html('<p class="text-muted">'+error.response.data.message+'</p>');
            $('#alert-info').removeAttr('hidden');
        }).then(function(){
            $(".owl-carousel").owlCarousel({
				autoWidth: true,
				autoplay: true
			});			
		});			
	}

	function setPrice(id) {
		axios({
            method: 'post',
            url: '/api/product/price/'+id,
            data: {
               sort: 'created_at ASC'
            }
        })
        .then(function (response) {  		          
            if(!response.data.status.error){
				let index = 0;
				let data = response.data.data;	
				
				let label = [];
				let price = [];

				data.forEach(dt => {
					label.push(moment(dt.created_at).format('YYYY-M-D h:mm:ss'));
					let temp = parseInt(numeral(dt.sale_price).value());
					price.push(temp);
				});				

				var ctx = document.getElementById('myChart');
				var myChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: label,
						datasets: [{				
							data: price
						}]
					},
					options: {                
						legend: {
							display: false
						},
						title: {
							display: false
						},
						gridLines:{
							display: false
						},
						scales: {
							xAxes: [{
								ticks: {
									display: false //this will remove only the label
								}
							}],
							yAxes: [{
								ticks: {
									display: false //this will remove only the label
								}
							}]
						},
						tooltips: {
							callbacks: {
								label: function(tooltipItem, data) {
									var label = data.datasets[tooltipItem.datasetIndex].label || '';

									if (label) {
										label += ': ';
									}
									label += numeral(tooltipItem.yLabel).format('$ 0,0[.]00');									
									return label;
								}
							}
						}
					}
				});
            }else{                
                $('#alert-info').html('<p class="text-muted">'+response.data.message+'</p>');
                $('#alert-info').removeAttr('hidden');
            }
        })
        .catch(function (error) {
            $('#alert-info').html('<p class="text-muted">'+error.response.data.message+'</p>');
            $('#alert-info').removeAttr('hidden');
        });
	}
</script>