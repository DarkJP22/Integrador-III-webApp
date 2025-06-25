<template>
    <div>
        <h2>Descuento para usuarios de Doctor Blue</h2>
        <div class="box box-primary">
            <loading :show="loader"></loading>

            <div class="box-header">
                <div class="form-group">
                    <label class="col-sm-12 control-label" for="name">Buscar Paciente</label>
                    <div class="col-sm-12">
                        <input v-model="q" autocomplete="false" class="form-control"
                               name="name" placeholder="Buscar por identificación o nombre" type="text"
                               @keyup="onSearch"/>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table v-show="items.length" class="table table-hover">
                    <tbody>
                    <tr>
                        <th>Identificacíon</th>
                        <th>Nombre</th>
                    </tr>
                    <tr v-for="item in items" :key="item.id">
                        <td data-title="Identificación">
                            <a href="#" @click="selectItem(item)">{{ item.ide }}</a>
                        </td>

                        <td data-title="Nombre">
                            <a :title="item.name" href="#" @click="selectItem(item)">{{ item.name }}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-show="!items.length && q" class="" style="text-align: center">
                    <h3>Paciente no encontrado</h3>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <form class="" @submit.prevent="save()">
            <div v-if="form.user_id" class="box box-primary">
                <div class="box-body box-profile">
                    <img alt="User profile picture" class="profile-user-img img-responsive img-circle"
                         src="/img/default-avatar.jpg"/>

                    <h3 class="profile-username text-center">
                        {{ selectedPatient.name }}
                    </h3>

                    <p class="text-muted text-center">{{ selectedPatient.ide }}</p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>Total Venta</b>
                            <div class="row">
                                <div class="col-sm-9">
                                    <input v-model="form.cost" class="form-control" required type="text"/>
                                    <form-error v-if="errors.cost" :errors="errors">
                                        {{ errors.cost[0] }}
                                    </form-error>
                                </div>
                                <div class="col-sm-3">
                                    <select v-model="form.CodigoMoneda" class="form-control" name="CodigoMoneda">
                                        <option v-for="currency in currencies" :key="currency.code"
                                                :disabled="currency.code != 'CRC'" :value="currency.code">
                                            {{ currency.code }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <b>Descuento Disponible</b>
                            <div class="row">
                                <div class="col-sm-9">
                                    <input v-model="availableDiscount" class="form-control" readonly type="text"/>
                                </div>
                                <div class="col-sm-3">
                                    <input v-model="porcDiscount" class="form-control" readonly type="text"/>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <b>Saldo Canjeable</b>
                            <div class="">
                                <input v-model="accumulatedAmount" class="form-control" readonly type="text"/>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <b>Total</b>
                            <div class="">
                                <input v-model="totalCost" class="form-control" readonly type="text"/>
                            </div>
                        </li>
                    </ul>

                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <button class="btn btn-secondary" type="button" @click="close()">
                        Cerrar
                    </button>
                </div>
                <!-- /.box-body -->
            </div>
        </form>
    </div>
</template>
<script>
import Loading from './Loading.vue';

export default {
    props: ['currencies', 'settings', 'general_cost_appointment'],
    components: {
        Loading,
    },
    data() {
        return {
            loader: false,
            errors: [],
            items: [],
            selectedPatient: {},
            q: '',
            accumulated: {},
            form: {
                user_id: '',
                CodigoMoneda: 'CRC',
                cost: 0,
                available_accumulated_discount: 0,
                total_cost: 0,
            },
        };
    },
    watch: {
        cTotal(value) {
            this.form.total_cost = value;
        },
    },
    computed: {
        porcDiscount() {
            const acumulado = this.accumulated?.acumulado ?? 0;
            const defaultDiscount = parseFloat(
                this.settings.porc_discount_accumulated ?? 0
            );
            const maxAccumulatedDiscount = (defaultDiscount / 100) * this.form.cost;

            if (maxAccumulatedDiscount <= acumulado) {
                return defaultDiscount;
            }

            return redondeo((acumulado / this.form.cost) * 100);
        },
        cTotal() {
            this.form.available_accumulated_discount = this.form.cost * (this.porcDiscount / 100) ?? 0;
            return this.form.cost - this.form.available_accumulated_discount;
        },
        generalCost() {
            return this.moneyFormat(this.form.cost);
        },
        accumulatedAmount() {
            return this.moneyFormat(parseFloat(this.accumulated?.acumulado));
        },
        totalCost() {
            return this.moneyFormat(this.form.total_cost);
        },
        availableDiscount() {
            return this.moneyFormat(this.form.available_accumulated_discount);
        },
    },
    methods: {
        moneyFormat(n) {
            if (typeof n === 'number') {
                return n.format(2);
            }

            return n;
        },
        close() {
            this.items = [];
            this.q = '';
            this.selectedPatient = {};
            this.clear(true);
        },
        clear(full) {
            if (full) {
                this.form.user_id = '';
            }

            this.form.cost = 0;
            this.form.available_accumulated_discount = 0;
            this.form.total_cost = 0;
        },
        save() {
            const dataToSave = {
                amount: this.form.cost,
                discount: this.porcDiscount,
                total_discount: this.form.available_accumulated_discount,
                total: this.form.total_cost,
                CodigoMoneda: this.form.CodigoMoneda,
            };

            axios
                .post(`/users/${this.form.user_id}/discounts`, dataToSave)
                .then((resp) => {
                    this.loader = false;
                    flash('Descuento Agregado', 'success');
                    this.accumulated = resp.data.accumulated;
                    this.clear();
                })
                .catch((error) => {
                    this.loader = false;
                    flash('Error al guardar descuento', 'danger');
                    this.errors = error.response.data.errors
                        ? error.response.data.errors
                        : [];
                });
        },
        selectItem(item) {
            this.selectedPatient = item;
            this.form.user_id = item.id;
            this.items = [];
            this.q = '';
            this.accumulated = item.accumulated ?? {};
        },
        onSearch(search) {
            this.search(search.target.value, this);
        },
        search: _.debounce((search, vm) => {
            const url = `/available-patients-for-discounts?q=${search}`;
            vm.loader = true;
            axios
                .get(url)
                .then((response) => {
                    vm.items = response.data.data;
                    vm.loader = false;
                })
                .catch(() => {
                    vm.loader = false;
                    flash('Error al consultar datos', 'danger');
                });
        }, 350),
    },
    created() {
        this.form.cost = parseFloat(this.general_cost_appointment ?? 0);
        // this.form.available_accumulated_discount = (this.form.cost * (this.settings.porc_discount_accumulated / 100) ?? 0);
    },
};
</script>
