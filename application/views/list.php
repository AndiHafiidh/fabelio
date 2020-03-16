<div class="app-detail">
    <div class="app-detail__header justify-content-center"> 
        <div class="col-12 col-md-6 col-lg-4">
			<div class="container pt-2">
				<div class="row justify-content-center">
					<img class="col" src="<? echo base_url('assets/img/logo.svg'); ?>" alt="backgroung.svg"
						height="75px">
				</div>
			</div>
		</div>
    </div>
    <div class="app-detail__body justify-content-center">
		<div class="card col-12 col-md-4 p-2">
			<div class="card-body p-2">
				<form id="search-form">
					<div class="input-group justify-content-center p-0">
						<div class="col-10 p-1">
							<input type="text" name="keyword" class="form-control" placeholder="Search product" required>
						</div>
						<div class="col-2 p-1">
							<button type="submit" class="btn btn-warning btn-search" id="button-search">
								<i class="icons icon-magnifier"></i>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
    <div class="col-12">
		<div class="row justify-content-center">
			<div class="col-12 col-md-4">
				<div class="row px-3 justify-content-between">
					<h6 class="title">ALL PRODUCTS</h6>					
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
             <div class="col-12 col-md-4">				
                <div class="col-12 text-center pt-5 mt-5" id="alert-info" hidden></div>
                <table class="custom-table" width="100%">                                    
                    <tbody id="product-list">                        
                    </tbody>
                </table>            
			</div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 py-2">                    
                <div id="pagination" hidden>                                
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1"><</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">></a>
                            </li>
                        </ul>
                    </nav>                                
                </div>                        
            </div>
        </div>
	</div>
</div>