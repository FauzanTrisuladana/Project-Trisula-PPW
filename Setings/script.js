function seekpass(){
    const input1 = document.getElementById("password");
    const input2 = document.getElementById("newpass");
    const input3 = document.getElementById("connewpass");
    input1.type = input1.type === "password" ? "text" : "password";
    input2.type = "password";
    input3.type = "password";
}
function seeknewpass(){
    const input1 = document.getElementById("password");
    const input2 = document.getElementById("newpass");
    const input3 = document.getElementById("connewpass");
    input2.type = input2.type === "password" ? "text" : "password";
    input1.type = "password";
    input3.type = "password";
}
function seekconnewpass(){
    const input1 = document.getElementById("password");
    const input2 = document.getElementById("newpass");
    const input3 = document.getElementById("connewpass");
    input3.type = input3.type === "password" ? "text" : "password";
    input1.type = "password";
    input2.type = "password";
}