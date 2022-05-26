<template>
    <div class="calendar_b">
        <DatePicker
            mode='range'
            :is-expanded = "true"
            title-position="left"
            @dayclick="onDayClick"
            :min-date="new Date()"
            v-model="selectedDate"
            show-caps>
        </DatePicker>
<!--        <Calendar-->
<!--            :locale="{id: 'en', masks: {weekdays: 'WW'}}"-->
<!--            :is-expanded=true-->
<!--            title-position="left"-->
<!--            :attributes="attributes_calendar"-->
<!--            :min-date="new Date()"-->
<!--            @dayclick="onDayClick"-->
<!--            :v-model="selectedDate">-->
<!--        </Calendar>-->

    </div>
</template>

<script>
import Calendar from 'v-calendar/lib/components/calendar.umd';
import DatePicker from 'v-calendar/lib/components/date-picker.umd';
import {setupCalendar} from 'v-calendar'
import JobWorkStepsEnum from "../enums/JobWorkStepsEnum";
import moment from 'moment-timezone'

setupCalendar({
    componentPrefix: 'vc',
});

export default {
    name: 'MyCalendar',
    components: {
        Calendar,
        DatePicker,
    },
    data() {
        return {
            selectedDate: null,
            attributes_calendar: [
                {
                    highlight: {
                        style: {
                            background: '#D5C4B1'
                        },
                        contentStyle: {
                            background: '#D5C4B1',
                            color: '#FFFFFF',
                            fontStyle: 'normal',
                            fontWeight: '600',
                            fontSize: '12px',
                            lineHeight: '15px',
                        }
                    },
                    //dates: this.selectedDate ? new Date(this.selectedDate) : new Date(),
                }
            ],
        }
    },
    created() {
    },
    methods: {
        onDayClick(day) {
            this.selectedDate = moment(this.selectedDate).format('YYYY-MM-DD');
            this.$emit('selected_date',
                this.selectedDate)
        }
    },
    computed: {},
    watch: {}
}
</script>
<style>
.calendar_b .is-disabled{
    pointer-events: none;
}
.calendar_b .vc-highlight{
    background-color: #D8C3AF!important;
    color: white!important;
}
.calendar_b .vc-day-content:focus {
    background-color: #D8C3AF!important;
    color: white!important;
}
</style>
<style lang="scss" scoped>
$primary_font_family: 'Montserrat', sans-serif;

</style>
