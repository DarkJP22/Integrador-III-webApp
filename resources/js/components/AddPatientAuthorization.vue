<template>
    <div class="form-horizontal flex-container-ce">

        <a href="#" @click="requestCode()" title="Solicitar Código de autorización" class="btn btn-primary">Solicitar
            Código</a>
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" class="btn btn-danger" @click="add()" title="Agregar a tu lista"><i
                    class="fa fa-plus"></i> Agregar a tu lista
                </button>
            </div>
            <!-- /btn-group -->
            <input type="text" name="code" placeholder="Código de confirmación" class="form-control" required="required"
                   v-model="code"/>

        </div>
        <form-error v-if="errors.code" :errors="errors" style="float:right;">
            {{ errors.code[0] }}
        </form-error>


    </div>
</template>
<script>
export default {
    props: ['patient'],
    data() {
        return {
            loader: false,
            code: '',
            errors: [],
            endpoint: '/patients/' + this.patient.id + '/addauth'
        };
    },
    methods: {
        requestCode() {
            this.showModalRequestCode();

        },
        showModalRequestCode() {
            let html = 'Selecciona en la lista a quienes quieras solicitar el codigo o contacta a soporte para solicitarlo<br><br>';

            html += '<div class="checks-contacts text-left"></div>';

            Swal.fire({
                title: 'Solicitar Código de autorización',
                html: html,
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                cancelButtonText: 'Cerrar',
                confirmButtonText: 'Solicitar',
                didOpen: () => {

                    if (this.loader) {
                        return;
                    }
                    const $checksContacs = $('.checks-contacts');

                    $checksContacs.html('<h2>Cargando...</h2>');

                    this.loader = true;
                    axios.get(`/patients/${this.patient.id}/responsables`)
                        .then(({data}) => {
                            this.loader = false;

                            let html = `<label for="contact_${this.patient.id}"><input id="contact_${this.patient.id}" class="" type="checkbox" value="${this.patient.phone_country_code}${this.patient.phone_number}" checked> ${this.patient.fullname} (${this.patient.phone_country_code}) ${this.patient.phone_number}</label> <br>`;

                            if (data) {

                                data.forEach((contact, index) => {
                                    html += `<label for="contact_${this.patient.id + index + 1}"><input id="contact_${this.patient.id + index + 1}" class="" type="checkbox" value="${contact.phone_country_code}${contact.phone_number}"> ${contact.name} (${contact.phone_country_code}) ${contact.phone_number}</label> <br>`;
                                });

                            }
                            // html += `<input type="text" class="form-control" name="numero" id="customNum" placeholder="Otro Numero Telefónico" />`;


                            $checksContacs.html(html);


                        });


                }

            }).then((result) => {

                const contacts = [];

                const inputs = document.querySelectorAll('input[type=\'checkbox\']');
                // let otroNumero = document.getElementById("customNum");

                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].checked == true) {
                        contacts.push(inputs[i].value);
                    }
                }

                // if(otroNumero && otroNumero.value){
                //      contacts.push(`+506${otroNumero.value}`);
                // }


                if (result.value) {

                    this.generateCode(this.patient, contacts);

                }


            });
        },
        generateCode(patient, contacts) {

            axios.post(`/patients/${patient.id}/generateauth`, {contacts})
                .then(() => {

                    this.emitter.emit('generatedCode', patient);

                    flash('Codigo Generado!');

                }).catch(error => {
                    if (error.response.status == 500) {

                        flash('Error al generar código. ' + error.response.data.message, 'danger');
                    } else {
                        flash('Error al generar código.', 'danger');
                    }

                });


        },
        add() {
            if (this.loader) {
                return;
            }

            this.loader = true;
            this.errors = [];

            axios.post(this.endpoint, {code: this.code})
                .then(() => {
                    this.loader = false;
                    this.errors = [];

                    this.code = '';


                    flash('Acceso autorizado, ya puedes continuar!!');

                    if (this.callback) {
                        window.location.href = this.callback;
                    } else {

                        window.location.reload();
                    }
                })
                .catch(error => {
                    this.loader = false;
                    flash(error.response.data.message, 'danger');
                    this.errors = error.response.data.errors;
                });
        }

    }
};
</script>
