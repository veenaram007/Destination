<?php

/**
 * DevTest.php
 *
 * Program to list all accommodation in the Blue Mountains
 *
 * @author     Veena Ramachandran
 */
 
 
class DevTest {
	
	//Function to return API URL, builds query string dynamically to load products for categories/areas.
	public function getUrl() {

		global $url;
		
		// query string
		$fields = array(
			'key' => '2015201520159',
			'out' => 'json',
			'cats' => 'ACCOMM',
			'ar' => 'Blue Mountains Area',
			'size' => "8",
		);
		
		$url = 'http://atlas.atdw.com.au/productsearchservice.svc/products?' . http_build_query($fields);
	}
	
	//Function to return API URL, builds query string dynamically to load products details.
	public function getDetails() {

		global $details;
		
		// query string
		$fields = array(
			'key' => '2015201520159',
			'out' => 'json',
		);
		
		$details = 'http://atlas.atdw.com.au/productsearchservice.svc/product?' . http_build_query($fields);
	}
	
}
	//Initialise Class
	$object = new DevTest();
	
	//Initialise URLs
	$object->getUrl();
	$object->getDetails();


?>

<!DOCTYPE html>
<html lang="en">
	<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Accommodations in Blue Mountains</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="devtest.css" rel="stylesheet">
	</head>

	<body>
	
	<!-- /nav -->
    <nav class="navbar">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-title" href="#">DevTest</a>
        </div>
      </div>
    </nav>
	<!-- /nav -->
	
	<!-- /banner -->
    <div class="banner">
      <div class="container">
        <h2>Accommodations in the Blue Mountains</h2>
        <p>We found <span id="number"></span> services for you...</p>
      </div>
    </div>
	<!-- /banner -->
	
	<!-- /container -->
    <div class="container">
		<hr>
		<div class="row"></div>
		<hr>
		<footer>
			<div id="pagination"></div>
		</footer>
    </div> 
	<!-- /container -->

	<!-- /Modal -->
	<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"></h3>
			<div class="rate">Rates from  <span id="ratefrm"></span> to <span id="rateto"></span> <span id="currency"></span></div>
		</div>
		<div class="modal-body">
			<p><div id="desc"></div></p>
			<br>
			<p>Address : <span id="address"></span></p>
			<p>Email : <span id="email"></span></p>
			<p>Phone : <span id="phn"></span></p>
			<p>Mobile : <span id="mob"></span></p>
			<p>Website : <span id="website"></span></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>
	<!-- /Modal -->
	
    <!-- Bootstrap core JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script>
		$(document).ready(function() {
			
			load(1);
			
		});
		
		function details(productId) {
			var request_details = $.ajax({
				url: "<?php echo $details;?>&productId="+productId,
				type: "get",
				dataType: "json",
				async: true
			});

			// Callback handler that will be called on success
			request_details.done(function (response, textStatus, jqXHR){
					
				$("#myModalLabel").text(response.productName);
				$("#desc").text(response.productDescription);
				$("#ratefrm").text(response.rateFrom);
				$("#rateto").text(response.rateTo);
				$("#currency").text(response.attributeIdCurrency);
				$("#address").text(response.addresses[0].addressLine1 +', '+ response.addresses[0].cityName +', '+ response.addresses[0].areaName +', '+ response.addresses[0].stateName +'-'+ response.addresses[0].addressPostalCode);
				$("#email").text(response.communication[0].communicationDetail);
				$("#phn").text(response.communication[1].communicationIsdCode +' '+ response.communication[1].communicationAreaCode +' '+ response.communication[1].communicationDetail);
				$("#mob").text(response.communication[2].communicationIsdCode +' '+ response.communication[2].communicationDetail);
				$("#website").html('<a href="http://'+response.communication[3].communicationDetail+'" target="_blank">'+response.communication[3].communicationDetail+'</a>');

				$('#myModal').modal('show'); 
				
			});

			// Callback handler that will be called on failure
			request_details.fail(function (jqXHR, textStatus, errorThrown){
				// Log the error to the console
				console.error("The following error occurred: "+textStatus, errorThrown);
			});
			
		}
		
		function load(page) {
			$(".row").html("");
			$("#pagination").html("");
			
			var request = $.ajax({
				url: "<?php echo $url;?>&pge="+page,
				type: "get",
				dataType: "json",
				async: true
			});

			// Callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				
				$.each(response,function(index,element){
					if(index == "numberOfResults"){
						$("#number").text(element);
						perpage = 8;
						total = Math.ceil(element/perpage);
						for(i=1;i<=total;i++){
							if(page == i)
								active = "active";
							else
								active = "";
							var span = $('<a href="javascript:void(0)" onclick="load('+i+');" class='+active+'><span><span></a>').html(i);
							$("#pagination").append(span);
						}
					}
					if(index == "products") {
						$.each(element,function(ind,elem){
							var product = elem.productId.split('$');
							var block = '<div class="col-md-3 block"><h4>' + elem.productName + '</h4><p><img src="'+elem.productImage+'"></img></p><p><a class="btn btn-default" href="#" role="button" href="javascript:void(0)" onclick="details(' + product[0] + ');">Learn More</a></p></div>';
							$(".row").append(block);
						});
					}
				});				
			});

			// Callback handler that will be called on failure
			request.fail(function (jqXHR, textStatus, errorThrown){
				// Log the error to the console
				console.error("The following error occurred: "+textStatus, errorThrown);
			});
		}

		</script>
  </body>
</html>
