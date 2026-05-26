<?php
function viewBase(){
    return rtrim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "")), "/") . "/";
}

function viewPath($file){
    return viewBase() . ltrim($file, "/");
}
