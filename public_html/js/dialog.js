/*function CustomConfirm(){
	this.render = function(dialog, op){
		var winW = window.innerWidth;
	    var winH = window.innerHeight;
		var dialogoverlay = document.getElementById('dialogoverlay');
	    var dialogbox = document.getElementById('dialogbox');
		dialogoverlay.style.display = "block";
	    dialogoverlay.style.height = winH+"px";
		dialogbox.style.left = (winW/2) - (550 * .5)+"px";
	    dialogbox.style.top = "100px";
	    dialogbox.style.display = "block";
		
		document.getElementById('dialogboxhead').innerHTML = "Confirm that action";
	    document.getElementById('dialogboxbody').innerHTML = dialog;
		document.getElementById('dialogboxfoot').innerHTML = '<button onclick="Confirm.yes(\''+op+'\')">Yes</button> <button onclick="Confirm.no()">No</button>';
	}
	this.no = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
	this.yes = function(op){
		window.location.assign(op);
	}
}
var Confirm = new CustomConfirm();*/
function CustomConfirm(){
	this.render = function(dialog, op){
		/*e.preventDefault();*/

		$('#modal-body').html(dialog)
	    $('.a').attr("href", op);

		$('.dialogboxfoot').html('<button class="btn btn-primary" onclick="Confirm.yes(\''+op+'\')">Yes</button>');
		$('#myModal').modal('show');
	}
	this.no = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
	this.yes = function(op){
		window.location.assign(op);
	}
}
var Confirm = new CustomConfirm();

function CustomConfirmEdit(){
	this.render = function(dialog){
		/*e.preventDefault();*/

		$('#modal-body').html(dialog)

		$('.dialogboxfoot').html('<button class="btn btn-primary" onclick="Confirm.yes()">Yes</button>');
		$('#myModal').modal('show');
	}
	this.no = function(){
		document.getElementById('dialogbox').style.display = "none";
		document.getElementById('dialogoverlay').style.display = "none";
	}
	this.yes = function(){
		document.getElementById("edit_form").submit();
	}
}
var ConfirmEdit = new CustomConfirmEdit();