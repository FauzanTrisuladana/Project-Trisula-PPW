function nonWajib() {
        document.getElementById("nama").removeAttribute("required");
        document.getElementById("kota").removeAttribute("required");
        document.getElementById("alamat").removeAttribute("required");
        document.getElementById("simpanan-pokok").removeAttribute("required");
        document.getElementById("simpanan-wajib").removeAttribute("required");
        document.getElementById("username").removeAttribute("required");
        document.getElementById("password").removeAttribute("required");
        document.getElementById("konpassword").removeAttribute("required");
        document.getElementById("email").removeAttribute("required");
    }
    function wajib() { // Prevent form submission
        document.getElementById("nama").setAttribute("required", "required");
        document.getElementById("kota").setAttribute("required", "required");
        document.getElementById("alamat").setAttribute("required", "required");
        document.getElementById("simpanan-pokok").setAttribute("required", "required");
        document.getElementById("simpanan-wajib").setAttribute("required", "required");
        document.getElementById("username").setAttribute("required", "required");
        document.getElementById("password").setAttribute("required", "required");
        document.getElementById("konpassword").setAttribute("required", "required");
        document.getElementById("email").setAttribute("required", "required");
    }