
function validateContentUpload(){
    var title = document.querySelector('input[name="title"]');
    var cat = document.querySelector('select[name="category_id"]');
    var file = document.querySelector('input[name="content_file"]');
    if(title && title.value.trim() === ""){
        alert("Title is required");
        return false;
    }
    if(cat && cat.value === ""){
        alert("Select a category");
        return false;
    }
    if(file && file.files.length === 0 && file.hasAttribute("required")){
        alert("Please select a file to upload");
        return false;
    }
    return true;
}

function deleteContentAjax(id, csrf){
    if(!confirm("Delete this content permanently?")){
        return;
    }
    var x = new XMLHttpRequest();
    x.open("POST", "../control/content_delete_api.php", true);
    x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    x.onload = function(){ location.reload(); };
    x.send("content_id=" + id + "&csrf_token=" + encodeURIComponent(csrf));
}

function deleteModeratorAjax(id, csrf){
    if(!confirm("Delete moderator and all their uploads?")){
        return;
    }
    var x = new XMLHttpRequest();
    x.open("POST", "../control/moderator_delete_api.php", true);
    x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    x.onload = function(){ location.reload(); };
    x.send("moderator_id=" + id + "&csrf_token=" + encodeURIComponent(csrf));
}
