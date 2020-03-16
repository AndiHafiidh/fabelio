<div class="app-detail">
	<div class="app-detail__header justify-content-center">
		<div class="col-4 col-md-4 p-0">
			<div class="container pt-2">
				<div class="row justify-content-center">
					<img class="col" src="<? echo base_url('assets/img/logo.svg'); ?>" alt="backgroung.svg"
						height="75px">
				</div>
			</div>
		</div>
	</div>
	<div class="app-detail__body justify-content-center">
		<div class="col-12 col-md-4">
			<div class="card">
                <div class="card-body pb-2">                    
                    <h5 id="product-name"></h5>
                </div>
				<div class="card-body pb-2 pt-0">
					<div id="galery" class="carousel slide" data-ride="carousel">
						<ol class="carousel-indicators" id="galery-indicator">
						</ol>
						<div class="carousel-inner" id="galery-image">						
						</div>
						<a class="carousel-control-prev" href="#galery" role="button"
							data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#galery" role="button"
							data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>
					</div>
				</div>
				<div class="card-body py-2 mb-3">
                    <h5 class="price text-left text-warning" id="product-price"></h5>
					<small>
						<p class="" id="product-description"></p>
					</small>
				</div>

				<div class="card-body pb-2" style="font-size:0.8rem">
					<div id="accordion">
						<div class="card">
							<div class="card-body py-2" id="headingOne">
								<h5 class="mb-0">
									<a class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
										aria-expanded="false" aria-controls="collapseOne">
										Product Specifiation
                                    </a>
								</h5>
							</div>

							<div id="collapseOne" class="collapse" aria-labelledby="headingOne"
								data-parent="#accordion">
								<div class="card-body" id="product-detail"></div>
							</div>
						</div>
					</div>
                </div>
                
                <div class="card-body">
                    <canvas id="myChart" width="100%"></canvas>
                </div>
			</div>
		</div>
	</div>
</div>