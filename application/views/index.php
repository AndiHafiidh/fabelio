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
				<div id="alert-submit"></div>
				<form id="url-form">
					<div class="input-group justify-content-center p-0">
						<div class="col-10 p-1">
							<input type="text" name="url" class="form-control" placeholder="Product url" required data-parsley-ui-enabled="false">
						</div>
						<div class="col-2 p-1">
							<button id="button-submit" type="submit" class="btn btn-warning btn-search">
								<i class="icons icon-magnifier"></i>        
								<!-- <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only ">Loading...</span>                         -->
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
					<h6 class="title">NEW PRODUCTS</h6>
					<a href="<?php echo base_url('product'); ?>" class="subtitle text-muted">OTHERS <i
							class="icons icon-arrow-right"></i></a>
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
             <div class="col-12 col-md-4">
				<div class="row">				
					<div class="col-12 text-center pt-5 mt-5" id="alert-info" hidden></div>
					<div class="owl-carousel px-3" id="lastest-product"></div>
				</div>
			</div>
		</div>
	</div>

</div>