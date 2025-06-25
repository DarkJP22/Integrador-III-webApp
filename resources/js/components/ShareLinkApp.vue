<template>

    <a href="#" @click="shareLink()" title="Compartir link APP" class="btn btn-secondary">Compartir link APP</a>


</template>
<script>
export default {
    props: ['patient', 'callback'],
    data() {
        return {
            loader: false,
            code: '',
            errors: [],
            //endpoint:'/patients/'+ this.patient.id +'/authorization',

        };
    },
    methods: {
        shareLink() {
            this.showModalShareLink();

        },
        showModalShareLink() {
            let html = 'Selecciona en la lista a quienes quieras enviar el link y/o escribe un número en el campo de abajo<br><br>';

            html += '<div class="checks-contacts text-left"></div>';

            Swal.fire({
                title: 'Compartir Link de Aplicación Movil',
                html: html,
                showCancelButton: true,
                confirmButtonColor: '#67BC9A',
                cancelButtonColor: '#dd4b39',
                cancelButtonText: 'Cerrar',
                confirmButtonText: 'Enviar Link',
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

                            html += '<input type="text" class="form-control" name="numero" id="customNum" placeholder="Otro Numero Telefónico" />';


                            $checksContacs.html(html);


                        });


                }

            }).then((result) => {

                const contacts = [];

                const inputs = document.querySelectorAll('input[type=\'checkbox\']');

                const otroNumero = document.getElementById('customNum');

                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].checked == true) {
                        contacts.push(inputs[i].value);
                    }
                }

                if (otroNumero && otroNumero.value) {
                    contacts.push(`+506${otroNumero.value}`);
                }


                if (result.value) {

                    this.sendLink(this.patient, contacts);

                }


            });
        },
        sendLink(patient, contacts) {

            axios.post(`/pharmacy/patients/${this.patient.id}/share/link`, {contacts})
                .then(() => {

                    this.emitter.emit('sharedLink', patient);

                    flash('Link Compartido!');

                }).catch(error => {

                    if (error.response.status == 500) {

                        flash('Error al compartir Link. ' + error.response.data, 'danger');
                    } else {
                        flash('Error al compartir Link.', 'danger');
                    }

                });


        }

    }
};
</script>
