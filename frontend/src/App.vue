<template>
    <div id="app">
        <router-view/>
        <Loader/>
    </div>
</template>
<script>
import Loader from "./components/Loader";

export default {
    components: {
        Loader
    },
    methods: {
    },

}
</script>
<style lang="scss">
@import "~toastify-js/src/toastify.css";

* {
    // background: #F4F2F2;
}

#app {
    font-family: Montserrat;
}

main {
    flex-grow: 1;
}
body{

}
.footer {
    flex-grow: 0;
    background: #FCF5EF;
    padding: 95px 105px 148px 88px;
}

.app_logo {
    width: 100%;
    text-align: center;
}

select:focus,
textarea:hover,
input:hover,
textarea:active,
input:active,
textarea:focus,
input:focus,
button:focus,
button:active,
button:hover,
label:focus,
.btn:active,
.btn.active {
    outline: 0 !important;
    -webkit-appearance: none;
    box-shadow: none !important;
}
.cp{
    cursor: pointer;
}
@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-VariableFont_wght.ttf) format("truetype");
}

@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-SemiBold.ttf) format("truetype");
    font-weight: 600;
}

@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-Medium.ttf) format("truetype");
    font-weight: 500;
}

@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-Regular.ttf) format("truetype");
    font-weight: 400;
}

@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-Light.ttf) format("truetype");
    font-weight: 300;
}
@font-face {
    font-family: "Montserrat";
    src: local("Montserrat"),
    url(./assets/fonts/Montserrat/Montserrat-ExtraLight.ttf) format("truetype");
    font-weight: 200;
}
.montserrat .form-control, .montserrat .form-select {
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 16px;
    line-height: 20px;
    color: #000000;
    padding: 15px 17px;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 10px;
}

.montserrat .form-control:focus, .montserrat .form-select:focus {
    border-color: #cccccc;
    -webkit-box-shadow: none;
    box-shadow: none;
}

.link-s1 {
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 18px;
    display: flex;
    align-items: center;
    text-align: center;
    text-decoration-line: underline;
    color: #000000;
}

.btn-c2 {
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;

    color: #000000;
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
    padding: 17px 23px;
    text-align: center;
    cursor: pointer;
    width: 250px;
    white-space: nowrap;
}

.btn-c3 {
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    border-radius: 10px;
    cursor: pointer;
    padding: 7px 33px;
    white-space: nowrap;
    font-family: Montserrat;
    font-style: normal;
    font-weight: 500;
    font-size: 16px;
    line-height: 20px;
    color: #FFFFFF;
}
.btn-mc3{
    font-style: normal;
    font-weight: 500;
    font-size: 14px;
    line-height: 17px;
    color: #494949;
    background: #FFFFFF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
  height: 60px;
}
.btn-mc2{
    font-style: normal;
    font-weight: 600;
    font-size: 18px;
    line-height: 22px;
    color: #FFFFFF;
    background: #D8C3AF;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.btn-c3:hover {
    opacity: 0.5;
}

.toastify {
    font-style: normal;
    font-weight: normal;
    font-size: 14px;
    line-height: 22px;
    border-radius: 4px;
    overflow: hidden;

    .toast-close {
        color: #ffffff00;
        position: relative;
        background: none;

        &::after {
            content: url("./assets/images/close_toast_icon.svg");
            position: absolute;
            right: 4px;
        }
    }
}

.not-active-btn {
    cursor: default;
    opacity: 0.5;
}
@media only screen and (max-width: 992px) {
    body {
        background: #F4F2F2!important;
    }
}
@media only screen and (min-width: 993px) {
    .onboarding_footer{
       text-align: center;
    }
    .app-content{
        padding: 0 96px;;
    }
    .box_container{
        padding-bottom: 100px;
    }
    .app-content {
        background: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 2 2%22%3E %3Ccircle r=%221%22 cx=%221%22 cy=%221%22 fill=%22%23ffffff%22/%3E %3C/svg%3E'), #FCF5EF;
        background-repeat: no-repeat, no-repeat;
        background-position: bottom center, center;
        background-size: 400%, cover;
    }
}
</style>
