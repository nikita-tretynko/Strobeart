<template>
    <div class="item_body">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="in_center" :class="window_desktop ? 'width_450' : 'width_300'">
                    <DatePicker
                        :locale="{id: 'en', masks: {weekdays: 'WW'}}"
                        mode='range'
                        v-model='date'
                        :is-expanded = "true"
                        title-position="left"
                        @dayclick="onDayClick"
                        :min-date="new Date()"
                        :max-date="(new Date()).setDate((new Date()).getDate()+6)"
                        show-caps>
                    </DatePicker>
<!--                    <Calendar-->
<!--                        :locale="{id: 'en', masks: {weekdays: 'WW'}}"-->
<!--                        :is-expanded = "true"-->
<!--                        title-position="left"-->
<!--                        :attributes="attributes_calendar"-->
<!--                        :min-date="new Date()"-->
<!--                        :max-date="(new Date()).setDate((new Date()).getDate()+6)"-->
<!--                        @dayclick="onDayClick"-->
<!--                        :v-model="date"></Calendar>-->
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-5 m_t_70 m_b_126" v-if="window_desktop">
            <div class="text-center">
                <button class="text-center custom_button white_background main_text"
                        @click="goHome">
                    Cancel
                </button>
            </div>
            <div class="text-center">
                <button
                    class="text-center custom_button braun_background white_text"
                    :class="this.dates.due_date ? '' : 'disabled_button'"
                    :disabled="!this.dates.due_date"
                    @click="$emit('submit-form')">
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Calendar from 'v-calendar/lib/components/calendar.umd';
import DatePicker from 'v-calendar/lib/components/date-picker.umd';
import { setupCalendar} from 'v-calendar'

setupCalendar({
    componentPrefix: 'vc',
});

export default {
    name: 'AddDueDate',
    components: {
        Calendar,
        DatePicker,
    },
    props: [
        'dates'
    ],
    data() {
        return {
            sizeW: null,
            window_desktop: true,
            date: null,
            selected_date: null,
            attributes_calendar: [
                {
                    highlight: {
                        style: {
                            background: '#D5C4B1'
                        },
                        contentStyle: {
                            color: '#FFFFFF',
                            fontStyle: 'normal',
                            fontWeight: '600',
                            fontSize: '12px',
                            lineHeight: '15px',
                        }
                    },
                    //dates: this.dates.due_date ? new Date(this.dates.due_date) : new Date(),
                }
            ],
            time: new Date(),
            attributes_time: [
                {
                    highlight: {
                        style: {
                            background: '#D5C4B1'
                        },
                    },
                   // dates: this.dates.due_date ? new Date(this.dates.due_date) : new Date(),
                },
            ],
            time_buttons: [
                '10:00',
                '12:30',
                '04:30',
                '05:30',
                '08:00',
                '09:30',
            ],
        }
    },
    created() {
        window.addEventListener('resize', this.updateWidth);
        this.updateWidth();
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.updateWidth);
    },
    methods: {
        updateWidth() {
            this.sizeW = document.documentElement.clientWidth;
        },
        goHome() {
            this.$router.push({to: '/business-homepage', name: "BusinessHomePage"});
        },
        onDayClick(day) {
            this.attributes_time[0].dates = day.id;
            this.dates.due_date = this.date;
        },
        nextTab() {

        }
    },
    computed: {
    },
    watch: {
        sizeW(val) {
            this.window_desktop = val > 992;
        },
        date(val) {
            console.log(val);
        }
    }
}
</script>
<style>
.in_center .is-disabled{
    pointer-events: none;
}
.in_center .vc-highlight{
    background-color: #D8C3AF!important;
    color: white!important;
}
.in_center .vc-day-content:focus {
    background-color: #D8C3AF!important;
    color: white!important;
}
</style>
<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;

.main_text {
    color: #494949;
    font-style: normal;
    font-family: $primary_font_family;
}
.white_text {
    color: #FFFFFF;
    font-style: normal;
    font-family: $primary_font_family;
}
.title_desktop {
    font-weight: 600;
    font-size: 35px;
    line-height: 44px;
}
.description_desktop {
    font-weight: normal;
    font-size: 17px;
    line-height: 24px;
}
.description_mobile {
    font-weight: 500;
    font-size: 12.5px;
    line-height: 20px;
}
.item_body {
    width: 100%;
    min-height: 340px;
    padding: 11px;
}
.m_bt_16 {
    margin: 16px 0;
}
.time_button {
    width: 86px;
    height: 41px;
    border: 0.3px solid #494949;
    box-sizing: border-box;
    border-radius: 100px;
    padding: 10px;
    font-weight: 500;
    font-size: 12.5px;
    line-height: 20px;
}
.time_button .active {
    background: #D5C4B1;
}
.custom_button {
    width: 200px;
    height: 60px;
    border: 0.5px solid #494949;
    box-sizing: border-box;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.25);
    border-radius: 100px;
}
.braun_background {
    background: #D8C3AF;
}
.white_background {
    background: #FFFFFF;
}
.disabled_button {
    opacity: 0.45;
}
.width_450 {
    width: 450px;
}
.width_300 {
    width: 300px;
}
.in_center {
    margin: 0 auto;
}
.m_t_70 {
    margin-top: 70px;
}
.m_b_126 {
    margin-bottom: 126px;
}
</style>
