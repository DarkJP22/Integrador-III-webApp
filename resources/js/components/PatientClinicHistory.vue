<template>
    <div class="tw-mb-4">
        <div class="tw-mb-4">
            <input v-model="q" class="form-control input-sm" placeholder="Buscar..." type="search" @keyup.enter="search">
        </div>
        <div class="row">
           
            <div v-for="appointment in appointments" :key="appointment.id" class="col-md-6 col-lg-4">

                <summary-appointment v-if="appointment.show_authorized" :diagnostics="appointment.diagnostics" :exams="appointment.physical_exams" :instructions="appointment.medical_instructions" :labexams="appointment.labexams" :medicines="appointment.patient.medicines" :notes="appointment.disease_notes" :treatments="appointment.treatments" history="">
                    Historia Clinica {{ parseDate(appointment.start) }}<br>
                    <small>Dr(a). {{ appointment.user?.name }}</small>
                </summary-appointment>

                <div v-else class="box box-solid">
                    <div class="box-header with-border">
                        <i class="fa fa-book"></i>

                        <h3 class="box-title">
                            Historia Clínica - {{ parseDate(appointment.start) }}<br>
                            <small>Dr(a). {{ appointment.user?.name }}</small>
                        </h3>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body summary-flex">
                         <AuthorizationPatientByCode :callback="'/general/patients/'+ appointment.patient_id + '?tab=history'" :patient="appointment.patient"></AuthorizationPatientByCode>
                    </div>
                </div>

            </div>
           

        </div>
        <div>
             <Pagination :links="dataSet.links" @changed="fetch"></Pagination>
        </div>
    </div>
</template>
<script>
import Pagination from './Pagination.vue';
import {format, parseISO} from 'date-fns';
export default {
    props: {
        patient: {
            type: Object,
            required: true
        }
    },
    components: {
        Pagination
    },
    data() {
        return {
            q: '',
            appointments: [],
            dataSet: {},
            isLoading: false
        };
    },
    methods: {
        parseDate(date) {
            if(!date) return '';
            return format(parseISO(date), 'yyyy-MM-dd');
        },
        search() {

            this.fetch();

        },
        fetch(page) {
            this.isLoading = true;
            axios.get(this.url(page)).then(this.refresh);
        },
        url(page) {
            if (!page) {
                // let query = location.search.match(/page=(\d+)/);
                page = 1; //query ? query[1] : 1;
            }
            let url = `/patients/${this.patient.id}/clinic-history?page=${page}`;

            if (this.q) {
                url += `&q=${this.q}`;
            }
            return url;
        },
        refresh({ data }) {
            this.isLoading = false;
            this.dataSet = data.meta;
            this.appointments = data.data;
            window.scrollTo(0, 0);
        },
    },
    created() {
        this.fetch();
    }

};
</script>