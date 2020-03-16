<script src="<?php echo base_url('assets/js/owl.carousel.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/parsley.min.js'); ?>"></script>
<script>
    $(document).ready(function(){        
        axios({
            method: 'post',
            url: '/api/product/read',
            data: {
                limit: 5,
                sort: 'created_at DESC'
            }
        })
        .then(function (response) {            
            if(!response.data.status.error){
                $('#alert-info').attr('hidden', true);

                response.data.data.forEach(data => {                    
                    let sale_price = numeral(data.sale_price).format('$ 0,0[.]00');
                    let price = numeral(data.price).format('$ 0,0[.]00');

                    $('#lastest-product').append('<a class="product-item" href="'+BASE_URL+'/product/'+data.id+'/'+createURL(data.name)+'"><div class="card lastest-product"><img src="'+data.image+'"class="card-img-top" alt="..."><div class="card-body pb-0"><div class="module"><h5 class="card-title line-clamp">'+data.name+'</h5></div><div class="module"><p class="card-text line-clamp">'+data.description+'</p></div></div><div class="card-body py-2 text-right"><h6>' + ((data.price == data.sale_price)? price : '<small class="text-muted sale">'+price+'</small> ' + sale_price ) + '</h6></div></div></a>');
                });

                $(".owl-carousel").owlCarousel({
                    stagePadding: 50,
                    margin:10,
                    loop:false,
                    autoWidth:true
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
            $('.preloader').attr('hidden', 'true');
        });


        $('#url-form').parsley().on('form:submit', function() {   
            $("#button-submit").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="sr-only ">Loading...</span>');

            let data = getFormData('#url-form', false);

            axios({
                method: 'post',
                url: '/api/product/create',
                data: data
            }).then(function (response) {
                let progress = -20; 
                let data = response.data.data[0];
                
                $('#alert-submit').html('<div class="alert alert-warning mb-1" role="alert"><i class="mdi mdi-check-all mr-2"></i> Product was found, we will redirect you..</div> <div class="progress mb-2" style="height: 4px;"><div id="loading-progress" class="progress-bar bg-warning" role="progressbar" width="0%"></div></div>');  
                var myInterval = setInterval(function () {
                    progress += 20;
                    $("#loading-progress").width(progress+"%");
                    if(progress == 100) window.location.replace(data);
                },1000);               
                
                $("#button-login").html('<i class="icons icon-magnifier"></i>');

            }).catch(function (error) {            
                let data = error.response.data;
                $('#alert-submit').html('<div class="alert alert-danger" role="alert"><i class="mdi mdi-block-helper mr-2"></i> ' + data.message + '</div>');                
                $("#button-login").html('<i class="icons icon-magnifier"></i>');
            });            
            return false; // Don't submit form for this demo
        });
    });
</script>