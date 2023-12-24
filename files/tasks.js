window.addEventListener('load', function () {

	for(let link of document.getElementsByClassName("task_update_action")){
		link.addEventListener("click", function(e) {
		const data=e.srcElement.dataset;
			window.open('plugins/Tasks/pages/task_action_update.php?update_id='+data.taskid+'&id='+data.id+'&time='+data.time, 'TaskUpdate', 'width=800, height=500'); 
			return false;
	});
	}

	for(let link of document.getElementsByClassName("task_edit_action")){
		link.addEventListener("click", function(e) {
		const data=e.srcElement.dataset;
			window.open('plugins/Tasks/pages/task_action_edit.php?edit_id='+data.taskid+'&id='+data.id+'&response='+data.response, 'TaskEdit', 'width=800, height=500'); 
			return false;
	});
	}

	for(let link of document.getElementsByClassName("task_cancel_action")){
		link.addEventListener("click", function(e) {
			window.close();
		});
	}
})