/* ***************
	Ajax validation of the registration form
   *************** */
var timer;
function check(id){
	clearTimeout(timer);
	timer=setTimeout(function validate(){
			params = 'act=' + 'validate' + '&in=' + id + '&val=' + $F(id);
			new Ajax.Request('ajax.php', { method: 'get', parameters: params,
				onSuccess: function(transport) { input_valid(id, transport.responseText); }
			});
		},250);
}

function input_valid(id, b){
	if(b == 1){
		$(id + '_v').innerHTML = "<img src='img/icons/tick.png' alt='Valid' title='OK' />";
	} else {
		$(id + '_v').innerHTML = "<img src='img/icons/cross.png' alt='Invalid' title='Invalid' />";
	}
}

function passcheck(rp, op){
        b=0;
	if(rp.value==op.value){
            b=1;
        }
	input_valid(rp, b);
}