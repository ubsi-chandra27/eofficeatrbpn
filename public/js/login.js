/*
|--------------------------------------------------------------------------
| E-OFFICE
| Login Javascript
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", () => {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const roleInput = document.getElementById("selected-role");

    const btnAdmin = document.getElementById("btn-admin");
    const btnPegawai = document.getElementById("btn-pegawai");
    const btnUmum = document.getElementById("btn-umum");

    const passwordInput = document.getElementById("password");

    const togglePassword = document.getElementById("togglePassword");

    const toggleIcon = document.getElementById("toggleIcon");

    const loginForm = document.querySelector("form");

    /*
    |--------------------------------------------------------------------------
    | ROLE
    |--------------------------------------------------------------------------
    */

    window.setRole = function(role){

        roleInput.value = role;

        document.querySelectorAll(".role-btn").forEach(button => {

            button.classList.remove("active-role");

        });

        switch(role){

            case "admin":

                btnAdmin.classList.add("active-role");

            break;

            case "pegawai":

                btnPegawai.classList.add("active-role");

            break;

            case "umum":

                btnUmum.classList.add("active-role");

            break;

        }

    };

    /*
    |--------------------------------------------------------------------------
    | DEFAULT ROLE
    |--------------------------------------------------------------------------
    */

    let defaultRole = roleInput.value;

    if(defaultRole == ""){

        defaultRole = "pegawai";

    }

    setRole(defaultRole);

    /*
    |--------------------------------------------------------------------------
    | SHOW PASSWORD
    |--------------------------------------------------------------------------
    */

    if(togglePassword){

        togglePassword.addEventListener("click",function(){

            if(passwordInput.type === "password"){

                passwordInput.type = "text";

                toggleIcon.classList.remove("bi-eye");

                toggleIcon.classList.add("bi-eye-slash");

            }else{

                passwordInput.type = "password";

                toggleIcon.classList.remove("bi-eye-slash");

                toggleIcon.classList.add("bi-eye");

            }

        });

    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN BUTTON LOADING
    |--------------------------------------------------------------------------
    */

    if(loginForm){

        loginForm.addEventListener("submit",function(){

            const submitButton = loginForm.querySelector("button[type='submit']");

            if(submitButton){

                submitButton.disabled = true;

                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Sedang Masuk...
                `;

            }

        });

    }

    /*
    |--------------------------------------------------------------------------
    | INPUT EFFECT
    |--------------------------------------------------------------------------
    */

    const inputs = document.querySelectorAll(".form-control");

    inputs.forEach(input=>{

        input.addEventListener("focus",function(){

            this.parentElement.classList.add("input-focus");

        });

        input.addEventListener("blur",function(){

            this.parentElement.classList.remove("input-focus");

        });

    });

});
