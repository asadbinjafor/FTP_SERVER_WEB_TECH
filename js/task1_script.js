
function validateProfileForm(){
    var name = document.querySelector('input[name="name"]');
    if(name && name.value.trim() === ""){
        alert("Name is required");
        return false;
    }
    return true;
}

function validatePasswordForm(){
    var cur = document.getElementById("current_password");
    var np = document.getElementById("new_password");
    var cf = document.getElementById("confirm_password");
    if(!np || !cf){ return true; }
    if(np.value.length < 8){
        alert("New password must be at least 8 characters");
        return false;
    }
    if(np.value !== cf.value){
        alert("Passwords do not match");
        return false;
    }
    if(cur && cur.value === ""){
        alert("Enter current password");
        return false;
    }
    return true;
}

function validateReg(){
    var p = document.getElementById("regPass");
    var c = document.getElementById("regConfirm");
    var email = document.querySelector('input[name="email"]');
    if(email && email.value.indexOf("@") < 1){
        alert("Enter a valid email");
        return false;
    }
    if(p.value.length < 8){
        alert("Password must be at least 8 characters");
        return false;
    }
    if(p.value !== c.value){
        alert("Passwords do not match");
        return false;
    }
    return true;
}
