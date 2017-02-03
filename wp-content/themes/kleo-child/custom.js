jQuery(document).ready(function() {

	console.log('loaded');

	jQuery('.single #commentform #comment').mentionsInput({
    	onDataRequest: function (mode, query, callback) {
      		console.log(query);
    	}
  	});


});