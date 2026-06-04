
function escapeHtml(t){
    var d = document.createElement("div");
    d.textContent = t;
    return d.innerHTML;
}

function showMsg(id, text, ok){
    var el = document.getElementById(id);
    if(!el){ return; }
    el.innerHTML = "<div class='" + (ok ? "msg-success" : "msg-error") + "'>" + escapeHtml(text) + "</div>";
}

function renderContentCards(items){
    var grid = document.getElementById("contentGrid");
    if(!grid){ return; }
    if(!items || items.length === 0){
        grid.innerHTML = "<div class='empty-state'>No content found. Try another search or category.</div>";
        return;
    }
    var html = "";
    for(var i = 0; i < items.length; i++){
        var c = items[i];
        html += "<article class='content-card'>";
        html += "<h3>" + escapeHtml(c.title) + "</h3>";
        html += "<p class='meta'>" + escapeHtml(c.category_name || "Uncategorized") + " · " + escapeHtml(c.uploader_name || "") + "</p>";
        html += "<p class='desc'>" + escapeHtml(c.description || "") + "</p>";
        html += "<div class='card-foot'>";
        html += "<span class='file-type-tag'>" + escapeHtml(c.file_type || "file") + "</span>";
        html += "<span class='download-count'>" + c.download_count + " downloads</span>";
        if(c.file_available && c.download_url){
            html += "<a class='btn-primary' href='" + escapeHtml(c.download_url) + "'>Download</a>";
        } else {
            html += "<span class='btn-secondary' style='opacity:0.7'>Unavailable</span>";
        }
        html += "</div></article>";
    }
    grid.innerHTML = html;
}

function loadContentsAjax(){
    var q = document.getElementById("searchQ");
    var cat = document.getElementById("filterCategory");
    var sub = document.getElementById("filterSub");
    var ft = document.getElementById("filterFileType");

    var qs = "q=" + encodeURIComponent(q ? q.value.trim() : "");
    qs += "&category=" + encodeURIComponent(cat ? cat.value : "0");
    qs += "&sub=" + encodeURIComponent(sub ? sub.value : "0");
    qs += "&file_type=" + encodeURIComponent(ft ? ft.value : "");

    if(q && q.value.trim().length === 1){
        showMsg("searchMsg", "Enter at least 2 characters to search", false);
        return;
    }

    var grid = document.getElementById("contentGrid");
    if(grid){
        grid.innerHTML = "<div class='empty-state'>Loading...</div>";
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState === 4 && this.status === 200){
            try{
                var data = JSON.parse(this.responseText);
                if(data.success){
                    renderContentCards(data.items);
                    showMsg("searchMsg", data.count + " result(s)", true);
                } else {
                    showMsg("searchMsg", data.message || "Search failed", false);
                }
            } catch(e){
                showMsg("searchMsg", "Invalid response", false);
            }
        }
    };
    xhttp.open("GET", "../control/content_search_api.php?" + qs, true);
    xhttp.send();
}

function loadSubcategories(){
    var cat = document.getElementById("filterCategory");
    var sub = document.getElementById("filterSub");
    if(!cat || !sub){ return; }
    var pid = cat.value;
    sub.innerHTML = "<option value='0'>All subcategories</option>";
    if(pid === "0"){ return; }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState === 4 && this.status === 200){
            var data = JSON.parse(this.responseText);
            if(data.success){
                for(var i = 0; i < data.items.length; i++){
                    var o = document.createElement("option");
                    o.value = data.items[i].id;
                    o.textContent = data.items[i].name;
                    sub.appendChild(o);
                }
            }
        }
    };
    xhttp.open("GET", "../control/subcategories_api.php?parent_id=" + encodeURIComponent(pid), true);
    xhttp.send();
}

function validateRequestBox(){
    var title = document.getElementById("reqTitle");
    var cat = document.getElementById("reqCategory");
    if(!title || !cat){ return true; }
    if(title.value.trim() === ""){
        showMsg("requestMsg", "Content title is required", false);
        title.focus();
        return false;
    }
    if(cat.value.trim() === ""){
        showMsg("requestMsg", "Please select a category", false);
        return false;
    }
    return true;
}

function submitRequestAjax(e){
    if(e){ e.preventDefault(); }
    if(!validateRequestBox()){ return false; }

    var csrf = document.getElementById("reqCsrf");
    var title = document.getElementById("reqTitle");
    var cat = document.getElementById("reqCategory");
    var msg = document.getElementById("reqMessage");

    var body = "csrf_token=" + encodeURIComponent(csrf ? csrf.value : "");
    body += "&content_title=" + encodeURIComponent(title.value.trim());
    body += "&category_requested=" + encodeURIComponent(cat.value);
    body += "&message=" + encodeURIComponent(msg ? msg.value.trim() : "");

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState === 4 && this.status === 200){
            try{
                var data = JSON.parse(this.responseText);
                showMsg("requestMsg", data.message, data.success);
                if(data.success){
                    document.getElementById("requestForm").reset();
                }
            } catch(err){
                showMsg("requestMsg", "Server error", false);
            }
        }
    };
    xhttp.open("POST", "../control/request_add_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(body);
    return false;
}

document.addEventListener("DOMContentLoaded", function(){
    var cat = document.getElementById("filterCategory");
    if(cat){
        cat.addEventListener("change", loadSubcategories);
    }
    var searchBtn = document.getElementById("btnSearch");
    if(searchBtn){
        searchBtn.addEventListener("click", function(e){
            e.preventDefault();
            loadContentsAjax();
        });
    }
    var searchQ = document.getElementById("searchQ");
    if(searchQ){
        searchQ.addEventListener("keydown", function(e){
            if(e.key === "Enter"){
                e.preventDefault();
                loadContentsAjax();
            }
        });
    }
    var reqForm = document.getElementById("requestForm");
    if(reqForm){
        reqForm.addEventListener("submit", submitRequestAjax);
    }
});
