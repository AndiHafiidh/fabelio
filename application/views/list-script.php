<script src="<?php echo base_url('assets/js/parsley.min.js'); ?>"></script>

<script>
    $(document).ready( function () {
        setList();

        $('#search-form').parsley().on('form:submit', function() {   
            $("#button-search").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="sr-only ">Loading...</span>');

            let data = getFormData('#search-form', false);

            setList(data.keyword);
                      
            return false; // Don't submit form for this demo
        });
    });

    function setList(keyword = null, page = 1) {        
        $('#product-list').html('');
        axios({
            method: 'post',
            url: '/api/product/read',
            data: {
                page: page,
                sort: 'name ASC',
                filter: (keyword != null) ? "LOWER(name) LIKE " + keyword : null
            }
        })
        .then(function (response) {               
            if(!response.data.status.error){
                $('#alert-info').attr('hidden', true);

                response.data.data.forEach(data => {                    
                    let sale_price = numeral(data.sale_price).format('$ 0,0[.]00');
                    let price = numeral(data.price).format('$ 0,0[.]00');

                    $('#product-list').append('<tr><td><a href="'+BASE_URL+'/product/'+data.id+'/'+createURL(data.name)+'"><div class="card"><div class="card-body media"><img src="'+data.image+'" alt="Product image" class="mr-3 product-img"><div class="media-body"><div class="module"><h6 class="product-name line-clamp">'+data.name+'</h6><span>' + ((data.price == data.sale_price)? price : sale_price + ' <small class="text-muted sale">'+price+'</small>') + '</span></div><div class="module mt-2"><p class="product-description line-clamp">'+data.description+'</p></div></div></div></div></a></td></tr>');
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
            $("#button-search").html('<i class="icons icon-magnifier"></i>');
        });
    }
</script>