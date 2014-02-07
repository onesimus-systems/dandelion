function showCats(mD, parent) {
    window.XMLHttpRequest ? xmlhttp=new XMLHttpRequest() : xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      
    xmlhttp.onreadystatechange=function()
      {
          if (xmlhttp.readyState===4 && xmlhttp.status===200)
            {                
                document.getElementById("workArea").innerHTML=xmlhttp.responseText;
            }
      }
      
    xmlhttp.open("POST",'scripts/categories.php',true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("maxdepth=" + mD + "&parent=" + parent);
}