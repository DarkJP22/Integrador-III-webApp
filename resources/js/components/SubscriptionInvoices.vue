<script setup>
import {computed, onMounted, reactive, ref} from 'vue';
import Pagination from '@/components/Pagination.vue';
import BaseFormatMoney from '@/components/base/BaseFormatMoney.vue';

const props = defineProps({
    invoicePaidStatuses: {
        type: Object,
        required: true
    },
    statuses: {
        type: Array,
        required: false
    },
    endpoint: {
        type: String,
        default() {
            return '/subscription/invoices';
        }
    }
});

const form = reactive({
    paid_status: ''
});

const subscriptionInvoices = ref([]);
const errors = ref(null);
const invoiceDetail = ref(null);
const voucher = ref(null);
const meta = ref({});
const isLoading = ref(false);
const filters = ref({
    q: '',
    status: []
});

const isAdminView = computed(() => {
    return props.endpoint.startsWith('/admin');
});
const commissionByAppointments = computed(() => {
    if (invoiceDetail.value === null) return 0;

    return invoiceDetail.value.customer?.specialities?.length ? invoiceDetail.value.customer?.subscription?.plan.specialist_cost_commission_by_appointment : invoiceDetail.value.customer?.subscription?.plan.general_cost_commission_by_appointment;
});
const commissionDiscount = computed(() => {
    if (invoiceDetail.value === null) return 0;

    return invoiceDetail.value.customer?.subscription?.plan.commission_discount;
});
const commissionDiscountRangeInMinutes = computed(() => {
    if (invoiceDetail.value === null) return 0;

    return invoiceDetail.value.customer?.subscription?.plan.commission_discount_range_in_minutes;
});

function getSubscriptionInvoices(page = 1) {
    if (page < 0) page = 1;

    axios.get(props.endpoint, {
        params: {
            page,
            ...filters.value
        }
    })
        .then(({data}) => {
            subscriptionInvoices.value = data.data;
            meta.value = data.meta;
        })
        .catch(error => {
            errors.value = error.response.data.errors;
        });
}

function uploadVoucher(invoice) {
    isLoading.value = true;
    axios.postForm(`/subscription/invoices/${invoice.id}/upload-voucher`, {
        voucher: voucher.value
    })
        .then(({data}) => {
            isLoading.value = false;
            const idx = subscriptionInvoices.value.findIndex(i => i.id === invoice.id);
            subscriptionInvoices.value[idx] = data.data;
        })
        .catch(error => {
            isLoading.value = false;
            flash('Ocurrió un error!!', 'danger');
            this.errors = error.response.data.errors ? error.response.data.errors : [];
        });
}

function search() {
    getSubscriptionInvoices();
}

function onSave(event, invoice) {
    //console.log(event.target.value);
    form.paid_status = event.target.value;
    if (!confirm('¿Estas seguro de cambiar el estado de la factura?')) return;
    axios.put(`${props.endpoint}/${invoice.id}`, form)
        .then(({data}) => {
            const idx = subscriptionInvoices.value.findIndex(i => i.id === invoice.id);
            subscriptionInvoices.value[idx] = data.data;
            flash('Estado Cambiado Correctamente', 'success');
        })
        .catch(error => {
            flash('Ocurrió un error!!', 'danger');
            this.errors = error.response.data.errors ? error.response.data.errors : [];
        });
}

function changeFile(e) {
    voucher.value = e.target.files[0];
}

function downloadPDF(invoice) {
    window.open(`${props.endpoint}/${invoice.id}/pdf`, '_blank');

}

onMounted(() => {
    getSubscriptionInvoices();
});
</script>

<template>
    <div>

        <h2>Historial de facturas</h2>

        <div class="row">
            <div class="col-sm-4 tw-mb-4">
                <input v-model="filters.q" class="form-control " placeholder="Buscar..." type="search" @keyup.enter="search">
            </div>
            <div class="col-sm-4 tw-mb-4">
                <select id="status" v-model="filters.status" class="form-control" name="status" @change="search">
                    <option value="">Todas</option>
                    <option v-for="status in statuses" :key="status.id" :value="status.id">{{ status.name }}</option>

                </select>
            </div>


        </div>

        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th v-if="isAdminView">Cliente</th>
                        <th>Numero</th>
                        <th>Description</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Comprobante</th>
                        <th v-if="isAdminView">Cambiar Estado</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr v-for="invoice in subscriptionInvoices" :key="invoice.id">
                        <td><a class="btn btn-primary" data-target="#modalSubscriptionInvoiceDetail"
                               data-toggle="modal"
                               href="#"
                               title="Detalle" @click="invoiceDetail = invoice"><i
                            class="fa fa-list"></i>
                        </a></td>
                        <td v-if="isAdminView">
                            {{ invoice.customer?.name }}
                            <div>
                                {{ invoice.customer?.ide }}
                            </div>
                        </td>
                        <td>
                            {{ invoice.invoice_number }}
                            <div>
                                <b>Vence: {{ invoice.due_date }}</b>
                            </div>
                        </td>
                        <td>{{ invoice.notes }}</td>
                        <td>
                            <BaseFormatMoney :amount="invoice.total"/>
                        </td>
                        <td>
                        <span :class="{
                        'label': true,
                        'label-success' : invoice.paid_status === invoicePaidStatuses.PAID,
                        'label-warning' : invoice.paid_status === invoicePaidStatuses.CHECKING,
                        'label-danger' : invoice.paid_status === invoicePaidStatuses.UNPAID,
                        'label-dark bg-dark' : invoice.paid_status === invoicePaidStatuses.REFUSED,
                        }">

                        {{ invoice.paid_status_label }}
                        </span>
                        </td>
                        <td>
                            <div v-if="isAdminView">
                                <a v-if="invoice.comprobante_url" :href="invoice.comprobante_url" download target="_blank">Descargar
                                    Comprobante</a>
                                <p v-else>El usuario no ha subido un comprobante de pago aun</p>
                            </div>
                            <div v-else>

                                <form
                                    v-if="invoice.paid_status === invoicePaidStatuses.UNPAID || invoice.paid_status === invoicePaidStatuses.REFUSED"
                                    enctype="multipart/form-data" method="POST"
                                    @submit.prevent="uploadVoucher(invoice)">

                                    <input name="voucher" type="file" @change="changeFile">

                                    <form-error v-if="errors?.voucher" :errors="errors" style="float:right;">
                                        {{ errors.voucher[0] }}
                                    </form-error>

                                    <button :disabled="isLoading" class="btn btn-secondary btn-sm" type="submit">Subir
                                        Comprobante
                                    </button>
                                </form>

                                <a v-else-if="invoice.comprobante_url" :href="invoice.comprobante_url" download target="_blank">Descargar Comprobante</a>
                            </div>

                        </td>
                        <td v-if="isAdminView">
                            <select :id="'status-'+ invoice.id" :key="invoice.id" :name="'status-'+ invoice.id" class="form-control"
                                    @change="onSave($event, invoice)">
                                <option value=""></option>
                                <option v-for="status in statuses" :key="status.id" :value="status.id">{{
                                        status.name
                                    }}
                                </option>

                            </select>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <div>
                    <Pagination :links="meta.links" @changed="getSubscriptionInvoices"></Pagination>

                </div>
            </div>

        </div>
        <div v-if="invoiceDetail" id="modalSubscriptionInvoiceDetail" aria-labelledby="modalSubscriptionInvoiceDetail" class="modal fade" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header tw-bg-blue-400 tw-text-white">
                        <div class="tw-flex tw-justify-between tw-items-center">

                            <h4 id="modalSubscriptionInvoiceDetailLabel" class="modal-title">Detalle Factura
                                {{ invoiceDetail.invoice_number }}</h4>
                            <div class="">
                        <span :class="{
                        'label': true,
                        'label-success' : invoiceDetail.paid_status === invoicePaidStatuses.PAID,
                        'label-warning' : invoiceDetail.paid_status === invoicePaidStatuses.CHECKING,
                        'label-danger' : invoiceDetail.paid_status === invoicePaidStatuses.UNPAID,
                        'label-info' : invoiceDetail.paid_status === invoicePaidStatuses.REFUSED,
                        }">

                        {{ invoiceDetail.paid_status_label }}
                        </span>
                            </div>
                        </div>

                    </div>

                    <div class="modal-body ">

                        <div
                            class=" tw-border-0 tw-border-solid tw-border-b tw-border-gray-200 tw-py-5 tw-flex tw-justify-between">
                            <span class="tw-font-bold">Nombre:</span> <span
                            class="tw-text-right">{{ invoiceDetail.customer?.name }} <br> {{
                                invoiceDetail.customer?.ide
                            }}</span>
                        </div>
                        <div
                            class="tw-border-0 tw-border-solid tw-border-b tw-border-gray-200 tw-py-5 tw-flex tw-justify-between">
                            <span class="tw-font-bold"> Fecha: </span> <span class="">{{ invoiceDetail.notes }}</span>
                        </div>
                        <div
                            class="tw-border-0 tw-border-solid tw-border-b tw-border-gray-200 tw-py-5 tw-flex tw-justify-between">
                            <span class="tw-font-bold"> Vence: </span> <span class="">{{ invoiceDetail.due_date }}</span>
                        </div>


                        <div
                            class="tw-mb-4 tw-font-bold tw-border-0 tw-border-solid tw-border-b tw-py-5 tw-border-gray-200">
                            Comisión por cita:
                            <BaseFormatMoney :amount="commissionByAppointments"/>
                        </div>
                        <div
                            class="tw-mb-4 tw-font-bold tw-border-0 tw-border-solid tw-border-b tw-py-5 tw-border-gray-200">
                            Descuento de Comisión: {{ commissionDiscount }}%
                            <div class="tw-font-normal">
                                Rango de tiempo para aplicar: {{ commissionDiscountRangeInMinutes }} min.
                            </div>
                        </div>
                        <div class="tw-space-y-2 tw-mb-8">

                            <div v-for="line in invoiceDetail.items" :key="line.id"
                                 class="tw-flex tw-justify-between tw-py-4">
                                <div>{{ line.name }}</div>
                                <div class="tw-font-bold">{{ line.quantity }} =
                                    <BaseFormatMoney :amount="line.total"/>
                                </div>

                            </div>
                        </div>
                        <div
                            class="tw-border-0 tw-border-solid tw-border-gray-200 tw-border-t tw-py-4 tw-flex tw-justify-between">
                            <span> Monto Total:</span>
                            <BaseFormatMoney :amount="invoiceDetail.total" class="tw-font-bold"/>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="downloadPDF(invoiceDetail)">Descargar PDF</button>
                        <button class="btn btn-default" data-dismiss="modal" type="button" @click="invoiceDetail = null">
                            Cerrar
                        </button>


                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
