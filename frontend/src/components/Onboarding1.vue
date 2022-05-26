<template>
<div class="onboarding">
    <div class="onboarding_header">
        <div class="image-1">
              <img class="pt-5" src="@/assets/images/undraw_save_to_bookmarks_re_8ajf_1.png" alt=""/>
        </div>
        <div class="dots">
            <img class="" src="@/assets/icons/dots1.svg" alt=""/>
        </div>
    </div>
    <div class="onboarding_footer">
        <div>
            <div class="onboarding_title">Post a job.</div>
            <div class="onboarding_text pt-3">Upload the raw files and
                add a new job.</div>
        </div>
        <div>
            <div class="d-flex justify-content-center">
                <div class="btn-c2" @click="nextOnboarding">Next</div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import RegistrationEnum from "../enums/RegistrationEnum";

export default {
    name: "Onboarding1",
    data() {
        return {};
    },
    mounted() {
    },
    computed: {},
    methods: {
        nextOnboarding() {
            this.$emit('Onboarding1Next',
                RegistrationEnum.ONBOARDING_2,
            )
        }
    }
}
</script>

<style lang="scss" scoped>
.onboarding{
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100vh;
}
.onboarding_header{
    height: 55%;
    display: flex;
    flex-direction: column;
}
.image-1{
    height: 100%;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
}
.image-1 img{
    background: white;
}
@media only screen and (max-width: 480px) {
    .image-1 img{
        width: 100%;
    }
}
.dots{
    padding-bottom: 24px;
    display: flex;
    justify-content: center;
    background: white;
    img{
        background: white;
    }
}
.onboarding_footer{
    height: 45%;
    padding: 44px 51px 59px 51px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.onboarding_title{
    font-family: Montserrat;
    font-style: normal;
    font-weight: 600;
    font-size: 24px;
    line-height: 44px;
    color: #494949;
}
.onboarding_text{
    font-family: Montserrat;
    font-style: normal;
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
    color: #494949;
}
</style>
